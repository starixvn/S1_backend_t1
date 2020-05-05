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
 *
 * General behaviors include
 * * Get messages of a certain user
 * * Get all message history
 * * Ge the message of a certain player
 * * able to search messages from the list
 * * search messages history lists
 * * able to sort message lists
 * * delete message history
 * * update message or message details
 *
 * @category CS Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Cs extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	/**
	 * detail: Will get the messages
	 *
	 * @param int $user_id messages admin id
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array
	 */
	public function getMessages($user_id, $limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = '';
		}

		/*$query = $this->db->query("SELECT c.*, p.username as sender, au.username  as recepient, (SELECT DISTINCT cd.status FROM messagesdetails as cd where cd.messageId = c.messageId AND cd.status = '0') as unread FROM messages as c
			LEFT JOIN player as p
			ON p.playerId = c.playerId
			LEFT JOIN adminusers as au
			ON au.userId = c.adminId
			where c.status = '0' AND c.status != '2' OR (c.status = '1' AND c.adminId = $user_id)
			GROUP BY c.messageId
			ORDER BY messageId ASC
			$limit
			$offset
		");*/

		$this->db->select('c.*, p.username as sender, au.username  as recepient, (SELECT DISTINCT cd.status FROM messagesdetails as cd where cd.messageId = c.messageId AND cd.status = 0) as unread');
		$this->db->from('messages as c');
		$this->db->join('player as p', 'p.playerId = c.playerId', 'left');
		$this->db->join('adminusers as au', 'au.userId = c.adminId', 'left');
		$this->db->where('c.status', '0');
		$this->db->where('c.status !=', '2');
		$this->db->or_where('c.status', '1');
		$this->db->where('c.adminId', $user_id);
		$this->db->group_by('c.messageId');
		$this->db->order_by('messageId', 'asc');

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * detail: Will get the messages history
	 *
	 * @param int $userId @deprecated
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return 	array
	 */
	public function getMessagesHistory($user_id, $limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// $query = $this->db->query("SELECT c.*, p.username as sender, au.username as recepient FROM messages as c
		// 	LEFT JOIN player as p
		// 	ON p.playerId = c.playerId
		// 	LEFT JOIN adminusers as au
		// 	ON au.userId = c.adminId
		// 	where c.status = '2' AND c.adminId = '" . $user_id . "'
		// 	ORDER BY messageId ASC
		// 	$limit
		// 	$offset
		// ");

		$this->db->select('c.*, p.username as sender, au.username as recepient');
		$this->db->from('messages as c');
		$this->db->join('player as p', 'p.playerId = c.playerId', 'left');
		$this->db->join('adminusers as au', 'au.userId = c.adminId', 'left');
		$this->db->where('c.status', '2');
		$this->db->order_by('messageId', 'asc');

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * detail: get message history of a certain player
	 *
	 * @param int $playerId messages player id
	 *
	 * @return array
	 */
	public function getPlayerMessageHistory($player_id,$where = null, $values = null) {

		$this->db->select('c.*, p.username as sender, au.username as recepient');
		$this->db->from('messages as c');
		$this->db->join('player as p', 'p.playerId = c.playerId', 'left');
		$this->db->join('adminusers as au', 'au.userId = c.adminId', 'left');
		$this->db->where('c.playerId', $player_id);
		$this->db->where($where['0'], $values['0']);
        $this->db->where($where['1'], $values['1']);
		$this->db->order_by('messageId', 'asc');

		$query = $this->db->get();
		$result =  $query->result_array();
		$result = json_decode(json_encode($result),true);
        return $result;
	}

	/**
	 * Will get the player messages history
	 *
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	// public function getPlayerMessageHistory($player_id) {
	// $query = $this->db->query("SELECT md.*, m.subject FROM messagesdetails as md
	// 	LEFT JOIN messages as m
	// 	ON md.messageId = m.messageId
	// 	where m.status = '2' AND m.playerId = '" . $player_id . "'
	// 	ORDER BY md.messageDetailsId ASC
	// ");

	// 	$this->db->select('md.*, m.subject');
	// 	$this->db->from('messagesdetails as md');
	// 	$this->db->join('messages as m', 'md.messageId = m.messageId', 'left');
	// 	$this->db->where('m.status', '2');
	// 	$this->db->where('m.playerId', $player_id);
	// 	$this->db->order_by('md.messageDetailsId', 'asc');

	// 	$query = $this->db->get();

	// 	return $query->result_array();
	// }

	/**
	 * detail: Will search messages based on the passed parameter
	 *
	 * @param int $userId messages adminId
	 * @param string $search
	 * @param int $limit
	 * @param int $offset
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

		$where_in = array('0', '1');

		$this->db->select('c.*, p.username as sender, au.username  as recepient, (SELECT DISTINCT DISTINCT cd.status FROM messagesdetails as cd where cd.messageId = c.messageId AND cd.status = 0) as unread');
		$this->db->from('messages as c');
		$this->db->join('player as p', 'p.playerId = c.playerId', 'left');
		$this->db->join('adminusers as au', 'au.userId = c.adminId', 'left');
		$this->db->where_in('c.status', $where_in);
		$this->db->or_where('c.adminId', $user_id);
		$this->db->or_where('c.status !=', '2');
		$this->db->like('p.username', $search);
		$this->db->or_like('au.username', $search);
		$this->db->or_like('c.subject', $search);
		$this->db->group_by('c.messageId');
		$this->db->order_by('c.messageId', 'asc');

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * detail: Will search messages history based on the passed parameter
	 *
	 * @param int $userId messages adminId
	 * @param string $search
	 * @param int $limit
	 * @param int $offset
	 * @return 	array
	 */
	public function searchMessagesHistoryList($user_id, $search, $limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// $query = $this->db->query("SELECT c.*, p.username as sender, au.username as recepient FROM messages as c
		// 	LEFT JOIN player as p
		// 	ON p.playerId = c.playerId
		// 	LEFT JOIN adminusers as au
		// 	ON au.userId = c.adminId
		// 	where p.username LIKE
		// 	'%" . $search . "%' OR
		// 	au.username LIKE
		// 	'%" . $search . "%' OR
		// 	c.subject LIKE
		// 	'%" . $search . "%' AND
		// 	c.status NOT IN ('0', '1') AND c.adminId = $user_id
		// 	ORDER BY c.messageId ASC
		// 	$limit
		// 	$offset
		// ");

		$where_not_in = array('0', '1');

		$this->db->select('c.*, p.username as sender, au.username as recepient');
		$this->db->from('messages as c');
		$this->db->join('player as p', 'p.playerId = c.playerId', 'left');
		$this->db->join('adminusers as au', 'au.userId = c.adminId', 'left');
		$this->db->where_not_in('c.status', $where_not_in);
		$this->db->or_where('c.adminId', $user_id);
		$this->db->like('p.username', $search);
		$this->db->or_like('au.username', $search);
		$this->db->or_like('c.subject', $search);
		$this->db->group_by('c.messageId');
		$this->db->order_by('c.messageId', 'asc');

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * detail: Will sort messages based on the passed parameter
	 *
	 * @param int $userId messages adminId
	 * @param string $sort
	 * @param int $limit
	 * @param int $offset
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

		// $query = $this->db->query("SELECT c.*, p.username as sender, au.username  as recepient, (SELECT DISTINCT cd.status FROM messagesdetails as cd where cd.messageId = c.messageId AND cd.status = '0') as unread FROM messages as c
		// 	LEFT JOIN player as p
		// 	ON p.playerId = c.playerId
		// 	LEFT JOIN adminusers as au
		// 	ON au.userId = c.adminId
		// 	where (c.status IN ('0', '1') OR c.adminId = $user_id) AND c.status != '2'
		// 	GROUP BY c.messageId
		// 	ORDER BY $sort ASC
		// 	$limit
		// 	$offset
		// ");

		$where_in = array('0', '1');

		$this->db->select('c.*, p.username as sender, au.username  as recepient, (SELECT DISTINCT cd.status FROM messagesdetails as cd where cd.messageId = c.messageId AND cd.status = 0) as unread');
		$this->db->from('messages as c');
		$this->db->join('player as p', 'p.playerId = c.playerId', 'left');
		$this->db->join('adminusers as au', 'au.userId = c.adminId', 'left');
		$this->db->where_in('c.status', $where_in);
		$this->db->or_where('c.adminId', $user_id);
		$this->db->where('c.status !=', '2');
		$this->db->group_by('c.messageId');
		$this->db->order_by($sort, 'asc');

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * detail: Will sort messages history based on the passed parameter
	 *
	 * @param int $userId messages adminId
	 * @param string $sort
	 * @param int $limit
	 * @param int $offset
	 * @return 	array
	 */
	public function sortMessagesHistoryList($user_id, $sort, $limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// $query = $this->db->query("SELECT c.*, p.username as sender, au.username as recepient FROM messages as c
		// 	LEFT JOIN player as p
		// 	ON p.playerId = c.playerId
		// 	LEFT JOIN adminusers as au
		// 	ON au.userId = c.adminId
		// 	where c.status = '2' AND c.adminId = '" . $user_id . "'
		// 	ORDER BY $sort ASC
		// 	$limit
		// 	$offset
		// ");

		$this->db->select('c.*, p.username as sender, au.username as recepient');
		$this->db->from('messages as c');
		$this->db->join('player as p', 'p.playerId = c.playerId', 'left');
		$this->db->join('adminusers as au', 'au.userId = c.adminId', 'left');
		$this->db->where('c.status', '2');
		$this->db->or_where('c.adminId', $user_id);
		$this->db->order_by($sort, 'asc');

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * detail: Will get messages history based on the session
	 *
	 * @param int $message_id message details messageId
	 * @param int $limit
	 * @param int $offset
	 * @return 	array
	 */
	public function getMessagesHistoryByMessageId($message_id, $limit, $offset) {

		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		// $query = $this->db->query("SELECT * FROM `messagesdetails` where messageId = '" . $message_id . "' $limit $offset");

		$this->db->select('*');
		$this->db->from('messagesdetails');
		$this->db->where('messageId', $message_id);

		$query = $this->db->get();

		return $query->result_array();
	}

	/**
	 * detail: Will delete messages history based on the session
	 *
	 * @param int $message_id
	 */
	public function deleteMessagesHistory($message_id) {
		$where = "messageId = '" . $message_id . "'";

		$this->db->where($where);
		$this->db->delete('messages');

		$this->db->where($where);
		$this->db->delete('messagesdetails');
	}

	/**
	 * detail: update messages
	 *
	 * @param array $messages
	 * @param int $message_id messages messageId
	 * @return 	void
	 */
	public function updateMessages($messages, $message_id) {
		$this->db->where('messageId', $message_id);
		$this->db->update('messages', $messages);
	}

	/**
	 * detail: update messages details
	 *
	 * @param array $message_details
	 * @param int $message_id messagesdetails messageId
	 *
	 * @return Boolean
	 */
	public function updateMessagesDetails($messages_details, $message_id) {
		$this->db->where('messageId', $message_id);
		$this->db->where('status', '0');
		$this->db->update('messagesdetails', $messages_details);
	}

	/**
	 * detail: add messages
	 *
	 * @param array $add_messages
	 * @return int
	 */
	public function addMessages($add_messages) {

		$this->db->insert('messages', $add_messages);
		return $this->db->insert_id();
	}

	/**
	 * detail: update messages details
	 *
	 * @param array $add_message_details
	 * @return Boolean
	 */
	public function addMessagesDetails($add_message_details) {
		$this->db->insert('messagesdetails', $add_message_details);
	}

	/**
	 * detail: count new messages in messages
	 *
	 * @return string
	 */
	public function countNewMessages() {
		$this->db->select('COUNT(*) as new');
		$this->db->from('messages');
		$this->db->where('status', '0');

		$query = $this->db->get();
		$result = $query->row_array();

		return $result['new'];
	}

	/**
	 * detail: check if ticket number is unique
	 *
	 * @param string $ticket_number
	 * @return Boolean
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
