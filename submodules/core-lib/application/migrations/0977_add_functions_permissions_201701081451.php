<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_functions_permissions_201701081451 extends CI_Migration {

    public function up() {
        // $this->load->model(array('roles'));

        // $this->roles->startTrans();

        // $this->roles->initFunction('pending_request', 'Pending Request', 207, 72, true);

        // $succ = $this->roles->endTransWithSucc();
        // if (!$succ) {
        //     throw new Exception('migrate failed');
        // }
    }

    public function down() {
        // $this->load->model(array('roles'));
        // $this->roles->deleteFunction(207);
    }
}