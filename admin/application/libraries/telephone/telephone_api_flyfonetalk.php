<?php
require_once dirname(__FILE__) . '/abstract_telephone_api.php';

/**
 * FLYFONETALK
 * www.flyfonetalk.com
 *
 * FLYFONETALK_TELEPHONE_API, ID: 904
 *
 * Required Fields:
 *
 * * URL
 * * Secret
 * * Extra Info
 *
 * Field Values:
 *
 * * URL: https://cms.flyfonetalk.com/callapi/{clientname}/
 * * Secret: ## API secret ##
 * * Extra Info
 * > {
 * >    "flyfonetalk_country_code" : ""
 * >    "flyfonetalk_department" : "thailand | indonesia",
 * >    "call_socks5_proxy" : ""
 * > }
 *
 *
 * @category Telephone
 * @copyright 2013-2016 TripleOneTech
 */
class Telephone_api_flyfonetalk extends Abstract_telephone_api {
    const RETURN_SUCCESS = '1';

    public function getPlatformCode() {
        return FLYFONETALK_TELEPHONE_API;
    }

    public function getCallUrl($phoneNumber, $callerId) {
        $country_code = $this->getSystemInfo('flyfonetalk_country_code');
        $department   = $this->getSystemInfo('flyfonetalk_department');
        $params = array(
            'outnumber' => $country_code.$phoneNumber,
            'secret' => $this->getSystemInfo('secret'),
            'ext' => $callerId,
            'department' => $department
        );

        $this->utils->debug_log("==============flyfonetalk getCallUrl params generated: ", $params);

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
        $this->utils->debug_log("==============flyfonetalk processTeleUrlForm Call URL", $url);
        $result_content = $this->submitGetForm($url, $params, false, $params['outnumber']);

        return $result_content;
    }

    public function decodeResult($resultString) {
        $decode_content = json_decode($resultString, true);
        $result = array(
            'success' => false,
            'type' => self::REDIRECT_TYPE_POST_RESULT,
            'msg' => 'Failed for unknown reason.'
        );
        $this->utils->debug_log("============flyfonetalk processTeleUrlForm decodeResult", $decode_content);
        if(!isset($decode_content['ResultCode'])) {
            $result['msg'] = "API response doesn't contain the key 'ResultCode'.";
        }
        else {
            if($decode_content['ResultCode'] == self::RETURN_SUCCESS) {
                $result['success'] = true;
                $result['msg'] = 'API success ResultCode - ['. $decode_content['ResultCode'] .']: Wait for connecting...';
            }
            else if(isset($decode_content['ResultCode'])) {
                $result['msg'] = 'API failed ResultCode - ['. $decode_content['ResultCode'] .']: '.$decode_content['ResultDesc'];
            }
        }

        return $result;
    }
}
