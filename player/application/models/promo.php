<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Promo
 *
 * This model represents promo data. It operates the following tables:
 * - player
 * - playerdetails
 * MOVED to promorules.php
 *
 * @author ASRII
 */

class Promo extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->helper('date');
	}

	/**
	 * Get Promo List
	 *
	 * @param	$int
	 * @param	$int
	 * @return	$array
	 */
	public function getAllPromo($limit, $offset) {
		$this->load->library(array('language_function'));
		$language = $this->language_function->getCurrentLangForPromo();
		$this->db->select(array(
			'promoCmsSettingId',
			'promoName',
			'promoDescription',
			'promoDetails',
			'promoThumbnail',
			'promoId',
			'status',
		));
		$this->db->from('promocmssetting');
		$this->db->where('status', 'active');
		$this->db->where('language', $language);
		$this->db->order_by('promoCmsSettingId', 'DESC');
		if ($limit != null) {
			if ($offset != null && $offset != 'undefined') {
				$this->db->limit($limit, $offset);
			} else {
				$this->db->limit($limit);
			}
		}
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}

	/**
	 * Will get featured promo
	 *
	 * @param   $int
	 * @param   $int
	 * @return  array
	 */
	public function getAllFeaturedPromo($limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$this->load->library(array('language_function'));
		$language = $this->language_function->getCurrentLangForPromo();
		// $language = $this->session->userdata('currentLanguage');
		// if ($language == '' || !$language) {
		// 	$language = 'ch';
		// }
		$query = $this->db->query("SELECT promocmssetting.*
			FROM promocmscategory
			LEFT JOIN promocmssetting
		 	ON promocmssetting.promoCmsSettingId = promocmscategory.promoCmsSettingId
			WHERE promocmssetting.status = 'active'
			AND promocmssetting.language = ?
			AND promocmscategory.promoCmsCatId = 1
			ORDER BY promocmssetting.createdOn DESC
			$limit
			$offset
		", array($language));
		if ($query->num_rows() > 0) {
			// foreach ($query->result_array() as $row) {
			// 	$data[] = $row;
			// }
			return $query->result_array();
		}
		return false;
	}

	/**
	 * Will get new promo
	 *
	 * @param   $int
	 * @param   $int
	 * @return  array
	 */
	public function getAllNewPromo($limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// $query = $this->db->query("SELECT mkt_promo.promoName,mkt_promo.promoCode,mkt_promodescription.promoHtmlDescription
		// 	FROM mkt_promocategory
		// 	LEFT JOIN cmspromo
		// 	ON cmspromo.promoId = mkt_promocategory.promoId
		// 	LEFT JOIN mkt_promo
		// 	ON mkt_promocategory.promoId = mkt_promo.promoId
		// 	LEFT JOIN mkt_promodescription
		// 	ON mkt_promodescription.promoId = mkt_promo.promoId
		// 	WHERE cmspromo.status = 'active'
		// 	AND mkt_promocategory.category = 'new'
		// 	ORDER BY mkt_promo.promoId DESC
		// 	$limit
		// 	$offset
		// ");
		// $language = "'" . $this->session->userdata('currentLanguage') . "'";
		$this->load->library(array('language_function'));
		$language = $this->language_function->getCurrentLangForPromo();
		$query = $this->db->query("SELECT promocmssetting.*
			FROM promocmscategory
			LEFT JOIN promocmssetting
		 	ON promocmssetting.promoCmsSettingId = promocmscategory.promoCmsSettingId
			WHERE promocmssetting.status = 'active'
			AND promocmssetting.language = ?
			AND promocmscategory.promoCmsCatId = 2
			ORDER BY promocmssetting.createdOn DESC
			$limit
			$offset
		", array($language));

		if ($query->num_rows() > 0) {
			// foreach ($query->result_array() as $row) {
			// 	$data[] = $row;
			// }
			// //var_dump($data);exit();
			// return $data;
			return $query->result_array();
		}
		return false;
	}

	/**
	 * Will get players promo
	 *
	 * @param   $int
	 * @param   $int
	 * @return  array
	 */
	public function getAllPlayersPromo($limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// $query = $this->db->query("SELECT mkt_promo.promoName,mkt_promo.promoCode,mkt_promodescription.promoHtmlDescription
		// 	FROM mkt_promocategory
		// 	LEFT JOIN cmspromo
		// 	ON cmspromo.promoId = mkt_promocategory.promoId
		// 	LEFT JOIN mkt_promo
		// 	ON mkt_promocategory.promoId = mkt_promo.promoId
		// 	LEFT JOIN mkt_promodescription
		// 	ON mkt_promodescription.promoId = mkt_promo.promoId
		// 	WHERE cmspromo.status = 'active'
		// 	AND mkt_promocategory.category = 'all'
		// 	ORDER BY mkt_promo.promoId DESC
		// 	$limit
		// 	$offset
		// ");
		// $language = "'" . $this->session->userdata('currentLanguage') . "'";
		$this->load->library(array('language_function'));
		$language = $this->language_function->getCurrentLangForPromo();
		$query = $this->db->query("SELECT promocmssetting.*
			FROM promocmscategory
			LEFT JOIN promocmssetting
		 	ON promocmssetting.promoCmsSettingId = promocmscategory.promoCmsSettingId
			WHERE promocmssetting.status = 'active'
			AND promocmssetting.language = ?
			AND promocmscategory.promoCmsCatId = 4
			ORDER BY promocmssetting.createdOn DESC
			$limit
			$offset
		", array($language));

		if ($query->num_rows() > 0) {
			// foreach ($query->result_array() as $row) {
			// 	$data[] = $row;
			// }
			// //var_dump($data);exit();
			// return $data;
			return $query->result_array();
		}
		return false;
	}

	/**
	 * Will get vip promo
	 *
	 * @param   $int
	 * @param   $int
	 * @return  array
	 */
	public function getAllVIPPromo($limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// $query = $this->db->query("SELECT mkt_promo.promoName,mkt_promo.promoCode,mkt_promodescription.promoHtmlDescription
		// 	FROM mkt_promocategory
		// 	LEFT JOIN cmspromo
		// 	ON cmspromo.promoId = mkt_promocategory.promoId
		// 	LEFT JOIN mkt_promo
		// 	ON mkt_promocategory.promoId = mkt_promo.promoId
		// 	LEFT JOIN mkt_promodescription
		// 	ON mkt_promodescription.promoId = mkt_promo.promoId
		// 	WHERE cmspromo.status = 'active'
		// 	AND mkt_promocategory.category = 'vip'
		// 	ORDER BY mkt_promo.promoId DESC
		// 	$limit
		// 	$offset
		// ");
		// $language = "'" . $this->session->userdata('currentLanguage') . "'";
		$this->load->library(array('language_function'));
		$language = $this->language_function->getCurrentLangForPromo();
		$query = $this->db->query("SELECT promocmssetting.*
			FROM promocmscategory
			LEFT JOIN promocmssetting
		 	ON promocmssetting.promoCmsSettingId = promocmscategory.promoCmsSettingId
			WHERE promocmssetting.status = 'active'
			AND promocmssetting.language = ?
			AND promocmscategory.promoCmsCatId = 3
			ORDER BY promocmssetting.createdOn DESC
			$limit
			$offset
		", array($language));

		if ($query->num_rows() > 0) {
			// foreach ($query->result_array() as $row) {
			// 	$data[] = $row;
			// }
			// //var_dump($data);exit();
			// return $data;
			return $query->result_array();
		}
		return false;
	}

	/**
	 * Will get cms promo
	 *
	 * @param	$int
	 * @return	$array
	 */
	public function getPromoCmsDetails($promocmsId) {
		//$query = $this->db->query("SELECT promoName, promoDetails,promoId,promoCmsSettingId FROM promocmssetting where promoCmsSettingId = '" . $promocmsId . "'");
		$this->db->select('promocmssetting.promoName,
						   promocmssetting.promoDetails,
						   promocmssetting.promoId,
						   promocmssetting.promoCmsSettingId,
						   promorules.promoType
						   ')
			->from('promocmssetting')
			->join('promorules', 'promorules.promorulesId = promocmssetting.promoId', 'left');
		$this->db->where('promocmssetting.promoCmsSettingId', $promocmsId);
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
	 * checkPromoPeriodApplication
	 *
	 * @param $promorulesId int
	 * @param $promoType str
	 *
	 */
	public function checkPromoPeriodApplication($promorulesId, $promoType) {
		$this->db->select('applicationPeriodStart,applicationPeriodEnd,noEndDateFlag')->from('promorules');
		$this->db->where('promorules.promorulesId', $promorulesId);
		$query = $this->db->get();
		$applicationPeriod = $query->row_array();

		$today = date('Y-m-d H:i:s');
		//var_dump($applicationPeriod);exit();
		if ($promoType == 'deposit') {
			//check if application is valid date
			if ($today >= $applicationPeriod['applicationPeriodStart']) {
				$applicationPeriodFlag = true;
			} else {
				$applicationPeriodFlag = false;
			}
		} else {
			//check if application is valid date
			if ($applicationPeriod['noEndDateFlag'] == 0) {
				if ($today >= $applicationPeriod['applicationPeriodStart']) {
					$applicationPeriodFlag = true;
				} else {
					$applicationPeriodFlag = false;
				}
			} else {
				if ($today >= $applicationPeriod['applicationPeriodStart'] && $today <= $applicationPeriod['applicationPeriodEnd']) {
					$applicationPeriodFlag = true;
				} else {
					$applicationPeriodFlag = false;
				}
			}

		}

		//var_dump($applicationPeriodFlag);exit();
		return $applicationPeriodFlag;
	}

	/**
	 * getPromoRuleGameType
	 *
	 * @param	$int
	 * @return	$array
	 */
	public function getPromoRuleGameType($promorulesId) {
		$this->db->select('promorulesgametype.gameType,game.game')->from('promorulesgametype');
		$this->db->where('promorulesgametype.promoruleId', $promorulesId);
		$this->db->join('game', 'game.gameId = promorulesgametype.gameType', 'left');
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
	 * getPlayerPromoStartDate
	 *
	 * @param	$int
	 * @return	$array
	 */
	public function getPlayerPromoStartDate($playerpromoId) {
		$this->db->select('dateApply as date')->from('playerpromo');
		$this->db->where('playerpromo.playerpromoId', $playerpromoId);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * isAutoApproveCancel
	 *
	 * @param	$int
	 * @return	$array
	 */
	const AUTO = 1;
	public function isAutoApproveCancel() {
		$this->db->select('value')->from('operator_settings');
		$this->db->where('name', 'promo_cancellation_setting');
		$query = $this->db->get();
		$cancelSetup = $query->row_array();

		if ($cancelSetup['value'] == AUTO_ONLINE_PAYMENT) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * getPromoRuleNonDepositPromoType
	 *
	 * @param	$int
	 * @return	$array
	 */
	public function getPromoRuleNonDepositPromoType($promorulesId) {
		$this->db->select('promorules.nonDepositPromoType')
			->from('promorules');
		$this->db->where('promorules.promorulesId', $promorulesId);
		$query = $this->db->get();

		return $query->row_array();
	}

	/**
	 * get registration fields
	 *
	 * @param string
	 * @return array
	 */
	function getPromoRulesId($playerpromoId) {
		$query = $this->db->query("SELECT promorulesId FROM playerpromo
			WHERE playerpromoId = '" . $playerpromoId . "'
		");

		return $query->row_array();
	}

	/**
	 * viewPromoRuleDetails
	 *
	 * @return	$array
	 */
	public function viewPromoRuleDetails($promoruleId) {
		$this->db->select('promorules.*,
						   promotype.promoTypeName,
						   admin1.userName as createdBy,
						   admin2.userName as updatedBy')->from('promorules');
		$this->db->join("promotype", "promotype.promotypeId = promorules.promoCategory");
		$this->db->join('adminusers as admin1', 'admin1.userId = promorules.createdBy', 'left');
		$this->db->join('adminusers as admin2', 'admin2.userId = promorules.updatedBy', 'left');
		$this->db->where('promorulesId', $promoruleId);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$row['applicationPeriodStart'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['applicationPeriodStart']));
				$row['applicationPeriodEnd'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['applicationPeriodEnd']));
				$row['createdOn'] = mdate('%M %d, %Y ', strtotime($row['createdOn']));
				$row['playerLevels'] = $this->getAllowedPlayerLevels($row['promorulesId']);
				$row['gameType'] = $this->getAllowedGameType($row['promorulesId']);
				$row['gameBetCondition'] = $this->getGameBetCondition($row['promorulesId']);
				$row['gameRecordStartDate'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['gameRecordStartDate']));
				$row['gameRecordEndDate'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['gameRecordEndDate']));
				if ($row['updatedOn'] != null) {
					$row['updatedOn'] = mdate('%M %d, %Y - %h:%i:%s %A', strtotime($row['updatedOn']));

				}
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * getAllowedPlayerLevels
	 *
	 * @return	$array
	 */
	public function getAllowedPlayerLevels($promoruleId) {
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId,vipsettingcashbackrule.vipLevelName,vipsetting.groupName')->from('promorulesallowedplayerlevel');
		$this->db->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = promorulesallowedplayerlevel.playerLevel', 'left');
		$this->db->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->where('promoruleId', $promoruleId);
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
	 * getAllowedGameType
	 *
	 * @return	$array
	 */
	public function getAllowedGameType($promoruleId) {
		$this->db->select('game.game')->from('promorulesgametype');
		$this->db->join('game', 'game.gameId = promorulesgametype.gameType', 'left');
		$this->db->where('promorulesgametype.promoruleId', $promoruleId);
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
	 * game bet condition
	 *
	 * @return	$array
	 */
	public function getGameBetCondition($promoruleId) {
		$this->db->select('cmsgame.gameName,cmsgame.gameCode,promorulesgamebetrule.betrequirement,game.game')->from('promorulesgamebetrule');
		$this->db->join('cmsgame', 'cmsgame.cmsGameId = promorulesgamebetrule.cmsgameId', 'left');
		$this->db->join('game', 'game.game = cmsgame.gameCompany', 'left');
		$this->db->where('promorulesgamebetrule.promoruleId', $promoruleId);
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
	 * Will save player withdrawal condition
	 *
	 * @param $conditionData array
	 * @return null
	 */
	public function savePlayerWithdrawalCondition($conditionData) {
		$this->db->insert('withdraw_conditions', $conditionData);
	}
}

/* End of file ip.php */
/* Location: ./application/models/ip.php */
