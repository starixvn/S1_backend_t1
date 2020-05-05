<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Evo_game_logs extends Base_game_logs_model {

    public function __construct() {
        parent::__construct();
    }

    protected $tableName = "evo_game_logs";

    public function getGameLogStatistics($dateFrom, $dateTo) {
        $select = 'evo_game_logs.external_uniqueid,
				   evo_game_logs.response_result_id,
				   evo_game_logs.player_name,
				   evo_game_logs.game_name,
				   evo_game_logs.bet_time,
				   evo_game_logs.bet_amount,
				   evo_game_logs.payout_amount,
				   evo_game_logs.after_balance,
				   evo_game_logs.result_amount,
				   evo_game_logs.created_at,
				   evo_game_logs.updated_at,
				   evo_game_logs.currency,
				   game_description.id  AS game_description_id,
				   game_description.game_name AS game,
				   game_description.game_code,
				   game_description.game_type_id,
				   game_description.void_bet,
				   game_type.game_type
				   ';

        $this->db->select($select);
        $this->db->from($this->tableName);
        $this->db->join('game_description', 'game_description.external_game_id = evo_game_logs.game_name', 'LEFT');
        $this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
        $this->db->where('evo_game_logs.bet_time >=', $dateFrom);
        $this->db->where('evo_game_logs.bet_time <=', $dateTo);
        $this->db->where('game_description.game_platform_id', EVOLUTION_GAMING_API);
        $this->db->where('game_description.void_bet !=', 1 );

        #$this->db->get();
        #echo $this->db->last_query();exit;

        return  $this->runMultipleRowArray();
    }

}