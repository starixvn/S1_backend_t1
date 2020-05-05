<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Betsoft_service_api extends BaseController {

    private $game_platform_id = BETSOFT_API;
    protected $tableName = "betsoft_game_logs";
    protected $freeRoundTableName = "betsoft_free_round_bonus";

    const ERROR_INTERNALERROR = 399;
    const ERROR_INVALIDHASH = 500;
    const ERROR_INVALIDTOKEN = 400;
    const ERROR_INSUFFICIENTFUNDS = 300;
    const ERROR_TRANSACTIONID = 302;
    const ERROR_UNKNOWNUSERID = 310;
    const ERROR_INVALIDAMOUNT = 602;
    const ERROR_INVALIDPARAMETER = 610;
    const SUCCESS = 'OK';
    const ERROR = 'ERROR';
    const FAILED = 'FAILED';

    const NO_REFUND_TRANSACTION = 0;
    const REFUND_TRANSACTION = 1;

    const ROUND_ONGOING = 0;
    const ROUND_FINISHED = 1;

    const BONUS_RELEASE = 1;
    const TRANS_SUCCESS = 1;
    const FREE_ROUND_BONUS_GAME_ID = 'frb'; // game code created by us

    const MD5_FIELDS_FOR_ORIGINAL=['user_id', 'transaction_id', 'is_round_finished', 'bet_amount','result_amount'];
    const MD5_FLOAT_AMOUNT_FIELDS=['bet_amount', 'result_amount'];

    const CALLBACKS = ['authenticate', 'bet_result', 'get_balance', 'refund_bet', 'get_account_info', 'bonus_release','bonus_win'];

    function __construct() {
        parent::__construct();
        $this->load->model(['wallet_model', 'game_provider_auth', 'game_logs', 'common_token', 'player_model', 'game_description_model']);
        $this->CI->load->helper('string');

        $this->api  = $this->utils->loadExternalSystemLibObject($this->game_platform_id);
        $this->pass_key = $this->api->passKey();
        $this->conversion_rate = $this->api->conversionRate();
        $this->lost_flag = $this->api->lossFlag();
        $this->forward_sites = $this->api->forwardSites();
        $this->token_prefix = $this->api->tokenPrefix();       # set to empty if not use forwarding site

        $this->token_prefix_len = 0;
        if (!empty($this->forward_sites)) {
            # make sure same lenght in key
            foreach ( $this->forward_sites as $key => $sites) {
                $this->token_prefix_len = strlen($key);
                break;
            }
        }
    }

    public function callback($method=null) {
        if($this->external_system->isGameApiActive($this->game_platform_id)) {
            $request = $this->getInputGetAndPost();

            $this->CI->utils->debug_log('request params ==>'.json_encode($request));

            if (in_array($method, self::CALLBACKS)) {
                // forward sites
                // game username prefix and token_prefix should be as forwarding site key
                if ($this->forward_sites) {
                    $this->CI->utils->debug_log('<<<<< FORWARD SITES ENABLED >>>>>');
                    if ($method == 'authenticate') {
                        $token_prefix = substr($request['token'], 0, $this->token_prefix_len);
                        if (isset($this->forward_sites[$token_prefix])) {
                            $url = $this->forward_sites[$token_prefix].$method.'?'.http_build_query($request);
                            $this->CI->utils->debug_log('AUTHENTICATE BETSOFT FORWARD URL',$url);
                            return $this->forwardCallback($url, $request);
                        } else {
                            $this->CI->utils->debug_log('INVALID FORWARDING, token prefix ====> '.$token_prefix);
                        }
                    } else {
                        $game_username = $request['userId'];
                        if ($this->forward_sites && preg_match("#^(?P<prefix>" . implode('|', array_keys($this->forward_sites)) . ")#", $game_username, $matches)) {
                            if (isset($this->forward_sites[$matches['prefix']])) {
                                $url = $this->forward_sites[$matches['prefix']].$method.'?'.http_build_query($request);
                                return $this->forwardCallback($url, $request);
                            }
                        } else {
                            $this->CI->utils->debug_log('INVALID FORWARDING, game username ====> '.$game_username);
                        }
                    }
                }

                $response = $this->process_callback($method, $request);

                $xml_object = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><EXTSYSTEM></EXTSYSTEM>");
                $xml_data = $this->CI->utils->arrayToXml($response, $xml_object);

                return $this->returnXml($xml_data);

            } else {
                $data = [
                    'code' => 'ERROR',
                    'message' => 'Betsoft callback not exist',
                    'available_callback' => self::CALLBACKS
                ];
                $this->returnJsonResult($data);
            }
        } else {
            $data  = [
                'code' => 'ERROR',
                'message' => 'Betsoft api is not active'
            ];
            $this->returnJsonResult($data);
        }
    }

    public function forwardCallback($url, $params) {
        list($header, $resultXml) = $this->api->httpCallApi($url, $params);
        $this->CI->utils->debug_log('forwardCallback', $url, $header, $resultXml);
        return $this->returnXml($resultXml);
    }

    public function process_callback($method, $params) {
        try {
            $data = $this->{$method}($params);
        } catch (Exception $e) {
            return $this->generateReturn($params, array(
                'RESULT' => self::ERROR,
                'CODE'   => self::ERROR_INTERNALERROR,
            ));
        }
        return $data;
    }

    // Get player subwallet balance in sbe
    public function player_balance($playerName) {
        return $this->api->dBtoGameAmount($this->api->queryPlayerBalance($playerName)['balance']);
    }

    public function authenticate($request) {

        $hash = $this->hash(array(
            $request['token']
        ));

        $this->CI->utils->debug_log('AUTHENTICATE HASH REQUEST =====> ',$request['hash'], ' GENERATED HASH =====> '.$hash, 'REQUEST PARAM '.json_encode($request));

        if ($request['hash'] == $hash) {

            $token = $request['token'];

            # USE FOR FORWARDING SITE
            if($this->token_prefix) {
                $token_prefix = substr($request['token'], 0, strlen($this->token_prefix));
                if ($token_prefix == $this->token_prefix) {
                    $token = substr($token, strlen($this->token_prefix));
                } else {
                    $this->CI->utils->debug_log('MISMATCH TOKEN. EXTRA INFO PREFIX TOKEN =====> ',$this->token_prefix, ' REQUEST TOKEN  =====> '.$token_prefix);
                }
            }

            $playerId = $this->common_token->getPlayerIdByToken($token);

            if (!$playerId) {
                return $this->generateReturn($request, array(
                    'RESULT' => self::ERROR,
                    'CODE'   => self::ERROR_INVALIDTOKEN,
                ));
            }

            $playerDetails  = $this->api->getPlayerDetails($playerId);
            $playerInfo     = $this->api->getPlayerInfo($playerId);
            $playerName     = $this->api->getGameUsernameByPlayerUsername($playerInfo->username);
            $balance        = $this->player_balance($playerInfo->username);

            $response = array(
                'RESULT'        => self::SUCCESS,
                'USERID'        => $playerName,                             # REQUIRED
                'USERNAME'      => $playerName,                             # OPTIONAL
                'FIRSTNAME'     => $playerDetails->firstName,               # OPTIONAL
                'LASTNAME'      => $playerDetails->lastName,                # OPTIONAL
                'EMAIL'         => $playerInfo->email,                      # OPTIONAL
                'CURRENCY'      => $this->api->getCurrency(),               # OPTIONAL
                'BALANCE'       => $balance,                                # current balance of player in cents on EC system side
            );
        } else {
            $response = array(
                'RESULT' => self::ERROR,
                'CODE'   => self::ERROR_INVALIDHASH,
            );
        }

        return $this->generateReturn($request, $response);
    }

    public function bet_result($request) {

        // base on api order
        if (isset($request['bet'])) {
            $hash = $this->hash(array(
                $request['userId'],
                $request['bet'],
                $request['isRoundFinished'],
                $request['roundId'],
                $request['gameId'],
            ));
        } else {
            $hash = $this->hash(array(
                $request['userId'],
                $request['win'],
                $request['isRoundFinished'],
                $request['roundId'],
                $request['gameId'],
            ));
        }

        $this->CI->utils->debug_log('BET RESULT HASH REQUEST =====> ',$request['hash'], ' GENERATED HASH =====> '.$hash);

        # Check the request parameters and validate the hash.
        if ($request['hash'] == $hash) {

            $playerId   = $this->api->getPlayerIdInGameProviderAuth($request['userId']);

            if ( ! $this->api->getGameUsernameByPlayerId($playerId)) {
                return $this->generateReturn($request, array(
                    'RESULT' => self::ERROR,
                    'CODE'   => self::ERROR_UNKNOWNUSERID,
                ));
            }

            $playerInfo = $this->api->getPlayerInfo($playerId);
            $balance = $this->player_balance($playerInfo->username);

            // process bet amount result amount and transaction_id(unique)
            list($bet_amount, $result_amount, $transaction_id, $is_player_win) = $this->process_bet_and_result($request);

            # Check if the transaction was already processed.
            $is_trans_id_exist = $this->get_transaction_id($transaction_id);

            $external_system_trans_id = uniqid();
            $amount  = $this->api->gameAmountToDB($bet_amount);

            # check balance
            if ($balance < $amount) {
                return $this->generateReturn($request, array(
                    'RESULT' => self::ERROR,
                    'CODE'   => self::ERROR_INSUFFICIENTFUNDS,
                ));
            }

            $last_sync_time = $this->CI->utils->getNowForMysql();
            $this->processGameLogs($request, $bet_amount, $result_amount,
                $external_system_trans_id, $transaction_id, $last_sync_time);


            if ( !$is_trans_id_exist) {
                if (isset($request['bet'])) {
                    // deduct balance when betting
                    $this->subtract_amount($playerId, $amount);           # subtract balance
                } else {
                    // process negative bets for rideempoker and craps games
                    if (isset($request['negativeBet'])) {
                        $negative_bet_amount = $this->api->gameAmountToDB($request['negativeBet']);
                        $this->add_amount($playerId, $negative_bet_amount);
                    }

                    if (isset($request['win'])) {
                        // sync only if there is already result
                        $token = random_string('unique');

                        // sync instantly
                        $dateTimeFrom = new DateTime($last_sync_time);
                        $dateTimeTo = new DateTime($last_sync_time);
                        $this->api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, null);
                        $this->api->syncMergeToGameLogs($token);

                        $result_amount  = $this->api->gameAmountToDB(abs($result_amount));

                        # add if win
                        if ($is_player_win) {
                            $this->add_amount($playerId, $result_amount);      # add balance
                        }
                    }
                }
            }

            # Return response to BSG.
            $response = array(
                'RESULT' => self::SUCCESS,
                'EXTSYSTEMTRANSACTIONID' => $external_system_trans_id,
                'BALANCE' => $this->player_balance($playerInfo->username), # current balance of player in cents on EC system side
            );

        } else {

            $response = array(
                'RESULT' => self::FAILED,
                'CODE'   => self::ERROR_INVALIDHASH,
            );
        }

        return $this->generateReturn($request, $response);
    }

    public function refund_bet($request) {

        $playerId = $this->api->getPlayerIdInGameProviderAuth($request['userId']);
        if ( ! $this->api->getGameUsernameByPlayerId($playerId) ) {
            return $this->generateReturn($request, array(
                'RESULT' => self::FAILED,
                'CODE' 	 => self::ERROR_UNKNOWNUSERID,
            ));
        }

        // from original logs
        $game_result = $this->get_game_result_by_trans_id($request['casinoTransactionId']);
        if ( empty($game_result['external_trans_id']) ) {
            return $this->generateReturn($request, array(
                'RESULT' => self::FAILED,
                'CODE' 	 => self::ERROR_TRANSACTIONID,
            ));
        }

        $hash = $this->hash(array(
            $request['userId'],
            $request['casinoTransactionId'],
        ));

        $this->CI->utils->debug_log('REFUND BET HASH REQUEST =====> ',$request['hash'], ' GENERATED HASH =====> '.$hash);

        # Check the request parameters and validate the hash.
        if ($request['hash'] == $hash) {

            // can add balance if no refund transaction yet
            if ($game_result['is_refunded'] == self::NO_REFUND_TRANSACTION) {
                $this->add_amount($game_result['player_id'], $this->api->gameAmountToDB($game_result['bet_amount']));      # add balance
                $this->set_game_to_refunded($game_result['external_trans_id']);
            }

            $extSystemTransactionId = uniqid();
            # Return response to BSG.
            $response = array(
                'RESULT' => self::SUCCESS,
                'EXTSYSTEMTRANSACTIONID' => $extSystemTransactionId, # not save
            );

        } else {
            $response = array(
                'RESULT' => self::FAILED,
                'CODE' 	 => self::ERROR_INVALIDHASH,
            );
        }

        return $this->generateReturn($request, $response);
    }

    public function get_balance($request) {

        $playerId = $this->api->getPlayerIdInGameProviderAuth($request['userId']);
        if ( ! $this->api->getGameUsernameByPlayerId($playerId) ) {
            return $this->generateReturn($request, array(
                'RESULT' => self::FAILED,
                'CODE' 	 => self::ERROR_UNKNOWNUSERID,
            ));
        }

        if ( !$playerId) {
            return $this->generateReturn($request, array(
                'RESULT' => self::ERROR,
                'CODE' 	 => self::ERROR_UNKNOWNUSERID,
            ));
        }

        $playerInfo = $this->api->getPlayerInfo($playerId);
        $balance = $this->player_balance($playerInfo->username);

        # Return response to BSG.
        $response = array(
            'RESULT' 	=> self::SUCCESS,
            'BALANCE' 	=> $balance, # current balance of player in cents on EC system side
        );

        return $this->generateReturn($request, $response);
    }

    public function get_account_info($request) {

        $hash = $this->hash(array(
            $request['userId'],
        ));

        $this->CI->utils->debug_log('GET ACCOUNT INFO BET HASH REQUEST =====> ',$request['hash'], ' GENERATED HASH =====> '.$hash);

        if ($request['hash'] == $hash) {

            $playerId = $this->api->getPlayerIdInGameProviderAuth($request['userId']);

            if ( ! $this->api->getGameUsernameByPlayerId($playerId)) {
                return $this->generateReturn($request, array(
                    'RESULT' => self::ERROR,
                    'CODE' 	 => self::ERROR_UNKNOWNUSERID,
                ));
            }

            $playerDetails = $this->api->getPlayerDetails($playerId);
            $playerInfo = $this->api->getPlayerInfo($playerId);
            $playerName = $this->api->getGameUsernameByPlayerUsername($playerInfo->username);

            $response = array(
                'RESULT' 	  => self::SUCCESS,
                'USERNAME' 	  => $playerName,					# OPTIONAL
                'FIRSTNAME'   => $playerDetails->firstName,		# OPTIONAL
                'LASTNAME' 	  => $playerDetails->lastName,		# OPTIONAL
                'EMAIL' 	  => $playerInfo->email,			# OPTIONAL
                'CURRENCY' 	  => $this->api->getCurrency(),
            );

        } else {
            $response = array(
                'RESULT' 	  => self::ERROR,
                'CODE' 	 	  => self::ERROR_INVALIDHASH,
            );
        }

        return $this->generateReturn($request, $response);
    }

    public function bonus_release($request) {

        $hash = $this->hash(array(
            $request['userId'],
            $request['bonusId'],
            $request['amount'],
        ));

        if ($request['hash'] == $hash) {

            $playerId = $this->api->getPlayerIdInGameProviderAuth($request['userId']);

            if ( ! $this->api->getGameUsernameByPlayerId($playerId)) {
                return $this->generateReturn($request, array(
                    'RESULT' => self::ERROR,
                    'CODE' 	 => self::ERROR_UNKNOWNUSERID,
                ));
            }

            $last_sync_time = $this->CI->utils->getNowForMysql(); // '2019-01-09 18:42:42';
            $id = $this->getFreeRoundBonusId($request['bonusId']);
            if(!$id) { // insert only if not exitst
                $data = [
                    'callback' => __FUNCTION__,
                    'bonus_id' => $request['bonusId'],
                    'amount' => $request['amount'],
                    'game_username' => $request['userId'],
                    'game_id' => self::FREE_ROUND_BONUS_GAME_ID,
                    'last_sync_time' => $last_sync_time,
                    'is_bonus_release' => self::BONUS_RELEASE,
                    'md5_sum' =>  $this->CI->game_logs->generateMD5SumOneRow($request, ['userId', 'bonusId', 'hash'], ['amount']),
                    'player_id' => $this->api->getPlayerIdInGameProviderAuth($request['userId'])
                ];
                $this->db->insert($this->freeRoundTableName, $data);

                // Add bonus
                $amount  = $this->api->gameAmountToDB($request['amount']);
                $this->add_amount($playerId, $amount);
            }

            $token = random_string('unique');

            // sync instantly
            $dateTimeFrom = new DateTime($last_sync_time);
            $dateTimeTo = new DateTime($last_sync_time);
            $this->api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, null);
            $this->api->syncMergeFreeBonusToGameLogs($token);

            $response = array(
                'RESULT' => self::SUCCESS,
            );
        } else {
            $response = array(
                'RESULT' => self::ERROR,
                'CODE' 	 => self::ERROR_INVALIDHASH,
            );
        }

        return $this->generateReturn($request, $response);
    }

    public function bonus_win($request) {

        if(!isset($request['amount']) || !isset($request['bonusId'])
            || !isset($request['userId']) || !isset($request['transactionId'])) {
            return $this->generateReturn($request, array(
                'RESULT' => self::ERROR,
                'CODE' 	 => self::ERROR_INVALIDPARAMETER,
            ));
        }

        if(!is_numeric($request['amount']) || empty($request['amount'])) {
            return $this->generateReturn($request, array(
                'RESULT' => self::ERROR,
                'CODE' 	 => self::ERROR_INVALIDAMOUNT,
            ));
        }

        $playerId = $this->api->getPlayerIdInGameProviderAuth($request['userId']);
        if ( ! $this->api->getGameUsernameByPlayerId($playerId)) {
            return $this->generateReturn($request, array(
                'RESULT' => self::ERROR,
                'CODE' 	 => self::ERROR_UNKNOWNUSERID,
            ));
        }

        $hash = $this->hash(array(
            $request['userId'],
            $request['bonusId'],
            $request['amount'],
        ));


        if ($request['hash'] == $hash) {

            $last_sync_time = $this->CI->utils->getNowForMysql();
            $id = $this->getTransactionId($request['transactionId']);

            if(!$id) { // insert only if not exitst
                $data = [
                    'callback' => __FUNCTION__,
                    'bonus_id' => $request['bonusId'],
                    'amount' => $request['amount'],
                    'game_username' => $request['userId'],
                    'game_id' => self::FREE_ROUND_BONUS_GAME_ID,
                    'last_sync_time' => $last_sync_time,
                    'is_trans_success' => self::TRANS_SUCCESS,
                    'transaction_id' => $request['transactionId'],
                    'md5_sum' =>  $this->CI->game_logs->generateMD5SumOneRow($request, ['userId', 'bonusId', 'hash'], ['amount']),
                    'player_id' => $this->api->getPlayerIdInGameProviderAuth($request['userId'])
                ];
                $this->db->insert($this->freeRoundTableName, $data);

                // Add bonus
                $amount  = $this->api->gameAmountToDB($request['amount']);
                $this->add_amount($playerId, $amount);
            }

            $token = random_string('unique');

            // sync instantly
            $dateTimeFrom = new DateTime($last_sync_time);
            $dateTimeTo = new DateTime($last_sync_time);
            $this->api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, null);
            $this->api->syncMergeFreeBonusToGameLogs($token);

            $playerId = $this->api->getPlayerIdInGameProviderAuth($request['userId']);
            $playerInfo = $this->api->getPlayerInfo($playerId);
            $balance = $this->player_balance($playerInfo->username);

            $response = array(
                'RESULT' 		=> self::SUCCESS,
                'BALANCE'		=> $balance
            );
        } else {
            $response = array(
                'RESULT' => self::ERROR,
                'CODE' 	 => self::ERROR_INVALIDHASH,
            );
        }

        return $this->generateReturn($request, $response);
    }

    // api return 2 response
    // request['bet'] process bet amount    (1st callback called)
    // request['win'] process result amount (2nd callback called)
    // NOTE : get bet amount and result amount if win_transid is not empty
    public function process_bet_and_result($request) {

        $is_player_win = false;
        if (isset($request['bet'])) {
            $bet_and_trans_id = explode("|",$request['bet']);
            $bet = $bet_and_trans_id['0'];
            $result_amount = 0;
            $transaction_id = $bet_and_trans_id['1'];
        } else {
            // only process result amount
            $win_and_trans_id = explode("|",$request['win']);
            $win = $win_and_trans_id['0']; # it can be loss if win is 0

            $bet_and_trans_id = $this->get_bet_trans_by_round($request['roundId']);
            $bet_and_trans_id = explode("|",$bet_and_trans_id);
            $bet = $bet_and_trans_id['0'];

            # get_bet_trans_by_round
            if($win == $this->lost_flag) {
                $result_amount = -$bet;
            } else {
                $result_amount = $win;   # use $win for result amount (sometimes it double)
                $is_player_win = true;
            }
            $transaction_id = $win_and_trans_id['1'];
        }

        return array($bet, $result_amount, $transaction_id, $is_player_win);
    }

    public function processGameLogs($request, $bet_amount, $result_amount, $external_system_trans_id, $transaction_id, $last_sync_time) {

        $player_id = $this->api->getPlayerIdInGameProviderAuth($request['userId']);

        $data['user_id'] = isset($request['userId']) ? $request['userId'] : null;
        $data['bet_transid'] = isset($request['bet']) ? $request['bet'] : 0;     # bet_amount|transactionId
        $data['win_transid'] = isset($request['win']) ? $request['win'] : 0;     # win_amount|transactionId
        $data['round_id'] = isset($request['roundId']) ? $request['roundId'] : null;
        $data['game_id'] = isset($request['gameId']) ? $request['gameId'] : null;
        $data['hash'] = isset($request['hash']) ? $request['hash'] : null;
        $data['game_session_id'] = isset($request['gameSessionId']) ? $request['gameSessionId'] : null;
        $data['negative_bet'] = isset($request['negativeBet']) ? $request['negativeBet'] : 0;
        $data['client_type'] = isset($request['clientType ']) ? $request['clientType '] : '';

        $round_flag = self::ROUND_ONGOING;
        if ( isset($request['isRoundFinished'])) {
            if ($request['isRoundFinished'] == 'true' || $request['isRoundFinished'] == self::ROUND_FINISHED) {
                $round_flag = self::ROUND_FINISHED;
            }
        }
        $data['is_round_finished'] = $round_flag;
        $data['bet_amount'] = $bet_amount;
        $data['result_amount'] = $result_amount;

        $data['transaction_id'] = $transaction_id;              # process trans id from bet_transid and win_transid
        $data['external_trans_id'] = $external_system_trans_id; # from callback response
        $data['last_sync_time'] = $last_sync_time;

        $data['md5_sum']=$this->CI->game_logs->generateMD5SumOneRow($data, self::MD5_FIELDS_FOR_ORIGINAL,
            self::MD5_FLOAT_AMOUNT_FIELDS);

        $data['external_uniqueid'] = $transaction_id;
        $data['player_id'] = $player_id ? $player_id : 0;

        return $this->syncGameLogs($data);
    }

    public function generateReturn($request, $response) {

        $data = array(
            'REQUEST'   => array(),
            'TIME'      => date('d M Y H:i:s'),
            'RESPONSE'  => $response,
        );

        foreach ($request as $key => $value) {
            $data['REQUEST'][strtoupper($key)] = $value;
        }

        return $data;
    }

    public function hash($parameters) {
        $parameters[] = $this->pass_key;
        $this->utils->debug_log('HASH PARAMS ', implode('', $parameters), 'PARAMS =====> ', json_encode($parameters));
        return md5(implode('', $parameters));
    }

    private function add_amount($player_id, $amount) {
        if (empty($amount)) {
            return true;
        }

        $game_platform_id = $this->api->getPlatformCode();

        $success = $this->wallet_model->lockAndTransForPlayerBalance($player_id, function () use ($game_platform_id, $player_id, $amount) {
            $success = $this->wallet_model->incSubWallet($player_id, $game_platform_id, $amount);
            $this->utils->debug_log('betsoft add_amount', 'player_id', $player_id, 'amount', $amount, 'success', $success);
            return $success;
        });

        return $success;
    }

    private function subtract_amount($player_id, $amount) {
        if (empty($amount)) {
            return true;
        }
        $game_platform_id = $this->api->getPlatformCode();
        $success = $this->wallet_model->lockAndTransForPlayerBalance($player_id, function () use ($game_platform_id, $player_id, $amount) {
            $success = $this->wallet_model->decSubWallet($player_id, $game_platform_id, $amount);
            $this->utils->debug_log('betsoft subtract_amount', 'player_id', $player_id, 'amount', $amount, 'success', $success);
            return $success;
        });

        return $success;
    }

    // transaction_id unique record
    public function get_transaction_id($trans_id) {
        $this->db->select('transaction_id')->from($this->tableName)->where('transaction_id', $trans_id);
        $query = $this->db->get();
        $row = $query->row_array();

        $trans_id = null;
        if(!empty($row)) {
            $trans_id = $row['transaction_id'];
        }
        return $trans_id;
    }

    // external_trans_id = response from bet_result
    public function get_game_result_by_trans_id($external_trans_id) {
        $this->db->select('*')->from($this->tableName)->where('transaction_id', $external_trans_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function set_game_to_refunded($external_trans_id) {
        $this->db->set('is_refunded', self::REFUND_TRANSACTION);
        $this->db->where_in('external_trans_id', $external_trans_id);
        $this->db->update($this->tableName);
    }

    // transaction_id unique record
    public function get_bet_trans_by_round($round_id) {
        $this->db->select('bet_transid');
        $this->db->from($this->tableName);
        $this->db->where('round_id', $round_id);
        $this->db->where('bet_transid != ',0,FALSE);
        $query = $this->db->get();
        $row = $query->row_array();

        $trans_id = null;
        if(!empty($row)) {
            $trans_id = $row['bet_transid'];
        }
        return $trans_id;
    }

    public function syncGameLogs($data) {
        $id=$this->get_transaction_id($data['transaction_id']);
        if (!empty($id)) {
            $this->db->where('transaction_id', $id);
            $this->db->set($data);
            $this->db->update($this->tableName);
        } else {
            $this->db->insert($this->tableName, $data);
        }
    }

    /**
     * FREE ROUND QUERY
     */
    public function getFreeRoundBonusId($bonus_id) {
        $this->db->select('id');
        $this->db->where('bonus_id', $bonus_id);
        $this->db->where('is_bonus_release', self::BONUS_RELEASE);
        $this->db->from('betsoft_free_round_bonus');
        $query = $this->db->get();
        $row = $query->row_array();
        $id = null;
        if (!empty($row)) {
            $id = $row['id'];
        }
        return $id;
    }

    public function getTransactionId($transaction_id) {
        $this->CI->db->select('*');
        $this->CI->db->where('transaction_id', $transaction_id);
        $this->CI->db->where('is_trans_success', self::TRANS_SUCCESS);
        $this->CI->db->from('betsoft_free_round_bonus');
        $query = $this->db->get();
        $row = $query->row_array();
        $id = null;
        if (!empty($row)) {
            $id = $row['id'];
        }
        return $id;
    }

}

///END OF FILE////////////