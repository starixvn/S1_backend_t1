<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_pragmaticplay_game_logs_20181212 extends CI_Migration {

    private $tableName = 'pragmaticplay_game_logs';

    public function up() {
        $fields = array(
	       'parent_session_id' => array(			
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
           'start_date' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'end_date' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'status' => array(				// I - in progress , C - complete
                'type' => 'VARCHAR',
                'constraint' => '5',
            ),
           'type_game_round' => array(		// type ( R -game round, F free spin)
                'type' => 'VARCHAR',
                'constraint' => '5'
            ),
           'bet' => array(           
                'type' => 'DOUBLE',
                'null' => false
            ),
            'win' => array(           
                'type' => 'DOUBLE',
                'null' => false
            ),
           'jackpot' => array(           
                'type' => 'DOUBLE',
                'null' => false
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'parent_session_id');
        $this->dbforge->drop_column($this->tableName, 'start_date');
        $this->dbforge->drop_column($this->tableName, 'end_date');
        $this->dbforge->drop_column($this->tableName, 'status');
        $this->dbforge->drop_column($this->tableName, 'type_game_round');
        $this->dbforge->drop_column($this->tableName, 'bet');
        $this->dbforge->drop_column($this->tableName, 'win');
        $this->dbforge->drop_column($this->tableName, 'jackpot');
    }
}
