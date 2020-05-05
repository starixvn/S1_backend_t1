<?php
require_once 'PlayerCenterBaseController.php';

/**
 * Show message
 */
class Show_message extends PlayerCenterBaseController{
    public function index(){
        $data = [
            'content_template' => 'default_with_menu.php',
            'type' => $this->input->get('type'),
            'title' => $this->input->get('title'),
            'message' => $this->input->get('message'),
            'redirect_url' => $this->utils->getSystemUrl('player', $this->input->get('redirect_url')),
        ];

        $view = $this->utils->getPlayerCenterTemplate() . '/includes/message';

        if($this->load->view_exists($view)){
            $this->loadTemplate();
            $this->template->write_view('main_content', $view, $data);
            $this->template->render();
        }else{
            $this->load->view('/resources/common/includes/message', $data);
        }
    }
}
