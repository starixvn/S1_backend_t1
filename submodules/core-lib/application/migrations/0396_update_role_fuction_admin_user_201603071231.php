<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_role_fuction_admin_user_201603071231 extends CI_Migration {

	public function up() {
		// $this->db->query('DELETE FROM rolefunctions WHERE roleId = 1 AND funcId = 97');
		// $this->db->query('DELETE FROM rolefunctions WHERE roleId = 1 AND funcId = 98');

		// $this->db->query('DELETE FROM rolefunctions_giving WHERE roleId = 1 AND funcId = 97');
		// $this->db->query('DELETE FROM rolefunctions_giving WHERE roleId = 1 AND funcId = 98');

		// $this->db->query("INSERT INTO `rolefunctions` (`roleId`, `funcId`) VALUES (1, 97)");
		// $this->db->query("INSERT INTO `rolefunctions` (`roleId`, `funcId`) VALUES (1, 98)");

		// $this->db->query("INSERT INTO `rolefunctions_giving` (`roleId`, `funcId`) VALUES (1, 97)");
		// $this->db->query("INSERT INTO `rolefunctions_giving` (`roleId`, `funcId`) VALUES (1, 98)");
	}

	public function down() {
		// $this->db->query('DELETE FROM rolefunctions WHERE roleId = 1 AND funcId = 97');
		// $this->db->query('DELETE FROM rolefunctions WHERE roleId = 1 AND funcId = 98');
		// $this->db->query('DELETE FROM rolefunctions_giving WHERE roleId = 1 AND funcId = 97');
		// $this->db->query('DELETE FROM rolefunctions_giving WHERE roleId = 1 AND funcId = 98');
	}
}