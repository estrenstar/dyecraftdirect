<?php
class ModelExtensionModuleGoogleAllFeedCeneje extends ModelExtensionModuleGoogleAllFeed {
    public function process_elements($feed_configuration, $elements, $element_ids) {
//        die(var_dump($feed_configuration['profile']));
        $tags = array(
            //basic product data
            'ID' => function($product){
                return isset($product['sku'])
                    ? $product['sku']
                    : $product['product_id'];
            },
            'name' => 'name',
            'description' => function($product){
                return $this->format_xml_description($product['description'], 5000);
            },
            'link' => 'product_link',
            'mainImage' => 'image_link',
            'moreImages' => function($product){
                return implode(',', $product['additional_images_link']);
            },
            //price and availability
            'price' => function($product){
                return ($product['special'] > 0 && $product['special'] < $product['price']) ? $product['special_formatted'] : $product['price_formatted'];
            },
            'regular_price' => 'price_formatted',
            'stock' => function($product){
              return ($product['quantity'] > 0) ? 'in stock' : 'out of stock';
            },
            //product category
            'fileUnder' => 'last_category_tree',
            //product identifiers
            'brand' => 'manufacturer',
            'EAN' => 'ean',
            //shipping
            'deliveryCost' => function($product){
                $value = $this->feed['profile']['google_all_feed_config_exclusive_ceneje_shipping_cost_default'];
                return $value != '' ? $value : '0';
            },
            //detailed product @todo all this is required in certain conditions
//            'groupId' => '',
//            'attributes' => '',
//            'gender' => '',
//            'color' => '',
//            'size' => '',
//            'ageGroup' => '',
//            'attribute' => '',
//            'name' => '',
//            'value' => '',
//            'values' => '',
        );

        $header = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
        $header .= '<CNJExport>';

        $footer = '</CNJExport>';

        return array(
            'header' => $header,
            'elements' => $this->elementsToFeed($elements, $tags),
            'footer' => $footer,
            'node' => 'Item'
        );
    }
}
?>