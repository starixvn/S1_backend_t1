<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Salt {
	// var $key;
	// function DES($key) {
	//     $this->key = $key;
	//  }

	function __construct() {
		$this->ci = &get_instance();
	}

	public function encrypt($input, $key) {
		if(empty($input)){
			return '';
		}

		if ($this->ci->utils->getConfig('salt_only_8_keys')) {
			$key = substr($key, 0, 8);
		}

		$size = @mcrypt_get_block_size('des', 'ecb');
		$input = $this->pkcs5_pad($input, $size);
		$td = @mcrypt_module_open('des', '', 'ecb', '');
		$iv = @mcrypt_create_iv(@mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		@mcrypt_generic_init($td, $key, $iv);
		$data = @mcrypt_generic($td, $input);
		@mcrypt_generic_deinit($td);
		@mcrypt_module_close($td);
		$data = base64_encode($data);
		return preg_replace("/\s*/", '', $data);
	}

	public function decrypt($encrypted, $key) {
		if(empty($encrypted)){
			return '';
		}

		if ($this->ci->utils->getConfig('salt_only_8_keys')) {
			$key = substr($key, 0, 8);
		}

		$encrypted = base64_decode($encrypted);
		$td = @mcrypt_module_open('des', '', 'ecb', '');
		//MCRYPT_DES,cbc mode
		$iv = @mcrypt_create_iv(@mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		$ks = @mcrypt_enc_get_key_size($td);
		@mcrypt_generic_init($td, $key, $iv);
		//Initiation
		$decrypted = @mdecrypt_generic($td, $encrypted);
		//Decrypt
		@mcrypt_generic_deinit($td);
		//End
		@mcrypt_module_close($td);
		$y = $this->pkcs5_unpad($decrypted);
		return $y;
	}

	public function pkcs5_pad($text, $blocksize) {
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}

	public function pkcs5_unpad($text) {
		$pad = ord($text{strlen($text) - 1});
		if ($pad > strlen($text)) {
			return false;
		}

		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
			return false;
		}

		return substr($text, 0, -1 * $pad);
	}
}
