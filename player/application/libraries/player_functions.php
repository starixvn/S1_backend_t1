<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Fe_Functions
 *
 * Fe_Functions library for FHI OG System
 *
 * @package     Fe_Functions
 * @author      Rendell Nuñez
 * @version     1.0.0
 */

class Player_Functions {

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('salt', 'session', 'transactions_library'));
		$this->ci->load->model(array('player_model', 'player', 'gameapi'));
	}

	protected function getDeskeyOG() {
		return $this->ci->config->item('DESKEY_OG');
	}

	/**
	 * Adds user to the database
	 *
	 * @return  boolean
	 */
	function register($data) {
		/*$hasher = new PasswordHash('8', TRUE);
		$data['password'] = $hasher->HashPassword($data['password']);*/
		$data['password'] = $this->ci->salt->encrypt($data['password'], $this->getDeskeyOG());

		$result = $this->ci->player->insertUser($data);
		return $result;
	}

	public function randomizer($username) {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789!@#$%^&*()'
			. $username); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$randomPassword = '';
		foreach (array_rand($seed, 9) as $k) {
			$randomPassword .= $seed[$k];
		}

		return $randomPassword;
	}

	public function generateReferralCode($player_id) {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$referral_code = '';
		foreach (array_rand($seed, 5) as $k) {
			$referral_code .= $seed[$k];
		}

		return $player_id . $referral_code . "OG";
	}

	public function getRandomVerificationCode() {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$verification_code = '';
		foreach (array_rand($seed, 32) as $k) {
			$verification_code .= $seed[$k];
		}

		return $verification_code;
	}

	public function getRandomTransactionCode() {
		$max = $this->ci->player->getMaxNumberOfWalletAccount();
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. '0123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$transaction_code = '';
		foreach (array_rand($seed, 14) as $k) {
			$transaction_code .= $seed[$k];
		}

		return $transaction_code;
	}

	/**
	 * Will check if the username is already existing
	 *
	 * @param   string
	 * @return  array
	 */
	public function checkIfReferralCodeExist($referral_code) {
		$result = $this->ci->player->checkIfReferralCodeExist($referral_code);
		return $result;
	}

	/**
	 * Will check if the username affilate is already existing
	 *
	 * @param   string
	 * @return  array
	 */
	public function checkIfAffiliateCodeExist($affiliate_code) {
		$result = $this->ci->player->checkIfAffiliateCodeExist($affiliate_code);
		return $result;
	}

	/**
	 * Will create new player details using the parameter data
	 *
	 * @param   array
	 */
	// public function createPlayerReferral($data) {
	// 	return $this->ci->player->createPlayerReferral($data);
	// }

	/**
	 * Will create new player details using the parameter data
	 *
	 * @param   array
	 */
	// public function createPlayerReferralDetails($data) {
	// 	$this->ci->player->createPlayerReferralDetails($data);
	// }

	/**
	 * Will check if the username is already existing
	 *
	 * @param   string
	 * @return  array
	 */
	public function checkUsernameExist($username) {
		$result = $this->ci->player->checkUsernameExist($username);
		return $result;
	}

	/**
	 * Will check if the email is already existing
	 *
	 * @param   string
	 * @return  array
	 */
	public function checkEmailExist($email) {
		$result = $this->ci->player->checkEmailExist($email);
		return $result;
	}

	/**
	 * Will check if the Contact Number already exists
	 * One contact number should not register for more than one account
	 *
	 * @param   string
	 * @return  array
	 */
	public function checkContactExist($contactNumber) {
		$result = $this->ci->player->checkContactExist($contactNumber);
		return $result;
	}

	/**
	 * Will create new player using the parameter data
	 * MOVED TO player_model
	 * @param   array
	 */
	public function insertPlayer($data) {
		/*$hasher = new PasswordHash('8', TRUE);
		$data['password'] = $hasher->HashPassword($data['password']);*/
		// $data['password'] = $this->ci->salt->encrypt($data['password'], DESKEY_OG);
		$this->ci->load->model(array('player_model'));
		$this->ci->player_model->insertPlayer($data);
		// $this->ci->player->insertPlayer($data);
	}

	/**
	 * Will create new player using the parameter data
	 *
	 * @param   array
	 */
	public function editPlayer($data, $player_id) {
		$this->ci->player->editPlayer($data, $player_id);
	}

	/**
	 * compare changes
	 *
	 * @param   array
	 * @return  boolean
	 */
	public function compareChanges($data, $player_id) {
		$current_player_details = $this->ci->player->getPlayerDetails($player_id);
		$changes = array();

		foreach ($data as $key => $value) {
			if ($value[$key] != $current_player_details[$key]) {
				array_push($changes, $key);
			}
		}

		print_r($changes);
		exit();
	}

	/**
	 * Will create new player details using the parameter data
	 *
	 * @param   array
	 */
	public function insertPlayerDetails($data) {
		$this->ci->player->insertPlayerDetails($data);
	}

	/**
	 * Will create new player details using the parameter data
	 *
	 * @param   array
	 */
	public function editPlayerDetails($data, $player_id) {
		$this->ci->player->editPlayerDetails($data, $player_id);
	}

    /**
     * Will create new player details using the parameter data
     *
     * @param   array
     */
    public function editPlayerEmail($data, $player_id) {
        return $this->ci->player->editPlayerEmail($data, $player_id);
    }

	/**
	 * Will get player account given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerAccount($player_id) {
		$result = $this->ci->player->getPlayerAccount($player_id);
		return $result;
	}

	/**
	 * getPlayerTotalDailyDeposit
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerTotalDailyDeposit($player_id) {
		$result = $this->ci->player->getPlayerTotalDailyDeposit($player_id);
		return $result;
	}

	/**
	 * Will get player main wallet at given playerId
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerMainWallet($player_id) {
		$result = $this->ci->player->getPlayerMainWallet($player_id);
		return $result;
	}

	/**
	 * Will get player account given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerAccountByPlayerId($player_id) {
		$result = $this->ci->player->getPlayerAccountByPlayerId($player_id);
		return $result;
	}

	/**
	 * Will get player account given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerAccountByPlayerIdOnly($player_id) {
		$result = $this->ci->player->getPlayerAccountByPlayerIdOnly($player_id);
		return $result;
	}

	/**
	 * Will get player account given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerAccountDetails($player_id) {
		$result = $this->ci->player->getPlayerAccountDetails($player_id);
		return $result;
	}

	/**
	 * Will get player account given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerAccountHistory($player_id) {
		$result = $this->ci->player->getPlayerAccountHistory($player_id);
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerById($player_id) {
		$result = $this->ci->player->getPlayerById($player_id);
		return $result;
	}

	/**
	 * Will get player username
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerUsername($player_id) {
		$result = $this->ci->player->getPlayerUsername($player_id);
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function createAccount($data) {
		$this->ci->player->createAccount($data);
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function createAccountDetails($data) {
		$this->ci->player->createAccountDetails($data);
	}

	/**
	 * Will deposit manual 3rd party deposit
	 *
	 * @param   int
	 * @return  array
	 */
	public function manual3rdPartyDeposit($walletAccountData, $manual3rdPartyDepositDetails) {
		$this->ci->player->manual3rdPartyDeposit($walletAccountData, $manual3rdPartyDepositDetails);
	}

	/**
	 * Will deposit local bank
	 *
	 * @param   int
	 * @return  array
	 */
	public function localBankDeposit($walletAccountData, $localBankDepositDetails) {
		$this->ci->player->localBankDeposit($walletAccountData, $localBankDepositDetails);
	}

	/**
	 * Will withdraw local bank
	 *
	 * @param   int
	 * @return  array
	 */
	public function localBankWithdrawal($walletAccountData, $localBankDepositDetails, $playerId) {
		$this->ci->player->localBankWithdrawal($walletAccountData, $localBankDepositDetails, $playerId);
	}

	/**
	 * Will deposit local bank deposit
	 *
	 * @param   playerId
	 * @return  array
	 */
	public function setPlayerBankaccountUndefault($playerBankDetailsId, $data) {
		$this->ci->player->setPlayerBankaccountUndefault($playerBankDetailsId, $data);
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function createAccountHistory($data) {
		$this->ci->player->createAccountHistory($data);
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function updatePlayerAccount($player_id, $player_account_id, $data) {
		$this->ci->player->updatePlayerAccount($player_id, $player_account_id, $data);
	}

	/**
	 * Will update 3rd party deposit request
	 *
	 * @param   int
	 * @return  array
	 */
	public function update3rdPartyDepositRequest($walletAccountId, $data) {
		$this->ci->player->update3rdPartyDepositRequest($walletAccountId, $data);
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerTotalBalance($player_id) {
		$result = $this->ci->player->getPlayerTotalBalance($player_id);
		return $result;
	}

	/**
	 * Will get player main wallet balance
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerMainWalletBalance($player_id) {
		$result = $this->ci->player->getPlayerMainWalletBalance($player_id);
		return $result;
	}

	/**
	 * Will get player main wallet balance
	 *
	 * @param   int
	 * @return  array
	 */
	public function getMainWalletBalance($player_id) {
		$result = $this->ci->player->getMainWalletBalance($player_id);
		return $result;
	}

	/**
	 * Will get player sub wallet balance
	 *
	 * @param   int
	 * @return  array
	 */
	public function getSubWalletBalance($player_id) {
		$result = $this->ci->player->getSubWalletBalance($player_id);
		return $result;
	}

	/**
	 * Will set new balance amount
	 *
	 * @param   $playerId int
	 * @param   $data array
	 * @return  Boolean
	 */
	public function setPlayerNewMainWalletBalAmount($playerId, $data) {
		return $this->ci->player->setPlayerNewMainWalletBalAmount($playerId, $data);
	}

	/**
	 * Will set new balance amount
	 *
	 * @param   $playerId int
	 * @param   $data array
	 * @return  Boolean
	 */
	public function saveToAdjustmentHistory($data) {
		return $this->ci->player->saveToAdjustmentHistory($data);
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getRankingLevelSettingByPlayerLevel($player_level) {
		$result = $this->ci->player->getRankingLevelSettingByPlayerLevel($player_level);
		return $result;
	}

	function get_age($birth_date) {
		return floor((time() - strtotime($birth_date)) / 31556926);
	}

	/**
	 * Get error message.
	 * Can be invoked after any failed operation such as login or register.
	 *
	 * @return  string
	 */
	public function checkPassword($password, $playerPassword) {
		/*$hasher = new PasswordHash('8', TRUE);
		return $hasher->CheckPassword($password, $playerPassword);*/
		return ($this->ci->salt->decrypt($playerPassword, $this->getDeskeyOG()) == $password);
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getRequestWithdrawal($player_id) {
		$result = $this->ci->player->getRequestWithdrawal($player_id);
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function changeDWStatus($data, $wallet_account_id) {
		$this->ci->player->changeDWStatus($data, $wallet_account_id);
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function updateCurrentMoney($data, $player_id) {
		$this->ci->player->updateCurrentMoney($data, $player_id);
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerFriendReferralId($player_id, $invited_player_id) {
		$result = $this->ci->player->getPlayerFriendReferralId($player_id, $invited_player_id);
		return $result;
	}

	/**
	 * Will get player friend referrals
	 *
	 * @param   int
	 * @return  array
	 */
	public function getReferralByPlayerId($player_id) {
		$result = $this->ci->player->getReferralByPlayerId($player_id);
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getAllTransactionHistoryByPlayerId($player_id) {
		$result = $this->ci->player->getAllTransactionHistoryByPlayerId($player_id);
		return $result;
	}

	/**
	 * Will get all bank type
	 *
	 * @param   int
	 * @return  array
	 */
	public function getAllBankType() {
		$result = $this->ci->player->getAllBankType();
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getOtcById($otc_payment_method_id) {
		$result = $this->ci->player->getOtcById($otc_payment_method_id);
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function insertOtcPaymentMethodDeatils($data) {
		$result = $this->ci->player->insertOtcPaymentMethodDeatils($data);
		return $result;
	}

	public function getTransactionId() {
		$seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$transaction_id = '';
		foreach (array_rand($seed, 18) as $k) {
			$transaction_id .= $seed[$k];
		}

		return $transaction_id;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getWalletAccountByPlayerId($player_id) {
		$result = $this->ci->player->getWalletAccountByPlayerId($player_id);
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function checkPromoCodeExist($promo_code) {
		$result = $this->ci->player->checkPromoCodeExist($promo_code);
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function checkIfAlreadyGetPromo($player_id, $promo_id) {
		$result = $this->ci->player->checkIfAlreadyGetPromo($player_id, $promo_id);
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function checkIfExpiredPromo($player_id, $promo_id) {
		$result = $this->ci->player->checkIfExpiredPromo($player_id, $promo_id);
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function processPromo($promo_id, $amount, $player_id) {
		$promo_details = $this->ci->player->retrievePromo($promo_id);

		$checkIfAlreadyGetPromo = $this->checkIfAlreadyGetPromo($player_id);

		if ($checkIfAlreadyGetPromo) {
			$promo_details = $this->ci->player->retrievePromo($checkIfAlreadyGetPromo['promoId']);
		}

		$player = $this->ci->player->getPlayerById($player_id);
		$today = date("F j, Y g:i A");
		$data = array();
		$totalBetAmount = "";
		$expiration_date = "";
		$promo_amount = "";
		$status = "";
		$message = "";
		if ($amount >= $promo_details['rules'][0]['in']) {
			if ($this->checkInrange($promo_details['period']['start'], $promo_details['period']['end'], $today)) {
				//if($checkIfAlreadyGetPromo['nthDepositCount'] < $promo_details['nthDeposit'] && $checkIfAlreadyGetPromo['status'] != 0) {
				// if($promo_details['rules'][0]['isOutPercent'] == "1") {
				//     $promo_amount = ($promo_details['rules'][0]['out'] / 100) * $amount;
				// } elseif($promo_details['rules'][0]['isOutPercent'] == "0") {
				//     $promo_amount = $promo_details['rules'][0]['out'];
				// }

				// if($promo_amount > $promo_details['bonus']['max']) {
				//     $promo_amount = $promo_details['bonus']['max'];
				// }

				// $totalBetAmount = ($promo_amount + $amount) * $promo_details['bonus']['modifier'];

				// if($promo_details['period']['code'] == 0) {
				//     $expiration_date = date("Y-m-d H:i:s", strtotime('+1 day'));
				// } elseif($promo_details['period']['code'] == 1) {
				//     $expiration_date = date("Y-m-d H:i:s", strtotime('+1 week'));
				// } elseif($promo_details['period']['code'] == 2) {
				//     $expiration_date = date("Y-m-d H:i:s", strtotime('+1 month'));
				// } elseif($promo_details['period']['code'] == 3) {
				//     $expiration_date = date("Y-m-d H:i:s", strtotime('+1 year'));
				// }

				// $message = "You have a bonus of <b>" . $promo_amount . " " . $promo_details['currency']['code'] . "</b> by depositing <b>" . $amount . " " . $player['currency'] . "</b>.";
				// $status = 'update';

				// $data = array(
				//         'promoId' => $checkIfAlreadyGetPromo['promoId'],
				//         'nthDepositCount' => $checkIfAlreadyGetPromo['nthDepositCount'] + 1,
				//         'totalBetAmount' => $totalBetAmount,
				//         'bonusAmount' => $promo_amount,
				//         'message' => $message,
				//         'status' => $status
				//     );

				//} elseif(!$checkIfAlreadyGetPromo) {
				if ($promo_details['rules'][0]['isOutPercent'] == "1") {
					$promo_amount = ($promo_details['rules'][0]['out'] / 100) * $amount;
				} elseif ($promo_details['rules'][0]['isOutPercent'] == "0") {
					$promo_amount = $promo_details['rules'][0]['out'];
				}

				if ($promo_amount > $promo_details['bonus']['max']) {
					$promo_amount = $promo_details['bonus']['max'];
				}

				$totalBetAmount = ($promo_amount + $amount) * $promo_details['bonus']['modifier'];

				if ($promo_details['period']['code'] == 0) {
					$expiration_date = date("Y-m-d H:i:s", strtotime('+1 day'));
				} elseif ($promo_details['period']['code'] == 1) {
					$expiration_date = date("Y-m-d H:i:s", strtotime('+1 week'));
				} elseif ($promo_details['period']['code'] == 2) {
					$expiration_date = date("Y-m-d H:i:s", strtotime('+1 month'));
				} elseif ($promo_details['period']['code'] == 3) {
					$expiration_date = date("Y-m-d H:i:s", strtotime('+1 year'));
				}

				$message = "You have a bonus of <b>" . $promo_amount . " " . $player['currency'] . "</b> by depositing <b>" . $amount . " " . $player['currency'] . "</b>.";
				$status = 'success';

				$data = array(
					'playerId' => $player_id,
					'promoId' => $promo_details['id'],
					'expiration' => $expiration_date,
					'transactionType' => $promo_details['type']['name'],
					'currency' => $promo_details['currency']['code'],
					'totalBetAmount' => $totalBetAmount,
					'bonusAmount' => $promo_amount,
					'message' => $message,
					'status' => $status,
				);

				// } else {
				//    $message = "You still have pending request or your have reached the maximun number of deposit.";
				//    $status = 'fail';

				//    $data = array(
				//          'message' => $message,
				//           'status' => $status
				//       );
				//}
			} else {
				$message = "Promo is already ended.";
				$status = 'fail';

				$data = array(
					'message' => $message,
					'status' => $status,
				);
			}
		} else {
			$message = "You did not meet the minimum reqiurement amount to be deposit.";
			$status = 'fail';

			$data = array(
				'message' => $message,
				'status' => $status,
			);
		}
		return $data;
	}

	function checkInrange($dt_start, $dt_end, $dt_check) {
		if (strtotime($dt_check) > strtotime($dt_start) && strtotime($dt_check) < strtotime($dt_end)) {
			return "true";
		}

		return "false";
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function createPlayerPromo($data) {
		$result = $this->ci->player->createPlayerPromo($data);
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function updatePlayerPromo($data, $player_id, $promo_id) {
		$result = $this->ci->player->updatePlayerPromo($data, $player_id, $promo_id);
		return $result;
	}

	/**
	 * Will update thirdparty deposit
	 *
	 * @param   int
	 * @return  array
	 */
	public function updateThirdpartyDeposit($data, $depositWalletAccountId) {
		$result = $this->ci->player->updateThirdpartyDeposit($data, $depositWalletAccountId);
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function retrievePromo($promo_id) {
		$result = $this->ci->player->retrievePromo($promo_id);
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPromoCode($promo_id) {
		$result = $this->ci->player->getPromoCode($promo_id);
		return $result;
	}

	/**
	 * Will get player given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerTotalBonus($player_id) {
		$result = $this->ci->player->getPlayerTotalBonus($player_id);
		return $result;
	}

	////////////////////////////______________________________________________________________\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	function promoRange($promo_start_date, $promo_end_date, $today) {
		if (strtotime($today) > strtotime($promo_start_date) && strtotime($today) < strtotime($promo_end_date)) {
			return true;
		}

		return false;
	}

	function promoConditionRange($condition_start_date, $condition_end_date, $today) {
		if (strtotime($today) > strtotime($condition_start_date) && strtotime($today) < strtotime($condition_end_date)) {
			return true;
		}

		return false;
	}

	function promoLevelMinMaxRange($min, $max, $amount) {
		if ($amount > $min && $amount < $max) {
			return true;
		}

		return false;
	}

	function promoExpirationdate($promo_period) {
		$expiration_date = "";

		switch ($promo_period) {

			case '0':$expiration_date = date("Y-m-d H:i:s", strtotime('+1 day'));
				break;
			case '1':$expiration_date = date("Y-m-d H:i:s", strtotime('+1 week'));
				break;
			case '2':$expiration_date = date("Y-m-d H:i:s", strtotime('+1 month'));
				break;
			case '3':$expiration_date = date("Y-m-d H:i:s", strtotime('+1 year'));
				break;

			default:$message = "Invalid promo period!";
				break;

		}
		return $expiration_date;
	}

	function checkIfFirstDeposit($player_id) {
		return $this->ci->player->checkIfFirstDeposit($player_id);
	}

	//main
	function playerPromo($player_id, $promo_id, $amount) {
		$today = date("Y-m-d H:i:s");
		$data = array();

		$message = "";
		$bonus = "";
		$expiration_date = "";
		$needed = "";
		$status = "";
		$isInRule = false;
		$pass_fail = "fail";
		$sorted_rules = array();

		$promo_details = $this->ci->player->retrievePromo($promo_id);
		// echo "<pre>";
		// print_r($promo_details);
		// echo "</pre>";

		$player_account = $this->ci->player->getPlayerAccountByPlayerId($player_id);
		$count_nth = count($this->ci->player->getPlayerPromoNth($player_id, $promo_id));
		$checkIfExpiredPromo = $this->checkIfExpiredPromo($player_id, $promo_id);
		$checkIfAlreadyGetPromo = $this->checkIfAlreadyGetPromo($player_id, $promo_id);

		if ($this->promoRange($promo_details['period']['start'], $promo_details['period']['end'], $today)) {
			$sorted_rules = $promo_details['rules'];

			// usort($sorted_rules, function ($sorted_rules, $sorted_rules) {
			// 	if ($sorted_rules == $sorted_rules) {
			// 		return 0;
			// 	}

			// 	return $sorted_rules < $sorted_rules ? 1 : -1;
			// });

			if (!empty($checkIfAlreadyGetPromo) && $checkIfAlreadyGetPromo['status'] == 0) {
				$message = "You have pending request.";
			} elseif (!empty($checkIfAlreadyGetPromo) && $checkIfAlreadyGetPromo['status'] > 0 && $checkIfAlreadyGetPromo['status'] != 6) {
				if ($promo_details['type']['code'] == 0) {
					if ($promo_details['condition']['code'] == 0) {
						//if Nth
						//check promo rule

						foreach ($sorted_rules as $row) {
							if ($amount >= $row['in']) {
								$bonus = ($row['isOutPercent'] == 1) ? ($bonus = ($row['out'] / 100) * $amount) : ($bonus = $row['out']);
								$isInRule = true;
								break;
							} else {
								$isInRule = false;
							}
						}

						if ($isInRule == true) {
							if ($bonus > $promo_details['bonus']['maximum']) {
								$bonus = $promo_details['bonus']['maximum'];
							} elseif ($bonus < $promo_details['bonus']['minimum']) {
								$bonus = $promo_details['bonus']['minimum'];
							}

							if ($promo_details['bonus']['release']['code'] == 0) {
								$needed = ($amount + $bonus) * $promo_details['bonus']['release']['value'];
							} elseif ($promo_details['bonus']['release']['code'] == 0) {
								$needed = $promo_details['bonus']['release']['value'];
							}

							if ($promo_details['condition']['value'] > $count_nth) {
								$data = array(
									'playerId' => $player_id,
									'promoId' => $promo_id,
									'conditionType' => $promo_details['condition']['name'],
									'nthDepositCount' => $count_nth + 1,
									'transactionType' => $promo_details['requirements']['name'],
									'totalBetAmount' => $needed,
									'bonusAmount' => $bonus,
									'currency' => $player_account['currency'],
									'status' => 0,
								);

								$this->ci->player->createPlayerPromo($data);
								echo $message = "You have a bonus of <b>" . $bonus . " " . $promo_details['currency']['code'] . "</b> by depositing <b>" . $amount . " " . $player['currency'] . "</b>.";
								$pass_fail = "success";
							} elseif ($checkIfAlreadyGetPromo['status'] < 3) {
								echo $message = "Your promo has expired.";
								$pass_fail = "fail";
								$status = array('status' => 3);
								$this->ci->player->updatePlayerPromo($status, $player_id, $promo_id);
							} else {
								echo $message = "Promo expired.";
								$pass_fail = "fail";
							}
						} else {
							echo $message = "You did not meet the minimum requirement for this promo.";
							$pass_fail = "fail";
						}

					} elseif ($promo_details['condition']['code'] == 1) {
						//if Total
						if ($this->promoConditionRange($promo_details['condition']['start'], $promo_details['condition']['end'], $today)) {
							if ($promo_details['requirements']['code'] == 0) {
								//summation of deposit
								$summation = $this->ci->player->getTotalDepositFrom(date("Y-m-d H:i:s", strtotime($promo_details['condition']['start'])), $today, $player_account['playerAccountId']);
								$amount = $summation['deposit'] + $amount;
							} elseif ($promo_details['requirements']['code'] == 1) {
								//summation of bet
							}
							print_r($summation);
							foreach ($sorted_rules as $row) {
								if ($amount >= $row['in']) {
									$bonus = ($row['isOutPercent'] == 1) ? ($bonus = ($row['out'] / 100) * $amount) : ($bonus = $row['out']);
									$isInRule = true;
									break;
								} else {
									$isInRule = false;
								}
							}

							if ($isInRule == true) {
								if ($bonus > $promo_details['bonus']['maximum']) {
									$bonus = $promo_details['bonus']['maximum'];
								} elseif ($bonus < $promo_details['bonus']['minimum']) {
									$bonus = $promo_details['bonus']['minimum'];
								}

								if ($promo_details['bonus']['release']['code'] == 0) {
									$needed = ($amount + $bonus) * $promo_details['bonus']['release']['value'];
								} elseif ($promo_details['bonus']['release']['code'] == 0) {
									$needed = $promo_details['bonus']['release']['value'];
								}

								$data = array(
									'playerId' => $player_id,
									'promoId' => $promo_id,
									'conditionType' => $promo_details['condition']['name'],
									'nthDepositCount' => NULL,
									'transactionType' => $promo_details['requirements']['name'],
									'totalBetAmount' => $needed,
									'bonusAmount' => $bonus,
									'currency' => $player_account['currency'],
									'status' => 0,
								);

								$this->ci->player->createPlayerPromo($data);
								$message = "You have a bonus of <b>" . $bonus . " " . $promo_details['currency']['code'] . "</b> by depositing <b>" . $amount . " " . $player['currency'] . "</b>.";
								$pass_fail = "success";
							} elseif ($checkIfAlreadyGetPromo['status'] < 3) {
								$message = "Your promo has expired.";
								$pass_fail = "fail";
								$status = array('status' => 3);
								$this->ci->player->updatePlayerPromo($status, $player_id, $promo_id);
							} else {
								$message = "Promo expired.";
								$pass_fail = "fail";
							}
						} else {
							echo $message = "You did not meet the minimum requirement for this promo.";
							$pass_fail = "fail";
						}
					}

				} elseif ($promo_details['type']['code'] == 1) {
					if ($this->promoConditionRange($promo_details['condition']['start'], $promo_details['condition']['end'], $today)) {
						switch ($promo_details['period']['code']) {

							case '0':$expiration_date = date("Y-m-d H:i:s", strtotime($checkIfAlreadyGetPromo['dateActivate'] . '+1 day'));
								break;
							case '1':$expiration_date = date("Y-m-d H:i:s", strtotime($checkIfAlreadyGetPromo['dateActivate'] . '+1 week'));
								break;
							case '2':$expiration_date = date("Y-m-d H:i:s", strtotime($checkIfAlreadyGetPromo['dateActivate'] . '+1 month'));
								break;
							case '3':$expiration_date = date("Y-m-d H:i:s", strtotime($checkIfAlreadyGetPromo['dateActivate'] . '+1 year'));
								break;

							default:$message = "Invalid promo period!";
								break;

						}

						if ($this->promoConditionRange($checkIfAlreadyGetPromo['dateActivate'], $expiration_date, $today)) {

							if ($promo_details['condition']['code'] == 0) {
								//if Nth
								//check promo rule
								foreach ($sorted_rules as $row) {
									if ($amount >= $row['in']) {
										$bonus = ($row['isOutPercent'] == 1) ? ($bonus = ($row['out'] / 100) * $amount) : ($bonus = $row['out']);
										$isInRule = true;
										break;
									} else {
										$isInRule = false;
									}
								}

								if ($isInRule == true) {
									if ($bonus > $promo_details['bonus']['maximum']) {
										$bonus = $promo_details['bonus']['maximum'];
									} elseif ($bonus < $promo_details['bonus']['minimum']) {
										$bonus = $promo_details['bonus']['minimum'];
									}

									if ($promo_details['bonus']['release']['code'] == 0) {
										$needed = ($amount + $bonus) * $promo_details['bonus']['release']['value'];
									} elseif ($promo_details['bonus']['release']['code'] == 0) {
										$needed = $promo_details['bonus']['release']['value'];
									}

									if ($promo_details['condition']['value'] > $count_nth) {
										$data = array(
											'playerId' => $player_id,
											'promoId' => $promo_id,
											'conditionType' => $promo_details['condition']['name'],
											'nthDepositCount' => $count_nth + 1,
											'transactionType' => $promo_details['requirements']['name'],
											'totalBetAmount' => $needed,
											'bonusAmount' => $bonus,
											'currency' => $player_account['currency'],
											'status' => 0,
										);

										$this->ci->player->createPlayerPromo($data);
										$message = "You have a bonus of <b>" . $bonus . " " . $promo_details['currency']['code'] . "</b> by depositing <b>" . $amount . " " . $player['currency'] . "</b>.";
										$pass_fail = "success";
									} elseif ($checkIfAlreadyGetPromo['status'] < 3 || $checkIfAlreadyGetPromo['dateExpire']) {
										$message = "Your promo has expired.";
										$pass_fail = "fail";
										$status = array('status' => 3);
										$this->ci->player->updatePlayerPromo($status, $player_id, $promo_id);
									} else {
										$message = "Promo expired.";
										$pass_fail = "fail";
									}
								} else {
									echo $message = "You did not meet the minimum requirement for this promo.";
									$pass_fail = "fail";
								}

							} elseif ($promo_details['condition']['code'] == 1) {
								//if Total
								if ($this->promoConditionRange($promo_details['condition']['start'], $promo_details['condition']['end'], $today)) {
									if ($promo_details['requirements']['code'] == 0) {
										//summation of deposit
										$summation = $this->ci->player->getTotalDepositFrom(date("Y-m-d H:i:s", strtotime($promo_details['condition']['start'])), $today, $player_account['playerAccountId']);
										$amount = $summation['deposit'] + $amount;
									} elseif ($promo_details['requirements']['code'] == 1) {
										//summation of bet
									}
									print_r($summation);
									foreach ($sorted_rules as $row) {
										if ($amount >= $row['in']) {
											$bonus = ($row['isOutPercent'] == 1) ? ($bonus = ($row['out'] / 100) * $amount) : ($bonus = $row['out']);
											$isInRule = true;
											break;
										} else {
											$isInRule = false;
										}
									}

									if ($isInRule == true) {
										if ($bonus > $promo_details['bonus']['maximum']) {
											$bonus = $promo_details['bonus']['maximum'];
										} elseif ($bonus < $promo_details['bonus']['minimum']) {
											$bonus = $promo_details['bonus']['minimum'];
										}

										if ($promo_details['bonus']['release']['code'] == 0) {
											$needed = ($amount + $bonus) * $promo_details['bonus']['release']['value'];
										} elseif ($promo_details['bonus']['release']['code'] == 0) {
											$needed = $promo_details['bonus']['release']['value'];
										}

										$data = array(
											'playerId' => $player_id,
											'promoId' => $promo_id,
											'conditionType' => $promo_details['condition']['name'],
											'nthDepositCount' => NULL,
											'transactionType' => $promo_details['requirements']['name'],
											'totalBetAmount' => $needed,
											'bonusAmount' => $bonus,
											'currency' => $player_account['currency'],
											'status' => 0,
										);

										$this->ci->player->createPlayerPromo($data);
										$message = "You have a bonus of <b>" . $bonus . " " . $promo_details['currency']['code'] . "</b> by depositing <b>" . $amount . " " . $player['currency'] . "</b>.";
										$pass_fail = "success";
									} elseif ($checkIfAlreadyGetPromo['status'] < 3 || $checkIfAlreadyGetPromo['dateExpire']) {
										$message = "Your promo has expired.";
										$pass_fail = "fail";
										$status = array('status' => 3);
										$this->ci->player->updatePlayerPromo($status, $player_id, $promo_id);
									} else {
										$message = "Promo expired.";
										$pass_fail = "fail";
									}
								} else {
									echo $message = "You did not meet the minimum requirement for this promo.";
									$pass_fail = "fail";
								}
							}

						} else {
							$message = "Bonus expired.";
							$pass_fail = "fail";
							$status = array('status' => 3);
							$this->ci->player->updatePlayerPromo($status, $player_id, $promo_id);
						}

					} else {
						$message = "Bonus expired.";
						$pass_fail = "fail";
						$status = array('status' => 3);
						$this->ci->player->updatePlayerPromo($status, $player_id, $promo_id);
					}
				}

			} else {
				if ($promo_details['type']['code'] == 0) {
					if ($promo_details['condition']['code'] == 0) {
						//if Nth
						//check promo rule
						foreach ($sorted_rules as $row) {
							if ($amount >= $row['in']) {
								$bonus = ($row['isOutPercent'] == 1) ? ($bonus = ($row['out'] / 100) * $amount) : ($bonus = $row['out']);
								$isInRule = true;
								break;
							} else {
								$isInRule = false;
							}
						}

						if ($isInRule == true) {
							if ($bonus > $promo_details['bonus']['maximum']) {
								$bonus = $promo_details['bonus']['maximum'];
							} elseif ($bonus < $promo_details['bonus']['minimum']) {
								$bonus = $promo_details['bonus']['minimum'];
							}

							if ($promo_details['bonus']['release']['code'] == 0) {
								$needed = ($amount + $bonus) * $promo_details['bonus']['release']['value'];
							} elseif ($promo_details['bonus']['release']['code'] == 0) {
								$needed = $promo_details['bonus']['release']['value'];
							}

							$data = array(
								'playerId' => $player_id,
								'promoId' => $promo_id,
								'conditionType' => $promo_details['condition']['name'],
								'nthDepositCount' => 1,
								'transactionType' => $promo_details['requirements']['name'],
								'totalBetAmount' => $needed,
								'bonusAmount' => $bonus,
								'currency' => $player_account['currency'],
								'status' => 0,
							);

							$this->ci->player->createPlayerPromo($data);
							$message = "You have a bonus of <b>" . $bonus . " " . $promo_details['currency']['code'] . "</b> by depositing <b>" . $amount . " " . $player['currency'] . "</b>.";
							$pass_fail = "success";

							if ($promo_details['condition']['value'] == $count_nth) {
								$message = "Promo expired.";
								$pass_fail = "fail";
								$status = array('status' => 3);
								$this->ci->player->updatePlayerPromo($status, $player_id, $promo_id);
							}
						} else {
							echo $message = "You did not meet the minimum requirement for this promo.";
							$pass_fail = "fail";
						}

					} elseif ($promo_details['condition']['code'] == 1) {
						//if Total

						if ($this->promoConditionRange($promo_details['condition']['start'], $promo_details['condition']['end'], $today)) {
							if ($promo_details['requirements']['code'] == 0) {
								//summation of deposit
								$summation = $this->ci->player->getTotalDepositFrom($promo_details['condition']['start'], $today, $player_account['playerAccountId']);
								$amount = $summation['deposit'] + $amount;
							} elseif ($promo_details['requirements']['code'] == 1) {
								//summation of bet
							}

							foreach ($sorted_rules as $row) {
								if ($amount >= $row['in']) {
									$bonus = ($row['isOutPercent'] == 1) ? ($bonus = ($row['out'] / 100) * $amount) : ($bonus = $row['out']);
									$isInRule = true;
									break;
								} else {
									$isInRule = false;
								}
							}

							if ($isInRule == true) {
								if ($bonus > $promo_details['bonus']['maximum']) {
									$bonus = $promo_details['bonus']['maximum'];
								} elseif ($bonus < $promo_details['bonus']['minimum']) {
									$bonus = $promo_details['bonus']['minimum'];
								}

								if ($promo_details['bonus']['release']['code'] == 0) {
									$needed = ($amount + $bonus) * $promo_details['bonus']['release']['value'];
								} elseif ($promo_details['bonus']['release']['code'] == 0) {
									$needed = $promo_details['bonus']['release']['value'];
								}

								$data = array(
									'playerId' => $player_id,
									'promoId' => $promo_id,
									'conditionType' => $promo_details['condition']['name'],
									'nthDepositCount' => NULL,
									'transactionType' => $promo_details['requirements']['name'],
									'totalBetAmount' => $needed,
									'bonusAmount' => $bonus,
									'currency' => $player_account['currency'],
									'status' => 0,
								);

								$this->ci->player->createPlayerPromo($data);
								$message = "You have a bonus of <b>" . $bonus . " " . $promo_details['currency']['code'] . "</b> by depositing <b>" . $amount . " " . $player['currency'] . "</b>.";
								$pass_fail = "success";
							} else {
								$message = "You did not meet the minimum requirement for this promo.";
								$pass_fail = "fail";
							}
						} else {
							$message = "Promo expired.";
							$pass_fail = "fail";
							$status = array('status' => 3);
							$this->ci->player->updatePlayerPromo($status, $player_id, $promo_id);
						}
					}

				} elseif ($promo_details['type']['code'] == 1) {
					if ($this->promoConditionRange($promo_details['condition']['start'], $promo_details['condition']['end'], $today)) {
						switch ($promo_details['period']['code']) {

							case '0':$expiration_date = date("Y-m-d H:i:s", strtotime($checkIfAlreadyGetPromo['dateActivate'] . '+1 day'));
								break;
							case '1':$expiration_date = date("Y-m-d H:i:s", strtotime($checkIfAlreadyGetPromo['dateActivate'] . '+1 week'));
								break;
							case '2':$expiration_date = date("Y-m-d H:i:s", strtotime($checkIfAlreadyGetPromo['dateActivate'] . '+1 month'));
								break;
							case '3':$expiration_date = date("Y-m-d H:i:s", strtotime($checkIfAlreadyGetPromo['dateActivate'] . '+1 year'));
								break;

							default:$message = "Invalid promo period!";
								break;

						}

						if ($promo_details['condition']['code'] == 0) {
							//if Nth
							//check promo rule
							foreach ($sorted_rules as $row) {
								if ($amount >= $row['in']) {
									$bonus = ($row['isOutPercent'] == 1) ? ($bonus = ($row['out'] / 100) * $amount) : ($bonus = $row['out']);
									$isInRule = true;
									break;
								} else {
									$isInRule = false;
								}
							}

							if ($isInRule == true) {
								if ($bonus > $promo_details['bonus']['maximum']) {
									$bonus = $promo_details['bonus']['maximum'];
								} elseif ($bonus < $promo_details['bonus']['minimum']) {
									$bonus = $promo_details['bonus']['minimum'];
								}

								if ($promo_details['bonus']['release']['code'] == 0) {
									$needed = ($amount + $bonus) * $promo_details['bonus']['release']['value'];
								} elseif ($promo_details['bonus']['release']['code'] == 0) {
									$needed = $promo_details['bonus']['release']['value'];
								}

								$data = array(
									'playerId' => $player_id,
									'promoId' => $promo_id,
									'conditionType' => $promo_details['condition']['name'],
									'nthDepositCount' => 1,
									'transactionType' => $promo_details['requirements']['name'],
									'totalBetAmount' => $needed,
									'bonusAmount' => $bonus,
									'currency' => $player_account['currency'],
									'status' => 0,
								);

								$this->ci->player->createPlayerPromo($data);
								$message = "You have a bonus of <b>" . $bonus . " " . $promo_details['currency']['code'] . "</b> by depositing <b>" . $amount . " " . $player['currency'] . "</b>.";
								$pass_fail = "success";
								if ($promo_details['condition']['value'] == $count_nth) {
									$message = "Promo expired.";
									$pass_fail = "fail";
									$status = array('status' => 3);
									$this->ci->player->updatePlayerPromo($status, $player_id, $promo_id);
								}
							} else {
								echo $message = "You did not meet the minimum requirement for this promo.";
								$pass_fail = "fail";
							}

						} elseif ($promo_details['condition']['code'] == 1) {
							//if Total
							if ($this->promoConditionRange($promo_details['condition']['start'], $promo_details['condition']['end'], $today)) {
								if ($promo_details['requirements']['code'] == 0) {
									//summation of deposit
									$summation = $this->ci->player->getTotalDepositFrom(date("Y-m-d H:i:s", strtotime($promo_details['condition']['start'])), $today, $player_account['playerAccountId']);
									$amount = $summation['deposit'] + $amount;
								} elseif ($promo_details['requirements']['code'] == 1) {
									//summation of bet
								}
								print_r($summation);
								foreach ($sorted_rules as $row) {
									if ($amount >= $row['in']) {
										$bonus = ($row['isOutPercent'] == 1) ? ($bonus = ($row['out'] / 100) * $amount) : ($bonus = $row['out']);
										$isInRule = true;
										break;
									} else {
										$isInRule = false;
									}
								}

								if ($isInRule == true) {
									if ($bonus > $promo_details['bonus']['maximum']) {
										$bonus = $promo_details['bonus']['maximum'];
									} elseif ($bonus < $promo_details['bonus']['minimum']) {
										$bonus = $promo_details['bonus']['minimum'];
									}

									if ($promo_details['bonus']['release']['code'] == 0) {
										$needed = ($amount + $bonus) * $promo_details['bonus']['release']['value'];
									} elseif ($promo_details['bonus']['release']['code'] == 0) {
										$needed = $promo_details['bonus']['release']['value'];
									}

									$data = array(
										'playerId' => $player_id,
										'promoId' => $promo_id,
										'conditionType' => $promo_details['condition']['name'],
										'nthDepositCount' => NULL,
										'transactionType' => $promo_details['requirements']['name'],
										'totalBetAmount' => $needed,
										'bonusAmount' => $bonus,
										'currency' => $player_account['currency'],
										'status' => 0,
									);

									$this->ci->player->createPlayerPromo($data);
									$message = "You have a bonus of <b>" . $bonus . " " . $promo_details['currency']['code'] . "</b> by depositing <b>" . $amount . " " . $player['currency'] . "</b>.";
									$pass_fail = "success";
								} else {
									$message = "You did not meet the minimum requirement for this promo.";
									$pass_fail = "fail";
								}
							} else {
								$message = "Promo expired.";
								$pass_fail = "fail";
								$status = array('status' => 3);
								$this->ci->player->updatePlayerPromo($status, $player_id, $promo_id);
							}
						}

					} else {
						$message = "Bonus expired.";
						$pass_fail = "fail";
						$status = array('status' => 3);
						$this->ci->player->updatePlayerPromo($status, $player_id, $promo_id);
					}
				}
			}
		} else {
			$message = "Promo expired.";
			$pass_fail = "fail";
		}

		$data = array(
			'pass_fail' => $pass_fail,
			'message' => $message,
			'player_promo_id' => $this->ci->player->checkIfAlreadyGetPromo($player_id, $promo_id)['playerPromoId'],
		);
		// echo "<pre>";
		// print_r($data);
		// print_r($promo_details);
		// echo "</pre>";
		// exit();
		return $data;
	}

	/**
	 * Will add player withdrawal details
	 *
	 * @return  array
	 */
	public function addWithdrawalDetails($data) {
		$this->ci->player->addWithdrawalDetails($data);
	}

	/**
	 * get email in email table
	 *
	 * @return  array
	 */
	public function getEmail() {
		return $this->ci->player->getEmail();
	}

	/**
	 * get email in email table
	 *
	 * @return  array
	 */
	public function getTotalAmountWithdraw($player_id) {
		return $this->ci->player->getTotalAmountWithdraw($player_id);
	}

	/**
	 * get email in email table
	 *
	 * @return  array
	 */
	public function getDailyWithdrawal($player_id, $day) {
		return $this->ci->player->getDailyWithdrawal($player_id, $day);
	}

	/**
	 * Will get player pt password given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerPassword($player_id, $api_type) {
		$result = $this->ci->gameapi->getPlayerPassword($player_id, $api_type);
		return $result;
	}

	/**
	 * get list of games
	 *
	 * @return  array
	 */
	public function getGames() {
		return $this->ci->player->getGames();
	}

	/**
	 * get player account id of sub wallet
	 *
	 * @param  int
	 * @param  int
	 * @return  array
	 */
	public function getPlayerAccountBySubWallet($player_id, $game_id) {
		return $this->ci->player->getPlayerAccountBySubWallet($player_id, $game_id);
	}

	/**
	 * add to sub wallet details
	 *
	 * @param  array
	 */
	// public function addToSubWalletDetails($data) {
	// 	return $this->ci->player->addToSubWalletDetails($data);
	// }

	/**
	 * get all player account by playerId
	 *
	 * @param  int
	 * @return  array
	 */
	public function getAllPlayerAccountByPlayerId($player_id) {
		return $this->ci->player->getAllPlayerAccountByPlayerId($player_id);
	}

	/**
	 * get all transfer history by playerId
	 *
	 * @param  int
	 * @return  array
	 */
	// public function getAllTransferHistoryByPlayerId($player_id) {
	// 	return $this->ci->player->getAllTransferHistoryByPlayerId($player_id);
	// }

	/**
	 * get all transfer history by playerId
	 *
	 * @param  int
	 * @return  array
	 */
	// public function getAllTransferHistoryByPlayerIdWLimit($player_id, $limit, $offset, $search) {
	// 	return $this->ci->player->getAllTransferHistoryByPlayerIdWLimit($player_id, $limit, $offset, $search);
	// }

	/**
	 * get all transfer history by playerId
	 *
	 * @param  int
	 * @return  array
	 */
	public function getActiveCurrency() {
		return $this->ci->player->getActiveCurrency();
	}

	/**
	 * Will get player pt password given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getFriendReferralSettings() {
		$result = $this->ci->player->getFriendReferralSettings();
		return $result;
	}

	/**
	 * Will get player pt password given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	// public function isReferredBy($player_id) {
	// 	$result = $this->ci->player->isReferredBy($player_id);
	// 	return $result;
	// }

	/**
	 * Will get player pt password given the Id
	 *
	 * @param   int
	 * @return  array
	 */
	public function getPlayerTotalDeposits($player_account_id) {
		$result = $this->ci->player->getPlayerTotalDeposits($player_account_id);
		return $result;
	}

	/**
	 * Will set player new balance amount by playerAccountId
	 *
	 * @param $dataRequest - array
	 * @return Bool - TRUE or FALSE
	 */

	public function setPlayerNewBalAmountByPlayerAccountId($playerAccountId, $data) {
		return $this->ci->player->setPlayerNewBalAmountByPlayerAccountId($playerAccountId, $data);
	}

	/**
	 * get balace by playerAccountId
	 *
	 * @return  array
	 */
	public function getBalanceByPlayerAccountId($player_account_id) {
		return $this->ci->player->getBalanceByPlayerAccountId($player_account_id);
	}

	/**
	 * Will randomize alphanumeric and special characters
	 *
	 * @param   string
	 * @return  string
	 */
	public function generateRandomCode() {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$generatePromoCode = '';
		foreach (array_rand($seed, 7) as $k) {
			$generatePromoCode .= $seed[$k];
		}

		return $generatePromoCode;
	}

	public function createPlayerGame($data) {
		return $this->ci->player->createPlayerGame($data);
	}

	/**
	 * Will get all news
	 *
	 * @return  array
	 */
	public function getAllNews() {
		return $this->ci->player->getAllNews();
	}

	public function getBankById($otc_payment_method_id) {
		return $this->ci->player->getBankById($otc_payment_method_id);
	}

	/**
	 * get email in email table
	 *
	 * @return  array
	 */
	public function getDailyDeposit($player_id, $day, $otc_payment_method_id) {
		return $this->ci->player->getDailyDeposit($player_id, $day, $otc_payment_method_id);
	}

	/**
	 * get email in email table
	 *
	 * @return  array
	 */
	public function getDailyDepositToPaymentMethod($player_id, $day, $otc_payment_method_id) {
		return $this->ci->player->getDailyDepositToPaymentMethod($player_id, $day, $otc_payment_method_id);
	}

	/**
	 * Will get player level of user
	 *
	 * @return  array
	 */
	public function getPlayerLevelGame($player_id) {
		return $this->ci->player->getPlayerLevelGame($player_id);
	}

	public function getActivePromo($player_id) {
		return $this->ci->player->getActivePromo($player_id);
	}

	public function getPromoById($player_id) {
		return $this->ci->player->getPromoById($player_id);
	}

	// public function getPlayerPromo($player_id) {
	//     return $this->ci->player->getPlayerPromo($player_id);
	// }

	public function getGameById($game_id) {
		return $this->ci->player->getGameById($game_id);
	}

	/**
	 * Resets Password of a user
	 *
	 * @return  boolean
	 */
	function resetPassword($player_id, $data) {
		/*$hasher = new PasswordHash('8', TRUE);
		$data['password'] = $hasher->HashPassword($data['password']);*/
		$data['password'] = $this->ci->salt->encrypt($data['password'], $this->getDeskeyOG());
		$this->ci->player_model->resetPassword($player_id, $data);
	}

	/**
	 * Checks if user is already existing
	 *
	 * @return  boolean
	 */
	function isValidPassword($player_id, $opassword) {

		$player = $this->getPlayerById($player_id);
		$password = $player['password'];

		if ($this->ci->salt->decrypt($password, $this->getDeskeyOG()) == $opassword) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * overview : check if password for withdraw is valid
	 *
	 * @deprecated move to player_model
	 *
	 * @param $player_id
	 * @param $opassword
	 * @return bool
	 */
	function isValidWithdrawPassword($player_id, $opassword) {

		$player = $this->getPlayerById($player_id);
		$password = $player['withdraw_password'];

		if ($this->ci->salt->decrypt($password, $this->getDeskeyOG()) == $opassword) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Resets Password of a user
	 *
	 * @return  boolean
	 */
	function resetWithrawalPassword($player_id, $password) {
		$data['withdraw_password'] = $password;
		$this->ci->player_model->resetPassword($player_id, $data);
	}

	public function getBankDetails($player_id) {
		return $this->ci->player->getBankDetails($player_id);
	}

	public function getDepositBankDetails($player_id) {
		return $this->ci->player->getDepositBankDetails($player_id);
	}

	public function getWithdrawalBankDetails($player_id) {
		return $this->ci->player->getWithdrawalBankDetails($player_id);
	}

    /**
     * @deprecated Not recommanded use in player center. Because that not filter by player id.
     * @param type $bank_details_id
     * @param type $data
     * @return type
     */
	public function updateBankDetails($bank_details_id, $data) {
		return $this->ci->player->updateBankDetails($bank_details_id, $data);
	}

    /**
     * @deprecated Not recommanded use in player center. Because that not filter by player id.
     * @param type $bank_details_id
     * @return type
     */
	public function getBankDetailsById($bank_details_id) {
		return $this->ci->player->getBankDetailsById($bank_details_id);
	}

	public function deleteBankDetails($bank_details_id) {
		return $this->ci->player->deleteBankDetails($bank_details_id);
	}

	public function addBankDetails($data) {
		return $this->ci->player->addBankDetails($data);
	}

	public function addBankDetailsByDeposit($data) {
		return $this->ci->player->addBankDetailsByDeposit($data);
	}

	public function addBankDetailsByWithdrawal($data) {
		return $this->ci->player->addBankDetailsByWithdrawal($data);
	}

	public function editBankDetails($bank_details_id, $data) {
		return $this->ci->player->editBankDetails($bank_details_id, $data);
	}

	public function getActiveBankDetails($dw_bank) {
		return $this->ci->player->getActiveBankDetails($dw_bank);
	}

	public function getAllDeposits($player_id) {
		return $this->ci->player->getAllDeposits($player_id);
	}

	public function getAllDepositsWLimit($player_id, $limit, $offset, $search) {
		return $this->ci->player->getAllDepositsWLimit($player_id, $limit, $offset, $search);
	}

	public function getAllWithdrawals($player_id) {
		return $this->ci->player->getAllWithdrawals($player_id);
	}

	public function getAllWithdrawalsWLimit($player_id, $limit, $offset, $search) {
		return $this->ci->player->getAllWithdrawalsWLimit($player_id, $limit, $offset, $search);
	}

	public function populateWithdrawals($player_id, $data) {
		return $this->ci->player->populateWithdrawals($player_id, $data);
	}

	public function populateDeposits($player_id, $data) {
		return $this->ci->player->populateDeposits($player_id, $data);
	}

	public function populateTrasferHistory($player_id, $data) {
		return $this->ci->player->populateTrasferHistory($player_id, $data);
	}

	/**
	 * Will getPlayerAvailableBankAccountDeposit
	 *
	 * @return  array
	 */
	public function getPlayerAvailableBankAccountDeposit($playerId) {
		return $this->ci->player->getPlayerAvailableBankAccountDeposit($playerId);
	}

	/**
	 * Will getPlayerCurrentTotalWithdrawalToday
	 *
	 * @return  array
	 */
	public function getPlayerCurrentTotalWithdrawalToday($playerAccountId) {
		return $this->ci->player_model->getPlayerCurrentTotalWithdrawalToday($playerAccountId);
	}

	/**
	 * Will getPlayerAvailableThirdPartyAccountDeposit
	 *
	 * @return  array
	 */
	public function getPlayerAvailableThirdPartyAccountDeposit($playerId) {
		return $this->ci->player->getPlayerAvailableThirdPartyAccountDeposit($playerId);
	}

	/**
	 * Will set new balance amount
	 *
	 * @param   $playerId int
	 * @param   $data array
	 * @return  Boolean
	 */
	public function setPlayerNewBalAmount($playerId, $data) {
		return $this->ci->player->setPlayerNewBalAmount($playerId, $data);
	}

	/**
	 * Will get PlayerDepositRule
	 *
	 * @return  array
	 */
	public function getPlayerDepositRule($playerId) {
		return $this->ci->player_model->getPlayerDepositRule($playerId);
	}

	/**
	 * Will get PlayerWithdrawalRule
	 *
	 * @return  array
	 */
	public function getPlayerWithdrawalRule($playerId) {
		return $this->ci->player_model->getPlayerWithdrawalRule($playerId);
	}

	/**
	 * Will get player balance details
	 *
	 * @param   $playerId int
	 * @return  array
	 */
	public function getPlayerCashbackWalletBalance($playerId) {
		return $this->ci->player->getPlayerCashbackWalletBalance($playerId);
	}

	/**
	 * checkPlayerPromo
	 *
	 * @param   depositPromoId int
	 */
	public function checkPlayerPromoActive($playerId) {
		return $this->ci->player->checkPlayerPromoActive($playerId);
	}

	/**
	 * getPlayerPromoActive
	 *
	 * @param   depositPromoId int
	 */
	public function getPlayerPromoActive($playerId) {
		return $this->ci->player->getPlayerPromoActive($playerId);
	}

	/**
	 * checkPlayerPromo
	 *
	 * @param   depositPromoId int
	 */
	public function checkPlayerPromoRequest($playerId) {
		return $this->ci->player->checkPlayerPromoRequest($playerId);
	}

	/**
	 * checkPlayerDuplicatePromo
	 *
	 * @param   playerId int
	 * @param   depositPromoId int
	 */
	public function checkPlayerDuplicatePromo($playerId, $depositPromoId) {
		return $this->ci->player->checkPlayerDuplicatePromo($playerId, $depositPromoId);
	}

	/**
	 * getPlayerDuplicatePromo
	 *
	 * @param   playerId int
	 * @param   promorulesId int
	 */
	// public function getPlayerDuplicatePromo($playerId, $promorulesId, $transactionStatus = 1) {
	// 	return $this->ci->player->getPlayerDuplicatePromo($playerId, $promorulesId, $transactionStatus);
	// }

	/**
	 * checkDepositPromoLevelRule
	 *
	 * @param   depositPromoId int
	 */
	public function checkDepositPromoLevelRule($playerId, $depositPromoId) {
		return $this->ci->player->checkDepositPromoLevelRule($playerId, $depositPromoId);
	}

	/**
	 * get promo details
	 *
	 * @return  rendered template
	 */
	public function getPromoDetails($depositPromoId) {
		return $this->ci->player->getPromoDetails($depositPromoId);
	}

	/**
	 * apply promo
	 *
	 * @return  rendered template
	 */
	public function applyDepositPromo($data) {
		return $this->ci->player->applyDepositPromo($data);
	}

	/**
	 * getPlayerAdjustmentHistory
	 *
	 * @return  rendered template
	 */
	public function getPlayerAdjustmentHistory($playerId) {
		return $this->ci->player->getPlayerAdjustmentHistory($playerId);
	}

	/**
	 * getPlayerAdjustmentHistory
	 *
	 * @return  rendered template
	 */
	public function getPlayerAdjustmentHistoryWLimit($playerId, $limit, $offset, $search) {
		return $this->ci->player->getPlayerAdjustmentHistoryWLimit($playerId, $limit, $offset, $search);
	}

	/**
	 * getPlayerAdjustmentHistory
	 *
	 * @return  rendered template
	 */
	public function getCashbackHistory($playerId) {
		return $this->ci->player->getCashbackHistory($playerId);
	}

	/**
	 * getPlayerAdjustmentHistory
	 *
	 * @return  rendered template
	 */
	public function getCashbackHistoryWLimit($playerId, $limit, $offset, $search) {
		return $this->ci->player->getCashbackHistoryWLimit($playerId, $limit, $offset, $search);
	}

	/**
	 * getPromoHistory
	 *
	 * @return  rendered template
	 */
	public function getPlayerPromoHistoryWLimit($player_id, $search) {
		return $this->ci->player->getPlayerPromoHistoryWLimit($player_id, $search);
	}

	/**
	 * getPlayerBlockGame
	 *
	 * @return  rendered template
	 */
	public function getPlayerBlockGame($playerId) {
		return $this->ci->player->getPlayerBlockGame($playerId);
	}

	/**
	 * getPlayer by Username
	 *
	 * @return  rendered template
	 */
	public function getPlayerByUsername($username) {
		return $this->ci->player->getPlayerByUsername($username);
	}

	/**
	 * save player info changes
	 *
	 * @return  rendered template
	 */
	public function savePlayerChanges($changes) {
		$this->ci->player->savePlayerChanges($changes);
	}

	/**
	 * save player bank changes
	 *
	 * @return  rendered template
	 */
	public function saveBankChanges($changes) {
		$this->ci->player->saveBankChanges($changes);
	}

	/**
	 * save cashcard deposit
	 *
	 * @return  rendered template
	 */
	public function saveCashCardDeposit($data) {
		$this->ci->player->saveCashCardDeposit($data);
	}

	/**
	 * getLocalBankTransactionFee
	 *
	 * @return  rendered template
	 */
	public function getLocalBankTransactionFee($bankAccountId) {
		return $this->ci->player->getLocalBankTransactionFee($bankAccountId);
	}

	/**
	 * getThirdpartyTransactionFee
	 *
	 * @return  rendered template
	 */
	public function getThirdpartyTransactionFee($thirdpartyAccountId) {
		return $this->ci->player->getThirdpartyTransactionFee($thirdpartyAccountId);
	}

	/**
	 * getTrasancationFeeSetting
	 *
	 * @return  array
	 */
	public function getTrasancationFeeSetting() {
		return $this->ci->player->getTrasancationFeeSetting();
	}

	/**
	 * getPlayerTotalDepositCnt
	 *
	 * @return  array
	 */
	public function getPlayerTotalDepositCnt($playerId, $periodFrom, $periodTo) {
		return $this->ci->player->getPlayerTotalDepositCnt($playerId, $periodFrom, $periodTo);
	}

	/**
	 * getPlayerTotalDepositDaily
	 *
	 * @return  int
	 */

	public function getPlayerTotalDepositDaily($playerId, $date) {
		return $this->ci->player->getPlayerTotalDepositDaily($playerId, $date);
	}

	/**
	 * getPlayerCurrentTotalDepositCnt
	 *
	 * @return  array
	 */
	public function getPlayerCurrentTotalDepositCnt($playerId, $periodFrom, $periodTo) {
		return $this->ci->player->getPlayerCurrentTotalDepositCnt($playerId, $periodFrom, $periodTo);
	}

	/**
	 * getPlayerRegisterDate
	 *
	 * @return  array
	 */
	public function getPlayerRegisterDate($playerId) {
		return $this->ci->player->getPlayerRegisterDate($playerId);
	}

	/**
	 * getPlayerCurrentBetAmt
	 *
	 * @return  array
	 */
	public function getPlayerCurrentBetAmt($playerName, $gameType, $startDate, $endDate) {
		return $this->ci->player->getPlayerCurrentBetAmt($playerName, $gameType, $startDate, $endDate);
	}

	/**
	 * checkPlayerPromo
	 *
	 * @return  array
	 */
	public function checkPlayerPromo($playerPromoId) {
		return $this->ci->player->checkPlayerPromo($playerPromoId);
	}

	/*
		 * Will check if multidimensional array consists of value and key your looking
		 *
		 * @param   int
		 * @return  array
	*/
	public function checkIfValueExists($array, $key, $val) {
		foreach ($array as $item) {
			if (isset($item[$key]) && $item[$key] == $val) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Will get if multidimensional array consists of value and key your looking
	 *
	 * @param   int
	 * @return  array
	 */
	public function getIfValueExists($array, $key, $val) {
		$result = array();

		foreach ($array as $item) {
			if (isset($item[$key]) && $item[$key] == $val) {
				array_push($result, $item);
			}
		}

		return $result;
	}

	/**
	 * processPromoApplication
	 *
	 * @param   data array
	 * @return  array
	 */
	// public function processPromoApplication($playerpromodata) {
	// 	$this->ci->player->processPromoApplication($playerpromodata);
	// }

	/**
	 * Will set new balance amount
	 *
	 * @param   $playerId int
	 * @param   $data array
	 * @return  Boolean
	 */
	public function setPlayerNewSubWalletBalAmount($playerId, $data, $gameType) {
		return $this->ci->player->setPlayerNewSubWalletBalAmount($playerId, $data, $gameType);
	}

	/**
	 * Will save transaction details
	 *
	 * @param   $data array
	 * @return  Boolean
	 */
	public function saveTransaction($data) {
		return $this->ci->transactions_library->saveTransaction($data);
	}

	/**
	 * Will check if non deposit promo exists
	 *
	 * @param   $playerId int
	 * @param   $promorulesId int
	 * @return  Boolean
	 */
	public function isPlayerNonDepositPromoExist($playerId, $promorulesId) {
		$data = $this->ci->promo->getPromoRuleNonDepositPromoType($promorulesId);
		//var_dump($data['nonDepositPromoType']);exit();
		if ($data['nonDepositPromoType'] < 4) {
			return $this->ci->player->isPlayerNonDepositPromoExist($playerId, $data['nonDepositPromoType']);
		} else {
			return false;
		}

	}

	/**
	 * Will check player registration complete
	 *
	 * @param   $playerId int
	 * @return  Boolean
	 */
	public function getPlayerRegistrationStatus($playerId) {
		$data = $this->ci->player->getPlayerRegistrationRequirements();
		//$this->ci->player->getPlayerExistingProfile('secretQuestion',$playerId);exit();
		// foreach ($data as $key) {
		//     //echo "fields: ".$key['alias'],$playerId;
		//     var_dump($this->ci->player->getPlayerExistingProfile($key['alias'],$playerId));
		// }

		foreach ($data as $key) {
			if (!$this->ci->player->isPlayerCompleteProfile($key['alias'], $playerId)) {
				return false; //registration is incomplete
			}
		}
		return true;
	}

	/**
	 * Will get player total bet
	 *
	 * @param   $playerName str
	 * @param   $gameRecordStartDate date
	 * @param   $gameRecordEndDate date
	 * @return  int
	 */
	public function getPlayerTotalBet($playerName, $gameRecordStartDate, $gameRecordEndDate) {
		return $data = $this->ci->player->getPlayerTotalBet($playerName, $gameRecordStartDate, $gameRecordEndDate);
	}

	/**
	 * Will get player total loss
	 *
	 * @param   $playerName str
	 * @param   $gameRecordStartDate date
	 * @param   $gameRecordEndDate date
	 * @return  int
	 */
	public function getPlayerTotalLoss($playerName, $gameRecordStartDate, $gameRecordEndDate) {
		return $data = $this->ci->player->getPlayerTotalLoss($playerName, $gameRecordStartDate, $gameRecordEndDate);
	}

	/**
	 * Will get player total win
	 *
	 * @param   $playerName str
	 * @param   $gameRecordStartDate date
	 * @param   $gameRecordEndDate date
	 * @return  int
	 */
	public function getPlayerTotalWin($playerName, $gameRecordStartDate, $gameRecordEndDate) {
		return $data = $this->ci->player->getPlayerTotalWin($playerName, $gameRecordStartDate, $gameRecordEndDate);
	}

	/**
	 * Will check get player start date
	 *
	 * @param   $playerId int
	 * @return  date
	 */
	public function getPlayerStartDate($playerId) {
		return $data = $this->ci->player->getPlayerStartDate($playerId);
	}

	/**
	 * Will check get player start date
	 *
	 * @param   $playerId int
	 * @return  boolean
	 */
	public function isPlayerWalletAccountZero($playerId) {
		//check player wallet account (main/sub)
		$playerMainWalletBalance = $this->ci->player->getMainWalletBalance($playerId);
		if ($playerMainWalletBalance['totalBalanceAmount'] == null) {
			$playerMainWalletBalance['totalBalanceAmount'] = 0;
		}

		if ($playerMainWalletBalance['totalBalanceAmount'] != 0) {
			return false;
		} else {
			$subwallet = $this->ci->player->getAllPlayerAccountByPlayerId($playerId);
			foreach ($subwallet as $key) {
				if ($key['totalBalanceAmount'] != 0) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * get registered fields
	 *
	 * @param  type
	 * @return array
	 */
	public function getRegisteredFields($type) {
		return $this->ci->player->getRegisteredFields($type);
	}

	/**
	 * check registration fields if visible
	 *
	 * @param  type
	 * @return array
	 */
	public function checkRegisteredFieldsIfVisible($field_name) {
		$registered_fields = $this->getRegisteredFields(1);

		foreach ($registered_fields as $key => $value) {
			if ($value['field_name'] == $field_name) {
				return $value['visible'];
			}
		}
	}

	/**
	 * check registration fields if required
	 *
	 * @param  type
	 * @return array
	 */
	public function checkRegisteredFieldsIfRequired($field_name) {
		$registered_fields = $this->getRegisteredFields(1);

		foreach ($registered_fields as $key => $value) {
			if ($value['field_name'] == $field_name) {
				return $value['required'];
			}
		}
	}

	/**
	 * check registration fields if visible
	 *
	 * @param  type
	 * @return array
	 */
	public function checkAccountFieldsIfVisible($field_name) {
		$registered_fields = $this->getRegisteredFields(1);

		foreach ($registered_fields as $key => $value) {
			if ($value['field_name'] == $field_name) {
				return !(bool)$value['account_visible'];
			}
		}
	}

	/**
	 * check registration fields if required
	 *
	 * @param  type
	 * @return array
	 */
	public function checkAccountFieldsIfRequired($field_name) {
		$registered_fields = $this->getRegisteredFields(1);

		foreach ($registered_fields as $key => $value) {
			if ($value['field_name'] == $field_name) {
				return !(bool)$value['account_required'];
			}
		}
	}

	//checkAccountFieldsIfRequired
	public function checkAccount_displayPlaceholderHint($field_name) {
		$registered_fields = $this->getRegisteredFields(1);
		$required_string = '';
		if ( $this->ci->system_feature->isEnabledFeature('enabled_display_placeholder_hint_require') ) {
			foreach ($registered_fields as $key => $value) {
				if ($value['field_name'] == $field_name) {
					if( !(bool)$value['account_required'] ) {
						return $required_string = '(' . lang('Required') . ')';
					}
				}
			}
		}
		return $required_string;
	}

	//checkAccountFieldsIfRequired
	public function checkAccount_displaySymbolHint($field_name) {
		$registered_fields = $this->getRegisteredFields(1);
		$required_string = '';
		foreach ($registered_fields as $key => $value) {
			if ($value['field_name'] == $field_name) {
				if( !(bool)$value['account_required'] ) {
					return $required_string = '<i class="text-danger accountinfo required">*</i>';
				}
			}
		}
		return $required_string;
	}

    //checkAccountFieldsIfNeedInputHint
    public function checkAccount_displayInputHint($field_alias) {
        $registered_fields = $this->getRegisteredFields(1);
        $required_string = '';
        foreach ($registered_fields as $key => $value) {
            if ($value['alias'] == $field_alias) {
                switch ($value['alias']){
                    case 'firstName':
                        if( $this->ci->system_feature->isEnabledFeature('enabled_account_fields_display_first_name_input_hint') ){
                            $required_string = '<span class="input_hint correspond_with_bank_account_name">' . lang('pi.input.hint.name') . '</span>';
                        }
                        break;
                }
            }
        }

        return $required_string;
    }

	/**
	 * get player sub wallet account id
	 *
	 * @param  playerId
	 * @param  subwalletType
	 * @return int
	 */
	public function getSubWalletAccountId($playerId, $subwalletType) {
		return $this->ci->player->getSubWalletAccountId($playerId, $subwalletType);
	}

	/**
	 * check if bankaccount record exists
	 *
	 * @param  int
	 * @param  int
	 * @return
	 */
	function isPlayerBankAccountNumberExists($bankAccountNumber, $dw_bank) {
		return $this->ci->player->isPlayerBankAccountNumberExists($bankAccountNumber, $dw_bank);
		//save pw to player table
	}

	/**
	 * getPlayerActivePromoDetails
	 *
	 * @return
	 */
	function getPlayerActivePromoDetails($playerId) {
		return $this->ci->player->getPlayerActivePromoDetails($playerId);
		//save pw to player table
	}

	function get_player_total_bet($player_id) {
		$this->ci->load->model(array('game_logs', 'game_provider_auth'));
		$game_platforms = $this->ci->game_provider_auth->getGamePlatforms($player_id);
		$game_logs = $this->ci->game_logs->getSummary($player_id);
		$game_data = array();
		foreach ($game_platforms as &$game_platform) {
			$game_platform_id = $game_platform['id'];

			if (isset($game_logs[$game_platform_id])) {

				$game_platform = array_merge($game_platform, $game_logs[$game_platform_id]);
				$game_data['total_bet_sum'] += $game_logs[$game_platform_id]['bet']['sum'];
			}

		}
		return $game_data ? $game_data : 0;
		var_dump($game_data);exit();
	}

	public function savePlayerUpdateLog($player_id, $changes, $updatedBy) {
		$this->savePlayerChanges([
			'playerId' => $player_id,
			'changes' => $changes,
			'createdOn' => date('Y-m-d H:i:s'),
			'operator' => $updatedBy,
		]);
	}

	/**
	 * Returns player's combined VIP level status
	 * Extracted from Api::getPlayerVipGroupDetails() as a library method, OGP-15849
	 * @param	int		$playerId		== player.playerId
	 * @param	bool	$force_desktop	Force to use desktop badge path, do not use mobile
	 *
	 * @see		Api::getPlayerVipGroupDetails()
	 * @see		player_functions::vip_getNextLevelRequirementsForBetAndDepAmt()
	 * @see		player_functions::vip_getCurrentPlayerLvlBonusAmount()
	 * @see		player_functions::vip_getCurrentPlayerTotalBetAmt()
	 * @see		player_functions::vip_getVipGroupNextLevel()
	 * @see		player_functions::vip_getPlayerTotalCashbackBalance()
	 * @see		player_functions::vip_getCurrentPlayerAvailableBalance()
	 * @see		player_functions::vip_getDaysLeftBeforeBirthday()
	 * @return	array
	 */
	public function getPlayerVipGroupDetails($playerId, $force_desktop = false) {
		$this->ci->load->model(array('game_logs', 'group_level', 'transactions', 'player_model', 'total_player_game_day'));

        $overview_config = $this->ci->utils->getConfig('overview');

        $player_today_total_betting_platforms = (isset($overview_config['today_total_betting_platforms']) && !empty($overview_config['today_total_betting_platforms'])) ? $overview_config['today_total_betting_platforms'] : null;

		if (empty($playerId)) { return null; }

		//get server protocol
		$serverProtocol = $this->ci->utils->getServerProtocol();

		//player info data
		$playerInfo = $this->ci->player_model->getPlayerInfoDetailById($playerId);
		if (empty($playerInfo['vipLevel'])) {
            // set to default level
            $defaultVipLevel = $this->ci->group_level->getVIPTopLevel(1, 1);
            if (!empty($defaultVipLevel)) {
                $newPlayerLevel = $defaultVipLevel->vipsettingcashbackruleId;
                $this->ci->utils->debug_log('======================== defaultVipLevel' . $newPlayerLevel);
                $this->ci->group_level->startTrans();
                $this->ci->group_level->adjustPlayerLevel($playerId, $newPlayerLevel);
                $data = array(
                    'playerId' => $playerId,
                    'changes' => 'Set player level to default level',
                    'createdOn' => date('Y-m-d H:i:s'),
                    'operator' => $this->ci->authentication->getUsername(),
                );
                $this->ci->player_model->addPlayerInfoUpdates($playerId, $data);

                $this->saveAction('Player Management', 'Adjust Player Level', "User " . $this->authentication->getUsername() . " has adjusted player '" . $playerId . "'");
                $this->ci->group_level->endTrans();
            }
            $playerInfo = $this->ci->player_model->getPlayerInfoDetailById($playerId);
        }


		//get player current level details
		$vipSettingId = $this->ci->group_level->getPlayerLevelId($playerId);
		$getPlayerCurrentLevelDetails = $this->ci->group_level->getVipGroupLevelDetails($vipSettingId);

		$vipGroupDetails = $this->ci->group_level->getVIPGroupDetail($getPlayerCurrentLevelDetails['vipSettingId']);

		//get how many levels in a vip group where player belong
		$vipGroupLevels = $this->ci->group_level->getVIPGroupLevels($vipGroupDetails['vipSettingId']);

		$maxLevel = 1;
		foreach ($vipGroupLevels as $detail) {
			if ($detail['vipLevel'] >= $maxLevel) {
				$maxLevel = $detail['vipLevel'];
			}
		}

		//initialize data
		$vipUpgradeDetailsDepositAmountRequirement = 0;
		$vipUpgradeDetailsBetAmountRequirement = 0;
		$getCurrentPlayerTotalBetAmt = 0;
		$vipNextLevelPercentageDeposit = 0;
		$vipNextLevelPercentageBet = 0;
		$playerTotalBonus = 0;
		$playerCurrentLvlBirthdayBonusAmt = 0;
		$playerCurrentLvlDepositAmt = 0;
		$nextLvlReqOperator = false;
		$formula = null;
		$playerUpgradeProgress = array();
		$schedule = '';

		//get upgrade details
		$vipUpgradeDetails = $this->ci->group_level->getVIPGroupUpgradeDetails($getPlayerCurrentLevelDetails['vip_upgrade_id']);
		if ($vipUpgradeDetails) {
			$accumulation = $vipUpgradeDetails['accumulation'];
			$vipUpgradeDetails = json_decode($vipUpgradeDetails['formula'], true);
			$formula = $this->ci->group_level->displayPlayerFormulaForUpgrade($vipUpgradeDetails);
			$playerUpgradeProgress = $this->ci->group_level->getPlayerUpgradePercentage($playerId, $vipUpgradeDetails, $getPlayerCurrentLevelDetails['period_up_down_2']);


			if(!empty($getPlayerCurrentLevelDetails['period_up_down_2'])) {
				$schedule = json_decode($getPlayerCurrentLevelDetails['period_up_down_2'], true);
				# $schedule = $this->group_level->getUpgradeSchedule($schedule)['sched'];
				$tmp = $this->ci->group_level->getUpgradeSchedule($schedule);
                if (isset($tmp['sched'])){
                    $schedule = $tmp['sched'];
                }
			}

			$arrayChildAmt = 1;
			$nextLvlReq = $this->vip_getNextLevelRequirementsForBetAndDepAmt($vipUpgradeDetails);
			$vipUpgradeDetailsDepositAmountRequirement = isset($nextLvlReq['deposit_amount']) ? $nextLvlReq['deposit_amount'][$arrayChildAmt] : 0;
			$vipUpgradeDetailsBetAmountRequirement = isset($nextLvlReq['bet_amount']) ? $nextLvlReq['bet_amount'][$arrayChildAmt] : 0;

			//get player total deposit amount for its currrent level
			list($playerCurrentLvlDepositAmt, $playerCurrentLvlBirthdayBonusAmt, $playerTotalBonus) = $this->vip_getCurrentPlayerLvlBonusAmount($playerId, json_decode($getPlayerCurrentLevelDetails['period_up_down_2'], true), $accumulation);

			//generate next level percentage by deposit, deposit condition must exists if not it will not show the percentage
			if ($playerCurrentLvlDepositAmt && $vipUpgradeDetailsDepositAmountRequirement) {
				$vipNextLevelPercentageDeposit = round(($playerCurrentLvlDepositAmt / $vipUpgradeDetailsDepositAmountRequirement) * 100);
				$vipNextLevelPercentageDeposit = $vipNextLevelPercentageDeposit >= 100 ? 100 : $vipNextLevelPercentageDeposit;
			}

			//get current player total bet amt per vip level
			$getCurrentPlayerTotalBetAmt = $this->vip_getCurrentPlayerTotalBetAmt($playerId, $getPlayerCurrentLevelDetails['period_up_down_2'], $accumulation);

			//generate next level percentage by bet, bet condition must exists if not it will not show the percentage
			if ($getCurrentPlayerTotalBetAmt && $vipUpgradeDetailsBetAmountRequirement) {
				$vipNextLevelPercentageBet = round(($getCurrentPlayerTotalBetAmt / $vipUpgradeDetailsBetAmountRequirement) * 100);
				$vipNextLevelPercentageBet = $vipNextLevelPercentageBet >= 100 ? 100 : $vipNextLevelPercentageBet;
			}

			if (isset($nextLvlReq['operator'])) {
				$nextLvlReqOperator = $nextLvlReq['operator'];
			}
		}

		//get next vip group level
		$nextVipGroupLvl = $this->vip_getVipGroupNextLevel($playerInfo['vipsettingcashbackruleId'], $vipGroupLevels);

		//get last login time
		// $lastLoginTime = new DateTime($playerInfo['lastLoginTime']);
		$lastLoginTimeObj = new DateTime($playerInfo['last_login_time']);
		$lastLoginTime = $this->ci->utils->formatDatetimeForDisplay($lastLoginTimeObj);
		$lastLoginTimeZone = $this->ci->utils->getDatetimeTimezone($lastLoginTimeObj);

		//get total cashback
		$playerTotalCashbackBalance = $this->vip_getPlayerTotalCashbackBalance($playerId);

		//get player available cashback
		$playerAvailableCashback = $this->vip_getCurrentPlayerAvailableBalance($playerId);

		//get player points details
		// list($playerAvailablePoints, $playerTotalPoints) = $this->getPlayerTotalPoints($playerId);

		//get days left before birthday
		$bdate = new DateTime($playerInfo['birthdate']);
		$daysLeftBeforeBday = $this->vip_getDaysLeftBeforeBirthday($bdate);

		//get vip badge
		$firstChild = 0;

		//$vipBadge = $getPlayerCurrentLevelDetails['badge'] ?: "vip-icon.png";

		// OGP-15868: Force output desktop version image
		$check_mobile = !$force_desktop;

		$vip_badge_filename = $this->ci->utils->getVipBadgePath($check_mobile) . $getPlayerCurrentLevelDetails['badge'];

		if (file_exists($vip_badge_filename)) {
			$vipBadge = $this->ci->utils->getVipBadgeUri($check_mobile).'/'.$getPlayerCurrentLevelDetails['badge'];
		} else {
			$vipBadge = base_url() . $this->ci->utils->getPlayerCenterTemplate($check_mobile).'/img/icons/star.png';
		}

		if(!empty($schedule)) {
			$schedule = lang('Level upgrade is set to').' '.lang($schedule);
		}

		$vipGroupInfo = array(
			"current_vip_level" => array(
				"maxLevel" => $maxLevel,
				"vip_group_name" => lang($playerInfo['groupName']),
				"vip_lvl_name" => lang($playerInfo['vipLevelName']),
				"vip_group_lvl" => lang($playerInfo['groupName']) . " - " . lang($playerInfo['vipLevelName']), //. " " . $playerInfo['vipLevel'],
				"vip_group_lvl_id" => $playerInfo['vipsettingcashbackruleId'],
				"vip_group_lvl_name" => lang($playerInfo['vipLevelName']),
				"vip_group_lvl_number" => $playerInfo['vipLevel'],
				"vip_group_lvl_bday_bonus_amt" => $getPlayerCurrentLevelDetails['birthday_bonus_amount'] ?: 0,
				"vip_group_lvl_badge" => $vipBadge,
				"upgrade_deposit_amt_req" => $this->ci->utils->formatCurrency($vipUpgradeDetailsDepositAmountRequirement, false, true, true),
				"upgrade_bet_amt_req" => $this->ci->utils->formatCurrency($vipUpgradeDetailsBetAmountRequirement, false, true, true),
				"current_lvl_deposit_amt" => $this->ci->utils->formatCurrency($playerCurrentLvlDepositAmt, false, true, true),
				"current_lvl_bet_amt" => $this->ci->utils->formatCurrency($getCurrentPlayerTotalBetAmt, false, true, true),
				// "next_level_percentage" => $vipNextLevelPercentageDeposit,
				"next_level_percentage_deposit" => $vipNextLevelPercentageDeposit,
				"next_level_percentage_bet" => $vipNextLevelPercentageBet,
				"next_level_percentage_operator" => $nextLvlReqOperator,
				"birthday_bonus_expiration_datetime" => $getPlayerCurrentLevelDetails['birthday_bonus_expiration_datetime'],
				"bonus_mode_birthday" => empty($getPlayerCurrentLevelDetails['bonus_mode_birthday']) ? null : $getPlayerCurrentLevelDetails['bonus_mode_birthday'],
				"formula" => $formula,
				"player_upgrade_progress" => $playerUpgradeProgress,
				"schedule" => $schedule,
			),
			"next_vip_level" => array(
				"vip_group_lvl_name" => lang($nextVipGroupLvl['vipLevelName']),
				"vip_group_lvl_number" => $nextVipGroupLvl['vipLevel'],
			),
			"others" => array(
				"player_total_bonus" => $playerTotalBonus,
				"player_total_cashback_amount_received" => $this->ci->utils->formatCurrencyNoSym(empty($playerTotalCashbackBalance) ? 0 : $playerTotalCashbackBalance),
				"player_available_cashback_amount" => $playerAvailableCashback,
				// "player_available_points" => $playerAvailablePoints,
				// "player_total_points" => $playerTotalPoints,
				"player_days_left_before_bday_bonus" => $daysLeftBeforeBday,
				"player_last_login_time" => $lastLoginTime,
				'player_last_login_timezone' => $lastLoginTimeZone,
				"player_birthdate" => $playerInfo['birthdate'] ?: false,
				"player_birthdate_exists" => $playerInfo['birthdate'] ? true : false,
				"player_birthday_bonus_amount_received" => $playerCurrentLvlBirthdayBonusAmt,
				// ** player_profile_pic, player_profile_progress not used anymore, commented out - OGP-15868
				// "player_profile_pic" => $serverProtocol . "://" . $this->ci->utils->getSystemHost('player') . "/" . $this->ci->utils->getPlayerCenterTemplate() . $this->setProfilePicture(),
				// "player_profile_progress" => $this->getProfileProgress(),
                'player_today_total_betting_amount' => $this->ci->utils->formatCurrency($this->ci->game_logs->getPlayerCurrentBetByPlatform($playerId, date("Y-m-d 00:00:00"), null, null, $player_today_total_betting_platforms)),
			),
		);

		// return $this->returnJsonResult($vipGroupInfo);
		return $vipGroupInfo;
	} // End function getPlayerVipGroupDetails()

	/**
	 * Calculation routine for player_functions::getPlayerVipGroupDetails(), OGP-15868
	 * @param	object	$vipUpgradeDetails	from Group_level::getVIPGroupUpgradeDetails()
	 * @see		player_functions::getPlayerVipGroupDetails()
	 * @return	mixed
	 */
	protected function vip_getNextLevelRequirementsForBetAndDepAmt($vipUpgradeDetails) {
		$result = array();
		if (array_key_exists('bet_amount', $vipUpgradeDetails)) {
			$result['bet_amount'] = $vipUpgradeDetails['bet_amount'];
		}

		if (array_key_exists('deposit_amount', $vipUpgradeDetails)) {
			$result['deposit_amount'] = $vipUpgradeDetails['deposit_amount'];
		}

		if (array_key_exists('operator_2', $vipUpgradeDetails)) {
			$result['operator'] = $vipUpgradeDetails['operator_2'];
		}
		return $result;
	}

	/**
	 * Calculation routine for player_functions::getPlayerVipGroupDetails(), OGP-15868
	 * @param	int		$playerId		== player.playerId
	 * @param	object	$groupLevelUpgradePeriodSetting
	 * @param	bool	$accumulation
	 * @see		player_functions::getPlayerVipGroupDetails()
	 * @return	array if successful, otherwise bool false
	 */
	protected function vip_getCurrentPlayerLvlBonusAmount($playerId, $groupLevelUpgradePeriodSetting, $accumulation) {
		if ($groupLevelUpgradePeriodSetting) {
			$this->ci->load->model(array("group_level", "transactions", "player_model"));
			// $rangeType = json_decode($groupLevelUpgradePeriodSetting, true);
			// $result = $this->group_level->getUpgradeSchedule($rangeType);
			$fromRange = $this->ci->group_level->getUpgradeSchedule($groupLevelUpgradePeriodSetting, true)['dateFrom'];
			$toRange = $this->ci->group_level->getUpgradeSchedule($groupLevelUpgradePeriodSetting, true)['dateTo'];
			if ((int)$accumulation == 1) {
				$playerDetails = $this->ci->player_model->getPlayerDetailsById($playerId);
				$now = new DateTime();
				$fromRange = $playerDetails->createdOn;
				$toRange = $now->format('Y-m-d H:i:s');
			}
			$result = $this->ci->transactions->getTotalDepositBonusAndBirthdayByPlayers($playerId, $fromRange, $toRange);
			return array($result[Transactions::DEPOSIT], $result[Transactions::BIRTHDAY_BONUS], $result['totalBonus'], $result['totalBonus']);
		}

		return false;
	}

	/**
	 * Calculation routine for player_functions::getPlayerVipGroupDetails(), OGP-15868
	 * @param	int		$playerId		== player.playerId
	 * @param	object	$groupLevelUpgradePeriodSetting
	 * @param	bool	$accumulation
	 * @see		player_functions::getPlayerVipGroupDetails()
	 * @return	array if successful, otherwise bool false
	 */
	protected function vip_getCurrentPlayerTotalBetAmt($playerId, $groupLevelUpgradePeriodSetting, $accumulation) {
		$this->ci->load->model(["group_level"]);
		if ($groupLevelUpgradePeriodSetting) {
			$gameLogData = $this->ci->group_level->getPlayerBetAmtForNextLvl($playerId, $groupLevelUpgradePeriodSetting, $accumulation);
			return $gameLogData;
		}

		return false;
	}

	/**
	 * Determines next vip level
	 * Calculation routine for player_functions::getPlayerVipGroupDetails(), OGP-15868
	 * @param	int		$currentGroupLvlId
	 * @param	array  	$groupLevel
	 * @see		player_functions::getPlayerVipGroupDetails()
	 * @return	object if successful, otherwise null
	 */
	protected function vip_getVipGroupNextLevel($currentGroupLvlId, $groupLevel) {
		foreach ($groupLevel as $key) {
			if ($currentGroupLvlId + 1 == $key['vipsettingcashbackruleId']) {
				return $key ?: null;
			}
		}
	}

	/**
	 * Determines player's lifetime total cashback
	 * Calculation routine for player_functions::getPlayerVipGroupDetails(), OGP-15868
	 * @param	int		$playerId		== player.playerId
	 * @see		player_functions::getPlayerVipGroupDetails()
	 * @return	float if successful, otherwise null
	 */
	protected function vip_getPlayerTotalCashbackBalance($playerId) {
		$this->ci->load->model(['transactions']);
		$balance = $this->ci->transactions->sumCashback($playerId);
		return $balance;
	}

	/**
	 * Calculation routine for player_functions::getPlayerVipGroupDetails(), OGP-15868
	 * @param	int		$playerId		== player.playerId
	 * @see		player_functions::getPlayerVipGroupDetails()
	 * @return	float
	 */
	protected function vip_getCurrentPlayerAvailableBalance($playerId) {
		$fromRange = new DateTime();
		$fromRange = $fromRange->format("Y-m-d") . " 00:00:00";
		$toRange = new DateTime();
		$toRange = $toRange->format("Y-m-d") . " 23:59:59";
		$this->ci->load->model("transactions");
		return $this->ci->transactions->getTotalDepositBonusAndBirthdayByPlayers($playerId, $fromRange, $toRange)['availableCashback'];
	}

	/**
	 * Determines how many days to player's next birthday
	 * Calculation routine for player_functions::getPlayerVipGroupDetails(), OGP-15868
	 * @param	DateTime	$bdate
	 * @see		player_functions::getPlayerVipGroupDetails()
	 * @return	int
	 */
	protected function vip_getDaysLeftBeforeBirthday($bdate) {
		$currentDate = new DateTime();
		$currentYear = $currentDate->format("Y");
		$bday = new DateTime($currentYear . "-" . $bdate->format("m-d"));

		$daysDiff = $currentDate->diff($bday);
		$daysLeft = $daysDiff->format('%R%a');
		$aYear = 365;
		if ($daysLeft < 0) {
			return $aYear - ($daysLeft * -1);
		} else {
			return (int) $daysDiff->format('%a');
		}
	}

} // End class user_functions

/* End of file user_functions.php */
/* Location: ./application/libraries/player_functions.php */