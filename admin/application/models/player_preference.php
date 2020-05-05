<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Handles the following player preferences:
 * * auto_transfer: Value = 0 / 1, controls whether transfer all money to game upon game launch
 */
class Player_preference extends BaseModel {

    private $tableName = 'player_preference';

    public function __construct() {
        parent::__construct();
    }

    public function isAutoTransferOnGameLaunch($playerId) {
        $playerPreference = $this->getPlayerPreference($playerId);
        if(empty($playerPreference) || !array_key_exists('auto_transfer', $playerPreference)) {
            return false;
        }
        return $playerPreference['auto_transfer'] == 1;
    }

    public function savePlayerPreference($playerId, $preference, $myfavorites = []) {
        $this->utils->deleteCache($this->getCacheKey($playerId));
        $inputPreferenceString = json_encode($preference);
        $inputMyfavoritesString = json_encode($myfavorites);
        $this->utils->debug_log("Saving player preference [$inputPreferenceString] for player [$playerId]");
        if(!$this->hasPlayerPreference($playerId)) {
            $this->db->insert($this->tableName,
                array(
                    'player_id' => $playerId,
                    'preference' => $inputPreferenceString,
                    'myfavorites' => $inputMyfavoritesString,
                    'created_at' => $this->utils->getNowForMysql(),
                    'updated_at' => $this->utils->getNowForMysql(),
                )
            );
        } else {
            $this->db->update($this->tableName,
                array(
                    'preference' => $inputPreferenceString,
                    'myfavorites' => $inputMyfavoritesString,
                    'updated_at' => $this->utils->getNowForMysql(),
                ),
                array('player_id' => $playerId)
            );
        }
    }

    public function getPlayerPreference($playerId) {
        $playerPreference = $this->utils->getJsonFromCache($this->getCacheKey($playerId));
        if(empty($playerPreference)) {
            $query = $this->db->get_where($this->tableName, array('player_id' => $playerId));
            $result = $query->result_array();
            if(empty($result) || empty($result[0]) || !array_key_exists('preference', $result[0])) {
                return $this->getDefaultPreference();
            } else {
                $playerPreference = json_decode($result[0]['preference'], true);
                $this->utils->saveJsonToCache($this->getCacheKey($playerId), $playerPreference);
            }
        }
        return $playerPreference;
    }

    public function getPlayerMyFavorites($playerId) {
        $query = $this->db->get_where($this->tableName, array('player_id' => $playerId));

        $result = $this->getOneRowArray($query);
        return (empty($result)) ? [] : @json_decode($result['myfavorites'], TRUE);
    }

    public function updatePlayerMyFavorites($playerId, $game_list, $sort = []){
        $current_myfavorites = $this->getPlayerMyFavorites($playerId);

        foreach($current_myfavorites as $game_id => $game_myfavorite){
            foreach($game_list as $game){
                if($game_id == $game['id']){
                    continue;
                }

                unset($current_myfavorites[$game_id]);
            }
        }

        foreach($game_list as $game){
            if(isset($current_myfavorites[$game['id']])){
                continue;
            }

            $current_myfavorites[$game['id']] = [
                // TODO
            ];
        }

        $player_myfavorite_limit_count = $this->utils->getConfig('player_myfavorite_limit_count');

        if(count($current_myfavorites) > $player_myfavorite_limit_count){
            $remove_keys = array_slice(array_keys($current_myfavorites), 0, count($current_myfavorites) - $player_myfavorite_limit_count);
            foreach($remove_keys as $key){
                unset($current_myfavorites[$key]);
            }
        }

        return $this->savePlayerPreference($playerId, $this->getPlayerPreference($playerId), ((empty($game_list)) ? [] : $current_myfavorites));
    }

    private function hasPlayerPreference($playerId) {
        $query = $this->db->get_where($this->tableName, array('player_id' => $playerId));
        return $query->num_rows() > 0;
    }

    private function getDefaultPreference() {
        return $this->utils->getConfig('default_player_preference');
    }

    private function getCacheKey($name) {
        return PRODUCTION_VERSION."|$this->tableName|$name";
    }

    public function storePrefItem($player_id, $pref_key, $pref_val) {
        $pref = $this->getPlayerPreference($player_id);
        $pref[$pref_key] = $pref_val;
        $this->savePlayerPreference($player_id, $pref);
    }
}
