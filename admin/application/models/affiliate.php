<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Represent affiliate data. It operate game, player, banner table
 * * Get affiliate payment
 * * Get earnings
 * * Add/delete/edit/search banner
 * * Search and get payment history
 * * Get tags data
 * * Get/search/insert traffic stats
 * * Get player withdrawal and deposit
 * * Check AG/PT Records
 * * Get Total bonuses
 * * Get affiliate settings
 * * Get all players under affiliated
 *
 * @category Affiliate Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Affiliate extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	/**
	 * overview : get all affiliate
	 *
	 * @param $limit
	 * @param $offset
	 * @param $sort
	 * @return array
	 */
	public function getAllAffiliates($limit, $offset, $sort) {
		$where = null;
		$sortby = null;
		$desc_order = null;

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		if (!empty($sort['sortby'])) {
			$sortby = 'ORDER BY ' . $sort['sortby'];
		} else {
			$sortby = 'ORDER BY affiliateId ASC';
		}

		if (!empty($sort['in'])) {
			if ($sort['in'] == 'desc') {
				$desc_order = "DESC";
			} else {
				$desc_order = "ASC";
			}
		}

		$q = <<<EOD
SELECT a.*, (SELECT b.username FROM affiliates as b where a.parentId = b.affiliateId) AS parent, ats.tagName,
			(SELECT SUM(approved) FROM affiliatemonthlyearnings as ame WHERE ame.affiliateId = a.affiliateId) as approved,
			(SELECT SUM(amount) FROM affiliatepaymenthistory as aph WHERE aph.affiliateId = a.affiliateId AND aph.status IN ('0', '1', '2', '3')) as deduct_amt
			FROM affiliates as a
			LEFT JOIN affiliatetag as at
			ON a.affiliateId = at.affiliateId
			LEFT JOIN affiliatetaglist as ats
			ON at.tagId = ats.tagId
			$sortby
			$desc_order
			$limit
			$offset
EOD;
		$query = $this->db->query($q);

		return $query->result_array();
	}

	/**
	 * overview : get selected affiliates
	 *
	 * @param $affiliate_ids
	 * @return array
	 */
	public function getSelectedAffiliates($affiliate_ids) {
		$affiliate_ids = explode(',', $affiliate_ids);
		$affiliates = array();

		foreach ($affiliate_ids as $key => $value) {
			$sql = "SELECT * FROM affiliates WHERE affiliateId = ?";

			$query = $this->db->query($sql, array($value));

			array_push($affiliates, $query->row_array());
		}

		return $affiliates;
	}

	/**
	 * overview : search all affiliate from affiliate table
	 * @param $limit
	 * @param $offset
	 * @param $data
	 * @return mixed
	 */
	public function searchAllAffiliates($limit, $offset, $data) {
		$search = array();
		$sortby = null;
		$desc_order = null;

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		if (isSet($data)) {
			foreach ($data as $key => $value) {
				if ($key == 'signup_range' && $value != '') {
					$search[$key] = "a.createdOn BETWEEN $value";
				} elseif ($key == 'status' && $value != null) {
					if ($value == 'active') {
						$search[$key] = "a.status = '0'";
					} elseif ($value == 'inactive') {
						$search[$key] = "a.status = '1'";
					} else {
						$search[$key] = "a.status = '2'";
					}

				} elseif ($key == 'game' && !empty($value)) {
					$search[$key] = "ag.$key = '" . $value . "'";
				} elseif ($key == 'parentId' && !empty($value)) {
					$search[$key] = "a.$key = '" . $value . "'";
				} elseif ($value != null) {
					$search[$key] = "a.$key LIKE '%" . $value . "%'";
				}
			}
		}

		// $query = "SELECT a.*, ats.tagName,"
		// . " (SELECT SUM(approved) FROM affiliatemonthlyearnings as ame WHERE ame.affiliateId = a.affiliateId) as approved,"
		// . " (SELECT SUM(amount) FROM affiliatepaymenthistory as aph WHERE aph.affiliateId = a.affiliateId AND aph.status IN ('0', '1', '2', '3')) as deduct_amt"
		// . " FROM affiliates as a"
		// . " LEFT JOIN affiliatetag as at"
		// . " ON a.affiliateId = at.affiliateId"
		// . " LEFT JOIN affiliatetaglist as ats"
		// . " ON at.tagId = ats.tagId";

		// if (count($search) > 0) {
		// 	$query .= " WHERE " . implode(' AND ', $search);
		// }

		// $run = $this->db->query("$query $limit $offset");

		// return $run->result_array();

		$q = <<<EOD
SELECT a.*, (SELECT b.username FROM affiliates as b where a.parentId = b.affiliateId) AS parent, ats.tagName,
(SELECT SUM(approved) FROM affiliatemonthlyearnings as ame WHERE ame.affiliateId = a.affiliateId) as approved,
(SELECT SUM(amount) FROM affiliatepaymenthistory as aph WHERE aph.affiliateId = a.affiliateId AND aph.status IN ('0', '1', '2', '3')) as deduct_amt
FROM affiliates as a
LEFT JOIN affiliatetag as at
ON a.affiliateId = at.affiliateId
LEFT JOIN affiliatetaglist as ats
ON at.tagId = ats.tagId
EOD;

		if (count($search) > 0) {
			$q .= " WHERE " . implode(' AND ', $search);
		}

		// $this->utils->debug_log('search affiliate sql', $q);
		$query = $this->db->query($q);

		return $query->result_array();
	}

	/**
	 * overview : get affiliate by id
	 * @param $affiliate_id
	 * @return array
	 */
	public function getAffiliateById($affiliate_id) {
		$sql = "Select * from affiliates WHERE affiliateId = ?";

		$query = $this->db->query($sql, array($affiliate_id));

		return $query->row_array();
	}

	/**
	 * overview : get affiliate payment by affiliateid from affiliate payment table
	 * @param $affiliate_id
	 * @return array
	 */
	public function getAffiliatePaymentById($affiliate_id) {
		$sql = "Select * from affiliatepayment WHERE affiliateId =  ?";

		$query = $this->db->query($sql, array($affiliate_id));

		return $query->result_array();
	}

	/**
	 * overview : get affiliate options by affiliateid from affliateoptions table
	 *
	 * @param $affiliate_id
	 * @param $game_id
	 * @return array
	 */
	public function getAffiliateOptions($affiliate_id, $game_id) {
		$sql = "SELECT * FROM affiliateoptions WHERE affiliateId = ? AND gameId = ?";

		$query = $this->db->query($sql, array($affiliate_id, $game_id));

		return $query->result_array();
	}

	/**
	 * overview : get default affiliate options from affiliatedefaultoptions table
	 *
	 * @param $game_id
	 * @return	array
	 */
	public function getAffiliateDefaultOptionsByGameId($game_id) {
		$sql = "SELECT * FROM affiliatedefaultoptions WHERE gameId = ?";

		$query = $this->db->query($sql, array($game_id));

		return $query->result_array();
	}

	/**
	 * overview : insert affiliate options by affiliateId from affiliateoptions table
	 *
	 * @param	$data
	 * @return	array
	 */
	public function insertAffiliateTerms($data) {
		$this->db->insert('affiliateoptions', $data);
	}

	/**
	 * overview : edit affiliate options by affiliateId from affiliateoptions table
	 *
	 * @param	array $data
	 * @param	int	  $affiliate_options_id
	 * @return	array
	 */
	public function editAffiliateTerms($data, $affiliate_options_id) {
		$this->db->where('affiliateOptionsId', $affiliate_options_id);
		$this->db->update('affiliateoptions', $data);
	}

	/**
	 * overview : add affiliate game
	 *
	 * @param	array	$data
	 * @return	array
	 */
	public function addAffiliateGame($data) {
		$this->db->insert('affiliategame', $data);
	}

	/**
	 * overview : get all monthly earnings
	 *
	 * @param	int	$affiliate_id
	 * @param	int	$status
	 * @return	array
	 */
	public function getMonthlyEarningsById($affiliate_id, $status) {
		$sql = "SELECT * from affiliatemonthlyearnings
			WHERE affiliateId = ?
			AND status = ?
			ORDER BY affiliateMonthlyEarningsId ASC";

		$query = $this->db->query($sql, array($affiliate_id, $status));

		return $query->result_array();
	}

	/**
	 * overview get all monthly earnings
	 *
	 * @param	int	$status
	 * @return	array
	 */
	public function getMonthlyEarnings($status) {
		$sql = "SELECT ame.*, a.username from affiliatemonthlyearnings as ame
			LEFT JOIN affiliates as a ON ame.affiliateId = a.affiliateId
			WHERE ame.status = ?
			ORDER BY ame.affiliateMonthlyEarningsId ASC";

		$query = $this->db->query($sql, array($status));

		return $query->result_array();
	}

	/**
	 * overview : get all monthly earnings by earnings id
	 *
	 * @param	int	$earnings_id
	 * @return	array
	 */
	public function getMonthlyEarningsId($earnings_id) {
		$sql = "SELECT ame.*, a.username from affiliatemonthlyearnings as ame
			LEFT JOIN affiliates as a ON ame.affiliateId = a.affiliateId
			WHERE ame.affiliateMonthlyEarningsId = ?";

		$query = $this->db->query($sql, array($earnings_id));

		return $query->row_array();
	}

	/**
	 * overview : get all payments history
	 *
	 * @param	int $affiliate_id
	 * @param   int $limit
	 * $param	int $offset
	 * @return	array
	 */
	public function getPaymentsById($affiliate_id, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT * from affiliatepaymenthistory
			WHERE affiliateId = ?
			AND status IN ('2', '3', '4')
			ORDER BY affiliatePaymentHistoryId DESC
			$limit
			$offset";

		$query = $this->db->query($sql, array($affiliate_id));

		return $query->result_array();
	}

	/**
	 * overview : delete affiliatemonthlyearnings by affiliateId
	 *
	 * @param	int	$affiliate_id
	 */
	public function deleteAffiliateMonthlyEarnings($affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->delete('affiliatemonthlyearnings');
	}

	/**
	 * overview : delete affiliateoptions by affiliateId
	 *
	 * @param	int $affiliate_id
	 */
	public function deleteAffiliateOptions($affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->delete('affiliateoptions');
	}

	/**
	 * overview : delete affiliatepayment by affiliateId
	 *
	 * @param	int $affiliate_id
	 */
	public function deleteAffiliatePayment($affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->delete('affiliatepayment');
	}

	/**
	 * overview : delete affiliatepaymenthistory by affiliateId
	 *
	 * @param	int $affiliate_id
	 */
	public function deleteAffiliatePaymentHistory($affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->delete('affiliatepaymenthistory');
	}

	/**
	 * delete affiliatetag by affiliateId
	 *
	 * @param	int $affiliate_id
	 */
	public function deleteAffiliateTag($affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->delete('affiliatetag');
	}

	/**
	 * overview : delete banner_hits by affiliateId
	 *
	 * @param	int	$affiliate_id
	 */
	public function deleteBannerHits($affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->delete('banner_hits');
	}

	/**
	 * overview : delete traffic_stats by affiliateId
	 *
	 * @param	int $affiliate_id
	 */
	public function deleteTrafficStats($affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->delete('traffic_stats');
	}

	/**
	 * overview : delete affiliate_stats by affiliateId
	 *
	 * @param	int	$affiliate_id
	 */
	public function deleteAffiliateStats($affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->delete('affiliate_stats');
	}

	/**
	 * overview : delete affiliates by affiliateId
	 *
	 * @param	int $affiliate_id
	 */
	// public function deleteAffiliates($affiliate_id) {
	// 	$this->db->where('affiliateId', $affiliate_id);
	// 	$this->db->delete('affiliates');
	// }

	/**
	 * overview : edit affiliates by affiliateId to affiliates table
	 *
	 * @param	array $data
	 * @param	int   $affiliate_id
	 */
	public function editAffiliates($data, $affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->update('affiliates', $data);
	}

	/**
	 * overview : return banner list from banner table
	 *
	 * @param int $limit
	 * @param int $offset
	 * @param int $sort
	 * @return	array
	 */
	public function getAllBanner($limit, $offset, $sort) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		if (!empty($sort['sortby'])) {
			$sortby = 'ORDER BY ' . $sort['sortby'];
		}

		if (!empty($sort['in'])) {
			if ($sort['in'] == 'desc') {
				$desc_order = "DESC";
			} else {
				$desc_order = "ASC";
			}
		}

		$query = $this->db->query("Select b.* from banner as b
			$sortby
			$desc_order
			$limit
			$offset
		");

		return $query->result_array();
	}

	/**
	 * overview : get search banner from banner table
	 *
	 * @param	int		$limit
	 * @param	int		$offset
	 * @param	array	$data
	 * @return	array
	 */
	public function getSearchBanner($limit, $offset, $data) {
		$search = array();
		$sortby = null;
		$desc_order = null;

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		foreach ($data as $key => $value) {
			if ($key == 'sign_time_period' && $value != '') {
				if ($value == 'week') {
					$search[$key] = "b.createdOn >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
				} elseif ($value == 'month') {
					$search[$key] = "b.createdOn >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
				} elseif ($value == 'past') {
					$search[$key] = "b.createdOn >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
				}
			} elseif ($key == 'signup_range' && $value != '') {
				$search[$key] = "b.createdOn BETWEEN $value";
			} elseif ($key == 'status' && $value != null) {
				if ($value == 'active') {
					$search[$key] = "b.status = '0'";
				} elseif ($value == 'inactive') {
					$search[$key] = "b.status = '1'";
				}

			}
		}

		$query = "SELECT b.* FROM banner as b ";

		if (count($search) > 0) {
			$query .= " WHERE " . implode(' AND ', $search);
		}

		$run = $this->db->query("$query $limit $offset");
		return $run->result_array();
	}

	/**
	 * overview : add banner to banner table
	 *
	 * @param	array $banner
	 */
	public function addBanner($banner) {
		$this->db->insert('banner', $banner);
	}

	/**
	 * overview : return banner list by bannerName from banner table
	 *
	 * @param 	string	$banner_name
	 * @return	array
	 */
	public function getBannerByName($banner_name) {
		$sql = "Select b.* from banner as b where b.bannerName = ?";

		$query = $this->db->query($sql, array($banner_name));

		return $query->row_array();
	}

	/**
	 * overview : edit banner by bannerId to banner table
	 *
	 * @param	array	$data
	 * @param	int		$banner_id
	 */
	public function editBanner($data, $banner_id) {
		$this->db->where('bannerId', $banner_id);
		$this->db->update('banner', $data);
	}

	/**
	 * overview : delete banner by bannerId to banner table
	 *
	 * @param	int		$banner_id
	 */
	public function deleteBanner($banner_id) {
		$this->db->where('bannerId', $banner_id);
		return $this->db->delete('banner');
	}

	/**
	 * overview : get payment history base on conditions
	 *
	 * @param	array	$sort
	 * @param	int		$limit
	 * @param	int		$offset
	 * @return	array
	 */
	public function getPaymentHistory($sort, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		if (!empty($sort['sortby'])) {
			$sortby = 'ORDER BY ' . $sort['sortby'];
		}

		if (!empty($sort['in'])) {
			if ($sort['in'] == 'desc') {
				$desc_order = "DESC";
			} else {
				$desc_order = "ASC";
			}
		}

		$query = $this->db->query("SELECT p.*, a.username, ap.accountNumber from affiliatepaymenthistory as p
			LEFT JOIN affiliates as a
			ON p.affiliateId = a.affiliateId
			LEFT JOIN affiliatepayment as ap
			ON p.affiliatePaymentId = ap.affiliatePaymentId
			WHERE p.status IN ('0', '1', '2', '3')
			$sortby
			$desc_order
			$limit
			$offset
		");

		return $query->result_array();
	}

	/**
	 * overview : get search payment from affiliatepaymenthistory table
	 *
	 * @param	int		$limit
	 * @param	int		$offset
	 * @param	array	$data
	 * @return	array
	 */
	public function getSearchPayment($limit, $offset, $data) {

		$search = array();
		$sortby = null;
		$desc_order = null;

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		foreach ($data as $key => $value) {
			if ($key == 'signup_range' && $value != '') {
				$search[$key] = "p.createdOn BETWEEN $value";
			} elseif ($key == 'status' && $value != null) {
				if ($value == 'requests') {
					$search[$key] = "p.status = '0'";
				} elseif ($value == 'process') {
					$search[$key] = "p.status = '1'";
				} elseif ($value == 'processed') {
					$search[$key] = "p.status = '2'";
				} elseif ($value == 'denied') {
					$search[$key] = "p.status = '3'";
				}
			} elseif ($key == 'username') {
				$search[$key] = "a.$key LIKE '%" . $value . "%'";
			} elseif ($value != null) {
				$search[$key] = "a.$key = '" . $value . "'";
			}
		}

		$query = "SELECT p.*, a.username, ap.accountNumber FROM affiliatepaymenthistory as p LEFT JOIN affiliates as a ON p.affiliateId = a.affiliateId LEFT JOIN affiliatepayment as ap ON p.affiliatePaymentId = ap.affiliatePaymentId";

		if (count($search) > 0) {
			$query .= " WHERE " . implode(' AND ', $search);
		}

		$run = $this->db->query("$query $limit $offset");
		return $run->result_array();
	}

	/**
	 * overview : edit payment in affiliatepaymenthistory
	 *
	 * $param	array 	$data
	 * @param	int 	$request_id
	 */
	public function editPayment($data, $request_id) {
		$this->db->where('affiliatePaymentHistoryId', $request_id);
		$this->db->update('affiliatepaymenthistory', $data);
	}

	/**
	 * overview : check if trackingCode is unique
	 *
	 * @param	string	$trackingCode
	 * @return	bool
	 */
	public function checkTrackingCode($trackingCode) {
		$sql = "SELECT * FROM affiliates where trackingCode = ?";

		$query = $this->db->query($sql, array($trackingCode));

		$result = $query->row_array();

		if (!empty($result)) {
			return true;
		}

		return false;
	}

	/**
	 * overview : get email in email table
	 *
	 * @return	array
	 */
	public function getEmail() {
		$query = $this->db->query("SELECT * FROM email");

		return $query->row_array();
	}

	/**
	 * overview : get currency
	 *
	 * @return	void
	 */
	public function getCurrency() {
		$sql = "SELECT * FROM currency where status = ?";

		$query = $this->db->query($sql, array('0'));

		$result = $query->row_array();

		return $result['currencyCode'];
	}

	const DOMAIN_STATUS_ENABLED = '0';
	const DOMAIN_STATUS_DISABLED = '1';

	/**
	 * overview : get domain list
	 *
	 * @return	array
	 */
	public function getDomain() {
		$query = $this->db->query("SELECT * FROM domain where status= ?", array(self::DOMAIN_STATUS_ENABLED));

		return $query->result_array();
	}

	/**
	 * overview : get game list
	 *
	 * @return	array
	 */
	public function getGame() {
		$query = $this->db->query("SELECT  id AS gameId, system_code AS game FROM external_system where system_type = ?", array(SYSTEM_GAME_API));

		return $query->result_array();
	}

	/**
	 * overview : get affiliate main term setup
	 *
	 * @return array
	 */
	public function getAffiliateMainRule() {
		$query = $this->db->query("SELECT * FROM operator_settings WHERE name='affiliate_main_percentage' OR name='affiliate_main_active'");

		return $query->result_array();
	}

	/**
	 * overview : update affiliate main term setup
	 *
	 * @param int	$percentage
	 * @param int	$active
	 */
	public function updateAffiliateMainRule($percentage, $active) {
		$this->db->where('name', 'affiliate_main_percentage');
		$this->db->update('operator_settings', array('value' => $percentage));

		$this->db->where('name', 'affiliate_main_active');
		$this->db->update('operator_settings', array('value' => $active));
	}

    public function isAffiliateTagDuplicate($affiliate_id, $tag_id){
        $result = false;

        $affiliate_tag = [];
        $tags = $this->getAffiliateTag($affiliate_id);
        if(empty($tags)){
            return $result;
        }

        foreach ($tags as $tag){
            $affiliate_tag[] = $tag['tagId'];
        }

        if(in_array($tag_id, $affiliate_tag)){
            $result = true;
        }

        return $result;
    }

	/**
	 * get affiliate tag by affiliateId
	 *
	 * @param	int		$affiliate_id
	 * @return	array
	 */
	public function getAffiliateTag($affiliate_id) {
		$this->db->join('affiliatetaglist','affiliatetaglist.tagId = affiliatetag.tagId');
		$this->db->where('affiliatetag.affiliateId', $affiliate_id);
		$query = $this->db->get('affiliatetag');
		return $query->result_array();
	}

	/**
	 * overview : get affiliate tags
	 *
	 * @param $sort
	 * @param $limit
	 * @param $offset
	 * @return bool
	 */
	public function getAllTags($sort, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = null;
		}

		$query = $this->db->query("SELECT * FROM affiliatetaglist as atl left join adminusers as au on atl.createBy = au.userId ORDER BY $sort DESC $limit $offset");

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * overview : get affiliate  tag map
	 *
	 * @return	array
	 */
	public function getAffTagsMap() {
		$query = $this->db->query("SELECT tagId,tagName FROM affiliatetaglist");
    	$tags =  $query->result_array();
		$map =[];
		foreach ($tags as $value) {
			$map[$value['tagId']] = $value['tagName'];
		}
		return $map;
	}


	/**
	 * overview : get affiliate active tag
	 *
	 * @return	array
	 */
	public function getActiveTags() {

		$query = $this->db->query("SELECT * FROM affiliatetaglist as atl left join adminusers as au on atl.createBy = au.userId"); /*WHERE atl.status = '0'*/

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * overview : insert affiliate tag
	 *
	 * @param	array	$data
	 * @return	array
	 */
	public function insertAffiliateTag($data) {
		$this->db->insert('affiliatetag', $data);
	}

	/**
	 * overview : change affiliate tag
	 *
	 * @param	int		$affiliate_id
	 * @param	array	$data
	 * @return	array
	 */
	public function changeAffiliateTag($affiliate_id, $data) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->update('affiliatetag', $data);
	}

	/**
	 * overview : insert to monthly earnings from upload csv file
	 *
	 * @param	int		$affiliate_id
	 * @param	array	$filename
	 * @return	void
	 */
	public function uploadMonthlyEarnings($affiliate_id, $filename) {
		$file = fopen($filename, "r");

		while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE && $emapData[0] != null) {
			$date = date("Y-m-d H:i:s", strtotime($emapData[6]));
			$notes = (empty($emapData[5])) ? null : $emapData[5];

			$query = $this->db->query("INSERT INTO affiliatemonthlyearnings(affiliateId, active_players, opening_balance, earnings, approved, closing_balance, notes, createdOn)
            	VALUES ('$affiliate_id', $emapData[0], $emapData[1], $emapData[2], $emapData[3], $emapData[4], '$notes', '$date')
        	");
		}

		fclose($file);
	}

	/**
	 * overview : get affiliate tag by name
	 *
	 * @param	string	$tag_name
	 * @return	array
	 */
	public function getAffiliateTagByName($tag_name) {
		$sql = "SELECT * FROM affiliatetaglist WHERE tagName = ?";

		$query = $this->db->query($sql, array($tag_name));

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->row_array();
		}
	}

	/**
	 * overview : insert tag
	 *
	 * @param	array	$data
	 * @return	array
	 */
	public function insertTag($data) {
		$this->db->insert('affiliatetaglist', $data);
	}

	/**
	 * overview : edit tag
	 *
	 * @param	array	$data
	 * @param	int		$tag_id
	 * @return	array
	 */
	public function editTag($data, $tag_id) {
		$this->db->where('tagId', $tag_id);
		$this->db->update('affiliatetaglist', $data);
	}

	/**
	 * overview : get tagDetails by tagId
	 *
	 * @param	int		$tag_id
	 * @return 	array
	 */
	public function getTagDetails($tag_id) {
		$this->db->select('*')->from('affiliatetaglist');
		$this->db->where('tagId', $tag_id);

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
	 * overview : delete Affiliate Tag by TagId
	 *
	 * @param	int		$tag_id
	 * @return 	array
	 */
	public function deleteAffiliateTagByTagId($tag_id) {
		$this->db->where('tagId', $tag_id);
		$this->db->delete('affiliatetag');
	}

	public function deleteAffiliateTagByAffiliateTagId($affiliateTagId, $affiliateId = NULL) {
		if ($affiliateId) {
			$this->db->where('affiliateId', $affiliateId);
		}
		$this->db->where('affiliateTagId', $affiliateTagId);
		$this->db->delete('affiliatetag');
	}

	/**
	 * overview : delete Tag by TagId
	 *
	 * @param	int		$tag_id
	 * @return 	array
	 */
	public function deleteTag($tag_id) {
		$this->db->where('tagId', $tag_id);
		$this->db->delete('affiliatetaglist');
	}

	/**
	 * overview : search affiliate tag
	 *
	 * @param	string	$search
	 * @param	int		$limit
	 * @param	int		$offset
	 * @return 	array
	 */
	public function getSearchTag($search, $limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT * FROM affiliatetaglist as t
			LEFT JOIN affiliatetag as at
			ON t.tagId = at.tagId
			LEFT JOIN adminusers as au
			ON t.createBy = au.userId
			WHERE t.tagName LIKE '%?%'
			OR au.username LIKE '%?%'
			$limit
			$offset";

		$query = $this->db->query($sql, array(urldecode($search), urldecode($search)));

		if (!$query->result_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * overview : get bannerDetails by bannerId
	 *
	 * @param	int		$banner_id
	 * @return 	array
	 */
	public function getBannerDetails($banner_id) {
		$this->db->select('*')->from('banner');
		$this->db->where('bannerId', $banner_id);

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
	 * overview : edit affiliate default terms setup
	 *
	 * @param	array	$data
	 * @param	string	$type
	 * @param	int		$game_id
	 * @return 	array
	 */
	public function editAffiliateDefaultTerms($data, $type, $game_id) {
		$this->db->where('optionsType', $type);
		$this->db->where('gameId', $game_id);
		$this->db->update('affiliatedefaultoptions', $data);
	}

	/**
	 * overview : insert affiliate stats
	 *
	 * @param	array	$data
	 * @return 	void
	 */
	public function insertAffiliateStats($data) {
		$this->db->insert('affiliate_stats', $data);
	}

	/**
	 * overview : get Affiliate by Ids
	 *
	 * @param	int	$ids
	 * @return 	array
	 */
	public function getAffiliatesByIds($ids) {
		$ids = explode(',', $ids);
		$result = array();

		foreach ($ids as $key => $value) {
			$sql = "SELECT * FROM affiliates WHERE affiliateId = ?";

			$query = $this->db->query($sql, array($value));

			array_push($result, $query->row_array());
		}

		return $result;
	}

	/**
	 * overview : get Affiliate Earnings
	 *
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @return 	array
	 */
	public function getEarnings($start_date, $end_date) {
		$sql = "SELECT me.*, a.username FROM affiliatemonthlyearnings as me
			LEFT JOIN affiliates as a
			ON me.affiliateId = a.affiliateId
			WHERE me.createdOn BETWEEN ? AND ?";

		$query = $this->db->query($sql, array($start_date, $end_date));

		return $query->result_array();
	}

	/**
	 * get Affiliate Payments
	 *
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @return 	array
	 */
	public function getPayments($start_date, $end_date) {
		$sql = "SELECT ph.*, a.username FROM affiliatepaymenthistory as ph
			LEFT JOIN affiliates as a
			ON ph.affiliateId = a.affiliateId
			WHERE ph.createdOn BETWEEN ? AND ?
			AND ph.status = ?";

		$query = $this->db->query($sql, array($start_date, $end_date, '2'));

		return $query->result_array();
	}

	/**
	 * overview : get uri segments
	 *
	 * @return array
	 */
	function getUriSegments() {
		return explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
	}

	/**
	 * overview : get uri segment
	 *
	 * @param $n
	 * @return string
	 */
	function getUriSegment($n) {
		$segs = $this->getUriSegments();
		return count($segs) > 0 && count($segs) >= ($n - 1) ? $segs[$n] : '';
	}

	/**
	 * overview : get affiliate statistics
	 *
	 * @param date $start_date
	 * @param date $end_date
	 * @param string $username
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getStatistics($start_date = null, $end_date = null, $username = null, $limit = null, $offset = null) {

		# GET PLAYER MODEL
		$this->load->model('player_model');

		// if(!$start_date) $start_date = date('Y-m-d');
		// if(!$end_date) $end_date = date('Y-m-d');

		// $start_date = $start_date . ' 00:00:00';
		// $end_date = $end_date . ' 59:99:99';

		$statistics = [];

		# GET LIST OF AFFILIATES
		$where = array("createdOn <=" => $end_date);

		if (!empty($username)) {
			$where['username'] = $username;
		}

		$affiliates = $this->getAllAffiliate($where);

		// var_dump($affiliates); die();

		foreach ($affiliates as $a) {
			$aff['username'] = $a['username'];
			$aff['realname'] = $a['firstname'] . ' ' . $a['lastname'];

			# GET AFFILIATE LEVEL
			$aff['levels'] = count($this->getAffiliateUpLevels($a['affiliateId'], $start_date, $end_date));

			# GET LIST OF SUB-AFFILIATES OF AN AFFILIATE
			$aff['subaffiliates'] = count($this->getAllAffiliatesUnderAffiliate($a['affiliateId'], $start_date, $end_date));

			# GET LIST OF PLAYERS UNDER AFFILIATE
			$players = $this->getAllPlayersUnderAffiliateId($a['affiliateId'], $start_date, $end_date);
			$aff['players'] = count($players);

			$aff['bets'] = 0;
			$aff['win'] = 0;
			$aff['loss'] = 0;
			$aff['bonus'] = 0;

			# GET TOTAL BET
			$aff['bets'] += $this->player_model->getPlayersTotalBets($players, $start_date, $end_date);

			# GET TOTAL WIN
			$aff['win'] += $this->player_model->getPlayersTotalWin($players, $start_date, $end_date);

			# GET TOTAL LOSE
			$aff['loss'] += $this->player_model->getPlayersTotalLoss($players, $start_date, $end_date);

			# GET TOTAL BONUS
			$aff['bonus'] += $this->player_model->getPlayersTotalBonus($players, $start_date, $end_date);

			# CALCULATE NET INCOME
			$aff['income'] = $aff['win'] - $aff['loss'];

			$aff['location'] = $a['location'];
			$aff['ip'] = $a['ip_address'];

			$statistics[] = $aff;
		}

		$total = count($statistics);
		$statistics = array(
			'draw' => '',
			'recordsTotal' => $total,
			'recordsFiltered' => $total,
			'data' => array_map('array_values', $statistics),
		);

		// echo '<pre>'; print_r($statistics); die();

		return $statistics;
	}

	/**
	 * overview : get Affiliate Statistics
	 *
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @return 	array
	 */
	public function getTodayStatistics($start_date, $end_date) {
		$where = null;

		if (!empty($start_date)) {
			$where = "WHERE a.createdOn BETWEEN '" . $start_date . "' AND '" . $end_date . "'
				AND aff_stats.date BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			";
		}

		$query = $this->db->query("SELECT aff_stats.*, a.username, CONCAT(a.firstname,' ', a.lastname) as realname, a.location, a.ip_address
			FROM affiliate_stats as aff_stats
			LEFT JOIN affiliates as a
			ON aff_stats.affiliateId = a.affiliateId
			$where
			ORDER BY affiliateStatId DESC
		");

		return $query->result_array();
	}

	/**
	 * overview : get affiliate levels in affiliates table
	 *
	 * @param	int
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @param	bool	$parent
	 * @return	array
	 */
	public function getAffiliateUpLevels($affiliate_id, $start_date, $end_date, $parent = false) {
		$affiliate = [];

		$this->db->where('affiliateId', $affiliate_id);
		$a = $this->db->get('affiliates');

		if ($a->num_rows() > 0) {
			$a = $a->row_array();
			$affiliate[] = $a;

			$parent_id = $a['parentId'];

			while ($parent_id != 0) {
				$parent = $this->getAffiliateParent($parent_id);
				$parent_id = $parent['parentId'];
				$affiliate[] = $parent;
			}
		}

		return array_reverse($affiliate);
	}

	/**
	 * overview : get affiliate parent
	 *
	 * @param $affiliate_id
	 * @return mixed
	 */
	public function getAffiliateParent($affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$a = $this->db->get('affiliates');

		if ($a->num_rows() > 0) {
			$a = $a->row_array();
			return $a;
		}
	}

	/**
	 * overview : get all players under affiliate in players table
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$date_from
	 * @param	date	$date_to
	 * @return	array
	 */
	public function getAllPlayersUnderAffiliate($affiliate_id, $date_from, $date_to) {
		$where = null;

		if (!empty($date_from)) {
			// $where = "AND p.createdOn BETWEEN '" . $date_from . "' AND '" . $date_to . "'";
			$where = "AND createdOn <= '" . $date_to . "'";
		}

		// $sql = "SELECT p.*, pa.playerAccountId FROM playeraccount as pa
		// 	LEFT JOIN player as p
		// 	ON pa.playerId = p.playerId
		// 	where pa.type = ? AND pa.typeId = ?
		// 	$where";

		// $query = $this->db->query($sql, array('affiliate', $affiliate_id));

		$qPlayers = <<<EOD
SELECT *, CONCAT(pd.firstName, ' ', pd.lastName) as realname
	FROM player as p
	LEFT JOIN playerdetails as pd ON p.playerId = pd.playerId
	where p.affiliateId = $affiliate_id $where
EOD;
		$query = $this->db->query($qPlayers);

		return $query->result_array();
	}

	/**
	 * overview : get all affiliates under affiliate (subaffiliate) in affiliates table
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$date_from
	 * @param	date	$date_to
	 * @return	array
	 */
	public function getAllAffiliatesUnderAffiliate($affiliate_id, $date_from = null, $date_to = null) {
		$where = null;

		if (!empty($date_from)) {
			// $where = "AND createdOn BETWEEN '" . $date_from . "' AND '" . $date_to . "'";
			$where = "AND createdOn <= '" . $date_to . "'";
		}
		$qSubAffiliates = <<<EOD
SELECT *, CONCAT(firstname, ' ', lastname) as realname
	FROM affiliates
	where parentId = $affiliate_id
	$where
EOD;
		$query = $this->db->query($qSubAffiliates);

		return $query->result_array();
	}

	/**
	 * overview : get registered affiliates
	 *
	 * @param	date	$date_from
	 * @param	date	$date_to
	 * @return	array
	 */
	public function getRegisteredAffiliate($date_from, $date_to) {
		$where = null;

		if (!empty($date_from)) {
			$where = "WHERE createdOn BETWEEN '" . $date_from . "' AND '" . $date_to . "'";
		}

		$query = $this->db->query("SELECT * FROM affiliates
			$where
		");

		return $query->result_array();
	}

	/**
	 * overview : get all deposit of players under affiliate in walletaccount table
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$date_from
	 * @param	date	$date_to
	 * @return	array
	 */
	public function getAllPlayersDepositUnderAffiliate($affiliate_id, $date_from, $date_to) {
		$where = null;

		if (!empty($date_from)) {
			$where = "AND wa.processDatetime BETWEEN '" . $date_from . "' AND '" . $date_to . "'";
		}

		$players = $this->getPlayersAccountId($affiliate_id);

		if (empty($players)) {
			return array('amount' => '', 'count' => 0);
		}

		$sql = "SELECT SUM(wa.amount) as amount, COUNT(wa.walletAccountId) as count FROM playeraccount as pa
			LEFT JOIN walletaccount as wa
			ON pa.playerAccountId = wa.playerAccountId
			where pa.playerId IN ($players)
			AND wa.dwStatus = ?
			AND wa.transactionType = ?
			AND pa.type = ?
			$where";

		$query = $this->db->query($sql, array('approved', 'deposit', 'wallet'));

		$result = $query->row_array();

		return $result;
	}

	/**
	 * overview : get all withdraw of players under affiliate in walletaccount table
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$date_from
	 * @param	date	$date_to
	 * @return	array
	 */
	public function getAllPlayersWithdrawUnderAffiliate($affiliate_id, $date_from, $date_to) {
		$where = null;

		if (!empty($date_from)) {
			$where = "AND wa.processDatetime BETWEEN '" . $date_from . "' AND '" . $date_to . "'";
		}

		$players = $this->getPlayersAccountId($affiliate_id);

		if (empty($players)) {
			return 0;
		}

		$sql = "SELECT SUM(wa.amount) as amount FROM playeraccount as pa
			LEFT JOIN walletaccount as wa
			ON pa.playerAccountId = wa.playerAccountId
			where pa.playerId IN ($players)
			AND wa.dwStatus = ?
			AND wa.transactionType = ?
			AND pa.type = ?
			$where";

		$query = $this->db->query($sql, array('approved', 'withdrawal', 'wallet'));

		$result = $query->row_array();

		return $result['amount'];
	}

	/**
	 * overview : get all players and return their accountid
	 *
	 * @param 	int		$affiliate_id
	 * @return	string
	 */
	public function getPlayersAccountId($affiliate_id) {
		$players = $this->getAllPlayersUnderAffiliate($affiliate_id, null, null);
		$count = 0;
		$player_id = null;

		foreach ($players as $key => $value) {
			if ($count == 0) {
				$player_id = "'" . $value['playerId'] . "'";
			} else {
				$player_id .= ", '" . $value['playerId'] . "'";
			}
			$count++;
		}

		return $player_id;
	}

	/**
	 * overview : insert traffic stats
	 *
	 * @param	array	$data
	 * @return	void
	 */
	public function insertTrafficStats($data) {
		$query = $this->db->get_where('traffic_stats', array('date' => $data['date'], 'playerId' => $data['playerId']), 1, 1);
		$record = $query->result_array();
		if (!$record) {
			$this->db->insert('traffic_stats', $data);
		} else {
			$this->db->where('trafficId', $record[0]['trafficId']);
			$this->db->update('traffic_stats', $data);
		}
	}

	/**
	 * overview : sync traffic statistics
	 *
	 * @param 	array	$data
	 */
	public function syncTrafficStats($data) {
		$affiliateId = $data['affiliateId'];
		$playerId = $data['playerId'];
		$this->db->insert('traffic_stats', $data);
	}

	/**
	 * overview : get traffic stats of players under affiliate
	 *
	 * @param	int		$limit
	 * @param	int		$offset
	 * @param	array	$sort
	 * @return	array
	 */
	public function getTrafficStats($limit, $offset, $sort) {
		$sortby = null;
		$desc_order = null;

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		if (!empty($sort['sortby'])) {
			$sortby = 'ORDER BY ' . $sort['sortby'];
		}

		if (!empty($sort['in'])) {
			if ($sort['in'] == 'desc') {
				$desc_order = "DESC";
			} else {
				$desc_order = "ASC";
			}
		}

		$sql = "SELECT t.* FROM traffic_stats as t
			WHERE t.affiliateId = ?
			$sortby
			$desc_order
			$limit
			$offset";

		$query = $this->db->query($sql, array($sort['affiliate_id']));

		return $query->result_array();
	}

	/**
	 * overview : search traffic stats of players under affiliate
	 *
	 * @param	int		$limit
	 * @param	int		$offset
	 * @param	array	$data
	 * @return	array
	 */
	public function searchTrafficStats($limit, $offset, $data) {
		$search = array();
		$sortby = null;
		$desc_order = null;

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		foreach ($data as $key => $value) {
			if ($key == 'sign_time_period' && $value != '') {
				if ($value == 'week') {
					$search[$key] = "t.start_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
				} elseif ($value == 'month') {
					$search[$key] = "t.start_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
				} elseif ($value == 'past') {
					$search[$key] = "t.start_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
				}
			} elseif ($key == 'signup_range' && $value != '') {
				$search[$key] = "t.start_date BETWEEN $value";
			} elseif ($key == 'affiliate_id' && $value != '') {
				$search[$key] = "t.affiliateId = '" . $value . "'";
			}
		}

		$query = "SELECT t.* FROM traffic_stats as t";

		if (count($search) > 0) {
			$query .= " WHERE " . implode(' AND ', $search);
		}

		$run = $this->db->query("$query $limit $offset");

		return $run->result_array();
	}

	/**
	 * overview : get players by traffic id
	 *
	 * @param	int		$traffic_id
	 * @return	string
	 */
	public function getTrafficById($traffic_id) {
		$sql = "SELECT * FROM traffic_stats WHERE trafficId = ?";

		$query = $this->db->query($sql, array($traffic_id));

		return $query->row_array();
	}

	/**
	 * overview : get players
	 *
	 * @param	int		$player_id
	 * @return	array
	 */
	public function getPlayers($player_id) {
		$sql = "SELECT p.username, p.createdOn, p.lastLoginTime,
			(SELECT wa.dwDateTime FROM playeraccount as pa LEFT JOIN walletaccount as wa ON pa.playerAccountId = wa.playerAccountId where pa.playerId = '$player_id' AND wa.dwStatus = 'approved' AND wa.transactionType = 'deposit' AND pa.type = 'wallet' ORDER BY wa.dwDateTime ASC LIMIT 1) as first_deposit_date,
			(SELECT SUM(wa.amount) FROM playeraccount as pa LEFT JOIN walletaccount as wa ON pa.playerAccountId = wa.playerAccountId where pa.playerId = '$player_id' AND wa.dwStatus = 'approved' AND wa.transactionType = 'deposit' AND pa.type = 'wallet') as deposit_amount,
			(SELECT SUM(wa.amount) FROM playeraccount as pa LEFT JOIN walletaccount as wa ON pa.playerAccountId = wa.playerAccountId where pa.playerId = '$player_id' AND wa.dwStatus = 'approved' AND wa.transactionType = 'withdrawal' AND pa.type = 'wallet') as withdrawal_amount,
			(SELECT SUM(gar.bets) FROM gameapirecord as gar WHERE gar.playerName = p.username AND gar.apitype = '1') + (SELECT SUM(gar.betAmount) FROM gameapirecord as gar WHERE gar.playerName = p.username AND gar.apitype = '2' AND dataType IN ('BR', 'EBR')) as bets,
			(SELECT SUM(gar.wins) FROM gameapirecord as gar WHERE gar.playerName = p.username AND gar.apitype = '1') + (SELECT SUM(gar.netAmount) FROM gameapirecord as gar WHERE gar.playerName = p.username AND gar.apitype = '2' AND dataType IN ('BR', 'EBR') AND gar.netAmount > 0) as wins,
			FROM player as p
 			WHERE p.playerId = ?";

		$query = $this->db->query($sql, array($player_id));

		return $query->row_array();
	}

	/**
	 * overview : get players deposit
	 *
	 * @param	int		$player_id
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @return	array
	 */
	public function getPlayerDeposit($player_id, $start_date, $end_date) {
		$sql = "SELECT SUM(wa.amount) as amount FROM walletaccount as wa
			LEFT JOIN playeraccount as pa
			ON wa.playerAccountId = pa.playerAccountId
			LEFT JOIN player as p
			ON pa.playerId = p.playerId
			WHERE p.playerId = ?
			AND wa.processDatetime BETWEEN ? AND ?
			AND dwStatus = ? AND transactionType = ?";

		$query = $this->db->query($sql, array($player_id, $start_date, $end_date, 'approved', 'deposit'));

		return $query->row_array();
	}

	/**
	 * overview : get players withdrawal
	 *
	 * @param	int		$player_id
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @return	array
	 */
	public function getPlayerWithdrawal($player_id, $start_date, $end_date) {
		$sql = "SELECT SUM(wa.amount) as amount FROM walletaccount as wa
			LEFT JOIN playeraccount as pa
			ON wa.playerAccountId = pa.playerAccountId
			LEFT JOIN player as p
			ON pa.playerId = p.playerId
			WHERE p.playerId = ?
			AND wa.processDatetime BETWEEN ? AND ?
			AND dwStatus = ? AND transactionType = ?";

		$query = $this->db->query($sql, array($player_id, $start_date, $end_date, 'approved', 'withdrawal'));

		return $query->row_array();
	}

	/**
	 * overview : get players statistics
	 *
	 * @param	int		$player_id
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @return	array
	 */
	public function getPlayerStatistics($player_id, $start_date, $end_date) {
		$sql = "SELECT ts.*, p.username, p.lastLoginTime, p.lastLoginIp, CONCAT(pd.firstname, ' ', pd.lastname) as realname, pd.country as location, pd.registrationIP as ip_address FROM traffic_stats as ts
			LEFT JOIN player as p
			ON ts.playerId = p.playerId
			LEFT JOIN playerdetails as pd
			ON p.playerId = pd.playerId
			WHERE ts.playerId = ?
			AND date BETWEEN ? AND ?";

		$query = $this->db->query($sql, array($player_id, $start_date, $end_date));

		return $query->row_array();
	}

	/**
	 * overview : check PT Records
	 *
	 * @param	string	$player_name
	 * @param	date	$date_from
	 * @param	date	$date_to
	 * @return	array
	 */
	public function checkPTRecords($player_name, $date_from, $date_to) {
		$where = null;

		if (!empty($date_from)) {
			$where = "AND gamedate BETWEEN '" . $date_from . "' AND '" . $date_to . "'";
		}

		$sql = "SELECT * FROM gameapirecord
			WHERE playerName = ?
			AND apitype = ?
			$where";

		$query = $this->db->query($sql, array($player_name, 1));

		return $query->result_array();
	}

	/**
	 * overview : check AG Records
	 *
	 * @param	string	$username
	 * @param	date	$date_from
	 * @param	date	$date_to
	 * @return	array
	 */
	public function checkAGRecords($username, $date_from, $date_to) {
		$where = null;

		if (!empty($date_from)) {
			$where = "AND gamedate BETWEEN '" . $date_from . "' AND '" . $date_to . "'";
		}

		$sql = "SELECT * FROM gameapirecord
			WHERE playerName = ?
			AND apitype = ?
			$where";

		$query = $this->db->query($sql, array($username, 2));

		return $query->result_array();
	}

	/* end of Check API Records */

	/* Get Bonuses */

	/**
	 * overview : get total friend referral bonus
	 *
	 * @param 	int		$player_id
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @return  array
	 */
	public function getTotalFriendReferralBonus($player_id, $start_date, $end_date) {
		$sql = "SELECT SUM(amount) as amount FROM playerfriendreferraldetails
		WHERE referralId = ?
		AND transactionDatetime BETWEEN ? AND ?
	";

		$query = $this->db->query($sql, array($player_id, $start_date, $end_date));

		$result = $query->row_array();

		if ($result['amount'] == null) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * overview : get total cashback bonus
	 *
	 * @param 	int		$player_id
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @return  array
	 */
	public function getTotalCashbackBonus($player_id, $start_date, $end_date) {
		$sql = "SELECT SUM(amount) as amount FROM playercashback
		WHERE playerId = ?
		AND receivedOn BETWEEN ? AND ?
	";

		$query = $this->db->query($sql, array($player_id, $start_date, $end_date));

		$result = $query->row_array();

		if ($result['amount'] == null) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * overview : get total promo
	 *
	 * @param 	int		$player_id
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @return  array
	 */
	public function getTotalPromoBonus($player_id, $start_date, $end_date) {
		$sql = "SELECT SUM(bonusAmount) as amount FROM playerpromo
		WHERE playerId = ?
		AND transactionStatus = ?
		AND dateProcessed BETWEEN ? AND ?
	";

		$query = $this->db->query($sql, array($player_id, '1', $start_date, $end_date));

		$result = $query->row_array();

		if ($result['amount'] == null) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/* end of Get Bonuses */

	/* save Earnings */

	/**
	 * overview : get last closing balance daily
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$start_yesterday_date
	 * @param	date	$end_yesterday_date
	 * @param	string	$in
	 * @return	array
	 */
	public function getLastClosingBalance($affiliate_id, $start_yesterday_date, $end_yesterday_date, $in) {
		$sql = "SELECT * FROM affiliatemonthlyearnings as ame
			WHERE ame.affiliateId = ?
			AND createdOn BETWEEN ? AND ?
			AND type = ?
			ORDER BY ame.createdOn $in";

		$query = $this->db->query($sql, array($affiliate_id, $start_yesterday_date, $end_yesterday_date, 'daily'));

		return $query->row_array();
	}

	/**
	 * overview : get earnings today
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @return	array
	 */
	public function getEarningsToday($affiliate_id, $start_date, $end_date) {
		$sql = "SELECT * FROM affiliate_stats as aff_stats
			WHERE aff_stats.affiliateId = ?
			AND date BETWEEN ? AND ?
			ORDER BY aff_stats.date DESC";

		$query = $this->db->query($sql, array($affiliate_id, $start_date, $end_date));

		return $query->row_array();
	}

	/**
	 * overview : get active players monthly
	 *
	 * @param	string	$players_ids
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @param	int		$game_id
	 * @return	array
	 */
	public function getActivePlayersThisMonth($players_ids, $start_date, $end_date, $game_id) {
		$game = null;

		if ($game_id != null) {
			$game = "AND gar.apitype = '" . $game_id . "'";
		}

		$sql = "SELECT DISTINCT(p.playerId) FROM player as p
			LEFT JOIN playeraccount as pa ON p.playerId = pa.playerId
			LEFT JOIN walletaccount as wa ON pa.playerAccountId = wa.playerAccountId
			LEFT JOIN gameapirecord as gar ON p.username = gar.playername
			WHERE p.playerId IN ($players_ids)
			AND gar.gamedate BETWEEN ? AND ?
			AND wa.processDatetime BETWEEN ? AND ?
			AND wa.dwStatus = ? AND transactionType = ?
			$game";

		$query = $this->db->query($sql, array($start_date, $end_date, $start_date, $end_date, 'approved', 'deposit'));

		return $query->result_array();
	}

	/**
	 * overview : get last closing balance monthly
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$start_date
	 * @param	string	$in
	 * @return	array
	 */
	public function getMonthlyLastClosingBalance($affiliate_id, $start_date, $in) {
		$sql = "SELECT * FROM affiliatemonthlyearnings as ame
			WHERE ame.affiliateId = ?
			AND createdOn < ?
			AND type = ?
			ORDER BY ame.createdOn " . $this->db->escape_str($in);

		$query = $this->db->query($sql, array($affiliate_id, $start_date, 'monthly'));

		return $query->row_array();
	}

	/**
	 * overview : get monthly earnings
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @return	array
	 */
	public function getMonthlyEarningsToday($affiliate_id, $start_date, $end_date) {
		$sql = "SELECT SUM(total_net_gaming) as total_earnings FROM affiliate_stats
			WHERE affiliateId = ?
			AND date BETWEEN ? AND ?
			ORDER BY date DESC";

		$query = $this->db->query($sql, array($affiliate_id, $start_date, $end_date));

		return $query->row_array();
	}

	/**
	 * overview : get monthly earnings per game
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$start_date
	 * @param	date	$end_date
	 * @param	string	$game_name
	 * @return	array
	 */
	public function getMonthlyEarningsPerGame($affiliate_id, $start_date, $end_date, $game_name) {
		$sql = "SELECT * FROM affiliate_stats
			WHERE affiliateId = ?
			AND date BETWEEN ? AND ?
			ORDER BY date DESC";

		$query = $this->db->query($sql, array($affiliate_id, $start_date, $end_date));

		return $query->result_array();
	}

	/**
	 * overview : update affiliatemonthlyearnings daily
	 *
	 * @param	int		$affiliate_id
	 * @param	date	$start_date
	 * @param	date	$end_date
	 */
	public function updateDailyEarnings($affiliate_id, $start_date, $end_date) {
		$sql = "UPDATE affiliatemonthlyearnings
			SET status = ?, updatedOn = ?
			WHERE affiliateId = ?
			AND createdOn BETWEEN ? AND ?
			OR createdOn < ?
			AND status = ?";

		$query = $this->db->query($sql, array('1', $end_date, $affiliate_id, $start_date, $end_date, $start_date, '0'));
	}

	/**
	 * overview : insert affiliatemonthlyearnings
	 *
	 * @param	array	$data
	 */
	public function insertAffiliateMonthlyEarnings($data) {
		$this->db->insert('affiliatemonthlyearnings', $data);
	}

	/**
	 * overview : update monthly earnings
	 *
	 * @param	array	$data
	 * @param	int		$earnings_id
	 * @return	array
	 */
	public function updateMonthlyEarnings($data, $earnings_id) {
		$this->db->where('affiliateMonthlyEarningsId', $earnings_id);
		$this->db->update('affiliatemonthlyearnings', $data);
	}

	/* end of save Earnings */

	/**
	 * overview : add payment
	 *
	 * @param	array	$data
	 * @param	int
	 */
	public function addPayment($data) {
		$this->db->insert('affiliatepayment', $data);
	}

	/**
	 * overview : edit payment bank info
	 *
	 * @param	int	$data
	 * @param	int	$payment_id
	 */
	public function editPaymentInfo($data, $payment_id) {
		$this->db->where('affiliatePaymentId', $payment_id);
		$this->db->update('affiliatepayment', $data);
	}

	/**
	 * overview : delete payment bank info
	 *
	 * @param	int	$payment_id
	 */
	public function deletePaymentInfo($payment_id) {
		$this->db->where('affiliatePaymentId', $payment_id);
		$this->db->delete('affiliatepayment');
	}

	/**
	 * overview : get all payment method of affiliate
	 *
	 * @param	int		$affiliate_payment_id
	 * @param	int
	 */
	public function getPaymentByPaymentId($affiliate_payment_id) {
		$sql = "SELECT * FROM affiliatepayment WHERE affiliatePaymentId = ?";

		$query = $this->db->query($sql, array($affiliate_payment_id));

		return $query->row_array();
	}

	/**
	 * overview : get active affiliate
	 *
	 * @param $affiliateId
	 */
	public function active($affiliateId) {
		$data = array(
			'status' => '0',
		);
		$this->db->where('affiliateId', $affiliateId);
		return $this->db->update('affiliates', $data);
	}

	/**
	 * overview : get inactive affiliate
	 *
	 * @param $affiliateId
	 */
	public function inactive($affiliateId) {
		$data = array(
			'status' => '1',
		);
		$this->db->where('affiliateId', $affiliateId);
		return $this->db->update('affiliates', $data);
	}

	/**
	 * overview : get affiliate
	 */
	public function getAffiliates() {
		//should be active
		$sql = "SELECT * FROM affiliates where status=?";

		return $this->db->query($sql, array(self::OLD_STATUS_ACTIVE . ''))->result_array();
	}


	/**
	 * overview : checking affiliate if exist
	 *
	 * @param $affiliate
	 * @return bool
	 */
	public function checkAffiliateIfExisting($affiliate) {
		$sql = "SELECT * FROM affiliates WHERE affiliateId = ?";

		$query = $this->db->query($sql, array($affiliate));

		$result = $query->row_array();

		if (empty($result)) {
			return false;
		}

		return true;
	}

	/**
	 * overview : checking of affiliate
	 *
	 * @param  int	$affiliate
	 * @return bool
	 */
	public function checkAffiliateTermsOptions($affiliate) {
		$sql = "SELECT * FROM affiliateoptions WHERE affiliateId = ? AND optionsType = ?";

		$query = $this->db->query($sql, array($affiliate, 'cpa'));

		$result = $query->row_array();

		if (empty($result)) {
			return false;
		}

		return true;
	}

	/*OLD
		 * get affiliate options
		 *
		 *

		public function getAffiliateTermsOptions($affiliate) {
		$sql = "SELECT * FROM affiliateoptions WHERE affiliateId = ? AND optionsType = ?";

		$query = $this->db->query($sql, array($affiliate, 'cpa'));

		/**
		 * get affiliate options
		 *
		 * @param  int
		 * @return array

		public function getAffiliateTermsOptions($affiliate) {
		$sql = "SELECT * FROM affiliateoptions WHERE affiliateId = ? AND optionsType = ?";

		$query = $this->db->query($sql, array($affiliate, 'cpa'));

		return $query->row_array();
		}
	*/

	/**
	 * overview : get affiliate options
	 *
	 * @param  int	$affiliate
	 * @return array
	 */
	public function getAffiliateTermsOptions($affiliate) {
		$sql = "SELECT * FROM affiliateoptions WHERE affiliateId = ?";

		$query = $this->db->query($sql, array($affiliate));

		return $query->result_array();
	}

	/**
	 * overview : add earnings
	 *
	 * @param  array	$data
	 * @return array
	 */
	public function addAffiliateEarnings($data) {
		$this->db->insert('affiliateearnings', $data);
	}

	/**
	 * overview : get of affiliate options
	 *
	 * @param  int		$affiliate
	 * @return string
	 */
	public function getCurrencyOfAffiliate($affiliate) {
		$sql = "SELECT currency FROM affiliates WHERE trackingCode = ?";

		$query = $this->db->query($sql, array($affiliate));

		$res = $query->row_array();

		return $res['currency'];
	}

	/**
	 * overview : get affiliateId by trackingCode
	 *
	 * @param  string	$affiliate
	 * @return int
	 */
	public function getAffiliateIdByTrackingCode($affiliate) {
		$sql = "SELECT affiliateId FROM affiliates WHERE trackingCode = ?";

		$query = $this->db->query($sql, array($affiliate));

		$res = $query->row_array();

		return $res['affiliateId'];
	}

	/*
		 *  OLD CODE
		 * get affiliateId by trackingCode
		 *
		 *

		public function getAffiliate($data) {
		$query = $this->db->get('affiliates', $data);
		return $query->row_array();
		}
	*/

	/**
	 * @param array $data
	 * @return array
	 */
	public function getAffiliate($data) {
		$query = $this->db->get_where('affiliates', $data);
		return $query->row_array();
	}

	/**
	 * overview : get all affiliate
	 *
	 * @param 	 array	$data
	 * @return	 array
	 */
	public function getAllAffiliate($data) {
		$sort = $this->uri->segment('5');
		if (!$sort || $sort == 'username') {
			$this->db->order_by("username", "asc");
		} elseif ($sort == 'registration') {
			$this->db->order_by("createdOn", "asc");
		} elseif ($sort == 'login') {
			$this->db->order_by("lastLoginTime", "asc");
		} elseif ($sort == 'report') {
			$this->db->order_by("lastActivityTime", "asc");
		}
		$query = $this->db->get_where('affiliates', $data);
		return $query->result_array();
	}

	/**
	 * overview : update default item
	 *
	 * @param $row
	 * @param $data
	 */
	public function updateDefaultTerms($row, $data) {
		$this->db->where('name', $row);
		$this->db->update('operator_settings', ['value' => $data]);
	}

	/**
	 * overview : update terms by id
	 *
	 * @param $affiliateId
	 * @param $row
	 * @param $data
	 */
	public function updateTermsById($affiliateId, $row, $data) {
		// var_dump(func_get_args()); die();

		$this->db->where('affiliateId', $affiliateId);
		$this->db->where('optionType', $row);
		$result = $this->db->get('affiliate_terms');

		if ($result->num_rows > 0) {
			$this->db->where('affiliateId', $affiliateId);
			$this->db->where('optionType', $row);
			$this->db->update('affiliate_terms', ['optionValue' => $data]);
		} else {
			$this->addTermsById($affiliateId, $row, $data);
		}
	}

	/**
	 * overview : add terms by id
	 *
	 * @param $affiliateId
	 * @param $row
	 * @param $data
	 */
	public function addTermsById($affiliateId, $row, $data) {
		$data = array(
			'affiliateId' => $affiliateId,
			'optionType' => $row,
			'optionValue' => $data,
		);

		$this->db->insert('affiliate_terms', $data);
	}

	/**
	 * overview : get affiliate settings
	 *
	 * @return null|string
	 */
	public function getAffiliateSettings() {
		$this->db->where('name', 'affiliate_settings');
		$result = $this->db->get('operator_settings');

		if ($result->num_rows() > 0) {
			$result = $result->result();
			return $result[0]->value;
		} else {
			return null;
		}
	}

	/**
	 * overview : get default affiliate terms
	 *
	 * @return null|string
	 */
	public function getDefaultAffiliateTerms() {
		$this->db->where('name', 'affiliate_default_terms');
		$result = $this->db->get('operator_settings');

		if ($result->num_rows() > 0) {
			$result = $result->result();
			return $result[0]->value;
		} else {
			return null;
		}

	}

	/**
	 * overview : get default sub affiliate terms
	 *
	 * @return null|string
	 */
	public function getDefaultSubAffiliateTerms() {
		$this->db->where('name', 'sub_affiliate_default_terms');
		$result = $this->db->get('operator_settings');

		if ($result->num_rows() > 0) {
			$result = $result->result();
			return $result[0]->value;
		} else {
			return null;
		}

	}

	/**
	 * overview : get affiliate terms by id
	 *
	 * @param  int	$affiliate_id
	 * @return null|string
	 */
	public function getAffiliateTermsById($affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->where('optionType', 'affiliate_default_terms');
		$result = $this->db->get('affiliate_terms');

		if ($result->num_rows() > 0) {
			$result = $result->result();
			return $result[0]->optionValue;
		} else {
			return null;
		}

	}

	/**
	 * overview : get sub affiliate terms by id
	 * @param $affiliate_id
	 * @return null|int
	 */
	public function getSubAffiliateTermsById($affiliate_id) {
		$this->db->where('affiliateId', $affiliate_id);
		$this->db->where('optionType', 'sub_affiliate_default_terms');
		$result = $this->db->get('affiliate_terms');

		if ($result->num_rows() > 0) {
			$result = $result->result();
			return $result[0]->optionValue;
		} else {
			return null;
		}

	}

	/**
	 * overview : get all player under affiliate id
	 *
	 * @param int	$affiliate_id
	 * @param date 	$date_from
	 * @param date 	$date_to
	 * @return array
	 */
	public function getAllPlayersUnderAffiliateId($affiliate_id, $date_from = null, $date_to = null) {
		$where = null;

		if (!empty($date_to)) {
			$where = "AND createdOn <= '" . $date_to . "'";
		} else {
			$where = "AND createdOn <= '" . date('Y-m-d') . "'";
		}

		$qPlayers = <<<EOD
SELECT playerId
	FROM player
	WHERE affiliateId = $affiliate_id
EOD;
		$query = $this->db->query($qPlayers);

		$result = [];
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $q) {
				$result[] = $q->playerId;
			}
		}

		return $result;
	}

	/**
	 * overview : filter active players by id
	 *
	 * @param int		$players_ids
	 * @param date		$yearmonth
	 * @param string	$getUsername
	 * @return array
	 */
	public function filterActivePlayersById($players_ids, $yearmonth = null, $getUsername = null) {
		// var_dump(func_get_args()); // debug: get gross income per affiliate by Bet-Win Condition
		# INITIALIZE MODEL
		$this->load->model(array('player_model'));

		# INITIALIZE PLAYER ID CONTAINER
		$active_players = array();

		# INITIALIZE YEAR MONTH
		$year = date('Y');
		$month = date('m');

		if (!empty($yearmonth)) {
			$year = substr($yearmonth, 0, 4);
			$month = substr($yearmonth, 4, 6);
		}

		# VALIDATE PLAYERS_ID
		if (count($players_ids)) {
			# CHECK ALL PLAYERS_ID
			foreach ($players_ids as $key => $value) {
				if ($this->player_model->isActivePlayer($value, $year, $month)) {
					if (!empty($getUsername)) {
						array_push($active_players, $this->player_model->getUsernameById($value));
					} else {
						array_push($active_players, $value);
					}
				}
			}
		}

		return $active_players;
	}

	/**
	 * overview : filter active player by id provider
	 *
	 * @param int	$players_ids
	 * @param int 	$yearmonth
	 * @param date	$providers_id
	 * @param int	$count
	 * @return array
	 */
	public function filterActivePlayersByIdByProvider($players_ids, $yearmonth = null, $providers_id, $count) {
		# INITIALIZE MODEL
		$this->load->model(array('player_model'));

		# INITIALIZE PLAYER ID CONTAINER
		$active_players = array();

		# INITIALIZE YEAR MONTH
		$year = date('Y');
		$month = date('m');

		if (!empty($yearmonth)) {
			$year = substr($yearmonth, 0, 4);
			$month = substr($yearmonth, 4, 6);
		}

		# VALIDATE PLAYERS_ID
		if (count($players_ids)) {
			# CHECK ALL PLAYERS_ID
			foreach ($players_ids as $key => $value) {
				if ($this->player_model->isActivePlayerByProvider($value, $year, $month, $providers_id)) {
					array_push($active_players, $value);
				}
			}
		}

		if (count($active_players) >= $count) {
			return $active_players;
		} else {
			return [];
		}

	}

	/**
	 * overview : check if username is exist
	 *
	 * @param 	string	 $username
	 * @return  bool
	 */
	public function checkUsernameIfExist($username) {

		$sql = "SELECT affiliateId as id , username FROM affiliates WHERE username = ? ";

		$q = $this->db->query($sql, array($username));

		$results = $q->result();

		if ($q->num_rows() > 0) {
			$results['isExist'] = TRUE;
			return $results;
		} else {
			return FALSE;
		}
	}

	/**
	 * overview : select ci affiliate session
	 *
	 * @return array
	 */
	public function selectCiAffiliateSessions() {
		$sql = 'SELECT * FROM ci_aff_sessions';
		$query = $this->db->query($sql);
		return array(
			'total' => $query->num_rows(),
			'data' => $query->result_array(),
		);
	}

    public function getAffiliateByName($username) {
        $this->db->from('affiliates')->where('username', $username);
        return $this->runOneRowArray();
    }

	public function getAffiliateIdByAgent_name($affiliate_username) {
		$this->db->select('affiliateId')->from('affiliates')->where('username', $affiliate_username);
		return $this->runOneRowOneField('affiliateId');
	}




}