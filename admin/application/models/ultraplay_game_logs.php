<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ultraplay_game_logs extends Base_game_logs_model {

	public function __construct() {
		parent::__construct();
	}

	protected $tableName = "ultraplay_game_logs";

	public function getGameLogStatistics($dateFrom, $dateTo) {

	}

}

///END OF FILE///////