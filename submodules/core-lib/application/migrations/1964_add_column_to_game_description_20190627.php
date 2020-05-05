<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_game_description_20190627 extends CI_Migration {
    private $tableName = 'game_description';
    public function up() {
        $this->dbforge->add_column($this->tableName, array(
            'locked_flag' => array(
                'type' => 'TINYINT(1)',
                'null' => true,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'locked_flag');
    }
}
