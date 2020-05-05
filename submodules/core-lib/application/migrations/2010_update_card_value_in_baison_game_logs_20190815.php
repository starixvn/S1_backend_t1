<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_card_value_in_baison_game_logs_20190815 extends CI_Migration {

    private $tableName = 'baison_game_logs';

    public function up() {

        $update_fields = array(
            'card_value' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        if($this->db->field_exists('card_value', $this->tableName)) {
            $this->dbforge->modify_column($this->tableName, $update_fields); 
        }
    }

    public function down() {

    }
}
