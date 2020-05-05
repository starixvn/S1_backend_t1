<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promotype_20190521 extends CI_Migration {

    private $tableName = 'promotype';

    public function up(){
        # Add column
        $fields = array(
            'promotypeOrder' => array(
                'type' => 'INT'
            )
        );


        if(!$this->db->field_exists('promotypeOrder', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields, 'promotypeId');
            $this->db->set('promotypeOrder', 'promotypeId', false);
            $this->db->update($this->tableName);
        }
    }

    public function down(){
        if($this->db->field_exists('promotypeOrder', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'promotypeOrder', 'promotypeId');
        }
    }
}
