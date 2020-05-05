<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) .'/abstract_promo_rule.php';

/**
 *
 * 招财彩金
 *
 * https://manage.tripleonetech.net/jira/browse/OGP-9195
 *
 * 20-27号允许申请，上个月的投注和存款符合条件，发固定奖金，一个月一次

condition:
{
    "class": "promo_rule_ole777_bets_deposit_bonus",
    "allowed_date": {
        "start": "20",
        "end": "27"
    },
    "exclude_promo_code": "lk1avrt0",
    "bonus_settings": {
        "1": {"min_bets": 250000, "min_deposit": 25000, "fixed_bonus": 277},
        "29": {"min_bets": 1000000, "min_deposit": 100000, "fixed_bonus": 777}
    }
}

mock:
{
    "today": "2018-09-20",
    "getBetsAndDepositByDate": [1000000,100000]
}

 *
 *
 */
class Promo_rule_ole777_bets_deposit_bonus extends Abstract_promo_rule{

	public function init($playerId, $promorule, $playerBonusAmount = null, $depositAmount=null){
		parent::init($playerId, $promorule, $playerBonusAmount, $depositAmount);
	}

	public function getClassName(){
		return 'Promo_rule_ole777_bets_deposit_bonus';
	}

	/**
	 * run bonus condition checker
	 * @param  array $description original description in rule
	 * @param  array $extra_info
	 * @param  boolean $dry_run
	 * @return  array ['success'=> success, 'message'=> errorMessageLang]
	 */
	protected function runBonusConditionChecker($description, &$extra_info, $dry_run){
		$success=false;
		$errorMessageLang=null;


		$today=$this->utils->getTodayForMysql();
		if($this->process_mock('today', $today)){
			//use mock data
			$this->appendToDebugLog('use mock today', ['today'=>$today]);
		}

		$bonus_settings=$description['bonus_settings'];

		//check date
		$allowed_date=$description['allowed_date'];
		$this->appendToDebugLog('check allowed_date', ['allowed_date'=>$allowed_date]);
		$d=new DateTime($today);
		$currentDate=$d->format('Y-m-d');
		$thisMonthFrom=$d->format('Y-m').'-01 '.Utils::FIRST_TIME;
		$thisMonthTo=$d->format('Y-m-t').' '.Utils::LAST_TIME;

		$minDate=$d->format('Y-m-').$allowed_date['start'];
		$maxDate=$d->format('Y-m-').$allowed_date['end'];

		$d=new DateTime($d->format('Y-m').'-01');
		$d->modify('-1 month');
		$fromDate=$d->format('Y-m').'-01 '.Utils::FIRST_TIME;
		$toDate=$d->format('Y-m-t').' '.Utils::LAST_TIME;

		if($currentDate>=$minDate && $currentDate<=$maxDate){
			$availableLevelId=$this->searchAvailableLevelId($bonus_settings, $fromDate, $toDate, $extra_info, $description, $errorMessageLang);
			$success=!empty($availableLevelId);
		}else{
			$errorMessageLang='Not right date';
		}

		$result=['success'=>$success, 'message'=>$errorMessageLang, 'continue_process_after_script' => FALSE];

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

		return $this->returnUnimplemented();
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
		$success=false;
		$errorMessageLang=null;
		$bonus_amount=0;

		$bonus_settings=$description['bonus_settings'];
		$allowed_date=$description['allowed_date'];

        $today=$this->utils->getTodayForMysql();
        if($this->process_mock('today', $today)){
            //use mock data
            $this->appendToDebugLog('use mock today', ['today'=>$today]);
        }

        $d=new DateTime($today);
        $d=new DateTime($d->format('Y-m').'-01');
        $d->modify('-1 month');
        $fromDate=$d->format('Y-m').'-01 '.Utils::FIRST_TIME;
        $toDate=$d->format('Y-m-t').' '.Utils::LAST_TIME;

		$availableLevelId=$this->searchAvailableLevelId($bonus_settings, $fromDate, $toDate, $extra_info, $description, $errorMessageLang);
		$this->appendToDebugLog('calc bonus', ['bonus_settings'=>$bonus_settings,
			'allowed_date'=>$allowed_date, 'availableLevelId'=>$availableLevelId]);
		if(!empty($availableLevelId)){
			if(array_key_exists($availableLevelId, $bonus_settings)){
				$setting=$bonus_settings[$availableLevelId];
				$bonus_amount=$setting['fixed_bonus'];
				$success=true;
				$this->appendToDebugLog('get bonus setting', ['bonus_settings'=>$setting, 'levelId'=>$availableLevelId, 'bonus_amount'=>$bonus_amount]);
			}else{
				$errorMessageLang='Not right group level';
			}
		}

		$result=['success'=>$success, 'message'=>$errorMessageLang, 'bonus_amount'=>$bonus_amount];
		return $result;
	}

	private function searchAvailableLevelId($bonus_settings, $fromDate, $toDate, &$extra_info, $description, &$errorMessageLang){
        $success = false;
	    $availableLevelId=null;
		$endAt=$this->callHelper('getLastMonthEndTime', []);
		$maxLevelId=$this->callHelper('getLastUpgradeLevelOrCurrentLevel',[$endAt]);
		$this->appendToDebugLog('search level id getLastUpgradeLevelOrCurrentLevel', ['endAt'=>$endAt, 'maxLevelId'=>$maxLevelId]);
		$levelIdList=$this->callHelper('getLevelIdListDownToFirstLevel',[$maxLevelId]);
		$this->appendToDebugLog('getLevelIdListDownToFirstLevel',['levelIdList'=>$levelIdList]);
		if(!empty($levelIdList)){
			$existLevel=false;
			foreach ($levelIdList as $levelId) {
				if(array_key_exists($levelId, $bonus_settings)){
					$existLevel=true;
					break;
				}
			}
			if($existLevel){
				//check bets and deposit
				list($bets, $deposit)=$this->callHelper('getBetsAndDepositByDate', [$fromDate, $toDate]);
				//high level to low level
				foreach ($levelIdList as $levelId) {
					if(array_key_exists($levelId, $bonus_settings)){
						$setting=$bonus_settings[$levelId];
						$min_bets=$setting['min_bets'];
						$min_deposit=$setting['min_deposit'];
						$this->appendToDebugLog('check bets and deposit', [
							'playerId'=>$this->playerId, 'fromDate'=>$fromDate, 'toDate'=>$toDate,
							'bets'=>$bets, 'deposit'=>$deposit,
							'min_bets'=>$min_bets, 'min_deposit'=>$min_deposit,
						]);
						if($bets>=$min_bets){
							if($deposit>=$min_deposit){
								$availableLevelId=$levelId;
								$success=true;
								if(isset($description['contact_live_chat_to_apply']) && $description['contact_live_chat_to_apply']){
                                    $button = '<a href="javascript:void(0)" onclick="' . $this->utils->getLiveChatOnClick() . '">' . lang('ole777_promo.contact_live_chat_to_apply') . '</a>';
                                    $extra_info['contact_live_chat_to_apply'] = $button;
                                }
							}else{
                                $errorMessageLang = 'No enough deposit';
                            }
						}else{
                            $errorMessageLang = 'No enough betting amount';
                        }
						if(!$success){
							$this->appendToDebugLog('ignore level because no', [
								'endAt'=>$endAt, 'maxLevelId'=>$maxLevelId, 'levelId'=>$levelId]);
						}else{
                            //clear
                            $errorMessageLang = null;

							//quit
							$this->appendToDebugLog('found success level');
							break;
						}
					}else{
						$this->appendToDebugLog('not exist this level in setting', [
							'levelId'=>$levelId
						]);
					}
				} // foreach ($levelIdList as $levelId)
			}else{ //if($existLevel){
                $errorMessageLang = 'Not right group level';
				$this->appendToDebugLog('never find exist level');
			}
		}else{
            $errorMessageLang = 'Not right group level';
        }

		return $availableLevelId;
	}

}
