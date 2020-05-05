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
		$this->ci->load->model(array('users'));
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

	const DATE_PICKER_LANG_ENGLISH = 'en-US';
	const DATE_PICKER_LANG_CHINESE = 'zh-CN';
	const DATE_PICKER_LANG_INDONESIAN = 'id';
	const DATE_PICKER_LANG_VIETNAMESE = 'vi';
	const DATE_PICKER_LANG_KOREAN = 'ko';
	const DATE_PICKER_LANG_THAI = 'th';

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
	 * @param	int
	 * @return 	array
	 */
	function setCurrentLanguage($language) {
		$this->ci->session->set_userdata('login_lan', $language); //$this->ci->player->setCurrentLanguage($data);
		$this->ci->config->set_item('language', $this->getLanguage($language));
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
				return 'english';
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
		$language = $this->ci->session->userdata('login_lan');

		if (empty($language)) {
			$default_admin_login_language=$this->getIntLanguage($this->ci->utils->getConfig('default_admin_login_language'));

			$this->ci->session->set_userdata('login_lan', $default_admin_login_language);
			$language = $default_admin_login_language;
		}

		return $language;
	}

	function getCurrentLanguageName() {
		return $this->getLanguage($this->getCurrentLanguage());
	}

	function getCurrentLangForPromo($isKeyword=false,$lang=false) {
		$currLang = $lang ?: $this->getCurrentLanguage();

		switch ($currLang) {
			case self::INT_LANG_CHINESE:
				$language = self::PROMO_SHORT_LANG_CHINESE;
				break;
			case self::INT_LANG_ENGLISH:
				$language = self::PROMO_SHORT_LANG_ENGLISH;
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
				$language = self::PROMO_SHORT_LANG_ENGLISH;
				break;
		}
		return $isKeyword ? $language : $currLang;
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
		}
	}

	#-------------only used in admin-------------
	function getAllSystemLanguages(){
		return array("languages"=>
			array("key"=>self::INT_LANG_ENGLISH,
				  "word"=>$this->getLanguage(self::INT_LANG_ENGLISH),
				  "short_code"=>self::PROMO_SHORT_LANG_ENGLISH
				),
			array("key"=>self::INT_LANG_CHINESE,
				  "word"=>$this->getLanguage(self::INT_LANG_CHINESE),
				  "short_code"=>self::PROMO_SHORT_LANG_CHINESE
				),
			array("key"=>self::INT_LANG_INDONESIAN,
				  "word"=>$this->getLanguage(self::INT_LANG_INDONESIAN),
				  "short_code"=>self::PROMO_SHORT_LANG_INDONESIAN
				),
			array("key"=>self::INT_LANG_VIETNAMESE,
				  "word"=>$this->getLanguage(self::INT_LANG_VIETNAMESE),
				  "short_code"=>self::PROMO_SHORT_LANG_VIETNAMESE
				),
			array("key"=>self::INT_LANG_KOREAN,
				  "word"=>$this->getLanguage(self::INT_LANG_KOREAN),
				  "short_code"=>self::PROMO_SHORT_LANG_KOREAN
				),
			array("key"=>self::INT_LANG_THAI,
				  "word"=>$this->getLanguage(self::INT_LANG_THAI),
				  "short_code"=>self::PROMO_SHORT_LANG_THAI
				)
		);
	}

	/**
	 * get language id from short_code
	 *
	 * @return	rendered Template
	 */
	function getPromoLanguageIdFromShortCode($lang) {
		switch ($lang) {
			case self::PROMO_SHORT_LANG_ENGLISH:
				return self::INT_LANG_ENGLISH;
				break;
			case self::PROMO_SHORT_LANG_CHINESE:
				return self::INT_LANG_CHINESE;
				break;
			case self::PROMO_SHORT_LANG_INDONESIAN:
				return self::INT_LANG_INDONESIAN;
				break;
			case self::PROMO_SHORT_LANG_VIETNAMESE:
				return self::INT_LANG_VIETNAMESE;
				break;
			case self::PROMO_SHORT_LANG_KOREAN:
				return self::INT_LANG_KOREAN;
				break;
			case self::PROMO_SHORT_LANG_THAI:
				return self::INT_LANG_THAI;
				break;
			default:
				return self::INT_LANG_ENGLISH;
				break;
		}
	}

	function convertToDatePickerLang($lang) {

		switch ($lang) {
			case self::INT_LANG_CHINESE:
				$datePickerLang = self::DATE_PICKER_LANG_CHINESE;
				break;
			case self::INT_LANG_ENGLISH:
				$datePickerLang = self::DATE_PICKER_LANG_ENGLISH;
				break;
			case self::INT_LANG_INDONESIAN:
				$datePickerLang = self::DATE_PICKER_LANG_INDONESIAN;
				break;
			case self::INT_LANG_VIETNAMESE:
				$datePickerLang = self::DATE_PICKER_LANG_VIETNAMESE;
				break;
			case self::INT_LANG_KOREAN:
				$datePickerLang = self::DATE_PICKER_LANG_KOREAN;
				break;
			case self::INT_LANG_THAI:
				$datePickerLang = self::DATE_PICKER_LANG_THAI;
				break;
			default:
				$datePickerLang = self::DATE_PICKER_LANG_ENGLISH;
				break;
		}

		return $datePickerLang;
	}

	function getAllSystemLangLocalWord(){
		return array("languages"=>
			array("key"=>self::INT_LANG_ENGLISH,
				  "word"=>$this->langToLocalWord(self::INT_LANG_ENGLISH),
				  "short_code"=>self::PROMO_SHORT_LANG_ENGLISH
				),
			array("key"=>self::INT_LANG_CHINESE,
				  "word"=>$this->langToLocalWord(self::INT_LANG_CHINESE),
				  "short_code"=>self::PROMO_SHORT_LANG_CHINESE
				),
			array("key"=>self::INT_LANG_INDONESIAN,
				  "word"=>$this->langToLocalWord(self::INT_LANG_INDONESIAN),
				  "short_code"=>self::PROMO_SHORT_LANG_INDONESIAN
				),
			array("key"=>self::INT_LANG_VIETNAMESE,
				  "word"=>$this->langToLocalWord(self::INT_LANG_VIETNAMESE),
				  "short_code"=>self::PROMO_SHORT_LANG_VIETNAMESE
				),
			array("key"=>self::INT_LANG_KOREAN,
				  "word"=>$this->langToLocalWord(self::INT_LANG_KOREAN),
				  "short_code"=>self::PROMO_SHORT_LANG_KOREAN
				),
			array("key"=>self::INT_LANG_THAI,
				  "word"=>$this->langToLocalWord(self::INT_LANG_THAI),
				  "short_code"=>self::PROMO_SHORT_LANG_THAI
				)
		);
	}

	function langToLocalWord($langStr) {
		switch (strtolower($langStr)) {
			case 'english':
			case 'en':
			case '1':
				return 'English';
				break;
			case 'chinese':
			case 'zh-cn':
			case '2':
				return '中文';
				break;
			case 'indonesian':
			case 'idn':
			case '3':
				return 'Indonesian';
				break;
			case 'vietnamese':
			case 'vn':
			case '4':
				return 'Vietnamese';
				break;
			case 'korean':
			case 'kr':
			case '5':
				return 'Korean';
				break;
			case 'thai':
			case 'th':
			case '6':
				return 'Thai';
				break;
			default:
				return 'English';
				break;
		}
	}
}

/* End of file language_function.php */
/* Location: ./application/libraries/language_function.php */
