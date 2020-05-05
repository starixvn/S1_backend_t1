<?php

defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_table_sbobet_seamless_game_transactions_20200122 extends CI_Migration
{

    private $tableName = "sbobet_seamless_game_transactions";

    public function up()
    {
        $fields = [
            "id" => [
                "type" => "BIGINT",
                "null" => false,
                "auto_increment" => true
            ],
            "transaction_type" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "gameusername" => [
                "type" => "VARCHAR",
                "constraint" => "60",
                "null" => true
            ],
            "product_type" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "game_type" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "currency" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "transfer_code" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "transaction_id" => [
                "type" => "VARCHAR",
                "constraint" => "60",
                "null" => true
            ],
            "amount" => [
                "type" => "DOUBLE",
                "null" => true,
            ],
            "result_type" => [
                "type" => "VARCHAR",
                "constraint" => "10",
                "null" => true
            ],
            "result_time" => [
                "type" => "DATETIME",
                "null" => true
            ],
            "tip_time" => [
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
            "unique_transaction_id" => [
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
            $this->player_model->addIndex($this->tableName,"idx_gameusername","gameusername");
            $this->player_model->addIndex($this->tableName,"idx_transaction_id","transaction_id");
            $this->player_model->addIndex($this->tableName,"idx_transfer_code","transfer_code");
            $this->player_model->addUniqueIndex($this->tableName,"idx_unique_transaction_id","unique_transaction_id");
        }
    }

    public function down()
    {
        if($this->db->table_exist($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
    }
}