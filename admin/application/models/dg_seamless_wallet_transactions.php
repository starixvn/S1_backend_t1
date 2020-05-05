<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class dg_seamless_wallet_transactions extends BaseModel {

    const TRANSACTION_ROLLBACK = 'rollback';
    const TRANSACTION_NORMAL = 'normal';
    const TRANSACTION_CANCELLED = 'cancel';

    const TRANSACTION_CREDIT = 'credit';
    const TRANSACTION_DEBIT = 'debit';

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "dg_seamless_wallet_transactions";

    public function searchByExternalTransactionId($external_transaction_id) {

        $this->db->from($this->tableName)->where('external_transaction_id', $external_transaction_id);

        $query = $this->db->get();

        if ($query && $query->num_rows() == 1) {
            return $this->getOneRowArray($query);
        }
        else if ($query && $query->num_rows() > 1) {
            return $query->result_array();
        }
        else {
            return [];
        }
    }

    public function searchByTransactionIdAndType($external_transaction_id, $transaction_type, $allow_duplicate_trans_id = false) {

        $this->db->from($this->tableName)
            ->where('external_transaction_id', $external_transaction_id);
        if($allow_duplicate_trans_id) {
            $this->db->where('transaction_type', $transaction_type);
        }

        return $this->runOneRowArray();
    }

    public function searchTicketTransactions($ticket_id) {

        $this->db->from($this->tableName)->where('ticket_id', $ticket_id)
            ->where('transaction_type != ', self::TRANSACTION_CANCELLED)
            ->where('transaction_type != ', self::TRANSACTION_ROLLBACK);

        return $this->runMultipleRow();
    }

    public function isCancelledTransaction($external_transaction_id) {
        $this->db->from($this->tableName)
            ->where('external_transaction_id', $external_transaction_id)
            ->where('transaction_type', self::TRANSACTION_CANCELLED);

        return !empty($this->runOneRowArray());
    }


    public function cancelTransaction($external_transaction_id) {
        $this->db->where('external_transaction_id', $external_transaction_id)
            ->set([
                'transaction_type' => self::TRANSACTION_CANCELLED
            ]);

        $this->runAnyUpdate($this->tableName);
    }


    public function searchAndGroupByTicketId($ticket_id, $hide_zero = false) {

        $this->db->from($this->tableName)
            ->where('ticket_id', $ticket_id)
            ->groupBy('ticket_id')
            ->select('ticket_id, external_transaction_id, sum(amount) as sum_amount')
            ->orderBy('updated_at', 'desc');

        if($hide_zero)

        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function insertTransaction($params) {
        $inserted = $this->db->insert($this->tableName, [
            'player_id' => $params->member->player_id,
            'before_balance' => $params->before_balance,
            'amount' => $params->member->amount,
            'after_balance' => $params->after_balance,
            'ticket_id' => $params->ticketId,
            'transaction_type' => $params->transaction_type,
            'external_transaction_id' => $params->data,
            'unique_transaction_id' => "{$params->data}-{$params->ticketId}-" . abs($params->member->amount) . "-{$params->transaction_type}"
        ]);

        return $inserted;
    }
}