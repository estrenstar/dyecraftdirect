<?php
use google_marketing_tools\Master;

class ControllerExtensionModuleGmtDatalayer extends Controller {

    public function setup($params = array()) {

        $email = '';
        $customer_info = $this->GMTMaster->get_customer_info();

        if (!empty($customer_info['email'])) {
            $email = $customer_info['email'];
        } elseif (!empty($this->session->data['gmt_customer_email'])) {
            $email = $this->session->data['gmt_customer_email'];
        }

        if(empty($_POST['datalayerDataGMT'])) {
            $this->response->addHeader('X-Robots-Tag: noindex, nofollow', true);
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(array()));
            exit;
        }

        $json = $_POST['datalayerDataGMT'];

        // Elimina caracteres de control no válidos excepto \r \n \t
        $json = preg_replace('/[[:cntrl:]]/', '', $json);
        $json = mb_convert_encoding($json, 'UTF-8', 'UTF-8');

        $data = json_decode($json, true);

        $data['general_data']['clientId'] = $this->GMTMaster->get_client_id();
        $data['general_data']['gclDc'] = $this->GMTMaster->get_gcl_dc();
        $data['general_data']['fbclid'] = $this->GMTMaster->get_fbclid();
        $data['general_data']['msclkid'] = $this->GMTMaster->get_msclkid();
        $data['general_data']['event_id'] = $this->GMTMaster->generate_uuid();
        $data['general_data']['email'] = $email;
        $data['general_data']['coupon'] = isset($this->session->data['coupon']) ? $this->session->data['coupon'] : '';
        $data['general_data']['userId'] = $this->GMTMaster->get_user_id();
        $data['general_data']['cart_products'] = $this->DataLayer->format_cart_products($this->GMTMaster->get_cart_products());
        $data['fb_pixel_track_info']['external_id'] = $this->session->getId();
        $data['fb_pixel_track_info']['event_id'] = $this->GMTMaster->generate_uuid();
        $data['fb_pixel_track_info']['event_id_page_view'] = $this->GMTMaster->generate_uuid();

        if (!empty($customer_info['email'])) {
            $data['tiktok_identify'] = array(
                'email' => $this->DataLayer->ads_hash($this->DataLayer->ads_format_email($customer_info['email'])),
                'phone_number' => $this->DataLayer->ads_hash($this->DataLayer->ads_format_phone($customer_info['phone'], !empty($customer_info['country_code']) ? $customer_info['country_code'] : '')),
                'external_id' => hash('SHA256', trim($this->session->getId()))
            );

            if ((in_array($data['general_data']['current_view'], array('account_success', 'contact_success'))) || isset($this->session->data['gmt_logged'])) {
                $data['customer'] = array(
                    'user_id'   => $this->GMTMaster->get_user_id(),
                    'email'     => $this->DataLayer->ads_format_email($customer_info['email'])
                );

                if (isset($customer_info['phone']) && !empty($customer_info['phone'])) {
                    $data['customer']['phone'] = $this->DataLayer->ads_format_phone($customer_info['phone'], !empty($customer_info['country_code']) ? $customer_info['country_code'] : '');
                }

                if (isset($customer_info['firstname'])) {
                    $data['customer']['address']['firstname'] = $customer_info['firstname'];
                }

                if (isset($customer_info['lastname'])) {
                    $data['customer']['address']['lastname'] = $customer_info['lastname'];
                }

                if (isset($customer_info['city'])) {
                    $data['customer']['address']['city'] = $customer_info['city'];
                }

                if (isset($customer_info['address'])) {
                    $data['customer']['address']['street_address'] = $customer_info['address'];
                }

                if (isset($customer_info['zone'])) {
                    $data['customer']['address']['region'] = $customer_info['zone'];
                }

                if (isset($customer_info['country_code'])) {
                    $data['customer']['address']['country'] = $customer_info['country_code'];
                }

                if (isset($customer_info['postcode'])) {
                    $data['customer']['address']['postal_code'] = $customer_info['postcode'];
                }

                if (isset($this->session->data['gmt_logged'])) {
                    $data['account_logged_event'] = $data['customer'];
                }
            }
        }

        //TODO
        $data['general_data']['string_searched'] = '';

        // Call GMT addons
        $gmt_addons = !is_null($this->DevmanGmtAddons) && is_array($this->DevmanGmtAddons) ? $this->DevmanGmtAddons : array();

        foreach ($gmt_addons as $addon) {
            $this->load->controller('extension/module/' . $addon . '/track', array('gmt_datalayer' => $data));
        }

        $this->response->addHeader('X-Robots-Tag: noindex, nofollow', true);
        $this->response->addHeader('Content-Type: application/json');

        $this->response->setOutput(json_encode($data));
    }

    public function save_order_tracking_status() {
        $result = array();

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $order_id   = isset($data['order_id']) ? (int)$data['order_id'] : 0;
        $action     = isset($data['action']) && in_array($data['action'], ['sent', 'cancel']) ? $data['action'] : '';
        $from_admin = isset($data['from_admin']) ? (bool)$data['from_admin'] : false;

        try {
            $order_query = $this->db->query("SELECT o.total, o.currency_code, o.currency_value, og.consent as gmt_consent FROM `". DB_PREFIX . "order` o INNER JOIN `" . DB_PREFIX . "order_google_all` og ON (o.order_id = og.order_id) WHERE o.order_id = '".(int)$order_id."'");

            if ($order_query->num_rows) {
                $order_data = $order_query->row;
                $consent    = isset($order_data['gmt_consent']) && !empty($order_data['gmt_consent']) ? json_decode($order_data['gmt_consent'], true) : array();
                //$tracket    = !$from_admin ? Master::can_track_the_conversion($consent) : true;
                //Always that "Purchase" event was triggered, set a tracked date.
                $tracket = true;

                if ($action === '') {
                    $result['error'] = 'Error - Invalid action';
                } elseif ($order_id) {
                    $current_date = date('Y-m-d H:i:s');

                    if (isset($data['order_total'])) {
                        $order_total = (float)$data['order_total'];
                    } else {
                        $order_total = (float)$this->currency->format($order_data['total'], $order_data['currency_code'], $order_data['currency_value'], false);
                    }

                    if ($action === 'sent') {
                        if ($tracket) {
                            $tracked_info = array(
                                'date'          => $current_date,
                                'order_total'   => $order_total,
                                'currency_code' => $order_data['currency_code']
                            );

                            $this->db->query("UPDATE `" . DB_PREFIX . "order_google_all` SET `tracked_info` = '" . $this->db->escape(json_encode($tracked_info)) . "' WHERE `order_id` = '" . (int)$order_id . "'");
                        }
                    } else {
                        $cancelled_info = array(
                            'date'          => $current_date,
                            'order_total'   => $order_total,
                            'currency_code' => $order_data['currency_code']
                        );

                        $this->db->query("UPDATE `" . DB_PREFIX . "order_google_all` SET `cancelled_info` = '" . $this->db->escape(json_encode($cancelled_info)) . "' WHERE `order_id` = '" . (int)$order_id . "'");
                    }

                    $result['success'] = true;

                    if (($action === 'sent' && $tracket) || $action === 'cancel') {
                        $result['date'] = $current_date;
                    } else {
                        $result['date'] = '';
                    }
                } else {
                    $result['error'] = 'Error - Order id not defined';
                }
            }
        } catch (Exception $e) {
            $result['error'] = 'Exception: ' . $e->getMessage();
        }

        $this->response->addHeader('X-Robots-Tag: noindex, nofollow', true);
        $this->response->addHeader('Content-Type: application/json');

        $this->response->setOutput(json_encode($result));
    }

    public function addons() {
        $json       = file_get_contents('php://input');
        $gmt_data   = json_decode($json, true);

        $gmt_addons = !is_null($this->DevmanGmtAddons) && is_array($this->DevmanGmtAddons) ? $this->DevmanGmtAddons : array();

        foreach ($gmt_addons as $addon) {
            $this->load->controller('extension/module/'.$addon.'/track', $gmt_data);
        }
    }
}