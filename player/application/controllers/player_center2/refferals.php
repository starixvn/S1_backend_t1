<?php
require_once 'PlayerCenterBaseController.php';

/**
 * Provides refferals function
 *
 */
class Refferals extends PlayerCenterBaseController{
    public function __construct(){
        parent::__construct();

        $this->load->vars('content_template', 'default_with_menu.php');
        $this->load->vars('activeNav', 'refferals');
    }

    public function index(){
        $player_id = $this->load->get_var('playerId');

        $data = [];

        $this->loadTemplate();
        $this->template->append_function_title(lang('Refer a Friend'));
        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/player/refferals', $data);
        $this->template->render();
    }
}