<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_agent_tracking_code_field_201710061355 extends CI_Migration {

	public function up() {

		//check alias
		$this->db->from('registration_fields')->where('registrationFieldId', 46);

		$qry=$this->db->get();
		$found=false;
		if(!empty($qry)){
			$row=$qry->row_array();
			if(!empty($row)){
				$found=true;
			}
		}

		if(!$found){
			//insert into registration_fields
			$this->db->insert('registration_fields', [
				'registrationFieldId'=>46,
				'alias'=>'agent_tracking_code',
				'field_name'=>'Agency Code',
				'type'=>'1',
				'visible'=>'0',
				'required'=>'0',
				'updatedOn'=>date('Y-m-d H:i:s'),
				'can_be_required'=>'1',
				'account_visible'=>'1',
				'account_required'=>'1',
			]);
		}

	}

	public function down() {

	}
}

