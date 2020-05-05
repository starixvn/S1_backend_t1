<?php
/**
 *   filename:   agency_model.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-06
 *   @brief:     model for agency sub system
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * This model represents agency data.
 */
class agency_model extends BaseModel {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = 'agency_agents';

    protected $table_structure = 'agency_structures';
    protected $table_agent = 'agency_agents';
    protected $table_payment = 'agent_payment';
    protected $table_payment_history = 'agent_payment_history';
    protected $table_setting = 'agency_settings';
    protected $table_transaction = 'agency_transactions';
    protected $table_log = 'agency_logs';
    protected $table_settlement = 'agency_settlement';
    protected $table_settlement_wl = 'agency_wl_settlement';
    protected $table_player_rolling_comm = 'agency_player_rolling_comm';
    protected $table_tier_comm_patterns = 'agency_tier_comm_patterns';
    protected $table_tier_comm_pattern_tiers = 'agency_tier_comm_pattern_tiers';

    const ROLLING_STATUS_SETTLED='settled';
    const ROLLING_STATUS_CURRENT='current';
    const ROLLING_STATUS_PENDING='pending';

    const TRACKING_TYPE_CODE = 1;
    const TRACKING_TYPE_DOMAIN = 2;
    const TRACKING_TYPE_SOURCE_CODE = 3;
    const AGENT_STATUS_ACTIVE='active';
    const AGENT_STATUS_FROZEN='frozen';
    const AGENT_STATUS_SUSPENDED='suspended';

    const SHOW_TO_AGENT_TYPE_HIDDEN=1;
    const SHOW_TO_AGENT_TYPE_ALL=2;
    const SHOW_TO_AGENT_TYPE_BATCH=3;
    const AGENT_ROLLING_MODE_TOTAL_BETS='total_bets';
    const AGENT_ROLLING_MODE_TOTAL_LOST_BETS='total_lost_bets';
    const AGENT_ROLLING_MODE_TOTAL_BETS_EXCEPT_TIE='total_bets_except_tie_bets';

    const DOMAIN_STATUS_DELETED = 3;

	const STATUS_WITHDRAW_REQUEST = 1;
	const STATUS_WITHDRAW_APPROVED = 2;
	const STATUS_WITHDRAW_DECLINED = 3;

    // structure {{{1
    // add_structure {{{2
    /**
     *  insert a new structure in agency_structures
     *
     *  @param  array for data
     *  @return structure_id
     */
    public function add_structure($data) {
        $structure_id = $this->insertData($this->table_structure, $data);
        return $structure_id;
    } // add_structure  }}}2
    // update_structure {{{2
    /**
     *  insert a new structure in agency_structures
     *
     *  @param  int structure_id
     *  @param  array for data
     *  @return structure_id
     */
    public function update_structure($structure_id, $data) {
        $this->db->where('structure_id', $structure_id);
        $this->db->update($this->table_structure, $data);
        return $this->db->affected_rows();
    } // update_structure  }}}2
    // get_structure_by_id {{{2
    /**
     *  get structure details by structure_id
     *
     *  @param  structure_id
     *  @return array for structure details
     */
    public function get_structure_by_id($structure_id) {
        $this->db->from($this->table_structure)->where('structure_id', $structure_id);
        return $this->runOneRowArray();
    } // get_structure_by_id  }}}2
    // get_structure_list {{{2
    /**
     *  get structures from table agency_structures
     *
     *  @param  search request
     *  @return array structure data
     */
    public function get_structure_list($request, $is_export = false) {
        $this->load->library(array('data_tables'));
        $i = 0;
        $input = $this->data_tables->extra_search($request);
        //$this->utils->debug_log('input:', $input);
        $table = $this->table_structure;
        $where = array();
        $values = array();

        if (isset($input['structure_name']) && $input['structure_name'] != '') {
            $where[] = "structure_name LIKE ?";
            $values[] = '%' .$input['structure_name']. '%';
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $where[] = "currency = ?";
            $values[] = $input['currency'];
        }
        if (isset($input['allowed_level']) && $input['allowed_level'] != '') {
            $where[] = "allowed_level = ?";
            $values[] = $input['allowed_level'];
        }
        //$this->utils->debug_log($where, $values);

        # DEFINE TABLE COLUMNS #####################################################################
        $columns = array(
            array(
                'dt' => $i++,
                'select' =>'structure_name',
                'name' => lang('Agent Template Name')
            ),
            array(
                'dt' => $i++,
                'select' => 'currency',
                'name' => lang('Currency')
            ),
            array(
                'dt' => $i++,
                'select' => 'credit_limit',
                'name' => lang('Credit Limit')
            ),
            array(
                'dt' => $i++,
                'select' => 'status',
                'name' => lang('Status')
            ),
        );

        // $columns[]=array(
        //     'dt' => $i++,
        //     'select' => 'rev_share',
        // );
        // $columns[]=array(
        //     'dt' => $i++,
        //     'select' => 'rolling_comm',
        // );
        // $columns[]=array(
        //     'dt' => $i++,
        //     'select' => 'rolling_comm_basis',
        // );

        $columns[]=array(
                'dt' => $i++,
                'select' => 'allowed_level',
                'name' => lang('Allowed Level')
        );
        $columns[]=array(
                'dt' => $i++,
                'select' => 'allowed_level_names',
                'name' => lang('Agent Level Names')
        );
        $columns[]=array(
            'dt' => $i++,
            'select' => 'vip_groups',
            'formatter' => function ($d) {
                if (empty($d)) {
                    return '';
                }
                $this->load->model('vipsetting');
                $groups = array();

                $ids = explode(',', $d);
                foreach($ids as $id) {
                    $group_details = $this->vipsetting->getVIPGroupDetails($id);
                    $groups[] = $group_details[0]['groupName'];
                }

                return implode(',', $groups);
            },
            'name' => lang('VIP Groups')
        );
        $columns[]=array(
            'dt' => $i++,
            'select' => 'settlement_period',
            'name' => lang('Settlement Period')
        );
        $columns[]=array(
            'dt' => $i++,
            'select' => 'structure_id',
            'formatter' => function ($d, $row) use ($is_export) {
                if ($is_export) {
                    return '';
                } else {
                    $structure_name = $row['structure_name'];
                    $title = lang('Delete this item');
                    $output = '';
                    $output .= '<a href="/agency_management/create_agent/' . $d . '" data-toggle="tooltip" title="' . lang('Create Agent') . '"><span class="glyphicon glyphicon-plus-sign"></span></a> ';
                    $output .= '<a href="/agency_management/edit_structure/' . $d . '" data-toggle="tooltip" title="' . lang('Edit Agent Template') . '" ><span class="glyphicon glyphicon-edit"></span></a> ';
                    $output .= "<a href='javascript:void(0)' data-toggle='tooltip' title='$title' onclick='remove_structure($d, \"{$structure_name}\")'><span class='fa fa-trash'></span></a> ";
                    return $output;
                }
            },
        );

        # OUTPUT ###################################################################################
        $result = $this->data_tables->get_data($request, $columns, $table, $where, $values);
        //$this->utils->debug_log($result);

        return $result;
    } // get_structure_list  }}}2
    // remove_structure {{{2
    /**
     *  remove a structure from table agency_structures
     *
     *  @param  int structure_id
     */
    public function remove_structure($structure_id) {
        if (is_array($structure_id)) {
            $this->db->where_in('structure_id', $structure_id);
        } else {
            $this->db->where('structure_id', $structure_id);
        }

        return $this->db->delete($this->table_structure);
    } // remove_structure  }}}2
	/** get_structure_id_by_structure_name {{{2
	 * overview : get structure id by username
	 *
	 * @param string $structure_name
	 * @return null
	 */
	public function get_structure_id_by_structure_name($structure_name) {
		$this->db->from('agency_structures')->where('structure_name', $structure_name);
		return $this->runOneRowOneField('structure_id');
	} // get_structure_id_by_structure_name }}}2
    // get_structure_id_and_names {{{2
    /**
     *  get all structure names from DB
     *
     *  @param
     *  @return array for structure names
     */
    public function get_structure_id_and_names() {
        $this->db->select("structure_id, structure_name");
        $result = $this->db->get($this->table_structure);

        return $this->getMultipleRowArray($result);
    } // get_structure_id_and_names  }}}2
    // structure }}}1

    // agent {{{1
    // add_agent {{{2
    /**
     *  insert a new agent in agency_agents
     *
     *  @param  array for data
     *  @return agent_id
     */
    public function add_agent($data) {
        $agent_details = $this->get_agent_by_name($data['agent_name']);
        if (!empty($agent_details)) {
            return null;
        }
        $agent_id = $this->insertData($this->table_agent, $data);
        return $agent_id;
    } // add_agent  }}}2
    // get_agent_by_id {{{2
    /**
     *  get agent details by agent_id
     *
     *  @param  agent_id
     *  @return array for agent details
     */
    public function get_agent_by_id($agent_id) {
        $this->db->from($this->table_agent)->where('agent_id', $agent_id);
        return $this->runOneRowArray();
    } // get_agent_by_id  }}}2

    public function get_agent_by_binding_player_id($binding_player_id){
        $this->db->from($this->table_agent)->where('binding_player_id', $binding_player_id);
        return $this->runOneRowArray();
    }

    // get_active_agents {{{2
    /**
     *  get all active agents
     *
     *  @param  bool only master
     *  @param  bool ordered by name
     *  @return array for agents
     */
    public function get_active_agents($only_master = false, $ordered_by_name = false) {
        $this->db->from($this->table_agent)->where('status', 'active');
        if ($only_master) {
            $this->db->where('ifnull(parent_id,0)=0', null, false);
        }
        if ($ordered_by_name) {
            $this->db->order_by('agent_name');
        } else {
            # Usually calculation starts with last level of agents
            $this->db->order_by('agent_level', 'desc');
        }
        return $this->runMultipleRowArray();
    } // get_active_agents  }}}2
    // get_password_by_id {{{2
    /**
     *  get agent details by agent_id
     *
     *  @param  agent_id
     *  @return array for agent details
     */
    public function get_password_by_id($agent_id) {
        $this->db->select('password, agent_name')->from($this->table_agent);
        $this->db->where('agent_id', $agent_id);
        $query = $this->db->get();
        return $query->row_array();
    } // get_password_by_id  }}}2
    // login {{{2
    /**
     *  check username and password for login
     *
     *  @param  string username
     *  @param  string password
     *  @return
     */
    public function login($username, $password) {
        $sql = "SELECT * FROM agency_agents where agent_name = ? and password = ?";
        $query = $this->db->query($sql, array($username, $password));

        return $query->row_array();
    } // login  }}}2
    // get_agent_by_name {{{2
    /**
     *  get agent details by agent_name
     *
     *  @param  agent_name
     *  @return array for agent details
     */
    public function get_agent_by_name($agent_name) {
        $this->db->from($this->table_agent)->where('agent_name', $agent_name);
        //$qry = $this->db->get();
        //return $qry->result_array();
        return $this->runOneRowArray();
    } // get_agent_by_name  }}}2
    // get_all_agents_by_name {{{2
    /**
     *  get agent details by agent_name
     *
     *  @param  agent_name
     *  @return array for agent details
     */
    public function get_all_agents_by_name($agent_name) {
        $this->db->from($this->table_agent)->where('agent_name', $agent_name);
        $qry = $this->db->get();
        return $qry->result_array();
    } // get_all_agents_by_name  }}}2
    // get_all_sub_agents {{{2
    /**
     *  get all sub agents of the given agent
     *
     *  @param  int agent_id
     *  @param  Date date from
     *  @param  Date date to
     *  @return array
     */
    public function get_all_sub_agents($agent_id = null, $date_from = null, $date_to = null, $not_frozen = null) {        $where = '';
        if ($agent_id) {
            $where .= 'WHERE parent_id = '.$agent_id;
        }

        if (!empty($date_from)) {
            $where .= "AND created_on >= '" . $date_from . "'";
        }
        if (!empty($date_to)) {
            $where .= "AND created_on <= '" . $date_to . "'";
        }

        if (!empty($not_frozen)) {
            $this->db->where('status <>', self::AGENT_STATUS_FROZEN);
        }

        $qStr = <<<EOD
SELECT * FROM agency_agents
$where
EOD;
        $query = $this->db->query($qStr);

        return $query->result_array();
    } // get_all_sub_agents  }}}2
    // get_all_sub_agent_ids {{{2
    /**
     *  get all sub agent ids including all downlines
     *
     *  @param  int agent_id
     *  @return array for all downline sub agent ids (self included)
     */
    public function get_all_sub_agent_ids($agent_id) {
        $parent_ids = array($agent_id);
        $sub_ids = array();
        $all_ids = $parent_ids;
        while(!empty($sub_ids = $this->get_sub_agent_ids_by_parent_id($parent_ids))) {
            //$this->utils->debug_log('sub_ids', $sub_ids);
            $all_ids = array_merge($all_ids, $sub_ids);
            $parent_ids = $sub_ids;
            $sub_ids = array();
        }

        return $all_ids;
    } // get_all_sub_agent_ids  }}}2

    public function get_all_unfrozen_sub_agent_ids($master_agent_id) {
        $this->db->from($this->tableName)
            ->select('agent_id')
            ->where('parent_id', $master_agent_id)
            ->where('status <>', self::AGENT_STATUS_FROZEN)
        ;

        $ufsres = $this->db->get()->result_array();

        $sares = [];
        foreach ($ufsres as $ufsrow) {
            $sares[] = $ufsrow['agent_id'];
        }

        return $sares;
    }

    // get_agent_list {{{2
    /**
     *  get agents from table agency_agents
     *
     *  @param  search request
     *  @return array agent data
     */
    public function get_agent_list($request, $is_export = false) {

        $this->load->library(array('data_tables'));
        $input = $this->data_tables->extra_search($request);
        $table = $this->table_agent;
        $where = array();
        $values = array();
        $joins = array(
            'agency_agents pa' => 'pa.agent_id = agency_agents.parent_id',
        );

        if (isset($input['search_on_date']) && $input['search_on_date']) {
            if (isset($input['date_from'], $input['date_to'])) {
                $where[] = "agency_agents.created_on BETWEEN ? AND ?";
                $values[] = $input['date_from'];
                $values[] = $input['date_to'];
            }
        }
        $has_parent = false;
        if (isset($input['parent_id']) && $input['parent_id'] != '') {
            $has_parent = true;
            /*
            if (isset($input['include_all_downlines']) && $input['include_all_downlines'] == true) {
                $all_ids = $this->get_all_sub_agent_ids($input['parent_id']);
                foreach ($all_ids as $i => $id) {
                    if ($i == 0) {
                        $w = "(parent_id = ?";
                    } else {
                        $w .= " OR parent_id = ?";
                    }
                    $values[] = $id;
                }
                $w .= ")";
                $where[] = $w;
            } else {
             */
                $where[] = "agency_agents.parent_id = ?";
                $values[] = $input['parent_id'];
            //}
        }
        if (isset($input['agent_name']) && $input['agent_name'] != '') {
            $where[] = "agency_agents.agent_name LIKE ?";
            $values[] = '%'.$input['agent_name'].'%';
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $where[] = "agency_agents.currency = ?";
            $values[] = $input['currency'];
        }
        if (isset($input['agent_level']) && $input['agent_level'] != '') {
            $where[] = "agency_agents.agent_level = ?";
            $values[] = $input['agent_level'];
        }
        //$this->utils->debug_log('agent list. where: ', $where, $values);

        # DEFINE TABLE COLUMNS #####################################################################
        $i = 0;
        $columns = array(
            array(
                'dt' => $is_export ? null : $i++,
                'alias' => 'batch_message_action',
                'select' => 'agency_agents.agent_id',
                'formatter' => function ($d, $row) use ($is_export) {

                    if ($is_export || $this->utils->isEnabledFeature('agency_hide_sub_agent_list_action')) {
                        return '';
                    }

                    //return '<input type="checkbox" class="batch-message-cb agent-oper" title="' . lang('Change Status') . '" value="' . $row['agent_id'].'" />';
                    $id = $row['agent_id'];
                    $output = '<input type="checkbox" class="batch-message-cb agent-oper check_all_agents"';
                    $output .= ' id="agent_'.$id.'" name="agents[]"';
                    $output .= ' onclick="uncheckAll(this.id)"';
                    $output .= ' title="' . lang('Change Status') . '" value="' . $id . '" />';
                    return $output;
                },
                'name' => '',
            ),
            array(
                'dt' => $i++,
                'alias' => 'agent_name',
                'select' =>'agency_agents.agent_name',
                'formatter' => function ($d, $row) use ($is_export) {
                    if ($is_export) {
                        return $d;
                    }
                    $ret = "<i class='fa fa-user' ></i> ";
                    $agent_id = $row['agent_id'];
                    $title = lang('Show Agent Info');
                    $ret .= "<a href='".site_url('/agency_management/agent_information/'.$agent_id)."' class='agent-username agent-oper' data-toggle='tooltip' title='$title' >$d</a> ";
                    return $ret;
                },
                'name' => lang('Agent Username'),
            ),
            array(
                'dt' => $i++,
                'alias' => 'created_on',
                'select' => 'agency_agents.created_on',
                'formatter' => function ($d) {
                    return (!$d || strtotime($d) < 0) ? '<i>' . lang('lang.norecyet') . '</i>' : date('Y-m-d H:i:s', strtotime($d));
                },
                'name' => lang('Created On'),
            ),
            array(
                'dt' => $i++,
                'alias' => 'credit_limit',
                'select' => 'agency_agents.credit_limit',
                'formatter' => 'currencyFormatter',
                'name' => lang('Credit Limit'),
            ),
            array(
                'dt' => $i++,
                'alias' => 'available_credit',
                'select' => 'agency_agents.available_credit',
                'formatter' => 'currencyFormatter',
                'name' => lang('Available Credit'),
            ),
            array(
                'dt' => $i++,
                'alias' => 'status',
                'select' => 'agency_agents.status',
                'name' => lang('Status'),
                'formatter' => function ($d) use ($is_export) {
                    if($is_export){
                        return lang($d);
                    }else{
                        switch ($d) {
                            case self::AGENT_STATUS_FROZEN:
                                return '<span class="text-danger">'.lang($d).'</span>';
                                break;

                            case self::AGENT_STATUS_SUSPENDED:
                                return '<span class="text-warning">'.lang($d).'</span>';
                                break;
                        }
                        return $d;
                    }
                },
            ),
        );

        if($this->utils->getConfig('show_agency_rev_share_etc')){
            $columns[]=array(
                'dt' => $i++,
                'alias' => 'rev_share',
                'select' => 'agency_agents.rev_share',
            );
            $columns[]=array(
                'dt' => $i++,
                'alias' => 'rolling_comm',
                'select' => 'agency_agents.rolling_comm',
            );
            $columns[]=array(
                'dt' => $i++,
                'alias' => 'rolling_comm_basis',
                'select' => 'agency_agents.rolling_comm_basis',
            );
        }

        $columns[]=array(
            'dt' => $i++,
            'alias' => 'agent_level',
            'select' => 'agency_agents.agent_level',
            'name' => lang('Agent Level'),
        );

        $columns[]=array(
            'alias' => 'parent_agent_id',
            'select' => 'pa.agent_id',
            'name' => lang('Agent Level'),
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'parent_agent_name',
            'select' =>'pa.agent_name',
            'formatter' => function ($d, $row) use ($is_export) {

                if(empty($d)) return lang('N/A');

                if ($is_export) return $d;

                $ret = "<i class='fa fa-user' ></i> ";
                $agent_id = $row['parent_agent_id'];
                $title = lang('Show Agent Info');
                $ret .= "<a href='".site_url('/agency_management/agent_information/'.$agent_id)."' class='agent-username agent-oper' data-toggle='tooltip' title='$title' >$d</a> ";
                return $ret;
            },
            'name' => lang('Parent Agent'),
        );

        // $columns[]=array(
        //     'dt' => $i++,
        //     'select' => 'agent_level_name',
        //     'formatter' => function ($d, $row) {
        //         if (empty($d)) {
        //             return '';
        //         }
        //         $names = explode(',', $d);
        //         if (count($names) > $row['agent_level']) {
        //             return $names[$row['agent_level']];
        //         } else {
        //             return '';
        //         }
        //     },
        // );
        $columns[]=array(
            'dt' => $i++,
            'alias' => 'vip_level',
            'select' => 'agency_agents.vip_level',
            'formatter' => function ($d) {
                if (empty($d)) {
                    return lang('N/A');
                }
                $this->load->model('vipsetting');
                $group_detail = $this->vipsetting->getVipGroupLevelDetails($d);

                $vipstr = lang('N/A');
                if (isset($group_detail['groupName']) && isset($group_detail['vipLevelName'])) {
                    $vipstr = implode('|', array(lang($group_detail['groupName']),lang($group_detail['vipLevelName'])));
                }

                return $vipstr;
            },
            'name' => lang('Default Player VIP'),
        );
        $columns[]=array(
            'dt' => $i++,
            'alias' => 'settlement_period',
            'select' => 'agency_agents.settlement_period',
            'name' => lang('Settlement Period'),
        );

        $columns[]=array(
            'dt' => $is_export ? null : $i++,
            'alias' => 'agent_id',
            'select' => 'agency_agents.agent_id',
            'formatter' => function ($d, $row) use ($has_parent, $is_export) {

                if ($is_export || $this->utils->isEnabledFeature('agency_hide_sub_agent_list_action')) {
                    return '';
                }

                if ($row['status'] == 'active') {
                    $active_en = '';
                    $inactive_en = 'disabled';
                } else {
                    $active_en = 'disabled';
                    $inactive_en = '';
                }
                $output = '';
                if ($row['status'] == 'active') {
                    $title = lang('Show Hierarchical Tree');
                    $output .= "<a href='javascript:void(0)' class='agent-oper $active_en' data-toggle='tooltip' title='$title' onclick='show_hierarchical_tree($d)'><span class='glyphicon glyphicon-tree-conifer text-success'></span></a> ";
                    if ($has_parent || $row['agent_level'] == 0) {
                        $title = lang('Adjust Credit');
                        $output .= "<a href='javascript:void(0)' class='agent-oper $active_en' data-toggle='tooltip' title='$title' onclick='agent_adjust_credit($d)'><span class='glyphicon glyphicon-credit-card text-warning'></span></a> ";
                    }
                    $title = lang('Add Sub Agents');
                    $output .= "<a href='javascript:void(0)' class='agent-oper $active_en' data-toggle='tooltip' title='$title' onclick='agent_add_sub_agents($d)'><span class='glyphicon glyphicon-plus-sign text-info'></span></a> ";

                    /*
                    $title = lang('Add Players');
                    $output .= "<a href='javascript:void(0)' class='agent-oper $active_en' data-toggle='tooltip' title='$title' onclick='agent_add_players($d)'><span class='glyphicon glyphicon-user'></span></a> ";
                     */
                    $title = lang('Edit This Agent');
                    $output .= "<a href='javascript:void(0)' class='agent-oper $active_en' data-toggle='tooltip' title='$title' onclick='edit_this_agent($d)'><span class='glyphicon glyphicon-edit text-primary'></span></a> ";
                    $title = lang('Suspend This Agent');
                    $output .= "<a href='javascript:void(0)' class='agent-oper $active_en' data-toggle='tooltip' title='$title' onclick='suspend_this_agent($d)'><span class='glyphicon glyphicon-arrow-down text-warning'></span></a> ";
                    $title = lang('Freeze This Agent');
                    $output .= "<a href='javascript:void(0)' class='agent-oper $active_en' data-toggle='tooltip' title='$title' onclick='freeze_this_agent($d)'><span class='glyphicon glyphicon-ban-circle text-danger'></span></a> ";
                    $title = lang('View Keys');
                    $output .= "<span data-toggle='modal' data-target='#view_agent_keys_modal'><a href='javascript:void(0)' class='agent-oper $active_en' data-toggle='tooltip' title='$title' onclick='open_agent_keys($d)'><span class='glyphicon glyphicon-paperclip text-info'></span></a> </span>";
                } else {
                    $title = lang('Activate This Agent');
                    $output .= "<a href='javascript:void(0)' class='agent-oper $inactive_en' data-toggle='tooltip' title='$title' onclick='activate_this_agent($d)'><span class='glyphicon glyphicon-arrow-up text-success'></span></a> ";
                }
                $title = lang('Credit Transaction');
                $output .= "<br><a href='javascript:void(0)' title='$title' onclick='credit_transactions(\"".$row['agent_name']."\")'><span class='fa fa-money text-success'></span></a>";

                return $output;
            },
            'name' => lang('Action'),
        );

        # OUTPUT ###################################################################################
        $result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
        //$this->utils->debug_log($result);

        return $result;
    } // get_agent_list  }}}2
    // activate {{{2
    public function activate($agent_id, $done_by, $is_admin = true) {
        // $data = array(
        //     'status' => 'active',
        // );
        $this->db->where('agent_id', $agent_id)->set('status', self::AGENT_STATUS_ACTIVE);
        // return $this->db->update($this->table_agent, $data);

        $success=$this->runAnyUpdate($this->table_agent);

        if($success){
            $agent_details = $this->get_agent_by_id($agent_id);
            $agent_name = $agent_details['agent_name'];
            $log_params = array(
                'action' => 'activate_agent',
                'link_url' => ($is_admin ? site_url('agency_management/activate_agent') : site_url('agency/activate_agent')). '/' . $agent_id,
                'done_by' => $done_by,
                'done_to' => $agent_name,
                'details' => ($is_admin ? 'Admin ' : 'Agent ') . $done_by .' activated agent '. $agent_name,
            );
            $this->agency_library->save_action($log_params);
        }

        if($this->utils->isEnabledFeature('always_update_subagent_and_player_status')){
            $success=$this->active_all_sub($agent_id, $done_by, $is_admin) && $success;
        }

        return $success;

    } // activate }}}2

    public function active_all_sub($agent_id, $done_by, $is_admin = true){
        //set agent to suspend
        $idArr=$this->get_all_sub_agent_ids($agent_id);

        $idArr[]=$agent_id;
        $idArr=array_unique($idArr);

        $this->db->where_in('agent_id', $idArr)->set('status', self::AGENT_STATUS_ACTIVE);

        $success=$this->runAnyUpdate($this->table_agent);

        if($success){

            // -- if success, save agent action per agent
            foreach ($idArr as $key => $sub_agent_id) {
                if($sub_agent_id == $agent_id) continue;

                $agent_details = $this->get_agent_by_id($sub_agent_id);
                $agent_name = $agent_details['agent_name'];
                $log_params = array(
                    'action' => 'activate_agent',
                    'link_url' => ($is_admin ? site_url('agency_management/activate_agent') : site_url('agency/activate_agent')). '/' . $sub_agent_id,
                    'done_by' => $done_by,
                    'done_to' => $agent_name,
                    'details' => 'Agent ' . $agent_name . ' has been activated after parent agent was activated',
                );
                $this->agency_library->save_action($log_params);

            }
        }

        //block player
        $success=$this->unblock_player_by_agent_ids($idArr) && $success;

        return $success;
    }

    // suspend {{{2
    public function suspend($agent_id, $done_by, $is_admin = true) {
        // $data = array(
        //     'status' => 'suspended',
        // );
        $this->db->where('agent_id', $agent_id)->set('status', self::AGENT_STATUS_SUSPENDED);

        // return $this->db->update($this->table_agent, $data);
        $success=$this->runAnyUpdate($this->table_agent);

        if($success){
            $agent_details = $this->get_agent_by_id($agent_id);
            $agent_name = $agent_details['agent_name'];
            $log_params = array(
                'action' => 'suspend_agent',
                'link_url' => ($is_admin ? site_url('agency_management/suspend_agent') : site_url('agency/suspend_agent')). '/' . $agent_id,
                'done_by' => $done_by,
                'done_to' => $agent_name,
                'details' => ($is_admin ? 'Admin ' : 'Agent ') . $done_by .' suspended agent '. $agent_name,
            );
            $this->agency_library->save_action($log_params);
        }

        //update sub-agent and player
        if($this->utils->isEnabledFeature('always_update_subagent_and_player_status')){
            $success=$this->suspend_all_sub($agent_id, $done_by, $is_admin) && $success;
        }

        return $success;
    } // suspend }}}2

    public function suspend_all_sub($agent_id, $done_by, $is_admin = true){
        //set agent to suspend
        $idArr=$this->get_all_sub_agent_ids($agent_id);

        $idArr[]=$agent_id;
        $idArr=array_unique($idArr);

        $this->db->where_in('agent_id', $idArr)->set('status', self::AGENT_STATUS_SUSPENDED);

        $success=$this->runAnyUpdate($this->table_agent);

        if($success){

            // -- if success, save agent action per agent
            foreach ($idArr as $key => $sub_agent_id) {
                if($sub_agent_id == $agent_id) continue;

                $agent_details = $this->get_agent_by_id($sub_agent_id);
                $agent_name = $agent_details['agent_name'];
                $log_params = array(
                    'action' => 'suspend_agent',
                    'link_url' => ($is_admin ? site_url('agency_management/suspend_agent') : site_url('agency/suspend_agent')). '/' . $sub_agent_id,
                    'done_by' => $done_by,
                    'done_to' => $agent_name,
                    'details' => 'Agent ' . $agent_name . ' has been suspended after parent agent was suspended',
                );
                $this->agency_library->save_action($log_params);

            }
        }

        //block player
        $success=$this->block_player_by_agent_ids($idArr) && $success;

        return $success;
    }

    public function block_player_by_agent_ids($idArr){

        $this->db->where_in('agent_id', $idArr)->set('blocked', 1);

        $success=$this->runAnyUpdate('player');

        $this->db->select('playerId')->where_in('agent_id', $idArr)->from('player');
        $rows=$this->runMultipleRowArray();
        //update game_provider_auth
        $playerIdArr=[];
        if(!empty($rows)){
            foreach ($rows as $row) {
                $playerId=$row['playerId'];
                $playerIdArr[]=$playerId;
            }
        }
        $this->load->model(['game_provider_auth']);
        $success=$this->game_provider_auth->blockPlayersGameAccount($playerIdArr) && $success;

        return $success;
    }

    public function unblock_player_by_agent_ids($idArr){

        $this->db->where_in('agent_id', $idArr)->set('blocked', 0);

        $success=$this->runAnyUpdate('player');

        $this->db->select('playerId')->where_in('agent_id', $idArr)->from('player');
        $rows=$this->runMultipleRowArray();
        //update game_provider_auth
        $playerIdArr=[];
        if(!empty($rows)){
            foreach ($rows as $row) {
                $playerId=$row['playerId'];
                $playerIdArr[]=$playerId;
            }
        }
        $this->load->model(['game_provider_auth']);
        $success=$this->game_provider_auth->unblockPlayersGameAccount($playerIdArr) && $success;

        return $success;
    }

    // freeze {{{2
    public function freeze($agent_id, $done_by, $is_admin = true) {
        // $data = array(
        //     'status' => 'frozen',
        // );
        $this->db->where('agent_id', $agent_id)->set('status', self::AGENT_STATUS_FROZEN);

        // return $this->db->update($this->table_agent, $data);

        $success=$this->runAnyUpdate($this->table_agent);

        if($success){
            $agent_details = $this->get_agent_by_id($agent_id);
            $agent_name = $agent_details['agent_name'];
            $log_params = array(
                'action' => 'freeze_agent',
                'link_url' => ($is_admin ? site_url('agency_management/freeze_agent') : site_url('agency/freeze_agent')). '/' . $agent_id,
                'done_by' => $done_by,
                'done_to' => $agent_name,
                'details' => ($is_admin ? 'Admin ' : 'Agent ') . $done_by .' froze agent '. $agent_name,
            );
            $this->agency_library->save_action($log_params);
        }

        //update sub-agent and player
        if($this->utils->isEnabledFeature('always_update_subagent_and_player_status')){
            $success=$this->freeze_all_sub($agent_id, $done_by, $is_admin) && $success;
        }

        return $success;

    } // freeze }}}2

    public function freeze_all_sub($agent_id, $done_by, $is_admin = true){
        //set agent to suspend
        $idArr=$this->get_all_downline_arr($agent_id);

        $idArr[]=$agent_id;
        $idArr=array_unique($idArr);

        $this->db->where_in('agent_id', $idArr)->set('status', self::AGENT_STATUS_FROZEN);

        $success=$this->runAnyUpdate($this->table_agent);

        if($success){

            // -- if success, save agent action per agent
            foreach ($idArr as $key => $sub_agent_id) {
                if($sub_agent_id == $agent_id) continue;

                $agent_details = $this->get_agent_by_id($sub_agent_id);
                $agent_name = $agent_details['agent_name'];
                $log_params = array(
                    'action' => 'freeze_agent',
                    'link_url' => ($is_admin ? site_url('agency_management/freeze_agent') : site_url('agency/freeze_agent')). '/' . $sub_agent_id,
                    'done_by' => $done_by,
                    'done_to' => $agent_name,
                    'details' => 'Agent ' . $agent_name . ' has been frozen after parent agent was frozen',
                );
                $this->agency_library->save_action($log_params);

            }
        }

        //block player
        $success=$this->block_player_by_agent_ids($idArr) && $success;

        return $success;
    }

    // inactivate {{{2
    public function inactivate($agent_id) {
        $data = array(
            'status' => self::AGENT_STATUS_SUSPENDED,
        );
        $this->db->where('agent_id', $agent_id);
        return $this->db->update($this->table_agent, $data);
    } // inactivate }}}2
    // get_players_by_agent_id {{{2
    /**
     *  get all players by agent_id
     *
     *  @param  int agent_id
     *  @return array
     */
    public function get_players_by_agent_id($agent_id, $date_from = null, $date_to = null) {
        $this->db->select('playerId')->from('player')->where('agent_id', $agent_id);

        if (!empty($date_from) && !empty($date_to)) {
            $this->db->where('createdOn >=', $date_from)->where('createdOn <=', $date_to);
        }

        $result = [];
        $rows = $this->runMultipleRow();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $result[] = $row->playerId;
            }
        }
        return $result;
    } // get_players_by_agent_id  }}}2
    // get_sub_agent_ids_by_parent_id {{{2
    /**
     *  get all sub agents of given agent from agency_agents
     *
     *  @param  int parent_id
     *  @return ids for sub agents
     */
    public function get_sub_agent_ids_by_parent_id($parent_id, $start_date = null, $end_date = null) {
        if ($start_date) {
            $this->db->where('created_on >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('created_on <=', $end_date);
        }
        if (is_array($parent_id)) {
            $this->db->where_in('parent_id', $parent_id);
        } else {
            $this->db->where('parent_id', $parent_id);
        }

        $result = $this->db->get('agency_agents');
        $sub_id = array();
        if ($result->num_rows() > 0) {
            foreach ($result->result() as $r) {
                array_push($sub_id, $r->agent_id);
            }
        }
        return $sub_id;
    } // get_sub_agent_ids_by_parent_id  }}}2
    // update_agent {{{2
    /**
     * update agent by agent_id
     *
     * @param   array
     * @param   int
     */
    public function update_agent($agent_id, $data) {
        $this->db->where('agent_id', $agent_id);
        return $this->db->update($this->table_agent, $data);
    } // update_agent }}}2
    // remove_agent {{{2
    /**
     *  remove a agent from table agency_agents
     *
     *  @param  int agent_id
     */
    public function remove_agent($agent_id) {
        if (is_array($agent_id)) {
            $this->db->where_in('agent_id', $agent_id);
        } else {
            $this->db->where('agent_id', $agent_id);
        }

        return $this->db->delete($this->table_agent);
    } // remove_agent  }}}2
    // get_agent_hierarchical_tree {{{2
    /**
     *  create data for agent hierarchical tree
     *
     *  @param  int agent_id
     *  @return array
     */
    public function get_agent_hierarchical_tree($agent_id) {
        $this->load->model(array("player_model"));
        $tree_array = array();

        $tree_array[] = $this->create_agent_tree_node($agent_id);

        return $tree_array;
    } // get_agent_hierarchical_tree  }}}2
    // create_agent_tree_node {{{2
    /**
     *  create data for agent hierarchical tree
     *
     *  @param  int agent_id
     *  @return array
     */
    public function create_agent_tree_node($agent_id) {
        $agent_details = $this->get_agent_by_id($agent_id);
        $agent_name = $agent_details['agent_name'];

        // create agent node
        $agent_node = array(
            'id' => $agent_name.'_'.$agent_id,
            'text' => $agent_name,
            'state' => ["opened" => true],
        );
        $sub_agent_cnt = 0;
        $player_cnt = 0;

        $player_ids = $this->get_players_by_agent_id($agent_id);
        $this->utils->debug_log('player_ids', $player_ids);
        if (!empty($player_ids)){
            $player_cnt = count($player_ids);
            // create players node
            $players_node = array(
                'id' => 'players_under'.$agent_name,
                'text' => lang('Players'). '('.$player_cnt.')',
                'icon' => 'fa fa-users',
                'state' => ["opened" => true],
            );
            foreach($player_ids as $player_id) {
                $player = $this->player_model->getPlayerUsername($player_id);
                $player_name = $player['username'];
                // player node
                $player_node = array(
                    'id' => $player_name.'_under'.$agent_name,
                    'text' => $player_name,
                    //'icon' => 'glyphicon glyphicon-user',
                    'icon' => 'fa fa-user',
                    //'state' => ["opened" => true],
                );
                $players_node['children'][] = $player_node;
            }

            $agent_node['children'][] = $players_node;
        }

        $sub_ids = $this->get_sub_agent_ids_by_parent_id($agent_id);
        if (!empty($sub_ids)){
            $sub_agent_cnt = count($sub_ids);
            foreach($sub_ids as $sub_id) {
                $agent_node['children'][] = $this->create_agent_tree_node($sub_id);
            }
        }
        $agent_node['text'] .= '('. lang('Sub Agents'). ': '.$sub_agent_cnt.'; '. lang('Players'). ': '.$player_cnt.')';

        return $agent_node;
    } // create_agent_tree_node  }}}2

    public function get_agent_node_players_and_sugAgent_ids($agent_id){
        $player_ids = $this->get_players_by_agent_id($agent_id);
        $agent_node['players_id'] = $player_ids;

        $sub_ids = $this->get_sub_agent_ids_by_parent_id($agent_id);
        $agent_node['sub_agent_id'] = $sub_ids;

        if (!empty($sub_ids)){
            foreach($sub_ids as $sub_id) {
                $nodes = $this->get_agent_node_players_and_sugAgent_ids($sub_id);
                $agent_node['players_id']  = array_merge($agent_node['players_id'], $nodes['players_id']);
                $agent_node['sub_agent_id']  = array_merge($agent_node['sub_agent_id'], $nodes['sub_agent_id']);
            }
        }
        return $agent_node;
    }

	/** get_agent_id_by_agent_name {{{2
	 * overview : get agent id by username
	 *
	 * @param string $agent_name
	 * @return null
	 */
	public function get_agent_id_by_agent_name($agent_name) {
		$this->db->from('agency_agents')->where('agent_name', $agent_name);
		return $this->runOneRowOneField('agent_id');
	} // get_agent_id_by_agent_name }}}2
	/**
	 * overview : get balance details
	 *
	 * @param 	int	$agent_id
	 * @return 	array
	 */
	public function get_agent_balance($agent_id) {
		$this->db->from('agency_agents')->where('agent_id', $agent_id);
		$row = $this->runOneRow();
		$frozen = $row->frozen;
		$main = $row->wallet_balance;
		$wallet_hold = $row->wallet_hold;

		return array('main_wallet' => $main, 'frozen' => $frozen, 'hold' => $wallet_hold,
			'total_balance' => $main + $frozen + $wallet_hold);
	} // get_agent_balance }}}2

    // update_binding_player_id {{{2
    /**
     *  update binding player id for a given agent
     *
     *  @param  INT agent_id
     *  @param  INT player_id
     *  @return
     */
    public function update_binding_player_id($agent_id, $player_id) {
        $this->db->set('binding_player_id', $player_id)->where('agent_id', $agent_id)
            ->update('agency_agents');
    } // update_binding_player_id  }}}2

    // agent }}}1

    // payment {{{1
    // insert_payment {{{2
    /**
     * insert bank info into agent_payment
     *
     * @param   array
     * @param   void
     */
    public function insert_payment($data) {
        $this->db->insert($this->table_payment, $data);
    } // insert_payment }}}2
    // update_payment {{{2
    /**
     * update agent_payment by agent_payment_id
     *
     * @param   array
     * @param   int
     */
    public function update_payment($agent_payment_id, $data) {
        $this->db->where('agent_payment_id', $agent_payment_id);
        $this->db->update($this->table_payment, $data);
    } // update_payment }}}2
    // remove_payment {{{2
    /**
     * remove agent_payment by agent_payment_id
     *
     * @param   array
     * @param   int
     */
    public function remove_payment($agent_payment_id) {
        $this->db->where('agent_payment_id', $agent_payment_id);
        return $this->db->delete($this->table_payment);
    } // remove_payment }}}2
    // get_payment_by_agent_id {{{2
    /**
     * get agent payment by agent_id from agent_payment table
     *
     * @param   int
     * @return  array
     */
    public function get_payment_by_agent_id($agent_id) {
        $sql = "Select * from agent_payment WHERE agent_id =  ?";

        $query = $this->db->query($sql, array($agent_id));

        return $query->result_array();
    }
    // get_payment_by_agent_id }}}2
    // get_payment_by_id {{{2
    /**
     * get agent payment by agent_payment_id from agent_payment table
     *
     * @param   int agent_payment_id
     * @return  array
     */
    public function get_payment_by_id($id) {
        $sql = "Select * from agent_payment WHERE agent_payment_id =  ?";

        $query = $this->db->query($sql, array($id));

        $result = $query->result_array();
        return $result[0];
    }
    // get_payment_by_id }}}2
	/** getMainWallet {{{2
	 * overview : get main wallet by agent id
	 * @param 	int		$agent_id
	 * @return null|string
	 */
	public function getMainWallet($agent_id) {
		$this->db->from('agency_agents')->where('agent_id', $agent_id);
		return $this->runOneRowOneField('wallet_balance');
	}

	/** getBalanceWallet {{{2
	 * overview : get balance wallet
	 * @param  int	$agent_id
	 * @return null|string
	 */
	public function getBalanceWallet($agent_id) {
		$this->db->from('agency_agents')->where('agent_id', $agent_id);
		return $this->runOneRowOneField('wallet_hold');
	}

	/** incMainWallet {{{2
	 * overview : increment main wallet
	 * @param int	$agent_id
	 * @param int	$incAmount
	 * @return bool
	 */
	public function incMainWallet($agent_id, $incAmount) {
		if ($agent_id && $incAmount > 0) {
            if(!$this->isResourceInsideLock($agent_id, Utils::LOCK_ACTION_AGENCY_BALANCE)){
                return false;
            }

			$this->db->set('wallet_balance', 'wallet_balance+' . $incAmount, false);
			$this->db->where('agent_id', $agent_id);
			$this->db->update($this->tableName);

			return true;
		}

		return false;
	} // incMainWallet }}}2
	/** decMainWallet {{{2
	 * overview : decrement main wallet
	 * @param int	$agent_id
	 * @param $decAmount
	 * @return bool
	 */
	public function decMainWallet($agent_id, $decAmount) {
		if ($agent_id && $decAmount > 0) {
            if(!$this->isResourceInsideLock($agent_id, Utils::LOCK_ACTION_AGENCY_BALANCE)){
                return false;
            }

			$this->db->set('wallet_balance', 'wallet_balance-' . $decAmount, false);
			$this->db->where('agent_id', $agent_id);
			$this->db->update($this->tableName);

			return true;
		}

		return false;
	} // decMainWallet }}}2
	/** incBalanceWallet {{{2
	 * overview : increment main wallet
	 * @param int	$agent_id
	 * @param int	$incAmount
	 * @return bool
	 */
	public function incBalanceWallet($agent_id, $incAmount) {
		if ($agent_id && $incAmount > 0) {
            if(!$this->isResourceInsideLock($agent_id, Utils::LOCK_ACTION_AGENCY_BALANCE)){
                return false;
            }

			$this->db->set('wallet_hold', 'wallet_hold+' . $incAmount, false);
			$this->db->where('agent_id', $agent_id);
			$this->db->update($this->tableName);

			return true;
		}

		return false;
	} // incBalanceWallet }}}2
	/** decBalanceWallet {{{2
	 * overview : decrement main wallet
	 * @param int	$agent_id
	 * @param int	$decAmount
	 * @return bool
	 */
	public function decBalanceWallet($agent_id, $decAmount) {
		if ($agent_id && $decAmount > 0) {
            if(!$this->isResourceInsideLock($agent_id, Utils::LOCK_ACTION_AGENCY_BALANCE)){
                return false;
            }

			$this->db->set('wallet_hold', 'wallet_hold-' . $decAmount, false);
			$this->db->where('agent_id', $agent_id);
			$this->db->update($this->tableName);

			return true;
		}

		return false;
	}

	/** freezeWalletBalance {{{2
	 * overview : freeze wallet balance
	 * @param int		$agent_id
	 * @param double	$amount
	 * @param string $walletType
	 * @return bool
	 */
	public function freezeWalletBalance($agent_id, $amount, $walletType) {
        if ($walletType == 'main') {
            $success=$this->decMainWallet($agent_id, $amount);
            if(!$success){
                return $success;
            }
        } else {
            $success=$this->decBalanceWallet($agent_id, $amount);
            if(!$success){
                return $success;
            }
        }
		$this->db->set('frozen', 'frozen+' . $amount, false)->where('agent_id', $agent_id);
		return $this->runAnyUpdate('agency_agents');
	}

	/** addWithdrawRequest {{{2
	 * overview : add withdraw request
	 *
	 * @param int	$agent_id
	 * @param array $payment_method
	 * @param $amount
	 * @return mixed
	 */
	public function addWithdrawRequest($agent_id, $payment_method, $amount, $walletType) {
		$data = array(
			'agent_id' => $agent_id,
			'payment_method' => $payment_method['payment_method'],
			'amount' => $amount,
			'fee' => 0,
			'status' => self::STATUS_WITHDRAW_REQUEST,
			'agent_payment_id' => $payment_method['agent_payment_id'],
			'created_on' => $this->utils->getNowForMysql(),
			'updated_on' => $this->utils->getNowForMysql(),
		);

		$id = $this->insertData('agent_payment_history', $data);

		//frozen
		$success = $this->freezeWalletBalance($agent_id, $amount, $walletType) && $id;
		return $id;
	}

    // getAgentRequestCount {{{2
	public function getAgentRequestCount($type, $dateFrom, $dateTo) {

		switch ($type) {
			case 'request':
				$status = self::STATUS_WITHDRAW_REQUEST;
			break;
			case 'approved':
				$status = self::STATUS_WITHDRAW_APPROVED;
			break;
			case 'declined':
				$status = self::STATUS_WITHDRAW_DECLINED;
			break;
			default:
			     $status = self::STATUS_WITHDRAW_REQUEST;
		}

		$sql = "SELECT COUNT(*) AS count_ FROM agent_payment_history WHERE STATUS = ? AND  created_on >= ? AND created_on <= ? " ;

        $count = 0 ;

		$query =  $this->db->query($sql,array($status,$dateFrom,$dateTo));
		$row = $query->row_array();

		if (isset($row) && !empty($row)){
	    	$count = $row['count_'];
		}

		return $count;
	}

	/** // getStatusListKV {{{2
	 * overview get status list KV
	 *
	 * @return array
	 */
	public function getStatusListKV() {
		return array(
			'' => lang("N/A"),
			self::STATUS_WITHDRAW_REQUEST => lang('Request'),
			self::STATUS_WITHDRAW_APPROVED => lang('Approved'),
			self::STATUS_WITHDRAW_DECLINED => lang('Declined'),
		);
	}

	/** getSearchPayment {{{2
	 * overview : get search payment
	 *
	 * @param int   $limit
	 * @param int   $offset
	 * @param array $data
	 * @return array
	 */
	public function getSearchPayment($limit, $offset, $data) {

		$search = array();
		$sortby = null;
		$desc_order = null;

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		foreach ($data as $key => $value) {
			if ($key == 'request_range' && $value != '') {
				$search[$key] = "p.created_on BETWEEN $value";
			} elseif ($key == 'status' && $value != null) {
				$search[$key] = "p.status = " . $value;
			} elseif ($key == 'agent_name') {
				$search[$key] = "a.$key LIKE '%" . $value . "%'";
			} elseif ($value != null) {
				$search[$key] = "a.$key = '" . $value . "'";
			}
		}

		$query = <<<EOD
SELECT p.*, a.agent_name, ap.account_number , ap.bank_name, adminusers.username as adminuser
FROM agent_payment_history as p
LEFT JOIN agency_agents as a ON p.agent_id = a.agent_id
LEFT JOIN agent_payment as ap ON p.agent_payment_id = ap.agent_payment_id
LEFT JOIN adminusers ON p.processed_by = adminusers.userId

EOD;

		if (count($search) > 0) {
			$query .= " WHERE " . implode(' AND ', $search);
		}

		$run = $this->db->query("$query $limit $offset");
		return $run->result_array();
	}

	/** declinePayment {{{2
	 * overview : decline payment
	 *
	 * @param int	 $history_id
	 * @param string $reason
	 * @param int	 $adminUserId
	 * @return bool
	 */
	public function declinePayment($history_id, $reason, $adminUserId) {
		$this->utils->debug_log('history_id', $history_id, 'reason', $reason, 'adminUserId', $adminUserId);
		$success = false;
		$this->db->from('agent_payment_history')->where('agent_payment_history_id', $history_id);
		$row = $this->runOneRow();
		if ($row && $row->status==self::STATUS_WITHDRAW_REQUEST) {

			$agent_id = $row->agent_id;
			$amount = $row->amount;

			$this->db->set('reason', $reason)
				->set('updated_on', $this->utils->getNowForMysql())
				->set('processed_on', $this->utils->getNowForMysql())
				->set('processed_by', $adminUserId)
				->set('status', self::STATUS_WITHDRAW_DECLINED)
				->where('agent_payment_history_id', $history_id)
				->where('status', self::STATUS_WITHDRAW_REQUEST);
			$success = $this->runAnyUpdate('agent_payment_history');
			if ($success) {
				//put frozen back
				$this->db->set('frozen', 'frozen-' . $amount, false)
					->set('wallet_balance', 'wallet_balance+' . $amount, false)
					->where('agent_id', $agent_id);
				$success = $this->runAnyUpdate('agency_agents');
			}

		}
		return $success;
	}

	/** approvePayment {{{2
	 * overview : approved payment
	 * @param int	 $history_id
	 * @param string $reason
	 * @param int	 $adminUserId
	 * @return bool
	 */
	public function approvePayment($history_id, $reason, $adminUserId) {
		$this->utils->debug_log('history_id', $history_id, 'reason', $reason, 'adminUserId', $adminUserId);
		$success = false;
		$this->db->from('agent_payment_history')->where('agent_payment_history_id', $history_id);
		$row = $this->runOneRow();
		if ($row && $row->status==self::STATUS_WITHDRAW_REQUEST) {

			$agent_id = $row->agent_id;
			$amount = $row->amount;

			$this->db->set('reason', $reason)
				->set('updated_on', $this->utils->getNowForMysql())
				->set('processed_on', $this->utils->getNowForMysql())
				->set('processed_by', $adminUserId)
				->set('status', self::STATUS_WITHDRAW_APPROVED)
				->where('agent_payment_history_id', $history_id)
				->where('status', self::STATUS_WITHDRAW_REQUEST);
			$success = $this->runAnyUpdate('agent_payment_history');
			if ($success) {
				$success = $this->transactions->withdrawFromAgentFrozen($agent_id, $amount,
					$adminUserId . ' approve [' . $history_id . '] ' . $amount, $adminUserId);
			} else {
				$this->utils->debug_log('update agent_payment_history failed');
			}

		} else {
			$this->utils->debug_log('cannot find agent_payment_history', $history_id);
		}
		return $success;
	}
    // payment }}}1

    // credit transactions {{{1
    // insert_transaction {{{2
    /**
     *
     *
     *  @param  array data for a new transaction
     *  @return transaction_id
     */
    public function insert_transaction($data) {
        $transaction_id = $this->insertData($this->table_transaction, $data);
        return $transaction_id;
    } // insert_transaction  }}}2
    // get_transactions {{{2
    /**
     *  get structures from table agency_structures
     *
     *  @param  search request
     *  @return array structure data
     */
    public function get_transactions($request) {
        $this->load->library(array('data_tables'));
        $this->load->model(array('transactions'));
        $input = $this->data_tables->extra_search($request);
        //$this->utils->debug_log('input:', $input);
        $table = 'transactions';
        $where = array();
        $values = array();
        $joins = array();
        $distinct=false;

        if (isset($input['parent_id'])) {
            $agent_id = $input['parent_id'];
            $agent_ids = $this->get_all_downline_arr($agent_id);
            $where[] = "((from_type = ? AND from_id IN (" . implode(',', $agent_ids) . ")) OR (to_type = ? AND to_id = ?))";
            $values[] = Transactions::AGENT;
            $values[] = Transactions::AGENT;
            $values[] = $input['parent_id'];
        }

        if (isset($input['transaction_type']) && ! empty($input['transaction_type'])) {
            if ( ! is_array($input['transaction_type'])) {
                $input['transaction_type'] = array($input['transaction_type']);
            }
            $where[] = "transaction_type IN (".implode(',', $input['transaction_type']).")";
        }

        if (isset($input['search_on_date']) && $input['search_on_date']) {
            if (isset($input['date_from'], $input['date_to'])) {
                $where[] = "created_at BETWEEN ? AND ?";
                $values[] = $input['date_from'];
                $values[] = $input['date_to'];
            }
        }

        if (isset($input['min_credit_amount'])) {
            $where[] = "amount >= ?";
            $values[] = $input['min_credit_amount'];
        }

        if (isset($input['max_credit_amount'])) {
            $where[] = "amount <= ?";
            $values[] = $input['max_credit_amount'];
        }

        if (isset($input['agent_name']) && $input['agent_name'] != '') {
            $where[] = "((to_type = ? AND to_username = ?) OR (from_type = ? AND from_username = ?))";
            $values[] = Transactions::AGENT;
            // $values[] = '%'.$input['agent_name'].'%';
            $values[] = $input['agent_name'];
            $values[] = Transactions::AGENT;
            // $values[] = '%'.$input['agent_name'].'%';
            $values[] = $input['agent_name'];
    /*
    if (isset($input['parent_id']) && $input['parent_id'] != '') {
    $joins['agency_agents'] = 'agency_agents.agent_name = agency_transactions.agent_name';
    }
     */
        // } else if (isset($input['parent_name']) && $input['parent_name'] != '') {
        //     $where[] = "((to_type = ? AND to_username = ?) OR (from_type = ? AND from_username = ?))";
        //     $values[] = Transactions::AGENT;
        //     $values[] = $input['parent_name'];
        //     $values[] = Transactions::AGENT;
        //     $values[] = $input['parent_name'];
        }

        if (isset($input['player_username']) && $input['player_username'] != '') {
            $where[] = "((to_type = ? AND to_username = ?) OR (from_type = ? AND from_username = ?))";
            $values[] = Transactions::PLAYER;
            $values[] = $input['player_username'];
            $values[] = Transactions::PLAYER;
            $values[] = $input['player_username'];
        }

        if (isset($input['ip_used']) && $input['ip_used'] != '') {
            $where[] = "ip_used = ?";
            $values[] = $input['ip_used'];
        }
        $this->utils->debug_log($where, $values);

        # DEFINE TABLE COLUMNS #####################################################################
        $i = 0;
        $columns = array(
            array(
                'select' =>'from_type',
            ),
            array(
                'select' =>'from_id',
            ),
            array(
                'select' =>'to_type',
            ),
            array(
                'select' =>'to_id',
            ),
            array(
                'dt' => $i++,
                'name' => lang('Date'),
                'select' =>'created_at',
            ),
            array(
                'dt' => $i++,
                'select' => 'from_username',
                'name' => lang('From User'),
                'formatter' => function ($d, $row) {
                    if ($row['from_type'] == Transactions::AGENT) {
                        $agent_details = $this->get_agent_by_id($row['from_id']);
                        return $agent_details['agent_name'];
                    } else {
                        return $d;
                    }
                },
            ),
            array(
                'dt' => $i++,
                'select' => 'to_username',
                'name' => lang('To User'),
                'formatter' => function ($d, $row) {
                    if ($row['to_type'] == Transactions::AGENT) {
                        $agent_details = $this->get_agent_by_id($row['to_id']);
                        return $agent_details['agent_name'];
                    } else {
                        return $d;
                    }
                },
            ),
            array(
                'dt' => $i++,
                'select' => 'amount',
                'name' => lang('Amount'),
                'formatter' => 'currencyFormatter',
            ),
            array(
                'dt' => $i++,
                'select' => 'before_balance',
                'name' => lang('Before Balance'),
                'formatter' => 'currencyFormatter',
            ),
            array(
                'dt' => $i++,
                'select' => 'after_balance',
                'name' => lang('After Balance'),
                'formatter' => 'currencyFormatter',
            ),
            array(
                'dt' => $i++,
                'name' => lang('Remarks'),
                'select' => 'note',
            ),
            /*
            array(
                'dt' => $i++,
                'select' => 'ip_used',
            ),
             */
        );

        # OUTPUT ###################################################################################
        $countOnlyField='transactions.id';
        $result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, [], [],
            $distinct, [], '', $countOnlyField);
        //$this->utils->debug_log($result);

        return $result;
    } // get_transactions  }}}2
    // transactions }}}1

    // agency_logs {{{1
    // insert_log {{{2
    /**
     *
     *
     *  @param  array data for a new log
     *  @return log_id
     */
    public function insert_log($data) {
        $log_id = $this->insertData($this->table_log, $data);
        $res = !empty($log_id) ? true : false;
        $this->utils->debug_log('the log id --->', $log_id);
        $this->utils->debug_log('the log result --->', $res);
        return $res;
    } // insert_log  }}}2
    // get_logs {{{2
    /**
     *  get structures from table agency_structures
     *
     *  @param  array $request
     *  @param  boolean $is_export
     *  @return array structure data
     */
    public function get_logs($request, $is_export = false) {
        $this->load->library(array('data_tables'));
        $i = 0;
        $input = $this->data_tables->extra_search($request);
        $this->utils->debug_log('get logs:', $input);
        $table = $this->table_log;
        $where = array();
        $values = array();

        if (isset($input['search_on_date']) && $input['search_on_date']) {
            if (isset($input['date_from'], $input['date_to'])) {
                $where[] = "done_at BETWEEN ? AND ?";
                $values[] = $input['date_from'];
                $values[] = $input['date_to'];
            }
        }
        if (isset($input['agent_name']) && $input['agent_name'] != '') {
            $where[] = "(done_by LIKE ? OR done_to LIKE ?)";
            $values[] = '%'.$input['agent_name'].'%';
            $values[] = '%'.$input['agent_name'].'%';
        }
        if (isset($input['agent_action']) && $input['agent_action'] != '') {
            $where[] = "action = ?";
            $values[] = $input['agent_action'];
        }
        $this->utils->debug_log($where, $values);

        # DEFINE TABLE COLUMNS #####################################################################
        $columns = array(
            array(
                'select' =>'link_name',
            ),
            array(
                'dt' => $i++,
                'select' =>'done_at',
                'name' => lang('Date')
            ),
            array(
                'dt' => $i++,
                'select' => 'done_by',
                'name' => lang('Done By')
            ),
            array(
                'dt' => $i++,
                'select' => 'done_to',
                'name' => lang('Done To')
            ),
            array(
                'dt' => $i++,
                'select' => 'action',
                'name' => lang('Action')
            ),
            array(
                'dt' => $i++,
                'select' => 'link_url',
                'name' => lang('Link'),
                'formatter' => function ($d, $row) use ($is_export) {
                    if($is_export){
                        return $d;
                    }else{
                        $link_name = $row['link_name'];
                        $ret = "<a href='$d' class='agent-oper' data-toggle='tooltip'>$link_name</a> ";
                        return $ret;
                    }
                },
            ),
            array(
                'dt' => $i++,
                'select' => 'details',
                'name' => lang('Details'),
                'formatter' => function($d) use($is_export){
                    if($is_export){
                        return strip_tags($d);
                    }else{
                        return $d;
                    }
                }
            ),
        );

        # OUTPUT ###################################################################################
        $result = $this->data_tables->get_data($request, $columns, $table, $where, $values);
        //$this->utils->debug_log($result);

        return $result;
    } // get_logs  }}}2
    // agency_logs }}}1

    // agency_settlement {{{1
    // insert_settlement {{{2
    /**
     *  insert data into agency_settlement
     *
     *  @param  array data for a new settlement
     *  @return settlement_id
     */
    public function insert_settlement($data) {
        $settlement_id = $this->insertData($this->table_settlement, $data);
        return $settlement_id;
    } // insert_settlement  }}}2
    // update_settlement {{{2
    /**
     *  update data into agency_settlement
     *
     *  @param  array data for a new settlement
     *  @return settlement_id
     */
    public function update_settlement($settlement_id, $data) {
        $this->db->where('settlement_id', $settlement_id);
        $this->db->update($this->table_settlement, $data);
    } // update_settlement  }}}2
    // get_settlement_by_id {{{2
    /**
     *  get settlement details by settlement_id
     *
     *  @param  settlement_id
     *  @return array for settlement details
     */
    public function get_settlement_by_id($settlement_id) {
        $this->db->from($this->table_settlement)->where('settlement_id', $settlement_id);
        return $this->runOneRowArray();
    } // get_settlement_by_id  }}}2
    // get_all_settlement {{{2
    /**
     *  search settlement and return result array
     *
     *  @param  int agent_id search settlement under given agent_id
     *  @return array
     */
    public function get_all_settlement($agent_id = null) {
        $where = '';
        if ($agent_id) {
            $all_ids = $this->get_all_sub_agent_ids($agent_id);
            $where = 'WHERE agent_id IN ('. implode(',', $all_ids) . ')';
        }

        $qStr = <<<EOD
    SELECT * FROM agency_settlement
    $where
EOD;
        $query = $this->db->query($qStr);

        return $query->result_array();
    } // get_all_settlement  }}}2
    // get_current_settlement {{{2
    /**
     *  get settlement in 'Current' status of given agent
     *
     *  @param  int agent_id
     *  @return array
     */
    public function get_current_settlement($agent_id) {
        $where = 'WHERE agent_id = '.$agent_id;
        $where .= " AND status = 'Current'";

        $qStr = <<<EOD
    SELECT * FROM agency_settlement
    $where
EOD;
        $query = $this->db->query($qStr);

        return $query->result_array();
    } // get_current_settlement  }}}2
    // get_settlement {{{2
    /**
     *  get structures from table agency_structures
     *
     *  @param  array search request
     *  @param  boolean when exported into a file (excel usually) HTML tags will be removed
     *  @return array structure data
     */
    public function get_settlement($request, $mode='only_agent', $is_export = false) {
        $this->load->library(array('data_tables'));
        $this->data_tables->is_export = $is_export;

        $input = $this->data_tables->extra_search($request);
        // $this->utils->debug_log('get_settlement request', $request);
        $table = $this->table_settlement;
        $where = array();
        $values = array();
        $joins = array();
        $joins['agency_agents'] = 'agency_agents.agent_id = agency_settlement.agent_id';
        $joins['external_system'] = 'external_system.id = agency_settlement.game_platform_id';
        $joins['game_type'] = 'game_type.id = agency_settlement.game_type_id';

        if (isset($input['search_on_date']) && $input['search_on_date']) {
            if (isset($input['date_from'], $input['date_to'])) {
                $where[] = "agency_settlement.settlement_date_from BETWEEN ? AND ?";
                $values[] = $input['date_from'];
                $values[] = $input['date_to'];
            }
        }
        $has_parent = false;
        $parent_id = null;
        if (isset($input['parent_id']) && $input['parent_id'] != '') {
            $has_parent = true;
            $parent_id = $input['parent_id'];
            $sub_ids = $this->get_sub_agent_ids_by_parent_id($parent_id);

            if($mode=='only_agent'){
                $where[] = 'agency_settlement.agent_id = ?';
                $values[] = $parent_id;
            }elseif($mode=='only_subagent'){
                $k=array_search($parent_id, $sub_ids);
                if($k!==FALSE){
                    unset($sub_ids[$k]);
                }

                if(!empty($sub_ids)){
                    $where[]='agency_settlement.agent_id in ('.implode(',', $sub_ids).') ';
                }else{
                    //is empty
                    $where[]='agency_settlement.agent_id = ?';
                    $values[]=-1;
                }

            }


            // $w = "(agency_settlement.agent_id = ?";
            // $values[] = $parent_id;
            // if (!empty($sub_ids)) {
            //     foreach ($sub_ids as $id) {
            //         $w .= " OR agency_settlement.agent_id = ?";
            //         $values[] = $id;
            //     }
            // }
            // $w .= ")";
            // $where[] = $w;
        }
        if($mode!='only_agent'){
            if (isset($input['agent_name']) && $input['agent_name'] != '') {
                $agent_details = $this->get_agent_by_name($input['agent_name']);
                $agent_id = $agent_details['agent_id'];
                $where[] = "agency_settlement.agent_id = ?";
                $values[] = $agent_id;
            }
        }
        if (isset($input['parent_name']) && $input['parent_name'] != '') {
            $parent_details = $this->get_agent_by_name($input['parent_name']);
            $parent_id = $parent_details['agent_id'];
            $where[] = "agency_agents.parent_id = ?";
            $values[] = $parent_id;
        }
        if (isset($input['status']) && $input['status'] != '') {
            if ($input['status'] != 'frozen') {
                $where[] = "agency_settlement.frozen = 0";
                $where[] = "agency_settlement.status = ?";
                $values[] = $input['status'];
            } else {
                $where[] = "agency_settlement.frozen = 1";
            }
        }
        /*
        if (isset($input['date_from']) && $input['date_from'] != '') {
            $where[] = "agency_settlement.settlement_date_from >= ?";
            $values[] = $input['date_from'];
        }
        if (isset($input['date_to']) && $input['date_to'] != '') {
            $where[] = "agency_settlement.settlement_date_to <= ?";
            $date_to = $input['date_to'];
            $day_end = date("Y-m-d", strtotime("$date_to")).' 23:59:59';
            $values[] = $day_end;
        }
         */
        if (isset($input['period']) && $input['period'] != '') {
            $where[] = "agency_settlement.settlement_period = ?";
            $values[] = $input['period'];
        }
        $this->utils->debug_log('get_settlement where values', $where, $values);

        # DEFINE TABLE COLUMNS #####################################################################
        $i = 0;
        $columns = array(
            array(
                'alias' => 'frozen',
                'select' =>'agency_settlement.frozen',
            ),
            array(
                'alias' => 'settlement_id',
                'select' =>'agency_settlement.settlement_id',
            ),
            array(
                'alias' => 'date_from',
                'select' =>'agency_settlement.settlement_date_from',
            ),
            array(
                'alias' => 'date_to',
                'select' =>'agency_settlement.settlement_date_to',
            ),
            array(
                'alias' => 'agent_id',
                'select' =>'agency_agents.agent_id',
            ),
            array(
                'alias' => 'start_day',
                'select' =>'agency_agents.settlement_start_day',
            ),
            array(
                'dt' => $i++,
                'alias' => 'agent_name',
                'select' => 'agency_agents.agent_name',
                'formatter' => function ($d, $row) use ($is_export) {
                    if ($is_export) {
                        return $d;
                    } else {
                        $ret = "<i class='fa fa-user' ></i> ";
                        $agent_id = $row['agent_id'];
                        $title = lang('Show Agent Info');
                        $ret .= "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='$title' onclick='show_agent_info($agent_id)'>$d</a> ";
                        // $ret .= "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='$title' onclick=\"show_agent_players_win_loss('".$d."','".date("Y-m-d 00:00:00", strtotime($row['date_from']))."','".date("Y-m-d 23:59:59", strtotime($row['date_to']))."','".$row['status']."')\">$d</a> ";
                        return $ret;
                    }
                },
                'name' => lang('Agent Username'),
            ),
            array(
                'dt' => $i++,
                'alias' => 'game_platform',
                'select' => 'external_system.system_code',
                'name' => lang('Game Platform'),
            ),
            array(
                'dt' => $i++,
                'alias' => 'game_type',
                'select' => 'game_type.game_type',
                'formatter' => function ($d, $row) {
                    return lang($d);
                },
                'name' => lang('Game Type'),
            ),
            array(
                'dt' => $i++,
                'alias' => 'status',
                'select' =>'agency_settlement.status',
                'formatter' => function ($d, $row){
                    if($row['frozen'] == 1) {
                        $ret = '<span style="color:red">'.lang('agency.settlement.status.frozen').'</span>';
                    } else {
                        $ret = lang("agency.settlement.status.$d");
                    }
                    return $ret;
                },
                'name' => lang('Status'),
            ),
            array(
                'dt' => $i++,
                'alias' => 'settlement_period',
                'select' => 'agency_settlement.settlement_period',
                'formatter' => function ($d, $row){
                    if($d == 'Weekly') {
                        $ret = $d . '('.$row['start_day'].')';
                    } else {
                        $ret = $d;
                    }
                    return $ret;
                },
                'name' => lang('Settlement Period'),
            ),
            array(
                'dt' => $i++,
                'alias' => 'date_range',
                'select' =>'agency_settlement.settlement_date_from',
                'formatter' => function ($d, $row){
                    $range = date("Y-m-d", strtotime($d));
                    $range .= ' ~ ';
                    $to = $row['date_to'];
                    $range .= date("Y-m-d", strtotime($to));
                    return $range;
                },
                'name' => lang('Date Range'),
            ),
            array(
                'dt' => $i++,
                'alias' => 'rev_share',
                'select' => 'agency_agents.rev_share',
                'formatter' => function($d, $row){
                    return $d.'%';
                },
                'name' => lang('Rev Share'),
            ),
            array(
                'dt' => $i++,
                'alias' => 'bets',
                'select' => 'agency_settlement.bets',
                'formatter' => 'currencyFormatter',
                'name' => lang('Bets'),
            ),
            // array(
            //     'dt' => $i++,
            //     'alias' => 'wins',
            //     'select' => 'agency_settlement.wins',
            //     'formatter' => 'currencyFormatter',
            //     'name' => lang('Wins'),
            // ),
            // // array(
            //     'dt' => $i++,
            //     'alias' => 'bonuses',
            //     'select' => 'agency_settlement.bonuses',
            //     'formatter' => 'currencyFormatter',
            //     'name' => lang('Bonuses'),
            // ),
            // array(
            //     'dt' => $i++,
            //     'alias' => 'rebates',
            //     'select' => 'agency_settlement.rebates',
            //     'formatter' => 'currencyFormatter',
            //     'name' => lang('Rebates'),
            // ),
            array(
                'dt' => $i++,
                'alias' => 'net_gaming',
                'select' => 'agency_settlement.net_gaming',
                'formatter' => 'currencyFormatter',
                'name' => lang('Net Gaming'),
            ),
        );

        // if($this->utils->getConfig('show_agency_rev_share_etc')){
        //     $columns[]= array(
        //         'dt' => $i++,
        //         'alias' => 'rev_share_amt',
        //         'select' => 'agency_settlement.rev_share_amt',
        //         'formatter' => 'currencyFormatter',
        //         'name' => lang('Rev Share Amt'),
        //     );
        //     $columns[]= array(
        //         'dt' => $i++,
        //         'alias' => 'rolling_comm',
        //         'select' => 'agency_agents.rolling_comm',
        //         'formatter' => 'currencyFormatter',
        //         'name' => lang('Rolling Comm'),
        //     );
        //     $columns[]=array(
        //         'dt' => $i++,
        //         'alias' => 'rolling_comm_basis',
        //         'select' => 'agency_agents.rolling_comm_basis',
        //         'formatter' => 'currencyFormatter',
        //         'name' => lang('Rolling Comm Basis'),
        //     );
        // }

        // $columns[]=array(
        //         'dt' => $i++,
        //         'alias' => 'lost_bets',
        //         'select' => 'agency_settlement.lost_bets',
        //         'formatter' => 'currencyFormatter',
        //         'name' => lang('Lost Bets'),
        //     );
        // $columns[]=array(
        //         'dt' => $i++,
        //         'alias' => 'bets_except_tie',
        //         'select' => 'agency_settlement.bets_except_tie',
        //         'formatter' => 'currencyFormatter',
        //         'name' => lang('Bets Except Tie Bets'),
        //     );

        if($this->utils->isEnabledFeature('rolling_comm_for_player_on_agency')){
            $columns[]=array(
                'dt' => $i++,
                'alias' => 'roll_comm_amt',
                'select' => 'agency_settlement.roll_comm_amt',
                'formatter' => 'currencyFormatter',
                'name' => lang('Rolling Comm Out'),
            );

            // $columns[]=array(
            //     'dt' => $i++,
            //     'alias' => 'roll_comm_amt_ebet',
            //     'select' => 'agency_settlement.roll_comm_amt_ebet',
            //     'formatter' => 'currencyFormatter',
            //     'name' => lang('Rolling Comm Sub'),
            //     //'name' => lang('Rolling Comm Amt EBET'),
            // );
            $columns[]=array(
                'dt' => $i++,
                'alias' => 'roll_comm_income',
                'select' => 'agency_settlement.roll_comm_income',
                'formatter' => function ($d, $row) use ($mode){
                    $ret=$this->utils->formatCurrencyNoSym($d);
                    if ($row['roll_comm_payment_status'] != 'paid') {
                        if($mode!='only_agent'){
                            $ret='<input type="button" class="btn btn-primary btn-xs" value="'.
                                lang('Pay to').' '.$row['agent_name'].'" onclick="pay_rolling_comm('.$row['settlement_id'].')"> '
                                .$ret;
                        }
                    }

                    return $ret;
                },
                'name' => lang('Rolling Comm Income'),
            );
            $columns[]= array(
                'dt' => $i++,
                'alias' => 'rolling_comm',
                'select' => 'agency_agents.rolling_comm',
                'formatter' => function($d, $row){
                    return $d.'%';
                },
                'name' => lang('Rolling Comm Rate'),
            );
            $columns[]=array(
                'dt' => $i++,
                'alias' => 'roll_comm_payment_status',
                'select' => 'agency_settlement.player_rolling_comm_payment_status',
                'formatter' => function ($d, $row) use ($mode){
                    if ($d == 'paid') {
                        $ret = lang('Paid');
                        // $settle_id = $row['settlement_id'];
                        // $ret = '<a href="/agency/show_player_rolling_comm_info/' . $settle_id . '"';
                        // $ret .= ' data-toggle="tooltip" target="_blank" title="' . lang('Player Rolling Comm Details') . '">'.$val.'</a> ';
                    }else{
                        $ret = lang('Not Paid');
                    }
                    return $ret;
                },
                'name' => lang('Rolling Comm Payment Status'),
            );
        }
        $columns[]=array(
                'dt' => $i++,
                'alias' => 'my_earning',
                'select' => 'agency_settlement.my_earning',
                'formatter' => 'currencyFormatter',
                // 'formatter' => function ($d, $row){
                //     // if ($row['status'] == 'current') {
                //         return $this->utils->formatCurrencyNoSym(($d*$row['rev_share']/100) + ($row['roll_comm_income']-$row['roll_comm_amt']));
                //     // } else {
                //         // return $this->utils->formatCurrencyNoSym($d);
                //     // }
                // },
                'name' => lang('My Earning'),
            );
        $columns[]=array(
                'dt' => $i++,
                'alias' => 'payable_amt',
                'select' => 'agency_settlement.payable_amt',
                'formatter' => 'currencyFormatter',
                'name' => lang('Current Amt Payable'),
            );
        $columns[]=array(
                'dt' => $i++,
                'alias' => 'actual_amt_payable',
                'select' => 'agency_settlement.payable_amt',
                // 'formatter' => 'currencyFormatter',
                'formatter' => function ($d, $row){
                    // if ($row['status'] == 'current') {
                        return $this->utils->formatCurrencyNoSym($d - $row['roll_comm_income']);
                    // } else {
                        // return $this->utils->formatCurrencyNoSym($d);
                    // }
                },
                'name' => lang('Actual Amt Payable'),
            );
        $columns[]=array(
                'dt' => $i++,
                'alias' => 'balance', // if status == 'unsettled' balance = payable_amt
                'select' => 'agency_settlement.balance',
                'formatter' => function ($d, $row) use ($is_export){
                    if ($is_export == false && $d > 0 && $row['status'] == 'current') {
                        $agent_name = '"'. $row['agent_name']. '"';
                        $title = lang('Show Unsettled Settlements');
                        $status = '"unsettled"';
                        $output = "<a href='javascript:void(0)' data-toggle='tooltip' title='$title' onclick='show_unsettled_settlements($agent_name, $status)'>" . $this->utils->formatCurrencyNoSym($d) . "</a> ";
                    } else {
                        $output = $this->utils->formatCurrencyNoSym($d);
                    }
                    return $output;
                },
                'name' => lang('Balance'),
            );
        $columns[]=array(
                'dt' => $i++,
                'alias' => 'total_payable_amt',
                'select' => 'agency_settlement.balance',
                'formatter' => function ($d, $row){
                    if ($row['status'] == 'current') {
                        return $this->utils->formatCurrencyNoSym($d + $row['payable_amt'] - $row['roll_comm_income']);
                        // return $this->utils->formatCurrencyNoSym($d + $row['payable_amt']);
                    } else {
                        return '';
                    }
                },
                'name' => lang('Total Amt Payable'),
            );
        $columns[]=array(
                'dt' => $i++,
                'alias' => 'parent_name',
                'select' => 'agency_agents.parent_id',
                'formatter' => function ($d, $row) use ($is_export, $has_parent, $parent_id){
                    if ($d > 0){
                        $parent_details = $this->get_agent_by_id($d);
                        $parent_name = $parent_details['agent_name'];
                        if ($is_export || ($has_parent && $row['agent_id'] == $parent_id)) {
                            $ret = $parent_name;
                        } else {
                            $ret = "<i class='fa fa-user' ></i> ";
                            $title = lang('Show Agent Info');
                            $ret .= "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='$title' onclick='show_agent_info($d)'>$parent_name</a> ";
                        }
                    } else {
                        $ret = '';
                    }
                    return $ret;
                },
                'name' => lang('Parent Agent Username'),
            );

        if(!$is_export) {
            array_push($columns, array(
                'dt' => $i++,
                'alias' => 'frozen',
                'select' =>'agency_settlement.frozen',
                'formatter' => function ($d, $row) use ($is_export, $has_parent, $parent_id){
                    if ($is_export) {
                        return '';
                    } else {
                        $id = $row['settlement_id'];
                        $output = '';
                        if ($d == '0'){
                            //$output .= '<a href="/agency_management/do_settlement/' . $id . '" data-toggle="tooltip" title="' . lang('Do Settlement') . '"><span class="glyphicon glyphicon-plus"></span></a> ';
                            //$output .= '<a href="/agency_management/settlement_send_invoice/'.$id.'" data-toggle="tooltip" title="' . lang('Send Invoice') . '" ><span class="glyphicon glyphicon-mail"></span></a> ';
                            if ($row['status'] != 'settled') {
                                $title = lang('Do Settlement');
                                $status = '"'.$row['status'].'"';
                                $output .= "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='$title' onclick='do_settlement($id, $status)'><span class='glyphicon glyphicon-credit-card text-success'></span></a>";
                            // } else if ($has_parent) {
                            //     if($row['agent_id'] != $parent_id) {
                            //         $title = lang('Pay Rolling Comm');
                            //         $output .= "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='$title' onclick='pay_rolling_comm($id)'><span class='fa fa-gift text-warning'></span></a> ";
                            //     } else {
                            //         // $title = lang('Pay Rolling Comm to Players');
                            //         // $output .= "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='$title' onclick='pay_rolling_comm_to_players($id)'><span class='fa fa-random text-warning'></span></a> ";
                            //     }
                            }
                            $title = lang('Send Invoice');
                            $output .= "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='$title' onclick='settlement_send_invoice($id)'><span class='glyphicon glyphicon-envelope text-info'></span></a> ";
                            $title = lang('Freeze this item');
                            $output .= "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='$title' onclick='freeze_settlement($id)'><span class='glyphicon glyphicon-ban-circle text-danger'></span></a> ";
                            return $output;
                        } else { // when frozen only 'unfreeze' is enabled
                            $title = lang('Unfreeze this item');
                            $output .= "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='$title' onclick='unfreeze_settlement($id)'><span class='glyphicon glyphicon-arrow-up text-success'></span></a> ";
                            return $output;
                        }
                    }
                },
                )
            );
        }
        # OUTPUT ###################################################################################
        $result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
        // $this->utils->debug_log('get_settlement result', $result);

        return $result;
    } // get_settlement  }}}2
    // get_settlement_by_agent {{{2
    /**
     *  get settlement record by agent_id
     *
     *  @param  int agent_id
     *  @return array records
     */
    public function get_settlement_by_agent($agent_id, $game_platform_id = null, $game_type_id = null, $period_name = null, $start_date = null, $status = null) {

        $this->db->where('agent_id', $agent_id);

        if ($game_platform_id) {
            $this->db->where('game_platform_id', $game_platform_id);
        }

        if ($game_type_id) {
            $this->db->where('game_type_id', $game_type_id);
        }

        if ($period_name) {
            $this->db->where('settlement_period', $period_name);
        }

        if ($start_date) {
            $this->db->where('settlement_date_from', $start_date);
        }

        if ($status) {
            $this->db->where('status', $status);
        }

        $result = $this->db->get('agency_settlement');

        return $this->getMultipleRowArray($result);

    } // get_settlement_by_agent  }}}2
    // get_unsettled_balance {{{2
    /**
     *  return sum of all Unsettled payable balance for given agent and period_name
     *
     *  @param  int agent_id
     *  @param  string period_name
     *  @return array
     */
    // public function get_unsettled_balance($agent_id, $period_name, $start_date = null) {
    public function get_unsettled_balance($agent_id, $game_platform_id, $game_type_id, $period_name) {

        // $this->db->select_sum('(payable_amt-roll_comm_income)', 'unsettled_balance');
        $this->db->select_sum('payable_amt', 'unsettled_balance');
        $this->db->from($this->table_settlement);
        $this->db->where('agent_id', $agent_id);
        $this->db->where('game_platform_id', $game_platform_id);
        $this->db->where('game_type_id', $game_type_id);
        $this->db->where('status', 'unsettled');
        $this->db->where('settlement_period LIKE ', '%'. $period_name. '%');
        // if ($start_date) {
        //     $this->db->where('settlement_date_from <', $start_date);
        // }

        $query = $this->db->get();
        $result = $query->row();

        return $result->unsettled_balance ? : 0;
    } // get_unsettled_balance  }}}2
    // agency_settlement }}}1

    // agency settings {{{1
    // insert_terms {{{2
    public function insert_terms($agent_id, $name, $value) {
        $data = array(
            'agent_id' => $agent_id,
            'name' => $name,
            'value' => $value,
        );
        $this->db->insert($this->table_setting, $data);
    } // insert_terms }}}2
    // insert_default_terms {{{2
    public function insert_default_terms($name, $value) {
        $data = array(
            'name' => $name,
            'value' => $value,
        );
        $this->db->insert($this->table_setting, $data);
    } // insert_default_terms }}}2
    // update_terms {{{2
    public function update_terms($agnet_id, $name, $value) {
        $this->db->where('agent_id', $agent_id);
        $this->db->where('name', $name);
        $this->db->update($this->table_setting, ['value' => $value]);
    } // update_terms }}}2
    // update_default_terms {{{2
    public function update_default_terms($name, $value) {
        $this->db->where('name', $name);
        $this->db->update($this->table_setting, ['value' => $value]);
    } // update_default_terms }}}2
    // insert_or_update_terms {{{2
    public function insert_or_update_terms($agent_id, $name, $value) {
        $this->db->where('agent_id', $agent_id);
        $this->db->where('name', $name);
        $result = $this->db->get($this->table_setting);

        if ($result->num_rows > 0) {
            $this->db->where('agent_id', $agent_id);
            $this->db->where('name', $name);
            $this->db->update($this->table_setting, ['value' => $value]);
        } else {
            $this->insert_terms($agent_id, $name, $value);
        }
    } // insert_or_update_terms }}}2
    // insert_or_update_default_terms {{{2
    public function insert_or_update_default_terms($name, $value) {
        $this->db->where('name', $name);
        $result = $this->db->get($this->table_setting);

        if ($result->num_rows > 0) {
            $this->update_default_terms($name, $value);
        } else {
            $this->insert_default_terms($name, $value);
        }
    } // insert_or_update_default_terms }}}2
    // get_agent_terms {{{2
    public function get_agent_terms($agent_id) {
        $this->db->where('agent_id', $agent_id);
        $this->db->where('name', 'agent_terms');
        $result = $this->db->get($this->table_setting);

        if ($result->num_rows() > 0) {
            $result = $result->result();
            return $result[0]->value;
        } else {
            return null;
        }
    } // get_agent_terms }}}2
    // get_sub_agent_terms {{{2
    public function get_sub_agent_terms($agent_id) {
        $this->db->where('agent_id', $agent_id);
        $this->db->where('name', 'sub_agent_terms');
        $result = $this->db->get($this->table_setting);

        if ($result->num_rows() > 0) {
            $result = $result->result();
            return $result[0]->value;
        } else {
            return null;
        }
    } // get_sub_agent_terms }}}2
    // get_operator_settings {{{2
    public function get_operator_settings($agent_id) {
        $this->db->where('agent_id', $agent_id);
        $this->db->where('name', 'operator_settings');
        $result = $this->db->get($this->table_setting);

        if ($result->num_rows() > 0) {
            $result = $result->result();
            return $result[0]->value;
        } else {
            return null;
        }
    }
    // get_operator_settings }}}2
    // get_default_agent_terms {{{2
    public function get_default_agent_terms() {
        $this->db->where('name', 'default_agent_terms');
        $result = $this->db->get($this->table_setting);

        if ($result->num_rows() > 0) {
            $result = $result->result();
            return $result[0]->value;
        } else {
            return null;
        }
    } // get_default_agent_terms }}}2
    // get_default_sub_agent_terms {{{2
    public function get_default_sub_agent_terms() {
        $this->db->where('name', 'default_sub_agent_terms');
        $result = $this->db->get($this->table_setting);

        if ($result->num_rows() > 0) {
            $result = $result->result();
            return $result[0]->value;
        } else {
            return null;
        }
    } // get_default_sub_agent_terms }}}2
    // get_default_operator_settings {{{2
    public function get_default_operator_settings() {
        $this->db->where('name', 'default_operator_settings');
        $result = $this->db->get($this->table_setting);

        if ($result->num_rows() > 0) {
            $result = $result->result();
            return $result[0]->value;
        } else {
            return null;
        }
    }
    // get_default_operator_settings }}}2
    // agency settings }}}1

    // agency_tier_comm_patterns {{{1
    // insert_tier_comm_pattern {{{2
    /**
     *  insert data into agency_tier_comm_patterns
     *
     *  @param  array $data
     *  @return void
     */
    public function insert_tier_comm_pattern($data) {
        $pattern_id = $this->insertData($this->table_tier_comm_patterns, $data);
        return $pattern_id;

    } // insert_tier_comm_pattern  }}}2
    // update_tier_comm_pattern {{{2
    /**
     * update pattern_id by pattern_id
     *
     * @param   int
     * @param   array
     */
    public function update_tier_comm_pattern($pattern_id, $data) {
        $this->db->where('pattern_id', $pattern_id);
        $this->db->update($this->table_tier_comm_patterns, $data);
    } // update_tier_comm_pattern }}}2
    // remove_tier_comm_pattern {{{2
    /**
     * remove agency_tier_comm_pattern by pattern_id
     *
     * @param   array
     * @param   int
     */
    public function remove_tier_comm_pattern($pattern_id) {
        $this->db->where('pattern_id', $pattern_id);
        return $this->db->delete($this->table_tier_comm_patterns);
    } // remove_tier_comm_pattern }}}2
    // get_tier_comm_pattern {{{2
    /**
     *  get tier comm pattern details by pattern_id
     *
     *  @param  pattern_id
     *  @return array for agent details
     */
    public function get_tier_comm_pattern($pattern_id) {
        $this->db->from($this->table_tier_comm_patterns)->where('pattern_id', $pattern_id);
        return $this->runOneRowArray();
    } // get_tier_comm_pattern  }}}2
    // get_all_tier_comm_patterns {{{2
    /**
     *  get all tier comm patterns
     *
     *  @return array for agent details
     */
    public function get_all_tier_comm_patterns() {
        $this->db->from($this->table_tier_comm_patterns);
        return $this->runMultipleRowArray();
    } // get_all_tier_comm_patterns  }}}2
    // get_tier_comm_pattern_tiers_by_pattern_id {{{2
    /**
     *  get tier comm pattern details by pattern_id
     *
     *  @param  pattern_id
     *  @return array for agent details
     */
    public function get_tier_comm_pattern_tiers_by_pattern_id($pattern_id) {
        $this->db->from($this->table_tier_comm_pattern_tiers)->where('pattern_id', $pattern_id);
        return $this->runMultipleRowArray();
    } // get_tier_comm_pattern_tiers_by_pattern_id  }}}2
    // insert_tier_comm_pattern_tier {{{2
    /**
     *  insert data into agency_tier_comm_pattern_tiers
     *
     *  @param  array $data
     *  @return void
     */
    public function insert_tier_comm_pattern_tier($data) {
        $this->db->insert($this->table_tier_comm_pattern_tiers, $data);

    } // insert_tier_comm_pattern_tier  }}}2
    // update_tier_comm_pattern_tier {{{2
    /**
     * update id by id
     *
     * @param   int
     * @param   array
     */
    public function update_tier_comm_pattern_tier($id, $data) {
        $this->db->where('id', $id);
        $this->db->update($this->table_tier_comm_pattern_tiers, $data);
    } // update_tier_comm_pattern_tier }}}2
    // remove_tier_comm_pattern_tier {{{2
    /**
     * remove agency_tier_comm_pattern_tier by id
     *
     * @param   int
     */
    public function remove_tier_comm_pattern_tier($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_tier_comm_pattern_tiers);
    } // remove_tier_comm_pattern_tier }}}2
    // remove_tier_comm_pattern_tiers_by_pattern_id {{{2
    /**
     * remove agency_tier_comm_pattern_tiers by pattern_id
     *
     * @param   int
     */
    public function remove_tier_comm_pattern_tiers_by_pattern_id($pattern_id) {
        $this->db->where('pattern_id', $pattern_id);
        return $this->db->delete($this->table_tier_comm_pattern_tiers);
    } // remove_tier_comm_pattern_tiers_by_pattern_id }}}2
    // get_tier_comm_pattern_list {{{2
    /**
     *  get structures from table agency_structures
     *
     *  @param  search request
     *  @return array structure data
     */
    public function get_tier_comm_pattern_list($request) {
        $this->load->library(array('data_tables','permissions'));
        $this->permissions->setPermissions();
        $input = $this->data_tables->extra_search($request);
        //$this->utils->debug_log('input:', $input);
        $table = $this->table_tier_comm_patterns;
        $where = array();
        $values = array();

        if (isset($input['pattern_name']) && $input['pattern_name'] != '') {
            $where[] = "pattern_name LIKE ?";
            $values[] = '%' .$input['pattern_name']. '%';
        }
        if (isset($input['tier_count']) && $input['tier_count'] != '') {
            $where[] = "tier_count = ?";
            $values[] = $input['tier_count'];
        }

        # DEFINE TABLE COLUMNS #####################################################################
        $i = 0;
        $columns = array(
            array(
                'dt' => $i++,
                'select' =>'pattern_name',
            ),
            array(
                'dt' => $i++,
                'select' => 'tier_count',
            ),
            array(
                'dt' => $i++,
                'select' => 'cal_method',
            ),
            array(
                'dt' => $i++,
                'select' => 'rev_share',
            ),
        );

        $columns[]=array(
            'dt' => $i++,
            'select' => 'rolling_comm_basis',
        );
        $columns[]=array(
            'dt' => $i++,
            'select' => 'rolling_comm',
        );

        $columns[]=array(
                'dt' => $i++,
                'select' => 'min_active_player_count',
            );
        $columns[]=array(
                'dt' => $i++,
                'select' => 'min_bets',
            );
        $columns[]=array(
                'dt' => $i++,
                'select' => 'min_trans',
            );
        $columns[]=array(
            'dt' => $i++,
            'select' => 'pattern_id',
            'formatter' => function ($d, $row) {
                $output = '';

                if ($this->permissions->checkPermissions('edit_tier_comm_pattern')) {
                    $title = lang('Edit Tier Comm Pattern');
                    $output .= "<a href='javascript:void(0)' data-toggle='tooltip' title='$title' onclick='edit_pattern($d)'><span class='glyphicon glyphicon-edit'></span></a> ";
                    $title = lang('Delete this pattern');
                    $output .= "<a href='javascript:void(0)' data-toggle='tooltip' title='$title' onclick='remove_pattern($d)'><span class='fa fa-trash'></span></a> ";
                }
                return $output;
            },
        );

        # OUTPUT ###################################################################################
        $result = $this->data_tables->get_data($request, $columns, $table, $where, $values);
        //$this->utils->debug_log($result);

        return $result;
    } // get_tier_comm_pattern_list  }}}2
    // agency_tier_comm_patterns }}}1

    // player rolling comm {{{1
    // insert_player_rolling_comm {{{2
    /**
     *  insert data into agency_player_rolling_comm
     *
     *  @param  array data for a new player_rolling_comm
     *  @return player_rolling_comm_id
     */
    public function insert_player_rolling_comm($data) {
        $player_rolling_comm_id = $this->insertData($this->table_player_rolling_comm, $data);
        return $player_rolling_comm_id;
    } // insert_player_rolling_comm  }}}2
    // update_player_rolling_comm {{{2
    /**
     * update player_rolling_comm by player_rolling_comm_id
     *
     * @param   array
     * @param   int
     */
    public function update_player_rolling_comm($player_rolling_comm_id, $data) {
        $this->db->where('id', $player_rolling_comm_id);
        return $this->db->update($this->table_player_rolling_comm, $data);
    } // update_player_rolling_comm }}}2
    // get_player_rolling_comm_by_settlement {{{2
    /**
     *  get player_rolling_comm details by player_rolling_comm_id
     *
     *  @param  player_rolling_comm_id
     *  @return array for player_rolling_comm details
     */
    public function get_player_rolling_comm_by_settlement($settlement_id, $player_id = false) {
        $this->db->from($this->table_player_rolling_comm)->where('settlement_id', $settlement_id);
        if ($player_id) {
            $this->db->where('player_id', $player_id);
        }
        return $this->runMultipleRowArray();
    } // get_player_rolling_comm_by_settlement  }}}2
    // get_player_rolling_comm_info {{{2
   //  /**
   //   *  return player rolling comm info
   //   *
   //   *  @param
   //   *  @return
   //   */
   //  public function get_player_rolling_comm_info($logged_agent_id, $request, $is_export=false) {
   //      $this->load->library(array('data_tables'));

   //      $input = $this->data_tables->extra_search($request);
   //      // $this->utils->debug_log('get_settlement request', $request);
   //      $table = $this->table_player_rolling_comm;
   //      $where = array();
   //      $values = array();
   //      $joins = array();
   //      $joins['player'] = 'player.playerId = agency_player_rolling_comm.player_id';
   //      // $joins['agency_settlement'] = 'agency_settlement.settlement_id = agency_player_rolling_comm.settlement_id';

   //      // $settlement_id = $input['settlement_id'];
   //      // $where[] = "agency_settlement.settlement_id = ?";
   //      // $values[] = $settlement_id;

   //      if(isset($input['status']) && !empty($input['status'])){
   //          $where[] = "agency_player_rolling_comm.payment_status = ?";
   //          $values[] = $input['status'];
   //      }

   //      if(isset($input['search_on_date']) && $input['search_on_date']=='true' ){
   //          $where[] = "agency_player_rolling_comm.start_at >= ? and agency_player_rolling_comm.start_at <= ?";
   //          $values[] = $input['date_from'];
   //          $values[] = $input['date_to'];
   //      }

   //      if(isset($input['player_username']) && !empty($input['player_username'])){
   //          $where[] = "player.username like ?";
   //          $values[] = '%'.$input['player_username'].'%';
   //      }

   //      $search_agent_id_arr=[$logged_agent_id];

   //      if(isset($input['sub_agent_username']) && !empty($input['sub_agent_username'])){
   //          $subagent=$this->get_agent_by_name($input['sub_agent_username']);
   //          if(!empty($subagent)){
   //              $logged_agent_id=$subagent['agent_id'];
   //              $search_agent_id_arr=[$logged_agent_id];
   //          }
   //      }

   //      if(isset($input['include_all_downlines']) && $input['include_all_downlines']=='true'){
   //          //search sub agent ids by logged_agent_id
   //          $search_agent_id_arr=$this->get_all_downline_arr($logged_agent_id);
   //          $search_agent_id_arr[]=$logged_agent_id;
   //          $search_agent_id_arr=array_unique($search_agent_id_arr);

   //      }

   //      $where[] = "agency_player_rolling_comm.agent_id in (".implode(',',$search_agent_id_arr).")";
   //      // $values[] = '';

   //      $i = 0;
   //      $columns = array(
   //          array(
   //              'alias' => 'player_id',
   //              'select' =>'player.playerId',
   //          ),
   //          array(
   //              'alias' => 'agency_player_rolling_comm_id',
   //              'select' =>'agency_player_rolling_comm.id',
   //          ),
   //          array(
            //  'dt' => $i++,
            //  'alias' => 'username',
            //  'select' => 'player.username',
            //  'formatter' => function ($d, $row) {
   //                  $player_id = $row['player_id'];
            //      $ret = "<i class='fa fa-user' ></i> ";
            //      $title = lang('Show Player Info');
            //      $ret .= "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='$title' onclick='show_player_info($player_id)'>$d</a> ";
            //      return $ret;
            //  },
            // ),
   //          array(
   //              'dt' => $i++,
   //              'alias' => 'payment_status',
   //              'select' => 'agency_player_rolling_comm.payment_status',
   //              'name' => lang('Status'),
   //          ),
   //          array(
   //              'dt' => $i++,
   //              'alias' => 'start_at',
   //              'select' =>'agency_player_rolling_comm.start_at',
   //              'name' => lang('Date From'),
   //          ),
   //          array(
   //              'dt' => $i++,
   //              'alias' => 'end_at',
   //              'select' =>'agency_player_rolling_comm.end_at',
   //              'name' => lang('Date To'),
   //          ),
   //          array(
   //              'dt' => $i++,
   //              'alias' => 'real_bets',
   //              'select' => 'agency_player_rolling_comm.real_bets',
   //              'name' => lang('Real Bet'),
   //          ),
   //          array(
   //              'dt' => $i++,
   //              'alias' => 'total_bets',
   //              'select' => 'agency_player_rolling_comm.total_bets',
   //              'name' => lang('Total Bet'),
   //          ),
   //          array(
   //              'dt' => $i++,
   //              'alias' => 'rolling_rate',
   //              'select' => 'agency_player_rolling_comm.rolling_rate',
   //              'name' => lang('Rolling Rate'),
   //          ),
   //          array(
   //              'dt' => $i++,
   //              'alias' => 'roll_comm_amt',
   //              'select' => 'agency_player_rolling_comm.rolling_comm_amt',
   //              'name' => lang('Rolling Comm Amt'),
   //          ),
   //          array(
   //              'dt' => $i++,
   //              'alias' => 'notes',
   //              'select' => 'agency_player_rolling_comm.notes',
   //              'name' => lang('Notes'),
   //          ),
   //          array(
   //              'dt' => $i++,
   //              'alias' => 'action',
   //              'select' => '""',
   //              'name' => lang('Action'),
   //              'formatter' => function ($d, $row){
   //                  //by status
   //                  $status=$row['payment_status'];
   //                  $id=$row['agency_player_rolling_comm_id'];
   //                  $amount=$row['roll_comm_amt'];
   //                  $username=$row['username'];
   //                  $show_settle=false;
   //                  $show_pending=false;
   //                  if($status=='pending'){
   //                      $show_settle=true;
   //                  }elseif($status=='settled'){
   //                      //none
   //                  }elseif($status=='current'){
   //                      $show_settle=true;
   //                      $show_pending=true;
   //                  }
   //                  $str='';
   //                  if($show_settle){
   //                      $str.=' <input type="button" class="settle btn btn-sm btn-primary" '
   //                          .'onclick="settle_rolling('.$id.', \''.$amount.'\', \''.$username.'\')" value="'.lang('Settle').'">';
   //                  }
   //                  if($show_pending){
   //                      $str.=' <input type="button" class="pending btn btn-sm btn-success" '
   //                          .'onclick="pending_rolling('.$id.', \''.$amount.'\', \''.$username.'\')" value="'.lang('Pending').'">';
   //                  }

   //                  return $str;
   //              }
   //          ),
   //      );

   //      $external_order=[['column'=>2, 'dir'=>'desc']];
   //      //$request, $columns, $table, $form_search = array(), $values = array(), $joins = array(), $group_by = array(), $having = array(), $distinct = true, $external_order
   //      $result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, null, null, true, $external_order);
   //      // $this->utils->debug_log('GET_PLAYER_ROLLING_COMM_INFO result', $result);

   //      return $result;
   //  } // get_player_rolling_comm_info  }}}2
    // player rolling comm }}}1

    public function dec_credit($agent_id, $amount) {
        $this->db->where('agent_id', $agent_id)->set('available_credit','available_credit-'.$amount,false);
        return $this->runAnyUpdate('agency_agents');
    }

    public function inc_credit($agent_id, $amount) {
        $this->db->where('agent_id', $agent_id)->set('available_credit','available_credit+'.$amount,false);
        return $this->runAnyUpdate('agency_agents');
    }

    public function get_credit_details($agent_id) {
        $this->db->from('agency_agents')->where('agent_id', $agent_id);
        $row = $this->runOneRow();
        $frozen = 0;
        $main = $row->available_credit;

        return array('main_wallet' => $main, 'frozen' => $frozen, 'total_balance' => $main + $frozen);
    }

    public function get_downline($id = NULL) {

        $this->db->select('agent_id');
        $this->db->select('agent_name');
        $this->db->select('parent_id');
        $this->db->from('agency_agents');
        $query = $this->db->get();

        $agents = array_column($query->result_array(), null, 'agent_id');

        foreach ($agents as $agent_id => &$agent) {
            $parent_id = $agent['parent_id'];
            if ($parent_id) {
                $agents[$parent_id]['sub_agents'][] = &$agent;
            }
        }

        return isset($agents[$id]) ? $agents[$id] : array_values(array_filter($agents, function(&$agent) {
            $result = $agent['parent_id'] == 0;
            unset($agent['parent_id']);
            return $result;
        }));

    }

    public function get_all_downline_arr($id = null) {
        $hierarchy = $this->get_downline($id);
        $return = array();
        array_walk_recursive($hierarchy, function ($val, $key) use (&$return) {
            if ($key == 'agent_id') {
                $return[] = $val;
            }
        });
        return $return;
    }

    public function get_all_downline_player_ids($agent_id) {
        $agent_ids = $this->get_all_downline_arr($agent_id);
        $this->load->model(array('player_model'));
        return $this->player_model->get_player_ids_by_agent_ids($agent_ids);
    }

    public function is_upline($agent_id, $parent_id) {

        $this->db->select('agent_id');
        $this->db->select('parent_id');
        $this->db->from('agency_agents');
        $query = $this->db->get();

        $agents = array_column($query->result_array(), null, 'agent_id');

        do {

            if (isset($agents[$agent_id])) {
                $agent_id = $agents[$agent_id]['parent_id'];
            } else return FALSE;

            if ($agent_id == $parent_id) {
                return TRUE;
            }

        } while (TRUE);
    }



    public function get_upline($agent_id, $include = false) {

        $this->db->select('agent_id');
        $this->db->select('parent_id');
        $this->db->from('agency_agents');
        $query = $this->db->get();

        $agents = array_column($query->result_array(), null, 'agent_id');

        $upline = $include ? array($agent_id) : array();

        while ($agent_id = $agents[$agent_id]['parent_id']) {
            $upline[] = $agent_id;
        }

        return $upline;

    }

    public function getBalanceDetails($agent_id){

        $agent=$this->get_agent_by_id($agent_id);

        // $this->db->from('agency_agents')->where('affiliateId', $agent_id);
        // $row = $this->runOneRow();
        // $frozen = $row->frozen;
        $main = $agent['available_credit'];
        $limit= $agent['credit_limit'];

        return array('main_wallet' => $main, 'limit'=>$limit, 'total_balance' => $main);

    }

 //    public function getRootAgencyByAgentId($agent_id){
	// 	$parent_id = $agent_id;

	// 	while($parent_id!=0){
	// 		$agent_id = $parent_id;
	// 		$parent_id = $this->getParentAgencyByAgentId($agent_id);
	// 	}

	// 	return $agent_id;
	// }

	// public function getParentAgencyByAgentId($agent_id){
 //    	$this->db->select('parent_id');
 //    	$this->db->from($this->table_agent);
 //    	$this->db->where('agent_id', $agent_id);

	// 	$parent_id = $this->runOneRowOneField('parent_id');

	// 	return $parent_id;
	// }

    public function generate_current_player_rolling_comm($agent_id, $all_downlines=false){

        $success=true;

        //create current player by agent
        // $agent_id = $agent_details['agent_id'];
        // $settlement_id = $settlement['settlement_id'];

        // $all_downlines = false;
        //$all_player_ids = $this->agency_library->get_all_player_ids_under_agent($agent_id, $all_downlines);

        $this->load->model(['game_logs']);

        $all_player_ids = $this->get_all_downline_player_ids($agent_id);
        $this->utils->debug_log('ALL_PLAYER_IDS', $all_player_ids);

        if(empty($all_player_ids)){
            //not found any player
            return $success;
        }

        // if($all_downlines){
        //     $all_agent_ids=$this->get_all_downline_arr($agent_id);
        //     $all_agent_ids[]=$agent_id;
        //     $all_agent_ids=array_unique($all_agent_ids);
        // }else{
        //     $all_agent_ids=[$agent_id];
        // }

        // $this->utils->debug_log('all_agent_ids', $all_player_ids);

        $this->db->select('rolling_comm, playerId, agent_id')->from('player')
            ->where_in('playerId', $all_player_ids);
        $rows=$this->runMultipleRowArray();
        $rolling_comm_map=[];
        foreach ($rows as $row) {
            $rolling_comm_map[$row['playerId']]=$row;
        }

        //find all pending and settled records
        $this->db->select('player_id, max(concat(end_at, "|", id )) as max_end_at_with_id ', false)
            ->from('agency_player_rolling_comm')
            ->where_in('player_id', $all_player_ids)
            ->where_in('payment_status', [self::ROLLING_STATUS_SETTLED, self::ROLLING_STATUS_PENDING])
            ->group_by('player_id');
        $rows=$this->runMultipleRowArray();

        //set start date and rolling comm
        $player_date_map=[];
        if(!empty($rows)){
            foreach ($rows as $row) {
                $max_end_at_with_id=$row['max_end_at_with_id'];
                list($end_at, $rolling_id)=explode('|', $max_end_at_with_id);
                $rolling_arr[]=$rolling_id;
                $start_at=new DateTime($end_at);
                $start_at->modify('+1 second');
                $player_id=$row['player_id'];
                $player_date_map[$player_id]=[
                    'start_at'=>$this->utils->formatDateTimeForMysql($start_at),
                    'rolling_comm'=>$rolling_comm_map[$player_id]['rolling_comm'],
                    'agent_id'=>$rolling_comm_map[$player_id]['agent_id'],
                ];
            }
        }
        $agency_max_start_rolling=new DateTime($this->utils->getConfig('agency_max_start_rolling'));
        $agency_max_start_rolling=$this->utils->formatDateTimeForMysql($agency_max_start_rolling);
        foreach ($all_player_ids as $player_id) {
            if(!isset($player_date_map[$player_id])){
                $player_date_map[$player_id]=[
                    'start_at'=>$agency_max_start_rolling,
                    'rolling_comm'=>$rolling_comm_map[$player_id]['rolling_comm'],
                    'agent_id'=>$rolling_comm_map[$player_id]['agent_id'],
                ];
            }
        }

        $this->utils->debug_log('player_date_map', $player_date_map);

        $player_cnt = 0;
        $rolling_comm_amt = 0;

        $now=$this->utils->getNowForMysql();

        foreach ($player_date_map as $player_id => $rollingInfo) {
            $start_at=$rollingInfo['start_at'];
            $rolling_comm=$rollingInfo['rolling_comm'];

            $bets = $this->game_logs->get_player_bet_info($player_id, $start_at, $now);
            $total_bets=0;
            $total_real_bets=0;
            $amt=0;

            // $this->utils->debug_log($bets);

            if(!empty($bets)){
                //by setting
                $total_real_bets=$bets['total_real_bets'];
                $total_bets=$bets['total_bets'];
                $amt = $total_bets * $rolling_comm / 100.0;

                $rolling_comm_amt+=$amt;
            }
            $player_cnt++;

            //create current
            //save agent id of player , not logged agent
            $this->syncCurrentPlayerRolling($player_id, $rollingInfo['agent_id'], $total_bets, $total_real_bets,
                $amt, $rolling_comm, $start_at, $now);
        }

        $this->utils->debug_log('player_cnt', $player_cnt, 'rolling_comm_amt', $rolling_comm_amt);

        // foreach($all_player_ids as $player_id) {

        //     $recs = $this->get_player_rolling_comm_by_settlement($settlement_id, $player_id);
        //     if (!empty($recs) && count($recs) > 0) {
        //         if ($recs[0]['payment_status'] != 'paid') {
        //             $player_cnt++;
        //             $rolling_comm_amt += $recs[0]['rolling_comm_amt'];
        //         }
        //         continue;
        //     }
        //     $player_details = $this->player_model->getPlayerArrayById($player_id);
        //     $rolling_comm = $player_details['rolling_comm'];
        //     if ($rolling_comm == 0) {
        //         $rolling_comm = $agent_details['player_rolling_comm'];
        //     }
        //     // fetch game data from game_logs
        //     $game_info = $this->agency_library->get_player_game_info($player_id, $period);
        //     $this->utils->debug_log('player game_info', $game_info);

        //     $total_bets = $game_info['total_bets'];
        //     if ($total_bets == 0) {
        //         continue;
        //     }
        //     $amt = $total_bets * $rolling_comm / 100.0;
        //     $data = array(
        //         'player_id' => $player_id,
        //         'settlement_id' => $settlement_id,
        //         'total_bets' => $total_bets,
        //         'rolling_comm_amt' => $amt,
        //         'payment_status' => 'not_paid',
        //     );
        //     $this->insert_player_rolling_comm($data);
        //     $player_cnt++;
        //     $rolling_comm_amt += $amt;
        // }

        return $success;
    }

    public function syncCurrentPlayerRolling($player_id, $agent_id, $total_bets, $total_real_bets,
            $rolling_comm_amt, $rolling_rate, $start_at, $end_at){

        $this->utils->debug_log('syncCurrentPlayerRolling', $player_id, $agent_id, $total_bets,
            $rolling_comm_amt, $rolling_rate, $start_at, $end_at);

        //check first, same status , same player
        $this->db->select('id')->from('agency_player_rolling_comm')
            ->where('player_id', $player_id)->where('payment_status', self::ROLLING_STATUS_CURRENT);
        $id=$this->runOneRowOneField('id');

        $data = array(
            'player_id' => $player_id,
            'agent_id' => $agent_id,
            'total_bets' => $total_bets,
            'real_bets' => $total_real_bets,
            'rolling_comm_amt' => $rolling_comm_amt,
            'rolling_rate'=>$rolling_rate,
            'payment_status' => self::ROLLING_STATUS_CURRENT,
            'start_at'=>$start_at,
            'end_at'=>$end_at,
            'updated_at'=>$this->utils->getNowForMysql(),
        );

        if(empty($id)){

            return $this->insertData('agency_player_rolling_comm', $data);

        }else{

            $this->db->where('id', $id)->set($data);
            return $this->runAnyUpdate('agency_player_rolling_comm');

        }

        // $this->insert_player_rolling_comm($data);

    }

    public function get_player_id_by_rolling_id($rolling_id){

        if(!empty($rolling_id)){
            $this->db->select('player_id')->from('agency_player_rolling_comm')
                ->where('id', $rolling_id);
            return $this->runOneRowOneField('player_id');
        }

        return null;

    }

    public function get_rolling_by_id($rolling_id){

        if(!empty($rolling_id)){
            $this->db->from('agency_player_rolling_comm')
                ->where('id', $rolling_id);
            return $this->runOneRowArray();
        }

        return null;
    }

    public function settle_rolling($agentId, $rolling_id, $notes, &$message=null){
        $success=false;
        $rollingInfo=$this->get_rolling_by_id($rolling_id);
        if(!empty($rollingInfo)){
            if($rollingInfo['rolling_comm_amt']>0){
                if(in_array($rollingInfo['payment_status'], [self::ROLLING_STATUS_CURRENT, self::ROLLING_STATUS_PENDING])){
                    $playerId=$rollingInfo['player_id'];
                    $amount=$rollingInfo['rolling_comm_amt'];
                    $this->load->model('transactions');
                    //create transaction
                    $trans_id=$this->transactions->createRollingTransactionForAgent($rollingInfo['agent_id'],
                        $playerId, $amount, $notes);

                    $success=!!$trans_id;

                    if($success){

                        $success=!!$this->transactions->createAgentToPlayerTransaction(
                            $agentId, $playerId, $amount, 'on player rolling');

                        if($success){
                            //update status
                            $this->db->set('payment_status', self::ROLLING_STATUS_SETTLED)
                                ->set('transaction_id', $trans_id)
                                ->where('id', $rolling_id);
                            $this->runAnyUpdate('agency_player_rolling_comm');

                            $this->appendRollingNotes($rolling_id, $notes);
                            $success=true;
                        }else{
                            $this->utils->error_log('transfer from agent for '.$rolling_id.' failed');
                            $message='transfer from agent for '.$rolling_id.' failed';
                        }

                    }else{
                        $this->utils->error_log('create transaction for '.$rolling_id.' failed');
                        $message='create transaction for '.$rolling_id.' failed';
                    }
                }else{
                    $this->utils->error_log('wrong status of '.$rolling_id);
                    $message='wrong status of '.$rolling_id;
                }
            }else{
                $this->utils->error_log('Amount should be >0', $rolling_id);
                $message='Amount should be >0';
            }
        }else{
            $this->utils->error_log('lost rolling id:'.$rolling_id);
            $message='lost rolling id:'.$rolling_id;
        }

        return $success;
    }

    public function pending_rolling($agentId, $rolling_id, $notes, &$message=null){
        $success=false;
        $rollingInfo=$this->get_rolling_by_id($rolling_id);
        if(!empty($rollingInfo)){
            if(in_array($rollingInfo['payment_status'], [self::ROLLING_STATUS_CURRENT])){
                $this->load->model('transactions');
                //update status
                $this->db->set('payment_status', self::ROLLING_STATUS_PENDING)
                    ->where('id', $rolling_id);
                $this->runAnyUpdate('agency_player_rolling_comm');

                $this->appendRollingNotes($rolling_id, $notes);
                $success=true;
            }else{
                $this->utils->error_log('wrong status of '.$rolling_id);
                $message='wrong status of '.$rolling_id;
            }
        }else{
            $this->utils->error_log('lost rolling id:'.$rolling_id);
            $message='lost rolling id:'.$rolling_id;
        }

        return $success;

    }

    public function appendRollingNotes($id, $notes) {
        $sql = "update agency_player_rolling_comm set notes=concat(ifnull(notes,''),' | ',?) where id=?";
        return $this->runRawUpdateInsertSQL($sql, array($notes, $id));
    }

    public function isEnabledRollingComm($agent_id){

        $this->db->select('show_rolling_commission')->from('agency_agents')->where('agent_id', $agent_id);

        return !!$this->runOneRowOneField('show_rolling_commission');
    }

    public function get_agent_id_list(){
        $this->db->select('agent_id')->from('agency_agents');

        return $this->runMultipleRowArray();
    }

    public function get_agent_id_list_order_by_level(){
        $this->db->select('agent_id')->from('agency_agents')
            ->order_by('agent_level', 'desc');

        return $this->runMultipleRowArray();
    }

    public function getFirstTopLevelAgent(){
        $this->db
            ->select('agent_id, agent_name')
            ->from('agency_agents')
            ->where('status', 'active')
            ->order_by('agent_level', 'asc');

        return $this->runOneRowArray();
    }

    public function getFirstAgent(){
        $this->db->from('agency_agents')->order_by('agent_id')->limit(1);

        return $this->runOneRowArray();
    }

    public function getAllActiveAgents(){
        $this->db
            ->select('agent_id, agent_name')
            ->from('agency_agents')
            ->where('status', 'active')
            ->order_by('agent_level', 'desc');

        return $this->runOneRowArray();
    }

    public function createAgentWithMerchant($agent_name, $merchant_name,$password,$credit_limit,$available_credit,
                                      $agent_level=0, $parent_id=0, $rev_share=1, $rolling_comm=0, $settlement_period='Weekly', $start_day='',
                                      $currency='CNY', $live_mode=self::DB_FALSE, $player_vip_groups=[], $player_vip_levels=[]){

//        $this->db->select('id')->from('merchants')->where('merchant_code', $agent_name);
//        if($this->runExistsResult()){
//            return false;
//        }
        $this->db->select('agent_id')->from('agency_agents')->where('agent_name', $agent_name);
        if($this->runExistsResult()){
            return false;
        }

        $this->load->library(['salt']);

        $status=self::AGENT_STATUS_ACTIVE;
        $rolling_comm_basis=self::AGENT_ROLLING_MODE_TOTAL_BETS_EXCEPT_TIE;

        $can_have_sub_agent=self::DB_TRUE;
        $can_have_players=self::DB_TRUE;
        $show_bet_limit_template=self::DB_TRUE;
        $show_rolling_commission=self::DB_TRUE;

        $today=$this->utils->getNowForMysql();

        $data = array(
            'agent_name' => $agent_name,
            'merchant_name' => $merchant_name,
            'password' => $this->salt->encrypt($password, $this->getDeskeyOG()),
            'currency' => $currency,
            'credit_limit' => $credit_limit,
            'available_credit' => $available_credit,
            'status' => $status,
            'active' => $status == 'active'? self::DB_TRUE : self::DB_FALSE,
            'rev_share' => $rev_share,
            'rolling_comm' => $rolling_comm,
            'rolling_comm_basis' => $rolling_comm_basis,
            'total_bets_except' => '',
            'agent_level' => $agent_level,
            'agent_level_name' => '',
            'can_have_sub_agent' => $can_have_sub_agent,
            'can_have_players' => $can_have_players,
            'show_bet_limit_template' => $show_bet_limit_template,
            'show_rolling_commission' => $show_rolling_commission,

            'settlement_period' => $settlement_period,
            'settlement_start_day' => $start_day,
            'created_on' => $today,
            'updated_on' => $today,
            'parent_id' => $parent_id,
            'vip_groups' => implode(',', $player_vip_groups),
            'vip_levels' => implode(',', $player_vip_levels),

            'live_mode'=>$live_mode,
            'live_sign_key'=>random_string('md5'),
            'staging_sign_key'=>random_string('md5'),
            'live_secure_key'=>random_string('md5'),
            'staging_secure_key'=>random_string('md5'),
        );
//        $this->utils->debug_log($data);

        $agent_id = $this->add_agent($data);

//        $this->createMerchant($agent_name, $agent_name, $password, $agent_id);

        return $agent_id;
    }

    // getSettlementAgentTotalPlayerRolling {{{2
    /**
     *  get total player rolling amount for a given agent
     *
     *  @param  INT $agent_id
     *  @return array
     */
    public function getSettlementAgentTotalPlayerRolling($agent_id, $date_from, $date_to) {
        $this->db->select_sum('agency_wl_settlement.player_commission');
        $this->db->where('agency_wl_settlement.type', 'player');
        $this->db->where('agency_wl_settlement.agent_id', $agent_id);

        $this->db->where('agency_wl_settlement.settlement_date_to <=',$date_to);
        $this->db->where('agency_wl_settlement.settlement_date_from >=', $date_from);

        $query = $this->db->get('agency_wl_settlement');
        $rows = $query->result_array();

        return $rows;
    } // getSettlementAgentTotalPlayerRolling  }}}2
    public function getSettlementRawData($user_id, $date_from, $date_to, $type = 'player', $game_info = true){
        $this->db->select('agency_daily_player_settlement.agent_id');
        if ($type == 'player') {
            $this->db->select('agency_daily_player_settlement.player_id');
        }
        if($game_info){
            $this->db->select('agency_daily_player_settlement.game_platform_id');
            $this->db->select('agency_daily_player_settlement.game_type_id');
        }
        $this->db->select_sum('agency_daily_player_settlement.bets');
        $this->db->select_sum('agency_daily_player_settlement.real_bets');
        $this->db->select_sum('agency_daily_player_settlement.tie_bets');
        $this->db->select_sum('agency_daily_player_settlement.lost_bets');
        $this->db->select_sum('agency_daily_player_settlement.winning_bets');
        $this->db->select_sum('agency_daily_player_settlement.bets_except_tie');
        $this->db->select_sum('agency_daily_player_settlement.wins');
        $this->db->select_sum('agency_daily_player_settlement.net_gaming');
        $this->db->select_sum('agency_daily_player_settlement.result_amount');

        if ($type == 'player') {
            $this->db->where('agency_daily_player_settlement.player_id', $user_id);
        } else {
            $this->db->where('agency_daily_player_settlement.agent_id', $user_id);
        }

        $this->db->where('agency_daily_player_settlement.settlement_date >=', $date_from);
        $this->db->where('agency_daily_player_settlement.settlement_date <=', $date_to);

        $query = $this->db->get('agency_daily_player_settlement');

        $last_query = $this->db->last_query();

        $this->utils->debug_log('getSettlementAgentTotalPlayerRolling sql_query: ' . $last_query);
        $this->utils->debug_log('getSettlementAgentTotalPlayerRolling : type, user_id ' . $type, $user_id);
        $rows = $query->result_array();

        return $rows;
    }

    public function getAllDailySettlements($agent_id, $date_from, $date_to) {
        $this->db->select('agency_daily_player_settlement.*');
        $this->db->where('agency_daily_player_settlement.settlement_date >=', $date_from);
        $this->db->where('agency_daily_player_settlement.settlement_date <=', $date_to);
        if(!empty($agent_id)){
            $this->db->where('agency_daily_player_settlement.agent_id', $agent_id);
        }
        $query = $this->db->get('agency_daily_player_settlement');
        return $query->result_array();
    }

    public function getPlayerDailySettlement($agent_id, $agent_username, $date_from, $date_to){
        $this->db->select('agency_daily_player_settlement.rev_share');
        $this->db->select('agency_daily_player_settlement.rolling_comm');
        $this->db->select('agency_daily_player_settlement.rolling_comm_basis');
        $this->db->select_sum('agency_daily_player_settlement.tie_bets');
        $this->db->select_sum('agency_daily_player_settlement.lost_bets');
        $this->db->select_sum('agency_daily_player_settlement.bets');
        $this->db->select_sum('agency_daily_player_settlement.real_bets');
        $this->db->select_sum('agency_daily_player_settlement.bets_except_tie');
        $this->db->select_sum('agency_daily_player_settlement.wins');
        $this->db->select_sum('agency_daily_player_settlement.bonuses_total', 'bonuses');
        $this->db->select_sum('agency_daily_player_settlement.rebates_total', 'rebates');
        $this->db->select_sum('agency_daily_player_settlement.transactions_total', 'transactions');
        $this->db->select_sum('agency_daily_player_settlement.net_gaming');
        $this->db->select_sum('agency_daily_player_settlement.earnings');
        $this->db->select_sum('agency_daily_player_settlement.bets_display');
        $this->db->select_sum('agency_daily_player_settlement.bets_except_tie_display');

        $this->db->select('agency_daily_player_settlement.agent_id');
        $this->db->select('agency_daily_player_settlement.player_id');
        $this->db->select('player.username as player_username');
        $this->db->select('agency_agents.agent_name as agent_username');
        $this->db->select_sum('agency_daily_player_settlement.result_amount');
        $this->db->select_sum('agency_daily_player_settlement.platform_fee');
        $this->db->select_sum('agency_daily_player_settlement.player_commission');
        $this->db->select_sum('agency_daily_player_settlement.rev_share_amt');
        $this->db->select_sum('(CASE WHEN agency_daily_player_settlement.agent_rolling_paid = 0 THEN agency_daily_player_settlement.agent_commission ELSE 0 END)', 'agent_commission');
        $this->db->select_sum('agency_daily_player_settlement.roll_comm_income');
        $this->db->select_sum('agency_daily_player_settlement.rev_share_amt');

        $this->db->join('player','player.playerId = agency_daily_player_settlement.player_id','left');
        $this->db->join('agency_agents','agency_agents.agent_id = player.agent_id','left');
        $this->db->where('agency_daily_player_settlement.agent_id', $agent_id);

        if ($agent_username) {
            $this->db->where('agency_agents.agent_name', $agent_username);
        }

        $this->db->where('agency_daily_player_settlement.settlement_date >=', $date_from);
        $this->db->where('agency_daily_player_settlement.settlement_date <=', $date_to);

        $this->db->group_by('agency_daily_player_settlement.player_id');
        $this->db->order_by('player_username');
        // $this->db->group_by('agent_username');
        $query = $this->db->get('agency_daily_player_settlement');

        $last_query = $this->db->last_query();

        // $this->utils->debug_log('getPlayerDailySettlement sql_query: ' . $last_query);

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
//                $row['player_username'] = "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='".lang('Show Player Info')."' onclick=\"show_player_game_history('".$row['player_username']."','".date("Y-m-d 00:00:00", strtotime($date_from))."','".date("Y-m-d 23:59:59", strtotime($date_to))."')\">".$row['player_username']."</a> ";
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

    /**
     * @param $agent_id
     * @param $agent_username
     * @param $date_from
     * @param $date_to
     * @param string $type(parent|current)
     * @return array
     */
    public function getAgentDailySettlement($agent_id, $agent_username, $date_from, $date_to, $type = 'parent', $calculate_summary = true){

        $deduct_agent_rolling = $this->utils->isEnabledFeature('deduct_agent_rolling_from_revenue_share');

        $this->db->select('agency_daily_player_settlement.agent_id');
        $this->db->select('agency_agents.agent_name as agent_username');
        $this->db->select_sum('agency_daily_player_settlement.winning_bets');
        $this->db->select_sum('agency_daily_player_settlement.real_bets');
        $this->db->select_sum('agency_daily_player_settlement.bets');
        $this->db->select_sum('agency_daily_player_settlement.tie_bets');
        $this->db->select_sum('agency_daily_player_settlement.result_amount');
        $this->db->select_sum('agency_daily_player_settlement.platform_fee');
        $this->db->select_sum('agency_daily_player_settlement.lost_bets');
        $this->db->select_sum('agency_daily_player_settlement.bets_except_tie');
        $this->db->select_sum('agency_daily_player_settlement.player_commission');
        $this->db->select_sum('agency_daily_player_settlement.roll_comm_income');
        $this->db->select_sum('agency_daily_player_settlement.agent_commission', 'agent_commission');
        $this->db->select_sum('agency_daily_player_settlement.wins');
        $this->db->select_sum('agency_daily_player_settlement.bonuses');
        $this->db->select_sum('agency_daily_player_settlement.admin');
        $this->db->select_sum('agency_daily_player_settlement.transactions');
        $this->db->select_sum('agency_daily_player_settlement.deposit_fee');
        $this->db->select_sum('agency_daily_player_settlement.withdraw_fee');
        $this->db->select_sum('agency_daily_player_settlement.deposit_comm');
        $this->db->select_sum('agency_daily_player_settlement.net_gaming');
        $this->db->select_sum('agency_daily_player_settlement.rebates');
        $this->db->select_sum('agency_daily_player_settlement.rev_share_amt');
        $this->db->select_sum('agency_daily_player_settlement.bonuses_total');
        $this->db->select_sum('agency_daily_player_settlement.rebates_total');
        $this->db->select_sum('agency_daily_player_settlement.transactions_total');
        $this->db->select_sum('agency_daily_player_settlement.deposit_fee_total');
        $this->db->select_sum('agency_daily_player_settlement.withdraw_fee_total');
        $this->db->select_sum('agency_daily_player_settlement.deposit_comm_total');
        $this->db->select_sum('agency_daily_player_settlement.admin_total');
        $this->db->select_sum('agency_daily_player_settlement.earnings');
        $this->db->select_sum('agency_daily_player_settlement.bets_display');
        $this->db->select_sum('agency_daily_player_settlement.bets_except_tie_display');

        $this->db->from('agency_daily_player_settlement');
        $this->db->join('agency_agents','agency_agents.agent_id = agency_daily_player_settlement.agent_id','left');

        if ($type == 'parent') {
            $this->db->where('agency_agents.parent_id', $agent_id);
        } else {
            $this->db->where('agency_agents.agent_id', $agent_id);
        }

        if ($agent_username) {
            $this->db->where('agency_agents.agent_name', $agent_username);
        }

        $this->db->where('agency_daily_player_settlement.settlement_date >=', $date_from);
        $this->db->where('agency_daily_player_settlement.settlement_date <=', $date_to);
        $this->db->group_by('agency_agents.agent_id');

        $query = $this->db->get();

        $rows = $query->result_array();

        $summary['bets']              = 0;
        $summary['real_bets']         = 0;
        $summary['bonuses']           = 0;
        $summary['rebates']           = 0;
        $summary['transactions']      = 0;
        $summary['deposit_fee']       = 0;
        $summary['withdraw_fee']      = 0;
        $summary['deposit_comm']      = 0;
        $summary['admin']             = 0;
        $summary['result_amount']     = 0;
        $summary['player_commission'] = 0;
        $summary['agent_commission']  = 0;
        $summary['rev_share_amt']     = 0;
        $summary['player_wl_com']     = 0;
        $summary['agent_wl_com']      = 0;
        $summary['upper_wl']          = 0;
        $summary['upper_com']         = 0;
        $summary['upper_wl_com']      = 0;

        if ( ! empty($rows) && $calculate_summary ) {
            foreach ($rows as &$row) {

                # format agent username
                $row['agent_username'] = "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='".lang('Show Agent Win / Loss Report')."' onclick=\"show_agent_players_win_loss(".$row['agent_id'].",'".date("Y-m-d 00:00:00", strtotime($date_from))."','".date("Y-m-d 23:59:59", strtotime($date_to))."')\">".$row['agent_username']."</a> ";

                # add date
                $row['settlement_date_from'] = date('Y-m-d', strtotime($date_from));
                $row['settlement_date_to']   = date('Y-m-d', strtotime($date_to));

                $summary['bets']                += $row['bets'];
                $summary['real_bets']           += $row['real_bets'];
                $summary['bonuses']             += $row['bonuses'];
                $summary['rebates']             += $row['rebates'];
                $summary['transactions']        += $row['transactions'];
                $summary['deposit_fee']         += $row['deposit_fee'];
                $summary['withdraw_fee']        += $row['withdraw_fee'];
                $summary['admin']               += $row['admin'];
                $summary['deposit_comm']        += $row['deposit_comm'];
                $summary['result_amount']       += $row['result_amount'];
                $summary['player_commission']   += $row['player_commission'];
                $summary['agent_commission']    += $row['agent_commission'];
                $summary['rev_share_amt']       += $row['rev_share_amt'];
                $summary['player_wl_com']       += $row['result_amount'] + $row['player_commission'];
                $summary['agent_wl_com']        += $row['rev_share_amt'] + $row['agent_commission'];
                $summary['upper_wl']            += - $row['result_amount'] - $row['rev_share_amt'];
                $summary['upper_com']           += - $row['player_commission'] - $row['agent_commission'];
                $summary['upper_wl_com']        += ( - $row['result_amount'] - $row['rev_share_amt']) + ( - $row['player_commission'] - $row['agent_commission']);

            }
        }

        return [$rows, $summary];
    }

    public function getWsPlayerSettlement($agent_id, $agent_username, $date_from, $date_to){
        $this->db->select('agency_wl_settlement.id');
        $this->db->select('agency_wl_settlement.user_id');
        $this->db->select('agency_wl_settlement.status');
        $this->db->select('agency_wl_settlement.rev_share');
        $this->db->select('agency_wl_settlement.rolling_comm');
        $this->db->select('agency_wl_settlement.rolling_comm_basis');
        $this->db->select_sum('agency_wl_settlement.tie_bets');
        $this->db->select_sum('agency_wl_settlement.lost_bets');
        $this->db->select_sum('agency_wl_settlement.bets_except_tie');
        $this->db->select_sum('agency_wl_settlement.wins');
        $this->db->select_sum('agency_wl_settlement.bonuses');
        $this->db->select_sum('agency_wl_settlement.rebates');
        $this->db->select_sum('agency_wl_settlement.net_gaming');
        $this->db->select_sum('agency_wl_settlement.earnings');

        $this->db->select('agency_agents.agent_id');
        $this->db->select('agency_wl_settlement.user_id player_id');
        $this->db->select('player.username as player_username');
        $this->db->select('agency_agents.agent_name as agent_username');
        $this->db->select('agency_agents.settlement_period');
        $this->db->select_sum('agency_wl_settlement.bets');
        $this->db->select_sum('agency_wl_settlement.real_bets');
        $this->db->select_sum('agency_wl_settlement.result_amount');
        $this->db->select_sum('agency_wl_settlement.player_commission');
        $this->db->select_sum('agency_wl_settlement.rev_share_amt');
        $this->db->select_sum('agency_wl_settlement.agent_commission');
        $this->db->select_sum('agency_wl_settlement.roll_comm_income');
        $this->db->select_sum('agency_wl_settlement.rev_share_amt');
        $this->db->join('player','player.playerId = agency_wl_settlement.user_id','left');
        $this->db->join('agency_agents','agency_agents.agent_id = agency_wl_settlement.agent_id','left');
        $this->db->where('agency_wl_settlement.type', 'player');
        $this->db->where('agency_wl_settlement.agent_id', $agent_id);

        if ($agent_username) {
            $this->db->where('agency_agents.agent_name', $agent_username);
        }

        $this->db->where('agency_wl_settlement.settlement_date_to <=',$date_to);
        $this->db->where('agency_wl_settlement.settlement_date_from >=', $date_from);

        $this->db->group_by('agency_wl_settlement.user_id');
        $this->db->order_by('player_username');
        // $this->db->group_by('agent_username');
        $query = $this->db->get('agency_wl_settlement');

        $last_query = $this->db->last_query();

        // $this->utils->debug_log('getPlayerDailySettlement sql_query: ' . $last_query);

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
//                $row['player_username'] = "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='".lang('Show Player Info')."' onclick=\"show_player_game_history('".$row['player_username']."','".date("Y-m-d 00:00:00", strtotime($date_from))."','".date("Y-m-d 23:59:59", strtotime($date_to))."')\">".$row['player_username']."</a> ";
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

    /**
     * @param $agent_id
     * @param $agent_username
     * @param $date_from
     * @param $date_to
     * @param string $type(parent|current)
     * @return array
     */
    public function getWsSettlement($agent_id, $agent_username, $date_from, $date_to, $type = 'parent'){

        $this->db->select('agency_wl_settlement.id');
        $this->db->select('agency_wl_settlement.user_id');
        $this->db->select('agency_wl_settlement.settlement_date_from');
        $this->db->select('agency_wl_settlement.settlement_date_to');
        $this->db->select('agency_wl_settlement.status');
        $this->db->select('agency_wl_settlement.rev_share');
        $this->db->select('agency_wl_settlement.rolling_comm');
        $this->db->select('agency_wl_settlement.rolling_comm_basis');
        $this->db->select_sum('agency_wl_settlement.tie_bets');
        $this->db->select_sum('agency_wl_settlement.lost_bets');
        $this->db->select_sum('agency_wl_settlement.bets_except_tie');
        $this->db->select_sum('agency_wl_settlement.wins');
        $this->db->select_sum('agency_wl_settlement.bonuses');
        $this->db->select_sum('agency_wl_settlement.rebates');
        $this->db->select_sum('agency_wl_settlement.net_gaming');
        $this->db->select_sum('agency_wl_settlement.earnings');
        $this->db->select_sum('agency_wl_settlement.invoice_id');

        $this->db->select('agency_agents.agent_id');
        $this->db->select('agency_wl_settlement.user_id player_id');
        $this->db->select('player.username as player_username');
        $this->db->select('agency_agents.agent_name as agent_username');
        $this->db->select('agency_agents.settlement_period');
        $this->db->select('agency_agents.settlement_start_day');
        $this->db->select_sum('agency_wl_settlement.bets');
        $this->db->select_sum('agency_wl_settlement.real_bets');
        $this->db->select_sum('agency_wl_settlement.result_amount');
        $this->db->select_sum('agency_wl_settlement.player_commission');
        $this->db->select_sum('agency_wl_settlement.rev_share_amt');
        $this->db->select_sum('agency_wl_settlement.agent_commission');
        $this->db->select_sum('agency_wl_settlement.roll_comm_income');
        $this->db->select_sum('agency_wl_settlement.rev_share_amt');
        $this->db->join('player','player.playerId = agency_wl_settlement.user_id','left');
        $this->db->join('agency_agents','agency_agents.agent_id = agency_wl_settlement.agent_id','left');
        $this->db->where('agency_wl_settlement.type', 'agent');

        if($type=='parent'){
            $this->db->where('agency_agents.parent_id', $agent_id);
        }else{
            $this->db->where('agency_agents.agent_id', $agent_id);
        }

        if ($agent_username) {
            $this->db->where('agency_agents.agent_name', $agent_username);
        }
        $this->db->where('agency_wl_settlement.settlement_date_to <=', $date_to);
        $this->db->where('agency_wl_settlement.settlement_date_to >=', $date_from);

        $this->db->group_by('agency_agents.agent_name, status');
        $query = $this->db->get('agency_wl_settlement');
        $last_query = $this->db->last_query();
        // $this->utils->debug_log('getAgentDailySettlement sql_query: ' . $last_query);
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
//                $row['settlement_date_from'] = date('Y-m-d', strtotime($date_from));
//                $row['settlement_date_to'] = date('Y-m-d', strtotime($date_to));
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

    public function checkDuplicatedSettlement($type, $user_id, $datetime_from, $datetime_to, $status = null){

        $dt = new DateTime($datetime_from);
        $date_from = $dt->format('Y-m-d');

        $dt = new DateTime($datetime_to);
        $date_to = $dt->format('Y-m-d');

        $this->db->select('id, status');
        $this->db->from('agency_wl_settlement');
        $this->db->where('type', $type);
        $this->db->where('user_id', $user_id);

        if(empty($status) || $status!='current'){
            $this->db->where('DATE(settlement_date_from)', $date_from);
//            $this->db->where('DATE(settlement_date_to)', $date_to);
        }else{
            $this->db->where('DATE(settlement_date_from)', $date_from);
        }

        $result = $this->runOneRow();

        $last_query = $this->db->last_query();
        // $this->utils->debug_log('checkDuplicatedSettlement', $last_query);

        return $result;
    }

    public function updateWlSettlement($id, $data){
        $this->db->where('id', $id);
        $this->db->update('agency_wl_settlement', $data);

        return $this->db->affected_rows();
    }

    public function doWlSettlement($user_id, $date_from, $date_to){
        $invoice_id = $this->createInvoice($user_id, $date_from, $date_to);

        $data = [
            'status'        => 'settled',
            'invoice_id'    => $invoice_id,
        ];

        //update agent
        $this->db->where('settlement_date_from >=', $date_from);
        $this->db->where('settlement_date_to <=', $date_to);
        $this->db->where('status', 'unsettled');
        $this->db->where('agent_id', $user_id);
        $this->db->update('agency_wl_settlement', $data);

        $last_query = $this->db->last_query();
        $this->utils->debug_log('doWlSettlement', $last_query);

        $affected_rows = $this->db->affected_rows();

        return $affected_rows;
    }

    /**
     * Gets all agency_wl_settlement rows in a settlement group
     * @param   int     $user_id    id of agent
     * @param   string  $date_from  settlement group start date
     * @param   string  $date_to    settlement group end date
     * @return  array
     */
    public function getWlSettlementRowGroup($user_id, $date_start, $date_end) {
        $this->db->from($this->table_settlement_wl)
            ->where('settlement_date_from >=', $date_start)
            ->where('settlement_date_to <=', $date_end)
            ->where('status', 'unsettled')
            ->where('agent_id', $user_id) ;

        $res = $this->db->get()->result_array();

        $this->utils->debug_log('Agency_model::getWlSettlementRowGroup()', $this->db->last_query());

        return $res;
    }

    /**
     * Commit WL settlement one row at a time; step-by-step edition for ::doWlSettlement()
     * This function needs to be called with transaction.
     * @param   int     $agency_wl_settlement_id    == agency_wl_settlement.id
     * @param   boolean     $adjustWallet  True if balance adjustment happens on wallet, false happens on credit
     * @return  int     0 on failure, 1 on success.  (For it updates one row at a time, don't expect for other values)
     */
    public function doSingleWlSettlement($agency_wl_settlement_id, $adjustWallet = false) {
        // Read agency_wl_settlement row by id
        $row = $this->db->from($this->table_settlement_wl)
            ->where('id', $agency_wl_settlement_id)
            ->get()->first_row('array');

        // Stop if row not found
        if (empty($row) || count($row) == 0) {
            return false;
        }

        $this->utils->debug_log("WL Settlement row: ", $row);

        # Do not include player here (yet)
        if($row['type'] != 'agent') {
            return true;
        }

        $agent_id = $row['user_id'];
        $amount = $row['earnings'];

        $this->load->model('transactions');
        if($adjustWallet) {
            # Adjust balance on wallet
            if($amount >= 0) {
                $success = $this->transactions->depositToAgent(
                            $agent_id, abs($amount),
                            lang("Agent settlement [$agency_wl_settlement_id]"), # $reason
                            1, # $adminUserId
                            Transactions::PROGRAM);
            } else {
                $success = $this->transactions->withdrawFromAgent(
                            $agent_id, abs($amount),
                            lang("Agent settlement [$agency_wl_settlement_id]"), # $reason
                            1, # $adminUserId
                            Transactions::PROGRAM);
            }
        } else {
            # Adjust balance on credit
            # TODO
            $success = true;
        }

        if(!$success) {
            $this->utils->error_log("Error performing balance adjustment: agent id [$agent_id], amount [$amount], adjust wallet [$adjustWallet]");
            return 0;
        }

        // Create invoice
        $invoice_id = $this->createInvoice($row['user_id'], $row['settlement_date_from'], $row['settlement_date_to']);

        $update_set = [
            'status'        => 'settled',
            'invoice_id'    => $invoice_id,
        ];

        // Update agent
        $this->db->where('id', $agency_wl_settlement_id);
        $this->db->update('agency_wl_settlement', $update_set);

        // Log query
        $last_query = $this->db->last_query();
        $this->utils->debug_log('doSingleWlSettlement', $last_query);

        $affected_rows = $this->db->affected_rows();
        return $affected_rows;
    }

    /**
     * Closes a settlement record
     *
     * @param   int     $agency_wl_settlement_id    == agency_wl_settlement.id
     * @return  int     0 on failure, 1 on success.  (For it updates one row at a time, don't expect for other values)
     */
    public function closeSingleWlSettlement($agency_wl_settlement_id) {
        // Read agency_wl_settlement row by id
        $row = $this->db->from($this->table_settlement_wl)
            ->where('id', $agency_wl_settlement_id)
            ->get()->first_row('array');

        // Stop if row not found
        if (empty($row) || count($row) == 0) {
            return false;
        }

        $this->utils->debug_log("Closing WL Settlement row: ", $row);

        # Do not include player here (yet)
        if($row['type'] != 'agent') {
            return false;
        }

        $this->db->where('id', $agency_wl_settlement_id);
        $this->db->update('agency_wl_settlement', array('status' => 'closed'));

        return $this->db->affected_rows();
    }

    public function createInvoice($agent_id, $date_from, $date_to){
        $now = $this->utils->getNowForMysql();

        $data = [
            'agent_id'              =>  $agent_id,
            'settlement_date_from'  =>  $date_from,
            'settlement_date_to'    =>  $date_to,
            'created_on'            =>  $now,
            'updated_on'            =>  $now,
        ];
        $this->db->insert('agency_wl_settlement_invoice', $data);
        return $this->db->insert_id();
    }

    public function getInvoice($invoice_id){

        $this->db->select('id');
        $this->db->select('agency_wl_settlement_invoice.agent_id');
        $this->db->select('settlement_date_from');
        $this->db->select('settlement_date_to');
        $this->db->select('agency_agents.agent_name');
        $this->db->join('agency_agents','agency_agents.agent_id = agency_wl_settlement_invoice.agent_id','left');
        $this->db->where('id', $invoice_id);

        $query = $this->db->get('agency_wl_settlement_invoice');
        $invoice = $query->row();

        return $invoice;
    }

    public function getInvoicesByDateRange($date_from, $date_to){
        $this->db->select('id');
        $this->db->select('agency_wl_settlement_invoice.agent_id');
        $this->db->select('settlement_date_from');
        $this->db->select('settlement_date_to');
        $this->db->select('agency_agents.agent_name');
        $this->db->join('agency_agents','agency_agents.agent_id = agency_wl_settlement_invoice.agent_id','left');
        $this->db->where('settlement_date_from >=', $date_from);
        $this->db->where('settlement_date_to <=', $date_to);

        $query = $this->db->get('agency_wl_settlement_invoice');
        $invoices = $query->result();

        $last_query = $this->db->last_query();

        return $invoices;
    }


    public function getNewInvoiceId(){
        $this->db->select('MAX(invoice_id) as invoice_id');
        $this->db->from($this->tableName);
        $invoice_id = $this->runOneRowOneField('invoice_id');

        if(!empty($invoice_id)) {
            $invoice_id += 1;
        }else{
            $invoice_id = 1;
        }

        return $invoice_id;
    }

    public function insertWlSettlement($data){
        $result = $this->insertData('agency_wl_settlement', $data);
        $last_query = $this->db->last_query();
        return $result;
    }

    public function getTopAgent(){
        $this->db->select('agent_id');
        $this->db->from('agency_agents');
        $this->db->where('status', 'active');
        $this->db->where('parent_id', 0);

        $result = $this->runOneRow();

        $last_query = $this->db->last_query();

        return $result;
    }

    public function getWlSettlement($request, $mode = 'only_agent', $is_export = false, $readonlyLogged=false) {
        $this->load->library(array('data_tables', 'authentication', 'session'));
        $this->data_tables->is_export = $is_export;

        $this->load->model('operatorglobalsettings');

        $input = $this->data_tables->extra_search($request);

        # Preload vars for detecting settlement availability
        $cashback_settings = $this->operatorglobalsettings->getSettingJson('cashback_settings');
        $cashback_pay_hour = $cashback_settings['payTimeHour'];

        # Detect which agent's info to load; and whether current user (agent/admin) has priviledge to load it
        $agent_name = isset($input['agent_name']) ? $input['agent_name'] : null;
        $search_agent = $this->get_agent_by_name($agent_name);
        $logged_in_admin_id = $this->authentication->getUserid();
        $logged_in_agent_id = $this->session->userdata('agent_id');
        $this->utils->debug_log("Currently logged in admin/agent: ", $logged_in_admin_id, $logged_in_agent_id);

        $agent_can_do_settlement = false;
        $emptyData = $this->data_tables->empty_data($request);
        $emptyData['summary'] = $this->data_tables->empty_data($request);
        if (!empty($logged_in_agent_id)){ # Logged in agent
            # Check access priiledge
            $current_agent_all_downline = $this->get_all_downline_arr($logged_in_agent_id);
            if(!in_array($search_agent['agent_id'], $current_agent_all_downline)){
                $this->utils->debug_log("Trying to access non-downline agent ", $search_agent['agent_name']);
                return $emptyData;
            }

            $current_agent_detail = $this->get_agent_by_id($logged_in_agent_id);
            $agent_can_do_settlement = $current_agent_detail['can_do_settlement'] == 1;
        } elseif(!empty($logged_in_admin_id)) { # Logged in admin user
            if($this->permissions->checkPermissions('allow_to_do_settlement')) {
                $agent_can_do_settlement = true;
            }
        } else {
            $this->utils->error_log("No logged in agent/admin found.");
            return $emptyData;
        }

        $table = 'agency_wl_settlement';
        $where = array();
        $values = array();

        $where[] = "agency_wl_settlement.type = ?";
        $values[] = 'agent';

        $joins = array();
        $joins['agency_agents'] = 'agency_agents.agent_id = agency_wl_settlement.agent_id';

        if($mode == 'only_agent'){
            $where[] = "agency_wl_settlement.user_id = ?";
            $values[] = $search_agent['agent_id'];
        } else {
            $where[] = "agency_agents.parent_id = ?";
            $values[] = $search_agent['agent_id'];
        }

        if (isset($input['date_from'], $input['date_to'])) {
            $where[] = "agency_wl_settlement.settlement_date_from >= ?";
            $values[] = $input['date_from'];

            $where[] = "agency_wl_settlement.settlement_date_from <= ?";
            $values[] = $input['date_to'];

            $where[] = "agency_wl_settlement.settlement_date_to >= ?";
            $values[] = $input['date_from'];

            # Limit of settlement end date is relaxed by 1 day to cater for settlement ending at e.g. 11:59 next day
            $where[] = "agency_wl_settlement.settlement_date_to <= DATE_ADD(?, INTERVAL 1 DAY)";
            $values[] = $input['date_to'];
        }

        if (isset($input['status']) && $input['status'] != '') {
            $where[] = "agency_wl_settlement.status = ?";
            $values[] = $input['status'];
        }

        # DEFINE TABLE COLUMNS #####################################################################
        $i = 0;
        $columns = array(
            array(
                'dt' => $i++,
                'alias' => 'agent_name',
                'select' =>'agent_name',
                'name' => 'Agent Username',
                'formatter' => function ($val, $row) use ($mode) {
                    if($mode != 'only_agent') {
                        return '<a class="goto-agent" href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="'.lang('Load Sub-agent Settlement Report').'">'.$val.'</a>';
                    } else {
                        return '<a class="goto-player" href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="'.lang('Load Agent Settlement Detail').'">'.$val.'</a>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'status',
                'select' =>'agency_wl_settlement.status',
                'formatter' => function ($val, $row){
                    return lang("agency.settlement.status.$val");
                },
                'name' => 'Status'
            ),
            array(
                'dt' => $i++,
                'alias' => 'settlement_period',
                'select' =>'settlement_period',
                'formatter' => function ($val, $row){
                    return lang("lang.".strtolower($val));
                },
                'name' => 'Settlement Period'
            ),
            array(
                'dt' => $i++,
                'alias' => 'settlement_date_from',
                'select' =>'settlement_date_from',
                'formatter' => function ($d, $row) {
                    return "{$d} ~ {$row['settlement_date_to']}";
                },
                'name' => 'Date Range'
            ),
            array(
                'dt' => $i++,
                'alias' => 'bets',
                'select' =>'bets_display',
                'formatter' => 'currencyFormatter',
                'name' => 'Bets'
            ),
            array(
                'dt' => $i++,
                'alias' => 'bets_except_tie',
                'select' =>'bets_except_tie_display',
                'formatter' => 'currencyFormatter',
                'name' => 'bets_except_tie'
            ),
            array(
                'dt' => $i++,
                'alias' => 'result_amount',
                'select' =>'result_amount',
                'formatter' => 'currencyFormatter',
                'name' => 'Player W/L'
            ),
            array(
                'dt' => $i++,
                'alias' => 'platform_fee',
                'select' =>'platform_fee',
                'formatter' => 'currencyFormatter',
                'name' => 'Platform Fee'
            ),
            array(
                'dt' => $i++,
                'alias' => 'player_commission',
                'select' =>'player_commission',
                'formatter' => 'currencyFormatter',
                'name' => 'Player Rolling'
            ),
            array(
                'dt' => $i++,
                'alias' => 'player_wl_commission',
                'select' =>'id',
                'formatter' => function ($d, $row) {
                    return $this->utils->formatCurrencyNoSym($row['result_amount'] + $row['player_commission']);
                },
                'name' => 'Player W/L Comm'
            ),
            array(
                'dt' => $i++,
                'alias' => 'admin',
                'select' =>'admin',
                'formatter' => function ($d, $row) {
                    return $this->utils->formatCurrencyNoSym($d) . ' / ' .$this->utils->formatCurrencyNoSym($row['admin_total']);
                },
                'name' => 'Admin Fees'
            ),
            array(
                'dt' => $i++,
                'alias' => 'rebates',
                'select' =>'rebates',
                'formatter' => function ($d, $row) {
                    return $this->utils->formatCurrencyNoSym($d) . ' / ' .$this->utils->formatCurrencyNoSym($row['rebates_total']);
                },
                'name' => 'Cashback Fees'
            ),
            array(
                'dt' => $i++,
                'alias' => 'bonuses',
                'select' =>'bonuses',
                'formatter' => function ($d, $row) {
                    return $this->utils->formatCurrencyNoSym($d) . ' / ' .$this->utils->formatCurrencyNoSym($row['bonuses_total']);
                },
                'name' => 'Bonus Fees'
            )
        );
        if($this->utils->isEnabledFeature('use_deposit_withdraw_fee')) {
            $columns = array_merge($columns, array(
                array(
                    'dt' => $i++,
                    'alias' => 'deposit_fee',
                    'select' =>'agency_wl_settlement.deposit_fee',
                    'formatter' => function ($d, $row) {
                        return $this->utils->formatCurrencyNoSym($d) . ' / ' .$this->utils->formatCurrencyNoSym($row['deposit_fee_total']);
                    },
                    'name' => 'Deposit Fee'
                ),
                array(
                    'dt' => $i++,
                    'alias' => 'withdraw_fee',
                    'select' =>'agency_wl_settlement.withdraw_fee',
                    'formatter' => function ($d, $row) {
                        return $this->utils->formatCurrencyNoSym($d) . ' / ' .$this->utils->formatCurrencyNoSym($row['withdraw_fee_total']);
                    },
                    'name' => 'Withdraw Fee'
                ),
            ));
        } else {
            $columns = array_merge($columns, array(
                array(
                    'dt' => $i++,
                    'alias' => 'transactions',
                    'select' =>'transactions',
                    'formatter' => function ($d, $row) {
                        return $this->utils->formatCurrencyNoSym($d) . ' / ' .$this->utils->formatCurrencyNoSym($row['transactions_total']);
                    },
                    'name' => 'Transaction Fees'
                ),
            ));
        }
        $columns = array_merge($columns, array(
            array(
                'dt' => $i++,
                'alias' => 'rev_share_amt',
                'select' =>'rev_share_amt',
                'formatter' => 'currencyFormatter',
                'name' => 'Master W/L'
            ),
            array(
                'dt' => $i++,
                'alias' => 'agent_commission',
                'select' =>'agent_commission',
                'formatter' => 'currencyFormatter',
                'name' => 'Master Rolling'
            ),
            array(
                'dt' => $i++,
                'alias' => 'deposit_comm',
                'select' =>'agency_wl_settlement.deposit_comm',
                'formatter' => 'currencyFormatter',
                'name' => 'Deposit Comm'
            ),
            array(
                'dt' => $i++,
                'alias' => 'agent_wl_commission',
                'select' =>'earnings',
                'formatter' => 'currencyFormatter',
                'name' => 'Master W/L Comm'
            ),
            array(
                'dt' => $i++,
                'alias' => 'upper_wl',
                'select' =>'agent_level',
                'formatter' => function ($d, $row) {
                    if($d != 0){
                        return $this->utils->formatCurrencyNoSym(- $row['result_amount'] - $row['rev_share_amt']);
                    }else{
                        return lang('N/A');
                    }
                },
                'name' => 'Upper W/L'
            ),
            array(
                'dt' => $i++,
                'alias' => 'upper_commission',
                'select' =>'agent_level',
                'formatter' => function ($d, $row) {
                    if($d != 0){
                        return $this->utils->formatCurrencyNoSym(- $row['player_commission'] - $row['agent_commission']);
                    }else{
                        return lang('N/A');
                    }
                },
                'name' => 'Upper Comm'
            ),
            array(
                'dt' => $i++,
                'alias' => 'upper_wl_commission',
                'select' =>'agent_level',
                'formatter' => function ($d, $row) {
                    if($d != 0){
                        return $this->utils->formatCurrencyNoSym(( - $row['result_amount'] - $row['rev_share_amt']) + ( - $row['player_commission'] - $row['agent_commission']));
                    }else{
                        return lang('N/A');
                    }
                },
                'name' => 'Upper W/L Comm'
            ),
            array(
                'dt' => $i++,
                'alias' => 'action',
                'select' =>'agency_wl_settlement.status',
                'formatter' => function ($d, $row) use ($cashback_pay_hour, $agent_can_do_settlement, $readonlyLogged) {
                    if($readonlyLogged){
                        return '';
                    }
                    # Decide whether this settlement can be settled already
                    $settlement_date = substr($row['settlement_date_to'], 0, 10); # Y-m-d
                    if($settlement_date == date('Y-m-d') && time() < strtotime($cashback_pay_hour)) {
                        # Settlement is finished today but at current time, cashback has not been paid
                        $settlement_finalized = false;
                    } else {
                        $settlement_finalized = true;
                    }

                    if($d != 'settled' && $d != 'closed') {
                        $param = "{$row['user_id']},\"{$row['status']}\",\"{$row['settlement_date_from']}\",\"{$row['settlement_date_to']}\"";
                        if($settlement_finalized && $agent_can_do_settlement) {
                            return "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='".lang('Do Settlement')."' onclick='do_settlement_wl({$param})'><span class='glyphicon glyphicon-credit-card text-success'></span></a>  <a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='".lang('Close Settlement')."' onclick='close_settlement_wl({$param})'><span class='glyphicon glyphicon-remove-circle text-success'></span></a>";
                        } else {
                            return "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='".lang('Do Settlement')."'><span class='glyphicon glyphicon-credit-card text-success' style='color:grey'></span></a>  <a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='".lang('Close Settlement')."' onclick='close_settlement_wl({$param})'><span class='glyphicon glyphicon-remove-circle text-success'></span></a>";
                        }
                    } else if($d == 'settled') {
                        return "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='Send Invoice' onclick='settlement_send_invoice_wl({$row['invoice_id']})'><span class='glyphicon glyphicon-envelope text-info'></span></a> ";
                    }
                },
            ),
            # Selects below are used in formatter, not displayed
            array(
                'dt' => $i++,
                'alias' => 'settlement_date_to',
                'select' =>'settlement_date_to',
            ),
            array(
                'dt' => $i++,
                'alias' => 'user_id',
                'select' =>'user_id',
            ),
            array(
                'dt' => $i++,
                'alias' => 'invoice_id',
                'select' =>'invoice_id',
            ),
            array(
                'dt' => $i++,
                'alias' => 'rebates_total',
                'select' =>'rebates_total',
            ),
            array(
                'dt' => $i++,
                'alias' => 'bonuses_total',
                'select' =>'bonuses_total',
            ),
            array(
                'dt' => $i++,
                'alias' => 'admin_total',
                'select' =>'admin_total',
            ),
            array(
                'dt' => $i++,
                'alias' => 'transactions_total',
                'select' =>'transactions_total',
            ),
            array(
                'dt' => $i++,
                'alias' => 'deposit_fee_total',
                'select' =>'deposit_fee_total',
            ),
            array(
                'dt' => $i++,
                'alias' => 'withdraw_fee_total',
                'select' =>'withdraw_fee_total',
            ),
        ));


        # OUTPUT ###################################################################################
        $result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
        $result['summary'] = $this->data_tables->summary($request, $table, $joins,
            'SUM(bets_display) bets_display, SUM(bets_except_tie_display) bets_except_tie_display, SUM(result_amount) result_amount, SUM(player_commission) player_commission, SUM(result_amount + player_commission) player_wl_commission, SUM(platform_fee) platform_fee, SUM(admin) admin, SUM(bonuses) bonuses, SUM(rebates) rebates, SUM(transactions) transactions, SUM(agency_wl_settlement.deposit_fee) deposit_fee, SUM(agency_wl_settlement.withdraw_fee) withdraw_fee, SUM(rev_share_amt) rev_share_amt, SUM(agent_commission) agent_commission, SUM(agency_wl_settlement.deposit_comm) deposit_comm, SUM(earnings) earnings',
            null, $columns, $where, $values);

        return $result;
    }

    public function getWlSettlementDetail($request) {
        $this->load->library(array('data_tables'));

        $input = $this->data_tables->extra_search($request);
        $table = 'agency_daily_player_settlement';
        $where = array();
        $values = array();

        $joins = array();
        $joins['player'] = 'agency_daily_player_settlement.player_id = player.playerId';
        $joins['agency_agents'] = 'agency_daily_player_settlement.agent_id = agency_agents.agent_id';
        $joins['agency_agents player_agent'] = 'player_agent.agent_id = player.agent_id';
        $joins['game_type'] = 'game_type.game_platform_id = agency_daily_player_settlement.game_platform_id AND game_type.id = agency_daily_player_settlement.game_type_id';
        $joins['external_system'] = 'agency_daily_player_settlement.game_platform_id = external_system.id';

        # Detect which agent's info to load; and whether current user (agent/admin) has priviledge to load it
        $agent_name = isset($input['agent_name']) ? $input['agent_name'] : null;
        $search_agent = $this->get_agent_by_name($agent_name);
        $logged_in_admin_id = $this->authentication->getUserid();
        $logged_in_agent_id = $this->session->userdata('agent_id');
        $this->utils->debug_log("Currently logged in admin/agent: ", $logged_in_admin_id, $logged_in_agent_id);


        $emptyData = $this->data_tables->empty_data($request);
        $emptyData['summary'] = $this->data_tables->empty_data($request);
        if (!empty($logged_in_agent_id)){ # Logged in agent
            # Check access priiledge
            $current_agent_all_downline = $this->get_all_downline_arr($logged_in_agent_id);
            if(!in_array($search_agent['agent_id'], $current_agent_all_downline)){
                $this->utils->debug_log("Trying to access non-downline agent ", $search_agent['agent_name']);
                return $emptyData;
            }
        } elseif(!empty($logged_in_admin_id)) { # Logged in admin user
            # By default has access
        } else {
            $this->utils->error_log("No logged in agent/admin found.");
            return $emptyData;
        }

        $where[] = "agency_agents.agent_id = ?";
        $values[] = $search_agent['agent_id'];

        if (isset($input['date_from'], $input['date_to'])) {
            $where[] = "agency_daily_player_settlement.settlement_date >= ?";
            $values[] = $input['date_from'];

            $where[] = "agency_daily_player_settlement.settlement_date <= ?";
            $values[] = $input['date_to'];
        }

        if (!isset($input['include_all_downline_players']) || $input['include_all_downline_players'] != 1) {
            $where[] = "player.agent_id = ?";
            $values[] = $search_agent['agent_id'];
        }

        # DEFINE TABLE COLUMNS #####################################################################
        $i = 0;
        $columns = array(
            array(
                'dt' => $i++,
                'alias' => 'agent_name',
                'select' =>'agency_agents.agent_name',
                'name' => 'Agent Username'
            ),
            array(
                'dt' => $i++,
                'alias' => 'settlement_date',
                'select' =>'settlement_date',
                'name' => 'Settlement Date'
            ),
            array(
                'dt' => $i++,
                'alias' => 'direct_agent_id',
                'select' =>'player.agent_id',
                'name' => 'Source',
                'formatter' => function ($val, $row) {
                    # If the row's player's agent id is direct agent id, this row records the direct player
                    return $val == $row['agent_id'] ? lang('Player') : lang('Agent') . '('.$row['player_agent_username'].')';
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'player_username',
                'select' =>'player.username',
                'name' => 'Player Username'
            ),
            array(
                'dt' => $i++,
                'alias' => 'game_platform_code',
                'select' =>'external_system.system_code',
                'name' => 'Game Platform'
            ),
            array(
                'dt' => $i++,
                'alias' => 'game_type',
                'select' =>'game_type.game_type',
                'formatter' => function ($val, $row) {
                    return lang($val);
                },
                'name' => 'Game Type'
            ),
            array(
                'dt' => $i++,
                'alias' => 'platform_fee',
                'select' =>'agency_daily_player_settlement.platform_fee',
                'name' => 'Platform Fee',
                'formatter' => 'currencyFormatter',
            ),
            array(
                'dt' => $i++,
                'alias' => 'admin',
                'select' =>'admin',
                'formatter' => function ($d, $row) {
                    return $this->utils->formatCurrencyNoSym($d) . ' / ' .$this->utils->formatCurrencyNoSym($row['admin_total']);
                },
                'name' => 'Admin Fees'
            ),
            array(
                'dt' => $i++,
                'alias' => 'rebates',
                'select' =>'rebates',
                'formatter' => function ($d, $row) {
                    return $this->utils->formatCurrencyNoSym($d) . ' / ' .$this->utils->formatCurrencyNoSym($row['rebates_total']);
                },
                'name' => 'Cashback Fees'
            ),
            array(
                'dt' => $i++,
                'alias' => 'bonuses',
                'select' =>'bonuses',
                'formatter' => function ($d, $row) {
                    return $this->utils->formatCurrencyNoSym($d) . ' / ' .$this->utils->formatCurrencyNoSym($row['bonuses_total']);
                },
                'name' => 'Bonus Fees'
            )
        );
        if($this->utils->isEnabledFeature('use_deposit_withdraw_fee')) {
            $columns = array_merge($columns, array(
                array(
                    'dt' => $i++,
                    'alias' => 'deposit_fee',
                    'select' =>'agency_daily_player_settlement.deposit_fee',
                    'formatter' => function ($d, $row) {
                        return $this->utils->formatCurrencyNoSym($d) . ' / ' .$this->utils->formatCurrencyNoSym($row['deposit_fee_total']);
                    },
                    'name' => 'Deposit Fee'
                ),
                array(
                    'dt' => $i++,
                    'alias' => 'withdraw_fee',
                    'select' =>'agency_daily_player_settlement.withdraw_fee',
                    'formatter' => function ($d, $row) {
                        return $this->utils->formatCurrencyNoSym($d) . ' / ' .$this->utils->formatCurrencyNoSym($row['withdraw_fee_total']);
                    },
                    'name' => 'Withdraw Fee'
                ),
            ));
        } else {
            $columns = array_merge($columns, array(
                array(
                    'dt' => $i++,
                    'alias' => 'transactions',
                    'select' =>'transactions',
                    'formatter' => function ($d, $row) {
                        return $this->utils->formatCurrencyNoSym($d) . ' / ' .$this->utils->formatCurrencyNoSym($row['transactions_total']);
                    },
                    'name' => 'Transaction Fees'
                ),
            ));
        }
        $columns = array_merge($columns, array(
            array(
                'dt' => $i++,
                'alias' => 'rev_share_amt',
                'select' =>'rev_share_amt',
                'formatter' => 'currencyFormatter',
                'name' => 'Agent Commission Income'
            ),
            array(
                'dt' => $i++,
                'alias' => 'agent_commission',
                'select' =>'agent_commission',
                'formatter' => function ($d, $row) {
                    if($row['agent_rolling_paid'] == 1 && $d >= 0.005) {
                        return $this->utils->formatCurrencyNoSym($d) .' ('.lang('Paid').')';
                    } else {
                        return $this->utils->formatCurrencyNoSym($d);
                    }
                },
                'name' => 'Agent Rolling'
            ),
            array(
                'dt' => $i++,
                'alias' => 'deposit_comm',
                'select' =>'agency_daily_player_settlement.deposit_comm',
                'formatter' => function ($d, $row) {
                    return $this->utils->formatCurrencyNoSym($d);
                },
                'name' => 'Deposit Comm'
            ),
            array(
                'dt' => $i++,
                'alias' => 'earnings',
                'select' =>'earnings',
                'formatter' => 'currencyFormatter',
                'name' => 'Agent Net Income'
            ),
            # Selects below are for use in formatter, not displayed
            array(
                'alias' => 'agent_id',
                'select' =>'agency_daily_player_settlement.agent_id',
            ),
            array(
                'alias' => 'rebates_total',
                'select' =>'agency_daily_player_settlement.rebates_total',
            ),
            array(
                'alias' => 'bonuses_total',
                'select' =>'agency_daily_player_settlement.bonuses_total',
            ),
            array(
                'alias' => 'transactions_total',
                'select' =>'agency_daily_player_settlement.transactions_total',
            ),
            array(
                'alias' => 'deposit_fee_total',
                'select' =>'agency_daily_player_settlement.deposit_fee_total',
            ),
            array(
                'alias' => 'withdraw_fee_total',
                'select' =>'agency_daily_player_settlement.withdraw_fee_total',
            ),
            array(
                'alias' => 'admin_total',
                'select' =>'agency_daily_player_settlement.admin_total',
            ),
            array(
                'alias' => 'agent_rolling_paid',
                'select' =>'agency_daily_player_settlement.agent_rolling_paid',
            ),
            array(
                'alias' => 'player_agent_username',
                'select' =>'player_agent.agent_name',
            ),
        ));


        $result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
        $result['summary'] = $this->data_tables->summary($request, $table, $joins,
            'SUM(platform_fee) platform_fee, SUM(admin) admin, SUM(bonuses) bonuses, SUM(rebates) rebates, SUM(transactions) transactions, SUM(agency_daily_player_settlement.deposit_fee) deposit_fee, SUM(agency_daily_player_settlement.withdraw_fee) withdraw_fee, SUM(rev_share_amt) rev_share_amt, SUM(agent_commission) agent_commission, SUM(agency_daily_player_settlement.deposit_comm) deposit_comm, SUM(earnings) earnings',
            null, $columns, $where, $values);

        return $result;
    }

    public function regenerateAndSaveSignKey($merchantInfo){
        if(!empty($merchantInfo)){
            $key=random_string('md5');
            if($merchantInfo['live_mode']){
                $this->db->set('live_sign_key', $key)->where('id', $merchantInfo['agent_id']);
            }else{
                $this->db->set('staging_sign_key', $key)->where('id', $merchantInfo['agent_id']);
            }
            return $this->runAnyUpdate('agency_agents');
        }

        return false;
    }

    public function regenerateAndSaveSecureKey($merchantInfo){
        if(!empty($merchantInfo)){
            $key=random_string('md5');
            if($merchantInfo['live_mode']){
                $this->db->set('live_secure_key', $key)->where('id', $merchantInfo['agent_id']);
            }else{
                $this->db->set('staging_secure_key', $key)->where('id', $merchantInfo['agent_id']);
            }
            return $this->runAnyUpdate('agency_agents');
        }

        return false;
    }

    public function getMerchantInfoByCode($merchant_code){
        $this->db->from('agency_agents')->where('agent_name', $merchant_code);
        return $this->runOneRowArray();
    }

    public function isUnderAgent($agent, $player_username){
        $success=false;
        $this->db->select('playerId, agent_id')->from('player')->where('username', $player_username);

        $row=$this->runOneRowArray();
        if(!empty($row)){
            $success=$agent['agent_id']==$row['agent_id'];
        }

        return $success;
    }

    /**
     *  get agent id by merchant code
     *
     *  @param  merchant_code
     *  @return agent_id
     */
    public function getAgentIdByMerchantCode($merchant_code) {
        if(!empty($merchant_code)){
            $this->db->select('agent_id')->from($this->table_agent)
                ->where('agent_name', $merchant_code);
            return $this->runOneRowOneField('agent_id');
        }

        return null;
    }

    /**
     *  get agent currency by merchant code
     *
     *  @param  agent_id
     *  @return currency code
     */
    public function getAgentCurrencyByAgentId($agent_id) {
        if(!empty($agent_id)){
            $this->db->select('currency')->from($this->table_agent)
                ->where('agent_id', $agent_id);
            return $this->runOneRowOneField('currency');
        }

        return null;
    }

    /**
     *  get agent id by merchant code
     *
     *  @param  merchant_code
     *  @return player_prefix
     */
    public function getPlayerPrefixByMerchantCode($merchant_code) {
        if(!empty($merchant_code)){
            $this->db->select('player_prefix')->from($this->table_agent)
                ->where('agent_name', $merchant_code);
            return $this->runOneRowOneField('player_prefix');
        }

        return null;
    }

    public function get_structure_game_platforms($structure_id) {
        $query = $this->db->get_where('agency_structure_game_platforms', array('structure_id' => $structure_id));
        return $query->result_array();
    }

    public function get_structure_game_types($structure_id) {
        $query = $this->db->get_where('agency_structure_game_types', array('structure_id' => $structure_id));
        return $query->result_array();
    }

    public function get_agent_game_platforms($agent_id, $game_platform_id = NULL) {
        $this->db->where('agent_id', $agent_id);
        if ($game_platform_id) {
            $this->db->where('game_platform_id', $game_platform_id);
        }
        $query = $this->db->get('agency_agent_game_platforms');
        return $game_platform_id ? $query->row_array() : $query->result_array();
    }

    private $agent_game_types = array();
    public function get_agent_game_types($agent_id, $game_platform_id = NULL, $game_type_id = NULL) {

        if(!array_key_exists($agent_id, $this->agent_game_types)) {
            $this->db->select('aagt.*');
            $this->db->from('agency_agent_game_platforms aagp');
            $this->db->join('agency_agent_game_types aagt', 'aagt.game_platform_id = aagp.game_platform_id AND aagt.agent_id = aagp.agent_id');
            $this->db->where('aagp.agent_id', $agent_id);
            $this->db->order_by('aagp.game_platform_id');
            $this->db->order_by('aagt.game_type_id');
            $query = $this->db->get();

            $this->agent_game_types[$agent_id] = $query->result_array();
            $this->agent_game_types['by_game_type_'.$agent_id] = array_column($this->agent_game_types[$agent_id], null, 'game_type_id');

            # Query tier comm pattern setting
            if($this->utils->isEnabledFeature('agent_tier_comm_pattern')) {
                foreach($this->agent_game_types['by_game_type_'.$agent_id] as $tid => $setting) {
                    # Query each game_type's tier pattern
                    $this->db->select('aagt.agent_id, aagt.game_type_id, tier_pattern.*');
                    $this->db->from('agency_agent_game_types aagt');
                    $this->db->join('agency_tier_comm_pattern_tiers tier_pattern', 'aagt.pattern_id = tier_pattern.pattern_id');
                    $this->db->where('aagt.agent_id', $agent_id);
                    $this->db->where('aagt.game_type_id', $tid);
                    $this->db->order_by('aagt.game_platform_id');
                    $this->db->order_by('aagt.game_type_id');
                    $this->db->order_by('tier_pattern.upper_bound');
                    $query = $this->db->get();
                    $this->agent_game_types['by_game_type_'.$agent_id][$tid]['tier_pattern'] = $query->result_array();
                }
            }
        }

        if(isset($game_type_id)) {
            $setting = $this->agent_game_types['by_game_type_'.$agent_id];
            if(!array_key_exists($game_type_id, $setting)) {
                return array();
            } else {
                return $setting[$game_type_id];
            }
        } else {
            return $this->agent_game_types[$agent_id];
        }
    }

    public function get_player_game_platforms($player_id, $game_platform_id = NULL) {
        $this->db->where('player_id', $player_id);
        if ($game_platform_id) {
            $this->db->where('game_platform_id', $game_platform_id);
        }
        $query = $this->db->get('agency_player_game_platforms');
        return $game_platform_id ? $query->row_array() : $query->result_array();
    }

    public function get_player_game_types($player_id, $game_platform_id = NULL, $game_type_id = NULL) {

        $this->db->select('aagt.*');
        $this->db->from('agency_player_game_platforms aagp');
        $this->db->join('agency_player_game_types aagt', 'aagt.game_platform_id = aagp.game_platform_id AND aagt.player_id = aagp.player_id');
        $this->db->where('aagp.player_id', $player_id);

        if ($game_platform_id) {
            $this->db->where('aagp.game_platform_id', $game_platform_id);
        }

        if ($game_type_id) {
            $this->db->where('aagt.game_type_id', $game_type_id);
        }

        $this->db->order_by('aagp.game_platform_id');
        $this->db->order_by('aagt.game_type_id');

        $query = $this->db->get();

        return isset($game_platform_id, $game_type_id) ? $query->row_array() : $query->result_array();
    }

    public function get_game_platforms_and_types() {
        $this->load->model('external_system');
        $query = $this->db->select('external_system.id as game_platform_id')
            ->select('external_system.system_name as game_platform_name')
            ->select('external_system.system_code as game_platform_code')
            ->select('game_type.id as game_type_id')
            ->select('game_type.game_type as game_type_name')
            ->from('external_system')
            ->join('game_type', 'game_type.game_platform_id = external_system.id')
            ->where('external_system.system_type', External_system::SYSTEM_GAME_API)
            ->where('external_system.status', External_system::STATUS_NORMAL)
            ->order_by('external_system.id')
            ->order_by('game_type.id')
            ->get();

        $game_platforms_and_types = $query->result_array();

        $game_platforms = array();

        foreach ($game_platforms_and_types as $game_type) {

            if ( ! isset($game_platforms[$game_type['game_platform_id']])) {
                $game_platforms[$game_type['game_platform_id']] = array(
                    'id' => $game_type['game_platform_id'],
                    'name' => $game_type['game_platform_code'],
                    'game_types' => array(),
                );
            }

            $game_platforms[$game_type['game_platform_id']]['game_types'][] = array(
                'id' => $game_type['game_type_id'],
                'name' => $game_type['game_type_name'],
            );

        }

        return $game_platforms;

    }

    public function get_vip_levels() {

        $this->load->model('group_level');
        $vip_levels = $this->group_level->getAllPlayerLevelsForSelect();
        $return = array();

        foreach ($vip_levels as $vip_levels) {

            if ( ! isset($return[$vip_levels['vipSettingId']])) {
                $return[$vip_levels['vipSettingId']] = array(
                    'id' => $vip_levels['vipSettingId'],
                    'name' => $vip_levels['groupName'],
                    'levels' => array(),
                );
            }

            $return[$vip_levels['vipSettingId']]['levels'][] = array(
                'id' => $vip_levels['vipsettingcashbackruleId'],
                'name' => $vip_levels['groupLevelName'],
            );

        }

        return $return;

    }

    public function getLevelOneAgencyByAgentId($agent_id){

        $this->db->select('agent_level')->from('agency_agents')->where('agent_id', $agent_id);
        $agent_level=$this->runOneRowOneField('agent_level');
        $parent_id = $agent_id;

        //return level 1
        while($agent_level>1){
            $agent_id = $parent_id;
            list($parent_id, $agent_level) = $this->getParentAgencyByAgentId($agent_id);
        }

        return $agent_id;
    }

    public function getRootAgencyByAgentId($agent_id){

        $this->db->select('agent_level')->from('agency_agents')->where('agent_id', $agent_id);
        $agent_level=$this->runOneRowOneField('agent_level');
        $parent_id = $agent_id;

        //return level 1
        while($agent_level>1){
            $agent_id = $parent_id;
            list($parent_id, $agent_level) = $this->getParentAgencyByAgentId($agent_id);
        }

        return $agent_id;
    }

    public function getParentAgencyByAgentId($agent_id){
        $this->db->select('parent_id, agent_level');
        $this->db->from($this->table_agent);
        $this->db->where('agent_id', $agent_id);

        $row = $this->runOneRowArray();
        $parent_id = $row['parent_id'];
        $agent_level = $row['agent_level'];

        return [$parent_id, $agent_level];
    }

    //----------agent domain----------------------------------------
    public function getAdditionalDomainList($agent_id) {
        $this->db->from('agency_tracking_domain')->where('agent_id', $agent_id)->where('tracking_type', self::TRACKING_TYPE_DOMAIN);
        // $this->ignoreDeleted('deleted_at');

        $rows = $this->runMultipleRowArray();
        return $rows;
    }

    /**
     * overview : check if additional agent domain exist
     *
     * @param string $agent_domain
     * @param int $agentTrackingId
     * @return bool
     */
    public function existsAdditionalAgentDomain($agent_domain, $agentTrackingId = null) {
        //ignore self and deleted
        $this->db->from('agency_tracking_domain')->where('tracking_domain', $agent_domain)
          ->where('tracking_type', self::TRACKING_TYPE_DOMAIN);
        if ($agentTrackingId) {
            $this->db->where('id !=', $agentTrackingId);
        }
        // $this->ignoreDeleted('deleted_at');

        return $this->runExistsResult();
    }

    /**
     * overview : update additional domain
     *
     * @param int       $agentTrackingId
     * @param string    $agent_domain
     * @return bool
     */
    public function updateAdditionalAgentDomain($agentTrackingId, $agent_domain) {
        $this->db->set('tracking_domain', $agent_domain)->where('id', $agentTrackingId);

        return $this->runAnyUpdate('agency_tracking_domain');
    }

    /**
     * overview : new additional domain
     * @param int   $agent_id
     * @param int   $agent_domain
     * @return mixed
     */
    public function newAdditionalAgentDomain($agent_id, $agent_domain) {
        $data = [
            'tracking_domain' => $agent_domain,
            'agent_id' => $agent_id,
            'tracking_type' => self::TRACKING_TYPE_DOMAIN,
            // 'created_at' => $this->utils->getNowForMysql(),
            // 'updated_at' => $this->utils->getNowForMysql(),
        ];

        return $this->insertData('agency_tracking_domain', $data);
    }

    /**
     * overview : remove additional  domain
     *
     * @param $agentTrackingId
     * @return bool
     */
    public function removeAdditionalAgentDomain($agentTrackingId) {
        return $this->db->delete('agency_tracking_domain', ['id'=> $agentTrackingId]);
        // $this->db->set('deleted_at', $this->utils->getNowForMysql())
        //   ->set('tracking_domain', 'concat("deleted ",tracking_domain)',false)
        //   ->where('id', $agentTrackingId);

        // return $this->runAnyUpdate('agency_tracking_domain');
    }

    //----------source code----------------------------------------
    /**
     * overview : get source code list
     *
     * @param $agent_id
     * @return null
     */
    public function getSourceCodeList($agent_id) {
        $this->db->from('agency_tracking_domain')->where('agent_id', $agent_id)->where('tracking_type', self::TRACKING_TYPE_SOURCE_CODE);
        // $this->ignoreDeleted('deleted_at');

        $rows = $this->runMultipleRowArray();
        return $rows;
    }

    /**
     * overview : update source code
     *
     * @param $agentTrackingId
     * @param $sourceCode
     * @return bool
     */
    public function updateSourceCode($agentTrackingId, $data) {
        $this->db->where('id', $agentTrackingId);
        if(is_array($data)){
            $this->db->set($data);

            return $this->runAnyUpdate('agency_tracking_domain');
        }else{
            $this->db->set('tracking_source_code', $data);

            return $this->runAnyUpdate('agency_tracking_domain');
        }

    }

    /**
     * overview : new source code
     *
     * @param string $sourceCode
     * @return mixed
     */
    public function newSourceCode($agent_id, $sourceCode) {

        if(is_array($sourceCode)){
            $data = $sourceCode;
            $data['agent_id'] = $agent_id;
            $data['tracking_type'] = self::TRACKING_TYPE_SOURCE_CODE;
        }else{
            $data = [
                'tracking_source_code' => $sourceCode,
                'agent_id' => $agent_id,
                'tracking_type' => self::TRACKING_TYPE_SOURCE_CODE
            ];
        }

        return $this->insertData('agency_tracking_domain', $data);

    }

    /**
     * overview : check if source code exist
     *
     * @param $agent_id
     * @param $sourceCode
     * @param null $agentTrackingId
     * @return bool
     */
    public function existsSourceCode($agent_id, $sourceCode, $agentTrackingId = null) {
        //ignore self and deleted
        $this->db->from('agency_tracking_domain')->where('tracking_source_code', $sourceCode)
            ->where('tracking_type', self::TRACKING_TYPE_SOURCE_CODE)
            ->where('agent_id', $agent_id);
        if ($agentTrackingId) {
            $this->db->where('id !=', $agentTrackingId);
        }
        // $this->ignoreDeleted('deleted_at');

        return $this->runExistsResult();

    }

    public function getSourceCodeById($agent_id, $agentTrackingId) {
        //ignore self and deleted
        $this->db->from('agency_tracking_domain')
            ->where('tracking_type', self::TRACKING_TYPE_SOURCE_CODE)
            ->where('agent_id', $agent_id)
            ->where('id', $agentTrackingId)
        ;


        return $this->runOneRowArray();

    }

    public function getSourceCodeFromTrackingCode($agent_tracking_code, $agent_tracking_source_code){
        $agent = $this->get_agent_by_tracking_code($agent_tracking_code);
        if(empty($agent)){
            $this->utils->debug_log('getSourceCodeFromTrackingCode', 'agent_tracking_code: ' . $agent_tracking_code . ' invalid');

            return FALSE;
        }

        //ignore self and deleted
        $this->db->from('agency_tracking_domain')->where('tracking_source_code', $agent_tracking_source_code)
            ->where('tracking_type', self::TRACKING_TYPE_SOURCE_CODE)
            ->where('agent_id', $agent['agent_id']);
        // $this->ignoreDeleted('deleted_at');

        $result = $this->runOneRowArray();
        if(empty($result)){
            return FALSE;
        }

        return [$agent, $result];
    }

    /**
     * overview : remove source code
     *
     * @param  int  $agentTrackingId
     * @return bool
     */
    public function removeSourceCode($agentTrackingId) {

        return $this->db->delete('agency_tracking_domain', ['id'=> $agentTrackingId]);
        // $this->db->set('deleted_at', $this->utils->getNowForMysql())->where('id', $agentTrackingId);

    }

    // public function recordTrackingCode($agent_id, $adminUserId = null) {
    //     $this->db->from('affiliates')->where('affiliateId', $agent_id);
    //     $row = $this->runOneRowArray();
    //     if (!empty($row) && !empty($row['trackingCode'])) {

    //         $data = [
    //             'agent_id' => $agent_id,
    //             'user_id' => $adminUserId,
    //             'tracking_code' => $row['trackingCode'],
    //             'created_at' => $this->utils->getNowForMysql(),
    //         ];
    //         return $this->insertData('aff_tracking_code_history', $data);
    //     }
    //     return false;
    // }

    public function updateTrackingCode($agent_id, $trackingCode, $adminUserId) {
        if (!empty($agent_id) && !empty($trackingCode)) {
            // $this->recordTrackingCode($agent_id, $adminUserId);
            //record old tracking code
            $this->db->set('tracking_code', $trackingCode)
                ->set('updated_on', $this->utils->getNowForMysql())
                ->set('updated_by', $adminUserId)
                ->where('agent_id', $agent_id);
            return $this->runAnyUpdate('agency_agents');
        } else {
            return false;
        }
    }

    public function generate_empty_tracking_code(){
        //use username

        return $this->runRawUpdateInsertSQL("update agency_agents set tracking_code=agent_name where tracking_code is null");

    }

    //----------domain for tracking link----------------------------------------
    public function get_domain_list() {
        $this->db->select(array('agency_domain.*','COUNT(agency_domain_permissions.agency_domain_id) count_of_agent'))
            ->join('agency_domain_permissions','agency_domain_permissions.agency_domain_id = agency_domain.id','left')
            ->where('status !=', self::DOMAIN_STATUS_DELETED)
            ->group_by('agency_domain.id')
            ->from('agency_domain');
        return $this->runMultipleRowArray();
    }

    public function get_domain_list_by_agent_id($agent_id) {

        //all
        // $this->db->select('*');
        $this->db->from('agency_domain');
        $this->db->where('agency_domain.status', self::STATUS_NORMAL);
        $this->db->where('agency_domain.show_to_agent_type', self::SHOW_TO_AGENT_TYPE_ALL);
        $allAgents = $this->runMultipleRowArray();

        //batch only
        // $this->db->select('*');
        $this->db->from('agency_domain');
        $this->db->join('agency_domain_permissions', 'agency_domain_permissions.agency_domain_id = agency_domain.id');
        $this->db->where('agency_domain.status', self::STATUS_NORMAL);
        $this->db->where('agency_domain.show_to_agent_type', self::SHOW_TO_AGENT_TYPE_BATCH);
        $this->db->where('agency_domain_permissions.agent_id', $agent_id);
        $selectedAgents = $this->runMultipleRowArray();

        //parent
        // $this->db->select('*');
        $this->db->from('agency_domain');
        $this->db->join('agency_domain_permissions', 'agency_domain_permissions.agency_domain_id = agency_domain.id');
        $this->db->join('agency_agents', 'agency_agents.parent_id = agency_domain_permissions.agent_id');
        $this->db->where('agency_domain.status', self::STATUS_NORMAL);
        $this->db->where('agency_domain.show_to_agent_type', self::SHOW_TO_AGENT_TYPE_BATCH);
        $this->db->where('agency_agents.agent_id', $agent_id);
        $withSelectedParents = $this->runMultipleRowArray();

        $rows = array_merge(($allAgents ?: array()), ($selectedAgents ?: array()), ($withSelectedParents ?: array()));
        $this->utils->debug_log('GETAFFILIATEDOMAIN >------------------------> ', json_encode($rows));
        return $rows;

    }

    public function insert_agent_domain_permission($agent_domain_list){

        $this->db->insert_batch('agency_domain_permissions', $agent_domain_list);

        return true;
    }

    public function edit_agent_domain_permission($agency_domain_id, $agent_domain_list){

        $this->db->delete('agency_domain_permissions', ['agency_domain_id'=>$agency_domain_id]);

        if(!empty($agent_domain_list)){
            $this->db->insert_batch('agency_domain_permissions', $agent_domain_list);
        }

        return true;

    }

    public function add_domain($show_to_agent_type, $domainName,$notes, $adminUserId){

        $data=[
            'show_to_agent_type'=>$show_to_agent_type,
            'domain_name'=>$domainName,
            'notes'=>$notes,
            'status'=>self::STATUS_NORMAL,
            'created_at'=>$this->utils->getNowForMysql(),
            'created_by'=>$adminUserId,
            'updated_at'=>$this->utils->getNowForMysql(),
            'updated_by'=>$adminUserId,
        ];

        return $this->insertData('agency_domain', $data);
    }

    public function edit_domain($domain_id, $show_to_agent_type, $domainName, $notes, $adminUserId){

        $data=[
            'show_to_agent_type'=>$show_to_agent_type,
            'domain_name'=>$domainName,
            'notes'=>$notes,
            'updated_at'=>$this->utils->getNowForMysql(),
            'updated_by'=>$adminUserId,
        ];

        $this->db->set($data)->where('id', $domain_id);

        return $this->runAnyUpdate('agency_domain');
    }

    public function activateDomain($domain_id, $adminUserId){

        $this->db->set('status', self::STATUS_NORMAL)->set('updated_by', $adminUserId)
            ->set('updated_at', $this->utils->getNowForMysql())
            ->where('id', $domain_id);

        return $this->runAnyUpdate('agency_domain');

    }

    public function deactivateDomain($domain_id, $adminUserId){

        $this->db->set('status', self::STATUS_DISABLED)->set('updated_by', $adminUserId)
            ->set('updated_at', $this->utils->getNowForMysql())
            ->where('id', $domain_id);

        return $this->runAnyUpdate('agency_domain');

    }

    public function deleteDomain($domain_id, $adminUserId){

        $this->db->set('status', self::DOMAIN_STATUS_DELETED)->set('updated_by', $adminUserId)
            ->set('domain_name', 'concat("del",domain_name)', false)
            ->set('updated_at', $this->utils->getNowForMysql())
            ->where('id', $domain_id);

        return $this->runAnyUpdate('agency_domain');

    }

    public function is_unique_tracking_code($tracking_code, $agent_id){

        if(empty($agent_id)){

            $this->db->from('agency_agents')->where('tracking_code', $tracking_code);
            return !$this->runExistsResult();

        }else{

            $this->db->from('agency_agents')->where('tracking_code', $tracking_code)
                ->where('agent_id !=', $agent_id);
            return !$this->runExistsResult();

        }

    }

    public function get_tracking_code_from_agent_domain($agent_domain){

        $this->db->select('agency_agents.tracking_code')->from('agency_agents')
            ->join('agency_tracking_domain', 'agency_tracking_domain.agent_id=agency_agents.agent_id')
            ->where('agency_tracking_domain.tracking_type', self::TRACKING_TYPE_DOMAIN)
            ->where('agency_tracking_domain.tracking_domain', $agent_domain);

        return $this->runOneRowOneField('tracking_code');
    }

    public function get_agent_by_tracking_code($tracking_code){

        $this->db->from('agency_agents')->where('tracking_code', $tracking_code);

        return $this->runOneRowArray();
    }

    public function getBelongsAgentsById($domain_id){

        $this->db->select('agency_agents.agent_id, agency_agents.agent_name')
            ->from('agency_domain_permissions')
            ->join('agency_agents', 'agency_domain_permissions.agent_id=agency_agents.agent_id')
            ->where('agency_domain_permissions.agency_domain_id', $domain_id);

        return $this->runMultipleRowArray();
    }

    # Returns agent's top level parent
    public function get_top_parent_agent($agent) {
        $this->utils->debug_log("get_top_parent_agent for $agent[agent_name] (id: $agent[agent_id]; level: $agent[agent_level])");
        $max_loop = 10;
        do {
            if($agent['agent_level'] == 0) {
                $this->utils->debug_log("get_top_parent_agent return: $agent[agent_name] (id: $agent[agent_id]; level: $agent[agent_level])");
                return $agent;
            }

            $agent = $this->get_parent_agent($agent);
        } while ($max_loop-- > 0);
    }

    # Returns agent's parent agent, result is cached
    private $agents_by_child_id = array();
    private function get_parent_agent($agent) {
        if($agent['agent_level'] == 0) {
            return null;
        }

        if(!array_key_exists($agent['agent_id'], $this->agents_by_child_id)) {
            $this->db->from('agency_agents')->where('agent_id', $agent['parent_id']);
            $parent_agent = $this->runOneRowArray();
            $this->agents_by_child_id[$agent['agent_id']] = $parent_agent;
        }

        return $this->agents_by_child_id[$agent['agent_id']];
    }

    /**
     * Check if target is master's downline
     * @param   string  $master     username of master agent
     * @param   string  $target     username of target agent
     * @return  boolean     true if target is master's downline; otherwise false
     */
    public function is_downline_of($master_username, $target_username) {
        $master = $this->get_agent_by_name($master_username);
        $target = $this->get_agent_by_name($target_username);

        try {
            // Agent username checks
            if (empty($master)) {
                throw new Exception("master agent username '$master_username' invalid", 1);
            }

            if (empty($target)) {
                throw new Exception("target agent username '$target_username' invalid", 2);
            }

            $parent_id = $target['parent_id'];
            $this->utils->debug_log(__METHOD__, [ 'master' => [ $master_username, $master['agent_id'] ], 'target' => [ $target_username, $target['agent_id'] ], 'target_parent' => $parent_id ]);

            // target has no parent
            if ($parent_id == 0) {
                throw new Exception("target has no parent", 3);
            }

            // master is the direct parent of target
            if ($parent_id == $master['agent_id']) {
                throw new Exception("master is right target's parent", 0);
            }

            $gen = 10; // safe-guard of traversing
            while ($parent_id != 0 && $gen > 0) {
                $this->db->from('agency_agents')
                    ->where('agent_id', $parent_id)
                    ->select('parent_id')
                ;
                $parent_id = $this->runOneRowOneField('parent_id');
                // $this->utils->debug_log(__METHOD__, [ 'traversing' => $target['agent_id'], 'parent' => $parent_id, 'gen' => $gen ]);
                if ($parent_id == $master['agent_id']) {
                    throw new Exception("master is among target's ancestry", 0);
                }
                --$gen;
            }

            throw new Exception("master is not among target's ancestry", 4);
        }
        catch (Exception $ex) {
            $code = $ex->getCode();
            $ret = $code > 0 ? false : true;
            $this->utils->debug_log(__METHOD__, 'code', $ex->getCode(), 'mesg', $ex->getMessage());
        }
        finally {
            return $ret;
        }
    } // End function is_downline_of()

    public function generatePermission($permissions, $perm_name, $parent_agent){

        $perm=false;

        if(isset($permissions[$perm_name])){
            if(!empty($parent_agent)){
                //only parent allow
                if($parent_agent[$perm_name]==self::DB_TRUE){
                    $perm=$permissions[$perm_name];
                }
            }else{
                //top agent
                $perm=$permissions[$perm_name];
            }

        }

        return $perm;

    }

    /**
     * create agent with base info
     *
     * @param  string  $agent_name          unique agent name
     * @param  string  $password            plain password, unencrypted
     * @param  string  $currency            currency string, like CNY
     * @param  integer $parent_id           parent agent id
     * @param  array  $credit_settings credit_limit, available_credit
     * @param  array  $commission_settings rev_share, rolling_comm, rolling_comm_basis, settlement_period, settlement_start_day
     * @param  array  $permissions         can_have_sub_agent, can_have_players, show_bet_limit_template, show_rolling_commission, generate_merchant, merchant_live_mode
     * @param  array $vip_settings  player_vip_groups: array, player_vip_levels: array
     * @param  array $extra_info any other fields
     * @return int                       agent id
     */
    public function createBaseAgent($agent_name, $password, $status, $currency=null, $parent_id=0, $credit_settings=null,
            $commission_settings=null, $permissions=null, $vip_settings=null, $extra_info=null){

        $this->db->select('agent_id')->from('agency_agents')->where('agent_name', $agent_name);
        if($this->runExistsResult()){
            return false;
        }

        $today=$this->utils->getNowForMysql();
        $merchant_name=$agent_name;
        $agent_level=0;
        $parent_agent=null;
        $credit_limit=null;
        $available_credit=null;

        if(!$this->isValidStatus($status)){
            //default status is frozen
            $status=self::AGENT_STATUS_FROZEN;
        }

        if($parent_id>0){
            //will overwrite $currency, and generate agent_level
            $parent_agent=$this->get_agent_by_id($parent_id);
            if(!empty($parent_agent)){
                $this->utils->error_log('wrong parent id');
                return false;
            }

            $currency=$parent_agent['currency'];
            $agent_level=$parent_agent['agent_level']+1;

        }

        if(empty($currency)){
            $currency=$this->utils->getDefaultCurrency();
        }

        //credit settings, default is 0
        $credit_limit=isset($credit_settings['credit_limit']) ? $credit_settings['credit_limit'] : 0;
        //default is credit_limit
        $available_credit=isset($credit_settings['available_credit']) ? $credit_settings['available_credit'] : $credit_limit;

        //commission settings
        $rev_share=isset($commission_settings['rev_share']) ? $commission_settings['rev_share'] : 0;
        $rolling_comm=isset($commission_settings['rolling_comm']) ? $commission_settings['rolling_comm'] : 0;
        $rolling_comm_basis=isset($commission_settings['rolling_comm_basis']) ? $commission_settings['rolling_comm_basis'] : self::AGENT_ROLLING_MODE_TOTAL_BETS_EXCEPT_TIE;
        $settlement_period=isset($commission_settings['settlement_period']) ? $commission_settings['settlement_period'] : self::SETTLEMENT_PERIOD_WEEKLY;
        $settlement_start_day=isset($commission_settings['settlement_start_day']) ? $commission_settings['settlement_start_day'] : 0;

        //vip settings
        $player_vip_groups=isset($vip_settings['player_vip_groups']) ? implode(',',$vip_settings['player_vip_groups']) : '';
        $player_vip_levels=isset($vip_settings['player_vip_levels']) ? implode(',',$vip_settings['player_vip_levels']) : '';

        $data = [

            'agent_name' => $agent_name,
            'merchant_name' => $merchant_name,
            'password' => $this->utils->encodePassword($password),
            'currency' => $currency,
            'credit_limit' => $credit_limit,
            'available_credit' => $available_credit,
            'status' => $status,
            'active' => $status == self::AGENT_STATUS_ACTIVE ? self::DB_TRUE : self::DB_FALSE,

            'rev_share' => $rev_share,
            'rolling_comm' => $rolling_comm,
            'rolling_comm_basis' => $rolling_comm_basis,
            'settlement_period' => $settlement_period,
            'settlement_start_day' => $start_day,

            'total_bets_except' => '',
            'agent_level' => $agent_level,
            'agent_level_name' => '',
            'can_have_sub_agent' => $this->generatePermission($permissions, 'can_have_sub_agent', $parent_agent),
            'can_have_players' => $this->generatePermission($permissions, 'can_have_players', $parent_agent),
            'show_bet_limit_template' => $this->generatePermission($permissions, 'show_bet_limit_template', $parent_agent),
            'show_rolling_commission' => $this->generatePermission($permissions, 'show_rolling_commission', $parent_agent),

            'vip_groups' => $player_vip_groups,
            'vip_levels' => $player_vip_levels,

            'created_on' => $today,
            'updated_on' => $today,
            'parent_id' => $parent_id,

        ];

        if(isset($permissions['generate_merchant']) && $permissions['generate_merchant']){

            $data['live_mode']= isset($permissions['merchant_live_mode']) ? $permissions['merchant_live_mode'] : self::DB_FALSE;
            $data['live_sign_key']=random_string('md5');
            $data['staging_sign_key']=random_string('md5');
            $data['live_secure_key']=random_string('md5');
            $data['staging_secure_key']=random_string('md5');

        }

        if(!empty($extra_info)){
            $data=array_merge($data, $extra_info);
        }

//        $this->utils->debug_log($data);

        $agent_id = $this->add_agent($data);

        return $agent_id;

    }

    /**
     * create agent from player
     *
     * @param  [type] $playerId [description]
     * @return [type]           [description]
     */
    public function createAgentFromPlayer($playerId){

        $this->load->model(['player_model', 'group_level']);
        $player=$this->player_model->getPlayerArrayById($playerId);

        if(empty($player)){
            return false;
        }

        //add prefix to username
        $agent_name=$this->genereateAgentNameFromPlayerUsername($player['username']);
        //same password
        $password=$this->utils->decodePassword($player['password']);
        $status=self::AGENT_STATUS_ACTIVE;
        //same currency
        $currency=$player['currency'];
        //same parent agent id
        $parent_id=$player['agent_id'];
        $credit_settings=null;
        $commission_settings=null;
        $permissions=null;
        //same vip
        $level=$this->group_level->getLevelById($player['levelId']);
        $vip_settings=['player_vip_groups'=>[$level['vipSettingId']], 'player_vip_levels'=>[$player['levelId']]];
        //add binding player id
        $extra_info=['binding_player_id'=>$playerId];

        return $this->createBaseAgent($agent_name, $password, $status, $currency, $parent_id,
            $credit_settings, $commission_settings, $permissions, $vip_settings, $extra_info);

    }

    public function genereateAgentNameFromPlayerUsername($username){
        //get default prefix
        $prefix_of_agent_name=$this->utils->getConfig('prefix_of_agent_name');

        $agent_name=$prefix_of_agent_name.$username;

        $exists=true;
        while ($exists) {
            //check if exists
            $agent_id=$this->get_agent_id_by_agent_name($agent_name);
            $exists=!empty($agent_id);

            if($exists){
                //try add 2 random number
                $agent_name=$agent_name.random_string('numeric', 2);
            }
        }

        return $agent_name;
    }

    public function has_player_permission($agent_id, $player_id){
        $this->db->select('playerId')->from('player')->where('playerId', $player_id)->where('agent_id', $agent_id);

        return $this->runExistsResult();
    }

    public function getAgentNameById($id){
        $this->db->select('agent_name')->from('agency_agents')->where('agent_id', $id);
        return $this->runOneRowOneField('agent_name');
    }

    /**
     * getT1LotteryAdditionalInfo
     * @param  string $agent_tracking_code. from registration link
     * @param  string $agent_tracking_source_code from registration link
     * @return array $additionalInfo
     */
    public function getT1LotteryAdditionalInfo($agent_tracking_code, $agent_tracking_source_code){

        $additionalInfo=null;
        //check root tracking code
        $top_agent_config=$this->utils->getConfig('top_agent_config');
        if($top_agent_config['tracking_code']==$agent_tracking_code){

            $additionalInfo=$this->getT1LotteryAdditionalInfoFromTopSettings($top_agent_config);

        }else{

            $result = $this->getSourceCodeFromTrackingCode($agent_tracking_code, $agent_tracking_source_code);
            if($result !== FALSE){
                list($agent, $agent_tracking_source_code_data) = $result;

                $parent_agent_username=null;
                // $agent_id=$agent['agent_id'];
                if(!empty($agent)){
                    $binding_player_id=$agent['binding_player_id'];
                    $login_info=$this->game_provider_auth->getLoginInfoByPlayerId($binding_player_id, T1LOTTERY_API);
                    if(!empty($login_info)){
                        $parent_agent_username=$login_info->login_name;
                    }
                    //try get username
                    // $parent_agent_username=$this->getAgentNameById($parent_id);
                }

                $additionalInfo = [
                    'bonus_rate' => $agent_tracking_source_code_data['bonus_rate'],
                    'rebate_rate' => $agent_tracking_source_code_data['rebate_rate'],
                    'player_type' => $agent_tracking_source_code_data['player_type'],
                    'parent_agent_username' => $parent_agent_username
                ];
            }

        }

        return $additionalInfo;
    }

    public function getT1LotteryAdditionalInfoFromTopSettings($top_agent_config){
        return [
            'bonus_rate'=>$top_agent_config['bonus_rate'],
            'rebate_rate'=>$top_agent_config['rebate_rate'],
            'player_type'=>$top_agent_config['player_type'],
            'parent_agent_username'=>null
        ];
    }

    public function registerPlayerToAgent($player_id, $agent_tracking_code, $agent_tracking_source_code){
        // $this->load->model([ 'agency_model' ]);

        if(!$this->utils->isEnabledFeature('enable_auto_binding_agency_agent_on_player_registration')){
            return FALSE;
        }
        $this->load->model(['game_provider_auth', 'player_model']);

        //if it's top agent code
        $top_agent_config=$this->utils->getConfig('top_agent_config');
        $is_root_code=$top_agent_config['tracking_code']==$agent_tracking_code;
        $agent_parent_id=null;
        $agent_template_id=null;
        if($is_root_code){
            //check type
            if(intval(@$top_agent_config['player_type'])===AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER){
                return false;
            }
            $default_status=Agency_model::AGENT_STATUS_ACTIVE;
            $agent_template_id=$top_agent_config['agent_template_id'];
            $apiId=T1LOTTERY_API;
            //if it's root, create t1 account
            $api=$this->utils->loadExternalSystemLibObject($apiId);
            if(!empty($api)){
                $playerInfo=$this->player_model->getPlayerArrayById($player_id);

                $decryptedPwd = $this->salt->decrypt($playerInfo['password'], $this->getDeskeyOG());
                $rlt=$api->createPlayer($playerInfo['username'], $player_id, $decryptedPwd);
                if(!$rlt['success']){
                    $this->utils->error_log('create t1 lottery account failed', $rlt);
                }else{
                    $rlt=$this->game_provider_auth->setRegisterFlag($player_id, $apiId, Game_provider_auth::DB_TRUE);
                    if(!$rlt){
                        $this->utils->error_log('set register flag failed', $rlt);
                    }
                }
            }else{
                $this->utils->debug_log('ignore create t1 lottery account');
            }
        }else{
            $result = $this->agency_model->getSourceCodeFromTrackingCode($agent_tracking_code, $agent_tracking_source_code);
            if($result === FALSE){
                return FALSE;
            }
            list($agent, $agent_tracking_source_code_data) = $result;

            if(AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER === (int)$agent_tracking_source_code_data['player_type']){
                return FALSE;
            }
            $default_status=Agency_model::AGENT_STATUS_ACTIVE;
            $agent_parent_id=$agent['agent_id'];
        }

        return $this->agency_model->convertPlayerToAgent($player_id, null, $agent_parent_id, $default_status, $agent_template_id);
    }

    public function convertPlayerToAgent($player_id, $agent_username = NULL, $agent_parent_id = NULL, $status='active', $agent_template_id=null){
        $this->load->model([ 'player_model' ]);

        $auto_create_agent_options = $this->utils->getConfig('agency_auto_create_from_player');

        // Fetch player
        $player = $this->player_model->getPlayerDetailsTagsById($player_id);

        if(empty($player)){
            return ERROR_PLAYER_NOT_EXISTS;
        }

        $agency_agent = $this->agency_model->get_agent_by_binding_player_id($player_id);
        if(!empty($agency_agent)){
            return ERROR_AGENT_ALREADY_BINDING;
        }

        $agent_username = (empty($agent_username)) ? $auto_create_agent_options['username_prefix'] . $player['username'] : $agent_username;

        // Skip validation
        $this->utils->debug_log('convertPlayerToAgent', 'agent reg start');

        // Determine tracking code

        // $trackingCode = $this->generateRandomCode(8);// str_replace(['+', '_'], ['', ''], $agent_username);
        $trackingCode = $this->utils->isEnabledFeature('agency_tracking_code_numbers_only') ? $this->utils->randomString(8) : $this->utils->generateRandomCode(8);
        while(!$this->agency_model->is_unique_tracking_code($trackingCode, NULL)){
            $trackingCode = $this->utils->isEnabledFeature('agency_tracking_code_numbers_only') ? $this->utils->randomString(8) : $this->utils->generateRandomCode(8);
        }

        $this->utils->debug_log('convertPlayerToAgent', 'agent tracking code', $trackingCode);

        // Determine parent ID
        $agent_level = 0;
        $parentId = (empty($player['agent_id'])) ? $auto_create_agent_options['default_parent_agent_id'] : $player['agent_id'];
        if (!empty($agent_parent_id)) {
            if ($parentId > 0){
                $parentAgent = $this->agency_model->get_agent_by_id($agent_parent_id);
                $this->utils->debug_log('add_new_agent TTT parentId, parentAgent', $agent_parent_id, $parentAgent);

                if(!empty($parentAgent)){
                    $parentLevel = $parentAgent['agent_level'];
                    $agent_level = $parentLevel + 1;
                }else{
                    $parentId = $auto_create_agent_options['default_parent_agent_id'];
                    $agent_level = 0;
                }
            }
        }
        $this->utils->debug_log('convertPlayerToAgent', 'agent parent ID', $parentId);

        $data=[];
        $structure_game_platforms=null;
        $structure_game_types=null;
        //try copy from template
        if(!empty($agent_template_id)){
            list($data, $structure_game_platforms, $structure_game_types)=$this->agency_model->copyTemplateToAgent($agent_template_id);
        }else{
            // Construct agent dataset
            $data = array(
                'binding_player_id' => $player_id,
                'parent_id'     => $parentId,
                'agent_level'   => $agent_level,
                'agent_name'    => $agent_username ,
                'password'      => $player['password'] ,
                'firstname'     => $player['firstName'] ,
                'lastname'      => $player['lastName'] ,
                'gender'        => $player['gender'] ,
                'email'         => $player['email'] ,
                'mobile'        => $player['contactNumber'],
                'im1'           => $player['imAccount'] ,
                'im2'           => $player['imAccount2'] ,
                'currency'      => $player['currency'] ,
                'status'        => $status,
                'active'        => $status=='active' ? '1' : '0',
                'created_on'    => $this->utils->getNowForMysql(),
                'tracking_code' => $trackingCode,
                'language'      => $player['language'] ,
                'note'          => "{$player['notes']}; agent registered by auto create;" ,
                'can_have_sub_agent'        => $auto_create_agent_options['can_have_sub_agent'],
                'can_have_players'          => $auto_create_agent_options['can_have_players'],
                'show_bet_limit_template'   => $auto_create_agent_options['show_bet_limit_template'],
                'show_rolling_commission'   => $auto_create_agent_options['show_rolling_commission'],
                'can_view_agents_list_and_players_list' => $auto_create_agent_options['can_view_agents_list_and_players_list'],
                'settlement_period'     => $auto_create_agent_options['settlement_period'],
                'settlement_start_day'  => $auto_create_agent_options['settlement_start_day'],
            );
        }

        // Skip agent templates (See addNewAgency(), register_use_default_template(),
        // copy_settings_from_template() unde /agency/agency.php)

        // The main event

        $agent_id = $this->agency_model->add_agent($data);
        $success = !empty($agent_id);

        if(!$success){
            $this->utils->debug_log('convertPlayerToAgent agent reg failed');

            return ERROR_AGENT_REGISTRATION_FAILED;
        }
        $this->player_model->setPlayerAgent($player_id, $agent_id);
        //try update game settings
        if(!empty($structure_game_platforms) || !empty($structure_game_types)){

            $success=$this->agency_model->add_game_comm_settings($structure_game_platforms, $structure_game_types,
                $agent_id, 'agent');
            if(!$success){
                $this->utils->error_log('convertPlayerToAgent agent update game failed', $structure_game_platforms, $structure_game_types);

                return ERROR_AGENT_REGISTRATION_FAILED;
            }
        }

        $this->utils->debug_log('convertPlayerToAgent agent reg successful', 'new agent', [$agent_id, $agent_username]);

        return [
            'agent_id' => $agent_id,
            'agent' => $this->agency_model->get_agent_by_id($agent_id)
        ];
    }

    public function copyTemplateToAgent($structure_id){

        $structure_details = $this->agency_model->get_structure_by_id($structure_id);

        $agent_type=[];
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

        $agent=[
            'structure_id' => $structure_details['structure_id'],
            'agent_name' => '',
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
        ];

        $structure_game_platforms = $this->agency_model->get_structure_game_platforms($structure_id);
        $structure_game_types = $this->agency_model->get_structure_game_types($structure_id);
        // $data['game_platform_settings']['conditions']['game_platforms'] = array_column($structure_game_platforms, NULL, 'game_platform_id');
        // $data['game_platform_settings']['conditions']['game_types'] = array_column($structure_game_types, NULL, 'game_type_id');

        return [$agent, $structure_game_platforms, $structure_game_types];
    }

    /**
     *  process and save game commission settings
     *
     *  @param  array game_platforms
     *  @param  array game_types
     *  @param  int   id   agent id or player_id
     *  @param  string type 'agent' or 'player'
     *  @return boolean  true for success
     */
    public function add_game_comm_settings($game_platforms, $game_types, $id, $type='agent', $is_update = false) {

        $id_name = 'structure_id';
        $game_platform_table = 'agency_structure_game_platforms';
        $game_type_table = 'agency_structure_game_types';

        if ($type == 'agent') {
            $id_name = 'agent_id';
            $game_platform_table = 'agency_agent_game_platforms';
            $game_type_table = 'agency_agent_game_types';
        } elseif ($type == 'player') {
            $id_name = 'player_id';
            $game_platform_table = 'agency_player_game_platforms';
            $game_type_table = 'agency_player_game_types';
        }

        if ($is_update) {
            $this->db->delete($game_platform_table, array($id_name => $id));
            $this->db->delete($game_type_table, array($id_name => $id));
        }

        if (! empty($game_platforms)) {
            $game_platform_data = array();
            $this->utils->debug_log('post GAME_PLATFORMS param', $game_platforms);
            $game_platforms = array_filter($game_platforms,
                function($game_platform, $game_platform_id) use ($id, $id_name, &$game_platform_data) {
                $enabled = $game_platform['enabled'];
                if ($enabled) {
                    $game_platform_data[] = array(
                        $id_name => $id,
                        'game_platform_id' => $game_platform_id,
                    );
                }
                return $enabled;
            }, ARRAY_FILTER_USE_BOTH );

            $this->utils->debug_log('update GAME_PLATFORMS:', $game_platform_data);

            if(!empty($game_platforms)){
                $this->db->insert_batch($game_platform_table, $game_platform_data);
            }
        }

        # UPDATE GAME TYPES
        if (! empty($game_types)) {
            $this->utils->debug_log('post GAME_TYPES param', $game_types);
            // $controller = $this;
            $game_types = array_filter($game_types, function(&$game_type, $game_type_id) use ($id, $id_name) {
                $enabled = false;
                if ($this->utils->isEnabledFeature('agent_tier_comm_pattern')) {
                    $enabled = isset($game_type['pattern_id']);
                } else {
                    # If this is not set to true, new record will never get inserted
                    $enabled = isset($game_type['rolling_comm']) && isset($game_type['rolling_comm_basis']);
                }
                if ($enabled){
                    $game_type['game_type_id'] = $game_type_id;
                    $game_type[$id_name] = $id;
                    if ($this->utils->isEnabledFeature('agent_tier_comm_pattern')) {
                        if(isset($game_type['pattern_id']) && $game_type['pattern_id'] > 0){
                            $pattern = $this->agency_model->get_tier_comm_pattern($game_type['pattern_id']);
                            $game_type['rev_share'] = $pattern['rev_share'];
                            $game_type['rolling_comm_basis'] = $pattern['rolling_comm_basis'];
                            $game_type['rolling_comm'] = $pattern['rolling_comm'];
                            $game_type['bet_threshold'] = $pattern['min_bets'];
                        } else {
                            $this->utils->debug_log('pattern_id EXCEPTION. GAME_TYPES:', $game_types);
                        }
                    }
                }
                return $enabled;
            }, ARRAY_FILTER_USE_BOTH );

            $this->utils->debug_log('update GAME_TYPES:', $game_types);

            if(!empty($game_types)){
                $this->db->insert_batch($game_type_table, $game_types);
            }
        }
        return true;
    }

    public function get_player_id_array_by_agent_id_array($agent_id_array) {
        $this->db->select('playerId')->from('player')->where_in('agent_id', $agent_id_array)
            ->where('deleted_at IS NULL', null, false);

        $result = [];
        $rows=$this->runMultipleRowArrayUnbuffered();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $result[] = $row['playerId'];
            }
        }
        unset($rows);
        return $result;
    }

    # Sum the bets_display of agent's settlement record in current settlement period,
    # up to the given date
    # Note: The calculation requires enabling 'settlement_include_all_downline' to work well with tier comm
    public function get_agent_bets_to_date($agent, $settlement_date) {
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

        $this->db->select_sum('bets_display', 'bets_to_date')
            ->from('agency_daily_player_settlement')
            ->where('agent_id', $agent['agent_id']);

        if($agent['settlement_period'] == 'Weekly') {
            $settlement_start_day = $agent['settlement_start_day'];
            $week_day = date('w', strtotime($settlement_date));

            # calculate the first day of current settlement period
            $minus_days = sprintf("%d", (- $week_day - (7 - $start_day[$settlement_start_day])) % 7);
            $first_day_of_settlement = date("Y-m-d", strtotime($minus_days.' days', strtotime($settlement_date)));

            $this->db->where("settlement_date >=", $first_day_of_settlement);
            $this->utils->debug_log("get_agent_bets_to_date: weekly settlement period, start day = ", $settlement_start_day);
        } else {
            # Treat all other cases as Monthly, as that's the only two modes we support for tier at the moment
            $first_day_of_month = date("Y-m", strtotime($settlement_date)).'-01';
            $this->db->where("settlement_date >=", $first_day_of_month);
        }

        $this->db->where("settlement_date <=", $settlement_date);

        $bets_to_date = $this->runOneRowOneField('bets_to_date');
        $this->utils->debug_log("get_agent_bets_to_date: ", $this->db->last_query(), $bets_to_date);
        return $bets_to_date;
    }

    public function getAgentIdByUsername($agent_name) {
        $this->db->from('agency_agents')->where('agent_name', $agent_name);
        return $this->runOneRowOneField('agent_id');
    }

    public function addAgentGames($gamePlatformArr){
        $result = $this->db->insert_batch('agency_agent_game_platforms', $gamePlatformArr);
        return $result;
    }

    public function addAgentGameTypes($gameTypesArr){
        $result = $this->db->insert_batch('agency_agent_game_types', $gameTypesArr);
        return $result;
    }

    public function getAgentNameIdMap() {
        $agentMap=[];
        // $this->utils->debug_log("Player Model getPlayerIdByUsername: ", $username, " Table: ", $this->tableName);
        $this->db->select('agent_name,agent_id')->from($this->tableName);
        $rows=$this->runMultipleRowArrayUnbuffered();
        if(!empty($rows)){
            foreach ($rows as $row) {
                $agentMap[$row['agent_name']]=$row['agent_id'];
            }
        }
        unset($rows);
        return $agentMap;
    }

    /**
     * getPlayerPrefixByAgentId
     * @param  int $agentId
     * @return string $playerPrefix
     */
    public function getPlayerPrefixByAgentId($agentId){
        $playerPrefix=null;
        if(!empty($agentId)){
            $this->db->select('player_prefix')->from($this->tableName)->where('agent_id', $agentId);
            $playerPrefix=$this->runOneRowOneField('player_prefix');
        }
        return $playerPrefix;
    }

    /**
     * getPrefixSettingsByAgentId
     * @param  int $agentId
     * @return array [
     *     game platform id => ['system_code'=>, 'prefix'=>]
     * ]
     */
    public function getPrefixSettingsByAgentId($agentId){
        $playerPrefix=$this->getPlayerPrefixByAgentId($agentId);

        $this->load->model(['external_system']);
        $apiList=$this->external_system->mapGameApi($playerPrefix);

        $this->db->from('agency_prefix_for_game_account')->where('agent_id', $agentId);
        $rows=$this->runMultipleRowArray();
        if(!empty($rows)){
            foreach ($rows as $row) {
                if(isset($apiList[$row['game_platform_id']])){
                    $apiList[$row['game_platform_id']]['prefix']=$row['prefix'];
                }
            }
        }

        return $apiList;
    }

    public function getPlayerPrefixByAgentIdAndGamePlatformId($agentId, $gamePlatformId) {
        $prefix=null;
        if(!empty($agentId)){
            $this->db->select('prefix')->from('agency_prefix_for_game_account')->where('agent_id', $agentId)
                ->where('game_platform_id', $gamePlatformId);
            $row=$this->runOneRowArray();
            if(empty($row)){
                $this->db->select('player_prefix')->from($this->tableName)->where('agent_id', $agentId);
                $prefix=$this->runOneRowOneField('player_prefix');
            }else{
                $prefix=$row['prefix'];
            }
        }

        return $prefix;
    }

    // public function initPrefixForGameAccount($agentId){
    //     $playerPrefix=$this->getPlayerPrefixByAgentId($agentId);

    //     $this->load->model(['external_system']);
    //     $apiList=$this->external_system->mapGameApi($playerPrefix);
    //     if(!empty($apiList)){
    //         $this->db->from('agency_prefix_for_game_account')->where('agent_id', $agentId);
    //         $rows=$this->runMultipleRowArray();

    //         foreach ($apiList as $gamePlatformId=>$apiInfo) {
    //             $found=false;
    //             if(!empty($rows)){
    //                 foreach ($rows as $row) {
    //                     if($row['game_platform_id']==$gamePlatformId){
    //                         $found=true;
    //                         break;
    //                     }
    //                 }
    //             }
    //             if(!$found){
    //                 //write record
    //                 $data=[
    //                     'agent_id'=>$agentId,
    //                     'game_platform_id'=>$gamePlatformId,
    //                     'prefix'=>$playerPrefix,
    //                     'created_at'=>$this->utils->getNowForMysql(),
    //                     'updated_at'=>$this->utils->getNowForMysql(),
    //                 ];
    //                 $success=$this->insertData('agency_prefix_for_game_account', $data);
    //                 if($success===false){
    //                     $this->utils->error_log('insert agency_prefix_for_game_account failed', $data);
    //                 }
    //             }
    //         }
    //     }

    // }

    public function getPrefixMapForGameAccount($agentId){
        $playerPrefix=$this->getPlayerPrefixByAgentId($agentId);

        $this->load->model(['external_system']);
        $result=$this->external_system->mapGameApi($playerPrefix);
        // if(!empty($result)){
        //     $this->db->select('agent_id, game_platform_id, prefix')->from('agency_prefix_for_game_account')->where('agent_id', $agentId);
        //     $rows=$this->runMultipleRowArray();

        //     foreach ($result as $gamePlatformId=>&$apiInfo) {
        //         if(!empty($rows)){
        //             foreach ($rows as $row) {
        //                 if($row['game_platform_id']==$gamePlatformId){
        //                     $apiInfo['prefix']=$row['prefix'];
        //                     break;
        //                 }
        //             }
        //         }
        //     }
        // }

        return $result;

    }

    public function getGamePlatformByAgentId($agent_id) {
        $this->load->model(['external_system']);
        $this->db->select("external_system.*")->from('external_system')
            ->join('agency_agent_game_platforms', 'agency_agent_game_platforms.game_platform_id=external_system.id')
            ->where('agency_agent_game_platforms.agent_id', $agent_id)
            ->where('external_system.status', self::STATUS_NORMAL)
            ->where('external_system.system_type', External_system::SYSTEM_GAME_API);

        return $this->runMultipleRowArray();
    }

    /**
     *
     * sync merchant info
     *
     * @param  string $merchant_code
     * @param  double $credit_limit
     * @param  double $available_credit
     * @param  array $extra_info
     * @return
     */
    public function syncMerchant($merchant_code, $credit_limit, $available_credit, array $extra_info=[]){
        // $agent_level=0, $parent_id=0, $rev_share=1, $rolling_comm=0, $settlement_period='Weekly', $start_day='',
        //     $currency='CNY', $live_mode=self::DB_FALSE, $player_vip_groups=[], $player_vip_levels=[]

        $agentId=$this->getAgentIdByUsername($merchant_code);
        $data=[
            'rev_share'=>1, 'rolling_comm'=>0, 'settlement_period'=> 'Manual',
            'credit_limit'=>$credit_limit, 'available_credit'=>$available_credit,
        ];
        if(!empty($agentId)){
            $this->db->set($data)->where('agent_id', $agentId);
            $this->runAnyUpdate('agency_agents');
        }else{
            $this->runInsertData('agency_agents', $data);
        }
    }

    /**
     * load readonly sub account
     * @param  int $agentId
     * @return array
     */
    public function loadReadonlySubAccount($agentId){
        $this->db->select('readonly_sub_account')->from('agency_agents')
          ->where('agent_id', $agentId);

        $json=$this->runOneRowOneField('readonly_sub_account');
        if(!empty($json)){
            return $this->utils->decodeJson($json);
        }else{
            return [];
        }
    }

    public function buildEmptyReadonlySubAccount(){
        return ['username'=>null, 'password'=>null, 'enabled'=>null];
    }

    /**
     * searchReadonlySubAccount
     * @param  int $agentId
     * @param  string $usernameOfReadonlyAccount
     * @return
     */
    public function searchReadonlySubAccount($agentId, $usernameOfReadonlyAccount){
        $accounts=$this->loadReadonlySubAccount($agentId);
        if(!empty($accounts)){
            foreach ($accounts as $acc) {
                if($acc['username']==$usernameOfReadonlyAccount){
                    return $acc;
                }
            }
        }

        return null;
    }

    public function saveReadonlySubAccount($agentId, $readonlyAccountList){
        $data=[
            'readonly_sub_account'=>$this->utils->encodeJson($readonlyAccountList),
        ];
        $this->db->where('agent_id', $agentId)->set($data);
        return $this->runAnyUpdate('agency_agents');
    }

    public function loginByReadonlySubAccount($agentUsername, $readonlyAccount, $password, &$error=null) {
        if(empty($agentUsername) || empty($readonlyAccount) || empty($password)){
            //failed
            return null;
        }
        $this->utils->debug_log('check login readonly sub-account', $agentUsername, $readonlyAccount, $password);

        $this->db->from('agency_agents')->where('agent_name', $agentUsername);
        $result=$this->runOneRowArray();
        if(!empty($result)){
            //found agent
            //search readonly account
            $readonly_sub_account=$result['readonly_sub_account'];
            if(!empty($readonly_sub_account)){
                $accounts=$this->utils->decodeJson($readonly_sub_account);
                if(!empty($accounts)){
                    foreach ($accounts as $idx=>$acc) {
                        if($acc['username']==$readonlyAccount){
                            if(!$acc['enabled']){
                                $error=lang('This readonly sub-account is disabled');
                                return null;
                            }
                            //decode password
                            $decryptedPassword=$this->utils->decryptPassword($acc['password'], $error);
                            $this->utils->debug_log('decrpted password', $decryptedPassword, $acc['username']);
                            if($decryptedPassword!==false && $decryptedPassword==$password){
                                //found
                                $this->utils->debug_log('found right readonly account', $acc);
                            }else{
                                if(!empty($error)){
                                    $this->utils->error_log('Decrypt password is failed', $acc['username']);
                                }
                                //password failed
                                $error=lang('Password is incorrect');
                                return null;
                            }
                        }
                    }
                    if(empty($result)){
                        $error=lang('Not found readonly sub-account');
                    }
                }else{
                    $error=lang('Not found readonly sub-account');
                }
            }
        }

        return $result;
    }

    /**
     * return no_prefix_on_username and player_prefix by agent id
     * @param  int $agentId
     * @return array
     */
    public function getNoPrefixInfoAndPrefixByAgentId($agentId){
        if(!empty($agentId)){
            $this->db->select('no_prefix_on_username, player_prefix')->from('agency_agents')
              ->where('agent_id', $agentId);
            $row=$this->runOneRowArray();
            if(!empty($row)){
                return [$row['no_prefix_on_username'], $row['player_prefix']];
            }
        }
        //not found
        return [null, null];
    }

    public function getIdListByAgentNameList(array $agentNameList){
        $this->db->select('agent_id, agent_name')->from('agency_agents')
            ->where_in('agent_name', $agentNameList);

        $rows=$this->runMultipleRowArray();
        $result=[];
        foreach ($rows as $row) {
            $result[$row['agent_id']]=$row;
        }
        return $result;
    }

    public function resetKeyAndPasswordByIdList(array &$agentIdList){

        foreach ($agentIdList as $agentId=>&$info) {
            $live_sign_key=random_string('md5');
            $staging_sign_key=random_string('md5');
            $live_secure_key=random_string('md5');
            $staging_secure_key=random_string('md5');
            $password=random_string('alnum', 6);

            $data=[
                'live_sign_key'=>$live_sign_key,
                'staging_sign_key'=>$staging_sign_key,
                'live_secure_key'=>$live_secure_key,
                'staging_secure_key'=>$staging_secure_key,
                'password'=>$this->utils->encodePassword($password),
            ];
            $this->db->set($data)
              ->where_in('agent_id', $agentId);
            $this->runAnyUpdate('agency_agents');
            $info['live_sign_key']=$live_sign_key;
            $info['staging_sign_key']=$staging_sign_key;
            $info['password']=$password;
        }

    }

}
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agency_model.php
