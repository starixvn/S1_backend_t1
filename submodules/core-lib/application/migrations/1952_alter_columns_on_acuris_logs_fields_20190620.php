<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_columns_on_acuris_logs_fields_20190620 extends CI_Migration {

	private $tableName = 'acuris_logs';

	public function up() {

        $fields = array(
            'articles' => array(
                'type' => 'MEDIUMTEXT',
            )
        );
        $this->dbforge->modify_column($this->tableName, $fields);

	}

	public function down() {}
}