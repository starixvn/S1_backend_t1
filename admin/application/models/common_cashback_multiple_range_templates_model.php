<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 */
class Common_cashback_multiple_range_templates_model extends BaseModel {
    private $tableName = 'common_cashback_multiple_range_templates';

    public function __construct() {
        parent::__construct();
    }

    public function hasTemplate($tpl_id){
        return $this->getTemplate($tpl_id);
    }

    public function getTemplate($tpl_id){
        $query = $this->db->get_where($this->tableName, [
            'cb_mr_tpl_id' => $tpl_id
        ]);

        $data = $this->getOneRowArray($query);

        if(empty($data)){
            return FALSE;
        }

        return $data;
    }

    public function getActiveTemplate(){
        $this->db->limit(1);
        $query = $this->db->get_where($this->tableName, [
            'active' => 1
        ]);

        $data = $this->getOneRowArray($query);

        if(empty($data)){
            if(!$this->createTemplate('Default')){
                return FALSE;
            }

            $active_template = $this->getLastUpdateTemplate();
            if(empty($active_template)){
                return FALSE;
            }
            $this->setActiveTemplate($active_template['cb_mr_tpl_id']);

            return $active_template;
        }

        return $data;
    }

    public function getLastUpdateTemplate(){
        $this->db->order_by('updated_at', 'desc');
        $query = $this->db->get($this->tableName);

        $data = $this->getOneRowArray($query);

        if(empty($data)){
            return FALSE;
        }

        return $data;
    }

    public function createTemplate($template_name = '', $note = ''){
        $data = [
            'template_name' => $template_name,
            'note' => $note,
            'active' => 0, // default always inactive
            'created_at' => $this->utils->getNowForMysql(),
            'updated_at' => $this->utils->getNowForMysql(),
        ];

        return $this->db->insert($this->tableName, $data);
    }

    public function updateTemplate($tpl_id, $template_name, $note){
        $data = [
            'template_name' => $template_name,
            'note' => $note,
            'updated_at' => $this->utils->getNowForMysql()
        ];

        $result = $this->db->update($this->tableName, $data, ['cb_mr_tpl_id' => $tpl_id]);

        return ($result) ? TRUE : FALSE;
    }

    public function setActiveTemplate($tpl_id){
        $result = $this->db->update($this->tableName, ['active' => 0]);

        if(!$result){
            return FALSE;
        }

        $data = [
            'active' => 1,
            'updated_at' => $this->utils->getNowForMysql()
        ];

        $result = $this->db->update($this->tableName, $data, ['cb_mr_tpl_id' => $tpl_id]);

        return ($result) ? TRUE : FALSE;
    }
}
