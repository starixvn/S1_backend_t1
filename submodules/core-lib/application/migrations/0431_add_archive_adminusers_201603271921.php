<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_archive_adminusers_201603271921 extends CI_Migration {

	public function up() {
		$sql = <<<EOD
CREATE TABLE archive_adminusers (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `realname` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `department` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `position` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `safetyQuestion` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `answer` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `lastLoginIp` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastLoginTime` datetime null,
  `lastLogoutTime` datetime null,
  `createTime` datetime,
  `createPerson` int(10) DEFAULT NULL,
  `status` int(1) unsigned DEFAULT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `session` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `maxWidAmt` double NOT NULL DEFAULT '0',
  `approvedWidAmt` double NOT NULL DEFAULT '0',
  `session_id` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

EOD;

		$this->db->query($sql);

		$this->db->query("create unique index idx_userid on archive_adminusers(userId)");
	}

	public function down() {
		$this->dbforge->drop_table('archive_adminusers');
	}
}
