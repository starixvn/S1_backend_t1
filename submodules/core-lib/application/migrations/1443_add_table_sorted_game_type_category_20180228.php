<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_sorted_game_type_category_20180228 extends CI_Migration {

    private $tableName = 'sorted_game_type_category';

    public function up() {

        $field = array(
            'game_type_order ' => array(
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            ),
        );

        if (!$this->db->field_exists('game_type_order', 'banner_promotion_ss')) {
            $this->dbforge->add_column('banner_promotion_ss', $field);
        }

        $fields = array(
            'id' => array(
                'type' => 'int',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'order_by' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
            ),
            'order_direction' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {

        if ($this->db->field_exists('game_type_order', 'banner_promotion_ss')) {
            $this->dbforge->drop_column('banner_promotion_ss', 'banner_promotion_order');
        }

        $this->dbforge->drop_table($this->tableName);

    }
}
