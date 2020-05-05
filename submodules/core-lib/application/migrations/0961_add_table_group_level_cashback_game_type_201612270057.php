<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_group_level_cashback_game_type_201612270057 extends CI_Migration {

	public function up() {

		$db_true=1;

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'vipsetting_cashbackrule_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'percentage' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => $db_true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),

		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('game_platform_id');

		$this->dbforge->create_table('group_level_cashback_game_platform');

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'vipsetting_cashbackrule_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_type_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'percentage' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => $db_true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),

		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('game_type_id');

		$this->dbforge->create_table('group_level_cashback_game_type');

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'vipsetting_cashbackrule_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_description_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'percentage' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => $db_true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),

		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('game_description_id');

		$this->dbforge->create_table('group_level_cashback_game_description');

		//convert old data to new tables
		$this->load->model(['group_level']);
		$this->group_level->convertToNewCashbackPercentage();

	}

	public function down() {
		$this->dbforge->drop_table('group_level_cashback_game_platform');
		$this->dbforge->drop_table('group_level_cashback_game_type');
		$this->dbforge->drop_table('group_level_cashback_game_description');
	}
}
