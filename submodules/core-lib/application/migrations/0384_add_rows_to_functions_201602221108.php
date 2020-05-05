<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_functions_201602221108 extends CI_Migration {

	public function up() {
		// $this->db->trans_start();

		// //add tag_a_member
		// $data = array(
		// 	'funcId' => 93,
		// 	'funcName' => 'Tag a Member',
		// 	'parentId' => '15',
		// 	'funcCode' => 'tag_a_member',
		// 	'sort' => '93',
		// );

		// $this->db->insert('functions', $data);

		// //add message_a_member
		// $data = array(
		// 	'funcId' => 94,
		// 	'funcName' => 'Message a Member',
		// 	'parentId' => '15',
		// 	'funcCode' => 'message_a_member',
		// 	'sort' => '94',
		// );

		// $this->db->insert('functions', $data);

		// //add message_a_member
		// $data = array(
		// 	'funcId' => 95,
		// 	'funcName' => 'Cancel member withdraw condition',
		// 	'parentId' => '15',
		// 	'funcCode' => 'cancel_member_withdaw_condition',
		// 	'sort' => '95',
		// );

		// $this->db->insert('functions', $data);

		// //add batch_adjust_balance
		// $data = array(
		// 	'funcId' => 96,
		// 	'funcName' => 'Batch adjust balance',
		// 	'parentId' => '59',
		// 	'funcCode' => 'batch_adjust_balance',
		// 	'sort' => '96',
		// );

		// $this->db->insert('functions', $data);

		// //add super admin permission
		// $this->db->insert('rolefunctions', array('roleId' => 1, 'funcId' => 93));
		// $this->db->insert('rolefunctions', array('roleId' => 1, 'funcId' => 94));
		// $this->db->insert('rolefunctions', array('roleId' => 1, 'funcId' => 95));
		// $this->db->insert('rolefunctions', array('roleId' => 1, 'funcId' => 96));

		// $this->db->insert('rolefunctions_giving', array('roleId' => 1, 'funcId' => 93));
		// $this->db->insert('rolefunctions_giving', array('roleId' => 1, 'funcId' => 94));
		// $this->db->insert('rolefunctions_giving', array('roleId' => 1, 'funcId' => 95));
		// $this->db->insert('rolefunctions_giving', array('roleId' => 1, 'funcId' => 96));

		// //hide generate sites
		// $this->db->where_in('funcCode', array('generate_sites', 'promocancel_list', 'promoplayer_list'));
		// $this->db->update('functions', array('status' => 2));

		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->delete('functions', array('funcCode' => 'tag_a_member'));
		// $this->db->delete('functions', array('funcCode' => 'message_a_member'));
		// $this->db->delete('functions', array('funcCode' => 'cancel_member_withdaw_condition'));
		// $this->db->delete('functions', array('funcCode' => 'batch_adjust_balance'));
		// $this->db->delete('rolefunctions', array('roleId' => 1, 'funcId' => 93));
		// $this->db->delete('rolefunctions', array('roleId' => 1, 'funcId' => 94));
		// $this->db->delete('rolefunctions', array('roleId' => 1, 'funcId' => 95));
		// $this->db->delete('rolefunctions', array('roleId' => 1, 'funcId' => 96));
		// $this->db->delete('rolefunctions_giving', array('roleId' => 1, 'funcId' => 93));
		// $this->db->delete('rolefunctions_giving', array('roleId' => 1, 'funcId' => 94));
		// $this->db->delete('rolefunctions_giving', array('roleId' => 1, 'funcId' => 95));
		// $this->db->delete('rolefunctions_giving', array('roleId' => 1, 'funcId' => 96));
	}
}