<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Marketing_Functions
 *
 * Marketing_Functions library for FHI OG System
 *
 * @author Kaiser Dapar (kaiserdapar@gmail.com)
 *
 */
class Marketing_functions {

	##################################################################################################################
	# CONSTRUCTOR
	##################################################################################################################
	function __construct() {
		$this->ci =& get_instance();
		$this->ci->load->model(array('users','currencies','games'));
	}

	##################################################################################################################
	# PROMO FUNCTIONS
	##################################################################################################################

		##################################################################################################################
		# BASIC CRUD
		##################################################################################################################
		// function createPromo($data) {
		// 	$result = $this->ci->promos->createPromo($data);
		// 	return $result;
		// }

		// function retrievePromo($promoId) {
		// 	$result = $this->ci->promos->retrievePromo($promoId);
		// 	return $result;
		// }

		// function retrievePromos($offset = 0, $datetime = null, $timezone = null, $type = null, $nth = null, $game = null, $level = null, $amount = null, $id = null) {

		// 	$result = array();

		// 	try {

		// 		$where  = "1 = 1 \n";

		// 		if ($datetime) {
		// 			if ($datetime == 'now') {
		// 				$datetime = 'UTC_TIMESTAMP';
		// 			} else if ( ! $timezone) {
		// 				$datetime = sprintf("'%s'", $datetime);
		// 			} else {
		// 				$datetime = sprintf("CONVERT_TZ('%s','%s','+00:00')", $datetime, $timezone);
		// 			}

		// 			$where .= "AND DATE({$datetime}) BETWEEN promoStartDate AND promoEndDate \n"
		// 			  .	"AND \n"
		// 			  . "( \n"
		// 			  .	"	( \n"
		// 			  .	"		promoStartTime > promoEndTime \n"
		// 			  . "		AND \n"
		// 			  . "		( \n"
		// 			  .	"  			TIME({$datetime}) BETWEEN promoStartTime AND MAKETIME(0,0,0) OR \n"
		// 			  .	"  			TIME({$datetime}) BETWEEN MAKETIME(0,0,0) AND promoEndTime \n"
		// 			  .	"		) \n"
		// 			  . "	) \n"
		// 			  . "	OR \n"
		// 			  . "	( \n"
		// 			  .	"		promoStartTime < promoEndTime \n"
		// 			  . "		AND \n"
		// 			  . "		TIME({$datetime}) BETWEEN promoStartTime AND promoEndTime \n"
		// 			  .	"	) \n"
		// 			  .	") \n"
		// 			  . "AND \n"
		// 			  . "( \n"
		// 			  .	"	(promoFrequency = 0 AND (DAYOFYEAR(DATE({$datetime})) - DAYOFYEAR(promoStartDate)) % promoInterval = 0 AND (promoDaysOfWeek & POW(2,(DAYOFWEEK(DATE({$datetime}))-1))) > 0) OR \n"
		// 			  .	"	(promoFrequency = 1 AND (WEEKOFYEAR(DATE({$datetime})) - DAYOFYEAR(promoStartDate)) % promoInterval = 0 AND (promoDaysOfWeek & POW(2,(DAYOFWEEK(DATE({$datetime}))-1))) > 0) OR \n"
		// 			  .	"	(promoFrequency = 2 AND (MONTH(DATE({$datetime})) - MONTH(promoStartDate)) % promoInterval = 0 AND promoDayOfMonth = DAY(DATE({$datetime}))) OR \n"
		// 			  .	"	(promoFrequency = 4 AND (MONTH(DATE({$datetime})) - MONTH(promoStartDate)) % promoInterval = 0 AND promoDayOfMonth = DAY(DATE({$datetime})) AND  promoMonth = MONTH(DATE({$datetime}))) \n"
		// 			  .	") \n";
		// 		}


		// 		if ($id != null) {
		// 			$where .= " AND promoId = {$id} \n";
		// 		}

		// 		if ($type != null) {
		// 			$where .= " AND promoType = {$type} \n";
		// 		}

		// 		if ($nth != null) {
		// 			$where .= " AND promoNthDeposit = {$nth} \n";
		// 		}

		// 		if ($game != null) {
		// 			$where .= " AND promoId IN (SELECT promoId FROM mkt_promogame WHERE gameId = {$game} GROUP BY promoId HAVING COUNT(1) > 0) \n";
		// 		}

		// 		if ($level != null) {
		// 			$where .= " AND promoId IN (SELECT promoId FROM mkt_promolevel WHERE levelId = {$level} GROUP BY promoId HAVING COUNT(1) > 0) \n";
		// 		}

		// 		if ($amount != null) {
		// 			$where .= " AND promoId IN (SELECT promoId FROM mkt_promorule WHERE promoRuleInValue <= {$amount} GROUP BY promoId HAVING COUNT(1) > 0) \n";
		// 		}

		// 		$result = $this->ci->promos->retrievePromos($offset, $where);

		// 	} catch (Exception $e) {
		// 		log_message('error', $e->getMessage());
		// 	}

		// 	return $result;
		// }

		// function retrievePromoCount($datetime = null, $timezone = null, $type = null, $nth = null, $game = null, $level = null, $amount = null, $id = null) {

		// 	$result = 0;

		// 	try {

		// 		$where  = "1 = 1 \n";

		// 		if ($datetime) {
		// 			if ($datetime == 'now') {
		// 				$datetime = 'UTC_TIMESTAMP';
		// 			} else if ( ! $timezone) {
		// 				$datetime = sprintf("'%s'", $datetime);
		// 			} else {
		// 				$datetime = sprintf("CONVERT_TZ('%s','%s','+00:00')", $datetime, $timezone);
		// 			}

		// 			$where .= "AND DATE({$datetime}) BETWEEN promoStartDate AND promoEndDate \n"
		// 			  .	"AND \n"
		// 			  . "( \n"
		// 			  .	"	( \n"
		// 			  .	"		promoStartTime > promoEndTime \n"
		// 			  . "		AND \n"
		// 			  . "		( \n"
		// 			  .	"  			TIME({$datetime}) BETWEEN promoStartTime AND MAKETIME(0,0,0) OR \n"
		// 			  .	"  			TIME({$datetime}) BETWEEN MAKETIME(0,0,0) AND promoEndTime \n"
		// 			  .	"		) \n"
		// 			  . "	) \n"
		// 			  . "	OR \n"
		// 			  . "	( \n"
		// 			  .	"		promoStartTime < promoEndTime \n"
		// 			  . "		AND \n"
		// 			  . "		TIME({$datetime}) BETWEEN promoStartTime AND promoEndTime \n"
		// 			  .	"	) \n"
		// 			  .	") \n"
		// 			  . "AND \n"
		// 			  . "( \n"
		// 			  .	"	(promoFrequency = 0 AND (DAYOFYEAR(DATE({$datetime})) - DAYOFYEAR(promoStartDate)) % promoInterval = 0 AND (promoDaysOfWeek & POW(2,(DAYOFWEEK(DATE({$datetime}))-1))) > 0) OR \n"
		// 			  .	"	(promoFrequency = 1 AND (WEEKOFYEAR(DATE({$datetime})) - DAYOFYEAR(promoStartDate)) % promoInterval = 0 AND (promoDaysOfWeek & POW(2,(DAYOFWEEK(DATE({$datetime}))-1))) > 0) OR \n"
		// 			  .	"	(promoFrequency = 2 AND (MONTH(DATE({$datetime})) - MONTH(promoStartDate)) % promoInterval = 0 AND promoDayOfMonth = DAY(DATE({$datetime}))) OR \n"
		// 			  .	"	(promoFrequency = 4 AND (MONTH(DATE({$datetime})) - MONTH(promoStartDate)) % promoInterval = 0 AND promoDayOfMonth = DAY(DATE({$datetime})) AND  promoMonth = MONTH(DATE({$datetime}))) \n"
		// 			  .	") \n";
		// 		}


		// 		if ($id != null) {
		// 			$where .= " AND promoId = {$id} \n";
		// 		}

		// 		if ($type != null) {
		// 			$where .= " AND promoType = {$type} \n";
		// 		}

		// 		if ($nth != null) {
		// 			$where .= " AND promoNthDeposit = {$nth} \n";
		// 		}

		// 		if ($game != null) {
		// 			$where .= " AND promoId IN (SELECT promoId FROM mkt_promogame WHERE gameId = {$game} GROUP BY promoId HAVING COUNT(1) > 0) \n";
		// 		}

		// 		if ($level != null) {
		// 			$where .= " AND promoId IN (SELECT promoId FROM mkt_promolevel WHERE levelId = {$level} GROUP BY promoId HAVING COUNT(1) > 0) \n";
		// 		}

		// 		if ($amount != null) {
		// 			$where .= " AND promoId IN (SELECT promoId FROM mkt_promorule WHERE promoRuleInValue <= {$amount} GROUP BY promoId HAVING COUNT(1) > 0) \n";
		// 		}

		// 		$result = $this->ci->promos->retrievePromoCount($where);

		// 	} catch (Exception $e) {
		// 		log_message('error', $e->getMessage());
		// 	}

		// 	return $result;
		// }

		/**
	     * Will get all promo
	     *
	     * @return  array
	     */
	 //    function getAllPromo() {
	 //        return $this->ci->promos->getAllPromo();
	 //    }

		// function updatePromo($data) {
		// 	$result = $this->ci->promos->updatePromo($data);
		// 	return $result;
		// }

		// function deletePromo($promoId) {
		// 	$result = $this->ci->promos->deletePromo($promoId);
		// 	return $result;
		// }

		// function deleteSelectedPromos($promoIds) {
		// 	$result = $this->ci->promos->deleteSelectedPromos($promoIds);
		// 	return $result;
		// }

		// function deleteAllPromos() {
		// 	$result = $this->ci->promos->deleteAllPromos();
		// 	return $result;
		// }

	##################################################################################################################
	# SUPPORT
	##################################################################################################################
		##################################################################################################################
		# CURRENCY FUNCTIONS
		##################################################################################################################

		function retrieveCurrencies($offset = 0) {
			$data = null;
			$result = $this->ci->currencies->retrieveCurrencies($data, $offset);
			return $result;
		}

		function retrieveCurrencyCount() {
			$result = $this->ci->currencies->retrieveCurrencyCount();
			return $result;
		}

		function createCurrency($data) {
			$result = $this->ci->currencies->createCurrency($data);
			return $result;
		}

		function deleteCurrency($currencyId) {
			$result = $this->ci->currencies->deleteCurrency($currencyId);
			return $result;
		}

		function deleteSelectedCurrencies($currencyIds) {
			$result = $this->ci->currencies->deleteSelectedCurrencies($currencyIds);
			return $result;
		}

		function deleteAllCurrencies() {
			$result = $this->ci->currencies->deleteAllCurrencies();
			return $result;
		}

		##################################################################################################################
		# GAMES FUNCTIONS
		##################################################################################################################

		function retrieveGames($offset = 0) {
			$data = null;
			$result = $this->ci->games->retrieveGames($data, $offset);
			return $result;
		}

		function retrieveGameCount() {
			$result = $this->ci->games->retrieveGameCount();
			return $result;
		}

		function createGame($data) {
			$result = $this->ci->games->createGame($data);
			return $result;
		}

		function deleteGame($promoId) {
			$result = $this->ci->games->deleteGame($promoId);
			return $result;
		}

		function deleteSelectedGames($promoIds) {
			$result = $this->ci->games->deleteSelectedGames($promoIds);
			return $result;
		}

		function deleteAllGames() {
			$result = $this->ci->games->deleteAllGames();
			return $result;
		}

}

/* End of file marketing_functions.php */
/* Location: ./application/libraries/marketing_functions.php */