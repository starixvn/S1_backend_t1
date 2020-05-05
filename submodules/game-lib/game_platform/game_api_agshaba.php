<?php

require_once dirname(__FILE__).'/game_api_common_ag.php';

/**
 * Defines general behavior of game API classes.
 *
 * General behaviors include:
 * * Extract xml record
 * * sync original game logs
 * * merge game logs
 * * sync logs to AGSHABA for records.
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
class Game_api_agshaba extends Game_api_common_ag
{
    public function getPlatformCode()
    {
        return AGSHABA_API;
    }

    public function __construct()
    {
        parent::__construct();

        $defaultIgnorePlatform=['AGIN', 'AG', 'DSP', 'AGHH', 'IPM', 'MG', 'BBIN', 'HG', 'PT',
          'OG', 'UGS', 'HB', 'XTD', 'PNG', 'NYX', 'ENDO', 'BG', 'HUNTER', 'AGTEX',
          'XIN', 'YOPLAY', 'TTG'];
        $this->ignore_platformtypes = $this->getSystemInfo('ignore_platformtypes', $defaultIgnorePlatform);
        // if (empty($this->ignore_platformtypes)) {
        //     $this->ignore_platformtypes = ['AGIN', 'BG', 'HG', 'HUNTER', 'NYX', 'PT', 'BBIN', 'TTG', 'XIN'];
        // }

        $this->is_update_original_row = $this->getSystemInfo('is_update_original_row');
        if ($this->is_update_original_row === null || $this->is_update_original_row === '') {
            $this->is_update_original_row = true;
        }
    }

    public function getAvailableRows($dataResult)
    {
        $this->CI->load->model('agshaba_game_logs');
        return $this->CI->agshaba_game_logs->getAvailableRows($dataResult);
    }

    public function insertBatchToGameLogs($availableResult)
    {
        $this->CI->load->model('agshaba_game_logs');
        return $this->CI->agshaba_game_logs->insertBatchGameLogsReturnIds($availableResult);
    }

    public function syncGameLogsToDB($dataResult){
        $this->CI->load->model('agshaba_game_logs');
        return $this->CI->agshaba_game_logs->syncGameLogs($dataResult);
    }

    public function getIngorePlatformTypes()
    {
        //ignore others
        return $this->ignore_platformtypes;
    }

    //===merge game logs=======================================================================
    public function getOriginalGameLogsByIds($ids)
    {
        $this->CI->load->model('agshaba_game_logs');

        return $this->CI->agshaba_game_logs->getGameLogStatisticsByIds($ids);
    }

    public function getOriginalGameLogsByDate($startDate, $endDate)
    {
        $this->CI->load->model('agshaba_game_logs');

        return $this->CI->agshaba_game_logs->getGameLogStatistics($startDate, $endDate);
    }

   /*
     * extract file name
     *
     * param xmlFileRecord string
     *
     * @return  void
     */
   // protected $uniqueIds =  array();
   // private function extractXMLRecord($xml, $playerName = null, $responseResultId = null) {
   //  $this->CI->load->model('agshaba_game_logs');
   //      $source = $xml;

   //      $xmlData = '<rows>' . file_get_contents($source, true) . '</rows>';

   //      $reportData = simplexml_load_string($xmlData);
   //      //var_dump($reportData);

   //       $cnt = 0;
   //      $dataResult = array();

   //      foreach ($reportData as $key => $value) {

   //          if (in_array( $value['dataType'], $this->ignore_type_array)) {
   //              continue; // this is Transfer Records
   //          }
   //          if (!in_array( $value['platformType'], $this->allowed_platformtypes)) {
   //              continue; // only allowed_platformtypes
   //          }
   //          if (!empty($playerName) && $playerName != $value['playerName']) {
   //              continue;//ignore
   //          }
   //          $cnt++;

   //          if ((string) $value['platformType'] == self::AGSHABA_PLATFORM_TYPE) {

   //              $remarkJsonString = (string)$value['remark'];
   //              $remark = json_decode($remarkJsonString);

   //              $result['datatype'] = (string) $value['dataType'];
   //              $result['billno'] = (string) $value['billNo'];
   //              $result['playername'] = (string) $value['playerName'];
   //              $result['agentcode'] = (string) $value['agentCode'];
   //              $result['gamecode'] = (string) $remark->sport_type;
   //              $result['netamount'] = (float) $value['netAmount'];
   //              $result['bettime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['betTime'])));
   //              //$result['creationtime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['betTime'])));
   //              $result['gametype'] = (string) $value['gameType'];
   //              $result['betamount'] = (float) $value['betAmount'];
   //              $result['validbetamount'] = (float) $value['validBetAmount'];
   //              $result['after_amount'] = (float) $remark->after_amount;
   //              $result['flag'] = (string) $value['flag'];
   //              $result['currency'] = (string) $value['currency'];
   //              $result['tablecode'] = $this->getStringValueFromXml($value, 'tableCode');
   //              $result['loginip'] = (string) $value['loginIP'];
   //              $result['recalcutime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['recalcuTime'])));
   //              $result['platformtype'] = (string) $value['platformType'];
   //              $result['remark'] = $this->getStringValueFromXml($value, 'remark');
   //              $result['round'] = (string) $value['round'];
   //              //$result['result'] = (string) $value['result'];
   //              $result['uniqueid'] = (string) $value['billNo'];
   //              $result['response_result_id'] = $responseResultId;
   //              $result['external_uniqueid'] = $result['uniqueid'];

   //             if ($value['validBetAmount'] != 0 || $value['betAmount'] != 0) {
   //                  #FILTER DUPLICATE ROWS IN XML
   //                  if(!in_array($result['uniqueid'], $this->uniqueIds)){

   //                      $result=$this->replaceNullValue($result);

   //                      array_push($dataResult, $result);
   //                      array_push($this->uniqueIds,$result['uniqueid']);
   //                   }
   //              }

   //          }
   //      }

   //      if(count($dataResult)>0){
   //          $availableResult = $this->CI->agshaba_game_logs->getAvailableRows($dataResult);

   //          if(count($availableResult)>0){

   //              $this->CI->utils->debug_log('insert agshaba game logs', count($availableResult));

   //              $ids =$this->CI->agshaba_game_logs->insertBatchToAGSHABAGameLogs($availableResult);
   //              $this->syncMergeToGameLogsByIds($ids);
   //          }
   //          // return $cnt;
   //      }

   //      $this->CI->utils->debug_log('count game record', $cnt, 'filepath', $xml);
   //      return $cnt;
   //  }

    public function onlyTransferPositiveInteger(){
        return true;
    }

}

/*end of file*/
