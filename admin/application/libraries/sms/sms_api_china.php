<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * 中國簡訊發送
 * http://122.146.17.51:30080/isr017/SMSBridgeEx.php
 *
 * Config items:
 * * sms_api_china_memberid
 * * sms_api_china_password
 */
class Sms_api_china extends Abstract_sms_api {
    const SUCCESS_CODE = "0";

    protected function signContent($content) {
        return sprintf("%s【%s】", $content, $this->CI->config->item('sms_from'));
    }

    public function getUrl() {
        return $this->getParam('url') ? $this->getParam('url') : "http://122.146.17.51:30080/isr017/SMSBridgeEx.php";
    }

    public function getFields($mobile, $content) {
        $fields = array(
            'MemberID' => $this->getParam('memberid'),
            'Password' => $this->getParam('password'),
            'MobileNo' => $mobile,
            'SMSMessage' => $content,
            'SourceProdID' => date("Y-m-d H:i:s"),
            'SourceMsgID' => time()
        );

        $fields['Password'] = $this->sign($fields);
        $this->utils->debug_log("===============china SMS fields", $fields);
        return $fields;
    }

    public function getErrorMsg($returnQueryString) {
        parse_str($returnQueryString, $resp);
        $returnInfo = $this->returnInfo();
        $errorCode = $resp['status'];
        if(!array_key_exists($errorCode, $returnInfo)) {
            $this->utils->error_log("===============china SMS return UNKNOWN ERROR!", $returnQueryString);
            $errorDesc = "未知错误";
        }
        else {
            $errorDesc = $returnInfo[$errorCode];
            $this->utils->error_log("===============china SMS return [$errorCode]: $errorDesc", $returnQueryString);
        }
        return $errorCode.": ".$errorDesc;
    }

    public function isSuccess($returnQueryString) {
        parse_str($returnQueryString, $resp);
        if( ($resp['status'] == self::SUCCESS_CODE) && ($resp['retstr'] != 'Retry_Success') )
            return true;
        else
            return false;
    }


    protected function getParam($name) {
        return $this->CI->config->item('sms_api_china_'.$name);
    }

    private function sign($fields){
        $signStr = $fields['MemberID'].':'.$fields['Password'].':'.$fields['SourceProdID'].':'.$fields['SourceMsgID'];
        return md5($signStr);
    }

    public function returnInfo() {
        $returnInfo = array(
            '0'  => '發送成功',
            '1'  => '無接收簡訊手機號碼',
            '3'  => '簡訊內容為空白',
            '5'  => '廠商登入帳號或已加密之登入密碼錯誤',
            '6'  => '無剩餘簡訊通數',
            '10' => '無SourceProdID或SourceMsgID',
            '11' => 'IP位址錯誤',
            '17' => '簡訊字數過長',
            '18' => '沒有簽名檔',
            '19' => '有過濾詞彙'
        );
        return $returnInfo;
    }

}
