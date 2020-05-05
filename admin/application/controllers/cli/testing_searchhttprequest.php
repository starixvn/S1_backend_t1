<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_searchHttpRequest extends BaseTesting {

	
	public function init() {
		$this->load->model('duplicate_account_setting');
	}
	//should overwrite testAll
	public function testAll() {
		//init first
		$this->init();
		//call your test function
		$this->searchHttpRequest();
	}

	public function searchHttpRequest(){
		$player_id =111;
		$request = 'referrer';
		/*$field = 'playerId';
		$type = 1;
		$value = array('');
		$result = $this->duplicate_account_setting->searchHttpRequest($player_id, $field, $type, $value);
		echo $result;*/

		$player_http_request = $this->duplicate_account_setting->getHTTPRequest($player_id);
		$list_by_type = array();

		foreach ($player_http_request as $key => $value) {
			if (!isset($list_by_type[$value['type']])) {
				//checking if request type is created in array or not
				$list_by_type[$value['type']] = array();
			}
			array_push($list_by_type[$value['type']], $value[$request]);
		}

		$duplicates = array();
		//echo "<pre>";print_r($list_by_type);exit;

		foreach ($list_by_type as $key => $value) {
			$duplicates[$key] = $this->duplicate_account_setting->searchHttpRequest($player_id, $request, $key, $value);
		}
		echo "<pre>";print_r($duplicates);exit;
	}

}
?>