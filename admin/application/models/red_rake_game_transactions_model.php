<?php
if(! defined("BASEPATH")){
    exit("No direct script access allowed");
}

require_once dirname(__FILE__) . "/base_model.php";

class Red_rake_game_transactions_model extends BaseModel
{

    protected $tableName = "red_rake_game_transactions";

    public function __construct()
    {
        parent::__construct();
    }

    /** 
     * Insert Transaction
     * 
     * @param array $data
     * 
     * @return int
    */
    public function insertTransaction($data)
    {
       $this->db->insert($this->tableName,$data);

       return $this->db->affected_rows();
    }

    /** 
     * check if transaction ID is already exist
     * 
     * @param int $transactionId
     * 
     * @return boolean
    */
    public function isTransactionAlreadyExist($transactionId)
    {
        $this->db->from($this->tableName)
                ->where("transaction_id",$transactionId);

        return $this->runExistsResult();
    }

    /** 
     * Update Refunded Transaction
     * 
     * @param int $refunded_transaction_id
     * 
     * @return int
     * 
    */
    public function updateRefundedTransaction($refunded_transaction_id)
    {
        $data = [
            "refunded_in" => $this->utils->getNowForMysql()
        ];

        $this->db->where("transaction_id",$refunded_transaction_id)
                ->update($this->tableName,$data);
        
        return $this->db->affected_rows();
    }

    /** 
     * Get Action Type of Transaction
     * 
     * @param int $transactionId
     * 
     * @return string
    */
    public function getActionType($transactionId)
    {
        $query = $this->db->select("action")
                    ->from($this->tableName)
                    ->where("transaction_id",$transactionId)
                    ->get();

         return $this->getOneRowOneField($query,"action");
    }

    /** 
     * Check if Transaction is already refunded
     * 
     * @param int $transactionId
     * 
     * @return boolean
    */
    public function isAlreadyRefunded($transactionId){

        $this->db->from($this->tableName)
                ->where("transaction_id",$transactionId)
                ->where("refunded_in is NOT NULL");

        return $this->runExistsResult();
    }

    /** 
     * Query red_rake_game_transactions table for Sync Original Game Logs
     * 
     * @param int $round_id
     * @param int $game_provider_id
     * 
     * @return array
    */
    public function getOriginalTransactions($round_id,$game_provider_id)
    {
        $where="rr.round_id = {$round_id} and gpa.game_provider_id = {$game_provider_id}";

        $sql = <<<EOD
SELECT rr.id as sync_index,
rr.action,
rr.game_id,
gd.english_name as game_name,
rr.token as session_id,
rr.player_id,
gpa.login_name as player_name,
rr.currency,
rr.round_id,
rr.transaction_id,
rr.amount as bet_amount,
rr.before_balance,
rr.after_balance,
rr.is_bet_loss,
rr.is_bonus_win as is_bonus_loss,
rr.timestamp,
rr.response_result_id,
rr.external_uniqueid as external_unique_id

FROM {$this->tableName} as rr
LEFT JOIN game_description as gd ON gd.external_game_id = rr.game_id
LEFT JOIN game_provider_auth as gpa ON gpa.player_id = rr.player_id
WHERE
{$where}
ORDER BY rr.timestamp ASC

EOD;

        $params=[$round_id,$game_provider_id];
        
        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
    }

    /**
     * Get The round id of record base in date from,date to and game provider ID
     * @param datetime $dateFrom
     * @param datetime $dateTo
     * @param int $game_provider_id
     * 
     */
    public function getRoundID($dateFrom,$dateTo,$game_provider_id)
    {
        $query = $this->db->select("rr.round_id,rr.player_id")
                    ->distinct()
                    ->from($this->tableName." rr")
                    ->join("game_provider_auth gpa","gpa.player_id = rr.player_id")
                    ->where("timestamp >=",$dateFrom)
                    ->where("timestamp <=",$dateTo)
                    ->where("gpa.game_provider_id",$game_provider_id)
                    ->get();

        return $this->getMultipleRowArray($query);
    }
}