<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_theme_id_to_promo_game_resources_201801092050 extends CI_Migration {

	private $tableName = 'promo_game_resources';

    public function up() {
        // $fields = array(
        //     'theme_id' => array(
        //         'type' => 'INT',
        //         'null' => true,
        //     ),
        // );

        // $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        // $this->dbforge->drop_column($this->tableName, 'theme_id');
    }
}

////END OF FILE////
