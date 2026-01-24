<?php
class ControllerExtensionModuleGa4Funnel extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/ga4_funnel');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_ga4_funnel', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_measurement_id'] = $this->language->get('entry_measurement_id');
        $data['entry_user_id'] = $this->language->get('entry_user_id');
        $data['entry_journal'] = $this->language->get('entry_journal');
        $data['entry_debug'] = $this->language->get('entry_debug');
        $data['help_measurement_id'] = $this->language->get('help_measurement_id');
        $data['help_user_id'] = $this->language->get('help_user_id');
        $data['help_journal'] = $this->language->get('help_journal');
        $data['help_debug'] = $this->language->get('help_debug');

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['error_measurement_id'] = isset($this->error['measurement_id']) ? $this->error['measurement_id'] : '';

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array('text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
        $data['breadcrumbs'][] = array('text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        $data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/ga4_funnel', 'user_token=' . $this->session->data['user_token'], true));

        $data['action'] = $this->url->link('extension/module/ga4_funnel', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $keys = array(
            'module_ga4_funnel_status' => 0,
            'module_ga4_funnel_measurement_id' => '',
            'module_ga4_funnel_user_id' => 1,
            'module_ga4_funnel_journal' => 1,
            'module_ga4_funnel_debug' => 0
        );

        foreach ($keys as $k => $default) {
            if (isset($this->request->post[$k])) {
                $data[$k] = $this->request->post[$k];
            } else {
                $data[$k] = $this->config->get($k);
                if ($data[$k] === null) $data[$k] = $default;
            }
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/ga4_funnel', $data));
    }

    public function install() {
        $this->load->model('setting/event');
        // Remove any existing events first
        $this->model_setting_event->deleteEventByCode('ga4_funnel');
        // Inject our view right before footer renders
        $this->model_setting_event->addEvent('ga4_funnel', 'catalog/controller/common/footer/before', 'extension/module/ga4_funnel/eventInject', 1, 0);
    }

    public function uninstall() {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('ga4_funnel');
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/ga4_funnel')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (empty($this->request->post['module_ga4_funnel_measurement_id']) || strpos($this->request->post['module_ga4_funnel_measurement_id'], 'G-') !== 0) {
            $this->error['measurement_id'] = $this->language->get('error_measurement_id');
        }
        return !$this->error;
    }
}
