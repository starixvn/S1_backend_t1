<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * CMS Manager
 *
 * CMS Manager library for FHI OG System
 *
 * @package		CMS Manager
 * @author		ASRII
 * @version		1.0.0
 */

class Cms_manager {
	private $error = array();

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array(''));
		$this->ci->load->model(array('cms'));
	}

	/**
	 * Will select menus
	 *
	 * @return	$array
	 */
	public function selectMenus() {
		return $this->ci->cms->selectMenus();
	}

	/**
	 * Will get promo list
	 *
	 * @return	$array
	 */
	public function getAllPromo($limit, $offset) {
		return $this->ci->cms->getAllPromo($limit, $offset);
	}

	/**
	 * Will get all activated promo list
	 *
	 * @return	$array
	 */
	public function getAllActivatedPromo($limit, $offset) {
		return $this->ci->cms->getAllActivatedPromo($limit, $offset);
	}

	/**
	 * Will get all activated game list
	 *
	 * @return	$array
	 */
	public function getAllActivatedGame() {
		return $this->ci->cms->getAllActivatedGame();
	}

	/**
	 * Will get all deactivated game list
	 *
	 * @return	$array
	 */
	public function sortCMSGame($sort, $limit, $offset) {
		return $this->ci->cms->sortCMSGame($sort, $limit, $offset);
	}

	/**
	 * Will get all deactivated game list
	 *
	 * @param	$int
	 * @param	$int
	 * @return	$array
	 */
	public function getAllCMSGame($limit = null, $offset = null) {
		return $this->ci->cms->getAllCMSGame($limit, $offset);
	}

	/**
	 * Will get all activated promo list
	 *
	 * @return	$array
	 */
	public function deactivatePromo($promoId) {
		return $this->ci->cms->deactivatePromo($promoId);
	}

	/**
	 * Will activated promo item
	 *
	 * @return	$array
	 */
	public function activatePromo($data) {
		return $this->ci->cms->activatePromo($data);
	}

	/**
	 * Will activated game item
	 *
	 * @return	$array
	 */
	public function activateGame($data) {
		return $this->ci->cms->activateGame($data);
	}

	/**
	 * Will deactivated game item
	 *
	 * @return	$array
	 */
	public function deactivateGame($data) {
		return $this->ci->cms->deactivateGame($data);
	}

	/**
	 * Will get all news category
	 *
	 * @param	$int
	 * @param	$int
	 * @return	$array
	 */
	public function getAllNewsCategory($limit = null, $offset = null, $sort) {
		return $this->ci->cms->getAllNewsCategory($limit, $offset, $sort);
	}

	/**
	 * Will get news Category
	 *
	 * @param	$int
	 * @return	$array
	 */
	public function getNewsCategory($category_id) {
		return $this->ci->cms->getNewsCategory($category_id);
	}

	/**
	 * Will add news category
	 *
	 * @param	$array
	 * @return	$void
	 */
	public function addNewsCategory($data) {
		$this->ci->cms->addNewsCategory($data);
	}

	/**
	 * Will edit news Category
	 *
	 * @param	$array
	 * @param	$int
	 * @return	$void
	 */
	public function editNewsCategory($data, $category_id) {
		$this->ci->cms->editNewsCategory($data, $category_id);
	}

	/**
	 * Will delete news Category
	 *
	 * @param	$int
	 * @return	$void
	 */
	public function deleteNewsCategory($category_id) {
		$this->ci->cms->deleteNewsCategory($category_id);
	}

	/**
	 * Will get all news
	 *
	 * @param	$int
	 * @param	$int
	 * @return	$array
	 */
	public function getAllNews($limit = null, $offset = null, $sort, $condition = []) {
		return $this->ci->cms->getAllNews($limit, $offset, $sort, $condition);
	}

	/**
	 * Will add news
	 *
	 * @param	$array
	 * @return	$void
	 */
	public function addNews($data) {
		$this->ci->cms->addNews($data);
	}

	/**
	 * Will get news
	 *
	 * @param	$int
	 * @return	$array
	 */
	public function getNews($news_id) {
		return $this->ci->cms->getNews($news_id);
	}

	/**
	 * Will edit news
	 *
	 * @param	$array
	 * @param	$int
	 * @return	$void
	 */
	public function editNews($data, $news_id) {
		$this->ci->cms->editNews($data, $news_id);
	}

	/**
	 * Will delete news
	 *
	 * @param	$int
	 * @return	$void
	 */
	public function deleteNews($news_id) {
		$this->ci->cms->deleteNews($news_id);
	}

	/**
	 * Will delete promo category
	 *
	 * @param	$int
	 * @return	$void
	 */
	public function deletePromoCategory($promo_id) {
		$this->ci->cms->deletePromoCategory($promo_id);
	}

	/**
	 * Will delete promo cms
	 *
	 * @param	$int
	 * @return	$void
	 */
	public function deletePromoCms($promocmsId) {
		$this->ci->cms->deletePromoCms($promocmsId);
	}

	/**
	 * Will delete promo item
	 *
	 * @return	$array
	 */
	public function deletePromoCmsItem($promocmsId) {
		return $this->ci->cms->deletePromoCmsItem($promocmsId);
	}

	/**
	 * Will delete bannercms
	 *
	 * @param	$int
	 * @return	$void
	 */
	public function deleteBannerCms($bannercmsId) {
		$this->ci->cms->deleteBannerCms($bannercmsId);
	}

	/**
	 * Will delete footer content cms
	 *
	 * @param	$int
	 * @return	$void
	 */
	public function deleteFootercontentCms($footercontentcmsId) {
		$this->ci->cms->deleteFootercontentCms($footercontentcmsId);
	}

	/**
	 * Will delete logocms
	 *
	 * @param	$int
	 * @return	$void
	 */
	public function deleteLogoCms($logocmsId) {
		$this->ci->cms->deleteLogoCms($logocmsId);
	}

	/**
	 * Will delete bannercms item
	 *
	 * @return	$array
	 */
	public function deleteBannerCmsItem($bannercmsId) {
		return $this->ci->cms->deleteBannerCmsItem($bannercmsId);
	}

	/**
	 * Will delete footer content item
	 *
	 * @return	$array
	 */
	public function deleteFootercontentCmsItem($footercontentcmsId) {
		return $this->ci->cms->deleteFootercontentCmsItem($footercontentcmsId);
	}

	/**
	 * Will delete logocms item
	 *
	 * @return	$array
	 */
	public function deleteLogoCmsItem($logocmsId) {
		return $this->ci->cms->deleteLogoCmsItem($logocmsId);
	}

	/**
	 * Will activate promo cms
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function activatePromoCms($data) {
		return $this->ci->cms->activatePromoCms($data);
	}

	/**
	 * Will activate banner cms
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function activateBannerCms($data) {
		return $this->ci->cms->activateBannerCms($data);
	}

	/**
	 * Will activate footer content cms
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function activateFootercontentCms($data) {
		return $this->ci->cms->activateFootercontentCms($data);
	}

	/**
	 * Will activate logo cms
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function activateLogoCms($data) {
		return $this->ci->cms->activateLogoCms($data);
	}

	/**
	 * Will add promo category
	 *
	 * @param	$array
	 * @return	$void
	 */
	public function addPromoCategory($data) {
		$this->ci->cms->addPromoCategory($data);
	}

	/**
	 * Will add new promo
	 *
	 * @param	$array
	 * @return	$void
	 */
	public function addNewPromo($data, $promoCategory) {
		$this->ci->cms->addNewPromo($data, $promoCategory);
	}

	/**
	 * Will add cms banner
	 *
	 * @param	$array
	 * @return	$void
	 */
	public function addCmsBanner($data) {
		$this->ci->cms->addCmsBanner($data);
	}

	/**
	 * Will add cms footer content
	 *
	 * @param	$array
	 * @return	$void
	 */
	public function addCmsFootercontent($data) {
		$this->ci->cms->addCmsFootercontent($data);
	}

	/**
	 * Will add cms logo
	 *
	 * @param	$array
	 * @return	$void
	 */
	public function addCmsLogo($data) {
		$this->ci->cms->addCmsLogo($data);
	}

	/**
	 * Will get cms promo name
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getPromoCMSName($group_name) {
		return $this->ci->cms->getPromoCMSName($group_name);
	}

	/**
	 * Will get promo category
	 *
	 * @param	$int
	 * @return	$array
	 */
	public function getPromoCategory($promo_id) {
		return $this->ci->cms->getPromoCategory($promo_id);
	}

	/**
	 * Will get ranking settings of player
	 *
	 * @return	$array
	 */
	public function getRankingSettings() {
		return $this->ci->cms->getRankingSettings();
	}

	public function insertcms($data) {
		$this->ci->cms->insertcms($data);
	}

	/**
	 * Will delete game category
	 *
	 * @param	$int
	 * @return	$void
	 */
	public function deleteGameCategory($game_id) {
		$this->ci->cms->deleteGameCategory($game_id);
	}

	/**
	 * Will add game category
	 *
	 * @param	$array
	 * @return	$void
	 */
	public function addGameCategory($data) {
		$this->ci->cms->addGameCategory($data);
	}

	/**
	 * Will get game category
	 *
	 * @param	$int
	 * @return	$array
	 */
	public function checkGameCategory($game_id, $level) {
		$category = $this->ci->cms->getGameCategory($game_id);

		foreach ($category as $key => $value) {
			if ($value['rankingLevelSettingId'] == $level) {
				return true;
			}
		}

		return false;
	}

	/**
	 * get all banner from cms banner table
	 *
	 * @return	array
	 */
	public function getAllCMSBanner($sort, $limit, $offset) {
		return $this->ci->cms->getAllCMSBanner($sort, $limit, $offset);
	}

	/**
	 * get all footer content from cmsfootercontent table
	 *
	 * @return	array
	 */
	public function getAllCMSFootercontent($sort, $limit, $offset) {
		return $this->ci->cms->getAllCMSFootercontent($sort, $limit, $offset);
	}

	/**
	 * get all logo from cms logo table
	 *
	 * @return	array
	 */
	public function getAllCMSLogo($sort, $limit, $offset) {
		return $this->ci->cms->getAllCMSLogo($sort, $limit, $offset);
	}

	/**
	 * return banner list by bannerId from cms banner table
	 *
	 * @return	array
	 */
	public function getBannerById($banner_id) {
		return $this->ci->cms->getBannerById($banner_id);
	}

	/**
	 * return footercontent list by bannerId from cms banner table
	 *
	 * @return	array
	 */
	public function getFootercontentById($footercontent_id) {
		return $this->ci->cms->getFootercontentById($footercontent_id);
	}

	/**
	 * return logo list by logoId from cms logo table
	 *
	 * @return	array
	 */
	public function getLogoById($logo_id) {
		return $this->ci->cms->getLogoById($logo_id);
	}

	/**
	 * edit cms banner by bannerId to cms banner table
	 *
	 * @param	array
	 * @param	int
	 */
	public function editCMSBanner($data) {
		$this->ci->cms->editCMSBanner($data);
	}

	/**
	 * edit cms footercontent by footercontentId to cms footercontent table
	 *
	 * @param	array
	 * @param	int
	 */
	public function editCMSFootercontent($data) {
		$this->ci->cms->editCMSFootercontent($data);
	}

	/**
	 * edit cms logo by logoId to cms logo table
	 *
	 * @param	array
	 * @param	int
	 */
	public function editCMSLogo($data) {
		$this->ci->cms->editCMSLogo($data);
	}

	/**
	 * Edit cms promo
	 *
	 * @return	$array
	 */
	public function editPromoCms($data, $editPromoCms, $promocmsId) {
		return $this->ci->cms->editPromoCms($data, $editPromoCms, $promocmsId);
	}

	/**
	 * Edit cms banner
	 *
	 * @return	$array
	 */
	public function editBannerCms($data, $bannercmsId) {
		return $this->ci->cms->editBannerCms($data, $bannercmsId);
	}

	/**
	 * Edit cms footercontent
	 *
	 * @return	$array
	 */
	public function editFootercontentCms($data, $footercontentId) {
		return $this->ci->cms->editFootercontentCms($data, $footercontentId);
	}

	/**
	 * Edit cms logo
	 *
	 * @return	$array
	 */
	public function editLogoCms($data, $logocmsId) {
		return $this->ci->cms->editLogoCms($data, $logocmsId);
	}

	/**
	 * get promo cms
	 *
	 * @param	array
	 * @param	int
	 */
	public function getPromoCmsDetails($promocmsId) {
		return $this->ci->cms->getPromoCmsDetails($promocmsId);
	}

	/**
	 * get banner cms
	 *
	 * @param	array
	 * @param	int
	 */
	public function getBannerCmsDetails($bannercmsId) {
		return $this->ci->cms->getBannerCmsDetails($bannercmsId);
	}

	/**
	 * get footercontent cms
	 *
	 * @param	array
	 * @param	int
	 */
	public function getFooterContentCmsDetails($footercontentcmsId) {
		return $this->ci->cms->getFooterContentCmsDetails($footercontentcmsId);
	}

	/**
	 * get logo cms
	 *
	 * @param	array
	 * @param	int
	 */
	public function getLogoCmsDetails($logocmsId) {
		return $this->ci->cms->getLogoCmsDetails($logocmsId);
	}

	/**
	 * get all vip settings
	 *
	 * @return 	array
	 */
	public function getPromoSettingList($sort, $limit, $offset) {
		return $this->ci->cms->getPromoSettingList($sort, $limit, $offset);
	}

	/**
	 * get deposit promo
	 *
	 * @param	array
	 * @param	int
	 */
	public function getDepositPromo() {
		return $this->ci->cms->getDepositPromo();
	}

	/**
	 * search promo settings
	 *
	 * @return 	array
	 */
	public function searchPromoCms($search, $limit, $offset) {
		return $this->ci->cms->searchPromoCms($search, $limit, $offset);
	}

	/**
	 * search banner cms
	 *
	 * @return 	array
	 */
	public function searchBannerCms($search, $limit, $offset) {
		return $this->ci->cms->searchBannerCms($search, $limit, $offset);
	}

	/**
	 * Will randomize alphanumeric and special characters
	 *
	 * @param   string
	 * @return  string
	 */
	public function generateRandomCode() {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$generatePromoCode = '';
		foreach (array_rand($seed, 7) as $k) {
			$generatePromoCode .= $seed[$k];
		}

		return $generatePromoCode;
	}

	/**
	 * get all cms promo settings
	 *
	 * @return 	array
	 */
	public function getPromoCmsSettingListToExport() {
		return $this->ci->cms->getPromoCmsSettingListToExport();
	}

    /**
     * get all Message template
     *
     * @return 	array
     */
    public function getMsgTemplate() {
        return $this->ci->cms->getMsgTemplate();
    }

    /**
     * get msg template
     *
     * @param	int
     * @return	array
     */
    public function getMsgCmsDetails($id) {
        return $this->ci->cms->getMsgCmsDetails($id);
    }

    /**
     * Edit Message Template
     *
     * @param	array
     * @return	$array
     */
    public function editMsgTempalte($data) {
        return $this->ci->cms->editMsgTempalte($data);
    }

	/**
	 * Get SMS Activity Msg
	 *
	 * @param boolean $currentMsg
	 * @return 	array
	 */
	public function getSMSActivityMsg($condition = null, $limit = null, $offset = 0, $sort = null) {
		return $this->ci->cms->getSMSActivityMsg($condition, $limit, $offset, $sort);
	}

	/**
	 * Add Activity Msg
	 *
     * @param arrayy $data
	 */
	public function addSMSActivityMsg($data) {
		$this->ci->cms->addSMSActivityMsg($data);
	}

	/**
	 * Edit Activity Msg
	 *
	 * @param array $id
	 * @param array $data
	 */
	public function editSMSActivityMsg($id = null, $data) {
		$this->ci->cms->editSMSActivityMsg($id, $data);
	}

	/**
	 * Delete Activity Msg
	 */
	public function deleteSMSActivityMsg($id) {
		$this->ci->cms->deleteSMSActivityMsg($id);
	}

	/**
	 * Get SMS registered Msg
	 *
	 * @param boolean $currentMsg
	 * @return 	array
	 */
	public function getSMSRegisteredMsg($condition = [], $limit, $offset = 0, $sort) {
		return $this->ci->cms->getSMSRegisteredMsg($condition, $limit, $offset, $sort);
	}

	/**
	 * Add registered Msg
	 *
     * @param arrayy $data
	 */
	public function addSMSRegisteredMsg($data) {
		$this->ci->cms->addSMSRegisteredMsg($data);
	}

	/**
	 * Edit registered Msg
	 *
	 * @param array $id
	 * @param array $data
	 */
	public function editSMSRegisteredMsg($id = null, $data) {
		$this->ci->cms->editSMSRegisteredMsg($id, $data);
	}

	/**
	 * Delete registered Msg
	 */
	public function deleteSMSRegisteredMsg($id) {
		$this->ci->cms->deleteSMSRegisteredMsg($id);
	}
}

/* End of file payment_manager.php */
/* Location: ./application/libraries/payment_manager.php */
