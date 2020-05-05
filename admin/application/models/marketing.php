<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Marketing
 *
 * This model represents marketing data. It operates the following tables:
 * - registration_fields
 *
 * @author  Johann Merle
 */

class Marketing extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    const TYPE_PLAYER_REGISTRATION=1;
    const TYPE_AFFILIATE_REGISTRATION=2;

    # This key is the same as that defined in class Registration_setting
    private function getCacheKey($key) {
        return PRODUCTION_VERSION.'|registration_fields|'.$key;
    }

    /**
     * get registration fields
     *
     * @param string
     * @return array
     */
    public function getRegisteredFields($type) {
        $qry = "SELECT * FROM registration_fields
            WHERE type = $type  ORDER BY field_order" ;
        $query = $this->db->query("$qry");
        return $query->result_array();
    }

    /**
     * save registration settings
     *
     * @param string
     * @return array
     */
    public function saveRegistrationSettings($data, $id) {
        $this->utils->deleteCache($this->getCacheKey(1 /*self::PLAYER*/));
        $this->utils->deleteCache($this->getCacheKey(2 /*self::AFFILIATE*/));

        $this->db->where('registrationFieldId', $id);
        return $this->db->update('registration_fields', $data);
    }

    /**
     * get registration fields
     *
     * @param string
     * @return array
     */
    public function getPromoRulesId($playerpromoId) {
        $qry = "SELECT promorulesId FROM playerpromo
            WHERE playerpromoId = '" . $playerpromoId . "'";
        $query = $this->db->query("$qry");
        return $query->row_array();
    }
}

/* End of file marketing.php */
/* Location: ./application/models/marketing.php */
