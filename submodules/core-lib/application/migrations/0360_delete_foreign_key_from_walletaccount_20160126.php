<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_delete_foreign_key_from_walletaccount_20160126 extends CI_Migration {

	public function up() {
		$this->db->query('ALTER TABLE `walletaccount` DROP FOREIGN KEY `FK_walletaccount_pai`');
	}

	public function down() {
		$this->db->query('ALTER TABLE `walletaccount` ADD CONSTRAINT `FK_walletaccount_pai` FOREIGN KEY (`playerAccountId`) REFERENCES `playeraccount` (`playerAccountId`) ON DELETE CASCADE ON UPDATE CASCADE;');
	}
}