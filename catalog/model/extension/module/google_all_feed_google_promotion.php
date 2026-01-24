<?php
class ModelExtensionModuleGoogleAllFeedGooglePromotion extends ModelExtensionModuleGoogleAllFeed {
    public function process_elements($feed_configuration, $elements, $element_ids) {
        $tags = array(
            'g:item_id' => 'product_identificator',
            'g:brand' => 'manufacturer',
            'g:image_link' => 'image_link',
            'g:promotion_id' => function($product){
                //TODO: define product identification, now id with a prepended "p"
                return 'p'.$product['product_identificator'];
            },
            'g:product_applicability' => function($product){
                //TODO: define product applicability type (ALL_PRODUCTS or SPECIFIC_PRODUCTS)
                return 'ALL_PRODUCTS';
            },
            'g:offer_type' => function($product){
                //TODO: define promotion code behavior
                return 'NO_CODE';
            },
            'g:long_title' => function($product){
                return $product['name'];
            },
            'g:promotion_effective_dates' => function($product){
                $date_start = date(DATE_ISO8601, strtotime($product['special_start'].' 00:00:00'));
                $date_end = date(DATE_ISO8601, strtotime($product['special_end'].' 00:00:00'));
                return $date_start.'/'.$date_end;
            },
            'g:redemption_channel' => function($product){
                return 'ONLINE';
            },
            'g:product_type' => function($product){
                if (!empty($product['last_category_tree'])) {
                    return $this->format_xml_value($product['last_category_tree']);
                }else{
                    return null;
                }
            },
            // Product filters
//            'item_​​group_​​id',
//            'item_id_exclusion',
//            'product_type_exclusion',
//            'brand_exclusion',
//            'item_group_id_exclusion',
            // Promotion details
            'g:minimum_purchase_quantity' => 'minimum',
//            'minimum_purchase_amount',
//            'membership_type',

            // Promotion categories
            'g:percent_off' => function($product){
                $orgPrice = $product['price_formatted'];
                $dicountPrice = $product['special_formatted'];
                $percent = ($orgPrice - $dicountPrice) * 100 / $orgPrice;
                return number_format( $percent, 2 );
            },
            'g:money_off_amount'  => function($product){
                // TODO: defined as price_formatted - special_formatted, this is ok?
                $specialFormatted = $product['special_formatted'];
                $priceFormatted = $product['price_formatted'];
                $diff = $priceFormatted - $specialFormatted;
                return $diff . ' ' . $product['currency_code'];
            },
//            'get_this_quantity_ discounted',
//            'free_shipping',
//            'free_gift_value',
//            'free_gift_description',
//            'free_gift_item_id',
            // Limits
//            'limit_quantity',
//            'limit_value',
            // Additional attributes
            'g:promotion_display_dates' => function($product){
                $date_start = date(DATE_ISO8601, strtotime($product['special_start'].' 00:00:00'));
                $date_end = date(DATE_ISO8601, strtotime($product['special_end'].' 00:00:00'));
                return $date_start.'/'.$date_end;
            },
            'g:description' => function($product){
                return $this->format_xml_description($product['description'], 5000);
            },
//            'generic_redemption_code',
//            'fine_print',
            'g:promotion_price' => function($product){
                return $product['special_formatted'].' '.$product['currency_code'];
            },

        );


        $header  = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        $header .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">'."\n";
        $header .= '<channel>'."\n";
        $header .= '<title><![CDATA[' . $this->get_config_meta_title() . ']]></title>'."\n";
        $header .= '<description><![CDATA[' . $this->get_config_meta_description() . ']]></description>'."\n";
        $header .= '<link><![CDATA[' . HTTPS_SERVER . ']]></link>'."\n";

        $footer = '</channel>'."\n";
        $footer .= '</rss>';

        return array(
            'header' => $header,
            'elements' => $this->elementsToFeed(array_filter($elements, function ($product){
                return $product['special'] ? true : false;
            }), $tags),
            'footer' => $footer,
            'node' => 'item'
        );
    }
}
?>