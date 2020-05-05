<?php
require_once dirname(__FILE__) . '/base_testing.php';

class Testing_model_sms_verification extends BaseTesting {

	# overload parent functions
	public function init() {
		$this->load->model('sms_verification');
		$this->test(true, isset($this->sms_verification), 'Test sms_verification model class loaded. Expected: [true], Actual: ['.($this->sms_verification ? 'true' : 'false').']');
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

	public function testGetAndVerifyCode() {
		$code = $this->sms_verification->getVerificationCode('test_session');
		$this->utils->debug_log("Get verification code: [$code]");
		$this->test(true, $code >= 10000, "Test get verification code: [$code]");
		$this->sms_verification->setVerified('test_session', $code);
		$this->utils->debug_log("Verification code verified: [$code]");
	}
}
