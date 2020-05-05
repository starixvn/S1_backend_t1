<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_vivo_gaming_idr1_game_logs_column_type_201910281830 extends CI_Migration {
    public function up() {
        //modify column data type
        $fields = array(
            'accounting_transaction_id' => array(
              'type' => 'VARCHAR',
              'constraint' => '50',
              'null' => true,
            ),
            'transaction_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'transaction_type_id' => array(
              'type' => 'INT',
              'constraint' => '11',
              'null' => true,
            ),
            'table_round_id' => array(
              'type' => 'VARCHAR',
              'constraint' => '50',
              'null' => true,
            ),
            'table_id' => array(
              'type' => 'VARCHAR',
              'constraint' => '50',
              'null' => true,
            ),
            'game_id' => array(
              'type' => 'VARCHAR',
              'constraint' => '50',
              'null' => true,
            ),
        );
        $this->dbforge->modify_column('vivo_gaming_idr1_game_logs', $fields);
    }

    public function down() {
        // not able to rollback due to data truncation
    }
}
