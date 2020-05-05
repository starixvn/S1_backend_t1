<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_fg_game_logs_201712021635 extends CI_Migration {

    protected $tableName = "fg_game_logs";

    public function up() {

        $add_cols=array(
            'related_trans_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $add_cols);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'related_trans_id');
    }
}

///END OF FILE//////////////////