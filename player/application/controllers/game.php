<?php

class Game extends CI_Controller {

	// public function __construct() {
	// 	parent::__construct();
	// 	$this->load->library(['game_library']);
	// }

	// public function index($game_platform_id, $game_type_id = 0, $offset = 0, $limit = 9, $orderby = 'game_description.id', $direction = 'desc') {
	// 	# ALLOW AJAX REQUEST ONLY
	// 	# if ( ! isset($_SERVER['HTTP_REFERER'])) show_404();

	// 	# PROCESS
	// 	$game_types = $this->game_library->getActiveGameTypeList($game_platform_id);
	// 	$game_descriptions = $this->game_library->getActiveGameList($game_platform_id, $game_type_id, $offset, $limit, $orderby, $direction);
	// 	$count = $this->game_library->getActiveGameCount($game_platform_id, $game_type_id);

	// 	# ADD DEFAULT GAME TYPE (ALL GAMES)
	// 	$game_types[] = [
	// 		'id' => 0,
	// 		'game_platform_id' => $game_platform_id,
	// 		'game_type' => 'All Games',
	// 		'game_type_lang' => 'lang.all', # TODO(KAISER): ADD TRANSLATION
	// 		'note' => null,
	// 		'status' => 1,
	// 	];

	// 	# MARK SELECTED GAME TYPE FOR DISPLAY
	// 	$game_types = array_column($game_types, null, 'id');
	// 	$game_types[$game_type_id]['active'] = true;

	// 	# PAGINATION
	// 	$this->load->library('pagination1');
	// 	$config['base_url'] = site_url('game_description/index/' . $game_platform_id . '/' . $game_type_id);
	// 	$config['total_rows'] = $count;
	// 	$config['per_page'] = $limit;
	// 	$config['uri_segment'] = 5;
	// 	$config['first_link'] = '第一';
	// 	$config['first_tag_open'] = '';
	// 	$config['first_tag_close'] = '';
	// 	$config['prev_link'] = '上一页';
	// 	$config['prev_tag_open'] = '';
	// 	$config['prev_tag_close'] = '';
	// 	$config['num_tag_open'] = '';
	// 	$config['num_tag_close'] = '';
	// 	$config['cur_tag_open'] = '<span class="curr">';
	// 	$config['cur_tag_close'] = '</span>';
	// 	$config['next_link'] = '下一页';
	// 	$config['next_tag_open'] = '';
	// 	$config['next_tag_close'] = '';
	// 	$config['last_link'] = '最后';
	// 	$config['last_tag_open'] = '';
	// 	$config['last_tag_close'] = '';
	// 	$this->pagination1->initialize($config);

	// 	# DATA
	// 	$data['game_types'] = array_values($game_types);
	// 	$data['game_descriptions'] = $game_descriptions;
	// 	$data['pagination'] = $this->pagination1->create_links();

	// 	# OUTPUT
	// 	$this->output->set_header('Access-Control-Allow-Origin: *');
	// 	$this->output->set_content_type('application/json');
	// 	$this->output->set_output(json_encode($data, JSON_PRETTY_PRINT));
	// }

}