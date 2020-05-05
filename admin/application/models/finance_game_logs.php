<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Finance_game_logs extends Base_game_logs_model {

    public function __construct() {
        parent::__construct();
    }

    protected $tableName = "finance_game_logs";

    public function getGameLogStatistics($dateFrom, $dateTo) {

        $gameList = $this->getFinanceGame();

        $select = 'finance_game_logs.external_uniqueid,
				   finance_game_logs.response_result_id,
                   finance_game_logs.PlatformAccountId,
				   finance_game_logs.BetTime,
				   finance_game_logs.EndTime,
				   finance_game_logs.BetAmount,
				   finance_game_logs.PayoutAmount,
				   finance_game_logs.WinLose,
				   finance_game_logs.UpDown,
				   finance_game_logs.IsDouble,
				   finance_game_logs.IsDelay,
				   finance_game_logs.IsClose,
				   finance_game_logs.RuleType,
                   finance_game_logs.username,
                   finance_game_logs.player_id,
				   ';
        $this->db->select($select);
        $this->db->from($this->tableName);
        $this->db->where('finance_game_logs.BetTime >=', $dateFrom);
        $this->db->where('finance_game_logs.BetTime <=', $dateTo);

        $gameRecord =  $this->runMultipleRowArray();

        if (!empty($gameRecord)) {
            foreach($gameRecord as $key => $record) {
                $gameRecord[$key]['game_description_id'] = $gameList['id'];
                $gameRecord[$key]['game_name'] = $gameList['game_name'];
                $gameRecord[$key]['game_code'] = $gameList['game_code'];
                $gameRecord[$key]['game_type_id'] = $gameList['game_type_id'];
                $gameRecord[$key]['void_bet'] = $gameList['void_bet'];
                $gameRecord[$key]['game_type'] = $gameList['id'];
            }
        }
        return $gameRecord;
    }

    public function getFinanceGame() {
        $this->db->select('game_description.id');
        $this->db->select('game_description.game_name');
        $this->db->select('game_description.game_code');
        $this->db->select('game_description.game_type_id');
        $this->db->select('game_description.void_bet');
        $this->db->select('game_type.game_type');
        $this->db->from('game_type');
        $this->db->join('game_description', 'game_description.game_type_id = game_type.id', 'LEFT');
        // $this->db->where('game_type.game_type_code', 'finance_game');
        $this->db->where('game_description.game_code', 'finance');
        $this->db->where('game_description.void_bet !=', 1 );
        $this->db->where('game_description.game_platform_id', FINANCE_API);
        return $this->runOneRowArray();
    }

    public function getOrderNumber($orderNumber){
        $this->db->select('id')->from($this->tableName)->where('OrderNo', $orderNumber);
        return $this->runOneRowOneField('id');
    }

    public function syncGameLogs($data) {
        $id=$this->getOrderNumber($data['OrderNo']);
        if (!empty($id)) {
            return $this->updateGameLog($id, $data);
        } else {
            return $this->insertGameLogs($data);
        }
    }

}