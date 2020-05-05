<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * CMS
 *
 * This model represents cms. It operates the following tables:
 * - cms manager
 *
 * @author	ASRII
 *
 * General behaviors include
 * * Get cms game lists
 * * Get all menus
 * * activate/deactivate game
 * * get all news records
 * * add/update/delete news records
 * * add/delete game categories
 * * get logo/promo/banners
 * * update/search promo cms
 * * export promo cms setting
 *
 * @category CMS Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Cms extends CI_Model {
	function __construct() {
		parent::__construct();
	}

	/**
	 * date: Will select menus
	 *
	 * @return array or Boolean
	 */

	public function selectMenus() {
		$this->db->select('*')->from('cms_menus');
		$this->db->order_by('cms_menus.menus_id', 'desc');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * date: sort cmsgame
	 *
	 * @param string $sort
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function sortCMSGame($sort, $limit = null, $offset = null) {
		$this->db->from('cmsgame');
		if ($sort['activeGame']) {
			$this->db->where('status', $sort['activeGame']);
		}
		if ($sort['gameType']) {
			$this->db->where('gameType', $sort['gameType']);
		}
		if ($sort['progressiveType']) {
			$this->db->where('progressive', $sort['progressiveType']);
		}
		if ($sort['brandedGame']) {
			$this->db->where('branded', $sort['brandedGame']);
		}
		if ($sort['gameProvider']) {
			$this->db->where('gameTypeId', $sort['gameProvider']);
		}
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: Will get game list
	 *
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getAllCMSGame($limit = null, $offset = null) {
		$this->db->from('cmsgame');
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: Will activate game
	 *
	 * @param array $data
	 * @return Boolean
	 */

	public function activateGame($data) {
		$this->db->where('cmsGameId', $data['cmsGameId']);
		$this->db->update('cmsgame', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * detail: Will deactivate game
	 *
	 * @param array $data
	 * @return Boolean
	 */

	public function deactivateGame($data) {
		$this->db->where('cmsGameId', $data['cmsGameId']);
		$this->db->update('cmsgame', $data);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * detail: Will get all news category in cmsnewscatrgory table
	 *
	 * @param int $limit
	 * @param int $offset
	 * @param string $sort
	 * @return array
	 */
	public function getAllNewsCategory($limit, $offset = 0, $sort) {
		$this->db->select('cmsnewscategory.*, adminusers.username');
		$this->db->from('cmsnewscategory');
		$this->db->join('adminusers', 'cmsnewscategory.userId = adminusers.userId', 'left');

		if ($sort) {
			$this->db->order_by($sort);
		}
		if (!is_null($limit)) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: Will add news category
	 *
	 * @param array $data
	 * @return Boolean
	 */
	public function addNewsCategory($data) {
		$this->db->insert('cmsnewscategory', $data);
	}

	/**
	 * detail: Will get news category
	 *
	 * @param int $category_id
	 * @return array
	 */
	public function getNewsCategory($category_id) {
		$this->db->select('cmsnewscategory.*, adminusers.username');
		$this->db->from('cmsnewscategory');
		$this->db->join('adminusers', 'cmsnewscategory.userId = adminusers.userId', 'left');
		$this->db->where('cmsnewscategory.id', $category_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * detail: Will edit news category
	 *
	 * @param array $data
	 * @param int $category_id
	 * @return Boolean
	 */
	public function editNewsCategory($data, $category_id) {
		$this->db->where('id', $category_id);
		$this->db->update('cmsnewscategory', $data);
	}

	/**
	 * detail: Will delete news category
	 *
	 * @param int $news_id
	 * @return category_id
	 */
	public function deleteNewsCategory($category_id) {
		$this->db->where('id', $category_id);
		$this->db->delete('cmsnewscategory');
	}

	/**
	 * detail: Will get all news in cmsnews table
	 *
	 * @param int $limit
	 * @param int $offset
	 * @param string $sort
	 * @return array
	 */
	public function getAllNews($limit, $offset = 0, $sort, $condition = []) {
		$this->db->select('cmsnews.*, adminusers.username, cmsnewscategory.name');
		$this->db->from('cmsnews');
		$this->db->join('adminusers', 'cmsnews.userId = adminusers.userId', 'left');
		$this->db->join('cmsnewscategory', 'cmsnews.categoryId = cmsnewscategory.id', 'left');
		if ($condition) {
			$this->db->where($condition);
		}
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
	 * detail: Will add news
	 *
	 * @param array $data
	 * @return Boolean
	 */
	public function addNews($data) {
		$this->db->insert('cmsnews', $data);
	}

	/**
	 * detail: Will get news
	 *
	 * @param int $news_id
	 * @return array
	 */
	public function getNews($news_id) {
		$this->db->select('cmsnews.*, adminusers.username');
		$this->db->from('cmsnews');
		$this->db->join('adminusers', 'cmsnews.userId = adminusers.userId', 'left');
		$this->db->where('cmsnews.newsId', $news_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * detail: Will edit news
	 *
	 * @param array $data
	 * @param int $news_id
	 * @return Boolean
	 */
	public function editNews($data, $news_id) {
		$this->db->where('newsId', $news_id);
		$this->db->update('cmsnews', $data);
	}

	/**
	 * detail: Will delete news
	 *
	 * @param int $news_id
	 * @return Boolean
	 */
	public function deleteNews($news_id) {
		$this->db->where('newsId', $news_id);
		$this->db->delete('cmsnews');
	}

	/**
	 * detail: Will get ranking settings of player
	 *
	 * @return array
	 */
	public function getRankingSettings() {
		$this->db->select('vipsetting.*, vipsettingcashbackrule.*');
		$this->db->from('vipsetting');
		$this->db->join('vipsettingcashbackrule', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function insertcms($data) {
		$this->db->insert('cmsgamecategory', $data);
	}

	/**
	 * detiail: Will delete game category
	 *
	 * @param int $game_id
	 * @return Boolean
	 */
	public function deleteGameCategory($game_id) {
		$this->db->where('cmsGameId', $game_id);
		$this->db->delete('cmsgamecategory');
	}

	/**
	 * detail: Will add game category
	 *
	 * @param array $data
	 * @return Boolean
	 */
	public function addGameCategory($data) {
		$this->db->insert('cmsgamecategory', $data);
	}

	/**
	 * detail: Will get game category
	 *
	 * @param int $data
	 * @return array
	 */
	public function getGameCategory($game_id) {
		$this->db->from('cmsgamecategory');
		$this->db->where('cmsGameId', $game_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * detail: Will get cms promo
	 *
	 * @param int $promocmsId
	 * @return array
	 */
	public function getPromoCmsDetails($promoCmsSettingId) {
		$this->load->model('promo_type');
		$this->db->from('promocmssetting');
		$this->db->where('promoCmsSettingId', $promoCmsSettingId);
		$query = $this->db->get();
		$firstChild = 0;
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				switch ($row['promo_category']) {
				case Promo_type::PROMO_TYPE_SLOTS:
					$row['promo_type_name'] = lang("Slots");
					break;

				case Promo_type::PROMO_TYPE_LOTTERY:
					$row['promo_type_name'] = lang("Lottery");
					break;

				case Promo_type::PROMO_TYPE_SPORTS:
					$row['promo_type_name'] = lang("Sports");
					break;

				case Promo_type::PROMO_TYPE_NEW_MEMBER:
					$row['promo_type_name'] = lang("New Member");
					break;

				case Promo_type::PROMO_TYPE_LIVE_CASINO:
					$row['promo_type_name'] = lang("Live Casino");
					break;
				case Promo_type::PROMO_TYPE_OTHERS:
					$row['promo_type_name'] = lang("cms.others");
					break;
				}
				$data[] = $row;
			}
			return $data[$firstChild];
		}
		return false;
	}

	/**
	 * detail: Will get footer content
	 *
	 * @param int $footercontentId
	 * @return array
	 */
	public function getFooterContentCmsDetails($footercontentId) {
		$this->db->from('cmsfootercontent');
		$this->db->where('footercontentId', $footercontentId);
		$query = $this->db->get();
		return $query->row_array();
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
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * date: Will get cms logo
	 *
	 * @param int $cmslogoId
	 * @return array
	 */
	public function getLogoCmsDetails($cmslogoId) {
		$this->db->from('cmslogo');
		$this->db->where('cmslogoId', $cmslogoId);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * date: Will get cms promo category
	 *
	 * @param int $promoCmsSettingId
	 * @return array
	 */
	public function getPromoCmsCategory($promoCmsSettingId) {
		$this->db->select('*');
		$this->db->from('promocmscategory');
		$this->db->where('promoCmsSettingId', $promoCmsSettingId);
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: Get all cms banner
	 *
	 * @param string $sort
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getAllCMSBanner($sort, $limit = null, $offset = null) {
		$this->db->select('cmsbanner.*, admin1.username AS createdBy, admin2.username AS updatedBy');
		$this->db->from('cmsbanner');
		$this->db->join('adminusers AS admin1', 'admin1.userId = cmsbanner.createdBy', 'left');
		$this->db->join('adminusers AS admin2', 'admin2.userId = cmsbanner.updatedBy', 'left');
		$this->db->order_by($sort);
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: Get all cms footer content
	 *
	 * @param string $sort
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getAllCMSFootercontent($sort, $limit = null, $offset = null) {
		$this->db->select('cmsfootercontent.*, admin1.username AS createdBy, admin2.username AS updatedBy');
		$this->db->from('cmsfootercontent');
		$this->db->join('adminusers AS admin1', 'admin1.userId = cmsfootercontent.createdBy', 'left');
		$this->db->join('adminusers AS admin2', 'admin2.userId = cmsfootercontent.updatedBy', 'left');
		$this->db->order_by($sort);
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: Get all cms logo
	 *
	 * @param string $sort
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getAllCMSLogo($sort, $limit = null, $offset = null) {
		$this->db->select('cmslogo.*, admin1.username AS createdBy, admin2.username AS updatedBy');
		$this->db->from('cmslogo');
		$this->db->join('adminusers AS admin1', 'admin1.userId = cmslogo.createdBy', 'left');
		$this->db->join('adminusers AS admin2', 'admin2.userId = cmslogo.updatedBy', 'left');
		$this->db->order_by($sort);
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		return $query->result_array();
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
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * detail: footer content list by footercontentId from cms table
	 *
	 * @param int $footercontent_id cmsfootercontent $footercontent_id
	 * @return	array
	 */
	public function getFooterContentById($footercontent_id) {
		$this->db->from('cmsfootercontent');
		$this->db->where('footercontentId', $footercontent_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * detail: logo list by bannerId from cms table
	 *
	 * @param int $cmslogo_id cmslogo bannerId
	 * @return	array
	 */
	public function getLogoById($cmslogo_id) {
		$this->db->from('cmslogo');
		$this->db->where('bannerId', $cmslogo_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * detail: get deposit promo lists
	 *
	 * @return	array
	 */
	public function getDepositPromo() {
		$this->db->select('promorulesId, promoName');
		$this->db->from('promorules');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: edit banner by bannerId to cms banner table
	 *
	 * @param array $data array
	 * @param int $bannercmsId cmsbanner bannerId field
	 * @return Boolean
	 */
	public function editBannerCms($data, $bannercmsId) {
		$this->db->where('bannerId', $bannercmsId);
		$this->db->update('cmsbanner', $data);
	}

	/**
	 * edit footer content by footercontentId to cms footer content table
	 *
	 * @param array $data
	 * @param int $footerconententcmsId cmsfootercontent footercontentId field
	 * @return Boolean
	 */
	public function editFootercontentCms($data, $footerconententcmsId) {
		$this->db->where('footercontentId', $footerconententcmsId);
		$this->db->update('cmsfootercontent', $data);
	}

	/**
	 * detail: edit logo by logocmsId to cmslogo table
	 *
	 * @param array $data
	 * @param int $logocmsId cmslogo logocmsId field
	 * @return Boolean
	 */
	public function editLogoCms($data, $logocmsId) {
		$this->db->where('cmslogoId', $logocmsId);
		$this->db->update('cmslogo', $data);
	}

	/**
	 * detail: Get promo setting List
	 *
	 * @param string $sort
	 * @param int $limit
	 * @param int $offset
	 * @return	$array
	 */
	public function getPromoSettingList($sort, $limit = null, $offset = null) {
		$this->db->select('promocmssetting.*, admin1.username AS createdBy, admin2.username AS updatedBy, promorules.promoName AS promoRuleName, promorules.promorulesId');
		$this->db->from('promocmssetting');
		$this->db->join('adminusers AS admin1', 'admin1.userId = promocmssetting.createdBy', 'left');
		$this->db->join('adminusers AS admin2', 'admin2.userId = promocmssetting.updatedBy', 'left');
		$this->db->join('promorules', 'promorules.promorulesId = promocmssetting.promoId', 'left');
		$this->db->order_by($sort);
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		$list = $query->result_array();
		foreach ($list as &$list_item) {
			$list_item['promoCmsCatId'] = $this->getPromoCmsCategory($list_item['promoCmsSettingId']);
		}
		// var_dump($list); die();
		return $list;
	}

	/**
	 * detail: Inserts data to promo cms
	 *
	 * @param array $data
	 * @param int $promoCategory
	 * @return	boolean
	 */
	public function addNewPromo($data, $promoCategory) {
		$this->db->insert('promocmssetting', $data);

		$promoCmsSettingId = $this->db->insert_id();
		//var_dump($promoCategory);exit();
		$promoCategoryData['promoCmsSettingId'] = $promoCmsSettingId;
		$promoCategoryData['promoCmsCatId'] = 4;
		$this->setPromoCmsCategory($promoCategoryData);
		if (!empty($promoCategory)) {
			foreach ($promoCategory as $pc) {
				$promoCategoryData['promoCmsSettingId'] = $promoCmsSettingId;
				$promoCategoryData['promoCmsCatId'] = $pc;

				$this->setPromoCmsCategory($promoCategoryData);
			}
		}
	}

	/**
	 * detail: Inserts data to banner cms
	 *
	 * @param array $data
	 * @return Boolean
	 */
	public function addCmsBanner($data) {
		$this->db->insert('cmsbanner', $data);
		//var_dump($promoCategory);exit();
	}

	/**
	 * detail: Inserts data to cmsfootercontent cms
	 *
	 * @param	array $data
	 * @return	boolean
	 */
	public function addCmsFootercontent($data) {
		$this->db->insert('cmsfootercontent', $data);
		//var_dump($promoCategory);exit();
	}

	/**
	 * detail: Inserts data to logo cms
	 *
	 * @param	array $data
	 * @return	boolean
	 */
	public function addCmsLogo($data) {
		$this->db->insert('cmslogo', $data);
		//var_dump($promoCategory);exit();
	}

	/**
	 * detail: edit promo cms
	 *
	 * @param array $data
	 * @param int $promoCategory
	 * @param int $promoCmsSettingId promocmssetting field
	 * @return array
	 */
	public function editPromoCms($data, $promoCategory, $promoCmsSettingId) {
		$this->db->where('promoCmsSettingId', $promoCmsSettingId);
		$this->db->update('promocmssetting', $data);

		//delete existing promo category
		$this->deletePromoCmsCategory($promoCmsSettingId);

		$promoCategoryData['promoCmsSettingId'] = $promoCmsSettingId;
		$promoCategoryData['promoCmsCatId'] = 4;
		$this->setPromoCmsCategory($promoCategoryData);
		if (!empty($promoCategory)) {
			foreach ($promoCategory as $pc) {
				$promoCategoryData['promoCmsSettingId'] = $promoCmsSettingId;
				$promoCategoryData['promoCmsCatId'] = $pc;

				$this->setPromoCmsCategory($promoCategoryData);
			}
		}
	}

	/**
	 * detail: Will search vip group list
	 *
	 * @param string $search
	 * @param int $limit
	 * @param int $offset
	 * @return 	array
	 */
	public function searchPromoCms($search, $limit = null, $offset = null) {
		$this->db->select('promocmssetting.*, admin1.username AS createdBy, admin2.username AS updatedBy');
		$this->db->from('promocmssetting');
		$this->db->join('adminusers AS admin1', 'admin1.userId = promocmssetting.createdBy', 'left');
		$this->db->join('adminusers AS admin2', 'admin2.userId = promocmssetting.updatedBy', 'left');
		if ($search) {
			$this->db->where('promoName', $search);
		}
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: Will search banner cms list
	 *
	 * @param string $search
	 * @param int $limit
	 * @param int $offset
	 * @return 	array
	 */
	public function searchBannerCms($search, $limit = null, $offset = null) {
		$this->db->select('cmsbanner.*, admin1.username AS createdBy, admin2.username AS updatedBy');
		$this->db->from('cmsbanner');
		$this->db->join('adminusers AS admin1', 'admin1.userId = cmsbanner.createdBy', 'left');
		$this->db->join('adminusers AS admin2', 'admin2.userId = cmsbanner.updatedBy', 'left');
		if ($search) {
			$this->db->where('bannerName', $search);
		}
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * detail: Will delete cms promo category
	 *
	 * @param int $promoCmsSettingId promocmscategory field
	 * @return Boolean
	 */
	public function deletePromoCmsCategory($promoCmsSettingId) {
		$this->db->where('promoCmsSettingId', $promoCmsSettingId);
		$this->db->delete('promocmscategory');
	}

	/**
	 * detail:
	 *
	 * @param array $promoCategory
	 * @return Boolean
	 */

	public function setPromoCmsCategory($promoCategoryData) {
		//var_dump($promoCategoryData);exit();
		$this->db->insert('promocmscategory', $promoCategoryData);

		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * detail: Will get tag based on the name of the tag
	 *
	 * @param string $promo_name promocmssetting field
	 * @return array
	 */
	public function getPromoCMSName($promo_name) {
		$this->db->from('promocmssetting');
		$this->db->where('promoName', $promo_name);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * detail: Will delete cms promo
	 *
	 * @param int $promocmsId promocmssetting promocmsId field
	 * @return Boolean
	 */
	public function deletePromoCms($promocmsId) {
		// git issue #1371
		// Change hard deletion to soft deletion
		$updateset = ['deleted_flag' => 1];
		$this->db->where('promoCmsSettingId', $promocmsId);
		$this->db->update('promocmssetting', $updateset);
	}

	/**
	 * detail: delete cms promo item
	 *
	 * @param int $promocmsId promocmssetting field
	 * @return array
	 */
	public function deletePromoCmsItem($promocmsId) {
		// git issue #1371
		// Change hard deletion to soft deletion
		$updateset = ['deleted_flag' => 1];
		$this->db->where('promoCmsSettingId', $promocmsId);
		$this->db->update('promocmssetting', $updateset);
	}

	/**
	 * detail: Will delete cms banner
	 *
	 * @param int $bannerId cmsbanner field
	 * @return Boolean
	 */
	public function deleteBannerCms($bannerId) {
		$this->db->where('bannerId', $bannerId);
		$this->db->delete('cmsbanner');
	}

	/**
	 * detail: Will delete cms footercontent
	 *
	 * @param int $footercontentId cmsfootercontent fiel
	 * @return Boolean
	 */
	public function deleteFootercontentCms($footercontentId) {
		$this->db->where('footercontentId', $footercontentId);
		$this->db->delete('cmsfootercontent');
	}

	/**
	 * detail: Will delete cms logo
	 *
	 * @param int $cmslogoId cmslogo field
	 * @return Boolean
	 */
	public function deleteLogoCms($cmslogoId) {
		$this->db->where('cmslogoId', $cmslogoId);
		$this->db->delete('cmslogo');
	}

	/**
	 * detail: delete cms banner item
	 *
	 * @param int $bannerId cmsbanner field
	 * @return array
	 */
	public function deleteBannerCmsItem($bannerId) {
		$this->db->where('bannerId', $bannerId);
		$this->db->delete('cmsbanner');
	}

	/**
	 * detail: delete cms footer content item
	 *
	 * @param int $footercontentId cmsfootercontent field
	 * @return array
	 */
	public function deleteFootercontentCmsItem($footercontentId) {
		$this->db->where('footercontentId', $footercontentId);
		$this->db->delete('cmsfootercontent');
	}

	/**
	 * detail: delete cms logo item
	 *
	 * @param int $cmslogoId cmslogo field
	 * @return array
	 */
	public function deleteLogoCmsItem($cmslogoId) {
		$this->db->where('cmslogoId', $cmslogoId);
		$this->db->delete('cmslogo');
	}

	/**
	 * detail: activate promo cms
	 *
	 * @param array $data
	 * @return array
	 */
	public function activatePromoCms($data) {
		$this->db->where('promoCmsSettingId', $data['promoCmsSettingId']);
		$this->db->update('promocmssetting', $data);
	}

	/**
	 * detail: activate banner cms
	 *
	 * @param array $data cmsbanner field
	 * @return array
	 */
	public function activateBannerCms($data) {
		$this->db->where('bannerId', $data['bannerId']);
		$this->db->update('cmsbanner', $data);
	}

	/**
	 * detail: activate footercontent cms
	 *
	 * @param array $data
	 * @return array
	 */
	public function activateFootercontentCms($data) {
		$this->db->where('footercontentId', $data['footercontentId']);
		$this->db->update('cmsfootercontent', $data);
	}

	/**
	 * detail: activate logo cms
	 *
	 * @param array $data
	 * @return	array
	 */
	public function activateLogoCms($data) {
		$this->db->where('cmslogoId', $data['cmslogoId']);
		$this->db->update('cmslogo', $data);
	}

	/**
	 * detail: Get promo cms setting List
	 *
	 * @return array
	 */
	public function getPromoCmsSettingListToExport() {
		$this->db->select('promocmssetting.promoName,
						   promocmssetting.promoDescription,
						   promocmssetting.createdOn,
						   adminusers.userName as createdBy,
						   promocmssetting.updatedOn,
						   promocmssetting.promoId,
						   promocmssetting.status,
						   adminusers.userName as updatedBy
							')->from('promocmssetting')
			->join('adminusers', 'adminusers.userId = promocmssetting.createdBy');

		$query = $this->db->get();
		$cnt = 0;
		return $query;
	}

    /**
     * detail: Will get Message template
     *
     * @return array
     */
    public function getMsgTemplate() {
        $this->db->from('operator_settings');
        $this->db->where('value', 'msgTpl');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * detail: Will get specific Msg template
     *
     * @param int $msgtemplateId operator_settings id
     * @return array
     */
    public function getMsgCmsDetails($id) {
        $this->db->from('operator_settings');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * detail: Edit Email Template Detail
     *
     * @param array
     * @return Boolean
     */
    public function editMsgTempalte($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('operator_settings', $data);
    }


	public function getPromoRuleIdByCmsId($promoCmsId) {
		$this->db->select('promocmssetting.promoId');
		$this->db->from('promocmssetting');
		$this->db->where('promoCmsSettingId', $promoCmsId);
		$this->db->join('adminusers AS admin1', 'admin1.userId = promocmssetting.createdBy', 'left');
		$this->db->join('adminusers AS admin2', 'admin2.userId = promocmssetting.updatedBy', 'left');
		$this->db->join('promorules', 'promorules.promorulesId = promocmssetting.promoId', 'left');

		$query = $this->db->get();

		return $query->row()->promoId;
	}

	/**
	 * detail: Inserts data to promo cms
	 *
	 * @param array $data
	 * @param int $promoCategory
	 * @return	boolean
	 */
	public function addPromoCms($data) {
		$this->db->insert('promocmssetting', $data);
		return $this->db->insert_id() ?: false;
	}

	/**
	 * detail: edit promo cms item
	 *
	 * @param array $data
	 */
	public function editPromoCmsItem($data) {
		$this->db->where('promoCmsSettingId', $data['promoCmsSettingId']);
		$this->db->update('promocmssetting', $data);
	}

	public function getSMSActivityMsg($condition = null, $limit = null, $offset = 0, $sort = null) {

		$this->db->select('sms_activity_msg.*, adminusers.username');
		$this->db->from('sms_activity_msg');
		$this->db->join('adminusers', 'sms_activity_msg.update_user_id = adminusers.userId', 'left');

		if ($condition) {
			$this->db->where($condition);
		}
		if ($sort) {
			$this->db->order_by($sort);
		}
		if ($limit) {
			$this->db->limit($limit, $offset);
		}

		$qry = $this->db->get();
		return $qry->result_array();
	}

	public function addSMSActivityMsg($data) {
		$this->db->insert('sms_activity_msg', $data);
	}

	public function editSMSActivityMsg($id = null, $data) {
		if ($id) {
			$this->db->where('id', $id);
		}
		$this->db->update('sms_activity_msg', $data);
	}

	public function deleteSMSActivityMsg($id) {
		$this->db->where('id', $id);
		$this->db->delete('sms_activity_msg');
	}

	public function getSMSRegisteredMsg($condition = [], $limit, $offset = 0, $sort) {

		$this->db->select('sms_registered_msg.*, adminusers.username');
		$this->db->from('sms_registered_msg');
		$this->db->join('adminusers', 'sms_registered_msg.userId = adminusers.userId', 'left');

		if ($condition) {
			$this->db->where($condition);
		}
		if ($sort) {
			$this->db->order_by($sort);
		}
		if ($limit) {
			$this->db->limit($limit, $offset);
		}

		$qry = $this->db->get();
		return $qry->result_array();
	}

	public function addSMSRegisteredMsg($data) {
		$this->db->insert('sms_registered_msg', $data);
	}

	public function editSMSRegisteredMsg($id = null, $data) {
		if ($id) {
			$this->db->where('id', $id);
		}
		$this->db->update('sms_registered_msg', $data);
	}

	public function deleteSMSRegisteredMsg($id) {
		$this->db->where('id', $id);
		$this->db->delete('sms_registered_msg');
	}

}

/* End of file cms.php */
/* Location: ./application/models/cms.php */
