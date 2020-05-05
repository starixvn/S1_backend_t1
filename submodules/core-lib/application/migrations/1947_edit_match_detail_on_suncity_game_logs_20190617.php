<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_edit_match_detail_on_suncity_game_logs_20190617 extends CI_Migration {

    private $tableName = 'suncity_game_logs';

    public function up()
    {
        # Add column
        $fields = array(
            'match_detail' => array(
                'type' => 'VARCHAR',
                'constraint' => '2000',
                'null' => true,
            )
        );
        
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'match_detail');
    }
}
