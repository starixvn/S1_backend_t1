<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_agent_report_20190920 extends CI_Migration {

	private $tableName = 'agency_agent_report_hourly';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'agent_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
            ),
            'agent_total_deposit' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'agent_total_withdrawal' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
			'summary_date' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'date_hour' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'agent_total_bet' => array(
				'type' => 'DOUBLE',
                'null' => true,
			),
			'agent_total_bet_for_cashback' => array(
				'type' => 'DOUBLE',
                'null' => true,
			),
			'agent_total_real_betting_amount' => array(
				'type' => 'DOUBLE',
                'null' => true,
			),
			'agent_total_win' => array(
				'type' => 'DOUBLE',
                'null' => true,
			),
			'agent_total_loss' => array(
				'type' => 'DOUBLE',
                'null' => true,
			),
			'agent_net_gaming' => array(
				'type' => 'DOUBLE',
                'null' => true,
			),
			'currency_key' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => false,
			),
			'unique_key' => array(
				'type' => 'VARCHAR',
				'constraint' => '40',
				'null' => false,
			),
			'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' => array(
                'null' => false,
            )
		);

		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->tableName);
			# Add Index
			$this->load->model('player_model');
			$this->player_model->addIndex($this->tableName, 'idx_summary_date', 'summary_date');
			$this->player_model->addIndex($this->tableName, 'idx_date_hour', 'date_hour');
			$this->player_model->addIndex($this->tableName, 'idx_created_at', 'created_at');
			$this->player_model->addIndex($this->tableName, 'idx_updated_at', 'updated_at');
			$this->player_model->addIndex($this->tableName, 'idx_agent_id', 'agent_id');
			$this->player_model->addUniqueIndex($this->tableName, 'idx_unique_key', 'unique_key');
		}
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);	
		}
	}
}
