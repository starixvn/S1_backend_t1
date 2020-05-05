<?php
defined('BASEPATH') OR exit('No direct script access allowed');

abstract class BaseTesting extends CI_Controller {

	public function __construct() {
		parent::__construct();
		//command only except debug mode
		if (!$this->input->is_cli_request() && !$this->utils->isDebugMode()) {
			//quit
			// echo 'Not allowed';
			show_error('Not allowed', 405);
			exit;
		}

		$this->load->library("unit_test");
	}

	public function index($target = null) {
		set_time_limit(0);

		//set to testing_debug_log
		$this->config->set_item('app_debug_log', $this->config->item('testing_debug_log'));
		if (!empty($target)) {
			$this->testTarget($target);
			// $this->$target();
			// call_user_func(array($this, $target));
		} else {
			$this->testAll();
		}
		$this->load->view('test_result');
	}

	protected function test($actually, $expectd, $testName, $notes = null, &$result=false) {
		$rlt=$this->unit->run($actually, $expectd, $testName, $notes, $result);

		if(!$result){
			$this->returnText('failed error');
			$msg= "Test [$testName] failed: Actual [$actually] => Expected [$expectd], Notes: [$notes]";
			$this->utils->error_log($msg);
			show_error($msg);
			exit(1);
		}

		return $rlt;
	}

	protected function getFirstPlayer($username = null) {
		$this->load->model("player");
		$qry = $this->player->db->from('player')->order_by('playerId','asc')->get();
		if ($qry && $qry->num_rows() > 0) {
			$player = $qry->result()[0];
			if (!empty($username)) {
				$this->db->where('playerId', $player->playerId)->update('player', array('username' => $username));
				$player->username = $username;
			}
			return $player;
		}
		return null;
	}

	abstract public function init();
	abstract public function testAll();

	public function testTarget($methodName) {
		return null;
	}

	public function getConfig($key) {
		return $this->config->item($key);
	}

	public function getFirstBanklist($platformId) {
		$this->db->from('bank_list')->where('external_system_id', $platformId);
		$qry = $this->db->get();
		return $qry->row();
	}

	protected function getFirstCollectionAccount() {

		$this->load->model("payment_account");

		$qry = $this->db->get_where('payment_account',['status'=>1], 1);

		return $this->payment_account->getOneRow($qry);

		// $qry = $this->player->db->get('player', 1);
		// if ($qry && $qry->num_rows() > 0) {
		// 	$player = $qry->result()[0];
		// 	if (!empty($username)) {
		// 		$this->db->where('playerId', $player->playerId)->update('player', array('username' => $username));
		// 		$player->username = $username;
		// 	}
		// 	return $player;
		// }
		// return null;
	}

	public function returnText($msg, $return = true) {
		$this->output->append_output($msg . ($return ? "\n" : ''));
	}

}
