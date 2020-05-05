<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * OG Utility
 *
 * writeToLog - Writes text file in the path provided
 * makedirs - create directory
 *
 * @package		OG Utility
 * @author		ASRII
 * @version		1.0.0
 */

class Og_utility {

	function __construct() {
		$this->ci =& get_instance();
	}

	/**
	 * write logs
	 *
	 * @param 	$logs str
	 * @param 	$path str
	 * @param 	$fileName str
	 * @return	null
	 */
	public function writeToLog($logs,$path,$fileName) {
        $path = realpath($path);
        $file = $path . '/' .$fileName.'.txt';
        $content = PHP_EOL . $logs . ";";
        file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
    }

	/**
	 * make directory
	 *
	 * @param 	dirpath str
	 * @param 	mode int
	 * @return	boolean
	 */
	function makedirs($dirpath, $mode=0777) {
        return is_dir($dirpath) || mkdir($dirpath, $mode, true);//Recursively create directory path
    }


	/**
	 * Pluck an array of values from an array. (Only for PHP 5.3+)
	 *
	 * @param  $array - data
	 * @param  $key - value you want to pluck from array
	 *
	 * @return plucked array only with key data
	 *
	 * Ref. to https://gist.github.com/ozh/82a17c2be636a2b1c58b49f271954071
	 */
	function array_pluck($array, $key) {
		return array_map(function($v) use ($key) {
		return is_object($v) ? $v->$key : $v[$key];
		}, $array);
	}
}