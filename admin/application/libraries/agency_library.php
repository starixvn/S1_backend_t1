<?php
/**
 *   filename:   agency_library.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-06-13
 *   @brief:     library for agency sub system. shared between agency in BO and UI
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Agency_library {

    public $log_actions = array();

    const INCLUDE_ALL_DOWLINES = true;
    const RECENT_SETTLEMENT_CALCULATION_DAY = '-1 days';
    const EARLIEST_SETTLEMENT_CALCULATION_DAY = 'first day of last month'; # Calculate WL daily settlement for up to this day
    const EARLIEST_WL_REPORT_CALCULATION_DAY = 'first day of -2 month'; # Calculate WL settlement report for up to this day.
    # Note: In weekly settlement, cut-off of week may happen at the beginning of this period, resulting in extra settlement WL record

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->model(array('agency_model', 'transactions', 'wallet_model'));

        $this->utils=$this->ci->utils;

        $this->log_actions = array(
            'account_login' => array(
                'name' => lang('Account Login'),
                'link_name' => lang('Login'),
            ),
            'account_logout' => array(
                'name' => lang('Account Logout'),
                'link_name' => lang('Logout'),
            ),
            'login_as_agent' => array(
                'name' => lang('Login as Agent'),
                'link_name' => lang('Login as Agent'),
            ),
            'credit_in' => array(
                'name' => lang('Credit In'),
                'link_name' => lang('Increase Credit'),
            ),
            'credit_out' => array(
                'name' => lang('Credit Out'),
                'link_name' => lang('Decrease Credit'),
            ),
            'create_structure' => array(
                'name' => lang('Create Agent Template'),
                'link_name' => lang('Add New Agent Template'),
            ),
            'create_agent' => array(
                'name' => lang('Create Agent'),
                'link_name' => lang('Create New Agent'),
            ),
            'create_sub_agent' => array(
                'name' => lang('Create Agent'),
                'link_name' => lang('Create Sub Agent'),
            ),
            'batch_create_agent' => array(
                'name' => lang('Create Agent'),
                'link_name' => lang('Batch Create Agents'),
            ),
            'create_players' => array(
                'name' => lang('Create Players'),
                'link_name' => lang('Create Players'),
            ),
            'modify_structure' => array(
                'name' => lang('Modify Agent Template'),
                'link_name' => lang('Edit Agent Template'),
            ),
            'modify_agent' => array(
                'name' => lang('Modify Agent'),
                'link_name' => lang('Edit Agent'),
            ),
            'edit_player' => array(
                'name' => lang('Edit Player'),
                'link_name' => lang('Edit Player'),
            ),
            'modify_player' => array(
                'name' => lang('Modify Player'),
                'link_name' => lang('Edit Player'),
            ),
            'transfer_balance_to_binding_player' => array(
                'name' => lang('Transfer Balance to Binding Player'),
                'link_name' => lang('Transfer Balance to Binding Player'),
            ),
            'transfer_balance_from_binding_player' => array(
                'name' => lang('Transfer Balance from Binding Player'),
                'link_name' => lang('Transfer Balance from Binding Player'),
            ),
            'bind_player' => array(
                'name' => lang('Bind Player'),
                'link_name' => lang('Bind Player'),
            ),
            'freeze_agent' => array(
                'name' => lang('Freeze Agent'),
                'link_name' => lang('Freeze Agent'),
            ),
            'suspend_agent' => array(
                'name' => lang('Suspend Agent'),
                'link_name' => lang('Suspend Agent'),
            ),
            'activate_agent' => array(
                'name' => lang('Activate Agent'),
                'link_name' => lang('Activate Agent'),
            ),
        );
    }

    // common {{{1
    // get_all_player_ids_under_agent {{{2
    /**
     *  get all player ids under given agent
     *
     *  @param  int agent_id
     *  @param  bool whether include all downline players
     *  @return array for all player ids
     */
    public function get_all_player_ids_under_agent($agent_id, $all_downlines = false) {
        if ($all_downlines == true) {
            // get all downline sub agent ids (including agent_id itself)
            $all_ids = $this->ci->agency_model->get_all_sub_agent_ids($agent_id);
        } else {
            // all direct sub agent ids (NOT including agent_id itself)
            $all_ids = $this->ci->agency_model->get_sub_agent_ids_by_parent_id($agent_id);
        }

        $this->ci->load->model(array('player'));
        return $this->ci->player->get_player_ids_by_agent_ids($all_ids);
    } // get_all_player_ids_under_agent  }}}2
    // get_player_vip_info {{{2
    /**
     *  get vip group ids and vip level ids
     *
     *  @param  array ',' separated 'groupId_levelId's
     *  @return arrays for group ids and level ids
     */
    public function get_player_vip_info($selected_vip_levels) {
        $group_ids = array();
        $level_ids = array();
        foreach($selected_vip_levels as $str) {
            $arr = explode('_', $str);
            if (!isset($arr[1]) || empty($arr[1])) {
                continue;
            }
            $group_id = $arr[0];
            $level_id = $arr[1];
            if (!in_array($group_id, $group_ids)) {
                $group_ids[] = $group_id;
            }
            if (!in_array($level_id, $level_ids)) {
                $level_ids[] = $level_id;
            }
        }
        return array($group_ids, $level_ids);
    } // get_player_vip_info  }}}2
    // old_password_format {{{2
    /**
     *  format on old password
     *
     *  @param  str  encrypted password
     *  @param  int  max length of password
     *  @return str formatted password
     */
    public function old_password_format($pass, $max_len) {
        if($max_len < strlen($pass)) {
            $len = $max_len;
        } else {
            $len = strlen($pass);
        }
        return substr($pass, 0, $len);
    } // old_password_format  }}}2
    // get_vip_group_names {{{2
    /**
     *  get vip group names via group ids
     *
     *  @param  string , separated group ids
     *  @return string , separated group names
     */
    public function get_vip_group_names($d) {
        if (empty($d)) {
            return '';
        }
        $this->ci->load->model('group_level');
        $groups = array();

        $ids = explode(',', $d);
        foreach($ids as $id) {
            $group_details = $this->ci->group_level->getVIPGroupDetails($id);
            $groups[] = $group_details[0]['groupName'];
        }

        return implode(',', $groups);
    } // get_vip_group_names  }}}2
    // common }}}1

    // agents {{{1
    // create_agent_val_json {{{2
    /**
     *  create json string corresponding to agent name in select list for a given settlement
     *
     *  @param
     *  @return
     */
    public function create_agent_val_json($rec) {
        $this->ci->utils->debug_log('create_agent_val_json', $rec);

        $agent_id = $rec['agent_id'];
        $agent_level= $rec['agent_level'];
        $agent_level_name = $rec['agent_level_name'];

        $agent_val = array(
            'parent_id' => $agent_id,
            'agent_level' => $agent_level + 1,
            'agent_level_name' => $agent_level_name,
            'currency' => $rec['currency'],
        );

        return json_encode($agent_val);

    } // create_agent_val_json  }}}2
    // agents }}}1

    // record credit transaction {{{1
    // record_transaction_on_creation {{{2
    /**
     *  record transaction when creating an agent or a sub-agent
     *
     *  @param  int parent_id  0 for level 0 agent
     *  @param  int agent_id   ID of the newly-created agent
     *  @return int transaction_id
     */
    public function record_transaction_on_creation($parent_id, $agent_id) {
        $transaction_id = null;
        $agent_details = $this->ci->agency_model->get_agent_by_id($agent_id);
        if (!empty($agent_details['available_credit']) && $agent_details['available_credit'] > 0) {
            $name = $agent_details['agent_name'];
            $credit = $agent_details['available_credit'];
            if ($parent_id == 0) {
                // for level 0 agent which can only created by admin
                $trans_type = Transactions::FROM_ADMIN_TO_AGENT;
                $note = lang('Level 0 agent '). $name . lang(' is created').'.';
                $from_type = Transactions::ADMIN;
                $from_name = $this->ci->authentication->getUsername();
                $from_id = $this->ci->authentication->getUserId();
            } else {
                $parent_details = $this->ci->agency_model->get_agent_by_id($parent_id);

                $trans_type = Transactions::FROM_AGENT_TO_SUB_AGENT;
                $from_type = Transactions::AGENT;
                $from_name = $parent_details['agent_name'];
                $from_id = $parent_id;
                $note = lang('Sub agent '). $name . lang(' is created').'.';
            }
            $data = array(
                'transaction_type' => $trans_type,
                'amount' => $credit,
                'from_type' => $from_type,
                'from_id' => $from_id,
                'from_username' => $from_name,
                'to_type' => Transactions::AGENT,
                'to_id' => $agent_id,
                'to_username' => $name,
                'note' => $note,
                'before_balance' => 0,
                'after_balance' => $credit,
            );
            $transaction_id = $this->ci->transactions->add_new_transaction($data);
        }
        return $transaction_id;
    } // record_transaction_on_creation  }}}2
    // record_transaction_on_adjust {{{2
    /**
     *  record credit transation when credit of an agent is adusted
     *
     *  @param  string operation = 'add' (transfer from parent to sub-agent) or 'deduct' (vice versa)
     *  @param  array details of the agent on which credit is adjusted.
     *  @param  int amount for adjusted credit
     *  @return int transaction_id
     */
    public function record_transaction_on_adjust($op, $agent_details, $amount) {
        if ($agent_details['agent_level'] == 0) {
            // for level 0 agent which can only created by admin
            $parent_type = Transactions::ADMIN;
            $parent_id = $this->ci->authentication->getUserId();
            $parent_name = $this->ci->authentication->getUsername();
        } else {
            $parent_details = $this->ci->agency_model->get_agent_by_id($agent_details['parent_id']);

            $parent_type = Transactions::AGENT;
            $parent_id = $parent_details['agent_id'];
            $parent_name = $parent_details['agent_name'];
        }
        $sub_type = Transactions::AGENT;
        $sub_id = $agent_details['agent_id'];
        $sub_name = $agent_details['agent_name'];
        if ($op == 'add') {
            // from parent to sub agent
            $note = lang('Credit transfer');
            $from_type = $parent_type;
            $from_id = $parent_id;
            $from_name = $parent_name;
            $to_type = $sub_type;
            $to_id = $sub_id;
            $to_name = $sub_name;
            $before_balance = $agent_details['available_credit'];
            $after_balance = $agent_details['available_credit'] + $amount;
            if ($from_type == Transactions::ADMIN){
                $note .= ' '.lang('from admin to agent').'.';
                $trans_type = Transactions::FROM_ADMIN_TO_AGENT;
            } else {
                $note .= ' '.lang('from agent to sub agent').'.';
                $trans_type = Transactions::FROM_AGENT_TO_SUB_AGENT;
            }
        } else {
            $note = lang('Credit transfer');
            $from_type = $sub_type;
            $from_id = $sub_id;
            $from_name = $sub_name;
            $to_type = $parent_type;
            $to_id = $parent_id;
            $to_name = $parent_name;
            $before_balance = $agent_details['available_credit'];
            $after_balance = $agent_details['available_credit'] - $amount;
            if ($to_type == Transactions::ADMIN){
                $note .= ' '.lang('from agent to admin').'.';
                $trans_type = Transactions::FROM_AGENT_TO_ADMIN;
            } else {
                $note .= ' '.lang('from sub agent to agent').'.';
                $trans_type = Transactions::FROM_SUB_AGENT_TO_AGENT;
            }
        }
        $data = array(
            'transaction_type' => $trans_type,
            'amount' => $amount,
            'from_type' => $from_type,
            'from_id' => $from_id,
            'from_username' => $from_name,
            'to_type' => $to_type,
            'to_id' => $to_id,
            'to_username' => $to_name,
            'note' => $note,
            'before_balance' => $before_balance,
            'after_balance' => $after_balance,
        );
        $transaction_id = $this->ci->transactions->add_new_transaction($data);
        return array($transaction_id, $trans_type);
    } // record_transaction_on_adjust  }}}2
    // record_transaction_on_player_deposit {{{2
    /**
     *  record credit transation when deposit 'credit' for a player
     *
     *  @param  int player_id
     *  @param  int amount for player_deposited credit
     *  @return int transaction_id
     */
    public function record_transaction_on_player_deposit($player_id, $player_name, $before_balance, $agent_id, $agent_name, $amount) {
        // $total_balance = $this->ci->wallet_model->getTotalBalance($player_id);
        // $data = array(
        //     'transaction_type' => Transactions::FROM_AGENT_TO_PLAYER,
        //     'amount' => $amount,
        //     'from_type' => Transactions::AGENT,
        //     'from_id' => $agent_id,
        //     'from_username' => $agent_name,
        //     'to_type' => Transactions::PLAYER,
        //     'to_id' => $player_id,
        //     'to_username' => $player_name,
        //     'note' => lang('Credit transfer').' '.lang('from agent to player'),
        //     'before_balance' => $before_balance,
        //     'after_balance' => $before_balance + $amount,
        //     'sub_wallet_id' => Wallet_model::MAIN_WALLET_ID,
        //     'total_before_balance' => $total_balance - $amount,
        // );
        // $transaction_id = $this->ci->transactions->add_new_transaction($data);
        // return $transaction_id;
    } // record_transaction_on_player_deposit  }}}2
    // record_transaction_on_player_withdraw {{{2
    /**
     *  record credit transation when withdraw 'credit' for a player
     *
     *  @param  int player_id
     *  @param  int amount for player_withdrawed credit
     *  @return int transaction_id
     */
    public function record_transaction_on_player_withdraw($player_id, $player_name, $before_balance, $agent_id, $agent_name, $amount) {
        // $total_balance = $this->ci->wallet_model->getTotalBalance($player_id);
        // $data = array(
        //     'transaction_type' => Transactions::FROM_PLAYER_TO_AGENT,
        //     'amount' => $amount,
        //     'from_type' => Transactions::PLAYER,
        //     'from_id' => $player_id,
        //     'from_username' => $player_name,
        //     'to_type' => Transactions::AGENT,
        //     'to_id' => $agent_id,
        //     'to_username' => $agent_name,
        //     'note' => lang('Credit transfer').' '.lang('from player to agent'),
        //     'before_balance' => $before_balance,
        //     'after_balance' => $before_balance - $amount,
        //     'sub_wallet_id' => Wallet_model::MAIN_WALLET_ID,
        //     'total_before_balance' => $total_balance + $amount,
        // );
        // $transaction_id = $this->ci->transactions->add_new_transaction($data);
        // return $transaction_id;
    } // record_transaction_on_player_withdraw  }}}2
    // record credit transaction }}}1

    // adjust credit for a agent {{{1
    // check_adjust_credit {{{2
    /**
     * Adjust credit for a given agent
     *
     * @param string 'add' or 'sub'
     * @param array agent_details
     * @param array parent_details
     * @param int adjust amount
     * @return  bool wheter can do credit adjustment
     * @return  string error message for false
     */
    public function check_adjust_credit($op, $agent_details, $parent_details, $adjust_amount) {
        $success = true;
        $message = '';
        $old_amount = $agent_details['available_credit'];
        $limit = $agent_details['credit_limit'];
        if ($op == 'add') {
            $new_credit = $old_amount + $adjust_amount;
            if ($new_credit > $limit) {
                $success = false;
                $message = lang('Available credit cannot exceed credit limit');
            }
            if ($agent_details['parent_id'] > 0) {
                if ($adjust_amount > $parent_details['available_credit']) {
                    $success = false;
                    $message = lang('No enough credit in parent agent');
                }
            }
        } else {
            if ($adjust_amount > $old_amount) {
                $success = false;
                $message = lang('Available credit cannot be nagitive');
            }
            if ($agent_details['parent_id'] > 0) {
                if ($adjust_amount + $parent_details['available_credit'] > $parent_details['credit_limit']) {
                    $success = false;
                    $message = lang('Exceed parent credit limit');
                }
            }
        }

        return array($success, $message);
    } // check_adjust_credit }}}2
    // do_adjust_credit {{{2
    /**
     * Adjust credit for a given agent. Only be invoked after checking is successful.
     *
     * @param string 'add' or 'sub'
     * @param array agent_details
     * @param array parent_details
     * @param int adjust amount
     * @return  void
     */
    public function do_adjust_credit($op, $agent_details, $parent_details, $adjust_amount) {
        $old_amount = $agent_details['available_credit'];
        $limit = $agent_details['credit_limit'];
        $parent_id = $agent_details['parent_id'];
        if ($op == 'add') {
            $new_credit = $old_amount + $adjust_amount;
            if ($parent_id > 0) {
                $parent_new_credit = $parent_details['available_credit'] - $adjust_amount;
            }
        } else {
            $new_credit = $old_amount - $adjust_amount;
            if ($parent_id > 0) {
                $parent_new_credit = $parent_details['available_credit'] + $adjust_amount;
            }
        }

        $data = array(
            'available_credit' => $new_credit,
        );

        $this->ci->agency_model->update_agent($agent_details['agent_id'], $data);

        // adjust credit for parent agent accordingly
        // if ($agent_details['agent_level'] > 0) {
        if ($parent_id > 0) {
            $data = array(
                'available_credit' => $parent_new_credit,
            );
            $this->ci->agency_model->update_agent($parent_id, $data);
        }
    } // do_adjust_credit }}}2
    // record_balance_history_on_adjust {{{2
    /**
     *  record in table 'balance_history' on credit adjustment
     *
     *  @param
     *  @return
     */
    public function record_balance_history_on_adjust($trans_type, $trans_id, $agent_details, $amount) {
        $recordType = Wallet_model::RECORD_TYPE_AFTER;
        $actionType = $trans_type;
        $userType = Wallet_model::USER_TYPE_AGENT;
        if (empty($trans_id)) {
            $trans_id = 0;
        }
        $agent_id = $agent_details['agent_id'];

        $data = array(
            'record_type' => $recordType,
            'action_type' => $actionType,
            'user_type' => $userType,
            'agent_id' => $agent_id,
            'transaction_id' => $trans_id,
            'created_at' => $this->ci->utils->getNowForMysql(),
            'updated_at' => $this->ci->utils->getNowForMysql(),
            'amount' => $amount,
            'total_balance' => $agent_details['available_credit'],
        );

        return $this->ci->wallet_model->insertData('balance_history', $data);
    } // record_balance_history_on_adjust  }}}2
    // adjust credit for a agent }}}1

    // settlement {{{1
    // get_player_game_info {{{2
    /**
     *  get all relative game info for given player
     *
     *  @param  array all downline player ids
     *  @param  array period [date_from, date_to]
     *  @return array all relative game data
     */
    public function get_player_game_info($player_id, $period) {
        $date_from = $period['date_from'];
        $date_to = $period['date_to'];

        $this->ci->load->model(array('game_logs'));

        $game_info = array();
        $bets = $this->ci->game_logs->get_player_bet_info($player_id, $date_from, $date_to);
        $this->ci->utils->debug_log('game info', $bets);

        if(!empty($bets)) {
            foreach ($bets as $rec) {
                foreach ($rec as $key => $val) {
                    if(empty($val)) {
                        $game_info[$key] = 0;
                    } else {
                        $game_info[$key] = $val;
                    }
                }
            }
        }

        return $game_info;
    } // get_player_game_info  }}}2
    // get_agent_game_info {{{2
    /**
     *  get all relative game info for given agent
     *
     *  @param  array all downline player ids
     *  @param  array period [date_from, date_to]
     *  @return array all relative game data
     */
    private function get_agent_game_info($agent_id, $player_ids, $game_platform_id, $game_type_id, $date_from, $date_to) {
        $this->ci->load->model(array('game_logs'));
        $bets = $this->ci->game_logs->get_agent_bet_info($agent_id, $player_ids, $game_platform_id, $game_type_id, $date_from, $date_to);
        return $bets;
    } // get_agent_game_info  }}}2
    // get_agent_player_info {{{2
    /**
     *  get all relative game info for given agent
     *
     *  @param  array all downline player ids
     *  @param  array period [date_from, date_to]
     *  @return array all relative game data
     */
    private $player_fee_data = array();
    private function get_agent_player_info($agent_details, $player_ids, $date_from, $date_to) {
        $data_key = join('|', $player_ids).".[$date_from].[$date_to]";
        if(array_key_exists($data_key, $this->player_fee_data)){
            return $this->player_fee_data[$data_key];
        }

        $this->ci->load->model(array('transactions','player_model', 'agency_model'));

        $player_info = array('bonuses' => 0, 'rebates' => 0, 'transactions' => 0);

        $top_parent_agent = $this->ci->agency_model->get_top_parent_agent($agent_details);

        if($this->utils->isEnabledFeature('variable_agent_fee_rate')) {
            # Record both current and top level agent admin fee rates
            $player_info['admin_fee'] = $agent_details['admin_fee'];
            $player_info['top_admin_fee'] = $top_parent_agent['admin_fee'];
            $this->utils->debug_log("Using variable agent fee rates, getting fee rate from current agent [$agent_details[agent_name] ($agent_details[agent_id])]: ",
                $agent_details['admin_fee'],
                $agent_details['bonus_fee'],
                $agent_details['cashback_fee'],
                $agent_details['transaction_fee'],
                $agent_details['deposit_fee'],
                $agent_details['withdraw_fee']
            );
        } else {
            $agent_details['bonus_fee'] = $top_parent_agent['bonus_fee'];
            $agent_details['cashback_fee'] = $top_parent_agent['cashback_fee'];
            $agent_details['transaction_fee'] = $top_parent_agent['transaction_fee'];
            $agent_details['deposit_fee'] = $top_parent_agent['deposit_fee'];
            $agent_details['withdraw_fee'] = $top_parent_agent['withdraw_fee'];
            # Record admin fee rate
            $player_info['admin_fee'] = $top_parent_agent['admin_fee'];
            $this->utils->debug_log("Using fixed agent fee rates, getting fee rate from top level agent [$top_parent_agent[agent_name] ($top_parent_agent[agent_id])]: ",
                $agent_details['admin_fee'],
                $agent_details['bonus_fee'],
                $agent_details['cashback_fee'],
                $agent_details['transaction_fee'],
                $agent_details['deposit_fee'],
                $agent_details['withdraw_fee']
            );
        }

        # BONUS FEE
        if (array_key_exists('bonus_fee', $agent_details)) {
            $bonuses = $this->ci->player_model->getPlayersTotalBonus($player_ids, $date_from, $date_to);
            if($this->utils->isEnabledFeature('variable_agent_fee_rate')) {
                $player_info['bonuses'] = $bonuses;
            } else {
                $player_info['bonuses'] = ($agent_details['bonus_fee'] / 100) * $bonuses;
            }
        }

        # CASHBACK FEE
        if (array_key_exists('cashback_fee', $agent_details)) {
            $rebates = $this->ci->player_model->getPlayersTotalCashback($player_ids, $date_from, $date_to, Transactions::TOTAL_CASHBACK_PLUS_1_DAY);
            if($this->utils->isEnabledFeature('variable_agent_fee_rate')) {
                $player_info['rebates'] = $rebates;
            } else {
                $player_info['rebates'] = ($agent_details['cashback_fee'] / 100) * $rebates;
            }
        }

        # TRANSACTION FEE
        if (array_key_exists('transaction_fee', $agent_details)) {
            $transactions = $this->ci->transactions->sumTransactionFee($player_ids, $date_from, $date_to);
            if($this->utils->isEnabledFeature('variable_agent_fee_rate')) {
                $player_info['transactions'] = $transactions;
            } else {
                $player_info['transactions'] = ($agent_details['transaction_fee'] / 100) * $transactions;
            }
        }

        # Calculate deposit amount
        if(array_key_exists('deposit_fee', $agent_details) || array_key_exists('deposit_comm', $agent_details)) {
            $deposit_sum = $this->ci->transactions->sumTransactionsDepositOrWithdrawal(
                $player_ids, $date_from, $date_to, Transactions::DEPOSIT);
        }

        # DEPOSIT FEE
        if (array_key_exists('deposit_fee', $agent_details)) {
            if($this->utils->isEnabledFeature('variable_agent_fee_rate')) {
                $player_info['deposit_fee'] = $deposit_sum;
            } else {
                $player_info['deposit_fee'] = ($agent_details['deposit_fee'] / 100) * $deposit_sum;
            }
        }

        # WITHDRAW FEE
        if (array_key_exists('withdraw_fee', $agent_details)) {
            $withdraw_sum = $this->ci->transactions->sumTransactionsDepositOrWithdrawal(
                $player_ids, $date_from, $date_to, Transactions::WITHDRAWAL);
            if($this->utils->isEnabledFeature('variable_agent_fee_rate')) {
                $player_info['withdraw_fee'] = $withdraw_sum;
            } else {
                $player_info['withdraw_fee'] = ($agent_details['withdraw_fee'] / 100) * $withdraw_sum;
            }
        }

        # DEPOSIT Commission (always follow variable_agent_fee_rate's pattern)
        if (array_key_exists('deposit_comm', $agent_details)) {
            $player_info['deposit_comm'] = $deposit_sum;
        }

        # ADMIN FEE will be calculated in the main calculation

        # has_data flag marks whether this player has any of these fees calculated as non-zero
        $total_value = 0;
        $all_fees = ['bonuses', 'rebates', 'transactions', 'deposit_fee', 'withdraw_fee'];
        foreach($all_fees as $k) {
            if(array_key_exists($k, $player_info)){
                $total_value += $player_info[$k];
            }
        }
        $player_info['has_data'] = $total_value > 0;
        $this->player_fee_data[$data_key] = $player_info;
        $this->utils->debug_log("Player fee data: ", $player_info);
        return $player_info;

    } // get_agent_player_info  }}}2
    // create_data_for_settlement {{{2
    /**
     *  create an array for a new settlement record
     *
     *  @param
     *  @return array
     */
    private function create_data_for_settlement($agent_details, $game_type, $game_info, $player_info, $date_from, $date_to, $calc_sub = true) {

        $game_platform_id = $game_type['game_platform_id'];
        $game_type_id = $game_type['game_type_id'];

        $data = array(
            'agent_id' => $agent_details['agent_id'],
            'game_platform_id' => $game_platform_id,
            'game_type_id' => $game_type_id,
            'bets' => $this->utils->roundCurrencyForShow($game_info['total_bets']),
            'wins' => $this->utils->roundCurrencyForShow($game_info['gain_loss_sum']),
            'bonuses' => $this->utils->roundCurrencyForShow($player_info['bonuses'] * $game_info['bet_percentage']),
            'rebates' => $this->utils->roundCurrencyForShow($player_info['rebates'] * $game_info['bet_percentage']),
            'transactions' => $this->utils->roundCurrencyForShow($player_info['transactions'] * $game_info['bet_percentage']),
            'admin' => 0,
            'lost_bets' => $this->utils->roundCurrencyForShow($game_info['lost_bets']),
            'bets_except_tie' => $this->utils->roundCurrencyForShow($game_info['total_bets'] - $game_info['tie_bets']),
        );

        # ADMIN FEE
        if ($agent_details['admin_fee']) {
            $data['admin'] = ($agent_details['admin_fee'] / 100) * $data['wins'];
        }

        $winning_bets = isset($game_info['gain_sum']) ? $game_info['gain_sum'] : 0;

        $data['net_gaming'] = $this->utils->roundCurrencyForShow( 0 - $data['wins'] - $data['admin'] - $data['bonuses'] - $data['rebates'] - $data['transactions']);
        $data['rev_share_amt'] = $this->utils->roundCurrencyForShow( $data['net_gaming'] * ($game_type['rev_share'] / 100.00) );

        switch ($game_type['rolling_comm_basis']) {
            case 'winning_bets':
                $basis = $winning_bets;
                break;
            case 'total_bets':
                $basis = $data['bets'];
                break;
            case 'total_lost_bets':
                $basis = $data['lost_bets'];
                break;
            case 'total_bets_except_tie_bets':
                $basis = $data['bets_except_tie'];
                break;
        }

        $data['payable_amt'] = $this->utils->roundCurrencyForShow($data['net_gaming'] - $data['rev_share_amt']);

        $this->ci->utils->debug_log($game_type['rolling_comm_basis'].': '.$basis.', rolling rate:'.$game_type['rolling_comm'].', rev share:'.$game_type['rev_share'],
            $basis.' x '.$game_type['rolling_comm'].'% x '.(100-$game_type['rev_share']).'%');

        $data['roll_comm_income'] = $this->utils->roundCurrencyForShow($basis * ($game_type['rolling_comm']/100) * ((100 - $game_type['rev_share'])/100.0));

        if ($calc_sub) {
            $data['roll_comm_amt'] = $this->utils->roundCurrencyForShow($this->sum_sub_agent_roll_comm_amount($agent_details['agent_id'], $game_platform_id, $game_type_id, $date_from, $date_to));
        }

        return $data;
    } // create_data_for_settlement  }}}2

    public function sum_sub_agent_roll_comm_amount($parent_agent_id, $game_platform_id, $game_type_id, $date_from, $date_to) {

        $roll_comm_amt = 0;

        $sub_agents = $this->ci->agency_model->get_all_sub_agents($parent_agent_id);

        if ( ! empty($sub_agents)) {

            foreach ($sub_agents as $agent_details) {

                $game_type = $this->ci->agency_model->get_agent_game_types($agent_details['agent_id'], $game_platform_id, $game_type_id);

                if ($game_type) {

                    $game_platform_id = $game_type['game_platform_id'];
                    $game_type_id = $game_type['game_type_id'];

                    $this->ci->utils->debug_log('sum_sub_agent_roll_comm_amount agent id: ' . $agent_details['agent_id']);

                    $all_player_ids = $this->get_all_player_ids_under_agent($agent_details['agent_id'], self::ALL_DOWNLINES);
                    $this->ci->utils->debug_log('sum_sub_agent_roll_comm_amount all_player_ids: '.count($all_player_ids), $agent_details['agent_id']);

                    if ( ! empty($all_player_ids)) {

                        $player_info = $this->get_agent_player_info($agent_details, $all_player_ids, $date_from, $date_to);
                        $game_info = $this->get_agent_game_info($agent_details['agent_id'], $all_player_ids, $game_platform_id, $game_type_id, $date_from, $date_to);

                        $calc_sub = false;

                        $data = $this->create_data_for_settlement($agent_details, $game_type, $game_info, $player_info, $date_from, $date_to, $calc_sub);

                        $roll_comm_amt += $data['roll_comm_income'];

                    } else {
                        $this->ci->utils->debug_log('sum_sub_agent_roll_comm_amount empty player id, agent: ' . $agent_details['agent_id']);
                    }

                }

            }

        } else {

            $this->ci->utils->debug_log('sum_sub_agent_roll_comm_amount empty sub agent, parent id: '.$parent_agent_id);

        }

        $this->ci->utils->debug_log('sum_sub_agent_roll_comm_amount roll_comm_amt: '.$roll_comm_amt);

        return $roll_comm_amt;
    }

    // create_settlement_period_array {{{2
    /**
     *  create all possible settlement datetime range according to settlement_period and created time
     *  Note: This follows the setting of cashback period
     *
     *  @param  array agent_details
     *  @return array all possible datetime range
     */
    private function create_settlement_period_array($agent_details, $calculate_from_date = null) {
        $this->ci->load->model('operatorglobalsettings');
        $cashback_settings = $this->ci->operatorglobalsettings->getSettingJson('cashback_settings');
        $cashback_from_hour = $cashback_settings['fromHour'];

        #$bgn_time = $this->ci->config->item('agency_settlement_time');
        $bgn_time = $cashback_from_hour.":00:00";

        $interval = array(
            'Daily' => '+1 day',
            'Weekly' => '+1 week',
            'Monthly' => 'first day of next month',
            'Quarterly' => 'first day of +3 month',
            'Manual' => 'first day of next year',
        );
        $start_day = array(
            'Sunday' => 0,
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
        );

        $ranges = array();

        $period_name=$agent_details['settlement_period'];
        $created_on = $agent_details['created_on'];
        $created_time = date("H:i:s", strtotime($created_on));
        $bgn_day = date("Y-m-d", strtotime($created_on));

        # adjust bgn_date according to the limit
        if(isset($calculate_from_date) && strtotime($bgn_day) < strtotime($calculate_from_date)) {
            $bgn_day = date("Y-m-d", strtotime($calculate_from_date));
        } elseif($created_time < $bgn_time) {
            # This adjustment should not occur when $calculate_from_date is provided
            $bgn_day = date("Y-m-d", strtotime("$bgn_day -1 day"));
        }
        $now = date("Y-m-d H:i:s");
        if(array_key_exists($period_name, $interval)) {
            $inter = $interval[$period_name];
        } else {
            $inter = $interval['Daily']; # Unrecognized settlement_period in db, use daily
        }

        $bgn = $bgn_day . ' ' . $bgn_time;
        if ($period_name == 'Weekly') {
            $weekday = date("w", strtotime($bgn));
            $settle_day = $start_day[$agent_details['settlement_start_day']];
            if ($settle_day <= $weekday) {
                $settle_day += 7;
            }
            $next_day = $settle_day - $weekday;

            $end = date("Y-m-d H:i:s", strtotime("$bgn +$next_day day"));
        } else {
            $bgnDateTime = new DateTime($bgn);
            $end = $bgnDateTime->modify($inter)->format("Y-m-d H:i:s");
        }

        $end_day = date("Y-m-d", strtotime($end));
        $end = $end_day . ' ' . $bgn_time;

        $ranges[$period_name] = array();

        $break_it = false;
        for ($i = 0; true; $i++) {
            $date_from = $bgn;
            if ($created_on > $date_from) {
                $date_from = $created_on;
            }
            $date_to = $end;
            if ($date_to > $now) {
                $break_it = true;
                $date_to = $now;
            }

            # When writing to return value, adjust date_to to 1 second earlier so intervals don't overlap
            $ranges[$period_name][] = array(
                'date_from' => $date_from,
                'date_to' => date("Y-m-d H:i:s", strtotime($date_to) - 1),
            );
            if ($date_to >= $now || $break_it) {
                break;
            }
            $end_day = date("Y-m-d", strtotime("$end"));
            $bgn = $end_day . ' ' . $bgn_time;
            $endDateTime = new DateTime($end);
            $end = $endDateTime->modify($inter)->format("Y-m-d H:i:s");
        }
        #$this->utils->debug_log("create_settlement_period_array for [$period_name], starting from [$created_on], calculate from [$calculate_from_date]: ", $ranges);
        return $ranges;
    }

    const ALL_DOWNLINES = true;
    // create_settlement {{{2
    /**
     *  create all possible settlement records for a given agent
     *
     *  @param  int agent_id
     *  @return
     */
    public function create_settlement($agent_id, $calc_sub = true) {

        $today = date('Y-m-d H:i:s');

        $agent_details = $this->ci->agency_model->get_agent_by_id($agent_id);

        if (empty($agent_details['settlement_period'])) {
            $agent_details['settlement_period'] = 'Weekly';
        }

        if (empty($agent_details['settlement_start_day'])) {
            $agent_details['settlement_start_day'] = 'Monday';
        }

        $all_player_ids = $this->get_all_player_ids_under_agent($agent_id, self::ALL_DOWNLINES);
        $this->ci->utils->debug_log('ALL_PLAYER_IDS', $all_player_ids);

        if (empty($all_player_ids)) {
            $this->ci->utils->debug_log('AGENT_ID: ' . $agent_id, 'SKIPPING: EMPTY PLAYERS');
            return;
        }

        $agent_game_types = $this->ci->agency_model->get_agent_game_types($agent_id);
        $this->ci->utils->debug_log('GAME TYPES', $agent_game_types);

        if (empty($agent_game_types)) {
            $this->ci->utils->debug_log('AGENT_ID: ' . $agent_id, 'SKIPPING: EMPTY GAME TYPES');
            return;
        }

        $agent_game_info = $this->get_agent_game_info($agent_id, $all_player_ids, NULL, NULL, NULL, NULL);
        if ($agent_game_info['total_bets'] == 0) {
            $this->ci->utils->debug_log('AGENT_ID: ' . $agent_id, 'SKIPPING: NO BETS');
            return;
        }

        $period_array = $this->create_settlement_period_array($agent_details);
        # $this->ci->utils->debug_log('PERIOD_ARRAY', $period_array);

        foreach ($period_array as $period_name => $periods) {

            foreach($periods as $period) {

                $date_from = $period['date_from'];
                $date_to = $period['date_to'];

                # BONUS AND CASHBACK SYSTEM FEATURE
                $player_info = $this->get_agent_player_info($agent_details, $all_player_ids, $date_from, $date_to);
                $period_game_info = $this->get_agent_game_info($agent_id, $all_player_ids, NULL, NULL, $date_from, $date_to);

                if ($period_game_info['total_bets'] == 0) {
                    $this->ci->utils->debug_log('AGENT_ID: ' . $agent_id, 'PERIOD: ' . "{$date_from} - {$date_to}",'SKIPPING: NO BETS');
                    continue;
                }

                foreach ($agent_game_types as $game_type) {

                    $game_platform_id = $game_type['game_platform_id'];
                    $game_type_id = $game_type['game_type_id'];

                    $rec_exist = false;

                    if ($today >= $date_from && $today <= $date_to){
                        $status = 'current';
                    } else {
                        $status = 'unsettled';
                        $record = $this->ci->agency_model->get_settlement_by_agent($agent_id, $game_platform_id, $game_type_id, $period_name, $date_from);
                        if (count($record) > 0) {
                            if ($record[0]['status'] == 'current') {
                                $rec_exist = true;
                                $rec_id = $record[0]['settlement_id'];
                            } else {
                                continue;
                            }
                        }
                    }

                    $game_info = $this->get_agent_game_info($agent_id, $all_player_ids, $game_platform_id, $game_type_id, $date_from, $date_to);

                    $data = $this->create_data_for_settlement($agent_details, $game_type, $game_info, $player_info, $date_from, $date_to, $calc_sub);

                    $data['status'] = $status;
                    $data['settlement_period'] = $period_name;
                    $data['settlement_date_from'] = $date_from;
                    $data['settlement_date_to'] = $date_to;
                    $data['updated_on'] = $today;

                    $this->ci->utils->debug_log('SETTLEMENT', $data);

                    if ($status == 'unsettled') {
                        $data['balance'] = $data['payable_amt'];
                        if ($rec_exist) {
                            $this->ci->agency_model->update_settlement($rec_id, $data);
                        } else {
                            $data['created_on'] = $today;
                            $this->ci->utils->debug_log('SETTLEMENT_DATA111', $data);
                            $this->ci->agency_model->insert_settlement($data);
                        }
                    } else if ($status == 'current') {
                        $record = $this->ci->agency_model->get_settlement_by_agent($agent_id, $game_platform_id, $game_type_id, $period_name, $date_from);
                        if (count($record) > 0) {
                            $bal = (double) $record[0]['balance'];
                            if ($bal < 0.01 && $bal > -0.01) $bal = 0;
                            $data['balance'] = $bal;
                            $this->ci->utils->debug_log('SETTLEMENT_DATA222', $data);
                            if($record[0]['player_rolling_comm_payment_status'] == 'paid'){
                                //remove rolling
                                unset($data['roll_comm_income']);
                                unset($data['rolling_comm']);
                            }
                            $this->ci->agency_model->update_settlement($record[0]['settlement_id'], $data);
                        } else {
                            $unsettled_balance = $this->ci->agency_model->get_unsettled_balance($agent_id, $game_platform_id, $game_type_id, $period_name);
                            $data['balance'] = $unsettled_balance;
                            $data['created_on'] = $today;
                            $this->ci->utils->debug_log('SETTLEMENT_DATA333', $data);
                            $this->ci->agency_model->insert_settlement($data);
                        }
                    }

                }

            }
        }

    } // create_settlement  }}}2

    public function create_current_settlement($agent_id, $calc_sub=true) {
        $current_settlement_id=null;

        $agent_details = $this->ci->agency_model->get_agent_by_id($agent_id);
        if(empty($agent_details['settlement_period'])) {
            $agent_details['settlement_period'] = 'Weekly';
        }
        if(empty($agent_details['settlement_start_day'])) {
            $agent_details['settlement_start_day'] = 'Monday';
        }

        $all_downlines = true;
        $all_player_ids = $this->get_all_player_ids_under_agent($agent_id, $all_downlines);
        $this->ci->utils->debug_log('ALL_PLAYER_IDS', $all_player_ids);
        if (empty($all_player_ids)) {
            return;
        }

        $today = date("Y-m-d H:i:s");
        $period_array = $this->create_settlement_period_array($agent_details);
        //only keep last one
        foreach($period_array as $period_name => &$periods) {
            $len=count($periods);
            if($len>1){
                $periods=[$periods[$len-2], $periods[$len-1]];
            }elseif($len>0){
                $periods=[$periods[$len-1]];
            }else{
                $periods=[];
            }
        }

        $this->ci->utils->debug_log('PERIOD_ARRAY', $period_array);
        foreach($period_array as $period_name => $periods) {

            // $period_name is 'Daily', 'Weekly', and so on
            // $periods is an array containing all possible settlement period
            foreach($periods as $period) {
                $rec_exist = false;
                $data = array();

                $date_from = $period['date_from'];
                $date_to = $period['date_to'];

                if ($today >= $date_from && $today <= $date_to){
                    $status = 'current';
                } else {
                    $status = 'unsettled';
                    // if record exists then continue
                    $record = $this->ci->agency_model->get_settlement_by_agent($agent_id, $game_platform_id, $game_type_id, $period_name, $date_from);
                    if (count($record) > 0) {
                        if ($record[0]['status'] == 'current') {
                            /*
                            $data['status'] = $status;
                            $data['balance'] = $record[0]['payable_amt'];
                            $data['settlement_date_to'] = $date_to;
                            $data['updated_on'] = $today;
                            $this->ci->agency_model->update_settlement($record[0]['settlement_id'], $data);
                             */
                            $rec_exist = true;
                            $rec_id = $record[0]['settlement_id'];
                        } else {
                            continue;
                        }
                    }
                }
                $game_info = $this->get_agent_game_info($all_player_ids, $date_from, $date_to);
                $player_info  = $this->get_agent_player_info($agent_details, $all_player_ids, $date_from, $date_to);

                $data = $this->create_data_for_settlement($agent_details, $game_info, $player_info, $date_from, $date_to, $calc_sub);
                $data['status'] = $status;
                $data['settlement_period'] = $period_name;
                $data['settlement_date_from'] = $date_from;
                $data['settlement_date_to'] = $date_to;
                $data['updated_on'] = $today;

                if ($status == 'unsettled') {

                    $unsettled_balance = $this->ci->agency_model->get_unsettled_balance($agent_id, $period_name, $date_from);

                    $data['balance'] = $unsettled_balance + $data['payable_amt'] - $data['roll_comm_income'];

                    if ($rec_exist) {
                        $this->ci->agency_model->update_settlement($rec_id, $data);
                    } else {
                        $data['created_on'] = $today;
                        $this->ci->utils->debug_log('SETTLEMENT_DATA111', $data);
                        $this->ci->agency_model->insert_settlement($data);
                    }

                } else if ($status == 'current') {

                    $record = $this->ci->agency_model->get_settlement_by_agent($agent_id, $game_platform_id, $game_type_id, $period_name, $date_from);

                    if (count($record) > 0) {

                        $record = $record[0];

                        $unsettled_balance = $this->ci->agency_model->get_unsettled_balance($agent_id, $period_name, $date_from);

                        $data['balance'] = $unsettled_balance;

                        $this->ci->utils->debug_log('SETTLEMENT_DATA222', $data);

                        if ($record['player_rolling_comm_payment_status'] == 'paid') {
                            //remove rolling
                            unset($data['roll_comm_income']);
                            unset($data['rolling_comm']);
                        }

                        $current_settlement_id = $record['settlement_id'];

                        $this->ci->agency_model->update_settlement($current_settlement_id, $data);

                    } else {

                        $unsettled_balance  = $this->ci->agency_model->get_unsettled_balance($agent_id, $period_name, $date_from);

                        $data['balance']            = $unsettled_balance;
                        $data['created_on']         = $today;

                        $this->ci->utils->debug_log('SETTLEMENT_DATA333', $data);

                        $current_settlement_id = $this->ci->agency_model->insert_settlement($data);

                    }
                }
            }
        }

        return $current_settlement_id;
    }

    // update_settlement {{{2
    /**
     *  update all unsettled settlement records for a given agent
     *
     *  @param  int agent_id
     *  @return
     */
    public function update_settlement($agent_id) {

        $agent_details = $this->ci->agency_model->get_agent_by_id($agent_id);

        if(empty($agent_details['settlement_period'])) {
            $agent_details['settlement_period'] = 'Monthly';
        }

        if(empty($agent_details['settlement_start_day'])) {
            $agent_details['settlement_start_day'] = 'Monday';
        }

        $all_downlines = true;
        $all_player_ids = $this->get_all_player_ids_under_agent($agent_id, $all_downlines);
        if (empty($all_player_ids)) {
            return;
        }

        $today = date("Y-m-d H:i:s");
        $period_array = $this->create_settlement_period_array($agent_details);
        foreach($period_array as $period_name => $periods) {
            foreach($periods as $period) {

                $date_from = $period['date_from'];
                $date_to = $period['date_to'];

                $records = $this->ci->agency_model->get_settlement_by_agent($agent_id, $game_platform_id, $game_type_id, $period_name, $date_from);

                if ( ! empty($records)) {
                    foreach ($records as $record) {
                        if ($record['status'] == 'unsettled') {

                            $game_info = $this->get_agent_game_info($all_player_ids, $date_from, $date_to);
                            $player_info  = $this->get_agent_player_info($agent_details, $all_player_ids, $date_from, $date_to);

                            $data = $this->create_data_for_settlement($agent_details, $game_info, $player_info, $date_from, $date_to);
                            $data['status'] = $status;
                            $data['settlement_period'] = $period_name;
                            $data['settlement_date_from'] = $date_from;
                            $data['settlement_date_to'] = $date_to;
                            $data['updated_on'] = $today;
                            $data['balance'] = $data['payable_amt'];
                            $this->ci->agency_model->update_settlement($record['settlement_id'], $data);
                        }
                    }
                }

            }
        }
    } // update_settlement  }}}2
    // create_invoice_name {{{2
    /**
     *  create invoice name in select list for a given settlement
     *
     *  @param
     *  @return
     */
    public function create_invoice_name($settlement_rec) {
        //$this->ci->utils->debug_log('create_invoice_name', $settlement_rec);
        $agent_id = $settlement_rec['agent_id'];
        $period = $settlement_rec['settlement_period'];
        $date_from= $settlement_rec['settlement_date_from'];
        $date_to= $settlement_rec['settlement_date_to'];

        $agent_details = $this->ci->agency_model->get_agent_by_id($agent_id);
        $agent_name = $agent_details['agent_name'];

        $invoice_name = $agent_name . '_' . $period . '_';
        $invoice_name .= date("Y-m-d", strtotime("$date_from"));
        $invoice_name .= '_to_';
        $invoice_name .= date("Y-m-d", strtotime("$date_to"));

        return $invoice_name;
    } // create_invoice_name  }}}2
    // create_invoice_val_json {{{2
    /**
     *  create json string corresponding to invoice name in select list for a given settlement
     *
     *  @param
     *  @return
     */
    public function create_invoice_val_json($settlement_rec) {
        $agent_id = $settlement_rec['agent_id'];
        $period = $settlement_rec['settlement_period'];
        $date_from= $settlement_rec['settlement_date_from'];
        $date_to= $settlement_rec['settlement_date_to'];

        $agent_details = $this->ci->agency_model->get_agent_by_id($agent_id);
        $agent_name = $agent_details['agent_name'];

        $invoice_val = array(
            'agent_name' => $agent_name,
            'period' => $period,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'include_all_downlines' => 'true',
            'group_by' => 'player.playerId',
        );

        return json_encode($invoice_val);

    } // create_invoice_val_json  }}}2
    // get_invoice_info {{{2
    /**
     *  fetch settlement data
     *
     *  @param
     *  @return output in JSON
     */
    public function get_invoice_info($request) {
        $this->ci->load->model(array('agency_model', 'report_model'));
        $result = array();
        $is_export = true;
        $result[] = $this->ci->agency_model->get_settlement($request, $is_export);

        $viewPlayerInfoPerm = true;
        $result[] = $this->ci->report_model->get_agency_player_reports($request, $viewPlayerInfoPerm, $is_export);

        $player_id = null;
        $result[] = $this->ci->report_model->get_agency_game_reports($request, $player_id, $is_export);

        //$this->utils->debug_log($result);
        return $result;

        //$rlt = array('success' => true, 'data' => $result);
        //$this->returnJsonResult($rlt);
    } // get_invoice_info  }}}2
    // settlement }}}1
    //
    // agency logs {{{1
    // save_action_on_adjust_credit {{{2
    /**
     *  save action in agency_logs for agent credit adjustment
     *
     *  @param  string operator name: parent agent or admin
     *  @param  string operation
     *  @param  array agent details
     *  @param  array parent agent details
     *  @param  int adjust_amount
     *  @return
     */
    public function save_action_on_adjust_credit($action_url, $operator, $op, $agent_details, $parent_details, $adjust_amount) {
        if($op == 'add') {
            $action = 'credit_in';
            $details = 'Add credit for agent '. $agent_details['agent_name'];
        } else {
            $action = 'credit_out';
            $details = 'Remove credit for agent '. $agent_details['agent_name'];
        }
        $details .= '. amount = '. $adjust_amount . '. ';
        if (!empty($parent_details)) {
            $details .= 'parent agent is '. $parent_details['agent_name'];
        }
        $log_params = array(
            'action' => $action,
            'link_url' => $action_url,
            'done_by' => $operator,
            'done_to' => $agent_details['agent_name'],
            'details' => $details,
        );
        $this->save_action($log_params);
    } // save_action_on_adjust_credit  }}}2
    // save_action {{{2
    /**
     *  save action into table agency_logs
     *
     *  @param  string action name
     *  @return int log_id
     */
    public function save_action($params) {
        $done_by = $params['done_by'];
        $done_to = $params['done_to'];
        $details = $params['details'];
        $link_url = $params['link_url'];

        $action = $params['action'];
        $action_name = $this->log_actions[$action]['name'];
        $link_name = $this->log_actions[$action]['link_name'];

        $data = array(
            'done_at' => date("Y-m-d H:i:s"),
            'done_by' => $done_by,
            'done_to' => $done_to,
            'action' => $action_name,
            'details' => $details,
            'link_name' => $link_name,
            'link_url' => $link_url,
        );
        $query = $this->ci->agency_model->insert_log($data);
        $this->ci->utils->debug_log('the log save action --->', $query);
        return $query;
    } // save_action  }}}2
    // agency logs }}}1

    public function create_settlement_by_win_loss($agent_id, $calc_sub = true){
        $today = date("Y-m-d H:i:s");
        $current_settlement_id = null;

        $agent_details = $this->ci->agency_model->get_agent_by_id($agent_id);

        if (empty($agent_details['settlement_period'])) {
            $agent_details['settlement_period'] = 'Weekly';
        }

        if (empty($agent_details['settlement_start_day'])) {
            $agent_details['settlement_start_day'] = 'Monday';
        }

        $date_start_day = date("Y-m-d 00:00:00", strtotime("-1 {$agent_details['settlement_start_day']}"));
        $date_start_day_next = date("Y-m-d 00:00:00", strtotime("+1 {$agent_details['settlement_start_day']}"));

        if ($date_start_day_next == date("Y-m-d 00:00:00")) {
            $date_start_day = date("Y-m-d 00:00:00", strtotime("+1 {$agent_details['settlement_start_day']}"));
            $date_start_day_next = date("Y-m-d 00:00:00", strtotime("+2 {$agent_details['settlement_start_day']}"));
        }

        $all_player_ids = $this->get_all_player_ids_under_agent($agent_id, self::INCLUDE_ALL_DOWLINES);

        $this->ci->utils->debug_log("date_start_day [$date_start_day], date_start_day_next [$date_start_day_next]");
        if (empty($all_player_ids)) {
            $this->ci->utils->debug_log("No players under agent [$agent_id], skipping");
            return;
        }
        $this->ci->utils->debug_log("All player ids under agent [$agent_id]", join(',', $all_player_ids));

        // limit period array only to the max of daily settlement we are going to calculate
        $period_array = $this->create_settlement_period_array($agent_details, self::EARLIEST_WL_REPORT_CALCULATION_DAY);

        foreach ($period_array as $period_name => $periods) {
            // $period_name is 'Daily', 'Weekly', and so on
            // $periods is an array containing all possible settlement period
            foreach ($periods as $period) {

                $rec_exist = false;
                $data = array();

                $date_from  = $period['date_from'];
                $date_to    = $period['date_to'];

                if(strtotime($date_to) < strtotime(self::EARLIEST_WL_REPORT_CALCULATION_DAY)) {
                    continue;
                }

                if ($period_name == 'Weekly') {
                    if ($date_from >= $date_start_day && $date_to < $date_start_day_next) {
                        $status = 'current';
                    } else {
                        $status = 'unsettled';
                    }
                } else {
                    if ($today >= $date_from && $today <= $date_to) {
                        $status = 'current';
                    } else {
                        $status = 'unsettled';
                    }
                }

                // start handle agent settlement
                // get agent_rows only, note that $calculate_summary parameter is false
                list($agent_rows, $agent_summary) = $this->ci->agency_model->getAgentDailySettlement($agent_id, null, $date_from, $date_to, 'current', false);

                if($agent_rows){
                    foreach ($agent_rows as $agent){

                        $settlement_agent = $this->ci->agency_model->checkDuplicatedSettlement('agent', $agent['agent_id'], $date_from, $date_to);

                        $data = array(
                            'rev_share'         => 0, # FROM AGENT GAME TYPE SETTINGS
                            'rolling_comm'      => 0, # FROM AGENT GAME TYPE SETTINGS
                            'rolling_comm_basis'=> 0, # FROM AGENT GAME TYPE SETTINGS
                            'winning_bets'      => $agent['winning_bets'],
                            'real_bets'         => $agent['real_bets'],
                            'bets'              => $agent['bets'],
                            'tie_bets'          => $agent['tie_bets'],
                            'result_amount'     => $agent['result_amount'],
                            'platform_fee'      => $agent['platform_fee'],
                            'lost_bets'         => $agent['lost_bets'],
                            'bets_except_tie'   => $agent['bets_except_tie'],
                            'player_commission' => $agent['player_commission'],
                            'agent_commission'  => $agent['agent_commission'],
                            'wins'              => $agent['wins'],
                            'bonuses'           => $agent['bonuses'],
                            'rebates'           => $agent['rebates'],
                            'admin'             => $agent['admin'],
                            'net_gaming'        => $agent['net_gaming'],
                            'rev_share_amt'     => $agent['rev_share_amt'],
                            'earnings'          => $agent['earnings'],
                            'updated_on'        => $this->ci->utils->getNowForMysql(),
                            'agent_id'          => $agent['agent_id'],
                            'bonuses_total'     => $agent['bonuses_total'],
                            'rebates_total'     => $agent['rebates_total'],
                            'admin_total'       => $agent['admin_total'],
                            'bets_display'              => $agent['bets_display'],
                            'bets_except_tie_display'   => $agent['bets_except_tie_display'],
                            'deposit_comm'      => $agent['deposit_comm'],
                            'deposit_comm_total'      => $agent['deposit_comm_total'],
                        );

                        if($this->utils->isEnabledFeature('use_deposit_withdraw_fee')) {
                            $data['deposit_fee']       = $agent['deposit_fee'];
                            $data['deposit_fee_total'] = $agent['deposit_fee_total'];
                            $data['withdraw_fee']       = $agent['withdraw_fee'];
                            $data['withdraw_fee_total'] = $agent['withdraw_fee_total'];
                        } else {
                            $data['transactions']       = $agent['transactions'];
                            $data['transactions_total'] = $agent['transactions_total'];
                        }

                        if(empty($settlement_agent)){
                            $data['status']                 = $status;
                            $data['type']                   = 'agent';
                            $data['user_id']                = $agent['agent_id'];
                            $data['settlement_date_from']   = $date_from;
                            $data['settlement_date_to']     = $date_to;
                            $data['created_on']             = $this->ci->utils->getNowForMysql();

                            $this->ci->agency_model->insertWlSettlement($data);
                            $this->ci->utils->debug_log($agent['agent_id'], $period_name, 'create_settlement_by_win_loss->insert agent row id', $this->ci->db->insert_id());
                            continue;
                        }

                        if($settlement_agent->status == 'settled' || $settlement_agent->status == 'closed'){
                            continue;
                        }else{
                            $data['status']                 = $status;
                            $data['settlement_date_to']     = $date_to;

                            $this->ci->agency_model->updateWLSettlement($settlement_agent->id, $data);
                            $this->ci->utils->debug_log('create_settlement_by_win_loss->update agent row id', $settlement_agent->id);
                        }
                    }
                }
                //end handle agent settlement
            }
        }
    }

    private function get_max_rev_share_rate($agent_game_types) {
        $max_rev_share_rate = 0;
        foreach($agent_game_types as $agent_id => $agent_game_type){
            if($agent_game_type['rev_share'] > $max_rev_share_rate) {
                $max_rev_share_rate = $agent_game_type['rev_share'];
            }
        }
        return $max_rev_share_rate;
    }

    # Build up agent daily player settlement records based on their direct players' data
    public function generate_agency_daily_player_settlement($recent_record_only = true, $run_for_date = null) {
        $this->ci->utils->debug_log('agency_daily_player_settlement start');
        $this->ci->load->model(array('agency_model','game_logs'));

        $daily_agent_rolling_disburse = $this->utils->isEnabledFeature('daily_agent_rolling_disbursement');
        $deduct_agent_rolling = $this->utils->isEnabledFeature('deduct_agent_rolling_from_revenue_share');

        $active_agents = $this->ci->agency_model->get_active_agents();
        $player_count = 0;

        if (empty($active_agents)) {
            $active_agents = [];
        }

        $calculate_from_date = $recent_record_only ? self::RECENT_SETTLEMENT_CALCULATION_DAY : self::EARLIEST_SETTLEMENT_CALCULATION_DAY;
        foreach ($active_agents as $agent) {

            $agent_id = $agent['agent_id'];
            $top_parent_agent = $this->ci->agency_model->get_top_parent_agent($agent);
            $top_parent_agent_id = $top_parent_agent['agent_id'];

            $players = $this->ci->player->get_players_by_agent_ids($agent_id);
            if (empty($players)) {
                $this->ci->utils->debug_log('AGENT_ID: ' . $agent_id, 'SKIPPING: NO PLAYERS');
                continue;
            }
            $players_by_id = array_column($players, NULL, 'playerId');
            $players_id = array_column($players, 'playerId');

            $top_parent_agent_game_types = $this->ci->agency_model->get_agent_game_types($top_parent_agent_id);
            if (empty($top_parent_agent_game_types)) {
                $this->ci->utils->debug_log("Skipping agent [$agent_id] as it has empty top parent game types");
                continue;
            }
            $top_parent_agent_game_types = array_column($top_parent_agent_game_types, NULL, 'game_type_id');


            $agent_game_types = $this->ci->agency_model->get_agent_game_types($agent_id);
            if (!empty($agent_game_types)) {
                $agent_game_types = array_column($agent_game_types, NULL, 'game_type_id');
            }

            $a = $agent;
            $a['settlement_period'] = 'Daily'; # Use a clone to avoid changing of agent detail
            $period_array = $this->create_settlement_period_array($a, $calculate_from_date);

            foreach($period_array as $period_name => $periods) {

                foreach($periods as $period) {

                    $date_from  = $period['date_from'];
                    $date_to    = $period['date_to'];

                    if(isset($run_for_date) && date('Y-m-d', strtotime($date_from)) != $run_for_date) {
                        $this->utils->debug_log("Running for [$run_for_date] only, skipping date_from = [$date_from]");
                        continue;
                    }

                    $players_bet_info = $this->ci->game_logs->get_players_bet_info($players_id, $date_from, $date_to);
                    $fee_percentage_by_game_platform_and_type = $this->ci->game_logs->get_bet_percentage_by_platform_and_type($agent_id, $players_id, $date_from, $date_to);

                    # Loop thru all games ticked in top parent agent
                    # Note: At the last iteration, the player_ids that still don't have bet data (i.e. this player doesn't have bet data for all game types)
                    # but have fee data, will be attributing its fees to this last iteration
                    $iteration_count = 0;
                    $max_iteration = count($top_parent_agent_game_types);
                    $player_ids_with_record = array(); # the player ids that already has a settlement record over the iterations
                    # This is the max rev share configured across all games for this agent. This rate is used to calculate fees. (OGP-11814)
                    $max_rev_share_rate = $this->get_max_rev_share_rate($agent_game_types);
                    foreach ($top_parent_agent_game_types as $game_type_id => $top_parent_agent_game_type) {
                        $iteration_count++;

                        $game_type_bet_info = array_key_exists($game_type_id, $players_bet_info['game_types']) ?
                            $players_bet_info['game_types'][$game_type_id] : array();

                        # Top parent must have this game ticked
                        $platform_fee_rate = $top_parent_agent_game_type['platform_fee'];

                        if (isset($agent_game_types[$game_type_id])) {
                            # Agent ticked the game type in its rev share config, use that
                            $game_type = $agent_game_types[$game_type_id];

                            $game_platform_id = $game_type['game_platform_id'];
                            $rev_share_rate = $game_type['rev_share'];
                            $agent_rolling_rate = $game_type['rolling_comm'];
                            $rolling_comm_basis = $game_type['rolling_comm_basis'];
                            $bet_threshold = $game_type['bet_threshold'];

                            # rewrite some setting with tier comm pattern
                            if($this->utils->isEnabledFeature('agent_tier_comm_pattern')) {
                                # get from model again to obtain pattern info
                                $game_type_with_pattern = $this->ci->agency_model->get_agent_game_types($agent_id, null, $game_type_id);
                                $tier_pattern = $this->get_tier_for_agent($agent, $date_from, $game_type_with_pattern['tier_pattern']);

                                $rev_share_rate = $tier_pattern['rev_share'];
                                $agent_rolling_rate = $tier_pattern['rolling_comm'];
                                $rolling_comm_basis = 'total_bets_except_tie_bets'; # Hard-coded for tier comm
                            }
                        } else {
                            # Agent did not configure this game type, use all 0.
                            $game_platform_id = $top_parent_agent_game_type['game_platform_id'];
                            $rev_share_rate = 0;
                            $agent_rolling_rate = 0;
                            # rolling comm basis comes from top level
                            $rolling_comm_basis = $top_parent_agent_game_type['rolling_comm_basis'];
                            $bet_threshold = 0;
                        }

                        # Loop thru all players under this agent - note: if any player does not have game log record
                        # we will still count its deposit and withdraw for fees
                        foreach ($players_id as $player_id) {
                            $fee_percentage = 0;
                            if( array_key_exists($game_platform_id, $fee_percentage_by_game_platform_and_type) &&
                                array_key_exists($game_type_id, $fee_percentage_by_game_platform_and_type[$game_platform_id]) &&
                                array_key_exists($player_id, $fee_percentage_by_game_platform_and_type[$game_platform_id][$game_type_id])
                            ){
                                $fee_percentage = $fee_percentage_by_game_platform_and_type[$game_platform_id][$game_type_id][$player_id];
                            }

                            if(!empty($game_type_bet_info)){
                                $player_bet_info = array_key_exists($player_id, $game_type_bet_info['players']) ? $game_type_bet_info['players'][$player_id] : array();
                            } else {
                                $player_bet_info = array();
                            }
                            $player_fee_info = $this->get_agent_player_info($agent, [$player_id], $date_from, $date_to);

                            if(empty($player_bet_info)) {
                                # For the last iteration, if player has no bet data but has fee data, record them
                                if($iteration_count == $max_iteration) {
                                    # Only skip if has fee data but is previously calculated, OR
                                    # has no fee data AND no bet data
                                    if(in_array($player_id, $player_ids_with_record) || !$player_fee_info['has_data']) {
                                        #$this->utils->debug_log("For game type [$game_type_id], skipping player [$player_id] as there is neither bet nor fees data");
                                        continue;
                                    } else {
                                        $is_within_24hr = (time() - strtotime($date_to)) < 86400;
                                        if($is_within_24hr) {
                                            #$this->utils->debug_log("For game type [$game_type_id], player [$player_id], within 24 hours, do not process additional settlement record yet.");
                                            continue;
                                        }

                                        $this->utils->debug_log("For game type [$game_type_id], player [$player_id] gets an additional settlement record to store its fee data");
                                        $fee_percentage = 1;
                                    }
                                } else if (!$player_fee_info['has_data'] || empty($fee_percentage)) {
                                    # Only continue calculation if there's fee percentage and fee data
                                    #$this->utils->debug_log("For game type [$game_type_id], skipping player [$player_id] as there is no bet data");
                                    continue;
                                }
                            }

                            # Record down the player id that has at least once record
                            if(!in_array($player_id, $player_ids_with_record)) {
                                $player_ids_with_record[] = $player_id;
                            }

                            $player_count++;
                            $player_game_types = $this->ci->agency_model->get_player_game_types($player_id);
                            $player_game_types = array_column($player_game_types, NULL, 'game_type_id');

                            # Read player rolling comm for current game type
                            $player_rolling_rate = array_key_exists($game_type_id, $player_game_types) ? $player_game_types[$game_type_id]['rolling_comm'] : 0;

                            $total_real_bets       = isset($player_bet_info['total_real_bets']) ? $player_bet_info['total_real_bets'] : 0;
                            $total_bets            = isset($player_bet_info['total_bets']) ? $player_bet_info['total_bets'] : 0;
                            $winning_bets          = isset($player_bet_info['winning_bets']) ? $player_bet_info['winning_bets'] : 0;
                            $total_lost_bets       = isset($player_bet_info['lost_bets']) ? $player_bet_info['lost_bets'] : 0;
                            $total_tie_bets        = isset($player_bet_info['tie_bets']) ? $player_bet_info['tie_bets'] : 0;
                            $result_amount         = isset($player_bet_info['gain_loss_sum']) ? $player_bet_info['gain_loss_sum'] : 0;
                            $total_loss            = isset($player_bet_info['loss_sum']) ? $player_bet_info['loss_sum'] : 0;
                            $total_bets_except_tie = $total_bets - $total_tie_bets;
                            $total_bets_display    = isset($player_bet_info['total_bets_display']) ? $player_bet_info['total_bets_display'] : 0;
                            $total_tie_bets_display    = isset($player_bet_info['total_tie_bets_display']) ? $player_bet_info['total_tie_bets_display'] : 0;
                            $total_bets_except_tie_display = $total_bets_display - $total_tie_bets_display;

                            $total_live_real_bets       = isset($player_bet_info['total_live_real_bets']) ? $player_bet_info['total_live_real_bets'] : 0;
                            $total_live_bets            = isset($player_bet_info['total_live_bets']) ? $player_bet_info['total_live_bets'] : 0;
                            $live_winning_bets          = isset($player_bet_info['live_winning_bets']) ? $player_bet_info['live_winning_bets'] : 0;
                            $total_live_lost_bets       = isset($player_bet_info['live_lost_bets']) ? $player_bet_info['live_lost_bets'] : 0;
                            $total_live_tie_bets        = isset($player_bet_info['live_tie_bets']) ? $player_bet_info['live_tie_bets'] : 0;
                            $live_result_amount         = isset($player_bet_info['live_gain_loss_sum']) ? $player_bet_info['live_gain_loss_sum'] : 0;
                            $total_live_loss            = isset($player_bet_info['live_loss_sum']) ? $player_bet_info['live_loss_sum'] : 0;
                            $total_live_bets_except_tie = $total_live_bets - $total_live_tie_bets;

                            switch ($rolling_comm_basis) {

                                case 'winning_bets':
                                    $basis = $winning_bets;
                                    $live = $live_winning_bets;
                                    break;

                                case 'total_lost_bets':
                                    $basis = $total_lost_bets;
                                    $live = $total_live_lost_bets;
                                    break;

                                case 'total_lost_amount':
                                    $basis = $total_loss;
                                    $live = $total_live_loss;
                                    break;

                                case 'total_bets_except_tie_bets':
                                    $basis = $total_bets_except_tie;
                                    $live = $total_live_bets_except_tie;
                                    break;

                                case 'total_bets':
                                default:
                                    $basis = $total_bets;
                                    $live = $total_live_bets;
                                    break;
                            }

                            # If basis less than threshold, all rolling will be 0
                            if($basis <= $bet_threshold) {
                                $basis = 0;
                            }

                            // https://docs.google.com/spreadsheets/d/18nMvy5VU7eRnuPx-WIdP4LRtvLNOnn2KDocs--zXzDs/edit#gid=0
                            $player_rolling = ($player_rolling_rate / 100.0) * $basis;
                            $player_ggr = - $result_amount;

                            # OGP-2667 - LIVE MATCH = 50%
                            $agent_rolling_basis_amount = ($basis - $live / 2);
                            $agent_rolling = ($agent_rolling_rate / 100.0) * $agent_rolling_basis_amount;

                            $agent_net_rolling = $agent_rolling - $player_rolling;
                            $net_gaming = 0 - $result_amount;

                            # The fee amounts are the total fee incurred by this player
                            # If using fixed rates, the fees are already calculated using the fixed rate; else they are only the total amount
                            # See get_agent_player_info for more info
                            # Fees are divided based on each game platform's bet amount percentage
                            $bonus_fee = isset($player_fee_info['bonuses']) ? $player_fee_info['bonuses'] * $fee_percentage : 0;
                            $cashback_fee = isset($player_fee_info['rebates']) ? $player_fee_info['rebates'] * $fee_percentage : 0;
                            $trans_fee = isset($player_fee_info['transactions']) ? $player_fee_info['transactions'] * $fee_percentage : 0;
                            $deposit_fee = isset($player_fee_info['deposit_fee']) ? $player_fee_info['deposit_fee'] * $fee_percentage : 0;
                            $withdraw_fee = isset($player_fee_info['withdraw_fee']) ? $player_fee_info['withdraw_fee'] * $fee_percentage : 0;
                            $deposit_comm = isset($player_fee_info['deposit_comm']) ? $player_fee_info['deposit_comm'] * $fee_percentage : 0;
                            $agent['admin_fee'] = isset($player_fee_info['admin_fee']) ? $player_fee_info['admin_fee'] : $agent['admin_fee'];
                            $platform_fee = $platform_fee_rate > 0 ? (($platform_fee_rate / 100) * $player_ggr) : 0;

                            if(!$this->utils->isEnabledFeature('allow_negative_platform_fee')) {
                                $platform_fee = $platform_fee < 0 ? 0 : $platform_fee;
                            }

                            $this->utils->debug_log("GGR: [$player_ggr], Platform Fee: [$platform_fee] ($platform_fee_rate %)");

                            $agent_deposit_comm_rate = $agent['deposit_comm'];
                            $agent_deposit_comm = $deposit_comm * $agent_deposit_comm_rate / 100;

                            if($this->utils->isEnabledFeature('variable_agent_fee_rate')) {
                                # Calculation of variable fee rate (all fee rate defined per agent)
                                $agent_admin_fee_rate = $agent['admin_fee'];
                                $agent_bonus_fee_rate = $agent['bonus_fee'];
                                $agent_cashback_fee_rate = $agent['cashback_fee'];
                                $agent_transaction_fee_rate = $agent['transaction_fee'];
                                $agent_deposit_fee_rate = $agent['deposit_fee'];
                                $agent_withdraw_fee_rate = $agent['withdraw_fee'];
                                $total_admin_fee_rate = isset($player_fee_info['top_admin_fee']) ? $player_fee_info['top_admin_fee'] : $agent_admin_fee_rate;

                                $this->utils->debug_log("Variable agent fee rate, fee rates for agent [$agent_id] are: ".
                                    "[$agent_admin_fee_rate], [$agent_bonus_fee_rate], [$agent_cashback_fee_rate], [$agent_transaction_fee_rate], [$agent_deposit_fee_rate], [$agent_withdraw_fee_rate]");

                                # The fee amounts are the amount of incurred fee born by this agent.
                                # These data are calculated separately as they are for display on the WL report.
                                $admin_fee = ($player_ggr - $platform_fee) * $total_admin_fee_rate / 100;
                                $agent_admin_fee = ($player_ggr - $platform_fee) * $agent_admin_fee_rate / 100;
                                $agent_bonus_fee = $bonus_fee * $agent_bonus_fee_rate / 100;
                                $agent_cashback_fee = $cashback_fee * $agent_cashback_fee_rate / 100;
                                $agent_transaction_fee = $trans_fee * $agent_transaction_fee_rate / 100;
                                $agent_deposit_fee = $deposit_fee * $agent_deposit_fee_rate / 100;
                                $agent_withdraw_fee = $withdraw_fee * $agent_withdraw_fee_rate / 100;

                                $this->utils->debug_log("Variable agent fee rate, fees for agent [$agent_id] are: ".
                                    "[$agent_admin_fee], [$agent_bonus_fee], [$agent_cashback_fee], [$agent_transaction_fee], [$agent_deposit_fee], [$agent_withdraw_fee]");
                            } else {

                                $admin_fee = ($player_ggr - $platform_fee) * ($agent['admin_fee'] / 100);
                                # Calculation of fixed fee rate (all fee rate follow lv 0 agent's)
                                # The fee amounts are the amount of incurred fee born by this agent.
                                # These data are calculated separately as they are for display on the WL report.
                                $agent_admin_fee = $admin_fee * $max_rev_share_rate / 100;
                                $agent_bonus_fee = $bonus_fee * $max_rev_share_rate / 100;
                                $agent_cashback_fee = $cashback_fee * $max_rev_share_rate / 100;
                                $agent_transaction_fee = $trans_fee * $max_rev_share_rate / 100;
                                $agent_deposit_fee = $deposit_fee * $max_rev_share_rate / 100;
                                $agent_withdraw_fee = $withdraw_fee * $max_rev_share_rate / 100;
                                $this->utils->debug_log("Fixed agent fee rate with rev_share_rate = [$rev_share_rate], max_rev_share_rate = [$max_rev_share_rate], fees for agent [$agent_id] are: ".
                                    "[$agent_admin_fee], [$agent_bonus_fee], [$agent_cashback_fee], [$agent_transaction_fee], [$agent_deposit_fee], [$agent_withdraw_fee]");
                            }

                            # Earnings, i.e. agent net revenue, should be calculated as:
                            # (Total revenue - bonus fee - cashback fee - trans fee - admin fee) * rev share
                            #   OR
                            # Total revenue * rev share - bonus fee - cashback fee - trans fee - admin fee
                            # depending on whether variable agent fee rate is set
                            # Note: Daily agent commission disbursement will be deducted later
                            $rev_share_amt = ($player_ggr - $platform_fee) * ($rev_share_rate / 100.00)
                                        - $agent_admin_fee - $agent_bonus_fee - $agent_cashback_fee;

                            if($this->utils->isEnabledFeature('use_deposit_withdraw_fee')) {
                                $rev_share_amt = $rev_share_amt - $agent_deposit_fee - $agent_withdraw_fee;
                            } else {
                                $rev_share_amt -= $agent_transaction_fee;
                            }

                            # Agent's net income. This value will be modified when disbursing daily rolling to agent
                            if($daily_agent_rolling_disburse && $deduct_agent_rolling) {
                                $earnings = $rev_share_amt;
                            } else {
                                $earnings = $rev_share_amt + $agent_net_rolling;
                            }

                            $earnings += $agent_deposit_comm;

                            $data = array(
                                'player_id'            => $player_id,
                                'agent_id'             => $agent_id,
                                'game_platform_id'     => $game_platform_id,
                                'game_type_id'         => $game_type_id,
                                'settlement_date'      => $date_from,
                                'rev_share'            => $rev_share_rate,
                                'rolling_comm'         => $agent_rolling_rate,
                                'rolling_comm_basis'   => $rolling_comm_basis,
                                'rolling_basis_amount' => $agent_rolling_basis_amount,
                                'winning_bets'         => $winning_bets,
                                'real_bets'            => $total_real_bets,
                                'bets'                 => $total_bets,
                                'tie_bets'             => $total_tie_bets,
                                'result_amount'        => $result_amount,
                                # Platform fee is a special fee tied with player's ggr
                                'platform_fee'         => $platform_fee,
                                'lost_bets'            => $total_lost_bets,
                                'bets_except_tie'      => $total_bets_except_tie,
                                'player_commission'    => $player_rolling,
                                'roll_comm_income'     => $agent_rolling,
                                'agent_commission'     => $agent_net_rolling,
                                'wins'                 => $result_amount,
                                # Fees stored in these columns are the actual fees paid by this agent
                                'bonuses'              => $agent_bonus_fee,
                                'rebates'              => $agent_cashback_fee,
                                'transactions'         => $agent_transaction_fee,
                                'deposit_fee'          => $agent_deposit_fee,
                                'withdraw_fee'         => $agent_withdraw_fee,
                                'admin'                => $agent_admin_fee,
                                'deposit_comm'         => $agent_deposit_comm,
                                # Fees stored in these columns are total fees to be bear by all uplines
                                'bonuses_total'        => $bonus_fee,
                                'rebates_total'        => $cashback_fee,
                                'transactions_total'   => $trans_fee,
                                'deposit_fee_total'    => $deposit_fee,
                                'withdraw_fee_total'   => $withdraw_fee,
                                'admin_total'          => $admin_fee,
                                'deposit_comm_total'   => $deposit_comm,
                                'net_gaming'           => $net_gaming,
                                'rev_share_amt'        => $rev_share_amt,
                                'earnings'             => $earnings,
                                'updated_on'           => date('Y-m-d H:i:s'),
                                'bets_display'                 => $total_bets_display,
                                'bets_except_tie_display'      => $total_bets_except_tie_display,
                            );

                            $this->ci->db->where('player_id', $player_id);
                            $this->ci->db->where('agent_id', $agent_id);
                            $this->ci->db->where('game_platform_id', $game_platform_id);
                            $this->ci->db->where('game_type_id', $game_type_id);
                            $this->ci->db->where('settlement_date', $date_from);

                            $this->ci->db->select('agency_daily_player_settlement.*');
                            $query = $this->ci->db->get('agency_daily_player_settlement');
                            $rows = $query->result_array();

                            if(count($rows) <= 0) {
                                $data['created_on'] = date('Y-m-d H:i:s');
                                $this->ci->db->insert('agency_daily_player_settlement', $data);
                                $this->utils->debug_log("New player settlement row inserted: ", $this->ci->db->insert_id());
                            } elseif(count($rows) == 1) {
                                if($rows[0]['agent_rolling_paid'] == 1) {
                                    unset($data['agent_commission']); # Do not update agent_commission column as that's the amount of rolling already paid, it has to match the payment record
                                    # Earning data must be updated to deduct the paid rolling
                                    $data['earnings'] = $data['earnings'] - $rows[0]['agent_commission'];
                                }

                                if(!$this->is_settlement_data_updated($rows[0], $data)) {
                                    $this->utils->debug_log("Player settlement row (id: ".$rows[0]['id'].") not changed.");
                                    continue;
                                }

                                $this->ci->db->where('id', $rows[0]['id']);
                                $this->ci->db->update('agency_daily_player_settlement', $data);
                                $this->utils->debug_log("Player settlement row (id: ".$rows[0]['id'].") updated.");
                            } else {
                                $this->utils->error_log("Exception case: more than 1 qualifying rows for query condition: ", 'player_id', $player_id, 'agent_id', $agent_id, 'game_platform_id', $game_platform_id, 'game_type_id', $game_type_id, 'settlement_date', $date_from);
                            }
                        }
                    }
                    $this->disburse_agent_rolling($agent_id, $date_from, $date_to);
                }
            }
        }  # End of agents loop

        $this->ci->utils->debug_log('generate_agency_daily_player_settlement: done for ['.
            count($active_agents).'] agents, total ['.
            $player_count.'] players with data.');
    }

    # Calculate agent income generated by their down-line agents
    # insert rows to record revenue generated from players of down-line agents
    # (player_id = original player id, agent_id = current agent id)
    # returns whether a new row is inserted (if yes, this function needs to be run again to examine the new row)
    public function generate_agency_daily_agent_settlement($recent_record_only = true, $run_for_date = null) {

        $this->ci->load->model(array('agency_model','game_logs'));
        $this->ci->utils->debug_log('agency_daily_agent_settlement: start');

        $daily_agent_rolling_disburse = $this->utils->isEnabledFeature('daily_agent_rolling_disbursement');
        $deduct_agent_rolling = $this->utils->isEnabledFeature('deduct_agent_rolling_from_revenue_share');

        $active_agents = $this->ci->agency_model->get_active_agents();
        $agents_by_id = array_column($active_agents, NULL, 'agent_id');

        $has_new_row = false;

        if (empty($active_agents)) {
            $active_agents = [];
        }

        $calculate_from_date = $recent_record_only ? self::RECENT_SETTLEMENT_CALCULATION_DAY : self::EARLIEST_SETTLEMENT_CALCULATION_DAY;

        # For all rows in daily player settlement, calculate its contribution to its direct parent agent's income
        foreach($active_agents as $current_agent) {
            $current_agent['settlement_period'] = 'Daily';
            $agent_id = $current_agent['agent_id'];
            $parent_agent_id = $current_agent['parent_id'];

            # This step is not needed for top level agent
            if($parent_agent_id == 0) {
                continue;
            }

            # Parent agent not active, skip
            if(!array_key_exists($parent_agent_id, $agents_by_id)) {
                $this->utils->debug_log("Parent agent [$parent_agent_id] is not active, skip");
                continue;
            }

            $agent = $agents_by_id[$parent_agent_id];
            $agent_game_types = $this->ci->agency_model->get_agent_game_types($parent_agent_id);
            if (!empty($agent_game_types)) {
                $agent_game_types = array_column($agent_game_types, NULL, 'game_type_id');
            }
            # This is the max rev share configured across all games for this agent. This rate is used to calculate fees. (OGP-11814)
            $max_rev_share_rate = $this->get_max_rev_share_rate($agent_game_types);
            $period_array = $this->create_settlement_period_array($current_agent, $calculate_from_date);
            foreach ($period_array as $period_name => $periods) {
                foreach($periods as $period) {
                    $date_from = $period['date_from'];
                    $date_to = $period['date_to']; # note that date_to here is now() for the last period

                    if(isset($run_for_date) && date('Y-m-d', strtotime($date_from)) != $run_for_date) {
                        $this->utils->debug_log("Running for [$run_for_date] only, skipping date_from = [$date_from]");
                        continue;
                    }

                    $player_daily_settlement_rows = $this->ci->agency_model->getAllDailySettlements($agent_id, $date_from, $date_to);
                    $this->utils->debug_log("Processing all daily settlements for: [$agent_id][$current_agent[agent_name]], [$date_from], [$date_to]; count: ", count($player_daily_settlement_rows));

                    foreach($player_daily_settlement_rows as $settlement_row) {
                        $this->ci->utils->debug_log("agency_daily_agent_settlement: calculating for agent [$agent_id][$current_agent[agent_name]]'s parent [$agent[agent_name]] based on settlement_row [$settlement_row[id]]");
                        $agent_setting_by_game_type = $this->ci->agency_model->get_agent_game_types($parent_agent_id,
                            $settlement_row['game_platform_id'], $settlement_row['game_type_id']);

                        # rewrite some setting with tier comm pattern
                        if($this->utils->isEnabledFeature('agent_tier_comm_pattern')) {
                            # get from model again to obtain pattern info
                            $game_type_with_pattern = $this->ci->agency_model->get_agent_game_types($parent_agent_id,
                            $settlement_row['game_platform_id'], $settlement_row['game_type_id']);
                            # $agent is now $agents_by_id[$parent_agent_id], tier data should be obtained based on this.
                            $tier_pattern = $this->get_tier_for_agent($agent, $date_from, $game_type_with_pattern['tier_pattern']);

                            $agent_setting_by_game_type['rev_share'] = $tier_pattern['rev_share'];
                            $agent_setting_by_game_type['rolling_comm'] = $tier_pattern['rolling_comm'];
                        }

                        $data['player_id'] = $settlement_row['player_id'];
                        $data['agent_id'] = $parent_agent_id; # Calculate the contribution of current player to parent agent
                        $data['game_platform_id'] = $settlement_row['game_platform_id'];
                        $data['game_type_id'] = $settlement_row['game_type_id'];
                        $data['settlement_date'] = $settlement_row['settlement_date'];
                        $data['rev_share'] = array_key_exists('rev_share', $agent_setting_by_game_type) ? $agent_setting_by_game_type['rev_share'] : 0;
                        $data['rolling_comm'] = array_key_exists('rolling_comm', $agent_setting_by_game_type) ? $agent_setting_by_game_type['rolling_comm'] : 0;
                        $data['rolling_comm_basis'] = $settlement_row['rolling_comm_basis'];

                        # calculate basis
                        $settlement_row_agent_rolling = $settlement_row['roll_comm_income'];
                        $settlement_row_rolling_basis = $settlement_row['rolling_basis_amount'];
                        $agent_rolling_rate = $data['rolling_comm'];
                        $agent_rolling = $settlement_row_rolling_basis * $agent_rolling_rate / 100.0;
                        $agent_net_rolling = $agent_rolling - $settlement_row_agent_rolling;

                        # calculate rev share amount
                        $settlement_row_rev_share_rate = $settlement_row['rev_share'];
                        $rev_share_rate = $data['rev_share'];
                        $settlement_row_ggr = -$settlement_row['result_amount'];
                        $settlement_row_platform_fee = $settlement_row['platform_fee'];
                        $settlement_row_player_rolling = $settlement_row['player_commission'];

                        if($this->utils->isEnabledFeature('settlement_include_all_downline')) {
                            $this->utils->debug_log("Settlement calculation include all downline");
                            # rev share no longer substract downline
                            $rev_share_rate_diff = $rev_share_rate;
                            $agent_deposit_comm_rate = $agent['deposit_comm'];
                        } else {
                            $rev_share_rate_diff = $rev_share_rate - $settlement_row_rev_share_rate;
                            $agent_deposit_comm_rate = $agent['deposit_comm'] - $current_agent['deposit_comm'];
                        }

                        $agent_deposit_comm = $settlement_row['deposit_comm_total'] * $agent_deposit_comm_rate / 100;

                        if($this->utils->isEnabledFeature('variable_agent_fee_rate')) {
                            # Calculation of variable fee rate (all fee rate defined per agent)
                            if($this->utils->isEnabledFeature('settlement_include_all_downline')) {
                                $agent_admin_fee_rate = $agent['admin_fee'];
                                $agent_bonus_fee_rate = $agent['bonus_fee'];
                                $agent_cashback_fee_rate = $agent['cashback_fee'];
                                $agent_transaction_fee_rate = $agent['transaction_fee'];
                                $agent_deposit_fee_rate = $agent['deposit_fee'];
                                $agent_withdraw_fee_rate = $agent['withdraw_fee'];
                            } else {
                                $agent_admin_fee_rate = $agent['admin_fee'] - $current_agent['admin_fee'];
                                $agent_bonus_fee_rate = $agent['bonus_fee'] - $current_agent['bonus_fee'];
                                $agent_cashback_fee_rate = $agent['cashback_fee'] - $current_agent['cashback_fee'];
                                $agent_transaction_fee_rate = $agent['transaction_fee'] - $current_agent['transaction_fee'];
                                $agent_deposit_fee_rate = $agent['deposit_fee'] - $current_agent['deposit_fee'];
                                $agent_withdraw_fee_rate = $agent['withdraw_fee'] - $current_agent['withdraw_fee'];
                            }

                            $this->utils->debug_log("Variable agent fee rate, fee rates for agent [$agent_id][$current_agent[agent_name]] are: ".
                                            "[$agent_admin_fee_rate], [$agent_bonus_fee_rate], [$agent_cashback_fee_rate], [$agent_transaction_fee_rate], [$agent_deposit_fee_rate], [$agent_withdraw_fee_rate]");

                            # The fee amounts are the amount of incurred fee born by this agent.
                            # These data are calculated separately as they are for display on the WL report.
                            $admin_fee = ($settlement_row_ggr - $settlement_row_platform_fee) * $agent_admin_fee_rate / 100;
                            $agent_bonus_fee = $settlement_row['bonuses_total'] * $agent_bonus_fee_rate / 100;
                            $agent_cashback_fee = $settlement_row['rebates_total'] * $agent_cashback_fee_rate / 100;
                            $agent_transaction_fee = $settlement_row['transactions_total'] * $agent_transaction_fee_rate / 100;
                            $agent_deposit_fee = $settlement_row['deposit_fee_total'] * $agent_deposit_fee_rate / 100;
                            $agent_withdraw_fee = $settlement_row['withdraw_fee_total'] * $agent_withdraw_fee_rate / 100;
                        } else {
                            # Calculation of fixed fee rate (all fee rate follow lv 0 agent's)
                            # The fee amounts are the amount of incurred fee born by this agent.
                            # These data are calculated separately as they are for display on the WL report.

                            if($this->utils->isEnabledFeature('settlement_include_all_downline')) {
                                $agent_bonus_fee = $settlement_row['bonuses_total'] * $max_rev_share_rate / 100;
                                $agent_cashback_fee = $settlement_row['rebates_total'] * $max_rev_share_rate / 100;
                                $agent_transaction_fee = $settlement_row['transactions_total'] * $max_rev_share_rate / 100;
                                $agent_deposit_fee = $settlement_row['deposit_fee_total'] * $max_rev_share_rate / 100;
                                $agent_withdraw_fee = $settlement_row['withdraw_fee_total'] * $max_rev_share_rate / 100;
                                $admin_fee = $settlement_row['admin_total'] * $max_rev_share_rate / 100;
                                $this->utils->debug_log("Fixed agent fee rate, using max_rev_share_rate: [$max_rev_share_rate]%, rev_share_rate_diff: [$rev_share_rate_diff]%");
                            } else {
                                $agent_bonus_fee = $settlement_row['bonuses_total'] * $rev_share_rate_diff / 100;
                                $agent_cashback_fee = $settlement_row['rebates_total'] * $rev_share_rate_diff / 100;
                                $agent_transaction_fee = $settlement_row['transactions_total'] * $rev_share_rate_diff / 100;
                                $agent_deposit_fee = $settlement_row['deposit_fee_total'] * $rev_share_rate_diff / 100;
                                $agent_withdraw_fee = $settlement_row['withdraw_fee_total'] * $rev_share_rate_diff / 100;
                                $admin_fee = $settlement_row['admin_total'] * $rev_share_rate_diff / 100;
                                $this->utils->debug_log("Fixed agent fee rate, using rev_share_rate_diff: [$rev_share_rate_diff]%");
                            }

                            $total_admin_fee = $settlement_row['admin_total'];
                        }
                        # --- Rev Share Calculation ---
                        # Note that admin fee is calculated different than other fees
                        $rev_share_amt = ($settlement_row_ggr - $settlement_row_platform_fee) * $rev_share_rate_diff / 100
                            - $admin_fee - $agent_bonus_fee - $agent_cashback_fee;
                        if($this->utils->isEnabledFeature('use_deposit_withdraw_fee')) {
                            $rev_share_amt = $rev_share_amt - $agent_deposit_fee - $agent_withdraw_fee;
                        } else {
                            $rev_share_amt -= $agent_transaction_fee;
                        }
                        $this->utils->debug_log(sprintf("Rev share calculation: GGR[%.2f], Rolling[%.2f], Rev Share[%.2f], Admin Fee[%.2f], Bonus Fee[%.2f], Cashback Fee[%.2f], Trans Fee[%.2f], Deposit Fee[%.2f], Withdraw Fee[%.2f], Deposit Comm[%.2f]",
                                        $settlement_row_ggr,
                                        $settlement_row_player_rolling,
                                        $rev_share_rate_diff / 100,
                                        $admin_fee,
                                        $agent_bonus_fee,
                                        $agent_cashback_fee,
                                        $agent_transaction_fee,
                                        $agent_deposit_fee,
                                        $agent_withdraw_fee,
                                        $agent_deposit_comm));
                        # --- END Revenus Share Calculation ---

                        # Agent's net income. This value will be modified when disbursing daily rolling to agent
                        if($daily_agent_rolling_disburse && $deduct_agent_rolling) {
                            $earnings = $rev_share_amt;
                        } else {
                            $earnings = $rev_share_amt + $agent_net_rolling;
                        }

                        $earnings += $agent_deposit_comm;

                        if($this->utils->isEnabledFeature('settlement_include_all_downline')) {
                            # add in all downline players' bet amounts, if they are already calculated
                            if(!empty($settlement_row['bets_display']) || !empty($settlement_row['bets_except_tie_display'])) {
                                $data['bets_display'] =
                                    array_key_exists('bets_display', $settlement_row) ? $settlement_row['bets_display'] : 0;
                                $data['bets_except_tie_display'] =
                                    array_key_exists('bets_except_tie_display', $settlement_row) ? $settlement_row['bets_except_tie_display'] : 0;
                            } else { # clear data left over from last iteration
                                $data['bets_display'] = 0;
                                $data['bets_except_tie_display'] = 0;
                            }
                        }
                        $data['winning_bets'] = 0;
                        $data['real_bets'] = 0;
                        $data['bets'] = 0;
                        $data['tie_bets'] = 0;
                        $data['lost_bets'] = 0;
                        $data['bets_except_tie'] = 0;
                        $data['player_commission'] = 0;
                        $data['roll_comm_income'] = $agent_rolling;
                        $data['agent_commission'] = $agent_net_rolling;
                        $data['wins'] = 0;
                        $data['bonuses'] = $agent_bonus_fee;
                        $data['rebates'] = $agent_cashback_fee;
                        $data['transactions'] = $agent_transaction_fee;
                        $data['deposit_fee'] = $agent_deposit_fee;
                        $data['withdraw_fee'] = $agent_withdraw_fee;
                        $data['deposit_comm'] = $agent_deposit_comm;
                        $data['admin'] = $admin_fee;
                        $data['net_gaming'] = 0;
                        $data['rev_share_amt'] = $rev_share_amt;
                        $data['earnings'] = $earnings;
                        $data['updated_on'] = date('Y-m-d H:i:s');
                        # Inherit the total fees
                        $data['bonuses_total'] = $settlement_row['bonuses_total'];
                        $data['rebates_total'] = $settlement_row['rebates_total'];
                        $data['transactions_total'] = $settlement_row['transactions_total'];
                        $data['deposit_fee_total'] = $settlement_row['deposit_fee_total'];
                        $data['withdraw_fee_total'] = $settlement_row['withdraw_fee_total'];
                        $data['deposit_comm_total'] = $settlement_row['deposit_comm_total'];
                        $data['admin_total'] = $settlement_row['admin_total'];
                        # Inherit rolling basis amount
                        $data['rolling_basis_amount'] = $settlement_row['rolling_basis_amount'];
                        # Inherit player's W/L result
                        $data['result_amount'] = $settlement_row['result_amount'];
                        $data['platform_fee'] = $settlement_row['platform_fee'];
                        $data['player_commission'] = $settlement_row['player_commission'];

                        $this->ci->db->where('player_id', $data['player_id']);
                        $this->ci->db->where('agent_id', $data['agent_id']);
                        $this->ci->db->where('game_platform_id', $data['game_platform_id']);
                        $this->ci->db->where('game_type_id', $data['game_type_id']);
                        $this->ci->db->where('settlement_date', $data['settlement_date']);

                        $this->ci->db->select('agency_daily_player_settlement.*');
                        $query = $this->ci->db->get('agency_daily_player_settlement');
                        $rows = $query->result_array();

                        if(count($rows) <= 0) {
                            $data['created_on'] = date('Y-m-d H:i:s');
                            $this->ci->db->insert('agency_daily_player_settlement', $data);
                            $has_new_row = true;
                            $this->utils->debug_log("New agent settlement row inserted: ", $this->ci->db->insert_id());
                        } elseif(count($rows) == 1) {
                            if($rows[0]['agent_rolling_paid'] == 1) {
                                unset($data['agent_commission']); # Do not update agent_commission column as that's the amount of rolling already paid, it has to match the payment record
                                # Earning data must be updated to deduct the paid rolling
                                $data['earnings'] = $data['earnings'] - $rows[0]['agent_commission'];
                            }

                            if(!$this->is_settlement_data_updated($rows[0], $data)) {
                                $this->utils->debug_log("Agent settlement row (id: ".$rows[0]['id'].") not changed.");
                                continue;
                            }

                            $this->ci->db->where('id', $rows[0]['id']);
                            $this->ci->db->update('agency_daily_player_settlement', $data);
                            $this->utils->debug_log("Agent settlement row (id: ".$rows[0]['id'].") updated.");
                        } else {
                            $this->utils->error_log("Exception case: more than 1 qualifying rows for query condition: ", 'player_id', $player_id, 'agent_id', $agent_id, 'game_platform_id', $game_platform_id, 'game_type_id', $game_type_id, 'settlement_date', $date_from);
                        }
                        $this->disburse_agent_rolling($agent['agent_id'], $date_from, $date_to);
                    }
                }
            }
        }

        $this->ci->utils->debug_log('agency_daily_agent_settlement: done for ['.count($active_agents).'] agents, has new row? ['.$has_new_row.']');
        return $has_new_row;
    }

    private function is_settlement_data_updated($original_data, $new_data) {
        $diff = array_diff_assoc($original_data, $new_data);
        $diff2 = array_diff_assoc($new_data, $original_data);
        $diff = array_merge($diff, $diff2);
        $change_fields = ['rev_share','rolling_comm','rolling_comm_basis','rolling_basis_amount','winning_bets','real_bets','bets','tie_bets','result_amount','platform_fee','lost_bets','bets_except_tie','player_commission','roll_comm_income','wins','bonuses','rebates','transactions','deposit_fee','withdraw_fee','admin','bonuses_total','rebates_total','transactions_total','deposit_fee_total','withdraw_fee_total','admin_total','net_gaming','rev_share_amt','earnings','bets_display','bets_except_tie_display', 'deposit_comm', 'deposit_comm_total'];
        foreach($diff as $key => $val){
            if(in_array($key, $change_fields) && floatval($original_data[$key]) != floatval($new_data[$key])) {
                $this->utils->debug_log("Found diff at key [$key], was [".@$original_data[$key]."], now [".@$new_data[$key]."]");
                return true;
            }
        }
        return false;
    }

    # Used for agent rolling daily disbursement
    public function disburse_agent_rolling($agent_id, $date_from, $date_to) {
        if(!$this->utils->isEnabledFeature('daily_agent_rolling_disbursement')) {
            return;
        }

        $this->ci->load->model(array('transactions'));
        $agent_rolling = array();
        $agent_rolling_paid_ids = array();

        $player_daily_settlement_rows = $this->ci->agency_model->getAllDailySettlements($agent_id, $date_from, $date_to);
        $this->ci->load->model('operatorglobalsettings');
        $cashback_settings = $this->ci->operatorglobalsettings->getSettingJson('cashback_settings');
        $cashback_pay_hour = $cashback_settings['payTimeHour']; # XX:00:00

        foreach($player_daily_settlement_rows as $settlement_row) {
            # Decide whether we should disburse rolling commission for current agent

            # We do not want to disburse if it has been done before, unless we want to refresh data
            if($settlement_row['agent_rolling_paid']) {
                continue;
            }

            # Skip negative rolling amount or if the amount is too small
            if(round(floatval($settlement_row['agent_commission']), 2) <= 0) {
                $this->utils->debug_log("Agent settlement row [$settlement_row[id]] commission not valid for disburse", $settlement_row['agent_commission']);
                continue;
            }

            # This is daily settlement, so when date_from is more than 1 day ago, we can disburse
            # Note: date_to will always be current time for the last period, so we cannot decide using date_to
            if(strtotime($date_from) >= date('U') - 86400) {
                continue;
            }

            # The actual disburse time should be half an hour later than the cashback pay time
            $cashback_pay_day = date('Y-m-d', strtotime($date_to));
            $this->utils->debug_log("Testing actual disburse time [$cashback_pay_day $cashback_pay_hour]");
            if(strtotime("$cashback_pay_day $cashback_pay_hour") >= date('U') - 1800) {
                continue;
            }

            $agent_id = $settlement_row['agent_id'];
            # prepare data for agent rolling disbursement
            if(!array_key_exists($agent_id, $agent_rolling)) {
                $agent_rolling[$agent_id] = 0;
                $agent_rolling_paid_ids[$agent_id] = array();
            }

            # Combine the rolling amount to pay as a whole
            # Note: The index for agent_id is not needed for now as there will be only one agent_id
            $agent_rolling[$agent_id] += $settlement_row['agent_commission'];
            $agent_rolling_paid_ids[$agent_id][] = $settlement_row['id'];
        }

        if(empty($agent_rolling)) {
            return;
        }

        # disburse agent daily rolling based on prior calculation
        $this->utils->debug_log("Disburse agent rolling calculation for agent [$agent_id]: ", $agent_rolling);
        $controller = $this->ci;
        foreach($agent_rolling as $rolling_agent_id => $rolling_amount){
            $success = $this->ci->lockAndTransForAgencyCredit($rolling_agent_id, function() use ($controller, $rolling_agent_id, $rolling_amount) {
                if($rolling_amount != 0) {
                    return $controller->transactions->depositToAgent(
                        $rolling_agent_id, $rolling_amount,
                        lang("Disburse agent daily rolling"), # $reason
                        1, # $adminUserId
                        Transactions::PROGRAM);
                } else {
                    return true;
                }
            });
            if($success) {
                $this->utils->debug_log("Updating the following player daily settlement rows as paid: ", $agent_rolling_paid_ids[$rolling_agent_id]);
                # update disbursed rows' agent_rolling_paid flag
                $this->ci->db->where_in('id', $agent_rolling_paid_ids[$rolling_agent_id]);
                $this->ci->db->where('agent_rolling_paid != ', 1);
                $this->ci->db->set('agent_rolling_paid', 1);
                $this->ci->db->set('earnings', 'earnings - agent_commission', false);
                $this->ci->db->update('agency_daily_player_settlement');
            }
            $this->utils->debug_log("Daily agent rolling disbursement, paying [$rolling_amount] to [$rolling_agent_id], success = [$success]");
        }
    }

    public function delete_phantom_settlements($recent_record_only = true) {
        $this->utils->debug_log("Deleting phantom settlement records... recent record? [$recent_record_only]");
        $date_from = $recent_record_only ? self::RECENT_SETTLEMENT_CALCULATION_DAY : self::EARLIEST_SETTLEMENT_CALCULATION_DAY;
        $delete_sql = <<<EOF
DELETE FROM agency_daily_player_settlement
WHERE settlement_date >= ? AND bets_display > 0 AND NOT EXISTS(
    SELECT id FROM game_logs WHERE agency_daily_player_settlement.player_id = game_logs.player_id AND game_logs.game_type_id = agency_daily_player_settlement.game_type_id AND end_at BETWEEN agency_daily_player_settlement.settlement_date AND DATE_ADD(agency_daily_player_settlement.settlement_date, INTERVAL 1 DAY) AND flag = 1 LIMIT 1
)
EOF;
        $start_time = new DateTime();
        $start_time->setTimestamp(strtotime($date_from));
        $this->ci->db->query($delete_sql, array(
            $this->utils->formatDateForMysql($start_time)
        ));
        #$this->utils->debug_log("Last query", $this->ci->db->last_query());
        $this->utils->debug_log("Number of phantom settlement records deleted:", $this->ci->db->affected_rows());
    }

    # Determines which tier applies to this agent at the given settlement date
    private function get_tier_for_agent($agent, $settlement_date, $tier_patterns) {
        $this->ci->load->model('agency_model');
        $bets_to_date = $this->ci->agency_model->get_agent_bets_to_date($agent, $settlement_date);
        # Note: $tier_patterns has already been sorted asc by upper_bound
        foreach($tier_patterns as $tier_pattern) {
            if($tier_pattern['upper_bound'] < $bets_to_date){
                continue;
            }
            # Return the first tier pattern with upper_bound higher than bets_to_date
            $this->utils->debug_log("get_tier_for_agent: For agent [$agent[agent_id]] calculated bets at [$settlement_date] is: ", $bets_to_date, "chosen tier with upper_bound: ", $tier_pattern['upper_bound'], "rev_share", $tier_pattern['rev_share']);
            return $tier_pattern;
        }
        $tier_pattern = end($tier_patterns);
        $this->utils->debug_log("get_tier_for_agent: For agent [$agent[agent_id]] calculated bets at [$settlement_date] is: ", $bets_to_date, "chosen tier with largest upper_bound: ", $tier_pattern['upper_bound'], "rev_share", $tier_pattern['rev_share']);
        return $tier_pattern;
    }
}
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agency_library.php
