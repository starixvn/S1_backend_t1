<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_currency_201809071244 extends CI_Migration {

    private $tableName = 'currency';

    public function up() {
        $fields = array(
            'currencyShortName' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('currencyShortName', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('currencyShortName', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'currencyShortName');
        }
    }
}