<?php

Abstract Class Abstract_email_template
{
    protected $user;
    protected $emailIntLang;
    protected $emailLang;
    protected $emailMode;

    abstract protected function replaceRule();

    public function __construct($params)
    {
        $this->CI=& get_instance();
        $this->CI->load->model(['player', 'affiliatemodel', 'agency_model', 'email_template_model', 'operatorglobalsettings']);
        $this->CI->load->library(['language_function']);

        $playerId     = isset($params['player_id'])    ? $params['player_id']    : 0;
        $affiliateId  = isset($params['affiliate_id']) ? $params['affiliate_id'] : 0;
        $agencyId     = isset($params['agency_id'])    ? $params['agency_id']    : 0;
        $templateLang = isset($params['template_lang']) ? $params['template_lang'] : null;
        $templateMode = isset($params['template_mode']) ? $params['template_mode'] : null;
        $this->platform  = $params['platform'];
        $this->template_name = $params['template_name'];

        $this->setUserDetail($playerId, $affiliateId, $agencyId, $this->platform);

        $this->setGlobalLang($templateLang);
        $this->setGlobalMode($templateMode);
    }

    public function setGlobalLang($templateLang = null)
    {
        if (!is_null($templateLang)) {
            if (is_numeric($templateLang)) {
                $this->emailLang    = $this->CI->language_function->getLanguage($templateLang);
                $this->emailIntLang = $this->CI->language_function->getIntLanguage($this->emailLang);
            } else {
                $this->emailIntLang = $this->CI->language_function->getIntLanguage($templateLang);
                $this->emailLang    = $this->CI->language_function->getLanguage($this->emailIntLang);
            }
            return;
        }

        $emailIntLang = 1; // english
        $operatorSendLang = $this->CI->operatorglobalsettings->getSettingJson('admin_email_template_set_send_lang');

        if (isset($operatorSendLang[0])) {
            $operator_item = $operatorSendLang[0];

            # Choose Default Language
            if ($operator_item == 1) {
                $emailIntLang = $operatorSendLang[1];
            }

            # Choose Player Language
            if ($operator_item == 2) {
                $userLang = isset($this->user['language']) ? $this->user['language'] : '';
                $emailIntLang  = $this->CI->language_function->getIntLanguage(strtolower($userLang));
            }
        }

        $this->emailIntLang = (int) $emailIntLang;
        $this->emailLang    = $this->CI->language_function->getLanguage($emailIntLang);
        return;
    }

    public function setGlobalMode($templateMode = null)
    {
        if (!is_null($templateMode)) {
            if (is_numeric($templateMode)) {
                $this->email_mode = ($templateMode == 2) ? 'text' : 'html';
            } else {
                $this->email_mode = $templateMode;
            }
            return;
        }
        $operatorSendMode = $this->CI->operatorglobalsettings->getSettingValue('admin_email_template_set_send_content_mode');
        $this->email_mode = ($operatorSendMode == 2) ? 'text' : 'html';
        return;
    }

    private function setUserDetail($playerId, $affiliateId, $agencyId, $type)
    {
        if ($type == 'player') {
            $this->user = $this->CI->player->getPlayerById($playerId);
        }

        if ($type == 'affiliate') {
            $this->user = $this->CI->affiliatemodel->getAffiliateById($affiliateId);
        }

        if ($type == 'agency') {
            $this->user = $this->CI->agency_model->get_agent_by_id($agencyId);
        }
    }

    private function replaceEmailContent($content, $is_formal)
    {
        $replaceElement = $this->replaceRule();

        foreach ($replaceElement as $element => $rule) {
            if (!$is_formal && !$rule['preview']) {
                continue;
            }
            if (isset($rule['callback']) && method_exists($this, $rule['callback'])) {
                $method = $rule['callback'];
                $affiliateUsername = $this->$method();
                $content = str_replace($element, $affiliateUsername, $content);
                // $content = str_replace($element, $this->$rule['callback'](), $content);
            }
        }

        return $content;
    }

    private function getEmailField($is_formal)
    {
        $field = $this->getEmailScript();

        $field['mail_content'] = $this->replaceEmailContent($field['mail_content'], $is_formal);
        $field['mail_plain_content'] = $this->replaceEmailContent($field['mail_plain_content'], $is_formal);

        return $field;
    }

    public function getEmailScript()
    {
        $platformTypeList = array_flip($this->CI->email_template_model->getPlatformType());

        $email = [];
        $emailList = $this->CI->email_template_model->getTemplateListByName($this->template_name);
        foreach ($emailList as $row) {
            if ($row['template_lang'] == $this->emailIntLang) {
                $email = $row;
            }
        }

        $mailSubject = (isset($email['mail_subject'])) ? $email['mail_subject'] : '';
        $mailContent = (isset($email['mail_content'])) ? $email['mail_content'] : '';
        $mailPlainContent = (isset($email['mail_content_text'])) ? $email['mail_content_text'] : '';

        if (!trim($mailSubject) || !trim($mailContent) || !trim($mailPlainContent) ) {
            $emailScript = [];
            $emailSampleFileName = "email_template_{$this->platform}_sample.php";
            $emailSampleAbsolutePath = dirname(__FILE__) . "/../email_template_sample/$emailSampleFileName";
            if (file_exists($emailSampleAbsolutePath)) {
                $emailScript = include($emailSampleAbsolutePath);
            }

            if ($emailScript && isset($emailScript[$this->template_name]['english'])) {
                $defaultSubject = isset($emailScript[$this->template_name]['english']['subject']) ? $emailScript[$this->template_name]['english']['subject'] : '' ;
                $defaultContent = isset($emailScript[$this->template_name]['english']['content']) ? $emailScript[$this->template_name]['english']['content'] : '' ;
                $emailSampleSubject = isset($emailScript[$this->template_name][$this->emailLang]['subject']) ? $emailScript[$this->template_name][$this->emailLang]['subject'] : $defaultSubject;
                $emailSampleContent = isset($emailScript[$this->template_name][$this->emailLang]['content']) ? $emailScript[$this->template_name][$this->emailLang]['content'] : $defaultContent;
            }

            $mailSubject = (trim($mailSubject)) ? $mailSubject : (($emailSampleSubject) ? $emailSampleSubject : '');
            $mailContent = (trim($mailContent)) ? $mailContent : (($emailSampleContent) ? nl2br($emailSampleContent) : '');
            $mailPlainContent = (trim($mailPlainContent)) ? $mailPlainContent : $emailSampleContent;
        }

        return [
            'mail_subject' => $mailSubject,
            'mail_content' => $mailContent,
            'mail_plain_content' => $mailPlainContent
        ];
    }

    public function getElement()
    {
        return array_keys($this->replaceRule());
    }

    public function getIsEnableByTemplateName($for_playercenter = false){
        $result = array('enable' => false, 'message' => null);

        $templateName = $this->template_name;
        $isEnable = $this->CI->email_template_model->getIsEnableByTemplateName($templateName);
        $result['enable'] = $isEnable;
        $this->CI->utils->debug_log("getIsEnableByTemplateName [$templateName] isEnable: [$isEnable]");

        if(!$isEnable){
            if($for_playercenter){
                $result['message'] = lang('email_template.disabled.player_center.message');
            }
            else{
                $result['message'] = sprintf(lang('email.template.disabled'), lang('email_template_name_'.$templateName));
            }
        }
        return $result;
    }

    public function sendingEmail($email_address, $callerType, $caller, $is_formal = true){
        $this->CI->load->library(['email_manager', 'lib_queue']);
        $mail = $this->getEmailField($is_formal);

        $subject = $mail['mail_subject'];
        $content = $mail['mail_content'];
        $plainContent = $mail['mail_plain_content'];

        try {
            # Detect frequency of email from this caller
            $cacheKey = "$callerType;$caller:LastEmailTime";
            $lastEmailTime = $this->CI->utils->readRedis($cacheKey);
            $emailCooldownTime = $this->CI->config->item('email_cooldown_time');

            if(!empty($emailCooldownTime) && $emailCooldownTime > 0)
            {
                if($lastEmailTime && time() - $lastEmailTime <= $emailCooldownTime) {
                    $this->CI->utils->error_log("[$cacheKey] This user is sending email too frequently.( emailCooldownTime = $emailCooldownTime ) Drop email request to [$email_address] titled [$subject].");
                    return null;
                }
            }

            $this->CI->utils->writeRedis($cacheKey, time());
            return $this->CI->lib_queue->addNewEmailJob($email_address, $subject, $content, $plainContent, $this->email_mode, $callerType, $caller, null);
        } catch (Exception $e) {
            $this->error_log('send mail error', $e);
        }
    }
}