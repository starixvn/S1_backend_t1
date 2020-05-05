<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_country_rules_20161015 extends CI_Migration {

    public function up() {

        $this->dbforge->drop_table('country_rules');

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'country_name' => array(
                'type' => 'VARCHAR',
                'null' => false,
                'constraint'=> 200,
            ),
            'country_code' => array(
                'type' => 'VARCHAR',
                'null' => false,
                'constraint'=> 10,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'created_by' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'flag' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'notes' => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint'=> 300,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('country_rules');

    }

    public function down() {
        $this->dbforge->drop_table('country_rules');
    }
}