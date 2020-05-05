<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_table_evolution_seamless_thb1_wallet_transactions_20191209 extends CI_Migration
{
    private $tableName = "evolution_seamless_thb1_wallet_transactions";

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
            "sid" => [
                "type" => "VARCHAR",
                "constraint" => "100",
                "null" => true
            ],
            "userId" => [
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ],
            "channelType" => [
                "type" => "VARCHAR",
                "constraint" => "1",
                "null" => true
            ],
            "uuid" => [
                "type" => "VARCHAR",
                "constraint" => "100",
                "null" => true
            ],
            "currency" => [
                "type" => "VARCHAR",
                "constraint" => "3",
                "null" => true
            ],
            "gameId" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "gameType" => [
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ],
            "gameDetailsTableId" => [
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ],
            "gameDetailsTableVid" => [
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ],
            "transactionId" => [
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ],
            "transactionRefId" => [
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ],
            "transactionAmount" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "refundedTransactionId" => [
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ],
            "refundedIn" => [
                "type" => "DATETIME",
                "null" => true
            ],
            "beforeBalance" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "afterBalance" => [
                "type" => "DOUBLE",
                "null" => true
            ],
            "isBonus" => [
                "type" => "TINYINT",
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
            $this->player_model->addIndex($this->tableName,"idx_sid","sid");
            $this->player_model->addIndex($this->tableName,"idx_userId","userId");
            $this->player_model->addUniqueIndex($this->tableName,"idx_transactionId","transactionId");
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