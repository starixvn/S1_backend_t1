<?php
/**
 * player_template_abstract.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
abstract class Player_template_abstract {
    /* @var PlayerCenterBaseController */
    public $CI;

    /* @var CI_Template */
    public $template_instance;

    public $template_props;

    public $require_scripts = [];
    public $functions_scripts = [];
    public $releated_scripts = [];
    public $custom_scripts = [];

    public $require_styles = [];
    public $functions_styles = [];
    public $releated_styles = [];
    public $custom_styles = [];

    public $template_base_path;
    public $is_mobile = FALSE;
    public $is_homepage = FALSE;

    public $web_title = [];
    public $function_title = [];

    public function __construct(){
        $this->CI =& get_instance();

        $this->template_base_path = '/' . $this->CI->utils->getPlayerCenterTemplate(FALSE);
        $this->is_mobile = $this->CI->utils->is_mobile();
    }

    public function __call($name, $arguments){
        return call_user_func_array([$this->template_instance, $name], $arguments);
    }

    public function __get($name){
        return $this->template_instance->$name;
    }

    public function __set($name, $value){
        $this->template_instance->$name = $value;
    }

    public function setTemplateInstance($template_instance){
        $this->template_instance = $template_instance;
    }

    public function setTemplateProps($props){
        $this->template_props = $props;
    }

    public function initialize($props){
        $this->__init_releated_scripts();
        $this->__init_releated_styles();

        $this->template_props = $props;

        $this->preloadSharedVars();

        if(method_exists($this, '_initialize')){
            return call_user_func_array([$this, '_initialize'], func_get_args());
        }
    }

    private function __init_releated_scripts(){
        $this->addRequireJS('/resources/third_party/jquery/jquery-3.1.1.min.js');
        $this->addRequireJS('/resources/third_party/bootstrap/3.3.7/bootstrap.min.js');

        $default_all_filename = "default_all.min.js";
        if($this->CI->utils->isEnabledMDB()
            && $this->CI->utils->isCurrencyDomain()
        ){
            //load file in currency
            $default_all_filename = $this->CI->utils->getActiveTargetDB().'_'.$default_all_filename;
        }

        $this->addCustomJS('/resources/player/built_in/'.$default_all_filename);

        if ($this->CI->utils->isEnabledFeature('enable_dynamic_javascript')) {
            $addJs = $this->CI->utils->getAddJs();
            if( !empty($addJs) ) {
                foreach ($addJs as $file => $value) {
                    $this->addCustomJS($value);
                }
            }
        }

        if(!$this->is_mobile){
            $this->addCustomJS('/common/js/main.js');

            $this->addCustomJS($this->template_base_path . '/js/template-script.js');

            if($this->CI->utils->isEnabledFeature('enable_custom_script')){
                $this->addCustomJS($this->CI->utils->getAnyCmsUrl('/includes/js/custom.js'), FALSE);
            }
        }else{
            $this->addCustomJS('/common/js/main-mobile.js');

            $this->addCustomJS($this->template_base_path . '/js/template-script-mobile.js');

            if($this->CI->utils->isEnabledFeature('enable_custom_script_mobile')){
                $this->addCustomJS($this->CI->utils->getAnyCmsUrl('/includes/js/custom-mobile.js'), FALSE);
            }
        }
    }

    private function __init_releated_styles(){
        $this->addRequireCSS('/resources/third_party/animate/3.6.0/animate.min.css');
        $this->addRequireCSS('/resources/third_party/bootstrap/3.3.7/bootstrap.min.css');

        if(!$this->is_mobile){
            $this->addRequireCSS($this->template_base_path . '/css/font-awesome.min.css');

            $this->addCustomCSS( $this->template_base_path . '/style.css');
            $this->addCustomCSS( $this->CI->utils->getActivePlayerCenterTheme());

            $this->addCustomCSS($this->CI->utils->getAnyCmsUrl('/includes/css/custom-style.css'), NULL, FALSE);
        }else{
            $this->addRequireCSS('/resources/css/fontawesome/5.0.1/web-fonts-with-css/css/fontawesome-all.min.css');

            $this->addCustomCSS( $this->template_base_path . '/css/style-mobile-default.css');
            if ($hostCustomHostCss = $this->CI->utils->getCustomHostCss()) { // same of the $this->CI->utils->getActivePlayerCenterTheme
                $this->addCustomCSS($this->CI->utils->getAnyCmsUrl('/includes/css/style-mobile-' . $hostCustomHostCss . '.css'), NULL);
            }

            $this->addCustomCSS($this->CI->utils->getAnyCmsUrl('/includes/css/style-mobile.css'), NULL, FALSE);
        }
    }

    /**
     * If you want to adjust the content, please ask elvis first.
     *
     * @return Player_template_abstract
     */
    public function preloadSharedVars(){
        $currency_display = $this->CI->operatorglobalsettings->getSettingJson('player_center_currency_display_format');
        $currency_display = (empty($currency_display)) ? [] : $currency_display;

        $this->assign([
            'active_template' => $this,
            'template_name' => $this->template_props['name'],
            'template_path' => VIEWPATH . '/' . $this->template_props['name'],
            'metaDataInfo' => $this->CI->utils->getMetaDataInfo(),

            'playercenter_logo' => $this->CI->utils->getPlayerCenterLogoURL(),
            'currency' => $this->CI->utils->getCurrentCurrency(),
            'display_currency_name' => in_array('currency_name', $currency_display),
            'display_currency_code' => in_array('currency_code', $currency_display),
            'display_currency_symbol' => in_array('currency_symbol', $currency_display),
        ]);
        $this->assign($this->CI->utils->getDefaultPlayerCenterCurrencyDisplayOptions());

        if($this->is_mobile){
            return $this->_preloadDesktopSharedVars();
        }else{
            return $this->_preloadMobileSharedVars();
        }
    }

    protected function _preloadDesktopSharedVars(){
        return $this;
    }

    protected function _preloadMobileSharedVars(){
        return $this;
    }

    public function resetScripts(){
        $this->require_scripts = [];
        $this->releated_scripts = [];
        $this->custom_scripts = [];
    }

    public function resetStyles(){
        $this->require_styles = [];
        $this->releated_styles = [];
        $this->custom_styles = [];
    }

    public function assign(){
        return call_user_func_array([$this->template_instance, __FUNCTION__], func_get_args());
    }

    public function get_var(){
        return call_user_func_array([$this->template_instance, __FUNCTION__], func_get_args());
    }

    public function renderScripts(){
        $newline = PHP_EOL . '    ';
        $html = '';
        foreach ($this->require_scripts as $js_url){
            $html .= '<script type="text/javascript" src="'. $js_url . '"></script>' . $newline;
        }
        foreach ($this->releated_scripts as $js_url){
            $html .= '<script type="text/javascript" src="'. $js_url . '"></script>' . $newline;
        }
        foreach ($this->functions_scripts as $js_url){
            $html .= '<script type="text/javascript" src="'. $js_url . '"></script>' . $newline;
        }
        foreach ($this->custom_scripts as $js_url){
            $html .= '<script type="text/javascript" src="'. $js_url . '"></script>' . $newline;
        }

        $html = trim($html) . PHP_EOL;

        return $html;
    }

    public function renderStyles(){
        $newline = PHP_EOL . '    ';
        $html = '';
        foreach ($this->require_styles as $css){
            $html .= '<link rel="stylesheet" href="' . $css['url'] . '"' . ((empty($css['media'])) ? '' : ' media="' . $css['media'] . '"') . ' />' . $newline;
        }
        foreach ($this->releated_styles as $css){
            $html .= '<link rel="stylesheet" href="' . $css['url'] . '"' . ((empty($css['media'])) ? '' : ' media="' . $css['media'] . '"') . ' />' . $newline;
        }
        foreach ($this->functions_styles as $css){
            $html .= '<link rel="stylesheet" href="' . $css['url'] . '"' . ((empty($css['media'])) ? '' : ' media="' . $css['media'] . '"') . ' />' . $newline;
        }
        foreach ($this->custom_styles as $css){
            $html .= '<link rel="stylesheet" href="' . $css['url'] . '"' . ((empty($css['media'])) ? '' : ' media="' . $css['media'] . '"') . ' />' . $newline;
        }

        $html = trim($html) . PHP_EOL;

        return $html;
    }

    public function addRequireJS($url, $append_version = TRUE){
        $this->require_scripts[] = ($append_version) ? $this->CI->utils->getPlayerCmsUrl($url) : $url;
        return $this;
    }

    public function addRequireCSS($url, $media = NULL, $append_version = TRUE){
        $this->require_styles[] = [
            'url' => ($append_version) ? $this->CI->utils->getPlayerCmsUrl($url) : $url,
            'media' => $media
        ];
        return $this;
    }

    public function addReleatedJS($url, $append_version = TRUE){
        $this->releated_scripts[] = ($append_version) ? $this->CI->utils->getPlayerCmsUrl($url) : $url;
        return $this;
    }

    public function addReleatedCSS($url, $media = NULL, $append_version = TRUE){
        $this->releated_styles[] = [
            'url' => ($append_version) ? $this->CI->utils->getPlayerCmsUrl($url) : $url,
            'media' => $media
        ];
        return $this;
    }

    public function addFunctionJS($url, $append_version = TRUE){
        $this->functions_scripts[] = ($append_version) ? $this->CI->utils->getPlayerCmsUrl($url) : $url;
        return $this;
    }

    public function addFunctionCSS($url, $media = NULL, $append_version = TRUE){
        $this->functions_styles[] = [
            'url' => ($append_version) ? $this->CI->utils->getPlayerCmsUrl($url) : $url,
            'media' => $media
        ];
        return $this;
    }

    public function addCustomJS($url, $append_version = TRUE){
        $this->custom_scripts[] = ($append_version) ? $this->CI->utils->getPlayerCmsUrl($url) : $url;
        return $this;
    }

    public function addCustomCSS($url, $media = NULL, $append_version = TRUE){
        $this->custom_styles[] = [
            'url' => ($append_version) ? $this->CI->utils->getPlayerCmsUrl($url) : $url,
            'media' => $media
        ];
        return $this;
    }

    public function setHomePage(){
        $this->is_homepage = TRUE;

        return $this;
    }

    public function isHomePage(){
        return $this->is_homepage;
    }

    public function appendWebTitle($title){
        $this->web_title[] = $title;

        return $this;
    }

    public function appendFunctionTitle($title){
        $this->function_title[] = $title;

        // TODO Implement append to web title

        return $this;
    }

    public function renderFunctionTitle(){
        $html = '';

        if(!$this->is_mobile){
            $html = implode( ' - ', $this->function_title);
        }else{
            $playercenter_logo = $this->get_var('playercenter_logo');
            $text = implode( ' - ', $this->function_title);

            $val = $this->CI->operatorglobalsettings->getSettingIntValue('player_center_mobile_header_title_style');
            switch($val){
                case PLAYER_CENTER_MOBILE_HEADER_STYLE_ALL_LOGO:
                    $html = '<span class="header_title_img"><img src="' . $playercenter_logo . '" /></span>';
                    break;
                case PLAYER_CENTER_MOBILE_HEADER_STYLE_LOGO_AND_TEXT:
                default:
                    if($this->is_homepage){
                        $html = '<span class="header_title_img"><img src="' . $playercenter_logo . '" /></span>';
                    }else{
                        $html = '<span class="header_title_text">' . $text . '</span>';
                    }
                    break;
            }
        }

        return $html;
    }
}