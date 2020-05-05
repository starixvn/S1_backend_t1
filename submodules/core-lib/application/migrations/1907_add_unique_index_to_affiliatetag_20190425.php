<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_index_to_affiliatetag_20190425 extends CI_Migration {
    private $tableName = 'affiliatetag';

    public function up(){
        $this->load->model(['player_model']);

        // clean up duplicate data
        $this->db->query("DELETE u1 FROM affiliatetag u1, affiliatetag u2 WHERE u1.affiliateId = u2.affiliateId AND u1.tagId = u2.tagId AND u1.affiliateTagId < u2.affiliateTagId");

        // create index
        $this->player_model->addIndex($this->tableName, 'idx_affid_and_tagid', 'affiliateId, tagId', TRUE);
    }

    public function down(){
        $this->load->model(['player_model']);
        $this->player_model->dropIndex($this->tableName, 'idx_affid_and_tagid');
    }
}