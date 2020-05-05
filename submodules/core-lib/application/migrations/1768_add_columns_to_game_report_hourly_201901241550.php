<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_game_report_hourly_201901241550 extends CI_Migration {

    private $tableName='game_report_hourly';

    public function up() {
        $fields = array(
           'external_game_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null'=> true
            ),
           'game_type_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null'=> true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'external_game_id');
        $this->dbforge->drop_column($this->tableName, 'game_type_code');
    }
}
