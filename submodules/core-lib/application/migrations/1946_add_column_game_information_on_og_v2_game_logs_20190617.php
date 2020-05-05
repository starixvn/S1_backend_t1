<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_game_information_on_og_v2_game_logs_20190617 extends CI_Migration {

    private $tableName='og_v2_game_logs';

    public function up()
    {
        # Add column
        $fields = array(
            'game_information' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            )
        );
        
        if(!$this->db->field_exists('game_information', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'game_information');
    }
}
