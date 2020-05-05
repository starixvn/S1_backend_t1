<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Class Country_management
 */
class Super_Report_Management extends BaseController {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library(array('permissions', 'template', 'form_validation', 'report_functions', 'salt', 'ip_manager','data_tables'));

        $this->currency_list_super_report = $this->utils->getConfig('currency_list_super_report');

        $this->load->model('super_report');

        $this->permissions->checkSettings();
        $this->permissions->setPermissions();

    }

    private function loadTemplate($title, $description, $keywords, $activenav) {
        $this->loadThirdPartyToTemplate('datatables');
        $this->template->add_js('resources/js/report_management/report_management.js');
        $this->template->add_js('resources/js/select2.full.js');
        $this->template->add_css('resources/css/select2.min.css');
        $this->template->add_css('resources/css/general/style.css');

        $this->template->write('title', $title);
        $this->template->write('description', $description);
        $this->template->write('keywords', $keywords);
        $this->template->write('activenav', $activenav);
        $this->template->write('username', $this->authentication->getUsername());
        $this->template->write('userId', $this->authentication->getUserId());
        $this->template->write_view('sidebar', 'super_report_management/sidebar');
    }

    private function error_access() {
        $this->loadTemplate('Super Report', '', '', 'system');

        $message = lang('con.i01');
        $this->alertMessage(2, $message);

        $this->template->render();
    }

    public function index() {

        if (!$this->permissions->checkPermissions('super_report')) {
            return $this->error_access();
        }

        if(!$this->utils->isEnabledFeature('use_new_super_report')){
            return redirect('super_report_management/summary_report');
        }

        // $data['currency_list_super_report'] = $this->currency_list_super_report;
        //export report permission checking
        // $data['export_report_permission'] = $this->permissions->checkPermissions('export_super_summary_report');

        $this->loadNewTemplate('Super Report', '', '', 'super_report');
        $data=['donotLoadExtraCSS'=>true, 'donotLoadExtraJS'=>true];
        $this->template->write_view('main_content', 'super_report_management/home', $data);
        $this->template->render();

        // redirect('super_report_management/summary2_report');
    }

    public function summary_report() {

        if (!$this->permissions->checkPermissions('super_summary_report')) {
            $this->error_access();
        }else{
            $data['currency_list_super_report'] = $this->currency_list_super_report;
            //export report permission checking
            $data['export_report_permission'] = $this->permissions->checkPermissions('export_super_summary_report');

            $this->loadTemplate('Super Summary Report', '', '', 'super_report');
            $this->template->write_view('main_content', 'super_report_management/summary_report', $data);
            $this->template->render();
        }
    }

    public function player_report() {

        if (!$this->permissions->checkPermissions('super_player_report')) {
            $this->error_access();
        }
        $data['currency_list_super_report'] = $this->currency_list_super_report;
        //export report permission checking
        $data['export_report_permission'] = $this->permissions->checkPermissions('export_super_player_report');

        $this->loadTemplate('Super Player Report', '', '', 'super_report');
        $this->template->write_view('main_content', 'super_report_management/player_report', $data);
        $this->template->render();
    }

    public function games_report() {

        if (!$this->permissions->checkPermissions('super_game_report')) {
            $this->error_access();
        }
        $data['currency_list_super_report'] = $this->currency_list_super_report;
        //export report permission checking
        $data['export_report_permission'] = $this->permissions->checkPermissions('export_super_game_report');

        $this->loadTemplate('Super Games Report', '', '', 'super_report');
        $this->template->write_view('main_content', 'super_report_management/game_report',$data);
        $this->template->render();
    }

    public function payment_report() {

        if (!$this->permissions->checkPermissions('super_payment_report')) {
            $this->error_access();
        }
        $data['currency_list_super_report'] = $this->currency_list_super_report;
        //export report permission checking
        $data['export_report_permission'] = $this->permissions->checkPermissions('export_super_payment_report');

        $this->loadTemplate('Super Payment Report', '', '', 'super_report');
        $this->template->write_view('main_content', 'super_report_management/payment_report',$data);
        $this->template->render();
    }

    public function promotion_report() {

        if (!$this->permissions->checkPermissions('super_promotion_report')) {
            $this->error_access();
        }
        $data['currency_list_super_report'] = $this->currency_list_super_report;
        //export report permission checking
        $data['export_report_permission'] = $this->permissions->checkPermissions('export_super_promotion_report');

        $this->loadTemplate('Super Promotion Report', '', '', 'super_report');
        $this->template->write_view('main_content', 'super_report_management/promotion_report',$data);
        $this->template->render();
    }

    public function cashback_report() {

        if (!$this->permissions->checkPermissions('super_cashback_report')) {
            $this->error_access();
        }
        $data['currency_list_super_report'] = $this->currency_list_super_report;
        //export report permission checking
        $data['export_report_permission'] = $this->permissions->checkPermissions('export_super_cashback_report');

        $this->loadTemplate('Super Cashback Report', '', '', 'super_report');
        $this->template->write_view('main_content', 'super_report_management/cashback_report',$data);
        $this->template->render();
    }

    private function loadNewTemplate($title, $description, $keywords, $activenav) {

        // $this->template->set_template('super_report');
        $this->template->write('title', $title);
        $this->template->write('description', $description);
        $this->template->write('keywords', $keywords);
        $this->template->write('activenav', $activenav);
        $this->template->write('username', $this->authentication->getUsername());
        $this->template->write('userId', $this->authentication->getUserId());
    }


}