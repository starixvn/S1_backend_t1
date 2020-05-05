<?php

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * General behaviors include:
 * * Loads Template
 * * Displays Messages
 * * Displays Chat/Message History
 * * Displays Chat details
 * * Reply Message
 * * Delete Message
 * * Provides Live Chat Link
 * * Searches chat/message
 *
 * @see Redirect redirect to affiliate statistics page*
 * @category CS Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 * @property Permissions $permissions
 * @property Player_message_library $player_message_library
 * @property Internal_message $internal_message
 */
class Cs_management extends BaseController {
    const MANAGEMENT_TITLE = 'CS Management';

	const STATUS_NEW = 3;
	const STATUS_READ = 4;

	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library(array('permissions', 'form_validation', 'template', 'cs_manager', 'report_functions', 'pagination','data_tables'));
		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
	}

	/**
	 * overview : template loading
	 *
	 * detail : load all javascript/css resources, customize head contents
	 *
	 * @param string $title
	 * @param string $description
	 * @param string $keywords
	 * @param string $activenav
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);

		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());

		$this->template->add_js('resources/js/cs_management/cs_management.js');
		$this->template->add_js('resources/js/datatables.min.js');
		//$this->template->add_js('resources/js/jquery.dataTables.min.js');
		//$this->template->add_js('resources/js/dataTables.responsive.min.js');

		$this->template->add_css('resources/css/general/style.css');
		$this->template->add_css('resources/css/datatables.min.css');
		//$this->template->add_css('resources/css/jquery.dataTables.css');
		//$this->template->add_css('resources/css/dataTables.responsive.css');
		$this->template->write_view('sidebar', 'cs_management/sidebar');
	}

	/**
	 * overview : error access
	 *
	 * detail : show error message if user can't access the page
	 */
	private function error_access() {
        $this->loadTemplate('CS Management', '', '', 'cs');
        $csUrl = $this->utils->activeCSSidebar();
        $data['redirect'] = $csUrl;

		$message = lang('con.cs01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->write_view('main_content', 'error_page', $data);
		$this->template->render();
	}

	/**
	 * overview : index page for player management
	 *
	 * detail : vipsetting_management/vipGroupSettingList
	 *
	 */
	public function index() {
		if ($this->permissions->checkPermissions('cs')) {
			redirect('cs_management/messages', 'refresh');
		}
	}

	########################################################################################################################################################################################
	########################################################################################################################################################################################
	########################################################################################################################################################################################
	########################################################################################################################################################################################
	########################################################################################################################################################################################
	########################################################################################################################################################################################
	########################################################################################################################################################################################
	########################################################################################################################################################################################
	########################################################################################################################################################################################

    public function messageSetting(){
        if (!$this->permissions->checkPermissions('chat')) {
            return $this->error_access();
        }

        $this->operatorglobalsettings->putSetting('admin_send_message_length_limit', (int)$this->input->post('admin_send_message_length_limit'));
        $this->operatorglobalsettings->putSetting('player_send_message_length_limit', (int)$this->input->post('player_send_message_length_limit'));
        $this->operatorglobalsettings->putSettingJson('player_message_request_form_attributes', $this->input->post('player_message_request_form'), 'template');

        $this->saveAction(static::MANAGEMENT_TITLE, 'Message Setting', "User " . $this->authentication->getUsername() . " update message setting");

        return $this->returnCommon(BaseController::MESSAGE_TYPE_SUCCESS, lang('save.success'));
    }

	/**
	 * overview : message details
	 *
	 * detail : get all admin messages
	 */
	public function messages() {
		if (!$this->permissions->checkPermissions('chat')) {
			$this->error_access();
		} else {
			$user_id = $this->authentication->getUserId();

            $this->load->library(['player_message_library']);

            $data = [];
			$data['date_from'] = $this->utils->get7DaysAgoForMysql() . ' 00:00:00';
			$data['date_to'] = $this->utils->getTodayForMysql() . ' 23:59:59';
			$data['playerUsername'] = NULL;
			$data['adminUserId'] = NULL;
			$data['subject'] = NULL;
			$data['messages'] = NULL;
            $data['default_admin_sender_name'] = $this->player_message_library->getDefaultAdminSenderName();
            $data['player_message_request_form_settings'] = $this->operatorglobalsettings->getPlayerMessageRequestFormSettings();

			$this->loadTemplate('CS Management', '', '', 'cs');
			$this->template->write_view('main_content', 'cs_management/messages', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : view chat details
	 *
	 * details : get message history by message id
	 *
	 * @param int $message_id	message_id
	 */
	public function viewChatDetails($message_id) {
        $this->load->library(['player_message_library']);

		$data = $this->player_message_library->getMessageByIdForAdmin($message_id);

        return $this->returnCommon(BaseController::MESSAGE_TYPE_SUCCESS, NULL, $data);
	}

	/**
	 * overview : view chat history details
	 *
	 * @param int $message_id
	 */
	public function viewChatHistoryDetails($message_id) {
        $this->load->library(['player_message_library']);

        $data['chat_details'] = $this->internal_message->getMessagesHistoryByMessageId($message_id, null, null);

		$this->load->view('cs_management/ajax_messages_history_details', $data);
	}

    /**
     * Delete message setting
     *
     * @param 	int
     * @return	redirect
     */
    public function deleteSelectedMessage() {
        if(!$this->permissions->checkPermissions('delete_messages')){
            return $this->error_access();
        }
        $this->load->library(['player_message_library']);

		$adminUserId = $this->authentication->getUserId();
        $messagecms = $this->input->post('messagecms');

        $data['date_from'] = $this->input->post('date_from_delete');
        $data['date_to'] = $this->input->post('date_to_delete');
        $data['sender'] = $this->input->post('sender_delete');
        $data['subject'] = $this->input->post('subject_delete');
        $data['messages'] = $this->input->post('messages_delete');
		$data['playerUsername'] = NULL;
		$data['adminUserId'] = $adminUserId;
        $data['player_message_request_form_settings'] = $this->operatorglobalsettings->getPlayerMessageRequestFormSettings();

        if (!empty($messagecms)) {
            foreach ($messagecms as $messagecmsId) {
                $this->internal_message->deleteMessageFromCheckbox($adminUserId, $messagecmsId);
            }

            $message = lang('message_checkbox_delete.success');
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
            $this->messagesDeleteRedirect($data);
        } else {
            $message = lang('message_checkbox_delete.failed');
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            $this->messagesDeleteRedirect($data);
        }
    }

	/**
	 * overview : delete message
	 *
	 * @param int $message_id	message_id
	 */
	public function delete($message_id) {
        if(!$this->permissions->checkPermissions('delete_messages')){
            return $this->error_access();
        }
        $this->load->library(['player_message_library']);

        $data['date_from'] = $this->input->post('date_from_delete');
		$data['date_to'] = $this->input->post('date_to_delete');
		$data['sender'] = $this->input->post('sender_delete');
		$data['subject'] = $this->input->post('subject_delete');
		$data['messages'] = $this->input->post('messages_delete');

		$today = date("Y-m-d H:i:s");
		$this->load->model(array('internal_message'));
		$userId = $this->authentication->getUserId();
		$this->internal_message->deleteMessage($message_id, $userId);
		$message = lang('message_checkbox_delete.success');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		$this->messagesDeleteRedirect($data);
	}

	/**
	 * overview : reply to message
	 *
	 * @param int $message_id message id
	 */
	public function reply($message_id) {
        if (!$this->permissions->checkPermissions('send_message_sms')) {
            return $this->error_access();
        }

        $this->load->library(array('player_message_library'));

		$this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean|callback_chkmsg');
        $this->form_validation->set_message('chkmsg', sprintf(lang('form.validation.max_length'), lang('Message'), $this->player_message_library->getDefaultAdminSendMessageLengthLimit()));

        if ($this->form_validation->run() == false) {
			//$this->viewChatDetails($message_id);

			//$message = lang('con.cs03');

            return $this->returnCommon(self::MESSAGE_TYPE_ERROR,  validation_errors());
		}

        $sender = $this->input->post('message-sender');
        $sender = (empty($sender)) ? $this->player_message_library->getDefaultAdminSenderName() : $sender;
        $sender = (empty($sender)) ? $this->authentication->getUsername() : $sender;

        $message = $this->input->post('message', true);

        $result = $this->player_message_library->replyMessageWithAdmin($message_id, $this->authentication->getUserId(), $sender, $message);

        $this->saveAction(static::MANAGEMENT_TITLE, 'Reply Message', "User " . $this->authentication->getUsername() . " has replied to a player");

        if ($result['status']) {
            $status = BaseController::MESSAGE_TYPE_SUCCESS;
            $message = lang('con.cs04');
        } else {
            $status = BaseController::MESSAGE_TYPE_ERROR;
            $message = $result['message'];
        }

        return $this->returnCommon($status,  $message);
	}

	/**
	 *  for OGP-1366
	 *
	 * @return bool
	 */
	 public function chkmsg($str) {
		return $this->player_message_library->checkMessageLength($str, $this->player_message_library->getDefaultAdminSendMessageLengthLimit());
	}

	/**
	 * overview : mark as close
	 *
	 * @param $message_id
	 */
	public function markAsClose($message_id) {
	    if(!$this->permissions->checkPermissions('mark_as_closed_message')){
	        return $this->error_access();
        }

		$this->load->model(array('internal_message'));

		$userId=$this->authentication->getUserId();

		$this->internal_message->startTrans();
		// $messages = array(
		// 	'status' => '2',
		// );
		$this->internal_message->markAsClose($message_id, $userId);

		// $messages = array(
		// 	'status' => '1',
		// );
		// $this->cs_manager->updateMessagesDetails($messages, $message_id);

		$this->saveAction(static::MANAGEMENT_TITLE, 'Close Message', "User " . $this->authentication->getUsername() . " has closed message to a player");

		if ($this->internal_message->endTransWithSucc()) {
            $status = BaseController::MESSAGE_TYPE_SUCCESS;
            $message = lang('con.cs05');
		} else {
            $status = BaseController::MESSAGE_TYPE_ERROR;
            $message = lang('error.default.db.message');
		}

		return $this->returnCommon($status, $message);
	}

	/**
	 * overview : filter messages
	 *
	 * detail : @return json data
	 *
	 * @param null $player_id		player_id
	 */
	public function getMessagesFilter($player_id = null) {

        $this->load->library(['player_message_library']);

        $default_admin_sender_name = $this->player_message_library->getDefaultAdminSenderName();
        $request_default_guest_name = lang('message.request_form.default_guest_name');

		$self = $this;

		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'dt'=>$i++,
				'alias' => 'messageId',
				'select' => 'messages.messageId',
			),
			array(
				'alias' => 'message_type',
				'select' => 'messages.message_type',
			),
			array(
				'dt'=>$i++,
				'alias' => 'messageId',
				'select' => 'messages.messageId',
				'formatter'=>function ($d,$row) {
                    return '
                        <td style="text-align:center;">
                        <input type="checkbox" data-checked-all-for="checkWhite" class="checkWhite" id="'.$row['messageId'].'" name="messagecms[]" value="'.$row['messageId'].'" onclick="uncheckAll(this.id)" data-player-id="' . $row['playerId']. '" data-player-name="' . $row['playerUsername'] .'" />
                        </td>';
                }
            ),
			array(
				'alias' => 'playerId',
				'select' => 'messages.playerId',
			),
			array(
				'alias' => 'playerUsername',
				'select' => 'player.username',
			),
			array(
				'alias' => 'adminId',
				'select' => 'messagesdetails.adminId',
			),
			array(
				'alias' => 'flag',
				'select' => 'messagesdetails.flag',
			),
			array(
				'alias' => 'affiliates_username',
				'select' => 'receiver_affiliates.username',
			),
            array(
                'dt'=>$i++,
                'alias' => 'sender',
                'select' => 'messagesdetails.sender',
                'formatter' => function($d, $row) use ($self, $default_admin_sender_name, $request_default_guest_name){
                    $sender_name_format = '<a target="_blank" href="/player_management/userInformation/%1$s" data-toggle="sbe-player-helper-menu" data-player-id="%1$s" data-player-name="%2$s">%3$s</a>';
                    if($row['message_type'] == Internal_message::MESSAGE_TYPE_REQUEST_FORM){
                        if($row['flag'] === 'admin'){
                            return $default_admin_sender_name;
                        }else{
                            if(empty($row['playerId'])){
                                return $request_default_guest_name;
                            }else{
                                return sprintf($sender_name_format, $row['playerId'], $d, (!empty($row['affiliates_username'])) ? $d . ' (' . $row['affiliates_username'] . ')' : $d);
                            }
                        }
                    }else{
                        if($row['flag'] === 'admin'){
                            return $default_admin_sender_name;
                        }else{
                            return sprintf($sender_name_format, $row['playerId'], $d, (!empty($row['affiliates_username'])) ? $d . ' (' . $row['affiliates_username'] . ')' : $d);
                        }
                    }
                }
            ),
            array(
                'dt'=>$i++,
                'alias' => 'recieve',
                'select' => 'player.username',
                'formatter' => function($d, $row) use ($self, $default_admin_sender_name, $request_default_guest_name){
                    $receive_name_format = '<a target="_blank" href="/player_management/userInformation/%1$s" data-toggle="sbe-player-helper-menu" data-player-id="%1$s" data-player-name="%2$s">%3$s</a>';
                    if($row['message_type'] == Internal_message::MESSAGE_TYPE_REQUEST_FORM){
                        if($row['flag'] === 'admin'){
                            if(empty($row['playerId'])){
                                return $request_default_guest_name;
                            }else{
                                return sprintf($receive_name_format, $row['playerId'], $d, (!empty($row['affiliates_username'])) ? $d . ' (' . $row['affiliates_username'] . ')' : $d);
                            }
                        }else{
                            return $default_admin_sender_name;
                        }
                    }else{
                        if($row['flag'] === 'admin'){
                            return sprintf($receive_name_format, $row['playerId'], $d, (!empty($row['affiliates_username'])) ? $d . ' (' . $row['affiliates_username'] . ')' : $d);
                        }else{
                            return $default_admin_sender_name;
                        }
                    }
                }
            ),
            array(
                'dt'=>$i++,
                'alias' => 'adminUsername',
                'select' => 'adminusers.username'
            ),
			array(
				'dt'=>$i++,
				'alias' => 'subject',
				'select' => 'messages.subject',
                'formatter'=>function ($d,$row) {
                    return '
                        <td style="text-align:center;">
                        <a href="javascript: void(0);" data-toggle="tooltip" title="'.lang('tool.cs01').'" onclick="message_reply_message(\''.$row['messageId'].'\');"><span style="display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; max-width:100px;">' .  $d. '</span> </a>
				    </td>';
                }
			),
			array(
				'alias' => 'session',
				'select' => 'messages.session',
			),
			array(
				'dt'=>$i++,
				'alias' => 'date',
				'select' => 'messages.date',
			),
            array(
                'dt'=>$i++,
                'alias' => 'status',
                'select' => 'messages.status',
                'formatter'=>function ($d,$row) {
                    $output = '';
                    if ($d == Internal_message::STATUS_NEW) {
                        $output .= '<span class="glyphicon glyphicon-ok text-success"></span>';
                    } else {
                        $output .= '<span class="glyphicon glyphicon-remove text-danger"></span>';
                    }
                    return '<td style="text-align:center;">' . $output . '</td>';
                }
            ),
            array(
                'dt'=>$i++,
                'alias' => 'admin_unread_count',
                'select' => 'messages.admin_unread_count',
            ),
            array(
                'dt'=>$i++,
                'alias' => 'player_unread_count',
                'select' => 'messages.player_unread_count',
            ),
            array(
                'dt'=>$i++,
                'alias' => 'isclose',
                'select' => 'messages.status',
                'formatter'=>function ($d,$row) {
                    if ($d == Internal_message::STATUS_DISABLED) {
                        return lang('lang.close');
                    } else {
                        return lang('lang.open');
                    }
                }
            ),
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'messages';
		$joins = array(
			'messagesdetails' => 'messagesdetails.messageId = messages.messageId',
			'player'=>'player.playerId = messages.playerId',
			'adminusers'=>'adminusers.userId = messages.adminId',
			'affiliates as receiver_affiliates' => 'receiver_affiliates.affiliateId = player.affiliateId',
			//'affiliates as receiver_affiliates' => 'receiver_affiliates.affiliateId = player.affiliateId'
		);

		$group_by = [ 'messages.messageId' ];

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

        if (isset($input['adminUserId'])) {
            $where[] = "messagesdetails.adminId = '".$this->db->escape_like_str($input['adminUserId'])."'";
            // $values[] = $input['messages'];
        }

        if (isset($input['messages'])) {
            $where[] = "messagesdetails.message LIKE '%".$this->db->escape_like_str($input['messages'])."%'";
            // $values[] = $input['messages'];
        }

        if (isset($input['subject'])) {
            $where[] = "messages.subject LIKE '%".$this->db->escape_like_str($input['subject'])."%'";
            // $values[] = $input['subject'];
        }

        if (isset($input['playerUsername'])) {
            $where[] = "(player.username LIKE '%".$this->db->escape_like_str($input['playerUsername'])."%')";
            // $values[] = $input['sender'];
        }

        if (isset($input['date_from'], $input['date_to'])) {
            $where[] = "messages.date >=?";
            $where[] = "messages.date <=?";
            $values[] = $input['date_from'];
            $values[] = $input['date_to'];
        }

        if (isset($input['status'])) {
            switch($input['status']){
                case 'player_new':
                    $where[] = "messages.status = " . Internal_message::STATUS_NEW;
                    break;
                case 'unprocessed':
                    $unprocessed_status = [Internal_message::STATUS_NEW, Internal_message::STATUS_UNPROCESSED];
                    $where[] = "messages.status IN (" . implode(',', $unprocessed_status) . ")";
                    break;
                case 'processed':
                    $unprocessed_status = [Internal_message::STATUS_PROCESSED, Internal_message::STATUS_NORMAL, Internal_message::STATUS_READ];
                    $where[] = "messages.status IN (" . implode(',', $unprocessed_status) . ")";
                    break;
                case 'admin_new':
                    $unprocessed_status = [Internal_message::STATUS_ADMIN_NEW];
                    $where[] = "messages.status IN (" . implode(',', $unprocessed_status) . ")";
                    break;
                case 'admin_read':
                    $unprocessed_status = [Internal_message::STATUS_READ];
                    $where[] = "messages.status IN (" . implode(',', $unprocessed_status) . ")";
                    break;
                case 'markclose':
                    $where[] = "messages.status = " . Internal_message::STATUS_DISABLED;
                    break;
            }
        }else{
            // $where[] = "messages.status <> ".Internal_message::STATUS_DISABLED;
        }


		//filter not closed messages
		$where[] = "messages.deleted = 0";
		// $values[] = 2;
		// filter message from player only
		// $where[] = "messages.adminId = 0";

		# END PROCESS SEARCH FORM #################################################################################################################################################

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by);

		$this->returnJsonResult($result);
	}

	/**
	 * overview : livechat link
	 */
	public function livechat_link(){

		$data = array('title' => lang('Live Chat Link'), 'sidebar' => 'cs_management/sidebar',
			'activenav' => 'player_live_chat_link');

		if (!$this->permissions->checkPermissions('player_live_chat_link')) {
			return $this->showErrorAccess($data['title'], $data['sidebar'], $data['system_settings']);
		}

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$render = true;

		$this->addBoxDialogToTemplate();

		// $settings_name_list = array('approve_transfer_to_main', 'approve_transfer_from_main', 'min_withdraw');

		$live_chat_options=$this->utils->getConfig('live_chat');

		$data['link']=$this->utils->getSystemUrl('player').'/pub/live_chat_link/'.$live_chat_options['www_chat_options']['lang'];
		// $this->utils->debug_log('load settings', $data['settings']);

		$this->loadDefaultTemplate(array(),
			array('resources/css/general/style.css'),
			array('title' => $data['title'],
				'activenav' => $data['activenav'],
				'userId' => $userId,
				'username' => $username), $data['sidebar'],
			'cs_management/livechat_link', $data, $render);

	}

	/**
	 * overview : redirect to support ticket
	 */
	public function go_support_ticket(){
		//redirect
		$url=$this->utils->getConfig('admin_support_ticket');
		if(empty($url)){
			redirect('/');
		}else{
			redirect($url);
		}
	}

	/**
	 * overview : message details
	 *
	 * detail : get all admin messages
	 */
	public function messagesDeleteRedirect($data) {
		if (!$this->permissions->checkPermissions('chat')) {
			$this->error_access();
		} else {
			$user_id = $this->authentication->getUserId();

			$this->load->model(array('internal_message'));

			$this->loadTemplate('CS Management', '', '', 'cs');
			$this->template->write_view('main_content', 'cs_management/messages', $data);
			$this->template->render();
		}
	}

	public function saveMessageDefaultAdminSenderName(){
        $this->operatorglobalsettings->putSetting('default_message_admin_sender_name', $this->input->post('default_message_admin_sender_name'));

        $this->saveAction(self::MANAGEMENT_TITLE, 'Save Message Default Admin Sender Name');

        return $this->returnCommon(BaseController::MESSAGE_TYPE_SUCCESS, lang('sys.gd25'));
    }

    public function sendBatchMessagePost() {
        if (!$this->permissions->checkPermissions('send_message_sms')) {
            return $this->error_access();
        }

        $this->load->library(['player_message_library']);

        $this->load->model(array('player_model', 'internal_message'));

        $this->form_validation->set_rules('message-body', 'Message', 'trim|required|xss_clean|callback_chkmsg');
        $this->form_validation->set_message('chkmsg', sprintf(lang('form.validation.max_length'), lang('Message'), $this->player_message_library->getDefaultAdminSendMessageLengthLimit()));

        if ($this->form_validation->run() == false) {
            return $this->returnCommon(self::MESSAGE_TYPE_ERROR,  validation_errors());
        }

        $allPlayer = array();
        $manual_input_usernames = $this->input->post('messages_send_message_username');
        $sender = $this->input->post('message-sender');
        $subject = $this->input->post('message-subject', TRUE);
        $message = $this->input->post('message-body', TRUE);
        $disabled_reply = !!$this->input->post('disabled_reply');

		if(!empty($manual_input_usernames)) {
			foreach ((array)$manual_input_usernames as $key => $value) {
				array_push($allPlayer, $value);
			}
		}

		if (!empty($_FILES['messages_send_message_batch_players']['tmp_name'])) {
	        $csv = file_get_contents($_FILES['messages_send_message_batch_players']['tmp_name']);
	        $row = explode("\n", $csv);
	        $row = array_filter($row);
	        $player_usernames = array_unique($row);

	        foreach ($player_usernames as $player_username) {
	            $player_username = str_replace(array('.', ' ', "\n", "\t", "\r"), '', trim($player_username));
	            $playerId = $this->player_model->getPlayerIdByUsername($player_username);
                $allPlayer[] = $playerId;
	        }
	    }

		$playerIds = array_filter(array_unique($allPlayer));

		if(count($playerIds) <= 0){
            return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('con.d02'));
		}

        if (!$subject || !$message ) {
            return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('con.d02'));
        }

        // OGP-14357: Sanitize message, remove script blocks
        if ($this->utils->getConfig('internal_message_edit_allow_only_plain_text_when_pasting')) {
        	$message = $this->message_remove_script_blocks($message);
        }

		$message = $this->utf8convert($message);

        $today = date('Y-m-d H:i:s');
        $userId = $this->authentication->getUserId();
        $sender = (empty($sender)) ? $this->player_message_library->getDefaultAdminSenderName() : $sender;
        $sender = (empty($sender)) ? $this->authentication->getUsername() : $sender;
        $this->startTrans();

        if ($playerIds) {
            foreach ($playerIds as $playerId) {
                $this->internal_message->addNewMessageAdmin($userId, $playerId, $sender, $subject, $message, TRUE, $disabled_reply);
            }
        }

        $succ = $this->endTransWithSucc();

        if (!$succ) {
            return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('sys.ga.erroccured'));
        } else {
            return $this->returnCommon(BaseController::MESSAGE_TYPE_SUCCESS, lang('mess.19'));
        }
	}

	protected function utf8convert($mesge, $key = null) {
		if (is_array($mesge)) {
			foreach ($mesge as $key => $value) {
				$mesge[$key] = utf8convert($value, $key);
			}
		} elseif (is_string($mesge)) {
			$fixed = mb_convert_encoding($mesge, "UTF-8", "auto");
			return $fixed;
		}
		return $mesge;
	}

    /**
     * Extra sanitization for internal messages
     * @param	string	$mesg	Message body, generally converted to HTML escape sequences by sceditor first.
     * @return	string	Sanitized message body.
     */
    protected function message_remove_script_blocks($mesg) {
    	// OGP-14357 - As HTML pasted to sceditor will be encoded into htmlspecialentities, it's hard to got to run through with DOMParser
    	// So we just remove everything between 'script' and '/script' here

    	$mesg_sanitized = preg_replace('/(<|&lt;)\/?script.+(>|&gt;)/is', '', $mesg);

    	return $mesg_sanitized;
    }
}

/* End of file cs_management.php */
/* Location: ./application/controllers/cs_management.php */
