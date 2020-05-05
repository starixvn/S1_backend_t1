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

class Cms_model extends BaseModel {
	function __construct() {
		parent::__construct();
	}

	const SMS_MSG_REGISTERED = 1;
	const SMS_MSG_WITHDRAWAL_REQUEST = 2;
	const SMS_MSG_WITHDRAWAL_SUCCESS = 3;
	const SMS_MSG_WITHDRAWAL_DECLINE = 4;

	public $smsManagerTypeList = [
		self::SMS_MSG_REGISTERED => 'cms.registered_msg',
		self::SMS_MSG_WITHDRAWAL_REQUEST => 'cms.withdrawal_msg_request',
		self::SMS_MSG_WITHDRAWAL_SUCCESS => 'cms.withdrawal_msg_success',
		self::SMS_MSG_WITHDRAWAL_DECLINE => 'cms.withdrawal_msg_decline',
	];

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

	public function getAllNewsCategoriesSimple() {
		$this->db->select('name')
			->from('cmsnewscategory')
			->order_by('name ASC')
		;

		$rows = $this->runMultipleRowArray();
		$res = array_column($rows, 'name');

		return $res;
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
	public function getAllNews($limit, $offset = 0, $sort, $condition = [], $defaultSearch=true) {
        $nowDate = date("Y-m-d H:i:s");
		$this->db->select('cmsnews.*, adminusers.username, cmsnewscategory.name');
		$this->db->from('cmsnews');
		$this->db->join('adminusers', 'cmsnews.userId = adminusers.userId', 'left');
		$this->db->join('cmsnewscategory', 'cmsnews.categoryId = cmsnewscategory.id', 'left');

		if ($defaultSearch) {
            $defaultSearchCondition = "cmsnews.is_daterange = 0 OR ( ";
            $defaultSearchCondition .= "cmsnews.is_daterange = 1 and ";
            $defaultSearchCondition .= "cmsnews.start_date <= '$nowDate' and ";
            $defaultSearchCondition .= "cmsnews.end_date   >= '$nowDate' )";
            $this->db->where($defaultSearchCondition);
        }

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
		return $this->db->delete('cmsnews');
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
	 * Will get game category
	 *
	 * @param	$int
	 * @return	$array
	 */
	public function checkGameCategory($game_id, $level) {
		$this->getGameCategory($game_id);

		foreach ($category as $key => $value) {
			if ($value['rankingLevelSettingId'] == $level) {
				return true;
			}
		}

		return false;
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
		$this->load->model(['promo_type','promorules']);
		$this->db->from('promocmssetting');
		$this->db->where('promoCmsSettingId', $promoCmsSettingId);
		$query = $this->db->get();
		$firstChild = 0;
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['default_lang'] = $this->language_function->getCurrentLangForPromo(true,$row['default_lang']);
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
				if(!empty($row['promoId'])){
					$getPormoRuleById = $this->promorules->getPromoDetails($row['promoId']);
					$row['promoDepositType'] = $getPormoRuleById[0]['promoType'];
					if($row['promoDepositType'] == Promorules::PROMO_TYPE_NON_DEPOSIT){
						if($row['hide_on_player'] == '0' || $row['hide_on_player'] == '2'){
							$this->updatePromoCmsField($promoCmsSettingId,Promorules::SHOW_ON_PLAYER_PROMOTION);
							$row['hide_on_player'] = $this->getPromoCmsHideOnPlayerbyId($promoCmsSettingId);
						}
					}
				}

                $row['promoDetails'] = $this->decodePromoDetailItem($row['promoDetails']);
				if($this->utils->isEnabledFeature('enable_multi_lang_promo_manager')){
				    if(isset($row['promo_multi_lang']) && !empty($row['promo_multi_lang'])){
                        $tempData = array("multi_lang"=>array());
                        $this->load->library(['language_function']);
                        $systemLanguages = $this->language_function->getAllSystemLanguages();

                        $data = json_decode($row['promo_multi_lang'], true);
                        foreach ($systemLanguages as $lang){
                            $tempData['multi_lang'][$lang['short_code']]['promo_title_'.$lang['short_code']] = ($data['multi_lang'][$lang['short_code']]["promo_title_".$lang['short_code']]) ?: "";
                            $tempData['multi_lang'][$lang['short_code']]['short_desc_'.$lang['short_code']] = ($data['multi_lang'][$lang['short_code']]["short_desc_".$lang['short_code']]) ?: "";
                            $tempData['multi_lang'][$lang['short_code']]['details_'.$lang['short_code']] = $this->decodePromoDetailItem($data['multi_lang'][$lang['short_code']]["details_".$lang['short_code']]) ?: "";
                            $tempData['multi_lang'][$lang['short_code']]['banner_'.$lang['short_code']] = ($data['multi_lang'][$lang['short_code']]["banner_".$lang['short_code']]) ?: "";
                        }
                        $row['promo_multi_lang'] = json_encode($tempData);
                    }
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
		$updateset = ['deleted_flag' => 1, 'updatedOn' => $this->utils->getNowForMysql()];
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
		$updateset = ['deleted_flag' => 1, 'updatedOn' => $this->utils->getNowForMysql()];
		$this->db->where('promoCmsSettingId', $promocmsId);
		$this->db->update('promocmssetting', $updateset);
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

	/**
	 * detail: update promo cms hide_on_player
	 *
	 * @param array $data
	 */
	public function updatePromoCmsField($promoCmsSettingId, $hide_on_player) {
		$this->db->where('promoCmsSettingId', $promoCmsSettingId);
		$this->db->update('promocmssetting', array('hide_on_player' => $hide_on_player));
	}

	/**
	 * detail: get new promo cms hide_on_player
	 *
	 * @param array $data
	 */
	public function getPromoCmsHideOnPlayerbyId($promoCmsSettingId) {
		$this->db->select('promocmssetting.hide_on_player');
		$this->db->from('promocmssetting');
		$this->db->where('promoCmsSettingId', $promoCmsSettingId);
		$query = $this->db->get();

		return $query->row()->hide_on_player;
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

	public function getSMSManagerMsg($condition = [], $limit, $offset = 0, $sort) {

		$this->db->select('sms_manager_msg.*, adminusers.username');
		$this->db->from('sms_manager_msg');
		$this->db->join('adminusers', 'sms_manager_msg.userId = adminusers.userId', 'left');

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

	public function addSMSManagerMsg($data) {
		$this->db->insert('sms_manager_msg', $data);
	}

	public function editSMSManagerMsg($condition = [], $data) {
		if ($condition) {
			$this->db->where($condition);
		}
		$this->db->update('sms_manager_msg', $data);
	}

	public function deleteSMSManagerMsg($id) {
		$this->db->where('id', $id);
		$this->db->delete('sms_manager_msg');
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

	public function getManagerContent($category) {
		$condition = [
			'status' => 1,
			'category' => $category
		];

		$qry = $this->db->get_where('sms_manager_msg', $condition);
		$rlt = $qry->row_array();

		if ($rlt) {
			return $rlt['content'];
		} else {
			return null;
		}
	}

	public function getAllMetaData($limit, $offset = 0, $sort, $condition = []) {
		$this->db->select('metadata_setting.*, adminusers.username');
		$this->db->from('metadata_setting');
		$this->db->join('adminusers', 'metadata_setting.updated_by = adminusers.userId', 'left');
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

	public function getMetaDataById($id) {
		$condition['id'] = $id;
		$query = $this->db->get_where('metadata_setting', $condition);
		return $query->row_array();
	}

	public function addMetaData($data) {
		$this->db->insert('metadata_setting', $data);
	}

	public function editMetaData($condition = [], $data) {
		if ($condition) {
			$this->db->where($condition);
		}
		$this->db->update('metadata_setting', $data);
	}

	public function deleteMetaData($id) {
		$this->db->where('id', $id);
		$this->db->delete('metadata_setting');
	}

    public function delTempMultiLang($session_id){
        $this->db->where('session_id', $session_id);
        $this->db->delete('promo_multi_lang_temp');
	}

	public function getTempMultiLang($session_id){
	    $this->db->from('promo_multi_lang_temp');
	    $this->db->where('session_id', $session_id);
        return $this->runOneRowArray();
    }

    public function insertTempMultiLang($multiLangData){
	    $existTempMultiLang = $this->getTempMultiLang($multiLangData['session_id']);
	    if($existTempMultiLang){
	        $this->db->update('promo_multi_lang_temp', $multiLangData);
        }else{
            $this->db->insert('promo_multi_lang_temp', $multiLangData);
        }
    }

    public function decodePromoDetailItem($details){
	    if($this->utils->isBase64Encode($details)){
	        $details = urldecode(base64_decode($details));
        }
        return $details;
    }
}



/* End of file cms.php */
/* Location: ./application/models/cms.php */
