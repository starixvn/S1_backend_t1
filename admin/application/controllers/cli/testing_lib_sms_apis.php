<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_sms_apis extends BaseTesting {

	# overload parent functions
	public function init() {
		$this->load->model('sms_verification');
		$this->load->library('sms/sms_sender');
		$this->load->library('sms/sms_api_sunmax');
		$this->load->library('sms/sms_api_dingdong');
		$this->load->library('sms/sms_api_santo');
		$this->load->library('sms/sms_api_ucpaas');
		$this->load->library('sms/sms_api_luosimao');
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

	public function testGetGlobalSmsCount() {
		$count = $this->sms_verification->getVerificationCodeCountPastMinute();

		$this->test($count >= 0, true, "Testing global sms count: [$count]");
	}

	public function testSunmax(){
		$api = $this->sms_api_sunmax;

		$apiFields = $api->getFields('13000000000', 'This is a test SMS.');
		$this->test(count($apiFields), 4, 'Testing number of fields from sunmax API');
		$this->test($apiFields['CorpID'], $this->config->item('Sms_api_sunmax_CorpID'), 'Testing field CorpID from sunmax API.');
		$this->test($apiFields['Pwd'], $this->config->item('Sms_api_sunmax_Pwd'), 'Testing field Pwd from sunmax API.');

		$code = '1234';
		$mobileNumber ='13000000000';
		$sms_content_template=$this->utils->getConfig('sms_content_template');
		$msg = sprintf($sms_content_template, $code);

		$sendResult = $this->sms_sender->send($mobileNumber, $msg, $api);
		$this->test($sendResult, true, 'Sending of SMS through Sunmax must succeed');
	}

	public function testDingdong(){
		$api = $this->sms_api_dingdong;

		$apiFields = $api->getFields('13000000000', 'This is a test SMS.');
		$this->test(count($apiFields), 3, 'Testing number of fields from dingdong API');
		$this->test($apiFields['apikey'], $this->config->item('Sms_api_dingdong_apikey'), 'Testing field apikey from dingdong API.');

		$code='1234';
		$mobileNumber='13000000000';
		$sms_content_template=$this->utils->getConfig('sms_content_template');
		$msg = sprintf($sms_content_template, $code);

		$sendResult = $this->sms_sender->send($mobileNumber, $msg, $api);
		$this->test($sendResult, true, 'Sending of SMS through dingdong cloud must succeed');
	}

	public function testSanto() {
		$api = $this->sms_api_santo;

		$apiFields = $api->getFields('13000000000', 'This is a test SMS.');
		$this->test(count($apiFields), 6, 'Testing number of fields from Santo API');
		$this->test($apiFields['cpid'], $this->config->item('Sms_api_santo_cpid'), 'Testing field cpid from Santo API, expected: '.$this->config->item('Sms_api_santo_cpid').', actual: '.$apiFields['cpid']);
		# Make sure default country code is prefixed to mobile number
		$this->test($apiFields['da'], $this->config->item('sms_default_country').'13000000000', 'Destination mobile number must prefix with default country code');

		# Below is the fixed content for sandbox testing
		$code='1234';
		$mobileNumber='861300000000';
		$sms_content_template=$this->utils->getConfig('sms_content_template');
		$msg = sprintf($sms_content_template, $code);

		$sendResult = $this->sms_sender->send($mobileNumber, $msg, $api);
		$this->test($sendResult, true, 'Sending of SMS through Santo must succeed');
	}

	public function testUcpaas() {
		$api = $this->sms_api_ucpaas;

		$templateContent = array(
			'templateId' => '26535', # Register new account
			'templateParam' => 'testUser,1234' # Format ref: README.md in DEMO php code
			# Expected SMS:【V8】尊敬的会员：testUser，您的注册验证码为：1234，欢迎登入官方网站进行游戏体验。
		);

		$sendResult = $this->sms_sender->send('13000000000', $templateContent, $api);
		$this->test($sendResult, true, 'Sending of SMS through Ucpaas must succeed');
	}

	public function testUcpaasBalance() {
		$api = $this->sms_api_ucpaas;
		$balanceString = $api->getBalanceString();
		$this->test($balanceString != null, true, 'Getting SMS balance string: '.$balanceString);
	}

	public function testLuosimao() {
		$api = $this->sms_api_luosimao;

		# Note: Please config sms_from properly for this test to succeed
		$regUserName = 'TEST';
		$code = '1234';
		$mobileNumber = '13761428267'; # number taken from official documentation
		$sms_content_template=$this->utils->getConfig('sms_content_template');
		$msg = sprintf($sms_content_template, $regUserName, $code);

		$sendResult = $this->sms_sender->send($mobileNumber, $msg, $api);
		$this->test($sendResult, true, 'Sending of SMS through luosimao must succeed');
	}

	public function testLuosimaoBalance() {
		$api = $this->sms_api_luosimao;
		$balanceString = $api->getBalanceString();
		$this->test($balanceString != null, true, 'Getting SMS balance string: '.$balanceString);
	}
}
