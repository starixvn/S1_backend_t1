<?php

/**
 * Class promo_cms_module
 *
 * General behaviors include
 *
 * * Add/update/delete promo cms
 * * Activate/deactivate promo cms
 * * Export report to excel
 *
 * @category Marketing Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 *
 */
trait promo_cms_module {
	/**
	 * overview : promo cms list
	 *
	 * detail : view page for CMS setting list
	 *
	 * @return  redered template
	 */
	public function promoCmsList() {
		if (!$this->permissions->checkPermissions('promocms')) {
			$this->error_access();
		} else {

			$this->load->model(array('promorules', 'promo_type'));
			$sort = "promoName";
			$data['promoList'] = $this->promorules->getPromoSettingList($sort, null, null);
            $data['promorules'] = $this->promorules->getUsablePromorules(true, true, $data['promoList']);

			//export report permission checking
			if (!$this->permissions->checkPermissions('export_report')) {
				$data['export_report_permission'] = FALSE;
			} else {
				$data['export_report_permission'] = TRUE;
			}

			$this->loadTemplate('Marketing Management', '', '', 'marketing');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');
			$this->template->add_css('resources/css/promo_cms_management/promo_cms_management.css');
			$this->template->write_view('main_content', 'cms_management/promotion/cms_promo_list', $data);
			$this->template->render();
		}
	}

	/**
	 * add new promo
	 *
	 * @return  rendered template
	 */
	public function addNewCmsPromo() {
		$this->form_validation->set_rules('promoName', 'Promo Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('promoDescription', 'Promo Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('promoDetails', 'Promo Details', 'trim|required');
		$this->load->model("cms_model");
		$this->load->library('Multiple_image_uploader');
		if (!$this->form_validation->run()) {
			$message = lang('con.cms03');
			$promoDetails = $this->input->post('promoDetails');

			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		} else {
			$promoCmsCategoryId = $this->input->post("promoCmsCategoryId");
			$promoCode = $this->input->post('promoCode');
			$promoName = $this->input->post('promoName');
			$language = $this->session->userdata('login_lan');
			$promoDescription = $this->input->post('promoDescription');
			$promoDetails = $this->input->post('promoDetails');
            $promoDetailsLength = $this->input->post('promoDetailsLength'); //the length of promoDetails before post to this function
			$promoId = $this->input->post('promoLink');
			$promocmsId = $this->input->post('promocmsId');
			$show_on_player = $this->input->post('show_on_player');
			$promoThumbnailName = $this->input->post('userfile');
			$promoBannerURL = $this->input->post('banner_url');
			$is_default_banner_flag = $this->input->post('is_default_banner_flag') ? 1 : 0;
			$allow_claim_promo_in_promo_page = $this->input->post('allow_claim_promo_in_promo_page') ? 1 : 0;
			$claim_button_link = $this->input->post('claim_button_link');
			$claim_button_name = $this->input->post('claim_button_name');

			//new feature
			$tagAsNewFlag = $this->input->post('tagAsNewFlag');

            $detailsDiffLength = FALSE;
            if($promoDetailsLength != strlen($promoDetails)){
                $detailsDiffLength = TRUE;
            }

			if(!empty($promoBannerURL) && strrpos($promoBannerURL, '.') > 0) {
				$fileType = substr($promoBannerURL, strrpos($promoBannerURL, '.') + 1);
			}

			if(empty($show_on_player)){
				$show_on_player = null ;
			}else{
				if(count($show_on_player) > 1){
					$show_on_player = Promorules::SHOW_ON_PLAYER_PROMOTION_AND_DEPOSIT;
				}else{
					$show_on_player = implode('', $show_on_player);
				}
			}

			$path_image = $_FILES['userfile']['name'];
			$image = isset($_FILES['userfile']) ? $_FILES['userfile'] : null;
            $promoDetails = htmlentities($promoDetails, ENT_QUOTES, "UTF-8") ?: '';
			//$ext = pathinfo($image, PATHINFO_EXTENSION);

			$promoCmsData = array(
				'promoName' => $promoName,
				'language' => $language,
				'promoId' => $promoId,
				'promo_code' => $promoCode,
				'promoDescription' => $promoDescription,
				'hide_on_player' => $show_on_player,
				'promoDetails' => $promoDetails,
				'promo_category' => $promoCmsCategoryId,
				'tag_as_new_flag' => $tagAsNewFlag == "on" ? true : false,
				'allow_claim_promo_in_promo_page' => $allow_claim_promo_in_promo_page,
				'claim_button_link' => $claim_button_link,
				'claim_button_name' => $claim_button_name,
				'updatedBy' => $this->authentication->getUserId(),
				'updatedOn' => $this->utils->getNowForMysql(),
			);

			// OGP-5225
			$defaultLangItem = $this->input->post('promo_item_default_lang');
			$defaultLang = $this->language_function->getPromoLanguageIdFromShortCode($defaultLangItem);
			$multiLangData = $this->getMultiLangVal();
			if ($this->utils->isEnabledFeature('enable_multi_lang_promo_manager')) {
				$promoCmsData['default_lang'] = $defaultLang;
				$multiLangData['multi_lang'][$defaultLangItem]['promo_title_'.$defaultLangItem] = $promoName;
				$multiLangData['multi_lang'][$defaultLangItem]['short_desc_'.$defaultLangItem] = $promoDescription;
				$multiLangData['multi_lang'][$defaultLangItem]['details_'.$defaultLangItem] = $promoDetails;
			}

			if ($promocmsId) {

                if($detailsDiffLength){
                    return $this->returnCommon(self::MESSAGE_TYPE_ERROR, lang('Edit Promo Cms Failed Due To Error Encoding'), NULL, BASEURL . 'marketing_management/promoSettingList');
                }

				$promoCmsData['is_default_banner_flag'] = $is_default_banner_flag;
				if(!empty($promoBannerURL) || $is_default_banner_flag) {
					$promoCmsData['promoThumbnail'] = $promoBannerURL;
				}
				$promoCmsData['promoCmsSettingId'] = $promocmsId;

				if (!empty($path_image[0])) {
					//we can use new upload library here
					$promoThumbnailName = 'promothumbnail-' . uniqid();

					$response = $this->multiple_image_uploader->do_multiple_uploads($image, $this->utils->getPromoThumbnails(), $this->getUploadConfig($promoThumbnailName), $promoThumbnailName);
					if ($response['status'] == "fail") {
						$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response['message']);
						redirect(BASEURL . 'marketing_management/promoSettingList');
					}
					$promoBannerURL = $promoThumbnailName . '.' . $fileType;
					$promoCmsData['promoThumbnail'] = $promoBannerURL;
					$promoCmsData['is_default_banner_flag'] = self::FALSE;
				}

				if ($this->utils->isEnabledFeature('enable_multi_lang_promo_manager')) {
					$multiLangData['multi_lang'][$defaultLangItem]['banner_'.$defaultLangItem] = $promoBannerURL;
					$promoCmsData['promo_multi_lang'] = $this->utils->encodeJson($multiLangData);
				}

				$this->cms_model->editPromoCmsItem($promoCmsData);
				$message = lang('con.cms04') . " <b>" . $promoName . "</b> " . lang('con.cms05');

				//record action
				$this->saveAction('Add CMS Promo', "User " . $this->authentication->getUsername() . " has successfully edited promo cms: " . $promoName);
			} else {

                if($detailsDiffLength){
                    return $this->returnCommon(self::MESSAGE_TYPE_ERROR, lang('Add Promo Cms Failed Due To Error Encoding'), NULL, BASEURL . 'marketing_management/promoSettingList');
                }

				$isPromoNameExist = $this->cms_model->getPromoCMSName($this->input->post('promoName'));
				if ($isPromoNameExist) {
					$message = lang('con.cms06');
					$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				} else {
					$promoCmsData['createdBy'] = $this->authentication->getUserId();
					$promoCmsData['createdOn'] = $this->utils->getNowForMysql();
					$promoCmsData['status'] = 'active';

					if (!empty($path_image[0])) {

						// upload image
						$promoThumbnailName = 'promothumbnail-' . uniqid();

						$response = $this->multiple_image_uploader->do_multiple_uploads($image, $this->utils->getPromoThumbnails(), $this->getUploadConfig($promoThumbnailName), $promoThumbnailName);
						if ($response['status'] == "fail") {
							$this->alertMessage(self::MESSAGE_TYPE_ERROR, $response['message']);
							redirect(BASEURL . 'marketing_management/promoSettingList');
						}
						$promoBannerURL = $promoThumbnailName . '.' . $fileType;
						$promoCmsData['promoThumbnail'] = $promoBannerURL;
						$promoCmsData['is_default_banner_flag'] = self::FALSE;
					} else {
						//will save the preset banner choosen
						$promoCmsData['promoThumbnail'] = $promoBannerURL ?: 'default_promo_cms_1.jpg?';
						$promoCmsData['is_default_banner_flag'] = self::TRUE;
					}

					//record action
					$this->saveAction('Add CMS Promo', "User " . $this->authentication->getUsername() . " has successfully added promo cms: " . $promoName);

					if ($this->utils->isEnabledFeature('enable_multi_lang_promo_manager')) {
						$multiLangData['multi_lang'][$defaultLangItem]['banner_'.$defaultLangItem] = $promoBannerURL;
						$promoCmsData['promo_multi_lang'] = $this->utils->encodeJson($multiLangData);
					}

					$this->cms_model->addPromoCms($promoCmsData);
					$message = "<b>" . $promoName . "</b> " . lang('con.cms07');
					$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
				}
			}
		}

		$this->clearPromoItemMultiLang();

		redirect(BASEURL . 'marketing_management/promoSettingList');
	}

	private function getUploadConfig($promoThumbnailName) {
		$config = array(
			'allowed_types' => 'jpg|jpeg|gif|png|PNG',
			'upload_path' => $this->utils->getPromoThumbnails(),
			'max_size' => 500000,
			'overwrite' => true,
			'remove_spaces' => true,
		);
		return $config;
	}

	/**
	 * get promo details
	 *
	 * @param   int
	 * @return  redirect
	 */
	public function getPromoCmsItemDetailsByPlayerId($type, $playerId, $promoCmsId) {
		echo json_encode($this->utils->getPlayerPromo($type, $playerId, $promoCmsId));
	}

	public function getPromoCmsItemDetails($promocmsId) {
		$this->load->model('cms_model');
		echo json_encode($this->cms_model->getPromoCmsDetails($promocmsId));
	}

	/**
	 * Delete selected cms promos
	 *
	 * @param   int
	 * @return  redirect
	 */
	public function deleteSelectedPromoCms() {
        $this->load->model(['withdraw_condition']);
		$promocms = $this->input->post('promocms');
        $promocms = json_decode($promocms, true);

		if (!empty($promocms)) {
			foreach ($promocms as $promocmsId) {
				$this->cms_model->deletePromoCms($promocmsId);

				$data = array(
					'username' => $this->authentication->getUsername(),
					'management' => 'CMS Promo Setting Management',
					'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
					'action' => 'Delete cms promo item id:' . $promocmsId,
					'description' => "User " . $this->authentication->getUsername() . " deleted cms promo id: " . $promocmsId,
					'logDate' => date("Y-m-d H:i:s"),
					'status' => self::FALSE,
				);
				$this->report_functions->recordAction($data);

                //finish withdraw condition which generated by this promocms
                $result = $this->withdraw_condition->getWithdrawConditionByPromoCmsSettingId($promocmsId);
                if(!empty($result)){
                    $this->withdraw_condition->updateWithdrawConditionByPromoCmsSettingId($result);
                }
			}

            $rlt = array('success' => true, 'promocmsId' => $promocmsId, 'result' => $result, 'type' => gettype($result));
			$message = lang('con.cms16');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message); //will set and send message to the user
		} else {
            $rlt = array('success' => false);
			$message = lang('con.cms17');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		}

        return $this->returnJsonResult($rlt);
	}

	/**
	 * Delete selected cms promo
	 *
	 * @param   int
	 * @return  redirect
	 */
	public function deletePromoCmsItem($promocmsId) {
        $this->cms_model->deletePromoCms($promocmsId);

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'CMS Promo Setting Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Delete cms promo item id:' . $promocmsId,
			'description' => "User " . $this->authentication->getUsername() . " deleted cms promo id: " . $promocmsId,
			'logDate' => $this->utils->getNowForMysql(),
			'status' => '0',
		);
		$this->report_functions->recordAction($data);

        //finish withdraw condition which generated by this promocms
        $this->load->model(['withdraw_condition']);
        $result = $this->withdraw_condition->getWithdrawConditionByPromoCmsSettingId($promocmsId);
        if(!empty($result)){
            $this->withdraw_condition->updateWithdrawConditionByPromoCmsSettingId($result);
        }

        $rlt = array('success' => true, 'promocmsId' => $promocmsId, 'result' => $result);

        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('con.cms18'));
        return $this->returnJsonResult($rlt);
	}

	/**
	 * activate promo cms
	 *
	 * @param   promocmsId
	 * @param   status
	 * @return  redirect
	 */
	public function activatePromoCms($promocmsId, $status) {
		$data = array(
			'updatedBy' => $this->authentication->getUserId(),
			'updatedOn' => $this->utils->getNowForMysql(),
			'status' => $status,
			'promoCmsSettingId' => $promocmsId,
		);

		$this->cms_model->activatePromoCms($data);

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'CMS Promo Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Update status of cms promo item: ' . $promocmsId . ' to status: ' . $status,
			'description' => "User " . $this->authentication->getUsername() . " edit cms promo id: " . $promocmsId . ' to status: ' . $status,
			'logDate' => date("Y-m-d H:i:s"),
			'status' => self::FALSE,
		);

		$this->report_functions->recordAction($data);

		redirect(BASEURL . 'marketing_management/promoSettingList');
	}


	/**
	 * save promo manager's/promo cms promoname and shortdescription multilang
	 *
	 * save data to session
	 * @return null
	 */
	public function savePromoCmsMultiLang() {
	    $insertData = [];
        $multiLangData = [];
        $postData = $this->input->post();
        array_filter($postData);

        $systemLanguages = $this->getSystemLanguages();
        $insertData['session_id'] = $this->session->userdata('session_id');

        foreach ($systemLanguages as $lang) {
            $multiLangData['multi_lang'][$lang['short_code']]['promo_title_'.$lang['short_code']] = $postData['promo_title_'.$lang['short_code']];
            $multiLangData['multi_lang'][$lang['short_code']]['short_desc_'.$lang['short_code']] = $postData['short_desc_'.$lang['short_code']];
            $multiLangData['multi_lang'][$lang['short_code']]['details_'.$lang['short_code']] = htmlentities($postData['details_'.$lang['short_code']], ENT_QUOTES, "UTF-8");
            $multiLangData['multi_lang'][$lang['short_code']]['banner_'.$lang['short_code']] = $postData['banner_'.$lang['short_code']];
        }
        $insertData['promo_multi_lang'] = json_encode($multiLangData);
        $this->cms_model->insertTempMultiLang($insertData);
	}

	/**
	 * OGP-5225
	 * get promo manager's/promo cms promoname and shortdescription multilang in stored session then
	 * parse into json format
	 *
	 * @return json
	 */
	public function getMultiLangVal(){
		$tempData = array("multi_lang"=>array());
        $session_id = $this->session->userdata('session_id');
		$mutiLang = $this->cms_model->getTempMultiLang($session_id);

		if(isset($mutiLang['promo_multi_lang']) && !empty($mutiLang['promo_multi_lang'])){
            $data = json_decode($mutiLang['promo_multi_lang'], true);
            $systemLanguages = $this->getSystemLanguages();

            foreach ($systemLanguages as $lang) {
                $tempData['multi_lang'][$lang['short_code']]['promo_title_'.$lang['short_code']] = ($data['multi_lang'][$lang['short_code']]["promo_title_".$lang['short_code']]) ?: "";
                $tempData['multi_lang'][$lang['short_code']]['short_desc_'.$lang['short_code']] = ($data['multi_lang'][$lang['short_code']]["short_desc_".$lang['short_code']]) ?: "";
                $tempData['multi_lang'][$lang['short_code']]['details_'.$lang['short_code']] = ($data['multi_lang'][$lang['short_code']]["details_".$lang['short_code']]) ?: "";
                $tempData['multi_lang'][$lang['short_code']]['banner_'.$lang['short_code']] = ($data['multi_lang'][$lang['short_code']]["banner_".$lang['short_code']]) ?: "";
            }
        }
		return $tempData;
	}

	public function clearPromoItemMultiLang(){
        $session_id = $this->session->userdata('session_id');
        $this->cms_model->delTempMultiLang($session_id);
	}

	private function getSystemLanguages(){
		return $this->language_function->getAllSystemLanguages();
	}

	public function multiUploadPromoManager(){
		$this->load->view('marketing_management/promorules/promo_mgr_multiupload');
	}

	public function uploadBannerMultiLang($bannerName=null) {
		$this->load->library('Multiple_image_uploader');
		$path_image = $_FILES['userfile']['name'];
		$image = isset($_FILES['userfile']) ? $_FILES['userfile'] : null;

		// upload image
		$bannerName = current(explode(".", $bannerName, 2));
		$bannerImgName = $bannerName ?: 'promomgr_item_' . uniqid();
		$response = $this->multiple_image_uploader->do_multiple_uploads($image, $this->utils->getPromoThumbnails(), $this->getUploadConfig($bannerImgName), $bannerImgName);
		echo json_encode($response);
	}
}