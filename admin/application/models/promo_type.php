<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Depositpromo
 *
 * This model represents promo type.
 *
 */

class Promo_type extends BaseModel {

	protected $tableName = 'promotype';

	const PROMO_TYPE_SLOTS = 1;
	const PROMO_TYPE_LOTTERY = 2;
	const PROMO_TYPE_SPORTS = 3;
	const PROMO_TYPE_NEW_MEMBER = 4;
	const PROMO_TYPE_LIVE_CASINO = 5;
	const PROMO_TYPE_OTHERS = 6;
	const PROMO_TYPE_DESC = array(
		self::PROMO_TYPE_SLOTS => "Slots",
		self::PROMO_TYPE_LOTTERY => "Lottery",
		self::PROMO_TYPE_SPORTS => "Sports",
		self::PROMO_TYPE_NEW_MEMBER => "New Member",
		self::PROMO_TYPE_LIVE_CASINO => "Live Casino",
		self::PROMO_TYPE_OTHERS => "Others",
	);

	function __construct() {
        $this->load->library(['depositpromo_manager']);
		parent::__construct();
	}

	/**
	 * add promo type
	 *
	 * @return	$array
	 */
	public function addPromoType($data) {
		$this->db->insert($this->tableName, $data);

		//checker
		if ($this->db->affected_rows()) {
			return $this->db->insert_id();
		}

		return FALSE;
	}

    public function getNextOrder() {
        $lastOrder = $this->getLastOrder();
        return $lastOrder += 10;
    }

    /**
     * detail: get the last order in the lists
     *
     * @return int
     */
    public function getLastOrder() {
        $this->db->select('promotypeOrder')
                 ->from($this->tableName)
                 ->order_by('promotypeOrder', 'desc')
                 ->limit(1);

        $qry = $this->db->get();
        $ord = $this->getOneRowOneField($qry, 'promotypeOrder');
        if ($ord) {
            return intval($ord);
        }
        return self::DEFAULT_START_ORDER;
    }


	/**
	 * overview : get promo type
	 * @return bool
	 */
	public function getPromoType($promoTypeId = null) {
		$this->db->select('promotype.promoTypeId,
						   promotype.promoTypeName,
						   promotype.promoTypeDesc,
						   promotype.use_in_player_center,
						   admusr1.username as createdBy,
						   admusr2.username as updatedBy,
						   promotype.createOn,
						   promotype.updatedOn
						')
			->from('promotype');
		$this->db->where('promorulesallowedplayerlevel.promoruleId', $depositPromoId);
		$this->db->join('adminusers as admusr1', 'admusr1.userId = promotype.createdBy', 'left');
		$this->db->join('adminusers as admusr2', 'admusr2.userId = promotype.updatedBy', 'left');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getPromoTypeAllowedToPromoManager() {
        $this->load->model('promorules');
		$this->db->select('promoTypeId as id, promoTypeName as name')
		  ->from($this->tableName)->where('promoTypeName != ', Promorules::SYSTEM_MANUAL_PROMO_TYPE_NAME)
		  ->where('deleted', 0);
		$query = $this->db->get();
		return $query->result_array();
	}

    public function uploadPromoCategoryIcon($file) {
        $image = isset($file) ? $file : null;

        # Player does not select icon to upload
        if(empty($image['name'][0])) {
            return array('status' => 'success');
        }

        $fullpath = $this->utils->getPromoCategoryIconPath();

        $config = array(
            'allowed_types' => "jpg|jpeg|png|svg",
            'max_size'      => $this->utils->getMaxUploadSizeByte(),
            'overwrite'     => true,
            'remove_spaces' => true,
            'upload_path'   => $fullpath,
        );

        if (!empty($image)) {
            $this->load->library('multiple_image_uploader');
            $response = $this->multiple_image_uploader->do_multiple_uploads($image, $fullpath, $config);


            if (strtolower($response['status']) == "success") {
                return array('status' => 'success', 'fileName' => $response['filename'][0]);
            }

            if(strtolower($response['status']) == "fail"){
                return array('status' => 'error', 'msg' => $response['message']);
            }
        }

        return false;
    }

    public function removePromoCategoryIcon($promoTypeId) {
        $response = [];

        $promoTypeDetail = $this->depositpromo_manager->getPromoTypeDetails($promoTypeId);
        if(empty($promoTypeDetail)){
            $response = [
                'status' => false,
                'message' => lang('cms.promoTypeEmpty'),
            ];
            $this->utils->debug_log('Promo category not exist',$response);
            return $response;
        }

        $promoIcon = $promoTypeDetail[0]['promoIcon'];
        if(!isset($promoIcon)){
            $response = [
                'status' => false,
                'message' => lang('cms.promoTypeIconEmpty'),
            ];
            $this->utils->debug_log('Promo category Icon not exist',$response);
            return $response;
        }

        $file_path = $this->utils->getPromoCategoryIconPath($promoIcon);
        if(!file_exists($file_path)){
            $response = [
                'status' => false,
                'message' => lang('cms.promoTypeIconEmpty'),
            ];
            $this->CI->utils->debug_log('Promo category icon file not exist',$response);
            return $response;
        }

        $deleted_file = unlink($file_path);
        if(!$deleted_file){
            $response = [
                'status' => false,
                'message' => lang('cms.promoTypeDelFailed'),
            ];
            $this->CI->utils->debug_log('Remove promo category icon file failed',$response);
            return $response;
        }

        $promotypedata = [
            'promoTypeId' => $promoTypeId,
            'promoIcon' => NULL,
        ];
        $this->depositpromo_manager->editPromoType($promotypedata);

        $response = array(
            'status' => true,
            'message' => lang('cms.promoTypeDelSuccess'),
        );

        return $response;
    }

}

/* End of file depositpromo.php */
/* Location: ./application/models/promo_type.php */
