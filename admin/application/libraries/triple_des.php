<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}
# Provided by: AllBet API
class Triple_des {

	private function pkcs5Pad($text, $blocksize) {
	    $pad = $blocksize - (strlen($text) % $blocksize);
	    return $text . str_repeat(chr($pad), $pad);
	}

	private function pkcs5Unpad($text) {
	    $pad = ord($text{strlen($text)-1});
	    if ($pad > strlen($text)) return false;
	    if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
	    return substr($text, 0, -1 * $pad);
	}

	public function encryptText($plain_text, $key) {
	    $padded = $this->pkcs5Pad($plain_text, @mcrypt_get_block_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_CBC));
		return @mcrypt_encrypt(MCRYPT_TRIPLEDES, base64_decode($key), $padded, MCRYPT_MODE_CBC, base64_decode("AAAAAAAAAAA="));
	}

	public function decryptText($cipher_text, $key) {
	    $plain_text = @mcrypt_decrypt(MCRYPT_TRIPLEDES, base64_decode($key), $cipher_text, MCRYPT_MODE_CBC, base64_decode("AAAAAAAAAAA="));
	    return $this->pkcs5Unpad($plain_text);
	}

};