<?php
require_once dirname(__FILE__) . '/base_model.php';

require_once dirname(__FILE__) . '/modules/cashback_model_module.php';

/**
 * General behaviors include :
 * * batchAddGlobalCashbackAllowedGames
 *
 * @category Marketing
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Cashback_settings extends BaseModel {

	const INACTIVE = 0;
	const ACTIVE = 1;
	protected $commonCashbackGameRule = 'common_cashback_game_rules';
	protected $commonCashbackRule = 'common_cashback_rules';

	const COMMON_CASHBACK_RULE_FIELDS = array('min_bet_amount', 'max_bet_amount', 'created_at',
		'updated_at', 'created_by', 'updated_by', 'default_percentage', 'status');

	use cashback_model_module;

	function __construct() {
		parent::__construct();
	}

	/**
	 * detail: add common cashback game rule
	 *
	 * @param string $cashback_maxbonus
	 * @param string $gameAptList
	 *
	 * @return Boolean
	 */
	public function addCommonCashbackGameRule($gamesAptList) {
		$this->deleteCommonCashbackGameRule();
		$cnt = 0;
		$data = array();
		$this->utils->debug_log('gamesAptList count', count($gamesAptList));
		foreach ($gamesAptList as $gameDescriptionId) {

			$data[] = array(
				'game_description_id' => $gameDescriptionId['id'],
				// 'percentage' => $cashback_percentage,
				'game_platform_percentage' => $gameDescriptionId['game_platform_number'],
				'game_type_percentage' => $gameDescriptionId['game_type_number'],
				'game_desc_percentage' => $gameDescriptionId['game_desc_number'],
			);
			if ($cnt >= 500) {
				//insert and clean
				$this->db->insert_batch($this->commonCashbackGameRule, $data);
				$data = array();
				$cnt = 0;
			}
			$cnt++;
		}
		if (!empty($data)) {
			$this->db->insert_batch($this->commonCashbackGameRule, $data);
		}
	}

	public function getCommonCashbackGameRule() {
		$this->db->select("external_system.system_name,game_description.game_name,ccbg.maxBonus")->from($this->commonCashbackGameRule . ' as ccbg');
		$this->db->join('game_description', 'game_description.id = ccbg.game_description_id');
		$this->db->join('external_system', 'external_system.id = game_description.game_platform_id');
		return $this->runMultipleRowArray();
	}

	public function getCommonCashbackRule($id = null) {
		$this->db->select("ccbr.id,
						   ccbr.min_bet_amount,
						   ccbr.max_bet_amount,
						   ccbr.default_percentage,
						   ccbr.created_at,
						   ccbr.updated_at,
						   ccbr.status,
						   ")->from('common_cashback_rules as ccbr');
		// $this->db->join('common_cashback_game_rules as ccgr', 'ccgr.id = ccbr.rule_id', 'left');
		if ($id) {
			$this->db->where('ccbr.id', $id);
			return $this->runOneRow();
		}
		return $this->runMultipleRowArray();
	}

	public function getGameTreeForCashbackGameRuleById($ruleId) {

		$this->load->model(array('game_description_model'));

		list($gamePlatformList, $gameTypeList, $gameDescList) = $this->getCashbackGameRuleTree($ruleId);

		$showGameDescTree = $this->config->item('show_particular_game_in_tree');

		return $this->game_description_model->getGameTreeArray($gamePlatformList, $gameTypeList, $gameDescList, false, $showGameDescTree);
	}

	public function getGameTreeForCashbackGameRule() {

		$this->load->model(array('game_description_model'));

		$gamePlatformList = array();
		$gameTypeList = array();
		$gameDescList = array();

		$showGameDescTree = $this->config->item('show_particular_game_in_tree');

		return $this->game_description_model->getGameTreeArray($gamePlatformList, $gameTypeList, $gameDescList, false, $showGameDescTree);
	}

	public function getCashbackGameRuleTree($ruleId) {
		$this->db->select('game_description.game_platform_id,game_description.game_type_id, game_description.id as game_description_id,
			game_platform_percentage,game_type_percentage,game_desc_percentage')
			->from('common_cashback_game_rules')
			->join('game_description', 'game_description.id = common_cashback_game_rules.game_description_id', 'left')
			->where('common_cashback_game_rules.rule_id', $ruleId);

		$rows = $this->runMultipleRowArray();

		$gamePlatformList = array();
		$gameTypeList = array();
		$gameDescList = array();

		if (!empty($rows)) {
			foreach ($rows as $row) {
				$gamePlatformList[$row['game_platform_id']] = $row['game_platform_percentage'];
				$gameTypeList[$row['game_type_id']] = $row['game_type_percentage'];
				$gameDescList[$row['game_description_id']] = $row['game_desc_percentage'];
			}
		}

		return array($gamePlatformList, $gameTypeList, $gameDescList);
	}

	/**
	 * detail: add common cashback game rules
	 *
	 * @param int $ruleId
	 * @param string $cashback_percentage
	 * @param string $gameAptList
	 *
	 * @return Boolean
	 */
	public function batchAddCashbackGameRules($ruleId, $cashback_percentage, $gamesAptList, $adminUserId=null, $postTree=null, $gamePlatformList=null, $gameTypeList=null, $gameDescList=null, &$diffList=[]) {

		$success = true;

		if(!is_null($adminUserId) && !is_null($postTree) && !is_null($gamePlatformList) && !is_null($gameTypeList) && !is_null($gameDescList)) {
			$this->utils->debug_log('==================batchAddCashbackGameRules postTree', $postTree);
			$backupType = "common_cashback";
			if (!$this->backupCashbackPercentage($ruleId, $adminUserId, $postTree, $backupType)) {
				$this->utils->error_log('backupCashbackPercentage failed', $ruleId);
			}
			$this->generateCommonCashbackDiffList($ruleId, $gamePlatformList, $gameTypeList, $gameDescList, $diffList);
		}

        if ( empty($gamesAptList['dont_remove_rule_id'])) {
            $this->deleteCommonCashbackGameRule($ruleId);
        }

		$cnt = 0;
		$data = array();
		$this->utils->debug_log('gamesAptList count', count($gamesAptList));

		if (is_array($gamesAptList)) {
			foreach ($gamesAptList as $gameDescriptionId) {

	            $this->db->select('id')->where('game_description_id',$gameDescriptionId['id'])->where('rule_id',$ruleId);
	            $isGameExist =$this->db->get($this->commonCashbackGameRule);

	            if ( ! empty($isGameExist->row('id'))) continue;

				$data[] = array(
					'rule_id' => $ruleId,
					'game_description_id' => $gameDescriptionId['id'],
					'percentage' => $cashback_percentage,
					'game_platform_percentage' => $gameDescriptionId['game_platform_number'],
					'game_type_percentage' => $gameDescriptionId['game_type_number'],
					'game_desc_percentage' => $gameDescriptionId['game_desc_number'],
				);
				if ($cnt >= 500) {
					//insert and clean
					$this->db->insert_batch($this->commonCashbackGameRule, $data);
					$data = array();
					$cnt = 0;
				}
				$cnt++;
			}
		}
		if (!empty($data)) {
			$this->db->insert_batch($this->commonCashbackGameRule, $data);
		}

		return $success;
	}

	/**
	 * overview : add cashback rules
	 *
	 * @param $data
	 * @return bool
	 */
	public function addCommonCashbackRule($data) {

		$data = $this->packToJsonInfo($data);

		$this->db->insert($this->commonCashbackRule, $data);

		//checker
		if ($this->db->affected_rows() == '1') {
			//return TRUE;
			return $this->db->insert_id();
		}

		return FALSE;
	}

	/**
	 * detail: delete common cashback game rule of a certain cashback rule setting
	 *
	 * @param int $ruleId
	 * @return Boolean
	 */
	public function deleteCommonCashbackGameRule($ruleId) {
		$this->db->where('rule_id', $ruleId);
		$this->db->delete($this->commonCashbackGameRule);
	}

	/**
	 * overview : update common cashback rules settings
	 *
	 * @param array $data
	 */
	public function updateCommonCashbackRule($data) {

		$data = $this->packToJsonInfo($data);
		$this->db->where('id', $data['id']);
		$this->db->update($this->commonCashbackRule, $data);
	}

	/**
	 * overview : update common cashback rules settings status
	 *
	 * @param array $data
	 */
	public function updateStatusCashbackGameRuleSetting($data) {
		$this->db->where('id', $data['id']);
		$this->db->update($this->commonCashbackRule, $data);
	}

	/**
	 * overview : pack to json info
	 *
	 * @param array $common cashback
	 * @return array|object
	 */
	public function packToJsonInfo($commonCashbackRule) {
		$isArr = false;
		if (is_array($commonCashbackRule)) {
			$commonCashbackRule = (object) $commonCashbackRule;
			$isArr = true;
		}
		$json_arr = array();
		foreach (self::COMMON_CASHBACK_RULE_FIELDS as $key) {
			if (isset($commonCashbackRule->$key)) {
				$json_arr[$key] = $commonCashbackRule->$key;
			}
		}

		$commonCashbackRule->json_info = json_encode($json_arr);
		if ($isArr) {
			$commonCashbackRule = (array) $commonCashbackRule;
		}

		return $commonCashbackRule;
	}

	public function deleteCommonCashbackRule($ruleId) {
		$this->db->where('id', $ruleId);
		$this->db->delete($this->commonCashbackRule);
	}
}

///END OF FILE//////////
