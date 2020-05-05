<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_oneworks_game_result_20180521 extends CI_Migration {

    private $tableName = 'oneworks_game_result';

    public function up() {
        $fields = array(
            //number game
            'game_no' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            //vitual game
            'colours' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'event_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'event_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'event_status' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'human_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'is_favor' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'key' => array(
                'type' => 'SMALLINT',
                'null' => true,
            ),
            'kick_off_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'lane' => array(
                'type' => 'SMALLINT',
                'null' => true,
            ),
            'place_odds' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'placing' => array(
                'type' => 'SMALLINT',
                'null' => true,
            ),
            'racer_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'racer_num' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'win_odds' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'game_no');
        $this->dbforge->drop_column($this->tableName, 'colours');
        $this->dbforge->drop_column($this->tableName, 'event_date');
        $this->dbforge->drop_column($this->tableName, 'event_id');
        $this->dbforge->drop_column($this->tableName, 'event_status');
        $this->dbforge->drop_column($this->tableName, 'human_id');
        $this->dbforge->drop_column($this->tableName, 'is_favor');
        $this->dbforge->drop_column($this->tableName, 'key');
        $this->dbforge->drop_column($this->tableName, 'kick_off_time');
        $this->dbforge->drop_column($this->tableName, 'lane');
        $this->dbforge->drop_column($this->tableName, 'place_odds');
        $this->dbforge->drop_column($this->tableName, 'placing');
        $this->dbforge->drop_column($this->tableName, 'racer_id');
        $this->dbforge->drop_column($this->tableName, 'racer_num');
        $this->dbforge->drop_column($this->tableName, 'win_odds');
    }
}