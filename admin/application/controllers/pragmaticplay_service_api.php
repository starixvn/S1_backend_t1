<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Pragmaticplay_service_api extends BaseController {

    const RETURN_OK = [
        'error' => 0,
        'description' => 'Success'
    ];

    const ERROR_BET_NOT_FOUND = [
        'error' => 0,
        'description' => 'Bet transaction not found'
    ];

    const ERROR_INSUFFICIENT_BALANCE = [
        'error' => 1,
        'description' => 'Insufficient balance'
    ];

    const ERROR_NOT_FOUND_PLAYER = [
        'error' => 2,
        'description' => 'Player not found or is logged out'
    ];

    const ERROR_BET_NOT_ALLOWED = [
        'error' => 3,
        'description' => 'Bet is not allowed'
    ];

    const ERROR_AUTH_FAILED = [
        'error' => 4,
        'description' => 'Player authentication failed due to invalid, not found or expired token'
    ];

    const ERROR_INVALID_HASH = [
        'error' => 5,
        'description' => 'Invalid hash code'
    ];
    const ERROR_PLAYER_BANNED = [
        'error' => 6,
        'description' => 'Player is frozen'
    ];

    const ERROR_BAD_REQUEST = [
        'error' => 7,
        'description' => 'Bad parameters in the request, please check post parameters'
    ];

    const TRANSACTION_ROLLBACK = 'rollback';
    const TRANSACTION_CANCELLED = 'cancel';

    const TRANSACTION_CREDIT = 'credit';
    const TRANSACTION_BONUS_NO_CREDIT = 'bonus-no-credit';
    const TRANSACTION_DEBIT = 'debit';

    private $requestParams;
    private $api;
    public function __construct() {

        parent::__construct();

        $this->api = $this->utils->loadExternalSystemLibObject(PRAGMATICPLAY_SEAMLESS_THB1_API);
        $this->CI->load->model('pragmaticplay_seamless_wallet_transactions', 'pp_transactions');
        $this->requestParams = new stdClass();
    }

    public function authenticate() {
        $rule_set = [
            'hash' => 'required',
            'providerId' => 'required',
            'token' => 'required',
        ];
        $this->preProcessRequest(__FUNCTION__, $rule_set);
        $player_info = $this->api->getPlayerInfoByToken($this->requestParams->params['token']);
        if($player_info) {
            $data = [
                'userId' => $this->api->getGameUsernameByPlayerUsername($player_info['username']),
                'currency' => $this->api->currency,
                'cash' => $this->api->queryPlayerBalance($player_info['username'])['balance'],
                'bonus' => 0
            ];
            return $this->setResponse(self::RETURN_OK, $data);
        }
        return $this->setResponse(self::ERROR_NOT_FOUND_PLAYER);
    }

    public function authenticate2() {
        $rule_set = [
            'providerId' => 'required',
            'hash' => 'required',
            'username' => 'required',
            'password' => 'required',
        ];
        $this->preProcessRequest(__FUNCTION__, $rule_set);
        $player_info = (array) $this->api->getPlayerInfoByUsername($this->requestParams->params['username']);
        if($player_info) {
            $password = $password = $this->api->getPassword($player_info['username'])['password'];
            if($password == $this->requestParams->params['password']) {
                $data = [
                    'userId' => $this->api->getGameUsernameByPlayerUsername($player_info['username']),
                    'currency' => $this->api->currency,
                    'cash' => $this->api->queryPlayerBalance($player_info['username'])['balance'],
                    'bonus' => 0
                ];
                return $this->setResponse(self::RETURN_OK, $data);
            }
        }
        return $this->setResponse(self::ERROR_AUTH_FAILED);
    }

    public function balance() {
        $rule_set = [
            'hash' => 'required',
            'providerId' => 'required',
            'userId' => 'required',
        ];
        $this->preProcessRequest(__FUNCTION__, $rule_set);
        $user_name = $this->api->getPlayerUsernameByGameUsername($this->requestParams->params['userId']);
        $player_info = (array) $this->api->getPlayerInfoByUsername($user_name);
        if($player_info) {
            $data = [
                'currency' => $this->api->currency,
                'cash' => $this->api->queryPlayerBalance($player_info['username'])['balance'],
                'bonus' => 0
            ];
            return $this->setResponse(self::RETURN_OK, $data);
        }
        return $this->setResponse(self::ERROR_NOT_FOUND_PLAYER);
    }

    public function bet() {
        $rule_set = [
            'hash' => 'required',
            'userId' => 'required',
            'gameId' => 'required',
            'roundId' => 'required',
            'amount' => 'required',
            'reference' => 'required',
            'providerId' => 'required',
            'timestamp' => 'required',
            'roundDetails' => 'required',
        ];

        $this->preProcessRequest(__FUNCTION__, $rule_set);
        $user_name = $this->api->getPlayerUsernameByGameUsername($this->requestParams->params['userId']);
        $player_info = (array) $this->api->getPlayerInfoByUsername($user_name);
        if($player_info) {

            $controller = $this;
            $transaction_data = [
                'code' => self::RETURN_OK
            ];
            $this->lockAndTransForPlayerBalance($player_info['playerId'], function() use($controller, $player_info, &$transaction_data) {
                if($old_transaction = $this->pp_transactions->searchByExternalTransactionIdByTransactionType($controller->requestParams->params['reference'])) {
                    $transaction_data['code'] = self::RETURN_OK;
                    $transaction_data['transaction_id'] = $old_transaction[0]['external_uniqueid'];
                    $transaction_data['after_balance'] = $this->api->queryPlayerBalance($player_info['username'])['balance'];
                    return true;
                }
                if($controller->requestParams->params['amount'] < 0) {
                    $transaction_data['code'] = self::ERROR_BAD_REQUEST;
                    return false;
                }
                $transaction_data = $controller->adjustWallet(self::TRANSACTION_DEBIT, $player_info);
                return true;
            });

            if($transaction_data['code'] != self::RETURN_OK) {
                return $this->setResponse($transaction_data['code']);
            }
            $data = [
                'transactionId' => $transaction_data['transaction_id'],
                'currency' => $this->api->currency,
                'cash' => $transaction_data['after_balance'],
                'bonus' => 0,
                'usedPromo' => 0
            ];
            return $this->setResponse($transaction_data['code'], $data);
        }
        return $this->setResponse(self::ERROR_NOT_FOUND_PLAYER);
    }

    public function result() {
        $rule_set = [
            'hash' => 'required',
            'userId' => 'required',
            'gameId' => 'required',
            'roundId' => 'required',
            'amount' => 'required',
            'reference' => 'required',
            'providerId' => 'required',
            'timestamp' => 'required',
            'roundDetails' => 'required',
        ];

        $this->preProcessRequest(__FUNCTION__, $rule_set);
        $user_name = $this->api->getPlayerUsernameByGameUsername($this->requestParams->params['userId']);
        $player_info = (array) $this->api->getPlayerInfoByUsername($user_name);
        if($player_info) {

            $controller = $this;
            $transaction_data = [
                'code' => self::RETURN_OK
            ];
            $this->lockAndTransForPlayerBalance($player_info['playerId'], function() use($controller, $player_info, &$transaction_data) {
                if($old_transaction = $this->pp_transactions->searchByExternalTransactionIdByTransactionType($controller->requestParams->params['reference'])) {
                    $transaction_data['code'] = self::RETURN_OK;
                    $transaction_data['transaction_id'] = $old_transaction[0]['external_uniqueid'];
                    $transaction_data['after_balance'] = $this->api->queryPlayerBalance($player_info['username'])['balance'];
                    return true;
                }
                if($controller->requestParams->params['amount'] < 0) {
                    $transaction_data['code'] = self::ERROR_BAD_REQUEST;
                    return false;
                }
                $transaction_data = $controller->adjustWallet(self::TRANSACTION_CREDIT, $player_info);
                return true;
            });

            if($transaction_data['code'] != self::RETURN_OK) {
                return $this->setResponse($transaction_data['code']);
            }
            $data = [
                'transactionId' => $transaction_data['transaction_id'],
                'currency' => $this->api->currency,
                'cash' => $transaction_data['after_balance'],
                'bonus' => 0,
            ];
            return $this->setResponse($transaction_data['code'], $data);
        }
        return $this->setResponse(self::ERROR_NOT_FOUND_PLAYER);
    }

    public function promoWin() {
        $rule_set = [
            'hash' => 'required',
            'providerId' => 'required',
            'timestamp' => 'required',
            'userId' => 'required',
            'campaignId' => 'required',
            'campaignType' => 'required',
            'amount' => 'required',
            'currency' => 'required',
            'reference' => 'required',
        ];

        $this->preProcessRequest(__FUNCTION__, $rule_set);
        $user_name = $this->api->getPlayerUsernameByGameUsername($this->requestParams->params['userId']);
        $player_info = (array) $this->api->getPlayerInfoByUsername($user_name);
        if($player_info) {

            $controller = $this;
            $transaction_data = [
                'code' => self::RETURN_OK
            ];
            $this->lockAndTransForPlayerBalance($player_info['playerId'], function() use($controller, $player_info, &$transaction_data) {
                if($old_transaction = $this->pp_transactions->searchByExternalTransactionIdByTransactionType($controller->requestParams->params['reference'])) {
                    $transaction_data['code'] = self::RETURN_OK;
                    $transaction_data['transaction_id'] = $old_transaction[0]['external_uniqueid'];
                    $transaction_data['after_balance'] = $this->api->queryPlayerBalance($player_info['username'])['balance'];
                    return true;
                }
                if($controller->requestParams->params['amount'] < 0) {
                    $transaction_data['code'] = self::ERROR_BAD_REQUEST;
                    return false;
                }
                $transaction_data = $controller->adjustWallet(self::TRANSACTION_CREDIT, $player_info);
                return true;
            });

            if($transaction_data['code'] != self::RETURN_OK) {
                return $this->setResponse($transaction_data['code']);
            }
            $data = [
                'transactionId' => $transaction_data['transaction_id'],
                'currency' => $this->api->currency,
                'cash' => $transaction_data['after_balance'],
                'bonus' => 0,
            ];
            return $this->setResponse($transaction_data['code'], $data);
        }
        return $this->setResponse(self::ERROR_NOT_FOUND_PLAYER);
    }

    public function bonusWin() {
        $rule_set = [
            'hash' => 'required',
            'userId' => 'required',
            'amount' => 'required',
            'reference' => 'required',
            'providerId' => 'required',
            'timestamp' => 'required',
            'bonusCode' => 'required',
            'roundId' => 'required',
            'gameId' => 'required',
        ];

        $this->preProcessRequest(__FUNCTION__, $rule_set);
        $user_name = $this->api->getPlayerUsernameByGameUsername($this->requestParams->params['userId']);
        $player_info = (array) $this->api->getPlayerInfoByUsername($user_name);
        if($player_info) {

            $controller = $this;
            $transaction_data = [
                'code' => self::RETURN_OK
            ];
            $this->lockAndTransForPlayerBalance($player_info['playerId'], function() use($controller, $player_info, &$transaction_data) {
                if($old_transaction = $this->pp_transactions->searchByExternalTransactionIdByTransactionType($controller->requestParams->params['reference'])) {
                    $transaction_data['code'] = self::RETURN_OK;
                    $transaction_data['transaction_id'] = $old_transaction[0]['external_uniqueid'];
                    $transaction_data['after_balance'] = $this->api->queryPlayerBalance($player_info['username'])['balance'];
                    return true;
                }
                if($controller->requestParams->params['amount'] < 0) {
                    $transaction_data['code'] = self::ERROR_BAD_REQUEST;
                    return false;
                }
                $transaction_data = $controller->adjustWallet(self::TRANSACTION_BONUS_NO_CREDIT, $player_info, ['amount' => 0]);
                return true;
            });

            if($transaction_data['code'] != self::RETURN_OK) {
                return $this->setResponse($transaction_data['code']);
            }
            $data = [
                'transactionId' => $transaction_data['transaction_id'],
                'currency' => $this->api->currency,
                'cash' => $transaction_data['after_balance'],
                'bonus' => 0,
            ];
            return $this->setResponse($transaction_data['code'], $data);
        }
        return $this->setResponse(self::ERROR_NOT_FOUND_PLAYER);
    }

    public function jackpotWin() {
        $rule_set = [
            'hash' => 'required',
            'userId' => 'required',
            'gameId' => 'required',
            'roundId' => 'required',
            'amount' => 'required',
            'reference' => 'required',
            'providerId' => 'required',
            'timestamp' => 'required',
            'jackpotId' => 'required',
        ];

        $this->preProcessRequest(__FUNCTION__, $rule_set);
        $user_name = $this->api->getPlayerUsernameByGameUsername($this->requestParams->params['userId']);
        $player_info = (array) $this->api->getPlayerInfoByUsername($user_name);
        if($player_info) {

            $controller = $this;
            $transaction_data = [
                'code' => self::RETURN_OK
            ];
            $this->lockAndTransForPlayerBalance($player_info['playerId'], function() use($controller, $player_info, &$transaction_data) {
                if($old_transaction = $this->pp_transactions->searchByExternalTransactionIdByTransactionType($controller->requestParams->params['reference'])) {
                    $transaction_data['code'] = self::RETURN_OK;
                    $transaction_data['transaction_id'] = $old_transaction[0]['external_uniqueid'];
                    $transaction_data['after_balance'] = $this->api->queryPlayerBalance($player_info['username'])['balance'];
                    return true;
                }
                if($controller->requestParams->params['amount'] < 0) {
                    $transaction_data['code'] = self::ERROR_BAD_REQUEST;
                    return false;
                }
                $transaction_data = $controller->adjustWallet(self::TRANSACTION_CREDIT, $player_info);
                return true;
            });

            if($transaction_data['code'] != self::RETURN_OK) {
                return $this->setResponse($transaction_data['code']);
            }
            $data = [
                'transactionId' => $transaction_data['transaction_id'],
                'currency' => $this->api->currency,
                'cash' => $transaction_data['after_balance'],
                'bonus' => 0
            ];
            return $this->setResponse($transaction_data['code'], $data);
        }
        return $this->setResponse(self::ERROR_NOT_FOUND_PLAYER);
    }

    public function refund() {
        $rule_set = [
            'hash' => 'required',
            'userId' => 'required',
            'gameId' => 'required',
            'roundId' => 'required',
            'amount' => 'required',
            'reference' => 'required',
            'providerId' => 'required',
            'timestamp' => 'required',
            'roundDetails' => 'required',
        ];

        $this->preProcessRequest(__FUNCTION__, $rule_set);
        $user_name = $this->api->getPlayerUsernameByGameUsername($this->requestParams->params['userId']);
        $player_info = (array) $this->api->getPlayerInfoByUsername($user_name);
        if($player_info) {

            $controller = $this;
            $transaction_data = [
                'code' => self::RETURN_OK
            ];
            $this->lockAndTransForPlayerBalance($player_info['playerId'], function() use($controller, $player_info, &$transaction_data) {
                if($old_transaction = $this->pp_transactions->searchByExternalTransactionIdByTransactionType($controller->requestParams->params['reference'])) {
                    if($old_transaction[0]['transaction_type'] == self::TRANSACTION_CANCELLED) {
                        $transaction_data['transaction_id'] = $old_transaction[1]['external_uniqueid'];
                    }
                    else {
                        $this->pp_transactions->cancelTransaction($old_transaction[0]['transaction_id']);
                        $extra = [
                            'amount' => $old_transaction[0]['amount']
                        ];
                        $transaction_data = $controller->adjustWallet(self::TRANSACTION_ROLLBACK, $player_info, $extra);
                    }
                }
                else {
                    $transaction_data['code'] = self::ERROR_BET_NOT_FOUND;
                }
                return true;
            });

            if($transaction_data['code'] != self::RETURN_OK && $transaction_data['code'] != self::ERROR_BET_NOT_FOUND) {
                return $this->setResponse($transaction_data['code']);
            }
            $data = [
                'transactionId' => isset($transaction_data['transaction_id']) ? $transaction_data['transaction_id'] : 0,
            ];
            return $this->setResponse($transaction_data['code'], $data);
        }
        return $this->setResponse(self::ERROR_NOT_FOUND_PLAYER);
    }

    public function adjustWallet($transaction_type, $player_info, $extra = []) {
        $return_data = [
            'code' => self::RETURN_OK
        ];
        $wallet_transaction = [];
        $return_data['before_balance'] = $this->api->queryPlayerBalance($player_info['username'])['balance'];
        $amount = $this->requestParams->params['amount'];

        if(array_key_exists('promoWinAmount', $this->requestParams->params)) {
            $amount += $this->requestParams->params['promoWinAmount'];
        }

        if($transaction_type == self::TRANSACTION_CREDIT) {
            $this->wallet_model->incSubWallet($player_info['playerId'], $this->api->getPlatformCode(), $amount);
            $return_data['after_balance'] = $this->api->queryPlayerBalance($player_info['username'])['balance'];

            $wallet_transaction['amount'] = $amount;
        }
        else if($transaction_type == self::TRANSACTION_DEBIT) {
            if($return_data['before_balance'] < $amount) {
                $return_data['code'] = self::ERROR_INSUFFICIENT_BALANCE;
                $return_data['after_balance'] = $return_data['before_balance'];
            }
            else {
                $this->wallet_model->decSubWallet($player_info['playerId'], $this->api->getPlatformCode(), $amount);
                $return_data['after_balance'] = $this->api->queryPlayerBalance($player_info['username'])['balance'];
                $wallet_transaction['amount'] = $amount * -1;
            }
        }
        else {
            if($amount <= 0) {
                $this->wallet_model->incSubWallet($player_info['playerId'], $this->api->getPlatformCode(), abs($amount));
                $return_data['after_balance'] = $this->api->queryPlayerBalance($player_info['username'])['balance'];
                $wallet_transaction['amount'] = $amount;
            }
            else if($amount > 0) {
                $this->wallet_model->decSubWallet($player_info['playerId'], $this->api->getPlatformCode(), $amount);
                $return_data['after_balance'] = $this->api->queryPlayerBalance($player_info['username'])['balance'];
                $wallet_transaction['amount'] = $amount * -1;
            }
        }

        $wallet_transaction['user_id'] = $this->requestParams->params['userId'];
        $wallet_transaction['game_id'] = isset($this->requestParams->params['gameId']) ? $this->requestParams->params['gameId'] : '';
        $wallet_transaction['round_id'] = isset($this->requestParams->params['roundId']) ? $this->requestParams->params['roundId'] : '';
        $wallet_transaction['transaction_id'] = $this->requestParams->params['reference'];
        $wallet_transaction['transaction_type'] = $transaction_type;
        $wallet_transaction['provider_id'] = $this->requestParams->params['providerId'];
        $wallet_transaction['timestamp'] = date("Y-m-d H:i:s", ($this->requestParams->params['timestamp']/ 1000));
        $wallet_transaction['round_details'] = isset($this->requestParams->params['roundDetails']) ? $this->requestParams->params['roundDetails'] : '';
        $wallet_transaction['before_balance'] = $return_data['before_balance'];
        $wallet_transaction['after_balance'] = $return_data['after_balance'];
        $wallet_transaction['jackpot_id'] = isset($this->requestParams->params['jackpotId']) ? $this->requestParams->params['jackpotId'] : '';
        $wallet_transaction['campaign_id'] = isset($this->requestParams->params['campaignId']) ? $this->requestParams->params['campaignId'] : (isset($this->requestParams->params['promoCampaignID']) ? $this->requestParams->params['promoCampaignID'] : '');
        $wallet_transaction['campaign_type'] = isset($this->requestParams->params['campaignType']) ? $this->requestParams->params['campaignType'] : (isset($this->requestParams->params['promoCampaignType']) ? $this->requestParams->params['promoCampaignType'] : '');;
        $wallet_transaction['currency'] = isset($this->requestParams->params['currency']) ? $this->requestParams->params['currency'] : $this->api->currency;
        $wallet_transaction['bonus_code'] = isset($this->requestParams->params['bonusCode']) ? $this->requestParams->params['bonusCode'] : '';
        $wallet_transaction['external_uniqueid'] = $this->api->getSecureId('pragmaticplay_seamless_wallet_transactions', 'external_uniqueid', true, 'PP');

        if($return_data['code'] == self::RETURN_OK) {
            $this->pp_transactions->insertTransaction($wallet_transaction);
        }

        $return_data['before_balance'] = $wallet_transaction['before_balance'];
        $return_data['after_balance'] = $wallet_transaction['after_balance'];
        $return_data['transaction_id'] = $wallet_transaction['external_uniqueid'];
        return $return_data;
    }

    public function validateRequest($rule_set) {
        $is_valid = true;
        foreach($rule_set as $key => $rules) {
            $rules = explode("|", $rules);
            foreach($rules as $rule) {
                if($rule == 'required' && !array_key_exists($key, $this->requestParams->params)) {
                    $is_valid = false;
                    break;
                }
            }
            if(!$is_valid) {
                break;
            }
        }
        return $is_valid;
    }

    public function preProcessRequest($functionName, $rule_set = []) {
        $params = $this->input->post();
        $this->requestParams->function = $functionName;
        $this->requestParams->params = $params;
        $is_valid = $this->validateRequest($rule_set);

        if(!$is_valid) {
            return $this->setResponse(self::ERROR_BAD_REQUEST);
        }

        $hash = $this->generateHash($params);
        if($hash != $params['hash']) {
            return $this->setResponse(self::ERROR_INVALID_HASH);
        }

        if($params['providerId'] != $this->api->provider_id) {
            return $this->setResponse(self::ERROR_BAD_REQUEST);
        }
    }

    private function generateHash($parameters) {
        unset($parameters['hash']);

        ksort($parameters);
        $hash = md5(http_build_query($parameters) . $this->api->secretKey);
        return $hash;
    }

    private function setResponse($returnCode, $data = []) {
        $data = array_merge($data, $returnCode);
        return $this->setOutput($data);
    }

    private function setOutput($data = []) {
        $flag = $data['error'] == 0 ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;

        if(array_key_exists('cash', $data)) {
            $data['cash'] = number_format($data['cash'], 2, '.', '');
        }
        if(array_key_exists('bonus', $data)) {
            $data['bonus'] = number_format($data['bonus'], 2, '.', '');
        }
        if(array_key_exists('usedPromo', $data)) {
            $data['usedPromo'] = number_format($data['usedPromo'], 2, '.', '');
        }

        $data = json_encode($data);
        $this->CI->response_result->saveResponseResult(
            $this->api->getPlatformCode(),
            $flag,
            $this->requestParams->function,
            json_encode($this->requestParams->params),
            $data,
            200,
            null,
            null
        );

        $this->output->set_content_type('application/json')->set_output($data);
        $this->output->_display();
        exit();
    }
}