<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_game_api_info_to_external_system_list_201602230220 extends CI_Migration {

	public function up() {
		// $this->db->where('id', PT_API)->update('external_system',
		// 	array(
		// 		'live_mode' => 1,
		// 		'live_url' => 'https://kioskpublicapi.mightypanda88.com/',
		// 		'live_secret' => '',
		// 		'sandbox_url' => 'https://kioskpublicapi.mightypanda88.com/',
		// 		'sandbox_secret' => '',
		// 		'extra_info' => '{\r\n \"ADMIN_NAME\": \"<admin name>\",\r\n \"KIOSK_NAME\": \"<kiosk name>\",\r\n \"CERT_KEY\": \"cert.key\",\r\n \"CERT_PEM\": \"cert.pem\"\r\n }',
		// 	)
		// );

		// $this->db->where('id', PT_API)->update('external_system_list',
		// 	array(
		// 		'live_mode' => 1,
		// 		'live_url' => 'https://kioskpublicapi.mightypanda88.com/',
		// 		'live_secret' => '',
		// 		'sandbox_url' => 'https://kioskpublicapi.mightypanda88.com/',
		// 		'sandbox_secret' => '',
		// 		'extra_info' => '{\r\n \"ADMIN_NAME\": \"<admin name>\",\r\n \"KIOSK_NAME\": \"<kiosk name>\",\r\n \"CERT_KEY\": \"cert.key\",\r\n \"CERT_PEM\": \"cert.pem\"\r\n }',
		// 	)
		// );

	}

	public function down() {

	}
}