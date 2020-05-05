<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_sbtech_bti_game_logs_201802081400 extends CI_Migration {

    private $tableName = 'sbtech_bti_game_logs';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'pl' => array( # profit - loss
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'non_cashout_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'combo_bonus_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'bet_settled_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'purchase_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'update_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'odds' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'odds_in_user_style' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'odds_style_of_user":' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'odds_dec":' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'total_stake' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'system_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'platform' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'return' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'bet_status' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'bet_type_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'bet_type_id' => array(
                'type' => 'INT',
                'constraint' => '5',
                'null' => false,
            ),
            'creation_date' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'customer_id' => array( # id in sbtech system
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'merchant_customer_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '3',
                'null' => true,
            ),
            'player_level_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'player_level_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'domain_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'selections' => array( # json data
                'type' => 'TEXT',
                'null' => true,
            ),
            # SBE data
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
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
            'response_result_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('username');
        $this->dbforge->add_key('external_uniqueid');
        $this->dbforge->create_table($this->tableName);
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid', true);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}