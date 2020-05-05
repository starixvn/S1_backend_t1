<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_logs_20181023 extends CI_Migration{
    private $tableName = 'logs';

    public function up(){
        if(!$this->db->field_exists('params', $this->tableName)){
            $this->dbforge->add_column($this->tableName, [
                'params' => [
                    'type' => 'JSON',
                    'null' => TRUE,
                ],
            ]);
        }
    }

    public function down(){
        if($this->db->field_exists('params', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'params');
        }
    }
}
