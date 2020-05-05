<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_cmsbanner_20180607 extends CI_Migration {

    private $tableName = 'cmsbanner';

    public function up() {
        $fields = [
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
        ];

        if(!$this->db->field_exists('title', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $fields = [
            'summary' => [
                'type' => 'text',
                'null' => true,
            ],
        ];

        if(!$this->db->field_exists('summary', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $fields = [
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => true,
            ],
        ];

        if(!$this->db->field_exists('link', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $fields = [
            'link_target' => [
                'type' => 'VARCHAR',
                'constraint' => '16',
                'null' => true,
                'default' => '_blank',
            ],
        ];

        if(!$this->db->field_exists('link_target', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $this->db->query('ALTER TABLE `cmsbanner` DROP PRIMARY KEY, ADD PRIMARY KEY (`bannerId`)');
    }

    public function down() {
        if($this->db->field_exists('updated_at', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'updated_at');
        }
        if($this->db->field_exists('created_at', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'created_at');
        }
        if($this->db->field_exists('myfavorites', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'myfavorites');
        }

        $this->db->query('ALTER TABLE `cmsbanner` DROP PRIMARY KEY, ADD PRIMARY KEY (`bannerId`, `createdOn`)');
    }
}