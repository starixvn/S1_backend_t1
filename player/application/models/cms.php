<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cms
 *
 * @author	ASRII
 */

class Cms extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('date');
	}

	/**
	 * Get cms banner
	 *
	 * $bannerType int
	 * @return	$array
	 */
	public function getCmsBanner($bannerType) {
		$language = $this->session->userdata('currentLanguage');

		$this->db->select('bannerName')
					->from('cmsbanner');
		$this->db->where('cmsbanner.category', $bannerType);
		$this->db->where('cmsbanner.status', 'active');
		$this->db->where('cmsbanner.language', $language);

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row){
				$data[] = $row;
			}

			return $data;
		}
		return false;
	}


	/**
	 * Get cms footer links
	 *
	 * @return	$array
	 */
	public function getCmsFooterLinks() {
		$language = $this->session->userdata('currentLanguage');
		$language == '' ? 'en' : $language;

		$this->db->select('footercontentId,footercontentName')->from('cmsfootercontent');
		$this->db->where('cmsfootercontent.status', 'active');
		$this->db->where('cmsfootercontent.category', 'footer');
		$this->db->where('cmsfootercontent.language', $language);

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row){
				$data[] = $row;
			}

			return $data;
		}
		return false;
	}

	/**
	 * Get cms footer links
	 *
	 * $footerlinkId int
	 * @return	$array
	 */
	public function getCmsFooterContent($footerlinkId) {
		$language = $this->session->userdata('currentLanguage');

		$this->db->select('footercontentId,footercontentName,content')->from('cmsfootercontent');
		$this->db->where('cmsfootercontent.footercontentId', $footerlinkId);
		$this->db->where('cmsfootercontent.language', $language);

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row){
				$data[] = $row;
			}

			return $data;
		}
		return false;
	}

	/**
	 * Get cms footer links
	 *
	 * $footerlinkId int
	 * @return	$array
	 */
	public function getCmsFooterContentData() {
		$language = $this->session->userdata('currentLanguage');
		$fc = array('1', '2');
		$this->db->select('content')->from('cmsfootercontent');
		$this->db->where_in('cmsfootercontent.footercontentId', $fc);
		$this->db->where('cmsfootercontent.language', $language);

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row){
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * Will get all news in cmsnews table
	 *
	 * @param  int
	 * @param  int
	 * @return array
	 */
	public function getAllNews($limit, $offset = 0, $sort) {
		$this->db->select('cmsnews.*, adminusers.username, cmsnewscategory.name');
		$this->db->from('cmsnews');
		$this->db->join('adminusers', 'cmsnews.userId = adminusers.userId', 'left');
		$this->db->join('cmsnewscategory', 'cmsnews.categoryId = cmsnewscategory.id', 'left');
		if ($sort) {
			$this->db->order_by($sort);
		}
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getAllNewsCategory($limit, $offset = 0, $sort, $condition = []) {
		$this->db->select('cmsnewscategory.*, adminusers.username');
		$this->db->from('cmsnewscategory');
		$this->db->join('adminusers', 'cmsnewscategory.userId = adminusers.userId', 'left');

		if ($condition) {
			$this->db->where($condition);
		}
		if ($sort) {
			$this->db->order_by($sort);
		}
		if (!is_null($limit)) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getRegisteredContent() {
		$condition = ['status' => 1];
		$qry = $this->db->get_where('sms_registered_msg', $condition);
		$rlt = $qry->row_array();

		if ($rlt) {
			return $rlt['content'];
		} else {
			return null;
		}
	}

	public function getMetaDataInfo($uri_string) {
		$condition['uri_string'] = $uri_string;
		$qry = $this->db->get_where('metadata_setting', $condition);
		return $qry->row_array();
	}
}