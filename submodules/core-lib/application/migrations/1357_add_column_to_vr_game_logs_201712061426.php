<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vr_game_logs_201712061426 extends CI_Migration {

    protected $tableName = "vr_game_logs";

    public function up() {

        $add_cols=array(
            'extra' => array(
                'type' => 'text',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $add_cols);

    }

    public function down() {
        // $this->dbforge->drop_column($this->tableName, 'extra');
    }
}

///END OF FILE//////////////////