<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Withdraw_condition
 *
 * General behaviors include :
 *
 * * Cancelling withdrawal conditions
 * * Disable/create withdrawal condition for player, deposit, promo
 * * Get available withdraw condition
 * * Check and clean withdraw condition
 * * Player cash back wallet balance
 * * Get withdraw transaction details
 * * Auto check withdrawal condition
 * * Clear withdraw condition settings
 *
 * @category Marketing Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Withdraw_condition extends BaseModel {

	protected $tableName = 'withdraw_conditions';

	protected $idField = 'id';

	const SOURCE_DEPOSIT = 1;
	// const SOURCE_DEPOSIT_WITH_PROMO = 2;
	// const SOURCE_PROMOTION = 3;
	const SOURCE_MANUAL = 4;
	const SOURCE_FRIEND_REFERRAL = 5;
	const SOURCE_RANDOM_BONUS = 6;
	const SOURCE_NON_DEPOSIT = 7;
	const SOURCE_CASHBACK = 8;
	const SOURCE_BONUS = 9;

	const DEPOSIT_CONDITION_TYPE_FIXED = 0;
	const DEPOSIT_CONDITION_TYPE_NONFIXED = 1;

	const BONUS_RELEASE_FIXED_BONUSAMOUNT = 0;
	const BONUS_RELEASE_BY_PERCENTAGE = 1;

	const WITHDRAW_REQUIREMENT_RULE_BYBETTING = 0;
	const WITHDRAW_REQUIREMENT_RULE_NONBETTING = 1;

	// detail status flag
	const DETAIL_STATUS_ACTIVE = 1;
	const DETAIL_STATUS_FINISHED_BETTING_AMOUNT_WHEN_DEPOSIT = 2;
	const DETAIL_STATUS_CANCELLED_MANUALLY = 3;
	const DETAIL_STATUS_CANCELLED_DUE_TO_SMALL_BALANCE = 4;  // if player balance is less than condition amount
	const DETAIL_STATUS_FINISHED_BETTING_AMOUNT_WHEN_WITHDRAW = 5;
	const DETAIL_STATUS_FINISHED_BY_DELETING_PROMO_MANAGER = 6;
	const DETAIL_STATUS_CANCELED_BY_DELETING_PROMO_MANAGER = 7;

	const WITHDRAW_CONDITION_TYPE_BETTING = 1; //default value
	const WITHDRAW_CONDITION_TYPE_DEPOSIT = 2;

	const UN_FINISHED_WITHDRAW_CONDITIONS_FLAG = 0; //default value
    const FINISHED_WITHDRAW_CONDITIONS_FLAG = 1;
	// afbw = auto_finished_by_withdraw
	const REASON_AFBW = 'auto finished by withdraw';
	/**
	 * overview : Withdraw_condition constructor.
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * overview : cancel withdrawal condition
	 *
	 * @param int	$playerpromoId
	 * @param bool|false $updated
	 * @return bool
	 */
	public function cancelWithdrawalConditionByPlayerPromoId($playerpromoId, &$updated = false) {
		if (!empty($playerpromoId)) {
			$this->db->from('withdraw_conditions')->where('player_promo_id', $playerpromoId)->limit(1);

			$row = $this->runOneRowArray();
			if (!empty($row)) {

				$this->utils->debug_log('cancel withdraw condition', $playerpromoId, 'withdraw condition id', $row['id']);

				$data['status'] = parent::STATUS_DISABLED;
				$data['updated_at'] = $this->utils->getNowForMysql();
				$data['stopped_at'] = $this->utils->getNowForMysql();
				$data['admin_user_id'] = $this->authentication->getUserId();

				$this->db->where_in('id', [$row['id']]);
				$this->db->set($data);
				// $update = array('status' => self::STATUS_DISABLED);
				// $this->db->update($this->tableName, $update);
				$updated = !!$this->runAnyUpdateWithResult('withdraw_conditions');
			}
		}
		return true;
	}

	/**
	 * overview : cancel withdrawal condition by trans id
	 *
	 * @param int	$transId
	 * @param int	$playerId
	 * @param bool|false $updated
	 * @return bool
	 */
	public function cancelWithdrawalConditionByTransId($transId, $playerId, &$updated = false) {
		if (!empty($transId) && !empty($playerId)) {
			$this->db->from('withdraw_conditions')->where('source_id', $transId)
				->where('player_id', $playerId)->limit(1);

			$row = $this->runOneRowArray();
			if (!empty($row)) {

				$this->utils->debug_log('cancel withdraw condition', $playerId, $transId, 'withdraw condition id', $row['id']);

				$data['status'] = parent::STATUS_DISABLED;
				$data['updated_at'] = $this->utils->getNowForMysql();
				$data['stopped_at'] = $this->utils->getNowForMysql();
				$data['admin_user_id'] = $this->authentication->getUserId();

				$this->db->where_in('id', [$row['id']]);
				$this->db->set($data);
				// $update = array('status' => self::STATUS_DISABLED);
				// $this->db->update($this->tableName, $update);
				$updated = !!$this->runAnyUpdateWithResult('withdraw_conditions');
			}
		}
		return true;
	}

	/**
	 * overview : disable player withdrawal condition
	 *
	 * @param int	$playerId
	 * @return bool
	 */
	function disablePlayerWithdrawalCondition($playerId, $reason=null, $detailStatus=self::DETAIL_STATUS_ACTIVE, $applyWithdrawDatetime=null) {
		$data['status'] = parent::STATUS_DISABLED;
		$data['updated_at'] = $this->utils->getNowForMysql();
		$data['stopped_at'] = $this->utils->getNowForMysql();
		$data['admin_user_id'] = $this->authentication->getUserId();
		$data['detail_status'] = $detailStatus;
        $data['note'] = $reason;

        if(isset($applyWithdrawDatetime)){
            $this->db->where('started_at <', $applyWithdrawDatetime);
        }

		$this->db->where('player_id', $playerId);
		$this->db->where('status', self::STATUS_NORMAL);   # only clear active, to avoid update all
		// $this->db->update($this->tableName, $data);
		$success =  $this->runUpdate($data);

		if ($success) {
			$this->load->model(['player_promo']);

			$this->db->select('player_id, player_promo_id')->from('withdraw_conditions')
					->where_in('player_id', $playerId)
					->where('player_promo_id is not null', null, false);

			$rows = $this->runMultipleRowArray();

			if (!empty($rows)) {
				$playerPromoArr = [];
				foreach ($rows as $row) {
					$playerPromoArr[] = $row['player_promo_id'];
				}
				if (!empty($playerPromoArr)) {
					$this->player_promo->finishPlayerPromos($playerPromoArr, $reason);
				}
			}
		}

		return $success;
	}

	/**
	 * overview : create withdrawal condition
	 *
	 * @param $isDeposit
	 * @param $player_id
	 * @param $bonusTransId
	 * @param $withdrawBetAmtCondition
	 * @param $deposit_amount
	 * @param $bonus_amount
	 * @param $bet_times
	 * @param $promorule
	 * @param $depositTransId
	 * @param null $playerpromoId
	 * @param null $startDate
	 * @param null $note
	 * @return mixed
	 */
	function createWithdrawConditionForPromoruleBonus($isDeposit // #1
		, $player_id // #2
		, $bonusTransId // #3
		, $withdrawBetAmtCondition // #4
		, $deposit_amount // #5
		, $bonus_amount // #6
		, $bet_times // #7
		, $promorule // #8
		, $depositTransId // #9
		, $playerpromoId = null // #10
		, $startDate = null // #11
		, $note = null // #12
		, &$extra_info = null // #13
	) {

		$this->load->model(['promorules']);

		if (empty($startDate)) {
			$startDate = $this->utils->getNowForMysql();
		}

		$promorulesId = $promorule['promorulesId'];
		if (empty($withdrawBetAmtCondition)) {
			$withdrawBetAmtCondition = 0;
		}

		if (empty($deposit_amount)) {
			$deposit_amount = 0;
		}

		if (empty($bonus_amount)) {
			$bonus_amount = 0;
		}

		$subWalletId = null;
		if (isset($extra_info['subWalletId'])) {
			$subWalletId = $extra_info['subWalletId'];
		} elseif (isset($promorule['releaseToSubWallet'])) {
			$subWalletId = $promorule['releaseToSubWallet'];
		}

		$cond = $this->getWithdrawConditionByPlayerPromoId($playerpromoId);
		if (empty($cond)) {

			$data = array(
				'source_id' => $bonusTransId,
				'source_type' => $isDeposit ? self::SOURCE_DEPOSIT : self::SOURCE_BONUS,//self::SOURCE_NON_DEPOSIT,
				'started_at' => $startDate,
				'condition_amount' => $withdrawBetAmtCondition,
				'status' => self::STATUS_NORMAL,
				'player_id' => $player_id,
				'deposit_amount' => $deposit_amount,
				'bonus_amount' => $bonus_amount,
				'bet_times' => $bet_times,
				'promotion_id' => $promorulesId,
				'promorules_json' => @$promorule['json_info'],
				'wallet_type' => $subWalletId,
				'player_promo_id' => $playerpromoId,
				'note' => $note,
				'trigger_amount' => $bonus_amount,
                'withdraw_condition_type' => self::WITHDRAW_CONDITION_TYPE_BETTING
			);
			$rlt = $this->insertRow($data);
		} else {
			$data = array(
				'source_id' => $bonusTransId,
				'source_type' => $isDeposit ? self::SOURCE_DEPOSIT : self::SOURCE_BONUS,//self::SOURCE_NON_DEPOSIT,
				'started_at' => $startDate,
				'condition_amount' => $withdrawBetAmtCondition,
				'status' => self::STATUS_NORMAL,
				'player_id' => $player_id,
				'deposit_amount' => $deposit_amount,
				'bonus_amount' => $bonus_amount,
				'bet_times' => $bet_times,
				'promotion_id' => $promorulesId,
				'promorules_json' => @$promorule['json_info'],
				'wallet_type' => $subWalletId,
				'player_promo_id' => $playerpromoId,
				'note' => $note,
				'trigger_amount' => $bonus_amount,
                'withdraw_condition_type' => self::WITHDRAW_CONDITION_TYPE_BETTING
			);

			$this->db->where('id', $cond['id'])->update('withdraw_conditions', $data);
			$rlt = $cond['id'];
		}

		return $rlt;
	}

    /**
     * overview : create deposit condition
     *
     * @param $isDeposit
     * @param $player_id
     * @param $bonusTransId
     * @param $withdrawBetAmtCondition
     * @param $deposit_amount
     * @param $bonus_amount
     * @param $bet_times
     * @param $promorule
     * @param $depositTransId
     * @param null $playerpromoId
     * @param null $startDate
     * @param null $note
     * @return mixed
     */
	function createDepositConditionForPromoruleBonus($isDeposit // #1
				, $player_id // #2
				, $bonusTransId // #3
				, $withdrawDepAmtCondition // #4
				, $deposit_amount // #5
				, $bonus_amount // #6
				, $bet_times // #7
				, $promorule // #8
				, $depositTransId // #9
				, $playerpromoId = null // #10
				, $startDate = null // #11
				, $note = null // #12
				, &$extra_info = null // #13
	) {

        $this->load->model(['promorules']);

        if (empty($startDate)) {
            $startDate = $this->utils->getNowForMysql();
        }

        $promorulesId = $promorule['promorulesId'];
        if (empty($withdrawDepAmtCondition)) {
            $withdrawDepAmtCondition = 0;
        }

        if (empty($deposit_amount)) {
            $deposit_amount = 0;
        }

        if (empty($bonus_amount)) {
            $bonus_amount = 0;
        }

        $subWalletId = null;
        if (isset($extra_info['subWalletId'])) {
            $subWalletId = $extra_info['subWalletId'];
        } elseif (isset($promorule['releaseToSubWallet'])) {
            $subWalletId = $promorule['releaseToSubWallet'];
        }

        $cond = $this->getDepositConditionByPlayerPromoId($playerpromoId);
        if (empty($cond)) {

            $data = array(
                'source_id' => $bonusTransId,
                'source_type' => $isDeposit ? self::SOURCE_DEPOSIT : self::SOURCE_BONUS,//self::SOURCE_NON_DEPOSIT,
                'started_at' => $startDate,
                'condition_amount' => $withdrawDepAmtCondition,
                'status' => self::STATUS_NORMAL,
                'player_id' => $player_id,
                'deposit_amount' => $deposit_amount,
                'bonus_amount' => $bonus_amount,
                'bet_times' => $bet_times,
                'promotion_id' => $promorulesId,
                'promorules_json' => @$promorule['json_info'],
                'wallet_type' => $subWalletId,
                'player_promo_id' => $playerpromoId,
                'note' => $note,
                'trigger_amount' => $bonus_amount,
                'withdraw_condition_type' => self::WITHDRAW_CONDITION_TYPE_DEPOSIT
            );
            $rlt = $this->insertRow($data);
        } else {
            $data = array(
                'source_id' => $bonusTransId,
                'source_type' => $isDeposit ? self::SOURCE_DEPOSIT : self::SOURCE_BONUS,//self::SOURCE_NON_DEPOSIT,
                'started_at' => $startDate,
                'condition_amount' => $withdrawDepAmtCondition,
                'status' => self::STATUS_NORMAL,
                'player_id' => $player_id,
                'deposit_amount' => $deposit_amount,
                'bonus_amount' => $bonus_amount,
                'bet_times' => $bet_times,
                'promotion_id' => $promorulesId,
                'promorules_json' => @$promorule['json_info'],
                'wallet_type' => $subWalletId,
                'player_promo_id' => $playerpromoId,
                'note' => $note,
                'trigger_amount' => $bonus_amount,
                'withdraw_condition_type' => self::WITHDRAW_CONDITION_TYPE_DEPOSIT
            );

            $this->db->where('id', $cond['id'])->update('withdraw_conditions', $data);
            $rlt = $cond['id'];
        }

        return $rlt;
    }

	/**
	 * overview : get withdrawal condition
	 *
	 * @param  int	$playerpromoId
	 * @return array
	 */
	function getWithdrawConditionByPlayerPromoId($playerpromoId) {
		$this->db->from('withdraw_conditions')->where('player_promo_id', $playerpromoId)->limit(1);
		return $this->runOneRowArray();
	}

	function getWithdrawConditionByPromoCmsSettingId($promocmsId){
        $deleted_promocms_filter = [self::DETAIL_STATUS_FINISHED_BY_DELETING_PROMO_MANAGER,self::DETAIL_STATUS_CANCELED_BY_DELETING_PROMO_MANAGER];
	    $this->db->select('withdraw_conditions.*')
                 ->from($this->tableName)
                 ->join('playerpromo','withdraw_conditions.player_promo_id = playerpromo.playerpromoId', 'left')
                 ->where('playerpromo.promoCmsSettingId', $promocmsId)
                 ->where('withdraw_conditions.status', parent::STATUS_NORMAL)
                 ->where('withdraw_conditions.detail_status not in (' . implode(',', $deleted_promocms_filter) . ')');

        return $this->runMultipleRowArray();
    }

    function updateWithdrawConditionByPromoCmsSettingId($withdraw_conditions){
	    $success = FALSE;
        if(!empty($withdraw_conditions)){
            foreach($withdraw_conditions as $wc){

                $data['detail_status'] = self::DETAIL_STATUS_CANCELED_BY_DELETING_PROMO_MANAGER;   //unfinished wc

                if($wc['is_finished'] == self::FINISHED_WITHDRAW_CONDITIONS_FLAG){
                    $data['detail_status'] = self::DETAIL_STATUS_FINISHED_BY_DELETING_PROMO_MANAGER;   //finished or canceled wc
                }

                $data['status'] = parent::STATUS_DISABLED;
                $data['updated_at'] = $this->utils->getNowForMysql();
                $data['stopped_at'] = $this->utils->getNowForMysql();
                $data['admin_user_id'] = $this->authentication->getUserId();

                $success = $this->updateRow($wc['id'], $data);
                $this->utils->debug_log('-----------------------> updateWithdrawConditionByPromoCmsSettingId withdraw condition id ', $wc['id']);
            }
        }

        return $success;
    }

    /**
     * overview : get deposit condition
     *
     * @param  int	$playerpromoId
     * @return array
     */
    function getDepositConditionByPlayerPromoId($playerpromoId) {
        $this->db->from('withdraw_conditions')
                 ->where('player_promo_id', $playerpromoId)
                 ->where('withdraw_condition_type', self::WITHDRAW_CONDITION_TYPE_DEPOSIT)
                 ->limit(1);
        return $this->runOneRowArray();
    }

	/**
	 * overview : create withdrawal condition for deposit only
	 * @param $saleOrder
	 * @param $depositTransId
	 * @param $withdrawBetAmtCondition
	 * @param $nonPromoWithdrawSettingVal
	 * @return array
	 */
	function createWithdrawConditionForDepositOnly($saleOrder // #1
					, $depositTransId // #2
					, $withdrawBetAmtCondition // #3
					, $nonPromoWithdrawSettingVal // #4
	) {
		// $withdrawalConditionData = array('source_id' => $walletAccountId,
		// 	'source_type' => 1, //source is transaction
		// 	'started_at' => date('Y-m-d H:i:s'),
		// 	'condition_amount' => $withdrawBetAmtCondition,
		// 	'status' => 1, //enabled
		// 	'player_id' => $playerId,
		// );

		$data = array(
			'source_id' => $depositTransId,
			'source_type' => self::SOURCE_DEPOSIT,
			'started_at' => $this->utils->getNowForMysql(),
			'condition_amount' => $withdrawBetAmtCondition,
			'status' => self::STATUS_NORMAL,
			'player_id' => $saleOrder->player_id,
			'deposit_amount' => $saleOrder->amount,
			'bet_times' => $nonPromoWithdrawSettingVal,
		);

		return $this->insertRow($data);
	}

	/**
	 * overview : create withdrawal condition for cash back
	 *
	 * @param int		$transId
	 * @param double	$withdrawBetAmtCondition
	 * @param int		$withdraw_condition_bet_times
	 * @param int $player_id
	 * @param double $deposit_amount
	 * @return array
	 */
	function createWithdrawConditionForCashback($transId, $withdrawBetAmtCondition,
		$withdraw_condition_bet_times, $player_id, $trigger_amount) {

		$data = array(
			'source_id' => $transId,
			'source_type' => self::SOURCE_BONUS,//self::SOURCE_CASHBACK,
			'started_at' => $this->utils->getNowForMysql(),
			'condition_amount' => $withdrawBetAmtCondition,
			'status' => self::STATUS_NORMAL,
			'player_id' => $player_id,
			// 'deposit_amount' => $deposit_amount,
			'bet_times' => $withdraw_condition_bet_times,
			'trigger_amount' => $trigger_amount
		);

		return $this->insertRow($data);
	}

	function createWithdrawCondForDepositOnly($transId, $withdrawBetAmtCondition,
		$withdraw_condition_bet_times, $player_id, $deposit_amount) {

		$data = array(
			'source_id' => $transId,
			'source_type' => self::SOURCE_DEPOSIT,//self::SOURCE_CASHBACK,
			'started_at' => $this->utils->getNowForMysql(),
			'condition_amount' => $withdrawBetAmtCondition,
			'status' => self::STATUS_NORMAL,
			'player_id' => $player_id,
			'deposit_amount' => $deposit_amount,
			'bet_times' => $withdraw_condition_bet_times,
			// 'trigger_amount' => $trigger_amount
		);

		return $this->insertRow($data);
	}

	/**
	 * overview : create withdrawal condition for member group deposit bonus
	 * @param int		$player_id
	 * @param int		$bonusTransId
	 * @param double	$withdrawBetAmtCondition
	 * @param double	$deposit_amount
	 * @param double	$bonus_amount
	 * @param int		$bet_times
	 * @return array
	 */
	function createWithdrawConditionForMemberGroupDepositBonus($player_id, $bonusTransId, $withdrawBetAmtCondition,
		$deposit_amount, $bonus_amount, $bet_times) {
		$data = array(
			'source_id' => $bonusTransId,
			'source_type' => self::SOURCE_DEPOSIT,
			'started_at' => $this->utils->getNowForMysql(),
			'condition_amount' => $withdrawBetAmtCondition,
			'status' => self::STATUS_NORMAL,
			'player_id' => $player_id,
			'deposit_amount' => $deposit_amount,
			'bonus_amount' => $bonus_amount,
			'bet_times' => $bet_times,
			'trigger_amount' => $bonus_amount
		);

		return $this->insertRow($data);
	}

	/**
	 * overview : create withdrawal condition for random bonus
	 *
	 * @param int		$player_id
	 * @param int		$bonusTransactionId
	 * @param double	$withdrawBetAmtCondition
	 * @param double	$bonus_amount
	 * @param int	$bet_times
	 * @return array
	 */
	function createWithdrawConditionForRandomBonus($player_id, $bonusTransactionId, $withdrawBetAmtCondition,
		$bonus_amount, $bet_times) {

		$data = array(
			'source_id' => $bonusTransactionId,
			'source_type' => self::SOURCE_BONUS,//self::SOURCE_NON_DEPOSIT,
			'started_at' => $this->utils->getNowForMysql(),
			'condition_amount' => $withdrawBetAmtCondition,
			'status' => self::STATUS_NORMAL,
			'player_id' => $player_id,
			'bonus_amount' => $bonus_amount,
			'bet_times' => $bet_times,
			'trigger_amount' => $bonus_amount
		);

		return $this->insertRow($data);
	}

	/**
	 * overview : create withdrawal condition for friend referral
	 *
	 * @param int		$playerId
	 * @param int		$bonusTransId
	 * @param date		$referralDate
	 * @param double	$conditionAmount
	 * @param double	$bonus_amount
	 * @param int		$bet_times
	 * @return array
	 */
	function createWithdrawConditionForFriendReferral($playerId, $bonusTransId, $referralDate, $conditionAmount,
		$bonus_amount, $bet_times, $promotion_id = null, $player_promo_id = null) {

		$data = array(
			'source_id' => $bonusTransId,
			'source_type' => self::SOURCE_BONUS,
			'started_at' => $referralDate,
			'condition_amount' => $conditionAmount,
			'status' => self::STATUS_NORMAL,
			'player_id' => $playerId,
			'bonus_amount' => $bonus_amount,
			'bet_times' => $bet_times,
			'promotion_id' => $promotion_id,
			'trigger_amount' => $bonus_amount,
            'player_promo_id' => $player_promo_id
		);

		return $this->insertRow($data);
	}

	/**
	 * overview : create withdrawal condition for birthday bonus
	 *
	 * @param int		$transId
	 * @param double	$withdrawBetAmtCondition
	 * @param int		$withdraw_condition_bet_times
	 * @param int $player_id
	 * @return array
	 */
	function createWithdrawConditionForBirthdayBonus($transId, $withdrawBetAmtCondition, $bonus_amount,
		$withdraw_condition_bet_times, $player_id) {

		$data = array(
			'source_id' => $transId,
			'source_type' => self::SOURCE_BONUS,//self::SOURCE_NON_DEPOSIT,
			'started_at' => $this->utils->getNowForMysql(),
			'condition_amount' => $withdrawBetAmtCondition,
			'status' => self::STATUS_NORMAL,
			'player_id' => $player_id,
			'bonus_amount' => $bonus_amount,
			'bet_times' => $withdraw_condition_bet_times,
			'note' => lang("Birthday Bonus"),
			'trigger_amount' => $bonus_amount
		);
		return $this->insertRow($data);
	}

	//===create withdraw condition=======================================

	// function createWithdrawConditionForDepositPromo($saleOrder, $transactionId, $promorulesId, $withdrawBetAmtCondition) {
	// 	// $withdrawalConditionData = array('source_id' => $walletAccountId,
	// 	// 	'source_type' => 2, //source is transaction
	// 	// 	'started_at' => date('Y-m-d H:i:s'),
	// 	// 	'condition_amount' => $withdrawBetAmtCondition,
	// 	// 	'status' => 1, //enabled
	// 	// 	'player_id' => $playerId,
	// 	// 	'promotion_id' => $promorules[0]['promorulesId'],
	// 	// );
	// 	$data = array(
	// 		'source_id' => $transactionId,
	// 		'source_type' => self::SOURCE_DEPOSIT_WITH_PROMO,
	// 		'started_at' => $this->utils->getNowForMysql(),
	// 		'condition_amount' => $withdrawBetAmtCondition,
	// 		'status' => self::STATUS_NORMAL,
	// 		'player_id' => $saleOrder->player_id,
	// 		'promotion_id' => $promorulesId,
	// 	);

	// 	$this->insertRow($data);
	// }

	/**
	 * overview : get first available withdraw condition
	 *
	 * @param $playerId
	 * @return null
	 */
	public function getFirstAvailableWithdrawCondition($playerId) {
		$this->db->select('min(started_at) as firstDateTime', false)
			->from('withdraw_conditions')->where('player_id', $playerId)
			->where('status', self::STATUS_NORMAL);

		return $this->runOneRowOneField('firstDateTime');
	}

	/**
	 * overview : get available amount on withdraw condition
	 *
	 * @param int	$playerId
	 * @return double
	 */
	public function getAvailableAmountOnWithdrawCondition($playerId) {
		$this->db->select('sum(condition_amount) as amount', false)
			->from('withdraw_conditions')->where('player_id', $playerId)
			->where('status', self::STATUS_NORMAL);

		return $this->runOneRowOneField('amount');
	}

	// const ACTION_CLEAN_WITHDRAW_CONDITION = 'Clean Withdraw Condition';
	// const MANAGEMENT_WITHDRAW_CONDITION = 'Withdraw Condition';

	/**
	 * overview : check and clean withdraw condition
	 *
	 * @param int	$playerId
	 * @return bool
	 */
	public function checkAndCleanWithdrawCondition($playerId) {
		$this->load->model(array('operatorglobalsettings', 'wallet_model', 'player_promo', 'game_logs'));
		$balLimit = $this->operatorglobalsettings->getSettingValue('previous_balance_set_amount');
		// $settings=$this->getClearWithdrawConditionSettings();
		// $balLimit = $settings['total'];

		$bal = $this->wallet_model->getTotalBalance($playerId);

		$this->utils->debug_log('playerId', $playerId, 'balLimit', $balLimit, 'bal', $bal);
		$clear = false;
		$description = null;
		if ($bal <= $balLimit) {
			//inactive all
			$clear = true;
			$description = 'clean withdraw condition, because last balance (' . $bal . ') of player ' . $playerId . ' <= ' . $balLimit;
		} else {
			//check bet amount >= withdraw condition
			//get first date
			$firstDateTime = $this->getFirstAvailableWithdrawCondition($playerId);

			if (!empty($firstDateTime)) {

				$betAmount = $this->game_logs->getPlayerCurrentBet($playerId, $firstDateTime);
				$conditionAmount = $this->getAvailableAmountOnWithdrawCondition($playerId);
				$this->utils->debug_log('betAmount', $betAmount, 'conditionAmount', $conditionAmount);
				$clear = $betAmount >= $conditionAmount;

				$description = 'clean withdraw condition, because total bet amount (' . $betAmount . ' from ' . $firstDateTime . ') of player ' . $playerId . ' >= ' . $conditionAmount;
			}
		}

		if ($clear) {
			$rlt = $this->disablePlayerWithdrawalCondition($playerId);
			$this->utils->debug_log('disablePlayerWithdrawalCondition', $rlt, 'playerId', $playerId);
			// $rlt = $this->player_promo->disablePlayerPromo($playerId);
			// $this->utils->debug_log('disablePlayerPromo', $rlt);

			// $this->utils->recordAction(self::MANAGEMENT_WITHDRAW_CONDITION, self::ACTION_CLEAN_WITHDRAW_CONDITION, $description);
		}

		return true;

		// $playerWithdrawalCondition = $this->ci->payment->getPlayerWithdrawalCondition($playerId);
		// //var_dump($playerWithdrawalCondition);exit();
		// if (!empty($playerWithdrawalCondition)) {
		// 	foreach ($playerWithdrawalCondition as $key) {
		// 		$data['status'] = 2;
		// 		$this->ci->payment->inactiveWithdrawCondition($data, $key['withdrawConditionId']);
		// 	}
		// }

	}

	public function getAllPlayersAvailableAmountOnWithdrawConditionByCashbackSettings($date, $startHour, $endHour, $start_date=null, $end_date=null, $player_id=null) {

		$lastDate = $this->utils->getLastDay($date);

		if (intval($endHour) == 23) {
			//all yesterday
			$date = $lastDate;
		}

		$from = $lastDate . ' ' . $startHour . ':00:00';
		$to = $date . ' ' . $endHour . ':59:59';

        // weekly cashback
        if(!empty($start_date) && !empty($end_date)){
            $from = $start_date . ' ' . $startHour . ':00:00';
            $to = $end_date . ' ' . $endHour . ':59:59';
        }
        $data = [$from, $to, '1'];

        //only for promotion
		$player_sql='';
		if(!empty($player_id)){
			$player_sql=' and player_id=? ';
            $data[]=$player_id;
		}

        $player_sql .=' and withdraw_conditions.status=? ';
        $data[]=self::STATUS_NORMAL;

		$sql = "SELECT player_id, sum(condition_amount) as amount FROM  withdraw_conditions
		join promorules on (withdraw_conditions.promotion_id=promorules.promorulesId)
		WHERE withdraw_conditions.started_at >= ? AND withdraw_conditions.started_at<= ?
		and promorules.disable_cashback_if_not_finish_withdraw_condition=?
		and withdraw_conditions.withdraw_condition_type=" . self::WITHDRAW_CONDITION_TYPE_BETTING . "
		{$player_sql}
		GROUP BY player_id ";

		$query = $this->db->query($sql, $data);

		$rows = $this->getMultipleRow($query);
		//echo $this->db->last_query();

		$wc_amount_map = array();
		if(!empty($rows)){
			foreach ($rows as $row) {
				$wc_amount_map[$row->player_id] = $row->amount;
			}
		}

		return $wc_amount_map;
	}

	// public function getAllPlayersAvailableAmountOnWithdrawConditionByCashbackSettings($date, $startHour, $endHour) {

	// 	$lastDate = $this->utils->getLastDay($date);

	// 	if (intval($endHour) == 23) {
	// 		//all yesterday
	// 		$date = $lastDate;
	// 	}

	// 	$from = $lastDate .' '. $startHour.':00:00';
	// 	$to = $date .' '. $endHour.':59:59';

	// 	$sql= "SELECT player_id, sum(condition_amount) as amount FROM  withdraw_conditions WHERE status = ? AND started_at BETWEEN ? AND ? GROUP BY player_id ";

	// 	$data = array(self::STATUS_NORMAL, $from , $to);
	// 	$query = $this->db->query($sql,$data);

	// 	$rows = $this->getMultipleRow($query);

	//     //echo $this->db->last_query();
	// 	$wc_amount_map = array();

	// 	foreach ($rows as $row) {
	// 		$wc_amount_map[$row->player_id] = $row->amount;
	// 	}

	// 	return $wc_amount_map ;

	// }

	/**
	 * overview : cancel withdrawal condition
	 *
	 * @param  array	$ids
	 * @return bool
	 */
	public function cancelWithdrawalCondition($ids, $cancelManualStatus=self::DETAIL_STATUS_ACTIVE) {
		$data['status'] = parent::STATUS_DISABLED;
		$data['updated_at'] = $this->utils->getNowForMysql();
		$data['stopped_at'] = $this->utils->getNowForMysql();
		$data['admin_user_id'] = $this->authentication->getUserId();
		if(!empty($cancelManualStatus)) {
			$data['detail_status'] = $cancelManualStatus;
		}
		$this->db->where_in('id', $ids);
		$this->db->set($data);
		$success = $this->runAnyUpdate('withdraw_conditions');

		// $this->utils->printLastSQL();

		if ($success) {
			$this->load->model(['player_promo']);

			$this->db->select('player_id, player_promo_id')->from('withdraw_conditions')
				->where_in('id', $ids)
				->where('player_promo_id is not null', null, false);

			$rows = $this->runMultipleRowArray();

			$this->utils->printLastSQL();
			$this->utils->debug_log('rows', count($rows));
			if (!empty($rows)) {
				$playerPromoArr = [];
				foreach ($rows as $row) {
					$playerPromoArr[] = $row['player_promo_id'];
				}
				if (!empty($playerPromoArr)) {
					$this->player_promo->finishPlayerPromos($playerPromoArr, 'auto finished by cancel withdraw condition');
				}
			}

		}

		return $success;
	}

	/**
	 * overview : get player withdrawal condition
	 *
	 * @param int	$playerId
	 * @return array|bool
	 */
	public function getPlayerWithdrawalCondition($playerId) {
        if($this->utils->isEnabledFeature('disabled_withdraw_condition_share_betting_amount')){
            return $this->getPlayerWithdrawalConditionWithoutSharingBettingAmount($playerId);
        }else{
            $this->load->model(array('transactions', 'promorules'));

            // git issue #1371: added promocmssetting.deleted_flag
            $this->db->distinct()->select('promorules.promorulesId,
						   promorules.promoName,
						   promocmssetting.promoCmsSettingId,
						   promocmssetting.promo_code,
						   promocmssetting.deleted_flag AS promocms_del_flag,
						   promorules.promoType,
						   promorules.nonDepositPromoType,
						   promorules.deleted_flag AS promorules_del_flag,
						   promorules.withdrawRequirementDepositConditionType,
						   player.username,
						   player.playerId,
						   playerpromo.playerpromoId,
						   playerpromo.withdrawalStatus,
						   playerpromo.promoStatus,
						   playerpromo.note as pp_note,
						   withdraw_conditions.bonus_amount bonusAmount,
						   withdraw_conditions.id as withdrawConditionId,
						   withdraw_conditions.condition_amount as conditionAmount,
						   withdraw_conditions.started_at,
						   withdraw_conditions.source_type,
						   withdraw_conditions.source_id,
						   withdraw_conditions.wallet_type,
						   withdraw_conditions.note,
						   withdraw_conditions.trigger_amount,
						   withdraw_conditions.withdraw_condition_type,
						   promotype.promoTypeName,
						   promotype.promoTypeDesc,
						   promorules.promoCategory,
						   withdraw_conditions.deposit_amount as walletDepositAmount')
                ->from('withdraw_conditions')
                ->join('playerpromo', 'playerpromo.playerpromoId = withdraw_conditions.player_promo_id', 'left')
                ->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId OR promorules.promorulesId = withdraw_conditions.promotion_id', 'left')
                ->join('promocmssetting', 'promocmssetting.promoId = promorules.promorulesId', 'left')
                ->join('promotype', 'promotype.promotypeId = promorules.promoCategory', 'left')
                ->join('player', 'player.playerId = withdraw_conditions.player_id', 'left');

            $this->db->where('withdraw_conditions.player_id', $playerId);
            $this->db->where('withdraw_conditions.status', self::STATUS_NORMAL);
            $this->db->where('promocmssetting.deleted_flag IS NULL', null, false);

		    $this->db->order_by("withdraw_conditions.started_at", "desc");

            $query = $this->db->get();

            $this->utils->printLastSQL();

            if ($query->num_rows() > 0) {
                $this->load->model(array('total_player_game_hour', 'group_level', 'game_logs'));
                $data = array();

                $gameDescIdArrFromLevel=$this->group_level->getAllowedGameIdArr($playerId);

                $wd_ident_ar = [];

                foreach ($query->result_array() as $row) {
                    // git issue #1371, hide probably one-to-many promorules-promocmsSettings matches
                    $wd_ident = "{$row['withdrawConditionId']}-{$row['promorulesId']}-{$row['playerpromoId']}-{$row['source_id']}";
                    if (array_key_exists($wd_ident, $wd_ident_ar)) {
                        continue;
                    }
                    else {
                        $wd_ident_ar[$wd_ident] = 1;
                    }
                    $gameDescIdArr=$gameDescIdArrFromLevel;

                    //check if deposit promo and non deposit promo (email,registration,mobile)
                    if ($row['promoType'] == Promorules::PROMO_TYPE_NON_DEPOSIT ||
                        $row['promoType'] == Promorules::PROMO_TYPE_DEPOSIT) {
                        if ($row['promorulesId']) {
                            $this->load->model(array('promorules'));
                            $gameDescIdArr = $this->promorules->getPlayerGames($row['promorulesId']);
                            $this->utils->debug_log('gameDescIdArr from promo rules:'.$row['promorulesId']);
                        }
                    }

					$row['currentBet'] = $this->game_logs->totalPlayerBettingAmountWithLimitByVIP($row['playerId'], $row['started_at'], null, $gameDescIdArr);

                    switch ($row['withdraw_condition_type']){
                        case self::WITHDRAW_CONDITION_TYPE_DEPOSIT:
                            $row['currentDeposit'] = 0;
                            $row['conditionDepositAmount'] = 0;

                            //if promorule has deposit condition
                            $row['conditionDepositAmount'] = $row['conditionAmount'];

                            if($row['withdrawRequirementDepositConditionType'] == Promorules::DEPOSIT_CONDITION_TYPE_MIN_LIMIT){
                                $row['currentDeposit'] = $this->transactions->getPlayerTotalDeposits($playerId,$row['started_at'],$this->utils->getNowForMysql());
                            }
                            if($row['withdrawRequirementDepositConditionType'] == Promorules::DEPOSIT_CONDITION_TYPE_MIN_LIMIT_SINCE_REGISTRATION){
                                $row['currentDeposit'] = $this->transactions->getPlayerTotalDeposits($playerId);
                            }
                            break;
                        case self::WITHDRAW_CONDITION_TYPE_BETTING:
						default:
                            $row['unfinished'] = $row['conditionAmount'] - $row['currentBet'];
                            $row['is_finished'] = ($row['unfinished'] > 0) ? self::UN_FINISHED_WITHDRAW_CONDITIONS_FLAG : self::FINISHED_WITHDRAW_CONDITIONS_FLAG ;
                            $this->updateWithdrawalConditionFinishFlag($row['withdrawConditionId'], $row['is_finished']);
                            break;
                    }

                    $row['promoBtn'] = $this->utils->createPromoDetailButton($row['promorulesId'], $row['promoName']);

                    $row['promoName'] == null ? $row['promoName'] = '' : $row['promoName'];

                    $row['promoCode'] = empty($row['promo_code']) ? '' : $row['promo_code'];

                    $data[] = $row;
                }
                //var_dump($data);exit();
                return $data;
            }

            return false;
        }
	}

    public function getPlayerWithdrawalConditionWithoutSharingBettingAmount($playerId){
        $this->load->model(array('transactions', 'promorules'));

        $this->db->distinct()->select('promorules.promorulesId,
						   promorules.promoName,
						   promocmssetting.promoCmsSettingId,
						   promocmssetting.promo_code,
						   promocmssetting.deleted_flag AS promocms_del_flag,
						   promorules.promoType,
						   promorules.nonDepositPromoType,
						   promorules.deleted_flag AS promorules_del_flag,
						   promorules.withdrawRequirementDepositConditionType,
						   player.username,
						   player.playerId,
						   playerpromo.playerpromoId,
						   playerpromo.withdrawalStatus,
						   playerpromo.promoStatus,
						   playerpromo.note as pp_note,
						   withdraw_conditions.bonus_amount bonusAmount,
						   withdraw_conditions.id as withdrawConditionId,
						   withdraw_conditions.condition_amount as conditionAmount,
						   withdraw_conditions.started_at,
						   withdraw_conditions.source_type,
						   withdraw_conditions.source_id,
						   withdraw_conditions.wallet_type,
						   withdraw_conditions.note,
						   withdraw_conditions.trigger_amount,
						   withdraw_conditions.withdraw_condition_type,
						   promotype.promoTypeName,
						   promotype.promoTypeDesc,
						   promorules.promoCategory,
						   withdraw_conditions.deposit_amount as walletDepositAmount')
            ->from('withdraw_conditions')
            ->join('playerpromo', 'playerpromo.playerpromoId = withdraw_conditions.player_promo_id', 'left')
            ->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId OR promorules.promorulesId = withdraw_conditions.promotion_id', 'left')
            ->join('promocmssetting', 'promocmssetting.promoId = promorules.promorulesId', 'left')
            ->join('promotype', 'promotype.promotypeId = promorules.promoCategory', 'left')
            ->join('player', 'player.playerId = withdraw_conditions.player_id', 'left');

        $this->db->where('withdraw_conditions.player_id', $playerId);
        $this->db->where('withdraw_conditions.status', self::STATUS_NORMAL);
        $this->db->where('promocmssetting.deleted_flag IS NULL', null, false);


        $this->db->order_by("withdraw_conditions.started_at", "ASC");
        $query = $this->db->get();

		$this->utils->printLastSQL();

        if ($query->num_rows() > 0) {
            $this->load->model(['group_level', 'game_logs']);
            $data = [];
            $rows = $query->result_array();

            $gameDescIdArrFromLevel=$this->group_level->getAllowedGameIdKV($playerId);
            $game_logs = $this->game_logs->getPlayerTotalGameLogsByDate($rows['0']['playerId'], $rows['0']['started_at']);

            $totalRequiredBet = 0;
            $totalPlayerBet = 0;
            $promo_game_list = [];
            $wd_ident_ar = [];

            $realTotalBet=0;
            foreach($game_logs as $key => $log_row){
                $realTotalBet+=$log_row['betting_amount'];
            }
            $this->utils->debug_log('first $realTotalBet', $realTotalBet, count($game_logs));

            foreach ($rows as $row) {

                // git issue #1371, hide probably one-to-many promorules-promocmsSettings matches
                $wd_ident = "{$row['withdrawConditionId']}-{$row['promorulesId']}-{$row['playerpromoId']}-{$row['source_id']}";
                if (array_key_exists($wd_ident, $wd_ident_ar)) {
                    continue;
                }
                else {
                    $wd_ident_ar[$wd_ident] = 1;
                }

                $gameDescIdArr=$gameDescIdArrFromLevel;

                if($row['promoType'] == Promorules::PROMO_TYPE_NON_DEPOSIT || $row['promoType'] == Promorules::PROMO_TYPE_DEPOSIT){
                    if(isset($row['promorulesId'])){

                        if(isset($promo_game_list[$row['promorulesId']])){
                            $gameDescIdArr = $promo_game_list[$row['promorulesId']];
                        }else{
                            $gameDescIdArr = $this->promorules->getPlayerGamesKV($row['promorulesId']);
                            $promo_game_list[$row['promorulesId']] = $gameDescIdArr;
                            $this->utils->debug_log('gameDescIdArr from promo rules:'.$row['promorulesId']);
                        }
                    }
                }

                $row['promoBtn'] = $this->utils->createPromoDetailButton($row['promorulesId'], $row['promoName']);
                $row['promoName'] == null ? $row['promoName'] = '' : $row['promoName'];
                $row['promoCode'] = empty($row['promo_code']) ? '' : $row['promo_code'];

                switch ($row['withdraw_condition_type']){
                    case self::WITHDRAW_CONDITION_TYPE_DEPOSIT:
                        $row['currentDeposit'] = 0;
                        $row['conditionDepositAmount'] = 0;

                        //if promorule has deposit condition
                        $row['conditionDepositAmount'] = $row['conditionAmount'];

                        if($row['withdrawRequirementDepositConditionType'] == Promorules::DEPOSIT_CONDITION_TYPE_MIN_LIMIT){
                            $row['currentDeposit'] = $this->transactions->getPlayerTotalDeposits($playerId,$row['started_at'],$this->utils->getNowForMysql());
                        }
                        if($row['withdrawRequirementDepositConditionType'] == Promorules::DEPOSIT_CONDITION_TYPE_MIN_LIMIT_SINCE_REGISTRATION){
                            $row['currentDeposit'] = $this->transactions->getPlayerTotalDeposits($playerId);
                        }
                        break;
                    case self::WITHDRAW_CONDITION_TYPE_BETTING:
                    default:
						$this->totalPlayerCurrentBetWithoutSharingBettingAmount($row, $game_logs, $gameDescIdArr, $totalRequiredBet, $totalPlayerBet);

						$this->utils->debug_log("Will patch for OGP-14387, currentBet:", $row['currentBet']);
						/// Patch for OGP-14387 player can't use APP withdraw
						// $row['currentBet'] is double, result will be scientific notation.
						// Avoid $row['unfinished'] turned into scientific notation
						// BTW $row['currentBet'] generated from $game_logs and game need float format.
						$f = "%f";
						$row['unfinished'] = $row['conditionAmount'] - sprintf($f, $row['currentBet']);
						$this->utils->debug_log("After patch for OGP-14387, unfinished:", $row['unfinished']);

                        if($row['unfinished'] > 0){
                            $unfinished_record = "withdraw_condition_id:[". $row['withdrawConditionId'] ."], unfinished:[" . $row['unfinished'] . "], conditionAmount:[" . $row['conditionAmount'] . "], currentBet:[" . $row['currentBet'] . "]";
                            $this->utils->debug_log("--------------------> getPlayerWithdrawalConditionWithoutSharingBettingAmount", $unfinished_record);
                        }

                        $row['is_finished'] = ($row['unfinished'] > 0) ? self::UN_FINISHED_WITHDRAW_CONDITIONS_FLAG : self::FINISHED_WITHDRAW_CONDITIONS_FLAG ;
                        $this->updateWithdrawalConditionFinishFlag($row['withdrawConditionId'], $row['is_finished']);
                        break;
                }

                $data[] = $row;
            }
            $data['totalRequiredBet'] = $totalRequiredBet;
            $data['totalPlayerBet'] = $totalPlayerBet;
            return $data;
        }

        return false;
    }

    public function totalPlayerCurrentBetWithoutSharingBettingAmount(&$withdraw_condition, &$game_logs, $gameDescIdArr, &$totalRequiredBet, &$totalPlayerBet){
		$withdraw_condition['currentBet'] = 0;
        $totalRequiredBet += $withdraw_condition['conditionAmount'];

        if(empty($game_logs)){
            return;
        }

        $withdraw_condition['requireBetAmount'] = 0;
        $withdraw_condition['requireBetAmount'] = $withdraw_condition['conditionAmount'];
        foreach($game_logs as $key => &$log_row){

            if(!isset($gameDescIdArr[$log_row['game_description_id']])){
                continue;
            }

            if($withdraw_condition['requireBetAmount'] <= 0){
                break;
            }

            if($log_row['used']){
                continue;
            }

            if(strtotime($log_row['date_minute']) - strtotime($withdraw_condition['started_at']) < 0){
                continue;
			}

			/// About OGP-14387,
			// $withdraw_condition['requireBetAmount'] type will from string convert to double.
            $withdraw_condition['requireBetAmount'] -= $log_row['betting_amount'];

            if($withdraw_condition['requireBetAmount'] >= 0){
                $log_row['betting_amount'] = 0;
                $log_row['used'] = true;
                unset($game_logs[$key]);
                continue;
            }

            $log_row['betting_amount'] = abs($withdraw_condition['requireBetAmount']);
        }

        if($withdraw_condition['requireBetAmount'] <= 0){
			$withdraw_condition['currentBet'] = $withdraw_condition['conditionAmount'];
        }else{
			$withdraw_condition['currentBet'] = $withdraw_condition['conditionAmount'] - $withdraw_condition['requireBetAmount'];
        }

        $this->utils->debug_log('withdraw condition', $withdraw_condition['currentBet'], $withdraw_condition['requireBetAmount'], $withdraw_condition['conditionAmount']);
        unset($withdraw_condition['requireBetAmount']);
        $totalPlayerBet += $withdraw_condition['currentBet'];

        $this->utils->debug_log('current $totalPlayerBet', $totalPlayerBet, $totalRequiredBet);
    }

    public function updateWithdrawalConditionFinishFlag($id, $is_finished = self::UN_FINISHED_WITHDRAW_CONDITIONS_FLAG) {
        $this->db->set('is_finished', $is_finished)->where('id', $id);
        $this->runAnyUpdate($this->tableName);
        $this->utils->debug_log('update withdraw condition finished flag ['. $is_finished .']',$id);
    }

    /**
     * overview : get player deposit condition in withdrawal conditions
     *
     * @param int	$playerId
     * @return array|bool
     */
    public function getPlayerDepositConditionInWithdrawalCondition($playerId) {
        $this->load->model(array('transactions'));
        $this->db->distinct()->select('promorules.withdrawRequirementDepositConditionType,
                       withdraw_conditions.condition_amount as conditionAmount,
                       withdraw_conditions.id as withdrawConditionId,
                       withdraw_conditions.started_at')
            ->from('withdraw_conditions')
            ->join('promorules', 'promorules.promorulesId = withdraw_conditions.promotion_id', 'left')
            ->where('withdraw_conditions.player_id', $playerId)
            ->where('withdraw_conditions.status', self::STATUS_NORMAL)
            ->where('withdraw_conditions.withdraw_condition_type', self::WITHDRAW_CONDITION_TYPE_DEPOSIT)
            ->order_by("withdraw_conditions.started_at", "asc");

        $query = $this->db->get();

        $this->utils->printLastSQL();

        if ($query->num_rows() > 0) {
            $data = array();
            foreach ($query->result_array() as $row) {
                $row['conditionDepositAmount'] = $row['conditionAmount'];
                switch ($row['withdrawRequirementDepositConditionType']){
                    case Promorules::DEPOSIT_CONDITION_TYPE_MIN_LIMIT:
                        $row['currentDeposit'] = $this->transactions->getPlayerTotalDeposits($playerId,$row['started_at'],$this->utils->getNowForMysql());
                        break;
                    case Promorules::DEPOSIT_CONDITION_TYPE_MIN_LIMIT_SINCE_REGISTRATION:
                        $row['currentDeposit'] = $this->transactions->getPlayerTotalDeposits($playerId);
                        break;
                    case Promorules::DEPOSIT_CONDITION_TYPE_NOTHING:
                    default :
                        break;
                }
                $data[] = $row;
            }

            return $data;
        }

        return false;
    }

	/**
	 * Computes for total required bet, current total bet, and unfinished bet amount pf player
	 * based on his withdrawal conditions.
	 *
	 * @param  string $playerId Player ID
	 * @return Array           Computed / summarized withdrawal conditions
	 * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
	 */
	public function computePlayerWithdrawalConditions($playerId)
	{
		$withdraw_conditions = $this->getPlayerWithdrawalCondition($playerId);

		$data = array(
			'totalRequiredBet' => 0,
			'totalPlayerBet' => 0,
			'unfinished' => 0,
		);

		if(!$withdraw_conditions) return $data;

		$tmp_required_bet = 0;
		$tmp_current_bet = 0;
		$tmp_unfinished_bet = 0;

		foreach($withdraw_conditions as $withdraw_condition){
            if($withdraw_condition['withdraw_condition_type'] != self::WITHDRAW_CONDITION_TYPE_BETTING){
                continue;
            }

		    // -- Compute for current total bet
			if($withdraw_condition['currentBet'] > $tmp_current_bet)
				$tmp_current_bet =  $withdraw_condition['currentBet'];

			// -- Compute for total required bet amount
			$tmp_required_bet +=  $withdraw_condition['conditionAmount'];
		}

		// -- Compute for total unfinished bet
		$tmp_unfinished_bet = $tmp_required_bet - $tmp_current_bet;


		$data['totalRequiredBet'] = $tmp_required_bet;
		$data['totalPlayerBet'] = $tmp_current_bet;
		$data['unfinished'] = $tmp_unfinished_bet;

		return $data;
	}

	/**
	 * overview : get clear withdraw condition settings
	 *
	 * @return array
	 */
	public function getClearWithdrawConditionSettings() {
		$this->load->model(['operatorglobalsettings']);
		$previous_balance_set_amount = $this->operatorglobalsettings->getSettingValue('previous_balance_set_amount');
		$clear_withdraw_cond_by_subwallet = $this->operatorglobalsettings->getSettingJson('clear_withdraw_cond_by_subwallet');
		if (empty($clear_withdraw_cond_by_subwallet)) {
			$clear_withdraw_cond_by_subwallet = [];
		}
		$apis = $this->utils->getActiveGameSystemList();
		foreach ($apis as $api) {
			$value = isset($clear_withdraw_cond_by_subwallet[$api['id']]['value']) ? $clear_withdraw_cond_by_subwallet[$api['id']]['value'] : $previous_balance_set_amount;
			$clear_withdraw_cond_by_subwallet[$api['id']] = [
				"id" => $api['id'],
				"label" => $api['system_code'],
				"value" => $value,
			];
		}
		return [
			'total' => $previous_balance_set_amount,
			'subwallets' => $clear_withdraw_cond_by_subwallet,
		];
	}

	/**
	 * overview : auto check withdraw condition
	 *
	 * @param int	$playerId
	 * @param string $message
	 * @return bool true=all finished
	 */
	public function autoCheckWithdrawConditionAndMoveBigWallet($playerId, &$message = null,
		$targetWalletType = null, $unfinished_is_false = false, $clean_condition = false, $secureId = null) {
		//first check subwallets
		$this->load->model(array('operatorglobalsettings', 'wallet_model', 'player_promo', 'game_logs'));
		// $balLimit = $this->operatorglobalsettings->getSettingValue('previous_balance_set_amount');
		$settings = $this->getClearWithdrawConditionSettings();
		// $balLimit = $settings['total'];
		$message = '';
		$promotion_rules = $this->utils->getConfig('promotion_rules');
		$success = true;
		$changed = false;
		$model = $this;

		$bigWallet = $this->wallet_model->getBigWalletByPlayerId($playerId);
		$subwallets = $settings['subwallets'];

		foreach ($subwallets as $sub) {

			$detailStatus = self::DETAIL_STATUS_ACTIVE;
			$clear = false;

			$walletType = $sub['id']; // sub wallet

			if ($targetWalletType !== null && $targetWalletType != $sub['id']) {
				//ignore other sub wallet
				$this->utils->debug_log('ignore other sub wallet', $sub['id'], 'because', $targetWalletType);
				continue;
			}

			if (!$this->existsAvailWithdrawalConditionByWalletType($playerId, $walletType)) {
				$message .= 'does not exist withdraw condition on ' . $sub['label'] . "\n";
				continue;
			}

			$this->utils->debug_log('process wallet: ' . $targetWalletType . ' , playerId: ' . $playerId);

			$conditionAmount = 0;
			$firstDateTime = null;
			$balLimit = doubleval($sub['value']);
			$bal = $bigWallet['sub'][$walletType]['total'];
			if ($bal <= $balLimit) {
				//FIXME
				//inactive all
				$clear = true;
				$message .= 'finish withdraw condition on ' . $sub['label'] . ', because last balance (' . $bal . ') of player ' . $playerId . ' <= ' . $balLimit . "\n";
				$detailStatus = self::DETAIL_STATUS_CANCELLED_DUE_TO_SMALL_BALANCE;
			} else {
				//check bet amount >= withdraw condition
				//get first date
				$firstDateTime = $this->getFirstAvailableWithdrawConditionByWalletType($playerId, $walletType);

				if (!empty($firstDateTime)) {
					//add promorulesId
					$betAmount = $this->game_logs->getPlayerCurrentBetByGamePlatformId($playerId, $walletType, $firstDateTime);
					$conditionAmount = $this->getAvailableAmountOnWithdrawConditionByWalletType($playerId, $walletType);
					$this->utils->debug_log('check withdraw condition', 'playerId', $playerId, 'walletType', $walletType,
						'firstDateTime', $firstDateTime, 'betAmount', $betAmount, 'conditionAmount', $conditionAmount);
					$clear = $betAmount >= $conditionAmount;
					if ($clear) {
						$message .= 'finish withdraw condition on ' . $sub['label'] . ', because total bet amount (' . $betAmount . ' from ' . $firstDateTime . ') of player ' . $playerId . ' >= ' . $conditionAmount . "\n";
						$detailStatus = self::DETAIL_STATUS_FINISHED_BETTING_AMOUNT_WHEN_DEPOSIT;
					}
				} else {
					$this->utils->debug_log('donot find first date time of withdraw', $playerId, 'wallet', $walletType);
				}
			}

			if ($clear) {
				if ($this->utils->isEnabledFeature('enabled_auto_clear_withdraw_condition') && $clean_condition) {
                    $reason = 'auto finished by deposit';
				    if(isset($secureId) && !empty($secureId)){
				        $reason .= ',Order ID : ' . $secureId;
                    }

					$success = $success && $this->disablePlayerWithdrawalConditionByWalletType($playerId, $walletType, $reason, $detailStatus);
					if (!$success) {
						return $success;
					}
					$this->utils->debug_log('disablePlayerWithdrawalConditionByWalletType', $success, 'playerId', $playerId, 'walletType', $walletType);

					//FIXME change status of player promo

				}
				$success = $this->wallet_model->moveSubWalletToReal($bigWallet, $walletType);
				if (!$success) {
					$message .= "move subwallet failed\n";
					return $success;
				}

				// if (isset($promotion_rules['enabled_auto_finish_playerpromo'])) {
				// if (@$promotion_rules['enabled_auto_finish_playerpromo']) {
				//load playerPromo and promorule from withdraw condition

				//FIXME change status of player promo
				// $this->lockAndTransForPlayerPromo($playerPromo['playerpromoId'], function () use ($model, $playerPromo, $promorule) {
				// 	$success = true;

				// 	return $success;
				// });

				// }
				// }

				$changed = true;
				// $rlt = $this->player_promo->disablePlayerPromo($playerId);
				// $this->utils->debug_log('disablePlayerPromo', $rlt);

			} else {
				$message .= 'still keep withdraw condition on ' . $sub['label'] . ', from ' . $firstDateTime . ', amount: ' . $conditionAmount . "\n";
				$this->utils->debug_log('unfinished_is_false', $unfinished_is_false, 'targetWalletType', $targetWalletType);
				if ($unfinished_is_false && $targetWalletType == $sub['id']) {
					$this->utils->debug_log('unfinished is false on', $targetWalletType);
					$success = false;
					break;
				}
			}
		} // EOF foreach ($subwallets as $sub)
		if (!$success) {
			return $success;
		}

		//main
		$balLimit = $settings['total'];
		$walletType = 0; // main wallet

		$bal = $this->wallet_model->getTotalBalance($playerId);
		$this->utils->debug_log('playerId', $playerId, 'balLimit', $balLimit, 'bal', $bal);
		$clear = false;
		$description = null;
		$conditionAmount = 0;
		$firstDateTime = null;
		$detailStatus = self::DETAIL_STATUS_ACTIVE;
		if ($bal <= $balLimit) {
			//inactive all
			$clear = true;
			$message .= 'finish withdraw condition, because last balance (' . $bal . ') of player ' . $playerId . ' <= ' . $balLimit . "\n";
			$detailStatus =  self::DETAIL_STATUS_CANCELLED_DUE_TO_SMALL_BALANCE;
		} else {
			//check bet amount >= withdraw condition
			//get first date
			$firstDateTime = $this->getFirstAvailableWithdrawCondition($playerId);
			if (!empty($firstDateTime)) {

				$betAmount = $this->game_logs->getPlayerCurrentBet($playerId, $firstDateTime);
				$conditionAmount = $this->getAvailableAmountOnWithdrawCondition($playerId);
				$this->utils->debug_log('check withdraw condition', 'playerId', $playerId, 'main wallet',
					'firstDateTime', $firstDateTime, 'betAmount', $betAmount, 'conditionAmount', $conditionAmount);
				$clear = $betAmount >= $conditionAmount;
				if ($clear) {
					$message .= 'clean withdraw condition, because total bet amount (' . $betAmount . ' from ' . $firstDateTime . ') of player ' . $playerId . ' >= ' . $conditionAmount . "\n";
					$detailStatus = self::DETAIL_STATUS_FINISHED_BETTING_AMOUNT_WHEN_DEPOSIT;
				}
			} else {
				$this->utils->debug_log('donot find first date time of withdraw', $playerId, 'main wallet');
			}
		}
		if ($clear) {
			if ($this->utils->isEnabledFeature('enabled_auto_clear_withdraw_condition') && $clean_condition) {
                $reason = 'auto finished by deposit';
                if(isset($secureId) && !empty($secureId)){
                    $reason .= ',Order ID : ' . $secureId;
                }

				$success = $success && $this->disablePlayerWithdrawalCondition($playerId, $reason, $detailStatus);
				$this->utils->debug_log('disablePlayerWithdrawalCondition', $success, 'playerId', $playerId);
				if (!$success) {
					return $success;
				}
				$this->utils->debug_log('disablePlayerWithdrawalCondition for main wallet', $success, 'playerId', $playerId);

				//change status of player promo
			}

			$success = $this->wallet_model->moveMainWalletToReal($bigWallet);
			if (!$success) {
				$message .= "move main wallet failed\n";
				return $success;
			}
			$changed = true;

		} elseif ($conditionAmount > 0) {
			$message .= 'still keep withdraw condition on main wallet, from ' . $firstDateTime . ', amount: ' . $conditionAmount . "\n";
			//ignore main wallet
			// if($unfinished_is_false && $targetWalletType==0){
			// 	$this->utils->debug_log('unfinished is false on', $targetWalletType);
			// 	$success=false;
			// }
		}
		// $this->utils->recordAction(self::MANAGEMENT_WITHDRAW_CONDITION, self::ACTION_CLEAN_WITHDRAW_CONDITION, $message);

		if ($changed && $success) {

			$this->wallet_model->totalBigWallet($bigWallet);

			$success = $this->wallet_model->updateBigWalletByPlayerId($playerId, $bigWallet);
			if (!$success) {
				$message .= 'update big wallet failed' . "\n";
			}
		}

		return $success;

		// $playerWithdrawalCondition = $this->ci->payment->getPlayerWithdrawalCondition($playerId);
		// //var_dump($playerWithdrawalCondition);exit();
		// if (!empty($playerWithdrawalCondition)) {
		// 	foreach ($playerWithdrawalCondition as $key) {
		// 		$data['status'] = 2;
		// 		$this->ci->payment->inactiveWithdrawCondition($data, $key['withdrawConditionId']);
		// 	}
		// }

	} // EOF autoCheckWithdrawConditionAndMoveBigWallet

	/**
	 * overview : get first available withdraw condition
	 *
	 * @param int	$playerId
	 * @param int	$walletType
	 * @return string
	 */
	public function getFirstAvailableWithdrawConditionByWalletType($playerId, $walletType) {
		$this->db->select('min(started_at) as firstDateTime', false)
			->from('withdraw_conditions')->where('player_id', $playerId)
			->where('wallet_type', $walletType)
			->where('status', self::STATUS_NORMAL);

		return $this->runOneRowOneField('firstDateTime');
	}

	/**
	 * overview : get available amount on withdraw condition
	 *
	 * @param $playerId
	 * @param $walletType
	 * @return null
	 */
	public function getAvailableAmountOnWithdrawConditionByWalletType($playerId, $walletType) {
		$this->db->select('sum(condition_amount) as amount', false)
			->from('withdraw_conditions')->where('player_id', $playerId)
			->where('wallet_type', $walletType)
			->where('status', self::STATUS_NORMAL);

		return $this->runOneRowOneField('amount');
	}

	/**
	 * overview : disable player withdrawal condition
	 *
	 * @param $playerId
	 * @param $walletType
	 * @return bool
	 */
	function disablePlayerWithdrawalConditionByWalletType($playerId, $walletType, $reason=null, $detailStatus=self::DETAIL_STATUS_ACTIVE) {

		$data['status'] = parent::STATUS_DISABLED;
		$data['updated_at'] = $this->utils->getNowForMysql();
		$data['stopped_at'] = $this->utils->getNowForMysql();
		$data['admin_user_id'] = $this->authentication->getUserId();
		$data['detail_status'] = $detailStatus;
        $data['note'] = $reason;

		$this->db->where('player_id', $playerId)->where('wallet_type', $walletType);

		# only clear active, to avoid reupdate all. should not affect cancelled/finished wc
		$this->db->where('status', self::STATUS_NORMAL);

		$this->db->set($data);
		// $this->db->update($this->tableName, $data);
		$success =  $this->runAnyUpdate('withdraw_conditions');

		if ($success) {
			$this->load->model(['player_promo']);

			$this->db->select('player_id, player_promo_id')->from('withdraw_conditions')
					->where_in('player_id', $playerId)
					->where('player_promo_id is not null', null, false);

			$rows = $this->runMultipleRowArray();

			if (!empty($rows)) {
				$playerPromoArr = [];
				foreach ($rows as $row) {
					$playerPromoArr[] = $row['player_promo_id'];
				}
				if (!empty($playerPromoArr)) {
					$this->player_promo->finishPlayerPromos($playerPromoArr, $reason);
				}
			}
		}

		return $success;
	}

	/**
	 * overview : check if available withdrawal condition exist
	 *
	 * @param $playerId
	 * @param $walletType
	 * @return bool
	 */
	public function existsAvailWithdrawalConditionByWalletType($playerId, $walletType) {
		$this->db->select('id')
			->from('withdraw_conditions')->where('player_id', $playerId)
			->where('wallet_type', $walletType)
			->where('status', self::STATUS_NORMAL);

		return $this->runExistsResult();

	}

	/**
	 * overvie : create withdrawal condition for manual
	 *
	 * @param int		$player_id
	 * @param int		$bonusTransId
	 * @param double	$withdrawBetAmtCondition
	 * @param double	$deposit_amount
	 * @param double	$bonus_amount
	 * @param int		$bet_times
	 * @param int		$promorule
	 * @return array
	 */
	function createWithdrawConditionForManual($player_id, $bonusTransId,
		$withdrawBetAmtCondition, $deposit_amount, $bonus_amount, $bet_times, $promorule, $reason=null, $player_promo_id = null) {

		$this->load->model(['promorules']);
		$promorulesId = null;
		$isDeposit = false;
        if(is_array($promorule)){
        	$promorulesId = $promorule['promorulesId'];
	    	$isDeposit = $promorule['promoType'] == Promorules::PROMO_TYPE_DEPOSIT;
        }


		$data = array(
			'source_id' => $bonusTransId,
			'source_type' => $isDeposit ? self::SOURCE_DEPOSIT : self::SOURCE_BONUS,//self::SOURCE_NON_DEPOSIT,
			'started_at' => $this->utils->getNowForMysql(),
			'condition_amount' => $withdrawBetAmtCondition,
			'status' => self::STATUS_NORMAL,
			'player_id' => $player_id,
			'deposit_amount' => $deposit_amount,
			'bonus_amount' => $bonus_amount,
			'bet_times' => $bet_times,
			'promotion_id' => $promorulesId,
			'promorules_json' => @$promorule['json_info'],
			'wallet_type' => @$promorule['releaseToSubWallet'],
			'trigger_amount' => $bonus_amount ,
			'note' => $reason
		);

		if (!empty($player_promo_id)) {
			$data['player_promo_id'] = $player_promo_id;
		}

		return $this->insertRow($data);
	}

	/**
	 * overview : get withdrawl transaction detail
	 *
	 * @param $walletAccountId
	 * @return array|bool
	 */
	public function getWithdrawalTransactionDetail($walletAccountId) {
		$this->db->select('walletaccount.*,
						   player.email,
						   player.createdOn,
						   player.username AS playerName,
						   playerdetails.*,
						   playeraccount.totalBalanceAmount AS currentBalAmount,
						   playeraccount.currency AS currentBalCurrency,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName,
						   banktype.bankTypeId,
						   banktype.bankName,
						   banktype.bank_code AS bankCode,
						   playerbankdetails.bankAccountFullName,
						   playerbankdetails.bankAccountNumber,
						   playerbankdetails.city,
						   playerbankdetails.province,
						   playerbankdetails.branch,
						   playerbankdetails.phone as bankPhone,
						   playerbankdetails.bankAddress,
						   adminusers.username as processedByAdmin,
						   playerbankdetails.customBankName as customBankName,
						   playerbankdetails.playerBankDetailsId as playerBankDetailsId,
						   ')
			->from('walletaccount')
			->join('playeraccount', 'playeraccount.playerAccountId = walletaccount.playerAccountId')
			->join('player', 'player.playerId = playeraccount.playerId')
			->join('playerdetails', 'playerdetails.playerId = player.playerId', 'left')
			->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left')
			->join('localbankwithdrawaldetails', 'localbankwithdrawaldetails.walletAccountId = walletaccount.walletAccountId', 'left')
			->join('playerbankdetails', 'playerbankdetails.playerBankDetailsId = localbankwithdrawaldetails.playerBankDetailsId', 'left')
			->join('adminusers', 'adminusers.userId = walletaccount.processedBy', 'left')
			->join('banktype', 'banktype.bankTypeId = playerbankdetails.bankTypeId', 'left');

		$this->db->where('walletaccount.walletAccountId', $walletAccountId);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$data = null;
			foreach ($query->result_array() as $row) {
				//format number
				$row['totalBalance'] = $row['currentBalAmount'];
				$row['currentBalAmount'] = $this->utils->formatCurrency($row['currentBalAmount']);
				$row['bankName'] = lang($row['bankName']);
				$row['firstName'] = $row['firstName'] ? ucwords($row['firstName']) : '';
				$row['lastName'] = $row['lastName'] ? ucwords($row['lastName']) : '';
				$row['dwDateTime'] = $row['dwDateTime'];
				$row['createdOn'] = $row['createdOn'];
				$row['processDatetime'] = $row['processDatetime'];
				$row['playerName'] = ucwords($row['playerName']);
				$row['subwalletBalanceAmount'] = $this->getSubWalletBalance($row['playerId']);
				if (!empty($row['subwalletBalanceAmount'])) {
					foreach ($row['subwalletBalanceAmount'] as &$subwallet) {
						$row['totalBalance'] += $subwallet['totalBalanceAmount'];
						$subwallet['totalBalanceAmount'] = $this->utils->formatCurrency($subwallet['totalBalanceAmount']);
					}
				}
				$row['totalBalance'] = $this->utils->formatCurrency($row['totalBalance']);
				$row['cashbackwalletBalanceAmount'] = 0; //$this->getPlayerCashbackWalletBalance($row['playerId']);
				// $row['withdrawCondition'] = $this->getPlayerWithdrawalCondition($row['playerId']);
				$row['customBankName'] = $row['customBankName'];
				$row['playerBankDetailsId'] = $row['playerBankDetailsId'];
				$row['currentBalCurrency'] = $this->utils->getCurrentCurrency()['currency_code'];

				$row['isCrypto'] = 0;
                $banktype = $this->banktype->getBankTypeById($row['bankTypeId']);
                if($this->utils->isCryptoCurrency($banktype)){
					$row['isCrypto'] = 1;
				}

				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return null;
	}

	/**
	 * overview : overview :get sub wallet balance
	 *
	 * @param int	$player_id
	 * @return bool|array
	 */
	public function getSubWalletBalance($player_id) {
		$qry = "SELECT totalBalanceAmount AS totalBalanceAmount,typeId FROM playeraccount WHERE TYPE IN ('subwallet') AND playerId = '" . $player_id . "'";
		$query = $this->db->query("$qry");

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * overview : get player cash back wallet balance
	 *
	 * @param  int	$playerId
	 * @return array
	 */
	public function getPlayerCashbackWalletBalance($playerId) {
		$this->db->select('playeraccount.totalBalanceAmount	AS cashbackwalletBalanceAmount
						   ')
			->from('playeraccount')
			->join('player', 'player.playerId = playeraccount.playerId', 'left');

		$this->db->where('playeraccount.type', 'cashbackwallet');
		$this->db->where('playeraccount.typeOfPlayer', 'real');
		$this->db->where('player.playerId', $playerId);

		$query = $this->db->get();
		return $query->result_array();
	}

	// public function getPlayerWithdrawalCondition($playerId) {
	// 	$this->db->distinct()->select('promorules.promorulesId,
	// 					   promorules.promoName,
	// 					   promorules.promoCode,
	// 					   promorules.promoType,
	// 					   promorules.nonDepositPromoType,
	// 					   promorules.depositConditionDepositAmount,
	// 					   promorules.depositConditionType,
	// 					   promorules.nonfixedDepositMinAmount,
	// 					   promorules.nonfixedDepositMaxAmount,
	// 					   promorules.depositConditionNonFixedDepositAmount,
	// 					   promorules.nonfixedDepositAmtCondition,
	// 					   promorules.nonfixedDepositAmtConditionRequiredDepositAmount,
	// 					   promorules.bonusReleaseRule,
	// 					   promorules.bonusAmount as promorulesBonusAmount,
	// 					   promorules.depositPercentage,
	// 					   promorules.maxBonusAmount,
	// 					   promorules.withdrawRequirementRule,
	// 					   promorules.withdrawRequirementBetAmount,
	// 					   promorules.withdrawRequirementBetCntCondition,
	// 					   promorules.withdrawRequirementConditionType,
	// 					   promorules.promoType,
	// 					   player.username,
	// 					   player.playerId,
	// 					   playerpromo.playerpromoId,
	// 					   playerpromo.bonusAmount,
	// 					   playerpromo.withdrawalStatus,
	// 					   playerpromo.promoStatus,
	// 					   withdraw_conditions.id as withdrawConditionId,
	// 					   withdraw_conditions.condition_amount as conditionAmount,
	// 					   withdraw_conditions.started_at,
	// 					   withdraw_conditions.source_type,
	// 					   promotype.promoTypeName,
	// 					   promotype.promoTypeDesc,
	// 					   transactions.promo_category,
	// 					   transactions.amount as walletDepositAmount')
	// 		->from('withdraw_conditions')
	// 		->join('promorules', 'promorules.promorulesId = withdraw_conditions.promotion_id', 'left')
	// 		->join('sale_orders', 'sale_orders.wallet_id = withdraw_conditions.source_id', 'left')
	// 		->join('transactions', 'transactions.id = withdraw_conditions.source_id', 'left')
	// 		->join('promotype', 'promotype.promotypeId = transactions.promo_category', 'left')
	// 		->join('playerpromo', 'playerpromo.playerpromoId = sale_orders.player_promo_id', 'left')
	// 		->join('player', 'player.playerId = withdraw_conditions.player_id', 'left');
	// 	$this->db->where('withdraw_conditions.player_id', $playerId);
	// 	$this->db->where('withdraw_conditions.status', 1); //1-active
	// 	$this->db->order_by("withdraw_conditions.started_at", "desc");

	// 	$query = $this->db->get();

	// 	// $this->utils->printLastSQL();

	// 	if ($query->num_rows() > 0) {
	// 		foreach ($query->result_array() as $row) {
	// 			//check if deposit promo and non deposit promo (email,registration,mobile)
	// 			if (($row['promoType'] == 1 && $row['nonDepositPromoType'] < 4) || ($row['promoType'] == 0)) {
	// 				$row['currentBet'] = $this->game_logs->getPlayerCurrentBet($row['playerId'], $row['started_at'], $this->utils->getNowForMysql(), $row['promorulesId']);
	// 			} else {
	// 				$row['currentBet'] = $this->game_logs->getPlayerCurrentBet($row['playerId'], $row['started_at'], $this->utils->getNowForMysql(), null);
	// 			}

	// 			$row['unfinished'] = $row['conditionAmount'] - $row['currentBet'][0]['totalBetAmount'];

	// 			// $row['started_at'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['started_at']));
	// 			$row['promoName'] == null ? $row['promoName'] = '' : $row['promoName'];
	// 			$row['promoCode'] == null ? $row['promoCode'] = '' : $row['promoCode'];
	// 			$data[] = $row;
	// 		}
	// 		//var_dump($data);exit();
	// 		return $data;
	// 	}
	// 	return false;
	// }

	public function isAllFinishedPlayerPromotion($playerId) {

		if ($this->utils->isEnabledFeature('check_disable_cashback_by_promotion')) {

			$this->db->select('withdraw_conditions.id')->from('withdraw_conditions')
				->join('promorules', 'withdraw_conditions.promotion_id=promorules.promorulesId')
                ->where('withdraw_conditions.withdraw_condition_type', self::WITHDRAW_CONDITION_TYPE_BETTING)
				->where('withdraw_conditions.status', self::STATUS_NORMAL)
				->where('promorules.disable_cashback_if_not_finish_withdraw_condition', '1')
				->where('withdraw_conditions.playerId', $playerId);

			$rows = $this->runMultipleRowArray();

			return empty($rows);
		}

		return true;
	}

	/**
	 * detail: get the cancelled withdrawa condition of a certain player
	 *
	 * @param int $player_id
	 * @return array
	 */
	public function getPlayerCancelledWithdrawalCondition($playerId = '', $where, $values) {

		$DepositType = lang('Deposit');
		$NonDepositType = lang('Non Deposit');
		$randomBonus = lang('Random Bonus');

		$select = '(CASE WHEN withdraw_conditions.source_type = ' . Withdraw_condition::SOURCE_DEPOSIT . ' THEN "' . $DepositType . '"
					WHEN withdraw_conditions.source_type = ' . Withdraw_condition::SOURCE_BONUS . ' THEN "' . $randomBonus . '"
					WHEN withdraw_conditions.source_type = ' . Withdraw_condition::SOURCE_NON_DEPOSIT . ' THEN "' . $NonDepositType . '" ELSE promocmssetting.promo_code END) as transaction_type,
					promocmssetting.promoName, promocmssetting.promo_code, withdraw_conditions.deposit_amount,
					withdraw_conditions.bonus_amount, withdraw_conditions.started_at,withdraw_conditions.condition_amount, withdraw_conditions.id as wc_id,
					withdraw_conditions.bet_amount, withdraw_conditions.note AS notes, withdraw_conditions.updated_at,withdraw_conditions.stopped_at,
					withdraw_conditions.detail_status,adminusers.username';

		$qobj = $this->db->select($select)
			->from('withdraw_conditions')
			// ->join('transaction_notes', 'transaction_notes.transaction_id = withdraw_conditions.id', 'LEFT')
			//->join('(SELECT note, transaction_id FROM transaction_notes WHERE id in (SELECT MAX(id) FROM transaction_notes GROUP BY transaction_id)) transaction_notes', 'transaction_notes.transaction_id = withdraw_conditions.id', 'left')		// Get the latest note made by the user
            ->join('playerpromo', 'playerpromo.playerpromoId = withdraw_conditions.player_promo_id', 'LEFT')
            ->join('promocmssetting', 'promocmssetting.promoCmsSettingId = playerpromo.promoCmsSettingId', 'LEFT')
			->join('adminusers', 'adminusers.userId = withdraw_conditions.admin_user_id', 'LEFT')
			->where('withdraw_conditions.player_id', $playerId)
			->where($where['0'], $values['0'])
			->where($where['1'], $values['1'])
			->where('withdraw_conditions.status', self::STATUS_DISABLED)
		// ->where('transaction_notes.transaction', 'cancel withdraw condition')
			->order_by('withdraw_conditions.started_at', 'DESC')
			->get();

			if($this->config->item('debug_data_table_sql')){
				$sql = $this->db->last_query();
				$this->utils->debug_log('in getPlayerCancelledWithdrawalCondition().$sql:', $sql);
			}

		return $qobj->result_array();
	} // EOF getPlayerCancelledWithdrawalCondition

	public function getAvailableWithdrawConditionOnlyPromoWithBet($date, $startHour, $endHour, $playerId=null){

		$lastDate = $this->utils->getLastDay($date);

		if (intval($endHour) == 23) {
			//all yesterday
			$date = $lastDate;
		}

		$from = $lastDate . ' ' . $startHour . ':00:00';
		$to = $date . ' ' . $endHour . ':59:59';

		return $this->getAvailableWithdrawConditionOnlyPromoWithBetByTime($from, $to, $playerId);

	}

	public function getAvailableWithdrawConditionOnlyPromoWithBetByTime($timeStart, $timeEnd, $playerId=null) {

		// $sql = "SELECT player_id, sum(condition_amount) as amount, min(started_at) as started_at FROM  withdraw_conditions WHERE promotion_id is not null and started_at BETWEEN ? AND ? GROUP BY player_id ";

		$this->db->select("withdraw_conditions.player_id, sum(withdraw_conditions.condition_amount) as amount, min(withdraw_conditions.started_at) as started_at")
		    ->from('withdraw_conditions')
		    ->join('promorules', 'promorules.promorulesId=withdraw_conditions.promotion_id')
            ->where('withdraw_conditions.withdraw_condition_type', self::WITHDRAW_CONDITION_TYPE_BETTING)
		    // ->where('promotion_id is not null', null, false)
		    ->where('withdraw_conditions.started_at >=', $timeStart)
		    ->where('withdraw_conditions.started_at <=', $timeEnd)
		    ->where('promorules.disable_cashback_if_not_finish_withdraw_condition', 1)
		    ->group_by('withdraw_conditions.player_id');

		if(!empty($playerId)){
			$this->db->where('withdraw_conditions.player_id', $playerId);
		}

		// $data = array($timeStart, $timeEnd);

		// $rows = $this->runRawSelectSQLArray($sql, $data);

		$rows=$this->runMultipleRowArray();

		$this->utils->debug_log('getAvailableWithdrawConditionOnlyPromoWithBetByTime SQL:', $this->db->last_query());
		$this->utils->debug_log('result rows:', $rows);

		$wc_amount_map = [];

		if (!empty($rows)) {
			foreach ($rows as $row) {
				$wc_amount_map[$row['player_id']] = ['amount' => $row['amount'], 'started_at' => $row['started_at']];
			}
		}

		// $query = $this->db->query($sql,$data);

		// $rows = $this->getMultipleRow($query);

		//    //echo $this->db->last_query();
		// $wc_amount_map = array();

		// foreach ($rows as $row) {
		// 	$wc_amount_map[$row->player_id] = $row->amount;
		// }

		return $wc_amount_map;

	}

	public function getAvailableWithdrawConditionWithBet($date, $startHour, $endHour) {

		$lastDate = $this->utils->getLastDay($date);

		if (intval($endHour) == 23) {
			//all yesterday
			$date = $lastDate;
		}

		$from = $lastDate . ' ' . $startHour . ':00:00';
		$to = $date . ' ' . $endHour . ':59:59';

		return $this->getAvailableWithdrawConditionWithBetByTime($from, $to);

	}

	/**
	 * get available withdraw condition with bet by start time and end time
	 *
	 * @param string $timeStart
	 * @param string $timeEnd
	 * @return array
	 */
	public function getAvailableWithdrawConditionWithBetByTime($timeStart, $timeEnd) {

		$sql = "SELECT player_id, sum(condition_amount) as amount, min(started_at) as started_at FROM  withdraw_conditions WHERE promotion_id is not null and started_at BETWEEN ? AND ? GROUP BY player_id ";

		$data = array($timeStart, $timeEnd);

		$rows = $this->runRawSelectSQLArray($sql, $data);

		$this->utils->debug_log('getAvailableWithdrawConditionWithBetByTime SQL:', $this->db->last_query());
		$this->utils->debug_log('result rows:', $rows);

		$wc_amount_map = [];

		if (!empty($rows)) {
			foreach ($rows as $row) {
				$wc_amount_map[$row['player_id']] = ['amount' => $row['amount'], 'started_at' => $row['started_at']];
			}
		}

		// $query = $this->db->query($sql,$data);

		// $rows = $this->getMultipleRow($query);

		//    //echo $this->db->last_query();
		// $wc_amount_map = array();

		// foreach ($rows as $row) {
		// 	$wc_amount_map[$row->player_id] = $row->amount;
		// }

		return $wc_amount_map;

	}

	public function getDisabledBetFromWithdrawCondition() {

		$types = [Transactions::DEPOSIT, Transactions::WITHDRAWAL, Transactions::ADD_BONUS, Transactions::AUTO_ADD_CASHBACK_TO_BALANCE];
		$types = implode(',', $types);

		//get last transaction
		$sql = "
select transactions.amount, transactions.transaction_type, transactions.created_at, promorules.disable_cashback_if_not_finish_withdraw_condition
from transactions
left join playerpromo on transactions.player_promo_id=playerpromo.playerpromoId
left join promorules on promorules.promorulesId=playerpromo.promorulesId
where transactions.transaction_type in ({$types})
and withdraw_conditions.withdraw_condition_type=" . self::WITHDRAW_CONDITION_TYPE_BETTING . "
and transactions.to_id=?
and transactions.to_type=?
and transactions.created_at<?
order by transactions.created_at desc
limit 1
";

		$lastTransRows = $this->runRawSelectSQLArray($sql, [$playerId, Transactions::PLAYER,
			$this->utils->formatDateTimeForMysql($start)]);

		$lastTransRow = null;
		if (!empty($lastTransRows)) {
			$lastTransRow = $lastTransRows[0];
		}

		if ($lastTransRow) {

		}

	}

	public function updatePlayerPromoId($id, $playerPromoId) {
		$this->db->set('player_promo_id', $playerPromoId)->where('id', $id);
		return $this->runAnyUpdate('withdraw_conditions');
	}

	public function checkIfPlayerHasUnfinishedCondition($playerId) {
		$withdrawalCondition = $this->getPlayerWithdrawalCondition($playerId);

		$hasUnfinished = false;
		if (!empty($withdrawalCondition)) {
			foreach( $withdrawalCondition as $condition) {
                $unfinished = 0;
                if(isset($condition['unfinished'])){
                    $unfinished = $condition['unfinished'];
                }
				if($unfinished > 0) {
					$hasUnfinished = true;
				}
			}
		}
		return $hasUnfinished;
	}

	public function updateStatus($ids, $status) {
		$this->db->set('status', $status)->where_in('id', $ids);
		return $this->runAnyUpdate('withdraw_conditions');
	}

	public function updateWithdrawConditionBet($bet_amount, $withdraw_condition_id) {
		$this->db->where('id', $withdraw_condition_id);
		$this->db->set(['bet_amount' => $bet_amount]);
		$this->db->update($this->tableName);
	}

	public function getPlayerCancelledAndFinishedCondition($player_id, $start_date, $updated_at, $hourly_update=null) {
		$this->load->model(array('promorules'));

		$this->db->distinct()->select('promorules.promorulesId,
						   promorules.promoName,
						   promocmssetting.promoCmsSettingId,
						   promorules.promoType,
						   promorules.nonDepositPromoType,
						   player.username,
						   player.playerId,
						   playerpromo.playerpromoId,
						   withdraw_conditions.id withdraw_condition_id,
						   withdraw_conditions.started_at,
						   withdraw_conditions.updated_at,
						   withdraw_conditions.stopped_at,
						   withdraw_conditions.bet_amount,
						   withdraw_conditions.source_type,
						   withdraw_conditions.source_id,
						   withdraw_conditions.id as withdrawConditionId,
						   withdraw_conditions.deposit_amount as walletDepositAmount')
				->from('withdraw_conditions')
				->join('playerpromo', 'playerpromo.playerpromoId = withdraw_conditions.player_promo_id', 'left')
				->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId OR promorules.promorulesId = withdraw_conditions.promotion_id', 'left')
				->join('promocmssetting', 'promocmssetting.promoId = promorules.promorulesId', 'left')
				->join('promotype', 'promotype.promotypeId = promorules.promoCategory', 'left')
				->join('player', 'player.playerId = withdraw_conditions.player_id', 'left')
                ->where('withdraw_conditions.withdraw_condition_type', self::WITHDRAW_CONDITION_TYPE_BETTING);

		if(!$hourly_update) {
			$this->db->where('withdraw_conditions.status', self::STATUS_DISABLED);
		}

		// update all if hourly
		if($hourly_update) {
		#	$this->utils->debug_log('hourly update');
		#	$this->db->where('withdraw_conditions.status', self::STATUS_NORMAL);
		}

		if(!empty($start_date)) {
			$this->db->where('withdraw_conditions.started_at >=', $start_date);
		#	$this->db->where('withdraw_conditions.started_at >=', '	2018-01-16 18:20:52');
		#	$this->db->where('withdraw_conditions.updated_at <=', $updated_at);
		}

		if (!empty($player_id)) {
			$this->db->where('withdraw_conditions.player_id', $player_id);
		}

		$this->db->order_by("withdraw_conditions.started_at", "desc");
		$rows = $this->runMultipleRowArray();

		$this->utils->debug_log('sql wc query', $this->utils->printLastSQL());

		if (!empty($rows)) {
			$this->load->model(array('total_player_game_hour', 'group_level', 'game_logs'));
			$wd_ident_ar = [];

			foreach($rows as $row) {
				$current_bet = 0;
				$wd_ident = "{$row['withdrawConditionId']}-{$row['promorulesId']}-{$row['playerpromoId']}-{$row['source_id']}";
				if (array_key_exists($wd_ident, $wd_ident_ar)) {
					continue;
				}
				else {
					$wd_ident_ar[$wd_ident] = 1;
				}

				$gameDescIdArr = $this->group_level->getAllowedGameIdArr($row['playerId']);
				$this->utils->debug_log('gameDescIdArr', count($gameDescIdArr));

				if ($row['promoType'] == Promorules::PROMO_TYPE_NON_DEPOSIT ||
						$row['promoType'] == Promorules::PROMO_TYPE_DEPOSIT) {
					if ($row['promorulesId']) {
						$this->load->model(array('promorules'));
						$gameDescIdArr = $this->promorules->getPlayerGames($row['promorulesId']);
					}
				}

				$current_bet = $this->game_logs->totalPlayerBettingAmountWithLimitByVIP($row['playerId'], $row['started_at'], $row['stopped_at'], $gameDescIdArr);

				$this->utils->debug_log('current bet', $current_bet, 'player id', $row['playerId'], 'DATE WC CONDITION', $row['started_at'], $row['stopped_at']);

				// update withdrawal condition bet
				if (!empty($current_bet)) {
					$this->updateWithdrawConditionBet($current_bet, $row['withdraw_condition_id']);
				}
			}
		} else {
			$this->utils->debug_log('empty data', $start_date, $updated_at);
		}
	}

	public function getPlayerUnfinishedWithdrawCondition($playerId) {
		$player_wc = $this->getPlayerWithdrawalCondition($playerId);

		if(empty($player_wc)){
            $this->utils->debug_log("player [$playerId], doesn't have unfinished withdraw condition");
		    return FALSE;
        }

		$total_bet = 0;
		$total_required_bet = 0;
        foreach( $player_wc as $data ) {
            if($data['withdraw_condition_type'] != self::WITHDRAW_CONDITION_TYPE_BETTING){
                continue;
            }

            if($this->utils->isEnabledFeature('disabled_withdraw_condition_share_betting_amount')){
                $total_bet += $data['currentBet'];
            }else{
                if($total_bet < $data['currentBet'] ) {
                    $total_bet = $data['currentBet'];
                }
            }

            $total_required_bet+= $data['conditionAmount'];
        }

		$unfinished_wc = $total_required_bet - $total_bet;
        $this->utils->debug_log("get_player_unfinished_withdraw_condition => player [$playerId], totalRequiredBet [$total_required_bet], totalPlayerBet [$total_bet], un_finished [$unfinished_wc]");

        if(empty($unfinished_wc) || $unfinished_wc < 0){
            return FALSE;
        }

		return $unfinished_wc;
	}

	public function getPlayerUnfinishedWithdrawConditionForeach($playerId){
        $player_wc = $this->getPlayerWithdrawalCondition($playerId);

        if(empty($player_wc)){
            $this->utils->debug_log("player [$playerId], doesn't have unfinished withdraw condition foreach");
            return FALSE;
        }

        foreach( $player_wc as $data ) {
            if($data['withdraw_condition_type'] != self::WITHDRAW_CONDITION_TYPE_BETTING){
                continue;
            }
            if($data['unfinished'] > 0) {
                $this->utils->debug_log("get_player_unfinished_withdraw_condition_foreach===>", "player", [$playerId], "conditionAmount", $data['conditionAmount'], "currentBet", $data['currentBet'], "un_finished", $data['unfinished']);
                return $data['unfinished'];
            }
        }

        return FALSE;
    }

    public function getPlayerUnfinishedDepositConditionForeach($playerId){
	    $this->load->model('promorules');
        $player_dc = $this->getPlayerDepositConditionInWithdrawalCondition($playerId);

        if(empty($player_dc)){
            $this->utils->debug_log("player [$playerId], doesn't have unfinished deposit condition");
            return FALSE;
        }

        $total_deposit = 0;
        $total_required_deposit = 0;

        foreach( $player_dc as $data ) {
            if($total_deposit < $data['currentDeposit']) {
                $total_deposit = $data['currentDeposit'];
            }

            $total_required_deposit = $data['conditionDepositAmount'];

            $unfinished_dc = $total_required_deposit - $total_deposit;
            $unfinished_dc = ($unfinished_dc <= 0) ? 0 : $unfinished_dc;

            if($unfinished_dc > 0) {
                $this->utils->debug_log("get_player_unfinished_withdraw_condition => player [$playerId], totalRequiredBet [$total_required_deposit], totalPlayerBet [$total_deposit], un_finished [$unfinished_dc]");
                return $unfinished_dc;
            }
        }

        return FALSE;
	}

	/**
	 * this will Cancel the withdrawal condition of deleted promotion
	 *
	 *
	 * @return int $row_count the rows updated
	 */
	public function cancelWithdrawalConditionOfDeletedPromotion()
	{
		$this->load->model(['promorules']);

		$promo_rules_id = $this->db->distinct()->select('pr.promorulesId')
								->from('promorules pr')
								->join('promocmssetting pcms','pcms.promoId = pr.promorulesId','left')
								->where('pcms.deleted_flag',1)
								->or_where('pcms.promoId IS NULL',null,false)
								->get();

		$promorules_array = $promo_rules_id->result_array();

		$promorules_count = count($promorules_array);

		if($promorules_count > 0){
			$imploded_pr_id = implode(',',array_column($promorules_array,'promorulesId'));
			$this->CI->utils->debug_log('row counts --------------->>>>>>>>>>>>>>',$promo_rules_id->num_rows);
			$this->CI->utils->debug_log('Promotion ID\'s to be update --------------->>>>>>>>>>>>>>',$imploded_pr_id);

			# updating in withdraw_conditions table
			$data['status'] = parent::STATUS_DISABLED;
			$data['detail_status'] = self::DETAIL_STATUS_CANCELLED_MANUALLY;
			$data['updated_at'] = $this->utils->getNowForMysql();
			$data['stopped_at'] = $this->utils->getNowForMysql();

			$this->db->where_in('promotion_id',explode(',',$imploded_pr_id));
			$this->db->set($data);
			$row_count = $this->runAnyUpdateWithResult('withdraw_conditions');

			$this->CI->utils->debug_log('Updated rows count --------------->>>>>>>>>>>>>>',$row_count);

			$this->utils->printLastSQL();

			return $row_count;
		}

		return $promorules_count;
	}
}

/////end of file///////
