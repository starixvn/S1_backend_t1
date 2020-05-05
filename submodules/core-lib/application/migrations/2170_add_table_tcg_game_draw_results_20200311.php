<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_tcg_game_draw_results_20200311 extends CI_Migration {

	private $tableName = 'tcg_game_draw_results';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'numero' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
            ),
            'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
            ),
            'win_no' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
            ),
            'winning_time' => array(
				'type' => 'DATETIME',
                'null' => true,
            ),
			# additional info
			'unique_id' => array(
				'type' => 'BIGINT',
				'null' => true,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
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
	        $this->player_model->addIndex($this->tableName, 'idx_game_code', 'game_code');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_as_unique_id', 'unique_id');

	    }
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
