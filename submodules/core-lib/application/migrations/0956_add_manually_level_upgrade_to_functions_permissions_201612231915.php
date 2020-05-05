<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_manually_level_upgrade_to_functions_permissions_201612231915 extends CI_Migration {

    public function up() {
        // $this->load->model(array('roles'));

        // $this->roles->startTrans();

        // $this->roles->initFunction('manually_upgrade_level', 'Manually Upgrade Level', 206, 72, true);

        // $succ = $this->roles->endTransWithSucc();
        // if (!$succ) {
        //     throw new Exception('migrate failed');
        // }
    }

    public function down() {
        // $this->load->model(array('roles'));
        // $this->roles->deleteFunction(205);
    }

}