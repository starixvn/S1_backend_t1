<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * daily balance for main-wallet, sub-wallet and total
 * http://ogdev.ddns.net:8090/display/OG/Constants
 *
 */
class Daily_currency extends BaseModel {

	protected $tableName = 'daily_currency';

	function __construct() {
		parent::__construct();
	}

	/**
	 * overview : call currency api and save to table
	 * Api limit: USD to any currency only
	 */
	public function generateDailyCurrencyRate($date = null){
		$url =  empty($date) ? $this->utils->getConfig('currency_api_url') : $this->utils->getConfig('currency_api_historical');
		$date = empty($date) ? $this->utils->getTodayForMysql() :  $date;
		$method = "GET";
		$params = array(
			"currencies" => $this->utils->getConfig('currency_api_currencies'),
			"source" => $this->utils->getConfig('currency_api_source'),
			"access_key" => $this->utils->getConfig('currency_api_access_key'),
			"date" => $date
		);	
		$isExist = $this->isRowIdAlreadyExists($date);
		if (!$isExist) {
			// print_r('not exist');exit();
			$result = $this->utils->callHttp($url, $method, $params);
			$api_result = json_decode($result[1]);
			if($api_result->success){
				$currencyData = (array)json_decode($result[1])->quotes;
				if(!empty($currencyData)){
					foreach ($currencyData as $key => $value) {
						$base = $this->utils->getConfig('currency_api_source');
						$target = trim($key,$base);
						$data = array(
					        'current_rate_date'	=>	$date,
					        'base_currency'		=>	$base,
					        'target_currency'	=>	$target,
					        'rate'				=>  $value
					    );
						$this->db->insert($this->tableName,$data);
						//generate oposite rate of any currency to USD 
						$data2 = array(
					        'current_rate_date'	=>	$date,
					        'base_currency'		=>	$target,
					        'target_currency'	=>	$base,
					        'rate'				=>  1/$value
					    );
					    $this->db->insert($this->tableName,$data2);
					}
				}
			}
			return $api_result->success;
		} else {
			// print_r(' exist');exit();
			return true;
		}
	}

	/**
	 * overview : check if have currency rate 
	 * @param  date		$dateNow
	 * @return boolean
	 */
	public function isRowIdAlreadyExists($dateNow) {
		$qry = $this->db->get_where($this->tableName, array(
			'current_rate_date' => $dateNow,
		));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : update currency rate 
	 *
	 * @param  array	$data
	 *
	 * @return boolean
	 */
	public function updateCurrencyRate($data) {
		$this->db->where('current_rate_date', $data['current_rate_date']);
		return $this->db->update($this->tableName, $data);
	}

	/**
	 * overview : update currency rate 
	 *
	 * @param  array	$data
	 *
	 * @return boolean
	 */
	public function getCurrentCurrencyRate($date,$base,$target) {
		$this->db->select('rate');
		$qry = $this->db->get_where($this->tableName, array(
			'current_rate_date' => $date,
			'base_currency' => $base,
			'target_currency' => $target,
		));
		$row = $this->getOneRow($qry);
		if ($row == null) {
			$success = $this->generateDailyCurrencyRate($date);
			if($success){
				return $this->getCurrentCurrencyRate($date,$base,$target);
			}
			return null;
		} else {
			return $row;
		}
	}
	/**
	 * overview : get currency rate by date only
	 *
	 * @param  date	$date
	 *
	 * @return rate
	 */
	public function getCurrentCurrencyRateWithNoGenerate($date,$base,$target)
	{
		$this->db->select('rate');
		$data = array(
			'current_rate_date' => $date,
			'base_currency' => $base,
			'target_currency' => $target,
		);
		$qry = $this->db->get_where($this->tableName, $data);
		$row = $this->getOneRow($qry);
		$data = array();
		if(!empty($row)){
			return $row->rate;
		} 
		return null;
	}
}

/////end of file///////