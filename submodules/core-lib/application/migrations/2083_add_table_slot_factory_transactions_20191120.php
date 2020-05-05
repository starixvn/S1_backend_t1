<?php

defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_table_slot_factory_transactions_20191120 extends CI_Migration
{

    private $tableName = "slot_factory_transactions";

    public function up()
    {
        $fields = array(
            "id" => array(
                "type" => "BIGINT",
                "null" => false,
                "auto_increment" => true
            ),
            "SessionID" => array(
                "type" => "VARCHAR",
                "constraint" => "60",
                "null" => true
            ),
            "AccountID" => array(
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ),
            "GameName" => array(
                "type" => "VARCHAR",
                "constraint" => "30",
                "null" => true
            ),
            "AuthToken" => array(
                "type" => "VARCHAR",
                "constraint" => "100",
                "null" => true
            ),
            "Action" => array(
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ),
            "PlayerIP" => array(
                "type" => "VARCHAR",
                "constraint" => "25",
                "null" => true
            ),
            "Timestamp" => array(
                "type" => "VARCHAR",
                "constraint" => "30",
                "null" => true
            ),
            "TransactionID" => array(
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ),
            "RoundID" => array(
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ),
            "BetAmount" => array(
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ),
            "WinAmount" => array(
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ),
            "GambleGames" => array(
                "type" => "TINYINT",
                "null" => true
            ),

            
            # SBE additional info
            "response_result_id" => array(
                "type" => "INT",
                "constraint" => "11",
                "null" => true
            ),
            "external_uniqueid" => array(
                "type" => "VARCHAR",
                "constraint" => "50",
                "null" => true
            ),
            "created_at DATETIME DEFAULT CURRENT_TIMESTAMP" => array(
                "null" => false
            ),
            "updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP" => array(
                "null" => false
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            )
        );

        if(! $this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key("id",true);
            $this->dbforge->create_table($this->tableName);

            # add Index
            $this->load->model("player_model");
            $this->player_model->addIndex($this->tableName,"idx_AuthToken","AuthToken");
            $this->player_model->addIndex($this->tableName,"idx_AccountID","AccountID");
            $this->player_model->addIndex($this->tableName,"idx_RoundID","RoundID");
            $this->player_model->addUniqueIndex($this->tableName,"idx_TransactionID","TransactionID");
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