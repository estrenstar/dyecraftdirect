<?php
    class ModelExtensionModuleGoogleAllFile extends ModelExtensionModuleGoogleAllFeed {
        public function __construct($registry){
            parent::__construct($registry);

            // Support for Windows and Unix paths
            $ds = (DIRECTORY_SEPARATOR == '\\') ? '\\' : '/';

            $this->root_path        = substr(DIR_APPLICATION, 0, strrpos(DIR_APPLICATION, '/', -2)).'/';
            $this->path_tmp         = str_replace('catalog'.$ds.'model'.$ds.'extension'.$ds.'module'.$ds.'google_all_file.php', 'google_all_feeds'.$ds, __FILE__);
            $this->path_tmp_public  = HTTPS_SERVER.($this->is_mijoshop ? 'components/com_mijoshop/opencart/':'').'google_all_feeds/';

            if(!is_dir($this->path_tmp)) {
                mkdir($this->path_tmp, 0755);
            }

            $htaccess_file = $this->path_tmp . '.htaccess';
            if (!file_exists($htaccess_file)) {
                $htaccess = 'AddType text/iepro iepro
                    <FilesMatch "\.(json|xlsx|ods|xml|xls|txt|csv)$">
                        allow from all
                    </FilesMatch>';
                file_put_contents($htaccess_file, $htaccess);
            }

            if($this->feed) {
                $this->file_destiny = array_key_exists('google_all_feed_config_file_destiny', $this->feed['profile']) ? $this->feed['profile']['google_all_feed_config_file_destiny'] : '';
                $this->file_type = $this->feed['file_format'];
                $this->feed_type = $this->feed['profile']['google_all_feed_config_file_format'];
            }
        }

        public function get_filename() {
            $filename = 'NO DEFINED';
            if($this->feed['profile'])
                $filename = $this->feed_type.'-'.date('Y-m-d-His').'.'.$this->file_type;
            elseif($this->force_filename)
                $filename = $this->force_filename.'-'.date('Y-m-d-His').'.'.$this->file_type;

            return $filename;
        }

        public function clean_temp_folder() {
            $files = glob($this->path_tmp.'*');
            foreach($files as $file){
                if(is_file($file)) unlink($file);
            }
        }

        /*
         * Called from model ie_pro_export.php
         * */
        function download_file_export() {
            if ($this->file_destiny == 'download') {
                $this->download_file($this->filename);
            } elseif ($this->file_destiny == 'server') {
                $new_path = rtrim($this->feed['profile']['google_all_feed_config_file_destiny_server_path'], '/') . '/';

                if (empty($new_path)) $this->exception($this->language->get('progress_export_empty_internal_server_path'));
                if (!file_exists($new_path)) mkdir($new_path, 0775, true);

                $filename = $this->_get_filename_with_sufix();
                $final_path = $new_path . $filename;

                copy($this->filename_path, $final_path);
                echo "File saved in <b>".$final_path.'</b><br>'; die;
            } elseif ($this->file_destiny == 'external_server') {
                $new_path = rtrim($this->feed['profile']['google_all_feed_config_ftp_path'], '/') . '/';
                $filename = $this->feed['profile']['google_all_feed_config_ftp_file'] . '.' . $this->file_type;

                if (empty($this->feed['profile']['google_all_feed_config_ftp_file'])) $this->exception($this->language->get('progress_export_ftp_empty_filename'));

                $final_path = $new_path . $filename;

                $connection = $this->ftp_open_connection();

                try {
                    ftp_chdir($connection, $new_path);
                } catch (Exception $e) {
                    ftp_mkdir($connection, $new_path);
                }

                $upload = ftp_put($connection, $final_path, $this->filename_path, FTP_BINARY);

                if (!$upload)
                    $this->exception(sprintf($this->language->get('progress_export_ftp_error_uploaded'), $final_path));

                ftp_close($connection);
                die("File uploaded to external server.");
            }
        }

        public function download_file($filename) {
            //$filePath = $this->path_tmp_public.$filename;
            $filePath = $this->path_tmp.$filename;
            /*$options  = array('http' => array('user_agent' => 'Test User-Agent'));
            $context  = stream_context_create($options);
            $output = file_get_contents($filePath, false, $context);*/

            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );

            $output = file_get_contents($filePath, false, stream_context_create($arrContextOptions));




            if($this->feed['file_format'] == 'xml') {
                $this->response->addHeader('Content-Type: application/xml');
                $this->response->setOutput($output);
            } else {
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename='.$filename);
                header('Pragma: no-cache');
                readfile($filePath);
            }
        }

        function check_extension_profile($file_name) {
            $extension_file =  strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if(!empty($extension_file) && $extension_file != $this->file_format)
                $this->exception(sprintf($this->language->get('progress_import_error_extension'), $this->file_format, $extension_file));
        }

        function ftp_open_connection() {
            $server = $this->feed['profile']['google_all_feed_config_ftp_host'];
            $username = $this->feed['profile']['google_all_feed_config_ftp_username'];
            $password = $this->feed['profile']['google_all_feed_config_ftp_password'];
            $port = $this->feed['profile']['google_all_feed_config_ftp_port'] ? $this->feed['profile']['google_all_feed_config_ftp_port'] : 21;

            $connection = ftp_connect($server, $port);
            if (!$connection)
                $this->exception($this->language->get('progress_export_ftp_error_connection'));
            $login = ftp_login($connection, $username, $password);

            if(array_key_exists('google_all_feed_config_ftp_passive_mode', $this->feed['profile']) && $this->feed['profile']['google_all_feed_config_ftp_passive_mode'])
                ftp_pasv($connection, true);

            if (!$login)
                $this->exception($this->language->get('progress_export_ftp_error_login'));

            return $connection;
        }

        function _get_filename_with_sufix() {
            $sufix = '';
            if(!empty($this->feed['profile']['google_all_feed_config_file_destiny_server_file_name_sufix'])) {
                $sufix_type = $this->feed['profile']['google_all_feed_config_file_destiny_server_file_name_sufix'];
                $sufix = '-'.($sufix_type == 'date' ? date('Y-m-d') : date('Y-m-d-His'));
            }
            $filename = $this->feed['profile']['google_all_feed_config_file_destiny_server_file_name'].$sufix.'.'.$this->file_type;
            return $filename;
        }

        function get_download_link() {
            $download_link = $this->path_tmp_public.$this->filename;
            return $download_link;
        }

        function get_force_download_link() {
            $download_link = html_entity_decode($this->url->link('extension/module/google_all_feed/download_file'.'&filename='.$this->filename));
            return $download_link;
        }
    }
?>