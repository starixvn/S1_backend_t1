<?php
/**
 * player_message_library.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 *
 * @property BaseController $CI
 * @property Internal_message $internal_message
 * @property CI_Form_validation $form_validation
 */
class Player_message_library {
    const VALIDATION_TYPE_ADD = 'add';
    const VALIDATION_TYPE_REPLY = 'reply';

    /* @var BaseController */
    public $CI;

    protected $_player_center_title = NULL;
    protected $_system_feature_hidden_msg_sender_from_sysadmin = FALSE;
    protected $_request_default_guest_name = NULL;

    public function __construct(){
        $this->CI =& get_instance();

        $this->CI->load->model(['internal_message']);
        $this->CI->load->library(['form_validation']);

        $this->internal_message = $this->CI->internal_message;
        $this->form_validation = $this->CI->form_validation;

        $this->_player_center_title = $this->CI->utils->getPlayertitle();
        $this->_system_feature_hidden_msg_sender_from_sysadmin = $this->CI->utils->isEnabledFeature('hidden_msg_sender_from_sysadmin');
        $this->_request_default_guest_name = lang('message.request_form.default_guest_name');
    }

    public function getDefaultAdminSenderName(){
        $value = $this->CI->operatorglobalsettings->getSettingValue('default_message_admin_sender_name');

        return (empty($value)) ? $this->_player_center_title : $value;
    }

    public function getDefaultAdminSendMessageLengthLimit(){
        $value = (int)$this->CI->operatorglobalsettings->getSettingIntValue('admin_send_message_length_limit');

        return (empty($value)) ? 0 : $value;
    }

    public function getDefaultPlayerSendMessageLengthLimit(){
        $value = (int)$this->CI->operatorglobalsettings->getSettingIntValue('player_send_message_length_limit');

        return (empty($value)) ? 0 : $value;
    }

    public function getPlayerAllMessages($player_id, $filter){
        $filter .= 'c.deleted = 0';
        return $this->internal_message->getMessages($player_id, null, null, $filter);
    }

    public function getPlayerUnreadMessages($player_id){
        return $this->internal_message->playerUnreadMessages($player_id);
    }

    public function run_validation($type){
        switch($type){
            case self::VALIDATION_TYPE_REPLY:
                $this->form_validation->set_rules('message', lang('cs.messagedetail'), 'trim|required|xss_clean');
                break;
            case self::VALIDATION_TYPE_ADD:
            default:
                $this->form_validation->set_rules('subject', lang('cs.subject'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('message', lang('cs.messagedetail'), 'trim|required|xss_clean');
        }

        return $this->form_validation->run();
    }

    public function addMessage($player_id, $player_name, $subject, $message, $save_to_drafts = FALSE){
        $result = [
            'status' => FALSE,
            'message' => NULL
        ];

        if(!$this->checkMessageLength($message, $this->getDefaultPlayerSendMessageLengthLimit())){
            $result['status'] = FALSE;
            $result['message'] = sprintf(lang('form.validation.max_length'), lang('cs.messagedetail'), $this->getDefaultPlayerSendMessageLengthLimit());
            return $result;
        }
        $result['status'] = $this->internal_message->addNewMessage($player_id, $subject, $player_name, $message, $save_to_drafts);

        return $result;
    }

    public function addRequestForm($player_id = NULL, $player_name, $real_name = NULL, $useranme = NULL, $contact_number = NULL, $email = NULL){
        $field_name_first_name = lang('First Name');
        $field_name_username = lang('Username');
        $field_name_contact_number = lang('Contact Number');
        $field_name_email = lang('Email Address');

        $message = <<<EOD
${field_name_first_name}: ${real_name}<br />
${field_name_username}: ${useranme}<br />
${field_name_contact_number}: ${contact_number}<br />
${field_name_email}: ${email}
EOD;

        $result = [
            'status' => FALSE,
            'message' => NULL
        ];

        $result['status'] = $this->internal_message->addRequestFormMessage($player_id, $player_name, $message);

        return $result;
    }

    protected function _process_message_entry($message){
        if($message['flag'] == 'admin'){
            $message['sender'] = $message['adminUsername'];
            $message['sender'] = (!empty($message['admin_custom_name'])) ? $message['admin_custom_name'] : $message['sender'];
            if(!$this->CI->utils->isAdminSubProject()){
                $message['sender'] = ($this->_system_feature_hidden_msg_sender_from_sysadmin) ? $this->getDefaultAdminSenderName() : $message['sender'];
            }
            if($message['message_type'] == Internal_message::MESSAGE_TYPE_REQUEST_FORM){
                $message['recipient'] = (empty($message['playerId'])) ? $this->_request_default_guest_name : $message['playerUsername'];
            }else{
                $message['recipient'] = $message['playerUsername'];
            }
        }else{
            if($message['message_type'] == Internal_message::MESSAGE_TYPE_REQUEST_FORM){
                $message['sender'] = (empty($message['playerId'])) ? $this->_request_default_guest_name : $message['playerUsername'];
            }else{
                $message['sender'] = $message['playerUsername'];
            }
            $message['recipient'] = $message['adminUsername'];
            $message['recipient'] = (!empty($message['admin_custom_name'])) ? $message['admin_custom_name'] : $message['recipient'];
            if(!$this->CI->utils->isAdminSubProject()){
                $message['recipient'] = ($this->_system_feature_hidden_msg_sender_from_sysadmin) ? $this->getDefaultAdminSenderName() : $message['recipient'];
            }
        }
        $message['detail'] = htmlspecialchars_decode($message['detail']);

        return $message;
    }

    public function getMessageById($message_id, $player_id = NULL){
        $messages = $this->internal_message->getMessageById($message_id, $player_id);

        if(empty($messages)){
            return FALSE;
        }

        foreach($messages as $key => $entry){
            $messages[$key] = $this->_process_message_entry($entry);
        }

        $topic = $messages[0];

        $results = [];
        $results['topic'] = $topic;
        $results['messages'] = $messages;
        $results['flags'] = [
            'is_new' => $topic['status'] == Internal_message::STATUS_NEW,
            'is_admin_new' => $topic['status'] == Internal_message::STATUS_ADMIN_NEW,
            'is_disabled' => $topic['status'] == Internal_message::STATUS_DISABLED,
            'is_disabled_reply' => $topic['status'] == Internal_message::STATUS_DISABLED || !!$topic['disabled_replay'],
            'is_deleted' => !!$topic['deleted'],
            'is_system_message' => !!$topic['is_system_message'],
        ];

        return $results;
    }

    public function getMessageByIdForPlayer($player_id, $message_id){
        $data = $this->getMessageById($message_id, $player_id);

        if(empty($data)){
            return FALSE;
        }

        if(!$data['flags']['is_new'] && !$data['flags']['is_disabled'] && !$data['flags']['is_deleted']){
            $this->updateMessageStatus($message_id, 'admin', Internal_message::STATUS_READ, Internal_message::MESSAGE_DETAILS_READ); //update message as read
        }else{
            $this->updateMessageStatus($message_id, 'admin', NULL, Internal_message::MESSAGE_DETAILS_READ); //update message as read
        }

        $total_unreads = $this->internal_message->countTotalUnreadByMessageId($message_id);

        $this->internal_message->updateMessages([
            'admin_unread_count' => $total_unreads['admin'],
            'player_unread_count' => $total_unreads['player'],
        ], $message_id);

        return $data;
    }

    public function getMessageByIdForAdmin($message_id){
        $data = $this->getMessageById($message_id);

        if(empty($data)){
            return FALSE;
        }

        $this->updateMessageStatus($message_id, 'player', NULL, Internal_message::MESSAGE_DETAILS_READ); //update message as read

        $total_unreads = $this->internal_message->countTotalUnreadByMessageId($message_id);

        $this->internal_message->updateMessages([
            'admin_unread_count' => $total_unreads['admin'],
            'player_unread_count' => $total_unreads['player'],
        ], $message_id);

        return $data;
    }

    public function replyMessage($message_id, $player_name, $message){
        $result = [
            'status' => FALSE,
            'message' => NULL
        ];

        $data = $this->getMessageByIdForAdmin($message_id);

        if(empty($data)){
            $result['message'] = lang('error.default.db.message');
            return $result;
        }

        if($data['flags']['is_deleted']){
            $result['message'] = lang('mess.20');
            return $result;
        }

        if($data['flags']['is_disabled_reply']){
            $result['message'] = lang('mess.20');
            return $result;
        }

        if(!$this->checkMessageLength($message, $this->getDefaultPlayerSendMessageLengthLimit())){
            $result['message'] = sprintf(lang('form.validation.max_length'), lang('cs.messagedetail'), $this->getDefaultPlayerSendMessageLengthLimit());
            return $result;
        }

        $this->internal_message->startTrans();

        $message_details_id = $this->internal_message->addNewMessageDetail($message_id, $player_name, $message);

        if(empty($message_details_id)){
            $result['message'] = lang('error.default.db.message');
            return $result;
        }

        if(!$data['flags']['is_new']){
            $this->updateMessageStatus($message_id, 'admin', Internal_message::STATUS_UNPROCESSED, Internal_message::MESSAGE_DETAILS_READ); //update message as read
        }

        $total_unreads = $this->internal_message->countTotalUnreadByMessageId($message_id);

        $this->internal_message->updateMessages([
            'player_last_reply_dt' => $this->CI->utils->getNowForMysql(),
            'admin_unread_count' => $total_unreads['admin'],
            'player_unread_count' => $total_unreads['player'],
        ], $message_id);

        if(!$this->internal_message->endTransWithSucc()){
            $result['message'] = lang('error.default.db.message');

            return $result;
        }

        $result['status'] = TRUE;

        return $result;
    }

    public function replyMessageWithAdmin($message_id, $adminId, $username, $message){
        $result = [
            'status' => FALSE,
            'message' => NULL
        ];

        $data = $this->getMessageByIdForAdmin($message_id);

        if(empty($data)){
            $result['message'] = lang('error.default.db.message');

            return $result;
        }

        $this->internal_message->startTrans();

        $message_details_id = $this->internal_message->addNewMessageDetailWithAdmin($message_id, $adminId, $username, $message);

        if(empty($message_details_id)){
            $this->internal_message->rollbackTrans();
            return FALSE;
        }

        if(!$data['flags']['is_disabled'] && !$data['flags']['is_deleted']){
            $this->updateMessageStatus($message_id, 'player', Internal_message::STATUS_PROCESSED, Internal_message::MESSAGE_DETAILS_READ); //update message as read
        }

        $total_unreads = $this->internal_message->countTotalUnreadByMessageId($message_id);

        $this->internal_message->updateMessages([
            'admin_last_reply_id' => $adminId,
            'admin_last_reply_dt' => $this->CI->utils->getNowForMysql(),
            'admin_unread_count' => $total_unreads['admin'],
            'player_unread_count' => $total_unreads['player'],
        ], $message_id);

        if(!$this->internal_message->endTransWithSucc()){
            $result['message'] = lang('error.default.db.message');

            return $result;
        }

        $result['status'] = TRUE;

        return $result;
    }

    public function updateMessageStatus($message_id, $flag, $status, $detail_status, $player_id = NULL){
        return $this->internal_message->updateMessageStatus($message_id, $flag, $status, $detail_status, $player_id);
    }

    public function checkMessageLength($content, $limit_length){
        // $content = filter_var($content, FILTER_SANITIZE_STRING);
        $strlen = mb_strlen(strip_tags($content));

        if(empty($limit_length)){
            return TRUE;
        }

        return ($strlen > 0 && $strlen <= $limit_length);
    }

    public function getRequestFormSettings(){
        $request_form_settings = $this->CI->operatorglobalsettings->getPlayerMessageRequestFormSettings();
        $request_form_settings['request_form_url'] = $this->CI->utils->getSystemUrl("player", '/api/submit_request_form');

        $request_form_rules = $this->CI->config->item('player_validator');

        $request_form_rules['username']['regex'] = $this->CI->utils->getUsernameRegForJS();
        $contact_number_regex = $this->CI->utils->getConfig('register_mobile_number_regex');
        if(isset($request_form_rules['contact_number']) && !empty($contact_number_regex)){
            $request_form_rules['contact_number']['regex'] = $contact_number_regex;
        }

        $request_form_settings['request_form_rules'] = $request_form_rules;

        return $request_form_settings;
    }
}