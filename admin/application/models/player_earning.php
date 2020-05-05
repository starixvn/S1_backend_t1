<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

class Player_earning extends BaseModel {

	function __construct() {
		parent::__construct();
		//$this->load->model(array('player_model', 'transactions'));
	}

	protected $tableName = 'friend_referrial_monthly_earnings';

	public function getYearMonthListToNow() {
		$this->db->distinct();
		$this->db->select('year_month');
		$query = $this->db->get('friend_referrial_monthly_earnings');
		$rows = $query->result_array();
		$year_months = array_column($rows, 'year_month');
		return array_combine($year_months, $year_months);
	}

	public function updateTotalOfFriendReferrialMonthlyCommission($FRMID, $amount) {
		$this->db->update('friend_referrial_monthly_earnings', array('total_commission'=>$amount), array('id'=>$FRMID));
	}

	public function transferAllEarningsToWallet($year_month = null, $min_amount = 0) {
		$this->load->model('transactions');
		$this->utils->debug_log('year_month', $year_month);
		
		$this->db->from('friend_referrial_monthly_earnings')
			->where('paid_flag', self::DB_FALSE)
			->where('total_commission !=', 0);
		$rows = $this->runMultipleRow();
		if ( ! empty($rows)) {
			foreach ($rows as $me) {
				if ($me->total_commission > 0) {
					$this->transferOneEarningToMainWallet($me, $min_amount);
				}
			}
		}
		return TRUE;
	}

	public function transferOneEarningToMainWallet($me, $min_amount = 0, $check = true) {
		if ($this->utils->isEnabledFeature('switch_to_ibetg_commission') && $check) {
			//check total active players
			$total_bets_limits = 100000;
			$yearmonth = $me->year_month;
			$total_bets = $me->total_bets;
			for ($i = 1; $i <= 3; $i++) {
				$previousReport = $this->getPreviousEarning($me->player_id, $yearmonth);
				$this->utils->debug_log('me year_month : ' . $me->year_month . 'year_month : ' . $yearmonth . ', previousReport : ', $previousReport);
				if (!empty($previousReport)) {
					$total_bets += $previousReport->total_bets;
					$yearmonth = $previousReport->year_month;
					$transferOldEarnings[] = $previousReport;
				}
			}
			if ($total_bets < $ttoal_bets_limits)
				return false;
			$transferOldEarnings = array_reverse($transferOldEarnings);
			foreach($transferOldEarnings as $oldEarnings) {
				$this->transferOneEarningToMainWallet($oldEarnings, $min_amount, false);
			}
		}
		$success = true;
		$player_id = $me->player_id;
		// $balance = $me->balance;
		$amount = $me->total_commission;
		$yearmonth = $me->year_month;
		$extraNotes = " for " . $yearmonth . ' earning id:' . $me->id;
		//lock affiliate balance
		if($amount == 0){
			$this->utils->debug_log('cannot transfer 0');
			return $success;
		}
		$adminUserId = $this->authentication->getUserId();
		
		$self = $this;
		$db_false = self::DB_FALSE;
		$db_true = self::DB_TRUE;
		$success = $this->lockAndTrans(Utils::LOCK_ACTION_BALANCE, $player_id, function() use($self, $me, $player_id, $adminUserId, $amount, $extraNotes, $db_false, $db_true) {
			//check paid flag
			$success = false;
			$self->db->from('friend_referrial_monthly_earnings')->where('id', $me->id)->where('paid_flag', $db_false);
			if ($self->runExistsResult() && $amount != 0) {
				if ($amount > 0) {
					$tranId = $self->transactions->createPlayerReferralTransaction($player_id, $adminUserId, abs($amount), null, null, null, 'calculate ' . $me->year_month . ' friend referrial bouns for #' . $me->id);
				}
				if ($tranId) {
					//update paid
					$self->db->set('paid_flag', $db_true)->where('id', $me->id);
					$self->runAnyUpdate('friend_referrial_monthly_earnings');
					$success = true;
				} else {
					$self->utils->error_log('deposit player failed', $player_id, $amount, $extraNotes);
					$success = false;
				}
			}
			return $success;
		});
		// $lock_type = Utils::LOCK_ACTION_BALANCE;
		// $lock_it = $this->utils->lockActionById($player_id, $lock_type);
		// $this->utils->debug_log('lock player', $player_id, 'earning id', $me->id, 'amount', $amount);
		// // $created_at = date('Y-m-d H-m-s');
		// try {
		// 	if ($lock_it) {
		// 		//check paid flag
		// 		$this->db->from('friend_referrial_monthly_earnings')->where('id', $me->id)->where('paid_flag', self::DB_FALSE);
		// 		if ($this->runExistsResult() && $amount != 0) {
		// 			$this->startTrans();
		// 			if ($amount > 0) {
		// 				//$playerId, $adminUserId, $amount, $totalBeforeBalance = null, $beforeBalance = null, $afterBalance = null
		// 				$tranId = $this->transactions->createPlayerReferralTransaction($player_id, $adminUserId, abs($amount), null, null, null, 'calculate ' . $me->year_month . ' friend referrial bouns for #' . $me->id);
		// 			}
		// 			if ($tranId) {
		// 				//update paid
		// 				$this->db->set('paid_flag', self::DB_TRUE)->where('id', $me->id);
		// 				$this->runAnyUpdate('friend_referrial_monthly_earnings');
		// 			} else {
		// 				$this->utils->error_log('deposit player failed', $player_id, $amount, $extraNotes);
		// 				$success = false;
		// 			}
		// 			$success = $this->endTransWithSucc() && $success;
		// 		}
		// 	} else {
		// 		$this->utils->error_log('lock player failed', $player_id, $amount, $extraNotes);
		// 		$success = false;
		// 	}
		// } finally {
		// 	$rlt = $this->utils->releaseActionById($player_id, $lock_type);
		// }
		return $success;
	}	

	public function transferToMainWalletById($earningid, $min_amount = 0) {
		$this->db->from('friend_referrial_monthly_earnings')->where('id', $earningid);
		$success = true;
		$me = $this->runOneRow();
		if ($me) {
			$success = $this->transferOneEarningToMainWallet($me, $min_amount);
		}
		return $success;
	}

	public function getPreviousEarning($player_id, $year_month) {
		$d = new \DateTime();
		$year = substr($year_month, 0, 4);
		$month = substr($year_month, 4, 2);
		$d->setDate($year, $month, 1);
		$d->setTime(0, 0, 0);
		$d->modify('-1 month');
		$prev_year_month = $d->format('Ym');
		$this->db->where('player_id', $player_id);
		$this->db->where('year_month', $prev_year_month);
		$this->db->where('paid_flag', self::DB_FALSE); // this will reset to zero if paid
		$this->db->order_by('updated_at', 'DESC'); // this will get the adjustment
		$result = $this->db->get('friend_referrial_monthly_earnings');
		if ($result->num_rows() > 0) {
			return $result->result()[0]; // return first record
		} else {
			return null;
		}
	}

}

////END OF FILE/////////////////