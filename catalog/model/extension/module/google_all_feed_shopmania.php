<?php

class ModelExtensionModuleGoogleAllFeedShopmania extends ModelExtensionModuleGoogleAllFeed
{
    public function process_elements($feed_configuration, $elements, $element_ids)
    {
//        die(var_dump($feed_configuration['profile']));
        $tags = array(
            'Category' => 'last_category_tree',
            'Manufacturer' => 'manufacturer',
            'MPN' => 'mpn',
            'Name' => 'name',
            'Description' => function ($product) {
                return $this->format_xml_description($product['description'], 5000);
            },
            'URL' => 'product_link',
            'Image' => 'image_link',
            'Price' => 'price_formatted',
            'Currency' => 'currency_code',
            'Shipping' => function ($product){
              $value = $this->feed['profile']['google_all_feed_config_exclusive_shopmania_shipping_cost_default'];
              return ($value != '') ? $value : '0';
            },
            'Availability' => function ($product) { //@todo
                return $product['quantity'] > 0
                    ? 'in stock'
                    : 'out of order';
            },
            'GTIN' => 'gtin',
        );

        $header = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
        $header .= '<store>' . "\n";
        $header .= '<products>' . "\n";

        $footer = '</products>' . "\n";
        $footer .= '</store>';

        return array(
            'header' => $header,
            'elements' => $this->elementsToFeed($elements, $tags),
            'footer' => $footer,
            'node' => 'Product'
        );
    }
}

?>