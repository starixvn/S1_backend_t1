<?php
trait playercenter_notif_module {

    public function notificationManagementSettings(){
        $this->loadPage("notification_settings",array("notif_settings"=>$this->getNotifSettings(),"lang_code"=>$this->getLangCode()));
    }

    public function editNotificationItem($itemId,$notifType="fund_transfer"){
        $notifSettings = $this->getNotifSettings();
        $lang_code = $this->getLangCode();
        switch ($notifType) {
            case "fund_transfer":
                $data = array("itemSettingDetails"=>$notifSettings['transfer_fund_notif'][$itemId],"itemId"=>$itemId,"lang_code"=>$lang_code);
                $pageName = "edit_fund_transfer_setting_item_form";
                break;
            case "cashback_claim":
                $data = array("itemSettingDetails"=>$notifSettings['cashback_notif'][$itemId],"itemId"=>$itemId,"lang_code"=>$lang_code);
                $pageName = "edit_cashback_claim_form";
                break;
            case "customer_support_url":
                $data = array("itemSettingDetails"=>$notifSettings['customer_support']);
                $pageName = "edit_customer_support_form";
                break;
        }
        $this->loadPage($pageName,$data);
    }

    public function submitUpdateSettings($settingsType,$itemId=null){
        $inputData = $this->input->post();
        array_filter($inputData);

        $notifSettings = $this->getNotifSettings();

        switch ($settingsType) {
            case "transfer_fund_notif":
                array_walk($notifSettings['transfer_fund_notif'], function (&$item,&$key) use ($itemId,$inputData) {
                    if($key == $itemId){
                        $item['custom_error_code'] = $inputData['custom_error_code'];
                        $item['multi_lang_messages'][$this->getLangCode()]['custom_error_msg'] = $inputData['custom_error_msg'];
                        $item['multi_lang_messages'][$this->getLangCode()]['player_option_msg1'] = $inputData['player_option_msg1'] ?: "N/A";
                        $item['multi_lang_messages'][$this->getLangCode()]['player_option_msg2'] = $inputData['player_option_msg2'] ?: "N/A";
                    }
                }
                );
                break;

            case "cashback_claim_notif":
                array_walk($notifSettings['cashback_notif'], function (&$item,&$key) use ($itemId,$inputData) {
                    if($key == $itemId){
                        $item['custom_error_code'] = $inputData['custom_error_code'];
                        $item['multi_lang_messages'][$this->getLangCode()]['custom_error_msg'] = $inputData['custom_error_msg'];
                        $item['multi_lang_messages'][$this->getLangCode()]['claim_error_notif_msg'] = $inputData['claim_error_notif_msg'];
                        $item['multi_lang_messages'][$this->getLangCode()]['player_option_msg'] = $inputData['player_option_msg'];
                    }
                }
                );
                break;

            case "customer_support_url":
                array_walk($notifSettings['customer_support'], function (&$item) use ($inputData) {
                    $item['url'] = $inputData['url'];
                }
                );
                break;
        }

        if($this->updateNotifSettings($notifSettings)){
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS,lang("Settings update has been successfully made"));
        }else{
            $this->alertMessage(self::MESSAGE_TYPE_ERROR,lang("Settings update failed"));
        }

        redirect('cms_management/notificationManagementSettings');
    }

    public function setSettingsStatus($settingsType,$itemId,$status){
        $notifSettings = $this->getNotifSettings();

        array_walk($notifSettings[$settingsType], function (&$item,&$key) use ($itemId,$status) {
            if($key == $itemId){
                $item['is_enabled'] = $status;
            }
        }
        );
        if($this->updateNotifSettings($notifSettings)){
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS,lang("Settings update has been successfully made"));
        }else{
            $this->alertMessage(self::MESSAGE_TYPE_ERROR,lang("Settings update failed"));
        }

        redirect('cms_management/notificationManagementSettings');
    }

    private function loadPage($pageName,$data=array()){
        if (!$this->permissions->checkPermissions('playercenter_notif_mngmt')) {
            $this->error_access();
        } else {
            $this->loadTemplate(lang('Player Center Notification Management'), '', '', 'cms');
            $this->template->write_view('main_content', 'cms_management/playercenter_notif_mngmt/'.$pageName, $data);
            $this->template->render();
        }
    }

    private function getNotifSettings(){
        return $this->utils->getOperatorSettingInJson("cashier_notification_settings","template");
    }

    private function updateNotifSettings($notifSettings){
        $this->load->model(['operatorglobalsettings']);
        return $this->operatorglobalsettings->putSettingJson("cashier_notification_settings",$notifSettings,"template");
    }

    private function getLangCode(){
        return $this->utils->getCurrentLanguageCode();
    }
}