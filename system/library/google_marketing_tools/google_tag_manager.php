<?php
	namespace google_marketing_tools;
	class google_tag_manager extends master
    {
		public function get_code_head($gmt_data) {
		    $gtm_ids = $this->get_gtm_id();

		    $code = $this->get_generic_js_variables($gmt_data);

		    foreach ($gtm_ids as $gtm) {
		        $code .= "<script type=\"text/javascript\" nitro-exclude=\"\">(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
					new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
					j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
					'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
					})(window,document,'script','dataLayer','$gtm');</script>";
		    }

			return $code;
		}

		public function get_code_body() {
		    $gtm_ids = $this->get_gtm_id();
		    $code = '';

		    foreach ($gtm_ids as $gtm) {
		        $code = "<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id=$gtm\"
					height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>";
		    }

			return $code;
		}

		public function get_google_consent_compatibility_cookie_script() {
			if ($this->config->get("google_all_gdpr_cookie_script_status_".$this->id_store) && $this->config->get("google_all_gdpr_cookie_script_cdm_".$this->id_store)) {
				return '<script type="text/javascript" charset="UTF-8" src="'.$this->config->get("google_all_gdpr_cookie_script_cdm_".$this->id_store).'"></script>';
			}

			return '';
		}

		public function get_generic_js_variables($gmt_data) {
            $main_domain = ($this->config->get("config_ssl") != '' ? $this->config->get("config_ssl") : HTTPS_SERVER);

            if(empty($gmt_data))
                return '';

            $data = $gmt_data;
            $cached_url 	= !empty($data['general_data']['current_view']) && !in_array($data['general_data']['current_view'], array('checkout','purchase'));

            /*if ($this->_getConfig('cache_compatibility')) {
                $cache_enabled = true;
            } else {
                $cache_enabled	= defined('LIGHT_ENABLED') || $this->config->get("module_lscache_status") || $this->config->get("module_nitropack_status") || is_file(DIR_SYSTEM."config/nitropackio/compatibility.php");
            }*/

            $cache_enabled = $this->_getConfig('cache_compatibility');
			
            if(version_compare(VERSION, '1.5.6.4', '<')) {
                //getChild function looks not working
                $cache_enabled = false;
            }

        /*if($cache_enabled && $cached_url) {
                $domain_uri = substr($_SERVER['REQUEST_URI'], 1);
                $link = $main_domain.$domain_uri;
            } else {
                $link = $main_domain.'index.php?route=extension/module/gmt_datalayer/setup&v='.$this->generate_uuid();
        }*/

            $link = $main_domain.'index.php?route=extension/module/gmt_datalayer/setup&v='.$this->generate_uuid();

			$is_gc_enabled 		= (bool)$this->_getConfig('gdpr_status');
			$gc_settings_json 	= json_encode($this->_get_google_consent_config());
			
			$code = "<script type=\"text/javascript\" nitro-exclude=\"\">
                            // Google Marketing Tools - Declare basic variables
                            var dataLayer = [];
					        var dataLayer_setup_link = '".$link."';
					        var gmt_language = '".$this->GMTMaster->get_language_code()."';
							var is_debug_enabled = ".($this->GMTMaster->debug_enabled() ? 'true' : 'false').";
                			var is_google_consent_enabled = ".($is_gc_enabled ? 'true' : 'false').";
							var is_gmt_admin = ".(!$this->config->get('config_theme') ? 'true' : 'false').";
							var track_api_url = '".$this->url->link('extension/module/gmt_datalayer/save_order_tracking_status', '', true)."';
							var call_addons_url = '".$this->url->link('extension/module/gmt_datalayer/addons', '', true)."';
							var gc_settings = ".$gc_settings_json.";							
                     </script>";
		    return $code;
        }

		/**
		 * Get Google Consent Custom Code
		 * 
		 * @return string
		 */
		public function get_custom_code() {
			return htmlspecialchars_decode($this->_getConfig('gdpr_custom_code'));
		}

        public function get_products_listed() {
		    $product_hrefs = array();
            $gmt_data = $this->GMTData->get();
            if (!empty($gmt_data['gmt_data']['products_listed'])) {
                foreach ($gmt_data['gmt_data']['products_listed'] as $key => $prod) {
                    $prod_id = $prod['product']['product_id'];
                    $href = $prod['product']['url'];

                    if (!empty($href) && !array_key_exists($href, $product_hrefs)) {
                        $product_hrefs[$href] = $prod_id;
                    }
                }
            }

            return $product_hrefs;
        }

        public function get_promotions_listed() {
		    $promotions_hrefs = array();
            $product_hrefs = array();
            $gmt_data = $this->GMTData->get();
            if (!empty($gmt_data['gmt_data']['promotions_listed'])) {
                foreach ($gmt_data['gmt_data']['promotions_listed'] as $key => $prom) {
                    $prom_id = $prom['id'];
                    $href = $prom['url'];

                    if (!empty($href) && !array_key_exists($href, $promotions_hrefs)) {
                        $promotions_hrefs[$href] = $prom_id;
                    }
                }
            }

            return $promotions_hrefs;
        }

		private function _get_google_consent_config() {
			$data = array();

			$language_code = $this->GMTMaster->get_language_code();
			$information_id = $this->_getConfig('gdpr_link_page');
			
			$data['languages'][$language_code] = [
				'popup_title' 				=> $this->_getConfig('gdpr_title', '', true),
				'button_configure_title'	=> $this->_getConfig('gdpr_button_title', '', true),
				'popup_explain'				=> htmlspecialchars_decode($this->_getConfig('gdpr_text', '', true)),
				'link_title'				=> $this->_getConfig('gdpr_link_title', '', true),
				'button_accept_all'			=> $this->_getConfig('gdpr_button_accept_all_text', '', true),
				'button_accept_selection'	=> $this->_getConfig('gdpr_button_accept_selection_text', '', true),
				'button_configure'			=> $this->_getConfig('gdpr_button_configure_text', '', true),
				'button_refuse_text'		=> $this->_getConfig('gdpr_button_refuse_text', '', true),
				'necessary_label'			=> $this->_getConfig('gdpr_button_checkbox_necessary', '', true),
				'necessary_explain'			=> htmlspecialchars_decode($this->_getConfig('gdpr_button_checkbox_necessary_explain', '', true)),
				'preferences_label'			=> $this->_getConfig('gdpr_button_checkbox_preferences', '', true),
				'preferences_explain'		=> htmlspecialchars_decode($this->_getConfig('gdpr_button_checkbox_preferences_explain', '', true)),
				'statistics_label'			=> $this->_getConfig('gdpr_button_checkbox_statistics', '', true),
				'statistics_explain'		=> htmlspecialchars_decode($this->_getConfig('gdpr_button_checkbox_statistics_explain', '', true)),
				'marketing_label'			=> $this->_getConfig('gdpr_button_checkbox_marketing', '', true),
				'marketing_explain'			=> htmlspecialchars_decode($this->_getConfig('gdpr_button_checkbox_marketing_explain', '', true)),				
				'link_href'					=> !empty($information_id) ? $this->url->link('information/information', 'information_id=' . $information_id) : false
			];

            $data['gdpr_cookiebot_status']		= (bool)$this->_getConfig('gdpr_cookiebot_status');

			// General config
			$data['gdpr_acceptance_with_page_change']		= (bool)$this->_getConfig('gdpr_acceptance_with_page_change');
			$data['gdpr_acceptance_with_scroll']			= (bool)$this->_getConfig('gdpr_acceptance_with_scroll');

			// Floating configure button settings
			$data['gdpr_button_position'] 					= $this->_getConfig('gdpr_button_position');
			$data['gdpr_button_configuration'] 				= empty($data['gdpr_button_position']) ? false : true;
            $data['gdpr_lock_screen'] 					    = $this->_getConfig('gdpr_lock_screen');
			$data['gdpr_popup_delay_time'] 					= abs((int)$this->_getConfig('gdpr_popup_delay_time') === 0 ? 1000 : (int)$this->_getConfig('gdpr_popup_delay_time'));

			// Consent bar settings and languages
			$data['gdpr_popup_position'] 					= $this->_getConfig('gdpr_popup_position');
			$data['gdpr_bar_text_button_configure'] 		= $this->_getConfig('gdpr_bar_text_button_configure');
			$data['gdpr_bar_background_button_refuse'] 		= $this->_getConfig('gdpr_bar_background_button_refuse');
			$data['gdpr_bar_text_button_refuse'] 			= $this->_getConfig('gdpr_bar_text_button_refuse');
            
			// Consent bar aspect
			$data['gdpr_bar_main_color']                    = $this->_getConfig('gdpr_bar_main_color', '229ac8');
			$data['gdpr_button_icon_color']                 = $this->_getConfig('gdpr_button_icon_color', 'ffffff');
			$data['gdpr_bar_background_button_configure']   = $this->_getConfig('gdpr_bar_background_button_configure', 'daebf3');
			$data['gdpr_bar_background']                    = $this->_getConfig('gdpr_bar_background', 'ffffff');
			$data['gdpr_bar_color']                         = $this->_getConfig('gdpr_bar_color', '0a010a');
			$data['gdpr_bar_button_accept_all_color']       = $this->_getConfig('gdpr_bar_button_accept_all_color', 'ffffff');        
			$data['gdpr_bar_button_accept_selection_color'] = $this->_getConfig('gdpr_bar_button_accept_selection_color', '63b0cc');
        	$data['gdpr_bar_text_button_accept_selection']  = $this->_getConfig('gdpr_bar_text_button_accept_selection', 'ffffff');
			$data['gdpr_bar_necessary_checkbox_color']      = $this->_getConfig('gdpr_bar_necessary_checkbox_color', $data['gdpr_bar_main_color']);
			$data['gdpr_bar_preferences_checkbox_color']    = $this->_getConfig('gdpr_bar_preferences_checkbox_color', $data['gdpr_bar_main_color']);
			$data['gdpr_bar_statistics_checkbox_color']     = $this->_getConfig('gdpr_bar_statistics_checkbox_color', $data['gdpr_bar_main_color']);
			$data['gdpr_bar_marketing_checkbox_color']      = $this->_getConfig('gdpr_bar_marketing_checkbox_color', $data['gdpr_bar_main_color']);
			
			return $data;
		}

		/**
		 * Get config
		 * 
		 * @param string $config
		 * @param string $default
		 * @param bool $is_multilanguage
		 * @return string
		 */
		private function _getConfig( $config, $default = '', $is_multilanguage = false ) {
			$key = "google_all_{$config}_{$this->id_store}";

			if ($is_multilanguage) {
				$key .= "_{$this->language_id}";
			}

			$value = $this->config->get($key);

			return $value ? $value : $default;
		}
	}
?>