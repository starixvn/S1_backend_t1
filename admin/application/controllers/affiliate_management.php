<?php

require_once dirname(__FILE__) . '/BaseController.php';

require_once dirname(__FILE__) . '/modules/affiliate_statistics_module.php';
require_once dirname(__FILE__) . '/modules/affiliate_bank_info_module.php';
require_once dirname(__FILE__) . '/modules/affiliate_banner_module.php';
require_once dirname(__FILE__) . '/modules/affiliate_list_module.php';
require_once dirname(__FILE__) . '/modules/affiliate_tag_module.php';
require_once dirname(__FILE__) . '/modules/affiliate_terms_module.php';

/**
 * General behaviors include:
 * * List of all affiliate
 * * Searches affiliates
 * * Affiiliates information
 * * personal information
 * * contact information
 * * withdrawal information
 * * bank information
 * * payment history
 * * share settings
 * * affiliate commission setting
 * * sub affiliate commission setting
 * * affiliate tracking code
 * * monthly earnings
 * * Add Tag to the selected affiliate
 * * Register Affiliate
 * * Export data to Excel
 * * Loads Template
 * * Make withdraw
 * * Make Deposit
 * * Checks Required Fields
 * * Validates entered Affiliate's Username
 * * Searches affiliate who's paid
 * * Affiliate username will redirect you to the affiliate's user information
 * * Displays affiliates data who's paid
 * * Searches monthly earnings
 * * Export data to Excel
 * *
 * @see Redirect redirect to affiliate list page
 *
 * @category Affiliate Manangement
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Affiliate_management extends BaseController {

	use affiliate_statistics_module;
	use affiliate_bank_info_module;
	use affiliate_banner_module;
	use affiliate_list_module;
	use affiliate_tag_module;
	use affiliate_terms_module;

	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library(array('permissions', 'form_validation', 'template', 'pagination', 'affiliate_manager', 'report_functions', 'salt'));
		$this->load->model(['affiliatemodel']);
		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
	}

	/**
	 * overview : template loading
	 *
	 * detail : load all javascript/css resources, customize head contents
	 *
	 * @param string $title
	 * @param string $description
	 * @param string $keywords
	 * @param string $activenav
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());

		$this->template->write_view('sidebar', 'affiliate_management/sidebar');
		$this->template->add_js('resources/js/affiliate_management/affiliate_management.js');

		$this->template->add_js('resources/js/datatables.min.js');
		$this->template->add_js('resources/js/mustache.min.js');
		$this->template->add_js('resources/datatables/Buttons-1.1.0/js/buttons.html5.min.js');
		$this->template->add_js('resources/datatables/Buttons-1.1.0/js/buttons.flash.min.js');

		$this->template->add_css('resources/css/general/style.css');
		$this->template->add_css('resources/css/datatables.min.css');
	}

	/**
	 * overview : error access
	 *
	 * detail : show error message if user can't access the page
	 */
	private function error_access() {
		$this->loadTemplate('Affiliate Management', '', '', 'affiliate');
		$affUrl = $this->utils->activeAffiliateSidebar();
		$data['redirect'] = $affUrl;

		$message = lang('con.aff01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}

	/**
	 * overview : traffic status
	 *
	 * detail : show player's traffic information under affiliate
	 *
	 * @param int $affiliate_id 	affiliate_id
	 * @return	void
	 */
	public function trafficStats($affiliate_id) {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			$number_traffic_list = '';
			$sort_by = '';
			$in = '';

			if ($this->session->userdata('number_traffic_list')) {
				$number_traffic_list = $this->session->userdata('number_traffic_list');
			} else {
				$number_traffic_list = 5;
			}

			if ($this->session->userdata('traffic_sort_by')) {
				$sort_by = $this->session->userdata('traffic_sort_by');
			} else {
				$sort_by = 't.start_date';
			}

			if ($this->session->userdata('traffic_in')) {
				$in = $this->session->userdata('traffic_in');
			} else {
				$in = 'desc';
			}

			$sort = array(
				'sortby' => $sort_by,
				'in' => $in,
				'affiliate_id' => $affiliate_id,
			);

			$this->session->set_userdata('affiliateId', $affiliate_id);

			$data['count_all'] = count($this->affiliate_manager->getTrafficStats(null, null, $sort));
			$config['base_url'] = "javascript:displayTrafficStats(";
			$config['total_rows'] = $data['count_all'];
			$config['per_page'] = $number_traffic_list;
			$config['num_links'] = '1';

			$config['first_tag_open'] = '<li>';
			$config['last_tag_open'] = '<li>';
			$config['next_tag_open'] = '<li>';
			$config['prev_tag_open'] = '<li>';
			$config['num_tag_open'] = '<li>';

			$config['first_tag_close'] = '</li>';
			$config['last_tag_close'] = '</li>';
			$config['next_tag_close'] = '</li>';
			$config['prev_tag_close'] = '</li>';
			$config['num_tag_close'] = '</li>';

			$config['cur_tag_open'] = "<li><span><b>";
			$config['cur_tag_close'] = "</b></span></li>";

			$this->pagination->initialize($config);

			$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
			$data['current_page'] = floor(($this->uri->segment(4) / $config['per_page']) + 1);
			$data['today'] = date("Y-m-d H:i:s");

			$data['traffic'] = $this->affiliate_manager->getTrafficStats(null, null, $sort);
			$data['affiliate'] = $this->affiliate_manager->getAffiliateById($affiliate_id);

			$this->template->write_view('main_content', 'affiliate_management/affiliates/view_traffic', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : display traffic stats
	 *
	 * detail : show player's traffic information under affiliate pagination
	 *
	 * @param string $segment
	 * @return	void
	 */
	public function displayTrafficStats($segment = "") {
		$number_traffic_list = '';
		$sort_by = '';
		$in = '';

		if ($this->session->userdata('number_traffic_list')) {
			$number_traffic_list = $this->session->userdata('number_traffic_list');
		} else {
			$number_traffic_list = 5;
		}

		if ($this->session->userdata('traffic_sort_by')) {
			$sort_by = $this->session->userdata('traffic_sort_by');
		} else {
			$sort_by = 't.start_date';
		}

		if ($this->session->userdata('traffic_in')) {
			$in = $this->session->userdata('traffic_in');
		} else {
			$in = 'desc';
		}

		$affiliate_id = $this->session->userdata('affiliateId');

		$sort = array(
			'sortby' => $sort_by,
			'in' => $in,
			'affiliate_id' => $affiliate_id,
		);

		$data['count_all'] = count($this->affiliate_manager->getTrafficStats(null, null, $sort));
		$config['base_url'] = "javascript:displayTrafficStats(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = $number_traffic_list;
		$config['num_links'] = '1';

		$config['first_tag_open'] = '<li>';
		$config['last_tag_open'] = '<li>';
		$config['next_tag_open'] = '<li>';
		$config['prev_tag_open'] = '<li>';
		$config['num_tag_open'] = '<li>';

		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['current_page'] = floor(($this->uri->segment(4) / $config['per_page']) + 1);
		$data['today'] = date("Y-m-d H:i:s");

		$data['traffic'] = $this->affiliate_manager->getTrafficStats(null, $segment, $sort);
		$data['affiliate'] = $this->affiliate_manager->getAffiliateById($affiliate_id);

		$this->load->view('affiliate_management/affiliates/ajax_view_traffic', $data);
	}

	/**
	 * overview : traffic sort page
	 *
	 * $param int $affiliate_id 	affiliate_id
	 * @return	void
	 */
	public function trafficSortPage($affiliate_id) {
		$sort_by = $this->input->post('sort_by');
		$this->session->set_userdata('traffic_sort_by', $sort_by);

		$in = $this->input->post('in');
		$this->session->set_userdata('traffic_in', $in);

		$number_traffic_list = $this->input->post('number_traffic_list');
		$this->session->set_userdata('number_traffic_list', $number_traffic_list);

		redirect('affiliate_management/trafficStats/' . $affiliate_id);
	}

	/**
	 * overview : traffic search page
	 *
	 * $param int $affiliate_id 	affiliate_id
	 * @return	void
	 */
	public function trafficSearchPage($affiliate_id) {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			$signup_range = null;

			if ($this->input->post('start_date') && $this->input->post('end_date') && $this->input->post('sign_time_period') == 'specify') {
				if ($this->input->post('start_date') < $this->input->post('end_date')) {
					$signup_range = "'" . $this->input->post('start_date') . "' AND '" . $this->input->post('end_date') . "'";
				} else {
					$message = lang('con.aff02');
					$this->alertMessage(2, $message);
				}
			} else {
				$period = $this->input->post('sign_time_period');
			}

			$search = array(
				'sign_time_period' => $period,
				'signup_range' => $signup_range,
				'affiliate_id' => $affiliate_id,
			);

			if (!array_filter($search)) {
				redirect('affiliate_management/trafficStats/' . $affiliate_id);
			} else {
				$number_traffic_list = '';

				if ($this->session->userdata('number_traffic_list')) {
					$number_traffic_list = $this->session->userdata('number_traffic_list');
				} else {
					$number_traffic_list = 5;
				}

				$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

				$data['count_all'] = count($this->affiliate_manager->searchTrafficStats(null, null, $search));
				$config['base_url'] = "javascript:displayTrafficStats(";
				$config['total_rows'] = $data['count_all'];
				$config['per_page'] = $number_traffic_list;
				$config['num_links'] = '1';

				$config['first_tag_open'] = '<li>';
				$config['last_tag_open'] = '<li>';
				$config['next_tag_open'] = '<li>';
				$config['prev_tag_open'] = '<li>';
				$config['num_tag_open'] = '<li>';

				$config['first_tag_close'] = '</li>';
				$config['last_tag_close'] = '</li>';
				$config['next_tag_close'] = '</li>';
				$config['prev_tag_close'] = '</li>';
				$config['num_tag_close'] = '</li>';

				$config['cur_tag_open'] = "<li><span><b>";
				$config['cur_tag_close'] = "</b></span></li>";

				$this->pagination->initialize($config);

				$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
				$data['current_page'] = floor(($this->uri->segment(4) / $config['per_page']) + 1);
				$data['today'] = date("Y-m-d H:i:s");

				$data['traffic'] = $this->affiliate_manager->searchTrafficStats(null, null, $search);
				$data['affiliate'] = $this->affiliate_manager->getAffiliateById($affiliate_id);

				$this->template->write_view('main_content', 'affiliate_management/affiliates/view_traffic', $data);
				$this->template->render();
			}
		}
	}

	/**
	 * overview : players click
	 *
	 * $param int $traffic_id		traffic_id
	 * @return	void
	 */
	public function players($traffic_id) {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			$data['players'] = $this->affiliate_manager->getPlayers($traffic_id);
			$data['affiliate_id'] = $this->session->userdata('affiliateId');

			$this->load->view('affiliate_management/affiliates/view_players', $data);
		}
	}

	/**
	 * overview : affiliate terms
	 *
	 * $param int $affiliate_id 	affiliate_id
	 * @return	void
	 */
	public function editAffiliateTerms($affiliate_id) {
		$game = $this->affiliate_manager->getGame();

		foreach ($game as $key => $value) {
			$this->form_validation->set_rules('percentage-' . $value['gameId'], $value['game'] . ' Percentage', 'trim|required|xss_clean|numeric|callback_checkPercentage');
			$this->form_validation->set_rules('active_players-' . $value['gameId'], $value['game'] . ' Active Players', 'trim|required|xss_clean|numeric|callback_checkActive');
		}

		$this->form_validation->set_rules('status', 'Allowed Game', 'trim|xss_clean|callback_checkAllowedGame');

		if ($this->form_validation->run() == false) {
			$message = lang('con.aff10');
			$this->alertMessage(2, $message);

			$this->userInformation($affiliate_id);
		} else {
			foreach ($game as $key => $game_value) {
				$percentage = $this->input->post('percentage-' . $game_value['gameId']);
				$active_players = $this->input->post('active_players-' . $game_value['gameId']);
				$status = $this->input->post('status-' . $game_value['gameId']);

				$options = $this->affiliate_manager->getAffiliateOptions($affiliate_id, $game_value['gameId']);

				if (empty($options)) {
					$data = array(
						'affiliateId' => $affiliate_id,
						'optionsType' => 'percentage',
						'optionsValue' => $percentage,
						'gameId' => $game_value['gameId'],
						'createdOn' => date('Y-m-d H:i:s'),
						'updatedOn' => date('Y-m-d H:i:s'),
						'status' => ($status == null) ? '1' : '0',
					);
					$this->affiliate_manager->insertAffiliateTerms($data);

					$data = array(
						'affiliateId' => $affiliate_id,
						'optionsType' => 'active_players',
						'optionsValue' => $active_players,
						'gameId' => $game_value['gameId'],
						'createdOn' => date('Y-m-d H:i:s'),
						'updatedOn' => date('Y-m-d H:i:s'),
						'status' => ($status == null) ? '1' : '0',
					);
					$this->affiliate_manager->insertAffiliateTerms($data);
				} else {
					foreach ($options as $key => $value) {
						if ($value['optionsType'] == 'percentage') {
							$data = array(
								'affiliateId' => $affiliate_id,
								'optionsType' => 'percentage',
								'optionsValue' => $percentage,
								'gameId' => $game_value['gameId'],
								'updatedOn' => date('Y-m-d H:i:s'),
								'status' => ($status == null) ? '1' : '0',
							);
						} elseif ($value['optionsType'] == 'active_players') {
							$data = array(
								'affiliateId' => $affiliate_id,
								'optionsType' => 'active_players',
								'optionsValue' => $active_players,
								'gameId' => $game_value['gameId'],
								'updatedOn' => date('Y-m-d H:i:s'),
								'status' => ($status == null) ? '1' : '0',
							);
						}

						$this->affiliate_manager->editAffiliateTerms($data, $value['affiliateOptionsId']);
					}
				}
			}

			$message = lang('con.aff12');
			$this->alertMessage(1, $message);

			redirect('affiliate_management/userInformation/' . $affiliate_id, 'refresh');
		}
	}

	/**
	 * overview : check allowed game
	 *
	 * detail : callback for editAffiliateTerms
	 *
	 * @return	redirect
	 */
	public function checkAllowedGame() {
		$game = $this->affiliate_manager->getGame();
		$check = false;

		foreach ($game as $key => $value) {
			$status = $this->input->post('status-' . $value['gameId']);

			if ($status != null) {
				$check = true;
			}
		}

		if ($check == false) {
			$this->form_validation->set_message('checkAllowedGame', 'Please select at least one game');
			return false;
		}
		return true;
	}

	/**
	 * overview : upload earnings
	 *
	 * @param 	int $affiliate_id
	 * @return	redirect page
	 */
	public function uploadEarnings($affiliate_id) {
		if ($_FILES["file"]["size"] == 0) {
			$message = lang('con.aff17');
			$this->alertMessage(2, $message);

			$this->session->set_userdata("earnings_upload", "File is required. Please choose a file before uploading.");

			$this->userInformation($affiliate_id);
		} else {
			$mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');

			if (in_array($_FILES['file']['type'], $mimes)) {
				$filename = $_FILES["file"]["tmp_name"];

				$this->affiliate_manager->uploadMonthlyEarnings($affiliate_id, $filename);

				$message = lang('con.aff18');
				$this->alertMessage(1, $message);

				$this->session->set_userdata("earnings_upload", null);

				redirect('affiliate_management/userInformation/' . $affiliate_id, 'refresh');
			} else {
				$message = lang('con.aff19');
				$this->alertMessage(2, $message);

				$this->session->set_userdata("earnings_upload", "File type should be csv. Please choose a csv file before uploading.");

				$this->userInformation($affiliate_id);
			}
		}
	}

	/**
	 * overview : reset affiliate term
	 *
	 * @param 	int $affiliate_id		affiliate_id
	 * @return	redirect page
	 */
	public function resetAffiliateTerm($affiliate_id) {
		$this->affiliate_manager->deleteAffiliateOptions($affiliate_id);

		$message = lang('con.aff48');
		$this->alertMessage(1, $message);
		redirect('affiliate_management/userInformation/' . $affiliate_id, 'refresh');
	}

	/**
	 * overview : get monthly earnings
	 *
	 * @param $segment
	 * $param int $affiliate_id		affiliate_id
	 * @return	void
	 */
	public function getMonthlyEarnings($segment = '', $affiliate_id) {
		$data['count_all'] = count($this->affiliate_manager->getMonthlyEarningsById($affiliate_id, null, null));
		$config['base_url'] = "javascript:getMonthlyEarnings(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 10;
		$config['num_links'] = '1';

		$config['first_tag_open'] = '<li>';
		$config['last_tag_open'] = '<li>';
		$config['next_tag_open'] = '<li>';
		$config['prev_tag_open'] = '<li>';

		$config['num_tag_open'] = '<li>';

		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['earnings'] = $this->affiliate_manager->getMonthlyEarningsById($affiliate_id, null, $segment);

		$this->load->view('affiliate_management/affiliates/ajax_monthly_earnings', $data);
	}

	/**
	 * overview : payment history
	 *
	 * @param $segment
	 * @param int $affiliate_id		affiliate_id
	 * @return	void
	 */
	public function getPayments($segment = '', $affiliate_id) {
		$data['count_all'] = count($this->affiliate_manager->getPaymentsById($affiliate_id, null, null));
		$config['base_url'] = "javascript:getPayments(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 10;
		$config['num_links'] = '1';

		$config['first_tag_open'] = '<li>';
		$config['last_tag_open'] = '<li>';
		$config['next_tag_open'] = '<li>';
		$config['prev_tag_open'] = '<li>';

		$config['num_tag_open'] = '<li>';

		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$this->pagination->initialize($config);

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
		$data['payment'] = $this->affiliate_manager->getPaymentsById($affiliate_id, null, $segment);

		$this->load->view('affiliate_management/affiliates/ajax_payment_history', $data);
	}

	/* ****** End of Affiliate Lists ****** */

	/* ****** Affiliate Payment ****** */

	/**
	 * overview : view affilliate payments
	 *
	 * @return	void
	 */
	public function viewAffiliatePayment() {
		if (!$this->permissions->checkPermissions('affiliate_payments')) {
			$this->error_access();
		} else {
			redirect('affiliate_management/paymentSearchPage');
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			$number_payment_list = '';
			$sort_by = '';
			$in = '';

			if ($this->session->userdata('number_payment_list')) {
				$number_payment_list = $this->session->userdata('number_payment_list');
			} else {
				$number_payment_list = 5;
			}

			if ($this->session->userdata('payment_sort_by')) {
				$sort_by = $this->session->userdata('payment_sort_by');
			} else {
				$sort_by = 'p.createdOn';
			}

			if ($this->session->userdata('payment_in')) {
				$in = $this->session->userdata('payment_in');
			} else {
				$in = 'desc';
			}

			$sort = array(
				'sortby' => $sort_by,
				'in' => $in,
			);

			$data['count_all'] = count($this->affiliate_manager->getPaymentHistory($sort, null, null));
			$config['base_url'] = "javascript:displayPayments(";
			$config['total_rows'] = $data['count_all'];
			$config['per_page'] = $number_payment_list;
			$config['num_links'] = '1';

			$config['first_tag_open'] = '<li>';
			$config['last_tag_open'] = '<li>';
			$config['next_tag_open'] = '<li>';
			$config['prev_tag_open'] = '<li>';
			$config['num_tag_open'] = '<li>';

			$config['first_tag_close'] = '</li>';
			$config['last_tag_close'] = '</li>';
			$config['next_tag_close'] = '</li>';
			$config['prev_tag_close'] = '</li>';
			$config['num_tag_close'] = '</li>';

			$config['cur_tag_open'] = "<li><span><b>";
			$config['cur_tag_close'] = "</b></span></li>";

			$this->pagination->initialize($config);

			$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
			$data['payments'] = $this->affiliate_manager->getPaymentHistory($sort, null, null);

			$this->template->write_view('main_content', 'affiliate_management/payments/view_payments', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : display payments page
	 *
	 * @param $segment
	 * @return	void
	 */
	public function displayPayments($segment = '') {
		if (!$this->permissions->checkPermissions('affiliate_payments')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			$number_payment_list = '';
			$sort_by = '';
			$in = '';

			if ($this->session->userdata('number_payment_list')) {
				$number_payment_list = $this->session->userdata('number_payment_list');
			} else {
				$number_payment_list = 5;
			}

			if ($this->session->userdata('payment_sort_by')) {
				$sort_by = $this->session->userdata('payment_sort_by');
			} else {
				$sort_by = 'p.createdOn';
			}

			if ($this->session->userdata('payment_in')) {
				$in = $this->session->userdata('payment_in');
			} else {
				$in = 'desc';
			}

			$sort = array(
				'sortby' => $sort_by,
				'in' => $in,
			);

			$data['count_all'] = count($this->affiliate_manager->getPaymentHistory($sort, null, null));
			$config['base_url'] = "javascript:displayPayments(";
			$config['total_rows'] = $data['count_all'];
			$config['per_page'] = $number_payment_list;
			$config['num_links'] = '1';

			$config['first_tag_open'] = '<li>';
			$config['last_tag_open'] = '<li>';
			$config['next_tag_open'] = '<li>';
			$config['prev_tag_open'] = '<li>';
			$config['num_tag_open'] = '<li>';

			$config['first_tag_close'] = '</li>';
			$config['last_tag_close'] = '</li>';
			$config['next_tag_close'] = '</li>';
			$config['prev_tag_close'] = '</li>';
			$config['num_tag_close'] = '</li>';

			$config['cur_tag_open'] = "<li><span><b>";
			$config['cur_tag_close'] = "</b></span></li>";

			$this->pagination->initialize($config);

			$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
			$data['payments'] = $this->affiliate_manager->getPaymentHistory($sort, null, $segment);

			$this->load->view('affiliate_management/payments/ajax_view_payment', $data);
		}
	}

	/**
	 * overview : sort payment
	 *
	 * @return	void
	 */
	public function paymentSortPage() {
		$sort_by = $this->input->post('sort_by');
		$this->session->set_userdata('payment_sort_by', $sort_by);

		$in = $this->input->post('in');
		$this->session->set_userdata('payment_in', $in);

		$number_payment_list = $this->input->post('number_payment_list');
		$this->session->set_userdata('number_payment_list', $number_payment_list);

		redirect('affiliate_management/viewAffiliatePayment');
	}

	/**
	 * overview : search payment
	 *
	 * @return	void
	 */
	public function paymentSearchPage() {
		if (!$this->permissions->checkPermissions('affiliate_payments')) {
			$this->error_access();
		} else {
			$this->load->model(array('affiliatemodel'));
			$search = array(
				"username" => $this->input->get('username'),
				"status" => $this->input->get('status'),
			);

			$data['input'] = $this->input->get();
			if ($this->input->get('start_date') && $this->input->get('end_date')) {
				$search['request_range'] = "'" . $this->input->get('start_date') . date(' H:i:s',mktime(00,00,00)) . "' AND '" . $this->input->get('end_date') . date(' H:i:s',mktime(23,59,59)) . "'";
			} else {
				$search['request_range'] = "'" . date("Y-m-d 00:00:00", strtotime('-1 month')) . "' AND '" . date("Y-m-d 23:59:59") . "'";
				$data['input']['start_date'] = date('Y-m-d 00:00:00', strtotime('-1 month'));
				$data['input']['end_date'] = date('Y-m-d 23:59:59');
			}
			$data['input']['status'] = $this->input->get('status');
			$data['input']['username'] = $this->input->get('username');

			$data['status_list'] = $this->affiliatemodel->getStatusListKV();

			$this->loadTemplate(lang('Affiliate Management'), '', '', 'affiliate');

			$data['payments'] = $this->affiliatemodel->getSearchPayment(null, null, $search);

			$this->template->write_view('main_content', 'affiliate_management/payments/view_payments', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : process payment
	 *
	 * @param int $request_id
	 * @param string username
	 * @return	void
	 */
	public function processPayment($request_id, $username) {
		$data = array(
			'status' => '1',
			'updatedOn' => date('Y-m-d H:i:s'),
		);
		$this->affiliate_manager->editPayment($data, $request_id);

		$message = lang('con.aff20') . " " . str_replace("%20", " ", $username) . " " . lang('con.aff21');
		$this->alertMessage(1, $message);

		$this->saveAction('Affiliate Payment', "User " . $this->authentication->getUsername() . " has successfully mark payment of " . ucfirst($username) . " as processing.");
		redirect('affiliate_management/viewAffiliatePayment', 'refresh');
	}

	/**
	 * overview : approve payment
	 *
	 * @param int $request_id
	 * @param string $username
	 * @return	void
	 */
	public function approvePayment($request_id, $username) {
		$data = array(
			'status' => '2',
			'updatedOn' => date('Y-m-d H:i:s'),
		);
		$this->affiliate_manager->editPayment($data, $request_id);

		$message = lang('con.aff20') . " " . str_replace("%20", " ", $username) . " " . lang('con.aff22');
		$this->alertMessage(1, $message);

		$this->saveAction('Affiliate Payment', "User " . $this->authentication->getUsername() . " has successfully mark payment of " . ucfirst($username) . " as processed.");
		redirect('affiliate_management/viewAffiliatePayment', 'refresh');
	}

	/**
	 * overview : deny payment
	 *
	 * @param int $request_id
	 * @param string $username
	 * @return	void
	 */
	public function denyPayment($request_id, $username) {
		$data = array(
			'reason' => $this->input->post('reason'),
			'status' => '3',
			'updatedOn' => date('Y-m-d H:i:s'),
		);
		$this->affiliate_manager->editPayment($data, $request_id);

		$message = lang('con.aff20') . " " . str_replace("%20", " ", $username) . " " . lang('con.aff23');
		$this->alertMessage(1, $message);

		$this->saveAction('Affiliate Payment', "User " . $this->authentication->getUsername() . " has successfully mark payment of " . ucfirst($username) . " as denied.");
		redirect('affiliate_management/viewAffiliatePayment', 'refresh');
	}

	/**
	 * overview : add notes
	 *
	 * @param int $request_id
	 * @param string $username
	 * @return	void
	 */
	public function addNotes($request_id, $username) {
		$data = array(
			'reason' => $this->input->post('reason'),
			'updatedOn' => date('Y-m-d H:i:s'),
		);
		$this->affiliate_manager->editPayment($data, $request_id);

		$message = lang('con.aff24') . " " . str_replace("%20", " ", $username);
		$this->alertMessage(1, $message);

		$this->saveAction('Affiliate Payment', "User " . $this->authentication->getUsername() . " has successfully add comment to " . ucfirst($username));
		redirect('affiliate_management/viewAffiliatePayment', 'refresh');
	}

	/* ****** End of Affiliate Payment ****** */

	/* ****** Affiliate Reset Password ****** */

	/**
	 * overview : modify password  page
	 *
	 * @param int $affiliate_id 	affiliate_id
	 * @return	void
	 */
	public function resetPassword($affiliate_id) {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			$data['affiliate_id'] = $affiliate_id;

			$this->template->write_view('main_content', 'affiliate_management/affiliates/reset_password', $data);
			$this->template->render();
		}
	}

	public function resetSecondPassword($affiliate_id) {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			$data['affiliate_id'] = $affiliate_id;

			$this->template->write_view('main_content', 'affiliate_management/affiliates/reset_second_password', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : modify password  page
	 *
	 * @param int $affiliate_id		affiliate_id
	 * @return	void
	 */
	public function adjustBalance($affiliate_id) {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			$data['affiliate_id'] = $affiliate_id;
			$data['affiliate'] = $this->affiliate_manager->getAffiliateById($affiliate_id);

			$this->template->write_view('main_content', 'affiliate_management/affiliates/adjust_balance', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : modify password  page
	 *
	 * @param int $affiliate_id		affiliate_id
	 */
	public function processAdjustBalance($affiliate_id) {
		$reason = $this->input->post('reason');
		$adjust_amount = $this->input->post('adjust_amount');
		$transaction_type = $this->input->post('transaction_type');
		$adminUserId = $this->authentication->getUserId();

		$this->load->model(array('affiliatemodel', 'wallet_model', 'transactions'));

		$self = $this;
		$success = $this->lockAndTransForAffiliateBalance($affiliate_id, function () use ($self, $adminUserId, $affiliate_id, $adjust_amount, $transaction_type, $reason) {

			$beforeBalance = $self->affiliatemodel->getCreditBalance($affiliate_id);

			if ($transaction_type == 'subtract') {
				if ($self->utils->compareResultFloat($adjust_amount, '>', $beforeBalance)) {
					return false;
				}
				$trans_type = Transactions::ADMIN_SUBTRACT_BALANCE_TO_AFFILIATE;
			} else {
				$trans_type = Transactions::ADMIN_ADD_BALANCE_TO_AFFILIATE;
			}

			// $reason = null;
			$success = $this->transactions->createAdjustmentAffiliateTransaction($trans_type,
				$adminUserId, $affiliate_id, $adjust_amount, $beforeBalance, null, $reason);

			return $success;
		});

		// $data['balance'] = $new_balance_amount;
		// $this->affiliatemodel->updateAffiliateBalance($data, $affiliate_id);

		// $this->load->library('payment_manager');

		// $transaction = $this->transactions->saveTransaction(array(
		// 	'amount' => $new_balance_amount,
		// 	'transaction_type' => $trans_type,
		// 	'from_id' => $this->authentication->getUserId(),
		// 	'from_type' => Transactions::ADMIN,
		// 	'to_id' => $affiliate_id,
		// 	'to_type' => Transactions::AFFILIATE,
		// 	'note' => sprintf('%s <b>%s</b> balance to affiliate id: <b>%s</b>\'s balance (<b>%s</b> to <b>%s</b>) by <b>%s</b>', $transaction_type, number_format($new_balance_amount, 2), $affiliate_id, number_format($before_balance_amount, 2), number_format($new_balance_amount, 2), $this->authentication->getUsername()),
		// 	'before_balance' => $before_balance_amount,
		// 	'after_balance' => $new_balance_amount,
		// 	'sub_wallet_id' => null,
		// 	'status' => Transactions::APPROVED,
		// 	'flag' => Transactions::MANUAL,
		// 	'created_at' => $this->utils->getNowForMysql(),
		// 	'promo_category' => null,
		// 	'total_before_balance' => $new_balance_amount,
		// 	'display_name' => null,
		// ), true);

		if ($success) {
			$message = lang('con.pym09');
			$this->alertMessage(1, $message);
		} else {
			$message = lang('aff.account.error.adjustbalance1');
			$this->alertMessage(2, $message);

		}
		redirect('affiliate_management/adjustBalance/' . $affiliate_id);
	}

	/**
	 * overview : verify change password
	 *
	 * $param
	 * @return	void
	 */
	public function verifyChangePassword($affiliate_id) {
		$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean');
		$this->form_validation->set_rules('confirm_new_password', 'Confirm New Password', 'trim|required|xss_clean|callback_checkIfPasswordMatch');

		if ($this->form_validation->run() == false) {
			$this->resetPassword($affiliate_id);
		} else {
			$password = $this->salt->encrypt($this->input->post('new_password'), $this->getDeskeyOG());
			$affiliateDetails = $this->affiliate_manager->getAffiliateById($affiliate_id);

			$data = array(
				'password' => $password,
			);

			$this->affiliate_manager->editAffiliates($data, $affiliate_id);

			$body = "<html><body><p style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg1') . " " . $affiliateDetails['lastname'] . " " . $affiliateDetails['firstname'] . "!</p><br/>
				<p style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg2') . "</p>
				<p style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg3') . ": <b>" . $this->input->post('new_password') . "</b></p>
				<p style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg4') . "</p><br/>
				<p style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg5') . "</p>
				<p style='color:#222;font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg6') . "</p><br/>
				<p style='color:rgb(57, 132, 198);font-size:13px;font-family:Verdana;'>" . lang('mod.emailMsgPassChg7') . "</p></body></html>";

			$this->utils->sendMail($affiliateDetails['email'], $this->operatorglobalsettings->getSettingValue('mail_from'), $this->operatorglobalsettings->getSettingValue('mail_from_email'),
				lang('mod.changepass'), $body, Queue_result::CALLER_TYPE_ADMIN, $this->authentication->getUserId());

			$this->saveAction('Reset Password of Affiliate', "User " . $this->authentication->getUsername() . " has reset password of affiliate: " . $affiliateDetails['username']);
			$this->load->model(['affiliatemodel']);
			$username=$this->affiliatemodel->getUsernameById($affiliate_id);
			$this->syncAffCurrentToMDBWithLock($affiliate_id, $username, false);

			$message = lang('con.aff54');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect("affiliate_management/userInformation/" . $affiliate_id, "refresh");
		}
	}

	public function verifyChangeSecondPassword($affiliate_id) {
		$this->load->model('affiliatemodel');

		$affiliate = $this->affiliatemodel->getAffiliateById($affiliate_id);

		$new_password = $this->input->post('new_password');
		$confirm_new_password = $this->input->post('confirm_new_password');

		if ( ! empty($new_password) && ! empty($confirm_new_password) && $new_password == $confirm_new_password) {

			$data = array('second_password' => $this->utils->encodePassword($new_password));

			$this->affiliatemodel->editAffiliates($data, $affiliate_id);

			$username=$this->affiliatemodel->getUsernameById($affiliate_id);
			$this->syncAffCurrentToMDBWithLock($affiliate_id, $username, false);

			$message = lang('con.17');
			$this->alertMessage(1, $message);
			redirect("affiliate_management/userInformation/{$affiliate_id}", "refresh");
		} else {
			$message = lang('con.04');
			$this->alertMessage(2, $message);
			redirect("affiliate_management/resetSecondPassword/{$affiliate_id}", "refresh");
		}
	}

	/**
	 * overview : call back for confirm password
	 *
	 * @return	bool
	 */
	public function checkIfPasswordMatch() {
		$affiliate_id = $this->input->post('affiliate_id');

		$result = $this->affiliate_manager->getAffiliateById($affiliate_id);

		$new_password = $this->salt->encrypt($this->input->post('new_password'), $this->getDeskeyOG());
		$confirm_new_password = $this->input->post('confirm_new_password');

		if ($new_password == $result['password']) {
			$this->form_validation->set_message('checkIfPasswordMatch', "New Password is the same as Old Password.");
			return false;
		} else if ($this->input->post('new_password') != $confirm_new_password) {
			$this->form_validation->set_message('checkIfPasswordMatch', "Confirm New Password didn't match.");
			return false;
		}

		return true;
	}

	/* ****** End of Affiliate Reset Password ****** */

	# START MONTHLY EARNINGS -------------------------------------------------

	public function calculateMonthlyEarnings($yearmonth = null) {

		// $this->utils->debug_log('sleeping...');
		// sleep(10);
		// $this->utils->debug_log('sleep 10');

		$this->load->model('affiliate_earnings');

		$rlt = false;
		if (!empty($yearmonth)) {
			$rlt = $this->affiliate_earnings->generate_monthly_earnings($yearmonth);
		} else if ($this->affiliate_earnings->todayIsPayday()) {
			$rlt = $this->affiliate_earnings->generate_monthly_earnings();
		}

		$this->returnText('result:' . $rlt . "\n");
	}

	public function calculateMonthlyEarnings_2($yearmonth = NULL) {

		$this->load->library('affiliate_commission');

		try {
			$this->affiliate_commission->generate_monthly_earnings($yearmonth);
			$type = 1;
			$message = lang("Affiliate Commission for the Year Month of {$yearmonth} has been successfully generated.");
		} catch (Exception $e) {
			$type = 2;
			$message = $e->getMessage();
		}

		$this->alertMessage($type, $message);
		redirect('/affiliate_management/viewAffiliateMonthlyEarnings?year_month=' . $yearmonth, 'refresh');
	}

	public function viewAffiliateEarnings($all = null) {

		if ( ! $this->permissions->checkPermissions('affiliate_earnings')) {
			return $this->error_access();
		}

		if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')) {
			return $this->viewAffiliatePlatformEarnings($all);
		}

		if ($this->utils->isEnabledFeature('switch_to_affiliate_daily_earnings')) {
			return $this->viewAffiliateDailyEarnings($all);
		}

		return $this->viewAffiliateMonthlyEarnings($all);
	}

	public function viewAffiliatePlatformEarnings($all = null) {

		$this->load->model(array('affiliate_earnings', 'affiliatemodel'));
		$this->load->library('affiliate_manager');

		if ( ! $this->permissions->checkPermissions('export_affiliate_earnings')) {
			$data['export_report_permission'] = FALSE;
		} else {
			$data['export_report_permission'] = TRUE;
		}

		$data['conditions'] = $this->safeLoadParams(array(
		    'year_month'            => '',
			'start_date' 			=> date('Y-m-d', strtotime('-1 day')),
			'end_date' 				=> date('Y-m-d', strtotime('-1 day')),
			'game_platform_id' 		=> array(),
			'affiliate_username' 	=> '',
			'parent_affiliate' 		=> '',
			'paid_flag' 			=> '',
			'year_month' 			=> '',
			'tag_id' 				=> [],
		));

		$data['tags'] = $this->affiliatemodel->getActiveTagsKV();
        $data['year_month_list'] = $this->affiliate_earnings->getYearMonthListToNow_2();
		$data['affiliates_list'] = $this->affiliatemodel->getParentAffKV();

		$game_platform_list = $this->external_system->getAllSytemGameApi();
		foreach ($game_platform_list as $game_platform) {
			$game_platform_list_kv[$game_platform['id']] = $game_platform['system_name'];
		}

		asort($game_platform_list_kv);
		$data['game_platform_list'] = $game_platform_list_kv;
        $data['cron_sched'] = date('Y-m-d ') . $this->utils->getConfig('affiliate_cron_schedule');
        if (date('Y-m-d H:i:s') >= $data['cron_sched']) {
        	$data['cron_sched'] = date('Y-m-d H:i:s', strtotime($data['cron_sched'] . ' +1 day'));
        }

		$data['flag_list'] = array(
			'' => '------' . lang('N/A') . '------',
			Affiliatemodel::DB_TRUE => lang('Paid'),
			Affiliatemodel::DB_FALSE => lang('Unpaid'),
		);

		$this->loadTemplate(lang('Affiliate Management'), '', '', 'affiliate');
		$this->template->write_view('main_content', 'affiliate_management/view_earnings_list_3', $data);
		$this->template->render();
	}

	public function viewAffiliateDailyEarnings($all = null) {

		$this->load->model(array('affiliate_earnings', 'affiliatemodel'));

		if ( ! $this->permissions->checkPermissions('export_affiliate_earnings')) {
			$data['export_report_permission'] = FALSE;
		} else {
			$data['export_report_permission'] = TRUE;
		}

		$data['conditions'] = $this->safeLoadParams(array(
			'date' 					=> '',
			'affiliate_username' 	=> '',
			'parent_affiliate' 		=> '',
			'paid_flag' 			=> '',
            'tag_id'                => [],
		));

        $data['tags'] = $this->affiliatemodel->getActiveTagsKV();
		$data['affiliates_list'] = $this->affiliatemodel->getParentAffKV();
        $data['cron_sched'] = date("Y-m-d 01:07:00"); # TODO:
		$data['flag_list'] = array(
			'' => '------' . lang('N/A') . '------',
			Affiliatemodel::DB_TRUE => lang('Paid'),
			Affiliatemodel::DB_FALSE => lang('Unpaid'),
		);

		$this->loadTemplate(lang('Affiliate Management'), '', '', 'affiliate');
		$this->template->write_view('main_content', 'affiliate_management/view_earnings_list_2', $data);
		$this->template->render();
	}

	public function viewAffiliateMonthlyEarnings($all = null) {

		$this->load->model(array('affiliate_earnings', 'affiliatemodel'));
        $this->load->library('affiliate_manager');

		if ( ! $this->permissions->checkPermissions('export_affiliate_earnings')) {
			$data['export_report_permission'] = FALSE;
		} else {
			$data['export_report_permission'] = TRUE;
		}


		$data['conditions'] = $this->safeLoadParams(array(
			'year_month' 			=> '',
			'affiliate_username' 	=> '',
			'parent_affiliate' 		=> '',
			'paid_flag' 			=> '',
            'tag_id'                => [],
		));

        $data['tags'] = $this->affiliatemodel->getActiveTagsKV();
		$data['year_month_list'] = $this->affiliate_earnings->getYearMonthListToNow_2();
		$data['affiliates_list'] = $this->affiliatemodel->getParentAffKV();
        $data['cron_sched'] = date("Y-m-d 01:07:00");
		$data['flag_list'] = array(
			'' => '------' . lang('N/A') . '------',
			Affiliatemodel::DB_TRUE => lang('Paid'),
			Affiliatemodel::DB_FALSE => lang('Unpaid'),
		);

		$this->loadTemplate(lang('Affiliate Management'), '', '', 'affiliate');
		$this->template->write_view('main_content', 'affiliate_management/view_earnings_list_2', $data);
		$this->template->render();
	}

	/**
	 * overview : pay all earnings
	 *
	 * @param null $year_month
	 */
	public function payAllEarnings($year_month = null) {
		$this->load->model('transactions');
		$this->load->model('affiliate_earnings');

		if ($year_month == null) {
			$year_month = date('Ym');
		}

		$min_amount = $this->affiliate_earnings->getMinimumPayAmountSetting();

		if ($this->transactions->payAllEarnigns($year_month, $min_amount)) {
			redirect('affiliate_management/viewAffiliateEarnings', 'refresh');
		} else {
			echo 'Error!';
		}
	}

	/**
	 * overview : post affiliate monthly earnings
	 */
	public function postAffiliateMonthEarnings() {
		$this->load->model('transactions');

		if (!empty($_POST)) {
			$id = $_POST['affiliateId'];
			$balance = $_POST['balance'];
			$yearmonth = $_POST['year_month'];
			$notes = $_POST['notes'];
			$created_at = date('Y-m-d H-m-s');

			switch ($_POST['type']) {
			case 'pay':
				$this->transactions->payMonthlyEarnings($id, $balance, $yearmonth, $notes, $created_at);
				break;
			case 'adjust':
				$this->transactions->adjustMonthlyEarnings($id, $balance, $yearmonth, $notes, $created_at);
				break;
			}
			$message = lang('report.log06');
			$this->alertMessage(1, $message);
			redirect('affiliate_management/viewAffiliateEarnings', 'refresh');
		} else {
			$message = lang('report.log07');
			$this->alertMessage(1, $message);
			redirect('affiliate_management/viewAffiliateEarnings', 'refresh');
		}
	}

	/**
	 * overview : edit earnings
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function editAffiliateMonthlyEarnings($earnings_id) {
		if (!$this->permissions->checkPermissions('affiliate_earnings')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			$data['earnings'] = $this->affiliate_manager->getMonthlyEarningsId($earnings_id);

			$this->template->write_view('main_content', 'affiliate_management/earnings/edit_earnings', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : verify edit earnings
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function verifyEditAffiliateMonthlyEarnings() {
		if (!$this->permissions->checkPermissions('affiliate_earnings')) {
			$this->error_access();
		} else {
			$earnings_id = $this->input->post('affiliateMonthlyEarningsId');

			$this->form_validation->set_rules('approved', lang('aff.ai47'), 'trim|xss_clean|required');
			$this->form_validation->set_rules('closing_balance', lang('aff.ai48'), 'trim|xss_clean|required');

			if ($this->form_validation->run() == false) {
				$this->editAffiliateMonthlyEarnings($earnings_id);
			} else {
				$data = array(
					"approved" => $this->input->post('approved'),
					"closing_balance" => $this->input->post('closing_balance'),
					"notes" => $this->input->post('notes'),
					"status" => ($this->input->post('markasfinal') != '1') ? '0' : '1',
				);

				$this->affiliate_manager->updateMonthlyEarnings($data, $earnings_id);

				$message = lang('con.aff53');
				$this->alertMessage(1, $message);
				redirect('affiliate_management/viewAffiliateEarnings');
			}
		}
	}

	/**
	 * overview : transfer
	 *
	 * @param int $earningid
	 */
	public function transfer_one($earningid) {
		$this->load->model(array('affiliate_earnings'));

		$min_amount = $this->affiliate_earnings->getMinimumPayAmountSetting();

		if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')) {
			$succ = $this->affiliate_earnings->transferToWalletById_4($earningid, $min_amount);
		} else if ($this->utils->isEnabledFeature('switch_to_affiliate_daily_earnings')) {
			$succ = $this->affiliate_earnings->transferToWalletById_3($earningid, $min_amount);
		} else {
			if ($this->utils->getConfig('use_old_affiliate_commission_formula')) {
				$succ = $this->affiliate_earnings->transferToWalletById($earningid, $min_amount);
			} else {
				$succ = $this->affiliate_earnings->transferToWalletById_2($earningid, $min_amount);
			}
		}

		if ($succ) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Transfer failed'));
		}

		redirect($this->agent->referrer());
	}

	/**
	 * overview : selected
	 *
	 * @param array $earningids
	 */
	public function transfer_selected() {

		$earningids = $this->input->post('earningids');

		$this->load->model(array('affiliate_earnings'));

		$min_amount = $this->affiliate_earnings->getMinimumPayAmountSetting();

		foreach ($earningids as $earningid) {

			if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')) {
				$succ = $this->affiliate_earnings->transferToWalletById_4($earningid, $min_amount);
			} else if ($this->utils->isEnabledFeature('switch_to_affiliate_daily_earnings')) {
				$succ = $this->affiliate_earnings->transferToWalletById_3($earningid, $min_amount);
			} else {
				if ($this->utils->getConfig('use_old_affiliate_commission_formula')) {
					$succ = $this->affiliate_earnings->transferToWalletById($earningid, $min_amount);
				} else {
					$succ = $this->affiliate_earnings->transferToWalletById_2($earningid, $min_amount);
				}
			}

			if ($succ) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'));
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Transfer failed'));
			}
		}

		return $this->agent->referrer();
	}

	/**
	 * overview : transfer all
	 *
	 * @param null $yearmonth
	 */
	public function transfer_all($yearmonth = null) {
		$this->load->model(array('affiliate_earnings'));

		$min_amount = $this->affiliate_earnings->getMinimumPayAmountSetting();

		if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')) {
			$succ = $this->affiliate_earnings->transferAllEarningsToWallet_4($yearmonth, $min_amount);
		} else if ($this->utils->isEnabledFeature('switch_to_affiliate_daily_earnings')) {
			$succ = $this->affiliate_earnings->transferAllEarningsToWallet_3($yearmonth, $min_amount);
		} else {
			if ($this->utils->getConfig('use_old_affiliate_commission_formula')) {
				$succ = $this->affiliate_earnings->transferAllEarningsToWallet($yearmonth, $min_amount);
			} else {
				$succ = $this->affiliate_earnings->transferAllEarningsToWallet_2($yearmonth, $min_amount);
			}
		}

		if ($succ) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Transfer failed'));
		}

		redirect($this->agent->referrer());
	}
	# END MONTHLY EARNINGS ---------------------------------------------------

	/**
	 * overview : affiliate deposit
	 *
	 * @param null $affId
	 */
	public function affiliate_deposit($affId = null) {
		if(!$this->permissions->checkPermissions('affiliate_deposit'))
			return $this->error_access();

		$this->load->model(array('affiliatemodel', 'transactions'));

		$this->form_validation->set_rules('username', lang('Affiliate Username'), 'trim|xss_clean|required|callback_is_exist[affiliates.username]');
		$this->form_validation->set_rules('amount', lang('Amount'), 'trim|xss_clean|required');
		// $this->form_validation->set_rules('account', lang('Account'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('date', lang('Date'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('reason', lang('Reason'), 'trim|xss_clean|required');
		// $this->form_validation->set_rules('status', lang('Status'), 'trim|xss_clean|required');

		$username = null;
		if ($affId) {
			$aff = $this->affiliatemodel->getAffiliateById($affId);
			$username = $aff['username'];
		}
		$data['username'] = $username;

		if ($this->form_validation->run()) {

			$username = $this->input->post('username');
			$affId = $this->affiliatemodel->getAffiliateIdByUsername($username);
			$amount = $this->input->post('amount');

			$date = $this->input->post('date');
			$reason = $this->input->post('reason');
			$adminUserId = $this->authentication->getUserId();

			$success = $this->lockAndTransForAffiliateBalance($affId, function ()
				use ($affId, $amount, $adminUserId, $reason, $date) {
				$success = $this->transactions->depositToAff($affId, $amount, $reason, $adminUserId, Transactions::MANUAL, $date);
				return $success;
			});

			// $lock_type = Utils::LOCK_ACTION_AFF_BALANCE;
			// $lock_it = $this->utils->lockActionById($affId, $lock_type);
			// $this->utils->debug_log('lock aff', $affId, 'amount', $amount);
			// try {
			// 	if ($lock_it) {
			// 		$this->transactions->startTrans();
			// 		$success = $this->transactions->depositToAff($affId, $amount, $reason, $adminUserId, Transactions::MANUAL, $date);
			// 		$success = $this->transactions->endTransWithSucc() && $success;
			// 	} else {
			// 		$this->utils->error_log('lock aff failed', $affId, $amount);
			// 		$success = false;
			// 	}
			// } finally {
			// 	$rlt = $this->utils->releaseActionById($affId, $lock_type);
			// }

			if ($success) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('New deposit has been successfully added'));
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
			}

			redirect('affiliate_management/affiliate_deposit');

			return;
		}

		$this->loadTemplate(lang('Affilaite Management'), '', '', 'affiliate');
		// $this->template->write_view('sidebar', 'payment_management/sidebar');
		$this->template->write_view('main_content', 'affiliate_management/new_deposit', $data);
		$this->template->render();
	}

	/**
	 * overview : affiliate withdraw
	 *
	 * @param null $affId
	 * @param string $walletType
	 */
	public function affiliate_withdraw($affId = null, $walletType='main') {
		if(!$this->permissions->checkPermissions('affiliate_withdraw'))
			return $this->error_access();

		$this->load->model(array('affiliatemodel', 'transactions'));

		$this->form_validation->set_rules('username', lang('Affiliate Username'), 'trim|xss_clean|required|callback_is_exist[affiliates.username]');
		$this->form_validation->set_rules('amount', lang('Amount'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('date', lang('Date'), 'trim|xss_clean|required');
		$this->form_validation->set_rules('reason', lang('Reason'), 'trim|xss_clean|required');

		if ($affId) {
			$aff = $this->affiliatemodel->getAffiliateById($affId);
			$username = $aff['username'];
		} else if ($username = $this->input->post('username')) {
			$affId = $this->affiliatemodel->getAffiliateIdByUsername($username);
		}

		$data['username'] = $username;
		$data['walletType'] = $walletType;

		if ($this->form_validation->run()) {

			$amount = $this->input->post('amount');

			$date = $this->input->post('date');
			$reason = $this->input->post('reason');
			$adminUserId = $this->authentication->getUserId();

			$message=null;

			$success = $this->lockAndTransForAffiliateBalance($affId, function ()
				use ($walletType, $affId, $amount, $adminUserId, $reason, $date, &$message) {

				if($walletType=='main'){
					$bal = $this->affiliatemodel->getMainWallet($affId);
				}else{
					$bal = $this->affiliatemodel->getBalanceWallet($affId);
				}
				if ($this->utils->compareResultFloat($bal, '>=', $amount)) {
					$success = $this->transactions->withdrawFromAff($affId, $amount, $reason, $adminUserId,
						Transactions::MANUAL, $date, $walletType);
				} else {
					$this->utils->error_log('do not have enough balance', $affId, $amount, 'wallet balance', $bal);
					$message=lang('Do not have enough balance');
					$success = false;
				}

				return $success;
			});

			// $lock_type = Utils::LOCK_ACTION_AFF_BALANCE;
			// $lock_it = $this->utils->lockActionById($affId, $lock_type);
			// $this->utils->debug_log('lock aff', $affId, 'amount', $amount);
			// try {
			// 	if ($lock_it) {
			// 		$this->transactions->startTrans();
			// 		//check enough balance
			// 		if($walletType=='main'){
			// 			$bal = $this->affiliatemodel->getMainWallet($affId);
			// 		}else{
			// 			$bal = $this->affiliatemodel->getBalanceWallet($affId);
			// 		}
			// 		if ($this->utils->compareResultFloat($bal, '>=', $amount)) {
			// 			$success = $this->transactions->withdrawFromAff($affId, $amount, $reason, $adminUserId, Transactions::MANUAL, $date, $walletType);
			// 		} else {
			// 			$this->utils->error_log('do not have enough balance', $affId, $amount, 'wallet balance', $bal);
			// 			$message=lang('Do not have enough balance');
			// 			$success = false;
			// 		}
			// 		$success = $this->transactions->endTransWithSucc() && $success;
			// 	} else {
			// 		$this->utils->error_log('lock aff failed', $affId, $amount);
			// 		$success = false;
			// 	}
			// } finally {
			// 	$rlt = $this->utils->releaseActionById($affId, $lock_type);
			// }

			if ($success) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('New withdrawal has been successfully added'));
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message ? $message : lang('error.default.db.message'));
			}

			if($affId){
				redirect('affiliate_management/affiliate_withdraw');
			}else{
				redirect('affiliate_management/affiliate_withdraw/'.$affId.'/'.$walletType);
			}

			return;
		}

		$this->loadTemplate(lang('Affilaite Management'), '', '', 'affiliate');
		$this->template->write_view('main_content', 'affiliate_management/new_withdrawal', $data);
		$this->template->render();
	}

	/**
	 * overview : affiliate manual add balance
	 *
	 * @param null $affId
	 */
	public function affiliate_manual_add_balance($affId = null) {
		$this->load->model(array('affiliatemodel', 'transactions'));

		$this->form_validation->set_rules('amount', lang('Amount'), 'trim|xss_clean|required|greater_than[0]');
		$this->form_validation->set_rules('reason', lang('Reason'), 'trim|xss_clean|required|htmlspecialchars');

		$username = null;
		if ($affId) {
			$aff = $this->affiliatemodel->getAffiliateById($affId);
			$username = $aff['username'];
		}
		$data['title'] = lang('Manually Add Balance to Affiliate Main Wallet');
		$data['affId'] = $affId;
		$data['username'] = $username;
		$data['balance'] = $this->affiliatemodel->getMainWallet($affId);

		if ($this->form_validation->run()) {

			$amount = $this->input->post('amount');
			$reason = $this->input->post('reason');
			$adminUserId = $this->authentication->getUserId();

			$success = $this->lockAndTransForAffiliateBalance($affId, function ()
				use ($affId, $amount, $adminUserId, $reason) {
				$success = $this->transactions->manualAddBalanceAff($affId, $amount, $reason, $adminUserId, Transactions::MANUAL);
				return $success;
			});

			// $lock_type = Utils::LOCK_ACTION_AFF_BALANCE;
			// $lock_it = $this->utils->lockActionById($affId, $lock_type);
			// $this->utils->debug_log('lock aff', $affId, 'amount', $amount);
			// try {
			// 	if ($lock_it) {
			// 		$this->transactions->startTrans();
			// 		$success = $this->transactions->manualAddBalanceAff($affId, $amount, $reason, $adminUserId, Transactions::MANUAL);
			// 		$success = $this->transactions->endTransWithSucc() && $success;
			// 	} else {
			// 		$this->utils->error_log('lock aff failed', $affId, $amount);
			// 		$success = false;
			// 	}
			// } finally {
			// 	$rlt = $this->utils->releaseActionById($affId, $lock_type);
			// }

			if ($success) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Balance has been successfully added to affiliate main wallet'));
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
			}

			redirect('affiliate_management/affiliate_manual_add_balance/'.$affId);

			return;
		}

		$this->loadTemplate(lang('Affiliate Management'), '', '', 'affiliate');
		$this->template->write_view('main_content', 'affiliate_management/affiliate_manual_add_subtract_balance', $data);
		$this->template->render();
	}

	/**
	 * overview : affiliate manual subtract balance
	 *
	 * @param null $affId
	 */
	public function affiliate_manual_subtract_balance($affId = null) {
		$this->load->model(array('affiliatemodel', 'transactions'));

		$this->form_validation->set_rules('amount', lang('Amount'), 'trim|xss_clean|required|greater_than[0]');
		$this->form_validation->set_rules('reason', lang('Reason'), 'trim|xss_clean|required|htmlspecialchars');

		$username = null;
		if ($affId) {
			$aff = $this->affiliatemodel->getAffiliateById($affId);
			$username = $aff['username'];
		}
		$data['title'] = lang('Manually Subtract Balance to Affiliate Main Wallet');
		$data['affId'] = $affId;
		$data['username'] = $username;
		$data['balance'] = $this->affiliatemodel->getMainWallet($affId);

		if ($this->form_validation->run()) {

			$amount = $this->input->post('amount');
			$reason = $this->input->post('reason');
			$adminUserId = $this->authentication->getUserId();

			$success = $this->lockAndTransForAffiliateBalance($affId, function ()
				use ($affId, $amount, $adminUserId, $reason) {
				$success = $this->transactions->manualSubtractBalanceAff($affId, $amount, $reason, $adminUserId, Transactions::MANUAL);
				return $success;
			});

			// $lock_type = Utils::LOCK_ACTION_AFF_BALANCE;
			// $lock_it = $this->utils->lockActionById($affId, $lock_type);
			// $this->utils->debug_log('lock aff', $affId, 'amount', $amount);
			// try {
			// 	if ($lock_it) {
			// 		$this->transactions->startTrans();
			// 		$success = $this->transactions->manualSubtractBalanceAff($affId, $amount, $reason, $adminUserId, Transactions::MANUAL);
			// 		$success = $this->transactions->endTransWithSucc() && $success;
			// 	} else {
			// 		$this->utils->error_log('lock aff failed', $affId, $amount);
			// 		$success = false;
			// 	}
			// } finally {
			// 	$rlt = $this->utils->releaseActionById($affId, $lock_type);
			// }

			if ($success) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Balance has been successfully subtracted to affiliate main wallet'));
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
			}

			redirect('affiliate_management/affiliate_manual_subtract_balance/'.$affId);

			return;
		}

		$this->loadTemplate(lang('Affiliate Management'), '', '', 'affiliate');
		$this->template->write_view('main_content', 'affiliate_management/affiliate_manual_add_subtract_balance', $data);
		$this->template->render();
	}

	/**
	 * overview : affiliate transfer balance to main
	 *
	 * @param $affId
	 * @param null $amount
	 */
	public function affiliate_transfer_bal_to_main($affId, $amount = null) {
		$this->load->model(array('transactions'));

		$self = $this;

		$adminUserId = $this->authentication->getUserId();

		// $lock_type = Utils::LOCK_ACTION_AFF_BALANCE;

		$success = $this->lockAndTransForAffiliateBalance($affId, function () use ($self, $affId, $amount, $adminUserId) {
			$success = $self->transactions->affTransferFromBalanceToMain($affId, $amount, $adminUserId);
			return $success;
		});

		if ($success) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
		}

		redirect("/affiliate_management/userInformation/" . $affId . "#bank_info");
	}

	/**
	 * overview : affiliate transfer balance from main
	 *
	 * @param $affId
	 * @param null $amount
	 */
	public function affiliate_transfer_bal_from_main($affId, $amount = null) {
		$this->load->model(array('transactions'));

		$self = $this;

		$adminUserId = $this->authentication->getUserId();

		// $lock_type = Utils::LOCK_ACTION_AFF_BALANCE;

		$success = $this->lockAndTransForAffiliateBalance($affId, function () use ($self, $affId, $amount, $adminUserId) {
			$success = $self->transactions->affTransferToBalanceFromMain($affId, $amount, $adminUserId);
			return $success;
		});

		if ($success) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
		}

		redirect("/affiliate_management/userInformation/" . $affId . "#bank_info");
	}

	/**
	 * overview : approved payment
	 */
	public function approve_payment() {
		if (!$this->permissions->checkPermissions('affiliate_payments')) {
			$this->error_access();
		} else {
			$this->form_validation->set_rules('reason', 'Reason', 'trim|xss_clean|htmlspecialchars');
			if($this->form_validation->run()) {
				$history_id = $this->input->post('history_id');
				$reason = $this->input->post('reason');
				$affId = $this->input->post('affId');
				$adminUserId = $this->authentication->getUserId();
				$success = !empty($history_id) && !empty($affId);

				if ($success) {
					$this->load->model(array('affiliatemodel'));

					$self = $this;
					$success = $this->lockAndTransForAffiliateBalance($affId, function ()
							use ($self, $history_id, $reason, $adminUserId) {
						$success = $self->affiliatemodel->approvePayment($history_id, $reason, $adminUserId);
						$self->utils->debug_log('approvePayment', $success);
						return $success;
					});
				}

				if ($success) {
					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Approved this payment'));
				} else {
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
				}
				//go back
				$url = '/affiliate_management/paymentSearchPage';
				if (isset($_SERVER['HTTP_REFERER'])) {
					$url = $_SERVER['HTTP_REFERER'];
				}
				redirect($url);
			}
		}
	}

	/**
	 * overview : decline payment
	 */
	public function decline_payment() {
		if (!$this->permissions->checkPermissions('affiliate_payments')) {
			$this->error_access();
		} else {
			$this->form_validation->set_rules('reason', 'Reason', 'trim|xss_clean|htmlspecialchars');
			if($this->form_validation->run()) {

				$history_id = $this->input->post('history_id');
				$reason = $this->input->post('reason');
				$affId = $this->input->post('affId');
				$adminUserId = $this->authentication->getUserId();
				$success = !empty($history_id) && !empty($affId);

				if ($success) {

					$this->load->model(array('affiliatemodel'));

					$self = $this;
					$success = $this->lockAndTransForAffiliateBalance($affId, function ()
							use ($self, $affId, $history_id, $reason, $adminUserId) {
						$success = $self->affiliatemodel->declinePayment($history_id, $reason, $adminUserId);
						$self->utils->debug_log('declinePayment:'.$history_id, $affId, $success);
						return $success;
					});

				}

				if ($success) {
					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Declined this payment'));
				} else {
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
				}
				//go back
				$url = '/affiliate_management/paymentSearchPage';
				if (isset($_SERVER['HTTP_REFERER'])) {
					$url = $_SERVER['HTTP_REFERER'];
				}
				redirect($url);
			}
		}
	}

	public function viewDomain() {
		if ( ! $this->permissions->checkPermissions('aff_domain_setting')) {
			$this->error_access();
		} else {
			$this->load->library('ip_manager');

			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active', 'system_crumb' => 'active'));
			}

			$data['domain'] = $this->ip_manager->getAllDomain();

			$this->loadTemplate('Domain List', '', '', 'affiliate');
			$this->template->write_view('main_content', 'affiliate_management/domains/view_domain', $data);
			$this->template->render();
		}
	}

	public function addDomain() {
		if ( ! $this->permissions->checkPermissions('aff_domain_setting')) {
			$this->error_access();
		} else {

			$this->form_validation->set_rules('domain', 'Domain', 'trim|required|valid_domain|xss_clean|is_unique[domain.domainName]');
			$this->form_validation->set_rules('notes', 'Notes', 'trim|xss_clean|htmlspecialchars');
			$this->form_validation->set_message('valid_domain', lang('validation.badDomain'));

			if ($this->form_validation->run() == false) {
				$this->viewDomain();
			} else {
				$this->load->library('ip_manager');
				$this->load->model('affiliatemodel');


				$show_to_affiliate = $this->input->post('show_to_affiliate');
				$current_time = date('Y-m-d H:i:s');
				$domainName = rtrim($this->input->post('domain'), '/');
				$notes = $this->input->post('notes');
				$domain_id = $this->ip_manager->addDomain(array(
					'show_to_affiliate' => $show_to_affiliate,
					'domainName' => $domainName,
					'notes' => $notes,
					'createdOn' => $current_time,
					'updatedOn' => $current_time,
				));

				if ($show_to_affiliate == 2) {
					$text = file_get_contents($_FILES['usernames']['tmp_name']);
					$usernames = explode("\n", $text);
					$usernames = explode(",", implode(",", $usernames));
					$usernames = array_filter($usernames);
					$usernames = array_unique($usernames);
					$affiliate_domain_list = array_filter(array_map(function($username) use ($domain_id) {
						$username = trim($username);
						$affiliate_id = $this->affiliatemodel->getAffiliateIdByUsername($username);
						return $affiliate_id ? array(
							'affiliateId' => $affiliate_id,
							'domainId' => $domain_id,
						) : NULL;
					}, $usernames));
				}

				if (isset($affiliate_domain_list) && ! empty($affiliate_domain_list)) {
					$this->db->insert_batch('affiliate_domain', $affiliate_domain_list);
				}

				$this->alertMessage(1, lang('con.i11'));
				$this->saveAction('Add Domain', 'User ' . $this->authentication->getUsername() . ' has added new domain' . $domainName);
				redirect(BASEURL . 'affiliate_management/viewDomain', 'refresh');
			}
		}
	}

	public function editDomain($domain_id) {
		if ( ! $this->permissions->checkPermissions('aff_domain_setting')) {
			$this->error_access();
		} else {



			$this->load->library('ip_manager');
			$data['domain'] = $this->ip_manager->getAllDomain();
			$data['domain_id'] = $domain_id;
			$data['edit_domain'] = $this->db->get_where('domain', array('domainId' => $domain_id))->row_array();
			$data['affiliate_domain_count'] = $this->db->where('domainId', $domain_id)->count_all_results('affiliate_domain');
			$this->loadTemplate('Domain List', '', '', 'system');
			$this->template->write_view('main_content', 'affiliate_management/domains/edit_domain', $data);
			$this->template->render();
		}
	}

	public function verifyEditDomain($domain_id) {
		if ( ! $this->permissions->checkPermissions('ip')) {
			$this->error_access();
		} else {
			// $this->form_validation->set_rules('domain', 'Domain', 'trim|required|valid_url|xss_clean|is_unique[domain.domainName]');
			$this->form_validation->set_rules('domain', 'Domain', 'trim|required|valid_domain|xss_clean]');
			$this->form_validation->set_rules('notes', 'Notes', 'trim|xss_clean|htmlspecialchars');
			$this->form_validation->set_message('valid_domain', lang('validation.badDomain'));
			if ($this->form_validation->run() == false) {
				$this->editDomain($domain_id);
			} else {
				$this->editDomainProcess($domain_id);
			}
		}
	}

	public function editDomainProcess($domain_id) {
		$this->load->library('ip_manager');
		$this->load->model('affiliatemodel');

		if (($show_to_affiliate = $this->input->post('show_to_affiliate')) == 2) {
			$text = file_get_contents($_FILES['usernames']['tmp_name']);
			$usernames = explode("\n", $text);
			$usernames = explode(",", implode(",", $usernames));
			$usernames = array_filter($usernames);
			$usernames = array_unique($usernames);
			$affiliate_domain_list = array_filter(array_map(function($username) use ($domain_id) {
				$username = trim($username);
				$affiliate_id = $this->affiliatemodel->getAffiliateIdByUsername($username);
				return $affiliate_id ? array(
					'affiliateId' => $affiliate_id,
					'domainId' => $domain_id,
				) : NULL;
			}, $usernames));
		}

		$current_time = date('Y-m-d H:i:s');
		$domainName = rtrim($this->input->post('domain'), "/");
		$notes = $this->input->post('notes');
		$this->ip_manager->editDomain(array(
			'show_to_affiliate' => $show_to_affiliate,
			'domainName' => $domainName,
			'notes' => $notes,
			'updatedOn' => $current_time,
		), $domain_id);

		if ($show_to_affiliate != 2) {
			$this->db->delete('affiliate_domain', array('domainId' => $domain_id));
		} else if (isset($affiliate_domain_list) && ! empty($affiliate_domain_list)) {
			$this->db->delete('affiliate_domain', array('domainId' => $domain_id));
			$this->db->insert_batch('affiliate_domain', $affiliate_domain_list);
		}

		$this->alertMessage(1, lang('con.i12'));
		$this->saveAction('Edit Domain', 'User ' . $this->authentication->getUsername() . ' has edited domain ' . $domainName);
		redirect(BASEURL . 'affiliate_management/editDomain/' . $domain_id, 'refresh');
	}

	public function activateDomain($domain_id, $domain_name) {
		if ( ! $this->permissions->checkPermissions('aff_domain_setting')) {
			$this->error_access();
		} else {
			$this->load->library('ip_manager');
			$current_time = date('Y-m-d H:i:s');
			$domain_name_str = str_replace('%3A', ':', base64_decode($domain_name));
			$this->ip_manager->editDomain(array(
				'status' => '0',
				'updatedOn' => $current_time,
			), $domain_id);
			$this->alertMessage(1, lang('con.i13') . ': ' . $domain_name_str);
			$this->saveAction('Activate Domain', 'User ' . $this->authentication->getUsername() . ' has activated domain ' . $domain_name_str);
			redirect(BASEURL . 'affiliate_management/viewDomain', 'refresh');
		}
	}

	public function deactivateDomain($domain_id, $domain_name) {
		if ( ! $this->permissions->checkPermissions('aff_domain_setting')) {
			$this->error_access();
		} else {
			$this->load->library('ip_manager');
			$current_time = date('Y-m-d H:i:s');
			$domain_name_str = str_replace('%3A', ':', base64_decode($domain_name));
			$this->ip_manager->editDomain(array(
				'status' => '1',
				'updatedOn' => $current_time,
			), $domain_id);
			$this->alertMessage(1, lang('con.i14') . ': ' . $domain_name_str);
			$this->saveAction('Deactivate Domain', 'User ' . $this->authentication->getUsername() . ' has deactivated domain ' . $domain_name_str);
			redirect(BASEURL . 'affiliate_management/viewDomain', 'refresh');
		}
	}

	public function deleteDomain($domain_id) {
		if ( ! $this->permissions->checkPermissions('aff_domain_setting')) {
			$this->error_access();
		} else {
			$this->load->library('ip_manager');
			$this->ip_manager->deleteDomain($domain_id);
			$this->saveAction('Delete Domain', 'User ' . $this->authentication->getUsername() . ' has deleted domain');
			$this->alertMessage(1, lang('con.i15'));
			redirect(BASEURL . 'affiliate_management/viewDomain', 'refresh');
		}
	}

	public function domain_affiliates($domainId) {
		# TODO:
		$this->db->select('affiliates.affiliateId, affiliates.username');
		$this->db->join('affiliates','affiliates.affiliateId = affiliate_domain.affiliateId');
		$query = $this->db->get_where('affiliate_domain', array('domainId' => $domainId));
		$data['affiliates'] = array_map(function($affiliate) {
			return '<a href="/affiliate_management/userInformation/'.$affiliate['affiliateId'].'" class="list-group-item">'.$affiliate['username'].'</a>';
		}, $query->result_array());
		$this->loadTemplate('Domain Affiliates', '', '', 'affiliate');
		$this->template->write_view('main_content', 'affiliate_management/domains/domain_affiliates', $data);
		$this->template->render();
	}

	/**
	 * overview : list of player notes
	 *
	 * @param int $affiliate_id	player id
	 */
	public function affiliate_notes($affiliate_id) {
		$this->load->model(['Affiliatemodel']);
		$user_id = $this->authentication->getUserId();
		$data = array(
			'user_id' => $user_id,
			'affiliate_id' => $affiliate_id,
		);
		$data['notes'] = $this->Affiliatemodel->getAffiliateNotes($affiliate_id);
		$this->load->view('affiliate_management/affiliate_notes', $data);
	}

	public function add_affiliate_notes($affiliate_id) {
	    $result['success'] = false;

		$this->load->model(['affiliatemodel']);
		$user_id = $this->authentication->getUserId();
		$notes = $this->input->post('notes');

		if ($notes) {

			$result['success'] = $this->affiliatemodel->addAffiliateNote($affiliate_id, $user_id, $notes);

			if ($result['success']) {
				$this->saveAction("Add notes to affiliate", 'Add Note for Affiliate', "Affiliate " . $this->authentication->getUsername() . " has added new note to affiliate");
			}

		}

		$this->returnJsonResult($result);
	}

	/**
	 * overview : manual adjustment for affiliate commission overview
	 *
	 * detail : load view for adjust affiliate manual adjustment level
	 *
	 * @param int $affiliateId
	 */
	public function affiliate_commision_manual_adjustment($affiliate_commission_id, $total_commission) {

		$this->load->model('affiliate_earnings');

		$data['affiliate_commission_id'] = $affiliate_commission_id;
		$data['total_commission'] = $total_commission;

		if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')) {
			$affiliate_commission_record = $this->affiliate_earnings->getAffiliatePlatformCommission($affiliate_commission_id);
			$data['commission_notes'] = ! empty($affiliate_commission_record) ? $affiliate_commission_record['adjustment_notes'] : '';
		} else if ($this->utils->isEnabledFeature('switch_to_affiliate_daily_earnings')) {
			$affiliate_commission_record = $this->affiliate_earnings->getAffiliateDailyCommission($affiliate_commission_id);
			$data['commission_notes'] = ! empty($affiliate_commission_record) ? $affiliate_commission_record['adjustment_notes'] : '';
		} else {
			$affiliate_commission_record = $this->affiliate_earnings->getAffiliateMonthlyCommission($affiliate_commission_id);
			$data['commission_notes'] = ! empty($affiliate_commission_record) ? $affiliate_commission_record['adjustment_notes'] : '';
		}

		$this->load->view('affiliate_management/affiliate_commision_manual_adjustment', $data);
	}

	public function updateTotalOfAffiliateCommission($affiliate_commission_id) {
		$this->load->model('affiliate_earnings');

		$amount = $this->input->post('total_amount');
		$note = $this->input->post('note');

		if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')) {
			$this->affiliate_earnings->updateAffiliateTotalPlatformCommission($affiliate_commission_id, $amount, $note);
		} else if ($this->utils->isEnabledFeature('switch_to_affiliate_daily_earnings')) {
			$this->affiliate_earnings->updateAffiliateTotalDailyCommission($affiliate_commission_id, $amount, $note);
		} else {
			$this->affiliate_earnings->updateAffiliateTotalMonthlyCommission($affiliate_commission_id, $amount, $note);
		}

		redirect($this->agent->referrer());
	}

	public function commission_details($id, $yearMonth = null, $affiliate_id = null) {

        $this->load->model(['external_system', 'affiliatemodel']);
		$this->db->where('id', $id);
		$query = $this->db->get('aff_monthly_earnings', 1);
		$earnings = $query->row_array();
		$earnings['details'] = json_decode($earnings['details'], TRUE);
        $commonSettings = $this->affiliatemodel->getDefaultAffSettings();
        if(!empty($yearMonth) && !empty($affiliate_id)){
            $previous_year_month = date('Ym', strtotime($yearMonth.'01'." -1 month") );
            $earnings['prev_negative_net_revenue'] = $this->affiliatemodel->getPreviousNegativeNetRevenue($affiliate_id, $previous_year_month); // get the last month negative net revenue
        }
		$this->load->view('affiliate_management/commission_details', array('earnings' => $earnings, 'settings' => $commonSettings) );
	}

    public function sync_aff_to_mdb($affId){
        if(!$this->permissions->checkPermissions('edit_affiliate_info') && empty($affId)) {
            return $this->error_access();
        }

        $rlt=null;
		$username=$this->affiliatemodel->getUsernameById($affiliate_id);
        $success=$this->syncAffCurrentToMDBWithLock($affId, $username, false, $rlt);

        if(!$success){
            $errKeys=[];
            foreach ($rlt as $dbKey => $dbRlt) {
                if(!$dbRlt['success']){
                    $errKeys[]=$dbKey;
                }
            }
            $errorMessage=implode(',', $errKeys);
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sync Affiliate Failed').': '.$errorMessage);
        }else{
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Sync Affiliate Successfully'));
        }

        redirect('/affiliate_management/userInformation/'.$affId);
    }

    public function commission_details_by_tier($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('aff_monthly_earnings', 1);
        $earnings = $query->row_array();
        $earnings['commission_amount_breakdown'] = json_decode($earnings['commission_amount_breakdown'], TRUE);
        $this->load->view('affiliate_management/earnings/commission_details_by_tier', array('earnings' => $earnings) );
    }

    public function sub_affiliate_details($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('aff_monthly_earnings', 1);
        $earnings = $query->row_array();

        $data['settings'] = $this->affiliatemodel->getDefaultAffSettings();
        $data['sub_aff_commission_breakdown'] = json_decode($earnings['sub_aff_commission_breakdown'], TRUE);
        if( count($data['sub_aff_commission_breakdown']) > 0 ){
            foreach($data['sub_aff_commission_breakdown'] as $key => $breakdown){
                $data['sub_aff_commission_breakdown'][$key]['username']=$this->affiliatemodel->getUsernameById($breakdown['subaffiliate_id']);
            }
        }
        $this->load->view('affiliate_management/earnings/view_sub_affiliate_details', $data );
    }
}
/* End of file affiliate_management.php */
/* Location: ./application/controllers/affiliate_management.php */