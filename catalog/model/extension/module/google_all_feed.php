<?php
class ModelExtensionModuleGoogleAllFeed extends Model {
    function load_feed($feed_id) {
        $array_return = array('error' => false, 'message' => '');

        $sql = "SELECT * FROM `".DB_PREFIX."gmt_feeds` WHERE id = ".(int)$feed_id;
        $result = $this->db->query($sql);

        if(empty($result->row)) {
            die("Feed with ID ".$feed_id." not found");
        }

        $profile = $result->row;
        $profile['profile'] = json_decode($this->sanitize_value(str_replace('&quot;', '\"', $profile['profile'])), true);
        $profile['category'] = 'products';
        $profile['main_table'] = 'product';
        $profile['main_field'] = 'product_id';
        $profile['file_format'] = $profile['type'] == 'google_adwords' || $profile['type'] == 'bing' || $profile['type'] == 'wish' || $profile['type'] == 'geizhals' ? 'csv' : 'xml';

        if(!$this->config->get('config_ssl'))
            $this->config->set('config_ssl', HTTPS_SERVER);

        $language_id = array_key_exists('google_all_feed_config_language_id', $profile['profile']) && $profile['profile']['google_all_feed_config_language_id'] !== '' ? $profile['profile']['google_all_feed_config_language_id'] : $this->config->get('config_language_id');
        $this->config->set('config_language_id', $language_id);
        $this->config->set('config_language_code', $this->db->query('SELECT * FROM '.DB_PREFIX.'language WHERE language_id ='.$language_id)->row['code']);
        $this->config->set('config_currency_code', $profile['profile']['google_all_feed_config_currency_code']);

        $store_id = array_key_exists('google_all_feed_config_store_id', $profile['profile']) && $profile['profile']['google_all_feed_config_store_id'] !== '' ? $profile['profile']['google_all_feed_config_store_id'] : $this->config->get('config_store_id');
        $this->config->set('config_store_id', $store_id);

        if($store_id > 0)
            $this->store_url = $this->db->query("SELECT * FROM ".DB_PREFIX."store WHERE store_id = ".$store_id)->row['ssl'];
        else
            $this->store_url = HTTPS_SERVER;


        foreach ($profile['profile'] as $key => $config) {
            if(is_array($config) && count($config) == 1 && array_key_exists(0, $config) && empty($config[0]))
                $profile['profile'][$key] = '';
        }
        return $profile;
    }

    public function escape_database_field($name) {
        return "`".$name."`";
    }

    public function escape_database_value($value) {
        return "'".$this->db->escape(str_replace('"', '&quot;', $value))."'";
    }

    public function escape_database_table_name($name) {
        return "`".DB_PREFIX.$name."`";
    }

    public function sanitize_value($value) {
        if(!is_array($value))
            return trim(htmlspecialchars_decode($value));
        else
            return '';
    }

    public function exception($message) {
        throw new Exception($message);
    }

    /**
     * Get elements
     * 
     * @param array $profile
     * @param array $gtin_priority
     */
    public function get_elements($profile, $gtin_priority) {
        $elements_id = $this->get_element_ids($profile);

        if(empty($elements_id)) {
            $this->exception('No data found. Check filters and conditions.');
        }

        $elements = $this->get_products_data($profile, $elements_id, $gtin_priority);

        return $elements;
    }

    public function get_element_ids($profile) {
        $category = $profile['category'];
        $main_table = $profile['main_table'];
        $main_field = $profile['main_field'];
        $exclude_prod_ids = !empty($profile['profile']['google_all_feed_config_quick_filter_excluded_ids']) ? $profile['profile']['google_all_feed_config_quick_filter_excluded_ids'] : false;
        $main_table_formatted = $this->escape_database_field(DB_PREFIX.$main_table);
        $main_field_formatted = 'main_table.'.$main_field;

        $sql = 'SELECT '.$main_field_formatted.' FROM '.$main_table_formatted.' main_table ';

        $filters = array_key_exists('export_filter', $profile['profile']) && array_key_exists('filters', $profile['profile']['export_filter']) && !empty($profile['profile']['export_filter']['filters']) ? $profile['profile']['export_filter']['filters'] : array();
        $filters_config = array_key_exists('export_filter', $profile['profile']) && array_key_exists('config', $profile['profile']['export_filter']) && !empty($profile['profile']['export_filter']['config']) ? $profile['profile']['export_filter']['config'] : array();
 
        $joins = '';
        $where = '';

        if(!empty($filters)) {
            $main_conditional = (array_key_exists('main_conditional', $filters_config) ? $filters_config['main_conditional'] : 'OR').' ';
            $filters_by_table = $this->format_filters_by_table($filters);

            //Get "no" filters from not main tables for make a subquery
            $non_conditions = array();
            foreach ($filters_by_table as $table_name => $field) {
                if ($table_name != $main_table) {
                    foreach ($field as $field_name => $conditions) {
                        foreach ($conditions as $key => $cond) {
                            if(!empty($cond['condition']) && in_array($cond['condition'], array("not_like", "!="))) {
                                if(!array_key_exists($table_name, $non_conditions))
                                    $non_conditions[$table_name] = array();
                                if(!array_key_exists($field_name, $non_conditions[$table_name]))
                                    $non_conditions[$table_name][$field_name] = array();

                                $non_conditions[$table_name][$field_name][] = $cond;
                                unset($filters_by_table[$table_name][$field_name][$key]);
                            }
                        }
                    }
                }
            }
            //Unset possible empty array after process to extract non conditionals
            foreach ($filters_by_table as $table_name => $field) {
                foreach ($field as $field_name => $conditions) {
                    if(empty($conditions))
                        unset($filters_by_table[$table_name][$field_name]);

                    if(empty($filters_by_table[$table_name]))
                        unset($filters_by_table[$table_name]);
                }
            }

            foreach ($filters_by_table as $table_name => $field) {
                if($table_name == $main_table) {
                    if(empty($where))
                        $where .= ' WHERE ';

                    foreach ($field as $field_name => $values) {
                        foreach ($values as $key2 => $val) {
                            $condition = $this->translate_condition($val['condition']);
                            $value = $this->translate_condition_value($val['condition'], $val['value']);
                            $like = in_array($val['condition'], array('like','not_like'));
                            $where .= 'main_table.'.$field_name." ".$condition." '".($like ? '%':'').$value.($like ? '%':'')."' ".$main_conditional;
                        }
                    }
                    $where = rtrim($where, $main_conditional);
                } else {
                    $table_formatted = $this->escape_database_field(DB_PREFIX.$table_name);
                    $table_join = 'ij_'.$table_name;

                    $joins .= 'INNER JOIN '.$table_formatted.' '.$table_join.' ON (main_table.'.$main_field.' = '.$table_join.'.'.$main_field.' AND (';
                        foreach ($field as $field_name => $values) {
                            foreach ($values as $key2 => $val) {
                                $condition = $this->translate_condition($val['condition']);
                                $value = $this->translate_condition_value($val['condition'], $val['value']);
                                $like = in_array($val['condition'], array('like','not_like'));
                                $joins .= $table_join.'.'.$field_name." ".$condition." '".($like ? '%':'').$value.($like ? '%':'')."' ".$main_conditional;
                            }
                        }
                    $joins = rtrim($joins, $main_conditional);
                    $joins .= ')) '."\n";
                }
            }

            if(!empty($non_conditions)) {
                if(empty($where))
                    $where .= ' WHERE ';

                foreach ($non_conditions as $table_name => $fields) {
                    $where .= $main_field_formatted. ' NOT IN (';
                        $table_formatted = $this->escape_database_field(DB_PREFIX.$table_name);

                        $where .= ' SELECT '.$main_field." FROM ".$table_formatted." WHERE ";
                        foreach ($fields as $field_name => $values) {
                            foreach ($values as $key2 => $val) {
                                //Custom translate condition
                                if($val['condition'] == '!=')
                                    $condition = '=';
                                else
                                $condition = $this->translate_condition($val['condition']);

                                $value = $this->translate_condition_value($val['condition'], $val['value']);
                                $like = in_array($val['condition'], array('not_like'));
                                $condition = $like ? "like" : $condition;

                                $where .= $table_formatted.'.'.$field_name." ".$condition." '".($like ? '%':'').$value.($like ? '%':'')."' OR ";
                            }
                            $where = rtrim($where, ' OR ').' GROUP BY '.$main_field;
                        }
                    $where .= ') '.$main_conditional;
                }

                $where = rtrim($where, $main_conditional);
            }
        }

        if($category == 'products') {
            $category_id_filters = array_key_exists('google_all_feed_config_quick_filter_category_ids', $profile['profile']) && !empty($profile['profile']['google_all_feed_config_quick_filter_category_ids']);
            if(!empty($category_id_filters)) {
                $joins .= ' INNER JOIN ' . $this->escape_database_table_name('product_to_category') . ' ptc ON (main_table.product_id = ptc.product_id AND ptc.category_id IN(' . implode(',', $profile['profile']['google_all_feed_config_quick_filter_category_ids']) . ')) ';
            }

            $manufacturer_id_filters = array_key_exists('google_all_feed_config_quick_filter_manufacturer_ids', $profile['profile']) && !empty($profile['profile']['google_all_feed_config_quick_filter_manufacturer_ids']);
            if(!empty($manufacturer_id_filters)) {
                 if(empty($where))
                     $where .= ' WHERE ';
                 else
                     $where .= 'AND';

                 $where .= ' main_table.manufacturer_id IN ('.implode(',', $profile['profile']['google_all_feed_config_quick_filter_manufacturer_ids']).')';
            }

            if(!empty($exclude_prod_ids)) {
                if(empty($where))
                     $where .= ' WHERE ';
                else
                     $where .= 'AND';

                $where .= ' main_table.product_id NOT IN ('.implode(',', $exclude_prod_ids).')';
            }
        }

        $sql = $sql.$joins.$where.' GROUP BY main_table.'.$main_field;
        $from = array_key_exists('from', $this->request->post) ? (int)$this->request->post['from'] : 0;
        $to = array_key_exists('to', $this->request->post) ? (int)$this->request->post['to'] : 0;

        if($to < $from)
            $this->exception($this->language->get('progress_export_error_range'));

        if($from > 0 || $to > 0)
            $sql .= ' LIMIT ';
        if($from > 0)
            $sql .= ($from-1).($to == 0 ? ',100000000000000':'');
        if($to > 0)
            $sql .= ($from == 0 ? '1':'').','.($to-1);

        $final_sql = $sql;

        $result = $this->db->query($final_sql);

        $final_result = array();
        if(!empty($result->rows)) {
            foreach ($result->rows as $key => $val) {
                $final_result[] = $val[$main_field];
            }
        }
        return $final_result;
    }

    /**
     * Get Products Data
     * 
     * @param array $profile
     * @param array $ids
     * @param array $gtin_priority
     */
    public function get_products_data($profile, $ids, $gtin_priority) {
        if(empty($ids))
            return array();
        
        $customer_group_special_id = array_key_exists('google_all_feed_config_customer_group_id_special', $profile['profile']) && $profile['profile']['google_all_feed_config_customer_group_id_special'] !== '' ? $profile['profile']['google_all_feed_config_customer_group_id_special'] : 1;
        $language_id = array_key_exists('google_all_feed_config_language_id', $profile['profile']) && $profile['profile']['google_all_feed_config_language_id'] !== '' ? $profile['profile']['google_all_feed_config_language_id'] : $this->config->get('config_language_id');
        $store_id = array_key_exists('google_all_feed_config_store_id', $profile['profile']) && $profile['profile']['google_all_feed_config_store_id'] !== '' ? $profile['profile']['google_all_feed_config_store_id'] : $this->config->get('config_store_id');
        $product_id_field = $profile['profile']['google_all_feed_config_product_identificator'];
        $only_in_stock = !array_key_exists('google_all_feed_config_show_products_out_of_stock', $profile['profile']);
        $feed_type = $profile['profile']['google_all_feed_config_file_format'];

        $sql = "SELECT DISTINCT
                p.product_id as product_id,
                p.".$product_id_field." as product_identificator,
                p.quantity as quantity,
                p.price as price,
                p.minimum as minimum,
                pd.name as name,
                pd.description as description,
                pd.meta_description as meta_description,
                pd.meta_keyword as meta_keyword,
                ma.name as manufacturer,
                p.image as image,
                p.date_available as date_available,
                p.model as model,
                p.stock_status_id as stock_status_id,";

            if(version_compare(VERSION, '1.5.3.1', '>'))
              $sql .= "p.ean as ean,
              p.mpn as mpn,";

            $sql .= "p.tax_class_id as tax_class_id,
              p.upc as upc,
              p.weight as weight,
              p.weight_class_id as weight_class_id,
              p.sku as sku,
              p.jan as jan,
              p.isbn as isbn,
              p.length as length,
              p.width as width,
              p.height as height
            
              FROM " . DB_PREFIX . "product p 
              INNER JOIN " . DB_PREFIX . "product_to_store pte ON (pte.product_id = p.product_id AND store_id = ".(int)$store_id.")
              LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id AND language_id = ".(int)$language_id.")
              LEFT JOIN " . DB_PREFIX . "manufacturer ma ON (p.manufacturer_id = ma.manufacturer_id)";

            if($feed_type == 'google_reviews')
                $sql .= " INNER JOIN " . DB_PREFIX . "review revi ON (revi.product_id = p.product_id AND revi.status = 1)";

            $sql .= " WHERE p.status = 1";

            $sql .= ' AND p.product_id IN('.implode(",", $ids).')';

            if($only_in_stock)
                $sql .= ' AND p.quantity > 0 ';

            $sql .= " GROUP BY p.product_id";

            $query = $this->db->query($sql);
            $products = $query->rows;


            //Add product specials - I had to move here becuase "LEFT JOIN" not working with "ORDER BY".
            foreach ($products as $key => $prod) {
                $special = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_special` pss WHERE pss.customer_group_id = ".$customer_group_special_id." AND pss.product_id = ".$prod['product_id']." AND ((pss.date_start = '0000-00-00' OR pss.date_start < NOW()) AND (pss.date_end = '0000-00-00' OR pss.date_end > NOW())) ORDER BY pss.priority ASC, pss.price ASC LIMIT 1");
                $products[$key]['special'] = !empty($special->row['price']) ? $special->row['price'] : '';
                $products[$key]['special_start'] = !empty($special->row['date_start']) ? $special->row['date_start'] : '';
                $products[$key]['special_end'] = !empty($special->row['date_end']) ? $special->row['date_end'] : '';
            }

            $this->format_product_thumbs($profile, $products);
            $this->format_product_general_data($profile, $products, $gtin_priority);
            
            if($feed_type == 'google_reviews') {
                return $products;
            }

            $split_options = array_key_exists('google_all_feed_config_split_options', $profile['profile']) && $profile['profile']['google_all_feed_config_split_options'];

            $this->all_option_values = $this->get_all_option_values($profile);

            if($split_options) {
                $key_sizes = 'google_all_feed_config_split_options_size';
                $key_colors = 'google_all_feed_config_split_options_color';
                $product_option_values = array(
                    'sizes' => array_key_exists($key_sizes, $profile['profile']) && !empty($profile['profile'][$key_sizes]) ? $this->get_product_options($profile, $ids, $profile['profile'][$key_sizes]) : array(),
                    'colors' => array_key_exists($key_colors, $profile['profile']) && !empty($profile['profile'][$key_colors]) ? $this->get_product_options($profile, $ids, $profile['profile'][$key_colors]) : array(),
                );

                $this->split_products_by_options($profile, $products, $product_option_values);
            }

            $split_discounts = array_key_exists('google_all_feed_config_split_discounts', $profile['profile']) && $profile['profile']['google_all_feed_config_split_discounts'];
            if($split_discounts)
                $this->split_products_by_discounts($profile, $products);

            $this->format_product_prices($profile, $products);

            return $products;
    }

    public function get_reviews_data($profile, $ids) {
        if(empty($ids))
            return array();
        $store_id = array_key_exists('google_all_feed_config_store_id', $profile['profile']) && $profile['profile']['google_all_feed_config_store_id'] !== '' ? $profile['profile']['google_all_feed_config_store_id'] : $this->config->get('config_store_id');


    }

    public function get_product_options($profile, $ids, $options_allowed, $name_format = false) {
        if($name_format)
            $all_option_values = $this->get_all_option_values($profile);
        $base_sql = "SELECT * FROM ".$this->escape_database_table_name('product_option_value')." WHERE product_id IN(".implode(",", $ids).")";
        $option_value_products = array();
        $temp_sql = $base_sql." AND option_id IN(".implode(',', $options_allowed).")";

        $show_out_of_stock = array_key_exists('google_all_feed_config_show_products_out_of_stock', $profile['profile']);

       if(!$show_out_of_stock)
            $temp_sql = $temp_sql." AND quantity > 0";

        $results = $this->db->query($temp_sql);
        foreach ($results->rows as $opt_val) {
            if(!array_key_exists($opt_val['product_id'], $option_value_products))
                $option_value_products[$opt_val['product_id']] = array();

            if(empty($opt_val['price_prefix']))
                $opt_val['price_prefix'] = '+';
            if(empty($opt_val['points_prefix']))
                $opt_val['points_prefix'] = '+';
            if(empty($opt_val['weight_prefix']))
                $opt_val['weight_prefix'] = '+';

            $option_value_products[$opt_val['product_id']][] = $name_format ? (array_key_exists($opt_val['option_value_id'], $all_option_values) ? $all_option_values[$opt_val['option_value_id']]['name'] : '') : $opt_val;
        }
        return $option_value_products;
    }

    public function get_product_attributes($profile, $ids, $attributes_allowed, $name_format = false) {
        $language_id = array_key_exists('google_all_feed_config_language_id', $profile['profile']) && $profile['profile']['google_all_feed_config_language_id'] !== '' ? $profile['profile']['google_all_feed_config_language_id'] : $this->config->get('config_language_id');

        $base_sql = "SELECT prodattri.*, attr.*, attrdesp.*  FROM ".$this->escape_database_table_name('product_attribute')." prodattri
        INNER JOIN ".$this->escape_database_table_name('attribute')." attr ON (attr.attribute_id = attr.attribute_id)
        LEFT JOIN ".$this->escape_database_table_name('attribute_description')." attrdesp ON (attrdesp.attribute_id = prodattri.attribute_id AND attrdesp.language_id = ".$language_id.")
        WHERE prodattri.language_id = ".$language_id." AND prodattri.product_id IN(".implode(",", $ids).") AND attr.attribute_id IN(".implode(",", $attributes_allowed).") GROUP BY prodattri.product_id, prodattri.attribute_id";
        $results = $this->db->query($base_sql);
        $product_attributes = array();
        foreach ($results->rows as $attribute) {
            if(!array_key_exists($attribute['product_id'], $product_attributes))
                $product_attributes[$attribute['product_id']] = array();

            if(in_array($attribute['attribute_id'], $attributes_allowed))
                $product_attributes[$attribute['product_id']][] = $attribute['text'];
        }

        return $product_attributes;
    }

    //Mover esta función a attriubtes, está funcionando bien.
    public function get_product_filters($profile, $ids, $filters_allowed, $name_format = false) {
        $language_id = array_key_exists('google_all_feed_config_language_id', $profile['profile']) && $profile['profile']['google_all_feed_config_language_id'] !== '' ? $profile['profile']['google_all_feed_config_language_id'] : $this->config->get('config_language_id');

        $base_sql = "SELECT prodfilter.*, filtdesc.*  FROM ".$this->escape_database_table_name('product_filter')." prodfilter
        LEFT JOIN ".$this->escape_database_table_name('filter_description')." filtdesc ON (filtdesc.filter_id = prodfilter.filter_id AND filtdesc.language_id = ".$language_id.")
        INNER JOIN ".$this->escape_database_table_name('filter')." filt ON (filt.filter_group_id IN(".implode(",", $filters_allowed)."))
        WHERE prodfilter.product_id IN(".implode(",", $ids).") GROUP BY prodfilter.product_id, prodfilter.filter_id";

        $results = $this->db->query($base_sql);

        $product_filters = array();
        foreach ($results->rows as $filter) {
            if(!array_key_exists($filter['product_id'], $product_filters))
                $product_filters[$filter['product_id']] = array();

            if(in_array($filter['filter_group_id'], $filters_allowed))
                $product_filters[$filter['product_id']][] = $filter['name'];
        }

        return $product_filters;
    }

    public function get_product_getvaluefroms($profile, $ids, $value_source, $name_format = false) {
        $products       = array();
        $valid_sources  = ['main_product_category', 'last_product_category', 'product_price_raw_format', 'stock_value_identification'];
        
        if (in_array($value_source, $valid_sources)) {
            $results = $this->db->query("SELECT * FROM ".$this->escape_database_table_name('product')." WHERE product_id IN (".implode(',', $ids).")");
            
            foreach ($results->rows as $product) {
                if ($value_source == 'main_product_category') {
                    $all_cat_trees      = $this->get_all_categories_branches_select($profile);
                    $product_categories = $this->get_product_categories($product['product_id']);
                    $main_category      = '';

                    if (!empty($product_categories)) {
                        foreach ($product_categories as $cat_id) {
                            if (array_key_exists($cat_id, $all_cat_trees) && !empty($all_cat_trees[$cat_id])) {
                                $main_category = explode(' &gt; ', $all_cat_trees[$cat_id])[0];
                                break;
                            }
                        }
                    }

                    $products[$product['product_id']] = [$main_category];
                    
                } elseif ($value_source == 'last_product_category') {
                    $all_cat_trees      = $this->get_all_categories_branches_select($profile);
                    $product_categories = $this->get_product_categories($product['product_id']);
                    $last_category      = '';

                    if (!empty($product_categories)) {
                        foreach ($product_categories as $cat_id) {
                            if (array_key_exists($cat_id, $all_cat_trees) && !empty($all_cat_trees[$cat_id])) {
                                $last_category = explode(' &gt; ', $all_cat_trees[$cat_id]);
                                $last_category = end($last_category);
                                break;
                            }
                        }
                    }

                    $products[$product['product_id']] = [$last_category];
                    
                } elseif ($value_source == 'product_price_raw_format') {
                    $products[$product['product_id']] = [$product['price']];
                } elseif ($value_source == 'stock_value_identification') {
                    $products[$product['product_id']] = (int)$product['quantity'] > 0 ? ['IN_STOCK'] : ['NO_STOCK'];
                }
            }
        }

        return $products;
    }

    public function split_products_by_options($profile, &$products, $product_option_values) {
        $final_products = array();

        $mask = $profile['profile']['google_all_feed_config_split_options_mask'];

        if (strpos($mask, '{product_name}') === false && strpos($mask, '{size}') === false && strpos($mask, '{color}') === false)
            $mask = '{product_name} - {size}, {color}';

        foreach ($products as $key => $prod) {
            $final_products[] = $prod;
            $product_id = $prod['product_id'];
            if(array_key_exists($product_id, $product_option_values['sizes'])) {

                //Remove the purchase direct link
                $index = count($final_products)-1;
                if(!empty($final_products[$index]['product_link_purchase']))
                    unset($final_products[$index]['product_link_purchase']);

                $sizes = $product_option_values['sizes'][$product_id];
                $colors = array_key_exists($product_id, $product_option_values['colors']) ? $product_option_values['colors'][$product_id] : array();
                $prod['name'] = str_replace('{product_name}', $prod['name'], $mask);
                $prod['original_price'] = $prod['price'];
                foreach ($sizes as $key => $prod_size) {
                    $opt_val_id = $prod_size['option_value_id'];
                    if(array_key_exists($opt_val_id, $this->all_option_values)) {
                        $size_name = $this->all_option_values[$opt_val_id]['name'];
                        $copy_product = $prod;
                        $copy_product['quantity'] = $prod_size['quantity'];
                        $copy_product['weight'] = $prod_size['weight_prefix'] == '=' ? $prod_size['weight'] : eval('return ' . (float)$copy_product['weight'] . $prod_size['weight_prefix'] . (float)$prod_size['weight'] . ';');

                        if(in_array($prod_size['price_prefix'], array("%", "/")) && !(float)$prod_size['price'])
                            $copy_product['price'] = $prod_size['price'];
                        else
                        $copy_product['price'] = $prod_size['price_prefix'] == '=' ? $prod_size['price'] : eval('return ' . (float)$copy_product['price'] . $prod_size['price_prefix'] . (float)$prod_size['price'] . ';');

                        if(!empty($copy_product['special'])) {
                            if(in_array($prod_size['price_prefix'], array("%", "/")) && !(float)$prod_size['price'])
                                $copy_product['special'] = (float)$copy_product['special'];
                            else
                            $copy_product['special'] = $prod_size['price_prefix'] == '=' ? $prod_size['price'] : eval('return ' . (float)$copy_product['special'] . $prod_size['price_prefix'] . (float)$prod_size['price'] . ';');
                        }

                        $copy_product['name'] = str_replace('{size}', $size_name, $copy_product['name']);
                        $copy_product['splitted_by_size'] = $size_name;
                        $copy_product['product_identificator'] .= '-' . $size_name;
                        $copy_product['option_price'] = $prod_size['price'];
                        $copy_product['option_price_prefix'] = $prod_size['price_prefix'];
                        $copy_product['option_price_formula'] = $prod_size['price_prefix'] . $prod_size['price'];

                        if(!empty($copy_product['product_link_purchase']))
                            $copy_product['product_link_purchase'] .= '&options='.$prod_size['product_option_id'].':'.$prod_size['product_option_value_id'];

                        if (!empty($colors)) {

                            foreach ($colors as $prod_color) {
                                $copy_product_2 = $copy_product;
                                $opt_val_id = $prod_color['option_value_id'];
                                if(array_key_exists($opt_val_id, $this->all_option_values)) {
                                    $color_name = $this->all_option_values[$opt_val_id]['name'];
                                    $copy_product_2['splitted_by_color'] = $color_name;
                                    $base_price = $copy_product_2['price'];
                                    if ($prod_color['price_prefix'] == '=')
                                        $new_price = $prod_color['price'];
                                    else
                                        $new_price = eval('return ' . (float)$base_price . $prod_color['price_prefix'] . (float)$prod_color['price'] . ';');

                                    $copy_product_2['option_price_formula'] .= $prod_color['price_prefix'] . $prod_color['price'];
                                    $copy_product_2['price'] = $new_price;
                                    if(!empty($copy_product_2['special']))
                                        $copy_product_2['special'] = $prod_color['price_prefix'] == '=' ? $prod_color['price'] : eval('return ' . (float)$copy_product_2['special'] . $prod_color['price_prefix'] . (float)$prod_color['price'] . ';');

                                    $copy_product_2['name'] = str_replace('{color}', $color_name, $copy_product['name']);
                                    $copy_product_2['product_identificator'] .= '-' . $color_name;

                                    if(!empty($copy_product_2['product_link_purchase']))
                                        $copy_product_2['product_link_purchase'] .= '-'.$prod_color['product_option_id'].':'.$prod_color['product_option_value_id'];

                                    $final_products[] = $copy_product_2;
                                }
                            }
                        } else {
                            $copy_product['name'] = str_replace(' {color}', '', $copy_product['name']);
                            $copy_product['name'] = str_replace('{color} ', '', $copy_product['name']);
                            $copy_product['name'] = str_replace('{color}', '', $copy_product['name']);
                            $final_products[] = $copy_product;
                        }
                    }
                }
            }elseif(array_key_exists($product_id, $product_option_values['colors'])) {
                foreach ($product_option_values['colors'][$product_id] as $prod_color) {
                    $opt_val_id = $prod_color['option_value_id'];
                    $color_name = $this->all_option_values[$opt_val_id]['name'];
                    $copy_product = $prod;
                    $copy_product['quantity'] = $prod_color['quantity'];
                    $copy_product['weight'] = $prod_color['weight_prefix'] == '=' ? $prod_color['weight'] : eval('return '.(float)$copy_product['weight'].$prod_color['weight_prefix'].(float)$prod_color['weight'].';');
                    $copy_product['price'] = $prod_color['price_prefix'] == '=' ? $prod_color['price'] : eval('return '.(float)$copy_product['price'].$prod_color['price_prefix'].(float)$prod_color['price'].';');
                    $copy_product['name'] = str_replace('{size}', $color_name, $copy_product['name']);
                    $copy_product['product_identificator'] .= '-'.$color_name;
                    $copy_product['splitted_by_color'] = $color_name;
                    $copy_product['option_price'] = $prod_color['price'];
                    $copy_product['option_price_prefix'] = $prod_color['price_prefix'];

                    $copy_product['name'] = str_replace(' {size}', '', $copy_product['name']);
                    $copy_product['name'] = str_replace('{size} ', '', $copy_product['name']);
                    $copy_product['name'] = str_replace('{size}', '', $copy_product['name']);

                    if(!empty($copy_product['product_link_purchase']))
                        $copy_product['product_link_purchase'] .= '-'.$prod_color['product_option_id'].':'.$prod_color['product_option_value_id'];

                    $final_products[] = $copy_product;
                }
            }
        }

        $products = $final_products;
    }

    public function split_products_by_discounts($profile, &$products) {
        $customer_group_discount_id = array_key_exists('google_all_feed_config_customer_group_id_discount', $profile['profile']) && $profile['profile']['google_all_feed_config_customer_group_id_discount'] !== '' ? $profile['profile']['google_all_feed_config_customer_group_id_discount'] : 1;
        $name_mask = $profile['profile']['google_all_feed_config_split_discounts_mask'];

        if (strpos($name_mask, '{product_name}') === false || strpos($name_mask, '{discount_number}') === false)
            $name_mask = '{product_name} x{discount_number}';

        $final_product = array();
        foreach ($products as $key => $product) {
            $sql ="SELECT * FROM ".$this->escape_database_table_name('product_discount')." pds WHERE ".$this->escape_database_field('product_id')." = ".$this->escape_database_value($product['product_id'])." AND ".$this->escape_database_field('customer_group_id')." = ".$this->escape_database_value($customer_group_discount_id)." AND ((pds.date_start = '0000-00-00' OR pds.date_start < NOW()) AND (pds.date_end = '0000-00-00' OR pds.date_end > NOW())) ORDER BY ".$this->escape_database_field('priority');
            $discounts = $this->db->query($sql);
            if(!empty($discounts->rows)) {
                $copy_product = $product;
                $copy_product['discount_splitted'] = true;
                $final_product[] = $copy_product;
                foreach ($discounts->rows as $key_discount => $dics) {
                    $copy_product = $product;
                    if(!empty($dics['price']) && !empty($dics['quantity'])) {
                        $copy_product['price'] = $dics['price']*$dics['quantity'];

                        if(array_key_exists('option_price_formula', $product)) {
                            $copy_product['price'] += eval('return '.$product['option_price_formula'].';')*$dics['quantity'];
                        }
                        $copy_product['special'] = 0;
                        $copy_product['price_unit'] = $dics['price'];
                        $copy_product['discount_unit'] = $dics['quantity'];
                        $final_name = str_replace('{product_name}', $copy_product['name'], $name_mask);
                        $final_name = str_replace('{discount_number}', $dics['quantity'], $final_name);
                        $copy_product['name'] = $final_name;
                        $copy_product['product_identificator'] .= 'x'.$dics['quantity'];
                        $final_product[] = $copy_product;
                    }
                }
            }
            else
                $final_product[] = $product;
        }

        $products = $final_product;
    }

    public function get_all_option_values($profile) {
        $language_id = array_key_exists('google_all_feed_config_language_id', $profile['profile']) && $profile['profile']['google_all_feed_config_language_id'] !== '' ? $profile['profile']['google_all_feed_config_language_id'] : $this->config->get('config_language_id');

        $sql = 'SELECT optval.option_value_id, optval.option_id, optvald.name  FROM '.$this->escape_database_table_name('option_value').' optval LEFT JOIN '.$this->escape_database_table_name('option_value_description').' optvald ON(optval.`option_value_id` = optvald.`option_value_id` AND `language_id` = '.$this->escape_database_value($language_id).')';

        $results = $this->db->query($sql);

        $options_values = array();
        foreach ($results->rows as $key => $opt_val) {
            $options_values[$opt_val['option_value_id']] = array(
                'name' => $this->map_option_value_name($profile, $opt_val['name']),
                'option_id' => $opt_val['option_id'],
            );
        }
        return $options_values;
    }

    public function map_option_value_name($profile, $option_value_name) {

        if(!empty($profile['profile']['google_all_feed_config_map_option_values'])) {
            $option_names = explode(PHP_EOL, $profile['profile']['google_all_feed_config_map_option_values']);
            $final_option_names = array();
            foreach ($option_names as $opt_names) {
                $opt_name = explode(':', $opt_names);
                $final_option_names[trim($opt_name[0])] = trim($opt_name[1]);
            }
            if(array_key_exists($option_value_name, $final_option_names))
                return $final_option_names[$option_value_name];
        }

        return $option_value_name;
    }

    public function format_filters_by_table($filters) {
        $final_filters = array();

        foreach ($filters as $key => $fil) {
            $field_split = explode('-', $fil['field']);
            $table = $field_split[0];
            $field = $field_split[1];
            $type = $field_split[2];

            if(!array_key_exists($table, $final_filters))
                $final_filters[$table] = array();
            if(!array_key_exists($field, $final_filters[$table]))
                $final_filters[$table][$field] = array();

            $condition = $fil['conditional'][$type];

            $final_filters[$table][$field][] = array(
                'value' => $this->db->escape($fil['value']),
                'condition' => html_entity_decode($condition)
            );
        }

        return $final_filters;
    }

    public function translate_condition($condition) {
        if(is_numeric($condition))
            return '=';

        switch ($condition) {
            case 'not_like':
                return 'NOT LIKE';
                break;
            case 'years_ago': case 'months_ago': case 'days_ago': case 'hours_ago': case 'minutes_ago':
                return '>=';
                break;
            default:
                return $condition;
                break;
        }
    }

    public function translate_condition_value($condition, $value) {
        if(in_array($condition, array('years_ago', 'months_ago', 'days_ago', 'hours_ago', 'minutes_ago'))) {
            $php_name = '';
            if($condition == 'years_ago') $php_name = 'years';
            elseif($condition == 'months_ago') $php_name = 'months';
            elseif($condition == 'days_ago') $php_name = 'days';
            elseif($condition == 'hours_ago') $php_name = 'hours';
            elseif($condition == 'minutes_ago') $php_name = 'minutes';

            return date('Y-m-d H:i:s', strtotime('-'.(int)$value.' '.$php_name));
        }

        if(is_numeric($condition))
            return $condition;

        return $value;
    }

    public function format_date_ISO_8601(&$date, $format='merchant_center') {
        $fecha_datetime = new DateTime($date);
        $date = $fecha_datetime->format('Y-m-d\TH:iP');
    }

    public function format_product_prices($profile, &$products) {
        $currency = $profile['profile']['google_all_feed_config_currency_code'];
        $apply_tax = array_key_exists('google_all_feed_config_price_tax', $profile['profile']) && $profile['profile']['google_all_feed_config_price_tax'];

        $currencies = array(
            'AED','AFN','ALL','AMD','ANG','AOA','ARS','AUD','AWG','AZN','BAM','BBD','BDT','BGN','BHD','BIF','BMD','BND','BOB','BOV','BRL','BSD','BTN','BWP','BYR','BZD','CAD','CDF','CHE','CHF','CHW','CLF','CLP','CNY','COP','COU','CRC','CUP','CVE','CYP','CZK','DJF','DKK','DOP','DZD','EEK','EGP','ERN','ETB','EUR','FJD','FKP','GBP','GEL','GHS','GIP','GMD','GNF','GTQ','GYD','HKD','HNL','HRK','HTG','HUF','IDR','ILS','INR','IQD','IRR','ISK','JMD','JOD','JPY','KES','KGS','KHR','KMF','KPW','KRW','KWD','KYD','KZT','LAK','LBP','LKR','LRD','LSL','LTL','LVL','LYD','MAD','MDL','MGA','MKD','MMK','MNT','MOP','MRO','MTL','MUR','MVR','MWK','MXN','MXV','MYR','MZN','NAD','NGN','NIO','NOK','NPR','NZD','OMR','PAB','PEN','PGK','PHP','PKR','PLN','PYG','QAR','RON','RSD','RUB','RWF','SAR','SBD','SCR','SDG','SEK','SGD','SHP','SKK','SLL','SOS','SRD','STD','SYP','SZL','THB','TJS','TMM','TND','TOP','TRY','TTD','TWD','TZS','UAH','UGX','USD','USN','USS','UYU','UZS','VEB','VND','VUV','WST','XAF','XAG','XAU','XBA','XBB','XBC','XBD','XCD','XDR','XFO','XFU','XOF','XPD','XPF','XPT','XTS','XXX','YER','ZAR','ZMK','ZWD'
        );

        if (in_array(strtoupper($currency), $currencies)) {
            $currency_code = $currency;
            $currency_value = $this->currency->getValue($currency_code);
        } else {
            $currency_code = 'USD';
            $currency_value = $this->currency->getValue('USD');
        }

        $prices = array('price', 'special');

        foreach ($products as $key => $prod) {
            foreach ($prices as $price_type) {
                $price = (float)$prod[$price_type];
                $tax_class_id = $prod['tax_class_id'];

                if ($apply_tax) {
                    $tax_to_apply = array_key_exists('google_all_feed_config_tax_to_apply', $profile['profile']) && $profile['profile']['google_all_feed_config_tax_to_apply'] !== '' ? $profile['profile']['google_all_feed_config_tax_to_apply'] : '0';
                    
                    if($tax_to_apply == '0') {
                        $price_formatted = $this->currency->format($this->tax->calculate($price, $tax_class_id), $currency_code, $currency_value, false);
                    } else {
                        $tax_class_id = $tax_to_apply;
                        $price_formatted = $this->currency->format($this->tax->calculate($price, $tax_class_id), $currency_code, $currency_value, false);
                    }                    
                } else {
                    $price_formatted = $this->currency->format($price, $currency_code, $currency_value, false);
                }

                $products[$key][$price_type.'_formatted'] = $price_formatted;

                $products[$key]['no_tax_'.$price_type.'_formatted'] = $this->currency->format($price, $currency_code, $currency_value, false);
            }
            $products[$key]['currency_code'] = $currency_code;
        }
    }

    public function format_product_thumbs($profile, &$products) {
        $this->load->model('tool/image');
        $this->load->model('catalog/product');
        $thumb_width = trim($profile['profile']['google_all_feed_config_thumb_width']);
        $thumb_width = is_numeric($thumb_width) ? $thumb_width : 800;
        $thumb_height = trim($profile['profile']['google_all_feed_config_thumb_height']);
        $thumb_height = is_numeric($thumb_height) ? $thumb_height : 800;

        $product_link_purchase = !empty($profile['profile']['google_all_feed_config_product_link_purchase']) ? trim($profile['profile']['google_all_feed_config_product_link_purchase']) : '';


        $final_products = array();
        foreach ($products as $key => $prod) {
            $image = !empty($prod['image']) ? $prod['image'] : (version_compare(VERSION, '2', '<') ? 'no_image.jpg' : 'no_image.png');

            $img_is_link = filter_var($image, FILTER_VALIDATE_URL);
            $prod['image_link'] = $img_is_link ? $img_is_link : (is_file(DIR_IMAGE.$image) ? $this->model_tool_image->resize($image, $thumb_width, $thumb_height) : '');
            $prod['image_link'] = $this->check_link_domain($prod['image_link']);
            $prod['image_link'] = $this->remove_image_webp_format($prod['image_link'], $image, $thumb_width, $thumb_height);

            $product_link = !empty($prod['force_url']) ? $prod['force_url'] : str_replace(' ', '%20', $this->url->link('product/product', '&product_id=' . $prod['product_id'], true));
            //$product_link .= (strpos($product_link, '?') !== false ? '&' : '?').'language='.$this->config->get('config_language_code').'&currency='.$this->config->get('config_currency_code');
            $product_link = $this->check_link_domain($product_link);
            $prod['product_link'] = $product_link;

            if($product_link_purchase)
                $prod['product_link_purchase'] = $this->url->link('extension/module/gmt_direct_checkout', '&product_id=' . $prod['product_id'], true);

            $additional_images = $this->model_catalog_product->getProductImages($prod['product_id']);
            if(empty($prod['additional_images'])) {
                $prod['additional_images'] = array();
                $prod['additional_images_link'] = array();
                foreach ($additional_images as $key => $image) {
                    $img_is_link = filter_var($image['image'], FILTER_VALIDATE_URL);
                    $prod['additional_images'][] = $this->check_link_domain($image['image']);

                    $img_link = $img_is_link ? $this->check_link_domain($image['image']) : (is_file(DIR_IMAGE . $image['image']) ? $this->check_link_domain($this->model_tool_image->resize($image['image'], $thumb_width, $thumb_height)) : '');
                    $img_link = $this->remove_image_webp_format($img_link, $image['image'], $thumb_width, $thumb_height);
                    $prod['additional_images_link'][] = $img_link;
                }
            }
            $final_products[] = $prod;
        }

        $products = $final_products;
    }

    public function remove_image_webp_format($image_link, $real_image, $thumb_width, $thumb_height) {
        if(pathinfo($image_link, PATHINFO_EXTENSION) == 'webp'){
            return $this->model_tool_image->resize($real_image, $thumb_width, $thumb_height);
        }
        return $image_link;

    }

    /**
     * Format product general data
     * 
     * @param array $profile
     * @param array $products
     * @pram arrat $gtin_priority
     */
    public function format_product_general_data($profile, &$products, $gtin_priority) {
        $all_cat_trees = $this->get_all_categories_branches_select($profile);
        
        foreach ($products as $key => $prod) {
            $last_bigger_cat_tree = $this->get_product_categorires_bigger_tree($prod['product_id'], $all_cat_trees);
            $products[$key]['last_category_id'] = $last_bigger_cat_tree['category_id'];
            $products[$key]['last_category_tree'] = $last_bigger_cat_tree['tree'];
            $products[$key]['first_category_tree'] = explode(" &gt; ", $last_bigger_cat_tree['tree'])[0];
            $products[$key]['gtin'] = $this->get_product_gtin($prod, $gtin_priority);
            $products[$key]['original_product_identificator'] = $prod['product_identificator'];
        }
    }

    public function check_link_domain($link) {
        return str_replace(HTTPS_SERVER, $this->store_url, $link);
    }

    /**
     * Get product gtin
     * 
     * @param array $product
     * @param array $gtin_priority
     */
    public function get_product_gtin($product, $gtin_priority) {
        foreach ($gtin_priority as $index) {
            if(array_key_exists($index, $product) && !empty($product[$index])) {
                return $product[$index];
            }
        }
        
        return '';
    }

    public function get_all_categories_branches_select($profile) {
        $language_id = array_key_exists('google_all_feed_config_language_id', $profile['profile']) && $profile['profile']['google_all_feed_config_language_id'] !== '' ? $profile['profile']['google_all_feed_config_language_id'] : $this->config->get('config_language_id');
        $sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR ' &gt; ') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$language_id . "' AND cd2.language_id = '" . (int)$language_id . "'";
        $sql .= " GROUP BY cp.category_id";
        $sql .= " ORDER BY sort_order ASC";
        $query = $this->db->query($sql);

        $final_categories = array();

        foreach ($query->rows as $key => $cat_info) {
            $final_categories[$cat_info['category_id']] = $cat_info['name'];
        }

        return $final_categories;
    }

    public function get_product_categorires_bigger_tree($product_id, $all_cat_trees) {
        $categories = $this->get_product_categories($product_id);
        $last_tree = array(
            'category_id' => '',
            'tree' => ''
        );

        if(!empty($categories)) {
            $bigger_tree = 0;
            foreach ($categories as $cat_id) {
                $count_tree = array_key_exists($cat_id, $all_cat_trees) ? count(explode(' &gt; ', $all_cat_trees[$cat_id])) : 0;

                if($count_tree > $bigger_tree) {
                    $bigger_tree = $count_tree;
                    $last_tree['category_id'] = $cat_id;
                    $last_tree['tree'] = $all_cat_trees[$cat_id];
                }
            }
        }

        return $last_tree;
    }

    public function get_product_categories($prod_id) {
        $sql = "SELECT ".$this->escape_database_field('category_id')." FROM ".$this->escape_database_table_name('product_to_category').' WHERE '.$this->escape_database_field('product_id')."=".$this->escape_database_value($prod_id);
        $categories = $this->db->query($sql);
        $final_categories = array();
        foreach ($categories->rows as $key => $cat_info) {
            $final_categories[] = $cat_info['category_id'];
        }

        return $final_categories;
    }
        
    public function format_xml_value($value) {
        if(!is_numeric($value) && $value)
            $value = html_entity_decode($value);

        return is_numeric($value) ? $value : '<![CDATA['.$value.']]>';
    }

    public function format_xml_description($description, $limit, $xml = true) {
        //$description = trim(strip_tags(htmlspecialchars_decode($description), '<br></ br>'));
        $description = trim(strip_tags(htmlspecialchars_decode($description), '<br></ br><ul><li><ol>'));
        $description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
        $description = str_replace('&nbsp;', ' ', $description);
        $description = str_replace('<br />', '<br>', $description);
        $description = strlen($description) > $limit ? mb_substr($description, 0, ($limit-3)).'...' : $description;
        //Avoid chinnese error: Bytes: 0x08 0xEF 0xBC 0x8C
        $description = preg_replace('/[\x00-\x1f]/','',$description);
        $description = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $description);

        return $xml ? $this->format_xml_value($description) : $description;
    }

    public function extract_values_in_array($items, $key) {
        return array_unique(array_map( function($item) use ($key) {
            return is_object($item) ? $item->$key : $item[$key];
        }, $items));
    }

    public function extract_google_category_id($cat_name) {
        if(empty($cat_name) && $this->google_taxonomy_default != '')
            $cat_name = $this->google_taxonomy_default;

        if (!is_string($cat_name)) {
            return '';
        }

        $google_category_id = explode(' - ', $cat_name);
		
        return !empty($google_category_id[0]) && is_numeric($google_category_id[0]) ? $google_category_id[0] : '';
    }

    public function get_config_meta_title() {
        $meta_title = $this->config->get('config_meta_title');

        if(!is_array($meta_title))
            return $meta_title;

        return '';
    }

    public function get_config_meta_description() {
        $meta_description = $this->config->get('config_meta_description');

        if(!is_array($meta_description))
            return $meta_description;

        return '';
    }

    /**
     * @param array $elements the products array
     * @param array $tags definitions of tags to generate the feed elements
     * @param bool $isXml defines if the feed is in xml format
     * @return array
     */
    public function elementsToFeed($elements, $tags, $isXml = true)
    {
        $feed_elements = array();
        foreach ($elements as $key => $product) {
            $temp = array();
            foreach ($tags as $tag_name => $index) {
                $value = null;

                if (is_null($index)) {
                    continue;
                } else if (is_string($index)) {
                    $value = ($isXml) ? $this->format_xml_value($product[$index]) : $product[$index];
                } else if (is_callable($index)) {
                    $value = $index($product);
                }

                if ($value !== null) {
                    if ($isXml) {
                        $temp[$tag_name] = $value;
                    } else {
                        $temp[] = $value;
                    }
                }
                else if(!$isXml){
                    $temp[] = '';
                }
            }

            $feed_elements[] = $temp;
        }
        return $feed_elements;
    }
}