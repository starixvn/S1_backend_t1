<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_redtiger_game_logs_20190618 extends CI_Migration {

	private $tableName = 'redtiger_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'ugsbetid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'txid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'betid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'beton' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'betclosedon' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'betupdatedon' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'timestamp' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'roundid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'roundstatus' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'userid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'riskamt' => array(
                'type' => 'double',
                'null' => true,
			),
			'winamt' => array(
                'type' => 'double',
                'null' => true,
			),
			'winloss' => array(
				'type' => 'double',
                'null' => true,
			),
			'result_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'beforebal' => array(
				'type' => 'double',
                'null' => true,
			),
			'postbal' => array(
				'type' => 'double',
                'null' => true,
			),
			'cur' => array(
                'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
            ),
            'gameprovider' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gameprovidercode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gamename' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'platformtype' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'ipaddress' => array(
				'type' => 'VARCHAR',
				'constraint' => '40',
				'null' => true,
			),
			'bettype' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
				'null' => true,
			),
			'playtype' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'playertype' => array(
				'type' => 'INT',
				'constraint' => '10',
				'null' => true,
			),
			'turnover' => array(
				'type' => 'double',
                'null' => true,
			),
			'validbet' => array(
				'type' => 'double',
                'null' => true,
			),
			'jackpotcontribution' => array(
				'type' => 'double',
                'null' => true,
			),
			'jackpotid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'jackpotwinamt' => array(
				'type' => 'double',
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


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('userid');
		$this->dbforge->create_table($this->tableName);
		# Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_redtiger_userid', 'userid');
        $this->player_model->addIndex($this->tableName, 'idx_redtiger_beton', 'beton');
        $this->player_model->addIndex($this->tableName, 'idx_redtiger_betclosedon', 'betclosedon');
        $this->player_model->addIndex($this->tableName, 'idx_redtiger_betupdatedon', 'betupdatedon');
        $this->player_model->addIndex($this->tableName, 'idx_redtiger_timestamp', 'timestamp');
        $this->player_model->addIndex($this->tableName, 'idx_redtiger_ugsbetid', 'ugsbetid',true);
        $this->player_model->addIndex($this->tableName, 'idx_redtiger_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
