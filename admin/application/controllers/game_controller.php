<?php

class Game_Controller extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library(array('form_validation', 'template', 'pagination', 'permissions', 'game_functions', 'player_manager'));

		$game_log = array();

		$this->permissions->checkSettings();
		$this->permissions->setPermissions();
	}

	/**
	 * set message for users
	 *
	 * @param	int
	 * @param   string
	 * @return  set session user data
	 */
	public function alertMessage($type, $message) {
		switch ($type) {
		case '1':
			$show_message = array(
				'result' => 'success',
				'message' => $message,
			);
			$this->session->set_userdata($show_message);
			break;

		case '2':
			$show_message = array(
				'result' => 'danger',
				'message' => $message,
			);
			$this->session->set_userdata($show_message);
			break;

		case '3':
			$show_message = array(
				'result' => 'warning',
				'message' => $message,
			);
			$this->session->set_userdata($show_message);
			break;
		}
	}

	/**
	 * Loads template for view based on regions in
	 * config > template.php
	 *
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_js('resources/js/player_management/player_management.js');

		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());
	}

	public function game($data = '') {
		$data['player'] = $this->player_manager->getPlayerAccount(1);
		$this->loadTemplate('Player Management', '', '', 'player');
		$this->template->write_view('main_content', 'player_management/game', $data);
		$this->template->render();
	}

	public function postGame($player_id) {
		$this->form_validation->set_rules('bet', 'Bet', 'trim|required|xss_clean|numeric');

		if ($this->form_validation->run() == false) {
			$this->game();
		} else {
			$bet = $this->input->post('bet');

			$currentMoney = $this->player_manager->getPlayerAccount($player_id);

			if ($currentMoney['amount'] == 0) {
				$message = "Opss, You dont have any money. Please load your account to continue your game";
				$this->alertMessage(2, $message);
				$this->game();
			} elseif ($bet > $currentMoney['amount']) {
				$message = "Bet must not exceed to your current money. Betting denied!";
				$this->alertMessage(2, $message);
				$this->game();
			} else {
				$result = '';
				$decision = '';
				$win = null;
				$loss = null;
				$game_begin = date("Y-m-d H:i:s");
				$data = null;

				if ($this->session->userdata('game_begin') == '') {
					$this->session->set_userdata('game_begin', $game_begin);
				}

				if ($this->session->userdata('win') != '') {
					$win = $this->session->userdata('win');
				} else {
					$win = 0;
				}

				if ($this->session->userdata('loss') != '') {
					$loss = $this->session->userdata('loss');
				} else {
					$loss = 0;
				}

				$randomNumber = $this->randomizer();

				if ($randomNumber % 2 == 0) {
					$result = $randomNumber;
					$decision = "Loss";

					$loss++;
					$this->session->set_userdata('loss', $loss);

					$bet = $currentMoney['amount'] - $bet;
					$data = array(
						'amount' => $bet,
					);
					$this->game_functions->updateCurrentMoney($data, $currentMoney['playerAccountId'], $player_id);
				} elseif ($randomNumber % 2 == 1) {
					$result = $randomNumber;
					$decision = "Win";

					$win++;
					$this->session->set_userdata('win', $win);

					$bet = $currentMoney['amount'] + $bet;
					$data = array(
						'amount' => $bet,
					);
					$this->game_functions->updateCurrentMoney($data, $currentMoney['playerAccountId'], $player_id);
				} else {
					$result = $randomNumber;
					$decision = "Try Again";
				}

				$data['result'] = $result;
				$data['bet'] = $this->input->post('bet');
				$data['time'] = date("Y-m-d H:i:s");
				$data['decision'] = $decision;
				$data['profit_loss'] = $decision == 'Win' ? '+' . $this->input->post('bet') : '-' . $this->input->post('bet');

				if ($this->session->userdata('game_log') != null) {
					$this->game_log = $this->session->userdata('game_log');
					array_unshift($this->game_log, $data);
				} else {
					$this->game_log[0] = $data;
				}

				$this->session->set_userdata('game_log', $this->game_log);

				$this->game($data);
			}
		}
	}

	public function endGame($player_id) {
		//get data to be saved
		$game_type = 'WhatTa-Game';
		$win = $this->session->userdata('win');
		$loss = $this->session->userdata('loss');
		$game_begin = $this->session->userdata('game_begin');
		$game_end = date("Y-m-d H:i:s");

		//create in game history
		$data = array(
			'playerId' => $player_id,
			'gameType' => $game_type,
			'status' => 1,
		);
		$this->game_functions->insertGame($data);

		$game_history = $this->game_functions->getGameHistory($player_id, $game_type);

		$data = array(
			'gameHistoryId' => $game_history['gameHistoryId'],
			'gameType' => $game_type,
			'gameBegin' => $game_begin,
			'gameEnd' => $game_end,
			'totalWin' => $win,
			'totalLoss' => $loss,
			'status' => 1,
		);
		$this->game_functions->insertGameDetails($data);

		$data = array(
			'win' => '',
			'loss' => '',
			'game_begin' => '',
		);

		$this->session->unset_userdata($data);
		$this->session->unset_userdata('game_log');

		$message = "Thank you for playing :)";
		$this->alertMessage(1, $message);
		$this->game($data);
	}

	public function randomizer() {
		// $seed = str_split('0123456789'); // and any other characters
		//    shuffle($seed); // probably optional since array_is randomized; this may be redundant
		//    $randomNumber = array_rand($seed, 1);

		return rand(1, 100);
	}

	public function typeOfAction($player_id) {
		$action = $this->input->post('action');

		switch ($action) {
		case 'Start':
			$this->postGame($player_id);
			break;

		case 'End':
			$this->endGame($player_id);
			break;

		default:
			$this->game();
			break;
		}

	}

}