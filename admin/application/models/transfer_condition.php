<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Transfer_condition
 *
 * General behaviors include :
 *
// * * Cancelling withdrawal conditions
// * * Disable/create withdrawal condition for player, deposit, promo
 *
 * @category Marketing Management
 * @version 1.0.0
 * @copyright 2013-2019 TripleOneTech
 */
class Transfer_condition extends BaseModel {

	protected $tableName = 'transfer_conditions';

	protected $idField = 'id';

	const STATUS_ACTIVE = 0;
    const STATUS_CANCEL = 1;
    const STATUS_COMPLETE = 2;

    /**
     * overview : get player withdrawal condition
     *
     * @param int	$playerId
     * @return array|bool
     */
    public function getPlayerTransferCondition($playerId){
        $this->load->model(array('transactions', 'promorules', 'game_logs'));

        $this->db->distinct()->select('promorules.promorulesId,
						   promorules.promoName,
						   promorules.promoType,
						   transfer_conditions.id,
						   transfer_conditions.promotion_id as promotion_id,
						   transfer_conditions.player_id,
						   transfer_conditions.condition_amount as conditionAmount,
						   transfer_conditions.started_at,
						   transfer_conditions.updated_at,
						   transfer_conditions.completed_at,
						   transfer_conditions.disallow_transfer_in,
						   transfer_conditions.disallow_transfer_out,
						   transfer_conditions.bet_details,
						   transfer_conditions.status')
            ->from('transfer_conditions')
            ->join('promorules', 'transfer_conditions.promotion_id = promorules.promorulesId', 'left')
            ->where('transfer_conditions.status', self::STATUS_ACTIVE)
            ->where('transfer_conditions.player_id', $playerId);

        $query = $this->db->get();
        $this->utils->printLastSQL();

        if ($query->num_rows() > 0) {

            $gameDescIdArrFromLevel=$this->group_level->getAllowedGameIdArr($playerId);

            foreach ($query->result_array() as $row) {
                $gameDescIdArr=$gameDescIdArrFromLevel;

                //check if deposit promo and non deposit promo (email,registration,mobile)
                if ($row['promoType'] == Promorules::PROMO_TYPE_NON_DEPOSIT ||
                    $row['promoType'] == Promorules::PROMO_TYPE_DEPOSIT) {
                    if ($row['promorulesId']) {
                        $this->load->model(array('promorules'));
                        $gameDescIdArr = $this->promorules->getPlayerGames($row['promorulesId']);
                        $this->utils->debug_log('get transfer condition gameDescIdArr from promo rules:'.$row['promorulesId']);
                    }
                }

                $row['currentBet'] = $this->game_logs->totalPlayerBettingAmountWithLimitByVIP($row['player_id'], $row['started_at'], null, $gameDescIdArr);

                $this->getDisallowTransferWalletName($playerId, $row);

                $data[] = $row;
            }
            return $data;
        }

        return false;
    }

    /**
     * detail: get the cancelled transfer condition of a certain player
     *
     * @param int $player_id
     * @return array
     */
    public function getPlayerCancelledTransferCondition($playerId = '', $where, $values) {
        $this->load->model(array('transactions', 'promorules', 'game_logs'));

        $this->db->distinct()->select('promorules.promorulesId,
						   promorules.promoName,
						   promorules.promoType,
						   transfer_conditions.id as tc_id,
						   transfer_conditions.player_promo_id,
						   transfer_conditions.promotion_id as promotion_id,
						   transfer_conditions.player_id,
						   transfer_conditions.condition_amount,
						   transfer_conditions.started_at,
						   transfer_conditions.updated_at,
						   transfer_conditions.status,
						   transaction_notes.note as notes,
						   playerpromo.bonusAmount as bonus_amount,
						   adminusers.username')
            ->from('transfer_conditions')
            ->join('(SELECT note, transaction_id FROM transaction_notes WHERE id in (SELECT MAX(id) FROM transaction_notes GROUP BY transaction_id)) transaction_notes', 'transaction_notes.transaction_id = transfer_conditions.id', 'left')
            ->join('promorules', 'transfer_conditions.promotion_id = promorules.promorulesId', 'left')
            ->join('playerpromo', 'transfer_conditions.player_promo_id = playerpromo.playerpromoId', 'left')
            ->join('adminusers', 'adminusers.userId = transfer_conditions.admin_user_id', 'left')
            ->where('transfer_conditions.status', self::STATUS_CANCEL)
            ->where('transfer_conditions.player_id', $playerId)
            ->where($where['0'], $values['0'])
            ->where($where['1'], $values['1'])
            ->order_by('transfer_conditions.started_at', 'desc')
        ;
        $query = $this->db->get();
        $this->utils->printLastSQL();

        if ($query->num_rows() > 0) {

            $gameDescIdArrFromLevel=$this->group_level->getAllowedGameIdArr($playerId);

            foreach ($query->result_array() as $row) {
                $gameDescIdArr=$gameDescIdArrFromLevel;

                //check if deposit promo and non deposit promo (email,registration,mobile)
                if ($row['promoType'] == Promorules::PROMO_TYPE_NON_DEPOSIT ||
                    $row['promoType'] == Promorules::PROMO_TYPE_DEPOSIT) {
                    if ($row['promorulesId']) {
                        $this->load->model(array('promorules'));
                        $gameDescIdArr = $this->promorules->getPlayerGames($row['promorulesId']);
                        $this->utils->debug_log('get transfer condition gameDescIdArr from promo rules:'.$row['promorulesId']);
                    }
                }

                $row['currentBet'] = $this->game_logs->totalPlayerBettingAmountWithLimitByVIP($row['player_id'], '2019-03-05 22:44:42', null, $gameDescIdArr);
                $data[] = $row;
            }
            return $data;
        }

        return null;
    }

    public function createTransferCondition($promorule, $playerId, $playerPromoId, $transferBetAmtCondition){
        $promorulesId = $promorule['promorulesId'];
        if (empty($transferBetAmtCondition)) {
            $transferBetAmtCondition = 0;
        }

        $json_info = $promorule['json_info'];
        $promorule_arr = json_decode($json_info, true);
        $transferRequirementWalletsInfo = $promorule_arr['transferRequirementWalletsInfo'];
        $wallet_info = json_decode($transferRequirementWalletsInfo, true);

        $disallow_transfer_in = json_encode($wallet_info['disallow_transfer_in_wallets']);
        $disallow_transfer_out = json_encode($wallet_info['disallow_transfer_out_wallets']);

        $admin_user_id = $this->authentication->getUserId();

        $subWalletId = null;

        $cond = $this->getTransferConditionByPlayerPromoId($playerPromoId);
        if (empty($cond)) {

            $data = array(
                'promotion_id' => $promorulesId,
                'player_id' => $playerId,
                'player_promo_id' => $playerPromoId,
                'disallow_transfer_in' => $disallow_transfer_in,
                'disallow_transfer_out' => $disallow_transfer_out,
                'bet_details' => '',
                'wallet_json' => $transferRequirementWalletsInfo,
                'condition_amount' => $transferBetAmtCondition,
                'status' => self::STATUS_ACTIVE,
                'started_at' => $this->utils->getNowForMysql(),
                'updated_at' => $this->utils->getNowForMysql(),
                'admin_user_id' => $admin_user_id
            );
            $rlt = $this->insertRow($data);
        } else {
            $data = array(
                'promotion_id' => $promorulesId,
                'player_id' => $playerId,
                'player_promo_id' => $playerPromoId,
                'disallow_transfer_in' => $disallow_transfer_in,
                'disallow_transfer_out' => $disallow_transfer_out,
                'bet_details' => '',
                'wallet_json' => $transferRequirementWalletsInfo,
                'condition_amount' => $transferBetAmtCondition,
                'status' => self::STATUS_ACTIVE,
                'updated_at' => $this->utils->getNowForMysql(),
                'admin_user_id' => $admin_user_id
            );

            $this->db->where('id', $cond['id'])
                 ->update($this->tableName, $data);
            $rlt = $cond['id'];
        }

        return $rlt;
    }

    /**
     * overview : get transfer condition
     *
     * @param  int	$playerpromoId
     * @return array
     */
    public function getTransferConditionByPlayerPromoId($playerpromoId) {
        $this->db->from($this->tableName)
                 ->where('player_promo_id', $playerpromoId)
                 ->limit(1);
        return $this->runOneRowArray();
    }

    /**
     * overview : cancel transfer condition
     *
     * @param  array	$ids
     * @return bool
     */
    public function cancelTransferCondition($ids, $cancelManualStatus=self::STATUS_CANCEL) {
        $data['status'] = $cancelManualStatus;
        $data['updated_at'] = $this->utils->getNowForMysql();
        $data['completed_at'] = $this->utils->getNowForMysql();
        $data['admin_user_id'] = $this->authentication->getUserId();

        $this->db->where_in('id', $ids);
        $this->db->set($data);
        $success = $this->runAnyUpdate($this->tableName);

        // $this->utils->printLastSQL();

        if ($success) {
            $this->load->model(['player_promo']);

            $this->db->select('player_id, player_promo_id')
                     ->from($this->tableName)
                     ->where_in('id', $ids)
                     ->where('player_promo_id is not null', null, false);

            $rows = $this->runMultipleRowArray();

            $this->utils->printLastSQL();
            $this->utils->debug_log('rows', count($rows));
            if (!empty($rows)) {
                $playerPromoArr = [];
                foreach ($rows as $row) {
                    $playerPromoArr[] = $row['player_promo_id'];
                }
                if (!empty($playerPromoArr)) {
                    $this->player_promo->finishPlayerPromos($playerPromoArr, 'auto finished by cancel transfer condition');
                }
            }

        }

        return $success;
    }

    public function getDisallowTransferWalletName($playerId, &$row){
        $this->load->model(['game_provider_auth']);

        $disallow_transfer_in = json_decode($row['disallow_transfer_in'], true);
        if(!is_array($disallow_transfer_in['wallet_id'])){
            $disallow_transfer_in['wallet_id'] = [];
        }

        $disallow_transfer_out = json_decode($row['disallow_transfer_out'], true);
        if(!is_array($disallow_transfer_out['wallet_id'])){
            $disallow_transfer_out['wallet_id'] = [];
        }

        $game_data = $this->game_provider_auth->getGamePlatforms($playerId);
        foreach($game_data as $game_platform) {
            if(in_array($game_platform['id'], $disallow_transfer_in['wallet_id'])){
                $row['disallow_transfer_in_wallets_name'][] = $game_platform['system_code'];
            }

            if(in_array($game_platform['id'], $disallow_transfer_out['wallet_id'])){
                $row['disallow_transfer_out_wallets_name'][] = $game_platform['system_code'];
            }
        }

        if(isset($row['disallow_transfer_in_wallets_name']) && (count($row['disallow_transfer_in_wallets_name']) > 0) ){
            $row['disallow_transfer_in_wallets_name'] = json_encode($row['disallow_transfer_in_wallets_name']);
        }

        if(isset($row['disallow_transfer_out_wallets_name']) && (count($row['disallow_transfer_out_wallets_name']) > 0) ){
            $row['disallow_transfer_out_wallets_name'] = json_encode($row['disallow_transfer_out_wallets_name']);
        }

        return $row;
    }

    public function isPlayerTransferConditionExist($playerId, $transfer_from, $transfer_to){
        $result = FALSE;
        $transferConditions = $this->getPlayerTransferCondition($playerId);

        if(empty($transferConditions)) {
            return $result;
        }

        foreach($transferConditions as $transferCondition){
            if($transferCondition['status'] != self::STATUS_ACTIVE){
                continue;
            }

            $disallow_transfer_in = json_decode($transferCondition['disallow_transfer_in'], true);
            $disallow_transfer_out = json_decode($transferCondition['disallow_transfer_out'], true);

            if(isset($disallow_transfer_in['wallet_id']) && in_array($transfer_to, $disallow_transfer_in['wallet_id'])){
                $result['transfer_in_failed'] = 'Do not allow to transfer to platform_id '. $transfer_to .', because of transfer condition id '. $transferCondition['id'];
            }

            if(isset($disallow_transfer_out['wallet_id']) && in_array($transfer_from, $disallow_transfer_out['wallet_id'])){
                $result['transfer_out_failed'] = 'Do not allow to transfer form platform_id '. $transfer_from .', because of transfer condition id '. $transferCondition['id'];
            }

            // OGP-17203
            if (isset($result['transfer_in_failed']) || isset($result['transfer_out_failed'])) {
                $result['tx_cond'] = $transferCondition;
            }

            $this->utils->debug_log("Player Transfer Conditions", $result);
        }

        return $result;
    }

    public function getAllAvtiveTransferConditions(){
        $this->db->select('*')
                 ->from('transfer_conditions')
                 ->where('transfer_conditions.status', self::STATUS_ACTIVE);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $data[] = $row;
            }
            return $data;
        }

        return FALSE;
    }

    public function updateTransferCondition($data, $id){
        $this->db->set($data);
        $this->db->where("id", $id);
        return $this->runAnyUpdate($this->tableName);
    }

    public function updateStatus($ids, $status) {
        $this->db->set('status', $status)->where_in('id', $ids);
        return $this->runAnyUpdate($this->tableName);
    }

    public function syncPlayerTotalBets(){
        $this->utils->debug_log("<=============================== TRANSFER_CONDITION: SYNC PLAYER TOTAL BETS ===============================>");
        $result = FALSE;

        $acvtiveTransferConditions = $this->getAllAvtiveTransferConditions();
        if(empty($acvtiveTransferConditions)) {
            $this->utils->debug_log("epmty transfercondition",$acvtiveTransferConditions);
            return $result;
        }

        $this->load->model('total_player_game_minute');
        $completed_at = $updated_at = $date_to = $this->utils->getNowForMysql();

        foreach($acvtiveTransferConditions as $transferCondition){
            $update_data = [];
            $total_bet_amount = 0;
            $id = $transferCondition['id'];

            ### GET TOTAL BETS GROUP BY GAME PLATFORM ID ####
            $totalBetsWinsLossGroupByGamePlatformByPlayers = $this->total_player_game_minute->getTotalBetsWinsLossGroupByGamePlatformByPlayers($transferCondition['player_id'], $transferCondition['started_at'], $date_to);
            if(empty($totalBetsWinsLossGroupByGamePlatformByPlayers)){
                $this->utils->debug_log("Get Total Bets Wins Loss Group By Game Platform By Players Result is empty with Tranfer Condition Id ", $transferCondition['id']);
                continue;
            }

            foreach($totalBetsWinsLossGroupByGamePlatformByPlayers as $totalBetsWinsLoss){
                if(isset($totalBetsWinsLoss['0'])){
                    $totalBets = $totalBetsWinsLoss['0'];
                    $total_bet_amount += $totalBets;
                }
            }

            // set update bet_detail and condition amount
            $update_data['bet_details'] = json_encode($totalBetsWinsLossGroupByGamePlatformByPlayers);
            $update_data['updated_at'] = $updated_at;

            if($total_bet_amount >= $transferCondition['condition_amount']){
                //set update status
                $update_data['status'] = self::STATUS_COMPLETE;
                $update_data['completed_at'] = $completed_at;
            }

            $result = $this->updateTransferCondition($update_data, $id);
            if(!$result){
                $this->utils->debug_log("====== update tansfer condition failed , id ", $id);
            }
        }

        return $result;
    }
}

/////end of file///////
