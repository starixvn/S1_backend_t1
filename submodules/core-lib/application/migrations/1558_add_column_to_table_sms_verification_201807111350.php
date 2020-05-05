<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_table_sms_verification_201807111350 extends CI_Migration {

	private $table = 'sms_verification';

    public function up() {
		if (!$this->db->field_exists('usage', $this->table)) {
			$fields = array(
				'usage' => array(
					'type' => 'VARCHAR',
					'null' => false,
					'default' => 'default',
					'constraint' => 32,
				),
			);
			$this->dbforge->add_column($this->table, $fields);
			$this->db->query("create index index_sid_mobile_usage_ctime on {$this->table}" . 
				"(`session_id`, `mobile_number`, `usage`, `create_time`)"
			);
		}

    }

    public function down() {
		if ($this->db->field_exists('usage', $this->table)) {
			$this->db->query("drop index index_sid_mobile_usage_ctime on {$this->table}");
			$this->dbforge->drop_column($this->table, 'usage');
		}
    }
}

////END OF FILE////
