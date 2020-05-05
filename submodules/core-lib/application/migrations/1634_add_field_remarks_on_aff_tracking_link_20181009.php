<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_field_remarks_on_aff_tracking_link_20181009 extends CI_Migration {

	private $tableName = 'aff_tracking_link';

	public function up() {
        $field = array(
            'remarks' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('remarks', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $field);
        }
	}

	public function down() {
        if($this->db->field_exists('remarks', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'remarks');
        }
	}
}