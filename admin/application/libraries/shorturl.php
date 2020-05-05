<?php
include_once __DIR__ . '/shorturl/abstract_shorturl_adapter.php';
include_once __DIR__ . '/shorturl/shorturl_adapter_unimplemented.php';

/**
 * shorturl.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 *
 * @property BaseController $CI
 * @property Utils $utils
 */
class Shorturl{
    /* @var array $options */
    protected $_options;

    /* @var Abstract_Shorturl_Adapter $adapter */
    protected $_adapter;

    public function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->library(['session']);

        $this->utils = $this->CI->utils;

        $this->_options = [
            'use_service' => 'google'
        ];

        $this->init();
    }

    public function init(){
        $this->_options = array_replace_recursive($this->_options, config_item('shorturl'));

        $class = 'Shorturl_Adapter_' . ucfirst($this->_options['use_service']);

        require_once __DIR__ . '/shorturl/shorturl_adapter_' . strtolower($this->_options['use_service']) . '.php';

        if(!class_exists($class)){
            $message = "The \"${class}\" shorturl adapter class not exists";
            trigger_error($message, E_USER_ERROR);

            $this->utils->error_log($message);

            $class = 'Shorturl_Adapter_Unimplemented';
        }

        $adapter = new $class($this);

        $this->_adapter = $adapter;
    }

    public function getOptions($key = NULL){
        return (empty($key)) ? $this->_options : ((!isset($key)) ? NULL : $this->_options[$key]);
    }

    public function long2short($long_url){
        return $this->_adapter->long2short($long_url);
    }

    public function short2long($short_url){
        return $this->_adapter->short2long($short_url);
    }
}