<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_registration_ip_in_player_details_20180716 extends CI_Migration {
    private $tableName = 'playerdetails';

    public function up() {
        //modify column
        $fields = array(
            'registrationIP' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {

    }
}
