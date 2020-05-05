<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_booming_seamless_game_logs_20190823 extends CI_Migration {

	private $tableName = 'boomingseamless_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'round' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'type' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
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
			'freespins_details' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'custome_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'after_balance' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bonus' => array(
                'type' => 'BOOLEAN',
                'null' => true,
                'default' => 0,
			),
			'game_date' => array(
				'type' => 'DATE',
				'null' => true,
			),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
		);

		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->tableName);
			# Add Index
	        $this->load->model('player_model');
        	$this->player_model->addIndex($this->tableName, 'idx_player_id', 'player_id');
        	$this->player_model->addIndex($this->tableName, 'idx_game_id', 'game_id');
        	$this->player_model->addIndex($this->tableName, 'idx_game_date', 'game_date');
	        $this->player_model->addIndex($this->tableName, 'idx_md5_sum', 'md5_sum');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
