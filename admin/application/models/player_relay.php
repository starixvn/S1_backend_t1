<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * player table relay
 * include,
 * username
 * first_deposit_amount
 * first_deposit_datetime
 *
 *
 * @category player_relay
 * @version 1.0.0
 * @copyright TripleOneTech
 */
class Player_relay extends BaseModel {



	protected $tableName = 'player_relay';
	protected $idField = 'id';
	protected $defaultData = null;

	public function __construct() {
		parent::__construct();
		$this->load->model(['Player', 'player_model', 'operatorglobalsettings', 'transactions']);
		$this->load->library(['player_manager']);

		$this->defaultData = $this->getDefaultData();
	}

	/**
	 * To sync exist players
	 *
	 * @param integer $limit
	 * @return void
	 */
	function cron4syncExistsPlayer($limit=9999999999){
		$isInitial = $this->isInitial();
		if( ! $isInitial ){
			// import players for player_id, username and deleted_at.
			$this->cronInitialize();
		}

		$oldestSyncTime = $this->getOldestSyncTimeOfPlayer_relay();

		// get sync_last_updatetime4cron4syncExistsPlayer
		$theOperatorGlobalSetting = $this->operatorglobalsettings->getOperatorGlobalSetting('lastSyncTimestamp_cron4syncExistsPlayer');
// $this->utils->debug_log('theOperatorGlobalSetting:', $theOperatorGlobalSetting);
		$lastSyncTimestamp_cron4syncExistsPlayer = $theOperatorGlobalSetting[0]['value'];
		// Get player who in player_relay.
		$sql = <<<EOF
		SELECT `player_id`
			, `username`
			, `deleted_at`
		FROM `player_relay`
		WHERE sync_updated_at
			BETWEEN STR_TO_DATE('$oldestSyncTime',"%Y-%m-%d %H:%i:%s")
				AND STR_TO_DATE('$lastSyncTimestamp_cron4syncExistsPlayer',"%Y-%m-%d %H:%i:%s")
		ORDER BY sync_updated_at ASC
		LIMIT $limit
EOF;
// $this->utils->debug_log('$sql63:', $sql);
		$result = $this->db->query($sql);
		$rows = $this->getMultipleRowArray($result);

		if( ! empty($rows) ){
			foreach($rows as $key => $row){
				$player_id = $row['player_id'];
				$consoleArr = [];

				$consoleArr[] = 'player_id:';
				$consoleArr[] = $player_id;
				$isAffectedRowsExists = $this->cronjob4UpdateByPlayer_idSyncPlayer($row['player_id']);
				$consoleArr[] = 'UpdatePlayer:';
				$consoleArr[] = $isAffectedRowsExists?1:0;

				// Update agent_id
				$isAffectedRowsExists = $this->cronjob4UpdateByPlayer_idSyncAgent_id($player_id);
				$consoleArr[] = 'UpdateAgent:';
				$consoleArr[] = $isAffectedRowsExists?1:0;
				// $this->utils->debug_log('player_id='.$player_id);
// $this->utils->debug_log('Update Agent_id:', $isAffectedRowsExists);
// // // OK

				// Update affiliate_id
				$isAffectedRowsExists = $this->cronjob4UpdateByPlayer_idSyncAffiliate_id($player_id);
				$consoleArr[] = 'UpdateAffiliate:';
				$consoleArr[] = $isAffectedRowsExists?1:0;
// $this->utils->debug_log('Update Affiliate_id:', $isAffectedRowsExists);
// $this->utils->debug_log('isAffectedRowsExists75Agent_id', $isAffectedRowsExists);
// die('aaaaa87');
// // OK

				// Update referrerId by player_id
				$isAffectedRowsExists = $this->cronjob4UpdateByPlayer_idSyncReferee_player_id($player_id);
				$consoleArr[] = 'UpdateReferee_player:';
				$consoleArr[] = $isAffectedRowsExists?1:0;
// $this->utils->debug_log('Update Referee_player_id:', $isAffectedRowsExists);
// $this->utils->debug_log('isAffectedRowsExists81Referee_player_id', $isAffectedRowsExists);
// die('aaaaa87');

				// update first_deposit_amount and first_deposit_datetime
				$isAffectedRowsExists = $this->cronjob4UpdateByPlayer_idSyncFirstDeposit($player_id);
				$consoleArr[] = 'UpdateFirstDeposit:';
				$consoleArr[] = $isAffectedRowsExists?1:0;
// $this->utils->debug_log('Update FirstDeposit:', $isAffectedRowsExists);
// $this->utils->debug_log('isAffectedRowsExists87First_deposit_datetime', $isAffectedRowsExists);
// OK
				call_user_func_array(array($this->utils, "debug_log"), $consoleArr);
				// $this->utils->debug_log('Update FirstDeposit:', $consoleArr);
			}

		}

		// update "lastSyncTimestamp_cron4syncNewPlayer" in operator_settings.
		// lastSyncTimestamp_cron4syncNewPlayer
		$nowFromMysql = $this->getNowForMysql();
		$theSetupSettings = [];
		$theSetupSettings['lastSyncTimestamp_cron4syncExistsPlayer'] = $nowFromMysql;
		$this->cronSetupSettings($theSetupSettings);

	} // EOF cron4syncExistsPlayer

	/**
	 * To sync new player
	 *
	 * @param integer $limit
	 * @return void
	 */
	function cron4syncNewPlayer($limit=9999999999){
		$isInitial = $this->isInitial();
		if( ! $isInitial ){
			// import players for player_id, username and deleted_at.
			$this->cronInitialize();
		}

		// for dev.
// $sql = <<<EOF
// 		DELETE FROM `player_relay` WHERE
// 		`player_relay`.`player_id` in(
// 			3150, 3153
// 			/*
// 			3150, 3153 - og.live.ref.fd
// 			3180, 3183 - og.live.ref
// 			3156 - og.live.aff.fd
// 			119118	aff
// 			5354   aff
// 			, 5355  direct
// 			, 212397  agent
// 			, 161862 referr - staging
// 			, 56933 - first d
// 			*/
// 		)
// EOF;
// $result = $this->db->query($sql);

		// Get player who not in player_relay.
		$sql = <<<EOF
		SELECT playerId
		,username
		,createdOn
		,deleted_at
		FROM player
		WHERE playerId NOT IN( /* as playerId */
				SELECT `player_id`
				FROM `player_relay`
		) /* EOF as playerId */
		ORDER BY createdOn ASC
		LIMIT $limit
EOF;
		$result = $this->db->query($sql);
		$rows = $this->getMultipleRowArray($result);
// $this->utils->debug_log('isInitial', $isInitial);
// $this->utils->debug_log('sql220', $sql);
// $this->utils->debug_log('rows221', $rows);
// 		die('bbbbbbb');
		if( ! empty($rows) ){
			foreach($rows as $key => $row){
				$player_id = $row['playerId'];
				$consoleArr = [];

				$consoleArr[] = 'player_id:';
				$consoleArr[] = $player_id;

				// update player_id, username and deleted_at
				$data = [];
				$data['player_id'] = $row['playerId'];
				$data['username'] = $row['username'];
				$data['created_on'] = $row['createdOn'];
				$data['deleted_at'] = $row['deleted_at'];
				$insert_id = $this->newData($data);
// $this->utils->debug_log('insert_id', $insert_id);
	// die('aaaaa');

// 				$playeraccount = $this->player_manager->getPlayerAccount($player_id);
// $this->utils->debug_log('playeraccount', $playeraccount);
// 	die('aaaaa');
// failed
				$consoleArr[] = 'NewPlayer,player_relay.id:';
				$consoleArr[] = $insert_id;
				// Update agent_id
				$isAffectedRowsExists = $this->cronjob4UpdateByPlayer_idSyncAgent_id($player_id);
// $this->utils->debug_log('player_id86', $player_id);
// $this->utils->debug_log('isAffectedRowsExists87Agent_id', $isAffectedRowsExists);
// // // OK
				$consoleArr[] = 'UpdateAgent:';
				$consoleArr[] = $isAffectedRowsExists?1:0;

				// Update affiliate_id
				$isAffectedRowsExists = $this->cronjob4UpdateByPlayer_idSyncAffiliate_id($player_id);
// $this->utils->debug_log('isAffectedRowsExists97affiliate_id', $isAffectedRowsExists);
// die('aaaaa87');
// // OK
				$consoleArr[] = 'UpdateAffiliate:';
				$consoleArr[] = $isAffectedRowsExists?1:0;

				// Update referrerId by player_id
				$isAffectedRowsExists = $this->cronjob4UpdateByPlayer_idSyncReferee_player_id($player_id);
// $this->utils->debug_log('isAffectedRowsExists114Referee_player_id', $isAffectedRowsExists);
// die('aaaaa87');
				$consoleArr[] = 'UpdateReferee_player:';
				$consoleArr[] = $isAffectedRowsExists?1:0;

				// update first_deposit_amount and first_deposit_datetime
				$isAffectedRowsExists = $this->cronjob4UpdateByPlayer_idSyncFirstDeposit($player_id);
// $this->utils->debug_log('isAffectedRowsExists160First_deposit_datetime', $isAffectedRowsExists);
				$consoleArr[] = 'UpdateFirstDeposit:';
				$consoleArr[] = $isAffectedRowsExists?1:0;

				call_user_func_array(array($this->utils, "debug_log"), $consoleArr);
				// $this->utils->debug_log('Update FirstDeposit:', $consoleArr);
			} // EOF foreach($rows,...
		}// EOF if( ! empty($rows) )

		// update "lastSyncTimestamp_cron4syncNewPlayer" in operator_settings.
		// lastSyncTimestamp_cron4syncNewPlayer
		$nowFromMysql = $this->getNowForMysql();
		$theSetupSettings = [];
		$theSetupSettings['lastSyncTimestamp_cron4syncNewPlayer'] = $nowFromMysql;
		$this->cronSetupSettings($theSetupSettings);


	}// EOF cron4syncNewPlayer

	/// @todo ref. to getPlayerAffiliateUsername().
	public function getPlayerAffiliate_DEL( $playerId, $selectStr = 'affiliates.username'){

		$row = $this->db->select('affiliates.affiliateId')
						 ->from('player')
						 ->where('playerId', $playerId)
						 ->join('affiliates', 'affiliates.affiliateId = player.affiliateId', 'LEFT')
						 ->get()
						 ->row_array();

		return $row;

	}

	/**
	 * Sync username, deleted_at by player_id at few time ago.
	 * @param integer $updatedSecAgo The updateed seconds ago.
	 * @return void
	 */
	function cron4syncByUpdatedAt_DEL( $updatedSecAgo = 1* 60* 60 ){
		$isInitial = $this->isInitial();
		if( ! $isInitial ){
			// import players for player_id, username and deleted_at.
			$this->cronInitialize();
		}
		// $updatedSecAgo = 1* 60* 60; // 1hr ago
		$whereStr = "1";
		$returnRows = true;
		$amountPer = 10;
		$orderBy['sync_updated_at'] = 'ASC';
		$rows = $this->getDataByWhere($whereStr, $amountPer, $returnRows, $orderBy);
		foreach($rows as $key => $row){
		}


	}




	/**
	 * initial cronjob for sync player_relay.
	 *
	 * @return void
	 */
	function cronInitialize(){
		// must be first,
		// setup default settings into operator_settings
		$defaultSettings = $this->getDefaultSettings();
		$this->cronSetupSettings( $defaultSettings );


		// import player
		$this->importPlayers();

	}

	/**
	 * Setup operator_settings for the player_relay sync
	 *
	 * @param array $data the key-value for operator_settings
	 * @return void
	 */
	function cronSetupSettings($data){
		$note = 'Cronjob for sync player_relay';
		foreach ($data as $key => $value) {
			if($this->operatorglobalsettings->existsSetting($key)){
				$this->operatorglobalsettings->putSetting($key, $value, 'value');
				$this->operatorglobalsettings->putSetting($key, $note, 'note');
			}else{
				$this->operatorglobalsettings->insertSetting($key, $value);
				$this->operatorglobalsettings->putSetting($key, $note, 'note');
			}
		}
	}// EOF cronSetupSettings


	/**
	 * Get the table status in MySql.
	 *
	 * @param string $tablename The table name.
	 * @return array  $row The row for information of the table.
	 *
	 */
	public function showTableStatus($tablename){ // @todo will apply to detect is table exists?

		$sql = 'SHOW TABLE STATUS LIKE "'. $tablename. '";';
		$query = $this->db->query($sql);
		$row = $query->row_array();
		$query->free_result(); // $query 物件將不再使用了
		unset($query);
		return $row;
	} // EOF showTableStatus

	/**
	 * Check The player_relay table is initialized
	 *
	 * Detect is initialized,
	 * The initialized, player has data and player_relay has too.
	 * else un-initialize.
	 *
	 * @return boolean true is initialized,false un-initialize.
	 */
	public function isInitial(){
		$returnRows = false;
		$fieldName = 'id >';
		$value = 0;
		$aRow = $this->getDataBy($fieldName,$value, $returnRows);

		$playerTableStatus = $this->showTableStatus('player');
		$player_relayTableStatus = $this->showTableStatus('player_relay');

		$isInitialized = false;
		if( ! empty($playerTableStatus)
			&& ! empty($playerTableStatus)
		){ // Check player and player_relay both exists.
			if( $playerTableStatus['Rows'] > 0
				&& $player_relayTableStatus['Rows'] > 0
			){ // check player has data and player_relay without data.
				$isInitialized = true;
			}
		}

		return $isInitialized;
	} // EOF isInitial
	/**
	 * Do import player data into player_relay at first time.
	 *
	 * Direct import records from player table into player_relay.
	 * But first_deposit_amount and first_deposit_datetime is default value, null.
	 * The first deposit info need do sync exists player to fill from transactions.
	 *
	 * @return boolean $return if complate true, else false.
	 */
	public function importPlayers(){
		$sql =<<<EOF
	INSERT INTO player_relay
		SELECT null 				/* player_relay.id */
			, playerId 				/* player_relay.player_id */
			, username 				/* player_relay.username */
			, affiliateId 			/* player_relay.affiliate_id */
			, agent_id 				/* player_relay.agent_id */
			, refereePlayerId 		/* player_relay.refereePlayer_id */
			, null 					/* player_relay.first_deposit_amount */
			, null 					/* player_relay.first_deposit_datetime */
			, now() as `sync_created_at` /* player_relay.sync_created_at */
			, now() as `sync_updated_at` /* player_relay.sync_updated_at */
			, createdOn  			/* player_relay.created_on */
			, deleted_at  			/* player_relay.deleted_at */
		FROM player;
EOF;

		$query = $this->db->query($sql);
		// $query->free_result(); // disable for "Call to a member function free_result() on boolean"
		$player_relayTableStatus = $this->showTableStatus( $this->tableName );

		$return = ! empty($player_relayTableStatus['Data_length']);

		if($return){
			$nowFromMysql = $this->getNowForMysql();
			$theSetupSettings = [];
			$theSetupSettings['lastSyncTimestamp_cron4syncNewPlayer'] = $nowFromMysql;
			$theSetupSettings['lastSyncTimestamp_cron4syncExistsPlayer'] = $nowFromMysql;
			$this->cronSetupSettings($theSetupSettings);
		}

		return $return;
	}// EOF importPlayers

	/**
	 * The default settings in operatorglobalsettings class
	 *
	 * lastSyncTimestamp_cron4syncExistsPlayer for sync exists player
	 * lastSyncTimestamp_cron4syncNewPlayer for sync new player
	 *
	 * @return array $defaultSettings The key-value for operatorglobalsettings.
	 */
	function getDefaultSettings(){
		$defaultSettings = [];
		$defaultSettings['lastSyncTimestamp_cron4syncExistsPlayer'] = '2019-10-21 09:48:08';
		$defaultSettings['lastSyncTimestamp_cron4syncNewPlayer'] = '2019-09-01 15:48:08';
		// $defaultSettings['lastSyncTimestamp4username'] = 0; // username and deleted_at
		// $defaultSettings['lastSyncTimestamp4agent_id'] = 0; // agent_id only
		// $defaultSettings['lastSyncTimestamp4affiliate_id'] = 0; // affiliate_id only
		// $defaultSettings['lastSyncTimestamp4referee_player_id'] = 0; // referee_player_id only
		// $defaultSettings['lastSyncTimestamp4first_deposit'] = 0; // first_deposit amount and time.
		return $defaultSettings;
	}// EOF getDefaultSettings

	/**
	 * get the default row
	 *
	 * @return void
	 */
	function getDefaultData(){

		$nowFromMysql = $this->getNowForMysql();
		$defaultData = [];
		$defaultData['id'] = null; // P.K.
		$defaultData['player_id'] = null;
		$defaultData['username'] = '';
		$defaultData['affiliate_id'] = 0;
		$defaultData['agent_id'] = 0;
		$defaultData['referee_player_id'] = 0;
		$defaultData['first_deposit_amount'] = 0;
		$defaultData['first_deposit_datetime'] = null;
		$defaultData['sync_created_at'] = $nowFromMysql;
		$defaultData['sync_updated_at'] = $nowFromMysql;
		$defaultData['deleted_at'] = 'NULL';

		return $defaultData;
	}// EOF getDefaultData

	/**
	 * insert a new data
	 *
	 * @param array $data The array should be fieldname-value, ex:
	 * - $data[player_id] = 123
	 * - $data[username] = 'abc'
	 * - $data[affiliate_id] = 0
	 * ...
	 * @return integer The insert_id.
	 */
	function newData($data){
		$defaultData = [];
		// update last sync update and create time.
		$nowFromMysql = $this->getNowForMysql();
		$defaultData['sync_created_at'] = $nowFromMysql;
		$defaultData['sync_updated_at'] = $nowFromMysql;
		$theNewData = array_merge($defaultData, $data);
// $this->utils->debug_log('theNewData:::::', $theNewData);

		return $this->insertData($this->tableName, $theNewData);
	} // EOF newData


	/**
	 * Get row/rows by a field value
	 *
	 * @param string $fieldName The field name.
	 * @param string $value The field value.
	 * @param boolean $returnRows if return all rows, and be true. false for first row.
	 * @return array The 1-way for $returnRows = false, and 2-way for $returnRows = true.
	 */
	function getDataBy($fieldName, $value, $returnRows = false){
		$fields = array_keys($this->defaultData);
		$this->db->select( implode(',',$fields) );

		$this->db->from($this->tableName);
		$this->db->where($fieldName, $value);
		$data = []; // default
		if( ! $returnRows ){ // single row
			$query = $this->db->get();
			$row = $query->row_array();
			if( !empty($row) ){
				$data = $row;
			}
			$query->free_result(); // $query 物件將不再使用了
			unset($query);
		}else{ // all rows
			$result = $this->db->get();
        	$rows = $this->getMultipleRowArray($result);

			// $rows = $this->runMultipleRowArray();
			if( !empty($rows) ){
				$data = $rows;
			}
		}

		return $data;
	} // EOF getDataBy

	/**
	 * get the oldest sync datetime
	 *
	 * @return null|string The oldest sync update time
	 */
	function getOldestSyncTimeOfPlayer_relay(){
		// Get player who not in player_relay.
		$sql = <<<EOF
		SELECT min(`sync_updated_at`) as `oldest_sync_time` FROM `player_relay`
EOF;
		$result = $this->db->query($sql);
		$rows = $this->getMultipleRowArray($result);
		$OldestSyncTime = null;
		if( ! empty($rows) ){
			$OldestSyncTime = $rows[0]['oldest_sync_time'];
		}
		return $OldestSyncTime;
	}// EOF getOldestSyncTimeOfPlayer_relay

	/**
	 * Undocumented function
	 *
	 * @param [type] $whereStr
	 * @param integer $limit
	 * @param boolean $returnRows
	 * @param [type] $orderBy
	 * @return void
	 */
	function getDataByWhere($whereStr, $limit = 10, $returnRows = false, $orderBy = null){
		$fields = array_keys($this->defaultData);
		$this->db->select( implode(',',$fields) );
		$this->db->from($this->tableName);
		// $whereStr = "name='Joe' AND status='boss' OR status='active'";
		$this->db->where($whereStr);
		$this->db->limit($limit);
		// $orderBy['sync_updated_at'] = 'ASC';
		if( ! empty($orderBy) ){
			foreach ($orderBy as $key => $value){
				$this->db->order_by($key, $value); // old first.
			}
		}
		$data = []; // default
		if( ! $returnRows ){ // single row
			$query = $this->db->get();
			$row = $query->row_array();
			if( !empty($row) ){
				$data = $row;
			}
			$query->free_result();
			unset($query);
		}else{ // all rows
			$result = $this->db->get();
        	$rows = $this->getMultipleRowArray($result);

			// $rows = $this->runMultipleRowArray();
			if( !empty($rows) ){
				$data = $rows;
			}
		}

		return $data;
	}


	/**
	 * Update row by field
	 *
	 * @param string $fieldName
	 * @param string $value
	 * @param array $data
	 * @return void
	 */
	function updateDataBy($fieldName,$value, $data){

		// update last sync update time.
		$defaultData = [];
		$nowFromMysql = $this->getNowForMysql();
		$defaultData['sync_updated_at'] = $nowFromMysql;
		$data = array_merge($defaultData, $data);

		$where = [];
		$where[$fieldName] = $value;
// $this->utils->debug_log('data:::::', $data);

		$this->db->update($this->tableName, $data, $where);
		$affected_rows = $this->db->affected_rows();

		return $affected_rows;
	} // EOF updateDataBy
	/**
	 * Update by player_id sync username and deleted_at
	 *
	 * @param integer $player_id The field, player.id .
	 * @return boolean $returnBool The affected_rows not empty.
	 */
	function cronjob4UpdateByPlayer_idSyncPlayer($player_id){
		$returnBool = false;
		$selectFields = [];
		$selectFields['username'] = 'username';
		$selectFields['createdOn'] = 'created_on';
		$selectFields['deleted_at'] = 'deleted_at';
		$rowOfPlayer = $this->player->getPlayerByPlayerId($player_id, $selectFields);

		$affected_rows = 0;
		// @todo update agent_id
		if( ! empty($rowOfPlayer) ){
			$data = [];
			$data['username'] = $rowOfPlayer['username'];
			$data['created_on'] = $rowOfPlayer['created_on'];
			$data['deleted_at'] = $rowOfPlayer['deleted_at'];
			$fieldName = 'player_id';
			$fieldvalue = $player_id;
			$affected_rows = $this->updateDataBy($fieldName,$fieldvalue, $data);
		}
		$returnBool = ! empty($affected_rows);
		return $returnBool;
	}
	/**
	 * Update by player_id sync agent_id
	 *
	 * To get the agent_id for sync the player.
	 *
	 * @param integer $player_id The field, player.id .
	 * @return boolean $returnBool The affected_rows not empty.
	 */
	function cronjob4UpdateByPlayer_idSyncAgent_id($player_id){
		$returnBool = false;
		$selectFields = [];
		// $selectFields['agent_name'] = 'agent_name'; // just check for dev.
		$selectFields['player.agent_id'] = 'agent_id'; // single assigned will be return a value, Not an array.
		$agent_id = $this->player->getAgentOfPlayer($player_id, $selectFields);

		$affected_rows = 0;
		// update agent_id
		if( ! empty($agent_id) ){
			$data = [];
			$data['agent_id'] = $agent_id;
			$fieldName = 'player_id';
			$fieldvalue = $player_id;
			$affected_rows = $this->updateDataBy($fieldName,$fieldvalue, $data);
		}
		$returnBool = ! empty($affected_rows);
		return $returnBool;
	} // EOF cronjob4UpdateByPlayer_idSyncAgent_id

	/**
	 * Update by player_id sync affiliate_id
	 *
	 * @param integer $player_id The field, player.id .
	 * @return boolean $returnBool The affected_rows not empty.
	 */
	function cronjob4UpdateByPlayer_idSyncAffiliate_id($player_id){
		$returnBool = false;
		$selectFields = [];
		$selectFields['affiliates.username'] = 'affiliate_name'; // for check for dev and assigned for update agent_id.
		$selectFields['affiliates.affiliateId'] = 'affiliate_id';
		$row4AgentOfPlayer = $this->player->getPlayerAffiliateUsername( $player_id, $selectFields); // @rbo =remove before online, "Checked OK."

		$affected_rows = 0;
		// @todo update agent_id
		if( ! empty($row4AgentOfPlayer['affiliate_id']) ){
			$data = [];
			$data['affiliate_id'] = $row4AgentOfPlayer['affiliate_id'];
			$fieldName = 'player_id';
			$fieldvalue = $player_id;
			$affected_rows = $this->updateDataBy($fieldName,$fieldvalue, $data);
		}
		$returnBool = ! empty($affected_rows);
		return $returnBool;
	} // EOF cronjob4UpdateByPlayer_idSyncAffiliate_id

	/**
	 * Update by player_id sync referee_player_id
	 *
	 * @param integer $player_id The field, player.id .
	 * @return boolean $returnBool The affected_rows not empty.
	 */
	function cronjob4UpdateByPlayer_idSyncReferee_player_id($player_id){
		$returnBool = false;
		// $selectFields = [];
		// $selectFields['affiliates.username'] = 'affiliate_name'; // just check for dev.
		// $selectFields['playerfriendreferral.playerId'] = 'referrerplayer_id';

		// $selectFields = [];
		// $selectFields['pfr.playerId'] = 'playerId';
		// $playerfriendreferralRow = $this->player->getReferredPlayer($player_id, $selectFields);
		// $referee_player_id = $playerfriendreferralRow['playerId'];


		$referee_player_id =  $this->player->getRefereePlayerId($player_id);

// $this->utils->debug_log('playerfriendreferralRow', $playerfriendreferralRow);
// $this->utils->debug_log('referee_player_id', $referee_player_id);
		$affected_rows = 0;
		// @todo update agent_id
		if( ! empty($referee_player_id) ){
			$data = [];
			$data['referee_player_id'] = $referee_player_id;
			$fieldName = 'player_id';
			$fieldvalue = $player_id;
			$affected_rows = $this->updateDataBy($fieldName,$fieldvalue, $data);
		}
		$returnBool = ! empty($affected_rows);
		return $returnBool;
	}

	/**
	 * Update by player_id sync first_deposit_amount and first_deposit_datetime
	 *
	 * @param integer $player_id The field, player.id .
	 * @return boolean $returnBool The affected_rows not empty.
	 */
	function cronjob4UpdateByPlayer_idSyncFirstDeposit($player_id){
		$first_last_deposit = $this->player_manager->getPlayerFirstLastApprovedTransaction($player_id, Transactions::DEPOSIT);

		if( ! empty($first_last_deposit) ){

			/// Get the first_deposit
			$first_deposit_datetime = $first_last_deposit['first']; // time
			if( ! empty($first_deposit_datetime) ){
				$firstDepositRow = $this->transactions->getLastDepositInfoByDate($player_id, $first_deposit_datetime, $first_deposit_datetime);
				$amount = $firstDepositRow['amount'];
			}else{
				$first_deposit_datetime = null;
				$amount = 0;
			}
			// update
			$data = [];
			$data['first_deposit_amount'] = $amount;
			$data['first_deposit_datetime'] = $first_deposit_datetime;
// $this->utils->debug_log('data::::754:', $data);
			$fieldName = 'player_id';
			$fieldvalue = $player_id;
			$affected_rows = $this->updateDataBy($fieldName,$fieldvalue, $data);
		}
		$returnBool = ! empty($affected_rows);
		return $returnBool;

	} // EOF cronjob4UpdateByPlayer_idSyncFirstDeposit



} // EOF Player_relay


// zR to open all folded lines
// vim:ft=php:fdm=marker
/* End of file player_relay.php */
/* Location: ./application/models/player_relay.php */
