<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Player_library
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 *
 * @property PlayerCenterBaseController $CI
 */
class Player_library{
    /* @var Player */
    public $player;

    /* @var Player_model */
    public $player_model;

    /* @var Http_request */
    public $http_request;

    /* @var Player_login_token */
    public $player_login_token;

    /* @var Common_token */
    public $common_token;

    /* @var Utils */
    public $utils;

    public function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->model(['player', 'player_model', 'http_request', 'player_login_token', 'common_token']);
        $this->CI->load->helper('cookie');

        $this->player = $this->CI->player;
        $this->player_model = $this->CI->player_model;
        $this->http_request = $this->CI->http_request;
        $this->player_login_token = $this->CI->player_login_token;
        $this->common_token = $this->CI->common_token;

        $this->utils = $this->CI->utils;
    }

    protected function _check_player(&$result, $player_id){
        $isBlocked = $this->player_model->isBlocked($player_id);
        $isDeleted = $this->player_model->isDeleted($player_id);
        $isBlockedFailedLoginAttempt = $this->player_model->isBlockedFailedLoginAttempt($player_id);
        $isSelfExclusioned = $this->player_model->isSelfExclusion($player_id);
        $isCooloff = $this->player_model->isCooloff($player_id);

        if ($isBlocked) {
            $result['success'] = FALSE;
            $result['errors']['blocked'] = lang('player.blocked');
            return $result;
        }

        if ($isDeleted) {
            $result['success'] = FALSE;
            $result['errors']['login'] = lang('con.04');
            return $result;
        }

        if ($isBlockedFailedLoginAttempt) {
            $getFailedLoginAttemptTimeoutUntilByPlayerId = $this->player_model->getFailedLoginAttemptTimeoutUntilByPlayerId($player_id);
            $login_attempt_reset_timeout = $this->CI->operatorglobalsettings->getSettingIntValue('player_login_failed_attempt_reset_timeout');
            $rest_of_time =floor((strtotime($getFailedLoginAttemptTimeoutUntilByPlayerId) + ($login_attempt_reset_timeout * 60) - time()) / 60);

            if($this->CI->operatorglobalsettings->getSettingBooleanValue('player_login_failed_attempt_blocked') && !empty($login_attempt_reset_timeout) && !empty($getFailedLoginAttemptTimeoutUntilByPlayerId)){
                if($this->CI->utils->isTimeoutNow($getFailedLoginAttemptTimeoutUntilByPlayerId, $login_attempt_reset_timeout)){
                    $this->player_model->updatePlayer($player_id, [
                        'blocked' => Player_model::DEFAULT_PLAYERACCOUNT_STATUS,
                        //'failed_login_attempt_timeout_until' => null,
                    ]);
                    $this->player_model->updatePlayerTotalWrongLoginAttempt($player_id);
                } else {
                    $result['success'] = FALSE;
                    $result['errors']['blocked'] = sprintf(lang('player.failed.login.attempt'), $rest_of_time);
                    return $result;
                }
            } else {
                $result['success'] = FALSE;
                $result['errors']['blocked'] = sprintf(lang('player.failed.login.attempt.forever'), $rest_of_time);
                return $result;
            }
        }

        if($isSelfExclusioned){
            $result['success'] = FALSE;
            $result['errors']['selfexclusion'] = lang('player.selfexclusion');

            return $result;
        }

        if($isCooloff){
            $result['success'] = FALSE;
            $result['errors']['selfexclusion'] = lang('player.selfexclusion');

            return $result;
        }

        return $result;
    }

    protected function _login_success($player_id, $remember_me){
        if($this->CI->operatorglobalsettings->getSettingBooleanValue('player_login_failed_attempt_blocked')){
            $this->player_model->updatePlayerTotalWrongLoginAttempt($player_id);
        }

        if ($remember_me) {
            list($loginTokenId, $token) = $this->player_login_token->newLoginToken($player_id);
            set_cookie('remember_me', $token, 604800);
        } else {
            delete_cookie('remember_me');
        }
    }

    public function login_by_player_info($playerInfo, $remember_me = FALSE, $allow_clear_session = true){
        $result = [
            'success' => TRUE,
            'errors' => []
        ];

        $player_id = $playerInfo['playerId'];

        $result = $this->_check_player($result, $player_id);

        if(!$result['success']){
            return $result;
        }

        if (!$this->CI->authentication->loginByPlayerInfo($playerInfo, $allow_clear_session)) {
            $result['success'] = FALSE;
            $result['errors'] = array_replace($result['errors'], $this->CI->authentication->get_error_errors());

            return $result;
        }

        $this->_login_success($player_id, $remember_me);

        $this->CI->utils->saveHttpRequest($player_id, Http_request::TYPE_LAST_LOGIN);

        return $result;
    }

    public function login_by_password($username, $password, $remember_me = FALSE){
        $result = [
            'success' => TRUE,
            'errors' => []
        ];

        $player_id = $this->player_model->getPlayerIdByUsername($username);

        $result = $this->_check_player($result, $player_id);

        if(!$result['success']){
            return $result;
        }

        if (!$this->CI->authentication->login($username, $password)) {
            $result['success'] = FALSE;
            $result['errors'] = array_replace($result['errors'], $this->CI->authentication->get_error_message());

            return $result;
        }

        $this->_login_success($player_id, $remember_me);

        $this->CI->utils->saveHttpRequest($player_id, Http_request::TYPE_LAST_LOGIN);

        return $result;
    }

    public function login_by_contact_number($contact_number, $remember_me = FALSE){
        $result = [
            'success' => TRUE,
            'errors' => []
        ];

        $playerInfo = $this->player_model->getPlayerLoginInfoByNumber($contact_number);

        if(empty($playerInfo)){
            $result['success'] = FALSE;
            $result['errors']['login'] = lang('Login failed, wrong player info');

            return $result;
        }

        return $this->login_by_player_info($playerInfo, $remember_me);
    }

    public function login_by_token($token, $remember_me = FALSE){
        $game_platform_id = filter_input(INPUT_GET, 'game_platform_id', FILTER_SANITIZE_URL);
        $allow_clear_session = true;
        if(in_array($game_platform_id, $this->utils->getConfig('game_allowed_multiple_login'))){
            $allow_clear_session = false;
        }

        $this->CI->utils->debug_log('login_by_token on pl --- token == '. $token);
        $result = [
            'success' => TRUE,
            'errors' => []
        ];

        $playerInfo = $this->common_token->getPlayerInfoByToken($token);

        if(empty($playerInfo)){
            $result['success'] = FALSE;
            $result['errors']['login'] = lang('Login status timed out, please login again');

            return $result;
        }

        return $this->login_by_player_info($playerInfo, $remember_me, $allow_clear_session);
    }

    public function login_from_game($platformId, $token){
        $this->CI->load->model(array('game_provider_auth'));

        $result = [
            'success' => TRUE,
            'errors' => []
        ];

        //check token by api
        /* @var Abstract_game_api $api */
        $api = $this->CI->utils->loadExternalSystemLibObject($platformId);

        if(empty($api)){
            $result['success'] = FALSE;
            $result['errors']['api_not_exists'] = lang('Login failed, wrong player info');

            return $result;
        }

        $rlt = $api->getPlayerInfoByToken($token);

        if (!isset($rlt['gameUsername']) && !isset($rlt['gameName'])) {
            $result['success'] = FALSE;
            $result['errors']['login'] = lang('Login failed, wrong player info');

            return $result;
        }

        return $this->login_by_player_info($rlt, FALSE);
    }

    public function kickPlayer($player_id){
        $result = $this->player_model->kickoutPlayer($player_id);
        $this->player_model->updatePlayerOnlineStatus($player_id, Player_model::PLAYER_OFFLINE);
        $this->CI->utils->debug_log('kickout', $result);
        return $result;

    }

    public function kickPlayerGamePlatform($player_id, $player_name){
        if($this->CI->utils->isEnabledFeature('kickout_game_when_kickout_player')){
            $this->CI->load->model(['game_provider_auth']);
            $gameApis = $this->CI->game_provider_auth->getPlayerGamePlatform($player_id);

            if(empty($gameApis)){
                $this->CI->utils->debug_log('Kick player with empty player game platform');
                return;
            }

            foreach ($gameApis as $key) {
                $api = $this->CI->utils->loadExternalSystemLibObject($key['id']);
                if ($api) {
                    $api_result = $api->logout($player_name);
                    $this->CI->utils->debug_log('api', $key['id'], 'logout result', $api_result);
                }
            }
        }
    }

    public function getPlayerById($player_id) {
        $result = $this->player_model->getPlayerInfoById($player_id);
        return $result;
    }

    public function checkModifiedFields($player_id, $new_data) {
        $old_data = $this->getPlayerById($player_id);

        $diff = array_diff_assoc($new_data, $old_data);

        foreach ($diff as $key => $value) {
            $changes[lang('reg.fields.' . $key) ?: $key] = [
                'old' => $old_data[$key],
                'new' => $new_data[$key],
            ];
        }

        $output = '<ul>';

        if (!empty($changes)) {
            ksort($changes);

            foreach ($changes as $key => $value) {
                $output .= "<li>{$key}:<br><code>Old: {$value['old']}</code><br><code>New: {$value['new']}</code></li>";
            }
        }
        $output .= '</ul>';

        return $output;
    }

    public function editPlayerDetails($data, $player_id){
        $this->player_model->editPlayerDetails($data, $player_id);
    }

    public function addPlayerInfoUpdates($playerId, $data) {
        return $this->player_model->addPlayerInfoUpdates($playerId, $data);
    }

    public function savePlayerUpdateLog($player_id, $changes, $updatedBy) {
        $dataset = [
            'playerId'	=> $player_id,
            'changes'	=> $changes,
            'createdOn'	=> $this->CI->utils->getNowForMysql() ,
            'operator'	=> $updatedBy,
        ];

        $this->addPlayerInfoUpdates($player_id, $dataset);
    }
}