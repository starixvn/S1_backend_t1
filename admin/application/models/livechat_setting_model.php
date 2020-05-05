<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

class livechat_setting_model extends BaseModel {

	protected $tableName = 'livechat_setting';

	public function __construct() {
		parent::__construct();
	}

	/**
	 * overview : will get livechat setting
	 *
	 * @return 	array
	 */
	public function getLivechatSetting() {

		$this->db->select('*');
		$this->db->from($this->tableName);

		$query = $this->db->get();

		return $query->result_array();
	}

	public function saveLivechatSetting($data, $id) {
		$this->db->where('livechatSettingName', $id);
		$this->db->update('livechat_setting', $data);
	}

	public function getMaxTip($livechatSettingName) {
		$sql = "SELECT livechatData FROM livechat_setting where livechatSettingName = ? ";

		$query = $this->db->query($sql, array($livechatSettingName));

		return $result = $query->row_array();
	}

}
