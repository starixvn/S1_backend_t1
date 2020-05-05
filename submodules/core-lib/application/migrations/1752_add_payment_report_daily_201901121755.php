<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_payment_report_daily_201901121755 extends CI_Migration {

    private $tableName='payment_report_daily';

    public function up() {

        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'BIGINT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'transaction_type' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'level_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'player_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'player_username' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ),
            'player_realname' => array(
                'type' => 'VARCHAR',
                'constraint' => 300,
                'null' => true,
            ),
            'payment_account_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'payment_account_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ),
            'bank_type_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'bank_type_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ),
            'external_system_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'external_system_code' => array(
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ),
            'payment_date' => array(
                'type' => 'DATE',
                'null' => false,
            ),
            //<payment_date>-<transaction_type>-<player_id>-<payment_account_id>-<external_system_id>
            'unique_key' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table($this->tableName);
        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_unique_key', 'unique_key',true);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
