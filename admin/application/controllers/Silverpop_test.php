<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

class Silverpop_test extends BaseController {

	protected $options = array(
		'apiHost'  => 'engage3.silverpop.com',
		'username' => 'kaiser.d@tripleonetech.com',
		'password' => 'Abc123!!',
	);

	function __construct() {
		parent::__construct();
		$this->load->library('silverpop_library', $this->options);
	}

	function index() {

		$autoResponderMailingId = 25968787;
		$listId = 5608727;
		$recipientEmail = 'kaiser.d@tripleonetech.com';

		try {

			header('Content-Type: text/plain');

			# Login ###########################################################
			$login_result = $this->silverpop_library->login();
			echo "login:\n";
			echo print_r($login_result, true) , "\n";

			# GetLists ########################################################
			// $getLists_result = $this->silverpop_library->getLists(Silverpop_library::VISIBILITY_SHARED, Silverpop_library::LIST_TYPE_DATABASES_CONTACT_LISTS_QUERIES);
			// echo "getLists:\n";
			// echo print_r($getLists_result, true) , "\n";

			// # AddRecipient ########################################################
			$addRecipient_result = $this->silverpop_library->addRecipient($listId, $recipientEmail);
			echo "addRecipient:\n";
			echo print_r($addRecipient_result, true) , "\n";

			// # SelectRecipientData ########################################################
			$selectRecipientData_result = $this->silverpop_library->selectRecipientData($listId, $recipientEmail);
			echo "selectRecipientData:\n";
			echo print_r($selectRecipientData_result, true) , "\n";

			# SendMailing #####################################################
			$sendMailing_result = $this->silverpop_library->sendMailing($autoResponderMailingId, $recipientEmail);
			echo "sendMailing:\n";
			echo print_r($sendMailing_result, true) , "\n";

			# Logout ##########################################################
			$logout_result = $this->silverpop_library->logout();
			echo "logout:\n";
			echo print_r($logout_result, true) , "\n";

		} catch (Exception $e) { 
			echo $e->getMessage() . "\n\n";
			echo 'getLastRequest: ' . print_r($this->silverpop_library->getLastRequest(), true) . "\n\n";
			echo 'getLastResponse: ' . print_r($this->silverpop_library->getLastResponse(), true) . "\n\n";
			echo 'getLastFault: ' . print_r($this->silverpop_library->getLastFault(), true) . "\n\n"; 
		}

	}

}