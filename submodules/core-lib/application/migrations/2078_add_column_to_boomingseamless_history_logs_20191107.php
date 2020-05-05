<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_boomingseamless_history_logs_20191107 extends CI_Migration {

    private $tableName = 'boomingseamless_history_logs';

    public function up() {

        $fields = array(
            'game_platform_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
        );

        if(! $this->db->field_exists('game_platform_id', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {

        if($this->db->field_exists('game_platform_id', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'game_platform_id');
        }

    }
}