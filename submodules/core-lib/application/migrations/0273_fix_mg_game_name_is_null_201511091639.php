<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_fix_mg_game_name_is_null_201511091639 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {
		$this->db->set('external_game_id', 'unknown')
			->where('game_code', 'unknown')->update($this->tableName);

		$platformId = MG_API;

		$this->load->model(array('game_description_model', 'game_logs'));
		$unknownGame = $this->game_description_model->getUnknownGame($platformId);

		//update mg_game_logs
		//insert into game_logs if display_name is null
		$sql = <<<EOD
SELECT row_id,
game_end_time,
account_number,
total_wager as bet_amount,
total_payout-total_wager as result_amount,
display_name as game,
display_game_category as game_type,
game_description.id as game_description_id,
game_description.game_type_id as game_type_id,
external_uniqueid,
mg_game_logs.response_result_id,
game_provider_auth.player_id,
game_provider_auth.login_name as playername
FROM mg_game_logs
JOIN game_provider_auth ON lower(mg_game_logs.account_number) = lower(game_provider_auth.login_name) COLLATE utf8_unicode_ci and game_provider_auth.game_provider_id = ?
left join game_description on game_description.external_game_id=ifnull(mg_game_logs.display_name,'unknown') COLLATE utf8_unicode_ci
WHERE
game_description.game_platform_id=? and game_description.void_bet!=1
and display_name is null
EOD;

		// $this->utils->debug_log($sql, $dateFrom, $dateTo);
		$qry = $this->db->query($sql, array($platformId, $platformId));
		// $this->db->from('mg_game_logs')->where('display_name is null',null,false);
		// $qry=$this->db->get();
		$rows = $qry->result();
		$cnt = 0;
		foreach ($rows as $row) {
			$external_uniqueid = $row->row_id;
			$this->db->select('id')->from('game_logs')->where('external_uniqueid', $external_uniqueid);
			$existsQry = $this->db->get();
			if ($existsQry && $existsQry->num_rows() > 0) {
				$this->utils->debug_log('exists', $external_uniqueid);
			} else {
				$gameLogs = array();
				//insert
				$game_type_id = $row->game_type_id;
				$game_description_id = $row->game_description_id;

				if (empty($game_description_id)) {
					$game_description_id = $unknownGame->id;
					$game_type_id = $unknownGame->game_type_id;
				}
				//search game name
				$username = strtolower($row->playername);
				$gameLogs['player_username'] = $username;
				$gameLogs['player_id'] = $row->player_id;
				$gameLogs['game_platform_id'] = $platformId;
				$gameLogs['bet_amount'] = $row->bet_amount;
				$gameLogs['result_amount'] = $row->result_amount;
				$gameLogs['response_result_id'] = $row->response_result_id;
				$gameLogs['external_uniqueid'] = $row->external_uniqueid;
				$gameDate = new DateTime($row->game_end_time);
				$gameDateStr = $this->utils->formatDateTimeForMysql($gameDate);
				$gameLogs['start_at'] = $gameDateStr;
				$gameLogs['end_at'] = $gameDateStr;
				$gameLogs['game_type'] = $row->game_type;
				$gameLogs['game'] = $row->game;
				$gameLogs['game_description_id'] = $game_description_id;
				$gameLogs['game_type_id'] = $game_type_id;
				$this->game_logs->syncToGameLogs($gameLogs);
				$this->utils->debug_log('insert row', $external_uniqueid);
			}
			$cnt++;
		}
		$this->utils->debug_log('processed', $cnt);
	}

	public function down() {
	}
}