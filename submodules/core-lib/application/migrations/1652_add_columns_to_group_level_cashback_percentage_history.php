<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_group_level_cashback_percentage_history extends CI_Migration {

	private $tableName = 'group_level_cashback_percentage_history';

	public function up() {

		if (!$this->db->field_exists('new_percentage', $this->tableName)) {
			$this->dbforge->add_column($this->tableName, array(
				'new_percentage' => array(
					'type' => 'MEDIUMTEXT',
					'null' => true,
				),
			));
		}

        $fields = array(
            'percentage_history' => array(
                'name'=>'percentage_history',
                'type' => 'MEDIUMTEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'new_percentage');
	}
}
