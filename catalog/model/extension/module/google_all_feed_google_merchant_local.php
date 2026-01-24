<?php
class ModelExtensionModuleGoogleAllFeedGoogleMerchantLocal extends ModelExtensionModuleGoogleAllFeed {
    public function process_elements($feed_configuration, $elements, $element_ids) {

        $pickup_method = !empty($feed_configuration['profile']['google_all_feed_config_pickup_method']) ? $feed_configuration['profile']['google_all_feed_config_pickup_method'] : false;
        $pickup_sla = !empty($feed_configuration['profile']['google_all_feed_config_pickup_sla']) ? $feed_configuration['profile']['google_all_feed_config_pickup_sla'] : false;
        $store_code = !empty($feed_configuration['profile']['google_all_feed_config_store_code']) ? $feed_configuration['profile']['google_all_feed_config_store_code'] : false;

        $tags = array(
            'g:id' => 'product_identificator',
            'title' => 'name',
            'g:quantity' => 'quantity',
        );

        //<editor-fold desc="Exclusive code to this feed type">
        $special_tags = array(
            'g:price',
            'g:sale_price',
            'g:sale_price_effective_date',
            'g:availability',
            'g:store_code',
            'g:pickup_method',
            'g:pickup_sla',
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
                if($tag_name == 'g:availability_date' && $product['quantity'] <= 0) {
                    $temp[$tag_name] = date("c", strtotime($product['date_available']." 00:00:00"));
                    continue;
                }

                if($tag_name == 'g:price') {
                    $temp[$tag_name] = $product['price_formatted'].' '.$product['currency_code'];
                    continue;
                }

                if($tag_name == 'g:sale_price' && $product['special'] > 0) {
                    $temp[$tag_name] = $product['special_formatted'].' '.$product['currency_code'];
                    continue;
                }

                if ($tag_name == 'g:sale_price_effective_date' && isset($product['special']) && $product['special'] > 0) {

                    if($product['special_start'] == '0000-00-00 00:00:00' || empty($product['special_start']) || $product['special_start'] == '0000-00-00')
                        $product['special_start'] = date('Y-m-d 00:00:00');
                    $this->format_date_ISO_8601($product['special_start']);

                    if($product['special_end'] == '0000-00-00 00:00:00' || empty($product['special_end']) || $product['special_end'] == '0000-00-00') {
                        $time = strtotime(date("Y-m-d"));
                        $product['special_end'] = date("Y-m-d 23:59:59", strtotime("+1 month", $time));
                    }

                    $this->format_date_ISO_8601($product['special_end']);

                    $temp[$tag_name] = $product['special_start'].'/'.$product['special_end'];
                }


                if($tag_name == 'g:availability') {
                    $temp[$tag_name] = $product['quantity'] > 0 ? 'in stock' : 'out of stock';
                    continue;
                }


                if($tag_name == 'g:pickup_method' && !empty($pickup_method)) {
                    $temp[$tag_name] = $pickup_method;
                    continue;
                }

                if($tag_name == 'g:pickup_sla' && !empty($pickup_sla)) {
                    $temp[$tag_name] = $pickup_sla;
                    continue;
                }

                if($tag_name == 'g:store_code' && !empty($store_code)) {
                    $temp[$tag_name] = $store_code;
                    continue;
                }
            }
            //</editor-fold>

            $feed_elmements[] = $temp;
        }

        $header  = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        $header .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">'."\n";
        $header .= '<channel>'."\n";
        $header .= '<title><![CDATA[' . $this->get_config_meta_title() . ']]></title>'."\n";
        $header .= '<description><![CDATA[' . $this->get_config_meta_description() . ']]></description>'."\n";
        $header .= '<link><![CDATA[' . $this->check_link_domain($this->config->get('config_url')) . ']]></link>'."\n";

        $footer = '</channel>'."\n";
        $footer .= '</rss>';

        return array(
            'header' => $header,
            'elements' => $feed_elmements,
            'footer' => $footer,
            'node' => 'item'
        );
    }
}
?>