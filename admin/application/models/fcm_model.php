<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

class fcm_model extends BaseModel 
{
    const T1T_API_VPNAPP = 1;
    const T1T_API_SECUREAPP = 2;

    protected $tableName = "firebase_cloud_messaging_token";

    public function addFcmData($postData)
    {
        $this->db->insert($this->tableName, $postData);
    }

    public function updateFcmData($postData, $playerId)
    {
        $this->db->where('player_id', $playerId);
        $this->db->update($this->tableName, $postData);
    }

    public function checkExiseID($app_type, $playerId=null)
    {
        if ($playerId) {
            $condition['player_id'] = $playerId;
        }
        $condition['app_type'] = $app_type;

        $qry = $this->db->get_where($this->tableName, $condition);
        return $qry->num_rows();
    }
}
