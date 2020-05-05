<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_match_detail_on_suncity_game_logs_20190614 extends CI_Migration {

    private $tableName = 'suncity_game_logs';

    public function up()
    {
        # Add column
        $fields = array(
            'match_detail' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            )
        );
        if(!$this->db->field_exists('match_detail', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'match_detail');
    }
}
