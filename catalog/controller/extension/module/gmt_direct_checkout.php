<?php
class ControllerExtensionModuleGmtDirectCheckout extends Controller {
    public function index() {
        $this->load->model('catalog/product');
        $this->language->load('checkout/cart');

        if (isset($this->request->get['product_id'])) {
            $product_id = (int)$this->request->get['product_id'];
        } else {
            $product_id = 0;
        }

        $product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info) {
            if (isset($this->request->get['options'])) {
                $options_string = $this->request->get['options'];
                $options_array = explode('-', $options_string);
                $options = array();
                foreach ($options_array as $option_pair) {
                    list($option_id, $option_value_id) = explode(':', $option_pair);
                    $options[$option_id] = $option_value_id;
                }
            } else {
                $options = array();
            }

            $product_options = $this->model_catalog_product->getProductOptions($product_id);
            foreach ($product_options as $product_option) {
                if ($product_option['required'] && empty($options[$product_option['product_option_id']])) {
                    // Redirigir a la página del producto si hay opciones requeridas faltantes
                    $this->session->data['error'] = sprintf($this->language->get('error_required'), $product_option['name']);
                    $this->response->redirect($this->url->link('product/product', 'product_id=' . $product_id));
                }
            }

            $quantity = 1; // Puedes ajustar esto según lo que necesites

            // Añadir al carrito
            $this->cart->add($product_id, $quantity, $options);

            // Redirigir al carrito después de añadir el producto
            $this->response->redirect($this->url->link('checkout/checkout'));
        } else {
            // Redirigir a la página de inicio si el producto no existe
            $this->response->redirect($this->url->link('common/home'));
        }
    }
}