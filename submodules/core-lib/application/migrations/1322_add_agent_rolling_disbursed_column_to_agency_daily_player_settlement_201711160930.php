<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_agent_rolling_disbursed_column_to_agency_daily_player_settlement_201711160930 extends CI_Migration {

    public function up() {
        # This field will be set to 1 if player's agent (including all up-line agents) have received their rolling commission
        # for the current daily settlement record. Rolling commission that settled daily goes directly to agent's wallet.
        $fields = array(
            'agent_rolling_paid' => array(
                'type' => 'tinyint',
                'null' => false,
                'default' => 0
            ),
        );

        $this->dbforge->add_column('agency_daily_player_settlement', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('agency_daily_player_settlement', 'agent_rolling_paid');
    }

}
