<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// class Migration_add_column_to_admin_dashboard_20190307 extends CI_Migration {
class Migration_add_column_browser_user_agent_to_sale_orders_20190422 extends CI_Migration {

    private $tableName = 'sale_orders';

    public function up() {

        $fields = array(
            'browser_user_agent' => array(
                'type' => 'VARCHAR',
				'constraint' => 500 ,
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
		if ($this->db->field_exists('browser_user_agent', $this->tableName)){
	        $this->dbforge->drop_column($this->tableName, 'browser_user_agent');
		}
    }
}
