<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Currencies
 *
 * This model represents currencies data. It operates the following tables:
 * - currency
 *
 * @category Player Management
 * @version 5.02.02
 * @copyright 2013-2016 TripleOneTech
 * @author	Kaiser Dapar
 */

class Currencies extends BaseModel
{

	public function __construct() {
		parent::__construct();
	}

	/**
	 * get active currency code
	 * @return code
	 */
	public function getActiveCurrencyCode() {
		if($this->utils->isEnabledMDB()){
			return $this->utils->getActiveCurrencyInfoOnMDB()['code'];
		}

		$this->db->select('currencyCode')->from('currency');
		$this->db->where('status', 0)->limit(1);
		return $this->runOneRowOneField('currencyCode');
	}

	public function getActiveCurrencyInfo() {
		$this->db->from('currency')->where('status',"0");

		$row=$this->runOneRowArray();
		$result=[];

		if(!empty($row)){
			$result['currency_name']=$row['currencyName'];
			$result['currency_short_name']=$row['currencyShortName'];
			$result['currency_code']=$row['currencyCode'];
			$result['symbol']=$row['currencySymbol'];
			$result['currency_decimals']=$this->utils->getConfig('default_currency_decimals');
			$result['currency_dec_point']=$this->utils->getConfig('default_currency_dec_point');
			$result['currency_thousands_sep']=$this->utils->getConfig('default_currency_thousands_sep');
		}

		return $result;
	}
}

/* End of file currencies.php */
/* Location: ./application/models/currencies.php */