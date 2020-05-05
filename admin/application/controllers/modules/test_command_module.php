<?php

trait test_command_module{

    public function test_cache(){

    	$key=$this->utils->getAppPrefix().'-test_key';
    	$text='random '.random_string();
    	$ttl=180;

		$this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));

		$this->utils->debug_log('-----get cache key', $key, $text, $ttl);

		$succ=$this->cache->save($key, $text, $ttl);
		$this->utils->info_log('save cache', $succ);

		$data = $this->cache->get($key);

		$this->utils->info_log('get data', $data);

    }

    public function test_log(){
		$this->utils->error_log('it is error');
		$this->utils->info_log('it is info');
		$this->utils->debug_log('it is debug');

		$this->utils->undefined_func();
    }

    public function test_mdb($target_db=null){

    	//example: _OG_TARGET_DB=cny bash ./admin/shell/test.sh test_mdb
    	$env_target_db=getenv('_OG_TARGET_DB');

    	$this->utils->info_log('target_db', $target_db, 'env_target_db', $env_target_db);

    	if(empty($target_db) && !empty($env_target_db)){
    		//overwrite
    		$target_db=$env_target_db;
    	}

    	$this->load->model(['player_model']);
    	$readonly=false;
    	$sql='select * from migrations';
    	$params=null;
    	$result=$this->player_model->runRawSelectSQLArrayOnMDB($sql, $params, $readonly);

    	$this->utils->debug_log($result);

    	$this->player_model->foreachMultipleDBToCurrentDB(function(){

    		$this->utils->debug_log('load db: '.$this->db->database);

    	});

    }

    public function test_sync_role_super_to_mdb($roleId){

        $this->load->model(['multiple_db_model', 'roles']);
        $rlt=$this->multiple_db_model->syncRoleFromSuperToOtherMDB($roleId);

        $this->utils->debug_log('syncRoleFromSuperToOtherMDB', $rlt);
    }

    public function test_sync_user_super_to_mdb($username){
        $this->load->model(['multiple_db_model', 'users']);
        $userId=$this->users->getIdByUsername($username);
        $rlt=$this->multiple_db_model->syncUserFromSuperToOtherMDB($userId);

        $this->utils->debug_log('syncUserFromSuperToOtherMDB', $rlt);
    }

    public function test_sync_player_super_to_mdb($username){
        $this->load->model(['multiple_db_model', 'player_model']);
        $playerId=$this->player_model->getPlayerIdByUsername($username);
        $rlt=$this->multiple_db_model->syncPlayerFromSuperToOtherMDB($playerId);

        $this->utils->debug_log('syncPlayerFromSuperToOtherMDB', $rlt);
    }

    public function test_sync_agency_super_to_mdb($username){
        $this->test_sync_agency_to_mdb(Multiple_db::SUPER_TARGET_DB, $username);
    }

    public function test_sync_agency_to_mdb($dbKey, $username){
        $this->load->model(['multiple_db_model', 'agency_model']);
        $agentId=$this->agency_model->get_agent_id_by_agent_name($username);
        $rlt=$this->multiple_db_model->syncAgencyFromOneToOtherMDB($dbKey, $agentId);

        $this->utils->debug_log('syncAgencyFromOneToOtherMDB:'.$dbKey, $rlt);
    }

    public function test_sync_aff_super_to_mdb($username){
        $this->test_sync_aff_to_mdb(Multiple_db::SUPER_TARGET_DB, $username);
    }

    public function test_sync_aff_to_mdb($dbKey, $username){
        $this->load->model(['multiple_db_model', 'affiliatemodel']);
        $affId=$this->affiliatemodel->getAffiliateIdByUsername($username);
        $rlt=$this->multiple_db_model->syncAffFromOneToOtherMDB($dbKey, $affId);

        $this->utils->debug_log('syncAffFromOneToOtherMDB:'.$dbKey, $rlt);
    }

    public function test_show_adminuser_on_mdb($username){
        $this->load->model('multiple_db_model');
        $sql=<<<EOD
select adminusers.*, roles.roleName from adminusers
join userroles on userroles.userId=adminusers.userId
join roles on userroles.roleId=roles.roleId
where username=?
EOD;
        $params=[$username];
        $rlt=$this->multiple_db_model->showResultOnMDB($sql, $params);
        $this->utils->info_log('test_show_adminuser_on_mdb', $rlt);
    }

    public function test_show_role_on_mdb($roleId){
        $this->load->model('multiple_db_model');
        $sql=<<<EOD
select * from roles
where roleId=?
EOD;
        $params=[$roleId];
        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);

        $sql=<<<EOD
select * from rolefunctions
where roleId=?
EOD;
        $params=[$roleId];
        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);

        $sql=<<<EOD
select * from rolefunctions_giving
where roleId=?
EOD;
        $params=[$roleId];
        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);

        $this->utils->info_log('test_show_role_on_mdb', $rlt);
    }

    public function test_show_player_on_mdb($username){
        $this->load->model('multiple_db_model');
        $playerId=$this->multiple_db_model->runAnyOnSuperDB(function($db)
            use($username){
            $db->select('playerId')->from('player')->where('username', $username);
            return $this->multiple_db_model->runOneRowOneField('playerId', $db);
        });

        $sql=<<<EOD
select player.username, player.password, playerdetails.language, player.currency from player
join playerdetails on player.playerId=playerdetails.playerId
where player.playerId=?
EOD;
        $params=[$playerId];
        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);

        $sql=<<<EOD
select * from playerlevel
where playerlevel.playerId=?
EOD;
        $params=[$playerId];

        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);
        $this->utils->info_log('test_show_player_on_mdb', $rlt);
    }

    public function test_show_agency_on_mdb($username){

        $this->load->model('multiple_db_model');
        $agent_id=$this->multiple_db_model->runAnyOnSuperDB(function($db)
            use($username){
            $db->select('agent_id')->from('agency_agents')->where('agent_name', $username);
            return $this->multiple_db_model->runOneRowOneField('agent_id', $db);
        });

        $sql=<<<EOD
select agency_agents.agent_id, agency_agents.agent_name, agency_agents.password, agency_agents.language, agency_agents.currency
from agency_agents
where agency_agents.agent_id=?
EOD;
        $params=[$agent_id];

        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);
        $this->utils->info_log('test_show_agency_on_mdb agency_agents');

        $sql=<<<EOD
select * from agency_agent_game_platforms
where agent_id=?
EOD;
        $params=[$agent_id];
        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);
        $this->utils->info_log('test_show_agency_on_mdb agency_agent_game_platforms');

        $sql=<<<EOD
select * from agency_agent_game_types
where agent_id=?
EOD;
        $params=[$agent_id];
        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);
        $this->utils->info_log('test_show_agency_on_mdb agency_agent_game_types');

        $sql=<<<EOD
select * from agency_flattening
where agent_id=?
EOD;
        $params=[$agent_id];
        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);
        $this->utils->info_log('test_show_agency_on_mdb agency_flattening');

        $sql=<<<EOD
select * from agency_flattening_options
where agent_id=?
EOD;
        $params=[$agent_id];
        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);
        $this->utils->info_log('test_show_agency_on_mdb agency_flattening_options');

        $sql=<<<EOD
select * from agency_tracking_domain
where agent_id=?
EOD;
        $params=[$agent_id];
        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);
        $this->utils->info_log('test_show_agency_on_mdb agency_tracking_domain');

        $this->utils->info_log('test_show_agency_on_mdb', $rlt);

    }

    public function test_show_aff_on_mdb($username){
        $this->load->model('multiple_db_model');
        $affiliateId=$this->multiple_db_model->runAnyOnSuperDB(function($db)
            use($username){
            $db->select('affiliateId')->from('affiliates')->where('username', $username);
            return $this->multiple_db_model->runOneRowOneField('affiliateId', $db);
        });

        $sql=<<<EOD
select affiliates.affiliateId, affiliates.username, affiliates.password, affiliates.language, affiliates.currency
from affiliates
where affiliates.affiliateId=?
EOD;
        $params=[$affiliateId];
        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);

        $sql=<<<EOD
select * from affiliate_terms
where affiliateId=?
EOD;
        $params=[$affiliateId];
        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);

        $sql=<<<EOD
select * from aff_tracking_link
where aff_id=?
EOD;
        $params=[$affiliateId];
        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);

        $sql=<<<EOD
select * from affiliate_read_only_account
where affiliate_id=?
EOD;
        $params=[$affiliateId];
        $rlt[]=$this->multiple_db_model->showResultOnMDB($sql, $params);

        $this->utils->info_log('test_show_aff_on_mdb', $rlt);
    }

    public function test_global_lock($username){

        $success=$this->utils->globalLockPlayerRegistration($username, function(){
            $success=true;

            sleep(20);
            $this->utils->info_log('locked');

            return $success;
        });

        $this->utils->info_log('global lock', $success);
    }

    public function test_parallel_global_lock($username, $max=4){

        for ($i=0; $i <$max ; $i++) {
            $cmd=$this->utils->generateCommandLine('test_global_lock', [$username], false);
            $this->utils->runCmd($cmd);
        }

    }

    public function test_refresh_wallet($username){
        $this->load->model(['player_model', 'wallet_model']);
        $playerId=$this->player_model->getPlayerIdByUsername($username);

        $success=$this->lockAndTransForPlayerBalance($playerId, function() use($playerId){
            $db=$this->player_model->getSuperDBFromMDB();
            $success=$this->wallet_model->refreshBigWalletOnDB($playerId, $db);
            return $success;
        });

        $this->utils->debug_log('refreshBigWalletOnDB', $success);
    }

    public function test_db_result($type='number'){
        $db=$this->db;
        $readonly=false;
        $cacheOnMysql=false;
        $sql='select * from logs';

        $this->utils->debug_log('start');
        $_multiple_db=Multiple_db::getSingletonInstance();
        $conn=$_multiple_db->rawConnectDB($readonly, $db);
        try{

            $qry = mysqli_query($conn, $sql, $cacheOnMysql ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT);

            $this->utils->info_log('before fetch');
            $rows = mysqli_fetch_all($qry, $type=='number' ? MYSQLI_NUM : MYSQLI_ASSOC);
            $this->utils->info_log('after fetch');

            $fields=mysqli_fetch_fields($qry);

            mysqli_free_result($qry);
            unset($qry);

        }finally{
            if(!empty($conn)){
                mysqli_close($conn);
            }
        }
        unset($conn);

        $this->utils->debug_log(count($rows));

        $this->utils->debug_log('done');

    }

    public function test_summary2_report(){
        $this->load->model(['multiple_db_model']);
        $conditions=[
            'searchBy'=>[
                'dateFrom'=>'2018-09-01',
                'dateTo'=>'2018-09-08',
                'monthOnly'=>false,
            ],
            'limitBy'=>[
                'pageSize'=>10,
                'currentPage'=>1,
            ],
        ];
        $rlt=$this->multiple_db_model->querySummary2Report($conditions, Multiple_db_model::QUERY_REPORT_TYPE_ONE_PAGE);

        $this->utils->info_log('querySummary2Report', $rlt);
    }

    public function test_send_alert(){
        $level='warning';
        $title='test warning '.random_string();
        $message='message text content warning '.random_string();
        $this->utils->sendAlertBack($level, $title, $message);
        $level='error';
        $title='test error '.random_string();
        $message='message text content error '.random_string();
        $this->utils->sendAlertBack($level, $title, $message);
    }

    public function test_fix_collation(){
        $this->load->model(['player_model']);
        $this->player_model->fixCollationOnTable('mg_game_logs', ['account_number', 'display_name',
            'display_game_category', 'external_uniqueid', 'module_id', 'client_id', 'uniqueid',
            'user_name', 'external_game_id', 'game_platform']);
    }

    public function test_google_auth($secret=null, $app_code=null){
        $this->load->library('third_party/lib_google_authenticator', null, 'lib_google_authenticator');
        if(empty($secret)){
            $secret=$this->lib_google_authenticator->createSecret();
        }
        $currentTimeSlice = floor(time() / 30);
        $discrepancy=10;
        for ($i = -$discrepancy; $i <= $discrepancy; ++$i) {
            $calculatedCode = $this->lib_google_authenticator->getCode($secret, $currentTimeSlice + $i);
            if($app_code==$calculatedCode){
                $this->utils->info_log('found code', $app_code, $secret, $currentTimeSlice);
            }
            $this->utils->debug_log('calculatedCode', $calculatedCode, $secret, $currentTimeSlice);
        }
        $code=$this->lib_google_authenticator->getCode($secret);
        $rlt=$this->lib_google_authenticator->verifyCode($secret, $code);
        $this->utils->debug_log('verify code', $secret, $code, $rlt);
    }

    public function test_lock_redis($playerId=9999999){

        $success=$this->lockAndTransForPlayerBalance($playerId, function(){
            $success=true;

            sleep(20);
            $this->utils->info_log('locked');

            return $success;
        });

        $this->utils->info_log('lock redis', $success);
    }

    public function test_parallel_lock_redis($playerId=9999999, $max=2){

        for ($i=0; $i <$max ; $i++) {
            $cmd=$this->utils->generateCommandLine('test_lock_redis', [$playerId], false);
            $this->utils->runCmd($cmd);
        }

    }

    public function test_forward_incomplete($username){
        $this->utils->debug_log('test_forward_incomplete: '.$username);
        $launcher_settings=['language'=>'en-us', 'mode'=>'real'];
        $gameManager=$this->utils->loadGameManager();
        $incompleteRlt=$gameManager->forwardToIncompleteGameLink($username, $launcher_settings);
        $this->utils->info_log('forwardToIncompleteGameLink ', $incompleteRlt, $username, $launcher_settings);
    }

    public function test_china_ip($ip){
        $this->utils->debug_log('test_china_ip', $this->utils->getIpCityAndCountry($ip));
        $this->utils->debug_log('test_china_ip only', $this->utils->getChinaCityCountryIp($ip));
    }

    public function test_calc_original_md5_for_api($apiId, $externalUniqueId, $filename){
        //$row[$md5Field]=$this->generateMD5SumOneRow($row, $keys, $floatFields);
        $api=$this->utils->loadExternalSystemLibObject($apiId);
        if(!empty($api)){
            $this->load->model(['original_game_logs_model']);
            $rlt=$this->original_game_logs_model->calcOriginalMD5ByApi($api->getOriginalTable(), $api, $externalUniqueId);
            $this->utils->info_log('test_calc_original_md5_for_api on db', $rlt);
            $jsonStr=file_get_contents('/home/vagrant/Code/'.$filename);
            $jsonArr=json_decode($jsonStr, true);
            $resultText=$jsonArr['resultText'];
            unset($jsonArr);unset($jsonStr);
            $rlt=$api->testMD5Fields($resultText, $externalUniqueId);
            $this->utils->info_log('test_calc_original_md5_for_api on result text', $rlt);
        }else{
            $this->utils->error_log('not found api', $apiId);
        }
    }

    public function test_transfer_subwallet_to_main_wallet($playerName, $apiId, $balance) {
        $this->load->model(['player_model','wallet_model']);
        $api = $this->utils->loadExternalSystemLibObject($apiId);
        if ($api) {
            $isPlayerExist = $api->isPlayerExist($playerName);
            if ($isPlayerExist) {
                $playerId=$this->player_model->getPlayerIdByUsername($playerName);
                $result = $this->utils->transferWallet($playerId, $playerName, $apiId, Wallet_model::MAIN_WALLET_ID, $balance);
                $this->utils->debug_log('return transferWallet', $result);
                if (isset($result['success']) && $result['success']) {
                    $this->utils->debug_log('transfer '.$playerName.' from '.$api->getPlatformCode().' balance:'.$balance.' success');
                } else {
                    $this->utils->error_log('transfer '.$playerName.' from '.$api->getPlatformCode().' balance:'.$balance.' failed');
                }
            }else{
                $this->utils->error_log('player does not exist');
            }
        }else{
            $this->utils->error_log('wrong api');
        }

    }

    public function test_unique_index_field($tableName, $fieldName){
        $this->load->model(['player_model']);
        $exists=$this->player_model->existsUniqueIndex($tableName, $fieldName);
        $this->utils->info_log('table:'.$tableName.', field:'.$fieldName.', unique index '.($exists ? 'exists' : 'does not exist'));
    }

    public function test_system_feature(){
        $this->load->model(['system_feature']);
        $enabled=$this->system_feature->isEnabledFeatureWithoutCache('notexist_feature');
        $this->utils->info_log('notexist_feature', $enabled);

        $enabled=$this->system_feature->isEnabledFeatureWithoutCache('add_security_on_deposit_transaction');
        $this->utils->info_log('add_security_on_deposit_transaction', $enabled);

        $enabled=$this->system_feature->isEnabledFeature('add_security_on_deposit_transaction');
        $this->utils->info_log('load from cache add_security_on_deposit_transaction', $enabled);

    }

    public function test_encrypt_decrypt($text){

        $error=null;
        $encryptedText=$this->utils->encryptPassword($text, $error);
        $this->utils->info_log('encrypt text', $text, 'to', $encryptedText, $error);

        $decryptedText=$this->utils->decryptPassword($encryptedText, $error);
        $this->utils->info_log('decrypt text', $encryptedText, 'to', $decryptedText, $error);

    }

    public function test_explain_sql(){
        $sql=<<<EOD
select game_logs.id as uniqueid,
game_logs.external_uniqueid as game_external_uniqueid,
game_provider_auth.login_name as username,
'idnsportidr' as merchant_code,
game_logs.game_platform_id as game_platform_id,
game_description.external_game_id as game_code,
game_description.game_name as game_name,
game_logs.end_at game_finish_time,
game_logs.note game_details,
game_logs.bet_at as bet_time,
game_logs.end_at as payout_time,
game_logs.`table` as round_number,
ROUND(ifnull(game_logs.trans_amount, game_logs.bet_amount), 2) as real_bet_amount,
ROUND(game_logs.bet_amount, 2) as effective_bet_amount,
ROUND(game_logs.result_amount, 2) as result_amount,
ROUND(game_logs.result_amount+ifnull(game_logs.trans_amount,game_logs.bet_amount), 2) as payout_amount,
ROUND(game_logs.after_balance, 2) as after_balance,
game_logs.bet_details as bet_details,
game_logs.md5_sum,
game_logs.ip_address,
game_logs.bet_type,
game_logs.odds_type,
game_logs.odds,
ROUND(game_logs.rent, 2) as rent,
game_logs.response_result_id,
game_logs.external_log_id as update_version,
'normal' as game_status,
1 as detail_status,
game_logs.updated_at as updated_at
from game_logs as game_logs use index(idx_updated_at)
join game_provider_auth on game_logs.player_id=game_provider_auth.player_id and game_logs.game_platform_id=game_provider_auth.game_provider_id
join game_description on game_description.id=game_logs.game_description_id
where
game_logs.flag=?
 and game_logs.updated_at >= ?
and game_provider_auth.agent_id=?

 and game_logs.game_platform_id=2090
order by game_logs.updated_at
 limit 2000

EOD;

        $params=[1, '2019-09-05 06:04:13', 13];
        $this->load->model(['player_model']);
        $rows=$this->player_model->queryExplainRows($sql, $params, 'game_logs');

        $this->utils->debug_log('explain sql', $rows);
    }

    public function test_redis_cache(){
        $key='test-'.date('YmdHis').'-'.rand();
        $originalVal='test001';

        // $redis=try_load_redis($this);
        // $redis->select(0);
        // $saveSucc=$redis->set($key, $originalVal);
        // $val=$redis->get($key);

        // $this->utils->info_log('try redis', $key, $originalVal, 'saveSucc', $saveSucc, 'get', $val);

        $key='test-'.date('YmdHis').'-'.rand();
        // $this->utils->setConfig('cache_driver_type', 'redis');
        $this->utils->setConfig('disable_cache', false);
        $saveSucc=$this->utils->saveTextToCache($key, $originalVal);
        $val = $this->utils->getTextFromCache($key);

        $this->utils->info_log('try utils cache', $key, $originalVal, 'saveSucc', $saveSucc,
            'get', $val);

        $delSucc = $this->utils->deleteCache($key);
        $this->utils->info_log('try utils delete key cache', $key, 'delSucc', $delSucc);
        // $delSucc = $this->utils->deleteCache();
        // $this->utils->info_log('try utils clear cache', 'delSucc', $delSucc);
        // sleep(30);
        // $redis=try_load_redis($this);
        // $redis->select(0);
        // $flushSucc=$redis->flushDb();
        // $this->utils->info_log('try utils flush cache', 'flushSucc', $flushSucc);

    }

    public function test_count_player_session(){

        $rlt=$this->player_model->countPlayerSession(new DateTime('-1 hour'));
        $this->utils->info_log('test countPlayerSession last hour', $rlt);
    }

    public function test_is_player_session_timeout($sessionId){
        $rlt=$this->player_model->isPlayerSessionTimeout($sessionId);
        $this->utils->info_log('test isPlayerSessionTimeout', $rlt);
    }

    public function test_exists_online_session($playerId){
        $rlt=$this->player_model->existsOnlineSession($playerId);
        $this->utils->info_log('test existsOnlineSession', $rlt);
    }

}
