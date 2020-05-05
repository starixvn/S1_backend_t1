<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Game_description
 *
 * Game description library
 *
 * @package		Game_description
 * @author		kaiser.dapar
 * @version		1.0.0
 */
class Game_library 
{
	private $site_dir		 = FCPATH;
	private $img_dir_path  	 = '/resources/images/games/';
	private $img_extension 	 = '.jpg';

	public function __construct() 
	{
		$this->ci =& get_instance();
		$this->ci->load->model(['game_description_model','game_type','static_site']);

		# LOAD LANGUAGE
		$site = $this->ci->static_site->getSiteByName('default');
		$this->ci->lang->load('main', $site->lang);
	}

	public function getActiveGameTypeList($game_platform_id, $orderby = 'order_id', $direction = 'desc') 
	{

		if ($game_platform_id) $criteria['game_platform_id'] = $game_platform_id;

		$criteria['flag_show_in_site'] = 1; # ACTIVE GAME TYPES
		$list = $this->ci->game_type->getGameTypeList($criteria, $orderby, $direction);
		
		# PROCESS TRANSLATION
		foreach ($list as &$item) {
			$item['game_type'] = lang($item['game_type_lang']) ? : $item['game_type'];
			$item['game_type'] = preg_replace("#^(AG|PT)#", '', $item['game_type']);
		}

		return $list;
	}

	public function getActiveGameDescriptionList($game_platform_id, $game_type_id, $offset = 0, $limit = 5, $orderby = 'game_description.id', $direction = 'desc') 
	{
		if ($game_platform_id) $criteria['game_description.game_platform_id'] = $game_platform_id;
		if ($game_type_id) $criteria['game_description.game_type_id'] = $game_type_id;

		# ONLY ACTIVE
		$criteria['game_type.flag_show_in_site'] = 1; # ACTIVE GAME TYPES
		$criteria['game_description.flag_show_in_site'] = 1; # ACTIVE GAMES
		$list = $this->ci->game_description_model->getGameDescriptionList($criteria, $offset, $limit, $orderby, $direction);
		
		# PROCESS TRANSLATION AND IMAGE
		$default = $this->img_dir_path . $game_platform_id . $this->img_extension;
		foreach ($list as &$item) {

			$file_path = $this->img_dir_path . $item['game_code'] . $this->img_extension;

			$item['game_name']   = lang($item['game_name']) ? : $item['game_name'];
			$item['game_image']  = site_url();
			$item['game_image'] .= file_exists($this->site_dir . $file_path) ? $file_path : $default;

		}

		return $list;
	}

	public function getActiveGameDescriptionCount($game_platform_id, $game_type_id) 
	{
		if ($game_platform_id) $criteria['game_description.game_platform_id'] = $game_platform_id;
		if ($game_type_id) $criteria['game_description.game_type_id'] = $game_type_id;

		# ONLY ACTIVE
		$criteria['game_type.flag_show_in_site'] = 1; # ACTIVE GAME TYPES
		$criteria['game_description.flag_show_in_site'] = 1; # ACTIVE GAMES
		$count = $this->ci->game_description_model->getGameDescriptionCount($criteria);
		
		return $count;
	}

}