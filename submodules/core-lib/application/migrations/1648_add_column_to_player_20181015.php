<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_20181015 extends CI_Migration {

    private $tableName = 'player';

    public function up() {
        $fields = array(
            'dispatch_account_level_id' => array(
				'type' => 'INT',
				'default' => 1,
			),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'dispatch_account_level_id');
    }
}