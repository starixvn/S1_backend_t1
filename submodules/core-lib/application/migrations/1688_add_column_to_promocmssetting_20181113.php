<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promocmssetting_20181113 extends CI_Migration {

    private $tableName = 'promocmssetting';

    public function up() {
        $fields = array(
            'allow_claim_promo_in_promo_page' => array(
                'type' => 'tinyint',
                'constraint' => '4',
                'default' => '1',
            ),
            'claim_button_link' => array(
                'type' => 'text',
                'null' => true,
            ),
            'claim_button_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('allow_claim_promo_in_promo_page', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('allow_claim_promo_in_promo_page', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'allow_claim_promo_in_promo_page');
            $this->dbforge->drop_column($this->tableName, 'claim_button_link');
            $this->dbforge->drop_column($this->tableName, 'claim_button_name');
        }
    }
}
