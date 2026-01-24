<?php
class ControllerExtensionModuleGa4Funnel extends Controller {
    // Inject our GA4 script view into the footer output
    public function eventInject(&$route, &$data, &$output) {
        if (!$this->config->get('module_ga4_funnel_status')) return;

        $data2 = array();
        $data2['measurement_id'] = $this->config->get('module_ga4_funnel_measurement_id');
        $data2['send_user_id'] = (int)$this->config->get('module_ga4_funnel_user_id');
        $data2['journal'] = (int)$this->config->get('module_ga4_funnel_journal');
        $data2['debug'] = (int)$this->config->get('module_ga4_funnel_debug');

        // Environment info
        $data2['currency_code'] = isset($this->session->data['currency']) ? $this->session->data['currency'] : $this->config->get('config_currency');
        $data2['route'] = isset($this->request->get['route']) ? $this->request->get['route'] : 'common/home';
        $data2['customer_id'] = $this->customer->isLogged() ? (int)$this->customer->getId() : 0;

        // Product page context for view_item
        if ($data2['route'] == 'product/product' && isset($this->request->get['product_id'])) {
            $this->load->model('catalog/product');
            $this->load->model('tool/image');
            $product_id = (int)$this->request->get['product_id'];
            $product = $this->model_catalog_product->getProduct($product_id);
            if ($product) {
                $item = array(
                    'item_id' => (string)$product['product_id'],
                    'item_name' => $product['name'],
                    'currency' => $data2['currency_code'],
                    'price' => (float)$product['price'],
                    'item_brand' => $product['manufacturer'],
                    'item_variant' => $product['model'],
                    'quantity' => 1
                );
                // Category (first category)
                $cats = $this->model_catalog_product->getCategories($product_id);
                if ($cats) {
                    $this->load->model('catalog/category');
                    $cat = $this->model_catalog_category->getCategory($cats[0]['category_id']);
                    if ($cat) $item['item_category'] = $cat['name'];
                }
                $data2['ga4_product'] = $item;
            }
        }

        // Cart context for begin_checkout
        if ($data2['route'] == 'checkout/checkout' || $data2['route'] == 'journal3/checkout') {
            $this->load->model('catalog/product');
            $items = array();
            $value = 0;
            $tax = 0;
            foreach ($this->cart->getProducts() as $p) {
                $item = array(
                    'item_id' => (string)$p['product_id'],
                    'item_name' => $p['name'],
                    'currency' => $data2['currency_code'],
                    'price' => (float)$p['price'],
                    'item_brand' => $p['manufacturer'] ?? '',
                    'item_variant' => $p['model'],
                    'quantity' => (int)$p['quantity']
                );
                // category
                $cats = $this->model_catalog_product->getCategories($p['product_id']);
                if ($cats) {
                    $this->load->model('catalog/category');
                    $cat = $this->model_catalog_category->getCategory($cats[0]['category_id']);
                    if ($cat) $item['item_category'] = $cat['name'];
                }
                $items[] = $item;
                $value += (float)$p['price'] * (int)$p['quantity'];
            }
            $data2['ga4_cart'] = array('items' => $items, 'value' => $value, 'currency' => $data2['currency_code']);
        }

        // Purchase context
        if ($data2['route'] == 'checkout/success' || $data2['route'] == 'journal3/checkout/success') {
            $this->load->model('checkout/order');
            $order_id = 0;
            if (isset($this->session->data['order_id'])) {
                $order_id = (int)$this->session->data['order_id'];
            } elseif (isset($this->session->data['ga4_last_order_id'])) {
                $order_id = (int)$this->session->data['ga4_last_order_id'];
            }
            if ($order_id) {
                $order_info = $this->model_checkout_order->getOrder($order_id);
                if ($order_info) {
                    $this->load->model('account/order');
                    $this->load->model('catalog/product');
                    $products = $this->model_account_order->getOrderProducts($order_id);
                    $items = array();
                    foreach ($products as $p) {
                        $item = array(
                            'item_id' => (string)$p['product_id'],
                            'item_name' => $p['name'],
                            'currency' => $order_info['currency_code'],
                            'price' => (float)$p['price'],
                            'quantity' => (int)$p['quantity'],
                            'item_variant' => $p['model']
                        );
                        // category
                        $cats = $this->model_catalog_product->getCategories($p['product_id']);
                        if ($cats) {
                            $this->load->model('catalog/category');
                            $cat = $this->model_catalog_category->getCategory($cats[0]['category_id']);
                            if ($cat) $item['item_category'] = $cat['name'];
                        }
                        $items[] = $item;
                    }

                    $data2['ga4_purchase'] = array(
                        'transaction_id' => $order_info['order_id'],
                        'affiliation' => $this->config->get('config_name'),
                        'value' => (float)$order_info['total'],
                        'tax' => (float)$order_info['total'] - (float)$order_info['shipping_method'] ? 0 : 0, // best-effort; GA4 requires tax field
                        'shipping' => 0,
                        'currency' => $order_info['currency_code'],
                        'coupon' => isset($order_info['coupon']) ? $order_info['coupon'] : '',
                        'items' => $items
                    );
                    // stash last id for safety
                    $this->session->data['ga4_last_order_id'] = $order_id;
                }
            }
        }

        $ga4_view = $this->load->view('extension/module/ga4_funnel', $data2);
        $output = str_replace('</body>', $ga4_view . '</body>', $output);
    }

    // Lightweight product JSON for AJAX add_to_cart tracking
    public function product_json() {
        $this->response->addHeader('Content-Type: application/json');
        $product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
        $qty = isset($this->request->get['quantity']) ? (int)$this->request->get['quantity'] : 1;
        if (!$product_id) {
            $this->response->setOutput(json_encode(array('ok'=>false)));
            return;
        }
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $product = $this->model_catalog_product->getProduct($product_id);
        if (!$product) {
            $this->response->setOutput(json_encode(array('ok'=>false)));
            return;
        }
        $cats = $this->model_catalog_product->getCategories($product_id);
        $cat_name = '';
        if ($cats) {
            $cat = $this->model_catalog_category->getCategory($cats[0]['category_id']);
            if ($cat) $cat_name = $cat['name'];
        }
        $currency_code = isset($this->session->data['currency']) ? $this->session->data['currency'] : $this->config->get('config_currency');
        $payload = array(
            'ok' => true,
            'item' => array(
                'item_id' => (string)$product['product_id'],
                'item_name' => $product['name'],
                'currency' => $currency_code,
                'price' => (float)$product['price'],
                'item_brand' => $product['manufacturer'],
                'item_variant' => $product['model'],
                'item_category' => $cat_name,
                'quantity' => $qty
            )
        );
        $this->response->setOutput(json_encode($payload));
    }
}
