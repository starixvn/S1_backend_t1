<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_livechat_setting_20171006 extends CI_Migration {

    private $tableName = 'livechat_setting';

    public function up() {
        if(!$this->db->table_exists($this->tableName)){
            $fields = array(
                    'id' => array(
                        'type' => 'int',
                        'null' => false,
                        'auto_increment' => TRUE,
                        ),
                    'livechatSettingName' => array(
                        'type' => 'varchar',
                        'constraint' => '100',
                        'null' => true,
                        ),
                    'description' => array(
                        'type' => 'varchar',
                        'constraint' => '100',
                        'null' => true,
                        ),
                    'livechatData' => array(
                        'type' => 'varchar',
                        'constraint' => '100',
                        'null' => true,
                        ),
                    );

            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);

        $this->db->query("insert into `livechat_setting` (`livechatSettingName`, `description`, `livechatData`)
                    values ('maximum_tip', 'Maximum amount can player tip', 1000);");
        }
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}