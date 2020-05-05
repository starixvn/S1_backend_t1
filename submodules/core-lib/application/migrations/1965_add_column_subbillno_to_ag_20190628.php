<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_subbillno_to_ag_20190628 extends CI_Migration {

    public function up() {

        $agin_game_logs_fields = array(
            'subbillno' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('agmg_game_logs', $agin_game_logs_fields);
        $this->dbforge->add_column('ag_game_logs', $agin_game_logs_fields);
        $this->dbforge->add_column('aghg_game_logs', $agin_game_logs_fields);
        $this->dbforge->add_column('agpt_game_logs', $agin_game_logs_fields);
        $this->dbforge->add_column('agshaba_game_logs', $agin_game_logs_fields);
    }

    public function down() {
        $this->dbforge->drop_column('agmg_game_logs', 'subbillno');
        $this->dbforge->drop_column('ag_game_logs', 'subbillno');
        $this->dbforge->drop_column('aghg_game_logs', 'subbillno');
        $this->dbforge->drop_column('agpt_game_logs', 'subbillno');
        $this->dbforge->drop_column('agshaba_game_logs', 'subbillno');
    }
}