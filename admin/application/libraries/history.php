<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * History
 *
 * @package		History
 * @author		Raihana Dandamun
 * @version		1.0.0
 */

class History {

	private $name = array();
	private $address = array();

	function __construct() {
		$this->ci =& get_instance();
		$this->ci->load->library(array('session'));
	}

	//get function
	public function getHistory() {
		$data = array(
			'name' => $this->ci->session->userdata('hist_name'),
			'address' => $this->ci->session->userdata('hist_address')
		);
		return $data;
	}

	//set function
	public function setHistory($title,$link) {
		$this->setVariable();

		$result = $this->check($title);

		if($result) {
			$data = $this->getHistory();

			$explodedName = explode(",", $data['name']);
			$explodedAddress = explode(",", $data['address']);

			// unsets the existing title/link from the array name and address
			unset($this->name[array_search($title, $explodedName)]);
			unset($this->address[array_search($link, $explodedAddress)]);
		}
		
		array_push($this->name, $title);
		array_push($this->address, $link);

		$this->ci->session->set_userdata('hist_name', implode(',', $this->name));
		$this->ci->session->set_userdata('hist_address', implode(',', $this->address));
	}

	public function setVariable() {
		$name = $this->ci->session->userdata('hist_name');
		$address = $this->ci->session->userdata('hist_address');

		if (!empty($name)) {
			$this->name = explode(',', $name);
			$this->address = explode(',', $address);
		}

	}

	//checking function
	public function check($title) {
		if($this->name == null)
			return false;

		if (in_array($title, $this->name)) {
			return true;
		} else {
			return false;
		}
	}

	//delete function
	public function delete($key) {
		$title = '';
		$link = '';
		$data = $this->getHistory();

		$explodedName = explode(",", $data['name']);
		$explodedAddress = explode(",", $data['address']);

		foreach ($explodedName as $row => $value) {
			$title = $explodedName[$key];
			$link = $explodedAddress[$key];
		}
		unset($this->name[array_search($title, $explodedName)]);
		unset($this->address[array_search($link, $explodedAddress)]);

		$this->ci->session->set_userdata('hist_name', implode(',', $this->name));
		$this->ci->session->set_userdata('hist_address', implode(',', $this->address));
	}
	
}