<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_walletaccount_20180124 extends CI_Migration {

    public function up() {
        $fields = array(            
            'bankAccountFullName' => array(
                'type' => 'varchar',
                'null' => true,
                'constraint' => '50'
            ),
            'bankAccountNumber' => array(
                'type' => 'varchar',
                'null' => true,
                'constraint' => '50'
            ),
            'bankName' => array(
                'type' => 'varchar',
                'null' => true,
                'constraint' => '255'
            ),
            'bankAddress' => array(
                'type' => 'varchar',
                'null' => true,
                'constraint' => '1000'
            ),
            'bankCity' => array(
                'type' => 'varchar',
                'null' => true,
                'constraint' => '100'
            ),
            'bankProvince' => array(
                'type' => 'varchar',
                'null' => true,
                'constraint' => '100'
            ),
            'bankBranch' => array(
                'type' => 'varchar',
                'null' => true,
                'constraint' => '100'
            ),
        );
        $this->dbforge->add_column('walletaccount', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('walletaccount', 'bankAccountFullName');
        $this->dbforge->drop_column('walletaccount', 'bankAccountNumber');
        $this->dbforge->drop_column('walletaccount', 'bankName');
        $this->dbforge->drop_column('walletaccount', 'bankAddress');
        $this->dbforge->drop_column('walletaccount', 'bankCity');
        $this->dbforge->drop_column('walletaccount', 'bankProvince');
        $this->dbforge->drop_column('walletaccount', 'bankBranch');
    }
}