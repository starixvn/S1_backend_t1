<?php
require_once dirname(__FILE__) . '/abstract_telephone_api.php';

/**
 * ATSTAR
 * http://47.244.58.26:8077
 *
 * ATSTAR_TELEPHONE_API, ID: 5009
 *
 * Required Fields:
 *
 * * URL
 * * Extra Info
 *
 * Field Values:
 *
 * * URL: http://47.244.58.26:8077/atstar/index.php/status-op
 * * Extra Info
 * > {
 * >    "use_newpage" : false,
 * > }
 *
 *
 * @category Telephone
 * @copyright 2013-2018 TripleOneTech
 */
class Telephone_api_atstar extends Abstract_telephone_api {
    const RETURN_SUCCESS = '+OK';

    public function getPlatformCode() {
        return ATSTAR_TELEPHONE_API;
    }

    public function getCallUrl($phoneNumber, $callerId) {
        $num = '1'.$phoneNumber;
        $num = $num*22 + 246135;
        $this->utils->debug_log("==============atstar getCallUrl phoneNumber = [$phoneNumber] , dia_num = [$num]");

        $params = array(
            'op' => 'dialv2',
            'ext_no' => $callerId,
            'dia_num' => $num
        );
        $this->utils->debug_log("==============atstar getCallUrl params generated: ", $params);

        if($this->getSystemInfo('use_newpage','false')){
            $callUrl = $this->getSystemInfo('url') . '?' . http_build_query($params);
            $this->utils->debug_log("==============atstar getCallUrl use_newpage callUrl", $callUrl);
            return array(
                'success' => true,
                'type' => self::REDIRECT_TYPE_GET_URL,
                'url' => $callUrl
            );
        }
        else{
            return $this->processTeleUrlForm($params, $phoneNumber);
        }
    }

    private function processTeleUrlForm($params, $phoneNumber) {
        $url = $this->getSystemInfo('url');
        $this->utils->debug_log("==============atstar processTeleUrlForm Call URL", $url);
        $result_content = $this->submitGetForm($url, $params, false, $phoneNumber);
        return $result_content;
    }

    public function decodeResult($resultString) {
        $result = array(
            'success' => false,
            'type' => self::REDIRECT_TYPE_POST_RESULT,
            'msg' => 'Failed for unknown reason.'
        );
        $this->utils->debug_log("============atstar processTeleUrlForm resultString", $resultString);


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
        $this->utils->debug_log("============atstar processTeleUrlForm result", $result);
        return $result;
    }
}
