<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General Behavior
 * * Gets IP by status
 * * Gets IP by ip id
 * * Checks IP if exists
 * * Checks IP if allowed
 * * Add/Edit/Delete/Lock IP
 * * Gets IP Details
 * * Gets All Domain
 * * Add/Edit/Delete Domain
 * * Gets email
 * * Edit Email Settings
 *
 * @category System Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Ip extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	const STATUS_BLOCK = '1';
	const STATUS_ALLOW = '0';
	const STATUS_DELETE = '2';

	const MAX_COMPATIBILITY='max_compatibility';
	const MIN_COMPATIBILITY='min_compatibility';

	/**
	 * overview : get all ip
	 *
	 * detail : Get all ip address in ip table
	 * @return	array
	 */
	function getAllIp() {

		$query = $this->db->query("SELECT i.*, au.realname FROM ip as i inner join adminusers as au on au.userId = i.createPerson ORDER BY ipId ASC");

		return $query->result_array();
	}

	/**
	 * overview : get ip by status
	 *
	 * detail : Get all ip address in ip table by status
	 * @param	int
	 * @return	array
	 */
	function getIpByStatus($status) {

		$sql = "SELECT i.*, au.realname FROM ip as i inner join adminusers as au on au.userId = i.createPerson where i.status = ?";

		$query = $this->db->query($sql, array($status));

		return $query->result_array();
	}

	/**
	 * overview : get ip by id
	 *
	 * detail : Get all ip address in ip table by ipId
	 * @param   int
	 * @return	array
	 */
	function getIpById($ip_id) {

		$sql = "SELECT * FROM ip where ipId = ? ORDER BY ipId DESC";

		$query = $this->db->query($sql, array($ip_id));

		return $query->row_array();
	}

	/**
	 * overview : Check if ip address in ip table exists
	 *
	 * @param   string
	 * @return	bool
	 */
	function checkIfIpExists($ip_name) {

		$sql = "SELECT * FROM ip where ipName = ?";

		$query = $this->db->query($sql, array($ip_name));

		$result = $query->row_array();

		if (!$result) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : check if IP allowed
	 *
	 * detail : Check if ip address in ip table is white listed
	 * @return	bool
	 */
	function checkIfIpAllowed() {

		$ip_name = $this->input->ip_address();
		$this->utils->debug_log('checkIfIpAllowed check ip', $ip_name);
		if(!empty($ip_name)){
			$sql = "SELECT * FROM ip where ipName = ? and status = ?";

			$query = $this->db->query($sql, array($ip_name, self::STATUS_ALLOW));

			$result = $query->row_array();
			if (!$result) {

				//check ip mask again
				$qry=$this->db->select('ipName')->from('ip')->where('ipName like "%/%"', null, false)->where('status',self::STATUS_ALLOW)->get()->result_array();
				if(!empty($qry)){
					foreach ($qry as $row) {
						$allowedIp=$row['ipName'];

						if($this->utils->compareIP($ip_name, $allowedIp)){
							return true;
						}
					}
				}

				return false;
			// } else {
			// 	return true;
			}
		}

		return true;
	}

	/**
	 * overview : add ip
	 *
	 * detail : add ip address in ip table
	 * @param	array 	$data
	 * @return	void
	 */
	function addIp($data) {
		$this->db->insert('ip', $data);
	}

	/**
	 * overview : delete ip
	 *
	 * detail : delete ip address in ip table
	 * @param	int 	$ip_id
	 * @return	void
	 */
	function deleteIp($ip_id) {
		$this->db->delete('ip', array('ipId' => $ip_id));
	}

	/**
	 * overview : lock ip address in ip table
	 *
	 * @param	int 	$ip_id
	 * @param	array 	$data
	 * @return	void
	 */
	function lockIp($ip_id, $data) {
		$this->db->where('ipId', $ip_id);
		$this->db->update('ip', $data);
	}

	/**
	 * overview edit ip address in ip table
	 *
	 * @param	int 	$ip_id
	 * @param	array 	$data
	 * @return	void
	 */
	function editIp($ip_id, $data) {
		$this->db->where('ipId', $ip_id);
		$this->db->update('ip', $data);
	}
	/**
	 * overview : get ip details
	 *
	 * @param  int 		$id
	 * @return array
	 */
	function getIPDetails($id) {
		$this->db->select('*')->from('ip');
		$this->db->where('ipId', $id);
		$qry = $this->db->get();
		return $qry->row_array();
	}

	const DOMAIN_STATUS_ENABLED = '0';
	const DOMAIN_STATUS_DISABLED = '1';
	/**
	 * overview : edit domain in domain table
	 *
	 * @return	array
	 */
	public function getAllDomain() {
		$query = $this->db
			->select(array('domain.*','COUNT(affiliate_domain.domainId) affiliates'))
			->join('affiliate_domain','affiliate_domain.domainId = domain.domainId','left')
			# ->where('status', self::DOMAIN_STATUS_ENABLED) # does not show deactivated domains on domain list
			->group_by('domain.domainId')
			->get('domain');
		return $query->result_array();
	}

	/**
	 * overview : add domain in domain table
	 *
	 * @param	array 	$data
	 * @return	void
	 */
	public function addDomain($data) {
		$this->db->insert('domain', $data);
		return $this->db->insert_id();
	}

	/**
	 * overview : edit domain in domain table
	 *
	 * @param	array 	$data
	 * @param	int 	$domain_id
	 * @return	void
	 */
	public function editDomain($data, $domain_id) {
		$this->db->where('domainId', $domain_id);
		$this->db->update('domain', $data);
	}

	/**
	 * overview : delete domain in domain table
	 *
	 * @param	int 	$domain_id
	 * @return	void
	 */
	public function deleteDomain($domain_id) {
		$this->db->delete('domain', array('domainId' => $domain_id));
	}

	/**
	 * overview : get email in email table
	 *
	 * @return	array
	 */
	public function getEmail() {
		$query = $this->db->query("SELECT * FROM email");

		return $query->row_array();
	}

	/**
	 * overview : edit domain in domain table
	 *
	 * @param	array 	$data
	 * @param	int 	$email_id
	 * @return	void
	 */
	public function editEmailSettings($data, $email_id) {
		$this->db->where('emailId', $email_id);
		$this->db->update('email', $data);
	}

	function checkIfIpAllowedForAdmin() {
		$exists=false;
		$admin_white_ip_list_mode=$this->utils->getConfig('admin_white_ip_list_mode');
		if($admin_white_ip_list_mode==self::MAX_COMPATIBILITY){
			//get ip
			$ip = $this->input->ip_address();
			if(!empty($ip)){
				$exists=$this->searchWhiteIP($ip);
				$this->utils->debug_log('MAX_COMPATIBILITY ip in searchWhiteIP', $ip, $exists);
			}
		}else{
			//check remote addr
			$ip=$this->input->getRemoteAddr();
			$this->utils->debug_log('MIN_COMPATIBILITY ip getRemoteAddr', $ip);
			if($this->isTrustedIP($ip)){
				//check forward for
				$ipList=$this->input->getIpListFromXForwardedFor();
				if(!empty($ipList)){
					foreach ($ipList as $ip) {
						$exists=$this->searchWhiteIP($ip);
						if(!$exists){
							//not in white ip means failed
							$this->utils->debug_log('not exist ip in getIpListFromXForwardedFor', $ip);
							break;
						}
					}
				}
				if($exists){
					$this->utils->debug_log('exist ip in getIpListFromXForwardedFor', $ipList, $exists);
				}
			}else{
				//if it's not pod
				$exists=$this->searchWhiteIP($ip);
				$this->utils->debug_log('ip not in trusted ip', $ip, $exists);
			}
		}

		return $exists;
	}

	public function isTrustedIP($ip){
		$ip=trim($ip);
		return strpos($ip, '10.') === 0 || $this->isCDNOrProxy($ip);
	}

	public function isCDNOrProxy($ip){
		return $this->isDefaultWhiteIP($ip);
	}

	public function searchWhiteIP($ip){
		$exists=false;
		$ip=trim($ip);
		if(!empty($ip)){
			$exists=$this->isDefaultWhiteIP($ip);
			if($exists){
				return $exists;
			}
			$this->db->select('ipName')->from('ip')->where('ipName', $ip)->where('status', self::STATUS_ALLOW);
			$exists=$this->runExistsResult();

			if (!$exists) {
				//check ip mask again
				$this->db->select('ipName')->from('ip')->where('ipName like "%/%"', null, false)
				    ->where('status',self::STATUS_ALLOW);
				$rows=$this->runMultipleRowArray();
				if(!empty($rows)){
					foreach ($rows as $row) {
						$allowedIp=$row['ipName'];
						$exists=$this->utils->compareIP($ip, $allowedIp);
						if($exists){
							return $exists;
						}
					}
				}
			}
		}
		return $exists;
	}

	public function isDefaultWhiteIP($ip){
		$exists=false;
		$default_white_ip_list=$this->utils->getConfig('default_white_ip_list');
		if(!empty($default_white_ip_list)){
			foreach ($default_white_ip_list as $allowedIp) {
				$exists=$this->utils->compareIP($ip, $allowedIp);
				if($exists){
					$this->utils->debug_log('exist ip in default_white_ip_list', $ip, $allowedIp);
					return $exists;
				}
			}
		}

		return $exists;
	}

	/**
	 * checkWhiteIpListForAdmin
	 * @param  callable $searchFunc search white ip
	 * @param  mixin   &$payload
	 * @return boolean $exists
	 */
	public function checkWhiteIpListForAdmin(callable $searchFunc, &$payload) {
		$exists=false;
		$admin_white_ip_list_mode=$this->utils->getConfig('admin_white_ip_list_mode');
		if($admin_white_ip_list_mode==self::MAX_COMPATIBILITY){
			//get ip
			$ip = $this->input->ip_address();
			if($ip=='0.0.0.0'){
				$ip=$this->input->getRemoteAddr();
			}
			$exists=$searchFunc($ip, $payload);
			$this->utils->debug_log('MAX_COMPATIBILITY search ip', $ip, $exists);
		}else{
			//check remote addr
			$ip=$this->input->getRemoteAddr();
			$this->utils->debug_log('MIN_COMPATIBILITY ip getRemoteAddr', $ip);
			if($this->isTrustedIP($ip)){
				//check forward for
				$ipList=$this->input->getIpListFromXForwardedFor();
				if(!empty($ipList)){
					foreach ($ipList as $ip) {
						$exists=$searchFunc($ip, $payload);
						$this->utils->debug_log('search ip', $ip, $exists);
						if(!$exists){
							//not in white ip means failed
							$this->utils->debug_log('not exist ip in getIpListFromXForwardedFor', $ip);
							break;
						}
					}
				}
				if($exists){
					$this->utils->debug_log('exist ip in getIpListFromXForwardedFor', $ipList, $exists);
				}
			}else{
				//if it's not pod
				$exists=$searchFunc($ip, $payload);
				$this->utils->debug_log('ip not in trusted ip', $ip, $exists);
			}
		}

		return $exists;
	}

}

/* End of file ip.php */
/* Location: ./application/models/ip.php */