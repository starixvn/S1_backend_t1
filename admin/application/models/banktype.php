<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Banktype
 *
 * General behaviors include
 * * Get the bank type status
 * * Get bank type by id
 * * Get all bank types
 * * Able to insert bank type
 * * Able to update bank type
 * * Can delete bank type
 * * Get all special payment lists
 * * Get deposit bank details
 * * Able to sync bank type from third party
 * * Get bank type lists filtered by status
 * * Get existing system id
 * * Get bank type using system id
 *
 * @category  Payment Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Banktype extends BaseModel {

	protected $tableName = 'banktype';

	protected $idField = 'bankTypeId';

	public function __construct() {
		parent::__construct();
	}

	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'not active';
	const STATUS_DELETE = 'delete';
	const PLAYER_ADDED_BANK = 1;

	/**
	 * detail: getting bank types by status
	 *
	 * @param string $status
	 * @return array
	 */
	public function getBankTypes($status = self::STATUS_ACTIVE) {
		if ($status) {
			$this->db->where('status', $status);
		}

		$query = $this->db->get($this->tableName);

		return $this->getMultipleRow($query);
	}

	/**
	 * @param string $status
	 * @return array
	 */
	public function getBankTypeKV($status = self::STATUS_ACTIVE) {
		$bankTypes = $this->getBankTypes($status);
		$needTranslate = true;
		$kv = $this->convertRowsToKV($bankTypes, $this->idField, 'bankName', $needTranslate);
		return $kv;
	}

	/**
	 * detail: get bank type using id
	 *
	 * @param int $id bank type Id
	 * @return string
	 */
	public function getBankTypeById($id) {
		return $this->getOneRowById($id);
	}
	#aris

	/**
	 * detail: get all bank types
	 *
	 * @return array
	 */
	public function getAllBanktype() {
		$sql = "
		SELECT Q1.*,B.username as updatedByUsername FROM
		(SELECT B.*, A.username as createdByUsername FROM banktype AS B
			LEFT JOIN adminusers AS A ON A.userId = B.createdBy) AS Q1
		LEFT JOIN adminusers AS B ON B.userId = Q1.updatedBy
		";

		return $this->db->query($sql)->result_array();
	}

	/**
	 * detail: adding new bank type
	 *
	 * @param string $bankName
	 * @param int $userId user id
	 * @param int $external_system_id external system id
	 * @return boolean
	 */
	public function addBanktype($bankName, $userId = null, $external_system_id = null, $bank_code = null, $payment_type_flag = Financial_account_setting::PAYMENT_TYPE_FLAG_BANK, $bank_icon = null, $status = self::STATUS_ACTIVE) {

		$data = array(
			'bankName' => $bankName,
			'external_system_id' => $external_system_id,
			'bank_code' => $bank_code,
			'createdOn' => date("Y-m-d h:i:s"),
			'updatedOn' => date("Y-m-d h:i:s"),
			'createdBy' => $userId,
			'updatedBy' => $userId,
			'status' => $status,
			'bankIcon' => $bank_icon,
			'payment_type_flag' => $payment_type_flag
		);

		$this->load->model(['payment_account']);
		if (!empty($external_system_id)) {
			$data['default_payment_flag'] = Payment_account::FLAG_AUTO_ONLINE_PAYMENT;
		}
		$this->db->insert('banktype', $data);
		return $this->db->insert_id();

	}

	/**
	 * detail: update the details of a certain bank type
	 *
	 * @param int $bankTypeId bank type id
	 * @param array $data
	 * @return boolean
	 */
	public function updateBanktype($bankTypeId, $data) {

		try {
			$this->load->model(['payment_account']);
			if (isset($data['external_system_id']) && !empty(@$data['external_system_id'])) {
				$data['default_payment_flag'] = Payment_account::FLAG_AUTO_ONLINE_PAYMENT;
			}

			$this->db->where('bankTypeId', $bankTypeId);
			$this->db->update('banktype', array_merge($data, array(
				'updatedOn' => $this->utils->getNowForMysql(),
				'updatedBy' => $this->authentication->getUserId(),

			)));

			// var_dump([$bankTypeId,  array_merge($data, array(
			// 	'updatedOn' => $this->utils->getNowForMysql(),
			// 	'updatedBy' => $this->authentication->getUserId(),

			// ))]); die();

			if ($this->db->_error_message()) {
				$this->utils->debug_log($this->db->_error_message());
				throw new Exception($this->db->_error_message());
			} else {
				return TRUE;
			}

		} catch (Exception $e) {
			return FALSE;
		}

	}

	/**
	 * detail: delete a certain bank type
	 *
	 * @param int $bankTypeId bank type id
	 * @return boolean
	 */
	public function deleteBankType($bankTypeId) {

		try {

			$this->db->where('bankTypeId', $bankTypeId);
			$this->db->delete('banktype');

			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());
			} else {
				return TRUE;
			}

		} catch (Exception $e) {
			return FALSE;
		}

	}

	/**
	 * detail: get all the special payments type
	 *
	 * @return array
	 */
	public function getSpecialPaymentTypeList() {
		$this->load->model('operatorglobalsettings');
		$special_payment_list = $this->operatorglobalsettings->getSpecialPaymentList();
		if (!empty($special_payment_list)) {
			$this->db->distinct()->select('banktype.*')
				->select('payment_account.id payment_account_id, payment_account.flag, payment_account.account_image_filepath, payment_account.account_icon_filepath, payment_account.min_deposit_trans, payment_account.max_deposit_daily, payment_account.max_deposit_trans')
				->from('banktype')
				->join('payment_account', 'payment_account.payment_type_id = banktype.bankTypeId')
				->where_in('payment_account.id', $special_payment_list)
				->where('payment_account.status', self::STATUS_NORMAL)
				->order_by('payment_account.payment_order');
			return $this->runMultipleRow();
		}
		return null;
	}

	/**
	 * detail: get all the special payments type configured for mobile device
	 *
	 * @return array
	 */
	public function getSpecialPaymentTypeListMobile() {
		$this->load->model('operatorglobalsettings');
		$special_payment_list = $this->operatorglobalsettings->getSpecialPaymentListMobile();
		if (!empty($special_payment_list)) {
			$this->db->distinct()->select('banktype.*')
				->select('payment_account.id payment_account_id, payment_account.flag, payment_account.account_image_filepath, payment_account.account_icon_filepath, payment_account.min_deposit_trans, payment_account.max_deposit_daily, payment_account.max_deposit_trans')
				->from('banktype')
				->join('payment_account', 'payment_account.payment_type_id = banktype.bankTypeId')
				->where_in('payment_account.id', $special_payment_list)
				->where('payment_account.status', self::STATUS_NORMAL)
				->order_by('payment_account.payment_order');
			return $this->runMultipleRow();
		}
		return null;
	}

	/**
	 * detail: get bank details
	 *
	 * @param int $playerId
	 * @return	array
	 */
	public function getDepositBankDetails($playerId) {
		$this->load->model(['playerbankdetails']);
		return $this->playerbankdetails->getDepositBankDetails($playerId);
	}

	/**
	 * detail: sync the data of bank type from third party
	 *
	 * @param string $bankName
	 * @param int $systemId system id
	 * @param int $adminUserId
	 * @param boolean $showOnPlayer
	 * @return boolean
	 */
	public function syncBankType3rdParty($bankName, $systemId, $adminUserId, $showOnPlayer = true) {
		$this->load->model(array('payment_account'));
		$this->db->select('bankTypeId')->from($this->tableName)
			->where('external_system_id', $systemId);
		$id = $this->runOneRowOneField('bankTypeId');
		$data = array(
			'bankName' => $bankName,
			'external_system_id' => $systemId,
			'updatedOn' => $this->utils->getNowForMysql(),
			'updatedBy' => $adminUserId,
			'status' => self::STATUS_ACTIVE,
			'show_on_player' => $showOnPlayer,
			'default_payment_flag' => Payment_account::FLAG_AUTO_ONLINE_PAYMENT,
		);
		if ($id) {
			//update
			$this->db->set($data)->where('external_system_id', $systemId)->update($this->tableName);
		} else {
			//insert
			$data['createdOn'] = $this->utils->getNowForMysql();
			$data['createdBy'] = $adminUserId;

			$this->db->set($data)->insert($this->tableName);
		}

	}

	/**
	 * detail: get the lists of bank type filtered by status
	 *
	 * @return array
	 */
	public function getList() {
		$this->db->from($this->tableName)->where('status', self::STATUS_ACTIVE);
		return $this->runMultipleRow();
	}

	/**
	 * detail: get existing bank type
	 *
	 * @param int $paymentTypeId payment type id
	 * @return boolean
	 */
	public function existsSystemId($paymentTypeId) {
		if (!empty($paymentTypeId)) {
			$banktype = $this->getBankTypeById($paymentTypeId);
			//only for gateway
			return $banktype && !empty($banktype->external_system_id);
		}
		return false;
	}

	/**
	 * detail: Get bank type system id
	 *
	 * @param int $systemId
	 * @return array
	 */
	public function getBanktypeBySystemId($systemId) {
		$this->db->select('bankTypeId')->from($this->tableName)
			->where('external_system_id', $systemId);

		return $this->runOneRow();
	}

	public function getActiveBankDropdown() {
		$map = [];
		$this->db->from($this->tableName)->where('status', self::STATUS_ACTIVE);
		$rows = $this->runMultipleRowArray();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$map[$row['bankTypeId']] = lang($row['bankName']);
			}
		}

		return $map;
	}

	public function getActiveMap() {
		$map = [];
		$this->db->from($this->tableName)->where('status', self::STATUS_ACTIVE);
		$rows = $this->runMultipleRowArray();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$map[$row['bank_code']] = $row;
			}
		}

		return $map;
	}

	public function updateBankCode() {

		$bank_list = [
			1 => 'ICBC',
			2 => 'CMB',
			3 => 'CCB',
			4 => 'AGB',
			5 => 'BCOMM',
			6 => 'BOC',
			7 => 'SDB',
			8 => 'GDB',
			9 => 'DRCBANK',
			10 => 'CITIC',
			11 => 'CMBC',
			12 => 'PSBC',
			13 => 'CIB',
			14 => 'HXB',
			15 => 'PAB',
			16 => 'GX966888',
			17 => 'GZCB',
			18 => 'NJCB',
			19 => 'GRCB',
			20 => 'CEB',
		];

		foreach ($bank_list as $key => $value) {
			$this->db->where('bankTypeId', $key)->update('banktype', ['bank_code' => $value]);
		}

	}

	/**
	 * Will get bank type
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function getAllActiveBankType() {
		$this->db->select('bankTypeId,bankName,enabled_withdrawal,enabled_deposit')
			->from('banktype')->where('status', self::STATUS_ACTIVE);

		return $this->runMultipleRowArray();
	}

	/**
	 * detail: get bank type id of other
	 *
	 * @return int
	 */
	public function getOtherBankTypeId() {
		$qobj = $this->db->select('bankTypeId')
                     ->from('banktype')
                      ->where('bank_code', 'other')
                     ->get();

		return $qobj->row()->bankTypeId;
	}

	public function syncBaseBanktype(){
		//load banktype.json

		$jsonFile=APPPATH.'config/banktype.json';

		$json=file_get_contents($jsonFile);
		$banktypeList=$this->utils->decodeJson($json);
		$now=$this->utils->getNowForMysql();

		if(empty($banktypeList)){
			throw new Exception('wrong banktype.json file');
		}
		foreach ($banktypeList as $banktype) {
			//search by id
			$this->db->select('bank_code')->from('banktype')->where('bank_code', $banktype['bank_code']);
			if(!$this->runExistsResult()){
				// $perm['status']=self::DB_TRUE;
				// $perm['sort']=$perm['funcId'];
				// $perm['createTime']=$now;
				// //insert
				// $this->insertData('functions', $perm);
				$data=[
					'bank_code'=>$banktype['bank_code'],
					'bankName'=>$banktype['bankName'],
					'createdOn'=>$now,
					'updatedOn'=>$now,
					'createdBy'=>1,
					'updatedBy'=>1,
					'status'=>self::STATUS_ACTIVE,
					'show_on_player'=>1,
					'default_payment_flag'=>MANUAL_ONLINE_PAYMENT,
					'enabled_deposit'=>1,
					'enabled_withdrawal'=>1,
				];
				//insert banktype
				$this->insertData('banktype', $data);
			}
		}
	}

	public function getBanktypeList($type = null) {
		$this->db->select('bankTypeId,bankName,external_system_id,enabled_withdrawal,enabled_deposit')
			->from('banktype')->where('status', self::STATUS_ACTIVE);
		if (!empty($type)) {
			if($type=='deposit'){
				$this->db->where('enabled_deposit', self::DB_TRUE);
			}else{
				//withdrawal
				$this->db->where('enabled_withdrawal', self::DB_TRUE);
			}
		}

		return $this->runMultipleRowArray();
	}

    public function getAvailableBankTypeList($type = null){
    	$this->load->model(['playerbankdetails']);
        $banktype_list = $this->getBanktypeList($type);
        if(empty($banktype_list)){
            return [];
        }

        $banktype_list_filterd = [];
        foreach($banktype_list as $banktype){
            if(!empty($banktype['external_system_id'])){
                continue;
            }

            if($banktype['bankTypeId'] == $this->playerbankdetails->BANK_TYPE_ALIPAY){
                continue;
            }

            if($banktype['bankTypeId'] == $this->playerbankdetails->BANK_TYPE_WECHAT){
                continue;
            }

            $banktype_list_filterd[] = $banktype;
        }

        return $banktype_list_filterd;
    }

    public function isEnabledDeposit($bank_type){
		$this->db->select('bankTypeId, bankName, enabled_withdrawal, enabled_deposit');
        $this->db->from('banktype');
        $this->db->where('bankTypeId', $bank_type);
        $this->db->where('status', self::STATUS_ACTIVE);
        $row = $this->runOneRow();

        return (empty($row)) ? FALSE : (($row->enabled_deposit) ? TRUE : FALSE);
    }

    public function isEnabledWithdrawal($bank_type){
		$this->db->select('bankTypeId, bankName, enabled_withdrawal, enabled_deposit');
        $this->db->from('banktype');
        $this->db->where('bankTypeId', $bank_type);
        $this->db->where('status', self::STATUS_ACTIVE);
        $row = $this->runOneRow();

        return (empty($row)) ? FALSE : (($row->enabled_withdrawal) ? TRUE : FALSE);
    }

    static public function getBankIcon($bankIcon){
  //       $CI = &get_instance();
		// return (!empty($bankIcon)) ? $CI->utils->getSystemUrl("player",'/upload/system/bank-icon/'.$bankIcon) : $CI->utils->imageUrl('no.png');
		 $CI = &get_instance();
		return (!empty($bankIcon)) ? $CI->utils->getBankIcon($bankIcon): $CI->utils->imageUrl('no.png');
    }

    static public function renderBankEntry($bank_type_id, $bank_entry_name, $bank_icon = NULL, $payment_account_icon = NULL, $extra_info = NULL){
        if(!empty($payment_account_icon)){
            $class_name = 'b-icon-custom';
            $img_tag = '<img src="' . $payment_account_icon . '" />';
        }elseif(!empty($bank_icon)){
            $class_name = 'b-icon-custom';
            $img_tag = '<img src="' . self::getBankIcon($bank_icon) . '" />';
        }else{
            $class_name = 'b-icon bank_' . $bank_type_id;
            $img_tag = '';
        }

        $extra_info = (empty($extra_info)) ? [] : $extra_info;
        $extra_info['class'] = (!isset($extra_info['class']) && empty($extra_info['class'])) ? $class_name : $class_name . ' ' . $extra_info['class'];

        $attributes = '';
        foreach($extra_info as $attr => $value){
            $attributes .= ' ' . $attr . '="' . $value . '"';
        }

        return <<<HTML
<span${attributes}>${img_tag}${bank_entry_name}</span>
HTML;
}

	public function checkIfBankCodeExist($bank_code, $input_bank_type_id) {
		$bank_type_id = $this->getBankTypeIdByBankcode($bank_code);
		if((is_null($bank_type_id)) || ($bank_type_id == $input_bank_type_id) || (empty($bank_code))) {
			return false;
		}
		return true;
	}

	public function getBankTypeIdByBankcode($bank_code){
		$this->db->select('bankTypeId')->from('banktype')->where('bank_code', $bank_code);

		return $this->runOneRowOneField('bankTypeId');
	}

	public function getDistinctActivePaymentTypeFlag(){
		$this->db->select('payment_type_flag');
		$this->db->distinct();
		$this->db->from($this->tableName);
		$this->db->where('status', self::STATUS_ACTIVE);
		$this->db->where('enabled_deposit', TRUE);
		$flags = $this->runMultipleRow();
		foreach($flags as $flag) {
			$deposit[] = $flag->payment_type_flag;
		}

		$this->db->select('payment_type_flag');
		$this->db->distinct();
		$this->db->from($this->tableName);
		$this->db->where('status', self::STATUS_ACTIVE);
		$this->db->where('enabled_withdrawal', TRUE);
		$flags = $this->runMultipleRow();
		foreach($flags as $flag) {
			$withdrawal[] = $flag->payment_type_flag;
		}

		$active_flag = array(
			"deposit" => $deposit,
			"withdrawal" => $withdrawal,
		);
        return $active_flag;
	}
}

///END OF FILE////////
