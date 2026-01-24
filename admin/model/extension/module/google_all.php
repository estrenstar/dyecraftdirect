<?php
    class ModelExtensionModuleGoogleAll extends Model {

        public function escape_database_field($name) {
            return "`".$name."`";
        }
        public function escape_database_value($value) {
            return "'".$this->db->escape(str_replace('"', '&quot;', $value))."'";
        }
        public function escape_database_table_name($name) {
            return "`".$this->db_prefix.$name."`";
        }
        public function _lang($lang_key) {
            return $this->language->get($lang_key);
        }
        public function get_all_categories_branches_select() {
            $this->load->model('catalog/category');
            $categories = $this->model_catalog_category->getCategories(true);
            if (empty($categories))
                $categories = $this->model_catalog_category->getCategories();

            $final_categories = array();

            if(is_array($categories)) {
                foreach ($categories as $key => $cat_info) {
                    $final_categories[$cat_info['category_id']] = $cat_info['name'];
                }
            }

            return $final_categories;
        }
        public function get_all_manufacturers_export_format($specificShop = false) {
            $final_manufacturers = array();
            $store_id = isset($this->session->data['gmt_selected_store_id']) ? (int)$this->session->data['gmt_selected_store_id'] : 0;

            if (!$this->is_ocstore || ($this->is_ocstore && $this->is_oc_3x)) {
                $sql = 'SELECT m.name, m.manufacturer_id FROM ' . $this->escape_database_table_name('manufacturer') . ' m';
                if ($specificShop) {
                    $sql .= ' INNER JOIN ' . $this->escape_database_table_name('manufacturer_to_store') . ' mts ON m.manufacturer_id = mts.manufacturer_id';
                    $sql .= ' WHERE mts.store_id = ' . (int)$store_id;
                }
                $manufacturers = $this->db->query($sql);
                foreach ($manufacturers->rows as $ma) {
                    $final_manufacturers[$ma['manufacturer_id']][$this->default_language_id] = $ma['name'];
                }
            } else {
                $sql = 'SELECT m.* FROM ' . $this->escape_database_table_name('manufacturer') . ' m';
                if ($specificShop) {
                    $sql .= ' INNER JOIN ' . $this->escape_database_table_name('manufacturer_to_store') . ' mts ON m.manufacturer_id = mts.manufacturer_id';
                    $sql .= ' WHERE mts.store_id = ' . (int)$store_id;
                }
                $ma_query = $this->db->query($sql);

                foreach ($ma_query->rows as $ma) {
                    $ma_id = $ma['manufacturer_id'];
                    $sql_desc = 'SELECT * FROM ' . $this->escape_database_table_name('manufacturer_description') . ' WHERE ' . $this->escape_database_field('manufacturer_id') . ' = ' . $this->escape_database_value($ma_id);
                    $mad_query = $this->db->query($sql_desc);

                    if ($mad_query->num_rows) {
                        foreach ($mad_query->rows as $ma_de) {
                            $lang_id = $ma_de['language_id'];
                            $name = $ma_de['name'];
                            if (!array_key_exists($ma_id, $final_manufacturers))
                                $final_manufacturers[$ma_id] = array();
                            $final_manufacturers[$ma_id][$lang_id] = $name;
                        }
                    } else {
                        $final_manufacturers[$ma_id][$this->default_language_id] = '';
                    }
                }
            }

            return $final_manufacturers;
        }

        public function get_all_manufacturers_import_format($select_format = false) {
            $export_format = $this->get_all_manufacturers_export_format(true);
            $final_manufacturers = array();

            foreach ($export_format as $manufacturer_id => $names) {
                foreach ($names as $lang_id => $name) {
                    $continue = !$select_format || ($select_format && $lang_id == $this->default_language_id);
                    if($continue) {
                        $name = $this->sanitize_value($name);
                        $final_manufacturers[$name .(!$select_format ?  '_' . $lang_id : '')] = $manufacturer_id;
                    }
                }
            }

            if($select_format) {
                $final_manufacturers_select = array();
                foreach ($final_manufacturers as $cat_name => $cat_id) {
                    $final_manufacturers_select[$cat_id] = $cat_name;
                }
                $final_manufacturers = $final_manufacturers_select;
            }

            return $final_manufacturers;
        }

        public function sanitize_value($value) {
            if(!is_array($value))
                return trim(htmlspecialchars_decode($value));
            else
                return '';
        }
        function get_image_link($image_name) {
            $img_link = $this->api_url.'opencart_admin/ext_ie_pro/img/'.$image_name;
            return $img_link;
        }
        public function get_legible_database_field_name($string) {
            $string = str_replace('_', ' ', $string);
            $string = ucfirst($string);
            return $string;
        }
        public function select_constructor($select_name, $values, $value, $extra = array()) {
            $onchange = array_key_exists('onchange', $extra) ? ' onchange="'.$extra['onchange'].'" ' : '';
            $class = array_key_exists('class', $extra) ? ' class="'.$extra['class'].'" ' : '';

            $select = '<select name="'.$select_name.'"'.$class.$onchange.'data-live-search="true">';
                foreach ($values as $option_value => $option_name) {
                    $select .= '<option '.($value == $option_value ? 'selected="selected"' : '').' value="'.$option_value.'">'.$option_name.'</option>';
                }
            $select .= '</select>';

            return $select;
        }
        public function validate_permiss() {
            if (!$this->user->hasPermission('modify', $this->real_extension_type.'/'.$this->extension_name)) {
                if(!empty($this->request->post['no_exit']))
                {
                    $array_return = array(
                        'error' => true,
                        'message' => $this->language->get('error_permission')
                    );
                    echo json_encode($array_return); die;
                }
                else
                    throw new Exception($this->language->get('error_permission'));

                return false;
            }
            return true;
        }
        public function exception($message) {
            throw new Exception($message);
        }
    }
?>