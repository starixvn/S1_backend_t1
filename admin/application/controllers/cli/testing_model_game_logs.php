<?php
require_once dirname(__FILE__) . '/base_testing.php';

class Testing_model_game_logs extends BaseTesting {

    public function init() {
        $this->load->model('game_logs');
    }

    ## all tests route through this function
    public function testTarget($methodName) {
        $this->init();
        $this->$methodName();
    }

    ## Invokes all tests defined below. A test function's name should begin with 'test'
    public function testAll() {
        $this->init();
        $classMethods = get_class_methods($this);
        $excludeMethods = array('test', 'testTarget', 'testAll');
        foreach ($classMethods as $method) {
            if (strpos($method, 'test') !== 0 || in_array($method, $excludeMethods)) {
                continue;
            }

            $this->$method();
        }
    }

    ## Actual tests
    private function test_get_bet_percentage_by_platform() {
        $agent_id = 0;
        // The player ids of our game log data
        $player_ids = [56933, 56954, 56955, 130856, 130860, 130906, 130909, 130922, 130923, 130925, 130934, 130952, 130959, 130965, 130985, 130998, 130999, 131000, 131005, 131006, 131007, 131015];
        // The dates where we have game log data
        $startDate = '2017-10-01';
        $endDate = '2017-10-31';
        $percentage = $this->game_logs->get_bet_percentage_by_platform_and_type($agent_id, $player_ids, $startDate, $endDate);
        $this->test(!empty($percentage), true, "Get bet percentage success");
    }

}

///end of file/////////////