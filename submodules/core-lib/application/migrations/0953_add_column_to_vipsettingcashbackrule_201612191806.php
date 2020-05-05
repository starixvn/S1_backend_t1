<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_201612191806 extends CI_Migration {

    private $tableName = 'vipsettingcashbackrule';

    public function up() {

        $fields = array(
            'promo_rule_id ' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'promo_cms_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'promo_rule_id');
        $this->dbforge->drop_column($this->tableName, 'promo_cms_id');
    }
}
