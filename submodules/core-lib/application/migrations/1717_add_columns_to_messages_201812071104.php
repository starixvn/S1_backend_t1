<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_messages_201812071104 extends CI_Migration {

    public function up() {
        $fields = [
            'player_last_reply_dt' => [
                'type' => 'DATETIME',
                'null' => TRUE,
                'default' => 0,
                'extra' => null
            ],
            'admin_last_reply_id' => [
                'type' => 'INT',
                'unsigned' => TRUE,
                'default' => 0,
                'null' => FALSE,
            ],
            'admin_last_reply_dt' => [
                'type' => 'DATETIME',
                'null' => TRUE,
                'default' => 0,
                'extra' => null
            ],
            'player_unread_count' => [
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => true,
                'default' => 0
            ],
            'admin_unread_count' => [
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => true,
                'default' => 0
            ]
        ];

        if(!$this->db->field_exists('player_last_reply_dt', 'messages')){
            $this->dbforge->add_column('messages', $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('player_last_reply_dt', 'messages')){
            $this->dbforge->drop_column('messages', 'player_last_reply_dt');
        }
        if($this->db->field_exists('admin_last_reply_id', 'messages')){
            $this->dbforge->drop_column('messages', 'admin_last_reply_id');
        }
        if($this->db->field_exists('admin_last_reply_dt', 'messages')){
            $this->dbforge->drop_column('messages', 'admin_last_reply_dt');
        }
        if($this->db->field_exists('player_unread_count', 'messages')){
            $this->dbforge->drop_column('messages', 'player_unread_count');
        }
        if($this->db->field_exists('admin_unread_count', 'messages')){
            $this->dbforge->drop_column('messages', 'admin_unread_count');
        }
    }
}
