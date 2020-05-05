<?php

trait unit_test_sale_order_module{

    public function unit_test_sale_order_updateSaleOrderResult(){
        $this->utils->printToConsole('start '.__METHOD__);
        $this->load->model(['sale_order']);
        //search available order
        // $this->db->select('id, status, player_id, payment_account_id')->from('sale_orders')->limit(1);
        // $row=$this->sale_order->runOneRowArray();
        $row=$this->unit_test_sale_order_helper_search_first_order();
        $this->utils->debug_log('get order', $row);
        $id=$row['id'];
        $playerId=$row['player_id'];
        //check status
        if($row['status']!=Sale_order::STATUS_PROCESSING){
            //update to processing
            $this->db->where('id', $id)->set('status', Sale_order::STATUS_PROCESSING);
            $success=$this->sale_order->runAnyUpdate('sale_orders');
            $this->assertTrue($success, 'update order status');
        }
        //check payment account
        if(empty($row['payment_account_id'])){
            //update available
            $this->db->select('id')->from('payment_account')->where('status',Sale_order::STATUS_NORMAL)->limit(1);
            $accRow=$this->sale_order->runOneRowArray();
            $this->assertNotEmpty($accRow);
            $this->db->where('id', $id)->set('payment_account_id', $accRow['id']);
            $success=$this->sale_order->runAnyUpdate('sale_orders');
            $this->assertTrue($success, 'update order payment_account_id');
        }
        $actionLog='unit test log';
        $show_reason_to_player=false;
        $newStatus=Sale_order::STATUS_SETTLED;
        $extra_info=[];
        $success=$this->dbtransOnly(function()
                use($id,$actionLog,$show_reason_to_player, $newStatus, $extra_info){
            $success=$this->sale_order->updateSaleOrderResult($id, $actionLog,
                $show_reason_to_player, $newStatus, $extra_info);
            $this->assertFalse($success);
            return $success;
        });
        $this->assertFalse($success);
        //validate status
        $this->assertEquals(Sale_order::STATUS_PROCESSING, $this->unit_test_sale_order_helper_search_order_status($id));

        $success=$this->lockAndTransForPlayerBalance($playerId, function()
                use($id,$actionLog,$show_reason_to_player, $newStatus, $extra_info){
            $success=$this->sale_order->updateSaleOrderResult($id, $actionLog,
                $show_reason_to_player, $newStatus, $extra_info);
            $this->assertTrue($success);
            return $success;
        });

        $this->assertTrue($success);

        //validate status
        $this->assertEquals(Sale_order::STATUS_SETTLED, $this->unit_test_sale_order_helper_search_order_status($id));
        $this->printAssertionSummary();
    }

    protected function unit_test_sale_order_helper_search_order_status($id){
        $this->db->select('id, status')->from('sale_orders')->where('id', $id);
        $newRow=$this->sale_order->runOneRowArray();
        return intval($newRow['status']);
    }

    protected function unit_test_sale_order_helper_search_first_order(){
        //search available order
        $this->db->select('id, status, player_id, payment_account_id')->from('sale_orders')->limit(1);
        $row=$this->sale_order->runOneRowArray();
        $this->assertNotEmpty($row, 'not found any order');
        return $row;
    }

    public function unit_test_sale_order_all(){
        $this->unit_test_sale_order_updateSaleOrderResult();
    }

}
