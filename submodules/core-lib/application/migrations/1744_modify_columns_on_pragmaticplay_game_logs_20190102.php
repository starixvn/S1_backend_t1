<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_columns_on_pragmaticplay_game_logs_20190102 extends CI_Migration {

    private $tableName = 'pragmaticplay_game_logs';

    public function up(){

        $fields = array(
            'parent_session_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => true
            ),
            'type_game_round' => array(
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => true
            ),
            'bet' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'win' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'jackpot' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
    }
}
