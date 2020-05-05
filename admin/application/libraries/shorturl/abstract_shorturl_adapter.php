<?php
/**
 * abstract_shorturl.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
abstract class Abstract_Shorturl_Adapter {
    /** @var Shorturl $shorturl */
    public $shorturl;

    /** @var BaseController $CI */
    public $CI;

    /** @var Utils */
    public $utils;

    /**
     * Abstract_Shorturl_Adapter constructor.
     *
     * @param $shorturl Shorturl
     */
    public function __construct($shorturl){
        $this->shorturl = $shorturl;
        $this->CI = $shorturl->CI;
        $this->utils = $shorturl->utils;
    }

    abstract public function long2short($long_url);

    public function short2long($short_url){
        return 'Unimplemented';
    }

    public function http_get($url, $params = [], $config = [], $initSSL = NULL){
        $default_config = $this->shorturl->getOptions('curl');
        if(!empty($default_config)){
            $config = array_replace_recursive($config, $this->shorturl->getOptions('curl'));
        }

        $query_string = (!empty($params)) ? http_build_query($params) : NULL;

        if($query_string){
            $url = (FALSE === strpos('?', $url)) ? $url . '?' . $query_string : $url . '&' . $query_string;
        }

        return $this->utils->httpCall($url, $params, $config, $initSSL);
    }

    public function http_post($url, $params = [], $config = [], $initSSL = NULL){
        $default_config = $this->shorturl->getOptions('curl');
        if(!empty($default_config)){
            $config = array_replace_recursive($config, $this->shorturl->getOptions('curl'));
        }

        $config['is_post'] = TRUE;

        return $this->utils->httpCall($url, $params, $config, $initSSL);
    }
}