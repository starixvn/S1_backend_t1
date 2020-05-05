<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_withdraw_conditions_201712131851 extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('withdraw_conditions', array(
            # Records the trigger amount value
            'trigger_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.0,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column('withdraw_conditions', 'trigger_amount');
    }
}