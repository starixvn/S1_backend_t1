<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * Infobip SMS API implementation
 * https://api.w88services.com/api/sms/SendSMS
 *
 * Config items:
 * * call_socks5_proxy
 * * sms_api_w88services_url
 * * sms_api_w88services_clientid
 * * sms_api_w88services_clientkey
 * * sms_api_w88services_country_code
 */
class Sms_api_w88services extends Abstract_sms_api {
    const ACCEPTED_CODE  = 0;
    const PENDING_CODE   = 1;
    const DELIVERED_CODE = 3;


    public function getUrl() {
        return $this->getParam('url') ? $this->getParam('url') : 'https://api.w88services.com/api/sms/SendSMS';
    }

    public function send($mobile, $content){
        $ch = curl_init();

        $fields      = $this->configCurl($ch, $mobile, $content);
        $result      = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header      = substr($result, 0, $header_size);
        $statusCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errCode     = curl_errno($ch);
        $error       = curl_error($ch);
        $statusText  = $errCode . ':' . $error;
        $content     = substr($result, $header_size);

        $this->utils->debug_log("===============sms api curl response ", $content);
        if($content === false) {
            $this->utils->error_log("Error sending SMS through ".get_class($this). "...", curl_error($ch));
        }
        curl_close($ch);

        #save to response result
        $this->CI->load->model(array('response_result'));
        $player_id = NULL;
        $resultAll['type']     = 'sms';
        $resultAll['url']      = $this->getUrl();
        $resultAll['params']   = $fields;
        $resultAll['content']  = $content;
        $flag = $this->isSuccess($content) ? Response_result::FLAG_NORMAL:Response_result::FLAG_ERROR;

        $response_result_id = $this->CI->response_result->saveResponseResult($this->getPlatformCode(), $flag,
            self::SMS_API, self::SMS_API, json_encode($resultAll), $statusCode, $statusText, $header,
            array('player_id' => $player_id, 'related_id3' => $mobile));

        return $content;
    }

    public function getFields($mobile, $content) {
        $fields = array(
            'ClientId' => $this->getParam('clientid'),
            'Content' => $content,
            'ReceiverNumber' => $this->getParam('country_code').$this->getMobile($mobile),
        );
        $fields['sign'] = $this->sign($fields);
        $this->utils->debug_log("===============w88services SMS fields", $fields);
        return $fields;
    }

    protected function configCurl($handle, $mobile, $content) {
        $url = $this->getUrl();
        $fields = $this->getFields($mobile, $content);
        $fields_json = json_encode($fields);

        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $fields_json);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $this->setCurlProxyOptions($handle);

        return $fields;
    }

    public function getErrorMsg($returnQueryString) {
        $resp = json_decode($returnQueryString, true);
        $this->utils->debug_log("===============w88services SMS getErrorMsg returnQueryString", $returnQueryString);
        $this->utils->debug_log("===============w88services SMS getErrorMsg resp", $resp);

        if($resp){
            $err_msg = $resp['Error'] .': '. $resp['Message'];
        }
        else{
            $err_msg = $returnQueryString;
        }
        return $err_msg;
    }

    public function isSuccess($returnQueryString) {
        $resp = json_decode($returnQueryString, true);
        if(isset($resp['Error']) && $resp['Error'] == 0){
            return true;
        }
        return false;
    }

    protected function getParam($name) {
        return $this->CI->config->item('sms_api_w88services_'.$name);
    }

    private function sign($fields){
        $sign_str = '';
        foreach ($fields as $key => $value) {
            $sign_str .= $value;
        }
        $sign_str .= $this->getParam('clientkey');

        $this->utils->debug_log("===============w88services sign sign_str", $sign_str);
        return hash('sha256', $sign_str);
    }

}
