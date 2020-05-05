<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_kingrich_api_logs_20180413 extends CI_Migration {

    private $tableName = 'kingrich_api_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'batch_transaction_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ),
            'api_created_date' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
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
        $this->dbforge->add_key('batch_transaction_id');
        $this->dbforge->create_table($this->tableName);

    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
