<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Total_cashback_player_game
 *
 * General behaviors include :
 *
 *
 * @category Game Model
 * @version 1.0.0
 * @copyright 2013-2017 TripleOneTech
 */
class Total_cashback_player_game extends BaseModel
{

	function __construct()
	{
		parent::__construct();
	}

	protected $tableName = "total_cashback_player_game";

	public function getSumOfAmountByCashbackRequestId($cashback_request_id){
		$this->db->select('SUM(amount) as amount');
		$this->db->from($this->tableName);
		$this->db->where('cashback_request_id', $cashback_request_id);
		$amount = $this->runOneRowOneField('amount');

		return $amount;
	}

}