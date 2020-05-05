<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_resident_country_to_player_details extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE playerdetails ADD COLUMN residentCountry VARCHAR(255) NULL AFTER language");
		$this->db->query("INSERT INTO registration_fields (registrationFieldId, type, field_name, alias, visible, required, updatedOn, can_be_required) VALUES (33, '1', 'Resident Country', 'residentCountry', '0', '0', CURRENT_TIMESTAMP, '0')");
	}

	public function down() {
		$this->db->query("ALTER TABLE playerdetails DROP COLUMN residentCountry");
		$this->db->query("DELETE FROM registration_fields WHERE registrationFieldId = 33");
	}
}