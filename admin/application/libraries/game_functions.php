<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

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
		$this->ci = &get_instance();
		$this->ci->load->library(array('session'));
		$this->ci->load->model(array('game', 'game_provider_auth'));
	}

	function getGameProviders($player_id) {
		//return $this->ci->game_provider_auth->getGameProviders($player_id);
		return $this->ci->game->getPlayerGame($player_id);
	}

	function toggleGameProvider($player_id, $game_provider_id, $status) {
		//$this->ci->game_provider_auth->toggleGameProvider($player_id, $game_provider_id);
		$data = array('blocked' => $status);
		$this->ci->game->changePlayerGameBlocked($player_id, $game_provider_id, $data);
	}

	function getGameHistory($player_id, $game_type) {
		$result = $this->ci->game->getGameHistory($player_id, $game_type);
		return $result;
	}

	function insertGame($data) {
		$this->ci->game->insertGame($data);
	}

	function insertGameDetails($data) {
		$this->ci->game->insertGameDetails($data);
	}

	function updateCurrentMoney($data, $player_account_id, $player_id) {
		$this->ci->game->updateCurrentMoney($data, $player_account_id, $player_id);
	}

	function getPlayerBlockedGames($player_id) {
		return $this->ci->game->getPlayerBlockedGames($player_id);
	}
}