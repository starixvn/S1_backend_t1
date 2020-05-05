<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Country_rules
 *
 * it's for player and frontend
 */
class Country_rules extends BaseModel {

    const WHITE_LIST = 1;
    const BLOCK_LIST = 2;
    const ITEM_WWW_IP_WHITE_LIST= 'www_ip_white_list';
    const ITEM_WWW_IP_BLOCK_LIST= 'www_ip_block_list';
    const ENABLED = 1;
    const DISABLED = 2;

    const SUBSITE_BLOCKED = 1;
    const SUBSITE_ALLOWED = 2;

    private $tableName = 'country_rules';

    public function __construct() {
        parent::__construct();
        $this->load->model('operatorglobalsettings');
    }

    public function getCountryRules( $is_export = false ) {
        $this->db->select('country_rules.*, adminusers.username');
        $this->db->from($this->tableName);
        $this->db->join('adminusers', 'adminusers.userId = country_rules.created_by', 'left');
        $query = $this->db->get()->result();

        if( $is_export ) return $query;

        $data['aaData'] = $query;
        return json_encode($data);
    }

    public function deleteCountryRules($countryIds) {
        if (!empty($countryIds)) {
            $this->db->where_in('id', $countryIds);
            $this->db->delete($this->tableName);
        }
    }

    public function blockCountryRules($countryIds, $flag = '') {
        if (!empty($countryIds)) {
            if ($flag == self::WHITE_LIST) {
                $this->db->set('flag', self::WHITE_LIST);
                $message = lang('Success Allow');
            } else {
                $this->db->set('flag', self::BLOCK_LIST);
                // OGP-12331
                $this->db
                    ->set('is_affiliate', self::SUBSITE_BLOCKED)
                    ->set('is_agent', self::SUBSITE_BLOCKED)
                    ->set('blocked_www_m', self::SUBSITE_BLOCKED);
                $message =  lang('Success Block');
            }
            $this->db->where_in('id', $countryIds);
            $this->db->update($this->tableName);
        }
        return array('message' => $message);
    }

    public function enableAffiliateOrAgency($ids, $flag, $field){
        $subsite = $field == 'is_agent' ? 'Agency' : 'Affiliate';
        if ($flag == self::ENABLED) {
            $this->db->set($field, self::DISABLED);
            $message = lang("{$subsite} site unblocked");
        } else {
            $this->db->set($field, self::ENABLED);
            $message =  lang("{$subsite} site blocked");
        }
        $this->db->where_in('id', $ids);
        $this->db->update($this->tableName);

        return array('message' => $message);
    }

    public function enableWwwm($ids, $flag){
        if ($flag == self::ENABLED) {
            $this->db->set('blocked_www_m', self::DISABLED);
            $message = lang('WWW/M sites unblocked');
        } else {
            $this->db->set('blocked_www_m', self::ENABLED);
            $message =  lang('WWW/M sites blocked');
        }
        $this->db->where_in('id', $ids);
        $this->db->update($this->tableName);

        return array('message' => $message);
    }

    public function addCountries($post) {
        $countries = unserialize(COUNTRY_ISO2);
        if(sizeof($post['country'])) {
            foreach($post['country'] as $countryCode) {
                foreach ($countries as $key => $value) {
                    if($countryCode == $value){
                        $data = array(
                            'country_name' => ucfirst(strtolower($key)),
                            'country_code' => $countryCode,
                            'created_at'   => $this->utils->getNowForMysql(),
                            'created_by'   => $this->session->userdata('user_id'),
                            'flag'         => self::WHITE_LIST,
                            'notes'        => $post['status']
                        );
                        $this->db->insert($this->tableName, $data);
                    }
                }
            }
        }
    }

    public function isCountryBlocked($countryName, $partner=null) {
        $this->db->from($this->tableName);
        $this->db->where(['country_name' => $countryName, 'flag' => self::BLOCK_LIST]);
        if(!empty($partner)){ // $partner:  Affiliate Or Agency
            $this->db->where([$partner => self::ENABLED ]);
        }
        $query = $this->db->get()->row_array();
        return !empty($query) ? true : false;
    }

    public function isCountryAllowed($countryName) {
        $this->db->from($this->tableName);
        $this->db->where(['country_name' => $countryName, 'flag' => self::WHITE_LIST]);
        $query = $this->db->get()->row_array();
        return !empty($query) ? true : false;
    }

    public function getCountryRulesMode(){
        return $this->operatorglobalsettings->getSettingValue('country_rules_mode', $this->utils->getConfig('coutry_rules_mode'));

        // return $this->utils->getConfig('coutry_rules_mode');
    }

    public function getBlockedPageUrl($countryName = NULL, $city = NULL){
        $default_blocked_page_url = $this->operatorglobalsettings->getSettingValue('block_page_url', $this->utils->getConfig('blocked_page_url'));

        if(empty($countryName)){
            return $this->getAvailableBlockedUrl($default_blocked_page_url);
        }

        $blocked_page_url_with_locale = (array)$this->utils->getConfig('blocked_page_url_with_locale');

        $country_blocked_url_list = (isset($blocked_page_url_with_locale[$countryName])) ? $blocked_page_url_with_locale[$countryName] : $default_blocked_page_url;

        if(!is_array($country_blocked_url_list)){
            return $this->getAvailableBlockedUrl((!empty($country_blocked_url_list)) ? $country_blocked_url_list : $default_blocked_page_url);
        }

        $city_blocked_url_list = (isset($country_blocked_url_list[$city])) ? $country_blocked_url_list[$city] : $default_blocked_page_url;

        return $this->getAvailableBlockedUrl((!empty($city_blocked_url_list)) ? $city_blocked_url_list : $default_blocked_page_url);
    }

    public function getAvailableBlockedUrl($url){
        // return $url;
        return ($this->CI->utils->isAvailableUrl($url)) ? $url : ($this->CI->utils->is_mobile() ? $this->CI->utils->getSystemUrl('m', $url) : $this->CI->utils->getSystemUrl('www', $url));
    }

    public function getWWWIpWhiteListJson(){
        $www_white_ip_list=$this->utils->encodeJson($this->operatorglobalsettings->getSettingJson(self::ITEM_WWW_IP_WHITE_LIST, 'template', $this->utils->getConfig('www_white_ip_list')));
        return $www_white_ip_list;

    }

    public function getWWWIpBlockListJson(){
        $www_block_ip_list=$this->utils->encodeJson($this->operatorglobalsettings->getSettingJson(self::ITEM_WWW_IP_BLOCK_LIST, 'template', $this->utils->getConfig('www_block_ip_list')));
        return $www_block_ip_list;
    }

    public function getWWWIpWhiteList(){
        $www_white_ip_list=$this->operatorglobalsettings->getSettingJson(self::ITEM_WWW_IP_WHITE_LIST, 'template', $this->utils->getConfig('www_white_ip_list'));
        return $www_white_ip_list;

    }

    public function getWWWIpBlockList(){
        $www_block_ip_list=$this->operatorglobalsettings->getSettingJson(self::ITEM_WWW_IP_BLOCK_LIST, 'template', $this->utils->getConfig('www_block_ip_list'));
        return $www_block_ip_list;
    }

    public function isIpAllowed($ip){
        $allowed=false;
        $www_white_ip_list=$this->getWWWIpWhiteList();
        $this->utils->debug_log('www_white_ip_list', $www_white_ip_list);
        //$this->utils->getConfig('www_white_ip_list');
        if(!empty($www_white_ip_list) && is_array($www_white_ip_list)){
            $allowed=in_array($ip, $www_white_ip_list);
        }

        return $allowed;
    }

    public function isIpBlocked($ip){
        $blocked=false;
        $www_block_ip_list=$this->getWWWIpBlockList();
        $this->utils->debug_log('www_block_ip_list', $www_block_ip_list);
        // $this->utils->getConfig('www_block_ip_list');
        if(!empty($www_block_ip_list) && is_array($www_block_ip_list)){
            $blocked=in_array($ip, $www_block_ip_list);
        }

        return $blocked;
    }

    public function getBlockedStatus($ip, $partner = null){

        $mode=$this->getCountryRulesMode();

        $isSiteBlock = $mode=='deny_all';

        list($city, $country) = $this->utils->getIpCityAndCountry($ip);

        if($this->isCountryBlocked($country, $partner)) {
            $isSiteBlock = true;
        }elseif($this->isCountryAllowed($country)){
            $isSiteBlock = false;
        }
        if($this->isIpAllowed($ip)){
            $isSiteBlock=false;
        }
        //block > allow
        if($this->isIpBlocked($ip)){
            $isSiteBlock=true;
        }

        $this->utils->debug_log('check_block_site_status ip', $ip, 'city',$city, 'country', $country, 'site block status', $isSiteBlock);

        return $isSiteBlock;
    }

    public function setCountryRulesSetting($rulesMode, $blockPageUrl) {
        if($rulesMode) {
            $this->operatorglobalsettings->setOperatorGlobalSetting([ 'name' => 'country_rules_mode', 'value' => $rulesMode ]);
        }
        $this->operatorglobalsettings->setOperatorGlobalSetting([ 'name' => 'block_page_url', 'value' => $blockPageUrl ]);

        return true;
    }
}

/* End of file country_rules.php *
/* Location: ./application/models/country_rules.php */
