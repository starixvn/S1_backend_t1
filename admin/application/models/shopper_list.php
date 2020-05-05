<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Shopper_list extends BaseModel {

	const REQUEST = 1;
	const APPROVED = 2;
	const DECLINED = 3;

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "shopper_list";

	public function getShopperList($shoppingItemId = null, $status = null, $playerId = null) {
		$this->db->from($this->tableName);
		$this->db->select('
						shopper_list.id,
						shopper_list.shopping_item_id,
						shopper_list.player_username,
						shopper_list.player_id,
						shopper_list.required_points,
						shopper_list.status,
						shopper_list.notes,
						shopper_list.processed_datetime,
						admusr.username as processed_by,
							');
		$this->db->join('adminusers as admusr', 'admusr.userId = shopper_list.processed_by', 'left');
		$shoppingItemId ? $this->db->where('shopper_list.shopping_item_id', $shoppingItemId) : "";
		$status ? $this->db->where('shopper_list.status', $status) : "";
		$playerId ? $this->db->where('shopper_list.player_id', $playerId) : "";
		$query = $this->db->get();
		return $this->getMultipleRowArray($query);
	}

	public function updateShopperData($data, $null1=null, $null2=null, $null3=null) {
		$this->db->set($data);
		$this->db->where("id", $data['id']);
		return $this->runAnyUpdate($this->tableName);
	}

	/**
	 * detail: Will get item details
	 *
	 * @param int $id
	 * @return array
	 */
	public function getItemDetails($id) {
		$this->db->from($this->tableName);
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * detail: activate shopping center item
	 *
	 * @param array $data
	 * @return array
	 */
	public function activateShoppingCenterItem($data) {
		$this->db->where('id', $data['id']);
		$this->db->update($this->tableName, $data);
	}

	/**
	 * detail: Will delete shopping center item
	 *
	 * @param int $id
	 * @return Boolean
	 */
	public function deleteItem($id) {
		$this->db->where('id', $id);
		$this->db->delete($this->tableName);
	}

	public function addRequestToShopperList($data) {
		return $this->insertData($this->tableName, $data);
	}

	public function countAllStatusOfShoppingList() {
		$this->db->select('
				count(sl.id) as cnt,
				sl.status
			');
		$this->db->group_by('sl.status');
		$this->db->from($this->tableName . ' as sl');

		$count = [
			self::REQUEST => 0,
			self::APPROVED => 0,
			self::DECLINED => 0,
		];

		$rows = $this->runMultipleRow();

		if (!empty($rows)) {
			foreach ($rows as $row) {
				$count[$row->status] = $row->cnt;
			}
		}

		return $count;
	}

	/**
	 * overview : get status name
	 *
	 * @param int $status
	 * @return string
	 */
	public function statusToName($status) {
		switch ($status) {
		case self::REQUEST:
			return lang('Request');
			break;

		case self::APPROVED:
			return lang('Approved');
			break;

		case self::DECLINED:
			return lang('Declined');
			break;
		}

		return lang('Unknown');
	}

	public function approveOrDeclinedShopItemClaimRequest($data) {
		$this->db->set($data);
		$this->db->where("id", $data['id']);
		return $this->runAnyUpdateWithResult($this->tableName);
	}

	public function getShoppingItemAvailableSlot($itemId = null) {
		$this->db->select('count(shopper_list.shopping_item_id) as usedItem')->from($this->tableName);
		$this->db->where('shopper_list.status', self::APPROVED);
		$itemId ? $this->db->where('shopper_list.shopping_item_id', $itemId) : "";
		$query = $this->db->get();
		return $this->getOneRowArray($query);
	}

	public function isplayerItemRequestExists($itemId, $playerId) {
		$this->db->select('id')->from($this->tableName . ' as sl');
		$this->db->where("sl.player_id", $playerId);
		$this->db->where("sl.shopping_item_id", $itemId);
		$this->db->where("sl.status", self::REQUEST);
		$res = $this->runOneRowArray();
		return $res;
		// var_dump($res);exit();
	}
}
