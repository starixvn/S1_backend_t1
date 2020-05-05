//在2017-12-19 00:00:00 之後註冊的玩家才能取得優惠
PHP.runtime.debug_log('start bonus release'); 
var success=false; 
var message='对不起，不符合优惠条件'; 
success = PHP.runtime.checkPlayerRegisteredDate('2017-12-22 00:00:00', null);
if (success) {
    message = '';
}
PHP.runtime.debug_log('success:'+success); 
var result={ "success": success , "message": message}; 
result; 