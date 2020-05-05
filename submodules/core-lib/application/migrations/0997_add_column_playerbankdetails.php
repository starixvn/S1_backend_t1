<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_playerbankdetails extends CI_Migration {

	public function up() {
		$fields = array(
			'customBankName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
		);
		if( !$this->db->field_exists('customBankName', 'playerbankdetails')){
		$this->dbforge->add_column('playerbankdetails', $fields);
		}

		if( $this->db->field_exists('remarks','banktype')){		
		$this->dbforge->drop_column('banktype', 'remarks');
		}
	}

	public function down() {
		if( $this->db->field_exists('customBankName', 'playerbankdetails') ){
		$this->dbforge->drop_column('playerbankdetails', 'customBankName');
		}
	}
}