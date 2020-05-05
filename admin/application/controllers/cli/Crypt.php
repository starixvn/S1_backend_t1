<?php
//!defined('IN_ZMH') && exit('Error');

/**
 * 加密类
 *
 * @example
 *   Crypy::encode('ok','123');  //加密
 *   Crypy::decode('ok','123');  //解密  必须用相同的密钥
 *
 */

class Crypt {

	/**
	 * 加密或解密
	 *
	 * @param  $string  明文或密文
	 * @param  $operation  类型  DECODE-解密  为空则为加密
	 * @param  $key   密钥
	 * @param  $expiry  有效时间
	 * @return  string
	 */
	private static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

		$ckey_length = 4;

		$key = md5($key ? $key : 'zmh');

		$keya = md5(substr($key, 0, 16));

		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) :
			substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya . md5($keya . $keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
		sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
		$string_length = strlen($string);
		$result = '';
		$box = range(0, 255);
		$rndkey = array();

		for ($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for ($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for ($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;

			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		if ($operation == 'DECODE') {
			if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
				substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc . str_replace('=', '', base64_encode($result));
		}
	}

	/**
	 * 加密字符串
	 *
	 * @param string $str  字符串
	 * @param  $key  密钥
	 * @return string
	 */
	public static function encode($str, $key = '') {
		return self::authcode($str, '', $key, '');

	}

	/**
	 * 解密字符串
	 *
	 * @param string $str  密文
	 * @param  $key  密钥
	 * @return string
	 */
	public static function decode($code, $key = '') {
		return self::authcode($code, 'DECODE', $key, '');
	}

}
/// END OF FILE///