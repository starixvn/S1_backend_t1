<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Levels
 *
 * This model represents levels data. It operates the following tables:
 * - level
 *
 * @author	Kaiser Dapar
 */

class Levels extends CI_Model
{

	function __construct() {
		parent::__construct();
	}

	public function createLevel($data) {
		return $this->db->insert('mkt_level', $data);
	}

	public function retrieveLevels($data = null, $offset = 0, $limit = DEFAULT_ITEMS_PER_PAGE) {
 		return $this->db->order_by('levelName', 'asc')->get_where('mkt_level', $data, $limit, $offset)->result();
	}

	public function retrieveLevelCount($data = null) {
 		return $this->db->count_all('mkt_level');
	}

	public function deleteLevel($levelId) {
		$this->db->delete('mkt_level', array('levelId' => $levelId));
		return $this->db->affected_rows();
	}

	public function deleteSelectedLevels($levelIds) {
		$this->db->where_in('levelId', $levelIds)->delete('mkt_level');
		return $this->db->affected_rows();
	}

	public function deleteAllLevels() {
		$this->db->empty_table('mkt_level');
		return $this->db->affected_rows();
	}

}

/* End of file levels.php */
/* Location: ./application/models/levels.php */