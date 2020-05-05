<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_and_add_tfgaming_game_logs_column_20191211 extends CI_Migration {
    
    private $tableName = 'tfgaming_esports_game_logs';
    
    public function up() {

        //modify and add column
        $fields = array(
            'malay_odds' => array(
              'type' => 'DOUBLE',
              'null' => true,
            ),
            'euro_odds' => array(
              'type' => 'DOUBLE',
              'null' => true,
            ),
        );

        if(!$this->db->field_exists('malay_odds', $this->tableName) && !$this->db->field_exists('euro_odds', $this->tableName)){
            if($this->db->field_exists('tickets', $this->tableName)) {
                $this->dbforge->modify_column($this->tableName, array(
                    'tickets' => array(
                        'type' => 'TEXT',
                        'null' => true
                    )
                ));
            }
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('malay_odds', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'malay_odds');
        }
        if($this->db->field_exists('euro_odds', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'euro_odds');
        }
    }
}
