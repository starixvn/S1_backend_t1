<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_20180828 extends CI_Migration {

    private $tableName = 'player';

    public function up() {
        $fields = array(
            'disable_player_update_status_pep' => array(
                'type' => 'TINYINT',
                'null' => true,
                'default' => 0,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'disable_player_update_status_pep');
    }
}