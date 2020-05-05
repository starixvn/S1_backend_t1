<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_system_features_20190620 extends CI_Migration {

    public function up() {

        $this->load->model(['player_model', 'system_feature']);
        //clear duplicate name first
        $this->system_feature->clearDuplicateName();
        $this->player_model->dropIndex('system_features', 'name');
        //unique index
        $this->player_model->addIndex('system_features','idx_name' , 'name', true);

    }

    public function down() {
    }
}

///END OF FILE//////////