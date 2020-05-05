<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/modules/base_agency_controller.php';
require_once dirname(__FILE__) . '/modules/agent_bank_info_module.php';
require_once dirname(__FILE__) . '/modules/agency_settlement_module.php';
/**
 * Agency
 *
 * Agency Controller for OG System
 *
 */
class Agency extends Base_agency_controller {

    use agent_bank_info_module;
    use agency_settlement_module;

    const ACTION_NEW_DEPOSIT = 1;
    const ACTION_NEW_WITHDRAW = 2;
    const ACTION_TRANSFER_FROM_SW = 3;
    const ACTION_TRANSFER_TO_SW = 4;

    function __construct() {
        parent::__construct();
        $this->load->helper('agency');
        $this->load->library(array('agency_library', 'vipsetting_manager', 'template', 'form_validation', 'session', 'pagination', 'salt', 'email_setting','security'));
        $this->load->model(array('agency_model', 'player_model', 'wallet_model', 'transactions', 'common_token'));

        $this->initiateLang();
        $this->controller_name = 'agency';

        $login_by_player_token = $this->input->get_post('login_by_player_token');
        $this->utils->debug_log('login_by_player_token: '.$login_by_player_token);
        if(!empty($login_by_player_token)){
            $playerInfo = $this->common_token->getPlayerInfoByToken($login_by_player_token);

            if(!empty($playerInfo)){

                $agency_agent = $this->agency_model->get_agent_by_binding_player_id($playerInfo['playerId']);
                $this->_login_success($agency_agent);

                $this->utils->debug_log('login by player token');
            }
        }
    }

    /**
     * initiate Language
     *
     * @return  void
     */
    public function initiateLang() {
        $lang = $this->language_function->getCurrentLanguage();
        $langCode = $this->language_function->getLanguageCode($lang);
        $language = $this->language_function->getLanguage($lang);

        $this->load->vars('current_language_id', $lang);
        $this->load->vars('current_language_name', $language);

        //$this->utils->debug_log('INITIATELANG', $lang, $language);
        $this->lang->load($langCode, $language);

        $custom_lange = config_item('custom_lang');
        if((FALSE !== $custom_lange) && (file_exists(APPPATH . 'language/custom/' . $custom_lange . '/' . $language . '/custom_lang.php'))){
            $this->lang->load('custom', 'custom/' . $custom_lange . '/' . $language);
        }
    }

    /**
     * internal function to change agency lang
     * @param  string $language
     * @return array
     */
    private function change_agency_lang($language){
        $this->session->set_userdata('agency_lang', $language);
        $this->language_function->setCurrentLanguage($language);

        $agent_id = $this->session->userdata('agent_id');
        if ($agent_id) {
            $language_str = $this->language_function->getLanguage($language);
            $this->agency_model->update_agent($agent_id, array('language' => $language_str));
        }

        $message = lang('con.usm03');
        //$this->alertMessage(1, $message);
        $arr = array('status' => 'success');

        return $arr;
    }

    /**
     * change Language
     *
     * @return  void
     */
    public function changeLanguage($language) {

        $arr = $this->change_agency_lang($language);
        // echo json_encode($arr);
        $this->returnJsonResult($arr);
    }

    public function get_agent_id_from_session() {
        return $this->session->userdata('agent_id');
    }

    public function get_logged_agent_from_session(){

        $this->load->model(['agency_model']);
        $agent_id=$this->get_agent_id_from_session();

        $agent=null;
        if(!empty($agent_id)){

            $agent=$this->agency_model->get_agent_by_id($agent_id);

            // $agent_id;
            // if(!empty($agent)){

            // }
        }

        return $agent;
    }

    // load_template {{{2
    /**
     * Loads template for view based on regions in
     * config > template.php
     *
     */
    protected function load_template($title, $description, $keywords, $sys=null) {
        $this->utils->debug_log('agency load_template', $title);
        $this->template->set_template($this->utils->getAgencyCenterTemplate());
        $this->template->write('title', $title);
        $this->template->write('description', $description);
        $this->template->write('keywords', $keywords);

        $this->template->add_css('resources/css/agency_style.css');
        $this->template->add_js2($this->utils->jsUrl('agency.js'));

        // $this->template->add_js2($this->utils->jsUrl('datatable.js'));
        // $this->template->add_js2($this->utils->jsUrl('datatables.min.js'));
        // $this->template->add_js2($this->utils->jsUrl('datatables.bootstrap.js'));
        // $this->template->add_js2($this->utils->jsUrl('jquery.dataTables.min.js'));
        // $this->template->add_js2($this->utils->jsUrl('dataTables.responsive.min.js'));
        //$this->template->add_js2($this->utils->jsUrl('bootstrap.js'));


        $this->template->add_js('resources/js/datatables.min.js');

        //$this->template->add_css('resources/css/general/style.css');
        $this->template->add_css('resources/css/datatables.min.css');
        // $this->template->add_css2($this->utils->cssUrl('datatables.min.css'));
        // $this->template->add_css2($this->utils->cssUrl('datatables.bootstrap.css'));
        // $this->template->add_css2($this->utils->cssUrl('jquery.dataTables.css'));
        // $this->template->add_css2($this->utils->cssUrl('dataTables.responsive.css'));
        // $this->template->add_css2($this->utils->cssUrl('bootstrap.css'));
        $this->utils->loadDatatables($this->template);
        $data = array();
        $agent_id = $this->session->userdata('agent_id');
        if ($agent_id) {
            $data['_agent'] = $this->agency_model->get_agent_by_id($agent_id);
        }
        $data['readonlyLogged']=$this->isAgencyReadonlySubaccountLogged();
        $data['readonly_sub_account']=$this->session->userdata('readonly_sub_account');

        $this->template->write_view('nav_right', 'agency/navigation', $data);
    }
    // load_template }}}2

    // switchTheme {{{2
    public function switchTheme($theme) {
        if(!$this->check_login_status()){
            return;
        }

        $this->session->set_userdata('agency_theme', $theme);

        $referred_from = $this->session->userdata('current_url');
        redirect($referred_from, 'refresh');
    }
    // switchTheme }}}2
    // index {{{2
    /**
     * Index Page of Agency Page
     *
     * @return  void
     */
    public function index() {
        $this->load_template(lang('Agency System'), '', '');

        if (!$this->check_login()) {
            $data['availableCurrencyList']=$this->utils->getAvailableCurrencyList();
            $data['isCurrencyDomain']=$this->utils->isCurrencyDomain();

            $data['activeCurrencyKeyOnMDB']=$this->utils->getActiveCurrencyKeyOnMDB();
            $data['useCaptchaOnLogin'] = $this->config->item('captcha_agency_login');
            $data['current_language_name'] = $this->load->get_var('current_language_name');
            $this->utils->debug_log('login data', $data);

            $this->load->view('agency/login', $data);

        } else {
            redirect('agency/dashboard');
        }
    }
    // index }}}2
    // common }}}1

    // login and logout {{{1
    // check_login {{{2
    /**
     * check if agent is login
     *
     * @return  bool
     */
    protected function check_login() {
        return $this->isLoggedAgency();
    }
    // check_login }}}2

    // check_login_status {{{2
    /**
     *  check login status
     *
     *  @param
     *  @return
     */
    public function check_login_status(&$agent_id=null) {
        if (!$this->isLoggedAgency($agent_id)) {
            redirect('/');
            return false;
        }
        return true;
    } // check_login_status  }}}2
    // login {{{2
    /**
     * login
     *
     * @return  void
     */
    public function login($adminToken = null, $agent_id = null) {
        $username = '';
        $password = '';
        $readonlySubAccount= null;
        $autoLogin = false;
        if ($adminToken) {
            $autoLogin = $this->validateAdminToken($adminToken, 'login_as_agent');
        }

        if (!$autoLogin) {
            if ($this->input->post()) {
                if ($this->config->item('captcha_agency_login')) {
                    $this->form_validation->set_rules('login_captcha', lang('label.captcha'), 'callback_check_captcha');
                    if ($this->form_validation->run() == false) {
                        $this->alertMessage(2, validation_errors()); // Wrong Captcha
                        redirect('agency');
                    }
                }
            }

            $username = $this->input->post('username');
            $readonlySubAccount = $this->input->post('readonly_username');
            if(!empty($readonlySubAccount)){
                //password for readonly
                $password = $this->input->post('password');
            }else{
                $password = $this->salt->encrypt($this->input->post('password'), $this->getDeskeyOG());
            }
        } else {
            //check admin session first
            if ($agent_id) {
                $agent = $this->agency_model->get_password_by_id($agent_id);
                $username = $agent['agent_name'];
                $pw = $this->salt->decrypt($agent['password'], $this->getDeskeyOG());
                $password = $this->salt->encrypt($pw, $this->getDeskeyOG());
            }
        }
        $result=null;
        $error=null;
        if($this->utils->isEnabledFeature('enabled_readonly_agency') && !empty($readonlySubAccount)){
            $result = $this->agency_model->loginByReadonlySubAccount($username, $readonlySubAccount, $password, $error);
        }else{
            $result = $this->agency_model->login($username, $password);
        }

        if ($result) {
            if ($result['status'] == 'frozen') {
                $message = lang('The agent is frozen!');
                $this->alertMessage(self::MESSAGE_TYPE_WARNING, $message); // Not yet activated
                redirect('/agency');
                /*
            } else if ($result['status'] == 'suspended') {
                $message = lang('The agent is suspended! Please activate it firstly.');
                $this->alertMessage(1, $message); // Account frozen
                redirect('agency');
                 */
            } else {
                $agent_details = $this->agency_model->get_agent_by_id($result['agent_id']);

                $language = $this->input->post('language');
                if ( ! isset($language) || empty($language)) {
                    switch ($agent_details['language']) {
                        case 'english':
                            $language = Language_function::INT_LANG_ENGLISH;
                            break;
                        case 'chinese':
                            $language = Language_function::INT_LANG_CHINESE;
                            break;
                        default:
                            $language = $this->language_function->getCurrentLanguage();
                            break;
                    }
                }
                $this->utils->debug_log('UI_language', $language);

                $this->_login_success($agent_details, $language, $readonlySubAccount);

                // record action in agency log {{{3
                $log_params = array(
                    'action' => 'account_login',
                    'link_url' => site_url('agency/login'),
                    'done_by' => $this->session->userdata('agent_name'),
                    'done_to' => $this->session->userdata('agent_name'),
                    'details' => 'Agent '. $result['agent_name'] . ' login at '. date('Y-m-d H:i:s'),
                );
                $this->agency_library->save_action($log_params);
                // record action in agency log }}}3

                $this->change_agency_lang($language);

                $message = lang('Login Successfully');
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); // Successful login
                $url='/agency';
                //if login by all
                if($this->utils->isEnabledMDB()){
                    $defaultCurrency=$agent_details['default_currency'];
                    if(empty($defaultCurrency)){
                        $defaultCurrency=$agent_details['currency'];
                    }
                    $defaultCurrency=strtolower($defaultCurrency);
                    if(!$this->utils->isActiveCurrency($defaultCurrency)){
                        if($this->utils->isSuperModeOnMDB()){
                            //not active and active is super then auto switch to default currency
                            $this->switch_active_currency_for_logged($defaultCurrency);
                            // $this->utils->appendDBToUrl($url, $defaultCurrency);
                        }
                    }

                }

                redirect($url);
            }
        }else if(!empty($error)){
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $error);
            redirect('/agency');
        }

        $message = lang('Incorrect login information');
        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message); // Login Details Incorrect
        redirect('/agency');
    } // login }}}2

    public function login_by_player_token(){
        $this->load->model(['common_token']);

        $token = $this->input->get_post('token');
        $iniframe = $this->input->get_post('iniframe');

        $result = [
            'status' => 'success',
            'msg' => NULL
        ];
        $iframe_act = 'agency_login_response';

        $agent_id = NULL;
        $agent_name = NULL;

        $playerInfo = $this->common_token->getPlayerInfoByToken($token);

        if(empty($playerInfo)){
            $error_message = lang('Incorrect login information');

            $result['status'] = 'error';
            $result['msg'] = $error_message;

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }elseif($iniframe){
                return $this->load->view('agency/iframe_callback', [
                    'origin' => '*',
                    'result' => $this->utils->encodeJson([
                        'act' => $iframe_act,
                        'data' => $result
                    ]),
                ]);
            }else{
                redirect('agency');
            }
        }

        $agency_agent = $this->agency_model->get_agent_by_binding_player_id($playerInfo['playerId']);

        if(empty($agency_agent)){
            // todo
            return ;
        }

        $login_result = $this->isLoggedAgency($agent_id, $agent_name);

        if($login_result && $agent_id === $agency_agent['agent_id']){
            $result['data']['agent_id'] = $agent_id;

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }elseif($iniframe){
                return $this->load->view('agency/iframe_callback', [
                    'origin' => '*',
                    'result' => $this->utils->encodeJson([
                        'act' => $iframe_act,
                        'data' => $result
                    ]),
                ]);
            }else{
                redirect('agency');
            }
        }

        $result['data']['agent_id'] = $agent_id = $agency_agent['agent_id'];

        $this->_login_success($agency_agent);

        if($this->input->is_ajax_request()){
            return $this->returnJsonResult($result);
        }elseif($iniframe){
            return $this->load->view('agency/iframe_callback', [
                'origin' => '*',
                'result' => $this->utils->encodeJson([
                    'act' => $iframe_act,
                    'data' => $result
                ]),
            ]);
        }else{
            redirect('agency');
        }
    }

    /**
     * logout
     *
     * @return  void
     */
    public function logout() {
        $data = array(
            'last_logout_time' => date('Y-m-d H:i:s'),
        );
        $this->agency_model->update_agent($this->session->userdata('agent_id'), $data);

        $agent_name = $this->session->userdata('agent_name');

        // $this->session->unset_userdata(array(
        //     'agent_name' => null,
        //     'agent_id' => null,
        //     'agency_lang' => null,
        //     'agent_status' => null,
        //     'agent_level' => null,
        //     'can_have_players' => null,
        //     'can_have_sub_agent' => null,
        //     'agency_lang'=>null,
        // ));

        $this->session->updateLoginId('agent_id', '');

        // record action in agency log {{{3
        $log_params = array(
            'action' => 'account_logout',
            'link_url' => site_url('agency/logout'),
            'done_by' => $agent_name,
            'done_to' => $agent_name,
            'details' => 'Agent '. $agent_name . 'logout at '. date('Y-m-d H:i:s'),
        );
        $this->agency_library->save_action($log_params);
        // record action in agency log }}}3

        $this->session->sess_destroy();

        redirect('agency');
    }

    /**
     * CAPTCHA LOADER
     *
     * @return img
     */
    public function captcha() {
        $active = $this->config->item('si_active');
        $allsettings = array_merge($this->config->item($active), $this->config->item('si_general'), ['namespace' => 'agency_login']);

        $this->load->library('captcha/securimage');
        $img = new Securimage($allsettings);
        $img->show($this->config->item('si_background'));
    }
    /**
     * CAPTCHA Validator
     * @param string
     * @return bool
     */
    public function check_captcha($val) {
        $rlt = false;

        $active = $this->config->item('si_active');
        $allsettings = array_merge($this->config->item($active), $this->config->item('si_general'), ['namespace' => 'agency_login']);

        //check captcha first
        $this->load->library('captcha/securimage');
        $securimage = new Securimage($allsettings);

        $rlt = $securimage->check($this->input->post('login_captcha'));
        $this->form_validation->set_message('check_captcha', lang('error.captcha'));

        return $rlt;
    }

    protected function getDeskeyOG() {
        return $this->config->item('DESKEY_OG');
    }

    /**
     *  validate input for structure
     *
     *  @param
     *  @return string json string for message
     */
    public function structure_validation_ajax() {
        foreach($this->fields_rules as $field => $rule) {
            $label = $this->labels_array[$field];
            $this->utils->debug_log('field', $field, 'label', $label, 'rule', $rule, 'val', $this->input->post($field));
            if ($this->input->post($field)) {
                $this->form_validation->set_rules($field, $label, $rule);
                $this->field_validation();
            }
        }
    }

    /**
     *  display agent information
     *
     *  @param  int agent_id
     *  @return
     */
    public function agent_information($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $session_agent_id = $this->get_agent_id_from_session();
        $all_ids = $this->agency_model->get_sub_agent_ids_by_parent_id($session_agent_id);
        $all_ids[] = $session_agent_id;
        $this->utils->debug_log('ALL IDs', $all_ids, $agent_id);
        if (!in_array($agent_id, $all_ids)) {
            redirect('agency/agent_information/' . $session_agent_id);
        } else {
            $this->load_template(lang('Agent Information'), '', '', 'agency');
            $this->template->add_js($this->utils->getAgencyCmsUrl('resources/js/agency_tracking_link.js'));
            $this->initAgentInfo($agent_id, $data);

            $this->addBoxDialogToTemplate();
            $this->addJsTreeToTemplate();

            //$this->template->write_view('nav_right', 'agency/navigation');
            $this->template->write_view('main_content', 'agency/agent_information', $data);
            $this->template->render();
        }

    } // agent_information  }}}2
    // sub_agents_list {{{2
    /**
     *  Create and Display sub agent list
     *
     *  @param
     *  @return
     */
    public function sub_agents_list() {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

            if($this->session->userdata('can_have_sub_agent')) {
                $data['parent_id'] = $this->session->userdata('agent_id');

                $this->load_template(lang('Sub Agent List'), '', '', '');
                $this->template->add_js('resources/js/bootstrap-switch.min.js');
                $this->template->add_css('resources/css/bootstrap-switch.min.css');
                $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/amcharts.js'));
                $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/serial.js'));
                $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/light.js'));

                $this->addJsTreeToTemplate();
                //$this->template->write_view('nav_right', 'agency/navigation');
                $this->template->write_view('main_content', 'agency/agent_list', $data);
                $this->template->render();
            } else if($this->session->userdata('can_have_players')) {
                redirect('agency/players_list', 'refresh');
            } else {
                $agent_id = $this->session->userdata('agent_id');
                redirect('agency/agent_information/'.$agent_id, 'refresh');
            }

    } // sub_agents_list  }}}2
    // create_sub_agent {{{2
    /**
     *  create a sub-agent for given agent
     *
     *  @param  int current agent_id
     *  @return
     */
    public function create_sub_agent($agent_id, $vip_levels='') {

        if ( ! $this->check_login_status()) {
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        // $data['min_agent_level'] = $this::MIN_ALLOWED_LEVEL;
        // $data['max_agent_level'] = $this::MAX_ALLOWED_LEVEL;
        $data['vipgrouplist'] = $this->vipsetting_manager->getVipGroupList();
        $this->load->model('game_type_model');
        $data['game_types'] = $this->game_type_model->getGameTypesArray();

        $this->initCreateSubAgentInfo($data, $agent_id, $vip_levels);

        /*
        $agent_details = $this->agency_model->get_agent_by_id($agent_id);
        // if (empty($vip_levels)) {
        //     $vip_levels = $agent_details['vip_levels'];
        // }

        $this->utils->debug_log('CREATE_SUB_AGENT details', $agent_details);

        $data['parent_id'] = $agent_details['agent_id'];
        $data['parent_name'] = $agent_details['agent_name'];
        $data['conditions'] = $this->safeLoadParams(array(
            'parent_id' => $agent_details['agent_id'],
            'parent_name' => $agent_details['agent_name'],
            'structure_id' => '',
            'agent_name' => '',
            'password' => '',
            'confirm_password' => '',
            'tracking_code' => '',
            'currency' => $agent_details['currency'],
            'status' => $agent_details['status'],
            'credit_limit' => $agent_details['credit_limit'],
            'available_credit' => $agent_details['available_credit'],
            // 'agent_level' => $agent_details['agent_level'] + 1,
            // 'agent_level_name' => $agent_details['agent_level_name'],
            // 'vip_level' => explode(',', $agent_details['vip_level']),
            'agent_level' => $agent_details['agent_level'] + 1,
            'agent_level_name' => $agent_details['agent_level_name'],
            'vip_level' => $agent_details['vip_level'],
            'rev_share' => $agent_details['rev_share'],
            'rolling_comm' => $agent_details['rolling_comm'],
            'rolling_comm_basis' => $agent_details['rolling_comm_basis'],
            'total_bets_except' => $agent_details['total_bets_except'],
            'can_have_sub_agent' => $agent_details['can_have_sub_agent'],
            'can_have_players' => $agent_details['can_have_players'],
            'show_bet_limit_template' => $agent_details['show_bet_limit_template'],
            'show_rolling_commission' => $agent_details['show_rolling_commission'],
            'can_view_agents_list_and_players_list' => $agent_details['can_view_agents_list_and_players_list'],
            // 'settlement_period' => explode(',', $agent_details['settlement_period']),
            'settlement_period' => $agent_details['settlement_period'],
            'start_day' => $agent_details['settlement_start_day'],
            'before_credit' => '0',
            'agent_count' => '1',
            'vip_groups' => $agent_details['vip_groups'],
            'admin_fee' => number_format($agent_details['admin_fee'],2),
            'transaction_fee' => number_format($agent_details['transaction_fee'],2),
            'bonus_fee' => number_format($agent_details['bonus_fee'],2),
            'cashback_fee' => number_format($agent_details['cashback_fee'],2),
            'note' => '', // $agent_details['note'],
            'show_bet_limit_template' => $agent_details['show_bet_limit_template'],
            'show_rolling_commission' => $agent_details['show_rolling_commission'],
        ));

        $this->utils->debug_log('CREATE_SUB_AGENT: conditions', $data['conditions']);

        // $data['min_allowed_level'] = $this::MIN_ALLOWED_LEVEL;
        // $data['max_allowed_level'] = $this::MAX_ALLOWED_LEVEL;
        $data['fields'] = $this->fields_array;
        $data['labels'] = $this->labels_array;
        $data['agency_player_rolling_settings']=$this->utils->getConfig('agency_player_rolling_settings');

        $data['game_platform_settings']['game_platform_list'] = array();
        $agent_game_platforms = $this->agency_model->get_agent_game_platforms($agent_id);
        $game_platforms_and_types = $this->agency_model->get_game_platforms_and_types();
        foreach ($agent_game_platforms as $agent_game_platform) {
            $game_platform_id = $agent_game_platform['game_platform_id'];
            if (isset($game_platforms_and_types[$game_platform_id])) {
                $data['game_platform_settings']['game_platform_list'][$game_platform_id] = $game_platforms_and_types[$game_platform_id];
            }
        }

        $agent_game_types = $this->agency_model->get_agent_game_types($agent_id);
        foreach ($agent_game_types as &$agent_game_type) {
            $agent_game_type['rolling_comm'] = $agent_game_type['rolling_comm_out'];
            $agent_game_type['rolling_comm_out'] = NULL;
        }

        $data['game_types'] = $this->game_type_model->getGameTypesArray();
        $data['game_platform_settings']['conditions']['game_platforms'] = array_column($agent_game_platforms, NULL, 'game_platform_id');
        $data['game_platform_settings']['conditions']['game_types'] = array_column($agent_game_types, NULL, 'game_type_id');
        $data['is_agent'] = TRUE;
        $data['form_url'] = site_url('agency/verify_agent/' . $agent_id);
        // $data['vip_levels'] = $this->agency_model->get_vip_levels();
         */

        $this->load_template(lang('Create Sub Agent'), '', '', 'agency');
        $this->template->add_js('resources/js/bootstrap-switch.min.js');
        $this->template->add_css('resources/css/bootstrap-switch.min.css');
        $this->addJsTreeToTemplate();

        // $this->template->write_view('main_content', 'agency/create_sub_agent', $data);
        $this->template->write_view('main_content', 'includes/common_agent_form', $data);
        $this->template->render();
    } // create_sub_agent  }}}2
    // agent_add_players {{{2
    /**
     *  batch add new players for an agent
     *
     *  @param  int agent_id
     *  @return void
     */
    public function agent_add_players($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if(!$this->utils->isEnabledFeature('enable_create_player_in_agency')){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $data = array();
        $type_code = $this->player_model->getBatchCode();
        if (!empty($type_code)) {
            $x = explode('-', $type_code['typeCode']);
            $data['type_code'] = $x['0'] . '-' . sprintf("%06s", ($x['1'] + 1));
        } else {
            $data['type_code'] = "OG-000001";
        }
        $agent_details = $this->agency_model->get_agent_by_id($agent_id);
        $agent_name = $agent_details['agent_name'];
        $data['parent_agent_name'] = $agent_name;
        $data['agent_id'] = $agent_id;
        $data['registered_by'] = Player_model::REGISTERED_BY_AGENCY_CREATED;

        $labels_array = array(
            //'count' => lang('Count'),
            'name' => lang('Name'),
            'password' => lang('Password'),
            'rolling_comm' => lang('Rolling Comm'),
        );
        $fields_array = array();
        foreach($labels_array as $field=>$label) {
            $fields_array[] = $field;
        }
        $data['fields'] = $fields_array;
        $data['labels'] = $labels_array;
        $upline = $this->agency_model->get_upline($agent_id, false);

        $this->db->select('*');
        $this->db->select('IF(agent_id = '.$agent_id.', default_template, 0) default_template', false);
        if ($upline) {
            $this->db->where('(agent_id', $agent_id);
            $this->db->or_where_in('(agent_id', $upline);
            $this->db->where('public_to_downline = 1))');
        } else {
            $this->db->where('agent_id', $agent_id);
        }
        $this->db->where('status', 1);
        $this->db->order_by('template_name', 'asc');
        $query = $this->db->get('bet_limit_template_list');

        $data['bet_limit_templates'] = $query->result_array(); # TODO: MOVE TO MODEL
        // agent_add_players
        $data['agency_player_rolling_settings']=$this->utils->getConfig('agency_player_rolling_settings');

        $is_new = true;
        $this->get_game_comm_settings($data, $agent_id, 'player', $is_new);
        $data['game_platform_settings']['is_player'] = true;
        $data['panel_heading'] = lang('Add Players');
        $this->load_template(lang('Agent Add Players'), '', '', 'agency');
        $this->template->write_view('main_content', 'includes/agency_player_form', $data);
        $this->template->render();
        //$this->load->view('players/ajax_add_account_process', $data);
    } // agent_add_players  }}}2
    // verify_add_players {{{2
    /**
     * Validates and verifies inputs
     * of the end user and will add batch account
     *
     *
     * @return  redirect page
     */
    public function verify_add_players($game_platform_id = null) {
        //change to default
        if(!$this->check_login_status()){
            return;
        }

        if(!$this->utils->isEnabledFeature('enable_create_player_in_agency')){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        if(!empty($game_platform_id)){
            # TODO: config enable feature
            $api = $this->utils->loadExternalSystemLibObject($game_platform_id);
            if( ! $api || $api->isDisabled()){
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Game is disabled'));
                return redirect('agency');
            }
        }
        # TODO: config enable feature
        // $api = $this->utils->loadExternalSystemLibObject($game_platform_id);
        // if( ! $api || $api->isDisabled()){
        //     return redirect('agency');
        // }

        $this->form_validation->set_rules('username', lang('Name'),
            'trim|required|min_length['. $this->utils->getConfig('default_min_size_username') . ']|max_length['. $this->utils->getConfig('default_max_size_username') . ']|alpha_numeric|xss_clean|callback_check_player_name');
        $this->form_validation->set_rules('password', lang('Password'), 'trim|required|min_length['. $this->utils->getConfig('default_min_size_password') . ']|max_length['. $this->utils->getConfig('default_max_size_password') . ']');
        $this->form_validation->set_rules('rolling_comm', lang('Rolling Comm'),
            'trim|numeric|xss_clean|callback_check_rolling_comm');

        $agent_id = $this->input->post('agent_id');

        if ($this->form_validation->run() == false) {
            $message = validation_errors();
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            return redirect('agency/agent_add_players/' . $agent_id);
        } else {
            $name = $this->input->post('username');
            $agent_name = $this->input->post('agent_name');
            $bet_limit_template_id = $this->input->post('bet_limit_template_id');

            $batch = false;
            $batch_add_players = $this->input->post('batch_add_players');
            if ($batch_add_players == '1') {
                $count = $this->input->post('count');
                $this->utils->debug_log('verify_add_players: name count ', $name, $count);

                $checkBatchExist = $this->player_model->checkBatchExist($name);
                if ($checkBatchExist) {
                    $message = lang('con.plm21') . " " . $name . ". " . lang('con.plm22');
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                    redirect('agency/players_list');
                }

                $details = 'Batch Create '. $count .' players under agent '. $agent_name;
                $batch = true;
            } else {
                $details = 'Create player '. $name.' under agent '. $agent_name;
            }
            $this->load->model('player_model');

            $thePost = $this->input->post();
            $player_ids = $this->player_model->register($thePost, $batch);

            if ( ! is_array($player_ids)) {
                $player_ids = array($player_ids);
            }
            if(!empty($player_ids)){
                foreach ($player_ids as $playerId) {
                    //sync
                    $this->load->model(['multiple_db_model']);
                    $rlt=$this->multiple_db_model->syncPlayerFromCurrentToOtherMDB($playerId, true);
                    $this->utils->debug_log('syncPlayerFromCurrentToOtherMDB', $rlt);
                }
            }

            if ($player_ids && $bet_limit_template_id) {

                $bet_limit_template_status = 0;

                if ( ! $batch || $this->config->item('update_bet_limit_on_batch_create')) {

                    foreach ($player_ids as $player_id) {
                        $player_name = $this->player_model->getUsernameById($player_id);
                        $template = $this->db->get_where('bet_limit_template_list', array('id' => $bet_limit_template_id))->row_array();
                        $betLimit = json_decode($template['bet_limit_json'], true);
                        $result = $api->getBetLimit($player_name);
                        $game_ids = $result['gameIds'];
                        foreach ($game_ids as $game_id) {
                            $params = array();
                            $initial = substr($game_id, 0, 1);
                            $params['gameId'] = $game_id;
                            foreach ($betLimit as $key => $value) {
                                if ($initial == substr(strtoupper($key), 0, 1)) {
                                    $params[$key] = $value;
                                }
                            }
                            $result = $api->updateBetLimit($player_name, $params);
                        }
                    }

                    $bet_limit_template_status = 1;

                }

                $this->player_model->updatePlayers($player_ids, array('bet_limit_template_id' => $bet_limit_template_id, 'bet_limit_template_status' => $bet_limit_template_status));

            }
            if($player_ids) {
                $game_platforms = $this->input->post('game_platforms');
                $game_types = $this->input->post('game_types');
                foreach ($player_ids as $player_id) {
                    $this->agency_model->add_game_comm_settings($game_platforms, $game_types, $player_id, 'player');
                }
            }

            // record action in agency log {{{3
            $log_params = array(
                'action' => 'create_players',
                'link_url' => site_url('agency/agent_add_players'),
                'done_by' => $this->session->userdata('agent_name'),
                'done_to' => $name,
                'details' => $details,
            );
            $this->agency_library->save_action($log_params);
            // record action in agency log }}}3
            $message = lang('con.plm23') . " " . $this->input->post('count') . " " . lang('con.plm24');
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
            redirect("agency/players_list");
        }
    }
    // verify_add_players }}}2
    // check_player_name {{{2
    /**
     *  check whether agent name is unique
     *
     *  @param  string
     *  @return bool
     */
    public function check_player_name() {
        $batch_add_players = $this->input->post('batch_add_players');
        $this->utils->debug_log('CHECK_PLAYER_NAME: batch_add_players', $batch_add_players);
        $name = $this->input->post('username');
        if ($batch_add_players == '1') {
            if (empty($this->input->post('count'))) {
                $this->form_validation->set_message('check_player_name', lang('Count') . lang(' is required!'));
                return false;
            } else {
                $count = $this->input->post('count');
                $this->utils->debug_log('CHECK_PLAYER_NAME: count', $count);
                if ($count <= 0 || $count > 20) {
                    $this->form_validation->set_message('check_player_name', lang('Count') . lang(' must be >= 1 and <= 20!'));
                    return false;
                }
                for ($i = 1; $i <= $count; $i++) {
                    $player_name = $name . $i;
                    if ($this->player_model->usernameExist($player_name)) {
                        $this->form_validation->set_message('check_player_name',
                            lang('Player Username') . ' '. $player_name .lang(' has been used!'));
                        return false;
                    }
                }
            }
        } else {
            $player_name = $name;
            if ($this->player_model->usernameExist($player_name)) {
                $this->form_validation->set_message('check_player_name',
                    lang('Player Username') . ' '. $player_name .lang(' has been used!'));
                return false;
            }
        }
        return true;
    } // check_player_name  }}}2
    // add_player_validation_ajax {{{2
    /**
     *  validate input for add_player
     *
     *  @param
     *  @return string json string for message
     */
    public function add_player_validation_ajax() {
        $labels_array = array(
            'name' => lang('Name'),
            'password' => lang('Password'),
            'rolling_comm' => lang('Rolling Comm'),
        );
        $fields_rules = array(
            'name' => 'trim|required|min_length[2]|max_length[12]|alpha_numeric|xss_clean|callback_check_player_name',
            'password' => 'trim|required|min_length[6]|max_length[12]',
            'rolling_comm' => 'trim|required|numeric|xss_clean|callback_check_rolling_comm',
        );
        foreach($fields_rules as $field => $rule) {
            $label = $labels_array[$field];
            $this->utils->debug_log('field', $field, 'label', $label, 'rule', $rule, 'val', $this->input->post($field));
            if ($this->input->post($field)) {
                $this->form_validation->set_rules($field, $label, $rule);
                $this->field_validation();
            }
        }
    } // add_player_validation_ajax  }}}2
    // edit_player {{{2
    /**
     *  batch add new players for an agent
     *
     *  @param  int player_id
     *  @return void
     */
    public function edit_player($player_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->utils->isEnabledFeature('enable_create_player_in_agency')){
            return show_error('No permission', 403);
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->load->model('agency_player_details');

        $data = array();
        $agent_id = $this->session->userdata('agent_id');
        $agent_details = $this->agency_model->get_agent_by_id($agent_id);
        $agent_name = $agent_details['agent_name'];
        $data['parent_agent_name'] = $agent_name;
        $data['agent_id'] = $agent_id;

        $data['player_id'] = $player_id;
        $data['player_details'] = $this->player_model->getPlayerSignupInfoByAgentId($player_id, $agent_id);
        $data['player_account_info'] = $this->player_model->getPlayerAccountInfo($player_id);
        $data['password'] = $this->player_model->getPasswordById($player_id);
        $data['base_credit'] = $this->agency_player_details->base_credit_read($player_id, $agent_id);
        $data['registered_by'] = $data['player_details']['registered_by'];

        $data['fields'] = [];
        $data['labels'] = [];
        $data['is_edit'] = true;

        $this->get_game_comm_settings($data, $player_id, 'player');
        $data['game_platform_settings']['is_player'] = true;

        $this->utils->debug_log('agent edit_player GAME_PLATFORMS_SETTEINGS: ', $data['game_platform_settings']);
        $data['panel_heading'] = lang('Edit Player');
        $this->load_template(lang('Agent Edit Players'), '', '', 'agency');
        //$this->template->write_view('main_content', 'players/edit_player', $data);
        $this->template->write_view('main_content', 'includes/agency_player_form', $data);
        $this->template->render();
        //$this->load->view('players/ajax_add_account_process', $data);
    } // edit_player  }}}2
    // verify_edit_player {{{2
    /**
     * Validates and verifies inputs
     * of the end user and will add batch account
     *
     *
     * @return  redirect page
     */
    public function verify_edit_player($player_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->utils->isEnabledFeature('enable_create_player_in_agency')){
            return show_error('No permission', 403);
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->load->model('agency_player_details');

        $this->form_validation->set_rules('password', lang('Password'), 'trim|required|min_length[6]|max_length[12]');
        //$this->form_validation->set_rules('rolling_comm', lang('Rolling Comm'),
        //    'trim|required|numeric|xss_clean|callback_check_rolling_comm');
        $this->form_validation->set_rules('base_credit', lang('Base Credit'),
            'trim|required|numeric|xss_clean');

        if ($this->form_validation->run() == false) {
            $agent_id = $this->input->post('agent_id');
            $this->edit_player($player_id);
        } else {
            $this->load->library('salt');

            $name = $this->input->post('name');
            $agent_name = $this->input->post('agent_name');
            $agent_id = $this->input->post('agent_id');
            $password = $this->input->post('password');
            $password_crypt = $this->salt->encrypt($password, $this->getDeskeyOG());

            $base_credit = $this->input->post('base_credit');

            $this->player_model->startTrans();

            $data = [];

            if($this->utils->isEnabledFeature('enable_reset_player_password_in_agency')){
                $data['password']=$password_crypt;
                $this->player_model->updatePlayer($player_id, $data);
            }

            $data = array(
                'language' => $this->input->post('language'),
            );
            $this->player_model->updatePlayerdetails($player_id, $data);
            $this->agency_player_details->base_credit_store($player_id, $agent_id, $base_credit);

            $game_platforms = $this->input->post('game_platforms');
            $game_types = $this->input->post('game_types');
            $is_update = true;
            $this->agency_model->add_game_comm_settings($game_platforms, $game_types, $player_id, 'player', $is_update);

            $succ = $this->player_model->endTransWithSucc();
            if (!$succ) {
                throw new Exception('Sorry, update player information failed.');
            }

            // record action in agency log {{{3
            $details = 'Edit Player'. ' ' . $name.' under agent '. $agent_name;
            $log_params = array(
                'action' => 'edit_player',
                'link_url' => site_url('agency/edit_player/'. $player_id),
                'done_by' => $this->session->userdata('agent_name'),
                'done_to' => $name,
                'details' => $details,
            );
            $this->agency_library->save_action($log_params);
            // record action in agency log }}}3
            //
            $message = lang('Successfully updated player information');
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
            redirect("agency/player_information/". $player_id, "refresh");
        }
    }

    /**
     *  update a agent and redirect to agent list
     *
     *  @return
     */
    public function verify_update_agent() {

        if ( ! $this->check_login_status()) {
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $agent_id = $this->input->post('agent_id');

        $controller_name=null;
        if(!$this->isEditAgencyPermission($agent_id, $controller_name)){
            return $this->show_privilege_error();
        }

        $this->agent_form_rules();
        $this->form_validation->set_rules('available_credit', lang('Available Credit'), '');

        // Tracking code has separate update form
        // $this->form_validation->set_rules('tracking_code', lang('Tracking Code'),
        //     'trim|required|min_length[4]|max_length[20]|alpha_numeric|callback_check_unique_tracking_code');

        if ($this->form_validation->run() == false) {
            $message = validation_errors();
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message); //will set and send message to the user
            $this->edit_agent($agent_id);
        } else {

            $old_agent = $this->agency_model->get_agent_by_id($agent_id);

            $this->update_agent($agent_id);

            $agent_details = $this->agency_model->get_agent_by_id($agent_id);

            //$this->saveUpdateAgentLog($old_agent, $agent_details);

            $modified_fields = $this->check_modified_fields($old_agent, $agent_details);
            $log_params = array(
                'action' => 'modify_agent',
                'link_url' => site_url('agency/edit_agent/' . $agent_id),
                'done_by' => $this->session->userdata('agent_name'),
                'done_to' => $old_agent['agent_name'],
                'details' => lang('Edit Agent'). ' ' . $old_agent['agent_name']. ': '. $modified_fields,
            );
            $this->agency_library->save_action($log_params);

            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save successfully')); //will set and send message to the user
            redirect('agency/sub_agents_list');
        }
    }

    /**
     * Check modified fields on player info
     *
     * @param   int
     * @param   array
     * @return  string
     */
    public function check_modified_fields($old_data, $new_data) {
        $diff = array_diff_assoc($new_data, $old_data);

        foreach ($diff as $key => $value) {
            $changes[lang('reg.fields.' . $key) ?: $key] = [
                'old' => $old_data[$key],
                'new' => $new_data[$key],
            ];
        }

        ksort($changes);

        $output = '<ul>';
        foreach ($changes as $key => $value) {
            $output .= "<li>{$key}:<br><code>Old: {$value['old']}</code><br><code>New: {$value['new']}</code></li>";
        }
        $output .= '</ul>';

        return $output;
    }

    /**
     *  edit an existed agent
     *
     *  @param  int agent_id
     *  @return
     */
    public function edit_agent($agent_id) {

        if ( ! $this->check_login_status()) {
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        //permission of agent id and init
        $agent_details=$this->initEditAgentInfo($data, $agent_id);
        if(empty($agent_details)){
            $this->utils->error_log('agent privilege error '.$agent_id, $data);
            return $this->show_privilege_error();
        }

        // $logged_agent=$this->get_logged_agent_from_session();
        // if(empty($logged_agent)){
        //     //return error
        //     return $this->show_privilege_error();
        // }
        // $this->load->model(['game_type_model','agency_model']);

        // $agent_details = $this->agency_model->get_agent_by_id($agent_id);
        //check parent id
        // $parent_id=$agent_details['parent_id'];
        // if($logged_agent['agent_id']!=$parent_id){
        //     $this->utils->error_log('agent_id', $agent_id, 'parent_id', $parent_id, 'logged_agent', $logged_agent['agent_id']);
        //     return $this->show_privilege_error();
        // }

        // $data['min_agent_level'] = $this::MIN_ALLOWED_LEVEL;
        // $data['max_agent_level'] = $this::MAX_ALLOWED_LEVEL;
        // $data['vipgrouplist'] = $this->vipsetting_manager->getVipGroupList();
        // $data['game_types'] = $this->game_type_model->getGameTypesArray();
        //$this->utils->debug_log('game_types', $data['game_types'], count($data['game_types']));
        // $data['agency_player_rolling_settings']=$this->utils->getConfig('agency_player_rolling_settings');
        // $this->utils->debug_log('EDIT_AGENT details', $agent_details);

        // $old_pass = $this->agency_library->old_password_format($agent_details['password'], $this::MAX_PASSWORD_LEN);

        // if($check) {
        //     $data['check_info'] = lang('Please check agent information again');
        // } else {
        //     $data['check_info'] = '';
        // }
        // $data['check'] = $check;

        // if (empty($vip_levels)) {
        //     $vip_levels = $agent_details['vip_levels'];
        // }
        // $this->utils->debug_log('EDIT_AGENT: conditions', $data['conditions']);

        // $data['agent_id'] = $agent_id;
        // if($check) {
        //     $data['check_info'] = lang('Please check agent information again');
        // } else {
        //     $data['check_info'] = '';
        // }
        // $data['check'] = $check;

        // $data['min_allowed_level'] = $this::MIN_ALLOWED_LEVEL;
        // $data['max_allowed_level'] = $this::MAX_ALLOWED_LEVEL;
        // $data['fields'] = $this->fields_array;
        // $data['labels'] = $this->labels_array;

        // $agent_game_platforms = $this->agency_model->get_agent_game_platforms($agent_id);
        // $game_platforms_and_types = $this->agency_model->get_game_platforms_and_types();

        // $data['game_platform_settings']['game_platform_list'] = array();

        // if (isset($agent_details['parent_id'])) {

        //     $parent_game_platforms = $this->agency_model->get_agent_game_platforms($agent_details['parent_id']);


        //     foreach ($parent_game_platforms as $parent_game_platform) {
        //         $game_platform_id = $parent_game_platform['game_platform_id'];
        //         if (isset($game_platforms_and_types[$game_platform_id])) {
        //             $data['game_platform_settings']['game_platform_list'][$game_platform_id] = $game_platforms_and_types[$game_platform_id];
        //         }
        //     }

        // } else {
        //     $data['game_platform_settings']['game_platform_list'] = $game_platforms_and_types;
        // }

        // $agent_game_types = $this->agency_model->get_agent_game_types($agent_id);

        // $data['game_types'] = $this->game_type_model->getGameTypesArray();
        // $data['game_platform_settings']['conditions']['game_platforms'] = array_column($agent_game_platforms, NULL, 'game_platform_id');
        // $data['game_platform_settings']['conditions']['game_types'] = array_column($agent_game_types, NULL, 'game_type_id');
        // $data['is_agent'] = TRUE;
        // $data['is_edit'] = TRUE;
        // $data['form_url'] = site_url('agency/verify_update_agent/'. $data['check']);
        // $data['vip_levels'] = $this->agency_model->get_vip_levels();

        $this->load_template(lang('Edit Agent'), '', '', 'agency');
        // $this->template->add_js('resources/js/bootstrap-switch.min.js');
        // $this->template->add_css('resources/css/bootstrap-switch.min.css');
        // $this->addJsTreeToTemplate();
        // $this->template->write_view('main_content', 'agency/edit_agent', $data);
        $this->template->write_view('main_content', 'includes/common_agent_form', $data);
        $this->template->render();
    }

    /**
     *  player_vip_levels is required
     *
     *  @param
     *  @return
     */
    public function check_player_vip_levels() {
        $selected_vip_levels = $this->input->post('selected_vip_levels');
        $this->utils->debug_log('selected_vip_levels', $selected_vip_levels, explode(',', $selected_vip_levels));
        if ($selected_vip_levels == '') {
            $this->form_validation->set_message('check_player_vip_levels',
                lang('VIP Level') . ' ' . lang('is required'));
            return false;
        }
        return true;
    } // check_player_vip_levels  }}}2
    // check_agent_settlement_period {{{2
    /**
     *  agent_settlement_period is >=0.00 and <= 90.00
     *
     *  @param
     *  @return
     */
    public function check_agent_settlement_period() {
        $settlement_period = $this->input->post('settlement_period');
        $this->utils->debug_log('settlement_period', $settlement_period);
        if ($settlement_period == '') {
            $this->form_validation->set_message('check_agent_settlement_period',
                lang('Settlement Period') . ' ' . lang('is required'));
            return false;
        }
        $start_day = '';
        if ($settlement_period == 'Weekly') {
            $start_day = $this->input->post('start_day');
            $this->utils->debug_log('start_day', $start_day);
            if ($start_day == '') {
                $this->form_validation->set_message('check_agent_settlement_period',
                    lang('Please select start day for weekly settlement'));
                return false;
            }
        }
        return true;
    } // check_agent_settlement_period  }}}2
    // add_new_agent {{{2
    /**
     *  Add a new agent into DB table agency_agents
     *
     *  @param
     *  @return agent ID
     */
    private function add_new_agent($current_agent) {

        if (empty($current_agent)) {
            throw new Exception(lang('Sorry, save agent failed.'));
        }

        // $agent_types = $this->input->post('agent_type');

        // $rolling_comm_basis = $this->input->post('rolling_comm_basis');
        // $total_bets_except = '';
        // if ('total_bets' == $rolling_comm_basis) {
        //     $total_bets_except = $this->input->post('except_game_type');
        // }
        // //$this->utils->debug_log($rolling_comm_basis);
        // //$this->utils->debug_log($total_bets_except);

        // $settlement_period = $this->input->post('settlement_period');
        // //$this->utils->debug_log($settlement_period);
        // //$this->utils->debug_log(implode(",", $settlement_period));
        // $start_day = '';
        // if (in_array('Weekly', $settlement_period)) {
        //     $start_day = $this->input->post('start_day');
        // }
        //$this->utils->debug_log($start_day);

        $today = date("Y-m-d H:i:s");

        //$vip_levels = $this->input->post('vip_level');

        // $selected_vip_levels = $this->input->post('selected_vip_levels');
        // $this->utils->debug_log('selected_vip_levels', $selected_vip_levels, explode(',', $selected_vip_levels));
        // list($player_vip_groups, $player_vip_levels) = $this->agency_library->get_player_vip_info(explode(',',$selected_vip_levels));
        // $this->utils->debug_log('vip groups levels', $player_vip_groups, $player_vip_levels);

        // $agent_level=  $current_agent['agent_level']+1; //current logged agent level+1;

        // $parent_id = $current_agent['agent_id'];
        $password = $this->utils->encodePassword($this->input->post('password'));
        $available_credit = $this->input->post('available_credit');
        $parent_id = $current_agent['agent_id'];
        $status = $this->input->post('status');
        $active = $status == 'active' ? 1:0;

        $agent_types = $this->input->post('agent_type');
        $data = array(
            'parent_id'                 => $parent_id,
            'agent_name'                => $this->input->post('agent_name'),
            'password'                  => $password,
            'tracking_code'             => $this->input->post('tracking_code'),
            'currency'                  => $current_agent['currency'],
            'credit_limit'              => $this->input->post('credit_limit'),
            'status'                    => $status,
            'active'                    => $active,
            'agent_level'               => $current_agent['agent_level'] + 1,
            'can_have_sub_agent' => in_array('can-have-sub-agents', $agent_types)? 1:0,
            'can_have_players' => in_array('can-have-players', $agent_types)? 1:0,
            'can_view_agents_list_and_players_list' => in_array('can-view-agents-list-and-players-list', $agent_types)? 1:0,
            'show_bet_limit_template' => in_array('show-bet-limit-template', $agent_types)? 1:0,
            'show_rolling_commission' => in_array('show-rolling-commission', $agent_types)? 1:0,
            'vip_level'                 => $this->input->post('vip_level'),
            'settlement_period'         => $this->input->post('settlement_period'),
            'settlement_start_day'      => $this->input->post('start_day'),
            'created_on'                => $today,
            'updated_on'                => $today,
            'note'                      => $this->input->post('note'),

            'admin_fee'                 => $this->input->post('admin_fee'),
            'transaction_fee'           => $this->input->post('transaction_fee'),
            'bonus_fee'                 => $this->input->post('bonus_fee'),
            'cashback_fee'              => $this->input->post('cashback_fee'),
            'min_rolling_comm'              => $this->input->post('min_rolling_comm'),
        );

        $agent_id = null;
        $controller = $this;
        $succ = $this->lockAndTransForAgencyCredit($parent_id, function()
            use ($controller, &$agent_id, $parent_id, $available_credit, $current_agent, $data) {

            $succ = false;

            if ($current_agent['available_credit'] >= $available_credit) {

                $agent_id = $this->agency_model->add_agent($data);

                $succ = ! empty($agent_id);

                if ($succ && $parent_id && $available_credit > 0) {

                    $succ = $this->transactions->createAgentToSubAgentTransaction($parent_id, $agent_id, $available_credit, 'on create sub-agent');

                }

                /*
                if ($succ && $game_platforms = $this->input->post('game_platforms')) {
                    $game_platforms = array_filter($game_platforms, function(&$game_platform, $game_platform_id) use ($agent_id) {
                        $enabled = $game_platform['enabled'];
                        if ($enabled) {
                            $game_platform['game_platform_id'] = $game_platform_id;
                            $game_platform['agent_id'] = $agent_id;
                            unset($game_platform['enabled']);
                        }
                        return $enabled;
                    }, ARRAY_FILTER_USE_BOTH );
                    $this->db->insert_batch('agency_agent_game_platforms', $game_platforms);
                }
                if ($succ && $game_types = $this->input->post('game_types')) {
                    $game_types = array_filter($game_types, function(&$game_platform, $game_type_id) use ($agent_id) {
                        //$enabled = ! empty($game_platform['rolling_comm_basis']);
                        //$enabled = isset($game_platform['rev_share']) && isset($game_platform['rolling_comm_basis']);
                        $enabled = isset($game_platform['pattern_id']);
                        //if ($enabled) {
                            $game_platform['game_type_id'] = $game_type_id;
                            $game_platform['agent_id'] = $agent_id;
                        //}
                        return $enabled;
                    }, ARRAY_FILTER_USE_BOTH );
                    $this->db->insert_batch('agency_agent_game_types', $game_types);
                } */
                if ($succ) {
                    $game_platforms = $this->input->post('game_platforms');
                    $game_types = $this->input->post('game_types');
                    $this->agency_model->add_game_comm_settings($game_platforms, $game_types, $agent_id, 'agent');
                }
            } else {
                $controller->utils->error_log('no enought credit from agent', $current_agent['available_credit'], 'sub agent', $available_credit);
            }

            return $succ;
        });

        if ( ! $succ) {
            throw new Exception(lang('Sorry, save agent failed.'));
        }

        return $agent_id;

    } // add_new_agent  }}}2
    // get_vip_level_names {{{2
    /**
     *  get vip level names according to given array of vip level ids
     *
     *  @param  array vip levle ids
     *  @return string ',' separated vip level names
     */
    private function get_vip_level_names($vip_levels) {
        $vip_level_names = array();
        foreach($vip_levels as $vip_level_id) {
            $vip_level_names[] = $this->input->post('vip_level_name_'.$vip_level_id);
        }

        return implode(',', $vip_level_names);
    } // get_vip_level_names  }}}2
    // verify_agent {{{2
    /**
     *  insert an agent into table agency_agents
     *
     *  @param
     *  @return
     */
    public function verify_agent() {
        if ( ! $this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $logged_agent = $this->get_logged_agent_from_session();
        $parent_id = $this->input->post('parent_id');

        $this->utils->debug_log('parent_id', $parent_id, 'logged_agent', $logged_agent, 'agent id from session', $this->get_agent_id_from_session());

        if (empty($logged_agent)) {
            return $this->show_privilege_error();
        }

        $this->agent_form_rules();
        $this->form_validation->set_rules('tracking_code', lang('Tracking Code'),
             'trim|required|min_length[4]|max_length[20]|alpha_numeric|callback_check_unique_tracking_code');
        $this->form_validation->set_rules('agent_name', lang('Agent Name'), 'trim|required|min_length[2]|max_length[12]|alpha_numeric|is_unique[agency_agents.agent_name]');

        if ($this->form_validation->run() == false) {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Failed to create agent'));
            return $this->create_sub_agent($parent_id);
        } else {

            $username=$this->input->post('agent_name');
            $agent_id=null;
            $success=$this->utils->globalLockAgencyRegistration($username, function()
                    use(&$agent_id, $parent_id, $username){
                $parent_agent = $this->agency_model->get_agent_by_id($parent_id);
                $agent_id = $this->add_new_agent($parent_agent);
                $this->utils->debug_log('agent_id', $agent_id);
                $success=!empty($agent_id);
                if($success){
                    $this->syncAgentCurrentToMDB($agent_id, false);
                }
                return $success;
            });

            if(!$success){
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Failed to create agent'));
                return $this->create_sub_agent($parent_id);
            }

            $agent_details = $this->agency_model->get_agent_by_id($agent_id);

            // record action in agency log {{{3
            if ($parent_id > 0) {
                $action = 'create_sub_agent';
                $link_url = site_url('agency/create_sub_agent') . '/' . $parent_id;
            }
            $log_params = array(
                'action' => $action,
                'link_url' => $link_url,
                'done_by' => $this->session->userdata('agent_name'),
                'done_to' => $agent_details['agent_name'],
                'details' => 'Create agent '. $agent_details['agent_name'],
            );
            $this->agency_library->save_action($log_params);
            // record action in agency log }}}3
            $this->utils->debug_log($agent_details);

            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save successfully')); //will set and send message to the user
            redirect("agency/sub_agents_list");
        }
    }

    public function activate_agent($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        # OGP-4271
        $session_agent_id = $this->get_agent_id_from_session();
        if ( ! $this->agency_model->is_upline($agent_id, $session_agent_id)) {
            $this->alertMessage(2, lang('Failed to Activate Agent!'));
            redirect('agency/sub_agents_list');
            return;
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            //active agent
            if ($this->agency_model->activate($agent_id, $this->getLoggedInAgentUsername(), false)) {

                $message = lang('Activated');
                $this->utils->debug_log($agent_id, $message);
                $this->alertMessage(1, $message); //will set and send message to the user
            } else {
                $message = lang('Failed to Activate Agent!');
                $this->alertMessage(2, $message); //will set and send message to the user

            }
            redirect("agency/agent_information/" . $agent_id);
        // }
    } // activate_agent }}}2
    // inactivate_agent {{{2
    public function inactivate_agent($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        # OGP-4271
        $session_agent_id = $this->get_agent_id_from_session();
        if ( ! $this->agency_model->is_upline($agent_id, $session_agent_id)) {
            $this->alertMessage(2, lang('Failed to Inactivate Agent!'));
            redirect('agency/sub_agents_list');
            return;
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            //inactivate agent
            if ($this->agency_model->inactivate($agent_id)) {
                $message = lang('Inactivated');
                $this->utils->debug_log($agent_id, $message);
                $this->alertMessage(1, $message); //will set and send message to the user
            } else {
                $message = lang('Failed to Inactivate Agent!');
                $this->alertMessage(2, $message); //will set and send message to the user

            }
            redirect("agency/agent_information/" . $agent_id);
        // }
    } // inactivate_agent }}}2
    // suspend_agent {{{2
    public function suspend_agent($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        # OGP-4271
        $session_agent_id = $this->get_agent_id_from_session();
        if ( ! $this->agency_model->is_upline($agent_id, $session_agent_id)) {
            $this->alertMessage(2, lang('Failed to Suspend Agent!'));
            redirect('agency/sub_agents_list');
            return;
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            //suspend agent
            $controller=$this;

            $success=$this->lockAndTrans(Utils::LOCK_ACTION_AGENCY_STATUS, $agent_id, function() use ($controller, $agent_id){

                return $controller->agency_model->suspend($agent_id, $this->getLoggedInAgentUsername(), false);
            });

            if ($success) {
                $message = lang('Suspended');
                $this->utils->debug_log($agent_id, $message);
                $this->alertMessage(1, $message); //will set and send message to the user
            } else {
                $message = lang('Failed to Suspend Agent!');
                $this->alertMessage(2, $message); //will set and send message to the user

            }
            redirect("agency/agent_information/" . $agent_id);
        // }
    } // suspend_agent }}}2
    // freeze_agent {{{2
    public function freeze_agent($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        # OGP-4271
        $session_agent_id = $this->get_agent_id_from_session();
        if ( ! $this->agency_model->is_upline($agent_id, $session_agent_id)) {
            $this->alertMessage(2, lang('Failed to Freeze Agent!'));
            redirect('agency/sub_agents_list');
            return;
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            //freeze agent
            if ($this->agency_model->freeze($agent_id, $this->getLoggedInAgentUsername(), false)) {
                $message = lang('Frozen');
                $this->utils->debug_log($agent_id, $message);
                $this->alertMessage(1, $message); //will set and send message to the user
            } else {
                $message = lang('Failed to Freeze Agent!');
                $this->alertMessage(2, $message); //will set and send message to the user
            }
            redirect("agency/agent_information/" . $agent_id);
        // }
    } // freeze_agent }}}2
    // activate_agent_array {{{2
    public function activate_agent_array() {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $session_agent_id = $this->get_agent_id_from_session();

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            //active agent
            $agents = $_POST['agent_ids'];
            $arr = explode(',', $agents);
            $this->utils->debug_log('activate_agent_array', $agents, $arr);
            $cnt = 0;
            foreach($arr as $agent_id) {
                # OGP-4271
                if ($this->agency_model->is_upline($agent_id, $session_agent_id) && $this->agency_model->activate($agent_id, $this->getLoggedInAgentUsername(), false)) {
                    $cnt++;
                }
            }
            if ($cnt == count($arr)) {
                $message = lang('Activated'). ' ' . $cnt . ' ' . lang('Agent');
                $this->alertMessage(1, $message); //will set and send message to the user
            } else {
                $message = lang('Failed to Activate Agent!');
                $this->alertMessage(2, $message); //will set and send message to the user
            }
            redirect("agency/sub_agents_list" , 'refresh');
        // }
    } // activate_agent_array }}}2
    // suspend_agent_array {{{2
    public function suspend_agent_array() {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $session_agent_id = $this->get_agent_id_from_session();

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            $agents = $_POST['agent_ids'];
            $arr = explode(',', $agents);
            $this->utils->debug_log('suspend_agent_array', $agents, $arr);
            $cnt = 0;
            foreach($arr as $agent_id) {
                # OGP-4271
                if ($this->agency_model->is_upline($agent_id, $session_agent_id) && $this->agency_model->suspend($agent_id, $this->getLoggedInAgentUsername(), false)) {
                    $cnt++;
                }
            }
            if ($cnt == count($arr)) {
                $message = lang('Suspended'). ' ' . $cnt . ' ' . lang('Agent');
                $this->alertMessage(1, $message); //will set and send message to the user
            } else {
                $message = lang('Failed to Suspend Agent!');
                $this->alertMessage(2, $message); //will set and send message to the user
            }
            redirect("agency/sub_agents_list" , 'refresh');
        // }
    } // suspend_agent_array }}}2
    // freeze_agent_array {{{2
    public function freeze_agent_array() {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $session_agent_id = $this->get_agent_id_from_session();

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            $agents = $_POST['agent_ids'];
            $arr = explode(',', $agents);
            $this->utils->debug_log('freeze_agent_array', $agents, $arr);
            $cnt = 0;
            foreach($arr as $agent_id) {
                # OGP-4271
                if ($this->agency_model->is_upline($agent_id, $session_agent_id) && $this->agency_model->freeze($agent_id, $this->getLoggedInAgentUsername(), false)) {
                    $cnt++;
                }
            }
            if ($cnt == count($arr)) {
                $message = lang('Froze'). ' ' . $cnt . ' ' . lang('Agent');
                $this->alertMessage(1, $message); //will set and send message to the user
            } else {
                $message = lang('Failed to Freeze Agent!');
                $this->alertMessage(2, $message); //will set and send message to the user
            }
            redirect("agency/sub_agents_list" , 'refresh');
        // }
    } // freeze_agent_array }}}2
    //reset_password {{{2
    /**
     * modify password  page
     *
     * @param int agent_id
     * @return  void
     */
    public function reset_password($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            $this->load_template(lang('Reset Password'), '', '', 'agency');

            $data['agent_id'] = $agent_id;

            $this->template->write_view('main_content', 'agency/reset_password', $data);
            $this->template->render();
        // }
    } //reset_password }}}2
    // verify_reset_password {{{2
    /**
     * verify change password
     *
     * @return  void
     */
    public function verify_reset_password($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean');
        $this->form_validation->set_rules('confirm_new_password', 'Confirm New Password',
            //'trim|required|xss_clean|matches[new_password]|callback_checkIfPasswordMatch');
            'trim|required|xss_clean|matches[new_password]');

        if ($this->form_validation->run() == false) {
            $this->reset_password($agent_id);
        } else {
            $password = $this->salt->encrypt($this->input->post('new_password'), $this->getDeskeyOG());
            //$agent_details = $this->agency_model->get_agent_by_id($agent_id);

            $this->agency_model->startTrans();
            $data = array(
                'password' => $password,
            );
            $this->agency_model->update_agent($agent_id, $data);

            $succ = $this->agency_model->endTransWithSucc();
            if (!$succ) {
                throw new Exception('Sorry, reset password failed.');
            }

            $username=$this->agency_model->getAgentNameById($agent_id);
            $this->syncAgentCurrentToMDBWithLock($agent_id, $username, false);

            //$this->saveAction('Reset Password ', "User " . $this->authentication->getUsername() . " has reset password of agent: " . $agentDetails['username']);

            $message = lang('Successfully Reset Password.');
            $this->alertMessage(1, $message); //will set and send message to the user
            redirect("agency/agent_information/" . $agent_id, "refresh");
        }
    } // verify_reset_password }}}2
    // modify_account {{{2
    /**
     *  Create and Display sub agent list
     *
     *  @param
     *  @return
     */
    public function modify_account() {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            $agent_id = $this->session->userdata('agent_id');

            // $data['min_agent_level'] = $this::MIN_ALLOWED_LEVEL;
            // $data['max_agent_level'] = $this::MAX_ALLOWED_LEVEL;
            // $data['vipgrouplist'] = $this->vipsetting_manager->getVipGroupList();
            $this->load->model('game_type_model');
            $data['game_types'] = $this->game_type_model->getGameTypesArray();
            //$this->utils->debug_log('game_types', $data['game_types'], count($data['game_types']));
            $agent_details = $this->agency_model->get_agent_by_id($agent_id);
            $this->utils->debug_log('Modify Account:', $agent_details);

            $data['conditions'] = $this->safeLoadParams(array(
                'agent_id' => $agent_details['agent_id'],
                'agent_name' => $agent_details['agent_name'],
                'currency' => $agent_details['currency'],
                'status' => $agent_details['status'],
                'credit_limit' => $agent_details['credit_limit'],
                'available_credit' => $agent_details['available_credit'],
                // 'agent_level' => $agent_details['agent_level'],
                // 'agent_level_name' => $agent_details['agent_level_name'],
                'vip_level' => explode(',', $agent_details['vip_level']),
                'rev_share' => $agent_details['rev_share'],
                'rolling_comm' => $agent_details['rolling_comm'],
                'rolling_comm_basis' => $agent_details['rolling_comm_basis'],
                'total_bets_except' => $agent_details['total_bets_except'],
                'can_have_sub_agent' => $agent_details['can_have_sub_agent'],
                'can_have_players' => $agent_details['can_have_players'],
                'settlement_period' => explode(',', $agent_details['settlement_period']),
                'start_day' => $agent_details['settlement_start_day'],
                'parent_id' => $agent_details['parent_id'],
            ));
            $this->load_template(lang('Modify Account'), '', '', '');
            $this->template->add_js('resources/js/bootstrap-switch.min.js');
            $this->template->add_css('resources/css/bootstrap-switch.min.css');
            $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/amcharts.js'));
            $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/serial.js'));
            $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/light.js'));

            //$this->template->write_view('nav_right', 'agency/navigation');
            $this->template->write_view('main_content', 'agency/modify_account', $data);
            $this->template->render();
        // }
    } // modify_account  }}}2
    // batch_create_sub_agent {{{2
    /**
     *  create an agent using given agent template
     *
     *  @param  int agent_id
     *  @return
     */
    public function batch_create_sub_agent($agent_id, $vip_levels = '') {

        if ( ! $this->check_login_status()) {
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        // $data['vipgrouplist'] = $this->vipsetting_manager->getVipGroupList();
        $this->load->model('game_type_model');
        // $data['game_types'] = $this->game_type_model->getGameTypesArray();
        //
        $this->initCreateSubAgentInfo($data, $agent_id, $vip_levels);

        /*
        $agent_details = $this->agency_model->get_agent_by_id($agent_id);
        // $agent_level = $agent_details['agent_level'];
        // $agent_level_name = $agent_details['agent_level_name'];
        // if (empty($vip_levels)) {
        //     $vip_levels = $agent_details['vip_levels'];
        // }

        $this->utils->debug_log('BATCH_CREATE_SUB_AGENT details', $agent_details);

        $data['conditions'] = $this->safeLoadParams(array(
            'parent_id' => $agent_id,
            'parent_name' => $agent_details['agent_name'],
            'before_credit' => '0',
            'agent_name' => '',
            'agent_count' => '1',
            'password' => '',
            'confirm_password' => '',
            'currency' => $agent_details['currency'],
            'status' => $agent_details['status'],
            'credit_limit' => $agent_details['credit_limit'],
            'available_credit' => $agent_details['available_credit'],
            // 'available_credit' => '0.00',
            'note' => '',
            'agent_level' => $agent_details['agent_level'] + 1,
            'agent_level_name' => $agent_details['agent_level_name'],
            'can_have_sub_agent' => $agent_details['can_have_sub_agent'],
            'can_have_players' => $agent_details['can_have_players'],
            'can_view_agents_list_and_players_list' => $agent_details['can_view_agents_list_and_players_list'],
            'settlement_period' => explode(',', $agent_details['settlement_period']),
            'start_day' => $agent_details['settlement_start_day'],
            'before_credit' => '0',
            // 'vip_levels' => $vip_levels,
            'admin_fee' => number_format($agent_details['admin_fee'],2),
            'transaction_fee' => number_format($agent_details['transaction_fee'],2),
            'bonus_fee' => number_format($agent_details['bonus_fee'],2),
            'cashback_fee' => number_format($agent_details['cashback_fee'],2),
            'note' => '', // $agent_details['note'],
            'show_bet_limit_template' => $agent_details['show_bet_limit_template'],
            'show_rolling_commission' => $agent_details['show_rolling_commission'],
            'vip_level' => $agent_details['vip_level'],
            'settlement_period' => $agent_details['settlement_period'],
            'start_day' => $agent_details['settlement_start_day'],
        ));

        $this->utils->debug_log('BATCH_CREATE_SUB_AGENT: conditions', $data['conditions']);

        // $data['min_allowed_level'] = $this::MIN_ALLOWED_LEVEL;
        // $data['max_allowed_level'] = $this::MAX_ALLOWED_LEVEL;
        $data['fields'] = $this->fields_array;
        $data['labels'] = $this->labels_array;
        // $data['agency_player_rolling_settings']=$this->utils->getConfig('agency_player_rolling_settings');

        $data['game_platform_settings']['game_platform_list'] = array();
        $agent_game_platforms = $this->agency_model->get_agent_game_platforms($agent_id);
        $game_platforms_and_types = $this->agency_model->get_game_platforms_and_types();
        foreach ($agent_game_platforms as $agent_game_platform) {
            $game_platform_id = $agent_game_platform['game_platform_id'];
            if (isset($game_platforms_and_types[$game_platform_id])) {
                $data['game_platform_settings']['game_platform_list'][$game_platform_id] = $game_platforms_and_types[$game_platform_id];
            }
        }

        $agent_game_types = $this->agency_model->get_agent_game_types($agent_id);
        foreach ($agent_game_types as &$agent_game_type) {
            $agent_game_type['rolling_comm'] = $agent_game_type['rolling_comm_out'];
            $agent_game_type['rolling_comm_out'] = NULL;
        }

        $data['game_types'] = $this->game_type_model->getGameTypesArray();
        $data['game_platform_settings']['conditions']['game_platforms'] = array_column($agent_game_platforms, NULL, 'game_platform_id');
        $data['game_platform_settings']['conditions']['game_types'] = array_column($agent_game_types, NULL, 'game_type_id');
        $data['parent_id'] = $agent_id;
        $data['is_agent'] = TRUE;
        $data['vip_levels'] = $this->agency_model->get_vip_levels();
         */
        $data['conditions']['agent_count'] = '1';
        $data['is_batch'] = TRUE;
        $data['form_url'] = site_url('agency/verify_batch_agents');

        $this->load_template(lang('Batch Create Agent'), '', '', 'agency');
        $this->template->add_js('resources/js/bootstrap-switch.min.js');
        $this->template->add_css('resources/css/bootstrap-switch.min.css');

        $this->addJsTreeToTemplate();

        $this->template->write_view('main_content', 'includes/common_agent_form', $data);
        // $this->template->write_view('main_content', 'agency/batch_create_sub_agents', $data);
        $this->template->render();
    } // batch_create_sub_agent  }}}2
    // add_batch_agents {{{2
    /**
     *  Add a new agent into DB table agency_agents
     *
     *  @param
     *  @return agent ID
     */
    private function add_batch_agents($current_agent) {
        if ( ! $this->check_login_status()) {
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $today = date("Y-m-d H:i:s");

        $controller = $this;
        $agent_name = $this->input->post('agent_name');
        $agent_count = $this->input->post('agent_count');
        $password = $this->salt->encrypt($this->input->post('password'), $this->getDeskeyOG());
        $available_credit = $this->input->post('available_credit');
        $parent_id = $current_agent['agent_id'];
        $status = $this->input->post('status');
        $active = $status == 'active' ? 1:0;

        $data = array(
            'parent_id'                 => $parent_id,
            'password'                  => $password,
            'currency'                  => $current_agent['currency'],
            'credit_limit'              => $this->input->post('credit_limit'),
            'status'                    => $status,
            'active'                    => $active,
            'agent_level'               => $current_agent['agent_level'] + 1,
            'can_have_sub_agent'        => $this->input->post('can_have_sub_agent'),
            'can_have_players'          => $this->input->post('can_have_players'),
            'show_bet_limit_template'   => $this->input->post('show_bet_limit_template'),
            'show_rolling_commission'   => $this->input->post('show_rolling_commission'),
            'vip_level'                 => $this->input->post('vip_level'),
            'settlement_period'         => $this->input->post('settlement_period'),
            'settlement_start_day'      => $this->input->post('start_day'),
            'note'                      => $this->input->post('note'),
            'admin_fee'                 => $this->input->post('admin_fee'),
            'transaction_fee'           => $this->input->post('transaction_fee'),
            'bonus_fee'                 => $this->input->post('bonus_fee'),
            'cashback_fee'              => $this->input->post('cashback_fee'),
            'min_rolling_comm'              => $this->input->post('min_rolling_comm'),
            'created_on'                => $today,
            'updated_on'                => $today,
        );

        $sub_agent_ids = array();
        while (count($sub_agent_ids) < $agent_count) {

            $agent_details = $this->agency_model->get_agent_by_name($agent_name);

            if ( ! empty($agent_details)) {
                continue;
            }

            $data['agent_name'] = $agent_name;
            $this->utils->debug_log('add_new_agent TTT username', $agent_name);
            $trackingCode=$agent_name;
            $this->load->helper('string');
            while (! $this->agency_model->is_unique_tracking_code($trackingCode, null)) {
                if ($this->utils->isEnabledFeature('agency_tracking_code_numbers_only')) {
                    $trackingCode = random_string('numeric', 8);
                } else {
                    $trackingCode = random_string('alpha_numeric', 8);
                }
            }
            $this->utils->debug_log('add_new_agent TTT trackingCode', $trackingCode);
            $data['tracking_code'] = $trackingCode;

            $agent_id = NULL;

            $current_agent = $this->agency_model->get_agent_by_id($current_agent['agent_id']);

            $succ = $this->lockAndTransForAgencyCredit($parent_id, function()
                use ($controller, &$agent_id, $parent_id, $available_credit, $current_agent, $data) {

                $succ = FALSE;

                if ($current_agent['available_credit'] >= $available_credit) {

                    $agent_id = $this->agency_model->add_agent($data);

                    $succ = ! empty($agent_id);

                    if ($succ && $parent_id && $available_credit > 0) {

                        $succ = $this->transactions->createAgentToSubAgentTransaction($parent_id, $agent_id, $available_credit, 'on create sub-agent');

                    }

                    /*
                    if ($succ && $game_platforms = $this->input->post('game_platforms')) {
                        $game_platforms = array_filter($game_platforms, function(&$game_platform, $game_platform_id) use ($agent_id) {
                            $enabled = isset($game_platform['enabled']) && $game_platform['enabled'];
                            if ($enabled) {
                                $game_platform['game_platform_id'] = $game_platform_id;
                                $game_platform['agent_id'] = $agent_id;
                                unset($game_platform['enabled']);
                            }
                            return $enabled;
                        }, ARRAY_FILTER_USE_BOTH );
                        if ( ! empty($game_platforms)) {
                            $this->db->insert_batch('agency_agent_game_platforms', $game_platforms);
                        }
                    }
                    if ($succ && $game_types = $this->input->post('game_types')) {
                        $game_types = array_filter($game_types, function(&$game_platform, $game_type_id) use ($agent_id) {
                            //$enabled = ! empty($game_platform['rolling_comm_basis']);
                            $enabled = isset($game_platform['pattern_id']);
                            if ($enabled) {
                                $game_platform['game_type_id'] = $game_type_id;
                                $game_platform['agent_id'] = $agent_id;
                            }
                            return $enabled;
                        }, ARRAY_FILTER_USE_BOTH );
                        if ( ! empty($game_types)) {
                            $this->db->insert_batch('agency_agent_game_types', $game_types);
                        }
                    } */
                    if ($succ) {
                        $game_platforms = $this->input->post('game_platforms');
                        $game_types = $this->input->post('game_types');
                        $this->agency_model->add_game_comm_settings($game_platforms, $game_types, $agent_id, 'agent');
                    }
                } else {
                    $controller->utils->error_log('no enought credit from agent', $current_agent['available_credit'], 'sub agent', $available_credit);
                }

                return $succ;
            });

            if ( ! $succ) {
                throw new Exception('Sorry, save agent failed.');
            }

            $sub_agent_ids[] = $agent_id;

            $agent_name = increment_string($agent_name, '');

        }

        return $sub_agent_ids;
    } // add_batch_agents  }}}2
    // verify_batch_agents {{{2
    /**
     *  insert an agent into table agency_agents
     *
     *  @param
     *  @return
     */
    public function verify_batch_agents() {

        if ( ! $this->check_login_status()) {
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $parent_id = $this->input->post('parent_id');

        $this->agent_form_rules();

        $this->form_validation->set_rules('agent_name', lang('Agent Name'), 'trim|required|min_length[2]|max_length[12]|alpha_numeric|is_unique[agency_agents.agent_name]');
        $this->form_validation->set_rules('agent_count', lang('Count'), 'trim|required|is_natural|greater_than[0]|less_than[20]|xss_clean');
        $this->form_validation->set_rules('tracking_code', lang('Tracking Code'), '');

        if ($this->form_validation->run() == false) {
            $this->utils->debug_log('verify_batch_agents form ERRORs: ', validation_errors()); //form_error());
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Failed to create agent'));
            $this->batch_create_sub_agent($parent_id);
        } else {

            $parent_agent = $this->agency_model->get_agent_by_id($parent_id);

            $agent_ids = $this->add_batch_agents($parent_agent);

            $this->utils->debug_log('the agent ids --->', $agent_ids);

            // record action in agency log {{{3
            $log_params = array(
                'action' => 'batch_create_agent',
                'link_url' => site_url('agency/batch_create_sub_agent') . '/' . $parent_id,
                'done_by' => $this->session->userdata('agent_name'),
                'done_to' => $this->input->post('agent_name'),
                'details' => 'Create sub agents: '. implode(',', $agent_ids).'. parent_id = ' . $parent_id,
            );
            $this->agency_library->save_action($log_params);
            // record action in agency log }}}3

            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save successfully'));
            redirect("agency/sub_agents_list", "refresh");
        }
    } // verify_batch_agents  }}}2
    // adjust_credit {{{2
    /**
     * Adjust credit for a given agent
     *
     * @return  void
     */
    public function adjust_credit($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            $this->load_template(lang('Adjust Credit'), '', '', 'agency');

            $data['agent_id'] = $agent_id;
            $data['agent'] = $this->agency_model->get_agent_by_id($agent_id);

            $this->template->write_view('main_content', 'agency/adjust_credit', $data);
            $this->template->render();
            //$this->load->view('agency/adjust_credit', $data);
        // }
    } // adjust_credit }}}2
    // process_adjust_credit {{{2
    /**
     * Adjust credit for a given agent
     *
     * @return  void
     */
    public function process_adjust_credit($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->form_validation->set_rules('adjust_amount', lang('Adjust Amount'),
            'trim|required|greater_than[0]|xss_clean|callback_check_credit_adjust');

        if ($this->form_validation->run() == false) {
            $this->adjust_credit($agent_id);
        } else {
            $op = $this->input->post('transaction_type');
            $adjust_amount = $this->input->post('adjust_amount');
            $success = false;
            $controller = $this;
            $message=null;

            if($adjust_amount>0){

                // lock and trans {{{3
                $success= $this->lockAndTrans(Utils::LOCK_ACTION_AGENCY_BALANCE , $agent_id, function()
                        use ($controller, $agent_id, $op, $adjust_amount, &$message){
                    $agent_details = $controller->agency_model->get_agent_by_id($agent_id);
                    $parent_details = null;
                    if ($agent_details['parent_id'] > 0) {
                        $parent_details = $controller->agency_model->get_agent_by_id($agent_details['parent_id']);
                    }

                    list($success, $message) = $controller->agency_library->check_adjust_credit($op, $agent_details,
                        $parent_details, $adjust_amount);

                    if ($success) {


                        if ($op == 'add') {
                            $extraNotes='on agent to sub-agent';
                            $controller->transactions->createAgentToSubAgentTransaction($parent_details['agent_id'], $agent_id, $adjust_amount, $extraNotes);
                        }else{
                            $extraNotes='on sub-agent to agent';
                            $controller->transactions->createSubAgentToAgentTransaction($parent_details['agent_id'], $agent_id, $adjust_amount, $extraNotes);
                        }

                        // $controller->agency_library->do_adjust_credit($op, $agent_details, $parent_details, $adjust_amount);

                        // // record credit transaction
                        // list($trans_id, $trans_type) = $controller->agency_library->record_transaction_on_adjust($op,
                        //     $agent_details, $parent_details, $adjust_amount);

                        // // update 'balance_history'
                        // $controller->agency_library->record_balance_history_on_adjust($trans_type,
                        //     $trans_id, $agent_details, $adjust_amount);

                        // record action in agency log
                        $action_url = site_url('agency/adjust_credit') . '/' . $agent_id;
                        $operator = $controller->session->userdata('agent_name');
                        $controller->agency_library->save_action_on_adjust_credit($action_url, $operator,
                            $op, $agent_details, $parent_details, $adjust_amount);

                        $message = lang('Successfully adjust credit.');
                    }
                    return $success;
                }); // lock and trans }}}3
            }else{
                $this->utils->error_log('cannot adjust 0 on agent_id', $agent_id);
            }

            if ($success) {
                $this->alertMessage(1, $message);
            } else {
                $this->alertMessage(2, $message); // error message
            }
            redirect("agency/agent_information/" . $agent_id, "refresh");
        }
    } // process_adjust_credit }}}2
    // check_credit_adjust {{{2
    /**
     *  check credit and credit_limit to assure the values are all reasonable
     *
     *  @param
     *  @return
     */
    public function check_credit_adjust() {
        $agent_id = $this->input->post('agent_id');
        $agent_details = $this->agency_model->get_agent_by_id($agent_id);
        $parent_details = null;
        if ($agent_details['parent_id'] > 0) {
            $parent_details = $this->agency_model->get_agent_by_id($agent_details['parent_id']);
        }
        $op = $this->input->post('transaction_type');
        $adjust_amount = $this->input->post('adjust_amount');
        $old_amount = $agent_details['available_credit'];
        $limit = $agent_details['credit_limit'];
        if ($op == 'add') {
            $new_credit = $old_amount + $adjust_amount;
            if ($new_credit > $limit) {
                $message = lang('Available credit cannot exceed credit limit');
                $this->form_validation->set_message('check_credit_adjust', $message);
                return false;
            }
            if ($agent_details['parent_id'] > 0) {
                if ($adjust_amount > $parent_details['available_credit']) {
                    $message = lang('No enough credit in parent agent');
                    $this->form_validation->set_message('check_credit_adjust', $message);
                    return false;
                }
            }
        } else {
            if ($adjust_amount > $old_amount) {
                $message = lang('No enough credit');
                $this->form_validation->set_message('check_credit_adjust', $message);
                return false;
            }
            if ($agent_details['parent_id'] > 0) {
                if ($adjust_amount + $parent_details['available_credit'] > $parent_details['credit_limit']) {
                    $message = lang('Exceed parent credit limit');
                    $this->form_validation->set_message('check_credit_adjust', $message);
                    return false;
                }
            }
        }

        return true;
    } // check_credit_adjust  }}}2
    // adjust_credit_limit {{{2
    /**
     * Adjust credit for a given agent
     *
     * @return  void
     */
    public function adjust_credit_limit($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            $this->load_template(lang('Adjust Credit Limit'), '', '', 'agency');

            $data['agent_id'] = $agent_id;
            $data['agent'] = $this->agency_model->get_agent_by_id($agent_id);

            $this->template->write_view('main_content', 'agency/adjust_credit_limit', $data);
            $this->template->render();
        // }
    } // adjust_credit_limit }}}2
    // process_adjust_credit_limit {{{2
    /**
     * Adjust credit for a given agent
     *
     * @return  void
     */
    public function process_adjust_credit_limit($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->form_validation->set_rules('new_credit_limit', lang('New Credit Limit'),
            'trim|required|numeric|greater_than[0]|less_than[1000000000]|xss_clean|callback_check_new_credit_limit');

        if ($this->form_validation->run() == false) {
            $this->adjust_credit_limit($agent_id);
        } else {
            //$agent_details = $this->agency_model->get_agent_by_id($agent_id);
            $new_limit = $this->input->post('new_credit_limit');
            $data = array(
                'credit_limit' => $new_limit,
            );

            $this->agency_model->update_agent($agent_id, $data);

            $message = lang('Successfully Adjust Credit Limit.');
            $this->alertMessage(1, $message); //will set and send message to the user
            redirect("agency/agent_information/" . $agent_id, "refresh");
        }
    } // process_adjust_credit_limit }}}2
    // check_new_credit_limit {{{2
    /**
     *  check credit and credit_limit to assure the values are all reasonable
     *
     *  @param
     *  @return
     */
    public function check_new_credit_limit() {
        $current_credit = $this->input->post('current_credit');
        $new_credit_limit = $this->input->post('new_credit_limit');
        $parent_id = $this->input->post('parent_id');

        if ($current_credit > $new_credit_limit) {
            $this->form_validation->set_message('check_new_credit_limit',
                $this->labels_array['available_credit'].' '.lang('cannot exceed').' ' . $this->labels_array['credit_limit']
            );
            return false;
        } else if ($parent_id > 0) {
            $parent_details = $this->agency_model->get_agent_by_id($parent_id);
            $parent_limit  = $parent_details['credit_limit'];
            if ($new_credit_limit > $parent_limit) {
                $this->form_validation->set_message('check_new_credit_limit',
                    lang("New credit limit cannot exceed its parent's credit limit"). ' ' . $parent_limit);
                return false;
            }
        }
        return true;
    } // check_new_credit_limit  }}}2
    // agent_hierarchy {{{2
    /**
     *  transfer credit(cash) from parent agent to the player
     *
     *  @param  int agent_id
     *  @return void
     */
    public function agent_hierarchy($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        $this->load_template(lang('Agent Hierarchy'), '', '', 'agency');
        $data['agent_id'] = $agent_id;

        //$this->load->view('agency/agent_hierarchical_tree', $data);
        $this->addJsTreeToTemplate();
        $this->template->add_js('resources/js/bootstrap-switch.min.js');
        $this->template->add_css('resources/css/bootstrap-switch.min.css');
        $this->template->write_view('main_content', 'agency/agent_hierarchy', $data);
        $this->template->render();
    } // agent_hierarchy  }}}2
    // agent_hierarchical_tree {{{2
    /**
     *  transfer credit(cash) from parent agent to the player
     *
     *  @param  int agent_id
     *  @return void
     */
    public function agent_hierarchical_tree($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        $this->load_template(lang('Agent Hierarchy'), '', '', 'agency');
        $data['agent_id'] = $agent_id;

        //$this->load->view('agency/agent_hierarchical_tree', $data);
        //$this->addJsTreeToTemplate();
        $this->template->add_js('resources/js/bootstrap-switch.min.js');
        $this->template->add_css('resources/css/bootstrap-switch.min.css');
        $this->load->view('agency/agent_hierarchical_tree', $data);
    } // agent_hierarchical_tree  }}}2
    // sub_agents }}}1

    // players {{{1
    // players_list {{{2
    /**
     *  list of players under given agent
     *
     *  @param
     *  @return
     */
    public function players_list() {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            $data['agent_id'] = $this->session->userdata('agent_id');
            // if($this->session->userdata('can_have_players')) {
                //$data['players']  = $this->player_model->get_players_by_agent_id($data['agent_id']);

                $this->load_template(lang('Agent Players List'), '', '');
                //$this->template->write_view('nav_right', 'agency/navigation');
                $this->template->write_view('main_content', 'players/list_players', $data);
                $this->template->render();
            // } else if($this->session->userdata('can_have_sub_agent')) {
            //     redirect('agency/sub_agents_list');
            // } else {
            //     $agent_id = $this->session->userdata('agent_id');
            //     redirect('agency/agent_information/'.$agent_id);
            // }
        // }
    } // players_list  }}}2
    // player_information {{{2
    /**
     *  player info including basic info, rolling comm, balance, etc.
     *
     *  @param  int player_id
     *  @return
     */
    public function player_information($player_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->load->model('agency_player_details');

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            if($this->session->userdata('can_have_players')) {
                //$agent_id = $this->session->userdata('agent_id');
                $player_details = $this->player_model->getPlayerArrayById($player_id);

                $agent_id = $player_details['agent_id'];
                $data['agent_id'] = $agent_id;
                $agent_details = $this->agency_model->get_agent_by_id($agent_id);
                $data['agent'] = $agent_details;

                $this->load->model(array('external_system'));
                $data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
                $data['playerDetails'] = $this->player_model->getPlayersSubWalletBalance($player_id, $data['game_platforms']);
                $data['player_account_info'] = $this->player_model->getPlayerAccountInfo($player_id);
                $data['player_signup_info'] = $this->player_model->getPlayerSignupInfoByAgentId($player_id, $agent_id);
                $data['player_signup_info']['typeOfPlayer'] = $this->player_model->getPlayerType($player_id);
                $data['base_credit'] = $this->agency_player_details->base_credit_read($player_id, $agent_id);

                $data['game_platform_settings']['view_only'] = TRUE;
                $this->get_game_comm_settings($data, $player_id, 'player');
                $data['game_platform_settings']['is_player'] = true;

                $this->load_template(lang('Player Information'), '', '', 'agency');
                $this->template->write_view('main_content', 'players/player_information', $data);
                $this->template->render();
            } else {
                redirect('agency/sub_agents_list', 'refresh');
            }
        // }
    } // player_information  }}}2
    // playerBetLimit {{{2
    public function playerBetLimit($player_id, $game_platform_id = EBET_API) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $player_username = $this->player_model->getUsernameById($player_id);

        $api = $this->utils->loadExternalSystemLibObject($game_platform_id);
        $result = $api->getBetLimit($player_username);

        if ( ! $result['success']) {
            if ($result['status'] == 4037) {
                $password = $this->player_model->getPasswordById($player_id);
                $result = $api->createPlayer($player_username, $player_id, $password);
                if ($result['success']) {
                    return $this->playerBetLimit($player_id, $game_platform_id);
                }
            }
            show_error($result['status']);
        }

        //move

        $data = array(
            'player_id'         => $player_id,
            'player_username'   => $player_username,
            'gameIds'           => $result['gameIds'],
            'limit'             => $result['limit'],
        );

        $this->utils->debug_log($data);

        $this->load_template(lang('Player Bet Limit'), '', '', 'agency');
        $this->template->write_view('main_content', 'players/player_bet_limit_2', $data);
        $this->template->render();
    } // playerBetLimit  }}}2
    // updatePlayerBetLimit {{{2
    public function updatePlayerBetLimit($player_id, $game_platform_id = EBET_API) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->load->library('user_agent');

        $player_username = $this->player_model->getUsernameById($player_id);

        $api = $this->utils->loadExternalSystemLibObject($game_platform_id);

        $old_values = array_map(function($value) {
            return json_encode($value);
        }, $this->input->post('old'));

        $new_values = array_map(function($value) {
            return json_encode($value);
        }, $this->input->post('new'));

        $game_tables = array_diff_assoc($new_values, $old_values);

        $result['success'] = true;
        foreach ($game_tables as $game_table_id => $game_table) {
            $game_table = json_decode($game_table, true);
            $game_table['gameId'] = $game_table_id;
            $result = $api->updateBetLimit($player_username, $game_table);
            if ( ! $result['success']) break;
        }

        if ($result['success']) {
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Player bet limit has been updated successfully!'));
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! Player bet limit update has failed.'));
        }

        redirect($this->agent->referrer());

    } // updatePlayerBetLimit  }}}2
    // reset_player_password {{{2
    /**
     * verify change password
     *
     * @return  void
     */
    public function reset_player_password($player_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            $this->load_template(lang('Reset Player Password'), '', '', 'agency');

            $data['player_id'] = $player_id;

            $this->template->write_view('main_content', 'players/reset_password', $data);
            $this->template->render();
        // }
    } // reset_player_password }}}2
    // verify_reset_player_password {{{2
    /**
     * verify change password
     *
     * @return  void
     */
    public function verify_reset_player_password($player_id) {

        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if(!$this->utils->isEnabledFeature('enable_reset_player_password_in_agency')){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean');
        $this->form_validation->set_rules('confirm_new_password', 'Confirm New Password',
            //'trim|required|xss_clean|matches[new_password]|callback_checkIfPasswordMatch');
            'trim|required|xss_clean|matches[new_password]');

        if ($this->form_validation->run() == false) {
            $this->reset_player_password($player_id);
        } else {
            $password = $this->salt->encrypt($this->input->post('new_password'), $this->getDeskeyOG());
            //$player_details = $this->agency_model->get_player_by_id($player_id);

            $this->player_model->startTrans();
            $data = array(
                'password' => $password,
            );

            $this->player_model->updatePlayer($player_id, $data);

            // update all password
            if ($this->utils->isEnabledFeature('sync_api_password_on_update')) {
            //    $username = $this->player_model->getUsernameById($player_id);
            //    $password = $this->player_model->getPasswordByUsername($username);
            //    if ( ! empty($password)) {
            //        $game_platform_id_list = $this->utils->getAllCurrentGameSystemList();
            //        foreach ($game_platform_id_list as $game_platform_id) {
            //            $api = $this->utils->loadExternalSystemLibObject($game_platform_id);
            //            if ($api && $api->isActive()) {
            //                $result = $api->changePassword($username, $password, $password);
            //                $this->utils->debug_log('agency change player password sync api password', 'game_platform_id', $game_platform_id, $result);
            //            }
            //        }
            //    }
            }

            $succ = $this->player_model->endTransWithSucc();
            if (!$succ) {
                throw new Exception('Sorry, reset player password failed.');
            }

            //$this->saveAction('Reset Password ', "User " . $this->authentication->getUsername() . " has reset password of player: " . $playerDetails['username']);

            $message = lang('Successfully Reset Player Password.');
            $this->alertMessage(1, $message); //will set and send message to the user
            redirect("agency/player_information/" . $player_id, "refresh");
        }
    } // verify_reset_player_password }}}2
    // reset_random_password {{{2
    /**
     * verify change password
     *
     * @return  void
     */
    public function reset_random_password($player_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $pass = $this->utils->create_random_password();
        $this->utils->debug_log('reset_random_password', $pass);
        $password = $this->salt->encrypt($pass, $this->getDeskeyOG());

        $this->player_model->startTrans();
        $data = array(
            'password' => $password,
        );

        $this->player_model->updatePlayer($player_id, $data);
        $succ = $this->player_model->endTransWithSucc();
        if (!$succ) {
            throw new Exception('Sorry, Reset Password Failed.');
        }

        //$this->saveAction('Reset Password ', "User " . $this->authentication->getUsername() . " has reset password of agent: " . $agentDetails['username']);

        $message = lang('Successfully Reset Password.');
        $this->alertMessage(1, $message); //will set and send message to the user
        $arr = array('status' => 'success', 'result' => $pass);
        $this->returnJsonResult($arr);
    } // reset_random_password }}}2
    // unfreeze_player {{{2
    /**
     * Will lock/unlock player
     *
     * @param   int
     * @param   int
     * @return  redirect page
     */
    public function unfreeze_player($playerId) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $controller=$this;

        $this->lockAndTrans(Utils::LOCK_ACTION_BALANCE, $playerId, function() use ($controller, $playerId){

            $success=$controller->player_model->unblockPlayerWithGame($playerId);

            // $this->player_model->updatePlayer($playerId, array(
            //     'blocked' => 1,
            // ));
            return $success;
        });

        // $this->player_model->updatePlayer($playerId, array(
        //     'blocked' => 0,
        // ));
        $this->savePlayerUpdateLog($playerId, lang('role.25') . ' - ' .
            lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('tool.pm08') . ') ' .
            lang('adjustmenthistory.title.afteradjustment') . ' (' . lang('tool.pm09') . ') ',
                $this->session->userdata('agent_name'));

        $this->saveAction('Agency', lang('member.log.unblock.website'), "User " .
            $this->session->userdata('agent_name') .  " has adjusted player '" . $playerId . "'");

        $message = lang('member.message.success.unblocked');
        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

        redirect('agency/players_list', 'refresh');
    }
    // unfreeze_player }}}2
    // freeze_player {{{2
    public function freeze_player($playerId) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $controller=$this;

        $this->lockAndTrans(Utils::LOCK_ACTION_BALANCE, $playerId, function() use ($controller, $playerId){

            $success=$controller->player_model->blockPlayerWithGame($playerId);

            // $this->player_model->updatePlayer($playerId, array(
            //     'blocked' => 1,
            // ));
            return $success;
        });

        $this->savePlayerUpdateLog($playerId, lang('role.25') . ' - ' .
            lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang('tool.pm09') . ') ' .
            lang('adjustmenthistory.title.afteradjustment') . ' (' . lang('tool.pm08') . ') ',
                $this->session->userdata('agent_name'));

        $this->saveAction('Agency', lang('member.log.block.website'), "User " .
            $this->session->userdata('agent_name') . " has adjusted player '" . $playerId . "'");

        $message = lang('member.message.success.blocked');
        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

        redirect('agency/players_list', 'refresh');
    }

    protected function saveAction($management, $action, $description = null) {
        $this->recordAction($management, $action, $description);
    }

    protected function recordAction($management, $action, $description) {
        $data = array(
            'username' => $this->session->userdata('agent_name'),
            'management' => $management,
            'userRole' => '', //$roleName,
            'action' => $action,
            'description' => $description,
            'logDate' => date("Y-m-d H:i:s"),
            'status' => '0',
        );

        $this->db->insert('logs', $data);
    }

    /**
     * Save log on playerupdatehistory
     *
     * @param   int
     * @param   string
     * @param   datetime
     * @param   string
     * @return  array
     */
    protected function savePlayerUpdateLog($player_id, $changes, $updatedBy) {
        $data = array(
            'playerId' => $player_id,
            'changes' => $changes,
            'createdOn' => date('Y-m-d H:i:s'),
            'operator' => $updatedBy,
        );
        $this->player_model->addPlayerInfoUpdates($player_id, $data);
    }

    /**
     *  transfer credit(cash) from parent agent to the player
     *
     *  @param  int player_id
     *  @return void
     */
    public function player_deposit($player_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }
        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->load_template(lang('Player Deposit'), '', '', 'player');

        $player_detail = $this->player_model->getPlayerArrayById($player_id);
        $data['player'] = $player_detail;
        $data['player_balance'] = $this->wallet_model->getTotalBalance($player_id);
        $subwalletId=$this->utils->getConfig('default_transfer_subwallet_id');
        $data['subwalletId']=$subwalletId;
        $data['subwalletList']=$this->wallet_model->getSubwalletKV();
        $data['agent'] = array();
        $this->utils->debug_log('PLAYER_DETAIL', $player_detail);
        if (!empty($player_detail['agent_id'])) {
            $data['agent'] = $this->agency_model->get_agent_by_id($player_detail['agent_id']);
        }
        $this->load->view('players/player_deposit', $data);
    }

    /**
     * accepts and processes agent-to-player deposit requests
     * @param   int     $player_id  Target player ID
     * @return  none; launches different redirects on success or error.
     */
    public function player_verify_deposit($player_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }
        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->load->model(array('transactions'));
        $agent_id=$this->get_agent_id_from_session();
        // $player_username = $this->input->post('player_username');
        // $available_credit = $this->input->post('available_credit');
        //load from agent
        // $agent_name = $this->input->post('agent_name');
        // $agent_id = $this->input->post('agent_id');
        // $player_avail = $this->input->post('balance');
        $amount = $this->input->post('deposit_amount');
        $subWalletId=$this->input->post('subwallet_id');
        $bet_limit_template_id = $this->input->post('bet_limit_template_id');

        $player_username=$this->player_model->getUsernameById($player_id);

        $controller=$this;
        $message=null;

        $success=$this->lockAndTransForPlayerBalanceAndAgencyCredit($player_id, $agent_id, function()
        // $success= $this->lockAndTrans(Utils::LOCK_ACTION_AGENCY_BALANCE , $agent_id, function()
            use ($controller, $player_id, $player_username, $subWalletId, $amount, $agent_id, &$message){

            $success=false;
            $agent_info=$controller->agency_model->get_agent_by_id($agent_id);
            $available_credit= $agent_info['available_credit'];
            $agent_name=$agent_info['agent_name'];

            if ($available_credit >= $amount) {

                $success=!!$controller->transactions->createDepositTransactionByAgent($player_id, $amount, $agent_id, null);
                // $controller->wallet_model->incMainWallet($player_id, $deposit_amount);
                if(!$success){
                    $message=lang('Deposit to player failed');
                    return $success;
                }

                $success=!!$controller->transactions->createAgentToPlayerTransaction($agent_id, $player_id, $amount, 'on player deposit');
                if(!$success){
                    $message=lang('Deduct from agent failed');
                    return $success;
                }

                // $this->agency_library->record_transaction_on_player_deposit($player_id, $player_username, $player_avail,
                //     $agent_id, $agent_name, $deposit_amount);

                // record action in agency log {{{3
                $log_params = array(
                    'action' => 'credit_in',
                    'link_url' => site_url('agency/player_deposit/' . $player_id) ,
                    'done_by' => $agent_name,
                    'done_to' => $player_username,
                    'details' => 'player deposit. amount = '. $amount . '. parent agent is '. $agent_name,
                );
                $controller->agency_library->save_action($log_params);

                // record action in agency log }}}3
                $success=true;

            }else{
                $controller->utils->error_log('no enough credit for '.$agent_name);
                $message=lang('No enough balance');
            }

            return $success;
        });

        $this->utils->debug_log('transfer to '.$subWalletId, 'amount', $amount);

        if($success && !empty($subWalletId)){
            //go to sub wallet
            $mainWallet=Wallet_model::MAIN_WALLET_ID;
            $rlt=$this->utils->transferWallet($player_id,$player_username,$mainWallet, $subWalletId, $amount);
            $success=$rlt['success'];
            if(!$success){
                $message=$rlt['message'];
                $this->utils->error_log('transfer faield', $subWalletId, 'amount', $amount, 'player', $player_username);
            }
        }else{
            $this->utils->debug_log('ignore subWalletId', $subWalletId);
        }
        if ($success && $bet_limit_template_id) {
            try {

                $api = $this->utils->loadExternalSystemLibObject($subWalletId);
                if ( ! $api || $api->isDisabled()) {
                    throw new Exception('api is disabled');
                }

                $template = $this->db->get_where('bet_limit_template_list', array('id' => $bet_limit_template_id))->row_array();
                $betLimit = json_decode($template['bet_limit_json'], true);
                $result   = $api->getBetLimit($player_username);

                foreach ($result['gameIds'] as $game_id) {
                    $params = array();
                    $initial = substr($game_id, 0, 1);
                    $params['gameId'] = $game_id;
                    foreach ($betLimit as $key => $value) {
                        if ($initial == substr(strtoupper($key), 0, 1)) {
                            $params[$key] = $value;
                        }
                    }
                    $api->updateBetLimit($player_username, $params);
                }

                $this->player_model->updatePlayer($player_id, array('bet_limit_template_status' => 1));

            } catch(Exception $e) {
                $this->utils->debug_log('error on update bet limit on player transfer: ' . $e->getMessage());
            }
        }

        if ($success) {
            if(empty($message)){
                $message = lang('con.plm31') . " <b>" . $player_username . " </b> Deposit ". $amount.".";
            }
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
        } else {
            //invalid amount
            if(empty($message)){
                $message = lang('Transaction Failed. Please check your input!');
            }
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
        }

        redirect("agency/players_list?username=".$player_username);
    }

    /**
     *  transfer credit(cash) from parent agent to the player
     *
     *  @param  int player_id
     *  @return void
     */
    public function player_withdraw($player_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }
        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->load_template(lang('Player Withdraw'), '', '', 'player');

        $player_detail = $this->player_model->getPlayerArrayById($player_id);
        $data['player'] = $player_detail;
        $data['player_balance'] = $this->wallet_model->getTotalBalance($player_id);
        $subwalletId=$this->utils->getConfig('default_transfer_subwallet_id');
        $data['subwalletId']=$subwalletId;
        $data['subwalletList']=$this->wallet_model->getSubwalletKV();
        $data['agent'] = array();
        if (!empty($player_detail['agent_id'])) {
            $data['agent'] = $this->agency_model->get_agent_by_id($player_detail['agent_id']);
        } else {
            $message_false = lang('Player is NOT under agency!');
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message_false);
            redirect("agency");
        }
        $this->load->view('players/player_withdraw', $data);
    } // player_withdraw  }}}2

    /**
     * post endpoint for withdraw-from-player operation
     * @param   int     $player_id                  == player.playerId
     * @uses    float   POST.withdraw_amount        amount of withdraw
     * @uses    int     POST.subwallet_id           ID of subwallet
     * @uses    int     POST.bet_limit_template_id  ID of bet limit template
     *
     * @return  none
     */
    public function player_verify_withdraw($player_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }
        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        // $player_username = $this->input->post('player_username');
        // $available_credit = $this->input->post('available_credit');
        // $credit_limit = $this->input->post('credit_limit');
        // $agent_name = $this->input->post('agent_name');
        // $agent_id = $this->input->post('agent_id');
        // $balance = $this->input->post('balance');
        $this->load->model(array('transactions'));
        $agent_id=$this->get_agent_id_from_session();
        $amount = $this->input->post('withdraw_amount');
        $subWalletId=$this->input->post('subwallet_id');
        $bet_limit_template_id = $this->input->post('bet_limit_template_id');

        $player_username=$this->player_model->getUsernameById($player_id);

        $success=true;
        $controller=$this;
        $message=null;

        $agent_info=$this->agency_model->get_agent_by_id($agent_id);
        $available_credit= $agent_info['available_credit'];
        $credit_limit= $agent_info['credit_limit'];

        $balance = $this->wallet_model->getTotalBalance($player_id);

        //init check
        if($amount<0){
            $success=false;
            $message=lang('Invalid Amount');
        }else if ($this->utils->compareResultFloat($balance, '>=', $amount)){
            // && $this->utils->compareResultFloat(($amount + $available_credit), '<=', $credit_limit)) {

        }else{
            $this->utils->error_log('$balance >= $amount && ($amount + $available_credit) <= $credit_limit is false',
                'balance', $balance, 'amount', $amount, 'available_credit', $available_credit, 'credit_limit', $credit_limit);
            $success=false;
            $message=lang('No enough balance');
        }

        if($success && !empty($subWalletId)){
            //go to sub wallet
            $mainWallet=Wallet_model::MAIN_WALLET_ID;
            $rlt=$this->utils->transferWallet($player_id,$player_username, $subWalletId, $mainWallet, $amount);
            $success=$rlt['success'];
            if(!$success){
                $message=$rlt['message'];
            }
        }

        if($success){
            $controller=$this;
            $success=$this->lockAndTransForPlayerBalanceAndAgencyCredit($player_id, $agent_id, function()
            // $success= $this->lockAndTrans(Utils::LOCK_ACTION_AGENCY_BALANCE , $agent_id, function()
                use ($controller, $player_id, $player_username, $amount, $agent_id, &$message){

                $success=false;
                $agent_info=$controller->agency_model->get_agent_by_id($agent_id);
                $available_credit= $agent_info['available_credit'];
                $agent_name=$agent_info['agent_name'];
                $balance = $controller->wallet_model->getMainWalletBalance($player_id);
                $credit_limit= $agent_info['credit_limit'];

                if ($this->utils->compareResultFloat($balance, '>=', $amount)){

                    //include decMainWallet
                    $success=!!$controller->transactions->createWithdrawTransactionByAgent($player_id, $amount, $agent_id, null);

                    if(!$success){
                        $message=lang('Withdraw from player failed');
                        return $success;
                    }

                    //include inc credit
                    $success=!!$controller->transactions->createPlayerToAgentTransaction($agent_id, $player_id, $amount, 'on player withdraw');

                    if(!$success){
                        $message=lang('Add credit to agency failed');
                        return $success;
                    }

                    // record action in agency log {{{3
                    $log_params = array(
                        'action' => 'credit_out',
                        'link_url' => site_url('agency/player_withdraw') . '/' . $player_id,
                        'done_by' => $agent_name,
                        'done_to' => $player_username,
                        'details' => 'player withdraw. amount = '. $amount . '. parent agent is '. $agent_name,
                    );
                    $controller->agency_library->save_action($log_params);

                    // record action in agency log }}}3
                    $success=true;

                }else{
                    $controller->utils->error_log('no enough credit for '.$player_username);
                    $message=lang('No enough balance');
                }

                return $success;
            });
        }

        if ($success && $bet_limit_template_id) {
            try {

                $api = $this->utils->loadExternalSystemLibObject($subWalletId);
                if ( ! $api || $api->isDisabled()) {
                    throw new Exception('api is disabled');
                }

                $template = $this->db->get_where('bet_limit_template_list', array('id' => $bet_limit_template_id))->row_array();
                $betLimit = json_decode($template['bet_limit_json'], true);
                $result   = $api->getBetLimit($player_username);

                foreach ($result['gameIds'] as $game_id) {
                    $params = array();
                    $initial = substr($game_id, 0, 1);
                    $params['gameId'] = $game_id;
                    foreach ($betLimit as $key => $value) {
                        if ($initial == substr(strtoupper($key), 0, 1)) {
                            $params[$key] = $value;
                        }
                    }
                    $api->updateBetLimit($player_username, $params);
                }

                $this->player_model->updatePlayer($player_id, array('bet_limit_template_status' => 1));

            } catch(Exception $e) {
                $this->utils->debug_log('error on update bet limit on player transfer: ' . $e->getMessage());
            }
        }

        if ($success) {
            if(empty($message)){
                $message = lang('con.plm31') . " <b>" . $player_username . " </b> " . lang('Withdraw') . " ". $amount.".";
            }
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
        } else {
            // invalid amount
            if(empty($message)){
                $message = lang('Transaction Failed. Please check your input!');
            }
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
        }

        redirect("agency/players_list?username=".$player_username);
    } // player_verify_withdraw }}}2

    public function player_refresh_balance($player_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }
        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $success = true;
        $manager = $this->utils->loadGameManager();
        $rlt = $manager->queryBalanceOnAllPlatformsByPlayerId($player_id);
        if (!empty($rlt)) {

            $this->load->model(array('wallet_model', 'daily_balance', 'game_logs', 'game_provider_auth'));

            $controller = $this;
			$this->lockAndTrans(Utils::LOCK_ACTION_BALANCE, $player_id, function () use ($controller, $player_id, $rlt) {

                $apiArray = $controller->utils->getApiListByBalanceInGameLog();

                foreach ($rlt as $systemId => $val) {
                    if ($val['success']) {
                        $balance = $val['balance'];

                        $api = $controller->utils->loadExternalSystemLibObject($systemId);

                        //$api->updatePlayerSubwalletBalance($player_id, $balance);
                        $api->updatePlayerSubwalletBalance($player_id, $balance);

                        //$this->wallet_model->syncSubWallet($player_id, $systemId, $balance);
                        // $this->lockAndTrans(Utils::LOCK_ACTION_BALANCE, $player_id, function () use ($controller, $player_id, $systemId, $balance) {
                        //  $controller->wallet_model->syncSubWallet($player_id, $systemId, $balance);
                        //  //after balance
                        //  $controller->wallet_model->recordPlayerAfterActionWalletBalanceHistory(Wallet_model::BALANCE_ACTION_REFRESH,
                        //      $player_id, null, -1, $balance, null, null, $systemId, null, $systemId);
                        // });

                        //only for balance_in_game_logs
                        if (in_array($systemId, $apiArray)) {
                            $afterBalance = $balance;
                            $amount = 0;
                            $gameUsername = $controller->game_provider_auth->getGameUsernameByPlayerId($player_id, $systemId);
                            $respResultId = null;
                            $transType = Game_logs::TRANS_TYPE_SUB_WALLET_TO_MAIN_WALLET;
                            $created_at = null;

                            //insert to game logs
                            $id = $controller->game_logs->insertGameTransaction($systemId, $player_id, $gameUsername,
                                $afterBalance, $amount, $respResultId, $transType, $created_at);

                            // $id = $this->lockAndTrans(Utils::LOCK_ACTION_BALANCE, $player_id, function () use ($controller, $systemId, $player_id, $gameUsername, $afterBalance, $amount, $respResultId, $transType, $created_at) {

                            //  return  $controller->game_logs->insertGameTransaction($systemId, $player_id, $gameUsername,
                            //                      $afterBalance, $amount, $respResultId, $transType, $created_at);
                            //      });

                            $controller->utils->debug_log('insert game transaction because reset balance',
                                $systemId, $player_id, 'balance', $afterBalance, $amount, $transType, 'id', $id);

                        }

                    } else {
                        $success = false;
                    }
                }
                //only record one
                $controller->wallet_model->recordPlayerAfterActionWalletBalanceHistory(Wallet_model::BALANCE_ACTION_REFRESH,
                    $player_id, null, -1, 0, null, null, null, null, null);

			});
        }

        if ($success) {
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('report.log06'));
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('report.log07'));
        }
        redirect('agency/players_list');
    }

    /**
     *  rolling comm setting for sub agents, players, and games
     *
     *  @param  int agent_id
     *  @return
     */
    public function rolling_comm_setting($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $data['agent_id'] = $agent_id;
        $agent_details = $this->agency_model->get_agent_by_id($agent_id);

        $data['conditions'] = $this->safeLoadParams(array(
            'rolling_comm' => $agent_details['rolling_comm'],
            'rolling_comm_basis' => $agent_details['rolling_comm_basis'],
            'total_bets_except' => $agent_details['total_bets_except'],
            'sub_agent_rolling_comm' => $agent_details['sub_agent_rolling_comm'],
            'player_rolling_comm' => $agent_details['player_rolling_comm'],
        ));

        $this->load_template(lang('Rolling Comm Setting'), '', '', '');
        $this->template->add_js('resources/js/bootstrap-switch.min.js');
        $this->template->add_css('resources/css/bootstrap-switch.min.css');
        $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/amcharts.js'));
        $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/serial.js'));
        $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/light.js'));

        $this->addBoxDialogToTemplate();
        $this->addJsTreeToTemplate();
        $this->template->write_view('main_content', 'agency/rolling_comm_setting', $data);
        $this->template->render();
    }

    /**
     *  process agency rolling comm setting
     *
     *  @param  int agent_id
     *  @return
     */
    public function process_rolling_comm_setting($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->form_validation->set_rules('sub_agent_rolling_comm', lang('Sub Agent Rolling Comm'),
             'trim|required|numeric|xss_clean|callback_check_sub_agent_rolling_comm');
        $this->form_validation->set_rules('player_rolling_comm', lang('Player Rolling Comm'),
             'trim|required|numeric|xss_clean|callback_check_player_rolling_comm');

        if ($this->form_validation->run() == false) {
            $this->rolling_comm_setting($agent_id);
        } else {
            $this->agency_model->startTrans();
            $data = array(
                'sub_agent_rolling_comm' => $this->input->post('sub_agent_rolling_comm'),
                'player_rolling_comm' => $this->input->post('player_rolling_comm'),
            );
            $this->agency_model->update_agent($agent_id, $data);
            $succ = $this->agency_model->endTransWithSucc();
            if (!$succ) {
                throw new Exception('Sorry, save agent failed.');
            }

            $this->load->model('game_description_model');
            $showGameTree = $this->config->item('show_particular_game_in_tree');
            $this->utils->debug_log('showGameTree', $showGameTree);
            $gamesAptList = $this->loadSubmitGameTreeWithNumber($showGameTree);
            if (!empty($gamesAptList)) {
                $this->utils->debug_log('PROCESS_ROLLING_COMM_SETTING gamesAptList', count($gamesAptList));
                $this->load->model(array('group_level'));
                $rlt = $this->group_level->batch_add_agency_game_rolling_comm($agent_id, $gamesAptList);
            }
            $message = lang('Successfully set rolling comm');
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
            redirect('agency/rolling_comm_setting/'. $agent_id, 'refresh');
        }
    }

    /**
     *  process agency rolling comm setting
     *
     *  @param  int agent_id
     *  @return
     */
    public function process_game_rolling_comm_setting($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->load->model('game_description_model');
        $showGameTree = $this->config->item('show_particular_game_in_tree');
        $this->utils->debug_log('showGameTree', $showGameTree);
        $gamesAptList = $this->loadSubmitGameTreeWithNumber($showGameTree);
        if (!empty($gamesAptList)) {
            $this->utils->debug_log('PROCESS_ROLLING_COMM_SETTING gamesAptList', count($gamesAptList));
            $this->load->model(array('group_level'));
            $rlt = $this->group_level->batch_add_agency_game_rolling_comm($agent_id, $gamesAptList);
        }
        $message = lang('Successfully set game rolling comm');
        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
        redirect('agency/agent_information/'. $agent_id, 'refresh');
    }

    /**
     *  sub_agent_rolling_comm is >=0.00 and <= 3.00
     *
     *  @param
     *  @return
     */
    public function check_sub_agent_rolling_comm() {
        $sub_agent_rolling_comm = $this->input->post('sub_agent_rolling_comm');
        if ($sub_agent_rolling_comm < 0.00 || $sub_agent_rolling_comm > 3.00) {
            //$this->form_validation->set_message('sub_agent_rolling_comm', "%s ". lang('must between 0.00 and 90.00'). ".");
            $this->form_validation->set_message('check_sub_agent_rolling_comm', "%s must be >= 0.00 and <= 3.00.");
            return false;
        }
        return true;
    }

    /**
     *  player_rolling_comm is >=0.00 and <= 3.00
     *
     *  @param
     *  @return
     */
    public function check_player_rolling_comm() {
        $player_rolling_comm = $this->input->post('player_rolling_comm');
        if ($player_rolling_comm < 0.00 || $player_rolling_comm > 3.00) {
            $this->form_validation->set_message('check_player_rolling_comm', "%s must be >= 0.00 and <= 3.00.");
            return false;
        }
        return true;
    }

    /**
     *  search game history
     *
     *  @param
     *  @return
     */
    public function game_history() {
        if(!$this->check_login_status()){
            return;
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            $this->load->model(array('game_type_model', 'game_logs', 'external_system'));
            $agent_id=$this->session->userdata('agent_id');
            //get game platforms only for agency
            $game_platforms = $this->agency_model->getGamePlatformByAgentId($agent_id);
            $data['game_platforms'] = $game_platforms; // $this->external_system->getAllActiveSytemGameApi();
            $data['game_types'] = $this->game_type_model->getGameTypesForDisplay();
            $data['player_levels'] = $this->player_model->getAllPlayerLevels();
            $data['agent_id'] = $agent_id;

            $data['conditions'] = $this->safeLoadParams(array(
                //last one hour
                'by_date_from' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                //'by_date_from' => $this->utils->getTodayForMysql(). ' 00:00:00',
                'by_date_to' => $this->utils->getTodayForMysql(). ' 23:59:59',
                'by_username' => $this->input->get('player_username'),
                'by_group_level' => '',
                'by_game_code' => '',
                'by_game_platform_id' => '',
                'by_game_flag' => '',
                'by_amount_from' => '',
                'by_amount_to' => '',
                'by_bet_amount_from' => '',
                'by_bet_amount_to' => '',
                'by_round_number' => '',
                'game_type_id' => '',
                'game_description_id'=>'',
            ));
            $this->utils->debug_log('GAME_HISTORY conditions', $data['conditions']);

            $this->load_template(lang('Game History'), '', '', 'game_history');
            $this->template->write_view('main_content', 'agency/view_game_logs', $data);
            $this->template->render();
        // }
    }

    /**
     *  Create and Display sub agent list
     *
     *  @param
     *  @return
     */
    public function settlement($agent_name = null, $status = 'current') {
        if(!$this->check_login_status()){
            return;
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            $agent_id = $this->session->userdata('agent_id');
            $data['parent_id'] = $agent_id;
            if($this->utils->isEnabledFeature('alwasy_create_agency_settlement_on_view')){
 	           $this->agency_library->create_settlement($agent_id);
			}

            // if ($agent_name == null) {
            //     $agent_name = $this->session->userdata('agent_name');
            // }

            $data['conditions'] = $this->safeLoadParams(array(
                'agent_name' => $agent_name,
                'status' => $status,
            ));

            $this->load_template(lang('Settlement'), '', '', '');
            $this->template->add_js('resources/js/bootstrap-switch.min.js');
            $this->template->add_css('resources/css/bootstrap-switch.min.css');
            $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/amcharts.js'));
            $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/serial.js'));
            $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/light.js'));

            //$this->template->write_view('nav_right', 'agency/navigation');
            $this->template->write_view('main_content', 'agency/settlement', $data);
            $this->template->render();
        // }
    }

    public function settlement_wl($agent_name = null, $status = '') {
        if(!$this->check_login_status()){
            return;
        }
        $data = $this->initSettlementWl($agent_name, $status);
        $this->load_template(lang('Settlement'), '', '', 'agency');
        $this->template->add_js('resources/js/bootstrap-switch.min.js');
        $this->template->add_css('resources/css/bootstrap-switch.min.css');
        $this->template->write_view('main_content', 'includes/agency_settlement_wl.php', $data);
        $this->template->render();
        return;
    }

    /**
     *  sent settlement invoice
     *
     *  @param  int number of allowed levels
     *  @return redirect to structure_list
     */
    public function settlement_send_invoice($settlement_id) {
        if(!$this->check_login_status()){
            return;
        }

        $data['settlement_id'] = $settlement_id;
        $this->load->view('agency/settlement_send_invoice', $data);
    }

    /**
     *  change status from 'Current' or 'Unsettled' into 'Settled'
     *
     *  @param  int settlement_id
     *  @return settlement_id
     */
    public function do_settlement($settlement_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $unsettled_rec = $this->agency_model->get_settlement_by_id($settlement_id);
        $this->utils->debug_log('DO_SETTLEMENT unsettled rec', $unsettled_rec);

        $agent_id = $unsettled_rec['agent_id'];
        $agent_details = $this->agency_model->get_agent_by_id($agent_id);
        $agent_name = $agent_details['agent_name'];

        // do settlement only for records in 'unsettled' status
        if ($unsettled_rec['status'] != 'unsettled') {
            $message = lang('"do settlement" is only for "unsettled" records');
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            redirect('agency/settlement/'. $agent_name . '/unsettled', 'refresh');
        } else {
            $success = false;
            $controller = $this;
            $message=null;

            // lock and trans {{{3
            $success= $this->lockAndTrans(Utils::LOCK_ACTION_AGENCY_BALANCE , $agent_id, function()
                use ($controller, $unsettled_rec, $agent_details, $logged_agent_id, &$message){

                    // $logged_agent = $controller->agency_model->get_agent_by_id($logged_agent_id);

                    $agent_id = $agent_details['agent_id'];
                    $settlement_id = $unsettled_rec['settlement_id'];
                    // update corresponding 'current' settlement record to substract the balance of this one
                    $period = $unsettled_rec['settlement_period'];

                    $parent_agent=null;
                    if(!empty($agent_details['parent_id'])){
                        $parent_agent=$this->agency_model->get_agent_by_id($agent_details['parent_id']);
                    }

                    $date_from = null;
                    $status = 'current';
                    $current_rec = $this->agency_model->get_settlement_by_agent($agent_id, $period, $date_from, $status);
                    $this->utils->debug_log('DO_SETTLEMENT current rec', $current_rec);

                    if (count($current_rec) > 0) {
                        $id = $current_rec[0]['settlement_id'];
                        $bal = (double)$current_rec[0]['balance'] - (double)$unsettled_rec['balance'];
                        if ($bal < 0.01 && $bal > -0.01) $bal = 0;
                        $this->utils->debug_log('DO_SETTLEMENT new balance', $bal);
                        $data = array(
                            'balance' => $bal,
                        );
                        $this->agency_model->update_settlement($id, $data);
                    }

                    // do settlement for the given record in 'unsettled' status
                    $data = array(
                        'status' => 'settled',
                        'balance' => 0,
                    );
                    $this->agency_model->update_settlement($settlement_id, $data);

                    //pay rolling
                    $action_url = site_url('agency/do_settlement') . '/' . $settlement_id;
                    $adjust_amount=$unsettled_rec['roll_comm_income'];
                    if($adjust_amount>0 && !empty($parent_agent)){
                        $success=$this->do_rolling_settlement($action_url, $settlement_id, $adjust_amount,
                            $agent_details, $parent_agent , $message);
                    }else{
                        $this->utils->debug_log('no rolling to pay '.$adjust_amount, $agent_details, $parent_agent);
                    }

                    if($success){
                    	$message = lang('Successfully do settlement');
                    }
                    // $success = true;
                    return $success;
                }); // lock and trans }}}3
            if($success) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
                redirect('agency/settlement/'. $agent_name . '/settled', 'refresh');
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                redirect('agency/settlement/'. $agent_name . '/unsettled', 'refresh');
            }
        }
    }

    /**
     * Do WL settlement; 'Do Settlement' button
     * Also deposit to/withdraw from user account, 'flattening' account to base credit
     * (PRH-768; see also PRH-652, PRH-326)
     * @uses    POST.user_id    == agency_wl_settlement.user_id, may refer to player_id or agent_id
     * @uses    POST.date_start Start of settlement date range
     * @uses    POST.date_end   End of settlement date range
     *
     * @return  none
     */
    public function do_settlement_wl() {
        $debug = false;
        $this->load->model(['agency_player_details']);

        $user_id = $this->input->post('user_id');
        $date_start = $this->input->post('date_start');
        $date_end = $this->input->post('date_end');

        // Read settlement group
        $wl_settlements = $this->agency_model->getWlSettlementRowGroup($user_id, $date_start, $date_end);
        $settlement_success = true;

        foreach ($wl_settlements as $wlst) {
            $controller = $this;
            $message = null;


            // Determine deposit amount: by player W/L
            $player_wl = $wlst['result_amount'];
            $player_base_credit = $this->agency_player_details->base_credit_read($wlst['user_id'], $wlst['agent_id']);
            /**
             * Flattening amount: with player's base credit given,
             * - if player wins  (W/L > 0): deduct his balance to match base credit
             *      flattening_amount = base_credit - player_wl     (result <= 0)
             * - if player loses (W/L < 0): amend him with enough cash to match base credit
             *      flattening_amount = base_credit + (-player_wl)  (result > 0)
             */
            $flattening_amount = $player_base_credit - $player_wl;

            // $this->utils->debug_log("Agency::do_settlement_wl: settlement row", $wlst);
            $this->utils->debug_log("Agency::do_settlement_wl: calc flattening", 'agent', $wlst['agent_id'], 'player', $wlst['user_id'], 'base_credit', $player_base_credit, 'player wl', $player_wl, 'flattening', $flattening_amount);

            $deposit_amount = $flattening_amount;

            // $deposit_amount = $wlst['player_commission'];

            // Transfer credit to player (protected by system feature switch)
            $deposit_result = null;

            // Deposit_amount > 0 : agent => player transfer
            if ($wlst['user_id'] != $wlst['agent_id'] && $deposit_amount > 0) {
                // Actual deposit is protected by the system feature switch
                if($this->utils->isEnabledFeature('transfer_rolling_to_player_when_do_settlement_wl')){
                    if (!$debug) {
                        $deposit_result = $controller->agent_to_player_transfer($wlst['user_id'], $wlst['agent_id'], abs($deposit_amount));
                    }
                    $this->utils->debug_log("Agency::do_settlement_wl: transfer: agent => player", 'agent', $wlst['agent_id'], 'player', $wlst['user_id'], 'amount', abs($deposit_amount), 'result', $deposit_result);
                }
                else {
                    $deposit_result = true;
                    $this->utils->debug_log("Agency::do_settlement_wl: transfer (disabled by sys feature) : agent => player", 'agent', $wlst['agent_id'], 'player', $wlst['user_id'], 'amount', abs($deposit_amount), 'result', $deposit_result);
                }
            }
            // Deposit amount < 0 : player => agent transfer
            else if ($wlst['user_id'] != $wlst['agent_id'] && $deposit_amount < 0) {
                // Actual withdraw is protected by the system feature switch
                if($this->utils->isEnabledFeature('transfer_rolling_to_player_when_do_settlement_wl')){
                    if (!$debug) {
                        $deposit_result = $controller->player_to_agent_transfer($wlst['user_id'], $wlst['agent_id'], abs($deposit_amount));
                    }
                    $this->utils->debug_log("Agency::do_settlement_wl: transfer: player => agent", 'agent', $wlst['agent_id'], 'player', $wlst['user_id'], 'amount', abs($deposit_amount), 'result', $deposit_result);
                }
                else {
                    $deposit_result = true;
                    $this->utils->debug_log("Agency::do_settlement_wl: transfer: (disabled by sys feature) player => agent", 'agent', $wlst['agent_id'], 'player', $wlst['user_id'], 'amount', abs($deposit_amount), 'result', $deposit_result);
                }
            }
            // Skip amount=0 deposits
            else if ($wlst['user_id'] != $wlst['agent_id'] && $deposit_amount == 0) {
                $this->utils->debug_log("Agency::do_settlement_wl: transfer: skipping amount=0", 'agent', $wlst['agent_id'], 'player', $wlst['user_id'], 'amount', $deposit_amount);
                $deposit_result = true;
            }
            // For agent self
            else {
                $this->utils->debug_log("Agency::do_settlement_wl: skipping agent self", $wlst['user_id']);
                $deposit_result = true;
            }

            // Do single settlement
            if (!empty($deposit_result)) {
                if($this->utils->isEnabledFeature('agent_settlement_to_wallet')) {
                    $success =
                        $this->lockAndTransForAgencyBalance($wlst['user_id'], function () use ($controller, $wlst) {
                            $row_affected = $controller->agency_model->doSingleWlSettlement($wlst['id'], true);
                            return $row_affected > 0;
                        });
                } else {
                    $success =
                        $this->lockAndTransForAgencyCredit($wlst['user_id'], function () use ($controller, $wlst) {
                            $row_affected = $controller->agency_model->doSingleWlSettlement($wlst['id'], false);
                            return $row_affected > 0;
                        });
                }
                $this->utils->debug_log("Agency::do_settlement_wl: doSingleWlSettlement success?", $success);
                $settlement_success = $settlement_success && $success;
            }

        } // End of foreach ($wl_settlements as $wlst)

        if($settlement_success) {
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Settlement record settled'));
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Error doing settlement'));
        }
    } // End of function do_settlement_wl()

    /**
     * Action for Close Settlement button.
     * Marks a settlement as closed.
     * @uses    POST.user_id    == agency_wl_settlement.user_id, may refer to player_id or agent_id
     * @uses    POST.date_start Start of settlement date range
     * @uses    POST.date_end   End of settlement date range
     *
     * @return  none
     */
    public function close_settlement_wl() {
        $user_id = $this->input->post('user_id');
        $date_start = $this->input->post('date_start');
        $date_end = $this->input->post('date_end');

        // Read settlement group
        $wl_settlements = $this->agency_model->getWlSettlementRowGroup($user_id, $date_start, $date_end);
        $settlement_success = true;

        foreach ($wl_settlements as $wlst) {
            $row_affected = $this->agency_model->closeSingleWlSettlement($wlst['id']);
            $settlement_success = $settlement_success && ($row_affected > 0);
        }

        if($settlement_success) {
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Settlement record closed'));
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Error closing settlement'));
        }
    }

    /**
     * accepts and processes agent-to-player deposit requests
     * Copied from Agency::player_verify_deposit()
     * @param   int     $player_id      id of target player
     * @param   int     $agent_id       id of source agent
     * @param   float   $amount         amount
     * @return  true on success, otherwise null or false
     */
    protected function agent_to_player_transfer($player_id, $agent_id, $amount) {

        $this->load->model(array('transactions', 'player_model', 'agency_model'));

        // Determine bet_limit_template_id
        $player = $this->player_model->getPlayerArrayById($player_id);
        $bet_limit_template_id = null;
        if (!$this->config->item('update_bet_limit_on_batch_create') && intval($player['bet_limit_template_id']) != 0 && $player['bet_limit_template_status'] == 0) {
            $bet_limit_template_id = intval($player['bet_limit_template_id']);
        }

        $player_username = $this->player_model->getUsernameById($player_id);

        $subWalletId = null;

        $controller = $this;
        $message = null;

        $this->utils->debug_log('Agency::agent_to_player_transfer()', [ 'player' => "$player_id ($player_username)", 'agent' => $agent_id, 'subWallet' => $subWalletId, 'amount' => $amount ]);

        // agency-to-player transfer (deposit) transaction
        $success = $this->lockAndTrans(Utils::LOCK_ACTION_AGENCY_BALANCE , $agent_id, function()
            use ($controller, $player_id, $player_username, $subWalletId, $amount, $agent_id, &$message) {

            $success=false;
            $agent_info=$controller->agency_model->get_agent_by_id($agent_id);
            $available_credit= $agent_info['available_credit'];
            $agent_name=$agent_info['agent_name'];

            if ($available_credit >= $amount) {

                $success=!!$controller->transactions->createDepositTransactionByAgent($player_id, $amount, $agent_id, null);
                if(!$success){
                    return $success;
                }

                $success = !!$controller->transactions->createAgentToPlayerTransaction($agent_id, $player_id, $amount, 'on player deposit');
                if (!$success){
                    return $success;
                }

                // record action in agency log {{{3
                $log_params = array(
                    'action' => 'credit_in',
                    'link_url' => site_url('agency/player_deposit/' . $player_id) ,
                    'done_by' => $agent_name,
                    'done_to' => $player_username,
                    'details' => 'player deposit. amount = '. $amount . '. parent agent is '. $agent_name,
                );
                $controller->agency_library->save_action($log_params);

                // record action in agency log }}}3
                $success=true;

            }else{
                $controller->utils->error_log("Insufficient credit for agent {$agent_name}");
            }

            return $success;
        }); // End of closure: agency-to-player transfer (deposit) transaction

        if ($success && $bet_limit_template_id) {
            try {
                $api = $this->utils->loadExternalSystemLibObject(BETMASTER_API);
                if ( ! $api || $api->isDisabled()) {
                    throw new Exception('api is disabled');
                }

                $template = $this->db->get_where('bet_limit_template_list', array('id' => $bet_limit_template_id))->row_array();
                $betLimit = json_decode($template['bet_limit_json'], true);
                $result   = $api->getBetLimit($player_username);

                foreach ($result['gameIds'] as $game_id) {
                    $params = array();
                    $initial = substr($game_id, 0, 1);
                    $params['gameId'] = $game_id;
                    foreach ($betLimit as $key => $value) {
                        if ($initial == substr(strtoupper($key), 0, 1)) {
                            $params[$key] = $value;
                        }
                    }
                    $api->updateBetLimit($player_username, $params);
                }

                $this->player_model->updatePlayer($player_id, array('bet_limit_template_status' => 1));

            } catch(Exception $e) {
                $this->utils->debug_log('error on update bet limit on player transfer: ' . $e->getMessage());
            }
        }

        return $success;

    } // End of agent_to_player_transfer()

    /**
     * processes player-to-agent transfer requests
     * Copied from Agency::player_verify_withdraw()
     * @param   int     $player_id  == player.playerId
     * @param   int     $agent_id   agent_id
     * @param   float   $amount     amount of transfer
     * @return  none
     */
    protected function player_to_agent_transfer($player_id, $agent_id, $amount) {

        $this->load->model(array('transactions', 'player_model', 'agency_model', 'wallet_model'));

        // Determine bet_limit_template_id
        $player = $this->player_model->getPlayerArrayById($player_id);
        $bet_limit_template_id = null;
        if (!$this->config->item('update_bet_limit_on_batch_create') && intval($player['bet_limit_template_id']) != 0 && $player['bet_limit_template_status'] == 0) {
            $bet_limit_template_id = intval($player['bet_limit_template_id']);
        }

        $player_username = $this->player_model->getUsernameById($player_id);

        $subWalletId = null;

        $controller=$this;
        $message=null;

        $this->utils->debug_log('Agency::player_to_agent_transfer()', [ 'player' => "$player_id ($player_username)", 'agent' => $agent_id, 'subWallet' => $subWalletId, 'amount' => $amount ]);

        $agent_info=$this->agency_model->get_agent_by_id($agent_id);
        $available_credit = $agent_info['available_credit'];
        $credit_limit= $agent_info['credit_limit'];

        $balance = $this->wallet_model->getTotalBalance($player_id);

        $success = true;
        // Check if player has enough balance
        if ($this->utils->compareResultFloat($balance, '>=', $amount)){
            // && $this->utils->compareResultFloat(($amount + $available_credit), '<=', $credit_limit)) {
        }else{
            $this->utils->error_log("Insufficient credit for player {$player_username}", 'balance', $balance, 'transfer amount', $amount);
            $success = false;
        }

        // -- Skip subwallet for now
        // if($success && !empty($subWalletId)){
        //     //go to sub wallet
        //     $mainWallet=Wallet_model::MAIN_WALLET_ID;
        //     $success=$this->utils->transferWallet($player_id,$player_username, $subWalletId, $mainWallet, $amount);
        // }

        if ($success) {
            $controller = $this;
            $success = $this->lockAndTrans(Utils::LOCK_ACTION_AGENCY_BALANCE , $agent_id, function()
                use ($controller, $player_id, $player_username, $amount, $agent_id, &$message){

                $success = false;
                $agent_info = $controller->agency_model->get_agent_by_id($agent_id);
                $available_credit = $agent_info['available_credit'];
                $agent_name = $agent_info['agent_name'];
                $balance = $controller->wallet_model->getMainWalletBalance($player_id);
                $credit_limit = $agent_info['credit_limit'];

                // Redundant guard.  Duplicate with the section 'check if player has enough balance' above.
                if ($balance >= $amount) { //} && ($amount + $available_credit) < $credit_limit) {

                    // Create transaction 'withdraw by agent'
                    $success = !!$controller->transactions->createWithdrawTransactionByAgent($player_id, $amount, $agent_id, null);
                    // $controller->wallet_model->decMainWallet($player_id, $amount);
                    if (!$success) {
                        return $success;
                    }

                    // Create transaction 'player to agent'
                    $success = !!$controller->transactions->createPlayerToAgentTransaction($agent_id, $player_id, $amount, 'on player withdraw');
                    if(!$success){
                        return $success;
                    }

                    // record action in agency log {{{3
                    $log_params = array(
                        'action' => 'credit_out',
                        'link_url' => site_url('agency/player_withdraw') . '/' . $player_id,
                        'done_by' => $agent_name,
                        'done_to' => $player_username,
                        'details' => 'player withdraw. amount = '. $amount . '. parent agent is '. $agent_name,
                    );
                    $controller->agency_library->save_action($log_params);

                    // record action in agency log }}}3
                    $success=true;

                }else{
                    $controller->utils->error_log("Insufficient credit for player {$player_username}");
                }

                return $success;
            }); // End of closure: player-to-agent transfer transaction
        } // End of guard if ($success)

        if ($success && $bet_limit_template_id) {
            try {

                $api = $this->utils->loadExternalSystemLibObject(BETMASTER_API);
                if ( ! $api || $api->isDisabled()) {
                    throw new Exception('api is disabled');
                }

                $template = $this->db->get_where('bet_limit_template_list', array('id' => $bet_limit_template_id))->row_array();
                $betLimit = json_decode($template['bet_limit_json'], true);
                $result   = $api->getBetLimit($player_username);

                foreach ($result['gameIds'] as $game_id) {
                    $params = array();
                    $initial = substr($game_id, 0, 1);
                    $params['gameId'] = $game_id;
                    foreach ($betLimit as $key => $value) {
                        if ($initial == substr(strtoupper($key), 0, 1)) {
                            $params[$key] = $value;
                        }
                    }
                    $api->updateBetLimit($player_username, $params);
                }

                $this->player_model->updatePlayer($player_id, array('bet_limit_template_status' => 1));

            } catch(Exception $e) {
                $this->utils->debug_log('error on update bet limit on player transfer: ' . $e->getMessage());
            }
        }

        return $success;

    } // End of player_to_agent_transfer()

    private function do_rolling_settlement($action_url, $settlement_id, $adjust_amount,$agent_details, $parent_agent, &$message){
        //check available credit
        $available_credit=$parent_agent['available_credit'];
        if($available_credit<$adjust_amount){
            $success=false;
            $message=lang('No enough available credit');
            return $success;
        }

        if($adjust_amount<=0){
            $success=false;
            $message=lang('Cannot pay zero');
            return $success;
        }

        $op = 'add';
        // list($success, $message) = $controller->agency_library->check_adjust_credit($op,
        //     $agent_details, $parent_details, $adjust_amount);

        $this->utils->debug_log('transfer '.$adjust_amount, $agent_details, $parent_agent);

        // if($success){
        //transfer parent_agent to agent_details
        $this->agency_library->do_adjust_credit($op, $agent_details, $parent_agent, $adjust_amount);

        //update status
        $data=['player_rolling_comm_payment_status'=>'paid'];
        $this->agency_model->update_settlement($settlement_id, $data);

        // record credit transaction
        list($trans_id, $trans_type) = $this->agency_library->record_transaction_on_adjust($op,
            $agent_details, $parent_agent, $adjust_amount);

        // update 'balance_history'
        $this->agency_library->record_balance_history_on_adjust($trans_type,
            $trans_id, $agent_details, $adjust_amount);

        // record action in agency log
        // $action_url = site_url('agency/pay_rolling_comm') . '/' . $settlement_id;
        $operator = $this->session->userdata('agent_name');
        $this->agency_library->save_action_on_adjust_credit($action_url, $operator,
            $op, $agent_details, $parent_agent, $adjust_amount);

        // $message = lang('Successfully paid rolling comm.');
        $success=true;

        return $success;
    }

    // freeze_settlement {{{2
    /**
     *  freeze given settlement
     *
     *  @param  int settlement_id
     *  @return void
     */
    public function freeze_settlement($settlement_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $data = array(
            'frozen' => '1',
        );
        $this->agency_model->update_settlement($settlement_id, $data);
        redirect('agency/settlement', 'refresh');
    }

    /**
     *  unfreeze given settlement
     *
     *  @param  int settlement_id
     *  @return void
     */
    public function unfreeze_settlement($settlement_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $data = array(
            'frozen' => '0',
        );
        $this->agency_model->update_settlement($settlement_id, $data);
        redirect("agency/settlement", 'refresh');
    }

    /**
     *  pay rolling comm to a sub agent
     *
     *  @param  int settlement_id
     *  @return
     */
    private function pay_rolling_comm($settlement_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $parent_id = $this->session->userdata('agent_id');

        $success = false;
        $controller = $this;
        $message=null;
        $agent_name = '';
        // lock and trans {{{3
        $success= $controller->lockAndTrans(Utils::LOCK_ACTION_AGENCY_BALANCE , $parent_id, function()
            use ($controller, $logged_agent_id, $settlement_id, &$message, &$agent_name){

                $rec = $controller->agency_model->get_settlement_by_id($settlement_id);

                $agent_id = $rec['agent_id'];
                $agent_details = $controller->agency_model->get_agent_by_id($agent_id);
                $logged_agent = $controller->agency_model->get_agent_by_id($logged_agent_id);

                $this->utils->debug_log('logged_agent', $logged_agent, 'agent_details', $agent_details);

                $agent_name = $agent_details['agent_name'];

                $controller->utils->debug_log('PAY_ROLLING_COMM rec agent', $rec, $agent_details);

                $success = false;
                if ($rec['player_rolling_comm_payment_status'] == 'paid') {
                    $message = lang('Rolling comm is already paid! No need to pay again!');
                    return $success;
                }


                if($logged_agent_id==$agent_details['parent_id']){

                    $adjust_amount = $rec['roll_comm_income'] ; //$rec['bets'] * (100.0 - $agent_details['rev_share']) * $parent_details['rolling_comm']/10000.0;

                    //check available credit
                    $available_credit=$logged_agent['available_credit'];
                    if($available_credit<$adjust_amount){
                        $success=false;
                        $message=lang('No enough available credit');
                        return $success;
                    }

                    if($adjust_amount<=0){
                        $success=false;
                        $message=lang('Cannot pay zero');
                        return $success;
                    }

                    $op = 'add';
                    // list($success, $message) = $controller->agency_library->check_adjust_credit($op,
                    //     $agent_details, $parent_details, $adjust_amount);

                    $this->utils->debug_log('transfer '.$adjust_amount, $agent_details, $logged_agent);

                    // if($success){
                        //transfer logged_agent to agent_details
                        $controller->agency_library->do_adjust_credit($op, $agent_details, $logged_agent, $adjust_amount);

                        //update status
                        $data=['player_rolling_comm_payment_status'=>'paid'];
                        $controller->agency_model->update_settlement($settlement_id, $data);

                        // record credit transaction
                        list($trans_id, $trans_type) = $controller->agency_library->record_transaction_on_adjust($op,
                            $agent_details, $logged_agent, $adjust_amount);

                        // update 'balance_history'
                        $controller->agency_library->record_balance_history_on_adjust($trans_type,
                            $trans_id, $agent_details, $adjust_amount);

                        // record action in agency log
                        $action_url = site_url('agency/pay_rolling_comm') . '/' . $settlement_id;
                        $operator = $controller->session->userdata('agent_name');
                        $controller->agency_library->save_action_on_adjust_credit($action_url, $operator,
                            $op, $agent_details, $logged_agent, $adjust_amount);

                        $message = lang('Successfully paid rolling comm.');
                        $success=true;

                    // }

                }else{
                    $message = lang('Rolling comm can only be paid by parent agent.');
                }

                // if ($agent_details['parent_id'] > 0) {
                //     //check available credit
                //     $available_credit=$agent_details['available_credit'];
                //     $adjust_amount = $rec['roll_comm_amt'] ; //$rec['bets'] * (100.0 - $agent_details['rev_share']) * $parent_details['rolling_comm']/10000.0;
                //     if($available_credit<$adjust_amount){
                //         $success=false;
                //         $message=lang('No enough available credit');
                //         return $success;
                //     }

                //     $parent_details = $controller->agency_model->get_agent_by_id($agent_details['parent_id']);

                //     // calculate rolling comm amount
                //     // $adjust_amount = $rec['roll_comm_amt'] ; //$rec['bets'] * (100.0 - $agent_details['rev_share']) * $parent_details['rolling_comm']/10000.0;

                //     // do settlement for the given record in 'unsettled' status
                //     $data = array(
                //         'roll_comm_income' => $adjust_amount,
                //     );
                //     $controller->agency_model->update_settlement($settlement_id, $data);

                //     $op = 'add';
                //     list($success, $message) = $controller->agency_library->check_adjust_credit($op,
                //         $agent_details, $parent_details, $adjust_amount);

                //     if ($success) {
                //         $controller->agency_library->do_adjust_credit($op, $agent_details, $parent_details, $adjust_amount);

                //         // record credit transaction
                //         list($trans_id, $trans_type) = $controller->agency_library->record_transaction_on_adjust($op,
                //             $agent_details, $parent_details, $adjust_amount);

                //         // update 'balance_history'
                //         $controller->agency_library->record_balance_history_on_adjust($trans_type,
                //             $trans_id, $agent_details, $adjust_amount);

                //         // record action in agency log
                //         $action_url = site_url('agency/pay_rolling_comm') . '/' . $settlement_id;
                //         $operator = $controller->session->userdata('agent_name');
                //         $controller->agency_library->save_action_on_adjust_credit($action_url, $operator,
                //             $op, $agent_details, $parent_details, $adjust_amount);

                //         $message = lang('Successfully paid rolling comm.');
                //     }
                // } else {
                //     $message = lang('Rolling comm can only be paid by parent agent.');
                // }
                return $success;
            }); // lock and trans }}}3
        if ($success) {
            $this->alertMessage(1, $message);
        } else {
            $this->alertMessage(2, $message); // error message
        }
        redirect('agency/settlement/' . $agent_name);
    }

    /**
     *  show player rolling comm in details associated to the given settlement_id
     *
     *  @param  int settlement_id
     *  @return
     */
    public function show_player_rolling_comm_info($settlement_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            $data['settlement_id'] = $settlement_id;
            $settlement = $this->agency_model->get_settlement_by_id($settlement_id);
            $data['settlement'] = $settlement;

            $this->load_template(lang('Player Rolling Comm Info'), '', '', 'player');
            $this->template->write_view('main_content', 'players/player_rolling_comm_info', $data);
            $this->template->render();
        // }
    }

    /**
     *  show player rolling comm in details associated to the given settlement_id
     *
     *  @param  int settlement_id
     *  @return
     */
    public function show_player_rolling_comm_info_detail($settlement_id, $player_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            $data['settlement_id'] = $settlement_id;
            $data['player_id'] = $player_id;

            $this->load_template(lang('Game Rolling Comm Info'), '', '', 'player');
            $this->template->write_view('main_content', 'players/game_rolling_comm_info', $data);
            $this->template->render();
        // }
    }

    /**
     *  Create and Display credit transactions
     *
     *  @param
     *  @return
     */
    public function credit_transactions() {
        if(!$this->check_login_status()){
            return;
        }

        // if (!$this->check_login()) {
        //     redirect('agency');
        // } else {
            $data['parent_id'] = $this->session->userdata('agent_id');
            $data['parent_name'] = $this->session->userdata('agent_name');
            $data['agent_username'] = $this->input->get('agent_username');
            $data['player_username'] = $this->input->get('player_username');
            $data['date_from'] = $this->input->get('date_from');
            $data['date_to'] = $this->input->get('date_to');

            $this->load_template(lang('Credit Transaction'), '', '', '');
            $this->template->add_js('resources/js/bootstrap-switch.min.js');
            $this->template->add_css('resources/css/bootstrap-switch.min.css');
            $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/amcharts.js'));
            $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/serial.js'));
            $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/light.js'));

            //$this->template->write_view('nav_right', 'agency/navigation');
            $this->template->write_view('main_content', 'agency/credit_transactions', $data);
            $this->template->render();
        // }
    }

    /**
     *  deposit for an agent
     *
     *  @param  array agent_details
     *  @param  array parent_details
     *  @param  int deposit amount
     *  @return bool true on success
     */
    private function transaction_agent_deposit($agent_details, $parent_details, $amount) {
        $success = false;
        // transfer credit from parent to agent
        $agent_limit = $agent_details['credit_limit'];
        $agent_avail = $agent_details['available_credit'];
        $parent_limit = $parent_details['credit_limit'];
        $parent_avail = $parent_details['available_credit'];
        $new_avail = $amount + $agent_avail;
        if ($amount < $parent_avail &&  $new_avail < $agent_limit) {
            $success = true;
            $data = array(
                'available_credit' => $new_avail,
            );
            $agent_id = $agent_details['agent_id'];
            $this->agency_model->update_agent($agent_id, $data);

            $data = array(
                'available_credit' => $parent_avail - $amount,
            );
            $parent_id = $parent_details['agent_id'];
            $this->agency_model->update_agent($parent_id, $data);
        }
        return $success;
    }

    /**
     *  withdraw for an agent
     *
     *  @param  array agent_details
     *  @param  array parent_details
     *  @param  int withdraw amount
     *  @return bool true on success
     */
    private function transaction_agent_withdraw($agent_details, $parent_details, $amount) {
        $success = false;
        // transfer credit from agent to parent
        $agent_limit = $agent_details['credit_limit'];
        $agent_avail = $agent_details['available_credit'];
        $parent_limit = $parent_details['credit_limit'];
        $parent_avail = $parent_details['available_credit'];
        if (($parent_avail + $amount) < $parent_limit &&  $agent_avail > $amount) {
            $success = true;
            $data = array(
                'available_credit' => $agent_avail - $amount,
            );
            $agent_id = $agent_details['agent_id'];
            $this->agency_model->update_agent($agent_id, $data);

            $data = array(
                'available_credit' => $parent_avail + $amount,
            );
            $parent_id = $parent_details['agent_id'];
            $this->agency_model->update_agent($parent_id, $data);
        }
        return $success;
    }

    /**
     *  deposit for a player
     *
     *  @param  array player_details
     *  @param  before_balance in player main wallet
     *  @param  array parent_details
     *  @param  int deposit amount
     *  @return bool true on success
     */
    private function transaction_player_deposit($player_details, $player_avail, $parent_details, $amount) {
        $success = false;
        $player_id = $player_details['playerId'];

        $parent_limit = $parent_details['credit_limit'];
        $parent_avail = $parent_details['available_credit'];
        $this->utils->debug_log('amount, parent_avail', $amount, $parent_avail, $parent_details);
        if ($amount < $parent_avail) {
            $success = true;
            $success=$this->wallet_model->incMainWallet($player_id, $amount);
            if($success){
                $data = array(
                    'available_credit' => $parent_avail - $amount,
                );
                $parent_id = $parent_details['agent_id'];
                $this->agency_model->update_agent($parent_id, $data);
            }
        }
        return $success;
    }

    /**
     *  player report for agency sub system in which each player has a parent agent id
     *
     *  @return
     */
    public function agency_player_report() {
        if(!$this->check_login_status()){
            return;
        }

        $data['allLevels'] = $this->player_model->getAllPlayerLevels();
        $data['agent_name'] = $this->session->userdata("agent_name");
        $data['agent_username'] = $this->input->get('agent_username');
        $data['date_from'] = $this->input->get('date_from');
        $data['date_to'] = $this->input->get('date_to');

        $this->load_template(lang('Player Report'), '', '', 'report');
        $this->template->write_view('main_content', 'agency/view_player_report', $data);
        $this->template->render();
    }

    /**
     * Check if target agent is among master's downlines; check whether master agent is
     * among target agent's ancestry.
     * @return  JSON    standard return object {success, result}
     */
    public function agency_check_ancestry() {
        $target_username = trim($this->input->post('target_agent', 1));
        $master_username = $this->session->userdata('agent_name');

        $result = $this->agency_model->is_downline_of($master_username, $target_username);

        $arr = [ 'success' => true, 'result' => $result ];
        $this->returnJsonResult($arr);
    }

    /**
     *  agent report for agency sub system in which each agent has a parent agent id
     *
     *  @return
     */
    public function agency_agent_report($agent_id = null) {
        if(!$this->check_login_status()){
            return;
        }

        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        if ( ! $agent_id && (empty($date_from) || empty($date_to))) {
            $date_from = date('Y-m-d ' . Utils::FIRST_TIME);
            $date_to = date('Y-m-d ' . Utils::LAST_TIME);
        }

        $session_agent_id = $this->session->userdata('agent_id');
        $agent_id = $agent_id ? : $session_agent_id;


        if ($session_agent_id == $agent_id || $this->agency_model->is_upline($agent_id, $session_agent_id)) {

            $agent_details = $this->agency_model->get_agent_by_id($agent_id);

            $data['agent_id']   = $agent_id;
            $data['date_from']  = $date_from;
            $data['date_to']    = $date_to;
            $data['agent_name'] = $agent_details['agent_name'];
            $data['parent_id']  = $agent_details['parent_id'];

            $this->load_template(lang('Sub Agent Report'), '', '', 'report');
            $this->template->write_view('main_content', 'agency/view_agent_report', $data);
            $this->template->render();

        } else {
            show_error('You don\'t have permission to view this agent');
        }
    }

    /**
     *  player report for agency sub system in which each player has a parent agent id
     *
     *  @return
     */
    public function agency_game_report() {
        if(!$this->check_login_status()){
            return;
        }

        $this->load->helper('form');
        $data['export_report_permission'] = TRUE;
        $data['agent_name'] = $this->session->userdata("agent_name");

        $data['conditions'] = $this->safeLoadParams(array(
            'date_from' => $this->utils->getTodayForMysql(),
            'hour_from' => '00',
            'date_to' => $this->utils->getTodayForMysql(),
            'hour_to' => '23',
            'total_bet_from' => '',
            'total_bet_to' => '',
            'total_loss_from' => '',
            'total_loss_to' => '',
            'total_gain_from' => '',
            'total_gain_to' => '',
            'group_by' => '',
            'username' => '',
            'affiliate_username' => '',
            'agent_name' => '',
        ));
        $this->utils->debug_log($data['conditions']);

        $this->load_template(lang('Games Report'), '', '', 'report');
        $this->template->write_view('main_content', 'agency/view_games_report', $data);
        $this->template->render();
    }

    /**
     *  create invoice file and supply downloading
     *
     *  @param
     *  @return
     */
    public function invoice($settlement_id = null) {
        if(!$this->check_login_status()){
            return;
        }

        $this->load_template(lang('Invoice'), '', '', 'agency');

        $this->template->add_js('resources/js/bootstrap-switch.min.js');
        $this->template->add_css('resources/css/bootstrap-switch.min.css');
        $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/amcharts.js'));
        $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/serial.js'));
        $this->template->add_js2($this->utils->thirdpartyUrl('amcharts/light.js'));
        $this->template->add_css('resources/third_party/bootstrap-multiselect-master/dist/css/bootstrap-multiselect.css');
        $this->template->add_js('resources/third_party/bootstrap-multiselect-master/dist/js/bootstrap-multiselect.js');

        $data = array();
        if ($settlement_id) {
            $data['settlement_id'] = $settlement_id;
        }
        $agent_id = $this->session->userdata('agent_id');
        $data['parent_id'] = $agent_id;
        $this->template->write_view('main_content', 'agency/invoice_page', $data);
        $this->template->render();
    }

    public function invoice_wl($invoice_id) {

        $post_invoice_id = $this->input->get('invoice_id');

        $invoice_id = $post_invoice_id ? $post_invoice_id : $invoice_id;

        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        if(empty($date_from) && empty($date_to)){
            $date_to = date("Y-m-d") . " 23:59:59";
            $date_from = date('Y-m-d', strtotime("-60 day", strtotime($date_to))) . " 00:00:00";
        }

        $data = [];
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['invoices'] = $this->agency_model->getInvoicesByDateRange($date_from, $date_to);

        if($invoice_id){
            $invoice = $this->agency_model->getInvoice($invoice_id);
            $agent_id = $invoice->agent_id;
            $agent_username = $invoice->agent_name;
            $date_from = $invoice->settlement_date_from;
            $date_to = $invoice->settlement_date_to;

            $data['conditions'] = $this->safeLoadParams(array(
                'agent_name' => $agent_username,
                'parent_name' => '',
                'status' => 'settle',
                'invoice_id' => $invoice_id,
            ));
        }

        if(!empty($agent_id)){

            $agent = $this->agency_model->get_agent_by_id($agent_id);

            list($rows, $summary) = $this->agency_model->getWsPlayerSettlement($agent_id, $agent_username, $date_from, $date_to);

            list($agent_rows, $agent_summary) = $this->agency_model->getWsSettlement($agent_id, $agent_username, $date_from, $date_to, 'settled');

            $data['rows'] = $rows;
            $data['agent_rows'] = $agent_rows;

            $data['agents'] = $this->agency_model->getAllActiveAgents();
            $data['agent_username'] = $agent_username;
            $data['agent'] = $agent;
            $data['summary'] = $summary;
            $data['agent_summary'] = $agent_summary;
        }

        $this->load_template(lang('Settlement'), '', '', 'agency');
        $this->template->add_js('resources/js/bootstrap-switch.min.js');
        $this->template->add_css('resources/css/bootstrap-switch.min.css');
        $this->template->write_view('main_content', 'agency/invoice_page_wl', $data);
        $this->template->render();
    }

    /**
     *  get invoice data throught ajax
     *
     *  @param  input post
     *  @return array
     */
    public function get_invoice_info_ajax() {
        $request = $this->input->post();
        $this->utils->debug_log('get_invoice_info_ajax', $request);
        $result = $this->agency_library->get_invoice_info($request);
        $this->utils->debug_log('get_invoice_info_ajax result', $result);

        $arr = array('status' => 'success', 'result' => $result);
        $this->returnJsonResult($arr);
    }

    public function get_invoice_wl_info_ajax() {
        $request = $this->input->post();
        $this->utils->debug_log('get_invoice_info_ajax', $request);
        $result = $this->agency_library->get_invoice_wl_info($request);
        $this->utils->debug_log('get_invoice_info_ajax result', $result);

        $arr = array('status' => 'success', 'result' => $result);
        $this->returnJsonResult($arr);
    }

    public function playerAction($action, $playerId) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $playerId)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $agent_id=$this->get_agent_id_from_session();

        $this->load->model(array('external_system'));
        switch ($action) {
            case self::ACTION_TRANSFER_FROM_SW:
                $data['transaction_title'] = lang('Subwallet to main wallet');
                $data['is_mainwallet'] = false;
                break;

            case self::ACTION_TRANSFER_TO_SW:
                $data['transaction_title'] = lang('Main wallet to sub wallet');
                $data['is_mainwallet'] = false;
                break;
        }

        $data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
        $data['playerDetails'] = $this->player_model->getPlayersSubWalletBalance($playerId, $data['game_platforms']);
        $data['transaction_type'] = $action;
        $data['player_account_info'] = $this->player_model->getPlayerAccountInfo($playerId);
        $data['player_signup_info'] = $this->player_model->getPlayerSignupInfoByAgentId($playerId, $agent_id);
        $data['player_signup_info']['typeOfPlayer'] = $this->player_model->getPlayerType($playerId);
        $this->load_template(lang('Transaction'), '', '', 'agency');
        $this->template->write_view('main_content', 'players/transact_player', $data);
        $this->template->render();
    }

    public function processTransaction($transaction_type) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        $player_id = $this->input->post('player_id');

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->load->model(array('transactions', 'users', 'external_system'));
        $agent_id = $this->get_agent_id_from_session();

        $amount = $this->input->post('transact_amount');
        $player_name = $this->player_model->getUsernameById($player_id);
        $subwallet = $this->input->post('subwallet_id');

        switch ($transaction_type) {
            case self::ACTION_TRANSFER_FROM_SW:
                $from_id = $subwallet; # Sub Wallet
                $to_id = 0; # Main Wallet
                $success=$this->utils->transferWallet($player_id, $player_name, $from_id, $to_id, $amount);
                $message = lang('Transfer from subwallet success');
                break;

            case self::ACTION_TRANSFER_TO_SW:
                $from_id = 0; # Main Wallet
                $to_id = $subwallet; # Sub Wallet
                $success=$this->utils->transferWallet($player_id, $player_name, $from_id, $to_id, $amount);
                $message = lang('Transfer to subwallet success');
                break;
        }

        if ($success) {
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
            redirect('agency/players_list?username='.$player_name);
        }else{
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save failed'));
            redirect('agency/players_list?username='.$player_name);
        }
    }

    public function player_notes($player_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->load->model(['player_model']);
        // $user_id = $this->authentication->getUserId();
        $data = array(
            // 'user_id' => $user_id,
            'player_id' => $player_id,
        );
        $data['notes'] = $this->player_model->getPlayerNotes($player_id);
        $this->load->view('agency/player_notes', $data);
    }

    public function add_player_notes($player_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $result['success'] = false;

        $this->load->model(['player_model']);
        $user_id = 0;//$this->authentication->getUserId();
        $notes = $this->input->post('notes');

        if ($notes) {

            $result['success'] = !!$this->player_model->addPlayerNote($player_id, $user_id, $notes);

            // if ($result['success']) {
                $this->saveAction('Agency Note', 'Add Note for Player', "Agency " . $this->get_agent_id_from_session() . " has added new note to player");
            // }

        }

        $this->returnJsonResult($result);
    }

    public function remove_player_note($note_id) {
        $logged_agent_id=null;
        if(!$this->check_login_status($logged_agent_id)){
            return;
        }

        $this->load->model(['player_model']);

        $player_id=$this->player_model->getPlayerIFromNoteId($note_id);

        if(!$this->agency_model->has_player_permission($logged_agent_id, $player_id)){
            return show_error('No permission', 403);
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $result['success'] = false;

        // $note = $this->player_manager->getNoteById($note_id);
        // $user_id = $this->authentication->getUserId();

        // if ($user_id == $note['userId']) {

            $result['success'] = !!$this->player_model->deleteNote($note_id);

            // if ($result['success']) {
                $this->saveAction('Agency Note', 'Delete Note for Player', "Agency " . $this->get_agent_id_from_session() . " has deleted note to player");
            // }

        // }

        $this->returnJsonResult($result);
    }

    public function bet_limit_template_list() {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        if ($this->utils->isEnabledFeature('hide_bet_limit_on_agency') || ! $this->session->userdata('show_bet_limit_template')) {
            return redirect('agency');
        }

        $data['agent_id'] = $this->get_agent_id_from_session();

        $this->load_template(lang('Bet Limit Template'), '', '');
        $this->template->write_view('main_content', 'players/bet_limit_template_list', $data);
        $this->template->render();
    }

    public function add_bet_limit_template($template_id=null, $game_platform_id = EBET_API) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        if ($this->utils->isEnabledFeature('hide_bet_limit_on_agency') || ! $this->session->userdata('show_bet_limit_template')) {
            return redirect('agency');
        }

        $api = $this->utils->loadExternalSystemLibObject($game_platform_id);
        if( ! $api || $api->isDisabled()){
            return redirect('agency');
        }

        $max_bet_limit=$api->getSystemInfo('max_bet_limit');
        $min_bet_limit=$api->getSystemInfo('min_bet_limit');
        if(empty($max_bet_limit)){
            $max_bet_limit=5000;
        }
        if(empty($min_bet_limit)){
            $min_bet_limit=0;
        }

        $data = array(
            'max_bet_limit'=>$max_bet_limit,
            'min_bet_limit'=>$min_bet_limit,
        );

        $this->load_template(lang('Bet Limit Template'), '', '');
        $this->template->write_view('main_content', 'players/player_bet_limit', $data);
        $this->template->render();
    }

    public function edit_bet_limit_template($template_id, $game_platform_id = EBET_API) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        if ($this->utils->isEnabledFeature('hide_bet_limit_on_agency') || ! $this->session->userdata('show_bet_limit_template')) {
            return redirect('agency');
        }

        $api = $this->utils->loadExternalSystemLibObject($game_platform_id);
        if( ! $api || $api->isDisabled()){
            return redirect('agency');
        }

        $query = $this->db->get_where('bet_limit_template_list', array('id' => $template_id));
        $template = $query->row_array();
        $template_name = $template['template_name'];
        $betLimit = json_decode($template['bet_limit_json'], true);

        $max_bet_limit=$api->getSystemInfo('max_bet_limit');
        $min_bet_limit=$api->getSystemInfo('min_bet_limit');
        if(empty($max_bet_limit)){
            $max_bet_limit=5000;
        }
        if(empty($min_bet_limit)){
            $min_bet_limit=0;
        }

        $data = array(
            'template_id' => $template_id,
            'default_template' => $template['default_template'],
            'public_to_downline' => $template['public_to_downline'],
            'template_name' => $template_name,
            'limit' => $betLimit,
            'max_bet_limit'=>$max_bet_limit,
            'min_bet_limit'=>$min_bet_limit,
        );

        $this->load_template(lang('Bet Limit Template'), '', '');
        $this->template->write_view('main_content', 'players/player_bet_limit', $data);
        $this->template->render();
    }

    public function post_bet_limit_template($template_id = null, $game_platform_id = EBET_API){
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $agent_id = $this->get_agent_id_from_session();

        $params = array_filter($this->input->post());

        $template_name = $params['template_name'];
        $default_template = $params['default_template'];
        $public_to_downline = $params['public_to_downline'];
        unset($params['template_name'], $params['default_template'], $params['public_to_downline']);

        $params = array_combine(array_map(function($field) {
            return str_replace('_', '.', $field);
        }, array_keys($params)), $params);

        $data = array(
            'game_platform_id' => $game_platform_id,
            'template_name' => $template_name,
            'public_to_downline' => $public_to_downline,
            'bet_limit_json' => json_encode($params),
            'updated_at' => date('Y-m-d H:i:s'),
        );


        if ($template_id) {

            # TODO: MOVE TO MODEL
            $this->db->update('bet_limit_template_list', $data, array('agent_id' => $agent_id, 'id' => $template_id));

        } else {

            # TODO: MOVE TO MODEL
            $data = array_merge($data, array(
                'agent_id' => $agent_id,
                'created_at' => date('Y-m-d H:i:s'),
                'note' => null,
                'status' => 1, # DEFAULT
            ));
            $this->db->insert('bet_limit_template_list', $data);
            $template_id = $this->db->insert_id();

        }

        if ($default_template) {
            $this->db->update('bet_limit_template_list', array('default_template' => NULL), array('agent_id' => $agent_id));
            $this->db->update('bet_limit_template_list', array('default_template' => 1), array('agent_id' => $agent_id, 'id' => $template_id));
        }

        //go back
        redirect('agency/edit_bet_limit_template/'.$template_id);
    }

    public function bet_detail($betHistoryId) {
        if(!$this->check_login_status()){
            return;
        }

        if(!empty($betHistoryId)){

            // $result = $this->db->get_where('ebet_game_logs', array('gameType' => 4))->result();
            $result = $this->db->get_where('ebet_game_logs', array('betHistoryId' => $betHistoryId))->result();
            foreach ($result as $row) {
                $this->load->view('agency/bet-detail', $row);
            }

        }
        // var_dump($row);
    }

    public function bet_result($betHistoryId) {
        if(!$this->check_login_status()){
            return;
        }

        if(!empty($betHistoryId)){
            // $result = $this->db->get_where('ebet_game_logs', array('gameType' => 3))->result();
            $result = $this->db->get_where('ebet_game_logs', array('betHistoryId' => $betHistoryId))->result();
            foreach ($result as $row) {
                $this->load->view('agency/bet-result', $row);
            }
        }
        // var_dump($row);
    }

    public function show_privilege_error(){
        return show_error(lang('No privilege'), 403);
    }

    public function player_rolling_comm() {
        $agent_id=null;
        if(!$this->check_login_status($agent_id)){
            return;
        }

        $agent_id = $this->session->userdata('agent_id');
        $data['parent_id'] = $agent_id;

        $data['conditions'] = $this->safeLoadParams(array(
            'sub_agent_username' => '',
            'player_username' => '',
            'date_from' => '',
            'date_to' => '',
            'status' => 'current',
        ));

        if($this->input->get('search_on_date')){
            $data['conditions']['search_on_date'] = $this->input->get('search_on_date')=='true';
        }else{
            $data['conditions']['search_on_date'] = false;
        }

        if($this->input->get('include_all_downlines')){
            $data['conditions']['include_all_downlines'] = $this->input->get('include_all_downlines')=='true';
        }else{
            $data['conditions']['include_all_downlines'] = true;
        }

        $controller=$this;
        $include_all_downlines=$data['conditions']['include_all_downlines'];
        //generate first
        $this->dbtransOnly(function() use($controller, $agent_id, $include_all_downlines){
            return $this->agency_model->generate_current_player_rolling_comm(
                $agent_id, $include_all_downlines);
        });

        $this->utils->debug_log('conditions', $data['conditions']);

        $this->load_template(lang('Rolling Commission'), '', '', '');
        // $this->template->add_js('resources/js/bootstrap-switch.min.js');
        // $this->template->add_css('resources/css/bootstrap-switch.min.css');

        //$this->template->write_view('nav_right', 'agency/navigation');
        $this->template->write_view('main_content', 'agency/player_rolling_comm', $data);
        $this->template->render();
    }

    public function settle_rolling($subWalletId=EBET_API){
        // $agent_id=null;
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $rolling_id=$this->input->post('rolling_id');
        $notes=$this->input->post('notes');
        $result=['success'=>false, 'message'=>lang('Settled rolling failed')];
        if(!empty($rolling_id)){
            $rollingInfo=$this->agency_model->get_rolling_by_id($rolling_id);
            $playerId=$rollingInfo['player_id']; // $this->agency_model->get_player_id_by_rolling_id($rolling_id);
            if(!empty($playerId)){

                $controller=$this;
                $agent_id=$this->player_model->get_agent_id_by_player_id($playerId);
                // $this->lockAndTransForPlayerBalance($playerId, function()
                //         use($controller, $agent_id, $rolling_id, $notes, &$result){


                $this->lockAndTrans(Utils::LOCK_ACTION_AGENCY_BALANCE , $agent_id, function()
                        use($controller, $agent_id, $playerId, $rolling_id, $notes, &$result){

                    $agent_info=$controller->agency_model->get_agent_by_id($agent_id);
                    $rollingInfo=$controller->agency_model->get_rolling_by_id($rolling_id);

                    $available_credit= $agent_info['available_credit'];
                    $amount=$rollingInfo['rolling_comm_amt'];
                    $agent_name=$agent_info['agent_name'];
                    if($amount>0){

                        if ($available_credit >= $amount) {
                            $result['amount']=$amount;
                            $result['success']=$this->agency_model->settle_rolling($agent_id,
                                $rolling_id, $notes, $result['message']);
                    if($result['success']){
                        $result['message']=lang('Settled rolling successfully');
                    }else{
                                if(empty($result['message'])){
                                    $result['message']=lang('Settled rolling failed');
                                }
                            }
                        }else{
                            $result['success']=false;
                            $result['message']=lang('No enough credit');
                        }
                    }else{
                        $result['success']=false;
                        $result['message']=lang('Amount should be >0');
                    }

                    return $result['success'];

                });

                if($result['success'] && !empty($result['amount'])){
                    $this->load->model(['external_system']);
                    //transfer to sub wallet
                    if(!empty($subWalletId) && $this->external_system->isGameApiActive($subWalletId)){
                        //go to sub wallet
                        $mainWallet=Wallet_model::MAIN_WALLET_ID;
                        $amount=$result['amount'];
                        $player_username=$this->player_model->getUsernameById($playerId);
                        $success=$this->utils->transferWallet($playerId,$player_username,$mainWallet, $subWalletId, $amount);
                        if(!$success){
                            $result['success']=$success;
                            $result['message']=lang('Transfer to subwallet failed');
                            $this->utils->error_log('transfer faield', $subWalletId, 'amount', $amount, 'player', $player_username);
                        }
                    }else{
                        $this->utils->debug_log('sub wallet id is empty or not active', $subWalletId);
                    }
                }

            }else{
                $result['message']=lang('Lost player');
            }
        }else{
            $result['message']=lang('Lost ID');
        }

        return $this->returnJsonResult($result);
    }

    public function pending_rolling(){
        $agent_id=null;
        if(!$this->check_login_status($agent_id)){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $rolling_id=$this->input->post('rolling_id');
        $notes=$this->input->post('notes');
        $result=['success'=>false, 'message'=>lang('Set rolling to pending failed')];
        if(!empty($rolling_id)){
            $playerId=$this->agency_model->get_player_id_by_rolling_id($rolling_id);
            if(!empty($playerId)){
                $controller=$this;
                $this->lockAndTransForPlayerBalance($playerId, function()
                        use($controller, $agent_id, $rolling_id, $notes, &$result){

                    $result['success']=$controller->agency_model->pending_rolling($agent_id, $rolling_id, $notes, $message);
                    if($result['success']){
                        $result['message']=lang('Set rolling to pending successfully');
                    }else{
                        $result['message']=!empty($message) ? $message : lang('Set rolling to pending failed');
                    }

                    return $result['success'];

                });
            }else{
                $result['message']=lang('Lost player');
            }
        }else{
            $result['message']=lang('Lost ID');
        }

        return $this->returnJsonResult($result);
    }

    private function getPlayerDailySettlement($agent_id, $agent_username, $date_from, $date_to){
        $this->db->select('agency_daily_player_settlement.player_id');
        $this->db->select('player.username as player_username');
        $this->db->select('agency_agents.agent_name as agent_username');
        $this->db->select_sum('agency_daily_player_settlement.bets');
        $this->db->select_sum('agency_daily_player_settlement.real_bets');
        $this->db->select_sum('agency_daily_player_settlement.result_amount');
        $this->db->select_sum('agency_daily_player_settlement.player_commission');
        $this->db->select_sum('agency_daily_player_settlement.rev_share_amt');
        $this->db->select_sum('agency_daily_player_settlement.agent_commission');
        $this->db->select_sum('agency_daily_player_settlement.roll_comm_income');
        $this->db->select_sum('agency_daily_player_settlement.rev_share_amt');
        $this->db->join('player','player.playerId = agency_daily_player_settlement.player_id','left');
        $this->db->join('agency_agents','agency_agents.agent_id = player.agent_id','left');
        $this->db->where('agency_daily_player_settlement.agent_id', $agent_id);

        if ($agent_username) {
            $this->db->where('agency_agents.agent_name', $agent_username);
        }

        $this->db->where('DATE(agency_daily_player_settlement.settlement_date) >=', date('Y-m-d', strtotime($date_from)));

        $this->db->where('DATE(agency_daily_player_settlement.settlement_date) <=', date('Y-m-d', strtotime($date_to)));

        $this->db->group_by('agency_daily_player_settlement.player_id');
        $this->db->order_by('player_username');
        // $this->db->group_by('agent_username');
        $query = $this->db->get('agency_daily_player_settlement');

        $this->utils->debug_log('getPlayerDailySettlement sql_query: ' . $this->db->last_query());

        $rows = $query->result_array();

        $summary['bets'] = 0;
        $summary['real_bets'] = 0;
        $summary['result_amount'] = 0;
        $summary['player_commission'] = 0;
        $summary['agent_commission'] = 0;
        $summary['rev_share_amt'] = 0;

        $summary['player_wl_com'] = 0;
        $summary['agent_wl_com'] = 0;
        $summary['upper_wl'] = 0;
        $summary['upper_com'] = 0;
        $summary['upper_wl_com'] = 0;

        if(!empty($rows)){
            foreach ($rows as &$row) {
                $row['player_username'] = "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='".lang('Show Player Info')."' onclick=\"show_player_game_history('".$row['player_username']."','".date("Y-m-d 00:00:00", strtotime($date_from))."','".date("Y-m-d 23:59:59", strtotime($date_to))."')\">".$row['player_username']."</a> ";
                $row['settlement_date_from'] = date('Y-m-d', strtotime($date_from));
                $row['settlement_date_to'] = date('Y-m-d', strtotime($date_to));

                $summary['bets'] += $row['bets'];
                $summary['real_bets'] += $row['real_bets'];
                $summary['result_amount'] += $row['result_amount'];
                $summary['player_commission'] += $row['player_commission'];
                $summary['agent_commission'] += $row['agent_commission'];
                $summary['rev_share_amt'] += $row['rev_share_amt'];

                $summary['player_wl_com'] += $row['result_amount'] + $row['player_commission'];
                $summary['agent_wl_com'] += $row['rev_share_amt'] + $row['agent_commission'];
                $summary['upper_wl'] += - $row['result_amount'] - $row['rev_share_amt'];
                $summary['upper_com'] += - $row['player_commission'] - $row['agent_commission'];
                $summary['upper_wl_com'] += ( - $row['result_amount'] - $row['rev_share_amt']) + ( - $row['player_commission'] - $row['agent_commission']);
            }
        }

        return [$rows, $summary];
    }

    private function getAgentDailySettlement($agent_id, $agent_username, $date_from, $date_to){
        $this->db->select('agency_agents.agent_id');
        $this->db->select('agency_daily_player_settlement.player_id');
        $this->db->select('player.username as player_username');
        $this->db->select('agency_agents.agent_name as agent_username');
        $this->db->select_sum('agency_daily_player_settlement.bets');
        $this->db->select_sum('agency_daily_player_settlement.real_bets');
        $this->db->select_sum('agency_daily_player_settlement.result_amount');
        $this->db->select_sum('agency_daily_player_settlement.player_commission');
        $this->db->select_sum('agency_daily_player_settlement.rev_share_amt');
        $this->db->select_sum('agency_daily_player_settlement.agent_commission');
        $this->db->select_sum('agency_daily_player_settlement.roll_comm_income');
        $this->db->select_sum('agency_daily_player_settlement.rev_share_amt');
        $this->db->join('player','player.playerId = agency_daily_player_settlement.player_id','left');
        $this->db->join('agency_agents','agency_agents.agent_id = agency_daily_player_settlement.agent_id','left');
        $this->db->where('agency_agents.parent_id', $agent_id);

        if ($agent_username) {
            $this->db->where('agency_agents.agent_name', $agent_username);
        }

        $this->db->where('DATE(agency_daily_player_settlement.settlement_date) >=', date('Y-m-d', strtotime($date_from)));

        $this->db->where('DATE(agency_daily_player_settlement.settlement_date) <=', date('Y-m-d', strtotime($date_to)));

        // $this->db->group_by('agency_daily_player_settlement.player_id');
        // $this->db->group_by('player_username');
        $this->db->group_by('agency_agents.agent_name');
        $query = $this->db->get('agency_daily_player_settlement');

        $this->utils->debug_log('getAgentDailySettlement sql_query: ' . $this->db->last_query());

        $rows = $query->result_array();

        $summary['bets'] = 0;
        $summary['real_bets'] = 0;
        $summary['result_amount'] = 0;
        $summary['player_commission'] = 0;
        $summary['agent_commission'] = 0;
        $summary['rev_share_amt'] = 0;

        $summary['player_wl_com'] = 0;
        $summary['agent_wl_com'] = 0;
        $summary['upper_wl'] = 0;
        $summary['upper_com'] = 0;
        $summary['upper_wl_com'] = 0;

        if(!empty($rows)){
            foreach ($rows as &$row) {
                $row['agent_username'] = "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='".lang('Show Agent Win / Loss Report')."' onclick=\"show_agent_players_win_loss(".$row['agent_id'].",'".date("Y-m-d 00:00:00", strtotime($date_from))."','".date("Y-m-d 23:59:59", strtotime($date_to))."')\">".$row['agent_username']."</a> ";
                $row['settlement_date_from'] = date('Y-m-d', strtotime($date_from));
                $row['settlement_date_to'] = date('Y-m-d', strtotime($date_to));

                $summary['bets'] += $row['bets'];
                $summary['real_bets'] += $row['real_bets'];
                $summary['result_amount'] += $row['result_amount'];
                $summary['player_commission'] += $row['player_commission'];
                $summary['agent_commission'] += $row['agent_commission'];
                $summary['rev_share_amt'] += $row['rev_share_amt'];

                $summary['player_wl_com'] += $row['result_amount'] + $row['player_commission'];
                $summary['agent_wl_com'] += $row['rev_share_amt'] + $row['agent_commission'];
                $summary['upper_wl'] += - $row['result_amount'] - $row['rev_share_amt'];
                $summary['upper_com'] += - $row['player_commission'] - $row['agent_commission'];
                $summary['upper_wl_com'] += ( - $row['result_amount'] - $row['rev_share_amt']) + ( - $row['player_commission'] - $row['agent_commission']);
            }
        }

        return [$rows, $summary];
    }

    public function win_loss_report() {

        $agent_id = null;

        if ( ! $this->check_login_status($agent_id)) {
            return;
        }

        $agent_username = $this->input->get('agent_username');

        if ($this->input->get('agent_id')) {
            $agent_id = $this->input->get('agent_id');
        }

        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        if(empty($date_from) && empty($date_to)){
            $date_to = date("Y-m-d") . " 23:59:59";
            $date_from = date('Y-m-d', strtotime("-7 day", strtotime($date_to))) . " 00:00:00";
        }

        list($rows, $summary) = $this->getPlayerDailySettlement($agent_id, $agent_username, $date_from, $date_to);

        list($agent_rows, $agent_summary) = $this->getAgentDailySettlement($agent_id, $agent_username, $date_from, $date_to);

        $data = array('rows' => $rows, 'agent_rows'=>$agent_rows);

        $data['agent_username'] = $agent_username;
        $data['agent'] = $this->agency_model->get_agent_by_id($agent_id);
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['summary'] = $summary;
        $data['agent_summary'] = $agent_summary;

        $this->load_template(lang('Win / Loss Report'), '', '', '');
        $this->template->add_js('resources/js/bootstrap-switch.min.js');
        $this->template->add_css('resources/css/bootstrap-switch.min.css');
        $this->template->write_view('main_content', 'agency/win_loss_report', $data);
        $this->template->render();
    }

    public function register($parentCode='', $showHeader = 'true') {
        $this->load_template(lang('Agency System'), '', '');

        $data['currency'] = $this->utils->getConfig('default_currency');

        $this->load->model(['agency_model']);
        $parentId = 0;
        $code = NULL;
        if (!empty($parentCode)) {
            $code = $parentCode;
        } elseif ($this->getAgentTrackingCodeFromDomain()) {
            $code = $this->getAgentTrackingCodeFromDomain();
        }

        if (!empty($code)) {
            $parentAgent = $this->agency_model->get_agent_by_tracking_code($code);
            $this->utils->debug_log(__METHOD__, 'can_have_sub_agent', $parentAgent['can_have_sub_agent'], 'parentCode', $parentCode);
            // OGP-12478: check value of can_have_sub_agent for parent agent
            if (!empty($parentAgent) && empty($parentAgent['can_have_sub_agent'])) {
                if ($this->utils->isEnabledFeature('hide_reg_page_for_subagent_link_if_parent_agent_cannot_have_subagents')) {
                    $this->utils->debug_log(__METHOD__, 'redirecting to login');
                    $this->alertMessage(self::MESSAGE_TYPE_WARNING, lang('agency.subagent_reg_disabled'));
                    redirect('agency');
                    return;
                }
            }
            else {
                $parentId = $parentAgent['agent_id'];
            }
        }

        $data['trackingCode'] = $code;
        $data['parentId'] = $parentId;
        $data['agency_registration_fields']=$this->utils->getConfig('agency_registration_fields');

        $data['current_language_name'] = $this->load->get_var('current_language_name');

        $currenTemplate = $this->utils->getConfig('agency_view_template');

        // if ($currenTemplate == 'agency') {

        //     if ($showHeader == 'true') {
        //         $this->template->write_view('nav_right', $this->utils->getConfig('agency_view_template') . '/navigation');
        //     }
        // }

        $this->template->write_view('main_content', $this->utils->getConfig('agency_view_template') . '/register', $data);

        $this->template->render();
    }

	/**
	 * overview : registration form rules
	 *
	 * @return	void
	 */
	public function register_form_rules() {
		$this->form_validation->set_rules('username', lang('aff.al10'), 'trim|required|min_length[5]|max_length[12]|alpha_numeric|is_unique[agency_agents.agent_name]');
		$this->form_validation->set_rules('password', lang('reg.05'), 'trim|required|min_length[6]|max_length[12]');
		$this->form_validation->set_rules('confirm_password', lang('reg.07'), 'trim|required|callback_confirmPassword');
		$this->form_validation->set_rules('email', lang('reg.a37'), 'trim|xss_clean|required|valid_email|is_unique[agency_agents.email]');

        $this->form_validation->set_rules('firstname', lang('aff.al14'), 'trim|xss_clean');
        $this->form_validation->set_rules('lastname', lang('aff.al15'), 'trim|xss_clean');
        $this->form_validation->set_rules('mobile', lang('reg.a54'), 'trim|xss_clean|numeric');
        $this->form_validation->set_rules('language', lang('ban.lang'), 'trim');
        $this->form_validation->set_rules('note', lang('Note'), 'trim|xss_clean');
	}

    public function verifyRegister($parentCode= '') {

        $this->load->model(['agency_model']);

        $this->register_form_rules();
        $this->utils->debug_log('agency VERIFYREGISTER post', $this->input->post());

        if ($this->form_validation->run() == false) {
            $message = validation_errors();
            $this->utils->debug_log('agency VERIFYREGISTER error', $message);

            $this->register($parentCode);
        } else {
            $this->utils->debug_log('agency VERIFYREGISTER validation PASS');

            $agent_id=null;
            $username=$this->input->post('username');
            $success=$this->utils->globalLockAgencyRegistration($username, function()
                    use(&$agent_id, $username){
                $agent_id = $this->addNewAgency($username);
                $success=!empty($agent_id);
                if($success){
                    $this->syncAgentCurrentToMDB($agent_id, false);
                }
                return $success;
            });

            $this->utils->debug_log('agency VERIFYREGISTER agent_id', $agent_id);

            if(!$success){
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Register agency failed'));
                $this->register($parentCode);
                return;
            }

            $contactTypeLabel = $this->config->item('agency_contact_type_label');
            $contactType = $this->config->item('agency_contact_type');

            $message = lang('con.24');
            if(!empty($this->config->item('agency_contact_qq'))) {
                $message .= '<br/><b>'.lang('aff.login.contact.qq').'</b>: '.$this->config->item('agency_contact_qq');
            }
            if(!empty($this->config->item('agency_contact_email'))) {
                $message .= '<br/><b>'.lang('aff.login.contact.email').'</b>: '.$this->config->item('agency_contact_email');
            }
            if(!empty($this->config->item('agency_contact_skype'))) {
                $message .= '<br/><b>'.lang('aff.login.contact.skype').'</b>: '.$this->config->item('agency_contact_skype');
            }
            if(!empty($contactTypeLabel) && !empty($contactType)) {
                $message = lang('con.24') . '<br><b>' . lang('aff.aai93') . '</b>: ' . $contactTypeLabel . ': ' . $contactType . '<br><b>' . lang('con.22') . '</b> <a href="mailto:' . $this->config->item('agency_contact_email') . '" style="color: #fff;">' . $this->config->item('agency_contact_email') . '</a>';
            }
            if(!empty($this->config->item('use_successfully_registered_msg'))) {
                $message = lang('con.24');
            }
            $this->alertMessage(1, $message);

            redirect("/");
        }
    }

    protected function addNewAgency($username) {
        $this->load->model(array('agency_model'));
        $this->utils->debug_log('add_new_agent TTT username', $username);
        $trackingCode=$username;
        $this->load->helper('string');
        while (! $this->agency_model->is_unique_tracking_code($trackingCode, null)) {
            if ($this->utils->isEnabledFeature('agency_tracking_code_numbers_only')) {
                $trackingCode = random_string('numeric', 8);
            } else {
                $trackingCode = random_string('alpha_numeric', 8);
            }
        }
        $this->utils->debug_log('add_new_agent TTT trackingCode', $trackingCode);

        $agent_level = 0;
        $parentId = 0;
        if ($this->input->post('parentId') != NULL) {
            $parentId = $this->input->post('parentId');
            if ($parentId > 0){
                $parentAgent = $this->agency_model->get_agent_by_id($parentId);
                $this->utils->debug_log('add_new_agent TTT parentId, parentAgent', $parentId, $parentAgent);
                $parentLevel = $parentAgent['agent_level'];
                $agent_level = $parentLevel + 1;
            }
        }
        $this->utils->debug_log('add_new_agent TTT parentId', $parentId);
        $lastname = $this->input->post('lastname');
        $data = array(
            'parent_id' => $parentId,
            'agent_level' => $agent_level,
            'agent_name' => $username, // $this->input->post('username'),
            'password' => $this->salt->encrypt($this->input->post('password'), $this->getDeskeyOG()),
            'firstname' => $this->input->post('firstname'),
            'lastname' => $lastname,
            'gender' => $this->input->post('gender'),
            'email' => $this->input->post('email'),
            'mobile' => $this->input->post('mobile'),
            'im1' => $this->input->post('im1'),
            'im2' => $this->input->post('im2'),
            'currency' => isset($parentAgent['currency']) ? $parentAgent['currency'] : $this->utils->getConfig('default_currency'),
            'status' => 'active', //(empty($trackingCode)) ? '1':'0',
            'active'                    => '1',
            'created_on' => $this->utils->getNowForMysql(),
            'tracking_code' => $trackingCode,
            'language' => $this->input->post('language'),
            'note' => $this->input->post('note'),
            'can_have_sub_agent' => 1,
            'can_have_players' => 1,
            'show_bet_limit_template' => 1,
            'show_rolling_commission' => 1,
            'can_view_agents_list_and_players_list' => 1,
            'settlement_period' => 'Weekly', # Default settlement period in db is wrong
            'settlement_start_day' => 'Monday', # Default settlement period in db is wrong
            'vip_level' => isset($parentAgent['vip_level']) ? $parentAgent['vip_level'] : null,
        );
        $structure_id = $this->register_use_default_template($data);
        if(!empty($structure_id)) {
            $this->copy_settings_from_template($data, $structure_id);
        }
        $this->utils->debug_log('add_new_agent DATA', $data);

        $this->agency_model->startTrans();

        $agent_id = $this->agency_model->add_agent($data);

        if(!empty($structure_id)) {
            $game_platforms = $this->agency_model->get_structure_game_platforms($structure_id);
            $game_types = $this->agency_model->get_structure_game_types($structure_id);
            $this->utils->debug_log('copy structure game settings: ', $game_platforms, $game_types);
            foreach ($game_platforms as &$game_platform) {
                $game_platform['agent_id'] =  $agent_id;
                unset($game_platform['id']);
                unset($game_platform['structure_id']);
            }
            foreach ($game_types as &$game_type) {
                $game_type['agent_id'] =  $agent_id;
                unset($game_type['id']);
                unset($game_type['structure_id']);
            }
            $this->utils->debug_log('copy structure game settings222: ', $game_platforms, $game_types);

            $game_platform_table = 'agency_agent_game_platforms';
            $game_type_table = 'agency_agent_game_types';
            if(!empty($game_platforms)){
                $this->db->insert_batch($game_platform_table, $game_platforms);
            }
            if(!empty($game_types)){
                $this->db->insert_batch($game_type_table, $game_types);
            }
        }

        $succ = $this->agency_model->endTransWithSucc();
        if ( ! $succ) {
            return null;
            // throw new Exception('Sorry, failed to save new registered agent.');
        }

        return $agent_id;
    }

    /**
     *  copy agent settings from default template
     *
     *  @param  array  for data
     *  @param  int structure_id
     *  @return void
     */
    private function copy_settings_from_template(&$data, $structure_id) {
        $structure_details = $this->agency_model->get_structure_by_id($structure_id);

        $agent_level = 0;
        $currency = $data['currency'];
        if(isset($data['parent_id']) && !empty($data['parent_id']) && $data['parent_id'] > 0) {
            $parent_id = $data['parent_id'];
            $parent_agent = $this->agency_model->get_agent_by_id($parent_id);
            $agent_level = $parent_agent['agent_level'] + 1;
            $currency = $parent_agent['currency'];
        }

        $data['can_have_sub_agent'] = $structure_details['can_have_sub_agent'];
        $data['can_have_players'] = $structure_details['can_have_players'];
        $data['show_bet_limit_template'] = $structure_details['show_bet_limit_template'];
        $data['show_rolling_commission'] = $structure_details['show_rolling_commission'];
        $data['can_view_agents_list_and_players_list'] = $structure_details['can_view_agents_list_and_players_list'];
        $data['settlement_period'] = $structure_details['settlement_period'];
        $data['settlement_start_day'] =$structure_details['settlement_start_day'];
        $data['admin_fee'] = $structure_details['admin_fee'];
        $data['transaction_fee'] = $structure_details['transaction_fee'];
        $data['bonus_fee'] = $structure_details['bonus_fee'];
        $data['cashback_fee'] = $structure_details['cashback_fee'];
        $data['min_rolling_comm'] = $structure_details['min_rolling_comm'];
        $data['vip_level'] = $structure_details['vip_level'];
        $data['credit_limit'] = $structure_details['credit_limit'];

        $data['agent_level'] = $agent_level;
        $data['currency'] = $currency;
    }

    /**
     *  can use a default agent template when registering
     *
     *  @param  array data
     *  @return array game_comm_settings
     */
    private function register_use_default_template(&$data) {
        $parent_id = null;
        if (!isset($data['parent_id']) || empty($data['parent_id']) || $data['parent_id'] == 0) {
            $default_parent_agent = $this->config->item('default_parent_agent');
            if (isset($default_parent_agent) && !empty($default_parent_agent)){
                $parent_id = $this->agency_model->get_agent_id_by_agent_name($default_parent_agent);
                $data['parent_id'] = !empty($parent_id)? $parent_id : 0;
            }
        } else {
            $parent_id = $data['parent_id'];
        }

        $default_agent_template = $this->config->item('default_agent_template');
        $this->utils->debug_log('default_agent_template: ', $default_agent_template);
        $message = "no default template given on agent registration";
        if (isset($default_agent_template) && !empty($default_agent_template)){
            $structure_id = $this->agency_model->get_structure_id_by_structure_name($default_agent_template);
            $chk = false;
            if(!empty($structure_id)) {
                $chk = $this->copy_template_check_values($message, $structure_id, $parent_id);
            }
            if($chk) {
                return $structure_id;
            }
        }
        $this->utils->debug_log('Failed to use default agent template on registration. Message: ', $message);
        //TODO: send error message to elasticsearch and alert
        return null;
    }

    public function validateThruAjax() {

        $this->form_validation->set_message('is_unique', lang('formvalidation.is_unique'));

        if ($this->input->post('username')) {
            $this->form_validation->set_rules('username', lang('aff.al10'), 'trim|required|min_length[5]|max_length[12]|alpha_numeric|is_unique[agency_agents.agent_name]');
        }
        if ($this->input->post('email')) {
            $this->form_validation->set_rules('email', lang('reg.a37'), 'trim|xss_clean|required|valid_email|is_unique[agency_agents.email]');
        }

        if ($this->form_validation->run() === false) {
            $arr = array('status' => 'error', 'msg' => validation_errors());
            $this->returnJsonResult($arr);
        } else {
            $arr = array('status' => 'success', 'msg' => "");
            $this->returnJsonResult($arr);
        }
    }

	/**
	 * create tracking code
	 *
	 * @return	void
	 */
	public function edit_tracking_code($agent_id) {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $userId = $this->session->userdata('agent_id');

        if($this->input->is_ajax_request()){
            $result = $this->verifyEditTrackingCode($agent_id, $userId, NULL);

            return $this->returnJsonResult($result);
        }else{
            $redirectUrl = 'agency/agent_information/' . $agent_id.'#agent_tracking_code';

            return false;
        }
	}

	public function log_unlock_trackingcode() {
        $agent_name = $this->session->userdata('agent_name');
		$this->saveAction('Unlock Tracking Code', "Agent " . $agent_name . " has unlock tracking code");
	}

    public function new_additional_agent_domain($agent_id){
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $redirectUrl = 'agency/agent_information/' . $agent_id.'#agent_tracking_code';

        $this->verifyNewAdditionalAgentDomain($agent_id, $redirectUrl);
    }

    // bank info and payment
    /**
     *  An agent can lanunch a withdraw request
     *
     *  @param  int $walletType
     *  @return
     */
    public function withdrawRequest($walletType = 'main') {

        if ( ! $this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $agent_id = $this->session->userdata('agent_id');
        $agent = $this->agency_model->get_agent_by_id($agent_id);

        if (empty($agent['withdraw_password_md5'])) {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('You have no withdrawal password. Create first to process transaction'));
            redirect('agency/reset_withdrawal_password','refresh');
        }

        $this->load_template(lang('Agency'), '', '');

        $this->load->model(array('transactions'));
        $data['agent'] = $agent;
        $data['agent_id'] = $agent_id;
        $data['payment_histories'] = $this->transactions->getAgentTransactions($agent_id);
        $data['payment_methods'] = $this->agency_model->get_payment_by_agent_id($agent_id);
        $data['walletType'] = $walletType;
        $data['target'] = $this->input->get("target");

        $this->addBoxDialogToTemplate();

        $this->addJsTreeToTemplate();


        $this->template->write_view('main_content', 'agency/view_new_payment', $data);
        $this->template->render();
    }

    /**
     * modify password  page
     *
     * @param int agent_id
     * @return  void
     */
    public function reset_withdrawal_password() {

        if( ! $this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $agent_id = $this->session->userdata('agent_id');

        $data['agent_id'] = $agent_id;

        $this->load_template(lang('Reset Password'), '', '', 'agency');
        $this->template->write_view('main_content', 'agency/reset_withdrawal_password', $data);
        $this->template->render();
    }

    /**
     * verify change password
     *
     * @return  void
     */
    public function verify_reset_withdrawal_password() {

        if( ! $this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $agent_id = $this->session->userdata('agent_id');

        $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean');
        $this->form_validation->set_rules('confirm_new_password', 'Confirm New Password',
            'trim|required|xss_clean|matches[new_password]');

        if ($this->form_validation->run() == false) {

            $this->reset_withdrawal_password();

        } else {

            $password = md5($this->input->post('new_password'));

            $this->agency_model->startTrans();

            $data = array(
                'withdraw_password_md5' => $password,
            );

            $this->agency_model->update_agent($agent_id, $data);

            $succ = $this->agency_model->endTransWithSucc();

            if ( ! $succ) {
                throw new Exception('Sorry, reset withdrawal password failed.');
            }

            $username=$this->agency_model->getAgentNameById($agent_id);
            $this->syncAgentCurrentToMDBWithLock($agent_id, $username, false);

            $message = lang('Successfully Reset Withdrawal Password.');

            $this->alertMessage(1, $message);

            redirect("agency/withdrawRequest/main/" . $agent_id, "refresh");

        }
    }

    /**
     *  process withdraw request in agency side
     *
     *  @param  string $walletType
     *  @return
     */
    public function processWithdrawRequest() {
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $walletType = $this->input->post('wallet_type');
        $is_transfer_binding_player = $this->input->post('is_transfer_binding_player');

        if(!$is_transfer_binding_player) {
            $this->form_validation->set_rules('payment_method', lang('aff.ai56'), 'trim|xss_clean|required');
        }

        $this->form_validation->set_rules('withdrawal_password', lang('Withdrawal Password'), 'trim|xss_clean|required|callback_checkWithdrawalPassword');
        $this->form_validation->set_rules('request_amount', lang('pay.reqamt'),
            'trim|xss_clean|required|numeric|callback_checkRequestAmount[' . $walletType . ']');

        if ($this->form_validation->run() == false) {
            $this->withdrawRequest($walletType);
        } elseif($is_transfer_binding_player) {
            $agent_id = $this->session->userdata('agent_id');
            $amount = $this->input->post('request_amount');
            return $this->agent_transfer_balance_to_binding_player($agent_id, $walletType, $amount, "agency/withdrawRequest/" . $walletType."?target=player");
        } else {
            $agent_id = $this->session->userdata('agent_id');
            $paymentMethodId = $this->input->post('payment_method');
            $amount = $this->input->post('request_amount');
            $payment = $this->agency_model->get_payment_by_id($paymentMethodId);
            $this->utils->debug_log('processWithdrawRequest: VARS', $agent_id, $paymentMethodId, $amount, $walletType);

            $success = $this->lockAndTransForAgencyBalance($agent_id, function ()
                use ($amount, $agent_id, $paymentMethodId, $payment, $walletType) {

                    if ($walletType == 'main') {
                        $bal = $this->agency_model->getMainWallet($agent_id);
                    } else {
                        $bal = $this->agency_model->getBalanceWallet($agent_id);
                    }

                    if ($this->utils->compareResultFloat($bal, '>=', $amount)) {
                        $success = $this->agency_model->addWithdrawRequest($agent_id, $payment, $amount, $walletType);
                    } else {
                        $this->utils->error_log('do not have enough balance', $agent_id, $amount, 'wallet balance', $bal);
                        $success = false;
                    }

                    return $success;
                });

            if ($success) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('New withdrawal has been successfully added'));
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
            }

            redirect("agency/withdrawRequest/" . $walletType);
        }
    }

	/**
	 * overview : callback check request amount
	 *
	 * @return	void
	 */
    public function checkRequestAmount($amount, $walletType) {
        $agent_id = $this->session->userdata('agent_id');
        $request_amount = $this->input->post('request_amount');
        // $available_balance = $this->input->post('amount');
        if ($walletType == 'main') {
            $available_balance = $this->agency_model->getMainWallet($agent_id);
        } else {
            $available_balance = $this->agency_model->getBalanceWallet($agent_id);
        }

        if ($request_amount == 0 || empty($request_amount)) {
            $this->form_validation->set_message('checkRequestAmount', lang('con.19'));
            return false;
        } else if ($request_amount > $available_balance) {
            $this->form_validation->set_message('checkRequestAmount', lang('con.18'));
            return false;
        }

        return true;
    }

    public function checkWithdrawalPassword($withdrawal_password) {

        $agent_id = $this->session->userdata('agent_id');
        $agent = $this->agency_model->get_agent_by_id($agent_id);
        $withdrawal_password = md5($withdrawal_password);

        if ($withdrawal_password != $agent['withdraw_password_md5']) {
            $this->form_validation->set_message('checkWithdrawalPassword', lang('Please check withdrawal password you entered'));
            return false;
        }

        return true;
    }

    public function dashboard(){
        $agent_id=null;
        if(!$this->check_login_status($agent_id)){
            return;
        }

        $this->load_template(lang('Agency Dashboard'), '', '', 'agency');
        $this->template->add_js('resources/js/bootstrap-switch.min.js');
        $this->template->add_css('resources/css/bootstrap-switch.min.css');
        $this->template->add_css('resources/css/dashboard.css');

        $this->load->model(array('transactions','total_player_game_day'));

        $data=null;

        if(!$this->utils->isEnabledFeature('disable_agent_dashboard')){

            $cache_key='agency-dashboard-'.$agent_id;

            //try get cache
            $jsonArr=$this->utils->getJsonFromCache($cache_key);
            if(!empty($jsonArr)){
                // $jsonArr=$this->utils->decodeJson($jsonData);
                // if(!empty($jsonArr)){
                    $data=$jsonArr;
                // }
            }

            if(empty($data)){

                $data = [
                    'total_active_players'=>0,
                    'total_sub_accounts'=>0,
                    'count_player_session'=>0,
                    'today_member_count'=>0,
                    'yesterday_member_count'=>0,
                    'all_member_count'=>0,
                    'all_member_deposited'=>0,
                    'total_all_balance_include_subwallet'=>0,
                    'today_deposit_sum'=>0,
                    'today_deposited_player'=>0,
                    'today_deposit_count'=>0,
                    'today_withdrawal_sum'=>0,
                    'today_withdrawed_player'=>0,
                    'today_withdraw_count'=>0,
                ];

                // $this->transactions->getDashboard();
                // $agent_id = $this->session->userdata('agent_id');
                // $agent_nodes = $this->agency_model->get_agent_node_players_and_sugAgent_ids($agent_id);

                $all_agent_ids=$this->agency_model->get_all_sub_agent_ids($agent_id);
                $all_agent_ids=array_unique($all_agent_ids);
                $player_ids=$this->agency_model->get_player_id_array_by_agent_id_array($all_agent_ids);

                $this->utils->debug_log('got agent id:'.count($all_agent_ids).', player id:'.count($player_ids));
                // $player_ids = $agent_nodes['players_id'];

                if(!empty($player_ids)){

                    $data['total_active_players']   = $this->total_player_game_day->getTodayActivePlayers($player_ids);
                    $data['total_sub_accounts']     = count($all_agent_ids)-1;
                    $data['count_player_session']   = $this->player_model->countPlayerSession(new DateTime('-1 hour'), $player_ids);
                    $data['today_member_count']     = $this->player_model->totalRegisteredPlayersByDate(date('Y-m-d'), $player_ids);
                    $data['yesterday_member_count'] = $this->player_model->totalRegisteredPlayersByDate(date('Y-m-d', strtotime('-1 day')), $player_ids);
                    $data['all_member_count']       = count($player_ids); // $this->player_model->totalRegisteredPlayers(null, $player_ids);
                    $data['all_member_deposited']   = $this->player_model->totalPlayerDeposited($player_ids);
                    $data['total_all_balance_include_subwallet'] = $this->player_model->getPlayersTotalBallanceIncludeSubwallet($player_ids);

                    $data['today_deposit_sum']      = $this->transactions->getTotalDepositsToday($player_ids);
                    $data['today_deposited_player'] = $this->transactions->getTotalDepositedPlayer($player_ids);
                    $data['today_deposit_count']    = $this->transactions->getTodayTotalDepositCount($player_ids);

                    $data['today_withdrawal_sum']   = $this->transactions->getTotalWithdrawalsToday($player_ids);
                    $data['today_withdrawed_player'] = $this->transactions->getTotalWithdrawedPlayer($player_ids);
                    $data['today_withdraw_count']   = $this->transactions->getTodayTotalWithdrawCount($player_ids);
                }

                //5 minutes
                $timeout=5*60;
                $this->utils->saveJsonToCache($cache_key, $data, $timeout);
            }
        }else{
            //empty dashboard
            $data = [
                'total_active_players'=>0,
                'total_sub_accounts'=>0,
                'count_player_session'=>0,
                'today_member_count'=>0,
                'yesterday_member_count'=>0,
                'all_member_count'=>0,
                'all_member_deposited'=>0,
                'total_all_balance_include_subwallet'=>0,
                'today_deposit_sum'=>0,
                'today_deposited_player'=>0,
                'today_deposit_count'=>0,
                'today_withdrawal_sum'=>0,
                'today_withdrawed_player'=>0,
                'today_withdraw_count'=>0,
            ];
        }

        $this->template->write_view('main_content', 'home.php', $data);
        $this->template->render();
        return true;
    }

    public function tracking_link_list(){
        if(!$this->check_login_status()){
            return;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->load->model(['external_system']);

        $agent_id = $this->session->userdata('agent_id');

        $data = [];
        $this->initAgentInfo($agent_id, $data);
        $data['controller_name'] = $this->controller_name;
        $data['t1_lottery_enabled']=$this->external_system->isGameApiActive(T1LOTTERY_API);
        $data['enabled_wechat_links_on_agency']=$data['t1_lottery_enabled'] && !$this->utils->isEnabledFeature('hide_bonus_group_on_agency');

        $this->load_template('', '', '', 'agency');
        $this->addBoxDialogToTemplate();
        $this->template->add_js($this->utils->getAgencyCmsUrl('resources/js/agency_tracking_link.js'));
        $this->template->write_view('main_content', 'agency/tracking_link_list', $data);
        $this->template->render();
    }

    public function show_404() {

        $url = $this->utils->getConfig('agency_404_override');

        return empty($url) ? show_404() : redirect($this->utils->getSystemUrl('www',$url));
    }

    public function redirect_t1lottery(){
        return $this->game_bo(T1LOTTERY_API);
    }

    public function game_bo($apiId){
        $agent_id=null;
        if (!$this->isLoggedAgency($agent_id)) {
            redirect('/');
            return false;
        }

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        try {
            //load api
            $api=$this->utils->loadExternalSystemLibObject($apiId);
            if (empty($api)) {
                throw new Exception("No permission, or API $apiID not ready", 1);
            }
            $this->load->model(['game_provider_auth', 'agency_model', 'player_model']);

            //create view and redirect it to bo
            $title = lang('Lottery BoxOffice');

            $data=['backoffice_info'=>null, 'title'=>$title];


            // if(!empty($api)){
            $boInfo=$api->getBackOfficeInfo();

            $this->utils->debug_log('game_bo() boInfo 1', $boInfo);

            //validate info
            // if(empty($boInfo['backoffice_url']) || empty($boInfo['backoffice_username']) || empty($boInfo['backoffice_password'])){
            if(empty($boInfo['backoffice_url'])){
                throw new Exception('backoffice_url not set', 2);
            }

            $data['backoffice_info'] = $boInfo;

            // }
            // else {
            //  $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('role.nopermission'));
            //  redirect('/home');
            //  return;
            // }

            // $roles = $this->lottery_bo_roles->roles_fetch();

            // $agent = $this->authentication->getUsername();
            //get binding player id
            $agent=$this->agency_model->get_agent_by_id($agent_id);
            $binding_player_id=$agent['binding_player_id'];
            if(!empty($binding_player_id)){

                $loginInfo=$this->game_provider_auth->getOrCreateLoginInfoByPlayerId($binding_player_id, $apiId);
                if(!empty($loginInfo)){
                    if($loginInfo->register=='0'){
                        $playerInfo=$this->player_model->getPlayerArrayById($binding_player_id);
                        // try create player
                        $decryptedPwd = $this->salt->decrypt($playerInfo['password'], $this->getDeskeyOG());
                        $rlt=$api->createPlayer($playerInfo['username'], $player_id, $decryptedPwd);
                        if(!$rlt['success']){
                            $this->utils->error_log('create t1 lottery account failed', $rlt);
                        }else{
                            //update register
                            $rlt=$this->game_provider_auth->setRegisterFlag($binding_player_id, $apiId, Game_provider_auth::DB_TRUE);
                            if(!$rlt){
                                $this->utils->error_log('set register flag failed', $rlt);
                            }
                        }
                    }
                    $data['backoffice_info']['backoffice_username'] = $loginInfo->login_name;
                    $data['backoffice_info']['backoffice_password'] = $loginInfo->password;
                }else{
                    throw new Exception('No binding player', 3);
                }

            }else{
                throw new Exception('No binding player', 3);
            }

            $this->utils->debug_log('lottery bo signin, agent_id:'.$agent_id.', binding_player_id:'.$binding_player_id,
                'backoffice_info', $data['backoffice_info']);

            // $description='';
            // $keywords='';
            // $activenav='t1lottery_bo';  //change nav

            $this->load_template(lang('Game BackOffice'), '', '', 'agency');
            // $this->template->add_js('resources/js/bootstrap-switch.min.js');
            // $this->template->add_css('resources/css/bootstrap-switch.min.css');
            $this->template->add_css('resources/css/dashboard.css');
            $this->template->write_view('main_content', 'includes/redirect_game_bo', $data);
            $this->template->render();

        } catch (Exception $ex) {
            // $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('role.nopermission'));
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang($ex->getMessage()) . " ({$ex->getCode()})");
            redirect('/');
            return;
        }
    }

    public function change_active_currency(){
        //make sure we set session
        $this->load->library(['session']);

        $currencyKey=$this->input->get(Multiple_db::__OG_TARGET_DB);
        $result=['success'=>false];
        //validate currency
        if($currencyKey==Multiple_db::SUPER_TARGET_DB || $this->utils->isAvailableCurrencyKey($currencyKey)){
            $_multiple_db=Multiple_db::getSingletonInstance();
            $_multiple_db->init($currencyKey);
            $_multiple_db->rememberActiveTargetDB();

            $result['success']=true;
        }else{
            $result['message']=lang('not available currency');
        }

        $this->returnJsonResult($result);
    }

    protected function switch_active_currency_for_logged($currencyKey) {
        $result=['success'=>false];
        $this->load->library(['authentication', 'session']);
        $this->load->model(['agency_model']);
        //still old db
        $loggedUserId=null;
        $loggedUsername=null;
        $language=$this->session->userdata('agency_lang');

        $this->utils->debug_log('ci db', $this->db->getOgTargetDB(), 'language', $language);
        if(!$this->isLoggedAgency($loggedUserId, $loggedUsername)){
            $result['message']=lang('session timeout, please relogin');
            return $this->returnJsonResult($result);
        }
        $this->utils->debug_log('loggedUserId', $loggedUserId, 'loggedUsername', $loggedUsername, 'ci db', $this->db->getOgTargetDB());

        // $currencyKey=$this->input->get(Multiple_db::__OG_TARGET_DB);
        //validate currency
        if($currencyKey==Multiple_db::SUPER_TARGET_DB || $this->utils->isAvailableCurrencyKey($currencyKey)){
            $_multiple_db=Multiple_db::getSingletonInstance();
            $_multiple_db->switchCIDatabase($currencyKey);
            //init session from target db
            $this->session->reinit();

            $message=null;
            $agent_details=$this->agency_model->get_agent_by_name($loggedUsername);

            $this->utils->debug_log('try load agent_details', $agent_details, $loggedUsername);

            $result['success']=!empty($agent_details);
            if($result['success']){

                $result['success']=$this->_login_success($agent_details, $language);
                if(!$result['success']){
                    $message=lang('Process login failed');
                }
            }else{
                $message=lang('Not found agency username').': '.$loggedUsername;
            }

        }else{
            $result['message']=lang('not available currency');
        }

        return $result;
    }

    public function change_active_currency_for_logged($currencyKey) {
        $result=$this->switch_active_currency_for_logged($currencyKey);

        return $this->returnJsonResult($result);
    }

    /**
     * Retrieve username of currently logged in agent
     *
     * @return string Agent username
     */
    protected function getLoggedInAgentUsername(){
        return $this->session->userdata('agent_name');
    }
}

// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agency.php
/* Location: ./application/controllers/agency.php */
