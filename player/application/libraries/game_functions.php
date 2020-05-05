<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Game_functions
 *
 * Game_functions library for FHI OG System
 *
 * @package		Game_functions
 * @author		Rendell Nuñez
 * @version		1.0.0
 */

class Game_functions {
	function __construct() {
		$this->ci =& get_instance();
		$this->ci->load->library(array('session'));
		$this->ci->load->model(array('player'));
	}

	function getGameHistoryDetails($gameHistoryId) {
		return $this->ci->player->getGameHistoryDetails($gameHistoryId);
	}

	function addGameHistory($data) {
		return $this->ci->player->addGameHistory($data);
	}

	function addGameHistoryDetails($data) {
		$this->ci->player->addGameHistoryDetails($data);
	}

	function updateGameHistory($data, $gameHistoryId) {
		$this->ci->player->updateGameHistory($data, $gameHistoryId);
	}

	function updateCurrentMoney($data, $player_id) {
		$this->ci->player->updateCurrentMoney($data, $player_id);
	}

	/**
     * get pt games
     *
     * @return
     */
	function getPTGames($gameType) {
        return $this->ci->gameapi->getPTGames($gameType);
        //save pw to player table
	}
}