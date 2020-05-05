<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

class Affiliate_statistics_model extends BaseModel{

    function __construct(){
        parent::__construct();
        $this->load->model(array('affiliatemodel', 'game_logs', 'player_model', 'transactions'));
    }

    protected $tableName = 'affiliate_static_report';

    /**
     * overview : generate affiliate statistics report
//     * @param datetime $start_date
//     * @param datetime $end_date
     * @param string $aff_userName
     * @return	array
     */
    public function generateStatistics($start_date, $end_date, $aff_userName = null){
        $trans = array();
        $trans['report_date'] = "FROM: ".$start_date."   TO: ".$end_date;

        $current_date_time = new DateTime();
        if (!empty($start_date)) {
            $current_date_time = new DateTime($start_date);
        }
        $report_date = $this->utils->formatDateForMysql($current_date_time);

        $where = null;
        if (!empty($aff_userName)) {
            $where['username'] = $aff_userName;
        }
        $newRecordCount = 0; $oldRecordCount = 0; $numberOfErrors = 0;

        $affiliates = $this->affiliatemodel->getAllAffiliates($where);
        $trans['total_of_affiliates'] = count($affiliates);

        foreach ($affiliates as $a) {
            $aff['affiliate_id'] = $a['affiliateId'];
            $aff['aff_username'] = $a['username'];
            $aff['real_name'] = $a['firstname'] . ' ' . $a['lastname'];
            $aff['affiliate_level'] = $a['levelNumber'];

            # GET LIST OF SUB-AFFILIATES OF AN AFFILIATE
            $affi_ids = $this->affiliatemodel->includeAllDownlineAffiliateIds($a['affiliateId']);
            $aff['total_sub_affiliates'] = count($this->affiliatemodel->getAllSubAffiliates($affi_ids, $start_date, $end_date));

            # GET LIST OF PLAYERS UNDER AFFILIATE
            $players = $this->affiliatemodel->getAllPlayersUnderAffiliateId($a['affiliateId'], $start_date, $end_date); // this is for players that registered on specific date
            $aff['total_registered_players'] = count($players);
            $aff['total_deposited_players'] = count($this->affiliatemodel->getAffiliateDepositedPLayer($a['affiliateId'], $start_date, $end_date));

            $players = $this->affiliatemodel->getAllPlayersUnderAffiliateId($a['affiliateId']);
            list($totalBets, $totalWins, $totalLoss) = $this->game_logs->getTotalBetsWinsLossByPlayers($players, $start_date, $end_date);
            $aff['total_bet'] = !empty($totalBets) ? $totalBets : 0;
            $aff['total_win'] = !empty($totalWins) ? $totalWins : 0;
            $aff['total_loss'] = !empty($totalLoss) ? $totalLoss : 0;

            $totalCashback = $this->player_model->getPlayersTotalCashback($players, $start_date, $end_date);
            $aff['total_cashback'] = $totalCashback;

            list($totalDeposit, $totalWithdrawal, $totalBonus) = $this->transactions->getTotalDepositWithdrawalBonusCashbackByPlayers($players, $start_date, $end_date);
            $aff['total_bonus'] = $totalBonus;
            $aff['total_deposit'] = $totalDeposit;
            $aff['total_withdraw'] = $totalWithdrawal;

            $win_loss = $totalLoss - $totalWins;
            $income = $win_loss - $totalBonus - $totalCashback;
            $aff['company_win_loss'] = $win_loss;
            $aff['company_income'] =  $income;
            $aff['report_date'] = $report_date;

            $aff['updated_at'] = $this->utils->getNowForMysql();

            if($this->isExistStatistics($a['affiliateId'], $report_date) > 0){
                $this->updateStatistics($a['affiliateId'], $report_date, $aff);
                $oldRecordCount++;
            }else{
                $this->insertStatistics($aff);
                $newRecordCount++;
            }
        }

        $trans['new_records_total'] = $newRecordCount;
        $trans['updated_records_total'] = $oldRecordCount;
        $this->utils->debug_log('Affiliate Statistics Report Status --------->', $trans);

        return true;
    }

    private function isExistStatistics($affiliate_id, $report_date){
        $query = $this->db
            ->get_where($this->tableName,
                array(
                    'affiliate_id' => $affiliate_id,
                    'report_date' => $report_date
                ));
        return count($query->result_array());
    }

    public function updateStatistics($affiliate_id, $report_date, $data){
        $this->db->where('affiliate_id', $affiliate_id)
            ->where('report_date', $report_date)
            ->update($this->tableName, $data);
    }

    public function insertStatistics($data){
        $data['created_at'] = $this->utils->getNowForMysql();
        return $this->db->insert($this->tableName, $data);
    }
}