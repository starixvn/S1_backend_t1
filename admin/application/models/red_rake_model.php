<?php
if(! defined('BASEPATH')){
    exit("No direct script access allowed");
}

require_once dirname(__FILE__) . "/base_game_logs_model.php";

class Red_rake_model extends Base_game_logs_model{

    protected $tablename = "red_rake_game_logs";
    
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Get Gamelogs statistics
     */
    public function getGameLogStatistics($dateFrom, $dateTo)
    {
        return null;
    }

}