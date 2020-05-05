<?php
require_once dirname(__FILE__) . '/game_api_common_ag.php';

/**
 * Defines general behavior of game API classes.
 *
 * General behaviors include:
 * * Getting Agent Handicap by using a player name.
 * * Creating Player
 * * Query player balances
 * * Deposit to game
 * * withdraw from game
 * * transfer credits
 *
 * The functions implemented by child class:
 * * Populating game form parameters
 * * Handling callbacks
 *
 *
 *
 * @see Redirect redirect to game page
 *
 * @category Game_platform
 * @version 1.8.10 
 * @copyright 2013-2019 TripleOneTech
 */

class Game_api_agmg extends Game_api_common_ag {

    public function getPlatformCode()
    {
        return AGMG_API;
    }

    public function __construct()
    {
        parent::__construct();

        $defaultIgnorePlatform=['AGIN', 'AG', 'DSP', 'AGHH', 'IPM', 'BBIN', 'SABAH', 'HG', 'PT',
            'OG', 'UGS', 'HB', 'XTD', 'PNG', 'NYX', 'ENDO', 'BG', 'HUNTER', 'AGTEX',
            'XIN', 'YOPLAY', 'TTG'];
        $this->ignore_platformtypes = $this->getSystemInfo('ignore_platformtypes', $defaultIgnorePlatform);
    }

    public function getAvailableRows($dataResult)
    {
        $this->CI->load->model('agmg_game_logs');
        return $this->CI->agmg_game_logs->getAvailableRows($dataResult);
    }

    public function insertBatchToGameLogs($availableResult)
    {
        $this->CI->load->model('agmg_game_logs');
        if ($this->merge_game_logs) {
            $syncRecords = $this->syncRecords($availableResult);
            return $this->CI->agmg_game_logs->insertBatchGameLogsReturnIds($syncRecords);
        } else {
            # add betdetails first before saving the data
            // foreach ($availableResult as $key => $value) {
            //     $availableResult[$key]['extra'] = $this->createGameBetDetialsJson($value);
            // }
            $records = $this->createBetDetailsAndCheckIfComboBets($availableResult);

            return $this->CI->agmg_game_logs->insertBatchGameLogsReturnIds($records);
        }

    }

    public function syncRecords($gameRecords) {
        $this->CI->load->model('agmg_game_logs');
        $round_ids = array();
        $externalUniqueIds = array();
        $map_external_to_round_id = array();
        $map = array();
        $count = 0;
        $mergeResult = array();

        if (!empty($gameRecords)) {
            foreach ($gameRecords as $row) {
                if (empty($row['gamecode'])) {
                    array_push($mergeResult, $row);
                    continue;
                }

                $externalId=isset($map_external_to_round_id[$row['gamecode'].$row['playername']]) ?
                    $map_external_to_round_id[$row['gamecode'].$row['playername']] : null;

                # check multiple bets or same round id but diffent player on the same round
                if (!in_array($row['gamecode'], $round_ids) ||
                    (in_array($row['gamecode'], $round_ids) &&
                        !isset($map[$externalId])
                    ))
                {
                    array_push($round_ids, $row['gamecode']);
                    array_push($externalUniqueIds, $row['billno']);

                    $data = $row;
                    $data['extra'] = $this->createGameBetDetialsJson($data);

                    if (!isset($map[$row['billno']])) {
                        $map[$row['billno']] = $data;
                        $map_external_to_round_id[$row['gamecode'].$row['playername']]  = $row['billno'];
                    }
                } else {
                    # merge amount, valid bet, win lose, and valid bet then add it extra info if multiple bets
                    $tmp_data = $map[$map_external_to_round_id[$row['gamecode'].$row['playername']]];
                    $extra = array();
                    $extra = json_decode($tmp_data['extra'], true);
                    $newExtra = $this->createGameBetDetialsJson($row, $extra);

                    $map[$map_external_to_round_id[$row['gamecode'].$row['playername']]]['betamount'] += $row['betamount'];
                    $map[$map_external_to_round_id[$row['gamecode'].$row['playername']]]['validbetamount'] += $row['validbetamount'];
                    $map[$map_external_to_round_id[$row['gamecode'].$row['playername']]]['netamount'] += $row['netamount'];
                    $map[$map_external_to_round_id[$row['gamecode'].$row['playername']]]['extra'] = $newExtra;
                }
            }

            $existingGameCode = $this->CI->agmg_game_logs->getExistingGameCode($round_ids);

            if (!empty($map)) {
                foreach ($map as $key => $row) {
                    # checkout if gamecode is exist in DB
                    if (!empty($existingGameCode) && !$this->is_update_original_row) {
                        if (in_array($row['gamecode'], array_column($existingGameCode, 'gamecode'))) {
                            # Get array index of existing game code
                            // $arrKey = array_search($row['gamecode'], array_column($existingGameCode, 'gamecode'));
                            $existingGameCodeArr = array_column($existingGameCode, 'gamecode');
                            $counts = array_count_values($existingGameCodeArr);
                            $gameCode = $row['gamecode'];

                            $filtered = array_filter($existingGameCodeArr, function ($value) use ($counts, $gameCode) {
                                return $counts[$value] = $gameCode;
                            });

                            if (!empty($filtered)) {
                                foreach ($filtered as $key => $value) {
                                    # Check if this round ID belongs to the same player
                                    if ($existingGameCode[$key]['playername'] == $row['playername']) {
                                        continue 2; # continue outer loop
                                    }
                                }
                            }

                        }
                    }

                    array_push($mergeResult, $row);
                    $count++;
                }
            }
        }

        return $mergeResult;
    }

    public function syncGameLogsToDB($availableResult){
        $this->CI->load->model('agmg_game_logs');
        $records = array();

        # check if multi array
        if (!isset($availableResult[0]) || !is_array($availableResult[0])) {
            //if only one array
            if(is_array($availableResult)){
                $availableResult=[$availableResult];
            }else{
                $this->CI->utils->debug_log('================== sync game logs error availableResult', $availableResult);
                return;
            }
        }

        # merge combo bets
        if ($this->merge_game_logs) {
            $records = $this->syncRecords($availableResult);
        } else {
            # Create bet details before saving data
            $records = $this->createBetDetailsAndCheckIfComboBets($availableResult);
        }

        # dump data to db
        if (!empty($records)) {
            foreach ($records as $record) {
                $this->CI->agmg_game_logs->syncGameLogs($record);
            }
            return;
        }
    }

    public function getIngorePlatformTypes()
    {
        //ignore bbin, pt, hg,
        return $this->ignore_platformtypes;
    }

    //===merge game logs=======================================================================
    public function getOriginalGameLogsByIds($ids)
    {
        $this->CI->load->model('agmg_game_logs');

        return $this->CI->agmg_game_logs->getGameLogStatisticsByIds($ids);
    }

    public function getOriginalGameLogsByDate($startDate, $endDate)
    {
        $this->CI->load->model('agmg_game_logs');

        return $this->CI->agmg_game_logs->getGameLogStatistics($startDate, $endDate);
    }

    public function createGameBetDetialsJson($data, $multibetData = null) {
        $betDetails = empty($multibetData) ? array() : $multibetData;

        $extra_win_amount = $data['netamount'] > 0 ? $data['netamount'] : 0;
        $won_side = $data['netamount'] > 0 ? "Yes" : "No";
        $bet_placed = null;
        if(array_key_exists($data['playtype'], self::AGIN_PLAY_TYPE)){
            $bet_placed=self::AGIN_PLAY_TYPE[$data['playtype']];
        }

        $betDetails['bet_details'][$data['billno']] = array(
            "odds" => null,
            'win_amount' => $extra_win_amount,
            'bet_amount' => $data['betamount'],
            "bet_placed" => $bet_placed,
            "won_side" => $won_side,
            "winloss_amount" => $data['netamount'],
        );
        $betDetails['isMultiBet'] = !empty($multibetData);

        return json_encode($betDetails);
    }

    public function createBetDetailsAndCheckIfComboBets($gameRecords) {
        $this->CI->load->model('agmg_game_logs');
        $map = array();
        $count = 0;
        $newData = [];
        # check if combo bets and create bet details
        if (!empty($gameRecords)) {
            foreach ($gameRecords as $key => $row) {
                if (empty($row['gamecode'])) {
                    array_push($newData, $row);
                    continue;
                }

                $gameRecords[$key]['extra'] = $this->createGameBetDetialsJson($row);
                $arrayMapKey = $row['gamecode'].$row['playername'];

                # check if game code is not exist in map
                if (!array_key_exists($arrayMapKey, $map)) {
                    $map[$arrayMapKey] = array(
                        "game_code" => $row['gamecode'],
                        "isMultiBet" => false,
                    );
                } else {
                    # if exist append bet detials to extra
                    $map[$arrayMapKey]['isMultiBet'] = true;
                    // $newExtra = $this->createGameBetDetialsJson($row, $extra);
                    // $map[$arrayMapKey]['extra'] = $newExtra;
                }
            }
        }

        // print_r($gameRecords);


        # append bet details to raw
        if (!empty($gameRecords)) {
            foreach ($gameRecords as $key => $value) {
                # extract extra info
                $extra = isset($value['extra'])?json_decode($value['extra'], true):[];

                # update extra if multi bet or single bet
                $mapArrayIndex = $value['gamecode'].$value['playername'];
                $extra['isMultiBet'] = isset($map[$mapArrayIndex]['isMultiBet'])?$map[$mapArrayIndex]['isMultiBet']:false;
                $gameRecords[$key]['extra'] = json_encode($extra);
            }
        }

        return $gameRecords;
    }

}

/*end of file*/