<?php
require_once dirname(__FILE__) . '/abstract_game_api_common_gpk.php';

class Game_api_gpk extends Abstract_game_api_common_gpk {

    const ORIGINAL_GAMELOGS_TABLE = 'gpk_gamelogs';
    
    public function getPlatformCode(){
        return GPK_API;
    }

    public function __construct(){
        parent::__construct();
        $this->original_gamelogs_table = self::ORIGINAL_GAMELOGS_TABLE;
    }
}
/*end of file*/