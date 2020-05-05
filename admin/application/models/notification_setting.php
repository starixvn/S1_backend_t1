<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

class Notification_setting extends BaseModel {

	protected $tableName = 'notification_setting';

	public function __construct() {
		parent::__construct();
	}

	public function get( $id ){

		return $this->db->where('notification_type', $id)
						->get($this->tableName)
						->row();

	}

	public function insert( $data ){

		return $this->db->insert( $this->tableName, $data );

	}

	public function update( $id, $data ){

		return $this->db->where('notification_type', $id)
						->update($this->tableName, $data);

	}

	public function delete( $id ){

		return $this->db->where('id', $id)
						->delete($this->tableName);

	}

	public function getAll(){

		return $this->db->get($this->tableName)
						->result_array();

	}

	public function remove( $id ){

		return $this->db->where('notification_type', $id)
						->delete($this->tableName);

	}

	public function getNotification(){

		return $this->db->select('notification_setting.notification_type, notifications.file, notifications.id')
						->from($this->tableName)
						->where('notification_setting.notification_type >', 0)
						->join('notifications', 'notifications.id = notification_setting.notification_id')
						->get()
						->result_array();

	}

}

/* End of file notifications.php */
/* Location: ./application/models/notification_setting.php */