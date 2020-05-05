<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_for_player_tables_20190401 extends CI_Migration {

    public function up()
    {
        $this->player_model->addIndex('player', 'idx_lastLoginTime' , 'lastLoginTime');
    }

    public function down()
    {
    }

}
