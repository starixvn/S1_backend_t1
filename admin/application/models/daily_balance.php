<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * daily balance for main-wallet, sub-wallet and total
 * http://ogdev.ddns.net:8090/display/OG/Constants
 *
 */
class Daily_balance extends BaseModel {

	protected $tableName = 'daily_balance';

	const TYPE_MAIN_AND_SUB = 1;
	const TYPE_SUB_ONLY = 2;
	const TYPE_MAIN = 3;

	function __construct() {
		parent::__construct();
	}

	/**
	 * @param int playerId
	 * @param datetime dateFrom
	 * @param datetime dateTo
	 *
	 * @return array
	 */
	function queryPlayerDailyBalance($playerId, $dateFrom = null, $dateTo = null) {
		$this->db->from($this->tableName);
		$this->db->where('type', self::TYPE_MAIN_AND_SUB); # TODO(KAISER): MAGIC NUMBER
		$this->db->where('player_id', $playerId);
		if (isset($dateFrom, $dateTo)) {
			$this->db->where('game_date >=', $dateFrom);
			$this->db->where('game_date <=', $dateTo);
		}
		$query = $this->db->get();
		return $query->row_array();
	}

	function convertGameLogsToDailyBalance($dateTimeFrom, $dateTimeTo, $playerId = null) {

		$apiArray = $this->utils->getApiListByBalanceInGameLog();

		if (!empty($apiArray)) {

			//game_logs to daily_balance
			$this->db->select("player_id, game_platform_id,DATE_FORMAT(end_at,'%Y-%m-%d') as game_date, " .
				"MAX(CONCAT(DATE_FORMAT(end_at,'%Y-%m-%d_%H:%i:%s'),'|', after_balance)) as balance", false);
			$this->db->where("end_at >=", $dateTimeFrom);
			$this->db->where("end_at <=", $dateTimeTo);
			if (!empty($playerId)) {
				$this->db->where('player_id', $playerId);
			}
			$this->db->where_in('game_platform_id', $apiArray);
			$this->db->group_by(array("player_id", "game_platform_id", "DATE_FORMAT(end_at,'%Y%m%d')"));
			$qry = $this->db->get('game_logs');

			// $this->utils->debug_log($qry);
			// $this->db->last_query();
			$rows = $this->getMultipleRow($qry);
			if ($rows) {
				foreach ($rows as $row) {
					$balance = $this->extractBalance($row->balance);
					$this->syncBalanceInfo($row->player_id, $row->game_platform_id, $row->game_date, $balance);
					//main + sub
					$totalBal = $this->calcTotalBalance($row->player_id, $row->game_date);
					$this->syncMainBalance($row->player_id, $row->game_date, $totalBal);
				}
			}

		}
	}

	public function syncMainBalance($playerId, $gameDate, $totalBal) {
		if ($playerId && $gameDate) {
			$bal = $this->getDailyBalanceForTotalBalance($playerId, $gameDate);

			if ($bal) {
				$data = array(
					'balance' => $totalBal,
					'updated_at' => $this->utils->getNowForMysql(),
				);
				// update
				$this->db->where('id', $bal->id);
				$this->db->update($this->tableName, $data);
			} else {
				$data = array(
					'player_id' => $playerId,
					'game_date' => $gameDate,
					'balance' => $totalBal,
					'updated_at' => $this->utils->getNowForMysql(),
					'type' => self::TYPE_MAIN_AND_SUB,
				);
				// insert
				$this->db->insert($this->tableName, $data);
			}
		}
	}

	/**
	 *
	 * @param string gameDate YYYY-mm-dd
	 *
	 */
	public function syncBalanceInfo($playerId, $gamePlatformId, $gameDate, $balance) {
		if ($playerId && $gamePlatformId && $gameDate) {
			$bal = $this->getDailyBalanceForSubWallet($playerId, $gamePlatformId, $gameDate);

			if ($bal) {
				$data = array(
					'balance' => $balance,
					'updated_at' => $this->utils->getNowForMysql(),
				);
				// update
				$this->db->where('id', $bal->id);
				$this->db->update($this->tableName, $data);
			} else {
				$data = array(
					'player_id' => $playerId,
					'sub_wallet_id' => $gamePlatformId,
					'game_date' => $gameDate,
					'balance' => $balance,
					'updated_at' => $this->utils->getNowForMysql(),
					'type' => self::TYPE_SUB_ONLY,
				);
				// insert
				$this->db->insert($this->tableName, $data);
			}

		}
	}

	private function extractBalance($balanceStr) {
		//extract balance
		$arr = explode('|', $balanceStr);
		$balance = $arr[0];
		if (count($arr) >= 2) {
			$balance = $arr[1];
		}
		return $balance;
	}

	public function calcTotalBalance($playerId, $gameDate) {
		//include main wallet and sub wallet
		$this->db->select('player_id, SUM(balance) as totalBalance', false);
		$this->db->where('player_id', $playerId);
		$this->db->where_in('type', array(self::TYPE_SUB_ONLY, self::TYPE_MAIN));
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'totalBalance');
	}

	public function getDailyBalanceForTotalBalance($playerId, $gameDate) {
		$this->db->where('player_id', $playerId);
		$this->db->where('game_date', $gameDate);
		$this->db->where('type', self::TYPE_MAIN_AND_SUB);
		$qry = $this->db->get($this->tableName);
		return $this->getOneRow($qry);
	}

	public function getDailyBalanceForSubWallet($playerId, $subWalletId, $gameDate) {
		$this->db->where('player_id', $playerId);
		$this->db->where('sub_wallet_id', $subWalletId);
		$this->db->where('game_date', $gameDate);
		$this->db->where('type', self::TYPE_SUB_ONLY);
		$qry = $this->db->get($this->tableName);
		return $this->getOneRow($qry);
	}

	// public function updateWalletFromDailyBalance($playerId = null) {

	// 	$this->load->model(array('wallet_model'));
	// 	//last balance
	// 	$this->db->select("player_id, sub_wallet_id, MAX(CONCAT(DATE_FORMAT(game_date,'%Y-%m-%d'),'|', balance)) as balance", false);
	// 	// $this->db->where("game_date >=", $dateTimeFrom);
	// 	// $this->db->where("game_date <=", $dateTimeTo);

	// 	if (!empty($playerId)) {
	// 		$this->db->where('player_id', $playerId);
	// 	}

	// 	$this->db->group_by(array("player_id", "sub_wallet_id", "game_date"));
	// 	$qry = $this->db->get($this->tableName);

	// 	$rows = $this->getMultipleRow($qry);
	// 	if ($rows) {
	// 		foreach ($rows as $row) {
	// 			//update subwallet, playeraccount
	// 			$balance = $this->extractBalance($row->balance);
	// 			$this->wallet_model->syncSubWallet($row->player_id, $row->sub_wallet_id, $balance);

	// 			// $subwallet = $this->wallet_model->getSubWalletBy($row->player_id, $row->sub_wallet_id);
	// 			// $balance = $this->extractBalance($row->balance);
	// 			// if ($subwallet) {
	// 			// 	//update
	// 			// 	$this->wallet_model->updateSubWallet($row->playerAccountId, $balance);
	// 			// } else {
	// 			// 	//insert
	// 			// 	$this->wallet_model->updateSubWallet($row->playerAccountId, $balance);
	// 			// }
	// 		}
	// 	}
	// }

	/**
	 * generates daily balance data, stores to daily_balance
	 * @param	date	$arg_date	The date to compute for.
	 * @return	none
	 */
	public function generateDailyBalance($arg_date = null) {
		$date = $this->utils->checkAndFormatDate($arg_date);
		if (empty($date)) {
			$date  = $this->utils->getTodayForMysql();
		}
		$this->utils->debug_log(__METHOD__, "Deleting existing entries and rebuilding daily balance", [ 'date' => $date, 'arg_date' => $arg_date ]);
		$this->db->where('game_date', $date)->delete('daily_balance');

		$this->generateDailyMainWallet($date);
		$this->generateDailySubWallet($date);
		$this->generateDailyTotal($date);
	}

	public function generateDailyMainWallet($date = NULL) {
		if (empty($date)) {
			$date = $this->utils->getTodayForMysql();
		}

		$currentDateTime = $this->utils->getNowForMysql();

		$this->db->select("NULL as id, playerId as player_id, totalBalanceAmount as balance, '" . self::TYPE_MAIN . "' as type, typeId as sub_wallet_id, '{$currentDateTime}' as updated_at, '{$date}' as game_date", false);
		$this->db->from('playeraccount');
		$this->db->where('status', 0);
		$this->db->where('type', 'wallet');
		$this->db->order_by('playerId', 'asc');
		$this->db->order_by('typeId', 'asc');
		$sql = 'INSERT INTO daily_balance ' . $this->db->_compile_select();
		$query = $this->db->get();
		$this->db->query($sql);
	}

	public function generateDailySubWallet($date = NULL) {
		if (empty($date)) {
			$date = $this->utils->getTodayForMysql();
		}

		$currentDateTime = $this->utils->getNowForMysql();

		$this->db->select("NULL as id, playerId as player_id, totalBalanceAmount as balance, '" . self::TYPE_SUB_ONLY . "' as type, typeId as sub_wallet_id, '{$currentDateTime}' as updated_at, '{$date}' as game_date", false);
		$this->db->from('playeraccount');
		$this->db->where('status', 0);
		$this->db->where('type', 'subwallet');
		$this->db->where('typeId in (select id from external_system where system_type = 1 and status = 1)');
		$this->db->order_by('playerId', 'asc');
		$this->db->order_by('typeId', 'asc');
		$sql = 'INSERT INTO daily_balance ' . $this->db->_compile_select();
		$query = $this->db->get();
		$this->db->query($sql);
	}

	public function generateDailyTotal($date = NULL, $deleteOldRecord = false) {
		if (empty($date)) {
			$date = $this->utils->getTodayForMysql();
		}

		$currentDateTime = $this->utils->getNowForMysql();

		$this->db->where('game_date', $date)->where("type", self::TYPE_MAIN_AND_SUB)->delete('daily_balance');
		$this->db->select("NULL as id, player_id, SUM(balance) as balance, '" . self::TYPE_MAIN_AND_SUB . "' as type, NULL as sub_wallet_id, '{$currentDateTime}' as updated_at, '{$date}' as game_date", false);
		$this->db->from('daily_balance');
		$this->db->where('game_date', $date);
		$this->db->group_by('player_id');

		// $this->db->select("NULL as id, playerId as player_id, SUM(totalBalanceAmount) as balance, '" . self::TYPE_MAIN_AND_SUB . "' as type, NULL as sub_wallet_id, CURRENT_TIMESTAMP() as updated_at, CURRENT_DATE() as game_date", false);
		// $this->db->from('playeraccount');
		// $this->db->where('status', 0);
		// $this->db->where_in('type', array('wallet', 'subwallet'));
		// $this->db->group_by('playerId');
		// $this->db->order_by('playerId', 'asc');
		// $this->db->order_by('typeId', 'asc');

		$sql = 'INSERT INTO daily_balance ' . $this->db->_compile_select();
		$query = $this->db->get();
		$this->db->query($sql);
	}

	public function get_daily_balance($date = NULL, $username = NULL, $exclude_player = null) {

		$game_platforms = $this->utils->getActiveGameSystemList();
		$game_platforms = array_column($game_platforms, NULL, 'id');
        $player_name_statement = $this->utils->isEnabledFeature('display_aff_beside_playername_daily_balance_report') ?
            '( CASE WHEN affiliates.username IS NULL THEN player.username ELSE CONCAT(player.username, \' (\', affiliates.username, \')\' ) END ) as username' : 'player.username';

        $this->db->select($player_name_statement, false);
		$this->db->select('daily_balance.game_date');
		$this->db->select('daily_balance.player_id');
		$this->db->select('SUM(IF(daily_balance.type = ' . self::TYPE_MAIN . ' AND daily_balance.sub_wallet_id = 0, daily_balance.balance, 0)) as main_wallet', false);
		$this->db->select('GROUP_CONCAT(IF(daily_balance.type = ' . self::TYPE_SUB_ONLY . ' AND daily_balance.sub_wallet_id > 0, CONCAT_WS(\':\', daily_balance.sub_wallet_id, daily_balance.balance), \'\')) as sub_wallet', false);
		$this->db->select('SUM(IF(daily_balance.type = 1 AND daily_balance.sub_wallet_id IS NULL,daily_balance.balance,0)) as total_balance', false);
		$this->db->select_max('daily_balance.updated_at');
		$this->db->from('daily_balance');
		$this->db->join('player', 'player.playerId = daily_balance.player_id')
        ->join('affiliates', 'affiliates.affiliateId = player.affiliateId');

		if ($date) {
			$this->db->where('daily_balance.game_date', $date);
		}

		if ($username) {
			$this->db->where('player.username', $username);
		}

		if(empty($username)){
			$this->db->where(array('player.deleted_at' => NULL));
		}
        if($exclude_player){
            $this->db->where_not_in('player.playerId', $exclude_player);
        }

		$this->db->group_by('daily_balance.game_date');
		$this->db->group_by('daily_balance.player_id');
		$limit = $this->utils->getConfig('daily_player_balance_report_limit');

		if(!empty($limit)){
			$this->db->limit($limit);
		}

		$query = $this->db->get();
		$rows = $query->result_array();
		$this->utils->debug_log('get_daily_balance query : ', $this->utils->printLastSQL());

		array_walk($rows, function(&$row) use ($game_platforms) {

			$sub_wallets = explode(',', $row['sub_wallet']);
			$sub_wallets = array_filter($sub_wallets, function(&$sub_wallet) {

				if ($sub_wallet) {

					list($key, $value) = explode(':', $sub_wallet);

					$sub_wallet = array(
						'game_platform_id' => $key,
						'balance' => floatval($value),
					);

					return TRUE;
				} else return FALSE;

			});
			$sub_wallets = array_column($sub_wallets, 'balance', 'game_platform_id');

			$row['sub_wallet'] = array();

			foreach ($game_platforms as $game_platform_id => $game_platform) {
				$row['sub_wallet'][$game_platform_id] = isset($sub_wallets[$game_platform_id]) ? $sub_wallets[$game_platform_id] : 0.0;
			}

		});

		return $rows;
	}

	public function generatePlayerBalanceByDate($startDate, $endDate) {
		$this->load->model(['banktype','external_system', 'game_type_model', 'game_logs', 'wallet_model']);
		$this->deletePlayerBalanceByDate($startDate);
		$depositMenuList = $this->utils->getDepositMenuList();
		$withdrawalBanks = $this->banktype->getBankTypes();
		$gameApiList = $this->external_system->getAllActiveSytemGameApi();
		$open_date = new DateTime();
		$open_date->modify($startDate);
		$open_date->modify('-1 day');
		$this->utils->debug_log('open date : ' . $open_date->format('Y-m-d 23:59:59'));
		$this->wallet_model->generateBigWalletBalanceByDate($open_date->format('Y-m-d 23:59:59'));
		$lastdayAmount = $this->wallet_model->getTotalBigWalletBalanceByDate($open_date->format('Y-m-d 23:59:59'));
		$open_data = [
			'data_date' => $startDate,
			'data_type' => 1,
			'data_key_id' => 0,
			'data_value' => round($lastdayAmount, 2)
		];
		$this->db->insert('daily_balance_report', $open_data);
		$closingRealPlayerBalance = round($lastdayAmount, 2);
		//deposit
		foreach($depositMenuList as $deposit) {
			$depositAmount = $this->transactions->getTotalDepositWithdrawalByPaymentAccount($deposit->bankTypeId, Transactions::DEPOSIT, $startDate, $endDate);
			$deposit_data = [
				'data_date' => $startDate,
				'data_type' => 2,
				'data_key_id' => $deposit->bankTypeId,
				'data_value' => round($depositAmount, 2)
			];
			$this->db->insert('daily_balance_report', $deposit_data);
			$closingRealPlayerBalance += round($depositAmount, 2);
		}
		//withdrawal
		foreach ($withdrawalBanks as $bank) {
			$withdrawalAmount = $this->transactions->getTotalDepositWithdrawalByPaymentAccount($bank->bankTypeId, Transactions::WITHDRAWAL, $startDate, $endDate);
			$withdraw_data = [
				'data_date' => $startDate,
				'data_type' => 3,
				'data_key_id' => $bank->bankTypeId,
				'data_value' => round($withdrawalAmount, 2)
			];
			$this->db->insert('daily_balance_report', $withdraw_data);
			$closingRealPlayerBalance -= round($withdrawalAmount, 2);
		}
		//game
		foreach ($gameApiList as $gamePlatform) {
			list($totalBet, $totalWin, $totalLoss) = $this->game_logs->getTotalBetsWinsLoss($startDate, $endDate, $gamePlatform['id']);
			$gamebet_data = [
				'data_date' => $startDate,
				'data_type' => 4,
				'data_key_id' => $gamePlatform['id'],
				'data_value' => round($totalBet, 2)
			];
			$this->db->insert('daily_balance_report', $gamebet_data);
			$closingRealPlayerBalance -= round($totalBet, 2);
			$gamewin_data = [
				'data_date' => $startDate,
				'data_type' => 5,
				'data_key_id' => $gamePlatform['id'],
				'data_value' => round($totalWin, 2)
			];
			$this->db->insert('daily_balance_report', $gamewin_data);
			$closingRealPlayerBalance += round($totalWin, 2);
			$gameloss_data = [
				'data_date' => $startDate,
				'data_type' => 6,
				'data_key_id' => $gamePlatform['id'],
				'data_value' => round($totalLoss, 2)
			];
			$this->db->insert('daily_balance_report', $gameloss_data);
			$totalCancelBets = $this->game_logs->getUnsettledBets(array(Game_logs::STATUS_CANCELLED), $startDate, $endDate, $gamePlatform['id']);
			$gamecancel_data = [
				'data_date' => $startDate,
				'data_type' => 7,
				'data_key_id' => $gamePlatform['id'],
				'data_value' => round($totalCancelBets, 2)
			];
			$this->db->insert('daily_balance_report', $gamecancel_data);
			$ManualAdjustments = $this->transactions->getTotalManualAdjustmentByWalletId($startDate, $endDate, $gamePlatform['id']);
			$gamemanualadjust_data = [
				'data_date' => $startDate,
				'data_type' => 8,
				'data_key_id' => $gamePlatform['id'],
				'data_value' => round($ManualAdjustments, 2)
			];
			$this->db->insert('daily_balance_report', $gamemanualadjust_data);
			$closingRealPlayerBalance += round($ManualAdjustments, 2);
			$gmaerealbonus = $this->transactions->getTotalBonusByWalletId($startDate, $endDate, $gamePlatform['id']);
			$gmaerealbonus_data = [
				'data_date' => $startDate,
				'data_type' => 9,
				'data_key_id' => $gamePlatform['id'],
				'data_value' => round($gmaerealbonus, 2)
			];
			$this->db->insert('daily_balance_report', $gmaerealbonus_data);
			$closingRealPlayerBalance += round($gmaerealbonus, 2);
		}
		//closing real player balance
		$closing_data = [
			'data_date' => $startDate,
			'data_type' => 10,
			'data_key_id' => 0,
			'data_value' => round($closingRealPlayerBalance, 2)
		];
		$this->db->insert('daily_balance_report', $closing_data);
		//unsettle
		$totalUnsettledBets = $this->game_logs->getUnsettledBets(array(Game_logs::STATUS_PENDING), $startDate, $endDate);
		$unsettle_data = [
			'data_date' => $startDate,
			'data_type' => 11,
			'data_key_id' => 0,
			'data_value' => round($totalUnsettledBets, 2)
		];
		$this->db->insert('daily_balance_report', $unsettle_data);
		//total player balance coverage requirement
		$totalBalance = $closingRealPlayerBalance + $totalUnsettledBets;
		$totalBalance_data = [
			'data_date' => $startDate,
			'data_type' => 12,
			'data_key_id' => 0,
			'data_value' => round($totalBalance, 2)
		];
		$this->db->insert('daily_balance_report', $totalBalance_data);
	}

	public function getPlayerBalanceByDate($startDate, $endDate) {
		$this->db->select('*')->from('daily_balance_report')->where('data_date >=', $startDate)->where('data_date <=', $endDate)->order_by('data_date', 'ASC')->order_by('data_type', 'ASC')->order_by('id', 'ASC');
		return $this->runMultipleRowArray();
	}

	public function deletePlayerBalanceByDate($date) {
		$this->db->delete('daily_balance_report', ['data_date' => $date]);
	}
}

/////end of file///////