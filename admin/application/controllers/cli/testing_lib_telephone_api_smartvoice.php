<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_telephone_api_smartvoice extends BaseTesting {

	private $platformCode = SMARTVOICE_TELEPHONE_API;
	private $api = null;

	# overload parent functions

	public function createTelExternalSystem($external_id) {
		$isInsert = true;
		$this->load->model('external_system');
		$data = $this->external_system->getPredefinedSystemById($external_id);
		if ($this->external_system->isAnyEnabledApi($external_id)) {
			$isInsert = false;
		}

		if ($data && isset($data->system_type) && $data->system_type == 4) {
			$teleApiInfo = $this->config->item('tele_api_setting');

			$live_url = $teleApiInfo['live_url'];
			$live_key = $teleApiInfo['live_key'];

			if (!$live_url || !$live_key) {
				echo "tele config incomplete.";
				return;
			}

			$data->live_url = $live_url;
			$data->live_key = $live_key;
			$data = json_decode(json_encode($data), true);
			if ($isInsert) {
				$rlt = $this->external_system->addTeleApi($data);
			} else {
				unset($data['id']);
				$rlt = $this->external_system->updateTeleApi($data, $external_id);
			}

			if ($rlt == false) {
				echo "add tele api faild.";
			} else {
				echo "add tele api success.";
			}
		} else {
			echo "tele api is unexist.";
		}
	}

	public function init() {
		list($loaded, $apiClassName) = $this->utils->loadExternalSystemLib($this->platformCode);

		$this->test($loaded, true, 'Is API class loaded. Expected: true');
		if (!$loaded) {
			$this->utils->debug_log("Error: API not loaded, platformCode = " . $this->platformCode);
			return;
		}

		$this->test($apiClassName, 'telephone_api_smartvoice', 'Test loaded API\'s class name. Expected: [telephone_api_smartvoice], Actual: ['.$apiClassName.']');
		$this->api = $this->$apiClassName;
		$this->test($this->api->getPlatformCode(), $this->platformCode, 'Test loaded API\'s platform code. Expected: [' . $this->platformCode.'], Actual: ['.$this->api->getPlatformCode().']');
		$this->test($this->api->getPrefix(), 'smartvoice', 'Test loaded API\'s prefix. Expected: [tonghui], Actual: ['.$this->api->getPrefix().']');
	}

	## all tests route through this function
	public function testTarget($methodName) {
		#$this->init();
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

	private function testGetCallUrl(){
		$phoneNumber = '18926154567';
		$callerId = '8001';
		$callUrl = $this->api->getCallUrl($phoneNumber, $callerId);
		$this->test(strpos($callUrl, 'dia_num=2616375646609'), true, 'API must correctly encrypt phone number and include in the call URL.');
	}

	private function testAff() {
		$this->load->model('affiliatemodel');
		var_dump($this->affiliatemodel->getAffiliateHierarchy());
	}

}