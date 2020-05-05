<?php

require_once dirname(__FILE__) . "/../events/SyncMDBEvent.php";
require_once dirname(__FILE__) . "/AbstractSubscriber.php";

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SyncMDBSubscriber extends AbstractSubscriber implements EventSubscriberInterface{

    public function __construct(){
        parent::__construct();
        $this->utils->info_log('load subscriber class', get_class());
    }

    public static function getSubscribedEvents(){
        return array(
            Queue_result::EVENT_SYNC_MDB => 'syncMDB',
        );
    }

    protected function getSuccessFromRlt($rlt){
        $success=false;
        if(!empty($rlt)){
            foreach ($rlt as $key => $dbRlt) {
                $success=$dbRlt['success'];
                if(!$success){
                    break;
                }
            }
        }
        return $success;
    }

    public function syncMDB(SyncMDBEvent $event){
        // ...
        $this->utils->debug_log('SyncMDBEvent', $event);

        if(!$this->utils->isEnabledMDB()){
            return $this->appendResult($event, ['enabled_mdb'=>false], true, true);
        }

        $this->load->model(['multiple_db_model']);
        $insertOnly=false;
        $success=false;
        $agent_id=$event->getAgentId();
        if(isset($agent_id) && !empty($agent_id) && !empty($event->getAgentLockUniqueName())){
            $success=$this->utils->globalLockSyncAgency($event->getAgentLockUniqueName(), function ()
                    use ($agent_id, $insertOnly, &$rlt) {
                $rlt=$this->multiple_db_model->syncAgencyFromCurrentToOtherMDB($agent_id, $insertOnly);
                return $this->getSuccessFromRlt($rlt);
            });

            $this->utils->debug_log('syncAgencyFromCurrentToOtherMDB :'.$agent_id, $rlt);
            $done=!$success; $failed=!$success;
            $this->appendResult($event, ['syncAgencyFromCurrentToOtherMDB'=>$rlt], $done, $failed);
            if($failed){
                $this->utils->error_log('syncAgencyFromCurrentToOtherMDB failed', $rlt);
                return false;
            }
        }

        $affiliate_id=$event->getAffiliateId();
        if(isset($affiliate_id) && !empty($affiliate_id) && !empty($event->getAffiliateLockUniqueName())){
            $success=$this->utils->globalLockSyncAffiliate($event->getAffiliateLockUniqueName(), function ()
                    use ($affiliate_id, $insertOnly, &$rlt) {
                $rlt=$this->multiple_db_model->syncAffFromCurrentToOtherMDB($affiliate_id, $insertOnly);
                return $this->getSuccessFromRlt($rlt);
            });

            $this->utils->debug_log('syncAffFromCurrentToOtherMDB :'.$affiliate_id, $rlt);
            $done=!$success; $failed=!$success;
            $this->appendResult($event, ['syncAffFromCurrentToOtherMDB'=>$rlt], $done, $failed);
            if($failed){
                $this->utils->error_log('syncAffFromCurrentToOtherMDB failed', $rlt);
                return false;
            }
        }

        $admin_user_id=$event->getAdminUserId();
        if(isset($admin_user_id) && !empty($admin_user_id) && !empty($event->getAdminUserLockUniqueName())){
            $success=$this->utils->globalLockSyncUser($event->getAdminUserLockUniqueName(), function ()
                    use ($admin_user_id, $insertOnly, &$rlt) {
                $rlt=$this->multiple_db_model->syncUserFromCurrentToOtherMDB($admin_user_id, $insertOnly);
                return $this->getSuccessFromRlt($rlt);
            });

            $this->utils->debug_log('syncUserFromCurrentToOtherMDB :'.$admin_user_id, $rlt);
            $done=!$success; $failed=!$success;
            $this->appendResult($event, ['syncUserFromCurrentToOtherMDB'=>$rlt], $done, $failed);
            if($failed){
                $this->utils->error_log('syncUserFromCurrentToOtherMDB failed', $rlt);
                return false;
            }
        }

        $role_id=$event->getRoleId();
        if(isset($role_id) && !empty($role_id) && !empty($event->getRoleLockUniqueName())){
            $success=$this->utils->globalLockSyncRole($event->getRoleLockUniqueName(), function ()
                    use ($role_id, $insertOnly, &$rlt) {
                $rlt=$this->multiple_db_model->syncRoleFromCurrentToOtherMDB($role_id, $insertOnly);
                return $this->getSuccessFromRlt($rlt);
            });

            $this->utils->debug_log('syncRoleFromCurrentToOtherMDB :'.$role_id, $rlt);
            $done=!$success; $failed=!$success;
            $this->appendResult($event, ['syncRoleFromCurrentToOtherMDB'=>$rlt], $done, $failed);
            if($failed){
                $this->utils->error_log('syncRoleFromCurrentToOtherMDB failed', $rlt);
                return false;
            }
        }

        $player_id=$event->getPlayerId();
        if(isset($player_id) && !empty($player_id) && !empty($event->getPlayerLockUniqueName())){
            $success=$this->utils->globalLockSyncPlayer($event->getPlayerLockUniqueName(), function ()
                    use ($player_id, $insertOnly, &$rlt) {
                $rlt=$this->multiple_db_model->syncPlayerFromCurrentToOtherMDB($player_id, $insertOnly);
                return $this->getSuccessFromRlt($rlt);
            });

            $this->utils->debug_log('syncPlayerFromCurrentToOtherMDB :'.$player_id, $rlt);
            $done=!$success; $failed=!$success;
            $this->appendResult($event, ['syncPlayerFromCurrentToOtherMDB'=>$rlt], $done, $failed);
            if($failed){
                $this->utils->error_log('syncPlayerFromCurrentToOtherMDB failed', $rlt);
                return false;
            }
        }

        $reg_setting_type=$event->getRegSettingType();
        if(isset($reg_setting_type) && !empty($reg_setting_type) && !empty($event->getRegSettingLockUniqueName())){
            $success=$this->utils->globalLockSyncRegistrationSettings($event->getRegSettingLockUniqueName(), function ()
                    use ($reg_setting_type, $insertOnly, &$rlt) {
                $rlt=$this->multiple_db_model->syncPlayerRegSettingsFromCurrentToOtherMDB($reg_setting_type, $insertOnly);
                return $this->getSuccessFromRlt($rlt);
            });

            $this->utils->debug_log('syncPlayerRegSettingsFromCurrentToOtherMDB :'.$reg_setting_type, $rlt);
            $done=!$success; $failed=!$success;
            $this->appendResult($event, ['syncPlayerRegSettingsFromCurrentToOtherMDB'=>$rlt], $done, $failed);
            if($failed){
                $this->utils->error_log('syncPlayerRegSettingsFromCurrentToOtherMDB failed', $rlt);
                return false;
            }
        }

        $this->appendResult($event, ['done'=>true], true, $success);

    }

}
