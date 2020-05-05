<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

/**
 * Class Asialong game logs
 *
 * @category Game Model
 *
 * @version 1.8.10
 *
 * @copyright 2013-2018 TripleOneTech
 */
class Asialong_game_logs extends Base_game_logs_model
{
    public function __construct() {
        parent::__construct();
    }

    protected $tableName = 'asialong_game_logs';

    public function getGameLogStatistics($dateTimeFrom, $dateTimeTo) {
        $sqlDateFrom = $dateTimeFrom->format('Y-m-d H:i:s');
        $sqlDateTo = $dateTimeTo->format('Y-m-d H:i:s');

        $this->db->select('username, gtype, betid, rtype, gold, ioratio, result, adddate, wingold, wgold_dm, orderip, betcontent, periodnumber, betdetail, result_ok, external_uniqueid, asialong_game_logs.response_result_id, game_description.id as game_description_id, game_description.game_code, game_description.game_type_id, game_description.game_name')
            ->from('asialong_game_logs')
            ->join('game_description', 'game_description.game_code = gtype AND game_description.game_platform_id = '.ASIALONG_API, 'LEFT')
            ->join('game_provider_auth', 'game_provider_auth.login_name = username AND game_provider_auth.game_provider_id = '.ASIALONG_API, 'LEFT')
            ->where('adddate >=', "$sqlDateFrom")
            ->where('adddate <=', "$sqlDateTo");
        return $this->runMultipleRowArray();
    }

    public function getAvailableRows($rows) {
        $this->db->select('external_uniqueid')->from('asialong_game_logs')->where_in('external_uniqueid', array_column($rows, 'BETID'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'external_uniqueid');
            $availableRows = array();
            foreach ($rows as $row) {
                $uniqueId = $row['external_uniqueid'];
                if (!in_array($uniqueId, $existsId)) {
                    $availableRows[] = $row;
                }
            }
        } else {
            $availableRows = $rows;
        }
        return $availableRows;
    }

}
///END OF FILE///////
