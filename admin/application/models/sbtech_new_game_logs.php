<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Sbtech_new_game_logs extends Base_game_logs_model {

    public function __construct() {
        parent::__construct();
    }

    protected $tableName = "sbtech_new_game_logs";

    public function getGameLogStatistics($dateFrom, $dateTo) {
        $select = 'sbtech_new_game_logs.external_uniqueid,
				   sbtech_new_game_logs.response_result_id,
                   sbtech_new_game_logs.pl as result_amount,
                   sbtech_new_game_logs.total_stake AS bet_amount,
                   sbtech_new_game_logs.status,
                   sbtech_new_game_logs.username,
                   sbtech_new_game_logs.bet_settled_date,
                   sbtech_new_game_logs.creation_date,
                   IFNULL(sbtech_new_game_logs.bet_settled_date,sbtech_new_game_logs.creation_date ) AS game_date,
                   sbtech_new_game_logs.status,
                   sbtech_new_game_logs.odds,
                   sbtech_new_game_logs.bet_type_name,
                   sbtech_new_game_logs.purchase_id,
                   sbtech_new_game_logs.player_id,
                   sbtech_new_game_logs.selections,
				   game_description.id  AS game_description_id,
				   game_description.game_name AS game,
				   game_description.game_code,
				   game_description.game_type_id,
				   game_description.void_bet,
				   game_type.game_type
				   ';
        $this->db->select($select);
        $this->db->from($this->tableName);
        $this->db->join('game_description', 'game_description.game_code = sbtech_new_game_logs.branch_name', 'LEFT');
        $this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
        #$this->db->where('sbtech_new_game_logs.bet_settled_date >=', $dateFrom);
        #$this->db->where('sbtech_new_game_logs.bet_settled_date <=', $dateTo);
        $this->db->where('IFNULL(`sbtech_new_game_logs`.`bet_settled_date`,`sbtech_new_game_logs`.`creation_date`) >= "'.$dateFrom.'" AND IFNULL(`sbtech_new_game_logs`.`bet_settled_date`,`sbtech_new_game_logs`.`creation_date`) <= "' . $dateTo . '"');
        $this->db->where('game_description.game_platform_id', SBTECH_API);
        $this->db->where('game_description.void_bet !=', 1 );

        return  $this->runMultipleRowArray();
    }

    // use unique id
    public function getPurchaseId($purchase_id){
        $this->db->select('id')->from($this->tableName)->where('external_uniqueid', $purchase_id);
        return $this->runOneRowOneField('id');
    }

    public function syncGameLogs($data, $isOpenBet=null) {
        $id=$this->getPurchaseId($data['external_uniqueid']);
        if($isOpenBet) {
            // insert if not exist
            if (empty($id)) {
                $this->insertGameLogs($data);
            }
        } else {
            if (!empty($id)) {
                $this->updateGameLog($id, $data);
            } else {
                $this->insertGameLogs($data);
            }
        }
    }

}