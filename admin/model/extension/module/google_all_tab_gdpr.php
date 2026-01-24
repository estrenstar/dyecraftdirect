<?php
class ModelExtensionModuleGoogleAllTabGdpr extends ModelExtensionModuleGoogleAll
{
    public function __construct($registry) {
        parent::__construct($registry);
        
        $this->load->language($this->real_extension_type.'/google_all_tab_gdpr');
    }

    public function get_fields() {
        $this->load->model("catalog/information");
        
        $information_pages_query = $this->model_catalog_information->getInformations();
        $information_pages = array(
            '' => $this->_lang("text_none")
        );

        foreach ($information_pages_query as $inf) {
            $information_pages[$inf['information_id']] = $inf['title'];
        }

        $fields = array(

            array(
                'text'          => '<i class="fa fa-cog"></i> '.$this->_lang("gdpr_compatibilities"),
                'type'          => 'legend'
            ),
            array(
                'label'         => $this->_lang("gdpr_cookie_script_status"),
                'type'          => 'boolean',
                'name'          => 'gdpr_cookie_script_status',
                'class_container'   => 'toogle_main_field',
            ),

            array(
                'label'         => $this->_lang("gdpr_cookie_script_cdm"),
                'help'          => $this->_lang("gdpr_cookie_script_cdm_help"),
                'type'          => 'text',
                'name'          => 'gdpr_cookie_script_cdm',
                'class_container'   => 'toogle_seconday_field '.$this->extension_group_config.'_gdpr_cookie_script_status'
            ),

            array(
                'label'         => $this->_lang("gdpr_cookiebot_status"),
                'type'          => 'boolean',
                'name'          => 'gdpr_cookiebot_status',
                'class_container'   => 'toogle_main_field',
            ),
            // Consent bar general configuration
            array(
                'text'          => '<i class="fa fa-cog"></i>'.$this->_lang('gdpr_configuration'),
                'type'          => 'legend'
            ),
            array(
                'label'         => $this->_lang('gdpr_status'),
                'type'          => 'boolean',
                'name'          => 'gdpr_status'
            ),
            array(
                'label'         => $this->_lang('gdpr_acceptance_with_page_change'),
                'help'          => $this->_lang('gdpr_acceptance_with_page_change_help'),
                'type'          => 'boolean',
                'name'          => 'gdpr_acceptance_with_page_change'
            ),
            array(
                'label'         => $this->_lang('gdpr_acceptance_with_scroll'),
                'help'          => $this->_lang('gdpr_acceptance_with_scroll_help'),
                'type'          => 'boolean',
                'name'          => 'gdpr_acceptance_with_scroll'
            ),
            array(
                'label'         => $this->_lang('gdpr_popup_position'),
                'type'          => 'select',
                'options'       => array(
                    'middle'        => $this->_lang('gdpr_popup_position_middle'),
                    'top_left'      => $this->_lang('gdpr_popup_position_top_left'),
                    'top_right'     => $this->_lang('gdpr_popup_position_top_right'),
                    'bottom_full'   => $this->_lang('gdpr_popup_position_bottom_full'),
                    'bottom_left'   => $this->_lang('gdpr_popup_position_bottom_left'),
                    'bottom_right'  => $this->_lang('gdpr_popup_position_bottom_right')
                ),
                'name'          => 'gdpr_popup_position'
            ),
            array(
                'label'         => $this->_lang('gdpr_lock_screen'),
                'help'          => $this->_lang('gdpr_lock_screen_info'),
                'type'          => 'boolean',
                'name'          => 'gdpr_lock_screen'
            ),
            array(
                'label'         => $this->_lang('gdpr_popup_delay_time'),
                'help'          => $this->_lang('gdpr_popup_delay_time_help'),
                'type'          => 'text',
                'default'       => '1000',
                'name'          => 'gdpr_popup_delay_time'
            ),

            // Consent bar texts
            array(
                'text'          => '<i class="fa fa-cog"></i>'.$this->_lang('gdpr_configuration_language'),
                'type'          => 'legend'
            ),
            array(
                'label'         => $this->_lang('gdpr_title'),
                'type'          => 'text',
                'name'          => 'gdpr_title',
                'default'       => 'This site uses cookies.',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_text'),
                'type'          => 'html_editor',
                'name'          => 'gdpr_text',
                'default'       => '<p>Some of these cookies are essential, while others help us to improve your experience by providing insights into how the site is being used.</p></p>',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_link_title'),
                'type'          => 'text',
                'name'          => 'gdpr_link_title',
                'multilanguage' => true,
                'default'       => 'More information'
            ),
            array(
                'label'         => $this->_lang('gdpr_link_page'),
                'type'          => 'select',
                'options'       => $information_pages,
                'name'          => 'gdpr_link_page'
            ),
            array(
                'label'         => $this->_lang('gdpr_button_accept_all_text'),
                'type'          => 'text',
                'name'          => 'gdpr_button_accept_all_text',
                'default'       => 'Accept all',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_button_accept_selection_text'),
                'type'          => 'text',
                'name'          => 'gdpr_button_accept_selection_text',
                'default'       => 'Accept selection',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_button_configure_text'),
                'type'          => 'text',
                'name'          => 'gdpr_button_configure_text',
                'default'       => 'Customize',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_button_refuse_text'),
                'type'          => 'text',
                'name'          => 'gdpr_button_refuse_text',
                'default'       => 'Refuse all',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_button_checkbox_necessary'),
                'type'          => 'text',
                'name'          => 'gdpr_button_checkbox_necessary',
                'default'       => 'Necessary',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_button_checkbox_necessary_explain'),
                'type'          => 'html_editor',
                'name'          => 'gdpr_button_checkbox_necessary_explain',
                'default'       => 'Enables browser storage related to security such as authentication functionality, fraud prevention, and other user protection',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_button_checkbox_preferences'),
                'type'          => 'text',
                'name'          => 'gdpr_button_checkbox_preferences',
                'default'       => 'Preferences',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_button_checkbox_preferences_explain'),
                'type'          => 'html_editor',
                'name'          => 'gdpr_button_checkbox_preferences_explain',
                'default'       => 'Enables storage that supports the functionality of the website or app e.g. language settings',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_button_checkbox_statistics'),
                'type'          => 'text',
                'name'          => 'gdpr_button_checkbox_statistics',
                'default'       => 'Statistics',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_button_checkbox_statistics_explain'),
                'type'          => 'html_editor',
                'name'          => 'gdpr_button_checkbox_statistics_explain',
                'default'       => 'Enables storage (such as cookies) related to analytics e.g. visit duration',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_button_checkbox_marketing'),
                'type'          => 'text',
                'name'          => 'gdpr_button_checkbox_marketing',
                'default'       => 'Marketing',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_button_checkbox_marketing_explain'),
                'type'          => 'html_editor',
                'name'          => 'gdpr_button_checkbox_marketing_explain',
                'default'       => 'Enables storage (such as cookies) related to advertising',
                'multilanguage' => true
            ),

            // Consent bar aspect
            array(
                'text'          => '<i class="fa fa-cog"></i>'.$this->_lang('gdpr_configuration_aspect'),
                'type'          => 'legend'
            ),
            array(
                'label'         => $this->_lang('gdpr_main_color'),
                'help'          => $this->_lang('gdpr_main_color_help'),
                'type'          => 'colpick',
                'default'       => '229ac8',
                'name'          => 'gdpr_bar_main_color'
            ),
            array(
                'label'         => $this->_lang('gdpr_bar_background'),
                'type'          => 'colpick',
                'default'       => 'ffffff',
                'name'          => 'gdpr_bar_background'
            ),
            array(
                'label'         => $this->_lang('gdpr_bar_color'),
                'type'          => 'colpick',
                'default'       => '0a010a',
                'name'          => 'gdpr_bar_color'
            ),
            array(
                'label'         => $this->_lang('gdpr_bar_button_accept_all_color'),
                'type'          => 'colpick',
                'default'       => 'ffffff',
                'name'          => 'gdpr_bar_button_accept_all_color'
            ),
            array(
                'label'         => $this->_lang('gdpr_bar_button_accept_selection_color'),
                'type'          => 'colpick',
                'default'       => '63b0cc',
                'name'          => 'gdpr_bar_button_accept_selection_color'
            ),
            array(
                'label'         => $this->_lang('gdpr_bar_text_button_accept_selection'),
                'type'          => 'colpick',
                'default'       => '000000',
                'name'          => 'gdpr_bar_text_button_accept_selection'
            ),
            array(
                'label'         => $this->_lang('gdpr_bar_background_button_configure'),
                'type'          => 'colpick',
                'default'       => 'daebf3',
                'name'          => 'gdpr_bar_background_button_configure'
            ),
            array(
                'label'         => $this->_lang('gdpr_bar_text_button_configure'),
                'type'          => 'colpick',
                'default'       => '000000',
                'name'          => 'gdpr_bar_text_button_configure'
            ),
            array(
                'label'         => $this->_lang('gdpr_bar_background_button_refuse'),
                'type'          => 'colpick',
                'default'       => 'ddd',
                'name'          => 'gdpr_bar_background_button_refuse'
            ),
            array(
                'label'         => $this->_lang('gdpr_bar_text_button_refuse'),
                'type'          => 'colpick',
                'default'       => '000000',
                'name'          => 'gdpr_bar_text_button_refuse'
            ),
            array(
                'label'         => $this->_lang('gdpr_bar_necessary_checkbox_color'),
                'help'          => $this->_lang('gdpr_bar_necessary_checkbox_color_help'),
                'type'          => 'colpick',
                'default'       => '229ac8',
                'name'          => 'gdpr_bar_necessary_checkbox_color'
            ),
            array(
                'label'         => $this->_lang('gdpr_bar_preferences_checkbox_color'),
                'help'          => $this->_lang('gdpr_bar_preferences_checkbox_color_help'),
                'type'          => 'colpick',
                'default'       => '229ac8',
                'name'          => 'gdpr_bar_preferences_checkbox_color'
            ),
            array(
                'label'         => $this->_lang('gdpr_bar_statistics_checkbox_color'),
                'help'          => $this->_lang('gdpr_bar_statistics_checkbox_color_help'),
                'type'          => 'colpick',
                'default'       => '229ac8',
                'name'          => 'gdpr_bar_statistics_checkbox_color'
            ),
            array(
                'label'         => $this->_lang('gdpr_bar_marketing_checkbox_color'),
                'help'          => $this->_lang('gdpr_bar_marketing_checkbox_color_help'),
                'type'          => 'colpick',
                'default'       => '229ac8',
                'name'          => 'gdpr_bar_marketing_checkbox_color'
            ),

            // Floating configuration button general settings
            array(
                'text'          => '<i class="fa fa-cog"></i>'.$this->_lang('gdpr_button_configuration'),
                'type'          => 'legend'
            ),
            array(
                'label'         => $this->_lang('gdpr_button_position'),
                'type'          => 'select',
                'options'       => array(
                    ''              => $this->_lang('gdpr_button_none_button'),
                    'top_left'      => $this->_lang('gdpr_button_position_top_left'),
                    'top_right'     => $this->_lang('gdpr_button_position_top_right'),
                    'bottom_left'   => $this->_lang('gdpr_button_position_bottom_left'),
                    'bottom_right'  => $this->_lang('gdpr_button_position_bottom_right')
                ),
                'name'          => 'gdpr_button_position'
            ),
            array(
                'label'         => $this->_lang('gdpr_button_title'),
                'type'          => 'text',
                'name'          => 'gdpr_button_title',
                'default'       => 'Cookies configuration',
                'multilanguage' => true
            ),
            array(
                'label'         => $this->_lang('gdpr_button_icon_color'),
                'type'          => 'colpick',
                'default'       => 'ffffff',
                'name'          => 'gdpr_button_icon_color'
            ),

            // Custom code
            array(
                'text'          => '<i class="fa fa-cog"></i>'.$this->_lang('gdpr_custom_code'),
                'type'          => 'legend'
            ),
            array(
                'label'         => $this->_lang('gdpr_custom_code'),
                'help'          => $this->_lang('gdpr_custom_code_help'),
                'type'          => 'textarea',
                'name'          => 'gdpr_custom_code',
                'style'         => 400
            )
        );

        return $fields;
    }

    public function _send_custom_variables_to_view( $variables ) {
        return $variables;
    }

    public function _check_ajax_function( $function_name ) {
    }
}
?>