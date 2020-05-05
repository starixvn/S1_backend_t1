<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get game data
 * * Add/Update/Modify game
 * * Get payment status
 * * Truncate table
 * * Sync data
 * * Add/update payment data
 * * Disable/delete payment data
 * * Check if game api is active
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class External_system extends BaseModel {

	protected $tableName = 'external_system';

	function __construct() {
		parent::__construct();
	}

	const SYSTEM_PAYMENT = SYSTEM_PAYMENT;
	const SYSTEM_GAME_API = SYSTEM_GAME_API;

    const MAINTENANCE_FINISH = 0;
    const MAINTENANCE_START = 1;
    const MAINTENANCE_OR_PAUSE_SYNCING_ON_PROGRESS = 1;

    const MAINTENANCE_STATUS_PENDING = 1;
    const MAINTENANCE_STATUS_IN_MAINTENANCE = 2;
    const MAINTENANCE_STATUS_DONE = 3;
    const MAINTENANCE_STATUS_CANCELLED = 4;

    const MAINTENANCE_INTERVAL_TIME = 2; //2mins

    const GAME_API_HISTORY_ACTION_ADD = 'add_cred';
    const GAME_API_HISTORY_ACTION_UPDATE = 'update_cred';
    const GAME_API_HISTORY_ACTION_DELETE = 'delete_cred';
    const GAME_API_HISTORY_ACTION_UNDER_MAINTENANCE = 'under_maintenance';
    const GAME_API_HISTORY_ACTION_FINISH_MAINTENANCE = 'finish_maintenance';
    const GAME_API_HISTORY_ACTION_BLOCKED = 'blocked';
    const GAME_API_HISTORY_ACTION_UNBLOCKED ='unblocked';
    const GAME_API_HISTORY_ACTION_PAUSED_SYNC = 'paused_sync';
    const GAME_API_HISTORY_ACTION_RESUMED_SYNC = 'resumed_sync';

	// public function getPT() {
	// 	return $this->getOneRowById(PT_API);
	// }

	// public function getAG() {
	// 	return $this->getOneRowById(AG_API);
	// }

	/**
	 * overview : get game name by id
	 *
	 * @param $id
	 * @return int
	 */
	public function getNameById($id) {
		$sys = $this->getSystemById($id);
		if ($sys) {
			return $sys->system_code;
		} else {
			return $id;
		}
	}

	/**
	 * overview : get system by id
	 *
	 * @param $id
	 * @return array
	 */
	public function getSystemById($id) {
		return $this->getOneRowByIdWithCache($id);
	}

	/**
	 * overview : get predefined system by id
	 *
	 * @param $id
	 * @return array
	 */
	public function getPredefinedSystemById($id) {
		$query = $this->db->get_where('external_system_list', array('id' => $id));
		return $this->getOneRow($query);
	}

	/**
	 * overview : get predefined system by id
	 *
	 * @param $id
	 * @return array
	 */
	public function getRowArrayFromPredefinedSystemById($id) {
		$query = $this->db->get_where('external_system_list', array('id' => $id));
		return $this->getOneRowArray($query);
	}

	/**
	 * overview : insert data
	 *
	 * @param $data
	 * @return array
	 */
	public function addRecord($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * overview : update data
	 *
	 * @param $data
	 * @return array
	 */
	public function updateRecord($data) {
		$this->db->where('last_sync_id', $data['last_sync_id']);
		return $this->db->update($this->tableName, $data);
	}

	/**
	 * overview : update last sync id
	 *
	 * @param $gamePlatformId
	 * @param $data
	 * @return mixed
	 */
	public function updateLastSyncId($gamePlatformId, $data) {
		$this->db->where('id', $gamePlatformId);
		return $this->db->update($this->tableName, $data);
	}

	/**
	 * overview : check if file exist
	 *
	 * @param $filename
	 * @return bool
	 */
	public function isFileExists($filename) {
		$qry = $this->db->get_where($this->tableName, array('last_sync_id' => $filename));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : get payment status
	 *
	 * @return array
	 */
	public function getPaymentSystems() {
		$this->db->where('system_type', self::SYSTEM_PAYMENT);
		$qry = $this->db->get($this->tableName);
		return $this->getMultipleRow($qry);
	}

	/**
	 * overview : get payment system
	 *
	 * @param bool|false $addEmpty
	 * @return mixed
	 */
	public function getPaymentSystemsKV($addEmpty = false) {
		$rows = $this->getPaymentSystems();
		$needTranslate = true;
		return $this->insertEmptyToHeader($this->convertRowsToKV($rows, 'id', 'system_name', $needTranslate, $addEmpty), '', lang('select.empty.line'));
	}

	/**
	 * overview : get withdraw payment system
	 *
	 * @return array
	 */
	public function getWithdrawPaymentSystemsKV() {
		$this->db->where('system_type', self::SYSTEM_PAYMENT);
		/*Abstract_payment_api::ALLOW_WITHDRAW; Abstract_payment_api::ALLOW_DEPOSIT | Abstract_payment_api::ALLOW_WITHDRAW*/
		$this->db->where_in('allow_deposit_withdraw', array(2, 3));

		$qry = $this->db->get($this->tableName);
		$rows = $this->getMultipleRow($qry);
		return $this->convertRowsToKV($rows, 'id', 'system_code');
	}

	/**
	 * get all game apis
	 *
	 * @return array
	 */
	public function getAllGameApis() {
		$sql = "SELECT id, system_code FROM $this->tableName where system_type=?";
		return $this->db->query($sql, array(self::SYSTEM_GAME_API))->result_array();
	}

	public function getAllGameApisAndStatus() {
		$this->db->select('id, system_code, status')->from($this->tableName)
		    ->where('system_type', self::SYSTEM_GAME_API);
		return $this->runMultipleRowArray();
		// $sql = "SELECT id, system_code, status FROM $this->tableName where system_type=?";
		// return $this->db->query($sql, array(self::SYSTEM_GAME_API))->result_array();
	}

	/**
	 * get all game apis
	 *
	 * @return array
	 */
	public function getSystemCodeMapping() {
		$result = array();
		$allGameApis = $this->getAllGameApisAndStatus();

		if($allGameApis){
			foreach ($allGameApis as $allGameApi){
				if ($allGameApi['status'] == self::STATUS_NORMAL){
					$result[$allGameApi['id']] = $allGameApi['system_code'];
				}
			}
		}

		return $result;
	}

	/**
	 * overview : get all system api by type
	 *
	 * @param $type
	 * @return array
	 */
	public function getAllSystemApiByType($type) {

		$key='getAllSystemApiByType-'.$type;
		$rows=$this->getFromTempCache($key);
		if($rows===null){
			$qry = $this->db->get_where($this->tableName, array('system_type' => $type));
			$rows= $qry->result_array();
			$this->saveToTempCache($key, $rows);
		}

		return $rows;
	}

	/**
	 * overview : get all system game api
	 *
	 * @return array
	 */
	public function getAllSytemGameApi() {
		return $this->getAllSystemApiByType(self::SYSTEM_GAME_API);
	}

	/**
	 * overview : get all system payment api
	 *
	 * @return array
	 */
	public function getAllSystemPaymentApi() {
		return $this->getAllSystemApiByType(self::SYSTEM_PAYMENT);
	}

	/**
	 * overview : get all active system api by type
	 *
	 * @param $type
	 * @return array
	 */
	private $_allActiveSystemApi = array();
	public function getAllActiveSystemApiByType($type, $all = false) {
		if(empty($this->_allActiveSystemApi)) {
			if(!$all){
				$this->db->where('status', self::STATUS_NORMAL);
			}
			$qry = $this->db->get('external_system');
			$rows = $qry->result_array();
			$apis = array();

			if(!empty($rows)){
				foreach ($rows as $row) {
					if(!array_key_exists($row['system_type'], $this->_allActiveSystemApi)) {
						$this->_allActiveSystemApi[$row['system_type']] = array();
					}
					if (!$this->utils->isDisabledApi($row['id'])) {
						$this->_allActiveSystemApi[$row['system_type']][] = $row;
					}
				}
			}
		}

		return array_key_exists($type, $this->_allActiveSystemApi) ? $this->_allActiveSystemApi[$type] : array();
	}

	/**
	 * overview : get all active system game api
	 *
	 * @return array
	 */
	public function getAllActiveSytemGameApi() {
		return $this->getAllActiveSystemApiByType(self::SYSTEM_GAME_API);
	}

	/**
	 * overview : get all active system payment api
	 *
	 * @return array
	 */
	public function getAllActiveSystemPaymentApi() {
		return $this->getAllActiveSystemApiByType(self::SYSTEM_PAYMENT);
	}

	/**
	 * overview : check if game api is active
	 *
	 * @param $gameApi
	 * @return bool
	 */
	public function isGameApiActive($gameApi) {
		if ($this->utils->isDisabledApi($gameApi)) {
			return false;
		}
		$this->db->select('status')->from($this->tableName)->where('id', $gameApi);
		// $sql = "SELECT status FROM $this->tableName WHERE id = " . $gameApi . "";
		$status = $this->runOneRowOneField('status');
		return $status == self::STATUS_NORMAL;
	}

	/**
	 * overview : add game api
	 *
	 * @param $data
	 * @return bool
	 */
	public function addGameApi($data) {

		try {

			$this->db->insert($this->tableName, $data);

			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());
			} else {
				return array('success'=> true, 'api_id'=> $this->db->insert_id());
			}

		} catch (Exception $e) {
			return FALSE;
		}

	}

	/**
	 * overview : update game api
	 *
	 * @param $data
	 * @param $gameApiId
	 * @return bool
	 */
	public function updateGameApi($data, $gameApiId) {

		try {
			$this->db->where('id', $gameApiId);
			$this->db->update($this->tableName, $data);

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
	 * overview : update game api
	 *
	 * @param $ids
	 * @return bool
	 */
	public function deleteGameApi($ids) {

		try {

			$this->db->where_in('id', $ids);
			$this->db->delete($this->tableName);

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
	 * overview : disable game api
	 *
	 * @param $data
	 * @param $paymentApiId
	 * @return bool
	 */
	public function disableAbleGameApi($data, $paymentApiId) {

		try {

			$this->db->where('id', $paymentApiId);
			$this->db->update($this->tableName, $data);

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
	 * overview : add payment api
	 *
	 * @param $data
	 * @return bool
	 */
	public function addPaymentApi($data) {

		try {

			$this->db->insert($this->tableName, $data);

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
	 * overview update payment api
	 *
	 * @param $data
	 * @param $gameApiId
	 * @return bool
	 */
	public function updatePaymentApi($data, $gameApiId) {

		try {

			$this->db->where('id', $gameApiId);
			$this->db->update($this->tableName, $data);

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
	 * overview : delete payment api
	 *
	 * @param $ids
	 * @return bool
	 */
	public function deletePaymentApi($ids) {

		try {

			$this->db->where_in('id', $ids);
			$this->db->delete($this->tableName);

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
	 * overview : disable payment api
	 *
	 * @param $data
	 * @param $paymentApiId
	 * @return bool
	 */
	public function disableAblePaymentApi($data, $paymentApiId) {

		try {

			$this->db->where('id', $paymentApiId);
			$this->db->update($this->tableName, $data);

			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());
			} else {

				return TRUE;
			}

		} catch (Exception $e) {
			return FALSE;
		}

	}

// 	/**
// 	 * overview : sync current external system
// 	 *
// 	 * @param null $gamePlatformId
// 	 * @return bool
// 	 */
// 	public function syncCurrentExternalSystem($gamePlatformId = null) {
// 		$success = true;
// 		//copy external_system_list to external_system
// 		$apiIds = $this->utils->getAllCurrentApiList();

// 		if (!empty($apiIds)) {

// 			$this->load->model(array('users'));
// 			$superAdmin = $this->users->getSuperAdmin();

// 			// $this->startTrans();
// 			if ($gamePlatformId != null) {
// 				// only keep gamePlatformId
// 				$apiIdStr = $gamePlatformId;
// 			} else {
// 				$apiIdStr = implode(',', $apiIds);
// 			}

// 			//update back
// 			$updateBackSql = <<<EOD
// update external_system_list as esl join external_system as es on esl.id=es.id
// set
// esl.last_sync_datetime=es.last_sync_datetime,
// esl.last_sync_id=es.last_sync_id,
// esl.last_sync_details=es.last_sync_details,
// esl.live_url= es.live_url,
// esl.sandbox_url= es.sandbox_url,
// esl.live_key= es.live_key,
// esl.sandbox_key= es.sandbox_key,
// esl.live_secret= es.live_secret,
// esl.sandbox_secret= es.sandbox_secret,
// esl.live_account= es.live_account,
// esl.sandbox_account= es.sandbox_account,
// esl.live_mode= es.live_mode,
// esl.second_url=es.second_url,
// esl.game_platform_rate=es.game_platform_rate,
// esl.extra_info=es.extra_info,
// esl.sandbox_extra_info=es.sandbox_extra_info
// EOD;

// 			$this->runRawUpdateInsertSQL($updateBackSql);

// 			//delete old external_system
// 			$this->db->empty_table('external_system');

// 			//insert external system
// 			$sql = <<<EOD

// 		insert into external_system(id, system_name,note,last_sync_datetime,last_sync_id,
// 			last_sync_details,system_type,live_url,sandbox_url,live_key,
// 			live_secret,sandbox_key,sandbox_secret,live_mode,second_url,
// 			sandbox_account,live_account,system_code,status,class_name,
// 			local_path, manager,game_platform_rate, extra_info, sandbox_extra_info)
// select id, system_name,note,last_sync_datetime,last_sync_id,
// last_sync_details,system_type,live_url,sandbox_url,live_key,
// live_secret,sandbox_key,sandbox_secret,live_mode,second_url,
// sandbox_account,live_account,system_code,status,class_name,
// local_path, manager,game_platform_rate, extra_info, sandbox_extra_info from external_system_list
// where id in ($apiIdStr)

// EOD;

// 			$this->runRawUpdateInsertSQL($sql);

// 			$this->db->empty_table('game');
// 			$gameType = SYSTEM_GAME_API;
// 			//add to game
// 			$sql = <<<EOD
// insert into game(gameId,game)
// select id,system_code from external_system_list
// where id in ($apiIdStr) and system_type={$gameType}
// EOD;
// 			$this->runRawUpdateInsertSQL($sql);

// 			// $this->syncToBanktype($superAdmin->userId);

// 			// $this->endTrans();
// 			$success = true; //$this->succInTrans();
// 		}

// 		return $success;
// 	}

	/**
	 * overview : set last sync id
	 *
	 * @param $id
	 * @param $lastSyncId
	 * @return bool
	 */
	public function setLastSyncId($id, $lastSyncId) {
		//$this->startTrans();

		$last_sync_datetime = $this->utils->getNowDateTime()->format('Y-m-d H:i:s');

		$data = array('last_sync_id' => $lastSyncId, 'last_sync_datetime' => $last_sync_datetime );
		$this->updateRow($id, $data);
		$this->db->where('id', $id)->update('external_system_list', $data);

		//$this->endTrans();
		//return $this->succInTrans();
		return true;
	}

	/**
	 * overview : get last sync id
	 *
	 * @param $id
	 * @return array
	 */
	public function getLastSyncId($id) {
		$this->db->select('last_sync_id');
		$qry = $this->db->get_where($this->tableName, array('id' => $id));
		return $this->getOneRowOneField($qry, 'last_sync_id');
	}

	/**
	 * overview : get last sync datetime
	 *
	 * @param $id
	 * @return array
	 */
	public function getLastSyncDatetime($id) {

		$this->startTrans();

		$this->db->select('last_sync_datetime');
		$qry = $this->db->get_where($this->tableName, array('id' => $id));
		return $this->getOneRowOneField($qry, 'last_sync_datetime');

		$this->endTrans();

	}

	/**
	 * overview : update last sync date time
	 *
	 * @param $id
	 * @param $last_sync_datetime
	 * @return bool
	 */
	public function updateLastSyncDatetime($id, $last_sync_datetime) {
		$this->startTrans();
		$data = array('last_sync_datetime' => $last_sync_datetime);
		$this->updateRow($id, $data);
		$this->db->where('id', $id)->update('external_system', $data);
		$this->endTrans();
		return $this->succInTrans();
	}

	/**
	 * overview set last sync date
	 *
	 * @param $id
	 * @param $lastSyncDate
	 * @return bool
	 */
	public function setLastSyncDate($id, $lastSyncDate) {
		if (!isset($lastSyncDate)) {
			$lastSyncDate = time();
		}
		$this->startTrans();
		$lastSyncDate = mysqli_real_escape_string($lastSyncDate);
		$data = array('last_sync_datetime' => date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $lastSyncDate))));
		$this->updateRow($id, $data);
		$this->db->where('id', $id)->update('external_system', $data);
		$this->endTrans();
		return $this->succInTrans();
	}

	/**
	 * overview sync to bank type
	 *
	 * @param $adminUserId
	 * @return bool
	 */
	public function syncToBanktype($adminUserId) {
		$this->load->model(array('banktype', 'payment_account'));
		//only insert/update
		$ids = $this->utils->getAllCurrentPaymentSystemList();
		foreach ($ids as $systemId) {
			$this->banktype->syncBankType3rdParty('payment.type.' . $systemId, $systemId, $adminUserId);
			$this->payment_account->syncPaymentGateway($systemId, $adminUserId);
		}

		return true;
	}

	/**
	 * overview : get extra information
	 *
	 * @param $rowArray
	 * @param $extraInfoKey
	 * @param null $defaultValue
	 * @return null
	 */
	public function getExtraInfo($rowArray, $extraInfoKey, $defaultValue = null) {
		# based on whether it's live_mode, provide corresponding extra_info field. Use live field by default.
		$extraInfoJson = (!isset($rowArray['live_mode']) || $rowArray['live_mode']) ? $rowArray['extra_info'] : $rowArray['sandbox_extra_info'];

		# Try to decode extraInfo json string
		$extraInfo = json_decode(stripcslashes($extraInfoJson), true) ?: array();

		# Return the query result, or empty string if not found
		return array_key_exists($extraInfoKey, $extraInfo) ? $extraInfo[$extraInfoKey] : $defaultValue;
	}

	/**
	 * overview : sync game table
	 */
	public function syncGameTable() {
		$this->db->query('delete from game');
		$this->db->query('insert into game(gameId,game) select id, system_code from external_system where status=?', array(self::STATUS_NORMAL));
	}

	/**
	 * overview : truncate table sync
	 *
	 * @param $secret_key
	 * @return array
	 */
	public function truncateTablesSync($secret_key) {
		if ($secret_key == 'Ch0wK1ing&M@ng!n@s@l') {
			$this->db->truncate($this->tableName);
			return array('success' => 1);
		}
		return array('success' => 0);
	}

	/**
	 * overview : get last sync id by game platform
	 *
	 * @param $gamePlatformId
	 * @return null
	 */
	public function getLastSyncIdByGamePlatform($gamePlatformId) {
		$this->db->select('last_sync_id');
		$qry = $this->db->get_where($this->tableName, array('id' => $gamePlatformId));
		return $this->getOneRowOneField($qry, 'last_sync_id');
	}

	/**
	 * overview : filter active game api
	 *
	 * @param $gameApiArr
	 * @return array
	 */
	public function filterActiveGameApi($gameApiArr) {

		$ids = array();

		if ( ! empty($gameApiArr)) {

			$this->db->from('external_system');
			$this->db->where('status', self::STATUS_NORMAL);
			$this->db->where_in('id', $gameApiArr);

			$rows = $this->runMultipleRowArray();

			if ( ! empty($rows)) {

				$rows = array_filter($rows, function($row) {
					return ! $this->utils->isDisabledApi($row['id']);
				});

				$ids = array_column($rows, 'id');

			}

		}

		return $ids;
	}

	public function updateBankList($bankList, $apiId){

		$systemInfo = $this->getSystemById($apiId);

		$extraInfoField = $systemInfo->live_mode ? 'extra_info' : 'sandbox_extra_info' ;
		$extraInfoJson = $systemInfo->live_mode ? $systemInfo->extra_info : $systemInfo->sandbox_extra_info;
		$extraInfo = $this->utils->decodeJson($extraInfoJson);

		// $this->utils->debug_log($apiId.' extraInfo before', $extraInfo);
		//save to bank_info_list
		$extraInfo['bank_info_list']=$bankList;

		// $this->utils->debug_log($apiId.' extraInfo', $extraInfo);

		$this->db->where('id', $apiId);
		$this->db->update('external_system', [$extraInfoField=>$this->utils->encodeJson($extraInfo, true) ]);

		return true;
	}

	private function getStringFrom($val){
		$val=strval($val);

		return $val=='NULL' ? null : $val;
	}

	public function syncExternalSystem(){

		if(!$this->db->table_exists('external_system_list')){
			return;
		}

		//load permissions.json
		//use xml, because extra info is json too
		$xmlFile=APPPATH.'config/external_system_list.xml';

		// $json=file_get_contents($jsonFile);
		$xml=simplexml_load_file($xmlFile);
		// $permissions=$this->utils->decodeJson($json);

		$now=$this->utils->getNowForMysql();

		if(empty($xml)){
			throw new Exception('wrong xml file');
		}
		foreach ($xml->ROW as $row) {
			$data=[
				'id'=>$this->getStringFrom($row->id),
				'system_name'=>$this->getStringFrom($row->system_name),
				'note'=>$this->getStringFrom($row->note),
				'last_sync_datetime'=>$this->getStringFrom($row->last_sync_datetime),
				'last_sync_id'=>$this->getStringFrom($row->last_sync_id),
				'last_sync_details'=>$this->getStringFrom($row->last_sync_details),
				'system_type'=>$this->getStringFrom($row->system_type),
				'live_url'=>$this->getStringFrom($row->live_url),
				'sandbox_url'=>$this->getStringFrom($row->sandbox_url),
				'live_key'=>$this->getStringFrom($row->live_key),
				'live_secret'=>$this->getStringFrom($row->live_secret),
				'sandbox_key'=>$this->getStringFrom($row->sandbox_key),
				'sandbox_secret'=>$this->getStringFrom($row->sandbox_secret),
				'live_mode'=>$this->getStringFrom($row->live_mode),
				'second_url'=>$this->getStringFrom($row->second_url),
				'sandbox_account'=>$this->getStringFrom($row->sandbox_account),
				'live_account'=>$this->getStringFrom($row->live_account),
				'system_code'=>$this->getStringFrom($row->system_code),
				'status'=>$this->getStringFrom($row->status),
				'class_name'=>$this->getStringFrom($row->class_name),
				'local_path'=>$this->getStringFrom($row->local_path),
				'manager'=>$this->getStringFrom($row->manager),
				'game_platform_rate'=>$this->getStringFrom($row->game_platform_rate),
				'extra_info'=>$this->getStringFrom($row->extra_info),
				'sandbox_extra_info'=>$this->getStringFrom($row->sandbox_extra_info),
				'allow_deposit_withdraw'=>$this->getStringFrom($row->allow_deposit_withdraw),
			];

			// $this->utils->debug_log('row', $data);

			$this->db->select('id')->from('external_system_list')->where('id', $data['id']);
			if(!$this->runExistsResult()){
				$this->insertData('external_system_list', $data);
			}else{
				$this->db->set($data);
				$this->db->where('id', $data['id']);
				$this->runAnyUpdate('external_system_list');
			}

			//search by id
			// $this->db->select('funcId')->from('functions')->where('funcId', $perm['funcId']);
			// if(!$this->runExistsResult()){
			// 	// $perm['status']=self::DB_TRUE;
			// 	// $perm['sort']=$perm['funcId'];
			// 	// $perm['createTime']=$now;
			// 	// //insert
			// 	// $this->insertData('functions', $perm);
			// 	$funcCode=$perm['funcCode'];
			// 	$funcName=$perm['funcName'];
			// 	$funcId=$perm['funcId'];
			// 	$parentId=$perm['parentId'];
			// 	$addToAllAdmin=true;
			// 	$this->initFunction($funcCode, $funcName, $funcId, $parentId, $addToAllAdmin);
			// }
		}
	}

	public function updateExtraInfoByGamePlatformId($gamePlatformId, $extra_data) {

		$this->db->select('extra_info');
		$qry 	= $this->db->get_where($this->tableName, array('id' => $gamePlatformId));
		$result 	= $this->getOneRowOneField($qry, 'extra_info');
		$result 	= json_decode($result,true);

		// $qry['is_agent_created'] = 0;
		foreach ($extra_data as $key => $value) {
			$result[$key] = $value;
		}

		$data = array(
			"extra_info" 			=> json_encode($result,true),
			"sandbox_extra_info"	=> json_encode($result,true),
		);
		$update =$this->db->where('id', $gamePlatformId);
		$update = $this->db->update($this->tableName, $data);
		return $update;
	}

	public function isAllEnabledApi($arr) {

		$this->db->select('id')->from($this->tableName)->where_in('id', $arr)->where('status', self::STATUS_NORMAL);
		$rows = $this->runMultipleRowArray();

		return count($arr) == count($rows);

	}

	public function isAnyEnabledApi($arr) {

		$this->db->select('id')->from($this->tableName)->where_in('id', $arr)->where('status', self::STATUS_NORMAL);
		$rows = $this->runMultipleRowArray();
		if( empty($rows) ){
			$rows = [];
		}
		return count($rows)>0;

	}


	public function getAllSystemApis() {
		// $sql = "SELECT id, system_code FROM $this->tableName where system_type=?";
		$this->db->from($this->tableName);
		return $this->runMultipleRowArray();
		// return $this->db->query($sql, array(self::SYSTEM_GAME_API))->result_array();
	}

	public function getSystemName($id){


        $this->db->select('system_name');
        $this->db->where('id', $id);
        $qry = $this->db->get($this->tableName);
        return $this->getOneRowOneField($qry, 'system_name');

    }

    public function getGameOddsForCommissionMap(){

    	$this->db->from('external_system')->where('system_type', self::SYSTEM_GAME_API);

    	$rows=$this->runOneRowArray();

    	if(!empty($rows)){
    		foreach ($rows as $row) {
    			$apiId=$row['id'];
    			$api=$this->utils->loadExternalSystemLibObject($apiId);
    			$greater_than_odds_for_commission=$api->getSystemInfo('greater_than_odds_for_commission');
    			//ignore 0/null/empty
    			if(!empty($greater_than_odds_for_commission)){
    				$map[$apiId]=$greater_than_odds_for_commission;
    			}
    		}
    	}

    }

    public function getPaymentInPopWindowDefaultScrolltopById($id){
        $id = intval($id);
        if($id>0){
            $rs = $this->db->select('extra_info')->from($this->tableName)->where('id', $id)->get();
            $extra_info = $this->getOneRowOneField($rs, 'extra_info');
	        $jsonArr = json_decode($extra_info,true);

	        if(is_array($jsonArr) && (array_key_exists("popInWindowDefaultScrolltop",$jsonArr))){
	                $height = $jsonArr['popInWindowDefaultScrolltop'];
	                return intval($height);
	        }else{
	                //add value in json
	                $height='0';
	                return $height;
	        }
        }
        else{
            return '0';
        }
    }

    /**
     * overview : call by ajax when update the field
     *
     * @param $postAry array
     * @return true or false;
     */
    public function triggerPopWindow($postAry){
        $id = $postAry['eid'];
        $popInWindow = intval($postAry['popFlg']);
        $rs = $this->db->select('extra_info')->from($this->tableName)->where('id', $id)->get();
        $extra_info = $this->getOneRowOneField($rs, 'extra_info');
        $extInfo = json_decode($extra_info,true);
        $extInfo['popInWindow'] = $popInWindow;
        $dataExtInfo =json_encode($extInfo);
        $data['extra_info']=$dataExtInfo;

        $this->db->where('id', $id);
        $rt = $this->db->update($this->tableName, $data);
        if($rt){
            return 1;
        }else{
            return 0;
        }


	}

	/**
	 * overview : add tele api
	 *
	 * @param $data
	 * @return bool
	 */
	public function addTeleApi($data) {
		try {
			$this->db->insert($this->tableName, $data);
			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());
			} else {
				return TRUE;
			}
		} catch (Exception $e) {
			return FALSE;
		}
	}

	public function updateTeleApi($data, $teleApiId) {
		try {
			$this->db->where('id', $teleApiId);
			$this->db->update($this->tableName, $data);
			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());
			} else {
				return TRUE;
			}
		} catch (Exception $e) {
			return FALSE;
		}
	}

	public function getActivedGameApiList(){

		$this->db->select('id')->from('external_system')->where('system_type', self::SYSTEM_GAME_API)
			->where('status', self::STATUS_NORMAL);

		$rows=$this->runMultipleRowArray();

		$list=[];
		foreach ($rows as $row) {
			$list[]=$row['id'];
		}
		return $list;

	}

	public function getAgentActivedGameApiList($agent_id) {

        $query = $this->db->select('external_system.id')
        	->from('external_system')
        	->join('agency_agent_game_platforms','agency_agent_game_platforms.game_platform_id = external_system.id')
        	->where('external_system.system_type', self::SYSTEM_GAME_API)
        	->where('external_system.status', self::STATUS_NORMAL)
			->where('agency_agent_game_platforms.agent_id', $agent_id)
			->get();

		$rows = $query->result_array();
		$rows = array_column($rows, 'id');

		return $rows;

	}

    public function isGameApiMaintenance($gameApi) {
        $this->db->select('maintenance_mode')->from($this->tableName)->where('id', $gameApi);
        $mode = $this->runOneRowOneField('maintenance_mode');
        return $mode == self::MAINTENANCE_OR_PAUSE_SYNCING_ON_PROGRESS;
    }

	public function setToMaintenanceMode($data, $gameId) {
		try {
			$this->db->where('id', $gameId);
			$this->db->update($this->tableName, $data);
			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());
			} else {
				return TRUE;
			}
		} catch (Exception $e) {
			return FALSE;
		}
	}

    public function setToMaintenanceOrPauseMode($data, $gameId) {
        try {
            $this->db->where('id', $gameId);
            $this->db->update($this->tableName, $data);
            if ($this->db->_error_message()) {
                throw new Exception($this->db->_error_message());
            } else {
                return TRUE;
            }
        } catch (Exception $e) {
            return FALSE;
        }
    }

    public function getGameApiMaintenanceOrPauseSyncing($field){
        $query = $this->db
            ->from($this->tableName)
            ->where($field, self::MAINTENANCE_OR_PAUSE_SYNCING_ON_PROGRESS)
            ->get();

        return $query->result_array();
    }

	public function isApiDisabledOrDisabledPaymentAccountByOrderId($apiId, $orderId) {
		$this->db->select('status, allow_deposit_withdraw')->from('external_system')->where('id', $apiId);
		$row = $this->runOneRowArray();
		// $sql = "SELECT status FROM $this->tableName WHERE id = " . $gameApi . "";
		$status = $row['status'];
		$allow_deposit_withdraw = $row['allow_deposit_withdraw'];
		$disabledApi=$status != self::STATUS_NORMAL;
		$disabledPaymentAccount = false;

		if( ($allow_deposit_withdraw == '1') && (substr($orderId, 0, 1) != 'W') ) {	//only check payment_account when deposit
			$this->db->select('payment_account.status')->from('sale_orders')
			  ->join('payment_account', 'payment_account.id=sale_orders.payment_account_id')
			  ->where('sale_orders.id', $orderId);

			//if lost,empty or another status means disabled
			$disabledPaymentAccount=$this->runOneRowOneField('status')!=self::STATUS_NORMAL;
		}

		return $disabledApi || $disabledPaymentAccount;
	}

    /**
     * getAllActiveT1GameApiForSync
     * @return array [$id=>$row]
     */
    public function getAllActiveT1GameApiForSync(){
    	$result=[];

    	$enabled_t1_directly_sync_api_list=$this->utils->getConfig('enabled_t1_directly_sync_api_list');

    	if(empty($enabled_t1_directly_sync_api_list)){
    		return $result;
    	}

    	//active and not maintenance and not t1 lottery
		$this->db->from($this->tableName)->where('status', self::STATUS_NORMAL)
		  ->where('local_path', 'game_platform/t1_api')
		  ->where('system_type', self::SYSTEM_GAME_API)
		  ->where('system_name !=', 'T1LOTTERY_API')
		  ->where_in('id', $enabled_t1_directly_sync_api_list)
		  ->where('maintenance_mode !=', self::MAINTENANCE_OR_PAUSE_SYNCING_ON_PROGRESS)
		  ->where('pause_sync', self::DB_FALSE);
		$rows = $this->runMultipleRowArray();

		if(!empty($rows)){
			foreach ($rows as $row) {
				if (!$this->utils->isDisabledApi($row['id'])) {
					$result[$row['id']]=$row;
				}
			}
		}

    	return $result;

    }

	public function isApiDisabled($apiId) {
		$this->db->select('status')->from($this->tableName)->where('id', $apiId);
		$row = $this->runOneRowArray();
		$status = $row['status'];

		if($status != self::STATUS_NORMAL)
			return true;
		else
			return false;
	}

	public function searchSystemCodeByList($list){
		$result=[];
		$this->db->select('system_code')->from($this->tableName)->where_in('id', $list);
		$rows=$this->runMultipleRowArray();
		foreach ($rows as $row) {
			$result[]=lang($row['system_code']);
		}

		return $result;
	}

	private $_allActiveSystemApiIDList = array();
	/**
	 * get id list by type
	 * @param  int $type
	 * @param  object $db
	 * @return array
	 */
	public function getAllActiveSystemApiIDByType($type, $db=null) {
		if(empty($db)){
			$db=$this->db;
		}
		if(empty($this->_allActiveSystemApiIDList)) {
			$db->select('id, system_type')->from('external_system')->where('status', self::STATUS_NORMAL);
			$rows = $this->runMultipleRowArray($db);

			if(!empty($rows)){
				foreach ($rows as $row) {
					if(!array_key_exists($row['system_type'], $this->_allActiveSystemApiIDList)) {
						$this->_allActiveSystemApiIDList[$row['system_type']] = array();
					}
					if (!$this->utils->isDisabledApi($row['id'])) {
						$this->_allActiveSystemApiIDList[$row['system_type']][] = $row['id'];
					}
				}
			}
		}

		return array_key_exists($type, $this->_allActiveSystemApiIDList) ? $this->_allActiveSystemApiIDList[$type] : array();
	}

	/**
	 * overview : validate entry game maintenance schedule
	 *
	 * @param $gameplatformid $startdate $enddate
	 * @return bool
	 */

	public function validateEntryGameMaintenanceSchedule($gameplatformid,$startdate,$enddate)
	{
		$sql ="SELECT id FROM game_maintenance_schedule WHERE game_platform_id = ? AND (start_date BETWEEN ? AND ? OR end_date BETWEEN ? AND ? ) ";
		$query=$this->db->query($sql, array($gameplatformid,$startdate,$enddate,$startdate,$enddate));
		return $query->num_rows();
	}

	/**
	 * overview : add new game maintenance schedule
	 *
	 * @param $data
	 * @return bool
	 */
	public function addNewGameMaintenanceSchedule($data) {
		try {
			$this->db->insert('game_maintenance_schedule', $data);
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
	 * get game maintenance schedule details
	 *
	 * @return array
	 */
	public function getGameMaintenanceScheduleById($id)
	{
		$sql = $this->db->get_where('game_maintenance_schedule', array('id' => $id));
		return $sql->result();
	}

	/**
	 * overview : edit details game maintenance schedule
	 *
	 * @param $data
	 * @return bool
	 */
	public function editDetailsGameMaintenanceSchedule($data,$id) {
		try {
			$this->db->where('id', $id);
			$this->db->update('game_maintenance_schedule', $data);
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
	 * overview : get Game Api Id Game Maintenance Schedule
	 *
	 * @param $id
	 * @return int
	 */
	public function getGameApiIdGameMaintenanceSchedule($id)
	{
		$this->db->select('game_platform_id');
    	$this->db->from('game_maintenance_schedule');
    	$this->db->where('id',$id);
    	return $this->db->get()->row()->game_platform_id;
	}

	/**
	 * get all data by status set
	 *
	 * @return array
	 */
	public function getMaintenanceScheduleByStatus($status){
		$query = $this->db->get_where('game_maintenance_schedule',array('status'=>$status));
		return $this->getMultipleRowArray($query);
	}

	/**
	 * get total row by status
	 *
	 * @return int
	 */
	public function checkIfEmptyResultInGameMaintenance($status){
			$query = $this->db->get_where('game_maintenance_schedule',array('status'=>$status));
			return $query->num_rows();
	}

	/**
	 * Retrieve all actively syncing APIs thru Java
	 *
	 * @return array
	 */
	public function getAllApiActivelySyncedThruJava(){

		$this->db->select("ac.game_platform_id as id");
		$this->db->from('api_config ac');
		$this->db->where('ac.sync_enabled', self::DB_TRUE);

		$result = $this->runMultipleRowArray();

		return $result ?: array();

	}

	/**
	 * Retrieve all api with system name and status
	 *
	 * @return array
	 */
	public function getAllGameApisWithSystemNameAndStatus(){

		$this->db->select("es.id as id, es.system_name as system_name, es.status as status, ");
		$this->db->from('external_system es');
		$this->db->where('es.system_type', self::SYSTEM_GAME_API);

		$result = $this->runMultipleRowArray();

		return $result ?: array();

	}

	public function mapGameApi($prefix=null){
		$result = [];
		$allGameApis = $this->getAllGameApisAndStatus();

		if(!empty($allGameApis)){
			foreach ($allGameApis as $allGameApi){
				$result[$allGameApi['id']] = [
					'system_code'=>$allGameApi['system_code'],
					'prefix'=>$prefix,
				];
			}
		}

		return $result;
	}

	public function addOrUpdateGameApi($apiInfo, $dynamicClassInfo=null) {
		//search id
		$this->db->select('id')->from($this->tableName)->where('id', $apiInfo['id']);
		$row=$this->runOneRowArray();
		//add fixed field
		$apiInfo['system_type']=self::SYSTEM_GAME_API;
		$apiInfo['created_on']=$this->utils->getNowForMysql();
		$apiInfo['class_key']=null;
		$success=false;
		$id=null;
		if(!empty($row)){
			$id=$row['id'];
			$this->db->set($apiInfo);
			$this->db->where('id', $id);
			//update
			$success=!!$this->runAnyUpdate($this->tableName);
			// $this->utils->printLastSQL();
		}else{
			// insert
			$id=$this->runInsertData($this->tableName, $apiInfo);
			$success=!empty($id);
		}

		if($success){
			if(!empty($dynamicClassInfo)){
				//try sync class table
				$this->syncDynamicClass(self::DYNAMIC_CLASS_TYPE_GAME,
					$dynamicClassInfo['class_key'], $dynamicClassInfo['class_name'],
					$dynamicClassInfo['class_content']);
				//update external system
				$this->db->set('class_key', $dynamicClassInfo['class_key'])->where('id', $id);
				$this->runAnyUpdate($this->tableName);
				unset($dynamicClassInfo);
			}
		}

		return $success;

	}

	public function addGameAPIPermissionToAgent($gameApiId, $agentId){

		$this->db->select('id')->from('agency_agent_game_platforms')
			->where('game_platform_id', $gameApiId)->where('agent_id', $agentId);
		$row=$this->runOneRowArray();
		if(empty($row)){
			$data=['game_platform_id'=>$gameApiId, 'agent_id'=>$agentId];
			return $this->runInsertData('agency_agent_game_platforms', $data);
		}
		return true;

	}

	const DYNAMIC_CLASS_CACHE_PREFIX='__dynamic-class';
	const DYNAMIC_CLASS_TYPE_GAME=1;
	const DYNAMIC_CLASS_TYPE_PAYMENT=2;
	const DYNAMIC_CLASS_TYPE_PROMO_RULE=3;

	/**
	 *
	 * sync dynamic class to db
	 *
	 * @param  int $classType
	 * @param  string $classKey
	 * @param  string $classContent
	 * @return
	 */
	public function syncDynamicClass($classType, $classKey, $className, $classContent){
		$success=false;
		if(!$this->utils->getConfig('enabled_dynamic_class')){
			return $success;
		}

		$this->db->select('id')->from('dynamic_class_lib')->where('unique_key', $classKey);
		$id=$this->runOneRowOneField('id');
		$data=[
			'class_type'=>$classType,
			'class_name'=>$className,
			'class_content'=>$classContent,
			'unique_key'=>$classKey,
			'md5_sum'=>md5($classContent),
			'updated_at'=>$this->utils->getNowForMysql(),
		];
		if(empty($id)){
			$data['created_at']=$this->utils->getNowForMysql();
			//insert
			$success=$this->runInsertData('dynamic_class_lib', $data);
		}else{
			//update
			$this->db->set($data)->where('id', $id);
			$success=$this->runAnyUpdate('dynamic_class_lib');
		}
		//clear cache
		$cacheKey=self::DYNAMIC_CLASS_CACHE_PREFIX.'-'.$classKey;
		$this->utils->deleteCache($cacheKey);
		unset($classContent);
		unset($data);

		return $success;
	}

	private $objectCachePerRequest=[];

	public function loadDynamicClassAsGameAPI($classKey, $enabledCache=false){
		$classType=self::DYNAMIC_CLASS_TYPE_GAME;
		//pre load abstract game api
		require_once dirname(__FILE__) . '/../libraries/game_platform/abstract_game_api.php';
		return $this->loadDynamicClass($classType, $classKey, $enabledCache);
	}

	public function loadDynamicClassAsPaymentAPI($classKey, $enabledCache=false){
		$classType=self::DYNAMIC_CLASS_TYPE_PAYMENT;
		//pre load abstract payment api
		require_once dirname(__FILE__) . '/../libraries/payment/abstract_payment_api.php';
		return $this->loadDynamicClass($classType, $classKey, $enabledCache);
	}

	/**
	 *
	 * load dynamic class
	 *
	 * @param  int $classType
	 * @param  string $classKey
	 * @return
	 */
	public function loadDynamicClass($classType, $classKey, $enabledCache=false){
		if(!$this->utils->getConfig('enabled_dynamic_class')){
			return null;
		}

		if(isset($this->objectCachePerRequest[$classKey])){
			$this->utils->debug_log('found object by '.$classKey);
			return $this->objectCachePerRequest[$classKey];
		}

		$cacheKey=self::DYNAMIC_CLASS_CACHE_PREFIX.'-'.$classKey;
		$row=null;
		//cache or not
		if($enabledCache){
			//try load it from cache
			$class_content=$this->utils->getTextFromCache($cacheKey);
			if(!empty($class_content)){
				$row=$this->utils->getJsonFromCache($cacheKey.'-info');
				if(!empty($row)){
					$row['class_content']=$class_content;
					//check md5 sum
					$md5=md5($class_content);
					if($md5!=$row['md5_sum']){
						//reset
						$row=null;
						$this->utils->deleteCache($cacheKey);
						$this->utils->deleteCache($cacheKey.'-info');
						$this->utils->debug_log('reset cache for class', $row);
					}
					$this->utils->debug_log('found class from cache by '.$cacheKey);
				}
			}
		}
		if(empty($row)){
			//try load it from db
			$this->db->select('class_name, class_content, md5_sum')->from('dynamic_class_lib')->where('unique_key', $classKey);
			$row=$this->runOneRowArray();
			if($enabledCache && !empty($row)){
				//write it to cache
				$this->utils->saveTextToCache($cacheKey, $row['class_content']);
				$this->utils->saveJsonToCache($cacheKey.'-info', ['class_name'=>$row['class_name'], 'md5_sum'=>$row['md5_sum']]);
				$this->utils->debug_log('save class to cache by '.$cacheKey);
			}
		}

		$obj=null;
		if(!empty($row)){
			$class_content=$row['class_content'];
			$class_name=$row['class_name'];
			$md5_sum=$row['md5_sum'];

			if(class_exists($class_name, false)){
				//exists same name class
				$this->utils->error_log('duplicate name of class', $class_name);
				return null;
			}

			//save to file and require
			$filename=$this->utils->createTempFile();
			$succ=file_put_contents($filename, $class_content);
			if($succ!==false){
				include $filename;
				unlink($filename);
				if(class_exists($class_name, false)){
					$obj=new $class_name();
				}else{
					//load failed
					$this->utils->error_log('load tmp file failed', $filename, $class_name);
				}
			}else{
				$this->utils->error_log('write class to tmp file failed', $filename, $class_name);
			}
		}

		if(!empty($obj)){
			//save to cache
			$this->utils->debug_log('save object to cache '.$classKey);
			$this->objectCachePerRequest[$classKey]=$obj;
		}
		return $obj;
	}

	/**
	 * overview : update game api history
	 *
	 * @param $data
	 * @param $gameApiId
	 * @return bool
	 */
	public function addToGameApiHistory($data){
		try {

			$this->db->insert('game_api_update_history', $data);

			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());
			} else {

				return TRUE;
			}
		} catch (Exception $e) {
			return FALSE;
		}
	}


    const ENCRYPT_METHOD = 'aes-256-ctr';
    const ENCRYPT_TAG_LENGTH = 16;
    public function encryptSecrets($data){
        $key = $this->utils->getConfig('payment_api_key');
        $ivLength = openssl_cipher_iv_length(self::ENCRYPT_METHOD);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $ciphertext = openssl_encrypt($data, self::ENCRYPT_METHOD, $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv.$ciphertext);
    }

    public function decryptSecrets($data){
        $key = $this->utils->getConfig('payment_api_key');
        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length(self::ENCRYPT_METHOD);
        $iv = mb_substr($data, 0, $ivLength, '8bit');
        $ciphertext = mb_substr($data, $ivLength, null, '8bit');
        return openssl_decrypt($ciphertext, self::ENCRYPT_METHOD, $key, OPENSSL_RAW_DATA, $iv);
    }
}

///END OF FILE
