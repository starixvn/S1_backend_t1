<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_isbseamless_game_logs_01242018 extends CI_Migration {

    private $tableName = 'isbseamless_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'int',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'transaction_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'roundid' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'gametype' => array(
                'type' => 'int',
                'constraint' => '32',
                'null' => true,
            ),
            'gamecategory' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'result' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'details' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'totalwin' => array(
                'type' => 'double',
                'null' => true,
            ),
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
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}