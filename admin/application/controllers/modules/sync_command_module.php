<?php

/**
 * Class sync_command_module
 *
 * for command and queue_server
 *
 * General behaviors include :
 *
 * * Sync password/'s
 * * Sync game platforms
 * * Sync NT api balance
 * * Sync PT user
 * * Sync game provider auth
 * * Sync MG api password
 * * Sync IPS to transaction
 * * Fix charset and affiliate count
 * * Sync batch balance
 * * Sync game account
 * * Batch copy of game type and game name
 * * Sync balance in game logs
 * * Sync game table
 * * Rebuild and sync game logs
 * * To Sync Sexy Gaming and King Maker Game History by Transaction Time
 *
 * @category Command Line
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait sync_command_module {

	/**
	 * overview : password sync
	 *
	 * @param int 		$gamePlatformId
	 * @param string	$username
	 */
	public function sync_password($gamePlatformId, $username) {
		//sync password to api
		$api = $this->loadApi($gamePlatformId);
		if ($api) {
			$this->load->model(array('game_provider_auth', 'player_model'));
			$this->load->library(array('salt'));
			$password = $this->player_model->getPasswordByUsername($username);
			if (!empty($password)) {
				$api->changePassword($username, $password, $password);
				$this->returnText('change password ' . $username . ' to ' . $password . "\n");
				$this->utils->debug_log('change password ' . $username . ' to ' . $password);
			} else {
				$this->returnText('empty password ' . $username . "\n");
				$this->utils->debug_log('empty password ' . $username);
			}
		} else {
			$this->returnText('you disabled ' . $gamePlatformId . "\n");
			$this->utils->debug_log('you disabled ' . $gamePlatformId);
		}
	}

	/**
	 * overview : fix affiliate count
     * this is used to sync the countSub field in affiliates table
     * countSub field used to call the parent affiliate
	 */
	 public function fix_affiliate_count() {
	 	$this->load->model(array('affiliatemodel'));
	 	$this->affiliatemodel->startTrans();

	 	$this->affiliatemodel->fixCountOfAll();

	 	$rlt = $this->affiliatemodel->endTransWithSucc();
	 	$msg = $this->utils->debug_log('fix affiliate count result', $rlt);
	 	$this->returnText($msg);
	 }

	/**
	 * overview :batch sync balance all
	 */
	public function batch_sync_balance_all() {

		// if($this->utils->getConfig('turn_off_batch_sync_balance_all')){
			$this->utils->debug_log('turn off batch_sync_balance_all');
			return;
		// }

		// set_time_limit(3600);

		// $manager = $this->utils->loadGameManager();

		// $result = array();

		// $this->load->model(array('player_model', 'wallet_model'));
		// if ($this->utils->getConfig('batch_sync_all_players')) {
		// 	$players = $this->player_model->getAllEnabledPlayers();
		// 	$this->utils->debug_log('get all players', count($players));
		// } else {
		// 	$players = $this->player_model->getAvailBalancePlayerList();
		// 	$this->utils->debug_log('getAvailBalancePlayerList', count($players));
		// }
		// // $this->utils->debug_log("players =========================================> ", $players);
		// $controller = $this;
		// if (!empty($players)) {
		// 	$playerUsernames = array();
		// 	foreach ($players as $row) {
		// 		$playerId = $row->playerId;
		// 		$username = $row->username;
		// 		$playerUsernames[] = $username;
		// 	}
		// 	$this->utils->debug_log("players =========================================> ", $playerUsernames);
		// 	$rlt = $manager->batchQueryPlayerBalanceOnAll($playerUsernames);
		// 	$this->utils->debug_log("rlt =========================================> ", $rlt);

		// 	# After batchQueryPlayerBalance above, some player have their balance updated
		// 	# Only those player needs to record balance history
		// 	if ($this->utils->getConfig('batch_sync_all_players')) {
		// 		$updatedPlayers = $this->player_model->getAllEnabledPlayers();
		// 	} else {
		// 		$updatedPlayers = $this->player_model->getAvailBalancePlayerList();
		// 	}
		// 	$updatedBigWalletsById = array();
		// 	foreach ($updatedPlayers as $row) {
		// 		$updatedBigWalletsById[$row->playerId] = $row->big_wallet;
		// 	}

		// 	foreach ($players as $row) {
		// 		$playerId = $row->playerId;
		// 		$username = $row->username;

		// 		# Skip the accounts whose wallet is not updated
		// 		if($row->big_wallet == $updatedBigWalletsById[$playerId]) {
		// 			$this->utils->debug_log("Skip record balance history for [$username], no change in big wallet");
		// 			continue;
		// 		}

		// 		$success = $this->lockAndTransForPlayerBalance($playerId,
		// 			function () use ($controller, $playerId, $rlt) {
		// 				return $controller->wallet_model->recordPlayerAfterActionWalletBalanceHistory(Wallet_model::BALANCE_ACTION_REFRESH,
		// 					$playerId, null, -1, 0, null, null, null, null, null);
		// 			}
		// 		);
		// 		if (!$success) {
		// 			//only record failed
		// 			$result[$playerId] = $rlt;
		// 		}
		// 	}

		// } else {
		// 	$this->utils->info_log('no players');
		// }

		// $this->utils->debug_log("failed players =========================================> ", $players);
		// $msg = $this->utils->debug_log('count players', count($players), 'failed', count($result), $result);
		// $this->returnText($msg);
	}

	/**
	 * overview : rebuild game logs for last hour
	 *
	 * @param date $endDateTimeStr
	 */
	public function rebuild_game_logs_last_hour($endDateTimeStr = null) {
		if (empty($endDateTimeStr)) {
			$d = new DateTime();
		} else {
			$d = new DateTime($endDateTimeStr);
		}
		return $this->rebuild_game_logs_by_timelimit(1, $d->format('Y-m-d H:00:00'));

	}

	public function rebuild_all_game_logs_by_timelimit($fromDateTimeStr, $endDateTimeStr, $timelimit = 30, $playerName = null) {
		//THERE'S A RULE FOR SYNC, DON'T CHANGE IT
		list($min_datetime, $max_datetime)=$this->utils->getSyncDateRule();

		$this->utils->debug_log('min/max date rule', $min_datetime, $max_datetime);

		if($fromDateTimeStr<$min_datetime){
			//wrong start time
			return $this->utils->error_log('min datetime is:'.$min_datetime);
		}
		if($endDateTimeStr>$max_datetime){
			//wrong datetime
			return $this->utils->error_log('max datetime is:'.$max_datetime);
		}

		return $this->rebuild_all_game_logs_by_timelimit_nolimit($fromDateTimeStr, $endDateTimeStr, $timelimit, $playerName);
	}

	/**
	 * overview : rebuild all game logs by time limit
	 *
	 * @param date	$fromDateTimeStr
	 * @param date	$endDateTimeStr
	 * @param int	$timelimit
	 * @param string 	$playerName
	 * @param string 	$sync_total
	 */
	public function rebuild_all_game_logs_by_timelimit_nolimit($fromDateTimeStr, $endDateTimeStr, $timelimit = 30, $playerName = '_null', $sync_total='true') {

		if($this->utils->getConfig('disabled_manually_sync_game_logs')){
			$this->utils->error_log('=========donnot allow sync manually game logs============================');
			return false;
		}

		$apis = $this->utils->getAllCurrentGameSystemList();
		$sync_total=$sync_total=='true';
		if($playerName=='_null'){
			$playerName=null;
		}
		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		$msg = $this->utils->debug_log('=========start rebuild_all_game_logs_by_timelimit============================',
			'fromDateTimeStr', $fromDateTimeStr, 'endDateTimeStr', $endDateTimeStr, 'playerName', $playerName, 'timelimit', $timelimit);
		// $this->returnText($msg);
		$mark = 'rebuild_all_game_logs_by_timelimit';
		$this->utils->markProfilerStart($mark);

		$dateTimeFrom = new \DateTime($fromDateTimeStr);
		$dateTimeTo = new \DateTime($endDateTimeStr);
		// list($todayFrom, $todayTo) = $this->utils->getTodayDateTimeRange();
		//set default datetime from to
		// if (empty($dateTimeFromStr)) {
		// 	$dateTimeFrom = $todayFrom;
		// }
		// if (empty($dateTimeToStr)) {
		// 	$dateTimeTo = $todayTo;
		// }

		$manager = $this->utils->loadGameManager();

		$from = $dateTimeFrom;
		while (!$this->utils->gtAndEqEndTime($from, $dateTimeTo)) {
			$to = $this->utils->getNextTime($from, '+' . $timelimit . ' minutes');
			$msg = $this->utils->debug_log('from', $from, 'to', $to, 'timelimit', $timelimit);
			$this->returnText($msg);
			// foreach ($apis as $gamePlatformId) {
			$ignore_public_sync = true;
			$manager->syncGameRecordsOnAllPlatforms($from, $to, $playerName, null, $ignore_public_sync, $sync_total);
			// }
			$from = $to;
		}

		$msg = $this->utils->markProfilerEndAndPrint($mark);
		// $this->returnText($msg);

		$msg = $this->utils->debug_log('=========end rebuild_all_game_logs_by_timelimit============================');
		// $this->returnText($msg);
	}

	/**
	 * overview : rebuild game logs for last hour without total
	 *
	 */
	public function rebuild_game_logs_last_2hours_without_totals() {
		$d = new DateTime();
		$hours=2;
		$this->rebuild_game_logs_by_timelimit($hours, $d->format('Y-m-d H:00:00'), 30, '_null', 'false');

		$dateTimeTo=new DateTime($d->format('Y-m-d H:00:00'));
		$dateTimeFrom = clone $dateTimeTo;
		$dateTimeFrom->modify('-' . $hours . ' hours');

		//try rebuild totals
		$this->rebuild_totals($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo->format('Y-m-d H:i:s'),
			'true', 'true');
	}

	/**
	 * overview : rebuild game logs by time limit
	 *
	 * @param string $hours
	 * @param date $endDateTimeStr
	 * @param date $timelimit
	 * @param string $playerName
	 */
	public function rebuild_game_logs_by_timelimit($hours = '24', $endDateTimeStr = '_null', $timelimit = 30, $playerName = '_null', $syncTotal='true') {
		if($endDateTimeStr=='_null'){
			$endDateTimeStr=null;
		}
		//for all game platform
		if (empty($endDateTimeStr)) {
			$dateTimeTo = new DateTime();
		} else {
			$dateTimeTo = new DateTime($endDateTimeStr);
		}
		if($playerName=='_null'){
			$playerName=null;
		}
		$syncTotal=$syncTotal=='true';

		$dateTimeFrom = clone $dateTimeTo;
		$dateTimeFrom->modify('-' . $hours . ' hours');

		$apis = $this->utils->getAllCurrentGameSystemList();

		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		$msg = $this->utils->debug_log('=========start rebuild_game_logs_by_timelimit============================', 'hours', $hours, 'endDateTimeStr', $endDateTimeStr, 'playerName', $playerName, 'timelimit', $timelimit);

		$this->utils->sendMessageService($msg);

		// $this->returnText($msg);
		$mark = 'rebuild_game_logs_by_timelimit';
		$this->utils->markProfilerStart($mark);

		// $dateTimeFrom = new \DateTime($dateTimeFromStr);
		// $dateTimeTo = new \DateTime($dateTimeToStr);
		// list($todayFrom, $todayTo) = $this->utils->getTodayDateTimeRange();
		//set default datetime from to
		// if (empty($dateTimeFromStr)) {
		// 	$dateTimeFrom = $todayFrom;
		// }
		// if (empty($dateTimeToStr)) {
		// 	$dateTimeTo = $todayTo;
		// }

		$sync_result=[];

		$manager = $this->utils->loadGameManager();

		$from = $dateTimeFrom;
		while (!$this->utils->gtAndEqEndTime($from, $dateTimeTo)) {
			$to = $this->utils->getNextTime($from, '+' . $timelimit . ' minutes');
			$msg = $this->utils->debug_log('from', $from, 'to', $to, 'timelimit', $timelimit);
			// $this->returnText($msg);
			// foreach ($apis as $gamePlatformId) {
			$ignore_public_sync = true;
			$sync_result_time=$manager->syncGameRecordsOnAllPlatforms($from, $to, $playerName, null, $ignore_public_sync, $syncTotal);
			$sync_result[]=['from'=>$from->format('Y-m-d H:i:s'), 'to'=>$to->format('Y-m-d H:i:s'),
				'result'=>$sync_result_time];
			// }
			$from = $to;

			// $this->utils->debug_log($sync_result);
		}

		$this->utils->markProfilerEndAndPrint($mark);
		// $this->returnText($msg);

		$this->utils->debug_log($sync_result);
		//send to mattermost
		$this->utils->sendMessageService($sync_result);

		$msg = $this->utils->debug_log('=========end rebuild_game_logs_by_timelimit============================', $dateTimeTo, $dateTimeFrom);
		$this->utils->sendMessageService($msg);
		// $this->returnText($msg);

	}


	public function rebuild_single_game_by_timelimit($gamePlatformId, $fromDateTimeStr, $endDateTimeStr, $timelimit = 30, $playerName = null) {
		//THERE'S A RULE FOR SYNC, DON'T CHANGE IT
		list($min_datetime, $max_datetime)=$this->utils->getSyncDateRule();

		$this->utils->debug_log('min/max date rule', $min_datetime, $max_datetime);

		if($fromDateTimeStr<$min_datetime){
			//wrong start time
			$this->utils->info_log('!!!!!!!!!!!!!ERROR, min datetime is:'.$min_datetime, 'from date time is', $fromDateTimeStr);
			return $this->utils->error_log('!!!!!!!!!!!!!ERROR, min datetime is:'.$min_datetime, 'from date time is', $fromDateTimeStr);
		}
		if($endDateTimeStr>$max_datetime){
			//wrong datetime
			$this->utils->info_log('!!!!!!!!!!!!!ERROR, max datetime is:'.$max_datetime, 'end date time is', $endDateTimeStr);
			return $this->utils->error_log('!!!!!!!!!!!!!ERROR, max datetime is:'.$max_datetime, 'end date time is', $endDateTimeStr);
		}
		return $this->rebuild_single_game_by_timelimit_nolimit($gamePlatformId, $fromDateTimeStr, $endDateTimeStr, $timelimit, $playerName);
	}

	/**
	 * overview : rebuild all game logs by time limit
	 *
	 * @param string	$fromDateTimeStr
	 * @param string	$endDateTimeStr
	 * @param string	$timelimit
	 * @param string 	$playerName
	 * @param string 	$sync_total
	 */
	public function rebuild_single_game_by_timelimit_nolimit($gamePlatformId, $fromDateTimeStr, $endDateTimeStr, $timelimit = 30, $playerName = '_null', $sync_total='true') {

		if($this->utils->getConfig('disabled_manually_sync_game_logs')){
			$this->utils->error_log('=========donnot allow sync manually game logs============================');
			return false;
		}

		$apis = $this->utils->getAllCurrentGameSystemList();
		$sync_total=$sync_total=='true';
		if($playerName=='_null'){
			$playerName=null;
		}
		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		$this->utils->debug_log('=========start rebuild_single_game_by_timelimit============================',
			'gamePlatformId', $gamePlatformId, 'fromDateTimeStr', $fromDateTimeStr, 'endDateTimeStr', $endDateTimeStr, 'playerName', $playerName, 'timelimit', $timelimit);
		// $this->returnText($msg);
		$mark = 'rebuild_single_game_by_timelimit';
		$this->utils->markProfilerStart($mark);

		$dateTimeFrom = new \DateTime($fromDateTimeStr);
		$dateTimeTo = new \DateTime($endDateTimeStr);
		// list($todayFrom, $todayTo) = $this->utils->getTodayDateTimeRange();
		//set default datetime from to
		// if (empty($dateTimeFromStr)) {
		// 	$dateTimeFrom = $todayFrom;
		// }
		// if (empty($dateTimeToStr)) {
		// 	$dateTimeTo = $todayTo;
		// }

		$manager = $this->utils->loadGameManager();

		$from = $dateTimeFrom;
		$this->utils->info_log('init date time', $from, $dateTimeTo, $this->utils->gtAndEqEndTime($from, $dateTimeTo));
		while (!$this->utils->gtAndEqEndTime($from, $dateTimeTo)) {
			$to = $this->utils->getNextTime($from, '+' . $timelimit . ' minutes');
			$this->utils->debug_log('from', $from, 'to', $to, 'timelimit', $timelimit, round(memory_get_usage()/(1024*1024), 2));
			// $this->returnText($msg);
			// foreach ($apis as $gamePlatformId) {
			$ignore_public_sync = true;
			$manager->syncOneGameRecords($gamePlatformId, $from, $to, $playerName, null, $ignore_public_sync, $sync_total);
			// }
			$from = $to;
		}

		$this->utils->markProfilerEndAndPrint($mark);
		// $this->returnText($msg);

		$this->utils->debug_log('=========end rebuild_single_game_by_timelimit============================');
		// $this->returnText($msg);
	}

	/**
	 *
	 * private function to run shell
	 *
	 * @param  boolean $dry_run
	 * @param  string  $dateTimeFromStr
	 * @param  string  $dateTimeToStr
	 * @param  int  $game_api_id
	 * @param  string  $playerName
	 * @param  string  $token
	 * @param  boolean $merge_only
	 * @param  boolean $only_original
	 * @return int
	 */
	private function run_cmd_sync_all_game($dry_run=true, $dateTimeFromStr=null, $dateTimeToStr=null, $game_api_id=null,
			$playerName=null, $token=null, $merge_only=false, $only_original=false){

		// $dry_run=$dry_run=='true';

		$this->load->model(['queue_result']);

		$game_api_id=intval($game_api_id);
		// $og_admin_home = realpath(dirname(__FILE__) . "/../../../");

		$apis = $this->utils->getAllCurrentGameSystemList();
		if(!empty($game_api_id)){
			$apis=[$game_api_id];
		}

		$api_str=implode(' ', $apis);

		if(empty($playerName)){
			$playerName='_null';
		}

		$params_str='"'.$dateTimeFromStr.'" "'.$dateTimeToStr.'" "'.$playerName.'"'.' "'.$token.'"';
		$og_admin_home = realpath(dirname(__FILE__) . "/../../../");

		$php_str=$this->utils->find_out_php();
		$noroot_command_shell='';
		if($merge_only){

			$noroot_command_shell=<<<EOD
#!/bin/bash

echo "start `date`"

echo "{$api_str}"

echo "{$params_str}"

for i in {$api_str} ;
do

	echo '{$php_str} {$og_admin_home}/shell/ci_cli.php cli/sync_game_records/sync_merge_game_logs "\$i" {$params_str}'

	{$php_str} {$og_admin_home}/shell/ci_cli.php cli/sync_game_records/sync_merge_game_logs "\$i" {$params_str}

done

echo "done `date`"
EOD;

		}else if($only_original){

			$noroot_command_shell=<<<EOD
#!/bin/bash

echo "start `date`"

echo "{$api_str}"

echo "{$params_str}"

for i in {$api_str} ; do {

	# sleep 1
	echo '{$php_str} {$og_admin_home}/shell/ci_cli.php cli/sync_game_records/sync_game_logs_no_merge \$i {$params_str}'

	{$php_str} {$og_admin_home}/shell/ci_cli.php cli/sync_game_records/sync_game_logs_no_merge "\$i" {$params_str}

} & done

wait

echo "done `date`"
EOD;

		}else{

			$noroot_command_shell=<<<EOD
#!/bin/bash

echo "start `date`"

echo "{$api_str}"

echo "{$params_str}"

for i in {$api_str} ;
do

	echo '{$php_str} {$og_admin_home}/shell/ci_cli.php cli/sync_game_records/sync_game_logs_with_total \$i {$params_str}'

	{$php_str} {$og_admin_home}/shell/ci_cli.php cli/sync_game_records/sync_game_logs_with_total \$i {$params_str}

done

echo "done `date`"
EOD;

		}

		// $cmd = 'bash '.$og_admin_home . '/shell/noroot_sync_game_logs.sh "' . implode(' ', $apis) . '" "' . $dateTimeFromStr . '" "' . $dateTimeToStr . '" "' . $playerName . '"';

		//app log
		$tmp_dir='/tmp/'.$this->_app_prefix;
		if(!file_exists($tmp_dir)){
			@mkdir($tmp_dir, 0777 , true);
		}

		$tmp_shell=$tmp_dir.'/sync_game_logs_'.random_string('md5').'.sh';
		file_put_contents($tmp_shell, $noroot_command_shell);

		$this->utils->debug_log('write shell', $tmp_shell, $noroot_command_shell);

		$cmd='bash '.$tmp_shell;
		$this->utils->debug_log('start sync', $cmd);

		$str=$output='';

		$return_var=0;

		if(!empty($token)){
			$this->utils->debug_log('check /system_management/common_queue/'.$token);
			$this->queue_result->appendResult($token, ['request_id'=>_REQUEST_ID,
				'func'=>'run_cmd_sync_all_game',
				'script'=>$tmp_shell,
				'dateTimeFromStr'=>$dateTimeFromStr,
				'dateTimeToStr'=>$dateTimeToStr,
				'game_api_id'=>$game_api_id, 'playerName'=>$playerName,
				'merge_only'=>$merge_only, 'only_original'=>$only_original,
				'log_file'=>'sync_game_logs.log',
			]);
		}

		if(!$dry_run){
			$t=time();
			$str = exec($cmd, $output, $return_var);
			$this->utils->debug_log('exec time', $t, time(), (time()-$t));
			if(time()-$t>60){
				$this->utils->debug_log('reset db');
				$this->db->reconnect();
				$this->db->initialize();
			}
		}

		if(!empty($token)){
			$this->queue_result->appendResult($token, ['request_id'=>_REQUEST_ID,
				'func'=>'run_cmd_sync_all_game',
				'result'=>$str,
			]);
		}

		//delete shell
		unlink($tmp_shell);
		$this->utils->debug_log('delete tmp shell: '.$tmp_shell);

		$this->utils->debug_log("run_cmd_sync_all_game done exec ", $str, 'return: '.$return_var);

		unset($output);
		unset($noroot_command_shell);

		return $return_var;
	}

	/**
	 * queue server or command
	 * @param  string  $dry_run
	 * @param  string  $fromDateTimeStr
	 * @param  string  $toDateTimeStr
	 * @param  string  $game_api_id
	 * @param  integer $timelimit
	 * @param  string  $playerName
	 * @param  string  $merge_only
	 * @param  string  $only_original
	 * @param  string  $token
	 * @return bool
	 */
	public function sync_game_logs($dry_run='true', $fromDateTimeStr, $toDateTimeStr, $game_api_id=_COMMAND_LINE_NULL,
		$timelimit = 60, $playerName = _COMMAND_LINE_NULL, $merge_only='false', $only_original='false', $token=_COMMAND_LINE_NULL) {

		$success=true;

		$this->load->library(['language_function']);
		$this->load->model(['queue_result']);
		$lang=$this->language_function->getCurrentLanguage();
		if($game_api_id==_COMMAND_LINE_NULL){
			$game_api_id=null;
		}
		$game_api_id=intval($game_api_id);
		$merge_only=$merge_only=='true';
		$only_original=$only_original=='true';
		$dry_run=$dry_run=='true';
		if($playerName==_COMMAND_LINE_NULL){
			$playerName=null;
		}
		if($token==_COMMAND_LINE_NULL){
			$token=null;
		}

		// $default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit(0);

		$this->utils->debug_log('=========start sync_game_logs============================',
			'fromDateTimeStr', $fromDateTimeStr, 'toDateTimeStr', $toDateTimeStr, 'playerName', $playerName, 'timelimit', $timelimit, 'dry_run', $dry_run);
		// $this->returnText($msg);
		$mark = 'sync_game_logs';
		$this->utils->markProfilerStart($mark);

		if($toDateTimeStr>$this->utils->getNowForMysql()){
			$toDateTimeStr=$this->utils->getNowForMysql();
		}

		$step='+'.$timelimit.' minutes';

		if(empty($token)){
			$params = [
				'request_id'=>_REQUEST_ID,
				'log_file'=>'sync_game_logs.log',
				'toDateTimeStr' => $toDateTimeStr,
				'fromDateTimeStr' => $fromDateTimeStr,
				'game_api_id' => $game_api_id,
				'timelimit' => $timelimit,
				'playerName' => $playerName,
				'dry_run'=>$dry_run,
				'merge_only'=>$merge_only,
				'only_original'=>$only_original,
			];
			$funcName='sync_game_logs';
			$caller=0;
			$callerType=Queue_result::CALLER_TYPE_SYSTEM;
			$state=null;
			$token=$this->createQueueOnCommand($funcName, $params,
				$lang , $callerType, $caller, $state);
		}
		$this->queue_result->updateResultRunning($token,[],array('processId'=>getmypid()));
		// $token=null;
		$dateList=[];
		$manager = $this->utils->loadGameManager();
    	$success=$this->utils->loopDateTimeStartEnd($fromDateTimeStr, $toDateTimeStr, $step, function($from, $to, $step)
    			use(&$dateList, $manager, $playerName, $game_api_id, $dry_run, $token, $merge_only, $only_original){

    		$this->utils->debug_log($from, $to, $game_api_id, $step);

			// $ignore_public_sync = true;
			$return_var=$this->run_cmd_sync_all_game($dry_run, $this->utils->formatDateTimeForMysql($from), $this->utils->formatDateTimeForMysql($to),
				$game_api_id, $playerName, $token, $merge_only, $only_original);

			$success=true; // $return_var==0;

    		$dateList[]=['from'=>$from, 'to'=>$to, 'success'=>$success];

    		return $success;
    	});

    	if($success){
			$this->utils->debug_log('queue: '.$token.' done');
    		//done
    		$this->queue_result->appendResult($token, ['request_id'=>_REQUEST_ID, 'result'=> $success ], true, false);
    	}else{
			$this->utils->error_log('queue: '.$token.' failed');
    		//error
    		$this->queue_result->appendResult($token, ['request_id'=>_REQUEST_ID, 'result'=> $success ], false, true);
    	}

		$this->utils->markProfilerEndAndPrint($mark);

		// $this->returnText($msg);
		//print queue result
		$this->utils->debug_log('=========end sync_game_logs============================', $dateList);
		// $this->returnText($msg);

		return $success;
	}

	public function createQueueOnCommand($funcName, $params,
		$lang , $callerType, $caller, $state){

		$systemId = Queue_result::SYSTEM_UNKNOWN;
		// $funcName = 'sync_game_logs';
		// $params = ['endDateTimeStr' => $endDateTimeStr,
		// 	'fromDateTimeStr' => $fromDateTimeStr,
		// 	'game_api_id' => $game_api_id,
		// 	'timelimit' => $timelimit,
		// 	'playerName' => $playerName,
		// 	'dry_run'=>$dry_run,
		// 	'merge_only'=>$merge_only];
		$token = $this->queue_result->newResult($systemId,
			$funcName, $params, $callerType, $caller, $state, $lang);

		return $token;
	}

	// public function scan_all_game_accounts($max_id=null) {
	// 	$this->load->model(array('game_provider_auth', 'player_model'));

	// 	$rows = $this->game_provider_auth->getAllGameAccounts($max_id);
	// 	$cnt = 0;
	// 	$failedCnt = 0;
	// 	foreach ($rows as $row) {
	// 		//check exists
	// 		$api = $this->utils->loadExternalSystemLibObject($row->game_provider_id);
	// 		$username = $this->player_model->getUsernameById($row->player_id);
	// 		$rlt = $api->isPlayerExist($username);

	// 		if (!$rlt['success']) {
	// 			$this->utils->error_log('===========search player failed', $username, 'api', $row->game_provider_id);
	// 			$failedCnt++;
	// 		}
	// 		if($rlt['success']){
	// 			if (isset($rlt['exists'])){
	// 				if($rlt['exists']===false) {
	// 					$this->utils->debug_log('===========player NOT exists', $username, 'api', $row->game_provider_id);
	// 					//set register
	// 					$this->game_provider_auth->setRegisterFlag($row->player_id, $row->game_provider_id,
	// 						Game_provider_auth::DB_FALSE);
	// 				}else if($rlt['exists']===true) {
	// 					$this->utils->debug_log('===========player exists', $username, 'api', $row->game_provider_id);
	// 					$this->game_provider_auth->setRegisterFlag($row->player_id, $row->game_provider_id,
	// 						Game_provider_auth::DB_TRUE);
	// 				}
	// 			}else{
	// 				$this->utils->debug_log('===========player exists UNKNOWN', $username, 'api', $row->game_provider_id);
	// 			}
	// 		}

	// 		$this->utils->debug_log('processed game account: '.$row->id);

	// 		$cnt++;
	// 	}

	// 	$this->utils->debug_log("scan_all_game_accounts is success", 'cnt', $cnt, 'failedCnt', $failedCnt);
	// 	// $this->returnText($msg);
	// }


	public function query_one_api_balance($username, $apiId){

		$api = $this->loadApi($apiId);
		if ($api) {
			$rlt=$api->queryPlayerBalance($username);

			$this->utils->debug_log('query_one_api_balance', $rlt);
		}else{
			$this->utils->error_log('query_one_api_balance load api failed', $username, $apiId);
		}

	}

	public function query_one_api_exists($username, $apiId){

		$api = $this->loadApi($apiId);
		if ($api) {
			$rlt=$api->isPlayerExist($username);

			$this->utils->debug_log('query_one_api_exists', $rlt);
		}else{
			$this->utils->error_log('query_one_api_exists load api failed', $username, $apiId);
		}

	}

	public function create_player_on_one_api($username, $apiId){

		$api = $this->loadApi($apiId);
		if ($api) {
			$this->load->model(['player_model']);
			$player=$this->player_model->getPlayerByUsername($username);
			$password=$this->utils->decodePassword($player->password);

			$rlt=$api->createPlayer($username, $player->playerId, $password);

			$this->utils->debug_log('create_player_on_one_api', $rlt);
		}else{
			$this->utils->error_log('create_player_on_one_api load api failed', $username, $apiId);
		}

	}

    public function sync_game_list_daily(){
        $this->sync_game_list(true);
    }

    public function sync_new_games(){
        $this->sync_game_list();
    }

    public function sync_game_list($update_game_list = false, $game_platform_id = null, $force_game_list_update = null){

    	$exempted_game_type_codes_for_sync = ['yoplay','tip'];
    	if (!empty($this->utils->getConfig('exempted_game_type_codes_for_sync')))
    		$exempted_game_type_codes_for_sync = $this->utils->getConfig('exempted_game_type_codes_for_sync');

        $this->load->model(['game_description_model','game_type_model']);

        $url = $this->utils->getConfig("game_list_api_url");

        $game_apis = $this->external_system->getAllActiveSytemGameApi();

        if ($game_platform_id && ! in_array($game_platform_id, ['null','false'])) {
        	$available_game_providers = array_column($game_apis, 'id');
	        $game_platform_id = (int) $game_platform_id;
        	$game_apis = ['game_platform'=>['id'=>$game_platform_id]];

        	if ( ! in_array($game_platform_id, $available_game_providers)) {
	        	echo "Error: ============= Game platform ID Not found ============";exit;
        	}
        }

        $keys_to_unset = ['id','game_tag_id','game_type_code','game_type','created_at','updated_at','md5_fields'];
        $game_tags = $this->game_type_model->getAllGameTags();
        $standard_game_type_codes = array_column($game_tags, 'tag_code');
        // $standard_game_type_names = array_column($game_tags, 'tag_name');

        foreach ($game_apis as $key => $row) {
            $game_map = $local_game_keys = [];

            $current_url = $url . "/game_description/getAllGames/". $row['id'];

            $game_gateway_games = file_get_contents($current_url);
            $game_gateway_games_map = json_decode($game_gateway_games,true);

            $api = $this->utils->loadExternalSystemLibObject($row['id']);
            if (empty($api)) continue;

            $local_games = $api->getGameList(null, null,null,null,true);
            $local_game_keys = array_column($local_games, "external_game_id");

            if (empty($game_gateway_games_map)) continue;

            $this->utils->debug_log("sync_current_games ==========>" . $row['id']);

            foreach ($game_gateway_games_map as $key => $game) {

                #per game preparation
                $game['game_type_id'] = $this->game_type_model->getGameTypeId($game['game_type'],$game['game_platform_id']);

                if (empty($game['game_platform_id'])) continue;

                #don't sync if game type is not standard
                if (!in_array($game['game_type_code'], $exempted_game_type_codes_for_sync)) {
                	if (!in_array($game['game_type_code'], $standard_game_type_codes)) continue;
                	// if (!in_array($game['game_type'], $standard_game_type_codes)) continue;
                }

                if(empty($game['game_type_id'])){
                    $game['game_type_id'] = $this->game_type_model->checkGameType($game['game_platform_id'],$game['game_type'],$game);
                }

                foreach ($keys_to_unset as $key) unset($game[$key]);
                #end, ready for game synchronizing

                if ( ! empty($update_game_list)) {
                    array_push($game_map, $game);
                }else{
                    if( ! array_search($game['external_game_id'],$local_game_keys)){
                        #for new games
                        if (empty($update_game_list)) {
                            $game['flag_new_game'] = true;
                            array_push($game_map, $game);
                        }
                    }
                }
            }

            $extra['force_game_list_update'] = $force_game_list_update;
            $this->game_description_model->syncGameDescription($game_map,true,true,null,null,$extra);
            unset($game_gateway_games);
			unset($game_gateway_games_map);
        }
    }

    public function sync_game_list_by_platform_id($update_game_list = false, $game_platform_id = null){

        $this->load->model(['game_description_model','game_type_model']);

        $url = $this->utils->getConfig("game_list_api_url");
		$gameapis = $this->external_system->getAllActiveSytemGameApi();

		$game_provider_id = $this->filter_sub_game_api($game_platform_id);

        if ($game_platform_id && ! in_array($game_platform_id, ['null','false'])) {
        	$available_game_providers = array_column($gameapis, 'id');
	        $game_platform_id = (int) $game_platform_id;
        	$gameapis = ['game_platform'=>['id'=>$game_platform_id]];

        	if ( ! in_array($game_platform_id, $available_game_providers)) {
	        	echo "Error: ============= Game platform ID Not found ============";exit;
        	}
        }

        foreach ($gameapis as $key => $row) {
            $current_url = $url . "/game_description/getAllGames/". $game_provider_id;
            $game_gateway_games = file_get_contents($current_url);
            $game_gateway_games_map = json_decode($game_gateway_games,true);

            $api = $this->utils->loadExternalSystemLibObject($row['id']);
            $game_map = [];
            $local_game_keys = [];

            $local_games = $api->getGameList(null, null,null,null,true);
            $local_game_keys = array_column($local_games, "external_game_id");

            if (empty($game_gateway_games_map)) continue;

            $this->utils->debug_log("sync_current_games ==========>" . $row['id']);

            foreach ($game_gateway_games_map as $key => $game) {
                #per game preparation
                $game['game_type_id'] = $this->game_type_model->getGameTypeId($game['game_type'],$game_provider_id);

                if(empty($game['game_type_id'])){
                    $game['game_type_id'] = $this->game_type_model->checkGameTypePerPlatform($game_provider_id,$game['game_type'],$game);
                }

                unset($game['id'],$game['game_tag_id'],$game['game_type_code'],$game['game_type']);
                #end, ready for game synchronizing

                if ( ! empty($update_game_list)) {
					$game['game_platform_id'] = $game_platform_id;
                    array_push($game_map, $game);
                }else{
                    if( ! array_search($game['external_game_id'],$local_game_keys)){
                        #for new games
                        if (empty($update_game_list)) {
                            $game['flag_new_game'] = true;
                        $game['game_platform_id'] = $game_platform_id;
                            array_push($game_map, $game);
                        }
                    }
                }
            }

            $this->game_description_model->syncGameDescription($game_map,true,true);
            unset($game_gateway_games);
			unset($game_gateway_games_map);
        }
    }

    /*
     * This function filters sub game api if sub not found will return original game_platform_id
     * @param int $game_platform_id
     */
    private function filter_sub_game_api($game_platform_id)
    {
		switch($game_platform_id){
			case T1KYCARD_API:
				return KYCARD_API;
				break;
			case T1MWG_API:
				return MWG_API;
				break;
			case T1ONEWORKS_API:
				return ONEWORKS_API;
				break;
			default:
    			return $game_platform_id;
    			break;
		}
	}

    /**
     * [sync_game_list_through_api
     * 	- Get latest game list from Game provider API
     * 	- Notify the Game list Update channel in MM about the game list update
     * ]
     * @return [type] [description]
     */
     public function sync_game_list_through_api($game_platform_id = null){
	    $this->load->helper('mattermost_notification_helper');
	    $this->load->model('game_type_model');

    	$game_provider_with_game_list_api = $this->utils->getConfig('game_provider_with_game_list_api');

    	if(!empty($game_platform_id) && isset($game_provider_with_game_list_api[$game_platform_id])) {
    		$game_provider_with_game_list_api = [$game_platform_id=>$game_provider_with_game_list_api[$game_platform_id]];
    	}

	    $app_prefix = str_replace("-", "_", $this->utils->getAppPrefix());
    	$game_apis = $this->external_system->getAllActiveSytemGameApi();
        $game_api_ids = array_column($game_apis, 'id');

	    $this->switch_id_to_key($game_apis);
        foreach ($game_provider_with_game_list_api as $defined_api_number => $config) {
	    	$gameText = $message = null;
	    	$this->utils->debug_log('game_provider_with_game_list_api =============>',$config);

    		if (in_array($defined_api_number, $game_api_ids)) {
	            $api = $this->utils->loadExternalSystemLibObject($defined_api_number);
	            $result = $api->getGameProviderGameList();
	            if (isset($result['Counts']) && array_sum($result['Counts']) == 0) continue;

	            #get game type
	            $notif_message = $game_type_id_list = $game_type_id_inserted = $game_type_id_updated = [];
	            if (isset($result['list_of_games']['inserted_games']))
	             	$game_type_id_inserted = array_unique(array_column($result['list_of_games']['inserted_games'], 'game_type_id'));

	            if (isset($result['list_of_games']['updated_games']))
	             	$game_type_id_updated = array_unique(array_column($result['list_of_games']['updated_games'], 'game_type_id'));

	            $game_type_id_list = array_unique(array_merge($game_type_id_inserted,$game_type_id_updated));
	            $game_type_id_list = implode(',', $game_type_id_list);
	            $this->game_type_list = $this->game_type_model->getGameTypeByQuery('*','id IN ('.$game_type_id_list . ')');

	            $this->switch_id_to_key($this->game_type_list);
	            #end

	            $this->utils->debug_log('list_of_games =============>',$result['Counts']);
	            $message_inserted = $message_updated = $message_info = null;
			    $game_provider_name = str_replace("_API", "", $game_apis[$defined_api_number]['system_name']);
	            foreach ($result['list_of_games'] as $key => $games) {
	            	if (empty($games)) continue;
			    	$message_info = "### #". $game_provider_name . $this->utils->getDatetimeNow()."\n Hostname: #". $this->utils->getHostname().", App prefix: #".$app_prefix."\n";

			    	if ($key == "inserted_games") {
			    		$message_inserted .= $this->prepare_text_for_mmnotif($games,'Inserted',$game_provider_name);
			    	}
			    	if ($key == "updated_games") {
			    		$message_updated .= $this->prepare_text_for_mmnotif($games,'Updated',$game_provider_name);
			    	}
			    }

			    if (!empty($message_inserted))
			    	$message = $message_inserted;

			    if (empty($message_inserted) && !empty($message_updated))
			    	$message = $message_updated;

			    if (!empty($message_inserted) && !empty($message_updated))
			    	$message = $message_inserted .= "\n\n".$message_updated;

		    	$message_arr['text'] = $message;
		    	$message_arr['type'] = 'info';

		    	array_push($notif_message, $message_arr);
    		}

    		if ($message)
				sendNotificationToMattermost('Game List Update','game_list_update', $notif_message, $message_info);
        }

    }

    private function prepare_text_for_mmnotif($games,$type,$game_provider_name){
		$message = $game_provider_name . " " .$type. " Games: ".$this->utils->getCurrentDatetime()."\n\n";
		$message .= "|Game Name|Chinese Name| Game Code| Game Type| \n |---| \n";
		foreach ($games as $key => $gameDetail) {
			$game_name_arr = json_decode(str_replace("_json:", "", $gameDetail['game_name']),true);
			$message .= "|".lang($gameDetail['game_name'])."|".$game_name_arr[2]."|".$gameDetail['game_code'] . "|".lang($this->game_type_list[$gameDetail['game_type_id']]['game_type'])."|\n";
		}
		return $message;
    }

    private function switch_id_to_key(&$data){
    	$temp_data = [];
    	foreach ($data as $key => $details) {
        	$temp_data[$details['id']] = $details;
        }

        $data = $temp_data;
        unset($temp_data);
    }

    /**
	 * overview : rebuild all game result logs by time limit
	 *
	 * @param date	$fromDateTimeSt
	 * @param date	$endDateTimeStr
	 * @param int	$timelimit
	 * @param null 	$playerName
	 */
	public function rebuild_single_game_result_logs($gamePlatformId, $fromDateTimeSt, $endDateTimeStr, $playerName = null) {

		$apis = $this->utils->getAllCurrentGameSystemList();

		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		$msg = $this->utils->debug_log('=========start rebuild_single_game_result_logs============================',
			'fromDateTimeSt', $fromDateTimeSt, 'endDateTimeStr', $endDateTimeStr, 'playerName', $playerName);
		$this->returnText($msg);
		$mark = 'rebuild_single_game_result_logs';
		$this->utils->markProfilerStart($mark);

		$dateTimeFrom = new \DateTime($fromDateTimeSt);
		$dateTimeTo = new \DateTime($endDateTimeStr);

		$ignore_public_sync = true;
		$manager = $this->utils->loadGameManager();
		$manager->syncGameResultRecordsOnOnePlatform($gamePlatformId, $dateTimeFrom, $dateTimeTo, $playerName, null, $ignore_public_sync);

		$msg = $this->utils->markProfilerEndAndPrint($mark);
		$this->returnText($msg);

		$msg = $this->utils->debug_log('=========end rebuild_single_game_result_logs============================');
		$this->returnText($msg);
	}

    /*
        Note: Don't execute if game description table is not backuped up to now
     */
    public function fix_duplicate_games($game_platform_id = false, $delete_game = null, $start_date = null , $end_date = null){
        $this->load->model(['game_description_model','game_type_model']);

        $game_platform_id = ($game_platform_id == "false" || $game_platform_id == "null") ? null:$game_platform_id;
        $delete_game = ($delete_game == "false" || $delete_game == "null") ? null:$delete_game;

        $data = $this->game_description_model->getDuplicateGames($game_platform_id);

        if (!empty($data)) {
            $isTableCreated = $this->game_description_model->backupGameDescriptionTable();

            $game_count = 0;
            $affected_game_logs_per_game_platform = [];
            if (!empty($isTableCreated)) {
                foreach ($data as $key => $game_detail) {
                    $affected_game_logs_row = 0;

                    $game_count+=$game_detail['game_count'];
                    if (empty($game_detail['game_code']) && empty($game_detail['external_game_id'])) {
                        continue;
                    }elseif (empty($game_detail['external_game_id'])) {
                        $external_game_id = 'game_code = "'.$game_detail['game_code'] .'"';
                    }else{
                        $external_game_id = 'external_game_id ="'.$game_detail['external_game_id'] .'"';
                    }

                    $query = 'id,external_game_id,game_name,game_platform_id';
                    $where = 'game_platform_id = '. $game_detail['game_platform_id'] . ' and '. $external_game_id;

                    $current_duplicate_game_data = $this->game_description_model->getGameByQuery($query,$where);
                    $game_ids = [];
                    $duplicate_game_details = [];

                    foreach ($current_duplicate_game_data as $key => $value) {
                        $duplicate_count = $this->game_description_model->getGameLogsCountPerGame($value['id']);
                        $affected_game_logs_row+=$duplicate_count;
                        $duplicate_game_details['external_game_id'] = $game_detail['external_game_id'] . '<->' . $game_detail['game_code'];
                        $duplicate_game_details['game_platform_id'] = $value['game_platform_id'];
                        $duplicate_game_details['game_logs_count_per_game_id'][$value['id']] = $duplicate_count;
                        $duplicate_game_details['duplicate_game_ids'][$value['id']] = $value['id'];
                        $duplicate_game_details['min_game_id'] = min($duplicate_game_details['duplicate_game_ids']);
                        $duplicate_game_details['duplicate_game_count'] = count($duplicate_game_details['duplicate_game_ids']);
                    }

                    unset($duplicate_game_details['duplicate_game_ids'][$duplicate_game_details['min_game_id']]);
                    $result = $this->game_description_model->moveGameLogsToMinimumGameId($duplicate_game_details);

                    if ($result) {
                        if (!empty($delete_game) && $delete_game == "true") {
                            $this->game_description_model->deleteGameByGameId($duplicate_game_details);
                        }
                    }

                    @$affected_game_logs_per_game_platform[$game_detail['game_platform_id']] += $affected_game_logs_row;
                    $this->utils->debug_log($duplicate_game_details);
                }

                $this->utils->debug_log(['Total Duplicate Games Count' => $game_count,'Total Affected Game logs row' => $affected_game_logs_per_game_platform]);
                if (!empty($start_date) && !empty($end_date) && $game_count > 0) {
                    $start_date = $this->utils->formatDateTimeForMysql(new DateTime($start_date));
                    $end_date = $this->utils->formatDateTimeForMysql(new DateTime($end_date));

                    #check if given date is valid
                    if (!empty($start_date) && !empty($end_date)) {
                        $this->rebuild_totals($start_date, $end_date, true, true);
                    }
                }
            }
        }
    }

	/**
	 * overview : sync game platform for anytime, only for debug
	 *
	 * @param int	 $gamePlatformId
	 * @param string $dateTimeFromStr
	 * @param string $dateTimeToStr
	 * @param string $playerName
	 * @return mixed
	 */
	public function sync_original_game_logs_anytime($gamePlatformId, $dateTimeFromStr = null, $dateTimeToStr = null, $playerName = null) {

		if($this->utils->getConfig('disabled_manually_sync_game_logs')){
			$this->utils->error_log('=========donnot allow sync manually game logs============================');
			return false;
		}

		set_time_limit(0);

		$today=date('Ymd');
		$this->utils->initRespTableByDate($today);
		$this->utils->info_log('THIS IS ONLY FOR DEBUG');
		sleep(1);
		$this->utils->debug_log('=========start sync_original_game_logs_anytime============================');

		$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);
		list($todayFrom, $todayTo) = $this->utils->getTodayDateTimeRange();
		//set default datetime from to
		if (empty($dateTimeFromStr)) {
			$dateTimeFrom = $todayFrom;
		}
		if (empty($dateTimeToStr)) {
			$dateTimeTo = $todayTo;
		}

		$api=$this->utils->loadExternalSystemLibObject($gamePlatformId);
		if(!empty($api)){
			//because we need really sync
			$ignore_public_sync=false;
			$token = random_string('unique');
			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, null, null,
				array('ignore_public_sync' => $ignore_public_sync));

			$this->utils->debug_log('syncOriginalGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$rlt=$api->syncOriginalGameLogs($token);
			$this->utils->debug_log('result of syncOriginalGameLogs', $rlt);

			$this->utils->debug_log('syncLostAndFound', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$rlt=$api->syncLostAndFound($token);
			$this->utils->debug_log('result of syncLostAndFound', $rlt);

			$api->clearSyncInfo($token);

		}else{
			$this->utils->error_log('NOT FOUND API', $gamePlatformId);
		}

		// $rlt = $manager->syncOriginalGameRecordsOnOnePlatform($gamePlatformId, $dateTimeFrom, $dateTimeTo, $playerName);

		$this->utils->debug_log('=========end sync_original_game_logs_anytime============================');

		$this->utils->debug_log('gamePlatformId', $gamePlatformId, 'dateTimeFromStr', $dateTimeFromStr,
			'dateTimeToStr', $dateTimeToStr, 'sync result', $rlt);
		// $this->returnText($msg);
		// return $msg;
	}

	//parameter $date 20180606
    public function sync_jumbgaming_daily_gamelogs_ftp($date = null) {
    	if (!isset($date)) {
    		$str_date = date('Y-m-d');
	    	$date = date('Ymd', strtotime('-1 day', strtotime($str_date)));
    	}

        $api = $this->utils->loadExternalSystemLibObject(JUMB_GAMING_API);

        $token = random_string('unique');
        $date = new DateTime($date);
        $dateTimeFrom = new DateTime($date->format('Y-m-d 00:00:00'));
        $dateTimeTo = new DateTime($date->format('Y-m-d 23:59:59'));
        $api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);

        $ignore_public_sync = true;
        $api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, null, null, null,
				array('ignore_public_sync' => $ignore_public_sync));

        $rlt = $api->syncJumbGamelogsFtp($token);
        $this->utils->debug_log('==================== JumbGaming Daily Game Logs via FTP ====================');
        $this->utils->debug_log($rlt);
        $this->utils->debug_log('============================================================================');
    }

	/**
	 * merge anytime, only for debug
	 * @param  int  $gamePlatformId
	 * @param  string  $dateTimeFromStr
	 * @param  string  $dateTimeToStr
	 * @param  string  $playerName
	 */
	public function sync_merge_game_logs_anytime($gamePlatformId, $dateTimeFromStr, $dateTimeToStr, $playerName = null) {

		if($this->utils->getConfig('disabled_manually_sync_game_logs')){
			$this->utils->error_log('=========donnot allow sync manually game logs============================');
			return false;
		}

		set_time_limit(0);

		$this->utils->info_log('THIS IS ONLY FOR DEBUG');
		sleep(1);
		$this->utils->debug_log('=========start sync_merge_game_logs_anytime============================', $gamePlatformId, 'dateTimeFromStr', $dateTimeFromStr, 'dateTimeToStr', $dateTimeToStr, 'playerName', $playerName);

		$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);

		$this->load->model(['external_system']);

		// $manager = $this->utils->loadGameManager();
		$api = $this->loadApi($gamePlatformId);
		if(!empty($api)){
			$token = random_string('unique');
			$ignore_public_sync = true;
			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, null, null,
				array('ignore_public_sync' => $ignore_public_sync));

			$rlt = $api->syncMergeToGameLogs($token);
	        $api->clearSyncInfo($token);
		}else{
			$this->utils->error_log('load api failed', $gamePlatformId);
		}

		$this->utils->debug_log('=========end sync_merge_game_logs_anytime============================', $gamePlatformId);
	}

	public function sync_t1_gamegateway($dateTimeFromStr, $dateTimeToStr, $playerName = null, $queue_token=null){

		// $default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		// set_time_limit($default_sync_game_logs_max_time_second);

		$this->load->model(['game_description_model', 'queue_result', 'external_system']);

		$this->alwaysEnableQueue=$this->utils->getConfig('alwaysEnableQueue');
		if($this->alwaysEnableQueue && empty($queue_token)){
			//create one
			$this->load->library(['language_function']);
			// $this->load->model(['queue_result']);
			$lang=$this->language_function->getCurrentLanguage();
			$systemId = Queue_result::SYSTEM_UNKNOWN;
			$params = [
				'dateTimeFromStr' => $dateTimeFromStr,
				'dateTimeToStr' => $dateTimeToStr,
				'playerName' => $playerName,
			];
			$funcName='sync_t1_gamegateway';
			$caller=0;
			$callerType=Queue_result::CALLER_TYPE_SYSTEM;
			$state=null;
			$queue_token = $this->queue_result->newResult($systemId,
				$funcName, $params, $callerType, $caller, $state, $lang);

		}

		if($playerName=='_null'){
			$playerName=null;
		}

		$this->utils->debug_log('=========start Sync_t1_gamegateway============================');

		$this->load->model(array('sync_status_model', 'queue_result'));

		if(!empty($queue_token)){
			$this->utils->debug_log('append result ', _REQUEST_ID, $queue_token);
			//update queue_results
			$this->queue_result->appendResult($queue_token, [
				'request_id'=>_REQUEST_ID, 'func'=>'Sync_t1_gamegateway', 'dateTimeFromStr'=>$dateTimeFromStr,
				'$dateTimeToStr'=>$dateTimeToStr]);
		}

		$success=false;

		$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);
		list($todayFrom, $todayTo) = $this->utils->getTodayDateTimeRange();
		//set default datetime from to
		if (empty($dateTimeFromStr)) {
			$dateTimeFrom = $todayFrom;
		}
		if (empty($dateTimeToStr)) {
			$dateTimeTo = $todayTo;
		}

		//load all active t1 games
		$apiList=$this->external_system->getAllActiveT1GameApiForSync();
		if(!empty($apiList)){
			//get min api id
			$apiId=-1;
			$api=null;
			//original code to current code
			$multiplePlatformIdMap=[];
			foreach ($apiList as $key => $value) {
				$tmpApi=$this->utils->loadExternalSystemLibObject($key);
				$multiplePlatformIdMap[$tmpApi->getOriginalPlatformCode()]=$tmpApi->getPlatformCode();
				if(empty($api) || $key<$apiId){
					$apiId=$key;
					$api=$tmpApi;
				}
			}
			//get unknown game from t1 platform
			$unknownGameTypeMap=$this->game_description_model->getMultipleUnknownGameMap(array_values($multiplePlatformIdMap));
			// $api=$this->utils->loadExternalSystemLibObject($apiId);
			$error_message=null;
			if(!empty($api)){

				$gameName=null;
				$sync_id=null;
				$token = random_string('unique');
				$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, $sync_id,
					['multiplePlatformIdMap'=>$multiplePlatformIdMap, 'unknownGameTypeMap'=>$unknownGameTypeMap,
					'ignore_public_sync'=>true]);

				$this->utils->info_log('multiplePlatformIdMap', $multiplePlatformIdMap, 'unknownGameTypeMap', $unknownGameTypeMap);

				$rlt=$api->syncDirectlyAllT1GameLogs($token);

				$this->utils->debug_log('print syncDirectlyAllT1GameLogs', $rlt);

				$api->clearSyncInfo($token);

				$success=$rlt['success'];
				$error_message=isset($rlt['error_message']) ? $rlt['error_message'] : null;
			}else{
				$success=false;
				$error_message='load t1 common game api failed';
				$this->utils->error_log($error_message);
			}
		}

		if(!empty($queue_token)){
			if($success){
				$this->queue_result->appendResult($queue_token, [
					'request_id'=>_REQUEST_ID, 'func'=>'Sync_t1_gamegateway', 'dateTimeFromStr'=>$dateTimeFromStr,
					'$dateTimeToStr'=>$dateTimeToStr, 'success'=> $success], true, false);
			}else{
				$this->queue_result->appendResult($queue_token, [
					'request_id'=>_REQUEST_ID, 'func'=>'Sync_t1_gamegateway', 'dateTimeFromStr'=>$dateTimeFromStr,
					'$dateTimeToStr'=>$dateTimeToStr, 'success'=> $success, 'error_message'=>$error_message],
					true, true);
			}
		}

		$this->utils->debug_log('=========end Sync_t1_gamegateway============================');

	}

    /**
     * [sync_unknown_game sync unknown game to available game apis in external_system_list.xml ]
     * @return [type]                   [description]
     */
    public function sync_unknown_game(){
        $this->load->model('game_description_model');
        $this->game_description_model->syncUnknownGame();
    }

	public function sync_md5_sum_on_original_anytime($gamePlatformId, $dateTimeFromStr, $dateTimeToStr, $playerName = null) {

		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		$this->utils->debug_log('=========start sync_md5_sum_on_original_anytime============================', $gamePlatformId, 'dateTimeFromStr', $dateTimeFromStr, 'dateTimeToStr', $dateTimeToStr, 'playerName', $playerName);

		$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);

		$this->load->model(['external_system']);

		// $manager = $this->utils->loadGameManager();
		$api = $this->loadApi($gamePlatformId);
		if(!empty($api)){
			$token = random_string('unique');
			$ignore_public_sync = true;
			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, null, null,
				array('ignore_public_sync' => $ignore_public_sync));

			$rlt = $api->syncOriginalMd5Sum($token);

			$this->utils->debug_log('=========syncOriginalMd5Sum============================', $rlt);

	        $api->clearSyncInfo($token);
		}else{
			$this->utils->error_log('load api failed', $gamePlatformId);
		}

		$this->utils->debug_log('=========end sync_md5_sum_on_original_anytime============================', $gamePlatformId);

	}

	/**
	 * manually sync
	 *
	 * @param  string  $dateTimeFromStr
	 * @param  string  $dateTimeToStr
	 * @param  string  $playerName
	 * @param  integer $timelimit
	 * @param  string  $queue_token
	 *
	 */
	public function manually_sync_t1_gamegateway($dateTimeFromStr, $dateTimeToStr, $playerName = '_null', $timelimit = 30, $queue_token='_null'){

		if($playerName=='_null'){
			$playerName=null;
		}
		if($queue_token=='_null'){
			$queue_token=null;
		}

		set_time_limit(0);
		$this->utils->debug_log('dateTimeFromStr', $dateTimeFromStr, 'dateTimeToStr', $dateTimeToStr, 'playerName', $playerName, 'timelimit', $timelimit);

		$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);

		$php_str=$this->utils->find_out_php();
		$og_admin_home = realpath(dirname(__FILE__) . "/../../../");
		$func='sync';
		$is_blocked=true;
		$use_bet_time='true';
		$step='+' . $timelimit . ' minutes';

		$success=$this->utils->loopDateTimeStartEnd($dateTimeFrom, $dateTimeTo, $step, function($from, $to, $step)
			use($playerName, $queue_token, $og_admin_home, $php_str, $func, $use_bet_time, $is_blocked){


		// $from = $dateTimeFrom;
		// while (!$this->utils->gtAndEqEndTime($from, $dateTimeTo)) {
			// $to = $this->utils->getNextTime($from, '+' . $timelimit . ' minutes');
			$this->utils->debug_log('from', $from, 'to', $to, 'step', $step, round(memory_get_usage()/(1024*1024), 2));

			$file_list=[];

			$args=[$this->utils->formatDateTimeForMysql($from), $this->utils->formatDateTimeForMysql($to),
				$playerName, $queue_token, $use_bet_time];

			$argStr = '';
			if (!empty($args)) {
				foreach ($args as $val) {
					if($val===null || $val===''){
						$val='_null';
					}
					$argStr .= ' "' . $val . '"';
				}
			}

			$cmd=$php_str.' '.$og_admin_home.'/shell/ci_cli.php cli/sync_t1_gamegateway/'.$func.$argStr;
			$cmd=$this->utils->generateCommonLine($cmd, $is_blocked, $func, $file_list);

			$this->utils->debug_log('start sync------------------', $cmd);
			system($cmd, $result);
			$this->utils->debug_log('end sync--------------------', $result, 'delete files', $file_list);

			if(!empty($file_list)){
				foreach ($file_list as $f) {
					$this->utils->debug_log('delete file: '.$f);
					@unlink($f);
				}
			}
			unset($file_list);

			return $result==0;

			// if(!$success){
			// 	$this->utils->error_log('sync failed',$from, $to, $playerName, $queue_token);
			// }

			// $from = $to;
		// }
		});

		if(!empty($queue_token)){
			$done=true;
			$is_error=!$success;
			if($success){
				$this->queue_result->appendResult($queue_token, [
					'request_id'=>_REQUEST_ID, 'func'=>'sync_t1_gamegateway', 'success'=> $success,
					'result'=>$rlt], $done, $is_error);
			}else{
				$this->queue_result->appendResult($queue_token, [
					'request_id'=>_REQUEST_ID, 'func'=>'sync_t1_gamegateway', 'success'=> $success, 'error_message'=>$error_message,
					'result'=>$rlt], $done, $is_error);
			}
		}

	}

	public function sync_game_tag($game_platform_id = null) {

		if( !empty($game_platform_id) ) {
			$this->load->model('game_type_model');
    		$this->game_type_model->syncGameTag( $game_platform_id );
		}

    }
    /**
     *
     * @param  string $mode last_one_hour/available/all
     *
     */
	public function batch_sync_balance_by($mode='last_one_hour', $dry_run='true', $max_number='10', $apiId='' , $token = null) {

		if($mode=='last_one_hour'){
			set_time_limit(3600);
		}else{
			//3 hours
			set_time_limit(10800);
		}

		$dry_run=$dry_run=='true';
		$max_number=intval($max_number);

		// $manager = $this->utils->loadGameManager();

		$this->load->model(array('player_model', 'wallet_model'));
		$players = $this->wallet_model->getPlayerListUnbuffered($mode,
			$mode=='last_one_hour' ? 0 : Wallet_model::MIN_WALLET_AMOUNT );
		$result=['players'=>count($players), 'failedPlayer'=>[]];

		$result_summary = array(
			'total_player_count' => $result['players'],
			'load api failed' => array(),
			'ignore seam less game' => array(),
			'ignore onlyTransferPositiveInteger and amount <1' => array(),
			'total_ignored_players' => 0,
			'total_failed_players' => 0,
			'total_success_players' => 0,
		);

		$this->utils->debug_log('getPlayerListUnbuffered', $result['players']);
		$this->utils->printLastSQL();

		$apiMap=[];
		// $this->utils->debug_log("players =========================================> ", $players);
		// $controller = $this;
		if (!empty($players)) {
			// $playerUsernames = array();
			$walletMap=[];
			$update_queue_count = 0;
			foreach ($players as $row) {
				$update_queue_count++;

				$playerId = $row['playerId'];
				$username = $row['username'];
				$subWalletId=$row['subWalletId'];
				$amount=$row['amount'];
				$api=null;
				if(isset($apiMap[$subWalletId])){
					$api=$apiMap[$subWalletId];
				}else{
					$api = $this->utils->loadExternalSystemLibObject($subWalletId);
					$apiMap[$subWalletId]=$api;
				}

				// -- update queue result if remote job
				// -- update only after every 1000 loops to avoid too many update queries
				if($update_queue_count >= 1000){
					$this->_batchSyncBalanceBy_updateQueueResult($token, $result_summary, FALSE, FALSE);
					$update_queue_count = 0;
				}

				if(empty($api)){
					$this->utils->error_log('load api failed', $subWalletId, $playerId, $username);
					$result['load api failed']['subWalletId'][$subWalletId][] = array('playerId' => $playerId, 'username' => $username);

					if(isset($result_summary['load api failed'][$subWalletId]['player_count']))
						$result_summary['load api failed'][$subWalletId]['player_count']++;
					else
						$result_summary['load api failed'][$subWalletId]['player_count'] = 1;

					continue;
				}
				//ignore seam less
				if($api->isSeamLessGame()){
					//print log
					$this->utils->debug_log('ignore seam less game', $api->getPlatformCode(), $playerId, $username);
					$result['ignore seam less game']['platform_code'][$api->getPlatformCode()][] = array('playerId' => $playerId, 'username' => $username);

					if(isset($result_summary['ignore seam less game'][$api->getPlatformCode()]['player_count']))
						$result_summary['ignore seam less game'][$api->getPlatformCode()]['player_count']++;
					else
						$result_summary['ignore seam less game'][$api->getPlatformCode()]['player_count'] = 1;

					continue;
				}
				if($api->onlyTransferPositiveInteger() && $amount<1){
					$this->utils->debug_log('ignore onlyTransferPositiveInteger and amount <1', $api->getPlatformCode(), $playerId, $amount);
					$result['ignore onlyTransferPositiveInteger and amount <1']['platform_code'][$api->getPlatformCode()][] = array('playerId' => $playerId, 'username' => $username);

					if(isset($result_summary['ignore onlyTransferPositiveInteger and amount <1'][$api->getPlatformCode()]['player_count']))
						$result_summary['ignore onlyTransferPositiveInteger and amount <1'][$api->getPlatformCode()]['player_count']++;
					else
						$result_summary['ignore onlyTransferPositiveInteger and amount <1'][$api->getPlatformCode()]['player_count'] = 1;

					continue;
				}
				// $playerUsernames[] = $username;
				if(!isset($walletMap[$subWalletId])){
					$walletMap[$subWalletId]=[];
				}
				$walletMap[$subWalletId][$playerId]=$username;
				// $this->utils->debug_log('subWalletId', $subWalletId, 'playerId', $playerId, 'username', $username);
			}
			unset($players);

			$this->utils->debug_log('dryrun', $dry_run, 'walletMap', array_keys($walletMap));

			$result['dryrun'] = $dry_run;
			$result['walletMap'] = implode(', ', array_keys($walletMap));
			$result_summary['walletMap'] = implode(', ', array_keys($walletMap));

			if(!empty($walletMap)){
				foreach ($walletMap as $subWalletId => $playerList) {
					$this->utils->info_log('sub wallet id', $subWalletId, 'player list', count($playerList));
					$result['total'][$subWalletId]=count($playerList);

					$result_summary['total'][$subWalletId] = count($playerList);

					if($apiId!='' && $apiId != '_null' && $apiId!=$subWalletId){
						//ignore other api

						if(!isset($result['ignore other api:']))
							$result['ignore other api:'] = '';

						$result['ignore other api:'] .= $subWalletId .', ';
						$result_summary['ignore other api:'] = $result['ignore other api:'];

						continue;
					}

					if(count($playerList)>$max_number){
						$oldPlayerList=$playerList;
						$playerList=[];
						$i=0;
						foreach ($oldPlayerList as $playerId => $username) {
							$i++;
							if($i>$max_number){
								break;
							}
							$playerList[$playerId]=$username;
						}
						// $playerList=array_slice($playerList, 0, $max_number);
						$this->utils->debug_log('only allow max number', $max_number);

						$result['only allow max number:'] =  $max_number;
						$result_summary['only allow max number'] =  $max_number;
					}

					$api = $this->utils->loadExternalSystemLibObject($subWalletId);
					$rlt=$api->batchQueryPlayerBalanceOneByOne($playerList);
					unset($playerList);
					unset($oldPlayerList);
					if($dry_run){
						$this->utils->debug_log('batchQueryPlayerBalanceOneByOne', $subWalletId, $rlt);
					}
					if($rlt['success'] && !empty($rlt['balances'])){
						$balances=$rlt['balances'];
						$cntIgnored=0;
						foreach ($balances as $playerId => $bal) {
							if(!empty($playerId) && $bal!==null){
								//ignore int only if <1
								$updated=false;
								$success = $this->lockAndTransForPlayerBalance($playerId,
									function () use ($playerId, $subWalletId, $bal, $dry_run, &$updated) {
										$success=true;
										//compare wallet
										$oldBal=$this->wallet_model->getSubWalletOnBigWalletByPlayer($playerId,
											Wallet_model::BIG_WALLET_SUB_TYPE_REAL, $subWalletId);
										if(!$this->utils->compareResultFloat($oldBal,'=',$bal)){
											if($dry_run){
												$this->utils->info_log('dry run, so ignore', $playerId, $bal);

												$result['dry run, so ignore'][] = array('playerId' => $playerId, 'balance' => $bal);

											}else{
												//update balance
												$success=$this->wallet_model->updateSubOnBigWalletByPlayerId(
													$playerId, $subWalletId, Wallet_model::BIG_WALLET_SUB_TYPE_REAL, $bal
												);
												$updated=true;
											}
										}

										return $success;
										// return $this->wallet_model->recordPlayerAfterActionWalletBalanceHistory(Wallet_model::BALANCE_ACTION_REFRESH,
										// 	$playerId, null, -1, 0, null, null, null, null, null);
									}
								);
								if(!$success){
									$result['failedPlayer'][]=['player'=>$playerId,'balance'=>$bal];
									$result_summary['total_failed_players']++;

									$this->utils->error_log('update balance failed', $playerId);


								}else{
									if($updated){
										if(isset($result['success'][$subWalletId])){
											$result['success'][$subWalletId][]=['player'=>$playerId,'balance'=>$bal];
										}else{
											$result['success'][$subWalletId]=[['player'=>$playerId,'balance'=>$bal]];
										}

										$result_summary['total_success_players']++;


									}else{
										$cntIgnored++;
										if($dry_run){
											if(isset($result['ignored'][$subWalletId])){
												$result['ignored'][$subWalletId][]=['player'=>$playerId,'balance'=>$bal];
											}else{
												$result['ignored'][$subWalletId]=[['player'=>$playerId,'balance'=>$bal]];
											}
										}else{
											$result['ignored'][$subWalletId]=$cntIgnored;
										}

										$result_summary['total_ignored_players']++;

									}
								}
							}
						}
					}else{
						if(empty($rlt['balances'])){
							$this->utils->error_log('empty balance', $subWalletId, $rlt);
							$result['empty balance'][] = array( $subWalletId => $rlt );
						}
						$this->utils->error_log('query balance failed', $subWalletId, $rlt);

						$result['query balance failed'][] = array( $subWalletId => $rlt );

						if(!isset($result_summary['query balance failed count'][$subWalletId])){
							$result_summary['query balance failed count'][$subWalletId] = 1;
						}else{
							$result_summary['query balance failed count'][$subWalletId]++;
						}
					}

					// -- update queue result if remote job
					$this->_batchSyncBalanceBy_updateQueueResult($token, $result_summary, FALSE, FALSE);
				}

			}
			// $this->utils->debug_log('walletMap', $walletMap);
			unset($walletMap);

		} else {
			$this->utils->info_log('no players');
		}

		// $this->utils->debug_log("failed players =========================================> ", $result);

		// -- update queue result if remote job
		$this->_batchSyncBalanceBy_updateQueueResult($token, $result_summary, TRUE, FALSE);

		// -- Save detailed log to remote log file if remote job
		$detailed_link = $this->_batchSyncBalanceBy_saveDetailedResultToLog($token, $result, $result_summary);
		if($detailed_link !== false)
			$result_summary['View Detailed Result'] = "<a href='".$detailed_link."' target='_blank'>".lang('CLICK HERE')."</a>";

		// -- update queue result if remote job with View Detailed Result
		$this->_batchSyncBalanceBy_updateQueueResult($token, $result_summary, TRUE, FALSE);

		$this->utils->info_log('result', $result, $result_summary);
	}

	/**
	 * Update queue result of remote batch sync balance
	 * --
	 * @param  string  $token
	 * @param  array   $result
	 * @param  boolean $isDone
	 * @param  boolean $hasError
	 * @return void
	 */
	private function _batchSyncBalanceBy_updateQueueResult($token = null, $result = array(), $isDone = true, $hasError = false){

		if(empty($token)) return;

		$this->load->model('queue_result');

		$this->queue_result->updateResultWithCustomStatus($token, $result, $isDone, $hasError);

		if (!$hasError)
			$this->utils->debug_Log("remote_batch_sync_balance_by  token:" . $token . " result: ",$result);
		else
			$this->utils->error_Log("remote_batch_sync_balance_by  token:" . $token . " result: ",$result);
	}

	/**
	 * Save detailed result to a remote log file
	 * --
	 * @param  string $token
	 * @param  array $result
	 * @param  array $result_summary
	 * @return boolean / string      false / link
	 */
	private function _batchSyncBalanceBy_saveDetailedResultToLog($token, $result, $result_summary){

		if(empty($token)) return false;

		$this->load->model('queue_result');

		$log_message  = "DATE: ". $this->utils->getNowForMysql()."\n\n";
		$log_message .= "METHOD CALLED: batch_sync_balance_by \n\n";
		$log_message .= "TOKEN: ". $token ."\n\n";
		$log_message .= "-------------------------------" . "\n\n\n";
		$log_message .= "RESULT: "."\n\n";
		$log_message .= json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
		$log_message .= "\n\n\n";
		$log_message .= "-------------------------------" . "\n\n\n";
		$log_message .= "SUMMARY: "."\n\n";
		$log_message .= json_encode($result_summary,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

		return $this->utils->_saveDetailedResultToRemoteLog($token, 'remote_batch_sync_balance', $log_message);
	}

	public function sync_original_game_logs_from_file($gamePlatformId, $dateTimeFromStr, $dateTimeToStr) {

		set_time_limit(0);

		$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);

		$today=date('Ymd');
		$this->utils->initRespTableByDate($today);

		$this->utils->debug_log('=========start sync_original_game_logs_from_file============================');
		$rlt=null;
		$api=$this->utils->loadExternalSystemLibObject($gamePlatformId);
		if(!empty($api)){
			//because we need really sync
			$ignore_public_sync=false;
			$playerName=null;
			$token = random_string('unique');
			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, null, null,
				array('ignore_public_sync' => $ignore_public_sync));

			$this->utils->debug_log('syncOriginalFromFile', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$rlt=$api->syncOriginalFromFile($token);
			if(!$rlt['success']){
				$this->utils->error_log('error of syncOriginalFromFile', $rlt);
			}else{
				$this->utils->debug_log('result of syncOriginalFromFile', $rlt);
			}
			$api->clearSyncInfo($token);
		}else{
			$this->utils->error_log('NOT FOUND API', $gamePlatformId);
		}

		$this->utils->debug_log('=========end sync_original_game_logs_from_file============================');

		$this->utils->debug_log('gamePlatformId', $gamePlatformId, 'sync result', $rlt);
	}

	/**
	 * overview : sync merge game logs by timelimit
	 *
	 * @param int    $gamePlatformId
	 * @param date   $dateTimeFromStr
	 * @param date   $dateTimeToStr
	 * @param string $playerName
	 * @param int 	 $timelimit
	 */
	public function sync_merge_game_logs_by_timelimit_nolimit($gamePlatformId, $dateTimeFromStr = null, $dateTimeToStr = null, $playerName = null, $timelimit = 30) {

		if($this->utils->getConfig('disabled_manually_sync_game_logs')){
			$this->utils->error_log('=========donnot allow sync manually game logs============================');
			return false;
		}

		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		$this->utils->debug_log('=========start sync_merge_game_logs_by_timelimit============================', $gamePlatformId, 'dateTimeFromStr', $dateTimeFromStr, 'dateTimeToStr', $dateTimeToStr, 'playerName', $playerName, 'timelimit', $timelimit);
		// $this->returnText($msg);
		$mark = 'sync_merge_game_logs_by_timelimit' . $gamePlatformId;
		$this->utils->markProfilerStart($mark);

		$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);
		list($todayFrom, $todayTo) = $this->utils->getTodayDateTimeRange();
		//set default datetime from to
		if (empty($dateTimeFromStr)) {
			$dateTimeFrom = $todayFrom;
		}
		if (empty($dateTimeToStr)) {
			$dateTimeTo = $todayTo;
		}
		if($playerName=='_null' || $playerName=='null'){
			$playerName='';
		}

		$this->load->model(['external_system']);

		// $manager = $this->utils->loadGameManager();
		$api = $this->loadApi($gamePlatformId);

		$from = $dateTimeFrom;
		while (!$this->utils->gtAndEqEndTime($from, $dateTimeTo)) {

			$to = $this->utils->getNextTime($from, '+' . $timelimit . ' minutes');
			$this->utils->debug_log('from', $from, 'to', $to, 'timelimit', $timelimit);
			// $this->returnText($msg);

			$token = random_string('unique');

			$ignore_public_sync = true;

			$api->saveSyncInfoByToken($token, $from, $to, $playerName, null, null,
				array('ignore_public_sync' => $ignore_public_sync));

			$rlt = $api->syncMergeToGameLogs($token);

	        $api->clearSyncInfo($token);

			// $manager->syncOriginalGameRecordsOnOnePlatform($gamePlatformId, $from, $to, $playerName);
			$from = $to;

			// $this->external_system->closeDB();
			// $this->external_system->openDB();

		}

		$this->utils->markProfilerEndAndPrint($mark);
		// $this->returnText($msg);

		$this->utils->debug_log('=========end sync_merge_game_logs_by_timelimit============================', $gamePlatformId);
		// $this->returnText($msg);
	}

	public function sync_incomplete_games($gamePlatformId=null){

		set_time_limit(120);

		$today=date('Ymd');
		$this->utils->initRespTableByDate($today);

		$apiList=null;
		if(empty($gamePlatformId)){
			$apiList=$this->utils->getConfig('exists_incomplete_game_api_list');
			//remove inactive api
			$this->load->model(['external_system']);
			$apiList=$this->external_system->filterActiveGameApi($apiList);
		}else{
			$apiList=[$gamePlatformId];
		}
		$this->utils->info_log('=========start sync_incomplete_games============================', $apiList);
		if(empty($apiList)){
			$this->utils->debug_log('no any available api', $gamePlatformId, $apiList);
			return;
		}
		foreach ($apiList as $apiId) {
			$rlt=null;
			$api=$this->utils->loadExternalSystemLibObject($apiId);
			if(!empty($api)){
				//because we need really sync
				$ignore_public_sync=false;
				$dateTimeTo=null;
				$dateTimeFrom=null;
				$playerName=null;
				$token = random_string('unique');
				$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, null, null,
					array('ignore_public_sync' => $ignore_public_sync));

				$this->utils->debug_log('syncIncompleteGames', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
				$rlt=$api->syncIncompleteGames($token);
				$this->utils->info_log('result of syncIncompleteGames', $rlt);

				$api->clearSyncInfo($token);

			}else{
				$this->utils->error_log('NOT FOUND API', $apiId);
			}
			$this->utils->info_log('apiId', $apiId, 'result', $rlt);
		}

		$this->utils->info_log('=========end sync_incomplete_games============================', $apiList);

	}

	/**
	 * manually sync
	 *
	 * @param  string  $dateTimeFromStr
	 * @param  string  $dateTimeToStr
	 * @param  string  $playerName
	 * @param  integer $timelimit
	 * @param  string  $queue_token
	 *
	 */
	public function manually_sync_t1_gamegateway_stream($dateTimeFromStr, $dateTimeToStr, $playerName = '_null', $queue_token='_null'){

		// if($playerName=='_null'){
		// 	$playerName=null;
		// }
		// if($queue_token=='_null'){
		// 	$queue_token=null;
		// }

		set_time_limit(0);
		$this->utils->debug_log('dateTimeFromStr', $dateTimeFromStr, 'dateTimeToStr', $dateTimeToStr, 'playerName', $playerName);
		$maxRetry=10;

		// $dateTimeFrom = new \DateTime($dateTimeFromStr);
		// $dateTimeTo = new \DateTime($dateTimeToStr);

		$now=new DateTime();
		$now->modify('-30 seconds');
		//check max limit
		if($dateTimeFromStr>$now->format('Y-m-d H:i:s') ||
				$dateTimeToStr>$now->format('Y-m-d H:i:s')){
			$this->utils->error_log('wrong date, max time is '.$now->format('Y-m-d H:i:s'), $dateTimeFromStr, $dateTimeToStr);
			return;
		}

		$php_str=$this->utils->find_out_php();
		$og_admin_home = realpath(dirname(__FILE__) . "/../../../");
		$func='sync_stream';
		$is_blocked=true;
		$retry=0;
		$success=true;
		// $use_bet_time='true';
		// $step='+' . $timelimit . ' minutes';
		$currentDateTimeStr=$dateTimeFromStr;
		while($currentDateTimeStr<=$dateTimeToStr){

		// $from = $dateTimeFrom;
		// while (!$this->utils->gtAndEqEndTime($from, $dateTimeTo)) {
			// $to = $this->utils->getNextTime($from, '+' . $timelimit . ' minutes');
			$this->utils->debug_log('currentDateTimeStr', $currentDateTimeStr, 'dateTimeToStr', $dateTimeToStr);

			$file_list=[];
			$lastDateTimeFileName=$this->utils->createTempFileName();
			$args=[$currentDateTimeStr, $lastDateTimeFileName, $playerName, $queue_token];

			$argStr = '';
			if (!empty($args)) {
				foreach ($args as $val) {
					if($val===null || $val===''){
						$val='_null';
					}
					$argStr .= ' "' . $val . '"';
				}
			}

			$cmd=$php_str.' '.$og_admin_home.'/shell/ci_cli.php cli/sync_t1_gamegateway/'.$func.$argStr;
			$cmd=$this->utils->generateCommonLine($cmd, $is_blocked, $func, $file_list);

			$this->utils->debug_log('start sync------------------', $cmd);
			system($cmd, $result);
			$this->utils->debug_log('end sync--------------------', $result, 'delete files', $file_list);

			if(!empty($file_list)){
				foreach ($file_list as $f) {
					$this->utils->debug_log('delete file: '.$f);
					@unlink($f);
				}
			}
			unset($file_list);

			$jsonResult=file_get_contents($this->utils->createTempDirPath().'/'.$lastDateTimeFileName);
			$jsonObj=json_decode($jsonResult, true);
			$this->utils->debug_log('get result', $jsonObj);
			if($jsonObj['success']){
				$retry=0;
				if(isset($jsonObj['normalResult']['next_datetime']) &&
						!empty($jsonObj['normalResult']['next_datetime'])){
					//next date time
					$currentDateTimeStr=$jsonObj['normalResult']['next_datetime'];
				}else{
					//empty next_datetime means no data, just quit
					$currentDateTimeStr=$dateTimeToStr;
				}
			}else{
				$retry++;
				//keep $currentDateTimeStr
				$this->utils->error_log('sync failed', $jsonObj, 'keep currentDateTimeStr', $currentDateTimeStr);
				if($retry>$maxRetry){
					$success=false;
					$error_message='retry too many times';
					break;
				}
			}
			$now=new DateTime();
			$now->modify('-30 seconds');
			//check max limit
			if($currentDateTimeStr>$now->format('Y-m-d H:i:s')){
				$this->utils->debug_log('exceed max time', $currentDateTimeStr, $now);
				$currentDateTimeStr=$now->format('Y-m-d H:i:s');
			}
			$this->utils->debug_log('next currentDateTimeStr', $currentDateTimeStr, 'dateTimeToStr', $dateTimeToStr);
		}
		$this->utils->debug_log('quit sync');

		if($queue_token=='_null'){
			$queue_token=null;
		}
		if(!empty($queue_token)){
			$done=true;
			$is_error=!$success;
			if($success){
				$this->queue_result->appendResult($queue_token, [
					'request_id'=>_REQUEST_ID, 'func'=>'manually_sync_t1_gamegateway_stream', 'success'=> $success,
					], $done, $is_error);
			}else{
				$this->queue_result->appendResult($queue_token, [
					'request_id'=>_REQUEST_ID, 'func'=>'manually_sync_t1_gamegateway_stream', 'success'=> $success, 'error_message'=>$error_message,
					], $done, $is_error);
			}
		}
		$this->utils->info_log('done');

	}

	/**
	 * syncGameLogsByCSV
	 *
	 * @param  boolean  $update
	 * @param  number  $api number
	 *
	 */
    public function syncOriginalGameLogsFromCSV($update = false,$api) {
    	$api = $this->utils->loadExternalSystemLibObject($api);
		$result = $api->syncOriginalGameLogsFromCSV($update);
		$this->utils->debug_log('end sync--------------------', $result);
    }

    /**
	 * syncBbinOriginalGameLogs
	 *
	 * @param  datetime  $dateFrom
	 * @param  datetime  $dateTo
	 * @param  string  $gameKind
	 * @param  string  $gameType
	 * @param  string  $apiName
	 * @param  int  $game
	 * @param  string  $subGameKind
	 *
	 */
    public function syncBbinOriginalGameLogs($dateTimeFromStr, $dateTimeToStr, $gameKind, $gameType, $apiName, $game, $subGameKind = null) {
    	$api = $this->utils->loadExternalSystemLibObject(BBIN_API);
    	$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);
    	$extra = array(
    		"gameKind" => $gameKind,
    		"gameType" => $gameType,
    		"apiName" => $apiName,
    		"game" => $game,
    		"subGameKind" => $subGameKind,
    	);
    	$this->utils->debug_log('syncBbinOriginalGameLogs extra--------------------', json_encode($extra));
    	$token = random_string('unique');
        $api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, null, null, null, $extra);
		$result = $api->syncOriginalGameLogs($token);
		$this->utils->debug_log('end sync--------------------', $result);
    }

    /**
	 * syncPgsoftOriginalGamelogs
	 *
	 * @param  datetime  $dateFrom
	 * @param  datetime  $dateTo
	 *
	 */
    public function syncPgsoftOriginalGamelogs($dateTimeFromStr , $dateTimeToStr){
    	$api = $this->utils->loadExternalSystemLibObject(PGSOFT_API);
    	$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);
    	$extra = array(
    		"sync_by_time_range" => true,
    	);
    	$token = random_string('unique');
    	$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, null, null, null, $extra);
    	$result = $api->syncOriginalGameLogs($token);
		$this->utils->debug_log('end sync--------------------', $result);
	}

	/**
	 * Sync Oneworks Original Game Logs by trans_id
	 *
	 * @param int $trans_id the transaction id
	 */
	public function sync_oneworks_original_game_logs_by_transid($trans_id){
		$api = $this->utils->loadExternalSystemLibObject(ONEWORKS_API);
		$extra = array(
			'sync_by_trans_id' => true,
			'trans_id' => $trans_id
		);
		$token = random_string('unique');
		$api->saveSyncInfoBytoken($token,null,null,null,null,null,$extra);
		$result = $api->syncOriginalGameLogs($token);

		$this->CI->utils->debug_log('end sync-------------------',$result);
		$this->CI->utils->debug_log('synced by trans_id >>>>>>>>>>>>>>>>',$trans_id);
	}

	/**
	 * Manual Sync Original Game logs of API based in last sync id
	 *
	 * @param int $game_provider_id
	 * @param string $last_sync_id
	 * @param int $loop_cycle how many the manual sync will loop
	*/
	public function manual_sync_api_by_last_sync_id($game_provider_id,$last_sync_id,$loop_cycle = 1)
	{
		$api = $this->utils->loadExternalSystemLibObject($game_provider_id);
		$now = new \DateTime();
		$token = random_string('unique');

		# we will loop based on how many $loop_cycle in param
		for($cnt = 1;$cnt <= $loop_cycle;$cnt++){

			$extra = [
				"manual_last_sync_id" => $last_sync_id,
			];

			$api->saveSyncInfoBytoken($token,$now,$now,null,null,null,$extra);

			$result = $api->syncOriginalGameLogs($token);

			$this->CI->utils->debug_log("manual_sync_api_by_last_sync_id result : >>>>",$result);

			# we re try if success call but no last_sync_id, meaning network error
			if(isset($result["success"]) && $result["success"] && ! isset($result["last_sync_id"])){
				$cnt--;
				continue;
			}
			# error in API
			if(isset($result["success"]) && ! $result["success"]){
				$this->CI->utils->debug_log("Manual Sync ERROR ========>",$result);
				break;
			}

			$last_sync_id = isset($result["last_sync_id"]) ? $result["last_sync_id"] : 0;

			$this->CI->utils->info_log("LOOP ====>", $cnt, "MANUAL_SYNC_LAST_SYNC_ID ====>", $last_sync_id);

			$api->clearSyncInfo($token);

			$all_ids_manual_synced[] = $last_sync_id;

		}
		$all_ids_manual_synced = (is_array($all_ids_manual_synced) && count($all_ids_manual_synced) > 0)
							? end($all_ids_manual_synced) : null;

		$this->CI->utils->debug_log("All sync IDS manually synced",$all_ids_manual_synced);
		$this->CI->utils->info_log("LAST SYNC ID FROM MANUAL SYNC IS: >>>>",$all_ids_manual_synced);
	}

	/**
	 * For: DG transfer wallet Game API
	 *
	 * Sync Data(lost data) of more than 24 hours data
	 *
	 * @param datetime $beginTime
	 * @param datetime @endTime
	 *
	 * @return void
	 */
	public function manual_sync_dg_transfer_wallet_missing_data($beginTime,$endTime){

		$api = $this->CI->utils->loadExternalSystemLibObject(DG_API);
		$now = new \DateTime();
		$token = random_string('unique');

		$beginTime = !empty($beginTime) ? (new \DateTime($beginTime)) : $now;
		$endTime = !empty($endTime) ? (new \DateTime($endTime)) : $now;

		$extra = [
			"isManualSync" => true
		];

		$api->saveSyncInfoBytoken($token,$beginTime,$endTime,null,null,null,$extra);
		$result = $api->syncMissingGameLogs($token);

		$this->CI->utils->debug_log("DG_API manual_sync_dg_transfer_wallet_missing_data result",$result);

		$api->clearSyncInfo($token);
	}

	/**
	 * Sync Game logs in different filter date but same response and using method syncOriginalGameLogs
	 * 
	 * @param int $gamePlatformId the game platform ID
	 * @param datetime $startDate the start date of syncing
	 * @param datetime $endDate the end date of syncing
	 */
	public function manual_sync_api_by_other_filter_date($gamePlatformId=_COMMAND_LINE_NULL,$startDate,$endDate)
	{
		$this->CI->utils->info_log('======== START manual_sync_api_by_other_filter_date ========');

		if(empty($gamePlatformId) || $gamePlatformId == '_null'){
			$this->CI->utils->error_log("game platform ID is required");
			return;
		}

		$api = $this->CI->utils->loadExternalSystemLibObject($gamePlatformId);

		if(! empty($api)){

			$now = new \DateTime();
			$startDate = !empty($startDate) ? (new \DateTime($startDate)) : $now;
			$endDate = !empty($endDate) ? (new \DateTime($endDate)) : $now;

			if($startDate > $now){
				$startDate = $now;
			}

			if($endDate > $now){
				$endDate = $now;
			}


			$token = random_string('unique');
			$extra = [
				'isManualSyncedByOtherFilterDate' => true
			];

			$api->saveSyncInfoByToken($token,$startDate,$endDate,null,null,null,$extra);
			$apiResult = $api->syncOriginalGameLogs($token);

			$this->CI->utils->debug_log('Last API RESULT >>>>>>>>',$apiResult);

			$api->clearSyncInfo($token);

		}else{
			$this->CI->error_log('CANNOT FIND API >>>>>>>>',$gamePlatformId);
		}

		$this->CI->utils->info_log('======== END manual_sync_api_by_other_filter_date ========');
	}

	/**
	 * syncGameplayOriginalGamelogsByGameType
	 *
	 * @param  datetime  $dateTimeFromStr
	 * @param  datetime  $dateTimeToStr
	 * @param  string  $gameType
	 *
	 */
    public function syncGameplayOriginalGamelogsByGameType($dateTimeFromStr , $dateTimeToStr, $gameType){
    	$api = $this->utils->loadExternalSystemLibObject(GAMEPLAY_API);
    	$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);
    	$extra = array(
    		"syncByGameType" => true,
    		"gameType" => $gameType
    	);
    	$token = random_string('unique');
    	$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, null, null, null, $extra);
    	$result = $api->syncOriginalGameLogs($token);
		$this->utils->debug_log('end sync--------------------', $result);
	}

	/**
	 * Sync Booming Original Game Logs by session_id
	 *
	 * @param int $trans_id the transaction id
	 */
	public function sync_booming_original_game_logs_by_session_id($session_id) {
		$api = $this->utils->loadExternalSystemLibObject(BOOMING_SEAMLESS_API);
		$result = $api->syncBySession($session_id);
		$this->CI->utils->debug_log('end sync-------------------',$result);
		$this->CI->utils->debug_log('synced by session_id() >>>>>>>>>>>>>>>>',$session_id);
	}

	/**
	 * overview : sync original game logs
	 *
	 * @param string $gamePlatformId
	 * @param string $dateTimeFromStr
	 * @param string $dateTimeToStr
	 * @param string $playerName
	 * @param string $timelimit
	 * @return mixed
	 */
	public function sync_original_game_logs_by_timelimit_nolimit($gamePlatformId, $dateTimeFromStr, $dateTimeToStr, $playerName = '_null', $timelimit = 30) {

		if($this->utils->getConfig('disabled_manually_sync_game_logs')){
			$this->utils->error_log('=========donnot allow sync manually game logs============================');
			return false;
		}

		set_time_limit(0);

		$today=date('Ymd');
		$this->utils->initRespTableByDate($today);

		$this->utils->info_log('=========start sync_original_game_logs_by_timelimit_nolimit============================');

		$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);
		if($playerName=='_null' || $playerName=='null'){
			$playerName='';
		}
		$gamePlatformId=intval($gamePlatformId);
		$timelimit=intval($timelimit);

		$api=$this->utils->loadExternalSystemLibObject($gamePlatformId);
		if(!empty($api)){

			$from = $dateTimeFrom;
			while (!$this->utils->gtAndEqEndTime($from, $dateTimeTo)) {

				$to = $this->utils->getNextTime($from, '+' . $timelimit . ' minutes');
				$this->utils->info_log('from', $from, 'to', $to, 'timelimit', $timelimit);

				//because we need really sync
				$ignore_public_sync=false;
				$token = random_string('unique');
				$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, null, null,
					array('ignore_public_sync' => $ignore_public_sync));

				$this->utils->debug_log('syncOriginalGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
				$rlt=$api->syncOriginalGameLogs($token);
				$this->utils->info_log('result of syncOriginalGameLogs', $rlt);

				$this->utils->debug_log('syncLostAndFound', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
				$rlt=$api->syncLostAndFound($token);
				$this->utils->info_log('result of syncLostAndFound', $rlt);
				$api->clearSyncInfo($token);

				$from = $to;
			}

		}else{
			$this->utils->error_log('NOT FOUND API', $gamePlatformId);
		}

		// $rlt = $manager->syncOriginalGameRecordsOnOnePlatform($gamePlatformId, $dateTimeFrom, $dateTimeTo, $playerName);

		$this->utils->info_log('=========end sync_original_game_logs_by_timelimit_nolimit============================');

		$this->utils->info_log('gamePlatformId', $gamePlatformId, 'dateTimeFromStr', $dateTimeFromStr,
			'dateTimeToStr', $dateTimeToStr, 'sync result', $rlt);
		// $this->returnText($msg);
		// return $msg;
	}

	/**
	 * Sync Original Game Logs with interval since some old APIs didn't handle this way of syncing
	 * For example on CQ9 the API will throw an error if the request of data has an interval of 12 hours to days of date range query for game logs
	 *
	 * Note: This is for fixing some data issue since the auto sync don't have this request of date interval with more than an hour to days of date range
	 *
	 * @param $iGamePlatformId
	 * @param $sDateTimeFromStr
	 * @param $sDateTimeToStr
	 * @param $sInterval
	 * 
	 */
	public function sync_original_game_logs_with_interval($iGamePlatformId, $sDateTimeFromStr = null, $sDateTimeToStr = null, $sInterval = '+12 hours') {
		$this->utils->debug_log('========= start sync_original_game_logs_by_interval =========');
		$this->oApi = $this->utils->loadExternalSystemLibObject($iGamePlatformId);
		if (empty($this->oApi)) {
			$this->utils->error_log('NOT FOUND API', $iGamePlatformId);
			return false;
		} else {
			$oDateTimeFrom = new \DateTime($sDateTimeFromStr);
			$oDateTimeTo = new \DateTime($sDateTimeToStr);
			list($oTodayFrom, $oTodayTo) = $this->utils->getTodayDateTimeRange();
			//set default datetime from to
			if (empty($sDateTimeFromStr)) {
				$oDateTimeFrom = $oTodayFrom;
			}
			if (empty($sDateTimeToStr)) {
				$oDateTimeTo = $oTodayTo;
			}

	        $oDateTimeFrom = new DateTime($this->oApi->serverTimeToGameTime($oDateTimeFrom->format('Y-m-d H:i:s')));
	        $oDateTimeTo = new DateTime($this->oApi->serverTimeToGameTime($oDateTimeTo->format('Y-m-d H:i:s')));

	        $oDateTimeFrom = $oDateTimeFrom->format('Y-m-d H:i:s');
	        $oDateTimeTo   = $oDateTimeTo->format('Y-m-d H:i:s');

	        $mLoopResult = array();
	        $mLoopResult[] = $this->utils->loopDateTimeStartEnd($oDateTimeFrom, $oDateTimeTo, $sInterval, function($oDateTimeFrom, $oDateTimeTo)  {
				$this->utils->info_log('START DATE ==>', $oDateTimeFrom, 'END DATE ==>', $oDateTimeTo);
				$ignore_public_sync = false;
				$sToken = random_string('unique');
				$this->oApi->saveSyncInfoByToken($sToken, $oDateTimeFrom, $oDateTimeTo, null, null, null, array('ignore_public_sync' => $ignore_public_sync));
				$mResult = $this->oApi->syncOriginalGameLogs($sToken);
				$this->utils->info_log('result of syncOriginalGameLogsPerInterval', $mResult);

				$this->utils->debug_log('syncLostAndFound', $this->oApi->getPlatformCode(), $this->oApi->getValueFromSyncInfo($sToken, 'oDateTimeFrom'), $this->oApi->getValueFromSyncInfo($sToken, 'oDateTimeTo'));
				$mResultSyncLostAndFound = $this->oApi->syncLostAndFound($sToken);
				$this->utils->debug_log('result of syncLostAndFound', $mResultSyncLostAndFound);
				$this->oApi->clearSyncInfo($sToken);
	        	return $mResult;
	        });
			$this->utils->info_log('result of syncOriginalGameLogsWithInterval', $mLoopResult);
			$this->utils->debug_log('========= end sync_original_game_logs_by_interval =========');
		}
    }

    /**
	 * syncSbobetGamelogs
	 *
	 * @param  string  $dateTimeFromStr
	 * @param  string  $dateTimeToStr
	 * @param  string  $gameType
	 *
	 */
    public function syncSbobetGamelogs($dateTimeFromStr, $dateTimeToStr, $gameType) {
    	$api = $this->utils->loadExternalSystemLibObject(SBOBET_API);
    	$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);
    	$extra = array(
    		"gameType" => $gameType,
    	);
    	$this->utils->debug_log('syncSbobetGamelogs extra--------------------', json_encode($extra));
    	$token = random_string('unique');
        $api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, null, null, null, $extra);
		$result = $api->syncOriginalGameLogs($token);
		$this->utils->debug_log('end sync--------------------', $result);
    }

    /**
	 * syncTcgDrawResults
	 *
	 * @param  string  $gameCode
	 * @param  string  $page
	 * @param  string  $showResults
	 *
	 */
    public function syncTcgDrawResults($gameCode, $page, $showResults = false) {
    	$api = $this->utils->loadExternalSystemLibObject(TCG_API);
		$result = $api->getGameResult($gameCode, $page, $showResults);
		$this->utils->debug_log('end syncTcgDrawResults--------------------', $result);
    }
}
////END OF FILE/////////
