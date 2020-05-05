<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ggpoker_ew_gamelogs_20181220 extends CI_Migration {

    private $tableName = 'ggpoker_ew_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINt',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'userId' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'nickname' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'gameType' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'ggr' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'rakeOrFee' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'rakedGameCount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'nonRakedGameCount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'profitAndLoss' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'profitAndLossPoker' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'profitAndLossPokerAofJackpot' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'profitAndLossPokerBigHandJackpot' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'profitAndLossPokerFlushJackpot' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'profitAndLossSideGame' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'fishBuffetReward' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'networkGiveaway' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'brandPromotion' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'tournamentOverlay' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'buyInCash' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'buyInGtd' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'buyInTicket' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            //converted fields
            'convertedGgr' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedRakeOrFee' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedRakedGameCount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedNonRakedGameCount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedProfitAndLoss' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedProfitAndLossPoker' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedProfitAndLossPokerAofJackpot' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedProfitAndLossPokerBigHandJackpot' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedProfitAndLossPokerFlushJackpot' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedProfitAndLossSideGame' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedFishBuffetReward' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedNetworkGiveaway' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedBrandPromotion' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedTournamentOverlay' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedBuyInCash' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedBuyInGtd' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'convertedBuyInTicket' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            //default field
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
            'response_result_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('ggpoker_ew_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('ggpoker_ew_game_logs', 'idx_userId', 'userId');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}