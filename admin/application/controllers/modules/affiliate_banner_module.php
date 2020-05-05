<?php

/**
 * General behaviors include:
 * * Get Banner Details
 * * Get Banner
 * * Displays banners
 * * Searches banner
 * * Add/Edit/Delete banner
 * * Activate/Deactivate banner
 * * Upload banner image
 *
 * @see Redirect redirect to affiliate tag page
 *
 * @category Affiliate Modules
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait affiliate_banner_module {

	/**
	 * overview : view affiliate banner
	 */
	public function viewAffiliateBanner() {
		if (!$this->permissions->checkPermissions('banner_settings')) {
			$this->error_access();
		} else {
			redirect('affiliate_management/bannerSearchPage');
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			$number_banner_list = '';
			$sort_by = '';
			$in = '';

			if ($this->session->userdata('number_banner_list')) {
				$number_banner_list = $this->session->userdata('number_banner_list');
			} else {
				$number_banner_list = 5;
			}

			if ($this->session->userdata('banner_sort_by')) {
				$sort_by = $this->session->userdata('banner_sort_by');
			} else {
				$sort_by = 'b.createdOn';
			}

			if ($this->session->userdata('banner_in')) {
				$in = $this->session->userdata('banner_in');
			} else {
				$in = 'desc';
			}

			$sort = array(
				'sortby' => $sort_by,
				'in' => $in,
			);

			$data['count_all'] = count($this->affiliate_manager->getAllBanner(null, null, $sort));
			$config['base_url'] = "javascript:get_banner_pages(";
			$config['total_rows'] = $data['count_all'];
			$config['per_page'] = $number_banner_list;
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
			$data['banner'] = $this->affiliate_manager->getAllBanner(null, null, $sort);

			$this->template->write_view('main_content', 'affiliate_management/banner/view_banner', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : pagination page of banner list
	 *
	 * @param string $segment
	 */
	public function getBannerPages($segment = '') {
		$number_banner_list = '';
		$sort_by = '';
		$in = '';

		if ($this->session->userdata('number_banner_list')) {
			$number_banner_list = $this->session->userdata('number_banner_list');
		} else {
			$number_banner_list = 5;
		}

		if ($this->session->userdata('banner_sort_by')) {
			$sort_by = $this->session->userdata('banner_sort_by');
		} else {
			$sort_by = 'b.createdOn';
		}

		if ($this->session->userdata('banner_in')) {
			$in = $this->session->userdata('banner_in');
		} else {
			$in = 'desc';
		}

		$sort = array(
			'sortby' => $sort_by,
			'in' => $in,
		);

		$data['count_all'] = count($this->affiliate_manager->getAllBanner(null, null, $sort));
		$config['base_url'] = "javascript:get_banner_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = $number_banner_list;
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
		$data['banner'] = $this->affiliate_manager->getAllBanner(null, $segment, $sort);

		$this->load->view('affiliate_management/banner/ajax_view_banner', $data);
	}

	/**
	 * overview : sort banner
	 *
	 * @return	redirect
	 */
	public function bannerSortPage() {
		$sort_by = $this->input->post('sort_by');
		$this->session->set_userdata('banner_sort_by', $sort_by);

		$in = $this->input->post('in');
		$this->session->set_userdata('banner_in', $in);

		$number_banner_list = $this->input->post('number_banner_list');
		$this->session->set_userdata('number_banner_list', $number_banner_list);

		redirect('affiliate_management/viewAffiliateBanner');
	}

	/**
	 * overview : search banner
	 *
	 * @return	void
	 */
	public function bannerSearchPage() {
		if (!$this->permissions->checkPermissions('banner_settings')) {
			$this->error_access();
		} else {

			$search = array(
				"status" => $this->input->post('status'),
			);

			$data['input'] = $this->input->post();

			if (!empty($data['input']['enabled_date'])) {
					if ($this->input->post('start_date') && $this->input->post('end_date')) {
					$search['signup_range'] = "'" . $this->input->post('start_date') . "' AND '" . $this->input->post('end_date') . "'";
				} else {
					$search['signup_range'] = "'" . date("Y-m-d 00:00:00") . "' AND '" . date("Y-m-d 23:59:59") . "'";
					$data['input']['start_date'] = date("Y-m-d 00:00:00");
					$data['input']['end_date'] = date("Y-m-d 23:59:59");
				}
			}


			$number_banner_list = '';

			if ($this->session->userdata('number_banner_list')) {
				$number_banner_list = $this->session->userdata('number_banner_list');
			} else {
				$number_banner_list = 5;
			}

			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');
			$this->load->model(['affiliatemodel']);

			$rows=$this->affiliatemodel->getSearchBanner($search);

			$data['count_all'] = count($rows);
			// $config['base_url'] = "javascript:get_banner_pages(";
			// $config['total_rows'] = $data['count_all'];
			// $config['per_page'] = $number_banner_list;
			// $config['num_links'] = '1';

			// $config['first_tag_open'] = '<li>';
			// $config['last_tag_open'] = '<li>';
			// $config['next_tag_open'] = '<li>';
			// $config['prev_tag_open'] = '<li>';
			// $config['num_tag_open'] = '<li>';

			// $config['first_tag_close'] = '</li>';
			// $config['last_tag_close'] = '</li>';
			// $config['next_tag_close'] = '</li>';
			// $config['prev_tag_close'] = '</li>';
			// $config['num_tag_close'] = '</li>';

			// $config['cur_tag_open'] = "<li><span><b>";
			// $config['cur_tag_close'] = "</b></span></li>";

			// $this->pagination->initialize($config);

			// $data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
			$data['banner'] = $rows;

			$this->template->add_js('resources/js/bootstrap-filestyle.min.js');
			$this->template->write_view('main_content', 'affiliate_management/banner/view_banner', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : validation through ajax
	 * @return json
	 */
	public function validateThruAjax() {

		if ($this->input->post('bannerName')) {
			$this->form_validation->set_rules('bannerName', 'Banner Name', 'trim|required|is_unique[banner.bannerName]');
		}

		if ($this->form_validation->run() == false) {
			$arr = array('status' => 'error', 'msg' => validation_errors());
			echo json_encode($arr);
		} else {

			$arr = array('status' => 'success', 'msg' => "");
			echo json_encode($arr);
		}
	}

	/**
	 * overview : activate banner
	 *
	 * @return	void
	 */
	public function activateBanner($banner_id, $banner_name) {
		$data = array(
			'status' => '0',
			'updatedOn' => date('Y-m-d H:i:s'),
		);
		$this->affiliate_manager->editBanner($data, $banner_id);

		$message = lang('con.aff25') . ": " . str_replace("%20", " ", $banner_name);
		$this->alertMessage(1, $message);

		$this->saveAction('Affiliate Banner', "User " . $this->authentication->getUsername() . " has successfully activated " . ucfirst($banner_name) . ".");
		redirect('affiliate_management/viewAffiliateBanner', 'refresh');
	}

	/**
	 * overview : deactivate banner
	 *
	 * @return	void
	 */
	public function deactivateBanner($banner_id, $banner_name) {
		$data = array(
			'status' => '1',
			'updatedOn' => date('Y-m-d H:i:s'),
		);
		$this->affiliate_manager->editBanner($data, $banner_id);

		$message = lang('con.aff26') . ": " . str_replace("%20", " ", $banner_name);
		$this->alertMessage(1, $message);

		$this->saveAction('Affiliate Banner', "User " . $this->authentication->getUsername() . " has successfully deactivated " . ucfirst($banner_name) . ".");
		redirect('affiliate_management/viewAffiliateBanner', 'refresh');
	}

	/**
	 * overview : action Banner
	 *
	 * @return	void
	 */
	public function actionBanner() {
		$this->form_validation->set_rules('bannerName', 'Banner Name', 'trim|required|xss_clean|htmlspecialchars');
		$this->form_validation->set_rules('bannerLanguage', 'Language', 'trim|required|xss_clean');
		$this->form_validation->set_rules('banner_url', 'bannerURL', 'trim|required|xss_clean');

		$imgName = isset($_FILES['txtImage']['name']) ? $_FILES['txtImage']['name'] : null;

		$wrongExt = false;
		if (!empty($imgName)) {
			// get file extension
			$ext = explode('.', $imgName);
			$ext = $ext[count($ext) - 1];
			//$ext = pathinfo($path, PATHINFO_EXTENSION);

			// store array of allowed image extensions
			$ext_allowed = array("jpg", "jpeg", "gif", "png");
			$wrongExt = in_array($ext, $ext_allowed) <= 0;
			// } else {
			// $imgName = pathinfo($this->input->post('banner_url'), PATHINFO_FILENAME);
		}
		//if (strcasecmp($ext, 'jpg') != 0 && strcasecmp($ext, 'jpeg') != 0 && strcasecmp($ext, 'gif') != 0 && strcasecmp($ext, 'png') != 0) {
		if ($wrongExt) {
			$message = lang('con.aff46');
			$this->alertMessage(2, $message);
		} else if ($this->form_validation->run() == false) {
			$message = lang('con.aff27');
			$this->alertMessage(2, $message);
		} else {
			$this->load->model(['affiliatemodel']);
			$bannerName = $this->input->post('bannerName');
			$bannerId = $this->input->post('bannerId');
			$lang = $this->input->post('bannerLanguage');

			$isBannerExist = $this->affiliatemodel->existsBannerByName($bannerName);

			if ($isBannerExist && !$this->input->post('bannerId')) {
				$message = lang('con.aff28');
				$this->alertMessage(2, $message);
			} else {
				$userId=$this->authentication->getUserId();
				if (empty($bannerId)) {

					$data = array(
						'bannerName' => $bannerName,
						// 'bannerURL' => $bannerURL,
						'language' => $lang,
						// 'width' => $width,
						// 'height' => $height,
						'createdOn' => $this->utils->getNowForMysql(),
						'updatedOn' => $this->utils->getNowForMysql(),
					);

					$bannerId=$this->affiliatemodel->addBanner($data);
					$message = lang('con.aff29') . " <b>" . $bannerName . "</b> " . lang('con.aff31');
					$this->saveAction('Affiliate Banner', "User " . $this->authentication->getUsername() . " has successfully added " . ucfirst($bannerName) . ".");
				}

				if (!empty($imgName)) {
					$result = $this->upload($bannerId);
				}else{
					$result=null;
				}

				if (isset($result['error'])) {
					$message = $result['error'];
					//$message = lang('con.aff46');
					$this->alertMessage(2, $message);
				} else {
					//$bannerURL = "resources/images/banner/" . str_replace(" ", "_", $bannerName) . $result['file_ext'];
					// if (!empty($imgName)) {
					// 	$bannerURL = "resources/images/banner/" . $imgName;
					// } else {
					// 	$bannerURL = $this->input->post('banner_url');
					// }
					// $width = $result['image_width'];
					// $height = $result['image_height'];
					// $today = date("Y-m-d H:i:s");

					$data = array(
						'bannerName' => $bannerName,
						'language' => $lang,
						// 'bannerURL' => $result['filename'],
						// 'width' => $width,
						// 'height' => $height,
						'updatedOn' => $this->utils->getNowForMysql(),
						'last_edit_user' => $userId,
						// 'file_ext' => $result['file_ext'],
					);

					if($result){
						$data['width']=$result['image_width'];
						$data['height']=$result['image_height'];
						$data['bannerURL']=$result['filename'];
						$data['file_ext']=$result['file_ext'];
					}

					$this->affiliatemodel->editBanner($data, $bannerId);
					$message = lang('con.aff29') . " <b>" . $bannerName . "</b> " . lang('con.aff30');
					$this->saveAction('Affiliate Banner', "User " . $this->authentication->getUsername() . " has successfully edited " . ucfirst($bannerName) . ".");

					$this->alertMessage(1, $message);
				}
			}
		}

		redirect('affiliate_management/viewAffiliateBanner');
	}

	/**
	 * overview : get banner details
	 *
	 * @param $banner_id
	 */
	public function getBannerDetails($banner_id) {
		$this->load->model(['affiliatemodel']);
		$this->returnJsonResult($this->affiliatemodel->getBannerDetails($banner_id));
	}

	/**
	 * overview : upload photo to resources
	 *
	 * @return	void
	 */
	public function upload($bannerId) {
		if ($_FILES['txtImage']['name'] != "") {
			//upload to upload dir use id
			// $path = realpath(APPPATH . "../public/resources/images/banner/");
			$path=realpath($this->getUploadPath()).'/banner';
			//add right /
			$path=rtrim($path, '/');
			$this->utils->addSuffixOnMDB($path);

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size'] = $this->utils->getMaxUploadSizeByte();
			$config['remove_spaces'] = true;
			$config['overwrite'] = true;
			$config['file_name'] = $bannerId;
			$config['max_width'] = '';
			$config['max_height'] = '';
			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			if (!$this->upload->do_upload('txtImage')) {
				$error = array('error' => $this->upload->display_errors());
				return $error;
			} else {
				$image = $this->upload->data();

				// if ($image['file_name']) {
				// 	$data['file1'] = $image['file_name'];
				// }

				// $product_image = $data['file1'];
				// $config['image_library'] = 'gd2';
				// $config['source_image'] = '/images/upload/' . $data['file1'];
				// $config['new_image'] = '/images/upload/new/';
				// $config['maintain_ratio'] = FALSE;
				// $config['overwrite'] = true;
				// $this->load->library('image_lib', $config); //load library
				// $this->image_lib->clear();
				// $this->image_lib->initialize($config);
				// $this->image_lib->resize(); //do whatever specified in config

				//rename file
				// rename($path.'/'.$image['file_name'], $path.'/'. $bannerId);

				$result = array(
					'file_ext' => $image['file_ext'],
					'image_width' => $image['image_width'],
					'image_height' => $image['image_height'],
					'filename'=> $image['orig_name'],
				);

				return $result;
			}
		}
	}

	/**
	 * post affiliate add banner
	 *
	 * @return	void
	 */
	// public function verifyAddBanner() {
	// 	$this->form_validation->set_rules('banner_name', 'Banner Name', 'trim|required');
	// 	$this->form_validation->set_rules('category', 'Category', 'trim|required|callback_checkCategory');
	// 	$this->form_validation->set_rules('language', 'Language', 'trim|required');
	// 	$this->form_validation->set_rules('currency', 'Currency', 'trim|required');

	// 	if ($this->form_validation->run() == FALSE) {
	// 		$this->addBanner();
	// 	} else {
	// 		$this->load->model(['affiliatemodel']);

	// 		$banner_name = $this->input->post('banner_name');
	// 		$category = $this->input->post('category');
	// 		$width = $this->input->post('width');
	// 		$height = $this->input->post('height');
	// 		$banner_url = $this->input->post('banner_url');
	// 		$language = $this->input->post('language');
	// 		$currency = $this->input->post('currency');

	// 		$data = array(
	// 			'bannerName' => $banner_name,
	// 			'category' => ($category == "Others") ? $category . " (" . $width . "x" . $height . ")" : $category,
	// 			'bannerURL' => $banner_url,
	// 			'language' => $language,
	// 			'currency' => $currency,
	// 			'width' => $width,
	// 			'height' => $height,
	// 		);

	// 		$this->affiliatemodel->addBanner($data);

	// 		$this->upload();

	// 		$this->saveAction('Add Banner', $this->authentication->getUsername() . ' created a new banner');
	// 		$message = lang('con.aff32');
	// 		$this->alertMessage(1, $message); //will set and send message to the user
	// 		redirect('affiliate_management/bannerSettings', 'refresh'); //redirect to viewRoles
	// 	}
	// }

	/**
	 * overview : delete banner settings page
	 *
	 * @param	int
	 * @return	void
	 */
    private function deleteBannerById($banner_id){
        $row=$this->affiliate->getBannerDetails($banner_id)[0];
        if($this->affiliate->deleteBanner($banner_id)){
        	$path=realpath($this->getUploadPath()).'/banner';
        	$this->utils->addSuffixOnMDB($path);
            $image=$path.'/'.$banner_id.$row['file_ext'];
            if(file_exists($image)){
                unlink($image);
            }
        }
    }
	public function deleteBanner($banner_id) {
		if (!$this->permissions->checkPermissions('banner_settings')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');
			//$this->load->model(['affiliate']);
			$data = array(
				'last_edit_user' => $this->authentication->getUserId(),
				'updatedOn' => $this->utils->getNowForMysql(),
				'deleted_at' => $this->utils->getNowForMysql()
			);
			$this->load->model(['affiliatemodel']);
			$this->affiliatemodel->softDeleteBanner($data,$banner_id);


			$this->saveAction('Delete Banner', $this->authentication->getUsername() . ' deleted banner ' . $banner_id);
			$message = lang('con.aff33');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect('affiliate_management/viewAffiliateBanner', 'refresh'); //redirect to viewRoles
		}
	}

	/**
	 * overview : delete selected banner
	 *
	 * @param 	int
	 * @return	redirect
	 */
	public function deleteSelectedBanner() {
		$banner = $this->input->post('banner');
		if ($banner != '') {
			foreach ($banner as $banner_id) {
				$this->deleteBannerById($banner_id);
			}
			$message = lang('con.aff34');
			$this->alertMessage(1, $message); //will set and send message to the user
			$this->saveAction('Delete Affiliate Banner', "User " . $this->authentication->getUsername() . " has successfully deleted banners.");
			redirect('affiliate_management/viewAffiliateBanner');
		} else {
			$message = lang('con.aff35');
			$this->alertMessage(2, $message);
			redirect('affiliate_management/viewAffiliateBanner');
		}
	}

	/**
	 * overview : get banner
	 *
	 * @param $id
	 */
	public function get_banner($id){
		// $this->load->helper('download');
		$this->load->model(['affiliatemodel']);
		$localPath=$this->affiliatemodel->getBannerLocalPathById($id);
		$this->utils->debug_log('get banner: '.$id, $localPath);

		$data = file_get_contents($localPath);
		$ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));

		// return force_download(basename($localPath), $data);
		// header('Content-Type: image/'.$ext);
		$this->output->set_content_type('image/'.$ext)->set_output($data);
	}

	/**
	 * overview : download banner
	 *
	 * @param $id
	 */
	public function download_banner($id){
		$this->load->helper(array('url', 'form', 'download'));

		// $this->load->helper('download');
		$this->load->model(['affiliatemodel']);
		$localPath=$this->affiliatemodel->getBannerLocalPathById($id);

		$data = file_get_contents($localPath);
		$ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
		$name = $id.'.'.$ext;

		force_download($name, $data);
	}

	/* ****** End of Affiliate Banner ****** */

}