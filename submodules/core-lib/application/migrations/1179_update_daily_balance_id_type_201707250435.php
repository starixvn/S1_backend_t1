<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_daily_balance_id_type_201707250435 extends CI_Migration {

    private $tableName = 'daily_balance';

    public function up() {
        $fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			)
        );

        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
        $fields = array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			)
        );

        $this->dbforge->modify_column($this->tableName, $fields);
    }
}
