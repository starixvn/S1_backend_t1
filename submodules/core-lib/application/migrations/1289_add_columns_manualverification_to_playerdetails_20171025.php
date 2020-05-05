<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_manualverification_to_playerdetails_20171025 extends CI_Migration {

    public function up() {
        $fields = array(
            'manual_verification' => array(
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            ),
        );
        $this->dbforge->add_column('playerdetails', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('playerdetails', 'manual_verification');
    }
}

///END OF FILE//////////