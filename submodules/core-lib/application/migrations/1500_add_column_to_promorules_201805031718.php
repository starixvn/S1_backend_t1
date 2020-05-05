<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201805031718 extends CI_Migration {

    private $tableName = 'promorules';

    public function up() {

        $fields = array(
            'withdrawal_max_limit' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'ignore_withdrawal_max_limit_after_first_deposit' => array(
                'type' => 'TINYINT',
                'null' => true,
            ),
            'always_apply_withdrawal_max_limit_when_first_deposit' => array(
                'type' => 'TINYINT',
                'null' => true,
            ),

        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'withdrawal_max_limit');
        $this->dbforge->drop_column($this->tableName, 'ignore_withdrawal_max_limit_after_first_deposit');
        $this->dbforge->drop_column($this->tableName, 'always_apply_withdrawal_max_limit_when_first_deposit');
    }
}
