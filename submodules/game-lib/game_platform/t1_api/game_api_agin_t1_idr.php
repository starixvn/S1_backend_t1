<?php
require_once dirname(__FILE__) . '/game_api_t1_common.php';
/*
*	This class will integrate game_gateway api for T1 AGIN
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

class Game_api_agin_t1_idr extends Game_api_t1_common {
	
	const CURRENCY = "IDR";

	public function getPlatformCode(){
        return T1AGIN_IDR_API;
    }

    public function getOriginalPlatformCode(){
        return AGIN_API;
    }

    public function __construct(){
        parent::__construct();

        $this->currency_code = self::CURRENCY;
    }
    
}

/*end of file*/
