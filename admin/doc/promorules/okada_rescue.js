//BONUS CONDITION
//3DAYS BEFORE EVENT DATE
PHP.runtime.debug_log('start bonus release'); 
var success=false; 
var message='Sorry, did not met promotion condition'; 
success = PHP.runtime.checkPlayerRegisteredDate('2017-12-19 00:00:00', '2017-12-31 23:59:59');
if (success) {
    message = '';
}
var MIN_BALANCE_AMOUNT = 1000;
var from_datetime = PHP.runtime.get_date_type('yesterday_start');
var to_datetime = PHP.runtime.get_date_type('yesterday_end');
var deposit_amount = PHP.runtime.sum_deposit_amount(from_datetime, to_datetime, 0);
var withdrawal_amount = PHP.runtime.sum_withdrawal_amount(from_datetime, to_datetime, 0);
loss_amount = deposit_amount - withdrawal_amount;
var total_balance = PHP.runtime.current_player_total_balance();
loss_amount = loss_amount - total_balance;
PHP.runtime.debug_log("loss amount : " + loss_amount);
if (total_balance > MIN_BALANCE_AMOUNT) {
    PHP.runtime.debug_log("total balnace <= " + MIN_BALANCE_AMOUNT);
    message='Sorry, did not met promotion condition'; 
    success=false;
}
var bonus = 0;
if (success) {
    bonus = parseFloat(loss_amount) * 0.05;
    if (bonus <= 0) {
        PHP.runtime.debug_log("bonus = " + bonus);
        message='Sorry, did not met promotion condition'; 
        success=false;
    }
}

PHP.runtime.debug_log('success:'+success); 
var result={ "success": success , "message": message}; 
result; 


//BONUS RELEASE
var MIN_BALANCE_AMOUNT = 1000;
var from_datetime = PHP.runtime.get_date_type('yesterday_start');
var to_datetime = PHP.runtime.get_date_type('yesterday_end');
var deposit_amount = PHP.runtime.sum_deposit_amount(from_datetime, to_datetime, 0);
var withdrawal_amount = PHP.runtime.sum_withdrawal_amount(from_datetime, to_datetime, 0);
loss_amount = deposit_amount - withdrawal_amount;
var total_balance = PHP.runtime.current_player_total_balance();
loss_amount = loss_amount - total_balance;
var errorMessageLang = null;
var success = true;
if (total_balance > MIN_BALANCE_AMOUNT) {
    errorMessageLang = 'Sorry, did not met promotion condition';
    success=false;
}
var bonus = 0;
if (success) {
    bonus = loss_amount * 0.05;
    PHP.runtime.debug_log("bonus release :" + bonus);

    if (bonus <= 0) {
        PHP.runtime.debug_log("validate: bonus<=0");
        errorMessageLang='Sorry, did not met promotion condition';
        success=false;
    }
}
var result = { "bonus_amount": bonus, "errorMessageLang": errorMessageLang };
result;
