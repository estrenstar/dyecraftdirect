<?php
class ControllerExtensionModuleGoogleAll extends Controller {
    public function get_product_datas()
    {
        $key_cart = $this->request->post['product_id_key'];
        $json = array();

        if(!empty($key_cart))
        {
            $product_key_array = explode(':', $key_cart);
            $product_id = $product_key_array[0];

            $this->load->model('catalog/product');
            $product_info = $this->model_catalog_product->getProduct($product_id);

            if (!empty($product_key_array[1])) {
                $options = unserialize(base64_decode($product_key_array[1]));

                $json['gtm_variant'] = $this->GMTMaster->get_product_variant($product_info['product_id'], $options);
            }
            $quantity = $this->request->post['quantity'];

            if(!empty($product_info))
            {
                $event_id = $this->GMTMaster->generate_uuid();
                $product_info = array_merge($product_info, $this->DataLayer->_add_alternative_product_ids($product_info));
                $manufacturer = $this->GMTMaster->get_product_manufacturer($product_info['product_id']);
                $category_name = $this->GMTMaster->get_product_category_name($product_info['product_id']);
                $price = $this->GMTMaster->format_price($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), true);

                $json['gtm_id'] = $product_info['product_id'];
                $json['gtm_id_ee'] = $product_info['product_id_ee'];
                $json['gtm_id_gdr_1'] = $product_info['product_id_gdr_1'];
                $json['gtm_id_gdr_2'] = $product_info['product_id_gdr_2'];
                $json['gtm_id_fb'] = $product_info['product_id_fb'];
                $json['gtm_name'] = $product_info['name'];
                $json['gtm_brand'] = $manufacturer;
                $json['gtm_category'] = $category_name;
                $json['gtm_quantity'] = $quantity;
                $json['gtm_price'] = $price;

                $json['fb_event_id'] = $event_id;

                // Call GMT addons
                $gmt_addons = !is_null($this->DevmanGmtAddons) && is_array($this->DevmanGmtAddons) ? $this->DevmanGmtAddons : array();

                $data = array(
                    'event_name'        => 'removeFromCart',
                    'event_id'          => $event_id,
                    'product_id'        => $product_info['product_id'],
                    'currency'          => $this->session->data['currency'],
                    'price'             => $price,
                    'price_without_tax' => !empty($product_info['special']) ? $product_info['special'] : $product_info['price'],
                    'quantity'          => $quantity,
                    'product_info'      => $product_info,
                    'category'          => $category_name
                );

                $data = array_merge($json, $data);

                foreach ($gmt_addons as $addon) {
                    $this->load->controller('extension/module/'.$addon.'/track', $data);
                }
            }
        }

        echo json_encode($json); die;
    }
}
?>