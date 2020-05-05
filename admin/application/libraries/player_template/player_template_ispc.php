<?php
/**
 * player_template_ispc.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 * @see
 */
class Player_template_ispc extends Player_template_abstract {
    public function _initialize(){
        $this->__init_releated_scripts();
        $this->__init_releated_styles();
    }

    private function __init_releated_scripts(){
        $this->addReleatedJS('/resources/third_party/bootstrap-notify/bootstrap-notify.min.js');
        // $this->addReleatedJS('/resources/third_party/webshim/1.15.8/polyfiller.min.js');
        $this->addReleatedJS('/resources/third_party/bootstrap-datepicker/1.7.0/bootstrap-datepicker.min.js');

        return $this;
    }

    private function __init_releated_styles(){
        $this->addReleatedCSS('/resources/third_party/bootstrap-datepicker/1.7.0/bootstrap-datepicker3.min.css');

        return $this;
    }
}