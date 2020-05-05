<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_common_cashback_multiple_range_templates_20181017 extends CI_Migration {

    private $tableName = 'common_cashback_multiple_range_templates';

    public function up() {
        if ($this->db->table_exists($this->tableName)) {
            return;
        }

        $fields = [
            'cb_mr_tpl_id' => ['type' => 'INT', 'auto_increment' => TRUE, 'unsigned' => TRUE],
            'template_name' => ['type' => 'VARCHAR', 'constraint' => '32', 'null' => TRUE],
            'note' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE],
            'active' => ['type' => 'TINYINT', 'constraint' => '4', 'null' => TRUE, 'default' => 0],
            'created_at' => ['type' => 'datetime', 'null' => TRUE],
            'updated_at' => ['type' => 'datetime', 'null' => TRUE]
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('cb_mr_tpl_id', TRUE);

        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
    }
}
