<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_affiliates_201604161724 extends CI_Migration {

	private $tableName = 'affiliates';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'levelNumber' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
		));

		$this->dbforge->add_column($this->tableName, array(
			'countSub' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
		));

		$this->dbforge->add_column($this->tableName, array(
			'countPlayer' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
		));

		$this->dbforge->add_column($this->tableName, array(
			'totalPlayerBet' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		));

		$this->dbforge->add_column($this->tableName, array(
			'totalPlayerWin' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		));

		$this->dbforge->add_column($this->tableName, array(
			'totalPlayerLoss' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		));

		$this->dbforge->add_column($this->tableName, array(
			'totalPlayerDeposit' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		));

		$this->dbforge->add_column($this->tableName, array(
			'totalPlayerWithdraw' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		));

		//fix data
		$this->load->model(array('affiliatemodel'));
		$this->affiliatemodel->startTrans();

		$this->affiliatemodel->fixCountOfAll();

		if (!$this->affiliatemodel->endTransWithSucc()) {
			throw new Exception('update affiliate failed');
		}

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'levelNumber');
		$this->dbforge->drop_column($this->tableName, 'countSub');
		$this->dbforge->drop_column($this->tableName, 'countPlayer');
		$this->dbforge->drop_column($this->tableName, 'totalPlayerBet');
		$this->dbforge->drop_column($this->tableName, 'totalPlayerWin');
		$this->dbforge->drop_column($this->tableName, 'totalPlayerLoss');
		$this->dbforge->drop_column($this->tableName, 'totalPlayerDeposit');
		$this->dbforge->drop_column($this->tableName, 'totalPlayerWithdraw');
	}
}

///END OF FILE////////////////