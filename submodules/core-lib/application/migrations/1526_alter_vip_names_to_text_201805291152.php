<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_vip_names_to_text_201805291152 extends CI_Migration {

    private $tableName1 = 'vipsetting';
    private $tableName2 = 'vipsettingcashbackrule';

    public function up() {

        $table1_fields = array(
            'groupName' => array(
                'name'=>'groupName',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName1, $table1_fields);

        $table2_fields = array(
            'vipLevelName' => array(
                'name'=>'vipLevelName',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName2, $table2_fields);

    }

    public function down() {

        $table1_fields = array(
            'groupName' => array(
                'name'=>'groupName',
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName1, $table1_fields);

        $table2_fields = array(
            'vipLevelName' => array(
                'name'=>'vipLevelName',
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName2, $table2_fields);
        
    }
}
