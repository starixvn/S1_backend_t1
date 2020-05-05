<?php
require_once 'PlayerCenterBaseController.php';

class Communication_preference extends PlayerCenterBaseController{
    public function __construct(){
        parent::__construct();

        $this->load->vars('content_template', 'default_with_menu.php');
        $this->load->vars('activeNav', 'communication_preference');
        $this->load->model('communication_preference_model');

        if(!$this->utils->isEnabledFeature('enable_communication_preferences')) 
            return $this->error_access();
    }

    /**
     * Load player's communication preference choices on player center
     * 
     * @return ci_view
     * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
     */
    public function index(){

        $player_id = $this->load->get_var('playerId');
        $player = $this->load->get_var('player');
        $data = array(
            'current_preferences' => $this->communication_preference_model->getCurrentPreferences($player_id),
            'config_prefs'        => $this->utils->getConfig('communication_preferences')
        );

        $this->loadTemplate();
        $this->template->write_view('main_content', $this->templateName . '/communication_preference/communication_preference', $data);
        $this->template->render();
    }

    /**
     * Update player's communication preference based on changes given
     * 
     * @return json Update status
     * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
     */
    public function updatePreference()
    {
        $player_id = $this->load->get_var('playerId');
        $ajax_response = array('status' => true);

        if(!$this->input->post()){
            $ajax_response['status'] = false;    
            $this->returnJsonResult($result);
            return;
        }

        $data = $this->input->post();
        $data['player_id'] = $player_id;

        // -- get changes
        $changes = $this->communication_preference_model->getCommunicationPreferenceChanges($data);
        // -- update player's preferences
        $update_preferences = $this->communication_preference_model->updatePlayerCommunicationPreference($player_id, $this->input->post());

        $result = ['status' => 'success', 'message' => lang('sys.gd25')];
        
        if(!$update_preferences){
            $result= ['status' => 'error', 'message' => lang('save.failed')];
        }

        // -- save new log
        $this->communication_preference_model->saveNewLog($player_id, $changes, $player_id, Communication_preference_model::PLATFORM_PLAYER_CENTER);

        $this->returnJsonResult($result);
        
    }

    
}