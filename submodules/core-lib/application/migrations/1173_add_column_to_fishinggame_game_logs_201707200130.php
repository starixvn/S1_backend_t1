<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_fishinggame_game_logs_201707200130 extends CI_Migration {

    private $tableName = 'fishinggame_game_logs';

    public function up() {
        $fields = array(
            'linkId' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'linkId');

    }
}

