<?php

class ModelExtensionModuleGoogleAllFeedTrovaprezziIt extends ModelExtensionModuleGoogleAllFeed
{
    public function process_elements($feed_configuration, $elements, $element_ids)
    {
        $exclusiveConfArr = $this->processSpecialConf($feed_configuration, $elements, $element_ids);
        $tags = array(
            'Name' => 'name',
            'Brand' => 'manufacturer',
            'Description' => function ($product) {
                return $this->format_xml_description($product['description'], 5000);
            },
            'Price' => 'price_formatted',
            'Code' => 'product_identificator',
            'Link' => 'product_link',
            'Stock' => 'quantity',
            'Categories' => 'last_category_tree',
            'Image' => 'image_link',
            'PartNumber' => function ($product) {
                return ($product['sku'] != '') ? $this->format_xml_value($product['sku']) : null;
            },
            'EanCode' => function ($product) {
                return ($product['ean'] != '') ? $this->format_xml_value($product['ean']) : null;
            },
            'Image2' => function ($product) {
                $imgs = $product['additional_images_link'];
                return (count($imgs) > 0) ? $imgs[0] : null;
            },
            'Image3' => function ($product) {
                $imgs = $product['additional_images_link'];
                return (count($imgs) > 0) ? $imgs[0] : null;
            },
            'size' => function ($element) use ($exclusiveConfArr) {
                if (array_key_exists('splitted_by_size', $element) && !empty($element['splitted_by_size'])) {
                    return $element['splitted_by_size'];
                } else if (isset($exclusiveConfArr['all_sizes']) && array_key_exists($element['product_id'], $exclusiveConfArr['all_sizes'])) {
                    $sliced_array = array_slice($exclusiveConfArr['all_sizes'][$element['product_id']], 0, 3);
                    return implode(',', $sliced_array);
                } else {
                    return '';
                }
            },
            'color' => function ($element) use ($exclusiveConfArr) {
                if (array_key_exists('splitted_by_color', $element) && !empty($element['splitted_by_color'])) {
                    return $element['splitted_by_color'];
                } else if (isset($exclusiveConfArr['all_colors']) && array_key_exists($element['product_id'], $exclusiveConfArr['all_colors'])) {
                    $sliced_array = array_slice($exclusiveConfArr['all_colors'][$element['product_id']], 0, 3);
                    return implode(',', $sliced_array);
                } else {
                    return '';
                }
            },
        );

//        die(var_dump($this->feed));
        $feed_elements = $this->elementsToFeed($elements, $tags);
        //special tags
        $feed_elements = array_map(function ($item) {
            $value = $this->feed['profile']['google_all_feed_config_exclusive_trovapretzzi_shipping_cost_default'];
            $item['ShippingCost'] = $value == '' ? 0 : $value;
            return $item;
        }, $feed_elements);

        $header = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
        $header .= '<Products>' . "\n";

        $footer = '</Products>' . "\n";

        return array(
            'header' => $header,
            'elements' => $feed_elements,
            'footer' => $footer,
            'node' => 'Offer'
        );
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