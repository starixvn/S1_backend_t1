<?php
require_once dirname(__FILE__) . '/abstract_game_api_common_hub88.php';

class Game_api_hub88 extends abstract_game_api_common_hub88 {
//	const CURRENCY_CODE = "CNY";

	public function getPlatformCode(){
		return HUB88_API;
    }

    public function __construct(){
        parent::__construct();
        
        $this->originalTable = 'hub88_game_logs';
//        $this->currency_code = self::CURRENCY_CODE;
    }

}
/*end of file*/