<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 *
 *
 * @deprecated
 *
 */
class Smartcashier extends CI_Controller {

// 	function __construct() {
	// 		parent::__construct();

// 		$this->load->helper('url');
	// 		$this->load->library(array('form_validation', 'authentication', 'player_functions', 'promo_functions', 'template', 'cms_function', 'pagination', 'og_utility', 'salt', 'game_platform/game_platform_manager'));
	// 		//$this->updateSubWallet();
	// 	}

// 	/**
	// 	 * set message for users
	// 	 *
	// 	 * @param   int
	// 	 * @param   string
	// 	 * @return  set session user data
	// 	 */
	// 	public function alertMessage($type, $message) {
	// 		switch ($type) {
	// 			case '1':
	// 				$show_message = array(
	// 					'result' => 'success',
	// 					'message' => $message,
	// 				);
	// 				$this->session->set_userdata($show_message);
	// 				break;

// 			case '2':
	// 				$show_message = array(
	// 					'result' => 'danger',
	// 					'message' => $message,
	// 				);
	// 				$this->session->set_userdata($show_message);
	// 				break;

// 			case '3':
	// 				$show_message = array(
	// 					'result' => 'warning',
	// 					'message' => $message,
	// 				);
	// 				$this->session->set_userdata($show_message);
	// 				break;
	// 		}
	// 	}

// 	public function index() {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			redirect(BASEURL . 'smartcashier/makeDeposit');
	// 		}
	// 	}

// 	/**
	// 	 * Loads template for view based on regions in
	// 	 * config > template.php
	// 	 *
	// 	 */
	// 	private function loadTemplate($title, $description, $keywords, $activenav) {
	// 		$this->template->add_js('resources/js/player/cashier.js');
	// 		$this->template->write('skin', 'template1.css');
	// 		$this->template->write('title', $title);
	// 		$this->template->write('description', $description);
	// 		$this->template->write('keywords', $keywords);
	// 		$this->template->write('activenav', $activenav);
	// 		$this->template->write('username', $this->authentication->getUsername());
	// 		$this->template->write('player_id', $this->authentication->getPlayerId());
	// 		$data['data'] = $this->player_functions->getPlayerMainWallet($this->authentication->getPlayerId());
	// 		$this->template->write('mainwallet', $data['data']['mainwallet']['totalBalanceAmount']);
	// 		$data['data'] = $this->player_functions->getPlayerById($this->authentication->getPlayerId());
	// 		$this->template->write('active', $data['data']['active']);
	// 	}

// 	/**
	// 	 * make deposit
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function makeDeposit($depositType = 1, $depositPromoId = '') {
	// 		if ($this->authentication->isLoggedIn()) {
	// 			$playerId = $this->authentication->getPlayerId();
	// 			$this->session->set_userdata('playerId', $playerId);
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			$data['player'] = $this->player_functions->getPlayerById($playerId);
	// 			$data['subwallet'] = $this->player_functions->getAllPlayerAccountByPlayerId($playerId);
	// 			$data['totalBalance'] = $this->player_functions->getPlayerTotalBalance($playerId)['totalBalance'];
	// 			$data['depositPromoId'] = $this->session->userdata('applicationPromoId');
	// 			$data['promoCmsSettingId'] = $this->session->userdata('promoCmsSettingId');

// 			//get player's deposit rule
	// 			$data['depositRule'] = $this->player_functions->getPlayerDepositRule($playerId);
	// 			$minDeposit = $data['depositRule'][0]['minDeposit'];
	// 			$maxDeposit = $data['depositRule'][0]['maxDeposit'];

// 			$this->loadTemplate('Make Deposit', '', '', 'wallet');
	// 			switch ($depositType) {
	// 				case 1:
	// 					//get player available bank account from bank account manager list according to its level
	// 					$data['thirdPartyAccountDetails'] = $this->player_functions->getPlayerAvailableThirdPartyAccountDeposit($playerId);
	// 					// var_dump($data['thirdPartyAccountDetails']);exit();
	// 					$this->template->write_view('main_content', 'cashier/manual_3rdparty_deposit', $data);
	// 					break;
	// 				case 2:
	// 					$this->template->write_view('main_content', 'cashier/auto_3rdparty_deposit', $data);
	// 					break;
	// 				case 3:
	// 					$this->template->write_view('main_content', 'cashier/localbank_deposit_checker', $data);
	// 					break;
	// 				case 4:
	// 					$this->template->write_view('main_content', 'cashier/cashcard_deposit', $data);
	// 					break;
	// 				default:
	// 					break;
	// 			}

// 			$this->template->render();
	// 		} else {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		}

// 	}

// 	/**
	// 	 * apply deposit promo
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function applyDepositPromo($depositPromoId, $promoCmsSettingId = '') {
	// 		$this->session->unset_userdata('applicationPromoId');
	// 		$this->session->set_userdata('applicationPromoId', $depositPromoId);
	// 		$this->session->unset_userdata('promoCmsSettingId');
	// 		$this->session->set_userdata('promoCmsSettingId', $promoCmsSettingId);
	// 		// $appPromoData = $this->session->userdata('promoCmsSettingId');
	// 		// var_dump($appPromoData);exit();
	// 		$this->makeDeposit();
	// 	}

// 	/**
	// 	 * verify promo application
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function verifyPromoApplication($depositAmount, $promorulesId, $promoCmsSettingId = '') {
	// 		//get playerid
	// 		$playerId = $this->authentication->getPlayerId();

// 		//check if player level is valid in this promo
	// 		$playerLevelFlag = $this->player_functions->checkDepositPromoLevelRule($playerId, $promorulesId);

// 		//get application period
	// 		$applicationPeriodFlag = $this->promo_functions->checkPromoPeriodApplication($promorulesId, 'deposit');
	// 		//var_dump($applicationPeriodFlag);exit();

// 		//get promo details
	// 		$promoDetails = $this->player_functions->getPromoDetails($promorulesId);
	// 		//var_dump($promoDetails);exit();

// 		//get playerPromoRequest
	// 		$playerPromoRequest = $this->player_functions->getPlayerDuplicatePromo($playerId, $promorulesId, 0);
	// 		//var_dump($playerPromoRequest);exit();

// 		if ($playerLevelFlag == false) {
	// 			//if playerlevel is invalid
	// 			//var_dump($playerLevelFlag);exit();
	// 			//Your player level is not valid in this promo! Please contact customer service for more information
	// 			$message = $this->session->set_userdata('promoMessage', lang('notify.35'));
	// 			$this->session->unset_userdata('applicationPromoId');
	// 		} elseif ($applicationPeriodFlag == false) {
	// 			//if application period is invalid
	// 			//var_dump($applicationPeriodFlag);exit();
	// 			//You are not allowed to join due to invalid applicaton period!
	// 			$this->session->set_userdata('promoMessage', lang('notify.78'));
	// 			$this->session->unset_userdata('applicationPromoId');
	// 		} elseif (!empty($playerPromoRequest)) {
	// //if promo request exists
	// 			//var_dump($playerPromoRequest);exit();
	// 			//you have existing promo request already
	// 			$this->session->set_userdata('promoMessage', lang('notify.34'));
	// 			$this->session->unset_userdata('applicationPromoId');
	// 		} else {
	// //this means the playerlevel and application period is valid
	// 			//var_dump($promoDetails[0]['bonusApplication']);exit();
	// 			if ($promoDetails[0]['bonusApplication'] == 0) {
	// 				//if deposit promo is by succession
	// 				//var_dump($promoDetails[0]['bonusApplication']);exit();
	// 				//get player total deposit count
	// 				$playerCurrentDepositSuccesionCnt = $this->player_functions->getPlayerTotalDepositCnt($playerId);
	// 				if (empty($playerCurrentDepositSuccesionCnt)) {
	// 					$playerCurrentDepositSuccesionCnt['dwCount'] = 0;
	// 				}

// 				if ($promoDetails[0]['depositSuccesionType'] < 3) {
	// 					//check if deposit succession condition is lessthan total 3 deposit cnt
	// 					//var_dump($promoDetails[0]['depositSuccesionType']);exit();
	// 					//check what succession the bonus should be applicable (0=1st deposit,1=2nd deposit,2=3rd deposit)
	// 					if ($promoDetails[0]['depositSuccesionType'] + 1 == $playerCurrentDepositSuccesionCnt['dwCount'] + 1) {

// 						//check how much bonus player can get
	// 						$this->checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId);
	// 					} else {
	// 						//var_dump(lang('notify.80'));exit();
	// 						//Required deposit count did not met!
	// 						$this->session->set_userdata('promoMessage', lang('notify.80'));
	// 						$this->session->unset_userdata('applicationPromoId');
	// 					}
	// 				} else {
	// 					//if deposit succesion condition more than total 3 deposit cnt

// 					//check what succession the bonus should be applicable (0=1st deposit,1=2nd deposit,2=3rd deposit)
	// 					if ($promoDetails[0]['depositSuccesionCnt'] == $playerCurrentDepositSuccesionCnt['dwCount'] + 1) {

// 						//check how much bonus player can get
	// 						$this->checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId);
	// 					} else {
	// 						//var_dump(lang('notify.80'));exit();
	// 						//Required deposit count did not met!
	// 						$this->session->set_userdata('promoMessage', lang('notify.80'));
	// 						$this->session->unset_userdata('applicationPromoId');
	// 					}
	// 				}
	// 			} else {
	// 				//if deposit promo is by application
	// 				//var_dump($promoDetails[0]['bonusApplicationRule']);exit();
	// 				if ($promoDetails[0]['bonusApplicationRule'] == 0) {
	// 					//repeat application
	// 					/*note: player must lose all money before repeat promo again
	// 					player may apply automatically if no existing promo
	// 					 */

// 					//get duplicate promo
	// 					$playerDuplicatePromo = $this->player_functions->getPlayerDuplicatePromo($playerId, $promorulesId);

// 					//var_dump($playerDuplicatePromo);exit();
	// 					if (count($playerDuplicatePromo) == 0) {
	// //no existing same promo

// 						//check how much bonus player can get
	// 						$this->checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId);
	// 					} else {
	// //with existing promo
	// 						//var_dump($promoDetails[0]['bonusApplicationLimitRule']);exit();

// 						//get player's main wallet balance
	// 						$playerMainWalletBalance = $this->player_functions->getPlayerMainWalletBalance($playerId);

// 						//var_dump($playerMainWalletBalance['totalBalanceAmount']);exit();
	// 						//if($playerMainWalletBalance['totalBalanceAmount'] == 0){ //if balance if 0 already

// 						if ($promoDetails[0]['bonusApplicationLimitRule'] == 0) {
	// 							//no limit

// 							//get bet amount requirement
	// 							$betAmountReq = ($playerDuplicatePromo[count($playerDuplicatePromo) - 1]['depositAmount'] + $playerDuplicatePromo[count($playerDuplicatePromo) - 1]['bonusAmount']) * $promoDetails[0]['repeatConditionBetCnt'];
	// 							//var_dump($betAmountReq);exit();

// 							//get required game type
	// 							$promoRuleGameType = $this->promo_functions->getPromoRuleGameType($promorulesId);
	// 							//var_dump($promoRuleGameType);exit();

// 							//get player name
	// 							$playerName = $this->authentication->getUsername();
	// 							//$playerName = 'rhaidan08';
	// 							//get game total bet
	// 							foreach ($promoRuleGameType as $key) {
	// 								//echo $key['gameType'];
	// 								$playerCurrentBetAmt[] = $this->player_functions->getPlayerCurrentBetAmt($playerName, $key['gameType'], $playerDuplicatePromo[count($playerDuplicatePromo) - 1]['validityStartDate'], $playerDuplicatePromo[count($playerDuplicatePromo) - 1]['validityEndDate']);
	// 							}

// 							$playerTotalBetAmount = 0;
	// 							foreach ($playerCurrentBetAmt as $key => $data) {
	// 								foreach ($data as $row) {
	// 									$playerTotalBetAmount += $row['totalbet'];
	// 								}
	// 							}

// 							//var_dump($betAmountReq);exit();
	// 							if ($playerTotalBetAmount >= $betAmountReq) {
	// //if bet requirement reached

// 								//check how much bonus player can get
	// 								$this->checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId);

// 							} else {
	// 								//Youre bet requirement did not met yet to join again in this promo!
	// 								$this->session->set_userdata('promoMessage', lang('notify.81'));
	// 								$this->session->unset_userdata('applicationPromoId');
	// 							}
	// 							//var_dump($playerCurrentBetAmt);exit();
	// 						} else {
	// //with limit
	// 							//var_dump($promoDetails[0]['bonusApplicationLimitRuleCnt']);exit();
	// 							if (count($playerDuplicatePromo) < $promoDetails[0]['bonusApplicationLimitRuleCnt']) {
	// 								//if do not exceeds limit

// 								//get bet amount requirement
	// 								$betAmountReq = ($playerDuplicatePromo[count($playerDuplicatePromo) - 1]['depositAmount'] + $playerDuplicatePromo[count($playerDuplicatePromo) - 1]['bonusAmount']) * $promoDetails[0]['repeatConditionBetCnt'];
	// 								//var_dump($betAmountReq);exit();

// 								//get required game type
	// 								$promoRuleGameType = $this->promo_functions->getPromoRuleGameType($promorulesId);
	// 								//var_dump($promoRuleGameType);exit();

// 								//get player name
	// 								$playerName = $this->authentication->getUsername();
	// 								//$playerName = 'rhaidan08';
	// 								//get game total bet
	// 								foreach ($promoRuleGameType as $key) {
	// 									//echo $key['gameType'];
	// 									$playerCurrentBetAmt[] = $this->player_functions->getPlayerCurrentBetAmt($playerName, $key['gameType'], $playerDuplicatePromo[count($playerDuplicatePromo) - 1]['validityStartDate'], $playerDuplicatePromo[count($playerDuplicatePromo) - 1]['validityEndDate']);
	// 								}

// 								$playerTotalBetAmount = 0;
	// 								foreach ($playerCurrentBetAmt as $key => $data) {
	// 									foreach ($data as $row) {
	// 										$playerTotalBetAmount += $row['totalbet'];
	// 									}
	// 								}

// 								//var_dump($playerTotalBetAmount);exit();
	// 								if ($playerTotalBetAmount >= $betAmountReq) {
	// //if bet requirement reached

// 									//check how much bonus player can get
	// 									$this->checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId);

// 								} else {
	// 									//var_dump(lang('notify.81'));exit();
	// 									//Youre bet requirement did not met yet to join again in this promo!
	// 									$this->session->set_userdata('promoMessage', lang('notify.81'));
	// 									$this->session->unset_userdata('applicationPromoId');
	// 								}
	// 							} else {
	// //if exceeds limit
	// 								//var_dump(lang('notify.82'));exit();
	// 								//You cannot join this promo anymore because you exceeds join promotion limit.
	// 								$this->session->set_userdata('promoMessage', lang('notify.82'));
	// 								$this->session->unset_userdata('applicationPromoId');
	// 							}
	// 						}
	// 					}
	// 				} else {
	// 					//no repeat application

// 					//get duplicate promo
	// 					$playerDuplicatePromo = $this->player_functions->getPlayerDuplicatePromo($playerId, $promorulesId);

// 					//var_dump(count($playerDuplicatePromo));exit();
	// 					if (count($playerDuplicatePromo) == 0) {
	// //no existing same promo

// 						//check how much bonus player can get
	// 						$this->checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId);
	// 					} else {
	// 						//var_dump(lang('notify.83'));exit();
	// 						//You cannot join this promo again you already joined this promo already.
	// 						$this->session->set_userdata('promoMessage', lang('notify.83'));
	// 						$this->session->unset_userdata('applicationPromoId');
	// 					}

// 				}
	// 			}
	// 		}
	// 	}

// 	/**
	// 	 * apply non deposit promo
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function applyNonDepositPromo($promorulesId, $promoCmsSettingId) {
	// 		$this->verifyNonDepositPromoApplication($promorulesId, $promoCmsSettingId);
	// 	}

// 	/**
	// 	 * verify promo application
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function verifyNonDepositPromoApplication($promorulesId, $promoCmsSettingId = '') {
	// 		//get playerid
	// 		$playerId = $this->authentication->getPlayerId();

// 		//check if player level is valid in this promo
	// 		$playerLevelFlag = $this->player_functions->checkDepositPromoLevelRule($playerId, $promorulesId);

// 		//get application period
	// 		$applicationPeriodFlag = $this->promo_functions->checkPromoPeriodApplication($promorulesId, 'nondeposit');
	// 		//var_dump($applicationPeriodFlag);exit();
	// 		//get promo details
	// 		$promoDetails = $this->player_functions->getPromoDetails($promorulesId);
	// 		//var_dump($promoDetails);exit();

// 		//get playerPromoRequest
	// 		$playerPromoRequest = $this->player_functions->getPlayerDuplicatePromo($playerId, $promorulesId, 0);
	// 		//var_dump($playerPromoRequest);exit();

// 		//check if player has existing non deposit promo (by email,by registration/complete registration,mobile,by mobile confirmation)
	// 		$isPlayerNonDepositPromoExistFlag = $this->player_functions->isPlayerNonDepositPromoExist($playerId, $promorulesId);
	// 		//var_dump($isPlayerNonDepositPromoExistFlag);exit();

// 		if ($isPlayerNonDepositPromoExistFlag) {
	// 			//if player joined this kind of promo already

// 			//Sorry, you cannot join in this kind of promo again!
	// 			$message = lang('notify.89');
	// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/viewCashier');
	// 		} elseif ($playerLevelFlag == false) {
	// 			//if playerlevel is invalid
	// 			//var_dump($playerLevelFlag);exit();
	// 			//Your player level is not valid in this promo! Please contact customer service for more information
	// 			$message = lang('notify.35');
	// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/viewCashier');
	// 		} elseif ($applicationPeriodFlag == false) {
	// //if application period is invalid
	// 			//var_dump($applicationPeriodFlag);exit();

// 			//You are not allowed to join due to invalid applicaton period!
	// 			$message = lang('notify.78');
	// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/viewCashier');
	// 		} elseif (!empty($playerPromoRequest)) {
	// //if promo request exists
	// 			//var_dump($applicationPeriodFlag);exit();

// 			//you have existing promo request already
	// 			$message = lang('notify.34');
	// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/viewCashier');
	// 		} else {
	// //this means the playerlevel and application period is valid
	// 			//var_dump('valid application');exit();
	// 			if ($promoDetails[0]['bonusApplicationRule'] == 0) {
	// 				//repeat application
	// 				/*note: player must lose all money before repeat promo again
	// 				player may apply automatically if no existing promo
	// 				 */

// 				//get duplicate promo
	// 				$playerDuplicatePromo = $this->player_functions->getPlayerDuplicatePromo($playerId, $promorulesId);
	// 				if ($playerDuplicatePromo == 0) {
	// //no existing same promo
	// 					//var_dump($playerDuplicatePromo);exit();
	// 					//check how much bonus player can get
	// 					$this->checkBonusConditionNonDepositPromo($playerId, $promorulesId, $promoCmsSettingId);
	// 				} else {
	// //with existing promo

// 					/*note: player must meet the ff condition to repeat promo
	// 					1.) Must lose all the money or zero wallet balance before join the promo
	// 					2.) Must meet bet amount requirement that was set in the promo rules
	// 					 */

// 					//check player wallet account (main/sub)
	// 					$isPlayerWalletAccountZeroFlag = $this->player_functions->isPlayerWalletAccountZero($playerId);

// 					if ($isPlayerWalletAccountZeroFlag) {
	// 						// player is zero balance

// 						//get promo details
	// 						$promoDetails = $this->player_functions->getPromoDetails($promorulesId);
	// 						$repeatPromoRequireBetAmount = $promoDetails[0]['bonusAmount'] * $promoDetails[0]['repeatConditionBetCnt'];

// 						//get player required details
	// 						$playerName = $this->authentication->getUsername();
	// 						$playerStartDate = $this->player_functions->getPlayerStartDate($playerId);

// 						//get player total bet
	// 						$data = $this->player_functions->getPlayerTotalBet($playerName, $playerStartDate['date'], date('Y-m-d H:i:s'));

// 						if ($data['currentTotalBet'] == null) {
	// 							$currentTotalBet = 0;
	// 						} else {
	// 							$currentTotalBet = $data['currentTotalBet'];
	// 						}

// 						//var_dump($currentTotalBet,$gameRequiredBet);exit();
	// 						if ($currentTotalBet >= $repeatPromoRequireBetAmount) {
	// 							//if repeat requirement met

// 							if ($promoDetails[0]['bonusApplicationLimitRule'] == 0) {
	// 								//no limit

// 								//check how much bonus player can get
	// 								$this->checkBonusConditionNonDepositPromo($playerId, $promorulesId, $promoCmsSettingId);
	// 							} else {
	// //with limit

// 								//var_dump($promoDetails[0]['bonusApplicationLimitRuleCnt']);exit();
	// 								if (count($playerDuplicatePromo) < $promoDetails[0]['bonusApplicationLimitRuleCnt']) {
	// //if do not exceeds limit

// 									//check how much bonus player can get
	// 									$this->checkBonusConditionNonDepositPromo($playerId, $promorulesId, $promoCmsSettingId);
	// 								} else {
	// //if exceeds limit
	// 									//var_dump(lang('notify.82'));exit();
	// 									$this->session->unset_userdata('applicationPromoId');

// 									//You cannot join this promo anymore because you exceeds join promotion limit.
	// 									$message = lang('notify.82');
	// 									$this->alertMessage(2, $message);
	// 									redirect(BASEURL . 'smartcashier/viewCashier');
	// 								}
	// 							}
	// 						} else {
	// 							//repeat requirement did not met
	// 							//Your promo application has been declined due to insufficient bet.
	// 							$message = lang('notify.95');
	// 							$this->alertMessage(2, $message);
	// 							redirect(BASEURL . 'smartcashier/viewCashier');
	// 						}
	// 					} else {
	// 						//Sorry, promo application has been declined, you still have remaining balance in your account
	// 						$message = lang('notify.96');
	// 						$this->alertMessage(2, $message);
	// 						redirect(BASEURL . 'smartcashier/viewCashier');
	// 					}
	// 				}
	// 			} else {
	// 				//no repeat
	// 				//get duplicate promo
	// 				$playerDuplicatePromo = $this->player_functions->getPlayerDuplicatePromo($playerId, $promorulesId);

// 				if ($playerDuplicatePromo == 0) {
	// //no existing same promo
	// 					//check how much bonus player can get
	// 					$this->checkBonusConditionNonDepositPromo($playerId, $promorulesId, $promoCmsSettingId);
	// 				} else {
	// 					//var_dump(lang('notify.83'));exit();
	// 					$this->session->unset_userdata('applicationPromoId');

// 					//You cannot join this promo again you already joined this promo already.
	// 					$message = lang('notify.83');
	// 					$this->alertMessage(2, $message);
	// 					redirect(BASEURL . 'smartcashier/viewCashier');
	// 				}
	// 			}
	// 		}
	// 	}

// 	/**
	// 	 * check bonus condition for deposit promo
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function checkBonusCondition($playerId, $depositAmount, $promorulesId, $promoCmsSettingId) {
	// 		//get promo details
	// 		$promoDetails = $this->player_functions->getPromoDetails($promorulesId);
	// 		//var_dump($promoDetails[0]['depositConditionType']);exit();
	// 		//check if deposit condition type is 0=fixed deposit amount or 1=non fixed deposit amount
	// 		if ($promoDetails[0]['depositConditionType'] == 1) {
	// 			//var_dump($promoDetails[0]['depositConditionNonFixedDepositAmount']);exit();
	// 			if ($promoDetails[0]['depositConditionNonFixedDepositAmount'] == 0) {
	// //0 = deposit is <= or >= amount condition

// 				if ($promoDetails[0]['nonfixedDepositAmtCondition'] == 0) {
	// //0 = deposit is <= amount condition

// 					if ($depositAmount <= $promoDetails[0]['nonfixedDepositAmtConditionRequiredDepositAmount']) {
	// 						//if required deposit amount met
	// 						//var_dump($promoDetails[0]['depositPercentage']);exit();
	// 						//check bonus if base on 0-fixed amount or 1-percentage
	// 						if ($promoDetails[0]['bonusReleaseRule'] != 0) {
	// 							$playerBonusAmount = $depositAmount * ($promoDetails[0]['depositPercentage'] / 100);

// 							//check if bonus is more than maxBonusAmount
	// 							if ($playerBonusAmount >= $promoDetails[0]['maxBonusAmount']) {
	// 								$playerBonusAmount = $promoDetails[0]['maxBonusAmount'];
	// 							}

// 						} else {
	// 							$playerBonusAmount = $promoDetails[0]['bonusAmount'];
	// 						}

// 						//var_dump($promoCmsSettingId);exit();

// 						$data = array('playerId' => $playerId,
	// 							'promorulesId' => $promorulesId,
	// 							'bonusAmount' => $playerBonusAmount,
	// 							'depositAmount' => $depositAmount,
	// 							'promoCmsSettingId' => $promoCmsSettingId,
	// 							//'promoExpiration' => date("Y/m/d", $expirationDate),
	// 							'transactionStatus' => 0, //request
	// 						);

// 						$playerdepositpromoId = $this->player_functions->applyDepositPromo($data);

// 						//Your promo application has been sent!
	// 						$this->session->set_userdata('promoMessage', lang('notify.36'));

// 						//save approved promoId to session
	// 						$this->session->set_userdata('playerPromoId', $playerdepositpromoId);

// 						return TRUE;
	// 					} else {
	// 						//$message = $this->session->set_userdata('promoMessage','You have insufficient amount of deposit to join the promo!');
	// 						$message = $this->session->set_userdata('promoMessage', lang('notify.37'));
	// 						//$this->alertMessage(2, $message);
	// 					}

// 				} else {
	// 					//1 = deposit is >= amount condition

// 					if ($depositAmount >= $promoDetails[0]['nonfixedDepositAmtConditionRequiredDepositAmount']) {
	// 						//if required deposit amount met
	// 						//var_dump($depositAmount);exit();
	// 						//check bonus if base on 0-fixed amount or 1-percentage
	// 						if ($promoDetails[0]['bonusReleaseRule'] != 0) {
	// 							$playerBonusAmount = $depositAmount * ($promoDetails[0]['depositPercentage'] / 100);

// 							//check if bonus is more than maxBonusAmount
	// 							if ($playerBonusAmount >= $promoDetails[0]['maxBonusAmount']) {
	// 								$playerBonusAmount = $promoDetails[0]['maxBonusAmount'];
	// 							}

// 						} else {
	// 							$playerBonusAmount = $promoDetails[0]['bonusAmount'];
	// 						}

// 						//var_dump($playerBonusAmount);exit();

// 						$data = array('playerId' => $playerId,
	// 							'promorulesId' => $promorulesId,
	// 							'bonusAmount' => $playerBonusAmount,
	// 							'depositAmount' => $depositAmount,
	// 							'promoCmsSettingId' => $promoCmsSettingId,
	// 							'transactionStatus' => 0, //request
	// 						);

// 						$playerdepositpromoId = $this->player_functions->applyDepositPromo($data);

// 						//Your promo application has been sent!
	// 						$this->session->set_userdata('promoMessage', lang('notify.36'));

// 						//save approved promoId to session
	// 						$this->session->set_userdata('playerPromoId', $playerdepositpromoId);

// 						return TRUE;
	// 					} else {
	// 						//$message = $this->session->set_userdata('promoMessage','You have insufficient amount of deposit to join the promo!');
	// 						$message = $this->session->set_userdata('promoMessage', lang('notify.37'));
	// 						//$this->alertMessage(2, $message);
	// 					}
	// 				}
	// 			} else {
	// 				// 1 = any deposit amount condition
	// 				if ($promoDetails[0]['bonusReleaseRule'] != 0) {
	// 					$playerBonusAmount = $depositAmount * ($promoDetails[0]['depositPercentage'] / 100);

// 					//check if bonus is more than maxBonusAmount
	// 					if ($playerBonusAmount >= $promoDetails[0]['maxBonusAmount']) {
	// 						$playerBonusAmount = $promoDetails[0]['maxBonusAmount'];
	// 					}

// 				} else {
	// 					$playerBonusAmount = $promoDetails[0]['bonusAmount'];
	// 				}

// 				//var_dump($playerBonusAmount);exit();

// 				$data = array('playerId' => $playerId,
	// 					'promorulesId' => $promorulesId,
	// 					'depositAmount' => $depositAmount,
	// 					'bonusAmount' => $playerBonusAmount,
	// 					'promoCmsSettingId' => $promoCmsSettingId,
	// 					//'promoExpiration' => date("Y/m/d", $expirationDate),
	// 					'transactionStatus' => 0, //request
	// 				);

// 				$playerdepositpromoId = $this->player_functions->applyDepositPromo($data);

// 				//Your promo application has been sent!
	// 				$this->session->set_userdata('promoMessage', lang('notify.36'));

// 				//save approved promoId to session
	// 				$this->session->set_userdata('playerPromoId', $playerdepositpromoId);

// 				return TRUE;
	// 			}
	// 		} else {
	// 			// fixed deposit amount
	// 			//var_dump($promoDetails[0]['depositConditionDepositAmount']);exit();
	// 			if ($depositAmount == $promoDetails[0]['depositConditionDepositAmount']) {
	// 				//if required deposit amount met
	// 				//var_dump($promoDetails[0]['depositPercentage']);exit();
	// 				//check bonus if base on 0-fixed amount or 1-percentage
	// 				if ($promoDetails[0]['bonusReleaseRule'] != 0) {
	// 					$playerBonusAmount = $depositAmount * ($promoDetails[0]['depositPercentage'] / 100);

// 					//check if bonus is more than maxBonusAmount
	// 					if ($playerBonusAmount >= $promoDetails[0]['maxBonusAmount']) {
	// 						$playerBonusAmount = $promoDetails[0]['maxBonusAmount'];
	// 					}

// 				} else {
	// 					$playerBonusAmount = $promoDetails[0]['bonusAmount'];
	// 				}

// 				//var_dump($playerBonusAmount);exit();

// 				$data = array('playerId' => $playerId,
	// 					'promorulesId' => $promorulesId,
	// 					'depositAmount' => $depositAmount,
	// 					'bonusAmount' => $playerBonusAmount,
	// 					'promoCmsSettingId' => $promoCmsSettingId,
	// 					//'promoExpiration' => date("Y/m/d", $expirationDate),
	// 					'transactionStatus' => 0, //request
	// 				);

// 				$playerdepositpromoId = $this->player_functions->applyDepositPromo($data);

// 				//Your promo application has been sent!
	// 				$this->session->set_userdata('promoMessage', lang('notify.36'));

// 				//save approved promoId to session
	// 				$this->session->set_userdata('playerPromoId', $playerdepositpromoId);

// 				return TRUE;
	// 			} else {
	// 				//var_dump(lang('notify.79'));exit();
	// 				//Required deposit amount did not met!
	// 				$message = $this->session->set_userdata('promoMessage', lang('notify.79'));
	// 				//$this->alertMessage(2, $message);
	// 			}
	// 			// }else{
	// 			//     //Your deposit amount is not equal to required deposit amount!
	// 			//     $message = $this->session->set_userdata('promoMessage',lang('promo.msg1'));
	// 			// }
	// 		}
	// 	}

// 	/**
	// 	 * check bonus condition for non deposit promo
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function checkBonusConditionNonDepositPromo($playerId, $promorulesId, $promoCmsSettingId) {
	// 		//get promo details
	// 		$promoDetails = $this->player_functions->getPromoDetails($promorulesId);
	// 		$playerBonusAmount = $promoDetails[0]['bonusAmount'];
	// 		$autoReleaseFlag = $promoDetails[0]['bonusReleaseToPlayer'] == 0 ? true : false;

// 		//promo type
	// 		$promoType = $promoDetails[0]['nonDepositPromoType'];

// 		//get mainwallet balance
	// 		$playerMainWalletBalance = $this->player_functions->getMainWalletBalance($playerId);
	// 		if ($playerMainWalletBalance['totalBalanceAmount'] == null) {
	// 			$playerMainWalletBalance['totalBalanceAmount'] = 0;
	// 		}
	// 		$currentMainwalletBal = $playerMainWalletBalance['totalBalanceAmount'];

// 		if ($autoReleaseFlag) {
	// //auto release
	// 			//process auto approve
	// 			$this->autoApprovePromo($promoType, $playerId, $currentMainwalletBal, $playerBonusAmount, $promorulesId, $promoCmsSettingId);

// 		} else {
	// //manual release
	// 			//process manual approve
	// 			$this->manualApprovePromo($promoType, $playerId, $currentMainwalletBal, $playerBonusAmount, $promorulesId, $promoCmsSettingId);
	// 		}
	// 	}

// 	/**
	// 	 * processs manual approve promo
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function manualApprovePromo($promoType, $playerId, $currentMainwalletBal, $playerBonusAmount, $promorulesId, $promoCmsSettingId) {

// 		switch ($promoType) {
	// 			case 0: //by email confirmation promo

// 				//apply promo
	// 				$data = array('playerId' => $playerId,
	// 					'promorulesId' => $promorulesId,
	// 					'bonusAmount' => $playerBonusAmount,
	// 					'promoCmsSettingId' => $promoCmsSettingId,
	// 					'transactionStatus' => 0, //request
	// 				);

// 				$playerPromoId = $this->player_functions->applyDepositPromo($data);
	// 				$this->sendEmail($playerId, $playerPromoId);

// 				//Your promo application has been approved, confirm the link sent to your email to get your bonus.

// 				$message = lang('notify.91');
	// 				$this->alertMessage(2, $message);
	// 				break;

// 			case 2: //registration promo
	// 				//apply promo
	// 				$data = array('playerId' => $playerId,
	// 					'promorulesId' => $promorulesId,
	// 					'bonusAmount' => $playerBonusAmount,
	// 					'promoCmsSettingId' => $promoCmsSettingId,
	// 					'transactionStatus' => 0, //request
	// 				);
	// 				$this->player_functions->applyDepositPromo($data);

// 				//Your promo application has been sent for review, once approved bonus amount will automatically add to your wallet account.
	// 				$message = lang('notify.92');
	// 				$this->alertMessage(2, $message);
	// 				break;

// 			case 3: //complete registration promo

// 				$isPlayerCompleteRegistrationFlag = $this->player_functions->getPlayerRegistrationStatus($playerId);
	// 				//var_dump($isPlayerCompleteRegistrationFlag);exit();
	// 				if (!$isPlayerCompleteRegistrationFlag) {
	// //player registration is incomplete
	// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'transactionStatus' => 0, //request
	// 						'verificationStatus' => 0, //means incomplete registration
	// 					);
	// 					$this->player_functions->applyDepositPromo($data);
	// 				} else {
	// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'transactionStatus' => 0, //request
	// 						'verificationStatus' => 1, //means complete registration
	// 					);
	// 					$this->player_functions->applyDepositPromo($data);
	// 				}

// 				//Your promo application has been sent for review, once approved bonus amount will automatically add to your wallet account.
	// 				$message = lang('notify.92');
	// 				$this->alertMessage(2, $message);
	// 				break;

// 			case 4: //by betting

// 				//get promo details
	// 				$promoDetails = $this->player_functions->getPromoDetails($promorulesId);
	// 				$gameRecordStartDate = $promoDetails[0]['gameRecordStartDate'];
	// 				$gameRecordEndDate = $promoDetails[0]['gameRecordEndDate'];
	// 				$gameRequiredBet = $promoDetails[0]['gameRequiredBet'];
	// 				$playerName = $this->authentication->getUsername();

// 				$data = $this->player_functions->getPlayerTotalBet($playerName, $gameRecordStartDate, $gameRecordEndDate);

// 				//$data = $this->player_functions->getPlayerTotalBet('rhaicese08','2015-05-17 10:54:25','2015-05-30 16:38:04');
	// 				if ($data['currentTotalBet'] == null) {
	// 					$currentTotalBet = 0;
	// 				} else {
	// 					$currentTotalBet = $data['currentTotalBet'];
	// 				}
	// 				//var_dump($currentTotalBet,$gameRequiredBet);exit();
	// 				if ($currentTotalBet >= $gameRequiredBet) {
	// 					//if required bet met
	// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'transactionStatus' => 0, //request
	// 						'verificationStatus' => 1, //means met required bet
	// 					);
	// 					$this->player_functions->applyDepositPromo($data);
	// 				} else {

// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'transactionStatus' => 0, //request
	// 						'verificationStatus' => 0, //means dit not met required bet
	// 					);
	// 					$this->player_functions->applyDepositPromo($data);
	// 				}
	// 				//Your promo application has been sent for review, once approved bonus amount will automatically add to your wallet account.
	// 				$message = lang('notify.92');
	// 				$this->alertMessage(2, $message);
	// 				break;

// 			case 5: //by lossing

// 				//get promo details
	// 				$promoDetails = $this->player_functions->getPromoDetails($promorulesId);
	// 				$gameRecordStartDate = $promoDetails[0]['gameRecordStartDate'];
	// 				$gameRecordEndDate = $promoDetails[0]['gameRecordEndDate'];
	// 				$gameRequiredBet = $promoDetails[0]['gameRequiredBet'];
	// 				$playerName = $this->authentication->getUsername();

// 				$data = $this->player_functions->getPlayerTotalLoss($playerName, $gameRecordStartDate, $gameRecordEndDate);
	// 				//$totalLoss = $this->player_functions->getPlayerTotalLoss('rhaicese08','2015-05-17 10:54:25','2015-05-30 16:38:04');

// 				if ($data['currentTotalLoss'] == null) {
	// 					$currentTotalLoss = 0;
	// 				} else {
	// 					$currentTotalLoss = $data['currentTotalLoss'];
	// 				}
	// 				//var_dump($currentTotalLoss,$gameRequiredBet);exit();
	// 				if ($currentTotalLoss >= $gameRequiredBet) {
	// 					//gameRequiredBet = gameRequiredLoss, need change field name if necessary
	// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'transactionStatus' => 0, //request
	// 						'verificationStatus' => 1, //means met required loss
	// 					);
	// 					$this->player_functions->applyDepositPromo($data);
	// 				} else {

// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'transactionStatus' => 0, //request
	// 						'verificationStatus' => 0, //means dit not met required loss
	// 					);
	// 					$this->player_functions->applyDepositPromo($data);
	// 				}
	// 				//Your promo application has been sent for review, once approved bonus amount will automatically add to your wallet account.
	// 				$message = lang('notify.92');
	// 				$this->alertMessage(2, $message);
	// 				break;

// 			case 6: //by winning

// 				//get promo details
	// 				$promoDetails = $this->player_functions->getPromoDetails($promorulesId);
	// 				$gameRecordStartDate = $promoDetails[0]['gameRecordStartDate'];
	// 				$gameRecordEndDate = $promoDetails[0]['gameRecordEndDate'];
	// 				$gameRequiredBet = $promoDetails[0]['gameRequiredBet'];
	// 				$playerName = $this->authentication->getUsername();

// 				$data = $this->player_functions->getPlayerTotalWin($playerName, $gameRecordStartDate, $gameRecordEndDate);
	// 				//$totalLoss = $this->player_functions->getPlayerTotalLoss('rhaicese08','2015-05-17 10:54:25','2015-05-30 16:38:04');

// 				if ($data['currentTotalWin'] == null) {
	// 					$currentTotalWin = 0;
	// 				} else {
	// 					$currentTotalWin = $data['currentTotalWin'];
	// 				}
	// 				//var_dump($currentTotalWin,$gameRequiredBet);exit();
	// 				if ($currentTotalWin >= $gameRequiredBet) {
	// 					//gameRequiredBet = gameRequiredLoss, need change field name if necessary
	// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'transactionStatus' => 0, //request
	// 						'verificationStatus' => 1, //means met required win
	// 					);
	// 					$this->player_functions->applyDepositPromo($data);
	// 				} else {

// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'transactionStatus' => 0, //request
	// 						'verificationStatus' => 0, //means dit not met required win
	// 					);
	// 					$this->player_functions->applyDepositPromo($data);
	// 				}
	// 				//Your promo application has been sent for review, once approved bonus amount will automatically add to your wallet account.
	// 				$message = lang('notify.92');
	// 				$this->alertMessage(2, $message);
	// 				break;

// 		}

// 		redirect(BASEURL . 'smartcashier/viewCashier', 'refresh');
	// 	}

// 	public function testFunction() {
	// 		$promorules = $this->promo_functions->getConditionAmount(117);
	// 		if ($promorules[0]['withdrawRequirementConditionType'] == 0) {
	// 			$withdrawBetAmtCondition = $promorules[0]['withdrawRequirementBetAmount'];
	// 		} else {
	// 			$withdrawBetAmtCondition = ($depositAmount + $bonusAmount) * $promorules['withdrawRequirementBetCntCondition'];
	// 		}
	// 		var_dump($withdrawBetAmtCondition);exit();
	// 	}

// 	/**
	// 	 * processs auto approve promo
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function autoApprovePromo($promoType, $playerId, $currentMainwalletBal, $playerBonusAmount, $promorulesId, $promoCmsSettingId) {
	// 		//var_dump($promoType);exit();
	// 		switch ($promoType) {
	// 			case 0: //by email confirmation
	// 				//approved promo
	// 				$data = array('playerId' => $playerId,
	// 					'promorulesId' => $promorulesId,
	// 					'bonusAmount' => $playerBonusAmount,
	// 					'promoCmsSettingId' => $promoCmsSettingId,
	// 					'dateProcessed' => date('Y-m-d H:i:s'),
	// 					'processedBy' => 1,
	// 					'transactionStatus' => 0, //apply temporarily until player confirm the email
	// 				);
	// 				$playerPromoId = $this->player_functions->applyDepositPromo($data);

// 				$promorules = $this->promo_functions->getConditionAmount($playerPromoId);

// 				if ($promorules[0]['withdrawRequirementConditionType'] == 0) {
	// 					$withdrawBetAmtCondition = $promorules[0]['withdrawRequirementBetAmount'];
	// 				} else {
	// 					$withdrawBetAmtCondition = $bonusAmount * $promorules['withdrawRequirementBetCntCondition'];
	// 				}

// 				//add to withdrawal conditions to deposit with promo
	// 				$withdrawalConditionData = array('source_id' => $playerPromoId,
	// 					'source_type' => 3, //player promotion
	// 					'started_at' => date('Y-m-d H:i:s'),
	// 					'condition_amount' => $withdrawBetAmtCondition,
	// 					'status' => 1, //enabled
	// 					'player_id' => $playerId,
	// 					'promotion_id' => $promorules[0]['promorulesId'],
	// 				);

// 				$this->promo_functions->savePlayerWithdrawalCondition($withdrawalConditionData);

// 				//Your promo application has been approved, confirm the link sent to your email to get your bonus
	// 				$message = lang('notify.91');
	// 				$this->alertMessage(2, $message);
	// 				break;

// 			case 1: //mobile confirmation
	// 				break;

// 			case 2: //registration promo
	// 				//approved promo
	// 				$data = array('playerId' => $playerId,
	// 					'promorulesId' => $promorulesId,
	// 					'bonusAmount' => $playerBonusAmount,
	// 					'promoCmsSettingId' => $promoCmsSettingId,
	// 					'dateProcessed' => date('Y-m-d H:i:s'),
	// 					'processedBy' => 1,
	// 					'transactionStatus' => 1, //approved
	// 				);
	// 				$playerPromoId = $this->player_functions->applyDepositPromo($data);

// 				$promorules = $this->promo_functions->getConditionAmount($playerPromoId);

// 				if ($promorules[0]['withdrawRequirementConditionType'] == 0) {
	// 					$withdrawBetAmtCondition = $promorules[0]['withdrawRequirementBetAmount'];
	// 				} else {
	// 					$withdrawBetAmtCondition = $bonusAmount * $promorules['withdrawRequirementBetCntCondition'];
	// 				}

// 				//add to withdrawal conditions to deposit with promo
	// 				$withdrawalConditionData = array('source_id' => $playerPromoId,
	// 					'source_type' => 3, //player promotion
	// 					'started_at' => date('Y-m-d H:i:s'),
	// 					'condition_amount' => $withdrawBetAmtCondition,
	// 					'status' => 1, //enabled
	// 					'player_id' => $playerId,
	// 					'promotion_id' => $promorules[0]['promorulesId'],
	// 				);

// 				$this->promo_functions->savePlayerWithdrawalCondition($withdrawalConditionData);

// 				//save transaction details on bonus
	// 				$transactionDetails = array('amount' => $playerBonusAmount,
	// 					'transaction_type' => 8, //add bonus
	// 					'from_id' => 1, //admin id = auto
	// 					'from_type' => 0, //admin
	// 					'to_id' => $playerId,
	// 					'to_type' => 1, //player
	// 					'note' => 'add bonus ' . $playerBonusAmount . ' to ' . $playerId,
	// 					'before_balance' => $currentMainwalletBal,
	// 					'after_balance' => $currentMainwalletBal + $playerBonusAmount,
	// 					'status' => 0, //approved
	// 				);
	// 				$this->player_functions->saveTransaction($transactionDetails);

// 				$newMainwalletBal = $currentMainwalletBal + $playerBonusAmount;
	// 				$playerAcctdata = array('totalBalanceAmount' => $newMainwalletBal);
	// 				$this->player_functions->setPlayerNewMainWalletBalAmount($playerId, $playerAcctdata);

// 				//Your promo application has been approved bonus amount was added to your main wallet account
	// 				$message = lang('notify.90');
	// 				$this->alertMessage(2, $message);
	// 				break;

// 			case 3: //complete registration promo
	// 				$isPlayerCompleteRegistrationFlag = $this->player_functions->getPlayerRegistrationStatus($playerId);
	// 				//var_dump($isPlayerCompleteRegistrationFlag);exit();
	// 				if (!$isPlayerCompleteRegistrationFlag) {
	// //player registration is incomplete
	// 					//decline promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'dateProcessed' => date('Y-m-d H:i:s'),
	// 						'processedBy' => 1,
	// 						'transactionStatus' => 2, //declined
	// 					);
	// 					$this->player_functions->applyDepositPromo($data);

// 					//Your promo application has been declined due to incomplete registration
	// 					$message = lang('notify.93');
	// 					$this->alertMessage(2, $message);
	// 				} else {
	// 					//complete registration
	// 					//approved promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'transactionStatus' => 1, //approved
	// 						'dateProcessed' => date('Y-m-d H:i:s'),
	// 						'processedBy' => 1,
	// 					);
	// 					$playerPromoId = $this->player_functions->applyDepositPromo($data);

// 					$promorules = $this->promo_functions->getConditionAmount($playerPromoId);

// 					if ($promorules[0]['withdrawRequirementConditionType'] == 0) {
	// 						$withdrawBetAmtCondition = $promorules[0]['withdrawRequirementBetAmount'];
	// 					} else {
	// 						$withdrawBetAmtCondition = $bonusAmount * $promorules['withdrawRequirementBetCntCondition'];
	// 					}

// 					//add to withdrawal conditions to deposit with promo
	// 					$withdrawalConditionData = array('source_id' => $playerPromoId,
	// 						'source_type' => 3, //player promotion
	// 						'started_at' => date('Y-m-d H:i:s'),
	// 						'condition_amount' => $withdrawBetAmtCondition,
	// 						'status' => 1, //enabled
	// 						'player_id' => $playerId,
	// 						'promotion_id' => $promorules[0]['promorulesId'],
	// 					);

// 					$this->promo_functions->savePlayerWithdrawalCondition($withdrawalConditionData);

// 					//save transaction details on bonus
	// 					$transactionDetails = array('amount' => $playerBonusAmount,
	// 						'transaction_type' => 8, //add bonus
	// 						'from_id' => 1, //admin id = auto
	// 						'from_type' => 0, //admin
	// 						'to_id' => $playerId,
	// 						'to_type' => 1, //player
	// 						'note' => 'add bonus ' . $playerBonusAmount . ' to ' . $playerId,
	// 						'before_balance' => $currentMainwalletBal,
	// 						'after_balance' => $currentMainwalletBal + $playerBonusAmount,
	// 						'status' => 0, //approved
	// 					);
	// 					$this->player_functions->saveTransaction($transactionDetails);

// 					//save transaction details on bonus
	// 					$transactionDetails = array('amount' => $playerBonusAmount,
	// 						'transaction_type' => 8, //add bonus
	// 						'from_id' => 1, //admin id = auto
	// 						'from_type' => 0, //admin
	// 						'to_id' => $playerId,
	// 						'to_type' => 1, //player
	// 						'note' => 'add bonus ' . $playerBonusAmount . ' to ' . $playerId,
	// 						'before_balance' => $currentMainwalletBal,
	// 						'after_balance' => $currentMainwalletBal + $playerBonusAmount,
	// 						'status' => 0, //approved
	// 					);
	// 					$this->player_functions->saveTransaction($transactionDetails);

// 					$newMainwalletBal = $currentMainwalletBal + $playerBonusAmount;
	// 					$playerAcctdata = array('totalBalanceAmount' => $newMainwalletBal);
	// 					$this->player_functions->setPlayerNewMainWalletBalAmount($playerId, $playerAcctdata);

// 					//Your promo application has been approved bonus amount was added to your main wallet account
	// 					$message = lang('notify.90');
	// 					$this->alertMessage(2, $message);
	// 				}
	// 				break;

// 			case 4: //by betting
	// 				//get promo details
	// 				$promoDetails = $this->player_functions->getPromoDetails($promorulesId);
	// 				$gameRecordStartDate = $promoDetails[0]['gameRecordStartDate'];
	// 				$gameRecordEndDate = $promoDetails[0]['gameRecordEndDate'];
	// 				$gameRequiredBet = $promoDetails[0]['gameRequiredBet'];
	// 				$playerName = $this->authentication->getUsername();

// 				$data = $this->player_functions->getPlayerTotalBet($playerName, $gameRecordStartDate, $gameRecordEndDate);

// 				//$data = $this->player_functions->getPlayerTotalBet('rhaicese08','2015-05-17 10:54:25','2015-05-30 16:38:04');
	// 				if ($data['currentTotalBet'] == null) {
	// 					$currentTotalBet = 0;
	// 				} else {
	// 					$currentTotalBet = $data['currentTotalBet'];
	// 				}
	// 				//var_dump($currentTotalBet,$gameRequiredBet);exit();
	// 				if ($currentTotalBet >= $gameRequiredBet) {
	// 					//if required bet met

// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'dateProcessed' => date('Y-m-d H:i:s'),
	// 						'processedBy' => 1,
	// 						'transactionStatus' => 1, //request
	// 						'verificationStatus' => 1, //means met required bet
	// 					);
	// 					$playerPromoId = $this->player_functions->applyDepositPromo($data);

// 					$promorules = $this->promo_functions->getConditionAmount($playerPromoId);

// 					if ($promorules[0]['withdrawRequirementConditionType'] == 0) {
	// 						$withdrawBetAmtCondition = $promorules[0]['withdrawRequirementBetAmount'];
	// 					} else {
	// 						$withdrawBetAmtCondition = $bonusAmount * $promorules['withdrawRequirementBetCntCondition'];
	// 					}

// 					//add to withdrawal conditions to deposit with promo
	// 					$withdrawalConditionData = array('source_id' => $playerPromoId,
	// 						'source_type' => 3, //player promotion
	// 						'started_at' => date('Y-m-d H:i:s'),
	// 						'condition_amount' => $withdrawBetAmtCondition,
	// 						'status' => 1, //enabled
	// 						'player_id' => $playerId,
	// 						'promotion_id' => $promorules[0]['promorulesId'],
	// 					);

// 					$this->promo_functions->savePlayerWithdrawalCondition($withdrawalConditionData);

// 					//save transaction details on bonus
	// 					$transactionDetails = array('amount' => $playerBonusAmount,
	// 						'transaction_type' => 8, //add bonus
	// 						'from_id' => 1, //admin id = auto
	// 						'from_type' => 0, //admin
	// 						'to_id' => $playerId,
	// 						'to_type' => 1, //player
	// 						'note' => 'add bonus ' . $playerBonusAmount . ' to ' . $playerId,
	// 						'before_balance' => $currentMainwalletBal,
	// 						'after_balance' => $currentMainwalletBal + $playerBonusAmount,
	// 						'status' => 0, //approved
	// 					);
	// 					//var_dump($transactionDetails);exit();
	// 					$this->player_functions->saveTransaction($transactionDetails);

// 					$newMainwalletBal = $currentMainwalletBal + $playerBonusAmount;
	// 					$playerAcctdata = array('totalBalanceAmount' => $newMainwalletBal);
	// 					$this->player_functions->setPlayerNewMainWalletBalAmount($playerId, $playerAcctdata);

// 					//Your promo application has been approved bonus amount was added to your main wallet account
	// 					$message = lang('notify.90');
	// 					$this->alertMessage(2, $message);

// 				} else {

// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'dateProcessed' => date('Y-m-d H:i:s'),
	// 						'processedBy' => 1,
	// 						'transactionStatus' => 2, //declined
	// 						'verificationStatus' => 0, //means dit not met required bet
	// 					);
	// 					$this->player_functions->applyDepositPromo($data);

// 					//Your promo application has been declined due to insufficient bet.
	// 					$message = lang('notify.94');
	// 					$this->alertMessage(2, $message);
	// 				}
	// 				break;

// 			case 5: //by lossing

// 				//get promo details
	// 				$promoDetails = $this->player_functions->getPromoDetails($promorulesId);
	// 				$gameRecordStartDate = $promoDetails[0]['gameRecordStartDate'];
	// 				$gameRecordEndDate = $promoDetails[0]['gameRecordEndDate'];
	// 				$gameRequiredBet = $promoDetails[0]['gameRequiredBet'];
	// 				$playerName = $this->authentication->getUsername();

// 				$data = $this->player_functions->getPlayerTotalLoss($playerName, $gameRecordStartDate, $gameRecordEndDate);
	// 				//$totalLoss = $this->player_functions->getPlayerTotalLoss('rhaicese08','2015-05-17 10:54:25','2015-05-30 16:38:04');

// 				if ($data['currentTotalLoss'] == null) {
	// 					$currentTotalLoss = 0;
	// 				} else {
	// 					$currentTotalLoss = $data['currentTotalLoss'];
	// 				}
	// 				//var_dump($currentTotalLoss,$gameRequiredBet);exit();
	// 				if ($currentTotalLoss >= $gameRequiredBet) {
	// 					//gameRequiredBet = gameRequiredLoss, need change field name if necessary
	// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'dateProcessed' => date('Y-m-d H:i:s'),
	// 						'processedBy' => 1,
	// 						'transactionStatus' => 1, //request
	// 						'verificationStatus' => 1, //means met required bet
	// 					);

// 					$this->promo_functions->savePlayerWithdrawalCondition($withdrawalConditionData);

// 					//save transaction details on bonus
	// 					$transactionDetails = array('amount' => $playerBonusAmount,
	// 						'transaction_type' => 8, //add bonus
	// 						'from_id' => 1, //admin id = auto
	// 						'from_type' => 0, //admin
	// 						'to_id' => $playerId,
	// 						'to_type' => 1, //player
	// 						'note' => 'add bonus ' . $playerBonusAmount . ' to ' . $playerId,
	// 						'before_balance' => $currentMainwalletBal,
	// 						'after_balance' => $currentMainwalletBal + $playerBonusAmount,
	// 						'status' => 0, //approved
	// 					);
	// 					//var_dump($transactionDetails);exit();
	// 					$this->player_functions->saveTransaction($transactionDetails);

// 					$newMainwalletBal = $currentMainwalletBal + $playerBonusAmount;
	// 					$playerAcctdata = array('totalBalanceAmount' => $newMainwalletBal);
	// 					$this->player_functions->setPlayerNewMainWalletBalAmount($playerId, $playerAcctdata);

// 					//Your promo application has been approved bonus amount was added to your main wallet account
	// 					$message = lang('notify.90');
	// 					$this->alertMessage(2, $message);

// 				} else {

// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'dateProcessed' => date('Y-m-d H:i:s'),
	// 						'processedBy' => 1,
	// 						'transactionStatus' => 2, //declined
	// 						'verificationStatus' => 0, //means dit not met required bet
	// 					);
	// 					$this->player_functions->applyDepositPromo($data);

// 					//Your promo application has been declined due to insufficient bet.
	// 					$message = lang('notify.97');
	// 					$this->alertMessage(2, $message);
	// 				}

// 				break;

// 			case 6: //by winning
	// 				//get promo details
	// 				$promoDetails = $this->player_functions->getPromoDetails($promorulesId);
	// 				$gameRecordStartDate = $promoDetails[0]['gameRecordStartDate'];
	// 				$gameRecordEndDate = $promoDetails[0]['gameRecordEndDate'];
	// 				$gameRequiredBet = $promoDetails[0]['gameRequiredBet'];
	// 				$playerName = $this->authentication->getUsername();

// 				$data = $this->player_functions->getPlayerTotalWin($playerName, $gameRecordStartDate, $gameRecordEndDate);
	// 				//$totalLoss = $this->player_functions->getPlayerTotalLoss('rhaicese08','2015-05-17 10:54:25','2015-05-30 16:38:04');

// 				if ($data['currentTotalWin'] == null) {
	// 					$currentTotalWin = 0;
	// 				} else {
	// 					$currentTotalWin = $data['currentTotalWin'];
	// 				}
	// 				//var_dump($currentTotalWin,$gameRequiredBet);exit();
	// 				if ($currentTotalWin >= $gameRequiredBet) {
	// 					//gameRequiredBet = gameRequiredLoss, need change field name if necessary
	// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'dateProcessed' => date('Y-m-d H:i:s'),
	// 						'processedBy' => 1,
	// 						'transactionStatus' => 1, //request
	// 						'verificationStatus' => 1, //means met required bet
	// 					);
	// 					$playerPromoId = $this->player_functions->applyDepositPromo($data);

// 					$promorules = $this->promo_functions->getConditionAmount($playerPromoId);

// 					if ($promorules[0]['withdrawRequirementConditionType'] == 0) {
	// 						$withdrawBetAmtCondition = $promorules[0]['withdrawRequirementBetAmount'];
	// 					} else {
	// 						$withdrawBetAmtCondition = $bonusAmount * $promorules['withdrawRequirementBetCntCondition'];
	// 					}

// 					//add to withdrawal conditions to promo
	// 					$withdrawalConditionData = array('source_id' => $playerPromoId,
	// 						'source_type' => 3, //player promotion
	// 						'started_at' => date('Y-m-d H:i:s'),
	// 						'condition_amount' => $withdrawBetAmtCondition,
	// 						'status' => 1, //enabled
	// 						'player_id' => $playerId,
	// 						'promotion_id' => $promorules[0]['promorulesId'],
	// 					);

// 					$this->promo_functions->savePlayerWithdrawalCondition($withdrawalConditionData);

// 					//save transaction details on bonus
	// 					$transactionDetails = array('amount' => $playerBonusAmount,
	// 						'transaction_type' => 8, //add bonus
	// 						'from_id' => 1, //admin id = auto
	// 						'from_type' => 0, //admin
	// 						'to_id' => $playerId,
	// 						'to_type' => 1, //player
	// 						'note' => 'add bonus ' . $playerBonusAmount . ' to ' . $playerId,
	// 						'before_balance' => $currentMainwalletBal,
	// 						'after_balance' => $currentMainwalletBal + $playerBonusAmount,
	// 						'status' => 0, //approved
	// 					);
	// 					//var_dump($transactionDetails);exit();
	// 					$this->player_functions->saveTransaction($transactionDetails);

// 					$newMainwalletBal = $currentMainwalletBal + $playerBonusAmount;
	// 					$playerAcctdata = array('totalBalanceAmount' => $newMainwalletBal);
	// 					$this->player_functions->setPlayerNewMainWalletBalAmount($playerId, $playerAcctdata);

// 					//Your promo application has been approved bonus amount was added to your main wallet account
	// 					$message = lang('notify.90');
	// 					$this->alertMessage(2, $message);

// 				} else {

// 					//apply promo
	// 					$data = array('playerId' => $playerId,
	// 						'promorulesId' => $promorulesId,
	// 						'bonusAmount' => $playerBonusAmount,
	// 						'promoCmsSettingId' => $promoCmsSettingId,
	// 						'dateProcessed' => date('Y-m-d H:i:s'),
	// 						'processedBy' => 1,
	// 						'transactionStatus' => 2, //declined
	// 						'verificationStatus' => 0, //means dit not met required bet
	// 					);
	// 					$this->player_functions->applyDepositPromo($data);

// 					//Your promo application has been declined due to insufficient bet.
	// 					$message = lang('notify.98');
	// 					$this->alertMessage(2, $message);
	// 				}
	// 				break;

// 			default:
	// 				# code...
	// 				break;
	// 		}

// 		redirect(BASEURL . 'smartcashier/viewCashier', 'refresh');
	// 	}

// 	/**
	// 	 * promoRuleTest
	// 	 *
	// 	 * Unit Testing
	// 	 * @return  rendered template
	// 	 */
	// 	public function promoRuleTest($scenario) {

// 		switch ($scenario) {
	// 			case 1:
	// 				/* Promo Type: deposit promo
	// 				Deposit Condition: Fixed Deposit = 1000
	// 				Deposit Succession: 1st Deposit
	// 				Bonus Release: Fixed Bonus Amount = 100, auto
	// 				Withdraw Requirement: Bet Amount >= 1000
	// 				Allowed Game Type: (AG,PT)
	// 				Allowed Player Level: Default

// 				Note: player must be 1st time deposit to avail this promo
	// 				Expected Result: Bonus Amount = 100
	// 				 */
	// 				$this->verifyPromoApplication(1000, 6, 19);
	// 				break;

// 			case 2:
	// 				/* Promo Type: deposit promo
	// 				Deposit Condition: Fixed Deposit = 2000
	// 				Deposit Succession: 1st Deposit
	// 				Bonus Release: 10% of deposit amount, up to 300 Max Bonus Amount.
	// 				Withdraw Requirement: Bet Amount >= 2000
	// 				Allowed Game Type: (AG,PT)
	// 				Allowed Player Level: Default

// 				Note: player must be 1st time deposit to avail this promo
	// 				Expected Result: Bonus Amount = 200
	// 				 */
	// 				$this->verifyPromoApplication(2000, 7, 19);
	// 				break;

// 			case 3:
	// 				/* Promo Type: deposit promo
	// 				Deposit Condition: Non-Fixed Deposit <= 1000
	// 				Deposit Succession: 1st Deposit
	// 				Bonus Release: Fixed Bonus Amount = 100
	// 				Withdraw Requirement: Bet Amount >= 1000
	// 				Allowed Game Type: (AG,PT)
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Star VIP Group Level Name 1
	// 				Star VIP Group Level Name 2
	// 				Star VIP Group Level Name 3

// 				Note: player must be 1st time deposit to avail this promo
	// 				Expected Result: Bonus Amount = 100
	// 				 */
	// 				$this->verifyPromoApplication(1000, 8, 19);
	// 				break;

// 			case 4:
	// 				/* Promo Type: deposit promo
	// 				Deposit Condition: Non fixed deposit amount >= 1000
	// 				Deposit Succession: 4th Deposit
	// 				Bonus Release: Fixed Bonus Amount = 300
	// 				Withdraw Requirement: Bet Amount >= 1000
	// 				Allowed Game Type: (AG,PT)
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Star VIP Group Level Name 1
	// 				Star VIP Group Level Name 2
	// 				Star VIP Group Level Name 3

// 				Note: player must have total 3 deposit cnt to avail this promo
	// 				Expected Result: Bonus Amount = 300
	// 				 */
	// 				$this->verifyPromoApplication(1000, 9, 19);
	// 				break;

// 			case 5:
	// 				/* Promo Type: deposit promo
	// 				Deposit Condition: Non fixed deposit any amount
	// 				Deposit Succession: 2nd Deposit
	// 				Bonus Release: Fixed Bonus Amount = 100
	// 				Withdraw Requirement: Bet Amount >= 5000
	// 				Allowed Game Type: (AG,PT)
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Slot Machine Group Level Name 1
	// 				Slot Machine Group Level Name 3
	// 				Test4og192 Level Name 1
	// 				Apple Level Name 1
	// 				Apple Level Name 2

// 				Note: player must have total 1 deposit cnt to avail this promo
	// 				Expected Result: Bonus Amount = 100
	// 				 */
	// 				$this->verifyPromoApplication(500, 10, 19);
	// 				break;

// 			case 6:
	// 				/* Promo Type: deposit promo
	// 				Deposit Condition: Non fixed deposit any amount
	// 				Deposit Succession: 3rd Deposit
	// 				Bonus Release: bonus is by percentage = 10% up to max bonus amount = 500
	// 				Withdraw Requirement: Bet Amount >= 4000
	// 				Allowed Game Type: (AG,PT)
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Star VIP Group Level Name 1
	// 				Star VIP Group Level Name 2
	// 				Star VIP Group Level Name 3
	// 				VIP Group Level Name 1
	// 				VIP Group Level Name 2
	// 				VIP Group Level Name 3
	// 				Slot Machine Group Level Name 1
	// 				Slot Machine Group Level Name 2
	// 				Slot Machine Group Level Name 3
	// 				Slot Machine Group Level Name 4
	// 				Slot Machine Group Level Name 5
	// 				Test4og192 Level Name 1
	// 				Apple Level Name 1
	// 				Apple Level Name 2

// 				Note: player must have total 2 deposit cnt to avail this promo
	// 				Expected Result: Bonus Amount = 10% of deposit up to max bonus amount = 500
	// 				 */
	// 				$this->verifyPromoApplication(100000, 11, 19);
	// 				break;

// 			case 7:
	// 				/* Promo Type: deposit promo
	// 				Deposit Condition: Non fixed deposit any amount
	// 				Deposit Succession: 3rd Deposit
	// 				Bonus Release: bonus is by percentage = 10% up to max bonus amount = 500
	// 				Withdraw Requirement: Bet Amount = (Deposit + Bonus) x 5
	// 				Allowed Game Type: (AG,PT)
	// 				Allowed Player Level:
	// 				Slot Machine Group Level Name 1
	// 				Slot Machine Group Level Name 2
	// 				Slot Machine Group Level Name 3
	// 				Slot Machine Group Level Name 4
	// 				Slot Machine Group Level Name 5

// 				Note: player must have total 2 deposit cnt to avail this promo
	// 				Expected Result: Bonus Amount = 10% of deposit up to max bonus amount = 500
	// 				 */
	// 				$this->verifyPromoApplication(1000000, 12, 19);
	// 				break;

// 			case 8:
	// 				/* Promo Type: deposit promo
	// 				Deposit Condition: Non fixed deposit amount >= 5000
	// 				By Application: Repeat, with repeat condition of Bet Amount = (Deposit + Bonus) x 2 betting times.
	// 				No limitation on application
	// 				Bonus Release: bonus is by percentage = 5% of deposit amount, up to 200 Max Bonus Amount.
	// 				Withdraw Requirement: Bet Amount >= 5000
	// 				Allowed Game Type: (AG,PT)
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Star VIP Group Level Name 1
	// 				Star VIP Group Level Name 2

// 				Note: theres no required deposit count
	// 				Expected Result: Bonus Amount = 5% of deposit amount, up to 200 Max Bonus Amount.
	// 				 */
	// 				$this->verifyPromoApplication(10000, 13, 19);
	// 				break;

// 			case 9:
	// 				/* Promo Type: deposit promo
	// 				Deposit Condition: Non fixed deposit any amount
	// 				By Application: Repeat, with repeat condition of Bet Amount = (Deposit + Bonus) x 2 betting times.
	// 				With limit 3 time on application
	// 				Bonus Release: bonus is by percentage = 60% of deposit amount, up to 1000 Max Bonus Amount.
	// 				Withdraw Requirement: Bet Amount = (Deposit + Bonus) x 9
	// 				Allowed Game Type: (AG,PT)
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Star VIP Group Level Name 1
	// 				Star VIP Group Level Name 2

// 				Note: theres no required deposit count
	// 				Expected Result: Bonus Amount = 60% of deposit amount, up to 1000 Max Bonus Amount.
	// 				 */
	// 				$this->verifyPromoApplication(10000, 12, 19);
	// 				break;

// 			case 10:
	// 				/* Promo Type: deposit promo
	// 				Deposit Condition: Non fixed deposit any amount
	// 				By Application: No repeat
	// 				Bonus Release: Fixed Bonus Amount = 250
	// 				Withdraw Requirement: No Bet Requirement
	// 				Allowed Game Type: (AG,PT)
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Star VIP Group Level Name 1
	// 				Star VIP Group Level Name 2

// 				Note: theres no required deposit count
	// 				Expected Result: Bonus Amount = 250
	// 				 */
	// 				$this->verifyPromoApplication(10000, 13, 19);
	// 				break;

// 			case 11:
	// 				/* Promo Type: non deposit promo (email confirmation)
	// 				By Application: No Repeat

// 				Bonus Release: Fixed Bonus Amount = 100
	// 				Withdraw Requirement: With bet condition of Bonus x 5
	// 				Allowed Game Type: (AG,PT)
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Star VIP Group Levfel Name 1
	// 				Star VIP Group Level Name 2

// 				Note: theres no required deposit count
	// 				Expected Result: Bonus Amount = 100
	// 				 */
	// 				$this->verifyNonDepositPromoApplication(28, 26);
	// 				break;

// 			case 12:
	// 				/* Promo Type: non deposit promo (email confirmation)
	// 				By Application: No Repeat

// 				Bonus Release: Fixed Bonus Amount = 200
	// 				Withdraw Requirement: With bet condition of Bet amount >= 1000
	// 				Allowed Game Type: (AG,PT)
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Star VIP Group Level Name 1
	// 				Star VIP Group Level Name 2

// 				Note: theres no required deposit count
	// 				Expected Result: Bonus Amount = 200
	// 				 */
	// 				$this->verifyNonDepositPromoApplication(28, 19);
	// 				break;

// 			case 13:
	// 				/* Promo Type: non deposit promo (registration promo)
	// 				By Application: No Repeat

// 				Bonus Release: Fixed Bonus Amount = 300
	// 				Withdraw Requirement: With bet condition of Bet amount >= 5000
	// 				Allowed Game Type: (AG,PT)
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Star VIP Group Level Name 1
	// 				Star VIP Group Level Name 2

// 				Note: theres no required deposit count
	// 				Expected Result: Bonus Amount = 300
	// 				 */
	// 				$this->verifyNonDepositPromoApplication(30, 19);
	// 				break;

// 			case 14:
	// 				/* Promo Type: non deposit promo (complete registration promo)
	// 				By Application: No Repeat

// 				Bonus Release: Fixed Bonus Amount = 150
	// 				Withdraw Requirement: With bet condition of Bonus x 5
	// 				Allowed Game Type: (AG)
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Star VIP Group Level Name 1
	// 				Star VIP Group Level Name 2

// 				Note: theres no required deposit count
	// 				Expected Result: Bonus Amount = 150
	// 				 */
	// 				$this->verifyNonDepositPromoApplication(31, 19);
	// 				break;

// 			case 15:
	// 				/* Promo Type: non deposit promo (by betting)
	// 				By Application: Repeat, No Limit

// 				Bonus Release: Fixed Bonus Amount = 30
	// 				Withdraw Requirement: With bet condition of Bonus x 5
	// 				Allowed Game Type: (ALL AG), Bet Requirement: 300
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Star VIP Group Level Name 1
	// 				Star VIP Group Level Name 2
	// 				VIP Group Level Name 3
	// 				Slot Machine Group Level Name 4

// 				Note: theres no required deposit count
	// 				Expected Result: Bonus Amount = 30
	// 				 */
	// 				$this->verifyNonDepositPromoApplication(32, 19);
	// 				break;

// 			case 16:
	// 				/* Promo Type: non deposit promo (by lossing)
	// 				By Application: Repeat, Limit 5 times
	// 				Repeat Condition, Bet Amount = Bonus x 5 Betting times

// 				Bonus Release: Fixed Bonus Amount = 200
	// 				Withdraw Requirement: With bet condition of Bet amount = Bonus x 5
	// 				Allowed Game Type: (ALL AG), Bet Requirement: 300
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Star VIP Group Level Name 1
	// 				Star VIP Group Level Name 2
	// 				VIP Group Level Name 3
	// 				Slot Machine Group Level Name 4

// 				Note: theres no required deposit count
	// 				Expected Result: Bonus Amount = 30
	// 				 */
	// 				$this->verifyNonDepositPromoApplication(33, 20);
	// 				break;

// 			case 17:
	// 				/* Promo Type: non deposit promo (by winning)
	// 				By Application: Repeat, Limit 5 times
	// 				Repeat Condition, Bet Amount = Bonus x 2 betting times

// 				Bonus Release: Fixed Bonus Amount = 345
	// 				Withdraw Requirement: With bet condition of Bet amount = Bonus x 5
	// 				Allowed Game Type: (ALL AG), Bet Requirement: 300
	// 				Allowed Player Level:
	// 				Default Player Group Level Name 1
	// 				Star VIP Group Level Name 1
	// 				Star VIP Group Level Name 2
	// 				VIP Group Level Name 3
	// 				Slot Machine Group Level Name 4

// 				Note: theres no required deposit count
	// 				Expected Result: Bonus Amount = 345
	// 				 */
	// 				$this->verifyNonDepositPromoApplication(34, 20);
	// 				break;

// 			default:
	// 				# code...
	// 				break;
	// 		}
	// 	}

// 	/**
	// 	 * apply deposit promo
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function verifyPromoViaAjax($depositAmount, $depositPromoId) {
	// 		//if($depositAmount!='' && $depositPromoId!=''){
	// 		$result = $this->verifyPromoApplication($depositAmount, $depositPromoId);
	// 		if ($result) {
	// 			//if promo application is success this will have data otherwise this is null
	// 			$playerPromoId = $this->session->userdata('playerPromoId');

// 			$data['playerPromoId'] = $playerPromoId;
	// 			$depositWalletAccountId = $this->session->userdata('depositWalletAccountId');
	// 			$this->player_functions->updateThirdpartyDeposit($data, $depositWalletAccountId);
	// 		}
	// 		$this->session->unset_userdata('applicationPromoId');

// 		//var_dump($depositPromoId);exit();
	// 		//$message = $this->session->userdata('promoMessage') . " Your deposit request has been sent!";
	// 		$message = $this->session->set_userdata('promoMessage', lang('notify.38'));
	// 		$this->alertMessage(1, $message);
	// 		return redirect(BASEURL . 'smartcashier/viewCashier', 'refresh');
	// 		//}

// 	}

// 	/**
	// 	 * test ajax
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function testAjax() {
	// 		$this->loadTemplate('Local Bank Deposit', '', '', 'wallet');
	// 		$this->template->write_view('main_content', 'cashier/test');
	// 		$this->template->render();
	// 	}

// 	/**
	// 	 * post make deposit
	// 	 *
	// 	 * @param   player_id int
	// 	 * @return  rendered template
	// 	 */
	// 	public function manualDeposit3rdParty() {
	// 		//$this->form_validation->set_rules('date','DOB','callback_checkDateFormat');

// 		$depositTo = $this->input->post('depositTo');
	// 		$depositAmount = $this->input->post('depositAmount');
	// 		$depositTransactionRef = $this->input->post('depositTransactionRef');
	// 		$depositDate = $this->input->post('depositDate');
	// 		$depositTime = $this->input->post('depositTime');
	// 		$depositDateTime = $depositDate . ' ' . $depositTime;
	// 		$depositName = $this->input->post('depositName');
	// 		$depositAccount = $this->input->post('depositAccount');
	// 		$rememberMyAccount = $this->input->post('rememberMyAccount_cb');
	// 		$playerId = $this->input->post('playerId');
	// 		$dwIp = $this->input->post('dw_ip');
	// 		$geolocation = $this->utils->getGeoplugin($dwIp);
	// 		$depositPromoId = $this->input->post('depositPromoId');
	// 		$promoCmsSettingId = $this->input->post('promoCmsSettingId');
	// 		$thirdpartypaymentmethodaccountId = $this->input->post('thirdpartypaymentmethodaccountId');

// 		$this->session->set_userdata('depositTo', $depositTo);
	// 		$this->session->set_userdata('depositAmount', $depositAmount);
	// 		$this->session->set_userdata('depositTransactionRef', $depositTransactionRef);
	// 		$this->session->set_userdata('depositDate', $depositDate);
	// 		$this->session->set_userdata('depositTime', $depositTime);
	// 		$this->session->set_userdata('depositName', $depositName);
	// 		$this->session->set_userdata('depositAccount', $depositAccount);
	// 		$this->session->set_userdata('rememberMyAccount', $rememberMyAccount);
	// 		$today = date("Y-m-d H:i:s");
	// 		$player_account = $this->player_functions->getPlayerAccountByPlayerIdOnly($playerId);

// 		//check if amount is not 0
	// 		if ($depositAmount < 0) {
	// 			$message = "Deposit amount should be 0!";
	// 			$message = lang('notify.39');
	// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/makeDeposit');
	// 		}
	// 		$data['depositRule'] = $this->player_functions->getPlayerDepositRule($playerId);
	// 		$minDeposit = $data['depositRule'][0]['minDeposit'];
	// 		$maxDeposit = $data['depositRule'][0]['maxDeposit'];
	// 		//var_dump($data);exit();

// 		//check player's deposit rule
	// 		if ($depositAmount < $minDeposit) {
	// 			//$message = "Minimum Deposit ".$minDeposit.'!';
	// 			$message = lang('notify.40') . ' ' . $minDeposit . '!';

// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/makeDeposit/1');
	// 		} else {
	// 			if ($maxDeposit > 0) {
	// 				//get players deposit total deposit
	// 				$playerTotalDailyDeposit = $this->player_functions->getPlayerTotalDailyDeposit($playerId);
	// 				//var_dump($playerTotalDailyDeposit['totalDailyDeposit']);exit();
	// 				if ($playerTotalDailyDeposit['totalDailyDeposit'] != null) {
	// 					if ($playerTotalDailyDeposit['totalDailyDeposit'] >= $maxDeposit) {
	// 						$message = lang('notify.74') . ' ' . $maxDeposit . '!';
	// 						$this->alertMessage(2, $message);
	// 						redirect(BASEURL . 'smartcashier/makeDeposit/1');
	// 					}
	// 				}
	// 			}
	// 			$depositSlipURL = $this->input->post('banner_url');
	// 			$fileType = substr($depositSlipURL, strrpos($depositSlipURL, '.') + 1);
	// 			$path = realpath(APPPATH . '../public/resources/images/depositslip');

// 			//upload image
	// 			$path_image = $_FILES['userfile']['name'];
	// 			$ext = pathinfo($path_image, PATHINFO_EXTENSION);

// 			if (strcasecmp($ext, 'jpg') != 0 && strcasecmp($ext, 'jpeg') != 0 && strcasecmp($ext, 'gif') != 0 && strcasecmp($ext, 'png') != 0) {
	// 				$message = lang('cashier.113');
	// 				$this->alertMessage(2, $message);
	// 				redirect(BASEURL . 'smartcashier/makeDeposit/1');
	// 			} else if ($depositSlipURL != '') {
	// 				$depositSlipName = 'manual3rdparty-deposit-slip-' . $this->player_functions->generateRandomCode();
	// 				$config = array(
	// 					'allowed_types' => 'jpg|jpeg|gif|png',
	// 					'upload_path' => $path,
	// 					'max_size' => $this->utils->getMaxUploadSizeByte(),
	// 					'overwrite' => true,
	// 					'file_name' => $depositSlipName,
	// 				);

// 				$this->load->library('upload', $config);
	// 				$this->upload->do_upload();
	// 			}

// 			if ($depositPromoId != '') {
	// 				$this->verifyPromoApplication($depositAmount, $depositPromoId, $promoCmsSettingId);
	// 				$this->session->unset_userdata('applicationPromoId');
	// 			}

// 			//if promo application is success this will have data otherwise this is null
	// 			$playerPromoId = $this->session->userdata('playerPromoId');

// 			$walletAccountData = array(
	// 				'playerAccountId' => $player_account['playerAccountId'],
	// 				'walletType' => 'Main',
	// 				'playerPromoId' => $playerPromoId,
	// 				'amount' => $depositAmount,
	// 				'dwMethod' => 4,
	// 				'dwStatus' => 'request',
	// 				'dwDateTime' => $today,
	// 				'transactionType' => 'deposit',
	// 				'thirdpartyPaymentMethod' => 'manual',
	// 				'dwIp' => $dwIp,
	// 				'dwLocation' => $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
	// 				'transactionCode' => $this->player_functions->getRandomTransactionCode(),
	// 				'status' => '0',
	// 			);

// 			$rememberMyAccount == TRUE ? $rememberMyAccount = '1' : $rememberMyAccount = '0';

// 			$thirdpartyAccountData = $this->player_functions->getThirdpartyTransactionFee($thirdpartypaymentmethodaccountId);
	// 			$manual3rdPartyDepositDetails = array(
	// 				'transactionFee' => $thirdpartyAccountData['transactionFee'],
	// 				'depositTo' => $depositTo,
	// 				'depositSlipName' => $depositSlipName . '.' . $fileType,
	// 				'transacRefCode' => $depositTransactionRef,
	// 				'depositDateTime' => $depositDateTime,
	// 				'depositorName' => $depositName,
	// 				'depositorAccount' => $depositAccount,
	// 				'isRemember' => $rememberMyAccount,
	// 				'thirdpartypaymentmethodaccountId' => $thirdpartypaymentmethodaccountId,
	// 				'status' => '0',
	// 			);

// 			//$this->player_functions->createAccountDetails($data);
	// 			$this->player_functions->manual3rdPartyDeposit($walletAccountData, $manual3rdPartyDepositDetails);

// 			//delete
	// 			$this->session->unset_userdata('playerPromoId');

// 			// save http_request (cookies, referer, user-agent)
	// 			$this->saveHttpRequest($this->authentication->getPlayerId(), 3);

// 			//var_dump($depositPromoId);exit();
	// 			//$message = $this->session->userdata('promoMessage') . " Your deposit request has been sent!";
	// 			$message = $this->session->userdata('promoMessage') . ' ' . lang('notify.38');
	// 			$this->alertMessage(1, $message);
	// 			redirect(BASEURL . 'smartcashier/viewCashier');
	// 		}
	// 		//}
	// 	}

// 	/**
	// 	 * localBankDepositChecker
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function localBankDepositChecker() {
	// 		if ($this->authentication->isLoggedIn()) {
	// 			$this->form_validation->set_rules('depositAmount', 'Deposit Amount', 'trim|required|xss_clean|numeric');

// 			$this->session->set_userdata('lbc_depositAmount', $this->input->post('depositAmount'));
	// 			$depositAmount = $this->input->post('depositAmount');
	// 			$depositPromoId = $this->input->post('depositPromoId');
	// 			$promoCmsSettingId = $this->input->post('promoCmsSettingId');

// 			//get player id
	// 			$playerId = $this->authentication->getPlayerId();

// 			//get player's deposit rule
	// 			$data['depositRule'] = $this->player_functions->getPlayerDepositRule($playerId);
	// 			$minDeposit = $data['depositRule'][0]['minDeposit'];
	// 			$maxDeposit = $data['depositRule'][0]['maxDeposit'];
	// 			//var_dump($data);exit();

// 			//check player's deposit rule
	// 			if ($depositAmount < $minDeposit) {
	// 				//$message = "Deposit amount should be minimum of RMB ".$minDeposit.'!';
	// 				$message = lang('notify.40') . ' ' . $minDeposit . '!';
	// 				$this->alertMessage(2, $message);
	// 				redirect(BASEURL . 'smartcashier/makeDeposit/3');
	// 			} else {
	// 				//check if deposit reached max limit
	// 				if ($maxDeposit > 0) {
	// 					//get players deposit total deposit
	// 					$playerTotalDailyDeposit = $this->player_functions->getPlayerTotalDailyDeposit($playerId);
	// 					//var_dump($playerTotalDailyDeposit['totalDailyDeposit']);exit();
	// 					if ($playerTotalDailyDeposit['totalDailyDeposit'] != null) {
	// 						if ($playerTotalDailyDeposit['totalDailyDeposit'] >= $maxDeposit) {
	// 							$message = lang('notify.74') . ' ' . $maxDeposit . '! ^^,';
	// 							$this->alertMessage(2, $message);
	// 							redirect(BASEURL . 'smartcashier/makeDeposit/1');
	// 						}
	// 					}
	// 				}

// 				$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 				$data['footerData'] = $this->cms_function->getCmsFooterContentData();

// 				//get player available bank account from bank account manager list according to its level
	// 				$playerdata['bankAccountDetails'] = $this->player_functions->getPlayerAvailableBankAccountDeposit($playerId);
	// 				//var_dump($playerdata['bankAccountDetails']);exit();
	// 				//get bank details
	// 				$playerdata['playerBankDetails'] = $this->player_functions->getDepositBankDetails($playerId);
	// 				//var_dump($playerdata['playerBankDetails']);exit();
	// 				//get all bank types
	// 				$playerdata['banks'] = $this->player_functions->getAllBankType();

// 				//var_dump($playerdata['playerBankDetails']);exit();
	// 				$playerdata['depositAmount'] = $depositAmount;
	// 				$playerdata['depositPromoId'] = $depositPromoId;
	// 				$playerdata['promoCmsSettingId'] = $promoCmsSettingId;

// 				$this->loadTemplate('Local Bank Deposit', '', '', 'wallet');
	// 				$this->template->write_view('main_content', 'cashier/localbank_deposit', $playerdata);
	// 				$this->template->render();
	// 			}
	// 		} else {
	// 			$data['playerGamePasswordPT'] = $this->player_functions->getPlayerPassword($this->authentication->getPlayerId(), 1);
	// 			$data['news'] = $this->player_functions->getAllNews();

// 			$bannerType = 1; //home banner big
	// 			$data['homemainbanner'] = $this->cms_function->getCmsBanner($bannerType);

// 			$bannerType = 3; //home banner small
	// 			$data['homesmallbanner'] = $this->cms_function->getCmsBanner($bannerType);

// 			$bannerType = 4; //home banner game
	// 			$data['homegamebanner'] = $this->cms_function->getCmsBanner($bannerType);

// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			//var_dump($data['footerData']); exit();
	// 			$this->loadTemplate('Welcome To OG Website', '', '', 'home');

// 			$this->template->write_view('main_content', 'online/view_welcome', $data);
	// 			$this->template->write_view('footer_content', 'template/footer_template');

// 			$this->template->render();
	// 		}
	// 	}

// 	function testID($playerId) {
	// 		$player_account = $this->player_functions->getPlayerAccountByPlayerIdOnly($playerId);
	// 		var_dump($player_account);exit();
	// 	}

// 	/**
	// 	 * localBankDeposit
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function localBankDeposit() {
	// 		$this->form_validation->set_rules('depositAmount', 'Deposit Amount', 'trim|required|xss_clean|is_numeric');
	// 		$this->form_validation->set_rules('depositTransactionRef', 'Deposit Transaction Reference', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('lastFiveDigitNo', 'Last 5 Digit No.', 'trim|required|xss_clean|is_numeric');
	// 		$this->form_validation->set_rules('bankType', 'Bank Type', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('pa_bankName', 'Preferred Account', 'trim|xss_clean');
	// 		$this->form_validation->set_rules('fullName', 'Full Name', 'trim|xss_clean');
	// 		$this->form_validation->set_rules('na_bankName', 'Bank Name', 'trim|xss_clean');
	// 		$this->form_validation->set_rules('depositAccountNo', 'Deposit Account', 'trim|xss_clean|is_numeric');

// 		$bankAccountId = $this->input->post('bankAccountId');
	// 		$depositAmount = $this->input->post('depositAmount');
	// 		$depositTransactionRef = $this->input->post('depositTransactionRef');
	// 		$lastFiveDigitNo = $this->input->post('lastFiveDigitNo');
	// 		$bankType = $this->input->post('bankType');
	// 		$pa_bankName = $this->input->post('pa_bankName');
	// 		$fullName = $this->input->post('fullName');
	// 		$na_bankName = $this->input->post('na_bankName');
	// 		$depositAccountNo = $this->input->post('depositAccountNo');
	// 		$dwIp = $this->input->post('dw_ip');
	// 		$geolocation = $this->utils->getGeoplugin($dwIp);
	// 		$rememberMyAccount = $this->input->post('rememberMyAccount_cb');
	// 		$itemAccount = $this->input->post('itemAccount');
	// 		$depositPromoId = $this->input->post('depositPromoId');
	// 		$promoCmsSettingId = $this->input->post('promoCmsSettingId');

// 		$this->session->set_userdata('lb_depositAmount', $depositAmount);
	// 		$this->session->set_userdata('lb_depositTransactionRef', $depositTransactionRef);
	// 		$this->session->set_userdata('lb_lastFiveDigitNo', $lastFiveDigitNo);
	// 		$this->session->set_userdata('lb_bankType', $bankType);
	// 		$this->session->set_userdata('lb_pa_bankName', $pa_bankName);
	// 		$this->session->set_userdata('lb_fullName', $fullName);
	// 		$this->session->set_userdata('lb_bankName', $na_bankName);
	// 		$this->session->set_userdata('lb_depositAccountNo', $depositAccountNo);
	// 		$this->session->set_userdata('lb_rememberMyAccount', $rememberMyAccount);
	// 		$today = date("Y-m-d H:i:s");

// 		//get player data
	// 		$playerId = $this->authentication->getPlayerId();
	// 		$player_account = $this->player_functions->getPlayerAccountByPlayerIdOnly($playerId);

// 		if ($this->form_validation->run() == false) {
	// 			//$message = "Please make sure the information you provided is complete!";
	// 			$message = lang('notify.41');
	// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/makeDeposit');
	// 		} else {

// 			//check if new account or old account
	// 			if ($itemAccount == 'new') {
	// 				//check if remember this account
	// 				$rememberMyAccount == TRUE ? $rememberMyAccount = '1' : $rememberMyAccount = '0';

// 				//add bank account details and get id
	// 				$data = array(
	// 					'playerId' => $playerId,
	// 					'bankTypeId' => $na_bankName,
	// 					'bankAccountNumber' => $depositAccountNo,
	// 					'bankAccountFullName' => $fullName,
	// 					'dwBank' => '0', //0 is deposit
	// 					'isRemember' => $rememberMyAccount, //1 is default
	// 					'status' => '0', //0 is active
	// 				);
	// 				$playerBankDetailsId = $this->player_functions->addBankDetailsByDeposit($data);
	// 			} else {
	// 				$playerBankDetailsId = $pa_bankName; //preferred player bank account id
	// 			}

// 			$depositSlipURL = $this->input->post('banner_url');
	// 			$fileType = substr($depositSlipURL, strrpos($depositSlipURL, '.') + 1);
	// 			$path = realpath(APPPATH . '../public/resources/images/depositslip');

// 			//upload image
	// 			$path_image = $_FILES['userfile']['name'];
	// 			$ext = pathinfo($path_image, PATHINFO_EXTENSION);

// 			if (strcasecmp($ext, 'jpg') != 0 && strcasecmp($ext, 'jpeg') != 0 && strcasecmp($ext, 'gif') != 0 && strcasecmp($ext, 'png') != 0) {
	// 				$message = lang('cashier.113');
	// 				$this->alertMessage(2, $message);
	// 				redirect(BASEURL . 'smartcashier/makeDeposit/3');
	// 			} else {
	// 				$depositSlipName = 'localbank-deposit-slip-' . $this->player_functions->generateRandomCode();
	// 				$config = array(
	// 					'allowed_types' => 'jpg|jpeg|gif|png',
	// 					'upload_path' => $path,
	// 					'max_size' => $this->utils->getMaxUploadSizeByte(),
	// 					'overwrite' => true,
	// 					'file_name' => $depositSlipName,
	// 				);

// 				$this->load->library('upload', $config);
	// 				$this->upload->do_upload();
	// 			}

// 			//promotion
	// 			//$depositPromoId = $this->session->userdata('applicationPromoId');

// 			if ($depositPromoId != '') {
	// 				$this->verifyPromoApplication($depositAmount, $depositPromoId, $promoCmsSettingId);
	// 				$this->session->unset_userdata('applicationPromoId');
	// 			} else {
	// 				$this->session->unset_userdata('playerPromoId');
	// 			}

// 			$playerPromoId = $this->session->userdata('playerPromoId');

// 			//wallet
	// 			$walletAccountData = array(
	// 				'playerAccountId' => $player_account['playerAccountId'],
	// 				'walletType' => 'Main',
	// 				'playerPromoId' => $playerPromoId,
	// 				'amount' => $depositAmount,
	// 				'dwMethod' => 1,
	// 				'dwStatus' => 'request',
	// 				'dwDateTime' => $today,
	// 				'transactionType' => 'deposit',
	// 				'localBankType' => $bankType,
	// 				'dwIp' => $dwIp,
	// 				'dwLocation' => $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
	// 				'transactionCode' => $this->player_functions->getRandomTransactionCode(),
	// 				'status' => '0',
	// 			);
	// 			//$bankAccountData = $this->player_functions->getLocalBankTransactionFee($bankAccountId);
	// 			//$bankAccountData = $this->player_functions->getTrasancationFeeSetting();
	// 			$this->session->unset_userdata('playerPromoId');

// 			$localBankDepositDetails = array(
	// 				'bankAccountId' => $bankAccountId,
	// 				//'transactionFee' => $bankAccountData['amount'],
	// 				'playerBankDetailsId' => $playerBankDetailsId, //link to available bank account for player's level
	// 				'depositSlipName' => $depositSlipName . '.' . $fileType,
	// 				'transacRefCode' => $depositTransactionRef,
	// 				'depositDateTime' => $today,
	// 				'status' => 'active',
	// 			);
	// 			//$this->player_functions->createAccountDetails($data);
	// 			$this->player_functions->localBankDeposit($walletAccountData, $localBankDepositDetails);

// 			// save http_request (cookies, referer, user-agent)
	// 			$this->saveHttpRequest($this->authentication->getPlayerId(), 3);

// 			$message = $this->session->userdata('promoMessage') . " Your deposit request has been sent!";
	// 			$this->alertMessage(1, $message);
	// 			redirect(BASEURL . 'smartcashier/viewCashier');
	// 		}
	// 	}

// 	// Check date format, if input date is valid return TRUE else returned FALSE.
	// 	function checkDateFormat($date) {
	// 		if (preg_match("/[0-31]{2}\/[0-12]{2}\/[0-9]{4}/", $date)) {
	// 			if (checkdate(substr($date, 3, 2), substr($date, 0, 2), substr($date, 6, 4))) {
	// 				return true;
	// 			} else {
	// 				return false;
	// 			}

// 		} else {
	// 			return false;
	// 		}
	// 	}

// 	public function setPlayerBankaccountUndefault() {
	// 		$playerId = $this->authentication->getPlayerId();
	// 		$data = array(
	// 			'isDefault' => '0');
	// 		$playerBankDetails = $this->player_functions->getDepositBankDetails($playerId);
	// 		//var_dump($playerBankDetails);exit();
	// 		foreach ($playerBankDetails as $row) {
	// 			//echo 'row: '.$row['playerBankDetailsId'];
	// 			$this->player_functions->setPlayerBankaccountUndefault($row['playerBankDetailsId'], $data);
	// 		}
	// 	}

// 	public function autoDeposit3rdParty() {
	// 		$this->form_validation->set_rules('deposit_from', 'Payment Method', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('deposit_amount', 'Deposit Amount', 'trim|required|xss_clean|numeric');

// 		$this->session->set_userdata('deposit_from', $this->input->post('deposit_from'));
	// 		$this->session->set_userdata('deposit_amount', $this->input->post('deposit_amount'));

// 		$player_id = $this->authentication->getPlayerId();
	// 		$player = $this->player_functions->getPlayerById($player_id);
	// 		//$getRankingLevelSettingByPlayerLevel = $this->player_functions->getRankingLevelSettingByPlayerLevel($player['playerLevel']);
	// 		//get player's deposit rule
	// 		$data['depositRule'] = $this->player_functions->getPlayerDepositRule($player_id);
	// 		$minDeposit = $data['depositRule'][0]['minDeposit'];
	// 		$maxDeposit = $data['depositRule'][0]['maxDeposit'];

// 		$dwIp = $this->input->post('dw_ip');
	// 		$geolocation = $this->utils->getGeoplugin($dwIp);

// 		if ($this->form_validation->run() == false) {
	// 			//$message = "Please make sure the information you provided is complete!";
	// 			//$this->form_validation->set_message('deposit_amount',' amount should be not less than RMB 100!');
	// 			$message = lang('notify.41');
	// 			$this->form_validation->set_message('deposit_amount', lang('notify.42'));

// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/makeDeposit/2');
	// 		} elseif ($minDeposit > $this->input->post('deposit_amount')) {
	// 			//$message = "You did not meet the minimum deposit amount requirement!";
	// 			$message = lang('notify.43');
	// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/makeDeposit/2');
	// 			//$this->deposit($player_id, $this->input->post('deposit_from'));
	// 		} else {
	// 			//check if deposit reached max limit
	// 			if ($maxDeposit > 0) {
	// 				//get players deposit total deposit
	// 				$playerTotalDailyDeposit = $this->player_functions->getPlayerTotalDailyDeposit($player_id);
	// 				//var_dump($playerTotalDailyDeposit['totalDailyDeposit']);exit();
	// 				if ($playerTotalDailyDeposit['totalDailyDeposit'] != null) {
	// 					if ($playerTotalDailyDeposit['totalDailyDeposit'] >= $maxDeposit) {
	// 						$message = lang('notify.74') . ' ' . $maxDeposit . '!';
	// 						$this->alertMessage(2, $message);
	// 						redirect(BASEURL . 'smartcashier/makeDeposit/1');
	// 					}
	// 				}
	// 			}
	// 			$player = $this->player_functions->getPlayerById($player_id);
	// 			$player_account = $this->player_functions->getPlayerAccountByPlayerId($player_id);
	// 			$deposit_from = $this->input->post('deposit_from'); //payment gateway
	// 			$deposit_to = 'Main wallet';
	// 			$deposit_amount = $this->input->post('deposit_amount') . '.00';
	// 			$today = date("Y-m-d H:i:s");
	// 			$this->session->set_userdata('transaction_id', $this->player_functions->getTransactionId());
	// 			$this->session->set_userdata('currency', $player_account['currency']);
	// 			$this->session->set_userdata('description', 'OG Product');
	// 			$payment_method = $deposit_from;
	// 			$processPromoStatus = NULL;
	// 			$player_promo_id = NULL;

// 			$player_account = $this->player_functions->getPlayerAccountByPlayerId($player_id);
	// 			$processPromo = "";

// 			$depositPromoId = $this->session->userdata('applicationPromoId');

// 			if ($depositPromoId) {
	// 				$this->verifyPromoApplication($deposit_amount, $depositPromoId);
	// 				$this->session->unset_userdata('applicationPromoId');
	// 			}

// 			//var_dump($deposit_from);exit();

// 			//for redirect
	// 			$data = array(
	// 				'playerAccountId' => $player_account['playerAccountId'],
	// 				'walletType' => 'Main',
	// 				'amount' => $deposit_amount,
	// 				'amount_int' => $this->input->post('deposit_amount'),
	// 				'processedBy' => '0',
	// 				'processDateTime' => '0000-00-00 00:00:00',
	// 				'notes' => NULL,
	// 				'dwMethod' => $payment_method,
	// 				'dwStatus' => 'request',
	// 				'dwDateTime' => $today,
	// 				'transactionType' => 'deposit',
	// 				'transactionCode' => $this->player_functions->getRandomTransactionCode(),
	// 				'waStatus' => '0',
	// 			);

// 			$this->session->set_userdata($data);

// 			if ($deposit_from == 2) {
	// 				$this->paypalExpress();
	// 			} elseif ($deposit_from == 4) {
	// 				$this->neteller($deposit_amount);
	// 			} elseif ($deposit_from == 3) {
	// 				$this->skrill();
	// 			}
	// 		}
	// 	}

// 	public function makeWithdrawal($player_id) {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			$data['player'] = $this->player_functions->getPlayerById($player_id);
	// 			$data['subwallet'] = $this->player_functions->getAllPlayerAccountByPlayerId($player_id);
	// 			$data['totalBalance'] = $this->player_functions->getPlayerTotalBalance($player_id)['totalBalance'];

// 			$this->loadTemplate('Make Withdrawal', '', '', 'wallet');
	// 			$this->template->write_view('main_content', 'cashier/make_withdrawal', $data);
	// 			$this->template->render();
	// 		}
	// 	}

// 	public function postMakeWithdrawal($player_id) {
	// 		$this->form_validation->set_rules('payment_method', 'Payment Method', 'trim|required|xss_clean');

// 		if ($this->form_validation->run() == false) {
	// 			$this->makeWithdrawal($player_id);
	// 		} else {
	// 			redirect(BASEURL . 'cashier/withdrawal/' . $player_id . '/' . $this->input->post('payment_method'));
	// 		}
	// 	}

// 	public function withdrawal($player_id, $payment_method) {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			$data['payment_method'] = $payment_method;
	// 			$data['player'] = $this->player_functions->getPlayerById($player_id);
	// 			$data['subwallet'] = $this->player_functions->getAllPlayerAccountByPlayerId($player_id);
	// 			$data['totalBalance'] = $this->player_functions->getPlayerTotalBalance($player_id)['totalBalance'];

// 			$this->loadTemplate('Withdrawal', '', '', 'wallet');
	// 			$this->template->write_view('main_content', 'cashier/withdrawal', $data);
	// 			$this->template->render();
	// 		}
	// 	}

// 	public function postWithdrawal($player_id) {
	// 		$this->form_validation->set_rules('withdrawal_from', 'Withdrawal From', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('withdrawal_to', 'Withdrawal To', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('withdrawal_amount', 'Withdrawal Amount', 'trim|required|xss_clean|numeric');
	// 		$this->form_validation->set_rules('withdrawal_acctname', 'Account Name', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('withdrawal_acctno', 'Account ID/No', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('withdrawal_acctemail', 'Email Account', 'trim|xss_clean|valid_email');
	// 		$player = $this->player_functions->getPlayerById($player_id);
	// 		$total_withdraw_amount = $this->player_functions->getTotalAmountWithdraw($player_id)['totalWithdrawAmount'] == NULL ? 0 : $this->player_functions->getTotalAmountWithdraw($player_id)['totalWithdrawAmount'];
	// 		$daily_max_withdrawal = $this->player_functions->getDailyWithdrawal($player_id, date('d'));
	// 		$getRankingLevelSettingByPlayerLevel = $this->player_functions->getRankingLevelSettingByPlayerLevel($player['playerLevel']);

// 		if ($this->form_validation->run() == false) {
	// 			$this->withdrawal($player_id, $this->input->post('withdrawal_from'));
	// 		} elseif ($this->input->post('withdrawal_amount') > $player['totalBalanceAmount']) {
	// 			$message = "Withdrawal amount should not be greater than your current total balance. Please try again!";
	// 			$this->alertMessage(2, $message);
	// 			$this->withdrawal($player_id, $this->input->post('withdrawal_from'));
	// 		} elseif (($total_withdraw_amount + $this->input->post('withdrawal_amount')) > $player['totalBalanceAmount']) {
	// 			$message = "Your amount requests should not greater than your total amount. Check/Cancel your previous withdrawal request and try again!";
	// 			$this->alertMessage(2, $message);
	// 			$this->withdrawal($player_id, $this->input->post('withdrawal_from'));
	// 		} else {
	// 			//$dwIp = '121.54.32.170';
	// 			$dwIp = $this->input->post('dw_ip');
	// 			$geolocation = $this->utils->getGeoplugin($dwIp);

// 			$withdrawal_from = $this->input->post('withdrawal_from');
	// 			$withdrawal_amount = $this->input->post('withdrawal_amount');
	// 			$withdrawal_to = $this->input->post('withdrawal_to');
	// 			$withdrawal_acctname = $this->input->post('withdrawal_acctname');
	// 			$withdrawal_acctno = $this->input->post('withdrawal_acctno');
	// 			$withdrawal_acctemail = $this->input->post('withdrawal_acctemail');
	// 			$today = date("Y-m-d H:i:s");
	// 			$payment_method = 0;
	// 			$bonus_message = "";
	// 			$total_amount = "";

// 			$player_account = $this->player_functions->getPlayerAccountByPlayerId($player_id);

// 			if ($getRankingLevelSettingByPlayerLevel['dailyMaxWithdrawal'] >= $daily_max_withdrawal && $getRankingLevelSettingByPlayerLevel['dailyMaxWithdrawal'] >= ($daily_max_withdrawal + $withdrawal_amount)) {
	// 				if ($withdrawal_amount > 0) {
	// 					if ($withdrawal_from == "Paypal") {
	// 						$payment_method = "2";
	// 					} elseif ($withdrawal_from == "BancNet") {
	// 						$payment_method = "3";
	// 					} elseif ($withdrawal_from == "Skrill") {
	// 						$payment_method = "4";
	// 					} elseif ($withdrawal_from == "Stripe") {
	// 						$payment_method = "5";
	// 					} else {
	// 						$payment_method = "1";
	// 					}

// 					$data = array(
	// 						'playerAccountId' => $player_account['playerAccountId'],
	// 						'playerPromoId' => NULL,
	// 						'walletType' => 'Main',
	// 						'amount' => $withdrawal_amount,
	// 						'processedBy' => '0',
	// 						'processDateTime' => '0000-00-00 00:00:00',
	// 						'notes' => NULL,
	// 						'dwMethod' => $payment_method,
	// 						'dwStatus' => 'request',
	// 						'dwDateTime' => $today,
	// 						'transactionType' => 'withdrawal',
	// 						'dwIp' => $dwIp,
	// 						'dwLocation' => $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
	// 						'transactionCode' => $this->player_functions->getRandomTransactionCode(),
	// 						'status' => '0',
	// 					);

// 					$this->player_functions->createAccountDetails($data);

// 					$wallet_account = $this->player_functions->getWalletAccountByPlayerId($player_id);

// 					$withdrawalDetails = array(
	// 						'walletAccountId' => $wallet_account['walletAccountId'],
	// 						'accountName' => $withdrawal_acctname,
	// 						'accountId' => $withdrawal_acctno,
	// 						'email' => $withdrawal_acctemail,
	// 						'status' => '0',
	// 					);

// 					$this->player_functions->addWithdrawalDetails($withdrawalDetails);

// 					//$message = "Withdrawal request Has been sent!" . $bonus_message;
	// 					$message = lang('notify.44') . ' ' . $bonus_message;
	// 					$this->alertMessage(1, $message);
	// 					redirect(BASEURL . 'cashier/transactionHistory/' . $player_id);

// 				} else {
	// 					$message = "Error! Your amount to be withdrawn should be greater than 0";
	// 					$this->alertMessage(2, $message);
	// 					redirect(BASEURL . 'cashier/transactionHistory/' . $player_id);
	// 				}
	// 			} else {
	// 				//$message = "You have reach your daily max withdrawal or Your withdrawal amount is greater than your daily max withdrawal!";
	// 				$message = lang('notify.45');
	// 				$this->alertMessage(2, $message);
	// 				redirect(BASEURL . 'cashier/transactionHistory/' . $player_id);
	// 			}
	// 		}
	// 	}

// 	public function cancelWithdrawal($player_id) {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			$data['player'] = $this->player_functions->getPlayerById($player_id);
	// 			$data['withdrawal_request'] = $this->player_functions->getRequestWithdrawal($player_id);
	// 			$data['subwallet'] = $this->player_functions->getAllPlayerAccountByPlayerId($player_id);
	// 			$data['totalBalance'] = $this->player_functions->getPlayerTotalBalance($player_id)['totalBalance'];

// 			//var_dump($data['withdrawal_request']);exit();
	// 			$this->loadTemplate('Cancel withdrawal', '', '', 'wallet');
	// 			$this->template->write_view('main_content', 'cashier/cancel_withdrawal', $data);
	// 			$this->template->render();
	// 		}
	// 	}

// 	public function postCancelWithdrawal($wallet_account_id, $player_id) {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			$today = date("Y-m-d H:i:s");
	// 			$data = array(
	// 				'dwStatus' => 'cancelled',
	// 				'dwDateTime' => $today,
	// 			);

// 			$this->player_functions->changeDWStatus($data, $wallet_account_id);

// 			$message = "The selected request withdrawal has been cancelled";
	// 			$this->alertMessage(1, $message);

// 			redirect(BASEURL . 'cashier/cancelWithdrawal/' . $player_id);
	// 		}
	// 	}

// 	public function transactionHistory($player_id) {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			$data['player'] = $this->player_functions->getPlayerById($player_id);
	// 			$data['transaction_history'] = $this->player_functions->getAllTransactionHistoryByPlayerId($player_id);
	// 			$data['subwallet'] = $this->player_functions->getAllPlayerAccountByPlayerId($player_id);
	// 			$data['totalBalance'] = $this->player_functions->getPlayerTotalBalance($player_id)['totalBalance'];

// 			$this->loadTemplate('Transaction History', '', '', 'wallet');
	// 			$this->template->write_view('main_content', 'cashier/transaction_history', $data);
	// 			$this->template->render();
	// 		}
	// 	}

// 	public function makeOtc($player_id) {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			$data['player'] = $this->player_functions->getPlayerById($player_id);
	// 			$data['subwallet'] = $this->player_functions->getAllPlayerAccountByPlayerId($player_id);
	// 			$data['otc'] = $this->player_functions->getAllOtc();
	// 			$data['totalBalance'] = $this->player_functions->getPlayerTotalBalance($player_id)['totalBalance'];

// 			$this->loadTemplate('Make Over The Counter', '', '', 'wallet');
	// 			$this->template->write_view('main_content', 'cashier/otc_payment_method', $data);
	// 			$this->template->render();
	// 		}
	// 	}

// 	public function paymentMethod($otc_payment_method_id = '') {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			$data['payment_method'] = $this->player_functions->getOtcById($otc_payment_method_id);

// 			$this->load->view('cashier/payment_method', $data);
	// 		}
	// 	}

// 	public function postMakeOtc($player_id) {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$this->form_validation->set_rules('otc_payment_method', 'Bank', 'trim|required|xss_clean');

// 			if ($this->form_validation->run() == false) {
	// 				$this->makeOtc($player_id);
	// 			} else {
	// 				redirect(BASEURL . 'cashier/otc/' . $player_id . '/' . $this->input->post('otc_payment_method'));
	// 			}
	// 		}
	// 	}

// 	public function otc($player_id, $otc_payment_method_id) {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			$data['otc'] = $this->player_functions->getOtcById($otc_payment_method_id);
	// 			$data['player'] = $this->player_functions->getPlayerById($player_id);
	// 			$data['subwallet'] = $this->player_functions->getAllPlayerAccountByPlayerId($player_id);
	// 			$data['totalBalance'] = $this->player_functions->getPlayerTotalBalance($player_id)['totalBalance'];

// 			$this->loadTemplate('Over The Counter', '', '', 'wallet');
	// 			$this->template->write_view('main_content', 'cashier/otc', $data);
	// 			$this->template->render();
	// 		}
	// 	}

// 	public function postOtc($player_id) {
	// 		$this->form_validation->set_rules('otc_payment_method_id', 'Deposit From', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('branch_code', 'Bracn Code', 'trim|xss_clean');
	// 		$this->form_validation->set_rules('reference_number', 'Reference Number', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('deposit_to', 'Deposit To', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('deposit_amount', 'Deposit Amount', 'trim|required|xss_clean|numeric');
	// 		$player = $this->player_functions->getPlayerById($player_id);
	// 		$getRankingLevelSettingByPlayerLevel = $this->player_functions->getRankingLevelSettingByPlayerLevel($player['playerLevel']);
	// 		$bank = $this->player_functions->getBankById($this->input->post('otc_payment_method_id'));
	// 		$daily_max_deposit_amount = $this->player_functions->getDailyDeposit($player['playerAccountId'], date('d'), $this->input->post('otc_payment_method_id'));
	// 		$depositsRequest = $this->player_functions->getDailyDepositToPaymentMethod($player['playerAccountId'], date('d'), $this->input->post('otc_payment_method_id'));

// 		if ($this->form_validation->run() == false) {
	// 			$this->otc($player_id, $this->input->post('otc_payment_method_id'));
	// 		} elseif ($getRankingLevelSettingByPlayerLevel['minDepositAmount'] > $this->input->post('deposit_amount')) {
	// 			//$message = "You did not meet the minimum deposit amount requirement!";
	// 			$message = lang('notify.43');
	// 			$this->alertMessage(2, $message);
	// 			$this->otc($player_id, $this->input->post('otc_payment_method_id'));
	// 		} elseif ($getRankingLevelSettingByPlayerLevel['maxDepositAmount'] < $this->input->post('deposit_amount')) {
	// 			//$message = "You have exceeded the maximum amount of deposit!";
	// 			$message = lang('notify.46');
	// 			$this->alertMessage(2, $message);
	// 			$this->otc($player_id, $this->input->post('otc_payment_method_id'));
	// 		} elseif (($depositsRequest['totalDeposit'] + $daily_max_deposit_amount['totalDeposit'] + $this->input->post('deposit_amount')) > $bank['dailyMaxDepositAmount'] || ($depositsRequest['totalDeposit'] + $daily_max_deposit_amount['totalDeposit']) > $bank['dailyMaxDepositAmount']) {
	// 			//$message = "You're requests had reached bank's daily max deposit!";
	// 			$message = lang('notify.47');
	// 			$this->alertMessage(2, $message);
	// 			$this->otc($player_id, $this->input->post('otc_payment_method_id'));
	// 		} elseif (($daily_max_deposit_amount['totalDeposit'] + $this->input->post('deposit_amount')) > $bank['dailyMaxDepositAmount'] || $this->input->post('deposit_amount') > $bank['dailyMaxDepositAmount']) {
	// 			//$message = "You have reach bank's daily max deposit or Your withdrawal amount is greater than bank's daily max deposit !";
	// 			$message = lang('notify.48');
	// 			$this->alertMessage(2, $message);
	// 			$this->otc($player_id, $this->input->post('otc_payment_method_id'));
	// 		} else {
	// 			$dwIp = $this->input->post('dw_ip');
	// 			$geolocation = $this->utils->getGeoplugin($dwIp);
	// 			//var_dump($geolocation['geoplugin_city'].','.$geolocation['geoplugin_countryName']);exit();

// 			$player = $this->player_functions->getPlayerById($player_id);
	// 			$otc_payment_method_id = $this->input->post('otc_payment_method_id');
	// 			$bank_name = $this->input->post('bank_name');
	// 			$account_name = $this->input->post('account_name');
	// 			$account_number = $this->input->post('account_number');
	// 			$branch_code = $this->input->post('branch_code');
	// 			$reference_number = $this->input->post('reference_number');
	// 			$deposit_to = $this->input->post('deposit_to');
	// 			$deposit_amount = $this->input->post('deposit_amount');
	// 			$today = date("Y-m-d H:i:s");
	// 			$payment_method = "";
	// 			$player_promo_id = NULL;
	// 			$processPromoStatus = NULL;

// 			$player_account = $this->player_functions->getPlayerAccountByPlayerId($player_id);
	// 			$processPromo = "";

// 			if (!empty($this->session->userdata('promoCode'))) {
	// 				$promo = $this->player_functions->checkPromoCodeExist($this->session->userdata('promoCode'));
	// 				$processPromo = $this->player_functions->playerPromo($this->session->userdata('player_id'), $promo['promoId'], $this->input->post('deposit_amount'));
	// 				$player_promo_id = $processPromo['player_promo_id'];
	// 				$this->session->set_userdata('promoMessage', $processPromo['message']);
	// 			}

// 			$data = array(

// 				'playerAccountId' => $player_account['playerAccountId'],
	// 				'playerPromoId' => $processPromo && $processPromo['pass_fail'] == 'success' ? $player_promo_id : NULL,
	// 				'walletType' => 'Main',
	// 				'amount' => $deposit_amount,
	// 				'processedBy' => '0',
	// 				'processDateTime' => '0000-00-00 00:00:00',
	// 				'notes' => NULL,
	// 				'dwMethod' => 1,
	// 				'dwStatus' => 'request',
	// 				'dwDateTime' => $today,
	// 				'transactionType' => 'deposit',
	// 				'dwIp' => $dwIp,
	// 				'dwLocation' => $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
	// 				'transactionCode' => $this->player_functions->getRandomTransactionCode(),
	// 				'status' => '0',
	// 			);

// 			$this->player_functions->createAccountDetails($data);

// 			$wallet_account = $this->player_functions->getWalletAccountByPlayerId($player_id);

// 			$data = array(
	// 				'otcPaymentMethodId' => $otc_payment_method_id,
	// 				'walletAccountId' => $wallet_account['walletAccountId'],
	// 				'branchCode' => $branch_code,
	// 				'amount' => $deposit_amount,
	// 				'transactionDatetime' => $today,
	// 				'transactionType' => 'deposit',
	// 				'referenceNumber' => $reference_number,
	// 				'status' => '0',
	// 			);

// 			$this->player_functions->insertOtcPaymentMethodDeatils($data);

// 			//$message = $this->session->userdata('promoMessage') ." Your deposit request has been sent!";
	// 			$message = $this->session->userdata('promoMessage') . ' ' . lang('notify.38');
	// 			$this->alertMessage(1, $message);
	// 			redirect(BASEURL . 'cashier/transactionHistory/' . $player_id);
	// 		}
	// 	}

// 	public function moneyTransfer($player_id) {
	// 		$this->session->set_userdata('playerId', $player_id);

// 		$data['player'] = $this->player_functions->getPlayerById($player_id);
	// 		$data['game'] = $this->player_functions->getGames();
	// 		$data['subwallet'] = $this->player_functions->getAllPlayerAccountByPlayerId($player_id);
	// 		$data['totalBalance'] = $this->player_functions->getPlayerTotalBalance($player_id)['totalBalance'];

// 		$this->loadTemplate('Money Transfer', '', '', 'wallet');
	// 		$this->template->write_view('main_content', 'cashier/money_transfer', $data);
	// 		$this->template->render();
	// 	}

// 	public function verifyMoneyTransfer($player_id) {
	// 		$this->form_validation->set_rules('transfer_from', 'Transfer From', 'trim|xss_clean|required');
	// 		$this->form_validation->set_rules('transfer_to', 'Transfer To', 'trim|xss_clean|required|callback_checkTransfer');
	// 		$this->form_validation->set_rules('amount', 'Amount', 'trim|xss_clean|required|numeric|callback_checkAmount');

// 		if ($this->form_validation->run() == false) {
	// 			redirect(BASEURL . 'smartcashier/viewCashier');
	// 		} else {
	// 			$transfer_from = $this->input->post('transfer_from');
	// 			$transfer_to = $this->input->post('transfer_to');
	// 			$amount = $this->input->post('amount');

// 			$api = $this->game_platform_manager->initAPI(1);
	// 			$isOnline = $api->checkLoginStatus($this->authentication->getUsername());

// 			if ($transfer_to != 0) {
	// 				$game = $transfer_to;
	// 				$this->transferProcess($player_id, $game, $transfer_from, $transfer_to, $amount);
	// 			} else {
	// 				$game = $transfer_from;

// 				if ($isOnline == 1) {
	// 					//$message = "Your currently login in Game! Logout First before proceeding to Money Transfer.";
	// 					$message = lang('notify.49');
	// 					$this->alertMessage(1, $message);

// 					redirect(BASEURL . 'smartcashier/viewCashier/');
	// 				} else {
	// 					$this->transferProcess($player_id, $game, $transfer_from, $transfer_to, $amount);
	// 				}
	// 			}
	// 		}
	// 	}

// 	public function transferProcess($player_id, $game, $transfer_from, $transfer_to, $amount) {
	// 		//$getPlayerPromo = $this->player_functions->getPlayerPromo($player_id);
	// 		$getGameById = $this->player_functions->getGameById($game);
	// 		$message = '';

// 		//$transfer_to == 0, mainwallet
	// 		//$transfer_to == 1, ptsubwallet
	// 		//$transfer_to == 2, agsubwallet
	// 		if ($transfer_to != 0 && $game == 1) {
	// 			//deposit to PT
	// 			$result = $this->depositToPT($amount, $player_id);
	// 			//var_dump($result);exit();
	// 			$this->PTresult($result, $player_id, $game, $transfer_from, $transfer_to, $amount);
	// 		} elseif ($transfer_to == 0 && $game == 1) {
	// 			//withdraw to PT
	// 			$result = $this->withdrawalToPT($amount, $player_id);
	// 			//var_dump($result);exit();
	// 			$this->PTresult($result, $player_id, $game, $transfer_from, $transfer_to, $amount);
	// 		} elseif ($transfer_to != 0 && $game == 2) {
	// 			//deposit to AG
	// 			$result = $this->depositToAG($amount, $player_id);

// 			$this->AGresult($result, $player_id, $game, $transfer_from, $transfer_to, $amount);
	// 		} elseif ($transfer_to == 0 && $game == 2) {
	// 			//withdraw to AG
	// 			$result = $this->withdrawalToAG($amount, $player_id);

// 			$this->AGresult($result, $player_id, $game, $transfer_from, $transfer_to, $amount);
	// 		}
	// 	}

// 	public function PTresult($result, $player_id, $game, $transfer_from, $transfer_to, $amount) {
	// 		if ($result['success'] == 1) {
	// 			if ($transfer_to == 0) {
	// 				// save http_request (cookies, referer, user-agent)
	// 				$this->saveHttpRequest($this->authentication->getPlayerId(), 6);
	// 			} else {
	// 				// save http_request (cookies, referer, user-agent)
	// 				$this->saveHttpRequest($this->authentication->getPlayerId(), 5);
	// 			}

// 			$transfer_from = $this->player_functions->getPlayerAccountBySubWallet($player_id, $transfer_from);
	// 			$transfer_to = $this->player_functions->getPlayerAccountBySubWallet($player_id, $transfer_to);

// 			$this->withdrawFromPlayerAccount($transfer_from, $amount, $result['currentplayerbalance']);
	// 			$this->depositFromPlayerAccount($transfer_to, $amount, $result['currentplayerbalance']);

// 			$data = array(
	// 				'playerId' => $player_id,
	// 				'gameId' => $game,
	// 				'processStatus' => 'transferred',
	// 				'requestDatetime' => date("Y-m-d H:i:s"),
	// 				'transferFrom' => $transfer_from['playerAccountId'],
	// 				'transferTo' => $transfer_to['playerAccountId'],
	// 				'amount' => $amount,
	// 			);

// 			$this->player_functions->addToSubWalletDetails($data);

// 			//$message .= "Transferring of money has been successfully made!";\
	// 			$message = lang('notify.50');
	// 			$this->alertMessage(1, $message);
	// 		} else {
	// 			//$message .= "Your transfer request hasn't been sent!";
	// 			$message = lang('notify.51');
	// 			$this->alertMessage(2, $message);
	// 		}

// 		redirect(BASEURL . 'smartcashier/viewCashier/');
	// 	}

// 	public function AGresult($result, $player_id, $game, $transfer_from, $transfer_to, $amount) {
	// 		if ($result['success'] == 1) {
	// 			if ($transfer_to == 0) {
	// 				// save http_request (cookies, referer, user-agent)
	// 				$this->saveHttpRequest($this->authentication->getPlayerId(), 6);
	// 			} else {
	// 				// save http_request (cookies, referer, user-agent)
	// 				$this->saveHttpRequest($this->authentication->getPlayerId(), 5);
	// 			}

// 			$transfer_from = $this->player_functions->getPlayerAccountBySubWallet($player_id, $transfer_from);
	// 			$transfer_to = $this->player_functions->getPlayerAccountBySubWallet($player_id, $transfer_to);

// 			$new_balance = $this->getAGBalance($player_id);

// 			if ($transfer_from['type'] == 'wallet') {
	// 				$this->withdrawFromPlayerAccount($transfer_from, $amount, null);
	// 			} else {
	// 				$this->withdrawFromPlayerAccount($transfer_from, $amount, $new_balance);
	// 			}

// 			if ($transfer_to['type'] == 'wallet') {
	// 				$this->depositFromPlayerAccount($transfer_to, $amount, null);
	// 			} else {
	// 				$this->depositFromPlayerAccount($transfer_to, $amount, $new_balance);
	// 			}

// 			$data = array(
	// 				'playerId' => $player_id,
	// 				'gameId' => $game,
	// 				'processStatus' => 'transferred',
	// 				'requestDatetime' => date("Y-m-d H:i:s"),
	// 				'transferFrom' => $transfer_from['playerAccountId'],
	// 				'transferTo' => $transfer_to['playerAccountId'],
	// 				'amount' => $amount,
	// 			);

// 			$this->player_functions->addToSubWalletDetails($data);

// 			//get player current balance
	// 			$playerMainWalletBalance = $this->player_functions->getMainWalletBalance($player_id);
	// 			if ($playerMainWalletBalance['totalBalanceAmount'] == null) {
	// 				$playerMainWalletBalance['totalBalanceAmount'] = 0;
	// 			}
	// 			$currentMainwalletBal = $playerMainWalletBalance['totalBalanceAmount'];

// 			//$message .= "Transferring of money has been successfully made!";\
	// 			$message = lang('notify.50');
	// 			$this->alertMessage(1, $message);
	// 		} else {
	// 			//$message .= "Your transfer request hasn't been sent!";
	// 			$message = lang('notify.51');
	// 			$this->alertMessage(2, $message);
	// 		}

// 		redirect(BASEURL . 'smartcashier/viewCashier/');
	// 	}

// 	/**
	// 	 * record wallet transaction
	// 	 *
	// 	 * @param  walletType int
	// 	 * @param  playerId int
	// 	 * @param  responseResult int
	// 	 * @param  gameProviderExternalTransactionId int
	// 	 * @param  responseResultId int
	// 	 * @param  amount double
	// 	 * @return null
	// 	 */
	// 	public function recordWalletTransaction($walletType, $playerId, $responseResult, $gameProviderExternalTransactionId, $responseResultId, $amount) {
	// 		//get player current balance
	// 		$playerMainWalletBalance = $this->player_functions->getMainWalletBalance($playerId);
	// 		if ($playerMainWalletBalance['totalBalanceAmount'] == null) {
	// 			$playerMainWalletBalance['totalBalanceAmount'] = 0;
	// 		}
	// 		$currentMainwalletBal = $playerMainWalletBalance['totalBalanceAmount'];

// 		if ($walletType == 1) {
	// 			$walletTypeName = 'PT';
	// 		} elseif ($walletType == 2) {
	// 			$walletTypeName = 'AG';
	// 		}

// 		//get sub wallet account id
	// 		$data = $this->player_functions->getSubWalletAccountId($playerId, $walletType);

// 		if ($responseResult == "Deposit OK") {
	// 			//save transaction details on wallet transfer
	// 			$transactionDetails = array('amount' => $amount,
	// 				'transaction_type' => 5, //transfer to sub wallet
	// 				'from_id' => $playerId,
	// 				'from_type' => 2, //player
	// 				'to_id' => $playerId,
	// 				'to_type' => 2, //player
	// 				'note' => 'player ' . $playerId . ' transferred ' . $amount . ' from main wallet to ' . $walletTypeName . ' subwallet',
	// 				'before_balance' => $currentMainwalletBal,
	// 				'after_balance' => $currentMainwalletBal - $amount,
	// 				'external_transaction_id' => $gameProviderExternalTransactionId,
	// 				'response_result_id' => $responseResultId,
	// 				'created_at' => date('Y-m-d H:i:s'),
	// 				'sub_wallet_id' => $data['playerAccountId'],
	// 				'status' => 0, //approved
	// 			);
	// 		} else {
	// 			//save transaction details on wallet transfer
	// 			$transactionDetails = array('amount' => $amount,
	// 				'transaction_type' => 5, //transfer to sub wallet
	// 				'from_id' => $playerId,
	// 				'from_type' => 2, //player
	// 				'to_id' => $playerId,
	// 				'to_type' => 2, //player
	// 				'note' => 'player ' . $playerId . ' transferred ' . $amount . ' from ' . $walletTypeName . ' subwallet to main wallet',
	// 				'before_balance' => $currentMainwalletBal,
	// 				'after_balance' => $currentMainwalletBal + $amount,
	// 				'external_transaction_id' => $gameProviderExternalTransactionId,
	// 				'response_result_id' => $responseResultId,
	// 				'created_at' => date('Y-m-d H:i:s'),
	// 				'sub_wallet_id' => $data['playerAccountId'],
	// 				'status' => 0, //approved
	// 			);

// 		}
	// 		$this->player_functions->saveTransaction($transactionDetails);
	// 	}

// 	public function checkTransfer() {
	// 		$transfer_from = $this->input->post('transfer_from');
	// 		$transfer_to = $this->input->post('transfer_to');

// 		if ($transfer_from == $transfer_to) {
	// 			//$this->form_validation->set_message('checkTransfer', "Make sure that transfer from is different from transfer to.");
	// 			//$this->form_validation->set_message('checkTransfer', lang('notify.52'));
	// 			$message = lang('notify.52');
	// 			$this->alertMessage(1, $message);
	// 			return false;
	// 		} else if ($transfer_from != 0 && $transfer_to != 0) {
	// 			//$this->form_validation->set_message('checkTransfer', "Cannot Transfer money from sub wallet to another sub wallet.");
	// 			$message = lang('notify.53');
	// 			$this->alertMessage(1, $message);
	// 			$this->form_validation->set_message('checkTransfer', lang('notify.53'));
	// 			return false;
	// 		}

// 		return true;
	// 	}

// 	public function checkAmount() {
	// 		$transfer_from = $this->input->post('transfer_from');
	// 		$amount = $this->input->post('amount');
	// 		$player_id = $this->authentication->getPlayerId();

// 		$player_account = $this->player_functions->getPlayerAccountBySubWallet($player_id, $transfer_from);

// 		if ($amount > $player_account['totalBalanceAmount']) {
	// 			//$this->form_validation->set_message('checkAmount', "Transfer of money should not be greater than the balance of your wallet.");
	// 			$message = lang('notify.54');
	// 			$this->alertMessage(1, $message);
	// 			//$this->form_validation->set_message('checkTransfer', lang('notify.54'));
	// 			return false;
	// 		}

// 		return true;
	// 	}

// 	public function withdrawFromPlayerAccount($player_account_from, $amount, $new_balance) {
	// 		$balance = $this->player_functions->getBalanceByPlayerAccountId($player_account_from['playerAccountId']);

// 		if ($player_account_from['type'] == 'wallet') {
	// 			$data = array(
	// 				'totalBalanceAmount' => $balance - $amount,
	// 			);
	// 		} else {
	// 			$data = array(
	// 				'totalBalanceAmount' => (double) $new_balance,
	// 			);
	// 		}

// 		$this->player_functions->setPlayerNewBalAmountByPlayerAccountId($player_account_from['playerAccountId'], $data);
	// 	}

// 	public function depositFromPlayerAccount($player_account_to, $amount, $new_balance) {
	// 		$balance = $this->player_functions->getBalanceByPlayerAccountId($player_account_to['playerAccountId']);

// 		if ($player_account_to['type'] == 'wallet') {
	// 			$data = array(
	// 				'totalBalanceAmount' => $balance + $amount,
	// 			);
	// 		} else {
	// 			$data = array(
	// 				'totalBalanceAmount' => (double) $new_balance,
	// 			);
	// 		}

// 		$this->player_functions->setPlayerNewBalAmountByPlayerAccountId($player_account_to['playerAccountId'], $data);
	// 	}

// 	/**
	// 	 * deposit to PT
	// 	 *
	// 	 * @param   playerName str
	// 	 * @param   amount int
	// 	 *
	// 	 * @return json
	// 	 */
	// 	function depositToPT($amount, $playerId) {
	// 		$playerName = $this->authentication->getUsername(); //$this->player_manager->getPlayerUsername($playerId);

// 		$api = $this->game_platform_manager->initAPI(1);

// 		$result = $api->depositToGame($playerName, $amount);

// 		return $result;
	// 	}

// 	/**
	// 	 * withdrawal to PT
	// 	 *
	// 	 * @param   playerName str
	// 	 * @param   amount int
	// 	 *
	// 	 * @return json
	// 	 */
	// 	function withdrawalToPT($amount, $playerId) {
	// 		$playerName = $this->authentication->getUsername(); //$this->player_manager->getPlayerUsername($playerId);

// 		$api = $this->game_platform_manager->initAPI(1);

// 		$api->logout($playerName);

// 		$result = $api->withdrawFromGame($playerName, $amount);

// 		return $result;
	// 	}

// 	public function depositToAG($amount, $playerId) {
	// 		$playerName = $this->authentication->getUsername();

// 		$api = $this->game_platform_manager->initAPI(2);

// 		$result = $api->depositToGame($playerName, $amount);

// 		return $result;
	// 	}

// 	/**
	// 	 * get ag balance
	// 	 *
	// 	 * @return  void
	// 	 */
	// 	public function getAGBalance($playerId, $test = false) {
	// 		$playerName = $this->authentication->getUsername();

// 		$api = $this->game_platform_manager->initAPI(2);
	// 		$result = $api->queryPlayerBalance($playerName);

// 		if ($result['success'] == 1) {
	// 			return $result['balance'];
	// 		}
	// 	}

// 	public function withdrawalToAG($amount, $playerId) {
	// 		$playerName = $this->authentication->getUsername();

// 		$api = $this->game_platform_manager->initAPI(2);

// 		$result = $api->withdrawFromGame($playerName, $amount);

// 		return $result;
	// 	}

// 	/**
	// 	 * view cashier
	// 	 *
	// 	 *
	// 	 * @return rendered template
	// 	 */
	// 	public function viewCashier() {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$player_id = $this->authentication->getPlayerId();
	// 			//$this->updateSubWallet();
	// 			$this->session->set_userdata('playerId', $player_id);
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			$data['player'] = $this->player_functions->getPlayerById($player_id);
	// 			$data['subwallet'] = $this->player_functions->getAllPlayerAccountByPlayerId($player_id);
	// 			$data['totalBalance'] = $this->player_functions->getPlayerTotalBalance($player_id)['totalBalance'];
	// 			$data['cashbackwallet'] = $this->player_functions->getPlayerCashbackWalletBalance($player_id);
	// 			//var_dump($data['cashbackwallet']);exit();
	// 			//var_dump($data['cashbackHistory']);exit();
	// 			$data['game'] = $this->player_functions->getGames();
	// 			$data['search_list'] = 0;

// 			//var_dump($data);exit();
	// 			$this->session->unset_userdata('search_from');
	// 			$this->session->unset_userdata('search_to');
	// 			$this->session->unset_userdata('transfer_history');
	// 			$this->session->unset_userdata('deposit_history');
	// 			$this->session->unset_userdata('withdraw_history');
	// 			$this->session->unset_userdata('cashback_history');

// 			$this->loadTemplate('Cashier', '', '', 'wallet');
	// 			$this->template->write_view('main_content', 'cashier/view_cashier', $data);
	// 			$this->template->render();
	// 		}

// 	}

// 	public function getSubWalletBalance() {
	// 		$player_id = $this->authentication->getPlayerId();
	// 		echo json_encode($this->player_functions->getAllPlayerAccountByPlayerId($player_id));
	// 	}

// 	public function depositHistory($segment, $from, $to) {
	// 		$player_id = $this->authentication->getPlayerId();

// 		$search = array(
	// 			'from' => urldecode($from),
	// 			'to' => urldecode($to),
	// 		);

// 		$data['count_all'] = count($this->player_functions->getAllDepositsWLimit($player_id, null, null, $search));
	// 		$config['base_url'] = "javascript:depositHistory(";
	// 		$config['total_rows'] = $data['count_all'];
	// 		$config['per_page'] = '5';
	// 		$config['num_links'] = '1';

// 		$config['first_tag_open'] = '<li>';
	// 		$config['last_tag_open'] = '<li>';
	// 		$config['next_tag_open'] = '<li>';
	// 		$config['prev_tag_open'] = '<li>';
	// 		$config['num_tag_open'] = '<li>';

// 		$config['first_tag_close'] = '</li>';
	// 		$config['last_tag_close'] = '</li>';
	// 		$config['next_tag_close'] = '</li>';
	// 		$config['prev_tag_close'] = '</li>';
	// 		$config['num_tag_close'] = '</li>';

// 		$config['cur_tag_open'] = "<li><span><b>";
	// 		$config['cur_tag_close'] = "</b></span></li>";

// 		$this->pagination->initialize($config);

// 		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
	// 		$data['deposits'] = $this->player_functions->getAllDepositsWLimit($player_id, $config['per_page'], $segment, $search);

// 		$this->load->view('cashier/ajax_deposit_history', $data);
	// 	}

// 	public function withdrawalHistory($segment, $from, $to) {
	// 		$player_id = $this->authentication->getPlayerId();

// 		$search = array(
	// 			'from' => urldecode($from),
	// 			'to' => urldecode($to),
	// 		);

// 		$data['count_all'] = count($this->player_functions->getAllWithdrawalsWLimit($player_id, null, null, $search));
	// 		$config['base_url'] = "javascript:withdrawalHistory(";
	// 		$config['total_rows'] = $data['count_all'];
	// 		$config['per_page'] = '5';
	// 		$config['num_links'] = '1';

// 		$config['first_tag_open'] = '<li>';
	// 		$config['last_tag_open'] = '<li>';
	// 		$config['next_tag_open'] = '<li>';
	// 		$config['prev_tag_open'] = '<li>';
	// 		$config['num_tag_open'] = '<li>';

// 		$config['first_tag_close'] = '</li>';
	// 		$config['last_tag_close'] = '</li>';
	// 		$config['next_tag_close'] = '</li>';
	// 		$config['prev_tag_close'] = '</li>';
	// 		$config['num_tag_close'] = '</li>';

// 		$config['cur_tag_open'] = "<li><span><b>";
	// 		$config['cur_tag_close'] = "</b></span></li>";

// 		$this->pagination->initialize($config);

// 		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
	// 		$data['withdrawals'] = $this->player_functions->getAllWithdrawalsWLimit($player_id, $config['per_page'], $segment, $search);

// 		$this->load->view('cashier/ajax_withdrawal_history', $data);
	// 	}

// 	public function cashbackHistory($segment, $from, $to) {
	// 		$player_id = $this->authentication->getPlayerId();

// 		$search = array(
	// 			'from' => urldecode($from),
	// 			'to' => urldecode($to),
	// 		);

// 		$data['count_all'] = count($this->player_functions->getCashbackHistoryWLimit($player_id, null, null, $search));
	// 		$config['base_url'] = "javascript:cashbackHistory(";
	// 		$config['total_rows'] = $data['count_all'];
	// 		$config['per_page'] = '5';
	// 		$config['num_links'] = '1';

// 		$config['cur_tag_open'] = "<li><span><b>";
	// 		$config['cur_tag_close'] = "</b></span></li>";

// 		$this->pagination->initialize($config);

// 		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
	// 		$data['cashbackHistory'] = $this->player_functions->getCashbackHistoryWLimit($player_id, $config['per_page'], $segment, $search);

// 		$this->load->view('cashier/ajax_cashback_history', $data);
	// 	}

// 	public function transferHistory($segment, $from, $to) {
	// 		$player_id = $this->authentication->getPlayerId();

// 		$search = array(
	// 			'from' => urldecode($from),
	// 			'to' => urldecode($to),
	// 		);

// 		$data['count_all'] = count($this->player_functions->getAllTransferHistoryByPlayerIdWLimit($player_id, null, null, $search));
	// 		$config['base_url'] = "javascript:transferHistory(";
	// 		$config['total_rows'] = $data['count_all'];
	// 		$config['per_page'] = '5';
	// 		$config['num_links'] = '1';

// 		$config['first_tag_open'] = '<li>';
	// 		$config['last_tag_open'] = '<li>';
	// 		$config['next_tag_open'] = '<li>';
	// 		$config['prev_tag_open'] = '<li>';
	// 		$config['num_tag_open'] = '<li>';

// 		$config['first_tag_close'] = '</li>';
	// 		$config['last_tag_close'] = '</li>';
	// 		$config['next_tag_close'] = '</li>';
	// 		$config['prev_tag_close'] = '</li>';
	// 		$config['num_tag_close'] = '</li>';

// 		$config['cur_tag_open'] = "<li><span><b>";
	// 		$config['cur_tag_close'] = "</b></span></li>";

// 		$this->pagination->initialize($config);

// 		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
	// 		$data['transfer_history'] = $this->player_functions->getAllTransferHistoryByPlayerIdWLimit($player_id, $config['per_page'], $segment, $search);

// 		$this->load->view('cashier/ajax_transfer_history', $data);
	// 	}

// 	public function balanceAdjustmentHistory($segment, $from, $to) {
	// 		$player_id = $this->authentication->getPlayerId();

// 		$search = array(
	// 			'from' => urldecode($from),
	// 			'to' => urldecode($to),
	// 		);

// 		$data['count_all'] = count($this->player_functions->getPlayerAdjustmentHistoryWLimit($player_id, null, null, $search));
	// 		$config['base_url'] = "javascript:balanceAdjustmentHistory(";
	// 		$config['total_rows'] = $data['count_all'];
	// 		$config['per_page'] = '5';
	// 		$config['num_links'] = '1';

// 		$config['first_tag_open'] = '<li>';
	// 		$config['last_tag_open'] = '<li>';
	// 		$config['next_tag_open'] = '<li>';
	// 		$config['prev_tag_open'] = '<li>';
	// 		$config['num_tag_open'] = '<li>';

// 		$config['first_tag_close'] = '</li>';
	// 		$config['last_tag_close'] = '</li>';
	// 		$config['next_tag_close'] = '</li>';
	// 		$config['prev_tag_close'] = '</li>';
	// 		$config['num_tag_close'] = '</li>';

// 		$config['cur_tag_open'] = "<li><span><b>";
	// 		$config['cur_tag_close'] = "</b></span></li>";

// 		$this->pagination->initialize($config);

// 		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
	// 		$data['balanceAdjustmentHistory'] = $this->player_functions->getPlayerAdjustmentHistoryWLimit($player_id, $config['per_page'], $segment, $search);

// 		$this->load->view('cashier/ajax_balance_adjustment_history', $data);
	// 	}

// 	public function promoHistory($from, $to) {
	// 		$player_id = $this->authentication->getPlayerId();

// 		$search = array(
	// 			'from' => urldecode($from),
	// 			'to' => urldecode($to),
	// 		);

// 		$data['promoHistory'] = $this->player_functions->getPlayerPromoHistoryWLimit($player_id, $search);

// 		$this->load->view('cashier/ajax_promo_history', $data);
	// 	}

// 	/**
	// 	 * view search cashier
	// 	 *
	// 	 *
	// 	 * @return rendered template
	// 	 */
	// 	public function searchCashier() {
	// 		$player_id = $this->authentication->getPlayerId();

// 		$this->form_validation->set_rules('from', 'From date', 'trim|xss_clean');
	// 		$this->form_validation->set_rules('to', 'To date', 'trim|xss_clean');

// 		if (!empty($this->input->post('from')) && !empty($this->input->post('to'))) {
	// 			$this->form_validation->set_rules('search_list', 'Search List', 'trim|required|xss_clean');
	// 		} else {
	// 			$this->form_validation->set_rules('search_list', 'Search List', 'trim|xss_clean');
	// 		}

// 		if ($this->form_validation->run() == false) {
	// 			$message = "Please fill the fields with valid inputs";
	// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/viewCashier');
	// 		} else {
	// 			$from = $this->input->post('from');
	// 			$to = $this->input->post('to');
	// 			$search_list = $this->input->post('search_list');

// 			if ($from > $to) {
	// 				$message = "'From date' should be less than 'To date'";
	// 				$this->alertMessage(2, $message);
	// 				redirect(BASEURL . 'smartcashier/viewCashier');
	// 			} else {
	// 				$data = array();
	// 				$search = array(
	// 					'from' => $from,
	// 					'to' => $to,
	// 				);
	// 				$data['search'] = $search;

// 				$this->session->set_userdata('search_from', $from);
	// 				$this->session->set_userdata('search_to', $to);

// 				$data['player'] = $this->player_functions->getPlayerById($player_id);
	// 				$data['subwallet'] = $this->player_functions->getAllPlayerAccountByPlayerId($player_id);
	// 				$data['totalBalance'] = $this->player_functions->getPlayerTotalBalance($player_id)['totalBalance'];
	// 				$data['cashbackwallet'] = $this->player_functions->getPlayerCashbackWalletBalance($player_id);
	// 				$data['game'] = $this->player_functions->getGames();
	// 				$data['search_list'] = $search_list;

// 				$this->loadTemplate('Cashier', '', '', 'wallet');
	// 				$this->template->write_view('main_content', 'cashier/view_cashier', $data);
	// 				$this->template->render();
	// 			}
	// 		}
	// 	}

// 	/**
	// 	 * view withdrawal form
	// 	 *
	// 	 *
	// 	 * @return rendered template
	// 	 */
	// 	public function viewWithdraw() {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			//get all bank types
	// 			$data['banks'] = $this->player_functions->getAllBankType();

// 			//get player id
	// 			$playerId = $this->authentication->getPlayerId();

// 			//get bank details
	// 			$data['playerBankDetails'] = $this->player_functions->getWithdrawalBankDetails($playerId);
	// 			//var_dump($data['playerBankDetails']);exit();
	// 			$this->loadTemplate('Cashier', '', '', 'wallet');
	// 			$this->template->write_view('main_content', 'cashier/view_withdrawal_form', $data);
	// 			$this->template->render();
	// 		}
	// 	}

// 	/**
	// 	 * verify withdrawal
	// 	 *
	// 	 *
	// 	 * @return rendered template
	// 	 */
	// 	public function verifyWithdrawal() {
	// 		$this->form_validation->set_rules('amount', 'Withdrawal Amount', 'trim|required|xss_clean|is_numeric');

// 		$withdrawalAmount = $this->input->post('amount');
	// 		$bankName = $this->input->post('bank_name');
	// 		$preferredAccount = $this->input->post('preferred_account');
	// 		$bankAccountFullname = $this->input->post('bank_account_fullname');
	// 		$bankAccountNumber = $this->input->post('bank_account_number');
	// 		$province = $this->input->post('province');
	// 		$city = $this->input->post('city');
	// 		$branch = $this->input->post('branch');
	// 		$dwIp = $this->input->post('dw_ip');

// 		$rememberMyAccount = $this->input->post('rememberMyAccount_cb');
	// 		$account_type = $this->input->post('account_type');

// 		$this->session->set_userdata('wf_bank_name', $bankName);
	// 		$this->session->set_userdata('wf_bank_account_fullname', $bankAccountFullname);
	// 		$this->session->set_userdata('wf_bank_account_number', $bankAccountNumber);
	// 		$this->session->set_userdata('wf_province', $province);
	// 		$this->session->set_userdata('wf_city', $city);
	// 		$this->session->set_userdata('wf_branch', $branch);
	// 		$this->session->set_userdata('wf_rememberMyAccount', $rememberMyAccount);
	// 		$today = date("Y-m-d H:i:s");

// 		//get player's data
	// 		$playerId = $this->authentication->getPlayerId();

// 		//get player's main wallet balance
	// 		$playerMainWalletBalance = $this->player_functions->getPlayerMainWalletBalance($playerId);

// 		//get player's withdrawal rule
	// 		$playerWithdrawalRule = $this->player_functions->getPlayerWithdrawalRule($playerId);

// 		//player's account details
	// 		$playerAccount = $this->player_functions->getPlayerAccountByPlayerIdOnly($playerId);

// 		//get player's withdrawal rule
	// 		$playerCurrentTotalWithdrawalToday = $this->player_functions->getPlayerCurrentTotalWithdrawalToday($playerAccount['playerAccountId']);

// 		//check if player has existing active promo
	// 		$existingActivePromoFlag = $this->player_functions->checkPlayerPromoActive($playerId);

// 		//check if player has existing active promo
	// 		$existingRequestPromoFlag = $this->player_functions->checkPlayerPromoRequest($playerId);

// 		//var_dump($account_type);exit();
	// 		if ($this->form_validation->run() == false) {
	// 			$message = "Some fields are filled incorrectly";
	// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/viewWithdraw', 'refresh');
	// 		} elseif ($withdrawalAmount > $playerMainWalletBalance['totalBalanceAmount']) {
	// 			//$message = "Withdrawal amount should not be greater than your current main wallet balance. Please try again!";
	// 			$message = lang('notify.55');
	// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/viewWithdraw', 'refresh');
	// 		} elseif ($playerCurrentTotalWithdrawalToday[0]['totalWithdrawalToday'] > $playerWithdrawalRule['dailyMaxWithdrawal']) {
	// 			//$message = "Your amount request should not greater than your daily max withdrawal limit.";
	// 			$message = lang('notify.56');
	// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/viewWithdraw', 'refresh');
	// 		} elseif ($withdrawalAmount <= 0) {
	// 			//$message = "Your withdrawal amount request should not be less than or equal to zero.";
	// 			$message = lang('notify.57');
	// 			$this->alertMessage(2, $message);
	// 			redirect(BASEURL . 'smartcashier/viewWithdraw', 'refresh');
	// 		} else {
	// 			//check if new account or old account
	// 			if (!empty($account_type) && $account_type == 'na') {
	// 				$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|required|xss_clean');
	// 				$this->form_validation->set_rules('bank_account_fullname', 'Bank Account Full Name', 'trim|required|xss_clean');
	// 				$this->form_validation->set_rules('bank_account_number', 'Bank Account Number', 'trim|required|xss_clean|is_numeric');
	// 				$this->form_validation->set_rules('province', 'Province', 'trim|xss_clean');
	// 				$this->form_validation->set_rules('city', 'City', 'trim|xss_clean');
	// 				$this->form_validation->set_rules('branch', 'Branch', 'trim|xss_clean');
	// 				//check if remember this account add to record
	// 				$rememberMyAccount == TRUE ? $rememberMyAccount = '1' : $rememberMyAccount = '0';

// 				//add bank account details and get id
	// 				$data = array(
	// 					'playerId' => $playerId,
	// 					'bankTypeId' => $bankName, //bank type id
	// 					'bankAccountNumber' => $bankAccountNumber,
	// 					'bankAccountFullName' => $bankAccountFullname,
	// 					'city' => $city,
	// 					'province' => $province,
	// 					'branch' => $branch,
	// 					'isRemember' => $rememberMyAccount, //0 is false
	// 					'dwBank' => '1', //0 is withdrawal
	// 					'status' => '0', //0 is active
	// 				);
	// 				//var_dump($data);exit();
	// 				$playerBankDetailsId = $this->player_functions->addBankDetailsByWithdrawal($data);
	// 			} else {
	// 				$playerBankDetailsId = $preferredAccount; //bank type id
	// 			}
	// 			$geolocation = $this->utils->getGeoplugin($dwIp);
	// 			//wallet account details
	// 			$walletAccountData = array(
	// 				'playerAccountId' => $playerAccount['playerAccountId'],
	// 				'walletType' => 'Main',
	// 				'amount' => $withdrawalAmount,
	// 				'dwMethod' => 1,
	// 				'dwStatus' => 'request',
	// 				'dwDateTime' => $today,
	// 				'transactionType' => 'withdrawal',
	// 				'dwIp' => $dwIp,
	// 				'dwLocation' => $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
	// 				'transactionCode' => $this->player_functions->getRandomTransactionCode(),
	// 				'status' => '0',
	// 			);

// 			//save record to withdrawal request
	// 			$localBankWithdrawalDetails = array(
	// 				'withdrawalAmount' => $withdrawalAmount,
	// 				'playerBankDetailsId' => $playerBankDetailsId,
	// 				'depositDateTime' => $today,
	// 				'status' => 'active',
	// 			);
	// 			//$this->player_functions->createAccountDetails($data);
	// 			$this->player_functions->localBankWithdrawal($walletAccountData, $localBankWithdrawalDetails);

// 			// save http_request (cookies, referer, user-agent)
	// 			$this->saveHttpRequest($this->authentication->getPlayerId(), 4);

// 			//$message = $this->session->userdata('promoMessage') . " Your withdrawal request has been sent!";
	// 			$message = $this->session->userdata('promoMessage') . ' ' . lang('notify.58');
	// 			$this->alertMessage(1, $message);
	// 			redirect(BASEURL . 'smartcashier/viewCashier');
	// 		}
	// 	}

// 	/**
	// 	 * view bank details
	// 	 *
	// 	 * @return void
	// 	 */
	// 	public function bankDetails() {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			//var_dump($data);exit();
	// 			$data['bank_details'] = $this->player_functions->getBankDetails($this->authentication->getPlayerId());
	// 			//var_dump($data['bank_details'] );exit();
	// 			$this->loadTemplate('Player Settings', '', '', 'settings');
	// 			$this->template->write_view('main_content', 'player/bank_details', $data);
	// 			$this->template->render();
	// 		}
	// 	}

// 	/**
	// 	 * view addedit bank
	// 	 *
	// 	 * @return void
	// 	 */
	// 	public function addEditBank($dw_bank, $bank_details_id = '') {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			//var_dump($data);exit();
	// 			$data['dw_bank'] = $dw_bank;
	// 			$data['bank'] = $this->player_functions->getBankDetailsById($bank_details_id);
	// 			$data['banks'] = $this->player_functions->getAllBankType();
	// 			$data['bank_details_id'] = $bank_details_id;
	// 			//var_dump($data);exit();
	// 			$this->loadTemplate('Player Settings', '', '', 'settings');
	// 			$this->template->write_view('main_content', 'player/add_edit_bank', $data);
	// 			$this->template->render();
	// 		}
	// 	}

// 	/**
	// 	 * view add edit withdrawal bank
	// 	 *
	// 	 * @return void
	// 	 */
	// 	public function addEditWithdrawalBank($dw_bank, $bank_details_id = '') {
	// 		if (!$this->authentication->isLoggedIn()) {
	// 			redirect(BASEURL . 'online/welcome', 'refresh');
	// 		} else {
	// 			$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 			$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 			$data['dw_bank'] = $dw_bank;
	// 			$data['bank'] = $this->player_functions->getBankDetailsById($bank_details_id);
	// 			$data['banks'] = $this->player_functions->getAllBankType();
	// 			$data['bank_details_id'] = $bank_details_id;
	// 			//var_dump($data);exit();
	// 			$this->loadTemplate('Player Settings', '', '', 'settings');
	// 			$this->template->write_view('main_content', 'player/add_edit_withdrawal_bank', $data);
	// 			$this->template->render();
	// 		}
	// 	}

// 	/**
	// 	 * post bank details
	// 	 *
	// 	 * @return void
	// 	 */
	// 	public function postBankDetails() {
	// 		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('bank_account_number', 'Bank Account Number', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('bank_account_fullname', 'Bank Account Full Name', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('bank_address', 'Bank Address', 'trim|xss_clean');

// 		$isEdit = $this->input->post('player_bank_details_id');
	// 		//var_dump($isEdit);exit();
	// 		if ($this->form_validation->run() == false) {
	// 			//$message = "Some Bank details are invalid";
	// 			$message = lang('notify.30');
	// 			$this->alertMessage(2, $message);
	// 			if (empty($isEdit)) {
	// 				$this->addEditBank($this->input->post('dw_bank'));
	// 			} else {
	// 				$this->addEditBank($this->input->post('dw_bank'), $isEdit);
	// 			}
	// 		} else {
	// 			if (empty($isEdit)) {
	// 				$data = array(
	// 					'playerId' => $this->authentication->getPlayerId(),
	// 					'bankTypeId' => $this->input->post('bank_name'),
	// 					'bankAccountNumber' => $this->input->post('bank_account_number'),
	// 					'bankAccountFullName' => $this->input->post('bank_account_fullname'),
	// 					'bankAddress' => $this->input->post('bank_address'),
	// 					'dwBank' => $this->input->post('dw_bank'),
	// 					'isDefault' => '0',
	// 					'isRemember' => '1',
	// 					'status' => '0',
	// 				);
	// 				$this->player_functions->addBankDetails($data);
	// 				//$message = "Bank details has successfully added";
	// 				$message = lang('notify.31');
	// 				$this->alertMessage(1, $message);
	// 			} else {
	// 				$data = array(
	// 					'bankTypeId' => $this->input->post('bank_name'),
	// 					'bankAccountNumber' => $this->input->post('bank_account_number'),
	// 					'bankAccountFullName' => $this->input->post('bank_account_fullname'),
	// 					'bankAddress' => $this->input->post('bank_address'),
	// 					'dwBank' => $this->input->post('dw_bank'),
	// 				);

// 				//save bank changes
	// 				$origbank = $this->player_functions->getBankDetailsById($isEdit);
	// 				$change = $this->checkBankChanges($origbank, $data);
	// 				$changes = array(
	// 					'playerBankDetailsId' => $isEdit,
	// 					'changes' => $change,
	// 					'createdOn' => date("Y-m-d H:i:s"),
	// 				);
	// 				$this->player_functions->saveBankChanges($changes);

// 				$this->player_functions->editBankDetails($isEdit, $data);
	// 				//$message = "Bank details has successfully edited";
	// 				$message = lang('notify.32');
	// 				$this->alertMessage(1, $message);
	// 			}

// 			redirect(BASEURL . 'smartcashier/bankDetails/');
	// 		}
	// 	}

// 	public function checkBankChanges($origbank, $data) {
	// 		$change = array();

// 		foreach ($data as $key => $value) {
	// 			if ($value != $origbank[$key] && $key != 'dwBank') {
	// 				if ($key == 'bankTypeId') {
	// 					array_push($change, 'bankName');
	// 				} else {
	// 					array_push($change, $key);
	// 				}
	// 			}
	// 		}

// 		$changes = implode(', ', $change);
	// 		return $changes;
	// 	}

// 	/**
	// 	 * post bank details
	// 	 *
	// 	 * @return void
	// 	 */
	// 	public function postWithdrawalBankDetails() {
	// 		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('bank_account_number', 'Bank Account Number', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('bank_account_fullname', 'Bank Account Full Name', 'trim|required|xss_clean');
	// 		$this->form_validation->set_rules('bank_province', 'Bank Province', 'trim|xss_clean');
	// 		$this->form_validation->set_rules('bank_city', 'Bank City', 'trim|xss_clean');
	// 		$this->form_validation->set_rules('bank_branch', 'Bank Branch', 'trim|xss_clean');

// 		$isEdit = $this->input->post('player_bank_details_id');
	// 		//var_dump($isEdit);exit();
	// 		if ($this->form_validation->run() == false) {
	// 			//$message = "Some Bank details are invalid";
	// 			$message = lang('notify.30');
	// 			$this->alertMessage(2, $message);
	// 			if (empty($isEdit)) {
	// 				$this->addEditWithdrawalBank($this->input->post('dw_bank'));
	// 			} else {
	// 				$this->addEditWithdrawalBank($this->input->post('dw_bank'), $isEdit);
	// 			}
	// 		} else {
	// 			if (empty($isEdit)) {
	// 				$data = array(
	// 					'playerId' => $this->authentication->getPlayerId(),
	// 					'bankTypeId' => $this->input->post('bank_name'),
	// 					'bankAccountNumber' => $this->input->post('bank_account_number'),
	// 					'bankAccountFullName' => $this->input->post('bank_account_fullname'),
	// 					'province' => $this->input->post('bank_province'),
	// 					'city' => $this->input->post('bank_city'),
	// 					'branch' => $this->input->post('bank_branch'),
	// 					'dwBank' => $this->input->post('dw_bank'),
	// 					'isDefault' => '0',
	// 					'isRemember' => '1',
	// 					'status' => '0',
	// 				);
	// 				$this->player_functions->addBankDetails($data);
	// 				//$message = "Bank details has successfully added";
	// 				$message = lang('notify.31');
	// 				$this->alertMessage(1, $message);
	// 			} else {
	// 				$data = array(
	// 					'bankTypeId' => $this->input->post('bank_name'),
	// 					'bankAccountNumber' => $this->input->post('bank_account_number'),
	// 					'bankAccountFullName' => $this->input->post('bank_account_fullname'),
	// 					'province' => $this->input->post('bank_province'),
	// 					'city' => $this->input->post('bank_city'),
	// 					'branch' => $this->input->post('bank_branch'),
	// 					'dwBank' => $this->input->post('dw_bank'),
	// 				);

// 				//save bank changes
	// 				$origbank = $this->player_functions->getBankDetailsById($isEdit);
	// 				$change = $this->checkBankChanges($origbank, $data);
	// 				$changes = array(
	// 					'playerBankDetailsId' => $isEdit,
	// 					'changes' => $change,
	// 					'createdOn' => date("Y-m-d H:i:s"),
	// 				);
	// 				$this->player_functions->saveBankChanges($changes);

// 				$this->player_functions->editBankDetails($isEdit, $data);
	// 				//$message = "Bank details has successfully edited";
	// 				$message = lang('notify.32');
	// 				$this->alertMessage(1, $message);
	// 			}

// 			redirect(BASEURL . 'smartcashier/bankDetails/');
	// 		}
	// 	}

// 	/**
	// 	 * view cancel promo
	// 	 *
	// 	 * @return void
	// 	 */
	// 	public function changeBankStatus($status, $bank_details_id) {
	// 		$data = array(
	// 			'status' => $status,
	// 		);
	// 		//var_dump($data);exit();
	// 		$this->player_functions->updateBankDetails($bank_details_id, $data);

// 		//save bank changes
	// 		$changes = array(
	// 			'playerBankDetailsId' => $bank_details_id,
	// 			'changes' => ($status == 0) ? 'Status: Active' : 'Status: Inactive',
	// 			'createdOn' => date("Y-m-d H:i:s"),
	// 		);
	// 		$this->player_functions->saveBankChanges($changes);

// 		$bank = $this->player_functions->getBankDetailsById($bank_details_id);
	// 		//$message = "Bank " . $bank['bankName'] . " details status has changed";
	// 		$message = $bank['bankName'] . ' ' . lang('notify.28');
	// 		$this->alertMessage(1, $message);
	// 		redirect(BASEURL . 'smartcashier/bankDetails/');
	// 	}

// 	/**
	// 	 * view delete bank details
	// 	 *
	// 	 * @return void
	// 	 */
	// 	public function deleteBankDetails($bank_details_id) {
	// 		$bank = $this->player_functions->getBankDetailsById($bank_details_id);
	// 		$this->player_functions->deleteBankDetails($bank_details_id);
	// 		//$message = "Bank " . $bank['bankName'] . " details has deleted";
	// 		$message = $bank['bankName'] . ' ' . lang('notify.29');
	// 		$this->alertMessage(1, $message);
	// 		redirect(BASEURL . 'smartcashier/bankDetails/');
	// 	}

// 	/**
	// 	 * set default bank
	// 	 *
	// 	 * @return void
	// 	 */
	// 	public function setDefaultBankDetails($bank_details_id, $dw_bank) {
	// 		$data = array(
	// 			'isDefault' => $dw_bank,
	// 			'updatedOn' => date("Y-m-d H:i:s"),
	// 		);
	// 		$this->player_functions->updateBankDetails($bank_details_id, $data);

// 		$bank = $this->player_functions->getBankDetailsById($bank_details_id);
	// 		//$message = "Bank " . $bank['bankName'] . " details status has changed";
	// 		$message = $bank['bankName'] . ' ' . lang('notify.28');
	// 		$this->alertMessage(1, $message);
	// 		redirect(BASEURL . 'smartcashier/bankDetails/');
	// 	}

// 	public function gets() {
	// 		var_dump($this->player_functions->getRandomTransactionCode());exit();
	// 	}

// 	/**
	// 	 * cashCardDeposit
	// 	 *
	// 	 * @return  rendered template
	// 	 */
	// 	public function cashCardDeposit() {
	// 		$this->form_validation->set_rules('cashCardAmount', 'Amount', 'trim|required|xss_clean|is_numeric');
	// 		$this->form_validation->set_rules('cashCardSn', 'Cash Card S/N', 'trim|required|xss_clean|is_unique[walletaccount.cashcard_sn]');
	// 		$this->form_validation->set_rules('cashCardPin', 'Cash Card PIN', 'trim|required|xss_clean|is_numeric');

// 		$cashCardAmount = $this->input->post('cashCardAmount');
	// 		$cashCardSn = $this->input->post('cashCardSn');
	// 		$cashCardPin = $this->input->post('cashCardPin');
	// 		$dwIp = $this->input->post('dw_ip');
	// 		$geolocation = $this->utils->getGeoplugin($dwIp);
	// 		$depositPromoId = $this->input->post('depositPromoId');

// 		$today = date("Y-m-d H:i:s");

// 		//get player data
	// 		$playerId = $this->authentication->getPlayerId();
	// 		$player_account = $this->player_functions->getPlayerAccountByPlayerIdOnly($playerId);

// 		if ($this->form_validation->run() == false) {
	// 			//$message = "Please make sure the information you provided is complete!";
	// 			/*$message = lang('notify.41');
	// 			$this->alertMessage(2, $message);*/
	// 			$this->makeDeposit(4);
	// 		} else {
	// 			//promotion
	// 			//$depositPromoId = $this->session->userdata('applicationPromoId');

// 			if ($depositPromoId) {
	// 				$this->verifyPromoApplication($cashCardAmount, $depositPromoId);
	// 				$this->session->unset_userdata('applicationPromoId');
	// 			}

// 			$playerPromoId = $this->session->userdata('playerPromoId');

// 			//wallet
	// 			$walletAccountData = array(
	// 				'playerAccountId' => $player_account['playerAccountId'],
	// 				'walletType' => 'Main',
	// 				'playerPromoId' => $playerPromoId,
	// 				'amount' => $cashCardAmount,
	// 				'dwMethod' => 5,
	// 				'dwStatus' => 'request',
	// 				'dwDateTime' => $today,
	// 				'transactionType' => 'deposit',
	// 				'cashcard_sn' => $cashCardSn,
	// 				'cashcard_pin' => $cashCardPin,
	// 				'dwIp' => $dwIp,
	// 				'dwLocation' => $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
	// 				'transactionCode' => $this->player_functions->getRandomTransactionCode(),
	// 				'status' => '0',
	// 			);
	// 			$this->player_functions->saveCashCardDeposit($walletAccountData);

// 			// save http_request (cookies, referer, user-agent)
	// 			$this->saveHttpRequest($this->authentication->getPlayerId(), 3);

// 			$message = $this->session->userdata('promoMessage') . " Your deposit request has been sent!";
	// 			$this->alertMessage(1, $message);
	// 			redirect(BASEURL . 'smartcashier/viewCashier');
	// 		}
	// 	}

// 	/**
	// 	 * cancelPromoApplication
	// 	 *
	// 	 *
	// 	 * @return  redered template
	// 	 */
	// 	public function cancelPromoApplication($playerpromoId, $promorulesId, $bonusAmount) {
	// 		//promo rule details
	// 		$promoDetails = $this->player_functions->getPromoDetails($promorulesId);

// 		//get required game type
	// 		$promoRuleGameType = $this->promo_functions->getPromoRuleGameType($promorulesId);
	// 		//var_dump($promoRuleGameType);exit();

// 		//get player promo start date
	// 		$playerPromoStartDate = $this->promo_functions->getPlayerPromoStartDate($playerpromoId);
	// 		//var_dump($playerPromoStartDate['date']);exit();
	// 		//get player name
	// 		$playerName = $this->authentication->getUsername();
	// 		//$playerName = 'rhaidan08';
	// 		//get game total bet
	// 		foreach ($promoRuleGameType as $key) {
	// 			//echo $key['gameType'];
	// 			$playerCurrentBetAmt[] = $this->player_functions->getPlayerCurrentBetAmt($playerName, $key['gameType'], $playerPromoStartDate['date'], date('Y-m-d H:i:s'));
	// 		}
	// 		//exit();

// 		$playerTotalBetAmount = 0;
	// 		foreach ($playerCurrentBetAmt as $key => $data) {
	// 			foreach ($data as $row) {
	// 				$playerTotalBetAmount += $row['totalbet'];
	// 			}
	// 		}

// 		if ($playerTotalBetAmount > 0) {
	// //if bet already
	// 			$message = $this->session->userdata('promoMessage') . ' ' . lang('notify.84');
	// 			$this->alertMessage(1, $message);

// 			$playerpromodata = array(
	// 				'playerpromoId' => $playerpromoId,
	// 				'dateCancelDeclined' => date('Y-m-d H:i:s'),
	// 				'cancelRequestStatus' => 2,
	// 				'declinedCancelReason' => lang('notify.84'),
	// 			);

// 			$this->player_functions->processPromoApplication($playerpromodata);

// 			redirect(BASEURL . 'smartcashier/viewCashier');
	// 		} else {

// 			//get cancellation setting
	// 			$autoApproveCancelFlag = $this->promo_functions->isAutoApproveCancel();
	// 			//var_dump($autoApproveCancelFlag);exit();

// 			if ($autoApproveCancelFlag) {
	// 				//means auto approve

// 				//get player's main wallet balance
	// 				$playerId = $this->authentication->getPlayerId();
	// 				$playerMainWalletBalance = $this->player_functions->getMainWalletBalance($playerId);
	// 				if ($playerMainWalletBalance['totalBalanceAmount'] == null) {
	// 					$playerMainWalletBalance['totalBalanceAmount'] = 0;
	// 				}
	// 				if ($bonusAmount <= $playerMainWalletBalance['totalBalanceAmount']) {
	// 					//get back the bonus from main wallet if main wallet is
	// 					$newMainwalletBal = $playerMainWalletBalance['totalBalanceAmount'] - $bonusAmount;
	// 					$playerAcctdata = array('totalBalanceAmount' => $newMainwalletBal);
	// 					$this->player_functions->setPlayerNewMainWalletBalAmount($playerId, $playerAcctdata);

// 					//save to history
	// 					$data = array('playerId' => $playerId,
	// 						'walletType' => '0', //mainwallet
	// 						'currentBalance' => $playerMainWalletBalance['totalBalanceAmount'],
	// 						'newBalance' => $newMainwalletBal,
	// 						'reason' => lang('notify.87'),
	// 						'adjustedOn' => date('Y-m-d H:i:s'),
	// 						'adjustedBy' => 1, //admin or system
	// 					);
	// 					$this->player_functions->saveToAdjustmentHistory($data);

// 					$playerpromodata = array(
	// 						'playerpromoId' => $playerpromoId,
	// 						'cancelRequestDate' => date('Y-m-d H:i:s'),
	// 						'cancelRequestStatus' => 3,
	// 						'transactionStatus' => 3,
	// 					);

// 					$this->player_functions->processPromoApplication($playerpromodata);

// 					//You're promo is cancelled now..
	// 					$message = $this->session->userdata('promoMessage') . ' ' . lang('notify.86');
	// 					$this->alertMessage(1, $message);

// 					redirect(BASEURL . 'smartcashier/viewCashier');
	// 				} else {

// 					$playerSubWalletBalance = $this->player_functions->getSubWalletBalance($playerId);
	// 					//var_dump($playerSubWalletBalance);exit();
	// 					foreach ($playerSubWalletBalance as $key) {
	// 						if ($key['typeId'] == 1) {
	// 							//pt
	// 							$ptSubWalletCurrentBal = $key['totalBalanceAmount'];
	// 						} else {
	// 							//ag
	// 							$agSubWalletCurrentBal = $key['totalBalanceAmount'];
	// 						}
	// 					}

// 					//var_dump($ptSubWalletCurrentBal);exit();
	// 					if ($bonusAmount <= $ptSubWalletCurrentBal) {
	// 						$newPTBalance = $ptSubWalletCurrentBal - $bonusAmount;
	// 						//var_dump($newPTBalance);exit();
	// 						$playerAcctdata = array('totalBalanceAmount' => $newPTBalance);
	// 						//var_dump($playerAcctdata);exit();
	// 						$this->player_functions->setPlayerNewSubWalletBalAmount($playerId, $playerAcctdata, 1);
	// 						$result = $this->withdrawalToPT($bonusAmount, $playerId);
	// 						if ($result == 'Withdraw OK') {
	// 							//$this->depositToPT($subwalletNewBal,$playerId);

// 							//save to history
	// 							$data = array('playerId' => $playerId,
	// 								'walletType' => '1', //ptsubwallet
	// 								'currentBalance' => $ptSubWalletCurrentBal,
	// 								'newBalance' => $newPTBalance,
	// 								'reason' => lang('notify.87'),
	// 								'adjustedOn' => date('Y-m-d H:i:s'),
	// 								'adjustedBy' => 1,
	// 							);
	// 							$this->player_functions->saveToAdjustmentHistory($data);

// 							$playerpromodata = array(
	// 								'playerpromoId' => $playerpromoId,
	// 								'cancelRequestDate' => date('Y-m-d H:i:s'),
	// 								'cancelRequestStatus' => 3,
	// 								'transactionStatus' => 3,
	// 							);

// 							$this->player_functions->processPromoApplication($playerpromodata);

// 							//You're promo is cancelled now..
	// 							$message = $this->session->userdata('promoMessage') . ' ' . lang('notify.86');
	// 							$this->alertMessage(1, $message);
	// 						} else {
	// 							//Cancellation failed due to api connection failure, please try again later!
	// 							$message = $this->session->userdata('promoMessage') . ' ' . lang('notify.86');
	// 							$this->alertMessage(2, $message);
	// 						}
	// 					} elseif ($bonusAmount <= $agSubWalletCurrentBal) {
	// 						$newAGBalance = $agSubWalletCurrentBal - $bonusAmount;
	// 						$playerAcctdata = array('totalBalanceAmount' => $newAGBalance);
	// 						//var_dump($playerAcctdata);exit();
	// 						$this->player_functions->setPlayerNewSubWalletBalAmount($playerId, $playerAcctdata, 2);

// 						$result = $this->withdrawalToAG($bonusAmount, $playerId, $playerName);
	// 						//var_dump($result); exit();
	// 						if ($result == 'Withdraw OK') {

// 							//save to history
	// 							$data = array('playerId' => $playerId,
	// 								'walletType' => '2', //agsubwallet
	// 								'currentBalance' => $agSubWalletCurrentBal,
	// 								'newBalance' => $newAGBalance,
	// 								'reason' => lang('notify.87'),
	// 								'adjustedOn' => date('Y-m-d H:i:s'),
	// 								'adjustedBy' => $this->authentication->getUserId(),
	// 							);
	// 							$this->player_functions->saveToAdjustmentHistory($data);

// 							$this->saveAction('Adjusted AG Wallet Balance', "User " . $this->authentication->getUsername() . " has successfully adust ag sub wallet balance of player id: " . $playerId . ".");
	// 						} else {
	// 							//Cancellation failed due to api connection failure, please try again later!
	// 							$message = $this->session->userdata('promoMessage') . ' ' . lang('notify.86');
	// 							$this->alertMessage(2, $message);
	// 						}
	// 					} else {
	// 						//Cancellation failed due to api connection failure, please try again later!
	// 						$message = $this->session->userdata('promoMessage') . ' ' . lang('notify.86');
	// 						$this->alertMessage(1, $message);
	// 					}
	// 				}

// 			} else {
	// 				//manual, need to check by admin
	// 				$playerpromodata = array(
	// 					'playerpromoId' => $playerpromoId,
	// 					'cancelRequestDate' => date('Y-m-d H:i:s'),
	// 					'cancelRequestStatus' => 1,
	// 				);

// 				$this->player_functions->processPromoApplication($playerpromodata);

// 				//You're promo cancellation request has been sent!
	// 				$message = $this->session->userdata('promoMessage') . ' ' . lang('notify.85');
	// 				$this->alertMessage(1, $message);
	// 			}

// 			redirect(BASEURL . 'smartcashier/viewCashier');
	// 		}

// 	}

// 	/**
	// 	 * Sending Email for Promotion Only
	// 	 *
	// 	 * @return  rendered Template
	// 	 */
	// 	public function sendEmail($player_id, $playerPromoId) {

// 		$player_info = $this->player_functions->getPlayerById($player_id);
	// 		$random_verification_code = $this->player_functions->getPlayerById($player_id)['verify'] . $playerPromoId;
	// 		$url = BASEURL . 'smartcashier/verify/';
	// 		$email = $this->player_functions->getEmailAddSender(); // Email Address Sender

// 		// Promo Email Template
	// 		$emailTemplate = $this->email_setting->getEmailTemplatePromo('email_promotion_template');
	// 		$str_replace1 = str_replace("[playername]", $player_info['lastName'] . ' ' . $player_info['firstName'], $emailTemplate);
	// 		$str_replace2 = str_replace("[random_code]", $random_verification_code, $str_replace1);

// 		$body = '<html><body>' . $str_replace2['template'] . '</body></html>';

// 		$data = array(
	// 			'email_sender' => $email['email'],
	// 			'email_sender_pass' => $this->salt->decrypt($email['password'], DESKEY_OG),
	// 			'subject' => 'Welcome to Paramount',
	// 			'from' => $email['email'],
	// 			'from_name' => 'Paramount TEAM',
	// 			'body' => $body,
	// 		);

// 		$this->email_setting->sendEmail($player_info['email'], $data);
	// 	}

// 	/**
	// 	 * Verify Email for Promotion and Bonus Release
	// 	 *
	// 	 * @return  rendered Template
	// 	 */
	// 	public function verify($verification_code) {

// 		$result = $this->email_setting->verifyEmail(substr($verification_code, 32, 32));
	// 		if ($result) {
	// 			$message = lang('notify.94');
	// 			$this->alertMessage(2, $message);
	// 		} else {
	// 			// Verifies the promo and change verificationStatus to 1
	// 			$data['verificationStatus'] = 1;
	// 			$this->email_setting->editVerificationStatus(substr($verification_code, 32, 32), $data);

// 			// Release bonus if bonusReleasToPlayer is automatic
	// 			$playerPromoDetails = $this->player_functions->checkPlayerPromo(substr($verification_code, 32, 32));
	// 			$promoRulesDetails = $this->player_functions->getPromoDetails($playerPromoDetails['promorulesId']);

// 			// Adjust Balance if promo rules is automatic
	// 			if ($promoRulesDetails[0]['bonusReleaseToPlayer'] == 0) {
	// 				// Updates playerpromo table and set transactionStatus to 1 (approved)
	// 				$playerpromodata = array(
	// 					'playerpromoId' => substr($verification_code, 32, 32),
	// 					'processedBy' => null,
	// 					'dateProcessed' => date('Y-m-d H:i:s'),
	// 					'transactionStatus' => 1,
	// 				);
	// 				$this->player_functions->processPromoApplication($playerpromodata);

// 				// Adjust Balance
	// 				$mainwallet = $this->player_functions->getMainWalletBalance($playerPromoDetails['playerId']);
	// 				$currentMainwalletBal = $mainwallet['totalBalanceAmount'];
	// 				$newMainwalletBal = $currentMainwalletBal + $playerPromoDetails['bonusAmount'];
	// 				$playerAcctdata = array('totalBalanceAmount' => $newMainwalletBal);
	// 				$this->player_functions->setPlayerNewMainWalletBalAmount($playerPromoDetails['playerId'], $playerAcctdata);
	// 			}

// 			$message = lang('notify.95');
	// 			$this->alertMessage(2, $message);
	// 		}
	// 		redirect(BASEURL);
	// 	}

// 	private function getRandomSequence() {
	// 		$seed = str_split('0123456789123456'); // and any other characters
	// 		shuffle($seed); // probably optional since array_is randomized; this may be redundant
	// 		$randomNum = '';
	// 		foreach (array_rand($seed, 16) as $k) {
	// 			$randomNum .= $seed[$k];
	// 		}

// 		return $randomNum;
	// 	}

// 	/**
	// 	 * Save http Request
	// 	 *
	// 	 * @return  rendered Template
	// 	 */
	// 	public function saveHttpRequest($player_id, $type) {
	// 		$this->load->library(array('user_agent', 'duplicate_account'));

// 		$headers = $this->input->request_headers();

// 		$data = array(
	// 			"playerId" => $player_id,
	// 			"ip" => $this->input->ip_address(),
	// 			"cookie" => $headers['Cookie'],
	// 			"referrer" => ($this->agent->is_referral() == TRUE) ? $this->agent->referrer() : ' ',
	// 			"user_agent" => $headers['User-agent'],
	// 			"os" => $this->agent->platform(),
	// 			"device" => ($this->agent->is_mobile() == TRUE) ? $this->agent->mobile() : $this->agent->browser() . " " . $this->agent->version(),
	// 			"is_mobile" => ($this->agent->is_mobile() == TRUE) ? 1 : 0,
	// 			"type" => $type,
	// 			"createdat" => date("Y-m-d H:i:s"),
	// 		);

// 		if ($this->duplicate_account->existingHttpRequest($data)) {
	// 			$this->duplicate_account->insertHttpRequest($data);
	// 		}

// 	}
}