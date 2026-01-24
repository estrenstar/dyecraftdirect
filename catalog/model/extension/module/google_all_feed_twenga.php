<?php
class ModelExtensionModuleGoogleAllFeedTwenga extends ModelExtensionModuleGoogleAllFeed {
    public function process_elements($feed_configuration, $elements, $element_ids) {
        $tags = array(
            'merchant_ref' => 'original_product_identificator',
            'brand' => 'manufacturer',
            'regular_price' => 'price_formatted',
            'manufacturer_id' => 'manufacturer',
            'product_url' => 'product_link',
            'image_url' => 'image_link',
            'category' => 'last_category_tree',
            'availability' => 'quantity',
        );

        //<editor-fold desc="Exclusive this feed type">
            $special_tags = array(
                'upc_ean',
                'merchant_id',
                'price',
                'designation',
                'description',
                'in_stock',
                'ecotax',
                'condition',
            );
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
                    if($tag_name == 'upc_ean') {
                        $value = !empty($product['ean']) ? $product['ean'] : (!empty($product['upc']) ? $product['upc'] : '');
                        if(!empty($value))
                            $temp[$tag_name] = $value;
                        continue;
                    }

                    if($tag_name == 'merchant_id') {
                        $splitted_by_color = array_key_exists('splitted_by_color', $product) && !empty($product['splitted_by_color']);
                        $splitted_by_size = array_key_exists('splitted_by_size', $product) && !empty($product['splitted_by_size']);
                        if($splitted_by_color || $splitted_by_size)
                            $temp[$tag_name] = $product['product_identificator'];
                        continue;
                    }

                    if($tag_name == 'price' && $product['special'] > 0) {
                        $temp[$tag_name] = $product['special_formatted'];
                        continue;
                    }

                    if($tag_name == 'designation') {
                        $temp[$tag_name] = $product['name'].' '.$product['manufacturer'].' '.$product['model'];
                    }

                    if($tag_name == 'description') {
                        $temp[$tag_name] = $this->format_xml_description($product['description'], 1000000000);
                        continue;
                    }

                    if($tag_name == 'in_stock') {
                        $temp[$tag_name] = $product['quantity'] > 0 ? 'Y' : 'N';
                        continue;
                    }

                    if($tag_name == 'ecotax' && !empty($product['tax_class_id'])) {
                        $currency_value = $this->currency->getValue($product['currency_code']);
                        $temp[$tag_name] = $this->currency->format($this->tax->getTax($product['price'], $product['tax_class_id']), $product['currency_code'], $currency_value, false);
                        continue;
                    }

                    if($tag_name == 'in_stock') {
                        $temp[$tag_name] = 0;
                        continue;
                    }
                }
            //</editor-fold>

            $feed_elmements[] = $temp;
        }

        $header  = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        $header .= '<products>'."\n";

        $footer = '</products>'."\n";

        return array(
            'header' => $header,
            'elements' => $feed_elmements,
            'footer' => $footer,
            'node' => 'product'
        );
    }
}
?>