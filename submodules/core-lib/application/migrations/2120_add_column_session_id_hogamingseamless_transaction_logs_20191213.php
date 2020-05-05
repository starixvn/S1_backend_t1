<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_column_session_id_hogamingseamless_transaction_logs_20191213 extends CI_Migration
{
    private $tableName = 'hogamingseamless_transaction_logs';

    public function up()
    {
        $this->load->model("player_model");
        
        $fields = array(
            'sessionid' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true
            )
        );
        
        if (!$this->db->field_exists('sessionid', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down()
    {
        if ($this->db->field_exists('sessionid', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'sessionid');
        }
    }
}
