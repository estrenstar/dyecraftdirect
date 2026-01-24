<?php
class ModelExtensionModuleGoogleAllFeedGoogleMerchant extends ModelExtensionModuleGoogleAllFeed {
    public function process_elements($feed_configuration, $elements, $element_ids) {

        $pickup_method = !empty($feed_configuration['profile']['google_all_feed_config_pickup_method']) ? $feed_configuration['profile']['google_all_feed_config_pickup_method'] : false;
        $pickup_sla = !empty($feed_configuration['profile']['google_all_feed_config_pickup_sla']) ? $feed_configuration['profile']['google_all_feed_config_pickup_sla'] : false;
        $store_code = !empty($feed_configuration['profile']['google_all_feed_config_store_code']) ? $feed_configuration['profile']['google_all_feed_config_store_code'] : false;

        //<editor-fold desc="Exclusive code to this feed type">
            $exclusive_configuration = array(
                'size'  => 'google_all_feed_config_tag_size',
                'color'  => 'google_all_feed_config_tag_color',
                'material'  => 'google_all_feed_config_tag_material',
                'gender'  => 'google_all_feed_config_tag_gender',
                'age_group'  => 'google_all_feed_config_tag_age_group',
                'custom_label_0'  => 'google_all_feed_config_tag_custom_label_0',
                'custom_label_1'  => 'google_all_feed_config_tag_custom_label_1',
                'custom_label_2'  => 'google_all_feed_config_tag_custom_label_2',
                'custom_label_3'  => 'google_all_feed_config_tag_custom_label_3',
                'custom_label_4'  => 'google_all_feed_config_tag_custom_label_4',
            );

            foreach ($exclusive_configuration as $type => $conf_name) {
                if (!array_key_exists($conf_name, $feed_configuration['profile'])) {
                    unset($exclusive_configuration[$type]);
                } else {
                    $in_getvaluefrom = !empty($feed_configuration['profile']['google_all_feed_config_tag_'.$type.'_in_getvaluefrom']);
                    $in_attribute = !empty($feed_configuration['profile']['google_all_feed_config_tag_'.$type.'_in_attribute']);
                    $in_filter = !empty($feed_configuration['profile']['google_all_feed_config_tag_'.$type.'_in_filter']);
                    $in_option = !empty($feed_configuration['profile']['google_all_feed_config_tag_'.$type.'_in_option']);
                    $default_value = !empty($feed_configuration['profile']['google_all_feed_config_tag_'.$type.'_fixed_value']);

                    if (!$in_getvaluefrom && !$in_attribute && !$in_filter && !$in_option && !$default_value) {
                        unset($exclusive_configuration[$type]);
                    }
                }
            }

            if(!empty($exclusive_configuration) && !empty($element_ids)) {
                $all_getvaluefroms = array();                
                $all_attributes = array();
                $all_filters = array();
                $all_options = array();
                $types = array('getvaluefrom', 'attribute', 'filter', 'option');

                foreach ($exclusive_configuration as $type => $conf_name) {
                    foreach ($types as $type_variable) {
                        $key = 'google_all_feed_config_tag_'.$type.'_in_'.$type_variable;
                        $allowed_ids = array_key_exists($key, $feed_configuration['profile']) ? $feed_configuration['profile'][$key] : false;
                        if(!empty($allowed_ids)) {
                            ${'all_'.$type.'s'} = $this->{'get_product_'.$type_variable.'s'}($feed_configuration, $element_ids, $allowed_ids, true);
                        }

                        $key = 'google_all_feed_config_tag_'.$type.'_fixed_value';
                        $default_value = array_key_exists($key, $feed_configuration['profile']) ? $feed_configuration['profile'][$key] : false;

                        if (!empty($default_value)) {
                            if (!isset(${'all_'.$type.'s'})) {
                                ${'all_'.$type.'s'} = array();

                                foreach ($element_ids as $el_id) {
                                    ${'all_'.$type.'s'}[$el_id] = array($default_value);
                                }
                            } else {
                                foreach ($element_ids as $el_id) {
                                    if (!array_key_exists($el_id, ${'all_'.$type.'s'})) {
                                        ${'all_'.$type.'s'}[$el_id] = array($default_value);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        //</editor-fold>

        $tags = array(
            'g:id' => 'product_identificator',
            'title' => 'name',
            'link' => 'product_link',
            'g:brand' => 'manufacturer',
            'g:image_link' => 'image_link',
            'g:mpn' => 'mpn',
            'g:quantity' => 'quantity',
        );

        //<editor-fold desc="Exclusive code to this feed type">
            $special_tags = array(
                'g:weight',
                'description',
                'g:availability_date',
                'g:product_type',
                'g:google_product_category',
                'g:identifier_exists',
                'g:price',
                'g:sale_price',
                'g:sale_price_effective_date',
                'g:availability',
                'g:additional_image_link',
                'g:gtin',
                'g:multipack',
                'g:color',
                'g:size',
                'g:item_group_id',
                'g:material',
                'g:age_group',
                'g:gender',
                'g:custom_label',
                'g:condition',
                //'g:adult',
                //'g:pattern',
                'g:store_code',
                'g:pickup_method',
                'g:pickup_sla',
                'g:checkout_link_template',
            );
        //</editor-fold>

        $feed_elmements = array();

        foreach ($elements as $key => $product) {
            $temp = array();

            foreach ($tags as $tag_name => $index) {
                if($product[$index] !== '')
                    $temp[$tag_name] = $this->format_xml_value($product[$index]);
            }

            //<editor-fold desc="Exclusive code to this feed type">
                foreach ($special_tags as $tag_name) {
                    if($tag_name == 'g:weight' && $product['weight'] > 0) {
                        $temp[$tag_name] = $this->weight->format($product['weight'], $product['weight_class_id']);
                        continue;
                    }

                    if($tag_name == 'g:availability_date' && $product['quantity'] <= 0) {
                        $temp[$tag_name] = date("c", strtotime($product['date_available']." 00:00:00"));
                        continue;
                    }

                    if($tag_name == 'description') {
                        $temp[$tag_name] = $this->format_xml_description($product['description'], 5000);
                        continue;
                    }

                    if($tag_name == 'g:product_type' && !empty($product['last_category_tree'])) {
                        $temp[$tag_name] = $this->format_xml_value($product['last_category_tree']);
                        continue;
                    }

                    if($tag_name == 'g:condition') {
                        $temp[$tag_name] = 'new';
                        continue;
                    }

                    if($tag_name == 'g:google_product_category' && !empty($product['last_category_id'])) {
                        $cat_id = $this->extract_google_category_id($this->config->get('google_all_feed_taxonomy_cat_'.$product['last_category_id']));
                        if(is_numeric($cat_id))
                            $temp[$tag_name] = $cat_id;
                        continue;
                    }

                    if($tag_name == 'g:identifier_exists') {
                        /*$rule_1 = empty($product['gtin']) && empty($product['manufacturer']);
                        $rule_2 = empty($product['mpn']) && empty($product['manufacturer']);
                        if($rule_1 || $rule_2)*/

                        $rule = empty($product['gtin']) && empty($product['mpn']);
                        if($rule)
                            $temp[$tag_name] = 'no';
                        continue;
                    }

                    if($tag_name == 'g:additional_image_link' && !empty($product['additional_images_link'])) {
                        $temp[$tag_name] = array_slice($product['additional_images_link'], 0, 10);
                    }
                    
                    if($tag_name == 'g:price') {
                        $temp[$tag_name] = $product['price_formatted'].' '.$product['currency_code'];
                        continue;
                    }

                    if($tag_name == 'g:sale_price' && $product['special'] > 0) {
                        $temp[$tag_name] = $product['special_formatted'].' '.$product['currency_code'];
                        continue;
                    }

                    if ($tag_name == 'g:sale_price_effective_date' && isset($product['special']) && $product['special'] > 0) {

                        if($product['special_start'] == '0000-00-00 00:00:00' || empty($product['special_start']) || $product['special_start'] == '0000-00-00')
                            $product['special_start'] = date('Y-m-d');

                        $this->format_date_ISO_8601($product['special_start']);

                        if($product['special_end'] == '0000-00-00 00:00:00' || empty($product['special_end']) || $product['special_end'] == '0000-00-00') {
                            $time = strtotime(date("Y-m-d"));
                            $product['special_end'] = date("Y-m-d", strtotime("+1 month", $time));
                        }

                        $this->format_date_ISO_8601($product['special_end']);

                        $temp[$tag_name] = $product['special_start'].'/'.$product['special_end'];
                    }

                    if ($tag_name == 'g:availability') {
                        if ($product['quantity'] <= 0 && !empty($feed_configuration['profile']['google_all_feed_config_availability_preorder']) && $product['stock_status_id'] == $feed_configuration['profile']['google_all_feed_config_availability_preorder']) {
                            $temp[$tag_name] = 'preorder';
                        } elseif ($product['quantity'] <= 0 && !empty($feed_configuration['profile']['google_all_feed_config_availability_backorder']) && $product['stock_status_id'] == $feed_configuration['profile']['google_all_feed_config_availability_backorder']) {
                            $temp[$tag_name] = 'backorder';
                        } elseif ($product['quantity'] > 0) {
                            $temp[$tag_name] = 'in_stock';
                        } else {
                            $temp[$tag_name] = 'out_of_stock';
                        }
                        
                        continue;
                    }

                    if($tag_name == 'g:gtin' && !empty($product['gtin'])) {
                        $temp[$tag_name] = $this->format_xml_value($product['gtin']);
                        continue;
                    }

                    if($tag_name == 'g:multipack' && array_key_exists('discount_unit', $product) && !empty($product['discount_unit'])) {
                        $temp[$tag_name] = $product['discount_unit'];
                        continue;
                    }

                    if($tag_name == 'g:color') {
                        if(array_key_exists('splitted_by_color', $product) && !empty($product['splitted_by_color'])) {
                            $temp[$tag_name] = $product['splitted_by_color'];
                        } else if(isset($all_colors) && array_key_exists($product['product_id'], $all_colors)) {
                            $sliced_array = array_slice($all_colors[$product['product_id']], 0, 3);
                            $temp[$tag_name] = implode('/', $sliced_array);
                        }
                        continue;
                    }

                    if($tag_name == 'g:size') {
                        if(array_key_exists('splitted_by_size', $product) && !empty($product['splitted_by_size'])) {
                            $temp[$tag_name] = $product['splitted_by_size'];
                        } else if(isset($all_sizes) && array_key_exists($product['product_id'], $all_sizes) && !empty($all_sizes[$product['product_id']])) {
                            $temp[$tag_name] = $all_sizes[$product['product_id']][0];
                        }
                        continue;
                    }

                    if($tag_name == 'g:item_group_id') {
                        $splitted_by_color = array_key_exists('splitted_by_color', $product) && !empty($product['splitted_by_color']);
                        $splitted_by_size = array_key_exists('splitted_by_size', $product) && !empty($product['splitted_by_size']);
                        if($splitted_by_color || $splitted_by_size)
                            $temp[$tag_name] = $product['product_id'];
                        continue;
                    }

                    if($tag_name == 'g:material' && isset($all_materials) && array_key_exists($product['product_id'], $all_materials) && !empty($all_materials[$product['product_id']][0])) {
                        $temp[$tag_name] = $all_materials[$product['product_id']][0];
                        continue;
                    }

                    if($tag_name == 'g:gender' && isset($all_genders) && array_key_exists($product['product_id'], $all_genders) && !empty($all_genders[$product['product_id']][0])) {
                        $temp[$tag_name] = $all_genders[$product['product_id']][0];
                        continue;
                    }

                    if($tag_name == 'g:age_group' && isset($all_age_groups) && array_key_exists($product['product_id'], $all_age_groups) && !empty($all_age_groups[$product['product_id']][0])) {
                        $temp[$tag_name] = $all_age_groups[$product['product_id']][0];
                        continue;
                    }
                    
                    if($tag_name == 'g:custom_label') {
                        for ($i = 0; $i <= 4; $i++) {
                            $var_name = 'all_custom_label_'.$i.'s';
                            if(isset(${''.$var_name}) && array_key_exists($product['product_id'], ${''.$var_name}) && !empty(${''.$var_name}[$product['product_id']][0])) {
                              $temp[$tag_name.'_'.$i] = ${''.$var_name}[$product['product_id']][0];  
                            } else if(!empty($feed_configuration['profile']['google_all_feed_config_tag_custom_label_'.$i]) && !empty($feed_configuration['profile']['google_all_feed_config_tag_custom_label_'.$i.'_fixed_value'])) {
                                $temp[$tag_name.'_'.$i] = $feed_configuration['profile']['google_all_feed_config_tag_custom_label_'.$i.'_fixed_value'];
                            }
                        }
                    }

                    if($tag_name == 'g:pickup_method' && !empty($pickup_method)) {
                        $temp[$tag_name] = $pickup_method;
                        continue;
                    }

                    if($tag_name == 'g:pickup_sla' && !empty($pickup_sla)) {
                        $temp[$tag_name] = $pickup_sla;
                        continue;
                    }

                    if($tag_name == 'g:store_code' && !empty($store_code)) {
                        $temp[$tag_name] = $store_code;
                        continue;
                    }

                    if($tag_name == 'g:checkout_link_template' && !empty($product['product_link_purchase'])) {
                        $temp[$tag_name] = $product['product_link_purchase'];
                    }

                }
            //</editor-fold>

            $feed_elmements[] = $temp;
        }

        $header  = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        $header .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">'."\n";
        $header .= '<channel>'."\n";
        $header .= '<title><![CDATA[' . $this->get_config_meta_title() . ']]></title>'."\n";
        $header .= '<description><![CDATA[' . $this->get_config_meta_description() . ']]></description>'."\n";
        $header .= '<link><![CDATA[' . $this->check_link_domain($this->config->get('config_url')) . ']]></link>'."\n";

        $footer = '</channel>'."\n";
        $footer .= '</rss>';

        return array(
            'header' => $header,
            'elements' => $feed_elmements,
            'footer' => $footer,
            'node' => 'item'
        );
    }
}
?>