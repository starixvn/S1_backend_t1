<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Report_functions
 *
 * Report_functions library for FHI OG System
 *
 * @package     Report_functions
 * @author      Rendell Nuñez
 * @version     1.0.0
 */

class Report_Functions {
    function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->library(array('session'));
        $this->ci->load->model(array('reports', 'player'));
    }

    /**
     * Will save to cronlogs
     *
     * @return  array
     */
    function saveToCronLogs($data) {
        $this->ci->reports->saveToCronLogs($data);
    }

    /**
     * Will get all user logs
     *
     * @return  array
     */
    function getAllLogs($limit, $offset) {
        $result = $this->ci->reports->getAllLogs($limit, $offset);
        return $result;
    }

    /**
     * Will record user logs
     *
     * @return  array
     */
    function recordAction($data) {
        $this->ci->reports->recordAction($data);
    }

    /**
     * Will get pt api issue
     *
     * @return  array
     */
    function getPTApiIssue($limit, $offset) {
        $result = $this->ci->reports->getPTApiIssue($limit, $offset);
        return $result;
    }

    /**
     * Will get player report
     *
     * @return  array
     */
    function getPlayersReport($sortBy,$limit, $offset) {
        $result = $this->ci->reports->getPlayersReport($sortBy,$limit, $offset);
        return $result;
    }

    /**
     * Will get payments report
     *
     * @return  array
     */
    function getPaymentsReport($sortBy) {
        $result = $this->ci->reports->getPaymentsReport($sortBy);
        return $result;
    }

    /**
     * Will get promo report
     *
     * @return  array
     */
    function getPromotionReport($sortBy) {
        $result = $this->ci->reports->getPromotionReport($sortBy);
        return $result;
    }

    /**
     * get all payment report list
     *
     * @return  array
     */
    public function getPaymentReportListToExport() {
        return $this->ci->reports->getPaymentReportListToExport();
    }

    /**
     * get all promotion report list
     *
     * @return  array
     */
    public function getPromotionReportListToExport() {
        return $this->ci->reports->getPromotionReportListToExport();
    }

    /**
     * get all logs report list
     *
     * @return  array
     */
    public function getLogsReportListToExport() {
        return $this->ci->reports->getLogsReportListToExport();
    }

    /**
     * get all summary report list
     *
     * @return  array
     */
    public function getSummaryReportListToExport() {
        return $this->ci->reports->getSummaryReportListToExport();
    }

    /**
     * get all income report list
     *
     * @return  array
     */
    public function getIncomeReportListToExport() {
        return $this->ci->reports->getIncomeReportListToExport();
    }

    /**
     * get all games report list
     *
     * @return  array
     */
    public function getGameReportListToExport() {
        return $this->ci->reports->getGameReportListToExport();
    }

    /**
     * get all player report list
     *
     * @return  array
     */
    public function getPlayerReportListToExport() {
        return $this->ci->reports->getPlayerReportListToExport();
    }

    /**
     * get pt game api logs report list
     *
     * @return  array
     */
    public function getPTGameAPIReportListToExport() {
        return $this->ci->reports->getPTGameAPIReportListToExport();
    }

    /**
     * get new registered players
     *
     * @return  array
     */
    public function getNewRegisteredPlayers($start_date, $end_date) {
        return $this->ci->reports->getNewRegisteredPlayers($start_date, $end_date);
    }

    /**
     * get registered players
     *
     * @return  array
     */
    public function getRegisteredPlayers() {
        return $this->ci->reports->getRegisteredPlayers();
    }

    /**
     * get deposit players
     *
     * @return  array
     */
    public function getDepositPlayers($start_date, $end_date) {
        return $this->ci->reports->getDepositPlayers($start_date, $end_date);
    }

    /**
     * get third party deposit players
     *
     * @return  array
     */
    public function getThirdPartyDepositPlayers($start_date, $end_date) {
        return $this->ci->reports->getThirdPartyDepositPlayers($start_date, $end_date);
    }

    /**
     * get withdrawal players
     *
     * @return  array
     */
    public function getWithdrawalPlayers($start_date, $end_date) {
        return $this->ci->reports->getWithdrawalPlayers($start_date, $end_date);
    }

    /**
     * get first deposit players
     *
     * @return  array
     */
    public function getFirstDepositPlayers($start_date, $end_date) {
        return $this->ci->reports->getFirstDepositPlayers($start_date, $end_date);
    }

    /**
     * get first deposit amount
     *
     * @return  array
     */
    public function getFirstDepositAmount($player_id, $start_date, $end_date) {
        return $this->ci->reports->getFirstDepositAmount($player_id, $start_date, $end_date);
    }

    /**
     * get second deposit players
     *
     * @return  array
     */
    public function getSecondDepositPlayers($start_date, $end_date) {
        return $this->ci->reports->getSecondDepositPlayers($start_date, $end_date);
    }

    /**
     * get second deposit amount
     *
     * @return  array
     */
    public function getSecondDepositAmount($player_id, $start_date, $end_date) {
        return $this->ci->reports->getSecondDepositAmount($player_id, $start_date, $end_date);
    }

    /**
     * get total deposit amount
     *
     * @return  array
     */
    public function getTotalDeposit($start_date, $end_date) {
        return $this->ci->reports->getTotalDeposit($start_date, $end_date);
    }

    /**
     * get total withdrawal amount
     *
     * @return  array
     */
    public function getTotalWithdrawal($start_date, $end_date) {
        return $this->ci->reports->getTotalWithdrawal($start_date, $end_date);
    }

    /**
     * get total pt gross income
     *
     * @return  array
     */
    public function getPTGrossIncome($start_date, $end_date) {
        return $this->ci->reports->getPTGrossIncome($start_date, $end_date);
    }

    /**
     * get total ag gross income
     *
     * @return  array
     */
    public function getAGGrossIncome($start_date, $end_date) {
        return $this->ci->reports->getAGGrossIncome($start_date, $end_date);
    }

    /**
     * get total deposits
     *
     * @return  array
     */
    public function getDepositAmount($player_id, $start_date, $end_date) {
        return $this->ci->reports->getDepositAmount($player_id, $start_date, $end_date);
    }

    /**
     * get total third party deposits
     *
     * @return  array
     */
    public function getThirdPartyDepositAmount($start_date, $end_date) {
        return $this->ci->reports->getThirdPartyDepositAmount($start_date, $end_date);
    }

    /**
     * get total withdrawal
     *
     * @return  array
     */
    public function getWithdrawalAmount($player_id, $start_date, $end_date) {
        return $this->ci->reports->getWithdrawalAmount($player_id, $start_date, $end_date);
    }

    /**
     * get total amount earned
     *
     * @return  array
     */
    public function getTotalAmountEarned($start_date, $end_date) {
        $total_deposits = $this->getDepositAmount($start_date, $end_date);
        $total_third_party_deposits = $this->getThirdPartyDepositAmount($start_date, $end_date);
        $total_withdrawals = $this->getWithdrawalAmount($start_date, $end_date);
        $total_bonuses = $this->getTotalBonuses($start_date, $end_date);

        $total_earned = ($total_deposits + $total_third_party_deposits) - ($total_withdrawals + $total_bonuses);

        return $total_earned;
    }

    /**
     * get referral bonuses
     *
     * @return  array
     */
    public function getTotalFriendReferralBonus($start_date, $end_date) {
        return $this->ci->reports->getTotalFriendReferralBonus($start_date, $end_date);
    }

    /**
     * get cashback bonuses
     *
     * @return  array
     */
    public function getTotalCashbackBonus($start_date, $end_date) {
        return $this->ci->reports->getTotalCashbackBonus($start_date, $end_date);
    }

    /**
     * get deposit/promo bonuses
     *
     * @return  array
     */
    public function getTotalPromoBonus($start_date, $end_date) {
        return $this->ci->reports->getTotalPromoBonus($start_date, $end_date);
    }

    /**
     * get total bonuses
     *
     * @return  array
     */
    public function getTotalBonuses($start_date, $end_date) {
        $total_friend_referral_bonus = $this->getTotalFriendReferralBonus($start_date, $end_date);
        $total_cashback_bonus = $this->getTotalCashbackBonus($start_date, $end_date);
        $total_promo_bonus = $this->getTotalPromoBonus($start_date, $end_date);

        $total_bonus = $total_friend_referral_bonus + $total_cashback_bonus + $total_promo_bonus;

        return $total_bonus;
    }

    /**
     * get total transaction fee
     *
     * @return  array
     */
    public function getTotalTransactionFee($start_date, $end_date) {
        return $this->ci->reports->getTotalTransactionFee($start_date, $end_date);
    }

    /**
     * get Online Players
     *
     * @return  array
     */
    public function getOnlinePlayers($start_date, $end_date) {
        return $this->ci->reports->getOnlinePlayers($start_date, $end_date);
    }

    /**
     * insert summary report
     *
     * @return  array
     */
    public function insertSummaryReport($data) {
        $this->ci->reports->insertSummaryReport($data);
    }

    /**
     * insert player report
     *
     * @return  array
     */
    public function insertPlayerReport($data) {
        $this->ci->reports->insertPlayerReport($data);
    }

    /**
     * insert income report
     *
     * @return  array
     */
    public function insertIncomeReport($data) {
        $this->ci->reports->insertIncomeReport($data);
    }

    /**
     * insert games report
     *
     * @return  array
     */
    public function insertGamesReport($data) {
        $this->ci->reports->insertGamesReport($data);
    }

    /**
     * get summary report
     *
     * @param  array
     * @return  array
     */
    public function getSummaryReport() {
        return $this->ci->reports->getSummaryReport();
    }

    /**
     * get PT total bets
     *
     * @return  array
     */
    public function getPTTotalBets($start_date, $end_date) {
        return $this->ci->reports->getPTTotalBets($start_date, $end_date);
    }

    /**
     * get PT total earn
     *
     * @return  array
     */
    public function getPTTotalEarn($start_date, $end_date) {
        return $this->ci->reports->getPTTotalEarn($start_date, $end_date);
    }

    /**
     * get AG total bets
     *
     * @return  array
     */
    public function getAGTotalBets($start_date, $end_date) {
        return $this->ci->reports->getAGTotalBets($start_date, $end_date);
    }

    /**
     * get AG total earn
     *
     * @return  array
     */
    public function getAGTotalEarn($start_date, $end_date) {
        return $this->ci->reports->getAGTotalBets($start_date, $end_date);
    }

    /**
     * get total registered players
     *
     * @return  array
     */
    public function getTotalRegisteredPlayers() {
        return $this->ci->reports->getTotalRegisteredPlayers();
    }

    /**
     * get total mass players
     *
     * @return  array
     */
    public function getTotalMassPlayers() {
        return $this->ci->reports->getTotalMassPlayers();
    }

    /**
     * get total online players
     *
     * @return  array
     */
    public function getTotalOnlinePlayers() {
        return $this->ci->reports->getTotalOnlinePlayers();
    }

    /**
     * get total deposit players
     *
     * @return  array
     */
    public function getTotalDepositPlayers() {
        return $this->ci->reports->getTotalDepositPlayers();
    }

    /**
     * get total deposit amount
     *
     * @return  array
     */
    public function getTotalDepositAmount() {
        return $this->ci->reports->getTotalDepositAmount();
    }

    /**
     * get total withdrawal players
     *
     * @return  array
     */
    public function getTotalWithdrawalPlayers() {
        return $this->ci->reports->getTotalWithdrawalPlayers();
    }

    /**
     * get total withdrawal amount
     *
     * @return  array
     */
    public function getTotalWithdrawalAmount() {
        return $this->ci->reports->getTotalWithdrawalAmount();
    }

    /**
     * get total bonus
     *
     * @return  array
     */
    public function getTotalBonus() {
        return $this->ci->reports->getTotalBonus();
    }

    /**
     * get total amount earned
     *
     * @return  array
     */
    public function getTotalEarned() {
        $total_bonus = $this->ci->reports->getTotalBonus();
        $total_deposit = $this->ci->reports->getTotalDepositAmount();
        $total_withdrawal = $this->ci->reports->getTotalWithdrawalAmount();

        $total_earned = $total_deposit - ($total_withdrawal + $total_bonus);

        if(empty($total_earned)) {
            return 0;
        } else {
            return $total_earned;
        }
    }

    /**
     * get total bets
     *
     * @return  array
     */
    public function getTotalBets() {
        return $this->ci->reports->getTotalBets();
    }

    /* Player Report */

    /**
     * get player report today
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getPlayerReportToday($start_date, $end_date) {
        return $this->ci->reports->getPlayerReport($start_date, $end_date);
    }

    /**
     * get player report
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getPlayerReport() {
        $report = $this->ci->reports->getPlayerReport(null, null);
        /*$player_count = count($this->ci->player->getAllPlayers('p.playerId', 'DESC', null, null));

        $result = array();
        $cnt = 0;

        foreach ($report as $key => $value) {
            $cnt++;

            if($cnt <= $player_count) {
                array_push($result, $value);
            }
        }

        return $result;*/
        return $report;
    }

    /**
     * get player report daily
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getPlayerReportDaily($start_date, $end_date) {
        $player_report = $this->ci->reports->getPlayerReport($start_date, $end_date);

        $result = array();
        $data = array();

        $date = null;

        foreach ($player_report as $key => $value) {
            $results = array();

            if($date != $value['date']) {
                $date = $value['date'];
                $player_count = 0;

                $registered_player = 0;
                $deposit_player = 0;
                $withdrawal_player = 0;

                $total_deposit_bonus = 0;
                $total_cashback_bonus = 0;
                $total_referral_bonus = 0;
                $total_bonus = 0;

                $total_first_deposit = 0;
                $total_second_deposit = 0;
                $total_deposit = 0;

                $total_withdrawal = 0;

                $results = $this->search($player_report, 'date', $value['date']);

                foreach ($results as $key => $value) {
                    $player_count++;

                    $registered_player = $value['registered_player'];
                    $deposit_player = $value['deposit_player'];
                    $withdrawal_player = $value['withdrawal_player'];

                    $total_deposit_bonus += $value['total_deposit_bonus'];
                    $total_cashback_bonus += $value['total_cashback_bonus'];
                    $total_referral_bonus += $value['total_referral_bonus'];
                    $total_bonus += $value['total_bonus'];

                    $total_first_deposit += $value['total_first_deposit'];
                    $total_second_deposit += $value['total_second_deposit'];
                    $total_deposit += $value['total_deposit'];

                    $total_withdrawal += $value['total_withdrawal'];
                }

                $data = array(
                    'total_player' => $player_count,
                    'registered_player' => $registered_player,
                    'deposit_player' => $deposit_player,
                    'withdrawal_player' => $withdrawal_player,
                    'total_deposit_bonus' => $total_deposit_bonus,
                    'total_cashback_bonus' => $total_cashback_bonus,
                    'total_referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_first_deposit' => $total_first_deposit,
                    'total_second_deposit' => $total_second_deposit,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'date' => $date,
                );
                array_push($result, $data);
            }
        }

        return $result;
    }

    /**
     * get player report weekly
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getPlayerReportWeekly($start_date, $end_date) {
        $player_report = array_reverse($this->getPlayerReportDaily($start_date, $end_date));

        $result = array();

        $player_count = 0;
        $registered_player = 0;
        $deposit_player = 0;
        $withdrawal_player = 0;

        $total_deposit_bonus = 0;
        $total_cashback_bonus = 0;
        $total_referral_bonus = 0;
        $total_bonus = 0;

        $total_first_deposit = 0;
        $total_second_deposit = 0;
        $total_deposit = 0;

        $total_withdrawal = 0;

        $report_count = count($player_report);
        $counter = 0;

        foreach ($player_report as $key => $value) {
            $counter++;

            if($player_count == 0) {
                $date_start = date('Y-m-d', strtotime($value['date']));
                $daycount = 7 - date("N", strtotime($date_start));
                $date_end = date('Y-m-d', strtotime($date_start . '+' . $daycount . ' day'));
            }

            $player_count++;

            $registered_player += $value['registered_player'];
            $deposit_player += $value['deposit_player'];
            $withdrawal_player += $value['withdrawal_player'];

            $total_deposit_bonus += $value['total_deposit_bonus'];
            $total_cashback_bonus += $value['total_cashback_bonus'];
            $total_referral_bonus += $value['total_referral_bonus'];
            $total_bonus += $value['total_bonus'];

            $total_first_deposit += $value['total_first_deposit'];
            $total_second_deposit += $value['total_second_deposit'];
            $total_deposit += $value['total_deposit'];

            $total_withdrawal += $value['total_withdrawal'];

            if($date_end == $value['date']) {
                $data = array(
                    'total_player' => $value['total_player'],
                    'registered_player' => $registered_player,
                    'deposit_player' => $deposit_player,
                    'withdrawal_player' => $withdrawal_player,
                    'total_deposit_bonus' => $total_deposit_bonus,
                    'total_cashback_bonus' => $total_cashback_bonus,
                    'total_referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_first_deposit' => $total_first_deposit,
                    'total_second_deposit' => $total_second_deposit,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'date' => ($date_start == $date_end) ? $date_start:$date_start . " - " . $date_end,
                );
                array_push($result, $data);

                $player_count = 0;
                $registered_player = 0;
                $deposit_player = 0;
                $withdrawal_player = 0;

                $total_deposit_bonus = 0;
                $total_cashback_bonus = 0;
                $total_referral_bonus = 0;
                $total_bonus = 0;

                $total_first_deposit = 0;
                $total_second_deposit = 0;
                $total_deposit = 0;

                $total_withdrawal = 0;

            } else if($counter == $report_count) {
                $data = array(
                    'total_player' => $value['total_player'],
                    'registered_player' => $registered_player,
                    'deposit_player' => $deposit_player,
                    'withdrawal_player' => $withdrawal_player,
                    'total_deposit_bonus' => $total_deposit_bonus,
                    'total_cashback_bonus' => $total_cashback_bonus,
                    'total_referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_first_deposit' => $total_first_deposit,
                    'total_second_deposit' => $total_second_deposit,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'date' => ($date_start == $value['date']) ? $date_start:$date_start . " - " . $value['date'],
                );
                array_push($result, $data);
            }
        }

        return $result;
    }

    /**
     * get player report monthly
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getPlayerReportMonthly($start_date, $end_date) {
        $player_report = array_reverse($this->getPlayerReportDaily($start_date, $end_date));

        $result = array();

        $total_player = 0;
        $registered_player = 0;
        $deposit_player = 0;
        $withdrawal_player = 0;

        $total_deposit_bonus = 0;
        $total_cashback_bonus = 0;
        $total_referral_bonus = 0;
        $total_bonus = 0;

        $total_first_deposit = 0;
        $total_second_deposit = 0;
        $total_deposit = 0;

        $total_withdrawal = 0;

        $report_count = count($player_report);
        $counter = 0;
        $month = null;

        foreach ($player_report as $key => $value) {
            $counter++;

            if($month == null) {
                $month = date('F', strtotime($value['date']));
            }

            $new_month = date('F', strtotime($value['date']));

            if($new_month != $month) {
                $data = array(
                    'total_player' => $total_player,
                    'registered_player' => $registered_player,
                    'deposit_player' => $deposit_player,
                    'withdrawal_player' => $withdrawal_player,
                    'total_deposit_bonus' => $total_deposit_bonus,
                    'total_cashback_bonus' => $total_cashback_bonus,
                    'total_referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_first_deposit' => $total_first_deposit,
                    'total_second_deposit' => $total_second_deposit,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'date' => $month,
                    'first_date' => date('Y-m-01', strtotime($date)),
                    'last_date' => date('Y-m-t', strtotime($date)),
                );
                array_push($result, $data);

                $total_player = 0;
                $registered_player = 0;
                $deposit_player = 0;
                $withdrawal_player = 0;

                $total_deposit_bonus = 0;
                $total_cashback_bonus = 0;
                $total_referral_bonus = 0;
                $total_bonus = 0;

                $total_first_deposit = 0;
                $total_second_deposit = 0;
                $total_deposit = 0;

                $total_withdrawal = 0;
                $month = $new_month;
            }

            $total_player = $value['total_player'];
            $registered_player += $value['registered_player'];
            $deposit_player += $value['deposit_player'];
            $withdrawal_player += $value['withdrawal_player'];

            $total_deposit_bonus += $value['total_deposit_bonus'];
            $total_cashback_bonus += $value['total_cashback_bonus'];
            $total_referral_bonus += $value['total_referral_bonus'];
            $total_bonus += $value['total_bonus'];

            $total_first_deposit += $value['total_first_deposit'];
            $total_second_deposit += $value['total_second_deposit'];
            $total_deposit += $value['total_deposit'];

            $total_withdrawal += $value['total_withdrawal'];
            $date = $value['date'];

            if($counter == $report_count) {
                $data = array(
                    'total_player' => $total_player,
                    'registered_player' => $registered_player,
                    'deposit_player' => $deposit_player,
                    'withdrawal_player' => $withdrawal_player,
                    'total_deposit_bonus' => $total_deposit_bonus,
                    'total_cashback_bonus' => $total_cashback_bonus,
                    'total_referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_first_deposit' => $total_first_deposit,
                    'total_second_deposit' => $total_second_deposit,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'date' => $month,
                    'first_date' => date('Y-m-01', strtotime($date)),
                    'last_date' => date('Y-m-t', strtotime($date)),
                );
                array_push($result, $data);
            }
        }

        return $result;
    }

    /**
     * get player report yearly
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getPlayerReportYearly($start_date, $end_date) {
        $player_report = $this->getPlayerReportMonthly($start_date, $end_date);

        $result = array();

        $total_player = 0;
        $registered_player = 0;
        $deposit_player = 0;
        $withdrawal_player = 0;

        $total_deposit_bonus = 0;
        $total_cashback_bonus = 0;
        $total_referral_bonus = 0;
        $total_bonus = 0;

        $total_first_deposit = 0;
        $total_second_deposit = 0;
        $total_deposit = 0;

        $total_withdrawal = 0;

        $report_count = count($player_report);
        $counter = 0;
        $year = null;

        foreach ($player_report as $key => $value) {
            $counter++;

            if($year == null) {
                $year = date('Y', strtotime($value['first_date']));
            }

            $new_year = date('Y', strtotime($value['first_date']));

            if($new_year != $year) {
                $data = array(
                    'total_player' => $total_player,
                    'registered_player' => $registered_player,
                    'deposit_player' => $deposit_player,
                    'withdrawal_player' => $withdrawal_player,
                    'total_deposit_bonus' => $total_deposit_bonus,
                    'total_cashback_bonus' => $total_cashback_bonus,
                    'total_referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_first_deposit' => $total_first_deposit,
                    'total_second_deposit' => $total_second_deposit,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'date' => $year,
                    'first_date' => date('Y-01-01', strtotime($date)),
                    'last_date' => date('Y-12-31', strtotime($date)),
                );
                array_push($result, $data);

                $total_player = 0;
                $registered_player = 0;
                $deposit_player = 0;
                $withdrawal_player = 0;

                $total_deposit_bonus = 0;
                $total_cashback_bonus = 0;
                $total_referral_bonus = 0;
                $total_bonus = 0;

                $total_first_deposit = 0;
                $total_second_deposit = 0;
                $total_deposit = 0;

                $total_withdrawal = 0;
                $year = $new_year;
            }

            $total_player = $value['total_player'];
            $registered_player += $value['registered_player'];
            $deposit_player += $value['deposit_player'];
            $withdrawal_player += $value['withdrawal_player'];

            $total_deposit_bonus += $value['total_deposit_bonus'];
            $total_cashback_bonus += $value['total_cashback_bonus'];
            $total_referral_bonus += $value['total_referral_bonus'];
            $total_bonus += $value['total_bonus'];

            $total_first_deposit += $value['total_first_deposit'];
            $total_second_deposit += $value['total_second_deposit'];
            $total_deposit += $value['total_deposit'];

            $total_withdrawal += $value['total_withdrawal'];
            $date = $value['date'];

            if($counter == $report_count) {
                $data = array(
                    'total_player' => $total_player,
                    'registered_player' => $registered_player,
                    'deposit_player' => $deposit_player,
                    'withdrawal_player' => $withdrawal_player,
                    'total_deposit_bonus' => $total_deposit_bonus,
                    'total_cashback_bonus' => $total_cashback_bonus,
                    'total_referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_first_deposit' => $total_first_deposit,
                    'total_second_deposit' => $total_second_deposit,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'date' => $year,
                    'first_date' => date('Y-01-01', strtotime($date)),
                    'last_date' => date('Y-12-31', strtotime($date)),
                );
                array_push($result, $data);
            }
        }

        return $result;
    }

    function search($array, $key, $value)
    {
        $results = array();
        $this->search_r($array, $key, $value, $results);
        return $results;
    }

    function search_r($array, $key, $value, &$results)
    {
        if (!is_array($array)) {
            return;
        }

        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }

        foreach ($array as $subarray) {
            $this->search_r($subarray, $key, $value, $results);
        }
    }

    /**
     * get registered player report today
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getRegisteredPlayerToday($start_date, $end_date) {
        return $this->ci->reports->getRegisteredPlayerToday($start_date, $end_date);
    }

    /* end of Player Report */


    /* Games Report */

    /**
     * get games report today
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getGamesReportToday($start_date, $end_date) {
        return $this->ci->reports->getGamesReport($start_date, $end_date);
    }

    /**
     * get games report
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getGamesReport() {
        $report = $this->ci->reports->getGamesReport(null, null);

        return $report;
    }

    /**
     * get games report daily
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getGamesReportDaily($start_date, $end_date) {
        $games_report = $this->ci->reports->getGamesReport($start_date, $end_date);

        $result = array();
        $data = array();

        $date = null;

        foreach ($games_report as $key => $value) {
            $results = array();

            if($date != $value['date']) {
                $date = $value['date'];
                $player_count = 0;

                $pt_bets = 0;
                $ag_bets = 0;
                $total_bets = 0;

                $pt_wins = 0;
                $ag_wins = 0;
                $total_wins = 0;

                $pt_loss = 0;
                $ag_loss = 0;
                $total_loss = 0;

                $pt_earned = 0;
                $ag_earned = 0;
                $total_earned = 0;

                $results = $this->search($games_report, 'date', $value['date']);

                foreach ($results as $key => $value) {
                    $player_count++;

                    $pt_bets += $value['pt_bets'];
                    $ag_bets += $value['ag_bets'];
                    $total_bets += $value['total_bets'];

                    $pt_wins += $value['pt_wins'];
                    $ag_wins += $value['ag_wins'];
                    $total_wins += $value['total_wins'];

                    $pt_loss += $value['pt_loss'];
                    $ag_loss += $value['ag_loss'];
                    $total_loss += $value['total_loss'];

                    $pt_earned += $value['pt_earned'];
                    $ag_earned += $value['ag_earned'];
                    $total_earned += $value['total_earned'];
                }

                $data = array(
                    'total_player' => $player_count,
                    'pt_bets' => $pt_bets,
                    'ag_bets' => $ag_bets,
                    'total_bets' => $total_bets,
                    'pt_wins' => $pt_wins,
                    'ag_wins' => $ag_wins,
                    'total_wins' => $total_wins,
                    'pt_loss' => $pt_loss,
                    'ag_loss' => $ag_loss,
                    'total_loss' => $total_loss,
                    'pt_earned' => $pt_earned,
                    'ag_earned' => $ag_earned,
                    'total_earned' => $total_earned,
                    'date' => $date,
                );
                array_push($result, $data);
            }
        }

        return $result;
    }

    /**
     * get games report weekly
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getGamesReportWeekly($start_date, $end_date) {
        $games_report = array_reverse($this->getGamesReportDaily($start_date, $end_date));

        $result = array();

        $player_count = 0;

        $pt_bets = 0;
        $ag_bets = 0;
        $total_bets = 0;

        $pt_wins = 0;
        $ag_wins = 0;
        $total_wins = 0;

        $pt_loss = 0;
        $ag_loss = 0;
        $total_loss = 0;

        $pt_earned = 0;
        $ag_earned = 0;
        $total_earned = 0;

        $report_count = count($games_report);
        $counter = 0;

        foreach ($games_report as $key => $value) {
            $counter++;

            if($player_count == 0) {
                $date_start = date('Y-m-d', strtotime($value['date']));
                $daycount = 7 - date("N", strtotime($date_start));
                $date_end = date('Y-m-d', strtotime($date_start . '+' . $daycount . ' day'));
            }

            $player_count++;

            $pt_bets += $value['pt_bets'];
            $ag_bets += $value['ag_bets'];
            $total_bets += $value['total_bets'];

            $pt_wins += $value['pt_wins'];
            $ag_wins += $value['ag_wins'];
            $total_wins += $value['total_wins'];

            $pt_loss += $value['pt_loss'];
            $ag_loss += $value['ag_loss'];
            $total_loss += $value['total_loss'];

            $pt_earned += $value['pt_earned'];
            $ag_earned += $value['ag_earned'];
            $total_earned += $value['total_earned'];

            if($date_end == $value['date']) {
                $data = array(
                    'total_player' => $value['total_player'],
                    'pt_bets' => $pt_bets,
                    'ag_bets' => $ag_bets,
                    'total_bets' => $total_bets,
                    'pt_wins' => $pt_wins,
                    'ag_wins' => $ag_wins,
                    'total_wins' => $total_wins,
                    'pt_loss' => $pt_loss,
                    'ag_loss' => $ag_loss,
                    'total_loss' => $total_loss,
                    'pt_earned' => $pt_earned,
                    'ag_earned' => $ag_earned,
                    'total_earned' => $total_earned,
                    'date' => ($date_start == $date_end) ? $date_start:$date_start . " - " . $date_end,
                );
                array_push($result, $data);

                $player_count = 0;

                $pt_bets = 0;
                $ag_bets = 0;
                $total_bets = 0;

                $pt_wins = 0;
                $ag_wins = 0;
                $total_wins = 0;

                $pt_loss = 0;
                $ag_loss = 0;
                $total_loss = 0;

                $pt_earned = 0;
                $ag_earned = 0;
                $total_earned = 0;

            } else if($counter == $report_count) {
                $data = array(
                    'total_player' => $value['total_player'],
                    'pt_bets' => $pt_bets,
                    'ag_bets' => $ag_bets,
                    'total_bets' => $total_bets,
                    'pt_wins' => $pt_wins,
                    'ag_wins' => $ag_wins,
                    'total_wins' => $total_wins,
                    'pt_loss' => $pt_loss,
                    'ag_loss' => $ag_loss,
                    'total_loss' => $total_loss,
                    'pt_earned' => $pt_earned,
                    'ag_earned' => $ag_earned,
                    'total_earned' => $total_earned,
                    'date' => ($date_start == $value['date']) ? $date_start:$date_start . " - " . $value['date'],
                );
                array_push($result, $data);
            }
        }

        return $result;
    }

    /**
     * get games report monthly
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getGamesReportMonthly($start_date, $end_date) {
        $games_report = array_reverse($this->getGamesReportDaily($start_date, $end_date));

        $result = array();

        $total_player = 0;

        $pt_bets = 0;
        $ag_bets = 0;
        $total_bets = 0;

        $pt_wins = 0;
        $ag_wins = 0;
        $total_wins = 0;

        $pt_loss = 0;
        $ag_loss = 0;
        $total_loss = 0;

        $pt_earned = 0;
        $ag_earned = 0;
        $total_earned = 0;

        $report_count = count($games_report);
        $counter = 0;
        $month = null;

        foreach ($games_report as $key => $value) {
            $counter++;

            if($month == null) {
                $month = date('F', strtotime($value['date']));
            }

            $new_month = date('F', strtotime($value['date']));

            if($new_month != $month) {
                $data = array(
                    'total_player' => $total_player,
                    'pt_bets' => $pt_bets,
                    'ag_bets' => $ag_bets,
                    'total_bets' => $total_bets,
                    'pt_wins' => $pt_wins,
                    'ag_wins' => $ag_wins,
                    'total_wins' => $total_wins,
                    'pt_loss' => $pt_loss,
                    'ag_loss' => $ag_loss,
                    'total_loss' => $total_loss,
                    'pt_earned' => $pt_earned,
                    'ag_earned' => $ag_earned,
                    'total_earned' => $total_earned,
                    'date' => $month,
                    'first_date' => date('Y-m-01', strtotime($date)),
                    'last_date' => date('Y-m-t', strtotime($date)),
                );
                array_push($result, $data);

                $total_player = 0;

                $pt_bets = 0;
                $ag_bets = 0;
                $total_bets = 0;

                $pt_wins = 0;
                $ag_wins = 0;
                $total_wins = 0;

                $pt_loss = 0;
                $ag_loss = 0;
                $total_loss = 0;

                $pt_earned = 0;
                $ag_earned = 0;
                $total_earned = 0;

                $month = $new_month;
            }

            $total_player = $value['total_player'];

            $pt_bets += $value['pt_bets'];
            $ag_bets += $value['ag_bets'];
            $total_bets += $value['total_bets'];

            $pt_wins += $value['pt_wins'];
            $ag_wins += $value['ag_wins'];
            $total_wins += $value['total_wins'];

            $pt_loss += $value['pt_loss'];
            $ag_loss += $value['ag_loss'];
            $total_loss += $value['total_loss'];

            $pt_earned += $value['pt_earned'];
            $ag_earned += $value['ag_earned'];
            $total_earned += $value['total_earned'];

            $date = $value['date'];

            if($counter == $report_count) {
                $data = array(
                    'total_player' => $total_player,
                    'pt_bets' => $pt_bets,
                    'ag_bets' => $ag_bets,
                    'total_bets' => $total_bets,
                    'pt_wins' => $pt_wins,
                    'ag_wins' => $ag_wins,
                    'total_wins' => $total_wins,
                    'pt_loss' => $pt_loss,
                    'ag_loss' => $ag_loss,
                    'total_loss' => $total_loss,
                    'pt_earned' => $pt_earned,
                    'ag_earned' => $ag_earned,
                    'total_earned' => $total_earned,
                    'date' => $month,
                    'first_date' => date('Y-m-01', strtotime($date)),
                    'last_date' => date('Y-m-t', strtotime($date)),
                );
                array_push($result, $data);
            }
        }

        return $result;
    }

    /**
     * get games report yearly
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getGamesReportYearly($start_date, $end_date) {
        $games_report = $this->getGamesReportMonthly($start_date, $end_date);

        $result = array();

        $total_player = 0;

        $pt_bets = 0;
        $ag_bets = 0;
        $total_bets = 0;

        $pt_wins = 0;
        $ag_wins = 0;
        $total_wins = 0;

        $pt_loss = 0;
        $ag_loss = 0;
        $total_loss = 0;

        $pt_earned = 0;
        $ag_earned = 0;
        $total_earned = 0;

        $report_count = count($games_report);
        $counter = 0;
        $year = null;

        foreach ($games_report as $key => $value) {
            $counter++;

            if($year == null) {
                $year = date('Y', strtotime($value['first_date']));
            }

            $new_year = date('Y', strtotime($value['first_date']));

            if($new_year != $year) {
                $data = array(
                    'total_player' => $total_player,
                    'pt_bets' => $pt_bets,
                    'ag_bets' => $ag_bets,
                    'total_bets' => $total_bets,
                    'pt_wins' => $pt_wins,
                    'ag_wins' => $ag_wins,
                    'total_wins' => $total_wins,
                    'pt_loss' => $pt_loss,
                    'ag_loss' => $ag_loss,
                    'total_loss' => $total_loss,
                    'pt_earned' => $pt_earned,
                    'ag_earned' => $ag_earned,
                    'total_earned' => $total_earned,
                    'date' => $year,
                    'first_date' => date('Y-01-01', strtotime($date)),
                    'last_date' => date('Y-12-31', strtotime($date)),
                );
                array_push($result, $data);

                $total_player = 0;

                $pt_bets = 0;
                $ag_bets = 0;
                $total_bets = 0;

                $pt_wins = 0;
                $ag_wins = 0;
                $total_wins = 0;

                $pt_loss = 0;
                $ag_loss = 0;
                $total_loss = 0;

                $pt_earned = 0;
                $ag_earned = 0;
                $total_earned = 0;

                $year = $new_year;
            }

            $total_player = $value['total_player'];

            $pt_bets += $value['pt_bets'];
            $ag_bets += $value['ag_bets'];
            $total_bets += $value['total_bets'];

            $pt_wins += $value['pt_wins'];
            $ag_wins += $value['ag_wins'];
            $total_wins += $value['total_wins'];

            $pt_loss += $value['pt_loss'];
            $ag_loss += $value['ag_loss'];
            $total_loss += $value['total_loss'];

            $pt_earned += $value['pt_earned'];
            $ag_earned += $value['ag_earned'];
            $total_earned += $value['total_earned'];

            $date = $value['date'];

            if($counter == $report_count) {
                $data = array(
                    'total_player' => $total_player,
                    'pt_bets' => $pt_bets,
                    'ag_bets' => $ag_bets,
                    'total_bets' => $total_bets,
                    'pt_wins' => $pt_wins,
                    'ag_wins' => $ag_wins,
                    'total_wins' => $total_wins,
                    'pt_loss' => $pt_loss,
                    'ag_loss' => $ag_loss,
                    'total_loss' => $total_loss,
                    'pt_earned' => $pt_earned,
                    'ag_earned' => $ag_earned,
                    'total_earned' => $total_earned,
                    'date' => $year,
                    'first_date' => date('Y-01-01', strtotime($date)),
                    'last_date' => date('Y-12-31', strtotime($date)),
                );
                array_push($result, $data);
            }
        }

        return $result;
    }

    /* end of Games Report */

    /* Income Report */

    /**
     * get income report today
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getIncomeReportToday($start_date, $end_date) {
        return $this->ci->reports->getIncomeReport($start_date, $end_date);
    }

    /**
     * get income report
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getIncomeReport() {
        $report = $this->ci->reports->getIncomeReport(null, null);
        /*$player_count = count($this->ci->player->getAllPlayers('p.playerId', 'DESC', null, null));

        $result = array();
        $cnt = 0;

        foreach ($report as $key => $value) {
            $cnt++;

            if($cnt <= $player_count) {
                array_push($result, $value);
            }
        }

        return $result;*/
        return $report;
    }

    /**
     * get income report daily
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getIncomeReportDaily($start_date, $end_date) {
        $income_report = $this->ci->reports->getIncomeReport($start_date, $end_date);

        $result = array();
        $data = array();

        $date = null;

        foreach ($income_report as $key => $value) {
            $results = array();

            if($date != $value['date']) {
                $date = $value['date'];
                $player_count = 0;

                $total_deposit_bonus = 0;
                $total_cashback_bonus = 0;
                $total_referral_bonus = 0;
                $total_bonus = 0;

                $total_deposit = 0;
                $total_withdrawal = 0;
                $total_earned = 0;

                $results = $this->search($income_report, 'date', $value['date']);

                foreach ($results as $key => $value) {
                    $player_count++;

                    $total_deposit_bonus += $value['deposit_bonus'];
                    $total_cashback_bonus += $value['cashback_bonus'];
                    $total_referral_bonus += $value['referral_bonus'];
                    $total_bonus += $value['total_bonus'];

                    $total_deposit += $value['total_deposit'];
                    $total_withdrawal += $value['total_withdrawal'];
                    $total_earned += $value['total_earned'];
                }

                $data = array(
                    'total_player' => $player_count,
                    'deposit_bonus' => $total_deposit_bonus,
                    'cashback_bonus' => $total_cashback_bonus,
                    'referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'total_earned' => $total_earned,
                    'date' => $date,
                );
                array_push($result, $data);
            }
        }

        return $result;
    }

    /**
     * get income report weekly
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getIncomeReportWeekly($start_date, $end_date) {
        $income_report = array_reverse($this->getIncomeReportDaily($start_date, $end_date));

        $result = array();

        $player_count = 0;

        $total_deposit_bonus = 0;
        $total_cashback_bonus = 0;
        $total_referral_bonus = 0;
        $total_bonus = 0;

        $total_deposit = 0;
        $total_withdrawal = 0;
        $total_earned = 0;

        $report_count = count($income_report);
        $counter = 0;

        foreach ($income_report as $key => $value) {
            $counter++;

            if($player_count == 0) {
                $date_start = date('Y-m-d', strtotime($value['date']));
                $daycount = 7 - date("N", strtotime($date_start));
                $date_end = date('Y-m-d', strtotime($date_start . '+' . $daycount . ' day'));
            }

            $player_count++;

            $total_deposit_bonus += $value['deposit_bonus'];
            $total_cashback_bonus += $value['cashback_bonus'];
            $total_referral_bonus += $value['referral_bonus'];
            $total_bonus += $value['total_bonus'];

            $total_deposit += $value['total_deposit'];
            $total_withdrawal += $value['total_withdrawal'];
            $total_earned += $value['total_earned'];

            if($date_end == $value['date']) {
                $data = array(
                    'total_player' => $value['total_player'],
                    'deposit_bonus' => $total_deposit_bonus,
                    'cashback_bonus' => $total_cashback_bonus,
                    'referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'total_earned' => $total_earned,
                    'date' => ($date_start == $date_end) ? $date_start:$date_start . " - " . $date_end,
                );
                array_push($result, $data);

                $player_count = 0;

                $total_deposit_bonus = 0;
                $total_cashback_bonus = 0;
                $total_referral_bonus = 0;
                $total_bonus = 0;

                $total_deposit = 0;
                $total_withdrawal = 0;
                $total_earned = 0;

            } else if($counter == $report_count) {
                $data = array(
                    'total_player' => $value['total_player'],
                    'deposit_bonus' => $total_deposit_bonus,
                    'cashback_bonus' => $total_cashback_bonus,
                    'referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'total_earned' => $total_earned,
                    'date' => ($date_start == $value['date']) ? $date_start:$date_start . " - " . $value['date'],
                );
                array_push($result, $data);
            }
        }

        return $result;
    }

    /**
     * get income report monthly
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getIncomeReportMonthly($start_date, $end_date) {
        $income_report = array_reverse($this->getIncomeReportDaily($start_date, $end_date));

        $result = array();

        $total_player = 0;

        $total_deposit_bonus = 0;
        $total_cashback_bonus = 0;
        $total_referral_bonus = 0;
        $total_bonus = 0;

        $total_deposit = 0;
        $total_withdrawal = 0;
        $total_earned = 0;

        $report_count = count($income_report);
        $counter = 0;
        $month = null;

        foreach ($income_report as $key => $value) {
            $counter++;

            if($month == null) {
                $month = date('F', strtotime($value['date']));
            }

            $new_month = date('F', strtotime($value['date']));

            if($new_month != $month) {
                $data = array(
                    'total_player' => $total_player,
                    'deposit_bonus' => $total_deposit_bonus,
                    'cashback_bonus' => $total_cashback_bonus,
                    'referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'total_earned' => $total_earned,
                    'date' => $month,
                    'first_date' => date('Y-m-01', strtotime($date)),
                    'last_date' => date('Y-m-t', strtotime($date)),
                );
                array_push($result, $data);

                $total_player = 0;

                $total_deposit_bonus = 0;
                $total_cashback_bonus = 0;
                $total_referral_bonus = 0;
                $total_bonus = 0;

                $total_deposit = 0;
                $total_withdrawal = 0;
                $total_earned = 0;

                $month = $new_month;
            }

            $total_player = $value['total_player'];

            $total_deposit_bonus += $value['deposit_bonus'];
            $total_cashback_bonus += $value['cashback_bonus'];
            $total_referral_bonus += $value['referral_bonus'];
            $total_bonus += $value['total_bonus'];

            $total_deposit += $value['total_deposit'];
            $total_withdrawal += $value['total_withdrawal'];
            $total_earned += $value['total_earned'];

            $date = $value['date'];

            if($counter == $report_count) {
                $data = array(
                    'total_player' => $total_player,
                    'deposit_bonus' => $total_deposit_bonus,
                    'cashback_bonus' => $total_cashback_bonus,
                    'referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'total_earned' => $total_earned,
                    'date' => $month,
                    'first_date' => date('Y-m-01', strtotime($date)),
                    'last_date' => date('Y-m-t', strtotime($date)),
                );
                array_push($result, $data);
            }
        }

        return $result;
    }

    /**
     * get income report yearly
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getIncomeReportYearly($start_date, $end_date) {
        $income_report = $this->getIncomeReportMonthly($start_date, $end_date);

        $result = array();

        $total_player = 0;

        $total_deposit_bonus = 0;
        $total_cashback_bonus = 0;
        $total_referral_bonus = 0;
        $total_bonus = 0;

        $total_deposit = 0;
        $total_withdrawal = 0;
        $total_earned = 0;

        $report_count = count($income_report);
        $counter = 0;
        $year = null;

        foreach ($income_report as $key => $value) {
            $counter++;

            if($year == null) {
                $year = date('Y', strtotime($value['first_date']));
            }

            $new_year = date('Y', strtotime($value['first_date']));

            if($new_year != $year) {
                $data = array(
                    'total_player' => $total_player,
                    'deposit_bonus' => $total_deposit_bonus,
                    'cashback_bonus' => $total_cashback_bonus,
                    'referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'total_earned' => $total_earned,
                    'date' => $year,
                    'first_date' => date('Y-01-01', strtotime($date)),
                    'last_date' => date('Y-12-31', strtotime($date)),
                );
                array_push($result, $data);

                $total_player = 0;

                $total_deposit_bonus = 0;
                $total_cashback_bonus = 0;
                $total_referral_bonus = 0;
                $total_bonus = 0;

                $total_deposit = 0;
                $total_withdrawal = 0;
                $total_earned = 0;

                $year = $new_year;
            }

            $total_player = $value['total_player'];

            $total_deposit_bonus += $value['deposit_bonus'];
            $total_cashback_bonus += $value['cashback_bonus'];
            $total_referral_bonus += $value['referral_bonus'];
            $total_bonus += $value['total_bonus'];

            $total_deposit += $value['total_deposit'];
            $total_withdrawal += $value['total_withdrawal'];
            $total_earned += $value['total_earned'];

            $date = $value['date'];

            if($counter == $report_count) {
                $data = array(
                    'total_player' => $total_player,
                    'deposit_bonus' => $total_deposit_bonus,
                    'cashback_bonus' => $total_cashback_bonus,
                    'referral_bonus' => $total_referral_bonus,
                    'total_bonus' => $total_bonus,
                    'total_deposit' => $total_deposit,
                    'total_withdrawal' => $total_withdrawal,
                    'total_earned' => $total_earned,
                    'date' => $year,
                    'first_date' => date('Y-01-01', strtotime($date)),
                    'last_date' => date('Y-12-31', strtotime($date)),
                );
                array_push($result, $data);
            }
        }

        return $result;
    }

    /* end of Income Report */

    /**
     * insert API player data per day
     *
     * @param  array
     * @return void
     */
    public function insertAPIData($data) {
        $this->ci->reports->insertAPIData($data);
    }

    /**
     * update API player data per day
     *
     * @param  array
     * @return void
     */
    public function updateAPIData($id, $data) {
        $this->ci->reports->updateAPIData($id, $data);
    }

    /**
     * check PT player data per day
     *
     * @param  array
     * @return void
     */
    public function checkPTPlayerPerDay($player_name, $bet_time) {
        return $this->ci->reports->checkPTPlayerPerDay($player_name, $bet_time);
    }

    /* Summary Report */

    /**
     * get new registered player
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getNewRegisteredPlayer($start_date, $end_date) {
        return $this->ci->reports->getNewRegisteredPlayer($start_date, $end_date);
    }

    /**
     * get registered player
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getRegisteredPlayer($date) {
        return $this->ci->reports->getRegisteredPlayer($date);
    }

    /**
     * get cashback player
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getCashbackPlayer($date) {
        return $this->ci->reports->getCashbackPlayer($date);
    }

    /**
     * get bonus player
     *
     * @param   date
     * @param   date
     * @return  array
     */
    public function getBonusPlayer($date) {
        return $this->ci->reports->getBonusPlayer($date);
    }

    // public function report_summary_lite($year = null, $month = null, $range = null) {
    //     $selected_tags = $this->ci->input->post('tag_list');

    //     $this->ci->load->model(array('report_model'));

    //     if (!empty($range)) {
    //         $date1 = $year; $date2 = $month;
    //         $transaction_summary_list = $this->ci->report_model->report_summary_2($date1, $date2);
    //     }
    //     else if ($month) {
    //         $transaction_summary_list = $this->ci->report_model->report_summary('DATE', $year . str_pad($month, 2, '0'), $selected_tags);
    //     }
    //     else if ($year) {
    //         $transaction_summary_list = $this->ci->report_model->report_summary('YEAR_MONTH', $year, $selected_tags);
    //     }
    //     else {
    //         $transaction_summary_list = $this->ci->report_model->report_summary('YEAR', null, $selected_tags);
    //     }

    //     $transaction_summary_list = array_combine(array_column($transaction_summary_list, 'common_date'), $transaction_summary_list);

    //     $data = null;
    //     if (!empty($range)) {
    //         $i = 0;
    //         while (true) {
    //             $date_start = $year;
    //             $date_end = $month;
    //             // $date = date('Y-m-d', strtotime('+' . $i++ . ' day', $month_start));
    //             $date = date('Y-m-d', strtotime("$date_start +{$i} day"));
    //             $i++;
    //             // $new_and_total_players = $this->ci->report_model->get_new_and_total_players('DATE', $date, $selected_tags);
    //             // $firs_and_second_deposit = $this->ci->report_model->get_first_and_second_deposit('DATE', $date, $selected_tags);
    //             $betWinLossPayoutCol = $this->ci->report_model->sumBetWinLossPayout('DATE', $date, $selected_tags);
    //             $data[] = array_merge(array('slug' => str_replace('-', '/', $date)), $betWinLossPayoutCol,
    //                 // $new_and_total_players, $firs_and_second_deposit,
    //                 isset($transaction_summary_list[$date]) ? $transaction_summary_list[$date] : array(
    //                     'common_date'           => $date,
    //                     'total_deposit'         => 0,
    //                     'total_withdraw'        => 0,
    //                     'total_bonus'           => 0,
    //                     'total_cashback'        => 0,
    //                     'total_transaction_fee' => 0,
    //                     'bank_cash_amount'      => 0,
    //                     'total_bet'             => 0,
    //                     'total_win'             => 0,
    //                     'total_loss'            => 0,
    //                     'payment'               => 0,
    //                     'payout'                => 0
    //                 ));
    //             if ($date == $date_end) {
    //                 break;
    //             }
    //             /*$this->ci->utils->debug_log('the $date =====> ', $date);
    //             $this->ci->utils->debug_log('the $transaction_summary_list =====> ', $transaction_summary_list);*/

    //         }
    //     }
    //     else if ($month) {
    //         //days of this month
    //         $month_start = strtotime('first day of this month', mktime(0, 0, 0, $month, 1, $year));
    //         $month_end = date('Y-m-d', strtotime('last day of this month', mktime(0, 0, 0, $month, 1, $year)));
    //         $i = 0;
    //         while (true) {
    //             $date = date('Y-m-d', strtotime('+' . $i++ . ' day', $month_start));
    //             // $new_and_total_players = $this->ci->report_model->get_new_and_total_players('DATE', $date, $selected_tags);
    //             // $firs_and_second_deposit = $this->ci->report_model->get_first_and_second_deposit('DATE', $date, $selected_tags);
    //             $betWinLossPayoutCol = $this->ci->report_model->sumBetWinLossPayout('DATE', $date, $selected_tags);
    //             $data[] = array_merge(array('slug' => str_replace('-', '/', $date)), $betWinLossPayoutCol,
    //                 // $new_and_total_players, $firs_and_second_deposit,
    //                 isset($transaction_summary_list[$date]) ? $transaction_summary_list[$date] : array(
    //                     'common_date'           => $date,
    //                     'total_deposit'         => 0,
    //                     'total_withdraw'        => 0,
    //                     'total_bonus'           => 0,
    //                     'total_cashback'        => 0,
    //                     'total_transaction_fee' => 0,
    //                     'bank_cash_amount'      => 0,
    //                     'total_bet'             => 0,
    //                     'total_win'             => 0,
    //                     'total_loss'            => 0,
    //                     'payment'               => 0,
    //                     'payout'                => 0
    //                 ));
    //             if ($date == $month_end) {
    //                 break;
    //             }
    //             /*$this->ci->utils->debug_log('the $date =====> ', $date);
    //             $this->ci->utils->debug_log('the $transaction_summary_list =====> ', $transaction_summary_list);*/

    //         }
    //     } else if ($year) {
    //         //months of this year
    //         for ($i = 1; $i <= 12; $i++) {
    //             $month = str_pad($i, 2, '0', 0);
    //             $year_month = $year . $month;
    //             $this->ci->utils->debug_log($year_month);
    //             // $new_and_total_players = $this->ci->report_model->get_new_and_total_players('YEAR_MONTH', $year_month, $selected_tags);
    //             // $firs_and_second_deposit = $this->ci->report_model->get_first_and_second_deposit('YEAR_MONTH', $year_month, $selected_tags);
    //             $betWinLossPayoutCol = $this->ci->report_model->sumBetWinLossPayout('YEAR_MONTH', $year . '-' . $month . '-01', $selected_tags);
    //             $data[] = array_merge(array('slug' => "{$year}/{$month}"),
    //                 $betWinLossPayoutCol,
    //                 // $new_and_total_players, $firs_and_second_deposit,
    //                 isset($transaction_summary_list[$year_month]) ? $transaction_summary_list[$year_month] : array(
    //                     'common_date'           => $year_month,
    //                     'total_deposit'         => 0,
    //                     'total_withdraw'        => 0,
    //                     'total_bonus'           => 0,
    //                     'total_cashback'        => 0,
    //                     'total_transaction_fee' => 0,
    //                     'bank_cash_amount'      => 0,
    //                     'total_bet'             => 0,
    //                     'total_win'             => 0,
    //                     'total_loss'            => 0,
    //                     'payment'               => 0,
    //                     'payout'                => 0
    //                 ));
    //         }
    //     } else {
    //         //years
    //         foreach ($transactions_summary_list as $transaction_summary) {
    //             // $new_and_total_players = $this->ci->report_model->get_new_and_total_players('YEAR', $transaction_summary['common_date'], $selected_tags);
    //             // $firs_and_second_deposit = $this->ci->report_model->get_first_and_second_deposit('YEAR', $transaction_summary['common_date'], $selected_tags);

    //             $betWinLossPayoutCol = $this->ci->report_model->sumBetWinLossPayout('YEAR', $transaction_summary['common_date'], $selected_tags);

    //             $data[] = array_merge($betWinLossPayoutCol, $transaction_summary,
    //                 // $new_and_total_players, $firs_and_second_deposit,
    //                 array(
    //                 'slug' => $transaction_summary['common_date'],
    //             ));
    //         }

    //         $this->ci->utils->debug_log('the data 2 -------->', $data);
    //     }
    //     if (empty($data)) {
    //         $output['data'] = [
    //             ["total_bet" => 0, "total_win" => 0, "total_loss" => 0, "payout" => 0, "common_date" => date('Y'),
    //                 "total_deposit" => "0", "total_withdraw" => "0", "total_bonus" => "0", "total_cashback" => "0",
    //                 "total_transaction_fee" => "0", "bank_cash_amount" => "0", "total_players" => "0",
    //                 "new_players" => "0", "first_deposit" => 0, "second_deposit" => 0, "slug" => date('Y')],
    //         ];
    //     } else {
    //         $output['data'] = array_values($data);
    //     }
    //     // $this->output->set_content_type('application/json')->set_output(json_encode($output));
    //     return $output;
    // }

    // public function report_summary_for_dashboard($typ, $year = null, $month = null) {
    //     $cache_key = 'report_summary_for_dashboard_' . $typ;

    //     $res = $this->ci->utils->getJsonFromCache($cache_key);
    //     if(!empty($res)){
    //         return $res;
    //     }

    //     switch ($typ) {
    //         case 'year' :
    //             $sum_year = $this->report_summary_lite($year, $month);
    //             $data = $sum_year['data'];
    //             $res = $this->format_summary_for_dashboard('year', $data);

    //             break;
    //         case 'month' :
    //             $raw_data = $this->report_summary_lite($year, $month);
    //             $raw = $raw_data['data'];
    //             $sum_month = [];
    //             for ($i = 0; $i < count($raw); ++$i) {
    //                 $week_num = intval($i / 7);
    //                 if (!isset($sum_month[$week_num])) {
    //                     $sum_month[$week_num] = [
    //                         'slug' => $raw[$week_num * 7]['slug'] ,
    //                         'total_bet' => $raw[$i]['total_bet'] ,
    //                         'payout' => $raw[$i]['payout'] ,
    //                     ];
    //                 }
    //                 else {
    //                     $sum_month[$week_num]['total_bet'] +=  $raw[$i]['total_bet'];
    //                     $sum_month[$week_num]['payout'] +=  $raw[$i]['payout'];
    //                 }
    //             }
    //             $res = $this->format_summary_for_dashboard('month', $sum_month);
    //             break;
    //         case 'week' : default :
    //             $raw_data = $this->report_summary_lite($year, $month, 'range');
    //             $raw = $raw_data['data'];
    //             $this->ci->utils->debug_log(__METHOD__, 'raw', $raw);
    //             $sum_week = [];
    //             for ($i = 0; $i < count($raw); ++$i) {
    //                 $sum_week[$i] = $raw[$i];
    //             }
    //             $res = $this->format_summary_for_dashboard('week', $sum_week);
    //             // $res = $raw_data;
    //             break;
    //     }

    //     if(!empty($res)){
    //         $this->ci->utils->saveJsonToCache($cache_key, $res, 360);
    //     }

    //     return $res;
    // }

    // public function format_summary_for_dashboard($mode, $data) {
    //     $x = [];
    //     foreach ($data as $row) {
    //         $x[] = date('Y-m-d', strtotime($mode == 'year' ? ($row['slug'] . '/01') : $row['slug']));
    //     }
    //     $amount_bet = array_column($data, 'total_bet');
    //     $gross_profit = array_column($data, 'payout');


    //     $sum_bets = array_sum($amount_bet);
    //     $sum_gprofits = array_sum($gross_profit);

    //     $this->ci->utils->debug_log(__METHOD__, 'sum_bets', $sum_bets, 'sum_gprofits', $sum_gprofits);

    //     $ratio_bet = $amount_bet;
    //     array_walk($ratio_bet, function (&$val, $key) use ($sum_bets) {
    //         $val = $sum_bets > 0 ? ($val / $sum_bets * 100) : 0;
    //     });
    //     $ratio_profit = $gross_profit;
    //     array_walk($ratio_profit, function (&$val, $key) use ($sum_gprofits) {
    //         $val = $sum_gprofits > 0 ? ($val / $sum_gprofits * 100) : 0;
    //     });
    //     array_unshift($x, 'x');
    //     array_unshift($amount_bet, 'amount_bet');
    //     array_unshift($gross_profit, 'gross_profit');
    //     array_unshift($ratio_bet, 'ratio_bet');
    //     array_unshift($ratio_profit, 'ratio_profit');

    //     $res = [
    //         'chart_figs' => [ $x, $amount_bet, $gross_profit, $ratio_bet, $ratio_profit ] ,
    //         'sums' => [ 'amount_bet' => $sum_bets, 'gross_profit' => $sum_gprofits ]
    //     ];

    //     return $res;
    // }

    /**
     * Generates figures for revenue chart on new dashboard
     *
     * @param   datestring  $date_from  Format: Y-m-d.  Start date.
     * @param   datestring  $date_to    Format: Y-m-d.  End date.
     * @param   datestring  $date_disp  The display date.  Output date will use this date over the original date.
     * @param   string      $type       Type of figures.  Only 'week' is supported now.
     * @return  array       [ chart_figs, sums ], where:
     *          chart_figs: c3.js compatible dataset
     *          sums:       Sum of total_bet and gross_profit
     */
    public function dashboard_revenue_chart($date_from, $date_to, $disp_date = null, $type='week') {
        $cache_key = 'dashboard_revenue_chart_' . $type;

        $res = $this->ci->utils->getJsonFromCache($cache_key);
        if(!empty($res)){
            return $res;
        }

        switch ($type) {
            case 'week' : default :
                $weekdays = $this->ci->total_player_game_day->get_bet_payout_by_date_interval($date_from, $date_to);
                $total = $this->ci->total_player_game_day->get_bet_payout_by_date_interval($date_from, $date_to, 'grand_total');
                $total = $total[0];

                $this->ci->utils->debug_log(__METHOD__, 'weekdays', $weekdays);

                $weekday2 = [];
                $sums = [ 'amount_bet' => 0.0, 'gross_profit' => 0.0 ];
                if (!empty($weekdays)) {
                    foreach ($weekdays as & $row) {
                        $row2 = $row;
                        $row2['ratio_bet'] = 0.0;
                        if ($total['amount_bet'] > 0) {
                            $row2['ratio_bet'] = $row['amount_bet'] / $total['amount_bet'] * 100;
                        }
                        $row2['ratio_profit'] = 0.0;
                        if ($total['gross_profit'] > 0) {
                            $row2['ratio_profit'] = $row['gross_profit'] / $total['gross_profit'] * 100;
                        }
                        unset($row2['date']);
                        // $weekday2[$row['date']] = $row2;
                        $date_disp_i = date('Y-m-d', strtotime($row['date']) - strtotime($date_to) + strtotime($disp_date));
                        $weekday2[$date_disp_i] = $row2;

                        $sums['amount_bet'] += $row['amount_bet'];
                        $sums['gross_profit'] += $row['gross_profit'];
                    }
                }

                // $this->ci->utils->debug_log(__METHOD__, 'weekday2', $weekday2);

                $datum = [
                    [ 'x' ] ,
                    [ 'amount_bet' ] ,
                    [ 'gross_profit' ] ,
                    [ 'ratio_bet' ] ,
                    [ 'ratio_profit' ]
                ];
                foreach ($weekday2 as $wd => $row2) {
                    // $this->ci->utils->debug_log(__METHOD__, 'row2', $row2);
                    $datum[0][] = $wd;
                    $datum[1][] = $row2[ 'amount_bet' ];
                    $datum[2][] = $row2[ 'gross_profit' ];
                    $datum[3][] = $row2[ 'ratio_bet' ];
                    $datum[4][] = $row2[ 'ratio_profit' ];
                }

                $res = [
                    'chart_figs' => $datum ,
                    'sums' => $sums
                ];

                break;
        }

        if(!empty($res)){
            $this->ci->utils->saveJsonToCache($cache_key, $res, 360);
        }

        return $res;
    }

    public function dashboard_revenue_chart_local_test($ratio = 1.0) {
        $chart_figs = [
            ["x","2018-11-10","2018-11-11","2018-11-12","2018-11-13","2018-11-14","2018-11-15","2018-11-16"],
            ["amount_bet","56949460.25","63290270.27000002","71596267.84","52186867.64000007","36990320.88999998","70764665.80000004","8391045.07"],
            ["gross_profit","-2470388.0049999976","3682404.479500002","1302615.7018999993","5598447.827100004","-1031918.2404000005","1780413.6670999979","1039095.6772000006"],
            ["ratio_bet",15.81187620702,17.57238636196,19.878525959704,14.489554196536,10.27027073133,19.647633718543,2.329752824907],
            ["ratio_profit",-24.951722748911,37.193483548279,13.156842478349,56.546144866034,-10.422710028502,17.982757408932,10.495204475819]
        ];

        // 'sums' => [
        //         "amount_bet" => 360168897.76 ,
        //         "gross_profit" => 9900671.1074
        //     ]

        $sums = [];
        foreach ($chart_figs as & $grp) {
            if (!in_array(reset($grp), [ 'amount_bet', 'gross_profit' ])) {
                continue;
            }
            for ($i = 1; $i < count($grp); ++$i) {
                $grp[$i] *= $ratio;
            }

            $workar = $grp;
            $title = array_shift($workar);
            $sums[$title] = array_sum($workar);
        }

        return [
            'chart_figs' => $chart_figs ,
            'sums' => $sums
        ];
    }

    /* end of Summary Report */
}