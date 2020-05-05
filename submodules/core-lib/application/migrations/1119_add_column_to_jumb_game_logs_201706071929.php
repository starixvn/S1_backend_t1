<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_jumb_game_logs_201706071929 extends CI_Migration {

    public function up() {
        $fields = array(
            'roomType' => array(
                'type' => 'int',
            ),
            'beforeBalance' => array(
                'type' => 'double',
            ),
            'afterBalance' => array(
                'type' => 'double',
            ),
        );

        $this->dbforge->add_column('jumb_game_logs', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('jumb_game_logs', 'roomType');
    }
}
