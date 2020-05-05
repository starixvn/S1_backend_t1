<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Language function
 *
 * Language function library for FHI OG System
 *
 * @package		Language function
 * @author		ASRII
 * @version		1.0.0
 */

class Language_function {
	private $error = array();

	const INT_LANG_ENGLISH = '1';
	const INT_LANG_CHINESE = '2';
	const INT_LANG_INDONESIAN = '3';
	const INT_LANG_VIETNAMESE = '4';
	const INT_LANG_KOREAN = '5';
	const INT_LANG_THAI = '6';

	const PROMO_SHORT_LANG_ENGLISH = 'en';
	const PROMO_SHORT_LANG_CHINESE = 'ch';
	const PROMO_SHORT_LANG_INDONESIAN = 'id';
	const PROMO_SHORT_LANG_VIETNAMESE = 'vn';
	const PROMO_SHORT_LANG_THAI = 'th';
	const PLAYER_LANG_KOREAN = 'Korean';

	const ISO2_LANG=[
		self::INT_LANG_ENGLISH=>'en',
		self::INT_LANG_CHINESE=>'cn',
		self::INT_LANG_INDONESIAN=>'id',
		self::INT_LANG_VIETNAMESE=>'vt',
		self::INT_LANG_KOREAN=>'kr',
		self::INT_LANG_THAI=>'th',
	];

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('session'));
		// $this->ci->load->model(array());
	}

	/**
	 * get language code
	 *
	 * @return	rendered Template
	 */
	function getLanguageCode($lang) {
		switch ($lang) {
		default:
			return 'main';
			break;
		}
	}

	/**
	 * get language
	 *
	 * @return	rendered Template
	 */
	function getLanguage($lang) {
		switch ($lang) {
		case 1:
			return 'english';
			break;
		case 2:
			return 'chinese';
			break;
		case 3:
			return 'indonesian';
			break;
		case 4:
			return 'vietnamese';
			break;
		case 5:
			return 'korean';
			break;
		case 6:
			return 'thai';
			break;
		default:
			# code...
			break;
		}
	}

	/**
	 * get current language
	 *
	 * @param	int
	 * @return 	array
	 */
	function getCurrentLanguage() {
		$language = $this->ci->session->userdata('agency_lang');

		if (empty($language)) {
			$type = $this->ci->config->item('default_agency_login_language');

			switch ($type) {
				case 'english':
					return self::INT_LANG_ENGLISH;
					break;
				case 'chinese':
					return self::INT_LANG_CHINESE;
					break;
				case 'indonesian':
					return self::INT_LANG_INDONESIAN;
					break;
				case 'vietnamese':
					return self::INT_LANG_VIETNAMESE;
					break;
				case 'korean':
					return self::INT_LANG_KOREAN;
					break;
				case 'thai':
					return self::INT_LANG_THAI;
					break;
				default:
					return self::INT_LANG_ENGLISH;
					break;
			}
		}

		return $language;
	}

	/**
	 * get current language
	 *
	 * @param	data
	 * @return 	array
	 */
	function setCurrentLanguage($data) {
		$this->ci->session->set_userdata('agency_lang', $data);
	}

	function getCurrentLanguageName() {
		return $this->getLanguage($this->getCurrentLanguage());
	}
}

/* End of file language_function.php */
/* Location: ./application/libraries/language_function.php */
