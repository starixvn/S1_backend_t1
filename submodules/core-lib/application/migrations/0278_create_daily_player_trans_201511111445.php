<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_daily_player_trans_201511111445 extends CI_Migration {

    public function up() {
        $sql = <<<EOD
CREATE TABLE `daily_player_trans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `trans_amount` double DEFAULT 0.00,
  `trans_type` int(1) NOT NULL,
  `trans_count` int(11) unsigned DEFAULT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `note` text,
  `before_balance` double DEFAULT NULL,
  `after_balance` double DEFAULT NULL,
  `sub_wallet_id` int(10) unsigned DEFAULT NULL,
  `status` int(2) NOT NULL,
  `created_at` datetime NOT NULL,
  `flag` int(11) NOT NULL DEFAULT '1',
  `payment_account_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `player_promo_id` int(11) DEFAULT NULL,
  `promo_category` int(11) DEFAULT NULL,
  `total_before_balance` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_player_id` (`player_id`),
  KEY `idx_trans_type` (`trans_type`),
  KEY `idx_payment_account_id` (`payment_account_id`),
  KEY `idx_trans_count` (`trans_count`),
  KEY `idx_sub_wallet_id` (`sub_wallet_id`),
  KEY `idx_date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=16820 DEFAULT CHARSET=utf8;
EOD;
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_table('daily_player_trans');
    }
}