<?php
require_once dirname(__FILE__) . '/abstract_telephone_api.php';

/**
 * EXUN e讯
 * http://45.63.56.153
 *
 * EXUN_TELEPHONE_API, ID: 897
 *
 * Required Fields:
 *
 * * URL
 * * Extra Info
 *
 * Field Values:
 *
 * * URL: http://45.63.56.153/clickcall/call.php
 * * Extra Info
 * > {
 * >    "use_newpage" : false,
 * > }
 *
 *
 * @category Telephone
 * @copyright 2013-2016 TripleOneTech
 */
class Telephone_api_exun extends Abstract_telephone_api {
    const RETURN_SUCCESS = '呼叫已发送';

    public function getPlatformCode() {
        return EXUN_TELEPHONE_API;
    }

    public function getCallUrl($phoneNumber, $callerId) {
        $params = array(
            'internalnum' => $callerId,
            'outboundnum' => $phoneNumber,
        );
        $this->utils->debug_log("==============exun getCallUrl params generated: ", $params);

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
        $this->utils->debug_log("==============exun processTeleUrlForm Call URL", $url);
        $result_content = $this->submitGetForm($url, $params, false, $params['outboundnum']);
        return $result_content;
    }

    public function decodeResult($resultString) {
        $result = array(
            'success' => false,
            'type' => self::REDIRECT_TYPE_POST_RESULT,
            'msg' => 'Failed for unknown reason.'
        );
        $this->utils->debug_log("============exun processTeleUrlForm resultString", $resultString);


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
        $this->utils->debug_log("============exun processTeleUrlForm result", $result);
        return $result;
    }
}
