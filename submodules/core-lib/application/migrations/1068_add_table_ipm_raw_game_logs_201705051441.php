<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ipm_raw_game_logs_201705051441 extends CI_Migration {

	public function up() {

		$sql=<<<EOD
create table ipm_raw_game_logs
(
`id` int(11) NOT NULL AUTO_INCREMENT,
`Date Created` varchar(50),
`Bet No` varchar(50),
`Home` varchar(50),
`Member Code` varchar(50),
`Currency` varchar(50),
`Match No` varchar(50),
`Stake Type` varchar(50),
`Settled` varchar(50),
`Hand Type` varchar(50),
`Stake Bet On` varchar(50),
`Bet Score Home` varchar(50),
`Bet Score Away` varchar(50),
`Team Name Home` varchar(200),
`Team Name Away` varchar(200),
`Bet Type` varchar(50),
`Stake Odds` varchar(50),
`Bet AmtL` varchar(50),
`Bet AmtF` varchar(50),
`Bet Amt ActualL` varchar(50),
`Bet Amt ActualF` varchar(50),
`Stake Return AmtL` varchar(50),
`Stake Return AmtF` varchar(50),
`Admin Code Updated` varchar(50),
`Checked` varchar(50),
`Member Bet Reason Type` varchar(50),
`League Name` varchar(200),
`Season Matchday` varchar(200),
`Groud Type` varchar(50),
`Odds Type` varchar(50),
`Score Home` varchar(50),
`Score Away` varchar(50),
`ScoreHome1stHalf` varchar(50),
`ScoreAway1stHalf` varchar(50),
`Sport ID` varchar(50),
`BetTime` varchar(50),
`Report Desc Name` varchar(50),
`Sport Name` varchar(50),
`Bet Created Date` varchar(50),
`Bet Created Time` varchar(50),
`Company ID` varchar(50),
`Company` varchar(50),
`BTPaidAmount` varchar(50),
`BTPaidAmountL` varchar(50),
`BTPaid` varchar(50),
`BTSBCancel` varchar(50),
`BTCancel` varchar(50),
`BTCancelMatch` varchar(50),
`isBettrade` varchar(50),
`IP Address` varchar(50),
`Danger Confirm` varchar(50),
`Member Bet Reason Type Code` varchar(50),
`Danger Reason Type` varchar(50),
`Cancel` varchar(50),
`Effective Turnover` varchar(50),
`Source Feed` varchar(50),
`Main Ticket` varchar(50),
`bet_time` datetime,
PRIMARY KEY (`id`),
KEY `idx_betno` (`Bet No`),
KEY `idx_bet_time` (`bet_time`)
)
EOD;

		$this->db->query($sql);

	}

	public function down() {
		$this->dbforge->drop_table('ipm_raw_game_logs');
	}
}