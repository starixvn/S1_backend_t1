<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_tpg_game_logs_20191210 extends CI_Migration {

    private $tableName = 'tpg_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'transaction_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => true,
            ),
            'bet_at' => array(
                "type" => "DATETIME",
                "null" => true
            ),
            'end_at' => array(
                "type" => "DATETIME",
                "null" => true
            ),
            'operator_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => false,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'game_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'game_theme' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'game_client_platform' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'game_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'is_test_account' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'completed' => array(
                'type' => 'TINYINT',
                'constraint' => '10',
                'null' => true,
            ),
            'total_deduct_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'bet_per_line' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'total_bet_multiplier' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'is_free_spin' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'freespin_won_count' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'line_payout' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'winning_lines_position' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'has_wild_symbol' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'winning_symbols' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'original_payouts' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'freespin_payout_multiplier' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'total_payout_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),

            # SBE additional info
            "response_result_id" => array(
                "type" => "INT",
                "constraint" => "11",
                "null" => true
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
            ),
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
        );

        if(!$this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key("id",true);
            $this->dbforge->create_table($this->tableName);

            # add index
            $this->player_model->addUniqueIndex($this->tableName, 'idx_transaction_id', 'transaction_id');
            $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
            $this->player_model->addIndex($this->tableName, 'idx_bet_at', 'bet_at');
            $this->player_model->addIndex($this->tableName, 'idx_end_at', 'end_at');
            $this->player_model->addIndex($this->tableName, 'idx_created_at', 'created_at');
            $this->player_model->addIndex($this->tableName, 'idx_updated_at', 'updated_at');
        }
    }

    public function down() {
        if($this->db->table_exists($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
    }
}
