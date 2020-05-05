<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_content_of_banners_promotions_cms_20180403 extends CI_Migration {

	private $tableName = 'banner_promotion_ss';

	public function up() {
		$fields = array(
            'content' => array(
				'type' => 'MEDIUMTEXT',
				'null' => true,
			),
        );

        $this->dbforge->modify_column($this->tableName, $fields);
    }

	public function down() {
		$fields = array(
            'content' => array(
				'type' => 'TEXT',
				'null' => true,
			),
        );

        $this->dbforge->modify_column($this->tableName, $fields);
	}	

}

///END OF FILE//////////