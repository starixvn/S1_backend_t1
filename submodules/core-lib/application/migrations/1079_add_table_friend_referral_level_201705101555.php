<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_friend_referral_level_201705101555 extends CI_Migration {

	public function up() {
		$fields = array(
			"id" => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
			),
			'min_betting' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'max_betting' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'min_volid_player' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'max_volid_player' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'traditional_betting_commission_rate' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'financial_betting_commission_rate' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('friend_referral_level');

	}

	public function down() {
		$this->dbforge->drop_table('friend_referral_level');
	}
}