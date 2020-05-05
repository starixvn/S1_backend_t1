<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Marketing Manager
 *
 * Marketing Manager library for FHI OG System
 *
 * @package		Marketing Manager
 * @author		Johann Merle
 * @version		1.0.0
 */

class Marketing_manager
{
	private $error = array();

	function __construct() {
		$this->ci =& get_instance();
		$this->ci->load->library(array(''));
		$this->ci->load->model(array('marketing','depositpromo'));
	}

	/**
	 * get registration fields
	 *
	 * @param string
	 * @return array
	 */
	public function getRegisteredFields($type) {
		return $this->ci->marketing->getRegisteredFields($type);
	}

	/**
	 * save registration settings
	 *
	 * @param string
	 * @return array
	 */
	public function saveRegistrationSettings($data, $id) {
		return $this->ci->marketing->saveRegistrationSettings($data, $id);
	}

    /**
    * check registration fields if visible
    *
    * @param  type
    * @return array
    */
    public function checkRegisteredFieldsIfVisible($field_name, $type) {
        $registered_fields = $this->getRegisteredFields($type);

        foreach ($registered_fields as $key => $value) {
            if($value['field_name'] == $field_name) {
                return $value['visible'];
            }
        }
    }

    /**
    * check registration fields if required
    *
    * @param  type
    * @return array
    */
    public function checkRegisteredFieldsIfRequired($field_name, $type) {
        $registered_fields = $this->getRegisteredFields($type);

        foreach ($registered_fields as $key => $value) {
            if($value['field_name'] == $field_name) {
                return $value['required'];
            }
        }
    }

    /**
     * Will get withdrawal condition amount
     *
     * @param   playerId int
     * @param   gameType int
     * @return  array
     */
    public function getConditionAmount($playerpromoId) {
        $data = $this->ci->marketing->getPromoRulesId($playerpromoId);
        return $this->ci->depositpromo->viewPromoRuleDetails($data['promorulesId']);
    }

}

/* End of file marketing_manager.php */
/* Location: ./application/libraries/marketing_manager.php */