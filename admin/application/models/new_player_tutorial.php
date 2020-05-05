<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * New Player Tutorial
 *
 * This model represents tutorial data. It operates the following tables:
 * - tutorial
 *
 * @author	Melmark Panugao
 */

class New_player_tutorial extends CI_Model {

	/**
	 * set if tutorial is done 1:0
	 *
	 * @param string
	 * @return array
	 */
	function setIsTutorialDone($data) {
		$this->db->set("is_tutorial_done", $data['is_tutorial_done']);
		$this->db->where('playerId', $data['playerId']);

		return $this->db->update('player');
	}
}

/* End of file new_player_tutorial.php */
