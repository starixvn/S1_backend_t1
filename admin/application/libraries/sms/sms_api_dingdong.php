<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * Dingdong Cloud SMS API implementation
 * http://www.dingdongcloud.com
 *
 * Config items: Sms_api_dingdong_apikey
 */
class Sms_api_dingdong extends Abstract_sms_api {
	const SUCCESS_CODE = 1;
	const ERROR_MSG = array(
		"-1" => "用户账户名与密码不匹配",
		"0" => "用户短信发送失败",
		"1" => "用户短信发送成功，或查询数据成功",
		"2" => "用户余额不足",
		"3" => "用户扣费失败",
		"4" => "用户输入账户名错误，无效号码",
		"5" => "用户输入短信内容错误，内容为空或者格式错误非utf-8编码",
		"6" => "用户无相关权限,开通请联系管理员（一般情况下未开通营销权限）",
		"7" => "ip错误(账户设置的ip白名单中不包含该ip地址)",
		"8" => "同一号码提交次数过多(半小时超过5条)",
		"9" => "签名为空(必须带有【】格式的签名)",
		"10" => "营销内容错误(请添加'退订回T'作为结尾)",
		"11" => "签名为空(签名未审核,请联系客服)",
		"12" => "maxSize值不正确(请保证在1-1024之间)",
		"13" => "内容匹配模板失败,请先创建验证码模板",
		"14" => "语音验证码内容错误(只能是4-6位数字)",
		"15" => "语音验证码内容错误只能是4-6位数字(号码中含有错误号码,请确认后提交)",
		"16" => "模板中未含有【#验证码#】",
		"17" => "该签名已经存在,请不要重复添加",
		"18" => "短信内容为空",
		"90" => "参数错误(请确认参数名是否正确)",
		"99" => "系统内部异常(请联系管理员)",
	);

	protected function signContent($content) {
		return $content;
		// return sprintf("【%s】%s", $this->CI->config->item('sms_from'), $content);
	}

	# Reference: http://wiki.dingdongcloud.com/doku.php?id=接口文档api#tab__php
	protected function configCurl($handle, $mobile, $content) {
		$url = $this->getUrl();
		$content = $this->signContent($content);
		$fields = $this->getFields($mobile, $content);
		$fields_string =http_build_query($fields);

		curl_setopt($handle,CURLOPT_URL,$url);
		curl_setopt($handle,CURLOPT_POST,count($fields));
		curl_setopt($handle,CURLOPT_POSTFIELDS,$fields_string);
		curl_setopt($handle,CURLOPT_RETURNTRANSFER,1);

		curl_setopt($handle, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_TIMEOUT, 10);
		curl_setopt($handle, CURLOPT_POST, 1);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

		return $fields;
	}

    public function getUrl() {
        return $this->getParam('url') ? $this->getParam('url') : 'https://api.dingdongcloud.com/v1/sms/sendyzm';
    }

	public function getFields($mobile, $content) {
		# note the $content should be in UTF-8 format
		$fields = array(
			'apikey' => $this->getParam('apikey'),
			'mobile' => $mobile,
			'content' => urlencode($content),
		);
		$this->utils->debug_log("Dingdong Cloud SMS fields", $fields);
		return $fields;
	}

	# $returnJson sample: {"code":4,"msg":"手机为空","result":null}
	public function getErrorMsg($returnJson) {
		$params = json_decode($returnJson, true);
		$this->utils->error_log("Dingdong Cloud SMS return value", $params);
		return sprintf("%s (%s)", self::ERROR_MSG[$params['code']], $params['msg']);
	}

	public function isSuccess($returnJson) {
		$params = json_decode($returnJson, true);
		return $params['code'] == self::SUCCESS_CODE;
	}

	protected function getParam($name) {
		return $this->CI->config->item('Sms_api_dingdong_'.$name);
	}
}
