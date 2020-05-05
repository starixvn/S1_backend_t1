<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_foreign_key_of_subwalletdetails_20151203 extends CI_Migration {
	public function up() {
		$this->db->query("ALTER TABLE subwalletdetails DROP FOREIGN KEY FK_subwalletdetails_gi");
		// $this->db->query("ALTER TABLE subwalletdetails ADD CONSTRAINT FK_subwalletdetails_gi FOREIGN KEY (gameId) REFERENCES external_system (id) ON DELETE CASCADE ON UPDATE CASCADE");
	}

	public function down() {
	}
}