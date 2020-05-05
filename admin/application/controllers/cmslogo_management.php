<?php

/**
 * CMS Logo Management
 *
 * CMS Logo Management Controller for FHI OG System
 *
 * @author  ASRII
 *
 */

class Cmslogo_Management extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('date_helper', 'url'));
		$this->load->model('cms_model');
		$this->load->library(array('permissions', 'form_validation', 'template', 'pagination', 'cms_model', 'excel', 'report_functions'));

		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
	}

	/**
	 * save action to Logs
	 *
	 * @return  rendered Template
	 */
	private function saveAction($action, $description) {
		$today = date("Y-m-d H:i:s");

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'CMS Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => $action,
			'description' => $description,
			'logDate' => $today,
			'status' => 0,
		);

		$this->report_functions->recordAction($data);
	}

	/**
	 * set message for users
	 *
	 * @param   int
	 * @param   string
	 * @return  set session user data
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
	 * Loads template for view based on regions in
	 * config > template.php
	 *
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_css('resources/css/cms_management/style.css');

		$this->template->add_js('resources/js/cms_management/cmslogo_management.js');
		# JS
		// $this->template->add_js('resources/js/moment.min.js');
		// $this->template->add_js('resources/js/daterangepicker.js');
		$this->template->add_js('resources/js/chosen.jquery.min.js');
		$this->template->add_js('resources/js/summernote.min.js');
		// $this->template->add_js('resources/js/bootstrap-datetimepicker.js');
		$this->template->add_js('resources/js/jquery.dataTables.min.js');
		$this->template->add_js('resources/js/dataTables.responsive.min.js');

		# CSS
		// $this->template->add_css('resources/css/daterangepicker-bs3.css');
		$this->template->add_css('resources/css/font-awesome.min.css');
		$this->template->add_css('resources/css/chosen.min.css');
		$this->template->add_css('resources/css/summernote.css');
		$this->template->add_css('resources/css/jquery.dataTables.css');
		$this->template->add_css('resources/css/dataTables.responsive.css');

		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());
		$this->template->write_view('sidebar', 'cms_management/sidebar');
	}

	/**
	 * Shows Error message if user can't access the page
	 *
	 * @return  rendered Template
	 */
	private function error_access() {
		$this->loadTemplate('CMS Management', '', '', 'cms');

		$message = lang('con.cl01');
		$this->alertMessage(2, $message);

		$this->template->render();
	}

	/**
	 * Index Page of Report Management
	 *
	 *
	 * @return  void
	 */
	public function index() {
		redirect(BASEURL . 'cmslogo_management/viewLogoManager');
	}

	/**
	 * view logo settings page
	 *
	 * @return  void
	 */
	public function viewLogoManager() {
		// if(!$this->permissions->checkPermissions('cms_logo_settings')){
		//     $this->error_access();
		// } else {
		$sort = "category";
		$this->loadTemplate('CMS Management', '', '', 'cms');

		$data['count_all'] = count($this->cms_model->getAllCMSLogo($sort, null, null));
		$config['base_url'] = "javascript:get_logo_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '10';
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
		$data['logo'] = $this->cms_model->getAllCMSLogo($sort, null, null);

		$this->template->write_view('main_content', 'cms_management/logo/view_cmslogo_settings', $data);
		$this->template->render();
		// }
	}

	/**
	 * add/edit logo setting
	 *
	 * @return  array
	 */
	public function addLogoCms() {
		$this->form_validation->set_rules('category', 'Logo Category', 'trim|required|xss_clean');

		//var_dump($promoCategory);exit();
		if ($this->form_validation->run() == false) {
			$message = lang('con.cl02');
			$this->alertMessage(2, $message);
		} else {

			//var_dump($promoCategory);exit();
			$logoName = $this->input->post('userfile');
			$category = $this->input->post('category');
			$today = date("Y-m-d H:i:s");
			$logocmsId = $this->input->post('logocmsId');
			$cmsLogoURL = $this->input->post('logo_url');

			$fileType = substr($cmsLogoURL, strrpos($cmsLogoURL, '.') + 1);
			$path = realpath(APPPATH . '../public/resources/images/cmslogo');

			$path_image = $_FILES['userfile']['name'];
			$ext = pathinfo($path_image, PATHINFO_EXTENSION);

			if (strcasecmp($ext, 'jpg') != 0 && strcasecmp($ext, 'jpeg') != 0 && strcasecmp($ext, 'gif') != 0 && strcasecmp($ext, 'png') != 0) {
				$message = lang('con.aff46');
				$this->alertMessage(2, $message);
				redirect(BASEURL . 'cmslogo_management/viewLogoManager');
			} else if ($logocmsId != '') {
				$config = array(
					'allowed_types' => 'jpg|jpeg|gif|png',
					'upload_path' => $path,
					'max_size' => $this->utils->getMaxUploadSizeByte(),
					'overwrite' => true,
					'file_name' => $this->input->post('editLogoCms'),
				);

				$this->load->library('upload', $config);
				$this->upload->do_upload();

				$data = array(
					'category' => $category,
					'updatedBy' => $this->authentication->getUserId(),
					'updatedOn' => $today,
				);

				$this->cms_model->editLogoCms($data, $logocmsId);
				$message = lang('con.cl03') . " <b>" . $logoName . "</b> " . lang('con.cl04');

				$data = array(
					'username' => $this->authentication->getUsername(),
					'management' => 'Edit CMS Logo Setting Management',
					'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
					'action' => 'Edit CMS Promo Name: ' . $promocmsId,
					'description' => "User " . $this->authentication->getUsername() . " edit CMS logo id: " . $logocmsId,
					'logDate' => $today,
					'status' => 0,
				);

				$this->report_functions->recordAction($data);
			} else {

				//upload image
				$cmsLogoName = 'cmslogo-' . $this->cms_model->generateRandomCode();
				$config = array(
					'allowed_types' => 'jpg|jpeg|gif|png',
					'upload_path' => $path,
					'max_size' => $this->utils->getMaxUploadSizeByte(),
					'overwrite' => true,
					'file_name' => $cmsLogoName,
				);

				$this->load->library('upload', $config);
				$this->upload->do_upload();

				$data = array(
					'category' => $category,
					'createdBy' => $this->authentication->getUserId(),
					'createdOn' => $today,
					'logoName' => $cmsLogoName . '.' . $fileType,
					'status' => 'active',
				);

				$this->cms_model->addCmsLogo($data);
				$message = "<b>" . $logoName . "</b> " . lang('con.cl05');
			}

			$this->alertMessage(1, $message);
		}
		redirect(BASEURL . 'cmslogo_management/viewLogoManager');
	}

	/**
	 * search logo cms
	 *
	 *
	 * @return  redirect page
	 */
	public function searchLogoCms($search = '') {
		$data['count_all'] = count($this->cms_model->searchLogoCms($search, null, null));
		$config['base_url'] = "javascript:get_logo_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 10;
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
		$data['logo'] = $this->cms_model->searchLogoCms($search, null, null);

		//export report permission checking
		// if(!$this->permissions->checkPermissions('export_report')){
		//     $data['export_report_permission'] = FALSE;
		// } else {
		//     $data['export_report_permission'] = TRUE;
		// }
		$this->load->view('cmslogo_management/logo/ajax_view_cmslogo_list', $data);
	}

	/**
	 * sort promo cms
	 *
	 * @param   sort
	 * @return  void
	 */
	public function sortLogoCms($sort) {
		$data['count_all'] = count($this->cms_model->getAllCMSLogo($sort, null, null));
		$config['base_url'] = "javascript:get_logo_pages(";
		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = 10;
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
		$data['logo'] = $this->cms_model->getAllCMSLogo($sort, null, null);

		$this->load->view('cmslogo_management/logo/ajax_view_cmslogo_list', $data);
	}

	/**
	 * get cms logo details
	 *
	 * @param   int
	 * @return  redirect
	 */
	public function getLogoCmsDetails($logocmsId) {
		echo json_encode($this->cms_model->getLogoCmsDetails($logocmsId));
	}

	/**
	 * Delete selected cms promo
	 *
	 * @param   int
	 * @return  redirect
	 */
	public function deleteSelectedLogoCms() {
		$logocms = $this->input->post('logocms');
		$today = date("Y-m-d H:i:s");

		if ($logocms != '') {
			foreach ($logocms as $logocmsId) {
				$this->cms_model->deleteLogoCms($logocmsId);
				$this->cms_model->deleteLogoCmsItem($logocmsId);
			}

			$message = lang('con.cl06');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect(BASEURL . 'cmslogo_management/viewLogoManager');

			$data = array(
				'username' => $this->authentication->getUsername(),
				'management' => 'CMS Logo Setting Management',
				'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
				'action' => 'Delete cms logo id:' . $promocmsId,
				'description' => "User " . $this->authentication->getUsername() . " delete cms logo id: " . $logocmsId,
				'logDate' => date("Y-m-d H:i:s"),
				'status' => 0,
			);

			$this->report_functions->recordAction($data);
		} else {
			$message = lang('con.cl07');
			$this->alertMessage(2, $message);
			redirect(BASEURL . 'cmslogo_management/viewLogoManager');
		}
	}

	/**
	 * Delete cms promo
	 *
	 * @param   int
	 * @return  redirect
	 */
	public function deleteLogoCmsItem($logocmsId) {
		$this->cms_model->deleteLogoCms($logocmsId);

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'CMS Logo Setting Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Delete cms logo id:' . $logocmsId,
			'description' => "User " . $this->authentication->getUsername() . " delete vip cms promo id: " . $promocmsId,
			'logDate' => date("Y-m-d H:i:s"),
			'status' => 0,
		);

		$this->report_functions->recordAction($data);

		$message = lang('con.cl08');
		$this->alertMessage(1, $message);
		redirect(BASEURL . 'cmslogo_management/viewLogoManager');
	}

	/**
	 * activate logo cms
	 *
	 * @param   promocmsId
	 * @param   status
	 * @return  redirect
	 */
	public function activateLogoCms($logocmsId, $status) {
		$data = array(
			'updatedBy' => $this->authentication->getUserId(),
			'updatedOn' => date("Y-m-d H:i:s"),
			'status' => $status,
			'logoId' => $logocmsId,
		);

		$this->cms_model->activateLogoCms($data);

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'CMS Logo Setting Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Update status of vip group id:' . $vipsettingId . 'to status:' . $status,
			'description' => "User " . $this->authentication->getUsername() . " edit cms logo status to " . $status,
			'logDate' => date("Y-m-d H:i:s"),
			'status' => 0,
		);

		$this->report_functions->recordAction($data);

		redirect(BASEURL . 'cmslogo_management/viewLogoManager');
	}
}

/* End of file cms_management.php */
/* Location: ./application/controllers/cms_management.php */