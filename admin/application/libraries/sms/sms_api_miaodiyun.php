<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * miaodiyun SMS API implementation
 * http://www.miaodiyun.com/home
 *
 * Config items:
 * * Sms_api_miaodiyun_apikey
 */
class Sms_api_miaodiyun extends Abstract_sms_api {
	const SUCCESS_CODE = "00000";

	public function getUrl() {
		return $this->getParam('url') ? $this->getParam('url') : 'https://api.miaodiyun.com/20150822/industrySMS/sendSMS';
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

	# note the $content should be in UTF-8 format
	# Reference: https://miaodiyun.com/docs/api
	public function getFields($mobile, $content) {
		$fields = array(
			'accountSid' => $this->getParam('account_sid'),
			'smsContent' => $this->signContent($content),
			'to' => $mobile,
			'timestamp' => date('Ymdhis')
		);
		$fields['sig'] = $this->sign($fields);
		$this->utils->debug_log("miaodiyun SMS fields", $fields);
		return $fields;
	}

	public function sign($fields){
		$account_sid = $this->getParam('account_sid');
		$auth_token = $this->getParam('auth_token');
		$timestamp = $fields['timestamp'];
		$signStr = md5($account_sid.$auth_token.$timestamp);
		return $signStr;
	}

	public function getErrorMsg($returnQueryString) {
		$params = json_decode($returnQueryString, true);
		$this->utils->debug_log("===============Sms_api_miaodiyun getErrorMsg ", $params);
		return self::ERROR_MSG[$params['respCode']];
	}

	public function isSuccess($returnQueryString) {
		$params = json_decode($returnQueryString, true);
		return $params['respCode'] == self::SUCCESS_CODE;
	}

	protected function getParam($name) {
		return $this->CI->config->item('sms_api_miaodiyun_'.$name);
	}

	# Reference: https://developer.miaodiyun.com/api/sms
	const ERROR_MSG = array(
		'00000' => '请求成功',
		'00001' => '未知错误，请联系技术客服。',
		'00002' => '未知的方法名',
		'00003' => '请求方式错误',
		'00004' => '参数非法，如request parameter (key) is missing',
		'00005' => 'timestamp已过期',
		'00006' => 'sign错误',
		'00007' => '重复提交',
		'00008' => '操作频繁',
		'00011' => '请求的xml格式不对',
		'00012' => '不支持get请求，请使用post',
		'00013' => '请求url格式不正确',
		'00015' => '时间戳超出有效时间范围',
		'00016' => '请求json格式不对',
		'00017' => '数据库操作失败',
		'00018' => '参数为空',
		'00019' => '订单已存在',
		'00020' => '用户不存在',
		'00021' => '子账号余额不足',
		'00022' => '操作频繁',
		'00023' => '开发者余额不足',
		'00025' => '手机格式不对',
		'00026' => '手机号存在',
		'00027' => '子账号名称已存在',
		'00028' => '子账号名称过长',
		'00029' => '回调开发者服务器异常',
		'00030' => '回调地址为空',
		'00031' => 'appId为空或者没有传值',
		'00032' => '主叫号码为空或者没有传值',
		'00033' => '被叫号码为空或者没有传值',
		'00034' => '子账号为空或者没有传值',
		'00035' => '主叫号码和被叫号码相同',
		'00036' => '验证码格式不对（4-8位数字',
		'00037' => 'limit格式不对',
		'00038' => 'start格式不对',
		'00039' => '验证码为空或者缺少此参数',
		'00040' => '用户名或者密码错误',
		'00050' => '短信或者语音验证码错误',
		'00051' => '显示号码与被叫号码一样，不允许呼叫',
		'00052' => '回拨主叫号码格式错误',
		'00053' => '被叫号码格式错误',
		'00054' => '显号格式错误',
		'00055' => '应用不包含此子账号',
		'00056' => '开发者不包含此应用',
		'00060' => '请求数据不存在',
		'00061' => 'app不存在',
		'00062' => 'developerId 请求错误',
		'00063' => 'app未上线',
		'00064' => '请求Content-Type错误',
		'00065' => '请求Accept错误',
		'00066' => '开发者余额已被冻结',
		'00070' => '手机号未绑定',
		'00071' => '通知类型已停用或者未创建',
		'00072' => 'balance格式不对（必须为大于等于0的double）',
		'00073' => 'charge格式不对（必须为大于等于0的double）',
		'00074' => '主叫和子账户绑定的手机号不相同',
		'00075' => '子账户没有绑定手机号',
		'00076' => '时间格式不对',
		'00077' => '开始时间小于结束时间',
		'00078' => '开始时间和結束時間必須是同一天',
		'00079' => '服务器内部异常',
		'00080' => '子账号不存在',
		'00081' => '通知计费系统失败',
		'00082' => '参数校验失败',
		'00083' => '充值失败',
		'00084' => '子账号没有托管 不能进行充值',
		'00085' => '开发者不包含子帐号',
		'00086' => 'DEMO不能进行充值',
		'00087' => 'IQ类型错误',
		'00090' => '回调地址为空',
		'00091' => '没有语音',
		'00093' => '没有这个语音文件或者审核没通过',
		'00094' => '每批发送的手机号数量不得超过100个',
		'00098' => '同一手机号每天只能发送n条相同的内容',
		'00099' => '相同的应用每天只能给同一手机号发送n条不同的内容',
		'00100' => '短信内容不能含有关键字或者审核不通过',
		'00101' => '配置短信端口号失败',
		'00102' => '一个开发者只能配置一个端口',
		'00104' => '相同的应用当天给同一手机号发送短信的条数小于等于n',
		'00105' => '本开发者只能发短信给移动手机',
		'00106' => '时间戳(timestamp)参数为空',
		'00107' => '签名(sig)参数为空',
		'00108' => '时间戳(timestamp)格式错误',
		'00109' => '子账号已被关闭',
		'00110' => '解析post数据失败，post数据不符合格式要求',
		'00111' => '匹配到黑名单',
		'00112' => 'accountSid参数为空',
		'00113' => '短信内容和模板匹配度过低',
		'00114' => 'clientNumber参数为空',
		'00115' => 'charge参数为空',
		'00116' => 'charge格式不对，不能解析成double',
		'00117' => 'fromTime参数为空',
		'00118' => 'toTime参数为空',
		'00119' => 'fromTime参数格式不正确',
		'00120' => 'toTime参数格式不正确',
		'00122' => 'date参数为空',
		'00123' => 'date的值不在指定范围内',
		'00124' => '没有查询到话单（所以没有生成下载地址）',
		'00125' => 'emailTemplateId参数为空',
		'00126' => 'to参数为空',
		'00127' => 'param参数个数不匹配',
		'00128' => 'emplateId参数为空',
		'00129' => '模板类型错误',
		'00130' => 'serviceType参数为空',
		'00131' => 'content参数为空',
		'00133' => '错误的业务类型',
		'00134' => '没有和内容匹配的模板',
		'00135' => '应用没有属于指定类型业务并且已审核通过、已启用的模板',
		'00136' => '开发者不能调用此接口',
		'00174' => '一分钟内下发短信超过次数限制',
		'00138' => '短信没有签名不能发送',
		'00139' => '短信签名已进入黑名单不能发送',
		'00141' => '一小时内发送给单个手机次数超过限制',
		'00142' => '一天内发送给单个手机次数超过限制',
		'00143' => '含有非法关键字',
		'00144' => 'mobile参数为空',
		'00145' => '新手机号和旧手机号相同，不必修改',
		'00146' => 'minutes格式不对（必须为大于等于0的double）',
		'00147' => '被叫次数超限',
		'00148' => '主叫次数超限',
		'00149' => '流量包大小格式错误',
		'00150' => '找不到匹配的流量包',
		'00151' => '该签名下的手机号码黑名单',
		'00152' => '端口号已被关闭',
		'00153' => '未知的手机号运营商',
		'00154' => '开发者无权限给此号码发短信',
		'00155' => '流量充值提交失败',
		'00156' => 'packageId为空或者没有传值',
		'00157' => 'packageId不存在',
		'00158' => '不允许发验证码',
		'00159' => '超过每秒发送频率限制',
		'00160' => '没有发送会员通知推广类短信权限',
		'00161' => '短信签名没有报备',
		'00162' => '没有发送营销短信权限',
		'00163' => '会员营销短信内容必须包含退订',
		'00164' => '端口号非法',
		'00165' => '关键字等待审核',
		'00166' => 'IP非法',
		'00167' => 'TemplateId错误',
		'00168' => 'TemplateId未审核或未启用',
		'00169' => 'param参数错误',
		'00171' => '变量长度超长',
		'00172' => '短信内容长度超长',
		'00173' => '变量内容不能含有中文',
		'00175' => '不完整的长短信',
		'00176' => 'IP已被锁定',
		'00177' => 'accountSid已被锁定',
		'00178' => '无语音验证码权限',
		'00179' => '发送会员营销短信需要先认证'
	);
}
