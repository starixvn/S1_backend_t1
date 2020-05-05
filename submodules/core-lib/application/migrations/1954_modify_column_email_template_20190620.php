<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_email_template_20190620 extends CI_Migration {

	private $tableName = 'email_template';

    public function up() {
    	if($this->db->table_exists($this->tableName)){
        	$this->db->query("ALTER TABLE $this->tableName CHANGE `mail_content` `mail_content` longtext NULL DEFAULT NULL");
        	$this->db->query("ALTER TABLE $this->tableName CHANGE `mail_content_text` `mail_content_text` longtext NULL DEFAULT NULL");
        }
    }

    public function down() {
    }
}