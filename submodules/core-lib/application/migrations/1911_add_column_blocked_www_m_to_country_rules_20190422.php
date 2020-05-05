<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_blocked_www_m_to_country_rules_20190422 extends CI_Migration {

    private $tableName = 'country_rules';

    public function up() {

        $fields = array(
            'blocked_www_m' => array(
                'type' => 'INT',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

		$this->seed_column();

    }

    public function down() {
		if ($this->db->field_exists('blocked_www_m', $this->tableName)){
	        $this->dbforge->drop_column($this->tableName, 'blocked_www_m');
		}
    }

	protected function seed_column() {
		$this->db->set([ 'blocked_www_m' => 1])
			->where('flag', 2)
			->update($this->tableName);
	}
}
