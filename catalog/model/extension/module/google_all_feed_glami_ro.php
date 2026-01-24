<?php

class ModelExtensionModuleGoogleAllFeedGlamiRo extends ModelExtensionModuleGoogleAllFeed
{
    public function process_elements($feed_configuration, $elements, $element_ids)
    {
        $excLusiveArr = $this->processSpecialConf($feed_configuration, $elements, $element_ids);
//        die(var_dump($excLusiveArr));

        $tags = array(
            'ITEM_ID' => 'product_identificator',
            'PRODUCTNAME' => 'name',
            //TODO IMPORTANT: urls cant be formatted
            'URL' => 'product_link',
            'IMGURL' => 'image_link',
            'MANUFACTURER' => 'manufacturer',
            'DESCRIPTION' => function ($product) {
                $this->format_xml_description($product['description'], 5000);
            },
            'IMGURL_ALTERNATIVE' => function ($product) {
                $slice = array_slice($product['additional_images_link'], 0, 1);
                return empty($slice) ? null : $slice[0];
            },
            'PRICE_VAT' => function ($product) {
                return $product['price_formatted'] . ' ' . $product['currency_code'];
            },
//            'SIZE', //@TODO and requrired
            'CATEGORYTEXT' => 'last_category_tree',
            'DELIVERY_DATE' => function ($product) {
                $value = $this->feed['profile']['google_all_feed_config_exclusive_glami_ro_delivery_date_default'];
                return $value != '' ? $value : '0';
            },
            'PARAM' => function ($product) use ($excLusiveArr) {
                if (array_key_exists('all_sizes', $excLusiveArr) && array_key_exists($product['product_id'], $excLusiveArr['all_sizes'])){
                    $paramName = "<PARAM_NAME>size</PARAM_NAME> \n";
                    $sliced_array = array_slice($excLusiveArr['all_sizes'][$product['product_id']], 0, 3);
                    $val = implode('/', $sliced_array);
                    $valTag = "<VAL>{$val}</VAL> \n";
                    return $paramName . $valTag;
                }
                return null;
            }
        );

        $header = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $header .= '<SHOP>';

        $footer = '</SHOP>';

        return array(
            'header' => $header,
            'elements' => $this->elementsToFeed($elements, $tags),
            'footer' => $footer,
            'node' => 'SHOPITEM',
        );
    }

    function processSpecialConf($feed_configuration, $elements, $element_ids)
    {
        $exclusive_configuration = array(
            'size' => 'google_all_feed_config_glami_ro_tag_size',
        );

        $result = array();

        foreach ($exclusive_configuration as $type => $conf_name) {
//            if (!array_key_exists($conf_name, $feed_configuration['profile']))
//                unset($exclusive_configuration[$type]);
//            else {
            $in_attribute = !empty($feed_configuration['profile']['google_all_feed_config_glami_ro_tag_' . $type . '_in_attribute']);
            $in_filter = !empty($feed_configuration['profile']['google_all_feed_config_glami_ro_tag_' . $type . '_in_filter']);
            $in_option = !empty($feed_configuration['profile']['google_all_feed_config_glami_ro_tag_' . $type . '_in_option']);
            $default_value = !empty($feed_configuration['profile']['google_all_feed_config_glami_ro_tag_' . $type . '_fixed_value']);

            if (!$in_attribute && !$in_filter && !$in_option && !$default_value)
                unset($exclusive_configuration[$type]);
//            }
        }

        if (!empty($exclusive_configuration)) {
            $all_attributes = array();
            $all_filters = array();
            $all_options = array();
            $types = array('attribute', 'filter', 'option');
            foreach ($exclusive_configuration as $type => $conf_name) {
                foreach ($types as $type_variable) {
                    $key = 'google_all_feed_config_glami_ro_tag_' . $type . '_in_' . $type_variable;
                    $allowed_ids = array_key_exists($key, $feed_configuration['profile']) ? $feed_configuration['profile'][$key] : false;
                    if (!empty($allowed_ids)) {
                        $result['all_' . $type . 's'] = $this->{'get_product_' . $type_variable . 's'}($feed_configuration, $element_ids, $allowed_ids, true);
                    }

                    $key = 'google_all_feed_config_glami_ro_tag_' . $type . '_fixed_value';
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