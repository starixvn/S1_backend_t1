<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_duplicate_account_setting_201612061622 extends CI_Migration {

    const LOGIN_IP = 13;

    private $tableName = 'duplicate_account_setting';

    public function up() {

        $this->load->model('duplicate_account_setting');

        $item = $this->duplicate_account_setting->getDuplicateAccountSetting(self::LOGIN_IP);
        if(!$item) {
            $data = array(
                'id' => self::LOGIN_IP,
                'item_id' => self::LOGIN_IP,
                'rate_exact' => 0,
                'rate_similar' => 0,
                'status' => 0,
                'description' => null
            );
            $this->db->insert($this->tableName, $data);
        }
    }

    public function down() {
        $this->db->delete($this->tableName, ['id' => self::LOGIN_IP] );
    }
}