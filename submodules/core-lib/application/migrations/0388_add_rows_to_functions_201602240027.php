<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_functions_201602240027 extends CI_Migration {

	// const FUNC_ID_LIVE_CHAT = 97;
	// const FUNC_ID_CS_MANAGEMENT = 37;

	public function up() {
		// $this->db->trans_start();

		// //add live chat
		// $data = array(
		// 	'funcId' => self::FUNC_ID_LIVE_CHAT,
		// 	'funcName' => 'Live Chat',
		// 	'parentId' => self::FUNC_ID_CS_MANAGEMENT, //CS Management
		// 	'funcCode' => 'live_chat',
		// 	'sort' => self::FUNC_ID_LIVE_CHAT,
		// );

		// $this->db->insert('functions', $data);

		// //add super admin permission
		// $this->db->insert('rolefunctions', array('roleId' => 1, 'funcId' => self::FUNC_ID_LIVE_CHAT));

		// $this->db->insert('rolefunctions_giving', array('roleId' => 1, 'funcId' => self::FUNC_ID_LIVE_CHAT));

		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->delete('functions', array('funcCode' => 'live_chat'));
		// $this->db->delete('rolefunctions', array('roleId' => 1, 'funcId' => self::FUNC_ID_LIVE_CHAT));
		// $this->db->delete('rolefunctions_giving', array('roleId' => 1, 'funcId' => self::FUNC_ID_LIVE_CHAT));
	}
}