<?php
/**
 * player_main_js_addons_abstract.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
abstract class Player_main_js_addons_abstract {
    /* @var BaseController */
    public $CI;

    protected $_addons_name;

    protected $_is_minify = TRUE;

    public function __construct(){
        $this->CI =& get_instance();

    }

    abstract public function isEnabled();

    public function addonsName($addons_name = null){
        if(empty($addons_name)){
            return $this->_addons_name;
        }

        $this->_addons_name = $addons_name;

        return $this->_addons_name;
    }

    public function getScript(){
        $script_path = PUBLICPATH . '/resources/player/addons/'. $this->_addons_name . '.js';
        $content = ($this->_is_minify) ? $this->CI->lib_minify->minifyJS($script_path) : file_get_contents($script_path);

        return $content;
    }

    abstract public function variables();
}