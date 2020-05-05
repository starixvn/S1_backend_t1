<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_remove_message_foreign_key_201902181625 extends CI_Migration {

    public function up() {
        /* check index exists */
        $result_query = $this->db->query("SHOW CREATE TABLE `messages`;");
        $rows = $result_query->row_array();
        if(!empty($rows)){
            $create_script = $rows['Create Table'];
            if(preg_match_all("/[`]FK_messages[`]\s+FOREIGN KEY/", $create_script)){
                $this->db->query("ALTER TABLE `messages` DROP FOREIGN KEY `FK_messages`");
            }
        }
    }

    public function down() {
    }
}