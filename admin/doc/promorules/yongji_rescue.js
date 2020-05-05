//===yongji救援金 奖金条件================================================
//48小时内的 存-取-现在余额 > 100, balance <=5

var MIN_LOSS_AMOUNT = 100;
// var MAX_BONUS_TODAY = 3000;
// var MAX_BONUS_TRANSACTION = 3000;
var MAX_BALANCE = 5;
var DEFAULT_BONUS=0;
var RESULT_BONUS_MAP = [
    {'result_amount': 100000, 'bonus': 5008}, // >=100000
    {'result_amount': 10000, 'bonus': 508}, // >=10000
    {'result_amount': 1000, 'bonus': 50}, // >=1000
    {'result_amount': 100, 'bonus': 5} // >=100
];

var bonus_amount = 0;
//最后一次救援金或48小时
var from_datetime = PHP.runtime.get_max_date_type(['-48 hours', 'last_release_bonus_time']);
var to_datetime = PHP.runtime.get_date_type('now');
//负数是输
var deposit_amount = PHP.runtime.sum_deposit_amount(from_datetime, to_datetime, 0);
var withdrawal_amount = PHP.runtime.sum_withdrawal_amount(from_datetime, to_datetime, 0);
var loss_amount=deposit_amount-withdrawal_amount;

//PHP.runtime.refresh_player_balance();
var total_balance = PHP.runtime.current_player_total_balance();

loss_amount=loss_amount-total_balance;

var result_amount= PHP.runtime.get_game_result_amount(from_datetime, to_datetime);

var min_loss_amount = MIN_LOSS_AMOUNT;

var errorMessageLang = null;
var success = true;
// var levelId = PHP.runtime.levelId;

PHP.runtime.debug_log(from_datetime + ' to ' + to_datetime + ', loss_amount:' + loss_amount +
    ', total_balance:' + total_balance + ', min_loss_amount:' + min_loss_amount+', result_amount:'+result_amount);

if (success && total_balance > MAX_BALANCE) {
    //救援金优惠，不满足最大余额条件
    errorMessageLang = 'promo.nondeposit_rescue_gt_max_balance';
    success=false;
}else{
    PHP.runtime.debug_log("validate: total_balance > MAX_BALANCE");
}

if (success && (loss_amount <= 0 || loss_amount < min_loss_amount )){
    //救援金优惠， 存-取-余额 不满足条件
    errorMessageLang = 'promo.nondeposit_rescue_result_amount';
    success=false;
}else{
    PHP.runtime.debug_log("validate: (loss_amount <= 0 || loss_amount < min_loss_amount )");
}

if (success && (result_amount >= 0 || 0.9*loss_amount>Math.abs(result_amount) ) ){
    //救援金优惠，游戏记录 不满足条件
    errorMessageLang = 'promo.nondeposit_rescue_result_amount';
    success=false;
}else{
    PHP.runtime.debug_log("validate: (result_amount >= 0 || loss_amount>Math.abs(result_amount) )");
}

var bonus=DEFAULT_BONUS;

if(success){
    for (var i = 0; i < RESULT_BONUS_MAP.length; i++) {
        var m=RESULT_BONUS_MAP[i];
        if(loss_amount>=m['result_amount']){
            bonus=m['bonus'];
            break;
        }
    }

    if(bonus<=0){
        //empty, wrong vip level
        errorMessageLang='promo.nondeposit_rescue_result_amount';
        success=false;
    }else{
        PHP.runtime.debug_log("validate: bonus<=0");
    }
}

PHP.runtime.debug_log('success:' + success + ", message:" + errorMessageLang + ', bonus:' + bonus);

var result = { 'success': success, 'message': errorMessageLang };
result;

//===yongji救援金 bonus==========================================================
//分等级

var MIN_LOSS_AMOUNT = 100;
// var MAX_BONUS_TODAY = 3000;
// var MAX_BONUS_TRANSACTION = 3000;
var MAX_BALANCE = 5;
var DEFAULT_BONUS=0;
var RESULT_BONUS_MAP = [
    {'result_amount': 100000, 'bonus': 5008}, // >=100000
    {'result_amount': 10000, 'bonus': 508}, // >=10000
    {'result_amount': 1000, 'bonus': 50}, // >=1000
    {'result_amount': 100, 'bonus': 5} // >=100
];

var bonus_amount = 0;
var from_datetime = PHP.runtime.get_max_date_type(['-48 hours', 'last_release_bonus_time']);
var to_datetime = PHP.runtime.get_date_type('now');
//负数是输
var deposit_amount = PHP.runtime.sum_deposit_amount(from_datetime, to_datetime, 0);
var withdrawal_amount = PHP.runtime.sum_withdrawal_amount(from_datetime, to_datetime, 0);
var loss_amount=deposit_amount-withdrawal_amount;

//PHP.runtime.refresh_player_balance();
var total_balance = PHP.runtime.current_player_total_balance();

loss_amount=loss_amount-total_balance;

var result_amount= PHP.runtime.get_game_result_amount(from_datetime, to_datetime);

var min_loss_amount = MIN_LOSS_AMOUNT;

var errorMessageLang = null;
var success = true;

PHP.runtime.debug_log(from_datetime + ' to ' + to_datetime + ', loss_amount:' + loss_amount +
    ', total_balance:' + total_balance + ', min_loss_amount:' + min_loss_amount+', result_amount:'+result_amount);

if (success && total_balance > MAX_BALANCE) {
    //救援金优惠，不满足最大余额条件
    errorMessageLang = 'promo.nondeposit_rescue_gt_max_balance';
    success=false;
}else{
    PHP.runtime.debug_log("validate: total_balance > MAX_BALANCE");
}

if (success && (loss_amount <= 0 || loss_amount < min_loss_amount )){
    //救援金优惠， 存-取-余额 不满足条件
    errorMessageLang = 'promo.nondeposit_rescue_result_amount';
    success=false;
}else{
    PHP.runtime.debug_log("validate: (loss_amount <= 0 || loss_amount < min_loss_amount )");
}

if (success && (result_amount >= 0 || loss_amount>Math.abs(result_amount) ) ){
    //救援金优惠，游戏记录 不满足条件
    errorMessageLang = 'promo.nondeposit_rescue_result_amount';
    success=false;
}else{
    PHP.runtime.debug_log("validate: (result_amount >= 0 || loss_amount>Math.abs(result_amount) )");
}

var bonus=DEFAULT_BONUS;

if(success){
    for (var i = 0; i < RESULT_BONUS_MAP.length; i++) {
        var m=RESULT_BONUS_MAP[i];
        if(loss_amount>=m['result_amount']){
            bonus=m['bonus'];
            break;
        }
    }

    if(bonus<=0){
        //empty, wrong vip level
        errorMessageLang='promo.nondeposit_rescue_result_amount';
        success=false;
    }else{
        PHP.runtime.debug_log("validate: bonus<=0");
    }
}

PHP.runtime.debug_log('bonus_amount:' + bonus + "," + errorMessageLang);

var result = { "bonus_amount": bonus, "errorMessageLang": errorMessageLang };
result;
