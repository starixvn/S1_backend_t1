<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_currency_key_to_reports_201901251559 extends CI_Migration {

    public function up() {
        $fields = array(
           'currency_key' => array(
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null'=> true
            ),
        );

        $this->dbforge->add_column('promotion_report_details', $fields);
        $this->dbforge->add_column('cashback_report_daily', $fields);

    }

    public function down() {
        $this->dbforge->drop_column('promotion_report_details', 'currency_key');
        $this->dbforge->drop_column('cashback_report_daily', 'currency_key');
    }
}
