<?php
class ModelExtensionModuleGoogleAllTabServerSide extends ModelExtensionModuleGoogleAll {
    private $extesion_version;
    
    public function __construct($registry) {
        parent::__construct($registry);

        $this->load->language($this->real_extension_type.'/google_all_tab_server_side');
        $this->extension_version = $registry->has('gmt_extension_version') ? $this->gmt_extension_version : date('Ymdhis');
    }

    public function get_fields() {
        $this->document->addStyle('view/stylesheet/devmanextensions/google_marketing_tools/tab_server_side.css?v='.$this->extension_version);

        $token_name = version_compare(VERSION, '3', '>=') ? 'user_token' : 'token';

        $addons = array(
            array(
                'name'      => 'Server Side Tracking - GA4',
                'icon'      => $this->api_url.'opencart_admin/google_marketing_tools/img/icons/ga4.jpg',
                'url'       => 'https://devmanextensions.com/cart?auto_add_extension_id=72a6c3a5-4e9a-4a9b-9ea7-ca3119e18c85&auto_add_units=1',
                'installed' => $this->_is_extension_installed('google_all_sst_ga4'),
                'setting'   => $this->url->link("extension/module/google_all_sst_ga4", $token_name.'=' . $this->session->data[$token_name])
            ),
            array(
                'name'      => 'Server Side Tracking - Tiktok',
                'icon'      => $this->api_url.'opencart_admin/google_marketing_tools/img/icons/tiktok_pixel.jpg',
                'url'       => 'https://devmanextensions.com/cart?auto_add_extension_id=5420686f-9450-4afa-a9c1-0994fa641jj7&auto_add_units=1',
                'installed' => $this->_is_extension_installed('google_all_sst_tiktok'),
                'setting'   => $this->url->link("extension/module/google_all_sst_tiktok", $token_name.'=' . $this->session->data[$token_name])
            ),
            array(
                'name'      => 'Server Side Tracking - Facebook',
                'icon'      => $this->api_url.'opencart_admin/google_marketing_tools/img/icons/facebook_pixel.jpg',
                'url'       => 'https://devmanextensions.com/cart?auto_add_extension_id=5420686f-9450-4afa-a9c1-0994fa641gg5&auto_add_units=1',
                'installed' => $this->_is_extension_installed('google_all_sst_facebook'),
                'setting'   => $this->url->link("extension/module/google_all_sst_facebook", $token_name.'=' . $this->session->data[$token_name])
            )
        );
        
        $html = '<ul class="addon-list">';

        foreach ($addons as $addon) {
            $classes     = $addon['installed'] ? 'btn get-btn disabled' : 'btn get-btn';
            $action_name = $addon['installed'] ? '<i class="fa fa-check-circle"></i> '.$this->_lang('text_installed') : $this->_lang('text_buy_addon');

            $html .= '<li class="addon-container">';
                $html .= '<div class="name-icon-container">';
                    $html .= '<img class="tag_icon addon-icon" src="'.$addon['icon'].'">';
                    $html .= '<span class="addon-name"><b>' . $addon['name'] . '</b></span>';
                $html .= '</div>';

                $html .= '<div>';
                    $html .= sprintf('<a href="%s" target="blank" class="%s">%s</a>', $addon['url'], $classes, $action_name);
                    
                    if ($addon['installed']) {
                        $html .= sprintf('<a href="%s" target="blank" class="btn btn-setting"><i class="fa fa-cog"></i></a>', $addon['setting']);
                    }
                $html .= '</div>';
            $html .= '</li>';
        }

        $html .= '</ul>';

        $fields = array(
            array(
                'type'          => 'html_hard',
                'html_code'     => $this->_lang('text_description'),
                'multi_store'   => false
            ),
            array(
                'type'          => 'html_hard',
                'html_code'     => $html,
                'multi_store'   => false
            )
        );

        return $fields;
    }

    public function _send_custom_variables_to_view($variables) {
        return $variables;
    }

    public function _check_ajax_function( $function_name ) {
        
    }

    private function _is_extension_installed( $extension_code ) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `type` = 'module' AND `code` = '" . $this->db->escape($extension_code) . "'");

        if ($query->row) {
            return true;
        } else {
            return false;
        }
    }
}
?>