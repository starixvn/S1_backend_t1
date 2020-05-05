<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_after_balance_to_rwb_game_transactions_20181213 extends CI_Migration {

    public function up() {
        $fields = [
            'after_balance' => [
                'type' => 'DOUBLE',
                'null' => true,
            ]
        ];

        if(!$this->db->field_exists('after_balance', 'rwb_game_transactions')){
            $this->dbforge->add_column('rwb_game_transactions', $fields);
        }

    }

    public function down() {
        if($this->db->field_exists('after_balance', 'rwb_game_transactions')){
            $this->dbforge->drop_column('rwb_game_transactions', 'after_balance');
        }
    }
}
