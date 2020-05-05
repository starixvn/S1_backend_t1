<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Vr_game_logs extends Base_game_logs_model {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "vr_game_logs";

    const STATE_PENDING = 0;
    const STATE_CANCELED = 1;
    const STATE_NOT_WINNING = 2;
    const STATE_WIN = 3;

    public function checkIssueNumber($issueNumber,$serialNumber,$gameUsername){
        $this->db->select("extra")
                 ->where("issueNumber", $issueNumber)
                 ->where("playerName", $gameUsername);
        $result = $this->db->get($this->tableName);

        $isExist = null;
        $extra = $result->row_array();

        if(!empty($extra)){
            $extra = json_decode($extra['extra']);

            if(!empty($extra)){
                 foreach ($extra as $key => $value) {
                    if($key == $serialNumber){
                        $isExist = true;
                        continue;
                    }
                }
            }
        }

        return $isExist;
    }

    /**
     * search by issue keys
     * @param  array $issueKeys
     * @param  array $stateArr
     * @return array same player same issue number and multiple bet
     */
    public function getMultipleBetRowsByIssueKeys($issueKeys, $stateArr){

        $this->db->select("vr_game_logs.id as id,
vr_game_logs.playerName,
vr_game_logs.cost AS bet_amount,
vr_game_logs.lossPrize AS loss_amount,
vr_game_logs.playerPrize AS win_amount,
vr_game_logs.external_uniqueid AS external_uniqueid,
vr_game_logs.response_result_id AS response_result_id,
vr_game_logs.createTime AS create_time,
vr_game_logs.updateTime AS update_time,
vr_game_logs.channelName AS channelName,
vr_game_logs.number AS betPlaced,
vr_game_logs.issue_key,

vr_game_logs.unit,
vr_game_logs.multiple,
vr_game_logs.count,
vr_game_logs.position,
vr_game_logs.betTypeName,
vr_game_logs.serialNumber,
vr_game_logs.issueNumber,
vr_game_logs.winningNumber,
vr_game_logs.odds,
vr_game_logs.number,
vr_game_logs.channelId as channel_id,
vr_game_logs.extra,
vr_game_logs.state
")
                 ->from($this->tableName)
                 ->where_in("issue_key", $issueKeys)
                 ->where_in('state', $stateArr);
        $rows=$this->runMultipleRowArray();
        $map=[];
        $result=[];
        if(!empty($rows)){
          foreach ($rows as $row) {
            if(!isset($map[$row['issue_key']])){
              $map[$row['issue_key']]=[];
            }

            $map[$row['issue_key']][]=$row;
          }
          //only return multiple bet
          foreach ($map as $key => $value) {
            if(count($value)>1){
              $result[$key]=$value;
            }
          }
        }

        unset($rows);
        unset($map);

        return $result;

    }

    public function isIssueNumberAlreadyExist($gameRecord){

      $issueNumber=$gameRecord['issueNumber'];
      $playerName=$gameRecord['playerName'];
      $stateArr=[self::STATE_WIN, self::STATE_NOT_WINNING];

      $this->db->select("*")
               ->from($this->tableName)
               ->where("issueNumber", $issueNumber)
               ->where_in('state', $stateArr)
               ->where("playerName", $playerName);
      return $this->runOneRowArray();

    }

    public function checkSerialNumberAlreadyExist($gameRecord){
        $this->db->select("id")
        ->where("serialNumber", $gameRecord['serialNumber']);

        $result = $this->db->get($this->tableName);
        $row_id = $result->row('id');

        return $row_id;
    }

    /**
     * overview : update game logs
     *
     * @param  array    $gameRecord
     * @return boolean
     */
    public function updateVrGameLogs($gameRecord) {
        $this->db->where('serialNumber', $gameRecord['serialNumber'])
            ->set($gameRecord);
        // $this->db->where('state', self::STATE_PENDING);
        // return $this->db->update($this->tableName, $gameRecord);

        return $this->runAnyUpdate( $this->tableName);
    }

    public function insertVrGameLogs($data) {
        // return $this->db->insert($this->tableName, $data);
        return $this->insertData($this->tableName, $data);
    }

    /**
     * get available rows
     * 1. serial number doesn't exist
     * 2. different state
     *
     * @param  array $rows
     * @return array
     */
    public function getAvailableRows($rows) {
        $this->db->select('serialNumber, state')->from($this->tableName)->where_in('serialNumber', array_column($rows, 'serialNumber'));

        $existsRow = $this->runMultipleRowArrayUnbuffered();
        $availableRows = null;
        $diffStateRows=null;

        if (!empty($existsRow)) {
            $availableRows = array();
            $stateMap=[];

            foreach ($existsRow as $existsRow) {
                $stateMap[$existsRow['serialNumber']]=$existsRow['state'];
            }

            foreach ($rows as $row) {
                $snId = $row['serialNumber'];

                if (!isset($stateMap[$snId])) {
                    //doesn't exist
                    $availableRows[] = $row;
                }elseif(isset($stateMap[$snId]) && $stateMap[$snId]!=$row['state']){
                    $this->utils->debug_log('----- found diffStateRows '.$snId);
                    //diff state
                    $diffStateRows[]=$row;
                }
            }
            unset($stateMap);

        } else {
            //doesn't exist all
            $availableRows = $rows;
        }

        return [$availableRows, $diffStateRows];
    }

    public function getGameLogStatistics($dateFrom, $dateTo, $use_create_time=false) {

        $sqlTime='vr_game_logs.updateTime >= ? and vr_game_logs.updateTime <= ?';
        if($use_create_time){
          $sqlTime='vr_game_logs.createTime >= ? and vr_game_logs.createTime <= ?';
        }

        $sql =<<<EOD
select
vr_game_logs.id as id,
game_provider_auth.player_id,
vr_game_logs.playerName,
vr_game_logs.cost AS bet_amount,
vr_game_logs.lossPrize AS loss_amount,
vr_game_logs.playerPrize AS win_amount,
vr_game_logs.external_uniqueid AS external_uniqueid,
vr_game_logs.response_result_id AS response_result_id,
vr_game_logs.createTime AS create_time,
vr_game_logs.updateTime AS update_time,
vr_game_logs.channelName AS channelName,
vr_game_logs.number AS betPlaced,
vr_game_logs.issue_key,

vr_game_logs.unit,
vr_game_logs.multiple,
vr_game_logs.count,
vr_game_logs.position,
vr_game_logs.betTypeName,
vr_game_logs.serialNumber,
vr_game_logs.issueNumber,
vr_game_logs.winningNumber,
vr_game_logs.odds,
vr_game_logs.number,
vr_game_logs.channelId as channel_id,
vr_game_logs.extra,
vr_game_logs.state,

game_description.id AS game_description_id,
game_description.game_name AS game,
game_description.game_code,
game_description.game_type_id,
game_description.void_bet,
game_type.game_type

from vr_game_logs
join game_provider_auth on game_provider_auth.login_name=vr_game_logs.playerName and game_provider_auth.game_provider_id=?
left join game_description on vr_game_logs.channelId = game_description.external_game_id AND game_description.game_platform_id = ?
left join game_type on game_description.game_type_id = game_type.id
where
{$sqlTime}
and vr_game_logs.state in (2,3)
EOD;

        // $secondReadDB = $this->getSecondReadDB();

        return $this->runRawSelectSQLArrayUnbuffered($sql, [VR_API, VR_API, $dateFrom, $dateTo]);

        // return $qry ? null : $qry->result_array();

        // $this->db->select($select, false);
        // $this->db->from('vr_game_logs');
        // $this->db->join('game_description', 'vr_game_logs.channelId = game_description.game_code AND game_description.game_platform_id = "' . VR_API . '" AND game_description.void_bet != 1', 'LEFT');
        // $this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
        // $this->db->where('vr_game_logs.updateTime >= "' . $dateFrom . '" AND vr_game_logs.updateTime <= "' . $dateTo . '"')
        //   ->where_in('vr_game_logs.state', [2,3]);
        // // $this->db->where('vr_game_logs.createTime >= "' . $dateFrom . '"');
        // // $qobj = $this->db->get();
        // $data = $this->runMultipleRowArray();;

        // return $data;

        // return $qobj->result_array();
    }
}

// END OF FILE