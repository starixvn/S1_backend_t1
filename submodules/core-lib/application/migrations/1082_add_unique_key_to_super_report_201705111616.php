<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_key_to_super_report_201705111616 extends CI_Migration {

    public function up() {
        $this->db->truncate('super_summary_report');
        $this->db->truncate('super_player_report');
        $this->db->truncate('super_game_report');
        $this->db->truncate('super_payment_report');
        $this->db->truncate('super_promotion_report');
        $this->db->truncate('super_cashback_report');
        $this->db->query('create unique index unique_super_summary_report on super_summary_report(backoffice_id,report_date)');
        $this->db->query('create unique index unique_super_player_report on super_player_report(backoffice_id,report_date,player_id)');
        $this->db->query('create unique index unique_super_game_report on super_game_report(backoffice_id,report_date_hour,player_id,game_platform_id,game_type_id,game_description_id)');
        $this->db->query('create unique index unique_super_payment_report on super_payment_report(backoffice_id,report_date,player_id,transaction_id)');
        $this->db->query('create unique index unique_super_promotion_report on super_promotion_report(backoffice_id,report_date,player_id,promorule_id)');
        $this->db->query('create unique index unique_super_cashback_report on super_cashback_report(backoffice_id,report_date,player_id,game_platform_id,game_type_id,game_description_id,history_id)');
    }

    public function down() {
        $this->db->query('drop index unique_super_summary_report on super_summary_report');
        $this->db->query('drop index unique_super_player_report on super_player_report');
        $this->db->query('drop index unique_super_game_report on super_game_report');
        $this->db->query('drop index unique_super_payment_report on super_payment_report');
        $this->db->query('drop index unique_super_promotion_report on super_promotion_report');
        $this->db->query('drop index unique_super_cashback_report on super_cashback_report');
    }
}
