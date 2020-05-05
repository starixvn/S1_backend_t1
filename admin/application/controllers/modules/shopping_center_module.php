<?php

/**
 * Class shopping_center_module
 *
 * General behaviors include
 *
 * * Add/update/delete shopping center
 * * Activate/deactivate shopping center
 * * Export report to excel
 *
 * @category Marketing Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 *
 */
trait shopping_center_module {
	/**
	 * overview : shopping center items list
	 *
	 * @return  redered template
	 */
	public function shoppingCenterItemList() {
		if (!$this->permissions->checkPermissions('shopping_center_manager')) {
			$this->error_access();
		} else {
			$this->load->model(array('shopping_center', 'shopper_list'));

			$data['shoppingItemList'] = $this->shopping_center->getData();
	
			foreach ($data['shoppingItemList'] as $key => $value) {
				$usedItem = count($this->shopper_list->getShopperList($value['id'], Shopper_list::APPROVED));
				$totalItem = $value['how_many_available'];
				$available  = (int) $totalItem - (int) $usedItem;
				$data['shoppingItemList'][$key]['how_many_available'] = $available . " " . lang("left out of") . " " . $totalItem;
			}
			
			//export report permission checking
			if (!$this->permissions->checkPermissions('export_report')) {
				$data['export_report_permission'] = FALSE;
			} else {
				$data['export_report_permission'] = TRUE;
			}

			$this->loadTemplate('Marketing Management', '', '', 'shopping center');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');
			$this->template->add_css('resources/css/shopping_center/shopping_center.css');
			$this->template->write_view('main_content', 'shopping_center/shopping_center_list', $data);
			$this->template->render();
		}
	}

	/**
	 * add new shopping
	 *
	 * @return  rendered template
	 */
	public function addNewshoppingItem() {
		$this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
		$this->form_validation->set_rules('short_description', 'Short Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('item_details', 'Details', 'trim|required');
		$this->form_validation->set_rules('how_many_available', 'How Many Available', 'trim|required');
		$this->load->library('Multiple_image_uploader');
		if ($this->form_validation->run() == false) {
			$message = lang('Please make sure required items are not blank');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		} else {

			$title = $this->input->post('title');
			$shortDescription = $this->input->post('short_description');
			$requiredPoints = $this->input->post('required_points');
			$howManyAvailable = $this->input->post('how_many_available');
			$details = $this->input->post('item_details');
			$today = $this->utils->getNowForMysql();
			$bannerImgName = $this->input->post('userfile');
			$bannerURL = $this->input->post('banner_url');
			$is_default_banner_flag = $this->input->post('is_default_banner_flag');
			$shoppingCenterItemId = $this->input->post('itemId');
			$hideOnPlayerCenter = $this->input->post('hideOnPlayerCenter') ? 1 : 0;

			//new feature
			$tagAsNewFlag = $this->input->post('tagAsNewFlag');
			$fileType = substr($bannerURL, strrpos($bannerURL, '.') + 1);
			$path_image = $_FILES['userfile']['name'];
			$image = isset($_FILES['userfile']) ? $_FILES['userfile'] : null;
			//$ext = pathinfo($path_image, PATHINFO_EXTENSION);

			/*if (!empty($path_image) && strcasecmp($ext, 'jpg') != 0 && strcasecmp($ext, 'jpeg') != 0 && strcasecmp($ext, 'gif') != 0 && strcasecmp($ext, 'png') != 0) {
				$message = lang('con.aff46');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				redirect('marketing_management/shoppingCenterItemList');
			}*/

			$this->load->model('shopping_center');

			if ($shoppingCenterItemId) {
				$data = array(
					'id' => $shoppingCenterItemId,
					'title' => $title,
					'short_description' => $shortDescription,
					'how_many_available' => $howManyAvailable,
					'details' => $details ?: '',
					'requirements' => json_encode(array("required_points" => $requiredPoints)),
					'updated_by' => $this->authentication->getUserId(),
					'updated_at' => $today,
					'tag_as_new' => $tagAsNewFlag == "on" ? true : false,
					'is_default_banner_flag' => $is_default_banner_flag == "true" ? 1 : 0,
					'status' => Shopping_center::STATUS_NORMAL,
					'hide_it_on_player_center' => $hideOnPlayerCenter,
				);
				$rawPost = $this->input->post('is_default_banner_flag');
				// var_dump($data, $rawPost);exit();

				if ($is_default_banner_flag) {
					$data['banner_url'] = $bannerURL;
				}

				if (!empty($path_image[0])) {
					$bannerImgName = 'shopping-item-' . uniqid();

					$config = array(
						'allowed_types' => 'jpg|jpeg|gif|png|PNG',
						'upload_path' => $this->utils->getShopThumbnailsPath(),
						'max_size' => 100000,
						'overwrite' => true,
						'file_name' => $bannerImgName,
					);

					$response = $this->multiple_image_uploader->do_multiple_uploads($image, $this->utils->getShopThumbnailsPath(), $config, $bannerImgName);
					if ($response['status'] == "fail") {
						$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response['message']);
						redirect(BASEURL . 'marketing_management/shoppingCenterItemList');
					}

					$data['banner_url'] = $bannerImgName. '.' . $fileType;
				}

				$this->shopping_center->updateItemData($data);
				$message = " <b>" . $title . "</b> " . lang('con.cms05');

				$data = array(
					'username' => $this->authentication->getUsername(),
					'management' => 'Marketing Management',
					'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
					'action' => 'Edit Shopping Center Item: ' . $shoppingCenterItemId,
					'description' => "User " . $this->authentication->getUsername() . " edit shoppping center item id: " . $shoppingCenterItemId,
					'logDate' => $today,
					'status' => self::FALSE,
				);
				$this->report_functions->recordAction($data);
			} else {

				$data = array(
					'title' => $title,
					'short_description' => $shortDescription,
					'how_many_available' => $howManyAvailable,
					'requirements' => json_encode(array("required_points" => $requiredPoints)),
					'details' => $details,
					'tag_as_new' => $tagAsNewFlag,
					'created_by' => $this->authentication->getUserId(),
					'created_at' => $today,
					'status' => self::TRUE,
					'is_default_banner_flag' => $is_default_banner_flag == "true" ? 1 : 0,
					'hide_it_on_player_center' => $hideOnPlayerCenter,
				);

				// var_dump($data);exit();

				if (!empty($path_image[0])) {
					// upload image
					$bannerImgName = 'shopping-item-' . uniqid();
					$config = array(
						'allowed_types' => 'jpg|jpeg|gif|png|PNG',
						'upload_path' => $this->utils->getShopThumbnailsPath(),
						'max_size' => 100000,
						'overwrite' => true,
						'file_name' => $bannerImgName,
					);

					$response = $this->multiple_image_uploader->do_multiple_uploads($image, $this->utils->getShopThumbnailsPath(), $config, $bannerImgName);
					if ($response['status'] == "fail") {
						$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response['message']);
						redirect(BASEURL . 'marketing_management/shoppingCenterItemList');
					}
					$data['banner_url'] = $bannerImgName . '.' . $fileType;
				} else {
					//will save the preset banner choosen
					$data['banner_url'] = $bannerURL;
				}

				//record action
				$this->saveAction('Add Shopping Center Item', "User " . $this->authentication->getUsername() . " has successfully added shopping center item: " . $title);
				$this->shopping_center->insertShoppingCenterItem($data);

				$message = "<b>" . $title . "</b> " . lang('con.cms07');
			}
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		}
		redirect(BASEURL . 'marketing_management/shoppingCenterItemList');
	}

	public function getShoppingItemDetails($itemId) {
		$this->load->model('shopping_center');
		echo json_encode($this->shopping_center->getItemDetails($itemId));
	}

	public function getShoppingItemDetailsWithPlayerId($itemId, $playerId) {
		$this->load->model(array('shopping_center', 'shopper_list'));
		$isPlayerItemReqExists = $this->shopper_list->isplayerItemRequestExists($itemId, $playerId);
		$res = $this->shopping_center->getItemDetails($itemId);
		$res['is_player_item_req_exists'] = $isPlayerItemReqExists ? true : false;
		echo json_encode($res);
	}

	/**
	 * activate shopping item
	 *
	 * @param   id
	 * @param   status
	 * @return  redirect
	 */
	public function activateShoppingItem($id, $status) {
		$this->load->model('shopping_center');
		$today = $this->utils->getNowForMysql();
		$data = array(
			'updated_by' => $this->authentication->getUserId(),
			'updated_at' => $this->utils->getNowForMysql(),
			'status' => $status == 'active' ? 1 : 0,
			'id' => $id,
		);

		$this->load->model('shopping_center');
		$this->shopping_center->activateShoppingCenterItem($data);

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Marketing Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Update status of shopping item: ' . $id . ' to status: ' . $status,
			'description' => "User " . $this->authentication->getUsername() . " edit shopping item id: " . $id . ' to status: ' . $status,
			'logDate' => $today,
			'status' => self::FALSE,
		);

		$this->report_functions->recordAction($data);
		redirect(BASEURL . 'marketing_management/shoppingCenterItemList');
	}

	/**
	 * Delete selected shopping item
	 *
	 * @param   int
	 * @return  redirect
	 */
	public function deleteSelectedShoppingItem() {
		$this->load->model('shopping_center');
		$items = $this->input->post('items');
		if ($items) {
			foreach ($items as $itemsId) {
				$this->shopping_center->deleteItem($itemsId);

				$data = array(
					'username' => $this->authentication->getUsername(),
					'management' => 'Marketing Management',
					'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
					'action' => 'Delete shopping item id:' . $itemsId,
					'description' => "User " . $this->authentication->getUsername() . " deleted shopping item id: " . $itemsId,
					'logDate' => $this->utils->getNowForMysql(),
					'status' => self::FALSE,
				);

				$this->report_functions->recordAction($data);
			}

			$message = lang('Successfully Deleted!');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
		} else {
			$message = lang('You must select item first!');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		}

		redirect(BASEURL . 'marketing_management/shoppingCenterItemList');
	}

	/**
	 * Delete shopping item
	 *
	 * @param   int
	 * @return  redirect
	 */
	public function deleteShoppingItem($id) {
		$this->load->model('shopping_center');
		$this->shopping_center->deleteItem($id);

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'Marketing Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Delete shopping item item id:' . $id,
			'description' => "User " . $this->authentication->getUsername() . " deleted shopping item id: " . $id,
			'logDate' => $this->utils->getNowForMysql(),
			'status' => self::FALSE,
		);

		$this->report_functions->recordAction($data);

		$message = lang('Successfully Deleted!');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		redirect(BASEURL . 'marketing_management/shoppingCenterItemList');
	}

	public function claimShoppingItem($itemId, $playerId) {
		$isAjaxCall = $this->input->is_ajax_request() ? true : false;
		$this->load->model(array('shopper_list', 'shopping_center', 'point_transactions'));
		if ($this->shopper_list->isplayerItemRequestExists($itemId, $playerId)) {
			return;
		}
		$shopperList = $this->shopper_list->getShopperList($itemId, Shopper_list::APPROVED);
		$firstChild = 0;
		$itemDetails = $this->shopping_center->getData($itemId)[$firstChild];

		$availableShoppingItem = $itemDetails['how_many_available'];
		$availableShoppingItemRequiredPts = json_decode($itemDetails['requirements'], true)['required_points'];

		$totalPoints = $this->getPlayerTotalPoints($playerId);
		if ($totalPoints >= $availableShoppingItemRequiredPts) {
			if (count($shopperList) < $availableShoppingItem) {
				#insert to shopper_list table
				$data = array(
					"player_id" => $this->authentication->getPlayerId(),
					"player_username" => $this->authentication->getUsername(),
					"shopping_item_id" => $itemId,
					"required_points" => $availableShoppingItemRequiredPts,
					"status" => Shopper_list::REQUEST,
					"application_datetime" => $this->utils->getNowForMysql(),
				);

				$this->shopper_list->addRequestToShopperList($data);

				$status = "success";
				$message = lang('Your request has successfully sent!');
				if (!$isAjaxCall) {
					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
				}
			} else {
				$status = "failed";
				$message = lang('Request limit has been reached, pls choose another item..');
				if (!$isAjaxCall) {
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				}
			}
		} else {
			$status = "failed";
			$message = lang('Your points is insufficient to claim this item!');
			if (!$isAjaxCall) {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			}
		}

		if ($isAjaxCall) {
			$this->returnJsonResult(array('status' => $status, 'msg' => lang($message)));
			return;
		}
		$this->utils->debug_log("Message: =============================================> " . $message);
		redirect('player_center');
	}

	/**
	 * shoppingClaimRequestList
	 *
	 *
	 * @return	redered template
	 */
	public function shoppingClaimRequestList() {
		if (!$this->permissions->checkPermissions('shopping_claim_request_list')) {
			$this->error_access();
		} else {
			$this->load->model(array('shopper_list', 'users', 'shopping_center'));

			$this->loadTemplate(lang('role.59'), '', '', 'marketing');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');

			if (!$this->permissions->checkPermissions('export_report')) {
				$data['export_report_permission'] = FALSE;
			} else {
				$data['export_report_permission'] = TRUE;
			}

			$data['countAllStatus'] = $this->shopper_list->countAllStatusOfShoppingList();
			$data['allLevels'] = $this->player_manager->getAllPlayerLevels();
			$data['allShoppingItem'] = $this->shopping_center->getData(null, Shopping_center::STATUS_NORMAL);
			$data['users'] = $this->users->getAllUsernames();

			$start_today = date("Y-m-d") . ' 00:00:00';
			$end_today = date("Y-m-d") . ' 23:59:59';

			$data['conditions'] = $this->safeLoadParams(array(
				'status' => Shopper_list::REQUEST,
				'request_date_from' => $start_today,
				'request_date_to' => $end_today,
				'username' => '',
				'processed_by' => '',
				'vipsettingcashbackruleId' => '',

			));

			$this->template->add_css('resources/css/dashboard.css');
			$this->template->add_js('resources/third_party/datatables/datatables.min.js');
			$this->template->add_css('resources/third_party/datatables/datatables.min.css');
			$this->template->write_view('main_content', 'shopping_center/shopping_claim_request_list', $data);
			$this->template->render();
		}
	}

	public function approveOrDeclinedShopItemClaimRequest($playerId, $itemId, $status) {
		$this->load->model(array('shopper_list', 'point_transactions', 'shopping_center'));
		$shoppingItemRequiredPoints = $this->shopper_list->getItemDetails($itemId)['required_points'];
		$playerTotalPoints = $this->getPlayerTotalPoints($playerId);

		$shoppingItemId = $this->shopper_list->getItemDetails($itemId)['shopping_item_id'];
		$shoppingUsedItem = $this->shopper_list->getShoppingItemAvailableSlot($shoppingItemId)['usedItem'];
		$shoppingItemLimit = $this->shopping_center->getData($shoppingItemId)[0]['how_many_available'];

		$data = array(
			"id" => $itemId,
			"player_id" => $playerId,
			"processed_datetime" => $this->utils->getNowForMysql(),
			"processed_by" => $this->authentication->getUserId(),
		);
		// var_dump($shoppingUsedItem, $shoppingItemLimit, $shoppingItemId);exit();
		if ($status == Shopper_list::APPROVED) {
			$action = "Approved";

			//if shopping item reached do not approve the request
			if ($shoppingUsedItem >= $shoppingItemLimit) {
				//record action
				$this->saveAction($action . ' Shopping Item Claim Request Denied', "User " . $this->authentication->getUsername() . " has failed to " . $action . " shopping item claim request: " . $itemId . " because shopping item limit has been reached");

				$message = lang('Shopping Item Claim Approval was declined because shopping item\'s limit has been reached!');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				redirect('marketing_management/shoppingClaimRequestList');
			}

			//if player points is insufficient do not approve the request
			if ($playerTotalPoints < $shoppingItemRequiredPoints) {
				$this->saveAction($action . ' Shopping Item Claim Request Denied', "User " . $this->authentication->getUsername() . " has failed to " . $action . " shopping item claim request: " . $itemId . " because player points is insufficient");

				$message = lang('Shopping Item Claim Approval was declined because player points is insufficient!');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				redirect('marketing_management/shoppingClaimRequestList');
			}

		} elseif ($status == Shopper_list::DECLINED) {

			$action = "Declined";
			$message = lang('Successfully Declined shopping request!');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

			$data["notes"] = $this->input->post("reason") ?: null;

		}

		$data["status"] = $status;
		$result = $this->shopper_list->approveOrDeclinedShopItemClaimRequest($data);

		if ($result) {

			$message = lang('Successfully ' . $action . ' shopping request!');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

			if ($status == Shopper_list::APPROVED) {
				#todo deduct points of player here
				$newPointsBalance = $playerTotalPoints - $shoppingItemRequiredPoints;
				$this->point_transactions->createPointTransaction(
					point_transactions::ADMIN,
					$playerId,
					$shoppingItemRequiredPoints,
					$playerTotalPoints,
					$newPointsBalance,
					null, null, Point_transactions::DEDUCT_POINT, null, null,
					Point_transactions::DEDUCT
				);
			}

			//record action
			$this->saveAction($action . ' Shopping Item Claim Request', "User " . $this->authentication->getUsername() . " has successfully " . $action . " shopping item claim request: " . $itemId);

		} else {

			$message = lang('Failed to Save in DB!');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

			//record action
			$this->saveAction('Failed to processe shopping claim request due to failed in DB!', "User " . $this->authentication->getUsername() . " has failed to process shopping item claim request: " . $itemId);
		}

		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult(array('status' => 'success'));
			return;
		}

		redirect('marketing_management/shoppingClaimRequestList');
	}

	public function getPlayerTotalPoints($playerId) {
		$totalPoints = $this->point_transactions->getPlayerTotalPoints($playerId);
		$playerTotalDeductedPoints = 0;
		$playerTotalPoints = 0;
		if (!empty($totalPoints)) {
			$playerTotalPoints = array_sum(array_column($totalPoints, 'points'));
		}
		$playerTotalDeductedPoints = $this->point_transactions->getPlayerTotalDeductedPoints($playerId)['points'];
		$remainingPoints = $playerTotalPoints - $playerTotalDeductedPoints;
		return $remainingPoints;
	}

	public function getShoppingTransactionHistory($playerId) {
		$this->load->model('shopper_list');
		$data = $this->shopper_list->getShopperList(null, null, $playerId);
		return $this->returnJsonResult($data);
	}

	// public function export(){
	// 	$this->exportToExcel("shopmanageritemlist","Marketing Management");
	// }

}