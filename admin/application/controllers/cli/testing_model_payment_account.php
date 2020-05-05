<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_model_payment_account extends BaseTesting {

	private $subWalletId;
	private $bigWallet;

	public function init() {
		$this->load->model(array('payment_account'));
	}

	## all tests route through this function
	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	# Actual Tests
	## Invokes all tests defined below. A test function's name should begin with 'test'
	public function testAll() {
		$classMethods = get_class_methods($this);
		$excludeMethods = array('test', 'testTarget', 'testAll');
		foreach ($classMethods as $method) {
			if (strpos($method, 'test') !== 0 || in_array($method, $excludeMethods)) {
				continue;
			}

			$this->$method();
		}
	}

	private function testGetTransFee() {
		$paymentAccountId = '32';

		$this->test($this->payment_account->getTransactionFee($paymentAccountId, 100), 9, 'Test transaction fee');
		$this->test($this->payment_account->getTransactionFee($paymentAccountId, 2), 1.5, 'Test min transaction fee');
		$this->test($this->payment_account->getTransactionFee($paymentAccountId, 1000000), 50, 'Test max transaction fee');
	}
}
