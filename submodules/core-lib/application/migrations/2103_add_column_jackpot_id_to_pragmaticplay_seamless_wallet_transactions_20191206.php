<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_jackpot_id_to_pragmaticplay_seamless_wallet_transactions_20191206 extends CI_Migration {

    private $tableName = 'pragmaticplay_seamless_wallet_transactions';

    public function up() {

        $fields = array(
            'jackpot_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('jackpot_id', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
             # add index
            $this->player_model->dropIndex($this->tableName, 'idx_uexternal_uniqueid');
            $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
        }
    }

    public function down() {
    }
}