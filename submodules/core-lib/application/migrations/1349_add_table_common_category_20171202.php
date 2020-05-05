<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_common_category_20171202 extends CI_Migration {

    private $tableName = 'common_category';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'category_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'category_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '500',
                'null' => true,
            ),
            'order_by' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'created_by' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'updated_by' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'status' => array(
                'type' => 'INT',
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
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}