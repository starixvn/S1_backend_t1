<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Marketing Manager - Deposit Promo
 *
 * Marketing Manager library for FHI OG System
 *
 * @package		Marketing Manager
 * @author		ASRII
 * @version		1.0.0
 */

class Depositpromo_manager {
	private $error = array();

	const BONUS_CONDITION_FIXEDBONUS = 0;
	const BONUS_CONDITION_DEPOSITPERCENTAGE = 1;
	const BONUS_CONDITION_BETPERCENTAGE = 2;

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array(''));
		$this->ci->load->model(array('depositpromo'));
	}

	/**
	 * get all deposit promo setting
	 *
	 * @return 	array
	 */
	public function getAllDepositPromo($sort, $limit, $offset) {
		return $this->ci->depositpromo->getAllDepositPromo($sort, $limit, $offset);
	}

	/**
	 * get all deposit promo name
	 *
	 * @return 	array
	 */
	public function getAllDepositPromoName() {
		return $this->ci->depositpromo->getAllDepositPromoName();
	}

	/**
	 * get all deposit promo setting
	 *
	 * @return 	array
	 */
	public function getDepositPromoListToExport() {
		return $this->ci->depositpromo->getDepositPromoListToExport();
	}

	/**
	 * Gets deposit promo last rank order
	 *
	 * @return	array
	 */
	public function getDepositPromoLastRankOrder() {
		return $this->ci->depositpromo->getDepositPromoLastRankOrder();
	}

	/**
	 * Gets deposit promo last rank order
	 *
	 * @return	array
	 */
	public function getDepositPromoBackupLastRankOrder() {
		return $this->ci->depositpromo->getDepositPromoBackupLastRankOrder();
	}

	/**
	 * Gets All Over the counter Payment Method
	 *
	 * @return	array
	 */
	public function getPaymentMethodDetails($id1, $id) {
		return $this->ci->depositpromo->getPaymentMethodDetails($id, $id);
	}

	/**
	 * Gets player group
	 *
	 * @return	array
	 */
	public function getPlayerGroup() {
		return $this->ci->depositpromo->getPlayerGroup();
	}

	/**
	 * Gets players level
	 *
	 * @return	array
	 */
	public function getAllPlayerLevels() {
		return $this->ci->depositpromo->getAllPlayerLevels();
	}

	/**
	 * getAllPromoRule
	 *
	 * @return	array
	 */
	public function getAllPromoRule() {
		return $this->ci->depositpromo->getAllPromoRule();
	}

	/**
	 * Add deposit promo Settings
	 *
	 * @param 	$data array
	 * @return	Boolean
	 */
	public function addDepositPromo($data, $playerLevels) {
		return $this->ci->depositpromo->addDepositPromo($data, $playerLevels);
	}

	/**
	 * Edit deposit promo Settings
	 *
	 * @return	$array
	 */
	public function editDepositPromo($data, $playerLevels, $vipsetting_id) {
		return $this->ci->depositpromo->editDepositPromo($data, $playerLevels, $vipsetting_id);
	}

	/**
	 * Get deposit promo details
	 *
	 * @return	$array
	 */
	public function getDepositPromoDetails($depositpromoId) {
		return $this->ci->depositpromo->getDepositPromoDetails($depositpromoId);
	}

	/**
	 * viewPromoRuleDetails
	 *
	 * @return	$array
	 */
	public function viewPromoRuleDetails($promoruleId) {
		return $this->ci->depositpromo->viewPromoRuleDetails($promoruleId);
	}

	/**
	 * getPromoRuleDetails
	 *
	 * @return	$array
	 */
	public function getPromoRuleDetails($promorulesId) {
		return $this->ci->depositpromo->getPromoRuleDetails($promorulesId);
	}

	/**
	 * clearPromoItems
	 *
	 * @return	$array
	 */
	public function clearPromoItems($promorulesId) {
		return $this->ci->depositpromo->clearPromoItems($promorulesId);
	}

	/**
	 * Will addDepositPromoPlayerLevelsLimit
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function addDepositPromoPlayerLevelsLimit($data) {
		return $this->ci->depositpromo->addDepositPromoPlayerLevelsLimit($data);
	}

	/**
	 * Delete Bank accounts
	 *
	 * @return	$array
	 */
	public function deleteDepositPromos($depositPromoId) {
		return $this->ci->depositpromo->deleteDepositPromos($depositPromoId);
	}

	/**
	 * Get Bank account item
	 *
	 * @return	$array
	 */
	// public function deleteDepositPromoItem($depositPromoId) {
	// 	return $this->ci->depositpromo->deleteDepositPromoItem($depositPromoId);
	// }

	/**
	 * deletePromoRules
	 *
	 * @return	$array
	 */
	public function deletePromoRules($promoId) {
		return $this->ci->depositpromo->deletePromoRules($promoId);
	}

	/**
	 * getAllGames
	 *
	 * @return	$array
	 */
	public function getAllGames($gameType = null) {
		return $this->ci->depositpromo->getAllGames($gameType);
	}

	/**
	 * getPromoRuleGamesType
	 *
	 * @return	$array
	 */
	public function getPromoRuleGamesType($promoId) {
		return $this->ci->depositpromo->getPromoRuleGamesType($promoId);
	}

	/**
	 * getPromoRuleGames
	 *
	 * @return	$array
	 */
	public function getPromoRuleGames($promoId) {
		return $this->ci->depositpromo->getPromoRuleGames($promoId);
	}

	/**
	 * deletePromoRuleItem
	 *
	 * @return	$array
	 */
	public function deletePromoRuleItem($promoId) {
		return $this->ci->depositpromo->deletePromoRuleItem($promoId);
	}

	/**
	 * Will activate deposit promo
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function activateDepositPromo($data) {
		return $this->ci->depositpromo->activateDepositPromo($data);
	}

	/**
	 * activatePromoRule
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function activatePromoRule($data) {
		return $this->ci->depositpromo->activatePromoRule($data);
	}

	/**
	 * search deposit promo
	 *
	 * @return 	array
	 */
	public function searchDepositPromoList($search, $limit, $offset) {
		return $this->ci->depositpromo->searchDepositPromoList($search, $limit, $offset);
	}

	/**
	 * Will randomize alphanumeric and special characters
	 *
	 * @param   string
	 * @return  string
	 */
	public function generateRandomCode() {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$generatePromoCode = '';
		foreach (array_rand($seed, 7) as $k) {
			$generatePromoCode .= $seed[$k];
		}

		return $generatePromoCode;
	}

	/**
	 * isPromoCodeExists
	 *
	 * @return 	array
	 */
	public function isPromoCodeExists($promoCode) {
		return $this->ci->depositpromo->isPromoCodeExists($promoCode);
	}

	/**
	 * isPromoNameExists
	 *
	 * @return 	array
	 */
	public function isPromoNameExists($promoName) {
		return $this->ci->depositpromo->isPromoNameExists($promoName);
	}

	/**
	 * get promo type
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function getPromoType() {
		return $this->ci->depositpromo->getPromoType();
	}

	/**
	 * addPromoType
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function addPromoType($promotypedata) {
		$this->ci->depositpromo->addPromoType($promotypedata);
	}

	/**
	 * addPromoRules
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function addPromoRules($data) {
		return $this->ci->depositpromo->addPromoRules($data);
	}

	/**
	 * editPromoRules
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function editPromoRules($data) {
		return $this->ci->depositpromo->editPromoRules($data);
	}

	/**
	 * addPromoRuleAllowedPlayerLevel
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function addPromoRuleAllowedPlayerLevel($data) {
		return $this->ci->depositpromo->addPromoRuleAllowedPlayerLevel($data);
	}

	/**
	 * addPromoRulesGameType
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function addPromoRulesGameType($data) {
		$this->ci->depositpromo->addPromoRulesGameType($data);
	}

	/**
	 * addGameRequirements
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function addGameRequirements($data) {
		$this->ci->depositpromo->addGameRequirements($data);
	}

	/**
	 * getPromoTypeDetails
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function getPromoTypeDetails($promotypeId) {
		return $data = $this->ci->depositpromo->getPromoTypeDetails($promotypeId);
		//var_dump($data);exit();
	}

	/**
	 * getAllPromoApplication
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function getAllPromoApplication($cancelStatus = '') {
		return $data = $this->ci->depositpromo->getAllPromoApplication($cancelStatus);
		//var_dump($data);exit();
	}

	/**
	 * getAllPromoPlayer
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function getAllPromoPlayer() {
		return $data = $this->ci->depositpromo->getAllPromoPlayer();
		//var_dump($data);exit();
	}

	/**
	 * getPromoCancelSetup
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function getPromoCancelSetup() {
		return $data = $this->ci->depositpromo->getPromoCancelSetup();
		//var_dump($data);exit();
	}

	/**
	 * setupPromoCancellation
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function setupPromoCancellation($data) {
		$this->ci->depositpromo->setupPromoCancellation($data);
	}

	/**
	 * editPromoType
	 *
	 * @param 	data array
	 * @return 	array
	 */
	public function editPromoType($promotypedata) {
		$this->ci->depositpromo->editPromoType($promotypedata);
	}

	/**
	 * processPromoApplication
	 *
	 * @param 	data array
	 * @return 	array
	 */
	// public function processPromoApplication($playerpromodata) {
	// 	$this->ci->depositpromo->processPromoApplication($playerpromodata);
	// }

	/**
	 * editPromoType
	 *
	 * @param 	promotypeid
	 * @return 	null
	 */
	public function deletePromoType($promotypeid) {
		return $this->ci->depositpromo->deletePromoType($promotypeid);
	}

	/**
	 * editPromoType
	 *
	 * @param 	promotypeid
	 * @return 	null
	 */
	public function fakeDeletePromoType($promotypeid) {
		return $this->ci->depositpromo->fakeDeletePromoType($promotypeid);
	}

	/**
	 * editPromoType
	 *
	 * @param 	promotypeid
	 * @return 	null
	 */
	public function getGameProvider() {
		return $this->ci->depositpromo->getGameProvider();
	}

	/**
	 * getGameType
	 *
	 * @return 	null
	 */
	public function getGameType($game_platform_id) {
		return $this->ci->depositpromo->getGameType($game_platform_id);
	}

	/**
	 * getPlatformGames
	 *
	 * @return 	null
	 */
	public function getPlatformGames($gamePlatformId) {
		return $this->ci->depositpromo->getPlatformGames($gamePlatformId);
	}

	/**
	 * getAGGames
	 *
	 * @return 	null
	 */
	public function getAGGames() {
		return $this->ci->depositpromo->getAGGames();
	}

	/**
	 * Will save player withdrawal condition
	 *
	 * @param   conditionData array
	 * @return  array
	 */
	public function savePlayerWithdrawalCondition($conditionData) {
		return $this->ci->depositpromo->savePlayerWithdrawalCondition($conditionData);
	}

	/**
	 * Will get withdrawal condition amount
	 *
	 * @param   playerPromoId int
	 * @return  array
	 */
	public function getConditionAmount($playerpromoId) {
		$data = $this->ci->depositpromo->getPromoRulesId($playerpromoId);
		return $this->ci->depositpromo->viewPromoRuleDetails($data['promorulesId']);
	}

	/**
	 * addCashbackAllowedGames
	 * @deprecated move to group_level
	 * @param 	data array
	 * @return 	array
	 */
	// public function addCashbackAllowedGames($data) {
	// 	$this->ci->depositpromo->addCashbackAllowedGames($data);
	// }

	/**
	 * isVipSettingIdExists
	 *
	 * @return 	array
	 */
	// public function isVipSettingIdExists($vipsetting_id) {
	// 	return $this->ci->depositpromo->isVipSettingIdExists($vipsetting_id);
	// }

	/**
	 * Delete cashback allowed game type
	 *
	 * @return	$array
	 */
	// public function deleteCashbackAllowedGameType($vipsetting_id) {
	// 	return $this->ci->depositpromo->deleteCashbackAllowedGameType($vipsetting_id);
	// }

	/**
	 * get cashback allowed game type
	 *
	 * @return	$array
	 */
	// public function getCashbackAllowedGame($vipsetting_id) {
	// 	return $this->ci->depositpromo->getCashbackAllowedGame($vipsetting_id);
	// }

	/**
	 * getPromoRuleLevels
	 *
	 * @return	$array
	 */
	public function getPromoRuleLevels($vipsetting_id) {
		return $this->ci->depositpromo->getPromoRuleLevels($vipsetting_id);
	}

	/**
	 * getAllPromoRuleLevels
	 *
	 * @return	$array
	 */
	public function getAllPromoRuleLevels() {
		return $this->ci->depositpromo->getAllPromoRuleLevels();
	}

	/**
	 * getAllGameType
	 *
	 * @return	$array
	 */
	public function getAllGameType() {
		return $this->ci->depositpromo->getAllGameType();
	}

	/**
	 * getAllGameProvider
	 *
	 * @return	$array
	 */
	public function getAllGameProvider() {
		return $this->ci->depositpromo->getAllGameProvider();
	}

	/**
	 * getPromoRuleGamesProvider
	 *
	 * @return	$array
	 */
	public function getPromoRuleGamesProvider($promoruleId) {
		return $this->ci->depositpromo->getPromoRuleGamesProvider($promoruleId);
	}

	/**
	 * getBonusAmount
	 *
	 * @return	$int
	 */
	public function getBonusAmount($promorulesId) {
		$promorules = $this->ci->depositpromo->getPromoBonusReleaseRule($promorulesId);
		if ($promorules['bonusReleaseRule'] == self::BONUS_CONDITION_FIXEDBONUS) {
			return $promorules['bonusAmount'];
		} elseif ($promorules['bonusReleaseRule'] == self::BONUS_CONDITION_BETPERCENTAGE) {
			return $promorules['gameRequiredBet'] * $promorules['depositPercentage'];
		}
	}

}

/* End of file depositpromo_manager.php */
/* Location: ./application/libraries/depositpromo_manager.php */
