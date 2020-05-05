<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_sms_manager_msg_201711291700 extends CI_Migration {

    private $tableName = 'sms_manager_msg';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'userId' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'content' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'create_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'update_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'category' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'status' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
