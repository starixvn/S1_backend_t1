<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_settle_status_to_rwb_game_transactions_20180710 extends CI_Migration {

    private $tableName = 'rwb_game_transactions';

    public function up() {
        $fields = array(
            'settle_status' => array(
                'type' => 'SMALLINT',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'settle_status');
    }
}
