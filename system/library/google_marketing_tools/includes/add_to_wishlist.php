<?php
if ($this->GMTMaster && $this->GMTMaster->gmt_is_enabled()) {
    if (is_array($product_info) && !empty($product_info['product_id'])) {
        $manufacturer = $this->GMTMaster->get_product_manufacturer($product_info['product_id']);
        $category_name = $this->GMTMaster->get_product_category_name($product_info['product_id']);

        $base_price = !empty($product_info['special']) ? $product_info['special'] : $product_info['price'];

        $event_id = $this->GMTMaster->generate_uuid();
        $price = $this->GMTMaster->format_price($this->tax->calculate($base_price, $product_info['tax_class_id'], $this->config->get('config_tax')), true);
        $price_eur = $this->GMTMaster->format_price($this->tax->calculate($base_price, $product_info['tax_class_id'], $this->config->get('config_tax')), true, false, 'EUR');

        $product_info = array_merge($product_info, $this->DataLayer->_add_alternative_product_ids($product_info));

        $json['gtm_id'] = $product_info['product_id'];
        $json['gtm_id_ee'] = $product_info['product_id_ee'];
        $json['gtm_id_gdr_1'] = $product_info['product_id_gdr_1'];
        $json['gtm_id_gdr_2'] = $product_info['product_id_gdr_2'];
        $json['gtm_id_fb'] = $product_info['product_id_fb'];
        $json['gtm_name'] = $product_info['name'];
        $json['gtm_brand'] = $manufacturer;
        $json['gtm_category'] = $category_name;
        $json['gtm_price'] = $price;
        $json['gtm_price_eur'] = $price_eur;
        $json['fb_event_id'] = $event_id;

        // Call GMT addons
        $gmt_addons = !is_null($this->DevmanGmtAddons) && is_array($this->DevmanGmtAddons) ? $this->DevmanGmtAddons : array();
        
        $data = array(
            'event_name'        => 'addToWishlist',
            'event_id'          => $event_id,
            'product_id'        => $product_info['product_id'],
            'currency'          => $this->session->data['currency'],
            'price'             => $price,
            'price_without_tax' => $base_price,
            'product_info'      => $product_info,
            'category'          => $category_name
        );

        $data = array_merge($json, $data);

        foreach ($gmt_addons as $addon) {
            $this->load->controller('extension/module/'.$addon.'/track', $data);
        }
    }
}
?>