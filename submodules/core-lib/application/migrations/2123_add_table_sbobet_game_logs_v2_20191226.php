<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_table_sbobet_game_logs_v2_20191226 extends CI_Migration
{
    private $tableName = "sbobet_game_logs_v2";

    public function up()
    {
        $fields = [
            "id" => [
                "type" => "BIGINT",
                "null" => false,
                "auto_increment" => true
            ],
            //default
            'ref_no' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'sports_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'order_time' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'modify_date' => array(
                'type' => 'DATETIME',
                'null' => true
            ),

            //sports and virtual sports
            'winlost_date' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'odds' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'odds_style' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'stake' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'actual_stake' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '3',
                'null' => true,
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'win_lost' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'turnover' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'is_half_won_lose' => array(
                'type' => 'BOOLEAN',
                'null' => true,
                'default' => 0,
            ),
            'is_live' => array(
                'type' => 'BOOLEAN',
                'null' => true,
                'default' => 0,
            ),
            'max_win_without_actual_stake' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'bet_details' => array(
                'type' => 'TEXT',
                'null' => true,
            ),

            //casino && game
            'game_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'table_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'product_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),

            # SBE additional info
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            "response_result_id" => array(
                "type" => "INT",
                "constraint" => "11",
                "null" => true
            ),
            "external_uniqueid" => array(
                "type" => "VARCHAR",
                "constraint" => "60",
                "null" => true
            ),
            "created_at DATETIME DEFAULT CURRENT_TIMESTAMP" => array(
                "null" => false
            ),
            "updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP" => array(
                "null" => false
            )
        ];

        if(!$this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key("id",true);
            $this->dbforge->create_table($this->tableName);

            # add index
            $indexPreStr = 'idx_';
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'ref_no', 'ref_no');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'order_time', 'order_time');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'modify_date', 'modify_date');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'winlost_date', 'winlost_date');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'game_id', 'game_id');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'username', 'username');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'status', 'status');
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