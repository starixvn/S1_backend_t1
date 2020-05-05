<?php

require_once dirname(__FILE__) . "/BaseEvent.php";

class DepositEvent extends BaseEvent{

    protected $sale_order_id;
    protected $transaction_id;
    protected $player_id;
    protected $payment_account_id;

    public function extractData($data){
        $this->player_id=$data['player_id'];
        $this->sale_order_id=$data['sale_order_id'];
        $this->transaction_id=$data['transaction_id'];
        $this->payment_account_id=$data['payment_account_id'];
    }

    public function getSaleOrderId(){
        return $this->sale_order_id;
    }

    public function getTransactionId(){
        return $this->transaction_id;
    }

    public function getPlayerId(){
        return $this->player_id;
    }

    public function getPaymentAccountId() {
        return $this->payment_account_id;
    }
}
