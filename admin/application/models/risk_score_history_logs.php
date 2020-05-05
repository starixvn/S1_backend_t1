<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get kyc Status
 * * Get player kyc Status
 * * Get/insert/update/ player kyc status
 *
 * @category Player Risk Score History Logs
 * @version 1.8.10
 * @author Jhunel L. Ebero
 * @copyright 2013-2019 TripleOneTech
 */
class risk_score_history_logs extends BaseModel {
	const CACHE_TTL = 600; # 10 minutes
	const CACHE_TTL_LONG = 3600; # 1 hour

	const zero_total = 0;
	const R1 = 'R1';
	const R2 = 'R2';
	const R3 = 'R3';
	const R4 = 'R4';
	const R5 = 'R5';
	const R6 = 'R6';
	const R7 = 'R7';
	const RC = 'RC';//risk score

	const RISK_SCORE_ALL = 'all';

	public function __construct() {
		parent::__construct();
		$this->load->model(array('player_model','risk_score_model','player','system_feature','transactions'));
	}

	protected $tableName = 'risk_score_history_logs';

	public function update_risk_score_logs($playerId,$risk_score_category,$to_data,$total_score = null){
		$from_data = $this->get_latest_record_log_by_category($playerId,$risk_score_category);
		$from_total_score = $this->get_latest_record_total_score($playerId);
		
		$risk_scores_chart = $this->risk_score_model->getRiskScoreInfo(self::RC);
		$risk_scores_chart_rules = null;
		if(!empty($risk_scores_chart) && isset($risk_scores_chart['rules'])){
			$risk_scores_chart_rules =  $risk_scores_chart['rules'];
		}

		if(!empty($from_data)){
			$is_data_change = false;
			if(isset($from_data['result_change_to']) && isset($to_data['generated_result'])){
				if($from_data['result_change_to'] != $to_data['generated_result']){
					$is_data_change = true;
				}
			}

			if(isset($from_data['score_to']) && isset($to_data['score'])){
				if($from_data['score_to'] != $to_data['score']){
					$is_data_change = true;
				}
			}

			if($is_data_change){
				$data = array(
					'player_id' => $playerId,
					'risk_score_category' => $risk_score_category,
					'result_change_from' => (isset($from_data['result_change_to'])) ? $from_data['result_change_to'] : lang('N/A',1),
					'result_change_to' => (isset($to_data['generated_result']) ? $to_data['generated_result'] : lang('N/A',1)),
					'score_from' => (isset($from_data['score_to'])) ? $from_data['score_to'] : 0,
					'score_to' => (isset($to_data['score']) ? $to_data['score'] : 0),
					'total_score_from' => (isset($from_total_score['total_score_to']) ? $from_total_score['total_score_to'] : 0),
					'total_score_to' => ($total_score) ? $total_score : 0,
					'risk_score_level_from' => (isset($from_data['risk_score_level_to'])) ? $from_data['risk_score_level_to'] : lang('N/A',1),
					'risk_score_level_to' => ($risk_scores_chart_rules)? $this->risk_score_model->calc_risk_score_with_formula($total_score, $risk_scores_chart_rules): lang('N/A',1),
					'created_at' => $this->utils->getNowForMysql()
				);
				$this->insert_new_logs($data);
			}
		} else {
			$data = array(
				'player_id' => $playerId,
				'risk_score_category' => $risk_score_category,
				'result_change_from' => (isset($from_data['result_change_from'])) ? $from_data['result_change_from'] : lang('N/A',1),
				'result_change_to' => (isset($to_data['generated_result']) ? $to_data['generated_result'] : lang('N/A',1)),
				'score_from' => (isset($from_data['score_from'])) ? $from_data['score_from'] : 0,
				'score_to' => (isset($to_data['score']) ? $to_data['score'] : 0),
				'total_score_from' => (isset($from_total_score['total_score_to']) ? $from_total_score['total_score_to'] : 0),
				'total_score_to' => ($total_score) ? $total_score : null,
				'risk_score_level_from' => (isset($from_data['risk_score_level_from'])) ? $from_data['risk_score_level_from'] : lang('N/A',1),
				'risk_score_level_to' => ($risk_scores_chart_rules)? $this->risk_score_model->calc_risk_score_with_formula($total_score, $risk_scores_chart_rules): lang('N/A',1),
				'created_at' => $this->utils->getNowForMysql()
			);
			$this->insert_new_logs($data);
		}
		//var_dump($from_data);die();
	}

	public function get_latest_record_log_by_category($playerId,$risk_score_category){
		$this->db->select('*');
		$this->db->where('risk_score_category', $risk_score_category);
		$this->db->where('player_id', (int)$playerId);
		$this->db->order_by('id', 'desc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowArray($qry);
	}

	public function get_latest_record_total_score($playerId){
		$this->db->select('id, total_score_from, total_score_to');
		$this->db->where('player_id', (int)$playerId);
		$this->db->order_by('id', 'desc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowArray($qry);
	}

	public function insert_new_logs($data){
		return $this->insertData($this->tableName, $data);
	}

	public function update_last_data_total_score($playerId,$total_score){
		$last_record = $this->get_latest_record_total_score($playerId);
		$risk_scores_chart = $this->risk_score_model->getRiskScoreInfo(self::RC);
		$risk_scores_chart_rules = null;
		if(!empty($risk_scores_chart) && isset($risk_scores_chart['rules'])){
			$risk_scores_chart_rules =  $risk_scores_chart['rules'];
		}
		if(!empty($last_record)){
			if(isset($last_record['total_score_to']) && isset($last_record['id'])){
				if($last_record['total_score_to'] != $total_score){
					$this->db->where('id', $last_record['id'] );
					$check = $this->db->update($this->tableName,
						array(
							'total_score_to' => $total_score,
							'risk_score_level_to' => ($risk_scores_chart_rules)? $this->risk_score_model->calc_risk_score_with_formula($total_score, $risk_scores_chart_rules): null
						)
					);
				}
			}
		}
		
	}

}