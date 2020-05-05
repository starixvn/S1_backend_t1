<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_rwb_game_transactions_20180331 extends CI_Migration {

	private $tableName = 'rwb_game_transactions';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
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

		$game_logs_fields = array(
            'refunded' => array(
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            ),
        );
        $this->dbforge->add_column('rwb_game_logs', $game_logs_fields);
	}

	public function down() {
		$this->dbforge->drop_column('rwb_game_logs', 'refunded');
		$this->dbforge->drop_table($this->tableName);

	}
}
