<?php
// if (PHP_SAPI === 'cli') {
// 	exit('No web access allowed');
// }

class Affiliate_checker extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library(array('affiliate_manager'));
	}

	function index() {
		$this->setTrafficStats(); //run end of the day
		$this->setAffiliateStatistics(); //run end of the day
		$this->saveAffiliateEarningsMonthly(); //run end of the month
	}

	/**
	 * save affiliate earnings per day
	 *
	 * @return  void
	 */
	private function saveAffiliateEarningsMonthly() {
		set_time_limit(0);

		try {
			$year = date('Y');
			$month = date('m');
			$date = date('d');

			/*$year = '2015';
				$month = '07';
			*/

			$start_date = date('Y-m-d H:i:s', mktime(0, 0, 0, $month, '1', $year));
			$end_date = date('Y-m-d H:i:s', mktime(23, 59, 59, $month, $date, $year));

			$affiliates = $this->affiliate_manager->getAllAffiliates(null, null, null); // get all affiliates

			foreach ($affiliates as $key => $value) {
				if ($value['status'] == 1) {
					continue;
				}

				$active_players = count($this->affiliate_manager->getActivePlayersThisMonth($value['affiliateId'], $start_date, $end_date, null));
				$last_closing_balance = $this->affiliate_manager->getMonthlyLastClosingBalance($value['affiliateId'], $start_date, 'DESC'); // check the last closing balance
				$earnings_today = $this->getMonthlyEarnings($value['affiliateId'], $start_date, $end_date); // get earnings
				$closing_balance = $last_closing_balance + $earnings_today; // get closing balance

				$this->affiliate_manager->updateDailyEarnings($value['affiliateId'], $start_date, $end_date); // update status of daily earnings

				$data = array(
					'affiliateId' => $value['affiliateId'],
					'type' => 'monthly',
					'active_players' => $active_players,
					'opening_balance' => ($last_closing_balance == null) ? 0 : $last_closing_balance,
					'earnings' => ($earnings_today == null) ? 0 : $earnings_today,
					'approved' => 0,
					'closing_balance' => $closing_balance,
					'createdOn' => $end_date,
					'status' => '0',
				);

				$this->affiliate_manager->insertAffiliateMonthlyEarnings($data);
			}

			$data = array(
				'type' => 'monthly',
				'subject' => 'Affiliate Earnings',
				'description' => 'Done Saving Affiliate Earnings',
				'status' => 'success',
				'date' => date('Y-m-d H:i:s'),
			);

			/*$this->report_functions->saveToCronLogs($data); //save logs to db*/
			$string = $this->convertArrayToString($data);
			$this->writeToLog($string);
		} catch (Exception $e) {
			$data = array(
				'type' => 'monthly',
				'subject' => 'Affiliate Earnings',
				'description' => $e->getMessage(),
				'status' => 'error',
				'date' => date('Y-m-d H:i:s'),
			);

			/*$this->report_functions->saveToCronLogs($data); //save logs to db*/
			$string = $this->convertArrayToString($data);
			$this->writeToLog($string);
		}
	}

	private function getMonthlyEarnings($affiliate_id, $start_date, $end_date) {
		try {
			$this->setTermsSetting($affiliate_id); // set settings of affiliate

			$games = $this->affiliate_manager->getGame();

			$game = array(); // game that meet the required active players

			foreach ($games as $key => $game_value) {
				$players = count($this->affiliate_manager->getActivePlayersThisMonth($affiliate_id, $start_date, $end_date, $game_value['gameId'])); // count players under affiliate

				if ($players >= $this->terms_setting[strtolower($game_value['game']) . '_active_players'] && $this->terms_setting[strtolower($game_value['game']) . '_status'] == 0) {
					array_push($game, $game_value);
				}
			}

			if (!empty($game)) {
				$earnings_today = 0;

				foreach ($game as $key => $value) {
					$result = $this->affiliate_manager->getMonthlyEarningsPerGame($affiliate_id, $start_date, $end_date, strtolower($value['game']));

					$game_name = strtolower($value['game']);

					if (empty($result)) {
						break;
					}

					foreach ($result as $key => $value_earn) {
						$tgi = $value_earn[$game_name . "_win"] - $value_earn[$game_name . "_loss"];
						$total_earnings = $tgi / $this->terms_setting[strtolower($value['game']) . '_percentage'];
						//$total_earnings = $tgi - $value_earn['total_bonus'];

						$earnings_today += $total_earnings;
					}
				}

				return $earnings_today;
			}

			return 0;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	private $terms_setting = array();

	/**
	 * set Affiliate Statistics
	 *
	 * @param   int
	 * @return  redirect
	 */
	private function setAffiliateStatistics() {
		set_time_limit(0);

		try {
			$year = date('Y');
			$month = date('m');
			$date = date('d');

			/*for($i=1; $i<=1; $i++) {
				$year = '2015';
				$month = '07';
			*/

			$start_date = date('Y-m-d H:i:s', mktime(0, 0, 0, $month, $date, $year));
			$end_date = date('Y-m-d H:i:s', mktime(23, 59, 59, $month, $date, $year));

			$affiliates = $this->affiliate_manager->getAllAffiliates(null, null, null); // get all affiliates
			$affiliate_registered = count($this->affiliate_manager->getRegisteredAffiliate($start_date, $end_date));

			foreach ($affiliates as $key => $value) {
				$this->setTermsSetting($value['affiliateId']);

				$data = array();

				$data['affiliateId'] = $value['affiliateId'];
				$data['register_affiliates'] = $affiliate_registered;
				$data['total_player'] = count($this->affiliate_manager->getAllPlayersUnderAffiliate($value['affiliateId'], null, null));

				$data['pt_bet'] = $this->affiliate_manager->getAllPTBetsUnderAffiliate($value['affiliateId'], $start_date, $end_date);
				$data['ag_bet'] = $this->affiliate_manager->getAllAGBetsUnderAffiliate($value['affiliateId'], $start_date, $end_date);
				$data['total_bet'] = $data['pt_bet'] + $data['ag_bet'];

				$data['pt_win'] = $this->affiliate_manager->getAllPTWinsUnderAffiliate($value['affiliateId'], $start_date, $end_date);
				$data['ag_win'] = $this->affiliate_manager->getAllAGWinsUnderAffiliate($value['affiliateId'], $start_date, $end_date);
				$data['total_win'] = $data['pt_win'] + $data['ag_win'];

				$data['pt_loss'] = $this->affiliate_manager->getAllPTLossUnderAffiliate($value['affiliateId'], $start_date, $end_date);
				$data['ag_loss'] = $this->affiliate_manager->getAllAGLossUnderAffiliate($value['affiliateId'], $start_date, $end_date);
				$data['total_loss'] = $data['pt_loss'] + $data['ag_loss'];

				$data['total_bonus'] = $this->affiliate_manager->getAffiliateTotalBonus($value['affiliateId'], $start_date, $end_date);
				$data['total_net_gaming'] = $this->getNetGaming($data);
				$data['date'] = $start_date;

				$data['pt_status'] = $this->terms_setting['pt_status'];
				$data['pt_percentage'] = $this->terms_setting['pt_percentage'];
				$data['pt_active_players'] = $this->terms_setting['pt_active_players'];

				$data['ag_status'] = $this->terms_setting['ag_status'];
				$data['ag_percentage'] = $this->terms_setting['ag_percentage'];
				$data['ag_active_players'] = $this->terms_setting['ag_active_players'];

				$this->affiliate_manager->insertAffiliateStats($data);
			}

			/*}*/

			$data = array(
				'type' => 'daily',
				'subject' => 'Affiliate Statistics',
				'description' => 'Done Saving Affiliate Statistics',
				'status' => 'success',
				'date' => date('Y-m-d H:i:s'),
			);

			/*$this->report_functions->saveToCronLogs($data); //save logs to db*/
			$string = $this->convertArrayToString($data);
			$this->writeToLog($string);
		} catch (Exception $e) {
			$data = array(
				'type' => 'daily',
				'subject' => 'Affiliate Statistics',
				'description' => $e->getMessage(),
				'status' => 'error',
				'date' => date('Y-m-d H:i:s'),
			);

			/*$this->report_functions->saveToCronLogs($data); //save logs to db*/
			$string = $this->convertArrayToString($data);
			$this->writeToLog($string);
		}
	}

	/**
	 * set terms setting per game per affiliate
	 *
	 * @param  int
	 * @return  void
	 */
	private function setTermsSetting($affiliate_id) {
		$game = $this->affiliate_manager->getGame();

		foreach ($game as $key => $game_value) {
			$default_opt = $this->affiliate_manager->getAffiliateDefaultOptionsByGameId($game_value['gameId']); //get default terms

			foreach ($default_opt as $key => $value) {
				if ($value['optionsType'] == 'percentage') {
					$percentage = $value['optionsValue'];
				} else {
					$active_players = $value['optionsValue'];
				}
				$status = 0;
			}

			$term = $this->affiliate_manager->getAffiliateOptions($affiliate_id, $game_value['gameId']); //get affiliate terms

			if (!empty($term)) {
				//change default percentage and active players if term per affiliate is set
				foreach ($term as $key => $value) {
					if ($value['optionsType'] == 'percentage') {
						$percentage = $value['optionsValue'];
					} else {
						$active_players = $value['optionsValue'];
					}
					$status = $value['status'];
				}
			}

			$this->terms_setting[strtolower($game_value['game']) . '_percentage'] = $percentage;
			$this->terms_setting[strtolower($game_value['game']) . '_active_players'] = $active_players;
			$this->terms_setting[strtolower($game_value['game']) . '_status'] = $status;
		}
	}

	private function getNetGaming($data) {
		$net_gaming = 0;
		$total_gross_income = 0;

		$game = $this->affiliate_manager->getGame();

		foreach ($game as $key => $game_value) {
			$gross_income = $data[strtolower($game_value['game']) . '_win'] - $data[strtolower($game_value['game']) . '_loss'];

			/*if ($this->terms_setting[strtolower($game_value['game']) . '_status'] == 0) {
				$total_gross_income += $gross_income / $this->terms_setting[strtolower($game_value['game']) . '_percentage'];
			*/

			$total_gross_income += $gross_income;
		}

		$net_gaming = $total_gross_income - $data['total_bonus']; // compute for net gaming income (Total Gross Income - bonus - cashback - transaction fee)

		return $net_gaming;
	}

	/**
	 * traffic statistics page set
	 *
	 * @return  void
	 */
	public function setTrafficStats() {
		set_time_limit(0);

		try {
			$affiliates = $this->affiliate_manager->getAllAffiliates(null, null, null);
			$year = date('Y');
			$month = date('m');
			$date = date('d');

			/*for($i=15; $i<=18; $i++) {
				$year = '2015';
				$month = '05';
			*/

			$start_date = date('Y-m-d H:i:s', mktime(0, 0, 0, $month, $date, $year));
			$end_date = date('Y-m-d H:i:s', mktime(23, 59, 59, $month, $date, $year));

			foreach ($affiliates as $key => $value) {
				$affiliate_id = $value['affiliateId'];

				$players = $this->affiliate_manager->getAllPlayersUnderAffiliate($affiliate_id, null, null); // get all players
				$player_registered = count($this->affiliate_manager->getAllPlayersUnderAffiliate($affiliate_id, $start_date, $end_date));

				foreach ($players as $key => $value) {
					$data = array();
					$player_id = $value['playerId'];
					$username = $value['username'];

					$data['affiliateId'] = $affiliate_id;
					$data['playerId'] = $player_id;
					$data['register_players'] = $player_registered;
					$data['deposit_amount'] = $this->affiliate_manager->getPlayerDeposit($player_id, $start_date, $end_date);
					$data['withdrawal_amount'] = $this->affiliate_manager->getPlayerWithdrawal($player_id, $start_date, $end_date);

					$data['pt_bets'] = $this->affiliate_manager->getPlayerPTBet($username, $start_date, $end_date);
					$data['ag_bets'] = $this->affiliate_manager->getPlayerAGBet($username, $start_date, $end_date);
					$data['total_bets'] = $data['pt_bets'] + $data['ag_bets'];

					$data['pt_wins'] = $this->affiliate_manager->getPlayerPTWins($username, $start_date, $end_date);
					$data['ag_wins'] = $this->affiliate_manager->getPlayerAGWins($username, $start_date, $end_date);
					$data['total_wins'] = $data['pt_wins'] + $data['ag_wins'];

					$data['pt_loss'] = $this->affiliate_manager->getPlayerPTLoss($username, $start_date, $end_date);
					$data['ag_loss'] = $this->affiliate_manager->getPlayerAGLoss($username, $start_date, $end_date);
					$data['total_loss'] = $data['pt_loss'] + $data['ag_loss'];

					$data['total_bonus'] = $this->affiliate_manager->getPlayerTotalBonus($player_id, $start_date, $end_date);
					$data['date'] = $start_date;

					$this->affiliate_manager->insertTrafficStats($data);
				}
			}
			/*}*/

			$data = array(
				'type' => 'daily',
				'subject' => 'Traffic Statistics',
				'description' => 'Done Saving Traffic Statistics',
				'status' => 'success',
				'date' => date('Y-m-d H:i:s'),
			);

			/*$this->report_functions->saveToCronLogs($data); //save logs to db*/
			$string = $this->convertArrayToString($data);
			$this->writeToLog($string);
		} catch (Exception $e) {
			$data = array(
				'type' => 'daily',
				'subject' => 'Traffic Statistics',
				'description' => $e->getMessage(),
				'status' => 'error',
				'date' => date('Y-m-d H:i:s'),
			);

			/*$this->report_functions->saveToCronLogs($data); //save logs to db*/
			$string = $this->convertArrayToString($data);
			$this->writeToLog($string);
		}
	}

	private function writeToLog($string) {
		// $path = realpath(APPPATH . "../public/logs");
		// $file = $path . '/' . LOG_FILE_NAME . "_" . date('Y-m-d') . '.txt';
		// $content = PHP_EOL . $string . ";";
		// file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
		$this->utils->debug_log($string);
		// $this->utils->writeAffErrorLog($string);

		/*$path = realpath(APPPATH . "../public/logs");
			$filename = $path . '/' . LOG_FILE_NAME . "_" . date('Y-m-d') . '.txt';
			$fp = fopen($filename, "a+");

			$content .= PHP_EOL . $string . ";";

			$write = fputs($fp, $content);
		*/
	}

	private function convertArrayToString($array) {
		if ($array != null) {
			$string = implode(",", $array);
			return $string;
		}

		return null;
	}
}
