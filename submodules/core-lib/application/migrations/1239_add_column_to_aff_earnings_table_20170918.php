<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_aff_earnings_table_20170918 extends CI_Migration {

	public function up() {
		$fields = array(
			'commission_percentage_breakdown' => array(
				'type' => 'TEXT',
				'null' => true,
			),

		);

		$this->dbforge->add_column('aff_monthly_earnings', $fields);
		$this->dbforge->add_column('aff_daily_earnings', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('aff_monthly_earnings', 'commission_percentage_breakdown');
		$this->dbforge->drop_column('aff_daily_earnings', 'commission_percentage_breakdown');
	}
}
