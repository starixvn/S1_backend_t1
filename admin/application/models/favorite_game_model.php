<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Favorite_game_model
 *
 * @deprecated marked by elvis
 */
class Favorite_game_model extends BaseModel {

	public function added_to_favorites($player_id, $url) {
		$this->db->where('player_id', $player_id);
		$this->db->where('url', $url);
		return $this->db->count_all_results('favorite_game') > 0;
	}

	public function get_favorites($player_id) {
		$this->db->select('name');
		$this->db->select('url');
		$this->db->select('image as imageUrl');
		$this->db->where('player_id', $player_id);
		$query = $this->db->get('favorite_game');
		return $query->result_array();
	}

	public function add_to_favorites($data) {
		$success = $this->db->insert('favorite_game', $data);
		return $success;
	}

	public function remove_from_favorites($player_id ,$url) {
		$this->db->where('player_id', $player_id);
		$this->db->where('url', $url);
		$success = $this->db->delete('favorite_game');
		return $success; 
	} 

}

///END OF FILE