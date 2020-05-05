<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_table_kg_poker_game_logs_20200124 extends CI_Migration
{
    private $tableName = "kg_poker_game_logs";

    public function up()
    {
        $fields = [
            "id" => [
                "type" => "BIGINT",
                "null" => false,
                "auto_increment" => true
            ],
            //default
            'userid' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'reason' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'gold_change' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'kickback' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'roomid' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'gold_remain' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'log_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'starttime' => array(
                'type' => 'VARCHAR',
                'constraint' => '60',
                'null' => true
            ),
            'endtime' => array(
                'type' => 'VARCHAR',
                'constraint' => '60',
                'null' => true
            ),
            'game_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '60',
                'null' => true
            ),
            'roomid_tableid_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'xpid' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'puid' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true
            ),
            'json' => array(
                'type' => 'TEXT',
                'null' => true
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
            $this->dbforge->add_key('id',true);
            $this->dbforge->create_table($this->tableName);

            # add index
            $indexPreStr = 'idx_';
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'puid', 'puid');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'log_time', 'log_time');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'starttime', 'starttime');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'endtime', 'endtime');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'userid', 'userid');
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