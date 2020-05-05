<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_manual_subtract_balance_tag_201708181434 extends CI_Migration {

    private $tableName = 'manual_subtract_balance_tag';

    public function up() {
        $tag_fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'adjust_tag_name' => array(
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
            ),
        );

        $this->dbforge->add_field($tag_fields);
        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table('manual_subtract_balance_tag');

        $log_fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'rtn_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'msbt_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($log_fields);
        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table('transactions_tag');
    }

    public function down() {
        $this->dbforge->drop_table('manual_subtract_balance_tag');
        $this->dbforge->drop_table('transactions_tag');
    }
}
