<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_201712181600 extends CI_Migration {

    private $tableName = 'player';

    public function up() {
        # After the player is registered successfully, the column control shows the successful pop-up page
        $fields = array(
            'is_registered_popup_success_done' => array(
                'type' => 'INT(2)',
                'null' => false,
                'default' => 0,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'is_registered_popup_success_done');
    }
}
