<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';
require_once dirname(__FILE__) . '/abstract_game_api_common_dg.php';

/******************************
	API NAME: DreamGame Game API Interface
	API version: [ v 1.0.9 ]

	Sample Extra Info:
	{
	    "limit_group": "A",
	    "language": "en",
	    "win_limit": 0,
	    "currency": "CNY",
	    "agent_name": "DGTE010140",
	    "api_key": "3b2bb255601b43a1abf569ef18f96f81",
	    "prefix_for_username": "t1",
	    "adjust_datetime_minutes": 10
	}
*******************************/

class Game_api_dg extends Abstract_game_api_dg_common {

    public function isSeamLessGame(){
        return false;
    }

    public function getPlatformCode() {
        return DG_API;
    }
}

/*end of file*/