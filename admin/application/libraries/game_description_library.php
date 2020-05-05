<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Game_description
 *
 * Game description library
 *
 * @package		Game_description
 * @author		kaiser.dapar
 * @version		1.0.0
 */
class Game_description_library {
	# TODO(KAISER): FIND A BETTER WAY TO IDENTIFY THE RIGHT DIRECTORIES AND PATHS
	private $site_dir = FCPATH;
	private $img_dir_path = '/resources/images/games/';
	private $jpg_extension = '.jpg';
	private $png_extension = '.png';

	public function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->model(['game_description_model', 'game_type_model', 'static_site']);

		# LOAD LANGUAGE
		$site = $this->ci->static_site->getSiteByName('default');
		$this->ci->lang->load('main', $site->lang);
	}

	public function getActiveGameTypeList($game_platform_id, $orderby = 'order_id', $direction = 'desc') {

		if ($game_platform_id) {
			$criteria['game_platform_id'] = $game_platform_id;
		}

		$criteria['flag_show_in_site'] = 1; # ACTIVE GAME TYPES
		$list = $this->ci->game_description_model->getGameTypeList($criteria, $orderby, $direction);

		# PROCESS TRANSLATION
		foreach ($list as &$item) {
			$item['game_type'] = lang($item['game_type_lang']) ?: $item['game_type'];
			$item['game_type'] = preg_replace("#^(AG|PT)#", '', $item['game_type']);
		}

		return $list;
	}

	public function getActiveGameDescriptionList($game_platform_id, $game_type_id = false, $offset = 0, $limit = 10000, $orderby = '(-game_description.game_order)', $direction = 'desc') {
		if ($game_platform_id) {
			$criteria['game_description.game_platform_id'] = $game_platform_id;
		}

		if ($game_type_id) {
			$criteria['game_description.game_type_id'] = $game_type_id;
		}
		# ONLY ACTIVE
		$criteria['game_type.flag_show_in_site'] = 1; # ACTIVE GAME TYPES
		$criteria['game_description.flag_show_in_site'] = 1; # ACTIVE GAMES
		if ($game_platform_id == MG_API) {
			$criteria['game_description.flash_enabled'] = 1; # flash games
			$criteria['game_description.html_five_enabled'] = null;
		}

		$list = $this->ci->game_description_model->getGameDescriptionList($criteria, $offset, $limit, $orderby, $direction);
		
		# PROCESS TRANSLATION AND IMAGE
		if ($game_platform_id == NT_API) {
			$img_dir_path = $this->img_dir_path . 'nt/';
			$default = $img_dir_path . $game_platform_id . $this->jpg_extension;
			foreach ($list as &$item) {
				$attributes = json_decode($item['attributes']);
				$file_path = $img_dir_path . $attributes->image . $this->jpg_extension;

				$item['game_name'] = lang($item['game_name']) ?: $item['game_name'];
				$item['game_image'] = site_url();
				$item['game_image'] .= file_exists($this->site_dir . $file_path) ? $file_path : $default;
				$item['image_file'] = (file_exists($this->site_dir . $file_path) ? $attributes->image : $game_platform_id) . $this->jpg_extension;

			}
		} else if ($game_platform_id == AG_API) {
			$img_dir_path = $this->img_dir_path;
			$default = $img_dir_path . $game_platform_id . $this->jpg_extension;
			foreach ($list as &$item) {

				$file_path = $img_dir_path . $item['game_code'] . $this->jpg_extension;

				$item['game_name'] = lang($item['game_name']) ?: $item['game_name'];
				$item['game_image'] = site_url();
				$item['game_image'] .= file_exists($this->site_dir . $file_path) ? $file_path : $default;
				$item['image_file'] = (file_exists($this->site_dir . $file_path) ? $item['game_code'] : $game_platform_id) . $this->jpg_extension;

			}
		} else if ($game_platform_id == BBIN_API) {
			$img_dir_path = $this->img_dir_path . 'bbin/';
			$default = $img_dir_path . $game_platform_id . $this->jpg_extension;
			foreach ($list as &$item) {
				$attributes = json_decode($item['attributes']);
				$file_path = $img_dir_path . 'Game_' . $item['game_code'] . $this->png_extension;
				$item['game_name'] = lang($item['game_name']) ?: $item['game_name'];
				$item['game_image'] = site_url();
				$item['game_image'] .= file_exists($this->site_dir . $file_path) ? $file_path : $default;
				$item['image_file'] = (file_exists($this->site_dir . $file_path) ? 'Game_' . $item['game_code'] . $this->png_extension : $game_platform_id . $this->jpg_extension);

			}
		} else if ($game_platform_id == ONE88_API) {
			$img_dir_path = $this->img_dir_path . '188/';
			$default = $img_dir_path . $game_platform_id . $this->jpg_extension;
			foreach ($list as &$item) {
				$attributes = json_decode($item['attributes']);
				$file_path = $img_dir_path . 'Game_' . $item['game_code'] . $this->png_extension;
				$item['game_name'] = lang($item['game_name']) ?: $item['game_name'];
				$item['game_image'] = site_url();
				$item['game_image'] .= file_exists($this->site_dir . $file_path) ? $file_path : $default;
				$item['image_file'] = (file_exists($this->site_dir . $file_path) ? 'Game_' . $item['game_code'] . $this->png_extension : $game_platform_id . $this->jpg_extension);

			}
		} else if ($game_platform_id == ONESGAME_API) {
			$img_dir_path = $this->img_dir_path . 'onesgame/';
			$default = $img_dir_path . $game_platform_id . $this->jpg_extension;
			foreach ($list as &$item) {
				$file_path = $img_dir_path . $item['game_code'] . $this->jpg_extension;
				$item['game_name'] = lang($item['game_name']) ?: $item['game_name'];
				$item['game_image'] = site_url();
				$item['game_image'] .= file_exists($this->site_dir . $file_path) ? $file_path : $default;
				$item['image_file'] = (file_exists($this->site_dir . $file_path) ? $item['game_code'] : $game_platform_id) . $this->jpg_extension;

			}
		} else if ($game_platform_id == INTEPLAY_API) {
			$img_dir_path = $this->img_dir_path . 'inteplay/';
			$default = $img_dir_path . $game_platform_id . $this->jpg_extension;
			foreach ($list as &$item) {
				$file_path = $img_dir_path . $item['game_code'] . $this->jpg_extension;
				$item['game_name'] = lang($item['game_name']) ?: $item['english_name'];
				$item['game_image'] = site_url();
				$item['game_image'] .= file_exists($this->site_dir . $file_path) ? $file_path : $default;
				$item['image_file'] = (file_exists($this->site_dir . $file_path) ? $item['game_code'] : $game_platform_id) . $this->jpg_extension;

			}
		} else if ($game_platform_id == GAMEPLAY_API) {

			$img_dir_path = $this->img_dir_path . 'gameplay/';
			$default = $img_dir_path . $game_platform_id . $this->jpg_extension;

			foreach ($list as &$item) {
				$file_path = $img_dir_path . $item['game_code'] . $this->jpg_extension;
				$item['game_name'] = lang($item['game_name']) ?: $item['english_name'];
				$item['game_image'] = site_url();
				$item['game_image'] .= file_exists($this->site_dir . $file_path) ? $file_path : $default;
				$item['image_file'] = (file_exists($this->site_dir . $file_path) ? $item['game_code'] : $game_platform_id) . $this->jpg_extension;

			}

		} else if ($game_platform_id == OPUS_API) {

			$img_dir_path = $this->img_dir_path . 'opus/';
			$default = $img_dir_path . $game_platform_id . $this->jpg_extension;

			foreach ($list as &$item) {
				$file_path = $img_dir_path . $item['game_code'] . $this->jpg_extension;
				$item['game_name'] = lang($item['game_name']) ?: $item['english_name'];
				$item['game_image'] = site_url();
				$item['game_image'] .= file_exists($this->site_dir . $file_path) ? $file_path : $default;
				$item['image_file'] = (file_exists($this->site_dir . $file_path) ? $item['game_code'] : $game_platform_id) . $this->jpg_extension;

			}

		} else if ($game_platform_id == IBC_API) {

			$img_dir_path = $this->img_dir_path . 'ibc/';
			$default = $img_dir_path . $game_platform_id . $this->jpg_extension;

			foreach ($list as &$item) {
				$file_path = $img_dir_path . $item['game_code'] . $this->jpg_extension;
				$item['game_name'] = lang($item['game_name']) ?: $item['english_name'];
				$item['game_image'] = site_url();
				$item['game_image'] .= file_exists($this->site_dir . $file_path) ? $file_path : $default;
				$item['image_file'] = (file_exists($this->site_dir . $file_path) ? $item['game_code'] : $game_platform_id) . $this->jpg_extension;

			}

		} else if ($game_platform_id == HB_API) {

			//$default = $img_dir_path . $game_platform_id . $this->jpg_extension;

			foreach ($list as &$item) {
				//$file_path = $img_dir_path . $item['game_code'] . $this->jpg_extension;
				$item['game_name'] = lang($item['game_name']) ?: $item['english_name'];
				$item['image_file'] = $item['game_code'].'.png';
				$item['game_code'] = $item['external_game_id'];

			}
		} else if ($game_platform_id == QT_API) {
			foreach ($list as &$item) {
				$item['game_name'] = lang($item['game_name']) ?: $item['english_name'];
				$item['image_file'] = $item['game_code'].'.png';
				$item['game_code'] = $item['external_game_id'];
			}
		}else {
			foreach ($list as &$item) {
				$item['game_name'] = lang($item['game_name']) ?: $item['game_name'];
				$item['image_file'] = $item['game_code'] . $this->jpg_extension;
			}
		}
		return $list;
	}

	public function getActiveGameDescriptionCount($game_platform_id, $game_type_id) {
		if ($game_platform_id) {
			$criteria['game_description.game_platform_id'] = $game_platform_id;
		}

		if ($game_type_id) {
			$criteria['game_description.game_type_id'] = $game_type_id;
		}

		# ONLY ACTIVE
		$criteria['game_type.flag_show_in_site'] = 1; # ACTIVE GAME TYPES
		$criteria['game_description.flag_show_in_site'] = 1; # ACTIVE GAMES
		$count = $this->ci->game_description_model->getGameDescriptionCount($criteria);

		return $count;
	}

}