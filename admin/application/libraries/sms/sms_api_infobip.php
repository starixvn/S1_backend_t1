<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * Infobip SMS API implementation
 * https://api.infobip.com/sms/1/text/single
 *
 * Config items:
 * * call_socks5_proxy
 * * sms_api_infobip_country_code
 * * sms_api_infobip_auth
 */
class Sms_api_infobip extends Abstract_sms_api {
    const ACCEPTED_CODE  = 0;
    const PENDING_CODE   = 1;
    const DELIVERED_CODE = 3;


    public function getUrl() {
        return $this->getParam('url') ? $this->getParam('url') : 'https://api.infobip.com/sms/1/text/single';
    }

    public function send($mobile, $content){
        $ch = curl_init();

        $fields      = $this->configCurl($ch, $mobile, $content);
        $result      = curl_exec($ch);

        $statusCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header      = substr($result, 0, $header_size);
        $content     = substr($result, $header_size);
        $errCode     = curl_errno($ch);
        $error       = curl_error($ch);
        $statusText  = $errCode . ':' . $error;
        curl_close($ch);


        $this->utils->debug_log("===============sms api curl response ", $content);
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

        $response_result_id = $this->CI->response_result->saveResponseResult($this->getPlatformCode(), $flag,
            self::SMS_API, self::SMS_API, json_encode($resultAll), $statusCode, $statusText, $header,
            array('player_id' => $player_id, 'related_id3' => $mobile));

        return $content;
    }

    public function getFields($mobile, $content) {
        $fields = array(
            'from' => $this->CI->config->item('sms_from'),
            'to' => $this->getParam('country_code').$mobile,
            'text' => $content
        );
        $this->utils->debug_log("===============infobip SMS fields", $fields);
        return $fields;
    }

    protected function configCurl($handle, $mobile, $content) {
        $url = $this->getUrl();
        $fields = $this->getFields($mobile, $content);
        $fields_json = json_encode($fields);
        $fields['auth'] = $this->getParam('auth');

        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $fields_json);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '.$fields['auth']));
        $this->setCurlProxyOptions($handle);

        return $fields;
    }

    public function getErrorMsg($returnQueryString) {
        $resp = json_decode($returnQueryString, true);
        $this->utils->debug_log("===============infobip SMS getErrorMsg returnQueryString", $returnQueryString);
        $this->utils->debug_log("===============infobip SMS getErrorMsg resp", $resp);

        if(isset($resp['requestError']['serviceException']['messageId'])){
            return $resp['requestError']['serviceException']['messageId'] .': '. $resp['requestError']['serviceException']['text'];
        }
    }

    public function isSuccess($returnQueryString) {
        $resp = json_decode($returnQueryString, true);
        $groupid = $resp['messages'][0]['status']['groupId'];
        if(isset($groupid)){
            if($groupid == self::ACCEPTED_CODE || $groupid == self::PENDING_CODE || $groupid == self::DELIVERED_CODE){
                return true;
            }
        }
        return false;
    }

    protected function getParam($name) {
        return $this->CI->config->item('sms_api_infobip_'.$name);
    }

}
