<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 */
class Common_cashback_multiple_range_rules_model extends BaseModel {
    private $tableName = 'common_cashback_multiple_range_rules';

    public function __construct() {
        parent::__construct();
    }

    public function getAllRulesByTplId($tpl_id){
        $query = $this->db->get_where($this->tableName, [
            'tpl_id' => $tpl_id,
        ]);

        $data = $this->getMultipleRowArray($query);

        if(empty($data)){
            return FALSE;
        }

        return $data;
    }

    public function hasRuleById($rule_id){
        return $this->getRuleById($rule_id);
    }

    public function getRuleById($rule_id){
        $query = $this->db->get_where($this->tableName, [
            'cb_mr_rule_id' => $rule_id
        ]);

        $data = $this->getOneRowArray($query);

        if(empty($data)){
            return FALSE;
        }

        return $data;
    }

    public function createRule($tpl_id, $type, $type_map_id, $min_bet_amount, $max_bet_amount, $cashback_percentage, $max_cashback_amount){
        $data = [
            'tpl_id' => $tpl_id,
            'type' => $type,
            'type_map_id' => $type_map_id,
            'min_bet_amount' => $min_bet_amount,
            'max_bet_amount' => $max_bet_amount,
            'cashback_percentage' => $cashback_percentage,
            'max_cashback_amount' => $max_cashback_amount,
            'created_at' => $this->utils->getNowForMysql(),
            'updated_at' => $this->utils->getNowForMysql(),
        ];
        $result = $this->db->insert($this->tableName, $data);

        return ($result) ? TRUE : FALSE;
    }

    public function updateRuleById($rule_id, $min_bet_amount, $max_bet_amount, $cashback_percentage, $max_cashback_amount){
        $data = [
            'min_bet_amount' => $min_bet_amount,
            'max_bet_amount' => $max_bet_amount,
            'cashback_percentage' => $cashback_percentage,
            'max_cashback_amount' => $max_cashback_amount,
            'updated_at' => $this->utils->getNowForMysql()
        ];

        $result = $this->db->update($this->tableName, $data, [
            'cb_mr_rule_id' => $rule_id
        ]);

        return ($result) ? TRUE : FALSE;
    }

    public function deleteRuleById($rule_id){
        $result = $this->db->delete($this->tableName, [
            'cb_mr_rule_id' => $rule_id
        ]);

        return ($result) ? TRUE : FALSE;
    }
}
