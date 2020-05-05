<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_payment_settings_to_functions_201612191132 extends CI_Migration {

    // const PARENT_ID = 72;

    # This function defines whether one can manage the orders with wait_API status

    public function up() {
        // $this->load->model(array('roles'));

        // $this->roles->startTrans();

        // $this->roles->initFunction('bank/3rd_payment_list', 'Bank/3rd payment list', 201, self::PARENT_ID, true);
        // $this->roles->initFunction('minimum_withdraw_settings', 'Minimum withdraw settings', 202, self::PARENT_ID, true);
        // $this->roles->initFunction('default_collection_account', 'Default Collection Account', 203, self::PARENT_ID, true);
        // $this->roles->initFunction('withdrawal_workflow', 'Withdrawal workflow', 204, self::PARENT_ID, true);

        // $succ = $this->roles->endTransWithSucc();
        // if (!$succ) {
        //     throw new Exception('migrate failed');
        // }
    }

    public function down() {
        // $this->load->model(array('roles'));
        // $this->roles->deleteFunction(201);
        // $this->roles->deleteFunction(202);
        // $this->roles->deleteFunction(203);
        // $this->roles->deleteFunction(204);

    }

}