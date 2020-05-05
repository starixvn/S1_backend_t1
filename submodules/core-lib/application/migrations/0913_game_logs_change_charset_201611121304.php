<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_game_logs_change_charset_201611121304 extends CI_Migration {

	private $game_logs = array('ab_game_logs','aghg_game_logs','agin_game_logs','agpt_game_logs','agshaba_game_logs','beteast_game_logs','bs_game_logs','crown_game_logs','ctxm_game_logs', 'ebet_game_logs','ebet2_game_logs','fishinggame_game_logs','gameplay_game_logs','gamesos_game_logs','gd_game_logs','gsag_game_logs','gsmg_game_logs','gspt_game_logs', 'hrcc_game_logs','ibc_game_logs','imslots_game_logs','inteplay_game_logs','kenogame_game_logs','lb_game_logs','onesgame_game_logs','pt_game_logs','qt_game_logs','seven77_game_logs','ttg_game_logs','uc_game_logs','wft_game_logs','xhtdlottery_game_logs');

	public function up() {

		foreach ($this->game_logs as $tableName) {
			$this->db->query("ALTER TABLE " . $tableName . " CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
		}

	}

	public function down() {

	}
}
