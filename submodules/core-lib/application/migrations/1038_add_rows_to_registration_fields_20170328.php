<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_to_registration_fields_20170328 extends CI_Migration {

	public function up() {
 		$this->db->query("INSERT INTO `registration_fields` (`registrationFieldId`, `type`, `field_name`, `alias`, `visible`, `required`, `updatedOn`, `can_be_required`)
		VALUES
		 (36, '1', 'City', 'city', '0', '0', '2017-03-28 15:00:00', '0'),
		 (37, '1', 'Address', 'address', '0', '0', '2017-03-28 15:00:00', '0');
		 ");
	}

	public function down() {
		$this->db->query("DELETE FROM `registration_fields` where field_name in ('City', 'Address') ");
	}
}