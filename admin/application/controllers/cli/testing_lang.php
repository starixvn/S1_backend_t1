<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lang extends BaseTesting {

	# overload parent functions
	public function init() {

		return true;
	}

	## all tests route through this function
	public function testTarget($methodName) {
		if($this->init()) {
			$this->$methodName();
		}
	}

	# Actual Tests
	## Invokes all tests defined below. A test function's name should begin with 'test'
	public function testAll() {
		$classMethods = get_class_methods($this);
		$excludeMethods = array('test', 'testTarget', 'testAll');
		foreach($classMethods as $method){
			if(strpos($method, 'test') !== 0 || in_array($method, $excludeMethods)) {
				continue;
			}

			# Clear loaded language before each test call
			$this->lang->is_loaded = array();
			$this->lang->language = array();

			$this->$method();
		}
	}

	public function testLoadEnglish() {
		$this->lang->load('main', 'english');
		$this->test(lang('lang.status'), 'Status', 'Loaded language = [english], lang key = [lang.status], expected: [Status], actual: ['.lang('lang.status').']');
	}

	public function testLoadChinese() {
		$this->lang->load('main', 'chinese');
		$this->test(lang('lang.status'), '状态', 'Loaded language = [chinese], lang key = [lang.status], expected: [状态], actual: ['.lang('lang.status').']');
	}

	public function testJson() {
		$this->test(lang('_json:{"1":"testJson - en", "2":"testJson - cn"}'), "testJson - en", "Testing json format lang key. expected: [testJson - en], actual: [".lang('_json:{"1":"testJson - en", "2":"testJson - cn"}')."]");
		$this->test(lang('_json:{invalid}'), "{invalid}", "Testing invalid json format lang key. expected: [{invalid}], actual: [".lang('_json:{invalid}')."]");
	}
}