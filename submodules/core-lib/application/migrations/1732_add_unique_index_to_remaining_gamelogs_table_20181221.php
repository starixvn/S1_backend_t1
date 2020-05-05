<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_index_to_remaining_gamelogs_table_20181221 extends CI_Migration {
	public function up() {
        $this->load->model('player_model'); # Any model class will do
        # FADA GAMELOGS 
        $this->player_model->addIndex('fadald_lottery_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

        # FG ENTAPLAY GAMELOGS
        $this->player_model->addIndex('fg_entaplay_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        
        # FG GAMELOGS
        $this->player_model->addIndex('fg_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

        # GD GAMELOGS
        $this->player_model->addIndex('gd_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('gd_game_logs', 'idx_bet_id', 'bet_id',true);
        
        # GENESIS GAMELOGS
        $this->player_model->addIndex('genesism4_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('genesism4_game_logs', 'idx_causality', 'causality',true);

        # GGPOKER GAMELOGS
        $this->player_model->addIndex('ggpoker_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

        # GL GAMELOGS
        $this->player_model->addIndex('gl_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

        # GOLDEN PGSOFT GAMELOGS
        $this->player_model->addIndex('goldenf_pgsoft_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('goldenf_pgsoft_game_logs', 'idx_traceId', 'traceId',true);

        # HABA GAMELOGS
        $this->player_model->addIndex('haba88_game_logs', 'idx_haba88_game_logs_external_uniqueid', 'external_uniqueid',true);

        # IDN GAMELOGS
        $this->player_model->addIndex('idn_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('idn_game_logs', 'idx_transaction_no', 'transaction_no',true);

        # IPM V2 GAMELOGS 
        $this->player_model->addIndex('ipm_v2_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('ipm_v2_game_logs', 'idx_BetId', 'BetId',true);

       	# ISB GAMELOGS 
        $this->player_model->addIndex('isb_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

       	# JUMB GAMELOGS 
        $this->player_model->addIndex('jumb_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('jumb_game_logs', 'idx_seqNo', 'seqNo',true);
        
       	# KENOGAME GAMELOGS 
        $this->player_model->addIndex('kenogame_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('kenogame_game_logs', 'idx_BetId', 'BetId',true);

       	# KYCARD GAMELOGS 
        $this->player_model->addIndex('kycard_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

        # LE GAMING GAMELOGS
        $this->player_model->addIndex('le_gaming_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

        # LOTUS GAMELOGS
        $this->player_model->addIndex('lotus_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        
        # MG DASHUR GAMELOGS
        $this->player_model->addIndex('mg_dashur_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('mg_dashur_game_logs', 'idx_mg_id', 'mg_id',true);

        # MWG GAMELOGS 
        $this->player_model->addIndex('mwg_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('mwg_game_logs', 'idx_gameNum', 'gameNum',true);

        # OG GAMELOGS 
        $this->player_model->addIndex('og_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('og_game_logs', 'idx_ProductID', 'ProductID',true);

        # ONESGAME GAMELOGS
        $this->player_model->addIndex('onesgame_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('onesgame_game_logs', 'idx_tran_id', 'tran_id',true);

        # OPUS GAMELOGS 
        $this->player_model->addIndex('opus_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('opus_game_logs', 'idx_bet_record_id', 'bet_record_id',true);

        # PGSOFT GAMELOGS 
        $this->player_model->addIndex('pgsoft_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('pgsoft_game_logs', 'idx_betid', 'betid',true);

        # QT GAMELOGS
        $this->player_model->addIndex('qt_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('qt_game_logs', 'idx_transId', 'transId',true);

        # RWB GAMELOGS
        $this->player_model->addIndex('rwb_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('rwb_game_logs', 'idx_bet_id', 'bet_id',true);
        
        # SA GAMING GAMELOGS
        $this->player_model->addIndex('sagaming_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('sagaming_game_logs', 'idx_BetID', 'BetID',true);

        # SBTECH GAMELOGS
        $this->player_model->addIndex('sbtech_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

        # SPADE GAMING 
        $this->player_model->addIndex('spadegaming_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('spadegaming_game_logs', 'idx_ticketId', 'ticketId',true);

        # TCG GAMELOGS
        $this->player_model->addIndex('tcg_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        
        # UC GAMELOGS
        $this->player_model->addIndex('uc_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('uc_game_logs', 'idx_TicketId', 'TicketId',true);

        # XYZBLUE GAMELOGS 
        $this->player_model->addIndex('xyzblue_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('xyzblue_game_logs', 'idx_roundid', 'roundid',true);

        # YOPLAY GAMELOGS 
        $this->player_model->addIndex('yoplay_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('yoplay_game_logs', 'idx_billno', 'billno',true);

	}

	public function down() {
	}
}

///END OF FILE//////////