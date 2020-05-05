<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_operator_settings_201610211707 extends CI_Migration {

    private $tableName = 'operator_settings';

    public function up() {

        // $this->load->model('operatorglobalsettings');

        // $data = [ 'name' => 'country_rules', 'value' => Operatorglobalsettings::DB_TRUE, 'note' => 'true = use country list, false = ignore country list' ];

        // $this->db->insert($this->tableName, $data);
    }

    public function down() {
        // $this->db->delete($this->tableName, ['name' => 'country_rules'] );
    }
}