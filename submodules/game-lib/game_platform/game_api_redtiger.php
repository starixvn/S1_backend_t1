<?php
require_once dirname(__FILE__) . '/game_api_common_redtiger.php';

class Game_api_redtiger extends Game_api_common_redtiger {
	const CURRENCY_TYPE = "RMB";
	public function getPlatformCode(){
		return REDTIGER_API;
    }

    public function __construct(){
        parent::__construct();
        $this->currency_type = self::CURRENCY_TYPE;
    }
}
/*end of file*/