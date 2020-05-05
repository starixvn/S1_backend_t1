<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';


class Super_Report extends BaseModel {

    public function summaryReports($currency = null, $start_date=null, $end_date=null) {
        $this->db->from('super_summary_report');

        if ($currency) {
            $this->db->where('currency', $currency);
        }
        if ($start_date && $end_date) {
            $this->db->where('report_date >=', $start_date);
            $this->db->where('report_date <=', $end_date);
        }

        $result = $this->runMultipleRowArray();
        return $result ? $result : array();
    }

    public function playerReports($currency = null, $start_date=null, $end_date=null, $player_name) {
        $this->db->from('super_player_report');

        if ($currency) {
            $this->db->where('currency', $currency);
        }
        if ($start_date && $end_date) {
            $this->db->where('report_date >=', $start_date);
            $this->db->where('report_date <=', $end_date);
        }
        if ($player_name) {
            $this->db->where('player_username', $player_name);
        }

        $result = $this->runMultipleRowArray();
        return $result ? $result : array();
    }

    public function gameReports($currency = null, $start_date=null, $end_date=null, $player_name) {
        $this->db->select('super_game_report.*,external_system_list.system_name');
        $this->db->from('super_game_report');
        $this->db->join('external_system_list', 'external_system_list.id = super_game_report.game_platform_id');

        if ($currency) {
            $this->db->where('currency', $currency);
        }
        if ($start_date && $end_date) {
            $this->db->where('report_date >=', $start_date);
            $this->db->where('report_date <=', $end_date);
        }
        if ($player_name) {
            $this->db->where('player_username', $player_name);
        }

        $result = $this->runMultipleRowArray();
        return $result ? $result : array();
    }

    public function paymentReports($currency=null, $start_date=null, $end_date=null, $player_name) {
        $this->db->from('super_payment_report');

        if ($currency) {
            $this->db->where('currency', $currency);
        }
        if ($start_date && $end_date) {
            $this->db->where('report_date >=', $start_date);
            $this->db->where('report_date <=', $end_date);
        }
        if ($player_name) {
            $this->db->where('player_username', $player_name);
        }

        $result = $this->runMultipleRowArray();
        return $result ? $result : array();
    }

    public function promotionReports($currency = null, $start_date=null, $end_date=null, $player_name) {
        $this->db->from('super_promotion_report');

        if ($currency) {
            $this->db->where('currency', $currency);
        }
        if ($start_date && $end_date) {
            $this->db->where('report_date >=', $start_date);
            $this->db->where('report_date <=', $end_date);
        }
        if ($player_name) {
            $this->db->where('player_username', $player_name);
        }

        $result = $this->runMultipleRowArray();
        return $result ? $result : array();
    }

    public function cashbackReports($currency = null, $start_date=null, $end_date=null, $player_name) {
        $this->db->select('super_cashback_report.*,external_system_list.system_name');
        $this->db->from('super_cashback_report');
        $this->db->join('external_system_list', 'external_system_list.id = super_cashback_report.game_platform_id');

        if ($currency) {
            $this->db->where('currency', $currency);
        }
        if ($start_date && $end_date) {
            $this->db->where('report_date >=', $start_date);
            $this->db->where('report_date <=', $end_date);
        }
        if ($player_name) {
            $this->db->where('player_username', $player_name);
        }

        $result = $this->runMultipleRowArray();
        return $result ? $result : array();
    }

    public function getSummaryReportData($date = NULL) {

        $readOnlyDB = $this->getReadOnlyDB();

        $this->load->model(array('transactions','report_model'));

        $readOnlyDB->select_sum(sprintf('IF(transaction_type = %s, amount, 0)', Transactions::DEPOSIT), 'total_deposit');
        $readOnlyDB->select_sum(sprintf('IF(transaction_type = %s, amount, 0)', Transactions::WITHDRAWAL), 'total_withdraw');
        $readOnlyDB->select_sum(sprintf('(CASE WHEN transaction_type IN (%s) THEN amount WHEN transaction_type = %s THEN -amount ELSE 0 END)', implode(',', array(Transactions::ADD_BONUS, Transactions::MEMBER_GROUP_DEPOSIT_BONUS, Transactions::PLAYER_REFER_BONUS, Transactions::RANDOM_BONUS)), Transactions::SUBTRACT_BONUS), 'total_bonus');
        $readOnlyDB->select_sum(sprintf('IF(transaction_type = %s, amount, 0)', Transactions::AUTO_ADD_CASHBACK_TO_BALANCE), 'total_cashback');
        $readOnlyDB->select_sum(sprintf('IF(transaction_type IN (%s), amount, 0)', implode(',', array(Transactions::FEE_FOR_PLAYER, Transactions::FEE_FOR_OPERATOR))), 'total_transaction_fee');
        $readOnlyDB->select_sum(sprintf('(CASE WHEN transaction_type = %s THEN amount WHEN transaction_type = %s THEN -amount ELSE 0 END)', Transactions::DEPOSIT, Transactions::WITHDRAWAL), 'bank_cash_amount');
        $readOnlyDB->from('transactions');
        $readOnlyDB->where('DATE(created_at)', $date);
        $readOnlyDB->group_by("DATE(created_at)");
        $query  = $readOnlyDB->get();
        $data   = $query->row_array();

        $new_and_total_players      = $this->report_model->get_new_and_total_players('DATE', $date);
        $first_and_second_deposit   = $this->report_model->get_first_and_second_deposit('DATE', $date);
        $betWinLossPayoutCol        = $this->report_model->sumBetWinLossPayout('DATE', $date);
        $data = array_merge($data, $betWinLossPayoutCol, $new_and_total_players, $first_and_second_deposit);

        return array(
            'report_date'               => $date,
            'new_players'               => $data['new_players'],
            'total_players'             => $data['total_players'],
            'first_deposit_count'       => $data['first_deposit'],
            'second_deposit_count'      => $data['second_deposit'],
            'total_deposit'             => isset($data['total_deposit']) ? $data['total_deposit'] : 0,
            'total_withdraw'            => isset($data['total_withdraw']) ? $data['total_withdraw'] : 0,
            'total_bonus'               => isset($data['total_bonus']) ? $data['total_bonus'] : 0,
            'total_cashback'            => isset($data['total_cashback']) ? $data['total_cashback'] : 0,
            'total_fee'                 => isset($data['total_transaction_fee']) ? $data['total_transaction_fee'] : 0,
            'total_bank_cash_amount'    => isset($data['bank_cash_amount']) ? $data['bank_cash_amount'] : 0,
            'total_bet'                 => $data['total_bet'],
            'total_win'                 => $data['total_win'],
            'total_loss'                => $data['total_loss'],
            'gross_payout'              => $data['payout'],
            'net_payout'                => 0, # TO CLARIFY
            'affiliate_commission'      => 0, # TO CLARIFY
        );
    }

    public function getPlayerReportData($date = NULL) {

        $this->load->model('reports');

        $this->db->select('player.playerId as player_id');
        $this->db->select('player.username as player_username');
        $this->db->select("CONCAT_WS('|', vipsetting.groupName, vipsettingcashbackrule.vipLevelName) player_level", false);
        $this->db->select('player.registered_by as registered_by');
        $this->db->select('SUM(CASE WHEN transactions.transaction_type = ' . Transactions::MEMBER_GROUP_DEPOSIT_BONUS . ' THEN transactions.amount ELSE 0 END) as total_deposit_bonus');
        $this->db->select('SUM(CASE WHEN transactions.transaction_type = ' . Transactions::AUTO_ADD_CASHBACK_TO_BALANCE . ' THEN transactions.amount ELSE 0 END) as total_cashback_bonus');
        $this->db->select('SUM(CASE WHEN transactions.transaction_type = ' . Transactions::PLAYER_REFER_BONUS . ' THEN transactions.amount ELSE 0 END) as total_referral_bonus');
        $this->db->select('SUM(CASE WHEN transactions.transaction_type = ' . Transactions::ADD_BONUS . ' THEN transactions.amount ELSE 0 END) as total_manual_bonus');
        $this->db->select('player.playerId as total_first_deposit');
        $this->db->select('player.playerId as total_second_deposit');
        $this->db->select('SUM(CASE WHEN transactions.transaction_type IN (' . implode(',', array(Transactions::DEPOSIT)) . ') THEN transactions.amount ELSE 0 END) as total_deposit');
        $this->db->select('SUM(CASE WHEN transactions.transaction_type IN (' . implode(',', array(Transactions::WITHDRAWAL)) . ') THEN transactions.amount ELSE 0 END) as total_withdraw');
        $this->db->from('player');
        $this->db->join('vipsettingcashbackrule','vipsettingcashbackrule.vipsettingcashbackruleId = player.levelId','left');
        $this->db->join('vipsetting','vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId','left');
        $this->db->join('transactions','transactions.to_id = player.playerId','left');
        $this->db->where('transactions.to_type', Transactions::PLAYER);
        $this->db->where('transactions.status', Transactions::APPROVED);
        if ($date) {
            $this->db->where('DATE(transactions.created_at)', $date);
        }
        $this->db->group_by('player_id');

        $players = $this->runMultipleRowArray();

        if ( ! empty($players)) {
            foreach ($players as &$player) {

                $player['report_date'] = $date;

                // $player['total_first_deposit'] = $this->reports->getFirstDepositAmount($player['player_id'], '1970-01-01 00:00:00', date('Y-m-d H:i:s'));
                $first_deposit = $this->db->query("SELECT transactions.amount FROM transactions JOIN player ON player.playerId = transactions.to_id WHERE transactions.to_type = ? AND player.playerId = ? AND transactions.transaction_type = ? ORDER BY created_at ASC LIMIT ?,1", array(
                    Transactions::PLAYER,
                    $player['player_id'],
                    Transactions::DEPOSIT,
                    0, # FIRST DEPOSIT
                ))->row();

                $player['total_first_deposit'] = $first_deposit ? $first_deposit->amount : 0;

                // $player['total_second_deposit'] = $this->reports->getSecondDepositAmount($player['player_id'], '1970-01-01 00:00:00', date('Y-m-d H:i:s'));
                $second_deposit = $this->db->query("SELECT transactions.amount FROM transactions JOIN player ON player.playerId = transactions.to_id WHERE transactions.to_type = ? AND player.playerId = ? AND transactions.transaction_type = ? ORDER BY created_at ASC LIMIT ?,1", array(
                    Transactions::PLAYER,
                    $player['player_id'],
                    Transactions::DEPOSIT,
                    1, # SECOND DEPOSIT
                ))->row();

                $player['total_second_deposit'] = $second_deposit ? $second_deposit->amount : 0;

            }
        }

        return $players;

    }

    public function getGameReportData($date = NULL, $hour = NULL) {
        $this->db->select('total_player_game_hour.date report_date');
        $this->db->select('total_player_game_hour.hour report_hour');
        $this->db->select('total_player_game_hour.date_hour report_date_hour');
        $this->db->select('total_player_game_hour.player_id');
        $this->db->select('player.username player_username');
        $this->db->select("CONCAT_WS('|', vipsetting.groupName, vipsettingcashbackrule.vipLevelName) player_level", false);
        $this->db->select('total_player_game_hour.game_platform_id');
        $this->db->select('total_player_game_hour.game_type_id');
        $this->db->select('total_player_game_hour.game_description_id');
        $this->db->select('player.affiliateId affiliate_id');
        $this->db->select('affiliates.username affiliate_username');
        $this->db->select('total_player_game_hour.betting_amount total_bet_amount');
        $this->db->select('total_player_game_hour.win_amount total_win_amount');
        $this->db->select('total_player_game_hour.loss_amount total_loss_amount');
        $this->db->select('total_player_game_hour.result_amount total_result_amount');
        $this->db->select('game_type.game_type game_type_name');
        $this->db->select('game_description.game_name game_description_name');
        $this->db->from('total_player_game_hour');
        $this->db->join('game_type','game_type.id = total_player_game_hour.game_type_id','left');
        $this->db->join('game_description','game_description.id = total_player_game_hour.game_description_id','left');
        $this->db->join('player','player.playerId = total_player_game_hour.player_id','left');
        $this->db->join('vipsettingcashbackrule','vipsettingcashbackrule.vipsettingcashbackruleId = player.levelId','left');
        $this->db->join('vipsetting','vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId','left');
        $this->db->join('affiliates','affiliates.affiliateId = player.affiliateId','left');
        if ($date) {
            $this->db->where('total_player_game_hour.date', $date);
        }
        if ($hour) {
            $this->db->where('total_player_game_hour.hour', $hour);
        }
        return $this->runMultipleRowArray();
    }

    public function getPaymentReportData($date = NULL) {
        $this->db->select('transactions.trans_date report_date');
        $this->db->select('transactions.to_id player_id');
        $this->db->select('player.username player_username');
        $this->db->select("CONCAT_WS('|', vipsetting.groupName, vipsettingcashbackrule.vipLevelName) player_level", false);
        $this->db->select('transactions.id transaction_id');
        $this->db->select('transactions.transaction_type');
        $this->db->select('transactions.payment_account_id');
        $this->db->select('payment_account.payment_account_name payment_account_type');
        $this->db->select('transactions.amount');
        $this->db->from('transactions');
        $this->db->join('payment_account', 'payment_account.id = transactions.payment_account_id', 'left');
        $this->db->join('banktype', 'payment_account.payment_type_id = banktype.bankTypeId', 'left');
        $this->db->join('player', 'player.playerId = transactions.to_id and transactions.to_type=' . Transactions::PLAYER, 'left');
        $this->db->join('vipsettingcashbackrule','vipsettingcashbackrule.vipsettingcashbackruleId = player.levelId','left');
        $this->db->join('vipsetting','vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId','left');
        if ($date) {
            $this->db->where('transactions.trans_date', $date);
        }
        return $this->runMultipleRowArray();
    }

    public function getPromotionReportData($date = NULL) {
        $this->db->select('playerpromo.dateApply report_date');
        $this->db->select('playerpromo.playerId player_id');
        $this->db->select('player.username player_username');
        $this->db->select("CONCAT_WS('|', vipsetting.groupName, vipsettingcashbackrule.vipLevelName) player_level", false);
        $this->db->select('playerpromo.promorulesId promorule_id');
        $this->db->select('promorules.promoName promorule_name');
        $this->db->select('promorules.status promorule_status'); # TODO: CONFIRM
        $this->db->select('playerpromo.bonusAmount amount');
        $this->db->from('playerpromo');
        $this->db->join('promorules', 'promorules.promorulesId = playerpromo.promorulesId', 'left');
        $this->db->join('player', 'player.playerId = playerpromo.playerId', 'left');
        $this->db->join('vipsettingcashbackrule','vipsettingcashbackrule.vipsettingcashbackruleId = playerpromo.level_id','left');
        $this->db->join('vipsetting','vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId','left');
        if ($date) {
            $this->db->where('DATE(playerpromo.dateApply)', $date);
            $this->db->or_where('DATE(playerpromo.dateProcessed)', $date);
        }
        return $this->runMultipleRowArray();
    }

    public function getCashbackReportData($date = NULL) {
        $this->db->select('total_cashback_player_game_daily.total_date report_date');
        $this->db->select('total_cashback_player_game_daily.player_id');
        $this->db->select('player.username player_username');
        $this->db->select("CONCAT_WS('|', vipsetting.groupName, vipsettingcashbackrule.vipLevelName) player_level", false);
        $this->db->select('total_cashback_player_game_daily.history_id');
        $this->db->select('total_cashback_player_game_daily.amount');
        $this->db->select('total_cashback_player_game_daily.bet_amount');
        $this->db->select('total_cashback_player_game_daily.withdraw_condition_amount');
        $this->db->select('total_cashback_player_game_daily.paid_amount');
        $this->db->select('total_cashback_player_game_daily.paid_flag');
        $this->db->select('total_cashback_player_game_daily.paid_date');
        $this->db->select('total_cashback_player_game_daily.game_platform_id');
        $this->db->select('total_cashback_player_game_daily.game_type_id');
        $this->db->select('total_cashback_player_game_daily.game_description_id');
        $this->db->select('total_cashback_player_game_daily.updated_at');
        $this->db->select('game_type.game_type game_type_name');
        $this->db->select('game_description.game_name game_description_name');
        $this->db->from('total_cashback_player_game_daily');
        $this->db->join('game_type','game_type.id = total_cashback_player_game_daily.game_type_id','left');
        $this->db->join('game_description','game_description.id = total_cashback_player_game_daily.game_description_id','left');
        $this->db->join('player','player.playerId = total_cashback_player_game_daily.player_id','left');
        #$this->db->join('vipsettingcashbackrule','vipsettingcashbackrule.vipsettingcashbackruleId = total_cashback_player_game_daily.level_id','left');
        $this->db->join('vipsettingcashbackrule','vipsettingcashbackrule.vipsettingcashbackruleId = player.levelId','left');
        $this->db->join('vipsetting','vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId','left');
        if ($date) {
            $this->db->where('DATE(total_cashback_player_game_daily.updated_at)', $date);
        }

        return $this->runMultipleRowArray();
    }

    public function superReportReceiverInsertBatch($table,$data){
        $this->db->insert_batch($table, $data);
    }


}