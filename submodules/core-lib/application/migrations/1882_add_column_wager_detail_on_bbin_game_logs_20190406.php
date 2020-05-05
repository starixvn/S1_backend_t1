<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_wager_detail_on_bbin_game_logs_20190406 extends CI_Migration {

    private $tableName = 'bbin_game_logs';

    public function up() {

        $fields = array(
            'wager_detail' => array(
                'type' => 'text',            
                'null' => true,
            ),
        );
        
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'wager_detail');
    }
}
