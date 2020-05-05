<?php
trait api_tree_module {

	public function get_game_tree_by_level($vipgrouplevelId, $filterColumn=array()) {
		// $result = array();
		$filterColumn = is_null($this->getQueryString()) ? array() : $this->getQueryString();
		$this->utils->debug_log('======================get_game_tree_by_level getQueryString', $filterColumn);
		$this->load->model(array('group_level'));
		$result = $this->group_level->getGameTreeForGroupLevel($vipgrouplevelId, $filterColumn);
		// $this->utils->debug_log('game tree', $result);
		$this->returnJsonResult($result, false, '*', false);
	}

	public function get_game_tree_by_agent($agent_id) {
		$this->load->model(array('group_level'));

		$result = $this->group_level->get_agency_game_rolling_comm_tree($agent_id);

		//$this->utils->debug_log('GET_GAME_TREE_BY_AGENT', $result);
		$this->returnJsonResult($result, false, '*', false);
	}

	public function get_game_tree_by_pub_affiliate_setting($affiliateId = null) {
		// $result = array();

		$this->load->model(array('affiliatemodel'));
		$result = $this->affiliatemodel->getGameTreeForAffiliate($affiliateId);

		// $this->utils->debug_log('game tree', $result);
		$this->returnJsonResult($result, false, '*', false);
	}

	public function get_game_tree_by_affilliate_Level2($vip_level) {
		$this->load->model(['operatorglobalsettings','affiliatemodel']);
		$affilliate_level_settings = json_decode($this->operatorglobalsettings->getSettingJson('affilliate_level_settings', 'template'), true);
		$this->utils->debug_log('affilliate_level_settings vip_level_select', $vip_level, var_export($affilliate_level_settings[$vip_level], true));
		$result = $this->affiliatemodel->getGameTreeForVipAffiliateLevel2($affilliate_level_settings[$vip_level]);
		$this->returnJsonResult($result, false, '*', false);
	}

	public function get_game_tree_by_affilliate_sub_Level2() {
		$this->load->model(['operatorglobalsettings','affiliatemodel']);
		$affilliate_level_settings = json_decode($this->operatorglobalsettings->getSettingJson('affilliate_sub_level_settings', 'template'), true);
		$this->utils->debug_log('affilliate_sub_level_settings vip_level_select', var_export($affilliate_level_settings, true));
		$result = $this->affiliatemodel->getGameTreeForVipAffiliateLevel2($affilliate_level_settings);
		$this->returnJsonResult($result, false, '*', false);
	}

	public function get_game_tree_by_level2($vipgrouplevelId) {
		// $result = array();

		$this->load->model(array('group_level'));
		$result = $this->group_level->getGameTreeForGroupLevel2($vipgrouplevelId);

		// $this->utils->debug_log('game tree', $result);
		$this->returnJsonResult($result, false, '*', false);
	}

	public function get_game_tree_by_promo($promoRuleId = '', $filterColumn=array()) {
		$this->load->model('promorules');

		$filterColumn = is_null($this->getQueryString()) ? array() : $this->getQueryString();
		$this->utils->debug_log('======================get_game_tree_by_promo getQueryString', $filterColumn);

		if ($promoRuleId) {
			$result = $this->promorules->getGameTreeForPromoRuleById($promoRuleId, $filterColumn);
		} else {
			$result = $this->promorules->getGameTreeForPromoRule($filterColumn);
		}

		$this->returnJsonResult($result, false, '*', false);
	}

	public function get_allowed_player_level($promoRuleId = '') {
		$this->load->model('promorules');

		$result = $this->promorules->getGameTreeForPlayerLevel($promoRuleId);
		$this->returnJsonResult($result, false, '*', false);
	}

	public function get_allowed_games() {
		$this->load->model('promorules');
		$result = $this->promorules->getGameListInTree();
		$this->returnJsonResult($result, false, '*', false);
	}

	public function get_cashback_game_rule($ruleId = null) {
		// $result = array();
		$this->load->model(array('cashback_settings'));
		if ($ruleId) {
			$result = $this->cashback_settings->getGameTreeForCashbackGameRuleById($ruleId);
		} else {
			$result = $this->cashback_settings->getGameTreeForCashbackGameRule();
		}

		$this->returnJsonResult($result, false, '*', false);
	}

	public function get_report_game_tree() {
		$this->load->model(array('game_description_model'));
		$result = $this->game_description_model->getGameTreeArray2(null, null, null, true, false);
		$this->returnJsonResult($result, false, '*', false);
	}

    public function get_game_tree_by_flag_new_game() {
        $this->load->model(array('game_description_model'));
        $result = $this->game_description_model->getGameTreeArrayByFlag(null, null);
        $this->returnJsonResult($result, false, '*', false);
    }
}

////END OF FILE////////////////
