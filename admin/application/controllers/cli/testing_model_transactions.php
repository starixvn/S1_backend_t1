<?php
//always include base testing
require_once dirname(__FILE__) . '/base_testing.php';

//always extends from BaseTesting
class Testing_model_transactions extends BaseTesting {

    //should overwrite init function
    public function init() {
        //init your model or lib
        $this->load->model('transactions');
        // $this->lang->load('main', 'chinese');
    }
    //should overwrite testAll
    public function testAll() {
        //init first
        $this->init();
        //call your test function
        // $this->testGetBankTypeTree();
    }

    public function testTarget($methodName) {
        $this->init();
        $this->$methodName();
    }

    //it's your real test function
    private function createDepositTransaction() {

        $saleOrder->amount = 8888888;
        $saleOrder->player_id = "11111";
        $saleOrder->id = 999999;
        $saleOrder->payment_account_id = 222222;
        $adminUserId = 1;
        $beforeBalance = 100.00;

        $rst = $this->transactions->createDepositTransaction($saleOrder, $adminUserId, $beforeBalance);

        var_dump($rst);

        //$this->destroy_test_data();
    }

    // withdraw
    private function saveTransaction() {

        $data = array('to_id' => 111,
            'from_type' => 2,
            'from_id' => 111,
            'created_at' => '2080-11-11 00:00:00',
            'amount' => 120.00,
            'transaction_type' => 2,
         );

        $rst = $this->transactions->saveTransaction($data);

        var_dump($rst);

        //$this->destroy_test_data();
    }

    private function createBonusTransaction() {

        $adminUserId = 1;
        $playerId = 111;
        $amount = 100.00;
        $beforeBalance = 99.99;
        $playerPromoId = 888;

        $rst = $this->transactions->createBonusTransaction($adminUserId, $playerId, $amount, $beforeBalance, $playerPromoId);

        var_dump($rst);

        //$this->destroy_test_data();
    }

    private function createDepositBonusTransaction() {

        $adminUserId = 1;
        $playerId = 111;
        $amount = 100.00;
        $beforeBalance = 99.99;
        $playerPromoId = 888;

        $rst = $this->transactions->createDepositBonusTransaction($adminUserId, $playerId, $amount, $beforeBalance, $playerPromoId);

        var_dump($rst);

        //$this->destroy_test_data();
    }

    private function insertAutoCashbackTransaction() {

        // $player_id = 17;
        // $cashback_amount = 22;
        // $from_id = 1;
        // $total_date = 895464;

        // $rst = $this->transactions->insertAutoCashbackTransaction($player_id, $cashback_amount, $from_id, $total_date);

        // var_dump($rst);

        //$this->destroy_test_data();
    }

    private function destroy_test_data() {
        // $this->db->where('date', '2080-11-11');
        // $this->db->delete('daily_player_trans');
    }

    private function tmp() {

    }

    private function testDepositInfo(){
        $row=$this->transactions->getAvailableDepositInfoByFreq($playerId, $frequency, $today, $min, $max,
            [Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET,
            Transactions::TRANSFER_TO_MAIN_FROM_BALANCE_AFFILIATE,
            Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET,
            Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET,
            Transactions::WITHDRAWAL]);


    }
}

///end of file/////////////