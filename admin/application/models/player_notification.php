<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 */
class Player_notification extends BaseModel{
    const NOTIFICATION_TYPE_INFO = 1;
    const NOTIFICATION_TYPE_SUCCESS = 2;
    const NOTIFICATION_TYPE_WARNING = 3;
    const NOTIFICATION_TYPE_DANGER = 4;

    const SOURCE_TYPE_LAST_LOGIN = 1;
    const SOURCE_TYPE_DEPOSIT = 2;
    const SOURCE_TYPE_WITHDRAWAL = 3;
    const SOURCE_TYPE_VIP_UPGRADE = 4;

    private $tableName = 'player_notification';

    public function __construct(){
        parent::__construct();
    }

    public function hasNotify($player_id){
        return $this->getNotify($player_id);
    }

    public function getNotify($player_id){
        $query = $this->db->get_where($this->tableName, [
            'player_id' => $player_id,
            'is_notify' => 0
        ]);

        $result = $this->getMultipleRowArray($query);

        if(empty($result)){
            return FALSE;
        }

        $list = [];
        foreach($result as $data){
            $list[$data['notify_id']] = $data;
        }

        return $list;
    }

    public function createNotify($player_id, $source_type, $notify_type, $title = NULL, $message = NULL, $url = NULL, $url_target = NULL){
        $data = [
            'player_id' => $player_id,
            'source_type' => $source_type,
            'notify_type' => $notify_type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'url_target' => $url_target,
            'is_notify' => 0,
            'notify_time' => NULL,
            'created_at' => $this->utils->getNowForMysql(),
            'updated_at' => $this->utils->getNowForMysql(),
        ];
        $result = $this->db->insert($this->tableName, $data);

        return ($result) ? $this->getNotify($player_id) : FALSE;
    }

    public function setIsNotify($player_id, $notify_id){
        $where = [
            'player_id' => $player_id
        ];
        if(!empty($notify_id)){
            $where['notify_id'] = $notify_id;
        }

        $data = [
            'is_notify' => 1,
            'notify_time' => $this->utils->getNowForMysql(),
            'updated_at' => $this->utils->getNowForMysql(),
        ];

        $result = $this->db->update($this->tableName, $data, $where);

        return ($result) ? TRUE : FALSE;
    }

    public function clearNotify($player_id, $notify_id = NULL){
        $where = [
            'player_id' => $player_id
        ];
        if(!empty($notify_id)){
            $where['notify_id'] = $notify_id;
        }

        $result = $this->db->delete($this->tableName, $where);

        return ($result) ? TRUE : FALSE;
    }
}
