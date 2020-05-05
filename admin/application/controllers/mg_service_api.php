<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Mg_service_api extends BaseController {

	private $APIauth;
	private $seq;
	private $name;
	private $player_id;
	private $timestamp;

	const ERROR_CODE = array(
			"6000" => "Unspecified Error",
			"6001" => "The player token is invalid.",
			"6002" => "The player token expired.",
			"6003" => "The authentication credentials for the API are incorrect.",
			"6101" => "Login validation failed.  Login name or password is incorrect.",
			"6501" => "Already processed with different details.",
			"6503" => "Player has insufficient funds.",
			"#" => "",
		);

	function __construct() {
		parent::__construct();
		$this->game_api_mg_quickfire = $this->utils->loadExternalSystemLibObject(MG_QUICKFIRE_API);
		$this->APIauth = $this->game_api_mg_quickfire->getAPIauth();
	}

	public function token($username, $password) {
		$result = $this->game_api_mg_quickfire->login($username, $password);
		$token = isset($result['token_attr']) ? $result['token_attr'] : NULL;
		$this->output->set_content_type('application/json')->set_output(json_encode(array('token' => $token)));
	}

	public function test($method, $params1 = NULL, $params2 = NULL) {
		$methods = array(
			'GetRollbackQueueData',
			'GetCommitQueueData',
			'ManuallyValidateBet',
			'GetFailedEndGameQueue',
			'ManuallyCompleteGame',
			'ValidateApiUser',
		);
		$result = $this->game_api_mg_quickfire->{$method}($params1,$params2);
		var_dump($result);
	}

	public function index() {

		$result = file_get_contents('php://input');

		log_message('error', $result);

		$reqXml 		 = new SimpleXMLElement($result);
		$reqString 		 = json_encode($reqXml);
		$reqArray 		 = json_decode($reqString, true);

		$this->name 	 = isset($reqArray['methodcall']['@attributes']['name']) ? $reqArray['methodcall']['@attributes']['name'] : NULL;
		$this->seq 		 = isset($reqArray['methodcall']['call']['@attributes']['seq']) ? $reqArray['methodcall']['call']['@attributes']['seq'] : NULL;
		$this->token 	 = isset($reqArray['methodcall']['call']['@attributes']['token']) ? $reqArray['methodcall']['call']['@attributes']['token'] : NULL;
		$this->timestamp = date('Y-m-d H:i:s', strtotime($reqArray['methodcall']['@attributes']['timestamp']));

		if ($response_result = $this->get_response_result_by_seq($this->seq)) {
			return $this->returnXml($response_result['content']);
		}

		$this->db->insert('response_results', array(
			'note' 				=> $this->seq,
			'system_type_id' 	=> $this->game_api_mg_quickfire->getPlatformCode(),
			'request_api' 		=> $this->name,
			'request_params' 	=> $reqString,
			'created_at' 		=> date('Y-m-d H:i:s'),
		));

		$this->response_result_id = $this->db->insert_id();

		try {

			$ApiUsername 	= $reqArray['methodcall']['auth']['@attributes']['login']; 
			$ApiPassword 	= $reqArray['methodcall']['auth']['@attributes']['password']; 

			if ($this->APIauth['APIusername'] == $ApiUsername && $this->APIauth['APIpassword'] == $ApiPassword) {

				switch($this->name){

					case 'login':
						$resXml = $this->login($reqArray);
						break;

					case 'getbalance':
						$resXml = $this->getbalance($reqArray);
						break;

					case 'refreshtoken':
						$resXml = $this->refreshtoken($reqArray);
						break;

					case 'play':
						$resXml = $this->play($reqArray);
						break;

					case 'endgame':
						$resXml = $this->endgame($reqArray);
						break;

					default: throw new Exception('Unknown method name.', 6000);

				}

			} else throw new Exception('The authentication credentials for the API are incorrect.', 6003);

		} catch (Exception $e) {

			log_message('error', $e->getMessage());

			$resXml = $this->returnError($e->getCode());

		}

		$player_id = $this->game_api_mg_quickfire->getPlayerIdByToken($this->token, FALSE);

		$this->db->update('response_results', array(
			'content' 	=> $resXml,
			'player_id' => $player_id
		), array(
			'id' => $this->response_result_id
		));

		$this->utils->debug_log('CALLBACK RESPONSE ====================', $resXml);

		$this->returnXml($resXml);

	}

	private function get_response_result_by_seq($seq) {
		return $this->db->select('content')->get_where('response_results', array('note' => $seq))->row_array();
	}

	private function login($reqArray){
		if ($this->token) {
			$result = $this->game_api_mg_quickfire->loginByToken($this->token);
		} else {

			$username = $reqArray['methodcall']['call']['@attributes']['loginname'];
			$password = $reqArray['methodcall']['call']['@attributes']['password'];
			$result = $this->game_api_mg_quickfire->login($username, $password);

		}

		$this->token = isset($result['token_attr']) ? $result['token_attr'] : NULL;

		$result['seq_attr'] = $this->seq; 

		if ($result['success']) {

 			unset($result['success']);

			return $this->generateMgXml($result);

		} else throw new Exception("Error Processing Request", $result['error_code']);

	}

	private function getbalance($reqArray){

		$this->CI->load->model(array('player_model'));

		$playerId = $this->game_api_mg_quickfire->getPlayerIdByToken($this->token);
		$username = $this->CI->player_model->getUsernameById($playerId);

		if ( ! empty($username)) {

			$result = $this->game_api_mg_quickfire->queryPlayerBalance($username);

			return $this->generateMgXml(array(
				'seq_attr' => $this->seq,
				'token_attr' => $this->token, 
				'balance_attr' => $result['balance'] * 100, 
				'bonusbalance_attr' => '0',
				'extinfo' => NULL
			));

		} else throw new Exception("Error Processing Request", 6000);

	}

	private function refreshtoken($reqArray){

		$token = $reqArray['methodcall']['call']['@attributes']['token'];
		
		$playerId = $this->game_api_mg_quickfire->getPlayerIdByToken($token);

		if (empty($playerId)) {
			throw new Exception("The player token is invalid.", 6001);
		// } else if (TRUE) {
		// 	throw new Exception("The player token expired.", 6002);
		}

		$result['seq_attr'] = $this->seq;
		$result['token_attr'] = $this->token; // TODO: GET NEW TOKEN
		$result['extinfo'] = NULL; 

		return $this->generateMgXml($result);

	}

	private function play($reqArray, $playerId = NULL) {

		$this->load->model(array('wallet_model','game_logs','mg_quickfire_game_logs'));
		
		$params 							= $reqArray['methodcall']['call']['@attributes'];
		$token 								= $reqArray['methodcall']['call']['@attributes']['token'];
		$system 							= $reqArray['methodcall']['@attributes']['system'];

		$playtype 							= isset($params['playtype']) ? $params['playtype'] : NULL;
		$gameid 							= isset($params['gameid']) ? $params['gameid'] : NULL;
		$gamereference 						= isset($params['gamereference']) ? $params['gamereference'] : NULL;
		$actionid 							= isset($params['actionid']) ? $params['actionid'] : NULL;
		$actiondesc 						= isset($params['actiondesc']) ? $params['actiondesc'] : NULL;
		$amount 							= isset($params['amount']) ? $params['amount'] : NULL;
		$start 								= isset($params['start']) ? $params['start'] : NULL;
		$finish 							= isset($params['finish']) ? $params['finish'] : NULL;
		$offline 							= isset($params['offline']) ? $params['offline'] : NULL;
		$currency 							= isset($params['currency']) ? $params['currency'] : NULL;
		$freegame 							= isset($params['freegame']) ? $params['freegame'] : NULL;
		$freegameofferinstanceid 			= isset($params['freegameofferinstanceid']) ? $params['freegameofferinstanceid'] : NULL;
		$freegamenumgamesplayed 			= isset($params['freegamenumgamesplayed']) ? $params['freegamenumgamesplayed'] : NULL;
		$freegamenumgamesremaining 			= isset($params['freegamenumgamesremaining']) ? $params['freegamenumgamesremaining'] : NULL;
		$clienttypeid 						= isset($params['clienttypeid']) ? $params['clienttypeid'] : NULL;

		$extinfo 							= $reqArray['methodcall']['call']['extinfo'];

		if ($playerId) {

			$username = $this->player_model->getUsernameById($playerId);
			$game_username = $this->game_api_mg_quickfire->getGameUsernameByPlayerId($playerId);

		} else {

			if ($offline == 'true') {
				list($game_username) = explode('_', $token);
				$playerId = $this->game_api_mg_quickfire->getPlayerIdInGameProviderAuth($game_username);
				$username = $this->game_api_mg_quickfire->getPlayerUsernameByGameUsername($game_username);
				$token = $this->game_api_mg_quickfire->getPlayerToken($playerId);
			} else {
				$playerId = $this->game_api_mg_quickfire->getPlayerIdByToken($token);
				$username = $this->player_model->getUsernameById($playerId);
				$game_username = $this->game_api_mg_quickfire->getGameUsernameByPlayerId($playerId);
			}

		}

		if (empty($playerId)) {
			throw new Exception("The player token is invalid.", "6001");
		}

		$external_uniqueid = $this->seq;

		$insert_data = array(
			'system'						=> $system,
			'timestamp'						=> $this->timestamp,
			'token'							=> $token,
			'seq'							=> $this->seq,
			'playtype'						=> $playtype,
			'gameid'						=> $gameid,
			'gamereference'					=> $gamereference,
			'actionid'						=> $actionid,
			'actiondesc'					=> json_encode($actiondesc),
			'amount'						=> $amount,
			'start'							=> $start,
			'finish'						=> $finish,
			'offline'						=> $offline,
			'currency'						=> $currency,
			'freegame'						=> $freegame,
			'freegameofferinstanceid'		=> $freegameofferinstanceid,
			'freegamenumgamesplayed'		=> $freegamenumgamesplayed,
			'freegamenumgamesremaining'		=> $freegamenumgamesremaining,
			'clienttypeid'					=> $clienttypeid,
			'extinfo'						=> json_encode($extinfo),
			'game_username'					=> $game_username,
			'external_uniqueid'				=> $external_uniqueid,
			'gameshortcode'					=> $gamereference,
			'response_result_id'			=> $this->response_result_id,
			'created_at'					=> $this->timestamp,
		);

		$game_record = $this->mg_quickfire_game_logs->get_game_record_by_external_uniqueid($external_uniqueid);

		if (empty($game_record)) { // check for idempotency

			$record = $this->mg_quickfire_game_logs->get_record($insert_data['system'], $insert_data['game_username'], $insert_data['gamereference'], $insert_data['gameid'], $insert_data['playtype'], $insert_data['actionid']);
			
			if ($record) {

				$previous_response = $this->get_response_result_by_seq($record['seq']);

				if (isset($previous_response, $previous_response['content'])) { # MG QUICKFIRE TIMEOUT BUT SUCCESSFULLY PROCESSED IN OUR SERVER

					$resXml 		 = new SimpleXMLElement($previous_response['content']);
					$resString 		 = json_encode($resXml);
					$resArray 		 = json_decode($resString, true);

					if (isset($resArray['methodresponse']['result']['@attributes'])) {

						$resp = $resArray['methodresponse']['result']['@attributes'];

						$result = array();
						$result['seq_attr'] = $this->seq; 
						$result['token_attr'] = $this->token;
						$result['balance_attr'] = $resp['balance']; 
						$result['bonusbalance_attr'] = $resp['bonusbalance']; 
						$result['exttransactionid_attr'] = $resp['exttransactionid']; 
						$result['extinfo'] = NULL; 

						return $this->generateMgXml($result);
						
					}

				}

				# PROCESSED BY QUEUE THAT'S WHY NO RESPONSE RESULT
				$this->utils->debug_log('mg_quickfire already processed with different details', $record);
				throw new Exception('Already processed with different details.', 6501);

			}

			// TODO: throw new Exception("Already processed with different details.", 6501); Play (Type = Refund)
			// TODO: throw new Exception('Player has insufficient funds.', 6503); Play (Bet / TransferToMgs)
			// TODO: throw new Exception("The external system name does not exist (gamereference).", 6511); Play (Bet / TransferToMgs)

			switch ($playtype) {

				case 'bet':
				case 'transfertomgs':
				case 'tournamentpurchase':

					$queryPlayerBalanceResult = $this->game_api_mg_quickfire->queryPlayerBalance($username);
					if ((($queryPlayerBalanceResult['balance'] * 100) - $amount) < 0) {
						throw new Exception('Player has insufficient funds.', 6503);
					}

					// Debit the player’s balance.
					$success = $this->subtract_amount($playerId, $amount);
					if ( ! $success) {
						throw new Exception('subtract_amount error', 6000);
					}
					break;

				case 'refund':

					// If a refund transaction is sent but the bet is not successfully processed in your system, we recommend the API responds successfully with an exttransactionid value of DEBIT-NOT-RECEIVED, as the transaction does not exist.
					$bet_record = $this->mg_quickfire_game_logs->get_bet_record_by_action_id($actionid);
					if (empty($bet_record)) {
						$exttransactionid = 'DEBIT-NOT-RECEIVED';
						break; 
					}

				case 'win':
				case 'transferfrommgs':
				case 'progressivewin':
					$success = $this->add_amount($playerId, $amount);
					if ( ! $success) {
						throw new Exception('add_amount error', 6000);
					}
					break;

				case 'admin':
					// Debit or credit the player’s balance. The actiondesc attribute provides more details for this balance update.
					$success = $this->add_amount($playerId, $amount);
					if ( ! $success) {
						throw new Exception('add_amount error', 6000);
					}
					// $this->subtract_amount($playerId, $amount);
					break;
				
				default:
					throw new Exception('Unknown Play Type', '#');

			}

			$queryPlayerBalanceResult = $this->game_api_mg_quickfire->queryPlayerBalance($username);
			$balance = $queryPlayerBalanceResult['balance'] * 100;

			$insert_data['after_balance'] = $queryPlayerBalanceResult['balance'];

			$this->mg_quickfire_game_logs->insertGameLogs($insert_data);

			# MERGE TO GAME LOGS IF FINISHED

			$data_for_merging = $this->mg_quickfire_game_logs->getDataForMerging($insert_data['gamereference'], $insert_data['game_username'], $insert_data['gameid']);
			
			if ( ! empty($data_for_merging)) {
				$this->game_api_mg_quickfire->mergeToGameLogs(
					$data_for_merging['system'], 
					$data_for_merging['gamereference'], 
					$data_for_merging['game_username'], 
					$data_for_merging['gameid'], 
					$data_for_merging['bet_amount'], 
					$data_for_merging['refund_amount'], 
					$data_for_merging['after_balance'], 
					$data_for_merging['win_amount'], 
					$data_for_merging['start_at'], 
					$data_for_merging['end_at'],
					$insert_data['finish'] == 'true' ? Game_logs::STATUS_SETTLED : Game_logs::STATUS_PENDING
				);
			}
			
		}

		if ( ! isset($exttransactionid)) {
			$exttransactionid = $this->game_logs->getIdByExternalUniqueid($external_uniqueid, 'mg_quickfire_game_logs');
		}

		// Get after balance
		if ( ! isset($balance)) {
			$queryPlayerBalanceResult = $this->game_api_mg_quickfire->queryPlayerBalance($username);
			$balance = $queryPlayerBalanceResult['balance'] * 100;
		}

		$bonusbalance = 0;

		$result = array();
		$result['seq_attr'] = $this->seq; 
		$result['token_attr'] = $token;
		$result['balance_attr'] = $balance; 
		$result['bonusbalance_attr'] = $bonusbalance; 
		$result['exttransactionid_attr'] = $exttransactionid; 
		$result['extinfo'] = NULL; 

		return $this->generateMgXml($result);

	}

	private function subtract_amount($player_id, $amount) {

		if ($amount == 0) {
			return TRUE;
		}

		$game_platform_id = $this->game_api_mg_quickfire->getPlatformCode();

		$success = $this->wallet_model->lockAndTransForPlayerBalance($player_id, function () use ($game_platform_id, $player_id, $amount) {
			$success = $this->wallet_model->decSubWallet($player_id, $game_platform_id, $amount / 100);
			$this->utils->debug_log('mg_quickfire subtract_amount', 'player_id', $player_id, 'amount', $amount, 'success', $success);
			return $success;
		});

		return $success;

		// $lockedKey = NULL;

		// $lock_it = $this->utils->lockResourceBy($player_id, Utils::LOCK_ACTION_BALANCE, $lockedKey);
		// $this->utils->debug_log('lock subtract_amount', 'id', $player_id, $lock_it);

		// if ($lock_it) {
		// 	try {
		// 		$this->startTrans();
		// 		$this->endTransWithSucc();
		// 	} finally {
		// 		$this->utils->releaseResourceBy($player_id, Utils::LOCK_ACTION_BALANCE, $lockedKey);
		// 		$this->utils->debug_log('release subtract_amount lock', 'id', $player_id);
		// 	}
		// }

	}

	private function add_amount($player_id, $amount) {

		if ($amount == 0) {
			return TRUE;
		}

		$game_platform_id = $this->game_api_mg_quickfire->getPlatformCode();

		$success = $this->wallet_model->lockAndTransForPlayerBalance($player_id, function () use ($game_platform_id, $player_id, $amount) {
			$success = $this->wallet_model->incSubWallet($player_id, $game_platform_id, $amount / 100);
			$this->utils->debug_log('mg_quickfire add_amount', 'player_id', $player_id, 'amount', $amount, 'success', $success);
			return $success;
		});

		return $success;

		// $lockedKey = NULL;
		
		// $lock_it = $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
		// $this->utils->debug_log('lock add_amount', 'id', $playerId, $lock_it);

		// if ($lock_it) {
		// 	try {
		// 		$this->startTrans();
		// 		$this->wallet_model->incSubWallet($playerId, $this->game_api_mg_quickfire->getPlatformCode(), $amount / 100);
		// 		$this->endTransWithSucc();
		// 	} finally {
		// 		$this->utils->releaseResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
		// 		$this->utils->debug_log('release add_amount lock', 'id', $playerId);
		// 	}
		// }

	}

	private function endgame($reqArray, $player_id = NULL) {

		$this->load->model(array('wallet_model','game_logs','mg_quickfire_game_logs'));

		$token 	   = $reqArray['methodcall']['call']['@attributes']['token'];
		$seq 	   = $reqArray['methodcall']['call']['@attributes']['seq'];

		try {

			$params = $reqArray['methodcall']['call']['@attributes'];
			$gamereference = isset($params['gamereference']) ? $params['gamereference'] : NULL;
			$gameid = isset($params['gameid']) ? $params['gameid'] : NULL;
			$offline = isset($params['offline']) ? $params['offline'] : NULL;
			$extinfo = $reqArray['methodcall']['call']['extinfo'];

			if ($player_id) {

				$username = $this->player_model->getUsernameById($player_id);
				$game_username = $this->game_api_mg_quickfire->getGameUsernameByPlayerId($player_id);

			} else {

				if ($offline == 'true') {
					$game_username = $token;
					$player_id = $this->game_api_mg_quickfire->getPlayerIdInGameProviderAuth($game_username);
					$username = $this->game_api_mg_quickfire->getPlayerUsernameByGameUsername($game_username);
					$token = $this->game_api_mg_quickfire->getPlayerToken($player_id);
				} else {
					$player_id = $this->game_api_mg_quickfire->getPlayerIdByToken($token);
					$username = $this->player_model->getUsernameById($player_id);
					$game_username = $this->game_api_mg_quickfire->getGameUsernameByPlayerId($player_id);
				}
				
			}

			if (empty($gamereference) || empty($game_username) || empty($gameid)) {
				return $this->returnError('#');
			}

			$data_for_merging = $this->mg_quickfire_game_logs->getDataForMerging($gamereference, $game_username, $gameid);

			$data_for_merging['end_at'] = $this->timestamp;

			$this->game_api_mg_quickfire->mergeToGameLogs(
				$data_for_merging['system'], 
				$data_for_merging['gamereference'], 
				$data_for_merging['game_username'], 
				$data_for_merging['gameid'], 
				$data_for_merging['bet_amount'], 
				$data_for_merging['refund_amount'], 
				$data_for_merging['after_balance'], 
				$data_for_merging['win_amount'], 
				$data_for_merging['start_at'], 
				$data_for_merging['end_at']
			);

			$balance 		= $data_for_merging['after_balance'] * 100;
			$bonusbalance 	= 0;

			$result['seq_attr'] = $this->seq; 
			$result['token_attr'] = $token; 
			$result['balance_attr'] = $balance; 
			$result['bonusbalance_attr'] = $bonusbalance; 
			$result['extinfo'] = NULL; 

			return $this->generateMgXml($result);

		} catch (Exception $e) {
			return $this->returnError($e->getCode());
		}
	}

	private function generateMgXml($result) {
		$xml_object = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><pkt></pkt>");
		return $this->CI->utils->arrayToXml(array(
			'methodresponse' => array(
				'name_attr' => $this->name,
				'timestamp_attr' => substr(date('Y/m/d H:i:s.u', time()), 0, -3),
				'result' => $result,
			)
		), $xml_object);
	} 

	private function returnError($errorCode) {
		return $this->generateMgXml(array(
			'seq_attr' => $this->seq,
			'errorcode_attr' => $errorCode, 
			'errordescription_attr' => self::ERROR_CODE[$errorCode], 
			'extinfo' => NULL
		));
	}

}

///END OF FILE////////////