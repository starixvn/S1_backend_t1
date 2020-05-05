<?php
/**
 * player_main_js_addons_game_preloader.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
class Player_main_js_addons_game_preloader extends Player_main_js_addons_abstract {
    /* @var Game_api_lottery_t1 */
    protected $_t1lottery_api;

    public function __construct(){
        parent::__construct();
    }

    public function isEnabled(){
        $is_enabled = $this->CI->utils->isEnabledFeature('player_main_js_enable_game_preloader');
        if($is_enabled){
            $this->_t1lottery_api = $this->CI->utils->loadExternalSystemLibObject(T1LOTTERY_API);
            if(empty($this->_t1lottery_api)){
                $this->CI->utils->debug_log('cannot load t1lottery api');
                $is_enabled = FALSE;
            }
        }
        return $is_enabled;
    }

    public function variables(){
        $lottery_sdk_url = NULL;
        $rlt = $this->_t1lottery_api->queryForwardSDK();
        if($rlt['success'] && !empty($rlt['url'])){
            $lottery_sdk_url = $rlt['url'];
        }

        $variables[$this->_addons_name] = [
            'lottery' => [
                'lottery_play_url' => $this->CI->utils->getSystemUrl('player', '/player_center/goto_t1lottery/'),
                'lottery_sdk_url' => $lottery_sdk_url,
            ]
        ];

        return $variables;
    }
}