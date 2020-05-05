<?php

require_once dirname(__FILE__) . "/BaseEvent.php";

class SyncMDBEvent extends BaseEvent{

    protected $agent_id;
    protected $affiliate_id;
    protected $admin_user_id;
    protected $role_id;
    protected $player_id;
    protected $reg_setting_type;
    protected $lock_unique_name;

    public function extractData($data){
        $this->agent_id=$data['agent_id'];
        $this->affiliate_id=$data['affiliate_id'];
        $this->admin_user_id=$data['admin_user_id'];
        $this->role_id=$data['role_id'];
        $this->player_id=$data['player_id'];
        $this->reg_setting_type=$data['reg_setting_type'];

        $this->agent_lock_unique_name=$data['agent_lock_unique_name'];
        $this->affiliate_lock_unique_name=$data['affiliate_lock_unique_name'];
        $this->admin_user_lock_unique_name=$data['admin_user_lock_unique_name'];
        $this->role_lock_unique_name=$data['role_lock_unique_name'];
        $this->player_lock_unique_name=$data['player_lock_unique_name'];
        $this->reg_setting_lock_unique_name=$data['reg_setting_lock_unique_name'];
    }

    public function getAgentId(){
        return $this->agent_id;
    }

    public function getAffiliateId(){
        return $this->affiliate_id;
    }

    public function getAdminUserId(){
        return $this->admin_user_id;
    }

    public function getRoleId(){
        return $this->role_id;
    }

    public function getPlayerId(){
        return $this->player_id;
    }

    public function getRegSettingType(){
        return $this->reg_setting_type;
    }

    public function getAgentLockUniqueName(){
        return $this->agent_lock_unique_name;
    }

    public function getAffiliateLockUniqueName(){
        return $this->affiliate_lock_unique_name;
    }

    public function getAdminUserLockUniqueName(){
        return $this->admin_user_lock_unique_name;
    }

    public function getRoleLockUniqueName(){
        return $this->role_lock_unique_name;
    }

    public function getPlayerLockUniqueName(){
        return $this->player_lock_unique_name;
    }

    public function getRegSettingLockUniqueName(){
        return $this->reg_setting_lock_unique_name;
    }

}
