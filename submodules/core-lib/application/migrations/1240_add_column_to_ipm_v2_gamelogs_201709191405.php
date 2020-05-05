<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ipm_v2_gamelogs_201709191405 extends CI_Migration {

    private $tableName = 'ipm_v2_game_logs';

    public function up() {
        $fields = array(
            'SportsName' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'SportsName');
    }
}