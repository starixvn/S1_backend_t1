<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 *
 * bank details of player
 *
 * status deleted=2, active=0
 *
 * General behaviors include
 * * delete certain bank information
 * * get bank list
 * * get list of bank by a certain player
 * * get deposit bank list for a certain player
 * * get withdrawal bank list for a certain player
 * * get bank details for a player using bank details id
 * * validate if the bank account if existing when doing update
 * * check bank account number if exists
 * * adding new bank details by withdrawal
 * * checking unique bank account number
 * * get bank information of a certain player
 * * get deposit bank details of a player
 * * get withdrawal bank details of a player
 * * get default deposit bank details of a player
 * * get Withdraw bank details of a player
 * * get default bank detail of a certain player
 * * get all bank details for a certain player
 *
 *	only_allow_duplicate_one_player: one player allow duplicate withdrawal and deposit, but can't allow duplicate number in withdrawal or deposit
 *	only_allow_duplicate_one_player_any: one player allow duplicate number , no matter withdrawal or deposit
 *	not_allow_duplicate_number: don't allow any duplicate bank account number.
 *	allow_any_duplicate_number: allow any duplicate bank account number for any player.
 *
 * @category Payment Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Playerbankdetails extends BaseModel {
	const NUM_CHAR_DISPLAY = 5;

	const STATUS_ACTIVE = '0';
	const STATUS_DELETED = '2';
	//for dwBank field
	const DEPOSIT_BANK = '0';
	const WITHDRAWAL_BANK = '1';
	const REMEMBERED = '1';
	const VERIFIED = '1';

	const ONLY_ALLOW_DUPLICATE_ONE_PLAYER='only_allow_duplicate_one_player';
	const ONLY_ALLOW_DUPLICATE_ONE_PLAYER_ANY='only_allow_duplicate_one_player_any';
    const NOT_ALLOW_DUPLICATE_NUMBER='not_allow_duplicate_number';
    const NOT_ALLOW_DUPLICATE_NUMBER_ON_SAME_BANKTYPE='not_allow_duplicate_number_on_same_banktype';
    const NOT_ALLOW_DUPLICATE_NUMBER_ON_SAME_BANKTYPE_ANY='not_allow_duplicate_number_on_same_banktype_any';
	const ALLOW_ANY_DUPLICATE_NUMBER='allow_any_duplicate_number';

	# Hard-coded bank type in banktype table
	public $BANK_TYPE_ALIPAY;
	public $BANK_TYPE_WECHAT;

	protected $tableName = 'playerbankdetails';
	protected $idField = 'playerBankDetailsId';

	public function __construct() {
		parent::__construct();

		$this->BANK_TYPE_ALIPAY = !empty($this->config->item('BANK_TYPE_ALIPAY')) ? $this->config->item('BANK_TYPE_ALIPAY') : BANK_TYPE_ALIPAY;
		$this->BANK_TYPE_WECHAT = !empty($this->config->item('BANK_TYPE_WECHAT')) ? $this->config->item('BANK_TYPE_WECHAT') : BANK_TYPE_WECHAT;
	}

	/**
	 * detail: delete a certain bank information
	 *
	 * @param int $bank_details_id bank detail id
	 * @return boolean
	 */
	public function deletePlayerBankInfo($bank_details_id) {
		$this->db->where('playerBankDetailsId', $bank_details_id);
		//$this->db->delete('playerbankdetails');

		$this->db->set('bankAccountNumber', 'concat("del_", bankAccountNumber)', false);
		return $this->db->update('playerbankdetails', array(
            // 'bankAccountNumber' => 'concat("del_", bankAccountNumber)',
            'deletedOn' => $this->utils->getNowForMysql(),
            'status' => self::STATUS_DELETED
        ));
	}

	/**
	 * detail: get bank informations filtered by given parameter
	 *
	 * @param array $data
	 * @return boolean or array
	 */
	public function getBankList($data = null) {
		$this->db->select(array('playerbankdetails.*', 'banktype.bankName'));
		$this->db->from('playerbankdetails');
		$this->db->join('banktype', 'playerbankdetails.bankTypeId = banktype.bankTypeId', 'left');
		$this->db->where($data);
		$this->db->order_by('banktype.bankName', 'asc');
		$this->db->order_by('playerbankdetails.bankAccountFullName', 'asc');
		$query = $this->db->get();

		if ( ! $query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * detail: get player active bank types by playerId
	 *
	 * @param int $playerId
	 * @return array
	 */
	public function getBankTypesByPlayerId($playerId, $dwbank) {
		$this->db->select('bankTypeId');
        $this->db->from($this->tableName);
		$this->db->where('playerId', $playerId);
		$this->db->where('dwbank', $dwbank);
		$this->db->where('status', self::STATUS_ACTIVE);

		$lists = $this->runMultipleRow();
		if(!is_null($lists)){
			foreach($lists as $list) {
				$bankTypeIds[] = $list->bankTypeId;
			}
			return $bankTypeIds;
		}

        return NULL;
	}

	public function PlayerApprovedDepositCount($playerId) {
		$this->db->select(array('playerId','approved_deposit_count') );
		$this->db->from('player');
		$this->db->where('playerId', $playerId);
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: get list of bank by a certain player
	 *
	 * @param int $playerId player id
	 * @param array $data
	 * @return boolean
	 */
	public function getPlayerBankList($playerId, $data = array()) {
		return $this->getBankList(array_merge($data, array(
			'playerbankdetails.playerId' => $playerId,
		)));
	}

	/**
	 * detail: get deposit bank list for a certain player
	 *
	 * @param int $playerId player id
	 * @return boolean
	 */
	public function getPlayerDepositBankList($playerId) {
		return $this->getPlayerBankList($playerId, array(
			'playerbankdetails.dwBank' => self::DEPOSIT_BANK,
			'IFNULL(playerbankdetails.status,\'0\') =' => self::STATUS_ACTIVE,
		));
	}

	/**
	 * detail: get withdrawal bank list for a certain player
	 *
	 * @param int $playerId player id
	 * @return boolean
	 */
	public function getPlayerWithdrawalBankList($playerId) {
		return $this->getPlayerBankList($playerId, array(
			'playerbankdetails.dwBank' => self::WITHDRAWAL_BANK,
			'IFNULL(playerbankdetails.status,\'0\') =' => self::STATUS_ACTIVE,
		));
	}

	/**
	 * detail: get bank details for a player using bank details id
	 *
	 * @param int $id bank details id
	 * @return array
	 */
	public function getPlayerBankDetailsById($id) {
		return $this->getOneRowById($id);
	}

	/**
	 * detail: validate if the bank account if existing when doing update
	 *
	 * note: parameters are mandatory to call this method
	 *
	 * @param string $newBankAcctNo
	 * @param string $prevBankAcctNo
	 * @return boolean
	 */
	public function checkBankAcctIsExistOnEdit($newBankAcctNo, $prevBankAcctNo) {

		$sql = "SELECT * FROM playerbankdetails WHERE bankAccountNumber = ? AND bankAccountNumber != ? ";
		$q = $this->db->query($sql, array($newBankAcctNo, $prevBankAcctNo));
		if ($q->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * detail: check bank account number if exists
	 *
	 * note: parameters mandatory to call this method
	 *
	 * @param string $accountNumber
	 * @return boolean
	 */
	public function checkBankAccountNumber($accountNumber) {
		$this->db->from($this->tableName);
		$this->db->join('player', 'playerbankdetails.playerId = player.playerId');
		$this->db->where('player.deleted_at IS NULL', null, false);
		$this->db->where('playerbankdetails.bankAccountNumber', $accountNumber);
		if ($this->db->count_all_results() === 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * detail: adding new bank details by withdrawal
	 *
	 * note: parameters mandatory to call this method
	 *
	 * @param array $data
	 * @return int
	 */
	public function addBankDetailsByWithdrawal($data) {
		$this->load->model(['operatorglobalsettings']);
		if (!array_key_exists('createdOn', $data)) {
			//add "now"
			$data['createdOn'] = $this->utils->getNowForMysql();
		}
		if (!array_key_exists('updatedOn', $data)) {
			//add "now"
			$data['updatedOn'] = $this->utils->getNowForMysql();
		}
		$this->db->insert('playerbankdetails', $data);
		$playerbankdetailsId = $this->db->insert_id();

		if($this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_can_be_withdraw_and_deposit')){
			//copy to deposit/withdrawal
			$this->duplicateBankToDepositOrWithdrawal($playerbankdetailsId);
		}

		return $playerbankdetailsId;
	}

	/**
	 * detail: checking unique bank account number
	 *
	 * note: parameters are mandatory to call this method
	 *
	 * @param string $bankAccountNumber
	 * @param string $accountType
	 * @return boolean
	 */
	public function checkUniqueBankAccountNumber($bankAccountNumber, $accountType, $player_id, $bankTypeId = NULL) {

		$this->db->select('*')
				 ->where('bankAccountNumber', $bankAccountNumber)
				 ->where('dwBank', $accountType)
				 ->where('bankTypeId', $bankTypeId);
		if($this->utils->getConfig('allow_multiplayer_to_have_existing_bank_account')){
			$this->db->where('playerId', $player_id);
		}

		$q = $this->db->get('playerbankdetails');

		if ($q->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * detail: get bank information of a certain player
	 *
	 * note: player id is mandatory to call this method
	 *
	 * @param int $player_id player id
	 * @return array
	 */
	public function getBankDetails($player_id) {
		$this->db->select("playerbankdetails.*, banktype.bankName, banktype.bankIcon, banktype.enabled_deposit, banktype.enabled_withdrawal, banktype.payment_type_flag")->from('playerbankdetails')
		    ->join('banktype', 'playerbankdetails.bankTypeId = banktype.bankTypeId')
		    ->where('playerbankdetails.playerId', $player_id)
		    ->where('playerbankdetails.dwBank', self::DEPOSIT_BANK)
		    ->where('playerbankdetails.verified', self::VERIFIED)
		    ->where('playerbankdetails.status', self::STATUS_ACTIVE);
		$dBank = $this->runMultipleRowArray();

        if(!empty($dBank)){
            $default_count = 0;
            foreach($dBank as &$player_bank){
                if($default_count > 0){
                    $player_bank['isDefault'] = 0;
                    continue;
                }
                if($player_bank['isDefault']){
                    $default_count++;
                }
            }

            if($default_count === 0){
                $dBank[0]['isDefault'] = 1;
            }
        }

		$this->db->select("playerbankdetails.*, banktype.bankName, banktype.bankIcon, banktype.enabled_deposit, banktype.enabled_withdrawal, banktype.payment_type_flag")->from('playerbankdetails')
		    ->join('banktype', 'playerbankdetails.bankTypeId = banktype.bankTypeId')
		    ->where('playerbankdetails.playerId', $player_id)
		    ->where('playerbankdetails.dwBank', self::WITHDRAWAL_BANK)
		    ->where('playerbankdetails.status', self::STATUS_ACTIVE);
		$wBank = $this->runMultipleRowArray();

        if(!empty($wBank)){
            $default_count = 0;
            foreach($wBank as &$player_bank){
                if($default_count > 0){
                    $player_bank['isDefault'] = 0;
                    continue;
                }
                if($player_bank['isDefault']){
                    $default_count++;
                }
            }

            if($default_count === 0){
                $wBank[0]['isDefault'] = 1;
            }
        }

		$result = array(
			'deposit' => $dBank,
			'withdrawal' => $wBank,
		);

		return $result;
	}

	public function getNotDeletedBankInfoList($player_id) {
		$this->db->select("playerbankdetails.*, banktype.bankName, banktype.bankIcon, banktype.enabled_deposit, banktype.enabled_withdrawal, banktype.payment_type_flag")
            ->from('playerbankdetails')
		    ->join('banktype', 'playerbankdetails.bankTypeId = banktype.bankTypeId')
		    ->where('playerbankdetails.playerId', $player_id)
		    ->where('playerbankdetails.dwBank', self::DEPOSIT_BANK)
		    ->where('playerbankdetails.status !=', self::STATUS_DELETED);
		$dBank = $this->runMultipleRowArray();

        if(!empty($dBank)){
            $default_count = 0;
            foreach($dBank as &$player_bank){
                if($default_count > 0){
                    $player_bank['isDefault'] = 0;
                    continue;
                }
                if($player_bank['isDefault']){
                    $default_count++;
                }
            }

            if($default_count === 0){
                $dBank[0]['isDefault'] = 1;
            }
        }

		$this->db->select("playerbankdetails.*, banktype.bankName, banktype.bankIcon, banktype.enabled_deposit, banktype.enabled_withdrawal, banktype.payment_type_flag")
            ->from('playerbankdetails')
		    ->join('banktype', 'playerbankdetails.bankTypeId = banktype.bankTypeId')
		    ->where('playerbankdetails.playerId', $player_id)
		    ->where('playerbankdetails.dwBank', self::WITHDRAWAL_BANK)
		    ->where('playerbankdetails.status !=', self::STATUS_DELETED);
		$wBank = $this->runMultipleRowArray();

        if(!empty($wBank)){
            $default_count = 0;
            foreach($wBank as &$player_bank){
                if($default_count > 0){
                    $player_bank['isDefault'] = 0;
                    continue;
                }
                if($player_bank['isDefault']){
                    $default_count++;
                }
            }

            if($default_count === 0){
                $wBank[0]['isDefault'] = 1;
            }
        }

		$result = array(
			'deposit' => $dBank,
			'withdrawal' => $wBank,
		);

		return $result;
	}

	/**
	 * detail: get deposit bank details of a player
	 *
	 * note: player id is mandatory to call this method
	 *
	 * @param int $playerId player id
	 * @return string
	 */
	public function getDepositBankDetail($playerId){
		$result = $this->getBankDetails($playerId);
		return $result['deposit'];
	}

	public function getAvailableDepositBankDetail($playerId){
		$result = $this->getBankDetails($playerId);

        if(empty($result['deposit'])){
            return NULL;
        }

        $filtered = [];
        foreach($result['deposit'] as $row){
            if(0 === (int)$row['enabled_deposit']){
                continue;
            }

            $row['displayName'] = lang($row['bankName']).' ('. self::getDisplayAccNum($row['bankAccountNumber']).')';

            $filtered[] = $row;
        }

		return empty($filtered) ? NULL : $filtered;
	}

	/**
	 * detail: get withdrawal bank details of a player
	 *
	 * note: player id is mandatory to call this method
	 *
	 * @param int $playerId player id
	 * @return string
	 */
	public function getWithdrawBankDetail($playerId){
		$result = $this->getBankDetails($playerId);
		return $result['withdrawal'];
	}

	public function getAvailableWithdrawBankDetail($playerId){
		$result =  $this->queryWithdrawBank($playerId);

        if(empty($result)){
            return NULL;
        }

        $filtered = [];
        foreach($result as $row){
            if(0 === (int)$row['enabled_withdrawal']){
                continue;
            }
            $filtered[] = $row;
        }

		return empty($filtered) ? NULL : $filtered;
	}

	# Returns player banks without wechat and alipay
	public function getWithdrawBankOnlyDetail($playerId){
		return $this->queryWithdrawBank($playerId, 'bank');
	}

	# Returns player's saved wechat accounts
	public function getWithdrawWeChatAccountDetail($playerId){
		return $this->queryWithdrawBank($playerId, 'wechat');
	}

	# Returns player's saved alipay accounts
	public function getWithdrawAlipayAccountDetail($playerId){
		return $this->queryWithdrawBank($playerId, 'alipay');
	}

	/**
	 * Read details for player's withdrawal banks
	 * @param	int		$playerId	= Player.playerId
	 * @param  [type] $type     [description]
	 * @return [type]           [description]
	 */
	private function queryWithdrawBank($playerId, $type = NULL) {
		$this->db->select("D.*, B.bankName, B.enabled_deposit, B.enabled_withdrawal, B.bankIcon, B.bank_code")
			->from('playerbankdetails AS D')
			->join('banktype AS B', 'D.bankTypeId = B.bankTypeId')
			->where('D.playerId', $playerId)
			->where('D.dwBank', self::WITHDRAWAL_BANK)
			->where('D.verified', self::VERIFIED)
			->where('D.status', self::STATUS_ACTIVE);

		# Limit bank type if $type is given
		if('bank' == $type) {
			$this->db->where('D.bankTypeId !=', $this->BANK_TYPE_WECHAT);
			$this->db->where('D.bankTypeId !=', $this->BANK_TYPE_ALIPAY);
		} elseif ('wechat' == $type) {
			$this->db->where('D.bankTypeId', $this->BANK_TYPE_WECHAT);
		} elseif ('alipay' == $type) {
			$this->db->where('D.bankTypeId', $this->BANK_TYPE_ALIPAY);
		}

		$result = $this->runMultipleRowArray();
		return empty($result) ? array() : $result; # avoid returning null on empty result
	}

	public function getDefaultDepositBankDetail($playerId){
		$result = $this->getDefaultBankDetail($playerId);
		return $result['deposit'];
	}

	/**
	 * detail: get Withdraw bank details of a player
	 *
	 * note: player id is mandatory to call this method
	 *
	 * @param int $playerId player id
	 * @return string
	 */
	public function getDefaultWithdrawBankDetail($playerId){
		$result=$this->getDefaultBankDetail($playerId);

		return $result['withdrawal'];
	}

	/**
	 * detail: get default bank detail of a certain player
	 *
	 * note: player id is mandatory to call this method
	 *
	 * @param int $playerId player id
	 * @return array
	 */
	public function getDefaultBankDetail($playerId){
		$this->db->select('pbd.*, banktype.bankName, banktype.bankIcon, banktype.enabled_deposit, banktype.enabled_withdrawal')->from('playerbankdetails as pbd')
		    ->join('banktype', 'banktype.bankTypeId=pbd.bankTypeId')->where('playerId', $playerId)
		    ->where('dwBank', self::DEPOSIT_BANK)->where('pbd.status', self::STATUS_ACTIVE)
		    ->where('isDefault', '1')->limit(1);

		$dBank=$this->runMultipleRowArray();

		$this->db->select('pbd.*, banktype.bankName, banktype.bankIcon, banktype.enabled_deposit, banktype.enabled_withdrawal')->from('playerbankdetails as pbd')
		    ->join('banktype', 'banktype.bankTypeId=pbd.bankTypeId')->where('playerId', $playerId)
		    ->where('dwBank', self::WITHDRAWAL_BANK)->where('pbd.status', self::STATUS_ACTIVE)
		    ->where('isDefault', '1')->limit(1);
		$wBank=$this->runMultipleRowArray();

		if(empty($dBank)){
			$this->db->select('playerBankDetailsId')->from('playerbankdetails')->where('playerId', $playerId)
				->where('status', self::STATUS_ACTIVE)
				->where('dwBank', self::DEPOSIT_BANK)->limit(1);
			$playerBankDetailsId=$this->runOneRowOneField('playerBankDetailsId');
			$this->utils->debug_log('playerBankDetailsId dBank', $playerBankDetailsId);
			if(!empty($playerBankDetailsId)){
				$this->db->set('isDefault', '1')->where('playerBankDetailsId', $playerBankDetailsId);
				$this->runAnyUpdate('playerbankdetails');
				$this->db->select('pbd.*, banktype.bankName, banktype.bankIcon, banktype.enabled_deposit, banktype.enabled_withdrawal')->from('playerbankdetails as pbd')
				    ->join('banktype', 'banktype.bankTypeId=pbd.bankTypeId')->where('playerId', $playerId)
				    ->where('dwBank', self::DEPOSIT_BANK)->where('pbd.status', self::STATUS_ACTIVE)
				    ->where('isDefault', '1');

				$dBank=$this->runMultipleRowArray();
			}
		}

		if(empty($wBank)){
			$this->db->select('playerBankDetailsId')->from('playerbankdetails')->where('playerId', $playerId)
				->where('status', self::STATUS_ACTIVE)
				->where('dwBank', self::WITHDRAWAL_BANK)->limit(1);
			$playerBankDetailsId=$this->runOneRowOneField('playerBankDetailsId');
			$this->utils->debug_log('playerBankDetailsId wBank', $playerBankDetailsId);
			if(!empty($playerBankDetailsId)){
				$this->db->set('isDefault', '1')->where('playerBankDetailsId', $playerBankDetailsId);
				$this->runAnyUpdate('playerbankdetails');

				$this->db->select('pbd.*, banktype.bankName, banktype.bankIcon, banktype.enabled_deposit, banktype.enabled_withdrawal')->from('playerbankdetails as pbd')
				    ->join('banktype', 'banktype.bankTypeId=pbd.bankTypeId')->where('playerId', $playerId)
				    ->where('dwBank', self::WITHDRAWAL_BANK)->where('pbd.status', self::STATUS_ACTIVE)
				    ->where('isDefault', '1');
				$wBank=$this->runMultipleRowArray();
			}
		}

		$result = array(
			'deposit' => $dBank,
			'withdrawal' => $wBank,
		);

		return $result;
	}

	/**
	 * Will get player deposit rule
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getBankDetailsById($bankDetailsId) {
		$this->db->select('D.playerBankDetailsId, D.bankTypeId, D.bankAccountFullName, D.bankAccountNumber,
			D.bankAddress, D.city, D.province, D.branch, T.bankName, D.playerId, D.dwBank')
			->from('playerbankdetails AS D')
			->join('banktype AS T', 'D.bankTypeId = T.bankTypeId', 'left');
		$this->db->where('D.playerbankdetailsId', $bankDetailsId);

		return $this->runOneRowArray();
	}

	/**
	 * Query playerbankdetails using bankTypeId and bankAccountNumber. Used by some withdrawal API.
	 */
	public function getBankDetailsByBankAccount($bankTypeId, $bankAccountNumber) {
		$this->db->select(
			'playerbankdetails.playerBankDetailsId,'.
			'playerbankdetails.bankTypeId,'.
			'playerbankdetails.bankAccountFullName,'.
			'playerbankdetails.bankAccountNumber,'.
			'playerbankdetails.bankAddress,'.
			'playerbankdetails.city,'.
			'playerbankdetails.province,'.
			'playerbankdetails.branch'
			)->from('playerbankdetails');
		$this->db->where('playerbankdetails.bankTypeId', $bankTypeId);
		$this->db->where('playerbankdetails.bankAccountNumber', $bankAccountNumber);

		return $this->runOneRowArray();
	}

	/**
	 * add bank details by deposit
	 *
	 * @return	array
	 */
	public function addBankDetailsByDeposit($data) {
		$this->load->model(['operatorglobalsettings']);
		if (!array_key_exists('createdOn', $data)) {
			//add "now"
			$data['createdOn'] = $this->utils->getNowForMysql();
		}
		if (!array_key_exists('updatedOn', $data)) {
			//add "now"
			$data['updatedOn'] = $this->utils->getNowForMysql();
		}
		$this->db->insert('playerbankdetails', $data);
		$playerbankdetailsId = $this->db->insert_id();

		if($this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_can_be_withdraw_and_deposit')){
			//copy to deposit/withdrawal
			$this->duplicateBankToDepositOrWithdrawal($playerbankdetailsId);
		}

		return $playerbankdetailsId;
	}

	/**
	 * detail: get bank details
	 *
	 * @param int $playerId
	 * @return	array
	 */
	public function getDepositBankDetails($playerId) {
		$this->db->select('playerbankdetails.*,
                banktype.*,
            ')
			->from('playerbankdetails')
			->join('banktype', 'banktype.bankTypeId = playerbankdetails.bankTypeId', 'left')
			->order_by('playerbankdetails.bankAccountFullName', 'asc');
		$this->db->where('playerbankdetails.playerId', $playerId);
		$this->db->where('playerbankdetails.dwBank', self::DEPOSIT_BANK); //0 = deposit
		$this->db->where('playerbankdetails.status', self::STATUS_ACTIVE); //0 = active
		// $this->db->where('playerbankdetails.isRemember', '1'); //1 = remembered

		$query = $this->db->get();
		//var_dump($query->result_array());exit();
		return $query->result_array();
	}

	public function updateBanktypeId( $playerBankDetailsId, $bankTypeId ){
		return $this->db->where('playerBankDetailsId', $playerBankDetailsId)
						->update('playerbankdetails', array(
							'bankTypeId' => $bankTypeId
						));
	}

	public function getBankNumberValidatorMode(){
		$this->load->model(['operatorglobalsettings']);
		return $this->operatorglobalsettings->getSettingValue('bank_number_validator_mode', self::ONLY_ALLOW_DUPLICATE_ONE_PLAYER);
	}

	public function is_only_allow_duplicate_one_player(){
		return self::ONLY_ALLOW_DUPLICATE_ONE_PLAYER==$this->getBankNumberValidatorMode();
	}

	public function is_only_allow_duplicate_one_player_any(){
		return self::ONLY_ALLOW_DUPLICATE_ONE_PLAYER_ANY==$this->getBankNumberValidatorMode();
	}

	public function is_not_allow_duplicate_number(){
		return self::NOT_ALLOW_DUPLICATE_NUMBER==$this->getBankNumberValidatorMode();
	}

	public function is_allow_any_duplicate_number(){
		return self::ALLOW_ANY_DUPLICATE_NUMBER==$this->getBankNumberValidatorMode();
	}

	public function addDepositBankDetails($data) {
		if (!array_key_exists('createdOn', $data)) {
			//add "now"
			$data['createdOn'] = $this->utils->getNowForMysql();
		}
		if (!array_key_exists('updatedOn', $data)) {
			//add "now"
			$data['updatedOn'] = $this->utils->getNowForMysql();
		}
		$data['dwBank']=self::DEPOSIT_BANK;
		$data['status']=self::STATUS_ACTIVE;

		$this->db->insert('playerbankdetails', $data);
		return $playerbankdetailsId = $this->db->insert_id();
	}

	public function getBankDetailInfo($bank_details_id){
		$this->db->select('playerbankdetails.*,
                banktype.*,
            ')
			->from('playerbankdetails')
			->join('banktype', 'banktype.bankTypeId = playerbankdetails.bankTypeId', 'left')
		    ->where('playerBankDetailsId', $bank_details_id);

		return $this->runOneRowArray();
	}

	public function validate_bank_account_number($playerId, $bank_account_number, $bank_type, $bank_details_id=null, $bank_type_id = NULL){

		// $bankInfo=null;
		// if(!empty($bank_details_id)){
		// 	$bankInfo=$this->getBankDetailInfo($bank_details_id);
		// }

		log_message('debug', 'validate_bank_account_number params' , ['playerId'=>$playerId,
			'bank_type_id' => $bank_type_id, 'bank_account_number'=>$bank_account_number, 'bank_type'=>$bank_type, 'bank_details_id'=>$bank_details_id]);

		$result=false;
		$mode=$this->getBankNumberValidatorMode();

		$check_status = [];
		if($this->utils->isEnabledFeature('duplicate_bank_account_number_verify_status_active')){
            $check_status[] = self::STATUS_ACTIVE;
        }

		log_message('debug', 'getBankNumberValidatorMode:'.$mode);
		switch ($mode) {
			case self::ONLY_ALLOW_DUPLICATE_ONE_PLAYER:
				//one player allow duplicate withdrawal and deposit, but can't allow duplicate number in same type
				//search other player
				$this->db->select('playerBankDetailsId')->from('playerbankdetails')
				    ->where('playerId !=', $playerId)
				    ->where('bankAccountNumber', $bank_account_number);
				if(!empty($check_status)){
				    $this->db->where_in('status', $check_status);
                }

				if(!empty($bank_details_id)){
					$this->db->where('playerBankDetailsId !=', $bank_details_id);
				}

				$result=! $this->runExistsResult();

				$this->utils->printLastSQL();

				if($result){
					//search this player on
					$this->db->select('playerBankDetailsId')->from('playerbankdetails')
						->where('playerId', $playerId)
						->where('dwBank', $bank_type)
						->where('bankAccountNumber', $bank_account_number);

                    if(!empty($check_status)){
                        $this->db->where_in('status', $check_status);
                    }

					if(!empty($bank_details_id)){
						$this->db->where('playerBankDetailsId !=', $bank_details_id);
					}

					$result=! $this->runExistsResult();

					$this->utils->printLastSQL();

				}

				break;

			case self::ONLY_ALLOW_DUPLICATE_ONE_PLAYER_ANY:
				//one player allow duplicate number , no matter withdrawal or deposit
				$this->db->select('playerBankDetailsId')->from('playerbankdetails')
				    ->where('playerId !=', $playerId)
				    ->where('bankAccountNumber', $bank_account_number);

                if(!empty($check_status)){
                    $this->db->where_in('status', $check_status);
                }

				if(!empty($bank_details_id)){
					$this->db->where('playerBankDetailsId !=', $bank_details_id);
				}

				$result=! $this->runExistsResult();
				break;
            case self::NOT_ALLOW_DUPLICATE_NUMBER:
                //don't allow any duplicate bank account number.
                $this->db->select('playerBankDetailsId')->from('playerbankdetails')
                    ->where('bankAccountNumber', $bank_account_number);

                if(!empty($check_status)){
                    $this->db->where_in('status', $check_status);
                }

                if(!empty($bank_details_id)){
                    $this->db->where('playerBankDetailsId !=', $bank_details_id);
                }

                $result=! $this->runExistsResult();
                break;
            case self::NOT_ALLOW_DUPLICATE_NUMBER_ON_SAME_BANKTYPE:
                $this->db->select('playerBankDetailsId')->from('playerbankdetails')
                    ->where('dwBank', $bank_type)
                    ->where('bankTypeId', $bank_type_id)
                    ->where('bankAccountNumber', $bank_account_number);

                if(!empty($check_status)){
                    $this->db->where_in('status', $check_status);
                }

                if(!empty($bank_details_id)){
                    $this->db->where('playerBankDetailsId !=', $bank_details_id);
                }

                $result=! $this->runExistsResult();
                break;
            case self::NOT_ALLOW_DUPLICATE_NUMBER_ON_SAME_BANKTYPE_ANY:
                $this->db->select('playerBankDetailsId')->from('playerbankdetails')
                    ->where('bankTypeId', $bank_type_id)
                    ->where('bankAccountNumber', $bank_account_number);

                if(!empty($check_status)){
                    $this->db->where_in('status', $check_status);
                }

                if(!empty($bank_details_id)){
                    $this->db->where('playerBankDetailsId !=', $bank_details_id);
                }

                $result=! $this->runExistsResult();
                break;
			default:
				$result=true;
				break;
		}

		return $result;
	}

	public function search_id_by_bank_number($playerId, $bank_type ,$bank_account_number_prev){
		$this->db->select('playerBankDetailsId')->from('playerbankdetails')
			->where('bankAccountNumber', $bank_account_number_prev)
		    ->where('dwBank', $bank_type)
		    ->where('playerId', $playerId)
			->where('status', self::STATUS_ACTIVE);

		return $this->runOneRowOneField('playerBankDetailsId');
	}

    /**
     *
     * @author Elvis Chen<elvis.chen@tripleonetech.net>
     * @since date 20170919
     * @param type $playerId
     * @param type $bank_detail_id
     * @return stdClass|boolean
     */
    public function getPlayerBankDetailById($playerId, $bank_detail_id, $dwBank = NULL){
		$this->db->where('playerId', $playerId);
        if(NULL !== $dwBank){
		    $this->db->where('dwBank', $dwBank);
        }
		return $this->getOneRowById($bank_detail_id);
    }

    /**
     *
     * @author Elvis Chen<elvis.chen@tripleonetech.net>
     * @since date 20170918
     * @param type $playerId
     * @param type $bank_detail_id
     * @param type $data
     * @return boolean
     */
	public function updatePlayerBankDetails($playerId, $bank_detail_id, $data) {
		$this->db->where('playerId', $playerId);
		$this->db->where('playerBankDetailsId', $bank_detail_id);
		return $this->db->update('playerbankdetails', $data);
	}

    /**
     * @author Elvis Chen<elvis.chen@tripleonetech.net>
     * @since date 20170919
     * @param int $playerId
     * @param int $bank_type
     * @param int $bank_detail_id
     * @return boolean
     */
    public function setPlayerDefaultBank($playerId, $bank_type, $bank_detail_id){
        $reset_data = [
            'isDefault' => '0'
        ];
		$this->db->where('playerId', $playerId);
		$this->db->where('dwBank', $bank_type);

		if(!$this->db->update('playerbankdetails', $reset_data)){
            return FALSE;
        }

        $data = [
            'isDefault' => '1',
            'updatedOn' => date("Y-m-d H:i:s")
        ];
		$this->db->where('playerId', $playerId);
		$this->db->where('dwBank', $bank_type);
		$this->db->where('playerBankDetailsId', $bank_detail_id);

        return $this->db->update('playerbankdetails', $data);
    }

    /**
     * @author Elvis Chen<elvis.chen@tripleonetech.net>
     * @since date 20170919
     * @param int $playerId
     * @param int $bank_detail_id
     * @return boolean
     */
    public function setPlayerDefaultDepositBank($playerId, $bank_detail_id){
        return $this->setPlayerDefaultBank($playerId, self::DEPOSIT_BANK, $bank_detail_id);
    }

    /**
     * @author Elvis Chen<elvis.chen@tripleonetech.net>
     * @since date 20170919
     * @param int $playerId
     * @param int $bank_detail_id
     * @return boolean
     */
    public function setPlayerDefaultWithdrawalBank($playerId, $bank_detail_id){
        return $this->setPlayerDefaultBank($playerId, self::WITHDRAWAL_BANK, $bank_detail_id);
    }

	static public function getDisplayAccNum($accNum) {
		$CI = &get_instance();
		if(strlen($accNum) <= self::NUM_CHAR_DISPLAY || $CI->utils->isEnabledFeature('show_player_complete_withdrawal_account_number')){
			return $accNum;
		}
		return '*'.substr($accNum, -self::NUM_CHAR_DISPLAY);
	}

    static public function AllowAddBankDetail($dwbank, $bank_list, &$message = '', $input_banktype_id = null){
        $CI = &get_instance();

        switch ($dwbank) {
        	case self::DEPOSIT_BANK:
	        	if($CI->operatorglobalsettings->getSettingValueWithoutCache('financial_account_deposit_account_limit')){
		            $limit_count = $CI->operatorglobalsettings->getSettingValueWithoutCache('financial_account_max_deposit_account_number');
		            if (count($bank_list) >= $limit_count) {
		                $message = sprintf(lang('notify.limit.bank.count'), $limit_count);
		                return FALSE;
		            }
	        	}
        		break;
        	case self::WITHDRAWAL_BANK:
	        	if($CI->operatorglobalsettings->getSettingValueWithoutCache('financial_account_withdraw_account_limit')){
		            $limit_count = $CI->operatorglobalsettings->getSettingValueWithoutCache('financial_account_max_withdraw_account_number');
		            if (count($bank_list) >= $limit_count) {
		                $message = sprintf(lang('notify.limit.bank.count'), $limit_count);
		                return FALSE;
		            }
	        	}
        		break;
        }

        if($CI->operatorglobalsettings->getSettingValueWithoutCache('financial_account_one_account_per_institution') && !is_null($input_banktype_id)) {
            $input_banktype = $CI->banktype->getBankTypeById($input_banktype_id);
            foreach ($bank_list as $bank) {
                if($input_banktype->bankTypeId == $bank['bankTypeId']) {
                    $message = sprintf(lang('notify.limit.one_account_per_institution'), lang($bank['bankName'])) ;
                    return FALSE;
                }
            }
        }

        return TRUE;
    }

    public function importPlayerBank($external_id, $playerId, $bankTypeId, $dwBank,
    	$bankAccountFullName, $bankAccountNumber, $province, $city, $branch, $bankAddress, $createdOn, $status, &$message){

    	if (empty($playerId)) {
    		$message = "Empty playerId: [$playerId]";
    		return false;
    	}

    	if (empty($bankTypeId)) {
    		$message = "Empty bankTypeId: [$bankTypeId]";
    		return false;
    	}

    	$data = array(
    		'playerId' => $playerId,
    		'bankTypeId' => $bankTypeId,
    		'bankAccountFullName' => $bankAccountFullName,
    		'bankAccountNumber' => $bankAccountNumber,
    		'bankAddress'=> $bankAddress,
    		'city'=>$city,
    		'province'=>$province,
    		'branch'=>$branch,
    		'isDefault'=>'1',
    		'isRemember'=>'1',
    		'dwBank' => $dwBank,
    		'status' => $status,
    		'createdOn' => $createdOn,
    		'updatedOn' => $this->utils->getNowForMysql(),
    		'external_id'=> $external_id,
    	);

    	$this->db->select('playerBankDetailsId')->from('playerbankdetails')
    	->where('playerId', $playerId)->where('external_id', $external_id);
    	$playerBankDetailsId = $this->runOneRowOneField('playerBankDetailsId');

    	if (!empty($playerBankDetailsId)) {
    		$exists=true;
    		$this->db->set($data)->where('playerBankDetailsId', $playerBankDetailsId)->update('playerbankdetails');
    	} else {
    		$exists=false;
    		$this->db->set($data)->insert('playerbankdetails');
    		$playerBankDetailsId = $this->db->insert_id();
    	}

    	return $playerBankDetailsId;
    }

    public function exists_one_withdrawal_bank($playerId){
    	$this->db->select('playerBankDetailsId')->from('playerbankdetails')->where('playerId', $playerId)
    	->where('dwBank', self::WITHDRAWAL_BANK)
    	->where('status', self::STATUS_ACTIVE);

    	return $this->runExistsResult();
    }

	public function addPlayerBankDetailByAdmin($data) {
		$this->load->model(['operatorglobalsettings']);
		if (!array_key_exists('createdOn', $data)) {
			//add "now"
			$data['createdOn'] = $this->utils->getNowForMysql();
		}
		if (!array_key_exists('updatedOn', $data)) {
			//add "now"
			$data['updatedOn'] = $this->utils->getNowForMysql();
		}
		$this->db->insert('playerbankdetails', $data);
		$playerbankdetailsId = $this->db->insert_id();

		if($this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_can_be_withdraw_and_deposit')){
			//copy to deposit/withdrawal
			$this->duplicateBankToDepositOrWithdrawal($playerbankdetailsId);
		}

		return $playerbankdetailsId;
	}

    public function duplicateBankToDepositOrWithdrawal($playerBankDetailsId){
    	$success=false;
    	if(!empty($playerBankDetailsId)){
	    	$this->db->from('playerbankdetails')->where('playerBankDetailsId', $playerBankDetailsId);
			$data = $this->runOneRowArray();

	    	if(!empty($data)){
	    		//remove id
	    		unset($data['playerBankDetailsId']);
				//change type
				if($data['dwBank']==Playerbankdetails::WITHDRAWAL_BANK){
			        if($this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_deposit_account_default_unverified')){
			            $data['verified'] = '0';
			        }
			        else{
			        	$data['verified'] = self::VERIFIED;
			        }
					$data['dwBank']=Playerbankdetails::DEPOSIT_BANK;
					$dw = 'deposit';
				}elseif($data['dwBank']==Playerbankdetails::DEPOSIT_BANK){
			        if($this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_withdraw_account_default_unverified')){
			            $data['verified'] = '0';
			        }
			        else{
			        	$data['verified'] = self::VERIFIED;
			        }
					$data['dwBank']=Playerbankdetails::WITHDRAWAL_BANK;
					$dw = 'withdrawal';
				}
				$bank_list = $this->getBankDetails($data['playerId']);
				$message = '';
				$allow = self::AllowAddBankDetail($data['dwBank'], $bank_list[$dw], $message, $data['bankTypeId']);
				if($allow){
					//insert
					$success = $this->insertData('playerbankdetails', $data);
				}
	    	}
    	}

    	return $success;
    }

    /**
     * Check if given playerbankdetails is valid and belongs to player
     * @param	int		$playerBankDetailsId	== playerbankdetails.playerBankDetailsId
     * @param	int		$player_id				== player.playerId
     * @param	boolean	$deposit				true for deposit, false for withdraw account
     * @return	boolean	true if valid, false if invalid
     */
    public function isValidBankForPlayer($playerBankDetailsId, $player_id, $deposit = true) {
    	$dwBank = $deposit ? 0 : 1;
    	$this->db->from($this->tableName)
    		->select('COUNT(*) AS count')
    		->where($this->idField, $playerBankDetailsId)
    		->where('playerId', $player_id)
    		->where("dwBank = '{$dwBank}' ", null, false)
    		->where("IF(status IS NULL, '0', status) = '0'", null, false)
    	;

    	$res = $this->runOneRowOneField('count');

    	// $this->utils->printLastSQL();

    	return $res == 1 ? true : false;
    }

}

///END OF FILE////////