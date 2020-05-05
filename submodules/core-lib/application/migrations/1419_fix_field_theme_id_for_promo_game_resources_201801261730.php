<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_fix_field_theme_id_for_promo_game_resources_201801261730 extends CI_Migration {

	private $tableName = 'promo_game_resources';

    public function up() {
        if (!$this->db->field_exists('theme_id', $this->tableName)) {
            $fields = [ 'theme_id' => [ 'type' => 'INT', 'null' => true ] ];

            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if ($this->db->field_exists('theme_id', $this->tableName)) {
            $this->drop_column($this->tableName, 'theme_id');
        }
    }
}

////END OF FILE////
