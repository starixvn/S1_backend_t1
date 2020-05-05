<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_affiliate_commission_20180116 extends CI_Migration {

    public function up() {
        $fields = array(
            'adjusment_notes' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        );

        $this->dbforge->add_column('affiliate_game_platform_earnings', $fields);
        $this->dbforge->add_column('aff_daily_earnings', $fields);
        $this->dbforge->add_column('aff_monthly_earnings', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('affiliate_game_platform_earnings', 'adjusment_notes');
        $this->dbforge->drop_column('aff_daily_earnings', 'adjusment_notes');
        $this->dbforge->drop_column('aff_monthly_earnings', 'adjusment_notes');
    }
}