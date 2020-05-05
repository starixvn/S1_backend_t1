<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_otp_secret_to_tables_201903291313 extends CI_Migration {

    public function up() {
        $fields = array(
            'otp_secret' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('agency_agents', $fields);
        $this->dbforge->add_column('affiliates', $fields);
        $this->dbforge->add_column('adminusers', $fields);
        $this->dbforge->add_column('player', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('agency_agents', 'otp_secret');
        $this->dbforge->drop_column('affiliates', 'otp_secret');
        $this->dbforge->drop_column('adminusers', 'otp_secret');
        $this->dbforge->drop_column('player', 'otp_secret');
    }
}
