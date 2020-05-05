<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Player_promo
 *
 * General behaviors include :
 *
 * * Status of promo application
 * * Get player active promo and bonus amount
 * * Promo players detail (approved, finish, decline, expire promo )
 * * Calculate bonus amount
 * * Get player promo history
 * * Get all pending sub wallet promo
 *
 * @category Marketing Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Player_promo extends BaseModel {

	protected $tableName = 'playerpromo';

	protected $idField = 'playerpromoId';

	const TRANS_STATUS_REQUEST = 0; // PENDING
	const TRANS_STATUS_APPROVED = 1; // APPLIED really released bonus
	const TRANS_STATUS_EXPIRED = 2; //EXPIRED, request => declined, locked bonus => declined, pre approved => declined
	const TRANS_STATUS_DECLINED = 3; // DECLINED, request => declined, locked bonus => declined, pre approved => declined
	// const TRANS_STATUS_REDEEMED = 4; //PAID / REDEEMED
	// const TRANS_STATUS_PERMITTED = 5; //useless , please ignore
	// const TRANS_STATUS_MANUAL_REQUEST_PENDING = 6; // PENDING
	const TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS = 7; //actived,  request => pre approved
	const TRANS_STATUS_DECLINED_FOREVER = 8; // player declined forever
	const TRANS_STATUS_FINISHED_WITHDRAW_CONDITION = 9; //locked bonus => finished, approved => finished
	const TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS = 10; //request => locked bonus

    const TRANSACTION_DATE_TYPE_REQUEST_TIME = 0;
    const TRANSACTION_DATE_TYPE_PROCESSED_TIME = 1;

	const PROMO_STATUS_ACTIVE = 0;
	const PROMO_STATUS_INACTIVE = 1;

	function __construct() {
		parent::__construct();
	}

    /**
     * Process $promoName
     *
     * @param string $promoName
     */
    public function promoName($promoName){
        return ($promoName === Promorules::SYSTEM_MANUAL_PROMO_RULE_NAME) ? lang('promo.' . Promorules::SYSTEM_MANUAL_PROMO_RULE_NAME) : $promoName;
    }

	/**
	 * overview : get status name
	 *
	 * @param int $status
	 * @return string
	 */
	public function statusToName($status) {
		switch ($status) {
		case Player_promo::TRANS_STATUS_REQUEST:
			return lang('Request');
			break;

		case Player_promo::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS:
			return lang('Actived');
			break;

		case Player_promo::TRANS_STATUS_APPROVED:
			return lang('Released');
			break;

		case Player_promo::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS:
			return lang('Locked Bonus');
			break;

		case Player_promo::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION:
			return lang('Finished');
			break;

		case Player_promo::TRANS_STATUS_EXPIRED:
			return lang('Expired');
			break;

		case Player_promo::TRANS_STATUS_DECLINED:
			return lang('Declined');
			break;

		case Player_promo::TRANS_STATUS_DECLINED_FOREVER:
			return lang('Declined Forever');
			break;

		}

		return lang('Unknown');
	}

	/**
	 * overview : count all status of promo application
	 *
	 * @return array
	 */
	public function countAllStatusOfPromoApplication() {
        $date_from = $this->utils->getFirstDateOfCurrentMonth() . ' 00:00:00';
        $date_to = date("Y-m-d") . ' 23:59:59';

		$this->db->select('count(playerpromoId) as cnt, transactionStatus', false)->from('playerpromo')
            ->join('player', "playerpromo.playerId = player.playerId", 'left')
            ->where('player.deleted_at IS NULL')
            ->where("playerpromo.dateProcessed BETWEEN '$date_from' AND '$date_to'")

			->group_by('transactionStatus');

		$count = [
			self::TRANS_STATUS_REQUEST => 0,
			self::TRANS_STATUS_APPROVED => 0,
			self::TRANS_STATUS_DECLINED => 0,
//			self::TRANS_STATUS_EXPIRED => 0,
			// self::TRANS_STATUS_REDEEMED=>0,
			// self::TRANS_STATUS_PERMITTED=>0,
			// self::TRANS_STATUS_MANUAL_REQUEST_PENDING=>0,
//			self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS => 0,
//			self::TRANS_STATUS_DECLINED_FOREVER => 0,
			self::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION => 0,
//			self::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS => 0,
		];

		$rows = $this->runMultipleRow();

		if (!empty($rows)) {
			foreach ($rows as $row) {
				$count[$row->transactionStatus] = number_format(@$count[$row->transactionStatus] + $row->cnt);
			}
		}

		return $count;

	}

	/**
	 * @deprecated
	 */
	public function disablePlayerPromo($playerId) {
		// $data['promoStatus'] = self::PROMO_STATUS_INACTIVE;
		// $this->db->where('playerId', $playerId);
		// // $this->db->update($this->tableName, $data);
		// return $this->runUpdate($data);
	}

	/**
	 * overview : update transaction player promo
	 *
	 * @param int	$playerPromoId
	 * @param int	$transStatus
	 * @return bool
	 */
	public function updateTransStatusPlayerPromo($playerPromoId, $transStatus) {
		$this->db->set('transactionStatus', $transStatus);
		$this->db->where('playerpromoId', $playerPromoId);
		$this->db->update('playerpromo');

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * overview : get player active promo
	 *
	 * @param int	$playerId
	 * @return array
	 */
	public function getPlayerActivePromo($playerId) {
		$this->db->select('playerpromo.dateProcessed as dateJoined,
						   playerpromo.bonusAmount,
						   promorules.promoName,
						   promorules.promoCode,
						   playerpromo.promorulesId
						   ')
			->from($this->tableName)
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left');
		// $this->db->where('playerpromo.promoStatus', self::PROMO_STATUS_ACTIVE);
		$this->db->where('playerpromo.transactionStatus', self::TRANS_STATUS_APPROVED)
			->where('playerpromo.playerId', $playerId);

		return $this->runMultipleRow();
		// $query = $this->db->get();
		// if ($query->num_rows() > 0) {
		// 	foreach ($query->result_array() as $row) {
		// 		$row['dateJoined'] = mdate('%M %d, %Y', strtotime($row['dateJoined']));
		// 		$data[] = $row;
		// 	}
		// 	//var_dump($data);exit();
		// 	return $data;
		// }
		// return false;
	}

	// public function approvePlayerPromotion($playerPromoId, $adminUserId) {
	// 	$data = array(
	// 		'processedBy' => $adminUserId,
	// 		// 'dateApply' => $this->utils->getNowForMysql(),
	// 		'dateProcessed' => $this->utils->getNowForMysql(),
	// 		'transactionStatus' => self::TRANS_STATUS_APPROVED,
	// 	);

	// 	return $this->updateRow($playerPromoId, $data);
	// }

	// public function declinePlayerPromotion($playerPromoId, $adminUserId, $reason = null) {
	// 	$data = array(
	// 		'processedBy' => $adminUserId,
	// 		'declinedApplicationReason' => $reason,
	// 		// 'cancelRequestDate' => $this->utils->getNowForMysql(),
	// 		'dateProcessed' => $this->utils->getNowForMysql(),
	// 		'transactionStatus' => self::TRANS_STATUS_DECLINED,
	// 	);

	// 	return $this->updateRow($playerPromoId, $data);
	// }

	/**
	 * overview : get player bonus amount
	 *
	 * @param $playerpromoId
	 * @return null
	 */
	public function getPlayerBonusAmount($playerpromoId) {
		return $this->runOneRowOneFieldById($playerpromoId, 'bonusAmount');
	}

	/**
	 * overview : calculate promo betting ammount
	 *
	 * @param int	$playerPromoId
	 * @param int	$depositAmount
	 * @param int 	$bonusAmount
	 * @return array
	 */
	public function calcPromoBettingAmount($playerPromoId, $depositAmount, $bonusAmount) {
		//load promorules
		$promorulesId = null;
		$bettingAmount = 0;
		$this->db->select('promorules.promorulesId , promorules.withdrawRequirementConditionType, promorules.withdrawRequirementBetAmount, promorules.withdrawRequirementBetCntCondition')
			->from($this->tableName)
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->where('playerpromo.playerpromoId', $playerPromoId);
		$row = $this->runOneRow();
		if ($row) {
			if ($row->withdrawRequirementConditionType == 0) {
				$bettingAmount = $row->withdrawRequirementBetAmount;
			} else {
				$bettingAmount = ($depositAmount + $bonusAmount) * $row->withdrawRequirementBetCntCondition;
			}
		}

		return array($promorulesId, $bettingAmount);
	}

	/**
	 * overview : count player duplicate promo
	 *
	 * @param int	$playerId
	 * @param int	$promorulesId
	 * @param array	$transactionStatusArr
	 * @param date	$dateFrom
	 * @param date	$dateTo
	 * @param int $transferSubwalletId
	 * @param int $playerPromoId
	 * @return mixed
	 */
	public function countPlayerDuplicatePromo($playerId, $promorulesId, $transactionStatusArr,
		$dateFrom = null, $dateTo = null, $transferSubwalletId = null, $playerPromoId = null) {
		//var_dump($playerId,$promorulesId,$transactionStatus);exit();
		$this->db->select('playerpromo.depositAmount,playerpromo.bonusAmount,playerpromo.dateProcessed,playerpromo.transactionStatus')->from('playerpromo');
		$this->db->where('playerpromo.playerId', $playerId);
		$this->db->where('playerpromo.promorulesId', $promorulesId);
		// $this->db->where('playerpromo.promoStatus', self::PROMO_STATUS_ACTIVE); // active
		$this->db->where_in('playerpromo.transactionStatus', $transactionStatusArr); //1=approved or 0=request status
		// $this->db->order_by('playerpromo.playerpromoId', 'asc');
		if ($dateFrom && $dateTo) {
			$this->db->where('playerpromo.dateProcessed >=', $dateFrom);
			$this->db->where('playerpromo.dateProcessed <=', $dateTo);
		}
		if ($playerPromoId) {
			$this->db->where('playerpromo.playerPromoId !=', $playerPromoId);
		}
		if ($transferSubwalletId) {
			//limit triggered_subwallet_id
			$this->db->where('triggered_subwallet_id', $transferSubwalletId);
		}
		$query = $this->db->get();
		//var_dump($query->num_rows());exit();
		return $query->num_rows();
	}

	/**
	 * Will get promo with the same promorulesid
	 *
	 * @param 	playerId int
	 * @param 	promorulesId int
	 * @return	cnt
	 */

	/**
	 * overview : get player duplicate promo
	 *
	 * @param int	$playerId
	 * @param int	$promorulesId
	 * @param int	$transactionStatus
	 * @param date  $dateFrom
	 * @param date  $dateTo
	 * @param int $transferSubwalletId
	 * @return array
	 */
	public function getPlayerDuplicatePromo($playerId, $promorulesId, $transactionStatus = self::TRANS_STATUS_APPROVED,
		$dateFrom = null, $dateTo = null, $transferSubwalletId = null) {
		//var_dump($playerId,$promorulesId,$transactionStatus);exit();
		$this->db->select('playerpromo.depositAmount,playerpromo.bonusAmount,playerpromo.dateProcessed,playerpromo.transactionStatus')->from('playerpromo');
		$this->db->where('playerpromo.playerId', $playerId);
		$this->db->where('playerpromo.promorulesId', $promorulesId);
		// $this->db->where('playerpromo.promoStatus', self::PROMO_STATUS_ACTIVE); // active
		if (is_array($transactionStatus)) {
			$this->db->where_in('playerpromo.transactionStatus', $transactionStatus); //1=approved or 0=request status
		} else {
			$this->db->where('playerpromo.transactionStatus', $transactionStatus); //1=approved or 0=request status
		}
		// $this->db->order_by('playerpromo.playerpromoId', 'asc');
		if ($dateFrom && $dateTo) {
			$this->db->where('playerpromo.dateProcessed >=', $dateFrom);
			$this->db->where('playerpromo.dateProcessed <=', $dateTo);
		}
		// if($transferSubwalletId){
		//limit triggered_subwallet_id
		// $this->db->where('playerpromo.triggered_subwallet_id', $transferSubwalletId);
		// }
		$query = $this->db->get();
		//var_dump($query->num_rows());exit();
		return $query->num_rows();
	}

	/**
	 * overview : approve promo to player
	 *
	 * @param int	$playerId
	 * @param int	$promorulesId
	 * @param int	$bonusAmount
	 * @param int	$promoCmsSettingId
	 * @param int	$adminId
	 * @param int	$playerPromoId
	 * @param int	$withdrawConditionAmount
	 * @param array $extra_info
	 * @param int $depositAmount
	 * @param null $betTimes
	 * @param null $reason
	 * @return array
	 */
	public function approvePromoToPlayer($playerId, $promorulesId, $bonusAmount,
		$promoCmsSettingId, $adminId, $playerPromoId = null, $withdrawConditionAmount = null,
		&$extra_info = null, $depositAmount = null, $betTimes = null, $reason = null, $transferConditionAmount = null) {

		$transactionStatus = self::TRANS_STATUS_APPROVED;
		return $this->syncPlayerPromo($playerPromoId, $playerId, $promorulesId, $promoCmsSettingId,
			$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes,
			$adminId, $transactionStatus, $reason, $transferConditionAmount);

		// if (!empty($playerPromoId)) {
		// 	$data = array(
		// 		'processedBy' => $adminId,
		// 		// 'dateApply' => $this->utils->getNowForMysql(),
		// 		'dateProcessed' => $this->utils->getNowForMysql(),
		// 		'transactionStatus' => self::TRANS_STATUS_APPROVED,
		// 		'verificationStatus' => self::DB_TRUE,
		// 		'triggered_subwallet_id' => @$extra_info['subWalletId'],
		// 	);

		// 	if($playerBonusAmount){
		// 		$data['bonusAmount']=$playerBonusAmount;
		// 	}
		// 	if($withdrawBetAmtCondition){
		// 		$data['withdrawConditionAmount']=$withdrawBetAmtCondition;
		// 	}

		// 	$this->updateRow($playerPromoId, $data);
		// 	return $playerPromoId;
		// } else {
		// 	$this->load->model(array('group_level'));
		// 	//update level id too
		// 	$level_id = $this->group_level->getPlayerLevelId($playerId);
		// 	$data = array('playerId' => $playerId,
		// 		'level_id' => $level_id,
		// 		'promorulesId' => $promorulesId,
		// 		'bonusAmount' => $playerBonusAmount,
		// 		'withdrawConditionAmount'=>$withdrawBetAmtCondition,
		// 		'promoCmsSettingId' => $promoCmsSettingId,
		// 		'dateProcessed' => $this->getNowForMysql(),
		// 		'processedBy' => $adminId,
		// 		'transactionStatus' => self::TRANS_STATUS_APPROVED, //approved
		// 		'verificationStatus' => self::DB_TRUE,
		// 		'triggered_subwallet_id' => @$extra_info['subWalletId'],
		// 	);

		// 	return $this->insertData($this->tableName, $data);
		// 	// $this->db->insert('playerpromo', $data);
		// 	// return $this->db->insert_id();
		// }
	}

	/**
	 * overview : approve promo to player without release
	 *
	 * @param int	$playerId
	 * @param int	$promorulesId
	 * @param int	$bonusAmount
	 * @param int	$promoCmsSettingId
	 * @param int	$adminId
	 * @param int	$playerPromoId
	 * @param int	$withdrawConditionAmount
	 * @param int	$depositAmount
	 * @param int   $betTimes
	 * @param string $reason
	 * @return array
	 */
	public function approvePromoToPlayerWithouRelease($playerId, $promorulesId, $bonusAmount,
		$promoCmsSettingId, $adminId, $playerPromoId = null, $withdrawConditionAmount = null,
		$depositAmount = null, $betTimes = null, $reason = null, $transferConditionAmount = null) {

		$transactionStatus = self::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS;
		return $this->syncPlayerPromo($playerPromoId, $playerId, $promorulesId, $promoCmsSettingId,
			$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes,
			$adminId, $transactionStatus, $reason, $transferConditionAmount);
	}

	// public function approvePrePromoToPlayer($playerId, $promorulesId, $playerBonusAmount,
	// 	$promoCmsSettingId, $adminId, $playerPromoId = null, $withdrawBetAmtCondition=null) {

	/**
	 * overview : approve pre promo to player
	 *
	 * @param int	 $playerId
	 * @param int	 $promorulesId
	 * @param int	 $bonusAmount
	 * @param int	 $promoCmsSettingId
	 * @param int	 $adminId
	 * @param double $depositAmount
	 * @param double $withdrawConditionAmount
	 * @param int 	 $transactionStatus
	 * @param null   $betTimes
	 * @param string $reason
	 * @param int  $playerPromoId
	 * @return array
	 */
	public function approvePrePromoToPlayer($playerId, $promorulesId, $bonusAmount,
		$promoCmsSettingId, $adminId = null, $depositAmount = null, $withdrawConditionAmount = null,
		$transactionStatus = self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS,
		$betTimes = null, $reason = null, $playerPromoId = null, $transferConditionAmount = null) {

		return $this->syncPlayerPromo($playerPromoId, $playerId, $promorulesId, $promoCmsSettingId,
			$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes,
			$adminId, $transactionStatus, $reason, $transferConditionAmount);
	}

	/**
	 * overview : update only number without release
	 *
	 * @param int		$playerId
	 * @param int		$promorulesId
	 * @param double	$playerBonusAmount
	 * @param int		$promoCmsSettingId
	 * @param int		$adminId
	 * @param int		$playerPromoId
	 * @param int		$withdrawBetAmtCondition
	 * @param double $depositAmount
	 * @return array
	 */
	public function updateOnlyNumberWithoutRelease($playerId, $promorulesId,
		$playerBonusAmount, $promoCmsSettingId, $adminId, $playerPromoId, $withdrawBetAmtCondition, $depositAmount = null) {

		//still request , just update number
		if (!empty($playerPromoId)) {
			//don't update when bonus amount !=0
			$playerPromo = $this->getPlayerPromo($playerPromoId);
			if (!empty($playerPromo)) {
				if (!empty($playerPromo->bonusAmount)) {
					return $playerPromoId;
				}
			}

			$data = array(
				'processedBy' => $adminId,
				// 'dateApply' => $this->utils->getNowForMysql(),
				'dateProcessed' => $this->utils->getNowForMysql(),
				// 'transactionStatus' => self::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS,
				'verificationStatus' => self::DB_FALSE,
			);

			if ($playerBonusAmount) {
				$data['bonusAmount'] = $playerBonusAmount;
			}
			if ($withdrawBetAmtCondition) {
				$data['withdrawConditionAmount'] = $withdrawBetAmtCondition;
			}
			if ($depositAmount) {
				$data['depositAmount'] = $depositAmount;
			}

			$this->updateRow($playerPromoId, $data);
			return $playerPromoId;
		} else {
			$this->load->model(array('group_level'));
			//update level id too
			$level_id = $this->group_level->getPlayerLevelId($playerId);

			if (empty($playerBonusAmount)) {
				$playerBonusAmount = 0;
			}
			if (empty($withdrawBetAmtCondition)) {
				$withdrawBetAmtCondition = 0;
			}

			$data = array('playerId' => $playerId,
				'level_id' => $level_id,
				'promorulesId' => $promorulesId,
				'bonusAmount' => $playerBonusAmount,
				'withdrawConditionAmount' => $withdrawBetAmtCondition,
				'depositAmount' => $depositAmount,
				'promoCmsSettingId' => $promoCmsSettingId,
				'dateProcessed' => $this->getNowForMysql(),
				'processedBy' => $adminId,
				'transactionStatus' => self::TRANS_STATUS_REQUEST, //approved
				'verificationStatus' => self::DB_FALSE,
				'login_ip' => $this->utils->getIP(),
			);

			return $this->insertData($this->tableName, $data);
			// $this->db->insert('playerpromo', $data);
			// return $this->db->insert_id();
		}

	}

	/**
	 * @param $playerId
	 * @param $promorulesId
	 * @param $bonusAmount
	 * @param $promoCmsSettingId
	 * @param $adminId
	 * @param $depositAmount
	 * @param $withdrawConditionAmount
	 * @param int $transactionStatus
	 * @param int $betTimes
	 * @param int $reason
	 * @param int $playerPromoId
	 * @return array
	 */
	public function expirePromoToPlayer($playerId, $promorulesId, $bonusAmount,
		$promoCmsSettingId, $adminId, $depositAmount, $withdrawConditionAmount,
		$transactionStatus = self::TRANS_STATUS_EXPIRED, $betTimes = null, $reason = null, $playerPromoId = null, $transferConditionAmount = null) {

		return $this->syncPlayerPromo($playerPromoId, $playerId, $promorulesId, $promoCmsSettingId,
			$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes,
			$adminId, $transactionStatus, $reason, $transferConditionAmount);

		// public function expirePromoToPlayer($playerId, $promorulesId, $playerBonusAmount,
		// 	$promoCmsSettingId, $adminId, $reason = null, $playerPromoId = null) {
		// if (!empty($playerPromoId)) {
		// 	$data = array(
		// 		'processedBy' => $adminId,
		// 		'declinedApplicationReason' => $reason,
		// 		// 'cancelRequestDate' => $this->utils->getNowForMysql(),
		// 		'dateProcessed' => $this->utils->getNowForMysql(),
		// 		'transactionStatus' => self::TRANS_STATUS_EXPIRED,
		// 	);

		// 	$this->updateRow($playerPromoId, $data);
		// 	return $playerPromoId;
		// } else {
		// 	$this->load->model(array('group_level'));
		// 	//update level id too
		// 	$level_id = $this->group_level->getPlayerLevelId($playerId);

		// 	//decline promo
		// 	$data = array('playerId' => $playerId,
		// 		'level_id' => $level_id,
		// 		'promorulesId' => $promorulesId,
		// 		'bonusAmount' => $playerBonusAmount,
		// 		'promoCmsSettingId' => $promoCmsSettingId,
		// 		'dateProcessed' => $this->getNowForMysql(),
		// 		'processedBy' => $adminId,
		// 		'transactionStatus' => self::TRANS_STATUS_EXPIRED,
		// 	);

		// 	return $this->insertData($this->tableName, $data);
		// }
	}

	/**
	 * overview : finish promo to player
	 *
	 * @param int		$playerId
	 * @param int		$promorulesId
	 * @param double	$bonusAmount
	 * @param int		$promoCmsSettingId
	 * @param int		$adminId
	 * @param int		$playerPromoId
	 * @param int		$withdrawConditionAmount
	 * @param array		$extra_info
	 * @param double $depositAmount
	 * @param int $betTimes
	 * @param string $reason
	 * @return array
	 */
	public function finishPromoToPlayer($playerId, $promorulesId, $bonusAmount,
		$promoCmsSettingId, $adminId, $playerPromoId = null, $withdrawConditionAmount = null,
		&$extra_info = null, $depositAmount = null, $betTimes = null, $reason = null, $transferConditionAmount = null) {

		$transactionStatus = self::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION;
		return $this->syncPlayerPromo($playerPromoId, $playerId, $promorulesId, $promoCmsSettingId,
			$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes,
			$adminId, $transactionStatus, $reason, $transferConditionAmount);

		// public function finishPromoToPlayer($playerId, $promorulesId, $promoCmsSettingId,
		// 	$playerBonusAmount, $depositAmount, $withdrawConditionAmount,
		// 	$adminId, $reason = null, $playerPromoId = null) {
		// 	if (!empty($playerPromoId)) {
		// 		$data = array(
		// 			'processedBy' => $adminId,
		// 			'declinedApplicationReason' => $reason,
		// 			// 'cancelRequestDate' => $this->utils->getNowForMysql(),
		// 			'dateProcessed' => $this->utils->getNowForMysql(),
		// 			'transactionStatus' => self::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION,
		// 		);

		// 		$this->updateRow($playerPromoId, $data);
		// 		return $playerPromoId;
		// 	} else {
		// 		$this->load->model(array('group_level'));
		// 		//update level id too
		// 		$level_id = $this->group_level->getPlayerLevelId($playerId);

		// 		//finished promo
		// 		$data = array('playerId' => $playerId,
		// 			'level_id' => $level_id,
		// 			'promorulesId' => $promorulesId,
		// 			'bonusAmount' => $playerBonusAmount,
		// 			'depositAmount'=> $depositAmount,
		// 			'withdrawConditionAmount'=>$withdrawConditionAmount,
		// 			'promoCmsSettingId' => $promoCmsSettingId,
		// 			'dateProcessed' => $this->getNowForMysql(),
		// 			'processedBy' => $adminId,
		// 			'transactionStatus' => self::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION,
		// 		);

		// 		return $this->insertData($this->tableName, $data);
		// 	}
	}

	/**
	 * overview : sync player promo
	 *
	 * @param int		$playerPromoId
	 * @param int		$playerId
	 * @param int		$promorulesId
	 * @param int		$promoCmsSettingId
	 * @param double	$bonusAmount
	 * @param double	$depositAmount
	 * @param double	$withdrawConditionAmount
	 * @param int		$betTimes
	 * @param int		$adminId
	 * @param int		$status
	 * @param string	$reason
	 * @return array
	 */
	public function syncPlayerPromo($playerPromoId, $playerId, $promorulesId, $promoCmsSettingId,
		$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes,
		$adminId, $status, $reason, $transferConditionAmount) {
		$this->utils->debug_log('syncPlayerPromo playerPromoId', $playerPromoId, 'playerId', $playerId,
			'promorulesId', $promorulesId, 'promoCmsSettingId', $promoCmsSettingId,
			'bonusAmount', $bonusAmount, 'depositAmount', $depositAmount, 'withdrawConditionAmount', $withdrawConditionAmount,
			'betTimes', $betTimes, 'adminId', $adminId, 'status', $status, 'reason', $reason, 'transferConditionAmount', $transferConditionAmount);

		if($status == self::TRANS_STATUS_APPROVED)  {
			$this->load->model('withdraw_condition');
			if($this->utils->isEnabledFeature('clear_withdraw_condition_when_add_player_bonus')){
				$this->withdraw_condition->checkAndCleanWithdrawCondition($playerId);
			}
		}

		if (!empty($playerPromoId)) {
			$data = array(
				'processedBy' => $adminId,
				'declinedApplicationReason' => $reason,
				'dateProcessed' => $this->utils->getNowForMysql(),
				'transactionStatus' => $status,
			);

			if ($bonusAmount) {
				$data['bonusAmount'] = $bonusAmount;
			}
			if ($withdrawConditionAmount) {
				$data['withdrawConditionAmount'] = $withdrawConditionAmount;
			}
            if ($transferConditionAmount) {
                $data['transferConditionAmount'] = $transferConditionAmount;
            }
			if ($depositAmount) {
				$data['depositAmount'] = $depositAmount;
			}
			if ($betTimes) {
				$data['betTimes'] = $betTimes;
			}

			$this->updateRow($playerPromoId, $data);

			if (!empty($reason)) {
				$sql = "update playerpromo set note=concat(ifnull(note,''),' | ',?) where playerpromoId=?";
				$this->runRawUpdateInsertSQL($sql, [$reason, $playerPromoId]);
			}

			return $playerPromoId;
		} else {
			$this->load->model(array('group_level'));
			//update level id too
			$level_id = $this->group_level->getPlayerLevelId($playerId);

			$bonusAmount = empty($bonusAmount) ? 0 : $bonusAmount;
			$withdrawConditionAmount = empty($withdrawConditionAmount) ? 0 : $withdrawConditionAmount;
            $transferConditionAmount = empty($transferConditionAmount) ? 0 : $transferConditionAmount;
			$depositAmount = empty($depositAmount) ? 0 : $depositAmount;
			$betTimes = empty($betTimes) ? 0 : $betTimes;

			//finished promo
			$data = array('playerId' => $playerId,
				'level_id' => $level_id,
				'promorulesId' => $promorulesId,
				'promoCmsSettingId' => $promoCmsSettingId,
				'bonusAmount' => $bonusAmount,
				'depositAmount' => $depositAmount,
				'withdrawConditionAmount' => $withdrawConditionAmount,
                'transferConditionAmount' => $transferConditionAmount,
				'betTimes' => $betTimes,
				'dateProcessed' => $this->getNowForMysql(),
				'processedBy' => $adminId,
				'note' => $reason,
				'transactionStatus' => $status,
				'login_ip' => $this->utils->getIP(),
			);

			return $this->insertData($this->tableName, $data);
		}
	}


    public function updatePlayerPromo($playerPromoId, $data) {
        if (empty($playerPromoId) || empty($data)) {
            return FALSE;
        }

        return $this->updateRow($playerPromoId, $data);
    }

	/**
	 * overview : decline promo to player
	 *
	 * @param int		$playerId
	 * @param int		$promorulesId
	 * @param double	$bonusAmount
	 * @param int		$promoCmsSettingId
	 * @param int		$adminId
	 * @param double	$depositAmount
	 * @param double	$withdrawConditionAmount
	 * @param int		$transactionStatus
	 * @param int		$betTimes
	 * @param string	$reason
	 * @param int	    $playerPromoId
	 * @return array
	 */
	public function declinePromoToPlayer($playerId, $promorulesId, $bonusAmount,
		$promoCmsSettingId, $adminId = null, $depositAmount = null, $withdrawConditionAmount = null,
		$transactionStatus = self::TRANS_STATUS_DECLINED, $betTimes = null, $reason = null, $playerPromoId = null, $transferConditionAmount = null) {

		return $this->syncPlayerPromo($playerPromoId, $playerId, $promorulesId, $promoCmsSettingId,
			$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes,
			$adminId, $transactionStatus, $reason, $transferConditionAmount);

		// if (!empty($playerPromoId)) {
		// 	$data = array(
		// 		'processedBy' => $adminId,
		// 		'declinedApplicationReason' => $reason,
		// 		// 'cancelRequestDate' => $this->utils->getNowForMysql(),
		// 		'dateProcessed' => $this->utils->getNowForMysql(),
		// 		'transactionStatus' => self::TRANS_STATUS_DECLINED,
		// 	);

		// 	$this->updateRow($playerPromoId, $data);
		// 	return $playerPromoId;
		// } else {
		// 	$this->load->model(array('group_level'));
		// 	//update level id too
		// 	$level_id = $this->group_level->getPlayerLevelId($playerId);

		// 	//decline promo
		// 	$data = array('playerId' => $playerId,
		// 		'level_id' => $level_id,
		// 		'promorulesId' => $promorulesId,
		// 		'bonusAmount' => $playerBonusAmount,
		// 		'promoCmsSettingId' => $promoCmsSettingId,
		// 		'dateProcessed' => $this->getNowForMysql(),
		// 		'processedBy' => $adminId,
		// 		'transactionStatus' => self::TRANS_STATUS_DECLINED, //declined
		// 	);

		// 	return $this->insertData($this->tableName, $data);
		// }
	}

	/**
	 * overview : decline forever promo to player
	 *
	 * @param int		$playerId
	 * @param int		$promorulesId
	 * @param double 	$bonusAmount
	 * @param int		$promoCmsSettingId
	 * @param int		$adminId
	 * @param double	$depositAmount
	 * @param int		$withdrawConditionAmount
	 * @param int 		$transactionStatus
	 * @param int	    $betTimes
	 * @param string	$reason
	 * @param int	    $playerPromoId
	 * @return array
	 */
	public function declinedForeverPromoToPlayer($playerId, $promorulesId, $bonusAmount,
		$promoCmsSettingId, $adminId = null, $depositAmount = null, $withdrawConditionAmount = null,
		$transactionStatus = self::TRANS_STATUS_DECLINED_FOREVER, $betTimes = null, $reason = null, $playerPromoId = null, $transferConditionAmount = null) {

		return $this->syncPlayerPromo($playerPromoId, $playerId, $promorulesId, $promoCmsSettingId,
			$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes,
			$adminId, $transactionStatus, $reason, $transferConditionAmount);

	}

	/**
	 * overview : request promo to player
	 *
	 * @param int		$playerId
	 * @param int		$promorulesId
	 * @param double	$bonusAmount
	 * @param int		$promoCmsSettingId
	 * @param int 		$adminId
	 * @param double	$depositAmount
	 * @param double	$withdrawConditionAmount
	 * @param int 		$transactionStatus
	 * @param int		$betTimes
	 * @param string	$reason
	 * @param int		$playerPromoId
	 * @return array
	 */
	public function requestPromoToPlayer($playerId, $promorulesId, $bonusAmount,
		$promoCmsSettingId, $adminId = null, $depositAmount = null, $withdrawConditionAmount = null,
		$transactionStatus = self::TRANS_STATUS_REQUEST, $betTimes = null, $reason = null, $playerPromoId = null, $transferConditionAmount = null) {

		return $this->syncPlayerPromo($playerPromoId, $playerId, $promorulesId, $promoCmsSettingId,
			$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes,
			$adminId, $transactionStatus, $reason, $transferConditionAmount);

		// $this->load->model(array('group_level'));
		// //update level id too
		// $level_id = $this->group_level->getPlayerLevelId($playerId);
		// //request
		// $data = array('playerId' => $playerId,
		// 	'level_id' => $level_id,
		// 	'promorulesId' => $promorulesId,
		// 	'bonusAmount' => $playerBonusAmount,
		// 	'promoCmsSettingId' => $promoCmsSettingId,
		// 	'processedBy' => $adminId,
		// 	'depositAmount' => $depositAmount,
		// 	'withdrawConditionAmount' => $withdrawBetAmtCondition,
		// 	'transactionStatus' => $transactionStatus, //request
		// 	'dateProcessed' => $this->getNowForMysql(),
		// 	'verificationStatus' => self::DB_FALSE,
		// );

		// return $this->insertData($this->tableName, $data);
	}

	// public function approvePromoRequestWithoutBonusRelease($playerId, $promorulesId, $playerBonusAmount,
	// 	$promoCmsSettingId, $adminId = null, $depositAmount = null, $withdrawBetAmtCondition = null) {
	// 	$this->load->model(array('group_level'));
	// 	//update level id too
	// 	$level_id = $this->group_level->getPlayerLevelId($playerId);

	// 	$result = $this->getPlayerPromoIdByPlayerIdAndPromoCmsSettingId($playerId, $promoCmsSettingId);
	// 	if ($result) {
	// 		$playerPromoId = @$result->playerpromoId ?: null;
	// 	}

	// 	//approved
	// 	$data = array('playerId' => $playerId,
	// 		'level_id' => $level_id,
	// 		'promorulesId' => $promorulesId,
	// 		'bonusAmount' => $playerBonusAmount,
	// 		'promoCmsSettingId' => $promoCmsSettingId,
	// 		'processedBy' => $adminId,
	// 		'depositAmount' => $depositAmount,
	// 		'withdrawConditionAmount' => $withdrawBetAmtCondition,
	// 		'transactionStatus' => self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS,
	// 		'verificationStatus' => self::DB_FALSE,
	// 	);
	// 	$whereArr = array("playerpromoId" => $playerPromoId);
	// 	return $this->editData($this->tableName, $whereArr, $data);
	// }

	// public function processPromoApplication($data) {
	// 	$this->db->where('playerpromoId', $data['playerpromoId']);
	// 	$this->db->update('playerpromo', $data);
	// }

	/**
	 * overview : count status of promo application
	 *
	 * @return array
	 */
	public function countStatusOfPromoApplication() {
		$this->db->select('count(playerpromoId) as cnt, transactionStatus', false)->from('playerpromo')
		// ->where('promoStatus', self::PROMO_STATUS_ACTIVE)
			->group_by('transactionStatus');

		$pendingCnt = 0;
		$approvedCnt = 0;
		$declinedCnt = 0;

		$rows = $this->runMultipleRow();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				switch ($row->transactionStatus) {
				case self::TRANS_STATUS_REQUEST:
					$pendingCnt += $row->cnt;
					break;
				// case self::TRANS_STATUS_MANUAL_REQUEST_PENDING:
				// 	$pendingCnt += $row->cnt;
				// 	break;
				case self::TRANS_STATUS_APPROVED:
					$approvedCnt += $row->cnt;
					break;
				case self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS:
					$approvedCnt += $row->cnt;
					break;
				case self::TRANS_STATUS_DECLINED:
					$declinedCnt += $row->cnt;
					break;
				}
			}
		}

		return array($pendingCnt, $approvedCnt, $declinedCnt);

	}

	/**
	 * overview : get all promo application
	 *
	 * @param int $transactionStatus
	 * @return array|bool
	 */
	public function getAllPromoApplication($transactionStatus = self::TRANS_STATUS_REQUEST) {

		// playerpromo.dateApply,
		// playerpromo.dateCancelled,
		// playerpromo.dateCancelDeclined,
		// playerpromo.cancelRequestDate,
		// playerpromo.cancelRequestStatus,
		// playerpromo.declinedCancelReason,

		$this->db->select('playerpromo.playerpromoId,
						   playerpromo.promoStatus,
						   playerpromo.transactionStatus,
						   playerpromo.bonusAmount,
						   playerpromo.dateApply,
						   playerpromo.dateProcessed,
						   playerpromo.declinedApplicationReason,
						   playerpromo.verificationStatus,
						   adminusers.username AS processedBy,
						   player.username,
						   player.playerId,
						   promorules.promoName,
						   promorules.nonDepositPromoType,
						   promorules.bonusReleaseToPlayer,
						   promorules.promorulesId,
						   promorules.promoType,
						   promocmssetting.promoCmsSettingId,
						   promocmssetting.promoName as promoTitle,
						   vipsettingcashbackrule.vipLevelName,
						   vipsettingcashbackrule.vipLevel,
						   vipsetting.groupName
						   ')->from('playerpromo');
		$this->db->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left');
		$this->db->join('player', 'player.playerId = playerpromo.playerId', 'left');
		$this->db->join('playerlevel', 'playerlevel.playerId = player.playerId', 'left');
		$this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerlevel.playerGroupId', 'left');
		$this->db->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->join('promocmssetting', 'promocmssetting.promoCmsSettingId = playerpromo.promoCmsSettingId', 'left');
		$this->db->join('adminusers', 'adminusers.userId = playerpromo.processedBy', 'left');
		$this->db->where('playerpromo.transactionStatus', $transactionStatus);
		// $this->db->where('playerpromo.promoStatus', self::PROMO_STATUS_ACTIVE);
		// if ($cancelStatus) {
		// 	// $requestStatus = array(1);
		// 	$this->db->where('playerpromo.transactionStatus', Player_promo::TRANS_STATUS_DECLINED);
		// } else {
		// 	$this->db->where('playerpromo.transactionStatus', Player_promo::TRANS_STATUS_REQUEST);
		// }
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				// $row['dateApply'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dateApply']));
				// if ($row['dateProcessed']) {
				// 	$row['dateProcessed'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dateProcessed']));
				// }
				// if ($row['dateCancelled']) {
				// 	$row['dateCancelled'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dateCancelled']));
				// }
				// if ($row['cancelRequestDate']) {
				// 	$row['cancelRequestDate'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['cancelRequestDate']));
				// }
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * overview : get player promo
	 *
	 * @param int	$playerPromoId
	 * @return array
	 */
	public function getPlayerPromo($playerPromoId) {
		$this->db->from($this->tableName)->where('playerpromoId', $playerPromoId);

		return $this->runOneRow();
	}

	/**
	 * overview : get player promo id by player and promo settings id
	 *
	 * @param int	$playerId
	 * @param int	$promoCmsSettingId
	 * @return array
	 */
	public function getPlayerPromoIdByPlayerIdAndPromoCmsSettingId($playerId, $promoCmsSettingId) {
		$this->db->select('playerpromoId');
		$where = array("playerId" => $playerId, "promoCmsSettingId" => $promoCmsSettingId);
		$this->db->from($this->tableName)->where($where);
		return $this->runOneRow();
	}

	/**
	 * overview : get last approved promo date time
	 *
	 * @param int	$playerId
	 * @param int	$promorulesId
	 * @return string
	 */
	public function getLastApprovedPromoDatetime($playerId, $promorulesId) {
		$this->db->from('playerpromo')->where('playerId', $playerId)->where('promorulesId', $promorulesId)
		// ->where('promoStatus', self::PROMO_STATUS_ACTIVE)
			->where('transactionStatus', self::TRANS_STATUS_APPROVED)
			->order_by('dateProcessed', 'desc');
		$this->limitOneRow();
		return $this->runOneRowOneField('dateProcessed');
	}

	/**
	 * overview : total bonus amount
	 *
	 * @param int	$playerId
	 * @param int	$promorulesId
	 * @param date	$from_datetime
	 * @param date  $to_datetime
	 * @return int|null
	 */
	public function sumBonusAmount($playerId, $promorulesId, $from_datetime, $to_datetime) {
		$sum_amount = 0;
		//load player promo id
		$this->db->from('playerpromo')
			->where('playerId', $playerId)->where('promorulesId', $promorulesId)
		// ->where('promoStatus', self::PROMO_STATUS_ACTIVE)
			->where_in('transactionStatus', [self::TRANS_STATUS_APPROVED,
				self::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION,
				self::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS]);

		$playerPromoIdArr = array();
		$rows = $this->runMultipleRow();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$playerPromoIdArr[] = $row->playerpromoId;
			}
		}

		if (!empty($playerPromoIdArr)) {
			$this->load->model(array('transactions'));

			$this->db->select('sum(amount) as sum_amount', false)->from('transactions')
				->where('transaction_type', Transactions::ADD_BONUS)
				->where('to_type', Transactions::PLAYER)
				->where('to_id', $playerId)
				->where_in('player_promo_id', $playerPromoIdArr)
				->where('created_at >=', $from_datetime)
				->where('created_at <=', $to_datetime)
				->where('status', Transactions::APPROVED);

			// $this->addWhereApproved();
			$sum_amount = $this->runOneRowOneField('sum_amount');
		}
		return $sum_amount;
	}

	/**
	 * overview : get player promo ids
	 *
	 * @param int	$playerId
	 * @param int	$promorulesId
	 * @return array
	 */
	public function getPlayerPromoIds($playerId, $promorulesId) {
		//var_dump($playerId,$promorulesId,$transactionStatus);exit();
		$this->db->select('playerpromo.playerpromoId')
			->from('playerpromo')
			->where('playerpromo.playerId', $playerId)
			->where('playerpromo.promorulesId', $promorulesId)
		// ->where('playerpromo.promoStatus', self::PROMO_STATUS_ACTIVE)
			->where('playerpromo.transactionStatus', self::TRANS_STATUS_APPROVED); //1=approved or 0=request status
		// $this->db->order_by('playerpromo.playerpromoId', 'asc');
		// $query = $this->db->get();
		// //var_dump($query->num_rows());exit();
		// return $query->num_rows();
		$rows = $this->runMultipleRow();
		return $this->convertRowsToArray($rows, 'playerpromoId');
	}

	/**
	 * overview : get player promo history limit
	 *
	 * @param int	$player_id
	 * @param int	$search
	 * @return array
	 */
	public function getPlayerPromoHistoryWLimit($player_id, $search, $limit = null, $offset = null, $is_count = false) {

		$this->load->model(['promorules']);

		if ($is_count) {
			$this->db->select('count(playerpromo.playerpromoId) as cnt');
		} else {
			$this->db->select(array(
				'playerpromo.*',
				'promocmssetting.promoName',
			));
		}

		if (isset($search['from'], $search['to'])) {
			$this->db->where("playerpromo.dateApply >=", $search['from']);
			$this->db->where("playerpromo.dateApply <=", $search['to']);
		}

		if (isset($limit, $offset)) {
			$this->db->limit($limit, $offset);
		}

		$this->db->from('playerpromo');

		$this->db->join('promocmssetting', 'promocmssetting.promoCmsSettingId = playerpromo.promoCmsSettingId', 'left');
		$this->db->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left');
		$this->db->where('playerpromo.playerId', $player_id);
		$this->db->order_by('playerpromo.dateProcessed', 'desc');
		//$this->db->where('promocmssetting.promoName', Promorules::SYSTEM_MANUAL_PROMO_CMS_NAME);

		$query = $this->db->get();

		if($is_count) {
			return $this->getOneRowOneField($query, 'cnt');
		} else {
			return $query->result_array();
		}

	}

	public function getPlayerPromoHistoryWInput($player_id, $search) {
		$params = [$player_id];
		$where = '';
		if ($search['dateRangeValueStart'] != null) {
			$where = " AND playerpromo.dateApply BETWEEN ? AND ?";
			$params[] = $search['dateRangeValueStart'];
			$params[] = $search['dateRangeValueEnd'];
		}
		if (isset($search["flag"])) {
			if ($search["flag"] != "") {
				$where .= " AND playerpromo.transactionStatus = ? ";
				$params[] = $search["flag"];
			}
		}

		$query = $this->db->query("SELECT
			playerpromo.dateApply,
			playerpromo.dateProcessed,
			promocmssetting.promoName,
			promorules.bonusAmount,
			playerpromo.transactionStatus,
			playerpromo.cancelRequestStatus,
			playerpromo.declinedCancelReason,
			playerpromo.declinedApplicationReason
		FROM playerpromo
		JOIN promocmssetting ON promocmssetting.promoCmsSettingId = playerpromo.promoCmsSettingId
		JOIN promorules ON promorules.promorulesId = playerpromo.promorulesId
		WHERE playerpromo.playerId = ?
		$where
		", $params);
		return $query->result_array();
	}

	/**
	 * Get details of player's active promo
	 * @param  int			$playerId                == player.playerId
	 * @param  int			$limit                   limit of returned row count, for paging
	 * @param  int			$offset                  offset of returned rows, for paging
	 * @param  int			$total_rec               if true, return count of returned rows
	 * @param  datestring	$time_start              start time
	 * @param  datestring	$time_end                end time
	 * @param  boolean		$include_declined_promos if true, include declined promos
	 * @return array 		array of promo records
	 */
	public function getPlayerActivePromoDetails($playerId, $limit = '', $offset = '', $total_rec = '', $time_start = null, $time_end = null, $include_declined_promos = false) {
		$this->utils->debug_log('playerId', $playerId);
		$this->load->model(array('transactions', 'promorules', 'game_logs', 'cms_model'));

		$this->db->distinct()->select('promorules.promorulesId,
						   promorules.promoName,
						   promorules.promoCategory,
						   promorules.hide_date as hideDate,
						   promotype.promoTypeName,
						   promocmssetting.promo_code as promoCode,
						   promocmssetting.promoCmsSettingId,
						   promocmssetting.promoName as promoCmsTitle,
						   promocmssetting.promoDescription,
						   promocmssetting.promoDetails,
						   promocmssetting.tag_as_new_flag,
						   promocmssetting.promoThumbnail,
						   promocmssetting.is_default_banner_flag,
						   playerpromo.playerpromoId,
						   playerpromo.bonusAmount,
						   playerpromo.dateProcessed,
						   playerpromo.depositAmount,
						   playerpromo.level_id,
						   playerpromo.transactionStatus,
						   playerpromo.withdrawConditionAmount,
						   playerpromo.dateApply,
						   vipsettingcashbackrule.vipLevelName,
						   player.username,
						   player.playerId,
						   withdraw_conditions.started_at
						   ')
			->from('playerpromo')
			->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left')
			->join('promotype', 'promotype.promotypeId = promorules.promoCategory', 'left')
			->join('promocmssetting', 'playerpromo.promoCmsSettingId = promocmssetting.promoCmsSettingId', 'left')
			->join('player', 'player.playerId = playerpromo.playerId', 'left')
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = playerpromo.level_id', 'left')
			->join('transactions', 'playerpromo.playerpromoId = transactions.player_promo_id', 'left')
			->join('withdraw_conditions', 'withdraw_conditions.player_promo_id = playerpromo.playerpromoId', 'left')
		// ->join('promotype as transPromotype', 'transPromotype.promotypeId = transactions.promo_category', 'left')
		;
		$this->db->where('playerpromo.playerId', $playerId);
		// $this->db->where('playerpromo.promoStatus', 0); //0-active
		$this->db->where('playerpromo.transactionStatus !=', self::TRANS_STATUS_DECLINED_FOREVER); //approved status

		if (!$include_declined_promos) {
			$this->db->where('playerpromo.transactionStatus !=', self::TRANS_STATUS_DECLINED); //declined status
		}
		$this->db->where('playerpromo.transactionStatus !=', self::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION); //finish status
		$this->db->where('promocmssetting.promoCmsSettingId IS NOT NULL');
		$this->db->order_by('playerpromo.dateProcessed', 'desc');

		if (!empty($limit) && !$total_rec) {
			$this->db->limit($limit, $offset);
		}

		if (!empty($time_start) && !empty($time_end)) {
			$this->db->where("playerpromo.dateProcessed BETWEEN '{$time_start}' AND '{$time_end}'", null, false);
		}

		$query = $this->db->get();
		$this->utils->printLastSQL();
		// $this->utils->debug_log($this->db->last_query());

		if ($query->num_rows() > 0) {
			$gameDescIdArr = $this->group_level->getAllowedGameIdArr($playerId);

			foreach ($query->result_array() as $row) {
				//check if deposit promo and non deposit promo (email,registration,mobile)
				// if (($row['promoType'] == 1 && $row['nonDepositPromoType'] < 4) || ($row['promoType'] == 0)) {
				// 	$row['currentBet'] = $this->getPlayerCurrentBet($row['username'], $row['dateProcessed'], $row['promorulesId'], $row['playerId']);
				// } else {
				// 	$row['currentBet'] = $this->getPlayerCurrentBet($row['username'], $row['dateProcessed']);
				// }

				// $row['dateProcessed'] = mdate('%M %d, %Y %h:%i:%s %A', strtotime($row['dateProcessed']));
				$row['promoName'] == null ? $row['promoName'] = '' : $row['promoName'];
				// $row['promoCode'] == empty ? $row['promo_code'] = '' : $row['promo_code'];
				//change

				if ($row['promorulesId']) {
					$this->load->model(array('promorules'));
					$gameDescIdArr = $this->promorules->getPlayerGames($row['promorulesId']);
				}

				$row['currentBet'] = $this->game_logs->totalPlayerBettingAmountWithLimitByVIP($row['playerId'], $row['started_at'], null, $gameDescIdArr) ?: 0;

				list($row['promoName'], $row['promoType'], $row['promoDetails']) = $this->promorules->getPromoNameAndType(
					'', $row['promoTypeName'], $row['promoTypeName'],
					$row['promoName'], $row['promoDetails'], $row['vipLevelName']);

				$row['fullPromoDesc'] = implode(' - ', array($row['promoType'], $row['promoName']));
                $row['promoThumbnailPath'] = $this->utils->getPromoThumbnailRelativePath();
                $row['promoDetails'] = $this->cms_model->decodePromoDetailItem($row['promoDetails']);

				if ($row['transactionStatus'] == Player_promo::TRANS_STATUS_APPROVED) {
					$row['status'] = lang('APPLIED');
				} elseif ($row['transactionStatus'] == Player_promo::TRANS_STATUS_REQUEST) {
					$row['status'] = lang('PENDING');
				} elseif ($row['transactionStatus'] == Player_promo::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS) {
					$row['status'] = lang('ACTIVE');
				} elseif ($row['transactionStatus'] == Player_promo::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS) {
					$row['status'] = lang('LOCKED BONUS');
				} elseif ($row['transactionStatus'] == Player_promo::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION) {
					$row['status'] = lang('FINISHED');
				} elseif ($row['transactionStatus'] == Player_promo::TRANS_STATUS_DECLINED) {
					$row['status'] = lang('DECLINED');
				} else {
					$row['status'] = lang('N/A');
				}

				$data[] = $row;
			}
			//var_dump($data);exit();
			// log_message('error', var_export($data, true));

			if (!empty($total_rec)) {
				return count($data);
			}

			return $data;
		}
		return false;
	}

	/**
	 * overview : get player current bet
	 *
	 * @param string $playerName
	 * @param date	 $dateJoined
	 * @param int	 $promoId
	 * @param strin	 $playerId
	 * @return array
	 */
	public function getPlayerCurrentBet($playerName, $dateJoined, $promoId = null, $playerId = null) {
		if ($promoId) {
			$playerGames = $this->getPlayerGames($promoId);

			$this->db->select_sum('betting_amount', 'totalBetAmount');
			$this->db->from('total_player_game_day');
			$this->db->where('date >=', date('Y-m-d', strtotime($dateJoined)));
			$this->db->where('date <=', date('Y-m-d'));
			$this->db->where('player_id', $playerId);
			if ($playerGames) {
				$this->db->where_in('game_description_id', $playerGames);
			}

			$qry = $this->db->get();
			if ($qry->num_rows() > 0) {
				return $qry->result();
			}
		} else {
			$this->db->select('SUM(betting_amount) as totalBetAmount')->from('total_player_game_day');
			$this->db->where('date >=', $dateJoined);
			$this->db->where('date <=', date('Y-m-d H:i:s'));
			$this->db->where('player_id', $playerId);
			$query = $this->db->get();
			if ($query->num_rows() > 0) {
				foreach ($query->result_array() as $row) {
					$row['totalBetAmount'] == null ? $row['totalBetAmount'] = 0 : $row['totalBetAmount'] = $row['totalBetAmount'];
					$data[] = $row;
				}
			}
			return $data;
		}

	}

	// private function getPlayerGames($promoId) {
	// 	$this->db->select('game_description_id')->from('promorulesgamebetrule');
	// 	$this->db->where('promoruleId', $promoId);
	// 	$qry = $this->db->get();
	// 	if ($qry && $qry->num_rows() > 0) {
	// 		foreach ($qry->result_array() as $row) {
	// 			$data[] = $row['game_description_id'];
	// 		}
	// 		//var_dump($data);exit();
	// 		return $data;
	// 	}

	// 	return false;
	// }

	/**
	 * overview : count player promo
	 *
	 * @param int	$playerId
	 * @param int	$promorulesId
	 * @param date	$fromDatetime
	 * @param date	$toDatetime
	 * @param array $transactionStatus
	 * @return int|null
	 */
	public function countPlayerPromo($playerId, $promorulesId, $fromDatetime, $toDatetime,
		$transactionStatus = [self::TRANS_STATUS_APPROVED, self::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION]) {
		//var_dump($playerId,$promorulesId,$transactionStatus);exit();

		$this->utils->debug_log('countPlayerPromo playerid', $playerId, 'promorulesId', $promorulesId, 'fromDatetime', $fromDatetime, 'toDatetime', $toDatetime);

		$this->db->select('count(playerpromo.playerpromoId) as cnt', false)->from('playerpromo');
		$this->db->where('playerpromo.playerId', $playerId)
			->where('playerpromo.promorulesId', $promorulesId);
		// ->where('playerpromo.promoStatus', self::PROMO_STATUS_ACTIVE)
		if (is_array($transactionStatus)) {
			$this->db->where_in('playerpromo.transactionStatus', $transactionStatus); //1=approved or 0=request status
		} else {
			$this->db->where('playerpromo.transactionStatus', $transactionStatus); //1=approved or 0=request status
		}
		// $this->db->order_by('playerpromo.playerpromoId', 'asc');
		// $query = $this->db->get();
		// //var_dump($query->num_rows());exit();
		// return $query->num_rows();
		if ($fromDatetime && $toDatetime) {
			$this->db->where('playerpromo.dateProcessed >=', $fromDatetime)
				->where('playerpromo.dateProcessed <=', $toDatetime);
		}

		$cnt = $this->runOneRowOneField('cnt');

		// $this->utils->printLastSQL();

		return $cnt ? $cnt : 0;
	}

	/**
	 * overview : exists promotions
	 *
	 * @param array	$playerIdArr
	 * @param int	$promorulesId
	 * @param int $transactionStatus
	 * @return int|null
	 */
	public function existsPromotion($playerIdArr, $promorulesId, $transactionStatus = self::TRANS_STATUS_APPROVED) {
		$this->db->select('count(playerpromo.playerpromoId) as cnt', false)
			->from('playerpromo')
			->where_in('playerpromo.playerId', $playerIdArr)
			->where('playerpromo.promorulesId', $promorulesId)
		// ->where('playerpromo.promoStatus', self::PROMO_STATUS_ACTIVE)
			->where('playerpromo.transactionStatus', $transactionStatus); //1=approved or 0=request status

		$cnt = $this->runOneRowOneField('cnt');

		// $this->utils->printLastSQL();

		return $cnt ? $cnt : 0;
	}

	/**
	 * overview : get times promotion
	 *
	 * @param $frequency
	 * @param $today
	 * @param $playerId
	 * @param $promorulesId
	 * @return null
	 */
	public function getTimesPromotion($frequency, $today, $playerId, $promorulesId) {
		$fromDate = $today . ' 00:00:00';
		$toDate = $today . ' 23:59:59';
		switch ($frequency) {
		case self::FREQUENCRY_ALL:
			$fromDate = null;
			$toDate = null;
			break;
		case self::FREQUENCRY_WEEKLY:
			list($fromDate, $toDate) = $this->utils->getFromToByWeek($today);
			$fromDate = $fromDate . ' 00:00:00';
			$toDate = $toDate . ' 23:59:59';
			break;

		case self::FREQUENCRY_MONTHLY:
			list($fromDate, $toDate) = $this->utils->getFromToByMonth($today);
			$fromDate = $fromDate . ' 00:00:00';
			$toDate = $toDate . ' 23:59:59';
			break;
		}

		$this->db->select('count(*) as cnt', null, false)->from('playerpromo')->where('playerId', $playerId)
			->where('promorulesId', $promorulesId)
			->where('transactionStatus', self::TRANS_STATUS_APPROVED)
			->where('dateProcessed >=', $fromDate)
			->where('dateProcessed <=', $toDate);

		return $this->runOneRowOneField('cnt');

	}

	/**
     * Deprecated by curtis
	 * overview : decline forever player promostatusToNamestatusToName
	 *
	 * @param int	$playerPromoId
	 * @param int	$adminUserId
	 * @return bool
	 */
	public function declinedForeverPlayerPromo($playerPromoId, $adminUserId) {
		/*
	    $this->db->set('transactionStatus', self::TRANS_STATUS_DECLINED_FOREVER)
			->set('processedBy', $adminUserId)
			->set('dateProcessed', $this->utils->getNowForMysql())

			->where('playerpromoId', $playerPromoId)
			->where('transactionStatus', self::TRANS_STATUS_DECLINED);

		return $this->runAnyUpdate('playerpromo');
		*/
	}

	/**
	 * overview : check if decline result
	 * @param $playerId
	 * @param $promorulesId
	 * @return bool
	 */
	public function isDeclinedForever($playerId, $promorulesId) {

		$this->db->from('playerpromo')->where('transactionStatus', self::TRANS_STATUS_DECLINED_FOREVER)
			->where('playerId', $playerId)
			->where('promorulesId', $promorulesId);

		return $this->runExistsResult();
	}

	/**
	 * overview : get available player promo list
	 *
	 * @param int	$playerId
	 * @param int $promoType
	 * @return array
	 */
	public function getAvailPlayerPromoList($playerId, $promoType = null) {
		$params = [$playerId, self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS];
		if ($promoType !== null) {
			$qry = " and promorules.promoType=? ";
			$params[] = $promoType;
		}

		$sql = <<<EOD
select playerpromo.playerpromoId, promocmssetting.promoName as description, promocmssetting.promoDescription

from playerpromo join promorules on playerpromo.promorulesId=promorules.promorulesId
join promocmssetting on playerpromo.promoCmsSettingId=promocmssetting.promoCmsSettingId
where playerpromo.playerId=?
and playerpromo.transactionStatus=?
{$qry}

EOD;

		return $this->runRawSelectSQLArray($sql, $params);
	}

	/**
	 * overview : check if available player promo exist
	 *
	 * @param int	$player_promo_id
	 * @return bool
	 */
	public function existsAvailPlayerPromo($player_promo_id) {
		$success = false;
		if (!empty($player_promo_id)) {
			$this->db->from('playerpromo')->where('playerpromoId', $player_promo_id)
				->where('transactionStatus', self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS);

			$success = $this->runExistsResult();
		}

		return $success;
	}

	/**
	 * overview : get available player by promo id
	 *
	 * @param int	 $playerPromoId
	 * @param double $depositAmount
	 * @param bool|false $updateBonusAmount
	 * @return array
	 */
	public function getAvailPlayerPromoById($playerPromoId, $depositAmount = null, $updateBonusAmount = false) {
		$params = [$playerPromoId, self::TRANS_STATUS_REQUEST, self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS];

		$sql = <<<EOD
select playerpromo.playerpromoId, promocmssetting.promoName, promocmssetting.promoDescription,
playerpromo.bonusAmount, playerpromo.withdrawConditionAmount, playerpromo.promorulesId, playerpromo.playerId,
playerpromo.promoCmsSettingId, promorules.releaseToSubWallet

from playerpromo join promorules on playerpromo.promorulesId=promorules.promorulesId
join promocmssetting on playerpromo.promoCmsSettingId=promocmssetting.promoCmsSettingId
where playerpromo.playerpromoId=?
and playerpromo.transactionStatus in (?,?)
limit 1

EOD;

		$rows = $this->runRawSelectSQLArray($sql, $params);
		$row = null;
		if (!empty($rows)) {
			$this->load->model(['wallet_model']);
			$row = $rows[0];
			if ($depositAmount !== null) {
				//calc bonusAmount and withdraw condition
				$this->load->model(['promorules']);
				$errorMessageLang = null;
				list($bonusAmount, $withdrawConditionAmount) = $this->promorules->calcBonusAmountAndWithdrawCondition($row['promorulesId'], $row['playerId'], $depositAmount, $errorMessageLang);
				$row['bonusAmount'] = $bonusAmount;
				$row['withdrawConditionAmount'] = $withdrawConditionAmount;

				if ($updateBonusAmount) {
					$this->db->where('playerpromoId', $playerPromoId)->update('playerpromo', [
						'bonusAmount' => $bonusAmount, 'withdrawConditionAmount' => $withdrawConditionAmount]);
				}
			}
			//convert wallet
			$row['release_to_wallet'] = $this->wallet_model->translateWallet($row['releaseToSubWallet']);
		}

		return $row;
	}

	/**
	 * overview : get release subwallet promo
	 *
	 * @param int	$playerId
	 * @param int	$subwalletId
	 * @return null
	 */
	public function getReleasedSubwalletPromo($playerId, $subwalletId) {

		//released
		$this->db->from('playerpromo')->where('playerId', $playerId)
			->where('triggered_subwallet_id', $subwalletId)
			->where('transactionStatus', self::TRANS_STATUS_APPROVED);

		return $this->runMultipleRowArray();

	}

	/**
	 * overview : get pending sub wallet promo
	 *
	 * @param int	$playerId
	 * @param int	$subwalletId
	 * @return array
	 */
	public function getPendingSubwalletPromo($playerId, $subwalletId) {
		//only pending and not release
		$this->db->from('playerpromo')->where('playerId', $playerId)
			->where('triggered_subwallet_id', $subwalletId)
			->where('transactionStatus', self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS);

		return $this->runMultipleRowArray();
	}

	/**
	 * overview : check waiting release
	 * @param $playerId
	 * @param $promorulesId
	 * @return bool
	 */
	public function isWaitingRelease($playerId, $promorulesId) {
		$this->db->from('playerpromo')->where('playerId', $playerId)->where('promorulesId', $promorulesId)
			->where('transactionStatus', self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS);

		return $this->runExistsResult();
	}

	/**
	 * overview : get available approved without release promo list
	 *
	 * @param $player_id
	 * @return array
	 */
	public function getAvailApprovedWithoutReleasePromoList($player_id) {
		$this->db->select('playerpromo.playerpromoId, promocmssetting.promoName, promocmssetting.promoDescription')
			->from('playerpromo')
			->join('promocmssetting', 'playerpromo.promoCmsSettingId=promocmssetting.promoCmsSettingId')
			->where('playerId', $player_id)
			->where('transactionStatus', self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS);

		return $this->runMultipleRowArray();
	}

	/**
	 * overview : get promo rule by id
	 *
	 * @param int	$playerPromoId
	 * @return array
	 */
	public function getPromoruleBy($playerPromoId) {
		$this->db->from('playerpromo')->where('playerPromoId', $playerPromoId);
		$promorulesId = $this->runOneRowOneField('promorulesId');
		$promorule = null;
		if (!empty($promorulesId)) {
			$this->load->model(['promorules']);
			$this->db->select('promorules.*, promocmssetting.promoCmsSettingId')->from('promorules')
				->join('promocmssetting', 'promorules.promorulesId=promocmssetting.promoId')
				->where_in('promorulesId', $promorulesId);
			// $promorule=$this->promorules->getPromorule($promorulesId);
			$promorule = $this->runOneRowArray();
		}

		return $promorule;
	}

	/**
	 * overview : check if unfinished promo exist
	 *
	 * @param int	$player_id
	 * @return bool
	 */
	public function existsUnfinishedPromoAndDonotAllowOthers($player_id) {
		$this->db->select('playerpromo.playerpromoId')
			->from('playerpromo')
			->join('promorules', 'playerpromo.promorulesId=promorules.promorulesId')
			->where('playerpromo.playerId', $player_id)
			->where_in('transactionStatus', [self::TRANS_STATUS_APPROVED, self::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS])
			->where('promorules.donot_allow_other_promotion', '1');

		return $this->runExistsResult();
	}

	public function existsPlayerPromoFromSameIp($promorulesId, $ip) {
		$this->db->select('playerpromo.playerpromoId')
			->from('playerpromo')
			->where('promorulesId', $promorulesId)
			->where_not_in('transactionStatus', [self::TRANS_STATUS_EXPIRED])
            ->where('login_ip', $ip)
        ;

		return $this->runExistsResult();
	}

	public function getPlayerPromoStatusById($playerPromoId){
        $this->db->select('transactionStatus')
                 ->where('playerpromoId',$playerPromoId)
                 ->from($this->tableName);
        $query = $this->db->get();
        $row = $query->row();

        $status = (int)$row->transactionStatus;
        return $status;
    }

	/**
	 * overview : get promo pre deposit promo list
	 *
	 * @param int	$player_id
	 * @return null
	 */
	public function getApprovedPreDepositPromoList($player_id) {
		$this->load->model(['promorules']);

		//approved pre, auto release, show on active , deposit
		$this->db->select('playerpromo.playerpromoId, promocmssetting.promoName, promocmssetting.promoDescription, promorules.trigger_wallets')
			->from('playerpromo')
			->join('promocmssetting', 'playerpromo.promoCmsSettingId=promocmssetting.promoCmsSettingId')
			->join('promorules', 'playerpromo.promorulesId=promorules.promorulesId')
			->where('playerpromo.playerId', $player_id)
			->where('promorules.promoType', Promorules::PROMO_TYPE_DEPOSIT)
		//approved pre
			->where('transactionStatus', self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS);

		return $this->runMultipleRowArray();
	}

	/**
	 * @deprecated
	 */
	public function getApprovedPreDepositAndOnlySubPromoList($player_id) {
		$this->load->model(['promorules']);

		//approved pre, auto release, only sub , deposit
		$this->db->select('playerpromo.playerpromoId, promocmssetting.promoName, promocmssetting.promoDescription')
			->from('playerpromo')
			->join('promocmssetting', 'playerpromo.promoCmsSettingId=promocmssetting.promoCmsSettingId')
			->join('promorules', 'playerpromo.promorulesId=promorules.promorulesId')
			->where('playerpromo.playerId', $player_id)
			->where('promorules.promoType', Promorules::PROMO_TYPE_DEPOSIT)
		//approved pre
			->where('transactionStatus', self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS)
			->where('promorules.trigger_wallets is not null', null, false); //subwallet available

		return $this->runMultipleRowArray();
	}

	/**
	 * overview : get approved player promo
	 *
	 * @param int	$playerId
	 * @param int	$promorulesId
	 * @return string
	 */
	public function getApprovedPlayerPromo($playerId, $promorulesId) {
		$playerPromoId = null;
		$this->load->model(['player_promo']);
		//search approved pre application
		// $promorulesId
		$this->db->select('playerpromoId')->from('playerpromo')
			->where('playerpromo.promorulesId', $promorulesId)
			->where('playerpromo.transactionStatus', Player_promo::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS)
			->where('playerpromo.playerId', $playerId);

		// $this->utils->printLastSQL();

		return $this->runOneRowOneField('playerpromoId');
		// if(!empty($row)){
		//     $playerPromoId=$row['playerpromoId'];
		// }
		// return $playerPromoId;
	}

	public function getDisableCashbackPlayers() {
		$this->db->distinct()->select('playerpromo.playerId, playerpromo.dateProcessed')
			->from('promorules')
			->join('playerpromo', 'playerpromo.promorulesId=promorules.promorulesId')
			->join('player', 'player.playerId=playerpromo.playerId')
			->where('promorules.disable_cashback_if_not_finish_withdraw_condition', '1')
			->where('player.disabled_cashback', self::DB_FALSE)
			->where('playerpromo.', Player_promo::TRANS_STATUS_APPROVED);

		return $this->runMultipleRowArray();
	}

    public function getDailyPromoRequestByPlayerId($playerId, $promocmssettingId) {
        $this->db->select('COUNT(1) AS request_count')
            ->from('playerpromo')
            ->join('promorules', 'promorules.promorulesId=playerpromo.promorulesId')
            ->where('playerpromo.promoCmsSettingId', $promocmssettingId)
            ->where('playerpromo.playerId', $playerId)
            ->where("DATE_FORMAT(playerpromo.dateApply, '%Y-%m-%d') = '" . date('Y-m-d', time()) . "'");

        return $this->runOneRowOneField('request_count');

    }

    public function getDailyPromoApproved($promorulesId) {
        $this->db->select('COUNT(playerpromoId) AS approved_count')
            ->from('playerpromo')
            ->join('promorules', 'promorules.promorulesId=playerpromo.promorulesId')
            ->where('playerpromo.promorulesId', $promorulesId)
            ->where_in('transactionStatus', [
                self::TRANS_STATUS_APPROVED,
                self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS,
                self::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION,
                self::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS,
            ])
            ->where("DATE_FORMAT(playerpromo.dateApply, '%Y-%m-%d') = '" . date('Y-m-d', time()) . "'")
        ;

        return $this->runOneRowOneField('approved_count');
    }

    public function getTotalPromoApproved($promorulesId) {
        $this->db->select('COUNT(playerpromoId) AS total_approved_count')
            ->from('playerpromo')
            ->join('promorules', 'promorules.promorulesId=playerpromo.promorulesId')
            ->where('playerpromo.promorulesId', $promorulesId)
            ->where_in('transactionStatus', [
                self::TRANS_STATUS_APPROVED,
                self::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS,
                self::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION,
                self::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS,
            ])
        ;

        return $this->runOneRowOneField('total_approved_count');
    }

	public function finishPlayerPromos($playerPromoIds, $reason, &$extra_info = null) {

		$transactionStatus = self::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION;

		$data=['transactionStatus'=>$transactionStatus];
		if(!is_array($playerPromoIds)){
			$playerPromoIds=[$playerPromoIds];
		}
		$this->db->where_in('playerpromoId', $playerPromoIds)->set($data);
		$result=$this->runAnyUpdate('playerpromo');

		if (!empty($reason)) {
			$sql = "update playerpromo set note=concat(ifnull(note,''),' | ',?) where playerpromoId in (".implode(',', $playerPromoIds).")";
			$this->runRawUpdateInsertSQL($sql, [$reason]);
		}

		return $result;
	}

	/**
	 * overview : revoke promo to player
	 *
	 * @param int		$playerId
	 * @param int		$promorulesId
	 * @param double 	$bonusAmount
	 * @param int		$promoCmsSettingId
	 * @param int		$adminId
	 * @param double	$depositAmount
	 * @param int		$withdrawConditionAmount
	 * @param int 		$transactionStatus
	 * @param int	    $betTimes
	 * @param string	$reason
	 * @param int	    $playerPromoId
	 * @return array
	 */
	public function revokePromoToPlayer($playerId, $promorulesId, $bonusAmount,
		$promoCmsSettingId, $adminId = null, $depositAmount = null, $withdrawConditionAmount = null,
		$transactionStatus = self::TRANS_STATUS_REQUEST, $betTimes = null, $reason = null, $playerPromoId = null, $transferConditionAmount = null) {

		return $this->syncPlayerPromo($playerPromoId, $playerId, $promorulesId, $promoCmsSettingId,
			$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes,
			$adminId, $transactionStatus, $reason, $transferConditionAmount);

	}

	/**
	 * Returns promo categories enabled for player center promo manager
	 * @return	array
	 */
	public function getAllPromoType() {
		$this->db->select('promoTypeId as id, promoTypeName as name, promoIcon as icon')->from('promotype')->where('isUseToPromoManager > 0 and deleted = 0')->order_by('promotypeOrder');
		$query = $this->db->get();
		return ($query) ? $query->result_array() : [];
	}

    /**
     * @deprecated marked by curtis , use getPlayerPromoStatusById instead
     */
	public function checkPromoIfUpdated($playerpromoId){
		$this->db->select('transactionStatus')
			->where('playerpromoId',$playerpromoId)
			->from($this->tableName);
		$query = $this->db->get();
		$row = $query->row();
		// return $row->transactionStatus;
		$status = ((int)$row->transactionStatus) > 0;
		return $status;
	}

	public function getLastReleasedPlayerPromo($playerId, $promorulesId, $start=null, $end=null){
		$this->db->from('playerpromo')->where('playerId', $playerId)
			->where('promorulesId', $promorulesId)->where_in('transactionStatus',
				[self::TRANS_STATUS_APPROVED, self::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION, self::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS])
			->order_by('dateProcessed desc')
			->limit(1);

		if(!empty($start)){
			$this->db->where('dateProcessed >=', $start);
		}
		if(!empty($end)){
			$this->db->where('dateProcessed <=', $end);
		}

		return $this->runOneRowArray();

	}

    /**
     * overview : add request by(admin or player) to player promo
     *
     * @param int		$playerPromoId
     * @param int		$adminId
     * @param int	    $playerId
     * @return bool
     */
    public function addPlayerPromoRequestBy($playerPromoId, $adminId=null, $playerId=null)
    {
        $hasUpdated = false;
        if(empty($playerPromoId)){
            $this->utils->debug_log("addPlayerPromoRequestBy playerPromoId[". $playerPromoId ."], adminId[". $adminId ."], playerId[". $playerId ."]", $hasUpdated);
            return $hasUpdated;
        }

        if(!empty($adminId)){
            $adminId = !isset($adminId)? $this->users->getSuperAdminId() : $adminId;
            $data = ['requestAdminId' => $adminId];
        }elseif (!empty($playerId)){
            $data = ['requestPlayerId' => $playerId];
        }

        $hasUpdated = $this->updatePlayerPromo($playerPromoId, $data);
        $this->utils->debug_log("addPlayerPromoRequestBy playerPromoId[". $playerPromoId ."], adminId[". $adminId ."], playerId[". $playerId ."] hasUpdated[" . $hasUpdated . "]", $data);

        return $hasUpdated;
	}
	/**
	 * @deprecated marked by curtis
	 * @param $playerId
	 */
	public function getMaxLimitWithdrawalFromAppliedPromoOnWithdrawal($playerId){

//		//get from player_promo, not finish
//		$this->db->select('promorules.withdrawal_max_limit, promorules.promorulesId, promorules.ignore_withdrawal_max_limit_after_first_deposit, promorules.always_apply_withdrawal_max_limit_when_first_deposit')
//			->from('playerpromo')
//			->join('promorules', 'promorules.promorulesId=playerpromo.promorulesId')
//			->where('playerId', $playerId)
//			->where('promorules.withdrawal_max_limit > 0')
//			->where('finish_max_limit_withdrawal !=', self::DB_TRUE);
//
//		$rows=$this->runMultipleRowArray();
//
//		if(empty($rows)) {
//			return null;
//		}
//
//		foreach ($rows as $row) {
//			//check deposit
//			if($row['ignore_withdrawal_max_limit_after_first_deposit']==self::DB_TRUE){
//				//search deposit
//				if($this->transactions->hasAnyDeposit($playerId)){
//					//ignore this promotion
//					continue;
//				}
//			}else{
//				//always minus
//				return $row['withdrawal_max_limit'];
//			}
//		}
//
//		return null;

	}

	public function existsReleasedBonusByCode($playerId, $exclude_promo_code, $fromDate, $toDate){
		//search released bonus
	}

	/**
	 * Data source for Api::getPlayerPromoHistoryWLimitById()
	 *
	 * @param	int		$player_id	= player.playerId
	 * @param	array 	$request	$_POST received by controller
	 * @param	array 	$args		arguments manually provided if $_POST not available
	 * @see 	Api::getPlayerPromoHistoryWLimitById()
	 *
	 * @return	array 		Dataset for Datatable.js
	 */
    public function getPlayerPromoHistoryWLimitById($player_id, $request) {
		$this->load->model(array('player_promo', 'promorules'));

        $controller = $this;

		if(empty($player_id)) {
			return $this->data_tables->empty_data($request);
		}


		$input = $this->data_tables->extra_search($request);

		$i = 0;

		$where = array();
		$values = array();

		$columns = array(
			array(
				'dt' => $i++,
				'select' => 'playerpromo.dateApply',
				'alias' => 'dateProcessed',
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.playerpromoId',
				'alias' => 'promoId',
				'formatter' => 'defaultFormatter'
			),
			array(
				'dt' => $i++,
				'select' => 'promocmssetting.promoName',
				'alias' => 'promoName',
				'formatter' => function ($d, $row) use ($controller) {
					return $this->player_promo->promoName($d);
				},
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.transactionStatus',
				'alias' => 'transactionStatus',
				'formatter' => function ($d, $row) use ($controller) {
					return $this->player_promo->statusToName($d);
				},
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.bonusAmount',
				'alias' => 'bonusAmount',
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.note',
				'alias' => 'promonotes',
			),
		);

		$table = 'playerpromo';
		$joins = array(
			'promocmssetting' => 'promocmssetting.promoCmsSettingId = playerpromo.promoCmsSettingId',
		);

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {
			$where[] = "playerpromo.dateApply BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}
		if ($player_id) {
			$where[] = "playerpromo.playerId = ? ";
			$values[] = $player_id;

		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

		return $result;
    }

    public function promoTypeName($promorulesId) {
    	$this->db->from('promorules AS R')
    		->join('promotype AS T', 'R.promoCategory = T.promotypeId', 'left')
    		->select('promotypeName')
    		->where([ 'R.promorulesId' => $promorulesId ])
    	;
    	$res = $this->runOneRowOneField('promotypeName');
    	if ($res == Promorules::SYSTEM_MANUAL_PROMO_TYPE_NAME) {
    		$res = lang("promo.{$res}");
    	}
    	else {
    		$res = lang($res);
    	}
    	return $res;
    }

    /**
     * Version of getPlayerPromoHistoryWLimitById() for player center API use
     * OGP-11940
     * @param 	int			$player_id		== player.playerId
     * @param	int			$limit			limit for paging use
     * @param	int			$offset			offset for paging use
     * @param	datestring	$date_from		min datetime of application
     * @param	datestring	$date_to		max datetime of application
     * @param	bool		$return_count	return count of records if true, otherwise data rows
     * @param	bool		$include_declined_promos	exclude declined promos if false (default: true)
     *
     * @see		Comapi_reports::playerActivePromoDetails()
     * @see		Api_common::getPlayerReports()
     * @return	array 	array of selected promo data
     */
    public function getPlayerPromoHistoryWLimitById_api($player_id, $limit = '', $offset = '', $date_from = null, $date_to = null, $return_count = false,  $include_declined_promos = true) {
    	$this->db->from("{$this->tableName} AS P")
    		->join('promocmssetting AS C', 'P.promoCmsSettingId = C.promoCmsSettingId', 'left')
    		->where([ 'P.playerId' => $player_id ])
    		->order_by('P.dateApply', 'desc')
    	;
    	if (!empty($date_from) && !empty($date_to)) {
    		$this->db->where("P.dateApply BETWEEN '{$date_from}' AND '{$date_to}'");
    	}
    	// OGP-15961: limit does not work when offset == 0
    	// if (!empty($limit) && !empty($offset) && !$return_count) {
    	if (!empty($limit) && !$return_count) {
    		$this->db->limit($limit, $offset);
    	}
    	if ($include_declined_promos == false) {
    		$this->db->where([ 'P.transactionStatus !=' => self::TRANS_STATUS_DECLINED ]);
    	}


    	if ($return_count) {
    		$query = $this->db->get();
    		return $query->num_rows();
    	}
    	else {
    		$res = $this->runMultipleRowArray();
    		$this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

    		if (count($res) > 0) {
	    		foreach ($res as & $row) {
	    			$row['promoName'] = $this->promoName($row['promoName']);
	    			$row['promoTypeName'] = $this->promoTypeName($row['promorulesId']);
	    			$row['status'] = $this->statusToName($row['transactionStatus']);
	    		}
	    	}

    		return $res;
    	}
    }

}

/////end of file///////