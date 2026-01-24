<?php
class ControllerExtensionModuleGoogleAllFeed extends Controller 
{
	public function index() {
	    ini_set('max_execution_time', -1);
	    ini_set('memory_limit', -1);

	    if(function_exists("error_reporting")) {
	        error_reporting(E_ALL);
        }

	    ini_set('display_errors', 'On');

	    set_error_handler(array(&$this, 'customCatchError'));

	    if (function_exists("error_reporting")) {
            register_shutdown_function(array(&$this, 'fatalErrorShutdownHandler'));
        }

        try {
            $gtin_priority = array('upc', 'ean', 'jan', 'isbn');
            $this->load->model('extension/module/google_all_feed');
            
            $this->google_taxonomy_default = $this->config->get('google_all_feed_taxonomy_cat_master');

            $feed_id = array_key_exists('feed_id', $this->request->get) ? $this->request->get['feed_id'] : $this->model_extension_module_google_all_feed->exception('Param "feed_id" not found');
            
            $this->feed = $this->model_extension_module_google_all_feed->load_feed($feed_id);
            $feed_elements = $this->model_extension_module_google_all_feed->get_elements($this->feed, $gtin_priority);
            $feed_elements_ids = $this->model_extension_module_google_all_feed->extract_values_in_array($feed_elements, 'product_id');

            $model_to_load = 'extension/module/google_all_feed_'.$this->feed['type'];
            $model_feed_type = 'model_extension_module_google_all_feed_'.$this->feed['type'];
            $this->load->model($model_to_load);
            $feed_elements = $this->{$model_feed_type}->process_elements($this->feed, $feed_elements, $feed_elements_ids);
            $model_to_load = 'extension/module/google_all_file';
            $model_file = 'model_extension_module_google_all_file';
            $this->load->model($model_to_load);

            $format = $this->feed['file_format'];
            $model_path = 'extension/module/google_all_file_'.$format;
            $model_name = 'model_extension_module_google_all_file_'.$format;
            $this->load->model('extension/module/google_all_file');
            $this->load->model($model_path);
            $this->model_extension_module_google_all_file->clean_temp_folder();
            $this->{$model_name}->create_file();

            $path = str_replace('catalog/', 'google_all_feeds_custom_xml_tags/', DIR_APPLICATION);
            if (is_dir($path) && is_file($path.$feed_id.'.php')) {
                require_once($path.$feed_id.'.php');

                foreach ($feed_elements['elements'] as $key => $element) {
                    $feed_elements['elements'][$key] = array_merge($feed_elements['elements'][$key], $new_elements);
                }
            }

            $this->{$model_name}->insert_data($feed_elements);

            if (array_key_exists('corrupt_images', $this->session->data) && !empty($this->session->data['corrupt_images'])) {
                /*
                try {

                } catch (Exception $e) {
                    $show_error = array_key_exists('route', $_GET) && $_GET['route'] == 'extension/module/google_all_feed';
                    if($show_error) {
                        if(!array_key_exists('corrupt_images', $this->session->data)) $this->session->data['corrupt_images'] = array();
                        $this->session->data['corrupt_images'][] = $PATH_IMAGE;
                        return true;
                    }
                }
                 */
                echo '<h1>Corrupt images detected</h1>';
                echo '<ul>';

                foreach ($this->session->data['corrupt_images'] as $key => $img_path) {
                    echo '<li>'.$img_path.'</li>';
                }

                echo '</ul>';
                $this->session->data['corrupt_images'] = array();
                die();
            }

            $this->{$model_name}->download_file_export();
        } catch (Exception $e) {
            echo '<b>Error</b>: '.$e->getMessage();
        }

        restore_error_handler();

        //Hacemos los cambios que se requieran para ese tipo de feed
    }

    public function fatalErrorShutdownHandler() {
        $last_error = error_get_last();

        if (is_array($last_error)) {
            $code = array_key_exists('code', $last_error) ? $last_error['code'] : '';
            $type = array_key_exists('type', $last_error) ? $last_error['type'] : '';
            $message = array_key_exists('message', $last_error) ? $last_error['message'] : '';
            $file = array_key_exists('file', $last_error) ? str_replace(DIR_APPLICATION, '', $last_error['file']) : '';
            $line = array_key_exists('line', $last_error) ? $last_error['line'] : '';
            
            $skip_error = stripos($message, 'use mysqli or PDO') !== false
                || stripos($message, 'imagecreatefromgif') !== false
                || stripos($message, 'imagecreatefrompng') !== false
                || stripos($message, 'imagecreatefromjpeg') !== false
                || stripos($message, 'imagecreatefromwebp') !== false
                || stripos($message, 'imagejpeg') !== false
                || stripos($message, 'imagepng') !== false
                || stripos($message, 'imagegif') !== false
                || stripos($message, 'imagewebp') !== false
                || stripos($message, 'getimagesize') !== false
                || stripos($file, 'twig') !== false;

            if(!$skip_error) {
                $final_message = '<ul>';
                $final_message .= !empty($code) ? '<li><b>Error code:</b> ' . $code . '</li>' : '';
                $final_message .= !empty($file) ? '<li><b>Error file:</b> ' . $file . '</li>' : '';
                $final_message .= !empty($line) ? '<li><b>Error line:</b> ' . $line . '</li>' : '';
                $final_message .= !empty($message) ? '<li><b>Error message:</b> ' . $message . '</li>' : '';
                $final_message .= '</ul>';

                throw new Exception($final_message);
            }
        }

        return false;
    }

    public function customCatchError( $errno = '', $errstr = '', $errfile = '', $errline = '' ) {
        $file = str_replace(DIR_APPLICATION, '', $errfile);

        $skip_error = stripos($errstr, 'use mysqli or PDO') !== false
            || stripos($errstr, 'imagecreatefromgif') !== false
            || stripos($errstr, 'imagecreatefrompng') !== false
            || stripos($errstr, 'imagecreatefromjpeg') !== false
            || stripos($errstr, 'imagecreatefromwebp') !== false
            || stripos($errstr, 'imagejpeg') !== false
            || stripos($errstr, 'imagepng') !== false
            || stripos($errstr, 'imagegif') !== false
            || stripos($errstr, 'imagewebp') !== false
            || stripos($errstr, 'filemtime') !== false
            || stripos($errstr, 'getimagesize') !== false
            || stripos($file, 'twig') !== false;


        if (!$skip_error) {
            if (!$errno) {
                $final_message = $errstr;
            } else {
                $final_message = '<ul>';
                $final_message .= '<li><b>Error code:</b> ' . $errno . '</li>';
                $final_message .= '<li><b>Error file:</b> ' . $file . '</li>';
                $final_message .= '<li><b>Error line:</b> ' . $errline . '</li>';
                $final_message .= '<li><b>Error message:</b> ' . $errstr . '</li>';
                $final_message .= '</ul>';
            }

            throw new Exception($final_message);
        }
    }
}
