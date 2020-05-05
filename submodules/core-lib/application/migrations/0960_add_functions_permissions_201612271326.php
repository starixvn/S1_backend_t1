<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_functions_permissions_201612271326 extends CI_Migration {

    public function up() {
        // $this->load->model(array('roles'));

        // $this->roles->deleteFunction(205);
        // $this->roles->deleteFunction(206);

        // $this->roles->startTrans();

        // $this->roles->initFunction('active_player_report', 'Active Player Report', 205, 25, true);
        // $this->roles->initFunction('manually_upgrade_level', 'Manually Upgrade Level', 206, 72, true);

        // $succ = $this->roles->endTransWithSucc();
        // if (!$succ) {
        //     throw new Exception('migrate failed');
        // }
    }

    public function down() {
        // $this->load->model(array('roles'));
        // $this->roles->deleteFunction(205);
        // $this->roles->deleteFunction(206);
    }
}