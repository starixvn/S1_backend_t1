<?php

require_once dirname(__FILE__) . '/../BaseController.php';

/**
 * Class Base_agency_controller
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 *
 * @property Shorturl $shorturl
 */
class Base_agency_controller extends BaseController {

    const MAX_PASSWORD_LEN  = 12;
    const NONE_CHANGED_PASSWORD='******';

    // consts for calculation methods in tier commission patterns
    const HIGHEST_ATTAINED = 1; // using the percentages of the highest tier attained
    const TIER_INDEPENDENT = 2; // using its own percentages for each tier

    public $fields_array = array();
    public $labels_array = array();
    public $fields_rules = array();
    public $labels_json  = '';
    public $controller_name = '';

    function __construct() {

        parent::__construct();

        $this->load->model(['game_type_model','agency_model']);
        $this->load->library(['agency_library', 'form_validation', 'shorturl']);

        $this->set_labels();
    }

    protected function _login_success($agent_details, $language = NULL, $readonlySubAccount=null){
        $success=true;
        if(empty($language)){
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

        $this->session->set_userdata(array(
            'agent_name' => $agent_details['agent_name'],
            'agent_id' => $agent_details['agent_id'],
            'agency_lang' => $language,
            'agent_status' => $agent_details['status'],
            'agent_level' => $agent_details['agent_level'],
            'can_have_players' => $agent_details['can_have_players'] == '1' ? true : false,
            // 'can_have_players' => $agent_details['can_have_players'] == '1'? true:true,
            'can_have_sub_agent' => $agent_details['can_have_sub_agent'] == '1'? true:false,
            'can_view_agents_list_and_players_list' => $agent_details['can_view_agents_list_and_players_list'] == '1'? true:false,
            'show_bet_limit_template' => $agent_details['show_bet_limit_template'] == '1'? true:false,
        ));

        if(!empty($readonlySubAccount)){
            $this->utils->debug_log('write readonly_sub_account to session', $readonlySubAccount);
            $this->session->set_userdata(['readonly_sub_account'=>$readonlySubAccount]);
        }

        $this->session->updateLoginId('agent_id', $agent_details['agent_id']);

        $data = array(
            'last_login_ip' => $this->utils->getIP(),
            'last_login_time' => date('Y-m-d H:i:s'),
            // 'last_logout_time' => date('Y-m-d H:i:s'),
        );
        $this->agency_model->update_agent($agent_details['agent_id'], $data);
        return $success;
    }

    // hasPermission {{{2
    /**
     *  permission checking and redirect according to controller_name
     *
     *  @param  string permission string
     *  @return boolean
     */
    public function hasPermission($p) {
        if ($this->controller_name == 'agency_management'){
            if (!$this->permissions->checkPermissions($p)) {
                $this->error_access();
                return false;
            }
        } else {
            return $this->check_login_status();
        }
        return true;
    } // hasPermission  }}}2
    // getUsername {{{2
    /**
     *  get username according to controller_name
     *
     *  @param
     *  @return string username
     */
    public function getUsername() {
        $agent_name = '';
        if ($this->controller_name == 'agency_management'){
            $agent_name = $this->authentication->getUsername();
        } else {
            if (!$this->isLoggedAgency($agent_id, $agent_name)) {
                redirect('/');
            }
        }
        return $agent_name;
    } // getUsername  }}}2
    // getUserId {{{2
    /**
     *  get username according to controller_name
     *
     *  @param
     *  @return string username
     */
    public function getUserId() {
        $agent_id = null;
        if ($this->controller_name == 'agency_management'){
            $agent_id = $this->authentication->getUserId();
        } else {
            if (!$this->isLoggedAgency($agent_id, $agent_name)) {
                redirect('/');
            }
        }
        return $agent_id;
    } // getUserId  }}}2
    // isAdmin {{{2
    /**
     *  to see whether current user is an admin user
     *
     *  @return boolean
     */
    public function isAdmin() {
        return $this->controller_name == 'agency_management';
    } // isAdmin  }}}2
    // field_validation {{{2
    /**
     *  validate input in a field and return result
     *
     *  @param
     *  @return json object
     */
    protected function field_validation() {
        if ($this->form_validation->run() === false) {
            $arr = array('status' => 'error', 'msg' => validation_errors());
            $this->returnJsonResult($arr);
        } else {
            $arr = array('status' => 'success', 'msg' => "");
            $this->returnJsonResult($arr);
        }
    } // field_validation  }}}2
    // set_labels {{{2
    protected function set_labels() {

        $agent_max_credit_limit=$this->utils->getConfig('agent_max_credit_limit');

        $this->fields_rules = array(
            'structure_name' => 'trim|required|min_length[2]|max_length[12]|alpha_numeric|is_unique[agency_structures.structure_name]',
            'agent_name' => 'trim|required|min_length[2]|max_length[12]|alpha_numeric|is_unique[agency_agents.agent_name]',
            'password' => 'trim|required|min_length[6]|max_length[12]',
            'confirm_password' => 'trim|required|callback_confirmPassword',
            'agent_count' => 'trim|required|is_natural|greater_than[0]|less_than[20]|xss_clean',
            'currency'       => 'trim|required',
            'status'       => 'trim|required',
            'credit_limit' => 'trim|required|numeric|greater_than[0]|less_than['.$agent_max_credit_limit.']|xss_clean|callback_check_credit_limit',
            // 'min_rolling_comm' => 'trim|required|numeric|xss_clean|callback_check_min_rolling_comm',
            'available_credit' => 'trim|required|numeric|xss_clean|callback_checkCredit',
            //'rev_share' => 'trim|required|numeric|greater_than[0.00]|less_than_equal_to[90.01]|xss_clean',
            //'rolling_comm' => 'trim|required|numeric|greater_than[0.00]|less_than[3.01]|xss_clean',
            'rev_share' => 'trim|required|numeric|xss_clean|callback_check_rev_share',
            'rolling_comm' => 'trim|required|numeric|xss_clean|callback_check_rolling_comm',
            'rolling_comm_basis' => 'trim|required',
            'except_game_type' => '',
            'allowed_level' => 'trim|required|is_natural|greater_than[0]|less_than[10]|xss_clean',
            'agent_level' => 'trim|required|is_natural|greater_than[0]|less_than[10]|xss_clean',
            'vip_level' => 'required',
            'settlement_period' => 'required',
            'start_day' => '',
            'tracking_code' => 'trim|xss_clean|required|min_length[4]|max_length[20]|alpha_numeric|callback_check_unique_tracking_code',
            'game_types-1-rolling_comm' => 'trim|required|numeric|less_than[5]|xss_clean|callback_check_game_rolling_comm_ajax',
            'game_types-1-min_rolling_comm' => 'trim|required|numeric|less_than[5]|xss_clean|callback_check_min_rolling_comm_ajax',
            'admin_fee' => 'trim|required|numeric|xss_clean|callback_check_fee',
            'transaction_fee' => 'trim|required|numeric|xss_clean|callback_check_fee',
            'bonus_fee' => 'trim|required|numeric|xss_clean|callback_check_fee',
            'cashback_fee' => 'trim|required|numeric|xss_clean|callback_check_fee',
        );
        $this->labels_array = array(
            'structure_name' => lang('Agent Template Name'),
            'agent_name' => lang('Agent Name'),
            'password' => lang('Password'),
            'confirm_password' => lang('Confirm Password'),
            'agent_count' => lang('Count'),
            'currency'       => lang('Currency'),
            'status'       => lang('Status'),
            'credit_limit' => lang('Credit Limit'),
            'min_rolling_comm' => lang('Min Rolling Comm'),
            'available_credit' => lang('Available Credit'),
            'rev_share' => lang('Rev Share'),
            'rolling_comm' =>lang('Rolling Comm'),
            'rolling_comm_basis' => lang('Rolling Comm Basis'),
            'except_game_type' => lang('Except Game Type'),
            'allowed_level' => lang('Allowed Level'),
            'agent_level' => lang('Agent Level'),
            'vip_level' => lang('VIP Level'),
            'settlement_period' => lang('Settlement Period'),
            'start_day' => lang('Start Day for Weekly Settlement'),
            'tracking_code' => lang('Tracking Code'),
            'game_types-1-rolling_comm' => lang('Game Rolling Comm'),
            'game_types-1-min_rolling_comm' => lang('Min Rolling Comm'),
            'admin_fee' => lang('Admin Fee'),
            'transaction_fee' => lang('Transaction Fee'),
            'bonus_fee' => lang('Bonus Fee'),
            'cashback_fee' => lang('Cashback Fee'),
        );
        $this->setGameRevAndCommLabels();

        foreach($this->labels_array as $field=>$label) {
            $this->fields_array[] = $field;
        }
        $this->labels_json = json_encode($this->labels_array);

    } // set_labels  }}}2

    // Add binding player for current agent
    // resetBindingPlayer {{{2
    /**
     *  add binding player for an agent
     *
     *  @param  post
     *  @return json
     */
    public function resetBindingPlayer($agent_id) {
        //$this->utils->debug_log('resetBindingPlayer POSTS', $this->input->post());
        $this->form_validation->set_rules('binding_player', lang('Binding Player'), 'trim|required|xss_clean');
        //$this->form_validation->set_rules('password', lang('Password'),
        //     'trim|required|xss_clean|min_length[3]|max_length[25]');
        if ($this->form_validation->run() == false) {
            $this->utils->debug_log("validation_errors", validation_errors());
            $message = lang('Failed binding the player. Please input correct information!');
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
        } else {
            //$password = $this->input->post('password');
            $player_id = $this->input->post('binding_player');
            //$player = $this->player_model->getPlayerArrayById($player_id);
            if (isset($player_id) && !empty($player_id)){
                $this->agency_model->update_binding_player_id($agent_id, $player_id);
                $message = lang('successfully bind player.');
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
                $this->load->model(['player_model', 'agency_model']);
                $username=$this->agency_model->getAgentNameById($agent_id);
                $this->syncAgentCurrentToMDBWithLock($agent_id, $username, false);

                // add action log
                $this->load->library(array('agency_library', 'player_manager'));
                $player_detail = $this->player_model->getPlayerArrayById($player_id);
                $player_name = $player_detail['username'];
                $agent_name = $this->input->post('agent_name');
                $log_params = array(
                    'action' => 'bind_player',
                    'link_url' => site_url('agency_management/agent_information/'. $agent_id),
                    'done_by' => $this->authentication->getUsername(),
                    'done_to' => $agent_name,
                    'details' => 'bind player '. $player_name . ' for agent ' . $agent_name,
                );
                $this->agency_library->save_action($log_params);
                $data = array(
                    'playerId' => $player_id,
                    'changes' => 'admin bind player '. $player_name . ' for agent ' . $agent_name,
                    'createdOn' => date('Y-m-d H:i:s'),
                    'operator' => $this->authentication->getUsername(),
                );
                $this->player_manager->addPlayerInfoUpdates($player_id, $data);
            }
            /*
            $saved_password = $this->salt->decrypt($player['password'], $this->config->item('DESKEY_OG'));
            if ($password == $saved_password){
                if (isset($player_id) && !empty($player_id)){
                    $this->agency_model->update_binding_player_id($agent_id, $player_id);
                    $message = lang('successfully bind player.');
                    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
                }
            } else {
                // password error
                $message = lang('Failed binding the player. Player password NOT match!');
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            }
             */
        }

        redirect('/' . $this->controller_name . '/agent_information/' . $agent_id, 'refresh');
    } // resetBindingPlayer  }}}2
    // adjustBindingPlayer {{{2
    /**
     *  adjust binding player for an agent
     *
     *  @param  INT agent_id
     *  @return
     */
    public function adjustBindingPlayer($agent_id) {
        if ($this->hasPermission('agent_binding_player')) {
            $agent_detail = $this->agency_model->get_agent_by_id($agent_id);
			$data['agent'] = $agent_detail;

            $this->load->model('player_model');
            //$result = $this->player->getAllPlayerUsernames();
            $result = $this->player_model->get_players_by_agent_id($agent_id);
			$data['players'] = array_column($result, 'username', 'playerId');
            $data['player'] = '';
            if (!empty($agent_detail['binding_player_id']) && $agent_detail['binding_player_id'] > 0) {
                $player_detail = $this->player_model->getPlayerArrayById($agent_detail['binding_player_id']);
                $data['player'] = $player_detail['username'];
			}
            $data['controller_name'] = $this->controller_name;
			$this->load->view('includes/adjust_agent_binding_player', $data);
		}
    } // adjustBindingPlayer  }}}2

    // setGameRevAndCommLabels {{{2
    /**
     *  set form rules and labels for game rev share and rolling comm
     *
     *  @param
     *  @return
     */
    public function setGameRevAndCommLabels() {
        $game_platform_list = $this->agency_model->get_game_platforms_and_types();
        foreach ($game_platform_list as $game_platform){
            foreach ($game_platform['game_types'] as $index => $game_type){
                $id_rolling_comm = "game_types-". $game_type['id'] ."-rolling_comm";
                $this->fields_rules[$id_rolling_comm] =
                    'trim|required|numeric|less_than[5]|xss_clean|callback_check_game_rolling_comm['.$game_type['id'].']';
                $this->labels_array[$id_rolling_comm] = lang('Game Rolling Comm');

                $id_rev_share = "game_types-". $game_type['id'] ."-rev_share";
                $this->fields_rules[$id_rev_share] =
                    'trim|required|numeric|xss_clean|callback_check_game_rev_share['.$game_type['id'].']';
                $this->labels_array[$id_rev_share] = lang('Game Rev Share');

                $id_platform_fee = "game_types-". $game_type['id'] ."-platform_fee";
                $this->fields_rules[$id_platform_fee] = 'trim|required|numeric|xss_clean';
                $this->labels_array[$id_platform_fee] = lang('Game Platform Fee');

                $id_min_rolling_comm = "game_types-". $game_type['id'] ."-min_rolling_comm";
                $this->fields_rules[$id_min_rolling_comm] = 'trim|required|numeric|less_than[5]|xss_clean|callback_check_min_rolling_comm';
                $this->labels_array[$id_min_rolling_comm] = lang('Min Rolling Comm');
            }
        }
    } // setGameRevAndCommLabels  }}}2

    // setGameRevAndCommRules {{{2
    /**
     *  set form rules and labels for game rev share and rolling comm
     *
     *  @param
     *  @return
     */
    public function setGameRevAndCommRules() {
        $game_platform_list = $this->agency_model->get_game_platforms_and_types();
        foreach ($game_platform_list as $game_platform){
            foreach ($game_platform['game_types'] as $index => $game_type){
                $id_rolling_comm = "game_types[". $game_type['id'] ."][rolling_comm]";
                $this->form_validation->set_rules($id_rolling_comm, lang('Game Rolling Comm'),
                    'trim|required|numeric|xss_clean|less_than[5]|callback_check_game_rolling_comm['.$game_type['id'].']');

                $id_rev_share = "game_types[". $game_type['id'] ."][rev_share]";
                $this->form_validation->set_rules($id_rev_share, lang('Game Rev Share'),
                    'trim|required|numeric|xss_clean|callback_check_game_rev_share['.$game_type['id'].']');

                $id_platform_fee = "game_types[". $game_type['id'] ."][platform_fee]";
                $this->form_validation->set_rules($id_platform_fee, lang('Game Platform Fee'),
                    'trim|required|numeric|xss_clean');

                $id_min_rolling_comm = "game_types[". $game_type['id'] ."][min_rolling_comm]";
                $this->form_validation->set_rules($id_platform_fee, lang('Min Rolling Comm'),
                    'trim|required|numeric|less_than[5]|xss_clean|callback_check_min_rolling_comm');
            }
        }
    } // setGameRevAndCommRules  }}}2
    // confirmPassword {{{2
    /**
     * confirm Password Validation Callback
     *
     * @return  bool
     */
    public function confirmPassword() {
        $password = $this->input->post('password');
        $confirm_password = $this->input->post('confirm_password');

        if ($password != $confirm_password) {
            $this->form_validation->set_message('confirmPassword', "%s " . lang('mod.didntMatch') . "");
            return false;
        }

        return true;
    } // confirmPassword }}}2
    // check_credit_limit {{{2
    /**
     *  check credit and credit_limit to assure the values are all reasonable
     *
     *  @param
     *  @return
     */
    public function check_credit_limit() {
        $credit_limit = $this->input->post('credit_limit');
        $parent_id = $this->input->post('parent_id');

        if ($parent_id > 0) {
            $parent_details = $this->agency_model->get_agent_by_id($parent_id);
            $parent_limit  = $parent_details['credit_limit'];
            if ($credit_limit > $parent_limit) {
                $this->form_validation->set_message('check_credit_limit',
                    lang("Credit limit cannot exceed its parent's credit limit". ' ' . $parent_limit));
                return false;
            }
        }
        return true;
    } // check_credit_limit  }}}2
    // checkCredit {{{2
    /**
     *  check credit and credit_limit to assure the values are all reasonable
     *
     *  @param
     *  @return
     */
    public function checkCredit() {
        $available_credit = $this->input->post('available_credit');
        $credit_limit = $this->input->post('credit_limit');
        $before_credit = $this->input->post('before_credit');
        $parent_id = $this->input->post('parent_id');
        $agent_count = $this->input->post('agent_count');
        if (empty($agent_count) || $agent_count == 0) {
            $agent_count = 1;
        }
        $this->utils->debug_log('check credit', $available_credit, $credit_limit, $before_credit, $parent_id, $agent_count);

        if ($available_credit > $credit_limit) {
            $this->form_validation->set_message('checkCredit',
                $this->labels_array['available_credit'].' '.lang('cannot exceed').' ' . $this->labels_array['credit_limit']
            );
            return false;
        } else if ($parent_id > 0) {
            $parent_details = $this->agency_model->get_agent_by_id($parent_id);
            $parent_credit = $parent_details['available_credit'];
            $parent_limit  = $parent_details['credit_limit'];
            if ($available_credit > $before_credit) {
                $amount = $agent_count * ($available_credit - $before_credit);
                if ($amount > $parent_credit) {
                    $this->form_validation->set_message('checkCredit',
                        lang('NO enough credit in parent agent'). '('. $parent_credit .')');
                    return false;
                }
            } else {
                $amount = $agent_count * ($before_credit - $available_credit);
                if($parent_credit + $amount > $parent_limit) {
                    $this->form_validation->set_message('checkCredit',
                        lang('Exceed parent credit limit'). ' ' . $parent_limit);
                    return false;
                }
            }
        }
        return true;
    } // checkCredit  }}}2
    // check_fee {{{2
    /**
     *  fee must be >=0.00 and <= 100.00
     *
     *  @param
     *  @return
     */
    public function check_fee($fee) {
        if ($fee < 0.00 || $fee > 100.00) {
            $this->form_validation->set_message('check_fee', "%s must be >= 0 and <= 100.00.");
            return false;
        }

        return true;
    } // check_fee  }}}2
    // check_rev_share {{{2
    /**
     *  rev_share is >=0.00 and <= 90.00
     *
     *  @param
     *  @return
     */
    public function check_rev_share() {
        $rev_share = $this->input->post('rev_share');
        $parent_id = $this->input->post('parent_id');
        if ($rev_share < 0.00 || $rev_share > 100.00) {
            //$this->form_validation->set_message('rev_share', "%s ". lang('must between 0.00 and 90.00'). ".");
            $this->form_validation->set_message('check_rev_share', "%s must be >= 0 and <= 100.00.");
            return false;
        }
        if ($parent_id > 0) {
            $parent_details = $this->agency_model->get_agent_by_id($parent_id);
            $parent_rev_share = $parent_details['rev_share'];
            if ($rev_share > $parent_rev_share) {
                $this->form_validation->set_message('check_rev_share', "%s CANNOT exceed its parent's Rev Share ". $parent_rev_share);
                return false;
            }
        }
        return true;
    } // check_rev_share  }}}2
    // check_rolling_comm {{{2
    /**
     *  rolling_comm is >=0.00 and <= 3.00
     *
     *  @param
     *  @return
     */
    public function check_rolling_comm() {
        $rolling_comm = $this->input->post('rolling_comm');
        if ($rolling_comm < 0.00 || $rolling_comm > 3.00) {
            //$this->form_validation->set_message('rolling_comm', "%s ". lang('must between 0.00 and 90.00'). ".");
            $this->form_validation->set_message('check_rolling_comm', "%s must be >= 0.00 and <= 3.00.");
            return false;
        }
        return true;
    } // check_rolling_comm  }}}2
    // check_game_rev_share {{{2
    /**
     *  rev_share is >=0.00 and <= 90.00
     *
     *  @param
     *  @return
     */
    public function check_game_rev_share($rev_share, $game_id) {
        $this->utils->debug_log('check_game_rev_share: game_id === ', $game_id);
        //$rev_name = 'game_types-'. $game_id . '-rev_share';
        if ($rev_share < 0.00 || $rev_share > 100.00) {
            //$this->form_validation->set_message('rev_share', "%s ". lang('must between 0.00 and 90.00'). ".");
            $this->form_validation->set_message('check_game_rev_share', "%s must be >= 0 and <= 100.00.");
            return false;
        }
        $parent_id = $this->input->post('parent_id');
        $this->utils->debug_log('check_game_rev_share: parent_id = ', $parent_id);
        if (isset($parent_id) && $parent_id > 0) {
            $p = $this->agency_model->get_agent_game_types($parent_id);
            $parent_game_types = array_column($p, NULL, 'game_type_id');
            $parent_rev_share = $parent_game_types[$game_id]['rev_share'];
            if ($rev_share > $parent_rev_share) {
                $this->form_validation->set_message('check_game_rev_share',
                    "%s must be <= parent game rev share.");
                return false;
            }
        }

        return true;
    } // check_rev_share  }}}2
    // check_game_rolling_comm {{{2
    /**
     *  rolling_comm is >=0.00 and <= 3.00
     *
     *  @param
     *  @return
     */
    public function check_game_rolling_comm($rolling_comm, $game_id) {
        $this->utils->debug_log('check_game_rolling_comm: game_id === ', $game_id);
        //$comm_name = 'game_types-'. $game_id . '-rolling_comm';
        if ($rolling_comm < 0.00 || $rolling_comm > 3.00) {
            //$this->form_validation->set_message('rolling_comm', "%s ". lang('must between 0.00 and 90.00'). ".");
            $this->form_validation->set_message('check_game_rolling_comm', "%s must be >= 0.00 and <= 3.00.");
            return false;
        }

        $parent_id = $this->input->post('parent_id');
        $this->utils->debug_log('check_game_rolling_comm: parent_id = ', $parent_id);
        if (isset($parent_id) && $parent_id > 0) {
            // $parent_details = $this->agency_model->get_agent_by_id($parent_id);
            // $min_rolling_comm = $parent_details['min_rolling_comm'];
            $p = $this->agency_model->get_agent_game_types($parent_id);
            $parent_game_types = array_column($p, NULL, 'game_type_id');
            $parent_rolling_comm = $parent_game_types[$game_id]['rolling_comm'];
            $parent_min_rolling_comm = $parent_game_types[$game_id]['min_rolling_comm'];
            if ($rolling_comm > 0.00 && $rolling_comm > $parent_rolling_comm - $parent_min_rolling_comm) {
                $this->form_validation->set_message('check_game_rolling_comm',
                    "%s must be <= (parent game rolling comm - parent min keep rolling comm).");
                return false;
            }
        }
        return true;
    } // check_game_rolling_comm  }}}2
    // check_min_rolling_comm {{{2
    /**
     *  min_rolling_comm is >=0.00 and <= 3.00
     *
     *  @param
     *  @return
     */
    public function check_min_rolling_comm($min_rolling_comm) {
        // $min_rolling_comm = $this->input->post('min_rolling_comm');
        if ($min_rolling_comm < 0.00 || $min_rolling_comm > 3.00) {
            $this->form_validation->set_message('check_min_rolling_comm', "%s must be >= 0.00 and <= 3.00.");
            return false;
        }
        return true;
    } // check_min_rolling_comm  }}}2

    // getControllerName {{{2
    /**
     *  get controller name according to current user
     *
     *  @param
     *  @return string current controller name
     */
    protected function getControllerName() {
        if ($this->isLoggedAdminUser()) {
            return 'agency_management';
        } else {
            return 'agency';
        }

    } // getControllerName  }}}2
    // isEditAgencyPermission {{{2
    protected function isEditAgencyPermission($agent_id, &$controller_name){

		$permission=false;

		$is_logged_agent=$this->isLoggedAgency($logged_agent_id);
		if($is_logged_agent){

            if($this->isAgencyReadonlySubaccountLogged()){
                return false;
            }

			//check agency permission, if it's downline
			//only update downline
			$permission=$this->agency_model->is_upline($agent_id, $logged_agent_id);
			$controller_name='agency';
		}else{

			//check admin permission
			$is_logged_admin=$this->isLoggedAdminUser();
			if($is_logged_admin){
				$controller_name='agency_management';
			}
			$permission=$is_logged_admin && $this->permissions->checkPermissions('edit_agent');

		}

		return $permission;
	} // isEditAgencyPermission }}}2

    // protected function init_agency_prefix_for_game_account(&$data, $agent_id){
    //     //load from db
    //     $data['agency_prefix_for_game_account']=$this->agency_model->getPrefixSettingsByAgentId($agent_id);
    // }

    // initEditAgentInfo {{{2
    protected function initEditAgentInfo(&$data, $agent_id, $is_agent=true){

		$controller_name=null;
		if(!$this->isEditAgencyPermission($agent_id, $controller_name)){
			return null;
		}

        $agent_details = $this->agency_model->get_agent_by_id($agent_id);

        $parent_agent_id = $agent_details['parent_id'];
        if(!empty($parent_agent_id)) {
            $parent_agent_details = $this->agency_model->get_agent_by_id($parent_agent_id);
        }

        $data['agent_id'] = $agent_id;

        $old_pass = $this->agency_library->old_password_format($agent_details['password'], self::MAX_PASSWORD_LEN);

		$data['conditions'] = $this->safeLoadParams(array(
            'agent_id' => $agent_details['agent_id'],
            'structure_id' => '',
            'parent_id' => $agent_details['parent_id'],
            'agent_count' => '1',
            'before_credit' => $agent_details['available_credit'],
            # 'structure_name' => '',
            'agent_name' => $agent_details['agent_name'],
            'firstname' => $agent_details['firstname'],
            'lastname' => $agent_details['lastname'],
            'password' => self::NONE_CHANGED_PASSWORD,
            'confirm_password' => self::NONE_CHANGED_PASSWORD,
            'tracking_code' => $agent_details['tracking_code'],
            'currency' => $agent_details['currency'],
            'status' => $agent_details['status'],
            'credit_limit' => $agent_details['credit_limit'],
            'available_credit' => $agent_details['available_credit'],
            'note' => $agent_details['note'],
            'agent_level' => $agent_details['agent_level'],
            'agent_level_name' => $agent_details['agent_level_name'],
            # 'allowed_level' => $agent_details['allowed_level'],
            # 'allowed_level_names' => $agent_details['allowed_level_names'],
            'vip_level' => $agent_details['vip_level'],
            # 'rev_share' => $agent_details['rev_share'],
            # 'rolling_comm' => $agent_details['rolling_comm'],
            # 'rolling_comm_basis' => $agent_details['rolling_comm_basis'],
            # 'total_bets_except' => $agent_details['total_bets_except'],
            'can_have_sub_agent' => $agent_details['can_have_sub_agent']==1,
            'can_have_players' => $agent_details['can_have_players']==1,
            'show_bet_limit_template' => $agent_details['show_bet_limit_template']==1,
            'show_rolling_commission' => $agent_details['show_rolling_commission']==1,
            'can_view_agents_list_and_players_list' => $agent_details['can_view_agents_list_and_players_list']==1,
            'can_do_settlement' => $agent_details['can_do_settlement']==1,

            # Controls whether the corresponding permission checkbox should be disabled
            'enabled_can_have_sub_agent' => empty($parent_agent_id) ? 1 : $parent_agent_details['can_have_sub_agent']==1,
            'enabled_can_have_players' => empty($parent_agent_id) ? 1 : $parent_agent_details['can_have_players']==1,
            'enabled_show_bet_limit_template' => empty($parent_agent_id) ? 1 : $parent_agent_details['show_bet_limit_template']==1,
            'enabled_show_rolling_commission' => empty($parent_agent_id) ? 1 : $parent_agent_details['show_rolling_commission']==1,
            'enabled_can_view_agents_list_and_players_list' => empty($parent_agent_id) ? 1 : $parent_agent_details['can_view_agents_list_and_players_list']==1,
            'enabled_can_do_settlement' => empty($parent_agent_id) ? 1 : $parent_agent_details['can_do_settlement']==1,

            'settlement_period' => $agent_details['settlement_period'],
            'start_day' => $agent_details['settlement_start_day'],
            # 'vip_groups' => $agent_details['vip_groups'],
            # 'vip_levels' => $vip_levels,
            'admin_fee' => number_format($agent_details['admin_fee'],2),
            'transaction_fee' => number_format($agent_details['transaction_fee'],2),
            'deposit_fee' => number_format($agent_details['deposit_fee'],2),
            'withdraw_fee' => number_format($agent_details['withdraw_fee'],2),
            'bonus_fee' => number_format($agent_details['bonus_fee'],2),
            'cashback_fee' => number_format($agent_details['cashback_fee'],2),
            'deposit_comm' => number_format($agent_details['deposit_comm'],2),
            'min_rolling_comm' => number_format($agent_details['min_rolling_comm'],2),
            'player_prefix' =>$agent_details['player_prefix'],
            'live_mode' =>$agent_details['live_mode'],
        ));

        $data['agency_player_rolling_settings']=$this->utils->getConfig('agency_player_rolling_settings');
        $data['game_types'] = $this->game_type_model->getGameTypesArray();
        /*
        $agent_game_platforms = $this->agency_model->get_agent_game_platforms($agent_id);
        $agent_game_types = $this->agency_model->get_agent_game_types($agent_id);
        $agent_game_types = array_column($agent_game_types, NULL, 'game_type_id');

        $game_platforms_and_types = $this->agency_model->get_game_platforms_and_types();
        $this->utils->debug_log('game_platforms_and_types: ', $game_platforms_and_types);

        $data['game_platform_settings']['game_platform_list'] = array();
        if (isset($agent_details['parent_id']) && $agent_details['parent_id'] > 0) {

            $this->utils->debug_log('agent_details parent_id: ', $agent_details['parent_id']);

            # ONLY SHOW GAME PLATFORM ENABLED BY PARENT
            $parent_game_platforms = $this->agency_model->get_agent_game_platforms($agent_details['parent_id']);
            foreach ($parent_game_platforms as $parent_game_platform) {
                $game_platform_id = $parent_game_platform['game_platform_id'];
                if (isset($game_platforms_and_types[$game_platform_id])) {
                    $data['game_platform_settings']['game_platform_list'][$game_platform_id] = $game_platforms_and_types[$game_platform_id];
                }
            }

            # DO NOT ALLOW VALUE GREATER THAN PARENT
            $parent_game_types = $this->agency_model->get_agent_game_types($agent_details['parent_id']);
            $data['game_platform_settings']['conditions']['game_types'] = array();
            foreach ($parent_game_types as $parent_game_type) {

                $game_type_id = $parent_game_type['game_type_id'];

                $game_type = array(
                    'id' => $parent_game_type['id'],
                    'max_rev_share' => $parent_game_type['rev_share'],
                    'rolling_comm_basis' => $parent_game_type['rolling_comm_basis'],
                    'max_rolling_comm' => $parent_game_type['rolling_comm'],
                    'max_bet_threshold' => $parent_game_type['bet_threshold'],
                );

                if (isset($agent_game_types[$game_type_id])) {
                    $game_type = array_merge($agent_game_types[$game_type_id], $game_type);
                }

                $data['game_platform_settings']['conditions']['game_types'][$game_type_id] = $game_type;

            }

        } else {
            $data['game_platform_settings']['game_platform_list'] = $game_platforms_and_types; # SHOW ALL GAME PLATFORM IF LEVEL 0
            $data['game_platform_settings']['conditions']['game_types'] = $agent_game_types; # NO MAX VALUE IF LEVEL 0
        }

        $this->utils->debug_log('game_platform_settings: ', $data['game_platform_settings']['game_platform_list']);

        $data['game_platform_settings']['conditions']['agent_level'] = $agent_details['agent_level'];
        $data['game_platform_settings']['conditions']['game_platforms'] = array_column($agent_game_platforms, NULL, 'game_platform_id');

        }
        $this->utils->debug_log('game_platform_settings: ', $data['game_platform_settings']['game_platform_list']);
        */
        /*
        $data['game_platform_settings']['conditions']['game_platforms'] = array_column($agent_game_platforms, NULL, 'game_platform_id');
        $data['game_platform_settings']['conditions']['game_types'] = array_column($agent_game_types, NULL, 'game_type_id');
        // get all tier comm patterns
        $patterns = $this->agency_model->get_all_tier_comm_patterns();
        $data['game_platform_settings']['patterns'] = $patterns;
        $this->utils->debug_log('initEditAgentInfo PATTERNS: ', $patterns);
         */
        $this->get_game_comm_settings($data, $agent_id, 'agent');

        // $this->init_agency_prefix_for_game_account($data, $agent_id);
        $data['game_platform_settings']['agent']['deposit_comm'] = $data['conditions']['deposit_comm'];
        $data['game_platform_settings']['agent_level'] = $data['conditions']['agent_level'];

        $data['is_agent'] = $is_agent;
        $data['is_edit'] = TRUE;

        $data['vip_levels'] = $this->agency_model->get_vip_levels();

        $data['fields'] = $this->fields_array;
        $data['labels'] = $this->labels_array;

        $data['form_url'] = site_url('/'.$controller_name.'/verify_update_agent');
		$data['validate_ajax_url'] = site_url('/'.$controller_name.'/edit_agent_validation_ajax');
		$data['parent_game_ajax_url'] = site_url('/'.$controller_name.'/get_parent_game_types_ajax');

        $data['controller_name'] = $controller_name;

        // get all agent templates
        $data['agent_templates'] = $this->agency_model->get_structure_id_and_names();
        $this->utils->debug_log('initEditAgentInfo structure NAMES: ', $data['agent_templates']);
		$data['copy_template_ajax_url'] = site_url('/'.$controller_name.'/agent_copy_template_ajax');

		return $agent_details;
	} // initEditAgentInfo }}}2

    // agent_copy_template_ajax {{{2
    /**
     *  validate input for agent creation
     *
     *  @param
     *  @return string json string for message
     */
    public function agent_copy_template_ajax() {
        $this->utils->debug_log('agent_copy_template_ajax POSTS: ', $this->input->post());
        $structure_id = $this->input->post("structure_id");
        $parent_id = $this->input->post("parent_id");

        $message = "failed";
        $chk = $this->copy_template_check_values($message, $structure_id, $parent_id);
        if($chk){
            $structure_details = $this->agency_model->get_structure_by_id($structure_id);
            $p = $this->agency_model->get_structure_game_types($structure_id);
            $structure_game_types = array_column($p, NULL, 'game_type_id');
            $result = array(
                "structure_details" => $structure_details,
                "structure_game_types" => $structure_game_types,
                "status" => "success"
            );
        } else {
            $result = array("msg" => $message, "status" => "failed");
        }
        return $this->returnJsonResult($result);
    } // agent_copy_template_ajax  }}}2
    // copy_template_check_values {{{2
    /**
     *  check values when copy settings from a template
     *
     *  @param  string  error message
     *  @param  int  structure_id
     *  @param  int  parent_id
     *  @return bool true if no value conflict
     */
    protected function copy_template_check_values(&$message, $structure_id, $parent_id = null) {
        $structure_details = $this->agency_model->get_structure_by_id($structure_id);
        $s = $this->agency_model->get_structure_game_types($structure_id);
        $structure_game_types = array_column($s, NULL, 'game_type_id');
        if (isset($parent_id) && !empty($parent_id) && $parent_id > 0){
            // for sub agent
            $parent_details = $this->agency_model->get_agent_by_id($parent_id);
            $p = $this->agency_model->get_agent_game_types($parent_id);
            $parent_game_types = array_column($p, NULL, 'game_type_id');
            if ($structure_details['credit_limit'] > $parent_details['credit_limit']) {
                $message = lang('Credit Limit') . ' (' . $structure_details['credit_limit'] . ')' .
                    lang(" CANNOT exceed parent's ") . lang('Credit Limit'). ' (' . $parent_details['credit_limit']. ')';
                return false;
            }
            foreach ($structure_game_types as $id=>$t) {
                if (!isset($parent_game_types[$id]) || empty($parent_game_types[$id])){
                    $message = lang('Game Platforms'). lang(" don't match parent's ") . lang('Game Platforms');
                    return false;
                }
                if ($t['rev_share'] > $parent_game_types[$id]['rev_share']) {
                    $message = lang('Rev Share') . ' (' . $t['rev_share'] . ')'. lang(" CANNOT exceed parent's ").
                        lang('Rev Share') . ' (' . $parent_game_types[$id]['rev_share'] . ')';
                    return false;
                }
                if ($t['rolling_comm'] > $parent_game_types[$id]['rolling_comm'] - $parent_game_types[$id]['min_rolling_comm']) {
                    $message = lang('Rolling Comm') . ' (' . $t['rolling_comm'] .')'. lang(" CANNOT exceed parent's ").
                        lang('Rolling Comm') .' - '. lang('Min Rolling Comm') . ' (' .
                        ($parent_game_types[$id]['rolling_comm'] - $parent_game_types[$id]['min_rolling_comm']). ')';
                    return false;
                }
            }
        } else {
            // for level 0 agent
            foreach ($structure_game_types as $id=>$t) {
                if ($t['rev_share'] != 100 && !empty($t['rev_share']) ) {
                    $message = lang("Rev share for level 0 agent must be 100%");
                    return false;
                }
            }
        }
        return true;
    } // copy_template_check_values  }}}2
    // edit_agent_validation_ajax{{{2
    public function edit_agent_validation_ajax() {
        # Set error messages. Copied from player_auth_module.php
        $this->form_validation->set_message('matches', lang('formvalidation.matches'));
        $this->form_validation->set_message('min_length', lang('formvalidation.min_length'));
        $this->form_validation->set_message('max_length', lang('formvalidation.max_length'));
        $this->form_validation->set_message('required', lang('formvalidation.required'));
        $this->form_validation->set_message('isset', lang('formvalidation.isset'));
        $this->form_validation->set_message('valid_email', lang('formvalidation.valid_email'));
        $this->form_validation->set_message('valid_emails', lang('formvalidation.valid_emails'));
        $this->form_validation->set_message('exact_length', lang('formvalidation.exact_length'));
        $this->form_validation->set_message('alpha', lang('formvalidation.alpha'));
        $this->form_validation->set_message('alpha_numeric', lang('formvalidation.alpha_numeric'));
        $this->form_validation->set_message('alpha_dash', lang('formvalidation.alpha_dash'));
        $this->form_validation->set_message('numeric', lang('formvalidation.numeric'));
        $this->form_validation->set_message('is_numeric', lang('formvalidation.is_numeric'));
        $this->form_validation->set_message('regex_match', lang('formvalidation.regex_match'));
        $this->form_validation->set_message('is_unique', lang('formvalidation.is_unique'));
        $this->form_validation->set_message('less_than', lang('formvalidation.is_unique'));
        $this->form_validation->set_message('greater_than', lang('formvalidation.is_unique'));

        $agent_name_rule = 'trim|required|min_length[4]|max_length[12]|alpha_numeric|callback_check_edit_agent_name';
        $structure_name_rule = 'trim|required|min_length[4]|max_length[12]|alpha_numeric|xss_clean';

        foreach($this->fields_rules as $field => $rule) {
            $label = $this->labels_array[$field];
            if ($field == 'structure_name') {
                $rule = $structure_name_rule;
            }
            if ($field == 'agent_name') {
                $rule = $agent_name_rule;
            }
            $this->utils->debug_log('field', $field, 'label', $label, 'rule', $rule);

            if ($this->input->post($field)) {
                $this->form_validation->set_rules($field, $label, $rule);
                $this->field_validation();
            }
        }
    } // edit_agent_validation_ajax  }}}2
    // initCreateSubAgentInfo {{{2
    /**
     *  Initialize data array for sub agent creation
     *
     *  @param  array data array
     *  @param  int agent_id parent agent id
     *  @param  int agent level
     *  @return
     */
    public function initCreateSubAgentInfo(&$data, $agent_id, $vip_levels='') {
        // $data['min_agent_level'] = $this::MIN_ALLOWED_LEVEL;
        // $data['max_agent_level'] = $this::MAX_ALLOWED_LEVEL;

        // if (empty($vip_levels)) {
        //     $vip_levels = $agent_details['vip_levels'];
        // }

        $agent_details = $this->agency_model->get_agent_by_id($agent_id);
        $this->utils->debug_log('CREATE_SUB_AGENT details', $agent_details);

        $data['parent_id'] = $agent_details['agent_id'];
        $data['parent_name'] = $agent_details['agent_name'];
        $data['conditions'] = $this->safeLoadParams(array(
            'parent_id' => $agent_details['agent_id'],
            'parent_name' => $agent_details['agent_name'],
            'structure_id' => '',
            'agent_name' => '',
            'firstname' => '',
            'lastname' => '',
            'password' => '',
            'confirm_password' => '',
            'tracking_code' => '', # strtoupper(random_string()), # When an Agent is creating his/her Sub-agent, the tracking code of the sub-agent should be the same as its USERNAME.
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
            'can_do_settlement' => $agent_details['can_do_settlement'],
            'enabled_can_have_sub_agent' => $agent_details['can_have_sub_agent'],
            'enabled_can_have_players' => $agent_details['can_have_players'],
            'enabled_show_bet_limit_template' => $agent_details['show_bet_limit_template'],
            'enabled_show_rolling_commission' => $agent_details['show_rolling_commission'],
            'enabled_can_view_agents_list_and_players_list' => $agent_details['can_view_agents_list_and_players_list'],
            'enabled_can_do_settlement' => $agent_details['can_do_settlement'],
            // 'settlement_period' => explode(',', $agent_details['settlement_period']),
            'settlement_period' => $agent_details['settlement_period'],
            'start_day' => $agent_details['settlement_start_day'],
            'before_credit' => '0',
            'agent_count' => '1',
            'vip_groups' => $agent_details['vip_groups'],
            'note' => '', // $agent_details['note'],
            'admin_fee' => number_format($agent_details['admin_fee'],2),
            'transaction_fee' => number_format($agent_details['transaction_fee'],2),
            'bonus_fee' => number_format($agent_details['bonus_fee'],2),
            'cashback_fee' => number_format($agent_details['cashback_fee'],2),
            'min_rolling_comm' => number_format($agent_details['min_rolling_comm'],2),
            'player_prefix' => ''
        ));

        $data['is_create'] = true;

        $this->utils->debug_log('CREATE_SUB_AGENT: conditions', $data['conditions']);

        // $data['min_allowed_level'] = $this::MIN_ALLOWED_LEVEL;
        // $data['max_allowed_level'] = $this::MAX_ALLOWED_LEVEL;
        $data['fields'] = $this->fields_array;
        $data['labels'] = $this->labels_array;
        $data['agency_player_rolling_settings']=$this->utils->getConfig('agency_player_rolling_settings');

        $data['game_types'] = $this->game_type_model->getGameTypesArray();

    /*
    $data['game_platform_settings']['game_platform_list'] = array();
    $agent_game_platforms = $this->agency_model->get_agent_game_platforms($agent_id);
    $game_platforms_and_types = $this->agency_model->get_game_platforms_and_types();
    $this->utils->debug_log('CREATE_SUB_AGENT: game_platforms_and_types', $game_platforms_and_types);

    # ONLY SHOW GAME PLATFORM ENABLED BY PARENT
    $data['game_platform_settings']['game_platform_list'] = array();
    $parent_game_platforms = $this->agency_model->get_agent_game_platforms($agent_id);
    foreach ($parent_game_platforms as $parent_game_platform) {
    $game_platform_id = $parent_game_platform['game_platform_id'];
    if (isset($game_platforms_and_types[$game_platform_id])) {
    $data['game_platform_settings']['game_platform_list'][$game_platform_id] = $game_platforms_and_types[$game_platform_id];
    }
    }

    # DO NOT ALLOW VALUE GREATER THAN PARENT
    $parent_game_types = $this->agency_model->get_agent_game_types($agent_id);
    $data['game_platform_settings']['conditions']['game_types'] = array();
    foreach ($parent_game_types as $parent_game_type) {
    $game_type_id = $parent_game_type['game_type_id'];
    $data['game_platform_settings']['conditions']['game_types'][$game_type_id] = array(
    'id' => $parent_game_type['id'],
    'max_rev_share' => $parent_game_type['rev_share'],
    'rolling_comm_basis' => $parent_game_type['rolling_comm_basis'],
    'max_rolling_comm' => $parent_game_type['rolling_comm'],
    'max_bet_threshold' => $parent_game_type['bet_threshold'],
    );
    }

    $data['game_platform_settings']['conditions']['game_platforms'] = array_column($agent_game_platforms, NULL, 'game_platform_id');
    $data['game_platform_settings']['conditions']['game_types'] = array_column($agent_game_types, NULL, 'game_type_id');
    // get all tier comm patterns
    $patterns = $this->agency_model->get_all_tier_comm_patterns();
    $data['game_platform_settings']['patterns'] = $patterns;
    $this->utils->debug_log('initCreateSubAgentInfo PATTERNS: ', $patterns);
     */
        // NOTE: rolling_comm_out is disabled at present
        $is_new = true;
        $this->get_game_comm_settings($data, $agent_id, 'agent', $is_new);
        $data['game_platform_settings']['agent_level'] = $data['conditions']['agent_level'];

        $data['is_agent'] = TRUE;
        $data['vip_levels'] = $this->agency_model->get_vip_levels();

        $data['fields'] = $this->fields_array;
        $data['labels'] = $this->labels_array;

        $controller_name = $this->getControllerName();
        $data['form_url'] = site_url('/'. $controller_name . '/verify_agent/' . $agent_id);
        $data['validate_ajax_url'] = site_url('/'.$controller_name.'/create_agent_validation_ajax');
        $data['parent_game_ajax_url'] = site_url('/'.$controller_name.'/get_parent_game_types_ajax');

        $data['controller_name'] = $controller_name;

        // get all agent templates
        $data['agent_templates'] = $this->agency_model->get_structure_id_and_names();
        $data['copy_template_ajax_url'] = site_url('/'.$controller_name.'/agent_copy_template_ajax');

    } // initCreateSubAgentInfo  }}}2
    // create_agent_validation_ajax {{{2
    /**
     *  validate input for agent creation
     *
     *  @param
     *  @return string json string for message
     */
    public function create_agent_validation_ajax() {

        # Set error messages. Copied from player_auth_module.php
        $this->form_validation->set_message('matches', lang('formvalidation.matches'));
        $this->form_validation->set_message('min_length', lang('formvalidation.min_length'));
        $this->form_validation->set_message('max_length', lang('formvalidation.max_length'));
        $this->form_validation->set_message('required', lang('formvalidation.required'));
        $this->form_validation->set_message('isset', lang('formvalidation.isset'));
        $this->form_validation->set_message('valid_email', lang('formvalidation.valid_email'));
        $this->form_validation->set_message('valid_emails', lang('formvalidation.valid_emails'));
        $this->form_validation->set_message('exact_length', lang('formvalidation.exact_length'));
        $this->form_validation->set_message('alpha', lang('formvalidation.alpha'));
        $this->form_validation->set_message('alpha_numeric', lang('formvalidation.alpha_numeric'));
        $this->form_validation->set_message('alpha_dash', lang('formvalidation.alpha_dash'));
        $this->form_validation->set_message('numeric', lang('formvalidation.numeric'));
        $this->form_validation->set_message('is_numeric', lang('formvalidation.is_numeric'));
        $this->form_validation->set_message('regex_match', lang('formvalidation.regex_match'));
        $this->form_validation->set_message('is_unique', lang('formvalidation.is_unique'));
        $this->form_validation->set_message('less_than', lang('formvalidation.is_unique'));
        $this->form_validation->set_message('greater_than', lang('formvalidation.is_unique'));


        foreach($this->fields_rules as $field => $rule) {
            $label = $this->labels_array[$field];
            //$this->utils->debug_log('field', $field, 'label', $label, 'rule', $rule);
            if ($this->input->post($field)) {
                $this->form_validation->set_rules($field, $label, $rule);
                $this->field_validation();
            }
        }
    } // create_agent_validation_ajax  }}}2

    public function check_edit_agent_name() {
        $agent_name = $this->input->post('agent_name');
        $agent_id = $this->input->post('agent_id');
        $query_array = $this->agency_model->get_all_agents_by_name($agent_name);
        $this->utils->debug_log('check_edit_agent_name', $query_array, $agent_id, $agent_name);
        if (!empty($query_array)) {
            foreach($query_array as $rec) {
                if ($rec['agent_id'] != $agent_id) {
                    $this->form_validation->set_message('check_edit_agent_name', lang('Agent name has been used!'));
                    return false;
                }
            }
        } else {
            return true;
        }
    } // check_edit_agent_name  }}}2
    // get_parent_game_types_ajax {{{2
    /**
     *  validate input for agent creation
     *
     *  @param
     *  @return string json string for message
     */
    public function get_parent_game_types_ajax() {
        $parent_id = $this->input->post("parent_id");

        if (isset($parent_id) && $parent_id > 0) {
            $parent_details = $this->agency_model->get_agent_by_id($parent_id);
            $p = $this->agency_model->get_agent_game_types($parent_id);
            $parent_game_types = array_column($p, NULL, 'game_type_id');
            $result = array(
                "parent_game_types" => $parent_game_types,
                "parent_details" => $parent_details,
                "status" => "success"
            );
        } else {
			$result = array("data" => null, "status" => "failed");
		}
		return $this->returnJsonResult($result);
    } // get_parent_game_types_ajax  }}}2

    // agent_form_rules {{{2
    protected function agent_form_rules() {
        /*
        $this->form_validation->set_rules('vip_level[]', lang('VIP Level'), 'trim|required');
        $this->form_validation->set_rules('settlement_period[]', lang('Settlement Period'), 'required');
        */
        $this->form_validation->set_rules('currency', lang('Currency'), 'trim');
        $this->form_validation->set_rules('status', lang('Status'), 'trim|required');
        $this->form_validation->set_rules('password', lang('Password'),
             'trim|required|min_length[6]|max_length[12]');
        $this->form_validation->set_rules('confirm_password', lang('Confirm Password'),
             'trim|required|callback_confirmPassword');
        # Validate credit only when we are not using wallet
        if (!$this->utils->isEnabledFeature('agent_settlement_to_wallet')) {
            $this->form_validation->set_rules('credit_limit', lang('Credit Limit'),
               'trim|required|numeric|greater_than[0]|less_than['.$this->utils->getConfig('agent_max_credit_limit').']|xss_clean|callback_check_credit_limit');
            $this->form_validation->set_rules('available_credit', lang('Available Credit'),
               'trim|numeric|xss_clean|callback_checkCredit');
        }
        // $this->form_validation->set_rules('rev_share', lang('Rev Share'),
        //      'trim|required|numeric|xss_clean|callback_check_rev_share');
        // $this->form_validation->set_rules('rolling_comm_basis', lang('Rolling Comm Basis'), 'required');
        // $this->form_validation->set_rules('rolling_comm', lang('Rolling Comm'),
        //      'trim|required|numeric|xss_clean|callback_check_rolling_comm');
        // $this->form_validation->set_rules('player_vip_levels', lang('VIP Levels'),
        //     'callback_check_player_vip_levels');
        $this->form_validation->set_rules('agent_settlement_period', lang('Settlement Period'),
            'callback_check_agent_settlement_period');
        // $this->setGameRevAndCommRules();
    } // agent_form_rules }}}2
    // add_game_comm_settings {{{2
    /**
     *  process and save game commission settings
     *
     *  @param  array game_platforms
     *  @param  array game_types
     *  @param  int   id   id or player_id
     *  @param  string type 'agent' or 'player'
     *  @return boolean  true for success
     */
    protected function add_game_comm_settings($game_platforms, $game_types, $id, $type='agent', $is_update = false) {

        $this->load->model(['agency_model']);
        return $this->agency_model->add_game_comm_settings($game_platforms, $game_types, $id, $type, $is_update);

        // if ($type == 'agent') {
        //     $id_name = 'agent_id';
        //     $game_platform_table = 'agency_agent_game_platforms';
        //     $game_type_table = 'agency_agent_game_types';
        // } elseif ($type == 'player') {
        //     $id_name = 'player_id';
        //     $game_platform_table = 'agency_player_game_platforms';
        //     $game_type_table = 'agency_player_game_types';
        // } else {
        //     $id_name = 'structure_id';
        //     $game_platform_table = 'agency_structure_game_platforms';
        //     $game_type_table = 'agency_structure_game_types';
        // }

        // if ($is_update) {
        //     $this->db->delete($game_platform_table, array($id_name => $id));
        //     $this->db->delete($game_type_table, array($id_name => $id));
        // }

        // if (! empty($game_platforms)) {
        //     $game_platform_data = array();
        //     $this->utils->debug_log('post GAME_PLATFORMS param', $game_platforms);
        //     $game_platforms = array_filter($game_platforms,
        //         function($game_platform, $game_platform_id) use ($id, $id_name, &$game_platform_data) {
        //         $enabled = $game_platform['enabled'];
        //         if ($enabled) {
        //             $game_platform_data[] = array(
        //                 $id_name => $id,
        //                 'game_platform_id' => $game_platform_id,
        //             );
        //         }
        //         return $enabled;
        //     }, ARRAY_FILTER_USE_BOTH );

        //     $this->utils->debug_log('update GAME_PLATFORMS:', $game_platform_data);

        //     if(!empty($game_platforms)){
        //         $this->db->insert_batch($game_platform_table, $game_platform_data);
        //     }
        // }

        // # UPDATE GAME TYPES
        // if (! empty($game_types)) {
        //     $this->utils->debug_log('post GAME_TYPES param', $game_types);
        //     $controller = $this;
        //     $game_types = array_filter($game_types, function(&$game_type, $game_type_id) use ($id, $id_name, $controller) {
        //         $enabled = false;
        //         if ($controller->utils->isEnabledFeature('agent_tier_comm_pattern')) {
        //             $enabled = isset($game_type['pattern_id']);
        //         } else {
        //             # If this is not set to true, new record will never get inserted
        //             $enabled = isset($game_type['rolling_comm']) && isset($game_type['rolling_comm_basis']);
        //         }
        //         if ($enabled){
        //             $game_type['game_type_id'] = $game_type_id;
        //             $game_type[$id_name] = $id;
        //             if ($controller->utils->isEnabledFeature('agent_tier_comm_pattern')) {
        //                 if(isset($game_type['pattern_id']) && $game_type['pattern_id'] > 0){
        //                     // settlement calculation still works when agent_tier_comm_pattern is disabled.
        //                     $pattern = $controller->agency_model->get_tier_comm_pattern($game_type['pattern_id']);
        //                     $game_type['rev_share'] = $pattern['rev_share'];
        //                     $game_type['rolling_comm_basis'] = $pattern['rolling_comm_basis'];
        //                     $game_type['rolling_comm'] = $pattern['rolling_comm'];
        //                     $game_type['bet_threshold'] = $pattern['min_bets'];
        //                 } else {
        //                     $controller->utils->debug_log('pattern_id EXCEPTION. GAME_TYPES:', $game_types);
        //                 }
        //             }
        //         }
        //         return $enabled;
        //     }, ARRAY_FILTER_USE_BOTH );

        //     $this->utils->debug_log('update GAME_TYPES:', $game_types);

        //     if(!empty($game_types)){
        //         $this->db->insert_batch($game_type_table, $game_types);
        //     }
        // }
        // return true;
    } // add_game_comm_settings  }}}2
    // get_game_comm_settings {{{2
    /**
     *  fetch game commission settings for an agent or player
     *
     *  @param  INT id = parent agent_id for a new sub agent or new player
     *  @param  string 'agent' or 'player'
     *  @return array $data
     */
    protected function get_game_comm_settings(&$data, $id = null, $type = 'agent', $is_new = false) {
        // get all game platforms and types
        $game_platforms_and_types = $this->agency_model->get_game_platforms_and_types();
        $data['game_platform_settings']['game_platform_list'] = $game_platforms_and_types;

        if (!$is_new) {
            if ($type == 'structure') {
                $game_platforms = $this->agency_model->get_structure_game_platforms($id);
                $game_types = $this->agency_model->get_structure_game_types($id);
            } elseif ($type == 'player') {
                $game_platforms = $this->agency_model->get_player_game_platforms($id);
                $game_types = $this->agency_model->get_player_game_types($id);
            } else { // agent
                $game_platforms = $this->agency_model->get_agent_game_platforms($id);
                $game_types = $this->agency_model->get_agent_game_types($id);
            }
            if(isset($data['game_platform_settings']['view_only']) && $data['game_platform_settings']['view_only']) {
                // only display enabled game_platforms
                $data['game_platform_settings']['game_platform_list'] = array();
                foreach ($game_platforms as $game_platform) {
                    $game_platform_id = $game_platform['game_platform_id'];
                    if (isset($game_platforms_and_types[$game_platform_id])) {
                        $data['game_platform_settings']['game_platform_list'][$game_platform_id]
                            = $game_platforms_and_types[$game_platform_id];
                    }
                }
            }

            $data['game_platform_settings']['conditions']['game_platforms']
                = array_column($game_platforms, NULL, 'game_platform_id');

            $game_types = array_column($game_types, NULL, 'game_type_id');
            $data['game_platform_settings']['conditions']['game_types'] = $game_types;

            if($type == 'agent'){
                $agent_details = $this->agency_model->get_agent_by_id($id);
                $data['game_platform_settings']['conditions']['agent_level'] = $agent_details['agent_level'];
            } else if ($type == 'player') {
                $this->load->model('player');
                $player_details = $this->player->getPlayerByPlayerId($id);
                if (isset($player_details['agent_id']) && $player_details['agent_id'] > 0) {
                    $this->get_parent_game_comm_settings($data, $player_details['agent_id'], $game_platforms_and_types, $game_types);
                }
            }
            $this->utils->debug_log('get_game_comm_settings : GAME_TYPES',
                $data['game_platform_settings']['conditions']['game_types']);
        } else if(!empty($id)) { // this is for a new sub agent or a new player under agent
            $this->get_parent_game_comm_settings($data, $id, $game_platforms_and_types);
            $this->utils->debug_log('get_game_comm_settings : GAME_TYPES',
                $data['game_platform_settings']['conditions']['game_types']);
        }
        $this->utils->debug_log('game_platform_settings: GAME_PLATFORMS',
            $data['game_platform_settings']['game_platform_list']);
        if ($this->utils->isEnabledFeature('agent_tier_comm_pattern')) {
            // get all tier comm patterns
            $patterns = $this->agency_model->get_all_tier_comm_patterns();
            $data['game_platform_settings']['patterns'] = $patterns;
            $this->utils->debug_log('get_game_comm_settings PATTERNS: ', $patterns);
        }
    } // get_game_comm_settings  }}}2
    // get_parent_game_comm_settings {{{2
    /**
     *  get parent game commission settings for a subagent or player
     *
     *  @param  INT parent_id
     *  @return void
     */
    private function get_parent_game_comm_settings(&$data, $parent_id, $game_platforms_and_types, $game_types = null) {
        $this->utils->debug_log('get_parent_game_comm_settings  parent_id: ', $parent_id);
        $data['game_platform_settings']['parent_id'] = $parent_id;
        // reset value for game_platform_list
        $data['game_platform_settings']['game_platform_list'] = array();
        # ONLY SHOW GAME PLATFORM ENABLED BY PARENT
        $parent_game_platforms = $this->agency_model->get_agent_game_platforms($parent_id);
        foreach ($parent_game_platforms as $parent_game_platform) {
            $game_platform_id = $parent_game_platform['game_platform_id'];
            if (isset($game_platforms_and_types[$game_platform_id])) {
                $data['game_platform_settings']['game_platform_list'][$game_platform_id] = $game_platforms_and_types[$game_platform_id];
            }
        }

        $data['game_platform_settings']['conditions']['game_platforms'] = array_column($parent_game_platforms, NULL, 'game_platform_id');

        # DO NOT ALLOW VALUE GREATER THAN PARENT
        $parent_game_types = $this->agency_model->get_agent_game_types($parent_id);
        // reset the value for game_types
        $data['game_platform_settings']['conditions']['game_types'] = array();
        foreach ($parent_game_types as $parent_game_type) {
            $game_type_id = $parent_game_type['game_type_id'];
            $game_type = array(
                'id' => $parent_game_type['id'],
                'max_rev_share' => $parent_game_type['rev_share'],
                'rolling_comm_basis' => $parent_game_type['rolling_comm_basis'],
                'max_rolling_comm' => $parent_game_type['rolling_comm'],
                'max_bet_threshold' => $parent_game_type['bet_threshold'],

                'rev_share' => $parent_game_type['rev_share'],
                'rolling_comm' => $parent_game_type['rolling_comm'],
                'rolling_comm_out' => $parent_game_type['rolling_comm_out'],
                'bet_threshold' => $parent_game_type['bet_threshold'],
                'platform_fee' => $parent_game_type['platform_fee'],
                'min_rolling_comm' => $parent_game_type['min_rolling_comm'],
            );
            if (!empty($game_types) && isset($game_types[$game_type_id])) {
                $game_type = array_merge($game_types[$game_type_id], $game_type);
            }
            $data['game_platform_settings']['conditions']['game_types'][$game_type_id] = $game_type;
        }
    } // get_parent_game_comm_settings  }}}2

    // update_agent {{{2
    protected function update_agent($agent_id) {
        // $agent_details = $this->agency_model->get_agent_by_id($agent_id);

        $agent_types = $this->input->post('agent_type');

        // $rolling_comm_basis = $this->input->post('rolling_comm_basis');
        // $total_bets_except = '';
        // if ('total_bets' == $rolling_comm_basis) {
        //     $total_bets_except = $this->input->post('except_game_type');
        // }
        // $this->utils->debug_log($rolling_comm_basis);
        // $this->utils->debug_log($total_bets_except);

        $settlement_period = $this->input->post('settlement_period');
        $this->utils->debug_log($settlement_period);
        if (is_array($settlement_period)) {
            $this->utils->debug_log(implode(",", $settlement_period));
        }
        $start_day = '';
        if ($settlement_period == 'Weekly') {
            $start_day = $this->input->post('start_day');
        }
        $this->utils->debug_log($start_day);

        // $today = date("Y-m-d H:i:s");

        $agent_details = $this->agency_model->get_agent_by_id($agent_id);
        // $before_credit = $agent_details['available_credit'];
        // $old_pass = $this->agency_library->old_password_format($agent_details['password'], $this::MAX_PASSWORD_LEN);

        $parent_id = $this->input->post('parent_id');
        $new_pass = $this->input->post('password');

        //$vip_levels = $this->input->post('vip_level');
        //$agent_level =  $this->input->post('agent_level');
        //$agent_level_name = $this->get_agent_level_name($agent_level);

        // $selected_vip_levels = $this->input->post('selected_vip_levels');
        // $this->utils->debug_log('selected_vip_levels', $selected_vip_levels, explode(',', $selected_vip_levels));
        // list($player_vip_groups, $player_vip_levels) = $this->agency_library->get_player_vip_info(explode(',',$selected_vip_levels));
        // $this->utils->debug_log('vip groups levels', $player_vip_groups, $player_vip_levels);

        // $available_credit = $this->input->post('available_credit');

        $data = array(
            // 'agent_name' => $this->input->post('agent_name'),
            'firstname'=>$this->input->post('firstname'),
            'lastname'=>$this->input->post('lastname'),
            //'password' => $this->salt->encrypt($this->input->post('password'), $this->getDeskeyOG()),
            // 'tracking_code'=>$this->input->post('tracking_code'), // separate update form
            'currency' => $this->input->post('currency'),
            'credit_limit' => $this->input->post('credit_limit'),
            // 'available_credit' => $available_credit,
            'status' => $this->input->post('status'),
            // 'rev_share' => $this->input->post('rev_share'),
            // 'rolling_comm' => $this->input->post('rolling_comm'),
            // 'rolling_comm_basis' => $rolling_comm_basis,
            // 'total_bets_except' => $total_bets_except,
            'agent_level' => $this->input->post('agent_level'),
            'agent_level_name' => $this->input->post('agent_level_name'),
            'can_have_sub_agent' => is_array($agent_types) && in_array('can-have-sub-agents',$agent_types) ? 1 : 0,
            'can_have_players' => is_array($agent_types) && in_array('can-have-players',$agent_types) ? 1 : 0,
            'can_view_agents_list_and_players_list' => is_array($agent_types) && in_array('can-view-agents-list-and-players-list',$agent_types) ? 1 : 0,
            'can_do_settlement' => is_array($agent_types) && in_array('can-do-settlement',$agent_types) ? 1 : 0,
            'show_bet_limit_template' => is_array($agent_types) && in_array('show-bet-limit-template',$agent_types) ? 1 : 0,
            'show_rolling_commission' => is_array($agent_types) && in_array('show-rolling-commission',$agent_types) ? 1 : 0,
            'vip_level' => $this->input->post('vip_level'),
            //'vip_level' => implode(",", $vip_levels),
            //'vip_level_name' => $this->get_vip_level_names($vip_levels),
            //'vip_group_name' => '',
            'settlement_period' => $settlement_period,
            'settlement_start_day' => $start_day,
            'updated_on' => $this->utils->getNowForMysql(),
            // 'vip_groups' => implode(',', $player_vip_groups),
            // 'vip_levels' => implode(',', $player_vip_levels),
            'note' => $this->input->post('note'),
            'admin_fee'                 => $this->input->post('admin_fee'),
            'bonus_fee'                 => $this->input->post('bonus_fee'),
            'cashback_fee'              => $this->input->post('cashback_fee'),
            'deposit_comm'              => $this->input->post('deposit_comm'),
            'min_rolling_comm'              => $this->input->post('min_rolling_comm'),
        );

        if($this->input->post('live_mode')) {
            $data['live_mode'] = $this->input->post('live_mode');
        }

        if($this->utils->isEnabledFeature('use_deposit_withdraw_fee')) {
            $data['deposit_fee'] = $this->input->post('deposit_fee');
            $data['withdraw_fee'] = $this->input->post('withdraw_fee');
        } else {
            $data['transaction_fee'] = $this->input->post('transaction_fee');
        }

        // $old_pass = $this->agency_library->old_password_format($agent_details['password'], $this::MAX_PASSWORD_LEN);
        $new_pass = $this->input->post('password');
        // $this->utils->debug_log('old_pass, new_pass', $old_pass, $new_pass);
        if ($new_pass != self::NONE_CHANGED_PASSWORD) {
            $data['password'] = $this->utils->encodePassword($new_pass);
        }

        $this->utils->debug_log($data);

        $parent_id = $this->input->post('parent_id');
        // $before_credit = $this->input->post('before_credit');

        $this->agency_model->startTrans();

        $row = $this->agency_model->update_agent($agent_id, $data);

        $this->db->delete('agency_agent_game_platforms', array('agent_id' => $agent_id));
        $game_platforms = $this->input->post('game_platforms');
        if ( ! empty($game_platforms)) {
            $this->utils->debug_log('post game_platforms param', $game_platforms);

            // $game_platforms = array_filter($game_platforms, function(&$game_platform, $game_platform_id) use ($agent_id) {

            //     $enabled = $game_platform['enabled'];

            //     if ($enabled) {

            //         $game_platform['game_platform_id'] = $game_platform_id;
            //         $game_platform['agent_id'] = $agent_id;

            //         unset($game_platform['enabled']);
            //     }

            //     return $enabled;

            // }, ARRAY_FILTER_USE_BOTH );

            foreach ($game_platforms as $key => $game_platform) {
                if(isset($game_platform['enabled']) && $game_platform['enabled']){
                    $game_platforms[$key]['game_platform_id'] = $key;
                    $game_platforms[$key]['agent_id'] = $agent_id;
                    unset($game_platforms[$key]['enabled']);
                }
            }
            
            $this->utils->debug_log('update agency_agent_game_platforms', $game_platforms);

            if(!empty($game_platforms)){
                $this->db->insert_batch('agency_agent_game_platforms', $game_platforms);
            }
        }

        # UPDATE GAME TYPES
        $this->db->delete('agency_agent_game_types', array('agent_id' => $agent_id));
        $game_types = $this->input->post('game_types');
        if (! empty($game_types)) {

            $this->utils->debug_log('post game_types param', $game_types);

            // $controller = $this;
            // $game_types = array_filter($game_types, function(&$game_platform, $game_type_id) use ($agent_id, $controller) {
            //     $enabled = false;
            //     // $enabled=true;
            //     if(isset($game_platform['rolling_comm_basis'])){

            //         if ($controller->utils->isEnabledFeature('agent_tier_comm_pattern')) {
            //             $enabled = isset($game_platform['pattern_id']);
            //         } else {
            //             $enabled = true;
            //         }

            //     }

            //     $game_platform['game_type_id'] = $game_type_id;
            //     $game_platform['agent_id'] = $agent_id;

            //     return $enabled;

            // }, ARRAY_FILTER_USE_BOTH );

            foreach ($game_types as $key => $game_type_value) {
                $enabled = false;
                if(isset($game_type_value['rolling_comm_basis'])){
                    if ($this->utils->isEnabledFeature('agent_tier_comm_pattern')) {
                        $enabled = isset($game_type_value['pattern_id']);
                    } else {
                        $enabled = true;
                    }
                }

                if($enabled){
                    $game_types[$key]['game_type_id'] = $key;
                    $game_types[$key]['agent_id'] = $agent_id;
                } else {
                    unset($game_types[$key]);
                }
            }


            $game_types = array_values($game_types);
            $this->utils->debug_log('update agency_agent_game_types', $game_types);

            if(!empty($game_types)){

                $this->db->insert_batch('agency_agent_game_types', $game_types);

                # UPDATE DOWNLINE AGENT
                $downline_agents = $this->agency_model->get_all_downline_arr($agent_id); # GETS ALL SUBAGENTS INCLUDING THE CURRENT AGENT
                $downline_agents = array_diff($downline_agents, [$agent_id]); # EXCLUDE CURRENT AGENT FROM THE LIST

                $game_types_for_downline = array_map(function($game_type) {
                    return array(
                        'game_type_id' => $game_type['game_type_id'],
                        'rolling_comm_basis' => isset($game_type['rolling_comm_basis']) ? $game_type['rolling_comm_basis'] : '',
                    );
                }, $game_types);

                $this->utils->debug_log('update downline agency_agent_game_types', $game_types_for_downline);

                array_walk($downline_agents, function($agent_id) use ($game_types_for_downline) {
                    $this->db->where('agent_id', $agent_id);
                    $this->db->update_batch('agency_agent_game_types', $game_types_for_downline, 'game_type_id');
                });

            }

        }

        // if ($before_credit != $available_credit) {
        //     if($parent_id > 0){
        //         $parent_details = $this->agency_model->get_agent_by_id($parent_id);
        //         $data = array(
        //             'available_credit' => $parent_details['available_credit'] + $before_credit - $available_credit,
        //         );
        //         $this->agency_model->update_agent($parent_id, $data);
        //     }
        // }
        $succ = $this->agency_model->endTransWithSucc();
        if (!$succ) {
            throw new Exception('Sorry, save agent failed.');
        }
        // if ($before_credit > $available_credit) {
        //     $adjust_amount = $before_credit - $available_credit;
        //     $this->agency_library->record_transaction_on_adjust('sub', $agent_details, $adjust_amount);
        // } else if ($before_credit < $available_credit) {
        //     $adjust_amount = $available_credit - $before_credit;
        //     $this->agency_library->record_transaction_on_adjust('add', $agent_details, $adjust_amount);
        // }

        return $row;
    } // update_agent  }}}2

    // initAgentInfo {{{2
    /**
     *  init data array for agent information page
     *
     *  @param  int agent_id
     *  @param  array data
     *  @return
     */
    protected function initAgentInfo($agent_id, &$data) {
        $this->load->model(['group_level', 'game_provider_auth']);

        $data['random_password'] = '';
        $data['agent_id'] = $agent_id;
        $agent_details = $this->agency_model->get_agent_by_id($agent_id);
        $data['agent'] = $agent_details;
        //get start bonus from bind player id
        $bind_player_max_bonus_rate=null;
        if (!empty($agent_details['binding_player_id']) && $agent_details['binding_player_id'] > 0) {
            $this->load->model('player');
            $player_details = $this->player->getPlayerByPlayerId($agent_details['binding_player_id']);
            $data['binding_player'] = $player_details['username'];
            //get t1 lottery max bonus rate
            $additionalInfo=$this->game_provider_auth->getAdditionalInfo($player_details['playerId'], T1LOTTERY_API);
            if(!empty($additionalInfo) && !empty($additionalInfo['bonus_rate'])){
                $bind_player_max_bonus_rate=$additionalInfo['bonus_rate'];
            }
        }
        $data['agent']['sub_link'] = $this->utils->getSystemUrl('agency'). '/agency/register/' . $agent_details['tracking_code'];

        $vip_detail = $this->group_level->getVipGroupLevelDetails($agent_details['vip_level']);
        $data['vip_group_name'] = !empty($vip_detail['groupName']) ?$vip_detail['groupName']: false;
        $data['vip_level_name'] = !empty($vip_detail['vipLevelName']) ?$vip_detail['vipLevelName']: false;

        $data['vip_groups'] = $this->agency_library->get_vip_group_names($agent_details['vip_groups']);
        $data['sub_agents'] = $this->agency_model->get_all_sub_agents($agent_id);
        $data['agent_additional_domain_list'] = $agent_additional_domain_list = $this->emptyOrArray($this->agency_model->getAdditionalDomainList($agent_id));
        $data['agent_source_code_list']       = $this->emptyOrArray($this->agency_model->getSourceCodeList($agent_id));

        $data['domain_list_for_agent'] = $domain_list_for_agent = $this->agency_model->get_domain_list_by_agent_id($agent_id);

        $data['agent_domains'] = $this->getAvailableAgentDomain($domain_list_for_agent, $agent_additional_domain_list);

        $data['tracking_link_protocol'] = $this->utils->isEnabledFeature('use_https_for_agent_tracking_links') ? 'https://' : 'http://';
        $data['first_domain'] = (empty($data['domain_list_for_agent'])) ? NULL : $data['domain_list_for_agent'][0]['domain_name'];
        if (!empty($data['agent']['parent_id']) && $data['agent']['parent_id'] > 0) {
            $data['parent'] = $this->agency_model->get_agent_by_id($data['agent']['parent_id']);
        } else {
            $data['parent'] = null;
        }

        $data['bank'] = $this->agency_model->get_payment_by_agent_id($agent_id);
        $this->load->model('transactions');
        if($this->utils->getConfig('disable_last_transaction_list_on_agent_info_page')){
            $data['transactions'] = [];
        }else{
            $data['transactions'] = $this->transactions->getAgentTransactions($agent_id, 100); # limit recent 100 records
        }

        $agency_tracking_link_bonus_rate_list_config = $this->utils->getConfig('agency_tracking_link_bonus_rate_list');

        $agency_tracking_link_bonus_rate_list = [];

        foreach($agency_tracking_link_bonus_rate_list_config as $range_setting){
            $start=$range_setting['start'];
            if($range_setting['start']=='MAX_BONUS_RATE'){
                if(empty($bind_player_max_bonus_rate)){
                    $start=$range_setting['end'];
                }else{
                    $start=$bind_player_max_bonus_rate;
                }
            }

            $list = range($start, $range_setting['end'], $range_setting['step']);
            foreach($list as $value){
                $agency_tracking_link_bonus_rate_list[] = $value;
            }
        }

        $data['agency_tracking_link_bonus_rate_list'] = $agency_tracking_link_bonus_rate_list;

        /*
        $agent_game_platforms = $this->agency_model->get_agent_game_platforms($agent_id);
        $agent_game_types = $this->agency_model->get_agent_game_types($agent_id);

        $data['game_platform_settings']['conditions']['game_platforms'] = array_column($agent_game_platforms, NULL, 'game_platform_id');
        $data['game_platform_settings']['conditions']['game_types'] = array_column($agent_game_types, NULL, 'game_type_id');
        $data['game_platform_settings']['game_platform_list'] = $this->agency_model->get_game_platforms_and_types();
         */

        $data['game_platform_settings']['view_only'] = TRUE;
        $this->get_game_comm_settings($data, $agent_id, 'agent');

        $data['controller_name'] = $this->controller_name;
    } // initAgentInfo  }}}2

    protected function getAvailableAgentDomain($domain_list_for_agent, $agent_additional_domain_list){
        $agent_domains = [];
        foreach($domain_list_for_agent as $agent_domain){
            $agent_domains[] = $agent_domain['domain_name'];
        }

        foreach($agent_additional_domain_list as $agent_additional__domain){
            $agent_domains[] = $agent_additional__domain['tracking_domain'];
        }

        return $agent_domains;
    }

    // Tracking Code {{{1
    // verifyEditTrackingCode {{{2
    /**
     *  verify edit tracking code for admin and agency portal
     *
     *  @param  int agent id for agent under editing
     *  @param  int user id which can be admin user id or current agent id
     *  @param  string redirect URL
     *  @return
     */
    protected function verifyEditTrackingCode($agent_id, $userId, $redirectUrl = NULL) {
        $this->form_validation->set_rules('tracking_code', 'Tracking Code', 'trim|xss_clean|required|min_length[4]|max_length[20]|alpha_numeric|is_unique[agency_agents.tracking_code]');

        if ($this->form_validation->run() == false) {
            $message = lang('con.aff13');

            if($redirectUrl){
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                redirect($redirectUrl);
            }else{
                return [
                    "status" => "error",
                    "message" => $message
                ];
            }
        } else {
            $this->agency_model->startTrans();

            $trackingCode = $this->input->post('tracking_code');
            $result = $this->agency_model->updateTrackingCode($agent_id, $trackingCode, $userId);
            if ($result) {
                $success = $this->agency_model->endTransWithSucc();
            } else {
                $this->agency_model->rollbackTrans();
                $this->utils->error_log('rollback on create code', $agent_id);

                $success = false;

            }

            if($success){
                $username=$this->agency_model->getAgentNameById($agent_id);
                $this->syncAgentCurrentToMDBWithLock($agent_id, $username, false);
            }

            if($success){
                $status = self::MESSAGE_TYPE_SUCCESS;
                $message = lang('con.aff14');
            }else{
                $status = self::MESSAGE_TYPE_ERROR;
                $message = lang('error.default.db.message');
            }

            if($redirectUrl){
                $this->alertMessage($status, $message);
                redirect($redirectUrl);
            }else{
                return [
                    "status" => ($status === self::MESSAGE_TYPE_SUCCESS) ? 'success' : 'error',
                    "message" => $message
                ];
            }
        }
    } // verifyEditTrackingCode  }}}2

    public function verifyNewAdditionalAgentDomain($agent_id, $redirectUrl){

        $this->form_validation->set_rules('agent_domain', 'Agent Domain', 'trim|required|valid_domain');
        $this->form_validation->set_message('valid_domain', lang('validation.badDomain'));

        if ($this->form_validation->run() == false) {
            $result = [
                'status' => 'error',
                'message' => validation_errors()
            ];

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['message']);
                return redirect($redirectUrl);
            }
        }

        $agent_domain = $this->input->post('agent_domain');
        // $this->utils->debug_log('agent_domain:'.$agent_domain);

        if (!empty($agent_domain)) {
            $agent_domain = trim($agent_domain);
            //remove http://
            if (strpos($agent_domain, 'http://') !== false || strpos($agent_domain, 'https://') !== false) {
                $agent_domain = parse_url($agent_domain, PHP_URL_HOST);
            }
        }

        $this->utils->debug_log('agent_domain:'.$agent_domain);

        if (empty($agent_domain)) {
            $result = [
                'status' => 'error',
                'message' => lang('Empty domain')
            ];

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['message']);
                return redirect($redirectUrl);
            }
        }

        $this->load->model('affiliatemodel');

        if($this->agency_model->existsAdditionalAgentDomain($agent_domain) || $this->affiliatemodel->existsAffdomain('', $agent_domain)){
            $result = [
                'status' => 'error',
                'message' =>lang('Save failed because the domain exists')
            ];

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['message']);
                return redirect($redirectUrl);
            }
        }

        $success = $this->agency_model->newAdditionalAgentDomain($agent_id, $agent_domain);

        if($success){
            $username=$this->agency_model->getAgentNameById($agent_id);
            $this->syncAgentCurrentToMDBWithLock($agent_id, $username, false);
        }

        $result = [
            'status' => 'success',
            'message' => NULL
        ];

        if ($success) {
            $result['status'] = 'success';
            $result['message'] = lang('Save settings successfully');
        } else {
            $result['status'] = 'error';
            $result['message'] = lang('Save settings failed');
        }

        if($this->input->is_ajax_request()){
            return $this->returnJsonResult($result);
        }else{
            $this->alertMessage(($result['status'] === 'success') ? self::MESSAGE_TYPE_SUCCESS : self::MESSAGE_TYPE_ERROR, $result['message']);
            return redirect($redirectUrl);
        }
    }

    public function change_additional_agent_domain($agent_id, $agentTrackingId){

        $controller_name = $this->getControllerName();

        $this->form_validation->set_rules('agent_domain', 'Agent Domain', 'trim|required|valid_domain');
        $this->form_validation->set_message('valid_domain', lang('validation.badDomain'));

        if ($this->form_validation->run() == false) {
            $result = [
                'status' => 'error',
                'message' => validation_errors()
            ];

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['message']);
                return redirect('/' . $controller_name . '/agent_information/' . $agent_id. '#agent_tracking_code');
            }
        }

        $agent_domain = $this->input->post('agent_domain');

        //try fix agent_domain
        if (!empty($agent_domain)) {
            $agent_domain = trim($agent_domain);
            //remove http://
            if (strpos($agent_domain, 'http://') !== false || strpos($agent_domain, 'https://') !== false) {
                $agent_domain = parse_url($agent_domain, PHP_URL_HOST);
            }
        }

        $this->load->model('affiliatemodel');

        if($this->agency_model->existsAdditionalAgentDomain($agent_domain, $agentTrackingId)  || $this->affiliatemodel->existsAffdomain('', $agent_domain)){
            $result = [
                'status' => 'error',
                'message' => lang('Save failed because the domain exists')
            ];

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['message']);
                return redirect('/' . $controller_name . '/agent_information/' . $agent_id. '#agent_tracking_code');
            }
        }

        $success = $this->agency_model->updateAdditionalAgentDomain($agentTrackingId, $agent_domain);

        if($success){
            $username=$this->agency_model->getAgentNameById($agent_id);
            $this->syncAgentCurrentToMDBWithLock($agent_id, $username, false);
        }

        $result = [
            'status' => 'success',
            'message' => NULL
        ];

        if ($success) {
            $result['status'] = 'success';
            $result['message'] = lang('Save settings successfully');
        } else {
            $result['status'] = 'error';
            $result['message'] = lang('Save settings failed');
        }

        if($this->input->is_ajax_request()){
            return $this->returnJsonResult($result);
        }else{
            $this->alertMessage(($result['status'] === 'success') ? self::MESSAGE_TYPE_SUCCESS : self::MESSAGE_TYPE_ERROR, $result['message']);
            return redirect('/' . $controller_name . '/agent_information/' . $agent_id. '#agent_tracking_code');
        }
    }

    public function remove_additional_agent_domain($agent_id, $agentTrackingId){
        $controller_name = $this->getControllerName();
        $success=false;
        if(!empty($agent_id) && !empty($agentTrackingId)){
            $success=$this->agency_model->removeAdditionalAgentDomain($agentTrackingId);
        }

        if($success){
            $username=$this->agency_model->getAgentNameById($agent_id);
            $this->syncAgentCurrentToMDBWithLock($agent_id, $username, false);
        }

        $result = [
            'status' => 'success',
            'message' => NULL
        ];

        if ($success) {
            $result['status'] = 'success';
            $result['message'] = lang('Save settings successfully');
        } else {
            $result['status'] = 'error';
            $result['message'] = lang('Save settings failed');
        }

        if($this->input->is_ajax_request()){
            return $this->returnJsonResult($result);
        }else{
            $this->alertMessage(($result['status'] === 'success') ? self::MESSAGE_TYPE_SUCCESS : self::MESSAGE_TYPE_ERROR, $result['message']);
            return redirect('/' . $controller_name . '/agent_information/' . $agent_id. '#agent_tracking_code');
        }
    }

    public function new_source_code($agent_id){
        $controller_name = $this->getControllerName();

        // $this->form_validation->set_rules('bonus_rate', lang('lang.bonus'), 'trim|required|alpha_dash');
        // $this->form_validation->set_rules('rebate_rate', lang('lang.rebate'), 'trim|required|numeric');
        $this->form_validation->set_rules('sourceCode', 'Agent Source Code', 'trim|required');

        if ($this->form_validation->run() == false) {
            $result = [
                'status' => 'error',
                'message' => validation_errors()
            ];

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['message']);
                return redirect('/' . $controller_name . '/agent_information/' . $agent_id. '#agent_tracking_code');
            }
        }

        $bonus_rate = $this->input->post('bonus_rate');
        $rebate_rate = $this->input->post('rebate_rate');
        $player_type = $this->input->post('player_type');
        $sourceCode = $this->input->post('sourceCode');

        if($this->agency_model->existsSourceCode($agent_id, $sourceCode)){
            $result = [
                'status' => 'error',
                'message' => lang('Save failed because the source code exists')
            ];

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['message']);
                return redirect('/' . $controller_name . '/agent_information/' . $agent_id. '#agent_tracking_code');
            }
        }

        $success = $this->agency_model->newSourceCode($agent_id, [
            'bonus_rate' => $bonus_rate,
            'rebate_rate' => $rebate_rate,
            'player_type' => $player_type,
            'tracking_source_code' => $sourceCode
        ]);

        if($success){
            $username=$this->agency_model->getAgentNameById($agent_id);
            $this->syncAgentCurrentToMDBWithLock($agent_id, $username, false);
        }

        $result = [
            'status' => 'success',
            'message' => NULL
        ];

        if ($success) {
            $result['status'] = 'success';
            $result['message'] = lang('Save settings successfully');
        } else {
            $result['status'] = 'error';
            $result['message'] = lang('Save settings failed');
        }

        if($this->input->is_ajax_request()){
            return $this->returnJsonResult($result);
        }else{
            $this->alertMessage(($result['status'] === 'success') ? self::MESSAGE_TYPE_SUCCESS : self::MESSAGE_TYPE_ERROR, $result['message']);
            return redirect('/' . $controller_name . '/agent_information/' . $agent_id. '#agent_tracking_code');
        }
    }

    public function change_source_code($agent_id, $agentTrackingId){
        $controller_name = $this->getControllerName();

        $this->form_validation->set_rules('bonus_rate', lang('lang.bonus'), 'trim|required|alpha_dash');
        $this->form_validation->set_rules('rebate_rate', lang('lang.rebate'), 'trim|required|numeric');
        $this->form_validation->set_rules('sourceCode', 'Agent Source Code', 'trim|required');

        if ($this->form_validation->run() == false) {
            $result = [
                'status' => 'error',
                'message' => validation_errors()
            ];

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['message']);
                return redirect('/' . $controller_name . '/agent_information/' . $agent_id. '#agent_tracking_code');
            }
        }

        $bonus_rate = $this->input->post('bonus_rate');
        $rebate_rate = $this->input->post('rebate_rate');
        $player_type = $this->input->post('player_type');
        $sourceCode = $this->input->post('sourceCode');

        if($this->agency_model->existsSourceCode($agent_id, $sourceCode, $agentTrackingId)){
            $result = [
                'status' => 'error',
                'message' => lang('Save failed because the source code exists')
            ];

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['message']);
                return redirect('/' . $controller_name . '/agent_information/' . $agent_id. '#agent_tracking_code');
            }
        }

        $success = $this->agency_model->updateSourceCode($agentTrackingId, [
            'bonus_rate' => $bonus_rate,
            'rebate_rate' => $rebate_rate,
            'player_type' => $player_type,
            'tracking_source_code' => $sourceCode
        ]);

        if($success){
            $username=$this->agency_model->getAgentNameById($agent_id);
            $this->syncAgentCurrentToMDBWithLock($agent_id, $username, false);
        }

        $result = [
            'status' => 'success',
            'message' => NULL
        ];

        if ($success) {
            $result['status'] = 'success';
            $result['message'] = lang('Save settings successfully');
        } else {
            $result['status'] = 'error';
            $result['message'] = lang('Save settings failed');
        }

        if($this->input->is_ajax_request()){
            return $this->returnJsonResult($result);
        }else{
            $this->alertMessage(($result['status'] === 'success') ? self::MESSAGE_TYPE_SUCCESS : self::MESSAGE_TYPE_ERROR, $result['message']);
            return redirect('/' . $controller_name . '/agent_information/' . $agent_id. '#agent_tracking_code');
        }
    }

    public function remove_source_code($agent_id, $agentTrackingId){
        $controller_name = $this->getControllerName();
        $success=false;
        if(!empty($agent_id) && !empty($agentTrackingId)){
            $success=$this->agency_model->removeSourceCode($agentTrackingId);
        }

        $result = [
            'status' => 'success',
            'message' => NULL
        ];

        if($success){
            $username=$this->agency_model->getAgentNameById($agent_id);
            $this->syncAgentCurrentToMDBWithLock($agent_id, $username, false);
        }

        if ($success) {
            $result['status'] = 'success';
            $result['message'] = lang('Save settings successfully');
        } else {
            $result['status'] = 'error';
            $result['message'] = lang('Save settings failed');
        }

        if($this->input->is_ajax_request()){
            return $this->returnJsonResult($result);
        }else{
            $this->alertMessage(($result['status'] === 'success') ? self::MESSAGE_TYPE_SUCCESS : self::MESSAGE_TYPE_ERROR, $result['message']);
            return redirect('/' . $controller_name . '/agent_information/' . $agent_id. '#agent_tracking_code');
        }
    }

    public function check_unique_tracking_code(){
        $tracking_code = $this->input->post('tracking_code');
        $agent_id = $this->input->post('agent_id');

        if(!empty($tracking_code) && !$this->agency_model->is_unique_tracking_code($tracking_code, $agent_id)){
            $this->form_validation->set_message('check_unique_tracking_code',
                lang("Tracking code should be unique"));
            return false;
        }
        return true;
    }

    public function generate_source_code_shorturl(){
        $controller_name = $this->getControllerName();
        $success=false;

        if (!$this->isLoggedAgency($agent_id, $agent_name)) {
            $result = [
                'status' => 'success',
                'message' => lang('Incorrect login information')
            ];

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage(($result['status'] === 'success') ? self::MESSAGE_TYPE_SUCCESS : self::MESSAGE_TYPE_ERROR, $result['message']);
                return redirect('/');
            }
        }

        $data = [];
        $this->initAgentInfo($agent_id, $data);

        $agentTrackingId = $this->input->get('agentTrackingId');
        $agentTrackingDomain = $this->input->get('agentTrackingDomain');

        if(empty($agentTrackingId) || empty($agentTrackingDomain) || empty($data['agent']['tracking_code'])){
            $result = [
                'status' => 'success',
                'message' => lang('Incorrect data information')
            ];

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage(($result['status'] === 'success') ? self::MESSAGE_TYPE_SUCCESS : self::MESSAGE_TYPE_ERROR, $result['message']);
                return redirect('/');
            }
        }

        $agentSourceCode = $this->agency_model->getSourceCodeById($agent_id, $agentTrackingId);

        if(empty($agentSourceCode)){
            $result = [
                'status' => 'success',
                'message' => sprintf(lang('gen.error.not_exist'), lang('Agent Source Code'))
            ];

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage(($result['status'] === 'success') ? self::MESSAGE_TYPE_SUCCESS : self::MESSAGE_TYPE_ERROR, $result['message']);
                return redirect('/');
            }
        }

        $agent_domains = $data['agent_domains'];

        $tmp_shorturls = json_decode($agentSourceCode['shorturl'], TRUE);

        $current_shorturls = (isset($tmp_shorturls[$data['agent']['tracking_code']])) ? $tmp_shorturls[$data['agent']['tracking_code']] : [];

        $shorturls = [];

        foreach($agent_domains as $agent_domain){
            $shorturls[$agent_domain] = (isset($current_shorturls[$agent_domain])) ? $current_shorturls[$agent_domain] : ['http://' => NULL, 'https://' => NULL];
        }

        if(!isset($shorturls[$agentTrackingDomain])){
            $result = [
                'status' => 'success',
                'message' => lang('Incorrect domain information')
            ];

            if($this->input->is_ajax_request()){
                return $this->returnJsonResult($result);
            }else{
                $this->alertMessage(($result['status'] === 'success') ? self::MESSAGE_TYPE_SUCCESS : self::MESSAGE_TYPE_ERROR, $result['message']);
                return redirect('/');
            }
        }

        $long_url = $data['tracking_link_protocol'] . $agentTrackingDomain . AGENT_TRACKING_BASE_URL . $data['agent']['tracking_code'] . '/' . $agentSourceCode['tracking_source_code'];
        $short_url = $this->shorturl->long2short($long_url);

        $shorturls[$agentTrackingDomain][$data['tracking_link_protocol']] = $short_url;


            // $success=$this->agency_model->removeSourceCode($agentTrackingId);
        $success = $this->agency_model->updateSourceCode($agentTrackingId, ['shorturl' => json_encode([$data['agent']['tracking_code'] => $shorturls])]);

        if($success){
            $username=$this->agency_model->getAgentNameById($agent_id);
            $this->syncAgentCurrentToMDBWithLock($agent_id, $username, false);
        }

        $result = [
            'status' => 'success',
            'message' => NULL
        ];

        if ($success) {
            $result['status'] = 'success';
            $result['message'] = lang('Save settings successfully');
            $result['short_url'] = $short_url;
        } else {
            $result['status'] = 'error';
            $result['message'] = lang('Save settings failed');
        }

        if($this->input->is_ajax_request()){
            return $this->returnJsonResult($result);
        }else{
            $this->alertMessage(($result['status'] === 'success') ? self::MESSAGE_TYPE_SUCCESS : self::MESSAGE_TYPE_ERROR, $result['message']);
            return redirect('/' . $controller_name . '/agent_information/' . $agent_id. '#agent_tracking_code');
        }
    }
    // Tracking Code }}}1

    protected function initSettlementWl($agent_name = null, $status = 'current') {
        $data = [];
        $data['conditions'] = $this->safeLoadParams(array(
            'agent_name' => $agent_name,
            'parent_name' => '',
            'status' => $status,
            'source_name' => '',
        ));

        $agent_id = $this->session->userdata('agent_id');
        $agent = $this->agency_model->get_agent_by_id($agent_id);
        $data['parent_id'] = $agent_id;

        if ($this->utils->isEnabledFeature('alwasy_create_agency_settlement_on_view')) {
            $this->create_settlement_wl($agent_id);
        }

        $agent_username = $this->input->get('agent_username');
        if(empty($agent_username)) {
            $agent_username = $agent['agent_name'];
        }

        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        if(empty($date_from) && empty($date_to)){
            $date_to = date("Y-m-d");
            $date_from = date('Y-m-d', strtotime("-15 day", strtotime($date_to)));
        }

        $rows = [];
        $summary = [];

        $data['agent_username'] = $agent_username;
        $data['agent'] = $agent;
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;

        return $data;
    }

    protected function initReadonlyAccount($agentId, &$data){
        $success=$this->checkAgentIdPermission('manage_readonly_agency_account', $agentId);
        if(!$success){
            return $success;
        }

        $this->load->model(['agency_model']);
        $readonlyAccountList=$this->agency_model->loadReadonlySubAccount($agentId);
        // $readonlyAccountList=[];
        $data=[
            'agentId'=>$agentId,
            'maxAgencyReadonlyAccount'=>$this->utils->getConfig('max_agency_readonly_account'),
            'emptyReadonlyAccount'=>[
                'username'=>'',
                'password'=>'',
                'enabled'=>false,
            ],
            'readonlyAccountList'=>$readonlyAccountList,
        ];

        return $success;
    }

    protected function checkAgentIdPermission($permCode, $agentId){
        if($this->utils->isAdminSubProject()){
            if(!$this->permissions->checkPermissions($permCode) || empty($agentId)) {
                $this->error_access();
                return false;
            }
        }else if($this->utils->isAgencySubProject()){
            //check login
            $loggedAgentId=null;
            $logged=$this->isLoggedAgency($loggedAgentId);
            if(!$logged || $agentId!=$loggedAgentId){
                //wrong agent id
                redirect('/');
                return false;
            }
        }else{
            $this->error_access();
            return false;
        }
        return true;
    }

    protected function checkAgentIdPermissionForAJAX($permCode, $agentId){
        if($this->utils->isAdminSubProject()){
            if(!$this->permissions->checkPermissions($permCode) || empty($agentId)) {
                return [false, lang('No permission')];
            }
        }else if($this->utils->isAgencySubProject()){
            //check login
            $loggedAgentId=null;
            $logged=$this->isLoggedAgency($loggedAgentId);
            if(!$logged || $agentId!=$loggedAgentId){
                //wrong agent id
                return [false, lang('No permission')];
            }
        }else{
            return [false, lang('Unknown backend')];
        }
        return [true, null];
    }

    protected function saveReadonlyAccountAJAX($agentId){
        list($success, $message)=$this->checkAgentIdPermissionForAJAX('manage_readonly_agency_account', $agentId);
        $result=['success'=>$success];
        if(!$success){
            $result['message']=$message;
            return $result;
        }
        //default is failed
        $result['success']=false;

        $json=file_get_contents("php://input");
        if(!empty($json)){
            $obj=$this->utils->decodeJson($json);
            $this->utils->debug_log('decode json from input', $obj);
            if(!empty($obj)){
                $this->load->model(['agency_model']);
                //validate format
                $readonlyAccountList=$this->agency_model->loadReadonlySubAccount($agentId);
                if(empty($readonlyAccountList)){
                    $readonlyAccountList=[];
                }
                $existsUsername=[];
                foreach ($obj as $idx => $inputItem) {
                    $this->utils->debug_log('process line', $idx);
                    $username=$this->security->xss_clean($inputItem['username']);
                    if(in_array($username, $existsUsername)){
                        //found, return error
                        $result['message']=lang('Found duplicate username').' '.$username;
                        return $result;
                    }
                    $item=null;
                    //username exists
                    if(isset($readonlyAccountList[$idx]) && !empty($readonlyAccountList[$idx]['username'])){
                        //load from db
                        $item=$readonlyAccountList[$idx];
                    }

                    if(empty($item)){
                        $item=$this->agency_model->buildEmptyReadonlySubAccount();
                        $password=@$inputItem['password'];
                        $this->utils->debug_log('process password', $username, $password);
                        if(!empty($username)){
                            //require password
                            if(!empty($password)){
                                $password=$this->security->xss_clean($password);
                                $this->utils->debug_log('try encrypt password', $username, $password);
                                //only accept password for new username
                                $encryptedPassword=$this->utils->encryptPassword($password, $error);
                                if(!empty($error) || $encryptedPassword===false){
                                    $this->utils->error_log('encrypt password failed', $error);
                                    $result['message']=lang('Encrypt password failed');
                                    return $result;
                                }
                                $item['password']=$encryptedPassword;
                            }else{
                                $this->utils->debug_log('empty password', $username);
                                $result['message']=lang('Please fill password for username').' '.$username;
                                return $result;
                            }
                        }
                    }

                    $item['username']=$username;
                    $item['enabled']=!!$inputItem['enabled'];
                    $readonlyAccountList[$idx]=$item;
                    if(!empty($username)){
                        $existsUsername[]=$username;
                    }
                }

                $this->utils->debug_log('generate and save readonlyAccountList', $readonlyAccountList, $agentId);
                $this->agency_model->saveReadonlySubAccount($agentId, $readonlyAccountList);
                //check duplicate username
                $result['success']=true;
                $result['message']=lang('Save readonly sub-account successfully');
            }else{
                $result['message']=lang('Cannot save empty account');
            }
        }else{
            $result['message']=lang('Cannot save empty account');
        }

        return $result;
    }

    public function resetReadonlyAccountPasswordAJAX($agentId, $indexOfAccount){
        list($success, $message)=$this->checkAgentIdPermissionForAJAX('manage_readonly_agency_account', $agentId);
        $result=['success'=>$success];
        if(!$success){
            $result['message']=$message;
            return $result;
        }
        //default is failed
        $result['success']=false;

        $this->load->model(['agency_model']);
        $readonlyAccountList=$this->agency_model->loadReadonlySubAccount($agentId);
        if(!empty($readonlyAccountList)){
            $found=false;
            //$readonlyAccountList
            foreach ($readonlyAccountList as $idx=>&$acc) {
                if($idx==$indexOfAccount){
                    $this->utils->debug_log('find account', $idx, $acc);
                    //reset password
                    $password=$this->utils->safeRandomString(4);
                    //save back
                    $encryptedPassword=$this->utils->encryptPassword($password, $error);
                    if(!empty($error) || $encryptedPassword===false){
                        $this->utils->error_log('encrypt password failed', $error);
                        $result['message']=lang('Encrypt password failed');
                        return $result;
                    }
                    $acc['password']=$encryptedPassword;
                    $this->agency_model->saveReadonlySubAccount($agentId, $readonlyAccountList);
                    $result['success']=true;
                    $result['message']=lang('Reset password of readonly sub-account successfully');
                    $result['new_password']=$password;
                    $result['username']=$acc['username'];
                    $found=true;
                    break;
                }
            }
            if(!$found){
                $result['message']=lang('Not found this account');
            }
        }else{
            //not found
            $result['message']=lang('Not found this account');
        }

        return $result;
    }

}
// zR to open all folded lines in vim
// vim:ft=php:fdm=marker
// end of Base_agency_controller.php
