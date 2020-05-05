<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Promo_model
 *
 * MOVED to promorules
 * @author	kaiser.dapar
 */
class Promo_model extends CI_Model {

	const SYSTEM = 1;
	const APPROVED = 1;

	const IS_HIDE_PROMO = 1;
	public function getPromoRules($promo_rule_id) {
		return $this->db->get('promorules', ['promorulesId', $promo_rule_id])->row_array();
	}

	public function updatePlayerPromo($player_promo_id, $player_promo) {
		$this->db->update('playerpromo', $player_promo, ['playerpromoId' => $player_promo_id]);
		if ($this->db->affected_rows()) {
			return $this->db->get_where('playerpromo', ['playerpromoId' => $player_promo_id])->row_array();
		}return FALSE;
	}

	public function checkPromoForHiding() {
		$now = $this->utils->getNowForMysql();
		$promo['status'] = self::IS_HIDE_PROMO;
		$this->db->where('hide_date <= ', $now);
		$this->db->update('promorules', $promo);
	}
}

/* End of file Promo_model.php */
/* Location: ./application/models/promo_model.php */