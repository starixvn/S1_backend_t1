//withdrawal bank condition
PHP.runtime.debug_log('start check condition');
var success=true;
var errorMessageLang='';

if(success && !PHP.runtime.is_player_at_least_one_withdrawal_bank()) {
	errorMessageLang = '<a href="/player_center/iframe_bankDetails">请点击此处绑定银行卡</a>';
	success=false;
}else{
	PHP.runtime.debug_log("validate: player_at_least_one_withdrawal_bank is ok");
}

PHP.runtime.debug_log('success:'+success+', error message:'+errorMessageLang);
var result={ "success": success , "message": errorMessageLang};
result;
