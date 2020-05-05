<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 *
 * General behaviors include
 * * set or update status of a message for a certain player
 * * get the message of a certain player
 * * add new message admin
 * * add new message
 * * Will get messages history based on the session
 * * Will get messages status based on the session
 * * delete a certain message
 * * mark message as close
 * * count unread message of a certain player
 *
 * @category CS Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Internal_message extends BaseModel {

	function __construct() {
		parent::__construct();
	}

    const STATUS_NEW = 3;
    const STATUS_READ = 4;
    const STATUS_DRAFTS = 5;
    const STATUS_ADMIN_NEW = 6;
    const STATUS_UNPROCESSED = 7;
    const STATUS_PROCESSED = 8;

	const MESSAGE_DETAILS_UNREAD = 0;
	const MESSAGE_DETAILS_READ = 1;

    const ADMIN_ID = 1;
    const SUPERADMIN_ID = 2;

    const MESSAGE_TYPE_NORMAL = 0;
    const MESSAGE_TYPE_REQUEST_FORM = 1;

	protected $tableName = 'messages';

	/**
	 * detail: get the message of a certain player
	 *
	 * @param int $playerId messages player id
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array
	 */
	public function getMessages($playerId, $limit, $offset, $where = '') {
		$this->db->select('c.messageId,
						  c.message_type,
						  c.playerId,
						  c.adminId,
						  c.session,
						  c.subject,
						  c.date,
						  c.status,
						  c.deleted,
						  c.deleted_at,
						  c.deleted_by,
						  p.username AS sender,
						  au.username AS recepient,
						  cd.message AS detail,
						  c.admin_unread_count,
						  c.player_unread_count');
		$this->db->from('messages AS c');
		$this->db->join('messagesdetails AS cd', 'cd.messageId = c.messageId', 'left');
		$this->db->join('player AS p', 'p.playerId = c.playerId', 'left');
        $this->db->join('adminusers AS au', 'au.userId = cd.adminId', 'left');
		$this->db->where('c.playerId', $playerId);
		$this->db->group_by('c.messageId');
		$this->db->order_by('c.messageId','DESC');

		if( ! empty( $where ) ) $this->db->where($where);

		if ($limit != null||$limit != 0) {
			$this->db->limit($limit,$offset);
		}

		$query = $this->db->get();
		$this->CI->utils->debug_log('retrieve sql', $this->db->last_query());

		return $query->result_array();
	}

	public function playerUnreadMessages($player_id){
        $filter = 'c.deleted = 0 AND cd.flag = "admin" AND cd.status = ' . static::MESSAGE_DETAILS_UNREAD;
        return $this->getMessages($player_id, null, null, $filter);
    }

	public function getMessageById($message_id, $player_id = NULL){
        $this->db->select('c.messageId,
						  c.message_type,
						  cd.messageDetailsId,
						  c.playerId,
						  p.username AS playerUsername,
						  c.adminId,
						  au.username AS adminUsername,
						  cd.sender AS admin_custom_name,
						  c.subject,
						  cd.message AS detail,
						  cd.date,
						  c.status,
						  cd.status AS detail_status,
						  cd.flag,
						  c.is_system_message,
						  c.disabled_replay,
						  c.deleted,
						  c.deleted_at,
						  c.deleted_by');
        $this->db->from('messages AS c');
        if(!empty($message_id)){
            $this->db->join('messagesdetails AS cd', 'cd.messageId = c.messageId AND cd.messageId="' . $message_id . '"', 'left');
        }else{
            $this->db->join('messagesdetails AS cd', 'cd.messageId = c.messageId', 'left');
        }
        $this->db->join('player AS p', 'p.playerId = c.playerId', 'left');
        $this->db->join('adminusers AS au', 'au.userId = cd.adminId', 'left');
        $this->db->order_by('cd.messageDetailsId','ASC');

        if(!empty($message_id)){
            $this->db->where('c.messageId', $message_id);
        }

        if(!empty($player_id)){
            $this->db->where('c.playerId', $player_id);
        }

        return $this->runMultipleRowArray();
    }

	/**
	 * detail: add new message admin
	 *
	 * @param int $user_id messages adminId
	 * @param int $playerId messages player id
	 * @param string  $ticket_number messages session
	 * @param string $subject messages subject
	 * @param string $sender messagedetails sender
	 * @param string $message messagedetails message
	 *
	 * @return int
	 */
	public function addNewMessageAdmin($user_id // #1
		, $playerId // #2
		, $sender // #3
		, $subject // #4
		, $message // #5
		, $is_system_message = FALSE // #6
		, $disabled_replay = FALSE // #7
	) {
        $this->startTrans();

        $ticket_number = 'Ticket#' . random_string('numeric', 16);

        $messageId = $this->insertData($this->tableName, array(
            'message_type' => self::MESSAGE_TYPE_NORMAL,
			'adminId' => $user_id,
			'playerId' => $playerId,
			'session' => $ticket_number,
			'subject' => $subject,
			'date' => $this->utils->getNowForMysql(),
			'is_system_message' => $is_system_message,
			'disabled_replay' => $disabled_replay,
			'admin_last_reply_id' => $user_id,
			'admin_last_reply_dt' => $this->utils->getNowForMysql(),
			'admin_unread_count' => 1,
			'status' => self::STATUS_ADMIN_NEW,
		));

        if(empty($messageId)){
            $this->rollbackTrans();
            return FALSE;
        }

        $messageDetailsId = $this->addNewMessageDetailWithAdmin($messageId, $user_id, $sender, $message);

        if(empty($messageDetailsId)){
            $this->rollbackTrans();
            return FALSE;
        }
        $this->endTransWithSucc();

        return $messageId;
	}

	/**
	 * detail: add new message
	 *
	 * @param int $player_id message player id
	 * @param string $ticket_number message session
	 * @param string $subject message subject
	 * @param string $message messagedetail message
	 *
	 * @return int
	 */
	public function addNewMessage($player_id, $subject, $sender, $message, $drafts = FALSE) {
        $this->startTrans();

        $ticket_number = 'Ticket#' . random_string('numeric', 16);

        $messageId = $this->insertData($this->tableName, array(
            'message_type' => self::MESSAGE_TYPE_NORMAL,
            'adminId' => static::ADMIN_ID,
			'playerId' => $player_id,
			'session' => $ticket_number,
			'subject' => $subject,
			'date' => $this->utils->getNowForMysql(),
            'is_system_message' => FALSE,
            'disabled_replay' => FALSE,
            'player_last_reply_dt' => $this->utils->getNowForMysql(),
            'player_unread_count' => 1,
            'status' => (!empty($drafts)) ? self::STATUS_DRAFTS : self::STATUS_NEW,
		));

        if(empty($messageId)){
            $this->rollbackTrans();
            return FALSE;
        }

        $messageDetailsId = $this->addNewMessageDetail($messageId, $sender, $message);

        if(empty($messageDetailsId)){
            $this->rollbackTrans();
            return FALSE;
        }
        $this->endTransWithSucc();

		return $messageId;
	}

    public function addRequestFormMessage($player_id = NULL, $sender = NULL, $message = NULL) {
        $this->startTrans();

        $ticket_number = 'Ticket#' . random_string('numeric', 16);

        $messageId = $this->insertData($this->tableName, array(
            'message_type' => self::MESSAGE_TYPE_REQUEST_FORM,
            'adminId' => static::ADMIN_ID,
            'playerId' => (empty($player_id)) ? '' : $player_id,
            'session' => $ticket_number,
            'subject' => lang($this->CI->utils->getConfig('message_request_form_default_subject')),
            'date' => $this->utils->getNowForMysql(),
            'is_system_message' => FALSE,
            'disabled_replay' => FALSE,
            'player_last_reply_dt' => $this->utils->getNowForMysql(),
            'player_unread_count' => 1,
            'status' => self::STATUS_NEW,
        ));

        if(empty($messageId)){
            $this->rollbackTrans();
            return FALSE;
        }

        $messageDetailsId = $this->addNewMessageDetail($messageId, $sender, $message);

        if(empty($messageDetailsId)){
            $this->rollbackTrans();
            return FALSE;
        }
        $this->endTransWithSucc();

        return $messageId;
    }

    /**
	 * detail: add new message details
	 *
	 * @param int $messageId messagesdetails/messages messageId
	 * @param string $sender messagesdetails sender
	 * @param string $message messagesdetails message
	 * @param int $flag messagesdetails flag
	 *
	 * @return bool|int
	 */
	public function addNewMessageDetail($messageId, $sender, $message) {
		$data = array(
            'messageId' => $messageId,
            'sender' => (empty($sender)) ? '' : $sender,
            'recipient' => '',
            'message' => $message,
            'date' => $this->utils->getNowForMysql(),
            'status' => static::MESSAGE_DETAILS_UNREAD,
            'flag' => 'player',
        );

		return $this->insertData('messagesdetails', $data);
	}

    public function addNewMessageDetailWithAdmin($messageId, $adminId, $sender, $message) {
        $data = array(
            'messageId' => $messageId,
            'adminId' => $adminId,
            'sender' => (empty($sender)) ? '' : $sender,
            'recipient' => '',
            'message' => $message,
            'date' => $this->utils->getNowForMysql(),
            'status' => static::MESSAGE_DETAILS_UNREAD,
            'flag' => 'admin',
        );

        return $this->insertData('messagesdetails', $data);
    }

    /**
	 * detail: Will get messages history based on the session
	 *
	 * @param int $message_id messagesdetails message_id
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getMessagesHistoryByMessageId($message_id, $limit, $offset, $order = 'a') {
		if ($limit != null) {
			$limit = "LIMIT " . $limit;
		}

		if ($offset != null && $offset != 'undefined') {
			$offset = "OFFSET " . $offset;
		} else {
			$offset = ' ';
		}

		$order = strtolower($order);
		$order_opt = in_array($order, [ 'desc', 'd' ]) ? 'DESC' : 'ASC';
		$order_clause = " ORDER BY messageDetailsId {$order_opt} ";

		$sql = "SELECT * FROM `messagesdetails` where messageId = ? {$order_clause} $limit $offset";

		$query = $this->db->query($sql, array($message_id));

		return $query->result_array();
	}

	/**
	 * detail: Will get messages status based on the session
	 *
	 * @param int $messages_id messages messageId
	 * @return int
	 */
	public function getMessagesStatusByMessageId($messages_id) {
		$sql = "SELECT * FROM `messages` where messageId = ? ";

		$query = $this->db->query($sql, array($messages_id));

		$result = $query->row_array();

		return $result['status'];
	}

	public function isDeleted($message_id){
        $sql = "SELECT * FROM `messages` where messageId = ? ";

        $query = $this->db->query($sql, array($message_id));

        $result = $query->row_array();

        return ($result['deleted'] > 0);
    }

	/**
	 * detail: delete a certain message
	 *
	 * @param int $message_id messages message_id
	 * @param int $userId messages deleted_by
	 *
	 * @return array
	 */
	public function deleteMessage($message_id, $userId = null) {
		$this->db->update('messages', array(
			'deleted' => self::DB_TRUE,
			'deleted_at' => $this->utils->getNowForMysql(),
			'deleted_by' => $userId,
		), array('messageId' => $message_id));
	}

	/**
	 * detail: Will get the messages
	 *
	 * @param int $user_id
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getAdminMessages($user_id, $limit, $offset) {

		$this->db->select('c.messageId,
						  c.message_type,
						  c.playerId,
						  c.adminId,
						  c.session,
						  c.subject,
						  c.date,
						  c.status,
						  c.deleted,
						  c.deleted_at,
						  c.deleted_by,
						  p.username AS sender,
						  MIN(cd.status) AS isread');
		$this->db->from('messages AS c');
		$this->db->join('messagesdetails AS cd', 'cd.messageId = c.messageId', 'left');
		$this->db->join('player AS p', 'p.playerId = c.playerId', 'left');
		$this->db->join('adminusers AS au', 'au.userId = c.adminId', 'left');
		$this->db->where('c.deleted', 0);
		$this->db->group_by('c.messageId');
		$this->db->order_by('c.messageId','ASC');

		if ($limit != null||$limit != 0) {
			$this->db->limit($limit,$offset);
		}

		$query = $this->db->get();
		return $this->db->last_query();exit;
	}

	/**
	 * detail: mark message as close
	 *
	 * @param int $message_id messages messageId
	 * @param int $user_id
	 * @return Boolean
	 */
	public function markAsClose($message_id, $user_id=null) {
		//set disabled
        $this->db->update('messages', array(
            'admin_unread_count' => 0,
            'player_unread_count' => 0,
            'status' => self::STATUS_DISABLED,
            'admin_last_reply_id' => $user_id,
            'admin_last_reply_dt' => $this->utils->getNowForMysql(),
        ), array('messageId' => $message_id));
		//set read
		$this->db->set('status', self::MESSAGE_DETAILS_READ)->where('messageId', $message_id);
		$this->runAnyUpdate('messagesdetails');
	}

	/**
	 * detail: count unread messages for a certain player
	 *
	 * @param int $player_id messages playerId
	 * @return int
	 */
    public function countPlayerUnreadMessages($player_id) {
        $result = $this->playerUnreadMessages($player_id);

        return (empty($result)) ? 0 : count($result);
    }

    /**
     * detail: count unread messages
     */
    public function countAdminTotalUnreadMessages() {
        $this->db->select('COUNT(messageId) as cnt')
            ->from('messages')
            ->where('deleted', 0)
            ->where_in('status', [self::STATUS_NEW, self::STATUS_UNPROCESSED])
        ;

        $cnt = $this->runOneRowOneField('cnt');

        return (empty($cnt)) ? 0 : (int)$cnt;
    }

    public function countTotalUnreadByMessageId($message_id){
        $this->db->select('flag, COUNT(messageDetailsId) as cnt')
            ->from('messagesdetails')
            ->where('messageId', $message_id)
            ->where('status', self::MESSAGE_DETAILS_UNREAD)
            ->group_by('flag')
        ;

        $rows = $this->runMultipleRowArray();

        $result = [
            'admin' => 0,
            'player' => 0
        ];
        if(empty($rows)){
            return $result;
        }

        foreach($rows as $entry){
            $result[$entry['flag']] = $entry['cnt'];
        }

        return $result;
    }

	/**
	 * detail: remove Message from checkbox selection
	 *
	 * @param int $messagecmsId
	 * @return Boolean
	 */
	public function deleteMessageFromCheckbox($user_id, $messagecmsId) {
        $fields = array(
            'deleted' => self::DB_TRUE,
            'deleted_at' => $this->utils->getNowForMysql(),
            'deleted_by' => $user_id,
        );

		$this->db->where('messageId', $messagecmsId);

        $this->db->update('messages', $fields);

        return ($this->db->affected_rows());
	}

    /**
     * detail: update status to read of a message
     *
     * @param int $messageId messages messageId
     * @return Boolean
     */
    public function updateMessageStatus($messageId, $flag, $status = NULL, $detail_status = NULL, $player_id = NULL) {
        if(!empty($status)){
            $this->db->set('status', $status)->where('messageId', $messageId);
            if(!empty($player_id)){
                $this->db->where('playerId', $player_id);
            }
            $this->runAnyUpdate('messages');
        }

        if(!empty($detail_status)){
            $this->db->set('status', $detail_status)->where('messageId', $messageId)->where('flag', $flag);
            $this->runAnyUpdate('messagesdetails');
        }
    }

    /**
     * detail: update messages
     *
     * @param array $messages
     * @param int $message_id messages messageId
     * @return 	void
     */
    public function updateMessages($messages, $message_id) {
        $this->db->where('messageId', $message_id);
        $this->db->update('messages', $messages);
    }

    /**
     * detail: update messages details
     *
     * @param array $message_details
     * @param int $message_id messagesdetails messageId
     *
     * @return Boolean
     */
    public function updateMessagesDetailsByMessageId($messages_details, $message_id, $flag = NULL) {
        $this->db->where('messageId', $message_id);
        if(!empty($flag)){
            $this->db->where('flag', $flag);
        }
        $this->db->update('messagesdetails', $messages_details);

        return ($this->db->affected_rows());
    }
}

///END OF FILE/////
