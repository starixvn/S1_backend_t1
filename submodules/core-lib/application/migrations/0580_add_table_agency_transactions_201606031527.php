<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_transactions_201606031527 extends CI_Migration {

	private $tableName = 'agency_transactions';

	public function up() {
		$sql = <<<EOD
CREATE TABLE IF NOT EXISTS `agency_transactions` (
  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_time` datetime DEFAULT '0000-00-00 00:00:00',
  `from_user_type` varchar(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `from_username` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `to_user_type` varchar(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `to_username` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `amount` double NOT NULL DEFAULT '0',
  `remarks` text COLLATE utf8_unicode_ci,
  `ip_used` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;
EOD;
		$this->db->query($sql);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName, TRUE);
	}
}

///END OF FILE//////////
