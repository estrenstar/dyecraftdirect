<?php
    if($this->GMTMaster && $this->GMTMaster->gmt_is_enabled()) {
        foreach ($this->request->post['cart'] as $key => $quantity) {
            if (!$quantity) {
                $key_deteled = $key;
                break;
            }
        }

        if(!empty($key_deteled)) {
            if (version_compare(VERSION, '2.1.0.2.1', '>=')) {
                $query = "SELECT * FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$key_deteled . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'";

                if (version_compare(VERSION, '2.3.0.0', '>='))
                    $query .= " AND api_id = " . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0);

                $result = $this->db->query($query);

                $product_removed = $result->row;
            } else {
                if (!is_numeric($key_deteled))
                    $product_removed = unserialize(base64_decode($key_deteled));
                else
                    $product_removed['product_id'] = $key_deteled;

                $product_removed['quantity'] = 1;

                //Devman Extensions - info@devmanextensions.com - 2017-05-31 16:07:38 - Get quantity removed
                $cart_products = $this->cart->getProducts();
                foreach ($cart_products as $key => $prod) {
                    if ($prod['product_id'] == $product_removed['product_id']) {
                        $product_removed['quantity'] = $prod['quantity'];
                        break;
                    }
                }
                //END
            }
        }

        if (!empty($product_removed['product_id'])) {
            $product_id = $product_removed['product_id'];
            $quantity = $product_removed['quantity'];

            $this->load->model('catalog/product');
            $product_info = $this->model_catalog_product->getProduct($product_id);

            $manufacturer = $this->GMTMaster->get_product_manufacturer($product_info['product_id']);
            $category_name = $this->GMTMaster->get_product_category_name($product_info['product_id']);
            $price = $this->GMTMaster->format_price($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), true);

            $json['gmt_status'] = true;
            $json['gmt_id'] = $product_info['product_id'];
            $json['gmt_name'] = $product_info['name'];
            $json['gmt_brand'] = $manufacturer;
            $json['gmt_category'] = $category_name;
            $json['gmt_quantity'] = $quantity;
            $json['gmt_price'] = $price;

            $json['gmt_variant'] = '';

            $event_id   = $this->GMTMaster->generate_uuid();
            $json['fb_event_id'] = $event_id;


            //Variants
            $option = '';

            if (!empty($product_removed['option']) && is_array($product_removed['option']))
                $option = $product_removed['option'];
            elseif (!empty($product_removed['option']))
                $option = json_decode($product_removed['option']);

            if (!empty($option))
                $json['atc_variant'] = $this->GMTMaster->get_product_variant($product_info['product_id'], $option);
            //END

            // Call GMT addons
            $gmt_addons = !is_null($this->DevmanGmtAddons) && is_array($this->DevmanGmtAddons) ? $this->DevmanGmtAddons : array();

            $data = array(
                'event_name'    => 'addToCart',
                'event_id'      => $event_id,
                'product_id'    => $product_info['product_id'],
                'currency'      => $this->session->data['currency'],
                'price'         => $price,
                'quantity'      => $quantity,
                'product_info'  => $product_info
            );

            $data = array_merge($json, $data);

            foreach ($gmt_addons as $addon) {
                $this->load->controller('extension/module/'.$addon.'/track', $data);
            }
        }
    }
?>