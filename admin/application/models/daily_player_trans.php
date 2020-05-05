<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Daily Player's Transactions
 *
 * This model represents transactions statistics per player.
 *
 *
 * @author  og
 */
class Daily_player_trans extends BaseModel {

	protected $tableName = 'daily_player_trans';

	protected $idField = 'id';

	# TRANSACTION_TYPE , The Same As Transcation Model
	const DEPOSIT = 1;
	const WITHDRAWAL = 2;
	const FEE_FOR_PLAYER = 3;
	const FEE_FOR_OPERATOR = 4;
	const TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET = 5;
	const TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET = 6;
	const MANUAL_ADD_BALANCE = 7;
	const MANUAL_SUBTRACT_BALANCE = 8;
	const ADD_BONUS = 9;
	const SUBTRACT_BONUS = 10;
	const MANUAL_ADD_BALANCE_ON_SUB_WALLET = 11;
	const MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET = 12;
	const AUTO_ADD_CASHBACK_TO_BALANCE = 13;
	const MEMBER_GROUP_DEPOSIT_BONUS = 14;
	const PLAYER_REFER_BONUS = 15;

	# FROM/TO
	const ADMIN = 1;
	const PLAYER = 2;
	const AFFILIATE = 3;

	# STATUS
	const APPROVED = 1;
	const DECLINED = 2;

	# FLAG
	const MANUAL = 1;
	const PROGRAM = 2;

	public function __construct() {
		parent::__construct();
	}

	public function update_today($transactionDetails) {
		$playerId = $transactionDetails['to_id'];
		if ($transactionDetails['from_type'] == self::PLAYER) {
			$playerId = $transactionDetails['from_id'];
		}
		$date = substr($transactionDetails['created_at'],0,10);
		$amount = $transactionDetails['amount'];
		$transType = $transactionDetails['transaction_type'];
        $exists = $this->get_existing_trans_today($playerId, $date, $transType); 
        $objDateTime = new DateTime('NOW');      
		if ($exists) {
            $id = $exists;
            $sql_update = "UPDATE daily_player_trans SET trans_amount = trans_amount + " . $amount .
            ", trans_count = trans_count + 1" .
            ", trans_type = " . $transType .
            ", updated_at = '" . $this->utils->formatDateTimeForMysql($objDateTime) .
            "' WHERE id = " . $id;
			return $this->db->query($sql_update);
		} else {
            $data = array(
               'player_id' => $playerId,
               'date' => $date,
               'trans_amount' => $amount,
               'trans_count' => 1,
               'trans_type' => $transType,
               'created_at' => $this->utils->formatDateTimeForMysql($objDateTime)
            );
            return $this->db->insert('daily_player_trans', $data); 
		}
	}

    public function get_existing_trans_today($playerId, $date, $transType) {
        $sql = "SELECT id FROM " . $this->tableName .
        " WHERE player_id=" . $playerId . 
        " AND date='" . $date . "'" .
        " AND trans_type=" . $transType;
        $qry = $this->db->query($sql);
        return $this->getOneRowOneField($qry, 'id');
    }

    public function get_today_withdraw($playerId) {
        $date = $this->getTodayForMysql();
        $sql = "SELECT trans_amount, trans_count FROM " . $this->tableName .
        " WHERE player_id=" . $playerId . 
        " AND date='" . $date . "'" .
        " AND trans_type=" . self::WITHDRAWAL;
        $qry = $this->db->query($sql);
        return $this->getOneRow($qry);
    }
}

/* End of file daily_player_trans.php */
/* Location: ./application/models/daily_player_trans.php */