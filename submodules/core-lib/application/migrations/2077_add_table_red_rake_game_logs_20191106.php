<?php

defined("BASEPATH") OR exit("No direct script access allowed");


class Migration_add_table_red_rake_game_logs_20191106 extends CI_Migration
{
    private $tableName = "red_rake_game_logs";

    public function up()
    {
        $fields = [
            "id" => [
                "type" => "BIGINT",
                "null" => false,
                "auto_increment" => true
            ],
            "game_id" => [
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ],
            "game_name" => [
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ],
            "session_id" => [
                "type" => "VARCHAR",
                "constraint" => "150",
                "null" => true
            ],
            "player_id" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "player_name" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "currency" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "round_id" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "transaction_id" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "bet_amount" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "real_bet_amount" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "result_amount" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "before_balance" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "after_balance" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "is_bet_loss" => [
                "type" => "TINYINT",
                "null" => true
            ],
            "is_bonus_loss" => [
                "type" => "TINYINT",
                "null" => true
            ],
            "status" => [
                "type" => "TINYINT",
                "null" => true
            ],
            "start_at" => [
                "type" => "DATETIME",
                "null" => true
            ],
            "end_at" => [
                "type" => "DATETIME",
                "null" => true
            ],
            # SBE additional info
            "response_result_id" => [
                "type" => "INT",
                "constraint" => "11",
                "null" => true
            ],
            "external_unique_id" => [
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ],
            "created_at DATETIME DEFAULT CURRENT_TIMESTAMP" => [
                "null" => false
            ],
            "updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP" => [
                "null" => false
            ],
            "md5_sum" => [
                "type" => "VARCHAR",
                "constraint" => "32",
                "null" => true
            ]
        ];

        if(! $this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key("id",true);
            $this->dbforge->create_table($this->tableName);

            # add index
            $this->load->model("red_rake_model");
            $this->red_rake_model->addIndex($this->tableName,"idx_player_id","player_id");
            $this->red_rake_model->addIndex($this->tableName,"idx_round_id","round_id");
            $this->red_rake_model->addUniqueIndex($this->tableName,"idx_transaction_id","transaction_id");
            $this->red_rake_model->addUniqueIndex($this->tableName,"idx_external_unique_id","external_unique_id");
        }
    }

    public function down()
    {
        if($this->db->table_exist($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
    }
}