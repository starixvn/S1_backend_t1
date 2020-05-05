<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_daily_currency_table_20171124 extends CI_Migration {

	public function up() {
		$fields = array(
			'base_currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'target_currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
			'rate' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
		);
		$this->dbforge->add_column('daily_currency', $fields);
		//drop column
		$this->dbforge->drop_column('daily_currency', 'current_rate');
		$this->dbforge->drop_column('daily_currency', 'api_response');
		$this->dbforge->drop_column('daily_currency', 'created_at');
		$this->dbforge->drop_column('daily_currency', 'updated_at');
	}

	public function down() {
		$fields = array(
            'current_rate' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'api_response' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )
        );
        $this->dbforge->add_column('daily_currency', $fields);
        //drop column
		$this->dbforge->drop_column('daily_currency', 'base_currency');
		$this->dbforge->drop_column('daily_currency', 'target_currency');
		$this->dbforge->drop_column('daily_currency', 'rate');
	}
	
}
