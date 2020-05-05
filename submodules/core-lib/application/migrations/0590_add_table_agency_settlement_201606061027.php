<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_settlement_201606061027 extends CI_Migration {

	private $tableName = 'agency_settlement';

	public function up() {
		$sql = <<<EOD
CREATE TABLE IF NOT EXISTS `agency_settlement` (
  `settlement_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'current' COMMENT 'settled, unsettled, current',
  `frozen` int(1) unsigned NOT NULL DEFAULT '0' COMMENT 'when frozen you cannot change it',
  `bets` double NOT NULL DEFAULT '0',
  `wins` double NOT NULL DEFAULT '0',
  `bonuses` double NOT NULL DEFAULT '0',
  `rebates` double NOT NULL DEFAULT '0',
  `net_gaming` double NOT NULL DEFAULT '0',
  `rev_share_amt` double NOT NULL DEFAULT '0',
  `lost_bets` double NOT NULL DEFAULT '0',
  `bets_except_tie` double NOT NULL DEFAULT '0',
  `roll_comm_amt` double NOT NULL DEFAULT '0',
  `payable_amt` double NOT NULL DEFAULT '0',
  `balance` double NOT NULL DEFAULT '0' COMMENT 'sum of payable_amt under unsettled status',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `agent_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`settlement_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;
EOD;
		$this->db->query($sql);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName, TRUE);
	}
}

///END OF FILE//////////
