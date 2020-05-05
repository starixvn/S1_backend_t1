<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Class Country_management
 */
class Country_rules_management extends BaseController {

    const COUNTRY_RULES = 'country_rules';

    const ITEM_WWW_IP_WHITE_LIST= 'www_ip_white_list';
    const ITEM_WWW_IP_BLOCK_LIST= 'www_ip_block_list';

    public function __construct() {
        parent::__construct();

        $this->load->helper('url');
        $this->load->library(array('permissions', 'template', 'form_validation', 'report_functions', 'salt', 'ip_manager'));
        $this->load->model(array('country_rules'));

        $this->permissions->checkSettings();
        $this->permissions->setPermissions();
    }

    private function loadTemplate($title, $description, $keywords, $activenav) {
        $this->loadThirdPartyToTemplate('datatables');
        $this->template->add_js('resources/js/select2.full.js');
        $this->template->add_css('resources/css/select2.min.css');
        $this->template->add_css('resources/css/general/style.css');
        $this->template->write('title', $title);
        $this->template->write('description', $description);
        $this->template->write('keywords', $keywords);
        $this->template->write('activenav', $activenav);
        $this->template->write('username', $this->authentication->getUsername());
        $this->template->write('userId', $this->authentication->getUserId());
        $this->template->write_view('sidebar', 'system_management/sidebar');
    }

    private function error_access() {
        $this->loadTemplate('Country', '', '', 'system');
        $systemUrl = $this->utils->activeSystemSidebar();
        $data['redirect'] = $systemUrl;

        $message = lang('con.i01');
        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

        $this->template->write_view('main_content', 'error_page', $data);
        $this->template->render();
    }

    public function viewList() {
        if (!$this->permissions->checkPermissions(self::COUNTRY_RULES)) {
            $this->error_access();
        } else {
            $this->loadTemplate('Country Management', '', '', 'system');

            $this->template->add_js('resources/js/ace/ace.js');
            $this->template->add_js('resources/js/ace/mode-json.js');
            $this->template->add_js('resources/js/ace/theme-tomorrow.js');

            if (($this->session->userdata('sidebar_status') == NULL)) {
                $this->session->set_userdata(array('sidebar_status' => 'active'));
            }

            if (($this->session->userdata('well_crumbs') == NULL)) {
                $this->session->set_userdata(array('well_crumbs' => 'active', 'system_crumb' => 'active'));
            }

            $data=[
               'www_ip_white_list' => $this->country_rules->getWWWIpWhiteListJson(),
               'www_ip_block_list' => $this->country_rules->getWWWIpBlockListJson(),
            ];

            $this->template->write_view('main_content', 'system_management/view_country_list', $data);
            $this->template->render();
        }
    }

    public function save_white_list() {
        if (!$this->permissions->checkPermissions(self::COUNTRY_RULES)) {
            $this->error_access();
        }

        $white_list=json_decode($this->input->post('white_list'));
        if(empty($white_list)){
            $white_list=[];
        }
        //save to $this->operatorglobalsettings
        $this->operatorglobalsettings->syncSettingJson(self::ITEM_WWW_IP_WHITE_LIST, $white_list, 'template');

        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save system settings successfully'));
        redirect('country_rules_management/viewList');
    }

    public function save_block_list() {
        if (!$this->permissions->checkPermissions(self::COUNTRY_RULES)) {
            $this->error_access();
        }

        $block_list=json_decode($this->input->post('block_list'));
        if(empty($block_list)){
            $block_list=[];
        }
        //save to $this->operatorglobalsettings
        $this->operatorglobalsettings->syncSettingJson(self::ITEM_WWW_IP_BLOCK_LIST, $block_list, 'template');

        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save system settings successfully'));
        redirect('country_rules_management/viewList');
    }

    public function countryRulesList() {
        if (!$this->permissions->checkPermissions(self::COUNTRY_RULES)) {
            $this->error_access();
        }

        echo $this->country_rules->getCountryRules();
    }

    public function deleteCountryRules() {
        if (!$this->permissions->checkPermissions(self::COUNTRY_RULES)) {
            $this->error_access();
        }

        $this->returnJsonResult($this->country_rules->deleteCountryRules($this->input->post('countryIds')));
    }

    public function blockCountryRules() {
        if (!$this->permissions->checkPermissions(self::COUNTRY_RULES)) {
            $this->error_access();
        }

        $this->returnJsonResult($this->country_rules->blockCountryRules($this->input->post('countryIds'), $this->input->post('flag')));
    }

    public function enableAffiliateOrAgency() {
        if (!$this->permissions->checkPermissions(self::COUNTRY_RULES)) {
            $this->error_access();
        }
        $this->returnJsonResult($this->country_rules->enableAffiliateOrAgency($this->input->post('countryIds'), $this->input->post('status'), $this->input->post('field')));
    }

    public function enableWwwm() {
        if (!$this->permissions->checkPermissions(self::COUNTRY_RULES)) {
            $this->error_access();
        }
        $this->returnJsonResult($this->country_rules->enableWwwm($this->input->post('countryIds'), $this->input->post('status')));
    }

    public function countryList() {
        if (!$this->permissions->checkPermissions(self::COUNTRY_RULES)) {
            $this->error_access();
        }

        $this->returnJsonResult(unserialize(COUNTRY_ISO2));
    }

    public function addCountryRules() {
        if (!$this->permissions->checkPermissions(self::COUNTRY_RULES)) {
            $this->error_access();
        }

        $this->country_rules->addCountries($this->input->post());
    }

    public function countryRulesSetting() {
        if (!$this->permissions->checkPermissions(self::COUNTRY_RULES)) {
            $this->error_access();
        }

        $this->country_rules->setCountryRulesSetting($this->input->post('rulesMode'), $this->input->post('blockUrl'));
    }
}

/* End of file ip_management.php */
/* Location: ./application/controllers/country_management.php */
