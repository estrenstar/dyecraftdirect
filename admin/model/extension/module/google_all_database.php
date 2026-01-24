<?php
    class ModelExtensionModuleGoogleAllDatabase extends ModelExtensionModuleGoogleAll {
        public function get_database($categories = array(), $fiel_conditions = array()) {
            $database_map = $this->get_database_fields();
            if(!empty($categories)) {
                if(!is_array($categories)) {
                    $copy_category = $categories;
                    $categories = array();
                    $categories[] = $copy_category;
                }
                $temp = array();
                foreach ($database_map as $category_temp => $tables) {
                    if(!in_array($category_temp, $categories))
                        unset($database_map[$category_temp]);
                }
            }
            $final_database_map = array();

            foreach ($database_map as $main_category => $tables) {
                foreach ($tables as $table_name => $fields) {
                    $table_name_with_prefix = $this->db_prefix.$table_name;
                    $query = 'SELECT * FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE TABLE_SCHEMA = \''.DB_DATABASE.'\' AND TABLE_NAME LIKE \''.$table_name_with_prefix.'\'';
                    $result = $this->db->query($query);

                    if(!empty($result->rows)) {
                        foreach ($result->rows as $key => $fiel_db_info) {
                            $field_name = $fiel_db_info['COLUMN_NAME'];
                            if(array_key_exists($field_name, $database_map[$main_category][$table_name])) {
                                $type = $this->translate_type($fiel_db_info['DATA_TYPE']);

                                $field_copy = $database_map[$main_category][$table_name][$field_name];

                                $add_field = empty($fiel_conditions) || (!empty($fiel_conditions) && is_array($field_copy));

                                if(!empty($fiel_conditions) && $add_field) {
                                    foreach ($fiel_conditions as $condition_key => $val) {
                                        if(!array_key_exists($condition_key, $field_copy) || $val != $field_copy[$condition_key]) {
                                            $add_field = false;
                                            break;
                                        }
                                    }
                                }

                                if($add_field) {
                                    if (is_array($field_copy)) {
                                        $final_database_map[$main_category][$table_name][$field_name] = $field_copy;
                                        $final_database_map[$main_category][$table_name][$field_name]['type'] = $type;
                                    } else
                                        $final_database_map[$main_category][$table_name][$field_name] = array('type' => $type);
                                }
                            }
                        }
                    }
                }
            }

            return $final_database_map;
        }
        public function translate_type($type) {
            if(in_array($type, array('int', 'float', 'decimal'))) {
                return 'number';
            } elseif(in_array($type, array('varchar', 'text'))) {
                return 'string';
            } elseif(in_array($type, array('tinyint'))) {
                return 'boolean';
            } elseif(in_array($type, array('date', 'datetime'))) {
                return 'date';
            }
        }
        public function get_database_fields() {
            $database = array(
                'products' => array(
                    'product' => array(
                        'product_id' => array('is_filter' => true),
                        'model' => array('is_filter' => true),
                        'sku' => array('is_filter' => true),
                        'upc' => array('is_filter' => true),
                        'ean' => array('is_filter' => true),
                        'jan' => array('is_filter' => true),
                        'isbn' => array('is_filter' => true),
                        'mpn' => array('is_filter' => true),
                        'location' => array('is_filter' => true),
                        'quantity' => array('is_filter' => true),
                        'stock_status_id' => array('is_filter' => true),
                        'image' => array('is_filter' => true),
                        'manufacturer_id' => array('is_filter' => true),
                        'shipping' => array('is_filter' => true),
                        'price' => array('is_filter' => true),
                        'points' => array('is_filter' => true),
                        'tax_class_id' => array('is_filter' => true),
                        'date_available' => array('is_filter' => true),
                        'weight' => array('is_filter' => true),
                        'weight_class_id' => array('is_filter' => true),
                        'length' => array('is_filter' => true),
                        'width' => array('is_filter' => true),
                        'height' => array('is_filter' => true),
                        'length_class_id' => array('is_filter' => true),
                        'subtract' => array('is_filter' => true),
                        'minimum' => array('is_filter' => true),
                        'sort_order' => array('is_filter' => true),
                        'status' => array('is_filter' => true),
                        'viewed' => array('is_filter' => true),
                        'date_added' => array('is_filter' => true),
                        'date_modified' => array('is_filter' => true),
                    ),
                    'product_attribute' => array(
                        'product_id' => array('is_filter' => true),
                        'attribute_id' => array('is_filter' => true, 'is_conditional_field' => true),
                        'language_id' => array('is_filter' => true, 'is_conditional_field' => true),
                        'text' => array('is_filter' => true),
                    ),
                    'product_description' => array(
                        'product_id' => array('is_filter' => true),
                        'language_id' => array('is_filter' => true, 'is_conditional_field' => true),
                        'name' => array('is_filter' => true),
                        'description' => array('is_filter' => true),
                        'tag' => array('is_filter' => true),
                        'meta_title' => array('is_filter' => true),
                        'meta_h1' => array('is_filter' => true),
                        'meta_description' => array('is_filter' => true),
                        'meta_keyword' => array('is_filter' => true),
                    ),
                    'product_discount' => array(
                        'product_discount_id' => array('is_filter' => true),
                        'product_id' => array('is_filter' => true, 'is_conditional_field' => true),
                        'customer_group_id' => array('is_filter' => true, 'is_conditional_field' => true),
                        'quantity' => array('is_filter' => true),
                        'priority' => array('is_filter' => true, 'is_conditional_field' => true),
                        'price' => array('is_filter' => true),
                        'date_start' => array('is_filter' => true, 'is_conditional_field' => true),
                        'date_end' => array('is_filter' => true, 'is_conditional_field' => true),
                    ),
                    'product_filter' => array(
                        'product_id' => array('is_filter' => true),
                        'filter_id' => array('is_filter' => true, 'is_conditional_field' => true),
                    ),
                    'product_image' => array(
                        'product_image_id' => array('is_filter' => true),
                        'product_id' => array('is_filter' => true),
                        'image' => array('is_filter' => true, 'is_conditional_field' => true),
                        'sort_order' => array('is_filter' => true),
                    ),
                    'product_option' => array(
                        'product_option_id' => array('is_filter' => true),
                        'product_id' => array('is_filter' => true),
                        'option_id' => array('is_filter' => true, 'is_conditional_field' => true),
                        'value' => array('is_filter' => true),
                        'option_value' => array('is_filter' => true),
                        'required' => array('is_filter' => true),
                    ),
                    'product_option_value' => array(
                        'product_option_value_id' => array('is_filter' => true),
                        'product_option_id' => array('is_filter' => true),
                        'product_id' => array('is_filter' => true, 'is_conditional_field' => true),
                        'option_id' => array('is_filter' => true, 'is_conditional_field' => true),
                        'option_value_id' => array('is_filter' => true, 'is_conditional_field' => true),
                        'quantity' => array('is_filter' => true),
                        'subtract' => array('is_filter' => true),
                        'price' => array('is_filter' => true),
                        'price_prefix' => array('is_filter' => true),
                        'points' => array('is_filter' => true),
                        'points_prefix' => array('is_filter' => true),
                        'weight' => array('is_filter' => true),
                        'weight_prefix' => array('is_filter' => true),
                    ),
                    'product_profile' => array(
                        'product_id' => array('is_filter' => true),
                        'profile_id' => array('is_filter' => true),
                        'customer_group_id' => array('is_filter' => true),
                    ),
                    'product_recurring' => array(
                        'product_id' => array('is_filter' => true),
                        'recurring_id' => array('is_filter' => true),
                        'store_id' => array('is_filter' => true),
                        'customer_group_id' => array('is_filter' => true),
                    ),
                    'product_related' => array(
                        'product_id' => array('is_filter' => true),
                        'related_id' => array('is_filter' => true, 'is_conditional_field' => true),
                    ),
                    'product_reward' => array(
                        'product_reward_id' => array('is_filter' => true),
                        'product_id' => array('is_filter' => true),
                        'customer_group_id' => array('is_filter' => true, 'is_conditional_field' => true),
                        'points' => array('is_filter' => true),
                    ),
                    'product_special' => array(
                        'product_special_id' => array('is_filter' => true),
                        'product_id' => array('is_filter' => true),
                        'customer_group_id' => array('is_filter' => true, 'is_conditional_field' => true),
                        'priority' => array('is_filter' => true, 'is_conditional_field' => true),
                        'price' => array('is_filter' => true),
                        'date_start' => array('is_filter' => true),
                        'date_end' => array('is_filter' => true),
                    ),
                    'product_tag' => array(
                        'product_id' => array('is_filter' => true),
                        'language_id' => array('is_filter' => true),
                        'tag' => array('is_filter' => true),
                    ),
                    'product_to_category' => array(
                        'product_id' => array('is_filter' => true),
                        'category_id' => array('is_filter' => true, 'is_conditional_field' => true),
                        'main_category' => array('is_filter' => true),
                    ),
                    'product_to_download' => array(
                        'product_id' => array('is_filter' => true),
                        'download_id' => array('is_filter' => true, 'is_conditional_field' => true),
                    ),
                    'product_to_layout' => array(
                        'product_id' => array('is_filter' => true),
                        'store_id' => array('is_filter' => true, 'is_conditional_field' => true),
                        'layout_id' => array('is_filter' => true),
                    ),
                    'product_to_store' => array(
                        'product_id' => array('is_filter' => true),
                        'store_id' => array('is_filter' => true, 'is_conditional_field' => true),
                    ),
                ),
            );

            //Add empty array to all fields
            $temp_database = array();
            foreach ($database as $main_category => $tables) {
                foreach ($tables as $table_name => $fields) {
                    foreach ($fields as $key => $field_name) {
                        if(!is_array($field_name))
                            $temp_database[$main_category][$table_name][$field_name] = array();
                        else
                            $temp_database[$main_category][$table_name][$key] = $field_name;
                    }

                }

            }
            $database = $temp_database;

            return $database;
        }
    }
?>