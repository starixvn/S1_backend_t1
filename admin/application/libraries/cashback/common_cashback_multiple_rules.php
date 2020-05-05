<?php
/**
 * common_cashback_multiple_rules.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
class Common_Cashback_multiple_rules {
    const COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_PLATFORM = 'game_platform';
    const COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_TYPE = 'game_type';
    const COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME = 'game';

    /* @var BaseController */
    public $CI;

    /* @var Group_level */
    public $group_level;

    /* @var Game_type_model */
    public $game_type_model;

    /* @var Game_description_model */
    public $game_description_model;

    /* @var Common_cashback_multiple_range_templates_model */
    public $common_cashback_multiple_range_templates_model;

    /* @var Common_cashback_multiple_range_settings_model */
    public $common_cashback_multiple_range_settings_model;

    /* @var Common_cashback_multiple_range_rules_model */
    public $common_cashback_multiple_range_rules_model;

    public function __construct(){
        $this->CI =& get_instance();

        $this->CI->load->model(['group_level', 'game_type_model', 'game_description_model', 'common_cashback_multiple_range_templates_model', 'common_cashback_multiple_range_settings_model', 'common_cashback_multiple_range_rules_model']);

        $this->group_level = $this->CI->group_level;
        $this->game_type_model = $this->CI->game_type_model;
        $this->game_description_model = $this->CI->game_description_model;
        $this->common_cashback_multiple_range_templates_model = $this->CI->common_cashback_multiple_range_templates_model;
        $this->common_cashback_multiple_range_settings_model = $this->CI->common_cashback_multiple_range_settings_model;
        $this->common_cashback_multiple_range_rules_model = $this->CI->common_cashback_multiple_range_rules_model;

        $this->cashback_common_settings = NULL;
        $this->cashback_settings_with_game_data = NULL;
        $this->all_vip_level = NULL;
        $this->level_cashback_map = NULL;
    }

    protected function _sortCashbackRules(&$cashback_rules){
        uasort($cashback_rules, function($entry_a, $entry_b){
            if($entry_a['min_bet_amount'] == $entry_b['min_bet_amount']){
                return 0;
            }
            return ($entry_a['min_bet_amount'] > $entry_b['min_bet_amount']) ? 1 : -1;
        });

        return $cashback_rules;
    }

    protected function _formateCashbackRule(&$rule_entry){
        $rule_entry['min_bet_amount'] = number_format((float)$rule_entry['min_bet_amount'], 0, '.', '');
        $rule_entry['max_bet_amount'] = number_format((float)$rule_entry['max_bet_amount'], 0, '.', '');
        $rule_entry['cashback_percentage'] = number_format((float)$rule_entry['cashback_percentage'], 3, '.', '');
        $rule_entry['max_cashback_amount'] = number_format((float)$rule_entry['max_cashback_amount'], 0, '.', '');

        if(!empty($rule_entry['min_bet_amount'])){
            $rule_entry['min_bet_amount_text'] = $this->CI->utils->formatCurrency($rule_entry['min_bet_amount'], TRUE, TRUE, FALSE);
        }else{
            $rule_entry['min_bet_amount_text'] = lang('Unlimited');
        }

        if(!empty($rule_entry['max_bet_amount'])){
            $rule_entry['max_bet_amount_text'] = $this->CI->utils->formatCurrency($rule_entry['max_bet_amount'], TRUE, TRUE, FALSE);
        }else{
            $rule_entry['max_bet_amount_text'] = lang('Unlimited');
        }

        $rule_entry['max_cashback_amount_text'] = $this->CI->utils->formatCurrency($rule_entry['max_cashback_amount'], TRUE, TRUE, FALSE);
    }

    protected function _match_rule($cashback_rules, $bet_amount){
        $match_rule = FALSE;

        $bet_amount = (float)$bet_amount;

        foreach($cashback_rules as $cashback_rule){
            if($bet_amount >= $cashback_rule['min_bet_amount']){
                if(empty($cashback_rule['max_bet_amount'])){
                    $match_rule = $cashback_rule;
                }elseif($bet_amount < $cashback_rule['max_bet_amount']){
                    $match_rule = $cashback_rule;
                }else{
                    continue;
                }
            }
        }

        return $match_rule;
    }

    public function init_caculate_cashback_require_data(){
        $this->cashback_common_settings = $this->group_level->getCashbackSettings();

        $template = $this->getTemplate();

        if(empty($template)){
            $this->CI->utils->debug_log(__METHOD__ . '(): invalid cashback template.');
            return FALSE;
        }

        $this->cashback_settings_with_game_data = $this->_getTemplateRulesWithGameData($template['cb_mr_tpl_id']);

        $this->all_vip_level = $this->group_level->getGroupLevelList();

        $this->level_cashback_map = $this->group_level->getFullCashbackPercentageMap();

        return $this;
    }

    public function getDefaultSettingTemplate(){
        $default_setting_template = [
            "tpl_id" => false,
            "type" => false,
            "type_map_id" => false,
            "enabled_cashback" => false,
            "created_at" => false,
            "updated_at" => false
        ];
        return $default_setting_template;
    }

    public function getActiveTemplate(){
        $active_template = $this->common_cashback_multiple_range_templates_model->getActiveTemplate();

        return $active_template;
    }

    /**
     * @param int|array $tpl_id
     *
     * @return bool|null
     */
    public function getTemplate($tpl_id = NULL){
        if(is_array($tpl_id)){
            return $tpl_id;
        }

        if(empty($tpl_id)){
            $template = $this->getActiveTemplate();

            if(empty($template)){
                return FALSE;
            }
        }else{
            $template = $this->common_cashback_multiple_range_templates_model->getTemplate($tpl_id);
        }

        if(empty($template)){
            return FALSE;
        }

        return $template;
    }

    public function saveSettings($tpl_id, $type, $type_map_id, $enabled_cashback){
        if(empty($tpl_id) || empty($type) || empty($type_map_id)){
            return FALSE;
        }

        if(FALSE === $this->common_cashback_multiple_range_templates_model->hasTemplate($tpl_id)){
            return FALSE;
        }

        $result = $this->common_cashback_multiple_range_settings_model->saveSetting($tpl_id, $type, $type_map_id, $enabled_cashback);
        if(!$result){
            return FALSE;
        }

        if($type === static::COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME){
            return TRUE;
        }

        $active_template_rules = $this->getTemplateRulesWithTree($tpl_id);
        if(empty($active_template_rules)){
            return FALSE;
        }

        if($type === static::COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_PLATFORM){
            foreach($active_template_rules['settings'] as $game_platform_id => $game_platform_data){
                if($type_map_id != $game_platform_id){
                    continue;
                }

                foreach($game_platform_data['types'] as $game_type_id => $game_type_data){
                    $this->common_cashback_multiple_range_settings_model->saveSetting($tpl_id, static::COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_TYPE, $game_type_id, $enabled_cashback);

                    foreach($game_type_data['game_list'] as $game_description_id => $game_data){
                        $this->common_cashback_multiple_range_settings_model->saveSetting($tpl_id, static::COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME, $game_description_id, $enabled_cashback);
                    }
                }
            }
        }elseif($type === static::COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_TYPE){
            foreach($active_template_rules['settings'] as $game_platform_id => $game_platform_data){
                foreach($game_platform_data['types'] as $game_type_id => $game_type_data){
                    if($type_map_id != $game_type_id){
                        continue;
                    }

                    foreach($game_type_data['game_list'] as $game_description_id => $game_data){
                        $this->common_cashback_multiple_range_settings_model->saveSetting($tpl_id, static::COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME, $game_description_id, $enabled_cashback);
                    }
                }
            }
        }

        return FALSE;
    }

    public function createRule($tpl_id, $type, $type_map_id, $min_bet_amount, $max_bet_amount, $cashback_percentage, $max_cashback_amount){
        if(empty($tpl_id) || empty($type) || empty($type_map_id)){
            return FALSE;
        }

        if(FALSE === $this->common_cashback_multiple_range_templates_model->hasTemplate($tpl_id)){
            return FALSE;
        }

        $result = $this->common_cashback_multiple_range_rules_model->createRule($tpl_id, $type, $type_map_id, $min_bet_amount, $max_bet_amount, $cashback_percentage, $max_cashback_amount);
        if(!$result){
            return FALSE;
        }

        return TRUE;
    }

    public function updateRule($rule_id, $min_bet_amount, $max_bet_amount, $cashback_percentage, $max_cashback_amount){
        if(empty($rule_id)){
            return FALSE;
        }

        if(FALSE === $this->common_cashback_multiple_range_rules_model->hasRuleById($rule_id)){
            return FALSE;
        }

        $result = $this->common_cashback_multiple_range_rules_model->updateRuleById($rule_id, $min_bet_amount, $max_bet_amount, $cashback_percentage, $max_cashback_amount);
        if(!$result){
            return FALSE;
        }

        return TRUE;
    }

    public function deleteRule($rule_id){
        if(empty($rule_id)){
            return FALSE;
        }

        if(FALSE === $this->common_cashback_multiple_range_rules_model->hasRuleById($rule_id)){
            return FALSE;
        }

        $result = $this->common_cashback_multiple_range_rules_model->deleteRuleById($rule_id);
        if(!$result){
            return FALSE;
        }

        return TRUE;
    }

    protected function _getTemplateSettings($tpl_id){
        $all_cashback_settings = $this->common_cashback_multiple_range_settings_model->getAllSettingsByTplId($tpl_id);

        $game_platform_cashback_settings_list = [];
        $game_type_cashback_settings_list = [];
        $game_cashback_settings_list = [];

        if(!empty($all_cashback_settings)){
            foreach($all_cashback_settings as $setting_entry){
                $setting_entry['enabled_cashback'] = !!$setting_entry['enabled_cashback'];

                switch($setting_entry['type']){
                    case static::COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_PLATFORM:
                        $game_platform_cashback_settings_list[$setting_entry['type_map_id']] = $setting_entry;
                        break;
                    case static::COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_TYPE:
                        $game_type_cashback_settings_list[$setting_entry['type_map_id']] = $setting_entry;
                        break;
                    case static::COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME:
                        $game_cashback_settings_list[$setting_entry['type_map_id']] = $setting_entry;
                        break;
                }
            }
        }

        return [$game_platform_cashback_settings_list, $game_type_cashback_settings_list, $game_cashback_settings_list];
    }

    protected function _getTemplateRules($tpl_id){
        $all_cashback_rules = $this->common_cashback_multiple_range_rules_model->getAllRulesByTplId($tpl_id);

        $game_platform_cashback_rules_list = [];
        $game_type_cashback_rules_list = [];
        $game_cashback_rules_list = [];

        if(!empty($all_cashback_rules)){
            foreach($all_cashback_rules as $rule_entry){
                $this->_formateCashbackRule($rule_entry);
                switch($rule_entry['type']){
                    case static::COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_PLATFORM:
                        $game_platform_cashback_rules_list[$rule_entry['type_map_id']]['rule_id_' . $rule_entry['cb_mr_rule_id']] = $rule_entry;
                        break;
                    case static::COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_TYPE:
                        $game_type_cashback_rules_list[$rule_entry['type_map_id']]['rule_id_' . $rule_entry['cb_mr_rule_id']] = $rule_entry;
                        break;
                    case static::COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME:
                        $game_cashback_rules_list[$rule_entry['type_map_id']]['rule_id_' . $rule_entry['cb_mr_rule_id']] = $rule_entry;
                        break;
                }
            }
        }

        return [$game_platform_cashback_rules_list, $game_type_cashback_rules_list, $game_cashback_rules_list];
    }

    protected function _getTemplateRulesWithGameData($tpl_id){
        $template_settings = $this->_getTemplateSettings($tpl_id);
        $template_rules = $this->_getTemplateRules($tpl_id);

        list($game_platform_cashback_settings_list, $game_type_cashback_settings_list, $game_cashback_settings_list) = $template_settings;
        list($game_platform_cashback_rules_list, $game_type_cashback_rules_list, $game_cashback_rules_list) = $template_rules;

        $default_setting_template = $this->getDefaultSettingTemplate();

        $game_map = $this->CI->utils->getGameSystemMap();
        $game_type_raw_list = $this->game_type_model->getAllGameTypeList();
        $game_raw_list = $this->game_description_model->getAllGameDescriptionList();

        $cashback_settings_list_by_game_platform = [];
        $cashback_settings_list_by_game_type = [];
        $cashback_settings_list_by_game = [];

        foreach($game_map as $game_platform_id => $game_platform_name){
            $entry = [
                'game_platform_id' => $game_platform_id,
                'name' => lang($game_platform_name),
                'cashback_settings' => (isset($game_platform_cashback_settings_list[$game_platform_id])) ? $game_platform_cashback_settings_list[$game_platform_id] : $default_setting_template,
                'cashback_rules' => (isset($game_platform_cashback_rules_list[$game_platform_id])) ? $game_platform_cashback_rules_list[$game_platform_id] : [],
            ];

            if(!empty($entry['cashback_rules'])){
                $this->_sortCashbackRules($entry['cashback_rules']);
            }

            $cashback_settings_list_by_game_platform[$game_platform_id] = $entry;
        }

        foreach($game_type_raw_list as $game_type_entry){
            if(!isset($cashback_settings_list_by_game_platform[$game_type_entry['game_platform_id']])){
                continue;
            }

            $game_type_entry['game_type'] = lang($game_type_entry['game_type']);
            $game_type_entry['game_type_lang'] = lang($game_type_entry['game_type_lang']);

            $entry = [
                'game_type_id' => $game_type_entry['id'],
                'game_platform_id' => $game_type_entry['game_platform_id'],
                'name' => lang($game_type_entry['game_type_lang']),
                'details' => $game_type_entry,
                'cashback_settings' => (isset($game_type_cashback_settings_list[$game_type_entry['id']])) ? $game_type_cashback_settings_list[$game_type_entry['id']] : $default_setting_template,
                'cashback_rules' => (isset($game_type_cashback_rules_list[$game_type_entry['id']])) ? $game_type_cashback_rules_list[$game_type_entry['id']] : [],
            ];

            if(!empty($entry['cashback_rules'])){
                $this->_sortCashbackRules($entry['cashback_rules']);
            }

            $cashback_settings_list_by_game_type[$game_type_entry['id']] = $entry;
        }

        foreach($game_raw_list as $game_description_entry){
            if(!isset($cashback_settings_list_by_game_platform[$game_description_entry['game_platform_id']])){
                continue;
            }

            if(!isset($cashback_settings_list_by_game_type[$game_description_entry['game_type_id']])){
                continue;
            }

            $game_type_entry = &$cashback_settings_list_by_game_type[$game_description_entry['game_type_id']];

            $game_description_entry['game_name'] = lang($game_description_entry['game_name']);

            $entry = [
                'game_id' => $game_description_entry['id'],
                'game_type_id' => $game_description_entry['game_type_id'],
                'game_platform_id' => $game_description_entry['game_platform_id'],
                'name' => $game_description_entry['game_name'],
                'details' => $game_description_entry,
                'cashback_settings' => (isset($game_cashback_settings_list[$game_description_entry['id']])) ? $game_cashback_settings_list[$game_description_entry['id']] : $default_setting_template,
                'cashback_rules' => (isset($game_cashback_rules_list[$game_description_entry['id']])) ? $game_cashback_rules_list[$game_description_entry['id']] : [],
            ];

            if(!empty($entry['cashback_rules'])){
                $this->_sortCashbackRules($entry['cashback_rules']);
            }

            if(!isset($game_cashback_settings_list[$game_description_entry['id']]) && $game_type_entry['details']['auto_add_to_cashback']){
                $entry['cashback_settings']['enabled_cashback'] = $game_type_entry['cashback_settings']['enabled_cashback'];
            }

            $cashback_settings_list_by_game[$game_description_entry['id']] = $entry;
        }

        return [$cashback_settings_list_by_game_platform, $cashback_settings_list_by_game_type, $cashback_settings_list_by_game];
    }

    public function getTemplateRulesWithTree($tpl_id = NULL){
        $template = $this->getTemplate($tpl_id);

        if(empty($template)){
            return FALSE;
        }

        list($cashback_settings_list_by_game_platform, $cashback_settings_list_by_game_type, $cashback_settings_list_by_game) = $this->_getTemplateRulesWithGameData($template['cb_mr_tpl_id']);

        $game_tree_map = [];

        foreach($cashback_settings_list_by_game_platform as $game_platform_id => $game_platform_entry){
            $game_platform_entry['types'] = [];

            $game_tree_map[$game_platform_id] = $game_platform_entry;
        }

        foreach($cashback_settings_list_by_game_type as $game_type_id => $game_type_entry){
            if(!isset($game_tree_map[$game_type_entry['game_platform_id']])){
                continue;
            }

            $game_type_entry['game_list'] = [];

            $game_tree_map[$game_type_entry['game_platform_id']]['types'][$game_type_id] = $game_type_entry;
        }

        foreach($cashback_settings_list_by_game as $game_description_id => $game_description_entry){
            if(!isset($game_tree_map[$game_description_entry['game_platform_id']])){
                continue;
            }

            $game_platform_entry = &$game_tree_map[$game_description_entry['game_platform_id']];

            if(!isset($game_platform_entry['types'][$game_description_entry['game_type_id']])){
                continue;
            }

            $game_type_entry = &$game_platform_entry['types'][$game_description_entry['game_type_id']];

            $game_type_entry['game_list'][$game_description_id] = $game_description_entry;
        }

        $template['settings'] = $game_tree_map;

        return $template;
    }

    protected function _getPlayerBetMap($date, $startHour, $endHour, $playerId = null, $start_date = null, $end_date = null){
        $use_settled_time_apis = $this->CI->utils->getConfig('api_array_when_calc_cashback_by_settled_time');

        $playerBetByDate = $this->group_level->getPlayerBetByDate($date, $startHour, $endHour, $playerId, $start_date, $end_date, NULL, $use_settled_time_apis);

        $playerBetBySettledDate = $this->group_level->getPlayerBetBySettledDate($date, $startHour, $endHour, $playerId, $start_date, $end_date, NULL, $use_settled_time_apis);

        $this->CI->utils->debug_log(__METHOD__ . '(): ', $date, $startHour, $endHour, $playerId, 'getPlayerBetByDate count', count($playerBetByDate), 'playerBetBySettledDate count', count($playerBetBySettledDate));

        $player_bet_map = [];

        if(!empty($playerBetByDate)){
            foreach($playerBetByDate as $pbbd){
                if ($pbbd->betting_total <= 0) {
                    continue;
                }

                $player_bet_map[$pbbd->player_id][] = $pbbd;
            }
        }

        if(!empty($playerBetBySettledDate)){
            foreach($playerBetBySettledDate as $pbbd){
                if ($pbbd->betting_total <= 0) {
                    continue;
                }

                $player_bet_map[$pbbd->player_id][] = $pbbd;
            }
        }

        return $player_bet_map;
    }

    public function getAvailableCashbackRule($cashback_rules, $total_bet_amount, $level_id, $game_platform_id, $game_type_id, $game_description_id){
        $level_details = (!empty($this->all_vip_level) && isset($this->all_vip_level[$level_id])) ? $this->all_vip_level[$level_id] : FALSE;

        if(empty($level_details)){
            $this->CI->utils->debug_log(__METHOD__ . '(): invalid vip level id [' . $level_id . '].');
            return FALSE;
        }

        $matched_cashback_rule = $this->_match_rule($cashback_rules, $total_bet_amount);

        $cashback_rule = [
            'history_id' => '',
            'cashback_percentage' => 0,
            'max_cashback_amount' => 0
        ];

        if(!empty($matched_cashback_rule)){
            $cashback_rule['history_id'] = 'cb_mr_' . $matched_cashback_rule['type'] . '_' . $matched_cashback_rule['cb_mr_rule_id'];
            $cashback_rule['cashback_percentage'] = (float)$matched_cashback_rule['cashback_percentage'];
            $cashback_rule['max_cashback_amount'] = (float)$matched_cashback_rule['max_cashback_amount'];
        }

        if($level_details['bonus_mode_cashback']){
            $cashback_percentage = (float)trim($level_details['cashback_percentage']);
            $cashback_maxbonus = (float)trim($level_details['cashback_maxbonus']);
            $cashback_rule['cashback_percentage'] = (!empty($cashback_percentage)) ? $cashback_percentage : $cashback_rule['cashback_percentage'];
            $cashback_rule['max_cashback_amount'] = (!empty($cashback_maxbonus)) ? $cashback_maxbonus : $cashback_rule['max_cashback_amount'];

            $vip_cashback_rule = $this->group_level->getCashbackRuleFromLevelCashbackMap($this->level_cashback_map, $level_id, $game_platform_id, $game_type_id, $game_description_id, TRUE);
            if(empty($vip_cashback_rule)){
                $this->CI->utils->debug_log(__METHOD__ . '(): disabled cashback by vip level setting by [' . $game_platform_id . ', ' . $game_type_id . ', ' . $game_description_id . '].');
                return FALSE;
            }else{
                $cashback_percentage = (float)trim($vip_cashback_rule->cashback_percentage);
                $cashback_maxbonus = (float)trim($vip_cashback_rule->cashback_maxbonus);

                $cashback_rule['history_id'] = 'vip_level_' . $vip_cashback_rule->level_id;
                $cashback_rule['cashback_percentage'] = (!empty($cashback_percentage)) ? $cashback_percentage : $cashback_rule['cashback_percentage'];
                $cashback_rule['max_cashback_amount'] = (!empty($cashback_maxbonus)) ? $cashback_maxbonus : $cashback_rule['max_cashback_amount'];
            }
        }

        return $cashback_rule;
    }

    public function calculateTotalCashback($date, $startHour, $endHour, $playerId = null, $withdraw_condition_bet_times = 0, &$result = false, $start_date = null, $end_date = null){
        $this->CI->load->model(array('player_model', 'transactions', 'users', 'game_description_model', 'withdraw_condition'));

        $cnt = 0;

        if(FALSE === $this->init_caculate_cashback_require_data()){
            return $cnt;
        }

        list($cashback_settings_list_by_game_platform, $cashback_settings_list_by_game_type, $cashback_settings_list_by_game) = $this->cashback_settings_with_game_data;

        $player_bet_map = $this->_getPlayerBetMap($date, $startHour, $endHour, $playerId, $start_date, $end_date);

        if (empty($player_bet_map)) {
            return $cnt;
        }

        $isNoCashbackBonusForNonDepositPlayer = $this->group_level->isNoCashbackBonusForNonDepositPlayer();

        $wc_amount_map = $this->CI->withdraw_condition->getAllPlayersAvailableAmountOnWithdrawConditionByCashbackSettings($date, $startHour, $endHour, $start_date, $end_date, $playerId);

        foreach($player_bet_map as $player_id => $player_bet_list){
            if(empty($player_bet_list)) continue;

            if ($isNoCashbackBonusForNonDepositPlayer) {
                //should check deposit
                $playerObj = $this->CI->player_model->getPlayerArrayById($player_id);
                if ($playerObj['totalDepositAmount'] <= 0) {
                    $this->CI->utils->debug_log(__METHOD__ . '(): ignore player ' . $player_id . ' for none deposit');
                    continue;
                }
            }

            $use_platform_total_bet_amount_list = [];
            $game_platform_total_bet_amount = [];

            $use_type_total_bet_amount_list = [];
            $game_type_total_bet_amount = [];

            $this->CI->utils->debug_log(__METHOD__ . '(): ', $date, $startHour, $endHour, $player_id, '$player_bet_list count', count($player_bet_list));

            foreach ($player_bet_list as $pbbd) {
                $game_platform_id = $pbbd->game_platform_id;
                $game_type_id = $pbbd->game_type_id;
                $game_description_id = $pbbd->game_description_id;

                if(!isset($cashback_settings_list_by_game[$game_description_id])){
                    $this->CI->utils->debug_log(__METHOD__ . '(): Not found game setting on cashback settings list.', 'Game Description id:', $game_description_id);
                    continue;
                }

                $cashback_settings_by_game = $cashback_settings_list_by_game[$game_description_id];

                if(!$cashback_settings_by_game['cashback_settings']['enabled_cashback']){
                    $this->CI->utils->debug_log(__METHOD__ . '(): Disabled cashback by game.', 'Game Description id:', $game_description_id);
                    continue;
                }

                if(empty($cashback_settings_by_game['cashback_rules'])){
                    $use_type_total_bet_amount_list[] = $pbbd;

                    $game_type_total_bet_amount[$game_type_id] = (isset($game_type_total_bet_amount[$game_type_id])) ? $game_type_total_bet_amount[$game_type_id] + $pbbd->betting_total : $pbbd->betting_total;
                    continue;
                }

                $result = $this->_calculatePlayerCashback($pbbd, $cashback_settings_by_game['cashback_rules'], $pbbd->betting_total, $date, $wc_amount_map, $withdraw_condition_bet_times);

                if(FALSE === $result){
                    continue;
                }

                $cnt++;
            }

            foreach ($use_type_total_bet_amount_list as $pbbd) {
                $game_platform_id = $pbbd->game_platform_id;
                $game_type_id = $pbbd->game_type_id;
                $game_description_id = $pbbd->game_description_id;

                if(!isset($cashback_settings_list_by_game_type[$game_type_id])){
                    $this->CI->utils->debug_log(__METHOD__ . '(): Not found game type setting on cashback settings list.', 'Game type id:', $game_type_id);
                    continue;
                }

                $cashback_settings_by_game_type = $cashback_settings_list_by_game_type[$game_type_id];

                if(empty($cashback_settings_by_game_type['cashback_rules'])){
                    $use_platform_total_bet_amount_list[] = $pbbd;

                    $game_platform_total_bet_amount[$game_platform_id] = (isset($game_platform_total_bet_amount[$game_platform_id])) ? $game_platform_total_bet_amount[$game_platform_id] + $pbbd->betting_total : $pbbd->betting_total;
                    continue;
                }

                $result = $this->_calculatePlayerCashback($pbbd, $cashback_settings_by_game_type['cashback_rules'], $game_type_total_bet_amount[$game_type_id], $date, $wc_amount_map, $withdraw_condition_bet_times);

                if(FALSE === $result){
                    continue;
                }

                $cnt++;
            }

            foreach ($use_platform_total_bet_amount_list as $pbbd) {
                $game_platform_id = $pbbd->game_platform_id;
                $game_type_id = $pbbd->game_type_id;
                $game_description_id = $pbbd->game_description_id;

                if(!isset($cashback_settings_list_by_game_platform[$game_platform_id])){
                    $this->CI->utils->debug_log(__METHOD__ . '(): Not found game platform setting on cashback settings list.', 'Game platform id:', $game_platform_id);
                    continue;
                }

                $cashback_settings_by_game_platform = $cashback_settings_list_by_game_platform[$game_platform_id];

                if(empty($cashback_settings_by_game_platform['cashback_rules'])){
                    $this->CI->utils->debug_log(__METHOD__ . '(): disabled cashback by invalid game platform cashback rules by [' . $game_platform_id . ', ' . $game_type_id . ', ' . $game_description_id . '].');
                    continue;
                }

                $result = $this->_calculatePlayerCashback($pbbd, $cashback_settings_by_game_platform['cashback_rules'], $game_platform_total_bet_amount[$game_platform_id], $date, $wc_amount_map, $withdraw_condition_bet_times);

                if(FALSE === $result){
                    continue;
                }

                $cnt++;
            }
        }

        return $cnt;
    }

    protected function _calculatePlayerCashback($pbbd, $cashback_rules, $total_bet_amount, $date, &$wc_amount_map, $withdraw_condition_bet_times){
        $cashback_type = Group_level::NORMAL_CASHBACK;

        $player_id = $pbbd->player_id;
        $level_id = $pbbd->levelId;
        $game_platform_id = $pbbd->game_platform_id;
        $game_type_id = $pbbd->game_type_id;
        $game_description_id = $pbbd->game_description_id;
        $is_parlay = (isset($pbbd->is_parlay)) ? $pbbd->is_parlay : 0;

        $cashback_rule = $this->getAvailableCashbackRule($cashback_rules, $total_bet_amount, $level_id, $game_platform_id, $game_type_id, $game_description_id);

        if(empty($cashback_rule)){
            return FALSE;
        }

        $rate = $cashback_rule['cashback_percentage'];
        $max_bonus = $cashback_rule['max_cashback_amount'];
        $history_id = $cashback_rule['history_id'];

        if($rate <= 0){
            $this->CI->utils->debug_log(__METHOD__ . '(): invalid cashback rate', 'cashback_rules', $cashback_rules, 'total_bet_amount', $total_bet_amount, 'rate', $rate, 'player_id', $player_id, 'game_description_id', $game_description_id,
                'history_id', $history_id, 'level_id', $level_id, 'betting', $pbbd->betting_total, 'betting*rate', $pbbd->betting_total * ($rate / 100), 'max_cashback_amount', $max_bonus);
            return FALSE;
        }

        $max_bonus = (empty($max_bonus)) ? $this->cashback_common_settings->max_cashback_amount : $max_bonus;

        $this->CI->utils->debug_log(__METHOD__ . '(): final', 'cashback_rules', $cashback_rules, 'total_bet_amount', 'rate', $rate, 'player_id', $player_id, 'game_description_id', $game_description_id,
            'history_id', $history_id, 'level_id', $level_id, 'betting', $pbbd->betting_total, 'betting*rate', $pbbd->betting_total * ($rate / 100), 'max_cashback_amount', $max_bonus, 'is_parlay', $is_parlay);

        $game_platform_id = $pbbd->game_platform_id;
        $game_description_id = $pbbd->game_description_id;
        $game_type_id = $pbbd->game_type_id;
        $total_date = $date;

        $original_bet_amount = $this->CI->utils->roundCurrencyForShow($pbbd->betting_total);

        $this->group_level->auto_deduct_withdraw_condition_from_bet($player_id, $wc_amount_map, $pbbd);

        $cashback_amount = $this->CI->utils->roundCurrencyForShow($pbbd->betting_total * ($rate / 100));
        $withdraw_condition_amount = $this->CI->utils->roundCurrencyForShow($cashback_amount * $withdraw_condition_bet_times);

        $uniqueid = sprintf("%s_%s_%s_%s_%s", $total_date, $cashback_type, $player_id, $game_description_id, $is_parlay);

        $this->group_level->syncCashbackDaily($player_id, $game_platform_id, $game_description_id, $total_date,
            $cashback_amount, $history_id, $game_type_id, $level_id, $rate,
            $this->CI->utils->roundCurrencyForShow($pbbd->betting_total),
            $withdraw_condition_amount, $max_bonus, $original_bet_amount, $cashback_type, NULL, $uniqueid);

        return TRUE;
    }
}