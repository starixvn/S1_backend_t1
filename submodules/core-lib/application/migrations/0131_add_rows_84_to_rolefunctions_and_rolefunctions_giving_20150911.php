<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_84_to_rolefunctions_and_rolefunctions_giving_20150911 extends CI_Migration {

	public function up() {

		// $this->db->insert('functions', [
		// 	'funcId'		=> 84,
		// 	'funcName'		=> 'Player Contact Info',
		// 	'parentId'		=> 7,
		// 	'funcCode'		=> 'player_contact_info',
		// 	'sort'			=> 83,
		// 	'createTime'	=> '0000-00-00 00:00:00',
		// ]);

		// $this->db->update('functions', ['parentId' => 84], ['funcId' => 17]);
		// $this->db->update('functions', ['parentId' => 84], ['funcId' => 18]);
		// $this->db->update('functions', ['parentId' => 84], ['funcId' => 19]);

		// $rolefunctions = [
		// 	'roleId' => 1,
		// 	'funcId' => 84,
		// ];

		// $this->db->insert('rolefunctions', $rolefunctions);
		// $this->db->insert('rolefunctions_giving', $rolefunctions);
	}

	public function down() {
		// $this->db->update('functions', ['parentId' => 7], ['funcId' => 17]);
		// $this->db->update('functions', ['parentId' => 7], ['funcId' => 18]);
		// $this->db->update('functions', ['parentId' => 7], ['funcId' => 19]);
		// $this->db->delete('functions', ['funcId' => 84]);
		// $this->db->delete('rolefunctions', ['funcId' => 84]);
		// $this->db->delete('rolefunctions_giving', ['funcId' => 84]);
	}

}