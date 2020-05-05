<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/../base_model.php';

/**
 *
 * promo rule
 *
 */
abstract class Abstract_promo_rule extends BaseModel{

    // private static $instance;

    /**
     * don't call
     */
	public function __construct(){
		parent::__construct();
	}

	protected $playerId = null;
	protected $promorule = null;
	protected $promorulesId=null;
	protected $playerBonusAmount = null;
	protected $depositAmount = null;
	protected $levelName=null;
	protected $vipGroupName=null;
	protected $levelId=null;
	protected $vipGroupId=null;
	protected $levelFullName=null;
	protected $log_str='';
	protected $dry_run=false;
	protected $extra_info=null;

	public function init($playerId, $promorule, $playerBonusAmount = null, $depositAmount=null){

		$this->appendToDebugLog('start init:'.$this->getClassName());

		$this->load->model(['player_model', 'group_level', 'operatorglobalsettings']);
		$this->log_str='';
		$this->playerId = $playerId;
		$this->promorule = $promorule;
		$this->promorulesId = isset($this->promorule['promorulesId']) ? $this->promorule['promorulesId'] : null;
		$this->playerBonusAmount = floatval($playerBonusAmount);
		$this->depositAmount = floatval($depositAmount);
		$this->non_promo_withdraw_setting = $this->operatorglobalsettings->getSettingDoubleValue('non_promo_withdraw_setting');
		if(!empty($playerId)){
			$this->player= $this->player_model->getPlayerArrayById($playerId);

			$vipInfo=$this->group_level->getPlayerGroupLevelInfo($playerId);
			if(!empty($vipInfo)){
				$this->levelId=$vipInfo['levelId'];
				$this->levelName=$vipInfo['vipLevelName'];
				$this->vipGroupId=$vipInfo['vipSettingId'];
				$this->vipGroupName=$vipInfo['groupName'];
				$this->levelFullName=$this->vipGroupName.' - '.$this->levelName;
			}
		}

		$this->appendToDebugLog('after init:'.$this->getClassName(), [
			'playerId'=>$this->playerId, 'promorulesId'=>$this->promorulesId,
			'playerBonusAmount'=>$this->playerBonusAmount, 'depositAmount'=>$this->depositAmount,
			'non_promo_withdraw_setting'=>$this->non_promo_withdraw_setting,
		]);
	}

	public function appendToDebugLog($log, $context=null){
		if(!is_string($log)){
			$log=var_export($log, true);
		}
		$msg=$log.' '.var_export($context, true);
		$this->log_str.=$msg."\n";
		$this->utils->debug_log($msg, ['class'=>$this->getClassName(), 'player_id' => $this->playerId, 'promorulesId' => $this->promorulesId,
			'playerBonusAmount' => $this->playerBonusAmount, 'depositAmount' => $this->depositAmount]);
	}

	// public function debug_log($msg, $context=null) {
	// 	$this->appendToDebugLog($msg, $context);
	// }

    public function returnUnimplemented() {
        return ['success' => false, 'unimplemented' => true];
    }

    public function run($func, $description, &$extra_info, $dry_run){
		$this->dry_run=$dry_run;
		$this->extra_info=$extra_info;

		$this->appendToDebugLog('start '.$func.'===========================');

    	$result=$this->$func($description, $extra_info, $dry_run);

    	$this->appendToDebugLog('result of '.$func.'===========================', ['result'=>$result]);
		$extra_info['debug_log'].=$this->log_str;

		return $result;
    }

	/**
	 * get current promo rule class name
	 * @return string
	 */
	public abstract function getClassName();

	/**
	 * run bonus condition checker
	 * @param  array $description original description in rule
	 * @param  array $extra_info exchange data
	 * @param  boolean $dry_run
	 * @return  array ['success'=> success, 'message_lang'=> errorMessageLang]
	 */
	protected abstract function runBonusConditionChecker($description, &$extra_info, $dry_run);

	/**
	 * generate withdrawal condition
	 * @param  array $description original description in rule
	 * @param  array $extra_info exchange data
	 * @param  boolean $dry_run
	 * @return  array ['success'=> success, 'message_lang'=> errorMessageLang, 'withdrawal_condition_amount'=> withdrawal condition amount]
	 */
	protected abstract function generateWithdrawalCondition($description, &$extra_info, $dry_run);

    /**
     * generate transfer condition
     * @param  array $description original description in rule
     * @param  array $extra_info exchange data
     * @param  boolean $dry_run
     * @return  array ['success'=> success, 'message_lang'=> errorMessageLang, 'withdrawal_condition_amount'=> withdrawal condition amount]
     */
    protected abstract function generateTransferCondition($description, &$extra_info, $dry_run);

	/**
	 * release bonus
	 * @param  array $description original description in rule
	 * @param  array $extra_info exchange data
	 * @param  boolean $dry_run
	 * @return  array ['success'=> success, 'message_lang'=> errorMessageLang, 'bonus_amount'=> bonus amount]
	 */
	protected abstract function releaseBonus($description, &$extra_info, $dry_run);


	//==============helper
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

	public function callHelper($funcName, $params){
		$val=null;
		if($this->process_mock($funcName, $val)){
			return $val;
		}

		return call_user_func_array([$this,$funcName], $params);

	}

	public function process_mock($name, &$val, $type=null){

		if($this->dry_run && (isset($this->extra_info['mock'][$name]) || isset($this->extra_info['mock']['promo_rule_class_mock'][$name])) ){
			$val=isset($this->extra_info['mock'][$name]) ? $this->extra_info['mock'][$name] : $this->extra_info['mock']['promo_rule_class_mock'][$name];
			if($type=='bool' || $type=='boolean'){
				$this->appendToDebugLog('convert bool mock:'.$name.', val:'.$val.', type:'.$type);
				$val=$val=='true' || $val=='on' || $val=='1';
			}

			$this->appendToDebugLog('process mock:'.$name.', type:'.$type, ['val'=>$val]);
			return true;
		}

		return false;
	}

	private function getLastSQL(){
		return $this->db->last_query();
	}

	/**
	 * get last deposit record , no exist withdrawal after deposit
	 *
	 * @param  string  $start
	 * @param  string  $end
	 * @param  boolean $check_transfer
	 * @return array $row
	 */
	public function getLastDepositByDate($start, $end, $check_transfer=false) {
		//desc and first row is last deposit
		$orderBy='desc';
		return $this->getAnyDepositByDate($start, $end, 'first', null, null, $check_transfer, $orderBy);
	}

	/**
	 * get any times deposit
	 * @param  string  $start
	 * @param  string  $end
	 * @param  mixin  $times  'first', 'last', number
	 * @param  int  $min
	 * @param  int  $max
	 * @param  boolean $check_transfer check transfer transaction
	 * @return array $row
	 */
	public function getAnyDepositByDate($start, $end, $times, $min, $max, $check_transfer=false, $orderBy='asc') {

		$this->load->model(array('transactions'));
		// $today = $this->utils->getTodayForMysql();
		//before transfer and withdraw

		$disable_after_type=[Transactions::WITHDRAWAL];
		if($check_transfer){
			$disable_after_type=[Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET,
				Transactions::TRANSFER_TO_MAIN_FROM_BALANCE_AFFILIATE,
				Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET,
				Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET,
				Transactions::WITHDRAWAL];
		}

		$row = $this->transactions->getAvailableDepositInfoByDate($this->playerId,
			$start, $end, $min, $max, $times, $disable_after_type, $orderBy);
		if ($row) {
			if(is_object($row)){
				$row=(array)$row;
			}
			return $row;
		}

		return null;
	}
	/**
	 * search upgrade or downgrade record
	 * @param  int $type
	 * @param  string $fromDate
	 * @param  string $toDate
	 * @param  int $fromLevelId
	 * @param  int $toLevelId
	 * @return int
	 */
	public function searchFirstGradeRecord($type, $fromDate, $toDate, $fromLevelId, $toLevelId){
		$this->load->model(['group_level']);

		$recordId=null;
		//vipsettingcashbackruleId to newvipId
		//request_grade=Group_level::RECORD_UPGRADE or Group_level::RECORD_DOWNGRADE
		if($type==Group_level::RECORD_UPGRADE || $type==Group_level::RECORD_DOWNGRADE){
			$this->group_level->queryFirstGradeRecordIdBy($this->playerId, $type, $fromDate, $toDate, $fromLevelId, $toLevelId);
		}

		return $recordId;
	}

	/**
	 * getBetsAndDepositByDate
	 * @param  string $fromDate
	 * @param  string $toDate
	 * @return array [$bets, $deposit]
	 */
	public function getBetsAndDepositByDate($fromDate, $toDate){
		$this->load->model(['player_model']);

		return $this->player_model->getBetsAndDepositByDate($this->playerId, $fromDate, $toDate);
	}

	/**
	 * existsReleasedBonusByCode
	 * @param  string $exclude_promo_code
	 * @param  string $fromDate
	 * @param  string $toDate
	 * @return bool
	 */
	public function existsReleasedBonusByCode($exclude_promo_code, $fromDate, $toDate){
		$this->load->model(['player_promo']);

		return $this->player_promo->existsReleasedBonusByCode($this->playerId, $exclude_promo_code, $fromDate, $toDate);
	}

	/**
	 * search upgrade level history
	 * @return int levelId
	 */
	public function getLastUpgradeLevelOrCurrentLevel($endAt){
		$levelId=$this->levelId;
		$this->load->model(['group_level']);
		$fromDate=$this->player['createdOn'];
		$toDate=$endAt;
		$playerId=$this->playerId;
		$row=$this->group_level->queryLastGradeRecordRowBy($playerId, $fromDate, $toDate);

		$this->appendToDebugLog('queryLastGradeRecordRowBy: '.$playerId,[
			'fromDate'=> $fromDate, 'toDate'=> $toDate , 'row'=>$row, 'sql'=>$this->getLastSQL()
		]);

		if(!empty($row)){
			$levelId=$row['newvipId'];
		}

		return $levelId;
	}

	public function getLastMonthEndTime(){
		$d=new DateTime();
		$d->modify('-1 month');
		$d->modify('+1 day');
		return $d->format('Y-m-t').' '.Utils::LAST_TIME;
	}

	public function isCheckingBeforeDeposit(){
		if(isset($this->extra_info['is_checking_before_deposit'])){
			return $this->extra_info['is_checking_before_deposit'];
		}

		return false;
	}

	public function calcWithdrawConditionAndCheckMaxBonus($percentage, $times, $timesForDeposit=null){

		$this->appendToDebugLog('calcWithdrawConditionAndCheckMaxBonus init',[
			'playerBonusAmount'=>$this->playerBonusAmount, 'depositAmount'=>$this->depositAmount,
			'percentage'=>$percentage, 'times'=>$times, 'timesForDeposit'=>$timesForDeposit,
		]);
		if($this->playerBonusAmount<=0 || $this->depositAmount<=0){
			return 0;
		}

		if($timesForDeposit===null){
			$timesForDeposit=$this->non_promo_withdraw_setting;
		}

		$playerBonusAmount=$this->playerBonusAmount;
		$depositAmount=$this->depositAmount;
		$withdrawal_condition_amount=($playerBonusAmount+$depositAmount)*$times;
		$depositWithBonus=round($playerBonusAmount/$percentage, 2);
		//means more than max bonus
		if($depositAmount>$depositWithBonus){
			$depositWithoutBonus=round(($depositAmount-$depositWithBonus), 2);
			$withdrawal_condition_amount=($playerBonusAmount+$depositWithBonus)*$times
				+$depositWithoutBonus*$timesForDeposit;
			$this->appendToDebugLog('calcWithdrawConditionAndCheckMaxBonus more than max bonus',
				['depositWithBonus'=>$depositWithBonus, 'depositWithoutBonus'=>$depositWithoutBonus,
				'withdrawal_condition_amount'=>$withdrawal_condition_amount]);
		}else{
			$this->appendToDebugLog('calcWithdrawConditionAndCheckMaxBonus normal',
				['depositWithBonus'=>$depositWithBonus, 'depositWithoutBonus'=>$depositWithoutBonus,
				'withdrawal_condition_amount'=>$withdrawal_condition_amount]);
		}

		return $withdrawal_condition_amount;
	}

	/**
	 * getLevelListDownToFirstLevel
	 * @param int $levelId
	 * @return int levelId
	 */
	public function getLevelIdListDownToFirstLevel($levelId){
		if(!empty($levelId)){
			$this->load->model(['group_level']);
			//high level to low level
			$rows=$this->group_level->getLevelListOnSameGroupFrom($levelId);

			$this->appendToDebugLog('getLevelIdListDownToFirstLevel: '.$levelId,[
				'rows'=> $rows,
			]);

			return $rows;
		}
		return null;
	}

}
