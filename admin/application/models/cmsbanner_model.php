<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * CMS Banner
 */
class Cmsbanner_model extends BaseModel {
    /**
     * detail: Get all cms banner
     *
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllCMSBanner() {
        $this->db->select('cmsbanner.*, admin1.username AS createdBy, admin2.username AS updatedBy');
        $this->db->from('cmsbanner');
        $this->db->join('adminusers AS admin1', 'admin1.userId = cmsbanner.createdBy', 'left');
        $this->db->join('adminusers AS admin2', 'admin2.userId = cmsbanner.updatedBy', 'left');

        return $this->runMultipleRowArray();
    }

	/**
	 * detail: Will get cms banner
	 *
	 * @param int $bannercmsId
	 * @return array
	 */
	public function getBannerCmsDetails($bannercmsId) {
		$this->db->from('cmsbanner');
		$this->db->where('bannerId', $bannercmsId);
		return $this->runOneRowArray();
	}

    /**
     * Get cms banner
     *
     * $bannerType int
     * @return	$array
     */
    public function getCmsBannerByCategory($category_id) {
        $this->db->from('cmsbanner');
        $this->db->where('cmsbanner.category', $category_id);

        return $this->runMultipleRowArray();
    }

    /**
     * Get cms banner for Player Center
     *
     * $bannerType int
     * @return	$array
     */
    public function getActiveCmsBannerByCategory($category_id) {
        $this->db->from('cmsbanner');
        $this->db->where('cmsbanner.category', $category_id);
        $this->db->where('cmsbanner.status', 'active');

        return $this->runMultipleRowArray();
    }

	/**
	 * detail: banner list by bannerId from cms table
	 *
	 * @param int $banner_id cmsbanner bannerId
	 * @return array
	 */
	public function getBannerById($banner_id) {
		$this->db->from('cmsbanner');
		$this->db->where('bannerId', $banner_id);
		return $this->runOneRowArray();
	}

	/**
	 * detail: edit banner by bannerId to cms banner table
	 *
	 * @param array $data array
	 * @param int $bannercmsId cmsbanner bannerId field
	 * @return mixed
	 */
	public function editBannerCms($data, $bannercmsId) {
        $data['updatedOn'] = $this->CI->utils->getNowForMysql();
        $data['updatedBy'] = $this->CI->authentication->getUserId();

		$this->db->where('bannerId', $bannercmsId);
		return $this->db->update('cmsbanner', $data);
	}

	/**
	 * detail: Inserts data to banner cms
	 *
	 * @param array $data
	 * @return mixed
	 */
	public function addCmsBanner($data) {
        $data['createdOn'] = $this->CI->utils->getNowForMysql();
        $data['createdBy'] = $this->CI->authentication->getUserId();

        $data['updatedOn'] = $this->CI->utils->getNowForMysql();
        $data['updatedBy'] = $this->CI->authentication->getUserId();
		return $this->db->insert('cmsbanner', $data);
	}

	/**
	 * detail: Will delete cms banner
	 *
	 * @param int $bannerId cmsbanner field
	 * @return mixed
	 */
	public function deleteBannerCms($bannerId) {
		$this->db->where('bannerId', $bannerId);
		return $this->db->delete('cmsbanner');
	}

	/**
	 * detail: activate banner cms
	 *
	 * @param array $data cmsbanner field
	 * @return mixed
	 */
	public function activateBannerCms($bannerId, $status) {
        $data = array(
            'updatedBy' => $this->authentication->getUserId(),
            'updatedOn' => date("Y-m-d H:i:s"),
            'status' => $status
        );

        $this->db->where('bannerId', $bannerId);
		return $this->db->update('cmsbanner', $data);
	}
}

/* End of file cmsbanner_model.php */
/* Location: ./application/models/cmsbanner_model.php */
