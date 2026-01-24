<?php

class ModelExtensionModuleGoogleAllFeedWish extends ModelExtensionModuleGoogleAllFeed
{
    public function process_elements($feed_configuration, $elements, $element_ids)
    {
        //required
        $tags = array(
            'Unique Id' => 'product_id',
            'Price' => 'price_formatted',
            'Local Currency Code' => function($product) {
                return $product['currency_code'];
            },
            'Product Name' => 'name',
            'Quantity' => 'quantity',
            'Shipping' => function($product){
                $value = $this->feed['profile']['google_all_feed_config_exclusive_wish_shipping_cost_default'];
                return ($value != '') ? $value : '0';
            },
            'Main Image URL' => 'image_link',
//            'Tags' => '', //@todo
            'Description' => function ($product) {
                return $this->format_xml_description($product['description'], 1000, false);
            },
            //optional attributes
//            'Size' => '',
//            'Color' => '',
//            'MSRP' => '',
            'Brand' => 'manufacturer',
            'Landing Page URL' => 'product_link',
            'Extra Image URL' => function ($product) {
                $slice = array_slice($product['additional_images_link'], 0, 1);
                return count($slice) ? $slice[0] : null;
            },
//            'UPC' => '',
//            'Shipping Time' => '',
//            'Max Quantity' => '',
//            'Local Currency Code' => '',
//            'Localized Price' => '',
//            'Localized Shipping' => '',
//            'Localized Cost' => '',
//            'Localized Shipping Cost' => '',
        );

        return array(
            'elements' => $this->elementsToFeed($elements, $tags, false),
            'columns' => array_keys($tags)
        );
    }
}

?>