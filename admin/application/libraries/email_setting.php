<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Email
 *
 * @package		Email
 * @author		Raihana Dandamun
 * @version		1.0.0
 */

class Email_setting {

	private $playerId = null;
	private $verify = null;

	public function __construct() {
		$this->ci = &get_instance();
		// $this->ci->load->library(array('email', 'PHPMailer/phpmailer'));
		$this->ci->load->model(array('player'));
	}

	/**
	 * Will send an email to player
	 *
	 * @param 	string
	 * @param 	array
	 * @return	array
	 */
	public function sendEmail($toEmail, $data, $config = null) { //var_dump($data,$config); exit();

		$this->ci->load->model(array('operatorglobalsettings'));

        if($this->ci->utils->isEnabledMDB()){
            $this->ci->utils->deleteCache(); // clear for $mail::properties from operatorglobalsettings::getSettingValue().
        }

        if (file_exists(APPPATH . "libraries/vendor/phpmailer/phpmailer/PHPMailerAutoload.php")) {
            require_once APPPATH . "libraries/vendor/phpmailer/phpmailer/PHPMailerAutoload.php";
            $mail = new PHPMailer();
        } else {
            require_once APPPATH . "libraries/vendor/autoload.php";
            $mail = new PHPMailer\PHPMailer\PHPMailer;
		}

		// $mail->SMTPDebug = 3;
		$mail->isSMTP(); // Set mailer to use SMTP
		$mail->Host = isset($config['mail_smtp_server']) ? $config['mail_smtp_server'] : $this->ci->operatorglobalsettings->getSettingValue('mail_smtp_server'); // Specify main and backup SMTP servers
		$mail->SMTPAuth = isset($config['mail_smtp_auth']) ? $config['mail_smtp_auth'] : $this->ci->operatorglobalsettings->getSettingValue('mail_smtp_auth'); // Enable SMTP authentication
		$mail->SMTPSecure = isset($config['mail_smtp_secure']) ? $config['mail_smtp_secure'] : $this->ci->operatorglobalsettings->getSettingValue('mail_smtp_secure'); // Enable TLS encryption, `ssl` also accepted
		$mail->Port = isset($config['mail_smtp_port']) ? $config['mail_smtp_port'] : $this->ci->operatorglobalsettings->getSettingValue('mail_smtp_port');

		$mail->Helo = $this->ci->utils->getConfig('mail_smtp_hostname');

		$from = isset($data['from']) && !empty($data['from']) ? $data['from'] : $this->ci->operatorglobalsettings->getSettingValue('mail_from_email');
		$fromName = isset($data['from_name']) && !empty($data['from_name']) ? $data['from_name'] : $this->ci->operatorglobalsettings->getSettingValue('mail_from');
		$mail->setFrom($from, $fromName);

		$this->ci->utils->debug_log('sendEmail from', $from, 'from_name', $fromName, 'to', $toEmail);
		// $mail->Username = $data['email_sender'];
		// $mail->Password = $data['email_sender_pass'];

		if (isset($config['disable_smtp_ssl_verify']) ? $config['disable_smtp_ssl_verify'] : $this->ci->operatorglobalsettings->getSettingValue('disable_smtp_ssl_verify')) {

			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true,
				),
			);
		}

		$print_debug=true;
		if(isset($config['print_debug'])){
			$print_debug=$config['print_debug'];
		}
		if(@$config['is_debug']){
			$print_debug=$config['is_debug'];
		}
		$mail->SMTPDebug = $print_debug;
		$mail->Username = isset($config['mail_smtp_username']) ? $config['mail_smtp_username'] : $this->ci->operatorglobalsettings->getSettingValue('mail_smtp_username');
		$mail->Password = isset($config['mail_smtp_password']) ? $config['mail_smtp_password'] : $this->ci->operatorglobalsettings->getSettingValue('mail_smtp_password');

		// $mail->WordWrap = 50;

		$mail->isHTML(true);
		$mail->Subject = $data['subject'];
		$mail->Body = $data['body'];
		$mail->addAddress($toEmail);

		$mail->CharSet = "UTF-8";
		$mail->Encoding = "8bit";
		$this->ci->utils->debug_log('######################################    MAILING RESULT            ##########################################################################', $mail);
		// $mail->IsHTML(true);
		// This SMTPDebug is used  to prevent debuglogs from php mailer to appear
		// $mail->SMTPDebug = false;
        // $mail->do_debug = 0;

		if (!$mail->send()) {
			$this->ci->utils->debug_log('######################################    MAILING ERROR INFO           ##########################################################################', $mail->ErrorInfo);
			return $mail->ErrorInfo;
		} else {
			return true;
		}
	}

	/**
	 * Check if player's verificationStatus is active
	 *
	 * @param 	int
	 * @return	array
	 */
	public function verifyEmail($playerId) {
		return $this->ci->player->getPlayerVerifiedEmail($playerId);
	}

	/**
	 * Update player's verificationStatus
	 *
	 * @param 	int
	 * @return	array
	 */
	public function editVerificationStatus($playerId, $data) {
		return $this->ci->player->editVerificationStatus($playerId, $data);
	}

	/**
	 * Get email template for promotion
	 *
	 * @param 	int
	 * @return	array
	 */
	public function getEmailTemplatePromo($name) {
		return $this->ci->player->getEmailTemplatePromo($name);
	}

	/**
	 * Will send an email to player
	 *
	 * @param 	string
	 * @param 	array
	 * @return	array
	 */
	public function testSendEmail() {

		// $mail = new PHPMailer();

		// $mail->SMTPDebug = 3;
		// $mail->isSMTP(); // Set mailer to use SMTP
		// $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
		// $mail->SMTPAuth = true; // Enable SMTP authentication
		// $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
		// $mail->Port = 587;

		// // $mail->SetLanguage('en', dirname(__FILE__) . '/PHPMailer/language/');
		// $mail->Username = '<your gmail>';
		// $mail->Password = '<your password>';

		// $mail->From = '<your gmail>';
		// $mail->FromName = 'Testing';

		// $mail->Subject = 'Testing';

		// $mail->WordWrap = 50;

		// $mail->MsgHTML("Testing email sending...");
		// $mail->AddAddress('<target gmail>', '');

		// $mail->CharSet = "UTF-8";
		// $mail->Encoding = "8bit";

		// // $mail->SMTPDebug = 1;

		// $mail->IsHTML(true);

		// if (!$mail->Send()) {
		// 	log_message('debug', $mail->ErrorInfo);
		// 	return false;
		// } else {
		// 	log_message('debug', "sent email");
		// 	return true;
		// }
	}

}