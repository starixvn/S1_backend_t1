//===资格判断====================================================================
var MIN_LOSS_AMOUNT = 100;
var MAX_BONUS_TODAY = 3000;
var MAX_BONUS_TRANSACTION = 3000;
var MAX_BALANCE = 5;
var DEFAULT_LEVEL_RATE=10/100;
var LEVEL_MAP = null;

var bonus_amount = 0;
var from_datetime = PHP.runtime.get_date_type('today_start');
var to_datetime = PHP.runtime.get_date_type('today_end');
//负数是输
var deposit_amount = PHP.runtime.sum_deposit_amount(from_datetime, to_datetime, 0);
var withdrawal_amount = PHP.runtime.sum_withdrawal_amount(from_datetime, to_datetime, 0);
var loss_amount=deposit_amount-withdrawal_amount;

//PHP.runtime.refresh_player_balance();
var total_balance = PHP.runtime.current_player_total_balance();
var min_loss_amount = MIN_LOSS_AMOUNT;

var errorMessageLang = null;
var success = true;
var levelId = PHP.runtime.levelId;

PHP.runtime.debug_log(from_datetime + ' to ' + to_datetime + ', loss_amount:' + loss_amount +
	', total_balance:' + total_balance + ', min_loss_amount:' + min_loss_amount);

if (total_balance > MAX_BALANCE) {
	//救援金优惠，不满足最大余额条件
	errorMessageLang = 'promo.nondeposit_rescue_gt_max_balance';
}

if (errorMessageLang === null && loss_amount < min_loss_amount ){
	//救援金优惠，玩家负盈利不满足条件
	errorMessageLang = 'promo.nondeposit_rescue_result_amount';
	success=false;
}

var rate = (LEVEL_MAP!==null && LEVEL_MAP[levelId] ?  LEVEL_MAP[levelId] : DEFAULT_LEVEL_RATE);

if(errorMessageLang === null && (typeof rate == "undefined" || Number.isNaN(rate) || rate===null || rate<=0)){
	//empty, wrong vip level
	errorMessageLang='promo.nondeposit_rescue_wrong_level';
	success=false;
}

PHP.runtime.debug_log('success:' + success + ", message:" + errorMessageLang + ', rate:' + rate);

var result = { 'success': success, 'message': errorMessageLang };
result;


//===奖金发放=================================================================

var MIN_LOSS_AMOUNT = 100;
var MAX_BONUS_TODAY = 3000;
var MAX_BONUS_TRANSACTION = 3000;
var DEFAULT_LEVEL_RATE=10/100;
var LEVEL_MAP = null;

var bonus_amount = 0;
var from_datetime = PHP.runtime.get_date_type('today_start');
var to_datetime = PHP.runtime.get_date_type('today_end');
//负数是输
var deposit_amount = PHP.runtime.sum_deposit_amount(from_datetime, to_datetime, 0);
var withdrawal_amount = PHP.runtime.sum_withdrawal_amount(from_datetime, to_datetime, 0);
var loss_amount=deposit_amount-withdrawal_amount;
var min_loss_amount = MIN_LOSS_AMOUNT;

var errorMessageLang = null;
var levelId = PHP.runtime.levelId;

PHP.runtime.debug_log(from_datetime + ' to ' + to_datetime + ', loss_amount:' + loss_amount);

var rate = (LEVEL_MAP!==null && LEVEL_MAP[levelId] ?  LEVEL_MAP[levelId] : DEFAULT_LEVEL_RATE);
// var deposit_amount = PHP.runtime.sum_deposit_amount(from_datetime, to_datetime, 0);

if (errorMessageLang === null && (loss_amount <min_loss_amount) ){
	//救援金优惠，玩家负盈利不满足条件
	errorMessageLang = 'promo.nondeposit_rescue_result_amount';
	loss_amount=0;
	// success=false;
}

loss_amount = Math.abs(loss_amount);

bonus_amount = loss_amount * rate;

if (bonus_amount > MAX_BONUS_TRANSACTION) {

	bonus_amount = MAX_BONUS_TRANSACTION;

	//救援金优惠，超过每笔最大限制
	// errorMessageLang = 'promo.nondeposit_rescue_invalid_bonus_trans_limit';
}

if (errorMessageLang === null) {
	//check bonus today
	// var today_bonus_amount = PHP.runtime.sum_bonus_amount_today();
	if (bonus_amount > MAX_BONUS_TODAY) {

		bonus_amount = MAX_BONUS_TODAY;
		// if (bonus_amount <= 0) {
		//     bonus_amount = 0;
		// }

		//救援金优惠，超过每日最大限制
		// errorMessageLang = 'promo.nondeposit_rescue_invalid_bonus_daily_limit';
	}
}

PHP.runtime.debug_log('bonus_amount:' + bonus_amount + "," + errorMessageLang + ', rate:' + rate);

var result = { "bonus_amount": bonus_amount, "errorMessageLang": errorMessageLang };
result;


