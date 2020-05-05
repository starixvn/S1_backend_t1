<?php


/**
 * Encapsulate some affiliate operations for convenience of Api_common
 * OGP-17093
 * @copyright Tripleonetech April 2020
 */
class affiliate_lib {

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->model([ 'affiliatemodel' ]);
		$this->ci->load->library([ 'utils' ]);
	}

	/**
	 * Ported from aff/application/controllers/affiliate.php
	 * OGP-17093
	 * @see		comapi_core_aff::aff_login()
	 * @param	array 	$affiliate		Aff entry return of Affiliatemodel::login()
	 * @param  	bool	$is_readonly	flag of readonly login.  False for API login scenario.
	 * @param	string	$language		aff language.  Defaults to null.
	 *
	 * @return	bool	Always true.
	 */
	public function after_login_affiliate($affiliate, $is_readonly, $language= null){

		$success=true;

		# OGP-1184 limit aff backend account can only login with one device
		// $this->ci->db->where('affiliate_id', $affiliate['affiliateId'])->delete('ci_aff_sessions');

		// $this->ci->session->set_userdata(array(
		// 	'affiliateUsername' => $affiliate['username'],
		// 	'affiliateId' => $affiliate['affiliateId'],
		// 	'affiliateTrackingCode' => $affiliate['trackingCode'],
		// 	'afflang' => $language,
		// 	'readonly' => $is_readonly,
		// ));

		// $this->ci->session->updateLoginId('affiliate_id', $affiliate['affiliateId']);

        // $this->ci->session->set_flashdata('is_login_behavior', 1);

		$data = array(
			'lastLoginIp' => $this->ci->utils->getIP(), //$_SERVER['SERVER_ADDR'],
			'lastLogin' => $this->ci->utils->getNowForMysql(),
			// 'lastLogout' => date('Y-m-d H:i:s'),
		);
		$this->ci->affiliatemodel->editAffiliates($data, $affiliate['affiliateId']);

		return $success;
	}

} // End class affiliate_lib