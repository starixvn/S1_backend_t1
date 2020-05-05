<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Pragmaticplay_seamless_wallet_transactions extends BaseModel {

    const TRANSACTION_ROLLBACK = 'rollback';
    const TRANSACTION_CANCELLED = 'cancel';

    const TRANSACTION_CREDIT = 'credit';
    const TRANSACTION_DEBIT = 'debit';

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "pragmaticplay_seamless_wallet_transactions";

    public function searchByExternalTransactionIdByTransactionType($transaction_id) {

        $this->db->from($this->tableName)->where('transaction_id', $transaction_id)
            ->order_by('created_at', 'asc');

        $query = $this->db->get();

        if ($query && $query->num_rows() >= 1) {
            return $query->result_array();
        }
        else {
            return [];
        }
    }

    public function cancelTransaction($transaction_id) {
        $this->db->where('transaction_id', $transaction_id)
            ->set([
                'transaction_type' => self::TRANSACTION_CANCELLED
            ]);

        $this->runAnyUpdate($this->tableName);
    }

    public function insertTransaction($params) {
        $inserted = $this->db->insert($this->tableName, [
            'user_id' => $params['user_id'],
            'game_id' => $params['game_id'],
            'round_id' => $params['round_id'],
            'amount' => $params['amount'],
            'transaction_id' => $params['transaction_id'],
            'transaction_type' => $params['transaction_type'],
            'provider_id' => $params['provider_id'],
            'timestamp' => $params['timestamp'],
            'round_details' => $params['round_details'],
            'jackpot_id' => $params['jackpot_id'],
            'before_balance' => $params['before_balance'],
            'after_balance' => $params['after_balance'],
            'campaign_id' => $params['campaign_id'],
            'campaign_type' => $params['campaign_type'],
            'currency' => $params['currency'],
            'external_uniqueid' => $params['external_uniqueid'],
        ]);

        return $inserted;
    }
}