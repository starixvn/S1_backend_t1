<?php
if(! defined("BASEPATH")){
    exit("No direct script access allowed");
}

require_once dirname(__FILE__) . "/base_model.php";

class Sbobet_seamless_game_logs_model extends BaseModel
{

    protected $tableName = "sbobet_seamless_game_logs";

    public function __construct()
    {
        parent::__construct();
    }

    /** 
     * Insert Game Logs
     * 
     * @param array $data
     * 
     * @return int
    */
    public function insertGameLogs($data)
    {
       $this->db->insert($this->tableName,$data);

       return $this->db->affected_rows();
    }

    /** 
     * check if External Unique ID is already exist
     * 
     * @param int $externalUniqueId
     * 
     * @return boolean
    */
    public function isExternalUniqueIdAlreadyExist($externalUniqueId)
    {
        $this->db->select('external_uniqueid');
        $this->db->from($this->tableName)
                ->where("external_uniqueid",$externalUniqueId);

        return $this->runExistsResult();
    }

    /** 
     * check if bet is already settled
     * 
     * @param int $externalUniqueId
     * 
     * @return boolean
    */
    public function isSettleBetAlready($externalUniqueId)
    {
        $this->db->select('external_uniqueid');
        $this->db->from($this->tableName)
        ->where("external_uniqueid",$externalUniqueId)
        ->where("result_amount > -1");
        return $this->runExistsResult();
    }
 
    /** 
     * check if bet is already settled
     * 
     * @param int $externalUniqueId
     * 
     * @return boolean
    */
    public function checkIfBetIsCancelledAlready($externalUniqueId)
    {
        $this->db->select('external_uniqueid');
        $this->db->from($this->tableName)
        ->where("external_uniqueid",$externalUniqueId)
        ->where("status","void");
        return $this->runExistsResult();
    }

    /** 
     * check if bet is already running
     * 
     * @param int $externalUniqueId
     * 
     * @return boolean
    */
    public function checkIfBetIsRunningAlready($externalUniqueId)
    {
        $this->db->select('external_uniqueid');
        $this->db->from($this->tableName)
        ->where("external_uniqueid",$externalUniqueId)
        ->where("status","running");
        return $this->runExistsResult();
    }

    /** 
     * Get BetAmount By External Unique Id
     * 
     * @param int $externalUniqueId
     * 
     * @return string
    */
    public function getBetAmountByExternalUniqueId($externalUniqueId)
    {
        $this->db->select("bet_amount")
                    ->from($this->tableName)
                    ->where("external_uniqueid",$externalUniqueId);

        return $this->runOneRowOneField("bet_amount");
    }

    
    /** 
     * Is Rolled Back already
     * 
     * @param int $externalUniqueId
     * 
     * @return string
    */
    public function isRolledBackAlready($externalUniqueId)
    {
        $this->db->select('external_uniqueid');
        $this->db->from($this->tableName)
                 ->where("external_uniqueid",$externalUniqueId)
                 ->where("rollback_time > -1");
        return $this->runExistsResult();
    }
    
    /** 
     * Is Bet Cancelled already
     * 
     * @param int $externalUniqueId
     * 
     * @return string
    */
    public function isBetCancelledAlready($externalUniqueId)
    {
        $this->db->select('external_uniqueid');
        $this->db->from($this->tableName)
                 ->where("external_uniqueid",$externalUniqueId)
                 ->where("cancel_time > -1");
        return $this->runExistsResult();
    }

    /**
     * Get Rollback Amount By External Unique Id
     * 
     * @param int $externalUniqueId
     * 
     * @return string
    */
    public function getRollbackAmountByExternalUniqueId($externalUniqueId)
    {
        $this->db->select("result_amount")
                    ->from($this->tableName)
                    ->where("external_uniqueid",$externalUniqueId)
                    ->order_by('created_at','desc');

        return $this->runOneRowOneField('result_amount');
    }

    /** 
     * Get Result Amount By ExternalUniqueId
     * 
     * @param int $externalUniqueId
     * 
     * @return string
    */
    public function getResultAmountByExternalUniqueId($externalUniqueId)
    {
        $this->db->select("result_amount")
                    ->from($this->tableName)
                    ->where("external_uniqueid",$externalUniqueId);

        return $this->runOneRowOneField('result_amount');
    }

    /** 
     * Update Bet Amount by ExternalUniqueId
     * 
     * @param int $transferCode
     * @param int $transactionType
     * 
     * @return int
     * 
    */
    public function updateBetAmountByExternalUniqueId($transferCode,$data)
    {
        $this->db->where(["external_uniqueid"=>$transferCode])
                ->update($this->tableName,$data);
        
        return $this->db->affected_rows();
    }

    
    /** 
     * Update Bet Amount by ExternalUniqueId
     * 
     * @param int $externalUniqueId
     * 
     * @return int
     * 
    */
    public function updateBetResultByExternalUniqueId($externalUniqueId,$data)
    {
        $this->db->where(["external_uniqueid"=>$externalUniqueId])
                ->update($this->tableName,$data);
        
        return $this->db->affected_rows();
    }

    /** 
     * Soft delete settled records
     * 
     * @param int $transferCode
     * @param int $transactionType
     * 
     * @return int
     * 
    */
    public function softDeleteSettledRecords($transferCode,$transactionType)
    {
        $this->db->where(["transfer_code"=>$transferCode,"transaction_type"=>$transactionType])
                ->update($this->tableName,["deleted_at"=>$this->getNowForMysql()]);
        
        return $this->db->affected_rows();
    }

    /** 
     * Update Game Transaction By ExternalUniqueId
     * 
     * @param int $transferCode
     * @param int $transactionType
     * 
     * @return int
     * 
    */
    public function doUpdateTransactionByExternalUniqueId($externalUniqueid,$data)
    {
        $this->db->where("external_uniqueid",$externalUniqueid)
                ->update($this->tableName,$data);
        
        return $this->db->affected_rows();
    }

    /** 
     * check if externalUniqueId is already exist
     * 
     * @param int $externalUniqueId
     * 
     * @return boolean
    */
    public function isPlayerTipExists($externalUniqueId)
    {
        $this->db->select('external_uniqueid');
        $this->db->from($this->tableName)
                ->where(["external_uniqueid"=>$externalUniqueId]);

        return $this->runExistsResult();
    }

    /** 
     * check if externalUniqueId is already exist
     * 
     * @param int $externalUniqueId
     * 
     * @return boolean
    */
    public function isPlayerBonusExists($externalUniqueId)
    {
        $this->db->select('external_uniqueid');
        $this->db->from($this->tableName)
                ->where(["external_uniqueid"=>$externalUniqueId]);

        return $this->runExistsResult();
    }

    /** 
     * Get externalUniqueId By ExternalUniqueId
     * 
     * @param int $externalUniqueId
     * 
     * @return string
    */
    public function getBetDetails($externalUniqueId)
    {
        $this->db->select('bet_amount as stake,result_amount + bet_amount as winloss,status');
        $this->db->from($this->tableName);
        $this->db->where('external_uniqueid', $externalUniqueId);
        return $this->runOneRowArray();
    }
}