<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_monthly_earnings_201511210606 extends CI_Migration {

	public function up() {
		$sql = <<<EOD
CREATE TABLE `monthly_earnings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `amount` double NOT NULL,
  `gross_net` double NOT NULL,
  `bonus_fee` double NOT NULL DEFAULT 0.00,
  `transaction_fee` double NOT NULL DEFAULT 0.00,
  `cashback` double NOT NULL DEFAULT 0.00,
  `admin_fee` double NOT NULL DEFAULT 0.00,
  `net` double NOT NULL,
  `rate_for_affiliate` double NOT NULL,
  `affiliate_id` int(10) unsigned NOT NULL,
  `year_month` varchar(100) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `type` int(10) NOT NULL,
  `paid_flag` int(10) DEFAULT 0 NOT NULL,
  `processed_by` int(10) NOT NULL,
  `note` varchar(255) NULL,
  `manual_flag` int(10) DEFAULT 1 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_affiliate_id` (`affiliate_id`),
  KEY `idx_year_month` (`year_month`)
);
EOD;
		$this->db->query($sql);
	}

	public function down() {
		$this->dbforge->drop_table('monthly_earnings');
	}
}