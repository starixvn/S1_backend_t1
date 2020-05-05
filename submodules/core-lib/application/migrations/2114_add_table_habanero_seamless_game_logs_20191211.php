<?php

defined("BASEPATH") OR exit("No direct script access allowed");


class Migration_add_table_habanero_seamless_game_logs_20191211 extends CI_Migration
{
    private $tableName = "habanero_seamless_game_logs";

    public function up()
    {
        $fields = [
            "id" => [
                "type" => "BIGINT",
                "null" => false,
                "auto_increment" => true
            ],
            "game_code" => [
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ],
            "game_name" => [
                "type" => "VARCHAR",
                "constraint" => "100",
                "null" => true
            ],
            "game_instance_id" => [
                "type" => "VARCHAR",
                "constraint" => "100",
                "null" => true
            ],
            "game_user_name" => [
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ],
            "player_id" => [
                "type" => "VARCHAR",
                "constraint" => "150",
                "null" => true
            ],
            "currency_code" => [
                "type" => "VARCHAR",
                "constraint" => "10",
                "null" => true
            ],
            "transaction_id" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "transfer_id" => [
                "type" => "VARCHAR",
                "constraint" => "100",
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
            "balance_before" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "balance_after" => [
                "type" => "DOUBLE",
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
			'is_valid_game_logs' => array(
				'type' => 'TINYINT(1)',
				'null' => false,
				'default' => 0
			),
			'is_retry' => array(
				'type' => 'TINYINT(1)',
				'null' => false,
				'default' => 0
			),
			'is_refund' => array(
				'type' => 'TINYINT(1)',
				'null' => false,
				'default' => 0
			),
			'is_recredit' => array(
				'type' => 'TINYINT(1)',
				'null' => false,
				'default' => 0
			),
            "game_state_mode" => [
                "type" => "TINYINT",
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
            $this->load->model("player_model");
            $this->player_model->addIndex($this->tableName,"idx_player_id","player_id");
            $this->player_model->addUniqueIndex($this->tableName,"idx_transaction_id","transaction_id");
            $this->player_model->addUniqueIndex($this->tableName,"idx_transfer_id","transfer_id");
            $this->player_model->addUniqueIndex($this->tableName,"idx_external_unique_id","external_unique_id");
        }
    }

    public function down()
    {
        if($this->db->table_exists($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
    }
}