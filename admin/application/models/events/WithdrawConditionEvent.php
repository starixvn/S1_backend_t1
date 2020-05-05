<?php

require_once dirname(__FILE__) . "/BaseEvent.php";

class WithdrawConditionEvent extends BaseEvent{

    protected $withdraw_condition_id;
    protected $transaction_id;
    protected $player_id;

    public function extractData($data){
        $this->player_id=$data['player_id'];
        $this->withdraw_condition_id=$data['withdraw_condition_id'];
        $this->transaction_id=$data['transaction_id'];
    }

    public function getWithdrawConditionId(){
        return $this->withdraw_condition_id;
    }

    public function getTransactionId(){
        return $this->transaction_id;
    }

    public function getPlayerId(){
        return $this->player_id;
    }

}
