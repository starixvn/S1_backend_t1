<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Mg_dashur_game_logs extends Base_game_logs_model {

    public function __construct() {
        parent::__construct();
    }

    const CATEGORY_WAGER = 'WAGER';
    const CATEGORY_PAYOUT = 'PAYOUT';

    protected $tableName = "mg_dashur_game_logs";

    public function getAvailableRows($rows) {

        $this->db->select('mg_id')->from($this->tableName)->where_in('mg_id', array_column($rows, 'id'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'mg_id');
            $availableRows = array();
            foreach ($rows as $row) {
                $ticketId = $row['id'];
                if (!in_array($ticketId, $existsId)) {
                    $availableRows[] = $row;
                }
            }
        } else {
            $availableRows = $rows;
        }
        return $availableRows;
    }

    public function getGameLogStatistics($dateFrom, $dateTo) {
        $sql = <<<EOD
            SELECT
              mg_dashur_game_logs.external_uniqueid,
              mg_dashur_game_logs.response_result_id,
              mg_dashur_game_logs.account_id,
              mg_dashur_game_logs.transaction_time,
              mg_dashur_game_logs.external_ref,
              mg_dashur_game_logs.amount,
              mg_dashur_game_logs.game_id,
              mg_dashur_game_logs.item_id,
              mg_dashur_game_logs.player_id,
              mg_dashur_game_logs.category,
              mg_dashur_game_logs.round_key,
              mg_dashur_game_logs.mg_id,
              game_provider_auth.player_id,
              game_provider_auth.login_name AS player_name,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet
            FROM
              mg_dashur_game_logs
              LEFT JOIN game_description
                ON (
                  mg_dashur_game_logs.item_id = game_description.external_game_id
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              JOIN `game_provider_auth`
                ON (
                  `mg_dashur_game_logs`.`account_id` = `game_provider_auth`.`external_account_id`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE (
                mg_dashur_game_logs.transaction_time >= ?
                AND mg_dashur_game_logs.transaction_time <= ?
              )
              GROUP BY round_key, account_id
EOD;

        # AND mg_dashur_game_logs.id IN ( select MIN(mg_id) as mg_id from mg_dashur_game_logs group by round_key )


        $query = $this->db->query($sql, array(
            MG_DASHUR_API,
            MG_DASHUR_API,
            $dateFrom,
            $dateTo
        ));

        return $this->getMultipleRowArray($query);
    }

    public function getWagerByRoundKeyAndCategory($roundKey, $accountId,$betTime=null) {
        $this->db->from($this->tableName);
        $this->db->where('round_key', $roundKey);
        $this->db->where('account_id', $accountId);
        $this->db->where('category', self::CATEGORY_WAGER);
        if($betTime){
          $this->db->where('transaction_time >=', $betTime." 00:00:00");
          $this->db->where('transaction_time <=', $betTime." 23:59:59"); 
        }
        return $this->runMultipleRowArray();
    }

    // get first unique mg id by WAGER category
    // game bo display the mg id
    public function getUniqueMGWagerId($roundKey, $accountId) {
        $this->db->select('MIN(mg_id) as mg_id');
        $this->db->from($this->tableName);
        $this->db->where('round_key', $roundKey);
        $this->db->where('account_id', $accountId);
        $this->db->where('category', self::CATEGORY_WAGER);
        return $this->runOneRowArray();
    }

    // get the last MG id.
    public function getPayOutByRoundKeyAndCategory($roundKey,$betTime=null) 
    {
        $this->db->from($this->tableName);
        $this->db->where('round_key', $roundKey);
        $this->db->where('category', self::CATEGORY_PAYOUT);
        if($betTime){
          $this->db->where('transaction_time >=', $betTime." 00:00:00");
          $this->db->where('transaction_time <=', $betTime." 23:59:59"); 
        }
        $this->db->order_by('mg_id', 'asc');
        return $this->runMultipleRowArray();
    }

}