<?php

/**
 *
 * get_from_datetime(from_type_arr);
 *
 * get_to_datetime(to_type_arr);
 *
 * sum_deposit_amount(from_datetime, to_datetime, min_amount)
 *
 * current_player_total_balance()
 *
 * get_game_result_amount(from_datetime,to_datetime)
 *
 * get_game_betting_amount(from_datetime,to_datetime)
 *
 * deposit_type: 'last', 'first'
 * to_type: 'now'
 * from_type: 'player_reg_date', 'last_withdraw', 'last_same_promo', 'last_transfer'
 *
 * get from_to_date by type , this_month, today
 */
class Runtime {

	const EMPTY_DATETIME='0000-00-00 00:00:00';

	const DEPOSIT_TYPE_LAST = 'last';
	const DEPOSIT_TYPE_FIRST = 'first';

	const TO_TYPE_NOW = 'now';
	const DATE_YESTERDAY_START = 'yesterday_start';
	const DATE_YESTERDAY_END = 'yesterday_end';
	const DATE_TODAY_START = 'today_start';
	const DATE_TODAY_END = 'today_end';
	const DATE_LAST_WEEK_START = 'last_week_start';
	const DATE_LAST_WEEK_END = 'last_week_end';
	const DATE_THIS_WEEK_START = 'this_week_start';
	const DATE_THIS_WEEK_END = 'this_week_end';
	const LAST_RELEASE_BONUS_TIME = 'last_release_bonus_time';

	const RESCUE_MODE_DEPOSIT_MINUS_WITHDRAWAL='deposit minus withdrawal';

	function __construct() {
		// parent::__construct();
		$this->CI = &get_instance();
		$this->SYS = $this->CI;
		$this->utils = $this->CI->utils;
	}

	public static function getRuntime($playerId, $promorule, $playerBonusAmount = null, $depositAmount = null) {
		$runtime = new Runtime();
		$runtime->playerId = $playerId;
		$runtime->promorule = $promorule;
		$runtime->playerBonusAmount = floatval($playerBonusAmount);
		$runtime->depositAmount = floatval($depositAmount);

		$runtime->CI->load->model(['group_level', 'player_model']);

		$runtime->player= $runtime->CI->player_model->getPlayerArrayById($playerId);

		$vipInfo=$runtime->CI->group_level->getPlayerGroupLevelInfo($playerId);
		if(!empty($vipInfo)){
			$runtime->levelId=$vipInfo['levelId'];
			$runtime->levelName=$vipInfo['vipLevelName'];
			$runtime->vipGroupId=$vipInfo['vipSettingId'];
			$runtime->vipGroupName=$vipInfo['groupName'];

		}

		return $runtime;
	}

	public function runjs($js, &$extra_info=null, $dry_run=false) {
		if (class_exists('V8Js')) {
			$v8 = new V8Js();
			$v8->runtime = $this;
			try {
			    if(empty($extra_info['debug_log'])){
                    $extra_info['debug_log'] = '';
                }
				$this->extra_info=$extra_info;
				$this->dry_run=$dry_run;
				$result = $v8->executeString($js, 'runtime.js', V8Js::FLAG_FORCE_ARRAY);
				$this->CI->utils->debug_log($result);
				$extra_info['debug_log'].=$this->log_str;
				return $result;
			} catch (V8JsException $e) {
				$this->CI->utils->error_log($e);

				$extra_info['debug_log'].="exception in js rule: ".$e->getMessage().", trace: ".$e->getTraceAsString();
			}
		} else {
			$this->CI->utils->error_log('lost v8js');
		}
		return null;
	}

	public function getConfig($item) {
		return $this->CI->utils->getConfig($item);
	}

	private $CI = null;
	public $SYS = null;
	public $playerId = null;
	public $promorule = null;
	public $playerBonusAmount = null;
	public $depositAmount = null;
	public $levelName=null;
	public $vipGroupName=null;
	public $levelId=null;
	public $vipGroupId=null;
	public $log_str='';
	public $dry_run=false;
	public $extra_info=null;
	// public $errorMessageLang = null;

	/**
	 * Get one of various 'from' datetimes
	 * @param  array 	$from_type_arr	array of datetime types
	 *      Will eval the value by order; if the 1st value is empty, will use the 2nd, 3rd, etc.
	 *      Valid datetime types are:
	 *         	player_reg_date		Player registration
	 *         	last_withdraw		Player last withdraw
	 *         	last_same_promo		Last time same promo is approved
	 *         	last_transfer		Player last transfer
	 * @example
	 * 		// Will look for the time of last same promo, then player registration
	 * 		$begin_dt = PHP.runtime.get_from_datetime([ 'last_same_promo', 'player_reg_date' ])
	 *
	 * @return	Datetime string
	 */
	public function get_from_datetime($from_type_arr) {
		return $this->CI->utils->getLastFromDatetime($from_type_arr, $this->playerId, $this->promorule['promorulesId']);
	}

	public function get_to_datetime($to_type_arr) {
		$to_datetime = null;
		if (!empty($to_type_arr)) {
			foreach ($to_type_arr as $to_type) {
				switch ($to_type) {
				case self::TO_TYPE_NOW:
					$to_datetime = $this->CI->utils->getNowForMysql();
					break;

				}
			}
		}
		return $to_datetime;
	}

	private function returnAmount($amt) {
		if (!$amt) {
			$amt = 0;
		}

		return $this->CI->utils->roundCurrencyForShow($amt);
	}

	public function current_player_total_balance() {

		$val=null;
		if($this->process_mock('current_player_total_balance', $val)){
			return $val;
		}

		$this->CI->load->model(array('wallet_model'));
		return $this->returnAmount($this->CI->wallet_model->getTotalBalance($this->playerId));
	}

	public function sum_deposit_amount($from_datetime, $to_datetime, $min_amount) {
		$val=null;
		if($this->process_mock('sum_deposit_amount', $val)){
			return $val;
		}

		//sum
		$this->CI->load->model(array('transactions'));
		return $this->returnAmount($this->CI->transactions->sumDepositAmount(
			$this->playerId, $from_datetime, $to_datetime, $min_amount));
	}

	public function sum_withdrawal_amount($from_datetime, $to_datetime, $min_amount) {
		$val=null;
		if($this->process_mock('sum_withdrawal_amount', $val)){
			return $val;
		}

		//sum
		$this->CI->load->model(array('transactions'));
		return $this->returnAmount($this->CI->transactions->sumWithdrawAmount(
			$this->playerId, $from_datetime, $to_datetime, $min_amount));
	}

	// public function get_deposit_amount_list($from_datetime, $to_datetime, $min_amount) {
	// 	//get list
	// }

	public function get_available_deposit_amount($deposit_type, $from_datetime) {
		$val=null;
		if($this->process_mock('get_available_deposit_amount', $val)){
			return $val;
		}

		$depositAmount = 0;
		$this->CI->load->model(array('transactions'));
		//get from transactions
		// * deposit_type: 'last', 'first'
		switch ($deposit_type) {
			case self::DEPOSIT_TYPE_LAST:
				$depositAmount = $this->CI->transactions->getLastDepositAmount($this->playerId);
				break;
			case self::DEPOSIT_TYPE_FIRST:
				$depositAmount = $this->CI->transactions->getFirstDepositAmount($this->playerId, $from_datetime);
				break;
		}

		return $this->returnAmount($depositAmount);
	}

	public function get_game_result_amount($from_datetime, $to_datetime, $gamePlatformId = null, $gameTypeId = null) {
		$val=null;
		if($this->process_mock('get_game_result_amount', $val)){
			return $val;
		}

		//get from total_player_game_hour
		$this->CI->load->model(array('total_player_game_hour'));
		return $this->returnAmount($this->CI->total_player_game_hour->getResultByPlayers(
			$this->playerId, $from_datetime, $to_datetime, $gamePlatformId, $gameTypeId));
	}

    public function get_game_betting_amount($from_datetime, $to_datetime) {
        $val=null;
        if($this->process_mock('get_game_betting_amount', $val)){
            return $val;
        }

        //get from total_player_game_hour
        $this->CI->load->model(array('total_player_game_hour'));
        list($totalBet, $totalWin, $totalLoss) = $this->CI->total_player_game_hour->getPlayerTotalBetsWinsLossByDatetime(
            $this->playerId, $from_datetime, $to_datetime);
        return $this->returnAmount($totalBet);
    }

    public function get_game_betting_count($from_datetime, $to_datetime, $game_platforms = NULL, $bet_limit = 0) {
        $val=null;
        if($this->process_mock('get_game_betting_count', $val)){
            return $val;
        }

        //get from total_player_game_hour
        $this->CI->load->model(array('game_logs'));
        $totalBetCount = $this->CI->game_logs->getPlayerTotalBetCount($this->playerId, $from_datetime, $to_datetime, $game_platforms, $bet_limit);
        return $this->returnAmount($totalBetCount);
    }

	public function sum_bonus_amount_today() {
		$val=null;
		if($this->process_mock('sum_bonus_amount_today', $val)){
			return $val;
		}

		list($from, $to) = $this->CI->utils->getTodayStringRange();
		return $this->returnAmount($this->sum_bonus_amount($from, $to));
	}

	public function sum_bonus_amount($from_datetime, $to_datetime) {
		$val=null;
		if($this->process_mock('sum_bonus_amount', $val)){
			return $val;
		}

		$this->CI->load->model(array('player_promo'));
		$promorulesId = $this->promorule['promorulesId'];
		return $this->returnAmount($this->CI->player_promo->sumBonusAmount(
			$this->playerId, $promorulesId, $from_datetime, $to_datetime));
	}

	public function last_day_balance($date) {
		$this->CI->load->model(array('wallet_model'));
		// $today = $this->CI->utils->getTodayForMysql();
		$balance = $this->CI->wallet_model->getLastDayTotalBalance($this->playerId, $date);
		if ($balance === null) {
			$balance = $this->CI->wallet_model->getTotalBalance($this->playerId);
		}
		return $this->returnAmount($balance);
	}

	public function get_balance_by_date($date) {
		$this->CI->load->model(array('wallet_model'));
		// $today = $this->CI->utils->getTodayForMysql();
		$balance = $this->CI->wallet_model->getTotalBalanceByDate($this->playerId, $date);
		if ($balance === null) {
			$balance = $this->CI->wallet_model->getTotalBalance($this->playerId);
		}
		return $this->returnAmount($balance);
	}

	public function get_loss_by_date($date) {
		$sum_deposit = $this->sum_deposit_by_date($date);
		$sum_withdraw = $this->sum_withdraw_by_date($date);
		$balance = $this->get_balance_by_date($date);

		return $sum_deposit - $sum_withdraw - $balance;
	}

	public function get_close_loss_include_game_log_by_date($date){
        $val=null;
        if($this->process_mock('get_close_loss_include_game_log_by_date', $val)){
            return $val;
        }

        $sum_deposit = $this->sum_deposit_by_date($date);
        $sum_withdraw = $this->sum_withdraw_by_date($date);

        $this->CI->load->model(array('wallet_model','game_logs'));
        $lastBalance = $this->CI->wallet_model->getLastTotalBalanceByDate($this->playerId, $date);
        $date_from = null;
        if(empty($lastBalance)){
            $balance = $this->CI->wallet_model->getTotalBalance($this->playerId);
            $date_from = strtotime($date . ' 00:00:00');
        }else{
            $balance = $lastBalance['total_balance'];
            $date_from = strtotime($lastBalance['created_at']);
            if ($balance === null) {
                $balance = $this->CI->wallet_model->getTotalBalance($this->playerId);
            }
        }

        $date_to = strtotime($date . ' 23:59:59');
        if( ($date_from - $date_to) > 300 ){
            $close_loss = $this->CI->game_logs->getCloseLossByPlayer($this->playerId, $date_from, $date_to);
            return $sum_deposit - $sum_withdraw - ($balance + $close_loss);
        }

        return $sum_deposit - $sum_withdraw - $balance;
    }

	public function count_approved_promo_by_date($date) {
		$this->CI->load->model(array('player_promo'));

		$fromDatetime = $date . ' ' . Utils::FIRST_TIME;
		$toDateTime = $date . ' ' . Utils::LAST_TIME;
		$promorulesId = $this->promorule['promorulesId'];

		$cnt = $this->CI->player_promo->countPlayerPromo($this->playerId, $promorulesId,
			$fromDatetime, $toDateTime);
		return $cnt;
	}

	public function get_today() {
		return $this->CI->utils->getTodayForMysql();
	}

	public function get_yesterday() {

		return $this->CI->utils->getLastDay($this->CI->utils->getTodayForMysql());
	}

	public function getUtils() {
		return $this->CI->utils;
	}

	public function getTransactions() {
		$this->CI->load->model(array('transactions'));

		return $this->CI->transactions;
	}

	public function exists_any_deposit($date) {
		$this->CI->load->model(array('transactions'));
		$cnt = $this->CI->transactions->countDepositByPlayerAndDate($this->playerId, $date);
		return $cnt > 0;
	}

	public function sum_deposit_by_date($date, $min_amount = 0) {
		$this->CI->load->model(array('transactions'));

		return $this->returnAmount($this->CI->transactions->sumDepositAmount(
			$this->playerId, $date . ' 00:00:00', $date . ' 23:59:59', $min_amount));
	}

	public function sum_withdraw_by_date($date, $min_amount = 0) {
		$this->CI->load->model(array('transactions'));

		return $this->returnAmount($this->CI->transactions->sumWithdrawAmount(
			$this->playerId, $date . ' 00:00:00', $date . ' 23:59:59', $min_amount));
	}

	public function debug_log($msg) {
		$promorulesId = isset($this->promorule['promorulesId']) ? $this->promorule['promorulesId'] : null;

		$this->appendToDebugLog($msg, '');
		// $this->appendToDebugLog($msg, ['player_id' => $this->playerId, 'promorulesId' => $promorulesId,
		// 	'playerBonusAmount' => $this->playerBonusAmount, 'depositAmount' => $this->depositAmount]);
		// log_message('debug','runtime debug_log');
		$this->CI->utils->debug_log($msg, ['player_id' => $this->playerId, 'promorulesId' => $promorulesId,
			'playerBonusAmount' => $this->playerBonusAmount, 'depositAmount' => $this->depositAmount]);
	}

	public function belong_to_aff($aff_name) {
		$this->CI->load->model(array('affiliatemodel'));
		return $this->CI->affiliatemodel->belongToAffUsername($this->playerId, $aff_name);
	}

	public function get_min_deposit_condition() {
		$this->CI->load->model(array('promorules'));

		$minAmount = 0;
		if ($this->promorule['depositConditionNonFixedDepositAmount'] == Promorules::NON_FIXED_DEPOSIT_MIN_MAX) {
			$minAmount = $this->promorule['nonfixedDepositMinAmount'];
		}

		return $minAmount;
	}

	public function get_max_deposit_condition() {
		$this->CI->load->model(array('promorules'));

		$maxAmount = PHP_INT_MAX;
		if ($this->promorule['depositConditionNonFixedDepositAmount'] == Promorules::NON_FIXED_DEPOSIT_MIN_MAX) {
			$maxAmount = $this->promorule['nonfixedDepositMaxAmount'];
		}

		return $maxAmount;
	}

	public function get_any_available_deposit_info($frequency, $times) {
		return $this->get_available_deposit_info($frequency, $times, null, null);
	}

	/**
	 * all, daily, weekly, monthly, first or times
	 */
	public function get_available_deposit_info($frequency, $times, $min, $max) {
		return $this->get_deposit_by($frequency, $times, $min, $max);
		// $this->CI->load->model(array('transactions'));
		// $today = $this->CI->utils->getTodayForMysql();
		// $row=$this->CI->transactions->getAvailableDepositInfoByFreq($this->playerId,
		// 	$frequency, $today, $min, $max, $times,
		// 	[Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET,
		// 	Transactions::TRANSFER_TO_MAIN_FROM_BALANCE_AFFILIATE,
		// 	Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET,
		// 	Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET,
		// 	Transactions::WITHDRAWAL]);
		// if($row){
		// 	return $row->id;
		// }
		// return null;
	}

	public function reached_limit_promotion($frequency, $times) {
		$this->CI->load->model(array('player_promo'));
		$promorulesId=$this->promorule['promorulesId'];
		$today = $this->CI->utils->getTodayForMysql();
		$t= $this->CI->player_promo->getTimesPromotion($frequency, $today, $this->playerId, $promorulesId);
		return $t >= $times;
	}

	/**
	 * Get transactions.id for the exact deposit record that matches criteria
	 * @param 	string 	$frequency	either of 'all', 'weekly', 'monthly'
	 * @param 	string 	$times 		number (n-th deposit), or 'first', 'last'
	 * @param 	float 	$min 		Minimum amount, or null
	 * @param 	float 	$max 		Maximum amount, or null
	 * @return 	int 	transaction ID
	 */
	public function get_deposit_by($frequency, $times, $min, $max) {
		$this->CI->load->model(array('transactions'));
		$today = $this->CI->utils->getTodayForMysql();
		//before transfer and withdraw
		$row = $this->CI->transactions->getAvailableDepositInfoByFreq($this->playerId,
			$frequency, $today, $min, $max, $times,
			[Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET,
				Transactions::TRANSFER_TO_MAIN_FROM_BALANCE_AFFILIATE,
				Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET,
				Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET,
				Transactions::WITHDRAWAL]);
		if ($row) {
			return $row->id;
		}

		return null;
	}

	public function get_deposit_times($from_date, $to_date, $min = NULL, $max = NULL){
        return 'test';
    }

    /**
     * Get player's all-time deposit count (from registration)
     * OGP-11379
     * @return	int		Count of deposits
     */
	public function get_deposit_count_all(){
        $this->CI->load->model(array('transactions'));
        $res = $this->CI->transactions->countDepositByPlayer($this->playerId);

        return $res;
    }

    /**
     *
     * exists double ip
     *
     * @param  string $type        registration, last_login, deposit, withdrawal, main_wallet_to_sub_wallet, sub_wallet_to_main_wallet
     * @param  string $exists_type [null], only_ip, with_promo
     * @return bool
     */
	public function exists_double_ip($type, $exists_type) {
		$val=null;
		if($this->process_mock('exists_double_ip', $val, 'bool')){
			return $val;
		}

		$this->CI->load->model(array('http_request'));
		$exists = false;
		$reqType = $this->CI->http_request->stringTypeToRequestType($type);
		//check
		$ip=$this->CI->http_request->getIpByType($this->playerId, $reqType);
		$this->debug_log('get ip:'.$ip);
		if($ip){
			if(empty($exists_type)){
				$exists_type='with_promo';
			}

			if($exists_type=='only_ip'){

				$exists= $this->CI->http_request->existsIp($ip, $this->playerId, null);

			}else{

				$promorulesId=$this->promorule['promorulesId'];
				$exists= $this->CI->http_request->existsIpWithPromotion($ip, $this->playerId, $promorulesId);
			}
			$this->debug_log('check ip:'.$ip.', exists:'.$exists.', type:'.$type);
		}
		return $exists;
	}

	public function init($msg) {
		$this->utils->debug_log($msg . ' init runtime ok');
	}

	public function get_from_to_date_by_type($type){
		//default is today
		if($type=='this_month'){
			list($from, $to)=$this->utils->getThisMonthRange();
		}else{
			list($from, $to)=$this->utils->getTodayDateTimeRange();
			$from=$this->utils->formatDateTimeForMysql($from);
			$to=$this->utils->formatDateTimeForMysql($to);
		}

		return ['from'=>$from, 'to'=>$to ];
	}

	/**
	 * Get record of latest deposit by various frequency options
	 * @param	string 	$frequency	One of following:
	 *       all		latest deposit of all-time
	 *       weekly		of current week (Monday to Sunday)
	 *       monthly	of current month (1st day to today)
	 * @return	array 	array of deposit record
	 */
	public function get_last_deposit_by($frequency) {
		$this->CI->load->model(array('transactions'));
		$today = $this->CI->utils->getTodayForMysql();
		//before transfer and withdraw
		$transRow = $this->CI->transactions->getLastDepositInfoByFreq($this->playerId, $frequency, $today);
		if ($transRow) {
			return $transRow;
		}

		return null;
	}

	public function get_last_week_monday_to_sunday_date($type, $date = null){
		$extra = null;
		if(!empty($date)){
			$extra =[];
			$extra['week_start'] = $date;
		}

		return $this->get_date_type($type, $extra);
	}

	public function get_date_type($type, $extra_info = NULL){

		$val=null;
		if($this->process_mock('get_date_type_'.$type, $val)){
			return $val;
		}

		switch ($type){
			case self::DATE_YESTERDAY_START:
				$d=$this->CI->utils->getYesterdayForMysql().' '.Utils::FIRST_TIME;
				break;
			case self::DATE_YESTERDAY_END:
				$d=$this->CI->utils->getYesterdayForMysql().' '.Utils::LAST_TIME;
				break;
			case self::DATE_TODAY_START:
				$d=$this->CI->utils->getTodayForMysql().' '.Utils::FIRST_TIME;
				break;
			case self::DATE_TODAY_END:
				$d=$this->CI->utils->getTodayForMysql().' '.Utils::LAST_TIME;
				break;
			case self::TO_TYPE_NOW:
				$d=$this->CI->utils->getNowForMysql();
				break;
            case self::DATE_LAST_WEEK_START:
                $week_start = (isset($extra_info['week_start'])) ? $extra_info['week_start'] : 'sunday';

                $previous_week = strtotime('-1 week +1 day', strtotime($this->CI->utils->getNowForMysql()));
                $dt = new DateTime();
                $dt->setTimestamp(strtotime('last ' . $week_start . ' midnight', $previous_week));

                $d=$this->CI->utils->formatDateForMysql($dt).' '.Utils::FIRST_TIME;
                break;
            case self::DATE_LAST_WEEK_END:
                $week_start = (isset($extra_info['week_start'])) ? $extra_info['week_start'] : 'sunday';
                $previous_week = strtotime("-1 week +1 day", strtotime($this->CI->utils->getNowForMysql()));
                $dt = new DateTime();
                $dt->setTimestamp(strtotime('last ' . $week_start . ' +6 day', $previous_week));

                $d=$this->CI->utils->formatDateForMysql($dt).' '.Utils::LAST_TIME;
                break;
            case self::DATE_THIS_WEEK_START:
            	//always monday
                $d=date('Y-m-d H:i:s', strtotime('midnight monday this week'));
                break;
            case self::DATE_THIS_WEEK_END:
            	//to now
                $d=$this->CI->utils->getNowForMysql();
                break;
			case self::LAST_RELEASE_BONUS_TIME:
				$this->CI->load->model(['player_promo']);
				$row=$this->CI->player_promo->getLastReleasedPlayerPromo($this->playerId, $this->promorule['promorulesId']);
				if(!empty($row)){
					$d=$row['dateProcessed'];
				}else{
					$d=self::EMPTY_DATETIME;
				}

				break;
			default:
				$d=new DateTime($type);
				$d=$this->CI->utils->formatDateTimeForMysql($d);
				break;
		}

		return $d;
	}

	/**
	 * from 12 to 12
	 * @param  string $type
	 * @return string datetime
	 */
	public function get_date_type_12($type){
		$d='';
		switch ($type){
			case self::DATE_YESTERDAY_START:
				$d = new \DateTime();
				$d->modify('-2 day');
				$str=$this->formatDateForMysql($d);
				$d=$str.' 12:00:00';
				break;
			case self::DATE_YESTERDAY_END:
				$d=$this->CI->utils->getYesterdayForMysql().' 11:59:59';
				break;
			case self::DATE_TODAY_START:
				$d=$this->CI->utils->getYesterdayForMysql().' 12:00:00';
				break;
			case self::DATE_TODAY_END:
				$d=$this->CI->utils->getTodayForMysql().' 11:59:59';
				break;
		}

		return $d;
	}

	public function times_released_bonus_on_this_promo_today(){
		$val=null;
		if($this->process_mock('times_released_bonus_on_this_promo_today', $val)){
			return $val;
		}

		//player, promo rule id

		$playerId=$this->playerId;
		$promorulesId=$this->promorule['promorulesId'];

		$this->CI->load->model(['player_promo']);

		$from=$this->get_date_type(self::DATE_TODAY_START);
		$to=$this->get_date_type(self::DATE_TODAY_END);

		$playerPromoReleasedCount=$this->CI->player_promo->countPlayerDuplicatePromo($playerId, $promorulesId,
				[
				Player_promo::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION,
				Player_promo::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS,
				Player_promo::TRANS_STATUS_APPROVED
				], $from, $to);

		return $playerPromoReleasedCount;
	}

	public function appendToDebugLog($log, $context=null){
		$this->log_str.=$log.' '.var_export($context, true)."\n";
	}

	public function count_deposit_by_date($from_datetime, $to_datetime, $min_amount) {
		$val=null;
		if($this->process_mock('count_deposit_by_date', $val)){
			return $val;
		}

		//sum
		$this->CI->load->model(array('transactions'));
		return $this->returnAmount($this->CI->transactions->countDepositAmount(
			$this->playerId, $from_datetime, $to_datetime, $min_amount));
	}

	public function count_deposit_by_day($periodFrom, $periodTo, $other_conditions = NULL){
        $val=null;
        if($this->process_mock('count_deposit_by_date', $val)){
            return $val;
        }

        $this->CI->load->model(array('transactions'));
        $deposit_list = $this->CI->transactions->count_deposit_by_day($this->playerId, $periodFrom, $periodTo, $other_conditions);

        return $deposit_list;
    }

	public function count_withdraw_by_date($from_datetime, $to_datetime, $min_amount) {
		$val=null;
		if($this->process_mock('count_withdraw_by_date', $val)){
			return $val;
		}

		//sum
		$this->CI->load->model(array('transactions'));
		return $this->returnAmount($this->CI->transactions->countWithdrawAmount(
			$this->playerId, $from_datetime, $to_datetime, $min_amount));
	}

	public function get_min_date_type($type_arr){
		$val=null;
		if($this->process_mock('get_min_date_type', $val)){
			return $val;
		}

		$result=null;
		if(!empty($type_arr)){

			foreach ($type_arr as $t) {
				$d=$this->get_date_type($t);
				//ignore empty
				if($result===null || ($d!=self::EMPTY_DATETIME && $d<$result)){
					$result=$d;
				}
			}
		}

		return $result;
	}

	public function get_max_date_type($type_arr){
		$val=null;
		if($this->process_mock('get_max_date_type', $val)){
			return $val;
		}

		$result=null;
		if(!empty($type_arr)){

			foreach ($type_arr as $t) {
				$d=$this->get_date_type($t);
				if($result===null || ($d!=self::EMPTY_DATETIME && $d>$result)){
					$result=$d;
				}
			}
		}

		return $result;
	}

	private function process_mock($name, &$val, $type=null){

		if($this->dry_run && isset($this->extra_info['mock'][$name])){
			$val=$this->extra_info['mock'][$name];
			if($type=='bool' || $type=='boolean'){
				$this->debug_log('convert bool mock:'.$name.', val:'.$val.', type:'.$type);
				$val=$val=='true' || $val=='on' || $val=='1';
			}

			$this->debug_log('process mock:'.$name.', val:'.$val.', type:'.$type);
			return true;
		}

		return false;
	}

	public function rescue($from_type_arr, $to_date_type, $bonus_map, $fixed_bonus_percent, $mode){
		if($mode==self::RESCUE_MODE_DEPOSIT_MINUS_WITHDRAWAL){

		}
	}

	public function checkPlayerRegisteredDate($fromDateTime, $toDateTime) {
		$val=null;
		if($this->process_mock('checkPlayerRegisteredDate', $val)){
			return $val;
		}

		$this->CI->load->model(['player_model']);
		$playerInfo = $this->CI->player_model->getPlayerArrayById($this->playerId);
		$registeredDate = new DateTime($playerInfo['createdOn']);
		$fromDateTime = new DateTime($fromDateTime);
		if (!empty($toDateTime)) {
			$toDateTime = new DateTime($toDateTime);
			if ($registeredDate >= $fromDateTime && $registeredDate <= $toDateTime) {
				return true;
			}
		} else {
			if ($registeredDate >= $fromDateTime) {
				return true;
			}
		}
		return false;
	}

	public function is_player_filled_first_name(){
		$val=null;
		if($this->process_mock('is_player_filled_first_name', $val)){
			return $val;
		}

		$result=false;

		if(!empty($this->playerId)){
			$this->CI->load->model(['player_model']);
			$playerDetail = $this->CI->player_model->getPlayerDetailArrayById($this->playerId);
			if(!empty($playerDetail)) {
				$result = !empty($playerDetail['firstName']);
			}
		}

		return $result;

	}

	public function is_player_filled_last_name(){
		$val=null;
		if($this->process_mock('is_player_filled_last_name', $val)){
			return $val;
		}

		$result=false;

		if(!empty($this->playerId)){
			$this->CI->load->model(['player_model']);
			$playerDetail = $this->CI->player_model->getPlayerDetailArrayById($this->playerId);
			if(!empty($playerDetail)) {
				$result = !empty($playerDetail['lastName']);
			}
		}

		return $result;

	}

	public function is_player_at_least_one_withdrawal_bank(){
		$val=null;
		if($this->process_mock('is_player_at_least_one_withdrawal_bank', $val)){
			return $val;
		}

		$result=false;

		if(!empty($this->playerId)){
			$this->CI->load->model(['playerbankdetails']);

			$result=$this->CI->playerbankdetails->exists_one_withdrawal_bank($this->playerId);
		}

		return $result;

	}

	public function already_released_promo_rule($promorulesId, $date_type){
		$val=null;
		if($this->process_mock('already_released_promo_rule', $val)){
			return $val;
		}

		$result=false;

		$start=null;
		$end=null;
		if($date_type=='today'){
			$start=date('Y-m-d').' '.Utils::FIRST_TIME;
			$end=date('Y-m-d').' '.Utils::LAST_TIME;
		}else if($date_type=='yesterday'){
			$start=date('Y-m-d', strtotime('yesterday')).' '.Utils::FIRST_TIME;
			$end=date('Y-m-d', strtotime('yesterday')).' '.Utils::LAST_TIME;
		}else if($date_type=='thisweek'){
			$start = $this->get_date_type(self::DATE_THIS_WEEK_START);
			$end = $this->get_date_type(self::DATE_THIS_WEEK_END);
		}

		if(!empty($this->playerId) && !empty($promorulesId)){
			$this->CI->load->model(array('player_promo'));

			$row=$this->CI->player_promo->getLastReleasedPlayerPromo($this->playerId, $promorulesId, $start, $end);
			if(empty($row)){
				$result=false;
			}else{
				$result=true;
			}
		}

		return $result;

	}

	public function exists_double_realname(){
		$val=null;
		if($this->process_mock('exists_double_realname', $val, 'bool')){
			return $val;
		}

		$this->CI->load->model(array('player_model'));
		$exists = false;
		$playerDetail = $this->CI->player_model->getPlayerDetailArrayById($this->playerId);
		if(!empty($playerDetail)) {

			$row=$this->CI->player_model->searchByFirstName($playerDetail['firstName']);

			$exists=!empty($exists);

			// $promorulesId=$this->promorule['promorulesId'];
			// $exists= $this->CI->http_request->existsIpWithPromotion($ip, $this->playerId, $promorulesId);
			// $this->CI->utils->debug_log('check ip','ip',$ip, 'exists', $exists, 'playerId', $this->playerId, 'type', $type);
		}
		return $exists;

	}

	public function is_player_verified_mobile(){
		$val=null;
		if($this->process_mock('is_player_verified_mobile', $val, 'bool')){
			return $val;
		}

		$result=false;

		if(!empty($this->playerId)){
			$this->CI->load->model(['player_model']);
			$playerDetail = $this->CI->player_model->getPlayerDetailArrayById($this->playerId);
			if(!empty($playerDetail)) {
				$result = !empty($playerDetail['contactNumber']) &&
					$this->CI->player_model->isVerifiedPhone($this->playerId);
			}
		}

		return $result;

	}


	public function is_available_vip_level($vip_id_array){
		$val=null;
		if($this->process_mock('is_available_vip_level', $val, 'bool')){
			return $val;
		}

		$result=false;

		$this->debug_log('player id:'.$this->playerId.', vip_id_array:'.var_export($vip_id_array, true));

		if(!empty($this->playerId) && !empty($vip_id_array)){
			$this->CI->load->model(['group_level']);
			$levelId = $this->CI->group_level->getPlayerLevelId($this->playerId);

			$this->debug_log('player id:'.$this->playerId.', levelId:'.$levelId);
			if(!empty($levelId)) {
				$result=in_array($levelId, $vip_id_array);
			}
		}

		return $result;

	}

}

////END OF FILE///////////