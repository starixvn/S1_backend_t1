<?php
/**
 * player_template_default.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 * @see
 */
class Player_template_default extends Player_template_abstract {
    protected function _initialize(){
        $this->resetScripts();
        $this->resetStyles();

        $this->addRequireJS('/resources/third_party/jquery/jquery-3.1.1.min.js');
        $this->addRequireJS('/resources/third_party/bootstrap/3.3.7/bootstrap.min.js');

        // $this->addCustomJS('/resources/player/built_in/default_all.min.js');
        // $this->addCustomJS('/common/js/player_main.js'); // legacy

        if(!$this->is_mobile){
            if($this->CI->utils->isEnabledFeature('enable_custom_script')){
                $this->addCustomJS($this->CI->utils->getAnyCmsUrl('/includes/js/custom.js'));
            }
        }else{
            if($this->CI->utils->isEnabledFeature('enable_custom_script_mobile')){
                $this->addCustomJS($this->CI->utils->getAnyCmsUrl('/includes/js/custom-mobile.js'));
            }
        }

        $this->addRequireCSS('/resources/third_party/bootstrap/3.3.7/bootstrap.min.css');

        if(!$this->is_mobile){
            $this->addRequireCSS($this->template_base_path . '/css/font-awesome.min.css');

            $this->addCustomCSS($this->template_base_path . '/style.css');
            $this->addCustomCSS($this->CI->utils->getActivePlayerCenterTheme());

            $this->addCustomCSS($this->CI->utils->getAnyCmsUrl('/includes/css/custom-style.css'));
        }else{
            $this->addRequireCSS('/resources/css/fontawesome/5.0.1/web-fonts-with-css/css/fontawesome-all.min.css');

            $this->addCustomCSS($this->template_base_path . '/css/style-mobile-default.css');

            if ($hostCustomHostCss = $this->CI->utils->getCustomHostCss()) { // same of the $this->CI->utils->getActivePlayerCenterTheme
                $this->addCustomCSS($this->CI->utils->getAnyCmsUrl('/includes/css/style-mobile-' . $hostCustomHostCss . '.css'), NULL);
            }

            $this->addCustomCSS($this->CI->utils->getAnyCmsUrl('/includes/css/style-mobile.css'));
        }
    }
}