<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_rolename_index_to_roles_2019011926 extends CI_Migration {
    public function up() {
       $this->load->model('player_model');
        # roles table
        $this->player_model->dropIndex('roles', 'idx_roleName');
        $this->player_model->addIndex('roles', 'idx_roleName', 'roleName', true);
      

    }

    public function down() {
    }
}

///END OF FILE//////////