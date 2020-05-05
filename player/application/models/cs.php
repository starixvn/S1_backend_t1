<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Player
 *
 * This model represents cs data. It operates the following tables:
 * - cs
 *
 * @author	Johann Merle
 */

class Cs extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	/**
	 * Will get the messages
	 *
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function getMessages($user_id, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT c.*, p.username as sender, au.username  as recepient, (SELECT DISTINCT cd.status FROM messagesdetails as cd where cd.messageId = c.messageId AND cd.status = '0') as unread FROM messages as c
			LEFT JOIN player as p
			ON p.playerId = c.playerId
			LEFT JOIN adminusers as au
			ON au.userId = c.adminId
			WHERE c.playerId = ?
			GROUP BY c.messageId
			ORDER BY c.messageId DESC
			$limit
			$offset";

		$query = $this->db->query($sql, array($user_id));

		return $query->result_array();
	}

	/**
	 * Will search messages based on the passed parameter
	 *
	 * @param 	int
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function searchMessagesList($user_id, $search, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT c.*, p.username as sender, au.username  as recepient, (SELECT DISTINCT cd.status FROM messagesdetails as cd where cd.messageId = c.messageId AND cd.status = '0') as unread FROM messages as c
			LEFT JOIN player as p
			ON p.playerId = c.playerId
			LEFT JOIN adminusers as au
			ON au.userId = c.adminId
			WHERE c.playerId = ?
			AND p.username LIKE ?
			OR au.username LIKE ?
			GROUP BY c.messageId
			ORDER BY c.messageId DESC
			$limit
			$offset ";

		$query = $this->db->query($sql, array($user_id, '%' . $search . '%', '%' . $search . '%'));

		return $query->result_array();
	}

	/**
	 * Will sort messages based on the passed parameter
	 *
	 * @param 	int
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function sortMessagesList($user_id, $sort, $limit, $offset) {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$sql = "SELECT c.*, p.username as sender, au.username  as recepient, (SELECT DISTINCT cd.status FROM messagesdetails as cd where cd.messageId = c.messageId AND cd.status = '0') as unread FROM messages as c
			LEFT JOIN player as p
			ON p.playerId = c.playerId
			LEFT JOIN adminusers as au
			ON au.userId = c.adminId
			where (c.status IN ('0', '1') AND c.playerId = ?)
			GROUP BY c.messageId
			ORDER BY $sort DESC
			$limit
			$offset";

		$query = $this->db->query($sql, array($user_id));

		return $query->result_array();
	}

	/**
	 * Will get messages status based on the session
	 *
	 * @param 	int
	 * @return 	int
	 */
	public function getMessagesStatusByMessageId($messages_id) {
		$sql = "SELECT * FROM `messages` where messageId = ? ";

		$query = $this->db->query($sql, array($messages_id));

		$result = $query->row_array();

		return $result['status'];
	}

	/**
	 * Will delete messages history based on the session
	 *
	 * @param 	int
	 */
	public function deleteMessagesHistory($messages_id) {
		$where = "messageId = '" . $messages_id . "'";

		$this->db->where($where);
		$this->db->delete('messages');

		$this->db->where($where);
		$this->db->delete('messagesdetails');
	}

	/**
	 * update messages
	 *
	 * @param 	array
	 * @param 	int
	 * @return 	void
	 */
	public function updateMessages($messages, $messages_id) {
		$this->db->where('messageId', $messages_id);
		$this->db->update('messages', $messages);
	}

	/**
	 * update messages details
	 *
	 * @param 	array
	 * @param 	int
	 * @return 	void
	 */
	public function updateMessagesDetails($messages_details, $messages_id) {
		$this->db->where('messageId', $messages_id);
		$this->db->update('messagesdetails', $messages_details);
	}

	/**
	 * add messages
	 *
	 * @param 	array
	 * @return 	int
	 */
	public function addMessages($add_messages) {

		$this->db->insert('messages', $add_messages);
		return $this->db->insert_id();
	}

	/**
	 * update messages details
	 *
	 * @param 	array
	 * @return 	void
	 */
	public function addMessagesDetails($add_messages_details) {

		$this->db->insert('messagesdetails', $add_messages_details);
	}

	/**
	 * count new messages in messages
	 *
	 * @return 	void
	 */
	public function countNewMessages() {
		$query = $this->db->query("SELECT COUNT(*) as new FROM messages where status = '0'");

		$result = $query->row_array();

		return $result['new'];
	}

	public function countPlayerUnreadMessages($player_id) {

		$this->db->select('c.messageId messageId');
		$this->db->select('MIN(cd.status) unread', false);
		$this->db->from('messagesdetails as cd');
		$this->db->join('messages as c', 'c.messageId = cd.messageId', 'left');
		$this->db->where('c.status', '1');
		$this->db->where('c.playerId', $player_id);
		$this->db->group_by('c.messageId');
		$this->db->having('unread !=', '0');

		$result = $this->db->get();
		return $result->num_rows;
	}

	/**
	 * check if ticket number is unique
	 *
	 * @param   string
	 * @return  bool
	 */
	public function checkTicketNumber($ticket_number) {
		$sql = "SELECT * FROM messages where session = ? ";

		$query = $this->db->query($sql, array($ticket_number));

		$result = $query->row_array();

		if (empty($result)) {
			return false;
		}

		return true;
	}
}

/* End of file cs.php */
/* Location: ./application/models/cs.php */
