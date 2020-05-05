<?php
require_once dirname(__FILE__) . '/abstract_game_api_common_asia_gaming.php';

/**
* Game Provider: AG (AsiaGaming)
* Game Type: live casino
* Wallet Type: Transfer
* Currency: RMB (CNY)
/**
* ORIGINAL API NAME: ASIA_GAMING_API
*
* @category game_platform
* @version not specified
* @copyright 2013-2020 TripleOneTech
* @integrator emmanuel.php.ph
**/
class Game_api_asia_gaming extends Abstract_game_api_common_asia_gaming
{
    const ORIGINAL_GAMELOGS_TABLE = 'asia_gaming_game_logs';
    // const GAME_TIMEZONE = null;

    public function getPlatformCode()
    {
        return TGP_AG_API;
    }
    
    public function __construct()
    {
        $data = array(
            'original_gamelogs_table' => self::ORIGINAL_GAMELOGS_TABLE,
            'game_platform_id' => $this->getPlatformCode(),
            // 'game_timezone' => self::GAME_TIMEZONE
        );
        parent::__construct($data);
        $this->api_url = $this->getSystemInfo('url', 'https://tgpaccess.com/');
        $this->currency = $this->getSystemInfo('currency');
        $this->language = $this->getSystemInfo('language');
        $this->clientId = $this->getSystemInfo('clientId');
        $this->betlimitid = $this->getSystemInfo('betlimitid');
        $this->clientSecret = $this->getSystemInfo('clientSecret');
        $this->game_url = $this->getSystemInfo('game_url');
        $this->method = "POST"; # default as POST
        $this->agent_name = $this->getSystemInfo('agent_name');
        $this->api_key = $this->getSystemInfo('api_key');
        $this->update_original = $this->getSystemInfo('update_original_logs');

        $this->gpcode = $this->getSystemInfo('gpcode', 'AG');
        $this->gcode = $this->getSystemInfo('gcode', 'Asia_Gaming_Lobby');

        $this->gameTimeToServerTime = $this->getSystemInfo('gameTimeToServerTime', '+8 hours');
        $this->serverTimeToGameTime = $this->getSystemInfo('serverTimeToGameTime', '-8 hours');
    }
}
