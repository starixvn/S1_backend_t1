<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * CS Manager
 *
 * CS Manager library for FHI Player Front End
 *
 * @package		CS Manager
 * @author		Johann Merle
 * @version		1.0.0
 */

class Cs_manager {
	private $error = array();

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array(''));
		$this->ci->load->model(array('cs'));
	}

	/**
	 * Will randomize alphanumeric and special characters
	 *
	 * @param 	string
	 * @return	string
	 */
	public function randomizer() {
		$seed = str_split( //'abcdefghijklmnopqrstuvwxyz'
			//.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			'0123456789'); //!@#$%^&*()'
		//.$name); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$randomPassword = '';
		foreach (array_rand($seed, 4) as $k) {
			$randomPassword .= $seed[$k];
		}

		return $randomPassword;
	}

	/**
	 * Will get the messages
	 *
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function getMessages($player_id, $limit, $offset) {
		return $this->ci->cs->getMessages($player_id, $limit, $offset);
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
	public function searchMessagesList($player_id, $search, $limit, $offset) {
		return $this->ci->cs->searchMessagesList($player_id, $search, $limit, $offset);
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
	public function sortMessagesList($player_id, $sort, $limit, $offset) {
		return $this->ci->cs->sortMessagesList($player_id, $sort, $limit, $offset);
	}

	/**
	 * Will get messages status based on the session
	 *
	 * @param 	int
	 * @return 	int
	 */
	public function getMessagesStatusByMessageId($message_id) {
		return $this->ci->cs->getMessagesStatusByMessageId($message_id);
	}

	/**
	 * Will delete messages history based on the session
	 *
	 * @param 	int
	 */
	public function deleteMessagesHistory($message_id) {
		$this->ci->cs->deleteMessagesHistory($message_id);
	}

	/**
	 * update messages
	 *
	 * @param 	array
	 * @param 	int
	 * @return 	void
	 */
	/*public function updateChat($messages, $message_id) {
	$this->ci->cs->updateChat($messages, $message_id);
	}*/

	/**
	 * update messages details
	 *
	 * @param 	array
	 * @param 	int
	 * @return 	void
	 */
	/*public function updateChatDetails($chat_details, $message_id) {
	$this->ci->cs->updateChatDetails($chat_details, $message_id);
	}*/

	/**
	 * add messages
	 *
	 * @param 	array
	 * @return 	int
	 */
	public function addMessages($add_messages) {
		return $this->ci->cs->addMessages($add_messages);
	}

	/**
	 * add messages details
	 *
	 * @param 	array
	 * @return 	void
	 */
	public function addMessagesDetails($add_messages_details) {
		$this->ci->cs->addMessagesDetails($add_messages_details);
	}

	/**
	 * count new messages in messages
	 *
	 * @return 	void
	 */
	public function countNewMessages() {
		return $this->ci->cs->countNewMessages();
	}

	/**
	 * check if ticket number is unique
	 *
	 * @param   string
	 * @return  bool
	 */
	public function checkTicketNumber($ticket_number) {
		return $this->ci->cs->checkTicketNumber($ticket_number);
	}
}

/* End of file cs_manager.php */
/* Location: ./application/libraries/cs_manager.php */
