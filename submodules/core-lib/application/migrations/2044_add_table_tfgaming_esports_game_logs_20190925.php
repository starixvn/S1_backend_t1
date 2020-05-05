<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_tfgaming_esports_game_logs_20190925 extends CI_Migration {

	private $tableName = 'tfgaming_esports_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'id_number' => array(
				'type' => 'VARCHAR',
				'constraint' => '36',
				'null' => true,
			),
			'odds' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'game_type_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '36',
                'null' => true,
			),
			'game_market_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'bet_type_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '36',
				'null' => true,
			),
			'competition_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'event_id' => array(
				'type' => 'INT',
				'constraint' => '16',
				'null' => true,
			),
			'event_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
                'null' => true,
			),
			'event_datetime' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
                'null' => true,
			),
			'date_created' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
                'null' => true,
			),
			'settlement_datetime' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true,
			),
			'bet_selection' => array(
				'type' => 'VARCHAR',
				'constraint' => '36',
                'null' => true,
			),
			'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '11',
				'null' => true,
			),
			'amount' => array(
                'type' => 'DOUBLE',
				'null' => true,
			),
			'settlement_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '36',
				'null' => true,
			),
			'result_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '36',
                'null' => true,
			),
			'earnings' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'handicap' => array(
				'type' => 'DOUBLE',
                'null' => true,
			),
			'type' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'member_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'is_combo' => array(
				'type' => 'TINYINT',
				'null' => true
			),
			'tickets' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true
			),
			'is_unsettled' => array(
				'type' => 'TINYINT',
				'null' => true
			),


			
			# SBE additional info
            'response_result_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            )
		);

		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->tableName);
			# Add Index
	        $this->load->model('player_model');
	        $this->player_model->addIndex($this->tableName, 'idx_event_datetime', 'event_datetime');
	        $this->player_model->addIndex($this->tableName, 'idx_date_created', 'date_created');
	        $this->player_model->addIndex($this->tableName, 'idx_settlement_datetime', 'settlement_datetime');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_id_number', 'id_number');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
