<?php

trait gamegateway_module{

	public function create_simple_merchant(){
		$this->load->model(['common_token', 'agency_model']);

        $agent_name='testmerchant';
        $merchant_name=$agent_name;
        $password='123456';
        $credit_limit=100000000;
        $available_credit=100000000;

		$rlt=$this->agency_model->createAgentWithMerchant($agent_name, $merchant_name, $password,$credit_limit,$available_credit);

		$this->utils->debug_log('create result', $rlt);

		return $rlt;
	}

	private function getFirstMerchantCode(){
        $this->load->model(['common_token', 'agency_model']);
		return $this->agency_model->getFirstAgent();
	}

	private function signGatewayApi($signKey, &$params){
        $this->load->model(['common_token', 'agency_model']);
		list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);

		$this->utils->debug_log('test sign', $signString, 'sign:'.$sign, $params);

		$params['sign']=$sign;
		return $params;
	}

	public function manually_generate_token($merchant_code){
		$this->load->model(['common_token', 'agency_model']);

		$merchant=$this->agency_model->getMerchantInfoByCode($merchant_code);
		$this->agency_model->regenerateAndSaveSignKey($merchant);
		$this->agency_model->regenerateAndSaveSecureKey($merchant);

		$merchant=$this->agency_model->getMerchantInfoByCode($merchant_code);
		$this->utils->debug_log('merchant', $merchant);

		return $merchant;
	}

	public function call_generate_token($force_new='false'){

		$this->load->model(['common_token', 'agency_model']);

		$merchant=$this->getFirstMerchantCode();
		$merchant_code=$merchant['agent_name'];
		$secure_key=$this->common_token->getSecureKeyFrom($merchant);
		$signKey=$this->common_token->getSignKeyFrom($merchant);

		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/generate_token';

		$params=['merchant_code'=>$merchant_code, 'secure_key'=>$secure_key, 'force_new'=>$force_new=='true'];
		if($this->utils->isEnabledMDB()){
			$params['currency']=$this->db->getOgTargetDB();
		}
		// $this->agency_model->initPrefixForGameAccount($merchant['agent_id']);

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

		if(empty($jsonObj['detail']['auth_token'])){
			throw new Exception('cannot get auth token');
		}

		return [$merchant_code, $jsonObj['detail']['auth_token'], $merchant];

	}

	private function init_any_auth_token(){

	    if(empty($this->game_platform_id)){
            $this->game_platform_id=T1LOTTERY_API;
        }

        if(empty($this->auth_token)){
    		list($this->merchant_code, $this->auth_token, $this->merchant)=$this->call_generate_token();
        }
//		$this->merchant=$merchant;
//		$this->merchant_code=$merchant_code;

		$params=['auth_token'=>$this->auth_token, 'merchant_code'=>$this->merchant_code,
            'game_platform_id'=>$this->game_platform_id];

		return [$params, $this->merchant];
	}

	public function call_create_player_account($game_platform_id=T1LOTTERY_API, $username=''){

		$this->load->model(['common_token','agency_model']);

        $this->game_platform_id=$game_platform_id;

        $this->utils->debug_log('---------------create_player_account----------------------- ', $this->game_platform_id);
		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/create_player_account';

		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);
		$agentId=$merchant['agent_id'];
		$prefix='';
		$this->utils->debug_log('get prefix ', $prefix, $agentId, $game_platform_id);
		$username=empty($username) ? $prefix.'testapi'.random_string('numeric', 5) : $username;
		$password=random_string('numeric', 8);
		$realname='real'.random_string('numeric', 6);
		$extra=[];

		$params=array_merge($params, ['username'=>$username, 'password'=>$password,
			'realname'=>$realname, 'extra'=>$extra]);
		$params['no_prefix_on_username']=true;

		if($this->utils->isEnabledMDB()){
			$params['currency']=$this->db->getOgTargetDB();
		}

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);
		if(isset($jsonObj['success']) && !$jsonObj['success']){
			$this->utils->error_log('error', $jsonObj);
		}

		$this->call_api_username=$username;

		return ['call_create_player_account'=>$jsonObj];
	}

	public function call_is_player_account_exist($username, $game_platform_id=T1LOTTERY_API){

		if(empty($username)){
			return $this->utils->error_log('wrong username', $username);
		}

		$this->load->model(['common_token']);

        $this->game_platform_id=$game_platform_id;

        $this->utils->debug_log('---------------is_player_account_exist----------------------- '.$username, $this->game_platform_id);
        $url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/is_player_account_exist';

		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

		$params=array_merge($params, ['username'=>$username]);

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        return ['call_is_player_account_exist'=>$jsonObj];
	}

	public function call_query_player_online_status($username, $game_platform_id=T1LOTTERY_API){

		if(empty($username)){
			return $this->utils->error_log('wrong username', $username);
		}

		$this->load->model(['common_token']);

        $this->game_platform_id=$game_platform_id;

        $this->utils->debug_log('---------------query_player_online_status----------------------- '.$username, $this->game_platform_id);
        $url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/query_player_online_status';

		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

		$params=array_merge($params, ['username'=>$username]);

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        return ['call_query_player_online_status'=>$jsonObj];
	}

	public function call_change_player_password($username, $game_platform_id=T1LOTTERY_API){

		if(empty($username)){
			return $this->utils->error_log('wrong username', $username);
		}

		$this->load->model(['common_token']);

        $this->game_platform_id=$game_platform_id;

        $this->utils->debug_log('---------------change password----------------------- '.$username, $this->game_platform_id);
		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/change_player_password';

		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

		$password='654321';
		$params=array_merge($params, ['username'=>$username, 'password'=>$password]);

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

//		$this->utils->debug_log('---------------change password----------------------- '.$username);
//		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/change_player_password';
//
//		list($params, $merchant)=$this->init_any_auth_token();
//		$signKey=$this->common_token->getSignKeyFrom($merchant);
//
//		// $game_platform_id=T1LOTTERY_API;
//		// $username='testapi'.random_string('numeric', 5);
//		$password='123456';
//		$params=array_merge($params, ['username'=>$username, 'password'=>$password]);
//
//		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
//		$this->signGatewayApi($signKey, $params);
//		$postJson=true;
//		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);
//
//		$jsonObj=$this->utils->decodeJson($json);
//
//		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        return ['call_change_player_password'=>$jsonObj];
	}

	public function call_block_player_account($username, $game_platform_id=T1LOTTERY_API){

		if(empty($username)){
			return $this->utils->error_log('wrong username', $username);
		}

		$this->load->model(['common_token']);

        $this->game_platform_id=$game_platform_id;

		$this->utils->debug_log('---------------block----------------------- '.$username, $this->game_platform_id);
		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/block_player_account';

		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

		$params=array_merge($params, ['username'=>$username]);

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

		$rlt[]=$jsonObj;

		$this->utils->debug_log('---------------query block----------------------- '.$username);
		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/query_player_block_status';

		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

		$params=array_merge($params, ['username'=>$username]);

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        $rlt[]=$jsonObj;

		$this->utils->debug_log('---------------unblock----------------------- '.$username);
		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/unblock_player_account';

		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

		// $game_platform_id=T1LOTTERY_API;
		// $username='testapi'.random_string('numeric', 5);

		$params=array_merge($params, ['username'=>$username]);

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        $rlt[]=$jsonObj;

		$this->utils->debug_log('---------------query block----------------------- '.$username);
		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/query_player_block_status';

		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

		$params=array_merge($params, ['username'=>$username]);

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        $rlt[]=$jsonObj;

        return ['call_block_player_account'=>$rlt];

    }

	public function call_deposit_player($username, $game_platform_id=T1LOTTERY_API, $amount='100')
	{

		if (empty($username)) {
			return $this->utils->error_log('wrong username', $username);
		}

		$this->load->model(['common_token']);

		$this->game_platform_id = $game_platform_id;

		//deposit
		$this->utils->debug_log('---------------deposit----------------------- ' . $username);
		$url = $this->utils->getConfig('call_gamegateway_api_url') . '/gamegateway/transfer_player_fund';

		list($params, $merchant) = $this->init_any_auth_token();
		$signKey = $this->common_token->getSignKeyFrom($merchant);

		$external_trans_id = random_string('md5');
		$params = array_merge($params, ['username' => $username, 'action_type' => 'deposit', 'amount' => $amount, 'external_trans_id' => $external_trans_id]);

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson = true;
		$json = $this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj = $this->utils->decodeJson($json);

		$this->utils->debug_log('send url:' . $url, 'params', $params, 'result', $json, $jsonObj);

		$rlt[] = $jsonObj;

		if (!$jsonObj['success']) {
			return $rlt;
		}

		if(isset($jsonObj['detail']['transaction_id'])){
			$this->utils->debug_log($external_trans_id.' to '.$jsonObj['detail']['transaction_id']);
			$external_trans_id=$jsonObj['detail']['transaction_id'];
		}

		$this->utils->debug_log('---------------query_transaction----------------------- ' . $external_trans_id);
		$rlt[] = $this->call_query_transaction($external_trans_id, $username, $game_platform_id);
		if (!$jsonObj['success']) {
			return $rlt;
		}
		$rlt[] = $this->call_query_player_balance($username, $game_platform_id);
		if (!$jsonObj['success']) {
			return $rlt;
		}

		return ['call_deposit_player'=>$rlt];
	}

	public function call_withdraw_player($username, $game_platform_id=T1LOTTERY_API, $amount='100'){

		if(empty($username)){
			return $this->utils->error_log('wrong username', $username);
		}

		$this->load->model(['common_token']);

        $this->game_platform_id=$game_platform_id;

		//withdraw
		$this->utils->debug_log('---------------withdraw----------------------- '.$username);
		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/transfer_player_fund';

		$this->game_platform_id=$game_platform_id;
		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

		$external_trans_id=random_string('md5');
		$params=array_merge($params, ['username'=>$username, 'action_type'=>'withdraw', 'amount'=>$amount, 'external_trans_id'=>$external_trans_id]);

		list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        $rlt[]=$jsonObj;

        if(!$jsonObj['success']){
            return $rlt;
        }

		if(isset($jsonObj['detail']['transaction_id'])){
			$this->utils->debug_log($external_trans_id.' to '.$jsonObj['detail']['transaction_id']);
			$external_trans_id=$jsonObj['detail']['transaction_id'];
		}

		$this->utils->debug_log('---------------query_transaction----------------------- '.$external_trans_id);
        $rlt[]=$this->call_query_transaction($external_trans_id, $username, $game_platform_id);
        if(!$jsonObj['success']){
            return $rlt;
        }
        $rlt[]=$this->call_query_player_balance($username, $game_platform_id);
        if(!$jsonObj['success']){
            return $rlt;
        }

		return ['call_withdraw_player'=>$rlt];
	}

	public function call_query_transaction($external_trans_id, $username, $game_platform_id=T1LOTTERY_API){
		if(empty($external_trans_id)){
			return $this->utils->error_log('wrong external_trans_id', $external_trans_id);
		}

		$this->load->model(['common_token']);

        $this->game_platform_id=$game_platform_id;

        $url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/query_transaction';

		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

		$params=array_merge($params, ['external_trans_id'=>$external_trans_id, 'username'=>$username]);

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        return ['call_query_transaction'=>$jsonObj];

	}

	public function call_query_player_balance($username, $game_platform_id=T1LOTTERY_API){

		if(empty($username)){
			return $this->utils->error_log('wrong username', $username);
		}

		$this->load->model(['common_token']);

        $this->game_platform_id=$game_platform_id;

		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/query_player_balance';

		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

		$params=array_merge($params, ['username'=>$username]);

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        return ['call_query_player_balance'=>$jsonObj];
	}

	public function call_query_game_launcher($username, $game_platform_id=T1LOTTERY_API, $game_code='', $mode='real'){
		if(empty($username)){
			return $this->utils->error_log('wrong username', $username);
		}

		$this->load->model(['common_token']);

        $this->game_platform_id=$game_platform_id;

        $url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/query_game_launcher';

		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

		// $game_platform_id=T1LOTTERY_API;
		// $username='testapi'.random_string('numeric', 5);
        $launcher_settings=['game_unique_code'=>$game_code, 'language'=>'zh-cn', 'platform'=>'pc', 'mode'=>$mode, 'extra'=>[]];
		$params=array_merge($params, ['username'=>$username, 'launcher_settings'=>$launcher_settings]);

		$params['game_platform_id']=$this->game_platform_id;

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        return ['call_query_game_launcher'=>$jsonObj];
	}

	public function call_all_query_game_launcher($username, $game_platform_id=T1LOTTERY_API){
		if(empty($username)){
			return $this->utils->error_log('wrong username', $username);
		}

		$this->load->model(['common_token']);

        $this->game_platform_id=$game_platform_id;

		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/query_game_launcher';

		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

		$urls=[];
		$games=[T1LOTTERY_API=>'', AGIN_API=>'0', AGBBIN_API=>'1', IMPT_API=>'fm', FISHINGGAME_API=>'0', MG_API=>'asianbeauty'];

		foreach ($games as $game_platform_id=>$game_code) {

			// $username='testapi'.random_string('numeric', 5);
			$launcher_settings=['game_unique_code'=>$game_code, 'language'=>'zh-cn', 'platform'=>'pc', 'extra'=>[]];
			$params=array_merge($params, ['username'=>$username, 'launcher_settings'=>$launcher_settings]);

			$params['game_platform_id']=$game_platform_id;

			// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
			$this->signGatewayApi($signKey, $params);
			$postJson=true;
			$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

			$jsonObj=$this->utils->decodeJson($json);

			$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

			$urls[]=$jsonObj['detail']['launcher']['url'];
		}

		$this->utils->debug_log($urls);

        return $urls;
	}

	public function call_query_game_history($page_number=1, $from=null, $to=null, $game_platform_id=T1LOTTERY_API, $username=null){

		$this->load->model(['common_token']);

		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/query_game_history';

        $this->game_platform_id=$game_platform_id;

        list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

        if(empty($from)){

            $qry=$this->db->select('min(end_at) as min_date, max(end_at) as max_date')
                ->from('game_logs')->where('flag', '1')
                ->where('game_platform_id', $this->game_platform_id)->get();

            $qryRlt=$qry->result_array();
			$from=$qryRlt[0]['min_date'];
			$to=$qryRlt[0]['max_date'];

		}

		$params=array_merge($params, ['from'=>$from, 'to'=>$to, 'page_number'=>$page_number,'size_per_page'=>3]);

		if(!empty($username)){
			$params['username']=$username;
		}

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        return ['call_query_game_history'=>$jsonObj];
	}

	public function call_query_unsettle_game_history($page_number=1, $from=null, $to=null, $game_platform_id=T1LOTTERY_API, $username=null){

		$this->load->model(['common_token']);

		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/query_unsettle_game_history';

        $this->game_platform_id=$game_platform_id;

        list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

        if(empty($from)){

            $qry=$this->db->select('min(end_at) as min_date, max(end_at) as max_date')
                ->from('game_logs')->where('flag', '1')
                ->where('game_platform_id', $this->game_platform_id)->get();

            $qryRlt=$qry->result_array();
			$from=$qryRlt[0]['min_date'];
			$to=$qryRlt[0]['max_date'];

		}

		$params=array_merge($params, ['from'=>$from, 'to'=>$to, 'page_number'=>$page_number,'size_per_page'=>3]);

		if(!empty($username)){
			$params['username']=$username;
		}

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        return ['call_query_unsettle_game_history'=>$jsonObj];
	}

	public function call_total_game_history($page_number=1, $from=null, $to=null, $game_platform_id=T1LOTTERY_API, $username=null){

		$this->load->model(['common_token']);

		$url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/total_game_history';

        $this->game_platform_id=$game_platform_id;

		list($params, $merchant)=$this->init_any_auth_token();
		$signKey=$this->common_token->getSignKeyFrom($merchant);

        if(empty($from)){
            $qry=$this->db->select('min(end_at) as min_date, max(end_at) as max_date')
                ->from('game_logs')->where('flag', '1')
                ->where('game_platform_id', $this->game_platform_id)->get();

            $qryRlt=$qry->result_array();
			$from=$qryRlt[0]['min_date'];
			$to=$qryRlt[0]['max_date'];
		}
        $total_type='minute';

		$params=array_merge($params, ['from'=>$from, 'to'=>$to, 'total_type'=>$total_type, 'page_number'=>$page_number,'size_per_page'=>3]);

		if(!empty($username)){
			$params['username']=$username;
		}

		// list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
		$this->signGatewayApi($signKey, $params);
		$postJson=true;
		$json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

		$jsonObj=$this->utils->decodeJson($json);

		$this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        return ['call_total_game_history'=>$jsonObj];
	}

    public function call_query_transaction_history($page_number=1, $from=null, $to=null, $game_platform_id=T1LOTTERY_API){

        $this->load->model(['common_token', 'transactions']);

        $url=$this->utils->getConfig('call_gamegateway_api_url').'/gamegateway/query_transaction_history';

        $this->game_platform_id=$game_platform_id;

        list($params, $merchant)=$this->init_any_auth_token();
        $signKey=$this->common_token->getSignKeyFrom($merchant);

        if(empty($from)){
            $qry=$this->db->select('min(created_at) as min_date, max(created_at) as max_date')
                ->from('transactions')->where('status', Transactions::APPROVED)
                ->where_in('transaction_type', [Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET,
                    Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET])->get();

            $qryRlt=$qry->result_array();
            $from=$qryRlt[0]['min_date'];
            $to=$qryRlt[0]['max_date'];
        }

        $params=array_merge($params, ['from'=>$from, 'to'=>$to, 'page_number'=>$page_number,'size_per_page'=>3]);

        // list($sign, $signString)=$this->common_token->generateSign($params, $signKey, ['sign']);
        $this->signGatewayApi($signKey, $params);
        $postJson=true;
        $json=$this->utils->simpleSubmitPostForm($url, $params, $postJson);

        $jsonObj=$this->utils->decodeJson($json);

        $this->utils->debug_log('send url:'.$url,'params', $params, 'result', $json, $jsonObj);

        return ['call_query_transaction_history'=>$jsonObj];

    }

    /**
     * @param string $apiStr like '53,72'
     * @return array
     */
    public function call_all_api($apiStr=T1LOTTERY_API){

        $this->load->model(['common_token']);
        $apiArr=explode(',', $apiStr);

        $page_number=1;
        $from='2017-07-01 00:00:00';
        $to='2017-07-15 23:59:59';

        $games=[T1LOTTERY_API=>'', AGIN_API=>'0', AGBBIN_API=>'1', IMPT_API=>'fm', FISHINGGAME_API=>'0', MG_API=>'asianbeauty'];

        $rlt=[];

        foreach ($apiArr as $api) {

            $this->game_platform_id=intval($api);

            $rlt[$api]=[];

            $rlt[$api][]=$this->call_create_player_account($api);
            //after create player account
            $username=$this->call_api_username;

            $rlt[$api][]=$this->call_is_player_account_exist($username, $api);

            $rlt[$api][]=$this->call_query_player_online_status($username, $api);

            $rlt[$api][]=$this->call_change_player_password($username, $api);

            $rlt[$api][]=$this->call_block_player_account($username, $api);

            $rlt[$api][]=$this->call_transfer_player_fund($username, $api);

            $rlt[$api][]=$this->call_query_game_launcher($username, $api, $games[$api]);

            $rlt[$api][]=$this->call_query_game_history($page_number, $from, $to, $api);

            $rlt[$api][]=$this->call_total_game_history($page_number, $from, $to, $api);

            $rlt[$api][]=$this->call_query_transaction_history($page_number, $from, $to, $api);

        }

        $this->utils->debug_log($rlt);

        return $rlt;
    }

}
