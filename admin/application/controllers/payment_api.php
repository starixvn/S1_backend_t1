<?php

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Payment API
 *
 * General behaviors include
 * * Load Template
 * * Display Payment Api's
 * * Get System Types
 * * Add/update/delete of Payment API
 * * Activate/Deactivation of Payment API
 *
 * @category payment_api
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 *
 */

class Payment_Api extends BaseController {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library(array('permissions', 'form_validation', 'template', 'pagination', 'report_functions', 'player_manager', 'duplicate_account', 'utils'));
        $this->load->model('external_system');

        $this->permissions->checkSettings();
        $this->permissions->setPermissions(); //will set the permission for the logged in user
    }

    /**
     * overview : loads template
     *
     * detail : Loads template for view based on regions in config > template.php
     *
     * @param string $title
     * @param string $description
     * @param string $keywords
     * @param string $activenav
     * @return rendered template
     */
    private function loadTemplate($title, $description, $keywords, $activenav) {
        $this->template->add_js('resources/js/datatables.min.js');
        $this->template->add_js('resources/js/highlight.pack.js');
        $this->template->add_js('resources/js/ace/ace.js');
        $this->template->add_js('resources/js/ace/mode-json.js');
        $this->template->add_js('resources/js/ace/theme-tomorrow.js');

        $this->template->add_css('resources/css/general/style.css');
        $this->template->add_css('resources/css/datatables.min.css');
        $this->template->add_css('resources/css/hljs.tomorrow.css');

        $this->template->write('title', $title);
        $this->template->write('description', $description);
        $this->template->write('keywords', $keywords);
        $this->template->write('activenav', $activenav);
        $this->template->write('username', $this->authentication->getUsername());
        $this->template->write('userId', $this->authentication->getUserId());
        $this->template->write_view('sidebar', 'system_management/sidebar');
    }

    /**
     * Will redirect to another sidebar if the permission was disabled
     *
     * Created by Mark Andrew Mendoza (andrew.php.ph)
     */
    private function error_redirection(){
        $this->loadTemplate('Payment API Management', '', '', 'system');
        $systemUrl = $this->utils->activeSystemSidebar();
        $data['redirect'] = $systemUrl;

        $message = lang('con.usm01');
        $this->alertMessage(2, $message);

        $this->template->write_view('main_content', 'error_page', $data);
        $this->template->render();
    }

    /**
     * overview : view payment api
     *
     * @return rendered template
     */
    public function viewPaymentApi() {
        if (!$this->permissions->checkPermissions('payment_api')) {
            $this->error_redirection();
        } else {
            if (($this->session->userdata('sidebar_status') == NULL)) {
                $this->session->set_userdata(array('sidebar_status' => 'active'));
            }

            // sets the history for breadcrumbs
            if (($this->session->userdata('well_crumbs') == NULL)) {
                $this->session->set_userdata(array('well_crumbs' => 'active'));
            }

            $this->history->setHistory('header_system.system_word23', 'payment_api/viewPaymentApi');

            $user_id = $this->authentication->getUserId();
            $user = $this->users->getUserById($user_id);
            $data['currentUser'] = $user_id;
            $data['roles'] = $this->rolesfunctions->getAllRolesByUser($user_id, null, null);

            $data['const_unlocked'] = 1;
            $data['const_locked'] = 2;

            $paymentApis = $this->external_system->getAllSystemPaymentApi();

            $constants = get_defined_constants(true)['user'];
            foreach ($constants as $key => $value) {
                if (substr($key, -3) == 'API' && $key != 'SYSTEM_GAME_API') {
                    if (strrpos($key, 'PAYMENT')) {
                        $data['api_types'][lang('system.word95')]['id'] = SYSTEM_PAYMENT;
                        $data['api_types'][lang('system.word95')]['list'][$key] = $value;
                    }
                }
            }

            $data['can_view_secret'] = $this->users->isAuthorizedViewPaymentSecretUsers($user['username']);

            ksort($data['api_types'][lang('system.word95')]['list']);

            $data['paymentApis'] = json_decode(json_encode($paymentApis), true);

            $this->loadTemplate('System Management', '', '', 'system');
            $this->template->write_view('main_content', 'system_management/view_payment_api', $data);
            $this->template->render();
        }
    }

    /**
     * overview : get system types
     *
     * detail : Load thru ajax
     * @return array
     */
    public function getSystemTypes() {
        $array = array();
        $est = $this->config->item('external_system_types');
        for ($i = 0; $i < count($est); $i++) {
            if ($i == 0) {
                $array[0]['id'] = $est[0];
                $array[0]['system_type'] = lang('sys.game.api');
            }
            if ($i == 1) {
                $array[1]['id'] = $est[1];
                $array[1]['system_type'] = lang('sys.payment.api');
            }

        }
        $data['sytemTypes'] = $array;
        $arr = array('status' => 'success', 'data' => $data);
        echo json_encode($arr);
    }

    /**
     * overview : edit payment api
     *
     * @param  int         $paymentApiId
     * @return array  banktype row
     */
    public function editPaymentApi($paymentApiId) {
        $array = array();
        $est = $this->config->item('external_system_types');
        for ($i = 0; $i < count($est); $i++) {
            if ($i == 0) {
                $array[0]['id'] = $est[0];
                $array[0]['system_type'] = lang('sys.game.api');
            }
            if ($i == 1) {
                $array[1]['id'] = $est[1];
                $array[1]['system_type'] = lang('sys.payment.api');
            }

        }
        $data['sytemTypes'] = $array;
        $data['paymentApi'] = $this->external_system->getSystemById($paymentApiId);

        if ($data['paymentApi']->go_live) { # if secret info have been encrypted
            # not display secret info in extra info
            list($loaded, $managerName) = $this->utils->loadExternalSystemLib($paymentApiId);
            if ($loaded) {
                $data['secret_list'] = $secret_list = $this->$managerName->getSecretInfoList();

                $extra_info  = json_decode($data['paymentApi']->extra_info, true) ?: array();
                $sandbox_extra_info  = json_decode($data['paymentApi']->sandbox_extra_info, true) ?: array();

                foreach ($secret_list as $key) {
                    if (array_key_exists($key, $extra_info)) {
                       unset($extra_info[$key]);
                    }
                    if (array_key_exists($key, $sandbox_extra_info)) {
                        unset($sandbox_extra_info[$key]);
                    }
                }

                   $data['paymentApi']->extra_info = empty($extra_info) ? null : json_encode($extra_info, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
                $data['paymentApi']->sandbox_extra_info = empty($sandbox_extra_info) ? null : json_encode($sandbox_extra_info, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            }
        }

        $arr = array('status' => 'success', 'data' => $data);
        echo json_encode($arr, JSON_PRETTY_PRINT);
    }

    /**
     * overview : disable/able payment api
     *
     * detail : enables or disables payment api
     * @return array
     */
    public function disableAblePaymentApi() {
        $this->form_validation->set_rules('id', 'Id', 'trim|xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'trim|numeric|xss_clean');

        if ($this->form_validation->run() == false) {
            $arr = array('status' => 'error', 'msg' => validation_errors());
            echo json_encode($arr);

        } else {
            $paymentApiId = $this->input->post('id');
            $data = array(
                'status' => $this->input->post('status'),
            );

            if ($this->external_system->disableAblePaymentApi($data, $paymentApiId)) {
                $this->alertMessage(1, lang('sys.ga.succsaved'));
                $arr = array('status' => 'success');
                echo json_encode($arr);
            } else {
                $arr = array('status' => 'failed');
                echo json_encode($arr);
                $this->alertMessage(2, lang('sys.ga.erroccured'));
            }
        }
    }

    /**
     * overview : add payment api
     *
     * @return  array
     */
    public function addPaymentApi() {
        $this->form_validation->set_rules('new_id', 'System ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('system_name', 'System Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('note', 'Note', 'trim|xss_clean');
        $this->form_validation->set_rules('last_sync_datetime', 'Last Sync Datetime', 'trim|xss_clean');
        $this->form_validation->set_rules('last_sync_id', 'Last Sync Id', 'trim|xss_clean');
        $this->form_validation->set_rules('last_sync_details', 'Last Sync Details', 'trim|xss_clean');
        $this->form_validation->set_rules('system_type', 'System Type', 'trim|xss_clean');
        $this->form_validation->set_rules('category', 'Category', 'trim|xss_clean');
        $this->form_validation->set_rules('amount_float', 'Amount Float', 'trim|xss_clean');
        $this->form_validation->set_rules('live_url', 'Live Url', 'trim|xss_clean');
        $this->form_validation->set_rules('sandbox_url', 'Sanbox Url', 'trim|xss_clean');
        $this->form_validation->set_rules('live_key', 'Live Key', 'trim|xss_clean');
        $this->form_validation->set_rules('live_secret', 'Live Secret', 'trim|xss_clean');
        $this->form_validation->set_rules('sandbox_key', 'Sandbox key', 'trim|xss_clean');
        $this->form_validation->set_rules('sandbox_secret', 'Sandbox secret', 'trim|xss_clean');
        $this->form_validation->set_rules('live_mode', 'Live Mode', 'trim|xss_clean');
        $this->form_validation->set_rules('second_url', 'Second Url', 'trim|xss_clean');
        $this->form_validation->set_rules('sandbox_account', 'Sandbox Account', 'trim|xss_clean');
        $this->form_validation->set_rules('live_account', 'Live Account', 'trim|xss_clean');
        $this->form_validation->set_rules('system_code', 'System Code', 'trim|xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'trim|xss_clean');
        $this->form_validation->set_rules('class_name', 'Class Name', 'trim|xss_clean');
        $this->form_validation->set_rules('local_path', 'Local Path', 'trim|xss_clean');
        $this->form_validation->set_rules('manager', 'Manager', 'trim|xss_clean');
        $this->form_validation->set_rules('extra_info', 'Extra Info', 'trim|xss_clean');
        $this->form_validation->set_rules('sandbox_extra_info', 'Sandbox Extra Info', 'trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $arr = array('status' => 'error', 'msg' => validation_errors());
            echo json_encode($arr);
        } else {
            $data = array(
                'id' => $this->input->post('new_id'),
                'system_name' => $this->input->post('system_name'),
                'note' => $this->input->post('note'),
                'last_sync_datetime' => $this->input->post('last_sync_datetime'),
                'last_sync_id' => $this->input->post('last_sync_id'),
                'last_sync_details' => $this->input->post('last_sync_details'),
                'system_type' => $this->input->post('system_type'),
                'category' => $this->input->post('category'),
                'amount_float' => $this->input->post('amount_float'),
                'live_url' => $this->input->post('live_url'),
                'sandbox_url' => $this->input->post('sandbox_url'),
                'live_key' => $this->input->post('live_key'),
                'live_secret' => $this->input->post('live_secret'),
                'sandbox_key' => $this->input->post('sandbox_key'),
                'live_mode' => $this->input->post('live_mode'),
                'sandbox_secret' => $this->input->post('sandbox_secret'),
                'second_url' => $this->input->post('second_url'),
                'sandbox_account' => $this->input->post('sandbox_account'),
                'live_account' => $this->input->post('live_account'),
                'system_code' => $this->input->post('system_code'),
                'status' => $this->input->post('status'),
                'class_name' => $this->input->post('class_name'),
                'local_path' => $this->input->post('local_path'),
                'manager' => $this->input->post('manager'),
                'extra_info' => $this->input->post('extra_info'),
                'sandbox_extra_info' => $this->input->post('sandbox_extra_info'),
                'allow_deposit_withdraw' => $this->input->post('allow_deposit_withdraw')
            );

            if ($this->external_system->addPaymentApi($data)) {
                $this->alertMessage(1, lang('sys.ga.succsaved'));
                $arr = array('status' => 'success');
                echo json_encode($arr);
            } else {
                $arr = array('status' => 'failed');
                echo json_encode($arr);
                $this->alertMessage(2, lang('sys.ga.erroccured'));
            }
        }
    }

    /**
     * overview : update payment api
     *
     * @return array
     */
    public function updatePaymentApi() {
        $this->form_validation->set_rules('id', 'ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('new_id', 'ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('system_name', 'System Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('note', 'Note', 'trim|xss_clean');
        $this->form_validation->set_rules('last_sync_datetime', 'Last Sync Datetime', 'trim|xss_clean');
        $this->form_validation->set_rules('last_sync_id', 'Last Sync Id', 'trim|xss_clean');
        $this->form_validation->set_rules('last_sync_details', 'Last Sync Details', 'trim|xss_clean');
        $this->form_validation->set_rules('system_type', 'System Type', 'trim|xss_clean');
        $this->form_validation->set_rules('category', 'Category', 'trim|xss_clean');
        $this->form_validation->set_rules('amount_float', 'Amount Float', 'trim|xss_clean');
        $this->form_validation->set_rules('live_url', 'Live Url', 'trim|xss_clean');
        $this->form_validation->set_rules('sandbox_url', 'Sanbox Url', 'trim|xss_clean');
        $this->form_validation->set_rules('live_key', 'Live Key', 'trim|xss_clean');
        $this->form_validation->set_rules('live_secret', 'Live Secret', 'trim|xss_clean');
        $this->form_validation->set_rules('sandbox_key', 'Sandbox key', 'trim|xss_clean');
        $this->form_validation->set_rules('sandbox_secret', 'Sandbox secret', 'trim|xss_clean');
        $this->form_validation->set_rules('live_mode', 'Live Mode', 'trim|xss_clean');
        $this->form_validation->set_rules('second_url', 'Second Url', 'trim|xss_clean');
        $this->form_validation->set_rules('sandbox_account', 'Sandbox Account', 'trim|xss_clean');
        $this->form_validation->set_rules('live_account', 'Live Account', 'trim|xss_clean');
        $this->form_validation->set_rules('system_code', 'System Code', 'trim|xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'trim|xss_clean');
        $this->form_validation->set_rules('class_name', 'Class Name', 'trim|xss_clean');
        $this->form_validation->set_rules('local_path', 'Local Path', 'trim|xss_clean');
        $this->form_validation->set_rules('manager', 'Manager', 'trim|xss_clean');
        $this->form_validation->set_rules('extra_info', 'Extra Info', 'trim');
        $this->form_validation->set_rules('sandbox_extra_info', 'Sandbox Extra Info', 'trim');

        if ($this->form_validation->run() == false) {
            $arr = array('status' => 'error', 'msg' => validation_errors());
            echo json_encode($arr);
        } else {
            $paymentApiId = $this->input->post('id');
            $data = array(
                'id' => $this->input->post('new_id'),
                'system_name' => $this->input->post('system_name'),
                'note' => $this->input->post('note'),
                'last_sync_datetime' => $this->input->post('last_sync_datetime'),
                'last_sync_id' => $this->input->post('last_sync_id'),
                'last_sync_details' => $this->input->post('last_sync_details'),
                'system_type' => $this->input->post('system_type'),
                'category' => $this->input->post('category'),
                'amount_float' => $this->input->post('amount_float'),
                'live_url' => $this->input->post('live_url'),
                'sandbox_url' => $this->input->post('sandbox_url'),
                'live_key' => $this->input->post('live_key'),
                'live_secret' => $this->input->post('live_secret'),
                'sandbox_key' => $this->input->post('sandbox_key'),
                'live_mode' => $this->input->post('live_mode'),
                'sandbox_secret' => $this->input->post('sandbox_secret'),
                'second_url' => $this->input->post('second_url'),
                'sandbox_account' => $this->input->post('sandbox_account'),
                'live_account' => $this->input->post('live_account'),
                'system_code' => $this->input->post('system_code'),
                'status' => $this->input->post('status'),
                'class_name' => $this->input->post('class_name'),
                'local_path' => $this->input->post('local_path'),
                'manager' => $this->input->post('manager'),
                'extra_info' => $this->input->post('extra_info'),
                'sandbox_extra_info' => $this->input->post('sandbox_extra_info'),
                'allow_deposit_withdraw' => $this->input->post('allow_deposit_withdraw')
            );

            $payment_api = (array)$this->external_system->getSystemById($paymentApiId);
            if ($payment_api['go_live']) { # if secret info have been encrypted
                # do not modify the secret content
                list($loaded, $managerName) = $this->utils->loadExternalSystemLib($paymentApiId);
                if ($loaded) {
                    $secret_list = $this->$managerName->getSecretInfoList();

                    $origin_extra_info  = json_decode($payment_api['extra_info'], true) ?: array();
                    $origin_sandbox_extra_info  = json_decode($payment_api['sandbox_extra_info'], true) ?: array();

                    $extra_info  = json_decode($data['extra_info'], true) ?: array();
                    $sandbox_extra_info  = json_decode($data['sandbox_extra_info'], true) ?: array();

                    foreach ($secret_list as $key) {
                        if (array_key_exists($key, $payment_api)) {
                            $data[$key] = $payment_api[$key];
                        } else {
                            if (array_key_exists($key, $origin_extra_info)) {
                                $extra_info[$key] = $origin_extra_info[$key];
                            }
                            if (array_key_exists($key, $origin_sandbox_extra_info)) {
                                $sandbox_extra_info[$key] = $origin_sandbox_extra_info[$key];
                            }
                        }
                    }
                    $data['extra_info'] = empty($extra_info) ? null : json_encode($extra_info, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
                    $data['sandbox_extra_info'] = empty($sandbox_extra_info) ? null : json_encode($sandbox_extra_info, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
                }
            }

            if ($this->external_system->updatePaymentApi($data, $paymentApiId)) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('sys.ga.succsaved'));
                $arr = array('status' => 'success');
                echo json_encode($arr);
            } else {
                $arr = array('status' => 'failed');
                echo json_encode($arr);
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('sys.ga.erroccured'));
            }
        }
    }

    /**
     * overview : deletes payment api
     *
     * @return array
     */
    public function deletePaymentApi() {
        $ids = $this->input->post("forDeletes");
        if ($this->external_system->deletePaymentApi($ids)) {
            $this->alertMessage(1, lang('sys.ga.succsaved'));
            $arr = array('status' => 'success');
            echo json_encode($arr);
        } else {
            $arr = array('status' => 'failed');
            echo json_encode($arr);
            $this->alertMessage(2, lang('sys.ga.erroccured'));
        }
    }

    public function encryptPaymentApi($paymentApiId) {
        if ($this->isWrongPaymentApiKey()){
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Wrong Payment Api Key.'));
            return redirect('/payment_api/viewPaymentApi');
        }

        list($loaded, $managerName) = $this->utils->loadExternalSystemLib($paymentApiId);

        if ($loaded) {
            $payment_api = (array)$this->external_system->getSystemById($paymentApiId);
            if ($payment_api['go_live']) {
                $this->alertMessage(self::MESSAGE_TYPE_WARNING, lang('Already went live.'));
                return redirect('/payment_api/viewPaymentApi');
            }

            $secret_list = $this->$managerName->getSecretInfoList();

            $extra_info         = json_decode($payment_api['extra_info'], true) ?: array();
            $sandbox_extra_info = json_decode($payment_api['sandbox_extra_info'], true) ?: array();

            $data = array();
            foreach ($secret_list as $key) {
                if (array_key_exists($key, $payment_api) && !empty($payment_api[$key])) {
                    $data[$key] = $this->external_system->encryptSecrets($payment_api[$key]);
                } else {
                    if (array_key_exists($key, $extra_info) && !empty($extra_info[$key])) {
                        $extra_info[$key] = $this->external_system->encryptSecrets($extra_info[$key]);
                    }
                    if (array_key_exists($key, $sandbox_extra_info) && !empty($sandbox_extra_info[$key])) {
                        $sandbox_extra_info[$key] = $this->external_system->encryptSecrets($sandbox_extra_info[$key]);
                    }
                }
            }

            $data['extra_info'] = empty($extra_info) ? null : json_encode($extra_info, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            $data['sandbox_extra_info'] = empty($sandbox_extra_info) ? null : json_encode($sandbox_extra_info, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            $data['go_live'] = true;
            $data['go_live_date'] = $this->utils->getNowForMysql();

            if ($this->external_system->updatePaymentApi($data, $paymentApiId)) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $payment_api['system_name'].' '.lang('Successfully went live.'));
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Update Failed'));
            }
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Payment Class Not Found'));
        }

        return redirect('/payment_api/viewPaymentApi');
    }

    public function decryptPaymentApi($paymentApiId) {
        $this->load->model(['users']);
        $loggedUserId = $this->authentication->getUserId();

        $user = $this->users->getUserById($loggedUserId);
        $isAuth = $this->users->isAuthorizedViewPaymentSecretUsers($user['username']);
        if (!$isAuth) {
            return redirect('/payment_api/viewPaymentApi');
        }

        if ($this->isWrongPaymentApiKey()){
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Wrong Payment Api Key.'));
            return redirect('/payment_api/viewPaymentApi');
        }

        list($loaded, $managerName) = $this->utils->loadExternalSystemLib($paymentApiId);
        if ($loaded) {
            $payment_api = (array)$this->external_system->getSystemById($paymentApiId);
            if (!$payment_api['go_live']) {
                $this->alertMessage(self::MESSAGE_TYPE_WARNING, lang("Not go live yet."));
                return redirect('/payment_api/viewPaymentApi');
            }

            $secret_list = $this->$managerName->getSecretInfoList();
            $extra_info         = json_decode($payment_api['extra_info'], true) ?: array();
            $sandbox_extra_info = json_decode($payment_api['sandbox_extra_info'], true) ?: array();

            $data = array();
            foreach ($secret_list as $key) {
                if (array_key_exists($key, $payment_api) && !empty($payment_api[$key])) {
                    $data[$key] = $this->external_system->decryptSecrets($payment_api[$key]);
                } else {
                    if (array_key_exists($key, $extra_info) && !empty($extra_info[$key])) {
                        $extra_info[$key] = $this->external_system->decryptSecrets($extra_info[$key]);
                    }
                    if (array_key_exists($key, $sandbox_extra_info) && !empty($sandbox_extra_info[$key])) {
                        $sandbox_extra_info[$key] = $this->external_system->decryptSecrets($sandbox_extra_info[$key]);
                    }
                }
            }

            $data['extra_info'] = empty($extra_info) ? null : json_encode($extra_info, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            $data['sandbox_extra_info'] = empty($sandbox_extra_info) ? null : json_encode($sandbox_extra_info, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            $data['go_live'] = false;

            if ($this->external_system->updatePaymentApi($data, $paymentApiId)) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $payment_api['system_name'].' '.lang('Successfully decrypted.'));
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Update Failed'));
            }

        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Payment Class Not Found'));
        }

        return redirect('/payment_api/viewPaymentApi');
    }

    public function editSecretInfo() {
        $paymentApiId = $this->input->post('id');
        list($loaded, $managerName) = $this->utils->loadExternalSystemLib($paymentApiId);
        if ($loaded) {
            $payment_api = (array)$this->external_system->getSystemById($paymentApiId);
            if (!$payment_api['go_live']) {
                $arr = array('status' => 'failed', 'msg' => lang("Not go live yet."));
                echo json_encode($arr);
            } else {
                $secret_list = $this->$managerName->getSecretInfoList();
                $extra_info  = json_decode($payment_api['extra_info'], true) ?: array();
                $sandbox_extra_info  = json_decode($payment_api['sandbox_extra_info'], true) ?: array();

                $data = array();
                foreach ($secret_list as $key) {
                    if (array_key_exists($key, $payment_api)) {
                        $data["general"][$key] = $payment_api[$key];
                    } else {
                        $data["extra_info"][$key] = empty($extra_info[$key]) ? '' : $extra_info[$key];
                        $data["sandbox_extra_info"][$key] = empty($sandbox_extra_info[$key]) ? '' : $sandbox_extra_info[$key];
                    }
                }
                $arr = array('status' => 'success', 'secret' => $data);
                echo json_encode($arr);
            }
        } else {
            $arr = array('status' => 'failed', 'msg' => lang("Payment Class Not Found."));
            echo json_encode($arr);
        }
    }

    public function updateSecretInfo() {
        $this->form_validation->set_rules('api_id', 'Id', 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $arr = array('status' => 'error', 'msg' => validation_errors());
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Update Success'));
        } else {

            if ($this->isWrongPaymentApiKey()){
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Wrong Payment Api Key.'));
                return redirect('/payment_api/viewPaymentApi');
            }

            $post_data = $this->getInputGetAndPost();

            # parse post data
            $new_secret = array();
        	foreach ($post_data as $key => $value) {
        		if ($key == "api_id") {
        			$paymentApiId = $value;
        		} elseif (strpos($key, "extra_info-") === 0) {
        			$parsed_key = str_replace("extra_info-", "", $key);
        			$new_secret["extra_info"][$parsed_key] = $value;
        		} elseif (strpos($key, "sandbox_extra_info-") === 0) {
        			$parsed_key = str_replace("sandbox_extra_info-", "", $key);
        			$new_secret["sandbox_extra_info"][$parsed_key] = $value;
        		} else {
                    $new_secret[$key] = $value;
        		}
        	}

            list($loaded, $managerName) = $this->utils->loadExternalSystemLib($paymentApiId);
            if ($loaded) {

                $payment_api = (array)$this->external_system->getSystemById($paymentApiId);
                if (!$payment_api['go_live']) {
	                $this->alertMessage(self::MESSAGE_TYPE_WARNING, lang("Not go live yet."));
	                return redirect('/payment_api/viewPaymentApi');
	            } else {
                    $secret_list = $this->$managerName->getSecretInfoList();
                    $extra_info  = json_decode($payment_api['extra_info'], true) ?: array();
                    $sandbox_extra_info  = json_decode($payment_api['sandbox_extra_info'], true) ?: array();

                    $data = array();
                    foreach ($secret_list as $key) {
                        if (array_key_exists($key, $payment_api) && array_key_exists($key, $new_secret)) {
                        	if ($new_secret[$key] != $payment_api[$key]) {
                        		$data[$key] = $this->external_system->encryptSecrets($new_secret[$key]);
                        	}
                            if (empty($new_secret[$key])) {
                                $data[$key] = '';
                            }
                        } else {
                            if (array_key_exists($key, $new_secret["extra_info"])) {
	                        	if (empty($extra_info[$key]) || $new_secret["extra_info"][$key] != $extra_info[$key]) {
	                        		$extra_info[$key] = empty($new_secret["extra_info"][$key]) ? '' : $this->external_system->encryptSecrets($new_secret["extra_info"][$key]);
	                        	}
                            }
                            if (array_key_exists($key, $new_secret["sandbox_extra_info"])) {
	                        	if (empty($sandbox_extra_info[$key]) || ($new_secret["sandbox_extra_info"][$key] != $sandbox_extra_info[$key]) ) {
	                        		$sandbox_extra_info[$key] = empty($new_secret["sandbox_extra_info"][$key]) ? '' : $this->external_system->encryptSecrets($new_secret["sandbox_extra_info"][$key]);
	                        	}
                            }
                        }
                    }
                }

           	 	$data['extra_info'] = empty($extra_info) ? null : json_encode($extra_info, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            	$data['sandbox_extra_info'] = empty($sandbox_extra_info) ? null : json_encode($sandbox_extra_info, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            }

	        $this->external_system->updatePaymentApi($data, $paymentApiId);
        	$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Update Success'));
        	return redirect('/payment_api/viewPaymentApi');
        }
    }

    public function getDecryptedSecretInfo() {
        $this->form_validation->set_rules('id', 'Id', 'trim|xss_clean');
        $this->form_validation->set_rules('code', 'OTP Password', 'trim|xss_clean');

        $this->load->model(['users']);
        $loggedUserId = $this->authentication->getUserId();

        $user = $this->users->getUserById($loggedUserId);
        $isAuth = $this->users->isAuthorizedViewPaymentSecretUsers($user['username']);
        $isWrongPaymentApiKey = $this->isWrongPaymentApiKey();

        if ($this->form_validation->run() == false) {
            $arr = array('status' => 'error', 'msg' => validation_errors());
            echo json_encode($arr);
        } elseif(!$isAuth) {
            $arr = array('status' => 'error', 'msg' => "No permission");
            echo json_encode($arr);
        } elseif ($isWrongPaymentApiKey) {
            $arr = array('status' => 'error', 'msg' => "Wrong Payment Api Key.");
            echo json_encode($arr);
        } else {
            $paymentApiId = $this->input->post('id');
            $code = $this->input->post('code');

            if($this->checkOTP($code)){
                list($loaded, $managerName) = $this->utils->loadExternalSystemLib($paymentApiId);

                if ($loaded) {
                    $payment_api = (array)$this->external_system->getSystemById($paymentApiId);
                    if (!$payment_api['go_live']) {
                        $arr = array('status' => 'failed', 'msg' => lang("Not go live yet."));
                        echo json_encode($arr);
                    } else {
                        $secret_list = $this->$managerName->getSecretInfoList();
                        $extra_info  = json_decode($payment_api['extra_info'], true) ?: array();
                        $sandbox_extra_info  = json_decode($payment_api['sandbox_extra_info'], true) ?: array();

                        $data = array();
                        foreach ($secret_list as $key) {
                            if (array_key_exists($key, $payment_api)) {
                                $data["general"][$key] = $this->external_system->decryptSecrets($payment_api[$key]);
                            } else {
                                if (array_key_exists($key, $extra_info)) {
                                    $data["extra_info"][$key] = $this->external_system->decryptSecrets($extra_info[$key]);
                                }
                                if (array_key_exists($key, $sandbox_extra_info)) {
                                    $data["sandbox_extra_info"][$key] = $this->external_system->decryptSecrets($sandbox_extra_info[$key]);
                                }
                            }
                        }
                        $arr = array('status' => 'success', 'secret' => $data);
                        echo json_encode($arr);
                    }
                } else {
                    $arr = array('status' => 'failed', 'msg' => lang("Payment Class Not Found."));
                    echo json_encode($arr);
                }
            } else {
                $arr = array('status' => 'failed', 'msg' => lang("Wrong OTP Password."));
                echo json_encode($arr);
            }
        }
    }

    private function isWrongPaymentApiKey(){
        $key = $this->utils->getConfig('payment_api_key');
        if(mb_strlen($key, '8bit') !== 32) {
            return true;
        }
        return false;
    }

    private function checkOTP($code){
        $this->load->model(['users']);
        $loggedUserId = $this->authentication->getUserId();

        $user = $this->users->getUserById($loggedUserId);
        $rlt = $this->users->validateOTPCode($loggedUserId, $user['otp_secret'], $code);
        return $rlt['success'];
    }
}

/* End of file payment_api.php */
/* Location: ./application/controllers/payment_api.php */
