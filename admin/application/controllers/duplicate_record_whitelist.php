<?php
trait duplicate_record_whitelist {

	/**
	 * overview : remove player in duplicate record whitelist
	 *
	 * @param int $player_id	player id
	 */
	public function removeDuplicateAccountWhitelist($playerId) {
		$this->db->where('a.playerId', $playerId);
		$this->db->where('b.playerId', $playerId);
		$this->db->update('playerdetails as a , player as b', array(
			'a.duplicate_record_exempted' => FALSE,
			'b.duplicate_record_exempted' => FALSE
		));
		$user = $this->user_functions->searchUser($playerId);
		$this->saveAction('Remove User', $user['username'] . "'s account has been remove from duplicate record whitelist by " . $this->authentication->getUsername());
		$result['message'] = "success";
		return $this->returnJsonResult($result);
	}

	/**
	 * overview : add player in duplicate record whitelist
	 *
	 * @param int $player_id	player id
	 */
	public function addDuplicateAccountWhitelist($username) {
		$this->db->select('player.playerId,playerdetails.duplicate_record_exempted');
		$this->db->where('player.username', $username);
		$this->db->join('playerdetails', 'playerdetails.playerId = player.playerId');
		$this->db->from('player');
		$query = $this->db->get();
		$data = $query->result_array();
		if(!empty($data)){
			if($data[0]['duplicate_record_exempted']) {
				$result['message'] = lang("User is already in the whitelist");
			}else{
				$this->db->where('a.playerId', $data[0]['playerId']);
				$this->db->where('b.playerId', $data[0]['playerId']);
				$this->db->update('playerdetails as a , player as b', array(
					'a.duplicate_record_exempted' => TRUE,
					'b.duplicate_record_exempted' => TRUE
				));
				$this->saveAction('Add User', $username . "'s account has been added to duplicate record whitelist by " . $this->authentication->getUsername());
				$result['message'] = lang('sys.gd27');
			}
		}else{
			$result['message'] = lang('User not found');
		}
		//$result['message'] = true;
		return $this->returnJsonResult($result);
	}

	/**
	 * overview : list of player in duplicate record whitelist
	 *
	 * @param int $player_id	player id
	 */
	public function viewDuplicateAccountWhitelist() {
		$this->db->select('player.username,playerdetails.playerId');
		$this->db->where('playerdetails.duplicate_record_exempted', TRUE);
		$this->db->join('playerdetails', 'playerdetails.playerId = player.playerId');
		$query = $this->db->get('player');
		$data['users'] = $query->result_array();

		$this->load->view('system_management/view_duplicate_account_whitelist',$data);
	}


}
