<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_external_system_table_20171201 extends CI_Migration {

	public function up() {
        if (!$this->db->field_exists('category', 'external_system')) {
            $field = array(
                'category' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                )
            );
            $this->dbforge->add_column('external_system', $field, 'system_type');
        }

        if (!$this->db->field_exists('amount_float', 'external_system')) {
            $field = array(
                'amount_float' => array(
                    'type' => 'TINYINT',
                    'constraint' => '4',
                    'default' => 2,
                    'null' => true,
                    'unsigned' => TRUE
                )
            );
            $this->dbforge->add_column('external_system', $field, 'category');
        }
	}

	public function down() {
        if ($this->db->field_exists('amount_float', 'external_system')) {
            $this->dbforge->drop_column('external_system', 'amount_float');
        }

        if ($this->db->field_exists('category', 'external_system')) {
            $this->dbforge->drop_column('external_system', 'category');
        }
	}
	
}
