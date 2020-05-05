<?php
require_once dirname(__FILE__) . '/game_api_t1_common.php';
require_once realpath(dirname(__FILE__) . '/..') . '/oneworks_betdetails_module.php';
/*
*	This class will integrate game_gateway api for T1 oneworks
*
*   For Testing, you must invoke "create_simple_merchant" in gamegateway_module.php, 
*   then change the extra_info based on generated keys
*
*   extra_info
*  {
*   "api_merchant_code": "testmerchant",
*   "api_secured_key": "123456"
*  }
*/

class Game_api_oneworks_t1 extends Game_api_t1_common {
	use oneworks_betdetails_module;

    # Don't ignore on refresh 
    const DEFAULT_IGNORED_0_ON_REFRESH = FALSE;
    
	public function getPlatformCode(){
        return T1ONEWORKS_API;
    }

    public function getOriginalPlatformCode(){
        return ONEWORKS_API;
    }

    public function __construct(){
        parent::__construct();
    }
    
}

/*end of file*/
