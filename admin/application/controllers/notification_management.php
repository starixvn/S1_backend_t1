<?php
require_once dirname(__FILE__) . '/BaseController.php';

class Notification_Management extends BaseController {

	public $path;

	function __construct() {
		parent::__construct();
		$this->load->helper(array('url', 'form'));
		$this->load->library(array('permissions', 'form_validation', 'template', 'pagination', 'form_validation'));
		$this->load->model(array('notifications', 'notification_setting'));

		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user

		//if( ! is_dir(realpath($this->getUploadPath()).'/notifications') ) @mkdir(realpath($this->getUploadPath()).'/notifications', 777);

		$this->path = realpath($this->getUploadPath()).'/notifications';
		$this->path = rtrim($this->path, '/');
		$this->utils->addSuffixOnMDB($this->path);
	}
	/**
	 * Used for redirection when permission is disabled
	 *
	 * Created by: Mark Andrew Mendoza (andrew.php.ph)
	 */
	private function error_access() {
		$this->loadTemplate('Notification Management', '', '', 'system');
		$systemUrl = $this->utils->activeSystemSidebar();
		$data['redirect'] = $systemUrl;

		$message = lang('con.i01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}

	public function index(){
		if(!$this->permissions->checkPermissions('notification'))
			return $this->error_access();

		$permissions = $this->permissions->getPermissions();
		$data['records'] = $this->notifications->getLists();

		$notifications = $this->notification_setting->getNotification();

		$notification = array();
		foreach ($notifications as $key => $value) $notification[] = $value['id'];

		$data['notifications'] = $notification;

        $this->template->add_js('resources/js/chosen.jquery.min.js');
		$this->template->add_js('resources/js/notification_management/jquery.mask.min.js');
		$this->template->add_js('resources/js/notification_management/notification.js');
		$this->loadTemplate('Notification Management', '', '', 'system');
		$this->template->write_view('main_content', 'notification_management/view_lists', $data);
		$this->template->render();

	}

	public function add(){

		if( $this->input->post() ) return $this->submit();

		$data=[
			'max_ogg_file_size'=>$this->utils->getConfig('max_ogg_file_size'),
		];

		$this->loadTemplate('Notification Management', '', '', 'management');
		$this->template->write_view('main_content', 'notification_management/form', $data);
		$this->template->render();

	}

	public function delete( $id = '' ){

		try{

			if( empty( $id ) ) throw new Exception("No records Found.");

			$record = $this->notifications->get_record( $id );
			$path = $this->path . '/' . $record->file;

			if( ! $this->notifications->delete( $id ) ) throw new Exception(lang('notify.deletefailed'));

			unlink($path);

			$this->alertMessage(1, lang('notify.deletesuccess'));

		}catch(Exception $e){

			$this->alertMessage(2, $e->getMessage());

		}

		redirect('notification_management');

	}

	public function delete_multiple(){

		$success = $failed = 0;
		$ids = explode(',', $this->input->post('ids'));

		foreach ($ids as $key => $id) {

			$record = $this->notifications->get_record( $id );
			$path = $this->path . '/' . $record->file;

			if( ! $this->notifications->delete( $id ) ){
				$failed++;
				continue;
			}

			unlink($path);

			$success++;

		}
		/*
		$msg = $success . " has been successfully deleted.";

		if( $failed > 0 ){
			$msg .= "<br>";
			$msg .= $failed . " was failed to deleted.";
		}*/

		$this->alertMessage(1, lang('notify.deletesuccess'));

		return true;

	}

	public function settings(){

		$data['records'] = $this->notifications->getLists();
		$settings = $this->notification_setting->getAll();

		$settingID = $notification = array();
		foreach ($settings as $key => $value) $settingID[] = $value['notification_type'];

		$data['setting'] = $settingID;

		$notifications = $this->notification_setting->getNotification();

		foreach ($notifications as $key => $value) $notification[$value['notification_type']] = $value['file'];

		$data['notification'] = $notification;
		$data['path'] = $this->path;
		$data['activeCurrencyKeyOnMDB'] =$this->utils->getActiveCurrencyKeyOnMDB();

        $this->template->add_js('resources/js/chosen.jquery.min.js');
        $this->template->add_js('resources/js/notification_management/jquery.mask.min.js');
		$this->template->add_js('resources/js/notification_management/notification.js');
		$this->loadTemplate('Notification Management', '', '', 'management');
		$this->template->write_view('main_content', 'notification_management/settings', $data);
		$this->template->render();

	}

	public function set_notification(){

		try{

			$notif_type = $this->input->post('notif_id');
			$notif_sound = $this->input->post('notif_sound');

			$data = array();

			if( $this->notification_setting->get( $notif_type ) ){

				$data['notification_id'] = $notif_sound;

				if( ! $this->notification_setting->update( $notif_type, $data ) ) throw new Exception(lang('notify.set.failed'));

			}else{

				$data['notification_type'] = $notif_type;
				$data['notification_id'] = $notif_sound;

				if( ! $this->notification_setting->insert( $data ) ) throw new Exception(lang('notify.set.failed'));
			}

			$this->alertMessage(1, lang('notify.set.success'));

		}catch(Exception $e){
			$this->alertMessage(2, $e->getMessage());
		}

		return true;

	}

	public function remove_notification( $id = '' ){

		try{

			if( empty($id) ) throw new Exception(lang('lang.norec'));

			if( ! $this->notification_setting->remove( $id ) ) throw new Exception(lang('notify.remove.failed'));

			$this->alertMessage(1, lang('notify.remove.success'));


		}catch(Exception $e){
			$this->alertMessage(2, $e->getMessage());
		}

		redirect('notification_management/settings');

	}

	private function submit(){

		try{

			$this->set_rules();

			$file = $_FILES['file']['name'];

			if( $this->form_validation->run() == FALSE && $file == "" ) throw new Exception(false);

			$file_upload = $this->upload();

			if(isset($file_upload['error'])){
				$this->utils->error_log($file, $file_upload);
				throw new Exception($file_upload['error']);
			}

			$data = array(
				'title' => $this->input->post('title'),
				'file' => $file_upload['filename']
			);

			if( ! $this->notifications->insert($data) ) throw new Exception(false);

			$this->alertMessage(1, lang('notify.addsuccess'));

		}catch(Exception $e){
			$this->alertMessage(2, lang('notify.failedAdding'));
		}

		redirect('notification_management');

	}

	private function set_rules(){

		$config = array(
		        array(
		                'field' => 'title',
		                'label' => 'Title',
		                'rules' => 'required'
		        ),
		        array(
		                'field' => 'file',
		                'label' => 'File',
		                'rules' => 'required'
		        )
		);

		return $this->form_validation->set_rules($config);

	}

	/**
	 * overview : upload file to resources
	 *
	 * @return	void
	 */
	public function upload() {

		if ($_FILES['file']['name'] != "") {

			$filename=random_string('unique');

			$config['upload_path'] = $this->path;
			$config['allowed_types'] = '*';
			$config['max_size'] = $this->utils->getMaxUploadSizeByte();
			$config['remove_spaces'] = true;
			$config['overwrite'] = true;
			$config['max_width'] = '';
			$config['max_height'] = '';
			$config['file_name'] = $filename;

			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			if (!$this->upload->do_upload('file')) {
				$error = array('error' => $this->upload->display_errors());
				return $error;
			} else {

				$file = $this->upload->data();

				$this->utils->debug_log('update', $file);

				$result = array(
					'file_ext' => $file['file_ext'],
					'filename'=> $file['orig_name'],
					'filepath' => $this->path . '/' . $file['orig_name'],
				);

				return $result;
			}
		}

	}

	/**
	 * overview : loads template
	 *
	 * detail : Loads template for view based on regions in config > template.php
	 *
	 * @param  string 	$title
	 * @param  string 	$description
	 * @param  string 	$keywords
	 * @param  string 	$activenav
	 * @return load template
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_js('resources/js/system_management/user_management.js');

		$this->template->add_js('resources/js/datatables.min.js');
		//$this->template->add_js('resources/js/jquery.dataTables.min.js');
		//$this->template->add_js('resources/js/dataTables.responsive.min.js');

		$this->template->add_css('resources/css/general/style.css');
		//$this->template->add_css('resources/css/jquery.dataTables.css');
		//$this->template->add_css('resources/css/dataTables.responsive.css');
		$this->template->add_css('resources/css/datatables.min.css');

		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());

		/*$lang = $this->language_function->getCurrentLanguage();
			$langCode = $this->language_function->getLanguageCode($lang);
			$language = $this->language_function->getLanguage($lang);
		*/
		$this->template->write_view('sidebar', 'system_management/sidebar');
	}

}

/* End of file notification_management.php */
/* Location: ./application/controllers/notification_management.php */
