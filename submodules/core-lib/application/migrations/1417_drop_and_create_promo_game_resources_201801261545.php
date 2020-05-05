<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_and_create_promo_game_resources_201801261545 extends CI_Migration {

	private $tableName = 'promo_game_resources';

    public function up() {
        if ($this->db->field_exists('prize_type', $this->tableName)) {
            // $this->db->drop_dolumn($this->tableName, 'prize_type');
            $this->dbforge->modify_column($this->tableName, [
                'prize_type' => [
                    'name' => 'type' ,
                    'type' => "ENUM('url-image', 'others')"    ,
                    'null' => false ,
                    'default' => 'url-image'
                ]
            ]);
        }

        if (!$this->db->field_exists('theme_id', $this->tableName)) {
            $this->dbforge->add_field([
                'theme_id'      => [ 'type' => 'INT'    , 'null' => true ]
            ]);
        }
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}

////END OF FILE////
