<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_game_report_hourly_201901241732 extends CI_Migration {

    private $tableName='game_report_hourly';

    public function up() {
        $fields = array(
           'game_platform_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null'=> true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'game_platform_code');
    }
}
