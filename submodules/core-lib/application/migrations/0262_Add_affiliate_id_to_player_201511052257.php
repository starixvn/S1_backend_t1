<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_affiliate_id_to_player_201511052257 extends CI_Migration {

	private $tableName = 'player';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'affiliateId' => array(
				'type' => 'INT',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'affiliateId');
	}
}