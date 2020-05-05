<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_other_dashboard_fields_to_admin_dashboard_20191113 extends CI_Migration {

    private $tableName = 'admin_dashboard';

    public function up() {

        $fields = array(
			'other_dashboard_fields' => [
				'type' => 'json' ,
				'null' => true
			]
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'other_dashboard_fields');
    }
}
