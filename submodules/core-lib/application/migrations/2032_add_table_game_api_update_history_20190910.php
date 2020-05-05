<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_game_api_update_history_20190910 extends CI_Migration {

	private $tableName = 'game_api_update_history';
	private $external_system_tbl = 'external_system';

	public function up() {
		$game_api_update_history_fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'user_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'action' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			
			'system_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'note' => array(
				'type' => 'VARCHAR',
				'constraint' => '1000',
				'null' => true,
			),
			'last_sync_datetime' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'last_sync_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'last_sync_details' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'system_type' => array(
				'type' => 'INT',
				'null' => true,
			),
			'category' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'amount_float' => array(
				'type' => 'INT',
				'null' => true,
			),
			'live_url' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'sandbox_url' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'second_url' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'live_key' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'sandbox_key' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'live_secret' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'sandbox_secret' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'live_mode' => array(
				'type' => 'INT',
				'null' => true,
			),
			'live_account' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'sandbox_account' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'system_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => true,
			),
			'class_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'local_path' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'manager' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'game_platform_rate' => array(
				'type' => 'INT',
				'null' => true,
			),
			'extra_info' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'sandbox_extra_info' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'allow_deposit_withdraw' => array(
				'type' => 'INT',
				'null' => true,
			),
			'created_on' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'maintenance_mode' => array(
				'type' => 'INT',
				'null' => true,
			),
			'pause_sync' => array(
				'type' => 'INT',
				'null' => true,
			),
			'class_key' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),

		);

		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->add_field($game_api_update_history_fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->tableName);
			# Add Index
			$this->load->model('player_model');
			$this->player_model->addIndex($this->tableName, 'idx_createOn', 'created_on');
			$this->player_model->addIndex($this->tableName, 'idx_updated_at', 'updated_at');
			$this->player_model->addIndex($this->tableName, 'idx_game_platform_id', 'game_platform_id');
			$this->player_model->addIndex($this->tableName, 'idx_action', 'action');        
		}
	    // remove on update at created_on field
		if($this->db->table_exists($this->external_system_tbl)){
			//note: modify_column() function not working on this part, i used raw sql
			$this->db->query("ALTER TABLE {$this->external_system_tbl} MODIFY COLUMN `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ");
		}
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);	
		}
		if($this->db->table_exists($this->external_system_tbl)){
			$this->db->query("ALTER TABLE {$this->external_system_tbl} MODIFY COLUMN `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ");
		}
	}
}
