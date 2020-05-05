<?php


Class email_manager
{
    public function __construct()
    {
        $this->CI=& get_instance();
        $this->CI->load->model(['email_template_model', 'queue_result']);
        $this->CI->load->library(['language_function', 'utils']);
    }

    public function template($platform, $template_name, $params = [])
    {
        $params['platform'] = $platform;
        $params['template_name'] = $template_name;

        $template_class = "email_template_$template_name";
        $this->CI->load->library("email_manager/email_template/$platform/$template_class", $params);

        return $this->CI->$template_class;
    }

    # Create Template In DB
    public function createTemplate($platformType, $templateType, $templateName)
    {
        $platformTypeList = $this->CI->email_template_model->getPlatformType();
        $templateTypeList = $this->CI->email_template_model->getTemplateType();

        if (array_key_exists($platformType, $platformTypeList) && array_key_exists($templateType, $templateTypeList)) {
            $data['template_lang'] = 1;
            $data['template_name'] = $templateName;
            $data['template_type'] = $templateType;
            $data['platform_type'] = $platformType;
            $data['createdBy'] = 1;

            $this->CI->email_template_model->insertData($data);
        }
    }

    public function sendEmail($toEmail, $data)
    {
        $this->CI->load->model(array('operatorglobalsettings'));

        if($this->CI->utils->isEnabledMDB()){
            $this->CI->utils->deleteCache(); // clear for $mail::properties from operatorglobalsettings::getSettingValue().
        }

        if(file_exists(APPPATH . "libraries/vendor/phpmailer/phpmailer/PHPMailerAutoload.php")) {
            require_once APPPATH . "libraries/vendor/phpmailer/phpmailer/PHPMailerAutoload.php";
            $mail = new PHPMailer();
        } else {
            require_once APPPATH . "libraries/vendor/autoload.php";
            $mail = new PHPMailer\PHPMailer\PHPMailer;
        }

        $mail->isSMTP();
        $mail->Host       = $this->CI->operatorglobalsettings->getSettingValue('mail_smtp_server');
        $mail->SMTPAuth   = $this->CI->operatorglobalsettings->getSettingValue('mail_smtp_auth');
        $mail->SMTPSecure = $this->CI->operatorglobalsettings->getSettingValue('mail_smtp_secure');
        $mail->Port       = $this->CI->operatorglobalsettings->getSettingValue('mail_smtp_port');
        $mail->Username   = $this->CI->operatorglobalsettings->getSettingValue('mail_smtp_username');
        $mail->Password   = $this->CI->operatorglobalsettings->getSettingValue('mail_smtp_password');

        if ($this->CI->operatorglobalsettings->getSettingValue('disable_smtp_ssl_verify')) {
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ),
            );
        }

        if(!isset($data['body_mode']) || empty($data['body_mode'])){
            $operatorSendMode = $this->CI->operatorglobalsettings->getSettingValue('admin_email_template_set_send_content_mode');
            $data['body_mode'] = ($operatorSendMode == 2) ? 'text' : 'html';
        }

        $isHtml = ($data['body_mode'] == 'text') ? false : true;
        $mail->isHTML($isHtml);
        $mail->addAddress($toEmail);
        $mail->SMTPDebug = true;
        $mail->Subject   = $data['subject'];
        $mail->Body      = ($isHtml) ? $data['body'] : $data['body_text'];
        $mail->CharSet   = "UTF-8";
        $mail->Encoding  = "8bit";

        $from     = $this->CI->operatorglobalsettings->getSettingValue('mail_from_email');
        $fromName = $this->CI->operatorglobalsettings->getSettingValue('mail_from');
        $mail->setFrom($from, $fromName);

        $this->CI->utils->debug_log('sendEmail from', $from, 'from_name', $fromName, 'to', $toEmail);

        $this->CI->utils->debug_log('########### MAILING RESULT ###########', $mail);
        if (!$mail->send()) {
            $this->CI->utils->debug_log('########### MAILING ERROR INFO ###########', $mail->ErrorInfo);
            return $mail->ErrorInfo;
        } else {
            return true;
        }
    } // EOF sendEmail
}