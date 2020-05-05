<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_suncity_game_logs_201802071725 extends CI_Migration {

	private $tableName = 'suncity_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'ugsbetid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'txid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'betid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'beton' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'betclosedon' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'betupdatedon' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'timestamp' => array(
                'type' => 'DATETIME',
                'null' => true,
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
				'constraint' => '3',
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
				'constraint' => '15',
                'null' => true,
			),
			'bettype' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
                'null' => true,
            ),
			'playtype' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
                'null' => true,
            ),
			'playertype' => array(
				'type' => 'INT',
				'constraint' => '1',
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
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
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
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('external_uniqueid');
		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
