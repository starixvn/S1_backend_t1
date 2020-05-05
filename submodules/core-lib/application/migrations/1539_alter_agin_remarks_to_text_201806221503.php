<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_agin_remarks_to_text_201806221503 extends CI_Migration {

    private $tableName = 'agin_game_logs';

    public function up() {

        $table1_fields = array(
            'remark' => array(
                'name'=>'remark',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $table1_fields);

    }

    public function down() {
        
    }
}
