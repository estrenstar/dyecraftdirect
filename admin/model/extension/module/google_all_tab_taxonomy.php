<?php
    class ModelExtensionModuleGoogleAllTabTaxonomy extends ModelExtensionModuleGoogleAll
    {
        private $extension_version;

        public function __construct($registry) {
            parent::__construct($registry);
            $this->load->language($this->real_extension_type.'/google_all_tab_taxonomy');

            $this->extension_version = $registry->has('gmt_extension_version') ? $this->gmt_extension_version : date('Ymdhis');

            $this->taxonomy_langs = array(
                'en-AU' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/au.png\'>Australia',
                'pt-BR' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/br.png\'>Brazil',
                'cs-CZ' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/cz.png\'>Czech Republic',
                'da-DK' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/dk.png\'>Denmark',
                'fr-FR' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/fr.png\'>France',
                'de-DE' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/de.png\'>Germany',
                'it-IT' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/it.png\'>Italy',
                'ja-JP' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/jp.png\'>Japan',
                'nl-NL' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/nl.png\'>Netherlands',
                'no-NO' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/no.png\'>Norway',
                'pl-PL' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/pl.png\'>Poland',
                'ru-RU' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/ru.png\'>Russia',
                'es-ES' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/es.png\'>Spain',
                'sv-SE' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/se.png\'>Sweden',
                'tr-TR' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/tr.png\'>Turkey',
                'en-GB' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/gb.png\'>United Kingdom',
                'en-US' => '<img src=\''.$this->api_url.'opencart_admin/common/img/flags-mini/us.png\'>USA',
            );

            $this->load->model('catalog/category');
            $categories = $this->model_catalog_category->getCategories(true);
            if (empty($categories))
                $categories = $this->model_catalog_category->getCategories();

            $this->categories = $categories;
        }

        public function get_fields() {
            $this->document->addStyle($this->api_url.'opencart_admin/google_marketing_tools/css/tab_taxonomy.css?v='.$this->extension_version);
            $this->document->addScript($this->api_url.'opencart_admin/google_marketing_tools/js/tab_taxonomy.js?v='.$this->extension_version);

            $fields = array(
                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-language"></i>'.$this->_lang('taxonomy_select_language_legend'),
                    'remove_border_button' => true
                ),
                array(
                    'label' => $this->_lang('taxonomy_select_language'),
                    'type' => 'select',
                    'options' => $this->taxonomy_langs,
                    'name' => 'language',
                    'class_container' => 'select_languages',
                    'after' => '<br>'.$this->model_extension_devmanextensions_tools->get_remodal('select_language', $this->_lang('taxonomy_select_language_remodal_title'), $this->_lang('taxonomy_select_language_remodal_description'), array('button_cancel' => false, 'link' => 'taxonomy_select_language_link_title')),
                ),
                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-google"></i>'.$this->_lang('taxonomy_master_categoy_legend'),
                    'remove_border_button' => true
                ),
                array(
                   'type' => 'text',
                   'label' => $this->_lang('taxonomy_master_categoy_label'),
                   'help' => $this->_lang('taxonomy_master_categoy_help'),
                   'placeholder' => $this->_lang('taxonomy_autocomplete_placeholder'),
                   'name' => 'feed_taxonomy_cat_master',
                   'after' => '<input type="hidden">',
                ),
                array(
                    'type' => 'legend',
                    'text' => '<i class="fa fa-google"></i>'.$this->_lang('taxonomy_category_legend'),
                    'remove_border_button' => true
                ),
            );

            foreach ($this->categories as $key_cat => $cat) {
               $fields[] = array(
                   'type' => 'text',
                   'label' => $cat['name'],
                   'placeholder' => $this->_lang('taxonomy_autocomplete_placeholder'),
                   'name' => 'feed_taxonomy_cat_'.$cat['category_id'],
                   'after' => '<input type="hidden">',
                   'span_input' => 8,
                   'span_label' => 4,
               );
            }

            return $fields;
        }

        public function _send_custom_variables_to_view($variables) {
            $variables['taxonomy_url_autocomplete_category'] = htmlspecialchars_decode($this->url->link($this->real_extension_type.'/'.$this->extension_name.'&ajax_function=autocomplete_category', $this->token_name.'=' . $this->session->data[$this->token_name], 'SSL'));
            return $variables;
        }

        public function _check_ajax_function($function_name) {
            if($function_name == 'autocomplete_category') {
                $this->autocomplete_category();
            }
        }

        public function _check_taxonomy_files() {
            $taxonomy_files = array(
                'taxonomy.en-AU.txt',
                'taxonomy.pt-BR.txt',
                'taxonomy.cs-CZ.txt',
                'taxonomy.da-DK.txt',
                'taxonomy.fr-FR.txt',
                'taxonomy.de-DE.txt',
                'taxonomy.it-IT.txt',
                'taxonomy.ja-JP.txt',
                'taxonomy.nl-NL.txt',
                'taxonomy.no-NO.txt',
                'taxonomy.pl-PL.txt',
                'taxonomy.ru-RU.txt',
                'taxonomy.es-ES.txt',
                'taxonomy.sv-SE.txt',
                'taxonomy.tr-TR.txt',
                'taxonomy.en-GB.txt',
                'taxonomy.en-US.txt',
            );

            $path_taxonomy_server = $this->api_url.'opencart_admin/google_marketing_tools/taxonomy/';

            if(!is_dir($this->path_taxonomy))
                mkdir($this->path_taxonomy, 0755);

            foreach ($taxonomy_files as $key => $filename) {
                if(!is_file($this->path_taxonomy.$filename)) {
                    try {
                        $f = file_put_contents($this->path_taxonomy.$filename, fopen($path_taxonomy_server.$filename, 'r'), LOCK_EX);
                        if(FALSE === $f)
                            throw new Exception($this->language->get('libraries_download_error_download'));
                    } catch (Exception $e) {
                    }
                }

            }
        }

        public function autocomplete_category() {
            $country            = $this->request->get['country_id'];
            $text               = $this->request->get['text'];
            $google_categories  = explode("\n", file_get_contents($this->path_taxonomy.'/taxonomy.'.$country.'.txt'));
            
            if (!empty($google_categories) && (isset($google_categories[0][0]) && $google_categories[0][0] == "#" || strpos($google_categories[0], 'Google_Product_Taxonomy_Version') !== false)) {
                unset($google_categories[0]);
            }

            $result = array();

            foreach ($google_categories as $key => $value) {
                if (in_array($country, array('no-NO','tr-TR','fr-FR'))) {
                    $value = mb_convert_encoding($value, "Windows-1252", "UTF-8");
                }

                if (!empty($text) && !empty($value) && strpos(strtoupper($value), strtoupper($text)) !== false) {
                    $result[] = array(
                        'id' => $key,
                        'name' => $value
                    );
                }
            }
            
            echo json_encode($result); die;
        }
    }
?>