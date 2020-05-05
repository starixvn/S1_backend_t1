<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_btag_column_to_player_20180702 extends CI_Migration {

    private $tableName = 'player';

    public function up() {
        $fields = array(
            'btag' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'btag');
    }
}
