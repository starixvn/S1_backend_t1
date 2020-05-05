<?php
require_once dirname(__FILE__) . "/game_api_t1_common.php";

/*
*	This class will integrate game_gateway api for T1RG_API
*
*   For Testing, you must invoke "create_simple_merchant" in gamegateway_module.php, 
*   then change the extra_info based on generated keys
*
*   extra_info
*  {
*   "api_merchant_code": "icecny", this is depend to agent username in gamegateway
*  }
*/

class Game_api_rg_t1 extends Game_api_t1_common
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getPlatformCode()
    {
        return T1RG_API;
    }

    public function getOriginalPlatformCode()
    {
        return RG_API;
    }
}