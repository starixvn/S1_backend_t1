<?php

defined("BASEPATH") OR exit("No direct script access allowed");


class Migration_add_table_game_provider_report_20191119 extends CI_Migration
{
    private $tableName = "game_provider_report";

    public function up()
    {
        $fields = [
            "id" => [
                "type" => "BIGINT",
                "null" => false,
                "auto_increment" => true
            ],
            "game_platform_id" => [
                "type" => "INT",
                "null" => true
            ],
            "game_type_id" => [
                "type" => "INT",
                "null" => true
            ],
            "game_description_id" => [
                "type" => "INT",
                "null" => true
            ],
            "player_id" => [
                "type" => "INT",
                "null" => true
            ],
            "player_username" => [
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ],
            "player_level" => [
                "type" => "VARCHAR",
                "constraint" => "100",
                "null" => true
            ],
            "affiliate_username" => [
                "type" => "VARCHAR",
                "constraint" => "100",
                "null" => true
            ],
            "affiliate_id" => [
                "type" => "INT",
                "null" => true
            ],
            "agent_id" => [
                "type" => "INT",
                "null" => true
            ],
            "agent_name" => [
                "type" => "VARCHAR",
                "constraint" => "100",
                "null" => true
            ],
            "betting_amount" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "real_betting_amount" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "result_amount" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "win_amount" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "loss_amount" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "date" => [
                "type" => "DATE",
                "null" => true
            ],
            "date_time" => [
                "type" => "DATETIME",
                "null" => true
            ],
            "timezone" => [
                "type" => "VARCHAR",
                "constraint" => "10",
                "null" => true
            ],
            "external_unique_id" => [
                "type" => "VARCHAR",
                "constraint" => "100",
                "null" => true
            ],
            "status" => [
                "type" => "SMALLINT",
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
            $indexPreStr = 'idx_';
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'game_platform_id', 'game_platform_id');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'game_type_id', 'game_type_id');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'game_description_id', 'game_description_id');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'player_id', 'player_id');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'external_unique_id', 'external_unique_id');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'date', 'date');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'date_time', 'date_time');
        }
    }

    public function down()
    {
        if($this->db->table_exists($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
    }
}