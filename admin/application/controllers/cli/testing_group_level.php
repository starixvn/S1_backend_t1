<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_group_level extends BaseTesting {
//should overwrite init function
    public function init() {
        //init your model or lib
        $this->load->model('group_level');
    }
    //should overwrite testAll
    public function testAll() {
        //init first
        $this->init();
        //call your test function
        $this->testTotalCashbackDaily();
    }
    //it's your real test function
    private function testTotalCashbackDaily() {
        $this->group_level->totalCashbackDaily();
    }
}
///end of file/////////////
