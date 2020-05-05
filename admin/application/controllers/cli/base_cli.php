<?php

require_once dirname(__FILE__) . '/../modules/lock_app_module.php';

/**
 * only cli
 *
 *
 *
 */
class Base_cli extends CI_Controller {

	use lock_app_module;

	public $_app_prefix;

	public function __construct() {
		parent::__construct();
		//only cli
		if (!$this->input->is_cli_request()) {
			//quit
			// echo 'Not allowed';
			show_error('Not allowed', 405);
			exit;
		}

		// if ($this->utils->isLoadedMonitor()) {
		// 	newrelic_background_job(true);
		// }
		// set_error_handler(array($this, 'errorHandler'));

		$this->CI = $this;

	    $this->_app_prefix=$this->utils->getAppPrefix();

	}

	public $syncId = null;

	private function needUpdateSyncStatus($severity, $message, $filepath) {

		$rlt = ($severity == E_WARNING || $severity == E_ERROR || $severity == E_USER_ERROR || $severity == E_RECOVERABLE_ERROR);

		if ($severity == E_WARNING) {
			//check content
			$ignore = strpos($message, 'Cannot modify header information') !== FALSE;
			// $this->utils->debug_log('ignore', $ignore);
			if ($ignore) {
				$rlt = false;
			}
		}

		return $rlt && $this->syncId;
	}

	// function errorHandler($severity, $message, $filepath, $line) {

	// 	//wirte log
	// 	$msg = $this->utils->debug_log('syncId', $this->syncId, 'file', $filepath . ':' . $line, 'severity', $severity, 'message', $message);

	// 	if ($this->needUpdateSyncStatus($severity, $message, $filepath)) {
	// 		$this->utils->debug_log('update sync status');
	// 		//set error
	// 		$this->load->model(array('sync_status_model'));
	// 		$this->sync_status_model->failedSync($this->syncId, null, null, 'error file:' . $filepath . ':' . $line . ', severity:' . $severity . ', error:' . $message);
	// 	}

	// 	if (strpos($msg, 'Undefined') !== FALSE) {
	// 	 	//send to slack
	// 	 	$this->utils->sendToSlack($msg);
	// 	}
	// }

	protected function loadApi($platformCode) {
		return $this->utils->loadExternalSystemLibObject($platformCode);
		// $this->load->library('game_platform/game_platform_manager', array("platform_code" => $platformCode));
		// return $this->game_platform_manager->initApi($platformCode);
	}

	protected function getDeskeyOG() {
		return $this->config->item('DESKEY_OG');
	}

	protected function writeSyncId($key, $syncId) {
		// $syncFile = $this->oghome . '/application/logs/sync_id_' . $key;
		// file_put_contents($syncFile, $syncId);
	}

	public function returnText($msg, $return = true) {
		$this->output->append_output($msg . ($return ? "\n" : ''));
	}

	public function resetDb($db){
	        //reset db
	        $db->_reset_select();
	        $db->reconnect();
	        $db->initialize();
	}
	
}

/// END OF FILE//////////////