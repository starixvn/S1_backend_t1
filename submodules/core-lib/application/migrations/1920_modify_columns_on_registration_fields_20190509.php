<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_columns_on_registration_fields_20190509 extends CI_Migration {

	private $tableName = 'registration_fields';

	public function up() {

        $fields = array(
            'account_visible' => array(
                'type' => 'INT',
				'default' => 1,
            )
        );
        $this->dbforge->modify_column($this->tableName, $fields);

        $fields = array(
            'account_required' => array(
                'type' => 'INT',
				'default' => 1,
            )
        );
        $this->dbforge->modify_column($this->tableName, $fields);

	}

	public function down() {}
}