<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_structures_201605141259 extends CI_Migration {

	private $tableName = 'agency_structures';

	public function up() {
		$sql = <<<EOD
CREATE TABLE IF NOT EXISTS `agency_structures` (
  `structure_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `structure_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `currency` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `credit_limit` int(11) unsigned NOT NULL DEFAULT '0',
  `available_credit` int(11) unsigned NOT NULL DEFAULT '0',
  `status` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '0-Active, 1-Suspended, 2-Frozen',
  `rev_share` int(3) unsigned NOT NULL DEFAULT '0',
  `rolling_comm` int(3) unsigned NOT NULL DEFAULT '0',
  `rolling_comm_basis` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '0-Total Bets, 1-Total Lost Bets, 2-Total Bets execpt Tie Bets',
  `total_bets_except` varchar(45) COLLATE utf8_unicode_ci,
  `allowed_level` int(3) unsigned NOT NULL DEFAULT '4',
  `allowed_level_names` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'JSON string for all level names',
  `can_have_sub_agent` int(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 - can; 1 - cannot;',
  `can_have_players` int(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 - can; 1 - cannot;',
  `vip_level` int(11) unsigned,
  `vip_level_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `vip_group_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `settlement_period` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '0-Daily;1-Weekly;2-Monthly;3-Quarterly;4-Manual;',
  `settlement_start_day` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '0-Mon;1-Tue;2-Wed;3-Thur;4-Fri;5-Sat;6-Sun;',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`structure_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;
EOD;
		$this->db->query($sql);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName, TRUE);
	}
}

///END OF FILE//////////
