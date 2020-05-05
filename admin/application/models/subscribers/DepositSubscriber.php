<?php

require_once dirname(__FILE__) . "/../events/DepositEvent.php";
require_once dirname(__FILE__) . "/AbstractSubscriber.php";

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DepositSubscriber extends AbstractSubscriber implements EventSubscriberInterface{

    public function __construct(){
        parent::__construct();
        // $this->utils->info_log('load subscriber class', get_class());
    }

    public static function getSubscribedEvents(){
        return array(
            Queue_result::EVENT_DEPOSIT_AFTER_DB_TRANS => 'afterDepositDBTrans',
        );
    }

    public function afterDepositDBTrans(DepositEvent $event){
        $this->utils->debug_log('===================Deposit Event afterDepositDBTrans', $event);

        $saleOrderId=$event->getSaleOrderId();
        $transactionId=$event->getTransactionId();
        $playerId=$event->getPlayerId();
        $paymentAccountId=$event->getPaymentAccountId();
        $saleOrder=null;
        $transaction=null;
        $player=null;
        $paymentAccount=null;

        $this->load->model(['sale_order', 'transactions', 'player_model', 'payment_account']);
        if(!empty($saleOrderId)){
            $saleOrder=$this->sale_order->getSaleOrderInfoById($saleOrderId);
        }
        if(!empty($transactionId)){
            $transaction=$this->transactions->getTransactionInfoById($transactionId);
        }
        if(!empty($playerId)){
            $player=$this->player_model->getPlayerArrayById($playerId);
        }
        if(!empty($paymentAccountId)){
            $paymentAccount=$this->payment_account->getPaymentAccountDetails($paymentAccountId);
        }

        //check promotion rules
        $this->utils->debug_log('start process deposit event', $event, $saleOrder, $transaction, $player, $paymentAccount);

        //check payment account deposit limit
        $this->checkPaymentAccountTotalAmountForNotification($paymentAccount);

        $this->utils->debug_log('end process deposit event', $event);
    }

    public function checkPaymentAccountTotalAmountForNotification($payment_account) {
        $this->load->model(['payment_account']);

        $get_payment_account_over_limit_percentage_result = $this->payment_account->checkIfPaymentAccountOverLimitPercentage($payment_account->id);
        $this->utils->debug_log('========checkPaymentAccountTotalAmountForNotification get_payment_account_over_limit_percentage_result', $get_payment_account_over_limit_percentage_result);
        if(isset($get_payment_account_over_limit_percentage_result['success'])) {
            if($get_payment_account_over_limit_percentage_result['success']) {
                $url = $this->getConfig('payment_account_notify_url');
                $user = $this->getConfig('payment_account_notify_user');
                $channel = $this->getConfig('payment_account_notify_channel');
                $this->utils->sendMessageService($get_payment_account_over_limit_percentage_result['msg'], $url, $user, $channel);
            }
        }
    }
}
