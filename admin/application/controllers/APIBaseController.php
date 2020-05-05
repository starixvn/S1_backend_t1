<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Class APIBaseController
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
class APIBaseController extends BaseController {
    const API_ACL_TYPE_IP_BANDWIDTH = 'ip_bandwidth';

    const API_ACL_RULE_TIME_RANGE = 'time_range';

    const API_ACL_RESULT_SUCCESS = 0;
    const API_ACL_RESULT_TIME_RANGE_FAILED = 1;

    protected $_api_acl_config = NULL;
    protected $_last_check_api_acl_response = NULL;

	public function __construct() {
		parent::__construct();
		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->library('authentication');
		$this->load->helper(array('text'));
		$this->utils->initiateLang();

		$this->_api_acl_config = array_replace_recursive($this->utils->getConfig('api_acl'), $this->utils->getConfig('api_acl_override'));
	}

	protected function _get_acl_config($config_key){
	    return (isset($this->_api_acl_config[$config_key])) ? $this->_api_acl_config[$config_key] : NULL;
    }

	protected function _check_api_acl($config_key = NULL, $base_config_key = NULL){
	    $config_key = (empty($config_key)) ? $this->uri->uri_string() : $config_key;
        $base_config_key = (empty($base_config_key)) ? 'default' : $base_config_key;
	    $config = $this->_get_acl_config($config_key);
	    $base_config = $this->_get_acl_config($base_config_key);

	    if(empty($base_config)){
	        return static::API_ACL_RESULT_SUCCESS;
        }

        $config = (!empty($config)) ? array_replace_recursive($base_config, $config) : $base_config;

        $result = static::API_ACL_RESULT_SUCCESS;

        foreach($config as $api_acl_type => $api_acl_type_rule){
	        if(!$api_acl_type_rule['enabled']){
	            continue;
            }
	        switch($api_acl_type){
                case static::API_ACL_TYPE_IP_BANDWIDTH:
                    $result = $this->_check_acl_by_ip_bandwidth($config_key, $api_acl_type_rule);
                    break;
                default:
                    continue;
            }

            if($result !== static::API_ACL_RESULT_SUCCESS){
	            break;
            }
        }

        if($result === static::API_ACL_RESULT_SUCCESS){
            return $result;
        }

        $response = (!empty($api_acl_type_rule) && isset($api_acl_type_rule['response'])) ? $api_acl_type_rule['response'] : ['code' => 403, 'msg' => 'Access Denied'];

        $this->_last_check_api_acl_response = $response;

        return $result;
    }

    protected function _show_last_check_acl_response($return_type = NULL){
	    switch($return_type){
            case 'json':
                $this->output->set_status_header($this->_last_check_api_acl_response['code']);
                return $this->returnCommon(self::MESSAGE_TYPE_ERROR, $this->_last_check_api_acl_response['msg'], NULL, NULL, 'json');
                break;
            default:
                show_error(lang($this->_last_check_api_acl_response['msg']), $this->_last_check_api_acl_response['code']);
                break;
        }
    }

    protected function _check_acl_by_ip_bandwidth($config_key, $rule){
        $client_ip = $this->utils->getIp();

        $redis_key = $config_key . '-' . static::API_ACL_TYPE_IP_BANDWIDTH . '-' . $client_ip;

        return $this->_check_acl_rule($redis_key, $rule);
    }

    protected function _check_acl_rule($redis_key, $rule){
        $result = NULL;
        switch($rule['type']){
            case static::API_ACL_RULE_TIME_RANGE:
                $result = $this->_check_acl_rule_by_time_range($redis_key, $rule);
                break;
        }

        return $result;
    }

    protected function _check_acl_rule_by_time_range($redis_key, $rule){
        $rule_data = json_decode($this->utils->readRedis($redis_key), TRUE);

        $time_range = (isset($rule['time_range'])) ? $rule['time_range'] : 60;
        $limit_value = (isset($rule['limit_value'])) ? $rule['limit_value'] : 10;
        $blocked_time_limit = (isset($rule['blocked_time'])) ? $rule['blocked_time'] : 600;

        $current_time = time();
        $first_access_time = (isset($rule_data['first_access_time'])) ? $rule_data['first_access_time'] : $current_time;
        $last_access_time = (isset($rule_data['last_access_time'])) ? $rule_data['last_access_time'] : $current_time;
        $access_count = (isset($rule_data['access_count'])) ? $rule_data['access_count'] : 0;
        $blocked_time = (isset($rule_data['blocked_time'])) ? $rule_data['blocked_time'] : $current_time;
        $access_count = $access_count + 1;

        if($blocked_time > $current_time){
            $is_blocked = TRUE;
        }elseif($blocked_time < $current_time){ // Blocked time expired.
            $is_blocked = FALSE;

            $last_access_time = $first_access_time;
        }else{
            $is_blocked = FALSE;
        }

        // var_dump($current_time, $first_access_time, $last_access_time, $access_count);

        $status = static::API_ACL_RESULT_SUCCESS;

        if((($current_time - $last_access_time) > $time_range) && (!$is_blocked)){
            $rule_data['first_access_time'] = $current_time;
            $rule_data['last_access_time'] = $current_time;
            $rule_data['access_count'] = 1;
            $rule_data['blocked_time'] = $current_time;
        }else{
            $rule_data['first_access_time'] = $first_access_time;
            $rule_data['last_access_time'] = $current_time; // always use current time
            $rule_data['access_count'] = $access_count;

            if($access_count > $limit_value){
                $status = static::API_ACL_RESULT_TIME_RANGE_FAILED;

                if(!$is_blocked){
                    $rule_data['blocked_time'] = $current_time + $blocked_time_limit;
                }
            }
        }

        $this->utils->debug_log(__METHOD__, $redis_key, $rule_data);

        $this->utils->writeRedis($redis_key, json_encode($rule_data), $blocked_time_limit);

        return $status;
    }
}
/////END OF FILE//////////////
