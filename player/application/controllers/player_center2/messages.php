<?php
require_once 'PlayerCenterBaseController.php';

/**
 * Provides Messages function
 *
 * @property Player_message_library $player_message_library
 */
class Messages extends PlayerCenterBaseController{
    public function __construct(){
        parent::__construct();

        $this->load->library(array('player_message_library'));

        $this->load->vars('content_template', 'default_with_menu.php');
        $this->load->vars('activeNav', 'messages');
    }

    public function index(){
        $player_id = $this->load->get_var('playerId');

        $data['chat'] = $this->player_message_library->getPlayerAllMessages($player_id, NULL);

        $this->loadTemplate();
        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/messages/messages', $data);
        $this->template->render();
    }

    /**
     * verify add messages
     */
    public function addMessages() {
        if(!$this->authentication->isLoggedIn()){
            return $this->showPlayerAuthFailed();
        }

        if($this->utils->isEnabledFeature('disabled_player_send_message')){
            return $this->returnCommon(self::MESSAGE_TYPE_ERROR, lang('mess.20'), NULL, $this->utils->getPlayerMessageUrl());
        }

        if(!$this->player_message_library->run_validation(Player_message_library::VALIDATION_TYPE_ADD)){
            return $this->returnCommon(self::MESSAGE_TYPE_ERROR, lang('mess.16'), NULL, $this->utils->getPlayerMessageUrl());
        }

        $player_id = $this->load->get_var('playerId');
        $player_name = $this->load->get_var('username');

        $subject = $this->stripHTMLtags($this->input->post('subject', TRUE));
        $message = $this->stripHTMLtags($this->input->post('message', true));

        $save_to_drafts = ( $this->input->post('save_to_drafts') ) ? 1 : '';

        $result = $this->player_message_library->addMessage($player_id, $player_name, $subject, $message, $save_to_drafts);

        if ($result['status']) {
            $message = lang('mess.18');
            $status = self::MESSAGE_TYPE_SUCCESS;
        } else {
            $message = $result['message'];
            $status = self::MESSAGE_TYPE_ERROR;
        }

        return $this->returnCommon($status, $message, NULL, $this->utils->getPlayerMessageUrl());
    }
}