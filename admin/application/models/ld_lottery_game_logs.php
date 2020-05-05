<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ld_lottery_game_logs extends Base_game_logs_model {

    public function __construct() {
        parent::__construct();
    }

    protected $tableName = "ld_lottery_game_logs";

    public function getGameLogStatistics($dateFrom, $dateTo) {
        $gameDescriptionArray = $this->getLDLotteryGame();
        $gameDescription = array_column($gameDescriptionArray, null, 'game_code');

        $this->db->from($this->tableName);
        $this->db->where('ld_lottery_game_logs.bet_time >=', $dateFrom);
        $this->db->where('ld_lottery_game_logs.bet_time <=', $dateTo);

        $gameRecord =  $this->runMultipleRowArray();

        if (!empty($gameRecord)) {
            foreach($gameRecord as $key => $record) {
                if(array_key_exists($record['lotto_name'], $gameDescription)) {
                    $theGame = $gameDescription[$record['lotto_name']];
                } else {
                    $theGame = $gameDescription['ld_lottery'];
                }
                $gameRecord[$key]['game_description_id'] = $theGame['id'];
                $gameRecord[$key]['game_name'] = $theGame['game_name'];
                $gameRecord[$key]['game_code'] = $theGame['game_code'];
                $gameRecord[$key]['game_type_id'] = $theGame['game_type_id'];
                $gameRecord[$key]['void_bet'] = $theGame['void_bet'];
                $gameRecord[$key]['game_type'] = $theGame['game_type'];
            }
        }

        return $gameRecord;
    }

    public function getLDLotteryGame() {
        $this->db->select('game_description.id');
        $this->db->select('game_description.game_name');
        $this->db->select('game_description.game_code');
        $this->db->select('game_description.game_type_id');
        $this->db->select('game_description.void_bet');
        $this->db->select('game_type.game_type');
        $this->db->from('game_type');
        $this->db->join('game_description', 'game_description.game_type_id = game_type.id', 'LEFT');
        $this->db->where('game_type.game_type_code', 'lottery');
        $this->db->where('game_description.void_bet !=', 1 );
        $this->db->where('game_description.game_platform_id', LD_LOTTERY_API);
        return $this->runMultipleRowArray();
    }

    public function getOrderNumber($orderNumber){
        $this->db->select('id')->from($this->tableName)->where('order_no', $orderNumber);
        return $this->runOneRowOneField('id');
    }

    public function syncGameLogs($data) {
        $id = $this->getOrderNumber($data['order_no']);

        if (!empty($id)) {
            return $this->updateGameLog($id, $data);
        } else {
            return  $this->insertGameLogs($data);
        }
    }

    public function getExistingBetIds($betIds) {
        $this->db->select('id, order_no')->from($this->tableName)->where_in('order_no', $betIds);
        return $this->runMultipleRowArray();
    }

}