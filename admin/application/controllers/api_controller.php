<?php

/**
 *
 * @deprecated
 *
 */
class Api_controller extends CI_Controller {

	function __construct() {
		parent::__construct();

		exit(1);

		//$this->load->helper('url');
		//$this->load->library(array('form_validation','authentication', 'player_functions','template'));
		$this->load->library(array('game_pt_api', 'api_functions'));

	}

	function index() {
		echo 'Initialize test!';

		//withdraw
		//$this->showEntityStructure();

		//create player
		//$this->createPlayer('pt_fhiuser02',21);

		//get player info
		//$this->getPlayerInfo('ACCOUNTS2');

		//deposit
		//$this->deposit('pn_fhiuser3',1000);

		//withdraw
		//$this->withdraw('pn_fhiuser3',1000);

		//is player online
		//$this->isPlayerOnline('pn_fhiuser7');

		//search player
		//$this->searchPlayer(API_ADMIN_NAME);

		//get all reports
		//$this->getAllReports();

		//get all players
		$this->getPlayers(API_ADMIN_NAME);

		//generate key
		//$this->generatekey(API_ADMIN_NAME);

		//update player
		//$this->updatePlayerInfo('PTTEST2','159357');

		//reset failed login attempts
		//$this->resetFailedLogin('pn_fhiuser1');

		//$this->load->view('ptgame/testgame');
	}

	/**
	 * displaying the business structure over API call
	 *
	 * @return json
	 */
	function showEntityStructure() {
		$data = 'entity/structure';
		$api_call = $this->game_pt_api->callApi($data);
		//save pw to player table

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * creating new sub-structure
	 *
	 * @return json
	 */
	function createSubStructure($entityName) {
		$data = 'entity/entityname/' . $entityName . '/parententityname/FHICNYSTRUCTURE';
		$api_call = $this->game_pt_api->callApi($data);
		//save pw to player table

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * generating key for kiosk API access for a sub-entity
	 *
	 * @param name
	 *
	 * @return json
	 */
	function generateKey($entityName) {
		$data = 'entity/generatekey/name/' . $entityName;
		$api_call = $this->game_pt_api->callApi($data);

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * display sub-entity structure
	 *
	 * @return json
	 */
	function showEntitySubStructure($entityName) {
		$data = 'entity/structure/entityname/' . $entityName;
		$api_call = $this->game_pt_api->callApi($data);
		//save pw to player table

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * create an admin account inside a sub-entity
	 *
	 * @return json
	 */
	function createAdminAccountInsideSubEntity($adminName, $entityName, $countryCode, $pw) {
		$data = 'admin/create/adminname/' . $adminName . '/entityname/' . $entityname . '/countrycode/' . $countryCode . '/password/' . $pw;
		$api_call = $this->game_pt_api->callApi($data);
		//save pw to player table

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * create a kiosk inside a sub-entity
	 *
	 * @return json
	 */
	function createKioskInsideSubEntity($kioskName, $entityName, $languageCode, $countryCode) {
		$data = 'kiosk/create/kioskname/' . $kioskName . '/entityname/' . $entityname . '/languagecode/' . $languageCode . '/countrycode/' . $countryCode . '/';
		$api_call = $this->game_pt_api->callApi($data);
		//save pw to player table

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * increase the sub-entity admin deposit balance
	 *
	 * @return json
	 */
	function increaseSubEntityAdminDepositBalance($fromAdminName, $toadminname, $depositBalanceAmount) {
		$data = 'admin/increaseBalance/fromadminname/' . $fromAdminName . '/toadminname/' . $toadminname . '/depositbalanceamount/' . $depositBalanceAmount;
		$api_call = $this->game_pt_api->callApi($data);
		//save pw to player table

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * decrease the sub-entity admin deposit balance
	 *
	 * @return json
	 */
	function decreaseSubEntityAdminDepositBalance($fromAdminName, $toadminname, $depositBalanceAmount) {
		$data = 'admin/decreaseBalance/fromadminname/' . $fromAdminName . '/toadminname/' . $toadminname . '/depositbalanceamount/' . $depositBalanceAmount;
		$api_call = $this->game_pt_api->callApi($data);
		//save pw to player table

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Player Data Synchronization ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//

	/**
	 * create player
	 *
	 * @param   playerName str
	 *
	 * @return json
	 */
	function createPlayer($playerName, $playerId) {
		$data = 'player/create/playername/' . $playerName . '/adminname/' . API_ADMIN_NAME . '/kioskname/' . API_KIOSK_NAME . '/';
		$api_call = $this->game_pt_api->callApi($data);

		//save pw to player table
		if (isset($api_call['errorcode']) != '') {
			echo 'error: ' . $api_call['errorcode'];
		} else {
			$playerProfileData = array('playerId' => $playerId,
				'gameType' => 'pt',
				'password' => $api_call['result']['password'],
				'status' => 'active');

			//var_dump($playerProfileData);exit();
			$this->api_functions->savePlayerGameProfile($playerProfileData);
		}

		//
		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * get player info
	 *
	 * @param   playerName str
	 *
	 * @return json
	 */
	function getPlayerInfo($playerName) {
		$data = 'player/info/playername/' . $playerName;
		$api_call = $this->game_pt_api->callApi($data);

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * update player info
	 *
	 * @param   playerName str
	 * @param   playerPassword str
	 *
	 * @return json
	 */
	function updatePlayerInfo($playerName, $playerPassword) {
		$data = 'player/update/playername/' . $playerName . '/password/' . $playerPassword;
		$api_call = $this->game_pt_api->callApi($data);

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * check player is online or has existing login session
	 *
	 * @param playerName
	 *
	 * @return json
	 */
	function isPlayerOnline($playerName) {
		$data = 'player/online/playername/' . $playerName;
		$api_call = $this->game_pt_api->callApi($data);

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * logout player - force logout player or remove existing session
	 *
	 * @param playerName
	 *
	 * @return json
	 */
	function logoutPlayer($playerName) {
		$data = 'player/logout/playername/' . $playerName;
		$api_call = $this->game_pt_api->callApi($data);

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * reset failed login
	 *
	 * @param playerName
	 *
	 * @return json
	 */
	function resetFailedLogin($playerName) {
		$data = 'player/resetFailedLogin/playername/' . $playerName;
		$api_call = $this->game_pt_api->callApi($data);

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * search player
	 *
	 * @param   adminName
	 *
	 * @return json
	 */
	function searchPlayer($adminName) {
		$data = 'player/list/adminname/' . $adminName;
		$api_call = $this->game_pt_api->callApi($data);

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * get players
	 *
	 * @param   kioskadminname
	 *
	 * @return json
	 */
	function getPlayers($kioskadminname) {
		$data = 'player/list/kioskadmin/' . $kioskadminname . '/page/4';
		$api_call = $this->game_pt_api->callApi($data);

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * deposit chips to player account
	 *
	 * @param   playerName str
	 * @param   amount int
	 * @param   playerName str
	 *
	 * @return json
	 */
	function deposit($playerName, $amount) {
		$data = 'player/deposit/playername/' . $playerName . '/amount/' . $amount . '/adminname/' . API_ADMIN_NAME . '/externaltranid/' . API_EXTERNALTRANID_DEPOSIT;
		$api_call = $this->game_pt_api->callApi($data);

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * withdrawing chips from a player account
	 *
	 * @param   playerName str
	 * @param   amount int
	 * @param   playerName str
	 *
	 * @return json
	 */
	function withdrawal($playerName, $amount) {
		$data = 'player/withdraw/playername/' . $playerName . '/amount/' . $amount . '/adminname/' . API_ADMIN_NAME . '/externaltranid/' . API_EXTERNALTRANID_WITHDRAWAL;
		$api_call = $this->game_pt_api->callApi($data);

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * check transanction status
	 *
	 * @param   externalTransactionId
	 *
	 * @return json
	 */
	function checkTransanctionStatus($externalTransactionId) {
		$data = 'player/checktransaction/externaltransactionid/' . $externalTransactionId;
		$api_call = $this->game_pt_api->callApi($data);

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * get online players
	 *
	 * @return json
	 */
	function getOnlinePlayers() {
		$data = 'player/list/online/1';
		$api_call = $this->game_pt_api->callApi($data);
		//save pw to player table

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Check reports ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//

	/**
	 * show a list of available reports
	 *
	 * @param   externalTransactionId
	 *
	 * @return json
	 */
	function getAllReports() {
		$data = 'player/customreport/getallreports/';
		$api_call = $this->game_pt_api->callApi($data);

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * Get specific reports
	 *
	 * @param   adminName
	 *
	 * @return json
	 */
	function getSpecificReport($kioskAdmin) {
		$data = 'customreport/getform/reportname/' . $kioskAdmin;
		$api_call = $this->game_pt_api->callApi($data);

		echo '<pre>';
		print_r($api_call);
		echo '</pre>';
	}

	/**
	 * view games
	 *
	 * @return json
	 */
	function viewGames() {
		$this->load->view('ptgame/ptgames');
	}

	function testAPI() {
		echo date('Y-m-d', strtotime('-60 day')) . '%2003:28:58';exit();
		//$data = 'customreport/getdata/reportname/PlayerStats/startdate/' . date('Y-m-d', strtotime('-30 day')) . '%2003:28:58/enddate/' . date('Y-m-d', strtotime('-25 day')) . '%2003:29:04/';
		$data = 'customreport/getdata/reportname/PlayerStats/startdate/' . date('Y-m-d', strtotime('-60 day')) . '%2003:28:58/enddate/' . date('Y-m-d', strtotime('+1 day')) . '%2003:29:04/timeperiod/specify/playername/RHAIDAN08/adminname/HLLCNYTLA/kioskname/HLLCNYTLK/entityname/HLLCNYTLE/platform/flash/reportby/player';
		$api_call = $this->game_pt_api->callApi($data);
		//$api_result = $api_call['result'];

		echo '<pre>';
		print_r($api_call);
		echo "</pre>";exit();
		//$this->getSpecificReport(API_ADMIN_NAME);
	}

	/**
	 * Get player reports everyday
	 *
	 * @param   adminName
	 *
	 * @return json
	 */
	/*function getSpecificReport($kioskAdmin) {
$data = 'customreport/getform/reportname/'.$kioskAdmin;
$api_call = $this->game_pt_api->callApi($data);

echo '<pre>';
print_r($api_call);
echo '</pre>';
}*/
}