<?php
// require_once dirname(__FILE__) . "/../libraries/vendor/autoload.php";

require_once dirname(__FILE__) . '/../controllers/modules/lock_app_module.php';

use Illuminate\Database\Capsule\Manager as Capsule;

if (!class_exists('BaseModel')) {
    /**
     * @property CI_DB_active_record $db
     */
	abstract class BaseModel extends CI_Model
	{

		use lock_app_module;

		const STATUS_NORMAL = 1;
		const STATUS_DISABLED = 2;

		const OLD_STATUS_ACTIVE = 0;
		const OLD_STATUS_INACTIVE = 1;

		const NO_LIMIT = 0;
		const DEFAULT_START_ORDER = 10;

		const DB_TRUE = 1;
		const DB_FALSE = 0;

		const DB_TRUE_STRING='normal';
		const DB_FALSE_STRING='disabled';

		const DB_BOOL_MAP=[
			self::DB_TRUE=>self::DB_TRUE_STRING,
			self::DB_FALSE=>self::DB_FALSE_STRING,
		];

		const DB_BOOL_STR_TO_INT=[
			self::DB_TRUE_STRING=>self::DB_TRUE,
			self::DB_FALSE_STRING=>self::DB_FALSE,
		];

		const SPLIT_NUMBER = 500;

		const FREQUENCRY_ALL = 'all';
		const FREQUENCRY_DAILY = 'daily';
		const FREQUENCRY_WEEKLY = 'weekly';
		const FREQUENCRY_MONTHLY = 'monthly';

		const TRUE = 1;
		const FALSE = 0;

		// default proof_attachment_type
		const Verification_Income = 'income';
		const Verification_Adress = 'address';
		const Verification_Photo_ID = 'photo_id';
		const Verification_Deposit_Withrawal = 'dep_wd';
		const Deposit_Attached_Document = 'deposit';
		const PROFILE_PICTURE = 'profile';

		// default attached remarks
		const Remark_No_Attach = 'no_attach';
		const Remark_Wrong_attach = 'wrong_attach';
		const Remark_Verified = 'verified';
		const Remark_Not_Verified = 'not_verified';

		const player_no_attached_document = "player_no_attached_document";
		const player_depositor = "player_depositor";
		const player_identity_verification = "player_identity_verification";
		const player_valid_documents = "player_valid_documents";
		const player_valid_identity_and_proof_of_address = "player_valid_identity_and_proof_of_address";
		const player_valid_proof_of_income = "player_valid_proof_of_income";

		const zero_total = 0;
		const R1 = 'R1';
		const R2 = 'R2';
		const R3 = 'R3';
		const R4 = 'R4';
		const R5 = 'R5';
		const R6 = 'R6';
		const R7 = 'R7';
		const R8 = 'R8';
		const RC = 'RC';//risk score

		const C6_True = "True";
		const C6_False = "False";

		const player_status_blocked = 1;
		const player_status_suspended = 5;
		// const STATUS_SETTLED = 5;

		const ACTION_ADD = "Add";
	    const ACTION_UPDATE = "Update";
	    const ACTION_DELETE = "Delete";

	    //Kingrich Scheduler Status
		const PENDING = 1;
		const ONGOING = 2;
		const PAUSED = 3;
		const STOPPED = 4;
		const DONE = 5;

		const MESSAGE_TYPE_SUCCESS = 1;
		const MESSAGE_TYPE_ERROR = 2;
		const MESSAGE_TYPE_WARNING = 3;

		private $readOnlyDB = null;
		private $secondReadDB=null;

		public function __construct()
		{
			parent::__construct();
			$this->CI = $this;
			$this->load->database();
			// $this->db->reconnect();
			// $this->reconnectDB();
			// $this->utils->initDebugBarData();
		}

		public function reconnectDB()
		{
			if (isset($this->db)) {
				$this->db->reconnect();
				// $this->utils->debug_log('close db first');
				// $this->db->close();
				// $this->utils->debug_log('init db again');
				// $this->db->initialize();
			}
		}

		public function closeDB()
		{
			if (isset($this->db)) {
				$this->db->close();
			}
		}

		public function openDB()
		{
			if (isset($this->db)) {
				$this->db->initialize();
			}
		}

		public function getNowForMysql()
		{
			return $this->utils->getNowForMysql();
		}

		public function getTodayForMysql()
		{
			return $this->utils->getTodayForMysql();
		}

		public function runExistsResult()
		{
			$qry = $this->db->get();
			return $this->existsResult($qry);
		}

		public function existsResult($qry)
		{
			return $qry && $qry->num_rows() > 0;
		}

		public function runOneRow($db=null)
		{
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$qry = $db->get();
			$row = $this->getOneRow($qry);
			unset($qry);
			return $row;
		}

		public function getOneRow($qry)
		{
			if ($qry && $qry->num_rows() > 0) {
				$row=$qry->row();
				$qry->free_result();
				return $row;
			}
			return null;
		}

		public function runOneRowArray($db=null)
		{
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$qry = $db->get();
			$row = $this->getOneRowArray($qry);
			unset($qry);
			return $row;
		}

		public function getOneRowArray($qry)
		{
			if ($qry && $qry->num_rows() > 0) {
				$row=$qry->row_array();
				$qry->free_result();
				return $row;
			}
			return [];
		}

		public function runMultipleRowArray($db=null)
		{
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$qry = $db->get();
			$rows= $this->getMultipleRowArray($qry);
			unset($qry);
			return $rows;
		}

		public function getMultipleRowArray($qry)
		{
			if ($qry && $qry->num_rows() > 0) {
				$rows=$qry->result_array();
				$qry->free_result();
				return $rows;
			}
			return [];
		}

		public function runMultipleRow($db=null)
		{
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$qry = $db->get();
			$rows= $this->getMultipleRow($qry);
			unset($qry);
			return $rows;
		}

		public function getMultipleRow($qry)
		{
			if ($qry && $qry->num_rows() > 0) {
				$rows=$qry->result();
				$qry->free_result();
				return $rows;
			}
			return [];
		}

		public function runMultipleRowArrayUnbuffered($db=null)
		{
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$qry = $db->get_unbuffered_mysql();
			$rows= $this->getMultipleRowArrayUnbuffered($qry);

			unset($qry);
			return $rows;
		}

		public function getMultipleRowArrayUnbuffered($qry)
		{
			if (!empty($qry)) {
				$rows=$qry->result_array_unbuffered();
				$qry->free_result();
				return $rows;
			}
			return [];
		}

		public function runMultipleRowObjectUnbuffered($db=null)
		{
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$qry = $db->get_unbuffered_mysql();
			$rows =  $this->getMultipleRowObjectUnbuffered($qry);

			unset($qry);
			return $rows;
		}

		public function getMultipleRowObjectUnbuffered($qry)
		{
			if (!empty($qry)) {
				$rows=$qry->result_object_unbuffered();
				$qry->free_result();
				return $rows;
			}
			return [];
		}

		public function runOneRowOneFieldById($idVal, $fldName, $db=null)
		{
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$db->select($fldName)->where($this->getIdField(), $idVal)->from($this->tableName);
			$qry = $db->get();
			$val= $this->getOneRowOneField($qry, $fldName);
			unset($qry);
			return $val;
		}

		public function runOneRowOneField($fldName, $db=null)
		{
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$qry = $db->get();
			$val= $this->getOneRowOneField($qry, $fldName);
			unset($qry);
			return $val;
		}

		public function getOneRowOneField($qry, $fldName)
		{
			$row = $this->getOneRow($qry);
			if ($row) {
				$fldValue=$row->$fldName;
				//free memory
				unset($row);

				return $fldValue;
			}
			return null;
		}

		public function initStatusField()
		{
			if (!isset($this->statusField)) {
				$this->statusField = 'status';
			}
		}

		public function initIdField()
		{
			if (!isset($this->idField)) {
				$this->idField = 'id';
			}
		}

		public function getOneRowArrayById($id, $checkStatus = false, $db=null)
		{
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			if (!empty($id)) {
				if ($checkStatus) {
					$this->addDefaultStatusWhere($db);
				}
				$db->where($this->getIdField(), $id);
				$qry = $db->get($this->tableName, 1);

				$rlt= $this->getOneRowArray($qry);
				unset($qry);
				return $rlt;

			} else {
				return [];
			}
		}

		public function getOneRowById($id, $checkStatus = false, $db=null)
		{
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			if (!empty($id)) {
				if ($checkStatus) {
					$this->addDefaultStatusWhere($db);
				}
				$db->where($this->getIdField(), $id);
				$qry = $db->get($this->tableName, 1);

				$rlt= $this->getOneRow($qry);
				unset($qry);
				return $rlt;
			} else {
				return null;
			}
		}

		public function getOneRowByField($fldName, $fldValue, $db=null)
		{
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			if ($fldName) {
				return $this->getOneRow($db->get_where($this->tableName, array($fldName => $fldValue), 1));
			} else {
				return null;
			}
		}

		public function insertRow($data, $db=null) {
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$qry=$db->insert($this->tableName, $data);

			if($qry===false){
				return false;
			}

			return $db->insert_id();
		}

		public function updateRow($id, $data, $db=null) {
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$db->where($this->getIdField(), $id);
			$qry=$db->update($this->tableName, $data);

			if($qry===false){
				return false;
			}

			return $this->db->affected_rows();
		}

		public function runUpdate($data, $db=null) {
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$qry=$db->update($this->tableName, $data);
			// return $this->db->affected_rows();

			if($qry===false){
				return false;
			}

			unset($qry);
			return true;
		}

		public function runAnyUpdate($tableName, $db=null) {
			return $this->runAnyUpdateWithoutResult($tableName, $db);
			// $this->db->update($tableName);
			// return $this->getResultOfUpdate();
		}

		public function runAnyUpdateWithResult($tableName, $db=null) {
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$qry=$db->update($tableName);

			if($qry===false){
				return false;
			}

			unset($qry);
			return $this->getResultOfUpdate(true, $db);
		}

		public function runAnyUpdateWithoutResult($tableName, $db=null) {
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$qry=$db->update($tableName);

			if($qry===false){
				return false;
			}

			unset($qry);
			// return $this->getResultOfUpdate();
			return true;
		}

		public function getResultOfUpdate($returnRows = true, $db=null) {
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			if ($returnRows) {
				return $db->affected_rows();
			} else {
				return true;
			}
		}

		public function getIdField()
		{
			$this->initIdField();
			return $this->tableName . '.' . $this->idField;
		}

		public function getStatusField()
		{
			$this->initStatusField();
			return $this->tableName . '.' . $this->statusField;
		}

		public function addDefaultStatusWhere($db=null)
		{
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$db->where($this->getStatusField() . ' !=', self::STATUS_DISABLED);
		}

		public function softDelete($id)
		{
			return $this->updateRow($id, array($this->getStatusField() => self::STATUS_DISABLED));
		}

		public function batchSoftDeleteCustom($tableName, $idField, $idArr)
		{
			$qry=$this->db->where_in($idField, $idArr)->update($tableName, array($this->getStatusField() => self::STATUS_DISABLED));

			if($qry===false){
				return false;
			}
			unset($qry);
			return true;
		}

		public function batchSoftDelete($idArr)
		{
			return $this->batchSoftDeleteCustom($this->tableName, $this->getIdField());
			// $this->db->where_in($this->getIdField(), $idArr)->update($this->tableName, array($this->getStatusField() => self::STATUS_DISABLED));
		}

		public function softDeleteWithDate($id)
		{
			return $this->updateRow($id, array($this->initDeletedAtField() => $this->utils->getNowForMysql()));
		}

		public function convertRowsToArray($rows, $valField, $needTranslate = false)
		{
			$data = array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					if (isset($row->$valField) && !empty($row->$valField)) {
						$v = $row->$valField;
						if (!empty($v) && $needTranslate) {
							$v = lang($v);
						}
						$data[] = $v;
					}
				}
			}
			return $data;
		}

		public function convertArrayRowsToArray($rows, $valField, $needTranslate = false)
		{
			$data = array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					if (isset($row[$valField]) && !empty($row[$valField])) {
						$v = $row[$valField];
						if (!empty($v) && $needTranslate) {
							$v = lang($v);
						}
						$data[] = $v;
					}
				}
			}
			return $data;
		}

		public function convertRowsToKV($rows, $keyField, $valField, $needTranslate = false, $addempty = false)
		{
			$kv = array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					if (isset($row->$keyField) && !empty($row->$keyField)) {
						$v = $row->$valField;
						if (!empty($v) && $needTranslate) {
							$v = lang($v);
						}
						$kv[$row->$keyField] = $v;
					}
				}
			}
			if ($addempty) {
				$kv = $this->insertEmptyToHeader($kv, '', lang('select.empty.line'));
			}
			return $kv;
		}

		public function convertArrayRowsToKV($rows, $keyField, $valField, $needTranslate = false, $addempty = false)
		{
			$kv = array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					if (isset($row[$keyField]) && !empty($row[$keyField])) {
						$v = $row[$valField];
						if (!empty($v) && $needTranslate) {
							$v = lang($v);
						}
						$kv[$row[$keyField]] = $v;
					}
				}
			}
			if ($addempty) {
				$kv = $this->insertEmptyToHeader($kv, '', lang('select.empty.line'));
			}
			return $kv;
		}

		public function insertEmptyToHeader($kv, $emptyValue, $emptyLabel)
		{
			return $this->utils->insertEmptyToHeader($kv, $emptyValue, $emptyLabel);
		}

		public function startTrans($db=null){
			if(empty($db)){
				$db=$this->db;
			}
			$db->trans_start();
		}

		public function endTrans($db=null){
			if(empty($db)){
				$db=$this->db;
			}
			$db->trans_complete();
		}

		public function endTransWithSucc($db=null){
			$this->endTrans($db);
			return $this->succInTrans($db);
		}

		public function rollbackTrans($db=null){
			if(empty($db)){
				$db=$this->db;
			}
			$db->trans_rollback();
		}

		public function isErrorInTrans($db=null){
			if(empty($db)){
				$db=$this->db;
			}
			return $db->trans_status() === FALSE;
		}

		public function succInTrans($db=null){

			return !$this->isErrorInTrans($db);
		}

		public function convertCurrency($amount, $fromCurrency)
		{
			//from to default currency
			return $amount;
		}

		public function editData($tableName, $whereArr, $updateDataArr)
		{
			$qry=$this->db->update($tableName, $updateDataArr, $whereArr);
			return $qry!==false;
			// return $this->db->affected_rows();
		}

		/**
		 * insert any data
		 * @param  string $tableName
		 * @param  array $data data
		 * @return integer last insert id
		 */
		public function insertData($tableName, $data, $db=null) {
			if(empty($db)){
				$db=$this->db;
			}
			$qry=$db->insert($tableName, $data);

			if($qry===false){
				return false;
			}

			unset($qry);
			return $this->db->insert_id();
		}

		public function updateData($fieldId, $id, $tableName, $data, $db=null) {
			if(empty($db)){
				$db=$this->db;
			}
			$db->where($fieldId, $id)->set($data);
			return $this->runAnyUpdate($tableName, $db);
		}

		protected function getDeskeyOG()
		{
			return $this->config->item('DESKEY_OG');
		}

		public function initDeletedAtField()
		{
			if (!isset($this->deletedAtField)) {
				$this->deletedAtField = 'deleted_at';
			}
		}

		public function getDeletedAtField()
		{
			$this->initDeletedAtField();
			return $this->tableName . '.' . $this->deletedAtField;
		}

		protected function ignoreDeleted($deletedAtFiled = null)
		{
			if (!$deletedAtFiled) {
				$deletedAtFiled = $this->getDeletedAtField();
			}
			$this->db->where($deletedAtFiled . ' is null', null, false);
		}

		public function runRawSelectSQL($sql, $params = null)
		{
			if(empty($params)){
				$params=false;
			}
			$qry = $this->db->query($sql, $params);
			$rlt= $this->getMultipleRow($qry);
			unset($qry);
			return $rlt;
		}

		public function runRawSelectSQLArray($sql, $params = null, $db=null)
		{
			if(empty($db)){
				$db=$this->db;
			}
			if(empty($params)){
				$params=false;
			}
			$qry = $db->query($sql, $params);
			$rlt= $this->getMultipleRowArray($qry);
			unset($qry);
			return $rlt;
		}

		public function runRawSelectSQLArrayUnbuffered($sql, $params = null, $db=null)
		{
			if(empty($db)){
				$db=$this->db;
			}
			if(empty($params)){
				$params=false;
			}
			$qry = $db->query($sql, $params, true, MYSQLI_USE_RESULT);
			$rlt= $this->getMultipleRowArrayUnbuffered($qry);
			unset($qry);
			return $rlt;
		}

		public function runRawUpdateInsertSQL($sql, $params = null, $db=null)
		{
			if(empty($db)){
				$db=$this->db;
			}
			if(empty($params)){
				$params=false;
			}
			$db->query($sql, $params, false);
			return $db->affected_rows();
		}

		public function getSecureId($tableName, $fldName, $needUnique = true, $prefix = null, $random_length = 12)
		{
			$secureId = null;
			while (empty($secureId)) {
				$secureId = $prefix . $this->utils->randomString($random_length);

				if (!$secureId) {
					break;
				}

				if ($needUnique) {
					//check unique
					$this->db->select($fldName)->from($tableName)->where($fldName, $secureId);
					if ($this->runExistsResult()) {
						$this->utils->debug_log('exists secure id', $secureId);
						$secureId = null;
					}
				}

			}

			return $secureId;
		}

		public function limitOneRow()
		{
			$this->db->limit(1);
		}

		public function getConfig($name)
		{
			return $this->config->item($name);
		}

		public function isValidDateTimeStr($dateTimeStr)
		{
			return $this->utils->isValidDateTimeStr($dateTimeStr);
		}

		public function getAll()
		{
			$this->db->from($this->tableName);
			return $this->runMultipleRow();
		}

		public function runRawArraySelectSQL($sql, $params = null)
		{
			if(empty($params)){
				$params=false;
			}
			$qry = $this->db->query($sql, $params);
			$rlt= $this->getMultipleRowArray($qry);
			unset($qry);
			return $rlt;
		}

		public $connMap = array();

		public function closeDBSession($conn = null)
		{
			$pid = strval(getmypid());
			if ($conn == null && array_key_exists($pid, $this->connMap)) {
				$conn = $this->connMap[$pid];
			}
			if ($conn != null) {
				$conn->disconnect();
				//try remove from connMap
				$this->connMap[$pid] = null;
			}
		}

		public function getDBSession($name = 'default')
		{
			$pid = strval(getmypid());
			if (array_key_exists($pid, $this->connMap) && $this->connMap[$pid] != null) {
				return $this->connMap[$pid];
			} else {
				$conn = $this->initDBSession($name);
				//add to connMap
				$this->connMap[$pid] = $conn;
				return $conn;
			}
		}

		public function initDBSession($name = 'default')
		{

			switch ($this->getConfig('db.' . $name . '.dbdriver')) {
				case 'mysqli':
					$driver = 'mysql';
					break;

				default:
					$driver = 'mysql';
					break;
			}

			$capsule = new Capsule;
			$capsule->addConnection(array(
				'driver' => 'mysql',
				'host' => $this->getConfig('db.' . $name . '.hostname'),
				'database' => $this->getConfig('db.' . $name . '.database'),
				'username' => $this->getConfig('db.' . $name . '.username'),
				'password' => $this->getConfig('db.' . $name . '.password'),
				'charset' => $this->getConfig('db.' . $name . '.char_set'),
				'collation' => $this->getConfig('db.' . $name . '.dbcollat'),
				'prefix' => $this->getConfig('db.' . $name . '.dbprefix'),
			));

			$capsule->setAsGlobal();

			// $capsule->bootEloquent();
			$conn = Capsule::connection();
			return $conn;
		}

		// return 1 means lock success
		public function transGetLock($trans_key)
		{
			$this->utils->debug_log('try lock', $trans_key);
			$timeout = $this->config->item('app_lock_timeout');

			$return = false;
			if ($this->utils->getConfig('use_ci_db_to_lock')) {

				$sql = "SELECT GET_LOCK('" . $trans_key . "', " . $timeout . ") as rlt, CONNECTION_ID() as conn_id";
				$qry = $this->db->query($sql);
				$row = $this->getOneRowArray($qry);
				$conn_id = null;
				if ($row) {
					$return = $row['rlt'] == '1';
					$conn_id = $row['conn_id'];
				}
				if (!$return) {
					$this->utils->error_log('lock', $trans_key, 'result', $return, 'conn_id', $conn_id);
				}

				$this->utils->debug_log('lock', $trans_key, 'result', $return, 'conn_id', $conn_id);
			} else {

				$conn = $this->getDBSession('readonly');
				// $tmpRlt = $conn->selectOne('select CONNECTION_ID() as conn_id');
				// $this->utils->debug_log('conn 1', $tmpRlt->conn_id);
				// $tmpRlt = $conn->selectOne('select CONNECTION_ID() as conn_id');
				// $this->utils->debug_log('conn 2', $tmpRlt->conn_id);

				$sql = "SELECT GET_LOCK('" . $trans_key . "', " . $timeout . ") as rlt, CONNECTION_ID() as conn_id ";
				$qryRlt = $conn->selectOne($sql);
				$conn_id = null;
				// $this->utils->debug_log($rlt);
				if (!empty($qryRlt)) {
					$return = $qryRlt->rlt == '1';
					$conn_id = $qryRlt->conn_id;
				}
				$this->utils->debug_log('lock', $trans_key, 'result', $return, 'conn_id', $conn_id);

			}
			return $return;

			// $this->utils->debug_log('try lock', $trans_key);
			// $timeout = $this->config->item('app_lock_timeout');
			// $sql = "SELECT GET_LOCK('" . $trans_key . "', " . $timeout . ") as lck";
			// $qry = $this->db->query($sql);
			// return $this->getOneRowOneField($qry, 'lck');

		}

		public function transReleaseLock($trans_key)
		{
			$this->utils->debug_log('try release lock', $trans_key);

			$return = false;

			if ($this->utils->getConfig('use_ci_db_to_lock')) {
				$sql = "SELECT RELEASE_LOCK('" . $trans_key . "') as rlt, CONNECTION_ID() as conn_id";
				$qry = $this->db->query($sql);
				$row = $this->getOneRowArray($qry);
				$conn_id = null;
				if ($row) {
					$return = $row['rlt'] == '1';
					$conn_id = $row['conn_id'];
				}
				if (!$return) {
					$this->utils->error_log('release lock', $trans_key, 'result', $return, 'conn_id', $conn_id);
				}

				$this->utils->debug_log('release lock', $trans_key, 'result', $return, 'conn_id', $conn_id);

			} else {

				$conn = $this->getDBSession('readonly');
				$sql = "SELECT RELEASE_LOCK('" . $trans_key . "') as rlt, CONNECTION_ID() as conn_id";
				$qryRlt = $conn->selectOne($sql);
				$conn_id = null;

				if (!empty($qryRlt)) {
					$return = $qryRlt->rlt == '1';
					$conn_id = $qryRlt->conn_id;
				}
				$this->closeDBSession($conn);
				$this->utils->debug_log('release lock', $trans_key, 'result', $return, 'conn_id', $conn_id);

			}

			return $return;

			// $this->utils->debug_log('try release lock', $trans_key);
			// $sql = "SELECT RELEASE_LOCK('" . $trans_key . "') as rlt";
			// $qry = $this->db->query($sql);
			// return $this->getOneRowOneField($qry, 'rlt');
			// $result = $this->db->query($sql)->row_array();
			// return array_values($result)[0];
		}

		/**
		 * Agent only
		 *
		 * @param  [type] $actionType      [description]
		 * @param  [type] $agentId         [description]
		 * @param  [type] $transactionId   [description]
		 * @param  [type] $amount          [description]
		 * @param  [type] $saleOrderId     [description]
		 * @param  [type] $playerPromoId   [description]
		 * @param  [type] $subWalletId     [description]
		 * @param  [type] $walletAccountId [description]
		 * @return [type]                  [description]
		 */
		public function recordAgentAfterActionWalletBalanceHistory($actionType,
																   $agentId, $transactionId, $amount, $saleOrderId = null, $playerPromoId = null,
																   $subWalletId = null, $walletAccountId = null, $gamePlatformId = null)
		{

			$this->load->model(array('wallet_model'));
			$playerId = null;
			$affId = null;

			return $this->wallet_model->recordWalletBalanceHistory(Wallet_model::USER_TYPE_AGENT, Wallet_model::RECORD_TYPE_AFTER,
				$actionType, $playerId, $affId, $transactionId, $amount, $saleOrderId, $playerPromoId,
				$subWalletId, $walletAccountId, $gamePlatformId, $agentId);

		}

		public function recordAffBeforeActionWalletBalanceHistory($actionType,
																  $affId, $transactionId, $amount, $saleOrderId = null, $playerPromoId = null,
																  $subWalletId = null, $walletAccountId = null)
		{

			$this->load->model(array('wallet_model'));
			$playerId = null;

			return $this->wallet_model->recordWalletBalanceHistory(Wallet_model::USER_TYPE_AFF, Wallet_model::RECORD_TYPE_BEFORE,
				$actionType, $playerId, $affId, $transactionId, $amount, $saleOrderId, $playerPromoId,
				$subWalletId, $walletAccountId);

		}

		public function recordAffAfterActionWalletBalanceHistory($actionType,
																 $affId, $transactionId, $amount, $saleOrderId = null, $playerPromoId = null,
																 $subWalletId = null, $walletAccountId = null)
		{

			$this->load->model(array('wallet_model'));
			$playerId = null;

			return $this->wallet_model->recordWalletBalanceHistory(Wallet_model::USER_TYPE_AFF, Wallet_model::RECORD_TYPE_AFTER,
				$actionType, $playerId, $affId, $transactionId, $amount, $saleOrderId, $playerPromoId,
				$subWalletId, $walletAccountId);

		}

		public function recordPlayerBeforeActionWalletBalanceHistory($actionType,
																	 $playerId, $affId, $transactionId, $amount, $saleOrderId = null, $playerPromoId = null,
																	 $subWalletId = null, $walletAccountId = null, $gamePlatformId = null)
		{

			$this->load->model(array('wallet_model'));

			return $this->wallet_model->recordWalletBalanceHistory(Wallet_model::USER_TYPE_PLAYER, Wallet_model::RECORD_TYPE_BEFORE,
				$actionType, $playerId, $affId, $transactionId, $amount, $saleOrderId, $playerPromoId,
				$subWalletId, $walletAccountId, $gamePlatformId);

		}

		public function recordPlayerAfterActionWalletBalanceHistory($actionType,
																	$playerId, $affId, $transactionId, $amount, $saleOrderId = null, $playerPromoId = null,
																	$subWalletId = null, $walletAccountId = null, $gamePlatformId = null)
		{

			$this->load->model(array('wallet_model'));

			return $this->wallet_model->recordWalletBalanceHistory(Wallet_model::USER_TYPE_PLAYER, Wallet_model::RECORD_TYPE_AFTER,
				$actionType, $playerId, $affId, $transactionId, $amount, $saleOrderId, $playerPromoId,
				$subWalletId, $walletAccountId, $gamePlatformId);
		}

		public function updateBalanceHistoryTransactionId($balanceHistoryId, $transactionId)
		{
			$this->db->set('transaction_id', $transactionId)->where('id', $balanceHistoryId);

			$this->runAnyUpdate('balance_history');

			$this->db->set('balance_history_id', $balanceHistoryId)->where('id', $transactionId);

			return $this->runAnyUpdate('transactions');
		}

		public function fixCharset()
		{
			$tables = $this->db->list_tables();
			foreach ($tables as $table) {
				$fields = $this->db->field_data($table); // $this->db->list_fields($table);
				$sql = "ALTER TABLE `" . $table . "` CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'";
				$this->utils->debug_log($sql);
				$this->db->query($sql);
				// foreach ($fields as $field) {
				// 	if (strtolower($field->type) == 'varchar') {
				// 		$sql = "ALTER TABLE `" . $table . "` CHANGE COLUMN `" . $field->name . "` `" . $field->name . "` " . $field->type . "(" . $field->max_length . ") CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'";
				// 		$this->utils->debug_log($sql);
				// 	}
				// }
			}

		}

		public function activeConnection()
		{
			$this->db->reconnect();
		}

		public function runOneRowById($idVal, $tableName = null, $idFld = null)
		{
			if (empty($tableName)) {
				$tableName = $this->tableName;
			}
			if (empty($idFld)) {
				$idFld = $this->getIdField();
			}
			$this->db->where($idFld, $idVal)->from($tableName);
			$qry = $this->db->get();
			$rlt= $this->getOneRow($qry);
			unset($qry);
			return $rlt;
		}

		public function runOneRowArrayById($idVal, $tableName = null, $idFld = null)
		{
			if (empty($tableName)) {
				$tableName = $this->tableName;
			}
			if (empty($idFld)) {
				$idFld = $this->getIdField();
			}
			$this->db->where($idFld, $idVal)->from($tableName);
			$qry = $this->db->get();
			$rlt= $this->getOneRowArray($qry);
			unset($qry);
			return $rlt;
		}

		public function runOneRowJsonContent($field)
		{

			$content = $this->runOneRowOneField($field);
			if (!empty($content)) {
				return json_decode($content, true);
			}
			return null;
		}

		public function runOneRowJsonContentById($field, $id, $idField, $tableName, $db=null)
		{
			if(empty($db)){
				$db=$this->db;
			}
			$db->from($tableName)->where($idField, $id);

			$content = $this->runOneRowOneField($field, $db);
			if (!empty($content)) {
				return json_decode($content, true);
			}
			return null;
		}

		public function runUpdateJsonContentById($json, $field, $id, $idField, $tableName, $db=null)
		{
			if(empty($db)){
				$db=$this->db;
			}

			$content = json_encode($json);
			$db->set($field, $content)->where($idField, $id);
			return $this->runAnyUpdate($tableName, $db);
		}

		public function existsIndex($tableName, $indexName)
		{
			$sql = "SELECT count(*) cnt FROM information_schema.statistics WHERE table_name = ? AND index_name = ? AND table_schema = database()";
			$rows = $this->runRawSelectSQLArray($sql, [$tableName, $indexName]);
			$cnt = 0;
			if (!empty($rows)) {
				foreach ($rows as $row) {
					$cnt = $row['cnt'];
				}
			}
			return $cnt > 0;
		}

		/**
		 *
		 * addIndex, will check name first
		 *
		 * @param string $tableName
		 * @param string $indexName
		 * @param string $fields
		 */
		public function addIndex($tableName, $indexName, $fields, $unique=false)
		{
			if (!$this->existsIndex($tableName, $indexName)) {
				$this->db->query('CREATE '.($unique ? 'UNIQUE' : '').' INDEX ' . $indexName . ' ON ' . $tableName . '(' . $fields . ')');
			} else {
				$this->utils->debug_log('ignore index ', $indexName, 'table name', $tableName, 'fields', $fields);
			}
		}

		public function addUniqueIndex($tableName, $indexName, $fields)
		{
			$this->addIndex($tableName, $indexName, $fields, true);
		}

		public function dropIndex($tableName, $indexName)
		{
			if ($this->existsIndex($tableName, $indexName)) {
				$this->db->query('DROP INDEX ' . $indexName . ' ON ' . $tableName);
			} else {
				$this->utils->debug_log('ignore index ', $indexName, 'table name', $tableName);
			}
		}

		public function getPlayerGames($promoIdOrArr)
		{
			$this->db->distinct()->select('game_description_id')->from('promorulesgamebetrule');
			if (is_array($promoIdOrArr)) {
				$this->db->where_in('promoruleId', $promoIdOrArr);

			} else {
				$this->db->where('promoruleId', $promoIdOrArr);

			}
			$qry = $this->db->get();
			if ($qry && $qry->num_rows() > 0) {
				foreach ($qry->result_array() as $row) {
					$data[] = $row['game_description_id'];
				}
				return $data;
			}

			return false;
		}

        public function getPlayerGamesKV($promoIdOrArr)
        {
            $this->db->distinct()->select('game_description_id')->from('promorulesgamebetrule');
            if (is_array($promoIdOrArr)) {
                $this->db->where_in('promoruleId', $promoIdOrArr);

            } else {
                $this->db->where('promoruleId', $promoIdOrArr);

            }
            $qry = $this->db->get();
            if ($qry && $qry->num_rows() > 0) {
                foreach ($qry->result_array() as $row) {
                    $data[$row['game_description_id']] = $row['game_description_id'];
                }
                return $data;
            }

            return false;
        }

		public function from($tableName)
		{
			return $this->db->from($tableName);
		}

		public function where($mix, $val, $flag)
		{
			return $this->db->where($mix, $val, $flag);
		}

		public function validateJsonTransArray($arr, $fieldName, &$message)
		{
			if (!empty($arr)) {

				foreach ($arr as $val) {
					if (!empty($val[$fieldName])) {
						if (!$this->validateJsonTrans($val[$fieldName])) {
							$message = 'wrong trans format: ' . $val[$fieldName];
							return false;
						}
					}
				}

			}

			return true;
		}

		public function validateJsonTrans($str)
		{

			$success = true;

			if (substr($str, 0, 6) === '_json:') {

				$jsonStr = substr($str, 6);
				$jsonArr = json_decode($jsonStr, true);
				//empty or found json error
				if (empty($jsonArr) || json_last_error() !== JSON_ERROR_NONE) {
					$success = false;
				}

			}

			return $success;

		}

		public function decodeJsonTrans($str)
		{

			if (substr($str, 0, 6) === '_json:') {

				$jsonStr = substr($str, 6);
				$jsonArr = json_decode($jsonStr, true);
				//empty or found json error
				// if(empty($jsonArr) || json_last_error() !== JSON_ERROR_NONE) {
				// 	$success=false;
				// }
				return $jsonArr;
			}

			return null;
		}

		public function buildMap($rows, $keyField, $valueField)
		{
			$map = [];
			if (!empty($rows)) {
				foreach ($rows as $row) {
					$map[$row[$keyField]] = $row[$valueField];
				}
			}

			return $map;
		}

		public function batchInsertWithLimit($tableName, $rows, $limit = 500)
		{

			$arr = array_chunk($rows, $limit);

			foreach ($arr as $data) {

				//insert and clean
				$this->db->insert_batch($tableName, $data);

			}

			return true;
		}

		// public function getConn()
		// {
		// 	return $this->utils->getConn();
		// }

		// public function getReadConn()
		// {
		// 	return $this->utils->getReadConn();
		// }

		// public function closeAllConn()
		// {
		// 	return $this->utils->closeAllConn();
		// }

		/**
		 *
		 * @param  string $sql if it's empty, use last sql
		 * @param  boolean $use_read read db or not
		 * @param  callable $callback
		 * @return
		 */
		// public function loopRawRows($use_read, callable $make_sql, callable $callback)
		// {
		// 	return $this->utils->loopRawRows($use_read, $make_sql, $callback);
		// }

		// public function executeSqlOnRaw($sql)
		// {

		// 	$success= $this->utils->executeSqlOnRaw($sql);

		// 	return $success;

		// }

		public function getOneRowByIdWithCache($id, $checkStatus = false, $enable_cache=false){

			$row=null;

			$cache_key=$this->tableName.'-'.$id;

			if($enable_cache){
				//load from array
				$row=$this->getFromTempCache($cache_key);
			}

			if($row===null){
				$row=$this->getOneRowById($id, $checkStatus);
				// $this->tmp_cache_list[$key]=$row;
				if($enable_cache){
					$this->saveToTempCache($cache_key, $row);
				}
			}

			return $row;

		}

		public function getOneRowArrayByIdWithCache($id, $checkStatus = false, $enable_cache=false){

			$row=null;

			$cache_key=$this->tableName.'-'.$id;

			if($enable_cache){
				//load from array
				$row=$this->getFromTempCache($cache_key);
			}

			if($row===null){
				$row=$this->getOneRowArrayById($id, $checkStatus);
				// $this->tmp_cache_list[$key]=$row;
				if($enable_cache){
					$this->saveToTempCache($cache_key, $row);
				}
			}

			return $row;

		}

		private $tmp_cache_list=[];
		public function saveToTempCache($key, $val){

			$this->tmp_cache_list[$key]=$val;

			return $val;
		}

		public function getFromTempCache($key){

			$val=null;
			if(isset($this->tmp_cache_list[$key])){
				$val=$this->tmp_cache_list[$key];
			}

			return $val;

		}

		public function checkIfEnabled($features, $fields, $columns){
		if( !$features ){
			$i = 0;
			foreach ($columns as $key => $data) {
				if (in_array($data['alias'], $fields)) {
						unset($columns[$key]);
					}else{
						if(isset($columns[$key]['dt'])){
							$columns[$key]['dt'] = $i++;
						}
					}
				}
			}
			return array_values($columns);
		}

		public function loopRowsFromReadySql($db, callable $callbackable){
			$qry=$db->get();
			if(!empty($qry)){
				return $this->loopRowsFromQuery($qry, $callbackable);
			}

			return false;
		}

		public function loopRowsFromQuery($qry, callable $callbackable){

			$success=!empty($qry) && !empty($callbackable);

			//set $row , if row is null, just quit
			while ($row=$qry->nextRowArray()) {
				//callback function can stop on any row
				$stop=!$callbackable($row);

				if(!$stop){
					break;
				}

				unset($row);
			}

			$qry->free_result();

			return $success;

		}

		/**
		 * maybe different with main db, may delay
		 *
		 */
		public function getReadOnlyDB()
		{
			if ($this->readOnlyDB == null) {
				$_multiple_db=Multiple_db::getSingletonInstance();
				$this->readOnlyDB = $_multiple_db->loadReadOnlyDB();
				$this->utils->setupReadOnlyDB($this->readOnlyDB);
			}

			return $this->readOnlyDB;
		}

		/**
		 * always same with main db, only for read
		 * @return object read db
		 */
		public function getSecondReadDB()
		{
			if ($this->secondReadDB == null) {
				$this->secondReadDB = $this->load->database(SECONDREAD_DATABASE, TRUE);
				$this->utils->setupSecondReadDB($this->secondReadDB);
			}

			return $this->secondReadDB;
		}

		public function runRealDelete($tableName, $db=null){
			if(empty($tableName)){
				return false;
			}
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}

			$qry=$db->delete($tableName);

			if($qry===false){
				return false;
			}

			unset($qry);
			return true;
		}

	    public function generateMD5Sum(array $values, &$originalStr=null){
	    	$originalStr=implode('', $values);
	        return strtolower(md5($originalStr));
	    }

	    public function generateMD5SumOneRow(array $row, array $keys, $floatFields=[], &$originalStr=null){
	        $arr=[];
	        foreach ($keys as $key) {
	            if(array_key_exists($key, $row)){
					if($row[$key]===null){
						$arr[]='';
					}else if(in_array($key, $floatFields)){
	            		$arr[]=sprintf('%.02F', doubleval($row[$key]));
	            	}else{
	                	$arr[]=$row[$key];
	            	}
	            }else{
	            	$this->utils->error_log('wrong key when generate md5 sum, key:'.$key, $row);
	            	throw new Exception('wrong key when generate md5 sum, key:'.$key);
	            }
	        }
	        return $this->generateMD5Sum($arr, $originalStr);
	    }

	    public function processMd5FieldsSetFalseIfNotExist(&$data,$md5Fields,$md5FloatFields){
			foreach ($md5Fields as $key) {
				if (!isset($data[$key])) {
					if (in_array($key, $md5FloatFields)) {
						$data[$key] = self::DB_FALSE;
					}else{
						$data[$key] = null;
					}
				}
			}
			unset($md5Fields);
	    }

	    //===MDB===================================================================
	    private $db_list=[];

	    /**
	     *
	     * foreachMultipleDB, will fetch multiple db, with db trans
	     *
	     * @param callable $callback
	     * @param boolean $readonly
	     * @param array $excludeList
	     * @return array db name=>result
	     *
	     */
	    public function foreachMultipleDB(callable $callback, $readonly=false, $excludeList=null){
	    	$readonly=$readonly && $this->utils->isEnabledReadonlyDB();
	    	$result=[];
	    	$multiple_databases=$this->utils->getConfig('multiple_databases');
	    	if(!empty($multiple_databases)){
	    		$keys=array_keys($multiple_databases);
	    		foreach ($keys as $dbKey) {
	    			if(!empty($excludeList) && in_array($dbKey, $excludeList)){
		    			$this->utils->debug_log('ignore db : '.$dbKey, $excludeList);
	    				continue;
	    			}
    				if($readonly){
    					$dbKey=$dbKey.'_readonly';
    				}
	    			$db=$this->getAnyDBFromMDBByKey($dbKey);
	    			//run key
	    			$this->utils->debug_log('run db : '.$dbKey);
	    			$success=$this->runDBTransOnly($db, $rlt, $callback);
	    			$result[$dbKey]=['success'=>$success, 'result'=>$rlt];
	    		}
	    	}else{
	    		if($readonly){
	    			$db=$this->getReadOnlyDB();
	    		}else{
	    			$db=$this->db;
	    		}
    			$rlt=null;
    			$success=$this->runDBTransOnly($db, $rlt, $callback);
    			$result['default']=['success'=>$success, 'result'=>$rlt];
	    	}

	    	return $result;
	    }

	    /**
	     * run raw sql , only for select
	     * @param  string $sql
	     * @param  array $params
	     * @return array db name=>result
	     */
	    public function runRawSelectSQLArrayOnMDB($sql, $params = null, $readonly=false){
	    	return $this->foreachMultipleDB(function($db, &$result)
	    		use ($sql, $params){
				$qry = $db->query($sql, $params);
				$success=!empty($qry);
				$result= $this->getMultipleRowArray($qry);
				unset($qry);
	    		return $success;
	    	}, $readonly);
	    }

	    /**
	     * run raw sql , only for select, no cache in php
	     * @param  string $sql
	     * @param  array $params
	     * @return array db name=>result
	     */
	    public function runRawSelectSQLArrayUnbufferedOnMDB($sql, $params = null, $readonly=false){
	    	return $this->foreachMultipleDB(function($db, &$result)
	    		use ($sql, $params){
				$qry = $db->query($sql, $params, true, MYSQLI_USE_RESULT);
				$success=!empty($qry);
				$result= $this->getMultipleRowArrayUnbuffered($qry);
				unset($qry);
				return $success;
	    	}, $readonly);
	    }

	    /**
	     * run raw sql , only for insert/update
	     * @param  string $sql
	     * @param  array $params
	     * @return array db name=>result
	     */
		public function runRawUpdateInsertSQLOnMDB($sql, $params = null){
			$readonly=false;
	    	return $this->foreachMultipleDB(function($db, &$result)
	    		use ($sql, $params){
	    		return $this->runRawUpdateInsertSQL($sql, $params, $db);
	    	}, $readonly);
		}

	    /**
	     * run raw sql , only for select
	     * @param  string $sql
	     * @param  array $params
	     * @return array db name=>result
	     */
	    public function runRawSelectSQLArrayWithoutSuperOnMDB($sql, $params = null, $readonly=false){
	    	return $this->foreachMultipleDB(function($db, &$result)
	    		use ($sql, $params){
				$qry = $db->query($sql, $params);
				$success=!empty($qry);
				$result= $this->getMultipleRowArray($qry);
				unset($qry);
	    		return $success;
	    	}, $readonly, [Multiple_db::SUPER_TARGET_DB]);
	    }

	    /**
	     * run raw sql , only for select, no cache in php
	     * @param  string $sql
	     * @param  array $params
	     * @return array db name=>result
	     */
	    public function runRawSelectSQLArrayUnbufferedWithoutSuperOnMDB($sql, $params = null, $readonly=false){
	    	return $this->foreachMultipleDB(function($db, &$result)
	    		use ($sql, $params){
				$qry = $db->query($sql, $params, true, MYSQLI_USE_RESULT);
				$rlt= $this->getMultipleRowArrayUnbuffered($qry);
				unset($qry);
				return $rlt;
	    	}, $readonly, [Multiple_db::SUPER_TARGET_DB]);
	    }

	    /**
	     * run raw sql , only for insert/update
	     * @param  string $sql
	     * @param  array $params
	     * @return array db name=>result
	     */
		public function runRawUpdateInsertSQLWithoutSuperOnMDB($sql, $params = null){
			$readonly=false;
	    	return $this->foreachMultipleDB(function($db, &$result)
	    		use ($sql, $params){
	    		return $this->runRawUpdateInsertSQL($sql, $params, $db);
	    	}, $readonly, [Multiple_db::SUPER_TARGET_DB]);
		}

	    /**
	     * run raw sql , only for select
	     * @param  string $sql
	     * @param  array $params
	     * @return array db name=>result
	     */
	    public function runRawSelectSQLArrayOnSuperDB($sql, $params = null, $readonly=false){
	    	$db=$this->getSuperDBFromMDB($readonly);
	    	return $this->runRawSelectSQLArray($sql, $params, $db);
	    }

	    /**
	     * run raw sql , only for select, no cache in php
	     * @param  string $sql
	     * @param  array $params
	     * @return array db name=>result
	     */
	    public function runRawSelectSQLArrayUnbufferedOnSuperDB($sql, $params = null, $readonly=false){
	    	$db=$this->getSuperDBFromMDB($readonly);
	    	return $this->runRawSelectSQLArrayUnbuffered($sql, $params, $db);
	    }

	    /**
	     * run raw sql , only for insert/update
	     * @param  string $sql
	     * @param  array $params
	     * @return array db name=>result
	     */
		public function runRawUpdateInsertSQLOnSuperDB($sql, $params = null){
	    	$db=$this->getSuperDBFromMDB();
	    	return $this->runRawUpdateInsertSQL($sql, $params, $db);
		}

	    /**
	     * run any , only for select
	     * @param  callable $callback
	     * @param  boolean $readonly
	     * @return array db name=>result
	     */
	    public function runAnyOnSuperDB(callable $callback, $readonly=false){
			return $this->runAnyOnSingleMDB(Multiple_db::SUPER_TARGET_DB, $callback, $readonly);
	    }

	    /**
	     * run any with db
	     * @param  string $sourceDB
	     * @param  callable $callback
	     * @param  boolean $readonly
	     * @return array db name=>result
	     */
	    public function runAnyOnSingleMDB($sourceDB, callable $callback, $readonly=false){
	    	$db=$this->getAnyDBFromMDBByKey($sourceDB, $readonly);
			return $callback($db);
	    }

	    /**
	     * run any with db and trans
	     * @param  callable $callback
	     * @param  mixin $result
	     * @param  boolean $readonly
	     * @return boolean $success
	     */
	    public function runAnyOnSuperDBWithTrans(callable $callback, &$result, $readonly=false){
	    	return $this->runAnyOnSingleMDBWithTrans(Multiple_db::SUPER_TARGET_DB, $callback, $result, $readonly);
	    }

	    /**
	     * run any with db and trans
	     * @param  string $sourceDB
	     * @param  callable $callback
	     * @param  mixin $result
	     * @param  boolean $readonly
	     * @return boolean $success
	     */
	    public function runAnyOnSingleMDBWithTrans($sourceDB, callable $callback, &$result, $readonly=false){
	    	$db=$this->getAnyDBFromMDBByKey($sourceDB, $readonly);
	    	return $this->runDBTransOnly($db, $result, $callback);
	    }

		/**
		 *
		 * foreach mdb without super
		 *
		 * @param  callable $callback
		 * @param  boolean  $readonly
		 * @return array $result
		 */
	    public function foreachMultipleDBWithoutSuper(callable $callback, $readonly=false){
	    	return $this->foreachMultipleDBWithoutSourceDB(Multiple_db::SUPER_TARGET_DB,
	    		$callback, $readonly);
	    }

		/**
		 *
		 * foreach mdb without source db
		 *
		 * @param  callable $callback
		 * @param  boolean  $readonly
		 * @return array $result
		 */
	    public function foreachMultipleDBWithoutSourceDB($sourceDB, callable $callback, $readonly=false){
			$excludeList=[$sourceDB];
	    	return $this->foreachMultipleDB($callback, $readonly, $excludeList);
	    }

	    /**
	     * get one model by any field
	     * @param  string $tableName
	     * @param  string $fieldName
	     * @param  string $val
	     * @param  object $db
	     * @return array
	     */
		public function getOneRowArrayByField($tableName, $fieldName, $val, $db=null){
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			if (!empty($val)) {

				$db->where($fieldName, $val);
				$qry = $db->get($tableName, 1);

				$rlt= $this->getOneRowArray($qry);
				unset($qry);
				return $rlt;

			} else {
				return [];
			}
		}

		public function getSuperDBFromMDB($readonly=false){
	    	$readonly=$readonly && $this->utils->isEnabledReadonlyDB();
			return $this->getAnyDBFromMDBByKey(Multiple_db::SUPER_TARGET_DB, $readonly);
		}

		public function getAnyDBFromMDBByKey($dbKey, $readonly=false){
			if($readonly){
				$dbKey=$dbKey.'_readonly';
			}
			if(!isset($this->db_list[$dbKey])){
    			$this->db_list[$dbKey]=$this->load->database($dbKey, TRUE);
			}

			return $this->db_list[$dbKey];
		}

		public function getInstanceMDB(){
			return Multiple_db::getSingletonInstance();
		}

        public function getDatabaseNameMapWithoutSuperFromMDB($readonly=false){
            return $this->getDatabaseNameMapFromMDB($readonly, [Multiple_db::SUPER_TARGET_DB]);
	    }

        public function getSuperDatabaseNameFromMDB($readonly=false){
            $readonly=$readonly && $this->utils->isEnabledReadonlyDB();
            $dbName=null;
            $multiple_databases=$this->utils->getConfig('multiple_databases');
            if(!empty($multiple_databases)){
                $dbName=$multiple_databases[Multiple_db::SUPER_TARGET_DB][$readonly ? 'readonly' : 'default']['database'];
            }

            return $dbName;
        }

        public function getDatabaseNameMapFromMDB($readonly=false, $excludeList=[]){
            $readonly=$readonly && $this->utils->isEnabledReadonlyDB();
            $dbNameList=[];
			$multiple_databases=$this->utils->getConfig('multiple_databases');
			if(!empty($multiple_databases)){
                foreach ($multiple_databases as $dbKey=>$item) {
                    if(!empty($excludeList) && in_array($dbKey, $excludeList)){
                        $this->utils->debug_log('ignore db : '.$dbKey, $excludeList);
                        continue;
                    }
                    $setting=$item['default'];
                    if($readonly){
                        $setting=$item['readonly'];
                    }
                    $dbNameList[$dbKey]=$setting['database'];
			    }
			}

			return $dbNameList;
	  //   	$readonly=$readonly && $this->utils->isEnabledReadonlyDB();
			// $dbKeyList=[];
	  //   	$multiple_databases=$this->utils->getConfig('multiple_databases');
	  //   	if(!empty($multiple_databases)){
	  //   		$keys=array_keys($multiple_databases);
	  //   		$superDBName=Multiple_db::SUPER_TARGET_DB;
	  //   		foreach ($keys as $dbKey) {
	  //   			if($dbKey==$superDBName){
	  //   				continue;
	  //   			}
	  //   			if($readonly){
	  //   				$dbKeyList[]=$dbKey.'_readonly';
	  //   			}else{
	  //   				$dbKeyList[]=$dbKey;
	  //   			}
	  //   		}
	  //   	}

			// return $dbKeyList;
		}

		/**
		 * insert any data
		 * @param  string $tableName
		 * @param  array $data data
		 * @return integer last insert id
		 */
		public function runInsertData($tableName, $data, $db=null){
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$qry=$db->insert($tableName, $data);

			if($qry===false){
				return false;
			}

			unset($qry);
			return $db->insert_id();
		}

		public function runUpdateData($tableName, $data=null, $db=null){
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			if(!empty($data)){
				$db->set($data);
			}
			$qry=$db->update($tableName);

			if($qry===false){
				return false;
			}

			unset($qry);
			return true;
		}

		/**
		 *
		 * it will change $this->db
		 *
		 * @param  callable $callback
		 * @param  boolean  $readonly
		 * @param  array   $excludeList
		 * @return array $result
		 */
	    // public function foreachMultipleDBToCIDB(callable $callback, $readonly=false, $excludeList=null){

	    // 	$readonly=$readonly && $this->utils->isEnabledReadonlyDB();

	    // 	$result=[];
		   //  $CI=& get_instance();
	    // 	$multiple_databases=$this->utils->getConfig('multiple_databases');
	    // 	if(!empty($multiple_databases)){
	    // 		$keys=array_keys($multiple_databases);
	    // 		$lastDB=$CI->db;
	    // 		foreach ($keys as $db_name) {
	    // 			if(!empty($excludeList) && in_array($db_name, $excludeList)){
		   //  			$this->utils->debug_log('ignore db : '.$db_name);
	    // 				continue;
	    // 			}
    	// 			if($readonly){
    	// 				$db_name=$db_name.'_readonly';
    	// 			}else{
    	// 				$db_name=$db_name;
    	// 			}
	    // 			$CI->db=$this->getAnyDBFromMDBByKey($db_name);
	    // 			//run key
	    // 			// $this->utils->debug_log('run db : '.$db_name);
	    // 			$result[$db_name]=$callback($CI->db);
	    // 		}
	    // 		//restore
	    // 		$CI->db=$lastDB;
	    // 	}else{
	    // 		$lastDB=$CI->db;
	    // 		if($readonly){
					// $this->readOnlyDB = $this->load->database(READONLY_DATABASE, TRUE);
					// $CI->db=$this->readOnlyDB;
	    // 		}
	    // 		//just current db
	    // 		$result['default']=$callback($CI->db);
	    // 		if($readonly){
		   //  		$CI->db=$lastDB;
	    // 		}
	    // 	}

	    // 	return $result;
	    // }

	  //   public function cleanSyncFields($obj, $onlySyncFields){
			// foreach ($obj as $key => $value) {
			// 	if(!in_array($key, $onlySyncFields)){
			// 		unset($obj[$key]);
			// 	}
			// }
			// return $obj;
	  //   }

	    // public function getSyncFieldsByTableName($tableName){
	    // 	$sync_data_on_mdb=$this->utils->getConfig('sync_data_on_mdb');
	    // 	if(isset($sync_data_on_mdb[$tableName])){
	    // 		return $sync_data_on_mdb[$tableName];
	    // 	}
	    // 	return null;
	    // }

	    public function activeItOnOtherMDB($obj){
	    	$active=true;
	    	$enable_object_to_other_mdb=$this->utils->getConfig('enable_object_to_other_mdb');
	    	if(!empty($enable_object_to_other_mdb) && isset($enable_object_to_other_mdb[$obj])){
	    		$active=$enable_object_to_other_mdb[$obj];
	    	}
	    	return $active;
	    }

	    public function getActiveTargetDB(){
	    	$mdb=$this->getInstanceMDB();
	    	return $mdb->getActiveTargetDB();
	    }

	    /**
	     * db trans
	     * @param  object   $db
	     * @param  callable $callbakcable($db, &$result)
	     * @return result
	     */
		public function runDBTransOnly($db, &$result, callable $callbakcable) {
			$success=false;
			$result = null;
			$this->startTrans($db);
			try {

				$success=$callbakcable($db, $result);

				if(!$success){
					//rollback
					$this->rollbackTrans($db);
					$this->utils->error_log('rollback trans because failed on callback function');
				}else{
					$success = $this->endTransWithSucc($db);
					$this->utils->info_log('commit trans:'.$db->getOgTargetDB());
				}

			}catch(Exception $e){
				$success=false;
				//rollback
				$this->rollbackTrans($db);
				$this->utils->error_log('rollback trans because exception', $e);
			}
			return $success;
		}

		public function runBatchInsertWithLimit($db, $tableName, $rows, $limit = 100, &$cnt=0){
			$success=false;
			//only allow 100, because in insert_batch, it's only 100
			$limit=100;
			if(empty($rows)){
				$success=true;
			}else{
				$arr = array_chunk($rows, $limit);
				foreach ($arr as $data) {
					//insert and clean
					$success=$db->insert_batch($tableName, $data);
					if(!$success){
						$this->utils->error_log('run insert batch failed', $tableName, $data);
						break;
					}else{
						$onceCount=$db->affected_rows();
						// $this->utils->debug_log('count of insert row', $onceCount, count($data), $cnt);
						$cnt+=$onceCount;
					}
				}
			}
			return $success;
		}

		public function makeSQLFromCI($db){
			$sql=$db->_compile_select();
			$db->_reset_select();
			return $sql;
		}

		/**
		 * it will return number array, not associative
		 * @param  object  $db
		 * @param  boolean $readonly
		 * @param  boolean $cacheOnMysql if it's true, only cache result on mysql server
		 * @return Raw_db_result
		 */
		public function runRawSelectOnMYSQLReturnNumberArray($db, callable $formatCallback,
			$readonly=false, $cacheOnMysql=false, &$sql=null){
			$rows=null;$fields=null;

			$sql=$this->makeSQLFromCI($db);

			$_multiple_db=Multiple_db::getSingletonInstance();
			$conn=$_multiple_db->rawConnectDB($readonly, $db);
			try{

				// $stmt=mysqli_prepare($conn, $sql);
				// foreach ($params as $param) {
				// 	$type='s';
				// 	if(is_int($param)){
				// 		$type='i';
				// 	}else if(is_double($param)){
				// 		$type='d';
				// 	}
				// 	mysqli_stmt_bind_param($stmt, $type, $param);
				// }

				// mysqli_stmt_execute($stmt);

				// mysqli_stmt_close($stmt);
				$this->utils->debug_log('run sql', $sql, $cacheOnMysql);
                $qry = mysqli_query($conn, $sql, $cacheOnMysql ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT);
                if($qry!==false){
	                $rows = mysqli_fetch_all($qry, MYSQLI_NUM);
	                $fields=mysqli_fetch_fields($qry);
				    mysqli_free_result($qry);
                }else{
                	$this->utils->error_log('run sql error', mysqli_error($conn), $sql);
                }

			    unset($qry);

			}finally{
				if(!empty($conn)){
	                mysqli_close($conn);
				}
			}
			unset($conn);
			if(!empty($rows) && !empty($fields)){
				$headerOfDB=[];
				foreach ($fields as $fld) {
					$headerOfDB[]=$fld->name;
				}
				$this->utils->debug_log('after query rows', count($rows), count($fields));
				foreach ($rows as &$row) {
					if(!$formatCallback($row, $headerOfDB)){
						$this->utils->error_log('format row failed', $row);
						break;
					}
				}
				unset($headerOfDB);
			}
			unset($fields);

			return $rows;
		}

		/**
		 * should create an unique index on unique_key
		 *
		 * @param  string $tableName
		 * @param  array $where
		 * @param  array $params
		 * @param  string $additionalField
		 * @param  string $idField
		 * @return
		 */
		public function mergeTableOverMDBToSuper($tableName, $where, $params, $additionalField='currency_key', $idField='id'){
			if(!$this->utils->isEnabledMDB()){
				return false;
			}
//			$db=$this->getSuperDBFromMDB();
			//get all fields
			$fields=$this->db->list_fields($tableName);
			$fields=array_diff($fields, [$idField, $additionalField]);
			$baseSelFld='`'.implode('`,`', $fields).'`';
			$insertFld='`'.implode('`,`', $fields).'`,`'.$additionalField.'`';
            $dbNameMap=$this->getDatabaseNameMapWithoutSuperFromMDB();
            $superDBName=$this->getSuperDatabaseNameFromMDB();
			$list=$this->utils->getAvailableCurrencyList();
			$currencyKeys=array_keys($list);
			$result=[];
			foreach ($currencyKeys as $currencyKey) {
				$sel=$baseSelFld.',"'.strtoupper($currencyKey).'" as `'.$additionalField.'`';
				$dbName=$dbNameMap[$currencyKey];

				$sql=<<<EOD
replace into {$superDBName}.{$tableName}({$insertFld})
select
{$sel}
from {$dbName}.{$tableName}
where
{$where}
EOD;

				$this->utils->debug_log($sql, $params);

				$cnt=$this->runRawUpdateInsertSQLOnSuperDB($sql, $params);
				$result[$currencyKey]=$cnt;
			}

			return $result;
		}

	    public function removeUselessFieldFromRow($columns, $row){
	        $processedRow=[];
	        for ($i=0; $i < count($row); $i++) {
                if(!isset($columns[$i]['dt'])){
                    continue;
                }
                if(isset($columns[$i]['visible']) && !$columns[$i]['visible']){
                    continue;
                }
	            $processedRow[]=$row[$i];
	        }
	        return $processedRow;
	    }

		/**
		 * run sql and export to csv
		 * @param  object   $db
		 * @param  callable $formatCallback
		 * @param  boolean  $readonly
		 * @param  boolean  $cacheOnMysql
		 * @param  string   &$sql
		 * @return boolean $success
		 */
		public function runRawSelectAndExportToCSV($db, $columns, $headerNameList, $csv_filepath, callable $formatCallback,
				$readonly=false, $cacheOnMysql=false, &$sql=null, $token=null){
			$fields=null;
			$success=true;

        	// $csv_filepath = $this->utils->getRemoteReportPath().'/' . $csv_filename . '.csv';
            //open csv file
        	$fp = fopen($csv_filepath, 'w');
        	if ($fp) {
        		$BOM = "\xEF\xBB\xBF";
                fwrite($fp, $BOM); // NEW LINE
            } else {
                //create report failed
            	$this->utils->error_log('create csv file failed', $csv_filepath);
            	$success=false;
            	return $success;
            }

			$sql=$this->makeSQLFromCI($db);

			$_multiple_db=Multiple_db::getSingletonInstance();
			$conn=$_multiple_db->rawConnectDB($readonly, $db);
			if(empty($conn)){
            	$this->utils->error_log('connect db failed');
            	$success=false;
				return $success;
			}
			try{
				$this->utils->debug_log('run sql when exporting', $sql, $cacheOnMysql);
                $qry = mysqli_query($conn, $sql, $cacheOnMysql ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT);
                if($qry!==false){
	                // $rows = mysqli_fetch_all($qry, MYSQLI_NUM);
	                $fields=mysqli_fetch_fields($qry);
					$headerOfDB=[];
					foreach ($fields as $fld) {
						$headerOfDB[]=$fld->name;
					}
					$this->utils->debug_log('write headerNameList', $headerNameList);
					//write header
					fputcsv($fp, $headerNameList, ',', '"');

	                do{
	                	$row=mysqli_fetch_row($qry);
	                	if($row===null){
	                		break;
	                	}
	                	//format
						if(!$formatCallback($columns, $row, $headerOfDB)){
							$this->utils->error_log('format row failed', $row);
							$success=false;
							break;
						}
						//remove no exporting row
			            $row=$this->removeUselessFieldFromRow($columns, $row);
						//write it to csv
                		fputcsv($fp, $row , ',', '"');
                		//update progress to queue result
                		// $this->queue_result->updateProgress($token, );
	            	}while($row!==null);

				    mysqli_free_result($qry);
				    unset($fields);
				    unset($headerOfDB);
                }else{
                	$this->utils->error_log('run sql error', mysqli_error($conn), $sql);
                	$success=false;
                }
				unset($qry);
			}finally{
				if(!empty($conn)){
	                mysqli_close($conn);
				}
				if(!empty($fp)){
			    	fclose($fp);
				}
			}
			unset($conn);

			return $success;
		}
	    //===MDB===================================================================

		public function fixCollationOnTable($tableName, array $fieldNameList){
			$rows=$this->runRawSelectSQLArray('show full columns from '.$tableName);
			$this->utils->info_log('colums of '.$tableName, $rows);
			if(!empty($rows)){
				foreach ($rows as $row) {
					if(!in_array($row['Field'], $fieldNameList)){
						continue;
					}
					if(substr($row['Type'],0,7)=='varchar' && strtolower($row['Collation'])!='utf8_unicode_ci'){
						//update
						$sql='alter table '.$tableName.' modify column '.$row['Field'].' '.$row['Type'].' CHARACTER SET utf8 collate utf8_unicode_ci';
						$this->utils->info_log('update char collation', $tableName, $row['Field'], $sql);
						$cnt=$this->runRawUpdateInsertSQL($sql);
						$this->utils->debug_log('update sql result:'.$cnt);
					}
				}
			}
		}


		/**
		 * updateOrInsertRowByUniqueField
		 * @param  string $tableName
		 * @param  array $data
		 * @param  callable $preprocess(&$data, $id)
		 * @param  string $uniqueField
		 * @param  string $idField
		 * @param  object $db
		 * @return int $id
		 */
	    public function updateOrInsertRowByUniqueField($tableName, array $data, callable $preprocess,
	    		$uniqueField='external_uniqueid', $idField='id', $db=null){
	    	if(empty($db)){
	    		$db=$this->db;
	    	}
	    	unset($data[$idField]);
	    	$db->select($idField)->from($tableName)->where($uniqueField, $data[$uniqueField]);
	    	$id=$this->runOneRowOneField($idField, $db);
	    	if(empty($id)){
	    		$preprocess($data, $id);
	    		//insert
	    		$id=$this->runInsertData($tableName, $data, $db);
	    	}else{
	    		$preprocess($data, $id);
	    		//update
	    		$db->where($idField, $id)->set($data);
	    		$success=$this->runAnyUpdate($tableName, $db);
	    		if(!$success){
	    			$id=null;
	    		}
	    	}

	    	return $id;
	    }

	    public function getExistsIdList($tableName, array $where, $idField='id', $db=null){
	    	if(empty($db)){
	    		$db=$this->db;
	    	}
	    	$db->select($idField)->from($tableName)->where($where);
	    	$rows=$this->runMultipleRowArray($db);
	    	if(!empty($rows)){
		    	return array_column($rows, $idField);
	    	}
	    	return null;
	    }

		public function runBatchDeleteByIdWithLimit($tableName, array $idArr, $idField='id', $limit = 500, $db=null){
	    	if(empty($db)){
	    		$db=$this->db;
	    	}
			$success=false;
			if(empty($idArr)){
				$success=true;
			}else{
				$arr = array_chunk($idArr, $limit);
				foreach ($arr as $data) {
					//insert and clean
					$db->where_in($idField, $data);
					$success=$this->runRealDelete($tableName, $db);
					if(!$success){
						$this->utils->error_log('run delete batch failed', $tableName, $data);
						break;
					}
				}
			}
			return $success;
		}

		public function existsUniqueIndex($tableName, $fieldName){
			$exists=false;
			$sql=<<<EOD
SELECT non_unique FROM information_schema.statistics
WHERE table_name = ? and column_name=? AND table_schema = database()
EOD;
			$rows=$this->runRawSelectSQLArray($sql, [$tableName, $fieldName]);
			if(!empty($rows)){
				foreach ($rows as $row) {
					$exists=$row['non_unique']==0;
					if($exists){
						break;
					}
				}
			}

			return $exists;
		}

		/**
		 *
		 * only available there's an unique index on any of data field
		 * call command.sh test_unique_index_field <table name> <field name> to test
		 *
		 * @param  string $tableName
		 * @param  array  $data  make sure there's an unique index field
		 * @param  object $db
		 * @return boolean $success
		 */
		public function runInsertOrUpdateData($tableName, array $data, $db=null){
			if(empty($tableName) || empty($data)){
				$this->utils->error_log('empty $tableName or $data', $tableName, $data);
				return false;
			}

			$fields=[];$values=[];$params=[];$updates=[];
			foreach ($data as $key => $val) {
				$fields[]='`'.$key.'`';
				$values[]='?';
				$params[]=$val;
				$updates[]='`'.$key.'`=VALUES(`'.$key.'`)';
			}
			$fieldStr=implode(',', $fields);
			$valStr=implode(',', $values);
			$updateStr=implode(',', $updates);
			$sql=<<<EOD
INSERT INTO {$tableName}
({$fieldStr})
values
({$valStr})
ON DUPLICATE KEY UPDATE
{$updateStr}
EOD;

			$success=true;
			// $cnt=0;
			$cnt=$this->runRawUpdateInsertSQL($sql, $params, $db);
			$this->utils->debug_log('insert or update sql', $cnt, $sql, $params);

			return $success;

		}

		public function buildParamsToSQL($sql, $params){
			return $this->db->compile_binds($sql, $params);
		}

		/**
		 * get target table rows from explain
		 * @param  string $sql
		 * @param  string $targetTable
		 * @return null or count of rows
		 */
		public function queryExplainRows($sql, $params, $targetTable){
			$result=null;

			$sql=$this->buildParamsToSQL($sql, $params);
			$explainSql='explain'."\n".$sql;
			$rows=$this->runRawSelectSQLArray($explainSql);
			if(!empty($rows)){
				foreach ($rows as $row) {
					// $this->utils->debug_log('queryExplainRows', $row);
					if($row['table']==$targetTable){
						$result=intval($row['rows']);
						break;
					}
				}
			}

			return $result;
		}

		//===redis===========================================================
		/**
		 * scan session hash on redis
		 * @param  string   $specialSessionTable
		 * @param  callable $callback ($redis, $o2sKey, $key, $lastActivity)
		 * @param  string   $queryKey  optional, default is null, sample: <id>-*
		 * @return void
		 */
		public function scanSessionHashOnRedis($specialSessionTable, callable $callback, $queryKey=null){
			if(empty($specialSessionTable)){
				$specialSessionTable=$this->utils->getConfig('sess_table_name');
			}
			$o2sKey=$this->utils->getSessionHashMapKeyForRedis($specialSessionTable);
			$redis=$this->utils->getSessionRedisServer();
			$count=0;
			if(!empty($redis)){
				$this->utils->debug_log('try scan '.$o2sKey, $queryKey);
				$redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
				$it = NULL;
				do {
					$arrKeys=null;
					if(!empty($queryKey)){
					    // Scan for some keys
						$arrKeys=$redis->hScan($o2sKey, $it, $queryKey);
					}else{
					    // Scan for some keys
						$arrKeys=$redis->hScan($o2sKey, $it);
					}

				    // Redis may return empty results, so protect against that
				    if (!empty($arrKeys)) {
				    	foreach ($arrKeys as $key => $lastActivity) {
				    		$continue=$callback($redis, $o2sKey, $key, $lastActivity);
				    		if(!$continue){
				    			$this->utils->debug_log('stop on', $key, $lastActivity);
				    			break;
				    		}
				    		$count++;
				    	}
				    }else if($it>0){
				    	$this->utils->debug_log('not found any key', $o2sKey, $queryKey);
				    	break;
				    }
				} while ($it > 0);
				$this->utils->debug_log('finish scan', $count);
			}else{
				$this->utils->debug_log('no redis when scanSessionHashOnRedis');
			}
			//no return, process everything on $callback
		}

		/**
		 * batch clean timeout session id
		 * @param  string $specialSessionTable
		 * @return int $count
		 */
		public function batchCleanExpiredSessionId($specialSessionTable){
			$count=0;
			$timeout = time()-$this->utils->getConfig('force_timeout_of_session_redis');
			$this->scanSessionHashOnRedis($specialSessionTable,
					function($redis, $o2sKey, $key, $lastActivity)
					use(&$count, $timeout, $specialSessionTable){
				if($lastActivity<=$timeout){
					$arr=explode('-', $key);
					if(count($arr)>1){
						$sessionId=$arr[1];
						//delete sessionId
						$sessKey=$this->utils->getSessionIdKeyForRedis($sessionId, $specialSessionTable);
						$rlt=$redis->unlink($sessKey);
		    			$this->utils->debug_log('delete session key', $sessKey, $rlt);
					}
					//delete
					$rlt=$redis->hDel($o2sKey, $key);
					$this->utils->debug_log('delete timeout session', $o2sKey, $key, $lastActivity, $timeout, $rlt);
					$count++;
				}

				return true;
			});

			return $count;
		}

		/**
		 * delete sessions by object id
		 * @param  string $specialSessionTable
		 * @param  int $objectId
		 * @return int $count
		 */
		public function deleteSessionsByObjectIdOnRedis($specialSessionTable, $objectId){
			$count=0;
			$queryKey=$objectId.'-*';
			$this->scanSessionHashOnRedis($specialSessionTable,
					function($redis, $o2sKey, $key, $lastActivity)
					use(&$count, $specialSessionTable){
				$arr=explode('-', $key);
				if(count($arr)>1){
					$sessionId=$arr[1];
					//delete sessionId
					$sessKey=$this->utils->getSessionIdKeyForRedis($sessionId, $specialSessionTable);
					$rlt=$redis->unlink($sessKey);
					$this->utils->debug_log('delete session key', $sessKey, $rlt);
				}
				//delete
				$rlt=$redis->hDel($o2sKey, $key);
				$this->utils->debug_log('delete hash session', $o2sKey, $key, $rlt);
			    $count++;
			    return true;

			}, $queryKey);
			return $count;
		}

		/**
		 * searchSessionIdByObjectIdOnRedis
		 * @param  string $specialSessionTable
		 * @param  int $objectId
		 * @return array $sessions
		 */
		public function searchSessionIdByObjectIdOnRedis($specialSessionTable, $objectId){
			$sessions=[];
			$queryKey=$objectId.'-*';
			$this->scanSessionHashOnRedis($specialSessionTable,
				function($redis, $o2sKey, $key, $lastActivity)
				use(&$sessions){
				$arr=explode('-', $key);
				if(count($arr)>1){
					$sessionId=$arr[1];
					$sessions[]=$sessionId;
				}
				return true;
			}, $queryKey);

			return $sessions;
		}

		/**
		 * return all object id, from $fromTimestamp to now
		 * @param string $specialSessionTable
		 * @param int $fromTimestamp
		 * @return array $objectIdList unique
		 */
		public function searchAllObjectIdOnRedis($specialSessionTable, $fromTimestamp=-1){
			$objectIdList=[];
			$this->scanSessionHashOnRedis($specialSessionTable,
					function($redis, $o2sKey, $key, $lastActivity)
					use(&$objectIdList, $fromTimestamp){
				$arr=explode('-', $key);
				if(count($arr)>1){
					$objectId=$arr[0];
					if(!empty($objectId)){
						//no limit or >= from time
						if($fromTimestamp<0 || $lastActivity>=$fromTimestamp){
							$objectIdList[]=$objectId;
						}
					}
				}
				return true;
			});

			return array_unique($objectIdList, SORT_NUMERIC);
		}

		/**
		 * count session id
		 *
		 * @param string $specialSessionTable
		 * @param int  $fromTimestamp
		 * @param array $objectIdList, default is null
		 * @return int $count
		 */
		public function countSessionIdByObjectIdOnRedis($specialSessionTable, $fromTimestamp, $objectIdList=null){
			$count=0;
			if(!empty($objectIdList)){
				$this->scanSessionHashOnRedis($specialSessionTable,
					function($redis, $o2sKey, $key, $lastActivity)
					use(&$count, $objectIdList, $fromTimestamp){
					$arr=explode('-', $key);
					if(count($arr)>1){
						$objectId=$arr[0];
						if(in_array($objectId, $objectIdList)){
							//no limit or >= from time
							if($fromTimestamp<0 || $lastActivity>=$fromTimestamp){
								$count++;
							}
						}
					}
					return true;
				});
			}else{
				$objectIdList=$this->searchAllObjectIdOnRedis($specialSessionTable, $fromTimestamp);
				$count=count($objectIdList);
			}

			return $count;

		}

		public function getSessionBySessionIdOnRedis($specialSessionTable, $sessionId){
			$session=null;
			$key=$this->utils->getSessionIdKeyForRedis($sessionId, $specialSessionTable);
			$redis=$this->utils->getSessionRedisServer();
			if(!empty($redis)){
				$this->utils->debug_log('try get session', $key);
				$session=$redis->get($key);
				if(empty($session)){
					$session=null;
				}else{
					$session=$this->utils->decodeJson($session);
				}
			}else{
				$this->utils->debug_log('no redis when getSessionBySessionIdOnRedis');
			}

			return $session;
		}

		public function getAnyAvailableSessionByObjectIdOnRedis($specialSessionTable, $objectId, $tiemoutSeconds){
			$session=null;
			$queryKey=$objectId.'-*';
			$this->scanSessionHashOnRedis($specialSessionTable,
				function($redis, $o2sKey, $key, $lastActivity)
				use(&$session, $specialSessionTable, $tiemoutSeconds){
				$continue=true;
				$arr=explode('-', $key);
				if(count($arr)>1){
					$sessionId=$arr[1];
					//load data form session id
					$sessKey=$this->utils->getSessionIdKeyForRedis($sessionId, $specialSessionTable);
					$data=$redis->get($sessKey);

					if(!empty($data)){
						$data=$this->utils->decodeJson($data);
						if(!empty($data)){
							//json format
							$last_activity = $data['last_activity'];
							// $this->utils->debug_log('check session timeout', $last_activity, $tiemoutSeconds);
							$is_timeout = time() > $last_activity + $tiemoutSeconds;
							if(!$is_timeout){
								//found , break
								$session=$data;
								$continue=false;
							}
						}
					}
				}
				return $continue;
			}, $queryKey);
			return $session;
		}
		//===redis===========================================================

	    const IP_LIMIT_KEY='_IP_LIMIT_RUNTIME';

	    /**
	     * clearIpLimitBy
	     * @param  string $ip
	     * @param  string $type
	     * @return boolean
	     */
	    public function clearIpLimitBy($ip){
	    	$key=self::IP_LIMIT_KEY.'-'.$ip;
	    	return $this->utils->deleteCache($key);
	    }

	    public function isEnabledIpLimitHourly($type){
	    	$enabled=false;
	    	$ip_limit_hourly=$this->utils->getConfig('ip_limit_hourly');
	    	if(!empty($ip_limit_hourly) && array_key_exists($type, $ip_limit_hourly) && !empty($ip_limit_hourly[$type])){
	    		$enabled=true;
	    	}

	    	return $enabled;
	    }

	    public function readIpLimitBy($ip){
	    	$key=self::IP_LIMIT_KEY.'-'.$ip;
	    	return $this->utils->getJsonFromCache($key);
	    }

	    /**
	     * reachedIpLimitBy
	     *
	     * @param  string $ip   [description]
	     * @param  string $type [description]
	     * @return boolean  true: reached, false: not reach, null: error
	     */
	    public function reachedIpLimitHourlyBy($ip, $type, &$err=null){
	    	$reached=false;
	    	//is valided type?
	    	$ip_limit_hourly=$this->utils->getConfig('ip_limit_hourly');
	    	if(!$this->isEnabledIpLimitHourly($type)){
	    		//wrong type
	    		$this->utils->error_log('ip limit type '.$type.' is not enabled');
	    		$err='ip limit type '.$type.' is not enabled';
	    		return null;
	    	}

	    	$key=self::IP_LIMIT_KEY.'-'.$ip;
			$hourKey=date('YmdH');
	    	$ipLimit=$this->utils->getJsonFromCache($key);

			$this->utils->debug_log('getJsonFromCache: '.$key, $ipLimit);

	    	//$ipLimit={
	    	//  'register':{
	    	//  	'2019112300'=>1,
	    	//  	'2019112301'=>1,
	    	//  }
	    	//};
			$found=false;
	    	if(!empty($ipLimit)){
				if(array_key_exists($type, $ipLimit)){
					//exists record
					//check same hour
					$hourCounting=$ipLimit[$type];
					if(array_key_exists($hourKey, $hourCounting)){
						//found current hour
						$found=true;
						$times=$hourCounting[$hourKey];
						$reached=$times>=$ip_limit_hourly[$type];
						$this->utils->debug_log('found it', $times, $reached);
						//reset, only keep current hour
						$ipLimit[$type]=[$hourKey=>$times+1];
					}
				}

	    	}else{
	    		$ipLimit=[$type=>[]];
	    	}
	    	if(!$found){
	    		//init
	    		$ipLimit[$type]=[$hourKey=>1];
	    	}
		    $this->utils->saveJsonToCache($key, $ipLimit);
		    $this->utils->debug_log('saveJsonToCache: '.$key, $ipLimit);

	    	return $reached;
	    }

	    /**
	     * query selector by explain
	     *
	     * explain sql then callback to check if it's best
	     *
	     * @param  array   $sqlPlans
	     * @return array $result , null means can't find any plan
	     */
	    public function queryPlanSelectorByExplain(array $sqlPlans){
	    	if(empty($sqlPlans)){
	    		return null;
	    	}
		    $result=null;
	    	$minCount=null;
	    	foreach ($sqlPlans as $sqlInfo) {
		    	$cnt=$this->queryExplainRows($sqlInfo['sql'], $sqlInfo['params'], $sqlInfo['mainTable']);
		    	$this->utils->debug_log('selector after queryExplainRows', $cnt, $sqlInfo);
		    	//null or found smaller count
		    	if($minCount===null || $minCount>$cnt){
		    		$minCount=$cnt;
		    		$result=$sqlInfo;
		    	}
	    	}
	    	return $result;
	    }

	    /**
	     * runMultipleRowOneFieldArray
	     * @param  string $fieldName
	     * @param  object $db
	     * @return array ['xxx', 'xxx'] not associative array
	     */
		public function runMultipleRowOneFieldArray($fieldName, $db=null){
			if(empty($db) || !is_object($db)){
				$db=$this->db;
			}
			$qry = $db->get();
			$rowsOneField= $this->getMultipleRowOneFieldArray($fieldName, $qry);
			unset($qry);
			return $rowsOneField;
		}

		public function getMultipleRowOneFieldArray($fieldName, $qry){
			if ($qry && $qry->num_rows() > 0) {
				$rows=$qry->result_array();
				$qry->free_result();
				if(!empty($rows)){
					$result=[];
					array_walk($rows, function($row) use(&$result, $fieldName){
						$result[]=$row[$fieldName];
					});
					unset($rows);
					return $result;
				}
			}
			return [];
		}


		const NEW_GAME_LOGS='game_logs_new';

		public function syncGameLogsStructureToNewTable($newTableName=null, $db=null){
			//check table name
			if(empty($db)){
				$db=$this->db;
			}
			if(empty($newTableName)){
				$newTableName=self::NEW_GAME_LOGS;
			}
			if($this->utils->table_really_exists($newTableName, $db)){
				//drop new
				$this->runRawUpdateInsertSQL('drop table '.$newTableName,null,$db);
			}

			$this->runRawUpdateInsertSQL('create table '.$newTableName.' like game_logs ');
			return true;
		}

		public function adjustIdOfGameLogsNew($newTableName=null, $db=null){
			if(empty($db)){
				$db=$this->db;
			}
			if(empty($newTableName)){
				$newTableName=self::NEW_GAME_LOGS;
			}
			if($this->utils->table_really_exists($newTableName, $db)){
				//change id to int64
				$this->runRawUpdateInsertSQL('alter table '.$newTableName.' modify id bigint unsigned not null AUTO_INCREMENT');
				$this->runRawUpdateInsertSQL('alter table '.$newTableName.' modify external_uniqueid varchar(256) not null');
				return true;
			}else{
				return false;
			}
		}

	}
}
