<?php

/**
 * Api_common core module: affiliate methods
 * Built 4/07/2020
 * @see		   api_common.php
 * @copyright  Tripleonetech April 2020
 */
trait comapi_core_aff {

    /**
     * Login endpoint for affiliate users, converted from aff/Affiliate::login()
     * Returns aff login token, which is used in all subsequent aff method calls
     * OGP-17093
     *
     * @uses    string  POST:api_key    The api_key, as md5 sum. Required.
     * @uses    string  POST:username   Affiliate username.  Required.
     * @uses    string  POST:password   Affiliate password.  Required.
     *
     * @see     aff/Affiliate::login()
     * @see     comapi_core_aff::aff_logout()
     * @return  JSON    Standard return tuple: [ success, code, mesg, result ]
     *                           in result: [ token, username, lastLogin, lastLoginIp ]
     */
    public function affLogin() {
        $api_key    = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }

        $this->load->model([ 'affiliatemodel', 'common_token' ]);
        $this->load->library([ 'affiliate_lib' ]);
        try {
            $username   = trim($this->input->post('username', 1));
            $password   = trim($this->input->post('password', 1));
            $request        = [ 'api_key' => $api_key, 'username' => $username, 'password' => $password ];
            $this->comapi_log(__FUNCTION__, 'request', $request);

            $encryptedPassword = $this->utils->encodePassword($password);

            $login_mesg = null;
            $al_res = $this->affiliatemodel->login($username, $encryptedPassword, $password, $additionalMessage, $login_mesg);

            if (!$al_res) {
                throw new Exception(lang('Cannot login, wrong username/password'), self::CODE_AFL_LOGIN_FAILURE);
            }

            switch ($al_res['status']) {
                case 1 :
                    throw new Exception(lang('Cannot login, account not activated yet'), self::CODE_AFL_ACC_NOT_ACTIVATED);
                    break;
                case 2 :
                    throw new Exception(lang('Cannot login, account frozen'), self::CODE_AFL_ACC_FROZEN);
                    break;
                default :
                    // Login successful
                    // $this->after_login_affiliate($result, $is_readonly, $language);
                    $this->affiliate_lib->after_login_affiliate($al_res, false, null);
                    break;
            }

            $token = $this->common_token->getAffiliateToken($al_res['affiliateId']);

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => lang('Affiliate logged in successfully'),
                'result'    => [
                    'token'         => $token ,
                    'username'      => $al_res['username'] ,
                    'lastLogin'     => $al_res['lastLogin'] ,
                    'lastLoginIp'   => $al_res['lastLoginIp'] ,
                ]
            ];

            $this->comapi_log(__FUNCTION__, 'Response', $ret);
        }
        catch (Exception $ex) {
            $this->comapi_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);
            // $this->session->unset_userdata([
            //     'affiliateUsername' => null,
            //     'affiliateId' => null,
            //     'last_time_enter_second_password' => null,
            //     'next_uri' => null,
            // ]);
            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }

    } // End function aff_login()

    /**
     * Internal method to check aff user login status by (username, token) pair
     * OGP-17093
     * @param   string  $aff_username   Affiliate username
     * @param   string  $aff_token      Affiliate token
     * @return  array   [ code, mesg ]
     */
    protected function _isAffLoggedIn($aff_username, $aff_token) {
        $this->load->model([ 'affiliatemodel', 'common_token' ]);
        try {
            $aff_id = $this->affiliatemodel->getAffiliateIdByUsername($aff_username);
            if (empty($aff_id)) {
                throw new Exception(lang('Invalid affiliate username'), self::CODE_AFF_COMMON_INVALID_USERNAME);
            }

            if (empty($aff_token)) {
                throw new Exception(lang('Invalid affiliate token'), self::CODE_AFF_COMMON_INVALID_TOKEN);
            }

            $aff_token_valid = $this->common_token->getAffiliateToken($aff_id);

            if ($aff_token_valid != $aff_token) {
                throw new Exception(lang('Invalid affiliate token'), self::CODE_AFF_COMMON_INVALID_TOKEN);
            }

            $ret = [ 'code' => 0, 'mesg' => 'Aff logged in', 'result' => [ 'aff_id' => $aff_id ] ];
        }
        catch (Exception $ex) {
            $ret = [ 'code' => $ex->getCode(), 'mesg' => $ex->getMessage() ];
        }
        finally {
            return $ret;
        }
    } // End function _isAffLoggedIn()

    /**
     * Logout endpoint for affiliate users, converted from aff/Affiliate::logout()
     * OGP-17094
     *
     * @uses    string  POST:api_key    The api_key, as md5 sum. Required.
     * @uses    string  POST:username   Affiliate username.  Required.
     * @uses    string  POST:token      Affiliate token.  Required.
     *
     * @see     aff/Affiliate::logout()
     * @see     comapi_core_aff::aff_login()
     * @return  JSON    Standard return tuple: [ success, code, mesg, result ]
     */
    public function affLogout() {
        $api_key    = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }

        $this->load->model([ 'affiliatemodel', 'common_token' ]);
        $this->load->library([ 'affiliate_lib' ]);
        try {
            $username   = trim($this->input->post('username', 1));
            $token      = trim($this->input->post('token', 1));
            $request        = [ 'api_key' => $api_key, 'username' => $username ];
            $this->comapi_log(__FUNCTION__, 'request', $request);

            // Check token validity
            // Common idiom for all affiliate API methods
            $log_check = $this->_isAffLoggedIn($username, $token);
            if ($log_check['code'] != 0) {
                throw new Exception($log_check['mesg'], $log_check['code']);
            }

            // Log user logout time
            $data = array(
                'lastLogout' => date('Y-m-d H:i:s'),
            );
            $aff_id = $this->affiliatemodel->getAffiliateIdByUsername($username);
            $this->affiliatemodel->editAffiliates($data, $aff_id);

            // Clear session data
            // $this->session->unset_userdata([
            //     'affiliateUsername' => null,
            //     'affiliateId' => null,
            //     'last_time_enter_second_password' => null,
            //     'next_uri' => null,
            // ]);

            // Invalidate token
            $this->common_token->disableToken($token);

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => lang('Affiliate logged out successfully'),
                'result'    => null
            ];

            $this->comapi_log(__FUNCTION__, 'Response', $ret);
        }
        catch (Exception $ex) {
            $this->comapi_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }

    } // End function aff_logout()

    /**
     * Sub-affiliate report
     * Converted from report_module_aff::get_subaffiliate_reports()
     * OGP-17088
     *
     * @uses    string      POST:api_key    The api_key, as md5 sum. Required.
     * @uses    string      POST:username   Affiliate username.  Required.
     * @uses    string      POST:token      Affiliate token.  Required.
     * @uses    datetime    POST:date_from  Start datetime of query
     * @uses    datetime    POST:date_to    End datetime of query
     * @uses    int         POST:offset     Offset for paging
     * @uses    int         POST:limit      Limit for paging
     *
     * @see     Affiliatemodel::get_subaffiliate_reports()
     * @see     report_module_aff::get_subaffiliate_reports()
     *
     * @return [type] [description]
     */
    public function reportSubAffs() {
        $api_key    = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }

        $this->load->model([ 'affiliatemodel', 'common_token' ]);
        $this->load->library([ 'affiliate_lib' ]);
        try {
            $username   = trim($this->input->post('username', 1));
            $token      = trim($this->input->post('token', 1));
            $date_from  = trim($this->input->post('date_from', 1));
            $date_to    = trim($this->input->post('date_to', 1));
            $offset     = (int) $this->input->post('offset', 1);
            $limit      = (int) $this->input->post('limit', 1);
            $request        = [ 'api_key' => $api_key, 'username' => $username, 'date_from' => $date_from, 'date_to' => $date_to ];
            $this->comapi_log(__FUNCTION__, 'request', $request);

            // Check token validity
            $log_check = $this->_isAffLoggedIn($username, $token);
            if ($log_check['code'] != 0) {
                throw new Exception($log_check['mesg'], $log_check['code']);
            }

            $aff_id = $log_check['result']['aff_id'];

            $report_subaff = $this->affiliatemodel->get_subaffiliate_reports($aff_id, $date_from, $date_to, $offset, $limit);

            // -----------------------------------------------------------

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => lang('Subaffiliate report generated successfully'),
                'result'    => $report_subaff
            ];

            $this->comapi_log(__FUNCTION__, 'Response', $ret);
        }
        catch (Exception $ex) {
            $this->comapi_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }

    } // End function aff_listSubAffs()


} // End of trait comapi_core_aff
