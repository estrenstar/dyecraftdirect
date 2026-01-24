<?php
class ControllerExtensionModuleGmtDebug extends Controller 
{
	public function index() {
        $gmt_data = $this->DataLayer->get_datalayer_data();

	    $no_debug = array("order_id", "enhanced_ecommerce_data");

	    foreach ($gmt_data as $key => $val) {
	        if (!in_array($key, $no_debug)) {
	            $gmt_data[$key] = json_encode($val, JSON_PRETTY_PRINT);
            } else {
	            unset($gmt_data[$key]);
            }
	    }

	    $data['gmt_data'] = $gmt_data;

        if (version_compare(VERSION, '2', '>=')) {
            $path = "extension/module/gmt_debug";
            $template_path = version_compare(VERSION, '2.1.0.3', '<=') ? 'default/template/'.$path.'.tpl' : $path;

            return $this->load->view($template_path.'', $data);
        } else {
            $this->data = $data;
            $this->template = 'extension/module/gmt_debug.tpl';
            
            $this->response->setOutput($this->render());
        }
    }
}