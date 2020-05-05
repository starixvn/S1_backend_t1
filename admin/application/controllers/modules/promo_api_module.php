<?php
trait promo_api_module {

	public function query_deposit_list($api_key, $username, $date){

		$result['success']=false;

		if(!$this->isValidApiKey($api_key)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found key');
			//return error
			return $this->returnJsonResult($result);
		}

		$this->load->model(['player_model', 'transactions']);
		$player=null;
		if(!empty($username)){
			$player=$this->player_model->getPlayerByUsername($username);
		}

		if(empty($player)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found player');
			//return error
			return $this->returnJsonResult($result);
		}

		$from=$date.' 00:00:00';
		$to=$date.' 23:59:59';

		$result['query_deposit_list']=$this->transactions->getDepositListBy($player->playerId, $from, $to);
		$result['success']=true;

		return $this->returnJsonResult($result);
	}

	public function has_any_deposit($api_key, $username){

		$result['success']=false;

		if(!$this->isValidApiKey($api_key)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found key');
			//return error
			return $this->returnJsonResult($result);
		}

		$this->load->model(['player_model', 'transactions']);
		$player=null;
		if(!empty($username)){
			$player=$this->player_model->getPlayerByUsername($username);
		}

		if(empty($player)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found player');
			//return error
			return $this->returnJsonResult($result);
		}

		$result['has_any_deposit']=$this->transactions->hasAnyDeposit($player->playerId);
		$result['success']=true;

		return $this->returnJsonResult($result);
	}

	public function is_only_first_deposit($api_key, $username){

		$result['success']=false;

		if(!$this->isValidApiKey($api_key)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found key');
			//return error
			return $this->returnJsonResult($result);
		}

		$this->load->model(['player_model', 'transactions']);
		$player=null;
		if(!empty($username)){
			$player=$this->player_model->getPlayerByUsername($username);
		}

		if(empty($player)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found player');
			//return error
			return $this->returnJsonResult($result);
		}

		$result['only_first_deposit']=$this->transactions->isOnlyFirstDeposit($player->playerId);
		$result['success']=true;

		return $this->returnJsonResult($result);
	}

	public function get_player_token($api_key){

		$this->load->model(['player_model', 'common_token']);

		$result=[];

		if(!$this->isValidApiKey($api_key)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found key');
			//return error
			return $this->returnJsonResult($result);
		}

	}


	public function manully_daily_add_promo($api_key, $cms_promo_code_or_cms_promo_id, $bonus_amount, $bet_times, $playerUsername, $state=null){

		return $this->manully_add_promo($api_key, 'daily', $cms_promo_code_or_cms_promo_id, $bonus_amount, $bet_times, $playerUsername, $state);
	}

	public function manully_any_add_promo($api_key, $cms_promo_code_or_cms_promo_id, $bonus_amount, $bet_times, $playerUsername, $state=null){
		return $this->manully_add_promo($api_key, 'any', $cms_promo_code_or_cms_promo_id, $bonus_amount, $bet_times, $playerUsername, $state);
	}

	public function manully_once_add_promo($api_key, $cms_promo_code_or_cms_promo_id, $bonus_amount, $bet_times, $playerUsername, $state=null){
		return $this->manully_add_promo($api_key, 'all', $cms_promo_code_or_cms_promo_id, $bonus_amount, $bet_times, $playerUsername, $state);
	}

	public function manully_add_promo($api_key, $period, $cms_promo_code_or_cms_promo_id, $bonus_amount, $bet_times, $playerUsername, $state=null){

		$this->utils->debug_log('manully_add_promo', $api_key, $period, $cms_promo_code_or_cms_promo_id, $bonus_amount, $bet_times, $playerUsername, $state);

		//search cms promo and promo
		$this->load->model(['promorules', 'player_model', 'common_token', 'player_promo']);
		list($promorule, $promoCmsSettingId)=$this->promorules->getByCmsPromoCodeOrId($cms_promo_code_or_cms_promo_id);
		$result=[];

		if(!$this->isValidApiKey($api_key)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found key');
			//return error
			return $this->returnJsonResult($result);
		}

		if(empty($promorule)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found promotion '.$cms_promo_code_or_cms_promo_id);
			//return error
			return $this->returnJsonResult($result);
		}

		//load player
		// $playerId=$this->authentication->getPlayerId();
		// if(empty($playerId) && !empty($player_token)){
			// $playerId=$this->common_token->getPlayerIdByToken($player_token);
		// }
		// $this->utils->debug_log('manully_add_promo get session player id', $playerId);

		$playerId=null;
		if(!empty($playerUsername)){
			$playerId=$this->player_model->getPlayerIdByUsername($playerUsername);
			// $this->utils->debug_log('manully_add_promo session', $this->session->all_userdata());
		}

		$player=null;
		//it's not empty
		if(!empty($playerId)){
			$player=$this->player_model->getPlayerArrayById($playerId);
		}

		if(empty($player)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found player');
			//return error
			return $this->returnJsonResult($result);
		}

		if($period=='any'){

		}else{

			$fromDatetime=null;
			$toDateTime=null;
			if($period=='daily'){
				//only daily
				$fromDatetime=$this->utils->getTodayForMysql().' 00:00:00';
				$toDateTime=$this->utils->getTodayForMysql().' 23:59:59';
			}

			$this->utils->debug_log('player id',$player['playerId'], 'promorule', $promorule['promorulesId'],
				'fromDatetime', $fromDatetime, 'toDateTime', $toDateTime);
			$cnt = $this->player_promo->countPlayerPromo($player['playerId'], $promorule['promorulesId'],
				$fromDatetime, $toDateTime);

			if($cnt>=1){
				$result['success']=false;
				$result['status']=403;
				$result['message']=lang('Already got promotion');
				//return error
				return $this->returnJsonResult($result);
			}
		}

		//apply promo
		//FIXME SHOULD CHECK
		//$this->promorules->checkAndProcessPromotion
		$controller=$this;
		$result['success']=$this->lockAndTransForPlayerBalance($player['playerId'], function()
			use ($controller, $player, $promorule, $promoCmsSettingId, $bonus_amount, $bet_times){

			return $controller->promorules->manuallyApplyPromo($player['playerId'], $promorule, $promoCmsSettingId, $bonus_amount, $bet_times);

		});


		$result['manully_add_promo']=true;


		return $this->returnJsonResult($result);
	}

	public function is_released_bonus($api_key, $username, $cms_promo_code_or_cms_promo_id, $period='all'){

		$result['success']=false;

		if(!$this->isValidApiKey($api_key)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found key');
			//return error
			return $this->returnJsonResult($result);
		}

		$this->load->model(['player_model', 'transactions', 'promorules', 'player_promo']);
		$player=null;
		if(!empty($username)){
			$player=$this->player_model->getPlayerByUsername($username);
		}

		if(empty($player)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found player');
			//return error
			return $this->returnJsonResult($result);
		}

		list($promorule, $promoCmsSettingId)=$this->promorules->getByCmsPromoCodeOrId($cms_promo_code_or_cms_promo_id);

		if(empty($promorule)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found promotion '.$cms_promo_code_or_cms_promo_id);
			//return error
			return $this->returnJsonResult($result);
		}

		$cnt = $this->player_promo->countPlayerPromo($player->playerId, $promorule['promorulesId'], null, null);

		$result['is_released_bonus']=$cnt>0;
		$result['success']=true;

		return $this->returnJsonResult($result);
	}

	public function dec_main_wallet($api_key, $external_tran_id, $username, $amount, $reason=null){

		$result['success']=false;

		if(!$this->isValidApiKey($api_key)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found key');
			//return error
			return $this->returnJsonResult($result);
		}

		if(empty($amount)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Wrong amount');
			//return error
			return $this->returnJsonResult($result);
		}

		$this->load->model(['player_model', 'wallet_model', 'transactions']);
		$player=null;
		if(!empty($username)){
			$player=$this->player_model->getPlayerByUsername($username);
		}

		if(empty($player)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found player');
			//return error
			return $this->returnJsonResult($result);
		}
		$playerId=$player->playerId;
		$controller=$this;


		$balance=null;
		$tran_id=null;
		$success=$this->wallet_model->lockAndTransForPlayerBalance($playerId, function()
			use ($controller,$external_tran_id, $playerId, $amount, $reason, &$balance, &$tran_id){

			//check main balance and dec
			$adminUserId=1;
			$note='player '.$playerId.' dec main balance : '.$amount.' '.$reason;
			$tran=$controller->transactions->createDecTransaction($playerId, $amount, $adminUserId, $note, $external_tran_id);
			$balance=$controller->wallet_model->getMainWalletBalance($playerId);

			$success= !!$tran;
			if($success){
				$tran_id=$tran['id'];
			}
			return $success;
		});

		$result['success']=$success;
		$result['main_balance']=$balance;
		$result['transaction_id']=$external_tran_id;

		return $this->returnJsonResult($result);

	}

	public function check_transaction($api_key, $external_tran_id){

		$result['success']=false;

		if(!$this->isValidApiKey($api_key)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found key');
			//return error
			return $this->returnJsonResult($result);
		}

		$this->load->model(['transactions']);

		$status=$this->transactions->checkStatusTransactionBy($external_tran_id);

		$result['success']=true;
		$result['status']= $status==Transactions::APPROVED ? 'approved' : 'none' ;
		$result['transaction_id']=$external_tran_id;

		return $this->returnJsonResult($result);

	}

	// public function add_promo_manually($player_token, $cms_promo_code_or_cms_promo_id, $playerUsername, $bonus_amount, $bet_times, $state=null){

	// 	return $this->add_promo_common(null, $player_token, 'manually', $cms_promo_code_or_cms_promo_id, $playerUsername, $bonus_amount, $bet_times, $state);
	// }

	public function is_applied_promo($player_token, $cms_promo_code_or_cms_promo_id, $playerUsername){
		return $this->is_applied_promo_common(null, $player_token, $mode, $cms_promo_code_or_cms_promo_id, $playerUsername);
	}

	public function is_applied_promo_api($api_key, $cms_promo_code_or_cms_promo_id, $playerUsername){
		return $this->is_applied_promo_common($api_key, null, $mode, $cms_promo_code_or_cms_promo_id, $playerUsername);
	}

	protected function is_applied_promo_common($api_key, $player_token, $mode, $cms_promo_code_or_cms_promo_id, $playerUsername){

		$this->utils->debug_log('is_applied_promo_common', $api_key, $player_token, $mode, $cms_promo_code_or_cms_promo_id, $playerUsername);

		//search cms promo and promo
		$this->load->model(['promorules', 'player_model', 'common_token', 'player_promo']);
		$result=[];

		$playerId=$this->player_model->getPlayerIdByUsername($playerUsername);

		if(empty($playerId)){
			$result['success']=false;
			$result['status']=403;
			$result['message']=lang('Invalid player, please login again');
			//return error
			return $this->returnJsonResult($result);
		}

		//key or token
		if(empty($api_key)){

			//check token
			if(!$this->isValidPlayerToken($player_token, $playerId)){
				$result['success']=false;
				$result['status']=403;
				$result['message']=lang('Not found token');
				//return error
				return $this->returnJsonResult($result);
			}

		}else{

			if(!$this->isValidApiKey($api_key)){
				$result['success']=false;
				$result['status']=403;
				$result['message']=lang('Not found key');
				//return error
				return $this->returnJsonResult($result);
			}
		}

		list($promorule, $promoCmsSettingId)=$this->promorules->getByCmsPromoCodeOrId($cms_promo_code_or_cms_promo_id);

		if(empty($promorule)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found promotion '.$cms_promo_code_or_cms_promo_id);
			//return error
			return $this->returnJsonResult($result);
		}

		$cnt = $this->player_promo->countPlayerPromo($playerId, $promorule['promorulesId'], null, null);

		$result['is_applied_promo']=$cnt>0;
		$result['count']=$cnt;
		$result['success']=true;
		$result['status']=200;

		return $this->returnJsonResult($result);

	}

	public function add_promo_auto($player_token, $cms_promo_code_or_cms_promo_id, $playerUsername){
		return $this->add_promo_common(null, $player_token, 'auto', $cms_promo_code_or_cms_promo_id, $playerUsername);
	}

	public function add_promo_manually_api($api_key, $cms_promo_code_or_cms_promo_id, $playerUsername, $bonus_amount, $bet_times, $state=null){
		return $this->add_promo_common($api_key, null, 'manually', $cms_promo_code_or_cms_promo_id, $playerUsername, $bonus_amount, $bet_times, $state);
	}

	public function add_promo_auto_api($api_key, $cms_promo_code_or_cms_promo_id, $playerUsername){
		return $this->add_promo_common($api_key, null, 'auto', $cms_promo_code_or_cms_promo_id, $playerUsername);
	}

	protected function add_promo_common($api_key, $player_token, $mode, $cms_promo_code_or_cms_promo_id, $playerUsername, $bonus_amount=null, $bet_times=null, $state=null){

		$this->utils->debug_log('add_promo_common', $api_key, $player_token, $mode, $cms_promo_code_or_cms_promo_id, $bonus_amount, $bet_times, $playerUsername, $state);

		//search cms promo and promo
		$this->load->model(['promorules', 'player_model', 'common_token', 'player_promo']);
		$result=[];

		$playerId=$this->player_model->getPlayerIdByUsername($playerUsername);

		if(empty($playerId)){
			$result['success']=false;
			$result['status']=403;
			$result['message']=lang('Invalid player, please login again');
			//return error
			return $this->returnJsonResult($result);
		}

		//key or token
		if(empty($api_key)){

			//check token
			if(!$this->isValidPlayerToken($player_token, $playerId)){
				$result['success']=false;
				$result['status']=403;
				$result['message']=lang('Not found token');
				//return error
				return $this->returnJsonResult($result);
			}

		}else{

			if(!$this->isValidApiKey($api_key)){
				$result['success']=false;
				$result['status']=403;
				$result['message']=lang('Not found key');
				//return error
				return $this->returnJsonResult($result);
			}
		}

		list($promorule, $promoCmsSettingId)=$this->promorules->getByCmsPromoCodeOrId($cms_promo_code_or_cms_promo_id);

		if(empty($promorule)){
			$result['success']=false;
			$result['status']=404;
			$result['message']=lang('Not found promotion '.$cms_promo_code_or_cms_promo_id);
			//return error
			return $this->returnJsonResult($result);
		}

		//load player
		// $playerId=$this->authentication->getPlayerId();
		// if(empty($playerId) && !empty($player_token)){
			// $playerId=$this->common_token->getPlayerIdByToken($player_token);
		// }
		// $this->utils->debug_log('manully_add_promo get session player id', $playerId);

		// $playerId=null;
		// if(!empty($playerUsername)){
		// 	$playerId=$this->player_model->getPlayerIdByUsername($playerUsername);
		// 	// $this->utils->debug_log('manully_add_promo session', $this->session->all_userdata());
		// }

		// $player=null;
		// //it's not empty
		// if(!empty($playerId)){
			// $player=$this->player_model->getPlayerArrayById($playerId);
		// }

		// if(empty($player)){
		// 	$result['success']=false;
		// 	$result['status']=404;
		// 	$result['message']=lang('Not found player');
		// 	//return error
		// 	return $this->returnJsonResult($result);
		// }

		// if($period=='any'){

		// }else{

		// 	$fromDatetime=null;
		// 	$toDateTime=null;
		// 	if($period=='daily'){
		// 		//only daily
		// 		$fromDatetime=$this->utils->getTodayForMysql().' 00:00:00';
		// 		$toDateTime=$this->utils->getTodayForMysql().' 23:59:59';
		// 	}

		// 	$this->utils->debug_log('player id',$player['playerId'], 'promorule', $promorule['promorulesId'],
		// 		'fromDatetime', $fromDatetime, 'toDateTime', $toDateTime);
		// 	$cnt = $this->player_promo->countPlayerPromo($player['playerId'], $promorule['promorulesId'],
		// 		$fromDatetime, $toDateTime);

		// 	if($cnt>=1){
		// 		$result['success']=false;
		// 		$result['status']=403;
		// 		$result['message']=lang('Already got promotion');
		// 		//return error
		// 		return $this->returnJsonResult($result);
		// 	}
		// }

		//apply promo
		//FIXME SHOULD CHECK
		//$this->promorules->checkAndProcessPromotion

		if($mode=='manually'){

			$controller=$this;
			$result['success']=$this->lockAndTransForPlayerBalance($playerId, function()
				use ($controller, $playerId, $promorule, $promoCmsSettingId, $bonus_amount, $bet_times){

				return $controller->promorules->manuallyApplyPromo($playerId, $promorule, $promoCmsSettingId, $bonus_amount, $bet_times);

			});
			$result['status']=200;

		}else{
			//auto
			$controller=$this;
			$message=null;
			$result['success']=$this->lockAndTransForPlayerBalance($playerId, function()
				use ($controller, $playerId, $promorule, $promoCmsSettingId, &$message){

				// $playerId, $promorule, $promoCmsSettingId,
				// $preapplication=false, $playerPromoId=null, &$extra_info=null, $triggerEvent=null, $dry_run
				$preapplication=false;
				$playerPromoId=null;
				$extra_info=['debug_log'=>''];
				$triggerEvent=null;
				$dry_run=false;
				list($success, $message)= $controller->promorules->checkAndProcessPromotion($playerId, $promorule, $promoCmsSettingId,
					$preapplication, $playerPromoId, $extra_info, $triggerEvent, $dry_run);

				$this->utils->debug_log('apply promo', $extra_info, $success, $message);

				return $success;
			});

			if($result['success']){
				$result['status']=200;
				$result['message']=lang($message);
			}else{
				$result['status']=400;
				$result['message']=lang($message);
			}

		}

		return $this->returnJsonResult($result);
	}

}
