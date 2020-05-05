<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_home_score_in_oneworks_game_result_20190204 extends CI_Migration {

    private $tableName = 'oneworks_game_result';

    public function up() {

        $update_fields = array(
            'home_score' => array(
                'type' => 'INT',
                'null' => true,
            ),
        );
        
        $this->dbforge->modify_column($this->tableName, $update_fields);
        
    }

    public function down() {}
}
