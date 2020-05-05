<?php
//always include base testing
require_once dirname(__FILE__) . '/base_testing.php';

//always extends from BaseTesting
class Testing_model_daily_player_trans extends BaseTesting {

    //should overwrite init function
    public function init() {
        //init your model or lib
        $this->load->model('daily_player_trans');
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
    private function update_today() {
        $transactionDetails = array('to_id' => 111,
            'from_type' => 1,
            'created_at' => '2080-11-11 00:00:00',
            'amount' => 120.00,
            'transaction_type' => 1,
         );
        $rst = $this->daily_player_trans->update_today($transactionDetails);
        
        var_dump($rst);
        
        //$this->destroy_test_data();
    }

    private function get_existing_trans_today() {
        $data = array(
           'player_id' => '111' ,
           'date' => '2080-11-11' ,
           'trans_type' => '1'
        );

        $this->db->insert('daily_player_trans', $data); 

        $rst = $this->daily_player_trans->get_existing_trans_today(111,'2080-11-11',5);
        
        var_dump($rst);

        $this->destroy_test_data(); 

    }

    private function get_today_withdraw() {
        //$this->update_today();
        $playerId = 111;
        $rst = $this->daily_player_trans->get_today_withdraw($playerId);     
        echo $rst->trans_amount;
        //$this->destroy_test_data(); 

    }

    private function destroy_test_data() {
        $this->db->where('date', '2080-11-11');
        $this->db->delete('daily_player_trans'); 
    }

    private function tmp() {

    }

}

///end of file/////////////