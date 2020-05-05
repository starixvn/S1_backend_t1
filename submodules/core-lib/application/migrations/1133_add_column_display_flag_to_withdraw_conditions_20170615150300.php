<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_display_flag_to_withdraw_conditions_20170615150300 extends CI_Migration {

    public function up() {
        $fields = array(
            'display_flag' => array(
                'type' => 'smallint',
                'default' => '0',
            ),
        );

        $this->dbforge->add_column('withdraw_conditions', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('withdraw_conditions', 'display_flag');
    }
}