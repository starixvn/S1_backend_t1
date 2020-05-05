<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_remove_index_kingrich_summary_reports_201811150053 extends CI_Migration {

    private $tableName = 'kingrich_summary_reports';

    public function up() {
        $this->player_model->dropIndex($this->tableName, 'idx_settlement_date');
    }

    public function down() {
        
    }
}