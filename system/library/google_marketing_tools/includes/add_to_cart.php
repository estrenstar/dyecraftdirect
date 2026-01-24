<?php
    if($this->GMTMaster && $this->GMTMaster->gmt_is_enabled() && !empty($product_info)) {
        $product_info = array_merge($product_info, $this->DataLayer->_add_alternative_product_ids($product_info));
        $manufacturer = $this->GMTMaster->get_product_manufacturer($product_info['product_id']);
        $category_name = $this->GMTMaster->get_product_category_name($product_info['product_id']);

        $base_price = !empty($product_info['special']) ? $product_info['special'] : $product_info['price'];

        //Options combinations compatibility
        if(!empty($product_info['options_combination'])) {
            $comb_special = $this->model_extension_module_options_combinations->findCustomerSpecial($product_info['options_combination']);
            if(!empty($comb_special['price'])) {
                $base_price = $comb_special['price'];
            } else {
                $comb_price = $this->model_extension_module_options_combinations->findCustomerPrice($product_info['options_combination']);
                if (!empty($comb_price) && !empty($comb_price['price_prefix']) && array_key_exists('price', $comb_price)) {
                    $price_prefix = $comb_price['price_prefix'];
                    $price_value = $comb_price['price'];
                    if ($price_prefix == '+') {
                        $base_price += $price_value;
                    } elseif ($price_prefix == '-') {
                        $base_price -= $price_value;
                    } elseif ($price_prefix == '=') {
                        $base_price = $price_value;
                    }
                }
            }
        }
        //END Options combinations compatibility

        $event_id   = $this->GMTMaster->generate_uuid();
        $price = $this->GMTMaster->format_price($this->tax->calculate($base_price, $product_info['tax_class_id'], $this->config->get('config_tax')), true);
        $price_eur = $this->GMTMaster->format_price($this->tax->calculate($base_price, $product_info['tax_class_id'], $this->config->get('config_tax')), true, false, 'EUR');
        
        $json['gtm_id'] = $product_info['product_id'];
        $json['gtm_id_ee'] = $product_info['product_id_ee'];
        $json['gtm_id_gdr_1'] = $product_info['product_id_gdr_1'];
        $json['gtm_id_gdr_2'] = $product_info['product_id_gdr_2'];
        $json['gtm_id_fb'] = $product_info['product_id_fb'];
        $json['gtm_name'] = str_replace('&amp;', '&', $product_info['name']);
        $json['gtm_brand'] = $manufacturer;
        $json['gtm_category'] = $category_name;
        $json['gtm_quantity'] = $quantity;
        $json['gtm_price'] = $price;
        $json['gtm_price_eur'] = $price_eur;
        $json['gtm_variant'] = '';
        $json['gtm_sku'] = !empty($product_info['sku']) ? $product_info['sku'] : '';
        $json['gtm_image'] = !empty($product_info['image']) ? $this->GMTMaster->get_product_image_url($product_info) : '';
        $json['gtm_url'] = $this->GMTMaster->get_product_url($product_info['product_id']);
        $json['gtm_extra_params'] = array();
        $json['fb_event_id'] = $event_id;

        //Variants
        if (!empty($option)) {
            //Domodi pixel
            $domodi_sizes_config = $this->config->get("google_all_domodi_size_options_" . $this->config->get('config_store_id'));
            $domodi_sizes = array();
            if(is_array($domodi_sizes_config))
                foreach ($option as $product_option_id => $product_option_value_id) {
                    $option_id = $this->GMTMaster->get_option_id_from_product_option_value_id($product_option_value_id);
                    if (!empty($option_id) && in_array($option_id, $domodi_sizes_config)) {
                        $domodi_sizes[] = $this->GMTMaster->get_option_value_name_from_product_option_value_id($product_option_value_id);
                    }
                }
            $json['gtm_extra_params']['sizes_domodi'] = !empty($domodi_sizes) ? "['" . implode("','", $domodi_sizes) . "']" : '';
            // END Domodi pixel

            $json['gtm_variant'] = $this->GMTMaster->get_product_variant($product_info['product_id'], $option);
        }
        //END

        // Call GMT addons
        $gmt_addons = !is_null($this->DevmanGmtAddons) && is_array($this->DevmanGmtAddons) ? $this->DevmanGmtAddons : array();
        
        $data = array(
            'event_name'        => 'addToCart',
            'event_id'          => $event_id,
            'product_id'        => $product_info['product_id'],
            'currency'          => $this->session->data['currency'],
            'price'             => $price,
            'price_without_tax' => $base_price,
            'quantity'          => $quantity,
            'product_info'      => $product_info,
            'category'          => $category_name
        );

        $data = array_merge($json, $data);

        foreach ($gmt_addons as $addon) {
            $this->load->controller('extension/module/'.$addon.'/track', $data);
        }


    }
?>