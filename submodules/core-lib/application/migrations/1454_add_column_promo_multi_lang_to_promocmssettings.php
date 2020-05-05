<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_promo_multi_lang_to_promocmssettings extends CI_Migration
{
    private $tableName = 'promocmssetting';

    public function up()
    {
        $fields = array(
            'promo_multi_lang' => array(
                'type' => 'TEXT',
                'null' => true
            )
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'promo_multi_lang');
    }
}
