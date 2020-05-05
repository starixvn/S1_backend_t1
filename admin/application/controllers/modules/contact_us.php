<?php

/**
 * Class promo_module
 *
 * General behaviors include :
 *
 * * Contact us features
 *
 * @category Player Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait contact_us {

	/**
	 * overview : pre application
	 *
	 * @param string	$promoCode
	 * note 5 requests per minute per ip
	 */
	public function post_contact_us() {
		if ($this->utils->isEnabledFeature('enable_contact_us')) {
			$this->load->model(array('player_contact_us'));

			$post_fields = $this->input->post();
			$other_fields = array();
			$basic_info = array('name','email','subject','message','captcha');

			$name = $this->input->post('name');
			$email = $this->input->post('email');
			$subject = $this->input->post('subject');
			$message = $this->input->post('message');
			$captcha = $this->input->post('captcha');

			if(!$this->check_captcha_contact_us($captcha)){
				if ($this->input->is_ajax_request()) {
					$this->returnJsonResult(array('status' => 'fail', 'msg' => lang('error.captcha')));
					return;
				} else {
					$msgParam = http_build_query(array('status' => 'fail', 'msg' => lang('error.captcha')));
					$redirect_url = $_SERVER["HTTP_REFERER"]."?".$msgParam;
					redirect($redirect_url);
				}
			}

			if(!empty($post_fields)) {
				foreach ($post_fields as $key => $value) {
					if(!in_array($key , $basic_info)){
						$other_fields[$key] = $value;
					}
				}
			}
			
			$data = array(
				'name' 			=> $name,
				'email' 		=> $email,
				'subject' 		=> $subject,
				'message' 		=> $message,
				'other_fields' 	=> json_encode($other_fields),
				'ip' 			=> $this->utils->getIP(),
				'created_at' 	=> $this->utils->getNowForMysql(),
			);

			$this->load->model(array('queue_result'));

			$templateType = 'email_contact_us_template';

			$emailTemplate = $this->email_setting->getEmailTemplatePromo($templateType);

			$sendToEmail = $this->config->item('email_contact_us_send_to');
			$str_replace1 = str_replace("[subject]", $subject, $emailTemplate);
			$str_replace2 = str_replace("[name]", $name, $str_replace1);
			$str_replace3 = str_replace("[email]", $email, $str_replace2);
			$str_replace4 = str_replace("[message]", $message, $str_replace3);

			if (!empty($sendToEmail)) {
				foreach ($sendToEmail as $key => $value) {
					$subject = $this->config->item('email_contact_us_subject').' / '.$subject;

					$body = '<html><body>'. $str_replace4['template'] .'</body></html>';

					$this->load->model(array('queue_result'));

					$token = $this->utils->sendMail($value, null, null, $subject, $body,
						Queue_result::CALLER_TYPE_PLAYER, null);
				}
			} else {
				if ($this->input->is_ajax_request()) {
					$this->returnJsonResult(array('status' => 'fail', 'msg' => lang('No client email set.')));
					return;
				} else {
					$msgParam = http_build_query(array('status' => 'fail', 'msg' => lang('No client email set.')));
					$redirect_url = $_SERVER["HTTP_REFERER"]."?".$msgParam;
					redirect($redirect_url);
				}
			}

			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('status' => 'success', 'msg' => lang('mess.19')));
				return;
			} else {
				$msgParam = http_build_query(array('status' => 'success', 'msg' => lang('mess.19')));
				$redirect_url = $_SERVER["HTTP_REFERER"]."?".$msgParam;
				redirect($redirect_url);
			}
		} else {
			
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('status' => 'fail', 'msg' => lang('Contact us features dissable')));
				return;
			} else {
				$msgParam = http_build_query(array('status' => 'fail', 'msg' => lang('Contact us features dissable')));
				$redirect_url = $_SERVER["HTTP_REFERER"]."?".$msgParam;
				redirect($redirect_url);
			}
		}
	}

	/**
	 * overview : check captcha
	 *
	 * @param string	$val
	 * @return bool
	 */
	public function check_captcha_contact_us($captcha) {
		$this->load->library('captcha/securimage');
	 	$securimage = new Securimage();

	 	$success = $securimage->check($captcha);
		return $success;
	}

}