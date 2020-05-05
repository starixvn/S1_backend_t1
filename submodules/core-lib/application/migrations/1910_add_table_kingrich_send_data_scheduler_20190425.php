<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_kingrich_send_data_scheduler_20190425 extends CI_Migration {

	private $tableName = 'kingrich_send_data_scheduler';

	public function up() {

		$fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'date_from' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'date_to' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'status' => array(
                'type' => 'TINYINT',
                'null' => false,
                'default' => 0,
            ),
            'created_by' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'note' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
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
        $this->player_model->addIndex($this->tableName, 'idx_date_from', 'date_from');
        $this->player_model->addIndex($this->tableName, 'idx_date_to', 'date_to');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
