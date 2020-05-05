<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_pt_v2_game_logs_20190925 extends CI_Migration {

	private $tableName = 'pt_v2_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'reference_no' => array(
				'type' => 'INT',
				'null' => true,
			),
			'entity_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'kiosk_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_server' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gamzo_player_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_shortcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'game_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'is_win' => array(
				'type' => 'INT',
				'null' => true,
			),
			'bet' => array(
				'type' => 'double',
                'null' => true,
			),
			'win' => array(
				'type' => 'double',
                'null' => true,
			),
			'progressive_bet' => array(
				'type' => 'double',
                'null' => true,
			),
			'progressive_win' => array(
				'type' => 'double',
                'null' => true,
			),
			'game_server_reference_1' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
            'bet_timestamp' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
			'bet_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'game_snapshot_token' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'game_snapshot' => array(
				'type' => 'TEXT',
				'null' => true,
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
	        $this->player_model->addIndex($this->tableName, 'idx_reference_no', 'reference_no');
	        $this->player_model->addIndex($this->tableName, 'idx_entity_name', 'entity_name');
	        $this->player_model->addIndex($this->tableName, 'idx_gamzo_player_name', 'gamzo_player_name');
	        $this->player_model->addIndex($this->tableName, 'idx_bet_datetime', 'bet_datetime');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
