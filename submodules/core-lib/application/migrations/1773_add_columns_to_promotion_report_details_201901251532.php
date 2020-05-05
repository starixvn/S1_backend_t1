<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_promotion_report_details_201901251532 extends CI_Migration {

    private $tableName='promotion_report_details';

    public function up() {
        $fields = array(
           'player_promo_id' => array(
                'type' => 'INT',
                'null'=> true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'player_promo_id');
    }
}
