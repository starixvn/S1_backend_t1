<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Riskscore KYC Management
 * * Get/insert/update/ Riskscore KYC Chart
 *
 * @category Riskscore KYC Management
 * @version 1.8.10
 * @author Jhunel L. Ebero
 * @copyright 2013-2017 TripleOneTech
 */
class riskscore_kyc_chart_management_model extends BaseModel {
	public function __construct() {
		parent::__construct();
	}

	protected $tableName = 'riskscore_kyc_chart_management';
	private $chartCoordinateTags = array();

	public function getRiskscoreKycChart() {
		$this->db->select("*");
		$qry = $this->db->get($this->tableName);
		return $this->getMultipleRowArray($qry);
	}

	/**
	 * @category get the coordinate tag between KYC rate code and Risk Score
	 * @param string kycLvl
	 * @param string riskLvl
	 *
	 * @return array
	 */
	public function getChartCoordinateTag($kycLvl, $riskLvl){
		if(empty($this->chartCoordinateTags)) {
			$chart = $this->getRiskscoreKycChart();
			# re-index the risk score chart with 'kyc_level-risk_level'
			if(is_array($chart)){
				foreach($chart as $row) {
					$this->chartCoordinateTags[$row['kyc_level'].'::'.$row['risk_level']] = $row;
				}
			}
		}

		return array_key_exists($kycLvl.'::'.$riskLvl, $this->chartCoordinateTags) ?
			$this->chartCoordinateTags[$kycLvl.'::'.$riskLvl] : array();
	}

	public function addUpdateChart($kycLvl, $riskLvl, $tag = "X"){
		$details = $this->getChartCoordinateTag($kycLvl, $riskLvl);
		if($details){
			$this->db->where('id', $details['id']);
			$data = array(
						"tag" => $tag
					);
			$this->db->set($data);
			return $this->runAnyUpdate($this->tableName);
		} else {
			$data = array(
						"kyc_level" => $kycLvl,
						"risk_level" => $riskLvl,
						"tag" => $tag
					);
			return $this->insertData($this->tableName, $data);
		}
	}

}