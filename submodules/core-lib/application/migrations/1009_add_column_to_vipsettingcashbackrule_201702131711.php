<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_201702131711 extends CI_Migration {

    public function up() {
        $fields = array(
            'period_up_down_2' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'vip_upgrade_id' => array(
                'type' => 'INT',
                'null' => true,
            )
        );

        $this->dbforge->add_column('vipsettingcashbackrule', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('vipsettingcashbackrule', 'period_up_down_2');
        $this->dbforge->drop_column('vipsettingcashbackrule', 'vip_upgrade_id');
    }
}
