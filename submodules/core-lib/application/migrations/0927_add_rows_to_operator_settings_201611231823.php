<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_operator_settings_201611231823 extends CI_Migration {

    private $tableName = 'operator_settings';

    public function up() {

        // $this->load->model('operatorglobalsettings');

        // $data = array(
        //     array(
        //         'name' => 'country_rules_mode ',
        //         'value' => 'allow_all',
        //         'note' => 'Allow all or deny all'
        //     ),
        //     array(
        //         'name' => 'block_page_url',
        //         'value' => '/block-page.html',
        //         'note' => 'Blocking page'
        //     )
        // );

        // $this->db->insert_batch($this->tableName, $data);
    }

    public function down() {
        // $this->db->delete($this->tableName, ['name' => 'country_rules_mode'] );
        // $this->db->delete($this->tableName, ['name' => 'block_page_url'] );
    }

}