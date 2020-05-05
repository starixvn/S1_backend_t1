<?php
if(! defined("BASEPATH")){
    exit("No direct script access allowed");
}

require_once dirname(__FILE__) . "/base_model.php";

class Sbobet_seamless_game_transactions_model extends BaseModel
{

    protected $tableName = "sbobet_seamless_game_transactions";

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
     * Get TransactionId By TransferCode
     * 
     * @param int $transferCode
     * @param int $transactionType
     * 
     * @return string
    */
    public function getTransactionIdByTransferCode($transferCode,$transactionType)
    {
        $this->db->select("transaction_id")
                    ->from($this->tableName)
                    ->where("transfer_code",$transferCode)
                    ->where("transaction_type",$transactionType)
                    ->order_by('created_at','desc');

        return $this->runOneRowOneField('transaction_id');
    }
}