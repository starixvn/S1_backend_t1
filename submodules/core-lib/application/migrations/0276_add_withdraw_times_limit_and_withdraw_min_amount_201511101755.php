<?php

class Migration_add_withdraw_times_limit_and_withdraw_min_amount_201511101755 extends CI_Migration {
    public function up() {
            $this->dbforge->add_column('vipsettingcashbackrule', array(
                        'withdraw_times_limit' => array(
                                        'type' => 'INT',
                                        'null' => true,
                                     'default' => 3,
                        ),
            ));
            // $this->dbforge->add_column('operator_settings', array(
            //     'withdraw_min_amount' => array(
            //     'type' => 'DOUBLE',
            //     'null' => true,
            //     'default' => 200.00,
            //     ),
            // ));
    }

    public function down() {
        $this->dbforge->drop_column('vipsettingcashbackrule', 'withdraw_times_limit');
        // $this->dbforge->drop_column('operator_settings', 'withdraw_min_amount');
    }
}