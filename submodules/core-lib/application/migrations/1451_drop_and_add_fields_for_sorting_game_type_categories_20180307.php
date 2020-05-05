<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_and_add_fields_for_sorting_game_type_categories_20180307 extends CI_Migration {

	private $tableName = 'sorted_game_type_category';

    public function up() {
        if($this->db->field_exists('order_direction', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'order_direction');
        }

        if (!$this->db->field_exists('promotion_header_id', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, [
                'promotion_header_id'      => [ 'type' => 'INT'    , 'null' => false ],
                'order'      => [ 'type' => 'INT'    , 'null' => true ],
            ]);
        }
        if ($this->db->field_exists('game_type_order', 'banner_promotion_ss')) {
            $this->dbforge->drop_column('banner_promotion_ss', 'game_type_order');
        }
    }

    public function down() {

    }
}

////END OF FILE////
