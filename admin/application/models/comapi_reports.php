<?php

require_once dirname(__FILE__) . '/base_model.php';
/**
 * Model for player reports used by Api_common (player center API; Common API)
 *
 * @used-by	Api_common::getPlayerReports()
 * @see		Api_common::getPlayerReports()
 */
class Comapi_reports extends BaseModel {

    protected $withdraw_statuses;
    protected $deposit_pay_types;

    const PLAYER_ACCOUNTS_DEPOSIT       = 100;
    const PLAYER_ACCOUNTS_WITHDRAWAL    = 101;

    public function __construct() {
        parent::__construct();
        $this->load->model('payment_account');
        $this->withdraw_statuses = [
            'request'   => lang('Request') ,
            'approved'  => lang('Approved') ,
            'declined'  => lang('Declined') ,
            'paid'      => lang('Paid') ,
            '__default' => lang('Processing') ,
        ];

        $this->deposit_pay_types = [
            Payment_account::FLAG_MANUAL_LOCAL_BANK => lang('pay.local_bank_offline') ,
            Payment_account::FLAG_MANUAL_ONLINE_PAYMENT => lang('pay.manual_online_payment') ,
            Payment_account::FLAG_AUTO_ONLINE_PAYMENT => lang('pay.auto_online_payment')
        ];
    }

    /**
     * Returns records of player's active promotions
     * @param   int         $player_id      == player.playerId
     * @param   datetime    $time_start     Start time of query
     * @param   datetime    $time_end       End time of query
     * @param   integer     $limit          Paging.  Count of returned records.
     * @param   integer     $offset         Paging.  Start index of returned records.
     * @return  array       Row array of query records
     */
    public function playerActivePromoDetails($player_id, $time_start, $time_end, $limit = 50, $offset = 0, $include_declined_promos = false){
        $this->load->model(array('player_promo'));

        $promo_count = $this->player_promo->getPlayerPromoHistoryWLimitById_api($player_id, null, null, $time_start, $time_end, true, $include_declined_promos);

        $promo_res = $this->player_promo->getPlayerPromoHistoryWLimitById_api($player_id, $limit, $offset, $time_start, $time_end, false, $include_declined_promos);

        $result = [];
        if (is_array($promo_res)) {
            foreach ($promo_res as $prow) {
                $row = [
                    // 'promo_name' => $prow['fullPromoDesc'] ,
                    'promo_name'    => $prow['promoName'] ,
                    'promo_type'    => $prow['promoTypeName'] ,
                    'date'          => $prow['dateApply'] ,
                    'deposit_amount'=> $prow['depositAmount'] ,
                    'bonus_amount'  => $prow['bonusAmount'] ,
                    'status'        => $prow['status'] ,
                    'id'            => $prow['playerpromoId']
                ];
                $result[] = $row;
            }
        }

        return [ 'row_count_total' => $promo_count, 'rows' => $result ];
    }

    /**
     * DISUSED
     * @deprecated
     */
    // public function playerTransferRequests0($playerId, $time_start, $time_end, $limit = 15, $offset = 0){

    //     $locale = [
    //         'main_to_sub' => lang('transaction.transaction.type.5') ,
    //         'sub_to_main' => lang('transaction.transaction.type.6') ,
    //     ];

    //     $this->db
    //         ->from("transfer_request AS TR")
    //         ->join('player AS P', 'P.playerId = TR.player_id', 'left')
    //         ->join('external_system AS S', 'S.id = TR.external_system_id', 'left')
    //         ->where('TR.player_id', $playerId)
    //         ->where("TR.updated_at BETWEEN '{$time_start}' AND '{$time_end}'", null, false)
    //         ->select([
    //         // 'transfer_request.id as id' ,
    //         // 'transfer_request.secure_id as secure_id' ,
    //         // 'transfer_request.from_wallet_type_id as from_wallet_type' ,
    //         'TR.updated_at AS date' ,
    //         // 'transfer_request.to_wallet_type_id as to_wallet_type' ,
    //         "IF(TR.from_wallet_type_id = 0, '{$locale['main_to_sub']}', '{$locale['sub_to_main']}') AS transfer_type" ,
    //         'TR.amount AS amount' ,
    //         'S.system_code AS sub_wallet'
    //         // 'transfer_request.status as status' ,
    //         // 'transfer_request.created_at as created_at' ,
    //         ])
    //     ;

    //     $this->db->order_by('updated_at', 'desc');

    //     if($limit>15) $limit = 15;
    //     $this->db->limit($limit, $offset);

    //     $rows = $this->runMultipleRowArray();
    //    $this->utils->debug_log('api_playerTransferRequest->sql', $this->db->last_query());

    //     return $rows;
    // } // end function playerTransferRequest()

    /**
     * Returns records of player's transfer between subwallets
     * @param   int         $player_id      == player.playerId
     * @param   datetime    $time_start     Start time of query
     * @param   datetime    $time_end       End time of query
     * @param   integer     $limit          Paging.  Count of returned records.
     * @param   integer     $offset         Paging.  Start index of returned records.
     * @return  array       Row array of query records
     */
    public function playerTransferRequests($player_id, $time_start, $time_end, $limit = 50, $offset = 0){

        // OGP-15774: Obtain count first
        $this->db
            ->from('transactions AS T')
            ->join('external_system AS S', 'T.sub_wallet_id = S.id')
            ->select('count(*) AS count')
            ->where("T.created_at BETWEEN '{$time_start}' AND '{$time_end}'")
            ->where_in('T.transaction_type', [ Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET, Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET ])
            ->where("( T.from_id = '{$player_id}' OR T.to_id = '{$player_id}' )")
        ;

        $count = $this->runOneRowOneField('count');

        // Then obtain rows
        $this->db
            ->from('transactions AS T')
            ->join('external_system AS S', 'T.sub_wallet_id = S.id')
            ->select([
                'T.created_at AS date' ,
                'T.transaction_type AS transaction_type' ,
                'T.amount' ,
                'T.before_balance' ,
                'T.after_balance' ,
                'S.system_code AS subwallet'
            ])
            ->where("T.created_at BETWEEN '{$time_start}' AND '{$time_end}'")
            ->where_in('T.transaction_type', [ Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET, Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET ])
            ->where("( T.from_id = '{$player_id}' OR T.to_id = '{$player_id}' )")
        ;

        $this->db->order_by('T.created_at', 'desc');

        // if($limit>50) $limit = 50;
        $this->db->limit($limit, $offset);

        $rows = $this->runMultipleRowArray();

        if (!empty($rows)) {
            foreach ($rows as & $row) {
                $row['transaction_type'] = lang("transaction.transaction.type.{$row['transaction_type']}");
                $row['subwallet'] = empty($row['subwallet']) ? lang('lang.norecyet') : $row['subwallet'];
            }
        }

        $this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

        return [ 'row_count_total' => $count, 'rows' => $rows ];
    } // end function playerTransferRequest()

   /**
     * For only auto cashback records (no including immediately cashback)
     *
     * @param $player_id
     * @param $time_start
     * @param $time_end
     * @param $limit
     * @param $offset
     * @return mixed
     */
    // public function getCashbackRequestRecords_obsolete($player_id, $time_start, $time_end, $limit = 15, $offset = 0){
    //     $this->db->select("
    //         cashback_request.id as id,
    //         cashback_request.request_datetime as request_datetime,
    //         cashback_request.request_amount as request_amount,
    //         cashback_request.status as status,
    //         cashback_request.processed_datetime as processed_datetime,
    //         cashback_request.notes as notes,
    //         cashback_request.created_at as created_at"
    //     );

    //     $this->db->from("cashback_request");

    //     $this->db->join('player', 'player.playerId = cashback_request.player_id', 'left');

    //     $this->db->where('cashback_request.player_id', $player_id);
    //     $this->db->where("cashback_request.request_datetime BETWEEN '{$time_start}' AND '{$time_end}'");

    //     $this->db->order_by('id', 'desc');

    //     if($limit>15) $limit = 15;
    //     $this->db->limit($limit, $offset);

    //     $rows = $this->runMultipleRowArray();

    //     $this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

    //     return $rows;
    // }

    /**
     * Returns records of player's auto cashbacks (w/o immediate cashbacks)
     * @param   int         $player_id      == player.playerId
     * @param   datetime    $time_start     Start time of query
     * @param   datetime    $time_end       End time of query
     * @param   integer     $limit          Paging.  Count of returned records.
     * @param   integer     $offset         Paging.  Start index of returned records.
     * @return  array       Row array of query records
     */
   public function playerCashbackRequestRecords($player_id, $time_start, $time_end, $limit = null, $offset = null){

       $this->db
            ->select('COUNT(*) AS count')
            ->where("CBD.player_id", $player_id)
            ->where("CBD.paid_date BETWEEN '{$time_start}' AND '{$time_end}'", null, false)
            ->where("CBD.paid_flag", 1)
            ->from('total_cashback_player_game_daily AS CBD')
            ->join('player AS P', 'CBD.invited_player_id = P.playerId', 'left')
        ;

        if (!$this->utils->isEnabledFeature('enable_friend_referral_cashback')) {
            $this->db->where('CBD.cashback_type', 1); // Group_level::NORMAL_CASHBACK = 1
        }


        $count = $this->runOneRowOneField('count');

        $this->db
            ->select([
                'CBD.total_date AS date' ,
                'CBD.amount' ,
                'CBD.paid_date'
            ])
            ->where("CBD.player_id", $player_id)
            ->where("CBD.paid_date BETWEEN '{$time_start}' AND '{$time_end}'", null, false)
            ->where("CBD.paid_flag", 1)
            ->from('total_cashback_player_game_daily AS CBD')
            ->join('player AS P', 'CBD.invited_player_id = P.playerId', 'left')
            ->order_by('paid_date', 'desc');
        ;

        if (!$this->utils->isEnabledFeature('enable_friend_referral_cashback')) {
            $this->db->where('CBD.cashback_type', 1); // Group_level::NORMAL_CASHBACK = 1
        }
        else {
            $this->db->select('CASE
                    WHEN CBD.cashback_type = 1 THEN "normal"
                    WHEN CBD.cashback_type = 2 THEN "friend_referral"
                    ELSE "normal"
                END AS cashback_type', false)
                ->select('P.username');
        }

        // if($limit>50) {$limit = 50;}
        if (!empty($limit)) {
            $this->db->limit($limit, $offset);
        }

        $rows = $this->runMultipleRowArray();

        $this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

        return [ 'row_count_total' => $count, 'rows' => $rows ];
    } // End function getCashbackRequestRecords()

    // public function pointsHistory($player_id, $time_start, $time_end, $limit = 15, $offset = 0){
    //     $this->load->model(array('point_transactions'));
    //     $result = $this->point_transactions->pointsHistory($player_id, $limit, $offset, ['from'=>$time_start, 'to'=>$time_end]);

    //     return $result;
    // }

    /**
     * Returns records of player's game history
     * @param   int         $player_id      == player.playerId
     * @param   datetime    $time_start     Start time of query
     * @param   datetime    $time_end       End time of query
     * @param   integer     $limit          Paging.  Count of returned records.
     * @param   integer     $offset         Paging.  Start index of returned records.
     * @return  array       Row array of query records
     */
   public function playerGameHistory($player_id, $time_start, $time_end, $limit = 50, $offset = 0, $reveal_unknown = false, $game_platform_id = null, $return_count = false) {


        $this->db->from("game_logs AS L")
            ->join('player AS P', 'P.playerId = L.player_id', 'left')
            ->join('affiliates AS AF', 'AF.affiliateId = P.affiliateId', 'left')
            ->join('game_description AS D', 'D.id = L.game_description_id', 'left')
            ->join('game_type AS T', 'T.id = D.game_type_id', 'left')
            ->join('external_system AS S', 'L.game_platform_id = S.id', 'left')
            ->where('L.player_id', $player_id)
            ->where("L.end_at BETWEEN '{$time_start}' AND '{$time_end}'");

        if (!empty($game_platform_id)) {
            $this->db->where("S.id", $game_platform_id);
        }
        if (!$reveal_unknown) {
            $this->db->where("D.game_code <>", 'unknown');
        }

        if ($return_count) {
            $this->db->select("COUNT(*) AS count");
            $count = $this->runOneRowOneField('count');
            return $count;
        }

        $this->db->select("
            L.id as id,
            S.id as game_platform_id,
            T.game_type as game_type,
            D.game_code as game_code,
            L.end_at as end_at,
            S.system_code as game,
            L.trans_amount as real_bet_amount,
            L.bet_amount as bet_amount,
            L.result_amount as result_amount,
            (L.bet_amount + L.result_amount) as bet_plus_result_amount,
            L.win_amount as win_amount,
            L.loss_amount as loss_amount,
            L.after_balance as after_balance,
            L.trans_amount as trans_amount,
            L.table as roundno,
            L.note as betDetails,
            L.flag as flag ,
            L.table as round_no ,
            L.bet_details
        ");

         $this->db->order_by('id', 'desc');

        // if($limit>50) $limit = 50;
        $this->db->limit($limit, $offset);

        $rows = $this->runMultipleRowArray();

        $this->utils->printLastSQL();

        return $rows;
    } // End function gamesHistory()

    /**
     * Returns records of player's withdrawals
     * @param   int         $player_id      == player.playerId
     * @param   datetime    $time_start     Start time of query
     * @param   datetime    $time_end       End time of query
     * @param   integer     $limit          Paging.  Count of returned records.
     * @param   integer     $offset         Paging.  Start index of returned records.
     * @return  array       Row array of query records
     */
    public function player_withdrawals($player_id, $time_start, $time_end, $limit = 50, $offset = 0){

         $this->db
            ->from('walletaccount')
            ->select('COUNT(*) AS count')
            ->where('transactionType', 'withdrawal')
            ->where('playerId', $player_id)
            ->where("dwDatetime BETWEEN '{$time_start}' AND '{$time_end}'", null, false)
        ;

        $count = $this->runOneRowOneField('count');

        $this->db
            ->from('walletaccount')
            ->select([
               'dwDateTime AS date' ,
               'amount' ,
               'transactionCode as tx_code' ,
               'dwStatus AS status' ,
               'walletAccountId' ,
            ])

            ->where('transactionType', 'withdrawal')
            ->where('playerId', $player_id)
            ->where("dwDatetime BETWEEN '{$time_start}' AND '{$time_end}'", null, false)

            ->order_by('dwDatetime', 'desc')
        ;


        // if($limit>50) $limit = 50;

        $this->db->limit($limit, $offset);

        $rows = $this->runMultipleRowArray();

        if (!empty($rows)) {
            $this->load->model('walletaccount_notes');
            foreach ($rows as $key => & $row) {
                $row['note'] = $this->walletaccount_notes->getNotesByNoteType(Walletaccount_notes::EXTERNAL_NOTE, $row['walletAccountId'], false, true, false);
                $row['status'] = $this->player_withdrawal_format_status($row['status']);
                unset($row['walletAccountId']);
            }
        }

        $this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

        return [ 'row_count_total' => $count, 'rows' => $rows ];
    } // End function list_withdrawals()

    /**
     * Formats withdrawal status into localized text
     * @param   string  $status  == walletaccount.dwStatus
     * @uses    Comapi_reports::withdraw_statuses
     * @return  string  Localized status text
     */
    protected function player_withdrawal_format_status($status) {
        return isset($this->withdraw_statuses[$status]) ? $this->withdraw_statuses[$status] : $this->withdraw_statuses['__default'];
    }

    /**
     * Returns records of player's deposits, older version
     * Superseded by ::player_deposits(); only kept for compatibility issues
     *
     * @param   int         $player_id      == player.playerId
     * @param   datetime    $time_start     Start time of query
     * @param   datetime    $time_end       End time of query
     * @param   integer     $limit          Paging.  Count of returned records.
     * @param   integer     $offset         Paging.  Start index of returned records.
     * @return  array       Row array of query records
     */
    public function player_deposits2($player_id, $time_start, $time_end, $limit = 50, $offset = 0){
        $this->db
            ->from('sale_orders AS S')
            // ->join('transactions AS T', 'S.transaction_id = T.id', 'left')
            ->join('player AS P', 'P.playerId = S.player_id', 'left')
            // ->join('playerdetails AS PD', 'PD.playerId = S.player_id', 'left')
            // ->join('adminusers AS AU', 'S.processed_by = AU.userId', 'left')
            // ->join('playerpromo AS PP', 'PP.playerpromoId = S.player_promo_id', 'left')
            // ->join('promorules AS PR', 'PP.promorulesId = PR.promorulesId', 'left')
            ->join('payment_account AS PA', 'PA.id = S.payment_account_id', 'left')
            // ->join('affiliates AS AF', 'AF.affiliateId = P.affiliateId', 'left')

            ->select([
                'S.secure_id as secure_id' ,
                'PA.flag as payment_account_flag' ,
                'S.created_at as request_date' ,
                'S.updated_at as pay_date' ,
                'S.amount as amount' ,
                'S.player_payment_type_name as player_payment_type_name' ,
                'S.status AS status'
            ])

            ->where('S.payment_kind', Sale_order::PAYMENT_KIND_DEPOSIT)
            ->where('S.player_id', $player_id)
            ->where("S.created_at BETWEEN '{$time_start}' AND '{$time_end}'", null, false)

            ->order_by('S.created_at', 'desc')
        ;

        // if($limit>50) $limit = 50;
        $this->db->limit($limit, $offset);

        $rows = $this->runMultipleRowArray();

        if (!empty($rows)) {
            foreach ($rows as & $row) {
                $row['pay_bank'] = lang($row['player_payment_type_name']);
                $row['pay_type']    = $this->deposit_format_pay_type($row['payment_account_flag']);
                $row['status']      = lang("sale_orders.status.{$row['status']}");
                unset($row['player_payment_type_name']);
                unset($row['payment_account_flag']);
            }
        }

        $this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

        // $result = [ 'row_count' => $row_count, 'rows' => $rows ];

        return $rows;
    } // End function list_deposits2()

    /**
     * Returns records of player's deposits, new format
     * in adherence of new player center deposit history
     * OGP-15695
     * @see     report_module_transaction::transactionsByWalletDeposit()
     *
     * @param   int         $player_id      == player.playerId
     * @param   datetime    $time_start     Start time of query
     * @param   datetime    $time_end       End time of query
     * @param   integer     $limit          Paging.  Count of returned records.
     * @param   integer     $offset         Paging.  Start index of returned records.
     * @return  array       Row array of query records
     */
    public function player_deposits($player_id, $time_start, $time_end, $limit = 50, $offset = 0){

         $this->db
            ->from('sale_orders AS S')
            ->join('player AS P', 'P.playerId = S.player_id', 'left')
            ->join('payment_account AS PA', 'PA.id = S.payment_account_id', 'left')
            ->join('banktype AS B', 'PA.payment_type_id = B.bankTypeId')
            ->select('COUNT(*) AS count')
            ->where('S.payment_kind', Sale_order::PAYMENT_KIND_DEPOSIT)
            ->where('S.player_id', $player_id)
            ->where("S.created_at BETWEEN '{$time_start}' AND '{$time_end}'", null, false)
        ;

        $count = $this->runOneRowOneField('count');

        $this->db
            ->from('sale_orders AS S')
            ->join('player AS P', 'P.playerId = S.player_id', 'left')
            ->join('payment_account AS PA', 'PA.id = S.payment_account_id', 'left')
            ->join('banktype AS B', 'PA.payment_type_id = B.bankTypeId')

            ->select([
                'S.secure_id as secure_id' ,
                // 'PA.flag as payment_account_flag' ,
                'S.created_at as request_date' ,
                // 'S.updated_at as pay_date' ,
                'S.amount as amount' ,
                // 'S.player_payment_type_name as player_payment_type_name' ,
                'S.status AS status' ,
                'PA.second_category_flag AS cat_flag2' ,
                'S.id AS sale_orders_id' ,
                // 'S.show_reason_to_player' ,
            ])

            ->where('S.payment_kind', Sale_order::PAYMENT_KIND_DEPOSIT)
            ->where('S.player_id', $player_id)
            ->where("S.created_at BETWEEN '{$time_start}' AND '{$time_end}'", null, false)

            ->order_by('S.created_at', 'desc')
        ;

        // if($limit>50) $limit = 50;
        $this->db->limit($limit, $offset);

        $rows = $this->runMultipleRowArray();

        if (!empty($rows)) {
            $this->load->model([ 'sale_orders_notes' ]);
            foreach ($rows as & $row) {
                // $row['pay_bank'] = lang($row['player_payment_type_name']);
                $row['pay_bank']    = null;
                $row['pay_date']    = null;
                $secCats = $this->utils->getPaymentAccountSecondCategoryAllFlagsKV();
                $row['pay_type']    = isset($secCats[$row['cat_flag2']]) ? $secCats[$row['cat_flag2']] : lang('N/A');
                $row['status']      = lang("sale_orders.status.{$row['status']}");
                $row['player_notes']    = $this->sale_orders_notes->getNotesByNoteType(Sale_orders_notes::PLAYER_NOTES, $row['sale_orders_id'], false, true);
                $row['notes']           = $this->sale_orders_notes->getNotesByNoteType(Sale_orders_notes::EXTERNAL_NOTE, $row['sale_orders_id'], false, true);
                unset($row['sale_orders_id']);
                unset($row['cat_flag2']);

                ksort($row);
            }
        }

        $this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

        return [ 'row_count_total' => $count, 'rows' => $rows ];
    } // End function list_deposits()

    /**
     * Formats deposit payment type into localized text
     * @param   string  $pay_flag  == payment_account.flag
     * @uses    Comapi_reports::deposit_pay_types
     * @return  string  Localized status text
     */
    protected function deposit_format_pay_type($pay_flag) {
        return isset($this->deposit_pay_types[$pay_flag]) ? $this->deposit_pay_types[$pay_flag] : $pay_flag;
    }

    /**
     * Old deposit/withdraw history model method based on transactions
     * for Api_common::getPlayerReports
     *
     * @deprecated
     * @replaced-by	::list_deposits()
     * @replaced-by	::list_withdrawals()
     */
	// public function api_transaction_details0($player_id, $time_start, $time_end, $trans_type, $limit = 15, $offset = 0){

	// 	$this->db->select([
	// 		'transactions.id as id',
	// 		'transactions.from_type as from_type',
	// 		'transactions.from_id as from_id',
	// 		'transactions.to_type as to_type',
	// 		'transactions.to_id as to_id',
	// 		'sale_orders.secure_id as order_id',
	// 		'transactions.status as status',
	// 		// 'transactions.created_at as created_at',
 //            'sale_orders.created_at as created_at',
 //            'sale_orders.updated_at as updated_at',
 //            'sale_orders.player_payment_type_name as player_payment_type_name' ,
	// 		'transactions.transaction_type as transaction_type',
	// 		// '(CASE transactions.from_type WHEN 1 THEN fromUser.username WHEN 2 THEN fromPlayer.username WHEN 3 THEN fromAffiliate.username ELSE NULL END) as from_username',
	// 		// '(CASE transactions.to_type WHEN 1 THEN toUser.username WHEN 2 THEN toPlayer.username WHEN 3 THEN toAffiliate.username ELSE NULL END) as to_username',
	// 		'transactions.amount as amount',
	// 		'transactions.before_balance as before_balance',
	// 		'transactions.after_balance as after_balance',
	// 		'external_system.system_code as subwallet',
	// 		'promotype.promoTypeName as promoTypeName',
	// 		'promorules.promoName as promo_name',
	// 		// 'transactions.changed_balance as total_before_balance',
	// 		'transactions.flag as flag',
	// 		'transactions.id as transaction_id',
	// 		'transactions.external_transaction_id as external_transaction_id',
	// 		'transactions.note as note' ,
 //            'payment_account.flag as payment_account_flag'
	// 	]);

	// 	$this->db->from("transactions");

	// 	// $this->db->join('adminusers as fromUser', 'transactions.from_type = 1 AND fromUser.userId = transactions.from_id', 'left');
	// 	$this->db->join('player as fromPlayer', 'transactions.from_type = 2 AND fromPlayer.playerId = transactions.from_id', 'left');
	// 	// $this->db->join('affiliates as fromAffiliate', 'transactions.from_type = 3 AND fromAffiliate.affiliateId = transactions.from_id', 'left');
	// 	// $this->db->join('adminusers as toUser', 'transactions.to_type = 1 AND toUser.userId = transactions.to_id', 'left');
	// 	$this->db->join('player as toPlayer', 'transactions.to_type = 2 AND toPlayer.playerId = transactions.to_id', 'left');
	// 	// $this->db->join('affiliates as toAffiliate', 'transactions.to_type = 3 AND toAffiliate.affiliateId = transactions.to_id', 'left');
	// 	$this->db->join('external_system', 'external_system.id = transactions.sub_wallet_id', 'left');
	// 	$this->db->join('promotype', 'promotype.promotypeId = transactions.promo_category', 'left');
	// 	$this->db->join('playerpromo', 'playerpromo.playerpromoId = transactions.player_promo_id', 'left');
	// 	$this->db->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left');
	// 	$this->db->join('sale_orders', 'sale_orders.transaction_id = transactions.id', 'left');
 //        $this->db->join('payment_account', 'sale_orders.payment_account_id = payment_account.id', 'left');

	// 	$this->db->where("(fromPlayer.playerId = {$player_id} OR toPlayer.playerId = {$player_id})");
 //        $this->db->where("transactions.created_at BETWEEN '{$time_start}' AND '{$time_end}'");

	// 	switch ($trans_type){
 //            case 'deposit':
 //                $this->db->where('transactions.transaction_type', Transactions::DEPOSIT);
 //                break;
 //            case 'withdrawal':
 //                $this->db->where('transactions.transaction_type', Transactions::WITHDRAWAL);
 //                break;
 //        }

	// 	if($limit>15) $limit = 15;

	// 	$this->db->limit($limit, $offset);

	// 	$rows = $this->runMultipleRowArray();

 //        $this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

 //        return $rows;
	// }

    /**
     * Return all accounts (deposit and withdrawal) for player
     * Workhorse for Comapi_reports::getPlayerRiskInfo(), OGP-12222
     *
     * @param   int     $player_id      == player.playerId
     * @see     Comapi_reports::getPlayerRiskInfo()
     * @return  array   assoc array of accounts, [ 'deposit' => [ ... ], 'withdrawal' => [ ...] ]
     */
    public function risk_player_all_accounts($player_id) {
        $acc_res = [];

        $acc_res = [
            'deposit' => $this->get_player_accounts_workhorse($player_id, self::PLAYER_ACCOUNTS_DEPOSIT) ,
            'withdrawal' => $this->get_player_accounts_workhorse($player_id, self::PLAYER_ACCOUNTS_WITHDRAWAL) ,
        ];

        return $acc_res;
    }

    /**
     * Returns player's accounts of specified type
     * Worker routine for Comapi_reports::risk_player_all_accounts()
     *
     * @param   int     $player_id      == player.playerId
     * @param   int     $acc_type       either self::PLAYER_ACCOUNTS_DEPOSIT or self::PLAYER_ACCOUNTS_WITHDRAWAL
     * @return  array   plain array of account records
     */
    protected function get_player_accounts_workhorse($player_id, $acc_type = null) {
        $this->load->model([ 'playerbankdetails' ]);
        $acc_res = [ 0 => 'deposit', 1 => 'withdrawal' ];

        $reveal_fields = [ 'bankName' , 'bankAccountFullName' , 'bankAccountNumber' , 'city' , 'province' , 'branch', 'bankAddress', 'status', 'updatedOn' , 'dwBank' ];
        $bank_types = [ 0 => 'deposit', 1 => 'withdrawal' ];

        switch ($acc_type) {
            case self::PLAYER_ACCOUNTS_DEPOSIT :
                $acc_raw = $this->playerbankdetails->getPlayerDepositBankList($player_id);
                break;
            case self::PLAYER_ACCOUNTS_WITHDRAWAL :
                $acc_raw = $this->playerbankdetails->getPlayerWithdrawalBankList($player_id);
                break;
        }

        // $acc_res = $acc_raw;
        $acc_res = $this->utils->array_select_fields($acc_raw, $reveal_fields);

        if (empty($acc_res)) {
            return [];
        }

        if (is_array($acc_res)) {
            foreach ($acc_res as & $row) {
                $row['bankName'] = lang($row['bankName']);
                $row['acc_type'] = $this->safe_ar($bank_types, $row['dwBank']);
                unset($row['dwBank']);
                ksort($row);
            }
        }

        return $acc_res;
    }

    /**
     * Return player's combined account information (deposits, withdrawals, bonuses)
     * Workhorse for Comapi_reports::getPlayerRiskInfo(), OGP-12222
     *
     * @param   int     $player_id      == player.playerId
     * @see     Comapi_reports::getPlayerRiskInfo()
     * @return  array   assoc array of [ 'deposits' => [ ... ], 'withdrawals' => [ ... ], 'bonuses' => [ ... ] ]
     */
    protected function risk_player_account_stats($player_id) {
        $this->load->library([ 'player_manager' ]);
        $this->load->model([ 'wallet_model', 'player_model', 'transactions', 'player_friend_referral' ]);
        $balance_details = $this->wallet_model->getBalanceDetails($player_id);

        // $mainwallet = ['totalBalanceAmount' => $balanceDetails['main_wallet']];
        // $subwallet = $balanceDetails['sub_wallet'];

        $playeraccount = $this->player_manager->getPlayerAccount($player_id);
        // $current_sys_currency = $this->utils->getCurrentCurrency();

        $first_last_deposit = $this->player_manager->getPlayerFirstLastApprovedTransaction($player_id, Transactions::DEPOSIT);
        $first_last_withdrawal = $this->player_manager->getPlayerFirstLastApprovedTransaction($player_id, Transactions::WITHDRAWAL);
        $total_promo_bonus = $this->player_model->getMemberTotalBonus($player_id, Transactions::ADD_BONUS)['totalBonus'];
        $total_deducted_promo_bonus = $this->player_model->getMemberTotalBonus($player_id, Transactions::SUBTRACT_BONUS)['totalBonus'];
        $total_deposit_bonus = $this->player_model->getMemberTotalBonus($player_id, Transactions::MEMBER_GROUP_DEPOSIT_BONUS)['totalBonus'];
        $total_cashback_bonus = $this->player_model->getPlayerTotalCashback($player_id);
        $total_referral_bonus = $this->player_friend_referral->getTotalReferralBonusByPlayerId($player_id);
        $total_bonus_received = ($total_promo_bonus + $total_referral_bonus + $total_cashback_bonus + $total_deposit_bonus) - $total_deducted_promo_bonus;

        $total_deposits = [
            'amount' => $this->transactions->getPlayerTotalDeposits($player_id),
            'count' => $this->transactions->getTransactionCount([
                'to_id' => $player_id,
                'to_type' => Transactions::PLAYER,
                'transaction_type' => Transactions::DEPOSIT,
                'status' => Transactions::APPROVED,
            ]),
        ];

        $total_withdrawals = [
            'amount' => $this->transactions->getPlayerTotalWithdrawals($player_id),
            'count' => $this->transactions->getTransactionCount([
                'to_id' => $player_id,
                'to_type' => Transactions::PLAYER,
                'transaction_type' => Transactions::WITHDRAWAL,
                'status' => Transactions::APPROVED,
            ]),
        ];

        $average_deposit_amount = 0;
        if (!empty($total_deposits['amount']) && !empty($total_deposits['count'])) {
            $average_deposit_amount = ($total_deposits['amount'] / $total_deposits['count']);
        }

        $average_withdrawal_amount = 0;
        if (!empty($total_withdrawals['amount']) && !empty($total_withdrawals['count'])) {
            $average_withdrawal_amount = ($total_withdrawals['amount'] / $total_withdrawals['count']);
        }

        $acc_stats = [
            'deposits' => [
                'count'             => floatval($total_deposits['count']) ,
                'amount'            => floatval($total_deposits['amount']) ,
                'average_amount'    => floatval($average_deposit_amount) ,
                'date_first'        => $first_last_deposit['first'] ,
                'date_last'         => $first_last_deposit['last'] ,
            ] ,
            'withdrawals' => [
                'count'             => floatval($total_withdrawals['count']) ,
                'amount'            => floatval($total_withdrawals['amount']) ,
                'average_amount'    => floatval($average_withdrawal_amount) ,
                'date_first'        => $first_last_withdrawal['first'] ,
                'date_last'         => $first_last_withdrawal['last'] ,
            ] ,
            'bonuses' => [
                'subtotal_deposit'  => floatval($total_deposit_bonus) ,
                'subtotal_cashback' => floatval($total_cashback_bonus) ,
                'subtotal_referral' => floatval($total_referral_bonus) ,
                'subtotal_promo'    => floatval($total_promo_bonus) ,
                'grandtotal_bonus'  => floatval($total_bonus_received)
            ]
        ];

        foreach ($acc_stats as & $row) { ksort($row); }

        return $acc_stats;
    }

    /**
     * Return player's profile and reg info
     * Workhorse for Comapi_reports::getPlayerRiskInfo(), OGP-12222
     *
     * @param   int     $player_id      == player.playerId
     * @see     Comapi_reports::getPlayerRiskInfo()
     * @return  array   assoc array [ 'player_profile' => [ ... ], 'reg_info' => [ ... ] ]
     */
    public function risk_player_profile($player_id) {
        $this->load->model([ 'agency_model', 'affiliate' ]);

        $player     = $this->player_model->getPlayerArrayById($player_id);
        $pdetails   = $this->player_model->getAllPlayerDetailsById($player_id);

        // -- Player profile
        // playerdetails fields to reveal
        $pprofile_fields_from_pdetails = [ 'firstName', 'birthplace', 'language', 'birthdate', 'address', 'country', 'lastName', 'citizenship', 'gender', 'city', 'contactNumber', 'registrationWebsite', 'registrationIP' ];
        $pprofile = $this->utils->array_select_fields($pdetails, $pprofile_fields_from_pdetails);
        // Age
        // $pprofile['age'] = null;
        // if (!empty($pprofile['birthdate'])) {
        //     $dt_player_birth = new DateTime($pprofile['birthdate']);
        //     $dt_now = new DateTime();
        //     $dt_diff = $dt_now->diff($dt_player_birth);
        //     $pprofile['age'] = $dt_diff->y;
        // }

        // Secret questions
        $pprofile['secretQuestion'] = $player['secretQuestion'];
        $pprofile['secretAnswer']   = $player['secretAnswer'];

        ksort($pprofile);

        // -- Reg info
        // last login
        $last_login = $this->http_request->getPlayerLastLogin($player_id);
        // dispatch account level
        $dacc_level = $this->player_model->getCurrentDispatchAccountLevel($player_id);
        $daccount_group = null;
        $daccount_level = null;
        if (is_array($dacc_level) && !empty($dacc_level)) {
            $daccount_group = lang($dacc_level[0]['group_name']);
            $daccount_level = lang($dacc_level[0]['level_name']);
        }
        // player runtime
        $pruntime = $this->player_model->getPlayerRuntimeByPlayerId($player_id);
        $last_login_date = null;
        $last_logout_date = null;
        if (is_array($pruntime)) {
            $last_login_date = $pruntime['lastLoginTime'];
            $last_logout_date = $pruntime['lastLogoutTime'];
        }
        // affiliate
        $aff = $this->affiliate->getAffiliateById($player['affiliateId']);
        $aff_username = null;
        if (is_array($aff) && !empty($aff)) {
            $aff_username = $aff['username'];
        }
        // invitation_count
        $referrals = $this->player_model->getPlayerReferral($player_id);
        $invitation_count = ($referrals == false) ? 0 : count($referrals);
        // pstatus
        $pstatus = $this->utils->getPlayerStatus($player_id, true, null, true);

        $reg_info = [
            'reg_date'            => $player['createdOn'] ,
            'last_login_date'     => $last_login_date ,
            'last_logout_date'    => $last_logout_date ,
            'online_status'       => !empty($player['online']) ,
            'password'            => $this->utils->decodePassword($player['password']) ,
            'affiliate'           => $aff_username ,
            'withdraw_password'   => $player['withdraw_password'] ,
            'newsletter_subscription'   => $player['newsletter_subscription'] ,
            'invitation_code'     => $player['invitationCode'] ,
            'invited_player'      => $this->player_model->getUsernameById($player['refereePlayerId']) ,
            'invitation_count'    => $invitation_count ,
            'agent'               => $this->agency_model->get_agent_by_id($player['agent_id'])['agent_name'] ,
            'tags'                => $this->player_model->getPlayerTagsForApi($player_id) ,
            'vip_group'           => lang($player['groupName']) ,
            'vip_level'           => lang($player['levelName']) ,
            'last_login_domain'   => $this->safe_ar($last_login, 'referrer') ,
            'last_login_ip'       => $this->safe_ar($last_login, 'ip') ,
            'status'              => $pstatus ,
            'daccount_group'      => lang($daccount_group) ,
            'daccount_level'      => lang($daccount_level) ,
        ];

        ksort($reg_info);

        return [ 'player_profile' => $pprofile, 'reg_info' => $reg_info ];
    }

    /**
     * Translates transaction type to text for withdrawal coditions
     * Worker routine for Comapi_reports::risk_withdrawal_conditions()
     *
     * @param   int     $tx_type    'source_type' column of Withdraw_condition::getPlayerWithdrawalCondition() return
     * @return  string  transaction type text
     */
    protected function risk_withdrawal_condition_tx_type_text($tx_type) {
        $tx_types = [
            Withdraw_condition::SOURCE_DEPOSIT ,
            Withdraw_condition::SOURCE_BONUS ,
            Withdraw_condition::SOURCE_CASHBACK ,
        ];
        $tx_type_text = lang('Non-deposit');
        if (in_array($tx_type, $tx_types)) {
            $tx_type_text = lang('withdraw_conditions.source_type.' . $tx_type);
        }

        return $tx_type_text;
    }

   /**
     * Translates subwallet ID to text
     * Worker routine for Comapi_reports::risk_withdrawal_conditions()
     *
     * @param   int     $wallet_Type    'wallet_type' column of Withdraw_condition::getPlayerWithdrawalCondition() return
     * @return  string  Subwallet name
     */
    protected function risk_subwallet_text($wallet_type) {
        $game_map = $this->utils->getGameSystemMap();
        if (empty($wallet_type) || !isset($game_map[$wallet_type])) {
            return '';
        }

        $subwallet = $game_map[$wallet_type];
        return $subwallet;
    }

    /**
     * Return player's withdrawal conditions
     * Workhorse for Comapi_reports::getPlayerRiskInfo(), OGP-12222
     *
     * @param   int     $player_id      == player.playerId
     * @see     Comapi_reports::getPlayerRiskInfo()
     * @return  array   plain array of player's withdrawal conditions
     */
    public function risk_withdrawal_conditions($player_id) {
        $wd_conds = $this->withdraw_condition->getPlayerWithdrawalCondition($player_id);
        if (empty($wd_conds)) {
            return [];
        }

        $wd_cond_fields = [ 'source_type', 'wallet_type', 'promoName', 'promo_code', 'walletDepositAmount', 'bonusAmount', 'started_at', 'conditionAmount', 'note', 'withdraw_condition_type' ];
        $wd_conds = $this->utils->array_select_fields($wd_conds, $wd_cond_fields);
        foreach ($wd_conds as & $row) {
            $row['transaction_type'] = $this->risk_withdrawal_condition_tx_type_text($row['source_type']);
            unset($row['source_type']);

            $row['subwallet'] = $this->risk_subwallet_text($row['wallet_type']);
            unset($row['wallet_type']);

            $row['status'] = $row['withdraw_condition_type'] > 0 ? lang('player.ub13') : lang('player.ub14');
            unset($row['withdraw_condition_type']);

            ksort($row);
        }

        return $wd_conds;
    }

    /**
     * Determines game status text by various game platform info
     * Worker routine for Comapi_reports::risk_game_stats(), OGP-12222
     *
     * @param   array   $gp     row array returned by Game_provider_auth::getGamePlatforms()
     * @return  string
     */
    protected function risk_game_stats_status_text($gp) {
        $gp_text = lang('lang.norec');
        if (empty($gp['register'])) {
            return $gp_text;
        }
        if ($gp['login_name'] ) {
            if ($gp['blocked'] == 1) {
                $gp_text = lang('sys.ip16');
            }
            else if ($gp['is_demo_flag'] == 1) {
                $gp_text = lang('Demo');
            }
            else if ($gp['blocked'] == 0) {
                $gp_text = lang('status.normal');
            }
        }

         return $gp_text;
    }

    /**
     * Return player's game stats
     * Workhorse for Comapi_reports::getPlayerRiskInfo(), OGP-12222
     *
     * @param   int     $player_id      == player.playerId
     * @see     Comapi_reports::getPlayerRiskInfo()
     * @return  array   plain array of game stat arrays for each platform
     */
    public function risk_game_stats($player_id) {
        $this->load->model([ 'L', 'game_provider_auth' ]);
        // $game_data = [
        //     '_total' => [
        //         'bet_amount'    => 0.0 ,
        //         'win_amount'    => 0.0 ,
        //         'loss_amount'   => 0.0 ,
        //         'result_pct'    => 0.0 ,
        //         'result_amount' => 0.0
        //     ]
        // ];

        $game_platforms = $this->game_provider_auth->getGamePlatforms($player_id);
        $L = $this->L->getSummary($player_id);

        $gp_fields = [ 'game_provider', 'game_account', 'status' ];

        foreach ($game_platforms as & $gp) {
            $gp['game_provider']    = $gp['system_code'];
            $gp['game_account']     = empty($gp['register']) ? '' : $gp['login_name'];
            $gp['status']           = $this->risk_game_stats_status_text($gp);

            $gpid = $gp['id'];

            $gp_stat_reveal = [
                'bet_amount'    => 0.0 ,
                'win_amount'    => 0.0 ,
                'loss_amount'   => 0.0 ,
                'result_pct'    => 0.0 ,
                'result_amount' => 0.0
            ];
            if (isset($L[$gpid])) {
                $gp_stat = $L[$gpid];
                $gp_stat_reveal = [
                    'bet_amount'    => floatval($gp_stat['bet']['sum']) ,
                    'win_amount'    => floatval($gp_stat['gain']['sum']) ,
                    'loss_amount'   => floatval($gp_stat['loss']['sum']) ,
                    'result_pct'    => floatval($gp_stat['result_percentage']['percent']) ,
                    'result_amount' => floatval($gp_stat['gain_loss']['sum'])
                ];

            }

            $gp = $this->utils->array_select_fields($gp, $gp_fields);
            $gp = array_merge($gp,  $gp_stat_reveal);
            ksort($gp);
        }

        $game_data = $game_platforms;
        // $game_data['game_platforms'] = $game_platforms;
        return $game_data;
    }

    /**
     * Returns player's risk information, workhorse for Api_common::getPlayerRiskInfo()
     * OGP-12222
     *
     * @param   int     $player_id      == player.playerId
     * @see     Api_common::getPlayerRiskInfo()
     * @return  array   assoc array of combined results
     */
    public function getPlayerRiskInfo($player_id) {
        $all_accounts   = $this->risk_player_all_accounts($player_id);
        $player_profile = $this->risk_player_profile($player_id);

        $acc_stats      = $this->risk_player_account_stats($player_id);
        $wd_conds       = $this->risk_withdrawal_conditions($player_id);
        $game_stats     = $this->risk_game_stats($player_id);

        $rinfo_res = [
            'accounts'              => $all_accounts ,
            'account_stats'         => $acc_stats ,
            'withdrawal_conditions' => $wd_conds ,
            'game_stats'            => $game_stats
        ];

        $rinfo_res = array_merge($rinfo_res, $player_profile);

        ksort($rinfo_res);

        return $rinfo_res;
    }

    /**
     * Safely get array element
     *
     * @param   array   $arr        The array
     * @param   string  $key        The key
     * @param   mixed   $default    Default value when key is not found in $arr
     * @return  mixed   array element pointed by key if present; otherwise $default
     */
    protected function safe_ar($arr, $key, $default = null) {
        if (is_array($arr) && isset($arr[$key])) {
            return $arr[$key];
        }
        return $default;
    }

    protected function formatPlayerDepositHistory($dh_results) {
        $this->load->model([ 'payment_account', 'transaction_notes', 'sale_order' ]);
        $payment_types = [
            Payment_account::FLAG_MANUAL_LOCAL_BANK     => lang('pay.local_bank_offline') ,
            Payment_account::FLAG_MANUAL_ONLINE_PAYMENT => lang('pay.manual_online_payment') ,
            Payment_account::FLAG_AUTO_ONLINE_PAYMENT   => lang('pay.auto_online_payment')
        ];

        foreach ($dh_results as & $row) {
            $row['payment_type'] = $this->safe_ar($payment_types, $row['payment_type'], lang('player.ub12'));
            $row['collection_bank'] = lang($row['collection_bank']);
            $row['deposit_payment_bank'] = lang($row['deposit_payment_bank']);
            $row['vip_group'] = lang($row['vip_group']);
            $row['vip_level'] = lang($row['vip_level']);

            $row['status'] = lang("sale_orders.status.{$row['status']}");
            $note1 = $this->transaction_notes->getNotesByTransaction(Transaction_notes::TRANS_DEPOSIT, $row['sid'], false);
            $note2 = $this->sale_order->getSaleOrderReason($row['sid']);
            $row['notes'] = empty($note1) ? $note2 : "{$note1}¥n{$note2}";

            unset($row['sid']);

            ksort($row);
        }

        return $dh_results;
    }

    public function getPlayerDepositHistory($player_id, $filter = 1, $time_from = null, $time_to = null, $limit = null, $offset = null) {
        $shown_statuses = [];
        $statuses_successful = [
            Sale_order::STATUS_BROWSER_CALLBACK ,   // 4
            Sale_order::STATUS_SETTLED              // 5
        ];
        $statuses_pending = [
            Sale_order::STATUS_PROCESSING ,    // 3
            Sale_order::STATUS_CHECKING        // 9
        ];
        $statuses_failed = [
            Sale_order::STATUS_DECLINED ,      // 8
            // Sale_order::FAILED         // 7
        ];

        if ($filter & 0x1) {
            $shown_statuses = array_merge($shown_statuses, $statuses_successful);
        }
        if ($filter & 0x2) {
            $shown_statuses = array_merge($shown_statuses, $statuses_pending);
        }
        if ($filter & 0x4) {
            $shown_statuses = array_merge($shown_statuses, $statuses_failed);
        }

        $this->from('sale_orders    AS S')
            ->where('S.payment_kind', Sale_order::PAYMENT_KIND_DEPOSIT)
            ->where_in('S.status', $shown_statuses )
            ->where('S.player_id', $player_id)
            ->select('count(*) AS count')
        ;

        if (!empty($time_from) && !empty($time_to)) {
            // $this->db->where("S.player_submit_datetime BETWEEN '{$time_from}' AND '{$time_to}'", null, false);
            $this->db->where("S.created_at BETWEEN '{$time_from}' AND '{$time_to}'", null, false);
        }

        $count = $this->runOneRowOneField('count');

        $this->utils->debug_log(__METHOD__, 'row_count_sql', $this->db->last_query());

        $this->from('sale_orders    AS S')
            ->join('player          AS P' , 'P.playerId = S.player_id'          , 'left')
            ->join('playerdetails   AS D' , 'D.playerId = S.player_id'          , 'left')
            ->join('adminusers      AS U' , 'U.userId   = S.processed_by'       , 'left')
            ->join('transactions    AS TX', 'TX.id      = S.transaction_id'     , 'left')
            ->join('payment_account AS PA', 'PA.id      = S.payment_account_id' , 'left')
            ->join('playerpromo     AS PP', 'PP.playerpromoId   = S.player_promo_id', 'left')
            ->join('promorules      AS PR', 'PR.promorulesId    = PP.promorulesId'  , 'left')
            ->join('affiliates      AS AF', 'AF.affiliateId     = P.affiliateId'    , 'left')
            ->where('S.payment_kind', Sale_order::PAYMENT_KIND_DEPOSIT)
            ->where_in('S.status', $shown_statuses)
            ->where('S.player_id', $player_id)
            ->select([
                'S.secure_id' ,
                'P.username' ,
                'PA.flag        AS payment_type' ,
                'S.created_at   AS request_time' ,
                'S.player_submit_datetime   AS deposit_time' ,
                'D.firstName',
                'D.lastName' ,
                'AF.username    AS affiliate_username' ,
                'P.groupName    AS vip_group' ,
                'P.levelName    AS vip_level' ,
                'S.amount' ,
                'S.status' ,
                'S.payment_type_name        AS collection_bank' ,
                'S.ip' ,
                'S.geo_location             AS ip_location' ,
                'S.updated_at' ,
                'S.timeout_at' ,
                'U.username                 AS processed_by' ,
                'S.process_time             AS processed_time' ,
                'PA.payment_account_name    AS collection_account' ,
                'S.player_payment_type_name         AS deposit_payment_bank' ,
                'S.player_payment_account_name      AS deposit_account_name' ,
                'S.player_payment_account_number    AS deposit_account_number' ,
                'S.player_deposit_transaction_code  AS deposit_transaction_code' ,
                'PR.promoName               AS promo_name' ,
                'PP.bonusAmount             AS promo_bonus' ,
                'S.external_order_id' ,
                'S.bank_order_id' ,
                // 'S.player_deposit_time' ,
                // 'S.player_mode_of_deposit' ,
                'S.notes                    AS notes_from_player' ,
                'S.id                       AS sid' ,
                'S.browser_user_agent       AS user_agent' ,
            ])
            ->order_by('S.created_at', 'desc')
        ;

        if (!empty($time_from) && !empty($time_to)) {
            // $this->db->where("S.player_submit_datetime BETWEEN '{$time_from}' AND '{$time_to}'", null, false);
            $this->db->where("S.created_at BETWEEN '{$time_from}' AND '{$time_to}'", null, false);
        }

        if (!empty($limit)) {
            $this->db->limit($limit, 0);
            if (!empty($offset)) {
                $this->db->limit($limit, $offset);
            }
        }

        $dh_results = $this->runMultipleRowArray();

        // $this->utils->debug_log(__METHOD__, 'real_query_sql', $this->db->last_query());

        $dh_formatted = [];
        if (!empty($dh_results)) {
            $dh_formatted = $this->formatPlayerDepositHistory($dh_results);
        }

        return [
            'time_from' => $time_from ,
            'time_to'   => $time_to ,
            'limit'     => $limit ,
            'offset'    => $offset ,
            'filter'    => $filter ,
            'row_count_total' => $count ,
            'rows'      => $dh_formatted
        ];
    } // End function getPlayerDepositHistory

    protected function formatPlayerWithdrawHistory($wh_results) {
        if (empty($wh_results)) {
            return [];
        }

        $this->load->model([ 'transaction_notes' ]);

        foreach ($wh_results as & $row) {
            $waid = $row['walletAccountId'];
            // Status
            $stat = '';
            switch ($row['dwStatus']) {
                case Wallet_model::REQUEST_STATUS :
                case Wallet_model::PENDING_REVIEW_STATUS :
                    $stat = $row['is_checking'] == 'true' ? lang('sale_orders.status.9') : lang('sale_orders.status.3');
                    break;
                case Wallet_model::PAID_STATUS :
                case Wallet_model::APPROVED_STATUS :
                    $stat = lang('transaction.status.1');
                    break;
                case Wallet_model::DECLINED_STATUS :
                    $stat = lang('transaction.status.2');
                    break;
                default :
                    $stat = $row['dwStatus'];
            }
            $row['status'] = $stat;

            // process_time, paid_time
            $proc_note = $this->transaction_notes->getTransactionNotesByTransactionId($waid, Transaction_notes::TRANS_WITHDRAWAL, Wallet_model::PAY_PROC_STATUS);
            $paid_note = $this->transaction_notes->getTransactionNotesByTransactionId($waid, Transaction_notes::TRANS_WITHDRAWAL, Wallet_model::PAID_STATUS);
            $row['processed_time'] = $proc_note ? $proc_note['create_date'] : null;
            $row['paid_time']      = $paid_note ? $paid_note['create_date'] : null;

            // time_spent
            $row['time_spent'] = null;
            if ($row['dwStatus'] == Wallet_model::PAID_STATUS) {
                $time_spent = $this->utils->dateDiff($row['last_update_time'], $row['request_time']);
                $row['time_spent'] = $time_spent;
            }

            // labels
            $row['tags'] = $this->player_model->getPlayerTagsForApi($row['playerId']);

            // payment type
            switch ($row['default_payment_flag']) {
                case Payment_account::FLAG_MANUAL_LOCAL_BANK:
                    $pay_type = lang('pay.local_bank_offline');
                    break;
                case Payment_account::FLAG_AUTO_ONLINE_PAYMENT :
                    $pay_type = lang('pay.auto_online_payment');
                    break;
                case Payment_account::FLAG_MANUAL_ONLINE_PAYMENT :
                default :
                    $pay_type = lang('pay.manual_online_payment');
                    break;
            }
            $row['payment_type'] = $pay_type;

            // withdraw_account_bank
            $row['withdraw_account_bank'] = lang($row['withdraw_account_bank']);

            // Notes
            $tnotes = $this->transaction_notes->getNotesByTransaction(Transaction_notes::TRANS_WITHDRAWAL, $waid, false);
            $wnotes = $this->wallet_model->getWalletAccountNote($waid);
            $row['notes'] = (empty($wnotes) ? '' : "$wnotes¥n") . $tnotes;

            $row['vip_group'] = lang($row['vip_group']);

            // Tidy-up
            unset($row['dwStatus'], $row['is_checking'], $row['walletAccountId'], $row['playerId'], $row['default_payment_flag']);
            ksort($row);
        }

        return $wh_results;
    }

    public function getPlayerWithdrawHistory($player_id, $time_from = null, $time_to = null, $limit = null, $offset = null) {

        $this->from('walletaccount  AS W')
            ->where('W.transactionType', 'withdrawal')
            ->where('W.status', 0)
            ->where('W.playerId', $player_id)
            ->select('COUNT(*) AS count')
        ;

        if (!empty($time_from) && !empty($time_to)) {
            $this->db->where("(W.dwDateTime BETWEEN '{$time_from}' AND '{$time_to}') OR (W.dwDateTime BETWEEN '{$time_from}' AND '{$time_to}')", null, false);
        }

        $count = $this->runOneRowOneField('count');

        $this->utils->debug_log(__METHOD__, 'row_count_sql', $this->db->last_query());

        $this->from('walletaccount  AS W')
            ->join('playeraccount   AS PA' , 'PA.playerAccountId = W.playerAccountId'          , 'left')
            ->join('player          AS P'  , 'P.playerId    = W.playerId'       , 'left')
            ->join('playerdetails   AS D'  , 'D.playerId    = P.playerId'      , 'left')
            ->join('localbankwithdrawaldetails  AS LD' , 'LD.walletAccountId        = W.walletAccountId'    , 'left')
            ->join('playerbankdetails   AS BD' , 'BD.playerBankDetailsId    = LD.playerBankDetailsId'       , 'left')
            ->join('banktype        AS BT' , 'BT.bankTypeId = BD.bankTypeId'    , 'left')
            ->join('adminusers      AS U'  , 'U.userId      = W.processedBy'    , 'left')
            // ->join('adminusers      AS LU' , 'LU.userId     = W.locked_user_id' , 'left')
            ->join('affiliates      AS AF' , 'P.affiliateId = AF.affiliateId'   , 'left')
            ->join('external_system AS S'  , 'S.id          = W.paymentAPI'     , 'left')
            ->select([
                'W.transactionCode  AS secure_id' ,
                'W.dwStatus' ,
                'W.is_checking' ,
                // 'LU.username' ,
                'P.username' ,
                'W.dwDateTime       AS request_time' ,
                'W.walletAccountId' ,
                'D.firstName' ,
                'D.lastName' ,
                'P.groupName        AS vip_group' ,
                'P.levelName        AS vip_level' ,
                'P.playerId' ,
                'W.amount' ,
                'if(W.bankName is null, BT.bankName, W.bankName)            AS withdraw_account_bank' ,
                'if(W.bankAccountFullName IS NULL, BD.bankAccountFullName, W.bankAccountFullName)   AS withdraw_account_name' ,
                'if(W.bankAccountNumber IS NULL, BD.bankAccountNumber, W.bankAccountNumber)         AS withdraw_account_number' ,
                'BT.default_payment_flag' ,
                'if(W.bankBranch IS NULL, BD.branch, W.bankBranch)          AS withdraw_account_branch' ,
                'if(W.bankProvince IS NULL, BD.province, W.bankProvince)    AS withdraw_account_province' ,
                'if(W.bankCity is NULL, BD.city, W.bankCity)                AS withdraw_account_city' ,
                'W.dwIp             AS ip' ,
                'W.dwLocation       AS ip_location' ,
                'U.username         AS processed_by' ,
                'W.processDateTime  AS last_update_time' ,
                'W.browser_user_agent   AS user_agent' ,
            ])
            ->where('W.transactionType', 'withdrawal')
            ->where('W.status', 0)
            ->where('W.playerId', $player_id)

            ->order_by('W.dwDateTime', 'desc')
        ;

        if (!empty($time_from) && !empty($time_to)) {
            $this->db->where("( (W.dwDateTime BETWEEN '{$time_from}' AND '{$time_to}') OR (W.processDateTime BETWEEN '{$time_from}' AND '{$time_to}') )", null, false);
        }

        if (!empty($limit)) {
            $this->db->limit($limit, 0);
            if (!empty($offset)) {
                $this->db->limit($limit, $offset);
            }
        }

        $wh_results = $this->runMultipleRowArray();

        $this->utils->debug_log(__METHOD__, 'real_query_sql', $this->db->last_query());

        $wh_formatted = $this->formatPlayerWithdrawHistory($wh_results);

        return [
            'time_from' => $time_from ,
            'time_to'   => $time_to ,
            'limit'     => $limit ,
            'offset'    => $offset ,
            'row_count_total' => $count ,
            'rows'      => $wh_formatted
        ];
    }

} // End class Comapi_reports
