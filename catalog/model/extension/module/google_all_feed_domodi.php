<?php

class ModelExtensionModuleGoogleAllFeedDomodi extends ModelExtensionModuleGoogleAllFeed
{
    public function process_elements($feed_configuration, $elements, $element_ids)
    {
        $excLusiveArr = $this->processSpecialConf($feed_configuration, $elements, $element_ids);
        $tags = array(
            'id' => 'product_identificator',
            'url' => 'product_link',
            'price' => function ($product) {
                return $product['special_formatted'] > 0 ? $product['special_formatted'] : $product['price_formatted'];
            },
            'brand' => 'manufacturer',
            'avail' => function ($product) {
                return $product['additional_images_link'] > 0 ? 1 : 99;
            },
            'cat' => function ($product) {
                return str_replace(' &gt; ', '/', $product['last_category_tree']);
            },
            'name' => 'name',
            'imgs' => function($product){
                $imgs = $product['additional_images_link'];
                if(!empty($product['image_link']))
                    array_unshift($imgs,$product['image_link']);
                $imgs = array_map(function($item){
                    return "<img><![CDATA[{$item}]]></img>" . "\n";
                }, $imgs);
                $imgs = implode('', $imgs);
                $imgs = preg_replace("/<img/", '<img default="true"', $imgs, 1);
                return $imgs;
            },
            'sizes' => function ($product) use ($excLusiveArr) {
                if (array_key_exists('all_sizes', $excLusiveArr) && array_key_exists($product['product_id'], $excLusiveArr['all_sizes'])){
                    $val = implode(';', $excLusiveArr['all_sizes'][$product['product_id']]);
                    return $val;
                }
                return null;
            },
            'IsPromoted' => function ($product) {
                return $product['special_formatted'] > 0 ? 1 : 0;
            },
            'oldprice' => function ($product) {
                return $product['special_formatted'] > 0 ? $product['price_formatted'] : '';
            },
            'desc' => 'description',
            'gender' => function ($product) use ($excLusiveArr) {
                if (array_key_exists('all_genders', $excLusiveArr) && array_key_exists($product['product_id'], $excLusiveArr['all_genders'])){
                    $val = implode(';', $excLusiveArr['all_genders'][$product['product_id']]);
                    return $val;
                }
                return null;
            },
            'attrs' => function ($product) use ($excLusiveArr) {
                $return = '';

                if (array_key_exists('all_colors', $excLusiveArr) && array_key_exists($product['product_id'], $excLusiveArr['all_colors'])){
                    $return .= '<attr name="Kolor"><![CDATA['.implode(';', $excLusiveArr['all_colors'][$product['product_id']]).']]></attr>';
                }

                if (!empty($product['ean'])){
                    $return .= '<attr name="EAN"><![CDATA['.$product['ean'].']]></attr>';
                }
                return $return;
            },
        );

        $header = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $header .= '<offers>';

        $footer = '</offers>';

        return array(
            'header' => $header,
            'elements' => $this->elementsToFeed($elements, $tags),
            'footer' => $footer,
            'node' => 'offer',
        );
    }

    function processSpecialConf($feed_configuration, $elements, $element_ids)
    {
        $exclusive_configuration = array(
            'size' => 'google_all_feed_config_domodi_tag_size',
            'gender' => 'google_all_feed_config_domodi_tag_gender',
            'color' => 'google_all_feed_config_domodi_tag_color',
        );

        $result = array();

        foreach ($exclusive_configuration as $type => $conf_name) {
            $in_attribute = !empty($feed_configuration['profile']['google_all_feed_config_domodi_tag_' . $type . '_in_attribute']);
            $in_filter = !empty($feed_configuration['profile']['google_all_feed_config_domodi_tag_' . $type . '_in_filter']);
            $in_option = !empty($feed_configuration['profile']['google_all_feed_config_domodi_tag_' . $type . '_in_option']);
            $default_value = !empty($feed_configuration['profile']['google_all_feed_config_domodi_tag_' . $type . '_fixed_value']);

            if (!$in_attribute && !$in_filter && !$in_option && !$default_value)
                unset($exclusive_configuration[$type]);
        }

        if (!empty($exclusive_configuration)) {
            $all_attributes = array();
            $all_filters = array();
            $all_options = array();
            $types = array('attribute', 'filter', 'option');
            foreach ($exclusive_configuration as $type => $conf_name) {
                foreach ($types as $type_variable) {
                    $key = 'google_all_feed_config_domodi_tag_' . $type . '_in_' . $type_variable;
                    $allowed_ids = array_key_exists($key, $feed_configuration['profile']) ? $feed_configuration['profile'][$key] : false;
                    if (!empty($allowed_ids)) {
                        $result['all_' . $type . 's'] = $this->{'get_product_' . $type_variable . 's'}($feed_configuration, $element_ids, $allowed_ids, true);
                    }

                    $key = 'google_all_feed_config_domodi_tag_' . $type . '_fixed_value';
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