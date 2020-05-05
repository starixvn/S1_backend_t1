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
 * @copyright 2013-2016 TripleOneTech
 */

class Game_api_agpt extends Game_api_common_ag {

	public function getPlatformCode() {
		return AGPT_API;
	}

    const AG_PLATFORM_TYPE = 'PT';

	public function __construct() {
		parent::__construct();

	}

    public function syncOriginalGameLogs($token) {
        $gameLogDirectoryAG = $this->getGameRecordPath();
        $playerName = $this->getValueFromSyncInfo($token, 'playerName');
        $dateTimeFrom = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');
        $syncId = parent::getValueFromSyncInfo($token, 'syncId');
        $this->CI->utils->debug_log('from', $dateTimeFrom, 'to', $dateTimeTo);

        // $intervalObj = DateInterval::createFromDateString('1 hours');
        $dateTimeFrom->modify($this->getDatetimeAdjust());

        $dirArr = $this->getSubDirectories($gameLogDirectoryAG);
        foreach ($dirArr as $dir) {
            $this->CI->utils->debug_log('search ag dir', $dir);
            $this->retrieveXMLFromLocal($dir, $dateTimeFrom, $dateTimeTo, $playerName, $syncId);
        }
        return array('success' => true);
    }


     protected function retrieveXMLFromLocal($directory, $dateTimeFrom = null, $dateTimeTo = null, $playerName = null, $syncId = null) {
        $intervalObj = DateInterval::createFromDateString('1 hours');
        $this->filterXML($directory, $dateTimeFrom->sub($intervalObj), $dateTimeTo, $playerName, $syncId);
     }


     private function filterXML($directory, $dateTimeFrom, $dateTimeTo, $playerName, $syncId) {
        //convert to game time
        $dateTimeFrom = new DateTime($this->serverTimeToGameTime($dateTimeFrom->format('Y-m-d H:i:s')));
        $dateTimeTo = new DateTime($this->serverTimeToGameTime($dateTimeTo->format('Y-m-d H:i:s')));

        $this->CI->utils->debug_log('real dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo);

        $startDate = $dateTimeFrom->format("Ymd");

        for ($i = $dateTimeFrom; $i <= $dateTimeTo; $i->modify('+1 day')) {
            //extract local xml to agGameLogs table
            $dir = $directory . '/' . $i->format("Ymd");
            $this->CI->utils->debug_log('real date time', $i, 'real dir', $dir);
            if (is_dir($dir)) {
                $agGameLogsXml = array_diff(scandir($dir), array('..', '.'));
                foreach ($agGameLogsXml as $key) {
                    if (current(explode(".", $key)) >= $startDate) {
                        //save to response result
                        $filepath = $dir . '/' . $key;
                        $responseResultId = $this->saveResponseResultForFile(true, 'AGPT', null, $filepath, array('sync_id' => $syncId));
                        $this->extractXMLRecord($filepath, $playerName, $responseResultId);
                    }
                }
            }
        }
    }



    /**
     * extract file name
     *
     * param xmlFileRecord string
     *
     * @return  void
     */
    private function extractXMLRecord($filepath, $playerName = null, $responseResultId = null) {
        $source = $filepath;

        $xmlData = '<rows>' . file_get_contents($source, true) . '</rows>';
        $reportData = simplexml_load_string($xmlData);
        $cnt = 0;
        foreach ($reportData as $key => $value) {
            if (!empty($playerName) && $playerName != $value['playerName']) {
                //ignore
                continue;
            }
            $cnt++;

              if ((string) $value['platformType'] == self::AG_PLATFORM_TYPE) {

                $result['datatype'] = (string) $value['dataType'];
                $result['billno'] = (string) $value['billNo'];
                $result['playername'] = (string) $value['playerName'];
                $result['agentcode'] = (string) $value['agentCode'];
                $result['gamecode'] = (string) $value['gameCode'];
                $result['netamount'] = (float) $value['netAmount'];
                $result['bettime'] = (string) $value['betTime'];
                $result['creationtime'] = (string) $value['betTime'];
                $result['gametype'] = (string) $value['gameType'];
                $result['betamount'] = (float) $value['betAmount'];
                $result['validbetamount'] = (float) $value['validBetAmount'];
                $result['flag'] = (string) $value['flag'];
                $result['currency'] = (string) $value['currency'];
                $result['tablecode'] = $this->getStringValueFromXml($value, 'tableCode');
                $result['loginip'] = (string) $value['loginIP'];
                $result['recalcutime'] = (string) $value['recalcuTime'];
                $result['platformtype'] = (string) $value['platformType'];
                $result['remark'] = $this->getStringValueFromXml($value, 'remark');
                $result['round'] = (string) $value['round'];
                $result['result'] = (string) $value['result'];
                $result['beforecredit'] = (string) $value['beforeCredit'];
                $result['uniqueid'] = (string) $value['billNo'];
                $result['response_result_id'] = $responseResultId;
                // $result['external_uniqueid'] = empty($value['gameCode']) ? $result['uniqueid'] : (string) $value['gameCode'];
                $result['external_uniqueid'] = empty($value['gameCode']) ? $result['uniqueid'] : (string) $value['gameCode'] . '-' . $value['playerName'];

                if ($this->isUniqueIdAlreadyExists($value['billNo'])) {
                    if ($value['flag'] != 0 || $value['validBetAmount'] != 0 || $value['betAmount'] != 0) {
                        $this->updateGameLogs($result);
                    }
                } else {
                    if ($value['flag'] != 0 || $value['validBetAmount'] != 0 || $value['betAmount'] != 0) {
                        $this->syncToAGPTGameLogs($result);
                    }
                }
                
            }
        }
        $this->CI->utils->debug_log('count game record', $cnt, 'filepath', $filepath);
        return $cnt;
    }



    public function syncMergeToGameLogs($token) {
      
 
    	 $this->CI->load->model(array('game_logs','agpt_game_logs'));

	    $dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
	    $dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
	    $dateTimeFrom->modify("-1 hours");

	    $this->CI->utils->debug_log('AGPT', '[syncMergeToGameLogs]', 'dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo);
	    $result = $this->CI->agpt_game_logs->getAGPTGameLogStatistics($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo->format('Y-m-d H:i:s'));
	 
	    if ($result) {
	        $unknownGame = $this->getUnknownGame();
	        foreach ($result as $key) {
	            $username = strtolower($key->playername);

	                // $game_type_id = empty($key->game_type_id) ? $unknownGame->game_type_id : $key->game_type_id;
	                // $game_description_id = empty($key->game_description_id) ? $unknownGame->game_description_id : $key->game_description_id;

	            list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($key, $unknownGame);

	            $gameDate = new \DateTime($key->creationtime);
	            $gameDateStr = $this->CI->utils->formatDateTimeForMysql($gameDate);
	            $extra = array('table' => $key->tablecode);
	            if ($key->datatype == 'TR') {
	                $result_amount = $key->transferamount;
	                $after_balance = $key->currentamount;
	                $flag = Game_logs::FLAG_TRANSACTION;
	            } else {
	                $result_amount = $key->netamount;
	                    $after_balance = $key->beforecredit; // + $result_amount;
	                    $flag = Game_logs::FLAG_GAME;
	                }

	                $this->syncGameLogs($game_type_id, $game_description_id, $key->game_code,
	                    $key->game_type, $key->game, null, $username,
	                    $key->validbetamount, $result_amount, null, null, $after_balance, null,
	                    $key->external_uniqueid, $gameDateStr, $gameDateStr, $key->response_result_id, $flag, $extra);

	            }
	        }
    }



    private function syncToAGPTGameLogs($data) {
		//convert bettime
		$this->CI->load->model('agpt_game_logs');
		if (isset($data['bettime']) && !empty(@$data['bettime'])) {
			$data['bettime'] = $this->gameTimeToServerTime($data['bettime']);
		}
		if (isset($data['creationtime']) && !empty(@$data['creationtime'])) {
			$data['creationtime'] = $this->gameTimeToServerTime($data['creationtime']);
		}
		$this->CI->agpt_game_logs->syncToAGPTGameLogs($data);
	}


    private function getGameDescriptionInfo($row, $unknownGame) {
        $externalGameId = $row->game;
        $extra = array('game_code' => $row->game_code);
        return $this->processUnknownGame(
            $row->game_description_id, $row->game_type_id,
            $row->game, $row->game_type, $externalGameId, $extra,
            $unknownGame);
    }


    private function updateGameLogs($data) {
		//convert bettime
		$this->CI->load->model('agpt_game_logs');
		if (isset($data['bettime']) && !empty(@$data['bettime'])) {
			$data['bettime'] = $this->gameTimeToServerTime($data['bettime']);
		}
		if (isset($data['creationtime']) && !empty(@$data['creationtime'])) {
			$data['creationtime'] = $this->gameTimeToServerTime($data['creationtime']);
		}
		return $this->CI->agpt_game_logs->updateGameLogs($data);
	}


	private function isUniqueIdAlreadyExists($uniqueId) {
		$this->CI->load->model('agpt_game_logs');
		return $this->CI->agpt_game_logs->isUniqueIdAlreadyExists($uniqueId);
	}



}

/*end of file*/