<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_columns_to_mg_dashur_game_logs_20180803 extends CI_Migration {

    private $tableName = 'mg_dashur_game_logs';

    public function up(){

        $fields = array(
            'mg_id' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'parent_transaction_id' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'account_id' => array(
                'type' => 'BIGINT',
                'null' => true,
            )
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
    }
}
