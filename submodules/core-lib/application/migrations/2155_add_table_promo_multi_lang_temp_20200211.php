<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_table_promo_multi_lang_temp_20200211 extends CI_Migration
{
    private $tableName = 'promo_multi_lang_temp';

    public function up() {
        $fields = array(
            'session_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => true,
            ),
            'promo_multi_lang' => array(
                'type' => 'MEDIUMTEXT',
                'null' => true
            )
        );

        if (!$this->db->table_exists($this->tableName)) {
            $this->dbforge->add_field($fields);
            $this->dbforge->create_table($this->tableName);
        }
    }

    public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
    }
}