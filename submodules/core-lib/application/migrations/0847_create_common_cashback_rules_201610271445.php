<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_common_cashback_rules_201610271445 extends CI_Migration {

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'min_bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'max_bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('common_cashback_rules');

		$fields = array(
			//link to common_cashback_rules
			'rule_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('common_cashback_game_rules', $fields);

		$this->db->query('create index idx_rule_id on common_cashback_game_rules(rule_id)');

	}

	public function down() {
		$this->dbforge->drop_table('common_cashback_rules');

		$this->dbforge->drop_column('common_cashback_game_rules', 'rule_id');
	}
}
