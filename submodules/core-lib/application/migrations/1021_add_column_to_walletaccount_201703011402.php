<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_walletaccount_201703011402 extends CI_Migration {

    protected $tableName = "walletaccount";

    public function up() {
        //will lock manually operate
        $this->dbforge->add_column($this->tableName, array(
            'lock_manually_opt' => array(
                'type' => 'INT',
                'null' => true,
                'default' => 0,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'lock_manually_opt');
    }

}

///END OF FILE//////////////////