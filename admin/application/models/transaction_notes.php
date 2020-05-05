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
class Transaction_notes extends BaseModel {
	protected $tableName = 'transaction_notes';

	const TRANS_WITHDRAWAL = 'withdrawal';
	const TRANS_DEPOSIT = 'deposit';

	public function __construct() {
		parent::__construct();
	}

	# $transaction can be 'withdrawal' or 'deposit'
	# $params can contain the following:
	# before_status
	# after_status
	public function add($note, $adminUserId, $transaction, $transactionId, $params = array()) {
		if(empty($note)){
			$note='N/A';
		}

		//transaction_id is not transaction.id ,it walletaccount.walletAccountId
		$data['note'] = $note;
		$data['admin_user_id'] = $adminUserId;
		$data['create_date'] = $this->utils->getNowForMysql();
		$data['transaction'] = $transaction;
		$data['transaction_id'] = $transactionId;

		$data = array_merge($data, $params);
		return $this->insertRow($data);
	}

	/**
	 * get notes by type and id
	 * @param  string $transaction   type
	 * @param  int $transactionId id
	 * @return string
	 */
	public function getNotesByTransaction($transaction, $transactionId, $display_last_notes = false) {
		$rows=$this->getByTransaction($transaction, $transactionId);
		$notes='';
		if(!empty($rows)){
			$notes=$this->formatTransNotes($rows, $display_last_notes);
		}
		return $notes;
	}

	/**
	 * get row(s) by type, transaction id, status
	 * @param  string $transaction   type
	 * @param  int $transactionId id
	 * @return string
	 */
	public function getTransactionNotesByTransactionId($transactionId, $transaction = null, $status = null) {
		$this->db->select($this->tableName.'.*');
		$this->db->from($this->tableName);
		$this->db->where('transaction_id', $transactionId);
		if (!empty($transaction)) {
			$this->db->where('transaction', $transaction);
		}
		if (!empty($status)) {
			$this->db->where('after_status', $status);
			$this->db->order_by('create_date','desc');
			$row = $this->runOneRowArray();
			return $row;
		}

		$this->db->order_by('id');

		return $this->runMultipleRowArray();
	}

    public function checkNoteExist($transId, $msg){
        $notes = $this->getTransactionNotesByTransactionId($transId, Transaction_notes::TRANS_WITHDRAWAL);
        if(strpos($msg, "valid hour") !== false){
            $msg = "Wrong callback, callback time over valid hour.";
        }

        if(is_array($notes)){
	        foreach ($notes as $note) {
	            if(strpos($note['note'], $msg) !== false){
	                return true;
	            }
	        }
	    }
        return false;
    }

	public function getByTransaction($transaction, $transactionId) {
		$this->db->select($this->tableName.'.*, adminusers.realname as admin_name');
		$this->db->from($this->tableName);
		$this->db->join('adminusers', $this->tableName.'.admin_user_id = adminusers.userId');
		$this->db->where('transaction_id', $transactionId);

		if (!empty($transaction)) {
			$this->db->where('transaction', $transaction);
		}

		$this->db->order_by('id');
		return $this->runMultipleRowArray();
	}

	/**
	 * format
	 * @param  array $transactionNotes
	 * @return string
	 */
	public function formatTransNotes($transactionNotes,$display_last_notes = false) {
		$noteString = '';
		if(!empty($transactionNotes)){
			if ($display_last_notes) {
				$aNote = end($transactionNotes);
				if($aNote) {
					$noteString .= sprintf("[%s] %s: %s</br>", $aNote['create_date'], $aNote['admin_name'], $aNote['note']);
				}
			} else {
				foreach ($transactionNotes as $aNote) {
					$noteString .= sprintf("[%s] %s: %s</br>", $aNote['create_date'], $aNote['admin_name'], $aNote['note']);
				}
			}
		}
		return $noteString;
	}

	public function getLatestNoteByTransaction($transaction, $transactionId) {
		$rows=$this->getByTransaction($transaction, $transactionId);
		if(empty($rows)) {
			return '';
		}
		$lastRow = array();
		$lastRow[] = end($rows);
		return $this->formatTransNotes($lastRow);
	}

	public function getNoteByTransaction($transaction, $transactionId) {
		//transaction_id is not transaction.id ,it walletaccount.walletAccountId
		if(!$this->chkShow($transactionId)){
			return $transactionId;
		}else{
			$rows=$this->getByTransaction($transaction, $transactionId);
			if(empty($rows)) {
				return '';
			}
			$lastRow = array();
			$lastRow[] = end($rows);
			return $this->formatTransNotes($lastRow);
			/*
			$rows=$this->getByTransaction($transaction, $transactionId);
			if(empty($rows)) {
				return '';
			}
			$str ="";
			foreach($rows as $k=>$v){
				$str .=$v['note']."<br>";

			}
			return $str;*/

		}
	}

	/**
	 * detail: add notes by batches
	 *
	 * @param array $data
	 * @return Boolean
	 */
	public function addByBatch( $data = array() ){

		return $this->db->insert_batch($this->tableName, $data);

	}

	/**
	 * detail: update ShowNoteTo player
	 *
	 * @param array $data
	 * @return Boolean
	 */
	public function updateShowNote($rs){
		/*

		$walletAccountId = $rs['walletAccountId'];
		$showNotesFlag = $rs['showNotesFlag'];
		$dataRequest = array('showNotesFlag' => $showNotesFlag);
		*/

		//get playerId by walletAccountId
		$this->db->where('walletAccountId', $rs['walletAccountId']);
		$this->db->update('walletaccount', $rs);

	}

	public function chkShow($walletAccountId){

		$tb ="walletaccount";
		$this->db->select('showNotesFlag');
		$this->db->from($tb);
		$this->db->where(array(
			'walletAccountId' => $walletAccountId
		));
		$chkObj = $this->runOneRow();
		if(is_null($chkObj)){
			return false;
		}else{
			$flg = $chkObj->showNotesFlag;
			switch($flg){
				case "1":
				case "true":
					return true;
				break;
				default:
					return false;
				break;
			}//end switch
		}//end else
	}

	public function getWithdrawalTransactionNotes($transaction, $transactionId, $show_admin_name=true, $hide_empty_note=false) {
		$transactionNotes=$this->getByTransaction($transaction, $transactionId);
		$notes='';
		if(!empty($transactionNotes)){
			foreach ($transactionNotes as $note) {

				if($hide_empty_note && (empty($note['note']) || trim($note['note'])=='N/A')){
					continue;
				}

				if($show_admin_name){
					$notes .= $note['admin_name']. ' : '.$note['note'].'<br/>';
				}else{
					$notes .= $note['note'].'<br/>';
				}
			}
		}
		return $notes;
	}

}
