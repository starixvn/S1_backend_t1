<?php

trait agent_list_info_module{

    /**
     *  show agents (search conditions supported)
     *
     *  @param  <param>
     *  @return <return>
     */
    public function agent_list($agentName = '', $currency = '', $agentLevel = '') {
        if (!$this->permissions->checkPermissions('agent_list')) {
            $this->error_access();
        } else {

            if (empty($currency)) {
                $currency = $this->utils->getConfig('default_currency');
            }

            $data['conditions'] = $this->safeLoadParams(array(
                'agent_name' => $agentName,
                'currency' => $currency,
                'agent_level' => $agentLevel,
            ));

            $this->utils->debug_log($data['conditions']);

            $this->load_template(lang('Agent List'), '', '', 'agency');
            $this->template->add_js('resources/js/bootstrap-switch.min.js');
            $this->template->add_css('resources/css/bootstrap-switch.min.css');
            $this->addJsTreeToTemplate();
            $this->template->write_view('main_content', 'agency_management/agent_list', $data);
            $this->template->render();
        }
    }

    /**
     *  create an agent using given structure template
     *
     *  @param  int structure_id
     *  @return
     */
    public function create_agent($structure_id = null, $vip_levels = '') {

        if (!$this->permissions->checkPermissions('edit_agent')) {
            return $this->error_access();
        }

        $this->load->model('game_type_model');
        $data['game_types'] = $this->game_type_model->getGameTypesArray();
        $agent_level = 0;

        // $data['vipgrouplist'] = $this->group_level->getVipGroupList();
        // $this->load->model('game_type_model');
        // $data['game_types'] = $this->game_type_model->getGameTypesArray();
        //$this->utils->debug_log('game_types', $data['game_types'], count($data['game_types']));
        // $agent_level = 0; // default is Level 0
        $agent_type = array();
        if (!empty($structure_id)) {
            $structure_details = $this->agency_model->get_structure_by_id($structure_id);
            //$this->utils->debug_log('Structure Details:', $structure_details);

            // if (empty($vip_levels)) {
            //     $vip_levels = $structure_details['vip_levels'];
            // }
            if ($structure_details['can_have_sub_agent']) {
                $agent_type[] = 'can-have-sub-agents';
            }
            if ($structure_details['can_have_players']) {
                $agent_type[] = 'can-have-players';
            }
            if ($structure_details['can_view_agents_list_and_players_list']) {
                $agent_type[] = 'can-view-agents-list-and-players-list';
            }
            if ($structure_details['show_bet_limit_template']) {
                $agent_type[] = 'show-bet-limit-template';
            }
            if ($structure_details['show_rolling_commission']) {
                $agent_type[] = 'show-rolling-commission';
            }
            if ($structure_details['can_do_settlement']) {
                $agent_type[] = 'can-do-settlement';
            }

            //$agent_level_name = $this->get_agent_level_name($structure_details, $agent_level);
            $data['conditions'] = $this->safeLoadParams(array(
                'structure_id' => $structure_details['structure_id'],
                'agent_name' => '',
                'firstname' => '',
                'lastname' => '',
                'password' => '',
                'confirm_password' => '',
                'currency' => $structure_details['currency'],
                'status' => $structure_details['status'],
                'credit_limit' => $structure_details['credit_limit'],
                'available_credit' => '0.00', // $structure_details['available_credit'],
                'agent_level' => $agent_level.'',
                // 'allowed_level_names' => $structure_details['allowed_level_names'],
                'vip_level' => $structure_details['vip_level'],
                // 'rev_share' => $structure_details['rev_share'],
                // 'rolling_comm' => $structure_details['rolling_comm'],
                // 'rolling_comm_basis' => $structure_details['rolling_comm_basis'],
                // 'total_bets_except' => $structure_details['total_bets_except'],
                'can_have_sub_agent' => $structure_details['can_have_sub_agent'],
                'can_have_players' => $structure_details['can_have_players'],
                'can_view_agents_list_and_players_list' => $structure_details['can_view_agents_list_and_players_list'],
                'can_do_settlement' => $structure_details['can_do_settlement'],
                'show_bet_limit_template' => $structure_details['show_bet_limit_template'],
                'show_rolling_commission' => $structure_details['show_rolling_commission'],
                'enabled_can_have_sub_agent' => 1,
                'enabled_can_have_players' => 1,
                'enabled_show_bet_limit_template' => 1,
                'enabled_show_rolling_commission' => 1,
                'enabled_can_view_agents_list_and_players_list' => 1,
                'enabled_can_do_settlement' => 1,
                'settlement_period' =>  $structure_details['settlement_period'],
                'start_day' => $structure_details['settlement_start_day'],
                'before_credit' => '0',
                'agent_count' => '1',
                'parent_id' => '0',
                // 'vip_groups' => $structure_details['vip_groups'],
                // 'vip_levels' => $vip_levels,
                'tracking_code' => '',
                'note' => '',
                'agent_type' => $agent_type,
                'tracking_code' => '', # strtoupper(random_string()), # When an Agent is creating his/her Sub-agent, the tracking code of the sub-agent should be the same as its USERNAME.
                'note' => '',
                'admin_fee' => number_format($structure_details['admin_fee'],2),
                'transaction_fee' => number_format($structure_details['transaction_fee'],2),
                'bonus_fee' => number_format($structure_details['bonus_fee'],2),
                'cashback_fee' => number_format($structure_details['cashback_fee'],2),
                'min_rolling_comm' => number_format($structure_details['min_rolling_comm'],2),
            ));

            $structure_game_platforms = $this->agency_model->get_structure_game_platforms($structure_id);
            $structure_game_types = $this->agency_model->get_structure_game_types($structure_id);
            $data['game_platform_settings']['conditions']['game_platforms'] = array_column($structure_game_platforms, NULL, 'game_platform_id');
            $data['game_platform_settings']['conditions']['game_types'] = array_column($structure_game_types, NULL, 'game_type_id');

            $this->utils->debug_log('Create AGENT CONDITIONS111:', $data['conditions']);
        } else {
            $defaultVipLevel=1;

            $data['conditions'] = $this->safeLoadParams(array(
                'structure_id' => '',
                'agent_name' => '',
                'firstname' => '',
                'lastname' => '',
                'password' => '',
                'confirm_password' => '',
                'currency' => $this->utils->getConfig('default_currency'),
                'status' => 'active', // active in default
                'credit_limit' => '0.00',
                'available_credit' => '0.00',
                'agent_level' => $agent_level,
                'agent_level_name' => '',
                'vip_level' => $defaultVipLevel,
                'can_have_sub_agent' => '',
                'can_have_players' => '',
                'can_view_agents_list_and_players_list' => '1',
                'show_bet_limit_template' => '',
                'show_rolling_commission' => '',
                'can_do_settlement'=>'',
                'enabled_can_have_sub_agent' => 1,
                'enabled_can_have_players' => 1,
                'enabled_show_bet_limit_template' => 1,
                'enabled_show_rolling_commission' => 1,
                'enabled_can_view_agents_list_and_players_list' => 1,
                'enabled_can_do_settlement' => 1,
                'settlement_period' => '',
                'start_day' => '',
                'before_credit' => '0',
                'agent_count' => '1',
                'parent_id' => '0',
                'tracking_code' => '', # strtoupper(random_string()), # When an Agent is creating his/her Sub-agent, the tracking code of the sub-agent should be the same as its USERNAME.
                'note' => '',
                'agent_type' => array(),
                'admin_fee' => '0.00',
                'deposit_fee' => '0.00',
                'withdraw_fee' => '0.00',
                'transaction_fee' => '0.00',
                'bonus_fee' => '0.00',
                'cashback_fee' => '0.00',
                'min_rolling_comm' => '0.00',
                'deposit_comm' => '0.00',
                'player_prefix' => '',
            ));

            $data['is_create'] = true;

            $this->utils->debug_log('Create AGENT CONDITIONS222:', $data['conditions']);
        }

        // $data['max_allowed_level'] = $this::MAX_ALLOWED_LEVEL;
        $data['fields'] = $this->fields_array;
        $data['labels'] = $this->labels_array;
        $data['agency_player_rolling_settings']=$this->utils->getConfig('agency_player_rolling_settings');

        $is_new = true;
        $this->get_game_comm_settings($data, null, 'agent', $is_new);
        $data['game_platform_settings']['agent_level'] = $data['conditions']['agent_level'];

        $data['vip_levels'] = $this->agency_model->get_vip_levels();
        $data['all_agents'] = $this->agency_model->get_all_sub_agents();
        $data['is_agent'] = TRUE;
        $data['form_url'] = site_url('agency_management/verify_agent');
		$data['validate_ajax_url'] = site_url('/agency_management/create_agent_validation_ajax');
		$data['parent_game_ajax_url'] = site_url('/agency_management/get_parent_game_types_ajax');
        // get all agent templates
        $data['agent_templates'] = $this->agency_model->get_structure_id_and_names();
		$data['copy_template_ajax_url'] = site_url('/agency_management/agent_copy_template_ajax');
        $data['controller_name'] = 'agency_management';
        $data['show_currency_options'] = true;

        $this->load_template(lang('Create Agent'), '', '', 'agency');
        $this->template->add_js('resources/js/bootstrap-switch.min.js');
        $this->template->add_css('resources/css/bootstrap-switch.min.css');

        // $this->addJsTreeToTemplate();

        $this->template->write_view('main_content', 'includes/common_agent_form', $data);
        // $this->template->write_view('main_content', 'agency_management/create_agent', $data);
        $this->template->render();
    } // create_agent  }}}2

    // agent_form_rules {{{2
    /**
     *  set for rules for agent creation
     *
     *  @param
     *  @return
     */
    // private function agent_form_rules() {
    //     /*
    //     $this->form_validation->set_rules('vip_level[]', lang('VIP Level'), 'trim|required');
    //     $this->form_validation->set_rules('settlement_period[]', lang('Settlement Period'), 'required');
    //     */
    //     $this->form_validation->set_rules('tracking_code', lang('Tracking Code'),
    //          'trim|required|min_length[4]|max_length[20]|alpha_numeric|callback_check_unique_tracking_code');
    //     $this->form_validation->set_rules('currency', lang('Currency'), 'trim|required');
    //     $this->form_validation->set_rules('status', lang('Status'), 'trim|required');
    //     $this->form_validation->set_rules('password', lang('Password'),
    //          'trim|required|min_length[6]|max_length[12]');
    //     $this->form_validation->set_rules('confirm_password', lang('Confirm Password'),
    //          'trim|required|callback_confirmPassword');
    //     $this->form_validation->set_rules('credit_limit', lang('Credit Limit'),
    //          'trim|required|numeric|greater_than[0]|less_than['.$this->utils->getConfig('agent_max_credit_limit').']|xss_clean|callback_check_credit_limit');
    //     $this->form_validation->set_rules('available_credit', lang('Available Credit'),
    //          'trim|numeric|xss_clean|callback_checkCredit');
    //     // $this->form_validation->set_rules('rev_share', lang('Rev Share'),
    //     //      'trim|required|numeric|xss_clean|callback_check_rev_share');
    //     // $this->form_validation->set_rules('rolling_comm_basis', lang('Rolling Comm Basis'), 'required');
    //     // $this->form_validation->set_rules('rolling_comm', lang('Rolling Comm'),
    //     //      'trim|required|numeric|xss_clean|callback_check_rolling_comm');
    //     // $this->form_validation->set_rules('player_vip_levels', lang('VIP Levels'),
    //     //     'callback_check_player_vip_levels');
    //     $this->form_validation->set_rules('agent_settlement_period', lang('Settlement Period'),
    //         'callback_check_agent_settlement_period');
    // } // agent_form_rules }}}2
    // add_new_agent {{{2
    /**
     *  Add a new agent into DB table agency_agents
     *
     *  @param
     *  @return agent ID
     */

    private function add_new_agent($username) {
        $agent_types = $this->input->post('agent_type');

        // $rolling_comm_basis = $this->input->post('rolling_comm_basis');
        // $total_bets_except = '';
        // if ('total_bets' == $rolling_comm_basis) {
        //     $total_bets_except = $this->input->post('except_game_type');
        // }
        //$this->utils->debug_log($rolling_comm_basis);
        //$this->utils->debug_log($total_bets_except);

        $settlement_period = $this->input->post('settlement_period');
        //$this->utils->debug_log($settlement_period);
        //$this->utils->debug_log(implode(",", $settlement_period));
        $start_day = '';
        if ($settlement_period == 'Weekly') {
            $start_day = $this->input->post('start_day');
        }
        //$this->utils->debug_log($start_day);

        $today = date("Y-m-d H:i:s");

        $password = $this->salt->encrypt($this->input->post('password'), $this->getDeskeyOG());
        $available_credit = $this->input->post('available_credit');
        $parent_id = $this->input->post('parent_id');

        if(!empty($parent_id)) {
            $parent_agent_details = $this->agency_model->get_agent_by_id($parent_id);
        } else {
            $parent_agent_details = array(
                'can_have_sub_agent' => 1,
                'can_have_players' => 1,
                'can_do_settlement' => 1,
                'can_view_agents_list_and_players_list' => 1,
                'show_bet_limit_template' => 1,
                'show_rolling_commission' => 1,
            );
        }
        // $status = $this->input->post('status');
        // $active = $status == 'active' ? 1:0;

        //$vip_levels = $this->input->post('vip_level');

        // $selected_vip_levels = $this->input->post('selected_vip_levels');
        // $this->utils->debug_log('selected_vip_levels', $selected_vip_levels, explode(',', $selected_vip_levels));
        // list($player_vip_groups, $player_vip_levels) = $this->agency_library->get_player_vip_info(explode(',',$selected_vip_levels));
        // $this->utils->debug_log('vip groups levels', $player_vip_groups, $player_vip_levels);

        // $parent_id = $this->input->post('parent_id');
        // $available_credit = $this->input->post('available_credit');

        $data = array(
            'agent_name' => $username,// $this->input->post('agent_name'),
            'tracking_code' => $this->input->post('tracking_code'),
            'password' => $password,
            'currency' => $this->input->post('currency'),
            'credit_limit' => $this->input->post('credit_limit'),
            'available_credit' => $available_credit,
            'status' => $this->input->post('status'),
            'active' => $this->input->post('status') == 'active'? 1:0,
            // 'rev_share' => $this->input->post('rev_share'),
            // 'rolling_comm' => $this->input->post('rolling_comm'),
            // 'rolling_comm_basis' => $rolling_comm_basis,
            // 'total_bets_except' => $total_bets_except,
            'agent_level' => $this->input->post('agent_level'),
            'agent_level_name' => $this->input->post('agent_level_name'),
            'can_have_sub_agent' => in_array('can-have-sub-agents', $agent_types)? $parent_agent_details['can_have_sub_agent'] : 0,
            'can_have_players' => in_array('can-have-players', $agent_types)? $parent_agent_details['can_have_players'] : 0,
            'can_do_settlement' => in_array('can-do-settlement', $agent_types)? $parent_agent_details['can_do_settlement'] : 0,
            'can_view_agents_list_and_players_list' => in_array('can-view-agents-list-and-players-list', $agent_types)? $parent_agent_details['can_view_agents_list_and_players_list'] : 0,
            'show_bet_limit_template' => in_array('show-bet-limit-template', $agent_types)? $parent_agent_details['show_bet_limit_template'] : 0,
            'show_rolling_commission' => in_array('show-rolling-commission', $agent_types)? $parent_agent_details['show_rolling_commission'] : 0,
            'vip_level'                 => $this->input->post('vip_level'),
            //'vip_level_name' => $this->get_vip_level_names($vip_levels),
            //'vip_group_name' => '',
            'settlement_period' => $settlement_period,
            'settlement_start_day' => $start_day,
            'created_on' => $today,
            'updated_on' => $today,
            'parent_id' => $parent_id,

            'admin_fee' => $this->input->post('admin_fee'),
            'transaction_fee' => $this->input->post('transaction_fee'),
            'bonus_fee' => $this->input->post('bonus_fee'),
            'cashback_fee' => $this->input->post('cashback_fee'),
            'min_rolling_comm' => $this->input->post('min_rolling_comm'),
            // 'vip_groups' => implode(',', $player_vip_groups),
            // 'vip_levels' => implode(',', $player_vip_levels),

            // generate keys when creating new agent
            'staging_secure_key' => md5('stg_secure'.$username),
            'staging_sign_key' => md5('stg_sign'.$username),
            'live_secure_key' => md5('live_secure'.$username),
            'live_sign_key' => md5('live_sign'.$username),
            'player_prefix' => $this->input->post('player_prefix'),
            'live_mode' => $this->input->post('live_mode')
        );
        $this->utils->debug_log($data);

        if ($parent_id > 0){
            $parent_details = $this->agency_model->get_agent_by_id($parent_id);
            $data['agent_level'] = $parent_details['agent_level'] + 1;
        }
        $this->agency_model->startTrans();

        $agent_id = $this->agency_model->add_agent($data);

        if ($parent_id > 0){
            $data = array(
                'available_credit' => $parent_details['available_credit'] - $available_credit,
            );
            $this->agency_model->update_agent($parent_id, $data);
        }

        /*
        // $this->db->delete('agency_agent_game_platforms', array('agent_id' => $agent_id));
        $game_platforms = $this->input->post('game_platforms');
        if ( ! empty($game_platforms)) {

            $this->utils->debug_log('post game_platforms param', $game_platforms);

            $game_platforms = array_filter($game_platforms, function(&$game_platform, $game_platform_id) use ($agent_id) {
                $enabled = $game_platform['enabled'];
                if ($enabled) {
                    $game_platform['game_platform_id'] = $game_platform_id;
                    $game_platform['agent_id'] = $agent_id;
                    unset($game_platform['enabled']);
                }
                return $enabled;
            }, ARRAY_FILTER_USE_BOTH );

            $this->utils->debug_log('update agency_agent_game_platforms', $game_platforms);

            if(!empty($game_platforms)){
                $this->db->insert_batch('agency_agent_game_platforms', $game_platforms);
            }

        }

        # UPDATE GAME TYPES
        // $this->db->delete('agency_agent_game_types', array('agent_id' => $agent_id));
        $game_types = $this->input->post('game_types');
        if (! empty($game_types)) {

            $this->utils->debug_log('post game_types param', $game_types);

            $game_types = array_filter($game_types, function(&$game_platform, $game_type_id) use ($agent_id) {

                // $enabled=true;
                //$enabled = isset($game_platform['rev_share']) && isset($game_platform['rolling_comm_basis']);
                $enabled = isset($game_platform['pattern_id']);

                // if ($enabled) {

                    $game_platform['game_type_id'] = $game_type_id;
                    $game_platform['agent_id'] = $agent_id;

                // }

                return $enabled;

            }, ARRAY_FILTER_USE_BOTH );


            $this->utils->debug_log('update agency_agent_game_types', $game_types);

            if(!empty($game_types)){
                $this->db->insert_batch('agency_agent_game_types', $game_types);
            }

        } */
        $game_platforms = $this->input->post('game_platforms');
        $game_types = $this->input->post('game_types');
        $this->agency_model->add_game_comm_settings($game_platforms, $game_types, $agent_id, 'agent');

        $succ = $this->agency_model->endTransWithSucc();
        if (!$succ) {
            throw new Exception('Sorry, save agent failed.');
        }
        return $agent_id;
    } // add_new_agent  }}}2


    public function _check_length($input) {
        $length = strlen($input);
        $min_prefix_agent=3;
        $max_prefix_agent=5;
        if ($length <= $max_prefix_agent && $length >= $min_prefix_agent) {
            return TRUE;
        } elseif ($length < $min_prefix_agent) {
            $this->form_validation->set_message('_check_length', 'Minimum number of characters is ' . $min_prefix_agent);
            return FALSE;
        } elseif ($length > $max_prefix_agent) {
            $this->form_validation->set_message('_check_length', 'Maximum number of characters is ' . $max_prefix_agent);
            return FALSE;
        }
    }

    // verify_agent {{{2
    /**
     *  insert an agent into table agency_agents
     *
     *  @param
     *  @return
     */
    public function verify_agent($parent_id = null) {
        if (!$this->permissions->checkPermissions('edit_agent')) {
            return $this->error_access();
        }

        $this->utils->debug_log('verify_agent parent_ID', $parent_id);
        $this->utils->debug_log('verify_agent inputs: ', $this->input->post());

        $this->agent_form_rules();
        $this->form_validation->set_rules('tracking_code', lang('Tracking Code'),
             'trim|required|min_length[4]|max_length[20]|alpha_numeric|callback_check_unique_tracking_code');
        $this->form_validation->set_rules('agent_name', lang('Agent Name'),
             'trim|required|min_length[4]|max_length[12]|alpha_numeric|is_unique[agency_agents.agent_name]');

        if($this->input->post('player_prefix'))
            $this->form_validation->set_rules('player_prefix', lang('Player Prefix'),'trim|callback__check_length|alpha_numeric');

        if ($this->form_validation->run() == false) {
            // $selected_vip_levels = $this->input->post('selected_vip_levels');
            // list($player_vip_groups, $player_vip_levels) =
            //     $this->agency_library->get_player_vip_info(explode(',',$selected_vip_levels));
            // $vip_levels = implode(',', $player_vip_levels);
            $this->utils->debug_log('verify_agent form ERRORs: ', validation_errors()); //form_error());
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Failed to create agent'));
            if ($parent_id == null) {
                $structure_id = $this->input->post('structure_id');
                $this->create_agent($structure_id);
            } else {
                $this->create_sub_agent($parent_id);
            }
        } else {

            $parent_id = $this->input->post('parent_id');
            $this->utils->debug_log('parent_ID for sub agent creation:', $parent_id);

            $agent_id=null;
            $username=$this->input->post('agent_name');
            $success=$this->utils->globalLockAgencyRegistration($username, function()
                    use(&$agent_id, $username){
                $agent_id = $this->add_new_agent($username);
                $this->utils->debug_log($agent_id);
                $success=!empty($agent_id);
                if($success){
                    $this->syncAgentCurrentToMDB($agent_id, false);
                }
                return $success;
            });

            if(!$success){
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Failed to create agent'));
                if ($parent_id == null) {
                    $structure_id = $this->input->post('structure_id');
                    $this->create_agent($structure_id);
                } else {
                    $this->create_sub_agent($parent_id);
                }
                return;
            }

            // level 0 agent can only be created by admin.
            // a sub-agent can be created by admin or the parent agent.
            $this->agency_library->record_transaction_on_creation($parent_id, $agent_id);

            //only for debug/test
            $agent_details = $this->agency_model->get_agent_by_id($agent_id);
            $this->utils->debug_log('Agent details after creation:', $agent_details);

            if ($parent_id > 0) {
                $action = 'create_sub_agent';
                $link_url = site_url('agency_management/create_sub_agent') . '/' . $parent_id;
            } else {
                $action = 'create_agent';
                $link_url = site_url('agency_management/create_agent');
            }
            $log_params = array(
                'action' => $action,
                'link_url' => $link_url,
                'done_by' => $this->authentication->getUsername(),
                'done_to' => $agent_details['agent_name'],
                'details' => 'Create agent '. $agent_details['agent_name'],
            );
            $this->agency_library->save_action($log_params);

            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save successfully'));
            //redirect("agency_management/agent_list", "refresh");
            // $check = false;
            redirect("agency_management/agent_information/" . $agent_id);
        }
    } // verify_agent  }}}2
    // batch_create_agent {{{2
    /**
     *  create an agent using given structure template
     *
     *  @param  int structure_id
     *  @return
     */
    public function batch_create_agent($parent_id = 0, $vip_levels = '', $structure_id = null) {
        if (!$this->permissions->checkPermissions('edit_agent')) {
            return $this->error_access();
        }

        // $data['vipgrouplist'] = $this->group_level->getVipGroupList();
        // $this->load->model('game_type_model');
        // $data['game_types'] = $this->game_type_model->getGameTypesArray();
        //$this->utils->debug_log('game_types', $data['game_types'], count($data['game_types']));
        $agent_level = 0; // default is Level 0
        $agent_type = array();
        if ($structure_id != null) {
            $structure_details = $this->agency_model->get_structure_by_id($structure_id);
            //$this->utils->debug_log('Structure Details:', $structure_details);

            if (empty($vip_levels)) {
                $vip_levels = $structure_details['vip_levels'];
            }
            if ($structure_details['can_have_sub_agent']) {
                $agent_type[] = 'can-have-sub-agents';
            }
            if ($structure_details['can_have_players']) {
                $agent_type[] = 'can-have-players';
            }
            if ($structure_details['can_view_agents_list_and_players_list']) {
                $agent_type[] = 'can-view-agents-list-and-players-list';
            }
            if ($structure_details['show_bet_limit_template']) {
                $agent_type[] = 'show-bet-limit-template';
            }
            if ($structure_details['show_rolling_commission']) {
                $agent_type[] = 'show-rolling-commission';
            }
            //$agent_level_name = $this->get_agent_level_name($structure_details, $agent_level);
            $data['conditions'] = $this->safeLoadParams(array(
                'structure_id' => $structure_details['structure_id'],
                'agent_name' => '',
                'firstname' => '',
                'lastname' => '',
                'currency' => $structure_details['currency'],
                'status' => $structure_details['status'],
                'credit_limit' => $structure_details['credit_limit'],
                'available_credit' => $structure_details['available_credit'],
                'agent_level' => $agent_level.'',
                'agent_level_name' => '',
                // 'allowed_level_names' => $structure_details['allowed_level_names'],
                'vip_level' => $structure_details['vip_level'],
                // 'rev_share' => $structure_details['rev_share'],
                // 'rolling_comm' => $structure_details['rolling_comm'],
                // 'rolling_comm_basis' => $structure_details['rolling_comm_basis'],
                // 'total_bets_except' => $structure_details['total_bets_except'],
                'can_have_sub_agent' => $structure_details['can_have_sub_agent'],
                'can_have_players' => $structure_details['can_have_players'],
                'can_view_agents_list_and_players_list' => $structure_details['can_view_agents_list_and_players_list'],
                'show_bet_limit_template' => $structure_details['show_bet_limit_template'],
                'show_rolling_commission' => $structure_details['show_rolling_commission'],
                'enabled_show_bet_limit_template' => 1,
                'settlement_period' => $structure_details['settlement_period'],
                'start_day' => $structure_details['settlement_start_day'],
                'before_credit' => '0',
                'parent_id' => '0',
                'tracking_code' => '',
                'admin_fee' => number_format($structure_details['admin_fee'],2),
                'transaction_fee' => number_format($structure_details['transaction_fee'],2),
                'bonus_fee' => number_format($structure_details['bonus_fee'],2),
                'cashback_fee' => number_format($structure_details['cashback_fee'],2),
                'min_rolling_comm' => number_format($structure_details['min_rolling_comm'],2),
                'note' => '',
                // 'vip_groups' => $structure_details['vip_groups'],
                //'vip_levels' => $structure_details['vip_levels'],
                // 'vip_levels' => $vip_levels,
                'agent_type' => $agent_type,
            ));

            $structure_game_platforms = $this->agency_model->get_structure_game_platforms($structure_id);
            $structure_game_types = $this->agency_model->get_structure_game_types($structure_id);
            $data['game_platform_settings']['conditions']['game_platforms'] = array_column($structure_game_platforms, NULL, 'game_platform_id');
            $data['game_platform_settings']['conditions']['game_types'] = array_column($structure_game_types, NULL, 'game_type_id');

            $this->utils->debug_log('Create Agent conditions:', $data['conditions']);
        } else {
            $agent_level_name = '';
            $data['conditions'] = $this->safeLoadParams(array(
                'structure_id' => '',
                'agent_name' => '',
                'firstname' => '',
                'lastname' => '',
                'password' => '',
                'confirm_password' => '',
                'currency' => $this->utils->getConfig('default_currency'),
                'status' => 'active',
                'credit_limit' => '',
                'available_credit' => '',
                'agent_level' => $agent_level.'',
                //'agent_level_name' => '',
                'allowed_level_names' => '',
                'vip_level' => '',
                // 'rev_share' => '',
                // 'rolling_comm' => '',
                // 'rolling_comm_basis' => '',
                // 'total_bets_except' => '',
                'can_have_sub_agent' => '',
                'can_have_players' => '',
                'can_view_agents_list_and_players_list' => '1',
                'show_bet_limit_template' => '',
                'show_rolling_commission' => '',
                'enabled_show_bet_limit_template' => 1,
                'settlement_period' => '',
                'start_day' => '',
                'agent_count' => '',
                'before_credit' => '0',
                'parent_id' => $parent_id,
                // 'vip_groups' => '',
                // 'vip_levels' => $vip_levels,
                'agent_type' => $agent_type,
                'admin_fee' => '0.00',
                'transaction_fee' => '0.00',
                'bonus_fee' => '0.00',
                'cashback_fee' => '0.00',
                'min_rolling_comm' => '0.00',
                'note' => '',
            ));
        }

        // $data['min_allowed_level'] = $this::MIN_ALLOWED_LEVEL;
        // $data['max_allowed_level'] = $this::MAX_ALLOWED_LEVEL;
        $data['fields'] = $this->fields_array;
        $data['labels'] = $this->labels_array;

        $is_new = true;
        $this->get_game_comm_settings($data, null, 'agent', $is_new);
        $data['game_platform_settings']['agent_level'] = $data['conditions']['agent_level'];

        $data['vip_levels'] = $this->agency_model->get_vip_levels();
        $data['all_agents'] = $this->agency_model->get_all_sub_agents();
        $data['is_agent'] = TRUE;
        $data['is_batch'] = TRUE;
        $data['form_url'] = '/agency_management/verify_batch_agents';
		$data['validate_ajax_url'] = site_url('/agency_management/create_agent_validation_ajax');
		$data['parent_game_ajax_url'] = site_url('/agency_management/get_parent_game_types_ajax');
        // get all agent templates
        $data['agent_templates'] = $this->agency_model->get_structure_id_and_names();
		$data['copy_template_ajax_url'] = site_url('/agency_management/agent_copy_template_ajax');
        $data['controller_name'] = 'agency_management';

        $this->load_template(lang('Batch Create Agent'), '', '', 'agency');
        $this->template->add_js('resources/js/bootstrap-switch.min.js');
        $this->template->add_css('resources/css/bootstrap-switch.min.css');
        $this->template->add_css('resources/third_party/bootstrap-multiselect-master/dist/css/bootstrap-multiselect.css');
        $this->template->add_js('resources/third_party/bootstrap-multiselect-master/dist/js/bootstrap-multiselect.js');
        $this->template->add_js('resources/js/bootstrap-notify.min.js');

        // $this->addJsTreeToTemplate();

        $this->template->write_view('main_content', 'includes/common_agent_form', $data);
        // $this->template->write_view('main_content', 'agency_management/batch_create_agents', $data);
        $this->template->render();
    } // batch_create_agent  }}}2
    // add_batch_agents {{{2
    /**
     *  Add a new agent into DB table agency_agents
     *
     *  @param
     *  @return agent ID
     */
    private function add_batch_agents() {
        if (!$this->permissions->checkPermissions('edit_agent')) {
            return $this->error_access();
        }

        $agent_types = $this->input->post('agent_type');

        // $rolling_comm_basis = $this->input->post('rolling_comm_basis');
        // $total_bets_except = '';
        // if ('total_bets' == $rolling_comm_basis) {
        //     $total_bets_except = $this->input->post('except_game_type');
        // }
        // //$this->utils->debug_log($rolling_comm_basis);
        // $this->utils->debug_log($total_bets_except);

        $settlement_period = $this->input->post('settlement_period');
        //$this->utils->debug_log($settlement_period);
        //$this->utils->debug_log(implode(",", $settlement_period));
        $start_day = '';
        if ($settlement_period == 'Weekly') {
            $start_day = $this->input->post('start_day');
        }
        //$this->utils->debug_log($start_day);

        $today = date("Y-m-d H:i:s");

        //$vip_levels = $this->input->post('vip_level');

        // $selected_vip_levels = $this->input->post('selected_vip_levels');
        // $this->utils->debug_log('selected_vip_levels', $selected_vip_levels, explode(',', $selected_vip_levels));
        // list($player_vip_groups, $player_vip_levels) = $this->agency_library->get_player_vip_info(explode(',',$selected_vip_levels));
        // $this->utils->debug_log('vip groups levels', $player_vip_groups, $player_vip_levels);

        $controller = $this;
        $agent_name = $this->input->post('agent_name');
        $agent_count = $this->input->post('agent_count');
        $tracking_code = $this->input->post('tracking_code');
        $parent_id = $this->input->post('parent_id');
        $parent_details = $this->agency_model->get_agent_by_id($parent_id);
        $available_credit = $this->input->post('available_credit');
        $data = array(
            'agent_name' => '',
            'password' => $this->salt->encrypt($this->input->post('password'), $this->getDeskeyOG()),
            'currency' => $this->input->post('currency'),
            'credit_limit' => $this->input->post('credit_limit'),
            'available_credit' => $available_credit,
            'status' => $this->input->post('status'),
            'active' => $this->input->post('status') == 'active'? 1:0,
            // 'rev_share' => $this->input->post('rev_share'),
            // 'rolling_comm' => $this->input->post('rolling_comm'),
            // 'rolling_comm_basis' => $rolling_comm_basis,
            // 'total_bets_except' => $total_bets_except,
            'agent_level'  => $parent_details['agent_level'] + 1,
            'agent_level_name' => $this->input->post('agent_level_name'),
            'can_have_sub_agent'                    => is_array($agent_types) ? in_array('can-have-sub-agents', $agent_types)? 1:0 : 0,
            'can_have_players'                      => is_array($agent_types) ? in_array('can-have-players', $agent_types)? 1:0 : 0,
            'can_view_agents_list_and_players_list' => is_array($agent_types) ? in_array('can-view-agents-list-and-players-list', $agent_types)? 1:0 : 0,
            'show_bet_limit_template'               => is_array($agent_types) ? in_array('show_bet_limit_template', $agent_types)? 1:0 : 0,
            'show_rolling_commission'               => is_array($agent_types) ? in_array('show_rolling_commission', $agent_types)? 1:0 : 0,
            'vip_level'                 => $this->input->post('vip_level'),
            //'vip_level_name' => $this->get_vip_level_names($vip_levels),
            //'vip_group_name' => '',
            'settlement_period' => $settlement_period,
            'settlement_start_day' => $start_day,
            'created_on' => $today,
            'updated_on' => $today,
            'parent_id' => $parent_id,
            'note' => $this->input->post('note'),

            'admin_fee' => $this->input->post('admin_fee'),
            'transaction_fee' => $this->input->post('transaction_fee'),
            'bonus_fee' => $this->input->post('bonus_fee'),
            'cashback_fee' => $this->input->post('cashback_fee'),
            'min_rolling_comm' => $this->input->post('min_rolling_comm'),
            // 'vip_groups' => implode(',', $player_vip_groups),
            // 'vip_levels' => implode(',', $player_vip_levels),
        );
        $this->utils->debug_log('the sub agent data : ---->', $data);

        // $count = $this->input->post('agent_count');
        // $sub_ids = array();
        // $added_cnt = 0;
        // for($i = 1; true; $i++) {
        //     $data['agent_name'] = $agent_name . $i;
        //     $data['tracking_code'] = $tracking_code.$i;
        //     $agent_details = $this->agency_model->get_agent_by_name($data['agent_name']);
        //     if (!empty($agent_details)) {
        //         continue;
        //     }

        //     $this->agency_model->startTrans();

        //     $agent_id = $this->agency_model->add_agent($data);

        //     if ($parent_id > 0){
        //         $parent_details = $this->agency_model->get_agent_by_id($parent_id);
        //         $data2 = array(
        //             'available_credit' => $parent_details['available_credit'] - $available_credit,
        //         );
        //         $this->agency_model->update_agent($parent_id, $data2);
        //     }

        //     $succ = $this->agency_model->endTransWithSucc();
        //     if (!$succ) {
        //         throw new Exception('Sorry, save agent failed.');
        //     } else {
        //         $this->agency_library->record_transaction_on_creation($parent_id, $agent_id);
        //         $sub_ids[] = $agent_id;
        //         $added_cnt++;
        //         if ($added_cnt >= $count) {
        //             break;
        //         }
        //     }
        //     // level 0 agent can only be created by admin.
        //     // a sub-agent can be created by admin or the parent agent.
        //     //$this->agency_library->record_transaction_on_creation($parent_id, $agent_id);
        // }
        // return $sub_ids;

        $sub_agent_ids = array();
        while (count($sub_agent_ids) < $agent_count) {

            $agent_details = $this->agency_model->get_agent_by_name($agent_name);

            if ( ! empty($agent_details)) {
                continue;
            }

            $data['agent_name'] = $agent_name;

            $agent_id = NULL;


            $succ = $this->lockAndTransForAgencyCredit($parent_id, function()
                use ($controller, &$agent_id, $parent_id, $available_credit, $parent_details, $data) {

                $succ = FALSE;
                $parent_details = $this->agency_model->get_agent_by_id($parent_details['agent_id']);

                if ($parent_details['available_credit'] >= $available_credit) {

                    $agent_id = $this->agency_model->add_agent($data);

                    $succ = ! empty($agent_id);

                    if ($succ ){
                        if ($parent_id && $available_credit > 0) {

                            $succ = $this->transactions->createAgentToSubAgentTransaction($parent_id, $agent_id, $available_credit, 'on create sub-agent');

                        }

                        /*
                        $game_platforms = $this->input->post('game_platforms');
                        if ( ! empty($game_platforms)) {

                            $this->utils->debug_log('post game_platforms param', $game_platforms);

                            $game_platforms = array_filter($game_platforms, function(&$game_platform, $game_platform_id) use ($agent_id) {

                                $enabled = $game_platform['enabled'];

                                if ($enabled) {

                                    $game_platform['game_platform_id'] = $game_platform_id;
                                    $game_platform['agent_id'] = $agent_id;

                                    unset($game_platform['enabled']);

                                }

                                return $enabled;

                            }, ARRAY_FILTER_USE_BOTH );

                            $this->utils->debug_log('update agency_agent_game_platforms', $game_platforms);

                            if(!empty($game_platforms)){
                                $this->db->insert_batch('agency_agent_game_platforms', $game_platforms);
                            }

                        }

                        # UPDATE GAME TYPES
                        // $this->db->delete('agency_agent_game_types', array('agent_id' => $agent_id));
                        $game_types = $this->input->post('game_types');
                        if (! empty($game_types)) {

                            $this->utils->debug_log('post game_types param', $game_types);

                            $game_types = array_filter($game_types, function(&$game_platform, $game_type_id) use ($agent_id) {

                                // $enabled=true;
                                //$enabled = isset($game_platform['rev_share']) && isset($game_platform['rolling_comm_basis']);
                                $enabled = isset($game_platform['pattern_id']);

                                // if ($enabled) {

                                    $game_platform['game_type_id'] = $game_type_id;
                                    $game_platform['agent_id'] = $agent_id;

                                // }

                                return $enabled;

                            }, ARRAY_FILTER_USE_BOTH );


                            $this->utils->debug_log('update agency_agent_game_types', $game_types);

                            if(!empty($game_types)){
                                $this->db->insert_batch('agency_agent_game_types', $game_types);
                            }

                        } */
                        if ($succ) {
                            $game_platforms = $this->input->post('game_platforms');
                            $game_types = $this->input->post('game_types');
                            $this->agency_model->add_game_comm_settings($game_platforms, $game_types, $agent_id, 'agent');
                        }
                    }
                } else {
                    $controller->utils->error_log('no enought credit from agent', $parent_details['available_credit'], 'sub agent', $available_credit);
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
        if (!$this->permissions->checkPermissions('edit_agent')) {
            return $this->error_access();
        }

        $this->agent_form_rules();
        $this->form_validation->set_rules('agent_name', lang('Agent Name'),
             'trim|required|min_length[2]|max_length[12]|alpha_numeric|is_unique[agency_agents.agent_name]');
        $this->form_validation->set_rules('agent_count', lang('Count'),
             'trim|required|is_natural|greater_than[0]|less_than[20]|xss_clean');

        $this->form_validation->set_rules('parent_id', lang('Parent Agent'), 'trim|required|xss_clean');
        $parent_id = $this->input->post('parent_id');
        if ($this->form_validation->run() == false) {
            // $selected_vip_levels = $this->input->post('selected_vip_levels');
            // list($player_vip_groups, $player_vip_levels) =
            //     $this->agency_library->get_player_vip_info(explode(',',$selected_vip_levels));
            // $vip_levels = implode(',', $player_vip_levels);
            //
            $this->utils->debug_log('verify_batch_agents form ERRORs: ', validation_errors()); //form_error());
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Failed to create agent'));
            $this->batch_create_agent($parent_id);
        } else {
            $parent_id = $this->input->post('parent_id');
            $sub_ids = $this->add_batch_agents();
            $this->utils->debug_log('the sub ids --->', $sub_ids);

            // record action in agency log {{{3
            $log_params = array(
                'action' => 'batch_create_agent',
                'link_url' => site_url('agency_management/batch_create_agent'),
                'done_by' => $this->authentication->getUsername(),
                'done_to' => $this->authentication->getUsername(),
                'details' => 'Create level 0 agents: '. implode(',', $sub_ids),
            );
            $this->agency_library->save_action($log_params);
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save successfully'));
            // record action in agency log }}}3
            redirect("agency_management/agent_list", "refresh");
        }
    } // verify_batch_agents  }}}2
    // edit_agent {{{2
    /**
     *  edit an existed agent
     *
     *  @param  int agent_id
     *  @return
     */
    public function edit_agent($agent_id) {
        if (!$this->permissions->checkPermissions('edit_agent')) {
            return $this->error_access();
        }

        //permission of agent id and init
        $agent_details=$this->initEditAgentInfo($data, $agent_id);
        if(empty($agent_details)){
            $this->utils->error_log('agent privilege error '.$agent_id, $data);
            return $this->error_access();
        }

        $this->load_template(lang('Edit Agent'), '', '', 'agency');

        $data['show_currency_options'] = true;

        $this->template->write_view('main_content', 'includes/common_agent_form', $data);
        $this->template->render();
    } // edit_agent  }}}2

    // verify_update_agent {{{2
    /**
     *  update a agent and redirect to agent list
     *
     *  @return
     */
    public function verify_update_agent() {
        $this->agent_form_rules();
        $this->form_validation->set_rules('available_credit', lang('Available Credit'), '');
        // $this->form_validation->set_rules('agent_name', lang('Agent Name'),
             // 'trim|required|min_length[2]|max_length[12]|alpha_numeric|xss_clean|callback_check_edit_agent_name');
             //'trim|required|min_length[2]|max_length[12]|alpha_numeric|xss_clean');
        $this->form_validation->set_rules('tracking_code', lang('Tracking Code'),
             'trim|min_length[4]|max_length[20]|alpha_numeric|callback_check_unique_tracking_code');

        $agent_id = $this->input->post('agent_id');

        if ($this->form_validation->run() == false) {
            // $selected_vip_levels = $this->input->post('selected_vip_levels');
            // list($player_vip_groups, $player_vip_levels) =
            //     $this->agency_library->get_player_vip_info(explode(',',$selected_vip_levels));
            // $vip_levels = implode(',', $player_vip_levels);
            // $check=true;
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Failed to update agent:').validation_errors('<div class="error">', '</div>'));
            $this->edit_agent($agent_id);
        } else {

            $old_agent = $this->agency_model->get_agent_by_id($agent_id);
            $username=$old_agent['agent_name'];
            $agent_details = $this->update_agent($agent_id);

            $agent_details = $this->agency_model->get_agent_by_id($agent_id);

            //$this->saveUpdateAgentLog($old_agent, $agent_details);

            // record action in agency log {{{3
            $modified_fields = $this->check_modified_fields($old_agent, $agent_details);

            $log_params = array(
                'action' => 'modify_agent',
                'link_url' => site_url('agency_management/edit_agent/' . $agent_id) ,
                'done_by' => $this->authentication->getUsername(),
                'done_to' => $old_agent['agent_name'],
                'details' => lang('Edit Agent'). ' ' . $old_agent['agent_name']. ': '. $modified_fields,
            );
            $this->agency_library->save_action($log_params);

            if(!empty($agent_id)){
                $this->syncAgentCurrentToMDBWithLock($agent_id, $username, false);
            }

            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save successfully'));

            // record action in agency log }}}3
            redirect("/agency_management/agent_information/".$agent_id);
        }
    } // verify_update_agent  }}}2
    // check_edit_agent_name {{{2
    /**
     *  check whether agent name is unique
     *
     *  @param  string
     *  @return bool
     */
    // public function check_edit_agent_name() {
    //     $agent_name = $this->input->post('agent_name');
    //     $agent_id = $this->input->post('agent_id');
    //     $query_array = $this->agency_model->get_all_agents_by_name($agent_name);
    //     $this->utils->debug_log('check_edit_agent_name', $query_array, $agent_id, $agent_name);
    //     if (!empty($query_array)) {
    //         foreach($query_array as $rec) {
    //             if ($rec['agent_id'] != $agent_id) {
    //                 $this->form_validation->set_message('check_edit_agent_name', lang('Agent name has been used!'));
    //                 return false;
    //             }
    //         }
    //     } else {
    //         return true;
    //     }
    // } // check_edit_agent_name  }}}2
    // create_sub_agent {{{2
    /**
     *  create a sub-agent for given agent
     *
     *  @param  int current agent_id
     *  @return
     */
    public function create_sub_agent($agent_id, $vip_levels = '') {

        if (!$this->permissions->checkPermissions('edit_agent')) {
            return $this->error_access();
        }
        $this->load->model('game_type_model');
        $this->initCreateSubAgentInfo($data, $agent_id, $vip_levels);

        // $data['min_agent_level'] = $this::MIN_ALLOWED_LEVEL;
        // $data['max_agent_level'] = $this::MAX_ALLOWED_LEVEL;
        // $data['vipgrouplist'] = $this->group_level->getVipGroupList();
        // $data['game_types'] = $this->game_type_model->getGameTypesArray();
        //$this->utils->debug_log('game_types', $data['game_types'], count($data['game_types']));
        /*
        $agent_details = $this->agency_model->get_agent_by_id($agent_id);
        $this->utils->debug_log('create_sub_agent', $agent_details);

        // if (empty($vip_levels)) {
        //     $vip_levels = $agent_details['vip_levels'];
        // }
        $data['parent_id'] = $agent_details['agent_id'];
        $data['parent_name'] = $agent_details['agent_name'];
        $data['conditions'] = $this->safeLoadParams(array(
            'structure_id' => '',
            'agent_name' => '',
            'password' => '',
            'confirm_password' => '',
            'parent_id' => $agent_details['agent_id'],
            'parent_name' => $agent_details['agent_name'],
            'currency' => $agent_details['currency'],
            'status' => $agent_details['status'],
            'credit_limit' => $agent_details['credit_limit'],
            'available_credit' => '0.00',
            'agent_level' => $agent_details['agent_level'] + 1,
            'agent_level_name' => $agent_details['agent_level_name'],
            'vip_level' => $agent_details['vip_level'],
            // 'rev_share' => $agent_details['rev_share'],
            // 'rolling_comm' => $agent_details['rolling_comm'],
            // 'rolling_comm_basis' => $agent_details['rolling_comm_basis'],
            // 'total_bets_except' => $agent_details['total_bets_except'],
            'can_have_sub_agent' => $agent_details['can_have_sub_agent'],
            'can_have_players' => $agent_details['can_have_players'],
            'can_view_agents_list_and_players_list' => $agent_details['can_view_agents_list_and_players_list'],
            'show_bet_limit_template' => $agent_details['show_bet_limit_template'],
            'show_rolling_commission' => $agent_details['show_rolling_commission'],
            'settlement_period' => explode(',', $agent_details['settlement_period']),
            'start_day' => $agent_details['settlement_start_day'],
            'before_credit' => '0',
            'agent_count' => '1',
            'tracking_code' => '',
            'note' => '',
            'admin_fee' => number_format($agent_details['admin_fee'],2),
            'transaction_fee' => number_format($agent_details['transaction_fee'],2),
            'bonus_fee' => number_format($agent_details['bonus_fee'],2),
            'cashback_fee' => number_format($agent_details['cashback_fee'],2),
            // 'vip_groups' => $agent_details['vip_groups'],
            // 'vip_levels' => $vip_levels,
        ));

        $agent_game_platforms = $this->agency_model->get_agent_game_platforms($agent_id);
        $agent_game_types = $this->agency_model->get_agent_game_types($agent_id);
        foreach ($agent_game_types as &$agent_game_type) {
            $agent_game_type['rolling_comm'] = $agent_game_type['rolling_comm_out'];
            $agent_game_type['rolling_comm_out'] = NULL;
        }
        $data['game_platform_settings']['game_platform_list'] = $this->agency_model->get_game_platforms_and_types();
        $data['game_types'] = $this->game_type_model->getGameTypesArray();
        $data['game_platform_settings']['conditions']['game_platforms'] = array_column($agent_game_platforms, NULL, 'game_platform_id');
        $data['game_platform_settings']['conditions']['game_types'] = array_column($agent_game_types, NULL, 'game_type_id');

        // $data['min_allowed_level'] = $this::MIN_ALLOWED_LEVEL;
        // $data['max_allowed_level'] = $this::MAX_ALLOWED_LEVEL;

        $data['fields'] = $this->fields_array;
        $data['labels'] = $this->labels_array;
        $data['vip_levels'] = $this->agency_model->get_vip_levels();

        // $data['parent_id'] = $agent_id;
        $data['is_agent'] = TRUE;
        $data['form_url'] = site_url('agency_management/verify_agent/' . $agent_id);
         */
        $this->utils->debug_log('Create Sub Agent DATA:', $data);

        $this->load_template(lang('Create Sub Agent'), '', '', 'agency');
        $this->template->add_js('resources/js/bootstrap-switch.min.js');
        $this->template->add_css('resources/css/bootstrap-switch.min.css');

        // $this->addJsTreeToTemplate();

        // $this->template->write_view('main_content', 'agency_management/create_sub_agent', $data);
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
    //public function agent_add_players($agent_id, $agent_name) {
    public function agent_add_players($agent_id) {
        $data = array();
        $type_code = $this->player_manager->getBatchCode();
        if (!empty($type_code)) {
            $x = explode('-', $type_code['typeCode']);
            $data['type_code'] = $x['0'] . '-' . sprintf("%06s", ($x['1'] + 1));
        } else {
            $data['type_code'] = "OG-000001";
        }
        $data['agent_id'] = $agent_id;
        $agent_details = $this->agency_model->get_agent_by_id($agent_id);
        $agent_name = $agent_details['agent_name'];
        $data['parent_agent_name'] = $agent_name;
        $this->load->view('agency_management/ajax_add_account_process', $data);
    } // agent_add_players  }}}2
    // agent_list }}}1

    /**
     *  display agent information
     *
     *  @param  int agent_id
     *  @return
     */
    public function agent_information($agent_id) {
        if (!$this->permissions->checkPermissions('view_agent') || empty($agent_id)) {
            return $this->error_access();
        }
        $this->load_template(lang('Agent Information'), '', '', 'agency');
        $this->initAgentInfo($agent_id, $data);

        // if post is not empty save agency settings data into DB
        if (!empty($_POST)) {
            $this->save_agent_settings($agent_id);
        }

        $this->addJsTreeToTemplate();

        $this->template->write_view('main_content', 'agency_management/agent_information', $data);
        $this->template->render();

    }

    public function new_additional_agent_domain($agent_id){
        //add permission
        if (!$this->permissions->checkPermissions('edit_agency_domain')) {
            return $this->error_access();
        }

        $redirectUrl = '/agency_management/agent_information/' . $agent_id. '#agent_tracking_code';
        $this->verifyNewAdditionalAgentDomain($agent_id, $redirectUrl);
    }

    /*
    public function change_additional_agent_domain($agent_id, $agentTrackingId){
        $this->load->model(array('agency_model'));

        $agent_domain = $this->input->post('agent_domain');

        //try fix agent_domain
        if (!empty($agent_domain)) {
            $agent_domain = trim($agent_domain);
            //remove http://
            if (strpos($agent_domain, 'http://') !== false || strpos($agent_domain, 'https://') !== false) {
                $agent_domain = parse_url($agent_domain, PHP_URL_HOST);
            }
        }

        if($this->agency_model->existsAdditionalAgentDomain($agent_domain, $agentTrackingId)){
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save failed because the domain exists'));
            return redirect('/agency_management/agent_information/' . $agent_id. '#agent_tracking_code');
        }

        $success = $this->agency_model->updateAdditionalAgentDomain($agentTrackingId, $agent_domain);


        if ($success) {
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
        }

        redirect('/agency_management/agent_information/' . $agent_id. '#agent_tracking_code');
    }

    public function remove_additional_agent_domain($agent_id, $agentTrackingId){
        $this->load->model(array('agency_model'));
        $success=false;
        if(!empty($agent_id) && !empty($agentTrackingId)){
            $success=$this->agency_model->removeAdditionalAgentDomain($agentTrackingId);
        }

        if ($success) {
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
        }
        redirect('/agency_management/agent_information/' . $agent_id. '#agent_tracking_code');
    }

    public function new_source_code($agent_id){
        $this->load->model(array('agency_model'));

        $sourceCode = $this->input->post('sourceCode');

        if($this->agency_model->existsSourceCode($agent_id, $sourceCode)){
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save failed because the domain exists'));
            return redirect('/agency_management/agent_information/' . $agent_id. '#agent_tracking_code');
        }

        $success = $this->agency_model->newSourceCode($agent_id, $sourceCode);

        if ($success) {
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
        }

        redirect('/agency_management/agent_information/' . $agent_id. '#agent_tracking_code');
    }

    public function change_source_code($agent_id, $agentTrackingId){
        $this->load->model(array('agency_model'));

        $sourceCode = $this->input->post('sourceCode');

        if($this->agency_model->existsSourceCode($agent_id, $sourceCode, $agentTrackingId)){
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save failed because the source code exists'));
            return redirect('/agency_management/agent_information/' . $agent_id. '#agent_tracking_code');
        }

        $success = $this->agency_model->updateSourceCode($agentTrackingId, $sourceCode);

        if ($success) {
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
        }

        redirect('/agency_management/agent_information/' . $agent_id. '#agent_tracking_code');
    }

    public function remove_source_code($agent_id, $agentTrackingId){
        $this->load->model(array('agency_model'));
        $success=false;
        if(!empty($agent_id) && !empty($agentTrackingId)){
            $success=$this->agency_model->removeSourceCode($agentTrackingId);
        }

        if ($success) {
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
        }
        redirect('/agency_management/agent_information/' . $agent_id. '#agent_tracking_code');
    }
     */

	public function log_unlock_trackingcode() {
        $this->saveAction('Unlock Tracking Code',
            "User " . $this->authentication->getUsername() . " has unlock tracking code");
	}

	/**
	 * create tracking code
	 *
	 * @return	void
	 */
	public function edit_tracking_code($agent_id) {
        if (!$this->permissions->checkPermissions('edit_agency_tracking_code')) {
            return $this->error_access();
        }
        $userId = $this->authentication->getUserId();
        $redirectUrl = 'agency_management/agent_information/' . $agent_id.'#agent_tracking_code';
        $this->verifyEditTrackingCode($agent_id, $userId, $redirectUrl);
	}

    public function get_agent_details($agent_id){
        $this->initAgentInfo($agent_id, $data);
        $this->returnJsonResult($data);
    }

}
