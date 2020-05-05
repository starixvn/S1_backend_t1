<?php
/**
 * syfxzz.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */

class Shorturl_Adapter_Ft12 extends Abstract_Shorturl_Adapter {
    protected $_api_url = 'http://api.ft12.com/api.php';

    public function long2short($long_url){
        $config = [
            'header_array' => [
            ]
        ];

        $params = [
            'format' => 'json',
            'url' => $long_url
        ];

        $result = $this->http_get($this->_api_url, $params, $config);

        list($header, $content, $statusCode, $statusText, $errCode, $error, $resultObj) = $result;

        if(!empty($errCode) || empty($content)){
            return FALSE;
        }

        try{
            $content = trim($content);
            $content = str_replace("\xEF\xBB\xBF", '', $content);
            $json = json_decode($content, TRUE);

            if(!isset($json['url'])){
                return FALSE;
            }
        }catch(Exception $e){
            return FALSE;
        }

        return $json['url'];
    }
}