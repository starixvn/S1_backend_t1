<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_settings_201606010443 extends CI_Migration {

	private $tableName = 'agency_settings';

	public function up() {
		$sql = <<<EOD
CREATE TABLE IF NOT EXISTS `agency_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'SAVE TRUE OR FALSE FOR BOOLEAN',
  `note` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agent_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_agency_settings_agent_id` (`agent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

EOD;
		$this->db->query($sql);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName, TRUE);
	}
}

///END OF FILE//////////
