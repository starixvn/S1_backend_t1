<?php
include_once __DIR__ . '/player_main_js/player_main_js_generator.php';
include_once __DIR__ . '/player_main_js/player_main_js_addons_abstract.php';
include_once __DIR__ . '/player_main_js/addons/player_main_js_addons_game_preloader.php';

/**
 * player_main_js_library.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
class Player_main_js_library {
    /* @var BaseController */
    public $CI;

    protected $_addons = [];
    protected $_enabled_addons = [];

    protected $_combine_suffix_name = 'all';
    protected $_combine_path = STORAGEPATH . '/player_pub';

    protected $_generate_result = FALSE;

    public function __construct(){
        $this->CI =& get_instance();

        $this->_addons[] = 'announcement';
        $this->_addons[] = 'player';
        $this->_addons[] = 'player_wallet';
        $this->_addons[] = 'player_message';
        $this->_addons[] = 'player_notification';
        $this->_addons[] = 'game_preloader';

        $this->_init();
    }

    protected function _init(){
        $addons = $this->_addons;
        foreach($addons as $addons_name){
            $addons_name = strtolower($addons_name);
            include_once __DIR__ . '/player_main_js/addons/player_main_js_addons_' . $addons_name . '.php';

            $class = 'Player_main_js_addons_' . $addons_name;
            /* @var $instance Player_main_js_addons_abstract */
            $instance = new $class();
            $instance->addonsName($addons_name);

            if($instance->isEnabled()){
                $this->_enabled_addons[$addons_name] = $instance;
            }
        }
    }

    public function generate_static_scripts($site_name){
        $this->CI->load->model(array('static_site'));

        $site = $this->CI->static_site->getSiteByName($site_name);

        $generator = new Player_main_js_generator();

        $generator->appendCSSFile(PUBLICPATH . '/resources/player/ui/t1t-ui.min.css', FALSE);
        $generator->appendCSSFile(PUBLICPATH . '/resources/player/material.css');

        $this->_generate_result = FALSE;

        if(empty($site)){
            $generator->append("/* The \"{$site_name}\" static site not exists. */");
        }else{
            $this->_generate_jquery($generator);
            $this->_generate_underscore($generator);
            $this->_generate_cookies($generator);

            $generator->appendFile(PUBLICPATH . '/resources/player/core/core.js');
            $generator->appendFile(PUBLICPATH . '/resources/player/core/event.js');
            $generator->appendFile(PUBLICPATH . '/resources/player/core/messagebox.js');
            $generator->appendFile(PUBLICPATH . '/resources/player/core/loader.js');
            $generator->append($this->_generate_common_variables($site_name));

            $generator->appendFile(PUBLICPATH . '/resources/player/smartbackend.js');
            $generator->appendFile(PUBLICPATH . '/resources/player/utils.js');
            $generator->appendFile(PUBLICPATH . '/resources/player/render-ui.js');
            $generator->appendFile(PUBLICPATH . '/resources/player/call_api.js');

            /* @var $addons_instance Player_main_js_addons_abstract */
            foreach($this->_enabled_addons as $addons_name => $addons_instance){
                $generator->append($addons_instance->getScript(), FALSE);
            }

            $generator->appendFile(PUBLICPATH . '/resources/player/player_main.js');
        }

        $content = $generator->createOutput($this->_combine_path, $site_name . '_' . $this->_combine_suffix_name);

        $this->_generate_result = (strlen($content) <= 100);

        if(!$this->_generate_result){
            $this->CI->utils->incCmsVersion();
        }
        return $content;
    }

    public function generate_result(){
        return $this->_generate_result;
    }

    /**
     * @param $generator Player_main_js_generator
     */
    protected function _generate_jquery($generator){
        $_sbejquery = file_get_contents(PUBLICPATH . '/resources/player/jquery-1.11.3.min.js');
        $jquery_plugins = [
            '/resources/player/jquery.ba-postmessage.min.js' => ['minify' => FALSE]
        ];

        $_sbejquery_content = <<<JAVASCRIPT
var _sbejquery = (function(){
    {$_sbejquery}
    return window.jQuery.noConflict(true);
})();
var jQuery, $;
jQuery = $ = _sbejquery;
JAVASCRIPT;

        $generator->append($_sbejquery_content);

        foreach($jquery_plugins as $related_file_path => $options){
            $generator->appendFile(PUBLICPATH . $related_file_path, $options['minify']);
        }

        $t1t_ui = file_get_contents(PUBLICPATH . '/resources/player/ui/t1t-ui.min.js');
        $t1t_ui .= file_get_contents(PUBLICPATH . '/resources/player/ui/bootstrap-notify.min.js');

        $t1t_ui_content = <<<JAVASCRIPT
(function(define, require, exports, module){ // skip requirejs
	{$t1t_ui}
}).call({
    "jQuery": jQuery
});
JAVASCRIPT;

        $generator->append($t1t_ui_content);
    }

    protected function _generate_underscore($generator){
        $underscore = file_get_contents(PUBLICPATH . '/resources/player/underscore-min.js');

        $underscore_content = <<<JAVASCRIPT
var original_underscore = window._;
var underscore = (function(){
    {$underscore}

    var internal_underscore = window._;

    window._ = original_underscore;

    return internal_underscore;
})();
var _;
_ = underscore;
JAVASCRIPT;

        $generator->append($underscore_content);
    }

    protected function _generate_cookies($generator){
        $cookies = file_get_contents(PUBLICPATH . '/resources/player/cookies.min.js');

        $cookies_content = <<<JAVASCRIPT
var cookies = (function(){
    {$cookies}

    return window.Cookies.noConflict();
})();
JAVASCRIPT;

        $generator->append($cookies_content);
    }

    protected function _common_variables($site_name){
        $this->CI->load->library(array('language_function'));

        $site = $this->CI->static_site->getSiteByName($site_name);

        $siteLang = $site->lang;

        if(!empty($siteLang)){
            $this->CI->language_function->setCurrentLanguage($this->CI->language_function->langStrToInt($siteLang));
            $this->CI->utils->loadLanguage($siteLang, 'main', true);
        }else{
            $this->CI->utils->initiateLang();
        }

        $default_prefix_for_username = $this->CI->config->item('default_prefix_for_username');

        $origin = "*";

        $view_template = $this->CI->utils->getPlayerCenterTemplate(FALSE);

        $player_live_chat_script = $this->CI->utils->getConfig('player_live_chat_script');

        $this->CI->utils->initCurrencyConfig();

        $variables = [
            // 'redirect_block_status' => $this->utils->getConfig('common_block_page_url'),
            'origin' => $origin,
            'siteLang' => $siteLang,
            'role' => 'player',
            'default_prefix_for_username' => $default_prefix_for_username,
            'view_template' => $view_template,
            'liveChatUsed' => $this->CI->utils->getCurrentLiveChatUsed(),
            'player_live_chat_script' => $player_live_chat_script,

            'currency' => $this->CI->utils->getCurrentCurrency(),
            'currency_display_order' => $this->CI->utils->getConfig('display_currency_order'),
            'currency_display_options' => $this->CI->utils->getDefaultPlayerCenterCurrencyDisplayOptions(),
            'ui' => [
                'loginIframeName' => '_login_iframe',
                'logoutIframeName' => '_logout_iframe',
                'loginContainer' => '#_player_login_area',
                'playerInfoContainer' => '#_player_info_area',
                'playerRegisterContainer' => '._player_register_area',
                'playerPromoContainer' => '#_promo_area',
            ],
            'templates' => [
                'login_template' => $site->login_template,
                'logged_template' => $site->logged_template,
            ],
            'css' => [
                't1t-ui' => rawurlencode($this->CI->utils->getFileFromCache(APPPATH . '/../public/resources/player/ui/t1t-ui.min.css')),
                'material' => rawurlencode($this->CI->utils->getFileFromCache(APPPATH . '/../public/resources/player/material.css'))
            ]
        ];

        return $variables;
    }

    protected function _generate_common_variables($site_name){
        $variables = $this->_common_variables($site_name);
        $variables_str = json_encode($variables);

        return <<<JAVASCRIPT
var common_variables = {$variables_str};
var variables = {};
JAVASCRIPT;
    }

    public function getVariables(){
        $this->CI->load->model(array('static_site', 'cms_model', 'common_token', 'queue_result', 'internal_message', 'wallet_model','player','country_rules'));
        $this->CI->load->library(array('language_function', 'session'));

        if($this->CI->utils->getConfig('set_language_by_subdomain')){
            $siteLang = $this->CI->setLanguageBySubDomainPub();

            $this->CI->language_function->setCurrentLanguage($this->CI->language_function->langStrToInt($siteLang));
            $this->CI->utils->loadLanguage($siteLang, 'main', true);
        }else{
            $this->CI->utils->initiateLang();
        }

        // check aff domain get tracking code or null
        $trackingCode = $this->CI->getTrackingCode();
        if(!empty($trackingCode)){
            $this->CI->setTrackingCodeToSession($trackingCode);
        }
        // $this->session->set_userdata('tracking_code', $trackingCode);
        $trackingSourceCode = $this->CI->getTrackingSourceCode();
        $this->CI->session->set_userdata('tracking_source_code', $trackingSourceCode);

        // check aff domain get tracking code or null
        $agentTrackingCode = $this->CI->getAgentTrackingCode();
        if(!empty($agentTrackingCode)){
            $this->CI->setAgentTrackingCodeToSession($agentTrackingCode);
        }
        // $this->session->set_userdata('tracking_code', $trackingCode);
        $agentTrackingSourceCode = $this->CI->getAgentTrackingSourceCode();
        $this->CI->session->set_userdata('agent_tracking_source_code', $agentTrackingSourceCode);

        $enabled_check_frondend_block_status = $this->CI->utils->isEnabledFeature('enabled_check_frondend_block_status');
        $ip = $this->CI->utils->getIP();
        // OGP-12331: separate www/m site blocking from country blocking
        $isSiteBlock = $this->CI->country_rules->getBlockedStatus($ip, 'blocked_www_m');
        list($city, $countryName) = $this->CI->utils->getIpCityAndCountry($ip);
        $block_page_url = $this->CI->country_rules->getBlockedPageUrl($countryName, $city);

        $this->CI->utils->debug_log('isSiteBlock', $isSiteBlock, 'ip', $ip, 'block_page_url', $block_page_url, 'city', $city, 'countryName', $countryName);

        $defaultErrorMessage = lang("error.default.message");

        $main_host = $this->CI->utils->getMainHostName();

        $this->CI->utils->initCurrencyConfig();

        $variables = [
            'debugLog' => $this->CI->utils->isDebugMode(), // ? 'true' : 'false';,
            'cms_version' => $this->CI->utils->getCmsVersion(),

            'websocket_server' => $this->CI->config->item('websocket_server_host'),
            'enabled_web_push' => $this->CI->utils->getConfig('enabled_web_push'),

            'assetBaseUrl' => $this->CI->utils->getSystemUrl("player", "/resources/player"),
            'apiBaseUrl' => $this->CI->utils->getAsyncApiUrl(),

            'trackingCode' => $trackingCode,
            'trackingSourceCode' => $trackingSourceCode,
            'agentTrackingCode' => $agentTrackingCode,
            'agentTrackingSourceCode' => $agentTrackingSourceCode,

            'enabled_switch_to_mobile_on_www' => $this->CI->utils->isEnabledFeature('enabled_switch_to_mobile_on_www'),
            'enabled_auto_switch_to_mobile_on_www' => $this->CI->utils->isEnabledFeature('enabled_auto_switch_to_mobile_on_www'),
            'enabled_switch_to_mobile_dir_on_www' => $this->CI->utils->isEnabledFeature('enabled_switch_to_mobile_dir_on_www'),
            'enabled_switch_www_to_https' => $this->CI->utils->isEnabledFeature('enabled_switch_www_to_https'),
            'auto_redirect_to_https_list' => $this->CI->utils->getConfig('auto_redirect_to_https_list'),
            'donot_auto_redirect_to_https_list' => $this->CI->utils->getConfig('donot_auto_redirect_to_https_list'),
            'adjust_domain_to_wwww' => $this->CI->utils->isEnabledFeature('adjust_domain_to_wwww'),

            'popup_window_on_player_center_for_mobile' => $this->CI->utils->isEnabledFeature('popup_window_on_player_center_for_mobile'),

            'popup_deposit_after_login' => $this->CI->utils->getConfig('popup_deposit_after_login'),

            'refresh_balance_interval_millisecond' => $this->CI->utils->getConfig('refresh_balance_interval_millisecond'),
            'auto_refresh_cold_down_time_milliseconds'=>$this->CI->utils->getConfig('auto_refresh_cold_down_time_milliseconds'),

            'enabled_check_frondend_block_status' => $enabled_check_frondend_block_status,
            'block_status' => $isSiteBlock,
            'block_page_url' => $block_page_url,

            'enabled_switch_language_also_set_to_static_site' => $this->CI->utils->isEnabledFeature('enabled_switch_language_also_set_to_static_site'),

            'defaultErrorMessage' => $defaultErrorMessage,
            'langText' => $this->CI->utils->generate_lang_text_array(),
            'online_players_count' => 0, //$this->player->getOnlinePlayers(),
            'is_mobile' => $this->CI->utils->is_mobile(),
            'main_host' => $main_host,
            'host' => "player." . $main_host,
            'hosts' => [
                'www' => "www." . $main_host,
                'player' => "player." . $main_host,
                'm' => "m." . $main_host,
                'aff' => "aff." . $main_host,
                'agency' => "agency." . $main_host
            ],
            'urls' => [
                'www' => $this->CI->utils->getSystemUrl("www"),
                'player' => $this->CI->utils->getSystemUrl("player"),
                'm' => $this->CI->utils->getSystemUrl("m"),
                'aff' => $this->CI->utils->getSystemUrl("aff"),
                'agency' => $this->CI->utils->getSystemUrl("agency")
            ],
            'currency' => $this->CI->utils->getCurrentCurrency(),
            'currency_display_order' => $this->CI->utils->getConfig('display_currency_order'),
            'currency_display_options' => $this->CI->utils->getDefaultPlayerCenterCurrencyDisplayOptions()
        ];

        $variables['ui'] = [
            'loginUrl' => $this->CI->utils->getSystemUrl("player", '/iframe_module/iframe_login'),
            'logoutUrl' => $this->CI->utils->getSystemUrl("player", '/iframe_module/iframe_logout'),
            'player_login_url' => $this->CI->utils->getPlayerLoginUrl(),
            'player_logout_url' => $this->CI->utils->getPlayerLogoutUrl(),
            'loginWindowUrl' => $this->CI->utils->getSystemUrl("player", '/iframe/auth/login'),
            'forgotPasswordUrl' => $this->CI->utils->getPlayerForgotPassword(),
            'enabled_refresh_balance_on_player' => $this->CI->utils->isEnabledFeature('auto_refresh_balance_on_cashier'),
            'disable_account_transfer_when_balance_check_fails' => (int)$this->CI->utils->isEnabledFeature('disable_account_transfer_when_balance_check_fails'),
            'captchaUrl' => $this->CI->utils->getSystemUrl("player", '/iframe/auth/captcha/static_site'),
            'captchaUrlWithRand' => $this->CI->utils->getSystemUrl("player", '/iframe/auth/captcha/static_site?' . random_string('alnum')),
            'captchaFlag' => $this->CI->operatorglobalsettings->getSettingIntValue('captcha_registration') && $this->CI->config->item('captcha_login') ? true : false,
            'captchaFlagReg' => $this->CI->operatorglobalsettings->getSettingJson('registration_captcha_enabled') ? true : false,
            'captchaFlagLogin' => $this->CI->operatorglobalsettings->getSettingJson('login_captcha_enabled') ? true : false,

            'available_currency_list'=>$this->CI->utils->getAvailableCurrencyList(),
            'active_currency_on_mdb'=>$this->CI->utils->getActiveCurrencyKeyOnMDB(),
            'is_enabled_mdb'=>$this->CI->utils->isEnabledMDB(),
            'currency_select_html'=>$this->buildCurrencySelectHtml(),
        ];

        $variables['template'] = [
            'playercenter_logo' => $this->CI->utils->getPlayerCenterLogoURL(),
            'img_dir' => $this->CI->utils->getSystemUrl("player", '/' . $this->CI->utils->getPlayerCenterTemplate() . '/img'),
        ];

        /* @var $addons_instance Player_main_js_addons_abstract */
        foreach($this->_enabled_addons as $addons_name => $addons_instance){
            $addons_variables = $addons_instance->variables();
            if(empty($addons_variables)){
                continue;
            }

            $variables = array_replace_recursive($variables, $addons_variables);
        }

        return $variables;
    }

    public function buildCurrencySelectHtml(){

        $this->CI->load->library(['authentication']);
        $logged = $this->CI->authentication->isLoggedIn();
        $addItemAll=false;
        return $this->CI->utils->buildCurrencySelectHtml($logged, $addItemAll, 'currency_list');

        // $html='';
        // if($this->CI->utils->isEnabledMDB()){
        //     $this->CI->load->library(['authentication']);
        //     $logged = $this->CI->authentication->isLoggedIn();

        //     $list=$this->CI->utils->getAvailableCurrencyList();
        //     $active_currency_key=$this->CI->utils->getActiveCurrencyKeyOnMDB();
        //     if(!empty($list)){
        //         $id=$logged ? '_select_currecny_on_logged' : '_select_currecny_on_login';
        //         $html='<!-- active: '.$active_currency_key.' --><select id="'.$id.'" class="currency_list">';
        //         foreach ($list as $currencyKey => $currencyInfo) {
        //             $selected=$currencyKey==$active_currency_key ? 'selected' : '' ;
        //             $html.='<option value="'.$currencyKey.'" '.$selected.' >'.$currencyInfo['symbol'].' '.$currencyInfo['code'].'</option>';
        //         }
        //         $html.='</select>';
        //     }
        // }

        // return $html;
    }

}