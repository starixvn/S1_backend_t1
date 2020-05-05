<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_columns_on_gameplay_game_logs_20190225 extends CI_Migration {

    private $tableName = 'gameplay_game_logs';

    public function up(){

        $fields = array(
            'bet_id' => array(      // cause error in int
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
    }
}