<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_field_content_option_to_banner_promotion_ss_20180212 extends CI_Migration {

    private $tableName = 'banner_promotion_ss';

	public function up() {
        $fields = array(
            'content_option' => array('type' => 'INT')
        );
        $this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
        $this->dbforge->drop_column($this->tableName, 'content_option');
	}
}
