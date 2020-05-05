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
	
}