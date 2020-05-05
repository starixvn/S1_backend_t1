<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_gpk_gamelogs_20200212 extends CI_Migration {

	private $origTableName = 'gpk_gamelogs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'AgentId' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
				'null' => true,
			),
			'UserAccount' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
				'null' => true,
			),
			'GameId' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
				'null' => true,
			),
			'WagersId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'GameAccount' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'GameWagersId' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'Bet' => array(
                'type' => 'DOUBLE',
                'null' => true,
			),
			'ValidBet' => array(
                'type' => 'DOUBLE',
                'null' => true,
			),
			'PayOff' => array(
                'type' => 'DOUBLE',
                'null' => true,
			),
			'BetTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'BalanceTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'GameGroupType' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
				'null' => true,
			),
			'UpdateTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'GameSupplier' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
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

	    if(!$this->db->table_exists($this->origTableName)){
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->origTableName);
			# Add Index
	        $this->load->model('player_model');
	        $this->player_model->addIndex($this->origTableName, 'idx_AgentId', 'AgentId');
	        $this->player_model->addIndex($this->origTableName, 'idx_UserAccount', 'UserAccount');
	        $this->player_model->addIndex($this->origTableName, 'idx_GameId', 'GameId');
	        $this->player_model->addIndex($this->origTableName, 'idx_GameAccount', 'GameAccount');
	        $this->player_model->addIndex($this->origTableName, 'idx_GameWagersId', 'GameWagersId');
	        $this->player_model->addIndex($this->origTableName, 'idx_md5_sum', 'md5_sum');
	        $this->player_model->addUniqueIndex($this->origTableName, 'idx_WagersId', 'WagersId');
	        $this->player_model->addUniqueIndex($this->origTableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		$this->dbforge->drop_table($this->origTableName);
	}
}
