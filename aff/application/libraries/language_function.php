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

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('session'));
	}

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
	const PROMO_SHORT_LANG_KOREAN = 'kr';
	const PROMO_SHORT_LANG_THAI = 'th';

	const PLAYER_LANG_ENGLISH = 'English';
	const PLAYER_LANG_CHINESE = 'Chinese';
	const PLAYER_LANG_INDONESIAN = 'Indonesian';
	const PLAYER_LANG_VIETNAMESE = 'Vietnamese';
	const PLAYER_LANG_KOREAN = 'Korean';
	const PLAYER_LANG_THAI = 'Thai';

    const ISO2_LANG=[
        self::INT_LANG_ENGLISH=>'en',
        self::INT_LANG_CHINESE=>'cn',
        self::INT_LANG_INDONESIAN=>'id',
        self::INT_LANG_VIETNAMESE=>'vt',
        self::INT_LANG_KOREAN=>'kr',
        self::INT_LANG_THAI=>'th',
    ];

	/**
	 * get current language
	 *
	 * @param	data
	 * @return 	array
	 */
	function setCurrentLanguage($data) {
		$this->ci->session->set_userdata('afflang', $data);
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
	 * get language
	 *
	 * @return	rendered Template
	 */
	function getIntLanguage($lang) {
		switch (strtolower($lang)) {
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

	/**
	 * get current language
	 *
	 * @param	int
	 * @return 	array
	 */
	function getCurrentLanguage() {
		$language = $this->ci->session->userdata('afflang');

		if (empty($language)) {
			$type = $this->ci->config->item('default_lang');
			switch ($type) {
				case 'english':
					$language = 1;
					break;
				case 'chinese':
					$language = 2;
					break;
				case 'indonesian':
					$language = 3;
					break;
				case 'vietnamese':
					$language = 4;
					break;
				case 'korean':
					$language = 5;
				case 'thai':
					$language = 6;
					break;
				default:
					$language = 1;
					break;
			}
		}

		return $language;
	}

	function getCurrentLanguageName() {
		return $this->getLanguage($this->getCurrentLanguage());
	}

	function getCurrentLangForPromo() {
		$currLang = $this->getCurrentLanguage();
		switch ($currLang) {
			case self::INT_LANG_ENGLISH:
				$language = self::PROMO_SHORT_LANG_ENGLISH;
				break;
			case self::INT_LANG_CHINESE:
				$language = self::PROMO_SHORT_LANG_CHINESE;
				break;
			case self::INT_LANG_INDONESIAN:
				$language = self::PROMO_SHORT_LANG_INDONESIAN;
				break;
			case self::INT_LANG_VIETNAMESE:
				$language = self::PROMO_SHORT_LANG_VIETNAMESE;
				break;
			case self::INT_LANG_KOREAN:
				$language = self::PROMO_SHORT_LANG_KOREAN;
				break;
			case self::INT_LANG_THAI:
				$language = self::PROMO_SHORT_LANG_THAI;
				break;
			default:
				$language = self::PROMO_SHORT_LANG_CHINESE;
				break;
		}
		return $language;
	}

	function langStrToInt($langStr) {
		switch (strtolower($langStr)) {
			case 'english':
			case 'en':
				return '1';
				break;
			case 'chinese':
			case 'zh-cn':
				return '2';
				break;
			case 'indonesian':
			case 'idn':
			case 'in':
				return '3';
				break;
			case 'vietnamese':
			case 'vn':
				return '4';
				break;
			case 'korean':
			case 'kr':
				return '5';
				break;
			case 'thai':
			case 'th':
				return '6';
				break;
			default:
				# code...
				break;
		}
	}
}

/* End of file language_function.php */
/* Location: ./application/libraries/language_function.php */
