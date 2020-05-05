<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_flag_new_game_to_game_description extends CI_Migration {

    private $tableName = 'game_description';

    public function up() {
        $fields = array(
            'flag_new_game' => array(
                'type' => 'boolean',
                'null' => true,
                'default' => 0
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'flag_new_game');
    }
}

////END OF FILE////