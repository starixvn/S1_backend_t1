<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Promo_library {

	public function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->model(['player_model', 'promorules', 'transactions', 'wallet_model', 'vipsetting', 'sale_order', 'withdraw_condition']);
	}

	public function approvePromo($player_promo_id, $approved_by = Promorules::SYSTEM) {

		$this->ci->db->trans_start();

		$current_timestamp = $this->ci->utils->getNowForMysql();

		# UPDATE PLAYERPROMO TABLE
		$promo = $this->ci->promorules->updatePlayerPromo($player_promo_id, [
			'processedBy' => $approved_by,
			'dateApply' => $current_timestamp,
			'dateProcessed' => $current_timestamp,
			'promoStatus' => Promorules::APPROVED,
		]);

		# IF PROMO NOT FOUND, RETURN FALSE
		if (!$promo) {
			return false;
		}

		$promo_rules = $this->ci->promorules->getPromoRules($player_promo_id);
		if ($promo_rules['withdrawRequirementConditionType'] == 0) {
			$withdrawBetAmtCondition = $promo_rules['withdrawRequirementBetAmount'];
		} else {
			$withdrawBetAmtCondition = ($promo['deposit_amount'] + $promo['bonusAmount']) * $promo_rules['withdrawRequirementBetCntCondition'];
		}

		$this->ci->player_model->savePlayerWithdrawalCondition([
			'source_id' => $player_promo_id,
			'source_type' => 2, # source is promotion
			'started_at' => $current_timestamp,
			'condition_amount' => $withdrawBetAmtCondition,
			'status' => 1, # enabled
			'player_id' => $promo['playerId'],
			'promotion_id' => $promo_rules['promorulesId'],
		]);

		$transaction = $this->ci->player_model->updateMainWalletBalance([
			'amount' => $promo['bonusAmount'],
			'transaction_type' => Transactions::ADD_BONUS,
			'from_id' => $approved_by,
			'from_type' => Transactions::ADMIN,
			'to_id' => $promo['playerId'],
			'to_type' => Transactions::PLAYER,
			'note' => 'Add bonus ' . $promo['bonusAmount'] . ' to ' . $promo['playerId'],
			'status' => Transactions::APPROVED,
			'flag' => $approved_by == Transactions::PROGRAM ? Transactions::PROGRAM : Transactions::MANUAL,
		]);

		if ($transaction) {
			$this->ci->db->trans_complete();
			return $transaction;
		} else {
			return false;
		}
	}

	public function getPromoRule($promo_id) {
		return $this->ci->promorules->getPromoRules($promo_id);
	}

	/**
	 * check all promo for hiding
	 *
	 * @param	int
	 * @return 	array
	 */
	function checkPromoForHiding() {
		$result = $this->ci->promorules->checkPromoForHiding();
		return $result;
	}
}