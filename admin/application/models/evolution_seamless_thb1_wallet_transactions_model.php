<?php
if(! defined("BASEPATH")){
    exit("No direct script access allowed");
}

require_once dirname(__FILE__) . "/base_model.php";

class Evolution_seamless_thb1_wallet_transactions_model extends BaseModel
{

    protected $tableName = "evolution_seamless_thb1_wallet_transactions";

    public function __construct()
    {
        parent::__construct();

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
                ->where("transactionId",$transactionId);

        return $this->runExistsResult();
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
     * Check if Transaction is already refunded
     * 
     * @param int $transactionId
     * 
     * @return boolean
    */
    public function isAlreadyRefunded($transactionId){

        $this->db->from($this->tableName)
                ->where("transactionId",$transactionId)
                ->where("refundedIn is NOT NULL");

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

        $this->db->where("transactionId",$refunded_transaction_id)
            ->set([
                "refundedIn" => $this->CI->utils->getNowForMysql()
            ]);

        $this->runAnyUpdate($this->tableName);

        return $this->db->affected_rows();
    }

    /**
     * Get Amount in Transaction based in transaction id
     * 
     * @param string $transactionId
     * 
     * @return string
     */
    public function getTransactionAmount($transactionId)
    {
        $query = $this->db->select("transactionAmount")
                    ->from($this->tableName)
                    ->where("transactionId",$transactionId)
                    ->get();

         return $this->getOneRowOneField($query,"transactionAmount");
    }


}