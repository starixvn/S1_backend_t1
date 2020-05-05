<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_release_date_to_game_description_history_20190312 extends CI_Migration {

    private $tableName='game_description_history';

    public function up() {
        $fields = array(
            'release_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'release_date');
    }
}