<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_bet_limit_template_list_201608180013 extends CI_Migration {

	private $tableName = 'bet_limit_template_list';

	public function up() {

		$this->dbforge->modify_column('game_logs_unsettle', [
				'id'=>[
					'name'=>'id',
					'type' => 'BIGINT',
				]
			]);

		$fields=array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'template_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'bet_limit_json' => array(
				'type' => 'TEXT',
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
			'note' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => true,
				'default'=>1,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);

		//index
		$this->db->query('create index idx_game_platform_id on bet_limit_template_list(game_platform_id)');
		$this->db->query('create index idx_agent_id on bet_limit_template_list(agent_id)');

	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}

///END OF FILE//////////