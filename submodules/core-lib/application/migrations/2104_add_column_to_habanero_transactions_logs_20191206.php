<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_habanero_transactions_logs_20191206 extends CI_Migration {

    private $tableName = 'habanero_transactions';

    public function up() {

        $fields = array(
            'balance_after' => array(
                'type' => 'double',
                'null' => true,
            ),
        );
        
        if(!$this->db->field_exists('balance_after', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('balance_after', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'balance_after');
        }
    }
}