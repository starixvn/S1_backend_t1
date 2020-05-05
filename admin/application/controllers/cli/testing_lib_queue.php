<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_queue extends BaseTesting {

	public function init() {
		$this->load->library('lib_queue');
		$this->load->model(array('queue_result'));
		$this->test($this->lib_queue != null, true, 'init lib_queue');
	}

	public function testAll() {
		$this->init();
		$this->testAddApiJob();
	}

	public function testAddApiJob() {
		$playerId = 112;
		$state = 'get back';
		$token = $this->lib_queue->addApiJob(AG_API, 'isPlayerExist', array('test1'),
			Queue_result::CALLER_TYPE_PLAYER, $playerId, $state);
		$this->test(empty($token), false, 'test add api job: isPlayerExist');
		// $this->test(, '', 'test ip:' . $ip);
	}

}

///END OF FILE/////////////