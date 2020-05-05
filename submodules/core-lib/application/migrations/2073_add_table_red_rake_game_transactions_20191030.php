<?php

defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_table_red_rake_game_transactions_20191030 extends CI_Migration
{

    private $tableName = "red_rake_game_transactions";

    public function up()
    {
        $fields = [
            "id" => [
                "type" => "BIGINT",
                "null" => false,
                "auto_increment" => true
            ],
            "action" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "token" => [
                "type" => "VARCHAR",
                "constraint" => "100",
                "null" => true
            ],
            "game_id" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "player_id" => [
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
            "refunded_transaction_id" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "amount" => [
                "type" => "DOUBLE",
                "null" => true,
            ],
            "timestamp" => [
                "type" => "DATETIME",
                "null" => true
            ],
            "refunded_in" => [
                "type" => "DATETIME",
                "null" => true
            ],
            "before_balance" => [
                "type" => "DOUBLE",
                "null" => true,
            ],
            "after_balance" => [
                "type" => "DOUBLE",
                "null" => true,
            ],
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
            $this->player_model->addIndex($this->tableName,"idx_token","token");
            $this->player_model->addIndex($this->tableName,"idx_player_id","player_id");
            $this->player_model->addUniqueIndex($this->tableName,"idx_transaction_id","transaction_id");
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