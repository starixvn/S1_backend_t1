<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * thaibulkssms SMS API implementation
 * http://thaibulksms.com/sms_api.php
 *
 * Config items:
 * * username
 * * password
 * * msisdn
 * * message
 * * ScheduledDelivery
 */
class Sms_api_thaibulksms extends Abstract_sms_api {
    const SUCCESS_CODE = "1";

    public function getUrl() {
        return $this->getParam('url') ? $this->getParam('url') : 'http://thaibulksms.com/sms_api.php';
    }

    public function send($mobile, $content){
        $this->utils->debug_log("===============sms send", $mobile);
        $ch = curl_init();
        $fields      = $this->configCurl($ch, $mobile, $content);
        $content     = curl_exec($ch);
        $statusCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errCode     = curl_errno($ch);
		$error       = curl_error($ch);
		$statusText  = $errCode . ':' . $error;
        curl_close($ch);

        $this->utils->debug_log("===============thaibulksms api curl response ", $content);

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
        $this->utils->debug_log("===============thaibulksms isSuccess ", $isSuccess);

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
            array('player_id' => $player_id, 'related_id3' => $mobile) //9
        );
        
        return $content;
    }

    public function getFields($mobile, $content) {
        $fields = array(
            'username' => $this->getParam('username'),
            'password' => $this->getParam('password'),
            'msisdn' => $mobile,
            'message' => $content,
        );
        return $fields;
    }

    protected function configCurl($handle, $mobile, $content) {
        $url = $this->getUrl();
        $fields = $this->getFields($mobile, $content);
        $params = http_build_query($fields);
        curl_setopt($handle, CURLOPT_URL, $url.'?'.$params);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    
        $this->utils->debug_log("===============thaibulksms SMS get url", $url.'?'.$params);
        return $fields;
    }

    public function getErrorMsg($returnQueryString) {
        $resp = $this->loadXmlResp($returnQueryString);
        $this->utils->debug_log("=================================thaibulksms SMS isSuccess resp", $resp);
        if(array_key_exists('Detail', $resp['QUEUE'])) {
            return $resp['QUEUE']['Detail'];
        }
        return $resp['QUEUE']['Status'];

    }

    public function isSuccess($returnQueryString) {
        $resp = $this->loadXmlResp($returnQueryString);
        $this->utils->debug_log("=================================thaibulksms SMS isSuccess resp", $resp);
        if($resp['QUEUE']['Status'] == self::SUCCESS_CODE) {
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
        return $this->CI->config->item('sms_api_thaibulksms_'.$name);
    }

}
