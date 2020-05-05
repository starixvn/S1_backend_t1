<?php

require_once dirname(__FILE__) . '/BaseController.php';

/**
 *
 * @deprecated
 *
 * Class Auth
 *
 * General behaviors include :
 *
 * * Setting message for user
 * * Set current language
 * * Player login
 * * Load template
 * * Create a player in all platforms
 * * Send promo code
 * * Resend email
 *
 * @category Player Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Auth extends BaseController {

	public function __construct() {
		parent::__construct();

		$this->load->helper('url');
		$this->load->library(array('form_validation', 'authentication', 'player_functions', 'template', 'affiliate_process', 'email', 'salt', 'cms_function', 'game_platform/game_platform_manager', 'duplicate_account'));
	}

	/**
	 * overview : set message for users
	 *
	 * @param $type
	 * @param $message
	 * @return set session user data
	 */
	public function alertMessage($type, $message) {
		switch ($type) {
			case '1':
				$show_message = array(
					'result' => 'success',
					'message' => $message,
				);
				$this->session->set_userdata($show_message);
				break;

			case '2':
				$show_message = array(
					'result' => 'danger',
					'message' => $message,
				);
				$this->session->set_userdata($show_message);
				break;

			case '3':
				$show_message = array(
					'result' => 'warning',
					'message' => $message,
				);
				$this->session->set_userdata($show_message);
				break;
		}
	}

	/**
	 * overview : index page for login
	 */
	public function index() {
		$this->login();
	}

	/**
	 * overview : loads template for view based on regions in
	 *
	 * detail : config > template.php
	 *
	 * @param string $title
	 * @param string $description
	 * @param string $keywords
	 * @param string $activenav
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_js('resources/js/player/player.js');
		$this->template->add_css(CSSPATH . 'bootstrap.css');
		$this->template->add_css(CSSPATH . 'template.css');

		$this->template->write('skin', 'template1.css');
		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('player_id', $this->authentication->getPlayerId());
	}

	/**
	 * send email
	 *
	 * @return  void
	 */
	// public function send_email($type, $email, &$data) {
	// 	include_once realpath(APPPATH . '/libraries/PHPMailer/class.phpmailer.php');

	// 	$mail = new PHPMailer();
	// 	$data['random_verification_code'] = $this->player_functions->getPlayerById($this->session->userdata('player_id'))['verify'];
	// 	$data['site_name'] = "OG Website";

	// 	$body = $this->load->view('email/' . $type . '-html', $data, TRUE);

	// 	$mail->Username = $data['email_sender'];
	// 	$mail->Password = $data['email_sender_pass'];

	// 	$mail->From = 'johnrendell21@gmail.com';
	// 	$mail->FromName = 'OG Team';

	// 	$mail->Subject = $type;

	// 	$mail->AltBody = $this->load->view('email/' . $type . '-txt', $data, TRUE);
	// 	$mail->WordWrap = 50;

	// 	$mail->MsgHTML($body);
	// 	$mail->AddAddress($email, '');

	// 	$mail->CharSet = "UTF-8";
	// 	$mail->Encoding = "8bit";

	// 	$mail->IsHTML(true);

	// 	//var_dump($mail);exit();

	// 	try {
	// 		while (!$mail->Send()) {
	// 			$mail->Send();
	// 		}

	// 		//return $message = " Check your email address to verify your account!";
	// 		return $message = lang('notify.10');
	// 	} catch (Exception $e) {
	// 		echo $mail->ErrorInfo;
	// 	}

	// }


	/**
	 * overview : send promo code
	 *
	 * @param $promoCode
	 */
	public function sendPromoCode($promoCode) {
		$checkPromoCodeExist = $this->player_functions->checkPromoCodeExist($promoCode);
		$promo = $this->player_functions->retrievePromo($checkPromoCodeExist['promoId']);
		$player = $this->player_functions->getPlayerById($this->session->userdata('player_id'));
		$sorted_rules = $promo['levels'];
		$isInLevel = false;

		// usort($sorted_rules, function ($sorted_rules, $sorted_rules) {
		// 	if ($sorted_rules == $sorted_rules) {
		// 		return 0;
		// 	}

		// 	return $sorted_rules < $sorted_rules ? 1 : -1;
		// });

		foreach ($sorted_rules as $row) {
			if ($player['playerLevel'] == $row['id']) {
				$isInLevel = true;
				break;
			} else {
				$isInLevel = false;
			}
		}

		if ($isInLevel) {
			$this->session->set_userdata('promoCode', $promoCode);
			if (!$this->authentication->isLoggedIn()) {
				redirect(BASEURL . 'auth/register');
			} else {
				redirect(BASEURL . 'cashier/makeDeposit/' . $this->session->userdata('player_id'));
			}
		} else {
			//$message = "This promo is not for your level";
			$message = lang('notify.13');
			$this->alertMessage(2, $message);
			redirect(BASEURL . 'online/promotions');
		}
	}

	/**
	 * overview : resend email
	 *
	 * @param int	$player_id
	 */
	public function resendEmail($player_id) {
		$player = $this->player_functions->getPlayerById($player_id);
		$email_settings = $this->player_functions->getEmail();

		$data['email_sender'] = (string) $email_settings['email'];
		$data['email_sender_pass'] = (string) base64_decode($email_settings['password']);

		$send_email = $this->send_email('verification', $player['email'], $data);

		//$message = "New verification message was sent to your email";
		$message = lang('notify.14');
		$this->alertMessage(1, $message);
		redirect(BASEURL . 'player_controller/playerSettings/' . $player_id);
	}

	/**
	 * overview : Testing Email Sending
	 *
	 * @return void
	 */
	public function testSendEmail() {
		$this->email_setting->testSendEmail();
	}

	/**
	 * Save http Request
	 *
	 * @return  rendered Template
	 */
	// public function saveHttpRequest($player_id, $type) {
	// 	$this->load->library(array('user_agent', 'duplicate_account'));

	// 	$headers = $this->input->request_headers();

	// 	$data = array(
	// 		"playerId" => $player_id,
	// 		"ip" => $this->input->ip_address(),
	// 		"cookie" => $headers['Cookie'],
	// 		"referrer" => ($this->agent->is_referral() == TRUE) ? $this->agent->referrer() : ' ',
	// 		"user_agent" => $headers['User-agent'],
	// 		"os" => $this->agent->platform(),
	// 		"device" => ($this->agent->is_mobile() == TRUE) ? $this->agent->mobile() : $this->agent->browser() . " " . $this->agent->version(),
	// 		"is_mobile" => ($this->agent->is_mobile() == TRUE) ? 1 : 0,
	// 		"type" => $type,
	// 		"createdat" => date("Y-m-d H:i:s"),
	// 	);

	// 	if ($this->duplicate_account->existingHttpRequest($data)) {
	// 		$this->duplicate_account->insertHttpRequest($data);
	// 	}

	// }



	// public function storeWindowWidthSession(){

	// 	$window_width = $this->input->post('window_width');
	// 	$this->session->set_userdata('window_width', $window_width);
	// 	$window_width_session = $this->session->userdata('window_width');
	// 	$this->returnJsonResult(array('success' => true, 'window_width' => $window_width_session));

	// }

} ///END OF FILE//////////////////
