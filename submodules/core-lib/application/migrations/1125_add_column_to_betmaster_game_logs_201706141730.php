<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_betmaster_game_logs_201706141730 extends CI_Migration {

    private $tableName = 'betmaster_game_logs';

    public function up() {
        $fields = array(
            'team_home_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'team_away_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'team_home_name');
        $this->dbforge->drop_column($this->tableName, 'team_away_name');
    }
}