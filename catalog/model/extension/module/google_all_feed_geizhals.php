<?php

class ModelExtensionModuleGoogleAllFeedGeizhals extends ModelExtensionModuleGoogleAllFeed
{
    public function process_elements($feed_configuration, $elements, $element_ids)
    {
        //required
        $tags = array(
            'Product name' => 'name',
            'Manufacturer name' => 'manufacturer',
            'Price' => 'price_formatted',
            'deeplink' => 'product_link',
//            'Manufacturer`s product code' => '', //todo ???
            'Product description' => function ($product){
                return $this->format_xml_description($product['description'], 1000, false);
            },
//            'Stock availability' => '', //todo needs mapping
//            'Delivery charge' => '', //todo the famous shipping
            'EAN' => 'ean',
//            'method of payment' => '', //todo ???
        );

        return array(
            'elements' => $this->elementsToFeed($elements, $tags, false),
            'columns' => array_keys($tags)
        );
    }
}

?>