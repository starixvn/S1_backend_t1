<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * IP Manager
 *
 * IP Manager library for FHI OG System
 *
 * @package		IP Manager
 * @author		Johann Merle
 * @version		1.0.0
 */

class Ip_manager {
	private $error = array();

	function __construct() {
		$this->ci = &get_instance();
		// $this->ci->load->library(array(''));
		$this->ci->load->model(array('ip'));
	}

	/**
	 * Check if ip address in ip table is white listed
	 *
	 * @return	bool
	 */
	private $configFile = 'config.xml';
	public function checkIfIpAllowed() {
		$this->ci->load->model(array('operatorglobalsettings'));
		// $configFile = 'config.xml';
		// $configXMLPath = realpath(site_url()) . "../system/" . $configFile;
		// if (!file_exists($configXMLPath)) {
		// 	$this->createConfigXML();
		// }
		// $xml = new DOMDocument();
		// $xml->load(BASEPATH . $configFile);

		// $nodes = $xml->getElementsByTagName("ipList");
		// $node = $nodes->item(0);
		// $ipList = $node->nodeValue;

		// if ($ipList == 'true') {
		// $this->ci->utils->debug_log('ip rules', $this->ci->operatorglobalsettings->getSettingValue('ip_rules'));
		if ($this->ci->operatorglobalsettings->getSettingValue('ip_rules') == 'true') {
			return $this->ci->ip->checkIfIpAllowed();
		}
		return true;
		// } else {
		// 	return TRUE;
		// }
	}

	/**
	 * Create Config XML
	 */
	public function createConfigXML() {
		$xml = new DOMDocument("1.0");

		$root = $xml->createElement("ipManagement");
		$xml->appendChild($root);

		$ipList = $xml->createElement("ipList");
		$ipListVal = $xml->createTextNode('false');
		$ipList->appendChild($ipListVal);

		$root->appendChild($ipList);

		$xml->formatOutput = true;
		echo "<xmp>" . $xml->saveXML() . "</xmp>";

		$xml->save(realpath(site_url()) . "../system/" . $this->configFile) or die("Error");
	}

	/**
	 * Get all ip address in ip table
	 *
	 * @return	array
	 */
	public function getAllIp() {
		return $this->ci->ip->getAllIp();
	}

	/**
	 * Get all ip address in ip table by ipId
	 *
	 * @param   int
	 * @return	array
	 */
	public function getIpById($ip_id) {
		return $this->ci->ip->getIpById($ip_id);
	}

	/**
	 * add ip address in ip table
	 *
	 * @param	array
	 */
	public function addIp($data) {
		$this->ci->ip->addIp($data);
	}

	/**
	 * Check if ip address in ip table exists
	 *
	 * @param   string
	 * @return	bool
	 */
	public function checkIfIpExists($ip_name) {
		return $this->ci->ip->checkIfIpExists($ip_name);
	}

	/**
	 * delete ip address in ip table
	 *
	 * @param	int
	 */
	public function deleteIp($ip_id) {
		return $this->ci->ip->deleteIp($ip_id);
	}

	/**
	 * lock ip address in ip table
	 *
	 * @param	int
	 * @param	array
	 */
	public function lockIp($ip_id, $data) {
		return $this->ci->ip->lockIp($ip_id, $data);
	}

	/**
	 * edit ip address in ip table
	 *
	 * @param	int
	 * @param	array
	 */
	public function editIp($ip_id, $data) {
		return $this->ci->ip->editIp($ip_id, $data);
	}

	/**
	 * get all domain in domain table
	 *
	 * @return	array
	 */
	public function getAllDomain() {
		return $this->ci->ip->getAllDomain();
	}

	/**
	 * add domain in domain table
	 *
	 * @param	array
	 * @return	void
	 */
	public function addDomain($data) {
		return $this->ci->ip->addDomain($data);
	}

	/**
	 * edit domain in domain table
	 *
	 * @param	array
	 * @param	int
	 * @return	void
	 */
	public function editDomain($data, $domain_id) {
		$this->ci->ip->editDomain($data, $domain_id);
	}

	/**
	 * delete domain in domain table
	 *
	 * @param	int
	 * @return	void
	 */
	public function deleteDomain($domain_id) {
		$this->ci->ip->deleteDomain($domain_id);
	}

	/**
	 * get email in email table
	 *
	 * @return	array
	 */
	public function getEmail() {
		return $this->ci->ip->getEmail();
	}

	/**
	 * edit domain in domain table
	 *
	 * @param	array
	 * @param	int
	 * @return	void
	 */
	public function editEmailSettings($data, $email_id) {
		$this->ci->ip->editEmailSettings($data, $email_id);
	}
}

/* End of file ip_manager.php */
/* Location: ./application/libraries/ip_manager.php */