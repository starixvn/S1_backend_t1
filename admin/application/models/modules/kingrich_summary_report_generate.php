<?php

/**
 * General behaviors include
 * * generate report
 *
 * @category kingrich_summary_report_generate
 * @version 3.06.02
 * @copyright 2013-2018 TripleOneTech
 */

trait kingrich_summary_report_generate {

	public function generate_kingrich_summary_report_hourly($date_from, $date_to) {

        if( empty($date_from) && empty($date_to) ) {

        	$date_from = date ("Y-m-d H:i:s", strtotime("-2 day", strtotime('today')));
        	$date_to = date ("Y-m-d 23:59:59", strtotime('today'));
		}

		$date_from = date ("Y-m-d H:i:s", strtotime($date_from));
        $date_to = date ("Y-m-d 23:59:59", strtotime($date_to));

		$this->utils->info_log('debug logs step by step',$date_from." = ".$date_to);
		$this->load->model(array('game_logs','kingrich_summary_report'));
		//count data with real player only
		$player_tag_test_account = $this->utils->getConfig('player_tag_test_account');

		if ( ! is_array($player_tag_test_account)) {
			$player_tag_test_account = [$player_tag_test_account];
		}

		foreach ($player_tag_test_account as &$tag) {
			$tag = "'{$tag}'";
		}

		$sql = "SELECT DISTINCT p.playerId FROM playertag AS pt JOIN tag AS t ON t.tagId = pt.tagId JOIN player AS p ON p.playerId = pt.playerId WHERE p.deleted_at IS NULL AND t.tagName IN (" . implode(',', $player_tag_test_account) . ")";
		$query = $this->db->query($sql);
		$rows = $query->result_array();
		$test_players_id = array_column($rows, 'playerId');

		//remove gametype not listed in kingrich

		$rows = [];

		$kingrich_gametypes = $this->utils->getConfig('kingrich_gametypes');

		if(empty($kingrich_gametypes)) $kingrich_gametypes = array();

		$this->utils->info_log('debug logs step by step kingrich_gametypes',$kingrich_gametypes);
		$this->load->model(['game_logs']);
		$kingrich_currency_branding = $this->utils->getConfig('kingrich_currency_branding');


		foreach ($kingrich_gametypes as $key => $value) {

			if(!isset($value['tag_id']) || !is_array($value['tag_id'])) continue;

			$game_tag_included = [];
			$game_provider_included = [];
			$row_data = [];

			foreach ($value['tag_id'] as $tag_key => $tag_value) {

				if(empty($tag_key)) continue;

				$game_tag_included = array_merge($game_tag_included, $tag_value);
				$game_provider_included = array_merge($game_provider_included, array_map('intval', explode(',', $tag_key)));
				
			}
							
			if( !empty($kingrich_currency_branding) && $this->utils->getConfig('multiple_currency_enabled') ){
				foreach ($kingrich_currency_branding as $currency => $value) {
					$this->generateReportNewProcess($date_to, $date_from, $test_players_id, $game_tag_included, $game_provider_included, $key, $rows, $currency);
				}
			}
			else
				$this->generateReportNewProcess($date_to, $date_from, $test_players_id, $game_tag_included, $game_provider_included, $key, $rows);			
		}
		

		//Deleting old data for recyncing
		$this->db->where('settlement_date <= ', $date_to);
		$this->db->where('settlement_date >= ', $date_from);
		$this->db->delete('kingrich_summary_reports');
		$this->utils->info_log('debug logs step by step removing existing data
			',$date_from." = ".$date_to);

		if( !empty($rows) ){
			foreach ($rows as $key => $value) {
				$response = $this->kingrich_summary_report->insertRecord($value);	
				$rows[$key]["status"] = $response;
			}
		}
		
		$this->utils->debug_log('generate_kingrich_summary_report_hourly logs row',$rows);
		return $rows;
	}

	/**
	 * Run new process of retriving kingrich summary report
	 * @param  string $date_to                
	 * @param  string $date_from              
	 * @param  string $test_players_id        
	 * @param  string $game_tag_included      
	 * @param  string $game_provider_included 
	 * @param  string $game_type              
	 * @param  array &$rows                  
	 * @param  string $currency               
	 * @return void                         
	 */
	private function generateReportNewProcess($date_to, $date_from, $test_players_id, $game_tag_included, $game_provider_included, $game_type, &$rows, $currency = ""){

		// -- add currency if available
		$select_currency = '';
		if( !empty($currency) && $this->utils->getConfig('multiple_currency_enabled') ){
			$select_currency = ', pl.currency as currency';
		}

		$this->db->select("gl.end_at as settlement_date, SUM(gl.trans_amount) as sum_bet_amount, SUM(gl.trans_amount) as sum_debit_amount, SUM(IF(gl.win_amount > 0, gl.win_amount + gl.trans_amount, 0)) as sum_credit_amount, SUM(gl.trans_amount - ( IF(gl.win_amount > 0, gl.win_amount + gl.trans_amount, 0))) as sum_net_amount, COUNT(gl.id) as sum_number_of_bets" . $select_currency, FALSE);

	    $this->db->from('game_logs gl');
	    $this->db->join('player pl', 'pl.playerId = gl.player_id', 'left');
	    $this->db->join('game_description gd', 'gd.id = gl.game_description_id', 'left');
	    $this->db->join('game_type gt', 'gt.id = gd.game_type_id', 'left');
	    $this->db->where('gl.end_at <= ', $date_to);
	    $this->db->where('gl.end_at >= ', $date_from);
	    $this->db->where('gl.flag', Game_logs::FLAG_GAME);
		    
	    $this->db->group_by(['date(gl.end_at)']);

	    if(!empty($test_players_id))
	    	$this->db->where("pl.playerId NOT IN (" . implode(',', $test_players_id) . ")");
	    
	    if(!empty($game_tag_included))
	    	$this->db->where("gt.game_tag_id IN (" . implode(',', $game_tag_included) . ")");
	    
	    if(!empty($game_provider_included))
	    	$this->db->where("gl.game_platform_id IN (" . implode(',', $game_provider_included) . ")");
	    
	    if( !empty($currency) && $this->utils->getConfig('multiple_currency_enabled') )
			$this->db->where("pl.currency",$currency);

		$row_data = $this->runMultipleRowArray();
		if(!empty($row_data)){
			$this->utils->debug_log('generate_kingrich_summary_report_hourly logs gametype',$game_type);
			foreach ($row_data as $row_data_key => $row_data_value) {
				$row_data[$row_data_key]["kingrich_game_type"] = $game_type;
			}

			$rows = array_merge($rows, $row_data);
		}
	}
}
