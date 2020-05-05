<?php
trait affiliate_list_module {
	/* ****** Affiliate Lists ****** */

	/**
	 * Index Page of Affiliate Management
	 *
	 * @return	void
	 */
	public function index() {
		$this->session->unset_userdata('number_affiliate_list');
		$this->session->unset_userdata('aff_sort_by');
		$this->session->unset_userdata('aff_in');

		redirect('affiliate_management/aff_list', 'refresh');
	}

	/**
	 * view affiliate page
	 *
	 * @return	void
	 */
	// public function viewAffiliates() {
	// 	if (!$this->permissions->checkPermissions('view_affiliates')) {
	// 		$this->error_access();
	// 	} else {
	// 		redirect('/affiliate_management/postSearchPage');
	// 		$number_affiliate_list = '';
	// 		$sort_by = '';
	// 		$in = '';

	// 		if ($this->session->userdata('number_affiliate_list')) {
	// 			$number_affiliate_list = $this->session->userdata('number_affiliate_list');
	// 		} else {
	// 			$number_affiliate_list = 5;
	// 		}

	// 		if ($this->session->userdata('aff_sort_by')) {
	// 			$sort_by = $this->session->userdata('aff_sort_by');
	// 		} else {
	// 			$sort_by = 'a.createdOn';
	// 		}

	// 		if ($this->session->userdata('aff_in')) {
	// 			$in = $this->session->userdata('aff_in');
	// 		} else {
	// 			$in = 'desc';
	// 		}

	// 		$sort = array(
	// 			'sortby' => $sort_by,
	// 			'in' => $in,
	// 		);

	// 		$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

	// 		$data['count_all'] = count($this->affiliate_manager->getAllAffiliates(null, null, array()));
	// 		$config['base_url'] = "javascript:displayAffiliates(";
	// 		$config['total_rows'] = $data['count_all'];
	// 		$config['per_page'] = $number_affiliate_list;
	// 		$config['num_links'] = '1';

	// 		$config['first_tag_open'] = '<li>';
	// 		$config['last_tag_open'] = '<li>';
	// 		$config['next_tag_open'] = '<li>';
	// 		$config['prev_tag_open'] = '<li>';
	// 		$config['num_tag_open'] = '<li>';

	// 		$config['first_tag_close'] = '</li>';
	// 		$config['last_tag_close'] = '</li>';
	// 		$config['next_tag_close'] = '</li>';
	// 		$config['prev_tag_close'] = '</li>';
	// 		$config['num_tag_close'] = '</li>';

	// 		$config['cur_tag_open'] = "<li><span><b>";
	// 		$config['cur_tag_close'] = "</b></span></li>";

	// 		$this->pagination->initialize($config);

	// 		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
	// 		$data['current_page'] = floor(($this->uri->segment(4) / $config['per_page']) + 1);
	// 		$data['today'] = date("Y-m-d H:i:s");

	// 		$data['affiliates'] = $this->affiliate_manager->getAllAffiliates(null, null, $sort);

	// 		$data['games'] = $this->affiliate_manager->getGame();

	// 		$this->template->write_view('main_content', 'affiliate_management/affiliates/view_affiliate', $data);
	// 		$this->template->render();
	// 	}
	// }

	/**
	 * view affiliate list page
	 *
	 * @return	void
	 */
	// public function viewAffiliatesList($segment = "") {
	// 	$number_affiliate_list = '';
	// 	$sort_by = '';
	// 	$in = '';

	// 	if ($this->session->userdata('number_affiliate_list')) {
	// 		$number_affiliate_list = $this->session->userdata('number_affiliate_list');
	// 	} else {
	// 		$number_affiliate_list = 5;
	// 	}

	// 	if ($this->session->userdata('aff_sort_by')) {
	// 		$sort_by = $this->session->userdata('aff_sort_by');
	// 	} else {
	// 		$sort_by = 'a.createdOn';
	// 	}

	// 	if ($this->session->userdata('aff_in')) {
	// 		$in = $this->session->userdata('aff_in');
	// 	} else {
	// 		$in = 'desc';
	// 	}

	// 	$sort = array(
	// 		'sortby' => $sort_by,
	// 		'in' => $in,
	// 	);

	// 	$data['count_all'] = count($this->affiliate_manager->getAllAffiliates(null, null, array()));
	// 	$config['base_url'] = "javascript:displayAffiliates(";
	// 	$config['total_rows'] = $data['count_all'];
	// 	$config['per_page'] = $number_affiliate_list;
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
	// 	$data['current_page'] = floor(($this->uri->segment(4) / $config['per_page']) + 1);
	// 	$data['today'] = date("Y-m-d H:i:s");

	// 	$data['affiliates'] = $this->affiliate_manager->getAllAffiliates(null, $segment, $sort);
	// 	$data['games'] = $this->affiliate_manager->getGame();

	// 	$this->load->view('affiliate_management/affiliates/ajax_affiliate_list', $data);
	// }

	/**
	 * change columns to display
	 *
	 * @return	void
	 */
	// public function postChangeColumns() {
	// 	$name = $this->input->post('name') ? "checked" : "unchecked";
	// 	$level = $this->input->post('level') ? "checked" : "unchecked";
	// 	$email = $this->input->post('email') ? "checked" : "unchecked";
	// 	$country = $this->input->post('country') ? "checked" : "unchecked";
	// 	$parent = $this->input->post('parent') ? "checked" : "unchecked";
	// 	$tag = $this->input->post('tag') ? "checked" : "unchecked";
	// 	$status_col = $this->input->post('status_col') ? "checked" : "unchecked";
	// 	$registered_on = $this->input->post('registered_on') ? "checked" : "unchecked";
	// 	$available_bal = $this->input->post('available_bal') ? "checked" : "unchecked";

	// 	$data = array(
	// 		'name' => $name,
	// 		'level' => $level,
	// 		'email' => $email,
	// 		'country' => $country,
	// 		'parent' => $parent,
	// 		'tag' => $tag,
	// 		'status_col' => $status_col,
	// 		'registered_on' => $registered_on,
	// 		'available_bal' => $available_bal,
	// 	);

	// 	$this->session->set_userdata($data);
	// 	redirect('affiliate_management/viewAffiliates');
	// }

	/**
	 * sort affiliates
	 *
	 * @return	void
	 */
	// public function postSortPage() {
	// 	$sort_by = $this->input->post('sort_by');
	// 	$this->session->set_userdata('aff_sort_by', $sort_by);

	// 	$in = $this->input->post('in');
	// 	$this->session->set_userdata('aff_in', $in);

	// 	$number_affiliate_list = $this->input->post('number_affiliate_list');
	// 	$this->session->set_userdata('number_affiliate_list', $number_affiliate_list);

	// 	redirect('affiliate_management/viewAffiliates');
	// }

	/**
	 * search affiliates
	 *
	 * @return	void
	 */
	// public function postSearchPage() {
	// 	if (!$this->permissions->checkPermissions('view_affiliates')) {
	// 		$this->error_access();
	// 	} else {
	// 		$this->load->model(array('affiliatemodel'));
	// 		$signup_range = null;

	// 		$search_reg_date = $this->input->post('search_reg_date');
	// 		if ($search_reg_date == '1') {
	// 			$search_reg_date = true;
	// 		} else {
	// 			$search_reg_date = false;
	// 		}

	// 		$search = array(
	// 			"username" => $this->input->post('username'),
	// 			"email" => $this->input->post('email'),
	// 			"game" => $this->input->post('game'),
	// 			"firstname" => $this->input->post('firstname'),
	// 			"lastname" => $this->input->post('lastname'),
	// 			"status" => $this->input->post('status'),
	// 			"parentId" => $this->input->post('parentId'),
	// 		);

	// 		$this->utils->debug_log('search_reg_date', $search_reg_date);
	// 		// if ($this->input->post('alltime') == null) {
	// 		$data['input'] = $this->input->post();
	// 		$data['input']['search_reg_date'] = $search_reg_date;
	// 		if ($search_reg_date) {
	// 			if ($this->input->post('start_date') && $this->input->post('end_date')) {
	// 				$search['signup_range'] = "'" . $this->input->post('start_date') . "' AND '" . $this->input->post('end_date') . "'";
	// 			} else {
	// 				$search['signup_range'] = "'" . date("Y-m-d 00:00:00") . "' AND '" . date("Y-m-d 23:59:59") . "'";
	// 				$data['input']['start_date'] = date("Y-m-d 00:00:00");
	// 				$data['input']['end_date'] = date("Y-m-d 23:59:59");
	// 			}
	// 		}
	// 		// }

	// 		$number_affiliate_list = '';

	// 		if ($this->session->userdata('number_affiliate_list')) {
	// 			$number_affiliate_list = $this->session->userdata('number_affiliate_list');
	// 		} else {
	// 			$number_affiliate_list = 5;
	// 		}

	// 		$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

	// 		$affiliateRows = $this->affiliatemodel->searchAllAffiliates(null, null, $search);
	// 		$data['count_all'] = count($affiliateRows);
	// 		$config['base_url'] = "javascript:displayAffiliates(";
	// 		$config['total_rows'] = $data['count_all'];
	// 		$config['per_page'] = $number_affiliate_list;
	// 		$config['num_links'] = '1';

	// 		$config['first_tag_open'] = '<li>';
	// 		$config['last_tag_open'] = '<li>';
	// 		$config['next_tag_open'] = '<li>';
	// 		$config['prev_tag_open'] = '<li>';
	// 		$config['num_tag_open'] = '<li>';

	// 		$config['first_tag_close'] = '</li>';
	// 		$config['last_tag_close'] = '</li>';
	// 		$config['next_tag_close'] = '</li>';
	// 		$config['prev_tag_close'] = '</li>';
	// 		$config['num_tag_close'] = '</li>';

	// 		$config['cur_tag_open'] = "<li><span><b>";
	// 		$config['cur_tag_close'] = "</b></span></li>";

	// 		$this->pagination->initialize($config);

	// 		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
	// 		$data['current_page'] = floor(($this->uri->segment(4) / $config['per_page']) + 1);
	// 		$data['today'] = date("Y-m-d H:i:s");

	// 		$data['affiliates'] = $affiliateRows;
	// 		$data['affiliates_list'] = $this->affiliatemodel->searchAllAffiliates(null, null, null);
	// 		$data['games'] = $this->affiliate_manager->getGame();
	// 		$data['tags'] = $this->affiliate_manager->getActiveTags();

	// 		$this->template->write_view('main_content', 'affiliate_management/affiliates/view_affiliate', $data);
	// 		$this->template->render();
	// 	}
	// }

	/**
	 * selected affiliates
	 *
	 * @return	void
	 */
	public function selectedAffiliates() {
		if ($this->input->post('affiliates')) {
			$affiliates = $this->input->post('affiliates');

			if (is_string($this->input->post('affiliates'))) {
				$affiliates = explode(', ', $this->input->post('affiliates'));
			}

			$affiliate_ids = implode(', ', $affiliates);

			$data['affiliate_ids'] = $affiliate_ids;
			$data['affiliates'] = $this->affiliate_manager->getSelectedAffiliates($affiliate_ids);
			$data['tags'] = $this->affiliate_manager->getActiveTags();

			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');
			$this->template->write_view('main_content', 'affiliate_management/affiliates/action_page', $data);
			$this->template->render();
		} else {
			$message = lang('con.aff03');
			$this->alertMessage(2, $message);
			redirect('affiliate_management/aff_list');
		}
	}

	public function actionType() {
		$this->form_validation->set_rules('action_type', 'Action Type', 'trim|required|xss_clean');

		if ($this->input->post('action_type') == 'tag') {
			$this->form_validation->set_rules('tags', 'Tag', 'trim|required|xss_clean');
		}

		if ($this->form_validation->run() == false) {
			$message = lang('con.aff04');
			$this->alertMessage(2, $message);
			$this->selectedAffiliates();
		} else {
			$action = $this->input->post('action_type');
			$period = $this->input->post('period');
			$today = date("Y-m-d H:i:s");
			$user_id = $this->authentication->getUserId();

			if ($action == 'locked') {
				foreach (explode(', ', $this->input->post('affiliates')) as $affiliate_id) {
					$data = array(
						'status' => '2',
						'updatedOn' => date('Y-m-d H:i:s'),
					);

					$this->affiliate_manager->editAffiliates($data, $affiliate_id);
				}

				$this->saveAction('Freeze Affiliates', "User " . $this->authentication->getUsername() . " has freeze affiliates");
				$message = lang('con.aff05');
				$this->alertMessage(1, $message);
				redirect('affiliate_management/aff_list');
			} elseif ($action == 'tag') {
				// if TAG
				$tags = $this->input->post('tags');
				foreach (explode(', ', $this->input->post('affiliates')) as $affiliate_id) {
					$check = $this->affiliate_manager->getAffiliateTag($affiliate_id);

					if (!$check) {
						$data = array(
							'affiliateId' => $affiliate_id,
							'taggerId' => $user_id,
							'tagId' => $tags,
							'status' => 1,
							'createdOn' => $today,
							'updatedOn' => $today,
						);

						$this->affiliate_manager->insertAffiliateTag($data);
					} else {
						$data = array(
							'tagId' => $tags,
							'updatedOn' => $today,
						);
						$this->affiliate_manager->changeAffiliateTag($check['affiliateId'], $data);
					}
				}

				$this->saveAction('Edit Tag for Affiliates', "User " . $this->authentication->getUsername() . " has edited Tag to affiliates");
				$message = lang('con.aff06');
				$this->alertMessage(1, $message);
				redirect('affiliate_management/aff_list');
			} elseif ($action == lang('Activate Selected')) {

				$affiliates = $this->input->post('affiliate');
				if (is_array($affiliates) && ! empty($affiliates)) {
					$this->load->model('affiliatemodel');

					foreach ($affiliates as $affiliate_id) {
						# active affiliate
						if ($this->affiliatemodel->active($affiliate_id)) {
							$affiliate = $this->affiliatemodel->getAffiliateById($affiliate_id);
							$this->syncAffCurrentToMDBWithLock($affiliate_id, $affiliate['username'], false);

							if(!empty($affiliate['email'])){
								#sending email
								$this->load->library(['email_manager']);
						        $template = $this->email_manager->template('affiliate', 'affiliate_activated', array('affiliate_id' => $affiliate_id));
						        $template_enabled = $template->getIsEnableByTemplateName();
						        if($template_enabled['enable']){
						        	$template->sendingEmail($affiliate['email'], Queue_result::CALLER_TYPE_ADMIN, $this->authentication->getUserId());
						        }
							}
						}
					}

					$this->saveAction('Activate Affiliates', "User " . $this->authentication->getUsername() . " has activated affiliates");
					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('con.aff52'));
				}
				redirect('affiliate_management/aff_list');
			}
		}
	}

	public function new_additional_affdomain($affId){
		$this->load->model(array('affiliatemodel'));

		$this->form_validation->set_rules('affdomain', 'Affdomain', 'trim|xss_clean|required|valid_domain');
        if ($this->form_validation->run() == false) {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save failed').': '.lang('validation.badDomain'));
            return redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
        }

		$affdomain = trim($this->input->post('affdomain'));

		//try fix affdomain
		if(strpos($affdomain, 'http://') !== false || strpos($affdomain, 'https://') !== false) {
			if($affdomain == 'http://'|| $affdomain == 'https://'){
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Please fill in domain.'));
				return redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
			}
			else{
				$affdomain = trim($this->input->post('affdomain'));
			}
		}

		if($this->affiliatemodel->existsAffdomain($affId, $affdomain)){
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save failed because the domain exists'));
			return redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
		}

		$success = $this->affiliatemodel->newAdditionalAffdomain($affId, $affdomain);

		if ($success) {
			$username=$this->affiliatemodel->getUsernameById($affId);
			$this->syncAffCurrentToMDBWithLock($affId, $username, false);
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
		}

		redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
	}

	public function change_additional_affdomain($affId, $affTrackingId){
		$this->load->model(array('affiliatemodel'));

		$this->form_validation->set_rules('affdomain', 'Affdomain', 'trim|xss_clean|required|valid_domain');
		if ($this->form_validation->run() == false) {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save failed').': '.lang('validation.badDomain'));
            return redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
		}

		$affdomain = trim($this->input->post('affdomain'));
		if(empty($affdomain)){
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Please fill in domain.'));
			return redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
		}

		//try fix affdomain
		if(strpos($affdomain, 'http://') !== false || strpos($affdomain, 'https://') !== false) {
			if($affdomain == 'http://'|| $affdomain == 'https://'){
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Please fill in domain.'));
				return redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
			}
			else{
				$affdomain = trim($this->input->post('affdomain'));
			}
		}

		if($this->affiliatemodel->existsAffdomain(null, $affdomain, $affTrackingId)){
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save failed because the domain exists'));
			return redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
		}

		$success = $this->affiliatemodel->updateAdditionalAffdomain($affTrackingId, $affdomain);


		if ($success) {
			$username=$this->affiliatemodel->getUsernameById($affId);
			$this->syncAffCurrentToMDBWithLock($affId, $username, false);
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
		}

		redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
	}

	public function remove_additional_affdomain($affId, $affTrackingId){

		$this->load->model(array('affiliatemodel'));
		$success=false;
		if(!empty($affId) && !empty($affTrackingId)){
			$success=$this->affiliatemodel->removeAdditionalAffdomain($affTrackingId);
		}

		if ($success) {
			$username=$this->affiliatemodel->getUsernameById($affId);
			$this->syncAffCurrentToMDBWithLock($affId, $username, false);
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
		}
		redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
	}

	public function new_affdomain(){
		$this->load->model(array('affiliatemodel'));

		$this->form_validation->set_rules('affdomainContent', 'Affdomain Content', 'trim|xss_clean|required|valid_domain');
        if ($this->form_validation->run() == false) {
            $result['success'] = false;
            return $this->returnJsonResult($result);
		}

		$affId = $this->input->post('affiliateId');
		$affdomainTitle = $this->input->post('affdomainTitle');
        $affdomainContent = $this->input->post('affdomainContent');


		$affdomain = $affdomainTitle.'://'.$affdomainContent;
		$result['success'] = false;

		if ($this->affiliatemodel->existsDedicatedAffdomain($affdomain)) {
			$result['success'] = null;
		}
		else {
			$success = $this->affiliatemodel->updateAffdomain($affId, $affdomain);

			if ($success) {
				$username = $this->affiliatemodel->getUsernameById($affId);
				$result['success'] = $this->syncAffCurrentToMDBWithLock($affId, $username, false);
			}else {
				$result['success'] = false;
			}
		}

		return $this->returnJsonResult($result);
	}

	public function change_affdomain(){
		// $success=true;
		$this->load->model(array('affiliatemodel','agency_model'));

		$this->form_validation->set_rules('affdomainContent', 'Affdomain Content', 'trim|xss_clean|required|valid_domain');

		if ($this->form_validation->run() == false) {
			$result['success'] = false;
			return $this->returnJsonResult($result);
		}

		$affId = $this->input->post('affiliateId');
        $affdomainTitle = $this->input->post('affdomainTitle');
		$affdomainContent = $this->input->post('affdomainContent');

		$affdomain = $affdomainTitle.'://'.$affdomainContent;
		$result['success'] = false;

		// -- Also check if domain already exists as agent domain
		if($this->agency_model->existsAdditionalAgentDomain($affdomain) || $this->affiliatemodel->existsDedicatedAffdomain($affdomain)){
			$result['success'] = null;
		}
		else {
			$success = $this->affiliatemodel->updateAffdomain($affId, $affdomain);

			if ($success) {
				$username=$this->affiliatemodel->getUsernameById($affId);
				$result['success'] = $this->syncAffCurrentToMDBWithLock($affId, $username, false);
			} else {
				$result['success'] = false;
			}

		}

		return $this->returnJsonResult($result);
	}

	public function remove_affdomain($affId){
		$this->load->model(array('affiliatemodel'));
		$success = false;
		if(!empty($affId)){
			$success = $this->affiliatemodel->updateAffdomain($affId, NULL);
		}

		if ($success) {
			$username = $this->affiliatemodel->getUsernameById($affId);
			$result['success'] = $this->syncAffCurrentToMDBWithLock($affId, $username,false);
		} else {
			$result['success'] = false;
		}

		return $this->returnJsonResult($result);
	}

	public function new_source_code($affId){
		$this->load->model(array('affiliatemodel'));

		$this->form_validation->set_rules('sourceCode', 'Source Code', 'trim|xss_clean|required|regex_match[/^[a-z0-9]+$/]');
        $this->form_validation->set_rules('remarks', 'Remarks', 'trim|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Wrong format.'));
            return redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
        }

		$sourceCode = $this->input->post('sourceCode');
		$remarks = $this->input->post('remarks');

		if($this->affiliatemodel->existsSourceCode($affId, $sourceCode)){
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save failed because the domain exists'));
			return redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
		}

		$success = $this->affiliatemodel->newSourceCode($affId, $sourceCode, $remarks);

		if ($success) {
			$username=$this->affiliatemodel->getUsernameById($affId);
			$this->syncAffCurrentToMDBWithLock($affId, $username, false);
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
		}

		redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
	}

	public function change_source_code($affId, $affTrackingId){
		$this->load->model(array('affiliatemodel'));

        $this->form_validation->set_rules('sourceCode', 'Source Code', 'trim|xss_clean|required|regex_match[/^[a-z0-9]+$/]');
		$this->form_validation->set_message('sourceCode', "Only a-z, A-Z, 0-9, -, _, and @ is allowed.");


        $this->form_validation->set_rules('remarks', 'Remarks', 'trim|xss_clean|htmlspecialchars');

		if ($this->form_validation->run() == false) {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Wrong format.'));
            return redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
		}

        $sourceCode = $this->input->post('sourceCode');
        $remarks = $this->input->post('remarks');

		if($this->affiliatemodel->existsSourceCode($affId, $sourceCode, $affTrackingId)){
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save failed because the source code exists'));
			return redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
		}

		$success = $this->affiliatemodel->updateSourceCode($affTrackingId, $sourceCode, $remarks);

		if ($success) {
			$username=$this->affiliatemodel->getUsernameById($affId);
			$this->syncAffCurrentToMDBWithLock($affId, $username, false);
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
		}

		redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
	}

	public function remove_source_code($affId, $affTrackingId){
		$this->load->model(array('affiliatemodel'));
		$success=false;
		if(!empty($affId) && !empty($affTrackingId)){
			$success=$this->affiliatemodel->removeSourceCode($affTrackingId);
		}

		if ($success) {
			$username=$this->affiliatemodel->getUsernameById($affId);
			$this->syncAffCurrentToMDBWithLock($affId, $username, false);
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
		}
		redirect('/affiliate_management/userInformation/' . $affId. '#aff_additional_domain_list');
	}

	/**
	 * show user information of affiliate
	 *
	 * @return	void
	 */
	public function userInformation($affiliate_id) {
		if ( ! $this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {

			$this->load->library(array('salt'));
			$this->load->model(array('external_system', 'affiliate_earnings', 'transactions', 'affiliatemodel'));

			$data['admin_user_id'] 		 		= $this->authentication->getUserId();
			$data['affiliateId'] 		 		= $affiliate_id;
			$data['player_register_uri'] 		= $this->config->item('player_register_uri');
			$data['bank'] 				 		= $this->affiliatemodel->getAffiliatePaymentById($affiliate_id);
			$data['subaffiliates'] 		 		= $this->affiliatemodel->getAllAffiliatesUnderAffiliate($affiliate_id);
			$data['affiliateSettings'] 	 		= $this->affiliatemodel->getAffTermsSettings($affiliate_id);
			$data['commonSettings'] 	 		= $this->affiliatemodel->getDefaultAffSettings();
			$data['aff_additional_domain_list'] = $this->emptyOrArray($this->affiliatemodel->getAdditionalDomainList($affiliate_id));
			$data['aff_source_code_list'] 		= $this->emptyOrArray($this->affiliatemodel->getSourceCodeList($affiliate_id));
			$data['game'] 				 		= $this->external_system->getAllActiveSytemGameApi();
			$data['transactions'] 		 		= $this->transactions->getAffTransactions($affiliate_id);

			if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')) {
				$data['earnings'] = $this->affiliate_earnings->getAllPlatformEarningsById($affiliate_id);
			} else if ($this->utils->isEnabledFeature('switch_to_affiliate_daily_earnings')) {
				$data['earnings'] = $this->affiliate_earnings->getAllDailyEarningsById($affiliate_id);
			} else {
				if ($this->utils->getConfig('use_old_affiliate_commission_formula')) {
					$data['earnings'] = $this->affiliate_earnings->getAllMonthlyEarningsById($affiliate_id);
				} else {
					$data['earnings'] = $this->affiliate_earnings->getAllMonthlyEarningsById_2($affiliate_id);
				}
			}

			// $data['earnings'] 		  = $this->affiliate_manager->getMonthlyEarningsById($affiliate_id, 1);
			// $data['operator_settings'] = json_decode($this->affiliate->getAffiliateSettings());

			$data['domain'] = $this->affiliatemodel->getAffiliateDomain($affiliate_id);
			if( ! empty($data['domain'])){
				$data['first_domain'] = $data['domain'][0]['domainName'];
			} else {
				$data['first_domain'] = NULL;
			}

			$data['affiliate'] = $this->affiliatemodel->getAffiliateById($affiliate_id);
			if(empty($data['affiliate'])){
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Affiliate not exist.'));
				redirect('/affiliate_management/aff_list');
			}
			if ( ! empty($data['affiliate']['parentId'])) {
				$data['parent'] = $this->affiliatemodel->getAffiliateById($data['affiliate']['parentId']);
			} else {
				$data['parent'] = NULL;
			}

			$data['hide_password'] = ! empty($data['affiliate']['password']) ? $this->salt->decrypt($data['affiliate']['password'], $this->getDeskeyOG()) : '';
			$data['sublink'] = $this->utils->getSystemUrl('aff') . '/' . $this->config->item('aff_sub_affiliate_link') . '/'. $data['affiliate']['trackingCode'];

            $this->template->add_css('resources/third_party/bootstrap-toggle-master/css/bootstrap-toggle.min.css');
			$this->loadTemplate(lang('Affiliate Management'), '', '', 'affiliate');

			$this->addBoxDialogToTemplate();
			$this->addJsTreeToTemplate();
            $this->template->add_js('resources/third_party/bootstrap-toggle-master/js/bootstrap-toggle.min.js');

			$this->template->write_view('main_content', 'affiliate_management/affiliates/view_information', $data);
			$this->template->render();
		}
	}

	public function active($affiliate_id, $from_url = null) {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			$this->load->model(array('affiliatemodel'));

			# active affiliate
			if ($this->affiliatemodel->active($affiliate_id)) {
				$affiliate = $this->affiliatemodel->getAffiliateById($affiliate_id);
				$this->syncAffCurrentToMDBWithLock($affiliate_id, $affiliate['username'], false);

				if(!empty($affiliate['email'])){
					#sending email
					$this->load->library(['email_manager']);
			        $template = $this->email_manager->template('affiliate', 'affiliate_activated', array('affiliate_id' => $affiliate_id));
			        $template_enabled = $template->getIsEnableByTemplateName();
			        if($template_enabled['enable']){
			        	$template->sendingEmail($affiliate['email'], Queue_result::CALLER_TYPE_ADMIN, $this->authentication->getUserId());
			        }
				}

				$message = lang('affiliate.actived');
				$this->utils->debug_log($affiliate_id, $message);
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
			} else {
				$message = lang('error.affiliate.actived');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message); //will set and send message to the user

			}
			if ($from_url == "aff_list") {
				redirect("affiliate_management/aff_list/" . $affiliate_id);
			}
			redirect("affiliate_management/userinformation/" . $affiliate_id);
		}
	}

	public function inactive($affiliate_id) {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			$this->load->model(array('affiliatemodel'));
			//active affiliate
			if ($this->affiliatemodel->inactive($affiliate_id)) {

				$username=$this->affiliatemodel->getUsernameById($affiliate_id);
				$this->syncAffCurrentToMDBWithLock($affiliate_id, $username, false);

				$message = lang('affiliate.inactived');
				$this->utils->debug_log($affiliate_id, $message);
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
			} else {
				$message = lang('error.affiliate.inactived');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message); //will set and send message to the user

			}
			redirect("affiliate_management/userinformation/" . $affiliate_id);
		}
	}

	/**
	 * user information: edit affiliate info
	 *
	 * @return	void
	 */
	public function editAffiliateInfo($affiliate_id) {
		if (!$this->permissions->checkPermissions('edit_affiliate_info')) {
			redirect('affiliate_management/error_access', 'refresh');
		} else {

			$this->load->model('affiliatemodel');

			//$data['affiliates_parents'] = $this->affiliatemodel->getAllAllowedParentAffiliates();
			$data['affiliates'] = $this->affiliatemodel->getAffiliateById($affiliate_id);
			$data['affiliates_domain'] = $this->affiliatemodel->getAffDomain();
			if (!empty($data['affiliates']['parentId'])) {
				//$data['parent_id'] = $data['affiliates']['parentId'];
				$data['affiliates_parent'] = $this->affiliatemodel->getAffiliateById($data['affiliates']['parentId']);
			} else {
				//$data['is_parent'] = true;
				//$data['parent_id'] = $data['affiliates']['affiliateId'];
				$data['affiliates_parent'] = '';
			}

            $data['mobile_dailing_num'] = $data['phone_dailing_num'] = $data['mobile_num'] = $data['phone_num'] = '';
            if(!empty($data['affiliates']['mobile'])){
                $mobile_detail = explode(' ',$data['affiliates']['mobile']);
                $data['mobile_dailing_num'] = (isset($mobile_detail[0])) ? $mobile_detail[0] : '';
                $data['mobile_num'] = (isset($mobile_detail[1])) ? $mobile_detail[1] : '';
                if(count($mobile_detail) == 1){
                    //if no dailing before
                    $data['mobile_num'] = (isset($mobile_detail[0])) ? $mobile_detail[0] : '';
                }
            }

            if(!empty($data['affiliates']['phone'])) {
                $phone_detail = explode(' ', $data['affiliates']['phone']);
                $data['phone_dailing_num'] = (isset($phone_detail[0])) ? $phone_detail[0] : '';
                $data['phone_num'] = (isset($phone_detail[1])) ? $phone_detail[1] : '';
                if(count($phone_detail) == 1){
                    //if no dailing before
                    $data['phone_num'] = (isset($phone_detail[0])) ? $phone_detail[0] : '';
                }
            }


            $data['countryNumList'] = unserialize(COUNTRY_NUMBER_LIST_FULL);
            if(!empty($data['countryNumList'])){
                $data['frequentlyUsedCountryNumList'] = array(
                    'China' => $data['countryNumList']['China'],
                    'Thailand' => $data['countryNumList']['Thailand'],
                    'Indonesia' => $data['countryNumList']['Indonesia'],
                    'Vietnam' => $data['countryNumList']['Vietnam'],
                    'Malaysia' => $data['countryNumList']['Malaysia'],
                );
            }

            $this->loadTemplate('Affiliate Management', '', '', 'affiliate');
            $this->template->add_js($this->utils->thirdpartyUrl('bootstrap-select/1.12.4/bootstrap-select.min.js'));
            $this->template->add_css($this->utils->thirdpartyUrl('bootstrap-select/1.12.4/bootstrap-select.min.css'));
			$this->template->write_view('main_content', 'affiliate_management/affiliates/edit_affiliate_info', $data);
			$this->template->render();
		}
	}

	/**
	 * user information: edit affiliate info
	 *
	 * @return	void
	 */
	public function verifyEditAffiliate($affiliate_id) {
		if (!$this->permissions->checkPermissions('edit_affiliate_info')) {
			$this->error_access();
		} else {
			$this->form_validation->set_rules('firstname', 'First Name', 'trim|xss_clean|required');
			$this->form_validation->set_rules('lastname', 'Last Name', 'trim|xss_clean');
			$this->form_validation->set_rules('company', 'Company', 'trim|xss_clean');
			$this->form_validation->set_rules('occupation', 'Occupation', 'trim|xss_clean');
			$this->form_validation->set_rules('birthday', 'Birthday', 'trim|xss_clean|callback_checkAge');
			$this->form_validation->set_rules('gender', 'Gender', 'trim|xss_clean');
			$this->form_validation->set_rules('city', 'City', 'trim|xss_clean');
			$this->form_validation->set_rules('address', 'Address', 'trim|xss_clean');
			$this->form_validation->set_rules('zip', 'Zip Code', 'trim|xss_clean|numeric');
			$this->form_validation->set_rules('state', 'State', 'trim|xss_clean');
			// $this->form_validation->set_rules('country', 'Country', 'trim|xss_clean|required');
			$this->form_validation->set_rules('mobile', 'Mobile', 'trim|xss_clean|numeric');
			$this->form_validation->set_rules('phone', 'Phone', 'trim|xss_clean|numeric');
			$this->form_validation->set_rules('im1', 'IM1', 'trim|xss_clean|callback_checkIM1Type');
			$this->form_validation->set_rules('imtype1', 'IM1 Type', 'trim|xss_clean');
			$this->form_validation->set_rules('im2', 'IM2', 'trim|xss_clean|callback_checkIM2Type');
			$this->form_validation->set_rules('imtype2', 'IM2 Type', 'trim|xss_clean');
			// $this->form_validation->set_rules('mode_of_contact', 'Preferred Mode of Contact', 'trim|xss_clean|callback_checkModeOfContact|alpha_numeric');
			$this->form_validation->set_rules('website', 'Website', 'trim|xss_clean');
			$this->form_validation->set_rules('affdomain', 'Affiliate Domain', 'trim|xss_clean|callback_validateAffDomain');
			// $this->form_validation->set_rules('parent_username', 'Parent Affiliate', 'trim|xss_clean|callback_checkAffUsername');
			$this->form_validation->set_rules('prefix_of_player', 'Prefix of player', 'trim|xss_clean|callback_checkPrefixOfPlayer');

			if ($this->input->post('email') == $this->input->post('email_db')) {
				$this->form_validation->set_rules('email', 'Email', 'trim|xss_clean|valid_email');
			} else {
				$this->form_validation->set_rules('email', 'Email', 'trim|xss_clean|valid_email|is_unique[affiliates.email]');
			}

			if ($this->form_validation->run() == false) {
				$message = lang('con.aff51');
				$this->alertMessage(2, $message); //will set and send message to the user
				$this->editAffiliateInfo($affiliate_id);
			} else {
				// $this->utils->debug_log('affdomain', $this->input->post('affdomain'));
				$bdate = substr($this->input->post('birthday'), 0, 4) == "0000" ? null : $this->input->post('birthday');
				$data = array(
					'firstname' => $this->input->post('firstname'),
					'lastname' => $this->input->post('lastname'),
					'company' => $this->input->post('company'),
					'occupation' => $this->input->post('occupation'),
					'birthday' => $bdate,
					'gender' => $this->input->post('gender'),
					'email' => $this->input->post('email'),
					'city' => $this->input->post('city'),
					'address' => $this->input->post('address'),
					'zip' => $this->input->post('zip'),
					'state' => $this->input->post('state'),
					'country' => $this->input->post('country'),
					'mobile' => !empty($this->input->post('mobile')) ? $this->input->post('mobile_dialing_code').' '.$this->input->post('mobile') : '',
					'phone' => !empty($this->input->post('phone')) ? $this->input->post('phone_dialing_code').' '.$this->input->post('phone') : '',
					'im1' => $this->input->post('im1'),
					'imType1' => $this->input->post('imtype1'),
					'im2' => $this->input->post('im2'),
					'imType2' => $this->input->post('imtype2'),
					'modeOfContact' => $this->input->post('mode_of_contact'),
					'website' => $this->input->post('website'),
					'currency' => $this->input->post('currency'),
					'prefix_of_player' => $this->input->post('prefix_of_player'),
				);

				if(empty($data['prefix_of_player'])){
					$data['prefix_of_player']=null;
				}

				// $parent_id = $this->input->post('parent_id');
				// $data['parentId'] = $affiliate_id == $this->input->post('parent_id') ? "0" : $parent_id;
				// $parent_username = $this->input->post('parent_username');
				// if (!empty($parent_username)) {
				// 	$parentId = $this->affiliatemodel->getAffiliateIdByUsername($parent_username);
				// 	$data['parentId'] = $parentId;
				// }


				if (empty($this->input->post('affdomain'))) {
					$data['affdomain']=null;
				}
				else{
					//try fix affdomain
					$data['affdomain'] = trim($this->input->post('affdomain'));
					$affdomain = $data['affdomain'];

					if(strpos($affdomain, 'http://') !== false || strpos($affdomain, 'https://') !== false) {
						if($affdomain == 'http://'|| $affdomain == 'https://'){
							redirect("affiliate_management/editAffiliateInfo/" . $affiliate_id);
						}
						else{
							$data['affdomain'] = trim($this->input->post('affdomain'));
						}

					}
				}

				foreach ($data as $key => $value) {
                    $data[$key] = $this->stripHTMLtags($value);
				}

				$this->affiliate_manager->editAffiliates($data, $affiliate_id);

				$username=$this->affiliatemodel->getUsernameById($affiliate_id);
				$this->syncAffCurrentToMDBWithLock($affiliate_id, $username, false);

				$message = lang('con.aff50');
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
				redirect("affiliate_management/userInformation/" . $affiliate_id);
			}
		}
	}

	public function checkPrefixOfPlayer($prefix_of_player){
		$affiliate_id = $this->input->post('affiliateId');
		if (!empty($affdomain)) {
			$this->load->model(array('affiliatemodel'));
			$success = !$this->affiliatemodel->existsPrefix($affiliate_id, $prefix_of_player);
			if (!$success) {
				$this->form_validation->set_message('checkPrefixOfPlayer', lang("Duplicate prefix"));
			}
			return $success;
		}
		return true;
	}

	public function validateAffDomain($affdomain) {
		$affiliate_id = $this->input->post('affiliateId');
		if (!empty($affdomain)) {
			$this->load->model(array('affiliatemodel'));
			$success = !$this->affiliatemodel->existsAffDomain($affiliate_id, $affdomain);
			if (!$success) {
				$this->form_validation->set_message('validateAffDomain', lang("Duplicate affiliate domain"));
			}
			return $success;
		}
		return true;
	}

	public function checkAffUsername($parent_username) {
		// $parent_username = $this->input->post('parent_username');
		if (!empty($parent_username)) {
			$this->load->model(array('affiliatemodel'));
			$affId = $this->affiliatemodel->getAffiliateIdByUsername($parent_username);
			$success = !empty($affId);
			if (!$success) {
				$this->form_validation->set_message('checkAffUsername', lang("Unknown affiliate username"));
			}
		}
		return true;
	}

	/**
	 * callback check IM1
	 *
	 * @return	int
	 */
	public function checkIM1Type() {
		$im = $this->input->post('im1');
		$imtype = $this->input->post('imtype1');

		if (preg_match('/[!#$%^&*()+=?.,~|{}:;<>`]/', $im) || preg_match("^/^", $im) || preg_match("^'^", $im) || preg_match('^"^', $im)) {
			$this->form_validation->set_message('checkIM1Type', "Only a-z, A-Z, 0-9, -, _, and @ is allowed.");
			return false;
		} else if ($im == null && $imtype != null) {
			$this->form_validation->set_message('checkIM1Type', "Please provide an IM type for your provided IM1.");
			return false;
		}
		return true;
	}

	/**
	 * callback check IM2
	 *
	 * @return	int
	 */
	public function checkIM2Type() {
		$im = $this->input->post('im2');
		$imtype = $this->input->post('imtype2');

		if (preg_match('/[!#$%^&*()+=?.,~|{}:;<>`]/', $im) || preg_match("^/^", $im) || preg_match("^'^", $im) || preg_match('^"^', $im)) {
			$this->form_validation->set_message('checkIM2Type', "Only a-z, A-Z, 0-9, -, _, and @ is allowed.");
			return false;
		} else if ($im == null && $imtype != null) {
			$this->form_validation->set_message('checkIM2Type', "Please provide an IM type for your provided IM2.");
			return false;
		}

		return true;
	}

	/**
	 * callback check mode of contact
	 *
	 * @return	int
	 */
	public function checkModeOfContact() {
		$mobile = $this->input->post('mobile');
		$phone = $this->input->post('phone');
		$im1 = $this->input->post('im1');
		$im2 = $this->input->post('im2');

		$mode_of_contact = $this->input->post('mode_of_contact');

		if ($mode_of_contact == 'mobile' && $mobile == null) {
			$this->form_validation->set_message('checkModeOfContact', "You choose Mobile Phone as your mode of contact. Please provide your mobile number.");
			return false;
		} else if ($mode_of_contact == 'phone' && $phone == null) {
			$this->form_validation->set_message('checkModeOfContact', "You choose Phone as your mode of contact. Please provide your phone number.");
			return false;
		} else if ($mode_of_contact == 'im' && ($im1 == null && $im2 == null)) {
			$this->form_validation->set_message('checkModeOfContact', "You choose Instant Message as your mode of contact. Please provide your instant message contact.");
			return false;
		}

		return true;
	}

	/**
	 * callback check Age
	 *
	 * @return	int
	 */
	public function checkAge() {
		$birthday = $this->input->post('birthday');
		$today = new DateTime();
		$birthday = new DateTime($birthday);
		$interval = $today->diff($birthday);
		$age = (int)$interval->format('%y');

		if ($age < 18) {
			$this->form_validation->set_message('checkAge', "You must be 18 years old and above to sign up.");
			return false;
		}

		return true;
	}

	/**
	 * freeze affiliate
	 *
	 * @return	void
	 */
	public function freezeAffiliate($affiliate_id, $username) {
		$data = array(
			'status' => '2',
			'updatedOn' => date('Y-m-d H:i:s'),
		);
		$this->affiliate_manager->editAffiliates($data, $affiliate_id);

		$message = lang('con.aff08') . ": " . $username;
		$this->alertMessage(1, $message);

		redirect('affiliate_management/aff_list');
	}

	/**
	 * unfreeze affiliate
	 *
	 * @return	void
	 */
	public function unfreezeAffiliate($affiliate_id, $username) {
		$data = array(
			'status' => '0',
			'updatedOn' => date('Y-m-d H:i:s'),
		);
		$this->affiliate_manager->editAffiliates($data, $affiliate_id);

		$message = lang('con.aff09') . ": " . $username;
		$this->alertMessage(1, $message);

		redirect('affiliate_management/aff_list');
	}

	/**
	 * activate affiliate
	 *
	 * @return	void
	 */
	public function activateAffiliate($affiliate_id, $username) {
		$data = array(
			'status' => '0',
			'updatedOn' => date('Y-m-d H:i:s'),
		);
		$this->affiliate_manager->editAffiliates($data, $affiliate_id);

		$message = lang('con.aff52') . ": " . $username;
		$this->alertMessage(1, $message);

		redirect('affiliate_management/aff_list');
	}

	/**
	 * create tracking code
	 *
	 * @return	void
	 */
	public function createCode($affiliate_id) {
		$this->form_validation->set_rules('tracking_code', 'Tracking Code', 'trim|xss_clean|required|min:3|max:8|alpha_numeric|is_unique[affiliates.trackingCode]');

		if ($this->form_validation->run() == false) {
			$message = lang('con.aff13');
			$this->alertMessage(2, $message);

			redirect('affiliate_management/userInformation/' . $affiliate_id, 'refresh');
		} else {

			$adminUserId = $this->authentication->getUserId();
			$this->load->model(array('affiliatemodel'));

			$this->affiliatemodel->startTrans();

			$trackingCode = $this->input->post('tracking_code');
			$success = $this->affiliatemodel->updateTrackingCode($affiliate_id, $trackingCode, $adminUserId);

			if ($success) {
				$success = $this->affiliatemodel->endTransWithSucc();
			} else {
				//rollback
				$this->affiliatemodel->rollbackTrans();
				$this->utils->error_log('rollback on create code', $affiliate_id);
			}

			// $data = array(
			// 	'trackingCode' => $this->input->post('tracking_code'),
			// 	'status' => '0',
			// 	'updatedOn' => date('Y-m-d H:i:s'),
			// );
			// $this->affiliate_manager->editAffiliates($data, $affiliate_id);
			if ($success) {
				$username=$this->affiliatemodel->getUsernameById($affiliate_id);
				$this->syncAffCurrentToMDBWithLock($affiliate_id, $username, false);

				$message = lang('con.aff14');
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
			}

			redirect('affiliate_management/userInformation/' . $affiliate_id);
		}
	}

	/**
	 * Will load view for tag
	 *
	 * @param 	int
	 * @return	loaded page
	 */
	public function affiliateTag($affiliate_id) {

		if ( ! $this->permissions->checkPermissions('affiliate_tag')) {
			$this->error_access();
		} else {

			$data['affiliateId'] 	= $affiliate_id;
			$data['affiliate'] 		= $this->affiliate_manager->getAffiliateById($affiliate_id);
			$data['affiliate_tags'] 	= $this->affiliate_manager->getAffiliateTag($affiliate_id);
			$data['tags'] 			= $this->affiliate_manager->getActiveTags();
			$this->load->view('affiliate_management/affiliates/affiliate_tag', $data);
		}
	}

	/**
	 * Validates and verifies inputs
	 * of the end user and edit a tag
	 *
	 * @param 	int
	 * @return	redirect page
	 */
	public function postEditTag($affiliate_id) {

		$this->form_validation->set_rules('tags', 'Tag', 'trim|required|xss_clean');

		if ($this->form_validation->run() == false) {
			$message = lang('con.aff15');
			$this->alertMessage(2, $message);
			redirect('affiliate_management/aff_list');
		} else {

			$tags = $this->input->post('tags');
            $isTagDuplicate = $this->affiliate_manager->isAffiliateTagDuplicate($affiliate_id, $tags);

            if(!$isTagDuplicate){
                $user_id = $this->authentication->getUserId();
                $today = date("Y-m-d H:i:s");

                $this->affiliate_manager->insertAffiliateTag(array(
                    'affiliateId' => $affiliate_id,
                    'taggerId' => $user_id,
                    'tagId' => $tags,
                    'status' => 1,
                    'createdOn' => $today,
                    'updatedOn' => $today,
                ));

                $this->report_functions->recordAction(array(
                    'username' => $this->authentication->getUsername(),
                    'management' => 'Affiliate Management',
                    'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
                    'action' => 'Add Tag for Affiliate',
                    'description' => "User " . $this->authentication->getUsername() . " added Tag for Affiliate",
                    'logDate' => $today,
                    'status' => '0',
                ));

                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('con.aff16'));
            }else{
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('con.aff37'));
            }

			redirect('affiliate_management/aff_list');
		}
	}

	/**
	 * Remove affiliate tag
	 *
	 * @param 	int
	 * @return	redirect page
	 */
	public function removeTag($tagId) {
		$this->affiliate_manager->deleteAffiliateTagByAffiliateTagId($tagId);
		$message = lang('con.aff47');
		$this->alertMessage(1, $message);
		redirect('affiliate_management/aff_list');
	}

	public function aff_list() {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			//echo "<pre>";print_r($_GET);exit;
			$this->load->model(array('affiliatemodel'));

			if (!$this->permissions->checkPermissions('export_affiliate_list')) {
				$data['export_report_permission'] = FALSE;
			} else {
				$data['export_report_permission'] = TRUE;
			}
			//echo "<pre>";print_r($_GET['by_status']);exit;
			$data['conditions'] = $this->safeLoadParams(array(
				'start_date' => isset($_GET['start_date']) ? $_GET['start_date'] : $this->utils->getTodayForMysql() . ' 00:00:00',
				'end_date' => isset($_GET['end_date']) ? $_GET['end_date'] : $this->utils->getTodayForMysql() . ' 23:59:59',
				'by_username' => '',
				'by_code' => '',
				'by_firstname' => '',
				'by_lastname' => '',
				'by_email' => '',
				'by_status' => isset($_GET['by_status']) ? $_GET['by_status'] : '',
				'by_parent_id' => '',
                'tag_id' => [],
				'show_with_parent' => '',
				'search_reg_date' => false,
				'domain' => '',
			));

			$data['aff_parent_list'] = $this->affiliatemodel->getParentAffKV();
			$data['flag_list'] = array(
				'' => '------' . lang('N/A') . '------',
				Affiliatemodel::DB_TRUE => lang('Paid'),
				Affiliatemodel::DB_FALSE => lang('Unpaid'),
			);
            $data['tags'] = $this->affiliatemodel->getActiveTagsKV();
			//echo "<pre>";print_r($data);exit;
			$this->loadTemplate(lang('Affiliate Management'), '', '', 'affiliate');
			$this->template->write_view('main_content', 'affiliate_management/view_affiliate_list', $data);
			$this->template->render();
		}
	}

	public function login_as_aff($affId) {
		if (!$this->permissions->checkPermissions('login_as_aff')) {
			$this->error_access();
		} else {
			// $this->load->library(array('authentication'));

			$adminUserId = $this->authentication->getUserId();

			$token = $this->getAdminToken($adminUserId);
			if ($token) {
				$url=$this->utils->getSystemUrl('aff') . '/affiliate/login/' . $token . '/' . $affId . '/' . $this->session->userdata('login_lan');
				$this->appendActiveDBToUrl($url);
				$this->utils->debug_log('login_as_aff url', $url);
				redirect($url);

			} else {
				$this->error_access();

			}
		}
	}

	public function log_unlock_trackingcode() {
		$this->saveAction('Unlock Tracking Code', "User " . $this->authentication->getUsername() . " has unlock tracking code");
	}
}