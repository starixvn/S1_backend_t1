<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_remove_foreignkey_in_playertag20180626308 extends CI_Migration {

    public function up() {
        $this->db->query('ALTER TABLE playertag  DROP FOREIGN KEY FK_playertag_ai');
        $this->db->query('ALTER TABLE playertag  DROP FOREIGN KEY FK_playertag_pi');
        $this->db->query('ALTER TABLE playertag  DROP FOREIGN KEY FK_playertag_ti');
    }

    public function down() {
        $this->db->query('ALTER TABLE playertag  ADD CONSTRAINT FK_playertag_ai FOREIGN KEY (taggerId) REFERENCES adminusers (userId) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE playertag  ADD CONSTRAINT FK_playertag_pi FOREIGN KEY (playerId) REFERENCES player (playerId) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE playertag  ADD CONSTRAINT FK_playertag_ti FOREIGN KEY(tagId) REFERENCES tag_original (tagId) ON DELETE CASCADE ON UPDATE CASCADE');
    }
}