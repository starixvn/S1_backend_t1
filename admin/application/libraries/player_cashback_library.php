<?php
require_once __DIR__ . '/cashback/common_cashback_multiple_rules.php';

/**
 * player_cashback_library.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 *
 * @property BaseController $CI
 */
class Player_cashback_library {
    // cashback mode
    const COMMON_CASHBACK_RULES_MODE_BY_SINGLE = 0;
    const COMMON_CASHBACK_RULES_MODE_BY_MULTIPLE_RANGE = 1;

    /* @var BaseController */
    public $CI;

    /* @var Group_level */
    public $group_level;

    /* @var Common_Cashback_multiple_rules */
    public $common_cashback_multiple_rules;

    public function __construct(){
        $this->CI =& get_instance();

        $this->CI->load->model(['group_level', 'game_type_model', 'game_description_model', 'common_cashback_multiple_range_templates_model', 'common_cashback_multiple_range_settings_model', 'common_cashback_multiple_range_rules_model']);

        $this->group_level = $this->CI->group_level;

        $this->common_cashback_multiple_rules = new Common_Cashback_multiple_rules();
    }

    public function calculateDailyTotalCashbackBySettings($cashBackSettings, $date, $playerId = null){
        $startHour = $cashBackSettings->fromHour;
        $endHour = $cashBackSettings->toHour;
        $withdraw_condition_bet_times = isset($cashBackSettings->withdraw_condition) ? $cashBackSettings->withdraw_condition : 0;

        switch((int)$cashBackSettings->common_cashback_rules_mode){
            case static::COMMON_CASHBACK_RULES_MODE_BY_MULTIPLE_RANGE:
                $calcResult = $this->common_cashback_multiple_rules->calculateTotalCashback($date, $startHour, $endHour, $playerId, $withdraw_condition_bet_times);
                break;
            case static::COMMON_CASHBACK_RULES_MODE_BY_SINGLE:
            default:
                $calcResult = $this->group_level->totalCashback($date, $startHour, $endHour, $playerId, $withdraw_condition_bet_times);
                break;
        }
        return $calcResult;
    }

    public function calculateWeeklyTotalCashbackBySettings($cashBackSettings, $date, $playerId = null){
        if(!$this->CI->utils->isEnabledFeature('enabled_weekly_cashback')){
            return;
        }

        $startHour = $cashBackSettings->fromHour;
        $endHour = $cashBackSettings->toHour;
        $withdraw_condition_bet_times = isset($cashBackSettings->withdraw_condition) ? $cashBackSettings->withdraw_condition : 0;
        $weekly = $cashBackSettings->weekly;

        $startDate = date("Y-m-d", strtotime("last week monday"));
        $endDate = date("Y-m-d", strtotime("last week sunday"));
        $result = false;

        // if found today is the day in cashback setting then calculate last week cash back
        $cashback_day = $this->group_level->getDayCashbackSetting($weekly);
        $current_day = date('l', strtotime($date));

        if ($cashback_day != $current_day) {
            return;
        }

        switch((int)$cashBackSettings->common_cashback_rules_mode){
            case static::COMMON_CASHBACK_RULES_MODE_BY_MULTIPLE_RANGE:
                $calcResult = $this->common_cashback_multiple_rules->calculateTotalCashback($date, $startHour, $endHour, $playerId, $withdraw_condition_bet_times, $result, $startDate, $endDate);
                break;
            case static::COMMON_CASHBACK_RULES_MODE_BY_SINGLE:
            default:
                $calcResult = $this->group_level->totalCashback($date, $startHour, $endHour, $playerId, $withdraw_condition_bet_times, $result, $startDate, $endDate);
                break;
        }
        return $calcResult;
    }

    public function manuallyCalculateTotalCashback($date, $playerId = null){
        $cashBackSettings = $this->group_level->getCashbackSettings();

        $startHour = $cashBackSettings->fromHour;
        $endHour = $cashBackSettings->toHour;
        $withdraw_condition_bet_times = isset($cashBackSettings->withdraw_condition) ? $cashBackSettings->withdraw_condition : 0;

        switch((int)$cashBackSettings->common_cashback_rules_mode){
            case static::COMMON_CASHBACK_RULES_MODE_BY_MULTIPLE_RANGE:
                $calcResult = $this->common_cashback_multiple_rules->calculateTotalCashback($date, $startHour, $endHour, $playerId, $withdraw_condition_bet_times);
                break;
            case static::COMMON_CASHBACK_RULES_MODE_BY_SINGLE:
            default:
                $calcResult = $this->group_level->totalCashback($date, $startHour, $endHour, $playerId, $withdraw_condition_bet_times);
                break;
        }
        return $calcResult;
    }
}