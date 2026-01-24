<?php

class ModelExtensionModuleGoogleAllFeedBing extends ModelExtensionModuleGoogleAllFeed
{
    public function process_elements($feed_configuration, $elements, $element_ids)
    {
        $exclusiveConfArr = $this->processSpecialConf($feed_configuration, $elements, $element_ids);
//        die(var_dump($exclusiveConfArr));

        $tags = array(
            'id' => 'product_identificator',
            'title' => 'name',
            'link' => 'product_link',
            'price' => function ($product) {
                return $product['price_formatted'] . ' ' . $product['currency_code'];
            },
            'description' => function ($product) {
                return $this->format_xml_description($product['description'], 1000, false);;
            },
            'image_link' => 'image_link',
            'shipping' => function ($product) use ($feed_configuration) {
                $value = $feed_configuration['profile']['google_all_feed_config_exclusive_bing_shipping_cost_default'];
                return $value;
            },
//            //required if manufactured
            'mpn' => function ($product) {
                return ($product['mpn']);
            },
            'gtin' => function ($product) {
                return ($product['gtin']);
            },
            'brand' => function ($product) {
                return ($product['manufacturer']);
            },
            'identifier_exists' => function ($product) {
                return isset($product['mpn']) && isset($product['gtin']) && isset($product['brand'])
                    ? 'TRUE'
                    : 'FALSE';
            },
//            //optional product variants
            //TODO ONLY SHOW THIS FIELD WHEN PRODUCT IS SPLITED BY OPTION
            //'item_group_id' => 'product_identificator',
//            'pattern' => '',
            'additional_image_link' => function ($product) {
                if (
                    isset($product['additional_images_link'])
                    && is_array($product['additional_images_link'])
                    && count($product['additional_images_link'])
                ) {
                    return $product['additional_images_link'][0];
                }
                return '';
            },
//            //optional others
            //TODO: this doesn't found any category
//            'product_category' => function ($product) {
//                $value = $this->extract_google_category_id($this->config->get('google_all_feed_taxonomy_cat_'.$product['last_category_id']));
//                return $value != '' ? $value : ' ';
//            },
//            'condition' => '',
//            'expiration_date' => '',
            'multipack' => function ($product) {
                return (isset($product['discount_unit'])) ? $product['discount_unit'] : '';
            },
            'product_type' => 'last_category_tree',
//            'unit_pricing_measure' => '',
//            'unit_pricing_base_measure' => '',
//            'energy_efficiency_class' => '',
//            //optional bing
//            'seller_name' => '',
//            'bingads_grouping' => '',
//            'bingads_label' => '',
//            'bingads_redirect' => '',
//            'custom_label_0' => '',
//            //sales and promotions
            'sale_price' => function ($product) {
                if (isset($product['special']) && $product['special'] > 0) {
                    return $product['special_formatted'];
                }
                return '';
            },
            'sale_price_effective_date' => function ($product) {
                if (isset($product['special']) && $product['special'] > 0) {

                    if($product['special_start'] == '0000-00-00 00:00:00' || empty($product['special_start']) || $product['special_start'] == '0000-00-00')
                        $product['special_start'] = date('Y-m-d 00:00:00');

                    $this->format_date_ISO_8601($product['special_start'], 'bing');


                    if($product['special_end'] == '0000-00-00 00:00:00' || empty($product['special_end']) || $product['special_end'] == '0000-00-00') {
                        $time = strtotime(date("Y-m-d"));
                        $product['special_end'] = date("Y-m-d 23:59:59", strtotime("+1 month", $time));
                    }

                    $this->format_date_ISO_8601($product['special_end'], 'bing');

                    return $product['special_start'].'/'.$product['special_end'];
                }
                return '';
            },
//            'promotion_ID' => '',
            'size' => function ($element) use ($exclusiveConfArr) {
                if (array_key_exists('splitted_by_size', $element) && !empty($element['splitted_by_size'])) {
                    return $element['splitted_by_size'];
                } else if (isset($exclusiveConfArr['all_sizes']) && array_key_exists($element['product_id'], $exclusiveConfArr['all_sizes'])) {
                    $sliced_array = array_slice($exclusiveConfArr['all_sizes'][$element['product_id']], 0, 3);
                    return implode('/', $sliced_array);
                } else {
                    return '';
                }
            },
            'size_type' => function ($element) use ($feed_configuration) {
                $sizeType = $feed_configuration['profile']['google_all_feed_config_bing_tag_size_type'];
                return $sizeType;
            },
            'size_system' => function ($element) use ($feed_configuration) {
                $sizeSystem = $feed_configuration['profile']['google_all_feed_config_bing_tag_size_system'];
                return $sizeSystem;
            },
            'color' => function ($element) use ($exclusiveConfArr) {
                if (array_key_exists('splitted_by_color', $element) && !empty($element['splitted_by_color'])) {
                    return $element['splitted_by_color'];
                } else if (isset($exclusiveConfArr['all_colors']) && array_key_exists($element['product_id'], $exclusiveConfArr['all_colors'])) {
                    $sliced_array = array_slice($exclusiveConfArr['all_colors'][$element['product_id']], 0, 3);
                    return implode('/', $sliced_array);
                } else {
                    return '';
                }
            },
            'material' => function ($element) use ($exclusiveConfArr) {
                if (isset($exclusiveConfArr['all_materials']) && array_key_exists($element['product_id'], $exclusiveConfArr['all_materials'])) {
                    $sliced_array = array_slice($exclusiveConfArr['all_materials'][$element['product_id']], 0, 3);
                    return implode('/', $sliced_array);
                } else {
                    return '';
                }
            },
            'gender' => function ($element) use ($exclusiveConfArr) {

                if (isset($exclusiveConfArr['all_genders']) && array_key_exists($element['product_id'], $exclusiveConfArr['all_genders'])) {
                    $sliced_array = array_slice($exclusiveConfArr['all_genders'][$element['product_id']], 0, 3);
                    return implode('/', $sliced_array);
                } else {
                    return '';
                }
            },
            'age_group' => function ($element) use ($exclusiveConfArr) {
                if (isset($exclusiveConfArr['all_age_groups']) && array_key_exists($element['product_id'], $exclusiveConfArr['all_age_groups'])) {
                    $sliced_array = array_slice($exclusiveConfArr['all_age_groups'][$element['product_id']], 0, 3);
                    return implode('/', $sliced_array);
                } else {
                    return '';
                }
            }
        );

        return array(
            'elements' => $this->elementsToFeed($elements, $tags, false),
            'columns' => array_keys($tags)
        );
    }

    function processSpecialConf($feed_configuration, $elements, $element_ids)
    {
        $exclusive_configuration = array(
            'size' => 'google_all_feed_config_bing_tag_size',
            'color' => 'google_all_feed_config_bing_tag_color',
            'material' => 'google_all_feed_config_bing_tag_material',
            'gender' => 'google_all_feed_config_bing_tag_gender',
            'age_group' => 'google_all_feed_config_bing_tag_age_group',
        );

        $result = array();

        foreach ($exclusive_configuration as $type => $conf_name) {
            if (!array_key_exists($conf_name, $feed_configuration['profile']))
                unset($exclusive_configuration[$type]);
            else {
                $in_attribute = !empty($feed_configuration['profile']['google_all_feed_config_bing_tag_' . $type . '_in_attribute']);
                $in_filter = !empty($feed_configuration['profile']['google_all_feed_config_bing_tag_' . $type . '_in_filter']);
                $in_option = !empty($feed_configuration['profile']['google_all_feed_config_bing_tag_' . $type . '_in_option']);
                $default_value = !empty($feed_configuration['profile']['google_all_feed_config_bing_tag_' . $type . '_fixed_value']);

                if (!$in_attribute && !$in_filter && !$in_option && !$default_value)
                    unset($exclusive_configuration[$type]);
            }
        }

        if (!empty($exclusive_configuration)) {
            $all_attributes = array();
            $all_filters = array();
            $all_options = array();
            $types = array('attribute', 'filter', 'option');
            foreach ($exclusive_configuration as $type => $conf_name) {
                foreach ($types as $type_variable) {
                    $key = 'google_all_feed_config_bing_tag_' . $type . '_in_' . $type_variable;
                    $allowed_ids = array_key_exists($key, $feed_configuration['profile']) ? $feed_configuration['profile'][$key] : false;
                    if (!empty($allowed_ids)) {
                        $result['all_' . $type . 's'] = $this->{'get_product_' . $type_variable . 's'}($feed_configuration, $element_ids, $allowed_ids, true);
                    }

                    $key = 'google_all_feed_config_bing_tag_' . $type . '_fixed_value';
                    $default_value = array_key_exists($key, $feed_configuration['profile']) ? $feed_configuration['profile'][$key] : false;

                    if (!empty($default_value)) {
                        if (!isset($result['all_' . $type . 's'])) {
                            $result['all_' . $type . 's'] = array();
                            foreach ($element_ids as $el_id) {
                                $result['all_' . $type . 's'][$el_id] = array($default_value);
                            }
                        } else {
                            foreach ($element_ids as $el_id) {
                                if (!array_key_exists($el_id, $result['all_' . $type . 's']))
                                    $result['all_' . $type . 's'][$el_id] = array($default_value);
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

}

?>