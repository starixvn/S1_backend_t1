<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_country_rules_to_functions_permissions_201610131814 extends CI_Migration {

    public function up() {
        // $this->load->model(array('roles'));

        // $this->roles->startTrans();

        // $this->roles->initFunction('country_rules', 'Country Rules', 169, 1, true);

        // $succ = $this->roles->endTransWithSucc();
        // if (!$succ) {
        //     throw new Exception('migrate failed');
        // }
    }

    public function down() {
        // $this->load->model(array('roles'));
        // $this->roles->deleteFunction(169);
    }

}