<?php

defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_table_sbobet_seamless_game_logs_20200122 extends CI_Migration
{

    private $tableName = "sbobet_seamless_game_logs";

    public function up()
    {
        $fields = [
            "id" => [
                "type" => "BIGINT",
                "null" => false,
                "auto_increment" => true
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
            "bet_amount" => [
                "type" => "DOUBLE",
                "null" => true,
            ],
            "result_amount" => [
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
            "rollback_time" => [
                "type" => "DATETIME",
                "null" => true
            ],
            "cancel_time" => [
                "type" => "DATETIME",
                "null" => true
            ],
            "tip_time" => [
                "type" => "DATETIME",
                "null" => true
            ],
            "status" => [
                "type" => "VARCHAR",
                "constraint" => "20",
                "null" => true
            ],
            "remarks" => [
                "type" => "VARCHAR",
                "constraint" => "100",
                "null" => true
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
            $this->player_model->addIndex($this->tableName,"idx_gameusername","gameusername");
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