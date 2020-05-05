<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_mg_quickfire_game_logs_20170712 extends CI_Migration {

    public function up() {
       $this->db->query("ALTER TABLE mg_quickfire_game_logs 
CHANGE COLUMN token token VARCHAR(200) CHARACTER SET 'utf8' NULL ,
CHANGE COLUMN seq seq VARCHAR(200) CHARACTER SET 'utf8' NULL ,
CHANGE COLUMN actionid actionid VARCHAR(200) NOT NULL");
       $this->db->query('UPDATE mg_quickfire_game_logs SET actionid = external_uniqueid');
    }

    public function down() {

    }

}