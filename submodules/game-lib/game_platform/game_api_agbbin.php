<?php
require_once dirname(__FILE__) . '/game_api_common_ag.php';

/**
 * Defines general behavior of game API classes.
 *
 * General behaviors include:
 * * XML extraction of logs
 * * sync and merge games logs for AGBBIN
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
 * @copyright 2013-2016 TripleOneTech
 */
class Game_api_agbbin extends Game_api_common_ag {

    public function getPlatformCode() {
        return AGBBIN_API;
    }

    public function __construct() {
        parent::__construct();

        $defaultIgnorePlatform=['AGIN', 'AG', 'DSP', 'AGHH', 'IPM', 'MG', 'SABAH', 'HG', 'PT',
            'OG', 'UGS', 'HB', 'XTD', 'PNG', 'NYX', 'ENDO', 'BG', 'HUNTER', 'AGTEX',
            'XIN', 'YOPLAY', 'TTG'];

        $this->ignore_platformtypes=$this->getSystemInfo('ignore_platformtypes', $defaultIgnorePlatform);
    }

    public function getAvailableRows($dataResult){
        $this->CI->load->model('agbbin_game_logs');
        return $this->CI->agbbin_game_logs->getAvailableRows($dataResult);
    }

    public function insertBatchToGameLogs($availableResult){
        $this->CI->load->model('agbbin_game_logs');
        return $this->CI->agbbin_game_logs->insertBatchGameLogsReturnIds($availableResult);
    }

    public function syncGameLogsToDB($dataResult){
        $this->CI->load->model('agbbin_game_logs');
        return $this->CI->agbbin_game_logs->syncGameLogs($dataResult);
    }

    public function getIngorePlatformTypes() {
        //ignore bbin, pt, hg,
        return $this->ignore_platformtypes;
    }

    //===merge game logs=======================================================================
    public function getOriginalGameLogsByIds($ids){
        $this->CI->load->model('agbbin_game_logs');
        return $this->CI->agbbin_game_logs->getGameLogStatisticsByIds($ids);
    }

    public function getOriginalGameLogsByDate($startDate,$endDate){
        $this->CI->load->model('agbbin_game_logs');
        return $this->CI->agbbin_game_logs->getGameLogStatistics($startDate,$endDate);
    }


        // $this->CI->load->model('agbbin_game_logs');
        // $source = $xml;

        // $this->CI->utils->debug_log('process filepath', $xml);

        // $xmlData = '<rows>' . file_get_contents($source, true) . '</rows>';

        // $reportData = simplexml_load_string($xmlData);
        // //print_r($reportData);
        // $cnt = 0;
        // $dataResult = array();

        // foreach ($reportData as $key => $value) {
        //     if((float)$value['netAmount'] == 0 && (float)$value['validBetAmount'] == 0){
        //         continue; // if both 0, ignore it
        //     }
        //     if (in_array( $value['dataType'], $this->ignore_type_array)) {
        //         continue; // this is Transfer Records
        //     }
        //     if (!in_array( $value['platformType'], $this->allowed_platformtypes)) {
        //         continue; // only allowed_platformtypes
        //     }
        //     if (!empty($playerName) && $playerName != $value['playerName']) {
        //         continue;//ignore
        //     }
        //     $cnt++;

        //     // if ((string) $value['platformType'] == self::AGBBIN_PLATFORM_TYPE) {
        //         $result = array();
        //         $result['datatype'] = (string) $value['dataType'];
        //         $result['billno'] = (string) $value['billNo'];
        //         $result['playername'] = (string) $value['playerName'];
        //         $result['agentcode'] = (string) $value['agentCode'];
        //         $result['gamecode'] = (string) $value['gameCode'];
        //         $result['netamount'] = (string) $value['netAmount'];
        //         $result['bettime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime((string) $value['betTime'])));
        //         $result['creationtime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime((string) $value['betTime'])));
        //         $result['recalcutime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime((string) $value['recalcuTime'])));
        //         $result['gametype'] = (string) $value['gameType'];
        //         $result['betamount'] = (string) $value['betAmount'];
        //         $result['validbetamount'] = (string) $value['validBetAmount'];
        //         $result['flag'] = (string) $value['flag'];
        //         $result['currency'] = (string) $value['currency'];
        //         $result['tablecode'] = $this->getStringValueFromXml($value, 'tableCode');
        //         $result['loginip'] = (string) $value['loginIP'];
        //         $result['platformtype'] = (string) $value['platformType'];
        //         $result['remark'] = $this->getStringValueFromXml($value, 'remark');
        //         $result['round'] = (string) $value['round'];
        //         $result['result'] = (string) $value['result'];
        //         $result['uniqueid'] = (string) $value['billNo'];
        //         $result['response_result_id'] = $responseResultId;
        //         $result['external_uniqueid'] = $result['uniqueid'];

        //         #FILTER DUPLICATE ROWS IN XML
        //         if(!in_array($result['uniqueid'], $this->uniqueIds)){
        //             // $result=$this->replaceNullValue($result);
        //             // if($result['billno']=='10836863103'){
        //             //     $this->CI->utils->debug_log('result', $result);
        //             // }
        //             array_push($dataResult, $result);
        //             array_push($this->uniqueIds,$result['uniqueid']);
        //         }

        //     // }
        // }

        // if(count($dataResult)>0){
        //     $availableResult = $this->CI->agbbin_game_logs->getAvailableRows($dataResult);
        //     if(count($availableResult)>0){

        //         $this->CI->utils->debug_log('insert agbbin game logs', count($availableResult));

        //         $ids =$this->CI->agbbin_game_logs->insertBatchToAGBBINGameLogs($availableResult);
        //         $this->syncMergeToGameLogsByIds($ids);
        //     }
        //     // return $cnt;
        // }

        // $this->CI->utils->debug_log('count game record', $cnt, 'filepath', $xml);
        // return $cnt;

    // public function processGameBetDetail($rowArray){

    // }

    public function getGameDescriptionInfo($row, $unknownGame)
    {
        $externalGameId = $row->game_code;
        $extra = array('game_code' => $row->game_code);

        return $this->processUnknownGame(
            $row->game_description_id, $row->game_type_id,
            $row->game, $row->game_type, $externalGameId, $extra,
            $unknownGame);
    }


    const FLAG_TRUE = 1;
    const FLAG_FALSE = 0;



}

/*end of file*/
