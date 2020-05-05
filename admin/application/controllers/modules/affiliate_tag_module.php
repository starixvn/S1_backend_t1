<?php

/**
 * General behaviors include:
 * * Displays Affiliate Tag
 * * Sorts Tag
 * * Searches Tag
 * * Add/Edit/Delete Tag
 *
 * @see Redirect redirect to affiliate tag page
 *
 * @category Affiliate Module
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait affiliate_tag_module {
	/**
	 * overview : view affiliate tag
	 */
	public function viewAffiliateTag() {
		if (!$this->permissions->checkPermissions('affiliate_tag')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			$sort = "tagId";

			$data['count_all'] = count($this->affiliate_manager->getAllTags($sort, null, null) ?: []);
			$config['base_url'] = "javascript:get_tag_pages(";
			$config['total_rows'] = $data['count_all'];
			$config['per_page'] = 5;
			$config['num_links'] = 2;

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
			$data['tags'] = $this->affiliate_manager->getAllTags($sort, null, null);

			$this->template->write_view('main_content', 'affiliate_management/tag/view_tags', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : get tag pages
	 *
	 * @param string $segment
	 */
	public function get_tag_pages($segment = "") {
		$sort = "tagId";

		$data['count_all'] = count($this->affiliate_manager->getAllTags($sort, null, null));
		$config['base_url'] = "javascript:get_tag_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 5;
		$config['num_links'] = 2;

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
		$data['tags'] = $this->affiliate_manager->getAllTags($sort, null, $segment);

		$this->load->view('affiliate_management/tag/ajax_view_tags', $data);
	}

	/**
	 * overview : sort tag
	 *
	 * @param $sort
	 */
	public function sortTag($sort) {
		$data['count_all'] = count($this->affiliate_manager->getAllTags($sort, null, null));
		$config['base_url'] = "javascript:get_tag_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 5;
		$config['num_links'] = 2;

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
		$data['tags'] = $this->affiliate_manager->getAllTags($sort, null, null);

		$this->load->view('affiliate_management/tag/ajax_view_tags', $data);
	}

	/**
	 * overview : search tag
	 * @param string $search
	 */
	public function searchTag($search = '') {
		$data['count_all'] = count($this->affiliate_manager->getSearchTag($search, null, null));
		$config['base_url'] = "javascript:get_tag_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 5;
		$config['num_links'] = 2;

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
		$data['tags'] = $this->affiliate_manager->getSearchTag($search, null, null);

		$this->load->view('affiliate_management/tag/ajax_view_tags', $data);

	}

	/**
	 * overview : save tag
	 * @return redirect
	 */
	public function actionTag() {
		$this->form_validation->set_rules('tagName', 'Bank Name', 'trim|required|xss_clean|htmlspecialchars');
		$this->form_validation->set_rules('tagDescription', 'Account Number', 'trim|required|xss_clean|htmlspecialchars');

		if ($this->form_validation->run() == false) {
			$message = lang('con.aff36');
			$this->alertMessage(2, $message);
		} else {
			$isTagExist = $this->affiliate_manager->getAffiliateTagByName($this->input->post('tagName'));

			if ($isTagExist && !$this->input->post('tagId')) {
				$message = lang('con.aff37');
				$this->alertMessage(2, $message);
			} else {
				$tagName = $this->input->post('tagName');
				$tagDescription = $this->input->post('tagDescription');
				$tagId = $this->input->post('tagId');
				$today = date("Y-m-d H:i:s");

				if ($tagId) {
					$data = array(
						'tagName' => ucfirst($tagName),
						'tagDescription' => $tagDescription,
						'updatedOn' => $today,
						'status' => 0,
					);

					$this->affiliate_manager->editTag($data, $tagId);
					$message = lang('con.aff38') . " <b>" . $tagName . "</b> " . lang('con.aff39');
					$this->saveAction('Affiliate Tag', "User " . $this->authentication->getUsername() . " has successfully edited " . ucfirst($tagName) . ".");
				} else {
					$data = array(
						'tagName' => ucfirst($tagName),
						'tagDescription' => $tagDescription,
						'createBy' => $this->session->userdata('user_id'),
						'createdOn' => $today,
						'updatedOn' => $today,
						'status' => 0,
					);

					$this->affiliate_manager->insertTag($data);
					$message = lang('con.aff38') . " <b>" . $tagName . "</b> " . lang('con.aff40');
					$this->saveAction('Affiliate Tag', "User " . $this->authentication->getUsername() . " has successfully added " . ucfirst($tagName) . ".");
				}

				$this->alertMessage(1, $message);
			}
		}

		redirect('affiliate_management/viewAffiliateTag');
	}

	/**
	 * overview : get tag details
	 *
	 * @param $tag_id
	 */
	public function getTagDetails($tag_id) {
		echo json_encode($this->affiliate_manager->getTagDetails($tag_id));
	}

	/**
	 * overview : delete tag
	 *
	 * @param $tag_id
	 */
	public function deleteTag($tag_id) {
		$this->affiliate_manager->deleteAffiliateTagByTagId($tag_id);
		$this->affiliate_manager->deleteTag($tag_id);

		$message = lang('con.aff41');
		$this->alertMessage(1, $message);
		$this->saveAction('Delete Affiliate Tag', "User " . $this->authentication->getUsername() . " has successfully deleted tag.");
		redirect('affiliate_management/viewAffiliateTag');
	}

	/**
	 * Delete selected tags
	 *
	 * @return	redirect
	 */
	public function deleteSelectedTag() {
		$tag = $this->input->post('tag');
		$today = date("Y-m-d H:i:s");
		$tags = '';

		if ($tag != '') {
			foreach ($tag as $tag_id) {
				$this->affiliate_manager->deleteAffiliateTagByTagId($tag_id);
				$this->affiliate_manager->deleteTag($tag_id);
			}

			$message = lang('con.aff42');
			$this->alertMessage(1, $message); //will set and send message to the user
			$this->saveAction('Delete Affiliate Tags', "User " . $this->authentication->getUsername() . " has successfully deleted tags.");
			redirect('affiliate_management/viewAffiliateTag');
		} else {
			$message = lang('con.aff43');
			$this->alertMessage(2, $message);
			redirect('affiliate_management/viewAffiliateTag');
		}
	}

	/* ****** End of Affiliate Tag ****** */

}