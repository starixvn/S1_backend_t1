<?php

/**
 *
 * run shell in thread, sync, should wait result
 *
 */
class Shell_thread extends Thread {

	public function __construct() {
	}

	public $cmd = null;
	public $result = null;

	public function run() {
		$this->utils->debug_log("try start", $this->cmd);
		if (!empty($this->cmd)) {
			//start it
			$str = shell_exec($this->cmd);
			$this->utils->debug_log($str);
			$result = $str;
		}
	}

}
///END OF FILE/////////////