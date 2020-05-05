<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_isb_game_logs_201610212034 extends CI_Migration {

    public function up() {
        $this->db->query("CREATE TABLE `isb_raw_game_logs` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `playerid` varchar(45) DEFAULT NULL,
          `operator` varchar(45) DEFAULT NULL,
          `currency` varchar(45) DEFAULT NULL,
          `sessionid` varchar(45) DEFAULT NULL,
          `gameid` int(11) DEFAULT NULL,
          `roundid` varchar(45) DEFAULT NULL,
          `status` varchar(45) DEFAULT NULL,
          `type` varchar(45) DEFAULT NULL,
          `transactionid` varchar(45) NOT NULL,
          `time` timestamp NULL DEFAULT NULL,
          `amount` double DEFAULT NULL,
          `balance` double DEFAULT NULL,
          `jpc` double DEFAULT NULL,
          `jpw` double DEFAULT NULL,
          `jpw_jpc` double DEFAULT NULL,
          `result_time` timestamp NULL DEFAULT NULL,
          `result_amount` double DEFAULT '0',
          `result_balance` double NOT NULL DEFAULT '0',
          `response_result_id` int(11) DEFAULT NULL,
          `uniqueid` varchar(45) DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `transactionid_UNIQUE` (`transactionid`),
          UNIQUE KEY `uniqueid_UNIQUE` (`uniqueid`)
        )");
    }

    public function down() {
        $this->db->query("DROP TABLE `isb_raw_game_logs`");
    }
}