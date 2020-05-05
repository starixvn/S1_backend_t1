<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_column_from_promocmssetting_201721021606 extends CI_Migration {

    public function up() {
    	if( $this->db->field_exists('attemp_request_limit', 'promocmssetting')){    		
        $this->dbforge->drop_column('promocmssetting', 'attemp_request_limit');
        $this->dbforge->drop_column('promocmssetting', 'player_attemp_request');
    	}
    }

    public function down() {
        
    }
}
