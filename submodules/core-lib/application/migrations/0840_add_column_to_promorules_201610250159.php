<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201610250159 extends CI_Migration {

    private $tableName = "promorules";

    public function up() {
        $fields = array(
            'trigger_wallets' => array(
                'type' => 'VARCHAR',
                'null' => TRUE,
                'constraint'=> 200,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'trigger_wallets');
    }
}
