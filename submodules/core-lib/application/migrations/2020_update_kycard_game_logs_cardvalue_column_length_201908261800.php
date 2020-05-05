<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_kycard_game_logs_cardvalue_column_length_201908261800 extends CI_Migration {
    private $tableName = 'kycard_game_logs';

    public function up() {
        //modify column size
        $fields = array(
            'cardvalue' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
        // not able to rollback due to data truncation
    }
}
