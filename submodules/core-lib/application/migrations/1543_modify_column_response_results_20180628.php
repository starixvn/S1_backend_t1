<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_response_results_20180628 extends CI_Migration {

    public function up() {
        $this->db->query('ALTER TABLE `response_results` CHANGE `related_id2` `related_id2` VARCHAR(255) NULL DEFAULT NULL');
        $this->db->query('ALTER TABLE `response_results` CHANGE `related_id3` `related_id3` VARCHAR(255) NULL DEFAULT NULL');
    }

    public function down() {

    }
}