<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Affiliate Manager
 *
 * Affiliate Manager library for FHI OG System
 *
 * @package		Affiliate Manager
 * @author		Johann Merle
 * @version		1.0.0
 */

class Affiliate_manager {
	private $error = array();

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array(''));
		$this->ci->load->model(array('affiliate'));
	}

	/**
	 * Will randomize alphanumeric and special characters
	 *
	 * @param 	string
	 * @return	string
	 */
	public function randomizer($name) {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789' //!@#$%^&*()
			 . $name); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$randomPassword = '';
		foreach (array_rand($seed, 8) as $k) {
			$randomPassword .= $seed[$k];
		}

		return $randomPassword;
	}

	/**
	 * get all affiliates from affiliate table
	 *
	 * @param	int
	 * @param	int
	 * @param	array
	 * @return	array
	 */
	public function getAllAffiliates($limit, $offset, $data) {
		return $this->ci->affiliate->getAllAffiliates($limit, $offset, $data);
	}

	/**
	 * get selected affiliates from affiliate table
	 *
	 * @param	int
	 * @param	int
	 * @param	array
	 * @return	array
	 */
	public function getSelectedAffiliates($affiliate_ids) {
		return $this->ci->affiliate->getSelectedAffiliates($affiliate_ids);
	}

	/**
	 * search all affiliates from affiliate table
	 *
	 * @param	int
	 * @param	int
	 * @param	array
	 * @return	array
	 */
	public function searchAllAffiliates($limit, $offset, $data) {
		return $this->ci->affiliate->searchAllAffiliates($limit, $offset, $data);
	}

	/**
	 * get affiliate by affiliateId from affiliate table
	 *
	 * @param	int
	 * @return	array
	 */
	public function getAffiliateById($affiliate_id) {
		return $this->ci->affiliate->getAffiliateById($affiliate_id);
	}

	/**
	 * get affiliate payment by affiliateId from affiliatepayment table
	 *
	 * @param	int
	 * @return	array
	 */
	public function getAffiliatePaymentById($affiliate_id) {
		return $this->ci->affiliate->getAffiliatePaymentById($affiliate_id);
	}

	/**
	 * get affiliate options by affiliateId from affiliateoptions table
	 *
	 * @param	int
	 * @return	array
	 */
	public function getAffiliateOptions($affiliate_id, $game_id) {
		return $this->ci->affiliate->getAffiliateOptions($affiliate_id, $game_id);
	}

	/**
	 * get default affiliate options from affiliatedefaultoptions table
	 *
	 * @return	array
	 */
	public function getAffiliateDefaultOptionsByGameId($game_id) {
		return $this->ci->affiliate->getAffiliateDefaultOptionsByGameId($game_id);
	}

	/**
	 * insert affiliate options by affiliateId from affiliateoptions table
	 *
	 * @param	array
	 * @return	array
	 */
	public function insertAffiliateTerms($data) {
		return $this->ci->affiliate->insertAffiliateTerms($data);
	}

	/**
	 * edit affiliate options by affiliateId from affiliateoptions table
	 *
	 * @param	array
	 * @param	int
	 * @return	array
	 */
	public function editAffiliateTerms($data, $affiliate_options_id) {
		return $this->ci->affiliate->editAffiliateTerms($data, $affiliate_options_id);
	}

	/**
	 * add affiliate game
	 *
	 * @param	array
	 * @return	array
	 */
	public function addAffiliateGame($data) {
		return $this->ci->affiliate->addAffiliateGame($data);
	}

	/**
	 * get all monthly earnings
	 *
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getMonthlyEarningsById($affiliate_id, $status) {
		return $this->ci->affiliate->getMonthlyEarningsById($affiliate_id, $status);
	}

	/**
	 * get all monthly earnings
	 *
	 * @param	int
	 * @return	array
	 */
	public function getMonthlyEarnings($status) {
		return $this->ci->affiliate->getMonthlyEarnings($status);
	}

	/**
	 * get all monthly earnings by earnings id
	 *
	 * @param	int
	 * @return	array
	 */
	public function getMonthlyEarningsId($earnings_id) {
		return $this->ci->affiliate->getMonthlyEarningsId($earnings_id);
	}

	/**
	 * get all payments history
	 *
	 * @param	int
	 * @return	array
	 */
	public function getPaymentsById($affiliate_id, $limit, $offset) {
		return $this->ci->affiliate->getPaymentsById($affiliate_id, $limit, $offset);
	}

	/**
	 * delete affiliate by affiliateId to affiliates table
	 *
	 * @param	int
	 */
	// public function deleteAffiliate($affiliate_id) {
	// 	$this->ci->affiliate->deleteAffiliateMonthlyEarnings($affiliate_id);
	// 	$this->ci->affiliate->deleteAffiliateOptions($affiliate_id);
	// 	$this->ci->affiliate->deleteAffiliatePayment($affiliate_id);
	// 	$this->ci->affiliate->deleteAffiliatePaymentHistory($affiliate_id);
	// 	$this->ci->affiliate->deleteAffiliateTag($affiliate_id);
	// 	$this->ci->affiliate->deleteBannerHits($affiliate_id);
	// 	$this->ci->affiliate->deleteTrafficStats($affiliate_id);
	// 	$this->ci->affiliate->deleteAffiliateStats($affiliate_id);
	// 	$this->ci->affiliate->deleteAffiliates($affiliate_id);
	// }

	/**
	 * delete affiliateoptions by affiliateId to affiliateoptions table
	 *
	 * @param	int
	 */
	public function deleteAffiliateOptions($affiliate_id) {
		$this->ci->affiliate->deleteAffiliateOptions($affiliate_id);
	}

	/**
	 * edit affiliates by affiliateId to affiliates table
	 *
	 * @param	array
	 * @param	int
	 */
	public function editAffiliates($data, $affiliate_id) {
		$this->ci->affiliate->editAffiliates($data, $affiliate_id);
	}

	/**
	 * get all banner from banner table
	 *
	 * @param	int
	 * @param	int
	 * @param	array
	 * @return	array
	 */
	public function getAllBanner($limit, $offset, $sort) {
		return $this->ci->affiliate->getAllBanner($limit, $offset, $sort);
	}

	/**
	 * get search banner from banner table
	 *
	 * @param	int
	 * @param	int
	 * @param	array
	 * @return	array
	 */
	public function getSearchBanner($limit, $offset, $search) {
		return $this->ci->affiliate->getSearchBanner($limit, $offset, $search);
	}

	/**
	 * add banner to banner table
	 *
	 * @param	array
	 */
	public function addBanner($banner) {
		$this->ci->affiliate->addBanner($banner);
	}

	/**
	 * return banner list by bannerName from banner table
	 *
	 * @return	array
	 */
	public function getBannerByName($banner_name) {
		return $this->ci->affiliate->getBannerByName($banner_name);
	}

	/**
	 * edit banner by bannerId to banner table
	 *
	 * @param	array
	 * @param	int
	 */
	public function editBanner($data, $banner_id) {
		$this->ci->affiliate->editBanner($data, $banner_id);
	}

	/**
	 * delete banner by bannerId to banner table
	 *
	 * @param	int
	 */
	public function deleteBanner($banner_id) {
		$this->ci->affiliate->deleteBanner($banner_id);
	}

	/**
	 * get payment history base on conditions
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getPaymentHistory($sort, $limit, $offset) {
		return $this->ci->affiliate->getPaymentHistory($sort, $limit, $offset);
	}

	/**
	 * get search payment from affiliatepaymenthistory table
	 *
	 * @param	int
	 * @param	int
	 * @param	array
	 * @return	array
	 */
	public function getSearchPayment($limit, $offset, $search) {
		return $this->ci->affiliate->getSearchPayment($limit, $offset, $search);
	}

	/**
	 * edit payment in affiliatepaymenthistory
	 *
	 * @param	int
	 */
	public function editPayment($data, $request_id) {
		$this->ci->affiliate->editPayment($data, $request_id);
	}

	/**
	 * check if trackingCode is unique
	 *
	 * @param	string
	 * @return	bool
	 */
	public function checkTrackingCode($trackingCode) {
		return $this->ci->affiliate->checkTrackingCode($trackingCode);
	}

	/**
	 * get email in email table
	 *
	 * @return	array
	 */
	public function getEmail() {
		return $this->ci->ip->getEmail();
	}

	/**
	 * get currency
	 *
	 * @param	array
	 * @return	void
	 */
	public function getCurrency() {
		return $this->ci->affiliate->getCurrency();
	}

	/**
	 * get domain list
	 *
	 * @return	array
	 */
	public function getDomain() {
		return $this->ci->affiliate->getDomain();
	}

	/**
	 * get game list
	 *
	 * @return	array
	 */
	public function getGame() {
		return $this->ci->affiliate->getGame();
	}

	/**
	 * get affiliate main term setup
	 */
	public function getAffiliateMainRule() {
		return $this->ci->affiliate->getAffiliateMainRule();
	}

	/**
	 * update affiliate main term setup
	 *
	 * @param int
	 * @param int
	 */
	public function updateAffiliateMainRule($percentage, $active) {
		return $this->ci->affiliate->updateAffiliateMainRule($percentage, $active);
	}

	/**
	 * get affiliate tag by affiliateId
	 *
	 * @return	array
	 */
	public function getAffiliateTag($affiliate_id) {
		return $this->ci->affiliate->getAffiliateTag($affiliate_id);
	}

    /**
     * check affiliate tag is duplicate by affiliateId and tagId
     *
     * @return	bool
     */
    public function isAffiliateTagDuplicate($affiliate_id, $tag_id) {
        return $this->ci->affiliate->isAffiliateTagDuplicate($affiliate_id, $tag_id);
    }
	/**
	 * get affiliate tag
	 *
	 * @param	string
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getAllTags($sort, $limit, $offset) {
		return $this->ci->affiliate->getAllTags($sort, $limit, $offset);
	}

	/**
	 * get affiliate active tag
	 *
	 * @return	array
	 */
	public function getActiveTags() {
		return $this->ci->affiliate->getActiveTags();
	}

	/**
	 * insert affiliate tag
	 *
	 * @param	array
	 * @return	array
	 */
	public function insertAffiliateTag($data) {
		return $this->ci->affiliate->insertAffiliateTag($data);
	}

	/**
	 * change affiliate tag
	 *
	 * @param	int
	 * @param	array
	 * @return	array
	 */
	public function changeAffiliateTag($affiliate_id, $data) {
		return $this->ci->affiliate->changeAffiliateTag($affiliate_id, $data);
	}

	/**
	 * insert to monthly earnings from upload csv file
	 *
	 * @param	int
	 * @param	string
	 * @return	void
	 */
	public function uploadMonthlyEarnings($affiliate_id, $filename) {
		$this->ci->affiliate->uploadMonthlyEarnings($affiliate_id, $filename);
	}

	/**
	 * get affiliate tag by name
	 *
	 * @param	string
	 * @return	array
	 */
	public function getAffiliateTagByName($tag_name) {
		return $this->ci->affiliate->getAffiliateTagByName($tag_name);
	}

	/**
	 * insert tag
	 *
	 * @param	array
	 * @return	array
	 */
	public function insertTag($data) {
		return $this->ci->affiliate->insertTag($data);
	}

	/**
	 * edit tag
	 *
	 * @param	array
	 * @param	int
	 * @return	array
	 */
	public function editTag($data, $tag_id) {
		return $this->ci->affiliate->editTag($data, $tag_id);
	}

	/**
	 * get tagDetails by tagId
	 *
	 * @param	int
	 * @return 	array
	 */
	public function getTagDetails($tag_id) {
		return $this->ci->affiliate->getTagDetails($tag_id);
	}

	/**
	 * delete Affiliate Tag by TagId
	 *
	 * @param	int
	 * @return 	array
	 */
	public function deleteAffiliateTagByTagId($tag_id) {
		$this->ci->affiliate->deleteAffiliateTagByTagId($tag_id);
	}

	public function deleteAffiliateTagByAffiliateTagId($affiliateTagId, $affiliateId = NULL) {
		$this->ci->affiliate->deleteAffiliateTagByAffiliateTagId($affiliateTagId, $affiliateId);
	}

	/**
	 * delete Affiliate Tag by affiliateId
	 *
	 * @param	int
	 * @return 	array
	 */
	public function deleteAffiliateTag($affiliate_id) {
		$this->ci->affiliate->deleteAffiliateTag($affiliate_id);
	}

	/**
	 * delete Tag by TagId
	 *
	 * @param	int
	 * @return 	array
	 */
	public function deleteTag($tag_id) {
		$this->ci->affiliate->deleteTag($tag_id);
	}

	/**
	 * search affiliate tag
	 *
	 * @param	string
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	public function getSearchTag($search, $limit, $offset) {
		return $this->ci->affiliate->getSearchTag($search, $limit, $offset);
	}

	/**
	 * get bannerDetails by bannerId
	 *
	 * @param	int
	 * @return 	array
	 */
	public function getBannerDetails($banner_id) {
		return $this->ci->affiliate->getBannerDetails($banner_id);
	}

	/**
	 * edit affiliate default terms setup
	 *
	 * @param	array
	 * @param	string
	 * @return 	array
	 */
	public function editAffiliateDefaultTerms($data, $type, $game_id) {
		return $this->ci->affiliate->editAffiliateDefaultTerms($data, $type, $game_id);
	}

	/**
	 * get Affiliate Total Bets
	 *
	 * @param	date
	 * @param	date
	 * @return 	array
	 */
	public function getAffiliateTotalBets($affiliate_id, $start_date, $end_date) {
		$pt_bets = $this->getAllPTBetsUnderAffiliate($affiliate_id, $start_date, $end_date);
		$ag_bets = $this->getAllAGBetsUnderAffiliate($affiliate_id, $start_date, $end_date);

		$total_bets = $pt_bets + $ag_bets;

		return $total_bets;
	}

	/**
	 * get Affiliate Total Wins
	 *
	 * @param	date
	 * @param	date
	 * @return 	array
	 */
	public function getAffiliateTotalWins($affiliate_id, $start_date, $end_date) {
		$pt_wins = $this->getAllPTWinsUnderAffiliate($affiliate_id, $start_date, $end_date);
		$ag_wins = $this->getAllAGWinsUnderAffiliate($affiliate_id, $start_date, $end_date);

		$total_wins = $pt_wins + $ag_wins;

		return $total_wins;
	}

	/**
	 * get Affiliate Total Loss
	 *
	 * @param	date
	 * @param	date
	 * @return 	array
	 */
	public function getAffiliateTotalLoss($affiliate_id, $start_date, $end_date) {
		$pt_loss = $this->getAllPTLossUnderAffiliate($affiliate_id, $start_date, $end_date);
		$ag_loss = $this->getAllAGLossUnderAffiliate($affiliate_id, $start_date, $end_date);

		$total_loss = $pt_loss + abs($ag_loss);

		return $total_loss;
	}

	/**
	 * get Affiliate Total Bonus
	 *
	 * @param	date
	 * @param	date
	 * @return 	array
	 */
	public function getAffiliateTotalBonus($affiliate_id, $start_date, $end_date) {
		$result = array();

		$players = $this->getAllPlayersUnderAffiliate($affiliate_id, null, null);

		foreach ($players as $key => $value) {
			$player = $this->getTotalBonuses($value['playerId'], $start_date, $end_date);

			array_push($result, $player);
		}

		if (!empty($result)) {
			$total_bonus = 0;

			foreach ($result as $key => $value) {
				$total_bonus += $value;
			}

			return $total_bonus;
		} else {
			return 0;
		}
	}

	/**
	 * get total bonuses
	 *
	 * @return  array
	 */
	public function getTotalBonuses($player_id, $start_date, $end_date) {
		$total_friend_referral_bonus = $this->getTotalFriendReferralBonus($player_id, $start_date, $end_date);
		$total_cashback_bonus = $this->getTotalCashbackBonus($player_id, $start_date, $end_date);
		$total_promo_bonus = $this->getTotalPromoBonus($player_id, $start_date, $end_date);

		$total_bonus = $total_friend_referral_bonus + $total_cashback_bonus + $total_promo_bonus;

		return $total_bonus;
	}

	/**
	 * get total friend referral bonus
	 *
	 * @return  array
	 */
	public function getTotalFriendReferralBonus($player_id, $start_date, $end_date) {
		return $this->ci->affiliate->getTotalFriendReferralBonus($player_id, $start_date, $end_date);
	}

	/**
	 * get total cashback bonus
	 *
	 * @return  array
	 */
	public function getTotalCashbackBonus($player_id, $start_date, $end_date) {
		return $this->ci->affiliate->getTotalCashbackBonus($player_id, $start_date, $end_date);
	}

	/**
	 * get total promo bonus
	 *
	 * @return  array
	 */
	public function getTotalPromoBonus($player_id, $start_date, $end_date) {
		return $this->ci->affiliate->getTotalPromoBonus($player_id, $start_date, $end_date);
	}

	/**
	 * insert affiliate stats
	 *
	 * @param	array
	 * @return 	void
	 */
	public function insertAffiliateStats($data) {
		return $this->ci->affiliate->insertAffiliateStats($data);
	}

	/**
	 * get Affiliate by Ids
	 *
	 * @param	date
	 * @param	date
	 * @return 	array
	 */
	public function getAffiliatesByIds($ids) {
		return $this->ci->affiliate->getAffiliatesByIds($ids);
	}

	/**
	 * get Affiliate Earnings
	 *
	 * @param	date
	 * @param	date
	 * @return 	array
	 */
	public function getEarnings($start_date, $end_date) {
		return $this->ci->affiliate->getEarnings($start_date, $end_date);
	}

	/**
	 * get Affiliate Payments
	 *
	 * @param	date
	 * @param	date
	 * @return 	array
	 */
	public function getPayments($start_date, $end_date) {
		return $this->ci->affiliate->getPayments($start_date, $end_date);
	}

	/* Affiliate Stats */
	/**
	 * get Affiliate Statistics
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return 	array
	 */
	public function getStatistics($start_date, $end_date) {
		return $this->ci->affiliate->getStatistics($start_date, $end_date);

		// $aff_stats = $this->ci->affiliate->getStatistics(null, null);
		// $affiliates_count = count($this->getAllAffiliates(null, null, null));

		// $result = array();
		// $cnt = 0;

		// foreach ($aff_stats as $key => $value) {
		// 	array_push($result, $value);
		// 	$cnt++;

		// 	if ($cnt >= $affiliates_count) {
		// 		break;
		// 	}
		// }

		// return $result;
	}

	/**
	 * get Today Affiliate Statistics
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return 	array
	 */
	public function getTodayStatistics($start_date, $end_date) {
		return $this->ci->affiliate->getStatistics($start_date, $end_date);
	}

	/**
	 * get Daily Affiliate Statistics
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return 	array
	 */
	public function getDailyStatistics($start_date, $end_date) {
		$aff_stats = $this->ci->affiliate->getStatistics($start_date, $end_date);

		$result = array();
		$data = array();

		$date = null;

		foreach ($aff_stats as $key => $value) {
			$results = array();

			if ($date != $value['date']) {
				$date = $value['date'];
				$aff_count = 0;

				$pt_bet = 0;
				$ag_bet = 0;
				$total_bet = 0;

				$pt_win = 0;
				$ag_win = 0;
				$total_win = 0;

				$pt_loss = 0;
				$ag_loss = 0;
				$total_loss = 0;

				$total_net_gaming = 0;
				$total_bonus = 0;

				$results = $this->search($aff_stats, 'date', $value['date']);

				foreach ($results as $key => $value) {
					$aff_count++;

					$pt_bet += $value['pt_bet'];
					$ag_bet += $value['ag_bet'];
					$total_bet += $value['total_bet'];

					$pt_win += $value['pt_win'];
					$ag_win += $value['ag_win'];
					$total_win += $value['total_win'];

					$pt_loss += $value['pt_loss'];
					$ag_loss += $value['ag_loss'];
					$total_loss += $value['total_loss'];

					$total_net_gaming += $value['total_net_gaming'];
					$total_bonus += $value['total_bonus'];
					//$total_affiliates = $value['total_affiliate'];
				}

				$data = array(
					'pt_bet' => $pt_bet,
					'ag_bet' => $ag_bet,
					'total_bet' => $total_bet,
					'pt_win' => $pt_win,
					'ag_win' => $ag_win,
					'total_win' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_affiliates' => $aff_count,
					'date' => $date,
				);
				array_push($result, $data);
			}
		}

		return $result;
	}

	function search($array, $key, $value) {
		$results = array();
		$this->search_r($array, $key, $value, $results);
		return $results;
	}

	function search_r($array, $key, $value, &$results) {
		if (!is_array($array)) {
			return;
		}

		if (isset($array[$key]) && $array[$key] == $value) {
			$results[] = $array;
		}

		foreach ($array as $subarray) {
			$this->search_r($subarray, $key, $value, $results);
		}
	}

	/**
	 * get Weekly Affiliate Statistics
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return 	array
	 */
	public function getWeeklyStatistics($start_date, $end_date) {
		$aff_stats = array_reverse($this->getDailyStatistics($start_date, $end_date));

		$result = array();

		$aff_count = 0;

		$pt_bet = 0;
		$ag_bet = 0;
		$total_bet = 0;

		$pt_win = 0;
		$ag_win = 0;
		$total_win = 0;

		$pt_loss = 0;
		$ag_loss = 0;
		$total_loss = 0;

		$total_net_gaming = 0;
		$total_bonus = 0;
		$total_affiliate = 0;

		$stats_count = count($aff_stats);
		$counter = 0;

		foreach ($aff_stats as $key => $value) {
			$counter++;

			if ($aff_count == 0) {
				$date_start = date('Y-m-d', strtotime($value['date']));
				$daycount = 7 - date("N", strtotime($date_start));
				$date_end = date('Y-m-d', strtotime($date_start . '+' . $daycount . ' day'));
			}

			$aff_count++;

			$pt_bet += $value['pt_bet'];
			$ag_bet += $value['ag_bet'];
			$total_bet += $value['total_bet'];

			$pt_win += $value['pt_win'];
			$ag_win += $value['ag_win'];
			$total_win += $value['total_win'];

			$pt_loss += $value['pt_loss'];
			$ag_loss += $value['ag_loss'];
			$total_loss += $value['total_loss'];

			$total_net_gaming += $value['total_net_gaming'];
			$total_bonus += $value['total_bonus'];
			$total_affiliate = $value['total_affiliates'];

			if ($date_end == $value['date']) {
				$data = array(
					'pt_bet' => $pt_bet,
					'ag_bet' => $ag_bet,
					'total_bet' => $total_bet,
					'pt_win' => $pt_win,
					'ag_win' => $ag_win,
					'total_win' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_affiliates' => $total_affiliate,
					'date' => ($date_start == $date_end) ? $date_start : $date_start . " - " . $date_end,
				);
				array_push($result, $data);

				$aff_count = 0;

				$pt_bet = 0;
				$ag_bet = 0;
				$total_bet = 0;

				$pt_win = 0;
				$ag_win = 0;
				$total_win = 0;

				$pt_loss = 0;
				$ag_loss = 0;
				$total_loss = 0;

				$total_net_gaming = 0;
				$total_bonus = 0;
				$total_affiliate = 0;
			} else if ($counter == $stats_count) {
				$data = array(
					'pt_bet' => $pt_bet,
					'ag_bet' => $ag_bet,
					'total_bet' => $total_bet,
					'pt_win' => $pt_win,
					'ag_win' => $ag_win,
					'total_win' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_affiliates' => $total_affiliate,
					'date' => ($date_start == $value['date']) ? $date_start : $date_start . " - " . $value['date'],
				);
				array_push($result, $data);
			}
		}

		return $result;
	}

	/**
	 * get Monthly Affiliate Statistics
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return 	array
	 */
	public function getMonthlyStatistics($start_date, $end_date) {
		$aff_stats = array_reverse($this->getDailyStatistics($start_date, $end_date));

		$result = array();

		$pt_bet = 0;
		$ag_bet = 0;
		$total_bet = 0;

		$pt_win = 0;
		$ag_win = 0;
		$total_win = 0;

		$pt_loss = 0;
		$ag_loss = 0;
		$total_loss = 0;

		$total_net_gaming = 0;
		$total_bonus = 0;
		$total_affiliate = 0;

		$stats_count = count($aff_stats);
		$counter = 0;
		$month = null;

		foreach ($aff_stats as $key => $value) {
			$counter++;

			if ($month == null) {
				$month = date('F', strtotime($value['date']));
			}

			$new_month = date('F', strtotime($value['date']));

			if ($new_month != $month) {
				$data = array(
					'pt_bet' => $pt_bet,
					'ag_bet' => $ag_bet,
					'total_bet' => $total_bet,
					'pt_win' => $pt_win,
					'ag_win' => $ag_win,
					'total_win' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_affiliates' => $total_affiliate,
					'date' => $month,
					'first_date' => date('Y-m-01', strtotime($date)),
					'last_date' => date('Y-m-t', strtotime($date)),
				);
				array_push($result, $data);

				$pt_bet = 0;
				$ag_bet = 0;
				$total_bet = 0;

				$pt_win = 0;
				$ag_win = 0;
				$total_win = 0;

				$pt_loss = 0;
				$ag_loss = 0;
				$total_loss = 0;

				$total_net_gaming = 0;
				$total_bonus = 0;
				$total_affiliate = 0;
				$month = $new_month;
			}

			$pt_bet += $value['pt_bet'];
			$ag_bet += $value['ag_bet'];
			$total_bet += $value['total_bet'];

			$pt_win += $value['pt_win'];
			$ag_win += $value['ag_win'];
			$total_win += $value['total_win'];

			$pt_loss += $value['pt_loss'];
			$ag_loss += $value['ag_loss'];
			$total_loss += $value['total_loss'];

			$total_net_gaming += $value['total_net_gaming'];
			$total_bonus += $value['total_bonus'];
			$total_affiliate = $value['total_affiliates'];
			$date = $value['date'];

			if ($counter == $stats_count) {
				$data = array(
					'pt_bet' => $pt_bet,
					'ag_bet' => $ag_bet,
					'total_bet' => $total_bet,
					'pt_win' => $pt_win,
					'ag_win' => $ag_win,
					'total_win' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_affiliates' => $total_affiliate,
					'date' => $month,
					'first_date' => date('Y-m-01', strtotime($date)),
					'last_date' => date('Y-m-t', strtotime($date)),
				);
				array_push($result, $data);
			}
		}

		return $result;
	}

	/**
	 * get Yearly Affiliate Statistics
	 *
	 * @param	array
	 * @param	int
	 * @param	int
	 * @return 	array
	 */
	public function getYearlyStatistics($start_date, $end_date) {
		$aff_stats = $this->getMonthlyStatistics($start_date, $end_date);

		$result = array();

		$pt_bet = 0;
		$ag_bet = 0;
		$total_bet = 0;

		$pt_win = 0;
		$ag_win = 0;
		$total_win = 0;

		$pt_loss = 0;
		$ag_loss = 0;
		$total_loss = 0;

		$total_net_gaming = 0;
		$total_bonus = 0;
		$total_affiliate = 0;

		$stats_count = count($aff_stats);
		$counter = 0;
		$year = null;

		foreach ($aff_stats as $key => $value) {
			$counter++;

			if ($year == null) {
				$year = date('Y', strtotime($value['first_date']));
			}

			$new_year = date('Y', strtotime($value['first_date']));

			if ($new_year != $year) {
				$data = array(
					'pt_bet' => $pt_bet,
					'ag_bet' => $ag_bet,
					'total_bet' => $total_bet,
					'pt_win' => $pt_win,
					'ag_win' => $ag_win,
					'total_win' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_affiliates' => $total_affiliate,
					'date' => $year,
					'first_date' => date('Y-01-01', strtotime($date)),
					'last_date' => date('Y-12-31', strtotime($date)),
				);
				array_push($result, $data);

				$pt_bet = 0;
				$ag_bet = 0;
				$total_bet = 0;

				$pt_win = 0;
				$ag_win = 0;
				$total_win = 0;

				$pt_loss = 0;
				$ag_loss = 0;
				$total_loss = 0;

				$total_net_gaming = 0;
				$total_bonus = 0;
				$total_affiliate = 0;
				$year = $new_year;
			}

			$pt_bet += $value['pt_bet'];
			$ag_bet += $value['ag_bet'];
			$total_bet += $value['total_bet'];

			$pt_win += $value['pt_win'];
			$ag_win += $value['ag_win'];
			$total_win += $value['total_win'];

			$pt_loss += $value['pt_loss'];
			$ag_loss += $value['ag_loss'];
			$total_loss += $value['total_loss'];

			$total_net_gaming += $value['total_net_gaming'];
			$total_bonus += $value['total_bonus'];
			$total_affiliate = $value['total_affiliates'];
			$date = $value['date'];

			if ($counter == $stats_count) {
				$data = array(
					'pt_bet' => $pt_bet,
					'ag_bet' => $ag_bet,
					'total_bet' => $total_bet,
					'pt_win' => $pt_win,
					'ag_win' => $ag_win,
					'total_win' => $total_win,
					'pt_loss' => $pt_loss,
					'ag_loss' => $ag_loss,
					'total_loss' => $total_loss,
					'total_net_gaming' => $total_net_gaming,
					'total_bonus' => $total_bonus,
					'total_affiliates' => $total_affiliate,
					'date' => $year,
					'first_date' => date('Y-01-01', strtotime($date)),
					'last_date' => date('Y-12-31', strtotime($date)),
				);
				array_push($result, $data);
			}
		}

		return $result;
	}

	/**
	 * get all pt bets under affiliate in api
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getAllPTBetsUnderAffiliate($affiliate_id, $date_from, $date_to) {
		/*$date_from = date('Y-m-d', strtotime($date_from));
		$date_to = date('Y-m-d', strtotime($date_from . '+1 day'));*/
		$result = array();

		$players = $this->getAllPlayersUnderAffiliate($affiliate_id, null, null);

		foreach ($players as $key => $value) {
			/*$data = 'customreport/getdata/reportname/PlayerStats/startdate/' . $date_from . '%2003:28:58/enddate/' . $date_to . '%2003:29:04/timeperiod/specify/playername/' . strtoupper($value['username']) . '/adminname/HLLCNYTLA/kioskname/HLLCNYTLK/entityname/HLLCNYTLE/platform/flash/reportby/day';
			$api_call = $this->ci->game_pt_api->callApi($data);*/
			$player = $this->ci->affiliate->checkPTRecords($value['username'], $date_from, $date_to);

			if (!empty($player)) {
				array_push($result, $player);
				//array_push($result, $api_call['result']);
			}
		}

		if (!empty($result)) {
			$total_bets = 0;

			foreach ($result as $key => $value) {
				foreach ($value as $key => $val) {
					$total_bets += $val['bet'];
				}
			}

			return $total_bets;
		} else {
			return 0;
		}
	}

	/**
	 * get all ag bets under affiliate in api
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getAllAGBetsUnderAffiliate($affiliate_id, $date_from, $date_to) {
		$result = array();

		$players = $this->getAllPlayersUnderAffiliate($affiliate_id, null, null);

		foreach ($players as $key => $value) {
			$player = $this->ci->affiliate->checkAGRecords($value['username'], $date_from, $date_to);

			if (!empty($player)) {
				array_push($result, $player);
			}
		}

		if (!empty($result)) {
			$total_bets = 0;

			foreach ($result as $key => $value) {
				foreach ($value as $key => $val) {
					$total_bets += $val['bet'];
				}
			}

			return $total_bets;
		} else {
			return 0;
		}
	}

	/**
	 * get all pt wins under affiliate in api
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getAllPTWinsUnderAffiliate($affiliate_id, $date_from, $date_to) {
		/*$date_from = date('Y-m-d', strtotime($date_from));
		$date_to = date('Y-m-d', strtotime($date_from . '+1 day'));*/
		$result = array();

		$players = $this->getAllPlayersUnderAffiliate($affiliate_id, null, null);

		foreach ($players as $key => $value) {
			/*$data = 'customreport/getdata/reportname/PlayerStats/startdate/' . $date_from . '%2003:28:58/enddate/' . $date_to . '%2003:29:04/timeperiod/specify/playername/' . strtoupper($value['username']) . '/adminname/HLLCNYTLA/kioskname/HLLCNYTLK/entityname/HLLCNYTLE/platform/flash/reportby/day';
			$api_call = $this->ci->game_pt_api->callApi($data);*/
			$player = $this->ci->affiliate->checkPTRecords($value['username'], $date_from, $date_to);

			if (!empty($player)) {
				array_push($result, $player);
				//array_push($result, $api_call['result']);
			}
		}

		if (!empty($result)) {
			$total_wins = 0;

			foreach ($result as $key => $value) {
				foreach ($value as $key => $val) {
					$total_wins += $val['win'];
				}
			}

			return $total_wins;
		} else {
			return 0;
		}
	}

	/**
	 * get all ag wins under affiliate in api
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getAllAGWinsUnderAffiliate($affiliate_id, $date_from, $date_to) {
		$result = array();

		$players = $this->getAllPlayersUnderAffiliate($affiliate_id, null, null);

		foreach ($players as $key => $value) {
			$player = $this->ci->affiliate->checkAGRecords($value['username'], $date_from, $date_to);

			if (!empty($player)) {
				array_push($result, $player);
			}
		}

		if (!empty($result)) {
			$total_wins = 0;

			foreach ($result as $key => $value) {
				foreach ($value as $key => $val) {
					$total_wins += $val['win'];
				}
			}

			return $total_wins;
		} else {
			return 0;
		}
	}

	/**
	 * get all pt loss under affiliate in api
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getAllPTLossUnderAffiliate($affiliate_id, $date_from, $date_to) {
		/*$date_from = date('Y-m-d', strtotime($date_from));
		$date_to = date('Y-m-d', strtotime($date_from . '+1 day'));*/
		$result = array();

		$players = $this->getAllPlayersUnderAffiliate($affiliate_id, null, null);

		foreach ($players as $key => $value) {
			/*$data = 'customreport/getdata/reportname/PlayerStats/startdate/' . $date_from . '%2003:28:58/enddate/' . $date_to . '%2003:29:04/timeperiod/specify/playername/' . strtoupper($value['username']) . '/adminname/HLLCNYTLA/kioskname/HLLCNYTLK/entityname/HLLCNYTLE/platform/flash/reportby/day';
			$api_call = $this->ci->game_pt_api->callApi($data);*/
			$player = $this->ci->affiliate->checkPTRecords($value['username'], $date_from, $date_to);

			if (!empty($player)) {
				array_push($result, $player);
				//array_push($result, $api_call['result']);
			}
		}

		if (!empty($result)) {
			$total_loss = 0;

			foreach ($result as $key => $value) {
				foreach ($value as $key => $val) {
					$total_loss += $val['loss'];
				}
			}

			return $total_loss;
		} else {
			return 0;
		}
	}

	/**
	 * get all ag loss under affiliate in api
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getAllAGLossUnderAffiliate($affiliate_id, $date_from, $date_to) {
		$result = array();

		$players = $this->getAllPlayersUnderAffiliate($affiliate_id, null, null);

		foreach ($players as $key => $value) {
			$player = $this->ci->affiliate->checkAGRecords($value['username'], $date_from, $date_to);

			if (!empty($player)) {
				array_push($result, $player);
			}
		}

		if (!empty($result)) {
			$total_loss = 0;

			foreach ($result as $key => $value) {
				foreach ($value as $key => $val) {
					$total_loss += $val['loss'];
				}
			}

			return $total_loss;
		} else {
			return 0;
		}
	}

	/**
	 * get all players id under affiliate per date
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getPlayerStatistics($affiliate_id, $start_date, $end_date) {
		$players = $this->getAllPlayersUnderAffiliate($affiliate_id, null, null);
		$result = array();

		foreach ($players as $key => $value) {
			$data = $this->ci->affiliate->getPlayerStatistics($value['playerId'], $start_date, $end_date);

			if (!empty($data)) {
				array_push($result, $data);
			}
		}

		return $result;
	}
	/* end of Affiliate Stats */

	/* traffic stats */

	/**
	 * get all players ids under affiliate in players table
	 *
	 * @param	int
	 * @return	array
	 */
	public function getAllPlayerIdsUnderAffiliate($affiliate_id) {
		$result = $this->ci->affiliate->getAllPlayersUnderAffiliate($affiliate_id, null, null);
		$ids = null;

		foreach ($result as $key => $value) {
			if (empty($ids)) {
				$ids = $value['playerId'];
			} else {
				$ids = $ids . "," . $value['playerId'];
			}
		}

		return $ids;
	}

	/**
	 * get all players under affiliate in players tables
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getAllPlayersUnderAffiliate($affiliate_id, $date_from, $date_to) {
		return $this->ci->affiliate->getAllPlayersUnderAffiliate($affiliate_id, $date_from, $date_to);
	}

	/**
	 * get registered affiliates
	 *
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getRegisteredAffiliate($date_from, $date_to) {
		return $this->ci->affiliate->getRegisteredAffiliate($date_from, $date_to);
	}

	/**
	 * get all deposit of players under affiliate in walletaccount table
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getAllPlayersDepositUnderAffiliate($affiliate_id, $date_from, $date_to) {
		return $this->ci->affiliate->getAllPlayersDepositUnderAffiliate($affiliate_id, $date_from, $date_to);
	}

	/**
	 * get all withdraw of players under affiliate in walletaccount table
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getAllPlayersWithdrawUnderAffiliate($affiliate_id, $date_from, $date_to) {
		return $this->ci->affiliate->getAllPlayersWithdrawUnderAffiliate($affiliate_id, $date_from, $date_to);
	}

	/**
	 * insert traffic stats
	 *
	 * @param	array
	 * @return	void
	 */
	public function insertTrafficStats($data) {
		$this->ci->affiliate->insertTrafficStats($data);
	}

	/**
	 * get traffic stats of players under affiliate
	 *
	 * @param	int
	 * @param	int
	 * @param	array
	 * @return	array
	 */
	public function getTrafficStats($limit, $offset, $sort) {
		return $this->ci->affiliate->getTrafficStats($limit, $offset, $sort);
	}

	/**
	 * search traffic stats of players under affiliate
	 *
	 * @param	int
	 * @param	int
	 * @param	array
	 * @return	array
	 */
	public function searchTrafficStats($limit, $offset, $search) {
		return $this->ci->affiliate->searchTrafficStats($limit, $offset, $search);
	}

	/**
	 * get players by TrafficId
	 *
	 * @param	int
	 * @return	array
	 */
	public function getPlayers($traffic_id) {
		$players = $this->ci->affiliate->getTrafficById($traffic_id);
		$players = explode(',', $players['playerIds']);
		$result = array();

		foreach ($players as $key => $value) {
			$res = $this->ci->affiliate->getPlayers($value);

			array_push($result, $res);
		}

		return $result;
	}

	/**
	 * get players deposit
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getPlayerDeposit($player_id, $start_date, $end_date) {
		$result = $this->ci->affiliate->getPlayerDeposit($player_id, $start_date, $end_date);

		if (empty($result['amount'])) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * get players withdrawal
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getPlayerWithdrawal($player_id, $start_date, $end_date) {
		$result = $this->ci->affiliate->getPlayerWithdrawal($player_id, $start_date, $end_date);

		if (empty($result['amount'])) {
			return 0;
		} else {
			return $result['amount'];
		}
	}

	/**
	 * get players total bets
	 *
	 * @param	array
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getPlayerTotalBets($player, $start_date, $end_date) {
		$pt_bets = $this->getPlayerPTBet($player['username'], $start_date, $end_date);
		$ag_bets = $this->getPlayerAGBet($player['username'], $start_date, $end_date);

		$total_bets = $pt_bets + $ag_bets;

		return $total_bets;
	}

	/**
	 * get players pt bets
	 *
	 * @param	array
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getPlayerPTBet($player_name, $start_date, $end_date) {
		$player = $this->ci->affiliate->checkPTRecords($player_name, $start_date, $end_date);

		if (!empty($player)) {
			$total_bets = 0;

			foreach ($player as $key => $value) {
				$total_bets += $value['bets'];
			}

			return $total_bets;
		} else {
			return 0;
		}
	}

	/**
	 * get players ag bets
	 *
	 * @param	array
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getPlayerAGBet($username, $start_date, $end_date) {
		$player = $this->ci->affiliate->checkAGRecords($username, $start_date, $end_date);

		if (!empty($player)) {
			$total_bets = 0;

			foreach ($player as $key => $val) {
				$total_bets += $val['betAmount'];
			}

			return $total_bets;
		} else {
			return 0;
		}
	}

	/**
	 * get players total wins
	 *
	 * @param	array
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getPlayerTotalWins($player, $start_date, $end_date) {
		$pt_wins = $this->getPlayerPTWins($player['username'], $start_date, $end_date);
		$ag_wins = $this->getPlayerAGWins($player['username'], $start_date, $end_date);

		$total_wins = $pt_wins + $ag_wins;

		return $total_wins;
	}

	/**
	 * get players pt wins
	 *
	 * @param	array
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getPlayerPTWins($player_name, $start_date, $end_date) {
		$player = $this->ci->affiliate->checkPTRecords($player_name, $start_date, $end_date);

		if (!empty($player)) {
			$total_wins = 0;

			foreach ($player as $key => $val) {
				$total_wins += $val['wins'];
			}

			return $total_wins;
		} else {
			return 0;
		}
	}

	/**
	 * get players ag wins
	 *
	 * @param	array
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getPlayerAGWins($player_id, $start_date, $end_date) {
		$player = $this->ci->affiliate->checkAGRecords($player_id, $start_date, $end_date);

		if (!empty($player)) {
			$total_wins = 0;

			foreach ($player as $key => $val) {
				if ($val['netAmount'] > 0) {
					$total_wins += $val['netAmount'];
				}
			}

			return $total_wins;
		} else {
			return 0;
		}
	}

	/**
	 * get players total loss
	 *
	 * @param	array
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getPlayerTotalLoss($player, $start_date, $end_date) {
		$pt_loss = $this->getPlayerPTLoss($player['username'], $start_date, $end_date);
		$ag_loss = $this->getPlayerAGLoss($player['username'], $start_date, $end_date);

		$total_loss = $pt_loss + $ag_loss;

		return $total_loss;
	}

	/**
	 * get players pt loss
	 *
	 * @param	array
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getPlayerPTLoss($player_name, $start_date, $end_date) {
		$player = $this->ci->affiliate->checkPTRecords($player_name, $start_date, $end_date);

		if (!empty($player)) {
			$total_loss = 0;

			foreach ($player as $key => $val) {
				$total_loss += $val['netloss'];
			}

			return $total_loss;
		} else {
			return 0;
		}
	}

	/**
	 * get players ag loss
	 *
	 * @param	array
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getPlayerAGLoss($player_id, $start_date, $end_date) {
		$player = $this->ci->affiliate->checkAGRecords($player_id, $start_date, $end_date);

		if (!empty($player)) {
			$total_loss = 0;

			foreach ($player as $key => $val) {
				if ($val['netAmount'] < 0) {
					$total_loss += $val['netAmount'];
				}
			}

			return $total_loss;
		} else {
			return 0;
		}
	}

	/**
	 * get players total bonus
	 *
	 * @param	array
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getPlayerTotalBonus($player_id, $start_date, $end_date) {
		$player = $this->getTotalBonuses($player_id, $start_date, $end_date);

		if (!empty($player)) {
			return $player;
		} else {
			return 0;
		}
	}

	/* end of traffic stats */

	/* Save Earnings */

	/**
	 * get last closing balance daily
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @param	string
	 * @return	array
	 */
	public function getLastClosingBalance($affiliate_id, $start_yesterday_date, $end_yesterday_date, $in) {
		$result = $this->ci->affiliate->getLastClosingBalance($affiliate_id, $start_yesterday_date, $end_yesterday_date, $in);

		if (empty($result) || $result == null) {
			return 0;
		} else {
			return $result['closing_balance'];
		}
	}

	/**
	 * get earnings today
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getEarningsToday($affiliate_id, $start_date, $end_date) {
		$result = $this->ci->affiliate->getEarningsToday($affiliate_id, $start_date, $end_date);

		if (empty($result) || $result == null) {
			return 0;
		} else {
			return $result['total_net_gaming'];
		}
	}

	/**
	 * get active players monthly
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @param	int
	 * @return	int
	 */
	public function getActivePlayersThisMonth($affiliate_id, $start_date, $end_date, $game_id) {
		$players = $this->getAllPlayersUnderAffiliate($affiliate_id, null, null);

		if (!empty($players)) {
			$players_ids = null;

			foreach ($players as $key => $value) {
				if ($players_ids == null) {
					$players_ids = "'" . $value['playerId'] . "'";
				} else {
					$players_ids += ", '" . $value['playerId'] . "'";
				}

			}

			$active_players = $this->ci->affiliate->getActivePlayersThisMonth($players_ids, $start_date, $end_date, $game_id);

			if (empty($active_players) || $active_players == null) {
				//OG-598,because outter function is justifize by array size, so must return null
				return null;
			} else {
				$result = array();

				foreach ($active_players as $key => $value) {
					array_push($result, $value['playerId']);
				}

				return $result;
			}
		}
		//OG-598,because outter function is justifize by array size, so must return null
		return null;
	}

	/**
	 * get last closing balance monthly
	 *
	 * @param	int
	 * @param	date
	 * @param	string
	 * @return	array
	 */
	public function getMonthlyLastClosingBalance($affiliate_id, $start_date, $in) {
		$result = $this->ci->affiliate->getMonthlyLastClosingBalance($affiliate_id, $start_date, $in);

		if (empty($result) || $result == null) {
			return 0;
		} else {
			return $result['closing_balance'];
		}
	}

	/**
	 * get monthly earnings
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @return	array
	 */
	public function getMonthlyEarningsToday($affiliate_id, $start_date, $end_date) {
		$result = $this->ci->affiliate->getMonthlyEarningsToday($affiliate_id, $start_date, $end_date);

		if (empty($result) || $result == null) {
			return 0;
		} else {
			return $result['total_earnings'];
		}
	}

	/**
	 * get monthly earnings per game
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 * @param	string
	 * @return	array
	 */
	public function getMonthlyEarningsPerGame($affiliate_id, $start_date, $end_date, $game_name) {
		$result = $this->ci->affiliate->getMonthlyEarningsPerGame($affiliate_id, $start_date, $end_date, $game_name);

		return $result;
	}

	/**
	 * updaye affiliatemonthlyearnings daily
	 *
	 * @param	int
	 * @param	date
	 * @param	date
	 */
	public function updateDailyEarnings($affiliate_id, $start_date, $end_date) {
		$this->ci->affiliate->updateDailyEarnings($affiliate_id, $start_date, $end_date);
	}

	/**
	 * insert affiliatemonthlyearnings
	 *
	 * @param	array
	 */
	public function insertAffiliateMonthlyEarnings($data) {
		$this->ci->affiliate->insertAffiliateMonthlyEarnings($data);
	}

	/**
	 * update monthly earnings
	 *
	 * @param	array
	 * @param	int
	 * @return	array
	 */
	public function updateMonthlyEarnings($data, $earnings_id) {
		return $this->ci->affiliate->updateMonthlyEarnings($data, $earnings_id);
	}

	/* end of Save Earnings */

	/**
	 * add payment
	 *
	 * @param	array
	 * @param	int
	 */
	public function addPayment($data) {
		$this->ci->affiliate->addPayment($data);
	}

	/**
	 * edit payment bank info
	 *
	 * @param	int
	 * @param	int
	 */
	public function editPaymentInfo($data, $payment_id) {
		$this->ci->affiliate->editPaymentInfo($data, $payment_id);
	}

	/**
	 * delete payment bank info
	 *
	 * @param	int
	 */
	public function deletePaymentInfo($payment_id) {
		$this->ci->affiliate->deletePaymentInfo($payment_id);
	}

	/**
	 * get all payment method of affiliate
	 *
	 * @param	int
	 * @param	int
	 */
	public function getPaymentByPaymentId($affiliate_payment_id) {
		return $this->ci->affiliate->getPaymentByPaymentId($affiliate_payment_id);
	}

}

/* End of file affiliate_manager.php */
/* Location: ./application/libraries/affiliate_manager.php */