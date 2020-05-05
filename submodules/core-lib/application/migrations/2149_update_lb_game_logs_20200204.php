<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_lb_game_logs_20200204 extends CI_Migration {
    
	private $tableName = 'lb_game_logs';

    public function up() {

        $fields = array(
            'bet_content' => array(
                'type' => 'TEXT',
                'null' => true,
            )
        );

        if($this->db->field_exists('bet_content', $this->tableName)){
            $this->dbforge->modify_column($this->tableName, $fields);
        }
    }

    public function down() {
    
    }
}