<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_primary_key_to_friendreferralsettings_201509131342 extends CI_Migration {

	private $tableName = 'friendreferralsettings';

	public function up() {
		$this->db->query('ALTER TABLE friendreferralsettings ADD PRIMARY KEY (friendReferralSettingsId) ');
	}

	public function down() {
		$this->db->query('ALTER TABLE friendreferralsettings DROP PRIMARY KEY');
	}
}