<?php
require_once dirname(__FILE__) . '/game_api_t1_common.php';
/*
*	This class will integrate game_gateway api for T1 PT
*
*   For Testing, you must invoke "create_simple_merchant" in gamegateway_module.php, 
*   then change the extra_info based on generated keys
*
*   extra_info
*   {
*    "api_url": "http://admin.og.local",
*    "api_merchant_code": "testmerchant",
*    "api_secured_key": "123456",
*    "api_signature_key": "dd35aPO0bd186dc6ace6We2e0fb48s70"
*   }
*/
class Game_api_pt_t1 extends Game_api_t1_common {

    public function getPlatformCode(){
       return T1PT_API;
    }

    public function getOriginalPlatformCode(){
    	switch ($this->getSystemInfo('currency', 'CNY')) {
    		case 'CNY':
        		return PT_API;
    			break;
    		case 'KRW':
        		return PT_KRW_API;
    			break;
    		default:
       		 	return PT_API;
    			break;
    	}
    }

    public function __construct(){
        parent::__construct();
    }
}

/*end of file*/
