<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_logs_201606041043 extends CI_Migration {

	private $tableName = 'agency_logs';

	public function up() {
		$sql = <<<EOD
CREATE TABLE IF NOT EXISTS `agency_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `done_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `done_by` varchar(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `done_to` varchar(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `action` varchar(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `details` varchar(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link_name` varchar(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link_url` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;
EOD;
		$this->db->query($sql);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName, TRUE);
	}
}

///END OF FILE//////////
