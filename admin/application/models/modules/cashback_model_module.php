<?php
trait cashback_model_module {

	public function group_sum_player_bet($cashback_bet_table, $player_id, $start, $end){
		$sql = <<<EOD
SELECT tpgh.player_id, player.levelId, sum(tpgh.bet_amount) as betting_total, tpgh.game_description_id, tpgh.game_type_id, tpgh.game_platform_id
  FROM $cashback_bet_table as tpgh
  join game_description as gd on tpgh.game_description_id=gd.id and gd.no_cash_back!=1
  join player on player.playerId=tpgh.player_id
where tpgh.end_at>=? and tpgh.end_at<=?
and player.disabled_cashback!=1
and tpgh.player_id=?
group by tpgh.game_description_id, tpgh.game_platform_id
EOD;

		$playerBetByDate=$this->runRawSelectSQL($sql, [$this->utils->formatDateTimeForMysql($start),
			$this->utils->formatDateTimeForMysql($end), $player_id]);

		if(empty($playerBetByDate)){
			$playerBetByDate=[];
		}
		return $playerBetByDate;
	}

	public function generate_start_end($date, $startHour, $endHour){

		$lastDate = $this->utils->getLastDay($date);

		if (intval($endHour) == 23) {
			//all yesterday
			$date = $lastDate;
		}

		$date = str_replace('-', '', $date);
		$lastDate = str_replace('-', '', $lastDate);

		$start=$lastDate . $startHour.'0000';
		$end=$date . $endHour.'5959';

		return [$start, $end];
	}

	public function generate_start_end_datetime($date, $startHour, $endHour){

		$lastDate = $this->utils->getLastDay($date);

		if (intval($endHour) == 23) {
			//all yesterday
			$date = $lastDate;
		}

		$start=new DateTime($lastDate.' '.$startHour.':00:00');
		$end=new DateTime($date.' '.$endHour.':59:59');

		return [$start, $end];
	}

	/**
	 *
	 *
	 * @param  DateTime $start
	 * @param  DateTime $end
	 * @return string table name
	 */
	public function generate_cashback_bet_table($start, $end) {
		$this->load->model(['game_logs']);

		$tablename='gl_'.$start->format('YmdHis').'_'.$end->format('YmdHis');
		$this->utils->debug_log('create table '.$tablename, $start, $end);

		//drop table
		$sql='drop table if exists '.$tablename;
		$this->runRawUpdateInsertSQL($sql);

		//create again
		$sql='create table '.$tablename.' select * from game_logs where end_at>=? and end_at<=? and flag=?';
		$this->runRawUpdateInsertSQL($sql, [$this->utils->formatDateTimeForMysql($start),
			$this->utils->formatDateTimeForMysql($end), Game_logs::FLAG_GAME]);

		//create index
		$this->addIndex($tablename, 'idx_player_id', 'player_id');
		$this->addIndex($tablename, 'idx_game_platform_id', 'game_platform_id');
		$this->addIndex($tablename, 'idx_game_type_id', 'game_type_id');
		$this->addIndex($tablename, 'idx_game_description_id', 'game_description_id');
		$this->addIndex($tablename, 'idx_end_at', 'end_at');
		$this->addIndex($tablename, 'idx_flag', 'flag');

		return $tablename;
	}

    public function get_disable_cashback_time_range_list($playerId, $beg, $end){
        $result = [];

        $this->load->model(array('player_promo', 'promorules','group_level'));
        $promos = $this->player_promo->getPlayerActivePromo( $playerId );
		if(!empty($promos)){
			foreach( $promos as $row ){
				$promo =  $this->promorules->getPromoRules($row->promorulesId);
				if( intval(@$promo["disable_cashback_entirely"]) <= 0 ){ continue; }

				$time_beg = new DateTime( $row->dateJoined );
				$time_end = new DateTime("now");
				if( $promo["disable_cashback_length"] > 0 ){
					$time_end = clone $time_beg;
					$time_end->add( DateInterval::createFromDateString( $promo['disable_cashback_length']." hour ") );
				}

				$result []= [
					'start_disable' => $this->utils->formatDateTimeForMysql($time_beg),
					'end_disable' => $this->utils->formatDateTimeForMysql($time_end),
					'note' => 'filter by promorules'
				];
			}
		}

        return $result;
    }

	public function get_disabled_time_ranger_list($playerId, $start, $end){

		$time_list=[];

		$types=[Transactions::DEPOSIT,Transactions::WITHDRAWAL,Transactions::ADD_BONUS];
		$types=implode(',', $types);

		$sql=<<<EOD
select transactions.amount, transactions.transaction_type, transactions.created_at, promorules.disable_cashback_if_not_finish_withdraw_condition
from transactions
left join playerpromo on transactions.player_promo_id=playerpromo.playerpromoId
left join promorules on promorules.promorulesId=playerpromo.promorulesId
where transactions.transaction_type in ({$types})
and transactions.to_id=?
and transactions.to_type=?
and transactions.created_at>=? and transactions.created_at<=?
EOD;

		$rows=$this->runRawSelectSQLArray($sql, [$playerId, Transactions::PLAYER,
			$this->utils->formatDateTimeForMysql($start), $this->utils->formatDateTimeForMysql($end)]);

		//get last transaction
		$sql=<<<EOD
select transactions.amount, transactions.transaction_type, transactions.created_at, promorules.disable_cashback_if_not_finish_withdraw_condition
from transactions
left join playerpromo on transactions.player_promo_id=playerpromo.playerpromoId
left join promorules on promorules.promorulesId=playerpromo.promorulesId
where transactions.transaction_type in ({$types})
and transactions.to_id=?
and transactions.to_type=?
and transactions.created_at<?
order by transactions.created_at desc
limit 1
EOD;

		$lastTransRows=$this->runRawSelectSQLArray($sql, [$playerId, Transactions::PLAYER,
			$this->utils->formatDateTimeForMysql($start)]);

		$lastTransRow=null;
		if(!empty($lastTransRows)){
			$lastTransRow=$lastTransRows[0];
		}

		$meet_promotion=false;
		$start_disable=null;
		$end_disable=null;

		if(!empty($lastTransRow)){
			if($lastTransRow['disable_cashback_if_not_finish_withdraw_condition']=='1'){
				//record current and next
				$start_disable=$lastTransRow['created_at'];
				$meet_promotion=true;

				$this->utils->debug_log('disable_cashback_if_not_finish_withdraw_condition', $playerId, $lastTransRow);
			}
		}

		if(!empty($rows)){
			usort($rows, function($a, $b){
				return strcmp($a['created_at'], $b['created_at']);
			});

			$end_disable_types=[Transactions::DEPOSIT,Transactions::WITHDRAWAL];

			foreach ($rows as $row) {
				if($row['disable_cashback_if_not_finish_withdraw_condition']=='1'){
					//record current and next
					$start_disable=$row['created_at'];
					$meet_promotion=true;
				}

				if($meet_promotion && in_array($row['transaction_type'], $end_disable_types)){
					$end_disable=$row['created_at'];
					$time_list[]=['start_disable'=>$start_disable, 'end_disable'=>$end_disable];
					//reset
					$meet_promotion=false;
					$start_disable=null;
					$end_disable=null;
				}
			}

		}
		if($meet_promotion){
			//to last time
			$end_disable=$this->utils->formatDateTimeForMysql($end);
			$time_list[]=['start_disable'=>$start_disable, 'end_disable'=>$end_disable];
			//reset
			$meet_promotion=false;
			$start_disable=null;
			$end_disable=null;
		}
		return $time_list;
	}

	public function delete_bet_for_cashback($cashback_bet_table, $playerId, $timeList){
		$result=true;
		if(!empty($timeList)){

			$timeQry='';

			foreach ($timeList as $time) {
				$start_disable=$time['start_disable'];
				$end_disable=$time['end_disable'];

				$timeQry.=' or (end_at>="'.$start_disable.'" and end_at<="'.$end_disable.'")';
			}

			$sql='delete from '.$cashback_bet_table.' where player_id=? and (1=0 '.$timeQry.')';
			$this->utils->debug_log('delete bet sql '.$cashback_bet_table, $sql);

			$result=$this->runRawUpdateInsertSQL($sql, [$playerId]);
		}

		return $result;
	}

	/**
	 * todo
	 *
	 * @param  [type] $cashback_bet_table [description]
	 * @param  [type] $wc_amount_map      [description]
	 * @param  [type] $player_id          [description]
	 * @param  [type] $start              [description]
	 * @param  [type] $end                [description]
	 * @return [type]                     [description]
	 */
	public function generate_player_bet_amount($cashback_bet_table, $wc_amount_map, $player_id, $start, $end){

		if($this->utils->isEnabledFeature('auto_deduct_withdraw_condition_from_bet')){

			$playerBetByDate=$this->group_sum_player_bet($cashback_bet_table, $player_id, $start, $end);

			//get withdraw condition
			if(isset($wc_amount_map[$player_id])){
				$wc_info=$wc_amount_map[$player_id];
				$start_from_wc=$wc_info['started_at'];
				//minus until wc_amount
				$wc_amount=$wc_info['amount'];
				if(is_string($wc_info['started_at'])){
					$start_from_wc=new DateTime($wc_info['started_at']);
				}
				//search bet again to minus
				$wc_bet_list=$this->group_sum_player_bet($cashback_bet_table, $player_id, $start_from_wc, $end);
				if(!empty($wc_bet_list)){
					$wc_bet_map=[];
					foreach ($wc_bet_list as $row) {
						$key=$row->player_id.'-'.$row->game_description_id;
						$wc_bet_map[$key]=$row;
					}

					foreach ($playerBetByDate as &$row) {
						$key=$row->player_id.'-'.$row->game_description_id;
						if(isset($wc_bet_map[$key])){

							$this->utils->debug_log('minus withdraw condition player_id:'.$player_id, $key, 'wc_amount:'.$wc_amount, $row->betting_total, $wc_bet_map[$key]->betting_total);

							if($wc_amount>0){
								$minus_wc_amount=$wc_bet_map[$key]->betting_total;
								if($minus_wc_amount>$wc_amount){
									$minus_wc_amount=$wc_amount;
								}

								$this->utils->debug_log($row->betting_total.'-'.$minus_wc_amount);

								$row->betting_total=round($row->betting_total-$minus_wc_amount, 2);
								$wc_amount=round($wc_amount-$minus_wc_amount, 2);
							}

							if($row->betting_total<0){
								$row->betting_total=0;
							}
						}
					}
				}
			}

			return $playerBetByDate;

		}else{
			$timeList=$this->get_disabled_time_ranger_list($player_id, $start, $end);

			$this->utils->debug_log('get_disabled_time_ranger_list '.$player_id, $start, $end, $timeList);
			if(!empty($timeList)){
				$this->delete_bet_for_cashback($cashback_bet_table, $player_id, $timeList);
			}

			$playerBetByDate=$this->group_sum_player_bet($cashback_bet_table, $player_id, $start, $end);

			return $playerBetByDate;
		}
	}

	/**
	 * generate cashback
	 *
	 * 1. get all active players by time
	 * 2. one by one process player
	 * 3. get disabled time ranger list
	 * 4. delete bet
	 * 5. get level
	 *
	 * @param string $date
	 * @param string $startHour
	 * @param string $endHour
	 * @param int $playerId
	 * @param int $withdraw_condition_bet_times
	 *
	 * @return int
	 */
	public function generate_cashback($date, $startHour, $endHour, $playerId = null, $withdraw_condition_bet_times=0, &$result=false) {

		$success=false;

		list($start, $end)=$this->generate_start_end_datetime($date, $startHour, $endHour);

		$this->utils->debug_log('generate_cashback params', $date, $startHour, $endHour, $start, $end, $playerId, $withdraw_condition_bet_times);

		//get withdraw condition
		$this->load->model(['withdraw_condition']);
		$wc_amount_map=$this->withdraw_condition->getAvailableWithdrawConditionWithBet($date, $startHour, $endHour);

		$mapPlayerBet=$this->sumPlayerBetByDate($date, $startHour, $endHour, $playerId);
		$this->utils->debug_log('sumPlayerBetByDate', $date, $startHour, $endHour, $playerId, 'count', count($mapPlayerBet));

		$this->load->model(array('player_model', 'game_logs', 'transactions', 'users', 'game_description_model'));
		$unknownGames = $this->game_description_model->getUnknownGameList();

		$commonRules=$this->getCommonCashbackRules();

		$extra_info=[
			'mapPlayerBet'=>$mapPlayerBet,
			'commonRules'=>$commonRules,
		];

		$isNoCashbackBonusForNonDepositPlayer=$this->isNoCashbackBonusForNonDepositPlayer();

		if (!empty($mapPlayerBet)) {

			$cashback_bet_table=$this->generate_cashback_bet_table($start, $end);

			$this->startTrans();

			$always_enable_unknown_games_on_callback = $this->getConfig('always_enable_unknown_games_on_callback');

			$extra_info['levelCashbackMap']=$this->getFullCashbackPercentageMap();

			$flag = Game_logs::FLAG_GAME;

			foreach ($mapPlayerBet as $player_id=>$betSum) {

				if($betSum>0){
					$rate       = 0;
					$max_bonus  = 0;
					$history_id = null;
					$levelId    = null;

					$playerBetByDate=$this->generate_player_bet_amount($cashback_bet_table, $wc_amount_map, $player_id, $start, $end);

					foreach ($playerBetByDate as $pbbd) {

						$game_platform_id = $pbbd->game_platform_id;
						$game_description_id = $pbbd->game_description_id;
						$game_type_id = $pbbd->game_type_id;

						if ($pbbd->betting_total > 0) {

							$rate = 0;
							$max_bonus = 0;
							$history_id = null;
							$player_id = $pbbd->player_id;
							$levelId= $pbbd->levelId;

							if($isNoCashbackBonusForNonDepositPlayer){
								//should check deposit
								$playerObj=$this->player_model->getPlayerArrayById($player_id);
								if($playerObj['totalDepositAmount']<=0){
									$this->utils->debug_log('ignore player '.$player_id.' for none deposit');
									continue;
								}
							}

							// get daily rate
							$playerDailyRate = $this->getPlayerRateFromLevel($player_id, $levelId,
								$game_platform_id, $game_type_id, $game_description_id, $extra_info);

							if ($playerDailyRate) {
								$rate = $playerDailyRate->cashback_percentage;
								$max_bonus = $playerDailyRate->cashback_maxbonus;
								$history_id = $playerDailyRate->id;
								$level_id = $playerDailyRate->level_id;
							}

							//only for exist cashback rate
							if ($rate > 0) {

								$this->utils->debug_log('final rate', $rate, 'player_id', $player_id, 'game_description_id', $game_description_id,
									'history_id', $history_id, 'level_id', $level_id, 'betting', $pbbd->betting_total, 'betting*rate', $pbbd->betting_total * ($rate / 100));


								$game_platform_id = $pbbd->game_platform_id;
								$game_description_id = $pbbd->game_description_id;
								$game_type_id = $pbbd->game_type_id;
								$total_date = $date;
								$cashback_amount = $this->utils->roundCurrencyForShow($pbbd->betting_total * ($rate / 100));
								$withdraw_condition_amount= $this->utils->roundCurrencyForShow($cashback_amount * $withdraw_condition_bet_times);
								$this->syncCashbackDaily($player_id, $game_platform_id, $game_description_id, $total_date,
									$cashback_amount, $history_id, $game_type_id, $level_id, $rate,
									$this->utils->roundCurrencyForShow($pbbd->betting_total),
									$withdraw_condition_amount , $max_bonus);
							}
						}
					}
				}
			}

			$success=$this->endTransWithSucc();

			$this->utils->debug_log('drop table '. $cashback_bet_table);
			$this->db->query('drop table '.$cashback_bet_table);

		}
		return $success;
	}

	/**
	 * generate cashback by time
	 *
	 * 1. process player cashback
	 * 2. get player withdraw condition
	 * 2. get disabled time ranger list
	 * 3. delete bet
	 * 4. get level
	 *
	 * @param string $timeStart
	 * @param string $timeEnd
	 * @param int $playerId
	 * @param int $cashback_request_id
	 * @param int $withdraw_condition_bet_times
	 *
	 * @return int
	 */
	public function generate_cashback_by_time($timeStart, $timeEnd, $playerId, $cashback_request_id, $withdraw_condition_bet_times=0) {

		$success=false;

		$start=new DateTime($timeStart);
		$end=new DateTime($timeEnd);

		$this->utils->debug_log('generate_cashback_by_time params', $timeStart, $timeEnd, $start, $end, $playerId, $withdraw_condition_bet_times);

		//get withdraw condition
		$this->load->model(['withdraw_condition']);
		$wc_amount_map=$this->withdraw_condition->getAvailableWithdrawConditionWithBetByTime($timeStart, $timeEnd);

		$mapPlayerBet=$this->sumPlayerBetByTime($timeStart, $timeEnd, $playerId);
		$this->utils->debug_log('sumPlayerBetByTime', $timeStart, $timeEnd, $playerId, 'count', count($mapPlayerBet));
		$this->utils->debug_log($mapPlayerBet);

		$this->load->model(array('player_model', 'game_logs', 'transactions', 'users', 'game_description_model'));
		$unknownGames = $this->game_description_model->getUnknownGameList();

		$commonRules=$this->getCommonCashbackRules();
		$extra_info=[
			'mapPlayerBet'=>$mapPlayerBet,
			'commonRules'=>$commonRules,
		];

		$isNoCashbackBonusForNonDepositPlayer=$this->isNoCashbackBonusForNonDepositPlayer();

		if (!empty($mapPlayerBet)) {

			$cashback_bet_table=$this->generate_cashback_bet_table($start, $end);

			$this->startTrans();

			$always_enable_unknown_games_on_callback = $this->getConfig('always_enable_unknown_games_on_callback');

			$extra_info['levelCashbackMap']=$this->getFullCashbackPercentageMap();

			$flag = Game_logs::FLAG_GAME;

			foreach ($mapPlayerBet as $player_id=>$betSum) {

				if($betSum>0){
					$rate = 0;
					$max_bonus = 0;
					$history_id = null;
					$levelId= null;

					$playerBetByDate=$this->generate_player_bet_amount($cashback_bet_table, $wc_amount_map, $player_id, $start, $end);

					$this->utils->debug_log('$playerBetByDate', $playerBetByDate);

					foreach ($playerBetByDate as $pbbd) {

						$game_platform_id = $pbbd->game_platform_id;
						$game_description_id = $pbbd->game_description_id;
						$game_type_id = $pbbd->game_type_id;
						if ($pbbd->betting_total > 0) {

							$rate = 0;
							$max_bonus = 0;
							$history_id = null;
							$player_id = $pbbd->player_id;
							$levelId= $pbbd->levelId;

							if($isNoCashbackBonusForNonDepositPlayer){
								//should check deposit
								$playerObj=$this->player_model->getPlayerArrayById($player_id);
								if($playerObj['totalDepositAmount']<=0){
									$this->utils->debug_log('ignore player '.$player_id.' for none deposit');
									continue;
								}
							}

							// get daily rate
							$playerDailyRate = $this->getPlayerRateFromLevel($player_id, $levelId,
								$game_platform_id, $game_type_id, $game_description_id, $extra_info);
							$this->utils->debug_log('$playerDailyRate', $playerDailyRate, $game_platform_id, $game_type_id, $game_description_id);

							if ($playerDailyRate) {
								$rate = $playerDailyRate->cashback_percentage;
								$max_bonus = $playerDailyRate->cashback_maxbonus;
								$history_id = $playerDailyRate->id;
								$level_id = $playerDailyRate->level_id;
							}

							//only for exist cashback rate
							if ($rate > 0) {

								$this->utils->debug_log('final rate', $rate, 'player_id', $player_id, 'game_description_id', $game_description_id,
									'history_id', $history_id, 'level_id', $level_id, 'betting', $pbbd->betting_total, 'betting*rate', $pbbd->betting_total * ($rate / 100));

								$game_platform_id = $pbbd->game_platform_id;
								$game_description_id = $pbbd->game_description_id;
								$game_type_id = $pbbd->game_type_id;
								$original_bet_amount = $this->utils->roundCurrencyForShow($pbbd->betting_total);

								$cashback_amount = $this->utils->roundCurrencyForShow($pbbd->betting_total * ($rate / 100));
								$withdraw_condition_amount= $this->utils->roundCurrencyForShow($cashback_amount * $withdraw_condition_bet_times);
								$this->syncCashbackByTime($player_id, $cashback_request_id,
									$game_platform_id, $game_description_id, $timeStart, $timeEnd,
									$cashback_amount, $history_id, $game_type_id, $level_id, $rate,
									$this->utils->roundCurrencyForShow($pbbd->betting_total),
									$withdraw_condition_amount , $max_bonus, $original_bet_amount);
							}
						}
					}
				}
			}

			$success=$this->endTransWithSucc();

			if($this->utils->isEnabledFeature('enabled_realtime_cashback_table_mode')){

				$this->utils->debug_log('drop table '. $cashback_bet_table);
				$dropSql='drop table if exists '.$cashback_bet_table;

				$this->runRawUpdateInsertSQL($dropSql);

			}else{

				$this->utils->debug_log('drop view '. $cashback_bet_table);
				$dropSql='drop view if exists '.$cashback_bet_table;

				$this->runRawUpdateInsertSQL($dropSql);
			}
		}
		return $success;
	}

	public function sum_paid_game_cashback_amount($start, $end, $player_id, $cashback_game_platform=null, &$sum_paid_cashback_amount_map=[]){
		$this->db->select('player_id, game_description_id, sum(amount) as amount, sum(paid_amount) as paid_amount, sum(bet_amount) as bet_amount, sum(withdraw_condition_amount) as withdraw_condition_amount')
		    ->from('total_cashback_player_game')
			->where('player_id', $player_id)->where('time_start >=', $start)
			->where('time_end <=', $end)
			->where('paid_flag', self::DB_TRUE)
			->group_by('player_id, game_description_id');

		if(!empty($cashback_game_platform)){
			$this->db->where('game_platform_id', $cashback_game_platform);
		}

		$sum_paid_game_cashback_amount=[];
		$rows=$this->runMultipleRowArray();

		if(!empty($rows)){
			foreach ($rows as $row) {
				$playerId=$row['player_id'];
				$key=$playerId.'-'.$row['game_description_id'];

				$sum_paid_game_cashback_amount[$key]=$row;

				if(!isset($sum_paid_cashback_amount_map[$playerId])){
					$sum_paid_cashback_amount_map[$playerId]=0;
				}

				$sum_paid_cashback_amount_map[$playerId]+=$row['amount'];

			}
		}
		return $sum_paid_game_cashback_amount;
	}

	public function process_all_player_cashback_amount($currentDate, $onlyPlayerId, $cashback_game_platform, $create_request,
			$notes=null, $paid=false, $dry_run=false, $batch_type=false){

		$this->load->model(['cashback_request']);

		$cashback_settings = $this->group_level->getCashbackSettings();
		//always yesterday
		$lastDay=$this->utils->getLastDay($currentDate);

		$first_time=$cashback_settings->fromHour.':00:00';
		$last_time=$cashback_settings->toHour.':59:59';

		$this->utils->debug_log('process_all_player_cashback_amount realtime cashback', $first_time, $last_time);

		$create_request=!$dry_run;
		if($first_time=='00:00:00'){

			$start=$lastDay.' 00:00:00';
			$end=$lastDay.' 23:59:59';

		}else{

			$start=$lastDay.' '.$first_time;
			$end=$currentDate.' '.$last_time;

		}

		$startHour=$this->utils->formatDateHourForMysql(new DateTime($start));
		$endHour=$this->utils->formatDateHourForMysql(new DateTime($end));

		$this->db->distinct()->select('player_id')->from('total_player_game_hour')
			->where('date_hour >=', $startHour)
			->where('date_hour <=', $endHour);

		$rows=$this->runMultipleRowArray();

		$this->utils->printLastSQL();

		$total_cashback_amount=$total_bet_amount=0;
		$cnt=0;
		$adminUserId=1;

		$result=[];

		if(!empty($rows)){

			foreach ($rows as $row) {

				if(!empty($onlyPlayerId) && $onlyPlayerId!=$row['player_id']){
					$this->utils->debug_log('ignore '.$row['player_id'], 'onlyPlayerId', $onlyPlayerId);
					continue;
				}

				$cnt++;

				$player_id=$row['player_id'];

				$cashbackRequestData=null;
				$cashback_amount= $bet_amount=0;
				list($cashback_amount, $bet_amount)=$this->process_cashback_amount($player_id, $start, $end,
					$cashback_game_platform, $create_request, $notes, $cashbackRequestData, $batch_type);

				$this->utils->debug_log('after player_id', $player_id, 'start', $start, 'end', $end, 'cashback_amount', $cashback_amount, 'bet_amount', $bet_amount, 'cashback_request_id', $cashbackRequestData['id']);

				$cashback_request_id=@$cashbackRequestData['id'];
				if($paid && !empty($cashback_request_id)){
					$success=$this->lockAndTransForPlayerBalance($player_id, function()
							use($cashback_request_id, $adminUserId, &$message){

						return $this->cashback_request->approveCashbackRequest($cashback_request_id, $adminUserId, $message);

					});

					if(!$success){
						$this->utils->debug_log('approve cashback:'.$cashback_request_id, $success, $message);
					}
				}

				$total_cashback_amount+=$cashback_amount;
				$total_bet_amount+=$bet_amount;

				$result[$player_id]=[
					'bet_amount'=>$bet_amount,
					'cashback_amount'=>$cashback_amount,
					'cashback_request_id'=>$cashback_request_id,
				];

			}
		}

		return $result;
	}

	public function onlyGetCashbackAmount($player_id, $start, $end, $cashback_game_platform = null) {
		$result=$this->process_cashback_amount($player_id, $start, $end, $cashback_game_platform);

		return $result;
	}

	public function process_daily_max_bonus_limit($start, $end, $playerMaxBonusMap, $sum_paid_cashback_amount_map,
		$withdraw_condition_bet_times, &$cashback_game_details, $extra_info){

		if(!empty($cashback_game_details)){

			foreach ($sum_paid_cashback_amount_map as $player_id => $cashback_amount) {

				if(isset($playerMaxBonusMap[$player_id]['cashback_daily_maxbonus']) &&
						$playerMaxBonusMap[$player_id]['cashback_daily_maxbonus']!=0){
					//minus paid from maxbonux
					$playerMaxBonusMap[$player_id]['cashback_daily_maxbonus']=$playerMaxBonusMap[$player_id]['cashback_daily_maxbonus']-$cashback_amount;
					if($playerMaxBonusMap[$player_id]['cashback_daily_maxbonus']<=0){
						$playerMaxBonusMap[$player_id]['cashback_daily_maxbonus']=-1;
					}
				}
			}


			foreach ($cashback_game_details as &$cashback_game_detail) {

				$player_id=$cashback_game_detail['player_id'];
				$this->utils->debug_log('process_daily_max_bonus_limit ', $player_id, $playerMaxBonusMap[$player_id]['cashback_daily_maxbonus'], $cashback_game_detail);

				if($cashback_game_detail['cashback_amount']<=0){
					continue;
				}

				//0 means unlimited
				if(isset($playerMaxBonusMap[$player_id]['cashback_daily_maxbonus']) &&
						$playerMaxBonusMap[$player_id]['cashback_daily_maxbonus']!=0){

					//-1 means no more
					if($playerMaxBonusMap[$player_id]['cashback_daily_maxbonus']<0){

						$cashback_game_detail['cashback_amount']=0;
						$cashback_game_detail['withdraw_condition_amount']=0;
						$cashback_game_detail['bet_amount']=0;

						$this->utils->debug_log('process_daily_max_bonus_limit max bonus<0 cashback_game_details',$cashback_game_detail, $playerMaxBonusMap);

					//minus cashback from cashback_daily_maxbonus
					}else if($cashback_game_detail['cashback_amount'] < $playerMaxBonusMap[$player_id]['cashback_daily_maxbonus']){

						$playerMaxBonusMap[$player_id]['cashback_daily_maxbonus'] = $this->utils->roundCurrencyForShow(
							$playerMaxBonusMap[$player_id]['cashback_daily_maxbonus']-$cashback_game_detail['cashback_amount']);

						$this->utils->debug_log('process_daily_max_bonus_limit  cashback < max bonus cashback_game_details',$cashback_game_detail, $playerMaxBonusMap);

					}else{
						//cashback >= max bonus
						//no more

						$cashback_game_detail['cashback_amount']= $this->utils->roundCurrencyForShow($playerMaxBonusMap[$player_id]['cashback_daily_maxbonus']);

						$cashback_game_detail['withdraw_condition_amount']=$this->utils->roundCurrencyForShow(
							$cashback_game_detail['cashback_amount'] * $withdraw_condition_bet_times);
						$cashback_game_detail['bet_amount']=$this->utils->roundCurrencyForShow(
							$cashback_game_detail['cashback_amount'] / ($cashback_game_detail['rate'] / 100));

						//because 0 means unlimited, -1 means can't
						$playerMaxBonusMap[$player_id]['cashback_daily_maxbonus']=-1;

						$this->utils->debug_log('process_daily_max_bonus_limit  cashback >= max bonus cashback_game_details',$cashback_game_detail, $playerMaxBonusMap);
					}
				}else{
					$this->utils->debug_log('no daily maxbonus');
				}
			}

		}

        $this->utils->debug_log('after process_daily_max_bonus_limit cashback_game_details', $cashback_game_details, 'playerMaxBonusMap', $playerMaxBonusMap);

	}

	public function sumPlayerBetByTimeOnTempTable($cashback_bet_table, $timeStart, $timeEnd, $playerId = null) {
		$playerQry = '';
		if ($playerId) {
			$playerId = intval($playerId);
			$playerQry .= ' and player_id=' . $playerId;
		}

		$this->load->model(array('game_logs'));

		//disabled_cashback will disable player cashback
		// cashback_start_hour to cashback_end_hour
		$flag = Game_logs::FLAG_GAME;
		$sql = <<<EOD
SELECT tpgh.player_id, sum(tpgh.bet_amount) AS betting_total
FROM {$cashback_bet_table} AS tpgh
	JOIN game_description AS gd
		ON tpgh.game_description_id=gd.id AND gd.no_cash_back!=1
	JOIN player
		ON player.playerId=tpgh.player_id
WHERE tpgh.end_at>=?
	AND tpgh.end_at<=?
	AND flag={$flag}
	AND player.disabled_cashback!=1
	{$playerQry}
GROUP BY tpgh.player_id
EOD;

		$qry = $this->db->query($sql, array($timeStart, $timeEnd));

		$map = [];
		$rows = $this->getMultipleRow($qry);
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$player_id = $row->player_id;
				$betting_total = $row->betting_total;
				$map[$player_id] = $betting_total;
			}
		}

		return $map;
	}

	public function process_cashback_amount($player_id, $start, $end,
		$cashback_game_platform = null, $create_request=false, $notes=null, &$cashbackRequestData=null, $batch_type=false){

        $total_cashback_amount = 0;
        $total_available_bet_amount=0;
        $total_bet_amount=0;
        $cashback_game_details=[];

		$this->load->model(['group_level', 'total_cashback_player_game', 'withdraw_condition', 'cashback_request']);

        $dateTimeStart = new DateTime($start);
        $dateTimeEnd = new DateTime($end);

		$start=$this->utils->formatDateTimeForMysql($dateTimeStart);
		$end=$this->utils->formatDateTimeForMysql($dateTimeEnd);

		$cashBackSettings = $this->group_level->getCashbackSettings();
		$this->utils->debug_log('cashBackSettings', $cashBackSettings, 'start', $start, 'end', $end);
		$withdraw_condition_bet_times= isset($cashBackSettings->withdraw_condition) ? $cashBackSettings->withdraw_condition : 0 ;

		$wc_amount_map=$this->withdraw_condition->getAvailableWithdrawConditionWithBetByTime($start, $end);
        $cashback_bet_table=$this->generate_cashback_bet_table($dateTimeStart, $dateTimeEnd, $player_id, $cashback_game_platform);
        $isNoCashbackBonusForNonDepositPlayer=$this->isNoCashbackBonusForNonDepositPlayer();

        $commonRules=$this->getCommonCashbackRules();

        $extra_info=[
            'commonRules'=>$commonRules,
			'levelCashbackMap'=>$this->getFullCashbackPercentageMap(),
        ];

		$rate            = 0;
		$max_bonus       = 0;
		$max_daily_bonus = 0;
		$history_id      = null;
		$levelId         = null;

		$sum_paid_cashback_amount_map=[];

        $sum_paid_game_cashback_amount=$this->sum_paid_game_cashback_amount($start, $end, $player_id, $cashback_game_platform, $sum_paid_cashback_amount_map);

        $this->utils->debug_log('sum_paid_game_cashback_amount', $sum_paid_game_cashback_amount,'sum_paid_cashback_amount_map', $sum_paid_cashback_amount_map);

        $playerBetByDate=$this->generate_player_bet_amount($cashback_bet_table, $wc_amount_map, $player_id, $dateTimeStart, $dateTimeEnd);

        $playerMaxBonusMap=[];

        foreach ($playerBetByDate as $pbbd) {

            $game_platform_id = $pbbd->game_platform_id;
            $game_description_id = $pbbd->game_description_id;
            $game_type_id = $pbbd->game_type_id;

            if ($pbbd->betting_total > 0) {

                $rate = 0;
                $max_bonus = 0;
                $history_id = null;
                $player_id = $pbbd->player_id;
                $levelId= $pbbd->levelId;

                if($isNoCashbackBonusForNonDepositPlayer){
                    //should check deposit
                    $playerObj=$this->player_model->getPlayerArrayById($player_id);
                    if($playerObj['totalDepositAmount']<=0){
                        $this->utils->debug_log('ignore player '.$player_id.' for none deposit');
                        continue;
                    }
                }

                // get daily rate
                $playerDailyRate = $this->getPlayerRateFromLevel($player_id, $levelId,
                    $game_platform_id, $game_type_id, $game_description_id, $extra_info);

                if ($playerDailyRate) {
                    $rate = $playerDailyRate->cashback_percentage;
                    $max_bonus = $playerDailyRate->cashback_maxbonus;
                    $max_daily_bonus = $playerDailyRate->cashback_daily_maxbonus;
                    $history_id = $playerDailyRate->id;
                    $level_id = $playerDailyRate->level_id;
                }

            	if(!isset($playerMaxBonusMap[$player_id])){
            		//init value
					$playerMaxBonusMap[$player_id]=[
						'cashback_daily_maxbonus'=>$max_daily_bonus,
						'max_bonus'=>$max_bonus,
					];
            	}

                //only for exist cashback rate
                if ($rate > 0) {

                    $this->utils->debug_log('final rate', $rate, 'player_id', $player_id, 'game_description_id', $game_description_id,
                        'history_id', $history_id, 'level_id', $level_id, 'betting', $pbbd->betting_total,
						'betting*rate', $pbbd->betting_total * ($rate / 100), 'max_bonus', $max_bonus);

                    $cashback_amount = $this->utils->roundCurrencyForShow($pbbd->betting_total * ($rate / 100));
					$bet_amount=$this->utils->roundCurrencyForShow($pbbd->betting_total);
					$original_bet_amount=$bet_amount;

	                $deduct_amount=0;
					$deduct_bet_amount=0;

					//minus paid
					$sum_key=$player_id.'-'.$game_description_id;
					if(isset($sum_paid_game_cashback_amount[$sum_key])){

						$deduct_amount=$sum_paid_game_cashback_amount[$sum_key]['amount'];
						$deduct_bet_amount=$sum_paid_game_cashback_amount[$sum_key]['bet_amount'];

						$cashback_amount=$this->utils->roundCurrencyForShow($cashback_amount-$deduct_amount);
						$bet_amount=$this->utils->roundCurrencyForShow($bet_amount-$deduct_bet_amount);

						$this->utils->debug_log('minus paid amount', $sum_paid_game_cashback_amount[$sum_key], $sum_key,
							'bet_amount', $bet_amount, 'cashback_amount', $cashback_amount,
							'deduct_amount', $deduct_amount, 'deduct_bet_amount', $deduct_bet_amount);

						if($cashback_amount<=0){
							$bet_amount=0;
							$cashback_amount=0;
						}
						if($bet_amount<=0){
							$bet_amount=0;
							$cashback_amount=0;
						}
					}

					if($cashback_amount>0 && isset($playerMaxBonusMap[$player_id]['max_bonus']) && $playerMaxBonusMap[$player_id]['max_bonus']!=0){
						//process max bonus
						//first minus cashback amount from max bonus

						if($playerMaxBonusMap[$player_id]['max_bonus']<0){

							$cashback_amount=0;
							$withdraw_condition_amount=0;
							$bet_amount=0;

						}else if($cashback_amount < $playerMaxBonusMap[$player_id]['max_bonus']){
							$playerMaxBonusMap[$player_id]['max_bonus'] = $this->utils->roundCurrencyForShow(
								$playerMaxBonusMap[$player_id]['max_bonus']-$cashback_amount);
						}else{
							//cashback > max bonus

							$cashback_amount = $this->utils->roundCurrencyForShow(
								$cashback_amount - $playerMaxBonusMap[$player_id]['max_bonus']);

							//recalc withdraw_condition_amount
							$withdraw_condition_amount= $this->utils->roundCurrencyForShow(
								$cashback_amount * $withdraw_condition_bet_times);

		                    $bet_amount = $this->utils->roundCurrencyForShow(
		                    	$cashback_amount / ($rate / 100));

							//because 0 means unlimited, -1 means can't
							$playerMaxBonusMap[$player_id]['max_bonus']=-1;
						}
					}

                    $total_cashback_amount += $cashback_amount;
                    $total_available_bet_amount += $bet_amount;
					$total_bet_amount += $original_bet_amount;

					$this->utils->debug_log('total_cashback_amount', $total_cashback_amount, 'total_available_bet_amount',
						$total_available_bet_amount, 'total_bet_amount', $total_bet_amount, 'max_bonus', @$playerMaxBonusMap[$player_id]['max_bonus']);

					$game_platform_id = $pbbd->game_platform_id;
					$game_description_id = $pbbd->game_description_id;
					$game_type_id = $pbbd->game_type_id;

					$withdraw_condition_amount= $this->utils->roundCurrencyForShow($cashback_amount * $withdraw_condition_bet_times);

					$key=$player_id.'-'.$level_id.'-'.$game_platform_id.'-'.$game_type_id.'-'.$game_description_id;

					$cashback_game_details[$key]=[
						'player_id'=>$player_id,
						'level_id'=>$level_id,
						'history_id'=>$history_id,
						'rate'=>$rate,
						'max_bonus'=>$max_bonus,
						'max_daily_bonus'=>$max_daily_bonus,
						'bet_amount'=>$bet_amount,
						'cashback_amount'=>$cashback_amount,
						'withdraw_condition_amount'=>$withdraw_condition_amount,
						'game_platform_id'=>$game_platform_id,
						'game_type_id'=>$game_type_id,
						'game_description_id'=>$game_description_id,
						'original_bet_amount'=>$original_bet_amount,
						'deduct_amount'=>$deduct_amount,
						'deduct_bet_amount'=>$deduct_bet_amount,
						'player_cashback_rate'=>$playerDailyRate,
					];
                }
            }
        }

		if($this->utils->isEnabledFeature('enabled_realtime_cashback_table_mode')){

			$this->utils->debug_log('drop table '. $cashback_bet_table);
			$dropSql='drop table if exists '.$cashback_bet_table;

			$this->runRawUpdateInsertSQL($dropSql);

		}else{

			$this->utils->debug_log('drop view '. $cashback_bet_table);
			$dropSql='drop view if exists '.$cashback_bet_table;

			$this->runRawUpdateInsertSQL($dropSql);
		}

        $this->utils->debug_log('summary cashback_game_details',$cashback_game_details,
        	'sum_paid_cashback_amount_map', $sum_paid_cashback_amount_map, 'playerMaxBonusMap', $playerMaxBonusMap);

        $this->process_daily_max_bonus_limit($start, $end, $playerMaxBonusMap, $sum_paid_cashback_amount_map,
        	$withdraw_condition_bet_times, $cashback_game_details, $extra_info);

		if(!empty($cashback_game_details)) {

	        $total_cashback_amount = 0;
	        $total_available_bet_amount=0;
	        $total_bet_amount=0;

			foreach ($cashback_game_details as $cashback_game_detail) {

                $total_cashback_amount += $cashback_game_detail['cashback_amount'];
                $total_available_bet_amount += $cashback_game_detail['bet_amount'];
				$total_bet_amount += $cashback_game_detail['original_bet_amount'];

			}
		}

		if($create_request) {
			//create cashback request

			if(!empty($cashback_game_details)) {

				$sum_bet_amount=0;
				$sum_cashback_amount=0;
				$sum_withdraw_condition_amount=0;
				foreach ($cashback_game_details as $cashback_game_detail) {
					$sum_bet_amount+=$cashback_game_detail['bet_amount'];
					$sum_cashback_amount+=$cashback_game_detail['cashback_amount'];
					$sum_withdraw_condition_amount+=$cashback_game_detail['withdraw_condition_amount'];
				}

				$secureId = $this->getSecureId('cashback_request', 'secure_id', true, 'C');
				$cashback_request_type = Cashback_request::TYPE_REAL;
				if($batch_type){
					$cashback_request_type = Cashback_request::TYPE_PERIOD;
				}

				if($sum_cashback_amount<=0){

					$total_cashback_amount = $this->utils->roundCurrencyForShow(0);
					$total_available_bet_amount = $this->utils->roundCurrencyForShow(0);
					$total_bet_amount = $this->utils->roundCurrencyForShow($total_bet_amount);

					$this->utils->debug_log('ignore 0 cashback request');
					return [$total_cashback_amount, $total_available_bet_amount, $total_bet_amount];
				}

				$cashbackRequestData = array(
					'secure_id' => $secureId,
					'player_id' => $player_id,
					'cashback_request_type' => $cashback_request_type,
					'request_starttime' => $start,
					'request_datetime' => $end,
					'status' => Cashback_request::PENDING,
					'notes' => $notes,
					'created_at' => $this->utils->getNowForMysql(),
					'updated_at' => $this->utils->getNowForMysql(),
					'request_amount' => $sum_cashback_amount,
					'bet_amount' => $sum_bet_amount,
					'withdraw_condition_amount' => $sum_withdraw_condition_amount,
				);

				$cashbackRequestData['id'] = $this->insertData('cashback_request', $cashbackRequestData);

				$this->utils->debug_log('process_cashback_amount add cashbackRequestData', $cashbackRequestData);

				$cashback_request_id=$cashbackRequestData['id'];

				if(!empty($cashback_request_id)){

					foreach ($cashback_game_details as $cashback_game_detail) {

						$cashback_amount=$cashback_game_detail['cashback_amount'];

						if($cashback_amount<=0){
							$this->utils->debug_log('ignore zero cashback', $cashback_game_detail);
							continue;
						}

						$player_id=$cashback_game_detail['player_id'];
						$game_platform_id=$cashback_game_detail['game_platform_id'];
						$game_type_id=$cashback_game_detail['game_type_id'];
						$game_description_id=$cashback_game_detail['game_description_id'];
						$history_id=$cashback_game_detail['history_id'];
						$level_id=$cashback_game_detail['level_id'];
						$rate=$cashback_game_detail['rate'];
						$bet_amount=$cashback_game_detail['bet_amount'];
						$withdraw_condition_amount=$cashback_game_detail['withdraw_condition_amount'];
						$original_bet_amount=$cashback_game_detail['original_bet_amount'];
						$max_bonus=$cashback_game_detail['max_bonus'];

						$cashback_detail_id=$this->insertCashbackByTime($player_id, $cashback_request_id,
							$game_platform_id, $game_description_id, $start, $end,
							$cashback_amount, $history_id, $game_type_id, $level_id, $rate,
							$bet_amount, $withdraw_condition_amount, $max_bonus, $original_bet_amount);

						$this->utils->debug_log('cashback_detail_id', $cashback_detail_id, 'cashback_game_detail', $cashback_game_detail);
					}
				}else{
					$total_cashback_amount=0;
					$this->utils->error_log('create cashback request failed');
				}

			}else{
				$this->utils->debug_log('process_cashback_amount empty cashback_game_details');
			}
		}else{
			$this->utils->debug_log('process_cashback_amount ignore create request');
		}

		$total_cashback_amount = $this->utils->roundCurrencyForShow($total_cashback_amount);
		$total_available_bet_amount = $this->utils->roundCurrencyForShow($total_available_bet_amount);
		$total_bet_amount = $this->utils->roundCurrencyForShow($total_bet_amount);
        return [$total_cashback_amount, $total_available_bet_amount, $total_bet_amount];
    }

	/**
	 * generate cashback by time range
	 *
	 * @param string $timeStart
	 * @param string $timeEnd
	 * @param int $playerId
	 * @param int $cashback_request_id
	 * @return bool
	 */

	public function generate_cashback_by_time_range($timeStart, $timeEnd, $playerId, $cashback_request_id) {

		$msg = $this->utils->debug_log('=========start generate_cashback by time range============================');

		$this->load->model(array('group_level'));

		$cashBackSettings = $this->group_level->getCashbackSettings();
		$this->utils->debug_log('cashBackSettings', $cashBackSettings);

		$calcEnabled = false;

		if (!empty($timeStart) && !empty($timeEnd)){
			$calcEnabled = true;
		}

		$calcResult = 'ignore calc';

		if ($calcEnabled) {
			$withdraw_condition_bet_times= isset($cashBackSettings->withdraw_condition) ? $cashBackSettings->withdraw_condition : 0 ;
			$calcResult = $this->generate_cashback_by_time($timeStart, $timeEnd, $playerId, $cashback_request_id, $withdraw_condition_bet_times);
		}


		$payResult = 'ignore pay';

		$msg = $this->utils->debug_log('cashback is success', 'calcResult', $calcResult, 'payResult', $payResult);
		$msg = $this->utils->debug_log('=========end generate_cashback============================');

		return $calcResult;
	}


	public function generate_cashback_by_request($playerId, $cashback_request_id) {
		$this->load->model(array('Cashback_request'));
		list($timeStart, $timeEnd) = $this->Cashback_request->getCashbackTimeInterval($playerId, $cashback_request_id);
		$this->generate_cashback_by_time_range($timeStart, $timeEnd, $playerId, $cashback_request_id);
	}

	public function getCashbackBacktrackingTimeLength($player_id){
		// filter by cashback tracking time length
		$this->load->model(['operatorglobalsettings']);
		return intval($this->operatorglobalsettings->getSettingValueWithoutCache('realtime_cashback_time_limit'));
	}

	/**
	 * load percentage map
	 * @param  int $levelId = vipsettingcashbackruleId
	 * @return array  $gamePlatformList, $gameTypeList, $gameDescList
	 */
	public function getCashbackPercentageMap($levelId) {
		$this->db->from('group_level_cashback_game_platform')->where('vipsetting_cashbackrule_id', $levelId);
		$rows = $this->runMultipleRowArray();
		$gamePlatformList = $this->buildMap($rows, 'game_platform_id', 'percentage');

		$this->db->from('group_level_cashback_game_type')->where('vipsetting_cashbackrule_id', $levelId);
		$rows = $this->runMultipleRowArray();
		$gameTypeList = $this->buildMap($rows, 'game_type_id', 'percentage');

		$this->db->from('group_level_cashback_game_description')->where('vipsetting_cashbackrule_id', $levelId);
		$rows = $this->runMultipleRowArray();
		$gameDescList = $this->buildMap($rows, 'game_description_id', 'percentage');

		return array($gamePlatformList, $gameTypeList, $gameDescList);
	}

	public function backupCashbackPercentage($relatedId = null, $adminUserId=null, $postTree=null, $backupType='vip_level_cashback') {

		if($backupType == "vip_level_cashback") {
			$levelId = $relatedId;
			if (!is_null($levelId)) {
				return $this->backupOneGroupLevelCashbackPercentage($levelId, $adminUserId, $postTree);
			} else {
				//save all
				$this->db->select('vipsettingcashbackruleId')->from('vipsettingcashbackrule');
				$rows = $this->runMultipleRowArray();
				foreach ($rows as $row) {
					$this->backupOneGroupLevelCashbackPercentage($row['vipsettingcashbackruleId'], $adminUserId, $postTree);
				}
			}
		}
		else if($backupType == "common_cashback") {
			$ruleId = $relatedId;
			if (!is_null($ruleId)) {
				return $this->backupOneCommonCashbackPercentage($ruleId, $adminUserId, $postTree);
			} else {
				//save all
				$this->db->select('id')->from('common_cashback_rules');
				$rows = $this->runMultipleRowArray();
				foreach ($rows as $row) {
					$this->backupOneCommonCashbackPercentage($row['id'], $adminUserId, $postTree);
				}
			}
		}
	}

	public function backupOneGroupLevelCashbackPercentage($levelId, $adminUserId=null, $postTree=null) {
		list($gamePlatformList, $gameTypeList, $gameDescList) = $this->getCashbackPercentageMap($levelId);

		//load cashback row
		$this->db->from('vipsettingcashbackrule')->where('vipsettingcashbackruleId', $levelId);
		$cashbackLevel = $this->runOneRowArray();

		$data = [
			'vipsetting_cashbackrule_id' => $levelId,
			'percentage_history' => $this->utils->encodeJson([
				'cashback_level' => $cashbackLevel,
				'game_platform' => $gamePlatformList,
				'game_type' => $gameTypeList,
				'game_description' => $gameDescList,
			]),
			'admin_user_id'=>$adminUserId,
			'new_percentage'=>$postTree,
			'updated_at'=>$this->utils->getNowForMysql(),
		];

		return $this->insertData('group_level_cashback_percentage_history', $data);
	}

	public function backupOneCommonCashbackPercentage($ruleId, $adminUserId=null, $postTree=null) {
		list($gamePlatformList, $gameTypeList, $gameDescList) = $this->getCashbackGameRuleTree($ruleId);

		//load cashback row
		$this->db->from('common_cashback_rules')->where('id', $ruleId);
		$common_cashback_rule = $this->runOneRowArray();

		$data = [
			'common_cashback_rules_id' => $ruleId,
			'percentage_history' => $this->utils->encodeJson([
				'common_cashback_rule' => $common_cashback_rule,
				'game_platform' => $gamePlatformList,
				'game_type' => $gameTypeList,
				'game_description' => $gameDescList,
			]),
			'admin_user_id'=>$adminUserId,
			'new_percentage'=>$postTree,
			'updated_at'=>$this->utils->getNowForMysql(),
		];

		return $this->insertData('common_cashback_rules_percentage_history', $data);
	}

	public function generateCashbackDiffList($levelId, $gamePlatformList, $gameTypeList, &$diffList){
		$this->db->select('game_platform_id')->from('group_level_cashback_game_platform')
		    ->where('vipsetting_cashbackrule_id', $levelId);

		$rows=$this->runMultipleRowArray();
		$oldGamePlatformList=[];
		if(!empty($rows)){
			foreach ($rows as $row) {
				$oldGamePlatformList[]=$row['game_platform_id'];
			}
		}

		$newGamePlatformList=array_keys($gamePlatformList);

		$deletedGP=array_diff($oldGamePlatformList, $newGamePlatformList);

		$diffList['deleted_game_platform']=array_values($deletedGP);

		$this->db->select('game_type_id')->from('group_level_cashback_game_type')
		    ->where('vipsetting_cashbackrule_id', $levelId);

		$rows=$this->runMultipleRowArray();
		$oldGameTypeList=[];
		if(!empty($rows)){
			foreach ($rows as $row) {
				$oldGameTypeList[]=$row['game_type_id'];
			}
		}

		$newGameTypeList=array_keys($gameTypeList);

		$deletedGT=array_diff($oldGameTypeList, $newGameTypeList);

		$diffList['deleted_game_type']=array_values($deletedGT);
	}

	public function generateCommonCashbackDiffList($ruleId, $gamePlatformList, $gameTypeList, $gameDescList, &$diffList){
		$this->db->select('game_description_id')->from('common_cashback_game_rules')
		    ->where('rule_id', $ruleId);

		$rows=$this->runMultipleRowArray();
		$oldGameDescriptionList=[];
		$oldGamePlatformList=[];
		$oldGameTypeList = [];

		if(!empty($rows)){
			foreach ($rows as $row) {
				$oldGameDescriptionList[]=$row['game_description_id'];

				$this->db->select('game_platform_id')->from('game_description')
					->where('id', $row['game_description_id']);
				$inside_row = $this->runOneRowArray();
				$oldGamePlatformList[]=$inside_row['game_platform_id'];

				$this->db->select('game_type_id')->from('game_description')
					->where('id', $row['game_description_id']);
				$inside_row_2 = $this->runOneRowArray();
				$oldGameTypeList[]=$inside_row_2['game_type_id'];
			}
		}
		$oldGamePlatformList = array_values(array_unique($oldGamePlatformList));
		$oldGameTypeList = array_values(array_unique($oldGameTypeList));

		$newGamePlatformList=array_keys($gamePlatformList);
		$deletedGP=array_diff($oldGamePlatformList, $newGamePlatformList);
		$diffList['deleted_game_platform']=array_values($deletedGP);

		$newGameTypeList=array_keys($gameTypeList);
		$deletedGT=array_diff($oldGameTypeList, $newGameTypeList);
		$diffList['deleted_game_type']=array_values($deletedGT);

		$newGameDescList=array_keys($gameDescList);
		$deletedGD=array_diff($oldGameDescriptionList, $newGameDescList);
		$diffList['deleted_game_description']=array_values($deletedGD);
	}
}

