<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_friendreferralsettings_201804021705 extends CI_Migration {

    private $tableName = 'friendreferralsettings';

    public function up() {

        if ( ! $this->db->field_exists('cashback_rate', $this->tableName)) {
            $fields = array(
                'cashback_rate' => array(
                    'type' => 'double',
                    'null' => true,
                ),
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {

        if ($this->db->field_exists('cashback_rate', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'cashback_rate');
        }

    }

}
