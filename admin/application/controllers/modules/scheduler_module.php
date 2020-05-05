<?php
trait scheduler_module {

// * * * * *     root   /bin/bash /home/vagrant/Code/og_sync/admin/shell/command.sh scheduler  >> /var/log/og/scheduler.log 2>&1

	/**
	 * cron settings
	 *
	 *
	 *
	 */
	public function scheduler() {

		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		$OGHOME = realpath(APPPATH . '../../');

		//load all scheduler
		require_once($OGHOME.'/admin/application/libraries/scheduler/GO/Job/Exceptions/InvalidFactoryException.php');
		require_once($OGHOME.'/admin/application/libraries/scheduler/GO/Job/Job.php');
		require_once($OGHOME.'/admin/application/libraries/scheduler/GO/Job/JobFactory.php');
		require_once($OGHOME.'/admin/application/libraries/scheduler/GO/Job/Closure.php');
		require_once($OGHOME.'/admin/application/libraries/scheduler/GO/Job/Php.php');
		require_once($OGHOME.'/admin/application/libraries/scheduler/GO/Job/Raw.php');
		require_once($OGHOME.'/admin/application/libraries/scheduler/GO/Services/Filesystem.php');
		require_once($OGHOME.'/admin/application/libraries/scheduler/GO/Services/Interval.php');
		require_once($OGHOME.'/admin/application/libraries/scheduler/GO/Scheduler.php');

		// $this->utils->loadComposerLib();
		$scheduler = new GO\Scheduler([
			'emailFrom' => $this->utils->getConfig('cronjob_email_from'),
			'timezone' => $this->utils->getConfig('current_php_timezone'), // 'Asia/Hong_Kong',
		]);

		$LOG_DIR = realpath(APPPATH . 'logs'); //.'/'.$this->_app_prefix;

		// @mkdir($LOG_DIR, 0777 , true);

		$this->utils->debug_log('oghome', $OGHOME, 'log dir', $LOG_DIR);

		//get cronjob from operator_settings
		$cronjobOptList = $this->operatorglobalsettings->getAllCronJobs();
		$allJobList = $this->utils->getConfig('all_cron_jobs');
		$cronjobList = array();
		foreach ($cronjobOptList as $cronjobOpt) {
			if ($cronjobOpt['value'] == 'true' || $cronjobOpt['value'] == '1') {
				if(array_key_exists($cronjobOpt['name'], $allJobList)){
					$cronjobList[$cronjobOpt['name']] = $allJobList[$cronjobOpt['name']];
				}else{
					$this->utils->debug_log('ignore unknown cronjob',$cronjobOpt['name']);
				}
			}
		}
		$msg='';
		$immediately_run = '* * * * *';
		$always_run_cron_job = $this->utils->getConfig('always_run_cron_job');

		$monitor_cron_job = $this->utils->getConfig('monitor_cron_job');

		//run cronjob
		foreach ($cronjobList as $name => $jobInfo) {
			// $this->utils->debug_log('run job', $jobInfo);
			//replace cmd
			$cmd = str_replace('{OGHOME}', $OGHOME, $jobInfo['cmd']);
			$cron = $jobInfo['cron'];
			$logfile = $LOG_DIR . '/' . $name . '.log';

			if (!empty($always_run_cron_job) && in_array($name, $always_run_cron_job)) {
				$cron = $immediately_run;
				$this->utils->debug_log('change ' . $name . ' to immediately_run');
			}

			if(!empty($monitor_cron_job) && in_array($name, $monitor_cron_job)){
				$this->utils->debug_log('try scheduler',$name, $jobInfo);
			}

			$cronExp=Cron\CronExpression::factory($cron);

			if($cronExp->isDue()){
				$msg.='run job '.' at '.$cron;
				$this->utils->debug_log('run job', $cmd, 'name', $name, 'cron', $cron, 'logfile', $logfile);

				$is_blocked=false;
				$cmd=$this->utils->generateCommonLine($cmd, $is_blocked, $name);
				//run
	            raw_debug_log(pclose(popen($cmd, 'r')));
			}

			// $this->utils->debug_log('run job', $cmd, 'name', $name, 'cron', $cron, 'logfile', $logfile);
			// $scheduler->raw($cmd)->at($cron)->output($logfile, true)->doNotOverlap();
			//check scheduler and run

			// $this->utils->debug_log('memory', round(memory_get_usage() / 1024) . 'k', 'peak memory', round(memory_get_peak_usage() / 1024) . 'k');
		}

		//debug
		// $scheduler->raw($OGHOME . '/admin/shell/clear_sessions.sh')->at('* * * * *')->output($LOG_DIR . '/cronjob_clear_sessions.log', true);
		// $cronjob_email_to = $this->utils->getConfig('cronjob_email_to');

		// $msg = $this->utils->debug_log('scheduler run', $scheduler->run());
		// $this->returnText($msg);
	}
}
///END OF FILE////////////////