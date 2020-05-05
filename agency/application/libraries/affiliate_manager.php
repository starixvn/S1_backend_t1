<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Affiliate Manager
 *
 * Affiliate Manager library for FHI OG System
 *
 * @package		Affiliate Manager
 * @author		Johann Merle
 * @version		1.0.0
 */

class Affiliate_manager {
	private $error = array();

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('game_pt_api', 'language_function'));
		$this->ci->load->model(array('affiliatemodel'));
	}

	/**
	 * Will randomize alphanumeric and special characters
	 *
	 * @param 	string
	 * @return	string
	 */
	public function randomizer($name) {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789' //!@#$%^&*()
			 . $name); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$randomPassword = '';
		foreach (array_rand($seed, 8) as $k) {
			$randomPassword .= $seed[$k];
		}

		return $randomPassword;
	}

	/**
	 * login
	 *
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	public function login($username, $password) {
		return $this->ci->affiliatemodel->login($username, $password);
	}

	/**
	 * get all affiliates from affiliate table
	 *
	 * @return	array
	 */
	public function getAllAffiliates() {
		return $this->ci->affiliatemodel->getAllAffiliates();
	}

	/**
	 * get affiliate by affiliateId from affiliate table
	 *
	 * @param	int
	 * @return	array
	 */
	public function getAffiliateById($affiliate_id) {
		return $this->ci->affiliatemodel->getAffiliateById($affiliate_id);
	}

	/* register affiliate */

	/**
	 * get affiliate game by affiliateId from affiliategame table
	 *
	 * @param	int
	 * @return	array
	 */
	public function getAffiliateGameById($affiliate_id) {
		return $this->ci->affiliatemodel->getAffiliateGameById($affiliate_id);
	}

	/**
	 * add affiliate to affiliate table
	 *
	 * @param	array
	 * @return	int
	 */
	public function addAffiliate($affiliate) {
		return $this->ci->affiliatemodel->addAffiliate($affiliate);
	}

	/**
	 * add affiliate payout options to affiliatepayout table
	 *
	 * @param	array
	 * @return	int
	 */
	public function addAffiliatePayout($affiliatepayout) {
		return $this->ci->affiliatemodel->addAffiliatePayout($affiliatepayout);
	}

	/**
	 * get registered fields
	 *
	 * @param  type
	 * @return array
	 */
	public function getRegisteredFields($type) {
		return $this->ci->affiliatemodel->getRegisteredFields($type);
	}

	/**
	 * check registration fields if visible
	 *
	 * @param  type
	 * @return array
	 */
	public function checkRegisteredFieldsIfVisible($field_name) {
		$registered_fields = $this->getRegisteredFields(2);

		foreach ($registered_fields as $key => $value) {
			if ($value['field_name'] == $field_name) {
				return $value['visible'];
			}
		}
	}

	/**
	 * check registration fields if required
	 *
	 * @param  type
	 * @return array
	 */
	public function checkRegisteredFieldsIfRequired($field_name) {
		$registered_fields = $this->getRegisteredFields(2);

		foreach ($registered_fields as $key => $value) {
			if ($value['field_name'] == $field_name) {
				return $value['required'];
			}
		}
	}

	/* end of register affiliate */

	/* edit affiliate */

	/**
	 * edit affiliates by affiliateId to affiliates table
	 *
	 * @param	array
	 * @param	int
	 */
	public function editAffiliates($data, $affiliate_id) {
		$this->ci->affiliatemodel->editAffiliates($data, $affiliate_id);
	}

	/**
	 * get all payment method of affiliate
	 *
	 * @param	int
	 * @param	int
	 */
	public function getPaymentById($affiliate_id) {
		return $this->ci->affiliatemodel->getPaymentById($affiliate_id);
	}

	/**
	 * add payment
	 *
	 * @param	array
	 * @param	int
	 */
	public function addPayment($data) {
		$this->ci->affiliatemodel->addPayment($data);
	}

	/**
	 * edit payment
	 *
	 * @param	int
	 * @param	int
	 */
	public function editPayment($data, $payment_id) {
		$this->ci->affiliatemodel->editPayment($data, $payment_id);
	}

	/**
	 * delete payment bank info
	 *
	 * @param	int
	 */
	public function deletePaymentInfo($payment_id) {
		$this->ci->affiliatemodel->deletePaymentInfo($payment_id);
	}

	/**
	 * get all payment method of affiliate
	 *
	 * @param	int
	 * @param	int
	 */
	public function getPaymentByPaymentId($affiliate_payment_id) {
		return $this->ci->affiliatemodel->getPaymentByPaymentId($affiliate_payment_id);
	}

	/* end of edit affiliate */

	/* banner settings */

	/**
	 * get all banner details from banner table
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getAllBanner($search, $limit, $offset) {
		return $this->ci->affiliatemodel->getAllBanner($search, $limit, $offset);
	}

	/**
	 * get all domains from domain table
	 *
	 * @return	array
	 */
	public function getAllDomain() {
		return $this->ci->affiliatemodel->getAllDomain();
	}

	/* end of banner settings */

	/* traffic stats */

	/**
	 * get all players ids under affiliate in players table
	 *
	 * @param	int
	 * @return	array
	 */
	public function getAllPlayerIdsUnderAffiliate($affiliate_id) {
		$result = $this->ci->affiliatemodel->getAllPlayersUnderAffiliate($affiliate_id, null, null);
		$ids = null;

		foreach ($result as $key => $value) {
			if (empty($ids)) {
				$ids = $value['playerId'];
			} else {
				$ids = $ids . "," . $value['playerId'];
			}
		}

		return $ids;
	}

	/**
	 * get all players under affiliate in players tables
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getAllPlayersUnderAffiliate($affiliate_id, $date_from, $date_to) {
		return $this->ci->affiliatemodel->getAllPlayersUnderAffiliate($affiliate_id, $date_from, $date_to);
	}

	/**
	 * get traffic stats base on conditions
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getTrafficStats() {
		$traffic = $this->ci->affiliatemodel->getTrafficStats(null, null);
		$affiliate_id = $this->ci->session->userdata('affiliateId');
		$player_count = count($this->getAllPlayersUnderAffiliate($affiliate_id, null, null));

		$result = array();
		$cnt = 0;

		foreach ($traffic as $key => $value) {
			$cnt++;

			if ($cnt <= $player_count) {
				array_push($result, $value);
			}
		}

		return $result;
	}

	/**
	 * get traffic stats base on conditions
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getTodayTrafficStats($start_date, $end_date) {
		return $this->ci->affiliatemodel->getTrafficStats($start_date, $end_date);
	}

	/**
	 * get daily traffic stats base on conditions
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getDailyTrafficStats($start_date, $end_date) {
		$aff_stats = $this->ci->affiliatemodel->getTrafficStats($start_date, $end_date);

		$result = array();
		$data = array();

		$date = null;

		foreach ($aff_stats as $key => $value) {
			$results = array();

			if ($date != $value['date']) {
				$date = $value['date'];
				$player_count = 0;
				$deposit_amount = 0;
				$withdrawal_amount = 0;

				$pt_bet = 0;
				$ag_bet = 0;
				$total_bet = 0;

				$pt_win = 0;
				$ag_win = 0;
				$total_win = 0;

				$pt_loss = 0;
				$ag_loss = 0;
				$total_loss = 0;

				$total_net_gaming = 0;
				$total_bonus = 0;

				$results = $this->search($aff_stats, 'date', $value['date']);

				foreach ($results as $key => $value) {
					$player_count++;

					$deposit_amount += $value['deposit_amount'];
					$withdrawal_amount += $value['withdrawal_amount'];

					$pt_bet += $value['pt_bets'];
					$ag_bet += $value['ag_bets'];
					$total_bet += $value['total_bets'];

					$pt_win += $value['pt_wins'];
					$ag_win += $value['ag_wins'];
					$total_win += $value['total_wins'];

					$pt_loss += $value['pt_loss'];
					$ag_loss += $value['ag_loss'];
					$total_loss += $value['total_loss'];

					/*$total_net_gaming += $value['total_net_gaming'];*/
					$total_bonus += $value['total_bonus'];
				}

				$data = array(
					'total_players' => $player_count,
					'deposit_amount' => $deposit_amount,
					'withdrawal_amount' => $withdrawal_amount,
					'pt_bets' => $pt_bet,
					'ag_bets' => $ag_bet,
					'total_bets' => $total_bet,
					'pt_wins' => $pt_win,
					'ag_wins' => $ag_win,
					'total_wins' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'date' => $date,
				);
				array_push($result, $data);
			}
		}

		return $result;
	}

	function search($array, $key, $value) {
		$results = array();
		$this->search_r($array, $key, $value, $results);
		return $results;
	}

	function search_r($array, $key, $value, &$results) {
		if (!is_array($array)) {
			return;
		}

		if (isset($array[$key]) && $array[$key] == $value) {
			$results[] = $array;
		}

		foreach ($array as $subarray) {
			$this->search_r($subarray, $key, $value, $results);
		}
	}

	/**
	 * get Weekly Traffic Statistics
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return 	array
	 */
	public function getWeeklyTrafficStats($start_date, $end_date) {
		$aff_stats = array_reverse($this->getDailyTrafficStats($start_date, $end_date));

		$result = array();

		$player_count = 0;
		$deposit_amount = 0;
		$withdrawal_amount = 0;

		$pt_bet = 0;
		$ag_bet = 0;
		$total_bet = 0;

		$pt_win = 0;
		$ag_win = 0;
		$total_win = 0;

		$pt_loss = 0;
		$ag_loss = 0;
		$total_loss = 0;

		$total_net_gaming = 0;
		$total_bonus = 0;
		$total_players = 0;

		$stats_count = count($aff_stats);
		$counter = 0;

		foreach ($aff_stats as $key => $value) {
			$counter++;

			if ($player_count == 0) {
				$date_start = date('Y-m-d', strtotime($value['date']));
				$daycount = 7 - date("N", strtotime($date_start));
				$date_end = date('Y-m-d', strtotime($date_start . '+' . $daycount . ' day'));
			}

			$player_count++;
			$deposit_amount += $value['deposit_amount'];
			$withdrawal_amount += $value['withdrawal_amount'];

			$pt_bet += $value['pt_bets'];
			$ag_bet += $value['ag_bets'];
			$total_bet += $value['total_bets'];

			$pt_win += $value['pt_wins'];
			$ag_win += $value['ag_wins'];
			$total_win += $value['total_wins'];

			$pt_loss += $value['pt_loss'];
			$ag_loss += $value['ag_loss'];
			$total_loss += $value['total_loss'];

			/*$total_net_gaming += $value['total_net_gaming'];*/
			$total_bonus += $value['total_bonus'];
			$total_players = $value['total_players'];

			if ($date_end == $value['date']) {
				$data = array(
					'deposit_amount' => $deposit_amount,
					'withdrawal_amount' => $withdrawal_amount,
					'pt_bets' => $pt_bet,
					'ag_bets' => $ag_bet,
					'total_bets' => $total_bet,
					'pt_wins' => $pt_win,
					'ag_wins' => $ag_win,
					'total_wins' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_players' => $total_players,
					'date' => ($date_start == $date_end) ? $date_start : $date_start . " - " . $date_end,
				);
				array_push($result, $data);

				$player_count = 0;
				$deposit_amount = 0;
				$withdrawal_amount = 0;

				$pt_bet = 0;
				$ag_bet = 0;
				$total_bet = 0;

				$pt_win = 0;
				$ag_win = 0;
				$total_win = 0;

				$pt_loss = 0;
				$ag_loss = 0;
				$total_loss = 0;

				$total_net_gaming = 0;
				$total_bonus = 0;
				$total_players = 0;
			} else if ($counter == $stats_count) {
				$data = array(
					'deposit_amount' => $deposit_amount,
					'withdrawal_amount' => $withdrawal_amount,
					'pt_bets' => $pt_bet,
					'ag_bets' => $ag_bet,
					'total_bets' => $total_bet,
					'pt_wins' => $pt_win,
					'ag_wins' => $ag_win,
					'total_wins' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_players' => $total_players,
					'date' => ($date_start == $value['date']) ? $date_start : $date_start . " - " . $value['date'],
				);
				array_push($result, $data);
			}
		}

		return $result;
	}

	/**
	 * get Monthly Traffic Statistics
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return 	array
	 */
	public function getMonthlyTrafficStats($start_date, $end_date) {
		$aff_stats = array_reverse($this->getDailyTrafficStats($start_date, $end_date));

		$result = array();

		$deposit_amount = 0;
		$withdrawal_amount = 0;

		$pt_bet = 0;
		$ag_bet = 0;
		$total_bet = 0;

		$pt_win = 0;
		$ag_win = 0;
		$total_win = 0;

		$pt_loss = 0;
		$ag_loss = 0;
		$total_loss = 0;

		$total_net_gaming = 0;
		$total_bonus = 0;
		$total_players = 0;

		$stats_count = count($aff_stats);
		$counter = 0;
		$month = null;

		foreach ($aff_stats as $key => $value) {
			$counter++;

			if ($month == null) {
				$month = date('F', strtotime($value['date']));
			}

			$new_month = date('F', strtotime($value['date']));

			if ($new_month != $month) {
				$data = array(
					'deposit_amount' => $deposit_amount,
					'withdrawal_amount' => $withdrawal_amount,
					'pt_bets' => $pt_bet,
					'ag_bets' => $ag_bet,
					'total_bets' => $total_bet,
					'pt_wins' => $pt_win,
					'ag_wins' => $ag_win,
					'total_wins' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_players' => $total_players,
					'date' => $month,
					'first_date' => date('Y-m-01', strtotime($date)),
					'last_date' => date('Y-m-t', strtotime($date)),
				);
				array_push($result, $data);

				$deposit_amount = 0;
				$withdrawal_amount = 0;

				$pt_bet = 0;
				$ag_bet = 0;
				$total_bet = 0;

				$pt_win = 0;
				$ag_win = 0;
				$total_win = 0;

				$pt_loss = 0;
				$ag_loss = 0;
				$total_loss = 0;

				$total_net_gaming = 0;
				$total_bonus = 0;
				$total_players = 0;
				$month = $new_month;
			}

			$deposit_amount += $value['deposit_amount'];
			$withdrawal_amount += $value['withdrawal_amount'];

			$pt_bet += $value['pt_bets'];
			$ag_bet += $value['ag_bets'];
			$total_bet += $value['total_bets'];

			$pt_win += $value['pt_wins'];
			$ag_win += $value['ag_wins'];
			$total_win += $value['total_wins'];

			$pt_loss += $value['pt_loss'];
			$ag_loss += $value['ag_loss'];
			$total_loss += $value['total_loss'];

			/*$total_net_gaming += $value['total_net_gaming'];*/
			$total_bonus += $value['total_bonus'];
			$total_players = $value['total_players'];
			$date = $value['date'];

			if ($counter == $stats_count) {
				$data = array(
					'deposit_amount' => $deposit_amount,
					'withdrawal_amount' => $withdrawal_amount,
					'pt_bets' => $pt_bet,
					'ag_bets' => $ag_bet,
					'total_bets' => $total_bet,
					'pt_wins' => $pt_win,
					'ag_wins' => $ag_win,
					'total_wins' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_players' => $total_players,
					'date' => $month,
					'first_date' => date('Y-m-01', strtotime($date)),
					'last_date' => date('Y-m-t', strtotime($date)),
				);
				array_push($result, $data);
			}
		}

		return $result;
	}

	/**
	 * get Yearly Traffic Statistics
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return 	array
	 */
	public function getYearlyTrafficStats($start_date, $end_date) {
		$aff_stats = array_reverse($this->getMonthlyTrafficStats($start_date, $end_date));

		$result = array();

		$deposit_amount = 0;
		$withdrawal_amount = 0;

		$pt_bet = 0;
		$ag_bet = 0;
		$total_bet = 0;

		$pt_win = 0;
		$ag_win = 0;
		$total_win = 0;

		$pt_loss = 0;
		$ag_loss = 0;
		$total_loss = 0;

		$total_net_gaming = 0;
		$total_bonus = 0;
		$total_players = 0;

		$stats_count = count($aff_stats);
		$counter = 0;
		$year = null;

		foreach ($aff_stats as $key => $value) {
			$counter++;

			if ($year == null) {
				$year = date('Y', strtotime($value['first_date']));
			}

			$new_year = date('Y', strtotime($value['first_date']));

			if ($new_year != $year) {
				$data = array(
					'deposit_amount' => $deposit_amount,
					'withdrawal_amount' => $withdrawal_amount,
					'pt_bets' => $pt_bet,
					'ag_bets' => $ag_bet,
					'total_bets' => $total_bet,
					'pt_wins' => $pt_win,
					'ag_wins' => $ag_win,
					'total_wins' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_players' => $total_players,
					'date' => $year,
					'first_date' => date('Y-01-01', strtotime($date)),
					'last_date' => date('Y-12-31', strtotime($date)),
				);
				array_push($result, $data);

				$deposit_amount = 0;
				$withdrawal_amount = 0;

				$pt_bet = 0;
				$ag_bet = 0;
				$total_bet = 0;

				$pt_win = 0;
				$ag_win = 0;
				$total_win = 0;

				$pt_loss = 0;
				$ag_loss = 0;
				$total_loss = 0;

				$total_net_gaming = 0;
				$total_bonus = 0;
				$total_players = 0;
				$year = $new_year;
			}

			$deposit_amount += $value['deposit_amount'];
			$withdrawal_amount += $value['withdrawal_amount'];

			$pt_bet += $value['pt_bets'];
			$ag_bet += $value['ag_bets'];
			$total_bet += $value['total_bets'];

			$pt_win += $value['pt_wins'];
			$ag_win += $value['ag_wins'];
			$total_win += $value['total_wins'];

			$pt_loss += $value['pt_loss'];
			$ag_loss += $value['ag_loss'];
			$total_loss += $value['total_loss'];

			/*$total_net_gaming += $value['total_net_gaming'];*/
			$total_bonus += $value['total_bonus'];
			$total_players = $value['total_players'];
			$date = $value['date'];

			if ($counter == $stats_count) {
				$data = array(
					'deposit_amount' => $deposit_amount,
					'withdrawal_amount' => $withdrawal_amount,
					'pt_bets' => $pt_bet,
					'ag_bets' => $ag_bet,
					'total_bets' => $total_bet,
					'pt_wins' => $pt_win,
					'ag_wins' => $ag_win,
					'total_wins' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_players' => $total_players,
					'date' => $year,
					'first_date' => date('Y-01-01', strtotime($date)),
					'last_date' => date('Y-12-31', strtotime($date)),
				);
				array_push($result, $data);
			}
		}

		return $result;
	}

	/**
	 * insert traffic stats
	 *
	 * @param	array
	 * @return	void
	 */
	public function insertTrafficStats($data) {
		$this->ci->affiliatemodel->insertTrafficStats($data);
	}

	/**
	 * get players
	 *
	 * @param	int
	 * @return	array
	 */
	public function getPlayers($traffic_id) {
		$players = $this->ci->affiliatemodel->getTrafficById($traffic_id);
		$players = explode(',', $players['playerIds']);
		$result = array();

		foreach ($players as $key => $value) {
			$res = $this->ci->affiliatemodel->getPlayers($value);

			array_push($result, $res);
		}

		return $result;
	}

	/**
	 * get players deposit
	 *
	 * @param	int
	 * @return	array
	 */
	public function getPlayersDeposit($traffic_id) {
		$traffic = $this->ci->affiliatemodel->getTrafficById($traffic_id);

		$result = $this->ci->affiliatemodel->getPlayersDeposit($traffic['start_date'], $traffic['end_date']);

		return $result;
	}

	/* end of traffic stats */

	/* monthly earnings */

	/**
	 * get monthly earnings base on conditions
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getEarnings() {
		return $this->ci->affiliatemodel->getEarnings(null, null);
		/*$earnings = array_reverse($this->ci->affiliatemodel->getEarnings(null, null));
	$result = array();

	if(!empty($earnings)) {
	array_push($result, $earnings[0]);
	return $result;
	} else {
	return $result;
	}*/
	}

	/**
	 * get traffic stats base on conditions
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getDailyEarnings($start_date, $end_date) {
		return $this->ci->affiliatemodel->getEarnings($start_date, $end_date);
	}

	/**
	 * get Weekly Monthly Earnings
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return 	array
	 */
	public function getWeeklyEarnings($start_date, $end_date) {
		$earns = $this->getDailyEarnings($start_date, $end_date);

		$result = array();

		$active_players = 0;
		$opening_balance = 0;
		$earnings = 0;
		$approved = 0;
		$closing_balance = 0;

		$earnings_count = count($earns);
		$counter = 0;

		foreach ($earns as $key => $value) {
			$counter++;
			$value['createdOn'] = date('Y-m-d', strtotime($value['createdOn']));

			if ($active_players == 0) {
				$opening_balance = $value['opening_balance'];
				$date_start = date('Y-m-d', strtotime($value['createdOn']));
				$daycount = 7 - date("N", strtotime($date_start));
				$date_end = date('Y-m-d', strtotime($date_start . '+' . $daycount . ' day'));
			}

			$active_players += $value['active_players'];
			$earnings += $value['earnings'];
			$approved += $value['approved'];
			$closing_balance = ($opening_balance + $earnings) - $approved;

			if ($date_end == $value['createdOn']) {
				$data = array(
					'active_players' => $active_players,
					'opening_balance' => $opening_balance,
					'earnings' => $earnings,
					'approved' => $approved,
					'closing_balance' => $closing_balance,
					'date' => ($date_start == $date_end) ? $date_start : $date_start . " - " . $date_end,
					'notes' => $value['notes'],
					'start_date' => $date_start,
					'end_date' => $date_end,
				);
				array_push($result, $data);

				$active_players = 0;
				$opening_balance = $value['closing_balance'];
				$earnings = 0;
				$approved = 0;
				$closing_balance = 0;

			} else if ($counter == $earnings_count) {
				$data = array(
					'active_players' => $active_players,
					'opening_balance' => $opening_balance,
					'earnings' => $earnings,
					'approved' => $approved,
					'closing_balance' => $closing_balance,
					'date' => ($date_start == $value['createdOn']) ? $date_start : $date_start . " - " . $value['createdOn'],
					'notes' => $value['notes'],
					'start_date' => $date_start,
					'end_date' => $date_end,
				);
				array_push($result, $data);
			}
		}

		return $result;
	}

	/**
	 * get Monthly Earnings
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return 	array
	 */
	public function getMonthlyEarnings($start_date, $end_date) {
		$earns = $this->getDailyEarnings($start_date, $end_date);

		$result = array();

		$active_players = 0;
		$opening_balance = 0;
		$earnings = 0;
		$approved = 0;
		$closing_balance = 0;

		$earnings_count = count($earns);
		$counter = 0;
		$month = null;

		foreach ($earns as $key => $value) {
			$counter++;

			$value['createdOn'] = date('Y-m-d', strtotime($value['createdOn']));

			if ($month == null) {
				$opening_balance = $value['opening_balance'];
				$month = date('F', strtotime($value['createdOn']));
			}

			$new_month = date('F', strtotime($value['createdOn']));

			if ($new_month != $month) {
				$data = array(
					'active_players' => $active_players,
					'opening_balance' => $opening_balance,
					'earnings' => $earnings,
					'approved' => $approved,
					'closing_balance' => $closing_balance,
					'notes' => $value['notes'],
					'date' => $month,
					'first_date' => date('Y-m-01', strtotime($date)),
					'last_date' => date('Y-m-t', strtotime($date)),
				);
				array_push($result, $data);

				$active_players = 0;
				$opening_balance = $closing_balance;
				$earnings = 0;
				$approved = 0;
				$closing_balance = 0;
				$month = $new_month;
			}

			$active_players += $value['active_players'];
			$earnings += $value['earnings'];
			$approved += $value['approved'];
			$closing_balance = ($opening_balance + $earnings) - $approved;
			$date = $value['createdOn'];

			if ($counter == $earnings_count) {
				$data = array(
					'active_players' => $active_players,
					'opening_balance' => $opening_balance,
					'earnings' => $earnings,
					'approved' => $approved,
					'closing_balance' => $closing_balance,
					'notes' => $value['notes'],
					'date' => $month,
					'first_date' => date('Y-m-01', strtotime($date)),
					'last_date' => date('Y-m-t', strtotime($date)),
				);
				array_push($result, $data);
			}
		}

		return $result;
	}

	/**
	 * get Yearly Earnings
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return 	array
	 */
	public function getYearlyEarnings($start_date, $end_date) {
		$earns = $this->getMonthlyEarnings($start_date, $end_date);

		$result = array();

		$active_players = 0;
		$opening_balance = 0;
		$open_balance = 0;
		$earnings = 0;
		$approved = 0;
		$closing_balance = 0;

		$earnings_count = count($earns);
		$counter = 0;
		$year = null;
		$date = null;

		foreach ($earns as $key => $value) {
			$counter++;

			if ($year == null) {
				$opening_balance = $value['opening_balance'];
				$year = date('Y', strtotime($value['first_date']));
			}

			$new_year = date('Y', strtotime($value['first_date']));

			if ($new_year != $year) {
				$data = array(
					'active_players' => $active_players,
					'opening_balance' => $opening_balance,
					'earnings' => $earnings,
					'approved' => $approved,
					'closing_balance' => $closing_balance,
					'notes' => $value['notes'],
					'date' => $year,
					'first_date' => date('Y-01-01', strtotime($date)),
					'last_date' => date('Y-12-31', strtotime($date)),
				);
				array_push($result, $data);

				$active_players = 0;
				$opening_balance = $closing_balance;
				$open_balance = 0;
				$earnings = 0;
				$approved = 0;
				$closing_balance = 0;
				$year = $new_year;
			}

			$active_players += $value['active_players'];
			$earnings += $value['earnings'];
			$approved += $value['approved'];
			$closing_balance = ($opening_balance + $earnings) - $approved;
			$date = $value['first_date'];

			if ($counter == $earnings_count) {
				$data = array(
					'active_players' => $active_players,
					'opening_balance' => $opening_balance,
					'earnings' => $earnings,
					'approved' => $approved,
					'closing_balance' => $closing_balance,
					'notes' => $value['notes'],
					'date' => $year,
					'first_date' => date('Y-01-01', strtotime($date)),
					'last_date' => date('Y-12-31', strtotime($date)),
				);
				array_push($result, $data);
			}
		}

		return $result;
	}

	/* end of monthly earnings */

	/**
	 * get payment history base on conditions
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getPaymentHistory($search) {
		return $this->ci->affiliatemodel->getPaymentHistory($search);
	}

	/**
	 * get tracking code base on affiliateId
	 *
	 * @param	int
	 * @return	array
	 */
	public function getTrackingCodeByAffiliateId($affiliate_id) {
		return $this->ci->affiliatemodel->getTrackingCodeByAffiliateId($affiliate_id);
	}

	/**
	 * check if trackingCode is unique
	 *
	 * @param	string
	 * @return	bool
	 */
	public function checkTrackingCode($trackingCode) {
		return $this->ci->affiliatemodel->checkTrackingCode($trackingCode);
	}

	/**
	 * get email in email table
	 *
	 * @return	array
	 */
	public function getEmail() {
		return $this->ci->affiliatemodel->getEmail();
	}

	/**
	 * get currency in currency table
	 *
	 * @return	array
	 */
	public function getCurrency() {
		return $this->ci->utils->getActiveCurrencyKey();
	}

	/* cashier */

	/**
	 * get available balance in monthly earnings
	 *
	 * @return	array
	 */
	public function getAvailableBalance($affiliate_id) {
		return $this->ci->affiliatemodel->getAvailableBalance($affiliate_id);
	}

	/**
	 * get pending payment request in payment history
	 *
	 * @return	array
	 */
	public function getPendingPayment($affiliate_id) {
		return $this->ci->affiliatemodel->getPendingPayment($affiliate_id);
	}

	/**
	 * get payment method in affiliate payment
	 *
	 * @return	array
	 */
	public function getPaymentMethod($affiliate_id) {
		return $this->ci->affiliatemodel->getPaymentMethod($affiliate_id);
	}

	/**
	 * add requests to payment history
	 *
	 * @param	array
	 * @return	void
	 */
	public function addRequests($data) {
		$this->ci->affiliatemodel->addRequests($data);
	}

	/**
	 * get payment requests
	 *
	 * @return	array
	 */
	public function getPaymentRequests($affiliate_id) {
		return $this->ci->affiliatemodel->getPaymentRequests($affiliate_id);
	}

	/**
	 * cancel requests to payment history
	 *
	 * @param	array
	 * @return	void
	 */
	public function cancelRequests($data, $request_id) {
		$this->ci->affiliatemodel->cancelRequests($data, $request_id);
	}
	/**
	 * get Sub-affiliates
	 * @modifyDate 2015-09-10T22:21:56+0800
	 * @param      inteter                   $affiliate_id parentId
	 * @param      integer                  $limit        [description]
	 * @param      integer                  $offset       [description]
	 * @return     array
	 */
	public function getSubAffiliates($parentId, $limit = 0, $offset = 10) {
		return $this->ci->affiliatemodel->getSubAffiliates($parentId, $limit, $offset);
	}
	/* end of cashier */
}

/* End of file affiliate_manager.php */
/* Location: ./application/libraries/affiliate_manager.php */