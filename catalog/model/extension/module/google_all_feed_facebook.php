<?php
class ModelExtensionModuleGoogleAllFeedFacebook extends ModelExtensionModuleGoogleAllFeed {
    public function process_elements($feed_configuration, $elements, $element_ids) {
        $tags = array(
            'id' => 'product_identificator',
            'title' => 'name',
            'link' => 'product_link',
            'brand' => 'manufacturer',
            'image_link' => 'image_link',
            'mpn' => 'mpn',
            'quantity' => 'quantity',
        );

        //<editor-fold desc="Exclusive this feed type">
            $special_tags = array(
                'shipping_weight',
                'description',
                'product_type',
                'google_product_category',
                'price',
                'sale_price',
                'sale_price_effective_date',
                'availability',
                'additional_image_link',
                'gtin',
                'color',
                'age_group',
                'gender',
                'size',
                'condition',
                'item_group_id',
                'identifier_exists'
            );
        //</editor-fold>

        //<editor-fold desc="Exclusive code to this feed type">
            $exclusive_configuration = array(
                'size'  => 'google_all_feed_config_tag_size',
                'color'  => 'google_all_feed_config_tag_color',
                'gender'  => 'google_all_feed_config_tag_gender',
                'age_group'  => 'google_all_feed_config_tag_age_group',
            );

            foreach ($exclusive_configuration as $type => $conf_name) {
                if(!array_key_exists($conf_name, $feed_configuration['profile']))
                    unset($exclusive_configuration[$type]);
                else {
                    $in_attribute = !empty($feed_configuration['profile']['google_all_feed_config_tag_'.$type.'_in_attribute']);
                    $in_filter = !empty($feed_configuration['profile']['google_all_feed_config_tag_'.$type.'_in_filter']);
                    $in_option = !empty($feed_configuration['profile']['google_all_feed_config_tag_'.$type.'_in_option']);
                    $default_value = !empty($feed_configuration['profile']['google_all_feed_config_tag_'.$type.'_fixed_value']);

                    if(!$in_attribute && !$in_filter && !$in_option && !$default_value)
                        unset($exclusive_configuration[$type]);
                }
            }

            if(!empty($exclusive_configuration) && !empty($element_ids)) {
                $all_attributes = array();
                $all_filters = array();
                $all_options = array();
                $types = array('attribute', 'filter', 'option');
                foreach ($exclusive_configuration as $type => $conf_name) {
                    foreach ($types as $type_variable) {
                        $key = 'google_all_feed_config_tag_'.$type.'_in_'.$type_variable;
                        $allowed_ids = array_key_exists($key, $feed_configuration['profile']) ? $feed_configuration['profile'][$key] : false;
                        if(!empty($allowed_ids)) {
                            ${'all_'.$type.'s'} = $this->{'get_product_'.$type_variable.'s'}($feed_configuration, $element_ids, $allowed_ids, true);
                        }

                        $key = 'google_all_feed_config_tag_'.$type.'_fixed_value';
                        $default_value = array_key_exists($key, $feed_configuration['profile']) ? $feed_configuration['profile'][$key] : false;

                        if(!empty($default_value)) {
                            if(!isset(${'all_'.$type.'s'})) {
                                ${'all_'.$type.'s'} = array();
                                foreach ($element_ids as $el_id) {
                                    ${'all_'.$type.'s'}[$el_id] = array($default_value);
                                }
                            } else {
                                foreach ($element_ids as $el_id) {
                                    if(!array_key_exists($el_id, ${'all_'.$type.'s'}))
                                        ${'all_'.$type.'s'}[$el_id] = array($default_value);
                                }
                            }
                        }
                    }
                }
            }
        //</editor-fold>

        $feed_elmements = array();

        foreach ($elements as $key => $product) {
            $temp = array();

            foreach ($tags as $tag_name => $index) {
                if($product[$index] !== '')
                    $temp[$tag_name] = $this->format_xml_value($product[$index]);
            }

            //<editor-fold desc="Exclusive this feed type">
                foreach ($special_tags as $tag_name) {
                    if($tag_name == 'shipping_weight' && $product['weight'] > 0) {
                        $temp[$tag_name] = $this->weight->format($product['weight'], $product['weight_class_id']);
                        continue;
                    }

                    if($tag_name == 'description') {
                        $temp[$tag_name] = $this->format_xml_description($product['description'], 5000);
                        continue;
                    }

                    if($tag_name == 'product_type' && !empty($product['last_category_tree'])) {
                        $temp[$tag_name] = $this->format_xml_value($product['last_category_tree']);
                        continue;
                    }

                    if($tag_name == 'google_product_category' && !empty($product['last_category_id'])) {
                        $cat_id = $this->extract_google_category_id($this->config->get('google_all_feed_taxonomy_cat_'.$product['last_category_id']));
                        if(is_numeric($cat_id))
                            $temp[$tag_name] = $cat_id;
                        continue;
                    }

                    if($tag_name == 'identifier_exists') {
                        $rule_1 = empty($product['gtin']) && empty($product['manufacturer']);
                        $rule_2 = empty($product['mpn']) && empty($product['manufacturer']);
                        if($rule_1 || $rule_2)
                            $temp[$tag_name] = 'no';
                        continue;
                    }

                    if($tag_name == 'additional_image_link' && !empty($product['additional_images_link'])) {
                        $temp[$tag_name] = array_slice($product['additional_images_link'], 0, 10);
                    }
                    
                    if($tag_name == 'price') {
                        $temp[$tag_name] = $product['price_formatted'].' '.$product['currency_code'];
                        continue;
                    }

                    if($tag_name == 'condition') {
                        $temp[$tag_name] = 'new';
                        continue;
                    }

                    if($tag_name == 'sale_price' && $product['special'] > 0) {
                        $temp[$tag_name] = $product['special_formatted'].' '.$product['currency_code'];
                        continue;
                    }

                    if($tag_name == 'sale_price_effective_date'
                        && $product['special'] > 0
                        && !empty($product['special_start'])
                        && $product['special_start'] != '0000-00-00'
                        && !empty($product['special_end'])
                        && $product['special_end'] != '0000-00-00')
                    {
                        $date_start = date(DATE_ISO8601, strtotime($product['special_start'].' 00:00:00'));
                        $date_end = date(DATE_ISO8601, strtotime($product['special_end'].' 00:00:00'));
                        $temp[$tag_name] = $date_start.'/'.$date_end;
                        continue;
                    }

                    if($tag_name == 'availability') {
                        $temp[$tag_name] = $product['quantity'] > 0 ? 'in stock' : 'out of stock';
                        continue;
                    }

                    if($tag_name == 'gtin' && !empty($product['gtin'])) {
                        $temp[$tag_name] = $this->format_xml_value($product['gtin']);
                        continue;
                    }

                    if($tag_name == 'multipack' && array_key_exists('discount_unit', $product) && !empty($product['discount_unit'])) {
                        $temp[$tag_name] = $product['discount_unit'];
                        continue;
                    }

                    if($tag_name == 'gender' && isset($all_genders) && array_key_exists($product['product_id'], $all_genders) && !empty($all_genders[$product['product_id']][0])) {
                        $temp[$tag_name] = $all_genders[$product['product_id']][0];
                        continue;
                    }

                    if($tag_name == 'age_group' && isset($all_age_groups) && array_key_exists($product['product_id'], $all_age_groups) && !empty($all_age_groups[$product['product_id']][0])) {
                        $temp[$tag_name] = $all_age_groups[$product['product_id']][0];
                        continue;
                    }

                    if($tag_name == 'color') {
                        if(array_key_exists('splitted_by_color', $product) && !empty($product['splitted_by_color'])) {
                            $temp[$tag_name] = $product['splitted_by_color'];
                        } else if(isset($all_colors) && array_key_exists($product['product_id'], $all_colors)) {
                            $sliced_array = array_slice($all_colors[$product['product_id']], 0, 3);
                            $temp[$tag_name] = implode('/', $sliced_array);
                        }
                        continue;
                    }

                    if($tag_name == 'size') {
                        if(array_key_exists('splitted_by_size', $product) && !empty($product['splitted_by_size'])) {
                            $temp[$tag_name] = $product['splitted_by_size'];
                        } else if(isset($all_sizes) && array_key_exists($product['product_id'], $all_sizes) && !empty($all_sizes[$product['product_id']])) {
                            $temp[$tag_name] = $all_sizes[$product['product_id']][0];
                        }
                        continue;
                    }

                    if($tag_name == 'item_group_id') {
                        $splitted_by_color = array_key_exists('splitted_by_color', $product) && !empty($product['splitted_by_color']);
                        $splitted_by_size = array_key_exists('splitted_by_size', $product) && !empty($product['splitted_by_size']);
                        if($splitted_by_color || $splitted_by_size)
                            $temp[$tag_name] = $product['product_id'];
                        continue;
                    }
                }
            //</editor-fold>

            $feed_elmements[] = $temp;
        }

        $header  = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        $header .= '<rss version="2.0">'."\n";
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