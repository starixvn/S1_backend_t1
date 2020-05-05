<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Used by monitor_game_logs command. Records missing game logs for resync.
 */
class Game_logs_missing extends BaseModel {

    protected $tableName = 'game_logs_missing';

    function __construct() {
        parent::__construct();
    }

    public function recordMissingGameLog($gamePlatformId, $startTime, $period) {
        if($this->hasRecord($gamePlatformId, $startTime, $period)) {
            return 0;
        }

        # Otherwise, record it
        return $this->db->insert($this->tableName, array(
            'game_platform_id' => $gamePlatformId,
            'start_time' => date('Y-m-d H:i:s', $startTime),
            'period' => $period,
            'last_updated' => date('Y-m-d H:i:s'),
        ));
    }

    public function markGameLogFound($gamePlatformId, $startTime, $period) {
        if(!$this->hasRecord($gamePlatformId, $startTime, $period)) {
            return 0;
        }

        $this->db->where('game_platform_id', $gamePlatformId);
        $this->db->where('start_time', date('Y-m-d H:i:s', $startTime));
        $this->db->where('period >= ', $period);
        return $this->db->update($this->tableName, array('resync_done' => 1, 'last_updated' => date('Y-m-d H:i:s')));
    }

    public function findMissingGameLogsByStage($gamePlatformId, $currentStage, $numDaysBack = 0) {
        $fromDate = date('Y-m-d', strtotime(" - $numDaysBack days")). ' 00:00:00';
        $this->db->select('*');
        $this->db->from($this->tableName);
        $this->db->where('resync_stage', $currentStage);
        $this->db->where('resync_done', 0);
        $this->db->where('start_time >= ', $fromDate);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function updateMissingGameLogsStage($id, $stage, $result) {
        $this->db->where('id', $id);
        $resultStr = json_encode($result);
        return $this->db->update($this->tableName, array(
            'resync_stage' => $stage,
            'last_updated' => date('Y-m-d H:i:s'),
            'sync_result' => $resultStr)
        );
    }

    private function hasRecord($gamePlatformId, $startTime, $period) {
        $this->db->select('count(id) as cnt');
        $this->db->from($this->tableName);
        $this->db->where('game_platform_id', $gamePlatformId);
        $this->db->where('start_time', date('Y-m-d H:i:s', $startTime));
        $this->db->where('period >= ', $period);
        $query = $this->db->get();
        return $this->getOneRowOneField($query, 'cnt') > 0;
    }
}

/////end of file///////