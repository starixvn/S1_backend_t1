<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_to_cmsnews_201708301400 extends CI_Migration
{
    private $tableName = 'cmsnews';

    public function up()
    {
  //       $fields = array(
  //           'is_enable' => array(
  //               'type' => 'INT',
  //               'constraint' => '11',
  //               'default' => 1,
  //               'null' => false
  //           )
  //       );

		// if (!$this->db->field_exists('is_enable', $this->tableName)) {
  //           $this->dbforge->add_column($this->tableName, $fields);
		// }
    }

    public function down()
    {
		// if ($this->db->field_exists('is_enable', $this->tableName)) {
  //           $this->dbforge->drop_column($this->tableName, 'is_enable');
		// }
    }
}