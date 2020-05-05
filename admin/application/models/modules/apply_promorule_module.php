<?php

/**
 * Class apply_promorule_module
 *
 * General behaviors include :
 *
 * * Get promo details ( request, approve, decline, finish promo )
 * * Approve promo
 * * Checking if promo is deposit/non deposit, transfer, betting etc.
 * * Get available deposit transaction
 * * Validate customized condition
 * * Get promo rules
 * * Releasing of random bonus
 * * Checking and process promotion
 * * Trigger promotions from admin/player
 * * Update current daily bonus
 *
 * @category Marketing Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait apply_promorule_module {

	/**
	 * overview : check if non deposit promo exist
	 * @param int	$playerId
	 * @param int	$nonDepositPromoType
	 * @return bool
	 */
	public function isPlayerNonDepositPromoExist($playerId, $nonDepositPromoType) {
		$this->db->select('promorules.nonDepositPromoType')->from('playerpromo');
		$this->db->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left');
		$this->db->where('playerpromo.playerId', $playerId);
		$this->db->where('promorules.nonDepositPromoType', $nonDepositPromoType);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * overview : check if repeatable
	 *
	 * @param string	$promorule
	 * @return bool
	 */
	public function isRepeatable($promorule) {
		return $promorule['bonusApplicationLimitRule'] == self::BONUS_APPLICATION_LIMIT_RULE_NO_LIMIT ||
			($promorule['bonusApplicationLimitRule'] == self::BONUS_APPLICATION_LIMIT_RULE_LIMIT_COUNT &&
			$promorule['bonusApplicationLimitRuleCnt'] > 1);
	}

	/**
	 * overview : get withdraw amount
	 *
	 * @param int	$promorule
	 * @param int	$playerBonusAmount
	 * @param int   $depositAmount
	 * @param int	$playerId
	 * @param int	$tranId
	 * @param int $betTimes
	 * @return int|null
	 */
	public function getWithdrawCondAmount($promorule, $playerBonusAmount, $depositAmount, $playerId,
			$tranId, &$betTimes=null, &$extra_info=null, $dry_run=false) {
		$this->load->model(['withdraw_condition']);

		//nothing default
		$withdrawBetAmtCondition = 0;
		$playerBonusAmountRelate = 0;
		switch ($promorule['withdrawRequirementConditionType']) {
		case self::WITHDRAW_CONDITION_TYPE_FIXED_AMOUNT:
			$withdrawBetAmtCondition = $promorule['withdrawRequirementBetAmount'];
			break;
		case self::WITHDRAW_CONDITION_TYPE_BETTING_TIMES:

			$non_promo_withdraw_setting = $this->operatorglobalsettings->getSettingDoubleValue('non_promo_withdraw_setting');
			$betTimes = $promorule['withdrawRequirementBetCntCondition'];

			$withdrawBetAmtCondition = ($playerBonusAmount + $depositAmount) * $betTimes;

			if( $promorule['withdrawShouldMinusDeposit'] > 0 ){
				$withdrawBetAmtCondition = $withdrawBetAmtCondition - ($depositAmount * $non_promo_withdraw_setting);
			}

			break;
        case self::WITHDRAW_CONDITION_TYPE_BETTING_TIMES_CHECK_WITH_MAX_BONUS:
            $betTimes = $promorule['withdrawRequirementBetCntCondition'];
            $depositPercentage = $promorule['depositPercentage'] / 100;
            $depAmtLessThanOrEqualToMaxBonusDividedByBonusPercent = $depositAmount <= ($promorule['maxBonusAmount'] / $depositPercentage);
            $depAmtGreaterThanMaxBonusDividedByBonusPercent = $depositAmount > ($promorule['maxBonusAmount'] / $depositPercentage);

            if($depAmtLessThanOrEqualToMaxBonusDividedByBonusPercent){
                $withdrawBetAmtCondition = ($depositAmount + $playerBonusAmount) * $betTimes;
            }

            if($depAmtGreaterThanMaxBonusDividedByBonusPercent){
                $withdrawBetAmtCondition = ( ( ($playerBonusAmount / $depositPercentage) + $promorule['maxBonusAmount'] ) * $betTimes )
                                         + ( $depositAmount - ($promorule['maxBonusAmount'] / $depositPercentage) );
            }

            break;
		case self::WITHDRAW_CONDITION_TYPE_BONUS_TIMES:

			$betTimes = $promorule['withdrawRequirementBetCntCondition'];

			$withdrawBetAmtCondition = $playerBonusAmount * $betTimes;

			break;
		case self::WITHDRAW_CONDITION_TYPE_CUSTOM:
			//run js
			$formula = $promorule['formula'];
            $errorMessageLang = null;
			$this->utils->debug_log('formula', $formula);
			if (!empty($formula)) {
				//convert to json
				$formula = json_decode($formula, true);
				if (isset($formula['withdraw_condition']) && !empty($formula['withdraw_condition'])) {
					$js = $formula['withdraw_condition'];
					$desc_class=$this->utils->decodeJson($js);

					if($desc_class!==null){
						//is json format, try load class
						$ruleObj=$this->loadCustomizedPromoRuleObject($extra_info, $desc_class, $playerId, $promorule, $playerBonusAmount, $depositAmount);
						$result=$ruleObj->run('generateWithdrawalCondition', $desc_class, $extra_info, $dry_run);
						if(isset($result['unimplemented']) && $result['unimplemented']){
							$this->utils->error_log('unimplemented promo rules', $desc_class);
						}else if($result['success']){
							$withdrawBetAmtCondition=$result['withdrawal_condition_amount'];
						}else{
							$this->utils->error_log('promo rules error on withdraw condition', $result);
						}
					}else{
						// Not JSON format (plain js code instead)
						// try run js
						$runtime = $this->loadRuntime($playerId, $promorule, $playerBonusAmount, $depositAmount);
						$withdrawBetAmtCondition = $runtime->runjs($js, $extra_info, $dry_run);
						// $result won't be available here
					}

					if(is_array($withdrawBetAmtCondition)){
                        $withdrawBetAmtCondition = (isset($withdrawBetAmtCondition['withdrawal_condition_amount'])) ? $withdrawBetAmtCondition['withdrawal_condition_amount'] : $playerBonusAmount;

                        if(isset($withdrawBetAmtCondition['errorMessageLang'])){
                            $errorMessageLang = $withdrawBetAmtCondition['errorMessageLang'];
                        }else{
                            if(empty($withdrawBetAmtCondition['withdrawal_condition_amount'])){
                                $errorMessageLang = 'Invalid Withdrawal Condition Amount';
                            }
                        }

                    }

                    $this->appendToExtraInfoDebugLog($extra_info, 'playerBonusAmount: '.$playerBonusAmount.', withdrawal_condition_amount: '.$withdrawBetAmtCondition,
                        ['errorMessageLang'=>$errorMessageLang, 'result' => isset($result) ? $result : null ]);
				}
			}
			break;
		}

		return $withdrawBetAmtCondition;
	}

    /**
     * overview : get transfer condition amount
     *
     * @param int	$promorule
     * @param int	$playerBonusAmount
     * @param int $betTimes
     * @return int|0
     */
    public function getTransferCondAmount($promorule, $playerBonusAmount, &$transferBetTimes=null, $depositAmount, $playerId, &$extra_info=null, $dry_run=false) {
        $transferBetAmtCondition = 0;
        switch ($promorule['transferRequirementConditionType']) {
            case self::TRANSFER_CONDITION_TYPE_FIXED_AMOUNT:
                $transferBetAmtCondition = $promorule['transferRequirementBetAmount'];
                break;
            case self::TRANSFER_CONDITION_TYPE_BETTING_TIMES:
                $non_promo_withdraw_setting = $this->operatorglobalsettings->getSettingDoubleValue('non_promo_withdraw_setting');
                $transferBetTimes = $promorule['transferRequirementBetCntCondition'];

                $transferBetAmtCondition = ($playerBonusAmount + $depositAmount) * $transferBetTimes;

                if( $promorule['transferShouldMinusDeposit'] > 0 ){
                    $transferBetAmtCondition = $transferBetAmtCondition - ($depositAmount * $non_promo_withdraw_setting);
                }

                break;
            case self::TRANSFER_CONDITION_TYPE_BONUS_TIMES:
                $transferBetTimes = $promorule['transferRequirementBetCntCondition'];
                $transferBetAmtCondition = $playerBonusAmount * $transferBetTimes;
                break;
            case self::TRANSFER_CONDITION_TYPE_CUSTOM:
                //run js
                $formula = $promorule['formula'];
                $errorMessageLang = null;
                $result = null;
                $this->utils->debug_log('formula', $formula);
                if (!empty($formula)) {
                    //convert to json
                    $formula = json_decode($formula, true);
                    if (isset($formula['transfer_condition']) && !empty($formula['transfer_condition'])) {
                        $js = $formula['transfer_condition'];
                        $desc_class=$this->utils->decodeJson($js);
                        if($desc_class!==null){
                            //is json format, try load class
                            $ruleObj=$this->loadCustomizedPromoRuleObject($extra_info, $desc_class, $playerId, $promorule, $playerBonusAmount, $depositAmount);
                            $result=$ruleObj->run('generateTransferConditionAmount', $desc_class, $extra_info, $dry_run);
                            if(isset($result['unimplemented']) && $result['unimplemented']){
                                $this->utils->error_log('unimplemented promo rules', $desc_class);
                            }else if($result['success']){
                                $transferBetAmtCondition=$result['transfer_condition_amount'];
                            }else{
                                $this->utils->error_log('promo rules error on transfer condition', $result);
                            }
                        }else{
                            //try run js
                            $runtime = $this->loadRuntime($playerId, $promorule, $playerBonusAmount, $depositAmount);
                            $result = $runtime->runjs($js, $extra_info, $dry_run);
                        }

                        if(is_array($result)){
                            $transferBetAmtCondition = (isset($result['transfer_condition_amount'])) ? $result['transfer_condition_amount'] : $playerBonusAmount;
                            if(isset($result['errorMessageLang'])){
                                $errorMessageLang = $result['errorMessageLang'];
                            }else{
                                if(empty($result['transfer_condition_amount'])){
                                    $errorMessageLang = 'Invalid Transfer Condition Amount';
                                }
                            }
                        }

                        $this->appendToExtraInfoDebugLog($extra_info, 'playerBonusAmount:'.$playerBonusAmount.'transferBetAmtCondition'.$transferBetAmtCondition,
                            ['errorMessageLang'=>$errorMessageLang, 'result'=>$result]);
                    }
                }
                break;
        }
        return $transferBetAmtCondition;
    }

	/**
	 * overview : get bonus amount
	 * @param $promorule
	 * @param $depositAmount
	 * @param $playerId
	 * @param null $errorMessageLang
	 * @return array
	 */
	public function getBonusAmount($promorule, $depositAmount, $playerId, &$errorMessageLang = null, &$extra_info=null, $dry_run=false) {

		$this->utils->debug_log('bonusReleaseRule', $promorule['bonusReleaseRule'], 'playerId', $playerId, 'depositAmount', $depositAmount);

		$playerBonusAmount = 0;
		switch ($promorule['bonusReleaseRule']) {
			case self::BONUS_RELEASE_RULE_FIXED_AMOUNT:
				$playerBonusAmount = $promorule['bonusAmount'];
				break;
			case self::BONUS_RELEASE_RULE_DEPOSIT_PERCENTAGE:
				$playerBonusAmount = $depositAmount * ($promorule['depositPercentage'] / 100);

				//check by cycle
				if($promorule['max_bonus_by_limit_date_type']=='1'){

					list($limitFrom, $limitTo) = $this->utils->getLimitDateRangeForPromo($promorule['bonusApplicationLimitDateType']);

					if(!empty($limitFrom) && !empty($limitTo)){
						//monthly, weekly
						//try sum bonus
						$promorulesId=$promorule['promorulesId'];
						$this->load->model(['player_promo']);
						$paidBonus=$this->player_promo->sumBonusAmount($playerId, $promorulesId,
							$limitFrom, $limitTo);

						$this->appendToDebugLog($extra_info['debug_log'], 'paidBonus:'.$paidBonus.
							', playerBonusAmount:'.$playerBonusAmount.
							', maxBonusAmount:'.$promorule['maxBonusAmount'],
							['bonusApplicationLimitDateType'=>$promorule['bonusApplicationLimitDateType'],
							'limitFrom'=>$limitFrom, 'limitTo'=>$limitTo]);
						//more than max bonus amount
						if ($playerBonusAmount+$paidBonus >= $promorule['maxBonusAmount']) {
							$playerBonusAmount = $promorule['maxBonusAmount']-$paidBonus;
						}
						if($this->utils->roundCurrency($playerBonusAmount)<=0){
							$playerBonusAmount=0;
                            $errorMessageLang = 'notify.114';
						}

					}else{
						//no limit
						if ($playerBonusAmount >= $promorule['maxBonusAmount']) {
							$playerBonusAmount = $promorule['maxBonusAmount'];
						}
					}
				}else{

					if ($playerBonusAmount >= $promorule['maxBonusAmount']) {
						$playerBonusAmount = $promorule['maxBonusAmount'];
					}

				}
				break;
			case self::BONUS_RELEASE_RULE_BET_PERCENTAGE:
				//TODO get from to date
				$from_datetime = null;
				$to_datetime = null;
				break;
			case self::BONUS_RELEASE_RULE_CUSTOM:
				//run js
				$formula = $promorule['formula'];
				$errorMessageLang = 'promo_rule.common.error';
				if (!empty($formula)) {
					//convert to json
					$formula = json_decode($formula, true);
					if (isset($formula['bonus_release']) && !empty($formula['bonus_release'])) {
						$js = $formula['bonus_release'];
						$desc_class=$this->utils->decodeJson($js);
						$rlt=null;
						if($desc_class!==null){
							//is json format, try load class
							$ruleObj=$this->loadCustomizedPromoRuleObject($extra_info, $desc_class, $playerId, $promorule, null, $depositAmount);
							$result=$ruleObj->run('releaseBonus', $desc_class, $extra_info, $dry_run);

							$this->appendToExtraInfoDebugLog($extra_info, 'promo rule:',
								['result'=>$result, 'dry_run'=>$dry_run, 'desc_class'=>$desc_class]);

							if(isset($result['unimplemented']) && $result['unimplemented']){
								$rlt=['errorMessageLang'=>'Unimplemented'];
							}else if($result['success']){
								$rlt=['bonus_amount'=>$result['bonus_amount'], 'errorMessageLang'=>''];
							}else{
								$rlt=['errorMessageLang'=>$result['message']];
							}
						}else{
							//try run js
							$runtime = $this->loadRuntime($playerId, $promorule, null, $depositAmount);

							$rlt = $runtime->runjs($js, $extra_info, $dry_run);
						}
						$playerBonusAmount = (isset($rlt['bonus_amount'])) ? $rlt['bonus_amount'] : $playerBonusAmount;
						$errorMessageLang = (isset($rlt['errorMessageLang'])) ? $rlt['errorMessageLang'] : $errorMessageLang;
						$this->appendToExtraInfoDebugLog($extra_info, 'playerBonusAmount:'.$playerBonusAmount,
							['errorMessageLang'=>$errorMessageLang, 'rlt'=>$rlt]);
					}
				}
				break;
			case self::BONUS_RELEASE_RULE_BONUS_GAME:
				$playerBonusAmount = 1;
				break;
		}

		return $this->utils->roundCurrency($playerBonusAmount, 2);
	}

	/**
	 * overview : request bonus
	 * @param $playerId
	 * @param $promorule
	 * @param $depositAmount
	 * @param $promoCmsSettingId
	 * @param null $adminId
	 * @param null $tranId
	 * @param bool|true $checkBonusAmount
	 * @param null $playerPromoId
	 * @param null $reason
	 * @return null
	 * @throws WrongBonusException
	 */
	public function requestPromo($playerId, $promorule, $depositAmount, $promoCmsSettingId,
		$adminId = null, $tranId = null, $checkBonusAmount=true, $playerPromoId=null, $reason=null, &$extra_info=null, $dry_run=false) {
		$this->load->model(array('player_promo','withdraw_condition', 'transactions', 'sale_order'));

		$promorulesId = $promorule['promorulesId'];

		$errorMessageLang=null;
		$playerBonusAmount = $this->getBonusAmount($promorule, $depositAmount, $playerId, $errorMessageLang, $extra_info, $dry_run);
		if($checkBonusAmount){
			if ($playerBonusAmount <= 0 || $playerBonusAmount == null) {
				throw new WrongBonusException($errorMessageLang, 'wrong bonus amount:' . $playerBonusAmount);
			}
		}

		$withdrawBetAmtCondition = $this->getWithdrawCondAmount($promorule, $playerBonusAmount,
			$depositAmount, $playerId, $tranId, $betTimes, $extra_info, $dry_run);

		$transferBetAmtCondition = $this->getTransferCondAmount($promorule, $playerBonusAmount,
            $transferBetTimes, $depositAmount, $playerId, $extra_info, $dry_run);

		if($dry_run){
			$this->appendToDebugLog($extra_info['debug_log'], 'dry run, will ignore requestPromo',
				['promorulesId'=>$promorulesId, 'playerBonusAmount'=>$playerBonusAmount,
				'withdrawBetAmtCondition'=>$withdrawBetAmtCondition, 'transferBetAmtCondition'=>$transferBetAmtCondition]);
			return null;
		}

        $playerPromoData = [];
        if($promorule['release_to_same_sub_wallet']=='1'){
            //set to same sub-wallet
            $subWalletId= @$extra_info['subWalletId'];
            $trigger_wallets=$promorule['trigger_wallets'];
            $trigger_wallets_arr=[];
            if(!empty($trigger_wallets)){
                $trigger_wallets_arr=explode(',',$trigger_wallets);
            }
            //trigger on sub wallet
            if(!empty($subWalletId) && (!empty($trigger_wallets_arr) && in_array($subWalletId, $trigger_wallets_arr))){
                $promorule['releaseToSubWallet']=$subWalletId;
                $playerPromoData['triggered_subwallet_id'] = $subWalletId;
            }
        }

		$playerPromoId = $this->player_promo->requestPromoToPlayer($playerId, $promorulesId,
			$playerBonusAmount, $promoCmsSettingId, $adminId, $depositAmount, $withdrawBetAmtCondition,
			Player_promo::TRANS_STATUS_REQUEST, $betTimes, $reason, $playerPromoId, $transferBetAmtCondition);

        $this->player_promo->updatePlayerPromo($playerPromoId, $playerPromoData);
		$this->updatePromoId($promorule, $tranId, $playerPromoId, $extra_info);

		return $playerPromoId;
	}

	/**
	 * overview : approve promo
	 *
	 * @param $playerId
	 * @param $promorule
	 * @param $promoCmsSettingId
	 * @param $adminId
	 * @param null $depositAmount
	 * @param null $tranId
	 * @param null $playerPromoId
	 * @param null $extra_info
	 * @param null $reason
	 * @return null
	 */
	public function approvePromo($playerId, $promorule, $promoCmsSettingId, $adminId,
		$depositAmount = null, $tranId = null, $playerPromoId = null, &$extra_info=null, $reason=null, $dry_run=false) {
		$this->load->model(array('wallet_model', 'transactions', 'player_promo', 'sale_order', 'withdraw_condition', 'player_model', 'transfer_condition'));

		$promorulesId = $promorule['promorulesId'];
		$depositTransId=$tranId;

		$playerPromoTriggeredSubWalletId = 0;
		$playerBonusAmount = 0;
		$withdrawBetAmtCondition=0;
        $transferBetAmtCondition=0;
		$betTimes=0;
		$reason=null;
		if (!empty($playerPromoId)) {
			//check status first
			$playerPromo = $this->player_promo->getPlayerPromo($playerPromoId);
			if (!empty($playerPromo)) {
				if($playerPromo->transactionStatus==Player_promo::TRANS_STATUS_APPROVED){
					$this->utils->debug_log('ignore approve promo because status', $playerPromoId);
					return $playerPromoId;
				}

				$playerBonusAmount = $playerPromo->bonusAmount;
				$withdrawBetAmtCondition = $playerPromo->withdrawConditionAmount;
				$transferBetAmtCondition = $playerPromo->transferConditionAmount;
                $playerPromoTriggeredSubWalletId = $playerPromo->triggered_subwallet_id;
			} else {
				$playerPromoId = null;
			}
		}

		//overwrite bonus amount
		if(isset($extra_info['bonusAmount']) && !empty($extra_info['bonusAmount'])){
			$playerBonusAmount=$extra_info['bonusAmount'];
			$withdrawBetAmtCondition = $this->getWithdrawCondAmount($promorule, $playerBonusAmount,
				$depositAmount, $playerId, $tranId, $betTimes, $extra_info, $dry_run);
		}

		if (empty($playerBonusAmount)) {

			$playerBonusAmount = $this->getBonusAmount($promorule, $depositAmount, $playerId, $errorMessageLang, $extra_info, $dry_run);
			if(!empty($errorMessageLang)){
				$this->utils->error_log('approvePromoWithoutRelease', $errorMessageLang);
			}

		}

		if (empty($withdrawBetAmtCondition)) {
			$withdrawBetAmtCondition = $this->getWithdrawCondAmount($promorule, $playerBonusAmount,
				$depositAmount, $playerId, $tranId, $betTimes, $extra_info, $dry_run);
		}

		if(empty($transferBetAmtCondition)){
            $transferBetAmtCondition = $this->getTransferCondAmount($promorule, $playerBonusAmount,
                $transferBetTimes, $depositAmount, $playerId, $extra_info, $dry_run);
        }

		if ($playerBonusAmount <= 0 || empty($playerBonusAmount)) {
			$this->utils->error_log('promorulesId', $promorulesId, 'playerPromoId', $playerPromoId, 'playerId', $playerId, ' wrong bonus amount:' . $playerBonusAmount);
			$extra_info['message']='wrong bonus amount:' . $playerBonusAmount;
			return false;
		}

		if($dry_run){
			$this->appendToDebugLog($extra_info['debug_log'], 'dry run, will ignore approvePromoToPlayer',
				['promorulesId'=>$promorulesId, 'depositTransId'=>$depositTransId, 'playerBonusAmount'=>$playerBonusAmount,
				'withdrawBetAmtCondition'=>$withdrawBetAmtCondition, 'transferBetAmtCondition'=>$transferBetAmtCondition]);
			return null;
		}
		$playerPromoId = $this->player_promo->approvePromoToPlayer($playerId, $promorulesId,
			$playerBonusAmount, $promoCmsSettingId, $adminId, $playerPromoId, $withdrawBetAmtCondition,
			$extra_info, $depositAmount, $betTimes, $reason, $transferBetAmtCondition);

		if ($promorule['bonusReleaseRule'] ==self::BONUS_RELEASE_RULE_BONUS_GAME) {
			$this->load->model(['promo_games']);
			$this->promo_games->player_game_grant_from_promorule($promorulesId, $playerId);
		}

		if($promorule['release_to_same_sub_wallet']=='1'){
			//set to same sub-wallet
			$subWalletId= @$extra_info['subWalletId'];
			if(empty($subWalletId) && !empty($playerPromoTriggeredSubWalletId)){
			    $subWalletId = $playerPromoTriggeredSubWalletId;
            }
			$trigger_wallets=$promorule['trigger_wallets'];
			$trigger_wallets_arr=[];
			if(!empty($trigger_wallets)){
				$trigger_wallets_arr=explode(',',$trigger_wallets);
			}
			//trigger on sub wallet
			if(!empty($subWalletId) && (!empty($trigger_wallets_arr) && in_array($subWalletId, $trigger_wallets_arr))){
				$promorule['releaseToSubWallet']=$subWalletId;
			}
		}

		$bonusTransId = $this->transactions->createBonusTransaction($adminId, $playerId, $playerBonusAmount,
			null, $playerPromoId, $tranId, Transactions::PROGRAM, null,
			Transactions::ADD_BONUS, null, @$promorule['promoCategory'], $promorule['releaseToSubWallet']);
        if(empty($bonusTransId)){
            $this->utils->error_log('create bonus transaction failed', $playerId, $playerBonusAmount);
            throw new WrongBonusException('Create bonus transaction failed');
        }
		if($promorule['releaseToSubWallet']>0){
			//depositAmount is transferAmount
			$originTransferAmount=$depositAmount;
			if($this->utils->isEnabledPromotionRule('only_real_when_release_bonus')){
				$walletType='real';
			}else{
				if(isset($extra_info['release_to_real']) && $extra_info['release_to_real']){
					$walletType='real';
				}else{
					$walletType='bonus';
				}
			}
			$playerName = $this->player_model->getUsernameById($playerId);
			$gamePlatformId = $promorule['releaseToSubWallet'];
			$transfer_from = 0; //main
			$transfer_to = $promorule['releaseToSubWallet'];
			$this->utils->debug_log('releaseToSubWallet', $transfer_to, 'playerName', $playerName, 'playerId', $playerId, 'playerBonusAmount', $playerBonusAmount);
			//transfer to subwallet
			$extra_info['releaseToSubWallet']=[
				'playerId'=>$playerId,
				'playerName'=>$playerName,
				'gamePlatformId'=>$gamePlatformId,
				'transfer_from'=>$transfer_from,
				'transfer_to'=>$transfer_to,
				'playerBonusAmount'=>$playerBonusAmount,
				'adminId'=>$adminId,
				'originTransferAmount'=>$originTransferAmount,
				'walletType'=>$walletType,
			];
		}

		$bet_times = 0;
		if ($promorule['withdrawRequirementConditionType'] == self::WITHDRAW_CONDITION_TYPE_BETTING_TIMES) {
			$bet_times = $promorule['withdrawRequirementBetCntCondition'];
		}

		if($promorule['withdrawRequirementConditionType'] != self::WITHDRAW_CONDITION_TYPE_NOTHING){
			$this->withdraw_condition->createWithdrawConditionForPromoruleBonus($this->isDepositPromo($promorule) // #1
											, $playerId // #2
											, $bonusTransId // #3
											, $withdrawBetAmtCondition // #4
											, $depositAmount // #5
											, $playerBonusAmount // #6
											, $bet_times // #7
											, $promorule // #8
											, $depositTransId // #9
											, $playerPromoId // #10
											, null // #11
											, null // #12
											, $extra_info // #13
										);
        }

        $allowDepositConditionType = [self::DEPOSIT_CONDITION_TYPE_MIN_LIMIT,self::DEPOSIT_CONDITION_TYPE_MIN_LIMIT_SINCE_REGISTRATION];
        if(in_array($promorule['withdrawRequirementDepositConditionType'],$allowDepositConditionType)){
            $withdrawDepAmtCondition = 0;
            if(!empty($promorule['withdrawRequirementDepositAmount'])){
                $withdrawDepAmtCondition = $promorule['withdrawRequirementDepositAmount'];
            }

			$this->withdraw_condition->createDepositConditionForPromoruleBonus($this->isDepositPromo($promorule) // #1
											, $playerId // #2
											, $bonusTransId // #3
											, $withdrawDepAmtCondition // #4
											, $depositAmount // #5
											, $playerBonusAmount // #6
											, $bet_times // #7
											, $promorule // #8
											, $depositTransId // #9
											, $playerPromoId // #10
											, null // #11
											, null // #12
											, $extra_info  // #13
									);
        }

        $transferRequirementWalletsInfo = null;
        if(isset($promorule['transferRequirementConditionType'])){
            $transferRequirementWalletsInfo = json_decode($promorule['transferRequirementWalletsInfo']);
        }

        if(isset($transferRequirementWalletsInfo) && ($promorule['transferRequirementConditionType'] != self::TRANSFER_CONDITION_TYPE_NOTHING)){
            $this->transfer_condition->createTransferCondition($promorule, $playerId, $playerPromoId, $transferBetAmtCondition);
        }

		$this->updatePromoId($promorule, $tranId, $playerPromoId, $extra_info);
		return $playerPromoId;
	}

	/**
	 * overview : decline promo
	 *
	 * @param $playerId
	 * @param $promorule
	 * @param $promoCmsSettingId
	 * @param $adminId
	 * @param null $bonusAmount
	 * @param null $depositAmount
	 * @param null $withdrawConditionAmount
	 * @param null $betTimes
	 * @param null $reason
	 * @param null $playerPromoId
	 * @return null
	 */
	public function declinePromo($playerId, $promorule, $promoCmsSettingId, $adminId,
		$bonusAmount=null, $depositAmount = null, $withdrawConditionAmount=null, $betTimes=null,
		$reason = null, $playerPromoId = null) {

		$this->load->model(array('player_promo','withdraw_condition', 'transactions', 'sale_order'));

		$promorulesId=$promorule['promorulesId'];
		$transactionStatus=Player_promo::TRANS_STATUS_DECLINED;

		$playerPromoId = $this->player_promo->declinePromoToPlayer($playerId, $promorulesId, $bonusAmount,
			$promoCmsSettingId, $adminId, $depositAmount, $withdrawConditionAmount,
			$transactionStatus, $betTimes, $reason, $playerPromoId);

		if (!empty($playerPromoId)) {
			//rollback
			$this->sale_order->clearPlayerPromoId($playerPromoId);
			$this->transactions->clearPlayerPromoId($playerPromoId);

			//cancel withdraw condition too
			$this->withdraw_condition->cancelWithdrawalConditionByPlayerPromoId($playerPromoId);
		}

		return $playerPromoId;
	}

	/**
     * Deprecated by curtis
	 * overview : expire promo
	 * @param $playerId
	 * @param $promorule
	 * @param $promoCmsSettingId
	 * @param $adminId
	 * @param null $bonusAmount
	 * @param null $depositAmount
	 * @param null $withdrawConditionAmount
	 * @param null $betTimes
	 * @param null $reason
	 * @param null $playerPromoId
	 * @return null
	 */
	public function expirePromo($playerId, $promorule, $promoCmsSettingId, $adminId,
		$bonusAmount=null, $depositAmount = null, $withdrawConditionAmount=null, $betTimes=null,
		$reason = null, $playerPromoId = null){
	    /*
		$this->load->model(array('player_promo','withdraw_condition', 'transactions', 'sale_order'));

		$promorulesId=$promorule['promorulesId'];
		$transactionStatus=Player_promo::TRANS_STATUS_EXPIRED;

		$playerPromoId = $this->player_promo->expirePromoToPlayer($playerId, $promorulesId, $bonusAmount,
			$promoCmsSettingId, $adminId, $depositAmount, $withdrawConditionAmount,
			$transactionStatus, $betTimes, $reason, $playerPromoId);


		if (!empty($playerpromoId)) {
			//rollback
			$this->sale_order->clearPlayerPromoId($playerPromoId);
			$this->transactions->clearPlayerPromoId($playerPromoId);

			//cancel withdraw condition too
			$this->withdraw_condition->cancelWithdrawalConditionByPlayerPromoId($playerPromoId);
		}

		return $playerPromoId;
	    */
	}


	/**
     * Deprecated by curtis
	 * overview : decline promo
	 *
	 * @param $playerId
	 * @param $promorule
	 * @param $promoCmsSettingId
	 * @param $adminId
	 * @param null $bonusAmount
	 * @param null $depositAmount
	 * @param null $withdrawConditionAmount
	 * @param null $betTimes
	 * @param null $reason
	 * @param null $playerPromoId
	 * @return null
	 */

	public function declineForeverPromo($playerId, $promorule, $promoCmsSettingId, $adminId,
		$bonusAmount=null, $depositAmount = null, $withdrawConditionAmount=null, $betTimes=null,
		$reason = null, $playerPromoId = null) {
        /*
        $this->load->model(array('player_promo','withdraw_condition', 'transactions', 'sale_order'));

        $promorulesId=$promorule['promorulesId'];
        $transactionStatus=Player_promo::TRANS_STATUS_DECLINED_FOREVER;

        $playerPromoId = $this->player_promo->declinedForeverPromoToPlayer($playerId, $promorulesId, $bonusAmount,
            $promoCmsSettingId, $adminId, $depositAmount, $withdrawConditionAmount,
            $transactionStatus, $betTimes, $reason, $playerPromoId);

        if (!empty($playerpromoId)) {
            //rollback
            $this->sale_order->clearPlayerPromoId($playerpromoId);
            $this->transactions->clearPlayerPromoId($playerpromoId);

            //cancel withdraw condition too
            $this->withdraw_condition->cancelWithdrawalConditionByPlayerPromoId($playerpromoId);

        }

        return $playerPromoId;
        */
    }


	/**
	 * overview : finish promo
	 *
	 * @param $playerId
	 * @param $promorule
	 * @param $promoCmsSettingId
	 * @param $adminId
	 * @param $bonusAmount
	 * @param $depositAmount
	 * @param $withdrawConditionAmount
	 * @param $betTimes
	 * @param $playerPromoId
	 * @param $extra_info
	 * @param null $reason
	 * @return mixed
	 */
	public function finishPromo($playerId, $promorule, $promoCmsSettingId,
		$adminId, $bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes,
		$playerPromoId, $extra_info, $reason=null){

		$this->load->model(array('player_promo','withdraw_condition', 'transactions', 'sale_order'));

		$promorulesId = $promorule['promorulesId'];
		$playerPromoId = $this->player_promo->finishPromoToPlayer($playerId, $promorulesId, $bonusAmount,
			$promoCmsSettingId, $adminId, $playerPromoId, $withdrawConditionAmount,
			$extra_info, $depositAmount, $betTimes, $reason);

		if (!empty($playerPromoId)) {
			//cancel withdraw condition too
			$this->withdraw_condition->cancelWithdrawalConditionByPlayerPromoId($playerPromoId);
		}

		return $playerPromoId;

	}

	/**
	 * overview : approve promo without release
	 *
	 * @param int $playerId
	 * @param int $promorule
	 * @param int $promoCmsSettingId
	 * @param int $adminId
	 * @param null $depositAmount
	 * @param null $tranId
	 * @param null $playerPromoId
	 * @param null $extra_info
	 * @param null $reason
	 * @return null
	 */
	public function approvePromoWithoutRelease($playerId, $promorule, $promoCmsSettingId, $adminId,
		$depositAmount = null, $tranId = null, $playerPromoId = null, &$extra_info=null, $reason=null, $dry_run=false) {
		$this->load->model(array('wallet_model', 'transactions', 'player_promo', 'sale_order', 'withdraw_condition', 'player_model', 'transfer_condition'));

		$promorulesId = $promorule['promorulesId'];
		$depositTransId=$tranId;

		$playerBonusAmount = 0;
		$withdrawBetAmtCondition=0;
        $transferBetAmtCondition=0;
		$betTimes=0;
        $reason=null;
		if (!empty($playerPromoId)) {
		    //check status first
			$playerPromo = $this->player_promo->getPlayerPromo($playerPromoId);
			if (!empty($playerPromo)) {
                if($playerPromo->transactionStatus==Player_promo::TRANS_STATUS_APPROVED){
                    $this->utils->debug_log('ignore approve promo because status', $playerPromoId);
                    return $playerPromoId;
                }

				$playerBonusAmount = $playerPromo->bonusAmount;
				$withdrawBetAmtCondition = $playerPromo->withdrawConditionAmount;
                $transferBetAmtCondition = $playerPromo->transferConditionAmount;
			} else {
				$playerPromoId = null;
			}
		}

        //overwrite bonus amount
        if(isset($extra_info['bonusAmount']) && !empty($extra_info['bonusAmount'])){
            $playerBonusAmount=$extra_info['bonusAmount'];
            $withdrawBetAmtCondition = $this->getWithdrawCondAmount($promorule, $playerBonusAmount,
                $depositAmount, $playerId, $tranId, $betTimes, $extra_info, $dry_run);
        }

		if (empty($playerBonusAmount)) {

			$playerBonusAmount = $this->getBonusAmount($promorule, $depositAmount, $playerId, $errorMessageLang, $extra_info, $dry_run);
			if(!empty($errorMessageLang)){
				$this->utils->error_log('approvePromoWithoutRelease', $errorMessageLang);
			}
		}

        if (empty($withdrawBetAmtCondition)) {
            $withdrawBetAmtCondition = $this->getWithdrawCondAmount($promorule, $playerBonusAmount,
                $depositAmount, $playerId, $tranId, $betTimes, $extra_info, $dry_run);
        }

        if(empty($transferBetAmtCondition)){
            $transferBetAmtCondition = $this->getTransferCondAmount($promorule, $playerBonusAmount,
                $transferBetTimes, $depositAmount, $playerId, $extra_info, $dry_run);
        }

		if($dry_run){
			$this->appendToDebugLog($extra_info['debug_log'], 'dry run, will ignore approvePromoToPlayerWithouRelease',
				['promorulesId'=>$promorulesId, 'depositTransId'=>$depositTransId, 'playerBonusAmount'=>$playerBonusAmount,
				'withdrawBetAmtCondition'=>$withdrawBetAmtCondition]);
			return null;
		}

		$playerPromoId = $this->player_promo->approvePromoToPlayerWithouRelease($playerId, $promorulesId, $playerBonusAmount,
			$promoCmsSettingId, $adminId, $playerPromoId , $withdrawBetAmtCondition,
			$depositAmount, $betTimes, $reason, $transferBetAmtCondition);

		$playerPromoData = [];

        // OGP-3381
        if ($promorule['bonusReleaseRule'] ==self::BONUS_RELEASE_RULE_BONUS_GAME) {
            $this->load->model(['promo_games']);
            $this->promo_games->player_game_grant_from_promorule($promorulesId, $playerId);
        }

        if($promorule['release_to_same_sub_wallet']=='1'){
            //set to same sub-wallet
            $subWalletId= @$extra_info['subWalletId'];
            $trigger_wallets=$promorule['trigger_wallets'];
            $trigger_wallets_arr=[];
            if(!empty($trigger_wallets)){
                $trigger_wallets_arr=explode(',',$trigger_wallets);
            }
            //trigger on sub wallet
            if(!empty($subWalletId) && (!empty($trigger_wallets_arr) && in_array($subWalletId, $trigger_wallets_arr))){
                $promorule['releaseToSubWallet']=$subWalletId;
                $playerPromoData['triggered_subwallet_id'] = $subWalletId;
            }
        }

		$bonusTransId = $this->transactions->createBonusTransaction($adminId, $playerId, $playerBonusAmount,
		 	null, $playerPromoId, $tranId, Transactions::PROGRAM, null,
		 	Transactions::ADD_BONUS, null, @$promorule['promoCategory'], $promorule['releaseToSubWallet']);
        if(empty($bonusTransId)){
            $this->utils->error_log('create bonus transaction failed', $playerId, $playerBonusAmount);
            throw new WrongBonusException('Create bonus transaction failed');
        }

        if($promorule['releaseToSubWallet']>0){
            //depositAmount is transferAmount
            $originTransferAmount=$depositAmount;
            if($this->utils->isEnabledPromotionRule('only_real_when_release_bonus')){
                $walletType='real';
            }else{
                if(isset($extra_info['release_to_real']) && $extra_info['release_to_real']){
                    $walletType='real';
                }else{
                    $walletType='bonus';
                }
            }
            $playerName = $this->player_model->getUsernameById($playerId);
            $gamePlatformId = $promorule['releaseToSubWallet'];
            $transfer_from = 0; //main
            $transfer_to = $promorule['releaseToSubWallet'];
            $this->utils->debug_log('releaseToSubWallet', $transfer_to, 'playerName', $playerName, 'playerId', $playerId, 'playerBonusAmount', $playerBonusAmount);
            //transfer to subwallet
            $extra_info['releaseToSubWallet']=[
                'playerId'=>$playerId,
                'playerName'=>$playerName,
                'gamePlatformId'=>$gamePlatformId,
                'transfer_from'=>$transfer_from,
                'transfer_to'=>$transfer_to,
                'playerBonusAmount'=>$playerBonusAmount,
                'adminId'=>$adminId,
                'originTransferAmount'=>$originTransferAmount,
                'walletType'=>$walletType,
            ];
        }

		$bet_times = 0;
		if ($promorule['withdrawRequirementConditionType'] == self::WITHDRAW_CONDITION_TYPE_BETTING_TIMES) {
			$bet_times = $promorule['withdrawRequirementBetCntCondition'];
		}

        if($promorule['withdrawRequirementConditionType'] != self::WITHDRAW_CONDITION_TYPE_NOTHING){
            $this->withdraw_condition->createWithdrawConditionForPromoruleBonus($this->isDepositPromo($promorule),
                $playerId, $bonusTransId, $withdrawBetAmtCondition, $depositAmount, $playerBonusAmount,
                $bet_times, $promorule, $depositTransId, $playerPromoId, null, null, $extra_info);
        }

        $allowDepositConditionType = [self::DEPOSIT_CONDITION_TYPE_MIN_LIMIT,self::DEPOSIT_CONDITION_TYPE_MIN_LIMIT_SINCE_REGISTRATION];
        if(in_array($promorule['withdrawRequirementDepositConditionType'],$allowDepositConditionType)){
            $withdrawDepAmtCondition = 0;
            if(!empty($promorule['withdrawRequirementDepositAmount'])){
                $withdrawDepAmtCondition = $promorule['withdrawRequirementDepositAmount'];
            }

			$this->withdraw_condition->createDepositConditionForPromoruleBonus($this->isDepositPromo($promorule) // #1
											, $playerId // #2
											, $bonusTransId // #3
											, $withdrawDepAmtCondition // #4
											, $depositAmount // #5
											, $playerBonusAmount // #6
											, $bet_times // #7
											, $promorule // #8
											, $depositTransId // #9
											, $playerPromoId // #10
											, null // #11
											, null // #12
											, $extra_info // #13
										);
        }

        $transferRequirementWalletsInfo = null;
        if(isset($promorule['transferRequirementConditionType'])){
            $transferRequirementWalletsInfo = json_decode($promorule['transferRequirementWalletsInfo']);
        }

        if(isset($transferRequirementWalletsInfo) && ($promorule['transferRequirementConditionType'] != self::TRANSFER_CONDITION_TYPE_NOTHING)){
            $this->transfer_condition->createTransferCondition($promorule, $playerId, $playerPromoId, $transferBetAmtCondition);
        }

        $this->player_promo->updatePlayerPromo($playerPromoId, $playerPromoData);
        $this->updatePromoId($promorule, $tranId, $playerPromoId, $extra_info);

		return $playerPromoId;
	}

	/**
	 * overview : approve promo
	 *
	 * @param int $playerId
	 * @param int $promorule
	 * @param int $promoCmsSettingId
	 * @param int $adminId
	 * @param null $depositAmount
	 * @param null $tranId
	 * @param null $playerPromoId
	 * @param null $extra_info
	 * @param null $reason
	 * @return null
	 */
	public function approvePrePromo($playerId, $promorule, $promoCmsSettingId, $adminId,
		$depositAmount = null, $tranId = null, $playerPromoId = null, &$extra_info=null, $reason=null, $dry_run=false) {
		$this->load->model(array('wallet_model', 'transactions', 'player_promo', 'sale_order', 'withdraw_condition', 'player_model', 'transfer_condition'));

		$promorulesId = $promorule['promorulesId'];
		$depositTransId=$tranId;

		$playerBonusAmount = 0;
		$withdrawBetAmtCondition=0;
        $transferBetAmtCondition=0;
        $betTimes=0;
        $reason=null;
		if (!empty($playerPromoId)) {
			$playerPromo = $this->player_promo->getPlayerPromo($playerPromoId);
			if (!empty($playerPromo)) {
                if($playerPromo->transactionStatus==Player_promo::TRANS_STATUS_APPROVED){
                    $this->utils->debug_log('ignore approve promo because status', $playerPromoId);
                    return $playerPromoId;
                }

				$playerBonusAmount = $playerPromo->bonusAmount;
				$withdrawBetAmtCondition = $playerPromo->withdrawConditionAmount;
                $transferBetAmtCondition = $playerPromo->transferConditionAmount;
			} else {
				$playerPromoId = null;
			}
		}

		//overwrite bonus amount
        if(isset($extra_info['bonusAmount']) && !empty($extra_info['bonusAmount'])){
            $playerBonusAmount=$extra_info['bonusAmount'];
            $withdrawBetAmtCondition = $this->getWithdrawCondAmount($promorule, $playerBonusAmount,
                $depositAmount, $playerId, $tranId, $betTimes, $extra_info, $dry_run);
        }

		$transactionStatus=Player_promo::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS;

		if (empty($playerBonusAmount)) {

			$playerBonusAmount = $this->getBonusAmount($promorule, $depositAmount, $playerId, $errorMessageLang,$extra_info, $dry_run);
			if(!empty($errorMessageLang)){
				$this->utils->error_log('approvePrePromo', $errorMessageLang);
			}

		}

        if (empty($withdrawBetAmtCondition)) {
            $withdrawBetAmtCondition = $this->getWithdrawCondAmount($promorule, $playerBonusAmount,
                $depositAmount, $playerId, $tranId, $betTimes, $extra_info, $dry_run);
        }

        if(empty($transferBetAmtCondition)){
            $transferBetAmtCondition = $this->getTransferCondAmount($promorule, $playerBonusAmount,
                $transferBetTimes, $depositAmount, $playerId, $extra_info, $dry_run);
        }

        if($dry_run){
            $this->appendToDebugLog($extra_info['debug_log'], 'dry run, will ignore approvePromoToPlayer',
                ['promorulesId'=>$promorulesId, 'depositTransId'=>$depositTransId, 'playerBonusAmount'=>$playerBonusAmount,
                    'withdrawBetAmtCondition'=>$withdrawBetAmtCondition, 'transferBetAmtCondition'=>$transferBetAmtCondition]);
            return null;
        }

		$playerPromoId = $this->player_promo->approvePrePromoToPlayer($playerId, $promorulesId, $playerBonusAmount,
			$promoCmsSettingId, $adminId, $depositAmount, $withdrawBetAmtCondition, $transactionStatus,
			$betTimes, $reason, $playerPromoId, $transferBetAmtCondition);

        if ($promorule['bonusReleaseRule'] ==self::BONUS_RELEASE_RULE_BONUS_GAME) {
            $this->load->model(['promo_games']);
            $this->promo_games->player_game_grant_from_promorule($promorulesId, $playerId);
        }

        if($promorule['release_to_same_sub_wallet']=='1'){
            //set to same sub-wallet
            $subWalletId= @$extra_info['subWalletId'];
            $trigger_wallets=$promorule['trigger_wallets'];
            $trigger_wallets_arr=[];
            if(!empty($trigger_wallets)){
                $trigger_wallets_arr=explode(',',$trigger_wallets);
            }
            //trigger on sub wallet
            if(!empty($subWalletId) && (!empty($trigger_wallets_arr) && in_array($subWalletId, $trigger_wallets_arr))){
                $promorule['releaseToSubWallet']=$subWalletId;
            }
        }

		$bonusTransId = $this->transactions->createBonusTransaction($adminId, $playerId, $playerBonusAmount,
		 	null, $playerPromoId, $tranId, Transactions::PROGRAM, null,
		 	Transactions::ADD_BONUS, null, @$promorule['promoCategory'], $promorule['releaseToSubWallet']);
        if(empty($bonusTransId)){
            $this->utils->error_log('create bonus transaction failed', $playerId, $playerBonusAmount);
            throw new WrongBonusException('Create bonus transaction failed');
        }

        if($promorule['releaseToSubWallet']>0){
            //depositAmount is transferAmount
            $originTransferAmount=$depositAmount;
            if($this->utils->isEnabledPromotionRule('only_real_when_release_bonus')){
                $walletType='real';
            }else{
                if(isset($extra_info['release_to_real']) && $extra_info['release_to_real']){
                    $walletType='real';
                }else{
                    $walletType='bonus';
                }
            }
            $playerName = $this->player_model->getUsernameById($playerId);
            $gamePlatformId = $promorule['releaseToSubWallet'];
            $transfer_from = 0; //main
            $transfer_to = $promorule['releaseToSubWallet'];
            $this->utils->debug_log('releaseToSubWallet', $transfer_to, 'playerName', $playerName, 'playerId', $playerId, 'playerBonusAmount', $playerBonusAmount);
            //transfer to subwallet
            $extra_info['releaseToSubWallet']=[
                'playerId'=>$playerId,
                'playerName'=>$playerName,
                'gamePlatformId'=>$gamePlatformId,
                'transfer_from'=>$transfer_from,
                'transfer_to'=>$transfer_to,
                'playerBonusAmount'=>$playerBonusAmount,
                'adminId'=>$adminId,
                'originTransferAmount'=>$originTransferAmount,
                'walletType'=>$walletType,
            ];
        }

		 $bet_times = 0;
		 if ($promorule['withdrawRequirementConditionType'] == self::WITHDRAW_CONDITION_TYPE_BETTING_TIMES) {
		 	$bet_times = $promorule['withdrawRequirementBetCntCondition'];
		 }

        if($promorule['withdrawRequirementConditionType'] != self::WITHDRAW_CONDITION_TYPE_NOTHING){
            $this->withdraw_condition->createWithdrawConditionForPromoruleBonus($this->isDepositPromo($promorule),
                $playerId, $bonusTransId, $withdrawBetAmtCondition, $depositAmount, $playerBonusAmount,
                $bet_times, $promorule, $depositTransId, $playerPromoId, null, null, $extra_info);
        }

        $allowDepositConditionType = [self::DEPOSIT_CONDITION_TYPE_MIN_LIMIT,self::DEPOSIT_CONDITION_TYPE_MIN_LIMIT_SINCE_REGISTRATION];
        if(in_array($promorule['withdrawRequirementDepositConditionType'],$allowDepositConditionType)){
            $withdrawDepAmtCondition = 0;
            if(!empty($promorule['withdrawRequirementDepositAmount'])){
                $withdrawDepAmtCondition = $promorule['withdrawRequirementDepositAmount'];
            }

			$this->withdraw_condition->createDepositConditionForPromoruleBonus($this->isDepositPromo($promorule) // #1
											, $playerId // #2
											, $bonusTransId // #3
											, $withdrawDepAmtCondition // #4
											, $depositAmount // #5
											, $playerBonusAmount // #6
											, $bet_times // #7
											, $promorule // #8
											, $depositTransId // #9
											, $playerPromoId // #10
											, null // #11
											, null // #12
											, $extra_info // #13
										);
        }

        $transferRequirementWalletsInfo = null;
        if(isset($promorule['transferRequirementConditionType'])){
            $transferRequirementWalletsInfo = json_decode($promorule['transferRequirementWalletsInfo']);
        }

        if(isset($transferRequirementWalletsInfo) && ($promorule['transferRequirementConditionType'] != self::TRANSFER_CONDITION_TYPE_NOTHING)){
            $this->transfer_condition->createTransferCondition($promorule, $playerId, $playerPromoId, $transferBetAmtCondition);
        }

        $this->updatePromoId($promorule, $tranId, $playerPromoId, $extra_info);
		return $playerPromoId;
	}

	/**
	 * overview : update only number without release
	 *
	 * @param $playerId
	 * @param $promorule
	 * @param $promoCmsSettingId
	 * @param $adminId
	 * @param null $depositAmount
	 * @param null $tranId
	 * @param null $playerPromoId
	 * @param null $extra_info
	 * @return array
	 */
	public function updateOnlyNumberWithoutRelease($playerId, $promorule, $promoCmsSettingId, $adminId,
		$depositAmount = null, $tranId = null, $playerPromoId = null, &$extra_info=null, $reason=null, $dry_run=false) {
		$this->load->model(array('wallet_model', 'transactions', 'player_promo', 'sale_order', 'withdraw_condition', 'player_model'));

		$success=false;
		$message=null;

		$promorulesId = $promorule['promorulesId'];
		$depositTransId=$tranId;

		$playerBonusAmount = 0;
		$withdrawBetAmtCondition=0;
		if (!empty($playerPromoId)) {
			$playerPromo = $this->player_promo->getPlayerPromo($playerPromoId);
			if (!empty($playerPromo)) {
				$playerBonusAmount = $playerPromo->bonusAmount;
				$withdrawBetAmtCondition = $playerPromo->withdrawConditionAmount;
			} else {
				$playerPromoId = null;
			}
		}

		if (empty($playerBonusAmount)) {

			$playerBonusAmount = $this->getBonusAmount($promorule, $depositAmount, $playerId, $errorMessageLang,$extra_info, $dry_run);
			if(!empty($errorMessageLang)){
				$this->utils->error_log('approvePromoWithoutRelease', $errorMessageLang);
			}

			$withdrawBetAmtCondition = $this->getWithdrawCondAmount($promorule, $playerBonusAmount,
				$depositAmount, $playerId, $tranId);
		}


		$playerPromoId = $this->player_promo->updateOnlyNumberWithoutRelease($playerId, $promorulesId,
			$playerBonusAmount, $promoCmsSettingId, $adminId, $playerPromoId, $withdrawBetAmtCondition, $depositAmount);

		$bonusTransId=0;

        $this->updatePromoId($promorule, $tranId, $playerPromoId, $extra_info);

		$success=!!$playerPromoId;
		$message=lang('Updated bonus amount');

		return array($success, $message);
	}

	public function updatePromoId($promorule, $tranId, $playerPromoId, &$extra_info = NULL){
        if(empty($playerPromoId)){
            return TRUE;
        }

        if (empty($tranId)) {
            if(isset($extra_info['transaction_list'])){
                foreach($extra_info['transaction_list'] as $transaction_entry){
                    $this->sale_order->updatePlayerPromoIdByTranId($transaction_entry['id'], $playerPromoId);
                    $this->transactions->updatePlayerPromoId($transaction_entry['id'], $playerPromoId, $promorule['promoCategory']);
                }
            }
            return TRUE;
        }

        if(isset($extra_info['is_payment_account_promo']) && $extra_info['is_payment_account_promo']){
            return TRUE;
        }

        $this->sale_order->updatePlayerPromoIdByTranId($tranId, $playerPromoId);
        $this->transactions->updatePlayerPromoId($tranId, $playerPromoId, $promorule['promoCategory']);

        if(isset($extra_info['saleOrder']) && $this->isDepositPromo($promorule) && $this->isTransferPromo($promorule)){
            $this->sale_order->updatePlayerPromoIdByTranId($extra_info['saleOrder']->transaction_id, $playerPromoId);
            $this->transactions->updatePlayerPromoId($extra_info['saleOrder']->transaction_id, $playerPromoId, $promorule['promoCategory']);
        }

        return TRUE;
    }

	/**
	 * overview : check if non deposit promo
	 *
	 * @param $promorule
	 * @return bool
	 */
	public function isNonDepositPromo($promorule) {
		return $promorule['promoType'] == self::PROMO_TYPE_NON_DEPOSIT;
	}

	/**
	 * overview : check if deposit promo
	 *
	 * @param $promorule
	 * @return bool
	 */
	public function isDepositPromo($promorule) {
		return $promorule['promoType'] == self::PROMO_TYPE_DEPOSIT;
	}

	/**
	 * overview : is transfer promo
	 *
	 * @param $promorule
	 * @return bool
	 */
	public function isTransferPromo($promorule) {
		return $promorule['promoType'] == self::PROMO_TYPE_DEPOSIT && !empty($promorule['trigger_wallets']);
	}

	/**
	 * overview : check if email promo
	 *
	 * @param $promorule
	 * @return bool
	 */
	public function isEmailPromo($promorule) {
		return $promorule['promoType'] == self::PROMO_TYPE_NON_DEPOSIT && $promorule['nonDepositPromoType'] == self::NON_DEPOSIT_PROMO_TYPE_EMAIL;
	}

	/**
	 * overview : check if mobile promo
	 *
	 * @param $promorule
	 * @return bool
	 */
	public function isMobilePromo($promorule) {
		return $promorule['promoType'] == self::PROMO_TYPE_NON_DEPOSIT && $promorule['nonDepositPromoType'] == self::NON_DEPOSIT_PROMO_TYPE_MOBILE;
	}

	/**
	 * overview : check if complete player information promo
	 *
	 * @param $promorule
	 * @return bool
	 */
	public function isCompletePlayerInfoPromo($promorule) {
		return $promorule['promoType'] == self::PROMO_TYPE_NON_DEPOSIT && $promorule['nonDepositPromoType'] == self::NON_DEPOSIT_PROMO_TYPE_COMPLETE_PLAYER_INFO;
	}

	/**
	 * overview : check if registration promo
	 *
	 * @param $promorule
	 * @return bool
	 */
	public function isRegistrationPromo($promorule) {
		return $promorule['promoType'] == self::PROMO_TYPE_NON_DEPOSIT && $promorule['nonDepositPromoType'] == self::NON_DEPOSIT_PROMO_TYPE_REGISTRATION;
	}

	/**
	 * overview :  check if betting promo
	 *
	 * @param $promorule
	 * @return bool
	 */
	public function isBettingPromo($promorule) {
		return $promorule['promoType'] == self::PROMO_TYPE_NON_DEPOSIT && $promorule['nonDepositPromoType'] == self::NON_DEPOSIT_PROMO_TYPE_BETTING;
	}

	/**
	 * overview : check if winning promo
	 * @param $promorule
	 * @return bool
	 */
	public function isWinningPromo($promorule) {
		return $promorule['promoType'] == self::PROMO_TYPE_NON_DEPOSIT && $promorule['nonDepositPromoType'] == self::NON_DEPOSIT_PROMO_TYPE_WINNING;
	}

	/**
	 * overview : check if lost promo
	 *
	 * @param $promorule
	 * @return bool
	 */
	public function isLossPromo($promorule) {
		return $promorule['promoType'] == self::PROMO_TYPE_NON_DEPOSIT && $promorule['nonDepositPromoType'] == self::NON_DEPOSIT_PROMO_TYPE_LOSS;
	}

	public function checkAvailableTransferTran($playerId, $promorule, $times, $fromDatetime = null) {
        $minAmount = null;
        $maxAmount = null;
        if ($promorule['depositConditionNonFixedDepositAmount'] == self::NON_FIXED_DEPOSIT_MIN_MAX) {
            $minAmount = $promorule['nonfixedDepositMinAmount'];
            $maxAmount = $promorule['nonfixedDepositMaxAmount'];
        }

        //from applicationPeriodStart to now
        $depositSuccesionPeriod = $promorule['depositSuccesionPeriod'];
        $this->load->model(array('transactions', 'player_model'));
        if ($depositSuccesionPeriod == self::DEPOSIT_SUCCESION_PERIOD_START_FROM_REG) {
            //from registration
            $periodFrom = $this->player_model->getPlayerRegisterDate($playerId);
            $periodTo = $this->getNowForMysql();
        } else {
            // ($depositSuccesionPeriod == self::DEPOSIT_SUCCESION_PERIOD_BONUS_EXPIRE) {
            //from start to hide date
            $periodFrom = $promorule['applicationPeriodStart'];
            $periodTo = $promorule['hide_date'];
        }
        if (!empty($fromDatetime)) {
            $periodFrom = $fromDatetime;
        }

        $this->utils->debug_log('search transaction by', $playerId, $periodFrom, $periodTo, $minAmount, $maxAmount, $times);

        // $disabledTransType=[
        // 		Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET,
        // 		Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET,
        // 		Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET,
        // 		Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET,
        // 		Transactions::WITHDRAWAL,
        // 	];

        // if(!$this->getPromotionRules('enabled_notallow_transaction_type')){
        $disabledTransType=null;
        // }

        $tran = $this->transactions->listDepositByDate($playerId, $periodFrom, $periodTo);

        return $tran;
    }

	/**
	 * overview : get available transfer transaction
	 *
	 * @param int	$playerId
	 * @param int	$promorule
	 * @param $times
	 * @param null $fromDatetime
	 * @return mixed
	 */
	public function getAvailableTransferTran($playerId, $promorule, $times, $fromDatetime = null) {
		$minAmount = null;
		$maxAmount = null;
		if ($promorule['depositConditionNonFixedDepositAmount'] == self::NON_FIXED_DEPOSIT_MIN_MAX) {
			$minAmount = $promorule['nonfixedDepositMinAmount'];
			$maxAmount = $promorule['nonfixedDepositMaxAmount'];
		}

		//from applicationPeriodStart to now
		$depositSuccesionPeriod = $promorule['depositSuccesionPeriod'];
		$this->load->model(array('transactions', 'player_model'));
		if ($depositSuccesionPeriod == self::DEPOSIT_SUCCESION_PERIOD_START_FROM_REG) {
			//from registration
			$periodFrom = $this->player_model->getPlayerRegisterDate($playerId);
			$periodTo = $this->getNowForMysql();
		} else {
			// ($depositSuccesionPeriod == self::DEPOSIT_SUCCESION_PERIOD_BONUS_EXPIRE) {
			//from start to hide date
			$periodFrom = $promorule['applicationPeriodStart'];
			$periodTo = $promorule['hide_date'];
		}
		if (!empty($fromDatetime)) {
			$periodFrom = $fromDatetime;
		}

		$this->utils->debug_log('search transaction by', $playerId, $periodFrom, $periodTo, $minAmount, $maxAmount, $times);

		// $disabledTransType=[
		// 		Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET,
		// 		Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET,
		// 		Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET,
		// 		Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET,
		// 		Transactions::WITHDRAWAL,
		// 	];

		// if(!$this->getPromotionRules('enabled_notallow_transaction_type')){
			$disabledTransType=null;
		// }

		$tran = $this->transactions->getAvailableTransferInfoByDate($playerId,
			$periodFrom, $periodTo, $minAmount, $maxAmount, $times, $disabledTransType);

		return $tran;
	}

	/**
	 * overview : get available deposit transaction
	 *
	 * @param int	$playerId
	 * @param int	$promorule
	 * @param $times
	 * @param date 	$fromDatetime
	 * @return array
	 */
	public function getAvailableDepositTran($playerId, $promorule, $times, $fromDatetime = null, &$extra_info = NULL) {
		$minAmount = null;
		$maxAmount = null;
		if ($promorule['depositConditionNonFixedDepositAmount'] == self::NON_FIXED_DEPOSIT_MIN_MAX) {
			$minAmount = $promorule['nonfixedDepositMinAmount'];
			$maxAmount = $promorule['nonfixedDepositMaxAmount'];
		}

		//from applicationPeriodStart to now
		$depositSuccesionPeriod = (int)$promorule['depositSuccesionPeriod'];
		$this->load->model(array('transactions', 'player_model'));

		switch($depositSuccesionPeriod){
            case self::DEPOSIT_SUCCESION_PERIOD_START_FROM_REG:
                //from registration
                $periodFrom = $this->player_model->getPlayerRegisterDate($playerId);
                $periodTo = $this->getNowForMysql();
                break;
            case self::DEPOSIT_SUCCESION_PERIOD_BONUS_EXPIRE:
            default:
                // ($depositSuccesionPeriod == self::DEPOSIT_SUCCESION_PERIOD_BONUS_EXPIRE) {
                //from start to hide date
                $periodFrom = $promorule['applicationPeriodStart'];
                $periodTo = $promorule['hide_date'];
                break;
        }
		if (!empty($fromDatetime)) {
			$periodFrom = $fromDatetime;
		}

		$this->utils->debug_log('search transaction by', $playerId, $periodFrom, $periodTo, $minAmount, $maxAmount, $times);

		$this->appendToExtraInfoDebugLog($extra_info, 'search transaction by', [
			'playerId'=>$playerId, 'periodFrom'=>$periodFrom, 'periodTo'=>$periodTo,
			'minAmount'=>$minAmount, 'maxAmount'=>$maxAmount, 'times'=>$times,
		]);

        $rows = [];
        $row = NULL;
        switch($promorule['depositSuccesionType']){
            case self::DEPOSIT_SUCCESION_TYPE_NOT_FIRST:
            case self::DEPOSIT_SUCCESION_TYPE_EVERY_TIME:
                $rows = $this->transactions->listDepositByDate($playerId, $periodFrom, $periodTo, 'desc');
                if (empty($rows) || count($rows) < $times) {
                    return null;
                }
                $row = $rows[0];
                break;
            case self::DEPOSIT_SUCCESION_TYPE_ANY:
                $rows = $this->transactions->listDepositByDate($playerId, $periodFrom, $periodTo, 'asc');
                if (empty($rows) || count($rows) < $times || !isset($rows[$times - 1])) {
                    return null;
                }
                $row = $rows[$times - 1];
                break;
            case self::DEPOSIT_SUCCESION_TYPE_FIRST:
            default:
                $rows = $this->transactions->listDepositByDate($playerId, $periodFrom, $periodTo, 'asc');
                if (empty($rows) || count($rows) < $times) {
                    return null;
                }
                $row = $rows[0];
                break;
        }
        //by times
        if ($times < 0) {
            //any available
            foreach ($rows as $row) {

                $this->utils->debug_log('isAppliedPromo', $this->transactions->isAppliedPromo($row), 'transaction', $row->id, 'player_promo_id', $row->player_promo_id,
                    'amount', $row->amount, 'min', $minAmount, 'max', $maxAmount);
                $this->appendToExtraInfoDebugLog($extra_info, 'search deposit record',
                    ['isAppliedPromo'=>$this->transactions->isAppliedPromo($row),
                        'transaction'=> $row->id,
                        'player_promo_id'=> $row->player_promo_id,
                        'amount'=> $row->amount,
                        'min'=> $minAmount,
                        'max'=> $maxAmount]);

                if (($row->amount >= $minAmount || $minAmount === null || $minAmount <= 0) && ($row->amount <= $maxAmount || $maxAmount === null || $maxAmount <= 0)) {
                    if($this->transactions->isAppliedPromo($row)){
                        continue;
                    }
                    $ret = $this->existsTransByTypesAfter($playerId, $promorule, $row->created_at, $extra_info);
                    if ($ret) {
                        continue;
                    }

                    $this->appendToExtraInfoDebugLog($extra_info, 'found deposit trans', [
                        'row'=>$row->id,
                    ]);
                    return $row;
                }
            }

            $extra_info['error_message'] = 'promo_rule.common.error';

            return NULL;
        } else {

            $this->utils->debug_log('isAppliedPromo', $this->transactions->isAppliedPromo($row), 'transaction', $row->id, 'player_promo_id', $row->player_promo_id,
                'amount', $row->amount, 'min', $minAmount, 'max', $maxAmount);
            $this->appendToDebugLog($extra_info['debug_log'], 'isAppliedPromo',
                ['isAppliedPromo'=>$this->transactions->isAppliedPromo($row),
                    'transaction'=> $row->id,
                    'player_promo_id'=> $row->player_promo_id,
                    'amount'=> $row->amount,
                    'min'=> $minAmount,
                    'max'=> $maxAmount]);

            if (($row->amount >= $minAmount || $minAmount === null || $minAmount <= 0) && ($row->amount <= $maxAmount || $maxAmount === null || $maxAmount <= 0)) {
                if($this->transactions->isAppliedPromo($row)){
                    $extra_info['error_message'] = 'notify.34';
                    return NULL;
                }
                $ret = $this->existsTransByTypesAfter($playerId, $promorule, $row->created_at, $extra_info);
                if ($ret) {
                    return NULL;
                }
                return $row;
            }else{
                $extra_info['error_message'] = 'notify.37';
                return NULL;
            }
        }

		return NULL;
	}

	public function existsTransByTypesAfter($playerId, $promorule, $created_at, &$extra_info){
        if($promorule['donot_allow_any_withdrawals_after_deposit']){
            if($this->transactions->existsTransByTypesAfter($created_at, $playerId, [Transactions::WITHDRAWAL], [Transactions::STATUS_NORMAL])){
            	$extra_info['error_message']='notify.promo_donot_allow_any_withdrawals_after_deposit';

				$this->appendToDebugLog($extra_info['debug_log'], 'WITHDRAWAL promo_donot_allow_any_withdrawals_after_deposit',
					['created_at'=>$created_at, 'playerId'=>$playerId]);

                return true;
            }
        }

        if($promorule['donot_allow_any_despoits_after_deposit']){
            if($this->transactions->existsTransByTypesAfter($created_at, $playerId, [Transactions::DEPOSIT], [Transactions::APPROVED])){
                $extra_info['error_message']='notify.promo_donot_allow_any_despoits_after_deposit';
                $this->appendToDebugLog($extra_info['debug_log'], 'DEPOSIT promo_donot_allow_any_despoits_after_deposit',
                    ['created_at'=>$created_at, 'playerId'=>$playerId]);
                return true;
            }
        }

        if($promorule['donot_allow_any_available_bet_after_deposit']){
            $this->CI->load->model(['game_logs']);

            list($totalBet, $totalWin, $totalLoss, $totalBet) = $this->CI->game_logs->getTotalBetsWinsLossByPlayers($playerId, $created_at, $this->CI->utils->getNowForMysql());
            $totalWin = (float)$totalWin;
            $totalLoss = (float)$totalLoss;
            if($totalWin != 0 || $totalLoss != 0){
                $extra_info['error_message']='notify.promo_donot_allow_any_available_bet_after_deposit';
                $this->appendToDebugLog($extra_info['debug_log'], 'DEPOSIT promo_donot_allow_any_available_bet_after_deposit',
                    ['created_at'=>$created_at, 'playerId'=>$playerId]);
                return true;
            }
        }

        if($promorule['donot_allow_any_transfer_after_deposit']){
            if($this->transactions->existsTransByTypesAfter($created_at, $playerId, [
            	Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET, Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET,
            	Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET, Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET], [Transactions::STATUS_NORMAL])){
            	$extra_info['error_message']='notify.promo_donot_allow_any_transfer_after_deposit';

				$this->appendToDebugLog($extra_info['debug_log'],
					'TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET/TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET/MANUAL_ADD_BALANCE_ON_SUB_WALLET/MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET promo_donot_allow_any_transfer_after_deposit',
					['created_at'=>$created_at, 'playerId'=>$playerId]);

                return true;
            }
        }

        $deposit_promotion_disabled_transaction_type_list=$this->utils->getConfig('deposit_promotion_disabled_transaction_type_list');

        $disabledTransType = [];

        foreach($deposit_promotion_disabled_transaction_type_list as $transType){
            switch($transType){
                case 'transfer':
                    $disabledTransType[] = Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET;
                    $disabledTransType[] = Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET;
                    $disabledTransType[] = Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET;
                    $disabledTransType[] = Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET;
                    break;
                case 'deposit':
                case 'withdrawal':
                default:
                    # code...
                    break;
            }
        }

        $this->utils->debug_log('disable_after_type', $disabledTransType);

        // if(!$this->getPromotionRules('enabled_notallow_transaction_type')){
        // 	$disabledTransType=null;
        // }

        if(empty($disabledTransType)){
            return false;
        }

        if($this->transactions->existsTransByTypesAfter($created_at, $playerId, $disabledTransType)){
            $extra_info['error_message']='notify.promo_donot_allow_any_transfer_after_deposit';
        	return true;
        }

        return false;
    }

	/**
	 * overview : get transaction deposit amount
	 *
	 * @param int $playerId
	 * @param int $promorule
	 * @param date $fromDatetime
	 * @return null
	 */
	public function getTranValidDepositAmount($playerId, $promorule, $fromDatetime = null) {
		// $this->load->model(array('transactions'));
		// $this->transactions->sumDepositByPlayerId($playerId,$promorule['applicationPeriodStart'], $this->getNowForMysql());

		$minAmount = null;
		$maxAmount = null;
		if ($promorule['depositConditionNonFixedDepositAmount'] == self::NON_FIXED_DEPOSIT_MIN_MAX) {
			$minAmount = $promorule['nonfixedDepositMinAmount'];
			$maxAmount = $promorule['nonfixedDepositMaxAmount'];
		}

		//from applicationPeriodStart to now
		$depositSuccesionPeriod = (int)$promorule['depositSuccesionPeriod'];
		$this->load->model(array('transactions'));

		switch($depositSuccesionPeriod){
            case self::DEPOSIT_SUCCESION_PERIOD_START_FROM_REG:
                //from registration
                $periodFrom = $this->player_model->getPlayerRegisterDate($playerId);
                $periodTo = $this->getNowForMysql();
                // } elseif ($depositSuccesionPeriod == 2) {
                // 	//this week
                // 	$periodFrom = date("Y-m-d", strtotime('monday this week')) . ' 00:00:00';
                // 	$periodTo = date("Y-m-d", strtotime('sunday this week')) . ' 23:59:59';
                // } elseif ($depositSuccesionPeriod == 3) {
                // 	//this month
                // 	$periodFrom = date("Y-m-d", strtotime('first day of')) . ' 00:00:00';
                // 	$periodTo = date("Y-m-d", strtotime('last day of')) . ' 23:59:59';
                break;
            case self::DEPOSIT_SUCCESION_PERIOD_BONUS_EXPIRE:
            default:
                // ($depositSuccesionPeriod == self::DEPOSIT_SUCCESION_PERIOD_BONUS_EXPIRE) {
                //from start to hide date
                $periodFrom = $promorule['applicationPeriodStart'];
                $periodTo = $promorule['hide_date'];
                break;
        }
		if (!empty($fromDatetime)) {
			$periodFrom = $fromDatetime;
		}

		// $playerPromoIds = $this->player_promo->getPlayerPromoIds($playerId, $promorule['promorulesId']);

		// $this->utils->debug_log('playerPromoIds', $playerPromoIds);
		// $this->utils->printLastSQL();

		$this->utils->debug_log('search transaction by', $playerId, $periodFrom, $periodTo, $minAmount, $maxAmount);

		$tranList = $this->transactions->getListValidDepositByPlayerId($playerId,
			$periodFrom, $periodTo, $minAmount, $maxAmount);

        // $ret = $this->existsTransByTypesAfter($playerId, $promorule, $row->created_at, $extra_info);
        // if ($ret) {
        //     return NULL;
        // }

		// $this->utils->printLastSQL();

		// $tranId = null;
		// $depositAmount = 0;
		// if (!empty($tran)) {
		// 	$tranId = $tran->id;
		// 	$depositAmount = $tran->amount;
		// 	// foreach ($trans as $t) {
		// 	// 	$tranIds[] = $t->id;
		// 	// 	$depositAmount += $t->amount;
		// 	// }
		// }
		// return array($depositAmount, $tranId);
		$tran = null;
		if (!empty($tranList)) {
			$tran = $tranList[0];
		}
		return $tran;
	}

	// public function getTranValidTransferAmount($playerId, $promorule, $fromDatetime = null) {
	// 	// $this->load->model(array('transactions'));
	// 	// $this->transactions->sumDepositByPlayerId($playerId,$promorule['applicationPeriodStart'], $this->getNowForMysql());

	// 	$minAmount = null;
	// 	$maxAmount = null;
	// 	if ($promorule['depositConditionNonFixedDepositAmount'] == self::NON_FIXED_DEPOSIT_MIN_MAX) {
	// 		$minAmount = $promorule['nonfixedDepositMinAmount'];
	// 		$maxAmount = $promorule['nonfixedDepositMaxAmount'];
	// 	}

	// 	//from applicationPeriodStart to now
	// 	$depositSuccesionPeriod = $promorule['depositSuccesionPeriod'];
	// 	$this->load->model(array('transactions'));
	// 	if ($depositSuccesionPeriod == self::DEPOSIT_SUCCESION_PERIOD_START_FROM_REG) {
	// 		//from registration
	// 		$periodFrom = $this->player_model->getPlayerRegisterDate($playerId);
	// 		$periodTo = $this->getNowForMysql();
	// 		// } elseif ($depositSuccesionPeriod == 2) {
	// 		// 	//this week
	// 		// 	$periodFrom = date("Y-m-d", strtotime('monday this week')) . ' 00:00:00';
	// 		// 	$periodTo = date("Y-m-d", strtotime('sunday this week')) . ' 23:59:59';
	// 		// } elseif ($depositSuccesionPeriod == 3) {
	// 		// 	//this month
	// 		// 	$periodFrom = date("Y-m-d", strtotime('first day of')) . ' 00:00:00';
	// 		// 	$periodTo = date("Y-m-d", strtotime('last day of')) . ' 23:59:59';
	// 	} else {
	// 		// ($depositSuccesionPeriod == self::DEPOSIT_SUCCESION_PERIOD_BONUS_EXPIRE) {
	// 		//from start to hide date
	// 		$periodFrom = $promorule['applicationPeriodStart'];
	// 		$periodTo = $promorule['hide_date'];
	// 	}
	// 	if (!empty($fromDatetime)) {
	// 		$periodFrom = $fromDatetime;
	// 	}

	// 	// $playerPromoIds = $this->player_promo->getPlayerPromoIds($playerId, $promorule['promorulesId']);

	// 	// $this->utils->debug_log('playerPromoIds', $playerPromoIds);
	// 	// $this->utils->printLastSQL();

	// 	$this->utils->debug_log('search transaction by', $playerId, $periodFrom, $periodTo, $minAmount, $maxAmount);

	// 	$tranList = $this->transactions->getListValidTransferByPlayerId($playerId,
	// 		$periodFrom, $periodTo, $minAmount, $maxAmount);

	// 	// $this->utils->printLastSQL();

	// 	// $tranId = null;
	// 	// $depositAmount = 0;
	// 	// if (!empty($tran)) {
	// 	// 	$tranId = $tran->id;
	// 	// 	$depositAmount = $tran->amount;
	// 	// 	// foreach ($trans as $t) {
	// 	// 	// 	$tranIds[] = $t->id;
	// 	// 	// 	$depositAmount += $t->amount;
	// 	// 	// }
	// 	// }
	// 	// return array($depositAmount, $tranId);
	// 	$tran = null;
	// 	if (!empty($tranList)) {
	// 		$tran = $tranList[0];
	// 	}
	// 	return $tran;
	// }


/*
public function countDepositSuccesion($playerId, $promorule, $fromDatetime = null) {
$this->load->model(array('player_model', 'transactions'));
$depositSuccesionPeriod = $promorule['depositSuccesionPeriod'];

$periodFrom = null;
$periodTo = null;

if ($depositSuccesionPeriod == self::DEPOSIT_SUCCESION_PERIOD_START_FROM_REG) {
//from registration
$periodFrom = $this->player_model->getPlayerRegisterDate($playerId);
$periodTo = $this->getNowForMysql();
// } elseif ($depositSuccesionPeriod == 2) {
// 	//this week
// 	$periodFrom = date("Y-m-d", strtotime('monday this week')) . ' 00:00:00';
// 	$periodTo = date("Y-m-d", strtotime('sunday this week')) . ' 23:59:59';
// } elseif ($depositSuccesionPeriod == 3) {
// 	//this month
// 	$periodFrom = date("Y-m-d", strtotime('first day of')) . ' 00:00:00';
// 	$periodTo = date("Y-m-d", strtotime('last day of')) . ' 23:59:59';
} elseif ($depositSuccesionPeriod == self::DEPOSIT_SUCCESION_PERIOD_BONUS_EXPIRE) {
//from start to hide date
$periodFrom = $promorule['applicationPeriodStart'];
$periodTo = $promorule['hide_date'];
} else {
$this->utils->debug_log("The given promorules.depositSuccesionPeriod({$depositSuccesionPeriod}) is not valid");
}
if (!empty($fromDatetime)) {
$periodFrom = $fromDatetime;
}
$this->utils->debug_log(array('depositSuccesionPeriod' => $depositSuccesionPeriod, 'fromDatetime' => $fromDatetime));
//get player total deposit count
$playerCurrentDepositSuccesionCnt = $this->transactions->countDepositByPlayerId($playerId, $periodFrom, $periodTo);
$this->utils->debug_log(array('playerCurrentDepositSuccesionCnt' => $playerCurrentDepositSuccesionCnt));
return $playerCurrentDepositSuccesionCnt;
}
 */

	/**
	 * overview : check if auto release
	 *
	 * @param $promorule
	 * @return bool
	 */
	public function isAutoRelease($promorule) {
		return $promorule['bonusReleaseToPlayer'] == self::BONUS_RELEASE_TO_PLAYER_AUTO;
	}

	/**
	 * overview : load run time
	 *
	 * @param int	$playerId
	 * @param int	$promorule
	 * @param int	$playerBonusAmount
	 * @param int	$depositAmount
	 * @return array
	 */
	public function loadRuntime($playerId, $promorule, $playerBonusAmount = null, $depositAmount = null) {
		require_once dirname(__FILE__) . '/../../libraries/runtime.php';
		$runtime = Runtime::getRuntime($playerId, $promorule, $playerBonusAmount, $depositAmount);
		return $runtime;
	}

	public function loadCustomizedPromoRuleObject(&$extra_info, $desc_class, $playerId, $promorule, $playerBonusAmount = null, $depositAmount = null) {
		// ['class'=>class name, 'any other parameter']
		$class_name=$desc_class['class'];

		$this->appendToDebugLog($extra_info['debug_log'], 'load promo rule class:'.$class_name);

		//try call class
		$this->load->model('customized_promo_rules/'.$class_name);
		$ruleObj=$this->$class_name;
		$ruleObj->init($playerId, $promorule, $playerBonusAmount, $depositAmount);
		return $ruleObj;
	}

	/**
	 * overview : validate customized condition
	 *
	 * @param $playerId
	 * @param $promorule
	 * @param null $extra_info
	 * @return array ['success'=>, 'message'=>, 'noscript'(optional)=>]
	 */
	public function validateCustomizedBonusCondition($playerId, $promorule, &$extra_info=null, $dry_run=false) {
		$formula = $promorule['formula'];
		// $this->utils->debug_log('formula', $formula);
		$formula = json_decode($formula, true);
		$this->appendToDebugLog($extra_info['debug_log'], 'formula', ['not empty bonus_condition'=>!empty($formula['bonus_condition'])]);
		// $this->utils->debug_log('formula', !empty($formula['bonus_condition']));

		$result = array('success' => true, 'message' => null, 'noscript' => true);

		if (isset($formula['bonus_condition']) && !empty($formula['bonus_condition'])) {
			$js = $formula['bonus_condition'];
			$bonusConditionResult = null;
			//if it's json, we try run php
			$desc_class=$this->utils->decodeJson($js);
			if($desc_class!==null){
				//is json format, try load class
				$ruleObj=$this->loadCustomizedPromoRuleObject($extra_info, $desc_class, $playerId, $promorule);
				// $ruleObj->init($playerId, $promorule);
                $bonusConditionResult = $ruleObj->run('runBonusConditionChecker', $desc_class, $extra_info, $dry_run);
				if(isset($bonusConditionResult['unimplemented']) && $bonusConditionResult['unimplemented']){
					$bonusConditionResult=['success'=>false, 'message'=>'Unimplemented'];
				}
                return (empty($bonusConditionResult)) ? $result : $bonusConditionResult;
			}else{
				//try run js
				$runtime = $this->loadRuntime($playerId, $promorule);
				$bonusConditionResult = $runtime->runjs($js, $extra_info, $dry_run);
			}
			$this->utils->debug_log('bonusConditionResult', $bonusConditionResult);
			return (empty($bonusConditionResult)) ? $result : $bonusConditionResult;
		}
		return $result;
	}

	/**
	 * overview : check only condition and bonus
	 *
	 * @param int	$playerId
	 * @param int	$promorule
	 * @param int	$promoCmsSettingId
	 * @param bool|false $preapplication
	 * @param null $playerPromoId
	 * @param null $extra_info
	 * @return array
	 */
	public function checkOnlyPromotion($playerId, $promorule, $promoCmsSettingId,
			$preapplication=false, $playerPromoId=null, &$extra_info=null, $dry_run=false) {

		$this->utils->debug_log('start check promotion', $playerId, 'promorule',
			@$promorule['promorulesId'], 'promoCmsSettingId', $promoCmsSettingId, 'preapplication', $preapplication,
			'playerPromoId', $playerPromoId, 'extra_info', $extra_info);

		$this->load->model(array('player_model', 'transactions', 'player_promo', 'total_player_game_hour'));


		$promorulesId = $promorule['promorulesId'];
		$this->utils->debug_log(array(
			'playerId' => $playerId,
			'promorule' => $promorulesId,
			'promoCmsSettingId' => $promoCmsSettingId,
		));
		$adminId = $this->users->getSuperAdminId();
		if(empty($extra_info)){
			$extra_info=[];
		}
		$extra_info['is_checking_before_deposit']=false;

		$allowedResult=[];
		$ignoreResult=[];
		//check if player level is valid in this promo
		$allowedFlag = $this->isAllowedPlayer($promorulesId, $promorule, $playerId, $allowedResult, $ignoreResult);
		$this->appendToDebugLog($extra_info['debug_log'], 'allowedFlag:'.$allowedFlag.', allowedResult:'.var_export($allowedResult, true).
			',ignoreResult: '.var_export($ignoreResult,true));
		$this->utils->debug_log('allowedFlag', $allowedFlag, $allowedResult, $ignoreResult);
		if ($allowedFlag == false) {
			//if playerlevel is invalid
			//Your player level is not valid in this promo! Please contact customer service for more information
			$message = 'notify.35';
			$success = false;

			if(!empty($allowedResult)){
				if(!$ignoreResult['level'] && $allowedResult['level']===false){
					$message = 'Your player level is not valid in this promo';
				}else if(!$ignoreResult['affiliate'] && $allowedResult['affiliate']===false){
					$message = 'Your affiliate is not valid in this promo';
				}else if(!$ignoreResult['agency'] && $allowedResult['agency']===false){
					$message = 'Your agency is not valid in this promo';
				}else if(!$ignoreResult['player'] && $allowedResult['player']===false){
					$message = 'You are not valid in this promo';
				}
			}

			return array($success, $message);
		}

		if($promorule['always_join_promotion']=='1'){
			//ignore check request count
		}else{

			//get playerPromoRequest
			$playerPromoRequestCount = $this->player_promo->countPlayerDuplicatePromo($playerId, $promorulesId,
				[Player_promo::TRANS_STATUS_REQUEST,
				Player_promo::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS,
				Player_promo::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS],
				null,null,null, $playerPromoId);
			$this->appendToDebugLog($extra_info['debug_log'], 'playerPromoRequestCount:'.$playerPromoRequestCount);
			$this->utils->debug_log(array('playerPromoRequestCount' => $playerPromoRequestCount));
			if ($playerPromoRequestCount > 0) {
				//if promo request exists
				//you have existing promo request already
				$message = 'notify.34';
				$success = false;
				return array($success, $message);
			}
		}

		//check daily request limit
		if(!empty($promorule['request_limit'])){
			$requestCount=$this->player_promo->getDailyPromoRequestByPlayerId($playerId, $promoCmsSettingId);
			if($requestCount>=$promorule['request_limit']){
				$message = 'Reached today request limit';
				$success = false;
				return [$success, $message];
			}
		}

        //check daily approved limit
        if(!empty($promorule['approved_limit'])){
            $requestCount=$this->player_promo->getDailyPromoApproved($promorulesId);
            if($requestCount>=$promorule['approved_limit']){
                $message = 'Reached today approved limit';
                $success = false;
                return [$success, $message];
            }
        }

        //check total approved limit
        if(!empty($promorule['total_approved_limit'])){
            $requestCount=$this->player_promo->getTotalPromoApproved($promorulesId);
            if($requestCount>=$promorule['total_approved_limit']){
                $message = 'Reached total approved limit';
                $success = false;
                return [$success, $message];
            }
        }

		//donot_allow_other_promotion is like group, radio button rule
        if($promorule['donot_allow_other_promotion']){
            if($this->player_promo->existsUnfinishedPromoAndDonotAllowOthers($playerId)){
                $this->appendToDebugLog($extra_info['debug_log'], 'existsUnfinishedPromoAndDonotAllowOthers, playerId:'.$playerId);

                $success=false;
                $message='notify.34';
                return array($success, $message);
            }
        }

        //donot_allow_other_promotion is like group, radio button rule
        if($promorule['dont_allow_request_promo_from_same_ips']){
            $player_ip = $this->CI->utils->getIp();
            if($this->player_promo->existsPlayerPromoFromSameIp($promorulesId, $player_ip)){
                $this->appendToDebugLog($extra_info['debug_log'], 'existsUnfinishedPromoAndDonotAllowOthers, playerId:'.$playerId);

                $success=false;
                $message='promo.dont_allow_request_promo_from_same_ips';
                return array($success, $message);
            }
        }

		if($preapplication){
			$success=true;
			$message='';
			return array($success, $message);
		}

		// } else {
		//if deposit promo is by application
		list($success, $message) = $this->validRepeatable($playerId, $promorule, $extra_info);
		$this->appendToDebugLog($extra_info['debug_log'], "is repeatable:".$success." message:".$message);
		$this->utils->debug_log("is repeatable:", $success, $message);
		if (!$success) {
			return array($success, $message);
		}

		//validate bonus condition
		$rlt = $this->validateCustomizedBonusCondition($playerId, $promorule, $extra_info, $dry_run);
		$this->appendToDebugLog($extra_info['debug_log'],'validateCustomizedBonusCondition:'.var_export($rlt, true));
		if (!$rlt['success']) {
			return array($rlt['success'], $rlt['message']);
		}
		if(isset($rlt['transaction_list'])){
            $extra_info['transaction_list']=$rlt['transaction_list'];
        }
		if(isset($rlt['continue_process_after_script'])){
			$extra_info['continue_process_after_script']=$rlt['continue_process_after_script'];
		}

		$noScriptDepositCondition = isset($rlt['noscript']) && $rlt['noscript'];
		if(!$noScriptDepositCondition){
			//required
			$depositTranId = isset($rlt['deposit_tran_id']) ? $rlt['deposit_tran_id'] : null;
			$transferTransId = isset($rlt['transferTransId']) ? $rlt['transferTransId'] : null;

			$extra_info['depositTranId'] = !empty($depositTranId) ? $depositTranId : null;
			$extra_info['transferTransId'] = !empty($transferTransId) ? $transferTransId : null;
		}
		$this->utils->debug_log('extra_info-1', $extra_info);
		$bonusAmount = 0;
		$withdrawConditionAmount=0;
		$betTimes=0;

		if (!empty($playerPromoId)) {
			$playerPromo = $this->player_promo->getPlayerPromo($playerPromoId);
			if (!empty($playerPromo)) {
				$bonusAmount = $playerPromo->bonusAmount;
				$withdrawConditionAmount = $playerPromo->withdrawConditionAmount;
			} else {
				//wrong id
				$playerPromoId = null;
			}
		}

		try {
			if($this->isTransferPromo($promorule)){

				$this->appendToDebugLog($extra_info['debug_log'],'is transfer promo');
				list($success, $message)=$this->checkTransferPromo($playerId, $promorule, $promoCmsSettingId, $preapplication,
					$noScriptDepositCondition, $playerPromoId, $extra_info, $dry_run);

			}elseif ($this->isDepositPromo($promorule)) {

				$this->appendToDebugLog($extra_info['debug_log'],'is deposit promo');
				list($success, $message)=$this->checkDepositPromo($playerId, $promorule, $promoCmsSettingId, $preapplication,
					$noScriptDepositCondition, $playerPromoId, $extra_info, $dry_run);

			} else {
				$this->appendToDebugLog($extra_info['debug_log'],'is other promo');
				list($success, $message) = $this->checkNonDepositPromo($playerId, $promorule, $promoCmsSettingId,
					$noScriptDepositCondition, $playerPromoId, $extra_info, $dry_run);

			}

		} catch (WrongBonusException $e) {
			$this->appendToDebugLog($extra_info['debug_log'],'wrong bonus exception', [
				'playerId'=>$playerId, 'promoruleid'=>$promorulesId, 'exception'=>$e,
			]);

			$this->utils->debug_log('wrong bonus exception', 'playerId', $playerId, 'promoruleid', $promorulesId, $e);
			$success = false;
			$message = 'promo_rule.common.error';
			if (!empty($e->error_message_lang)) {
				$message = $e->error_message_lang;
			}
		}
		return array($success, $message);
	}

	public function checkOnlyPromotionBeforeDeposit($playerId, $promorule, $promoCmsSettingId,
			$preapplication=false, $playerPromoId=null, &$extra_info=null, $dry_run=false) {

		$this->utils->debug_log('start check promotion', $playerId, 'promorule',
			@$promorule['promorulesId'], 'promoCmsSettingId', $promoCmsSettingId, 'preapplication', $preapplication,
			'playerPromoId', $playerPromoId, 'extra_info', $extra_info);

		$this->load->model(array('player_model', 'transactions', 'player_promo', 'total_player_game_hour'));

		$success = false;
		$message = 'promo_rule.common.error';
		$bonusAmount = 0;
		$depositAmount = 0;
		$withdrawConditionAmount=0;
		$betTimes=0;

		$promorulesId = $promorule['promorulesId'];
		$this->utils->debug_log(array(
			'playerId' => $playerId,
			'promorule' => $promorulesId,
			'promoCmsSettingId' => $promoCmsSettingId,
		));
		$adminId = $this->users->getSuperAdminId();
		if(empty($extra_info)){
			$extra_info=[];
		}
		$extra_info['is_checking_before_deposit']=true;
		//check if player level is valid in this promo
		$allowedFlag = $this->isAllowedPlayer($promorulesId, $promorule, $playerId);
		$this->appendToDebugLog($extra_info['debug_log'], 'allowedFlag:'.$allowedFlag);
		$this->utils->debug_log('allowedFlag', $allowedFlag);
		if ($allowedFlag == false) {
			//if playerlevel is invalid
			//Your player level is not valid in this promo! Please contact customer service for more information
			$message = 'notify.35';
			$success = false;
			return array($success, $message);
		}

		if($promorule['always_join_promotion']=='1'){
			//ignore check request count
		}else{

			//get playerPromoRequest
			$playerPromoRequestCount = $this->player_promo->countPlayerDuplicatePromo($playerId, $promorulesId,
				[Player_promo::TRANS_STATUS_REQUEST,
				Player_promo::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS,
				Player_promo::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS],
				null,null,null, $playerPromoId);
			$this->appendToDebugLog($extra_info['debug_log'], 'playerPromoRequestCount:'.$playerPromoRequestCount);
			$this->utils->debug_log(array('playerPromoRequestCount' => $playerPromoRequestCount));
			if ($playerPromoRequestCount > 0) {
				//if promo request exists
				//you have existing promo request already
				$message = 'notify.34';
				$success = false;
				return array($success, $message);
			}
		}

        //check daily request limit
        if(!empty($promorule['request_limit'])){
            $requestCount=$this->player_promo->getDailyPromoRequestByPlayerId($playerId, $promoCmsSettingId);
            if($requestCount>=$promorule['request_limit']){
                $message = 'Reached today request limit';
                $success = false;
                return [$success, $message];
            }
        }

        //check daily approved limit
        if(!empty($promorule['approved_limit'])){
            $requestCount=$this->player_promo->getDailyPromoApproved($promorulesId);
            if($requestCount>=$promorule['approved_limit']){
                $message = 'Reached today approved limit';
                $success = false;
                return [$success, $message];
            }
        }

        //check total approved limit
        if(!empty($promorule['total_approved_limit'])){
            $requestCount=$this->player_promo->getTotalPromoApproved($promorulesId);
            if($requestCount>=$promorule['total_approved_limit']){
                $message = 'Reached total approved limit';
                $success = false;
                return [$success, $message];
            }
        }

        //donot_allow_other_promotion is like group, radio button rule
        if($promorule['donot_allow_other_promotion']){
            if($this->player_promo->existsUnfinishedPromoAndDonotAllowOthers($playerId)){
                $this->appendToDebugLog($extra_info['debug_log'], 'existsUnfinishedPromoAndDonotAllowOthers, playerId:'.$playerId);

                $success=false;
                $message='notify.34';
                return array($success, $message);
            }
        }

        //donot_allow_other_promotion is like group, radio button rule
        if($promorule['dont_allow_request_promo_from_same_ips']){
            $player_ip = $this->CI->utils->getIp();
            if($this->player_promo->existsPlayerPromoFromSameIp($promorulesId, $player_ip)){
                $this->appendToDebugLog($extra_info['debug_log'], 'existsUnfinishedPromoAndDonotAllowOthers, playerId:'.$playerId);

                $success=false;
                $message='promo.dont_allow_request_promo_from_same_ips';
                return array($success, $message);
            }
        }

		if($preapplication){
			$success=true;
			$message='';
			return array($success, $message);
		}

		// } else {
		//if deposit promo is by application
		list($success, $message) = $this->validRepeatable($playerId, $promorule, $extra_info);
		$this->appendToDebugLog($extra_info['debug_log'], "is repeatable:".$success." message:".$message);
		$this->utils->debug_log("is repeatable:", $success, $message);
		if (!$success) {
			return array($success, $message);
		}

		//validate bonus condition
		$rlt = $this->validateCustomizedBonusCondition($playerId, $promorule, $extra_info, $dry_run);
		$this->appendToDebugLog($extra_info['debug_log'],'validateCustomizedBonusCondition:'.var_export($rlt, true));
		if (!$rlt['success']) {
			return array($rlt['success'], $rlt['message']);
		}
        if(isset($rlt['transaction_list'])){
            $extra_info['transaction_list']=$rlt['transaction_list'];
        }
		if(isset($rlt['continue_process_after_script'])){
			$extra_info['continue_process_after_script']=$rlt['continue_process_after_script'];
		}

		$noScriptDepositCondition = isset($rlt['noscript']) && $rlt['noscript'];
		if(!$noScriptDepositCondition){
			//required
			$depositTranId = isset($rlt['deposit_tran_id']) ? $rlt['deposit_tran_id'] : null;
			$transferTransId = isset($rlt['transferTransId']) ? $rlt['transferTransId'] : null;

			$extra_info['depositTranId'] = !empty($depositTranId) ? $depositTranId : null;
			$extra_info['transferTransId'] = !empty($transferTransId) ? $transferTransId : null;
		}
		$this->utils->debug_log('extra_info-1', $extra_info);
		$bonusAmount = 0;
		$withdrawConditionAmount=0;
		$betTimes=0;

		if (!empty($playerPromoId)) {
			$playerPromo = $this->player_promo->getPlayerPromo($playerPromoId);
			if (!empty($playerPromo)) {
				$bonusAmount = $playerPromo->bonusAmount;
				$withdrawConditionAmount = $playerPromo->withdrawConditionAmount;
			} else {
				//wrong id
				$playerPromoId = null;
			}
		}

		try {
			if ($this->isDepositPromo($promorule)) {
				$this->appendToDebugLog($extra_info['debug_log'],'is deposit promo');
				list($success, $message)=$this->checkDepositPromoBeforeDeposit($playerId, $promorule, $promoCmsSettingId, $preapplication,
					$noScriptDepositCondition, $playerPromoId, $extra_info, $dry_run);
			}

		} catch (WrongBonusException $e) {
			$this->appendToDebugLog($extra_info['debug_log'],'wrong bonus exception', [
				'playerId'=>$playerId, 'promoruleid'=>$promorulesId, 'exception'=>$e,
			]);

			$this->utils->debug_log('wrong bonus exception', 'playerId', $playerId, 'promoruleid', $promorulesId, $e);
			$success = false;
			$message = 'promo_rule.common.error';
			if (!empty($e->error_message_lang)) {
				$message = $e->error_message_lang;
			}
		}
		return array($success, $message);
	}

	public function checkDepositPromoBeforeDeposit($playerId, $promorule, $promoCmsSettingId,
			$preapplication, $noScriptDepositCondition, $playerPromoId, &$extra_info, $dry_run=false){

		$bonusAmount = 0;
		$withdrawConditionAmount=0;
		$betTimes=0;
		$promorulesId=$promorule['promorulesId'];

		//get deposit amount
		$depositAmount = @$extra_info['depositAmount'];
		$tranId = null;
        $depositTranId= isset($extra_info['depositTranId']) ? $extra_info['depositTranId'] : null;
        $checkSuccess = false;
        // always overwrite depositTranId
		if (!empty($depositTranId)) {
			$tranRow = $this->transactions->getTransaction($depositTranId);
			$depositAmount = $tranRow->amount;
			$tranId = $tranRow->id;
			// $playerCurrentDepositSuccesionCnt = 1;
			// } else {
		}

		$this->appendToDebugLog($extra_info['debug_log'],'getValidDepositAmount', [
			'depositAmount'=>$depositAmount, 'noScriptDepositCondition'=>$noScriptDepositCondition,
			'tranId'=>$tranId,
		]);
		$this->utils->debug_log('getValidDepositAmount', $depositAmount, 'noScriptDepositCondition', $noScriptDepositCondition, 'tranId', $tranId);

		$checkMessage = '';
		if ($noScriptDepositCondition) {

			if ($promorule['depositSuccesionType'] == self::DEPOSIT_SUCCESION_TYPE_NOT_FIRST) {
				//found transaction or force
				if(empty($tranId)){
					$from_type_arr = $this->getPromotionRules('from_date_order');
					$fromDatetime = $this->utils->getLastFromDatetime($from_type_arr, $playerId, $promorulesId);
					$tran = $this->getTranValidDepositAmount($playerId, $promorule, $fromDatetime);
					//have to be at last one transaction record
					if (!empty($tran)) {
						$checkSuccess = true;
					}
				}
			} elseif ($promorule['depositSuccesionType'] == self::DEPOSIT_SUCCESION_TYPE_EVERY_TIME) {
				//found transaction or force
				if(empty($tranId)){
					//every time
					$checkSuccess = true;
				}
			} else {
				//found transaction or force
				if(empty($tranId)){
					if ($promorule['depositSuccesionType'] == self::DEPOSIT_SUCCESION_TYPE_FIRST) {
						$depositSuccesionCnt = 1;
					} else {
						$depositSuccesionCnt = $promorule['depositSuccesionCnt'];
					}
					$tran = $this->getAvailableDepositTran($playerId, $promorule, $depositSuccesionCnt, NULL, $extra_info);
					$this->utils->debug_log('-- check promo ==', $playerId, $promorule, $depositSuccesionCnt, $tran);
					if (empty($tran)) {
						$checkSuccess = true;
					}
				}
			}

			if ($checkSuccess) {
			} else {
				//Required deposit count did not met!
				$message = 'notify.80';
				$success = false;
				return array($success, $message);
			}
		}

		//deposit amount rule
		if ($promorule['depositConditionNonFixedDepositAmount'] == self::NON_FIXED_DEPOSIT_MIN_MAX) {
			if ($depositAmount >= $promorule['nonfixedDepositMinAmount'] && $depositAmount <= $promorule['nonfixedDepositMaxAmount']) {
			} else {
				$message = 'notify.37';
				$success = false;
				return array($success, $message);
			}
		}

		if (empty($bonusAmount)) {
			//first tran id
			// $tranId = null;
			// if (!empty($tranIds)) {
			// 	$tranId = $tranIds[0];
			// }
			$errorMessageLang=null;
			$bonusAmount = $this->getBonusAmount($promorule, $depositAmount, $playerId, $errorMessageLang, $extra_info, $dry_run);
		}

		if (empty($withdrawConditionAmount)) {
			$withdrawConditionAmount = $this->getWithdrawCondAmount($promorule, $bonusAmount,
				$depositAmount, $playerId, $tranId, $betTimes);
		}

		$this->appendToDebugLog($extra_info['debug_log'], 'check promotion '.$promorulesId,[
			'bonusAmount'=> $bonusAmount,
			'depositAmount'=> $depositAmount,
			'withdrawConditionAmount'=> $withdrawConditionAmount,
			'betTimes'=> $betTimes
		]);
		$this->utils->debug_log('check promotion '.$promorulesId.' result ', $playerId,
			'bonusAmount', $bonusAmount, 'depositAmount', $depositAmount, 'withdrawConditionAmount', $withdrawConditionAmount,
			'betTimes', $betTimes);

		$extra_info['bonusAmount']=$bonusAmount;
		$extra_info['depositAmount']=$depositAmount;
		$extra_info['withdrawConditionAmount']=$withdrawConditionAmount;
		$extra_info['betTimes']=$betTimes;
		$extra_info['depositTranId']=$tranId;

		$success=true;
		$message='notify.36';

		return array($success, $message);
	}

	private function process_mock($dry_run, $extra_info, $name, &$val){

		if($dry_run && isset($extra_info['mock'][$name])){
			$val=$extra_info['mock'][$name];
			return true;
		}

		return false;
	}

	/**
	 * overview : check deposit promo
	 *
	 * @param int	$playerId
	 * @param int	$promorule
	 * @param int	$promoCmsSettingId
	 * @param int	$preapplication
	 * @param $noScriptDepositCondition
	 * @param $playerPromoId
	 * @param $extra_info
	 * @return array
	 */
	public function checkDepositPromo($playerId, $promorule, $promoCmsSettingId,
			$preapplication, $noScriptDepositCondition, $playerPromoId, &$extra_info, $dry_run=false){

		$bonusAmount = 0;
		$withdrawConditionAmount=0;
		$betTimes=0;
		$promorulesId=$promorule['promorulesId'];

		//get deposit amount
		$depositAmount = 0;
		$tranId = null;

		$continue_process_after_script=isset($extra_info['continue_process_after_script']) && $extra_info['continue_process_after_script'];

        if(!$continue_process_after_script){
            $continue_process_after_script = $noScriptDepositCondition;
        }

        $this->appendToExtraInfoDebugLog($extra_info, 'checkDepositPromo continue_process_after_script', [
        	'continue_process_after_script'=>$continue_process_after_script,
        	'noScriptDepositCondition'=>$noScriptDepositCondition,
        ]);

		if ($continue_process_after_script) {
            $check_deposit_transaction_result = $this->checkPromoWithDepositTransaction($playerId, $promorule,
                $promoCmsSettingId, $preapplication, $noScriptDepositCondition, $extra_info, $dry_run);

            if($check_deposit_transaction_result[0]){
                $tranRow = $check_deposit_transaction_result[1];
                $depositAmount = $tranRow->amount;
                $tranId = $tranRow->id;
            }else{
                return [$check_deposit_transaction_result[0], $check_deposit_transaction_result[1]];
            }

            //deposit amount rule
            if ($promorule['depositConditionNonFixedDepositAmount'] == self::NON_FIXED_DEPOSIT_MIN_MAX) {
                if ($depositAmount >= $promorule['nonfixedDepositMinAmount'] && $depositAmount <= $promorule['nonfixedDepositMaxAmount']) {
                } else {
                    $message = 'notify.37';
                    $success = false;
                    return array($success, $message);
                }
            }
		}elseif(isset($extra_info['depositTranId'])){
            $tranRow = $this->transactions->getTransaction($extra_info['depositTranId']);
            if(empty($tranRow)){
                return [FALSE, 'notify.80'];
            }

            if($this->transactions->isAppliedPromo($tranRow)){
                return [FALSE, 'notify.34'];
            }

            $depositAmount = $tranRow->amount;
            $tranId = $tranRow->id;
        }

		if (empty($bonusAmount)) {
			//first tran id
			// $tranId = null;
			// if (!empty($tranIds)) {
			// 	$tranId = $tranIds[0];
			// }
			$errorMessageLang=null;
			$bonusAmount = $this->getBonusAmount($promorule, $depositAmount, $playerId, $errorMessageLang, $extra_info, $dry_run);
			if(empty($bonusAmount)){
			    return [FALSE, $errorMessageLang];
            }
		}

        if(empty($withdrawConditionAmount)){
            $withdrawConditionAmount = $this->getWithdrawCondAmount($promorule, $bonusAmount, $depositAmount, $playerId, $tranId, $betTimes);
        }

		$this->appendToExtraInfoDebugLog($extra_info, 'check promotion '.$promorulesId,[
			'bonusAmount'=> $bonusAmount,
			'depositAmount'=> $depositAmount,
			'withdrawConditionAmount'=> $withdrawConditionAmount,
			'betTimes'=> $betTimes
		]);
		$this->utils->debug_log('check promotion '.$promorulesId.' result ', $playerId,
			'bonusAmount', $bonusAmount, 'depositAmount', $depositAmount, 'withdrawConditionAmount', $withdrawConditionAmount,
			'betTimes', $betTimes);

        $extra_info['bonusAmount'] = $bonusAmount;
        $extra_info['depositAmount'] = $depositAmount;
        $extra_info['withdrawConditionAmount'] = $withdrawConditionAmount;
        $extra_info['betTimes'] = $betTimes;
        $extra_info['depositTranId'] = $tranId;

		$success=true;
		$message='notify.36';

		return array($success, $message);
	}

	public function checkPromoWithDepositTransaction($playerId, $promorule, $promoCmsSettingId,
            $preapplication, $noScriptDepositCondition, &$extra_info = NULL, $dry_run=false){

        $this->appendToExtraInfoDebugLog($extra_info, 'checkPromoWithDepositTransaction', [
            'noScriptDepositCondition'=>$noScriptDepositCondition,
            'promorule'=>empty($promorule),
        ]);
        $this->utils->debug_log('checkPromoWithDepositTransaction','noScriptDepositCondition', $noScriptDepositCondition);

	    if(empty($promorule)){
            return [FALSE, 'notify.promo_not_exists'];
        }

        $promorulesId = $promorule['promorulesId'];

        $depositAmount = 0;
        $depositTranId = isset($extra_info['depositTranId']) ? $extra_info['depositTranId'] : NULL;
        $tranRow = NULL;
        $tranId = NULL;

        // always overwrite depositTranId
        if (!empty($depositTranId)) {
            $tranRow = $this->transactions->getTransaction($depositTranId);
            $depositAmount = $tranRow->amount;
            $tranId = $tranRow->id;
            // $playerCurrentDepositSuccesionCnt = 1;
            // } else {
        }

        $this->appendToExtraInfoDebugLog($extra_info, 'checkPromoWithDepositTransaction', [
            'depositAmount'=>$depositAmount, 'noScriptDepositCondition'=>$noScriptDepositCondition,
            'tranId'=>$tranId,
        ]);
        $this->utils->debug_log('checkPromoWithDepositTransaction', $depositAmount, 'noScriptDepositCondition', $noScriptDepositCondition, 'tranId', $tranId);

        if ($promorule['depositSuccesionType'] == self::DEPOSIT_SUCCESION_TYPE_NOT_FIRST) {
            //found transaction or force
            if(empty($tranId)){
//                $from_type_arr = $this->getPromotionRules('from_date_order');
                // $from_type_arr = ['player_reg_date', 'last_withdraw', 'last_transfer'];
//                $fromDatetime = $this->utils->getLastFromDatetime($from_type_arr, $playerId, $promorulesId);

//                $tran = $this->getTranValidDepositAmount($playerId, $promorule);

                $tran = $this->getAvailableDepositTran($playerId, $promorule, 2, NULL, $extra_info);
                if(!empty($tran)){
                    $tranRow = $tran;
                    $depositAmount = $tranRow->amount;
                    $tranId = $tranRow->id;
                    // $playerCurrentDepositSuccesionCnt = count($tranList);
                }
            }

            //mock overwrite
            $mocked=$this->process_mock($dry_run, $extra_info, 'not_first_time_deposit_transaction_id', $tranId);
            if($mocked){
            	if(empty($tranRow)){
            		$tranRow=(object)['id'=>$tranId];
            	}
            	$tranRow->id=$tranId;
            }
            $mocked=$this->process_mock($dry_run, $extra_info, 'not_first_time_deposit_amount', $depositAmount);
            if($mocked){
            	if(empty($tranRow)){
            		$tranRow=(object)['amount'=>$depositAmount];
            	}
            	$tranRow->amount=$depositAmount;
            }

            $this->appendToDebugLog($extra_info['debug_log'],'not first', [
                'depositSuccesionType' => $promorule['depositSuccesionType'],
                'tranId' => $tranId,
                'depositAmount' => $depositAmount,
            ]);
            // $totalDepositSuccesionCnt = $this->countDepositSuccesion($playerId, $promorule);
            // $this->utils->debug_log('totalDepositSuccesionCnt', $totalDepositSuccesionCnt, 'playerCurrentDepositSuccesionCnt', $playerCurrentDepositSuccesionCnt);
            $this->utils->debug_log(array(
                // 'playerCurrentDepositSuccesionCnt' => $playerCurrentDepositSuccesionCnt,
                'depositSuccesionType' => $promorule['depositSuccesionType'],
                'tranId' => $tranId,
                'depositAmount' => $depositAmount,
            ));

            // if ($playerCurrentDepositSuccesionCnt >= 1 && $totalDepositSuccesionCnt > 1) {

        } elseif ($promorule['depositSuccesionType'] == self::DEPOSIT_SUCCESION_TYPE_EVERY_TIME) {

            //found transaction or force
            if(empty($tranId)){
                //every time
//                $from_type_arr = ['player_reg_date', 'last_withdraw', 'last_transfer'];
//                $fromDatetime = $this->utils->getLastFromDatetime($from_type_arr, $playerId, $promorulesId);

                // $tran = $this->getTranValidDepositAmount($playerId, $promorule);

                $tran = $this->getAvailableDepositTran($playerId, $promorule, -1, NULL, $extra_info);
                if (!empty($tran)) {
                    $tranRow = $tran;
                    $depositAmount = $tranRow->amount;
                    $tranId = $tranRow->id;
                    // $playerCurrentDepositSuccesionCnt = count($tranList);
                }
            }

            //mock overwrite
            $mocked=$this->process_mock($dry_run, $extra_info, 'every_time_deposit_transaction_id', $tranId);
            if($mocked){
            	if(empty($tranRow)){
            		$tranRow=(object)['id'=>$tranId];
            	}
            	$tranRow->id=$tranId;
            }
            $mocked=$this->process_mock($dry_run, $extra_info, 'every_time_deposit_amount', $depositAmount);
            if($mocked){
            	if(empty($tranRow)){
            		$tranRow=(object)['amount'=>$depositAmount];
            	}
            	$tranRow->amount=$depositAmount;
            }

            $this->appendToDebugLog($extra_info['debug_log'], 'every time',[
                'depositSuccesionType' => $promorule['depositSuccesionType'],
                'tranId' => $tranId,
                'depositAmount' => $depositAmount,
            ]);
            // $totalDepositSuccesionCnt = $this->countDepositSuccesion($playerId, $promorule);
            // $this->utils->debug_log('totalDepositSuccesionCnt', $totalDepositSuccesionCnt, 'playerCurrentDepositSuccesionCnt', $playerCurrentDepositSuccesionCnt);
            $this->utils->debug_log(array(
                // 'playerCurrentDepositSuccesionCnt' => $playerCurrentDepositSuccesionCnt,
                'depositSuccesionType' => $promorule['depositSuccesionType'],
                'tranId' => $tranId,
                'depositAmount' => $depositAmount,
            ));

        } else {
            //found transaction or force
            if(empty($tranId)){
                if($promorule['depositSuccesionType'] == self::DEPOSIT_SUCCESION_TYPE_FIRST){
                    $depositSuccesionCnt = 1;
                }else{
                    $depositSuccesionCnt = $promorule['depositSuccesionCnt'];
                }

                $tran = $this->getAvailableDepositTran($playerId, $promorule, $depositSuccesionCnt, NULL, $extra_info);
                if(!empty($tran)){
                    $tranRow = $tran;
                    $depositAmount = $tranRow->amount;
                    $tranId = $tranRow->id;
                    // $playerCurrentDepositSuccesionCnt = count($tranList);
                }

                if(isset($extra_info['error_message']) && !empty($extra_info['error_message'])){
                    return [FALSE, $extra_info['error_message']];
                }
            }

            //mock overwrite
            $mocked=$this->process_mock($dry_run, $extra_info, 'fixed_time_deposit_transaction_id', $tranId);
            if($mocked){
            	if(empty($tranRow)){
            		$tranRow=(object)['id'=>$tranId];
            	}
            	$tranRow->id=$tranId;
            }
            $mocked=$this->process_mock($dry_run, $extra_info, 'fixed_time_deposit_amount', $depositAmount);
            if($mocked){
            	if(empty($tranRow)){
            		$tranRow=(object)['amount'=>$depositAmount];
            	}
            	$tranRow->amount=$depositAmount;
            }

            $this->appendToDebugLog($extra_info['debug_log'], 'first time',[
                'depositSuccesionType' => $promorule['depositSuccesionType'],
                'tranId' => $tranId,
                'depositAmount' => $depositAmount,
            ]);
            // $playerCurrentDepositSuccesionCnt = $this->countDepositSuccesion($playerId, $promorule);
            $this->utils->debug_log(array(
                // 'playerCurrentDepositSuccesionCnt' => $playerCurrentDepositSuccesionCnt,
                'depositSuccesionType' => $promorule['depositSuccesionType'],
                'tranId' => $tranId,
                'depositAmount' => $depositAmount,
            ));

            //if deposit succesion condition more than total 3 deposit cnt
        }

        return ($tranId) ? [TRUE, $tranRow] : [FALSE, 'notify.80'];
    }

	/**
	 *
	 */
	public function checkTransferPromo($playerId, $promorule, $promoCmsSettingId,
			$preapplication, $noScriptDepositCondition, $playerPromoId, &$extra_info, $dry_run=false){

		$bonusAmount = 0;
		$withdrawConditionAmount=0;
		$betTimes=0;
		$promorulesId=$promorule['promorulesId'];

		$transferAmount = @$extra_info['transferAmount'];
		$transferTransId = @$extra_info['transferTransId'];
		$transferCreateAt = @$extra_info['transferCreateAt'];
		$subWalletId= @$extra_info['subWalletId'];

		// if(empty($transferAmount) || empty($transferTransId) || empty($subWalletId)){
			//try search avail transfer id
			// $this->getAvailableTransferTran();
		// }

		if(empty($transferAmount) || empty($transferTransId) || empty($subWalletId)){
			//search last transfer trans id
			$this->load->model(['transactions']);
			$transRow=$this->transactions->searchLastTransfer($playerId);

			$this->utils->debug_log('transRow', $transRow);
			if($this->transactions->isAppliedPromo($transRow)){
				$transRow=null;
				return [FALSE, 'notify.33'];
			}

			if(empty($transRow)){
				$this->utils->error_log('empty anyone transferTransId', $transferTransId, 'transferAmount', $transferAmount, 'subWalletId', $subWalletId);
				// $message = 'Only trigger on transfer sub-wallet';
				$message = 'Please deposit to target sub-wallet first';
				$success = false;
				return array($success, $message);
			}

			$transferAmount=$transRow['amount'];
			$transferTransId=$transRow['id'];
			$transferCreateAt=$transRow['created_at'];
			$subWalletId=$transRow['sub_wallet_id'];

			$extra_info['transferAmount']=$transferAmount;
			$extra_info['transferTransId']=$transferTransId;
			$extra_info['subWalletId']=$subWalletId;
		}

		//check subwallet id
		$trigger_wallets=$promorule['trigger_wallets'];
		$trigger_wallets_arr=[];
		if(!empty($trigger_wallets)){
			$trigger_wallets_arr=explode(',',$trigger_wallets);
		}
		if(!in_array($subWalletId, $trigger_wallets_arr)){
			$this->utils->error_log('subWalletId should be ', $trigger_wallets_arr ,'current',$subWalletId);
			// $message = 'Only trigger on transfer right sub-wallet';
			$message = 'Please deposit to target sub-wallet first';
			$success = false;
			return array($success, $message);
		}

		if (!$noScriptDepositCondition && !empty($transferTransId)) {
			$tranRow = $this->transactions->getTransaction($transferTransId);
			$transferAmount = $tranRow->amount;
			$transferTransId = $tranRow->id;
            $transferCreateAt = $tranRow->created_at;
		}
		$this->appendToDebugLog($extra_info['debug_log'],
			['transferAmount'=> $transferAmount, 'noScriptDepositCondition'=> $noScriptDepositCondition,
			'transferTransId'=> $transferTransId]
		);
		$this->utils->debug_log('transferAmount', $transferAmount, 'noScriptDepositCondition', $noScriptDepositCondition, 'transferTransId', $transferTransId);

		//load minAmount,maxAmount, periodFrom, periodTo
		// $minAmount = null;
		// $maxAmount = null;
		// if ($promorule['depositConditionNonFixedDepositAmount'] == self::NON_FIXED_DEPOSIT_MIN_MAX) {
		// 	$minAmount = $promorule['nonfixedDepositMinAmount'];
		// 	$maxAmount = $promorule['nonfixedDepositMaxAmount'];
		// }

		//from applicationPeriodStart to now
        $this->load->model(array('transactions', 'player_model'));
//		$depositSuccesionPeriod = $promorule['depositSuccesionPeriod'];
//		if ($depositSuccesionPeriod == self::DEPOSIT_SUCCESION_PERIOD_START_FROM_REG) {
//			//from registration
//			$periodFrom = $this->player_model->getPlayerRegisterDate($playerId);
//			$periodTo = $this->getNowForMysql();
//		} else {
//			// ($depositSuccesionPeriod == self::DEPOSIT_SUCCESION_PERIOD_BONUS_EXPIRE) {
//			//from start to hide date
//			$periodFrom = $promorule['applicationPeriodStart'];
//			$periodTo = $promorule['hide_date'];
//		}
		// if (!empty($fromDatetime)) {
		// 	$periodFrom = $fromDatetime;
		// }

        if ($noScriptDepositCondition) {
            $check_deposit_transaction_result = $this->checkPromoWithDepositTransaction($playerId, $promorule,
                $promoCmsSettingId, $preapplication, $noScriptDepositCondition, $extra_info, $dry_run);

            if($check_deposit_transaction_result[0]){
                $deposit_tranRow = $check_deposit_transaction_result[1];
                $tranId = $deposit_tranRow->id;
            }else{
                return [$check_deposit_transaction_result[0], $check_deposit_transaction_result[1]];
            }

            $saleOrder = $this->sale_order->getSaleOrderWithPlayerById($deposit_tranRow->order_id);

            if(empty($saleOrder)){
                $this->utils->debug_log('checkPromoWithDepositTransaction not found saleOrder', $check_deposit_transaction_result, 'noScriptDepositCondition', $noScriptDepositCondition, 'transferTransId', $transferTransId);
                return [FALSE, 'notify.80'];
            }

            // Check transfer needs to be done after the deposit
            if(strtotime($transferCreateAt) < strtotime($saleOrder->processed_approved_time)){
                $message = 'Please deposit to target sub-wallet first';
                $success = false;
                return array($success, $message);
            }

            $extra_info['saleOrder'] = $saleOrder;
        }

        if($promorule['donot_allow_any_transfer_in_after_transfer']){
            if($this->transactions->existsTransByTypesAfter($transferCreateAt, $playerId, [Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET])){
                $extra_info['error_message']='notify.promo_donot_allow_any_transfer_in_after_transfer';

                $this->appendToDebugLog($extra_info['debug_log'], 'TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET promo_donot_allow_any_transfer_in_after_transfer',
                    ['created_at'=>$transferCreateAt, 'playerId'=>$playerId]);

                return true;
            }
        }

        if($promorule['donot_allow_any_transfer_out_after_transfer']){
            if($this->transactions->existsTransByTypesAfter($transferCreateAt, $playerId, [Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET])){
                $extra_info['error_message']='notify.promo_donot_allow_any_transfer_out_after_transfer';

                $this->appendToDebugLog($extra_info['debug_log'], 'TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET promo_donot_allow_any_transfer_out_after_transfer',
                    ['created_at'=>$transferCreateAt, 'playerId'=>$playerId]);

                return true;
            }
        }

		//transfer amount rule
		if ($promorule['depositConditionNonFixedDepositAmount'] == self::NON_FIXED_DEPOSIT_MIN_MAX) {
			if ($transferAmount >= $promorule['nonfixedDepositMinAmount'] && $transferAmount <= $promorule['nonfixedDepositMaxAmount']) {
			} else {
				$message = 'notify.37';
				$success = false;
				return array($success, $message);
			}
		}

		$depositAmount = $transferAmount;
		// $bonusAmount = 0;
		// $withdrawConditionAmount=0;
		// $betTimes=0;

		if (empty($bonusAmount)) {
			//first tran id
			// $tranId = null;
			// if (!empty($tranIds)) {
			// 	$tranId = $tranIds[0];
			// }
			$errorMessageLang=null;
			$bonusAmount = $this->getBonusAmount($promorule, $depositAmount, $playerId, $errorMessageLang, $extra_info, $dry_run);
		}

		if (empty($withdrawConditionAmount)) {
			$withdrawConditionAmount = $this->getWithdrawCondAmount($promorule, $bonusAmount,
				$depositAmount, $playerId, $transferTransId, $betTimes);
		}

		$this->appendToDebugLog($extra_info['debug_log'], 'check promotion '.$promorulesId, [
			'playerId'=>$playerId,
			'bonusAmount', $bonusAmount, 'depositAmount', $depositAmount, 'withdrawConditionAmount', $withdrawConditionAmount,
			'betTimes', $betTimes
			]
		);
		$this->utils->debug_log('check promotion '.$promorulesId.' result ', $playerId,
			'bonusAmount', $bonusAmount, 'depositAmount', $depositAmount, 'withdrawConditionAmount', $withdrawConditionAmount,
			'betTimes', $betTimes);

        $success = TRUE;
        $message = 'notify.36';

        $extra_info['bonusAmount'] = $bonusAmount;
        $extra_info['depositAmount'] = $depositAmount;
        $extra_info['withdrawConditionAmount'] = $withdrawConditionAmount;
        $extra_info['betTimes'] = $betTimes;
        $extra_info['transferTransId'] = $transferTransId;

		return array($success, $message);
	}

	/**
	 * overview : check non deposit promo
	 *
	 * @param int	$playerId
	 * @param int	$promorule
	 * @param int	$promoCmsSettingId
	 * @param $noScriptDepositCondition
	 * @param int	$playerPromoId
	 * @param null $extra_info
	 * @return array
	 */
	public function checkNonDepositPromo($playerId, $promorule, $promoCmsSettingId, $noScriptDepositCondition, $playerPromoId,
		&$extra_info=null, $dry_run=false) {
		$this->load->model(array('users', 'wallet_model', 'transactions', 'player_model', 'total_player_game_hour', 'game_logs'));

		$success = false;
		$message = 'error.default.message';

		//always fixed
		$playerBonusAmount = $this->getBonusAmount($promorule, null, $playerId, $errorMessageLang, $extra_info, $dry_run); // $promorule['bonusAmount'];
		$bonusAmount = $playerBonusAmount;
		$betTimes=null;
		$withdrawConditionAmount=$this->getWithdrawCondAmount($promorule, $playerBonusAmount, null, $playerId, null, $betTimes);

		$promoType = $promorule['nonDepositPromoType'];
		$promorulesId = $promorule['promorulesId'];
		$adminId = $this->users->getSuperAdminId();

		$this->appendToDebugLog($extra_info['debug_log'], 'checkNonDepositPromo', ['playerId'=> $playerId, 'promoType'=> $promoType]);
		$this->utils->debug_log('playerId', $playerId, 'promoType', $promoType);

		switch ($promoType) {
		case self::NON_DEPOSIT_PROMO_TYPE_EMAIL: //by email confirmation
			//approved promo
			$conditionResult = true;
			if ($noScriptDepositCondition) {
				$conditionResult = $this->player_model->isVerifiedEmail($playerId);
			}
			//check email first
			if ($conditionResult) {

				// $this->approvePromo($playerId, $promorule, $promoCmsSettingId, $adminId);
				//wait for confirm email
				if ($this->isAutoRelease($promorule)) {
					// if ($promorule['bonusReleaseToPlayer'] == self::BONUS_RELEASE_TO_PLAYER_AUTO) {
					//auto approve
					// $this->approvePromo($playerId, $promorule, $promoCmsSettingId, $adminId, null, null, $playerPromoId, $extra_info);
					$message = 'notify.90';
				} else {
					// if ($approveProcessPendingFlag) {
					// 	$playerdepositpromoId = $this->approvePendingRequestWithoutBonusRelease($playerId, $promorule, 0, $promoCmsSettingId, $adminId);
					// 	$this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
					// 	$message = 'Promo has been approved without bonus release to player.';
					// } else {
						// $playerdepositpromoId = $this->requestPromo($playerId, $promorule, 0, $promoCmsSettingId, $adminId, null);
						// $this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
						//Your promo application has been sent!
						//save approved promoId to session
						$message = 'notify.36';
					// }

				}
				$success = true;
			} else {
				// $message = 'Your promo application has been declined due to unconfirmed email.';
				// $this->declinePromo($playerId, $promorule, $promoCmsSettingId, $adminId, null, $message);
				$message = 'Please confirm your email.';
				$success = false;

			}

			//Your promo application has been approved, confirm the link sent to your email to get your bonus
			// $message = 'notify.90';
			// $this->alertMessage(2, $message);
			break;

		case self::NON_DEPOSIT_PROMO_TYPE_MOBILE: //mobile confirmation

			$conditionResult = true;
			if ($noScriptDepositCondition) {
				$conditionResult = $this->player_model->isVerifiedPhone($playerId);
			}
			// $success = false;
			if ($conditionResult) {
				if ($this->isAutoRelease($promorule)) {
					// if ($promorule['bonusReleaseToPlayer'] == self::BONUS_RELEASE_TO_PLAYER_AUTO) {
					//auto approve
					// $this->approvePromo($playerId, $promorule, $promoCmsSettingId, $adminId, null, null, $playerPromoId, $extra_info);
					$message = 'notify.90';
				} else {
					#TODO CHECK APPROVE PROMO
					// if ($approveProcessPendingFlag) {
					// 	$playerdepositpromoId = $this->approvePendingRequestWithoutBonusRelease($playerId, $promorule, 0, $promoCmsSettingId, $adminId);
					// 	$this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
					// 	$message = 'Promo has been approved without bonus release to player.';
					// } else {
						// $playerdepositpromoId = $this->requestPromo($playerId, $promorule, 0, $promoCmsSettingId, $adminId, null);
						// $this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
						//Your promo application has been sent!
						//save approved promoId to session
						$message = 'Your promo application has been declined due to unconfirmed mobile.';
					// }
				}
				$success = true;
			} else {
				// $message = 'Your promo application has been declined due to unconfirmed mobile.';
				// $this->declinePromo($playerId, $promorule, $promoCmsSettingId, $adminId, null, $message);

				$message = 'notify.93';
				$success = false;
			}
			break;

		case self::NON_DEPOSIT_PROMO_TYPE_REGISTRATION: //registration promo
			$conditionResult = true;
			if ($noScriptDepositCondition) {
				$conditionResult = $this->player_model->isEnabled($playerId);
			}

			if ($conditionResult) {
				if ($this->isAutoRelease($promorule)) {
					//approved promo
					// $this->approvePromo($playerId, $promorule, $promoCmsSettingId, $adminId, null, null, $playerPromoId, $extra_info);
					//Your promo application has been approved bonus amount was added to your main wallet account
					$message = 'notify.90';
				} else {
					// if ($approveProcessPendingFlag) {
					// 	$playerdepositpromoId = $this->approvePendingRequestWithoutBonusRelease($playerId, $promorule, 0, $promoCmsSettingId, $adminId);
					// 	$this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
					// 	$message = 'Promo has been approved without bonus release to player.';
					// } else {
						// $playerdepositpromoId = $this->requestPromo($playerId, $promorule, 0, $promoCmsSettingId, $adminId, null);
						// $this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
						//Your promo application has been sent!
						//save approved promoId to session
						$message = 'notify.36';
					// }
				}
				$success = true;
			} else {
				$message = 'notify.93';
				$success = false;

			}
			break;

		case self::NON_DEPOSIT_PROMO_TYPE_COMPLETE_PLAYER_INFO: //complete registration promo
			$conditionResult = true;
			if ($noScriptDepositCondition) {
				// $conditionResult = $this->player_model->getPlayerRegistrationStatus($playerId);
				$conditionResult = $this->player_model->getPlayerAccountInfoStatus($playerId);
                $conditionResultStatus = $conditionResult['status'];
                $conditionResultMissingFields = $conditionResult['missing_fields'];
			}

			// $isPlayerCompleteRegistrationFlag = $this->player_model->getPlayerRegistrationStatus($playerId);
			if ($conditionResultStatus) {
				if ($this->isAutoRelease($promorule)) {
					//complete registration
					//approved promo
					// $this->approvePromo($playerId, $promorule, $promoCmsSettingId, $adminId, null, null, $playerPromoId, $extra_info);

					//Your promo application has been approved bonus amount was added to your main wallet account
					$message = 'notify.90';
					// $this->alertMessage(2, $message);
					$success = true;
				} else {
					// if ($approveProcessPendingFlag) {
					// 	$playerdepositpromoId = $this->approvePendingRequestWithoutBonusRelease($playerId, $promorule, 0, $promoCmsSettingId, $adminId);
					// 	$this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
					// 	$message = 'Promo has been approved without bonus release to player.';
					// } else {
						// $playerdepositpromoId = $this->requestPromo($playerId, $promorule, 0, $promoCmsSettingId, $adminId, null);
						// $this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
						//Your promo application has been sent!
						//save approved promoId to session
						$message = 'notify.36';
						$success = true;
					// }
				}
			} else {
				//player registration is incomplete
				//decline promo
				// $this->declinePromo($playerId, $promorule, $promoCmsSettingId, $adminId);
				//Your promo application has been declined due to incomplete registration
                $message = 'notify.93';

                $extra_info['error_redirect_url'] = $this->utils->getPlayerProfileSetupUrl();
                /*
                 OGP-7925
                 ## show which fields are not filled with it
                $message = '';
                foreach($conditionResultMissingFields as $missingFields){
                    $message .= lang('reg.fields.'.$missingFields).'/';
                }
                $message = rtrim($message,'/');
                */
				// $this->alertMessage(2, $message);
				$success = false;
			}
			break;
		case self::NON_DEPOSIT_PROMO_TYPE_RESCUE: //rescue
			$conditionResult = true;
			if ($noScriptDepositCondition) {

				// $success = true;
				$rescue_promotion_amount = $this->utils->getConfig('rescue_promotion_amount');
				$totalBalance = $this->wallet_model->getTotalBalance($playerId);

				$bonusAmount = $this->getBonusAmount($promorule, null, $playerId, $errorMessageLang, $extra_info, $dry_run);

				$conditionResult = $totalBalance <= $rescue_promotion_amount && $bonusAmount > 0;
				// && $this->transactions->existsDepositByPlayer($playerId);

			}
			// $bonusAmount = $this->getBonusAmount($promorule, null, $playerId);

			if (!$conditionResult) {
				$this->declinePromo($playerId, $promorule, $promoCmsSettingId, $adminId);
				$message = 'promo.request_rescue.decliend';
				if (!empty($errorMessageLang)) {
					$message = $errorMessageLang;
				}
				$success = false;
			} else {

				if ($this->isAutoRelease($promorule)) {
					// if ($promorule['bonusReleaseToPlayer'] == self::BONUS_RELEASE_TO_PLAYER_AUTO) {
					//auto approve
					// $this->approvePromo($playerId, $promorule, $promoCmsSettingId, $adminId, null, null, $playerPromoId, $extra_info);
					$message = 'notify.90';
				} else {
					// if ($approveProcessPendingFlag) {
					// 	$playerdepositpromoId = $this->approvePendingRequestWithoutBonusRelease($playerId, $promorule, 0, $promoCmsSettingId, $adminId);
					// 	$this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
					// 	$message = 'Promo has been approved without bonus release to player.';
					// } else {
						// $playerdepositpromoId = $this->requestPromo($playerId, $promorule, 0, $promoCmsSettingId, $adminId, null);
						// $this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
						//Your promo application has been sent!
						//save approved promoId to session
						$message = 'notify.36';
					// }
				}
				$success = true;

			}
			break;
		case self::NON_DEPOSIT_PROMO_TYPE_BETTING: //by betting
			$conditionResult = true;
			if ($noScriptDepositCondition) {

				//get promo details
				// $promoDetails = $this->player_functions->getPromoDetails($promorulesId);
				$gameRecordStartDate = $promorule['gameRecordStartDate'];
				$gameRecordEndDate = $promorule['gameRecordEndDate'];
				$gameRequiredBet = $promorule['gameRequiredBet'];
				// $playerName = $this->authentication->getUsername();

				list($totalBet, $totalWin, $totalLoss) = $this->game_logs->sumBetsWinsLossByDatetime($playerId, $gameRecordStartDate, $gameRecordEndDate, null, $promorulesId);

				//$data = $this->player_functions->getPlayerTotalBet('rhaicese08','2015-05-17 10:54:25','2015-05-30 16:38:04');
				if ($totalBet == null) {
					$currentTotalBet = 0;
				} else {
					$currentTotalBet = $totalBet;
				}
				$conditionResult = $currentTotalBet >= $gameRequiredBet;
			}
			//var_dump($currentTotalBet,$gameRequiredBet);exit();
			if ($conditionResult) {
				//if required bet met

				//apply promo
				// $this->approvePromo($playerId, $promorule, $promoCmsSettingId, $adminId, null, null, $playerPromoId, $extra_info);

				//Your promo application has been approved bonus amount was added to your main wallet account
				$message = 'notify.90';
				// $this->alertMessage(2, $message);
				$success = true;

			} else {

				// $this->declinePromo($playerId, $promorule, $promoCmsSettingId, $adminId);

				//Your promo application has been declined due to insufficient bet.
				$message = 'notify.94';
				// $this->alertMessage(2, $message);
				$success = false;
			}
			break;

		case self::NON_DEPOSIT_PROMO_TYPE_LOSS: //by lossing
			$conditionResult = true;
			if ($noScriptDepositCondition) {

				//get promo details
				// $promoDetails = $this->player_functions->getPromoDetails($promorulesId);
				$gameRecordStartDate = $promorule['gameRecordStartDate'];
				$gameRecordEndDate = $promorule['gameRecordEndDate'];
				$gameRequiredAmount = $promorule['gameRequiredBet'];
				// $playerName = $this->authentication->getUsername();

				list($totalBet, $totalWin, $totalLoss) = $this->game_logs->sumBetsWinsLossByDatetime($playerId, $gameRecordStartDate, $gameRecordEndDate, null, $promorulesId);
				// $data = $this->player_functions->getPlayerTotalLoss($playerName, $gameRecordStartDate, $gameRecordEndDate);
				//$totalLoss = $this->player_functions->getPlayerTotalLoss('rhaicese08','2015-05-17 10:54:25','2015-05-30 16:38:04');

				if ($totalLoss == null) {
					$currentTotalLoss = 0;
				} else {
					$currentTotalLoss = $totalLoss;
				}
				$conditionResult = $currentTotalLoss >= $gameRequiredAmount;
			}
			//var_dump($currentTotalLoss,$gameRequiredBet);exit();
			if ($conditionResult) {
				//gameRequiredBet = gameRequiredLoss, need change field name if necessary
				//apply promo
				// $this->approvePromo($playerId, $promorule, $promoCmsSettingId, $adminId, null, null, $playerPromoId, $extra_info);

				//Your promo application has been approved bonus amount was added to your main wallet account
				$message = 'notify.90';
				// $this->alertMessage(2, $message);
				$success = true;

			} else {

				// $this->declinePromo($playerId, $promorule, $promoCmsSettingId, $adminId);

				//Your promo application has been declined due to insufficient bet.
				$message = 'notify.97';
				// $this->alertMessage(2, $message);
				$success = false;
			}

			break;

		case self::NON_DEPOSIT_PROMO_TYPE_WINNING: //by winning
			$conditionResult = true;
			if ($noScriptDepositCondition) {

				//get promo details
				$gameRecordStartDate = $promorule['gameRecordStartDate'];
				$gameRecordEndDate = $promorule['gameRecordEndDate'];
				$gameRequiredAmount = $promorule['gameRequiredBet'];
				// $playerName = $this->authentication->getUsername();

				list($totalBet, $totalWin, $totalLoss) = $this->game_logs->sumBetsWinsLossByDatetime($playerId, $gameRecordStartDate, $gameRecordEndDate, null, $promorulesId);
				//$totalLoss = $this->player_functions->getPlayerTotalLoss('rhaicese08','2015-05-17 10:54:25','2015-05-30 16:38:04');

				if ($totalWin == null) {
					$currentTotalWin = 0;
				} else {
					$currentTotalWin = $totalWin;
				}
				$conditionResult = $currentTotalWin >= $gameRequiredAmount;
			}

			//var_dump($currentTotalWin,$gameRequiredBet);exit();
			if ($conditionResult) {
				//gameRequiredBet = gameRequiredLoss, need change field name if necessary
				//apply promo
				// $this->approvePromo($playerId, $promorule, $promoCmsSettingId, $adminId, null, null, $playerPromoId, $extra_info);

				//Your promo application has been approved bonus amount was added to your main wallet account
				$message = 'notify.90';
				// $this->alertMessage(2, $message);
				$success = true;

			} else {

				// $this->declinePromo($playerId, $promorule, $promoCmsSettingId, $adminId);

				//Your promo application has been declined due to insufficient bet.
				$message = 'notify.98';
				// $this->alertMessage(2, $message);
				$success = false;
			}
			break;

		case self::NON_DEPOSIT_PROMO_TYPE_LOSS_MINUS_WIN: //by lossing
			if ($noScriptDepositCondition) {

				//get promo details
				// $promoDetails = $this->player_functions->getPromoDetails($promorulesId);
				// $gameRecordStartDate = $promorule['gameRecordStartDate'];
				// $gameRecordEndDate = $promorule['gameRecordEndDate'];
				$gameRequiredAmount = $promorule['gameRequiredBet'];
				// $playerName = $this->authentication->getUsername();
				list($limitFrom, $limitTo) = $this->utils->getLimitDateRangeForPromo($promorule['bonusApplicationLimitDateType']);

				$this->utils->debug_log('limitFrom', $limitFrom, 'limitTo', $limitTo, 'bonusApplicationLimitDateType', $promorule['bonusApplicationLimitDateType']);

				list($totalBet, $totalWin, $totalLoss) = $this->game_logs->sumBetsWinsLossByDatetime($playerId, $limitFrom, $limitTo, null, $promorulesId);
				// $data = $this->player_functions->getPlayerTotalLoss($playerName, $gameRecordStartDate, $gameRecordEndDate);
				//$totalLoss = $this->player_functions->getPlayerTotalLoss('rhaicese08','2015-05-17 10:54:25','2015-05-30 16:38:04');

				if ($totalLoss == null) {
					$currentTotalLoss = 0;
				} else {
					$currentTotalLoss = $totalLoss;
				}
				if ($totalWin == null) {
					$currentTotalWin = 0;
				} else {
					$currentTotalWin = $totalWin;
				}
				$conditionResult = ($currentTotalLoss-$currentTotalWin) >= $gameRequiredAmount;
			}else{
				$conditionResult = true;
			}
			//var_dump($currentTotalLoss,$gameRequiredBet);exit();
			if ($conditionResult) {
				//gameRequiredBet = gameRequiredLoss, need change field name if necessary
				//apply promo
				// $this->approvePromo($playerId, $promorule, $promoCmsSettingId, $adminId, null, null, $playerPromoId, $extra_info);

				//Your promo application has been approved bonus amount was added to your main wallet account
				$message = 'notify.90';
				// $this->alertMessage(2, $message);
				$success = true;

			} else {

				// $this->declinePromo($playerId, $promorule, $promoCmsSettingId, $adminId);

				//Your promo application has been declined due to insufficient bet.
				$message = 'notify.97';
				// $this->alertMessage(2, $message);
				$success = false;
			}

			break;
		case self::NON_DEPOSIT_PROMO_TYPE_CUSTOMIZE: //customize
			//get bonus
			if ($this->isAutoRelease($promorule)) {
			// 	// if ($promorule['bonusReleaseToPlayer'] == self::BONUS_RELEASE_TO_PLAYER_AUTO) {
			// 	//auto approve
				// $this->approvePromo($playerId, $promorule, $promoCmsSettingId, $adminId);
				$message = 'notify.90';
			} else {
			// 	// if ($approveProcessPendingFlag) {
			// 	// 	$playerdepositpromoId = $this->approvePendingRequestWithoutBonusRelease($playerId, $promorule, 0, $promoCmsSettingId, $adminId);
			// 	// 	$this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
			// 	// 	$message = 'Promo has been approved without bonus release to player.';
			// 	// } else {
			// 		$playerdepositpromoId = $this->requestPromo($playerId, $promorule, 0, $promoCmsSettingId, $adminId, null);
			// 		$this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
			// 		//Your promo application has been sent!
			// 		//save approved promoId to session
				$message = 'notify.36';
				// }
			}
			$success = true;
		default:
			// case self::NON_DEPOSIT_PROMO_TYPE_CASHBACK: //cashback
			// case self::NON_DEPOSIT_PROMO_TYPE_CUSTOMIZE: //customize
			$conditionResult = true;

			if (!$conditionResult) {
				$this->declinePromo($playerId, $promorule, $promoCmsSettingId, $adminId);
				$message = 'promo.request_rescue.decliend';
				if (!empty($errorMessageLang)) {
					$message = $errorMessageLang;
				}
				$success = false;
			} else {

				if ($this->isAutoRelease($promorule)) {
				// 	// if ($promorule['bonusReleaseToPlayer'] == self::BONUS_RELEASE_TO_PLAYER_AUTO) {
				// 	//auto approve
					// $this->approvePromo($playerId, $promorule, $promoCmsSettingId, $adminId);
					$message = 'notify.90';
				} else {
				// 	// if ($approveProcessPendingFlag) {
				// 	// 	$playerdepositpromoId = $this->approvePendingRequestWithoutBonusRelease($playerId, $promorule, 0, $promoCmsSettingId, $adminId);
				// 	// 	$this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
				// 	// 	$message = 'Promo has been approved without bonus release to player.';
				// 	// } else {
				// 		$playerdepositpromoId = $this->requestPromo($playerId, $promorule, 0, $promoCmsSettingId, $adminId, null);
				// 		$this->utils->debug_log('playerdepositpromoId', $playerdepositpromoId);
				// 		//Your promo application has been sent!
				// 		//save approved promoId to session
					$message = 'notify.36';
					// }
				}
				$success = true;

			}
			break;
		}

		$extra_info['bonusAmount']=$bonusAmount;
		$extra_info['withdrawConditionAmount']=$withdrawConditionAmount;
		$extra_info['betTimes']=$betTimes;

		$this->appendToDebugLog($extra_info['debug_log'], 'get result' ,[
			'bonusAmount'=>$bonusAmount, 'withdrawConditionAmount'=>$withdrawConditionAmount,
			'betTimes'=>$betTimes
		]);

		// redirect('iframe_module/iframe_promos');
		return array($success, $message);
	}

	/**
     * Deprecated: No one use it
     *
	 * overview : trigger promotion from manual admin
	 *
	 * @param int	$playerId
	 * @param int	$promorule
	 * @param int	$promoCmsSettingId
	 * @param bool|false $preapplication
	 * @param null $playerPromoId
	 * @param null $extra_info
	 * @return array
	 */
	public function triggerPromotionFromManualAdmin($playerId, $promorule, $promoCmsSettingId,
			$preapplication=false, $playerPromoId=null, &$extra_info=null) {

		return $this->checkAndProcessPromotion($playerId, $promorule, $promoCmsSettingId,
			$preapplication, $playerPromoId, $extra_info, 'manual_admin');

	}

	/**
	 * overview : trigger promotion from manual player
	 *
	 * @param int	$playerId
	 * @param int	$promorule
	 * @param int	$promoCmsSettingId
	 * @param bool|false $preapplication
	 * @param null $playerPromoId
	 * @param null $extra_info
	 * @return array
	 */
	public function triggerPromotionFromManualPlayer($playerId, $promorule, $promoCmsSettingId,
			$preapplication=false, $playerPromoId=null, &$extra_info=null) {

		return $this->checkAndProcessPromotion($playerId, $promorule, $promoCmsSettingId,
			$preapplication, $playerPromoId, $extra_info, 'manual_player');

	}

	/**
	 * overview : trigger promotion from deposit
	 *
	 * @param int	$playerId
	 * @param int	$promorule
	 * @param int	$promoCmsSettingId
	 * @param bool|false $preapplication
	 * @param null $playerPromoId
	 * @param null $extra_info
	 * @return array
	 */
	public function triggerPromotionFromDeposit($playerId, $promorule, $promoCmsSettingId,
			$preapplication=false, $playerPromoId=null, &$extra_info=null) {

		return $this->checkAndProcessPromotion($playerId, $promorule, $promoCmsSettingId,
			$preapplication, $playerPromoId, $extra_info, 'deposit');

	}

	/**
	 * overview : trigger promotion from transfer
	 *
	 * @param int	$playerId
	 * @param int	$promorule
	 * @param int	$promoCmsSettingId
	 * @param bool|false $preapplication
	 * @param null $playerPromoId
	 * @param null $extra_info
	 * @return array
	 */
	public function triggerPromotionFromTransfer($playerId, $promorule, $promoCmsSettingId,
			$preapplication=false, $playerPromoId=null, &$extra_info=null) {

		return $this->checkAndProcessPromotion($playerId, $promorule, $promoCmsSettingId,
			$preapplication, $playerPromoId, $extra_info, 'transfer');

	}

	/**
	 * overview : check and process promotion
	 *
	 * @param int	$playerId
	 * @param int	$promorule
	 * @param int	$promoCmsSettingId
	 * @param bool|false $preapplication
	 * @param int $playerPromoId exists player promo
	 * @param null $extra_info
	 * @param null $triggerEvent
	 * @return array
	 */
	public function checkAndProcessPromotion($playerId, $promorule, $promoCmsSettingId,
			$preapplication=false, $playerPromoId=null, &$extra_info=null, $triggerEvent=null, $dry_run=false) {

		$this->utils->debug_log('start check and process promotion trigger: '.$triggerEvent, $playerId, 'promorule',
			$promorule['promorulesId'], 'promoCmsSettingId', $promoCmsSettingId, 'preapplication', $preapplication,
			'playerPromoId', $playerPromoId, 'extra_info', $extra_info);


		$adminId = $this->users->getSuperAdminId();
		if(empty($extra_info)){
			$extra_info=[];
		}
		$extra_info['is_checking_before_deposit']=false;
		$promorulesId = $promorule['promorulesId'];

		$this->utils->debug_log('checkAndProcessPromotion-checkOnlyPromotion-params', $playerId, $promorule, $promoCmsSettingId,
			$preapplication, $playerPromoId, $extra_info, $dry_run);
		list($success, $message)=$this->checkOnlyPromotion($playerId, $promorule, $promoCmsSettingId,
			$preapplication, $playerPromoId, $extra_info, $dry_run);
		$this->utils->debug_log('checkAndProcessPromotion-checkOnlyPromotion-result', $success, $message);
		if($success){
			$reason=null;
			try {
				if($preapplication){
					//and don't check bonus number
					$playerPromoId = $this->requestPromo($playerId, $promorule,
						null, $promoCmsSettingId, $adminId, null, false, null, null, $extra_info, $dry_run);
					$success= !!$playerPromoId;
					if($success){
                        $extra_info['player_promo_request_id'] = $playerPromoId;
						$message = 'notify.36';
					}else{
						$message = 'promo_rule.common.error';
					}
					return array($success, $message);
				}

				if($this->isTransferPromo($promorule)){
					$this->utils->debug_log('checkAndProcessPromotion-isTransferPromo', true);
					$transferAmount = @$extra_info['transferAmount'];
					$transferTransId = @$extra_info['transferTransId'];
					$subWalletId= @$extra_info['subWalletId'];

					//release manually or auto
					if ($this->isAutoRelease($promorule) || $this->isWaitingRelease($playerId, $promorule)) {

						$this->appendToDebugLog($extra_info['debug_log'], 'release auto', [
							'transferAmount'=>$transferAmount, 'transferTransId'=>$transferTransId,
							'subWalletId'=>$subWalletId,
						]);

						if($promorule['add_withdraw_condition_as_bonus_condition']=='1'){
							//only update bonus and withdarw condition
							$playerPromoId=$this->approvePromoWithoutRelease($playerId, $promorule, $promoCmsSettingId,
								$adminId, $transferAmount, $transferTransId, $playerPromoId, $extra_info, $reason, $dry_run);
						}else{
							//auto approve
							$playerPromoId = $this->approvePromo($playerId, $promorule, $promoCmsSettingId,
								$adminId, $transferAmount, $transferTransId, $playerPromoId, $extra_info, $reason, $dry_run);
						}
                        $extra_info['player_promo_request_id'] = $playerPromoId;
						$message = 'notify.90';
					} else {

						$this->appendToDebugLog($extra_info['debug_log'], 'release manually', [
							'transferAmount'=>$transferAmount, 'transferTransId'=>$transferTransId,
							'subWalletId'=>$subWalletId,
						]);

						//create request
						// if ($approveProcessPendingFlag) {
						// 	#TODO $approveProcessPendingFlag
						// 	$playerdepositpromoId = $this->approvePendingRequestWithoutBonusRelease($playerId, $promorule,
						// 		$transferAmount, $promoCmsSettingId, $adminId, $tranId);
						// } else {
						$checkBonusAmount=false;
						$playerPromoId = $this->requestPromo($playerId, $promorule,
							$transferAmount, $promoCmsSettingId, $adminId, $transferTransId, $checkBonusAmount, $playerPromoId,
							$reason, $extra_info, $dry_run);
						// }
                        $extra_info['player_promo_request_id'] = $playerPromoId;

						$message = 'notify.36';
					}

				} elseif ($this->isDepositPromo($promorule)) {
					$this->utils->debug_log('checkAndProcessPromotion-isDepositPromo', true);

					$bonusAmount = @$extra_info['bonusAmount'];
					$depositAmount = @$extra_info['depositAmount'];
					$withdrawConditionAmount = @$extra_info['withdrawConditionAmount'];
					$betTimes = @$extra_info['betTimes'];
					$tranId = @$extra_info['depositTranId'];
					$this->utils->debug_log('checkAndProcessPromotion-isDepositPromo', $bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes, $tranId);
					//release manually or auto
					if ($this->isAutoRelease($promorule)) {
						$this->utils->debug_log('checkAndProcessPromotion-isAutoRelease', $promorule);
						$this->appendToDebugLog($extra_info['debug_log'], 'release auto', [
							'bonusAmount'=>$bonusAmount, 'depositAmount'=>$depositAmount,
							'withdrawConditionAmount'=>$withdrawConditionAmount, 'betTimes'=>$betTimes,
							'tranId'=>$tranId,
						]);

						$this->utils->debug_log('checkAndProcessPromotion-add_withdraw_condition_as_bonus_condition', $promorule['add_withdraw_condition_as_bonus_condition']);
						if($promorule['add_withdraw_condition_as_bonus_condition']=='1'){
							//only update bonus and withdarw condition
							$playerPromoId=$this->approvePromoWithoutRelease($playerId, $promorule, $promoCmsSettingId,
								$adminId, $depositAmount, $tranId, $playerPromoId, $extra_info, $reason, $dry_run);
						}else{
							//auto approve
							$playerPromoId = $this->approvePromo($playerId, $promorule, $promoCmsSettingId,
								$adminId, $depositAmount, $tranId, $playerPromoId, $extra_info, $reason, $dry_run);
						}
                        $extra_info['player_promo_request_id'] = $playerPromoId;
						$message = 'notify.90';

					} else {

						$this->appendToDebugLog($extra_info['debug_log'], 'release manually', [
							'bonusAmount'=>$bonusAmount, 'depositAmount'=>$depositAmount,
							'withdrawConditionAmount'=>$withdrawConditionAmount, 'betTimes'=>$betTimes,
							'tranId'=>$tranId,
						]);

						$checkBonusAmount=false;
                        $playerPromoId = $this->requestPromo($playerId, $promorule,
							$depositAmount, $promoCmsSettingId, $adminId, $tranId, $checkBonusAmount, $playerPromoId,
							$reason, $extra_info, $dry_run);
                        $extra_info['player_promo_request_id'] = $playerPromoId;

						$message = 'notify.36';
					}

				} else {

					$bonusAmount = @$extra_info['bonusAmount'];
					$withdrawConditionAmount = @$extra_info['withdrawConditionAmount'];
					$betTimes = @$extra_info['betTimes'];

					if ($this->isAutoRelease($promorule)) {

						$this->appendToDebugLog($extra_info['debug_log'], 'release auto', [
							'bonusAmount'=>$bonusAmount, 'withdrawConditionAmount'=>$withdrawConditionAmount,
							'betTimes'=>$betTimes,
						]);

						// if ($promorule['bonusReleaseToPlayer'] == self::BONUS_RELEASE_TO_PLAYER_AUTO) {
						//auto approve
						if($promorule['add_withdraw_condition_as_bonus_condition']=='1'){
							//only update bonus and withdarw condition
							$playerPromoId=$this->approvePromoWithoutRelease($playerId, $promorule, $promoCmsSettingId,
								$adminId, 0, null, $playerPromoId, $extra_info, $reason, $dry_run);
						}else{
							$playerPromoId = $this->approvePromo($playerId, $promorule, $promoCmsSettingId,
								$adminId, null, null, $playerPromoId, $extra_info, $reason, $dry_run);
						}
                        $extra_info['player_promo_request_id'] = $playerPromoId;
						$message = 'notify.90';
					} else {

						$this->appendToDebugLog($extra_info['debug_log'], 'release manually', [
							'bonusAmount'=>$bonusAmount, 'withdrawConditionAmount'=>$withdrawConditionAmount,
							'betTimes'=>$betTimes,
						]);

						$checkBonusAmount=false;
						$playerPromoId = $this->requestPromo($playerId, $promorule,
							0, $promoCmsSettingId, $adminId, null, $checkBonusAmount, $playerPromoId,
							$reason, $extra_info, $dry_run);
                        $extra_info['player_promo_request_id'] = $playerPromoId;
						//Your promo application has been sent!
						//save approved promoId to session
						$message = 'notify.36';

					}
				}

				if(!empty($playerPromoId)){
                    $this->load->model(['player_promo']);
                    $this->player_promo->addPlayerPromoRequestBy($playerPromoId, null, $playerId);
                }

			} catch (WrongBonusException $e) {
				$this->utils->debug_log('wrong bonus exception', 'playerId', $playerId, 'promoruleid', $promorulesId, $e);
				$success = false;
				$message = 'promo_rule.common.error';
				if (!empty($e->error_message_lang)) {
					$message = $e->error_message_lang;
				}
			}
		}
 		return array($success, $message);

	}

	/**
	 * overview : waiting release
	 *
	 * @param  $playerId
	 * @param  $promorule
	 * @return array
	 */
	public function isWaitingRelease($playerId, $promorule){

		$this->load->model(['player_promo']);
		return $this->player_promo->isWaitingRelease($playerId, $promorule['promorulesId']);

	}

	/**
	 * overview : only for repeatable
	 *
	 * @param $playerId
	 * @param $promorule
	 * @param null $extra_info
	 * @return array
	 */
	public function validRepeatable($playerId, $promorule, $extra_info=null) {
		$this->load->model(array('player_model', 'total_player_game_hour', 'player_promo'));
		$this->utils->debug_log('is promorule', $promorule);
		$success = false;
		$message = 'error.default.message';

		$repeatable = $this->isRepeatable($promorule);
		$promorulesId = $promorule['promorulesId'];

		list($fromDatetime, $toDatetime) = $this->utils->getLimitDateRangeForPromo($promorule['bonusApplicationLimitDateType']);
		// $this->utils->debug_log('is fromDatetime', $fromDatetime, $toDatetime);
		$this->utils->debug_log('fromDatetime', $fromDatetime, 'toDatetime', $toDatetime, 'bonusApplicationLimitDateType', $promorule['bonusApplicationLimitDateType']);

		//get duplicate promo and date type
		$playerDuplicatePromoCount = $this->player_promo->getPlayerDuplicatePromo($playerId, $promorulesId,
			[Player_promo::TRANS_STATUS_APPROVED, Player_promo::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION],
			$fromDatetime, $toDatetime);

		$this->utils->debug_log('is playerDuplicatePromoCount', $playerDuplicatePromoCount, $fromDatetime, $toDatetime);
		$noDupPromo = $playerDuplicatePromoCount <= 0;
		$this->utils->debug_log('noDupPromo: ', array(
			'repeatable' => $repeatable,
			'playerDuplicatePromoCount' => $playerDuplicatePromoCount,
			'noDupPromo' => $noDupPromo,
		));

		if (!$repeatable) {
			if ($noDupPromo) {
				$message = null;
				$success = true;
			} else {
				$message = 'notify.83';
				$success = false;
			}
		} else {
			// $repeatPromoRequireBetAmount = $promorule['bonusAmount'] * $promorule['repeatConditionBetCnt'];

			// //get player required details
			// // $playerName = $this->authentication->getUsername();
			// $playerStartDate = $this->player_model->getPlayerRegisterDate($playerId);

			// //get player total bet
			// $data = $this->total_player_game_hour->getPlayerTotalBet($playerId, $playerStartDate, $this->utils->getNowForMysql());
			// // $data = $this->player_functions->getPlayerTotalBet($playerName, $playerStartDate['date'], date('Y-m-d H:i:s'));

			// if ($data['currentTotalBet'] == null) {
			// 	$currentTotalBet = 0;
			// } else {
			// 	$currentTotalBet = $data['currentTotalBet'];
			// }

			// if ($currentTotalBet >= $repeatPromoRequireBetAmount) {
			//if repeat requirement met

			if ($promorule['bonusApplicationLimitRule'] == self::BONUS_APPLICATION_LIMIT_RULE_NO_LIMIT) {
				//no limit
				$message = null;
				$success = true;
				//check how much bonus player can get
				// list($success, $message) = $this->processNonDepositPromo($playerId, $promorule, $promoCmsSettingId);
			} else {
				//with limit
				if ($playerDuplicatePromoCount < $promorule['bonusApplicationLimitRuleCnt']) {
					//if do not exceeds limit
					$message = null;
					$success = true;
					//check how much bonus player can get
					// list($success, $message) = $this->processNonDepositPromo($playerId, $promorule, $promoCmsSettingId);
				} else {
					//if exceeds limit
					//You cannot join this promo anymore because you exceeds join promotion limit.
					$message = 'notify.82';
					$success = false;
				}
			}
			// } else {
			// 	//repeat requirement did not met
			// 	//Your promo application has been declined due to insufficient bet.
			// 	$message = 'notify.95';
			// 	$success = false;
			// }
		}
		return array($success, $message);
	}

	/**
	 * overview : only available random bonus
	 *
	 * @param $playerId
	 * @param $random_bonus_mode
	 * @param $promoCategoryId
	 * @return array
	 */
	public function isAvailableRandomBonus($playerId, $random_bonus_mode, $promoCategoryId) {
		$this->load->model(array('transactions', 'random_bonus_history'));
		//get available bonus
		$availableDepositForPickUpBonus = $this->transactions->getAvailableDepositForPickupBonus($playerId);

		$depositForPickUpBonus = null;
		$success = false;

		switch ($random_bonus_mode) {
		case self::RANDOM_BONUS_MODE_PERCENT_DEPOSIT:
			$success = !empty($availableDepositForPickUpBonus);
			if ($success) {
				$depositForPickUpBonus = $availableDepositForPickUpBonus[0];
			}
			break;

		case self::RANDOM_BONUS_MODE_FIXED_ITEM:
			$success = !empty($availableDepositForPickUpBonus);
			if ($success) {
				$depositForPickUpBonus = $availableDepositForPickUpBonus[0];
			}
			break;

		case self::RANDOM_BONUS_MODE_COUNTING:
			$bonusExistsToday = $this->random_bonus_history->isPlayerBonusExistsTodayForBonusModeCounting($playerId);
			$this->utils->debug_log('playerId', $playerId, 'bonusExistsToday', $bonusExistsToday);
			$success = !empty($availableDepositForPickUpBonus) && !$bonusExistsToday;
			if (!empty($availableDepositForPickUpBonus)) {
				$depositForPickUpBonus = $availableDepositForPickUpBonus[0];
			}
			break;

		}

		return array($success, $depositForPickUpBonus);
	}

	/**
	 * overview : calculate random bonus
	 *
	 * @param int	$playerId
	 * @param int	$randomBonusMode
	 * @param null $depositForPickUpBonus
	 * @return array
	 */
	public function calcRandomBonus($playerId, $randomBonusMode, $depositForPickUpBonus = null) {
		$randomRate = 0;
		$this->utils->debug_log('playerId', $playerId, 'randomBonusMode', $randomBonusMode, 'depositForPickUpBonus', $depositForPickUpBonus);
		switch ($randomBonusMode) {
		case self::RANDOM_BONUS_MODE_PERCENT_DEPOSIT:
			$depositAmount = $depositForPickUpBonus['amount'];
			$min = $this->utils->getConfig('min_random_bonus_rate');
			$max = $this->utils->getConfig('max_random_bonus_rate');
			$randomRate = rand($min, $max);
			$randomBonusPercentage = $randomRate / 100;
			$randomBonusAmount = $randomBonusPercentage * $depositAmount;
			break;
		case self::RANDOM_BONUS_MODE_FIXED_ITEM:
			$depositAmount = $depositForPickUpBonus['amount'];

			$random_bonus_fixed_items = $this->utils->getConfig('random_bonus_fixed_items');

			$awards = array();
			$min_num = 1;
			$max_num = 0;
			foreach ($random_bonus_fixed_items as $key => $item) {
				$min_num = $max_num + 1;
				$max_num += $item['probability'] * 1000;

				$awards[] = array('key' => $key, 'min_num' => $min_num, 'max_num' => $max_num,
					'info' => $item);
			}

			$this->utils->debug_log('award', $awards);

			$awardNum = rand(1, 100000);
			$awardInfo = null;
			foreach ($awards as $award) {
				if ($awardNum >= $award['min_num'] && $awardNum <= $award['max_num']) {
					$awardInfo = $award['info'];
				}
			}
			$this->utils->debug_log('awardInfo', $awardInfo);

			if (empty($awardInfo)) {
				//first
				$awardInfo = $awards[0]['info'];
			}
			$randomRate = $awardInfo['rate_by_deposit'];

			$randomBonusPercentage = $randomRate / 100;
			$randomBonusAmount = $randomBonusPercentage * $depositAmount;
			break;
		case self::RANDOM_BONUS_MODE_COUNTING:
			$this->db->select('count')->from('count_random_bonus_daily');
			$qry = $this->db->get();

			$currentRandomBonusDailyCntData = $qry->row_array();
			if (empty($currentRandomBonusDailyCntData)) {
				$currentRandomBonusDailyCnt = self::DEFAULT_RANDOM_BONUS_DAILY_COUNT;

				$date = new DateTime();
				$randomBonusDailyDefaultData = array(
					"count" => 0,
					"date" => $date->format('Y-m-d'),
					"created_at" => $date->format('Y-m-d H:i:s'),
				);

				$this->db->insert('count_random_bonus_daily', $randomBonusDailyDefaultData);
			} else {
				$currentRandomBonusDailyCnt = $currentRandomBonusDailyCntData['count'];
			}

			$big_bonus_trigger_count_number = $this->utils->getConfig('big_bonus_trigger_count_number');
			$big_random_bonus_amount = $this->utils->getConfig('big_random_bonus_amount');
			$small_bonus_amount = $this->utils->getConfig('small_bonus_amount');

			$newCurrentRandomBonusDailyCnt = $currentRandomBonusDailyCnt += 1;

			//update current bonus daily count
			$this->updateCurrentBonusDailyCntForBonusModeCounting($newCurrentRandomBonusDailyCnt);

			if ($newCurrentRandomBonusDailyCnt % $big_bonus_trigger_count_number == 0) {
				$randomBonusAmount = $big_random_bonus_amount;
			} else {
				$randomBonusAmount = $small_bonus_amount;
			}
			break;
		default:
			$randomBonusAmount = 0;
			break;
		}

		return array($randomBonusAmount, $randomRate);
	}

	/**
	 * overview : release random bonus
	 *
	 * @param $playerId
	 * @param $randomBonusAmount
	 * @param $depositForPickUpBonus
	 * @param $promoCategoryId
	 * @param $randomRate
	 */
	public function releaseRandomBonus($playerId, $randomBonusAmount, $depositForPickUpBonus,
		$promoCategoryId, $randomRate) {
		$this->load->model(array('transactions', 'wallet_model', 'withdraw_condition', 'random_bonus_history'));

		$random_bonus_withdraw_condition_times = $this->utils->getConfig('random_bonus_withdraw_condition_times');
		$depositAmount = $depositForPickUpBonus['amount'];
		$depositTransactionId = $depositForPickUpBonus['deposit_transaction_id'];

		$beforeBalance = $this->wallet_model->getMainWalletBalance($playerId);

		//add to main wallet, move transaction
		// $this->wallet_model->incMainWallet($playerId, $randomBonusAmount);

		//write to transactions
		$adminUserId = 1;
		$bonusTransactionId = $this->transactions->createBonusTransaction($adminUserId, $playerId, $randomBonusAmount, $beforeBalance,
			null, null, Transactions::MANUAL, null, Transactions::RANDOM_BONUS,
			'rate is ' . $randomRate . '% , deposit amount is ' . $depositAmount, $promoCategoryId);
        if(empty($bonusTransId)){
            $this->utils->error_log('create bonus transaction failed', $playerId, $playerBonusAmount);
            throw new WrongBonusException('Create bonus transaction failed');
        }

		//write to withdrawal condition
		$withdrawalConditionAmount = $random_bonus_withdraw_condition_times * $randomBonusAmount;
		$this->withdraw_condition->createWithdrawConditionForRandomBonus($playerId, $bonusTransactionId, $withdrawalConditionAmount,
			$randomBonusAmount, $random_bonus_withdraw_condition_times);

		//write to random history
		$this->random_bonus_history->addToRandomBonusHistory($playerId, $depositTransactionId, $bonusTransactionId,
			$depositAmount, $randomBonusAmount, $randomRate);

	}

	/**
	 * overview : update current daily bonus
	 *
	 * @param int	$cnt
	 */
	private function updateCurrentBonusDailyCntForBonusModeCounting($cnt) {
		$date = new DateTime();
		$this->db->update('count_random_bonus_daily',
			array(
				"count" => $cnt,
				"date" => $date->format('Y-m-d'),
				"created_at" => $date->format('Y-m-d H:i:s'),
			)
		);
	}

	/**
	 * overview : get promotion rules
	 *
	 * @param $name
	 * @return mixed
	 */
	public function getPromotionRules($name){
		return @$this->utils->getConfig('promotion_rules')[$name];
	}

	/**
	 * overview : calculate bonus amount and withdraw condition
	 *
	 * @param $promorulesId
	 * @param $playerId
	 * @param $depositAmount
	 * @param null $errorMessageLang
	 * @return array
	 */
	public function calcBonusAmountAndWithdrawCondition($promorulesId, $playerId, $depositAmount, &$errorMessageLang=null, &$extra_info=null, $dry_run=false){

		$promorule=$this->getPromoruleById($promorulesId);
		$this->utils->debug_log('bonusReleaseRule', $promorule['bonusReleaseRule'], 'playerId', $playerId, 'depositAmount', $depositAmount);

		$errorMessageLang=null;
		$bonusAmount=$this->getBonusAmount($promorule, $depositAmount, $playerId, $errorMessageLang, $extra_info, $dry_run);
		// $this->getWithdrawCondAmount
		$withdrawCondAmount=$this->getWithdrawCondAmount($promorule, $bonusAmount, $depositAmount, $playerId, null);

		return [$bonusAmount, $withdrawCondAmount];

	}

	/**
	 * overview : manually apply promo
	 *
	 * @param $player_id
	 * @param $promorule
	 * @param $promoCmsSettingId
	 * @param $bonusAmount
	 * @param $bet_times
	 * @return bool
	 */
	public function manuallyApplyPromo($player_id, $promorule, $promoCmsSettingId, $bonusAmount, $bet_times, $extra_notes = null){

		// $deductDeposit = $this->input->post('deductDeposit');
		// $deposit_amt_condition = $this->input->post('depositAmtCondition') ? $this->input->post('depositAmtCondition') : null;

		// $betTimes = $this->input->post('betTimes');
		// if ($deductDeposit) {
		// 	$condition = (($amount + $deposit_amt_condition) * $betTimes) - $deposit_amt_condition;
		// } else {
		// 	$condition = ($amount + $deposit_amt_condition) * $betTimes;
		// }

		// $promorulesId = empty($promoRuleId) ? $this->promorules->getSystemManualPromoRuleId() : $promoRuleId;

		$this->load->model(['withdraw_condition','transactions', 'player_promo','wallet_model','player_model']);

		$condition=$bonusAmount * $bet_times;
		$deposit_amount=0;

		$promorulesId=$promorule['promorulesId'];
		$promo_category=$promorule['promoCategory'];
		$user_id=1;
		$adjustment_type=Transactions::ADD_BONUS;

		$totalBeforeBalance = $this->wallet_model->getTotalBalance($player_id);
		$before_adjustment = $this->player_model->getMainWalletBalance($player_id);
		$after_adjustment = $before_adjustment + $bonusAmount;


		$note = sprintf('add %s balance to %s\'s by %s; %s',
			number_format($bonusAmount, 2), $player_id, $user_id, $extra_notes);

		// $transaction = $this->transactions->createAdjustmentTransaction($adjustment_type,
		// 	$user_id, $player_id, $bonusAmount, null, $note, null,
		// 	$promo_category, false, null);

		$transaction = $this->transactions->createAdjustmentTransaction($adjustment_type,
			$user_id, $player_id, $bonusAmount, $before_adjustment, $note, $totalBeforeBalance,
			$promo_category, false, null);

		$bonusTransId=$transaction['id'];
		$this->withdraw_condition->createWithdrawConditionForManual($player_id, $bonusTransId,
			$condition, $deposit_amount, $bonusAmount, $bet_times, $promorule, $extra_notes);

		// $this->payment_manager->savePlayerWithdrawalCondition([
		// 	'source_id' => $transaction['id'],
		// 	'source_type' => 4, # manual
		// 	'started_at' => $this->utils->getNowForMysql(),
		// 	'condition_amount' => $condition,
		// 	'status' => 1, # enabled
		// 	'player_id' => $player_id,
		// 	'promotion_id' => $promorulesId,
		// 	'bet_times'=>$bet_times,
		// 	'bonus_amount'=>$bonusAmount,
		// ]);

		//save to player promo

		// $promoCmsSettingId = $this->promorules->getSystemManualPromoCMSId();
		$player_promo_id = $this->player_promo->approvePromoToPlayer($player_id, $promorulesId, $bonusAmount,
			$promoCmsSettingId, $user_id);
		//update player promo id of transaction
		$this->transactions->updatePlayerPromoId($transaction['id'], $player_promo_id);

		return true;
	}

	/**
	 * overview : release after apply promo
	 *
	 * @param $extra_info
	 * @return array
	 */
    public function releaseToAfterApplyPromo($extra_info){

        $rlt=['success'=>false];

        if(isset($extra_info['releaseToSubWallet']) && !empty($extra_info['releaseToSubWallet'])){
            $releaseToSubWallet=$extra_info['releaseToSubWallet'];

            $this->utils->debug_log('releaseToSubWallet', $releaseToSubWallet);
            //transfer to subwallet
            $bonusResult=$this->utils->transferWallet($releaseToSubWallet['playerId'],
                $releaseToSubWallet['playerName'],
                $releaseToSubWallet['transfer_from'], $releaseToSubWallet['transfer_to'],
                $releaseToSubWallet['playerBonusAmount'], $releaseToSubWallet['adminId'],
                $releaseToSubWallet['walletType'], $releaseToSubWallet['originTransferAmount']);

            //move transfer amount to real_for_bonus

            if(!$bonusResult['success']){
                $this->utils->error_log('transfer bonus failed', $bonusResult );
            }
            $rlt=$bonusResult;
            // if(isset($bonusResult['success'])){
            // 	$result['message'].=' '. $message;
            // }else{
            // 	$result['message'].=' '. $message;
            // }
        }
        return $rlt;
    }

    public function releaseToAfterApplyPromoV2($promorule, $extra_info){

        $rlt=['success'=>false];

        if(isset($extra_info['releaseToSubWallet']) && !empty($extra_info['releaseToSubWallet'])){
            $releaseToSubWallet=$extra_info['releaseToSubWallet'];

            $this->utils->debug_log('releaseToSubWallet', $releaseToSubWallet);
            //transfer to subwallet
            $bonusResult=$this->utils->transferWallet($releaseToSubWallet['playerId'],
                $releaseToSubWallet['playerName'],
                $releaseToSubWallet['transfer_from'], $releaseToSubWallet['transfer_to'],
                $releaseToSubWallet['playerBonusAmount'], $releaseToSubWallet['adminId'],
                $releaseToSubWallet['walletType'], $releaseToSubWallet['originTransferAmount']);

            //move transfer amount to real_for_bonus

            if(!$bonusResult['success']){
                $this->utils->error_log('transfer bonus failed', $bonusResult );
            }elseif(isset($extra_info['player_promo_request_id'])){
                $this->promorules->updatePromoId($promorule, $bonusResult['transferTransId'], $extra_info['player_promo_request_id']);
            }
            $rlt=$bonusResult;
            // if(isset($bonusResult['success'])){
            // 	$result['message'].=' '. $message;
            // }else{
            // 	$result['message'].=' '. $message;
            // }
        }
        return $rlt;
    }

	public function appendToExtraInfoDebugLog(&$extra_info, $log, $context=null){
		$debug_log=$extra_info['debug_log'];
		$this->appendToDebugLog($debug_log, $log, $context);
		$extra_info['debug_log']=$debug_log;
		$this->utils->debug_log($log, $context);
	}

	public function appendToDebugLog(&$debug, $log, $context=null){
		if (!is_string($log)) {
			$log = json_encode($log);
		}
		$debug.=$log.' '.var_export($context, true)."\n";
	}

}
////END OF FILE/////////