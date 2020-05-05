<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_white_label_game_logs_201810091945 extends CI_Migration {

    private $tableName = 'whitelabel_game_logs';

    public function up() {
        $fields = array(
            'modifyDate' => array(
                'type' => 'datetime',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('modifyDate', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('modifyDate', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'modifyDate');
        }
    }
}