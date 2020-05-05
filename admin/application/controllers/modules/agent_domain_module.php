<?php

trait agent_domain_module {

	public function agent_domain_list() {
		if ( ! $this->permissions->checkPermissions('agent_domain_list')) {
			return $this->error_access();
		}

			$this->load->model(['agency_model']);

			if (($this->session->userdata('sidebar_status') == NULL)) {
				$this->session->set_userdata(array('sidebar_status' => 'active'));
			}

			if (($this->session->userdata('well_crumbs') == NULL)) {
				$this->session->set_userdata(array('well_crumbs' => 'active', 'system_crumb' => 'active'));
			}

			$data['domain_list'] = $this->agency_model->get_domain_list();

			$this->load_template('Domain List', '', '', 'agency');
			$this->template->write_view('main_content', 'agency_management/domains/view_domain', $data);
			$this->template->render();

	}

	public function new_domain() {
		if ( ! $this->permissions->checkPermissions('edit_agent_domain')) {
			return $this->error_access();
		}

		$this->form_validation->set_rules('domain', lang('Domain'), 'trim|valid_domain|required|xss_clean|is_unique[agency_domain.domain_name]');
		$this->form_validation->set_rules('notes', lang('Notes'), 'trim|xss_clean');
		$this->form_validation->set_message('valid_domain', lang('validation.badDomain'));


		if ($this->form_validation->run() == false) {
			$this->agent_domain_list();
		} else {
			// $this->load->library('ip_manager');
			$this->load->model(['agency_model']);
			$adminUserId=$this->authentication->getUserId();

			$show_to_agent_type = $this->input->post('show_to_agent_type');
			// $current_time = date('Y-m-d H:i:s');
			$domainName = rtrim($this->input->post('domain'), '/');

			$notes = $this->input->post('notes');

			$this->agency_model->startTrans();

			$domain_id = $this->agency_model->add_domain($show_to_agent_type, $domainName, $notes, $adminUserId);
			if(!empty($domain_id)){

				if ($show_to_agent_type == Agency_model::SHOW_TO_AGENT_TYPE_BATCH) {
					$text = file_get_contents($_FILES['usernames']['tmp_name']);
					$usernames = explode("\n", $text);
					$usernames = explode(",", $text);
					$usernames = array_filter($usernames);
					$usernames = array_unique($usernames);
					if(!empty($usernames)){
						$agent_domain_list = array_filter(array_map(function($username) use ($domain_id) {
							$agent = $this->agency_model->get_agent_by_name($username);
							return $agent ? array(
								'agent_id' => $agent['agent_id'],
								'agency_domain_id' => $domain_id,
							) : NULL;
						}, $usernames));
						if (isset($agent_domain_list) && ! empty($agent_domain_list)) {
							$this->agency_model->insert_agent_domain_permission($agent_domain_list);
						}
					}
				}
			}

			$success=$this->agency_model->endTransWithSucc();
			if($success){

				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('con.i11'));

			}else{

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));

			}

			$this->saveAction('Add Domain', 'User ' . $this->authentication->getUsername() . ' has added new domain' . $domainName);
			redirect('agency_management/agent_domain_list');
		}

	}

	public function edit_domain($domain_id) {
		if ( ! $this->permissions->checkPermissions('edit_agent_domain')) {
			return $this->error_access();
		}

		// $this->load->library('ip_manager');
		// $data['domain'] = $this->ip_manager->getAllDomain();
		$data['domain_list'] = $this->agency_model->get_domain_list();
		$data['domain_id'] = $domain_id;
		$data['edit_domain'] = $this->db->get_where('agency_domain', array('id' => $domain_id))->row_array();
		$data['agent_domain_count'] = $this->db->where('agency_domain_id', $domain_id)->count_all_results('agency_domain_permissions');
		$this->load_template('Domain List', '', '', 'agency');
		$this->template->write_view('main_content', 'agency_management/domains/edit_domain', $data);
		$this->template->render();

	}

	public function verifyEditDomain($domain_id) {
		if ( ! $this->permissions->checkPermissions('edit_agent_domain')) {
			return $this->error_access();
		}

		if(empty($domain_id)){
			return $this->error_access();
		}

			// $this->form_validation->set_rules('domain', 'Domain', 'trim|required|xss_clean|is_unique[domain.domainName]');
			$this->form_validation->set_rules('domain', 'Domain', 'trim|required|xss_clean]');
			$this->form_validation->set_rules('notes', 'Notes', 'trim|xss_clean');
			if ($this->form_validation->run() == false) {
				$this->edit_domain($domain_id);
			} else {
				$this->editDomainProcess($domain_id);
			}

	}

	protected function editDomainProcess($domain_id) {

		if ( ! $this->permissions->checkPermissions('edit_agent_domain')) {
			return $this->error_access();
		}

		if(empty($domain_id)){
			return $this->error_access();
		}

		// $this->load->library('ip_manager');
		$this->load->model(['agency_model']);
		$adminUserId=$this->authentication->getUserId();

		$this->agency_model->startTrans();

			// $current_time = date('Y-m-d H:i:s');
			$domainName = rtrim($this->input->post('domain'), "/");
			$notes = $this->input->post('notes');
			// $this->ip_manager->editDomain(array(
			// 	'show_to_affiliate' => $show_to_affiliate,
			// 	'domainName' => $domainName,
			// 	'notes' => $notes,
			// 	'updatedOn' => $current_time,
			// ), $domain_id);

			$show_to_agent_type = $this->input->post('show_to_agent_type');
			$this->agency_model->edit_domain($domain_id, $show_to_agent_type, $domainName, $notes, $adminUserId);

			$agent_domain_list=null;
			if ($show_to_agent_type == Agency_model::SHOW_TO_AGENT_TYPE_BATCH) {
				if(!empty($_FILES['usernames']['tmp_name'])){
					$text = file_get_contents($_FILES['usernames']['tmp_name']);
					if(!empty($text)){
						$usernames = explode("\n", $text);
						$usernames = explode(",", implode(",", $usernames));
						$usernames = array_filter($usernames);
						$usernames = array_unique($usernames);
						if(!empty($usernames)){
							$agent_domain_list = array_filter(array_map(function($username) use ($domain_id) {
								$username = trim($username);
								$agent = $this->agency_model->get_agent_by_name($username);
								return $agent ? array(
									'agent_id' => $agent['agent_id'],
									'agency_domain_id' => $domain_id,
								) : NULL;
							}, $usernames));
						}
					}
				}
				// if (isset($agent_domain_list) && ! empty($agent_domain_list)) {
				// 	$this->agency_model->edit_agent_domain_permission($domain_id, $agent_domain_list);
				// }
			}

			$this->agency_model->edit_agent_domain_permission($domain_id, $agent_domain_list);

			// if ($show_to_agent_type != Agency_model::SHOW_TO_AGENT_TYPE_BATCH) {
			// 	$this->db->delete('affiliate_domain', array('domainId' => $domain_id));
			// } else if (isset($affiliate_domain_list) && ! empty($affiliate_domain_list)) {
			// 	$this->db->delete('affiliate_domain', array('domainId' => $domain_id));
			// 	$this->db->insert_batch('affiliate_domain', $affiliate_domain_list);
			// }

		$success=$this->agency_model->endTransWithSucc();
		if($success){

			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('con.i12'));

		}else{

			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));

		}

		$this->saveAction('Edit Domain', 'User ' . $this->authentication->getUsername() . ' has edited domain ' . $domainName);
		redirect('agency_management/edit_domain/' . $domain_id);
	}

	public function activateDomain($domain_id, $domain_name) {
		if ( ! $this->permissions->checkPermissions('edit_agent_domain')) {
			return $this->error_access();
		}

		$this->load->model(['agency_model']);
		$adminUserId=$this->authentication->getUserId();
		// $this->load->library('ip_manager');
		$current_time = date('Y-m-d H:i:s');
		$domain_name_str = str_replace('%3A', ':', base64_decode($domain_name));
		// $this->ip_manager->editDomain(array(
		// 	'status' => '0',
		// 	'updatedOn' => $current_time,
		// ), $domain_id);

		$this->agency_model->activateDomain($domain_id, $adminUserId);

		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('con.i13') . ': ' . $domain_name_str);
		$this->saveAction('Activate Domain', 'User ' . $this->authentication->getUsername() . ' has activated domain ' . $domain_name_str);
		redirect('agency_management/agent_domain_list');

	}

	public function deactivateDomain($domain_id, $domain_name) {
		if ( ! $this->permissions->checkPermissions('edit_agent_domain')) {
			return $this->error_access();
		}

		$this->load->model(['agency_model']);
		$adminUserId=$this->authentication->getUserId();
		// $this->load->library('ip_manager');
		$current_time = date('Y-m-d H:i:s');
		$domain_name_str = str_replace('%3A', ':', base64_decode($domain_name));
		// $this->ip_manager->editDomain(array(
		// 	'status' => '1',
		// 	'updatedOn' => $current_time,
		// ), $domain_id);

		$this->agency_model->deactivateDomain($domain_id, $adminUserId);

		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('con.i14') . ': ' . $domain_name_str);
		$this->saveAction('Deactivate Domain', 'User ' . $this->authentication->getUsername() . ' has deactivated domain ' . $domain_name_str);
		redirect('agency_management/agent_domain_list');

	}

	public function delete_domain($domain_id) {
		if ( ! $this->permissions->checkPermissions('edit_agent_domain')) {
			return $this->error_access();
		}

		$this->load->model(['agency_model']);
		$adminUserId=$this->authentication->getUserId();
		$this->agency_model->deleteDomain($domain_id, $adminUserId);

		// $this->load->library('ip_manager');
		// $this->ip_manager->deleteDomain($domain_id);
		$this->saveAction('Delete Domain', 'User ' . $this->authentication->getUsername() . ' has deleted domain');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('con.i15'));
		redirect('agency_management/agent_domain_list');

	}

	public function domain_agents($domain_id) {
		if ( ! $this->permissions->checkPermissions('agent_domain_list')) {
			return $this->error_access();
		}

		// $this->db->select('affiliates.affiliateId, affiliates.username');
		// $this->db->join('affiliates','affiliates.affiliateId = affiliate_domain.affiliateId');
		// $query = $this->db->get_where('affiliate_domain', array('domainId' => $domainId));
		// $data['affiliates'] = array_map(function($affiliate) {
		// 	return '<a href="/affiliate_management/userInformation/'.$affiliate['affiliateId'].'" class="list-group-item">'.$affiliate['username'].'</a>';
		// }, $query->result_array());

		$agents=$this->agency_model->getBelongsAgentsById($domain_id);

		$data['agents'] =null;

		if(!empty($agents)){
			$data['agents'] = array_map(function($agent) {
				return '<a href="/agency_management/agent_information/'.$agent['agent_id'].'" class="list-group-item">'.$agent['agent_name'].'</a>';
			}, $agents);
		}

		$this->utils->debug_log('domain_agents', $data['agents'], $agents);

		$this->load_template('Domain Agency', '', '', 'agency');
		$this->template->write_view('main_content', 'agency_management/domains/domain_agents', $data);
		$this->template->render();
	}

}
