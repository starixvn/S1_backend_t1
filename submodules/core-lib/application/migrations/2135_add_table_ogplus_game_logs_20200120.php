<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ogplus_game_logs_20200120 extends CI_Migration {

	private $tableName = 'ogplus_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'transaction_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
            ),
			'membername' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gamename' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bettingcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bettingdate' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'gameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'roundno' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
            'game_information' => array(
				'type' => 'TEXT',
				'null' => true,
            ),
            'result' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'winloseresult' => array(
                'type' => 'double',
                'null' => true,
            ),
            'bettingamount' => array(
                'type' => 'double',
                'null' => true,
            ),
            'validbet' => array(
                'type' => 'double',
                'null' => true,
			),
			'winloseamount' => array(
                'type' => 'double',
                'null' => true,
            ),
            'balance' => array(
                'type' => 'double',
                'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'handicap' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gamecategory' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'settledate' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'remark' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
            ),
			'vendor_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
            ),


			'shoehandnumber' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
            ),
			'tablenumber' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
            ),
			'bet_place' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
            ),
			'result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
            ),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
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
            $this->player_model->addIndex($this->tableName, 'idx_transaction_id', 'transaction_id');
            $this->player_model->addIndex($this->tableName, 'idx_gameid', 'gameid');
            $this->player_model->addIndex($this->tableName, 'idx_gamename', 'gamename');
            $this->player_model->addIndex($this->tableName, 'idx_membername', 'membername');	       
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
        }
        
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
