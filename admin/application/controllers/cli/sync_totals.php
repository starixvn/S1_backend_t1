<?php
require_once dirname(__FILE__) . "/base_cli.php";

/**
 * Class Sync_totals
 *
 * General behaviors include :
 *
 * * Sync totals
 * * Sync service start
 *
 * @category Command Line
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Sync_totals extends Base_cli {

	const LOG_TITLE = '[sync_totals_controller]';

	public $oghome = null;

	/**
	 * overview : Sync_game_records constructor.
	 */
	public function __construct() {
		parent::__construct();
		// load game platform
		// $this->load->library(array('lib_gearman', 'utils'));
		// $this->config->set_item('app_debug_log', APPPATH . 'logs/game_api.log');

		$this->config->set_item('print_log_to_console', true);

		$this->oghome = realpath(dirname(__FILE__) . "/../../../");

		// register_shutdown_function(array($this, 'clean_shutdown'));
	}

	/**
	 * overview : sync service start
	 *
	 * @param string $dateTimeFromStr
	 */
	public function sync_totals_start($dateTimeFromStr = '-10 minutes') {
		//never stop
		set_time_limit(0);
		// if (!$this->isRuning()) {
		// from -> now
		$dateTimeFromStr = (new \DateTime($dateTimeFromStr))->format('Y-m-d H:i:s');
		$dateTimeToStr = date('Y-m-d H:i:s');
		// $this->remove_stop_flag();

		// $this->install_sig_handler();

		$this->utils->debug_log('runing... started from:' . $dateTimeFromStr . 'to:' . $dateTimeToStr);
		// echo "runing... started from " . $dateTimeFrom->format(\DateTime::ISO8601) . "\n";

		$mark = 'benchSyncTotals';
		//run sync
		while (true) {
			//check stop flag first
			// if ($this->exists_stop_flag()) {
			// 	break;
			// }
			$syncSleepTime = $this->config->item('sync_totals_sleep_seconds');

			$this->utils->markProfilerStart($mark);

			$this->utils->debug_log("try start", $dateTimeFromStr, $dateTimeToStr);

			$file_list=[];
			//run
			$cmd = $this->getCommandLine('sync', array($dateTimeFromStr, $dateTimeToStr), $file_list);

			$this->utils->debug_log('start sync '.$mark, $cmd);
			shell_exec($cmd);
			$this->utils->debug_log('end sync '.$mark);

			if(!empty($file_list)){
				foreach ($file_list as $f) {
					$this->utils->debug_log('delete file: '.$f);

					if($this->utils->getConfig('always_delete_total_tmp_shell')){
						unlink($f);
					}
				}
			}

			$this->utils->markProfilerEndAndPrint($mark);

			$this->utils->debug_log('sleep...', $syncSleepTime, 'from', $dateTimeFromStr, 'to', $dateTimeToStr);
			// echo "sleep...\n";
			for ($i = 0; $i < $syncSleepTime; $i++) {
				// if ($this->exists_stop_flag()) {
				// 	break;
				// }
				sleep(1);
			}

			//set next
			$dateTimeFromStr = $dateTimeToStr;
			$dateTimeToStr = date('Y-m-d H:i:s');

			$this->utils->debug_log('next from', $dateTimeFromStr, 'to', $dateTimeToStr);
			gc_collect_cycles();
		}

		$this->utils->debug_log(self::LOG_TITLE, 'stopped');
	}

	/**
	 * overview : get command line
	 *
	 * @param string $func
	 * @param array $args
	 * @param array $file_list
	 * @return string
	 */
	protected function getCommandLine($func, $args, &$file_list) {
		$og_home = $this->oghome;
		$php_str=$this->utils->find_out_php();
		// echo "try start\n";

		$argStr = '';
		if (!empty($args)) {
			foreach ($args as $val) {
				$argStr .= ' "' . $val . '"';
			}
		}

		$cmd = $php_str . ' ' . $og_home . '/shell/ci_cli.php cli/sync_totals/' . $func . $argStr;

		return $this->utils->generateCommonLine($cmd, true, $func, $file_list);

//		return $cmd;
	}

	public function sync($dateTimeFromStr = null, $dateTimeToStr = null,
								$playerName = null, $queue_token=null){

		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		if($playerName=='_null'){
			$playerName=null;
		}

		$this->utils->debug_log('=========start sync_total============================', $dateTimeFromStr, $dateTimeToStr, $playerName, $queue_token);

		$this->load->model(array('sync_status_model', 'queue_result'));

		if(!empty($queue_token)){
			$this->utils->debug_log('append result ', _REQUEST_ID, $queue_token);
			//update queue_results
			$this->queue_result->appendResult($queue_token, [
				'request_id'=>_REQUEST_ID, 'func'=>'sync_total', 'dateTimeFromStr'=>$dateTimeFromStr,
				'$dateTimeToStr'=>$dateTimeToStr]);
		}

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

		$manager = $this->utils->loadGameManager();

		$rlt = $manager->syncTotalStatsAll($dateTimeFrom, $dateTimeTo,
			$playerName, null);

        if($this->utils->isEnabledFeature('enabled_transfer_condition')) {
            $this->load->model('transfer_condition');
            $this->transfer_condition->syncPlayerTotalBets();
        }

		$this->utils->debug_log('=========end sync_total============================', $dateTimeFrom, $dateTimeTo, $rlt);

	}

}

/// END OF FILE//////////////
