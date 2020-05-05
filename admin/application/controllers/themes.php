<?php

/**
 * Themes Management
 *
 * Themes Management Controller for FHI OG System
 *
 * @author  Mark Bandilla
 *
 */

class Themes extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('url'));
        $this->load->library(array('session'));
    }

	public function switchTheme($theme) {
		$this->session->set_userdata('admin_theme', $theme);

		$referred_from = $this->session->userdata('current_url');
		redirect($referred_from, 'refresh');
	}
}

?>