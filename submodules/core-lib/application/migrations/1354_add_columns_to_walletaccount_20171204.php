<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_walletaccount_20171204 extends CI_Migration {

    public function up() {
        $fields = array(
            'withdrawal_declined_category_id' => array(
                'type' => 'int',
                'null' => true,
                'constraint' => '11'
            )
        );
        $this->dbforge->add_column('walletaccount', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('walletaccount', 'withdrawal_declined_category_id');
    }
}