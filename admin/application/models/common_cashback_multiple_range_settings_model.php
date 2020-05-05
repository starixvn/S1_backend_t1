<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 */
class Common_cashback_multiple_range_settings_model extends BaseModel {
    private $tableName = 'common_cashback_multiple_range_settings';

    public function __construct() {
        parent::__construct();
    }

    public function getAllSettingsByTplId($tpl_id){
        $query = $this->db->get_where($this->tableName, [
            'tpl_id' => $tpl_id,
        ]);

        $data = $this->getMultipleRowArray($query);

        if(empty($data)){
            return FALSE;
        }

        return $data;
    }

    public function hasSetting($tpl_id, $type, $type_map_id){
        return $this->getSetting($tpl_id, $type, $type_map_id);
    }

    public function getSetting($tpl_id, $type, $type_map_id){
        $query = $this->db->get_where($this->tableName, [
            'tpl_id' => $tpl_id,
            'type' => $type,
            'type_map_id' => $type_map_id,
        ]);

        $data = $this->getOneRowArray($query);

        if(empty($data)){
            return FALSE;
        }

        return $data;
    }

    public function saveSetting($tpl_id, $type, $type_map_id, $enabled_cashback = 0){
        if(FALSE === $setting = $this->hasSetting($tpl_id, $type, $type_map_id)){
            return $this->createSetting($tpl_id, $type, $type_map_id, $enabled_cashback);
        }

        return $this->updateSetting($tpl_id, $type, $type_map_id, $enabled_cashback);
    }

    public function createSetting($tpl_id, $type, $type_map_id, $enabled_cashback = 0){
        $data = [
            'tpl_id' => $tpl_id,
            'type' => $type,
            'type_map_id' => $type_map_id,
            'enabled_cashback' => ($enabled_cashback) ? 1 : 0,
            'created_at' => $this->utils->getNowForMysql(),
            'updated_at' => $this->utils->getNowForMysql(),
        ];
        $result = $this->db->insert($this->tableName, $data);

        return ($result) ? $this->getSetting($tpl_id, $type, $type_map_id) : FALSE;
    }

    public function updateSetting($tpl_id, $type, $type_map_id, $enabled_cashback = 0){
        $data = [
            'enabled_cashback' => ($enabled_cashback) ? 1 : 0,
            'updated_at' => $this->utils->getNowForMysql()
        ];

        $result = $this->db->update($this->tableName, $data, [
            'tpl_id' => $tpl_id,
            'type' => $type,
            'type_map_id' => $type_map_id,
        ]);

        return ($result) ? TRUE : FALSE;
    }
}
