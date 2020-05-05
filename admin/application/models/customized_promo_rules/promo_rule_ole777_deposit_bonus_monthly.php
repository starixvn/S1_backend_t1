<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) .'/abstract_promo_rule.php';

/**
 *
 * 存送优惠
 *
 * https://manage.tripleonetech.net/jira/browse/OGP-9194
 *
 * 10-17号内最接近现在的一笔有效存款，不允许在取款后
 * 如果升级报表里的最后一条记录并且是这个月之前的
 * 存在这个记录就使用，如果不存在就按现在的级别算

condition:
{
    "class": "promo_rule_ole777_deposit_bonus_monthly",
    "bet_condition_times": 10,
    "allowed_date": {
        "start": "10",
        "end": "17"
    },
    "bonus_settings": {
        "1": {"percentage": 30, "max_bonus": 277},
        "29": {"percentage": 30, "max_bonus": 777}
    }
}

mock:
{
    "today": "2018-09-10",
    "getLastDepositByDate":{
		"amount": 100,
		"id": 559
    }
}

mock for isCheckingBeforeDeposit
{
    "today": "2018-09-10",
    "isCheckingBeforeDeposit": true
}

 *
 *
 */
class Promo_rule_ole777_deposit_bonus_monthly extends Abstract_promo_rule{

	public function init($playerId, $promorule, $playerBonusAmount = null, $depositAmount=null){
		parent::init($playerId, $promorule, $playerBonusAmount, $depositAmount);
	}

	public function getClassName(){
		return 'Promo_rule_ole777_deposit_bonus_monthly';
	}

	/**
	 * run bonus condition checker
	 * @param  array $description original description in rule
	 * @param  array $extra_info
	 * @param  boolean $dry_run
	 * @return  array ['success'=> success, 'message'=> errorMessageLang, 'continue_process_after_script' => FALSE]
	 */
	protected function runBonusConditionChecker($description, &$extra_info, $dry_run){
		$success=false;
		$errorMessageLang=null;

		$result=['success'=>$success, 'message'=>$errorMessageLang, 'continue_process_after_script' => FALSE];

		$today=$this->utils->getTodayForMysql();
		if($this->process_mock('today', $today)){
			//use mock data
			$this->appendToDebugLog('use mock today', ['today'=>$today]);
		}

		$bonus_settings=$description['bonus_settings'];
		$endAt=$this->callHelper('getLastMonthEndTime', []);
		$levelId=$this->callHelper('getLastUpgradeLevelOrCurrentLevel',[$endAt]);
		$this->appendToDebugLog('search level id getLastUpgradeLevelOrCurrentLevel', ['endAt'=>$endAt, 'levelId'=>$levelId]);
		if(array_key_exists($levelId, $bonus_settings)){
			//check date
			$allowed_date=$description['allowed_date'];
			$this->appendToDebugLog('check allowed_date', ['allowed_date'=>$allowed_date]);
			$d=new DateTime($today);
			$minDate=$d->format('Y-m-').$allowed_date['start'];
			$maxDate=$d->format('Y-m-').$allowed_date['end'];
			$currentDate=$d->format('Y-m-d');
			$check_transfer=false;
			$trans=$this->callHelper('getLastDepositByDate', [$minDate.' '.Utils::FIRST_TIME, $maxDate.' '.Utils::LAST_TIME]);

			$this->appendToDebugLog('check date and trans id', ['currentDate'=>$currentDate, 'minDate'=>$minDate, 'maxDate'=>$maxDate, 'trans'=>$trans]);

			if($currentDate>=$minDate && $currentDate<=$maxDate){
                $result['deposit_tran_id'] = $trans['id'];
				if($this->callHelper('isCheckingBeforeDeposit',[])){
					$this->appendToDebugLog('ignore trans', ['is_checking_before_deposit'=>$extra_info['is_checking_before_deposit']]);
					//it's for deposit choice
					$result['success']=true;
				}else{
					if(!empty($trans)){
						$result['success']=true;
					}else{
						$result['message']='Not found available deposit';
					}
				}
			}else{
				$result['message']='Not right date';
			}
		}else{
			$result['message']='Not right group level';
		}

		return $result;
	}

	/**
	 * generate withdrawal condition
	 * @param  array $description original description in rule
	 * @param  array $extra_info
	 * @param  boolean $dry_run
	 * @return  array ['success'=> success, 'message'=> errorMessageLang, 'withdrawal_condition_amount'=> withdrawal condition amount]
	 */
	protected function generateWithdrawalCondition($description, &$extra_info, $dry_run){
		$success=false;
		$errorMessageLang=null;
		$bonus_amount=0;

		$bonus_settings=$description['bonus_settings'];
		$allowed_date=$description['allowed_date'];
		$endAt=$this->callHelper('getLastMonthEndTime', []);
		$this->appendToDebugLog('search level id getLastUpgradeLevelOrCurrentLevel', ['endAt'=>$endAt, 'levelId'=>$levelId]);
		$this->appendToDebugLog('calc bonus', ['bonus_settings'=>$bonus_settings, 'allowed_date'=>$allowed_date, 'levelId'=>$levelId]);

		$times=$description['bet_condition_times'];
		if($times>0){
			$levelId=$this->callHelper('getLastUpgradeLevelOrCurrentLevel',[$endAt]);
			if(array_key_exists($levelId, $bonus_settings)){
				$setting=$bonus_settings[$levelId];

				$this->appendToDebugLog('get bonus setting', ['bonus_settings'=>$setting, 'levelId'=>$levelId]);
				$maxBonus=$setting['max_bonus'];
				$withdrawal_condition_amount=$this->callHelper('calcWithdrawConditionAndCheckMaxBonus',
					[$setting['percentage']/100, $times]);
				$success=$withdrawal_condition_amount>0;
				if(!$success){
					if($this->playerBonusAmount<=0){
						$errorMessageLang='Bonus amount is not correct';
					}else if($this->depositAmount<=0){
						$errorMessageLang='Deposit amount is not correct';
					}else{
						$errorMessageLang='Withdraw condition is not correct';
					}
				}
			}else{
				$errorMessageLang='Not right group level';
			}
		}else{
			$errorMessageLang='Lost bet_condition_times in settings';
		}

		$result=['success'=>$success, 'message'=>$errorMessageLang, 'withdrawal_condition_amount'=>round($withdrawal_condition_amount, 2)];
		return $result;
	}

    /**
     * generate transfer condition
     * @param  array $description original description in rule
     * @param  array $extra_info exchange data
     * @param  boolean $dry_run
     * @return  array ['success'=> success, 'message_lang'=> errorMessageLang, 'withdrawal_condition_amount'=> withdrawal condition amount]
     */
    protected function generateTransferCondition($description, &$extra_info, $dry_run){
        return $this->returnUnimplemented();
    }

	/**
	 * release bonus
	 * @param  array $description original description in rule
	 * @param  array $extra_info
	 * @param  boolean $dry_run
	 * @return  array ['success'=> success, 'message'=> errorMessageLang, 'bonus_amount'=> bonus amount]
	 */
	protected function releaseBonus($description, &$extra_info, $dry_run){

		//存送-30% 最高277-10倍	存送-30% 最高777-10倍	存送-35% 最高1177-10倍	存送-35% 最高2777-10倍	存送-37% 最高5777-10倍	存送-40% 最高10000-10倍
		$success=false;
		$errorMessageLang=null;
		$bonus_amount=0;

		$bonus_settings=$description['bonus_settings'];
		$allowed_date=$description['allowed_date'];
		$endAt=$this->callHelper('getLastMonthEndTime', []);
		$levelId=$this->callHelper('getLastUpgradeLevelOrCurrentLevel',[$endAt]);
		$this->appendToDebugLog('search level id getLastUpgradeLevelOrCurrentLevel', ['endAt'=>$endAt, 'levelId'=>$levelId]);
		$this->appendToDebugLog('calc bonus', ['bonus_settings'=>$bonus_settings, 'allowed_date'=>$allowed_date, 'levelId'=>$levelId]);

		if(array_key_exists($levelId, $bonus_settings)){
			$setting=$bonus_settings[$levelId];

			$this->appendToDebugLog('get bonus setting', ['bonus_settings'=>$setting, 'levelId'=>$levelId]);
			//last deposit in this month
			$this->load->model(['transactions']);
			$d=new DateTime();
			$minDate=$d->format('Y-m-').$allowed_date['start'];
			$maxDate=$d->format('Y-m-').$allowed_date['end'];
			$trans=$this->callHelper('getLastDepositByDate', [$minDate.' '.Utils::FIRST_TIME, $maxDate.' '.Utils::LAST_TIME]);
			if(!empty($trans)){
				$last_deposit_this_month=$trans['amount'];
				$bonus_amount=($setting['percentage']/100)*$last_deposit_this_month;
				if($bonus_amount>$setting['max_bonus']){
					$bonus_amount=$setting['max_bonus'];
				}
				$success=true;
			}
		}

		$result=['success'=>$success, 'message'=>$errorMessageLang, 'bonus_amount'=>$bonus_amount];
		return $result;
	}
}
