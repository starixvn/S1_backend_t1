<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_iongaming_game_logs_betoptions_column_length_201909021000 extends CI_Migration {
    public function up() {
        //modify column size
        $fields = array(
            'betOptions' => array(
                'type' => 'VARCHAR',
                'constraint' => '1200',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('iongaming_game_logs', $fields);
        $this->dbforge->modify_column('iongaming_idr1_game_logs', $fields);
    }

    public function down() {
        // not able to rollback due to data truncation
    }
}
