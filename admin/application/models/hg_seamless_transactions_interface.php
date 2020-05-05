<?php
interface HogamingSeamlessTransactionsInterface
{
    public function isRowIdAlreadyExists($request_id);

    public function isTransactionIdAlreadyExists($transaction_id, $action);

    public function getFailedTransaction($bet_id);
}
