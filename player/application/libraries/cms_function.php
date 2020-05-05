<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cmsbanner_functions
 *
 * Cmsbanner_functions library for FHI OG System
 *
 * @package		Cmsbanner_functions
 * @author		ASRII
 * @version		1.0.0
 */

class Cms_function {
	function __construct() {
		$this->ci =& get_instance();
		$this->ci->load->library(array('session'));
		$this->ci->load->model(array('cms_model'));
	}

	/**
     * get all cms banner
     *
     * @param   int
     * @param   string
     * @return  set session user data
     */
	function getCmsBanner($bannerType) {
		return $this->ci->cms_model->getCmsBanner($bannerType);
	}

	/**
     * get all cms footer
     *
     * @param   int
     * @param   string
     * @return  set session user data
     */
	function getCmsFooterLinks() {
		return $this->ci->cms_model->getCmsFooterLinks();
	}

	/**
     * get all cms footer content
     *
     * @param   int
     * @param   string
     * @return  set session user data
     */
	function getCmsFooterContent($footerlinkId) {
		return $this->ci->cms_model->getCmsFooterContent($footerlinkId);
	}

     /**
     * get all cms content
     *
     * @param   int
     * @param   string
     * @return  set session user data
     */
     function getCmsContentDetail($contentId) {
          return $this->ci->cms_model->getCmsFooterContent($contentId);
     }

	/**
     * get all cms footer content
     *
     * @param   int
     * @param   string
     * @return  set session user data
     */
	function getCmsFooterContentData() {
		return $this->ci->cms_model->getCmsFooterContentData();
	}

}