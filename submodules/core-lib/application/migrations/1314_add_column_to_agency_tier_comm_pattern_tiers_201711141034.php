<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_tier_comm_pattern_tiers_201711141034 extends CI_Migration {

	public function up() {
        // Add new table 'agency_tier_comm_pattern_tiers'
		$fields = array(
			'rolling_comm' => array(
				'type' => 'DOUBLE',
				'null' => false,
                'default' => 0
			),
		);

        $this->dbforge->add_column('agency_tier_comm_pattern_tiers', $fields);
    }

	public function down() {
		$this->dbforge->drop_column('agency_tier_comm_pattern_tiers', 'rolling_comm');
	}

}
