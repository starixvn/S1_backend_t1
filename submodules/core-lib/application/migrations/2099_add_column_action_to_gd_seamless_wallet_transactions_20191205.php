<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_action_to_gd_seamless_wallet_transactions_20191205 extends CI_Migration {

    private $tableName = 'gd_seamless_wallet_transactions';

    public function up() {

        $fields = array(
            'action' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true
            ),
        );

        if(!$this->db->field_exists('action', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
             # add index
            $indexPreStr = 'idx_';
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'action', 'action');
        }
    }

    public function down() {
        if($this->db->field_exists('action', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'action');
        }
    }
}