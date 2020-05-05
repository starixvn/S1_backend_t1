<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * http://ogdev.ddns.net:8090/display/OG/PaymentAccount
 *
 *
 */
class Http_request_summary extends BaseModel {

	protected $tableName = 'http_request_summary';

	protected $idField = 'id';

	public function insertBatch( $data ){

		return $this->db->insert_batch($this->tableName, $data);

	}

}

///END OF FILE/////////