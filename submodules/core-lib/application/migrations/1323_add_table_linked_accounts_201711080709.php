<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_linked_accounts_201711080709 extends CI_Migration {

    private $tableName = 'linked_accounts';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'int',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => false,
            ),
            'link_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
                'null' => true,
            ),
            'admin_user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false,
            ),
            'remarks' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'link_datetime' => array(
                'type' => 'DATE',
                'null' => true,
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