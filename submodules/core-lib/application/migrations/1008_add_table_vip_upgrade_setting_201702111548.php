<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_vip_upgrade_setting_201702111548 extends CI_Migration {

    protected $tableName = "vip_upgrade_setting";

    public function up() {

        $this->dbforge->add_field(array(
            'upgrade_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'setting_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'description' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'formula' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'status' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'level_upgrade' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
        ));
        $this->dbforge->add_key('upgrade_id', TRUE);

        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}

///END OF FILE//////////////////