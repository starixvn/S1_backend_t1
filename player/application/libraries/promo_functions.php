<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Promo Functions
 *
 * Promo Functions library for FHI OG System
 * MOVED TO promorules.php
 * @package     Promo Functions
 * @author      ASRII
 * @version     1.0.0
 */

class Promo_Functions {

	const REQUEST_TO_CANCEL = 1;
	const DECLINE_CANCEL_REQUEST = 2;
	const APPROVED_CANCEL_REQUEST = 3;

	const REQUEST = 0;
	const APPROVED = 1;
	const DECLINED = 2;
	const CANCELLED = 3;

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('session'));
		$this->ci->load->model(array('promo'));
	}

	/**
	 * Will get promo
	 *
	 * @param   $int
	 * @param   $int
	 * @return  array
	 */
	public function getAllPromo($limit, $offset) {
		$result = $this->ci->promo->getAllPromo($limit, $offset);
		return $result;
	}

	/**
	 * Will get featured promo
	 *
	 * @param   $int
	 * @param   $int
	 * @return  array
	 */
	public function getAllFeaturedPromo($limit, $offset) {
		$result = $this->ci->promo->getAllFeaturedPromo($limit, $offset);
		return $result;
	}

	/**
	 * Will get new promo
	 *
	 * @param   $int
	 * @param   $int
	 * @return  array
	 */
	public function getAllNewPromo($limit, $offset) {
		$result = $this->ci->promo->getAllNewPromo($limit, $offset);
		return $result;
	}

	/**
	 * Will get  players promo
	 *
	 * @param   $int
	 * @param   $int
	 * @return  array
	 */
	public function getAllPlayersPromo($limit, $offset) {
		$result = $this->ci->promo->getAllPlayersPromo($limit, $offset);
		return $result;
	}

	/**
	 * Will get VIP promo
	 *
	 * @param   $int
	 * @param   $int
	 * @return  array
	 */
	public function getAllVIPPromo($limit, $offset) {
		$result = $this->ci->promo->getAllVIPPromo($limit, $offset);
		return $result;
	}

	/**
	 * get promo cms details
	 *
	 * @param   int
	 */
	public function getPromoCmsDetails($promocmsId) {
		return $this->ci->promo->getPromoCmsDetails($promocmsId);
	}

	/**
	 * checkPromoPeriodApplication
	 *
	 * @param $promorulesId int
	 * @param $promoType str
	 *
	 */
	public function checkPromoPeriodApplication($promorulesId, $promoType) {
		return $this->ci->promo->checkPromoPeriodApplication($promorulesId, $promoType);
	}

	/**
	 * get promo game type
	 *
	 * @param   int
	 */
	public function getPromoRuleGameType($promorulesId) {
		return $this->ci->promo->getPromoRuleGameType($promorulesId);
	}

	/**
	 * get player promo start date
	 *
	 * @param   int
	 */
	public function getPlayerPromoStartDate($promorulesId) {
		return $this->ci->promo->getPlayerPromoStartDate($promorulesId);
	}

	/**
	 * isAutoApproveCancel
	 *
	 * @param   int
	 */
	public function isAutoApproveCancel() {
		return $this->ci->promo->isAutoApproveCancel();
	}

	/**
	 * Will get withdrawal condition amount
	 *
	 * @param   playerPromoId int
	 * @return  array
	 */
	public function getConditionAmount($playerpromoId) {
		$data = $this->ci->promo->getPromoRulesId($playerpromoId);
		return $this->ci->promo->viewPromoRuleDetails($data['promorulesId']);
	}

	/**
	 * Will save player withdrawal condition
	 *
	 * @param   conditionData array
	 * @return  array
	 */
	public function savePlayerWithdrawalCondition($conditionData) {
		return $this->ci->promo->savePlayerWithdrawalCondition($conditionData);
	}

	/**
	 * get promo game type
	 *
	 * @param   int
	 */
	public function getAllowedPlayerLevels($promorulesId) {
		return $this->ci->promo->getAllowedPlayerLevels($promorulesId);
	}

}

/* End of file promo_functions.php */
/* Location: ./application/libraries/promo_functions.php */