<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_affiliate_comm_tier_settings_20181118 extends CI_Migration {

    private $tableName = 'affiliate_comm_tier_settings';
    private $tableName2 = 'aff_monthly_earnings';

    public function up() {

        $field = [
            'active_members' => [
                'type' => 'INT',
                'null' => false,
                'default' => 0
            ]
        ];
        if(!$this->db->field_exists('active_members', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $field);
        }

        $fields = array(
            'negative_net_revenue' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ],
            'total_net_revenue' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ],
            'commission_amount_by_tier' => [
                'type' => 'double',
                'null' => false,
                'default' => 0,
            ],
            'commission_amount_breakdown' => [
                'type' => 'TEXT',
                'null' => true
            ]
        );
        $exist_fields = $this->db->list_fields($this->tableName2);

        foreach ($fields as $key => $value) {
            if (!in_array($key, $exist_fields)) {
                $this->dbforge->add_column($this->tableName2, array($key=>$value));
            }
        }
    }

    public function down() {
        if($this->db->field_exists('active_members', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'active_members');
        }

        $exist_fields = $this->db->list_fields($this->tableName2);

        $drop_columns = ['negative_net_revenue', 'total_net_revenue', 'commission_amount_by_tier', 'commission_amount_breakdown'];

        foreach ($drop_columns as $value) {
            if (in_array($value, $exist_fields)) {
                $this->dbforge->drop_column($this->tableName2, $value);
            }
        }
    }
}
