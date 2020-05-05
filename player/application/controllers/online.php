<?php

/**
 *
 * @deprecated
 *
 */
class Online extends CI_Controller {

	function __construct() {
		parent::__construct();

		$this->load->helper('url');
		$this->load->library(array('form_validation', 'authentication', 'player_functions', 'cms_function', 'template', 'promo_functions', 'pagination', 'api_functions', 'salt', 'cs_manager', 'game_platform/game_platform_manager'));
	}

	/**
	 * set message for users
	 *
	 * @param   int
	 * @param   string
	 * @return  set session user data
	 */
	// public function alertMessage($type, $message) {
	// 	switch ($type) {
	// 		case '1':
	// 			$show_message = array(
	// 				'result' => 'success',
	// 				'message' => $message,
	// 			);
	// 			$this->session->set_userdata($show_message);
	// 			break;

	// 		case '2':
	// 			$show_message = array(
	// 				'result' => 'danger',
	// 				'message' => $message,
	// 			);
	// 			$this->session->set_userdata($show_message);
	// 			break;

	// 		case '3':
	// 			$show_message = array(
	// 				'result' => 'warning',
	// 				'message' => $message,
	// 			);
	// 			$this->session->set_userdata($show_message);
	// 			break;
	// 	}
	// }

	// function index() {
	// 	redirect(BASEURL . 'online/welcome');
	// }

	/**
	 * set language
	 *
	 * @return  rendered Template with array of data
	 */
	public function setCurrentLanguage($language) {
		$this->language_function->setCurrentLanguage($language);

		$language == 1 ? $this->session->set_userdata('currentLanguage', 'en') : $this->session->set_userdata('currentLanguage', 'ch');
		$arr = array('status' => 'success');

		echo json_encode($arr);
	}

	/**
	 * Loads template for view based on regions in
	 * config > template.php
	 *
	 */
	// private function loadTemplate($title, $description, $keywords, $activenav) {
	// 	$this->template->add_js('resources/js/player/player.js');
	// 	$this->template->write('skin', 'template1.css');
	// 	$this->template->write('title', $title);
	// 	$this->template->write('description', $description);
	// 	$this->template->write('keywords', $keywords);
	// 	$this->template->write('activenav', $activenav);
	// 	$this->template->write('username', $this->authentication->getUsername());
	// 	$this->template->write('player_id', $this->authentication->getPlayerId());
	// 	$data['data'] = $this->player_functions->getPlayerMainWallet($this->authentication->getPlayerId());
	// 	$this->template->write('mainwallet', $data['data']['mainwallet']['totalBalanceAmount']);
	// 	$data['data'] = $this->player_functions->getPlayerById($this->authentication->getPlayerId());
	// 	$this->template->write('active', $data['data']['active']);
	// }

	/**
	 * view welcome/homepage
	 *
	 * @return void
	 */
	// function welcome() {

	// 	$this->session->userdata('currentLanguage') == '' ? $this->session->set_userdata('currentLanguage', 'en') : '';
	// 	// if (!$this->authentication->isLoggedIn()) {
	// 	//     //loads news
	// 	//     $data['news'] = $this->player_functions->getAllNews();

	// 	//     $bannerType = 1; //home banner big
	// 	//     $data['homemainbanner'] = $this->cms_function->getCmsBanner($bannerType);

	// 	//     $bannerType = 3; //home banner small
	// 	//     $data['homesmallbanner'] = $this->cms_function->getCmsBanner($bannerType);

	// 	//     $bannerType = 4; //home banner game
	// 	//     $data['homegamebanner'] = $this->cms_function->getCmsBanner($bannerType);

	// 	//     $data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 	//     $data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 	//     $this->loadTemplate('Welcome To OG Website', '', '', 'home');
	// 	//     $this->template->write_view('main_content', 'online/view_welcome', $data);
	// 	//     $this->template->write_view('footer_content', 'template/footer_template');
	// 	//     $this->template->render();
	// 	// } else {
	// 	$data['news'] = $this->player_functions->getAllNews();

	// 	$bannerType = 1; //home banner big
	// 	$data['homemainbanner'] = $this->cms_function->getCmsBanner($bannerType);

	// 	$bannerType = 3; //home banner small
	// 	$data['homesmallbanner'] = $this->cms_function->getCmsBanner($bannerType);

	// 	$bannerType = 4; //home banner game
	// 	$data['homegamebanner'] = $this->cms_function->getCmsBanner($bannerType);

	// 	$active = $this->player_functions->getPlayerById($this->authentication->getPlayerId());
	// 	$active = $active['active'];

	// 	$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 	$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 	$this->loadTemplate('Welcome To OG Website', '', '', 'home');

	// 	if ($this->authentication->isLoggedIn() && $active == 0) {
	// 		redirect(BASEURL . 'auth/activate/' . $this->authentication->getPlayerId());
	// 	} else {
	// 		$this->session->unset_userdata(array('key' => ''));
	// 		$this->template->write_view('main_content', 'online/view_welcome', $data);
	// 		$this->template->write_view('footer_content', 'template/footer_template');
	// 		$this->template->render();
	// 	}

	// 	// }
	// }

	/**
	 * view aboutus
	 *
	 * @return void
	 */
	// function aboutus() {
	// 	$this->loadTemplate('About Us', '', '', 'home');
	// 	$this->template->write_view('main_content', 'online/view_aboutus');
	// 	$this->template->write_view('sidebar_content', 'template/sidebar_template');
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * view getstart
	 *
	 * @return void
	 */
	// function getstart() {
	// 	$this->loadTemplate('Get Started', '', '', 'getstart');
	// 	$this->template->write_view('main_content', 'online/view_getstart');
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * view contact us
	 *
	 * @return void
	 */
	// function contactus() {
	// 	if (!$this->authentication->isLoggedIn()) {
	// 		$this->loadTemplate('Contact Us', '', '', 'contactus');
	// 		$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 		$data['footerData'] = $this->cms_function->getCmsFooterContentData();

	// 		$this->template->write_view('main_content', 'online/view_contactus', $data);
	// 		$this->template->write_view('footer_content', 'template/footer_template');
	// 		$this->template->render();
	// 	} else {
	// 		redirect(BASEURL . 'messages');
	// 	}
	// }

	/**
	 * email contact us
	 *
	 * @return void
	 */
	// function emailContactUs() {
	// 	$sendToEmail = $this->input->post('sendto');
	// 	$emailSender = $this->input->post('emailsender');

	// 	$fullName = $this->input->post('fullname');
	// 	$fromEmail = $this->input->post('email');
	// 	$subject = $this->input->post('subject');
	// 	$message = $this->input->post('message');
	// 	$email = $this->email_setting->sendEmail($sendToEmail,

	// 		array(
	// 			'from'=>$emailSender,
	// 			'from_name' => $fullName,
	// 			'subject' => $subject,
	// 			'body' => $message." note: email is from: ".$fromEmail
	// 		)
	// 	);
	// 	return $email;
	// }

	/**
	 * view casino
	 *
	 * @return void
	 */
	// function casino() {
	// 	$this->loadTemplate('casino', '', '', 'casino');

	// 	if ($this->authentication->isLoggedIn()) {
	// 		$playerId = $this->authentication->getPlayerId();
	// 		// $playerUsername = $this->authentication->getUsername();

	// 		$data['blockGame'] = $this->player_functions->getPlayerBlockGame($playerId);

	// 		//pt account
	// 		// $playerGamePasswordPT = $this->player_functions->getPlayerPassword($playerId, 1);
	// 		// $data['playerGamePasswordPT'] = $playerGamePasswordPT->password; //$this->salt->decrypt($playerGamePasswordPT->password, DESKEY_OG);

	// 		//player level
	// 		$data['playerLevelAllowedGame'] = $this->player_functions->getPlayerLevelGame($playerId);

	// 		//blocked game
	// 		$data['playerBlockedGame'] = $this->player_functions->getPlayerBlockGame($playerId);
	// 	}

	// 	$bannerType = 2; //home banner
	// 	$data['casinobanner'] = $this->cms_function->getCmsBanner($bannerType);
	// 	$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 	$data['footerData'] = $this->cms_function->getCmsFooterContentData();

	// 	$this->template->write_view('main_content', 'online/view_casino', $data);
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }
	// protected function getDeskeyOG() {
	// 	return $this->config->item('DESKEY_OG');
	// }

	// public function checkAGPlayerExist() {
	// 	$sid = CAGENT_AG . $this->getRandomSequence();

	// 	$currentLang = $this->session->userdata('currentLanguage');
	// 	if ($currentLang == 'en') {
	// 		$lang = 3;
	// 	} elseif ($currentLang == 'ch') {
	// 		$lang = 1;
	// 	} else {
	// 		$lang = 1;
	// 	}

	// 	$api = $this->game_platform_manager->initApi(2);
	// 	$result = $api->isPlayerExist($this->authentication->getUsername());

	// 	if ($result['exists'] == false) {
	// 		$playerId = $this->authentication->getPlayerId();
	// 		$playerDetails = $this->player_functions->getPlayerById($playerId); // Password from player table
	// 		$passwordAG = $this->salt->decrypt($playerDetails['password'], $this->getDeskeyOG());

	// 		$api->createPlayer($this->authentication->getUsername(), $playerId, $passwordAG); //create player
	// 	} else {
	// 		$getPasswordResult = $api->getPassword($this->authentication->getUsername());
	// 		$passwordAG = $getPasswordResult['password'];
	// 	}

	// 	$input = "cagent=" . CAGENT_AG . "/\\\\/loginname=" . $this->authentication->getUsername() . "/\\\\/actype=1/\\\\/password=" . $passwordAG . "/\\\\/dm=" . DM_AG . "/\\\\/sid=" . $sid . "/\\\\/lang=" . $lang . "/\\\\/gameType=0/\\\\/oddtype=A/\\\\/cur=CNY";

	// 	$params = $this->salt->encrypt($input, DESKEY_AG);
	// 	$keys = MD5($params . MD5KEY_AG);

	// 	redirect(INVOKEURL_AG . 'forwardGame.do?params=' . $params . '&key=' . $keys);
	// }

	// public function checkPTPlayerExist() {
	// 	$playerId = $this->authentication->getPlayerId();
	// 	$playerUsername = $this->authentication->getUsername();

	// 	$ptPasswordResult = $this->player_functions->getPlayerPassword($playerId, 1);
	// 	$playerGamePasswordPT = $ptPasswordResult->password;

	// 	$playerDetails = $this->player_functions->getPlayerById($playerId); // Password from player table
	// 	$passwordPT = $this->salt->decrypt($playerDetails['password'], $this->getDeskeyOG());

	// 	$api = $this->game_platform_manager->initApi(1);
	// 	$result = $api->isPlayerExist($playerUsername);

	// 	if ($result['exists'] == false) {
	// 		$api->createPlayer($playerUsername, $playerId, $passwordPT); //create player
	// 	}

	// 	log_message('error', var_export($ptPasswordResult, true));

	// 	$data = array(
	// 		'ptUname' => $playerUsername,
	// 		'ptPass' => $playerGamePasswordPT,
	// 	);

	// 	echo json_encode($data);
	// }

	/**
	 * view sports
	 *
	 * @return void
	 */
	// function sports() {
	// 	$this->loadTemplate('sports', '', '', 'sports');
	// 	$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 	$data['footerData'] = $this->cms_function->getCmsFooterContentData();

	// 	$this->template->write_view('main_content', 'online/view_sports', $data);
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * view keno
	 *
	 * @return void
	 */
	// function keno() {
	// 	$this->loadTemplate('keno', '', '', 'keno');
	// 	$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 	$data['footerData'] = $this->cms_function->getCmsFooterContentData();

	// 	$this->template->write_view('main_content', 'online/view_keno', $data);
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }
	/**
	 * view poker
	 *
	 * @return void
	 */
	// function poker() {
	// 	$this->loadTemplate('poker', '', '', 'poker');
	// 	$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 	$data['footerData'] = $this->cms_function->getCmsFooterContentData();

	// 	$this->template->write_view('main_content', 'online/view_poker', $data);
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * view promotions
	 *
	 * @return void
	 */
	// function promotions($segment = "") {
	// 	$this->loadTemplate('Promotions', '', '', 'promotions');
	// 	$this->template->add_js('resources/js/online/promotions.js');
	// 	$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 	$data['footerData'] = $this->cms_function->getCmsFooterContentData();

	// 	$data['count_all'] = count($this->promo_functions->getAllPromo(null, null));
	// 	$config['base_url'] = "javascript:get_promo_pages(";
	// 	$config['total_rows'] = $data['count_all'];
	// 	$config['per_page'] = '3';
	// 	$config['num_links'] = '1';

	// 	$config['first_tag_open'] = '<li>';
	// 	$config['last_tag_open'] = '<li>';
	// 	$config['next_tag_open'] = '<li>';
	// 	$config['prev_tag_open'] = '<li>';
	// 	$config['num_tag_open'] = '<li>';

	// 	$config['first_tag_close'] = '</li>';
	// 	$config['last_tag_close'] = '</li>';
	// 	$config['next_tag_close'] = '</li>';
	// 	$config['prev_tag_close'] = '</li>';
	// 	$config['num_tag_close'] = '</li>';

	// 	$config['cur_tag_open'] = "<li><span><b>";
	// 	$config['cur_tag_close'] = "</b></span></li>";

	// 	$this->pagination->initialize($config);

	// 	$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
	// 	$data['promo'] = $this->promo_functions->getAllPromo($config['per_page'], $segment);

	// 	$this->template->write_view('main_content', 'online/view_promotions', $data);
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * preview promo cms
	 *
	 * @param   promocmsId
	 * @param   status
	 * @return  redirect
	 */
	// public function viewPromoDetails($promocmsId) {
	// 	$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 	$data['promocms'] = $this->promo_functions->getPromoCmsDetails($promocmsId);
	// 	$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 	//var_dump($data['promocms']);exit();
	// 	$this->loadTemplate('Promotions', '', '', 'promotions');
	// 	$this->template->add_js('resources/js/online/promotions.js');
	// 	$this->template->write_view('main_content', 'online/view_promotion_details', $data);
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * viewGameLoading
	 *
	 * @param   status
	 * @return  redirect
	 */
	// public function viewGameLoading() {
	// 	//var_dump($data);exit();
	// 	$this->loadTemplate('Games', '', '', 'games');
	// 	$this->template->add_js('resources/js/online/promotions.js');
	// 	$this->template->write_view('main_content', 'online/view_gameloading');
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * view featured promotions
	 *
	 * @return void
	 */
	// function featured_promotions($segment = "") {
	// 	$this->loadTemplate('Promotions', '', '', 'promotions');
	// 	$this->template->add_js('resources/js/online/promotions.js');
	// 	$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 	$data['footerData'] = $this->cms_function->getCmsFooterContentData();

	// 	$data['count_all'] = count($this->promo_functions->getAllFeaturedPromo(null, null));
	// 	$config['base_url'] = "javascript:get_featured_promo_pages(";
	// 	$config['total_rows'] = $data['count_all'];
	// 	$config['per_page'] = '3';
	// 	$config['num_links'] = '1';

	// 	$config['first_tag_open'] = '<li>';
	// 	$config['last_tag_open'] = '<li>';
	// 	$config['next_tag_open'] = '<li>';
	// 	$config['prev_tag_open'] = '<li>';
	// 	$config['num_tag_open'] = '<li>';

	// 	$config['first_tag_close'] = '</li>';
	// 	$config['last_tag_close'] = '</li>';
	// 	$config['next_tag_close'] = '</li>';
	// 	$config['prev_tag_close'] = '</li>';
	// 	$config['num_tag_close'] = '</li>';

	// 	$config['cur_tag_open'] = "<li><span><b>";
	// 	$config['cur_tag_close'] = "</b></span></li>";

	// 	$this->pagination->initialize($config);

	// 	$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
	// 	$data['promo'] = $this->promo_functions->getAllFeaturedPromo($config['per_page'], $segment);
	// 	$this->template->write_view('main_content', 'online/view_featured_promotions', $data);
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * view new promotions
	 *
	 * @return void
	 */
	// function new_promotions($segment = "") {
	// 	$this->loadTemplate('Promotions', '', '', 'promotions');
	// 	$this->template->add_js('resources/js/online/promotions.js');
	// 	$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 	$data['footerData'] = $this->cms_function->getCmsFooterContentData();

	// 	$data['count_all'] = count($this->promo_functions->getAllNewPromo(null, null));
	// 	$config['base_url'] = "javascript:get_new_promo_pages(";
	// 	$config['total_rows'] = $data['count_all'];
	// 	$config['per_page'] = '3';
	// 	$config['num_links'] = '1';

	// 	$config['first_tag_open'] = '<li>';
	// 	$config['last_tag_open'] = '<li>';
	// 	$config['next_tag_open'] = '<li>';
	// 	$config['prev_tag_open'] = '<li>';
	// 	$config['num_tag_open'] = '<li>';

	// 	$config['first_tag_close'] = '</li>';
	// 	$config['last_tag_close'] = '</li>';
	// 	$config['next_tag_close'] = '</li>';
	// 	$config['prev_tag_close'] = '</li>';
	// 	$config['num_tag_close'] = '</li>';

	// 	$config['cur_tag_open'] = "<li><span><b>";
	// 	$config['cur_tag_close'] = "</b></span></li>";

	// 	$this->pagination->initialize($config);

	// 	$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
	// 	$data['promo'] = $this->promo_functions->getAllNewPromo($config['per_page'], $segment);

	// 	$this->template->write_view('main_content', 'online/view_new_promotions', $data);
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * view players promotions
	 *
	 * @return void
	 */
	// function all_players_promotions($segment = "") {
	// 	$this->loadTemplate('Promotions', '', '', 'promotions');
	// 	$this->template->add_js('resources/js/online/promotions.js');
	// 	$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 	$data['footerData'] = $this->cms_function->getCmsFooterContentData();

	// 	$data['count_all'] = count($this->promo_functions->getAllPlayersPromo(null, null));
	// 	$config['base_url'] = "javascript:get_players_promo_pages(";
	// 	$config['total_rows'] = $data['count_all'];
	// 	$config['per_page'] = '3';
	// 	$config['num_links'] = '1';

	// 	$config['first_tag_open'] = '<li>';
	// 	$config['last_tag_open'] = '<li>';
	// 	$config['next_tag_open'] = '<li>';
	// 	$config['prev_tag_open'] = '<li>';
	// 	$config['num_tag_open'] = '<li>';

	// 	$config['first_tag_close'] = '</li>';
	// 	$config['last_tag_close'] = '</li>';
	// 	$config['next_tag_close'] = '</li>';
	// 	$config['prev_tag_close'] = '</li>';
	// 	$config['num_tag_close'] = '</li>';

	// 	$config['cur_tag_open'] = "<li><span><b>";
	// 	$config['cur_tag_close'] = "</b></span></li>";

	// 	$this->pagination->initialize($config);

	// 	$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
	// 	$data['promo'] = $this->promo_functions->getAllPlayersPromo($config['per_page'], $segment);

	// 	$this->template->write_view('main_content', 'online/view_players_promotions', $data);
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * view VIP promotions
	 *
	 * @return void
	 */
	// function vip_promotions($segment = "") {
	// 	$this->loadTemplate('Promotions', '', '', 'promotions');
	// 	$this->template->add_js('resources/js/online/promotions.js');
	// 	$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 	$data['footerData'] = $this->cms_function->getCmsFooterContentData();

	// 	$data['count_all'] = count($this->promo_functions->getAllVIPPromo(null, null));
	// 	$config['base_url'] = "javascript:get_vip_promo_pages(";
	// 	$config['total_rows'] = $data['count_all'];
	// 	$config['per_page'] = '3';
	// 	$config['num_links'] = '1';

	// 	$config['first_tag_open'] = '<li>';
	// 	$config['last_tag_open'] = '<li>';
	// 	$config['next_tag_open'] = '<li>';
	// 	$config['prev_tag_open'] = '<li>';
	// 	$config['num_tag_open'] = '<li>';

	// 	$config['first_tag_close'] = '</li>';
	// 	$config['last_tag_close'] = '</li>';
	// 	$config['next_tag_close'] = '</li>';
	// 	$config['prev_tag_close'] = '</li>';
	// 	$config['num_tag_close'] = '</li>';

	// 	$config['cur_tag_open'] = "<li><span><b>";
	// 	$config['cur_tag_close'] = "</b></span></li>";

	// 	$this->pagination->initialize($config);

	// 	$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
	// 	$data['promo'] = $this->promo_functions->getAllVIPPromo($config['per_page'], $segment);

	// 	$this->template->write_view('main_content', 'online/view_vip_promotions', $data);
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * play pt games
	 *
	 * @return void
	 */
	// function playPTGames($gameType = '') {
	// 	switch ($gameType) {
	// 		case 1:$gameType = 'video pokers';
	// 			break;
	// 		case 2:$gameType = 'table and card games';
	// 			break;
	// 		case 3:$gameType = 'scratchcards';
	// 			break;
	// 		case 4:$gameType = 'slot machines';
	// 			break;
	// 		case 5:$gameType = 'live games';
	// 			break;
	// 		case 6:$gameType = 'arcade games';
	// 			break;
	// 		default:$gameType = '';
	// 			break;
	// 	}

	// 	$data['ptGames'] = $this->api_functions->getPTGames($gameType);
	// 	//var_dump($data);exit();
	// 	$this->template->add_js('resources/js/online/games.js');
	// 	$this->loadTemplate('Play Games', '', '', 'games');
	// 	$this->template->write_view('main_content', 'online/view_games', $data);
	// 	$this->template->write_view('sidebar_content', 'template/sidebar_template');
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * preview footer content cms
	 *
	 * @param   footercontentId
	 * @return  redirect
	 */
	// public function viewFootercontentDetails($footercontentId) {
	// 	$data['cmsfootercontent'] = $this->cms_function->getCmsFooterContent($footercontentId);
	// 	$this->load->view('online/view_footercontent_details', $data);
	// }

	/**
	 * preview content cms
	 *
	 * @param   footercontentId
	 * @return  redirect
	 */
	// public function viewContentDetails($contentId) {
	// 	$this->loadTemplate('content', '', '', 'content');
	// 	$data['cmscontent'] = $this->cms_function->getCmsFooterContent($contentId);
	// 	$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 	$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 	//var_dump($data['cmscontent']);exit();
	// 	$this->template->write_view('main_content', 'online/view_content_details', $data);
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * view privacy_policy
	 *
	 * @return void
	 */
	// function privacy_policy() {
	// 	$this->loadTemplate('Privacy Policy', '', '', 'home');

	// 	$this->template->write_view('main_content', 'online/view_privacy_policy');
	// 	$this->template->write_view('sidebar_content', 'template/sidebar_template');
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * view responsible_gaming
	 *
	 * @return void
	 */
	// function responsible_gaming() {
	// 	$this->loadTemplate('Responsible Gaming', '', '', 'home');

	// 	$this->template->write_view('main_content', 'online/view_responsible_gaming');
	// 	$this->template->write_view('sidebar_content', 'template/sidebar_template');
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * view view_terms_and_conditions
	 *
	 * @return void
	 */
	// function terms_and_conditions() {
	// 	$this->loadTemplate('Terms and Conditions', '', '', 'home');

	// 	$this->template->write_view('main_content', 'online/view_terms_and_conditions');
	// 	$this->template->write_view('sidebar_content', 'template/sidebar_template');
	// 	$this->template->write_view('footer_content', 'template/footer_template');
	// 	$this->template->render();
	// }

	/**
	 * test ag
	 *
	 * @return  redirect
	 */
	// public function agTest() {
	// 	//$input = "cagent=AG01/\\\\/method=tc";

	// 	//method=lg, CheckOrCreateGameAccout
	// 	//actype=1, real account
	// 	//actype=2, trial account

	// 	$input = "cagent=" . CAGENT_AG . "/\\\\/loginname=hn4338/\\\\/method=lg/\\\\/actype=0/\\\\/password=a111111";
	// 	$params = $this->salt->encrypt($input, DESKEY_AG);
	// 	$md5Key = MD5($params . MD5KEY_AG);

	// 	echo "URL: " . INVOKEURL_AG . "<br/>";
	// 	echo "Params: " . $params . "<br/>";
	// 	echo "Decode Params: " . $this->salt->decrypt($params, DESKEY_AG) . "<br/>";
	// 	echo "MD5 KEY: " . $md5Key . "<br/>";
	// 	echo "DES KEY: " . DESKEY_AG . "<br/>";
	// 	echo "URL: " . INVOKEURL_AG . 'doBusiness.do?params=' . $params . '&key=' . $md5Key;

	// 	$header = array('Content-Type:application/xml', 'Accept:application/xml');

	// 	$ch = curl_init();
	// 	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	// 	curl_setopt($ch, CURLOPT_URL, INVOKEURL_AG . 'doBusiness.do?params=' . $params . '&key=' . $md5Key);
	// 	curl_setopt($ch, CURLOPT_USERAGENT, 'WEB_LIB_GI_' . CAGENT_AG);
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);

	// 	$html = curl_exec($ch);

	// 	$xml = new SimpleXMLElement($html);
	// 	$value = (string) $xml['info'];

	// 	echo '<br/>info:' . $value . "<br/>";
	// 	print_r($xml);exit();
	// }

	/**
	 * test ag api
	 *
	 * @return  redirect
	 */
	// public function agForwardGame() {
	// 	$this->load->view('aggame/testaggame', 'refresh');
	// }

	/**
	 * test ag api
	 *
	 * @return  redirect
	 */
	// public function agTestReport() {
	// 	$ftp_server = "ftp.agingames.com";
	// 	$ftp_user = "D27.hll999";
	// 	$ftp_pass = "";

	// 	// // set up a connection or die
	// 	// $conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server");

	// 	// // try to login
	// 	// if (@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
	// 	//     echo "Connected as $ftp_user@$ftp_server\n";
	// 	// } else {
	// 	//     echo "Couldn't connect as $ftp_user\n";
	// 	// }

	// 	// $ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
	// 	// $login = ftp_login($ftp_conn, $ftp_user, $ftp_pass);

	// 	// $local_file = "local.zip";
	// 	// $server_file = "agin/20150226/201502260712.xml";
	// 	// // download server file
	// 	// if (ftp_get($ftp_conn, $local_file, $server_file, FTP_ASCII))
	// 	//   {
	// 	//   echo "Successfully written to $local_file.";
	// 	//   }
	// 	// else
	// 	//   {
	// 	//   echo "Error downloading $server_file.";
	// 	//   }

	// 	// // close connection
	// 	// ftp_close($ftp_conn);
	// 	//$server_file = "201502260712.xml";
	// 	$server_file = "ftp://ftp.agingames.com/AGIN/20150226/201502260712.xml";
	// 	$localFilePath = PROMOCMSBANNERPATH;
	// 	$this->download($ftp_server, $ftp_user, $ftp_pass, $server_file, $localFilePath);
	// }

	/**
	 * Handles FTP download of a remote file and stores it in a local file.
	 *
	 * @param   string  $ftpServer          The FTP server to connect to, i.e. ftp.server.com
	 * @param   string  $username           The FTP username
	 * @param   string  $password           The FTP password
	 * @param   string  $remoteFilePath     The path to the remote file if necessary
	 * @param   string  $localFilePath      The full path to the local file
	 * @param   int     $port               The FTP server port, 21 is standard
	 * @return  bool
	 */
	// function download(
	// 	$ftpServer,
	// 	$username,
	// 	$password,
	// 	$remoteFilePath,
	// 	$localFilePath,
	// 	$port = 21,
	// 	$timeout = 30) {
	// 	// set up basic connection
	// 	$conn = ftp_connect($ftpServer, $port, $timeout)
	// 	or die('Could not connect to FTP server.');

	// 	// login with username and password
	// 	if (@ftp_login($conn, $username, $password)) {
	// 		// turn on PASV mode
	// 		@ftp_pasv($conn, TRUE);

	// 		// attempt to download the file, with retries
	// 		return $this->_download($conn, $remoteFilePath, $localFilePath);
	// 	} else {
	// 		if (!empty($conn)) {
	// 			ftp_close($conn);
	// 		}
	// 		die('Access credential problem connecting to FTP server.');
	// 	}

	// 	if (!empty($conn)) {
	// 		ftp_close($conn);
	// 	}

	// 	return false;
	// }

	/**
	 * Handle file downloads with attempted retries. Note that filesize() doesn't
	 * work on all machines. Also, depending on the file type, you may need to swap
	 * out FTP_BINARY for FTP_ASCII.
	 *
	 * @param   resource    $conn
	 * @param   string      $remoteFilePath     The path to the remote file if necessary
	 * @param   string      $localFilePath      The full path to the local file
	 * @param   int     $retries
	 */
	// function _download($conn, $remoteFilePath, $localFilePath, $retries = 1) {
	// 	// check if the local file already exists to determine resume d/l position
	// 	$resumePosition = 0;
	// 	if (file_exists($localFilePath)) {
	// 		$resumePosition = filesize($localFilePath);
	// 	}

	// 	// attempt to download with auto-resume capabilities up to 5 retries
	// 	if (@ftp_get($conn, $localFilePath, $remoteFilePath, FTP_BINARY, $resumePosition)) {
	// 		if (!empty($conn)) {
	// 			ftp_close($conn);
	// 		}
	// 		return true;
	// 	} else if ($retries < 5) {
	// 		// retry up to five times
	// 		return $this->_download($conn, $remoteFilePath, $localFilePath, $retries + 1);
	// 	} else {
	// 		die('There was a problem downloading the remote file.');
	// 	}
	// }

	// function testAGReports() {
	// 	// Initate the download
	// 	// connect and login to FTP server
	// 	$ftp_server = "ftp.agingames.com";
	// 	$ftp_user = "D27.hll999";
	// 	$ftp_pass = "";
	// 	$port = 21;
	// 	$timeout = 30;
	// 	$ftp_conn = ftp_connect($ftp_server, $port, $timeout) or die("Could not connect to $ftp_server");
	// 	$login = ftp_login($ftp_conn, $ftp_user, $ftp_pass);

	// 	//var_dump($ftp->ftp_nlist());

	// 	$server_file = "AGIN/20150226/201502260712.xml";
	// 	$localFilePath = PROMOCMSBANNERPATH;

	// 	$ret = ftp_nb_get($ftp_conn, $server_file, $localFilePath, FTP_BINARY);
	// 	while ($ret == FTP_MOREDATA) {

	// 		// Do whatever you want
	// 		echo ".";

	// 		// Continue downloading...
	// 		$ret = ftp_nb_continue($ftp_conn);
	// 	}
	// 	if ($ret != FTP_FINISHED) {
	// 		echo "There was an error downloading the file...";
	// 		exit(1);
	// 	}
	// }

	// function getXMLReportFile($val) {
	// 	//$val = '/AGIN/20150226/201502260328.xml';
	// 	echo date("Ymd");
	// 	$dir = substr(strrchr($val, "/"), 1);
	// 	return $dir;
	// 	//echo $dir;
	// 	//echo '<br/>';

	// }

	// function getFtpDirectories() {
	// 	// set up basic connection
	// 	$ftp_host = "ftp.agingames.com";
	// 	$ftp_user_name = "D27.hll999";
	// 	$ftp_user_pass = "";

	// 	//Connect
	// 	echo "<br />Connecting to $ftp_host via FTP...";
	// 	$conn = ftp_connect($ftp_host);
	// 	$login = ftp_login($conn, $ftp_user_name, $ftp_user_pass);

	// 	//
	// 	//Enable PASV ( Note: must be done after ftp_login() )
	// 	//
	// 	$mode = ftp_pasv($conn, TRUE);

	// 	//Login OK ?
	// 	if ((!$conn) || (!$login) || (!$mode)) {
	// 		die("FTP connection has failed !");
	// 	}
	// 	echo "<br />Login Ok.<br />";

	// 	//
	// 	//Now run ftp_nlist()
	// 	//
	// 	$fileDateTime = date("Ymd");
	// 	$server_file = "/AGIN/" . $fileDateTime . "/";
	// 	$file_list = ftp_nlist($conn, "/AGIN/" . $fileDateTime);
	// 	foreach ($file_list as $file) {
	// 		$trimmedFile = $this->getXMLReportFile($file);
	// 		$local_file = 'resources/agreport/' . $trimmedFile;
	// 		if (ftp_get($conn, $local_file, $server_file . $trimmedFile, FTP_BINARY)) {
	// 			echo "Successfully written to $local_file\n";
	// 		} else {
	// 			echo "There was a problem\n";
	// 		}
	// 	}

	// 	// to show list of directory
	// 	// $filelist = $this->filecollect($conn);
	// 	// echo "<pre>";
	// 	//   print_r($filelist);
	// 	// echo "</pre>";

	// 	//$local_file = 'resources/agreport/agreport.xml';
	// 	// $local_file = 'resources/agreport/agreport';
	// 	// $server_file = "/AGIN/20150226";
	// 	// // try to download $server_file and save to $local_file
	// 	// if (ftp_get($conn, $local_file, $server_file, FTP_BINARY)) {
	// 	//     echo "Successfully written to $local_file\n";
	// 	// } else {
	// 	//     echo "There was a problem\n";
	// 	// }

	// 	//close
	// 	ftp_close($conn);
	// }

	// function readAGReport() {
	// 	$doc = new DOMDocument();
	// 	$doc->load('employees.xml');

	// 	$employees = $doc->getElementsByTagName("employee");
	// 	foreach ($employees as $employee) {
	// 		$names = $employee->getElementsByTagName("name");
	// 		$name = $names->item(0)->nodeValue;

	// 		$ages = $employee->getElementsByTagName("age");
	// 		$age = $ages->item(0)->nodeValue;

	// 		$salaries = $employee->getElementsByTagName("salary");
	// 		$salary = $salaries->item(0)->nodeValue;

	// 		echo "<b>$name - $age - $salary\n</b><br>";
	// 	}
	// }

	// function filecollect($conn_id, $dir = '.') {
	// 	static $flist = array();
	// 	if ($files = ftp_nlist($conn_id, $dir)) {
	// 		foreach ($files as $file) {
	// 			if (ftp_size($conn_id, $file) == "-1") {
	// 				$this->filecollect($conn_id, $file);
	// 			} else {
	// 				$flist[] = $file;
	// 			}

	// 		}
	// 	}
	// 	return $flist;
	// }

	// // ftp_sync - Copy directory and file structure
	// function ftp_sync($dir) {

	// 	global $conn_id;

	// 	if ($dir != ".") {
	// 		if (ftp_chdir($conn_id, $dir) == false) {
	// 			echo ("Change Dir Failed: $dir<BR>\r\n");
	// 			return;
	// 		}
	// 		if (!(is_dir($dir))) {
	// 			mkdir($dir);
	// 		}

	// 		chdir($dir);
	// 	}

	// 	$contents = ftp_nlist($conn_id, ".");
	// 	foreach ($contents as $file) {

	// 		if ($file == '.' || $file == '..') {
	// 			continue;
	// 		}

	// 		if (@ftp_chdir($conn_id, $file)) {
	// 			ftp_chdir($conn_id, "..");
	// 			ftp_sync($file);
	// 		} else {
	// 			ftp_get($conn_id, $file, $file, FTP_BINARY);
	// 		}

	// 	}

	// 	ftp_chdir($conn_id, "..");
	// 	chdir("..");

	// }

	// /**
	//  * send email
	//  *
	//  * @return  void
	//  */
	// public function sendMessage() {
	// 	$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
	// 	$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
	// 	$this->form_validation->set_rules('subject', 'Subject', 'trim|required|xss_clean');
	// 	$this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean');

	// 	$email_settings = $this->player_functions->getEmail();

	// 	$email_sender = (string) $email_settings['email'];
	// 	$email_sender_pass = (string) $this->salt->decrypt($email_settings['password'], $this->getDeskeyOG());

	// 	if ($this->form_validation->run() == false) {
	// 		$this->contactus();
	// 	} else {

	// 		$data['body'] = lang('pi.1') . ': ' . $this->input->post('name') . "<br/>" .
	// 		lang('pi.6') . ': ' . $this->input->post('email') . "<br/>" .
	// 		lang('cu.6') . ': ' . $this->input->post('subject') . "<br/><br/>" .
	// 		lang('cu.7') . ': ' . $this->input->post('message');

	// 		$data['email_sender'] = $email_sender;
	// 		$data['email_sender_pass'] = $email_sender_pass;
	// 		$data['from'] = $this->input->post('email');
	// 		$data['from_name'] = $this->input->post('name');
	// 		$data['subject'] = lang('header.contactus');

	// 		$this->email_setting->sendEmail($email_sender, $data);

	// 		$message = lang('mess.19');
	// 		$this->alertMessage(1, $message);
	// 		redirect(BASEURL . 'online/contactus', 'refresh');
	// 	}
	// }

	// /*
	//  *   api pattern
	//  *   www.hll999.com/online/agGameReturn/?id=D27AGIN13087&type=12&stamp=1425531997473&feature=b1cc2cd24ea7d77f02c8a2974eeb51d0
	//  *
	//  */

	// public function agGameReturn() {

	// 	// $params = $this->input->get('url');
	// 	// echo 'params: '.$test;exit();
	// 	redirect('online/casino', 'refresh');

	// 	// $md5Key = '';
	// 	// $md5Result = MD5($id.$type.$stamp.$md5Key);
	// 	// echo '$md5Result: '.$md5Result;
	// 	// if($feature == $md5Result){
	// 	//     if($type == 12){
	// 	//         redirect('online/casino','refresh');
	// 	//     }
	// 	// } else {
	// 	//     return 'Error!';
	// 	// }

	// }

	// public function agReturn() {
	// 	$id = $this->input->get('id');
	// 	$type = $this->input->get('type');
	// 	$stamp = $this->input->get('stamp');
	// 	$feature = $this->input->get('feature');
	// 	$result = MD5($id . $type . $stamp . MD5KEY_HLL999);
	// 	// echo $feature;
	// 	// echo '<br/>';
	// 	// echo $result;
	// 	// echo '<br/>';
	// 	if ($feature == $result) {
	// 		//echo 'success';
	// 		if ($type == 12) {
	// 			redirect('online/casino', 'refresh');
	// 		}
	// 	}
	// }

	// public function playerSettings() {
	// 	if (!$this->authentication->isLoggedIn()) {
	// 		redirect(BASEURL . 'auth/login');
	// 	} else {
	// 		$player_id = $this->authentication->getPlayerId();
	// 		$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 		$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 		$data['current_promo'] = "";
	// 		$data['player'] = $this->player_functions->getPlayerById($player_id);
	// 		//var_dump($data['player']);exit();
	// 		$data['bank_details'] = $this->player_functions->getBankDetails($player_id);

	// 		if (!empty($this->session->userdata('promoCode'))) {
	// 			$promo = $this->player_functions->checkPromoCodeExist($this->session->userdata('promoCode'));
	// 			$data['current_promo'] = $this->player_functions->checkIfAlreadyGetPromo($player_id, $promo['promoId']);
	// 		}

	// 		$this->loadTemplate('Player Settings', '', '', 'settings');
	// 		$this->template->write_view('main_content', 'player/view_player_settings', $data);
	// 		$this->template->render();
	// 	}
	// }

	// /**
	//  * Set rules for updating player details
	//  *
	//  * @return  redirect page
	//  */
	// public function formRulesEditPlayer() {

	// 	if ($this->player_functions->checkRegisteredFieldsIfRequired('Language') == 0) {
	// 		$this->form_validation->set_rules('language', 'Language', 'trim|required|xss_clean');
	// 	} else {
	// 		$this->form_validation->set_rules('language', 'Language', 'trim|xss_clean');
	// 	}

	// 	if ($this->player_functions->checkRegisteredFieldsIfRequired('Contact Number') == 0) {
	// 		$this->form_validation->set_rules('contact_number', 'Contact Number', 'trim|required|xss_clean');
	// 	} else {
	// 		$this->form_validation->set_rules('contact_number', 'Contact Number', 'trim|xss_clean');
	// 	}

	// 	if ($this->player_functions->checkRegisteredFieldsIfRequired('Instant Message 1') == 0) {
	// 		$this->form_validation->set_rules('im_type', 'IM 1', 'trim|required|xss_clean');
	// 	} else {
	// 		$this->form_validation->set_rules('im_type', 'IM 1', 'trim|xss_clean');
	// 	}

	// 	if ($this->player_functions->checkRegisteredFieldsIfRequired('Instant Message 2') == 0) {
	// 		$this->form_validation->set_rules('im_type2', 'IM 2', 'trim|required|xss_clean');
	// 	} else {
	// 		$this->form_validation->set_rules('im_type2', 'IM 2', 'trim|xss_clean');
	// 	}

	// 	if ($this->player_functions->checkRegisteredFieldsIfRequired('Nationality') == 0) {
	// 		$this->form_validation->set_rules('citizenship', 'Nationality', 'trim|required|xss_clean');
	// 	} else {
	// 		$this->form_validation->set_rules('citizenship', 'Nationality', 'trim|xss_clean');
	// 	}

	// 	if ($this->player_functions->checkRegisteredFieldsIfRequired('BirthPlace') == 0) {
	// 		$this->form_validation->set_rules('birthplace', 'Birthplace', 'trim|required|xss_clean');
	// 	} else {
	// 		$this->form_validation->set_rules('birthplace', 'Birthplace', 'trim|xss_clean');
	// 	}

	// 	if (!empty($this->input->post('im_type'))) {
	// 		if ($this->input->post('im_type') == 'QQ') {
	// 			$this->form_validation->set_rules('im_account', 'IM Account 1', 'trim|required|xss_clean|numeric');
	// 		} elseif ($this->input->post('im_type') == 'Skype' || $this->input->post('im_type') == 'MSN') {
	// 			$this->form_validation->set_rules('im_account', 'IM Account 1', 'trim|required|xss_clean|valid_email');
	// 		} else {
	// 			$this->form_validation->set_rules('im_account', 'IM Account 1', 'trim|required|xss_clean');
	// 		}
	// 	}

	// 	if (!empty($this->input->post('im_type2'))) {
	// 		if ($this->input->post('im_type2') == 'QQ') {
	// 			$this->form_validation->set_rules('im_account2', 'IM Account 2', 'trim|required|xss_clean|numeric');
	// 		} elseif ($this->input->post('im_type2') == 'Skype' || $this->input->post('im_type2') == 'MSN') {
	// 			$this->form_validation->set_rules('im_account2', 'IM Account 2', 'trim|required|xss_clean|valid_email');
	// 		} else {
	// 			$this->form_validation->set_rules('im_account2', 'IM Account 2', 'trim|required|xss_clean');
	// 		}
	// 	}
	// }

	// public function postEditPlayer() {
	// 	$this->formRulesEditPlayer();

	// 	if (!empty($this->input->post('im_type'))) {
	// 		if ($this->input->post('im_type') == 'QQ') {
	// 			$this->form_validation->set_rules('im_account', 'IM 1', 'trim|required|xss_clean|numeric');
	// 		} elseif ($this->input->post('im_type') == 'Skype' || $this->input->post('im_type') == 'MSN') {
	// 			$this->form_validation->set_rules('im_account', 'IM 1', 'trim|required|xss_clean');
	// 		} else {
	// 			$this->form_validation->set_rules('im_account', 'IM 1', 'trim|required|xss_clean');
	// 		}
	// 	}

	// 	if (!empty($this->input->post('im_type2'))) {
	// 		if ($this->input->post('im_type2') == 'QQ') {
	// 			$this->form_validation->set_rules('im_account2', 'IM 2', 'trim|required|xss_clean|numeric');
	// 		} elseif ($this->input->post('im_type2') == 'Skype' || $this->input->post('im_type2') == 'MSN') {
	// 			$this->form_validation->set_rules('im_account2', 'IM 2', 'trim|required|xss_clean');
	// 		} else {
	// 			$this->form_validation->set_rules('im_account2', 'IM 2', 'trim|required|xss_clean');
	// 		}
	// 	}

	// 	if ($this->form_validation->run() == false) {
	// 		$message = lang('notify.22');
	// 		$this->alertMessage(2, $message);
	// 		$this->playerSettings();
	// 	} else {
	// 		$player_id = $this->authentication->getPlayerId();
	// 		$origdata = $this->player_functions->getPlayerById($player_id);

	// 		$country = $this->input->post('country');
	// 		$address = $this->input->post('address');
	// 		$city = $this->input->post('city');
	// 		$zipcode = $this->input->post('zipcode');
	// 		$language = $this->input->post('language');
	// 		$citizenship = $this->input->post('citizenship');
	// 		$contact_number = $this->input->post('contact_number');
	// 		$im_account = $this->input->post('im_account');
	// 		$im_type = $this->input->post('im_type');
	// 		$im_account2 = $this->input->post('im_account2');
	// 		$im_type2 = $this->input->post('im_type2');
	// 		$today = date("Y-m-d H:i:s");

	// 		$player = $this->player_functions->getPlayerById($this->authentication->getPlayerId());

	// 		if (!empty($im_type) && !empty($im_account) && !empty($im_type2) && !empty($im_account2) && $im_account == $im_account2) {
	// 			//$message = "Your IM Account 1 should be different to IM Account 2";
	// 			$message = lang('notify.23');
	// 			$this->alertMessage(2, $message);
	// 			$this->playerSettings();
	// 		} else {
	// 			$data = array(
	// 				'updatedOn' => $today,
	// 			);

	// 			$this->player_functions->editPlayer($data, $this->authentication->getPlayerId());

	// 			//$new_player = $this->player_functions->checkUsernameExist($username);

	// 			$data = array(
	// 				'language' => $language,
	// 				'country' => $country,
	// 				'address' => $address,
	// 				'city' => $city,
	// 				'zipcode' => $zipcode,
	// 				'contactNumber' => $contact_number,
	// 				'imAccount' => $im_account,
	// 				'imAccountType' => $im_type,
	// 				'imAccount2' => $im_account2,
	// 				'imAccountType2' => $im_type2,
	// 				'citizenship' => $citizenship,
	// 			);

	// 			//save changes to playerupdatehistory
	// 			$change = $this->checkPlayerChanges($origdata, $data);
	// 			$changes = array(
	// 				'playerId' => $player_id,
	// 				'changes' => $change,
	// 				'createdOn' => date('Y-m-d H:i:s'),
	// 			);
	// 			$this->player_functions->savePlayerChanges($changes);

	// 			//$this->player_functions->compareChanges($data, $this->authentication->getPlayerId());
	// 			$this->player_functions->editPlayerDetails($data, $this->authentication->getPlayerId());

	// 			//$message = "Your account has been edited";
	// 			$message = lang('notify.24');
	// 			$this->alertMessage(1, $message);
	// 			redirect(BASEURL . 'online/playerSettings/' . $this->authentication->getPlayerId());
	// 		}
	// 	}
	// }

	// public function checkPlayerChanges($origdata, $data) {
	// 	$change = array();

	// 	foreach ($data as $key => $value) {
	// 		if ($value != $origdata[$key] && ($key != 'imAccountType' || $key != 'imAccountType2')) {
	// 			array_push($change, $key);
	// 		}
	// 	}

	// 	$changes = implode(', ', $change);
	// 	return $changes;
	// }

	// /**
	//  * view cancel promo
	//  *
	//  * @return void
	//  */
	// public function changePassword() {
	// 	if (!$this->authentication->isLoggedIn()) {
	// 		redirect(BASEURL . 'auth/login');
	// 	} else {
	// 		$data['footerlinks'] = $this->cms_function->getCmsFooterLinks();
	// 		$data['footerData'] = $this->cms_function->getCmsFooterContentData();
	// 		$this->loadTemplate('Player Settings', '', '', 'settings');
	// 		$this->template->write_view('main_content', 'player/change_password', $data);
	// 		$this->template->render();
	// 	}
	// }

	// /**
	//  * view cancel promo
	//  *
	//  * @return void
	//  */
	// public function postResetPassword() {
	// 	$this->form_validation->set_rules('old_password', 'Current Password', 'trim|required|xss_clean');
	// 	$this->form_validation->set_rules('password', 'New Password', 'trim|required|xss_clean');
	// 	$this->form_validation->set_rules('cpassword', 'Confirm Password', 'trim|required|xss_clean|matches[password]');

	// 	if ($this->form_validation->run() == false) {
	// 		$message = lang('notify.25');
	// 		$this->alertMessage(2, $message);
	// 		$this->changePassword();
	// 	} else {
	// 		$old_password = $this->input->post('old_password');
	// 		$password = $this->input->post('password');

	// 		$check = $this->player_functions->isValidPassword($this->authentication->getPlayerId(), $old_password);
	// 		if (!$check) {
	// 			$message = lang('notify.26');
	// 			$this->alertMessage(2, $message);
	// 			$this->changePassword();
	// 		} else {
	// 			$data = array('password' => $password);
	// 			$this->player_functions->resetPassword($this->authentication->getPlayerId(), $data);

	// 			$email = $this->player_functions->getEmail(); // Email Address Sender
	// 			$playerDetails = $this->player_functions->getPlayerById($this->authentication->getPlayerId());

	// 			$data['body'] = "<html><body>
 //                    <span style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('cp.emailMsgPassChg1') . ' ' . $playerDetails['lastName'] . ' ' . $playerDetails['firstName'] . "!<span><br/><br/>
 //                    <span style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('cp.emailMsgPassChg8') . "</span><br/>
 //                    <span style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('cp.emailMsgPassChg3') . ": <b>" . $password . "</b></span><br/>
 //                    <span style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('cp.emailMsgPassChg9') . "</span><br/><br/><br/>
 //                    <span style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('cp.emailMsgPassChg5') . ",</span><br/>
 //                    <span style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('cp.emailMsgPassChg6') . "</span><br/><br/><br/>
 //                    <span style='color:rgb(57, 132, 198);font-size:13px;font-family:Verdana;'>" . lang('cp.emailMsgPassChg7') . "</span>
 //                    </body></html>";

	// 			$data['email_sender'] = $email['email'];
	// 			$data['email_sender_pass'] = $this->salt->decrypt($email['password'], $this->getDeskeyOG());
	// 			$data['from_name'] = 'Paramount TEAM';
	// 			$data['subject'] = lang('cp.changePass');

	// 			$this->email_setting->sendEmail($playerDetails['email'], $data);

	// 			$message = lang('notify.27');
	// 			$this->alertMessage(1, $message);
	// 			redirect(BASEURL . 'smartcashier/viewCashier');
	// 		}
	// 	}
	// }

	// private function getRandomSequence() {
	// 	$seed = str_split('0123456789123456'); // and any other characters
	// 	shuffle($seed); // probably optional since array_is randomized; this may be redundant
	// 	$randomNum = '';
	// 	foreach (array_rand($seed, 16) as $k) {
	// 		$randomNum .= $seed[$k];
	// 	}

	// 	return $randomNum;
	// }
}