<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_index_on_invitedplayerid_playerfriendreferral_20200225 extends CI_Migration
{
    public function up()
    {
        $this->load->model(['player_model']);
        $this->player_model->addIndex('playerfriendreferral', 'idx_playerfriendreferral_invitedPlayerId', 'invitedPlayerId');
    }

    public function down()
    {
    }
}

///END OF FILE//////////
