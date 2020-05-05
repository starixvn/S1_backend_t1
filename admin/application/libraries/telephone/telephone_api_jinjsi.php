<?php
require_once dirname(__FILE__) . '/abstract_telephone_api.php';

/**
 * jinjsi 
 * www.jinjsi.com
 *
 * JINJSI_TELEPHONE_API, ID: 5017
 *
 * Required Fields:
 *
 * * URL
 * * Secret
 * * Extra Info
 *
 * Field Values:
 *
 * * URL: http://ipcenter.jinjsi.com:9600/DailerQueue.php
 * * Secret: ## API secret ##
 * * Extra Info
 * > {
 * >    "jinjsi_country_code" : ""
 * >    "jinjsi_department" : "thailand | indonesia",
 * >    "call_socks5_proxy" : ""
 * > }
 *
 *
 * @category Telephone
 * @copyright 2013-2016 TripleOneTech
 */
class Telephone_api_jinjsi extends Abstract_telephone_api {
    const RETURN_SUCCESS = '1';

    public function getPlatformCode() {
        return JINJSI_TELEPHONE_API;
    }

    public function getCallUrl($phoneNumber, $callerId) {

        $params = array(
            'dn' => $callerId,
            'phonenumber' => $phoneNumber
        );

        $this->utils->debug_log("==============jinjsi getCallUrl params generated: ", $params);

        if($this->getSystemInfo('use_newpage')){
            $callUrl = $this->getSystemInfo('url') . '?' . http_build_query($params);
            $this->utils->debug_log("==============exun getCallUrl use_newpage callUrl", $callUrl);
            return array(
                'success' => true,
                'type' => self::REDIRECT_TYPE_GET_URL,
                'url' => $callUrl
            );
        }
        else{
            return $this->processTeleUrlForm($params);
        }
    }

    private function processTeleUrlForm($params) {
        $url = $this->getSystemInfo('url');
        $this->utils->debug_log("==============jinjsi processTeleUrlForm Call URL", $url);
        $result_content = $this->submitGetForm($url, $params, false, $params['phonenumber']);

        return $result_content;
    }

    public function decodeResult($resultString) {
        $decode_content = json_decode($resultString, true);
        $result = array(
            'success' => false,
            'type' => self::REDIRECT_TYPE_POST_RESULT,
            'msg' => 'Failed for unknown reason.'
        );
        $this->utils->debug_log("============jinjsi processTeleUrlForm decodeResult", $decode_content);
        if(!isset($resultString)) {
            $result['msg'] = "API response failed.";
        }
        else {
           if($resultString == self::RETURN_SUCCESS) {
                $result['success'] = true;
                $result['msg'] = 'API success code - ['. $resultString .']: Wait for connecting...<br>';
            }
            else if(isset($resultString)) {
                $result['msg'] = 'API failed code - ['. $resultString .']';
            }
        }

        return $result;
    }
}
