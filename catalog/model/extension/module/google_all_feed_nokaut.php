<?php

class ModelExtensionModuleGoogleAllFeedNokaut extends ModelExtensionModuleGoogleAllFeed
{
    public function process_elements($feed_configuration, $elements, $element_ids)
    {
//        die(var_dump($elements));
        $tags = array(
            //required
            'name' => 'name',
            'id' => 'product_identificator',
            'description' => function ($product) {
                return $this->format_xml_description($product['description'], 5000);
            },
            'url' => 'product_link',
            'price' => 'price_formatted',
            'category' => 'last_category_tree',
            'producer' => 'manufacturer',
            'image' => 'image_link',

            //not required
            //TODO: warning, there is some attributes in this way: <property name="isbn">0957921896</property>, with the actual strucure we cant handle that tag.
            'gallery' => function($product){
                $imgs = $product['additional_images_link'];
                $imgs = array_map(function($item){
                    return "<image>{$item}</image>" . "\n";
                }, $imgs);
                $imgs = implode('', $imgs);
                return $imgs;
            },
            'instock' => 'quantity',
            //TODO weights must to be in kilogrammes without any string referencing hte scale. The number must be splitted by dots not by colon. (E.g. 2.60)
//            'weight' => function ($product){
//                $weight = $product['weight'];
//                $weight = sprintf('%.2f', $weight);
//                return $weight;
//            }

        );

        $header = '<?xml version="1.0" encoding="UTF-8"?>' . "\n\n";
        $header .= '<!DOCTYPE nokaut SYSTEM "http://www.nokaut.pl/integracja/nokaut.dtd">' . "\n";
        $header .= '<nokaut>' . "\n";
        $header .= '<offers>' . "\n";

        $footer = '</offers>' . "\n";
        $footer .= '</nokaut>' . "\n";

        return array(
            'header' => $header,
            'elements' => $this->elementsToFeed($elements, $tags),
            'footer' => $footer,
            'node' => 'offer'
        );
    }
}

?>