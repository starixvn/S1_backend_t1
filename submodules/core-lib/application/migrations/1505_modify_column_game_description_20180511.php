<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_game_description_20180511 extends CI_Migration {

    private $tableName = 'game_description';

    public function up() {
        $data = $this->db->select('id, progressive')
             ->get($this->tableName);

        if ($data->result_array()) {
            foreach ($data->result_array() as $key => $game) {
                if (empty($game['progressive'])) {

                    $this->db->where('id',$game['id']);
                    $this->db->update($this->tableName,['progressive' => 0]);
                }else{
                    $this->db->where('id',$game['id']);
                    $this->db->update($this->tableName,['progressive' => 1]);
                }
            }
        }

        $fields = array(
            'progressive' => array(
                'name'=>'progressive',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
            ),
            'enabled_on_android' => array(
                'name'=>'enabled_on_android',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
            ),
            'enabled_on_ios' => array(
                'name'=>'enabled_on_ios',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
            ),
            'html_five_enabled' => array(
                'name'=>'html_five_enabled',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
            ),
            'dlc_enabled' => array(
                'name'=>'dlc_enabled',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
            ),
            'offline_enabled' => array(
                'name'=>'offline_enabled',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
            ),
            'enabled_freespin' => array(
                'name'=>'enabled_freespin',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
            ),
            'mobile_enabled' => array(
                'name'=>'mobile_enabled',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
            ),
            'created_on' => array(
                'name'=>'created_on',
                'type' => 'datetime',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);

    }

    public function down() {
        $fields = array(
            'progressive' => array(
                'name'=>'progressive',
                'type' => 'varchar',
                'constraint' => 200,
                'null' => true
            ),
            'enabled_on_android' => array(
                'name'=>'enabled_on_android',
                'type' => 'tinyint',
                'null' => false,
            ),
            'enabled_on_ios' => array(
                'name'=>'enabled_on_ios',
                'type' => 'tinyint',
                'null' => false,
            ),
            'html_five_enabled' => array(
                'name'=>'html_five_enabled',
                'type' => 'tinyint',
                'null' => false,
            ),
            'dlc_enabled' => array(
                'name'=>'dlc_enabled',
                'type' => 'tinyint',
                'null' => false,
                'default' => 1,
            ),
            'offline_enabled' => array(
                'name'=>'offline_enabled',
                'type' => 'tinyint',
                'null' => true,
            ),
            'enabled_freespin' => array(
                'name'=>'enabled_freespin',
                'type' => 'tinyint',
                'null' => false,
                'default' => 1,
            ),
            'mobile_enabled' => array(
                'name'=>'mobile_enabled',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
            ),
            'created_on' => array(
                'name'=>'created_on',
                'type' => 'timestamp',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }
}