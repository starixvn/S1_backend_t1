<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_columns_to_evolution_2_game_logs_20181024 extends CI_Migration {

    private $tableName = 'evolution_2_game_logs';

    public function up(){

        $fields = array(
            'game_round_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
    }
}
