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
class Sale_orders_notes extends BaseModel {
	protected $tableName = 'sale_orders_notes';

	const ACTION_LOG    = 1;
	const INTERNAL_NOTE = 2;
	const EXTERNAL_NOTE = 3;
	const PLAYER_NOTES  = 4;

	public function __construct() {
		parent::__construct();
	}

	# $transaction can be 'withdrawal' or 'deposit'
	# $params can contain the following:
	# before_status
	# after_status
	public function add($note, $created_by, $note_type, $saleOrderId, $stages_name = null, $params = array()) {
		if(empty($note)){
			$note='N/A';
		}

		$data['content'] = $note;
		$data['created_by'] = $created_by;
		$data['created_at'] = $this->utils->getNowForMysql();
		$data['note_type'] = $note_type;
		$data['sale_order_id'] = $saleOrderId;
		$data['status_name'] = $stages_name;

		// $data = array_merge($data, $params);
		$this->db->where('id', $saleOrderId);
		$this->db->update('sale_orders', array('updated_at' => $data['created_at']));

		return $this->insertRow($data);
	}

	/**
	 * get notes by type and id
	 * @param  string $transaction   type
	 * @param  int $transactionId id
	 * @return string
	 */
	public function getNotesByNoteType($note_type, $saleOrderId, $display_last_notes = false, $donotShowUsername = false, $limit_the_number_of_words_displayed = false) {
		$rows=$this->getBySaleOrdersNotes($note_type, $saleOrderId);
		$notes='';
		if(!empty($rows)){
			if($limit_the_number_of_words_displayed){
				$notes = $this->formatDepositLessNotes($rows, $saleOrderId, $note_type);
			}else{
				$notes=$this->formatDepositNotes($rows, $display_last_notes, $donotShowUsername);
			}
		}
		return $notes;
	}

	/**
	 * get row(s) by type, transaction id, status
	 * @param  string $transaction   type
	 * @param  int $transactionId id
	 * @return string
	 */
	public function getSaleOrdersNotesBySaleOrderId($saleOrderId, $note_type = null) {
		$this->db->select($this->tableName.'.*');
		$this->db->from($this->tableName);
		$this->db->where('sale_order_id', $saleOrderId);
		if (!empty($note_type)) {
			$this->db->where('note_type', $note_type);
		}

		$this->db->order_by('created_at');

		return $this->runMultipleRowArray();
	}

    public function checkNoteExist($saleOrderId, $msg){
        $notes = $this->getSaleOrdersNotesBySaleOrderId($saleOrderId, Sale_orders_notes::ACTION_LOG);
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

	public function getBySaleOrdersNotes($note_type, $saleOrderId) {
		switch($note_type){
			case self::PLAYER_NOTES:
				$this->db->select($this->tableName.'.created_at, player.username as creater_name, content');
				$this->db->from($this->tableName);
				$this->db->join('player', $this->tableName.'.created_by = player.playerId');
				$this->db->where('sale_order_id', $saleOrderId);
				break;
			case self::INTERNAL_NOTE:
			case self::EXTERNAL_NOTE:
			case self::ACTION_LOG:
				$this->db->select($this->tableName.'.created_at, adminusers.realname as creater_name, content, status_name');
				$this->db->from($this->tableName);
				$this->db->join('adminusers', $this->tableName.'.created_by = adminusers.userId');
				$this->db->where('sale_order_id', $saleOrderId);
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
	 * @param  array $transactionNotes
	 * @return string
	 */
	public function formatDepositNotes($depositNotes, $display_last_notes = false, $donotShowUsername = false) {
		$noteString = '';
		if(!empty($depositNotes)){
			if ($display_last_notes) {
				$aNote = end($depositNotes);
				if($aNote) {
					$noteString .= sprintf("[%s] %s: %s</br>", $aNote['created_at'], $aNote['creater_name'], $aNote['content']);
				}
			} else {
				foreach ($depositNotes as $aNote) {
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

	public function formatDepositLessNotes($depositNotes, $saleOrderId, $note_type){
		$limit = $this->utils->getConfig('limit_the_number_of_words_displayed');
		$fewerStrings = '';
		foreach ($depositNotes as $aNote) {
			$content_length = mb_strlen(str_replace('</br>', "\n", $aNote['content']), "utf-8");

			if($limit >= 0){
				$limitLength  = $limit;
			}

			$limit = $limit - $content_length;

			// if($aNote['status_name'] == null){
			// 	$aNote['status_name'] = lang('no status');
			// }

			if(abs($limit) < 50 &&  $limit >= 0){

				$fewerStrings .= sprintf("[%s] %s: %s</br>", $aNote['created_at'], $aNote['creater_name'], $aNote['content']);

			}else if ($limit<0){

				$contentString = mb_substr($aNote['content'], 0, $limitLength , "utf-8").'<a href="javascript:void(0)" class="" id="btn_show_all_notes" style="cursor:" onclick="showDetialNotes('.$saleOrderId.','.$note_type.')">'.' ...'.lang('More');

				$fewerStrings .= sprintf("[%s] %s: %s</br>", $aNote['created_at'], $aNote['creater_name'], $contentString);
				break;
			}
		}
		return $fewerStrings;
	}

	public function batchCopyDataFromSaleOrders(){
		$this->db->select('*');
		$this->db->from('sale_orders');
		$this->db->where('reason IS NOT NULL');
		$this->db->or_where('notes IS NOT NULL');

		$query = $this->runMultipleRow();

		$sql = $this->db->last_query();
		$this->utils->debug_log('========================get sale_orders sql ' . $sql);

		$data         = [];
		$id           = '';
		$note_type    = '';
		$content      = '';
		$created_time = '';
		$created_by   = '';

		foreach ($query as $value) {
			$id                      = $value->id;
			$notes                   = $value->notes;
			$reason                  = $value->reason;
			$created_at              = $value->created_at;
			$processed_approved_time = $value->processed_approved_time;
			$processed_by            = $value->processed_by;

			# handle data in `notes`
			if(!empty($notes)){
				$notes_array = explode('|', $notes);
				foreach ($notes_array as $content) {
					$content = trim($content);
					if(empty($content)){
						continue;
					}

					switch ($content) {
						case strpos($content, 'Manual Deposit') !== FALSE:
						case strpos($content, 'New Deposit') !== FALSE:
						case strpos($content, 'create ') !== FALSE:
							$note_type = self::ACTION_LOG;
							$created_time = $created_at;
							$created_by = Users::SUPER_ADMIN_ID;
							break;

						case strpos($content, 'diff amount') !== FALSE:
						case strpos($content, 'batch') !== FALSE:
							$note_type = self::ACTION_LOG;
							$created_time = empty($processed_approved_time) ? $created_at : $processed_approved_time;
							$created_by = Users::SUPER_ADMIN_ID;
							break;

						default:
							$note_type = self::INTERNAL_NOTE;
							$created_time = $created_at;
							$created_by = empty($processed_by) ? Users::SUPER_ADMIN_ID : $processed_by;
							break;
					}

					$str = array(
						'sale_order_id' => $id,
						'note_type' => $note_type,
						'content' => trim($content),
						'created_at' => $created_time,
						'created_by' => $created_by
					);
					$data[] = $str;
				}
			}

			# handle data in `reason`
			if(!empty($reason)){
				$reason_array = explode('|', $reason);
				foreach ($reason_array as $content) {
					$content = trim($content);
					if(empty($content)){
						continue;
					}

					switch ($content) {
						case strpos($content, 'callback') !== FALSE:
						case strpos($content, 'batch') !== FALSE:
						case strpos($content, 'Resend') !== FALSE:
							$note_type = self::ACTION_LOG;
							$created_time = $processed_approved_time;
							$created_by = Users::SUPER_ADMIN_ID;
							break;

						case strpos($content, 'Player Bank Name   :') !== FALSE:
						case strpos($content, 'Player Bank Number :') !== FALSE:
						case strpos($content, 'Player Email 	  :') !== FALSE:
						case strpos($content, 'Player Phone 	  :') !== FALSE:
						case strpos($content, 'Cardholder Name    :') !== FALSE:
							$note_type = self::ACTION_LOG;
							$created_time = $processed_approved_time;
							$created_by = Users::SUPER_ADMIN_ID;
							break;

						default:
							$note_type = self::INTERNAL_NOTE;
							$created_time = $created_at;
							$created_by = empty($processed_by) ? Users::SUPER_ADMIN_ID : $processed_by;
							break;
					}

					$str = array(
						'sale_order_id' => $id,
						'note_type' => $note_type,
						'content' => trim($content),
						'created_at' => $created_time,
						'created_by' => $created_by
					);
					$data[] = $str;
				}

				if($value->show_reason_to_player){
					$note_type = self::EXTERNAL_NOTE;
					$dup = array(
						'sale_order_id' => $id,
						'note_type' => self::EXTERNAL_NOTE,
						'content' => array_pop($reason_array),
						'created_at' => $created_at,
						'created_by' => empty($processed_by) ? Users::SUPER_ADMIN_ID : $processed_by
					);
					$data[] = $dup;
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
		$this->db->join('sale_orders','transaction_notes.transaction_id = sale_orders.id');
		$this->db->where('transaction', 'deposit');

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
			if(empty($content)){
				continue;
			}

			switch ($content) {
				case strpos($content, 'Deposit status') !== FALSE:
				case strpos($content, '存款状态') !== FALSE:
				case strpos($content, 'Trang thái gửi tiền') !== FALSE:
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
				'sale_order_id' => $id,
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

	public function getLatestNoteByTransaction($transaction, $transactionId) {
		$rows=$this->getBySaleOrdersNotes($transaction, $transactionId);
		if(empty($rows)) {
			return '';
		}
		$lastRow = array();
		$lastRow[] = end($rows);
		return $this->formatTransNotes($lastRow);
	}
}
