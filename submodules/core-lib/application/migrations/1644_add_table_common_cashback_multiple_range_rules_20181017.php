<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_common_cashback_multiple_range_rules_20181017 extends CI_Migration {

    private $tableName = 'common_cashback_multiple_range_rules';

    public function up() {
        if ($this->db->table_exists($this->tableName)) {
            return;
        }

        $fields = [
            'cb_mr_rule_id' => ['type' => 'INT', 'auto_increment' => TRUE, 'unsigned' => TRUE],
            'tpl_id' => ['type' => 'INT', 'unsigned' => TRUE, 'null' => TRUE],
            'type' => ['type' => 'VARCHAR', 'constraint' => '15', 'null' => TRUE],
            'type_map_id' => ['type' => 'INT', 'unsigned' => TRUE, 'null' => TRUE],
            'min_bet_amount' => ['type' => 'DECIMAL', 'constraint' => '19,6', 'null' => TRUE, 'default' => 0],
            'max_bet_amount' => ['type' => 'DECIMAL', 'constraint' => '19,6', 'null' => TRUE, 'default' => 0],
            'cashback_percentage' => ['type' => 'DECIMAL', 'constraint' => '19,6', 'null' => TRUE, 'default' => 0],
            'max_cashback_amount' => ['type' => 'DECIMAL', 'constraint' => '19,6', 'null' => TRUE, 'default' => 0],
            'created_at' => ['type' => 'datetime', 'null' => TRUE],
            'updated_at' => ['type' => 'datetime', 'null' => TRUE]
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('cb_mr_rule_id', TRUE);

        $this->dbforge->create_table($this->tableName);

        $this->db->query("ALTER TABLE {$this->tableName} ADD INDEX tpl_id (`tpl_id`)");
        $this->db->query("ALTER TABLE {$this->tableName} ADD INDEX tpl_id_type (`tpl_id`, `type`, `type_map_id`)");
    }

    public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
    }
}
