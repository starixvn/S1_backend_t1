<?php

require_once dirname(__FILE__) . '/base_testing.php';

/**
 *
 *
 * test some function
 *
 */
class Testing_function extends BaseTesting {

	public function init() {
	}

	public function testAll() {
		$this->init();
		$this->testVerifyPromoApplication();
	}

	public function testVerifyPromoApplication() {
		$this->load->library(array('form_validation', 'authentication', 'player_functions', 'cms_function', 'template', 'promo_functions', 'pagination', 'api_functions', 'salt', 'cs_manager', 'email_setting', 'og_utility', 'game_platform/game_platform_manager', 'promo_functions', 'duplicate_account', 'affiliate_process'));
		$depositAmount = 100;
		$promorulesId = 46;
		$promoCmsSettingId = 18;
		$playerId = 112;
		$username = 'test002';
		$this->session->set_userdata('player_id', $playerId);
		$this->session->set_userdata('username', $username);
		$this->verifyPromoApplication($depositAmount, $promorulesId, $promoCmsSettingId);
	}

	private function verifyPromoApplication($depositAmount, $promorulesId, $promoCmsSettingId = '') {
		//get playerid
		$playerId = $this->authentication->getPlayerId();

		//check if player level is valid in this promo
		$playerLevelFlag = $this->player_functions->checkDepositPromoLevelRule($playerId, $promorulesId);

		//get application period
		$applicationPeriodFlag = $this->promo_functions->checkPromoPeriodApplication($promorulesId, 'deposit');

		//get promo details
		$promoDetails = $this->player_functions->getPromoDetails($promorulesId);
		//var_dump($promoDetails);exit();

		//get playerPromoRequest
		$playerPromoRequest = $this->player_functions->getPlayerDuplicatePromo($playerId, $promorulesId, 0);
		//var_dump($playerPromoRequest);exit();

		$this->utils->debug_log('playerId', $playerId, 'playerLevelFlag', $playerLevelFlag, 'applicationPeriodFlag', $applicationPeriodFlag, $promoDetails, $playerPromoRequest);

		if ($playerLevelFlag == false) {
			//if playerlevel is invalid
			//var_dump($playerLevelFlag);exit();
			//Your player level is not valid in this promo! Please contact customer service for more information
			$message = $this->session->set_userdata('promoMessage', lang('notify.35'));
			$this->session->unset_userdata('applicationPromoId');
		} elseif ($applicationPeriodFlag == false) {
			//if application period is invalid
			//var_dump($applicationPeriodFlag);exit();
			//You are not allowed to join due to invalid applicaton period!
			$this->session->set_userdata('promoMessage', lang('notify.78'));
			$this->session->unset_userdata('applicationPromoId');
		} elseif (!empty($playerPromoRequest)) {
//if promo request exists
			//var_dump($playerPromoRequest);exit();
			//you have existing promo request already
			$this->session->set_userdata('promoMessage', lang('notify.34'));
			$this->session->unset_userdata('applicationPromoId');
		} else {
//this means the playerlevel and application period is valid
			if ($promoDetails[0]['bonusApplication'] == 0) {
				//if deposit promo is by succession
				$depositSuccesionPeriod = $promoDetails[0]['depositSuccesionPeriod'];
				if ($depositSuccesionPeriod == 1) {
					//from registration
					$periodFrom = $this->player_functions->getPlayerRegisterDate($playerId)['createdOn'];
					$periodTo = date('Y-m-d H:i:s');
				} elseif ($depositSuccesionPeriod == 2) {
					//this week
					$periodFrom = date("Y-m-d", strtotime('monday this week')) . ' 00:00:00';
					$periodTo = date("Y-m-d", strtotime('sunday this week')) . ' 23:59:59';
				} elseif ($depositSuccesionPeriod == 3) {
					//this month
					$periodFrom = date("Y-m-d", strtotime('first day of')) . ' 00:00:00';
					$periodTo = date("Y-m-d", strtotime('last day of')) . ' 23:59:59';
				}

				//get player total deposit count
				$playerCurrentDepositSuccesionCnt = $this->player_functions->getPlayerCurrentTotalDepositCnt($playerId, $periodFrom, $periodTo);
				//$playerCurrentDepositSuccesionCnt = $this->player_functions->getPlayerTotalDepositCnt($playerId,$periodFrom,$periodTo);
				if (empty($playerCurrentDepositSuccesionCnt)) {
					$playerCurrentDepositSuccesionCnt['dwCount'] = 0;
				}
				$this->utils->debug_log('depositSuccesionType', $promoDetails[0]['depositSuccesionType'], 'playerCurrentDepositSuccesionCnt', $playerCurrentDepositSuccesionCnt['dwCount']);
				if ($promoDetails[0]['depositSuccesionType'] < 3) {
					//check if deposit succession condition is lessthan total 3 deposit cnt
					//var_dump($promoDetails[0]['depositSuccesionType']);exit();
					//check what succession the bonus should be applicable (0=1st deposit,1=2nd deposit,2=3rd deposit)
					if ($promoDetails[0]['depositSuccesionType'] + 1 == $playerCurrentDepositSuccesionCnt['dwCount'] + 1) {

						//check how much bonus player can get
						$this->checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId);
					} else {
						//var_dump(lang('notify.80'));exit();
						//Required deposit count did not met!
						$this->session->set_userdata('promoMessage', lang('notify.80'));
						$this->session->unset_userdata('applicationPromoId');
					}
				} else {
					//if deposit succesion condition more than total 3 deposit cnt
					if ($promoDetails[0]['depositSuccesionCnt'] == $playerCurrentDepositSuccesionCnt['dwCount'] + 1) {

						//check how much bonus player can get
						$this->checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId);
					} else {
						//var_dump(lang('notify.80'));exit();
						//Required deposit count did not met!
						$this->session->set_userdata('promoMessage', lang('notify.80'));
						$this->session->unset_userdata('applicationPromoId');
					}
				}
			} else {
				//if deposit promo is by application
				//var_dump($promoDetails[0]['bonusApplicationRule']);exit();
				if ($promoDetails[0]['bonusApplicationRule'] == 0) {
					//repeat application
					/*note: player must lose all money before repeat promo again
					player may apply automatically if no existing promo
					 */

					//get duplicate promo
					$playerDuplicatePromo = $this->player_functions->getPlayerDuplicatePromo($playerId, $promorulesId);

					//var_dump($playerDuplicatePromo);exit();
					if (count($playerDuplicatePromo) == 0) {
//no existing same promo

						//check how much bonus player can get
						$this->checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId);
					} else {
//with existing promo
						//var_dump($promoDetails[0]['bonusApplicationLimitRule']);exit();

						//get player's main wallet balance
						$playerMainWalletBalance = $this->player_functions->getPlayerMainWalletBalance($playerId);

						//var_dump($playerMainWalletBalance['totalBalanceAmount']);exit();
						//if($playerMainWalletBalance['totalBalanceAmount'] == 0){ //if balance if 0 already

						if ($promoDetails[0]['bonusApplicationLimitRule'] == 0) {
							//no limit

							//get bet amount requirement
							$betAmountReq = ($playerDuplicatePromo[count($playerDuplicatePromo) - 1]['depositAmount'] + $playerDuplicatePromo[count($playerDuplicatePromo) - 1]['bonusAmount']) * $promoDetails[0]['repeatConditionBetCnt'];
							//var_dump($betAmountReq);exit();

							//get required game type
							$promoRuleGameType = $this->promo_functions->getPromoRuleGameType($promorulesId);
							//var_dump($promoRuleGameType);exit();

							//get player name
							$playerName = $this->authentication->getUsername();
							//$playerName = 'rhaidan08';
							//get game total bet
							foreach ($promoRuleGameType as $key) {
								//echo $key['gameType'];
								$playerCurrentBetAmt[] = $this->player_functions->getPlayerCurrentBetAmt($playerName, $key['gameType'], $playerDuplicatePromo[count($playerDuplicatePromo) - 1]['validityStartDate'], $playerDuplicatePromo[count($playerDuplicatePromo) - 1]['validityEndDate']);
							}

							$playerTotalBetAmount = 0;
							foreach ($playerCurrentBetAmt as $key => $data) {
								foreach ($data as $row) {
									$playerTotalBetAmount += $row['totalbet'];
								}
							}

							//var_dump($betAmountReq);exit();
							if ($playerTotalBetAmount >= $betAmountReq) {
//if bet requirement reached

								//check how much bonus player can get
								$this->checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId);

							} else {
								//Youre bet requirement did not met yet to join again in this promo!
								$this->session->set_userdata('promoMessage', lang('notify.81'));
								$this->session->unset_userdata('applicationPromoId');
							}
							//var_dump($playerCurrentBetAmt);exit();
						} else {
//with limit
							//var_dump($promoDetails[0]['bonusApplicationLimitRuleCnt']);exit();
							if (count($playerDuplicatePromo) < $promoDetails[0]['bonusApplicationLimitRuleCnt']) {
								//if do not exceeds limit

								//get bet amount requirement
								$betAmountReq = ($playerDuplicatePromo[count($playerDuplicatePromo) - 1]['depositAmount'] + $playerDuplicatePromo[count($playerDuplicatePromo) - 1]['bonusAmount']) * $promoDetails[0]['repeatConditionBetCnt'];
								//var_dump($betAmountReq);exit();

								//get required game type
								$promoRuleGameType = $this->promo_functions->getPromoRuleGameType($promorulesId);
								//var_dump($promoRuleGameType);exit();

								//get player name
								$playerName = $this->authentication->getUsername();
								//$playerName = 'rhaidan08';
								//get game total bet
								foreach ($promoRuleGameType as $key) {
									//echo $key['gameType'];
									$playerCurrentBetAmt[] = $this->player_functions->getPlayerCurrentBetAmt($playerName, $key['gameType'], $playerDuplicatePromo[count($playerDuplicatePromo) - 1]['validityStartDate'], $playerDuplicatePromo[count($playerDuplicatePromo) - 1]['validityEndDate']);
								}

								$playerTotalBetAmount = 0;
								foreach ($playerCurrentBetAmt as $key => $data) {
									foreach ($data as $row) {
										$playerTotalBetAmount += $row['totalbet'];
									}
								}

								//var_dump($playerTotalBetAmount);exit();
								if ($playerTotalBetAmount >= $betAmountReq) {
//if bet requirement reached

									//check how much bonus player can get
									$this->checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId);

								} else {
									//var_dump(lang('notify.81'));exit();
									//Youre bet requirement did not met yet to join again in this promo!
									$this->session->set_userdata('promoMessage', lang('notify.81'));
									$this->session->unset_userdata('applicationPromoId');
								}
							} else {
//if exceeds limit
								//var_dump(lang('notify.82'));exit();
								//You cannot join this promo anymore because you exceeds join promotion limit.
								$this->session->set_userdata('promoMessage', lang('notify.82'));
								$this->session->unset_userdata('applicationPromoId');
							}
						}
					}
				} else {
					//no repeat application

					//get duplicate promo
					$playerDuplicatePromo = $this->player_functions->getPlayerDuplicatePromo($playerId, $promorulesId);

					//var_dump(count($playerDuplicatePromo));exit();
					if (count($playerDuplicatePromo) == 0) {
//no existing same promo

						//check how much bonus player can get
						$this->checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId);
					} else {
						//var_dump(lang('notify.83'));exit();
						//You cannot join this promo again you already joined this promo already.
						$this->session->set_userdata('promoMessage', lang('notify.83'));
						$this->session->unset_userdata('applicationPromoId');
					}

				}
			}
		}
	}

	/**
	 * check bonus condition for deposit promo
	 *
	 * @return  rendered template
	 */
	private function checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId) {
		//get promo details
		$promoDetails = $this->player_functions->getPromoDetails($promorulesId);
		//var_dump($promoDetails[0]['depositConditionType']);exit();
		//check if deposit condition type is 0=fixed deposit amount or 1=non fixed deposit amount
		if ($promoDetails[0]['depositConditionType'] == 1) {
			//var_dump($promoDetails[0]['depositConditionNonFixedDepositAmount']);exit();
			if ($promoDetails[0]['depositConditionNonFixedDepositAmount'] == 0) {
//0 = deposit is <= or >= amount condition

				if ($promoDetails[0]['nonfixedDepositAmtCondition'] == 0) {
//0 = deposit is <= amount condition

					if ($depositAmount <= $promoDetails[0]['nonfixedDepositAmtConditionRequiredDepositAmount']) {
						//if required deposit amount met
						//var_dump($promoDetails[0]['depositPercentage']);exit();
						//check bonus if base on 0-fixed amount or 1-percentage
						if ($promoDetails[0]['bonusReleaseRule'] != 0) {
							$playerBonusAmount = $depositAmount * ($promoDetails[0]['depositPercentage'] / 100);

							//check if bonus is more than maxBonusAmount
							if ($playerBonusAmount >= $promoDetails[0]['maxBonusAmount']) {
								$playerBonusAmount = $promoDetails[0]['maxBonusAmount'];
							}

						} else {
							$playerBonusAmount = $promoDetails[0]['bonusAmount'];
						}

						//var_dump($promoCmsSettingId);exit();

						$data = array('playerId' => $playerId,
							'promorulesId' => $promorulesId,
							'bonusAmount' => $playerBonusAmount,
							'promoCmsSettingId' => $promoCmsSettingId,
							'depositAmount' => $depositAmount,
							'transactionStatus' => 0, //request
						);

						$playerdepositpromoId = $this->player_functions->applyDepositPromo($data);

						//Your promo application has been sent!
						$this->session->set_userdata('promoMessage', lang('notify.36'));

						//save approved promoId to session
						$this->session->set_userdata('playerPromoId', $playerdepositpromoId);

						return TRUE;
					} else {
						//$message = $this->session->set_userdata('promoMessage','You have insufficient amount of deposit to join the promo!');
						$message = $this->session->set_userdata('promoMessage', lang('notify.37'));
						//$this->alertMessage(2, $message);
					}

				} else {
					//1 = deposit is >= amount condition

					if ($depositAmount >= $promoDetails[0]['nonfixedDepositAmtConditionRequiredDepositAmount']) {
						//if required deposit amount met
						//var_dump($depositAmount);exit();
						//check bonus if base on 0-fixed amount or 1-percentage
						if ($promoDetails[0]['bonusReleaseRule'] != 0) {
							$playerBonusAmount = $depositAmount * ($promoDetails[0]['depositPercentage'] / 100);

							//check if bonus is more than maxBonusAmount
							if ($playerBonusAmount >= $promoDetails[0]['maxBonusAmount']) {
								$playerBonusAmount = $promoDetails[0]['maxBonusAmount'];
							}

						} else {
							$playerBonusAmount = $promoDetails[0]['bonusAmount'];
						}

						//var_dump($playerBonusAmount);exit();

						$data = array('playerId' => $playerId,
							'promorulesId' => $promorulesId,
							'bonusAmount' => $playerBonusAmount,
							'depositAmount' => $depositAmount,
							'promoCmsSettingId' => $promoCmsSettingId,
							'transactionStatus' => 0, //request
						);

						$playerdepositpromoId = $this->player_functions->applyDepositPromo($data);

						//Your promo application has been sent!
						$this->session->set_userdata('promoMessage', lang('notify.36'));

						//save approved promoId to session
						$this->session->set_userdata('playerPromoId', $playerdepositpromoId);

						return TRUE;
					} else {
						//$message = $this->session->set_userdata('promoMessage','You have insufficient amount of deposit to join the promo!');
						$message = $this->session->set_userdata('promoMessage', lang('notify.37'));
						//$this->alertMessage(2, $message);
					}
				}
			} else {
				// 1 = any deposit amount condition
				if ($promoDetails[0]['bonusReleaseRule'] != 0) {
					$playerBonusAmount = $depositAmount * ($promoDetails[0]['depositPercentage'] / 100);

					//check if bonus is more than maxBonusAmount
					if ($playerBonusAmount >= $promoDetails[0]['maxBonusAmount']) {
						$playerBonusAmount = $promoDetails[0]['maxBonusAmount'];
					}

				} else {
					$playerBonusAmount = $promoDetails[0]['bonusAmount'];
				}

				//var_dump($playerBonusAmount);exit();

				$data = array('playerId' => $playerId,
					'promorulesId' => $promorulesId,
					'depositAmount' => $depositAmount,
					'bonusAmount' => $playerBonusAmount,
					'promoCmsSettingId' => $promoCmsSettingId,
					//'promoExpiration' => date("Y/m/d", $expirationDate),
					'transactionStatus' => 0, //request
				);

				$playerdepositpromoId = $this->player_functions->applyDepositPromo($data);

				//Your promo application has been sent!
				$this->session->set_userdata('promoMessage', lang('notify.36'));

				//save approved promoId to session
				$this->session->set_userdata('playerPromoId', $playerdepositpromoId);

				return TRUE;
			}
		} else {
			//very weird unfair
			// fixed deposit amount
			//var_dump($promoDetails[0]['depositConditionDepositAmount']);exit();
			if ($depositAmount == $promoDetails[0]['depositConditionDepositAmount']) {
				//if required deposit amount met
				//var_dump($promoDetails[0]['depositPercentage']);exit();
				//check bonus if base on 0-fixed amount or 1-percentage
				if ($promoDetails[0]['bonusReleaseRule'] != 0) {
					$playerBonusAmount = $depositAmount * ($promoDetails[0]['depositPercentage'] / 100);

					//check if bonus is more than maxBonusAmount
					if ($playerBonusAmount >= $promoDetails[0]['maxBonusAmount']) {
						$playerBonusAmount = $promoDetails[0]['maxBonusAmount'];
					}

				} else {
					$playerBonusAmount = $promoDetails[0]['bonusAmount'];
				}

				//var_dump($playerBonusAmount);exit();

				$data = array('playerId' => $playerId,
					'promorulesId' => $promorulesId,
					'depositAmount' => $depositAmount,
					'bonusAmount' => $playerBonusAmount,
					'promoCmsSettingId' => $promoCmsSettingId,
					//'promoExpiration' => date("Y/m/d", $expirationDate),
					'transactionStatus' => 0, //request
				);

				$playerdepositpromoId = $this->player_functions->applyDepositPromo($data);

				//Your promo application has been sent!
				$this->session->set_userdata('promoMessage', lang('notify.36'));

				//save approved promoId to session
				$this->session->set_userdata('playerPromoId', $playerdepositpromoId);

				return TRUE;
			} else {
				//var_dump(lang('notify.79'));exit();
				//Required deposit amount did not met!
				$message = $this->session->set_userdata('promoMessage', lang('notify.79'));
				//$this->alertMessage(2, $message);
			}
			// }else{
			//     //Your deposit amount is not equal to required deposit amount!
			//     $message = $this->session->set_userdata('promoMessage',lang('promo.msg1'));
			// }
		}
	}

}
