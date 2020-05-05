<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ebet_ag_game_logs extends Base_game_logs_model {

    const LOST_AND_FOUND = 1;

    public function __construct() {
        parent::__construct();
    }

    protected $tableName = "ebet_ag_game_logs";

    public function getGameLogStatistics($dateFrom, $dateTo) {
        $select = 'ebet_ag_game_logs.external_uniqueid,
				   ebet_ag_game_logs.response_result_id,
                   ebet_ag_game_logs.bill_no,
				   ebet_ag_game_logs.bet_time,
				   ebet_ag_game_logs.bet_amount,
                   ebet_ag_game_logs.player_name,
                   ebet_ag_game_logs.player_id,
                   ebet_ag_game_logs.game_type,
                   ebet_ag_game_logs.before_credit,
                   ebet_ag_game_logs.net_amount AS result_amount,
         		   game_description.id  AS game_description_id,
				   game_description.game_name AS game,
				   game_description.game_code,
				   game_description.game_type_id,
				   game_description.void_bet,
				   game_type.game_type
				   ';

        $this->db->select($select);
        $this->db->from($this->tableName);
        $this->db->join('game_description', 'game_description.game_code = ebet_ag_game_logs.game_type AND game_description.game_platform_id = '.EBET_AG_API . ' AND game_description.void_bet != 1', 'LEFT');
        $this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
        $this->db->where('ebet_ag_game_logs.bet_time >=', $dateFrom);
        $this->db->where('ebet_ag_game_logs.bet_time <=', $dateTo);

        return $this->runMultipleRowArray();
    }

    public function getBillNumber($billNo){
        $this->db->select('id')->from($this->tableName)->where('bill_no', $billNo);
        return $this->runOneRowOneField('id');
    }

    public function checkLostAndFound($billNo){
        $this->db->select('id')->from($this->tableName)->where('bill_no', $billNo)->where('is_from_lost_and_found_folder', self::LOST_AND_FOUND);
        return $this->runOneRowOneField('id');
    }

    public function syncGameLogs($data) {
        $id=$this->getBillNumber($data['bill_no']);
        if (!empty($id)) {
            if($this->checkLostAndFound($data['bill_no'])) {
                $this->updateGameLog($id, $data);
            }
        } else {
            $this->insertGameLogs($data);
        }
    }
}