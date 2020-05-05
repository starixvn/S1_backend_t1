<?php

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Class Cmsbanner_Management
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 * @property Cmsbanner_library $cmsbanner_library
 */
class Cmsbanner_Management extends BaseController {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('date_helper', 'url'));
		$this->load->library(array('permissions', 'form_validation', 'template', 'report_functions', 'cmsbanner_library'));

		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
	}

	/**
	 * Loads template for view based on regions in
	 * config > template.php
	 *
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_css('resources/css/cms_management/style.css');

		$this->template->add_js('resources/js/cms_management/cmsbanner_management.js');
		# JS
		// $this->template->add_js('resources/js/moment.min.js');
		// $this->template->add_js('resources/js/daterangepicker.js');
		$this->template->add_js('resources/js/chosen.jquery.min.js');
		$this->template->add_js('resources/js/summernote.min.js');
		// $this->template->add_js('resources/js/bootstrap-datetimepicker.js');
		$this->template->add_js('resources/js/datatables.min.js');
		$this->template->add_js('resources/js/dataTables.responsive.min.js');

		# CSS
		// $this->template->add_css('resources/css/daterangepicker-bs3.css');
		$this->template->add_css('resources/css/font-awesome.min.css');
		$this->template->add_css('resources/css/chosen.min.css');
		$this->template->add_css('resources/css/summernote.css');
        $this->template->add_css('resources/css/datatables.min.css');
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
	 * @return mixed
	 */
	private function error_access() {
		$this->loadTemplate('CMS Management', '', '', 'cms');
		$cmsUrl = $this->utils->activeCMSSidebar();
		$data['redirect'] = $cmsUrl;

		$message = lang('con.cb01');

		if($this->input->is_ajax_request()){
		    $result = [
		        'status' => 'error',
                'message' => $message,
            ];
		    return $this->returnJsonResult($result);
        }else{
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

            $this->template->write_view('main_content', 'error_page', $data);
            return $this->template->render();
        }
	}

	/**
	 * Index Page of Report Management
	 *
	 *
	 * @return  void
	 */
	public function index() {
		redirect(BASEURL . 'cmsbanner_management/viewBannerManager');
	}

	/**
	 * view banner settings page
	 *
	 * @return  void
	 */
	public function viewBannerManager() {
		if(!$this->permissions->checkPermissions('bannercms')){
		    return $this->error_access();
        }

		$this->loadTemplate('CMS Management', '', '', 'cms');

		$data['banner'] = $this->cmsbanner_library->getAllCMSBanner();

		$this->template->write_view('main_content', 'cms_management/banner/view_cmsbanner_settings', $data);
		$this->template->render();
	}

	/**
	 * add/edit banner setting
	 *
	 * @return  mixed
	 */
	public function addBannerCms() {
		$this->form_validation->set_rules('category', 'Banner Category', 'trim|required|xss_clean');

		//var_dump($promoCategory);exit();
		if ($this->form_validation->run() == false) {
			$message = lang('con.cb02');
			$this->alertMessage(2, $message);
            redirect(BASEURL . 'cmsbanner_management/viewBannerManager');

			return;
		}

        $bannercmsId = $this->input->post('bannercmsId');
        $category = $this->input->post('category');
        $language = $this->input->post('language');
        $bannerTitle = $this->input->post('title');
        $bannerSummary = $this->input->post('summary');
        $bannerLink = $this->input->post('link');
        $bannerLinkTarget = $this->input->post('link_target');

        $cmsBannerName = '';
        if(isset($_FILES['userfile']) && !empty($_FILES['userfile']['name'])){
            $cmsBannerName = $this->cmsbanner_library->uploadBannerImage('userfile');

            if(FALSE === $cmsBannerName){
                $message = $this->upload->display_errors('', '');
                $this->alertMessage(2, $message);
                redirect(BASEURL . 'cmsbanner_management/viewBannerManager');
                return NULL;
            }
        }

        if ($bannercmsId != '') {
            $data = array(
                'category' => $category,
                'language' => $language,
                'bannerName' => $cmsBannerName,
                'title' => $bannerTitle,
                'summary' => $bannerSummary,
                'link' => $bannerLink,
                'link_target' => $bannerLinkTarget
            );

            $result = $this->cmsbanner_library->editBannerCms($data, $bannercmsId);

            if($result){
                $status = 1;
                $message = lang('con.cb03') . " <b>" . $bannerTitle . "</b> " . lang('con.cb04');
            }else{
                $status = 2;
                $message = lang('save.failed');
            }

            $this->recordAction('Edit CMS Banner Setting Management', 'Edit CMS Banner Name: ' . $bannercmsId,  "User " . $this->authentication->getUsername() . " edit CMS banner id: " . $bannercmsId);
        } else {
            $data = array(
                'category' => $category,
                'language' => $language,
                'bannerName' => $cmsBannerName,
                'title' => $bannerTitle,
                'summary' => $bannerSummary,
                'link' => $bannerLink,
                'link_target' => $bannerLinkTarget,
                'status' => 'active',
            );

            $result = $this->cmsbanner_library->addCmsBanner($data);

            if($result){
                $status = 1;
                $message = lang('con.cb03') . " <b>" . $bannerTitle . "</b> " . lang('con.cb05');
            }else{
                $status = 2;
                $message = lang('save.failed');
            }

            $this->saveAction(lang('cms.banner2'), $this->authentication->getUsername() . lang('cms.banner1'));
        }

        $this->alertMessage($status, $message);
		redirect(BASEURL . 'cmsbanner_management/viewBannerManager');

		return null;
	}

	/**
	 * get cms banner details
	 *
	 * @param   int
	 * @return  mixed
	 */
	public function getBannerCmsDetails($bannercmsId) {
        if (!$this->permissions->checkPermissions('bannercms')) {
            $this->error_access();

            return null;
        }

        $data = $this->cmsbanner_library->getBannerCmsDetails($bannercmsId);

        $result = [
            'status' => 'success',
            'message' => NULL,
            'data' => $data
        ];

		return $this->returnJsonResult($result);
	}

	/**
	 * Delete selected cms promo
	 *
	 * @param   int
	 * @return  mixed
	 */
	public function deleteSelectedBannerCms() {
        if (!$this->permissions->checkPermissions('bannercms')) {
            return $this->error_access();
        }

        $bannercms = $this->input->post('bannercms');

		if (empty($bannercms)) {
			$message = lang('con.cb07');
			$this->alertMessage(2, $message);
			redirect(BASEURL . 'cmsbanner_management/viewBannerManager');
			return null;
		}

        $bannercms = (is_array($bannercms)) ? $bannercms : [$bannercms];

        foreach ($bannercms as $bannercmsId) {
            $this->cmsbanner_library->deleteBannerCms($bannercmsId);

            $this->recordAction('CMS Banner Setting Management', 'Delete cms banner id:' . $bannercmsId, "User " . $this->authentication->getUsername() . " delete cms banner id: " . $bannercmsId);
        }

        $message = lang('con.cb06');
        $this->alertMessage(1, $message); //will set and send message to the user
        redirect(BASEURL . 'cmsbanner_management/viewBannerManager');

        return null;
	}

	/**
	 * Delete cms promo
	 *
	 * @param   int
	 * @return  redirect
	 */
	public function deleteBannerCmsItem($bannercmsId) {
        if (!$this->permissions->checkPermissions('bannercms')) {
            return $this->error_access();
        }

        $this->cmsbanner_library->deleteBannerCms($bannercmsId);

		$this->recordAction('CMS Banner Setting Management', 'Delete cms banner id:' . $bannercmsId, "User " . $this->authentication->getUsername() . " delete cms banner id: " . $bannercmsId);

		$message = lang('con.cb08');
		$this->alertMessage(1, $message);
		redirect(BASEURL . 'cmsbanner_management/viewBannerManager');

        return null;
	}

	/**
	 * activate banner cms
	 *
	 * @param   promocmsId
	 * @param   status
	 * @return  mixed
	 */
	public function activateBannerCms($bannercmsId, $status) {
        if (!$this->permissions->checkPermissions('bannercms')) {
            return $this->error_access();
        }

		$this->cmsbanner_library->activateBannerCms($bannercmsId, $status);

		$this->recordAction('CMS Banner Setting Management', 'Update banner id: ' . $bannercmsId . ' to status: ' . $status, "User " . $this->authentication->getUsername() . " edit cms banner status to " . $status);

		redirect(BASEURL . 'cmsbanner_management/viewBannerManager');

        return null;
	}
}

/* End of file cms_management.php */
/* Location: ./application/controllers/cms_management.php */