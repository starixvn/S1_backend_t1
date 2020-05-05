<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include
 * * get default company title, contact skype, contact email
 * * get extra information
 * * get all static sites
 * * delete/edit static sites
 *
 * @category CMS Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Static_site extends BaseModel {
	protected $tableName = 'static_sites';
	private $defaultSiteName = '';

	function __construct() {
		parent::__construct();
		$CI = &get_instance();
		$this->defaultSiteName = $CI->utils->getConfig('default_site_name');
	}

	public function getSiteById($id) {
		return $this->getOneRowById($id);
	}

	private $siteCacheByName = array();

	/**
	 * detail: get site by name
	 *
	 * @param string $site_name
	 * @return string
	 */
	public function getSiteByName($site_name) {
		// if (!array_key_exists($site_name, $this->siteCacheByName)) {
			// $this->siteCacheByName[$site_name] = $this->getOneRowByField('site_name', $site_name);
		// }
		// return $this->siteCacheByName[$site_name];

		// return $this->getOneRowByField('site_name', $site_name);
		$this->db->from('static_sites')->where('site_name', $site_name);
		return $this->runOneRow();
	}

	/**
	 * detail: create new site
	 *
	 * @param string $site_name
	 * @param string $site_url
	 * @param string $lang
	 * @param string $template_name
	 * @param string $template_path
	 * @param string $notes
	 *
	 * @return int
	 */
	public function createSite($site_name, $site_url, $lang, $template_name = null, $template_path = null, $notes = null) {
		$this->db->insert($this->tableName, array('site_name' => $site_name, 'site_url' => $site_url, 'lang' => $lang,
			'template_name' => $template_name, 'template_path' => $template_path, 'notes' => $notes, 'status' => self::STATUS_NORMAL));
		return $this->db->insert_id();
	}

	/**
	 * detail: get site current language
	 *
	 * @return string
	 */
	public function getCurrentLang() {
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'lang');
	}

	/**
	 * detail: get site default logo url
	 *
	 * @param string $lang
	 *
	 * @return string
	 */
	public function getDefaultLogoUrl() {
		return $this->getLogoUrl($this->defaultSiteName);
	}

	/**
	 * detail: get site default logo horizontal url
	 *
	 * @param string $lang
	 *
	 * @return string
	 */
	public function getDefaultLogoHorizontalUrl() {
		return $this->getLogoHorizontalUrl($this->defaultSiteName);
	}

	/**
	 * detail: get site default company title
	 *
	 * @param string $lang
	 *
	 * @return string
	 */
	public function getDefaultCompanyTitle($lang = '') {
		return $this->getCompanyTitle($this->defaultSiteName, $lang);
	}

	/**
	 * detail: get site default contact skype
	 *
	 * @param string $lang
	 *
	 * @return string
	 */
	public function getDefaultContactSkype($lang = '') {
		return $this->getContactSkype($this->defaultSiteName, $lang);
	}

	/**
	 * detail: get site default contact email
	 *
	 * @param string $lang
	 *
	 * @return string
	 */
	public function getDefaultContactEmail($lang = '') {
		return $this->getContactEmail($this->defaultSiteName, $lang);
	}

	/**
	 * detail: get site logo url
	 *
	 * @param string $siteName
	 *
	 * @return string
	 */
	public function getLogoUrl($siteName) {
		$site = $this->getSiteByName($siteName);
		return $site ? $site->logo_icon_filepath : '';
	}

	/**
	 * detail: get site logo horizontal URL
	 *
	 * @param string $siteName
	 *
	 * @return string
	 */
	public function getLogoHorizontalUrl($siteName) {
		$site = $this->getSiteByName($siteName);
		return $site ? $site->logo_icon_horizontal_filepath : '';
	}

	/**
	 * detail: get site company title
	 *
	 * @param string $siteName
	 * @param string $lang
	 *
	 * @return string
	 */
	public function getCompanyTitle($siteName, $lang = '') {
		$site = $this->getSiteByName($siteName);
		return $site ? $this->getByLang($site->company_title, $lang) : '';
	}

	/**
	 * detail: get site contact skype
	 *
	 * @param string $siteName
	 * @param string $lang
	 *
	 * @return string
	 */
	public function getContactSkype($siteName, $lang = '') {
		$site = $this->getSiteByName($siteName);
		return $site ? $this->getByLang($site->contact_skype, $lang) : '';
	}

	/**
	 * detail: get site contact email
	 *
	 * @param string $siteName
	 * @param string $lang
	 *
	 * @return string
	 */
	public function getContactEmail($siteName, $lang = '') {
		$site = $this->getSiteByName($siteName);
		return $site ? $this->getByLang($site->contact_email, $lang) : '';
	}

	/**
	 * detail: get site extra information
	 *
	 * @param string $siteName
	 * @param string $field
	 *
	 * @return string
	 */
	public function getExtraInfo($siteName, $field) {
		$site = $this->getSiteByName($siteName);
		if (!$site) {
			return '';
		}
		$extraInfo = json_decode($site->extra_info, true);
		if (!$extraInfo || !array_key_exists($field, $extraInfo)) {
			return '';
		}
		return $extraInfo[$field];
	}

	private function getByLang($inputString, $lang) {
		if (!$this->isJson($inputString)) {
			return $inputString;
		}
		$jsonArray = json_decode($inputString, true);
		if (isset($jsonArray[$lang])) {
			return $jsonArray[$lang];
		} elseif (isset($jsonArray['english'])) {
			return $jsonArray['english'];
		} else {
			return $jsonArray;
		}
	}

	/**
	 * detail: validate if string is json
	 *
	 * @param string $string
	 */
	private function isJson($string) {
		$string = trim($string);
		if (strpos($string, '{') != 0 && strpos($string, '[') != 0) {
			return false;
		}
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	/**
	 * detail: Get All Static sites
	 *
	 * @param int $limit
	 * @param int $offset
	 * @param string $sort
	 *
	 * @return array
	 */

	public function getAllStaticSites($limit = null, $offset = null, $sort) {
		$this->db->select('*');
		$this->db->from('static_sites');
		if ($sort) {
			$this->db->order_by($sort);
		}
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: add static sites data
	 *
	 * @param array $data
	 * @return Boolean
	 */
	public function addStaticSites($data){
		$this->db->insert('static_sites', $data);
	}

	/**
	 * detail: delete static sites
	 *
	 * @param int $id static sites id
	 * @return Boolean
	 */
	public function deleteStaticSite($id){
		$this->db->where('id', $id);
		$this->db->delete('static_sites');
	}

	/**
	 * detail: update static sites
	 *
	 * @param array $data
	 * @param int $id static sites id
	 * @return Boolean
	 */
	public function editStaticSite($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('static_sites', $data);
	}

	/**
	 * detail: update static sites by site name
	 *
	 * @param array $data
	 * @param int $siteName (default/staging)
	 * @return Boolean
	 */
	public function editStaticSiteBySiteName($data, $siteName) {
		$this->db->where('site_name', $siteName);
		return $this->db->update('static_sites', $data);
	}

	/**
	 * detail: delete logo icon path
	 *
	 * @param array $data
	 * @param int $id static sites id
	 * @return Boolean
	 */
	public function deleteStaticSiteLogoIconPath($data,$id) {
		$this->db->where('id', $id);
		$this->db->update('static_sites', $data);
	}

}

/////end of file///////
