<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Agency_flattening
 */
class Agency_player_details extends BaseModel {

	protected $table			= 'agency_player_details';
	protected $table_settings	= 'agency_settings';
	protected $name_base_credit_default = 'base_credit_default';
	public $base_credit_default_static = 500.0;

	function __construct() {
		$this->load->model(['agency_model']);
		parent::__construct();
	}

	public function detail_get($playerId, $agent_id) {
		$this->db->from($this->table)
			->where('playerId', $playerId)
			->where('agent_id', $agent_id);
		$res = $this->db->get()->first_row('array');

		if (empty($res) || count($res) == 0) {
			return false;
		}
		return $res;
	}

	public function base_credit_get($playerId, $agent_id) {
		$detail = $this->detail_get($playerId, $agent_id);

		if (empty($detail)) {
			return false;
		}
		return $detail['base_credit'];
	}

	public function base_credit_read($playerId, $agent_id) {
		$base_credit = $this->base_credit_get($playerId, $agent_id);
		if (empty($base_credit)) {
			$base_credit = $this->base_credit_default_get($agent_id);
		}
		if (empty($base_credit)) {
			$base_credit = $this->base_credit_default_static;
		}

		return $base_credit;
	}

	public function base_credit_store($playerId, $agent_id, $base_credit) {
		$base_credit_current = $this->base_credit_get($playerId, $agent_id);

		$update_flag = true;

		if (!$base_credit_current) {
			// Insert as new record
			$dataset = [
				'playerId' => $playerId ,
				'agent_id' => $agent_id ,
				'base_credit' => $base_credit
			];
			$this->db->insert($this->table, $dataset);
		}
		else if ($base_credit_current != $base_credit) {
			// Update
			$this->db->where('playerId', $playerId)
				->where('agent_id', $agent_id)
				->update($this->table, [ 'base_credit' => $base_credit ]);
		}
		else {
			// No update executed
			$update_flag = false;
		}

		return !$update_flag || ($this->db->affected_rows() > 0);
	}

	public function base_credit_default_get($agent_id) {
		$this->db->from($this->table_settings)
			->where('agent_id', $agent_id)
			->where('name', $this->name_base_credit_default);

		$res = $this->db->get()->first_row('array');

		if (empty($res) || count($res) == 0) {
			return false;
		}
		$base_credit_default = floatval($res['value']);
		return $base_credit_default;
	}

	public function base_credit_default_store($agent_id, $base_credit_default_new) {
		$base_credit_default_current = $this->base_credit_default_get();

		$update_flag = true;
		if (!$base_credit_default_current) {
			// Insert as a new record
			$dataset = [
				'name' => $this->name_base_credit_default ,
				'value' => $base_credit_default_new ,
				'agent_id' => $agent_id
			];
			$this->db->insert($this->table_settings, $dataset);
		}
		else if ($base_credit_default_current != $base_credit_default_new) {
			// Update
			$this->db->where('agent_id', $agent_id)
				->where('name', $this->name_base_credit_default)
				->update($this->table_settings, [ 'value' => $base_credit_default_new]);
		}
		else {
			// No update executed
			$update_flag = false;
		}

		return !$update_flag || $this->db->affected_rows() > 0;
	}

}

/* End of file agency_player_details.php */
/* Location: ./application/models/agency_player_details.php */
