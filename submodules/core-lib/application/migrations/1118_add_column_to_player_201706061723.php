<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_201706061723 extends CI_Migration {

    private $tableName = 'player';

    public function up() {
        $fields = array(
            'choose_cashback_or_promotion' => array(
                'type' => 'INT',
                'null' => false,
                'default' => -1,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'choose_cashback_or_promotion');
    }
}