<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_playerdetails_20200110 extends CI_Migration {

    public function up() {
        $this->db->query('ALTER TABLE `playerdetails` CHANGE `lastName` `lastName` VARCHAR(48) NULL DEFAULT NULL');
        $this->db->query('ALTER TABLE `playerdetails` CHANGE `zipcode` `zipcode` VARCHAR(36) NULL DEFAULT NULL');
        $this->db->query('ALTER TABLE `playerdetails` CHANGE `birthplace` `birthplace` VARCHAR(120) NULL DEFAULT NULL');
        $this->db->query('ALTER TABLE `playerdetails` CHANGE `citizenship` `citizenship` VARCHAR(255) NULL DEFAULT NULL');
        $this->db->query('ALTER TABLE `playerdetails` CHANGE `region` `region` VARCHAR(120) NULL DEFAULT NULL');
        $this->db->query('ALTER TABLE `playerdetails` CHANGE `imAccount` `imAccount` VARCHAR(255) NULL DEFAULT NULL');
        $this->db->query('ALTER TABLE `playerdetails` CHANGE `imAccount2` `imAccount2` VARCHAR(255) NULL DEFAULT NULL');
    }

    public function down() {

    }
}