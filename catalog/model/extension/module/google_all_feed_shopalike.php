<?php
class ModelExtensionModuleGoogleAllFeedShopalike extends ModelExtensionModuleGoogleAllFeed {
    public function process_elements($feed_configuration, $elements, $element_ids) {
        $tags = array(
            'itemId' => 'product_identificator',
            'name' => 'name',
            'link' => 'product_link',
            // TODO: feed requires segregated categories
//            'fullCategory' => 'last_category_tree',
            'fullCategory' => function($product){
                $tree = $product['last_category_tree'];
                $categories = explode(' &gt; ', $tree);
                if ($categories){
                    return $categories[0];
                }
                return null;
            },
            'category' => function($product){
                $tree = $product['last_category_tree'];
                $categories = explode(' &gt; ', $tree);
                if (count($categories) > 1){
                    return $categories[1];
                }
                return null;
            },
//            'gender',
//            'color' => 'color',
            'brand' => 'manufacturer',
//            'material',
            'description' => function($product){
                return $this->format_xml_description($product['description'], 5000);
            },
            'price' => function($product){
                return $product['price_formatted'].' '.$product['currency_code'];
            },
//            'oldPrice',
            'currency' => 'currency_code',
            'availability' => 'quantity',
//            'shippingCosts',
//            'sizes',
            'image' => 'image_link',
            'deepLink' => 'product_link',
//            'lastModified',
            'ean' => 'ean',
            'imageaux' => function($product){
                return array_slice($product['additional_images_link'], 0, 1);
            },
//            'extra1',
//            'cpc',
        );

        $header  = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        $header .= '<items>'."\n";

        $footer = '</items>';

        return array(
            'header' => $header,
            'elements' => $this->elementsToFeed($elements, $tags),
            'footer' => $footer,
            'node' => 'item'
        );
    }
}
?>