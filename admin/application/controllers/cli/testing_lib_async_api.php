<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_async_api extends BaseTesting {

	public function init() {
		$this->load->library('utils');
		$this->test($this->utils != null, true, 'init utls');
	}

	public function testAll() {
		$this->init();
	}

	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	private function createSign($params, $key){

		// $token= $params['token'];

		// unset($params['token']);

		$values=array_values($params);

		//sort params and append
		ksort($values, SORT_STRING);

		$original=$key.implode('', $values);

		$md5=md5($original);

		$this->utils->debug_log('original', $original, 'md5', $md5);

		return $md5;

	}

	private function testCallTransfer(){
		$key='7b99796981dccd538f23ff194b630d6f';
		$apply_site='fn139';
		$apply_type='birdegg';
		$receive_type='main_wallet';
		$bal= 3;
		$playerUsername='test002';
		$time=time();
		$url='http://player.le8878.com/async/call_transfer/'.FN139_PAYMENT_API;
		$this->utils->debug_log('call url', $url);
		$params=['api'=>'transfer', 'apply_site'=>$apply_site, 'apply_username'=>'test000',
			'apply_type'=>$apply_type, 'order_id'=>random_string('unique'), 'num'=> $bal,
			'receive_username'=> $playerUsername, 'receive_type'=> $receive_type, 'time'=>$time];

		$token=$this->createSign($params, $key);
		$params['token']=$token;

		$response=null;
		try{
			$response=\Httpful\Request::post($url, $params)
				->sendsType(\Httpful\Mime::FORM)
				// ->useSocks5Proxy('127.0.0.1','1080')
			    ->expectsPlain()
			    ->send();
			    // ->expectsJson()
			$this->utils->debug_log($response->raw_body);
		}catch(Exception $e){
			$this->utils->error_log($e, $response->raw_body);
		}

		$success=!$response->hasErrors();

		$this->test($success, true, 'call transfer api');

		$this->utils->debug_log('body', $response->body);

		$result=json_decode($response->body);

		$this->test($result->state, 'succeed', 'call transfer api');
		$this->test($result->errorNum, 0, 'call transfer api');

	}

}
