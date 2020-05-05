<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_player_financial_account_rules_201902251030 extends CI_Migration {

    private $tableName = 'player_financial_account_rules';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => FALSE,
                'auto_increment' => TRUE,
            ),
            'payment_type_flag' => array(
                'type' => 'TINYINT',
                'null' => FALSE,
            ),
            'account_number_min_length' => array(
                'type' => 'INT',
                'null' => FALSE,
            ),
            'account_number_max_length' => array(
                'type' => 'INT',
                'null' => FALSE,
            ),
            'account_number_only_allow_numeric' => array(
                'type' => 'BOOLEAN',
                'null' => FALSE,
            ),
            'account_name_allow_modify_by_players' => array(
                'type' => 'BOOLEAN',
                'null' => FALSE,
            ),
            'field_show' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'field_required' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        # Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_player_financial_account_rules_payment_type_flag', 'payment_type_flag', true);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
