<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_pragmaticplay_vnd3_gamelogs_20190809 extends CI_Migration {

	private $tableName = 'pragmaticplay_vnd3_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'sbeplayerid' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'playerid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'extplayerid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'playsessionid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'timestamp' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'referenceid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'amount' => array(
                'type' => 'DOUBLE',
                'null' => true
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
            'related_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
	       'parent_session_id' => array(		
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ),
           'start_date' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'end_date' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'status' => array(	
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => true
            ),
           'type_game_round' => array(	
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => true
            ),
           'bet' => array(           
                'type' => 'DOUBLE',
                'null' => true
            ),
            'win' => array(         
                'type' => 'DOUBLE',
                'null' => true
            ),
           'jackpot' => array(        
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
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
			'result_time' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
		);

		if(!$this->db->table_exists($this->tableName)){

			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->tableName);

	        $this->load->model('player_model');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_pp_vnd3_game_logs_external_uniqueid', 'external_uniqueid');
	        $this->player_model->addIndex($this->tableName, 'idx_pp_vnd3_md5_sum', 'md5_sum');
	        $this->player_model->addIndex($this->tableName, 'idx_pp_vnd3_result_time', 'result_time');
	        $this->player_model->addIndex($this->tableName, 'idx_pp_vnd3_playerid', 'playerid');
		}
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
