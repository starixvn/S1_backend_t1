<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_bank_list extends CI_Migration {

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'external_system_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'bank_shortcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'bank_type_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bank_type_order' => array(
				'type' => 'INT',
				'default' => 100,
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('bank_list');

		$this->db->query("create unique index idx_bank_code on bank_list(external_system_id,bank_shortcode,bank_type_code)");

		//init data
		$data = array(
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'bjncsyyh', 'bank_type_code' => '00056'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'bjncsyyh', 'bank_type_code' => '00193'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'bjyh', 'bank_type_code' => '00212'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'bjyh', 'bank_type_code' => '00050'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'bhyh', 'bank_type_code' => '00095'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'bhyh', 'bank_type_code' => '00140'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'dyyh', 'bank_type_code' => '00096'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'gdyh', 'bank_type_code' => '00057'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'gfyh', 'bank_type_code' => '00052'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'hzyh', 'bank_type_code' => '00081'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'hbyh', 'bank_type_code' => '00149'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'yxyh', 'bank_type_code' => '00041'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'jtyh', 'bank_type_code' => '00005'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'msyh', 'bank_type_code' => '00013'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'msyh', 'bank_type_code' => '00135'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'njyh', 'bank_type_code' => '00194'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'nbyh', 'bank_type_code' => '00085'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'payh', 'bank_type_code' => '00087'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'payh', 'bank_type_code' => '00205'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'pdfzyh', 'bank_type_code' => '00198'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'pdfzyh', 'bank_type_code' => '00032'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'shyh', 'bank_type_code' => '00084'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'szfzyh', 'bank_type_code' => '00023'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'xyyh', 'bank_type_code' => '00016'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'xyyh', 'bank_type_code' => '00133'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'yzcx', 'bank_type_code' => '00051'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'yzcx', 'bank_type_code' => '00138'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'zsyh', 'bank_type_code' => '00128'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'zsyh', 'bank_type_code' => '00021'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'zjtlsyyh', 'bank_type_code' => '00209'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'zsyh', 'bank_type_code' => '00196'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'zsyh', 'bank_type_code' => '00086'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'zggsyh', 'bank_type_code' => '00124'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'zggsyh', 'bank_type_code' => '00004'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'zgjsyh', 'bank_type_code' => '00174'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'zgjsyh', 'bank_type_code' => '00015'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'zgnyyh', 'bank_type_code' => '00122'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'zgnyyh', 'bank_type_code' => '00017'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'zgyh', 'bank_type_code' => '00083'),
			array('external_system_id' => IPS_PAYMENT_API, 'bank_shortcode' => 'zxyh', 'bank_type_code' => '00054'),
		);
		$this->db->insert_batch('bank_list', $data);
	}

	public function down() {
		$this->dbforge->drop_table('bank_list');
	}
}

///END OF FILE