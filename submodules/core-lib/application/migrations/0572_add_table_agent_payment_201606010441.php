<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agent_payment_201606010441 extends CI_Migration {

	private $tableName = 'agent_payment';

	public function up() {
		$sql = <<<EOD
CREATE TABLE IF NOT EXISTS `agent_payment` (
  `agent_payment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `account_number` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `bank_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `branch_address` varchar(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `payment_method` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fee` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0-active, 1-inactive',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `agent_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`agent_payment_id`),
  KEY `FK_agent_payment_agent_id` (`agent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;
EOD;
		$this->db->query($sql);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName, TRUE);
	}
}

///END OF FILE//////////
