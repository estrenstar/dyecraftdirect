<?php
class ModelExtensionModuleGoogleAllFeedCriteo extends ModelExtensionModuleGoogleAllFeed {
    public function process_elements($feed_configuration, $elements, $element_ids) {
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
            );
        //</editor-fold>

        $feed_elmements = array();

        foreach ($elements as $key => $product) {
            $temp = array();

            foreach ($tags as $tag_name => $index) {
                if(is_numeric($product[$index]) || !empty($product[$index]))
                    $temp[$tag_name] = $this->format_xml_value($product[$index]);
            }

            //<editor-fold desc="Exclusive code to this feed type">
                foreach ($special_tags as $tag_name) {
                    if($tag_name == 'g:weight' && $product['weight'] > 0) {
                        $temp[$tag_name] = $this->weight->format($product['weight'], $product['weight_class_id']);
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

                    if($tag_name == 'g:google_product_category' && !empty($product['last_category_id'])) {
                        $cat_id = $this->extract_google_category_id($this->config->get('google_all_feed_taxonomy_cat_'.$product['last_category_id']));
                        if(is_numeric($cat_id))
                            $temp[$tag_name] = $cat_id;
                        continue;
                    }

                    if($tag_name == 'g:identifier_exists') {
                        $rule_1 = empty($product['gtin']) && empty($product['manufacturer']);
                        $rule_2 = empty($product['mpn']) && empty($product['manufacturer']);
                        if($rule_1 || $rule_2)
                            $temp[$tag_name] = 'FALSE';
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

                    if($tag_name == 'g:sale_price_effective_date'
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

                    if($tag_name == 'g:availability') {
                        $temp[$tag_name] = $product['quantity'] > 0 ? 'in_stock' : 'out_of_stock';
                        continue;
                    }

                    if($tag_name == 'g:gtin' && !empty($product['gtin'])) {
                        $temp[$tag_name] = $product['gtin'];
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
                        if(isset($all_sizes) && array_key_exists($product['product_id'], $all_sizes)) {
                            $temp[$tag_name] = $all_sizes[$product['product_id']][0];
                        } else if(array_key_exists('splitted_by_size', $product) && !empty($product['splitted_by_size'])) {
                            $temp[$tag_name] = $product['splitted_by_size'];
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
                }
            //</editor-fold>

            $feed_elmements[] = $temp;
        }

        $header  = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        $header .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">'."\n";
        $header .= '<channel>'."\n";
        $header .= '<title><![CDATA[' . $this->get_config_meta_title() . ']]></title>'."\n";
        $header .= '<description><![CDATA[' . $this->get_config_meta_description() . ']]></description>'."\n";
        $header .= '<link><![CDATA[' . $this->config->get('config_url') . ']]></link>'."\n";

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