<?php
// if (PHP_SAPI === 'cli') {
// 	exit('No web access allowed');
// }
require_once dirname(__FILE__) . "/base_cli.php";

class Migrate extends Base_cli {

	private $target_version;

	function __construct() {
		parent::__construct();
		$this->load->library('migration');
		$this->config->set_item('print_log_to_console', true);
		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		$this->config->load('migration');
		$this->target_version = $this->config->item('migration_version');

	}

	function index() {

		$messageList=null;
		$rlt=null;
		if($this->utils->isEnabledMDB()){
	        //try run on multiple db
	        $rlt=$this->utils->foreachMultipleDBToCIDB(function($db)
	        		use(&$messageList){

	        	$this->utils->info_log('exec migration on '.$db->getOgTargetDB());

		        $tableName=$this->utils->initRespTableByDate(date('Ymd'));
	        	$succ=$this->migration->migrate();
	        	if(!$succ){
	        		$messageList[$db->getOgTargetDB()]=$this->migration->error_string();
	        	}
	        	return $succ;
	        });

			$this->utils->debug_log('migrate result================================', $rlt);

	        $success=false;
	        foreach ($rlt as $db_name => $succ) {
	        	$success=$succ;
	        	if(!$succ){
	        		break;
	        	}
	        }
		}else{
	        $success=$this->migration->migrate();
	        $rlt['default']=$success;
	        $messageList['default']=$this->migration->error_string();
		}

		if (!$success) {
			$this->utils->error_log('migrate failed================================', $rlt, $messageList);
			$status_code=500;
			set_status_header($status_code);

			exit(1);
		} else {

			if($this->utils->isEnabledMDB()){
		        $rlt=$this->utils->foreachMultipleDBToCIDB(function($db){

		        	$this->utils->info_log('exec postMigration on '.$db->getOgTargetDB());

			        return $this->postMigration($db);
		        });
		        $success=false;
		        foreach ($rlt as $db_name => $succ) {
		        	$success=$succ;
		        	if(!$succ){
		        		break;
		        	}
		        }
			}else{
				$success=$this->postMigration($this->db);
				$rlt['default']=$success;
			}

			if (!$success) {
				$this->utils->error_log('post migration failed================================', $rlt);
				$status_code=500;
				set_status_header($status_code);

				exit(1);
			}

			if($this->utils->isEnabledMDB()){
		        //compare version
				//get db version
		        $rlt=$this->utils->foreachMultipleDBToCIDB(function($db){

		        	$db->select('version')->from('migrations');
		        	$db_version=$this->player_model->runOneRowOneField('version', $db);

		        	$this->utils->info_log('check migration version on '.$db->getOgTargetDB(),
		        		$this->target_version, $db_version);

		        	return $this->target_version==$db_version;
		        });
		        $success=false;
		        foreach ($rlt as $db_name => $succ) {
		        	$success=$succ;
		        	if(!$succ){
		        		break;
		        	}
		        }
		    }else{
	        	$this->db->select('version')->from('migrations');
	        	$db_version=$this->player_model->runOneRowOneField('version', $this->db);

	        	$this->utils->info_log('check migration version on '.$this->db->getOgTargetDB(),
	        		$this->target_version, $db_version);

	        	$success=$this->target_version==$db_version;
		    }
			if (!$success) {
				$this->utils->error_log('migration version is wrong ================================', $rlt);
				$status_code=500;
				set_status_header($status_code);

				exit(1);
			}

			$this->utils->debug_log("done. migrate version is " . $this->target_version);

		}
	}

	private function postMigration($db){

		$this->load->model(array('operatorglobalsettings', 'roles', 'system_feature', 'external_system','player_model','game_description_model'));

		$this->operatorglobalsettings->startTrans();

		$this->operatorglobalsettings->syncAllOperatorSettings();

		$succ = $this->operatorglobalsettings->endTransWithSucc();

		$this->utils->info_log('syncAllOperatorSettings: '. $succ);

		//also add cron jobs
		$this->utils->info_log('addAllCronJobs: '.$this->operatorglobalsettings->addAllCronJobs());

		//add functions
		$this->roles->startTrans();

		$this->roles->syncAllFunctions();

		$succ = $this->roles->endTransWithSucc();

		$this->utils->info_log('syncAllFunctions: '. $succ);

		if ($this->db->table_exists('system_features')) {
			// $this->roles->startTrans();

			$succ=$this->lockAndTrans(Utils::LOCK_ACTION_SYSTEM_FEATURE, 0, function(){
				return $this->system_feature->syncAllFeatures();
			});

			// $succ=$this->roles->endTransWithSucc();

			$this->utils->info_log('syncAllFeatures: '. $succ);
		}

		$this->external_system->startTrans();

		$this->external_system->syncExternalSystem();

		$succ=$this->external_system->endTransWithSucc();

		$this->utils->info_log('syncExternalSystem: '. $succ);

		$this->player_model->startTrans();

        $this->player_model->createT1TestPlayers();

        $this->utils->info_log('createT1TestPlayers: '. $succ);

        $succ=$this->player_model->endTransWithSucc();

        $this->game_description_model->startTrans();

		$this->game_description_model->syncUnknownGame();

		$succ=$this->game_description_model->endTransWithSucc();

        $this->utils->info_log('syncUnknownGame: '. $succ);

        return $succ;
	}

	function rollback($version = null) {
		if (empty($version)) {
			$this->config->load('migration');
			$version = $this->config->item('migration_version') - 1;
		}
		$this->utils->info_log('try rollback to: '.$version);
		$rlt=null;
		$messageList=null;
	    $success=false;
		if($this->utils->isEnabledMDB()){
	        $rlt=$this->utils->foreachMultipleDBToCIDB(function($db)
        		use(&$messageList, $version){
				$succ=$this->migration->version($version);
	        	if(!$succ){
	        		$messageList[$db->getOgTargetDB()]=$this->migration->error_string();
	        	}
				return $succ;
        	});
	        foreach ($rlt as $db_name => $succ) {
	        	$success=$succ;
	        	if(!$succ){
	        		break;
	        	}
	        }
	    }else{
			$success=$this->migration->version($version);
			$rlt['default']=$success;
	        $messageList['default']=$this->migration->error_string();
	    }

		// log_message('debug', "rollback to " . $version);
		if (!$success) {
			$this->utils->error_log('rollback is failed ================================', $rlt, $messageList);
			$status_code=500;
			set_status_header($status_code);

			exit(1);
		}else{
			$this->utils->info_log('done rollback to: '.$version);
		}

		// if (!$this->migration->version($version)) {
		// 	show_error($this->migration->error_string());
		// } else {
		// 	$msg = $this->utils->debug_log("rollback version " . $version);
		// 	$this->returnText($msg);
		// }
	}
}
////END OF FILE//////////