<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Hogaming_seamless_service_api extends BaseController
{
    public $game_api;

    // Error codes for get balance response.
    const ERR_GETBAL_SUCCESS = 0;
    const ERR_GETBAL_ACCOUNT_NOT_EXIST = 1000;
    const ERR_GETBAL_INVALID_CURRENCY = 1001;
    const ERR_GETBAL_LOCKED_ACCOUNT = 1004;
    const ERR_GETBAL_SYSTEM_ERROR = 9999;

    // Error code list for get balance response.
    public static $get_bal_error_codes = array(
        self::ERR_GETBAL_SUCCESS => 'No Error. Success response.',
        self::ERR_GETBAL_ACCOUNT_NOT_EXIST => 'user account does not exist',
        self::ERR_GETBAL_INVALID_CURRENCY => 'invalid currency',
        self::ERR_GETBAL_LOCKED_ACCOUNT => 'locked account',
        self::ERR_GETBAL_SYSTEM_ERROR => 'system error'
    );

    // Error codes for fund transfer response.
    const ERR_FUNDTRANS_SUCCESS = 0;
    const ERR_FUNDTRANS_ACCOUNT_NOT_EXIST = 1000;
    const ERR_FUNDTRANS_INVALID_CURRENCY = 1001;
    const ERR_FUNDTRANS_INVALID_AMOUNT = 1002;
    const ERR_FUNDTRANS_EXCEEDED_DAILY_WINLOST_LIMIT = 1003;
    const ERR_FUNDTRANS_LOCKED_ACCOUNT = 1004;
    const ERR_FUNDTRANS_INSUFFICIENT_BALANCE = 1005;
    const ERR_FUNDTRANS_EXCEEDED_DAILY_TURNOVER_LIMIT = 1006;
    const ERR_FUNDTRANS_SYSTEM_ERROR = 9999;

    // Error code list for fund transfer response.
    public static $fund_trans_error_codes = array(
        self::ERR_FUNDTRANS_SUCCESS => 'No Error. Success response.',
        self::ERR_FUNDTRANS_ACCOUNT_NOT_EXIST => 'User account does not exist',
        self::ERR_FUNDTRANS_INVALID_CURRENCY => 'Invalid currency',
        self::ERR_FUNDTRANS_INVALID_AMOUNT => 'Invalid amount',
        self::ERR_FUNDTRANS_EXCEEDED_DAILY_WINLOST_LIMIT => 'Exceeded win/loss limit for the day',
        self::ERR_FUNDTRANS_LOCKED_ACCOUNT => 'Locked account',
        self::ERR_FUNDTRANS_INSUFFICIENT_BALANCE => 'Insufficient Balance',
        self::ERR_FUNDTRANS_EXCEEDED_DAILY_TURNOVER_LIMIT => 'Exceeded the turnover limit for the day',
        self::ERR_FUNDTRANS_SYSTEM_ERROR => 'System error'
    );

    const FUNDTRANS_PLACE_BET_REQUEST = 500;
    const FUNDTRANS_CANCEL_BET_REQUEST = 501;
    const FUNDTRANS_PLAYER_WIN_REQUEST = 510;
    const FUNDTRANS_PLAYER_LOSE_REQUEST = 520;
    const FUNDTRANS_PLACE_BET_CANCELLATION_REQUEST = 502;

    public static $fund_trans_requests = array(
        self::FUNDTRANS_PLACE_BET_REQUEST => 'Place Bet Request',
        self::FUNDTRANS_CANCEL_BET_REQUEST => 'Cancel Bet Request',
        self::FUNDTRANS_PLAYER_WIN_REQUEST => 'Player Win Request',
        self::FUNDTRANS_PLAYER_LOSE_REQUEST => 'Player Lose Request',
        self::FUNDTRANS_PLACE_BET_CANCELLATION_REQUEST => 'Place Bet Cancellation Request'
    );

    public static $fund_trans_categories = array(
        self::FUNDTRANS_PLACE_BET_REQUEST => 'placebet',
        self::FUNDTRANS_CANCEL_BET_REQUEST => 'cancelbet',
        self::FUNDTRANS_PLAYER_WIN_REQUEST => 'playerwin',
        self::FUNDTRANS_PLAYER_LOSE_REQUEST => 'playerlose',
        self::FUNDTRANS_PLACE_BET_CANCELLATION_REQUEST => 'placebetcancel'
    );

    public static $fund_trans_operators = array(
        self::FUNDTRANS_PLACE_BET_REQUEST => '-',
        self::FUNDTRANS_CANCEL_BET_REQUEST => '+',
        self::FUNDTRANS_PLAYER_WIN_REQUEST => '+',
        self::FUNDTRANS_PLAYER_LOSE_REQUEST => null,
        self::FUNDTRANS_PLACE_BET_CANCELLATION_REQUEST => null
    );

    const FUNDTRANS_REFUND_OPERATOR = '+';

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('wallet_model','game_provider_auth','common_token','original_game_logs_model','player_model'));
        $this->game_platform_id = HOGAMING_SEAMLESS_API;
        $this->game_api = $this->utils->loadExternalSystemLibObject($this->game_platform_id);
        $game_api = $this->game_api;
        $this->currency = $this->game_api->getSystemInfo('currency', $game_api::CURRENCY_THB);
    }

    private function _xmlToArray($xml_string)
    {
        $xml = simplexml_load_string($xml_string);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        return $array;
    }

    private function _xmlToJson($xml_string)
    {
        $xml = simplexml_load_string($xml_string);
        $json = json_encode($xml);

        return $json;
    }

    private function _convertToDecimalWithoutComma($amount)
    {
        $balance = number_format($amount, 2, '.', '');

        return $balance;
    }

    private function _getBalanceResponse($is_success, $game_username, $currency, $error_code, $amount = null)
    {
        if ($is_success) {
            echo "<cw type='getBalanceResp' uname ='{$game_username}' cur='{$currency}' amt='{$amount}' err='{$error_code}' />";
        } else {
            echo "<cw type='getBalanceResp' uname ='{$game_username}' cur='{$currency}' err='{$error_code}' />";
        }

        return true;
    }

    public function getBalance()
    {
        $game_api = $this->game_api;
        $is_response_success = false;
        $request_xml_string = file_get_contents('php://input');
        $request_array = $this->_xmlToArray($request_xml_string);
        $request_json = $this->_xmlToJson($request_xml_string);

        $this->utils->debug_log("HOGAMING_SEAMLESS_API GET BALANCE REQUEST ============================>", $request_xml_string);

        if (isset($request_array['@attributes']['type']) && $request_array['@attributes']['type'] == 'getBalanceReq') {
            if (isset($request_array['@attributes']['uname'])) {
                $game_username = $request_array['@attributes']['uname'];
                $player_name = $game_api->getPlayerUsernameByGameUsername($game_username);

                if (empty($player_name)) {
                    $this->utils->debug_log("============================= HOGAMING_SEAMLESS_API GET BALANCE CANNOT FIND PLAYER NAME =============================");

                    $error_code = self::ERR_GETBAL_ACCOUNT_NOT_EXIST;
                    $game_api->saveResponseResult($is_response_success, $game_api::API_NAME, $request_json, self::$get_bal_error_codes[$error_code], $error_code);

                    // trigger_error("Cannot find player name.");
                    
                    $this->_getBalanceResponse($is_response_success, $game_username, $this->currency, $error_code);
                } else {
                    $is_response_success = true;
                    $current_balance = $game_api->queryPlayerBalance($player_name)['balance'];
                    $formatted_balance = $this->_convertToDecimalWithoutComma($current_balance);
                    $error_code = self::ERR_GETBAL_SUCCESS;

                    $this->utils->debug_log("HOGAMING_SEAMLESS_API GET BALANCE RESPONSE ====================>", 'current_balance', $current_balance, 'formatted_balance', $formatted_balance, 'error', self::$get_bal_error_codes[$error_code]);

                    // Get balance response success.
                    $this->_getBalanceResponse($is_response_success, $game_username, $this->currency, $error_code, $formatted_balance);
                }
            } else {
                $error_code = self::ERR_GETBAL_SYSTEM_ERROR;
                $this->utils->debug_log("============================= HOGAMING_SEAMLESS_API GET BALANCE UNDEFINED INDEX 'uname' =============================");
                // trigger_error("Undefined index 'uname' via get balance request.");

                $this->_getBalanceResponse($is_response_success, null, $this->currency, $error_code);
            }
        } else {
            $error_code = self::ERR_GETBAL_SYSTEM_ERROR;
            // Get balance request failed.
            // trigger_error("Get balance request failed.");

            $this->_getBalanceResponse($is_response_success, null, $this->currency, $error_code);
        }
    }

    private function _fundTransferResponse($is_success, $game_username, $currency, $error_code, $amount = null)
    {
        if ($is_success) {
            echo "<cw type='fundTransferResp' uname ='{$game_username}' cur='{$currency}' amt='{$amount}' err='{$error_code}' />";
        } else {
            echo "<cw type='fundTransferResp' uname ='{$game_username}' cur='{$currency}' err='{$error_code}' />";
        }

        return true;
    }

    private function _addAmount($playerId, $amount)
    {
        $lockedKey = null;
        $lock_it = $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
        $this->utils->debug_log('lock add_amount', 'id', $playerId, $lock_it);
        $success = false;

        if ($lock_it) {
            try {
                // $reqParams = ['playerId' => $playerId, 'amount' => $amount, 'gamePlatformId' => $this->gamePlatformId];
                // $response = 'Booming add wallet process';
                $this->startTrans();
                $this->wallet_model->incSubWallet($playerId, $this->game_platform_id, $amount);
                // $this->game_api_booming->saveToResponseResult(BOOMINGSEAMLESS_GAME_LOGS::PROCESS_SUCCESS, BOOMINGSEAMLESS_GAME_LOGS::WALLET_ADD_METHOD, $reqParams, $response);
                $this->endTransWithSucc();
            } finally {
                $this->utils->releaseResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
                $this->utils->debug_log('release add_amount lock', 'id', $playerId);
            }
        }
    }

    private function _subtractAmount($playerId, $amount)
    {
        $lockedKey = null;
        $lock_it = $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
        $this->utils->debug_log('lock subtract_amount', 'id', $playerId, $lock_it);

        if ($lock_it) {
            try {
                // $reqParams = ['playerId' => $playerId, 'amount' => $amount, 'gamePlatformId' => $this->gamePlatformId];
                // $response = 'Booming deduct wallet process';
                $this->startTrans();
                $this->wallet_model->decSubWallet($playerId, $this->game_platform_id, $amount);
                // $this->game_api_booming->saveToResponseResult(BOOMINGSEAMLESS_GAME_LOGS::PROCESS_SUCCESS, BOOMINGSEAMLESS_GAME_LOGS::WALLET_SUBTRACT_METHOD, $reqParams, $response);
                $this->endTransWithSucc();
            } finally {
                $this->utils->releaseResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
                $this->utils->debug_log('release subtract_amount lock', 'id', $playerId);
            }
        }
    }

    private function _checkProcessedPlaceBetTransactionId($txnid)
    {
        $game_api = $this->game_api;
        $table = $game_api::TRANSACTION_LOGS_TABLE;

        $sql = <<<EOD
            SELECT id
            FROM {$table}
            WHERE txnid = ?
EOD;

        $params = [
            $txnid
        ];

        $queryResult = $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);

        if (!empty($queryResult)) {
            return true;
        }

        return false;
    }

    private function _isProcessedTransaction($txnid)
    {
        $game_api = $this->game_api;
        $table = $game_api::TRANSACTION_LOGS_TABLE;

        $sql = <<<EOD
            SELECT id
            FROM {$table}
            WHERE txnid = ?
EOD;

        $params = [
            $txnid
        ];

        $queryResult = $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);

        if (!empty($queryResult)) {
            return true;
        }

        return false;
    }

    private function _insertIntoOriginalGameLogs($data, $response_result_id)
    {
        $game_api = $this->game_api;
        $data = $data['@attributes'];
        
        $original_data = array(
            'uname' => isset($data['uname']) ? $data['uname'] : null,
            'cur' => isset($data['cur']) ? $data['cur'] : null,
            'amt' => isset($data['amt']) ? $data['amt'] : null,
            'txnid' => isset($data['txnid']) ? $data['txnid'] : null,
            'gametypeid' => isset($data['gametypeid']) ? $data['gametypeid'] : null,
            'txnsubtypeid' => isset($data['txnsubtypeid']) ? $data['txnsubtypeid'] : null,
            'gameid' => isset($data['gameid']) ? $data['gameid'] : null,
            'bAmt' => isset($data['bAmt']) ? $data['bAmt'] : null,
            'txn_reverse_id' => isset($data['txn_reverse_id']) ? $data['txn_reverse_id'] : null,
            'category' => isset($data['category']) ? $data['category'] : null,
            'operator' => isset($data['operator']) ? $data['operator'] : null,
            'response_result_id' => isset($response_result_id) ? $response_result_id : null,
            'external_uniqueid' => isset($data['txnid']) ? $data['txnid'] . '-' . time() : null
        );

        $game_api->doSyncOriginal(array($original_data), $game_api::ORIGINAL_GAMELOGS_TABLE);
    }

    private function _insertIntoTransactionLogs($data, $response_result_id, $before_balance = null, $after_balance = null)
    {
        $game_api = $this->game_api;
        $data = $data['@attributes'];
        
        $transaction_data = array(
            'uname' => isset($data['uname']) ? $data['uname'] : null,
            'cur' => isset($data['cur']) ? $data['cur'] : null,
            'amt' => isset($data['amt']) ? $data['amt'] : null,
            'txnid' => isset($data['txnid']) ? $data['txnid'] : null,
            'gametypeid' => isset($data['gametypeid']) ? $data['gametypeid'] : null,
            'txnsubtypeid' => isset($data['txnsubtypeid']) ? $data['txnsubtypeid'] : null,
            'gameid' => isset($data['gameid']) ? $data['gameid'] : null,
            'bAmt' => isset($data['bAmt']) ? $data['bAmt'] : null,
            'txn_reverse_id' => isset($data['txn_reverse_id']) ? $data['txn_reverse_id'] : null,
            'category' => isset($data['category']) ? $data['category'] : null,
            'operator' => isset($data['operator']) ? $data['operator'] : null,
            'response_result_id' => isset($response_result_id) ? $response_result_id : null,
            'external_uniqueid' => isset($data['txnid']) ? $data['txnid'] . '-' . time() : null,
            'provider_id' => $game_api->getPlatformCode(),
            'before_balance' => isset($before_balance) ? $before_balance : null,
            'after_balance' => isset($after_balance) ? $after_balance : null,
            'sessionid' => isset($data['sessionid']) ? $data['sessionid'] : null
        );

        $game_api->doSyncTransaction(array($transaction_data), $game_api::TRANSACTION_LOGS_TABLE);
    }

    private function _addCategoryAndOperatorField($data, $fund_transfer_type, $is_refund = null)
    {
        if (isset($data['@attributes'])) {
            $data['@attributes']['category'] = self::$fund_trans_categories[$fund_transfer_type];
            $data['@attributes']['operator'] = self::$fund_trans_operators[$fund_transfer_type];

            if ($is_refund) {
                $data['@attributes']['operator'] = self::FUNDTRANS_REFUND_OPERATOR;
            }
        }

        return $data;
    }

    private function _processFundTransfer($request_type, $is_response_success, $deducted_amount, $amount, $current_balance, $player_id, $player_name, $game_username, $error_code, $data = null)
    {
        $game_api = $this->game_api;
        $is_processed_transaction = false;

        if (isset($data['@attributes']['txnid'])) {
            $is_processed_transaction = $this->_isProcessedTransaction($data['@attributes']['txnid']);
        }

        switch ($request_type) {
            case self::FUNDTRANS_PLACE_BET_REQUEST:
                if ($deducted_amount > 0) {
                    if ($amount <= $current_balance) {
                        $data = $this->_addCategoryAndOperatorField($data, self::FUNDTRANS_PLACE_BET_REQUEST);
                        $response_result_id = $game_api->saveResponseResult($is_response_success, __FUNCTION__, $data, self::$get_bal_error_codes[$error_code], $error_code);

                        if (!$is_processed_transaction) {
                            $this->startTrans();
                            $before_balance = $game_api->queryPlayerBalance($player_name)['balance'];

                            if ($before_balance == $current_balance) {
                                $this->_subtractAmount($player_id, $amount);
                                $after_balance = $game_api->queryPlayerBalance($player_name)['balance'];
                                $this->_insertIntoTransactionLogs($data, $response_result_id, $before_balance, $after_balance);
                            } else {
                                $is_response_success = false;
                                $error_code = self::ERR_FUNDTRANS_SYSTEM_ERROR;

                                $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_requests[self::FUNDTRANS_PLACE_BET_REQUEST], 'error', self::$fund_trans_error_codes[$error_code]);

                                $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code);
                            }
                            $this->endTransWithSucc();
                        }

                        $current_balance = $game_api->queryPlayerBalance($player_name)['balance'];
                        $formatted_balance = $this->_convertToDecimalWithoutComma($current_balance);

                        $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_requests[self::FUNDTRANS_PLACE_BET_REQUEST], 'error', self::$fund_trans_error_codes[$error_code], 'SUBTRACTED AMOUNT', 'player_id', $player_id, 'amount', $amount, 'current_balance', $current_balance);

                        $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code, $formatted_balance);
                    } else {
                        $is_response_success = false;
                        $error_code = self::ERR_FUNDTRANS_INSUFFICIENT_BALANCE;

                        $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_requests[self::FUNDTRANS_PLACE_BET_REQUEST], 'error', self::$fund_trans_error_codes[$error_code]);

                        $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code);
                    }
                } else {
                    $is_response_success = false;
                    $error_code = self::ERR_FUNDTRANS_INVALID_AMOUNT;

                    $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_requests[self::FUNDTRANS_PLACE_BET_REQUEST], 'error', self::$fund_trans_error_codes[$error_code]);

                    $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code);
                }
                break;
            case self::FUNDTRANS_CANCEL_BET_REQUEST:
                if ($deducted_amount > 0) {
                    $data = $this->_addCategoryAndOperatorField($data, self::FUNDTRANS_CANCEL_BET_REQUEST);
                    $response_result_id = $game_api->saveResponseResult($is_response_success, __FUNCTION__, $data, self::$get_bal_error_codes[$error_code], $error_code);

                    if (!$is_processed_transaction) {
                        $this->startTrans();
                        $before_balance = $game_api->queryPlayerBalance($player_name)['balance'];

                        if ($before_balance == $current_balance) {
                            $this->_addAmount($player_id, $amount);
                            $after_balance = $game_api->queryPlayerBalance($player_name)['balance'];
                            $this->_insertIntoTransactionLogs($data, $response_result_id, $before_balance, $after_balance);
                        } else {
                            $is_response_success = false;
                            $error_code = self::ERR_FUNDTRANS_SYSTEM_ERROR;

                            $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_requests[self::FUNDTRANS_PLACE_BET_REQUEST], 'error', self::$fund_trans_error_codes[$error_code]);

                            $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code);
                        }
                        $this->endTransWithSucc();
                    }

                    $current_balance = $game_api->queryPlayerBalance($player_name)['balance'];
                    $formatted_balance = $this->_convertToDecimalWithoutComma($current_balance);

                    $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_requests[self::FUNDTRANS_CANCEL_BET_REQUEST], 'error', self::$fund_trans_error_codes[$error_code], 'ADDED AMOUNT', 'player_id', $player_id, 'amount', $amount, 'current_balance', $current_balance);

                    $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code, $formatted_balance);
                } else {
                    $is_response_success = false;
                    $error_code = self::ERR_FUNDTRANS_INVALID_AMOUNT;

                    $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_requests[self::FUNDTRANS_CANCEL_BET_REQUEST], 'error', self::$fund_trans_error_codes[$error_code]);

                    $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code);
                }
                break;
            case self::FUNDTRANS_PLAYER_WIN_REQUEST:
                if ($deducted_amount > 0) {
                    $data = $this->_addCategoryAndOperatorField($data, self::FUNDTRANS_PLAYER_WIN_REQUEST);
                    $response_result_id = $game_api->saveResponseResult($is_response_success, __FUNCTION__, $data, self::$get_bal_error_codes[$error_code], $error_code);

                    if (!$is_processed_transaction) {
                        $this->startTrans();
                        $before_balance = $game_api->queryPlayerBalance($player_name)['balance'];

                        if ($before_balance == $current_balance) {
                            $this->_addAmount($player_id, $amount);
                            $after_balance = $game_api->queryPlayerBalance($player_name)['balance'];
                            $this->_insertIntoTransactionLogs($data, $response_result_id, $before_balance, $after_balance);
                        } else {
                            $is_response_success = false;
                            $error_code = self::ERR_FUNDTRANS_SYSTEM_ERROR;

                            $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_requests[self::FUNDTRANS_PLACE_BET_REQUEST], 'error', self::$fund_trans_error_codes[$error_code]);

                            $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code);
                        }
                        $this->endTransWithSucc();
                    }

                    $current_balance = $game_api->queryPlayerBalance($player_name)['balance'];
                    $formatted_balance = $this->_convertToDecimalWithoutComma($current_balance);

                    $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_requests[self::FUNDTRANS_PLAYER_WIN_REQUEST], 'error', self::$fund_trans_error_codes[$error_code], 'ADDED AMOUNT', 'player_id', $player_id, 'amount', $amount, 'current_balance', $current_balance);

                    $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code, $formatted_balance);
                } else {
                    $is_response_success = false;
                    $error_code = self::ERR_FUNDTRANS_INVALID_AMOUNT;

                    $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_requests[self::FUNDTRANS_PLAYER_WIN_REQUEST], 'error', self::$fund_trans_error_codes[$error_code]);

                    $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code);
                }
                break;
            case self::FUNDTRANS_PLAYER_LOSE_REQUEST:
                $data = $this->_addCategoryAndOperatorField($data, self::FUNDTRANS_PLAYER_LOSE_REQUEST);
                $response_result_id = $game_api->saveResponseResult($is_response_success, __FUNCTION__, $data, self::$get_bal_error_codes[$error_code], $error_code);

                $this->startTrans();
                $current_balance = $game_api->queryPlayerBalance($player_name)['balance'];
                $formatted_balance = $this->_convertToDecimalWithoutComma($current_balance);
                $this->_insertIntoTransactionLogs($data, $response_result_id, $current_balance, $current_balance);
                $this->endTransWithSucc();

                $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_requests[self::FUNDTRANS_PLAYER_LOSE_REQUEST], 'error', self::$fund_trans_error_codes[$error_code], 'current_balance', $current_balance);

                $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code, $formatted_balance);
                break;
            case self::FUNDTRANS_PLACE_BET_CANCELLATION_REQUEST:
                $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_requests[self::FUNDTRANS_PLACE_BET_CANCELLATION_REQUEST], 'error', self::$fund_trans_error_codes[$error_code]);

                $is_refund = false;

                if (isset($data['@attributes']['txn_reverse_id'])) {
                    $is_refund = $this->_checkProcessedPlaceBetTransactionId($data['@attributes']['txn_reverse_id']);
                }

                if ($is_refund) {
                    if ($deducted_amount > 0) {
                        $data = $this->_addCategoryAndOperatorField($data, self::FUNDTRANS_PLACE_BET_CANCELLATION_REQUEST, $is_refund);
                        $response_result_id = $game_api->saveResponseResult($is_response_success, __FUNCTION__, $data, self::$get_bal_error_codes[$error_code], $error_code);

                        if (!$is_processed_transaction) {
                            $this->startTrans();
                            $before_balance = $game_api->queryPlayerBalance($player_name)['balance'];

                            if ($before_balance == $current_balance) {
                                $this->_addAmount($player_id, $amount);
                                $after_balance = $game_api->queryPlayerBalance($player_name)['balance'];
                                $this->_insertIntoTransactionLogs($data, $response_result_id, $before_balance, $after_balance);
                            } else {
                                $is_response_success = false;
                                $error_code = self::ERR_FUNDTRANS_SYSTEM_ERROR;

                                $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_requests[self::FUNDTRANS_PLACE_BET_REQUEST], 'error', self::$fund_trans_error_codes[$error_code]);

                                $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code);
                            }
                            $this->endTransWithSucc();
                        }

                        $current_balance = $game_api->queryPlayerBalance($player_name)['balance'];
                        $formatted_balance = $this->_convertToDecimalWithoutComma($current_balance);

                        $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code, $formatted_balance);
                    } else {
                        $is_response_success = false;
                        $error_code = self::ERR_FUNDTRANS_INVALID_AMOUNT;

                        $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code);
                    }
                } else {
                    $data = $this->_addCategoryAndOperatorField($data, self::FUNDTRANS_PLACE_BET_CANCELLATION_REQUEST);
                    $response_result_id = $game_api->saveResponseResult($is_response_success, __FUNCTION__, $data, self::$get_bal_error_codes[$error_code], $error_code);

                    $this->startTrans();
                    $current_balance = $game_api->queryPlayerBalance($player_name)['balance'];
                    $formatted_balance = $this->_convertToDecimalWithoutComma($current_balance);
                    $this->_insertIntoTransactionLogs($data, $response_result_id, $current_balance, $current_balance);
                    $this->endTransWithSucc();

                    $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code, $formatted_balance);
                }
                break;
            default:
                $this->utils->debug_log('HOGAMING_SEAMLESS_API', 'request', self::$fund_trans_error_codes[$error_code], 'error', self::$fund_trans_error_codes[$error_code]);

                $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code);
                break;
        }
    }

    public function fundTransfer()
    {
        $game_api = $this->game_api;
        $is_response_success = false;
        $request_xml_string = file_get_contents('php://input');
        $request_array = $this->_xmlToArray($request_xml_string);
        $request_json = $this->_xmlToJson($request_xml_string);

        $this->utils->debug_log("HOGAMING_SEAMLESS_API FUND TRANSFER REQUEST ============================>", $request_xml_string);

        if (isset($request_array['@attributes']['type']) && $request_array['@attributes']['type'] == 'fundTransferReq') {
            if (isset($request_array['@attributes']['txnsubtypeid'])) {
                if (isset($request_array['@attributes']['uname'])) {
                    $game_username = $request_array['@attributes']['uname'];
                    $player_name = $game_api->getPlayerUsernameByGameUsername($game_username);
                    $player_id = $game_api->getPlayerIdFromUsername($player_name);

                    if (empty($player_name)) {
                        $this->utils->debug_log("============================= HOGAMING_SEAMLESS_API FUND TRANSFER CANNOT FIND PLAYER NAME =============================");

                        $error_code = self::ERR_FUNDTRANS_ACCOUNT_NOT_EXIST;
                        $game_api->saveResponseResult($is_response_success, $game_api::API_NAME, $request_json, self::$fund_trans_error_codes[$error_code], $error_code);

                        // trigger_error("Cannot find player name.");
                        
                        $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code);
                    } else {
                        $is_response_success = true;
                        $current_balance = $game_api->queryPlayerBalance($player_name)['balance'];
                        $error_code = self::ERR_FUNDTRANS_SUCCESS;

                        if (isset($request_array['@attributes']['amt']) && isset($request_array['@attributes']['bAmt'])) {
                            $amount = empty($request_array['@attributes']['amt']) ? 0 : $request_array['@attributes']['amt'];
                            $bonus_amount = empty($request_array['@attributes']['bAmt']) ? 0 : $request_array['@attributes']['bAmt'];
                            $deducted_amount = $amount - $bonus_amount;
                        } else {
                            $is_response_success = false;
                            $error_code = self::ERR_FUNDTRANS_INVALID_AMOUNT;

                            $this->_fundTransferResponse($is_response_success, $game_username, $this->currency, $error_code);
                        }

                        $this->utils->debug_log("HOGAMING_SEAMLESS_API FUND TRANSFER RESPONSE ====================>", 'current_balance', $current_balance, 'error', self::$fund_trans_error_codes[$error_code]);

                        switch ($request_array['@attributes']['txnsubtypeid']) {
                            case self::FUNDTRANS_PLACE_BET_REQUEST:
                                $this->_processFundTransfer(self::FUNDTRANS_PLACE_BET_REQUEST, $is_response_success, $deducted_amount, $amount, $current_balance, $player_id, $player_name, $game_username, $error_code, $request_array);
                                break;
                            case self::FUNDTRANS_CANCEL_BET_REQUEST:
                                $this->_processFundTransfer(self::FUNDTRANS_CANCEL_BET_REQUEST, $is_response_success, $deducted_amount, $amount, $current_balance, $player_id, $player_name, $game_username, $error_code, $request_array);
                                break;
                            case self::FUNDTRANS_PLAYER_WIN_REQUEST:
                                $this->_processFundTransfer(self::FUNDTRANS_PLAYER_WIN_REQUEST, $is_response_success, $deducted_amount, $amount, $current_balance, $player_id, $player_name, $game_username, $error_code, $request_array);
                                break;
                            case self::FUNDTRANS_PLAYER_LOSE_REQUEST:
                                $this->_processFundTransfer(self::FUNDTRANS_PLAYER_LOSE_REQUEST, $is_response_success, $deducted_amount, $amount, $current_balance, $player_id, $player_name, $game_username, $error_code, $request_array);
                                break;
                            case self::FUNDTRANS_PLACE_BET_CANCELLATION_REQUEST:
                                $this->_processFundTransfer(self::FUNDTRANS_PLACE_BET_CANCELLATION_REQUEST, $is_response_success, $deducted_amount, $amount, $current_balance, $player_id, $player_name, $game_username, $error_code, $request_array);
                                break;
                            default:
                                $is_response_success = false;
                                $error_code = self::ERR_FUNDTRANS_SYSTEM_ERROR;

                                $this->_processFundTransfer(self::ERR_FUNDTRANS_SYSTEM_ERROR, $is_response_success, $deducted_amount, $amount, $current_balance, $player_id, $player_name, $game_username, $error_code);
                                break;
                        }
                    }
                } else {
                    $error_code = self::ERR_FUNDTRANS_SYSTEM_ERROR;
                    $this->utils->debug_log("============================= HOGAMING_SEAMLESS_API FUND TRANSFER UNDEFINED INDEX 'uname' =============================");
                    // trigger_error("Undefined index 'uname' via fund transfer request.");

                    $this->_fundTransferResponse($is_response_success, null, $this->currency, $error_code);
                }
            } else {
                $error_code = self::ERR_FUNDTRANS_SYSTEM_ERROR;
                $this->utils->debug_log("============================= HOGAMING_SEAMLESS_API UNDEFINED INDEX 'txnsubtypeid' =============================");
                // trigger_error("Undefined index 'txnsubtypeid' via fund transfer request.");

                $this->_fundTransferResponse($is_response_success, null, $this->currency, $error_code);
            }
        } else {
            $error_code = self::ERR_FUNDTRANS_SYSTEM_ERROR;
            // Get balance request failed.
            // trigger_error("Fund transfer request failed.");

            $this->_fundTransferResponse($is_response_success, null, $this->currency, $error_code);
        }
    }
}
