<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_email_setting extends BaseTesting {

	public function init() {
		$this->load->library(array('email_setting', 'PHPMailer/phpmailer'));
		$this->load->helper('string');
	}

	public function testAll() {
		$this->init();
		$this->testSampleSendEmail();
	}

	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	private function testSampleSendEmail() {
		$this->utils->debug_log('testSampleSendEmail',
			$this->email_setting->sendEmail('james@smartbackend.com', array(
				'from_name' => 'Admin',
				'subject' => 'sample testing ' . random_string('alnum'),
				'body' => 'testing ' . random_string('alnum'),
			))
		);
	}

	private function testSendEmail() {
		$mail = new PHPMailer();

		$mail->SMTPDebug = 3;
		$mail->isSMTP(); // Set mailer to use SMTP
		$mail->Host = $this->config->item('mail_smtp_server'); // Specify main and backup SMTP servers
		$mail->SMTPAuth = $this->config->item('mail_smtp_auth'); // Enable SMTP authentication
		$mail->SMTPSecure = $this->config->item('mail_smtp_secure'); // Enable TLS encryption, `ssl` also accepted
		$mail->Port = $this->config->item('mail_smtp_port');

		if ($this->config->item('disable_smtp_ssl_verify')) {
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true,
				),
			);
		}
		// $mail->SetLanguage('en', dirname(__FILE__) . '/PHPMailer/language/');
		$mail->Username = $this->config->item('mail_smtp_username');
		$mail->Password = $this->config->item('mail_smtp_password');

		// $mail->From = 'noreply@mail.shengshunjiakaow.com';
		$mail->From = $this->config->item('mail_from');
		$mail->FromName = 'Testing';

		$mail->Subject = 'Testing' . random_string('unique');

		$mail->WordWrap = 50;

		$mail->MsgHTML("Testing email sending..." . random_string('unique'));

		$mail->AddAddress('zhucedongxi@yahoo.com', 'magicgod');
		// $mail->AddAddress('noreply@mail.shengshunjiakaow.com', '');

		$mail->CharSet = "UTF-8";
		$mail->Encoding = "8bit";

		// $mail->SMTPDebug = 1;

		$mail->IsHTML(true);

		if (!$mail->Send()) {
			log_message('debug', $mail->ErrorInfo);
			$success = false;
		} else {
			log_message('debug', "sent email");
			$success = true;
		}
		$this->test($success, true, 'test send email');
	}
}
