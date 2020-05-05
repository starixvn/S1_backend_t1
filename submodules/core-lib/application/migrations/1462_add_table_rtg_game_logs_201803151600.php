<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_rtg_game_logs_201803151600 extends CI_Migration {

	private $tableName = 'rtg_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'login' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'player_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'session_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'date_started' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'date_finished' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'game_number' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'game_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'machine_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameid_machineid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'machine_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet_amount' => array(
                'type' => 'double',
                'null' => true,
			),
			'bet_amount_featured_guarantee' => array(
                'type' => 'double',
                'null' => true,
			),
			'bet_description' => array(
                'type' => 'TEXT',
                'null' => true,
			),
			'payout' => array(
                'type' => 'double',
                'null' => true,
			),
			'jackpot_contribution_mini' => array(
                'type' => 'double',
                'null' => true,
			),
			'jackpot_contribution_minor' => array(
                'type' => 'double',
                'null' => true,
			),
			'jackpot_contribution_major' => array(
                'type' => 'double',
                'null' => true,
			),
			'jackpot_win_mini' => array(
                'type' => 'double',
                'null' => true,
			),
			'jackpot_win_minor' => array(
                'type' => 'double',
                'null' => true,
			),
			'jackpot_win_major' => array(
                'type' => 'double',
                'null' => true,
			),
			'game_mode' => array(
                'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'currency_code' => array(
                'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'currency_symbol' => array(
                'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'session_machine_name' => array(
                'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'seamless_reference_id' => array(
                'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'balance_before' => array(
                'type' => 'double',
                'null' => true,
			),
			'balance_after' => array(
                'type' => 'double',
                'null' => true,
			),
			'bet_ip_address' => array(
                'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'jackpot_type' => array(
                'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'client_type' => array(
                'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('external_uniqueid');
		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
