<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_player_report_simple_game_daily_20190611 extends CI_Migration {

    private $tableName = 'player_report_simple_game_daily';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'player_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint'=> 100,
                'null' => false,
            ),
            'game_platform_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'game_type_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'betting_amount'=>array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'real_betting_amount'=>array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'result_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'win_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'loss_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'total_date' => array(
                'type' => 'DATE',
                'null' => false
            ),
            'created_at' => array(
                'type'=>'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type'=>'DATETIME',
                'null' => false,
            ),
            'currency_key'=> array(
                'type' => 'VARCHAR',
                'constraint'=> 5,
                'null' => false,
            ),
            'md5_sum'=> array(
                'type' => 'VARCHAR',
                'constraint'=> 64,
                'null' => true,
            ),
            'uniqueid'=> array(
                'type' => 'VARCHAR',
                'constraint'=> 64,
                'null' => false,
            ),
        );

        if(!$this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);
            # Add Index
            $this->load->model('player_model');
            $this->player_model->addIndex($this->tableName, 'idx_player_id', 'player_id');
            $this->player_model->addIndex($this->tableName, 'idx_username', 'username');
            $this->player_model->addIndex($this->tableName, 'idx_total_date', 'total_date');
            $this->player_model->addIndex($this->tableName, 'idx_uniqueid', 'uniqueid', true);
        }
    }

    public function down() {
        if($this->db->table_exists($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
    }
}

