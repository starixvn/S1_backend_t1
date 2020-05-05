<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_responsible_gaming_history_201808131330 extends CI_Migration {

    private $tableName = 'responsible_gaming_history';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'responsible_gaming_id' => array(
                'type' => 'INT',
                'null' => FALSE,
            ),
            'old_status' => array(
                'type' => 'INT',
                'null' => FALSE,
            ),
            'status' => array(
                'type' => 'INT',
                'null' => FALSE,
            ),
            'remarks' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'admin_id' => array(
                'type' => 'INT',
                'null' => FALSE,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}