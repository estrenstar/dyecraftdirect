<?php
class ModelExtensionModuleGoogleAllFeedBestprice extends ModelExtensionModuleGoogleAllFeed {
    public function process_elements($feed_configuration, $elements, $element_ids) {
        $availability_in_stock = !empty($feed_configuration['profile']['google_all_feed_config_exclusive_bestprice_availability_in_stock']) ? $feed_configuration['profile']['google_all_feed_config_exclusive_bestprice_availability_in_stock'] : false;
        $availability_out_of_stock = !empty($feed_configuration['profile']['google_all_feed_config_exclusive_bestprice_availability_out_of_stock']) ? $feed_configuration['profile']['google_all_feed_config_exclusive_bestprice_availability_out_of_stock'] : false;
        $shipping = !empty($feed_configuration['profile']['google_all_feed_config_exclusive_bestprice_shipping']) ? $feed_configuration['profile']['google_all_feed_config_exclusive_bestprice_shipping'] : false;



        $tags = array(
            'productId' => 'product_identificator',
            'title' => 'name',
            'productURL' => 'product_link',
            'brand' => 'manufacturer',
        );

        //<editor-fold desc="Exclusive code to this feed type">
        $special_tags = array(
            'price',
            'imagesURL',
            'category_path',
            'availability',
            'stock',
            'identification', //ean barcode mpn sku
            'isbn',
            'size',
            'color',
            'weight',
            'shipping',
        );
        //</editor-fold>

        $feed_elmements = array();

        foreach ($elements as $key => $product) {
            $temp = array();

            foreach ($tags as $tag_name => $index) {
                if($product[$index] !== '')
                    $temp[$tag_name] = $this->format_xml_value($product[$index]);
            }

            //<editor-fold desc="Exclusive code to this feed type">
            foreach ($special_tags as $tag_name) {
                if($tag_name == 'weight' && $product['weight'] > 0) {
                    $temp[$tag_name] = $product['weight'];
                }

                if($tag_name == 'price') {
                    $price = $product['special_formatted'] > 0 ? $product['special_formatted'] : $product['price_formatted'];
                    $price = number_format($price, 2, ',', '.');
                    $temp[$tag_name] = $price;
                    continue;
                }

                if($tag_name == 'category_path') {
                    $temp[$tag_name] = str_replace("&gt;", "->", $product['last_category_tree']);
                }


                if($tag_name == 'imagesURL') {
                    $node = '<img1>'.$product['image_link'].'</img1>'."\n";

                    foreach ($product['additional_images_link'] as $key => $img_link) {
                        $node .= '<img'.($key+2).'>'.$img_link.'</img'.($key+2).'>'."\n";
                    }

                    $temp[$tag_name] = $node;
                }


                if($tag_name == 'availability') {
                    $temp[$tag_name] = $product['quantity'] ? $availability_in_stock : $availability_out_of_stock;
                }

                if($tag_name == 'stock') {
                    $temp[$tag_name] = $product['quantity'] ? 'Y' : 'N';
                }

                if($tag_name == 'identification') {
                    if(!empty($product['ean']))
                        $temp['ean'] = $product['ean'];
                    elseif(!empty($product['mpn']))
                        $temp['mpn'] = $product['mpn'];
                    elseif(!empty($product['sku']))
                        $temp['sku'] = $product['sku'];
                }

                if($tag_name == 'isbn' && !empty($product['isbn'])) {
                    $temp[$tag_name] = $product['isbn'];
                }

                if($tag_name == 'shipping') {
                    $temp[$tag_name] = $shipping;
                }


                if($tag_name == 'color') {
                    if(array_key_exists('splitted_by_color', $product) && !empty($product['splitted_by_color'])) {
                        $temp[$tag_name] = $product['splitted_by_color'];
                    } else if(isset($all_colors) && array_key_exists($product['product_id'], $all_colors)) {
                        $sliced_array = array_slice($all_colors[$product['product_id']], 0, 3);
                        $temp[$tag_name] = implode('/', $sliced_array);
                    }
                    continue;
                }

                if($tag_name == 'size') {
                    if(array_key_exists('splitted_by_size', $product) && !empty($product['splitted_by_size'])) {
                        $temp[$tag_name] = $product['splitted_by_size'];
                    } else if(isset($all_sizes) && array_key_exists($product['product_id'], $all_sizes) && !empty($all_sizes[$product['product_id']])) {
                        $temp[$tag_name] = $all_sizes[$product['product_id']][0];
                    }
                    continue;
                }


            }
            //</editor-fold>

            $feed_elmements[] = $temp;
        }

        $header  = '<?xml version="1.0" encoding="ISO-8859-7" ?>'."\n";
        $header .= '<store>'."\n";
        $header .= '<date><![CDATA[' . date("Y-m-d H:i:s") . ']]></date>'."\n";
        $header .= '<products>'."\n";

        $footer = '</products>'."\n";
        $footer .= '</store>';

        return array(
            'header' => $header,
            'elements' => $feed_elmements,
            'footer' => $footer,
            'node' => 'product'
        );
    }
}
?>