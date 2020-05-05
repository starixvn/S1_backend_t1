<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_game_logs_unsettle_201608112348 extends CI_Migration {

	private $tableName = 'game_logs_unsettle';

	public function up() {
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
			'game_type_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_description_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'game_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'room' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'table' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'result_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'rent' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'start_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'end_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'external_log_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'note' => array(
				'type' => 'VARCHAR',
				'constraint' => '1000',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'flag' => array(
				'type' => 'INT',
				'null' => true,
				'default'=>1,
			),
			'has_both_side' => array(
				'type' => 'INT',
				'null' => true,
				// 'default'=>1,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'trans_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'trans_type' => array(
				'type' => 'INT',
				'null' => true,
			),
			'win_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'loss_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			//add status
			'status' => array(
				'type' => 'INT',
				'null' => true,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);

		//index
		$this->db->query('create index idx_external_uniqueid on game_logs_unsettle(external_uniqueid)');
		$this->db->query('create index idx_end_at on game_logs_unsettle(end_at)');
		$this->db->query('create index idx_player_id on game_logs_unsettle(player_id)');
		$this->db->query('create index idx_game_platform_id on game_logs_unsettle(game_platform_id)');
		$this->db->query('create index idx_game_type_id on game_logs_unsettle(game_type_id)');
		$this->db->query('create index idx_game_description_id on game_logs_unsettle(game_description_id)');

	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}

///END OF FILE//////////