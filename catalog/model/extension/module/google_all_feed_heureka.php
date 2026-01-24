<?php
class ModelExtensionModuleGoogleAllFeedHeureka extends ModelExtensionModuleGoogleAllFeed {
    public function process_elements($feed_configuration, $elements, $element_ids) {
        $tags = array(
            'ITEM_ID' => 'product_identificator',
            'PRODUCTNAME' => 'name',
            'PRODUCT' => 'name',
            'DESCRIPTION' => function($product){
                return $this->format_xml_description($product['description'], 200);
            },
            'URL' => 'product_link',
            'IMGURL' => 'image_link',
            'IMGURL_ALTERNATIVE' => function($product){
                return array_slice($product['additional_images_link'], 0, 1);
            },
//            'VIDEO_URL',
            'PRICE_VAT' => function($product){
                return $product['price_formatted'];
            },
//            'HEUREKA_CPC',
            'MANUFACTURER' => 'manufacturer',
            'CATEGORYTEXT' => 'last_category_tree',
            'EAN' => 'ean',
            'PRODUCTNO' => 'product_identificator',
//            'PARAM',
//            'DELIVERY_DATE',
//            'DELIVERY',
//            'ITEMGROUP_ID',
//            'ACCESSORY',
//            'GIFT',

        );

        $header  = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        $header .= '<SHOP>'."\n";

        $footer = '</SHOP>';

        return array(
            'header' => $header,
            'elements' => $this->elementsToFeed($elements, $tags),
            'footer' => $footer,
            'node' => 'SHOPITEM'
        );
    }
}
?>