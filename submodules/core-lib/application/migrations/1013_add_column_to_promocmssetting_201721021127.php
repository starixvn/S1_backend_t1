<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promocmssetting_201721021127 extends CI_Migration {

    public function up() {
        $fields = array(
            'attemp_request_limit' => array(
                'type' => 'INT',
                'constraint' => '3',
                'null' => true,
            ),
            'player_attemp_request' => array(
                'type' => 'TEXT',
                'null' => true,
            )
        );

        $this->dbforge->add_column('promocmssetting', $fields);
    }

    public function down() {
        if( $this->db->field_exists('attemp_request_limit', 'promocmssetting') ){
        $this->dbforge->drop_column('promocmssetting', 'attemp_request_limit');
        $this->dbforge->drop_column('promocmssetting', 'player_attemp_request');
        }
    }
}
