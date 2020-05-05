<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_redtigerseamless_game_logs_20190703 extends CI_Migration {

    private $tableName = 'redtigerseamless_game_logs';

    public function up() {

        if ( ! $this->db->table_exists($this->tableName)) {

            $fields = array(
                'id' => array(
                    'type' => 'bigint',
                    'null' => false,
                    'auto_increment' => true,
                    'unsigned' => true,
                ),

                'transaction_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '200',
                    'null' => false,
                ),
                'type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '200',
                    'null' => false,
                ),

                'token' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '32',
                    'null' => false,
                ),
                'casino' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '50',
                    'null' => false,
                ),
                'userId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '36',
                    'null' => false,
                ),
                'currency' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '8',
                    'null' => false,
                ),
                'ip' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '200',
                    'null' => false,
                ),

                'transaction_stake_payout' => array(
                    'type' => 'DOUBLE',
                    'null' => false,
                ),
                'transaction_stake_payout_promo' => array(
                    'type' => 'DOUBLE',
                    'null' => false,
                ),
                'transaction_details_game' => array(
                    'type' => 'DOUBLE',
                    'null' => false,
                ),
                'transaction_details_jackpot' => array(
                    'type' => 'DOUBLE',
                    'null' => false,
                ),

                'game_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '200',
                    'null' => true,
                ),
                'game_key' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '128',
                    'null' => true,
                ),
                'game_version' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '128',
                    'null' => true,
                ),

                'round_id' => array(
                    'type' => 'bigint',
                    'null' => false,
                    'unsigned' => true,
                ),
                'round_starts' => array(
                    'type' => 'tinyint',
                    'null' => false,
                    'default' => 0,
                ),
                'round_ends' => array(
                    'type' => 'tinyint',
                    'null' => false,
                    'default' => 0,
                ),

                'promo_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '200',
                    'null' => true,
                ),
                'promo_instanceCode' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '64',
                    'null' => true,
                ),
                'promo_instanceId' => array(
                    'type' => 'int',
                    'null' => true,
                    'unsigned' => true,
                ),
                'promo_campaignCode' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '64',
                    'null' => true,
                ),
                'promo_campaignId' => array(
                    'type' => 'int',
                    'null' => true,
                    'unsigned' => true,
                ),

                'retry' => array(
                    'type' => 'tinyint',
                    'null' => false,
                    'default' => 0,
                ),

                'transaction_sources_lines' => array(
                    'type' => 'DOUBLE',
                    'null' => true,
                ),
                'transaction_sources_features' => array(
                    'type' => 'DOUBLE',
                    'null' => true,
                ),
                'transaction_sources_jackpot' => array(
                    'type' => 'TEXT',
                    'null' => true,
                ),

                'jackpot_group' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'jackpot_contribution' => array(
                    'type' => 'int',
                    'unsigned' => true,
                    'null' => true,
                ),
                'jackpot_pots' => array(
                    'type' => 'TEXT',
                    'null' => true,
                ),
                
                'player_id' => array(
                    'type' => 'bigint',
                    'null' => false,
                ),

                'after_balance' => array(
                    'type' => 'DOUBLE',
                    'null' => false,
                ),

                'response_result_id' => array(
                    'type' => 'bigint',
                    'null' => false,
                ),

                'created_at' => array(
                    'type' => 'DATETIME',
                    'null' => false,
                ),

            );


            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);

            $this->dbforge->create_table($this->tableName);

            $this->load->model('player_model');

            $this->player_model->addUniqueIndex($this->tableName, 'transaction_id', 'transaction_id, type');

            $this->player_model->addIndex($this->tableName, 'idx_redtigerseamless_userId', 'userId');
            $this->player_model->addIndex($this->tableName, 'idx_redtigerseamless_created_at', 'created_at');

        }

    }

    public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
    }
}