<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_messages_201811301354 extends CI_Migration {

    public function up() {
        $fields = [
            'is_system_message' => [
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => 0,
                'null' => false,
            ],
            'disabled_replay' => [
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => 0,
                'null' => false,
            ],
        ];

        if(!$this->db->field_exists('is_system_message', 'messages')){
            $this->dbforge->add_column('messages', $fields);
        }

        $this->db->query("ALTER TABLE messagesdetails CHANGE message message TEXT");
    }

    public function down() {
        if($this->db->field_exists('is_system_message', 'messages')){
            $this->dbforge->drop_column('messages', 'is_system_message');
        }
        if($this->db->field_exists('disabled_replay', 'messages')){
            $this->dbforge->drop_column('messages', 'disabled_replay');
        }
    }
}
