<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_country_rules_20180813 extends CI_Migration {

    private $tableName = 'country_rules';

    public function up() {
        $affiliate = array(
            'is_affiliate' => array(
                'type' => 'INT',
                'null' => true,
            ));
        if (!$this->db->field_exists('is_affiliate', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $affiliate);
        }

        $agency = array(
            'is_agent' => array(
                'type' => 'INT',
                'null' => true,
            ));
        if (!$this->db->field_exists('is_agent', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $agency);
        }
    }

    public function down() {
        if ($this->db->field_exists('is_affiliate', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'is_affiliate');
        }
        if ($this->db->field_exists('is_agent', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'is_agent');
        }
    }
}
