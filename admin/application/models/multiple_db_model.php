<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';
require_once dirname(__FILE__) . '/reports/define_summary2_report.php';
require_once dirname(__FILE__) . '/reports/define_player_report.php';
require_once dirname(__FILE__) . '/reports/define_payment_report.php';
require_once dirname(__FILE__) . '/reports/define_game_report.php';
require_once dirname(__FILE__) . '/reports/define_promotion_report.php';
require_once dirname(__FILE__) . '/reports/define_cashback_report.php';

/**
 * Class Multiple_db_model
 *
 * sync mdb
 */
class Multiple_db_model extends BaseModel {

    use define_summary2_report;
    use define_player_report;
    use define_payment_report;
    use define_game_report;
    use define_promotion_report;
    use define_cashback_report;

    const SUPER_REPORT_TYPE_SUMMARY2='summary2';
    const SUPER_REPORT_TYPE_PLAYER='player';
    const SUPER_REPORT_TYPE_GAME='game';
    const SUPER_REPORT_TYPE_PAYMENT='payment';
    const SUPER_REPORT_TYPE_PORMOTION='promotion';
    const SUPER_REPORT_TYPE_CASHBACK='cashback';

    //===sync mdb=================================================================================
    //sync to other db, so doesn't work on same db transaction

    public function syncRoleFromSuperToOtherMDB($roleId, $insertOnly=false){
        return $this->syncRoleFromOneToOtherMDB(Multiple_db::SUPER_TARGET_DB, $roleId, $insertOnly);
    }

    public function syncRoleFromCurrentToOtherMDB($roleId, $insertOnly=false){
        $sourceDB=$this->getActiveTargetDB();
        return $this->syncRoleFromOneToOtherMDB($sourceDB, $roleId, $insertOnly);
    }

    /**
     * sync role
     * @param  string $roleName
     * @return array
     */
    public function syncRoleFromOneToOtherMDB($sourceDB, $roleId, $insertOnly=false){
        $result=null;
        if($this->utils->isEnabledMDB() && !empty($roleId)){
            // $sourceDB=$this->getActiveTargetDB();
            $roleFuncList=null;
            $roleFuncGivingList=null;
            //get db from super
            $role=$this->runAnyOnSingleMDB($sourceDB,function($db)
                    use($roleId, &$roleFuncList, &$roleFuncGivingList){
                $db->from('roles')->where('roleId', $roleId);
                $role=$this->runOneRowArray($db);
                if(!empty($role)){
                    $db->from('rolefunctions')->where('roleId', $role['roleId']);
                    $rows=$this->runMultipleRowArray($db);
                    $roleFuncList=[];
                    if(!empty($rows)){
                        foreach ($rows as $row) {
                            $roleFuncList[]=$row['funcId'];
                        }
                    }
                    $db->select('funcId')->from('rolefunctions_giving')->where('roleId', $role['roleId']);
                    $rows=$this->runMultipleRowArray($db);
                    $roleFuncGivingList=[];
                    if(!empty($rows)){
                        foreach ($rows as $row) {
                            $roleFuncGivingList[]=$row['funcId'];
                        }
                    }
                }

                return $role;
            });
            if(!empty($role)){

                $roleId=$role['roleId'];
                $active_on_other_mdb=$this->activeItOnOtherMDB('role');

                //copy role and func
                // unset($role['roleId']);
                unset($role['status']);
                $this->utils->debug_log('roleId: '.$roleId, $role, $active_on_other_mdb);

                $result=$this->foreachMultipleDBWithoutSourceDB($sourceDB, function($db, &$rlt)
                        use($insertOnly, $roleId, $role, $roleFuncList, $roleFuncGivingList, $active_on_other_mdb){
                    //update other db
                    $db->select('roleId')->from('roles')->where('roleId', $roleId);
                    $roleId=$this->runOneRowOneField('roleId', $db);
                    $success=false;
                    $inserted=false;
                    if(!empty($roleId)){
                        if(!$insertOnly){
                            $db->where('roleId', $roleId);
                            //update
                            $success=$this->runUpdateData('roles', $role, $db);
                        }else{
                            $success=true;
                        }
                    }else{
                        if($active_on_other_mdb){
                            $role['status']=self::OLD_STATUS_ACTIVE;
                        }
                        //insert with id
                        $roleId=$this->runInsertData('roles', $role, $db);
                        $success=!empty($roleId);
                        $inserted=true;
                    }
                    //if insert only and inserted, or not insert only,  change this
                    if($success && !empty($roleFuncList) && (($inserted && $insertOnly) || !$insertOnly)){
                        //try update role func list
                        //delete first
                        //then insert all
                        $db->where('roleId', $roleId);
                        $this->runRealDelete('rolefunctions', $db);
                        $data=[];
                        foreach ($roleFuncList as $funcId) {
                            $data[]=['roleId'=>$roleId, 'funcId'=>$funcId];
                        }
                        //batch insert
                        $db->insert_batch('rolefunctions', $data);
                        //giving table
                        $db->where('roleId', $roleId);
                        $this->runRealDelete('rolefunctions_giving', $db);
                        if(!empty($roleFuncGivingList)){
                            $data=[];
                            foreach ($roleFuncGivingList as $funcId) {
                                $data[]=['roleId'=>$roleId, 'funcId'=>$funcId];
                            }
                            //batch insert
                            $db->insert_batch('rolefunctions_giving', $data);
                        }
                    }
                    $rlt=$roleId;

                    return $success;
                });
            }else{
                $this->utils->error_log('not found role by roleId: '.$roleId);
            }
        }

        return $result;
    }

    public function syncUserFromCurrentToOtherMDB($userId, $insertOnly=false){
        $sourceDB=$this->getActiveTargetDB();
        return $this->syncUserFromOneToOtherMDB($sourceDB, $userId, $insertOnly);
    }

    public function syncUserFromSuperToOtherMDB($userId, $insertOnly=false){
        return $this->syncUserFromOneToOtherMDB(Multiple_db::SUPER_TARGET_DB, $userId, $insertOnly);
    }

    /**
     * sync user on MDB, super to others
     * @param  string $userId
     * @return
     */
    public function syncUserFromOneToOtherMDB($sourceDB, $userId, $insertOnly=false){

        $result=null;
        if($this->utils->isEnabledMDB() && !empty($userId)){
            // $sourceDB=$this->getActiveTargetDB();
            //get db from active
            $roleId=null;
            $user=$this->runAnyOnSingleMDB($sourceDB, function($db)
                    use($userId, &$roleId){
                $db->from('adminusers')->where('userId', $userId);
                $user=$this->runOneRowArray($db);
                if(!empty($user)){
                    //get role
                    $db->select('userroles.roleId')->from('userroles')
                        ->where('userroles.userId', $user['userId'])->limit(1);
                    $roleId=$this->runOneRowOneField('roleId', $db);
                }
                return $user;
            });

            if(!empty($user) && !empty($roleId)){
                //sync role first
                //just make sure the id is exist
                $this->syncRoleFromOneToOtherMDB($sourceDB, $roleId, true);

                $active_on_other_mdb=$this->activeItOnOtherMDB('user');
                $this->clearFieldsOnData($user, ['maxWidAmt','approvedWidAmt','singleWidAmt',]);

                // $onlySyncFields=$this->getSyncFieldsByTableName('adminusers');
                // $user=$this->cleanSyncFields($user, $onlySyncFields);
                $result=$this->foreachMultipleDBWithoutSourceDB($sourceDB, function($db, &$rlt)
                        use($insertOnly, $userId, $user, $roleId, $active_on_other_mdb){
                    //update other db
                    $db->select('userId')->from('adminusers')->where('userId', $userId);
                    $userId=$this->runOneRowOneField('userId', $db);
                    $success=false;
                    $inserted=false;
                    if(!empty($userId)){
                        if(!$insertOnly){
                            $db->where('userId', $userId);
                            //update
                            $success=$this->runUpdateData('adminusers', $user, $db);
                        }else{
                            $success=true;
                        }
                    }else{
                        //init status
                        if(!$active_on_other_mdb){
                            $user['status']=self::DB_TRUE;
                        }
                        //insert
                        $userId=$this->runInsertData('adminusers', $user, $db);
                        $success=!empty($userId);
                        $inserted=true;
                    }
                    //sync userroles
                    if($success && (($inserted && $insertOnly) || !$insertOnly)){
                        //try set role id by roleName
                        //delete and insert
                        $db->where('userId', $userId);
                        $this->runRealDelete('userroles', $db);
                        //insert
                        $success=$this->runInsertData('userroles', ['userId'=>$userId, 'roleId'=>$roleId], $db);
                    }
                    $rlt=$userId;

                    return $success;
                });
            }else{
                $this->utils->error_log('not found user by userId: '.$userId.' or roleId: '.$roleId);
            }
        }

        return $result;
    }

    public function syncPlayerFromCurrentToOtherMDB($playerId, $insertOnly=false){
        $sourceDB=$this->getActiveTargetDB();
        return $this->syncPlayerFromOneToOtherMDB($sourceDB, $playerId, $insertOnly);
    }

    public function syncPlayerFromSuperToOtherMDB($playerId, $insertOnly=false){
        return $this->syncPlayerFromOneToOtherMDB(Multiple_db::SUPER_TARGET_DB, $playerId, $insertOnly);
    }

    /**
     * sync player from super to others
     * @param  string $playerId
     * @return array of bool
     */
    public function syncPlayerFromOneToOtherMDB($sourceDB, $playerId, $insertOnly=false){
        $result=null;
        if($this->utils->isEnabledMDB() && !empty($playerId)){
            // $sourceDB=$this->getActiveTargetDB();
            //get db from super
            $player=$this->runAnyOnSingleMDB($sourceDB,function($db)
                    use($playerId){
                $db->from('player')->where('playerId', $playerId);
                return $this->runOneRowArray($db);
            });
            if(!empty($player)){

                $playerId=$player['playerId'];
                list($playerDetails, $updateHistoryList, $invitedPlayer, $playernotes)=$this->runAnyOnSingleMDB($sourceDB, function($db)
                        use($playerId){
                    $db->from('playerdetails')->where('playerId', $playerId);
                    $details=$this->runOneRowArray($db);
                    $db->from('playerupdatehistory')->where('playerId', $playerId);
                    $history=$this->runMultipleRowArray($db);
                    //sync be invited player id
                    $db->from('playerfriendreferral')->where('invitedPlayerId', $playerId);
                    $invitedPlayer=$this->runOneRowArray($db);

                    $db->from('playernotes')->where('playerId', $playerId);
                    $playernotes=$this->runMultipleRowArray($db);

                    return [$details, $history, $invitedPlayer, $playernotes];
                });
                $active_on_other_mdb=$this->activeItOnOtherMDB('player');

                //sync aff and agent
                if(!empty($player['agent_id'])){
                    //just make sure the id is exist
                    $this->syncAgencyFromOneToOtherMDB($sourceDB, $player['agent_id'], true);
                }
                if(!empty($player['affiliateId'])){
                    //just make sure the id is exist
                    $this->syncAffFromOneToOtherMDB($sourceDB, $player['affiliateId'], true);
                }

                $this->clearFieldsOnData($player, ['approved_deposit_count', 'declined_deposit_count', 'total_deposit_count', 'totalBettingAmount',
                    'refereePlayerId', 'totalDepositAmount', 'session_id', 'deleted_at', 'approvedWithdrawCount', 'approvedWithdrawAmount',
                    'big_wallet', 'main_real', 'main_bonus', 'main_cashback', 'main_win_real', 'main_win_bonus', 'main_withdrawable', 'main_total_nofrozen',
                    'main_total', 'total_real', 'total_bonus', 'total_cashback', 'total_win_real', 'total_win_bonus', 'total_withdrawable', 'total_frozen',
                    'total_total_nofrozen', 'total_total',]);

                //copy player playerdetails
                // $onlySyncFieldsOnPlayer=$this->getSyncFieldsByTableName('player');
                // $onlySyncFieldsOnPlayerDetails=$this->getSyncFieldsByTableName('playerdetails');
                $this->clearFieldsOnData($playerDetails, ['playerDetailsId']);

                $this->utils->debug_log('playerId: '.$playerId, $player, $playerDetails,
                    $active_on_other_mdb);

                // $player=$this->cleanSyncFields($player, $onlySyncFieldsOnPlayer);
                // $playerDetails=$this->cleanSyncFields($playerDetails, $onlySyncFieldsOnPlayerDetails);
                $result=$this->foreachMultipleDBWithoutSourceDB($sourceDB, function($db, &$rlt)
                        use($sourceDB, $insertOnly, $playerId, $player, $active_on_other_mdb,
                            $playerDetails, $updateHistoryList, $invitedPlayer, $playernotes){
                    //update other db
                    $db->select('playerId')->from('player')->where('playerId', $playerId);
                    $playerId=$this->runOneRowOneField('playerId', $db);
                    $success=false;
                    $inserted=false;
                    $currencyInfo=$this->getCurrencyByDB($db);
                    $player['currency']=$currencyInfo['code'];

                    unset($player['levelId']);
                    unset($player['levelName']);
                    unset($player['groupName']);

                    if(!empty($playerId)){
                        if(!$insertOnly){
                            $db->where('playerId', $playerId);
                            //update
                            $success=$this->runUpdateData('player', $player, $db);
                        }else{
                            $success=true;
                        }
                    }else{
                        if(!$active_on_other_mdb){
                            $player['status']=self::OLD_STATUS_ACTIVE;
                        }
                        //set default language
                        $playerDetails['language']=$currencyInfo['player_default_language'];
                        //insert
                        $playerId=$this->runInsertData('player', $player, $db);
                        $success=!empty($playerId);
                        if($success){
                            //update wallet to empty
                            $this->wallet_model->refreshBigWalletOnDB($playerId, $db);
                            //set level
                            $levelId = $currencyInfo['player_default_level_id'];

                            $this->runInsertData('playerlevel', array(
                                'playerId' => $playerId,
                                'playerGroupId' => $levelId,
                            ), $db);

                            $db->select('vipsettingcashbackrule.vipLevelName,vipsetting.groupName')->from('vipsettingcashbackrule')
                                ->join('vipsetting', 'vipsettingcashbackrule.vipSettingId = vipsetting.vipSettingId')
                                ->where('vipsettingcashbackrule.vipsettingcashbackruleId', $levelId)->limit(1);
                            $row=$this->runOneRowArray($db);

                            $groupName = $row['groupName'];
                            $levelName = $row['vipLevelName'];

                            $db->where('playerId', $playerId);
                            $success=$this->runUpdateData('player', [
                                'levelId' => $levelId,
                                'groupName' => $groupName,
                                'levelName' => $levelName,
                            ], $db);

                        }
                    }

                    if($success){
                        $db->select('playerDetailsId')->from('playerdetails')->where('playerId', $playerId);
                        $playerDetailsId=$this->runOneRowOneField('playerDetailsId', $db);

                        if(!empty($playerDetailsId)){
                            $db->where('playerDetailsId', $playerDetailsId);
                            //update
                            $success=$this->runUpdateData('playerdetails', $playerDetails, $db);
                        }else{
                            $playerDetailsId=$this->runInsertData('playerdetails', $playerDetails, $db);
                            $success=!empty($playerDetailsId);
                        }
                    }

                    if($success && !empty($updateHistoryList)){
                        $db->where('playerId', $playerId);
                        $success=$this->runRealDelete('playerupdatehistory', $db);
                        if($success){
                            $data=[];
                            //sync updateHistoryList
                            foreach ($updateHistoryList as $row) {
                                unset($row['playerUpdateHistoryId']);
                                $data[]=$row;
                            }
                            $success=$this->runBatchInsertWithLimit($db, 'playerupdatehistory', $data);
                        }
                    }
                    if($success && !empty($playernotes)){
                        $db->where('playerId', $playerId);
                        $success=$this->runRealDelete('playernotes', $db);
                        if($success){
                            $data=[];
                            //sync updateHistoryList
                            foreach ($playernotes as $row) {
                                unset($row['noteId']);
                                $data[]=$row;
                            }
                            $success=$this->runBatchInsertWithLimit($db, 'playernotes', $data);
                        }
                    }
                    if($success && !empty($invitedPlayer)){
                        $db->select('referralId')->from('playerfriendreferral')->where('invitedPlayerId', $playerId);
                        $referralId=$this->runOneRowOneField('referralId', $db);
                        if(empty($referralId)){
                            //insert if not exist
                            unset($invitedPlayer['referralId']);
                            $referralId=$this->runInsertData('playerfriendreferral', $invitedPlayer, $db);
                            $success=!empty($referralId);
                        }
                    }

                    $rlt=$playerId;

                    return $success;
                });
            }else{
                $this->utils->error_log('not found player by playerId: '.$playerId);
            }
        }

        return $result;
    }

    public function showResultOnMDB($sql, $params){

        return $this->runRawSelectSQLArrayOnMDB($sql, $params);

    }

    public function listPlayerFromMDB($username){

        return $this->foreachMultipleDB(function($db, &$result)
            use($username){

            $db->from('player')->where('username', $username);
            $player=$this->runOneRowArray($db);
            // $this->utils->debug_log('search by '.$username, $user);
            $playerDetails=null;
            $success=!empty($player);
            if($success){
                $db->from('playerdetails')
                    ->where('playerId', $player['playerId']);
                $playerDetails=$this->runOneRowArray($db);
                // $this->utils->debug_log('the player details is', $role);
            }else{
                $this->utils->error_log('not found user', $username);
            }

            $result=['player'=>$player, 'playerDetails'=>$playerDetails];

            return $success;
        });

    }

    public function syncAgencyFromCurrentToOtherMDB($agent_id, $insertOnly=false){
        $sourceDB=$this->getActiveTargetDB();
        return $this->syncAgencyFromOneToOtherMDB($sourceDB, $agent_id, $insertOnly);
    }

    public function syncAgencyFromSuperToOtherMDB($agent_id, $insertOnly=false){
        return $this->syncAgencyFromOneToOtherMDB(Multiple_db::SUPER_TARGET_DB, $agent_id, $insertOnly);
    }

    /**
     * sync player from super to others
     * @param  string $agent_id
     * @return array of bool
     */
    public function syncAgencyFromOneToOtherMDB($sourceDB, $agent_id, $insertOnly=false){
        $result=null;
        if($this->utils->isEnabledMDB() && !empty($agent_id)){

            $this->load->model(['agency_model']);

            $agent=$this->runAnyOnSingleMDB($sourceDB,function($db)
                    use($agent_id){
                $db->from('agency_agents')->where('agent_id', $agent_id);
                return $this->runOneRowArray($db);
            });
            if(!empty($agent)){

                $agent_id=$agent['agent_id'];
                list($agency_agent_game_platforms, $agency_agent_game_types, $agency_flattening, $agency_flattening_options,
                    $agency_tracking_domain)=
                    $this->runAnyOnSingleMDB($sourceDB, function($db)
                        use($agent_id){
                    //agency_agent_game_platforms
                    //agency_agent_game_types
                    //agency_flattening
                    //agency_flattening_options
                    //agency_tier_comm_patterns
                    //agency_tier_comm_pattern_tiers
                    $db->from('agency_agent_game_platforms')->where('agent_id', $agent_id);
                    $agency_agent_game_platforms=$this->runMultipleRowArray($db);
                    $db->from('agency_agent_game_types')->where('agent_id', $agent_id);
                    $agency_agent_game_types=$this->runMultipleRowArray($db);
                    $db->from('agency_flattening')->where('agent_id', $agent_id);
                    $agency_flattening=$this->runMultipleRowArray($db);
                    $db->from('agency_flattening_options')->where('agent_id', $agent_id);
                    $agency_flattening_options=$this->runMultipleRowArray($db);
                    $db->from('agency_tracking_domain')->where('agent_id', $agent_id);
                    $agency_tracking_domain=$this->runMultipleRowArray($db);
                    // $this->utils->printLastSQL($db);
                    // $db->from('agency_tier_comm_patterns')->where('agent_id', $agent_id);
                    // $agency_tier_comm_patterns=$this->runMultipleRowArray($db);
                    // $db->from('agency_tier_comm_pattern_tiers')->where('agent_id', $agent_id);
                    // $agency_tier_comm_pattern_tiers=$this->runMultipleRowArray($db);

                    return [$agency_agent_game_platforms, $agency_agent_game_types, $agency_flattening, $agency_flattening_options,
                        $agency_tracking_domain];
                });
                $active_on_other_mdb=$this->activeItOnOtherMDB('agency');

                $this->clearFieldsOnData($agent, ['credit_limit', 'available_credit', 'wallet_balance', 'wallet_hold', 'frozen']);

                $this->clearFieldsOnRows($agency_agent_game_platforms, ['id']);
                $this->clearFieldsOnRows($agency_agent_game_types, ['id']);
                $this->clearFieldsOnRows($agency_flattening, ['id']);
                $this->clearFieldsOnRows($agency_flattening_options, ['id']);
                $this->clearFieldsOnRows($agency_tracking_domain, ['id']);

                $this->utils->debug_log('agent_id: '.$agent_id, $active_on_other_mdb, 'agency_tracking_domain', $agency_tracking_domain);
                // $agent=$this->cleanSyncFields($agent, $onlySyncFields);
                $result=$this->foreachMultipleDBWithoutSourceDB($sourceDB, function($db, &$rlt)
                        use($agent_id, $agent, $agency_agent_game_platforms, $agency_agent_game_types, $agency_flattening,
                            $agency_flattening_options, $agency_tracking_domain, $active_on_other_mdb){
                    //update other db
                    $db->select('agent_id')->from('agency_agents')->where('agent_id', $agent_id);
                    $agent_id=$this->runOneRowOneField('agent_id', $db);
                    $success=false;

                    $currencyInfo=$this->getCurrencyByDB($db);
                    //adjust field
                    $agent['currency']=$currencyInfo['code'];
                    if(!empty($agent_id)){
                        $db->where('agent_id', $agent_id);
                        //update
                        $success=$this->runUpdateData('agency_agents', $agent, $db);
                    }else{
                        if(!$active_on_other_mdb){
                            $agent['status']=agency_model::AGENT_STATUS_FROZEN;
                        }
                        $agent['language']=$currencyInfo['default_language'];
                        //insert
                        $agent_id=$this->runInsertData('agency_agents', $agent, $db);
                        $success=!empty($agent_id);
                    }
                    // if($success){
                    //     //sync agency_agent_game_platforms, delete then insert
                    //     $db->where('agent_id', $agent_id);
                    //     $this->runRealDelete('agency_agent_game_platforms', $db);
                    //     $success=$this->runBatchInsertWithLimit($db, 'agency_agent_game_platforms', $agency_agent_game_platforms);
                    // }
                    // if($success){
                    //     //sync agency_agent_game_types, delete then insert
                    //     $db->where('agent_id', $agent_id);
                    //     $this->runRealDelete('agency_agent_game_types', $db);
                    //     $success=$this->runBatchInsertWithLimit($db, 'agency_agent_game_types', $agency_agent_game_types);
                    // }
                    if($success){
                        //sync agency_flattening, delete then insert
                        $db->where('agent_id', $agent_id);
                        $this->runRealDelete('agency_flattening', $db);
                        $success=$this->runBatchInsertWithLimit($db, 'agency_flattening', $agency_flattening);
                    }
                    if($success){
                        //sync agency_flattening_options, delete then insert
                        $db->where('agent_id', $agent_id);
                        $this->runRealDelete('agency_flattening_options', $db);
                        $success=$this->runBatchInsertWithLimit($db, 'agency_flattening_options', $agency_flattening_options);
                    }
                    if($success){
                        // $this->utils->debug_log('sync agency_tracking_domain');
                        //sync agency_tracking_domain, delete then insert
                        $db->where('agent_id', $agent_id);
                        $this->runRealDelete('agency_tracking_domain', $db);
                        // $this->utils->printLastSQL($db);
                        $success=$this->runBatchInsertWithLimit($db, 'agency_tracking_domain', $agency_tracking_domain);
                        // $this->utils->printLastSQL($db);

                        // $db->from('agency_tracking_domain')->where('agent_id', $agent_id);
                        // $this->utils->info_log($this->runMultipleRowArray($db));
                    }

                    $this->utils->debug_log('success', $success);

                    $rlt=$agent_id;

                    return $success;
                });

            }else{
                $this->utils->error_log('not found agency by agent_id: '.$agent_id);
            }
        }
        return $result;
    }
    public function syncAffFromCurrentToOtherMDB($affiliateId, $insertOnly=false){
        $sourceDB=$this->getActiveTargetDB();
        return $this->syncAffFromOneToOtherMDB($sourceDB, $affiliateId, $insertOnly);
    }

    public function syncAffFromSuperToOtherMDB($affiliateId, $insertOnly=false){
        return $this->syncAffFromOneToOtherMDB(Multiple_db::SUPER_TARGET_DB, $affiliateId, $insertOnly);
    }

    /**
     * sync affiliate from super to others
     * @param  string $affiliateId
     * @return array of bool
     */
    public function syncAffFromOneToOtherMDB($sourceDB, $affiliateId, $insertOnly=false){
        $result=null;
        if($this->utils->isEnabledMDB() && !empty($affiliateId)){
            $aff=$this->runAnyOnSingleMDB($sourceDB,function($db)
                    use($affiliateId){
                $db->from('affiliates')->where('affiliateId', $affiliateId);
                return $this->runOneRowArray($db);
            });

            list($affiliate_terms, $aff_tracking_link, $affiliate_read_only_account)=$this->runAnyOnSingleMDB($sourceDB, function($db)
                use($affiliateId){
                $db->from('affiliate_terms')->where('affiliateId', $affiliateId);
                $affiliate_terms=$this->runMultipleRowArray($db);
                //aff_tracking_link
                $db->from('aff_tracking_link')->where('aff_id', $affiliateId);
                $aff_tracking_link=$this->runMultipleRowArray($db);
                // affiliate_read_only_account
                $db->from('affiliate_read_only_account')->where('affiliate_id', $affiliateId);
                $affiliate_read_only_account=$this->runMultipleRowArray($db);
                return [$affiliate_terms, $aff_tracking_link, $affiliate_read_only_account];
            });

            $this->clearFieldsOnData($aff, ['balance', 'wallet_balance', 'wallet_hold', 'frozen']);

            //remove id
            $this->clearFieldsOnRows($affiliate_terms, ['id']);
            $this->clearFieldsOnRows($aff_tracking_link, ['id']);
            $this->clearFieldsOnRows($affiliate_read_only_account, ['id']);

            $this->utils->debug_log('affiliate_terms', $affiliate_terms, 'aff_tracking_link', $aff_tracking_link,
                'affiliate_read_only_account', $affiliate_read_only_account);

            if(!empty($aff)){
                $active_on_other_mdb=$this->activeItOnOtherMDB('affiliate');

                $this->utils->debug_log('affiliateId: '.$affiliateId, $active_on_other_mdb);
                // $agent=$this->cleanSyncFields($agent, $onlySyncFields);
                $result=$this->foreachMultipleDBWithoutSourceDB($sourceDB, function($db, &$rlt)
                        use($affiliateId, $aff, $active_on_other_mdb,
                            $affiliate_terms, $aff_tracking_link, $affiliate_read_only_account){
                    //update other db
                    $db->select('affiliateId')->from('affiliates')->where('affiliateId', $affiliateId);
                    $affiliateId=$this->runOneRowOneField('affiliateId', $db);
                    $success=false;
                    $currencyInfo=$this->getCurrencyByDB($db);
                    //set currency
                    $aff['currency']=$currencyInfo['code'];
                    $this->utils->debug_log('search by id', $affiliateId);
                    if(!empty($affiliateId)){
                        $db->where('affiliateId', $affiliateId);
                        //update
                        $success=$this->runUpdateData('affiliates', $aff, $db);
                    }else{
                        if(!$active_on_other_mdb){
                            $aff['status']=self::OLD_STATUS_ACTIVE;
                        }
                        $aff['language']=$currencyInfo['default_language'];
                        //insert
                        $affiliateId=$this->runInsertData('affiliates', $aff, $db);
                        $success=!empty($affiliateId);
                    }

                    //sync $affiliate_terms
                    // if($success){
                    //     //sync affiliate_terms, delete then insert
                    //     $db->where('affiliateId', $affiliateId);
                    //     $this->runRealDelete('affiliate_terms', $db);
                    //     // $this->utils->printLastSQL($db);
                    //     $success=$this->runBatchInsertWithLimit($db, 'affiliate_terms', $affiliate_terms);
                    // }
                    //sync $aff_tracking_link
                    if($success){
                        //sync aff_tracking_link, delete then insert
                        $db->where('aff_id', $affiliateId);
                        $this->runRealDelete('aff_tracking_link', $db);
                        $success=$this->runBatchInsertWithLimit($db, 'aff_tracking_link', $aff_tracking_link);
                    }
                    //sync $affiliate_read_only_account
                    if($success){
                        //sync affiliate_read_only_account, delete then insert
                        $db->where('affiliate_id', $affiliateId);
                        $this->runRealDelete('affiliate_read_only_account', $db);
                        $success=$this->runBatchInsertWithLimit($db, 'affiliate_read_only_account', $affiliate_read_only_account);
                    }

                    $rlt=$affiliateId;

                    return $success;
                });

            }else{
                $this->utils->error_log('not found affiliate by affiliateId: '.$affiliateId);
            }
        }

        return $result;
    }

    public function clearFieldsOnRows(&$rows, $fields){
        if(!empty($rows)){
            foreach ($rows as &$row) {
                $this->clearFieldsOnData($row, $fields);
            }
        }
    }

    public function clearFieldsOnData(&$row, $fields){
        foreach ($fields as $fldName) {
            unset($row[$fldName]);
        }
    }

    //===sync mdb=================================================================================

    public function getCurrencyByDB($db){
        if(empty($db)){
            return $this->utils->getConfig('super_default_currency_info');
        }
        $availableCurrencyList=$this->utils->getAvailableCurrencyList();
        $dbKey=$db->getOgTargetDB();
        if($dbKey==Multiple_db::SUPER_TARGET_DB || empty($availableCurrencyList)){
            return $this->utils->getConfig('super_default_currency_info');
        }
        return $availableCurrencyList[$dbKey];
    }

    public function enableCurrencyForPlayerOnSuper($playerId, $currencyKey){

    }

    public function disableCurrencyForPlayerOnSuper($playerId, $currencyKey){

    }

    const ID_TYPE_PLAYER_ID='player_id';
    const ID_TYPE_AGENCY_ID='agency_id';
    const ID_TYPE_USER_ID='user_id';
    const ID_TYPE_AFFILIATE_ID='affiliate_id';

    public function enableDisableCurrencyOnDB($id, $type,
        $enableCurrencyKeyList, $disableCurrencyKeyList, $db){

        if(empty($id)){
            return false;
        }

        $idFldName=null;
        switch ($type) {
            case self::ID_TYPE_PLAYER_ID:
                $idFldName='player_id';
                break;
            case self::ID_TYPE_AGENCY_ID:
                $idFldName='agency_id';
                break;
            case self::ID_TYPE_USER_ID:
                $idFldName='user_id';
                break;
            case self::ID_TYPE_AFFILIATE_ID:
                $idFldName='affiliate_id';
                break;
            default:
                return false;
                break;
        }

        $currencyKeyList=array_merge($enableCurrencyKeyList, $disableCurrencyKeyList);
        //delete
        $db->where($idFldName, $id)->where_in('currency_key', $currencyKeyList)
            ->where('status', self::DB_FALSE);
        $success=$this->runRealDelete('currency_permissions', $db);
        if($success && !empty($disableCurrencyKeyList)){
            $rows=[];
            //insert disable only
            foreach ($disableCurrencyKeyList as $currencyKey) {
                $rows[]=[
                    $idFldName=>$id,
                    'currency_key'=>$currencyKey,
                    'status'=>self::DB_FALSE,
                    'created_at'=>$this->utils->getNowForMysql(),
                ];
            }

            $success=$this->runBatchInsertWithLimit($db, 'currency_permissions', $rows);
        }

        return $success;

    }


    public function syncPlayerRegSettingsFromCurrentToOtherMDB($type){
        $sourceDB=$this->getActiveTargetDB();
        return $this->syncPlayerRegSettingsFromOneToOtherMDB($sourceDB, $type);
    }

    public function syncPlayerRegSettingsFromSuperToOtherMDB($type){
        return $this->syncPlayerRegSettingsFromOneToOtherMDB(Multiple_db::SUPER_TARGET_DB, $type);
    }

    /**
     * sync player reg settings from super to others
     * @param  string $playerId
     * @return array of bool
     */
    public function syncPlayerRegSettingsFromOneToOtherMDB($sourceDB, $type){
        $result=null;
        if($this->utils->isEnabledMDB()){
            list($playerRegSettings, $operatorSettings)=$this->runAnyOnSingleMDB($sourceDB,function($db)
                    use($type){

                $settings=['registration_captcha_enabled', 'login_captcha_enabled',
                    'login_after_registration_enabled', 'login_after_registration_enabled',
                    'remember_password_enabled', 'forget_password_enabled',
                    'restrict_password_enabled', 'restrict_username_enabled',
                    'generate_pep_gbg_auth_after_registration_enabled', 'set_password_min_max',
                ];

                $db->from('operator_settings')->where_in('name', $settings);
                $operatorSettings=$this->runMultipleRowArray($db);

                $db->from('registration_fields')->where('type', $type);
                $playerRegSettings=$this->runMultipleRowArray($db);

                return [$playerRegSettings, $operatorSettings];
            });
            if(!empty($playerRegSettings)){
                $result=$this->foreachMultipleDBWithoutSourceDB($sourceDB, function($db, &$rlt)
                        use($playerRegSettings, $operatorSettings, $type){

                    $rlt=['registration_fields'=>0, 'operator_settings'=>0];

                    //delete then insert all
                    $db->where('type', $type);
                    $success=$this->runRealDelete('registration_fields', $db);
                    if($success){
                        foreach ($playerRegSettings as $row) {
                            unset($row['id']);
                            $success=$this->runInsertData('registration_fields', $row, $db);
                            if(!$success){
                                break;
                            }
                            $rlt['registration_fields']++;
                        }
                    }
                    //check name
                    if($success){
                        foreach ($operatorSettings as $row) {
                            unset($row['id']);
                            $db->where('name', $row['name']);
                            $success=$this->runRealDelete('operator_settings', $db);
                            if(!$success){
                                break;
                            }
                            $success=$this->runInsertData('operator_settings', $row, $db);
                            if(!$success){
                                break;
                            }
                            $rlt['operator_settings']++;
                        }
                        //clear cache
                        $this->utils->deleteCache();
                    }
                    return $success;
                });
            }

        }

        return $result;
    }

    public function syncAffiliateRegSettingsFromCurrentToOtherMDB($type){
        $sourceDB=$this->getActiveTargetDB();
        return $this->syncAffiliateRegSettingsFromOneToOtherMDB($sourceDB, $type);
    }

    public function syncAffiliateRegSettingsFromSuperToOtherMDB($type){
        return $this->syncAffiliateRegSettingsFromOneToOtherMDB(Multiple_db::SUPER_TARGET_DB, $type);
    }

    /**
     * sync aff reg settings from super to others
     * @param  string $playerId
     * @return array of bool
     */
    public function syncAffiliateRegSettingsFromOneToOtherMDB($sourceDB, $type){
        $result=null;
        if($this->utils->isEnabledMDB()){
            $affRegSettings=$this->runAnyOnSingleMDB($sourceDB,function($db)
                    use($type){

                $db->from('registration_fields')->where('type', $type);
                $affRegSettings=$this->runMultipleRowArray($db);

                return $affRegSettings;
            });
            if(!empty($affRegSettings)){
                $result=$this->foreachMultipleDBWithoutSourceDB($sourceDB, function($db, &$rlt)
                        use($affRegSettings, $type){

                    $rlt=['registration_fields'=>0];

                    //delete then insert all
                    $db->where('type', $type);
                    $success=$this->runRealDelete('registration_fields', $db);
                    if($success){
                        foreach ($affRegSettings as $row) {
                            unset($row['id']);
                            $success=$this->runInsertData('registration_fields', $row, $db);
                            if(!$success){
                                break;
                            }
                            $rlt['registration_fields']++;
                        }
                    }
                    return $success;
                });
            }

        }

        return $result;
    }

    //===super report===================================================
    const QUERY_REPORT_TYPE_ONE_PAGE='onePage';
    const QUERY_REPORT_TYPE_COUNT='count';
    const QUERY_REPORT_TYPE_TOTAL='total';
    const QUERY_REPORT_TYPE_SUMMARY='summary';
    const QUERY_REPORT_TYPE_EXPORT='export';
    public function syncSummary2Report($from, $to){
        //insert to super db
        return $this->mergeTableOverMDBToSuper('summary2_report_daily', 'summary_date>=? and summary_date<=?', [$from, $to]);
    }
	public function syncPlayerReportHourly($from, $to){
		$from=$this->utils->formatDateHourForMysql(new DateTime($from));
		$to=$this->utils->formatDateHourForMysql(new DateTime($to));
		//insert to super db
		return $this->mergeTableOverMDBToSuper('player_report_hourly', 'date_hour>=? and date_hour<=?', [$from, $to]);
	}
    public function syncGameReportHourly($from, $to){
        $from=$this->utils->formatDateHourForMysql(new DateTime($from));
        $to=$this->utils->formatDateHourForMysql(new DateTime($to));
        //insert to super db
        return $this->mergeTableOverMDBToSuper('game_report_hourly', 'date_hour>=? and date_hour<=?', [$from, $to]);
    }
    public function syncPaymentReportDaily($from, $to){
        $from=$this->utils->formatDateForMysql(new DateTime($from));
        $to=$this->utils->formatDateForMysql(new DateTime($to));
        //sync to super db
        return $this->mergeTableOverMDBToSuper('payment_report_daily', 'payment_date>=? and payment_date<=?', [$from, $to]);
    }
    public function syncCashbackReportDaily($from, $to){
        $from=$this->utils->formatDateForMysql(new DateTime($from));
        $to=$this->utils->formatDateForMysql(new DateTime($to));
        //sync to super db
        return $this->mergeTableOverMDBToSuper('cashback_report_daily', 'cashback_date>=? and cashback_date<=?', [$from, $to]);
    }
    public function syncPromotionReportDetails($from, $to){
        //sync to super db
        return $this->mergeTableOverMDBToSuper('promotion_report_details', 'promotion_datetime>=? and promotion_datetime<=?', [$from, $to]);
    }

    /**
     *
     * build select query
     * set select on db
     *
     * @param  array $columns
     * @param  object $db
     */
    public function buildSelectOnDBFromColumns($columns, $db){
        // 'dt' => index of column,
        // 'alias' => sql alias,
        // 'name' => readable name,
        // 'select' => sql select string,
        // 'visible' => ,
        // 'formatter' => formatter function or standard formatter
        if(!empty($columns)){
            foreach ($columns as $col) {
                //default is visible
                if(!isset($col['visible']) || $col['visible']){
                    if(isset($col['alias'])){
                        $db->select($col['select'].' as '.$col['alias'], false);
                    }else{
                        $db->select($col['select'], false);
                    }
                }
            }
        }
    }
    /**
     * build count on db
     * @param  array $columns
     * @param  object $db
     * @return array $colForCount
     */
    public function buildCountOnDBFromColumns($columns, $db){
        $colForCount=null;
        if(!empty($columns)){
            foreach ($columns as $col) {
                if(isset($col['key_for_count'])){
                    $db->select('count('.$col['select'].') as '.$col['alias'], false);
                    $colForCount=$col;
                    break;
                }
            }
        }
        return $colForCount;
    }
    /**
     * build total columns
     * @param  array $columns
     * @param  object $db
     * @return array $totalColMap
     */
    public function buildTotalOnDBFromColumns($columns, $db, &$existsSumField=false){
        // $map=[];
        // $i=0;
        foreach ($columns as $column) {
            if(!isset($col['visible']) || $col['visible']){
                //only keep sum field
                if(isset($column['select_sum'])){
                    $existsSumField=true;
                    $alias=$column['select'];
                    if(isset($column['alias'])){
                        $alias=$column['alias'];
                    }
                    $db->select($column['select_sum'].' as '.$alias, false);
                    // $map[$i]=$column;
                    // $i++;
                }else{
                    $alias=$column['select'];
                    if(isset($column['alias'])){
                        $alias=$column['alias'];
                    }
                    $db->select('"" as '.$alias, false);
                }
            }
        }

        // return $map;
    }
    // public function convertToSumColumns($columns){
    //     $sumColumns=[];
    //     // $i=0;
    //     foreach ($columns as $column) {
    //         //only keep sum field
    //         if(isset($column['select_sum']) && isset($column['alias'])){
    //             $sumColumns[]=$column;
    //         }
    //     }
    //     return $sumColumns;
    // }
    /**
     *
     * @param  array $joinList
     * [
     *     ['table'=>, 'mode'=>(left, inner), 'join_condition'=>]
     * ]
     * @param  object $db
     *
     */
    public function buildJoinOnDB($joinList, $db){
        if(!empty($joinList)){
            foreach ($joinList as $join) {
                if(empty($join['mode'])){
                    $join['mode']='inner';
                }
                $db->join($join['table'], $join['join_condition'], $join['mode']);
            }
        }
    }

    /**
     *
     * @param  array $groupList,
     * @param  object $db
     *
     */
    public function buildGroupByOnDB($groupList, $db){
        if(!empty($groupList)){
            foreach ($groupList as $group) {
                //no escape
                $db->group_by($group, false);
            }
        }
    }
    public function buildOrderByOnDB($orderList, $db){
        if(!empty($orderList)){
            foreach ($orderList as $ord) {
                //no escape
                $db->order_by($ord['field'], $ord['direction'], false);
            }
        }
    }
    public function buildSimpleWhereOnDB($whereList, $db){
        if(!empty($whereList)){
            foreach ($whereList as $where) {
                $db->where($where['key'], $where['value']);
            }
        }
    }
    /**
     * build limit on $db
     * @param  array $conditions
     * @param  object $db
     */
    public function buildCommonLimitOnDB($conditions, $db){
        $size=$conditions['limitBy']['sizePerPage'];
        $startRow=($conditions['limitBy']['currentPage']-1)*$size;
        $db->limit($size, $startRow);
    }
    /**
     * 'dt' => index of column,
     * 'alias' => sql alias,
     * 'name' => readable name,
     * 'select' => sql select string,
     * 'visible' => ,
     * 'formatter' => formatter function or standard formatter
     *
     * @param  array $columns
     * @return array $header ['label'=>, 'key'=>, 'fixed'=>]
     *
     */
    public function buildHeaderByColumns($columns){
        $header=[];
        if(!empty($columns)){
            foreach ($columns as $column) {
                if(!isset($column['dt'])){
                    continue;
                }
                if(isset($col['visible']) && !$col['visible']){
                    continue;
                }
                $h=['label'=>$column['name'], 'key'=>$column['alias'], 'fixed'=>false,
                'sortable'=>true, 'align'=>'left'];
                if(isset($column['fixed'])){
                    $h['fixed']=$column['fixed'];
                }
                if(isset($column['sortable'])){
                    $h['sortable']=$column['sortable'];
                }
                if(isset($column['width'])){
                    $h['width']=intval($column['width']);
                }
                if(isset($column['minWidth'])){
                    $h['minWidth']=intval($column['minWidth']);
                }
                if(isset($column['align'])){
                    $h['align']=$column['align'];
                }else{
                    if(isset($column['formatter']) ){
                        if($column['formatter']=='currencyFormatter'
                            || $column['formatter']=='percentageFormatter'){
                            $h['align']='right';
                        }
                    }
                }
                $header[]=$h;
            }
        }
        return $header;
    }

    public function buildNameListFromColumns($columns){
        $headerNameList=[];
        foreach ($columns as $col) {
            //index and visible
            if(!isset($col['dt'])){
                continue;
            }
            if(isset($col['visible']) && !$col['visible']){
                continue;
            }

            $headerNameList[]=$col['name'];
        }
        return $headerNameList;
    }

    public function formatAnyValue($type, &$d, $row, $keepEmpty=false, $exporting=false){
        $success=true;
        if(is_callable($type)){
            //call format function
            $d=$type($d, $row, $exporting);
            if(is_array($d)){
                $this->utils->debug_log('format result', $d);
                $d=$d['value'];
            }
            return $success;
        }
        if(($d===null || $d==='') && $keepEmpty){
            $d='';
            return $success;
        }

        switch ($type) {
            case 'percentageFormatter':
                if(empty($d)){
                    $d=0;
                }
                $d=$this->utils->formatCurrencyNoSym($d * 100).'%';
                break;
            case 'currencyFormatter':
                if(empty($d)){
                    $d=0;
                }
                $d=$this->utils->formatCurrencyNoSym($d);
                break;
            case 'languageFormatter':
                if(!empty($d)){
                    $d=lang($d);
                }else{
                    $d='';
                }
                break;
            case 'dateTimeFormatter':
                if(!empty($d)){
                    $d=date('Y-m-d H:i:s', strtotime($d));
                }else{
                    $d='';
                }
                break;
            case 'dateFormatter':
                if(!empty($d)){
                    $d=date('Y-m-d', strtotime($d));
                }else{
                    $d='';
                }
                break;
            default:
                //unknown formatter
                $this->utils->error_log('unknown formatter', $type, $d);
                $success=false;
                break;
        }

        return $success;
    }

    public function formatColumns(&$row, $columns, $headerOfDB, $keepEmpty=false, $exporting=false){
        $success=true;

        $asscRow=array_combine($headerOfDB, $row);
        for ($i=0; $i < count($row); $i++) {
            // $this->utils->debug_log($i, $colMap[$i]);
            if(isset($columns[$i]) && isset($columns[$i]['formatter'])){
                $success=$this->formatAnyValue($columns[$i]['formatter'], $row[$i], $asscRow, $keepEmpty, $exporting);
                if(!$success){
                    break;
                }
            }
        }

        return $success;
    }

    // public function convertColumnsToMap($columns){
    //     $map=[];
    //     for ($i=0; $i < count($columns); $i++) {
    //         //only keep exist index
    //         $map[$i]=$columns[$i];
    //     }

    //     return $map;
    // }
    public function generateOrderByDefine($orderBy, $columns){
        $orderList=[];
        if(!empty($orderBy)){
            if(!empty($orderBy['orderAlias']) && !empty($orderBy['direction'])){
                //try add
                $orderList[]=[
                    'field'=>$orderBy['orderAlias'],
                    'direction'=>$orderBy['direction']];
            }
        }
        return $orderList;
    }

    public function getSuperReportSettings($reportType){
        $settings;
        $super_report_settings=$this->utils->getConfig('super_report_settings');
        if(isset($super_report_settings[$reportType])){
            $settings=$super_report_settings[$reportType];
        }else{
            //default settings
            $settings=$this->utils->getConfig('default_super_settings');
        }
        return $settings;
    }

    public function commonRemoteExportCSV($conditions, $reportName, $modelName, $funcName, $db=null){
        //create remote job
        $this->load->library(['lib_queue']);
        $this->load->model(['queue_result']);
        $params=[
            'event'=>[
                'name'=>Queue_result::EVENT_EXPORT_CSV,
                'class'=>'ExportEvent',
                'data'=>[
                    'conditions'=>$conditions,
                    'reportName'=>$reportName,
                    'exportingModel'=>$modelName,
                    'exportingFunc'=>$funcName,
                ]
            ]
        ];
        $callerType=Queue_result::CALLER_TYPE_SYSTEM;
        $caller=0;
        $this->utils->debug_log('trigger remote event:export', $reportName, $funcName);
        $token=$this->lib_queue->triggerAsyncRemoteEvent(Queue_result::SYSTEM_UNKNOWN, $params,
            $callerType, $caller);
        $url='/system_management/common_queue/'.$token;
        return ['result_url'=>$url, 'token'=>$token];
    }

    //===super report===================================================

}
