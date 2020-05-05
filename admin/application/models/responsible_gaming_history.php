<?php require_once dirname(__FILE__) . '/base_model.php';

/**
 * Responsible Gaming Hisroty for SelfExclusion
 *
 * General behaviors include
 * * autoApprovedSelfExclusion
 * * autoExpiredSelfExclusion
 *
 * @category Payment Model
 * @version 1.0.1
 * @copyright 2013-2018 TripleOneTech
 */

class Responsible_gaming_history extends BaseModel {

	protected $tableName = "responsible_gaming_history";

    public function __construct(){
        $this->load->model(array('responsible_gaming'));
    }

	public function createInsertData($id, $old_status, $data, $adminId = null){
        $data['responsible_gaming_id'] = $id;
        $data['old_status'] = $old_status;
        $data['admin_id'] = empty($adminId) ? Users::SUPER_ADMIN_ID : $adminId;
        $data['created_at'] = $this->utils->getNowForMysql();
        $this->db->insert($this->tableName,$data);
    }

    public function addSelfExclusionAutoApprovedRecord($id, $old_status, $notes = null){
        $this->utils->debug_log('==============SelfExclusion==Approved=Record====================='.$id);
        $data = [
            'status' => Responsible_gaming::STATUS_APPROVED,
            'remarks' => empty($notes) ? 'self_exclusion.auto.approved' : $notes
        ];
        $this->createInsertData($id, $old_status,$data);
    }

    public function addSelfExclusionExpiredRecord($id, $old_status, $notes = null){
        $this->utils->debug_log('==============SelfExclusion==Expired=Record======================'.$id);
        $data = [
            'status' => Responsible_gaming::STATUS_EXPIRED,
            'remarks' => empty($notes) ? 'self_exclusion.auto.expired' : $notes
        ];
        $this->createInsertData($id, $old_status,$data);
    }

    public function addSelfExclusionAutoCoolingOffRecord($id, $old_status, $notes = null){
        $this->utils->debug_log('==============SelfExclusion==CoolingOff=Record======================'.$id);
        $data = [
            'status' => Responsible_gaming::STATUS_COOLING_OFF,
            'remarks' => empty($notes) ? 'self_exclusion.auto.cooling_off' : $notes
        ];
        $this->createInsertData($id, $old_status,$data);
    }

    public function addCoolOffAutoApprovedRecord($id, $old_status, $notes = null){
        $this->utils->debug_log('=================CoolOff==Approved=Record========================'.$id);
        $data = [
            'status' => Responsible_gaming::STATUS_APPROVED,
            'remarks' => empty($notes) ? 'cool_off.auto.approved' : $notes
        ];
        $this->createInsertData($id, $old_status,$data);
    }

    public function addCoolOffAutoExpiredRecord($id, $old_status, $notes = null){
        $this->utils->debug_log('=================CoolOff==Expired=Record========================='.$id);
        $data = [
            'status' => Responsible_gaming::STATUS_EXPIRED,
            'remarks' => empty($notes) ? 'cool_off.auto.expired' : $notes
        ];
        $this->createInsertData($id, $old_status,$data);
    }

    public function addDepositLimitsAutoApprovedRecord($id, $old_status, $notes = null){
        $this->utils->debug_log('=================DepositLimits==Approved=Record========================'.$id);
        $data = [
            'status' => Responsible_gaming::STATUS_APPROVED,
            'remarks' => empty($notes) ? 'deposit_limits.auto.approved' : $notes
        ];
        $this->createInsertData($id, $old_status,$data);
    }

    public function addDepositLimitsAutoExpiredRecord($id, $old_status, $notes = null){
        $this->utils->debug_log('=================DepositLimits==Expired=Record========================='.$id);
        $data = [
            'status' => Responsible_gaming::STATUS_EXPIRED,
            'remarks' => empty($notes) ? 'deposit_limits.auto.expired' : $notes
        ];
        $this->createInsertData($id, $old_status,$data);
    }

    public function addDepositLimitsAutoSubscribeRecord($id, $old_status){
        $this->utils->debug_log('=================DepositLimits==Add Auto Subscribe=Record========================='.$id);
        $data = [
            'status' => $old_status,
            'remarks' => 'deposit_limits.auto.subscribe'
        ];
        $this->createInsertData($id, $old_status,$data);
    }

    public function addWageringLimitsAutoSubscribeRecord($id, $old_status){
        $this->utils->debug_log('=================WageringLimits==Add Auto Subscribe=Record========================='.$id);
        $data = [
            'status' => $old_status,
            'remarks' => 'wagering_limits.auto.subscribe'
        ];
        $this->createInsertData($id, $old_status,$data);
    }

    public function updateDepositLimitsAmountRecord($id, $origin_status, $origin_amount, $new_amount){
        $this->utils->debug_log('=================DepositLimits==Update Current Amount=Record========================='.$id);
        $data = [
            'status' => $origin_status,
            'remarks' => '[' . lang('adjustmenthistory.title.beforeadjustment') . '] ' . lang('pay.amt') . ':' . $origin_amount . ' [' . lang('adjustmenthistory.title.afteradjustment') . '] ' . lang('pay.amt') . ':' . $new_amount
        ];
        $this->createInsertData($id, $origin_status,$data);
    }

    public function updateWageringLimitsAmountRecord($id, $origin_status, $origin_amount, $new_amount){
        $this->utils->debug_log('=================WageringLimits==Update Current Amount=Record========================='.$id);
        $data = [
            'status' => $origin_status,
            'remarks' => '[' . lang('adjustmenthistory.title.beforeadjustment') . '] ' . lang('pay.amt') . ':' . $origin_amount . ' [' . lang('adjustmenthistory.title.afteradjustment') . '] ' . lang('pay.amt') . ':' . $new_amount
        ];
        $this->createInsertData($id, $origin_status,$data);
    }

    public function addWageringLimitsAutoApprovedRecord($id, $old_status, $notes = null){
        $this->utils->debug_log('=================WageringLimits==Approved=Record========================'.$id);
        $data = [
            'status' => Responsible_gaming::STATUS_APPROVED,
            'remarks' => empty($notes) ? 'wagering_limits.auto.approved' : $notes
        ];
        $this->createInsertData($id, $old_status,$data);
    }

    public function addWageringLimitsAutoExpiredRecord($id, $old_status, $notes = null){
        $this->utils->debug_log('=================WageringLimits==Expired=Record========================='.$id);
        $data = [
            'status' => Responsible_gaming::STATUS_EXPIRED,
            'remarks' => empty($notes) ? 'wagering_limits.auto.expired' : $notes
        ];
        $this->createInsertData($id, $old_status,$data);
    }

    public function addResponsibleGamingManualCanceledRecord($id, $old_status, $notes = null){
        $this->utils->debug_log('============ResponsibleGaming==Manual Canceled=Record===================='.$id);
        $adminId = $this->authentication->getUserId();
        $data = [
            'status' => Responsible_gaming::STATUS_CANCELLED,
            'remarks' => empty($notes) ? 'responsible_gaming.manual.canceled' : $notes
        ];
        $this->createInsertData($id, $old_status, $data, $adminId);
    }

    public function addResponsibleGamingAutoCanceledRecord($id, $old_status, $notes = null){
        $this->utils->debug_log('============ResponsibleGaming==Auto Canceled=Record===================='.$id);
        $adminId = $this->authentication->getUserId();
        $data = [
            'status' => Responsible_gaming::STATUS_CANCELLED,
            'remarks' => empty($notes) ? 'responsible_gaming.auto.canceled' : $notes
        ];
        $this->createInsertData($id, $old_status, $data, $adminId);
    }

}