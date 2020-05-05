<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_columns_name_in_fg_game_logs_201712041613 extends CI_Migration {

    protected $tableName = "fg_game_logs";

    public function up() {
        $fields = array(
            'related_trans_id' => array(
                    'name' => 'extra',
                    'type' => 'text',
                    'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
        // $fields = array(
        //     'extra' => array(
        //             'name' => 'related_trans_id',
        //             'type' => 'text',
        //             'null' => true,
        //     ),
        // );
        // $this->dbforge->modify_column($this->tableName, $fields);
    }
}

///END OF FILE//////////////////