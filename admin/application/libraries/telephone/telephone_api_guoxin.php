<?php
require_once dirname(__FILE__) . '/abstract_telephone_api.php';

/**
 * GUOXIN 国信呼叫中心
 * http://www.telcrm.cn
 *
 * GUOXIN_TELEPHONE_API, ID: 889
 *
 * Required Fields:
 *
 * * URL
 * * Extra Info
 *
 * Field Values:
 *
 * * URL: http://203.82.36.236:12121/bridge/callctrl
 * * Extra Info
 * > {
 * >    "guoxin_default_caller" : "817",
 * >    "guoxin_country_code" : ""
 * > }
 *
 *
 * @category Telephone
 * @copyright 2013-2016 TripleOneTech
 */
class Telephone_api_guoxin extends Abstract_telephone_api {
    const RETURN_SUCCESS = '200';

    public function getPlatformCode() {
        return GUOXIN_TELEPHONE_API;
    }

    public function getCallUrl($phoneNumber, $callerId) {
        if(empty($callerId)) {
            $callerId = $this->getSystemInfo('guoxin_default_caller');
        }

        $country_code = $this->getSystemInfo('guoxin_country_code', '86');
        $params = array(
            'caller' => $callerId,
            'callee' => $country_code.$phoneNumber,
            'authtype' => "no",
            'opt' => "CLICK_TO_IP_DIAL"
        );

        $this->utils->debug_log("==============guoxin getCallUrl params generated: ", $params);

        return $this->processTeleUrlForm($params);
    }

    private function processTeleUrlForm($params) {
        $url = $this->getSystemInfo('url');
        $this->utils->debug_log("==============guoxin processTeleUrlForm Call URL", $url);
        $result_content = $this->submitGetForm($url, $params, false, $params['callee']);

        if($result_content['success'] == true){
            $hang_up_url = $url.'?caller='.$params['caller'].'&callee='.$params['callee'].'&opt=CLICK_TO_HUNGUP';
            $result_content['msg'] .= '<a onclick=hangupTeleCall("'.$hang_up_url.'") href="javascript:void(0)"><i class="fa fa-phone"></i><span class="hidden-xs">'.lang('Hang up').'</span></a>';
        }

        return $result_content;
    }

    public function decodeResult($resultString) {
        $result = array(
            'success' => false,
            'type' => self::REDIRECT_TYPE_POST_RESULT,
            'msg' => 'Failed for unknown reason.'
        );
        $this->utils->debug_log("============guoxin processTeleUrlForm resultString", $resultString);


        if(!isset($resultString)) {
            $result['msg'] = "API response failed.";
        }
        else {
            if($resultString == self::RETURN_SUCCESS) {
                $result['success'] = true;
                $result['msg'] = 'API success code - ['. $resultString .']: Wait for connecting...<br>';
            }
            else if(isset($resultString)) {
                $returnInfo = $this->returnInfo();
                if(!array_key_exists($resultString, $returnInfo)) {
                    $this->utils->error_log("========================guoxin return UNKNOWN ERROR!");
                    $returnDesc = "未知错误";
                }
                else {
                    $returnDesc = $returnInfo[$resultString];
                }

                $result['msg'] = 'API failed code - ['. $resultString .']: ['.$returnDesc.']';
            }
        }
        $this->utils->debug_log("============guoxin processTeleUrlForm result", $result);
        return $result;
    }

    public function returnInfo() {
        $returnInfo = array(
            '200' => '转话成功',
            '402' => '授权限制',
            '486' => '目标分机忙或网关线路忙',
            '403' => '黑名单号码，禁止拨打',
            '502' => '指定的网关账号类型错误，不是出群网关',
            '500' => '系统错误'
        );
        return $returnInfo;
    }

}
