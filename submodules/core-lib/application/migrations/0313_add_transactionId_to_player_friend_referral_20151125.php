<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_transactionId_to_player_friend_referral_20151125 extends CI_Migration {

	private $tableName = 'playerfriendreferral';

	public function up() {
		$fields = array(
			'transactionId' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
		$this->db->query('create unique index idx_playerfriendreferral_transactionId on playerfriendreferral(transactionId)');
	}

	public function down() {
		$this->db->query('drop index idx_playerfriendreferral_transactionId on playerfriendreferral');
		$this->dbforge->drop_column($this->tableName, 'transactionId');
	}
}