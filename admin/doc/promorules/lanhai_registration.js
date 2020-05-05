//first name , withdrawal bank condition
PHP.runtime.debug_log('start check condition');
var success=true;
var errorMessageLang='';

if(success && !PHP.runtime.checkPlayerRegisteredDate('2017-12-22 00:00:00', null)){
	errorMessageLang = '对不起，不符合优惠条件';
	success=false;
}else{
	PHP.runtime.debug_log("validate: checkPlayerRegisteredDate is ok");
}

if(success && !PHP.runtime.is_player_filled_first_name()) {
	errorMessageLang = '对不起，请填写真实姓名';
	success=false;
}else{
	PHP.runtime.debug_log("validate: player_filled_first_name is ok");
}

if(success && !PHP.runtime.is_player_at_least_one_withdrawal_bank()) {
	errorMessageLang = '对不起，至少要绑定一张取款卡';
	success=false;
}else{
	PHP.runtime.debug_log("validate: player_at_least_one_withdrawal_bank is ok");
}

PHP.runtime.debug_log('success:'+success+', error message:'+errorMessageLang);
var result={ "success": success , "message": errorMessageLang};
result;

