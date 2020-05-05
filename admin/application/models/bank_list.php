<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Bank_list
 *
 * General behaviors include
 * * Get bank type code using id
 * * Get bank type short code using id
 * * Get the bank type id using bank type short code
 * * Get bank types as tree formatted filtered by system id
 *
 * @category Payment Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
 
class Bank_list extends BaseModel {

	protected $tableName = 'bank_list';

	function __construct() {
		parent::__construct();
	}
	
	/**
	 * detail: get bank types formatted as tree
	 * 
	 * @param int $systemId system id
	 * @return array
	 */
	public function getBankTypeTree($systemId) {
		$bankTree = array();
		$bankList = array();
		// $bankTypeList = array();
		$this->db->where('external_system_id', $systemId);
		$this->db->order_by('bank_type_order');
		$qry = $this->db->get($this->tableName);
		$rows = $this->getMultipleRow($qry);
		if ($rows) {
			$api = $this->utils->loadExternalSystemLibObject($systemId);
			$prefix = $api->getPrefix();
			// if ($systemId == IPS_PAYMENT_API) {
			// 	$prefix = 'ips';
			// } else if ($systemId == GOPAY_PAYMENT_API) {
			// 	$prefix = 'gopay';
			// }
			$isFirst = true;
			$firstBankRow = null;
			foreach ($rows as $row) {
				$bankShortCode = $row->bank_shortcode;
				$bankName = lang($prefix . '_bank_' . $bankShortCode);
				$bankTypeName = !empty($row->bank_type_code) ? lang($prefix . '_bank_' . $bankShortCode . '_' . $row->bank_type_code) : '';
				$bankList[$bankShortCode] = $bankName;
				$bankTypeInfo = array('bank_name' => $bankName, 'bank_id' => $row->id, 'bank_type_code' => $row->bank_type_code, 'bank_type_name' => $bankTypeName);
				// if (($chooseBank == null && $isFirst) || $chooseBank == $row->bank_shortcode) {
				// 	$bankTypeList[] = $bankTypeInfo;
				// }
				$bankTree[$bankShortCode][] = $bankTypeInfo;
				if ($isFirst) {
					// $firstBankRow = $row;
					$isFirst = false;
				}
			}
		}
		return array($bankList, $bankTree);
	}
	
	/**
	 * detail: get the bank code using id
	 * 
	 * @param int $id bank type id
	 * @return string
	 */
	public function getBankTypeCodeById($id) {
		$row = $this->getOneRowById($id);
		if ($row) {
			return $row->bank_type_code;
		}
		return null;
	}
	
	/**
	 * detail: get the short code of a bank type by id
	 * 
	 * @param int $id bank type id
	 * @return string
	 */
	public function getBankShortCodeById($id) {
		$row = $this->getOneRowById($id);
		if ($row) {
			return $row->bank_shortcode;
		}
		return null;
	}
	
	/**
	 * detail: get the bank type id using a certain bank short code
	 * 
	 * @param int $bankShortcode bank type short code
	 * @return string
	 */
	public function getIdByShortcode($bankShortcode) {
		if (!empty($bankShortcode)) {
			$this->db->from($this->tableName)->where('bank_shortcode', $bankShortcode);
			return $this->runOneRowOneField('id');
		}

		return null;
	}
}

///END OF FILE
