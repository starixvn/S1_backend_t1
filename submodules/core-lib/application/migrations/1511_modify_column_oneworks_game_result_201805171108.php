<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_oneworks_game_result_201805171108 extends CI_Migration {

    private $tableName = 'oneworks_game_result';

    public function up() {
        $fields = array(
            'home_score' => array(
                'type' => 'SMALLINT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);

    }

    public function down() {
    }
}