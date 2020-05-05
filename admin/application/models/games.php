<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Games
 *
 * This model represents games data. It operates the following tables:
 * - game
 *
 * @author	Kaiser Dapar
 */

class Games extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}

	public function createGame($data) {
		return $this->db->insert('game', $data);
	}

	public function retrieveGames($data = null, $offset = 0, $limit = DEFAULT_ITEMS_PER_PAGE) {
 		return $this->db->order_by('game', 'asc')->get_where('game', $data, $limit, $offset)->result();
	}

	public function retrieveGameCount($data = null) {
 		return $this->db->count_all('game');
	}

	public function deleteGame($gameId) {
		$this->db->delete('game', array('gameId' => $gameId));
		return $this->db->affected_rows();
	}

	public function deleteSelectedGames($gameIds) {
		$this->db->where_in('gameId', $gameIds)->delete('game');
		return $this->db->affected_rows();
	}

	public function deleteAllGames() {
		$this->db->empty_table('game');
		return $this->db->affected_rows();
	}

}

/* End of file games.php */
/* Location: ./application/models/games.php */