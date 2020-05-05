<?php
require_once dirname(__FILE__) . '/base_testing.php';

/**
 * This class aims to ensure the two commands runs correctly:
 *
 * * generate_agency_daily_player_settlement
 * * generate_agency_settlement_wl
 *
 * The following manual configuration is needed before running this unit test
 *
 * * Enable AG API
 * * Have an agent, a sub-agent, and one player [player-id] under this sub-agent
 * * Setup agent with the following:
 *     - AG_API - EBR: Rev Share = 80, Rolling Comm Basis = Bets except tie bets, Rolling = 2.00
 *     - AG_API - BR2: Rev Share = 70, Rolling Comm Basis = Bets except tie bets, Rolling = 2.00
 * * Setup sub-agent with the following:
 *     - AG_API - EBR: Rev Share = 70, Rolling = 1.50
 *     - AG_API - BR2: Rev Share = 60, Rolling = 1.50
 * * Setup player with the following:
 *     - AG_API - EBR: Rolling = 1.20
 *     - AG_API - BR2: Rolling = 1.00
 * * Fees setting:
 *     - Admin fee: 10%
 *     - Platform Fee: EBR: 10%; BR2: 20%; Allow negative platform fee: OFF
 *     - Other fees 100%
 *     - Deposit Fee: 1%
 *     - Withdraw Fee: 0.4%
 * * Insert the following data with player's user id:
 *     - Game Log: INSERT INTO game_logs (game_platform_id,game_type_id,player_id,player_username,result_amount,bet_amount,after_balance,start_at,end_at,external_uniqueid, trans_amount,flag)
 *                 VALUES (2,1,[player-id],"unit_test",-15000,15000,0,CONCAT(CURDATE(), " 00:48:27"),CONCAT(CURDATE(), " 00:48:27"),3996027655, 15000, 1), (2,1,[player-id],"unit_test",-15000,15000,0,CONCAT(CURDATE(), " 00:48:32"),CONCAT(CURDATE(), " 00:48:32"),3996028423, 15000, 1), (2,1,[player-id],"unit_test",-15000,15000,0,CONCAT(CURDATE(), " 00:48:36"),CONCAT(CURDATE(), " 00:48:36"),3996029202, 15000, 1), (2,1,[player-id],"unit_test",-15000,15000,0,CONCAT(CURDATE(), " 00:48:40"),CONCAT(CURDATE(), " 00:48:40"),3996029896, 15000, 1), (2,1,[player-id],"unit_test",-15000,15000,0,CONCAT(CURDATE(), " 00:48:46"),CONCAT(CURDATE(), " 00:48:46"),3996030780, 15000, 1), (2,1,[player-id],"unit_test",-15000,15000,0,CONCAT(CURDATE(), " 00:48:51"),CONCAT(CURDATE(), " 00:48:51"),3996031493, 15000, 1), (2,1,[player-id],"unit_test",10000,15000,0,CONCAT(CURDATE(), " 00:48:55"),CONCAT(CURDATE(), " 00:48:55"),3996032293, 15000, 1), (2,1,[player-id],"unit_test",87500,15000,0,CONCAT(CURDATE(), " 00:49:03"),CONCAT(CURDATE(), " 00:49:03"),3996033742, 15000, 1), (2,2,[player-id],"unit_test",-2500,15000,0,CONCAT(CURDATE(), " 00:49:13"),CONCAT(CURDATE(), " 00:49:13"),3996035315, 15000, 1), (2,2,[player-id],"unit_test",-12500,15000,0,CONCAT(CURDATE(), " 00:49:19"),CONCAT(CURDATE(), " 00:49:19"),3996036361, 15000, 1)
 *     - Bonus: INSERT INTO transactions (amount,transaction_type,from_id,from_type,to_id,to_type,note,created_at,flag,trans_date,status) VALUES (1001,9,1,1,[player-id],2,"unit_test_bonus",CONCAT(CURDATE(), " 01:36:17"),1,CURDATE(), 1)
 *     - Transaction Fee: INSERT INTO transactions (amount,transaction_type,from_id,from_type,to_id,to_type,note,status,created_at,changed_balance) VALUES (100, 3, 1, 1, [player-id], 2, 'unit_test_trans_fee', 1, CONCAT(CURDATE(), " 01:48:27"), 100), (200, 3, 1, 1, [player-id], 2, 'unit_test_trans_fee', 1, CONCAT(CURDATE(), " 02:48:27"), 200), (300, 3, 1, 1, [player-id], 2, 'unit_test_trans_fee', 1, CONCAT(CURDATE(), " 03:48:27"), 300), (300, 3, 1, 1, [player-id], 2, 'unit_test_trans_fee', 1, CONCAT(CURDATE(), " 04:48:27"), 300), (100, 1, 1, 1, [player-id], 2, 'unit_test_deposit_fee', 1, CONCAT(CURDATE(), " 05:48:27"), 100), (200, 1, 1, 1, [player-id], 2, 'unit_test_deposit_fee', 1, CONCAT(CURDATE(), " 06:48:27"), 200), (300, 2, 1, 1, [player-id], 2, 'unit_test_withdraw_fee', 1, CONCAT(CURDATE(), " 07:48:27"), 300), (300, 2, 1, 1, [player-id], 2, 'unit_test_withdraw_fee', 1, CONCAT(CURDATE(), " 08:48:27"), 300)
 *     - Cashback: INSERT INTO transactions (amount,transaction_type,from_id,from_type,to_id,to_type,note,created_at,flag,trans_date,status) VALUES (1002,13,1,1,[player-id],2,"unit_test_cashback",CONCAT(CURDATE() + INTERVAL 1 DAY, " 01:05:45"),1,CURDATE(), 1)
 *
 * * Explained:
 *     - Game Log: 10 bets, each with amount 15k; total_bet = 150k, play total lose 7.5k
 *         + AG_API - EBR: 8 bets, total bet = 120k (80% of total), player total win 7.5k, rolling = 1440, platform fee = 0
 *         + AG_API - BR2: 2 bets, total bet = 30k  (20% of total), player total lose 15k, rolling = 300, platform fee = 15000 x 20% = 3000
 *     - Bonus: Player received 1 time bonus amount 1001
 *     - Cashback: Player received 1 time cashback amount 1002
 *     - Transaction Fee: Player incurred 4 transaction fees, total (100+200+300+300) = 900
 *
 * * Expected: Please refer to formula excel
 */
class Testing_agency_wl_settlement extends BaseTesting {
    private $agent_id = 22; # Replace with your chosen agent's ID
    private $sub_agent_id = 23; # Replace with your chosen sub-agent's ID
    private $player_id = 56962; # Replace with your player under your chosen sub-agent

    public function init() {
        $this->load->model(array('game_logs', 'agency_model'));
        $this->load->library('agency_library');
    }

    ## all tests route through this function
    public function testTarget($methodName) {
        $this->init();
        $this->$methodName();
    }

    ## Invokes all tests defined below. A test function's name should begin with 'test'
    public function testAll() {
        $this->init();
        $classMethods = get_class_methods($this);
        $excludeMethods = array('test', 'testTarget', 'testAll');
        foreach ($classMethods as $method) {
            if (strpos($method, 'test') !== 0 || in_array($method, $excludeMethods)) {
                continue;
            }

            $this->$method();
        }
    }

    ## Actual tests
    private function test_generate_agency_settlement_wl() {
        # This has to be run before generating agency settlement
        $this->test_generate_agency_daily_player_settlement();
        $this->agency_library->create_settlement_by_win_loss($this->sub_agent_id);
        $this->agency_library->create_settlement_by_win_loss($this->agent_id);
    }

    private function test_generate_agency_daily_player_settlement() {
        $this->agency_library->generate_agency_daily_player_settlement();

        $max_run = 5;
        while($this->agency_library->generate_agency_daily_agent_settlement() && $max_run > 0) {
            sleep(1);
            $max_run--;
        }
    }


}

///end of file/////////////