<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alert_column_to_player_201712271700 extends CI_Migration {

    private $tableName = 'player';

    public function up() {
        $data = $this->getPlayerIdOfSMSRegisteredList();
        if ($data) {
            $this->db->update_batch($this->tableName, $data, 'playerId');
        }
    }

    public function down() {
        $this->db->update($this->tableName, ['is_phone_registered' => 0]);
    }

    private function getPlayerIdOfSMSRegisteredList() {
        $qry = $this->db->query("
            SELECT
                player.playerId,
                player.username,
                right(playerdetails.contactNumber, 4) as suffix
            FROM player
            LEFT JOIN playerdetails on playerdetails.playerId = player.playerId
            WHERE 
                LENGTH(player.username) = 9;
        ");
        $rlt = $qry->result_array();

        $playerIdList = [];
        foreach ($rlt as $data) {
            if (!$data['suffix']) continue;
            $prefix = substr($data['username'], 0, 5);
            $suffix = substr($data['username'], 5, 9);

            if (preg_match("/^([a-z]+)$/", $prefix) && $suffix == $data['suffix']) {
                $playerIdList[] = [
                    'playerId' => $data['playerId'],
                    'is_phone_registered' => 1
                ];
            }
        }

        return $playerIdList;
    }
}