<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_game_description_201707241628 extends CI_Migration {

    private $tableName = 'game_description';

    public function up() {
        $fields = array(
            'updated_at' => array(
                'type' => 'datetime',
                'null' => true,
            ),
        );

        if (!$this->db->field_exists('updated_at', $this->tableName))
        {
        $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if ($this->db->field_exists('updated_at', $this->tableName))
        {
        $this->dbforge->drop_column($this->tableName, 'updated_at');
        }
    }
}

////END OF FILE////