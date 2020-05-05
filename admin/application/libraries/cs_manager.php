<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * CS Manager
 *
 * CS Manager library for FHI OG System
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
	public function randomizer($name) {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
			. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. '0123456789!@#$%^&*()'
			. $name); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$randomPassword = '';
		foreach (array_rand($seed, 9) as $k) {
			$randomPassword .= $seed[$k];
		}

		return $randomPassword;
	}
	/**
	 * Will randomize alphanumeric and special characters
	 *
	 * @param 	string
	 * @return	string
	 */
	public function generateTicket() {
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
	 * Will get the chat
	 *
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function getMessages($user_id, $limit, $offset) {
		return $this->ci->cs->getMessages($user_id, $limit, $offset);
	}

	/**
	 * Will get the chat history
	 *
	 * @param 	int
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function getMessagesHistory($user_id, $limit, $offset) {
		return $this->ci->cs->getMessagesHistory($user_id, $limit, $offset);
	}

	/**
	 * Will get the player messages history
	 *
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function getPlayerMessageHistory($player_id) {
		return $this->ci->cs->getPlayerMessageHistory($player_id);
	}

	/**
	 * Will search chat based on the passed parameter
	 *
	 * @param 	int
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function searchMessagesList($user_id, $search, $limit, $offset) {
		return $this->ci->cs->searchMessagesList($user_id, $search, $limit, $offset);
	}

	/**
	 * Will search chat history based on the passed parameter
	 *
	 * @param 	int
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function searchMessagesHistoryList($user_id, $search, $limit, $offset) {
		return $this->ci->cs->searchMessagesHistoryList($user_id, $search, $limit, $offset);
	}

	/**
	 * Will sort chat based on the passed parameter
	 *
	 * @param 	int
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function sortMessagesList($user_id, $sort, $limit, $offset) {
		return $this->ci->cs->sortMessagesList($user_id, $sort, $limit, $offset);
	}

	/**
	 * Will sort chat history based on the passed parameter
	 *
	 * @param 	int
	 * @param 	string
	 * @param 	int
	 * @param 	int
	 * @return 	array
	 */
	public function sortMessagesHistoryList($user_id, $sort, $limit, $offset) {
		return $this->ci->cs->sortMessagesHistoryList($user_id, $sort, $limit, $offset);
	}

	/**
	 * Will delete chat history based on the session
	 *
	 * @param 	int
	 */
	public function deleteMessagesHistory($message_id) {
		$this->ci->cs->deleteMessagesHistory($message_id);
	}

	/**
	 * update chat
	 *
	 * @param 	array
	 * @param 	int
	 * @return 	void
	 */
	public function updateMessages($messages, $message_id) {
		$this->ci->cs->updateMessages($messages, $message_id);
	}

	/**
	 * update chat details
	 *
	 * @param 	array
	 * @param 	int
	 * @return 	void
	 */
	public function updateMessagesDetails($messages_details, $message_id) {
		$this->ci->cs->updateMessagesDetails($messages_details, $message_id);
	}

	/**
	 * update chat details
	 *
	 * @param 	array
	 * @return 	void
	 */
	public function addMessagesDetails($add_messages_details) {
		$this->ci->cs->addMessagesDetails($add_messages_details);
	}

	/**
	 * count new messages in chat
	 *
	 * @return 	void
	 */
	public function countNewMessages() {
		return $this->ci->cs->countNewMessages();
	}

	/**
	 * get all banner from cms banner table
	 *
	 * @return	array
	 */
	public function getAllCMSBanner($limit, $offset) {
		return $this->ci->affiliate->getAllCMSBanner($limit, $offset);
	}
}

/* End of file cs_manager.php */
/* Location: ./application/libraries/cs_manager.php */
