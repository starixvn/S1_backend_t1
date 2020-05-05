<?php
trait lock_app_module {

	protected function createLockKey($arr) {
		return $this->utils->createLockKey($arr);
	}

	// protected function lockTransferSubwallet($playerId, $gamePlatformId, $lock_type) {
	// 	// $this->load->model('player_model');
	// 	// $trans_key = $this->createLockKey(array($playerId, $gamePlatformId, $lock_type));
	// 	// // lock it
	// 	// $lock_it = $this->player_model->transGetLock($trans_key);

	// 	// return $lock_it;
	// 	return $this->lockActionById($playerId, Utils::LOCK_ACTION_BALANCE);
	// }

	// protected function releaseTransferSubwallet($playerId, $gamePlatformId, $lock_type) {
	// 	// $this->load->model('player_model');
	// 	// $trans_key = $this->createLockKey(array($playerId, $gamePlatformId, $lock_type));
	// 	// $rlt = $this->player_model->transReleaseLock($trans_key);
	// 	// return $rlt;
	// 	return $this->releaseActionById($playerId, Utils::LOCK_ACTION_BALANCE);
	// }

	// // const LOCK_ACTION_WITHDRAWAL = 'player_withdrawal';
	// // const LOCK_ACTION_BALANCE = 'balance';

	// protected function lockWithdrawal($playerId) {

	// 	return $this->lockActionById($playerId, Utils::LOCK_ACTION_BALANCE);
	// 	// $this->load->model('player_model');
	// 	// $lock_type = 'withdrawal';
	// 	// $trans_key = $this->createLockKey(array($playerId, $lock_type));
	// 	// // lock it
	// 	// $lock_it = $this->player_model->transGetLock($trans_key);
	// 	// return $lock_it;
	// }

	// protected function releaseWithdrawal($playerId) {
	// 	return $this->releaseActionById($playerId, Utils::LOCK_ACTION_BALANCE);

	// 	// $this->load->model('player_model');
	// 	// $lock_type = 'withdrawal';
	// 	// $trans_key = $this->createLockKey(array($playerId, $lock_type));
	// 	// $rlt = $this->player_model->transReleaseLock($trans_key);
	// 	// return $rlt;
	// }

	protected function lockActionById($anyId, $action) {
		return $this->utils->lockActionById($anyId, $action);
	}

	protected function releaseActionById($anyId, $action) {
		return $this->utils->releaseActionById($anyId, $action);
	}

	// protected function lockPlayerBalance($playerId) {
	// 	return $this->lockActionById($playerId, Utils::LOCK_ACTION_BALANCE);
	// }

	// protected function releasePlayerBalance($playerId) {
	// 	return $this->releaseActionById($playerId, Utils::LOCK_ACTION_BALANCE);
	// }


	public function startTrans() {
		if (!property_exists($this, 'player_model')) {
			$this->load->model('player_model');
		}

		return $this->player_model->startTrans();
	}

	public function endTransWithSucc() {
		if (!property_exists($this, 'player_model')) {
			$this->load->model('player_model');
		}

		return $this->player_model->endTransWithSucc();
	}

	public function rollbackTrans() {
		if (!property_exists($this, 'player_model')) {
			$this->load->model('player_model');
		}

		return $this->player_model->rollbackTrans();
	}

	public function lockAndTransForAgencyCredit($agentId, $callbakcable) {
		return $this->lockAndTrans(Utils::LOCK_ACTION_AGENCY_BALANCE, $agentId, $callbakcable);
	}

	public function lockAndTransForAgencyBalance($agentId, $callbakcable) {
		return $this->lockAndTrans(Utils::LOCK_ACTION_AGENCY_BALANCE, $agentId, $callbakcable);
	}

	public function lockAndTransForPlayerBalance($playerId, $callbakcable, $add_prefix=true, &$isLockFailed=false) {
		return $this->lockAndTrans(Utils::LOCK_ACTION_BALANCE, $playerId, $callbakcable, $add_prefix, $isLockFailed);
	}

	public function lockAndTransForPlayerPromo($playerPromoId, $callbakcable) {
		return $this->lockAndTrans(Utils::LOCK_ACTION_PLAYER_PROMO, $playerPromoId, $callbakcable);
	}

	public function lockAndTransForAffiliateBalance($affId, $callbakcable) {
		return $this->lockAndTrans(Utils::LOCK_ACTION_AFF_BALANCE, $affId, $callbakcable);
	}

	public function lockAndTransForDepositLock($salesOrderId, $callbakcable) {
		return $this->lockAndTrans(Utils::LOCK_ACTION_DEPOSIT_LOCK, $salesOrderId, $callbakcable);
	}

    public function lockAndTransForWithdrawLock($walletAccountId, $callbakcable) {
        return $this->lockAndTrans(Utils::LOCK_ACTION_WITHDRAW_LOCK, $walletAccountId, $callbakcable);
    }

	public function lockResourceBy($anyId, $action, &$lockedKey) {
		return $this->utils->lockResourceBy($anyId, $action, $lockedKey);
	}

	public function releaseResourceBy($anyId, $action, &$lockedKey) {
		return $this->utils->releaseResourceBy($anyId, $action, $lockedKey);
	}

	public function lockPlayerBalanceResource($playerId, &$lockedKey){
		return $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
	}

	public function releasePlayerBalanceResource($playerId, &$lockedKey){
		return $this->utils->releaseResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
	}

	public function lockAndTrans($lock_type, $anyid, $callbakcable, $add_prefix=true, &$isLockFailed=false) {
		$success = false;
		$lockedKey = null;
		$lock_it = $this->utils->lockResourceBy($anyid, $lock_type, $lockedKey, $add_prefix);
		try {
			if ($lock_it) {
				$this->startTrans();

				$success = $callbakcable();

				if(!$success){
					//rollback
					$this->rollbackTrans();
					$this->utils->error_log('rollback trans because failed');
				} else {
					$success = $this->endTransWithSucc();
				}

			} else {
				$isLockFailed=true;
				$retryTime=$this->utils->getConfig('lock_retry_delay')*20;
				$timeout = $this->utils->getConfig('app_lock_timeout') * 1000;
				$this->utils->error_log('lock failed by retryTime: '.$retryTime, 'timeout: '.$timeout, $lock_type, $anyid);
			}
		} finally {
			if(!empty($lockedKey)){
				$rlt = $this->utils->releaseResourceBy($anyid, $lock_type, $lockedKey);
				if(!$rlt){
					$this->utils->error_log('release failed', $lock_type, $anyid, $lockedKey);
				}
			} else {
				$this->utils->info_log('cannot release empty key', $lock_type, $anyid, $lockedKey);
			}
		}
		return $success;
	}

	public function dbtransOnly($callbakcable) {
		$success = false;
		$this->startTrans();
		try {

			$success = $callbakcable();

			if(!$success){
				//rollback
				$this->rollbackTrans();
				$this->utils->error_log('rollback trans because failed');
			}else{
				$success = $this->endTransWithSucc();
			}

		}catch(Exception $e){
			$this->utils->error_log('throw exception on trans', $e);
			$success=false;
			//rollback
			$this->rollbackTrans();
		}
		return $success;
	}

	public function lockAndTransForPlayerBalanceAndAgencyCredit($playerId, $agentId, $callbakcable) {

		$lock_array=[
			['lock_type'=>Utils::LOCK_ACTION_BALANCE, 'anyid'=>$playerId],
			['lock_type'=>Utils::LOCK_ACTION_AGENCY_BALANCE, 'anyid'=>$agentId],
		];

		return $this->multipleLockAndTrans($lock_array, $callbakcable);
	}

	public function multipleLockAndTrans($lock_array, $callbakcable) {

		$success = false;

		if(empty($lock_array) || empty($callbakcable)){
			return $success;
		}

		$locked_all=false;
		$lock_it=[];
		foreach ($lock_array as &$lock_info) {

			$key=$this->utils->createLockKey([$lock_info['anyid'], $lock_info['lock_type']]);

			$lock_it[$key] = $this->utils->lockResourceBy($lock_info['anyid'], $lock_info['lock_type'], $lockedKey);

			$lock_info['lockedKey']=$lockedKey;
			// $lock_it[$key] = $this->utils->lockActionById($lock_info['anyid'], $lock_info['lock_type']);
			if(!$lock_it[$key]){
				$locked_all=false;
			}else{
				$locked_all=true;
			}

		}

		$this->utils->debug_log('try lock', $lock_array, 'result', $lock_it);

		try {

			if ($locked_all) {

				$success = $callbakcable();

			} else {

				$this->utils->error_log('lock failed', $lock_array);

			}
		} finally {

			for($i=count($lock_array)-1;$i>=0;$i--){
				$lock_info=$lock_array[$i];
			// foreach ($lock_array as $lock_info) {

				$lockedKey=isset($lock_info['lockedKey']) ? $lock_info['lockedKey'] : null;
				if(!empty($lockedKey)){
					$rlt = $this->utils->releaseResourceBy($lock_info['anyid'], $lock_info['lock_type'], $lockedKey);
					if(!$rlt){
						$this->utils->error_log('release failed', $lock_info);
					}
				}
				// $this->utils->releaseActionById($lock_info['anyid'], $lock_info['lock_type']);
			}
			//don't care about release result

		}
		return $success;
	}

	public function localLockResourceBy($anyId, $action, &$lockedKey) {
		return $this->utils->localLockResourceBy($anyId, $action, $lockedKey);
	}

	public function localReleaseResourceBy($anyId, $action, &$lockedKey) {
		return $this->utils->localReleaseResourceBy($anyId, $action, $lockedKey);
	}

	public function localLockOnlyWithResult($lock_type, $anyid, callable $callbakcable) {
		$success = false;
		$error=null;
		$lockedKey=null;
		// $lock_type = Utils::LOCK_ACTION_AFF_BALANCE;
		$lock_it = $this->utils->localLockResourceBy($anyid, $lock_type, $lockedKey);
		// $this->utils->debug_log('lock aff', $anyid, 'amount', $amount);
		try {
			if ($lock_it) {

				$success = $callbakcable($error);

			} else {
				$timeout = $this->utils->getConfig('app_lock_timeout') * 1000;
				$this->utils->error_log('lock failed by timeout :'.$timeout, $lock_type, $anyid);
				$error='Lock failed';
			}
		} finally {
			if(!empty($lockedKey)){
				$rlt = $this->utils->localReleaseResourceBy($anyid, $lock_type, $lockedKey);
				if(!$rlt){
					// $error='Release key failed';
					$this->utils->error_log('release failed', $lock_type, $anyid, $lockedKey);
				}
			}else{
				$this->utils->info_log('cannot release empty key', $lock_type, $anyid, $lockedKey);
			}
		}

		$result['success']=$success;
		$result['error']=$error;

		return $result;
	}

	public function isResourceInsideLock($anyId, $action){
		return $this->utils->isResourceInsideLock($anyId, $action);
	}

}

///END OF FILE/////////
