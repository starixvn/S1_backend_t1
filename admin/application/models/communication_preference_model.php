<?php
/**
 *   filename:   communication_preference_model.php
 *   author:     Cholo Miguel Antonio
 *   e-mail:     cholo.php.ph@tripleonetech.net
 *   date:       2018-07-16
 *   @brief:     model for communication preferences
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/base_model.php';

/**
 * This model represents communication preference data.
 */
class Communication_preference_model extends BaseModel {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = 'player_communication_preference_history';

    const STATUS_CANCELLED = 2;
    const STATUS_SET_AS_PREFERENCE = 1;

    const PLATFORM_PLAYER_CENTER = 2;
    const PLATFORM_SBE = 1;

    /**
     * Save a new log/history based on changes made
     *
     * @param  string  $player_id Player ID
     * @return boolean       Saving status
     * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
     */
    public function saveNewLog($player_id, $final_changes, $requested_by, $platform_used = self::PLATFORM_SBE, $comments = null)
    {
    	if(!$this->utils->isEnabledFeature('enable_communication_preferences')) return false;

    	$this->utils->debug_log('=========> ' . date('Y-m-d H:i:s') . ' - Start saving new log for player_id: ', $player_id, $final_changes);

    	$config_pref = $this->utils->getConfig('communication_preferences');

    	if(empty($config_pref)) return false;

    	if(is_null($player_id) || is_null($final_changes) || is_null($requested_by)) return false;

    	$current_preference = (array) $this->getCurrentPreferences($player_id);

    	$data = array(
    		'player_id' => $player_id,
    		'preferences' => '',
    		'changes' => '',
    		'status' => '',
    		'notes' => $comments,
    		'platform_used' => $platform_used,
    		'requested_by' => $requested_by,
    		'requested_at' => date('Y-m-d H:i:s'),
    	);



    	$changes = json_decode($final_changes);
    	// -- Save logs for each changes made
    	foreach ($changes as $key => $change) {
    		//-- Get changes
    		$data['changes'] = json_encode(array($key => $change));

    		// -- set status
    		$data['status'] = self::STATUS_SET_AS_PREFERENCE;
    		if($change == "false")
    			$data['status'] = self::STATUS_CANCELLED;

    		if(empty($current_preference))
    			$data['preferences'] = $final_changes;
    		else
    		{
    			// -- Remove the old preference values
    			foreach ($current_preference as $preference_key => $current) {
    				if(!isset($config_pref[$preference_key]))
    					unset($current_preference[$preference_key]);
    			}

    			// -- Insert / Update new changes
    			if(isset($current_preference[$key]))
    				$current_preference[$key] = $changes->$key;
    			else
    				$current_preference[$key] = $change;

    			$data['preferences'] = json_encode($current_preference);

    		}

    		$this->utils->debug_log('Inserting new communication preference log - ', $data);

    		if($this->insertRow($data))
    			$this->utils->debug_log('Successfully created new communication preference log.');
    		else
    			$this->utils->debug_log('Error in creating new communication preference log. Log was not saved.');

    	}

    	return true;
    }

    /**
     * Get latest communication preferences
     * @param  string $player_id Player's ID
     * @return array            Player's Preferences
     * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
     */
    public function getCurrentPreferences($player_id = null)
    {
    	if($player_id == null) return (object) array();

    	$playerdetails = (array) $this->db->get_where('playerdetails', array('playerId' => $player_id))->row();

    	if(empty($playerdetails) || empty($playerdetails['communication_preference'])) return (object) array();

    	return json_decode($playerdetails['communication_preference']);
    }

    /**
	 * Get latest changes on communcation preference
	 *
	 * @param  array $post Input Post
	 * @return JSON       Latest preferences in JSON format
	 * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
	 */
	public function getCommunicationPreferenceChanges($post)
	{
		$final_changes = array();
		$old_preferences = array();
		$player_id = 0;
		$config_prefs = $this->utils->getConfig('communication_preferences');

		if(!$this->utils->isEnabledFeature('enable_communication_preferences') || empty($config_prefs))
			return json_encode($final_changes);


		if(isset($post['player_id'])){
			$player_id = $post['player_id'];
			$old_preferences = $this->getCurrentPreferences($player_id);
		}

		// -- Get changes based on post request
		foreach ($config_prefs as $config_prefs_key => $config_pref) {

			if(!isset($post['player_id']))
				$final_changes[$config_prefs_key] = "false";

			if(isset($post['pref-data-'.$config_prefs_key]))
				$final_changes[$config_prefs_key] = $post['pref-data-'.$config_prefs_key];
		}

		// -- Filter only the changes based on the last record
		if(!$old_preferences){
			foreach ($old_preferences as $key => $old_preference) {
				if(isset($final_changes[$key]) && $final_changes[$key] == $old_preference)
					unset($final_changes[$key]);
			}
		}


		return json_encode($final_changes);
	}

    /**
     * Update's player communication preference based on the changes given
     *
     * @param  string $player_id Player's Id
     * @param  json $changes     Changes on his current commpref
     * @return boolean           Update status
     * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
     */
	public function updatePlayerCommunicationPreference($player_id, $changes)
	{
		$final_changes = array();

		$config_prefs = $this->utils->getConfig('communication_preferences');

		if(!$this->utils->isEnabledFeature('enable_communication_preferences') || empty($player_id) || empty($changes)) return false;

        $this->utils->debug_log('=========> ' . date('Y-m-d H:i:s') . ' - Start Updating of Player Communication Preference.', $player_id, $changes );

		$player_preferences = (object) $this->getCurrentPreferences($player_id);

		// -- Change current values of the current preference; Remove preferences that are not in config anymore
		foreach ($player_preferences as $key => $old_preference) {

			if(isset($changes['pref-data-'.$key]))
				$player_preferences->$key = $changes['pref-data-'.$key];
			elseif(!isset($config_prefs[$key]))
				unset($player_preferences->$key);
		}

		// -- Add new preferences that does not exist from old preferences
		foreach ($changes as $change_key => $change) {

			$change_key = str_replace("pref-data-","",$change_key);

			if(!isset($player_preferences->$change_key))
				$player_preferences->$change_key = $change;
		}

		// -- Add missing preferences from config but was not submitted
		foreach ($config_prefs as $config_pref_key => $config_pref) {
			if(!isset($player_preferences->$config_pref_key))
				$player_preferences->$config_pref_key = "false";
		}

		$data = array(
			'communication_preference' => json_encode($player_preferences)
		);

        $this->utils->debug_log('Value to be updated in playerdetails: ', $data );

		$this->db->where('playerId', $player_id);
		return $this->db->update('playerdetails', $data);

	}

    /**
     * Retrieve Player's Communication Preference History
     * based on his Player ID and other filters such as
     * datetime range
     *
     * @param  string $playerId Player ID
     * @param  array $where    Filters
     * @param  array $values   Filter Values
     * @return array/object    Result Set
     * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
     */
    public function getCommunicationPreferenceHistory($playerId = null, $where = null, $values = null){

        $this->db->select('cp.id , cp.player_id, cp.preferences, cp.changes, cp.status, cp.notes, cp.requested_by, cp.requested_at, cp.platform_used, p.username as "username"');
        $this->db->from('player_communication_preference_history cp');
        $this->db->join('player p', 'cp.player_id = p.playerId', 'left');

        if($playerId != null)
            $this->db->where('cp.player_id', $playerId);

        if($where != null)
        {
            foreach ($where as $where_key => $where_value) {
                 $this->db->where($where_value, $values[$where_key]);
            }
        }

		$query = $this->db->get();
		// print_r('$query:');
		// print_r($this->db->last_query())
		$result = $query->result_array();
        $result = json_decode(json_encode($result),true);  ///  <<<< OGP-13339, memory exhausted

        return $result;
    }


    /**
     * Reset communication preferences of the player
     * using the configurations
     *
     * @param  string $player_id
     * @param  string $notes
     * @return void
     * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
     */
    public function resetCommunicationPreference($player_id, $requested_by, $notes, $platform_used)
    {
        // -- Reset values of player's communication preference
        $config_comm_pref = $this->utils->getConfig('communication_preferences');

        if(!empty($config_comm_pref) && $this->utils->isEnabledFeature('enable_communication_preferences'))
        {
            $data = array();
            $data['player_id'] = $player_id;
            $data['notes'] = $notes;

            foreach ($config_comm_pref as $key => $value) {
                $data['pref-data-'.$key] = "false";
            }

            // -- Get changes on player's communication preferences
            $changes = $this->getCommunicationPreferenceChanges($data);
            unset($data['player_id']);
            unset($data['notes']);

            // -- Update player's preference
            $update_preferences = $this->updatePlayerCommunicationPreference($player_id, $data);


            // -- save new log
            $this->saveNewLog($player_id, $changes, $requested_by, $platform_used, $notes);
        }

    }

    public function updateCommunicationPreferenceWithSelfExclusion($player_id, $notes){
        $this->load->model('communication_preference_model');
        $this->load->library('authentication');
        $config_comm_pref = $this->utils->getConfig('communication_preferences');

        $data = [];
        foreach ($config_comm_pref as $comm_pref_key => $comm_pref_value) {
            $data['pref-data-'.$comm_pref_key] = 'false';
        }

        // -- Get changes on player's communication preferences
        $changes = $this->getCommunicationPreferenceChanges($data);

        // -- Update player's preference
        $this->updatePlayerCommunicationPreference($player_id, $data);

        // -- Get admin user ID
        $adminId = $this->authentication->getUserId();
        $adminId = empty($adminId) ? Users::SUPER_ADMIN_ID : $adminId;

        // -- save new log
        $this->saveNewLog($player_id, $changes, $adminId, Communication_preference_model::PLATFORM_SBE, $notes);

    }

}