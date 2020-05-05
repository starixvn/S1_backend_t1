<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Class Gamegateway
 *
 * support game api proxy with game logs
 *
 */
class Gamegateway extends BaseController {

	private $params;
	private $merchant_code;
    private $currency;
	private $merchant;
    private $agent_obj;
	private $game_api;
	private $username;
    private $player_id;
	private $game_platform_id;
    private $start_time;

    const VERSION='5.06';

	const CODE_SUCCESS='0';
	const CODE_INVALID_SIGN='1';
	const CODE_INVALID_MERCHANT_CODE='2';
	const CODE_INVALID_SECURE_KEY='3';
	const CODE_INVALID_AUTH_TOKEN='4';
	const CODE_INVALID_GAME_PLATFORM_ID='5';
	const CODE_INVALID_USERNAME='6';
	const CODE_INVALID_PASSWORD='7';
	const CODE_DUPLICATE_USERNAME='8';
	const CODE_INVALID_EXTRA_INFO='9';
	const CODE_INVALID_GAME_PLATFORM_SETTINGS='10';
	const CODE_INVALID_BET_LIMIT='11';
	const CODE_INVALID_ACTION_TYPE='12';
	const CODE_INVALID_AMOUNT='13';
	const CODE_DUPLICATE_EXTERNAL_TRANS_ID='14';
	const CODE_NO_ENOUGH_BALANCE='15';
	const CODE_INVALID_FROM_DATE_TIME='16';
	const CODE_INVALID_TO_DATE_TIME='17';
	const CODE_INVALID_EXTERNAL_TRANS_ID='18';
	const CODE_EXTERNAL_API_ERROR='19';
	const CODE_INVALID_SIZE_PRE_PAGE='20';
	const CODE_INVALID_TOTAL_TYPE='21';
    const CODE_INVALID_LAUNCHER_SETTINGS='22';
    const CODE_INVALID_PAGE_NUMBER='23';
    const CODE_USERNAME_DOES_NOT_BELONG_MERCHANT='24';
    const CODE_INVALID_GAME_STATUS='25';
    const CODE_INVALID_DATE_MODE='26';
    const CODE_INVALID_MULTIPLE_GAME_PLATFORM='27';
    const CODE_INVALID_GAME_HISTORY_STATUS='28';
    const CODE_INVALID_USERNAME_PREFIX='29';
    const CODE_CREATE_MERCHANT_FAILED='30';
    const CODE_INVALID_MASTER_KEY='31';
    const CODE_CREDIT_LIMIT_OR_AVAILABLE_LIMIT_IS_EMPTY='32';
    const CODE_CREATE_API_FAILED='33';
    const CODE_INVALID_CURRENCY='34';
    const CODE_INVALID_BET_CODE='35';
    const CODE_INVALID_BET_DETAILS='36';
    const CODE_INVALID_GAME_CODE='37';
    const CODE_INVALID_GAME_CATEGORY='38';
    const CODE_INVALID_MIN_SIZE='39';
    const CODE_INVALID_GAME_TYPE_CODE='40';
    const CODE_NO_ACTIVE_GAME_PLATFORM_IN_AGENT='41';
    const CODE_NO_PERMISSION_ON_GAME_PLATFORM='42';

    //internal
    const CODE_INVALID_INIT_GAME_PLATFORM_ID='8000';
    const CODE_NO_PERMISSION_TO_CREATE_MERCHANT='8001';
    const CODE_NO_PERMISSION_TO_CREATE_GAME_API='8002';
    const CODE_NO_PERMISSION_TO_UPDATE_GAME_LIST='8003';
    const CODE_INVALID_GAME_LIST='8004';
    const CODE_INVALID_GAME_TYPE_LIST='8005';

    //system
    const CODE_LOCK_FAILED='9998';
	const CODE_INTERNAL_ERROR='9999';

	protected $codes=[
		self::CODE_SUCCESS=>'success',
		self::CODE_INVALID_SIGN=>'invalid signature',
		self::CODE_INVALID_MERCHANT_CODE=>'invalid merchant_code',
		self::CODE_INVALID_SECURE_KEY=>'invalid secure_key',
		self::CODE_INVALID_AUTH_TOKEN=>'invalid auth_token',
		self::CODE_INVALID_GAME_PLATFORM_ID=>'invalid game_platform_id',
		self::CODE_INVALID_USERNAME=>'invalid username',
		self::CODE_INVALID_PASSWORD=>'invalid password',
		self::CODE_DUPLICATE_USERNAME=>'duplicate username',
		self::CODE_INVALID_EXTRA_INFO=>'invalid extra info',
		self::CODE_INVALID_GAME_PLATFORM_SETTINGS=>'invalid game platform settings',
		self::CODE_INVALID_BET_LIMIT=>'invalid bet limit',
		self::CODE_INVALID_ACTION_TYPE=>'invalid action type',
		self::CODE_INVALID_AMOUNT=>'invalid amount',
		self::CODE_DUPLICATE_EXTERNAL_TRANS_ID=>'duplicate external_trans_id',
		self::CODE_NO_ENOUGH_BALANCE=>'No enough balance',
		self::CODE_INVALID_FROM_DATE_TIME=>'invalid from date time',
		self::CODE_INVALID_TO_DATE_TIME=>'invalid to date time',
		self::CODE_INVALID_EXTERNAL_TRANS_ID=>'invalid external_trans_id',
		self::CODE_EXTERNAL_API_ERROR=>'external api error',
		self::CODE_INVALID_SIZE_PRE_PAGE=>'invalid size pre page',
		self::CODE_INVALID_TOTAL_TYPE=>'invalid total type',
        self::CODE_INVALID_LAUNCHER_SETTINGS=>'invalid launcher settings',
        self::CODE_INVALID_PAGE_NUMBER=>'invalid page number',
        self::CODE_USERNAME_DOES_NOT_BELONG_MERCHANT=>'username does not belong this merchant',
    	self::CODE_INVALID_GAME_STATUS => 'invalid game status',
    	self::CODE_INVALID_DATE_MODE => 'invalid date mode',
    	self::CODE_INVALID_MULTIPLE_GAME_PLATFORM => 'invalid multiple game platform',
    	self::CODE_INVALID_GAME_HISTORY_STATUS=>'invalid game history status',
    	self::CODE_INVALID_USERNAME_PREFIX=>'invalid username prefix',
    	self::CODE_CREATE_MERCHANT_FAILED => 'create merchant failed',
    	self::CODE_INVALID_MASTER_KEY => 'invalid master key',
    	self::CODE_CREDIT_LIMIT_OR_AVAILABLE_LIMIT_IS_EMPTY => 'credit limit or available limit is empty',
    	self::CODE_CREATE_API_FAILED => 'create api failed',
        self::CODE_INVALID_CURRENCY=>'invalid currency',
        self::CODE_INVALID_BET_CODE=>'invalid bet code',
        self::CODE_INVALID_BET_DETAILS=>'invalid bet details',
        self::CODE_INVALID_GAME_CODE=>'invalid game code',
        self::CODE_INVALID_GAME_CATEGORY=>'invalid game category',
        self::CODE_INVALID_MIN_SIZE=>'invalid min size',
        self::CODE_INVALID_GAME_TYPE_CODE=>'invalid game type code',
        self::CODE_NO_ACTIVE_GAME_PLATFORM_IN_AGENT=>'no active game platform in agent',
        self::CODE_NO_PERMISSION_ON_GAME_PLATFORM=>'no permission on game platform',

        self::CODE_INVALID_INIT_GAME_PLATFORM_ID=>'invalid init_game_platform_id',
        self::CODE_NO_PERMISSION_TO_CREATE_MERCHANT=>'no permission to create merchant',
        self::CODE_NO_PERMISSION_TO_CREATE_GAME_API=>'no permission to create game api',
        self::CODE_NO_PERMISSION_TO_UPDATE_GAME_LIST=>'no permission to update game list',
        self::CODE_INVALID_GAME_LIST=>'invalid game list',
        self::CODE_INVALID_GAME_TYPE_LIST=>'invalid game type list',

        self::CODE_LOCK_FAILED=>'lock failed',
		self::CODE_INTERNAL_ERROR=>'internal error',
	];

	const DEFAULT_SIZE_PRE_PAGE=1000;
    const DEFAULT_MIN_SIZE_STREAM=2000;
	const GAME_STATUS_VALID_VALUE=['settled','unsettle'];
	const DATE_MODE_VALID_VALUE=['by_bet_time','by_last_update_time', 'by_payout_time'];
	const DEFAULT_GAME_STATUS_FILTER = 'settled';
	const DEFAULT_DATE_MODE_FILTER = 'by_bet_time';

	const DATE_MODE_BY_BET_TIME='by_bet_time';
	const DATE_MODE_BY_LAST_UPDATE_TIME='by_last_update_time';

	const GAME_HISTORY_STATUS_VALID_VALUE=['normal','others'];
	const GAME_HISTORY_STATUS_NORMAL = 'normal';
	const GAME_HISTORY_STATUS_OTHERS = 'others';

    const GAME_CATEGORY_VALID_VALUE=['hb'];

	const MAX_SIZE_PER_PAGE=5000;
	const MIN_SIZE_PER_PAGE=10;

    const IGNORE_VALIDATE_USERNAME=['create_player_account'];

    const CREDIT_LIMIT = 100000000;
    const AVAILABLE_LIMIT = 100000000;

    const FLAG_TRUE = 1;
    const FLAG_FALSE = 0;
    const LANG_EN = 1;

	const SYNC_LOGS_INTERVAL = "5 minutes";

	public function __construct() {

		parent::__construct();

		$this->load->model(['common_token', 'external_system', 'player_model', 'agency_model', 'wallet_model']);

	}

	protected function initApi($require_game_api=false){
        $this->start_time=time();

		//read json
		$json = file_get_contents('php://input');
		$this->params=$this->utils->decodeJson($json);
        $this->currency=$this->getParam('currency');
        if(empty($this->currency)){
            //try get it from __OG_TARGET_DB
            $this->currency=$this->input->get(Multiple_db::__OG_TARGET_DB);
            if(empty($this->currency)){
                //still empty
                $this->currency=null;
            }
        }

        if($this->validateCurrencyAndSwitchDB()){
            //safe
        }else{
            $this->returnError(self::CODE_INVALID_CURRENCY);
            return false;
        }
        $this->load->library(['uri']);
        //example: /gamegateway/is_player_account_exist
        $this->api_name=$this->uri->segment(2);
        $this->merchant_code=$this->getParam('merchant_code');
		$this->game_platform_id=$this->getIntParam('game_platform_id');
        $this->username=$this->getParam('username');
        $this->multiple_game_platform=$this->getParam('multiple_game_platform');

		//record raw call
		$this->utils->debug_log('-------- get json from input on game api', $json, $this->params, $this->merchant_code);
        unset($json);

		if(!empty($this->merchant_code)){
            $this->agent_obj=$this->merchant=$this->agency_model->get_agent_by_name($this->merchant_code);
            $this->agent_id=$this->agent_obj['agent_id'];
//		}else{
//			$this->merchant=null;
//			$this->returnError(self::CODE_INVALID_MERCHANT_CODE);
//			return false;
		}

		$this->actived_game=$this->external_system->getAgentActivedGameApiList($this->agent_obj['agent_id']);
        $this->exists_game_api_list=$this->external_system->getSystemCodeMapping();

		$this->utils->debug_log('-------- game platforms available for agent', $this->actived_game, $this->merchant_code);

        if($this->validateMerchant()){
            //safe
        }else{
            $this->returnError(self::CODE_INVALID_MERCHANT_CODE);
            return false;
        }

        if($this->validateSign()){
			//safe
		}else{
			$this->returnError(self::CODE_INVALID_SIGN);
			return false;
		}

		if($this->validateAuthToken()){
			//safe
		}else{
			$this->returnError(self::CODE_INVALID_AUTH_TOKEN);
			return false;
		}

        if($this->validateUsername()){
            //safe
        }else{
            $this->returnError(self::CODE_USERNAME_DOES_NOT_BELONG_MERCHANT);
            return false;
        }

        if($this->validateGamePlatformId()){
			//safe
		}else{
            if(empty($this->actived_game)){
                //no any permission
                $this->returnError(self::CODE_NO_ACTIVE_GAME_PLATFORM_IN_AGENT);
            }if(array_key_exists($this->game_platform_id, $this->exists_game_api_list)){
                //exists game platform , but no permission
                $this->returnError(self::CODE_NO_PERMISSION_ON_GAME_PLATFORM);
            }else{
                $this->returnError(self::CODE_INVALID_GAME_PLATFORM_ID);
            }
			return false;
		}

        if($this->validateMultipleGamePlatform()){
			//safe
		}else{
			$this->returnError(self::CODE_INVALID_MULTIPLE_GAME_PLATFORM);
			return false;
		}

		if(!empty($this->game_platform_id)){
			//load game api
			$this->game_api=$this->utils->loadExternalSystemLibObject($this->game_platform_id);
			if($require_game_api && empty($this->game_api)){
				return false;
			}
		}

		return true;
	}

	protected function getParam($key, $default=null){
		if(isset($this->params[$key])){
			return $this->params[$key];
		}

		return $default;
	}

    protected function resetAllParam(){
        unset($this->params);
        $this->params=[];
    }

    protected function getIntParam($key, $default=null){

	    return intval($this->getParam($key, $default));

    }

    protected function getBoolParam($key, $default=null){
        $val=$this->getParam($key, $default);
        if(is_bool($val)){
            return $val;
        }
        if(is_string($val)){
            //string true
            return strtolower($val)=='true';
        }
        if(is_int($val)){
            //!=0 is true
            return $val!=0;
        }

        return boolval($val);
    }

    private $except=['generate_token'];
	private $actived_game=[];

	protected function validateSign(){

		$signKey=$this->common_token->getSignKeyFrom($this->agent_obj);
		if(empty($signKey)){
			$this->utils->debug_log('empty sign key for :'. $this->getParam('merchant_code'));
			return false;
		}

		list($sign, $signString)=$this->common_token->generateSign($this->params, $signKey, ['sign']);

		$requestSign=strtolower($this->getParam('sign'));

		$this->utils->debug_log('sign string:'.$signString.', sign:'.$sign.', request sign:'.$requestSign);

		return $sign===$requestSign;
	}

	protected function validateAuthToken(){
		$success=false;
		$func=$this->uri->segment(2);
		if(in_array($func, $this->except)){
			//ignore
			$success=true;
		}else{
			//check token
			if($this->common_token->isValidAuthToken($this->agent_obj, $this->getParam('auth_token'))){
				//pass
				$success=true;
			}else{
				$success=false;
			}
		}

		return $success;
	}

	protected function validateGamePlatformId(){

		if(!empty($this->game_platform_id)){
            $this->game_platform_id=intval($this->game_platform_id);
			if(in_array($this->game_platform_id, $this->actived_game)){
				return true;
			}else{
				$this->utils->debug_log('validateGamePlatformId', $this->game_platform_id, $this->actived_game);
				return false;
			}

		}else{
			//ignore
			return true;
		}

	}

	protected function validateMultipleGamePlatform(){

		if(!empty($this->multiple_game_platform)){
            if(!is_array($this->multiple_game_platform)){
                $this->utils->debug_log('validateMultipleGamePlatform failed is not array', $this->multiple_game_platform);
                return false;
            }

			foreach ($this->multiple_game_platform as $game_platform_id) {
				if(in_array($game_platform_id, $this->actived_game)){
				}else{
					$this->utils->debug_log('validateMultipleGamePlatform', $this->multiple_game_platform, $this->actived_game);
					return false;
				}
			}

			return true;

		}else{
			//ignore
			return true;
		}

	}
    protected function validateMerchant(){

        if(empty($this->merchant_code) || empty($this->agent_obj)){

            return false;

        }else{
            //ignore
            return true;
        }

    }

    protected function validateCurrencyAndSwitchDB(){
        if(!$this->utils->isEnabledMDB()){
            return true;
        }
        if(empty($this->currency)){
            return false;
        }else{
            //validate currency name
            if(!$this->utils->isAvailableCurrencyKey($this->currency)){
                //invalid currency name
                return false;
            }else{
                //switch to target db
                $_multiple_db=Multiple_db::getSingletonInstance();
                $_multiple_db->switchCIDatabase($this->currency);
                return true;
            }
        }
    }

    protected function validateUsername(){
	    //username belongs agent
        if(!empty($this->username)){
            //ignore create_player_account function
            if(in_array($this->uri->segment(2), self::IGNORE_VALIDATE_USERNAME)){
                return true;
            }
            //update back
            $this->params['username']=$this->username=$this->appendPrefixIfPossible($this->username);
            //username belongs agent
            if($this->agency_model->isUnderAgent($this->agent_obj, $this->username)){
                $this->player_id=$this->player_model->getPlayerIdByUsername($this->username);
                if(!empty($this->player_id)){
                    $this->player_id=intval($this->player_id);
                }
                return true;
            }else{
                return false;
            }
        }
        return true;

    }

    protected function returnError($code, $customized_message=null, $detail=null){
        //if error is does not belong and it's is_player_account_exist, don't write to response result
        $ignoreWriteLog=false;
        if($code==self::CODE_USERNAME_DOES_NOT_BELONG_MERCHANT && $this->api_name=='is_player_account_exist'){
            $ignoreWriteLog=true;
        }
        ///gamegateway/query_multiple_platform_stream_game_history
        if($code==self::CODE_INVALID_FROM_DATE_TIME && $this->api_name=='query_multiple_platform_stream_game_history'){
            $ignoreWriteLog=true;
        }

        $message=$customized_message;
        if(empty($message)){
            $message=$this->codes[$code];
        }

        $result=['success'=>false, 'version'=>self::VERSION, 'code'=>$code, 'message'=> $message,
            'request_id'=>$this->utils->getRequestId(), 'server_time'=>$this->utils->getNowForMysql(),
            'cost'=>time()-$this->start_time,
            'detail'=>$detail];
        if(!$ignoreWriteLog){
            //get function name
            $requstApi=$this->api_name;
            $returnJson=$result;
            $is_error=true;
            $this->saveGamegatewayResponseResult($requstApi, $returnJson, $is_error);
        }

		return $this->returnJsonResult($result);
	}

	protected function returnSuccess($detail, $customized_message=null){
		$code=self::CODE_SUCCESS;
		$message=$customized_message;
		if(empty($message)){
			$message=$this->codes[$code];
		}

		$result=['success'=>true, 'version'=>self::VERSION, 'code'=>$code, 'message'=> $message,
			'request_id'=>$this->utils->getRequestId(), 'server_time'=>$this->utils->getNowForMysql(),
            'cost'=>time()-$this->start_time,
			'detail'=>$detail];

		return $this->returnJsonResult($result);
	}

    protected function saveGamegatewayResponseResult($requstApi, $returnJson,
            $is_error=false, $extra=null, $statusCode=200, $statusText=null){
        $this->load->model(['response_result']);
        $systemId=GAMEGATEWAY_API;
        $flag= $is_error ? Response_result::FLAG_ERROR : Response_result::FLAG_NORMAL;
        $requestParams=json_encode($this->params);
        if(empty($returnJson)){
            $returnJson=[];
        }
        if(!is_array($returnJson)){
            $returnJson=[$returnJson];
        }
        $returnJson['cost']=time()-$this->start_time;
        $resultText=json_encode($returnJson);
        return $this->response_result->saveResponseResult($systemId, $flag, $requstApi,
            $requestParams, $resultText, $statusCode, $statusText, $extra);
    }

	protected function returnUnimplemented($customized_message=null){

		$code=self::CODE_SUCCESS;
		$message=$customized_message;
		if(empty($message)){
			$message=$this->codes[$code];
		}

		$result=['success'=>true, 'code'=>$code, 'message'=> $message, 'request_id'=>$this->utils->getRequestId(),
			'detail'=>['unimplemented'=>true]];

		return $this->returnJsonResult($result);

	}

    protected function appendPrefixIfPossible(&$username){
        if(empty($username)){
            return $username;
        }
        // $default_no_prefix_on_username=$this->agent_obj['no_prefix_on_username']==Agency_model::DB_TRUE;
        $no_prefix_on_username=$this->getParam('no_prefix_on_username', false);
        if($no_prefix_on_username){
            $player_prefix=$this->agent_obj['player_prefix'];
            //create username with prefix
            $username=$player_prefix.$username;
        }

        return $username;
    }

    /**
     *
     * @return bool|void
     * @internal param string $merchant_code
     * @internal param string $secure_key
     * @internal param string $sign
     *
     */
	public function generate_token(){

		if(!$this->initApi()){
			return false;
		}

		list($auth_token, $timeout_datetime)=$this->common_token->generateAuthKeyBySecureKey(
			$this->agent_obj, $this->getParam('secure_key'), $this->getParam('force_new', false));

		if(empty($auth_token)){
			return $this->returnError(self::CODE_INVALID_SECURE_KEY);
		}else{
			$detail=['auth_token'=> $auth_token, 'timeout_datetime'=> $timeout_datetime];
			return $this->returnSuccess($detail);
		}
	}

	public function create_player_account(){
		if(!$this->initApi(true)){
			return false;
		}
		//validate
		//username: 6-12 , letter and number
		//password: 6-12 , letter and number
		//realname: 1-100
		//extra: optional

		$username=$this->getParam('username');
		$password=$this->getParam('password');
		$realname=$this->getParam('realname');
        $no_prefix_on_username=$this->getParam('no_prefix_on_username', false);
        $create_or_update_mode=$this->getParam('create_or_update_mode', true);
        $lock_when_create_player_account_on_gamegateway_api=$this->getParam('lock_when_create_player_account_on_gamegateway_api',
            $this->utils->getConfig('lock_when_create_player_account_on_gamegateway_api'));
		$extra=$this->getParam('extra');

		if(empty($username)){
		    return $this->returnError(self::CODE_INVALID_USERNAME);
        }
        if(empty($password)){
            return $this->returnError(self::CODE_INVALID_PASSWORD);
        }
        $levelId=$this->utils->getConfig('default_level_id');
        $agentId=$this->agent_obj['agent_id'];
        $this->load->model(['player_model','agency_model', 'game_provider_auth']);
        $player_prefix = $this->agency_model->getPlayerPrefixByAgentId($agentId);
        $usernameWithoutPrefix=$username;
        if($no_prefix_on_username){
            //still create username with prefix
            $username=$player_prefix.$username;
        }else{
            $param_username_prefix = substr($username, 0, strlen($player_prefix));
            # checking username prefix
            if($player_prefix != $param_username_prefix){
                $this->utils->error_log('CODE_INVALID_USERNAME_PREFIX', $player_prefix,
                    $param_username_prefix, 'no_prefix_on_username', $no_prefix_on_username);
    			return $this->returnError(self::CODE_INVALID_USERNAME_PREFIX);
            }
            $usernameWithoutPrefix=substr($username, strlen($player_prefix));
        }

        //check duplicate username
        $playerInfo=$this->player_model->getPlayerArrayByUsername($username);
        $this->utils->debug_log('playerInfo', $playerInfo, 'username', $username, 'player_prefix', $player_prefix,
            'usernameWithoutPrefix', $usernameWithoutPrefix, 'no_prefix_on_username', $no_prefix_on_username);
        if($create_or_update_mode){
            if(!empty($playerInfo) && $playerInfo['agent_id']!=$agentId){
            	//duplicate between other agent
            	return $this->returnError(self::CODE_DUPLICATE_USERNAME);
            }
        }else{
            if(!empty($playerInfo)){
                //duplicate
                return $this->returnError(self::CODE_DUPLICATE_USERNAME);
            }
        }
        $created_mode='created_only';
        if(!empty($playerInfo) && $playerInfo['agent_id']==$agentId){
            $created_mode='updated';
        }
        $playerId=null;
        $success=false;
        $this->utils->debug_log('lock_when_create_player_account_on_gamegateway_api', $lock_when_create_player_account_on_gamegateway_api);
        $isDuplicate=false;
        $isLockFailed=false;
        if($lock_when_create_player_account_on_gamegateway_api){
            $enable_username_cross_site_checking=$this->utils->isEnabledMDB() || $this->utils->isEnabledFeature('enable_username_cross_site_checking');
            $this->utils->debug_log('enable_username_cross_site_checking', $enable_username_cross_site_checking);
            //same with register
            if($enable_username_cross_site_checking){
                //global lock
                $add_prefix = false;
                $anyid = 0;
            } else {
                //not global lock but still lock
                $add_prefix = true;
                $anyid = 0; // random_string('numeric', 5);
            }
            $success=$this->lockAndTrans(Utils::GLOBAL_LOCK_ACTION_REGISTRATION, $anyid, function ()
                use (&$playerId, $username, $password, $realname, $levelId, $agentId,
                    $usernameWithoutPrefix, $no_prefix_on_username, $extra, $create_or_update_mode, &$isDuplicate) {
                //double check
                $playerInfo=$this->player_model->getPlayerArrayByUsername($username);
                if($create_or_update_mode){
                    if(!empty($playerInfo) && $playerInfo['agent_id']!=$agentId){
                        //duplicate between other agent
                        // return $this->returnError(self::CODE_DUPLICATE_USERNAME);
                        $isDuplicate=true;
                        return false;
                    }
                }else{
                    if(!empty($playerInfo)){
                        //duplicate
                        // return $this->returnError(self::CODE_DUPLICATE_USERNAME);
                        $isDuplicate=true;
                        return false;
                    }
                }

                //sync player in player table
                $playerId=$this->player_model->syncPlayerInfoFromExternal($username, $password, $realname,
                    $levelId, $agentId, $extra);
                $this->utils->debug_log('syncPlayerInfoFromExternal', $playerId, $username);

                $success=!empty($playerId);
                if($success){
                    $gameAccountUsername=$username;
                    if($no_prefix_on_username){
                        $gameAccountUsername=$usernameWithoutPrefix;
                        $this->utils->debug_log('sync game account with lock', $gameAccountUsername);
                        $success=$this->game_provider_auth->syncGameAccountForAgency($agentId, $playerId,
                            $gameAccountUsername, $password, $this->game_platform_id);
                        $this->utils->debug_log('syncGameAccountForAgency', $gameAccountUsername, $playerId, $success);
                        if(!$success){
                            $this->utils->error_log('syncGameAccountForAgency failed', $agentId, 'playerId', $playerId,
                                'gameAccountUsername', $gameAccountUsername, 'username', $username,
                                'usernameWithoutPrefix', $usernameWithoutPrefix, 'password', $password);
                        }
                        // $prefixMap=$this->agency_model->getPrefixMapForGameAccount($agentId);
                        // $this->utils->debug_log('getPrefixMapForGameAccount', $agentId, $prefixMap);
                    }
                }
                return $success;

            }, $add_prefix, $isLockFailed);
            $this->utils->debug_log('finish syncPlayerInfoFromExternal with lock', $success, $isLockFailed, $isDuplicate);
            if($success && !empty($playerId)){
                //sync player
                // $insert_only=true;$rlt=null;
                // $success=$this->syncPlayerCurrentToMDB($playerId, $insert_only, $rlt);
                $syncSucc=$this->syncPlayerCurrentToMDBWithLock($playerId, $username);
                $this->utils->debug_log('finish syncPlayerCurrentToMDBWithLock', $syncSucc, $playerId, $username);
            }

        }else{
            //sync player in player table
            $playerId=$this->player_model->syncPlayerInfoFromExternal($username, $password, $realname,
                $levelId, $agentId, $extra);
            $success=!empty($playerId);
            $this->utils->debug_log('finish syncPlayerInfoFromExternal without lock', $success);
            if($success){
                $gameAccountUsername=$username;
                if($no_prefix_on_username){
                    $gameAccountUsername=$usernameWithoutPrefix;
                    $this->utils->debug_log('sync game account without lock', $gameAccountUsername);
                    $success=$this->game_provider_auth->syncGameAccountForAgency($agentId, $playerId,
                        $gameAccountUsername, $password, $this->game_platform_id);
                    if(!$success){
                        $this->utils->error_log('syncGameAccountForAgency failed', $agentId, 'playerId', $playerId,
                            'gameAccountUsername', $gameAccountUsername, 'username', $username,
                            'usernameWithoutPrefix', $usernameWithoutPrefix, 'password', $password);
                    }
                    // $prefixMap=$this->agency_model->getPrefixMapForGameAccount($agentId);
                    // $this->utils->debug_log('getPrefixMapForGameAccount', $agentId, $prefixMap);
                }
            }
            if($success){
                //sync player
                $syncSucc=$this->syncPlayerCurrentToMDBWithLock($playerId, $username);
                $this->utils->debug_log('finish syncPlayerCurrentToMDBWithLock', $syncSucc, $playerId, $username);
            }
        }

		if(!$success || empty($playerId)){
            if($isDuplicate){
                return $this->returnError(self::CODE_DUPLICATE_USERNAME);
            }else if($isLockFailed){
                return $this->returnError(self::CODE_LOCK_FAILED);
            }else{
    			//internal error
    			return $this->returnError(self::CODE_INTERNAL_ERROR, 'save player info failed');
            }
		}
        //sync player
        // $this->syncPlayerCurrentToMDBWithLock($playerId, $username);

		$extra['agent_id'] = $agentId;
		// $rlt=$this->game_api->createPlayer($username, $playerId, $password, @$extra['email'], ['agent_id'=>$agentId]);
		$email=isset($extra['email']) ? $extra['email'] : null;
        $rlt=$this->game_api->createPlayer($username, $playerId, $password, $email, $extra);

		$this->utils->debug_log('call createPlayer by '.$username.' result', $rlt);

		if($rlt && $rlt['success']){

			$detail=['username'=>$username, 'created_mode'=>$created_mode];
            $requstApi='create_player_account';
            $this->saveGamegatewayResponseResult($requstApi, $detail);

            $this->appendApiLogs($detail);
			return $this->returnSuccess($detail);
		}else{
            $this->appendApiLogs($rlt);
			return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
		}

	}

	public function is_player_account_exist(){
		if(!$this->initApi(true)){
			return false;
		}

		$username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

        $this->utils->debug_log('is_player_account_exist username', $username);

		$extra=$this->getParam('extra');
		$rlt=$this->game_api->isPlayerExist($username, $extra);
		if($rlt && $rlt['success']){
			$detail=['exists'=>!!$rlt['exists']];
            $this->appendApiLogs($detail);
			return $this->returnSuccess($detail);
		}else{
            $this->appendApiLogs($rlt);
			return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
		}

	}

	public function query_player_account(){
		if(!$this->initApi(true)){
			return false;
		}

		$username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

		$rlt=$this->game_api->queryPlayerInfo($username);
		if($rlt && $rlt['success']){
			$detail=['username'=>$username, 'realname'=>@$rlt['realname'], 'extra'=>$rlt];
            $this->appendApiLogs($detail);
			return $this->returnSuccess($detail);
		}else{
            $this->appendApiLogs($rlt);
			return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
		}

	}

	public function update_player_account(){
		if(!$this->initApi(true)){
			return false;
		}

		$username=$this->username;
		$realname=$this->getParam('realname');
		$extra=$this->getParam('extra');
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

		$extra['realname']=$realname;
		$rlt=$this->game_api->updatePlayerInfo($username, $extra);
		if($rlt && $rlt['success']){
			$detail=['updated'=>true];
            $this->appendApiLogs($detail);
			return $this->returnSuccess($detail);
		}else{
            $this->appendApiLogs($rlt);
			return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
		}

	}

	public function change_player_password(){
		if(!$this->initApi(true)){
			return false;
		}

		$username=$this->username;
		$password=$this->getParam('password');
		$old_password=$this->getParam('old_password');
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }
        if(empty($password)){
            return $this->returnError(self::CODE_INVALID_PASSWORD);
        }

		$rlt=$this->game_api->changePassword($username, $old_password, $password);

		$this->utils->debug_log('call changePassword by '.$username.' result', $rlt);

		if($rlt && $rlt['success']){
			if (isset($rlt['unimplemented']) && $rlt['unimplemented'] == true) {
                return $this->returnUnimplemented();
            } else {
                $detail=['updated'=>true];
                $this->appendApiLogs($detail);
                return $this->returnSuccess($detail);
            }
		}else{
            $this->appendApiLogs($rlt);
			return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
		}

	}

    public function check_player_internal_password(){
        if(!$this->initApi(true)){
            return false;
        }

        $username=$this->username;
        $password=$this->getParam('password');
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }
        if(empty($password)){
            return $this->returnError(self::CODE_INVALID_PASSWORD);
        }

        //search game_provider_auth
        $this->load->model(['game_provider_auth']);
        $gamePassword=$this->game_provider_auth->getPasswordByPlayerId($this->player_id, $this->game_platform_id);
        $success=$gamePassword!==null;
        $this->utils->debug_log('call internal password by '.$username.' result', $success);

        if($success){
            $detail=['is_same_password'=>$gamePassword==$password];
            return $this->returnSuccess($detail);
        }else{
            return $this->returnError(self::CODE_EXTERNAL_API_ERROR);
        }
    }

	public function block_player_account(){
		if(!$this->initApi(true)){
			return false;
		}

		$username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

		$rlt=$this->game_api->blockPlayer($username);

		$this->utils->debug_log('call blockPlayer by '.$username.' result', $rlt);

		if($rlt && $rlt['success']){
			$detail=['blocked'=>true];
            $this->appendApiLogs($detail);
			return $this->returnSuccess($detail);
		}else{
            $this->appendApiLogs($rlt);
			return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
		}

	}

	public function unblock_player_account(){
		if(!$this->initApi(true)){
			return false;
		}

		$username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

		$rlt=$this->game_api->unblockPlayer($username);

		$this->utils->debug_log('call unblockPlayer by '.$username.' result', $rlt);

		if($rlt && $rlt['success']){
			$detail=['blocked'=>false];
            $this->appendApiLogs($detail);
			return $this->returnSuccess($detail);
		}else{
            $this->appendApiLogs($rlt);
			return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
		}

	}

	public function query_player_block_status(){
		if(!$this->initApi(true)){
			return false;
		}

		$username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

		$rlt=$this->game_api->isBlockedResult($username);

		$this->utils->debug_log('call isBlocked by '.$username.' result', $rlt);

		if($rlt && $rlt['success']){
			$detail=['blocked'=>$rlt['blocked']];
            $this->appendApiLogs($detail);
			return $this->returnSuccess($detail);
		}else{
            $this->appendApiLogs($rlt);
			return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
		}

	}

    public function query_player_internal_block_status(){
        if(!$this->initApi(true)){
            return false;
        }

        $username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }
        $this->load->model(['game_provider_auth']);
        $isBlocked=$this->game_provider_auth->isBlockedUsernameInDB($this->player_id, $this->game_platform_id);

        $this->utils->debug_log('call isBlockedUsernameInDB by '.$username.' isBlocked', $isBlocked);

        $detail=['blocked'=>$isBlocked];
        return $this->returnSuccess($detail);
    }

	public function kick_out_game(){
		if(!$this->initApi(true)){
			return false;
		}

		$username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

		$rlt=$this->game_api->logout($username);
		if($rlt && $rlt['success']){
			$detail=['kicked'=>true];
            $this->appendApiLogs($detail);
			return $this->returnSuccess($detail);
		}else{
            $this->appendApiLogs($rlt);
			return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
		}

	}

	public function query_player_online_status(){
		if(!$this->initApi(true)){
			return false;
		}

		$username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

		$rlt=$this->game_api->checkLoginStatus($username);
		if($rlt && $rlt['success']){
			$detail=['is_online'=>!!@$rlt['is_online']];
            $this->appendApiLogs($detail);
			return $this->returnSuccess($detail);
		}else{
            $this->appendApiLogs($rlt);
			return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
		}

	}

    /**
     *
     * {
     * “game_unique_code”: “xxxx”,
     * “language”: “”,
     * “mode”: “real”,
     * “platform”: “pc/mobile”,
     * “extra”: {}
     * }
     *
     * @return bool|void
     */
	public function query_game_launcher(){
		if(!$this->initApi(true)){
			return false;
		}

		$username=$this->username;
		$merchant_code = $this->getParam('merchant_code');
		$launcher_settings=$this->getParam('launcher_settings');
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }
        if(empty($launcher_settings)){
            return $this->returnError(self::CODE_INVALID_LAUNCHER_SETTINGS);
        }
        if(empty($this->game_api)){
            return $this->returnError(self::CODE_INVALID_GAME_PLATFORM_ID);
        }

        $check_incomplete_then_redirect = $this->getParam('check_incomplete_then_redirect', false);

        if($check_incomplete_then_redirect){
            //check incomplete first
            $gameManager=$this->utils->loadGameManager();
            $incompleteRlt=$gameManager->forwardToIncompleteGameLink($username, $launcher_settings);
            $this->utils->debug_log('forwardToIncompleteGameLink ', $username, $launcher_settings, $incompleteRlt);
            if($incompleteRlt['success'] && !empty($incompleteRlt['url'])){
                $detail=['launcher'=>$incompleteRlt];
                return $this->returnSuccess($detail);
            }
            //ignore
        }

		$rlt=$this->game_api->getGotoUrl($username, $launcher_settings, $merchant_code);
		if($rlt && $rlt['success']){
            $requstApi='query_game_launcher';
            $returnJson=['getGotoUrl'=>$rlt];
            $this->saveGamegatewayResponseResult($requstApi, $returnJson);

			$detail=['launcher'=>$rlt];
            $this->appendApiLogs($detail);
			return $this->returnSuccess($detail);
		}else{
            $this->appendApiLogs($rlt);
			return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
		}

	}

    /**
     * @param string $username
     * @param string $game_category hb
     * @return bool
     */
    public function query_incomplete_games(){
        if(!$this->initApi(true)){
            return false;
        }

        $username=$this->username;
        $game_category = $this->getParam('game_category');
        if(!in_array($game_category, self::GAME_CATEGORY_VALID_VALUE)){
            return $this->returnError(self::CODE_INVALID_GAME_CATEGORY);
        }
        $this->load->model(['original_game_logs_model', 'game_provider_auth']);
        $rows=null;
        switch ($game_category) {
            case 'hb':
                $rows=$this->original_game_logs_model->queryHBIncompleteGameList($username);
                if(!empty($rows)){
                    $currencyAppend='';
                    if($this->utils->isEnabledMDB()){
                        $key=$this->utils->getActiveCurrencyKeyOnMDB();
                        $currencyAppend='?'.Multiple_db::__OG_TARGET_DB.'='.$key;
                    }

                    //process url
                    foreach ($rows as &$row) {
                        $row['launcher_url']=null;
                        $launcher_settings=['game_unique_code'=>$row['game_key_name'],
                            'mode'=>'real', 'try_get_real_url'=>false];
                        $game_platform_id=$row['game_platform_id'];
                        $api=$this->utils->loadExternalSystemLibObject($game_platform_id);
                        if(!empty($api)){
                            //get real username
                            $playerUsername=$this->game_provider_auth->getUsernameByGameUsername($row['username'], $game_platform_id);
                            if(!empty($playerUsername)){
                                //try get launcher url
                                $urlResult=$api->getGotoUrl($playerUsername,
                                    $launcher_settings, $this->merchant_code);
                                if(!empty($urlResult) && isset($urlResult['success'])
                                    && !empty($urlResult['url']) && $urlResult['success']){
                                    $row['launcher_url']=$urlResult['url'];
                                    if($this->utils->isEnabledMDB()){
                                        //append currency
                                        $row['launcher_url']=$row['launcher_url'].$currencyAppend;
                                    }
                                }
                            }
                        }
                    }
                }
                break;
        }
        $detail=['incomplete_game_list'=>$rows];
        return $this->returnSuccess($detail);
    }

	public function query_player_game_settings(){
		if(!$this->initApi(true)){
			return false;
		}

        $username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

		return $this->returnUnimplemented();

	}

	public function update_player_game_settings(){
		if(!$this->initApi(true)){
			return false;
		}

        $username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

        return $this->returnUnimplemented();

	}

	public function query_player_bet_limit(){
		if(!$this->initApi(true)){
			return false;
		}

        $username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

        return $this->returnUnimplemented();

	}

	public function update_player_bet_limit(){
		if(!$this->initApi(true)){
			return false;
		}

        $username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

        return $this->returnUnimplemented();

	}

    public function query_simple_player_report(){
        if(!$this->initApi(true)){
            return false;
        }

        // $username=$this->username;
        $game_platform_id=$this->game_platform_id;
        //from agent
        $gamePlatformIdArray=$this->actived_game;
        if(empty($gamePlatformIdArray)){
            return $this->returnError(self::CODE_INVALID_FROM_DATE_TIME);
        }
        $size_per_page=$this->getParam('size_per_page');
        $page_number=$this->getParam('page_number');

        $from_date=$this->utils->checkAndFormatDate($this->getParam('from_date'));
        $to_date=$this->utils->checkAndFormatDate($this->getParam('to_date'));
        if(empty($from_date)){
            return $this->returnError(self::CODE_INVALID_FROM_DATE_TIME);
        }
        if(empty($to_date)){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }
        $this->utils->debug_log('query_simple_player_report', $from_date, $to_date, $size_per_page, $page_number);
        if($from_date>$to_date){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }
        //max days is 31
        $fd=new DateTime($from_date);
        $fd->modify('+31 days');
        $maxToDate=$fd->format('Y-m-d');
        $this->utils->debug_log('query_simple_player_report compare max to date', $to_date, $maxToDate);
        if($to_date>$maxToDate){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }
        if(empty($size_per_page) || !is_numeric($size_per_page) || $size_per_page<self::MIN_SIZE_PER_PAGE || $size_per_page>self::MAX_SIZE_PER_PAGE ){
            return $this->returnError(self::CODE_INVALID_SIZE_PRE_PAGE);
        }
        if(empty($page_number) || !is_numeric($page_number) || $page_number<1){
            return $this->returnError(self::CODE_INVALID_PAGE_NUMBER);
        }
        $this->load->model(['game_logs', 'player_model']);
        $playerId=$this->player_id;
        $agentId=$this->agent_obj['agent_id'];
        // if(!empty($username)){
        //     $playerId=$this->player_model->getPlayerIdByPlayerName($username);
        //     if(!empty($playerId)){
        //         $playerId=intval($playerId);
        //     }
        // }
        // public function queryPlayerGameReportPagination($from_date, $to_date, array $gamePlatformIdArray,
        //     $page_number, $size_per_page, $playerId=null, $game_platform_id=null){
        list($rows, $total_pages, $current_page, $total_rows_current_page, $sqlInfo)=$this->game_logs->queryPlayerGameReportPagination(
            $from_date, $to_date, $gamePlatformIdArray, $page_number, $size_per_page, $playerId, $game_platform_id, $agentId);

        $detail=['report'=>$rows, 'total_pages'=>$total_pages, 'current_page'=>$current_page,
            'total_rows_current_page'=>$total_rows_current_page];
        $requstApi='query_simple_player_report';
        $returnJson=['page_number'=>$page_number, 'size_per_page'=>$size_per_page,
            'total_pages'=>$total_pages, 'current_page'=>$current_page, 'total_rows_current_page'=>$total_rows_current_page,
            'sqlInfo'=>$sqlInfo];
        $this->saveGamegatewayResponseResult($requstApi, $returnJson);

        $this->returnSuccess($detail);
        //free
        unset($rows);
        unset($detail);
        return true;
    }

	public function transfer_player_fund(){
		if(!$this->initApi(true)){
			return false;
		}

		$username=$this->username;
		$action_type=$this->getParam('action_type');
		$amount=$this->getParam('amount');
		$external_trans_id=$this->getParam('external_trans_id');

		$this->load->model(['wallet_model']);

        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }
        if(empty($action_type)){
            return $this->returnError(self::CODE_INVALID_ACTION_TYPE);
        }
        if(empty($amount) || !is_numeric($amount)){
            return $this->returnError(self::CODE_INVALID_AMOUNT);
        }

        $rlt=null;
		if(!in_array($action_type,['deposit', 'withdraw'])) {
            return $this->returnError(self::CODE_INVALID_ACTION_TYPE);
        }
        $withdraw_all_amount=$this->getParam('withdraw_all_amount', false);
        $game_platform_id=$this->getParam('game_platform_id');
        $user_id=1;

        if($action_type=='deposit'){
            $transfer_from=Wallet_model::MAIN_WALLET_ID;
            $transfer_to=$game_platform_id;
        }else{
            //withdraw
            $transfer_from=$game_platform_id;
            $transfer_to=Wallet_model::MAIN_WALLET_ID;
        }

        //add balance to main wallet
        $player_id=$this->player_id; //$this->player_model->getPlayerIdByUsername($username);

        $this->utils->debug_log('call transferWallet', $player_id, $username, $transfer_from, $transfer_to, $amount,
            $user_id, $external_trans_id, 'withdraw_all_amount', $withdraw_all_amount);
        //update balance from api
		$balRlt=$this->game_api->queryPlayerBalance($username);
        if ($balRlt['success'] && isset($balRlt['balance'])) {
            $subwalletBalance=$balRlt['balance'];
    		$this->wallet_model->lockAndTransForPlayerBalance($player_id, function ()
                use ($game_platform_id, $username, $player_id, $subwalletBalance){
                return $this->wallet_model->updateSubWalletByPlayerId($player_id, $game_platform_id, $subwalletBalance);
    		});
        }

		$ignore_promotion_check = true;
        $isLockFailed=false;
        $rlt=$this->transferWallet($player_id, $username, $transfer_from, $transfer_to, $amount,
                $user_id, $external_trans_id, $ignore_promotion_check, $withdraw_all_amount, $isLockFailed);

		if(@$rlt['success']){

			$responseResultId = isset($rlt['response_result_id']) ? $rlt['response_result_id'] : null;
			$external_transaction_id = isset($rlt['external_transaction_id']) ? $rlt['external_transaction_id'] : $external_trans_id;
			$transfer_status = isset($rlt['transfer_status']) ? $rlt['transfer_status'] : Abstract_game_api::COMMON_TRANSACTION_STATUS_UNKNOWN;
			$reason_id = isset($rlt['reason_id']) ? $rlt['reason_id'] : Abstract_game_api::REASON_UNKNOWN;

			$detail=['updated'=>true, 'transaction_id'=>$external_transaction_id, 'transfer_status'=>$transfer_status,
				'reason_id'=>$reason_id, 'response_result_id'=>$responseResultId];
            $this->appendApiLogs($detail);
			return $this->returnSuccess($detail);

		}else{
            $rlt['reason_id'] = isset($rlt['reason_id']) ? $rlt['reason_id'] : Abstract_game_api::REASON_UNKNOWN;
            $this->appendApiLogs($rlt);
            if($isLockFailed){
                return $this->returnError(self::CODE_LOCK_FAILED, null, $rlt);
            }else{
    			return $this->returnError(self::CODE_INTERNAL_ERROR, null, $rlt);
            }
		}

	}

    private function transferWallet($player_id, $playerName, $transfer_from, $transfer_to, $amount,
            $user_id, $external_trans_id, $ignore_promotion_check = false, $withdraw_all_amount=false, &$isLockFailed=false) {
        $this->load->model(array('wallet_model', 'http_request', 'common_token', 'transactions', 'response_result'));
        $api=$this->game_api;
        if (empty($player_id)) {
            $this->utils->error_log('player id is empty', $player_id);
            $result['success'] = false;
            $result['message'] = lang('notify.61');
            return $result;
        }

        if (empty($playerName)) {
            $playerName = $this->player_model->getUsernameById($player_id);
        }

        $token = $this->common_token->getPlayerToken($player_id);
        $this->utils->debug_log('create token first for callback', $player_id, $token);

        $transactionType = null;
        $gamePlatformId = null;
        // $lock_type = null;
        if ($transfer_to != Wallet_model::MAIN_WALLET_ID) {
            $gamePlatformId = $transfer_to;
            $transactionType = Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET;
            // 	$lock_type = 'main_to_sub';
        } else {
            $gamePlatformId = $transfer_from;
            $transactionType = Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET;
            // 	$lock_type = 'sub_to_main';
        }

        if($this->game_api->onlyTransferPositiveInteger()){
            $this->utils->debug_log('try print positive int on transfer to', $this->game_api->onlyTransferPositiveInteger(), $amount);

            $amount=intval($amount);
            # can't withdraw through bank if less than 0. because can't transfer cent
            # set success if amount is zero OGP-7151
        } else {
            // control by extra info round_transfer_amount. value(true|false)
            $amount = $this->game_api->formatAmountBeforeTransfer($amount);
        }

        #OGP-4898
        $amount = $this->game_api->convertTransactionAmount($amount);

        if($amount <= 0) {
            return [
                'success' => true,
                'message' => lang('notify.50'),
            ];
        }

        $transfer_secure_id = $external_trans_id;
        $requestId = $this->wallet_model->addTransferRequest($player_id, $transfer_from, $transfer_to, $amount, $user_id, $transfer_secure_id,$gamePlatformId);
        $respRlt = null;
        $respFileRlt=null;

		// $lock_type = self::LOCK_ACTION_BALANCE;

        $result = array('success' => false);

        //auto check username on game platform

//        $withdrawCheckMsg = null;
        $message = null;
        $self = $this;
        $add_prefix=true;
        $this->wallet_model->lockAndTransForPlayerBalance($player_id, function () use (
            $self, $player_id, $gamePlatformId, $transfer_from, $transfer_to, $amount, $playerName, $user_id,
            $transfer_secure_id, $transactionType, $ignore_promotion_check, $withdraw_all_amount,
            &$result, &$message, &$respRlt, &$respFileRlt) {

            $agent_id=$this->agent_obj['agent_id'];

            //round main/big wallet first
            // $self->wallet_model->roundMainWallet($player_id);

            $self->utils->debug_log('lockTransferSubwallet', $player_id, $gamePlatformId);
            if ($transactionType == Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET) {
                //check credit
                $available_credit= $this->agent_obj['available_credit'];

                if ($available_credit < $amount) {
                    $result['success']=false;
                    $result['message'] = lang('No enough credit');
                    return  $result['success'];
                }

                $result['success'] = !!$this->transactions->createDepositTransactionByAgent($player_id, $amount, $agent_id, null);
                if(! $result['success']){
                    $result['message'] = lang('Create Deposit Transaction Failed');
                    return  $result['success'];
                }

                $result['success'] = !!$this->transactions->createAgentToPlayerTransaction($agent_id, $player_id, $amount, 'on player deposit');
                if(! $result['success']){
                    $result['message'] = lang('Create Agent to Player Transaction Failed');
                    return  $result['success'];
                }

            }else{
                //withdraw
                if($withdraw_all_amount){
                    //load it from subwallet
                    $amount=$this->wallet_model->getSubWalletTotalOnBigWalletByPlayer($player_id, $gamePlatformId);
                }
            }

            $result = $self->utils->transferWalletWithoutLock($player_id, $playerName, $gamePlatformId,
                $transfer_from, $transfer_to, $amount, $user_id, null, null,
                $transfer_secure_id, $ignore_promotion_check,  null, null, $withdraw_all_amount);

            if ($result['success']) {

                $self->utils->debug_log('transfer success', 'player_id', $player_id, 'playerName', $playerName, $transfer_from, $transfer_to, $amount);

                if ($result['success']) {
                    $result['success'] = true;
                    $result['message'] = !empty($result['message']) ? lang('notify.50') . ' ' . $result['message'] : lang('notify.50');
                } else {
                    $result['success'] = false;
                    $result['message'] = lang('notify.61');
                }

				if ($transactionType == Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET) {

	                //check credit
	//                $available_credit= $this->agent_obj['available_credit'];
	//
	//                if ($available_credit < $amount) {
	//                    $result['success']=false;
	//                    $result['message'] = lang('No enough credit');
	//                    return  $result['success'];
	//                }

	                $result['success']=!!$this->transactions->createWithdrawTransactionByAgent($player_id, $amount, $agent_id, null);
	                if(!$result['success']){
	                    $result['message'] = lang('Create Withdrawal Transaction Failed');
	                    return $result['success'];
	                }

	                $result['success']=!!$this->transactions->createPlayerToAgentTransaction($agent_id, $player_id, $amount, 'on player withdraw');
	                if(!$result['success']){
	                    $result['message'] = lang('Create Player to Agent Transaction Failed');
	                    return $result['success'];
	                }

	            }

            } else {
                $self->utils->error_log('transfer failed', $result, 'player_id', $player_id, 'playerName', $playerName, $transfer_from, $transfer_to, $amount);

                if (isset($result['response_result_id']) && !empty($result['response_result_id'])) {
                    //read response results
                    $respRlt = $self->response_result->getResponseResultById($result['response_result_id']);
                    $respFileRlt = $self->response_result->getResponseResultFileByResultId($result['response_result_id']);
                    $self->utils->debug_log('load failed response', $respRlt);
                }
                //should rollback but keep response result

                $result['success'] = false;
                $result['message'] = empty($result['message']) ? lang('notify.61') : $result['message'];
            }

            return $result['success'];

        }, $add_prefix, $isLockFailed);

		if (!empty($respRlt)) {
			//create response results again
			$result['response_result_id'] = $this->response_result->copyResult($respRlt);
			$this->response_result->copyResultFile($respFileRlt);
			$this->utils->debug_log('write back result file', $respFileRlt);
		} elseif (isset($result['success']) && !$result['success']) {
			$this->utils->error_log('lost response result', $player_id, $playerName, $transfer_from, $transfer_to, $amount, $user_id);
		}

		// $result['extra_message'] = $withdrawCheckMsg;

		$responseResultId = isset($result['response_result_id']) ? $result['response_result_id'] : null;
		$external_transaction_id = isset($result['external_transaction_id']) ? $result['external_transaction_id'] : null;
		$transfer_status = isset($result['transfer_status']) ? $result['transfer_status'] : Abstract_game_api::COMMON_TRANSACTION_STATUS_UNKNOWN;
		$reason_id = isset($result['reason_id']) ? $result['reason_id'] : Abstract_game_api::REASON_UNKNOWN;

		if (@$result['success']) {

			if($transactionType == Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET){
				// save http_request (cookies, referer, user-agent)
                $self->saveHttpRequest($player_id, Http_request::TYPE_MAIN_WALLET_TO_SUB_WALLET);
			}
			else{
				// save http_request (cookies, referer, user-agent)
	            $self->saveHttpRequest($player_id, Http_request::TYPE_SUB_WALLET_TO_MAIN_WALLET);
			}

			$this->wallet_model->setSuccessToTransferReqeust($requestId, $responseResultId, $external_transaction_id, $transfer_status, $reason_id);
		} else {
			$this->wallet_model->setFailedToTransferReqeust($requestId, $responseResultId, $external_transaction_id, $transfer_status, $reason_id);
		}

        return $result;
    }


	public function query_transaction(){
		if(!$this->initApi(true)){
			return false;
		}

		$external_trans_id=$this->getParam('external_trans_id');
        if(empty($external_trans_id)){
            return $this->returnError(self::CODE_INVALID_EXTERNAL_TRANS_ID);
        }
        $username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }
        //validate transfer request
        $this->load->model(['wallet_model', 'player_model']);

        $transferRequest=$this->wallet_model->getTransferRequestByExternalTransactionIdAndExternalSystemId($external_trans_id, $this->game_platform_id);
        $playerId=$this->player_id; // $this->player_model->getPlayerIdByUsername($username);
		$transfer_method=$transferRequest['from_wallet_type_id']==0 ? 'deposit' : 'withdrawal';

		$extra = [
            'playerName'=>$username,
            'playerId'=>$playerId,
            'transfer_method'=>$transfer_method,
            'transfer_time'=>$transferRequest['created_at'],
            'secure_id'=>$transferRequest['secure_id'],
            'amount' => $transferRequest['amount'],
            'transfer_updated_at' => $transferRequest['updated_at'],
        ];

		$rlt=$this->game_api->queryTransaction($external_trans_id, $extra);

		if($rlt && $rlt['success']){
			$detail=['status'=>@$rlt['status'], 'original_reason'=>$rlt];
            $this->appendApiLogs($detail);
			return $this->returnSuccess($detail);
		}else{
			$this->utils->error_log('query transaction', $rlt);
            $this->appendApiLogs($rlt);
			return $this->returnError(self::CODE_INTERNAL_ERROR, null, $rlt);
		}

	}

	public function query_transaction_history(){
		if(!$this->initApi()){
			return false;
		}
        $from=$this->getParam('from');
        $to=$this->getParam('to');
        $page_number=$this->getParam('page_number'); // start from 1
        $username=$this->username;
        $size_per_page=$this->getParam('size_per_page', self::DEFAULT_SIZE_PRE_PAGE);
        $playerId=$this->player_id;

        if(empty($from)){
            return $this->returnError(self::CODE_INVALID_FROM_DATE_TIME);
        }
        if(empty($to)){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }
        if(empty($size_per_page) || !is_numeric($size_per_page) || $size_per_page<self::MIN_SIZE_PER_PAGE || $size_per_page>self::MAX_SIZE_PER_PAGE ){
            return $this->returnError(self::CODE_INVALID_SIZE_PRE_PAGE);
        }
        if(empty($page_number) || !is_numeric($page_number)){
            return $this->returnError(self::CODE_INVALID_PAGE_NUMBER);
        }

        // if(!empty($username)){
        //     $playerId=$this->player_model->getPlayerIdByPlayerName($username);
        // }

        $this->load->model(['transactions', 'player_model']);
        list($rows, $total_pages, $current_page, $total_rows_current_page)=$this->transactions->queryPagination(
            $from, $to, $this->game_platform_id, $playerId, $page_number, $size_per_page, $this->agent_obj);

        $detail=['transaction_history'=>$rows, 'total_pages'=>$total_pages, 'current_page'=>$current_page,
            'total_rows_current_page'=>$total_rows_current_page];
        return $this->returnSuccess($detail);

	}

	public function query_player_balance(){
		if(!$this->initApi(true)){
			return false;
		}

		$this->load->model(array('wallet_model', 'player_model'));

		$username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

		$rlt=$this->game_api->queryPlayerBalance($username);
		if($rlt && $rlt['success']){

			$detail=['game_platform_balance'=>$rlt['balance']];

			$game_platform_id=$this->getParam('game_platform_id');
			$player_id=$this->player_id; //$this->player_model->getPlayerIdByUsername($username);

            $add_prefix=true;
            $isLockFailed=false;
			//update balance
			$success=$this->wallet_model->lockAndTransForPlayerBalance($player_id, function () use ($game_platform_id, $rlt, $username, $player_id){

				return $this->wallet_model->updateSubWalletsOnBigWallet($player_id, [$game_platform_id=>$rlt]);

			}, $add_prefix, $isLockFailed);
            $this->utils->debug_log('update subwallet', $success, $isLockFailed);

            $this->appendApiLogs($detail);
			return $this->returnSuccess($detail);
		}else{
            $this->appendApiLogs($rlt);
			return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
		}
	}

	public function query_game_history(){
		if(!$this->initApi()){
			return false;
		}

		//search it from game logs
		$from=$this->getParam('from');
		$to=$this->getParam('to');
		$page_number=$this->getParam('page_number'); // start from 1
		$username=$this->username;
		$game_status=$this->getParam('game_status',self::DEFAULT_GAME_STATUS_FILTER);
		$date_mode=$this->getParam('date_mode',self::DEFAULT_DATE_MODE_FILTER);
		$size_per_page=$this->getParam('size_per_page', self::DEFAULT_SIZE_PRE_PAGE);

        $from=$this->utils->checkAndFormatDateTime($from);
        $to=$this->utils->checkAndFormatDateTime($to);
        if(empty($from)){
            return $this->returnError(self::CODE_INVALID_FROM_DATE_TIME);
        }
        if(empty($to)){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }
        $this->utils->debug_log('query_game_history', $from, $to);
        if($from>$to){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }
        //max is 2 hours
        $fd=new DateTime($from);
        $fd->modify('+2 hours');
        $maxToDate=$fd->format('Y-m-d H:i:s');
        $this->utils->debug_log('query_game_history compare max to date', $to, $maxToDate);
        if($to>$maxToDate){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }
        if(empty($size_per_page) || !is_numeric($size_per_page) || $size_per_page<self::MIN_SIZE_PER_PAGE || $size_per_page>self::MAX_SIZE_PER_PAGE ){
            return $this->returnError(self::CODE_INVALID_SIZE_PRE_PAGE);
        }
        if(empty($page_number) || !is_numeric($page_number) || $page_number<1){
            return $this->returnError(self::CODE_INVALID_PAGE_NUMBER);
        }
        if(!in_array($game_status, self::GAME_STATUS_VALID_VALUE)){
        	return $this->returnError(self::CODE_INVALID_GAME_STATUS);
        }
        if(!in_array($date_mode, self::DATE_MODE_VALID_VALUE)){
        	return $this->returnError(self::CODE_INVALID_DATE_MODE);
        }

		$this->load->model(['game_logs', 'player_model']);

		$playerId=$this->player_id;
		// if(!empty($username)){
		// 	$playerId=$this->player_model->getPlayerIdByPlayerName($username);
		// }

		$query_table = $game_status==self::DEFAULT_GAME_STATUS_FILTER?'game_logs':'game_logs_unsettle';

		list($rows, $total_pages, $current_page, $total_rows_current_page)=$this->game_logs->queryPagination($from, $to, $this->game_platform_id,
			$playerId, $page_number, $size_per_page, $this->agent_obj, $game_status, $query_table, $date_mode);

		$detail=['game_history'=>$rows, 'total_pages'=>$total_pages, 'current_page'=>$current_page,
			'total_rows_current_page'=>$total_rows_current_page];

		$this->returnSuccess($detail);
		//free
		unset($detail);
		unset($rows);
		return true;
	}

	public function total_game_history(){
		if(!$this->initApi()){
			return false;
		}

		//search it from game logs
		$from=$this->getParam('from');
		$to=$this->getParam('to');
		$page_number=$this->getParam('page_number'); // start from 1
		$username=$this->username;
		$size_per_page=$this->getParam('size_per_page', self::DEFAULT_SIZE_PRE_PAGE);
		$total_type=$this->getParam('total_type');

        $from=$this->utils->checkAndFormatDateTime($from);
        $to=$this->utils->checkAndFormatDateTime($to);
        if(empty($from)){
            return $this->returnError(self::CODE_INVALID_FROM_DATE_TIME);
        }
        if(empty($to)){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }
        if($from>$to){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }
        if(!empty($size_per_page) && !is_numeric($size_per_page)){
            return $this->returnError(self::CODE_INVALID_SIZE_PRE_PAGE);
        }
        if(empty($page_number) || !is_numeric($page_number)){
            return $this->returnError(self::CODE_INVALID_PAGE_NUMBER);
        }

        $this->load->model(['game_logs', 'player_model']);
		$playerId=$this->player_id;
		// if(!empty($username)){
		// 	$playerId=$this->player_model->getPlayerIdByPlayerName($username);
		// }

		if(!in_array($total_type, ['minute', 'hourly', 'daily', 'monthly', 'yearly'])){
			//wrong type
			return $this->returnError(self::CODE_INVALID_TOTAL_TYPE);
		}

		list($rows, $total_pages, $current_page, $total_rows_current_page)=$this->game_logs->queryTotalPagination(
            $total_type, $from, $to, $this->game_platform_id, $playerId, $page_number, $size_per_page, $this->agent_obj);

		$detail=['game_history'=>$rows, 'total_pages'=>$total_pages, 'current_page'=>$current_page,
			'total_rows_current_page'=>$total_rows_current_page];
		return $this->returnSuccess($detail);

	}

	public function query_game_result(){
        if(!$this->initApi(true)){
            return false;
        }

        $this->load->model(array('wallet_model', 'player_model'));

        $game_code=$this->getParam('game_code');
        if(empty($game_code)){
            return $this->returnError(self::CODE_INVALID_GAME_CODE);
        }
        $bet_code=$this->getParam('bet_code');
        if(empty($bet_code)){
            return $this->returnError(self::CODE_INVALID_BET_CODE);
        }
        $extra=$this->getParam('extra');

        $rlt=$this->game_api->queryGameResult($game_code, $bet_code, $extra);
        if($rlt && $rlt['success']){
            $detail=['game_status'=>$rlt['game_status'], 'original_result'=>$rlt['original_result']];
            return $this->returnSuccess($detail);
        }else{
            return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
        }
	}

	public function query_multiple_platform_game_history(){
		if(!$this->initApi()){
			return false;
		}

		//search it from game logs
		$from=$this->getParam('from');
		$to=$this->getParam('to');
		$page_number=$this->getParam('page_number'); // start from 1
		$username=$this->username;
		$game_history_status=$this->getParam('game_history_status',self::GAME_HISTORY_STATUS_NORMAL);
		$date_mode=$this->getParam('date_mode',self::DATE_MODE_BY_LAST_UPDATE_TIME);
		$size_per_page=$this->getParam('size_per_page', self::DEFAULT_SIZE_PRE_PAGE);

        $from=$this->utils->checkAndFormatDateTime($from);
        $to=$this->utils->checkAndFormatDateTime($to);
        if(empty($from)){
            return $this->returnError(self::CODE_INVALID_FROM_DATE_TIME);
        }
        if(empty($to)){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }
        // $this->utils->debug_log('get from param', $from, $to);
        $this->utils->debug_log('query_multiple_platform_game_history', $from, $to);
        if($from>$to){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }
        //max is 2 hours
        $fd=new DateTime($from);
        $fd->modify('+2 hours');
        $maxToDate=$fd->format('Y-m-d H:i:s');
        $this->utils->debug_log('query_multiple_platform_game_history compare max to date', $to, $maxToDate);
        if($to>$maxToDate){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }
        if(empty($size_per_page) || !is_numeric($size_per_page) || $size_per_page<self::MIN_SIZE_PER_PAGE || $size_per_page>self::MAX_SIZE_PER_PAGE ){
            return $this->returnError(self::CODE_INVALID_SIZE_PRE_PAGE);
        }
        if(empty($page_number) || !is_numeric($page_number) || $page_number<1){
            return $this->returnError(self::CODE_INVALID_PAGE_NUMBER);
        }
        if(!in_array($game_history_status, self::GAME_HISTORY_STATUS_VALID_VALUE)){
        	return $this->returnError(self::CODE_INVALID_GAME_HISTORY_STATUS);
        }
        if(!in_array($date_mode, self::DATE_MODE_VALID_VALUE)){
        	return $this->returnError(self::CODE_INVALID_DATE_MODE);
        }
        $this->utils->debug_log('multiple_game_platform', $this->multiple_game_platform);
        if(empty($this->multiple_game_platform) || !is_array($this->multiple_game_platform)){
        	return $this->returnError(self::CODE_INVALID_MULTIPLE_GAME_PLATFORM);
        }

		$this->load->model(['game_logs', 'player_model']);
		$playerId=$this->player_id;
		// if(!empty($username)){
		// 	$playerId=$this->player_model->getPlayerIdByPlayerName($username);
		// }

		$query_table = $game_history_status==self::GAME_HISTORY_STATUS_NORMAL ? 'game_logs' : 'game_logs_unsettle';

		// $rows=[]; $total_pages=0; $current_page=0; $total_rows_current_page=0;
		$sql=null;$countSql=null;
        list($rows, $total_pages, $current_page, $total_rows_current_page)=
			$this->game_logs->queryMultiplePagination($from, $to, $this->multiple_game_platform, $playerId,
			$page_number, $size_per_page, $this->agent_obj, $game_history_status, $query_table, $date_mode,
            $sql, $countSql
        );

		$detail=['game_history'=>$rows, 'total_pages'=>$total_pages, 'current_page'=>$current_page,
			'total_rows_current_page'=>$total_rows_current_page];
        $requstApi='query_multiple_platform_game_history';
        $returnJson=['page_number'=>$page_number, 'size_per_page'=>$size_per_page,
            'sql'=>$sql, 'countSql'=>$countSql];
        $this->saveGamegatewayResponseResult($requstApi, $returnJson);

		$this->returnSuccess($detail);
		//free
		unset($rows);
		unset($detail);
		return true;
	}

    public function query_multiple_platform_stream_game_history(){
        if(!$this->initApi()){
            return false;
        }

        //search it from game logs
        $last_update_time=$this->getParam('last_update_time');
        $username=$this->username;
        $game_history_status=$this->getParam('game_history_status',self::GAME_HISTORY_STATUS_NORMAL);
        $min_size=$this->getParam('min_size', self::DEFAULT_MIN_SIZE_STREAM);

        $last_update_time=$this->utils->checkAndFormatDateTime($last_update_time);
        if(empty($last_update_time)){
            return $this->returnError(self::CODE_INVALID_FROM_DATE_TIME);
        }
        $gamegateway_stream_query_max_limit_seconds=$this->utils->getConfig('gamegateway_stream_query_max_limit_seconds');
        //less than now-30s
        $now=new DateTime();
        $now->modify('-'.$gamegateway_stream_query_max_limit_seconds.' seconds');
        $maxTime=$now->format('Y-m-d H:i:s');
        $this->utils->debug_log('query_multiple_platform_stream_game_history', $last_update_time, $maxTime);
        //validate last_update_time
        if($last_update_time>$maxTime){
            return $this->returnError(self::CODE_INVALID_FROM_DATE_TIME);
        }
        if(empty($min_size) || !is_numeric($min_size) || $min_size<self::MIN_SIZE_PER_PAGE || $min_size>self::MAX_SIZE_PER_PAGE ){
            return $this->returnError(self::CODE_INVALID_MIN_SIZE);
        }
        if(!in_array($game_history_status, self::GAME_HISTORY_STATUS_VALID_VALUE)){
            return $this->returnError(self::CODE_INVALID_GAME_HISTORY_STATUS);
        }
        $this->utils->debug_log('multiple_game_platform', $this->multiple_game_platform);
        if(empty($this->multiple_game_platform) || !is_array($this->multiple_game_platform)){
            return $this->returnError(self::CODE_INVALID_MULTIPLE_GAME_PLATFORM);
        }
        $max_minutes_limit=$this->getIntParam('max_minutes_limit');

        $this->load->model(['game_logs', 'player_model']);
        $playerId=$this->player_id;
        // if(!empty($username)){
        //     $playerId=$this->player_model->getPlayerIdByPlayerName($username);
        // }

        $query_table = $game_history_status==self::GAME_HISTORY_STATUS_NORMAL ? 'game_logs' : 'game_logs_unsettle';

        $sqlInfo=null;
        list($rows, $total_count, $last_datetime, $next_datetime)=
            $this->game_logs->queryStreamByStartTime($last_update_time, $this->multiple_game_platform, $playerId,
                $min_size, $this->agent_obj, $game_history_status, $query_table, $max_minutes_limit, $sqlInfo);

        $detail=['game_history'=>$rows, 'total_count'=>$total_count, 'last_datetime'=>$last_datetime, 'next_datetime'=>$next_datetime];
        $requstApi='query_multiple_platform_stream_game_history';
        $returnJson=['min_size'=>$min_size, 'total_count'=>$total_count,
            'last_datetime'=>$last_datetime, 'next_datetime'=>$next_datetime, 'sqlInfo'=>$sqlInfo];
        $this->saveGamegatewayResponseResult($requstApi, $returnJson);

        $this->returnSuccess($detail);
        //free
        unset($rows);
        unset($detail);
        return true;
    }

	public function sync_game_logs() {
		if(!$this->initApi(true)){
			return false;
		}

		$from= $this->getParam('from');
		$to= $this->getParam('to');
		$playerName =$this->username; //optional

		if(empty($from)){
			return $this->returnError(self::CODE_INVALID_FROM_DATE_TIME);
		}
		if(empty($to)){
			return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
		}
        if($from>$to){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }
        //max is 20 minutes
        $fd=new DateTime($from);
        $fd->modify('+20 minutes');
        $maxToDate=$fd->format('Y-m-d H:i:s');
        $this->utils->debug_log('sync_game_logs compare max to date', $to, $maxToDate);
        if($to>$maxToDate){
            return $this->returnError(self::CODE_INVALID_TO_DATE_TIME);
        }

		$this->utils->debug_log('=========start rebuild_single_game_by_timelimit============================',
				'gamePlatformId', $this->game_platform_id, 'fromDateTimeStr', $from, 'endDateTimeStr', $to);

		$mark = 'sync_game_logs';
		$this->utils->markProfilerStart($mark);

		$date_time_from = new \DateTime($from);
		$date_time_to = new \DateTime($to);

		$manager = $this->utils->loadGameManager();
		$rlt=$manager->syncOneGameRecords($this->game_platform_id, $date_time_from, $date_time_to, $playerName);

		$this->utils->markProfilerEndAndPrint($mark);
		$this->utils->debug_log('=========end gamegateway sync logs============================');

        $detail=['sync_result'=>$rlt];
        $this->appendApiLogs($detail);
        return $this->returnSuccess($detail);
	}

    public function player_bet(){
        if(!$this->initApi(true)){
            return false;
        }

        $this->load->model(array('wallet_model', 'player_model'));

        $username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }
        $amount=$this->getParam('amount');
        if(empty($amount) || !is_numeric($amount)){
            return $this->returnError(self::CODE_INVALID_AMOUNT);
        }
        $game_code=$this->getParam('game_code');
        if(empty($game_code)){
            return $this->returnError(self::CODE_INVALID_GAME_CODE);
        }
        $bet_details=$this->getParam('bet_details');

        $rlt=$this->game_api->playerBet($username, $amount, $game_code, $bet_details);

        if($rlt && $rlt['success']){
            $detail=['updated'=>$rlt['updated'], 'original_result'=>$rlt['original_result']];
            $this->appendApiLogs($detail);
            return $this->returnSuccess($detail);
        }else{
            $this->appendApiLogs($rlt);
            return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
        }
    }

    /**
     * @param string game_unique_code
     * @param string $game_type_unique_code
     * @return array game_list
     */
    public function query_game_list(){
        if(!$this->initApi(true)){
            return false;
        }

        $game_unique_code=$this->getParam('game_unique_code');
        $game_type_unique_code=$this->getParam('game_type_unique_code');

        $sqlInfo=null;
        $list=$this->game_api->queryGameListBy($game_type_unique_code, $game_unique_code, $sqlInfo);
        $requstApi='query_game_list';
        $this->saveGamegatewayResponseResult($requstApi, ['sqlInfo'=>$sqlInfo]);
        $detail=['game_list'=>$list];
        return $this->returnSuccess($detail);
    }

    /**
     * @param string $game_type_unique_code
     * @return array game_type_list
     */
    public function query_game_type_list(){
        if(!$this->initApi(true)){
            return false;
        }

        $game_type_unique_code=$this->getParam('game_type_unique_code');

        $sqlInfo=null;
        $list=$this->game_api->queryGameTypeList($game_type_unique_code, $sqlInfo);
        $requstApi='query_game_type_list';
        $this->saveGamegatewayResponseResult($requstApi, ['sqlInfo'=>$sqlInfo]);
        $detail=['game_type_list'=>$list];
        return $this->returnSuccess($detail);
    }

    public function query_game_list_from_game_provider(){
        if(!$this->initApi(true)){
            return false;
        }

        $rlt=$this->game_api->queryGameListFromGameProvider();
        $requstApi='query_game_list_from_game_provider';
        $this->saveGamegatewayResponseResult($requstApi, ['rlt'=>$rlt]);
        if($rlt && $rlt['success']){
            $detail=['game_list'=>$rlt['game_list'], 'game_type_list'=>$rlt['game_type_list']];
            return $this->returnSuccess($detail);
        }else{
            $this->appendApiLogs($rlt);
            return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
        }
    }

    public function update_game_list(){
        if(!$this->initApi(true)){
            return false;
        }

        if(!$this->utils->getConfig('allow_to_update_game_list_by_api')){
            return $this->returnError(self::CODE_NO_PERMISSION_TO_UPDATE_GAME_LIST);
        }
        $from_game_provider=$this->getBoolParam('from_game_provider');
        $game_list=null;
        $game_type_list=null;
        if($from_game_provider){
            $rlt=$this->game_api->queryGameListFromGameProvider();
            $this->appendApiLogs($rlt);
            if($rlt && $rlt['success']){
                //success
                $game_list=$rlt['game_list'];
                $game_type_list=$rlt['game_type_list'];
            }else{
                return $this->returnError(self::CODE_EXTERNAL_API_ERROR, null, $rlt);
            }
        }else{
            $game_list=$this->getParam('game_list');
            $game_type_list=$this->getParam('game_type_list');
        }
        if(empty($game_list)){
            return $this->returnError(self::CODE_INVALID_GAME_LIST);
        }
        if(empty($game_type_list)){
            return $this->returnError(self::CODE_INVALID_GAME_TYPE_LIST);
        }
        $requstApi='update_game_list';
        $this->saveGamegatewayResponseResult($requstApi, ['game_list'=>$game_list, 'game_type_list'=>$game_type_list]);
        //try update db
        $this->load->model(['game_type_model', 'game_description_model']);
        $syncGameTypeSucc=$this->game_type_model->syncFrom($this->game_platform_id, $game_type_list);
        $syncGameSucc=$this->game_description_model->syncFrom($this->game_platform_id, $game_list);
        return $this->returnSuccess(['game_type'=>$syncGameTypeSucc, 'game'=>$syncGameSucc]);
    }

    /**
     * @param string $init_game_platform_id
     * @return
     */
    public function try_setup_game_api(){

        if(!$this->initApi()){
            return false;
        }

        $allow_to_create_or_update_game_api_by_api=$this->utils->getConfig('allow_to_create_or_update_game_api_by_api');

        $detail=['done'=>true];
        $init_game_platform_id=$this->getParam('init_game_platform_id');
        if(empty($init_game_platform_id) || !is_numeric($init_game_platform_id)){
            $this->utils->debug_log('init game platform id failed', $init_game_platform_id);
            return $this->returnError(self::CODE_INVALID_INIT_GAME_PLATFORM_ID);
        }

        $init_game_platform_id=intval($init_game_platform_id);

        //check game platform id exists
        $existsGameAPI=array_key_exists($init_game_platform_id, $this->exists_game_api_list);
        $existsInAgency=in_array($init_game_platform_id, $this->actived_game);
        //permission
        if(!$allow_to_create_or_update_game_api_by_api){
            if($existsGameAPI && $existsInAgency){
                $this->returnSuccess($detail);
                return true;
            }else if($existsGameAPI && !$existsInAgency){
                $this->returnError(self::CODE_NO_PERMISSION_ON_GAME_PLATFORM);
                return false;
            }else{
                if(empty($this->actived_game)){
                    $this->returnError(self::CODE_NO_ACTIVE_GAME_PLATFORM_IN_AGENT);
                }else{
                    $this->returnError(self::CODE_NO_PERMISSION_TO_CREATE_GAME_API);
                }
                return false;
            }
        }

        $this->load->model(['external_system']);

        //create or update game api
        $extra_info=$this->getParam('extra_info');
        $sandbox_extra_info=$this->getParam('sandbox_extra_info');

        if(empty($extra_info) || !is_array($extra_info)){
            return $this->returnError(self::CODE_INVALID_EXTRA_INFO);
        }
        if(empty($sandbox_extra_info)){
            $sandbox_extra_info=$extra_info;
        }
        $extra_info=json_encode($extra_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if($extra_info===false){
            return $this->returnError(self::CODE_INVALID_EXTRA_INFO);
        }
        $sandbox_extra_info=json_encode($sandbox_extra_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if($sandbox_extra_info===false){
            return $this->returnError(self::CODE_INVALID_EXTRA_INFO);
        }

        $apiInfo=[
            'id'=>$init_game_platform_id,
            'live_mode'=>$this->getParam('live_mode', true) ? External_system::DB_TRUE : External_system::DB_FALSE,
            'live_url'=>$this->getParam('live_url'),
            'sandbox_url'=>$this->getParam('sandbox_url'),
            'live_key'=>$this->getParam('live_key'),
            'live_secret'=>$this->getParam('live_secret'),
            'sandbox_key'=>$this->getParam('sandbox_key'),
            'sandbox_secret'=>$this->getParam('sandbox_secret'),
            'second_url'=>$this->getParam('second_url'),
            'sandbox_account'=>$this->getParam('sandbox_account'),
            'live_account'=>$this->getParam('live_account'),
            'extra_info'=>$this->getParam('extra_info'),
            'sandbox_secret'=>$this->getParam('sandbox_secret'),
            'extra_info'=>$extra_info,
            'sandbox_extra_info'=>$sandbox_extra_info,

            'system_name'=>$this->getParam('system_name'),
            'system_code'=>$this->getParam('system_code'),
            'class_name'=>$this->getParam('class_name'),
            'local_path'=>$this->getParam('local_path'),

            'manager'=>$this->getParam('manager', 'game_platform_manager'),
            'game_platform_rate'=>$this->getParam('game_platform_rate', 100),
            'status'=>$this->getParam('status', External_system::DB_TRUE),

        ];

        $class_key=$this->getParam('class_key');
        $class_content=$this->getParam('class_content');
        $dynamicClassInfo=null;
        if(!empty($class_key) && !empty($class_content)){
            $dynamicClassInfo=[
                'class_name'=>$this->getParam('class_name'),
                'class_key'=>$this->getParam('class_key'),
                'class_content'=>$this->getParam('class_content'),
            ];
        }
        //setup
        $success=$this->external_system->addOrUpdateGameApi($apiInfo, $dynamicClassInfo);
        if(!$success){
            $this->returnError(self::CODE_INTERNAL_ERROR, 'create/update game api failed');
            return;
        }
        //free memory
        unset($dynamicClassInfo);

        if(!$existsInAgency){
            //add permission
            $success=$this->external_system->addGameAPIPermissionToAgent(
                $init_game_platform_id, $this->agent_id);
            if(!$success){
                $this->returnError(self::CODE_INTERNAL_ERROR, 'add game api permission to agent failed');
                return;
            }
        }

        $this->resetAllParam();

        return $this->returnSuccess($detail);
    }

    /**
     *
     * @return
     */
    public function try_setup_merchant(){

        $merchant_code=$this->getParam('merchant_code');
        $credit_limit=$this->getParam('credit_limit');
        $available_credit=$this->getParam('available_credit');

        $allow_to_create_or_update_agent_by_api=$this->utils->getConfig('allow_to_create_or_update_agent_by_api');
        $this->load->model(['agency_model']);

        if(!$allow_to_create_or_update_agent_by_api){
            //check agent name exists
            $agent_obj=$this->merchant=$this->agency_model->get_agent_by_name($merchant_code);
            if(!empty($agent_obj)){
                $result= ['merchant_info'=>$this->getMerchantInfo($agent_obj)]; //$this->agency_model->get_agent_by_id($agentId);
                return $this->returnSuccess($result);
            }else{
                return $this->returnFailed(self::CODE_NO_PERMISSION_TO_CREATE_MERCHANT);
            }
        }

        $agent_obj = $this->agency_model->syncMerchant($merchant_code,
            $credit_limit, $available_credit);

        if(!empty($agent_obj)){
            $result= ['merchant_info'=>$this->getMerchantInfo($agent_obj)]; //$this->agency_model->get_agent_by_id($agentId);
            return $this->returnSuccess($result);
        }else{
            return $this->returnError(self::CODE_CREATE_MERCHANT_FAILED);
        }

    }

    protected function getMerchantInfo($agent_obj){
        $info=['merchant_code'=>$agent_obj['agent_name']];
        if($agent_obj['live_mode']==Agency_model::DB_TRUE){
            $info['secure_key']=$agent_obj['live_secure_key'];
            $info['sign_key']=$agent_obj['live_sign_key'];
        }else{
            $info['secure_key']=$agent_obj['staging_secure_key'];
            $info['sign_key']=$agent_obj['staging_sign_key'];
        }
        return $info;
    }

    protected function appendApiLogs(&$detail){
        if($this->getBoolParam('_append_api_logs', false)){
            if(!empty($this->game_api)){
                $detail['api_logs']=$this->game_api->getInternalLogs();
            }
        }
    }

    //===========================================================

	/*
	* This api will create merchant in gamegateway
	*
	* Params: merchant_code,master_key,credit_limit,available_credit as str
	*/
	public function create_merchant(){
		$json = file_get_contents('php://input');
		$params = $this->utils->decodeJson($json);
		$merchant_code = isset($params['merchant_code']) ? $params['merchant_code'] : null;
		$master_key = isset($params['agent_master_key']) ? $params['agent_master_key'] : null;
		$credit_limit = isset($params['credit_limit']) ? $params['credit_limit'] : null;
		$available_credit = isset($params['available_credit']) ? $params['available_credit'] : null;
		$agent_prefix = isset($params['agent_prefix']) ? $params['agent_prefix'] : null;
		$game_apis = isset($params['game_apis']) ? $params['game_apis'] : null;

		if(!$this->utils->getConfig('agent_master_key')) return;

		if($master_key != $this->utils->getConfig('agent_master_key')){
			return $this->returnError(self::CODE_INVALID_MASTER_KEY);
		}

		if(!$credit_limit || !$available_credit){
			return $this->returnError(self::CODE_CREDIT_LIMIT_OR_AVAILABLE_LIMIT_IS_EMPTY);
		}

    $agent_name = $merchant_code;
    $merchant_name = $merchant_code;
    $password = "pw".$merchant_code;
		$game_apis_arr = explode(',', $game_apis);

    $this->load->model(['agency_model']);
		$agentId = $this->agency_model->createAgentWithMerchant($agent_name, $merchant_name, $password,$credit_limit,$available_credit);

		if($agentId){
			// update agency other vital fields
			$result['update_agent_creds_result'] = $this->agency_model->update_agent($agentId,['player_prefix'=>$agent_prefix,'vip_level'=>1,'live_mode'=>self::FLAG_TRUE]);

			// add agent game platforms
			$result['add_agent_game_apis_result'] = $this->add_agent_gameplatforms($agentId,$game_apis_arr);

			$result = $this->agency_model->get_agent_by_id($agentId);
			$this->returnSuccess($result);
		}else{
			return $this->returnError(self::CODE_CREATE_MERCHANT_FAILED);
		}
	}

	/*
	* This api will add game apis and game types from auto generated site agent
	*
	* Params: agentId as str
	*/
	public function add_agent_gameplatforms($agentId,$game_apis=null){
		$this->load->model(['agency_model','game_type_model']);

		# Default Auto Generated Site Agent Game Apis
		$gamePlatforms = [
                      ["agent_id" => $agentId,"game_platform_id" => PT_API],
                      ["agent_id" => $agentId,"game_platform_id" => MG_API],
                      ["agent_id" => $agentId,"game_platform_id" => BBIN_API],
											["agent_id" => $agentId,"game_platform_id" => AB_API],
											["agent_id" => $agentId,"game_platform_id" => GD_API],
                      ["agent_id" => $agentId,"game_platform_id" => HB_API],
											["agent_id" => $agentId,"game_platform_id" => QT_API],
											["agent_id" => $agentId,"game_platform_id" => TTG_API],
											["agent_id" => $agentId,"game_platform_id" => EBET_API],
                      ["agent_id" => $agentId,"game_platform_id" => ISB_API],
											["agent_id" => $agentId,"game_platform_id" => ONEWORKS_API],
                      ["agent_id" => $agentId,"game_platform_id" => FISHINGGAME_API],
											["agent_id" => $agentId,"game_platform_id" => AGIN_API],
											["agent_id" => $agentId,"game_platform_id" => EZUGI_API],
											["agent_id" => $agentId,"game_platform_id" => UC_API],
											["agent_id" => $agentId,"game_platform_id" => DT_API],
											["agent_id" => $agentId,"game_platform_id" => IDN_API],
                      ["agent_id" => $agentId,"game_platform_id" => SA_GAMING_API],
											["agent_id" => $agentId,"game_platform_id" => OG_API],
											["agent_id" => $agentId,"game_platform_id" => JUMB_GAMING_API],
											["agent_id" => $agentId,"game_platform_id" => SPADE_GAMING_API],
                      ["agent_id" => $agentId,"game_platform_id" => PRAGMATICPLAY_API],
                      ["agent_id" => $agentId,"game_platform_id" => PNG_API],
											["agent_id" => $agentId,"game_platform_id" => VR_API],
											["agent_id" => $agentId,"game_platform_id" => EVOLUTION_GAMING_API],
											["agent_id" => $agentId,"game_platform_id" => YOPLAY_API],
											["agent_id" => $agentId,"game_platform_id" => DG_API],
											["agent_id" => $agentId,"game_platform_id" => IPM_V2_SPORTS_API],
											["agent_id" => $agentId,"game_platform_id" => HG_API],
                      ["agent_id" => $agentId,"game_platform_id" => GGPOKER_GAME_API],
											["agent_id" => $agentId,"game_platform_id" => MWG_API],
											["agent_id" => $agentId,"game_platform_id" => SUNCITY_API],
											["agent_id" => $agentId,"game_platform_id" => FG_ENTAPLAY_API],
											["agent_id" => $agentId,"game_platform_id" => KYCARD_API],
											["agent_id" => $agentId,"game_platform_id" => CQ9_API],
											["agent_id" => $agentId,"game_platform_id" => TIANHAO_API],
											["agent_id" => $agentId,"game_platform_id" => RTG_MASTER_API],
											["agent_id" => $agentId,"game_platform_id" => LE_GAMING_API],
											["agent_id" => $agentId,"game_platform_id" => SBTECH_BTI_API],
											["agent_id" => $agentId,"game_platform_id" => LOTUS_API],
											["agent_id" => $agentId,"game_platform_id" => T1LOTTERY_EXT_API],
                    ];

		if($game_apis){
			$gamePlatforms = [];
			foreach ($game_apis as $key => $value) {
				array_push($gamePlatforms,['agent_id' => $agentId,'game_platform_id' => (int) $value]);
			}
		}

		$addAgenGamesResponse = $this->agency_model->addAgentGames($gamePlatforms);
		if($addAgenGamesResponse){
            $result["add_agent_game_platform_success"] = true;
			foreach ($gamePlatforms as $gp) {
				$game_types_list[] = $this->game_type_model->getGameTypeListByGamePlatformId($gp['game_platform_id']);
			}
		}else{
			$result["add_agent_game_platform_success"] = false;
		}

		# Add Auto Generated Site Agent Game Types
		$gameTypesArr = [];
		if(!empty($game_types_list)){
			foreach ($game_types_list as $gt) {
				if(!is_array($gt)) continue;
				array_walk($gt, function(&$val) use (&$gameTypesArr,$agentId){
				    $gameTypesArr[] = [
				    					'agent_id' => $agentId,
				    	 				'game_platform_id' => $val['game_platform_id'],
				    					'game_type_id' => $val['id']
				    				  ];
				});
			}
			$addAgentGameTyperesponse = $this->agency_model->addAgentGameTypes($gameTypesArr);
			$result["add_agent_game_type_success"] = $addAgentGameTyperesponse ? true : false;
		}
		return $result;
	}

	/*
	* This api will setup game apis in the client site
	*
	* Params: merchant_code,master_key,live_key,live_secret,
	*         sandbox_key,sandbox_secret,game_apis (ex: '1,2,6') as str
	*/
	public function setup_gameapi(){
		$json = file_get_contents('php://input');
		$params = $this->utils->decodeJson($json);

		$live_mode = isset($params['live_mode']) ? $params['live_mode'] : null;
		$merchant_code = isset($params['merchant_code']) ? $params['merchant_code'] : null;
		$master_key = isset($params['agent_master_key']) ? $params['agent_master_key'] : null;
		$live_key = isset($params['live_key']) ? $params['live_key'] : null;
		$live_secret = isset($params['live_secret']) ? $params['live_secret'] : null;
		$sandbox_key = isset($params['sandbox_key']) ? $params['sandbox_key'] : null;
		$sandbox_secret = isset($params['sandbox_secret']) ? $params['sandbox_secret'] : null;
		$game_apis = isset($params['game_apis']) ? $params['game_apis'] : null;
		$web_template_type = isset($params['web_template_type']) ? $params['web_template_type'] : null;
		$agent_prefix = isset($params['agent_prefix']) ? $params['agent_prefix'] : null;

		if(!$this->utils->getConfig('agent_master_key')) return;

		if($master_key != $this->utils->getConfig('agent_master_key')){
			return $this->returnError(self::CODE_INVALID_MASTER_KEY);
		}

		$this->load->model(['external_system']);

		// set login template
		$setup_login_template_response = $this->set_login_template();
		// set theme
		$setup_theme_response = $this->setup_theme($web_template_type);
		// delete cn news
		$delete_cn_news_response = $this->deleteDefaultCnNews();
		// update cms vesion
		$cms_version_update_response = $this->update_cms_version();
		// set default collection accounts
		$set_default_collection_account_response = $this->set_default_collection_account();
		// set default login and registration settings
		$set_login_and_registration_default_settings_response = $this->set_login_and_registration_default_settings();

		$gameApis = explode(",", $game_apis);
		$setup_game_response = array();
		foreach ($gameApis as $val) {
			$data = $this->external_system->getRowArrayFromPredefinedSystemById($val);
			$data['live_url'] = "http://admin.gamegateway.t1t.games";
			$data['sandbox_url'] = "http://admin.staging.gamegateway.t1t.games";
			$data['live_mode'] = $live_mode;
			$data['live_key'] = $live_key;
			$data['live_secret'] = $live_secret;
			$data['sandbox_key'] = $sandbox_key;
			$data['sandbox_secret'] = $sandbox_secret;
			$data['extra_info'] = "{\"api_merchant_code\":\"".$merchant_code."\",\"prefix_for_username\":\"".$agent_prefix."\"}";
			$data['sandbox_extra_info'] = "{\"api_merchant_code\":\"".$merchant_code."\",\"prefix_for_username\":\"".$agent_prefix."\"}";

			$result = $this->external_system->addRecord($data);
			if($result){
				$setup_game_response[] = [
										 "success" => true,
										 "game_api_code" => $val
									   ];
			}else{
				$setup_game_response[] = [
										 "success" => false,
										 "game_api_code" => $val
									   ];
			}
		}

		// disable dummy game api
		$dummyGameApiId = 9998;
		$disable_dummy_gameapi_response = $this->external_system->disableAbleGameApi(['status'=>self::FLAG_FALSE],$dummyGameApiId);

		// create demo player
		$create_demo_player = $this->createDemoPlayer();

		$response["success"] = true;
		$response["detail"] = [
								"setup_game_response" => $setup_game_response,
								"setup_login_template_response" => $setup_login_template_response,
								"setup_sysfeat_response" => $setup_sysfeat_response,
								"cms_version_update_response" => $cms_version_update_response,
								"delete_cn_news_response" => $delete_cn_news_response,
								"set_player_center_tutorial_Lang" => $set_player_center_tutorial_Lang,
								"disable_dummy_gameapi_response" => $disable_dummy_gameapi_response,
								"set_default_collection_account_response" => $set_default_collection_account_response,
								"set_login_and_registration_default_settings_response" => $set_login_and_registration_default_settings_response
							 ];

		$this->returnSuccess($response);
	}

	/*
	* This api will setup login template in the client site
	*
	* Params: login_template_type as str
	*/
	public function set_login_template($login_template_type="temp1"){
		$login_template = "";
		$logged_template = "";

		switch ($login_template_type) {
			case "temp1":
				$login_template = "<div class=\"hidden-xs is-desktop\">
    <a href=\"javascript: void(0);\" class=\"waves-effect btn red-bg\" onclick=\"window.location.href = window.location.protocol + '//' + _export_smartbackend.variables.hosts.player + '/iframe/auth/login'\">Login</a>
    <a href=\"javascript: void(0);\" class=\"waves-effect btn yellow-orange-bg\" onclick=\"window.location.href = window.location.protocol + '//' + _export_smartbackend.variables.hosts.player + '/player_center/iframe_register'\">Signup</a>
</div>

<div class=\"is-mobile\">
	<span class=\"btn-wrapper\">
		<button class=\"login_btn dark-red-bg white\" onclick=\"window.location.href = window.location.protocol + '//' + _export_smartbackend.variables.hosts.player + '/iframe/auth/login'\">Login</button>
		<button class=\"reg_btn\" onclick=\"window.location.href = window.location.protocol + '//' + _export_smartbackend.variables.hosts.player + '/player_center/iframe_register'\">Signup</button>
	</span>
</div>

<style>
  .is-desktop a {
      border-radius: 5px;
      height: 30px;
      line-height: 0 !important;
      text-transform: unset;
      color: #fff !important;
  }
</style>";

				$logged_template = "<div class=\"logged is-desktop\">
    <ul>
        <li class=\"user-acct-name\">
            <a href=\"/player_center/dashboard\" class=\"youname\"><%- playerName %></a>
            <div class=\"sub-menu\">
                <ul>
                    <li><a class=\"ui-btn ui-btn-logout fn-left\" href=\"/player_center/dashboard/index#accountInformation\" ><i class=\"fa fa-user-circle\" aria-hidden=\"true\"></i><%- langText.header_acct_info %></a></li>
                    <li><a class=\"ui-btn ui-btn-logout fn-left\" href=\"<%- ui.logoutUrl %>\" target=\"<%- ui.logoutIframeName %>\"><i class=\"fa fa-power-off\" aria-hidden=\"true\"></i><%- langText.button_logout %></a></li>
                </ul>
            </div>
        </li>
        <li class=\"_player_wallet_list\">
            <a href=\"javascript: void(0);\" class=\"_player_wallet_list_toggle\">
                <span class=\"_player_balance_span\"><strong><%- ui.total_balance %></strong></span>
            </a>
        </li>
    </ul>
</div>

<div class=\"is-mobile\">
    <div class=\"center-align\">
        <span class=\"player-username\">Welcome! <%- default_prefix_for_username+playerName %></span>
        <span class=\"balance\"><span class=\"_player_balance_span\">$0</span></span>
        <span class=\"btn-wrapper\">
            <button class=\"login_btn dark-red-bg white\" onclick=\"window.location.href = window.location.protocol + '//' + _export_smartbackend.variables.hosts.player\">Dashboard</button>
            <button class=\"reg_btn\" target=\"<%- ui.logoutIframeName %>\" onclick=\"window.location.href = '<%- ui.logoutUrl %>/1'\"><%- langText.button_logout %></button>
        </span>
    </div>
</div>
<iframe name=\"<%- ui.logoutIframeName %>\" id=\"<%- ui.logoutIframeName %>\" width=\"0\" height=\"0\" border=\"0\" style=\"display:none;border:0px;width:0px;height:0px;\"></iframe>

<script>
    $('.favoriteBtn').css('pointer-events','auto');
</script>
";
				break;

			case "temp2":
				$login_template = "";
				$logged_template = "";
				break;

			default:
				$login_template = null;
				$logged_template = null;
				break;
		}

		if(!$login_template && !$logged_template) return $this->returnError(array("success"=>false));

		$this->load->model(['static_site']);
		$data = [
				  "login_template" => $login_template,
				  "logged_template" => $logged_template
				];

		$result = $this->static_site->editStaticSiteBySiteName($data,"default");
		return array("success"=>true,"result"=>$result);
	}

	/*
	* This api will setup themes in the client site
	*/
	public function setup_theme($modelType="model1"){
		$this->load->model(['operatorglobalsettings']);

		$headerThemeStatus = false;
		$footerThemeStatus = false;
		$playerCenterThemeStatus = false;
		$playerCenterLanguage = false;
		$enLangVal = 1;

		if($this->operatorglobalsettings->syncSettingJson('player_center_header', $modelType.'-header', 'value')){
			$headerThemeStatus = true;
		}

		if($this->operatorglobalsettings->syncSettingJson('player_center_footer', $modelType.'-footer', 'value')){
			$footerThemeStatus = true;
		}

		if($this->operatorglobalsettings->syncSettingJson('player_center_theme', $modelType, 'value')){
			$playerCenterThemeStatus = true;
		}

		if($this->operatorglobalsettings->syncSettingJson("player_center_language", $enLangVal, 'value')){
			$playerCenterLanguage = true;
		}


		$result = [
					"success" => true,
					"header_theme_status" => $headerThemeStatus,
					"footer_theme_status" => $footerThemeStatus,
					"playercenter_theme_status" => $playerCenterThemeStatus
				  ];

		return array("success"=>true,"result"=>$result);
	}

	/*
	* This api will cms version in the client site
	*/
	public function update_cms_version($manualVersion="011"){
		$cms_version = "1.000.000.".$manualVersion;
		$res = $this->operatorglobalsettings->syncSettingJson("cms_version", $cms_version , 'value');

		if($res){
			$result = [
						"success" => true,
						"result" => $res,
					  ];
		}else{
			$result = [
						"success" => false,
						"result" => $res,
					  ];
		}
		return array("success"=>true,"result"=>$result);
	}

	public function deleteDefaultCnNews(){
		$this->load->model(['cms_model']);
		$newsItemIds = ['10','12','13'];
		$result = array();
		foreach ($newsItemIds as $newsId) {
			$result[] = $this->cms_model->deleteNews($newsId);
		}
		return array("success"=>true,"result"=>$result);
	}

	public function createDemoPlayer(){
		$this->load->model(['player_model']);
		$username = "osdemo";
		$player_data = array(
					'username' => $username,
					'gameName' => $username,
					'email' => $username."@gmail.com",
					'password' => $username,
					'secretQuestion' => "My Password",
					'secretAnswer' => $username,
					'verify' => $username,
					'withdraw_password' => $username,
					'firstName' => $username,
					'lastName' => $username,
					'language' => self::LANG_EN,
					'imAccount' => $username,
					'registrationIp' => $this->utils->getIP(),
					'verified_phone' => null,
					'newsletter_subscription' => null,
					'gender' => null,
					'birthdate' => null,
					'contactNumber' => null,
					'citizenship' => null,
					'imAccountType' => null,
					'imAccount2' => null,
					'imAccountType2' => null,
					'imAccount3' => null,
					'imAccountType3' => null,
					'birthplace' => null,
					'registrationWebsite' => null,
					'residentCountry' => null,
					'city' => null,
					'address' => null,
					'address2' => null,
					'address3' => null,
					'zipcode' => null,
					'dialing_code' => null,
					'id_card_number' => null,
				);

		$res = $this->player_model->register($player_data);

		return array("success" => true,"result" => $res);
	}

	public function set_default_collection_account(){
		$this->load->model(['operatorglobalsettings','marketing']);

		$payment_account_types = "{\"1\" : { \"lang_key\": \"pay.manual_online_payment\", \"enabled\": true},\"2\" : { \"lang_key\": \"pay.auto_online_payment\", \"enabled\": false},\"3\" : { \"lang_key\": \"pay.local_bank_offline\", \"enabled\": false}}";

		return $this->operatorglobalsettings->setPaymentAccountTypes($payment_account_types, true);
	}

	public function set_login_and_registration_default_settings(){
		$this->load->model(['operatorglobalsettings','marketing']);
		$result['enable_reg_captcha_response'] = $this->operatorglobalsettings->syncSettingJson("registration_captcha_enabled", self::FLAG_FALSE, 'value');
		$result['enable_login_captcha_response'] = $this->operatorglobalsettings->syncSettingJson("login_captcha_enabled", self::FLAG_FALSE, 'value');

		$lastName = 2;
		$contactNo = 8;
		$referralCode = 13;
		$affCode = 14;
		$smsVerification = 34;
		$withdrawPw = 35;
		$city = 36;
		$address = 37;
		$agencyCode = 46;
		$registrationFieldsIds = [
								  $lastName,
								  $contactNo,
								  $referralCode,
								  $affCode,
								  $smsVerification,
								  $withdrawPw,
								  $city,
								  $address,$agencyCode
								 ];

		$data = [
				  'visible' => '1',
				  'required' => '1',
				  'updatedOn' => date("Y-m-d H:i:s"),
				];
		foreach ($registrationFieldsIds as $value) {
			$result['reg_settings'][] = $this->marketing->saveRegistrationSettings($data,$value);
		}

		return $result;
	}

    public function query_bet_detail_link(){
        if(!$this->initApi(true)){
            return false;
        }

        $external_uniq_id=$this->getParam('bet_detail_id');
        if(empty($external_uniq_id)){
            return $this->returnError(self::CODE_INVALID_EXTERNAL_TRANS_ID);
        }
        $username=$this->username;
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }

        $rlt=$this->game_api->queryBetDetailLink($username, $external_uniq_id);

        if($rlt && $rlt['success']){
            $detail=['status'=>@$rlt['status'], 'original_reason'=>$rlt];
            $this->appendApiLogs($detail);
            return $this->returnSuccess($detail);
        }else{
            $this->utils->error_log('query bet detail link', $rlt);
            $this->appendApiLogs($rlt);
            return $this->returnError(self::CODE_INTERNAL_ERROR, null, $rlt);
        }

    }
}
