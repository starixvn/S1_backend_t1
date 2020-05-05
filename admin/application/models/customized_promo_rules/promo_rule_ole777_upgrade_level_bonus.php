<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) .'/abstract_promo_rule.php';

/**
 *
 * 晋级礼金
 *
 * https://manage.tripleonetech.net/jira/browse/OGP-9190
 *
 *
 * 最近90天内第一笔指定的升级

condition:
{
    "class": "promo_rule_ole777_upgrade_level_bonus",
    "last_days": 90,
    "from_group_level_id": 1,
    "to_group_level_id": 29
}

mock:
{
    "now": "2018-09-10 12:00:00",
    "searchFirstGradeRecord": 1
}

 *
 *
 */
class Promo_rule_ole777_upgrade_level_bonus extends Abstract_promo_rule{

	public function init($playerId, $promorule, $playerBonusAmount = null, $depositAmount=null){
		parent::init($playerId, $promorule, $playerBonusAmount, $depositAmount);
	}

	public function getClassName(){
		return 'Promo_rule_ole777_upgrade_level_bonus';
	}

	/**
	 * run bonus condition checker
	 * @param  array $description original description in rule
	 * @param  array $extra_info
	 * @param  boolean $dry_run
	 * @return  array ['success'=> success, 'message'=> errorMessageLang, 'continue_process_after_script' => FALSE]
	 */
	protected function runBonusConditionChecker($description, &$extra_info, $dry_run){
		// $success=true;
		$errorMessageLang=null;

		$now=$this->utils->getTodayForMysql();
		if($this->process_mock('now', $now)){
			//use mock data
			$this->appendToDebugLog('use mock now', ['now'=>$now]);
		}
		$this->load->model(['group_level']);

		$last_days=$description['last_days'];
		$fromLevelId=$description['from_group_level_id'];
		$toLevelId=$description['to_group_level_id'];

		$fromDate=new DateTime($now);
		$fromDate->modify('-'.$last_days.' days ');
		$fromDate=$this->utils->formatDateForMysql($fromDate).' '.Utils::FIRST_TIME;

		$toDate=$now;

		$this->appendToDebugLog('check first grade record', ['fromDate'=>$fromDate, 'toDate'=>$toDate, 'fromLevelId'=>$fromLevelId, 'toLevelId'=>$toLevelId]);

		$recordId=$this->callHelper('searchFirstGradeRecord', [Group_level::RECORD_UPGRADE, $fromDate, $toDate, $fromLevelId, $toLevelId]);
		$success=!empty($recordId);
		if(!$success){
			$errorMessageLang='Not found upgrade level record';
		}

		$this->appendToDebugLog('check first grade record result', ['success'=>$success, 'recordId'=>$recordId]);

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

		return $this->returnUnimplemented();
	}
}
