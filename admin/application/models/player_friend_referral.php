<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 *
 * playerId = referee
 *
 */
class Player_friend_referral extends BaseModel {

	const TABLE_NAME = 'playerfriendreferral';

	private $tableName = self::TABLE_NAME;

	const STATUS_PAID = 3;
	const STATUS_CANCELLED = 4;

	public function __construct() {
		parent::__construct();
	}

	public function getReferredByPlayerId($playerId) {
		$query = $this->db->get(self::TABLE_NAME, array(
			'playerId' => $playerId,
		));
		return $query->result();
	}

	public function getUnprocessedReferrals() {
		$this->db->from(self::TABLE_NAME);
		$this->db->where('transactionId IS NULL', null, false)->where('status', self::STATUS_NORMAL);
		$this->db->order_by('playerId');
		$this->db->order_by('referredOn');

		return $this->runMultipleRow();
	}

	public function paidPlayerFriendReferral($referralId, $transactionId) {
		$this->db->set('transactionId', $transactionId)
			->set('status', self::STATUS_PAID)
			->where('referralId', $referralId);

		return $this->runAnyUpdate($this->tableName);
	}

	public function updatePlayerFriendReferral($referralId, $data) {
		$this->db->update(self::TABLE_NAME, $data, array(
			'referralId' => $referralId,
		));
	}

	public function checkAllReferrals() {
		$success = true;
		//get player
		$this->db->from('player')->where('ifnull(refereePlayerId,0)!=0', null, false);
		$rows = $this->runMultipleRow();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$this->syncReferral($row->refereePlayerId, $row->playerId);
			}
		}

		return $success;
	}

	public function syncReferral($refereePlayerId, $invitedPlayerId) {
		$this->db->from($this->tableName)->where('playerId', $refereePlayerId)
			->where('invitedPlayerId', $invitedPlayerId);
		if (!$this->runExistsResult()) {
			//add it
			$this->utils->debug_log('add player id', $refereePlayerId, 'invitedPlayerId', $invitedPlayerId);
			return $this->insertReferral($refereePlayerId, $invitedPlayerId);
		} else {
			$this->utils->debug_log('ignore player id', $refereePlayerId, 'invitedPlayerId', $invitedPlayerId);
		}
		return null;
	}

	public function insertReferral($refereePlayerId, $invitedPlayerId) {
		$data = array(
			'playerId' => $refereePlayerId,
			'invitedPlayerId' => $invitedPlayerId,
			'referredOn' => $this->getNowForMysql(),
			'status' => self::STATUS_NORMAL,
		);
		return $this->insertData($this->tableName, $data);

	}

	public function isUnprocessed($referral_id) {
		$this->db->from(self::TABLE_NAME);
		$this->db->where('referralId', $referral_id)->where('status', self::STATUS_NORMAL);

		return $this->runExistsResult();
	}

	public function checkReferral() {

		$this->load->model(array('friend_referral_settings', 'total_player_game_hour', 'promorules',
			'player_model', 'withdraw_condition', 'transactions', 'player_promo', 'users'));

		$settings = $this->friend_referral_settings->getFriendReferralSettings();
		$this->utils->debug_log('referral settings', $settings);
		$required_referred_deposit = isset($settings['ruleInDeposit']) ? $settings['ruleInDeposit'] : 0;
		$required_referred_bet = isset($settings['ruleInBet']) ? $settings['ruleInBet'] : 0;
		$required_referrer_bet = isset($settings['withdrawalCondition']) ? $settings['withdrawalCondition'] : 0;
		$referred_bonus = isset($settings['bonusAmount']) ? $settings['bonusAmount'] : 0;

		$success = 0;
		$qualified = 0;
		$paid_array = array();

		if ($referred_bonus <= 0) {
			$this->utils->debug_log('ignore referral because bonus <=0');
			return array($success, $qualified, $paid_array);
		}

		$now = $this->utils->getNowForMysql();
		$referral_list = $this->getUnprocessedReferrals();
		if (!empty($referral_list)) {
			$model = $this;
			$adminUserId = $this->users->getSuperAdminId();
			$promorulesId = $this->promorules->getSystemManualPromoRuleId(); // manual promo rules
			$promoCmsSettingId = $this->promorules->getSystemManualPromoCMSId(); // manual cms setting
			//$friend_referral_settings = $this->friend_referral_settings->getFriendReferralSettings();
			if($settings['promo_id'] != 0) { // if bind a cms on friend referral
				$this->load->model(array('promorules'));
				$promoCmsSettingId = $settings['promo_id'];
				$promorulesId = $this->promorules->getPromorulesIdByPromoCmsId($promoCmsSettingId);
			}
			foreach ($referral_list as $referral) {

				$this->utils->debug_log('check referral ', $referral->playerId, 'referralId', $referral->referralId);

				$referrer_id = $referral->playerId;

				$successTrans = $this->lockAndTransForPlayerBalance($referrer_id, function ()
					 use ($model, $referral, $adminUserId, $promorulesId, $promoCmsSettingId, $now, $referred_bonus,
						$required_referred_bet, $required_referrer_bet, $required_referred_deposit, &$qualified) {
						$referral_id = $referral->referralId;
						$referrer_id = $referral->playerId;
						$referred_id = $referral->invitedPlayerId;
						// $referred_player = $this->player_model->getPlayerById($referred_id);

						$referral_date = $referral->referredOn;
						list($totalDeposit, $totalWithdrawal, $totalBonus) = $model->transactions->getPlayerTotalDepositWithdrawalBonusByDatetime($referred_id, $referral_date, $now);
						list($totalBet, $totalWin, $totalLoss) = $model->total_player_game_hour->getPlayerTotalBetsWinsLossByDatetime($referred_id, $referral_date, $now);

						// $referred_username = $referred_player->username;

						$model->utils->debug_log('referrer_id', $referrer_id, 'referred_id', $referred_id, 'referral_date', $referral_date,
							'totalDepositAmount', $totalDeposit, 'required_referred_deposit', $required_referred_deposit, 'totalBettingAmount', $totalBet, 'required_referred_bet', $required_referred_bet);

						if ($model->utils->compareResultCurrency($totalDeposit, '>=', $required_referred_deposit)
							&& $model->utils->compareResultCurrency($totalBet, '>=', $required_referred_bet)
							&& $model->isUnprocessed($referral_id)) {

							$paid_array[] = array('player id:' . $referrer_id . ',invitedPlayerId:' . $referred_id .
								',totalDeposit:' . $totalDeposit . ',required_referred_deposit:' . $required_referred_deposit .
								',totalBet:' . $totalBet . ',required_referred_bet:' . $required_referred_bet);
							$qualified++;

							// $this->startTrans();

							# UPDATE MAIN WALLET AND SAVE TO TRANSACTION
							$transaction = $model->player_model->updateMainWalletBalance(array(
								'amount' => $referred_bonus,
								'transaction_type' => Transactions::PLAYER_REFER_BONUS,
								'from_id' => Transactions::SYSTEM_ID,
								'from_type' => Transactions::ADMIN,
								'to_id' => $referrer_id,
								'to_type' => Transactions::PLAYER,
								'note' => sprintf('%s referral bonus for referring %s', $model->utils->formatCurrencyNoSym($referred_bonus), $referred_id),
								'status' => Transactions::APPROVED,
								'flag' => Transactions::PROGRAM,
							));

							if ($transaction) {
                                $bet_amount_withdraw_condition = $required_referrer_bet * $referred_bonus;
								//update player promo
								$playerBonusAmount = $referred_bonus;
								$player_promo_id = $model->player_promo->approvePromoToPlayer($referrer_id,
									$promorulesId, $playerBonusAmount,
									$promoCmsSettingId, $adminUserId, null, $bet_amount_withdraw_condition);

                                //add requestAdmin
								$model->player_promo->addPlayerPromoRequestBy($player_promo_id, $adminUserId, null);

								$model->transactions->updatePlayerPromoId($transaction['id'], $player_promo_id);

								# GET FRIEND REFERRAL PROMOID, SET BY ADMIN - NEW FEATURE
								$bindPromoIdToFriendReferral = null;
								if ($this->utils->isEnabledFeature('bind_promorules_to_friend_referral')) {
									$bindPromoIdToFriendReferral = $promoCmsSettingId;
								}

								# CREATE WITHDRAWAL CONDITION
								$model->withdraw_condition->createWithdrawConditionForFriendReferral($referrer_id, $transaction['id'],
									$now, $bet_amount_withdraw_condition, $referred_bonus, $required_referrer_bet, $bindPromoIdToFriendReferral, $player_promo_id);

								# SET REFERRAL TO PROCESSED
								$model->paidPlayerFriendReferral($referral_id, $transaction['id']);

							}
							# COMMIT CHANGES
							// if ($this->endTransWithSucc()) {
							// }
							return true;

						}else{
							$this->utils->debug_log('ignore referral player', $referral_id);
						}

						return true;

					});

				if ($successTrans) {
					$success++;
				}
			}
		}

		return array($success, $qualified, $paid_array);
	}

	public function getPlayerFriendRefferedCountAndTotalBets($player_id, $startdate, $enddate) {
		$today_referred_count = 0;
		$today_total_bets = 0;
		$referred_sql = "SELECT * FROM playerfriendreferral WHERE playerId = '{$player_id}'";
		$referred_query = $this->db->query($referred_sql);
		$referreds = $referred_query->result_array();
		$today_referred_sql = $referred_sql . " AND referredOn >= '$startdate' AND referredOn <= '$enddate'";
		$today_referred_query = $this->db->query($today_referred_sql);
		if ($today_referred_query->num_rows() > 0)
			$today_referred_count = $today_referred_query->num_rows();
		foreach ($referreds as $referred) {
			$today_total_bet_sql = "SELECT sum(bet_amount) bet_amount FROM game_logs WHERE player_id = '{$referred['invitedPlayerId']}' AND end_at >= '$startdate' AND end_at <= '$enddate'";
			$total_bets_query = $this->db->query($today_total_bet_sql);
			if ($total_bets_query->num_rows() > 0)
			 	$today_total_bets += (float)$this->getOneRowOneField($total_bets_query, 'bet_amount');
		}
		return Array($today_referred_count, $today_total_bets);
	}

	public function getReferrerByInvitedPlayerId($invitedPlayerId) {
		$this->db->select('playerId');
		$this->db->from(self::TABLE_NAME);
		$this->db->where('invitedPlayerId', $invitedPlayerId);
		return $this->runOneRowOneField('playerId');
	}

	public function getTotalReferralBonusByPlayerId($player_id) {
		$this->db->select_sum('transactions.amount')
		    ->from(self::TABLE_NAME)
		    ->join('transactions', 'transactions.id = playerfriendreferral.transactionId')
			->where('playerfriendreferral.playerId', $player_id);

		$amount = $this->runOneRowOneField('amount');
		return $amount !== NULL ? $amount : 0;
	}
}

///END OF FILE