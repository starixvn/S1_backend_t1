<?php
require_once dirname(__FILE__) . '/BaseController.php';

class Theme_Management extends BaseController {

	public $path;
	public $header_php_path;
	public $header_logo_path;
	public $site_path;
	public $mobile_site_path;
	// public $home_dir;
	public $prefix_header;
	public $prefix_footer;
	public $register_footer;
	public $shtml_header_filename;
	public $shtml_header_bak_filename;
	public $shtml_footer_filename;
	public $shtml_footer_bak_filename;
	public $footer_php_path;
	public $themes_style_path;
	public $themes_image_path;
	public $register_php_path;
	public $js_path;
	public $prefix_js;
	public $live_mode;
	public $is_k8s;
	public $mobile_login_php_path;
	public $mobile_login;

	const ACTION_MANAGEMENT_TITLE = 'Theme Management';

	function __construct() {
		parent::__construct();
		$this->load->helper(array('url', 'form', 'download'));
		$this->load->library(array('permissions', 'form_validation', 'template', 'pagination', 'Multiple_image_uploader','report_functions'));
		$this->load->model(array('notifications', 'notification_setting'));

		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user

		$this->prefix_header = 'dynamic_top_';
		$this->prefix_footer = 'dynamic_footer_';
		$this->register_footer = 'register_';
		$this->mobile_login = 'login_';
		$this->prefix_js = 'js-';
		$this->shtml_header_filename = '_main_nav.shtml';
		$this->shtml_header_bak_filename = '_main_nav_bak.shtml';
		$this->shtml_footer_filename = '_main_footer.shtml';
		$this->shtml_footer_bak_filename = '_main_footer_bak.shtml';

		//if( ! is_dir(realpath($this->getUploadPath()).'/notifications') ) @mkdir(realpath($this->getUploadPath()).'/notifications', 777);
		// $this->home_dir = $_SERVER['/'];

		$this->path = realpath($this->getUploadPath()).'/notifications';
		$this->path = rtrim($this->path, '/');
		$this->utils->addSuffixOnMDB($this->path);
		$this->header_php_path = rtrim($this->utils->getHeaderTemplatePath(), '/');
		//$this->header_logo_path = $this->utils->getLogoTemplatePath();
		//live or staging
		$hostname=gethostname();
		$this->is_k8s=getenv('KUBERNETES_PORT')!==false;

		if($this->is_k8s){
			if(strpos($hostname, 'staging')===false){
				//it's live
				$this->live_mode=true;

				$this->mobile_site_path= '/home/vagrant/site/mobile_live/';
				$this->site_path = '/home/vagrant/site/live/';
			}else{
				$this->live_mode=false;

				$this->mobile_site_path= '/home/vagrant/site/mobile_live/';
				$this->site_path = '/home/vagrant/site/live/'; //$this->home_dir.'/home/vagrant/'.$this->utils->getConfig('site_path');
			}
		}else{
			if(strpos($hostname, 'staging')===false){
				$this->live_mode=true;

				$this->mobile_site_path= '/home/vagrant/Code/mobile_site/';
				$this->site_path = '/home/vagrant/Code/site/';
			}else{
				$this->live_mode=false;

				$this->mobile_site_path= '/home/vagrant/Code/mobile_site/';
				$this->site_path = '/home/vagrant/Code/site/'; //$this->home_dir.'/home/vagrant/'.$this->utils->getConfig('site_path');
			}
		}

		$this->footer_php_path = rtrim($this->utils->getFooterTemplatePath(), '/');
		$this->themes_style_path = $this->utils->getThemesTemplatePath().'/styles/';
		$this->themes_image_path = $this->utils->getThemesTemplatePath().'/img_themes/';
		$this->js_path = rtrim($this->utils->getJsTemplatePath(), '/');

		$this->utils->debug_log('path',[
			'footer_php_path'=>$this->footer_php_path,
			'themes_style_path'=>$this->themes_style_path,
			'themes_image_path'=>$this->themes_image_path,
			'js_path'=>$this->js_path,
		]);

		//Register Template
		//var_dump(dirname(__FILE__).'/../../../player/application/views/'.$this->utils->getPlayerCenterTemplate().'/auth/', '/');die();
		$this->register_php_path = rtrim(dirname(__FILE__).'/../../../player/application/views/resources/common/auth/', '/');
		$this->mobile_login_php_path = rtrim(dirname(__FILE__).'/../../../player/application/views/'.$this->utils->getPlayerCenterTemplate().'/mobile/auth/', '/');
	}

	const MESSAGE_TYPE_SUCCESS = 1;
	const MESSAGE_TYPE_ERROR = 2;
	const MESSAGE_TYPE_WARNING = 3;

	protected function alertMessage($type, $message) {

			$type = intval($type);
			switch ($type) {
			case self::MESSAGE_TYPE_SUCCESS:
				$show_message = array(
					'result' => 'success',
					'message' => $message,
				);
				$this->session->set_userdata($show_message);
				break;

			case self::MESSAGE_TYPE_ERROR:
				$show_message = array(
					'result' => 'danger',
					'message' => $message,
				);
				$this->session->set_userdata($show_message);
				break;

			case self::MESSAGE_TYPE_WARNING:
				$show_message = array(
					'result' => 'warning',
					'message' => $message,
				);
				$this->session->set_userdata($show_message);
				break;
			}
		}

	public function index($offLiveChat = false){
		if(!$this->permissions->checkPermissions('theme_management'))
		return $this->error_access();

		$files_default = glob(dirname(__FILE__) . '/../../../player/public/'.$this->utils->getPlayerCenterTemplate().'/styles/base-theme-*');
		$themes_default = array_map(function($file) {
			return preg_replace("#.*base-theme-([^\.]*)\.css.*#", '$1', $file);
		}, $files_default);

		$files_uploaded = glob($this->themes_style_path.'base-theme-*');
		$themes_uploaded = array_map(function($file) {
			return preg_replace("#.*base-theme-([^\.]*)\.css.*#", '$1', $file);
		}, $files_uploaded);
		$themes = array_merge($themes_default, $themes_uploaded);

		$data = array(
			'player_url' => 'https://player.' . $this->getMainDomain(),
			'selected_theme' => $this->utils->getPlayerCenterTheme(false),
			'themes' => $themes
		);
        $data['offChat'] = $offLiveChat;

        $this->load->model("static_site");
        $data['logo_icon'] = $this->static_site->getDefaultLogoUrl();
		$this->loadTemplate('Theme Management', '', '', 'theme_management');
		$this->template->write_view('main_content', 'theme_management/view_list', $data);
		$this->template->render();

	}

	public function save() {
		$theme = $this->input->post('theme');
		$dir_default = dirname(__FILE__) . '/../../../player/public/'.$this->utils->getPlayerCenterTemplate().'/styles/base-theme-' . $theme . '.css';
		$dir_uploaded = $this->themes_style_path.'base-theme-' . $theme . '.css';
		if (file_exists($dir_default) || file_exists($dir_uploaded)) {
			$success = $this->operatorglobalsettings->syncSettingJson("player_center_theme", $theme, 'value');
		} else {
			$message = lang('Theme not found');
			$success = false;
		}

		if ($success) {

			$this->load->helper('cookie');
			$domain = '.' . $this->getMainDomain();
			delete_cookie('preview_theme', $domain);

			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));

		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, !empty($message) ? $message : lang('error.default.db.message'));
		}

		redirect('theme_management/index');
	}

	public function preview($preview_theme) {
		$this->load->helper('cookie');
		$domain = '.' . $this->getMainDomain();
		set_cookie('preview_theme', $preview_theme, 60, $domain);
		redirect('http://player' . $domain);
	}

	public function reset() {
		$this->load->helper('cookie');
		$domain = '.' . $this->getMainDomain();
		delete_cookie('preview_theme', $domain);
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
		$this->template->add_js('resources/js/theme_management/theme_management.js');

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
		$this->template->write_view('sidebar', 'theme_management/sidebar');
	}

	public function upload_new_themes(){
		$file_css_theme = isset($_FILES['file_css_theme']) ? $_FILES['file_css_theme'] : null;
		$file_image = isset($_FILES['file_image']) ? $_FILES['file_image'] : null;
		$theme_name = str_replace(' ', '_', $this->input->post('txtName'));

		$path_css_theme =$this->themes_style_path;
		$path_css_theme=rtrim($path_css_theme, '/');

		$css_name = 'base-theme-'. $theme_name;

		$path_image =$this->themes_image_path;
		$path_image=rtrim($path_image, '/');
		$image_name = 'playercenter_'. $theme_name;

		if(empty($theme_name)) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('theme_name_required'));
		} else if(empty($file_css_theme['size'][0])) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('css_theme_required'));
		} else if(empty($file_image['size'][0])) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Please select image'));
		} else {
			$config_css_theme = array(
	            'allowed_types' => 'css',
	            'max_size'      => $this->utils->getMaxUploadSizeByte(),
	            'overwrite'     => true,
	            'remove_spaces' => true,
	            'upload_path'   => $path_css_theme,
	        );

	        $config_image = array(
	            'allowed_types' => 'jpg',//array("jpg","jpeg","png","gif", "PNG"),
	            'max_size'      => $this->utils->getMaxUploadSizeByte(),
	            'overwrite'     => true,
	            'remove_spaces' => true,
	            'upload_path'   => $path_image,
	        );

	        $response_image = $this->multiple_image_uploader->do_multiple_uploads($file_image, $path_image, $config_image, $image_name);

	        if($response_image['status'] == "fail" ) {
	        	$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response_image['message']);
	        }

	        $response_css = $this->multiple_image_uploader->do_multiple_uploads($file_css_theme, $path_css_theme, $config_css_theme, $css_name);
	        if($response_css['status'] == "fail" ) {
	        	$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response_image['message']);
	        }
		}


        redirect('theme_management/index');
	}

	public function headerIndex($offLiveChat = false){
		if(!$this->permissions->checkPermissions('theme_management'))
		return $this->error_access();

		$files = glob(realpath($this->header_php_path).'/'.$this->prefix_header.'*');
		$headers = array_map(function($file) {
			return preg_replace("#.*".$this->prefix_header."([^\.]*)\.php.*#", '$1', $file);
		}, $files);

        $builtin_headers = [
            'default',
            'lottery',
            'model1-header',
            'model2-header',
            'model3-header'
        ];

		$data = array(
			'player_url' => $this->utils->getSystemUrl('player'),
			'selected_header' => $this->utils->getPlayerCenterHeader(false),
            'builtin_headers' => $builtin_headers,
            'headers' => $headers,
		);
        $data['offChat'] = $offLiveChat;
		if(!empty($this->session->userdata('message'))) {
			$data['alert_message'] = $this->session->userdata('message');
		}

        $this->load->model("static_site");
        $data['logo_icon'] = $this->static_site->getDefaultLogoUrl();
		$this->loadTemplate('Theme Management', '', '', 'theme_management');
		$this->template->write_view('main_content', 'theme_management/header_list', $data);
		$this->template->render();

	}

	public function saveHeader() {
		$header = $this->input->post('header_template');

        $success = FALSE;

		$dynamic_header_path = $this->header_php_path.'/'.$this->prefix_header. $header . '.php';
		if (file_exists($dynamic_header_path)) {
            $success = TRUE;
		}

		$buildin_header_path = $this->utils->getHeaderTemplateBuiltInPath() . $header . '.tmpl';
        if (!$success && file_exists($buildin_header_path)) {
            $success = TRUE;
        }

		if ($success) {
            if($this->operatorglobalsettings->syncSettingJson("player_center_header", $header, 'value')){
                $this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Change header template', "User ".$this->authentication->getUsername()." change header template from". $this->utils->getPlayerCenterHeader(false) . "to header" .$header );

                $this->load->helper('cookie');
                $domain = '.' . $this->getMainDomain();
                delete_cookie('preview_header', $domain);

                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
            }else{
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
            }
		} else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Header not found'));
		}

		redirect('theme_management/headerIndex');
	}

	public function upload_new_header(){
		$files = glob($this->header_php_path.$this->prefix_header.'*');
		$headers = array_map(function($file) {
			return preg_replace("#.*".$this->prefix_header."([^\.]*)\.php.*#", '$1', $file);
		}, $files);

		$file_php_header = isset($_FILES['file_php_header']) ? $_FILES['file_php_header'] : null;
		//$file_header_logo = isset($_FILES['file_header_logo']) ? $_FILES['file_header_logo'] : null;
		//$file_image = isset($_FILES['file_image']) ? $_FILES['file_image'] : null;
		$header_name = str_replace(' ', '_', $this->input->post('txtName'));
		$file_to_upload = array();

		if(in_array($header_name, $headers)) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('header.template.msg6'));
		} else {
			$php_header = $this->header_php_path;
			//$php_header=rtrim($php_header, '/');
			$php_name = $this->prefix_header. $header_name;

			/*$path_image = $this->header_img_path;
			$path_image=rtrim($path_image, '/');
			$image_name = 'playercenter_header_'. $header_name;*/

			//$path_logo_image = $this->header_logo_path;
			//$path_logo_image=rtrim($path_logo_image, '/');

			$image_logo_name = 'playercenter_header_logo_'. $header_name;

			if(empty($file_php_header['size'][0]) || empty($header_name)) {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('player.mp14'));
			} else {
				$config_header_php = array(
		            'allowed_types' => 'tmpl|phtml',
		            'max_size'      => $this->utils->getMaxUploadSizeByte(),
		            'overwrite'     => true,
		            'remove_spaces' => true,
		            'upload_path'   => $php_header,
		        );

				$file_to_upload[] = array(
					"file_details" => $file_php_header,
					"upload_path" => $php_header,
					"config_header" => $config_header_php,
					"file_name" => $php_name,
				);

		        /*$config_logo_image = array(
		            'allowed_types' => 'png',//array("jpg","jpeg","png","gif", "PNG"),
		            'max_size'      => $this->utils->getMaxUploadSizeByte(),
		            'overwrite'     => true,
		            'remove_spaces' => true,
		            'upload_path'   => $path_logo_image,
		        );

		        $file_to_upload[] = array(
					"file_details" => $file_header_logo,
					"upload_path" => $path_logo_image,
					"config_header" => $config_logo_image,
					"file_name" => $image_logo_name,
				);*/

		        foreach ($file_to_upload as $key => $value) {
		        	$response = $this->multiple_image_uploader->do_multiple_uploads($value['file_details'], $value['upload_path'], $value['config_header'], $value['file_name']);
			        if($response['status'] == "fail" ) {
			        	$this->deleteHeader($header_name);
			        	$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response['message']);
			        	redirect('theme_management/headerIndex');
			        	//die();
			        }
		        }

		        //rename the tmpl file to php
		        $file_from = $this->header_php_path.'/'.$php_name.'.tmpl';
		        if (file_exists($file_from)) {
		        	$file_to = $this->header_php_path.'/'.$php_name.'.php';
		        	rename($file_from , $file_to);
		        }

		        $this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Add header template', "User ".$this->authentication->getUsername()." add header template ". $header_name );
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('header.template.msg3'));
			}

		}


        redirect('theme_management/headerIndex');
	}

	public function previewHeader($preview_header) {
		$this->load->helper('cookie');
		$domain = '.' . $this->getMainDomain();
		set_cookie('preview_header', $preview_header, 60, $domain);
		redirect($this->utils->getSystemUrl("player"));
	}

	public function deleteHeader($header){
		if($header == $this->utils->getPlayerCenterHeader(false)){
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('header.template.msg1'));
		} else {
			$file_to_remove = array();

			$file_to_remove[] = $this->header_php_path.'/'.$this->prefix_header.$header.'.php';
			$file_to_remove[] = $this->header_php_path.'/'.$this->prefix_header.$header.'.tmpl';
			//$file_to_remove[] = $this->header_logo_path.'/playercenter_header_logo_'.$header.'.png';

			foreach ($file_to_remove as $key => $value) {
				if (file_exists($value)) {
					unlink($value);
	  			}
			}

			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete header template', "User ".$this->authentication->getUsername()." delete header template ". $header );
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('header.template.msg2'));
		}
	}

	public function resetHeader() {
		$this->load->helper('cookie');
		$domain = '.' . $this->getMainDomain();
		delete_cookie('preview_header', $domain);
	}

	public function setToDefault() {
		$this->operatorglobalsettings->syncSettingJson("player_center_header", null, 'value');
		//$this->operatorglobalsettings->syncSettingJson("player_center_logo", null, 'value');
		$delete_file = $this->site_path.$this->shtml_header_filename;
		$rebirt_file = $this->site_path.$this->shtml_header_bak_filename;
		if (file_exists($delete_file) && file_exists($rebirt_file)) {
			unlink($delete_file);
			rename ($rebirt_file , $delete_file);
		}

	}

	public function transferFileToSite($header) {
		if($header != $this->utils->getPlayerCenterHeader(false)){
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, sprintf(lang('header.template.msg9'),$header));
		} else {
            //render then save
            $file_to = $this->site_path.$this->shtml_header_filename;
            $content = $this->utils->processHeaderTemplate();
            $rlt = file_put_contents($file_to, $content);

            if($rlt) {
                chmod($file_to, 0777);
                chmod($this->site_path.$this->shtml_header_bak_filename, 0777);
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, sprintf(lang('template.msg3'),$this->utils->getPlayerCenterHeader(false)));
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, sprintf(lang('template.msg4'),$this->utils->getPlayerCenterHeader(false)));
            }
		}
	}

	public function footerIndex($offLiveChat = false){
		if(!$this->permissions->checkPermissions('theme_management'))
		return $this->error_access();

		$files = glob(realpath($this->footer_php_path).'/'.$this->prefix_footer.'*');
		$footers = array_map(function($file) {
			return preg_replace("#.*".$this->prefix_footer."([^\.]*)\.php.*#", '$1', $file);
		}, $files);

        $builtin_footers = [
            'lottery',
            'model1-footer',
            'model2-footer',
            'model3-footer'
        ];

		$data = array(
			'player_url' => $this->utils->getSystemUrl("player"),
			'selected_footer' => $this->utils->getPlayerCenterFooter(false),
			'builtin_footers' => $builtin_footers,
			'footers' => $footers,
		);

        $data['offChat'] = $offLiveChat;
		if(!empty($this->session->userdata('message'))) {
			$data['alert_message'] = $this->session->userdata('message');
		}

        $this->load->model("static_site");
        $data['logo_icon'] = $this->static_site->getDefaultLogoUrl();
		$this->loadTemplate('Theme Management', '', '', 'theme_management');
		$this->template->write_view('main_content', 'theme_management/footer_list', $data);
		$this->template->render();
	}

	public function upload_new_footer(){
		$files = glob($this->footer_php_path.$this->prefix_footer.'*');
		$footers = array_map(function($file) {
			return preg_replace("#.*".$this->prefix_footer."([^\.]*)\.php.*#", '$1', $file);
		}, $files);

		$file_php_footer = isset($_FILES['file_php_footer']) ? $_FILES['file_php_footer'] : null;

		$footer_name = str_replace(' ', '_', $this->input->post('txtName'));
		$file_to_upload = array();

		if(in_array($footer_name, $footers)) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('header.template.msg6'));
		} else {
			$php_footer = $this->footer_php_path;

			$php_name = $this->prefix_footer. $footer_name;

			if(empty($file_php_footer['size'][0]) || empty($footer_name)) {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('player.mp14'));
			} else {

				$config_footer_php = array(
		            'allowed_types' => 'tmpl',
		            'max_size'      => $this->utils->getMaxUploadSizeByte(),
		            'overwrite'     => true,
		            'remove_spaces' => true,
		            'upload_path'   => $php_footer,
		        );

				$file_to_upload[] = array(
					"file_details" => $file_php_footer,
					"upload_path" => $php_footer,
					"config_header" => $config_footer_php,
					"file_name" => $php_name,
				);

		        foreach ($file_to_upload as $key => $value) {
		        	$response = $this->multiple_image_uploader->do_multiple_uploads($value['file_details'], $value['upload_path'], $value['config_header'], $value['file_name']);
			        if($response['status'] == "fail" ) {
			        	$this->deleteHeader($php_name);
			        	$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response['message']);
			        	redirect('theme_management/footerIndex');
			        	//die();
			        }
		        }

		        //rename the tmpl file to php
		        $file_from = $this->footer_php_path.'/'.$php_name.'.tmpl';
		        if (file_exists($file_from)) {
		        	$file_to = $this->footer_php_path.'/'.$php_name.'.php';
		        	rename($file_from , $file_to);
		        }
		        $this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Add footer template', "User ".$this->authentication->getUsername()." add header template ". $footer_name );
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('footer.template.msg3'));
			}

		}

        redirect('theme_management/footerIndex');
	}

	public function saveFooter() {
		$footer = $this->input->post('footer_template');

        $success = FALSE;

        $dynamic_footer_path = $this->footer_php_path.'/'.$this->prefix_footer. $footer . '.php';
		if (file_exists($dynamic_footer_path)) {
            $success = TRUE;
		}

        $buildin_footer_path = $this->utils->getFooterTemplateBuiltInPath() . $footer . '.tmpl';
        if (!$success && file_exists($buildin_footer_path)) {
            $success = TRUE;
        }

		if ($success) {
            if($this->operatorglobalsettings->syncSettingJson("player_center_footer", $footer, 'value')){
                $this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Change footer template', "User ".$this->authentication->getUsername()." change footer template from". $this->utils->getPlayerCenterFooter(false) . "to footer" .$footer );

                $this->load->helper('cookie');
                $domain = '.' . $this->getMainDomain();
                delete_cookie('preview_footer', $domain);

                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
            }else{
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
            }
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Footer not found'));
		}

		redirect('theme_management/footerIndex');
	}

	public function previewFooter($preview_footer) {
		$this->load->helper('cookie');
		$domain = '.' . $this->getMainDomain();
		set_cookie('preview_footer', $preview_footer, 60, $domain);

		redirect($this->utils->getSystemUrl("player"));
	}

	public function resetFooter() {
		$this->load->helper('cookie');
		$domain = '.' . $this->getMainDomain();
		delete_cookie('preview_footer', $domain);

	}

	public function deleteFooter($footer){
		if($footer == $this->utils->getPlayerCenterFooter(false)){
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('footer.template.msg1'));
		} else {
			$file_to_remove = array();

			$file_to_remove[] = $this->footer_php_path.'/'.$this->prefix_footer.$footer.'.php';

			foreach ($file_to_remove as $key => $value) {
				if (file_exists($value)) {
					unlink($value);
	  			}
			}

			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete footer template', "User ".$this->authentication->getUsername()." delete footer template ". $footer );
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('footer.template.msg2'));
		}
	}

	public function setToDefaultFooter() {
		$this->operatorglobalsettings->syncSettingJson("player_center_footer", null, 'value');
		$delete_file = $this->site_path.$this->shtml_footer_filename;
		$rebirt_file = $this->site_path.$this->shtml_footer_bak_filename;
		if (file_exists($delete_file) && file_exists($rebirt_file)) {
			unlink($delete_file);
			rename ($rebirt_file , $delete_file);
		}

	}

	public function transferFileToSiteFooter($footer) {
		if($footer != $this->utils->getPlayerCenterFooter(false)){
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, sprintf(lang('footer.template.msg9'),$footer));
		} else {
			$file_from = $this->footer_php_path.'/'.$this->prefix_footer.$this->utils->getPlayerCenterFooter(false).'.php';
			$file_to = $this->site_path.$this->shtml_footer_filename;
			rename ($file_to , $this->site_path.$this->shtml_footer_bak_filename);
			//$this->utils->debug_log("file tooooo ====>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>".$file_to);
			if(file_exists($file_from)) {
				//render then save
				$rlt=file_put_contents($file_to, $this->utils->renderHeaderTemplate($file_from, true));

				if($rlt) {
					chmod($file_to, 0777);
					chmod($this->site_path.$this->shtml_footer_bak_filename, 0777);
					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, sprintf(lang('template.msg3'),$this->utils->getPlayerCenterFooter(false)));
				} else {
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, sprintf(lang('template.msg4'),$this->utils->getPlayerCenterFooter(false)));
				}
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, sprintf(lang('template.msg2'),$this->utils->getPlayerCenterFooter(false)));
			}
		}
	}

	//downloading template tmpl
	public function downloadHeader($header) {
		if(!empty($header)){
			// Specify file path.
			$path = $this->header_php_path; // '/uplods/'
			$download_file =  $path.'/'.$this->prefix_header.$header.'.php';
			// Check file is exists on given path.

            if(!file_exists($download_file)){
                $download_file = realpath($this->utils->getHeaderTemplateBuiltInPath() . $header . '.tmpl');
            }

			if(file_exists($download_file))
			{
				$data = file_get_contents($download_file); // Read the file's contents
				$name = $header.'.tmpl';

				force_download($name, $data);
			}
			else
			{
			  $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Header not found'));
			}

		}
	}

	public function downloadFooter($footer) {
		if(!empty($footer)){
			// Specify file path.
			$path = $this->footer_php_path; // '/uplods/'
			$download_file =  $path.'/'.$this->prefix_footer.$footer.'.php';
			// Check file is exists on given path.

            if(!file_exists($download_file)){
                $download_file = realpath($this->utils->getFooterTemplateBuiltInPath() . $footer . '.tmpl');
            }

			if(file_exists($download_file))
			{
			  	$data = file_get_contents($download_file); // Read the file's contents
				$name = $footer.'.tmpl';

				force_download($name, $data);
			}
			else
			{
			  $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Header not found'));
			}

		}
	}

		//downloading template tmpl
	public function downloadThemes($themes) {
		if(!empty($themes)){
			// Specify file path.
			$default_path = dirname(__FILE__) . '/../../../player/public/'.$this->utils->getPlayerCenterTemplate().'/styles/';
			$uploaded_path = $this->themes_style_path; // '/uplods/'

			if( file_exists($default_path.'/base-theme-'.$themes.'.css') ) {
				$download_file =  $default_path.'/base-theme-'.$themes.'.css';
			} else {
				$download_file =  $uploaded_path.'/base-theme-'.$themes.'.css';
			}

			// Check file is exists on given path.

			if(file_exists($download_file))
			{
				$data = file_get_contents($download_file); // Read the file's contents
				$name = $themes.'.css';

				force_download($name, $data);
			}
			else
			{
			  $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Themes not found'));
			}

		}
	}

	public function registerIndex($offLiveChat = false) {
		if(!$this->permissions->checkPermissions('theme_management'))
		return $this->error_access();

		$files = glob(realpath($this->register_php_path).'/'.$this->register_footer.'*');
		$registers = array_map(function($file) {
			return preg_replace("#.*".$this->register_footer."([^\.]*)\.php.*#", '$1', $file);
		}, $files);
		//var_dump($files);die();
		$data = array(
			'player_url' => $this->utils->getSystemUrl("player"),
			'selected_registers' => $this->utils->getPlayerCenterRegistration(false),
			'registers' => $registers,
		);
        $data['offChat'] = $offLiveChat;

		if(!empty($this->session->userdata('message'))) {
			$data['alert_message'] = $this->session->userdata('message');
		}

        $this->load->model("static_site");
        $data['logo_icon'] = $this->static_site->getDefaultLogoUrl();
		$this->loadTemplate('Theme Management', '', '', 'theme_management');
		$this->template->write_view('main_content', 'theme_management/register_list', $data);
		$this->template->render();
	}

	public function saveRegistration() {
		$registration = $this->input->post('registration_template');

		$dir = $this->register_php_path.'/'.$this->register_footer. $registration . '.php';
		if (file_exists($dir)) {
			$success = $this->operatorglobalsettings->syncSettingJson("player_center_registration", $registration, 'value');
		} else {
			$message = lang('Registration tempalte not found');
			$success = false;
		}

		if ($success) {
			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Change registration template', "User ".$this->authentication->getUsername()." change registration template from". $this->utils->getPlayerCenterRegistration(false) . "to registration template" .$registration );

			$this->load->helper('cookie');
			$domain = '.' . $this->getMainDomain();
			delete_cookie('preview_registration', $domain);


			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));

		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, !empty($message) ? $message : lang('error.default.db.message'));
		}

		redirect('theme_management/registerIndex');
	}

	public function resetRegistration() {
		$this->load->helper('cookie');
		$domain = '.' . $this->getMainDomain();
		delete_cookie('preview_registration', $domain);

	}

	public function previewRegistration($preview_registration) {
		$this->load->helper('cookie');
		$domain = '.' . $this->getMainDomain();
		set_cookie('preview_registration', $preview_registration, 60, $domain);

		redirect('http://player' . $domain.'/player_center/iframe_register');
	}

	public function otherJsIndex(){
		$files = glob(realpath($this->js_path).'/'.$this->prefix_js.'*');
		$js_files = array_map(function($file) {
			return preg_replace("#.*".$this->prefix_js."([^\.]*)\.js.*#", '$1', $file);
		}, $files);

		$data = array(
			'player_url' => $this->utils->getSystemUrl("player"),
			'selected_footer' => $this->utils->getPlayerCenterFooter(false),
			'files' => $js_files,
		);

		if(!empty($this->session->userdata('message'))) {
			$data['alert_message'] = $this->session->userdata('message');
		}

        $this->load->model("static_site");
        $data['logo_icon'] = $this->static_site->getDefaultLogoUrl();
		$this->loadTemplate('Theme Management', '', '', 'theme_management');
		$this->template->write_view('main_content', 'theme_management/js_list', $data);
		$this->template->render();
	}

	public function upload_new_js(){
		$files = glob(realpath($this->js_path).'/'.$this->prefix_js.'*');
		$js_files = array_map(function($file) {
			return preg_replace("#.*".$this->prefix_js."([^\.]*)\.js.*#", '$1', $file);
		}, $files);

		$file_js = isset($_FILES['file_js']) ? $_FILES['file_js'] : null;

		$js_name = lcfirst(str_replace(' ', '-', $this->input->post('txtName')));
		$file_to_upload = array();

		if(in_array($js_name, $js_files)) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('js.template.msg6'));
		} else {
			$js_path_dir = $this->js_path;

			$js_path = $this->prefix_js.$js_name;

			if(empty($file_js['size'][0]) || empty($js_name)) {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('player.mp14'));
			} else {

				$config_footer_php = array(
		            'allowed_types' => 'js',
		            'max_size'      => $this->utils->getMaxUploadSizeByte(),
		            'overwrite'     => true,
		            'remove_spaces' => true,
		            'upload_path'   => $js_path_dir,
		        );

				$file_to_upload[] = array(
					"file_details" => $file_js,
					"upload_path" => $js_path_dir,
					"config_header" => $config_footer_php,
					"file_name" => $js_path,
				);

		        foreach ($file_to_upload as $key => $value) {
		        	$response = $this->multiple_image_uploader->do_multiple_uploads($value['file_details'], $value['upload_path'], $value['config_header'], $value['file_name']);
			        if($response['status'] == "fail" ) {
			        	$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response['message']);
			        	redirect('theme_management/otherJsIndex');
			        	//die();
			        }
		        }

		        $this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Add javascript template', "User ".$this->authentication->getUsername()." add javascript template ". $js_name );
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('js.template.msg3'));
			}

		}

        redirect('theme_management/otherJsIndex');
	}

	public function downloadJS($file) {
		if(!empty($file)){
			// Specify file path.
			$path = $this->js_path; // '/uplods/'
			$download_file =  $path.'/'.$this->prefix_js.$file.'.js';
			// Check file is exists on given path.

			if(file_exists($download_file))
			{
			  	$data = file_get_contents($download_file); // Read the file's contents
				$name = $file.'.js';

				force_download($name, $data);
			}
			else
			{
			  $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Footer not found'));
			}

		}
	}

	public function deleteJs($file){

			$file_to_remove = array();

			$file_to_remove[] = $this->js_path.'/'.$this->prefix_js.$file.'.js';

			foreach ($file_to_remove as $key => $value) {
				if (file_exists($value)) {
					unlink($value);
	  			}
			}

			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Delete javascript template', "User ".$this->authentication->getUsername()." delete javascript template ". $file );
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('js.template.msg2'));
	}

	public function mobileLoginIndex() {
		if(!$this->permissions->checkPermissions('theme_management'))
		return $this->error_access();

		$files = glob(realpath($this->mobile_login_php_path).'/'.$this->mobile_login.'*');
		$login_mobile = array_map(function($file) {
			return preg_replace("#.*".$this->mobile_login."([^\.]*)\.php.*#", '$1', $file);
		}, $files);
		//var_dump($login_mobile);die();
		$data = array(
			'selected_mobile_login' => $this->utils->getPlayerCenterMobileLogin(false),
			'mobile_login_list' => $login_mobile,
		);

		if(!empty($this->session->userdata('message'))) {
			$data['alert_message'] = $this->session->userdata('message');
		}

        $this->load->model("static_site");
        $data['logo_icon'] = $this->static_site->getDefaultLogoUrl();
		$this->loadTemplate('Theme Management', '', '', 'theme_management');
		$this->template->write_view('main_content', 'theme_management/mobile_login_list', $data);
		$this->template->render();
	}

	public function saveMobileLogin() {
		$registration = $this->input->post('mobile_login_template');

		$dir = $this->mobile_login_php_path.'/'.$this->mobile_login. $registration . '.php';
		if (file_exists($dir)) {
			$success = $this->operatorglobalsettings->syncSettingJson("player_center_mobile_login", $registration, 'value');
		} else {
			$message = lang('Mobile Login tempalte not found');
			$success = false;
		}

		if ($success) {
			$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Change mobile login template', "User ".$this->authentication->getUsername()." change mobile login template from". $this->utils->getPlayerCenterMobileLogin(false) . "to mobile login template" .$registration );

			$this->load->helper('cookie');
			$domain = '.' . $this->getMainDomain();
			delete_cookie('preview_mobile_login', $domain);


			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));

		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, !empty($message) ? $message : lang('error.default.db.message'));
		}

		redirect('theme_management/mobileLoginIndex');
	}

	public function previewMobileLogin($preview_mobile_login) {
		$this->load->helper('cookie');
		$domain = '.' . $this->getMainDomain();
		set_cookie('preview_mobile_login', $preview_mobile_login, 60, $domain);

		redirect($this->utils->getSystemUrl("player").'/player_center/login');
	}

	public function resetMobileLogin() {
		$this->load->helper('cookie');
		$domain = '.' . $this->getMainDomain();
		delete_cookie('preview_mobile_login', $domain);

	}

	public function themeHostIndex() {

		# Get theme color list
		$files_default = glob(dirname(__FILE__) . '/../../../player/public/'.$this->utils->getPlayerCenterTemplate().'/styles/base-theme-*');
		$themes_default = array_map(function($file) {
			return preg_replace("#.*base-theme-([^\.]*)\.css.*#", '$1', $file);
		}, $files_default);

		$files_uploaded = glob($this->themes_style_path.'base-theme-*');
		$themes_uploaded = array_map(function($file) {
			return preg_replace("#.*base-theme-([^\.]*)\.css.*#", '$1', $file);
		}, $files_uploaded);

		$data['themes'] = array_merge($themes_default, $themes_uploaded);

		# Get theme header list
		$files = glob(realpath($this->header_php_path).'/'.$this->prefix_header.'*');
		$headers = array_map(function($file) {
			return preg_replace("#.*".$this->prefix_header."([^\.]*)\.php.*#", '$1', $file);
		}, $files);

		$headers[] = 'lottery';
		$data['headers'] = $headers;

		# Get theme footer list
		$files = glob(realpath($this->footer_php_path).'/'.$this->prefix_footer.'*');
		$footers = array_map(function($file) {
			return preg_replace("#.*".$this->prefix_footer."([^\.]*)\.php.*#", '$1', $file);
		}, $files);

		$footers[] = 'lottery';
		$data['footers'] = $footers;

		# Get theme Setting list
		$themeList = [];
		$_themeSetting = $this->operatorglobalsettings->getSetting("player_center_theme_host_template");
		if ($_themeSetting) {
			$themeList = json_decode($_themeSetting->template, true);
		}
		$data['themeList'] = $themeList;

		$this->loadTemplate('Theme Management', '', '', 'theme_management');
		$this->template->write_view('main_content', 'theme_management/view_theme_host_list', $data);
		$this->template->render();
	}

	public function saveThemeHost() {

		if ($_POST) {

			$hostname = $this->input->post('hostname', true);
			$theme_template  = $this->input->post('theme_template', true);
			$header_template = $this->input->post('header_template', true);
			$footer_template = $this->input->post('footer_template', true);
			$custom_css_file = $this->input->post('custom_css_file', true);

			$themesData = [];
			$hostname = ($hostname) ? $hostname : [];

			foreach ($hostname as $key => $val) {
				$themesData[$key]['hostname'] = trim($val);
				$themesData[$key]['theme_template']  = $theme_template[$key];
				$themesData[$key]['header_template'] = $header_template[$key];
				$themesData[$key]['footer_template'] = $footer_template[$key];
				$themesData[$key]['custom_css_file'] = $custom_css_file[$key];
			}

			$this->operatorglobalsettings->syncSettingJson("player_center_theme_host_template", $themesData, 'template');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
			redirect('theme_management/themeHostIndex');
		} else {
			redirect('theme_management/themeHostIndex');
		}
	}
	/**
	 * Used for redirection when permission is disabled
	 *
	 * Created by: Mark Andrew Mendoza (andrew.php.ph)
	 */
	private function error_access() {
		$this->loadTemplate('Theme Management', '', '', 'theme_management');
		$systemUrl = $this->utils->activeSystemSidebar();
		$data['redirect'] = $systemUrl;

		$message = lang('con.i01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}
}

/* End of file notification_management.php */
/* Location: ./application/controllers/notification_management.php */
