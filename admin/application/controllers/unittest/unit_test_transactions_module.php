<?php

trait unit_test_transactions_module{

    public function unit_test_transactions_createDepositTransaction(){
        $this->utils->printToConsole('start '.__METHOD__);
        $this->load->model(['transactions', 'sale_order']);
        $row=$this->unit_test_sale_order_helper_search_first_order();
        $playerId=$row['player_id'];

        $flag = Transactions::MANUAL;
        $adminUserId=1;
        $saleOrder=$this->sale_order->getSaleOrderById($row['id']);
        $beforeBalance=null;
        $totalBeforeBalance=null;
        $success=$this->dbtransOnly(function()
                use($saleOrder, $adminUserId, $beforeBalance, $flag, $totalBeforeBalance) {
            $success=$this->transactions->createDepositTransaction($saleOrder, $adminUserId, $beforeBalance,
                $flag, $totalBeforeBalance);
            $this->assertFalse($success, 'without lock');
            return $success;
        });
        $this->assertFalse($success, 'without lock');

        $success=$this->lockAndTransForPlayerBalance($playerId, function()
                use($saleOrder, $adminUserId, $beforeBalance, $flag, $totalBeforeBalance){
            $result=$this->transactions->createDepositTransaction($saleOrder, $adminUserId, $beforeBalance,
                $flag, $totalBeforeBalance);
            $success=!!$result;
            $this->assertTrue($success, 'with lock');
            return $success;
        });
        $this->assertTrue($success, 'with lock');

        $this->printAssertionSummary();
    }

    public function unit_test_transactions_createDepositTransactionByAgent(){
        $this->utils->printToConsole('start '.__METHOD__);
        $this->load->model(['transactions']);
        //first player
        $this->db->select('playerId')->from('player')->where('deleted_at is null', null, false)->limit(1);
        $row=$this->transactions->runOneRowArray();
        $playerId=$row['playerId'];
        //first agent
        $this->db->select('agent_id')->from('agency_agents')->where('status','active')->limit(1);
        $row=$this->transactions->runOneRowArray();
        $amount=10;
        $agentId=$row['agent_id'];
        $success=$this->dbtransOnly(function()
                use($playerId, $amount, $agentId) {
            $success=$this->transactions->createDepositTransactionByAgent($playerId, $amount, $agentId);
            $this->assertFalse($success, 'without lock');
            return $success;
        });
        $this->assertFalse($success, 'without lock');

        $success=$this->lockAndTransForPlayerBalance($playerId, function()
                use($playerId, $amount, $agentId){
            $result=$this->transactions->createDepositTransactionByAgent($playerId, $amount, $agentId);
            $success=!!$result;
            $this->assertTrue($success, 'with lock');
            return $success;
        });
        $this->assertTrue($success, 'with lock');

        $this->printAssertionSummary();
    }

    public function unit_test_transactions_createWithdrawTransactionByAgent(){
        $this->utils->printToConsole('start '.__METHOD__);
        $this->load->model(['transactions']);
        //first player
        $this->db->select('playerId')->from('player')->where('deleted_at is null', null, false)->limit(1);
        $row=$this->transactions->runOneRowArray();
        $playerId=$row['playerId'];
        //first agent
        $this->db->select('agent_id')->from('agency_agents')->where('status','active')->limit(1);
        $row=$this->transactions->runOneRowArray();
        $amount=10;
        $agentId=$row['agent_id'];
        $success=$this->dbtransOnly(function()
                use($playerId, $amount, $agentId) {
            $success=$this->transactions->createWithdrawTransactionByAgent($playerId, $amount, $agentId);
            $this->assertFalse($success, 'without lock');
            return $success;
        });
        $this->assertFalse($success, 'without lock');

        $success=$this->lockAndTransForPlayerBalance($playerId, function()
                use($playerId, $amount, $agentId){
            $result=$this->transactions->createWithdrawTransactionByAgent($playerId, $amount, $agentId);
            $success=!!$result;
            $this->assertTrue($success, 'with lock');
            return $success;
        });
        $this->assertTrue($success, 'with lock');

        $this->printAssertionSummary();
    }

    public function unit_test_transactions_all(){
        $this->unit_test_transactions_createDepositTransaction();
        $this->unit_test_transactions_createDepositTransactionByAgent();
        $this->unit_test_transactions_createWithdrawTransactionByAgent();
    }

}
