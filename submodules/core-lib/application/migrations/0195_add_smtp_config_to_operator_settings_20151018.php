<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_smtp_config_to_operator_settings_20151018 extends CI_Migration {

	private $tableName = 'operator_settings';

	public function up() {
		// $this->db->insert_batch($this->tableName, array(
		// 	array(
		// 		'name' => 'mail_smtp_server',
		// 		'value' => $this->config->item('mail_smtp_server')
		// 	),
		// 	array(
		// 		'name' => 'mail_smtp_port',
		// 		'value' => $this->config->item('mail_smtp_port')
		// 	),
		// 	array(
		// 		'name' => 'mail_smtp_auth',
		// 		'value' => $this->config->item('mail_smtp_auth')
		// 	),
		// 	array(
		// 		'name' => 'mail_smtp_secure',
		// 		'value' => $this->config->item('mail_smtp_secure')
		// 	),
		// 	array(
		// 		'name' => 'mail_smtp_username',
		// 		'value' => $this->config->item('mail_smtp_username')
		// 	),
		// 	array(
		// 		'name' => 'mail_smtp_password',
		// 		'value' => $this->config->item('mail_smtp_password')
		// 	),
		// 	array(
		// 		'name' => 'mail_from',
		// 		'value' => $this->config->item('mail_from')
		// 	),
		// 	array(
		// 		'name' => 'disable_smtp_ssl_verify',
		// 		'value' => $this->config->item('disable_smtp_ssl_verify')
		// 	),
		// ));
	}

	public function down() {
		// $this->db->where_in('name', array(
		// 	'mail_smtp_server',
		// 	'mail_smtp_port',
		// 	'mail_smtp_auth',
		// 	'mail_smtp_secure',
		// 	'mail_smtp_username',
		// 	'mail_smtp_password',
		// 	'mail_from',
		// 	'disable_smtp_ssl_verify',
		// ));
		// $this->db->delete($this->tableName);
	}
}

///END OF FILE//////////