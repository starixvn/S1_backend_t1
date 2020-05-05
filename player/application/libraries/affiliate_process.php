<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Affiliate Process
 *
 * Affiliate Process library for FHI OG System
 *
 * @package     Affiliate Process
 * @author      Johann Merle
 * @version     1.0.0
 */

class Affiliate_process
{
    private $error = array();

    function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->library(array('session'));
        $this->ci->load->model(array('affiliate'));
    }

    /**
     * checking of affiliate
     *
     * @param  int
     * @return bool
     */
    public function checkAffiliateIfExisting($affiliate) {
        return $this->ci->affiliate->checkAffiliateIfExisting($affiliate);
    }

    /**
     * checking of affiliate options
     *
     * @param  int
     * @return bool
     */
    public function checkAffiliateTermsOptions($affiliate) {
        return $this->ci->affiliate->checkAffiliateTermsOptions($affiliate);
    }

    /**
     * get of affiliate options
     *
     * @param  int
     * @return array
     */
    public function getAffiliateTermsOptions($affiliate) {
        return $this->ci->affiliate->getAffiliateTermsOptions($affiliate);
    }

    /**
     * add earnings
     *
     * @param  array
     * @return array
     */
    public function addAffiliateEarnings($data) {
        return $this->ci->affiliate->addAffiliateEarnings($data);
    }

    /**
     * get of affiliate options
     *
     * @param  int
     * @return string
     */
    public function getCurrencyOfAffiliate($affiliate) {
        return $this->ci->affiliate->getCurrencyOfAffiliate($affiliate);
    }

    /**
     * get affiliateId by trackingCode
     *
     * @param  string
     * @return int
     */
    public function getAffiliateIdByTrackingCode($affiliate) {
        return $this->ci->affiliate->getAffiliateIdByTrackingCode($affiliate);
    }
}

/* End of file affiliate_process.php */
/* Location: ./application/libraries/affiliate_process.php */