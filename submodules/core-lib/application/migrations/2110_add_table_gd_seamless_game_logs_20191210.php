<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_table_gd_seamless_game_logs_20191210 extends CI_Migration
{
    private $tableName = "gd_seamless_game_logs";

    public function up()
    {
        $fields = [
            "id" => [
                "type" => "BIGINT",
                "null" => false,
                "auto_increment" => true
            ],
            'user_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'bet_time' => array(
                'type' => 'DATETIME',
                'null' => false
            ),
            'balance_time' => array(
                'type' => 'DATETIME',
                'null' => false
            ),
            'product_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'client_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'game_interface' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'bet_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '60',
                'null' => true,
            ),
            'bet_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'bet_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'winloss' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'bet_result' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'start_balance' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'end_balance' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'transaction_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '60',
                'null' => true,
            ),
            'bet_arrays' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            # SBE additional info
            "response_result_id" => [
                "type" => "INT",
                "constraint" => "11",
                "null" => true
            ],
            "external_uniqueid" => [
                "type" => "VARCHAR",
                "constraint" => "60",
                "null" => true
            ],
            "created_at DATETIME DEFAULT CURRENT_TIMESTAMP" => [
                "null" => false
            ],
            "updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP" => [
                "null" => false
            ]
        ];

        if(!$this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key("id",true);
            $this->dbforge->create_table($this->tableName);

            # add index
            $indexPreStr = 'idx_';
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'bet_time', 'bet_time');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'balance_time', 'balance_time');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'transaction_id', 'transaction_id');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'bet_id', 'bet_id');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'user_id', 'user_id');
            $this->player_model->addUniqueIndex($this->tableName, $indexPreStr. 'external_uniqueid', 'external_uniqueid');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'created_at', 'created_at');
        }
    }

    public function down()
    {
        if($this->db->table_exists($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
    }
}