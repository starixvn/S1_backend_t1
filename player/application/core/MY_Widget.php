<?php
/**
 * Created by PhpStorm.
 * User: Elvis
 * Date: 2018/1/12
 * Time: 上午 11:27
 */

class MY_Widget{
    public $CI;

    protected $_data = [];

    protected $_options;

    public function __construct($options = []){
        $this->CI = &get_instance();

        if($this->authentication->isLoggedIn()) {
            $playerId = $this->authentication->getPlayerId();
            $username = $this->authentication->getUsername();
            $player = $this->player_functions->getPlayerById($playerId);

            $this->load->vars('playerId', $playerId);
            $this->load->vars('username', $username);
            $this->load->vars('player', $player);
            $this->load->vars('isLogged', TRUE);
        }else{
            $this->load->vars('isLogged', FALSE);
        }

        $this->_data['widget_id'] = strtolower(get_class($this)) . '_' . spl_object_hash($this);

        $this->initialize($options);
    }

    public function __call($method_name, $arguments){
        return call_user_func_array([$this->CI, $method_name], $arguments);
    }

    public static function __callStatic($method_name, $arguments){
        $CI = &get_instance();
        return call_user_func_array([$CI, $method_name], $arguments);
    }

    public function __get($name){
        return $this->CI->$name;
    }

    public function initialize($options = []){}

    /**
     * @return string
     */
    public function render(){
        $widget = preg_replace('/^widget_/', '', strtolower(get_class($this)));

        if(file_exists($this->utils->getPlayerCenterTemplate() . '/widgets/' . $widget)){
            return $this->load->view($this->utils->getPlayerCenterTemplate() . '/widgets/' . $widget, $this->_data, TRUE);
        }else{
            return $this->load->view('resources/common/widgets/' . $widget, $this->_data, TRUE);
        }
    }

    public function __toString(){
        return $this->render();
    }
}