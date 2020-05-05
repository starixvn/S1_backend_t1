<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_external_uniqueid_in_evolution_seamless_thb1_game_logs_20200205 extends CI_Migration {

    private $tableName = 'evolution_seamless_thb1_game_logs';

    public function up(){

        $fields = array(
            'external_uniqueid' => array(
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