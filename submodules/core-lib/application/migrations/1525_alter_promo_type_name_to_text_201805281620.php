<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_promo_type_name_to_text_201805281620 extends CI_Migration {

    private $tableName = 'promotype';

    public function up() {

        $fields = array(
            'promoTypeName' => array(
                'name'=>'promoTypeName',
                'type' => 'TEXT',
                'null' => false,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);

    }

    public function down() {
        $fields = array(
            'promoTypeName' => array(
                'name'=>'promoTypeName',
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }
}
