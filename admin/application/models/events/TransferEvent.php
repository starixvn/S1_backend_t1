<?php

require_once dirname(__FILE__) . "/BaseEvent.php";

class TransferEvent extends BaseEvent{

    protected $transfer_request_id;
    protected $transaction_id;
    protected $player_id;

    public function extractData($data){
        $this->player_id=$data['player_id'];
        $this->transfer_request_id=$data['transfer_request_id'];
        $this->transaction_id=$data['transaction_id'];
    }

    public function getTransferRequestId(){
        return $this->transfer_request_id;
    }

    public function getTransactionId(){
        return $this->transaction_id;
    }

    public function getPlayerId(){
        return $this->player_id;
    }

}
