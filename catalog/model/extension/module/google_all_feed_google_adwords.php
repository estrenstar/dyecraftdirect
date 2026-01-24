<?php
class ModelExtensionModuleGoogleAllFeedGoogleAdwords extends ModelExtensionModuleGoogleAllFeed {
    public function process_elements($feed_configuration, $elements, $element_ids) {
        $tags = array(
            'ID' => 'product_identificator',
            'ID2' => 'model',
            'Item title' => 'name',
            'Final URL' => 'product_link',
            'Image URL' => 'image_link',
            'Item description' => 'SPECIAL',
            'Item subtitle' => 'SPECIAL',
            'Item category' => 'SPECIAL',
            'Price' => 'SPECIAL',
            'Formatted price' => 'SPECIAL',
            'Sale price' => 'SPECIAL',
            'Formatted sale price' => 'SPECIAL',
            'Contextual keywords' => 'meta_keyword',
            'Item address' => 'SPECIAL',
            'Tracking template' => 'SPECIAL',
            'Custom parameter' => 'SPECIAL',
            'Destination URL' => 'SPECIAL',
        );

        $feed_elmements = array();

        foreach ($elements as $key => $product) {
            $temp = array();

            foreach ($tags as $tag_name => $index) {
                if($tag_name == 'Price') {
                    $temp[] = $product['price_formatted'].' '.$product['currency_code'];
                    continue;
                }

                if($tag_name == 'Formatted price') {
                    $temp[] = str_replace(',','', $product['price_formatted']).' '.$product['currency_code'];
                    continue;
                }

                if($tag_name == 'Sale price') {
                    if($product['special'] > 0)
                        $temp[] = $product['special_formatted'].' '.$product['currency_code'];
                    else
                        $temp[] = '';
                    continue;
                }

                if($tag_name == 'Formatted sale price') {
                    if($product['special'] > 0)
                        $temp[] = str_replace(',','', $product['special_formatted']).' '.$product['currency_code'];
                    else
                        $temp[] = '';
                    continue;
                }

                if($tag_name == 'Item description') {
                    $temp[] = $this->format_xml_description($product['description'], 1000, false);
                    continue;
                }

                if($tag_name == 'Item category') {
                    $temp[] = !empty($product['last_category_tree']) ? $product['last_category_tree'] : '';
                    continue;
                }

                if(in_array($tag_name, array('Item subtitle', 'Item address', 'Tracking template', 'Custom parameter', 'Destination URL'))) {
                    $temp[] = '';
                    continue;
                }
                
                $temp[] = $product[$index];
            }

            $feed_elmements[] = $temp;
        }

        return array(
            'elements' => $feed_elmements,
            'columns' => array_keys($tags)
        );
    }
}
?>