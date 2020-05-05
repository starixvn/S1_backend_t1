<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_common_tokens_20191029 extends CI_Migration {

    private $tableName = 'common_tokens';

    public function up() {

        $fields = array(
            'sign_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
        );

        if(! $this->db->field_exists('sign_key', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {

        if($this->db->field_exists('sign_key', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'sign_key');
        }

    }
}