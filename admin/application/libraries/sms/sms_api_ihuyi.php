<?php
session_start();
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * ihuyi SMS API implementation
 * http://106.ihuyi.com/webservice/sms.php?method=Submit
 *
 * Config items:
 * * Sms_api_ihuyi_apikey
 */
class Sms_api_ihuyi extends Abstract_sms_api {
	const SUCCESS_CODE = "2";

    public function getUrl() {
        return $this->getParam('url') ? $this->getParam('url') : 'http://106.ihuyi.com/webservice/sms.php?method=Submit';
    }

	protected function signContent($content) {
		return sprintf("【%s】%s", $this->CI->config->item('sms_from'), $content);
	}

	protected function configCurl($handle, $mobile, $content) {
		$url = $this->getUrl();
		$fields = $this->getFields($mobile, $content);
		$fields_string = http_build_query($fields);

		curl_setopt($handle, CURLOPT_URL,$url);
		curl_setopt($handle, CURLOPT_POSTFIELDS,$fields_string);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_TIMEOUT, 10);
		curl_setopt($handle, CURLOPT_POST, 1);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

		return $fields;
	}

	public function getFields($mobile, $content) {
		$fields = array(
			'account'  => $this->getParam('api_id'),
			'password' => $this->getParam('api_key'),
			'mobile'   => $mobile,
			'content'  =>  $content
		);
		$this->utils->debug_log("=================================ihuyi SMS fields", $fields);
		return $fields;
	}

	public function getErrorMsg($returnQueryString) {
        $obj = simplexml_load_string($returnQueryString);
        $arr = $this->utils->xmlToArray($obj);

        $message = 'Unknown error!';
        if(isset($arr['code'])){
        	if(isset($arr['msg'])){
        		$message = $arr['code'].': '.$arr['msg'];
        	} else {
        		$message = $arr['code'].': '.self::ERROR_MSG[$arr['code']];
        	}
        }

		return $message;
	}

	public function isSuccess($returnQueryString) {
		$result = false;
        $obj = simplexml_load_string($returnQueryString);
        $arr = $this->utils->xmlToArray($obj);

        if(isset($arr['code']) && $arr['code'] == self::SUCCESS_CODE){
        	$result = true;
        }

		$this->utils->debug_log("=================================ihuyi SMS isSuccess resp", $arr);
		return $result;
	}

	protected function getParam($name) {
		return $this->CI->config->item('sms_api_ihuyi_'.$name);
	}

	# Reference: https://developer.ihuyi.com/api/sms
	const ERROR_MSG = array(
		'0'     => '提交失败',
		'2'     => '提交成功',
		'400'   => '非法 ip访问',
		'401'   => '帐号不能为空',
		'402'   => '密码不能为空',
		'403'   => '手机号码不能为空',
		'4030'  => '手机号码已被列入黑名单',
		'404'   => '短信内容不能为空',
		'405'   => 'API ID或API KEY 不正确',
		'4050'  => '账号被冻结',
		'40501' => '动态密码已过期',
		'40502' => '动态密码校验失败',
		'4051'  => '剩余条数不足',
		'4052'  => '访问 ip与备案ip不符',
		'406'   => '手机号码格式不正确',
		'407'   => '短信内容含有敏感字符',
		'4070'  => '签名格式不正确',
		'4071'  => '没有提交备案模板',
		'4072'  => '提交的短信内容与审核通过的模板内容不匹配',
		'40722' => '变量内容超过指定的长度【8】',
		'4073'  => '短信内容超出长度限制',
		'4074'  => '短信内容包含 emoji 符号',
		'4075'  => '签名未通过审核',
	);
}
