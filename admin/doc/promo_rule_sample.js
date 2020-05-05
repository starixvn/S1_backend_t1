//每日首存2存3存存款===================
PHP.runtime.debug_log('start bonus condition, daily 1/2/3 deposit');

var success=false;
var message='对不起，不符合优惠条件';
var tran_id=null;

var min=PHP.runtime.get_min_deposit_condition();
var max=PHP.runtime.get_max_deposit_condition();
transRow=PHP.runtime.get_last_deposit_by('daily');
PHP.runtime.debug_log(transRow);
if(transRow['amount']>=min && transRow['amount']<=max && transRow['transOrder']<=3 && transRow['player_promo_id']==''){
	if(transRow['transOrder']=='1'){
		success=true;

	}
}
//success= !PHP.runtime.reached_limit_promotion('daily', 3) && tran_id!=null;
message= success ? null : "对不起，没有有效的当日存款，无法申请优惠";
PHP.runtime.debug_log('min:'+min+', max:'+max+', daily tran_id:'+tran_id+' message:'+message);


$result={ "success": success , "message": message,"deposit_tran_id":tran_id};
$result;


//无条件每日送
PHP.runtime.init('无条件每日送');
var success=false;
var message='对不起，不符合优惠条件';

success= !PHP.runtime.reached_limit_promotion('daily', 1);
if($success){
	message='';
}

var result={"success":success,"message":message};
PHP.runtime.debug_log(result);

result;


//检查注册ip，不允许重复
//每日任何一次存款===================
PHP.runtime.debug_log('start bonus condition, daily any deposit');

var success=false;
var message='对不起，不符合优惠条件';
var tran_id=null;

if(!PHP.runtime.exists_double_ip('registration')){

var min=PHP.runtime.get_min_deposit_condition();
var max=PHP.runtime.get_max_deposit_condition();
tran_id=PHP.runtime.get_available_deposit_info('daily', -1, min,max);
success= !PHP.runtime.reached_limit_promotion('daily', 1) && tran_id!=null;
message= success ? null : "对不起，没有有效的当日存款，无法申请优惠";
PHP.runtime.debug_log('min:'+min+', max:'+max+', daily tran_id:'+tran_id+' message:'+message);

}

$result={ "success": success , "message": message,"deposit_tran_id":tran_id};
$result;



//仅存款10倍
var playerBonusAmount=PHP.runtime.playerBonusAmount;
var depositAmount=PHP.runtime.depositAmount;

PHP.runtime.debug_log('playerBonusAmount:'+playerBonusAmount+", depositAmount:"+depositAmount);

withdraw_amount=depositAmount*10;

withdraw_amount;



//本月流水超过188888
PHP.runtime.init('check betting amount');
var success=false;
var message='对不起，不符合优惠条件';

var this_month=PHP.runtime.get_from_to_date_by_type('this_month');
var fromDate=this_month['from'];
var toDate=this_month['to'];

var betting_amount=PHP.runtime.get_game_betting_amount(fromDate, toDate);

PHP.runtime.debug_log('fromDate:'+fromDate+', toDate:'+toDate+', betting_amount:'+betting_amount);

success=betting_amount>=18888;
if(success){
	message=null;
}else{
    message="对不起，流水要求没有达到";
}

var result={"success":success,"message":message};
PHP.runtime.debug_log(result);

result;



//六档随机奖金
PHP.runtime.debug_log('start bonus release');

function randomInProbability( weights ){
  if( arguments.length > 1 ){
    weights = [].slice.call( arguments );
  }

  var total, current = 0, parts = [],
      i = 0, l = weights.length;

  // reduce 方法的简单兼容
  total = weights.reduce ? weights.reduce( function( a, b ){
    return a + b;
  } ) : eval( weights.join( '+' ) );

  for( ; i < l; i ++ ){
    current += weights[ i ];
    parts.push( 'if( p < ', current / total, ' ) return ', i / l, ' + n;' );
  }

  return Function( 'var p = Math.random(), n = Math.random() / ' + l + ';' + parts.join( '' ) );
}

var all_bonus=[2,4,8,16];
var updateRandom = randomInProbability( 0.9 , 0.06, 0.03, 0.01  );

var randomIndex = Math.floor( all_bonus.length * updateRandom() );
var bonus_amount=all_bonus[randomIndex];
var errorMessageLang=null;

PHP.runtime.debug_log('bonus_amount:'+bonus_amount+', errorMessageLang:'+errorMessageLang);

var result={"bonus_amount":bonus_amount,"errorMessageLang":errorMessageLang};
result;



//检查注册ip，不允许重复
PHP.runtime.init('check double ip');
var success=false;
var message='对不起，不符合优惠条件';

if(!PHP.runtime.exists_double_ip('registration')){
	success=true;
	message='';
}

var result={"success":success,"message":message};
PHP.runtime.debug_log(result);

result;



//首次存款后没有转账或取款
PHP.runtime.debug_log('start bonus condition, first deposit');

var min=PHP.runtime.promorule['nonfixedDepositMinAmount'];
var max=PHP.runtime.promorule['nonfixedDepositMaxAmount'];
var tran_id=PHP.runtime.get_deposit_by('daily',1,min,max);

var success= tran_id!=null;

var message= success ? null : "对不起，没有有效的首存，无法申请优惠";
PHP.runtime.debug_log('tran_id:'+tran_id+' message:'+message); //+' min:'+min+" max:"+max);

$result={ "success": success , "message": message,"deposit_tran_id":tran_id};
$result;


//一天一次，昨天存-昨天取-今天0点余额>10
PHP.runtime.debug_log('start bonus release');
var yesterday=PHP.runtime.get_yesterday();
var today=PHP.runtime.get_today();

var loss_amount=PHP.runtime.get_loss_by_date(yesterday);
var cnt_promo=PHP.runtime.count_approved_promo_by_date(today);

var min=10;
var success=false;
var message='对不起，不符合优惠条件';

if( loss_amount>min && cnt_promo<1){
    success=true;
    message=null;
}

PHP.runtime.debug_log('yesterday:'+yesterday+', loss_amount:'+loss_amount);

var result={"success":success,"message":message};
result;

//奖金发放：（昨天存-昨天取-今天0点余额）* 10% 上限8888
PHP.runtime.debug_log('start bonus release');
var yesterday=PHP.runtime.get_yesterday();
var loss_amount=PHP.runtime.get_loss_by_date(yesterday);

var bonus_amount=loss_amount*0.1;
var max_bonus=8888;
var errorMessageLang=null;

if(bonus_amount>max_bonus){
	bonus_amount=max_bonus;
}
if(bonus_amount<=0){
	errorMessageLang="对不起，不符合优惠条件";
}

PHP.runtime.debug_log('yesterday:'+yesterday+', loss_amount:'+loss_amount);

var result={"bonus_amount":bonus_amount,"errorMessageLang":errorMessageLang};
result;

//奖金发放：存款 * 30% 上限 388/588/888/1288/1588/1888
PHP.runtime.debug_log('start bonus release');
var depositAmount=PHP.runtime.depositAmount;
//from 1
var levelName=PHP.runtime.levelName;
var vipGroupName=PHP.runtime.vipGroupName;
//30%
var bonus_amount=depositAmount*0.3;
var max_bonus= 388;
if(vipGroupName=='VIP'){

	if(levelName=='青铜会员'){
		max_bonus= 388;
	}else if(levelName=='白银VIP'){
		max_bonus= 588;
	}else if(levelName=='黄金VIP'){
		max_bonus= 888;
	}else if(levelName=='铂金VIP'){
		max_bonus= 1288;
	}else if(levelName=='钻石VIP'){
		max_bonus= 1588;
	}else if(levelName=='至尊VIP'){
		max_bonus= 1888;
	}

}else if(vipGroupName=='特邀VIP'){
	max_bonus= 1888;
}

var errorMessageLang=null;

if(bonus_amount>max_bonus){
	bonus_amount=max_bonus;
}
if(bonus_amount<=0){
	errorMessageLang="对不起，不符合优惠条件";
}

PHP.runtime.debug_log('levelName:'+levelName+', vipGroupName:'+vipGroupName+', depositAmount:'+depositAmount+', bonus_amount:'+bonus_amount);

var result={"bonus_amount":bonus_amount,"errorMessageLang":errorMessageLang};
result;


//每日第一次存款===================
PHP.runtime.debug_log('start bonus condition, daily first deposit');

var min=PHP.runtime.get_min_deposit_condition();
var max=PHP.runtime.get_max_deposit_condition();
var tran_id=PHP.runtime.get_available_deposit_info('daily',1, min,max);
var success= tran_id!=null;
var message= success ? null : "对不起，没有有效的当日首存，无法申请优惠";
PHP.runtime.debug_log('daily tran_id:'+tran_id+' message:'+message); //+' min:'+min+" max:"+max);

$result={ "success": success , "message": message,"deposit_tran_id":tran_id};
$result;

//resuce===============================================
//昨天有存款，并且昨天的余额 < 5
PHP.runtime.debug_log('start bonus condition');

var yesterday=PHP.runtime.get_yesterday();
var min=5;
var exists_deposit=PHP.runtime.exists_any_deposit(yesterday);
var yesterday_balance=PHP.runtime.last_day_balance(yesterday);

PHP.runtime.debug_log('yesterday:'+yesterday+', exists_deposit:'+exists_deposit+', yesterday_balance:'+yesterday_balance);

var success=true;
var message=null;
if(!exists_deposit){
	message='对不起，昨天没有存款，不符合优惠条件';
	success=false;
}
if(yesterday_balance>min){
	message='对不起，昨天的总余额 > '+min+'，不符合优惠条件';
	success=false;
}
var result={ "success": success , "message": message};
result;

//奖金发放：（昨天的存款-昨天的取款）* 10%
PHP.runtime.debug_log('start bonus release');
var yesterday=PHP.runtime.get_yesterday();
var sum_withdraw=PHP.runtime.sum_withdraw_by_date(yesterday);
var sum_deposit=PHP.runtime.sum_deposit_by_date(yesterday);
var bonus_amount=(sum_deposit-sum_withdraw)*0.1;
var max_bonus=3000;
var errorMessageLang=null;
if(bonus_amount<=0){
	errorMessageLang="对不起，不符合优惠条件";
}
if(bonus_amount>max_bonus){
	bonus_amount=max_bonus;
}

PHP.runtime.debug_log('yesterday:'+yesterday+', sum_withdraw:'+sum_withdraw+', sum_deposit:'+sum_deposit);

var result={"bonus_amount":bonus_amount,"errorMessageLang":errorMessageLang};
result;




//resuce===============================================
var bonus_amount=0;
var from_datetime=PHP.runtime.get_from_datetime(['last_withdraw','last_same_promo','player_reg_date']);
var to_datetime=PHP.runtime.get_to_datetime(['now']);
var result_amount=PHP.runtime.get_game_result_amount(from_datetime,to_datetime);
var total_balance=PHP.runtime.current_player_total_balance();

var max_bonus_today=3000;
var min_balance=PHP.runtime.getConfig('rescue_promotion_amount'); //5
var errorMessageLang=null;

PHP.runtime.debug_log(from_datetime+' to '+to_datetime+', result_amount:'+result_amount+', total_balance:'+total_balance);

if(total_balance<min_balance && result_amount<0){
	// result_amount=Math.abs(result_amount);

	var deposit_amount=PHP.runtime.sum_deposit_amount(from_datetime,to_datetime,200);

PHP.runtime.debug_log('deposit_amount:'+deposit_amount);

	var rate=0;
	if(deposit_amount>=200 && deposit_amount<1000){
		rate=0.1;
	}else if(deposit_amount>=1000 && deposit_amount<5000){
		rate=0.11;
	}else if(deposit_amount>=5000){
		rate=0.12;
	}

PHP.runtime.debug_log('rate:'+rate);

	if(rate>0){
		bonus_amount=deposit_amount*rate;

		var today_bonus_amount=PHP.runtime.sum_bonus_amount_today();
		if(today_bonus_amount+bonus_amount>max_bonus_today){
			bonus_amount=max_bonus_today-today_bonus_amount;
			if(bonus_amount<=0){
				bonus_amount=0;
			}
		}

	}
}else{
	if(total_balance>=min_balance){
		errorMessageLang='promo.nondeposit_rescue_info';
	}
	if(result_amount>=0){
		errorMessageLang='promo.nondeposit_rescue_result_amount';
	}
}

PHP.runtime.debug_log('bonus_amount:'+bonus_amount+","+errorMessageLang);

var result={"bonus_amount":bonus_amount,"errorMessageLang":errorMessageLang};
result;

//recuse withdraw====================================
var withdraw_amount=0;
var playerBonusAmount=PHP.runtime.playerBonusAmount;
if(playerBonusAmount>=20){
	withdraw_amount=playerBonusAmount*3;
}else if(playerBonusAmount>=10){
	withdraw_amount=playerBonusAmount*2;
}else{
	withdraw_amount=playerBonusAmount;
}

PHP.runtime.debug_log('withdraw_amount:'+withdraw_amount+", playerBonusAmount:"+playerBonusAmount);

withdraw_amount;


//first deposit========================================================
var bonus_amount=0;
var depositAmount=PHP.runtime.depositAmount;
var max_bonus_amount=8888;

PHP.runtime.debug_log('max_bonus_amount:'+max_bonus_amount+", depositAmount:"+depositAmount);

if(depositAmount>=100 && depositAmount<2000){
	bonus_amount=depositAmount*1;
}else if(depositAmount>=2000){
	bonus_amount=depositAmount*1;
}
if(bonus_amount>max_bonus_amount){
	bonus_amoun=max_bonus_amount;
}
var result={"bonus_amount":bonus_amount,"errorMessageLang":errorMessageLang};
result;

//withdraw condition==================================================
var withdraw_amount=0;
var playerBonusAmount=PHP.runtime.playerBonusAmount;
var depositAmount=PHP.runtime.depositAmount;

PHP.runtime.debug_log('playerBonusAmount:'+playerBonusAmount+", depositAmount:"+depositAmount);

if(playerBonusAmount>=100 && playerBonusAmount<2000){
	withdraw_amount=(playerBonusAmount+depositAmount)*18;
}else if(playerBonusAmount>=2000){
	withdraw_amount=(playerBonusAmount+depositAmount)*25;
}

withdraw_amount;


//===救援金==================================
//活动期间，元宝平台开启超值救援金活动，只要负盈利200元或以上，天天皆可申请
//昨天0点到今天0点，负盈利>=200，现在余额小于1，昨天的负盈利>=存款金额
//每天第2次或以上次数申请时，负盈利达到50元以上，即可申请

var FIRST_MAX_GAME_AMOUNT = -200;
var SECOND_MAX_GAME_AMOUNT = -50;
var MAX_BONUS_TODAY = 5000;
var MAX_BONUS_TRANSACTION = 5000;
var MAX_BALANCE = 1;
var DEFAULT_LEVEL_RATE=8/100;
var LEVEL_MAP = {
    '10': 8 / 100, //青铜VIP1
    '11': 10 / 100, //白银VIP2
    '12': 10 / 100, //黄金VIP
    '13': 11 / 100, //铂金VIP
    '14': 11 / 100, //钻石VIP
};

var bonus_amount = 0;
var from_datetime = PHP.runtime.get_date_type('yesterday_start');
var to_datetime = PHP.runtime.get_date_type('yesterday_end');
var result_amount = PHP.runtime.get_game_result_amount(from_datetime, to_datetime);
//PHP.runtime.refresh_player_balance();
var total_balance = PHP.runtime.current_player_total_balance();
var times_released = PHP.runtime.times_released_bonus_on_this_promo_today();
var max_game_result_amount = FIRST_MAX_GAME_AMOUNT;

if (times_released > 1) {
    max_game_result_amount = SECOND_MAX_GAME_AMOUNT;
}

var errorMessageLang = null;
var success = false;
var levelId = PHP.runtime.levelId;

PHP.runtime.debug_log(from_datetime + ' to ' + to_datetime + ', result_amount:' + result_amount +
    ', total_balance:' + total_balance + ', times_released:' + times_released + ', max_game_result_amount:' + max_game_result_amount);

//昨天0点到今天0点，负盈利>=200，现在余额小于1，昨天的负盈利>=存款金额

if (total_balance > MAX_BALANCE) {
    //救援金优惠，不满足最大余额条件
    errorMessageLang = 'promo.nondeposit_rescue_gt_max_balance';
}

if (errorMessageLang === null && result_amount > max_game_result_amount) {
    //救援金优惠，玩家负盈利不满足条件
    errorMessageLang = 'promo.nondeposit_rescue_result_amount';
}

var rate = LEVEL_MAP[levelId] ?  LEVEL_MAP[levelId] : DEFAULT_LEVEL_RATE;

if(errorMessageLang === null && (typeof rate == "undefined" || Number.isNaN(rate) || rate===null || rate<=0)){
    //empty, wrong vip level
    errorMessageLang='promo.nondeposit_rescue_wrong_level';
}

if (errorMessageLang === null) {
    result_amount = Math.abs(result_amount);
    var deposit_amount = PHP.runtime.sum_deposit_amount(from_datetime, to_datetime, 0);

    if (result_amount >= deposit_amount) {
        success = true;
    } else {
        //救援金优惠，玩家负盈利不满足条件
        errorMessageLang = 'promo.nondeposit_rescue_result_amount';
    }

}

PHP.runtime.debug_log('success:' + success + ", message:" + errorMessageLang + ', rate:' + rate);

var result = { 'success': success, 'message': errorMessageLang };
result;



//===救援金==================================
//活动期间，元宝平台开启超值救援金活动，只要负盈利200元或以上，天天皆可申请
//昨天0点到今天0点，负盈利>=200，现在余额小于1，昨天的负盈利>=存款金额
//每天第2次或以上次数申请时，负盈利达到50元以上，即可申请

var MAX_BONUS_TODAY = 5000;
var MAX_BONUS_TRANSACTION = 5000;
var DEFAULT_LEVEL_RATE=8/100;
var LEVEL_MAP = {
    '10': 8 / 100, //青铜VIP1
    '11': 10 / 100, //白银VIP2
    '12': 10 / 100, //黄金VIP
    '13': 11 / 100, //铂金VIP
    '14': 11 / 100, //钻石VIP
};

var bonus_amount = 0;
var from_datetime = PHP.runtime.get_date_type('yesterday_start');
var to_datetime = PHP.runtime.get_date_type('yesterday_end');
var result_amount = PHP.runtime.get_game_result_amount(from_datetime, to_datetime);

var errorMessageLang = null;
var levelId = PHP.runtime.levelId;

PHP.runtime.debug_log(from_datetime + ' to ' + to_datetime + ', result_amount:' + result_amount);

var rate = LEVEL_MAP[levelId] ?  LEVEL_MAP[levelId] : DEFAULT_LEVEL_RATE;
var deposit_amount = PHP.runtime.sum_deposit_amount(from_datetime, to_datetime, 0);
result_amount = Math.abs(result_amount);

if (result_amount >= deposit_amount) {

    bonus_amount = deposit_amount * rate;

    if (bonus_amount > MAX_BONUS_TRANSACTION) {
        //救援金优惠，超过每笔最大限制
        errorMessageLang = 'promo.nondeposit_rescue_invalid_bonus_trans_limit';
    }

    if (errorMessageLang === null) {
        //check bonus today
        var today_bonus_amount = PHP.runtime.sum_bonus_amount_today();
        if (today_bonus_amount + bonus_amount > MAX_BONUS_TODAY) {

            bonus_amount = MAX_BONUS_TODAY - today_bonus_amount;
            if (bonus_amount <= 0) {
                bonus_amount = 0;
            }

            //救援金优惠，超过每日最大限制
            errorMessageLang = 'promo.nondeposit_rescue_invalid_bonus_daily_limit';
        }
    }

} else {
    //救援金优惠，玩家负盈利不满足条件
    errorMessageLang = 'promo.nondeposit_rescue_result_amount';
}

PHP.runtime.debug_log('bonus_amount:' + bonus_amount + "," + errorMessageLang + ', rate:' + rate);

var result = { "bonus_amount": bonus_amount, "errorMessageLang": errorMessageLang };
result;


//===youhu救援金 奖金条件==================================
//并且以当日总存款额扣除当日总提款额为大于等于100元负盈利的情况下才算符合申请条件。
//负盈利=取-存
//昨天0点到今天0点，负盈利绝对值>=100，现在余额小于5，昨天的result<0

var MIN_LOSS_AMOUNT = 100;
var MAX_BONUS_TODAY = 3000;
var MAX_BONUS_TRANSACTION = 3000;
var MAX_BALANCE = 5;
var DEFAULT_LEVEL_RATE=10/100;
var LEVEL_MAP = null;

var bonus_amount = 0;
var from_datetime = PHP.runtime.get_date_type('yesterday_start');
var to_datetime = PHP.runtime.get_date_type('yesterday_end');
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

//===youhu 救援金==================================
//负盈利*10%

var MIN_LOSS_AMOUNT = 100;
var MAX_BONUS_TODAY = 3000;
var MAX_BONUS_TRANSACTION = 3000;
var DEFAULT_LEVEL_RATE=10/100;
var LEVEL_MAP = null;

var bonus_amount = 0;
var from_datetime = PHP.runtime.get_date_type('yesterday_start');
var to_datetime = PHP.runtime.get_date_type('yesterday_end');
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


//===dlcity救援金 奖金条件==================================
//存款次数>=2 累积存款>=100 并且以当日总存款额扣除当日总提款额为>=100元负盈利的情况下才算符合申请条件。
//负盈利=取-存
//昨天0点到今天0点，负盈利绝对值>=100，现在余额<=3
//奖金上限1888
var MIN_LOSS_AMOUNT = 100;
var MAX_BONUS_TODAY = 1888;
var MAX_BONUS_TRANSACTION = 1888;
var MAX_BALANCE = 3;
var DEFAULT_LEVEL_RATE=10/100;
var LEVEL_MAP = null;
var MIN_COUNT_DEPOSIT=2;

var bonus_amount = 0;
var from_datetime = PHP.runtime.get_date_type('today_start');
var to_datetime = PHP.runtime.get_date_type('today_end');
//负数是输
var deposit_amount = PHP.runtime.sum_deposit_amount(from_datetime, to_datetime, 0);
var withdrawal_amount = PHP.runtime.sum_withdrawal_amount(from_datetime, to_datetime, 0);
var loss_amount=deposit_amount-withdrawal_amount;
//存款次数>=2
var count_deposit=PHP.runtime.count_deposit_by_date(from_datetime, to_datetime);


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

if (errorMessageLang === null && count_deposit < MIN_COUNT_DEPOSIT ){
	//存款次数<2 不满足
    errorMessageLang = 'promo.nondeposit_rescue_count_deposit';
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

//===dlcity 救援金==================================
//存款次数>=1 累积存款>=100 并且以当日总存款额扣除当日总提款额为>=100元负盈利的情况下才算符合申请条件。
//负盈利=取-存
//负盈利*10%
//昨天0点到今天0点，负盈利绝对值>=100，现在余额<=3
//奖金上限 1888

var MIN_LOSS_AMOUNT = 100;
var MAX_BONUS_TODAY = 1888;
var MAX_BONUS_TRANSACTION = 1888;
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

//===dlcity 天天打卡 奖金条件==================================
//累积存款>=5 投注额>=100元
//3倍流水
var MIN_DEPOSIT_AMOUNT = 5;
var MIN_BET_AMOUNT = 100;

var bonus_amount = 0;
var from_datetime = PHP.runtime.get_date_type('today_start');
var to_datetime = PHP.runtime.get_date_type('today_end');
var deposit_amount = PHP.runtime.sum_deposit_amount(from_datetime, to_datetime, 0);
//投注额
var betting_amount = PHP.runtime.get_game_betting_amount(from_datetime, to_datetime);

var errorMessageLang = null;
var success = true;
var levelId = PHP.runtime.levelId;

PHP.runtime.debug_log(from_datetime + ' to ' + to_datetime + ', betting_amount:' + betting_amount);

if (errorMessageLang === null && deposit_amount < MIN_DEPOSIT_AMOUNT){
    //存款金额<5
    errorMessageLang = 'promo.nondeposit_not_suit_deposit_amount';
    success=false;
}

if (errorMessageLang === null && betting_amount < MIN_BET_AMOUNT){
    //投注额<100
    errorMessageLang = 'promo.nondeposit_not_suit_betting_amount';
    success=false;
}

PHP.runtime.debug_log('success:' + success + ", message:" + errorMessageLang);

var result = { 'success': success, 'message': errorMessageLang };
result;

//===dlcity 天天打卡 发奖金==================================
//累积存款>=5 投注额>=100元
//3倍流水

var MIN_DEPOSIT_AMOUNT = 5;
var MIN_BET_AMOUNT = 100;
var FIXED_BONUS=5;

var bonus_amount = 0;
var from_datetime = PHP.runtime.get_date_type('today_start');
var to_datetime = PHP.runtime.get_date_type('today_end');
var deposit_amount = PHP.runtime.sum_deposit_amount(from_datetime, to_datetime, 0);
//投注额
var betting_amount = PHP.runtime.get_game_betting_amount(from_datetime, to_datetime);

var errorMessageLang = null;
var success = true;

PHP.runtime.debug_log(from_datetime + ' to ' + to_datetime + ', betting_amount:' + betting_amount);

if (errorMessageLang === null && deposit_amount < MIN_DEPOSIT_AMOUNT){
    //存款金额<5
    errorMessageLang = 'promo.nondeposit_not_suit_deposit_amount';
    bonus_amount=0;
}

if (errorMessageLang === null && betting_amount < MIN_BET_AMOUNT){
    //投注额<100
    errorMessageLang = 'promo.nondeposit_not_suit_betting_amount';
    bonus_amount=0;
}

if (errorMessageLang !== null){
	bonus_amount=FIXED_BONUS;
}

PHP.runtime.debug_log('bonus_amount:' + bonus_amount + "," + errorMessageLang + ', rate:' + rate);

var result = { "bonus_amount": bonus_amount, "errorMessageLang": errorMessageLang };
result;


