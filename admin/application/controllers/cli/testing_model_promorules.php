<?php
//always include base testing
require_once dirname(__FILE__) . '/base_testing.php';

//always extends from BaseTesting
class Testing_model_promorules extends BaseTesting {

	//should overwrite init function
	public function init() {
		//init your model or lib
		$this->load->model('promorules');
		//init lang
	}
	//should overwrite testAll
	public function testAll() {
		//init first
		$this->init();
		//call your test function
		$this->testModel();
	}

	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	private function testSystemManual() {
		$this->test($this->promorules->getSystemManualPromoTypeId() != 0, true, 'test system manual promo type id');
		$this->test($this->promorules->getSystemManualPromoRuleId() != 0, true, 'test system manual promo rule id');
		$this->test($this->promorules->getSystemManualPromoCMSId() != 0, true, 'test system manual promo cms id');
	}

	//it's your real test function
	private function testModel() {
		$this->getPromoRules(1);
	}

	private function getFirstDepositPromo() {
		$this->db->from('promorules')->where('promoType', Promorules::PROMO_TYPE_DEPOSIT)
			->where('status', Promorules::OLD_STATUS_ACTIVE);

		$qry = $this->db->get();
		return $qry->row_array();
	}

	private function getFirstNonDepositPromo() {
		$this->db->from('promorules')->where('promoType', Promorules::PROMO_TYPE_NON_DEPOSIT)
			->where('status', Promorules::OLD_STATUS_ACTIVE);

		$qry = $this->db->get();
		return $qry->row_array();
	}

	private function testDepositPromo() {
		$player = $this->getFirstPlayer();
		$depositPromoRule = $this->getFirstDepositPromo();
		$promoCmsSettingId = null;
		list($success, $message) = $this->promorules->checkAndProcessDepositPromotion(
			$player->playerId, $depositPromoRule, $promoCmsSettingId);

		$this->test($success, true, 'test deposit promo');
	}

	private function testNonDepositPromo() {
		$player = $this->getFirstPlayer();
		$nonDepositPromoRule = $this->getFirstNonDepositPromo();
		$promoCmsSettingId = null;
		list($success, $message) = $this->promorules->checkAndProcessNoDepositPromotion(
			$player->playerId, $nonDepositPromoRule, $promoCmsSettingId);

		$this->utils->debug_log('success', $success, 'message', $message);
		$this->test($success, true, 'test non-deposit promo');
	}

}

/**
 * promoRuleTest
 *
 * SHOULD BE MOVED TO cli
 *
 * Unit Testing
 * @return  rendered template
 */
// public function promoRuleTest($scenario) {

// 	switch ($scenario) {
// 		case 1:
/* Promo Type: deposit promo
Deposit Condition: Fixed Deposit = 1000
Deposit Succession: 1st Deposit
Bonus Release: Fixed Bonus Amount = 100, auto
Withdraw Requirement: Bet Amount >= 1000
Allowed Game Type: (AG,PT)
Allowed Player Level: Default

Note: player must be 1st time deposit to avail this promo
Expected Result: Bonus Amount = 100
 */
// 	$this->verifyPromoApplication(1000, 6, 19);
// 	break;

// case 2:
/* Promo Type: deposit promo
Deposit Condition: Fixed Deposit = 2000
Deposit Succession: 1st Deposit
Bonus Release: 10% of deposit amount, up to 300 Max Bonus Amount.
Withdraw Requirement: Bet Amount >= 2000
Allowed Game Type: (AG,PT)
Allowed Player Level: Default

Note: player must be 1st time deposit to avail this promo
Expected Result: Bonus Amount = 200
 */
// 	$this->verifyPromoApplication(2000, 7, 19);
// 	break;

// case 3:
/* Promo Type: deposit promo
Deposit Condition: Non-Fixed Deposit <= 1000
Deposit Succession: 1st Deposit
Bonus Release: Fixed Bonus Amount = 100
Withdraw Requirement: Bet Amount >= 1000
Allowed Game Type: (AG,PT)
Allowed Player Level:
Default Player Group Level Name 1
Star VIP Group Level Name 1
Star VIP Group Level Name 2
Star VIP Group Level Name 3

Note: player must be 1st time deposit to avail this promo
Expected Result: Bonus Amount = 100
 */
// 	$this->verifyPromoApplication(1000, 8, 19);
// 	break;

// case 4:
/* Promo Type: deposit promo
Deposit Condition: Non fixed deposit amount >= 1000
Deposit Succession: 4th Deposit
Bonus Release: Fixed Bonus Amount = 300
Withdraw Requirement: Bet Amount >= 1000
Allowed Game Type: (AG,PT)
Allowed Player Level:
Default Player Group Level Name 1
Star VIP Group Level Name 1
Star VIP Group Level Name 2
Star VIP Group Level Name 3

Note: player must have total 3 deposit cnt to avail this promo
Expected Result: Bonus Amount = 300
 */
// 	$this->verifyPromoApplication(1000, 9, 19);
// 	break;

// case 5:
/* Promo Type: deposit promo
Deposit Condition: Non fixed deposit any amount
Deposit Succession: 2nd Deposit
Bonus Release: Fixed Bonus Amount = 100
Withdraw Requirement: Bet Amount >= 5000
Allowed Game Type: (AG,PT)
Allowed Player Level:
Default Player Group Level Name 1
Slot Machine Group Level Name 1
Slot Machine Group Level Name 3
Test4og192 Level Name 1
Apple Level Name 1
Apple Level Name 2

Note: player must have total 1 deposit cnt to avail this promo
Expected Result: Bonus Amount = 100
 */
// 	$this->verifyPromoApplication(500, 10, 19);
// 	break;

// case 6:
/* Promo Type: deposit promo
Deposit Condition: Non fixed deposit any amount
Deposit Succession: 3rd Deposit
Bonus Release: bonus is by percentage = 10% up to max bonus amount = 500
Withdraw Requirement: Bet Amount >= 4000
Allowed Game Type: (AG,PT)
Allowed Player Level:
Default Player Group Level Name 1
Star VIP Group Level Name 1
Star VIP Group Level Name 2
Star VIP Group Level Name 3
VIP Group Level Name 1
VIP Group Level Name 2
VIP Group Level Name 3
Slot Machine Group Level Name 1
Slot Machine Group Level Name 2
Slot Machine Group Level Name 3
Slot Machine Group Level Name 4
Slot Machine Group Level Name 5
Test4og192 Level Name 1
Apple Level Name 1
Apple Level Name 2

Note: player must have total 2 deposit cnt to avail this promo
Expected Result: Bonus Amount = 10% of deposit up to max bonus amount = 500
 */
// 	$this->verifyPromoApplication(100000, 11, 19);
// 	break;

// case 7:
/* Promo Type: deposit promo
Deposit Condition: Non fixed deposit any amount
Deposit Succession: 3rd Deposit
Bonus Release: bonus is by percentage = 10% up to max bonus amount = 500
Withdraw Requirement: Bet Amount = (Deposit + Bonus) x 5
Allowed Game Type: (AG,PT)
Allowed Player Level:
Slot Machine Group Level Name 1
Slot Machine Group Level Name 2
Slot Machine Group Level Name 3
Slot Machine Group Level Name 4
Slot Machine Group Level Name 5

Note: player must have total 2 deposit cnt to avail this promo
Expected Result: Bonus Amount = 10% of deposit up to max bonus amount = 500
 */
// 	$this->verifyPromoApplication(1000000, 12, 19);
// 	break;

// case 8:
/* Promo Type: deposit promo
Deposit Condition: Non fixed deposit amount >= 5000
By Application: Repeat, with repeat condition of Bet Amount = (Deposit + Bonus) x 2 betting times.
No limitation on application
Bonus Release: bonus is by percentage = 5% of deposit amount, up to 200 Max Bonus Amount.
Withdraw Requirement: Bet Amount >= 5000
Allowed Game Type: (AG,PT)
Allowed Player Level:
Default Player Group Level Name 1
Star VIP Group Level Name 1
Star VIP Group Level Name 2

Note: theres no required deposit count
Expected Result: Bonus Amount = 5% of deposit amount, up to 200 Max Bonus Amount.
 */
// 	$this->verifyPromoApplication(10000, 13, 19);
// 	break;

// case 9:
/* Promo Type: deposit promo
Deposit Condition: Non fixed deposit any amount
By Application: Repeat, with repeat condition of Bet Amount = (Deposit + Bonus) x 2 betting times.
With limit 3 time on application
Bonus Release: bonus is by percentage = 60% of deposit amount, up to 1000 Max Bonus Amount.
Withdraw Requirement: Bet Amount = (Deposit + Bonus) x 9
Allowed Game Type: (AG,PT)
Allowed Player Level:
Default Player Group Level Name 1
Star VIP Group Level Name 1
Star VIP Group Level Name 2

Note: theres no required deposit count
Expected Result: Bonus Amount = 60% of deposit amount, up to 1000 Max Bonus Amount.
 */
// 	$this->verifyPromoApplication(10000, 12, 19);
// 	break;

// case 10:
/* Promo Type: deposit promo
Deposit Condition: Non fixed deposit any amount
By Application: No repeat
Bonus Release: Fixed Bonus Amount = 250
Withdraw Requirement: No Bet Requirement
Allowed Game Type: (AG,PT)
Allowed Player Level:
Default Player Group Level Name 1
Star VIP Group Level Name 1
Star VIP Group Level Name 2

Note: theres no required deposit count
Expected Result: Bonus Amount = 250
 */
// 	$this->verifyPromoApplication(10000, 13, 19);
// 	break;

// case 11:
/* Promo Type: non deposit promo (email confirmation)
By Application: No Repeat

Bonus Release: Fixed Bonus Amount = 100
Withdraw Requirement: With bet condition of Bonus x 5
Allowed Game Type: (AG,PT)
Allowed Player Level:
Default Player Group Level Name 1
Star VIP Group Levfel Name 1
Star VIP Group Level Name 2

Note: theres no required deposit count
Expected Result: Bonus Amount = 100
 */
// 	$this->verifyNonDepositPromoApplication(28, 26);
// 	break;

// case 12:
/* Promo Type: non deposit promo (email confirmation)
By Application: No Repeat

Bonus Release: Fixed Bonus Amount = 200
Withdraw Requirement: With bet condition of Bet amount >= 1000
Allowed Game Type: (AG,PT)
Allowed Player Level:
Default Player Group Level Name 1
Star VIP Group Level Name 1
Star VIP Group Level Name 2

Note: theres no required deposit count
Expected Result: Bonus Amount = 200
 */
// 	$this->verifyNonDepositPromoApplication(28, 19);
// 	break;

// case 13:
/* Promo Type: non deposit promo (registration promo)
By Application: No Repeat

Bonus Release: Fixed Bonus Amount = 300
Withdraw Requirement: With bet condition of Bet amount >= 5000
Allowed Game Type: (AG,PT)
Allowed Player Level:
Default Player Group Level Name 1
Star VIP Group Level Name 1
Star VIP Group Level Name 2

Note: theres no required deposit count
Expected Result: Bonus Amount = 300
 */
// 	$this->verifyNonDepositPromoApplication(30, 19);
// 	break;

// case 14:
/* Promo Type: non deposit promo (complete registration promo)
By Application: No Repeat

Bonus Release: Fixed Bonus Amount = 150
Withdraw Requirement: With bet condition of Bonus x 5
Allowed Game Type: (AG)
Allowed Player Level:
Default Player Group Level Name 1
Star VIP Group Level Name 1
Star VIP Group Level Name 2

Note: theres no required deposit count
Expected Result: Bonus Amount = 150
 */
// 	$this->verifyNonDepositPromoApplication(31, 19);
// 	break;

// case 15:
/* Promo Type: non deposit promo (by betting)
By Application: Repeat, No Limit

Bonus Release: Fixed Bonus Amount = 30
Withdraw Requirement: With bet condition of Bonus x 5
Allowed Game Type: (ALL AG), Bet Requirement: 300
Allowed Player Level:
Default Player Group Level Name 1
Star VIP Group Level Name 1
Star VIP Group Level Name 2
VIP Group Level Name 3
Slot Machine Group Level Name 4

Note: theres no required deposit count
Expected Result: Bonus Amount = 30
 */
// 	$this->verifyNonDepositPromoApplication(32, 19);
// 	break;

// case 16:
/* Promo Type: non deposit promo (by lossing)
By Application: Repeat, Limit 5 times
Repeat Condition, Bet Amount = Bonus x 5 Betting times

Bonus Release: Fixed Bonus Amount = 200
Withdraw Requirement: With bet condition of Bet amount = Bonus x 5
Allowed Game Type: (ALL AG), Bet Requirement: 300
Allowed Player Level:
Default Player Group Level Name 1
Star VIP Group Level Name 1
Star VIP Group Level Name 2
VIP Group Level Name 3
Slot Machine Group Level Name 4

Note: theres no required deposit count
Expected Result: Bonus Amount = 30
 */
// 	$this->verifyNonDepositPromoApplication(33, 20);
// 	break;

// case 17:
/* Promo Type: non deposit promo (by winning)
By Application: Repeat, Limit 5 times
Repeat Condition, Bet Amount = Bonus x 2 betting times

Bonus Release: Fixed Bonus Amount = 345
Withdraw Requirement: With bet condition of Bet amount = Bonus x 5
Allowed Game Type: (ALL AG), Bet Requirement: 300
Allowed Player Level:
Default Player Group Level Name 1
Star VIP Group Level Name 1
Star VIP Group Level Name 2
VIP Group Level Name 3
Slot Machine Group Level Name 4

Note: theres no required deposit count
Expected Result: Bonus Amount = 345
 */
// 			$this->verifyNonDepositPromoApplication(34, 20);
// 			break;

// 		default:
// 			# code...
// 			break;
// 	}
// }
///end of file/////////////