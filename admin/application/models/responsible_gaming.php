<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Responsible_gaming extends BaseModel {
	#RESPONSIBLE GAMING TYPE
	const SELF_EXCLUSION_TEMPORARY = 1;
	const SELF_EXCLUSION_PERMANENT = 2;
	const COOLING_OFF = 3;
	const TIMER_REMINDERS = 4;
	const SESSION_LIMITS = 5;
	const DEPOSIT_LIMITS = 6;
	const LOSS_LIMITS = 7;
    const WAGERING_LIMITS = 8;

	#PERIOD TYPE
	const PERIOD_TYPE_DAY = 1;
	const PERIOD_TYPE_WEEK = 2;
	const PERIOD_TYPE_MONTH = 3;
	const PERIOD_TYPE_PERMANENT = 4;
	const PERIOD_TYPE_MINUTES = 5;

	#STATUS
	const STATUS_REQUEST = 1;
	const STATUS_APPROVED = 2;
	const STATUS_DECLINED = 3;
	const STATUS_CANCELLED = 4;
	const STATUS_EXPIRED = 5;
	const STATUS_PLAYER_DEACTIVATED = 6;
    const STATUS_COOLING_OFF = 7;

    const ONLY_HAVE_ONE_REQUEST = 1;

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "responsible_gaming";

    /**
     * @param array $data
     * @param null   $empty
     *
     * @return bool
     */
	public function insertData($data, $empty=null, $db=null) {
        if(empty($db)){
            $db=$this->db;
        }
		$result = $db->insert($this->tableName, $data);
		if(FALSE === $result){
		    return FALSE;
        }

        return $db->insert_id();
	}

	public function getData($playerId=null,$type=null,$status=null,$date_from=null,$date_to=null,$period_type=null) {
		if($playerId){
			$this->db->where('player_id', $playerId);
		}
		if($type){
		    if(is_array($type)){
		        $this->db->where_in('type', $type);
            }else{
                $this->db->where('type', $type);
            }
		}
		if($status){
            if(is_array($status)){
                $this->db->where_in('status', $status);
            }else{
                $this->db->where('status', $status);
            }
		}
		if($date_from){
			$this->db->where('date_from <= ', $date_from);
		}
		if($date_to){
			$this->db->where('date_to <= ', $date_to);
		}
		if($period_type){
			$this->db->where('period_type', $period_type);
		}
		$this->db->from($this->tableName);
		return $this->runMultipleRow();
	}

	public function updateResponsibleGamingData($data, $status='') {
	    //for action player check,if admin_id not in array ,set null
        //if(!array_key_exists("admin_id",$data)){
        //    $data['admin_id']=null;
        //}

		$this->db->set($data);
		$this->db->where("player_id", $data['player_id']);
		$this->db->where("type", $data['type']);
        $this->db->where('status !=', $status);
		return $this->runAnyUpdate($this->tableName);
	}

    public function updateResponsibleGamingRecord($rpgId, $playerId, $data, $adminId = null) {

	    $data['admin_id'] = empty($adminId) ? Users::SUPER_ADMIN_ID : $adminId;
	    $data['updated_at'] = $this->utils->getNowForMysql();
        $this->utils->debug_log("updateResponsibleGamingRecord:-----------------------------> ",$data);

	    $this->db->set($data);
        $this->db->where("player_id", $playerId);
        if(is_array($rpgId)){
            $this->db->where_in('id', $rpgId);
            $this->utils->debug_log('the rpgId-------------------------------------------->', $rpgId);
        }else{
            $this->db->where('id', $rpgId);
        }

        $result = $this->runAnyUpdate($this->tableName);
        return ($result) ? TRUE : FALSE ;
    }

    public function updateDepositLimitsCurrentAmount($rpgId, $playerId, $amount){
        $data = [
            'amount' => $amount
        ];
        return $this->updateResponsibleGamingRecord($rpgId, $playerId, $data);
    }

    public function updateWageringLimitsCurrentAmount($rpgId, $playerId, $amount){
        $data = [
            'amount' => $amount
        ];
        return $this->updateResponsibleGamingRecord($rpgId, $playerId, $data);
    }

    public function setSelfExclusionToApprove($rpgId, $playerId, $notes = null){
        $data = [
            'status' => self::STATUS_APPROVED,
            'remarks' => empty($notes) ? 'self_exclusion.auto.approved' : $notes
        ];
        return $this->updateResponsibleGamingRecord($rpgId, $playerId, $data);
    }

    public function setSelfExclusionToExpire($rpgId, $playerId, $notes = null){
        $data = [
            'status' => self::STATUS_EXPIRED,
            'remarks' => empty($notes) ? 'self_exclusion.auto.expired' : $notes
        ];
        return $this->updateResponsibleGamingRecord($rpgId, $playerId, $data);
    }

    public function setSelfExclusionToCoolingOff($rpgId, $playerId, $notes = null){
        $data = [
            'status' => self::STATUS_COOLING_OFF,
            'remarks' => empty($notes) ? 'self_exclusion.auto.cooling_off' : $notes
        ];
        return $this->updateResponsibleGamingRecord($rpgId, $playerId, $data);
    }

    public function setCoolOffToApprove($rpgId, $playerId, $notes = null){
        $data = [
            'status' => self::STATUS_APPROVED,
            'remarks' => empty($notes) ? 'cool_off.auto.approved' : $notes
        ];
        return $this->updateResponsibleGamingRecord($rpgId, $playerId, $data);
    }

    public function setCoolOffToExpire($rpgId, $playerId, $notes = null){
        $data = [
            'status' => self::STATUS_EXPIRED,
            'remarks' => empty($notes) ? 'cool_off.auto.expired' : $notes
        ];
        return $this->updateResponsibleGamingRecord($rpgId, $playerId, $data);
    }

    public function setDepositLimitsToApprove($rpgId, $playerId, $notes = null){
        $data = [
            'status' => self::STATUS_APPROVED,
            'remarks' => empty($notes) ? 'deposit_limits.auto.approved' : $notes
        ];
        return $this->updateResponsibleGamingRecord($rpgId, $playerId, $data);
    }

    public function setDepositLimitsToExpire($rpgId, $playerId, $notes = null){
        $data = [
            'status' => self::STATUS_EXPIRED,
            'remarks' => empty($notes) ? 'deposit_limits.auto.expired' : $notes
        ];
        return $this->updateResponsibleGamingRecord($rpgId, $playerId, $data);
    }

    public function setWageringLimitsToApprove($rpgId, $playerId, $notes = null){
        $data = [
                'status' => self::STATUS_APPROVED,
                'remarks' => empty($notes) ? 'wagering_limits.auto.approved' : $notes
        ];
        return $this->updateResponsibleGamingRecord($rpgId, $playerId, $data);
    }

    public function setWageringLimitsToExpire($rpgId, $playerId, $notes = null){
        $data = [
                'status' => self::STATUS_EXPIRED,
                'remarks' => empty($notes) ? 'wagering_limits.auto.expired' : $notes
        ];
        return $this->updateResponsibleGamingRecord($rpgId, $playerId, $data);
    }

    public function setPlayerResponsibleGamingToCancel($rpgId, $playerId, $notes = null){
        $adminId = $this->authentication->getUserId();
        $data = [
            'status' => self::STATUS_CANCELLED,
            'remarks' => empty($notes) ? 'responsible_gaming.manual.canceled' : $notes
        ];
        return $this->updateResponsibleGamingRecord($rpgId, $playerId, $data, $adminId);
    }

    public function setPlayerResponsibleGamingToAutoCancel($rpgId, $playerId, $notes = null){
        $adminId = $this->authentication->getUserId();
        $data = [
            'status' => self::STATUS_CANCELLED,
            'remarks' => empty($notes) ? 'responsible_gaming.auto.canceled' : $notes
        ];
        return $this->updateResponsibleGamingRecord($rpgId, $playerId, $data, $adminId);
    }

    public function getResponsibleGData($playerId,$where,$values){
        $this->db->select('rg.id , rg.player_id, rg.type, rg.time_by_min, rg.time_by_hour, rg.period_cnt, rg.period_type, rg.date_from, rg.date_to, rgh.status, rg.game_provider, rgh.remarks, rg.created_at, rg.updated_at, rgh.created_at as "updated at", rg.admin_id, rg.amount, p.username as "user", a.username as "action player"');
        $this->db->from('responsible_gaming rg');
        $this->db->join('responsible_gaming_history rgh','rg.id = rgh.responsible_gaming_id','left');
        $this->db->join('player p', 'rg.player_id = p.playerId', 'left');
        $this->db->join('adminusers a', 'rg.admin_id = a.userId', 'left');
        $this->db->where('rg.player_id', $playerId);
        $this->db->where($where['0'], $values['0']);
        $this->db->where($where['1'], $values['1']);
        $query = $this->db->get();
        $result = $query->result_array();
        $result = json_decode(json_encode($result),true);
        return $result;
    }

    public function chkexist($playerId, $selftype) {
        $arr['player_id']=$playerId;
        $arr['type']=$selftype;
        $qry = $this->db->get_where($this->tableName, $arr);
        if ($this->getOneRow($qry) == null) {
            return false;
        } else {
            return true;
        }

    }

    public function saveTimeReminder($uid,$min){

    }

    public function getAvailableResponsibleGamingData(){
        $result = call_user_func_array([$this, 'getData'], func_get_args());
        if(empty($result)){
            return [];
        }

        $return_result = [];
        foreach ($result as $data) {
            if(in_array($data->status, [self::STATUS_DECLINED, self::STATUS_CANCELLED, self::STATUS_EXPIRED, self::STATUS_PLAYER_DEACTIVATED])){
                continue;
            }

            $return_result[] = $data;
        }

        return $return_result;
    }

    public function getActiveResponsibleGamingData(){
        $result = call_user_func_array([$this, 'getAvailableResponsibleGamingData'], func_get_args());
        if(empty($result)){
            return [];
        }

        $current = time();
        $return_result = [];
        $this->load->model(array('operatorglobalsettings'));
        foreach ($result as $data) {
            switch($data->type){
                case Responsible_gaming::SELF_EXCLUSION_PERMANENT:
                    if($current >= strtotime($data->date_from)){
                        $return_result[] = $data;
                    }
                    break;
                case Responsible_gaming::SELF_EXCLUSION_TEMPORARY:
                    if(!(int)$this->operatorglobalsettings->getSettingIntValue('automatic_reopen_temp_self_exclusion_account',0)) {
                        if($current >= strtotime($data->date_from)){
                            $return_result[] = $data;
                        }
                    }else{
                        if($current >= strtotime($data->date_from) && $current <= strtotime($data->date_to)){
                            $return_result[] = $data;
                        }else if($data->type == Responsible_gaming::SELF_EXCLUSION_TEMPORARY && ($current >= strtotime($data->date_to) && $current <= strtotime($data->cooling_off_to))){
                            $return_result[] = $data;
                        }
                    }
                    break;
                case Responsible_gaming::COOLING_OFF:
                case Responsible_gaming::WAGERING_LIMITS:
                    if($current >= strtotime($data->date_from) && $current <= strtotime($data->date_to)){
                        $return_result[] = $data;
                    }else if($data->type == Responsible_gaming::SELF_EXCLUSION_TEMPORARY && ($current >= strtotime($data->date_to) && $current <= strtotime($data->cooling_off_to))){
                        $return_result[] = $data;
                    }
                    break;
            }
        }

        return $return_result;
    }

    public function chkSelfExclusion($playerId){
        //COOLING_OFF belong to  self exclusion when show player status
        $result = FALSE;
        $responsible_gaming = $this->getActiveResponsibleGamingData($playerId,[Responsible_gaming::SELF_EXCLUSION_PERMANENT,Responsible_gaming::SELF_EXCLUSION_TEMPORARY]);
        $allow_status = [Responsible_gaming::STATUS_APPROVED,Responsible_gaming::STATUS_COOLING_OFF];
        if(isset($responsible_gaming['0'])){
            $responsible_gaming = $responsible_gaming['0'];
            $result = in_array($responsible_gaming->status,$allow_status);
        }
        return $result;
    }

    public function chkCoolOff($playerId){
        $result = FALSE;
        $responsible_gaming = $this->getActiveResponsibleGamingData($playerId, Responsible_gaming::COOLING_OFF);
        if(isset($responsible_gaming['0'])){
            $responsible_gaming = $responsible_gaming['0'];
            $result = ($responsible_gaming->status == self::STATUS_APPROVED);
        }
        return $result;
    }

    public function getAllTypeReport($player_id=null){
            $sql="select player_id,(datediff(date_to,date_from )) as days,type,period_cnt,period_type,`status`,amount,created_at, IFNULL(updated_at, created_at) as updated_at
from responsible_gaming  where `status`<>4 order by player_id,type";

        $qry = $this->db->query($sql);
        //$last = $this->db->last_query();

        if ($qry && $qry->num_rows() > 0) {
            $rs =  $qry->row_array();
        }else{
            $rs =null;
            return "N/A" ;
        }
        $arr = $qry->result_array();

        //define array format
        foreach($arr as $rs){
            $rfArr[$rs['player_id']][1]="N/A";
            $rfArr[$rs['player_id']][2]="N/A";
            $rfArr[$rs['player_id']][3]="N/A";
            $rfArr[$rs['player_id']][4]="N/A";
            $rfArr[$rs['player_id']][5]="N/A";
            $rfArr[$rs['player_id']][6]="N/A";
            $rfArr[$rs['player_id']][7]="N/A";
        }
        foreach($arr as $rs){


            if($rs['status']==4){
                $rfArr[$rs['player_id']][$rs['type']]="N/A";
            }else{
                switch($rs['type']){
                    case 1:
                    case 2:
                        if($rs['type']=="2"){
                            //forever
                            $rfArr[$rs['player_id']][1]=lang('Self Exclusion Permanent');
                        }else{
                            if(is_null($rs['days'])){
                                $rfArr[$rs['player_id']][1]="N/A";
                            }else{
                                $rfArr[$rs['player_id']][1]=$rs['days'].lang('day');;
                            }

                        }

                        break;
                    case 3:
                        //cool off /time out

                        $day =$rs['period_cnt'];
                        $rfArr[$rs['player_id']][3]= $day." ".lang('day');
                        break;
                    case 4:
                        $min =$rs['period_cnt'];
                        $rfArr[$rs['player_id']][4]= $min." ".lang('min');
                        break;
                    case 5:
                        $min =$rs['period_cnt'];
                        $rfArr[$rs['player_id']][5]= $min." ".lang('min');
                        break;
                    case 6:
                        $rfArr[$rs['player_id']][6] = $rs['amount'];
                        break;
                    case 7:

                        $rfArr[$rs['player_id']][7] = $rs['amount'];
                        break;
                }

            }

        }//end foreach

        return $rfArr;


    }

    public function getSFcounts(){
        $date = $this->getTodayForMysql();
        $sql="select count(id) as couns from responsible_gaming where ( (`type` = 1 AND `status` = 2) OR (`type` = 1 AND `status` = 7) OR (`type` = 2 AND `status` = 2) OR (`type` = 3 AND `status` = 2) ) and date(created_at) ='".$date."'";
        $qry = $this->db->query($sql);
        if($qry->num_rows()>0){
            $arr = $qry->result_array();
            return $arr[0]['couns'];

        }else{
            return 0;
        }

    }

    public function type_to_string($type) {
        $mapping = [
            self::SELF_EXCLUSION_TEMPORARY    => lang('Self Exclusion, Temporary') ,
            self::SELF_EXCLUSION_PERMANENT    => lang('Self Exclusion, Permanent') ,
            self::COOLING_OFF         => lang('Time Out') ,
            self::TIMER_REMINDERS     => lang('Time Reminders') ,
            self::SESSION_LIMITS      => lang('Session limits') ,
            self::DEPOSIT_LIMITS      => lang('Deposit Limits') ,
            self::LOSS_LIMITS         => lang('Loss limits') ,
            self::WAGERING_LIMITS     => lang('Wagering Limits') ,
        ];

        $to_string = isset($mapping[$type]) ? $mapping[$type] : lang('N/A');
        return $to_string;
    }

    public function status_to_string($status) {
        $mapping = [
            self::STATUS_REQUEST    => lang('Pending for Approval') ,
            self::STATUS_APPROVED   => lang('rg.approved') ,
            self::STATUS_DECLINED   => lang('DECLINED') ,
            self::STATUS_CANCELLED  => lang('rg.cancelled') ,
            self::STATUS_EXPIRED    => lang('EXPIRED') ,
            self::STATUS_PLAYER_DEACTIVATED => lang('rg.deactivated') ,
        ];

        $to_string = isset($mapping[$status]) ? $mapping[$status] : lang('N/A');
        return $to_string;
    }

    public static function getTempPeriodList() {
        $CI =& get_instance();
        $today = new DateTime();

        $responsible_gaming_self_exclusion_period_list = $CI->utils->getConfig('responsible_gaming_self_exclusion_period_list');

        $tempPeriodList = [];
        foreach($responsible_gaming_self_exclusion_period_list as $period){
            $tempPeriodList[$CI->utils->getDatetimeDiffByDays($today, $period['interval_spec'])] = $period['value'] . lang($period['text_suffix']);
        }

        return $tempPeriodList;
    }

    /**
     * overview : process player reponsible gaming
     *
     * detail : @return array data
     *
     * @param array $data
     *
     */
    public function processPlayerResponsibleGaming($player_id) {
        $respGamingType = array(self::SELF_EXCLUSION_TEMPORARY => "temp_self_exclusion",
            self::SELF_EXCLUSION_PERMANENT => "permanent_self_exclusion",
            self::COOLING_OFF => "cool_off",
            self::TIMER_REMINDERS => "timer_reminders",
            self::SESSION_LIMITS => "session_limits",
            self::DEPOSIT_LIMITS => "deposit_limits",
            self::LOSS_LIMITS => "loss_limits",
            self::WAGERING_LIMITS => "wagering_limits",
        );
        $data = $this->getAvailableResponsibleGamingData($player_id);
        $respData = array();
        $currentDateTime = $this->utils->getNowForMysql();
        $depositLimitsExist = FALSE;
        $wageringLimitsExist = FALSE;

        if (!empty($data)) {
            foreach ($data as $key) {
                if($key->type == self::DEPOSIT_LIMITS && !$depositLimitsExist){
                    if($key->status == self::STATUS_APPROVED && ($key->date_from < $currentDateTime) && ($key->date_to > $currentDateTime)){
                        $depositLimitsExist = TRUE;
                        $respData[$respGamingType[$key->type]] = $key;
                        continue;
                    }
                }else if($key->type == self::WAGERING_LIMITS && !$wageringLimitsExist){
                    if($key->status == self::STATUS_APPROVED && ($key->date_from < $currentDateTime) && ($key->date_to > $currentDateTime)){
                        $wageringLimitsExist = TRUE;
                        $respData[$respGamingType[$key->type]] = $key;
                        continue;
                    }
                }else if(!in_array($key->type,[self::DEPOSIT_LIMITS,self::WAGERING_LIMITS])){
                    $respData[$respGamingType[$key->type]] = $key;
                }
            }
        }

        return $respData;
    }
}