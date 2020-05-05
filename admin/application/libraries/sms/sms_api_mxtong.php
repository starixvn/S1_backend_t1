<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * mxtong SMS API implementation
 * http://www.mxtong.net.cn/GateWay/Services.asmx
 *
 * Config items:
 * * sms_api_mxtong_userid
 * * sms_api_mxtong_account
 * * sms_api_mxtong_password
 */
class Sms_api_mxtong extends Abstract_sms_api {
    const SUCCESS_CODE = "Sucess";

    protected function signContent($content) {
		return sprintf("%s【%s】", $content, $this->CI->config->item('sms_from'));
	}

    public function getUrl() {
        return $this->getParam('url') ? $this->getParam('url') : 'http://www.mxtong.net.cn/GateWay/Services.asmx/DirectSend';
    }
    public function send($mobile, $content){
        $ch = curl_init();
        $fields      = $this->configCurl($ch, $mobile, $content);
        $content     = curl_exec($ch);
        $statusCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errCode     = curl_errno($ch);
		$error       = curl_error($ch);
		$statusText  = $errCode . ':' . $error;
        curl_close($ch);


        if($content === false) {
            $this->utils->error_log("Error sending SMS through ".get_class($this). "...", $error);
        }
        #save to response result
        $this->CI->load->model(array('response_result'));
  
        $player_id = NULL;
        $resultAll['type']     = 'sms';
        $resultAll['url']      = $this->getUrl();
        $resultAll['params']   = $fields;
        $resultAll['content']  = $content;

        $isSuccess = $this->isSuccess($content);
        $this->utils->debug_log("===============sms isSuccess ", $isSuccess);

        if($isSuccess){
            $flag = Response_result::FLAG_NORMAL;
        }
        else{
            $flag = Response_result::FLAG_ERROR;
        }   

        $response_result_id = $this->CI->response_result->saveResponseResult(
            $this->getPlatformCode(),
            $flag,
            self::SMS_API,
            self::SMS_API,
            json_encode($resultAll),
            $statusCode, 
            $statusText,
            '',   
            array('player_id' => $player_id, 'related_id3' => $mobile)
        );

        return $content;
    }
    protected function configCurl($handle, $mobile, $content) {
        $url = $this->getUrl();
        $content = $this->signContent($content);
        $fields = $this->getFields($mobile, $content);
        $params = http_build_query($fields);
        curl_setopt($handle, CURLOPT_URL, $url.'?'.$params);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);

        $this->utils->debug_log("===============mxtong SMS get url", $url.'?'.$params);
        return $fields;
    }

    public function getFields($mobile, $content) {
        $fields = array(
			'userID' => $this->getParam('userid'),
			'Account' => $this->getParam('account'),
			'Password' => $this->getParam('password'),
			'Phones' => $mobile.';',
			'Content' => $content,
            'SendTime' => '',
            'SendType' => 1,
			'PostFixNumber' => ''
		);
		$this->utils->debug_log("===============mxtong SMS fields", $fields);
		return $fields;
    }



    public function getErrorMsg($returnQueryString) {
        $resp = $this->loadXmlResp($returnQueryString);
        $this->utils->debug_log("===============mxtong SMS getErrorMsg resp", $resp);
        if(array_key_exists('Message', $resp)) {
            return $resp['RetCode'].": ".$resp['Message'];
        }
        return $resp['RetCode'];

    }

    public function isSuccess($returnQueryString) {
        $resp = $this->loadXmlResp($returnQueryString);
        $this->utils->debug_log("=================================mxtong SMS isSuccess resp", $resp);
        if($resp['RetCode'] == self::SUCCESS_CODE) {
            return true;
        }else {
            return false;
        }

    }

    public function loadXmlResp($returnQueryString) {
    	$xml_object = simplexml_load_string($returnQueryString,NULL,LIBXML_NOWARNING);
    	$xml_array = $this->object2array($xml_object);
        return $xml_array;
    }

    public function object2array($object) {
    	return @json_decode(@json_encode($object),1);
    }

    protected function getParam($name) {
        return $this->CI->config->item('sms_api_mxtong_'.$name);
    }

}
