<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Transaction_notes
 *
 * This model represents notes attached to transactions e.g. withdrawal.
 *
 */
class Walletaccount_notes extends BaseModel {
	protected $tableName = 'walletaccount_notes';

	const ACTION_LOG    = 1;
	const INTERNAL_NOTE = 2;
	const EXTERNAL_NOTE = 3;

	public function __construct() {
		parent::__construct();
	}

	# $transaction can be 'withdrawal' or 'deposit'
	# $params can contain the following:
	# before_status
	# after_status
	public function add($note, $created_by, $note_type, $walletAccountId, $stages_name = null, $params = array()) {
		if(empty($note)){
			$note='N/A';
		}

		$data['content'] = $note;
		$data['created_by'] = $created_by;
		$data['created_at'] = $this->utils->getNowForMysql();
		$data['note_type'] = $note_type;
		$data['walletAccountId'] = $walletAccountId;
		$data['status_name'] = $stages_name;

		// $data = array_merge($data, $params);
		// OGP-14817 updated walletaccount processDateTime
		$this->db->where('walletAccountId', $walletAccountId);
		$this->db->update('walletaccount', array('processDateTime' => $data['created_at']));

		return $this->insertRow($data);
	}

	/**
	 * get notes by type and id
	 * @param  string $note_type
	 * @param  int $walletAccountId
	 * @return string
	 */
	public function getNotesByNoteType($note_type, $walletAccountId, $display_last_notes = false, $donotShowUsername = false, $limit_the_number_of_words_displayed = false) {
		$rows=$this->getWalletAccountNotes($note_type, $walletAccountId);
		$notes='';
		if(!empty($rows)){
			if($limit_the_number_of_words_displayed){
				$notes = $this->formatWithdrawalLessNotes($rows, $walletAccountId, $note_type);
			}else{
				$notes = $this->formatWithdrawalNotes($rows, $display_last_notes, $donotShowUsername);
			}
		}
		return $notes;
	}

	/**
	 * get row(s) by type, walletAccountId, status
	 * @param  string $note_type   type
	 * @param  int $walletAccountId id
	 * @return string
	 */
	public function getWithdrawalNotesBywalletAccountId($walletAccountId, $note_type = null) {
		$this->db->select($this->tableName.'.*');
		$this->db->from($this->tableName);
		$this->db->where('walletAccountId', $walletAccountId);
		if (!empty($note_type)) {
			$this->db->where('note_type', $note_type);
		}

		$this->db->order_by('created_at');

		return $this->runMultipleRowArray();
	}

    public function checkNoteExist($walletAccountId, $msg){
        $notes = $this->getWithdrawalNotesBywalletAccountId($walletAccountId, walletaccount_notes::ACTION_LOG);
        if(strpos($msg, "valid hour") !== false){
            $msg = "Wrong callback, callback time over valid hour.";
        }

        if(is_array($notes)){
	        foreach ($notes as $note) {
	            if(strpos($note['content'], $msg) !== false){
	                return true;
	            }
	        }
	    }
        return false;
    }

	public function getWalletAccountNotes($note_type, $walletAccountId) {
		switch($note_type){
			case self::ACTION_LOG:
			case self::INTERNAL_NOTE:
			case self::EXTERNAL_NOTE:
				$this->db->select($this->tableName.'.created_at, adminusers.realname as creater_name, content ,status_name');
				$this->db->from($this->tableName);
				$this->db->join('adminusers', $this->tableName.'.created_by = adminusers.userId');
				$this->db->where('walletAccountId', $walletAccountId);
				break;
			default:
				break;
		}

		if (!empty($note_type)) {
			$this->db->where('note_type', $note_type);
		}

		$this->db->order_by('created_at');
		return $this->runMultipleRowArray();
	}

	/**
	 * format
	 * @param  array $WithdrawalNotes
	 * @return string
	 */
	public function formatWithdrawalNotes($withdrawalNotes, $display_last_notes = false, $donotShowUsername = false) {
		$noteString = '';
		if(!empty($withdrawalNotes)){
			if ($display_last_notes) {
				$aNote = end($withdrawalNotes);
				if($aNote) {
					$noteString .= sprintf("[%s] %s: %s</br>", $aNote['created_at'], $aNote['creater_name'], $aNote['content']);
				}
			} else {
				foreach ($withdrawalNotes as $aNote) {
					if($donotShowUsername){
						$noteString .= sprintf("[%s] : %s</br>", $aNote['created_at'], $aNote['content']);
					}else{
						$noteString .= sprintf("[%s] %s: %s</br>", $aNote['created_at'], $aNote['creater_name'], $aNote['content']);
					}
				}
			}
		}
		return $noteString;
	}

	public function formatWithdrawalLessNotes($withdrawalNotes, $walletAccountId, $note_type){
		$limit = $this->utils->getConfig('limit_the_number_of_words_displayed');
		$fewerStrings = '';
		foreach ($withdrawalNotes as $aNote) {
			$content_length = mb_strlen(str_replace('</br>', "\n", $aNote['content']), "utf-8");

			if($limit >= 0){
				$limitLength  = $limit;
			}

			$limit = $limit - $content_length;

			if($aNote['status_name'] == null){
				$aNote['status_name'] = lang('no status');
			}

			if(abs($limit) < 50 &&  $limit >= 0){

				$fewerStrings .= sprintf("[%s] %s_%s: %s</br>", $aNote['created_at'], $aNote['creater_name'], $aNote['status_name'], $aNote['content']);

			}else if ($limit<0){

				$contentString = mb_substr($aNote['content'], 0, $limitLength , "utf-8").'<a href="javascript:void(0)" class="" id="btn_show_all_notes" style="cursor:" onclick="showDetialNotes('.$walletAccountId.','.$note_type.')">'.' ...'.lang('More');

				$fewerStrings .= sprintf("[%s] %s_%s: %s</br>", $aNote['created_at'], $aNote['creater_name'], $aNote['status_name'], $contentString);
				break;
			}
		}
		return $fewerStrings;
	}

	public function batchCopyDataFromWalletaccount(){
		$this->db->select('*');
		$this->db->from('walletaccount');
		$this->db->where('notes IS NOT NULL');
		$this->db->where('transactionType', 'withdrawal');

		$query = $this->runMultipleRow();

		$sql = $this->db->last_query();
		$this->utils->debug_log('========================get walletaccount sql ' . $sql);

		$data         = [];
		$id           = '';
		$note_type    = '';
		$content      = '';
		$created_time = '';
		$created_by   = '';

		foreach ($query as $value) {
			$id                      = $value->walletAccountId;
			$notes                   = $value->notes;
			$created_at              = $value->dwDateTime;
			$processed_approved_time = $value->processDatetime;
			$processed_by            = $value->processedBy;

			# handle data in `notes`
			if(!empty($notes)){
				$notes_array = explode('|', $notes);
				foreach ($notes_array as $content) {
					$content = trim($content);
					if(empty($content)){
						continue;
					}

					switch ($content) {
						case strpos($content, 'Fee') !== FALSE:
						case strpos($content, 'ETH') !== FALSE:
						case strpos($content, 'BTC') !== FALSE:
						case strpos($content, 'Rate') !== FALSE:
						case strpos($content, 'declined by player') !== FALSE:
							$note_type = self::ACTION_LOG;
							$created_time = $created_at;
							$created_by = Users::SUPER_ADMIN_ID;
							break;

						default:
							$note_type = self::INTERNAL_NOTE;
							$created_time = empty($processed_approved_time) ? $created_at : $processed_approved_time;
							$created_by = empty($processed_by) ? Users::SUPER_ADMIN_ID : $processed_by;
							break;
					}

					$str = array(
						'walletAccountId' => $id,
						'note_type' => $note_type,
						'content' => trim($content),
						'created_at' => $created_time,
						'created_by' => $created_by
					);
					$data[] = $str;
				}
			}
		}
		$this->startTrans();
		$this->db->insert_batch($this->tableName, $data);
		$success = $this->endTransWithSucc();

		return $success;
	}

	public function batchCopyDataFromTransactionNotes(){
		$this->db->select('transaction_notes.*');
		$this->db->from('transaction_notes');
		$this->db->join('walletaccount','transaction_notes.transaction_id = walletaccount.walletAccountId');
		$this->db->where('transaction_notes.transaction', 'withdrawal');

		$query = $this->runMultipleRow();

		$sql = $this->db->last_query();
		$this->utils->debug_log('========================get transaction_notes sql ' . $sql);

		$data         = [];
		$id           = '';
		$note_type    = '';
		$content      = '';
		$created_time = '';
		$created_by   = '';
		foreach ($query as $value) {
			$id           = $value->transaction_id;
			$content      = $value->note;
			$created_at   = $value->create_date;
			$processed_by = $value->admin_user_id;

			$content = trim($content);
			if(empty($content) || $content == "N/A" || $content == "false"){
				continue;
			}

			switch ($content) {
				case strpos($content, 'Withdraw status') !== FALSE:
				case strpos($content, '取款状态') !== FALSE:
				case strpos($content, 'สถานะการถอนเงิน') !== FALSE:
				case strpos($content, 'Trạng thái rút tiền') !== FALSE:
				case strpos($content, '출금 상태') !== FALSE:
				case strpos($content, 'Withdraw successful:') !== FALSE:
				case strpos($content, '取款成功:') !== FALSE:
				case strpos($content, 'ถอนเงินสำเร็จ:') !== FALSE:
				case strpos($content, 'Rút tiền thành công:') !== FALSE:
				case strpos($content, 'Withdraw failed:') !== FALSE:
				case strpos($content, '取款失败:') !== FALSE:
				case strpos($content, 'ถอนเงินล้มเหลว:') !== FALSE:
				case strpos($content, 'Rút tiền không thành công:') !== FALSE:
				case strpos($content, 'Payment Failed -') !== FALSE:
				case strpos($content, '付款失败 -') !== FALSE:
				case strpos($content, 'Thanh toán không thành công -') !== FALSE:
					$note_type = self::ACTION_LOG;
					$created_time = $created_at;
					$created_by = Users::SUPER_ADMIN_ID;
					break;

				case strpos($content, 'Unlock Order by') !== FALSE:
				case strpos($content, 'callback') !== FALSE:
				case strpos($content, 'Batch') !== FALSE:
				case strpos($content, 'Fee') !== FALSE:
					$note_type = self::ACTION_LOG;
					$created_time = $created_at;
					$created_by = Users::SUPER_ADMIN_ID;
					break;

				case strpos($content, 'pay withdraw') !== FALSE:
				case strpos($content, 'response') !== FALSE:
				case strpos($content, 'success') !== FALSE:
				case strpos($content, 'RPN') !== FALSE:
				case strpos($content, 'Vicus') !== FALSE:
				case strpos($content, 'SDPay2ND') !== FALSE:
				case strpos($content, 'Status Code:') !== FALSE:
					$note_type = self::ACTION_LOG;
					$created_time = $created_at;
					$created_by = Users::SUPER_ADMIN_ID;
					break;

				default:
					$note_type = self::INTERNAL_NOTE;
					$created_time = $created_at;
					$created_by = empty($processed_by) ? Users::SUPER_ADMIN_ID : $processed_by;
					break;
			}

			$str = array(
				'walletAccountId' => $id,
				'note_type' => $note_type,
				'content' => trim($content),
				'created_at' => $created_time,
				'created_by' => $created_by
			);
			$data[] = $str;
		}

		$this->startTrans();
		$this->db->insert_batch($this->tableName, $data);
		$success = $this->endTransWithSucc();

		return $success;
	}

	public function copyExternalNotesByWalletAccountId(){
		$this->db->select('*');
		$this->db->from('walletaccount');
		$this->db->where('showNotesFlag', TRUE);
		$query = $this->runMultipleRow();

		$data = [];
		foreach ($query as $value) {
			$this->db->select('transaction_notes.*');
			$this->db->from('transaction_notes');
			$this->db->where('transaction_notes.transaction_id', $value->walletAccountId);
			$this->db->where('transaction_notes.transaction', 'withdrawal');
			$rows = $this->runMultipleRowArray();

			if(!is_array($rows)){
				continue;
			}

			$last = end($rows);
			$dup = array(
				'walletAccountId' => $last['transaction_id'],
				'note_type' => self::EXTERNAL_NOTE,
				'content' => $last['note'],
				'created_at' => $last['create_date'],
				'created_by' => $last['admin_user_id'],
			);
			$data[] = $dup;
		}

		$this->startTrans();
		$this->db->insert_batch($this->tableName, $data);
		$success = $this->endTransWithSucc();

		return $success;
	}

	public function getLatestNoteByWalletAccountId($note_type, $walletAccountId) {
		$rows=$this->getWalletAccountNotes($note_type, $walletAccountId);
		if(empty($rows)) {
			return '';
		}
		$lastRow = array();
		$lastRow[] = end($rows);
		return $this->formatWithdrawalNotes($lastRow);
	}
}
