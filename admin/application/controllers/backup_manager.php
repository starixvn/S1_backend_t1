<?php

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Back up manager
 *
 * @deprecated
 *
 * @author  shu
 *
 */
class Backup_manager extends BaseController {
	function __construct() {
		parent::__construct();
		$this->load->helper(array('url','file'));
		$this->load->model(array('external_system','payment_account','Operatorglobalsettings','vipsetting'));
		$this->load->library(array('permissions', 'form_validation', 'template', 'pagination', 'report_functions', 'utils','zip'));
		$this->load->dbutil();
		$this->permissions->checkSettings();
		$this->permissions->setPermissions(); //will set the permission for the logged in user
	}

	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_js('resources/js/system_management/user_management.js');

		$this->template->add_js('resources/js/datatables.min.js');
		//$this->template->add_js('resources/js/jquery.dataTables.min.js');
		//$this->template->add_js('resources/js/dataTables.responsive.min.js');

		$this->template->add_css('resources/css/general/style.css');
		//$this->template->add_css('resources/css/jquery.dataTables.css');
		//$this->template->add_css('resources/css/dataTables.responsive.css');
		$this->template->add_css('resources/css/datatables.min.css');

		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());

		/*$lang = $this->language_function->getCurrentLanguage();
			$langCode = $this->language_function->getLanguageCode($lang);
			$language = $this->language_function->getLanguage($lang);
		*/
		$this->template->write_view('sidebar', 'system_management/sidebar');
	}

	function backup_system_settings(){
		$backup_data = array();
		//get external_system table game api and payment api
		$backup_data['external_system'] = array_merge($this->external_system->getAllSytemGameApi(),$this->external_system->getAllSystemPaymentApi());
		//get collection account from payment_account table
		$backup_data['payment_account'] = $this->payment_account->getAllPaymentAccount();
		$backup_data['payment_account_player_level'] = $this->payment_account->getAllPaymentAccountPlayerLevel();
		//get operator_settings
		$backup_data['operator_settings'] = $this->Operatorglobalsettings->getAllOperatorSettings();
		//get vip settings
		//TODO group_level
		// $backup_data['vipsetting'] = $this->vipsetting->getAllvipsetting();
		// $backup_data['vipgrouppayoutsetting'] =  $this->vipsetting->getAllvipgrouppayoutsetting();
		// $backup_data['vipsetting_cashback_game'] = $this->vipsetting->getAllvipsetting_cashback_game();
		// $backup_data['vipsettingcashbackbonuspergame'] = $this->vipsetting->getAllvipsettingcashbackbonuspergame();
		// $backup_data['vipsettingcashbackrule'] = $this->vipsetting->getAllvipsettingcashbackrule();

		$backup_data_json = json_encode($backup_data);
		//print_r($backup_data_json);exit();
		$folder ='system_backup_'.date("YmdHms");
		if(!is_dir('./backup-restore/')){
			mkdir('./backup-restore'); // make directory for backups
		}
		$path ='./backup-restore/';// create path name
		mkdir($path);//make directory
		$filename = "data.json";//file name of json file
		$fp = fopen($path.'/'.$filename, 'w'); // open data.json file
        fwrite($fp, $backup_data_json);// write backuo_data_json to data.json
        fclose($fp); // exit file
        copy('../../secret_keys/config_secret_local.php', $path.'/config_secret_local.php');//copy config secret to backup filepath

       	$zip = new ZipArchive;
       	$result = $zip->open($path.$folder.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
       	$zip->addFile($path.'config_secret_local.php',$path.'/config_secret_local.php');
       	$zip->addFile($path.'data.json',$path.'/data.json');
       	$zip->close();
      // 	echo "<pre>";print_r($zip);exit;
       	if (file_exists($path.$folder.'.zip')) {
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename="'.basename($path.$folder.'.zip').'"');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($path.$folder.'.zip'));
		    ob_end_clean();
		    readfile($path.$folder.'.zip');
		}

		//delete files after archive
		delete_files('./backup-restore',true);
	}

	function backuprestore_manager(){
		$secret_key = 'Ch0wK1ing&M@ng!n@s@l';// for trancate table
    $config['upload_path'] = './backup-restore/';
    $config['allowed_types'] = 'zip|rar';
    $config['max_size']    = '';
    $dataArray = array();
    $this->load->library('upload', $config);
    if ( ! $this->upload->do_upload()){
  		if($this->upload->file_name!=''){
   	 		$dataArray = array('success'=>0,'message'=>lang('bak.notAllowedFile'));
   	 	}
    }else{
    	$data = array('upload_data' => $this->upload->data());
    	$zip = new ZipArchive;
    	$file = $data['upload_data']['full_path'];
			chmod($file,0777);
			$path ='./backup-restore/';
			if ($zip->open($file) === TRUE) {
    			if($zip->extractTo($path)){
    				$zip->close();
    				if(is_dir($path.'backup-restore')){
    					if(file_exists($path.'backup-restore/data.json')&&file_exists($path.'backup-restore/data.json')){
    						$read_data = json_decode(read_file($path.'backup-restore/data.json'),true);
    						// truncate tables before restore data from data.json set secret_key truncate
    						// $this->vipsetting->truncateTablesSync($secret_key);
    						$this->Operatorglobalsettings->truncateTablesSync($secret_key);
    						$this->external_system->truncateTablesSync($secret_key);
    						$this->payment_account->truncateTablesSync($secret_key);
    						foreach($read_data as $key => $val){
    							$modelName = $key;
    							$function = 'addRecord'; // default funcion
    							//swithc bypass modelName and function name
    							switch($key){
    								case 'payment_account_player_level':
								        $modelName = 'payment_account';
								        $function = 'addRecordPlayerLevel';
								        break;
								    case 'operator_settings':
								    	$modelName = 'Operatorglobalsettings';
								    	break;
								    case 'vipgrouppayoutsetting':
								    	$modelName = 'vipsetting';
								    	$function = 'addVipGroupPayout';
								    	break;
								    case 'vipsetting_cashback_game':
								    	$modelName = 'vipsetting';
								    	$function = 'addCashbackGame';
								    	break;
								    case 'vipsettingcashbackbonuspergame':
								    	$modelName = 'vipsetting';
								    	$function = 'addCashbackBonusPerGame';
								    	break;
								    case 'vipsettingcashbackrule':
								    	$modelName = 'vipsetting';
								    	$function = 'addCashbackRule';
								    	break;
    							}
    							//insert data to table || restore data
    							foreach($read_data[$key] as $data){
    								$this->$modelName->$function($data);
    							}
    						}
    						//restore config_secret_local.php from backup file
   							rename('./backup-restore/backup-restore/config_secret_local.php', $_SERVER['HOME'].'/Code/og/secret_keys/config_secret_local.php');
    						//delete all files after restore data
							delete_files('./backup-restore',true);
    						$dataArray = array('success'=>1,'message'=>lang('bak.restoreDone'));
    					}else{
    						$dataArray = array('success'=>0,'message'=>lang('bak.backMissing'));
    					}
    				}else{
    					$dataArray = array('success'=>0,'message'=>lang('bak.invalidBak'));
    				}
    			}else{
    				$dataArray = array('success'=>0,'message'=>lang('bak.unableExtract'));
    			}
			}else{
    			$dataArray = array('success'=>0,'message'=>lang('bak.unableOpen').' '.$this->upload->file_name);
			}
    }
        //return $dataArray;
    $this->loadTemplate('System Management', '', '', 'system');
		$this->template->write_view('main_content', 'system_management/view_backup_manager', $dataArray);
		$this->template->render();
        //redirect('user_management/backupManager','refresh');
	}


}
