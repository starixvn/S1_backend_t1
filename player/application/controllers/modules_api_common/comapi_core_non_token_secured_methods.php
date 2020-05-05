<?php

/**
 * Api_common core module: Non token-secured methods
 * Separated 9/25/2019
 * @see		api_common.php
 */
trait comapi_core_non_token_secured_methods {

	public function isIpBlocked() {
		$api_key = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }

        $res = null;

        try {
        	$req_ip		= $this->input->post('req_ip', true);
        	$request = [ 'api_key' => $api_key, 'req_ip' => $req_ip ];
        	$this->comapi_log(__METHOD__, 'request', $request);

        	if (empty(filter_var($req_ip, FILTER_VALIDATE_IP))) {
        		throw new Exception('IP malformed', self::CODE_IPBLK_MALFORMED_IP);
        	}

        	$this->load->model([ 'country_rules' ]);

        	list($req_city, $req_country) = $this->utils->getIpCityAndCountry($req_ip);

        	$flag_is_ip_blocked = $this->country_rules->getBlockedStatus($req_ip);

        	$res_isblock = [
        		'is_blocked'	=> $flag_is_ip_blocked ,
        		'city'			=> $req_city ,
        		'country'		=> $req_country
        	];

            $ret = [
                'success'   => true ,
                'code'      => self::CODE_SUCCESS ,
                'mesg'      => lang('IP blocked status returned successfully') ,
                'result'    => $res_isblock
            ];

            $this->comapi_log(__METHOD__, 'Successful return', $ret);
        }
        catch (Exception $ex) {
            $this->comapi_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => $res
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
	}

    /**
     * Gets all or partial system features
     *
     * @uses    string  POST: api_key       api key given by system
     * @uses    string  POST: feature_names string or comma separated string (xxx,yyy,...)
     * @see     Comapi_lib::gsf_get_sys_features()
     *
     * @return  JSON    Standard JSON return structure
     */
    public function getSysFeatures() {
        $api_key = $this->input->post('api_key');

        if (!$this->__checkKey($api_key)) { return; }

        $res = null;

        try {
            $feature_names = trim($this->input->post('feature_names', true));
            if (!empty($feature_names)) {
                $feature_names = explode(',', $feature_names);
            }

            $creds = [ 'api_key' => $api_key, 'feature_names' => $feature_names ];
            $this->utils->debug_log(__METHOD__, 'request', $creds);

            // Get all or selected features
            list($features_selected, $features_log, $features_mode) = $this->comapi_lib->gsf_get_sys_features($feature_names);

            // Read all register settings
            $reg_settings = $this->comapi_lib->gsf_get_reg_settings();

            $res = [ 'features' => $features_selected ];
            $log_res = [ 'features' => $features_log ];

            $ret = [
                'success'   => true,
                'code'      => self::CODE_SUCCESS,
                'mesg'      => "{$features_mode} Feature(s) retrieved successfully",
                'result'    => $res
            ];

            $this->utils->debug_log(__METHOD__, 'response', $log_res, $creds);
        }
        catch (Exception $ex) {
            $this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => $res
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    } // End function getSysFeature()

    /**
     * Gets all registration settings from config, mostly obsolete
     * @deprecated
     *
     * @uses    string  POST: api_key       api key given by system
     * @see     Comapi_lib::gsf_get_reg_settings()
     *
     * @return  JSON    Standard JSON return structure
     */
    public function getReg0Settings() {
        $api_key = $this->input->post('api_key');

        if (!$this->__checkKey($api_key)) { return; }

        $res = null;

        try {

            $creds = [ 'api_key' => $api_key ];
            $this->utils->debug_log(__METHOD__, 'request', $creds);

            // Read all register settings
            $reg_settings = $this->comapi_lib->gsf_get_config_reg_settings();

            $res = $reg_settings;

            $ret = [
                'success'   => true,
                'code'      => self::CODE_SUCCESS,
                'mesg'      => "Registration settings retrieved successfully",
                'result'    => $res
            ];

            $this->utils->debug_log(__METHOD__, 'response', $res, $creds);
        }
        catch (Exception $ex) {
            $this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => $res
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    } // End function getReg0Settings()

    /**
     * Gets all player registration settings from SBE
     *
     * @uses    string  POST: api_key       api key given by system
     * @see     Comapi_lib::gsf_get_reg_settings()
     *
     * @return  JSON    Standard JSON return structure
     */
    public function getRegSettings() {
        $api_key = $this->input->post('api_key');

        if (!$this->__checkKey($api_key)) { return; }

        $res = null;

        try {

            $creds = [ 'api_key' => $api_key ];
            $this->utils->debug_log(__METHOD__, 'request', $creds);

            // Read all register settings
            $reg_settings = $this->comapi_lib->gsf_get_sbe_reg_settings();

            $res = $reg_settings;

            $ret = [
                'success'   => true,
                'code'      => self::CODE_SUCCESS,
                'mesg'      => "Registration settings retrieved successfully",
                'result'    => $res
            ];

            $this->utils->debug_log(__METHOD__, 'response', $res, $creds);
        }
        catch (Exception $ex) {
            $this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => $res
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    } // End function getRegSettings()

} // End of trait comapi_core_non_token_secured_methods
