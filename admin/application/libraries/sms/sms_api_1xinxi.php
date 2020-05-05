<?php

require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * url : http://www.1xinxi.cn/
 *
 * need default params : name, pwd, sign
 *
 */
class Sms_api_1xinxi extends Abstract_sms_api {

    private $sms_name;
    private $sms_pwd;
    private $sms_sign;
    private $sms_type = 'pt';
    private $standardParams = ['name', 'pwd', 'sign'];

    private $api_url = 'http://web.1xinxi.cn/asmx/smsservice.aspx';

    public function __construct()
    {
        parent::__construct();

        $smsInfo = $this->CI->config->item('Sms_api_1xinxi');
        $this->sms_name = $smsInfo['name'];
        $this->sms_pwd  = $smsInfo['pwd'];
        $this->sms_sign = $smsInfo['sign'];

        if (!$this->validateDefaultParam()) {
            throw new Exception("Default params must be set in config file");
        }
    }

    private function validateDefaultParam()
    {
        $standardParams = $this->standardParams;
        foreach ($standardParams as $param) {
            $param = 'sms_' . $param;
            if (empty($this->$param)) return false;
        }
        return true;
    }

    public function getUrl()
    {
        return $this->api_url;
    }

    public function getFields($mobile, $content)
    {
        $fields = [
            'mobile'  => $mobile,
            'content' => $content,
            'type'    => $this->sms_type,
            'sign'    => $this->sms_sign,
            'name'    => $this->sms_name,
            'pwd'     => $this->sms_pwd
        ];

        $this->utils->debug_log("1xinxi SMS fields", $fields);
        return $fields;
    }

    public function getErrorMsg($response)
    {
        $rspInfo = explode(',', $response);
        $code = $rspInfo[0];
        if ($code != 0) {
            return $this->responseCodeMsg($code);
        }
    }

    public function isSuccess($response)
    {
        $rspInfo = explode(',', $response);
        $code = $rspInfo[0];
        return  ($code == 0) ? true : false;
    }

    public function responseCodeMsg($code)
    {
        $codeMsg = [
            '0' => '提交成功',
            '1' => '含有敏感词汇',
            '2' => '余额不足',
            '3' => '手机号码不存在',
            '4' => '包含sql语句',
            '10' => '账号不存在',
            '11' => '账号注销',
            '12' => '账号停用',
            '13' => 'IP鉴权失败',
            '14' => '格式错误',
            '-1' => '系统异常'
        ];

        return (isset($codeMsg[$code])) ? $codeMsg[$code] : "The error Code: $code has not translate";
    }
}
