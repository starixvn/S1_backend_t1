<?php

require_once dirname(__FILE__).'/game_api_common_ag.php';

/**
 * Defines general behavior of game API classes.
 *
 * General behaviors include:
 * * Extract xml record
 * * sync original game logs
 * * merge game logs
 * * sync logs to AGIN for records.
 * * Getting game description
 * * Updating game logs
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
 *
 * @version 1.8.10
 *
 * @copyright 2013-2016 TripleOneTech
 */
class Game_api_agin extends Game_api_common_ag
{
    public function getPlatformCode()
    {
        return AGIN_API;
    }

    // const AG_PLATFORM_TYPE = 'AGIN';
    // const AG_FISHING_PLATFORM_TYPE = 'HUNTER';
    // const AG_XIN_PLATFORM_TYPE = 'XIN';

    const AGIN_PLAY_TYPE = array(
        1 => "Banker",
        2 => "Player",
        3 => "Tie",
        4 => "Banker Pair",
        5 => "Player Pair",
        6 => "Big",
        7 => "Small",
        8 => "Banker Insurance bets",
        9 => "Player Insurance Bets",
        11 => "Banker no commission",
        12 => "Banker dragon bonus",
        13 => "Player dragon bonus",
        14 => "Super Six",
        15 => "Any Pair",
        16 => "Perfect Pair",
        21 => "Dragon",
        22 => "Tiger",
        23 => "Tie (Dragon Tiger)",
        41 => "big",
        42 => "small",
        43 => "single",
        44 => "double",
        45 => "all wei",
        46 => "wei 1",
        47 => "wei 2",
        48 => "wei 3",
        49 => "wei 4",
        50 => "wei 5",
        51 => "wei 6",
        52 => "single 1",
        53 => "single 2",
        54 => "single 3",
        55 => "single 4",
        56 => "single 5",
        57 => "single 6",
        58 => "double 1",
        59 => "double 2",
        60 => "double 3",
        61 => "double 4",
        62 => "double 5",
        63 => "double 6",
        64 => "combine 12",
        65 => "combine 13",
        66 => "combine 14",
        67 => "combine 15",
        68 => "combine 16",
        69 => "combine 23",
        70 => "combine 24",
        71 => "combine 25",
        72 => "combine 26",
        73 => "combine 34",
        74 => "combine 35",
        75 => "combine 36",
        76 => "combine 45",
        77 => "combine 46",
        78 => "combine 56",
        79 => "sum 4",
        80 => "sum 5",
        81 => "sum 6",
        82 => "sum 7",
        83 => "sum 8",
        84 => "sum 9",
        85 => "sum 10",
        86 => "sum 11",
        87 => "sum 12",
        88 => "sum 13",
        89 => "sum 14",
        90 => "sum 15",
        91 => "sum 16",
        92 => "sum 17",
        101 => "Direct",
        102 => "Separate",
        103 => "Street",
        104 => "Three Numbers",
        105 => "Four Numbers",
        106 => "Triangle",
        107 => "Row (1st Row)",
        108 => "Row (2nd Row)",
        109 => "Row (3rd Row)",
        110 => "Line",
        111 => "1st dozen",
        112 => "2nd dozen",
        113 => "3rd dozen",
        114 => "Red",
        115 => "Black",
        116 => "Big",
        117 => "Small",
        118 => "Odd",
        119 => "Even",
        130 => "1 Fan",
        131 => "2 Fan",
        132 => "3 Fan",
        133 => "4 Fan",
        134 => "1 Nim 2",
        135 => "1 Nim 3",
        136 => "1 Nim 4",
        137 => "2 Nim 1",
        138 => "2 Nim 3",
        139 => "2 Nim 4",
        140 => "3 Nim 1",
        141 => "3 Nim 2",
        142 => "3 Nim 4",
        143 => "4 Nim 1",
        144 => "4 Nim 2",
        145 => "4 Nim 3",
        146 => "Kwok (1,2)",
        147 => "Odd",
        148 => "Kwok (1,4)",
        149 => "Kwok (2,3)",
        150 => "Even",
        151 => "Kwok (3,4)",
        152 => "142",
        153 => "132",
        154 => "143",
        155 => "123",
        156 => "134",
        157 => "124",
        158 => "243",
        159 => "213",
        160 => "234",
        161 => "214",
        162 => "324",
        163 => "314",
        164 => "3:1 (3,2,1)",
        165 => "3:1(2,1,4)",
        166 => "3:1(1,4,3)",
        167 => "3:1(4,3,2)",
        180 => "judgeResult-holdem-180",
        181 => "judgeResult-holdem-181",
        182 => "judgeResult-holdem-182",
        183 => "judgeResult-holdem-183",
        184 => "judgeResult-holdem-184",
        207 => "judgeResult-nn-207",
        208 => "judgeResult-nn-208",
        209 => "judgeResult-nn-209",
        210 => "judgeResult-nn-210",
        211 => "judgeResult-nn-211",
        212 => "judgeResult-nn-212",
        213 => "judgeResult-nn-213",
        214 => "judgeResult-nn-214",
        215 => "judgeResult-nn-215",
        216 => "judgeResult-nn-216",
        217 => "judgeResult-nn-217",
        218 => "judgeResult-nn-218",
        220 => "judgeResult-blackjack-220",
        221 => "judgeResult-blackjack-221",
        222 => "judgeResult-blackjack-222",
        223 => "judgeResult-blackjack-223",
        224 => "judgeResult-blackjack-224",
        225 => "judgeResult-blackjack-225",
        226 => "judgeResult-blackjack-226",
        227 => "judgeResult-blackjack-227",
        228 => "judgeResult-blackjack-228",
        229 => "judgeResult-blackjack-229",
        230 => "judgeResult-blackjack-230",
        231 => "judgeResult-blackjack-231",
        232 => "judgeResult-blackjack-232",
        233 => "judgeResult-blackjack-233",
        260 => "dragon",
        261 => "Phoenix",
        262 => "judgeResult-winThreeCards-262",
        263 => "judgeResult-winThreeCards-263",
        264 => "judgeResult-winThreeCards-264",
        265 => "judgeResult-winThreeCards-265",
        266 => "judgeResult-winThreeCards-266",
        270 => "judgeResult-bullFight-270",
        271 => "judgeResult-bullFight-271",
        272 => "judgeResult-bullFight-272",
        273 => "judgeResult-bullFight-273",
        274 => "judgeResult-bullFight-274",
        275 => "judgeResult-bullFight-275",
        276 => "judgeResult-bullFight-276",
        277 => "judgeResult-bullFight-277",
        278 => "judgeResult-bullFight-278",
        279 => "judgeResult-bullFight-279",
        280 => "judgeResult-bullFight-280",
        281 => "judgeResult-bullFight-281",
        282 => "judgeResult-bullFight-282",
        283 => "judgeResult-bullFight-283",
        284 => "judgeResult-bullFight-284",

    );

    public function __construct()
    {
        parent::__construct();

        $defaultIgnorePlatform=['IPM', 'BBIN', 'MG', 'SABAH', 'HG', 'PT',
            'OG', 'UGS', 'XTD', 'ENDO', 'BG'];
        $this->ignore_platformtypes = $this->getSystemInfo('ignore_platformtypes', $defaultIgnorePlatform);
    }

    public function getAvailableRows($dataResult)
    {
        $this->CI->load->model('agin_game_logs');
        return $this->CI->agin_game_logs->getAvailableRows($dataResult);
    }

    public function insertBatchToGameLogs($availableResult)
    {
        $this->CI->load->model('agin_game_logs');
        if ($this->merge_game_logs) {
            $syncRecords = $this->syncRecords($availableResult);
            return $this->CI->agin_game_logs->insertBatchGameLogsReturnIds($syncRecords);
        } else {
            # add betdetails first before saving the data
            // foreach ($availableResult as $key => $value) {
            //     $availableResult[$key]['extra'] = $this->createGameBetDetialsJson($value);
            // }
            $records = $this->createBetDetailsAndCheckIfComboBets($availableResult);

            return $this->CI->agin_game_logs->insertBatchGameLogsReturnIds($records);
        }

    }

    public function syncRecords($gameRecords) {
        $this->CI->load->model('agin_game_logs');
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

            $existingGameCode = $this->CI->agin_game_logs->getExistingGameCode($round_ids);

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

                    // $this->CI->ab_game_logs->insertGameLogs($row);
                    array_push($mergeResult, $row);
                    $count++;
                }
            }
        }

        return $mergeResult;
    }

    public function syncGameLogsToDB($availableResult){
        $this->CI->load->model('agin_game_logs');
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
            // $availableResult['extra']  = $this->createGameBetDetialsJson($availableResult);
            $records = $this->createBetDetailsAndCheckIfComboBets($availableResult);
            // return $this->CI->agin_game_logs->syncGameLogs($availableResult);
        }

        # dump data to db
        if (!empty($records)) {
            foreach ($records as $record) {
                $this->CI->agin_game_logs->syncGameLogs($record);
            }
            return;
        }
        // return $this->CI->agin_game_logs->syncGameLogs($dataResult);
    }

    public function getIngorePlatformTypes()
    {
        //ignore bbin, pt, hg,
        return $this->ignore_platformtypes;
    }

    //===merge game logs=======================================================================
    public function getOriginalGameLogsByIds($ids)
    {
        $this->CI->load->model('agin_game_logs');

        return $this->CI->agin_game_logs->getGameLogStatisticsByIds($ids);
    }

    public function getOriginalGameLogsByDate($startDate, $endDate)
    {
        $this->CI->load->model('agin_game_logs');

        return $this->CI->agin_game_logs->getGameLogStatistics($startDate, $endDate);
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
        $this->CI->load->model('agin_game_logs');
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
