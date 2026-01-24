<?php

class ModelExtensionModuleGoogleAllFeedSkroutz extends ModelExtensionModuleGoogleAllFeed
{
    public function process_elements($feed_configuration, $elements, $element_ids)
    {
        $exclusiveConfArr = $this->processSpecialConf($feed_configuration, $elements, $element_ids);
        $tags = array(
            //required
            'UniqueID' => 'product_identificator',
            'Name' => 'name',
            'link' => 'product_link',
            'image' => 'image_link',
            'category' => 'last_category_tree',
            'PriceVat' => function ($product) {
                return !empty($product['special']) && $product['special'] > 0 ? $product['special_formatted'] : $product['price_formatted'];
            },
            'Availability' => function ($product) {
                return ($product['quantity'] > 0) ? 'In Stock' : 'Out of stock';
            },
            'Manufacturer' => 'manufacturer',
            'MPN' => function ($product) {
                return isset($product['mpn'])
                    ? $product['mpn']
                    : null;
            },
            //optional
            'Ean' => function ($product) {
                return isset($product['ean'])
                    ? $product['ean']
                    : null;
            },
            'InStock' => function ($product) {
                return ($product['quantity'] > 0) ? 'Y' : 'N';
            },
//            'shipping' => 'shipping',
            'weight' => function ($product) {
                return $product['weight'] != 0 ? $product['weight'] : null;
            },
            'additional_imageurl' => function ($product) {
                return $product['additional_images_link'];
            },
            'size' => function ($element) use ($exclusiveConfArr) {
                if (array_key_exists('splitted_by_size', $element) && !empty($element['splitted_by_size'])) {
                    return $element['splitted_by_size'];
                } else if (isset($exclusiveConfArr['all_sizes']) && array_key_exists($element['product_id'], $exclusiveConfArr['all_sizes'])) {
                    $sliced_array = $exclusiveConfArr['all_sizes'][$element['product_id']];
                    return implode(',', $sliced_array);
                } else {
                    return '';
                }
            },
            'color' => function ($element) use ($exclusiveConfArr) {
                if (array_key_exists('splitted_by_color', $element) && !empty($element['splitted_by_color'])) {
                    return $element['splitted_by_color'];
                } else if (isset($exclusiveConfArr['all_colors']) && array_key_exists($element['product_id'], $exclusiveConfArr['all_colors'])) {
                    $sliced_array = $exclusiveConfArr['all_colors'][$element['product_id']];
                    return implode(',', $sliced_array);
                } else {
                    return '';
                }
            },
        );

        $header = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
        $header .= '<mywebstore>' . "\n";
        $header .= '<created_at>' . date("Y-m-d H:i", time()) . '</created_at>' . "\n";
        $header .= '<products>' . "\n";

        $footer = '</products>' . "\n";
        $footer .= '</mywebstore>';

        $output = array(
            'header' => $header,
            'elements' => $this->elementsToFeed($elements, $tags),
            'footer' => $footer,
            'node' => 'product'
        );

        return $output;
    }

    function processSpecialConf($feed_configuration, $elements, $element_ids)
    {
        $exclusive_configuration = array(
            'size' => 'google_all_feed_config_tag_size',
            'color' => 'google_all_feed_config_tag_color',
        );

        $result = array();

        foreach ($exclusive_configuration as $type => $conf_name) {
            if (!array_key_exists($conf_name, $feed_configuration['profile']))
                unset($exclusive_configuration[$type]);
            else {
                $in_attribute = !empty($feed_configuration['profile']['google_all_feed_config_tag_' . $type . '_in_attribute']);
                $in_filter = !empty($feed_configuration['profile']['google_all_feed_config_tag_' . $type . '_in_filter']);
                $in_option = !empty($feed_configuration['profile']['google_all_feed_config_tag_' . $type . '_in_option']);
                $default_value = !empty($feed_configuration['profile']['google_all_feed_config_tag_' . $type . '_fixed_value']);

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
                    $key = 'google_all_feed_config_tag_' . $type . '_in_' . $type_variable;
                    $allowed_ids = array_key_exists($key, $feed_configuration['profile']) ? $feed_configuration['profile'][$key] : false;
                    if (!empty($allowed_ids)) {
                        $result['all_' . $type . 's'] = $this->{'get_product_' . $type_variable . 's'}($feed_configuration, $element_ids, $allowed_ids, true);
                    }

                    $key = 'google_all_feed_config_tag_' . $type . '_fixed_value';
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