<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_rwb_game_logs_201803220103 extends CI_Migration {

	private $tableName = 'rwb_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			//initial(debit/credit)
			'request_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'transaction_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'user_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'reason' => array(
				'type' => 'INT',
				'null' => false,
			),
			'force_debit' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'amount' => array(
                'type' => 'double',
                'null' => true,
			),
			'currency' => array(
                'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'description' => array(
                'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			//other column for bet history
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'settle_status' => array(
				'type' => 'INT',
				'null' => false,
			),
			'stake' => array(
                'type' => 'double',
                'null' => true,
			),
			'payout' => array(
                'type' => 'double',
                'null' => true,
			),
			'potential_payout' => array(
                'type' => 'double',
                'null' => true,
			),
			'is_mobile' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'ip_address' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'price_format' => array(
				'type' => 'INT',
				'null' => false,
			),
			'bet_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'settle_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'selections' => array(
                'type' => 'text',
                'null' => true,
            ),
			//sbe column
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
		$this->dbforge->add_key('request_id');
		$this->dbforge->add_key('transaction_id');
		$this->dbforge->add_key('bet_id');
		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
