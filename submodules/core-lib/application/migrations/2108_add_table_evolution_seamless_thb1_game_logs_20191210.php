<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_table_evolution_seamless_thb1_game_logs_20191210 extends CI_Migration
{
    private $tableName = "evolution_seamless_thb1_game_logs";

    public function up()
    {
        $fields = [
            "id" => [
                "type" => "BIGINT",
                "null" => false,
                "auto_increment" => true
            ],
            'game_round_id' => array(
                'type' => 'BIGINT',
                'null' => false,
            ),
            'started_at' => array(
                'type' => 'DATETIME',
                'null' => false
            ),
            'settled_at' => array(
                'type' => 'DATETIME',
                'null' => false
            ),
            'payout' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'dealer' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'result' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'game_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'wager' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'table' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'participants' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'player_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'decisions' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'city' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'screen_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'casino_session_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'casino_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'country' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'bet_coverage' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'session_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'config_overlays' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'bets' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'player_bet_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'player_payout' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            # SBE additional info
            "response_result_id" => [
                "type" => "INT",
                "constraint" => "11",
                "null" => true
            ],
            "external_uniqueid" => [
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ],
            "created_at DATETIME DEFAULT CURRENT_TIMESTAMP" => [
                "null" => false
            ],
            "updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP" => [
                "null" => false
            ]
        ];

        if(! $this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key("id",true);
            $this->dbforge->create_table($this->tableName);

            # add Index
            $this->load->model("player_model");
            $this->player_model->addIndex($this->tableName,"idx_session_id","session_id");
            $this->player_model->addIndex($this->tableName,"idx_player_id","player_id");
            $this->player_model->addUniqueIndex($this->tableName,"idx_external_uniqueid","external_uniqueid");
        }
    }

    public function down()
    {
        if($this->db->table_exist($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
    }
}