<?php

/**
 * General behaviors include
 * * generate report
 *
 * @category report_module_generate
 * @version 3.06.02
 * @copyright 2013-2018 TripleOneTech
 */

trait report_module_generate {


    protected function getPlayerInfo($player_id){

        $this->db->select("p.playerId as playerId, p.levelId, p.levelName, p.groupName, p.agent_id, p.affiliateId,
            p.username as player_username, CONCAT( pd.firstName, ' ', pd.lastName ) as player_realName,
            p.email, pd.contactNumber, pd.gender, agents.agent_name as agent_username, p.createdOn as registered_date,
            aff.username as affiliate_username, p.registered_by, pd.registrationIP, pr.lastLoginIp as last_login_ip,
            pr.lastLoginTime as last_login_date, pr.lastLogoutTime as last_logout_date", FALSE)
        ->from('player p')
        ->join('playerdetails pd', 'pd.playerId = p.playerId', 'left')
        ->join('player_runtime pr', 'pr.playerId = p.playerId', 'left')
        ->join('affiliates aff', 'aff.affiliateId = p.affiliateId', 'left')
        ->join('agency_agents agents', 'agents.agent_id = p.agent_id', 'left')
        ->where('p.deleted_at IS NULL')
        ->where('p.playerId', $player_id)
        ->limit(1);

        return $this->runOneRowArray();

    }

    public function generate_player_report_hourly($from, $to, $player_id = null){
        if(empty($from) || empty($to)){
            return false;
        }

        $success=true;
        $currencyKey=$this->utils->getActiveCurrencyKey();

        $fromHour=$this->utils->formatDateHourForMysql(new DateTime($from));
        $toHour=$this->utils->formatDateHourForMysql(new DateTime($to));

        $hourList=$this->utils->makeDateHourList($from, $to);

        // $this->db->where('date_hour >= "'. $fromHour. '"')
        //     ->where('date_hour <= "' .$toHour. '"')
        //     ->delete('player_report_hourly');
//        $this->utils->debug_log('the query ---------->', $this->db->last_query());

        //hour to hour
        $this->load->model(['player_model', 'transactions']);

//      $this->db->select('playerId, levelId, levelName, groupName, agent_id, affiliateId')->from('player');
        // $this->db->select("p.playerId as playerId, p.levelId, p.levelName, p.groupName, p.agent_id, p.affiliateId,
        //     p.username as player_username, CONCAT( pd.firstName, ' ', pd.lastName ) as player_realName,
        //     p.email, pd.contactNumber, pd.gender, agents.agent_name as agent_username, p.createdOn as registered_date,
        //     aff.username as affiliate_username, p.registered_by, pd.registrationIP, pr.lastLoginIp as last_login_ip,
        //     pr.lastLoginTime as last_login_date, pr.lastLogoutTime as last_logout_date", FALSE)
        //     ->from('player p')
        //     ->join('playerdetails pd', 'pd.playerId = p.playerId', 'left')
        //     ->join('player_runtime pr', 'pr.playerId = p.playerId', 'left')
        //     ->join('affiliates aff', 'aff.affiliateId = p.affiliateId', 'left')
        //     ->join('agency_agents agents', 'agents.agent_id = p.agent_id', 'left')
        //     ->where('p.deleted_at IS NULL');
        //     if(!empty($player_id)){
        //         $this->db->where('p.playerId', $player_id);
        //     }

        $this->db->select("playerId")
            ->from('player')
            ->where('deleted_at IS NULL');

        if(!empty($player_id)){
            $this->db->where('p.playerId', $player_id);
        }
        $rows=$this->runMultipleRowArray();
        $playerMap=null;
        if(!empty($rows)){
            foreach ($rows as $row) {
               $playerMap[$row['playerId']] = []; 
           }
        }
        unset($rows);

        $type_arr=[Transactions::DEPOSIT, Transactions::WITHDRAWAL, Transactions::ADD_BONUS, Transactions::MEMBER_GROUP_DEPOSIT_BONUS,
            Transactions::PLAYER_REFER_BONUS, Transactions::RANDOM_BONUS, Transactions::SUBTRACT_BONUS, Transactions::AUTO_ADD_CASHBACK_TO_BALANCE,
            Transactions::MANUAL_ADD_BALANCE, Transactions::MANUAL_SUBTRACT_BALANCE];

        $type_query="and transactions.transaction_type in (".implode(',', $type_arr).")";
        if(!empty($player_id)){
            $type_query .= "and from_id = ".$player_id." OR to_id = ". $player_id;
        }
        $amount_sql="sum(if(transaction_type=".Transactions::DEPOSIT.",transactions.amount,0)) deposit_amount, ".
            "sum(if(transaction_type=".Transactions::WITHDRAWAL.",transactions.amount,0)) withdrawal_amount, ".
            "sum( case transaction_type when ".Transactions::ADD_BONUS." then transactions.amount when ".Transactions::MEMBER_GROUP_DEPOSIT_BONUS." then transactions.amount when ".Transactions::PLAYER_REFER_BONUS." then transactions.amount when ".Transactions::RANDOM_BONUS." then transactions.amount when ".Transactions::SUBTRACT_BONUS." then -transactions.amount else 0 end ) bonus_amount, ".
            "sum(if(transaction_type=".Transactions::AUTO_ADD_CASHBACK_TO_BALANCE.",transactions.amount,0)) cashback_amount, ".
            "SUM(CASE WHEN transaction_type = ". Transactions::MEMBER_GROUP_DEPOSIT_BONUS ." THEN amount ELSE 0 END) deposit_bonus, ".
            "SUM(CASE WHEN transaction_type = ". Transactions::PLAYER_REFER_BONUS ." THEN amount ELSE 0 END) referral_bonus, ".
            "SUM(CASE WHEN transaction_type = ". Transactions::ADD_BONUS ." THEN amount ELSE 0 END) manual_bonus, ".
            "COUNT(CASE WHEN transaction_type = ". Transactions::DEPOSIT ." THEN 1 END) total_deposit_times, ".
            "SUM(CASE WHEN transaction_type = ". Transactions::SUBTRACT_BONUS ." THEN amount ELSE 0 END) subtract_bonus, ".
            "sum( case transaction_type when ".Transactions::MANUAL_ADD_BALANCE." then transactions.amount when ".Transactions::MANUAL_SUBTRACT_BALANCE." then -transactions.amount else 0 end ) manual_amount ";

        $player_sql="( if(transactions.to_type=".Transactions::PLAYER.",transactions.to_id,transactions.from_id) )";

        $fromDateTime=$this->utils->convertHourFormatToDateTime($fromHour, true);
        $toDateTime=$this->utils->convertHourFormatToDateTime($toHour, false);

        $sql=<<<EOD
select $player_sql player_id, DATE_FORMAT(transactions.created_at, '%Y%m%d%H') date_hour,
$amount_sql
from transactions
where transactions.status = ?
AND ( transactions.to_type = ? or transactions.from_type = ?)
AND transactions.created_at>=? and transactions.created_at<=?
$type_query
group by transactions.to_id, DATE_FORMAT(transactions.created_at, '%Y%m%d%H')
EOD;

        $transRows=$this->runRawSelectSQLArray($sql, [Transactions::APPROVED, Transactions::PLAYER, Transactions::PLAYER,
            $fromDateTime, $toDateTime]);
        $transMap=null;
        if(!empty($transRows)){
            foreach ($transRows as $row) {
                $transMap[$row['date_hour']][$row['player_id']]=$row;
            }
        }
        unset($transRows);

        //get game data
        // $gameRows = $this->getGameDataList($fromHour, $toHour, $player_id);
        $this->db->select('player_id, date_hour, sum(betting_amount) betting_amount, game_platform_id')
            ->select('SUM(result_amount)/SUM(betting_amount) as payout_rate')
            ->select_sum('result_amount', 'payout')
            ->select_sum('win_amount', 'win_amount')
            ->select_sum('loss_amount', 'loss_amount')
            ->select_sum('result_amount', 'result_amount')
            ->from('total_player_game_hour')
            ->where('(date_hour >= '.$fromHour.' AND date_hour <= '.$toHour.')');
            if(!empty($player_id)){
                $this->db->where('player_id', $player_id);
            }
            $this->db->group_by(['player_id', 'date_hour', 'game_platform_id']);

        $gameRows= $this->runMultipleRowArray();

        $gameMap=null;
        if(!empty($gameRows)){
            foreach ($gameRows as $row) {
                // if(isset($gameMap[$row['date_hour']][$row['player_id']])){
                //     //exists
                //     $gameMap[$row['date_hour']][$row['player_id']][$row['game_platform_id']]=$row;
                // }else{
                //     //new
                //     $gameMap[$row['date_hour']][$row['player_id']]=[$row['game_platform_id']=>$row];
                // }
                $gameMap[$row['date_hour']][$row['player_id'].'-'.$row['game_platform_id']]=$row;
            }
        }

        //get game logs more bets and result
        // $gameLogsRows = $this->getGameLogsTotal($fromHour, $toHour, $player_id);
        $this->db->select('player_id, DATE_FORMAT(end_at, "%Y%m%d%H") date_hour, game_platform_id', FALSE)
            ->select_sum('(CASE WHEN result_amount > 0 THEN bet_amount ELSE 0 END)', 'winning_bets')
            ->select_sum('(CASE WHEN result_amount < 0 THEN bet_amount ELSE 0 END)', 'lost_bets')
            ->select_sum('(CASE WHEN result_amount = 0 THEN bet_amount ELSE 0 END)', 'tie_bets')
            ->select_sum('(CASE WHEN (ISNULL(odds) OR odds = 0 OR odds > 2) THEN bet_amount ELSE bet_amount * (odds-1) END)', 'total_odds_bets')
            ->select_sum('(CASE WHEN (ISNULL(odds) OR odds = 0 OR odds > 2) THEN trans_amount ELSE trans_amount * (odds-1) END)', 'total_odds_real_bets')
            ->select_sum('(CASE WHEN match_type = 1 THEN bet_amount ELSE 0 END)', 'total_live_bets')
            ->select_sum('(CASE WHEN match_type = 1 THEN trans_amount ELSE 0 END)', 'total_live_real_bets')
            ->select_sum('(CASE WHEN match_type = 1 AND result_amount > 0 THEN bet_amount ELSE 0 END)', 'live_winning_bets')
            ->select_sum('(CASE WHEN match_type = 1 AND result_amount < 0 THEN bet_amount ELSE 0 END)', 'live_lost_bets')
            ->select_sum('(CASE WHEN match_type = 1 AND result_amount = 0 THEN bet_amount ELSE 0 END)', 'live_tie_bets')
            ->select_sum('(CASE WHEN match_type = 1 AND result_amount >= 0 THEN result_amount ELSE 0 END)', 'live_gain_sum')
            ->from('game_logs')
            ->where('flag', 1);
            if(!empty($player_id)){
                $this->db->where('player_id', $player_id);
            }
            $fromDateTime=$this->utils->convertHourFormatToDateTime($fromHour, true);
            $toDateTime=$this->utils->convertHourFormatToDateTime($toHour, false);
            //date time
            $this->db->where('end_at >=', $fromDateTime)->where('end_at <=', $toDateTime);

            // $this->db->where('(DATE_FORMAT(end_at, "%Y%m%d%H") >= "'.$fromHour.'" and DATE_FORMAT(end_at, "%Y%m%d%H") <= "'.$toHour.'")')
            $this->db->group_by(['player_id', 'DATE_FORMAT(end_at, "%Y%m%d%H")', 'game_platform_id']);

        $gameLogsRows= $this->runMultipleRowArray();
        $gameLogsMap=null;
        if(!empty($gameLogsRows)){
            foreach ($gameLogsRows as $row) {
                // if(isset($gameLogsMap[$row['date_hour']][$row['player_id']])){
                //     $gameLogsMap[$row['date_hour']][$row['player_id']][$row['game_platform_id']]=$row;
                // }else{
                //     //new
                //     $gameLogsMap[$row['date_hour']][$row['player_id']]=[$row['game_platform_id']=>$row];
                // }
                $gameLogsMap[$row['date_hour']][$row['player_id'].'-'.$row['game_platform_id']]=$row;
            }
        }
        unset($gameLogsRows);

        $this->utils->debug_log('before process '.$from.' to '.$to, $hourList); //, $transMap, $gameMap);

        foreach ($hourList as $date_hour) {

            $gameList=$transList=$gameLogList=null;
            if(!empty($transMap)){
                $transList=isset($transMap[$date_hour]) ? $transMap[$date_hour] : null;
            }
            if(!empty($gameMap)){
                $gameList=isset($gameMap[$date_hour]) ? $gameMap[$date_hour] : null;
            }
            if(!empty($gameLogsMap)){
                $gameLogList=isset($gameLogsMap[$date_hour]) ? $gameLogsMap[$date_hour] : null;
            }
            $playerDataMap=null;
            // $playerAdditionalDataMap=null;
            if(!empty($transList)){

                foreach ($transList as $playerId=>$transInfo) {
                    $playerInfo = $this->getPlayerInfo($playerId);
                    if(!empty($playerInfo)){
                       if(isset($playerMap[$playerId]) && empty($playerMap[$playerId])){
                         $playerMap[$playerId] = $playerInfo ;
                        }
                    }
                    //$playerInfo=isset($playerMap[$playerId]) ? $playerMap[$playerId] : null;
                    if(empty($playerInfo)){
                        //lost player
                        $this->utils->error_log('lost player on generate_player_report_hourly --------------> '.$playerId);
                        continue;
                    }
                    $gamePlatformId=0;
                    $playerGameKey=$playerId.'-'.$gamePlatformId;
                    //always new, because it's first
                    $playerDataMap[$playerGameKey] = [
                        'player_id'         =>  $playerId,
                        'affiliate_id'      =>  $playerInfo['affiliateId'],
                        'agent_id'          =>  $playerInfo['agent_id'],
                        'level_id'          =>  $playerInfo['levelId'],
                        'level_name'        =>  $playerInfo['levelName'],
                        'group_name'        =>  $playerInfo['groupName'],
                        'total_deposit'     =>  $this->utils->roundCurrencyForShow($transInfo['deposit_amount']),
                        'total_withdrawal'  =>  $this->utils->roundCurrencyForShow($transInfo['withdrawal_amount']),
                        'total_bonus'       =>  $this->utils->roundCurrencyForShow($transInfo['bonus_amount']),
                        'total_cashback'    =>  $this->utils->roundCurrencyForShow($transInfo['cashback_amount']),
                        'total_manual'      =>  $this->utils->roundCurrencyForShow($transInfo['manual_amount']),
                        'total_gross'       =>  $this->utils->roundCurrencyForShow($transInfo['deposit_amount']-$transInfo['withdrawal_amount']),
                        'date_hour'         =>  $date_hour,
                        'deposit_times'     =>  $transInfo['total_deposit_times'],
                        'referral_bonus'    =>  $transInfo['referral_bonus'],
                        'subtract_bonus'    =>  $transInfo['subtract_bonus'],
                        'manual_bonus'      =>  $transInfo['manual_bonus'],
                        'game_platform_id'  =>  $gamePlatformId,
                    ];

                    $this->db->select("amount, created_at")->from('transactions')
                        ->where('to_id', $playerId)
                        ->where('to_type', Transactions::PLAYER)
                        ->where('transaction_type', Transactions::DEPOSIT)
                        ->where('status', Transactions::APPROVED)
                        ->order_by('transactions.created_at', 'ASC')
                        ->limit(2);
                    $deposits_res = $this->runMultipleRowArray();

                    if(count($deposits_res) > 0 ){
                        $playerDataMap[$playerGameKey]['first_deposit_amount'] = $deposits_res[0]['amount'];
                        $playerDataMap[$playerGameKey]['first_deposit_datetime'] = $deposits_res[0]['created_at'];
                    }else if(count($deposits_res) > 1){
                        $playerDataMap[$playerGameKey]['second_deposit_amount'] = $deposits_res[1]['amount'];
                    }
                }

            }

            if(!empty($gameList)){

                foreach ($gameList as $playerGameKey=>$gameInfo) {
                    list($playerId, $gamePlatformId)=explode('-', $playerGameKey);

                    $playerInfo = $this->getPlayerInfo($playerId);
                    if(!empty($playerInfo)){
                       if(isset($playerMap[$playerId]) && empty($playerMap[$playerId])){
                         $playerMap[$playerId] = $playerInfo ;
                        }
                    }
                    //$playerInfo=isset($playerMap[$playerId]) ? $playerMap[$playerId] : null;
                    if(empty($playerInfo)){
                        //lost player
                        $this->utils->error_log('lost player on generate_player_report_hourly --------------> '.$playerId);
                        continue;
                    }
                    //$gameInfo=[game platform id => gamePlatformData]
                    $gamePlatformId=$gameInfo['game_platform_id'];
                    $playerZeroKey=$playerId.'-0';
                    //process 0 id first
                    if(isset($playerDataMap[$playerZeroKey])){
                        //replace 0 id to game platform id
                        $tmpData=$playerDataMap[$playerZeroKey];
                        unset($playerDataMap[$playerZeroKey]);
                        $playerDataMap[$playerGameKey]=$tmpData;
                    }

                    if(isset($playerDataMap[$playerGameKey])){
                        //exists transaction
                        $playerDataMap[$playerGameKey]['total_bet']=$this->utils->roundCurrencyForShow($gameInfo['betting_amount']);
                        $playerDataMap[$playerGameKey]['total_win']=$this->utils->roundCurrencyForShow($gameInfo['win_amount']);
                        $playerDataMap[$playerGameKey]['total_loss']=$this->utils->roundCurrencyForShow($gameInfo['loss_amount']);
                        $playerDataMap[$playerGameKey]['total_result']=$this->utils->roundCurrencyForShow($gameInfo['result_amount']);
                        $playerDataMap[$playerGameKey]['payout']         = $this->utils->roundCurrencyForShow($gameInfo['payout']);
                        $playerDataMap[$playerGameKey]['payout_rate']    = !empty($gameInfo['payout_rate']) ? $gameInfo['payout_rate'] : 0;
                        $playerDataMap[$playerGameKey]['game_platform_id']    = $gameInfo['game_platform_id'];

                    }else{
                        //only exist game in this hour
                        $playerDataMap[$playerGameKey]=[
                            'player_id'=>$playerId,
                            'total_deposit'=>0,
                            'total_withdrawal'=>0,
                            'total_bonus'=>0,
                            'total_cashback'=>0,
                            'total_manual'=>0,
                            'total_gross'=>0,
                            'total_bet'=>$this->utils->roundCurrencyForShow($gameInfo['betting_amount']),
                            'total_win'=>$this->utils->roundCurrencyForShow($gameInfo['win_amount']),
                            'total_loss'=>$this->utils->roundCurrencyForShow($gameInfo['loss_amount']),
                            'total_result'=>$this->utils->roundCurrencyForShow($gameInfo['result_amount']),
                            'payout' => $this->utils->roundCurrencyForShow($gameInfo['payout']),
                            'payout_rate' => !empty($gameInfo['payout_rate']) ? $gameInfo['payout_rate'] : 0,
                            'date_hour'=>$date_hour,
                            'game_platform_id'=> $gamePlatformId,
                        ];
                    }
                    if(isset($gameLogList[$playerGameKey])){
                        $gameLogsInfo=$gameLogList[$playerGameKey];
                        $playerDataMap[$playerGameKey]['winning_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['winning_bets']);
                        $playerDataMap[$playerGameKey]['lost_bets']= $this->utils->roundCurrencyForShow($gameLogsInfo['lost_bets']);
                        $playerDataMap[$playerGameKey]['tie_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['tie_bets']);
                        $playerDataMap[$playerGameKey]['total_odds_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['total_odds_bets']);
                        $playerDataMap[$playerGameKey]['total_odds_real_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['total_odds_real_bets']);
                        $playerDataMap[$playerGameKey]['total_live_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['total_live_bets']);
                        $playerDataMap[$playerGameKey]['total_live_real_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['total_live_real_bets']);
                        $playerDataMap[$playerGameKey]['live_winning_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['live_winning_bets']);
                        $playerDataMap[$playerGameKey]['live_lost_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['live_lost_bets']);
                        $playerDataMap[$playerGameKey]['live_tie_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['live_tie_bets']);
                        $playerDataMap[$playerGameKey]['live_gain_sum'] = $this->utils->roundCurrencyForShow($gameLogsInfo['live_gain_sum']);
                    }

                    $this->db->select("amount, created_at")->from('transactions')
                        ->where('to_id', $playerId)
                        ->where('to_type', Transactions::PLAYER)
                        ->where('transaction_type', Transactions::DEPOSIT)
                        ->where('status', Transactions::APPROVED)
                        ->order_by('transactions.created_at', 'ASC')
                        ->limit(2);
                    $deposits_res = $this->runMultipleRowArray();

                    if(count($deposits_res) > 0 ){
                        $playerDataMap[$playerGameKey]['first_deposit_amount'] = $deposits_res[0]['amount'];
                        $playerDataMap[$playerGameKey]['first_deposit_datetime'] = $deposits_res[0]['created_at'];
                    }else if(count($deposits_res) > 1){
                        $playerDataMap[$playerGameKey]['second_deposit_amount'] = $deposits_res[1]['amount'];
                    }
                }
            }

            $this->startTrans();

            $this->db->where('date_hour >= "'. $date_hour. '"')
                ->where('date_hour <= "' .$date_hour. '"')
                ->delete('player_report_hourly');

            $this->utils->debug_log('process '.$date_hour, 'delete first', $this->db->affected_rows());

            if(!empty($playerDataMap)) {

                $this->utils->debug_log('date_hour: ' . $date_hour . ',playerDataMap: ' . count($playerDataMap));

                foreach ($playerDataMap as $playerGameKey => $reportData) {
                    list($playerId, $gamePlatformId)=explode('-', $playerGameKey);
                    $playerInfo = $this->getPlayerInfo($playerId);
                    if(!empty($playerInfo)){
                       if(isset($playerMap[$playerId]) && empty($playerMap[$playerId])){
                         $playerMap[$playerId] = $playerInfo ;
                        }
                    }
                    //$playerInfo = isset($playerMap[$playerId]) ? $playerMap[$playerId] : null;
                    if (empty($playerInfo)) {
                        //lost player
                        $this->utils->error_log('lost player on generate_player_report_hourly --------------> ' . $playerId);
                        continue;
                    }
                    // $gamePlatformId=$reportData['game_platform_id'];
                    //build unique key
                    $unique_key =$currencyKey.'-'.$playerId.'-'.$date_hour.'-'.$gamePlatformId;

                    $reportData['affiliate_id'] = $playerInfo['affiliateId'];
                    $reportData['agent_id'] = $playerInfo['agent_id'];
                    $reportData['level_id'] = $playerInfo['levelId'];
                    $reportData['level_name'] = $playerInfo['levelName'];
                    $reportData['group_name'] = $playerInfo['groupName'];
                    $reportData['player_username'] = $playerInfo['player_username'];
                    $reportData['player_realName'] = $playerInfo['player_realName'];
                    $reportData['email'] = $playerInfo['email'];
                    $reportData['contactNumber'] = $playerInfo['contactNumber'];
                    $reportData['gender'] = $playerInfo['gender'];
                    $reportData['agent_username'] = $playerInfo['agent_username'];
                    $reportData['affiliate_username'] = $playerInfo['affiliate_username'];
                    $reportData['registered_by'] = $playerInfo['registered_by'];
                    $reportData['registered_date'] = $playerInfo['registered_date'];
                    $reportData['registrationIP'] = $playerInfo['registrationIP'];
                    $reportData['last_login_ip'] = $playerInfo['last_login_ip'];
                    $reportData['last_login_date'] = $playerInfo['last_login_date'];
                    $reportData['last_logout_date'] = $playerInfo['last_logout_date'];

                    $reportData['unique_key'] = $unique_key;
                    $reportData['currency_key']=$currencyKey;

                    //update or insert
                    $this->db->select('id')->from('player_report_hourly')->where('unique_key', $unique_key);
                    $id = $this->runOneRowOneField('id');
                    if (empty($id)) {
                        //insert
                        $reportData['created_at'] = $this->utils->getNowForMysql();
                        $reportData['updated_at'] = $this->utils->getNowForMysql();
                        $success = $this->insertData('player_report_hourly', $reportData);
                        // $this->utils->debug_log('insert date_hour', $date_hour, $reportData);
                    } else {
                        //update
                        $reportData['updated_at'] = $this->utils->getNowForMysql();
                        $this->db->set($reportData)->where('id', $id);
                        $success = $this->runAnyUpdate('player_report_hourly');
                        // $this->utils->debug_log('update date_hour', $date_hour, $reportData);
                    }
                    if (!$success) {
                        $this->utils->error_log('insert/update player_report_hourly failed', $reportData, $id);
                        break;
                    }
                }

                if (!$success) {
                    break;
                }
            }

            unset($transList);
            unset($gameList);
            unset($gameLogList);
            unset($playerDataMap);

            unset($transMap[$date_hour]);
            unset($gameMap[$date_hour]);
            unset($gameLogsMap[$date_hour]);

            $success=$this->endTransWithSucc();
            if(!$success){
                $this->utils->error_log('generate player report failed', $date_hour);
                break;
            }

        }

        unset($transMap);
        unset($gameMap);
        unset($gameLogsMap);
        unset($playerMap);

        return $success;

    }

    /**
     * old function
     */
    public function generate_player_report_hourly_old($from, $to, $player_id = null){
        if(empty($from) || empty($to)){
            return false;
        }

        $success=true;
        $currencyKey=$this->utils->getActiveCurrencyKey();

        $fromHour=$this->utils->formatDateHourForMysql(new DateTime($from));
        $toHour=$this->utils->formatDateHourForMysql(new DateTime($to));

        $hourList=$this->utils->makeDateHourList($from, $to);

        $this->db->where('date_hour >= "'. $fromHour. '"')
            ->where('date_hour <= "' .$toHour. '"')
            ->delete('player_report_hourly');
//        $this->utils->debug_log('the query ---------->', $this->db->last_query());

        //hour to hour
        $this->load->model(['player_model', 'transactions']);

//      $this->db->select('playerId, levelId, levelName, groupName, agent_id, affiliateId')->from('player');
        $this->db->select("p.playerId as playerId, p.levelId, p.levelName, p.groupName, p.agent_id, p.affiliateId,
            p.username as player_username, CONCAT( pd.firstName, ' ', pd.lastName ) as player_realName,
            p.email, pd.contactNumber, pd.gender, agents.agent_name as agent_username, p.createdOn as registered_date,
            aff.username as affiliate_username, p.registered_by, pd.registrationIP, pr.lastLoginIp as last_login_ip,
            pr.lastLoginTime as last_login_date, pr.lastLogoutTime as last_logout_date", FALSE)
            ->from('player p')
            ->join('playerdetails pd', 'pd.playerId = p.playerId', 'left')
            ->join('player_runtime pr', 'pr.playerId = p.playerId', 'left')
            ->join('affiliates aff', 'aff.affiliateId = p.affiliateId', 'left')
            ->join('agency_agents agents', 'agents.agent_id = p.agent_id', 'left')
            ->where('p.deleted_at IS NULL');
            if(!empty($player_id)){
                $this->db->where('p.playerId', $player_id);
            }

        $rows=$this->runMultipleRowArray();
        $playerMap=null;
        if(!empty($rows)){
            foreach ($rows as $row) {
                $playerMap[$row['playerId']]=$row;
            }
        }
        unset($rows);

        $type_arr=[Transactions::DEPOSIT, Transactions::WITHDRAWAL, Transactions::ADD_BONUS, Transactions::MEMBER_GROUP_DEPOSIT_BONUS,
            Transactions::PLAYER_REFER_BONUS, Transactions::RANDOM_BONUS, Transactions::SUBTRACT_BONUS, Transactions::AUTO_ADD_CASHBACK_TO_BALANCE,
            Transactions::MANUAL_ADD_BALANCE, Transactions::MANUAL_SUBTRACT_BALANCE];

        $type_query="and transactions.transaction_type in (".implode(',', $type_arr).")";
        if(!empty($player_id)){
            $type_query .= "and from_id = ".$player_id." OR to_id = ". $player_id;
        }
        $amount_sql="sum(if(transaction_type=".Transactions::DEPOSIT.",transactions.amount,0)) deposit_amount, ".
            "sum(if(transaction_type=".Transactions::WITHDRAWAL.",transactions.amount,0)) withdrawal_amount, ".
            "sum( case transaction_type when ".Transactions::ADD_BONUS." then transactions.amount when ".Transactions::MEMBER_GROUP_DEPOSIT_BONUS." then transactions.amount when ".Transactions::PLAYER_REFER_BONUS." then transactions.amount when ".Transactions::RANDOM_BONUS." then transactions.amount when ".Transactions::SUBTRACT_BONUS." then -transactions.amount else 0 end ) bonus_amount, ".
            "sum(if(transaction_type=".Transactions::AUTO_ADD_CASHBACK_TO_BALANCE.",transactions.amount,0)) cashback_amount, ".
            "SUM(CASE WHEN transaction_type = ". Transactions::MEMBER_GROUP_DEPOSIT_BONUS ." THEN amount ELSE 0 END) deposit_bonus, ".
            "SUM(CASE WHEN transaction_type = ". Transactions::PLAYER_REFER_BONUS ." THEN amount ELSE 0 END) referral_bonus, ".
            "SUM(CASE WHEN transaction_type = ". Transactions::ADD_BONUS ." THEN amount ELSE 0 END) manual_bonus, ".
            "COUNT(CASE WHEN transaction_type = ". Transactions::DEPOSIT ." THEN 1 END) total_deposit_times, ".
            "SUM(CASE WHEN transaction_type = ". Transactions::SUBTRACT_BONUS ." THEN amount ELSE 0 END) subtract_bonus, ".
            "sum( case transaction_type when ".Transactions::MANUAL_ADD_BALANCE." then transactions.amount when ".Transactions::MANUAL_SUBTRACT_BALANCE." then -transactions.amount else 0 end ) manual_amount ";

        $player_sql="( if(transactions.to_type=".Transactions::PLAYER.",transactions.to_id,transactions.from_id) )";

        $fromDateTime=$this->utils->convertHourFormatToDateTime($fromHour, true);
        $toDateTime=$this->utils->convertHourFormatToDateTime($toHour, false);

		$sql=<<<EOD
select $player_sql player_id, DATE_FORMAT(transactions.created_at, '%Y%m%d%H') date_hour,
$amount_sql
from transactions
where transactions.status = ?
AND ( transactions.to_type = ? or transactions.from_type = ?)
AND transactions.created_at>=? and transactions.created_at<=?
$type_query
group by transactions.to_id, DATE_FORMAT(transactions.created_at, '%Y%m%d%H')
EOD;

		$transRows=$this->runRawSelectSQLArray($sql, [Transactions::APPROVED, Transactions::PLAYER, Transactions::PLAYER,
            $fromDateTime, $toDateTime]);
		$transMap=null;
		if(!empty($transRows)){
			foreach ($transRows as $row) {
				$transMap[$row['date_hour']][$row['player_id']]=$row;
			}
		}
        unset($transRows);

        //get game data
        $gameRows = $this->getGameDataList($fromHour, $toHour, $player_id);
        $gameMap=null;
        if(!empty($gameRows)){
            foreach ($gameRows as $row) {
                $gameMap[$row['date_hour']][$row['player_id']]=$row;
            }
        }

        //get game logs more bets and result
        $gameLogsRows = $this->getGameLogsTotal($fromHour, $toHour, $player_id);
        $gameLogsMap=null;
        if(!empty($gameLogsRows)){
            foreach ($gameLogsRows as $row) {
                $gameLogsMap[$row['date_hour']][$row['player_id']]=$row;
            }
        }
        unset($gameLogsRows);

        $this->utils->debug_log('before process '.$from.' to '.$to, $hourList); //, $transMap, $gameMap);

        foreach ($hourList as $date_hour) {

            $gameList=$transList=$gameLogList=null;
            if(!empty($transMap)){
                $transList=isset($transMap[$date_hour]) ? $transMap[$date_hour] : null;
            }
            if(!empty($gameMap)){
                $gameList=isset($gameMap[$date_hour]) ? $gameMap[$date_hour] : null;
            }
            if(!empty($gameLogsMap)){
                $gameLogList=isset($gameLogsMap[$date_hour]) ? $gameLogsMap[$date_hour] : null;
            }
            $playerDataMap=null;
            if(!empty($transList)){

                foreach ($transList as $playerId=>$transInfo) {
                    $playerInfo=isset($playerMap[$playerId]) ? $playerMap[$playerId] : null;
                    if(empty($playerInfo)){
                        //lost player
                        $this->utils->error_log('lost player on generate_player_report_hourly --------------> '.$playerId);
                        continue;
                    }

                    //always new, because it's first
                    $playerDataMap[$playerId] = [
                        'player_id'         =>  $playerId,
                        'affiliate_id'      =>  $playerInfo['affiliateId'],
                        'agent_id'          =>  $playerInfo['agent_id'],
                        'level_id'          =>  $playerInfo['levelId'],
                        'level_name'        =>  $playerInfo['levelName'],
                        'group_name'        =>  $playerInfo['groupName'],
                        'total_deposit'     =>  $this->utils->roundCurrencyForShow($transInfo['deposit_amount']),
                        'total_withdrawal'  =>  $this->utils->roundCurrencyForShow($transInfo['withdrawal_amount']),
                        'total_bonus'       =>  $this->utils->roundCurrencyForShow($transInfo['bonus_amount']),
                        'total_cashback'    =>  $this->utils->roundCurrencyForShow($transInfo['cashback_amount']),
                        'total_manual'      =>  $this->utils->roundCurrencyForShow($transInfo['manual_amount']),
                        'total_gross'       =>  $this->utils->roundCurrencyForShow($transInfo['deposit_amount']-$transInfo['withdrawal_amount']),
                        'date_hour'         =>  $date_hour,
                        'deposit_times'     =>  $transInfo['total_deposit_times'],
                        'referral_bonus'     =>  $transInfo['referral_bonus'],
                        'subtract_bonus'     =>  $transInfo['subtract_bonus'],
                        'manual_bonus'      =>  $transInfo['manual_bonus'],
                    ];

                    $this->db->select("amount, created_at")->from('transactions')
                        ->where('to_id', $playerId)
                        ->where('to_type', Transactions::PLAYER)
                        ->where('transaction_type', Transactions::DEPOSIT)
                        ->where('status', Transactions::APPROVED)
                        ->order_by('transactions.created_at', 'ASC')
                        ->limit(2);
                    $deposits_res = $this->runMultipleRowArray();

                    if(count($deposits_res) > 0 ){
                        $playerDataMap[$playerId]['first_deposit_amount'] = $deposits_res[0]['amount'];
                        $playerDataMap[$playerId]['first_deposit_datetime'] = $deposits_res[0]['created_at'];
                    }else if(count($deposits_res) > 1){
                        $playerDataMap[$playerId]['second_deposit_amount'] = $deposits_res[1]['amount'];
                    }
                }

            }

            if(!empty($gameList)){

                foreach ($gameList as $playerId=>$gameInfo) {
                    $playerInfo=isset($playerMap[$playerId]) ? $playerMap[$playerId] : null;
                    if(empty($playerInfo)){
                        //lost player
                        $this->utils->error_log('lost player on generate_player_report_hourly --------------> '.$playerId);
                        continue;
                    }

                    if(isset($playerDataMap[$playerId])){
                        //exists transaction
                        $playerDataMap[$playerId]['total_bet']=$this->utils->roundCurrencyForShow($gameInfo['betting_amount']);
                        $playerDataMap[$playerId]['total_win']=$this->utils->roundCurrencyForShow($gameInfo['win_amount']);
                        $playerDataMap[$playerId]['total_loss']=$this->utils->roundCurrencyForShow($gameInfo['loss_amount']);
                        $playerDataMap[$playerId]['total_result']=$this->utils->roundCurrencyForShow($gameInfo['result_amount']);
                        $playerDataMap[$playerId]['payout']         = $this->utils->roundCurrencyForShow($gameInfo['payout']);
                        $playerDataMap[$playerId]['payout_rate']    = !empty($gameInfo['payout_rate']) ? $gameInfo['payout_rate'] : 0;
                        $playerDataMap[$playerId]['game_platform_id']    = $gameInfo['game_platform_id'];

                    }else{
                        //only exist game in this hour
                        $playerDataMap[$playerId]=[
                            'player_id'=>$playerId,
                            'total_deposit'=>0,
                            'total_withdrawal'=>0,
                            'total_bonus'=>0,
                            'total_cashback'=>0,
                            'total_manual'=>0,
                            'total_gross'=>0,
                            'total_bet'=>$this->utils->roundCurrencyForShow($gameInfo['betting_amount']),
                            'total_win'=>$this->utils->roundCurrencyForShow($gameInfo['win_amount']),
                            'total_loss'=>$this->utils->roundCurrencyForShow($gameInfo['loss_amount']),
                            'total_result'=>$this->utils->roundCurrencyForShow($gameInfo['result_amount']),
                            'payout' => $this->utils->roundCurrencyForShow($gameInfo['payout']),
                            'payout_rate' => !empty($gameInfo['payout_rate']) ? $gameInfo['payout_rate'] : 0,
                            'date_hour'=>$date_hour,
                            'game_platform_id'=> $gameInfo['game_platform_id'],
                        ];
                    }
                    if(isset($gameLogList[$playerId])){
                        $gameLogsInfo=$gameLogList[$playerId];
                        $playerDataMap[$playerId]['winning_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['winning_bets']);
                        $playerDataMap[$playerId]['lost_bets']= $this->utils->roundCurrencyForShow($gameLogsInfo['lost_bets']);
                        $playerDataMap[$playerId]['tie_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['tie_bets']);
                        $playerDataMap[$playerId]['total_odds_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['total_odds_bets']);
                        $playerDataMap[$playerId]['total_odds_real_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['total_odds_real_bets']);
                        $playerDataMap[$playerId]['total_live_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['total_live_bets']);
                        $playerDataMap[$playerId]['total_live_real_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['total_live_real_bets']);
                        $playerDataMap[$playerId]['live_winning_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['live_winning_bets']);
                        $playerDataMap[$playerId]['live_lost_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['live_lost_bets']);
                        $playerDataMap[$playerId]['live_tie_bets'] = $this->utils->roundCurrencyForShow($gameLogsInfo['live_tie_bets']);
                        $playerDataMap[$playerId]['live_gain_sum'] = $this->utils->roundCurrencyForShow($gameLogsInfo['live_gain_sum']);
                    }
                }
            }

            if(!empty($playerDataMap)) {

                $this->utils->debug_log('date_hour: ' . $date_hour . ',playerDataMap: ' . count($playerDataMap));

                foreach ($playerDataMap as $playerId => $reportData) {
                    $playerInfo = isset($playerMap[$playerId]) ? $playerMap[$playerId] : null;
                    if (empty($playerInfo)) {
                        //lost player
                        $this->utils->error_log('lost player on generate_player_report_hourly --------------> ' . $playerId);
                        continue;
                    }
                    //build unique key
                    $unique_key =$currencyKey.'-'.$playerId.'-'.$date_hour;

                    $reportData['affiliate_id'] = $playerInfo['affiliateId'];
                    $reportData['agent_id'] = $playerInfo['agent_id'];
                    $reportData['level_id'] = $playerInfo['levelId'];
                    $reportData['level_name'] = $playerInfo['levelName'];
                    $reportData['group_name'] = $playerInfo['groupName'];
                    $reportData['player_username'] = $playerInfo['player_username'];
                    $reportData['player_realName'] = $playerInfo['player_realName'];
                    $reportData['email'] = $playerInfo['email'];
                    $reportData['contactNumber'] = $playerInfo['contactNumber'];
                    $reportData['gender'] = $playerInfo['gender'];
                    $reportData['agent_username'] = $playerInfo['agent_username'];
                    $reportData['affiliate_username'] = $playerInfo['affiliate_username'];
                    $reportData['registered_by'] = $playerInfo['registered_by'];
                    $reportData['registered_date'] = $playerInfo['registered_date'];
                    $reportData['registrationIP'] = $playerInfo['registrationIP'];
                    $reportData['last_login_ip'] = $playerInfo['last_login_ip'];
                    $reportData['last_login_date'] = $playerInfo['last_login_date'];
                    $reportData['last_logout_date'] = $playerInfo['last_logout_date'];

                    $reportData['unique_key'] = $unique_key;
                    $reportData['currency_key']=$currencyKey;

                    //update or insert
                    $this->db->select('id')->from('player_report_hourly')->where('player_id', $playerId)->where('date_hour', $date_hour);
                    $id = $this->runOneRowOneField('id');
                    if (empty($id)) {
                        //insert
                        $reportData['created_at'] = $this->utils->getNowForMysql();
                        $reportData['updated_at'] = $this->utils->getNowForMysql();
                        $success = $this->insertData('player_report_hourly', $reportData);
                        // $this->utils->debug_log('insert date_hour', $date_hour, $reportData);
                    } else {
                        //update
                        $reportData['updated_at'] = $this->utils->getNowForMysql();
                        $this->db->set($reportData)->where('id', $id);
                        $success = $this->runAnyUpdate('player_report_hourly');
                        // $this->utils->debug_log('update date_hour', $date_hour, $reportData);
                    }
                    if (!$success) {
                        $this->utils->error_log('insert/update player_report_hourly failed', $reportData, $id);
                        break;
                    }
                }

                if (!$success) {
                    break;
                }
            }

            unset($transList);
            unset($gameList);
            unset($gameLogList);
            unset($playerDataMap);

            unset($transMap[$date_hour]);
            unset($gameMap[$date_hour]);
            unset($gameLogsMap[$date_hour]);
        }

        unset($transMap);
        unset($gameMap);
        unset($gameLogsMap);
        unset($playerMap);

        return $success;

    }

    private function getGameDataList($fromHour, $toHour, $player_id){
        $this->db->select('player_id, date_hour, sum(betting_amount) betting_amount, game_platform_id')
            ->select('SUM(result_amount)/SUM(betting_amount) as payout_rate')
            ->select_sum('result_amount', 'payout')
            ->select_sum('win_amount', 'win_amount')
            ->select_sum('loss_amount', 'loss_amount')
            ->select_sum('result_amount', 'result_amount')
            ->from('total_player_game_hour')
            ->where('(date_hour >= '.$fromHour.' AND date_hour <= '.$toHour.')');
            if(!empty($player_id)){
                $this->db->where('player_id', $player_id);
            }
            $this->db->group_by(['player_id', 'date_hour', 'game_platform_id']);

        return $this->runMultipleRowArray();
    }

    private function getGameLogsTotal($fromHour, $toHour, $player_id){
        $this->db->select('player_id, DATE_FORMAT(end_at, "%Y%m%d%H") date_hour, game_platform_id', FALSE)
            ->select_sum('(CASE WHEN result_amount > 0 THEN bet_amount ELSE 0 END)', 'winning_bets')
            ->select_sum('(CASE WHEN result_amount < 0 THEN bet_amount ELSE 0 END)', 'lost_bets')
            ->select_sum('(CASE WHEN result_amount = 0 THEN bet_amount ELSE 0 END)', 'tie_bets')
            ->select_sum('(CASE WHEN (ISNULL(odds) OR odds = 0 OR odds > 2) THEN bet_amount ELSE bet_amount * (odds-1) END)', 'total_odds_bets')
            ->select_sum('(CASE WHEN (ISNULL(odds) OR odds = 0 OR odds > 2) THEN trans_amount ELSE trans_amount * (odds-1) END)', 'total_odds_real_bets')
            ->select_sum('(CASE WHEN match_type = 1 THEN bet_amount ELSE 0 END)', 'total_live_bets')
            ->select_sum('(CASE WHEN match_type = 1 THEN trans_amount ELSE 0 END)', 'total_live_real_bets')
            ->select_sum('(CASE WHEN match_type = 1 AND result_amount > 0 THEN bet_amount ELSE 0 END)', 'live_winning_bets')
            ->select_sum('(CASE WHEN match_type = 1 AND result_amount < 0 THEN bet_amount ELSE 0 END)', 'live_lost_bets')
            ->select_sum('(CASE WHEN match_type = 1 AND result_amount = 0 THEN bet_amount ELSE 0 END)', 'live_tie_bets')
            ->select_sum('(CASE WHEN match_type = 1 AND result_amount >= 0 THEN result_amount ELSE 0 END)', 'live_gain_sum')
            ->from('game_logs')
            ->where('flag', 1);
            if(!empty($player_id)){
                $this->db->where('player_id', $player_id);
            }
            $fromDateTime=$this->utils->convertHourFormatToDateTime($fromHour, true);
            $toDateTime=$this->utils->convertHourFormatToDateTime($toHour, false);
            //date time
            $this->db->where('end_at >=', $fromDateTime)->where('end_at <=', $toDateTime);

            // $this->db->where('(DATE_FORMAT(end_at, "%Y%m%d%H") >= "'.$fromHour.'" and DATE_FORMAT(end_at, "%Y%m%d%H") <= "'.$toHour.'")')
            $this->db->group_by(['player_id', 'DATE_FORMAT(end_at, "%Y%m%d%H")', 'game_platform_id']);

        return $this->runMultipleRowArray();
    }

    public function generate_summary2_report_daily($dateFrom, $dateTo){
        if(empty($dateFrom) || empty($dateTo)){
            return false;
        }
        $success=true;
        $currencyKey=$this->utils->getActiveCurrencyKey();

        $dateFrom = (!$dateFrom) ? date("Y-m-01") : $dateFrom;
        $dateTo = (!$dateTo) ? date("Y-m-d") : $dateTo;

        // $this->load->model(array('report_model'));
        $transaction_summary_list = $this->report_summary_2($dateFrom, $dateTo);
        $transaction_summary_list = array_combine(array_column($transaction_summary_list, 'common_date'), $transaction_summary_list);

        $i = 1;
        $dateDiff = strtotime($dateFrom) - strtotime($dateTo);
        $dayNumberDiff = floor($dateDiff / (60 * 60 * 24));

        while ($i <= (abs($dayNumberDiff) + 1)) {
            $dateFrom = date('Y-m-d', strtotime($dateFrom . (($i == 1) ? 0 : 1) . " days"));
            $new_and_total_players = $this->get_new_and_total_players('DATE', $dateFrom);
            $firs_and_second_deposit = $this->get_first_and_second_deposit('DATE', $dateFrom);
            $betWinLossPayoutCol = $this->sumBetWinLossPayout('DATE', $dateFrom);
            $data[] = array_merge(array('slug' => str_replace('-', '/', $dateFrom)), $betWinLossPayoutCol, $new_and_total_players, $firs_and_second_deposit,
                isset($transaction_summary_list[$dateFrom]) ? $transaction_summary_list[$dateFrom] : array(
                    'common_date' => $dateFrom,
                    'total_deposit' => 0,
                    'total_withdraw' => 0,
                    'total_bonus' => 0,
                    'total_cashback' => 0,
                    'total_transaction_fee' => 0,
                    'bank_cash_amount' => 0,
                    'payment' => 0,
                ));
            $i++;
        }

        if (!empty($data)) {
            // $output['data'] = array_values($data);
            //write to db
            foreach ($data as $row) {
                $summary_date=$row['common_date'];
                $unique_key=$currencyKey.'-'.$summary_date;

                $d=[
                    'summary_date'=>$summary_date,
                    'count_new_player'=> !empty($row['new_players']) ? $row['new_players'] : 0,
                    'count_all_players'=>$row['total_players'],
                    'count_first_deposit'=>$row['first_deposit'],
                    'count_second_deposit'=>$row['second_deposit'],
                    'total_deposit'=>$row['total_deposit'],
                    'total_withdrawal'=>$row['total_withdraw'],
                    'total_bonus'=>$row['total_bonus'],
                    'total_cashback'=>$row['total_cashback'],
                    'total_fee'=>$row['total_transaction_fee'],
                    'total_bank_cash_amount'=>$row['bank_cash_amount'],
                    'total_bet'=>$row['total_bet'],
                    'total_win'=>$row['total_win'],
                    'total_loss'=>$row['total_loss'],
                    'total_payout'=>$row['payout'],
                    'unique_key'=>$unique_key,
                    'currency_key'=>$currencyKey,
                    'last_update_time'=>$this->utils->getNowForMysql(),
                ];

                $this->db->select('id')->from('summary2_report_daily')->where('summary_date', $summary_date);
                $id=$this->runOneRowOneField('id');
                if(empty($id)){
                    //insert
                    $success=$this->insertData('summary2_report_daily', $d);
                }else{
                    //update
                    $this->db->set($d)->where('id', $id);
                    $success=$this->runAnyUpdate('summary2_report_daily');
                }
                if(!$success){
                    $this->utils->error_log('insert/update summary2 failed', $d, $id);
                    break;
                }

                unset($d);
            }

            unset($data);
        }

        return $success;
    }

    public function getOneworksDataByWinloss($date){
        $this->load->model(['original_game_logs_model']);
        $sqlTime='date(winlost_datetime) = ?';
        $sql = <<<EOD
SELECT oneworks.id as sync_index,
oneworks.trans_id,
oneworks.vendor_member_id as player_username,
oneworks.stake as bet_amount,
oneworks.stake as real_bet_amount,
oneworks.winlost_amount as result_amount,
oneworks.ticket_status as status_in_db,
oneworks.sport_type as game_code,
oneworks.sport_type as game,
oneworks.cash_out_data,
oneworks.original_stake,
oneworks.winlost_datetime,

game_provider_auth.player_id,

gd.id as game_description_id,
gd.game_name as game_description_name,
gd.game_type_id,

p.affiliateId,
p.agent_id,
p.username,
concat(p.groupname," - ",p.levelname) as player_level,
af.username as affiliate_username,
ag.agent_name as agent_username

FROM oneworks_game_logs as oneworks
LEFT JOIN game_description as gd ON oneworks.sport_type = gd.external_game_id AND gd.game_platform_id = ?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth ON oneworks.vendor_member_id = game_provider_auth.login_name and game_provider_auth.game_provider_id=?
JOIN player as p ON game_provider_auth.player_id = p.playerId
LEFT JOIN affiliates as af ON p.affiliateId = af.affiliateId
LEFT JOIN agency_agents as ag ON p.agent_id = ag.agent_id
WHERE

{$sqlTime}
EOD;
//AND
//LOWER(ticket_status) in('won','draw','lose','half won','half lose')
//EOD;
//old LOWER(ticket_status) not in('running','void','reject','refund')
$params=[ONEWORKS_API, ONEWORKS_API,
          $date];
        $result = $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
        return $result;
    }

    public function preprocessOneworksData(array &$rows){
        $this->CI->load->model(array('game_logs'));
        if(!empty($rows)){
            foreach ($rows as $key => &$row) {
                if(!empty($row['cash_out_data'])){
                    $cash_out_data = json_decode($row['cash_out_data']);
                    $cashout_winloss = $row['result_amount'];
                    foreach ($cash_out_data as $key => $cashout) {
                        $cashout_winloss = ((float)$cashout->buyback_amount - ((! isset($cashout->real_stake)) ? 0 : $cashout->real_stake));
                    }
                    $arr = [ # array of result amount of oneworks_game_logs table and cashout_winloss of cash_out_data
                        'result_amount' => $row['result_amount'],
                        'cashout_winloss' => $cashout_winloss
                    ];
                    if(!isset($row['total_wins'])){ $row['total_wins'] = 0; } # init total_win if not exist

                    if(!isset($row['total_loss'])){ $row['total_loss'] = 0; } # init total_loss if not exist
                    foreach($arr as $key => $value){
                        if($value < 0){
                            $row['total_loss'] += abs($value);
                        }else{
                            $row['total_wins'] += abs($value);
                        }
                    }
                }
            }
        }
    }

    public function generate_oneworks_report_daily($date){
        $this->load->model(['original_game_logs_model']);
        $date = date("Y-m-d", strtotime($date));
        $rows = $this->getOneworksDataByWinloss($date);
        $this->preprocessOneworksData($rows);
        $MD5_FIELDS_FOR_ORIGINAL =['status_in_db','winlost_datetime'];
        $MD5_FLOAT_AMOUNT_FIELDS =['bet_amount', 'real_bet_amount', 'result_amount'];
        $result['data_count'] = 0;
        if(!empty($rows)){

            list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal('oneworks_game_report', $rows,
                    'trans_id', 'trans_id', $MD5_FIELDS_FOR_ORIGINAL, 'md5_sum', 'id', $MD5_FLOAT_AMOUNT_FIELDS);

            unset($rows);
            if (!empty($insertRows)) {
                $result['data_count'] += $this->updateOrInsertOneworksReport($insertRows, 'insert');
            }
            unset($insertRows);
            if (!empty($updateRows)) {
                $result['data_count'] += $this->updateOrInsertOneworksReport($updateRows, 'update');
            }
            unset($updateRows);
        }
        return array(true,$result);

    }
    public function updateOrInsertOneworksReport($rows, $update_type){
        $this->load->model(['original_game_logs_model']);
        $dataCount=0;
        if(!empty($rows)){
            foreach ($rows as $value) {
				$data = [
					'game_platform_id' => ONEWORKS_API,
					'game_type_id' => $value['game_type_id'],
					'game_description_id' => $value['game_description_id'],
					'player_id' => $value['player_id'],
					'player_username' => $value['username'],
					'player_level' => $value['player_level'],
					'affiliate_username' => $value['affiliate_username'],
					'affiliate_id' => $value['affiliateId'],
					'agent_id' => $value['agent_id'],
					'agent_name' => $value['agent_username'],
					'total_bets' => $value['bet_amount'],
					'total_wins' => (isset($value['total_wins'])) ?  $value['total_wins'] : (($value['result_amount'] > 0 ) ? abs($value['result_amount'])  : 0),
					'total_loss' => (isset($value['total_loss'])) ? $value['total_loss'] : (($value['result_amount'] < 0 ) ? abs($value['result_amount'])  : 0),
					'game_date' => $value['winlost_datetime'],
					'trans_id'=>$value['trans_id'],
					'md5_sum'=>$value['md5_sum'],
                    'status'=>strtolower($value['status_in_db'])
				];
				$data['payout'] = $data['total_loss'] - $data['total_wins'];
				$data['payout_rate'] = ($value['bet_amount'] > 0) ? $data['payout'] / $value['bet_amount'] : null;
                //insert or update data to Oneworks API report table database
                if ($update_type=='update') {
                    $data['id']=$value['id'];
                    $this->CI->original_game_logs_model->updateRowsToOriginal('oneworks_game_report', $data);
                } else {
                    $this->CI->original_game_logs_model->insertRowsToOriginal('oneworks_game_report', $data);
                }
                $dataCount++;
                unset($data);
            }
        }

        return $dataCount;
    }


    /***
     *  SBOBET GAME REPORT
     */
    public function generate_sbobet_game_report_daily($date){
        $this->load->model(['original_game_logs_model']);
        $date = date("Y-m-d", strtotime($date));
        $rows = $this->getSbobetSportsDataByWinloss($date);
        $this->preprocessSboBetSportsData($rows);

        $MD5_FIELDS_FOR_ORIGINAL =['status_in_db','ref_no','winlostDate'];
        $MD5_FLOAT_AMOUNT_FIELDS =['actualStake', 'winlose', 'result_amount'];
        $result['data_count'] = 0;
        if(!empty($rows)){

            list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal('sbo_game_report', $rows,
                'ref_no', 'ref_no', $MD5_FIELDS_FOR_ORIGINAL, 'md5_sum', 'id', $MD5_FLOAT_AMOUNT_FIELDS);

			unset($rows);
			if (!empty($insertRows)) {
				$result['data_count'] += $this->updateOrInsertSbobetSportsReport($insertRows, 'insert');
			}
			unset($insertRows);
			if (!empty($updateRows)) {
				$result['data_count'] += $this->updateOrInsertSbobetSportsReport($updateRows, 'update');
			}
			unset($updateRows);
		}
		return array(true,$result);
	}

	public function getSbobetSportsDataByWinloss($date){
		# payout (winlose-actualStake)
		# filter waiting rejected ( game bo removed status waiting rejected in computation of payout and result amount ) which cause variance
		$this->load->model(['original_game_logs_model']);
		$sqlTime='date(winlostDate) = ?';
		$sql = <<<EOD
SELECT whitelabel.id as sync_index,
whitelabel.refNo as ref_no,
whitelabel.UserName as player_username,
whitelabel.actualStake,
whitelabel.winlose,
whitelabel.status as status_in_db,
whitelabel.external_game_id as game_code,
whitelabel.external_game_id as game,
whitelabel.stake AS bet_amount,
whitelabel.winlostDate,

game_provider_auth.player_id,

gd.id as game_description_id,
gd.game_name as game_description_name,
gd.game_type_id,

p.affiliateId,
p.agent_id,
p.username,
concat(p.groupname," - ",p.levelname) as player_level,
af.username as affiliate_username,
ag.agent_name as agent_username

FROM whitelabel_game_logs as whitelabel
LEFT JOIN game_description as gd ON whitelabel.external_game_id = gd.external_game_id AND gd.game_platform_id = ?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth ON whitelabel.UserName = game_provider_auth.login_name and game_provider_auth.game_provider_id=?
JOIN player as p ON game_provider_auth.player_id = p.playerId
LEFT JOIN affiliates as af ON p.affiliateId = af.affiliateId
LEFT JOIN agency_agents as ag ON p.agent_id = ag.agent_id
WHERE

{$sqlTime}
AND
LOWER(whitelabel.status) not in('running','void','reject','refund','waiting rejected')
EOD;
        $params=[SBOBET_API, SBOBET_API,
            $date];
        $result = $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
        return $result;
    }

    public function preprocessSboBetSportsData(array &$rows){
        $this->CI->load->model(array('game_logs'));
        if(!empty($rows)){
            foreach ($rows as $key => &$row) {
                $row['result_amount'] = $row['winlose']-$row['actualStake'];
            }
        }
    }

    public function updateOrInsertSbobetSportsReport($rows, $update_type){
        $this->load->model(['original_game_logs_model']);
        $dataCount=0;
        if(!empty($rows)){
            foreach ($rows as $value) {
                $data = [
                    'game_platform_id' => SBOBET_API,
                    'game_type_id' => $value['game_type_id'],
                    'game_description_id' => $value['game_description_id'],
                    'player_id' => $value['player_id'],
                    'player_username' => $value['username'],
                    'player_level' => $value['player_level'],
                    'affiliate_username' => $value['affiliate_username'],
                    'affiliate_id' => $value['affiliateId'],
                    'agent_id' => $value['agent_id'],
                    'agent_name' => $value['agent_username'],
                    'total_bets' => $value['bet_amount'],
                    'total_wins' => ($value['result_amount'] > 0 ) ? abs($value['result_amount'])  : 0,
                    'total_loss' => ($value['result_amount'] < 0 ) ? abs($value['result_amount'])  : 0,
                    'game_date' => $value['winlostDate'],
                    'ref_no'=>$value['ref_no'],
                    'md5_sum'=>$value['md5_sum'],
                ];
                $data['payout'] = $data['total_loss'] - $data['total_wins'];
                $data['payout_rate'] = ($value['bet_amount'] > 0) ? $data['payout'] / $value['bet_amount'] : null;
                //insert or update data to whitelabel API report table database

                if ($update_type=='update') {
                    $data['id']=$value['id'];
                    $this->CI->original_game_logs_model->updateRowsToOriginal('sbo_game_report', $data);
                } else {
                    $this->CI->original_game_logs_model->insertRowsToOriginal('sbo_game_report', $data);
                }
                $dataCount++;
                unset($data);
            }
        }
        return $dataCount;
    }

    /**
     * generate payment_report_daily from transactions
     * @param  string $from
     * @param  string $to
     * @param  object $db
     * @return boolean $success
     */
    public function generate_payment_report_daily($from, $to, $db=null){
        if(empty($from) || empty($to)){
            return false;
        }
        if(!empty($db)){
            $db=$this->db;
        }
        $this->load->model(['transactions', 'external_system', 'payment_account']);
        $fromDate=$this->utils->formatDateForMysql(new DateTime($from));
        $toDate=$this->utils->formatDateForMysql(new DateTime($to));

        $success=true;
        $currencyKey=$this->utils->getActiveCurrencyKey();

        $payMap=[];
        $paymentRows=$this->external_system->getPaymentSystems();
        if(!empty($paymentRows)){
            foreach ($paymentRows as $r) {
                $payMap[$r->id]=$r->system_code;
            }
        }
        unset($paymentRows);
        //unique key
        //<payment_date>-<transaction_type>-<player_id>-<payment_account_id>-<external_system_id>

        $sql=<<<EOD
select
transactions.transaction_type, transactions.to_id as player_id,
player.levelId as level_id, player.levelName as player_group_level_name, player.groupName as player_group_name,
player.username as player_username, playerdetails.firstName, playerdetails.lastName,
transactions.trans_date as payment_date, transactions.payment_account_id, payment_account.payment_account_name as payment_account_name,
payment_account.second_category_flag as second_category_flag, payment_account.flag as first_category_flag,
banktype.bankTypeId as bank_type_id, banktype.bankName as bank_type_name, banktype.external_system_id as deposit_api_id,
walletaccount.paymentAPI as withdrawal_api_id,
sum(transactions.amount) as amount
from transactions
left join walletaccount on walletaccount.transaction_id=transactions.id
left join payment_account on payment_account.id=transactions.payment_account_id
left join banktype on banktype.bankTypeId=payment_account.payment_type_id
join player on transactions.to_id=player.playerId
join playerdetails on transactions.to_id=playerdetails.playerId
where
trans_date>=? and trans_date<=?
and transaction_type in (?,?)
and to_type=?
group by transactions.transaction_type, transactions.to_id,
transactions.trans_date, transactions.payment_account_id,
banktype.external_system_id, walletaccount.paymentAPI
EOD;

        $params=[$fromDate, $toDate, Transactions::DEPOSIT, Transactions::WITHDRAWAL,
            Transactions::PLAYER];

        $this->utils->debug_log('try run sql', $sql, $params);
        $rows=$this->runRawSelectSQLArrayUnbuffered($sql, $params, $db);
        if(!empty($rows)){
            $this->utils->info_log('found rows when generate_payment_report_daily', count($rows));
            foreach ($rows as $row) {

                //firstName+lastName=>player_realname
                $firstName=$row['firstName'];
                $lastName=$row['lastName'];
                $player_realname=$firstName.$lastName;
                $row['player_realname']=$player_realname;
                unset($row['firstName']);
                unset($row['lastName']);
                $row['player_group_and_level']=$row['player_group_name'].' - '.$row['player_group_level_name'];
                //deposit_api_id,withdrawal_api_id=>external_system_id,external_system_code
                $deposit_api_id=$row['deposit_api_id'];
                $withdrawal_api_id=$row['withdrawal_api_id'];
                $external_system_id=null;
                $external_system_code=null;
                switch ($row['transaction_type']) {
                    case Transactions::DEPOSIT:
                        $external_system_id=$deposit_api_id;
                        break;
                    case Transactions::WITHDRAWAL:
                        $external_system_id=$withdrawal_api_id;
                        break;
                }
                if(!empty($external_system_id)){
                    if(isset($payMap[$external_system_id])){
                        $external_system_code=$payMap[$external_system_id];
                    }
                }
                $row['external_system_id']=$external_system_id;
                $row['external_system_code']=$external_system_code;
                unset($row['deposit_api_id']);
                unset($row['withdrawal_api_id']);
                $row['currency_key']=$currencyKey;
                $row['unique_key']=$currencyKey.'-'.$row['payment_date'].'-'.$row['transaction_type'].'-'.
                    $row['player_id'].'-'.$row['payment_account_id'].'-'.$row['external_system_id'];
                //update or insert
                $this->db->select('id')->from('payment_report_daily')->where('unique_key', $row['unique_key']);
                $id = $this->runOneRowOneField('id');
                if (empty($id)) {
                    //insert
                    $row['created_at'] = $this->utils->getNowForMysql();
                    $row['updated_at'] = $this->utils->getNowForMysql();
                    $success = $this->insertData('payment_report_daily', $row);
                } else {
                    //update
                    $row['updated_at'] = $this->utils->getNowForMysql();
                    $this->db->set($row)->where('id', $id);
                    $success = $this->runAnyUpdate('payment_report_daily');
                }
                if (!$success) {
                    $this->utils->error_log('insert/update payment_report_daily failed', $reportData, $id);
                    break;
                }

            }

        }
        unset($rows);

        return $success;
    }

    public function generate_game_report_houry($from, $to, $db=null){
        if(empty($from) || empty($to)){
            return false;
        }
        if(!empty($db)){
            $db=$this->db;
        }

        $success=true;
        $currencyKey=$this->utils->getActiveCurrencyKey();

        $fromHour=$this->utils->formatDateHourForMysql(new DateTime($from));
        $toHour=$this->utils->formatDateHourForMysql(new DateTime($to));

        $result=['updateCount'=>0, 'insertCount'=>0];

        $sql=<<<EOD
select
player.levelId as level_id, player.levelName as player_group_level_name, player.groupName as player_group_name,
total_player_game_hour.player_id, player.username as player_username, playerdetails.firstName, playerdetails.lastName,
total_player_game_hour.game_platform_id, game_type.game_type_code, game_description.external_game_id,
external_system.system_code as game_platform_code,
total_player_game_hour.betting_amount,total_player_game_hour.real_betting_amount, total_player_game_hour.result_amount,
total_player_game_hour.win_amount, total_player_game_hour.loss_amount,
total_player_game_hour.date_hour
from total_player_game_hour
join player on total_player_game_hour.player_id=player.playerId
join playerdetails on total_player_game_hour.player_id=playerdetails.playerId
left join game_type on total_player_game_hour.game_type_id=game_type.id
join external_system on total_player_game_hour.game_platform_id=external_system.id
join game_description on total_player_game_hour.game_description_id=game_description.id
where
total_player_game_hour.date_hour>=? and total_player_game_hour.date_hour<=?
EOD;

        $params=[$fromHour, $toHour];

        $this->utils->debug_log('try run sql', $sql, $params);
        $rows=$this->runRawSelectSQLArrayUnbuffered($sql, $params, $db);
        if(!empty($rows)){
            $this->utils->info_log('found rows when generate_game_report_houry', count($rows));
            foreach ($rows as $row) {

                //firstName+lastName=>player_realname
                $firstName=$row['firstName'];
                $lastName=$row['lastName'];
                $player_realname=$firstName.$lastName;
                $row['player_realname']=$player_realname;
                unset($row['firstName']);
                unset($row['lastName']);
                $row['player_group_and_level']=$row['player_group_name'].' - '.$row['player_group_level_name'];

                if($row['real_betting_amount']===null){
                    $row['real_betting_amount']=$row['betting_amount'];
                }
                $this->processNullToZero($row, ['betting_amount', 'result_amount', 'win_amount', 'loss_amount']);

                //<currency_key>-<date_hour>-<player_id>-<game_platform_id>-<game_type_code>-<external_game_id>
                $row['currency_key']=$currencyKey;
                $row['unique_key']=$currencyKey.'-'.$row['date_hour'].'-'.$row['player_id'].'-'.
                    $row['game_platform_id'].'-'.$row['game_type_code'].'-'.$row['external_game_id'];
                //update or insert
                $this->db->select('id')->from('game_report_hourly')->where('unique_key', $row['unique_key']);
                $id = $this->runOneRowOneField('id');
                if (empty($id)) {
                    $result['insertCount']++;
                    //insert
                    $row['created_at'] = $this->utils->getNowForMysql();
                    $row['updated_at'] = $this->utils->getNowForMysql();
                    $success = $this->insertData('game_report_hourly', $row);
                } else {
                    $result['updateCount']++;
                    //update
                    $row['updated_at'] = $this->utils->getNowForMysql();
                    $this->db->set($row)->where('id', $id);
                    $success = $this->runAnyUpdate('game_report_hourly');
                }
                if (!$success) {
                    $this->utils->error_log('insert/update payment_report_daily failed', $reportData, $id);
                    break;
                }

            }

        }
        unset($rows);
        $this->utils->info_log('sync result', $result);

        return $success;

    }

    public function generate_promotion_report_details($from, $to, $db=null){
        if(empty($from) || empty($to)){
            return false;
        }
        if(!empty($db)){
            $db=$this->db;
        }
        $this->load->model(['transactions', 'external_system', 'promorules']);
        $fromDate=$from;
        $toDate=$to;

        $success=true;
        $currencyKey=$this->utils->getActiveCurrencyKey();

        $transaction_types = implode(',', [Transactions::ADD_BONUS, Transactions::MEMBER_GROUP_DEPOSIT_BONUS,
            Transactions::PLAYER_REFER_BONUS, Transactions::RANDOM_BONUS]);

        $sql=<<<EOD
select
playerpromo.playerpromoId as player_promo_id,transactions.transaction_type, playerpromo.playerId as player_id,
player.levelId as level_id, player.levelName as player_group_level_name, player.groupName as player_group_name,
player.username as player_username, playerdetails.firstName, playerdetails.lastName,
transactions.created_at as promotion_datetime, transpromotype.promoTypeName as promo_type,
promotype.promoTypeName,
promorules.promoName as promo_name, playerpromo.transactionStatus as promo_status,
transactions.amount as amount
from playerpromo
join transactions on playerpromo.playerpromoId = transactions.player_promo_id and transactions.transaction_type IN ({$transaction_types})
join player on playerpromo.playerId = player.playerId
join playerdetails on playerpromo.playerId = playerdetails.playerId
join promorules on promorules.promorulesId = playerpromo.promorulesId
left join promotype as transpromotype on transpromotype.promotypeId = transactions.promo_category
join promotype on promotype.promotypeId = promorules.promoCategory
where
transactions.created_at>=? and transactions.created_at<=?
EOD;

        $params=[$fromDate, $toDate];

        $this->utils->debug_log('try run sql', $sql, $params);
        $rows=$this->runRawSelectSQLArrayUnbuffered($sql, $params, $db);
        if(!empty($rows)){
            $this->utils->info_log('found rows when generate_promotion_report_details', count($rows));
            foreach ($rows as $row) {

                //firstName+lastName=>player_realname
                $firstName=$row['firstName'];
                $lastName=$row['lastName'];
                $player_realname=$firstName.$lastName;
                $row['player_realname']=$player_realname;
                unset($row['firstName']);
                unset($row['lastName']);
                $row['player_group_and_level']=$row['player_group_name'].' - '.$row['player_group_level_name'];
                //build promo name
                list($promoName, $promoType, $promoDetails) = $this->promorules->getPromoNameAndType($row['transaction_type'],
                    $row['promo_type'], $row['promoTypeName'], $row['promo_name'], null, $row['player_group_and_level']);

                $fullPromoDesc = implode(' - ', array(lang($promoType), $promoName));
                $row['promo_name']=$fullPromoDesc;
                unset($row['promoTypeName']);

                $row['currency_key']=$currencyKey;
                $row['unique_key']=$currencyKey.'-'.$row['player_promo_id'];
                //update or insert
                $this->db->select('id')->from('promotion_report_details')->where('unique_key', $row['unique_key']);
                $id = $this->runOneRowOneField('id');
                if (empty($id)) {
                    //insert
                    $row['created_at'] = $this->utils->getNowForMysql();
                    $row['updated_at'] = $this->utils->getNowForMysql();
                    $success = $this->insertData('promotion_report_details', $row);
                } else {
                    //update
                    $row['updated_at'] = $this->utils->getNowForMysql();
                    $this->db->set($row)->where('id', $id);
                    $success = $this->runAnyUpdate('promotion_report_details');
                }
                if (!$success) {
                    $this->utils->error_log('insert/update promotion_report_details failed', $reportData, $id);
                    break;
                }

            }

        }
        unset($rows);

        return $success;
    }

    public function generate_cashback_report_daily($from, $to, $db=null){
        if(empty($from) || empty($to)){
            return false;
        }
        if(!empty($db)){
            $db=$this->db;
        }
        $this->load->model(['transactions', 'external_system', 'payment_account']);
        $fromDate=$this->utils->formatDateForMysql(new DateTime($from));
        $toDate=$this->utils->formatDateForMysql(new DateTime($to));

        $success=true;
        $currencyKey=$this->utils->getActiveCurrencyKey();

        $payMap=[];
        $paymentRows=$this->external_system->getPaymentSystems();
        if(!empty($paymentRows)){
            foreach ($paymentRows as $r) {
                $payMap[$r->id]=$r->system_code;
            }
        }
        unset($paymentRows);
        //unique key
        //<payment_date>-<transaction_type>-<player_id>-<payment_account_id>-<external_system_id>

        $sql=<<<EOD
select
total_cashback_player_game_daily.total_date as cashback_date,
player.levelId as level_id, player.levelName as player_group_level_name, player.groupName as player_group_name,
player.username as player_username, playerdetails.firstName, playerdetails.lastName,
total_cashback_player_game_daily.id as cashback_id,
total_cashback_player_game_daily.amount as cashback_amount,
total_cashback_player_game_daily.bet_amount as betting_amount,
total_cashback_player_game_daily.original_bet_amount as original_betting_amount,
total_cashback_player_game_daily.paid_amount,
total_cashback_player_game_daily.withdraw_condition_amount,
total_cashback_player_game_daily.paid_date,
total_cashback_player_game_daily.paid_flag,
total_cashback_player_game_daily.cashback_type,
total_cashback_player_game_daily.invited_player_id,
invited_player.username as invited_player_username,
total_cashback_player_game_daily.max_bonus,
total_cashback_player_game_daily.game_platform_id,
total_cashback_player_game_daily.game_type_id,
total_cashback_player_game_daily.game_description_id,
game_type.game_type_code, game_description.external_game_id,
external_system.system_code as game_platform_code

from total_cashback_player_game_daily

join player on total_cashback_player_game_daily.player_id=player.playerId
join playerdetails on total_cashback_player_game_daily.player_id=playerdetails.playerId
join player as invited_player on total_cashback_player_game_daily.player_id=invited_player.playerId
join game_type on total_cashback_player_game_daily.game_type_id=game_type.id
join external_system on total_cashback_player_game_daily.game_platform_id=external_system.id
join game_description on total_cashback_player_game_daily.game_description_id=game_description.id

where
total_date>=? and total_date<=?
EOD;

        $params=[$fromDate, $toDate];

        $this->utils->debug_log('try run sql', $sql, $params);
        $rows=$this->runRawSelectSQLArrayUnbuffered($sql, $params, $db);
        if(!empty($rows)){
            $this->utils->info_log('found rows when generate_cashback_report_daily', count($rows));
            foreach ($rows as $row) {

                //firstName+lastName=>player_realname
                $firstName=$row['firstName'];
                $lastName=$row['lastName'];
                $player_realname=$firstName.$lastName;
                $row['player_realname']=$player_realname;
                unset($row['firstName']);
                unset($row['lastName']);
                $row['player_group_and_level']=$row['player_group_name'].' - '.$row['player_group_level_name'];

                $this->processNullToZero($row, ['betting_amount', 'cashback_amount', 'game_type_code']);

                $row['currency_key']=$currencyKey;
                $row['unique_key']=$currencyKey.'-'.$row['cashback_id'];
                unset($row['cashback_id']);
                //update or insert
                $this->db->select('id')->from('cashback_report_daily')->where('unique_key', $row['unique_key']);
                $id = $this->runOneRowOneField('id');
                if (empty($id)) {
                    //insert
                    $row['created_at'] = $this->utils->getNowForMysql();
                    $row['updated_at'] = $this->utils->getNowForMysql();
                    $success = $this->insertData('cashback_report_daily', $row);
                } else {
                    //update
                    $row['updated_at'] = $this->utils->getNowForMysql();
                    $this->db->set($row)->where('id', $id);
                    $success = $this->runAnyUpdate('cashback_report_daily');
                }
                if (!$success) {
                    $this->utils->error_log('insert/update cashback_report_daily failed', $reportData, $id);
                    break;
                }

            }

        }
        unset($rows);

        return $success;
    }

    public function processNullToZero(&$row, $keys){
        foreach ($keys as $key) {
            if($row[$key]===null){
                $row[$key]=0;
            }
        }
    }

    public function processNullToEmpty(&$row, $keys){
        foreach ($keys as $key) {
            if($row[$key]===null){
                $row[$key]='';
            }
        }
    }

    public function getVRDataForReport($date) {
        $this->load->model(['original_game_logs_model']);
        $sqlTime='date(vr.createTime) = ?';
        $sql = <<<EOD
SELECT 
vr.id AS vr_id,
vr.playerName AS vr_playerName,
vr.cost AS vr_bet,
vr.playerPrize AS vr_playerPrize,
vr.merchantPrize AS vr_merchantPrize,
vr.createTime AS vr_game_date,
vr.external_uniqueid AS vr_external_uniqueid,
vr.merchantCode AS vr_merchantCode,

gpa.player_id as gpa_player_id,

gd.id as gd_gameId,
gd.game_name as gd_game_name,
gd.game_type_id as gd_game_type_id,

p.affiliateId as p_affiliateId,
p.agent_id as p_agent_id,
p.username as p_username,
concat(p.groupname," - ",p.levelname) as p_player_level,
af.username as af_affiliate_username,
ag.agent_name as ag_agent_username

FROM vr_game_logs as vr
LEFT JOIN game_description as gd ON vr.channelId = gd.external_game_id AND gd.game_platform_id = ?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth as gpa ON vr.playerName = gpa.login_name and gpa.game_provider_id = ?
JOIN player as p ON gpa.player_id = p.playerId
LEFT JOIN affiliates as af ON p.affiliateId = af.affiliateId
LEFT JOIN agency_agents as ag ON p.agent_id = ag.agent_id
WHERE

{$sqlTime}
EOD;
$params=[VR_API, VR_API,
          $date];
        $result = $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
        return $result;

    }

    public function preprocessVRData(array &$rows){
        if(!empty($rows)){
            foreach ($rows as $key => &$row) {
                $player_prize = json_decode($row['vr_playerPrize']);
                $merchant_prize = json_decode($row['vr_merchantPrize']);
                $bet_amount = json_decode($row['vr_bet']);

                if(!isset($row['total_wins'])){ $row['total_wins'] = 0; } # init total_win if not exist
                if(!isset($row['total_loss'])){ $row['total_loss'] = 0; } # init total_loss if not exist

                # init md5_sum since it not exist on original game logs;
                $row['md5_sum'] = '';

                if ($merchant_prize == 0) {
                    $row['total_wins'] = 0;
                    $row['total_loss'] = $bet_amount;
                } else {
                    $row['total_wins'] = $merchant_prize;
                    $row['total_loss'] = 0;
                }
            }
        }
    }

    public function generate_vr_report_daily($date){
        $this->load->model(['original_game_logs_model']);
        $date = date("Y-m-d", strtotime($date));
        $rows = $this->getVRDataForReport($date);
        if(!empty($rows)){
            $this->preprocessVRData($rows);
        } else {
            return array(false,'NO DATA ON THIS DATE');
        }
        $MD5_FIELDS_FOR_ORIGINAL =['vr_bet', 'vr_playerPrize', 'vr_merchantPrize', 'vr_game_date'];
        $MD5_FLOAT_AMOUNT_FIELDS =['bet_amount', 'real_bet_amount', 'result_amount'];
        $result['data_count'] = 0;
        if(!empty($rows)){

            list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal('vr_game_report', $rows,
                    'vr_external_uniqueid', 'external_unique_id', $MD5_FIELDS_FOR_ORIGINAL, 'md5_sum', 'id', $MD5_FLOAT_AMOUNT_FIELDS);

            unset($rows);
            if (!empty($insertRows)) {
                $result['data_count'] += $this->updateOrInsertVRReport($insertRows, 'insert');
            }
            unset($insertRows);
            if (!empty($updateRows)) {
                $result['data_count'] += $this->updateOrInsertVRReport($updateRows, 'update');
            }
            unset($updateRows);
        }
        return array(true,$result);

    }
    public function updateOrInsertVRReport($rows, $update_type){
        $this->load->model(['original_game_logs_model']);
        $dataCount=0;
        if(!empty($rows)){
            foreach ($rows as $value) {
                $status = 'lose';
                if ($value['total_wins'] > 0) {
                    $status = 'win';
                }
                $data = [
                    'game_platform_id'    => VR_API,
                    'game_type_id'        => $value['gd_game_type_id'],
                    'game_description_id' => $value['gd_gameId'],
                    'player_id'           => $value['gpa_player_id'],
                    'player_username'     => $value['p_username'],
                    'player_level'        => $value['p_player_level'],
                    'affiliate_username'  => $value['af_affiliate_username'],
                    'affiliate_id'        => $value['p_affiliateId'],
                    'agent_id'            => $value['p_agent_id'],
                    'agent_name'          => $value['ag_agent_username'],
                    'merchant_code'       => $value['vr_merchantCode'],
                    'merchantPrize'       => $value['vr_merchantPrize'],
                    'playerPrize'         => $value['vr_playerPrize'],
                    'total_bets'          => $value['vr_bet'],
                    'total_wins'          => $value['total_wins'],
                    'total_loss'          => $value['total_loss'],
                    'game_date'           => $value['vr_game_date'],
                    'external_unique_id'  => $value['vr_external_uniqueid'],
                    'md5_sum'             => $value['md5_sum'],
                    'status'              => $status
                ];
                $data['payout'] = $data['total_loss'] - $data['total_wins'];
                $data['payout_rate'] = ($value['vr_bet'] > 0) ? ($data['total_loss'] - $data['total_wins']) / $value['vr_bet'] : null;
                //insert or update data to Oneworks API report table database
                if ($update_type=='update') {
                    $data['id']=$value['id'];
                    $this->CI->original_game_logs_model->updateRowsToOriginal('vr_game_report', $data);
                } else {
                    $this->CI->original_game_logs_model->insertRowsToOriginal('vr_game_report', $data);
                }
                $dataCount++;
                unset($data);
            }
        }

        return $dataCount;
    }

    public function generate_afb88_report_daily($date){
        $this->load->model(['original_game_logs_model']);
        $date = date("Y-m-d", strtotime($date));
        $rows = $this->getAfb88DataByDate($date);
        $this->preprocessAfb88Data($rows);
        $MD5_FIELDS_FOR_ORIGINAL =['status'];
        $MD5_FLOAT_AMOUNT_FIELDS =['betting_amount', 'real_betting_amount', 'result_amount'];
        $result['data_count'] = 0;
        if(!empty($rows)){

            list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal('game_provider_report', $rows,
                    'external_unique_id', 'external_unique_id', $MD5_FIELDS_FOR_ORIGINAL, 'md5_sum', 'id', $MD5_FLOAT_AMOUNT_FIELDS);

            unset($rows);
            if (!empty($insertRows)) {
                $result['data_count'] += $this->updateOrInsertAfb88Report($insertRows, 'insert');
            }
            unset($insertRows);
            if (!empty($updateRows)) {
                $result['data_count'] += $this->updateOrInsertAfb88Report($updateRows, 'update');
            }
            unset($updateRows);
        }
        return array(true,$result);
    }

    public function getAfb88DataByDate($date){
        $this->load->model(['original_game_logs_model','game_logs']);
        $game_platform_id = AFB88_API;
        $sqlTime='date(work_date) = ?';
        $sql = <<<EOD
SELECT {$game_platform_id} as game_platform_id,
afb88_game_logs.external_uniqueid as external_unique_id,
afb88_game_logs.bet_amount as betting_amount,
afb88_game_logs.bet_amount as real_betting_amount,
afb88_game_logs.win_amount as result_amount,
afb88_game_logs.status,
afb88_game_logs.work_date as date,
afb88_game_logs.transaction_date as date_time,
afb88_game_logs.md5_sum,
afb88_game_logs.result as status,


game_provider_auth.player_id,

gd.id as game_description_id,
gd.game_type_id,

p.affiliateId as affiliate_id,
p.agent_id,
p.username as player_username,
concat(p.groupname," - ",p.levelname) as player_level,
af.username as affiliate_username,
ag.agent_name

FROM afb88_game_logs as afb88_game_logs
LEFT JOIN game_description as gd ON afb88_game_logs.game_externalid = gd.external_game_id AND gd.game_platform_id = ?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth ON afb88_game_logs.player_name = game_provider_auth.login_name and game_provider_auth.game_provider_id=?
JOIN player as p ON game_provider_auth.player_id = p.playerId
LEFT JOIN affiliates as af ON p.affiliateId = af.affiliateId
LEFT JOIN agency_agents as ag ON p.agent_id = ag.agent_id
WHERE

{$sqlTime}
EOD;
$params=[AFB88_API, AFB88_API,
          $date];
        $result = $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
        return $result;
    }

    public function preprocessAfb88Data(&$rows){
        if(!empty($rows)){
            foreach ($rows as $key => &$row) {
                $row['win_amount'] =  (($row['result_amount'] > 0 ) ? abs($row['result_amount'])  : 0);
                $row['loss_amount'] = (($row['result_amount'] < 0 ) ? abs($row['result_amount'])  : 0);
                $row['status'] = ($row['status'] == 'P') ? Game_logs::STATUS_PENDING : Game_logs::STATUS_SETTLED;
            }
        }
    }

    public function updateOrInsertAfb88Report($rows, $update_type){
        $this->load->model(['original_game_logs_model']);
        $dataCount=0;
        if(!empty($rows)){
            foreach ($rows as $data) {
                //insert or update data to AFB88 API report table database
                if ($update_type=='update') {
                    $data['id']=$data['id'];
                    $this->CI->original_game_logs_model->updateRowsToOriginal('game_provider_report', $data);
                } else {
                    $this->CI->original_game_logs_model->insertRowsToOriginal('game_provider_report', $data);
                }
                $dataCount++;
                unset($data);
            }
        }

        return $dataCount;
    }


}
