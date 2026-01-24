<?php
    class ModelExtensionModuleGoogleAllFeed extends ModelExtensionModuleGoogleAll {
        function save() {
            $this->request->post['no_exit'] = true;
            $this->validate_permiss();
            $feed_id = array_key_exists('feed_id', $this->request->post) ? $this->request->post['feed_id'] : '';
            $profile_name = array_key_exists('google_all_feed_config_feed_name', $this->request->post) ? $this->request->post['google_all_feed_config_feed_name'] : '';

            $array_return = array('error' => false, 'message' => '');

            if(count($this->request->post, true) > ini_get('max_input_vars'))
                $this->exception(sprintf($this->_lang('feed_error_max_input_vars'), ini_get('max_input_vars'), count($this->request->post, true)));

            if(empty($profile_name))
                $this->exception($this->_lang('feed_error_empty_name'));

            if(array_key_exists('export_filter', $this->request->post)) {
                unset($this->request->post['export_filter']['replace_by_number']);

                $final_config = array(
                    'filters' => array(),
                    'config' => array(),
                );
                foreach ($this->request->post['export_filter'] as $key => $val) {
                    if(is_numeric($key)) {
                        $final_config['filters'][] = $val;
                    } else {
                        $final_config['config'][$key] = $val;
                    }
                }

                $this->request->post['export_filter'] = $final_config;

                if(empty($this->request->post['export_filter']['filters']))
                    unset($this->request->post['export_filter']);
                else
                    $this->request->post['export_filter']['filters'] = array_values($this->request->post['export_filter']['filters']);
            }
            unset($this->request->post['google_all_feed_config_profiles']);
            foreach ($this->request->post as $key => $config) {
                if(is_array($config) && count($config) == 1 && array_key_exists(0, $config) && empty($config[0]))
                    $this->request->post[$key] = '';
            }

            $config_json = $this->db->escape(json_encode($this->request->post));
            $profile_name = $this->db->escape($profile_name);
            $profile_type = $this->request->post['google_all_feed_config_file_format'];
            if(!empty($feed_id)) {
                $sql = "UPDATE ".$this->escape_database_table_name('gmt_feeds')." SET ".$this->escape_database_field('type')." = ".$this->escape_database_value($profile_type).", ".$this->escape_database_field('name')." = ".$this->escape_database_value($profile_name).", ".$this->escape_database_field('profile')." = '".$config_json."', ".$this->escape_database_field('modified')." = NOW() WHERE id = ".$feed_id;
            } else {
                $sql = "INSERT INTO ".$this->escape_database_table_name('gmt_feeds')." SET ".$this->escape_database_field('type')." = ".$this->escape_database_value($profile_type).", ".$this->escape_database_field('name')." = ".$this->escape_database_value($profile_name).", ".$this->escape_database_field('profile')." = '".$config_json."', ".$this->escape_database_field('created')." = NOW(), ".$this->escape_database_field('modified')." = NOW();";
            }
            $this->db->query($sql);
            echo json_encode($array_return); die;
        }

        function isValidXmlElementName($elementName, $check_special_names)
        {
            $elementName = html_entity_decode($elementName);
            if($check_special_names && $this->is_special_xml_name($elementName) && !preg_match('/\s/',$elementName))
                return true;

            $elementName = str_replace('>', '', $elementName);

            try {
                new DOMElement($elementName);
                return true;
            } catch (DOMException $e) {
                return false;
            }
            return false;
        }

        function delete() {
            $this->request->post['no_exit'] = true;
            $this->validate_permiss();
            $feed_id = array_key_exists('feed_id', $this->request->post) ? $this->request->post['feed_id'] : '';
            $array_return = array('error' => false, 'message' => '');
            if(empty($feed_id)) {
                $array_return['error'] = true;
                $array_return['message'] = $this->_lang('feed_error_delete_feed_id_empty');
                echo json_encode($array_return); die;
            }
            $sql = "DELETE FROM ".$this->escape_database_table_name('gmt_feeds')." WHERE ".$this->escape_database_field('id')." = ".$this->escape_database_value($feed_id);
            $this->db->query($sql);
            echo json_encode($array_return); die;
        }

        function load($force_id = '', $skip_json_encode = false) {
            $array_return = array('error' => false, 'message' => '');

            $feed_id_ajax = array_key_exists('feed_id', $this->request->post) ? $this->request->post['feed_id'] : '';
            $json_encode = !empty($feed_id_ajax) && !$skip_json_encode;
            $feed_id = !empty($feed_id_ajax) ? $feed_id_ajax : $force_id;

            $sql = "SELECT * FROM ".$this->escape_database_table_name('gmt_feeds')." WHERE ".$this->escape_database_field('id')." = ".$this->escape_database_value($feed_id);
            $result = $this->db->query($sql);

            if(empty($result->row)) {
                $array_return['error'] = true;
                $array_return['message'] = $this->_lang('feed_load_error_not_found');
                echo json_encode($array_return); die;
            }

            $result->row['profile'] = json_decode($this->sanitize_value(str_replace('&quot;', '\"', $result->row['profile'])), true);

            if($json_encode) {
                echo json_encode($result->row); die;
            }
            else return $result->row;
        }

        function get_profiles($type = '', $id = '', $select_format = false) {
            $sql = "SELECT * FROM ".$this->escape_database_table_name('gmt_feeds');
            if(!empty($type))
                $sql .= ' WHERE '.$this->escape_database_field('type').' = '.$this->escape_database_value($type);
            if(!empty($id))
                $sql .= ' WHERE '.$this->escape_database_field('id').' = "'.$this->escape_database_value($id);

            $sql .= ' ORDER BY '.$this->escape_database_field('type').' ASC, '.$this->escape_database_field('modified').' ASC';

            $result = $this->db->query($sql);

            foreach ($result->rows as $key => $profile) {
                $profile_decoded = json_decode($profile['profile'], true);

                if(empty($profile_decoded['feed_id']))
                    $profile_decoded['feed_id'] = $profile['id'];
                $result->rows[$key]['profile'] = $profile_decoded;
            }

            if(!$select_format) {
                return $result->rows;
            } else {
                $final_profiles = array('' => $this->_lang('feed_select_text_empty'));
                foreach ($result->rows as $key => $profile) {
                    $final_profiles[$profile['id']] = '['.$profile['id'].'] - '.$profile['name'];
                }
                return $final_profiles;
            }
        }

        function get_profiles_for_url_and_crons() {
            $profiles = $this->get_profiles();

            $final_profiles = array();
            foreach ($profiles as $key => $prof) {
                $type = $prof['profile']['google_all_feed_config_file_destiny'];
                $name = '['.$prof['profile']['feed_id'].'] - '.$prof['profile']['google_all_feed_config_feed_name'];

                $store_id = $prof['profile']['google_all_feed_config_store_id'];
                $store_url = rtrim($this->stores[$store_id]['url'],'/') . '/';

                if(array_key_exists($store_id, $this->stores)) {
                    $store_ssl_url = array_key_exists('ssl', $this->stores[$store_id]) ? $this->stores[$store_id]['ssl'] : '';

                    if (!empty($store_ssl_url)) {
                        $store_url = rtrim($store_ssl_url, '/') . '/';
                    }
                }
                
                $lang_code = !empty($prof['profile']['google_all_feed_config_language_id']) ? $this->db->query('SELECT code FROM '.DB_PREFIX.'language WHERE language_id = '.$prof['profile']['google_all_feed_config_language_id'])->row['code'] : '';

                $final_profiles[] = array(
                    'id' => $prof['id'],
                    'name' => $name,
                    'url_direct' => $store_url.'index.php?route=extension/module/google_all_feed&feed_id='.$prof['id'].'&language='.$lang_code.'&currency='.$prof['profile']['google_all_feed_config_currency_code'],
                    'url_cron' => $type != 'download' ? DIR_CATALOG.'/controller/extension/module/gmt_cron.php feed_id='.$prof['id'] : false
                );
            }
            return $final_profiles;
        }

        public function _check_feeds_table() {
            $this->db->query("CREATE TABLE IF NOT EXISTS ".$this->escape_database_table_name('gmt_feeds')." (
              ".$this->escape_database_field('id')." int(11) unsigned NOT NULL AUTO_INCREMENT,
              ".$this->escape_database_field('type')." varchar(100) DEFAULT NULL,
              ".$this->escape_database_field('name')." varchar(360) DEFAULT NULL,
              ".$this->escape_database_field('profile')." MEDIUMTEXT,
              ".$this->escape_database_field('created')." datetime DEFAULT NULL,
              ".$this->escape_database_field('modified')." datetime DEFAULT NULL,
              PRIMARY KEY (".$this->escape_database_field('id').")
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
    }
?>