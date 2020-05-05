<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_sbobetseamless_thb_gamelogs_20200210 extends CI_Migration {

	private $origTableName = 'sbobet_seamless_thb_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'playerid' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'betoption' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'markettype' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'hdp' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'odds' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'league' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'match' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'winlostdate' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'modifydate' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'livescore' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'htscore' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'ftscore' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'refno' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'sporttype' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'ordertime' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'donetime' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'customeizedbettype' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'oddsstyle' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'stake' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'actualstake' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'winlose' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'turnover' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'ishalfwonlose' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'islive' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'maxwinwithoutactualstake' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'accountid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'tablename' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'producttype' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'customeizedbettype' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'subbet' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'external_game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
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
	        $this->player_model->addIndex($this->origTableName, 'idx_sbbslthb_username', 'username');
	        $this->player_model->addIndex($this->origTableName, 'idx_sbbslthb_sporttype', 'sporttype');
	        $this->player_model->addIndex($this->origTableName, 'idx_sbbslthb_ordertime', 'ordertime');
	        $this->player_model->addIndex($this->origTableName, 'idx_sbbslthb_donetime', 'donetime');
	        $this->player_model->addUniqueIndex($this->origTableName, 'idx_sbbslthb_refno', 'refno');
	        $this->player_model->addUniqueIndex($this->origTableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		$this->dbforge->drop_table($this->origTableName);
	}
}
