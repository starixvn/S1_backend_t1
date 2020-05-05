<?php

/**
 * General behaviors include:
 * * Loads Template
 * * Displays Affiliate Statistics
 * * Searches Statistics
 * * Displays Affiliate Players with links to affiliate's information
 *
 * @see Redirect redirect to affiliate statistics page
 *
 * @category Affiliate Modules
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait affiliate_statistics_module {

	/**
	 * overview : affiliate statistics
	 */
	public function affiliate_statistics() {
		if (!$this->permissions->checkPermissions('affiliate_statistics')) {
			$this->error_access();
		} else {
			// $this->load->model(array('promorules'));
			// $data['vipgrouplist'] = null; // $this->vipsetting_manager->getVipGroupList();
			// $data['allPromo'] = $this->promorules->getAllPromorulesList();
			// $data['allPromoTypes'] = $this->promorules->getAllPromoTypeList();
			//export report permission checking
			if (!$this->permissions->checkPermissions('export_affiliate_statistics')) {
				$data['export_report_permission'] = FALSE;
			} else {
				$data['export_report_permission'] = TRUE;
			}

			$data['title'] = lang('Affiliate Statistics');

			$data['conditions'] = $this->safeLoadParams(array(
				'by_date_from' =>  $this->utils->get7DaysAgoForMysql(),
				'by_date_to' =>  $this->utils->getTodayForMysql(),
				'by_affiliate_username' => '',
				'by_status' => '0',
				'show_game_platform' => false,
				'tag_id' => [],
				'by_total_cashback_date_type' => Transactions::TOTAL_CASHBACK_SAME_DAY,
			));

			$data['conditions']['enable_date'] = $this->safeGetParam('enable_date', false, true);

            $this->load->model(['affiliatemodel']);
            $data['tags'] = $this->affiliatemodel->getActiveTagsKV();
			// $data['isDefaultOpenSearchPanel'] = $this->isDefaultOpenSearchPanel();
			// $this->utils->debug_log($data['conditions']);
			//$data['activenav'] = 'affiliate';

			$data['total_cashback_date_type'] = array(
				Transactions::TOTAL_CASHBACK_SAME_DAY    => lang('total_cashback_same_day'),
				Transactions::TOTAL_CASHBACK_PLUS_1_DAY  => lang('total_cashback_plus_1_day'),
			);

			$this->loadTemplate($data['title'], '', '', 'affiliate');
			$this->template->add_js($this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/js/bootstrap-switch.min.js'));
			$this->template->add_css($this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css'));
			$this->template->write_view('main_content', 'affiliate_management/view_statistics', $data);
			$this->template->render();
		}
	}

    public function affiliate_statistics2() {
        if (!$this->permissions->checkPermissions('affiliate_statistics')) {
            $this->error_access();
        } else {
            if (!$this->permissions->checkPermissions('export_affiliate_statistics')) {
                $data['export_report_permission'] = FALSE;
            } else {
                $data['export_report_permission'] = TRUE;
            }

            $data['title'] = lang('Affiliate Statistics');

            $data['conditions'] = $this->safeLoadParams(array(
                'by_date_from' =>  $this->utils->get7DaysAgoForMysql(),
                'by_date_to' =>  $this->utils->getTodayForMysql(),
                'by_affiliate_username' => '',
                'by_status' => '0',
                'show_game_platform' => false,
                'tag_id' => [],
            ));

            $data['conditions']['enable_date'] = $this->safeGetParam('enable_date', false, true);

            $this->load->model(['affiliatemodel']);
            $data['tags'] = $this->affiliatemodel->getActiveTagsKV();

            $this->loadTemplate($data['title'], '', '', 'affiliate');
            $this->template->add_js('resources/js/bootstrap-switch.min.js');
            $this->template->add_css('resources/css/bootstrap-switch.min.css');
            $this->template->write_view('main_content', 'affiliate_management/view_statistics2', $data);
            $this->template->render();
        }
    }


    /**
	 * overview : view affiliate statistics
	 *
	 * @return redirect
	 */
	public function viewAffiliateStatistics() {
		if (!$this->permissions->checkPermissions('affiliate_statistics')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			$username = null;
			$start_date = null;
			$end_date = null;
			$data = array();
			if ($this->isPostMethod()) {

				// $this->form_validation->set_rules('period', 'Period', 'trim|xss_clean');
				$this->form_validation->set_rules('username', 'Username', 'trim|xss_clean');
				$this->form_validation->set_rules('start_date', 'Start Date', 'trim|xss_clean');
				$this->form_validation->set_rules('end_date', 'End Date', 'trim|xss_clean');

				if ($this->form_validation->run()) {
					// $period = $this->input->post('period');
					$username = $this->input->post('username');
					// $type_date = $this->input->post('type_date');
					$start_date = $this->input->post('dateRangeValueStart');
					$end_date = $this->input->post('dateRangeValueEnd');
					// $date_range_value = $this->utils->formatDateTimeForMysql($start_date) . ' - ' . $this->utils->formatDateTimeForMysql($end_date);

					// $data['date_range_value'] = $date_range_value;
					// $this->session->set_userdata(array(
					// 	// 'period' => $period,
					// 	'username' => $username,
					// 	// 'type_date' => $type_date,
					// 	'start_date' => $start_date,
					// 	'end_date' => $end_date,
					// 	'date_range_value' => $date_range_value,
					// ));
				}
			} else {
				$username = null;
				list($start_date, $end_date) = $this->utils->getTodayStringRange();

				// $this->session->unset_userdata(array(
				// 	// 'period' => "",
				// 	'username' => "",
				// 	// 'type_date' => "",
				// 	'start_date' => "",
				// 	'end_date' => "",
				// 	'date_range_value' => "",
				// ));
			}
			$data['username'] = $username;
			$data['start_date'] = $start_date;
			$data['end_date'] = $end_date;

			$this->load->model(array('affiliatemodel'));
			$onlyMaster = false;
			$data['affiliates'] = $this->affiliatemodel->getAllActivtedAffiliates($onlyMaster);
			$data['statistics'] = $this->affiliatemodel->getAffiliateStatistics($start_date, $end_date, $username);

			//var_dump($data['statistics']); die();
			$this->template->add_css('resources/css/select2.min.css');
			$this->template->add_js('resources/js/select2.full.min.js');

			$this->template->write_view('main_content', 'affiliate_management/statistics/view_statistics', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : view affiliate statistics
	 *
	 * @return json
	 */
	public function viewAffiliateStatisticsJSON() {
		$this->load->model(array('affiliatemodel'));

		if ($this->session->userdata('start_date') != null) {
			$start_date = $this->session->userdata('start_date');
			$end_date = $this->session->userdata('end_date');
			$username = $this->session->userdata('username');
		} else {
			$start_date = date('Y-m-d') . '00:00:00';
			$end_date = date('Y-m-d') . '23:59:59';
			$username = null;
		}

		$this->returnJsonResult($this->affiliatemodel->getAffiliateStatistics($start_date, $end_date, $username));

		// print_r(json_encode($this->affiliate->getStatistics($start_date, $end_date, $username)));
	}

	/**
	 * overview : view Affiliate Statistics Today
	 *
	 * @param 	int
	 * @return	redirect
	 */
	// public function viewAffiliateStatisticsToday() {
	// 	if (!$this->permissions->checkPermissions('affiliate_statistics')) {
	// 		$this->error_access();
	// 	} else {
	// 		$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

	// 		$start_date = $this->input->post('dateRangeValueStart');
	// 		$end_date = $this->input->post('dateRangeValueEnd');

	// 		$data['affiliates'] = $this->affiliate_manager->getAllAffiliates(null, null, null);
	// 		//$data['statistics'] = $this->affiliate->getStatistics($start_date, $end_date);

	// 		$this->template->write_view('main_content', 'affiliate_management/statistics/view_statistics', $data);
	// 		$this->template->render();
	// 	}
	// }

	/**
	 * overview : view affiliate statistics ajax data
	 *
	 * @param $start
	 * @param $end
	 */
	public function viewAffiliateStatisticsAJAX($start, $end) {
		$data['statistics'] = $this->affiliate->getStatistics($start_date, $end_date);
		$this->load->view('affiliate_management/statistics/ajax_view_statistics', $data);
	}

	/**
	 * overview : search affiliate statistics
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function searchStatistics() {
		if (!$this->permissions->checkPermissions('affiliate_statistics')) {
			$this->error_access();
		} else {
			$this->form_validation->set_rules('period', 'Period', 'trim|xss_clean');
			$this->form_validation->set_rules('start_date', 'Start Date', 'trim|xss_clean');
			$this->form_validation->set_rules('end_date', 'End Date', 'trim|xss_clean');

			if ($this->form_validation->run()) {
				$period = $this->input->post('period');
				$username = $this->input->post('username');
				// $type_date = $this->input->post('type_date');
				$start_date = $this->input->post('dateRangeValueStart');
				$end_date = $this->input->post('dateRangeValueEnd');
				$date_range_value = date("F j, Y", strtotime($start_date)) . ' - ' . date("F j, Y", strtotime($end_date));

				$this->session->set_userdata(array(
					'period' => $period,
					'username' => $username,
					// 'type_date' => $type_date,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'date_range_value' => $date_range_value,
				));
			}

			$this->viewAffiliateStatistics();

			// if ($this->form_validation->run() == false) {
			// 	$this->viewAffiliateStatistics();
			// } else {
			// 	$period = $this->input->post('period');
			// 	$username = $this->input->post('username');
			// 	$type_date = $this->input->post('type_date');
			// 	$start_date = $this->input->post('dateRangeValueStart');
			// 	$end_date = $this->input->post('dateRangeValueEnd');
			// 	$date_range_value = date("F j, Y", strtotime($start_date)) . ' - ' . date("F j, Y", strtotime($end_date));

			// 	$this->session->set_userdata(array(
			// 		'period' => $period,
			// 		'username' => $username,
			// 		'type_date' => $type_date,
			// 		'start_date' => $start_date,
			// 		'end_date' => $end_date,
			// 		'date_range_value' => $date_range_value,
			// 	));

			// 	$this->viewAffiliateStatistics();

			// 	// if ($period == "daily") {
			// 	// 	$this->viewAffiliateStatisticsDaily($start_date, $end_date);
			// 	// } elseif ($period == "weekly") {
			// 	// 	$this->viewAffiliateStatisticsWeekly($start_date, $end_date);
			// 	// } elseif ($period == "monthly") {
			// 	// 	$this->viewAffiliateStatisticsMonthly($start_date, $end_date);
			// 	// } elseif ($period == "yearly") {
			// 	// 	$this->viewAffiliateStatisticsYearly($start_date, $end_date);
			// 	// } else {
			// 	// 	$this->viewAffiliateStatisticsToday($start_date);
			// 	// }
			// }
		}
	}

	/**
	 * overview : view affiliate players
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function viewAffiliatePlayers($affiliate_id, $date) {
		if (!$this->permissions->checkPermissions('affiliate_statistics')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			$date = urldecode($date);
			$start_date = $date . " 00:00:00";
			$end_date = $date . " 23:59:59";

			$data['statistics'] = $this->affiliate_manager->getPlayerStatistics($affiliate_id, $start_date, $end_date);

			$this->template->write_view('main_content', 'affiliate_management/statistics/view_player_statistics', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : search statistics
	 *
	 * @return	void
	 */
	public function statisticsSearchPage() {
		if (!$this->permissions->checkPermissions('affiliate_statistics')) {
			$this->error_access();
		} else {
			$signup_range = null;
			$period = null;

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
			);

			if (!array_filter($search)) {
				redirect('affiliate_management/viewAffiliateStatistics');
			} else {
				$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

				$number_statistics_list = '';

				if ($this->session->userdata('number_statistics_list')) {
					$number_statistics_list = $this->session->userdata('number_statistics_list');
				} else {
					$number_statistics_list = 5;
				}

				$data['count_all'] = count($this->affiliate_manager->getSearchStatistics(null, null, $search));
				$config['base_url'] = "javascript:displayStatistics(";
				$config['total_rows'] = $data['count_all'];
				$config['per_page'] = $number_statistics_list;
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
				$data['statistics'] = $this->affiliate_manager->getSearchStatistics(null, null, $search);

				$this->template->write_view('main_content', 'affiliate_management/statistics/view_statistics', $data);
				$this->template->render();
			}
		}
	}

	/* ****** End of Affiliate Statistics ****** */

}

///END OF FILE///