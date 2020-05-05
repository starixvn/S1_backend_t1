<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_kingrich_scheduler_logs_20190508 extends CI_Migration {

	private $tableName = 'kingrich_scheduler_logs';

	public function up() {

		$fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'scheduler_id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'default' => 0,
            ),
            'batch_transaction_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ),
            'total' => array(
                'type' => 'INT',
                'null' => false,
                'default' => 0,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_scheduler_id', 'scheduler_id');
        $this->player_model->addIndex($this->tableName, 'idx_batch_transaction_id', 'batch_transaction_id');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
