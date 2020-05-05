<?php

/**
 * CMS Footer Content Management
 *
 * CMS Footer Content Management Controller for FHI OG System
 *
 * @author  ASRII
 *
 */

class Cmsfootercontent_Management extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('date_helper','url'));
        $this->load->model('cms_model');
        $this->load->library(array('permissions','form_validation', 'template', 'pagination','excel', 'report_functions'));

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
            'status' => 0
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

        $this->template->add_js('resources/js/cms_management/cmsfootercontent_management.js');
        # JS
        // $this->template->add_js('resources/js/moment.min.js');
        // $this->template->add_js('resources/js/daterangepicker.js');
        $this->template->add_js('resources/js/chosen.jquery.min.js');
        $this->template->add_js('resources/js/summernote.min.js');
        // $this->template->add_js('resources/js/bootstrap-datetimepicker.js');
        $this->template->add_js('resources/js/marketing_management/marketing_management.js');
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

        $message = lang('con.cf01');
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
        redirect(BASEURL . 'cmsfootercontent_management/viewContentManager');
    }


    /**
     * view footercontent settings page
     *
     * @return  void
     */
    public function viewContentManager() {
        // if(!$this->permissions->checkPermissions('cms_footercontent_settings')){
        //     $this->error_access();
        // } else {
            $sort = "footercontentName";
            $this->loadTemplate('CMS Management', '', '', 'cms');

            $data['count_all'] = count($this->cms_model->getAllCMSFootercontent($sort, null, null));
            $config['base_url'] = "javascript:get_footercontentcms_pages(";
            $config['total_rows'] = $data['count_all'];
            $config['per_page'] = '20';
            $config['num_links'] = '1';

            $config['first_tag_open'] = '<li>';
            $config['last_tag_open']= '<li>';
            $config['next_tag_open']= '<li>';
            $config['prev_tag_open'] = '<li>';
            $config['num_tag_open'] = '<li>';

            $config['first_tag_close'] = '</li>';
            $config['last_tag_close']= '</li>';
            $config['next_tag_close']= '</li>';
            $config['prev_tag_close'] = '</li>';
            $config['num_tag_close'] = '</li>';

            $config['cur_tag_open'] = "<li><span><b>";
            $config['cur_tag_close'] = "</b></span></li>";

            $this->pagination->initialize($config);

            $data['total_pages'] = ceil($data['count_all'] / $config['per_page']);
            $data['data'] = $this->cms_model->getAllCMSFootercontent($sort, null, null);

            //export report permission checking
            if(!$this->permissions->checkPermissions('export_report')){
                $data['export_report_permission'] = FALSE;
            } else {
                $data['export_report_permission'] = TRUE;
            }

            $this->template->write_view('main_content', 'cms_management/footercontent/view_cmsfootercontent_settings', $data);
            $this->template->render();
        // }
    }

    /**
     * get pages
     *
     * @param   sort
     * @return  void
     */
    public function get_promosetting_pages($segment) {
        $sort = "footercontentName";

        $data['count_all'] = count($this->cms_model->getAllCMSFootercontent($sort, null, null));
        $config['base_url'] = "javascript:get_footercontentcms_pages(";
        $config['total_rows'] = $data['count_all'];
        $config['per_page'] = '10';
        $config['num_links'] = '2';

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
        $data['data'] = $this->cms_model->getAllCMSFootercontent($sort, null, $segment);

        $this->load->view('cms_management/footercontent/ajax_cmsfootercontent_settings', $data);
    }

    /**
     * add/edit footercontent setting
     *
     * @return  array
     */
    public function addNewFootercontent() {
        $this->form_validation->set_rules('title', 'Footer Content Title' , 'trim|required|xss_clean');
        $this->form_validation->set_rules('language', 'Footer Content Language' , 'trim|required|xss_clean');

        //var_dump($promoCategory);exit();
        if($this->form_validation->run() == false) {
            $message = lang('con.cf02');
            $this->alertMessage(2, $message);
        } else {
                //var_dump($promoCategory);exit();
                $footercontentTitle = $this->input->post('title');
                $footercontentContent = $this->input->post('content');
                $footercontentLanguage = $this->input->post('language');
                $today = date("Y-m-d H:i:s");
                $footercontentcmsId = $this->input->post('footercontentcmsId');

                if($footercontentcmsId!='') {
                    //var_dump($footercontentContent);exit();
                    $data = array(
                            'footercontentName' => $footercontentTitle,
                            'language' => $footercontentLanguage,
                            'content' => $footercontentContent,
                            'category' => 'footer',
                            'updatedBy' => $this->authentication->getUserId(),
                            'updatedOn' => $today
                        );

                    $this->cms_model->editFootercontentCms($data, $footercontentcmsId);
                    $message = lang('con.cf03') . " <b>" . $footercontentTitle . "</b> " . lang('con.cf04');

                    $data = array(
                            'username' => $this->authentication->getUsername(),
                            'management' => 'Edit CMS Footer Content Setting Management',
                            'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
                            'action' => 'Edit CMS Footer Content Name: '.$footercontentcmsId,
                            'description' => "User " . $this->authentication->getUsername() . " edit CMS footercontent id: ".$footercontentcmsId,
                            'logDate' => $today,
                            'status' => 0
                        );

                    $this->report_functions->recordAction($data);
                } else {

                    $data = array(
                        'footercontentName' => $footercontentTitle,
                        'language' => $footercontentLanguage,
                        'content' => $footercontentContent,
                        'category' => 'footer',
                        'createdBy' => $this->authentication->getUserId(),
                        'createdOn' => $today,
                        'status' => 'active'
                    );

                    $this->cms_model->addCmsFootercontent($data);
                    $message = "<b>" . $footercontentTitle . "</b> " . lang('con.cf05');
                }

                $this->alertMessage(1, $message);
            }
        redirect(BASEURL . 'cmsfootercontent_management/viewContentManager');
    }

    /**
     * search footercontent cms
     *
     *
     * @return  redirect page
     */
    public function searchFootercontentCms($search='') {
        $data['count_all'] = count($this->cms_model->searchFootercontentCms($search, null, null));
        $config['base_url'] = "javascript:get_footercontentcms_pages(";
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
        $data['data'] = $this->cms_model->searchFootercontentCms($search, null, null);

        //export report permission checking
        // if(!$this->permissions->checkPermissions('export_report')){
        //     $data['export_report_permission'] = FALSE;
        // } else {
        //     $data['export_report_permission'] = TRUE;
        // }
        $this->load->view('cms_management/footercontent/ajax_cmsfootercontent_settings', $data);
    }

    /**
     * sort promo cms
     *
     * @param   sort
     * @return  void
     */
    public function sortFootercontentCms($sort) {
        $data['count_all'] = count($this->cms_model->getAllCMSFootercontent($sort, null, null));
        $config['base_url'] = "javascript:get_footercontentcms_pages(";
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
        $data['data'] = $this->cms_model->getAllCMSFootercontent($sort, null, null);

        $this->load->view('cms_management/footercontent/ajax_cmsfootercontent_settings', $data);
    }

    /**
     * get cms footercontent details
     *
     * @param   int
     * @return  redirect
     */
    public function getFootercontentCmsDetails($footercontentcmsId) {
        echo json_encode($this->cms_model->getFootercontentCmsDetails($footercontentcmsId));
    }

    /**
     * Delete selected cms promo
     *
     * @param   int
     * @return  redirect
     */
    public function deleteSelectedFootercontentCms() {
        $footercontentcms = $this->input->post('footercontentcms');
        $today = date("Y-m-d H:i:s");

        if($footercontentcms != '') {
            foreach ($footercontentcms as $footercontentcmsId) {
                $this->cms_model->deleteFootercontentCms($footercontentcmsId);
                $this->cms_model->deleteFootercontentCmsItem($footercontentcmsId);
            }

            $message = lang('con.cf06');
            $this->alertMessage(1, $message); //will set and send message to the user
            redirect(BASEURL . 'cmsfootercontent_management/viewContentManager');

            $data = array(
                'username' => $this->authentication->getUsername(),
                'management' => 'CMS Footercontent Setting Management',
                'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
                'action' => 'Delete cms footercontent id:'.$promocmsId,
                'description' => "User " . $this->authentication->getUsername() . " delete cms footercontent id: ".$footercontentcmsId,
                'logDate' => date("Y-m-d H:i:s"),
                'status' => 0
            );

            $this->report_functions->recordAction($data);
        } else {
            $message = lang('con.cf08');
            $this->alertMessage(2, $message);
            redirect(BASEURL . 'cmsfootercontent_management/viewContentManager');
        }
    }

    /**
     * Delete cms footer content
     *
     * @param   int
     * @return  redirect
     */
    public function deleteFootercontentCmsItem($footercontentcmsId) {
        $this->cms_model->deleteFootercontentCms($footercontentcmsId);

        $data = array(
                'username' => $this->authentication->getUsername(),
                'management' => 'CMS Footercontent Setting Management',
                'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
                'action' => 'Delete cms footercontent id:'.$footercontentcmsId,
                'description' => "User " . $this->authentication->getUsername() . " delete vip cms promo id: ".$promocmsId,
                'logDate' => date("Y-m-d H:i:s"),
                'status' => 0
            );

        $this->report_functions->recordAction($data);

        $message = lang('con.cf08');
        $this->alertMessage(1, $message);
        redirect(BASEURL . 'cmsfootercontent_management/viewContentManager');
    }

    /**
     * activate footercontent cms
     *
     * @param   promocmsId
     * @param   status
     * @return  redirect
     */
    public function activateFootercontentCms($footercontentcmsId,$status) {
        $data = array(
                        'updatedBy' => $this->authentication->getUserId(),
                        'updatedOn' => date("Y-m-d H:i:s"),
                        'status' => $status,
                        'footercontentId' => $footercontentcmsId
                        );

        $this->cms_model->activateFootercontentCms($data);

        $data = array(
                'username' => $this->authentication->getUsername(),
                'management' => 'CMS Footercontent Setting Management',
                'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
                'action' => 'Update status of vip group id:'.$vipsettingId. 'to status:'.$status,
                'description' => "User " . $this->authentication->getUsername() . " edit cms footercontent status to ".$status,
                'logDate' => date("Y-m-d H:i:s"),
                'status' => 0
            );

        $this->report_functions->recordAction($data);

        redirect(BASEURL . 'cmsfootercontent_management/viewContentManager');
    }

    /**
     * preview footer content cms
     *
     * @param   footercontentId
     * @return  redirect
     */
    public function viewFootercontentDetails($footercontentId) {
        $data['cmsfootercontent'] = $this->cms_model->getFooterContentCmsDetails($footercontentId);
        $this->load->view('cms_management/footercontent/view_footercontent_details',$data);
    }
}

/* End of file cms_management.php */
/* Location: ./application/controllers/cms_management.php */