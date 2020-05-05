<?php

trait game_logs_module {

    /**
     * issue :  always missing original logs due to api rules(1 min per call).
     * resolution : fetch missing game logs using new api. (GetAllBetDetailsDV)
     *
     * default : auto sync
     * Command e.g : sudo ./command.sh sync_sa_gaming_api '2018-11-03'
     */
   	public function sync_sa_gaming_api($date = null, $force_merge = false) {

		$api = $this->utils->loadExternalSystemLibObject(SA_GAMING_API);
		if ($api) {
			$token = random_string('unique');

			if (empty($date)) {
				// if auto sync will get yesterday date base on date today
				$date = $this->utils->getTodayDateTimeRange();
				$dateTimeFrom = $date[0]->modify('-1 day');
				$dateTimeTo = $date[1]->modify('-1 day');
            } else {
                $dateTimeFrom = new \DateTime($date);
                $dateTimeFrom->setTime(0, 0, 0);

                $dateTimeTo = new \DateTime($date);
                $dateTimeTo->setTime(23, 59, 59);
			}

            $api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, null);
		    $rlt=$api->syncOriginalGameLogsDaily($token);

            if($rlt['success']) {
                if(isset($rlt['count']) && $rlt['count'] > 0) {
                    $api->syncMergeToGameLogs($token);
                    $start_date = $dateTimeFrom->format('Y-m-d H:i:s');
                    $end_date = $dateTimeTo->format('Y-m-d H:i:s');
                    if (!empty($start_date) && !empty($end_date)) {
                        $this->rebuild_totals($start_date, $end_date, true, true);
                    }
                } else {
                    if(isset($rlt['details'])) { // [details] => skip Syncing due to 1 call per minute restriction.
                        $this->utils->debug_log('sa gaming error', $rlt['details']);
                    } else {  // else already insert all for this date
                        $date = !empty($date) ? $date : date('Y-m-d', strtotime('-1 day', strtotime($date)));
                        $this->utils->debug_log('sa gaming error', "Already fetch data for this date [$date]");
                    }
                }
            }
			$this->utils->debug_log('sync SA GAMING daily', $rlt);
		} else {
			$this->returnText('load api failed');
		}
	}

    /**
     *  issue : api doesn't provide settlement datetime. only date. that's why we separate report
     * resolution : separate sbobet game report /report_management/viewSbobetGameReport
     *
     * default : auto sync
     */
    public function generate_sbobet_game_report_daily($date = null, $run_manual_sync= 'false'){
        if ($run_manual_sync != 'true') { # override feature checking if manual sync
            if(!$this->utils->isEnabledFeature('enabled_sbobet_sports_game_report')){
                $this->utils->debug_log('feature enabled_sbobet_sports_game_report disabled');
                return;
            }
        }

        if(!$date){
            $date = date('Y-m-d', strtotime('today'));
        }
        $this->load->model(['report_model']);
        $success=$this->report_model->generate_sbobet_game_report_daily($date);
        $this->utils->debug_log('generate_sbobet_game_report_daily', $date, $success);
    }
    public function generate_sbobet_game_report_by_date_range($date_from, $date_to, $run_manual_sync= 'false'){
        $date = $date_from;
        $end_date = $date_to;
        while (strtotime($date) <= strtotime($end_date)) {
            $this->generate_sbobet_game_report_daily($date, $run_manual_sync);
            $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
        }
        $this->utils->debug_log('generate_sbobet_game_report_by_date_range', $date_from ,$date_to);
    }

    /**
     * purpose : sync game play per sub provider
     *   format : sub_provider -> game_type
     *   sub_provider = [
            game_play => [slots, rslots, table],
            keno => [keno ladder pk10 rockpaperscisors thailottery],
            betsoft => null,
            ctxm => null,
            isoftbet => null,
            png => null,
            sbtech => null,
        ];
     * default : manual sync
     */
    public function sync_game_play_api($date_from=null ,$date_to=null, $sub_provider = 'game_play', $game_type='rslots'){
        $this->load->model(['report_model']);

        if (empty($date_from) && empty($date_to)) {
            $date_from = $this->utils->getTodayForMysql(). ' 00:00:00';
            $date_to =  $this->utils->getTodayForMysql(). ' 23:59:59';
        }

        $api = $this->utils->loadExternalSystemLibObject(GAMEPLAY_API);
        $dateTimeFrom = new DateTime($date_from);
        $dateTimeTo = new DateTime($date_to);

        $token = random_string('unique');
        $api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, null);
        $api->syncGamePlayPerSubProvider($token, $sub_provider, $game_type);
    }

    /**
     *  issue : need to adjust by sync id. when syncing previous game logs
     * resolution : created command to sync OG by date range, that won't affect current ID
     *
     * note : only sync original game logs
     * default : manual sync
     */
    public function sync_game_api_og($date_from=null ,$date_to=null){

        $this->load->model(['report_model']);

        if (empty($date_from) && empty($date_to)) {
            $date_from = $this->utils->getTodayForMysql(). ' 00:00:00';
            $date_to =  $this->utils->getTodayForMysql(). ' 23:59:59';
        }

        $api = $this->utils->loadExternalSystemLibObject(OG_API);

        $dateTimeFrom = new DateTime($date_from);
        $dateTimeTo = new DateTime($date_to);
        $startDate = $dateTimeFrom->format('Y-m-d H:i:s');
        $endDate = $dateTimeTo->format('Y-m-d H:i:s');

        $api->syncGameByDateTime($startDate, $endDate);
    }

    public function sync_game_api_goldenf_pgsoft($date_from = null ,$date_to = null) {
        if (empty($date_from) && empty($date_to)) {
            $date_from = $this->utils->getTodayForMysql(). ' 00:00:00';
            $date_to =  $this->utils->getTodayForMysql(). ' 23:59:59';
        }

        $api = $this->utils->loadExternalSystemLibObject(GOLDENF_PGSOFT_API);

        $dateTimeFrom = new DateTime($date_from);
        $dateTimeTo = new DateTime($date_to);
        $startDate = $dateTimeFrom->format('Y-m-d H:i:s');
        $endDate = $dateTimeTo->format('Y-m-d H:i:s');

        $api->syncGameByDateTime($startDate, $endDate);
    }

    /**
     * issue : original game logs can only be search through bet time, that's why some record can merge on time
     *         specially if match will end more than 1 days ahead
     * resolution : get all unsettled games, from 60 days ago to know. then resync
     *
     * default : auto sync
     */
    public function resync_ipm_unsettle_game_records($set_date_limit = 'true') {
        $set_date_limit = $set_date_limit == 'true';
        $api = $this->utils->loadExternalSystemLibObject(SPORTSBOOK_API);
        $dates = $api->searchAndResyncSettledGamesByBetTime($set_date_limit);

        if(!empty($dates)) { // try rebuild
            foreach($dates as $date) {
                list($from, $to) = $this->getDateRangeTime($date);
                $start_date = $from->format('Y-m-d H:i:s');
                $end_date = $to->format('Y-m-d H:i:s');

                if (!empty($start_date) && !empty($end_date)) {
                    $this->rebuild_totals($start_date, $end_date, true, true);

                    $rep_date_from = $from->format('Y-m-d');
                    $rep_date_to =  $to->modify('+1 day')->format('Y-m-d');

                    $date =  new DateTime($date);
                    $match_date  = new DateTime($rep_date_to);
                    $interval =  $date->diff($match_date);
                    if($interval->days != 0) { // generate only if date is not today
                        $this->generate_all_report_from_to($rep_date_from,$rep_date_to);
                    }
                }
            }
        }
    }

    /**
     * MISC
     */
    public function getDateRangeTime($date) {
        $dateTimeFrom = new \DateTime($date);
        $dateTimeFrom->setTime(0, 0, 0);
        $dateTimeTo = new \DateTime($date);
        $dateTimeTo->setTime(23, 59, 59);

        return array($dateTimeFrom, $dateTimeTo);
    }



    protected function processAndExecuteExport($client_csv_download_base_url,$current_time,$export_details,
        $game_platform_name_map,$cover_from,$cover_to,$mm_key,$mm_user,$isManualExport){

        $cmd = null;
        $cmd_str = null;
        $php_str=$this->utils->find_out_php();
        $og_admin_home = realpath(dirname(__FILE__) . "/../../../");
        $export_details_group_by = [];
        $export_details_group_by_details = [];
        $for_clients_view = null;
        $for_t1s_view = null;

        $client_tag = [
            ':information_source:',
            '#'.$mm_key,
            '#export_year_'. $current_time->format('Y'),
            '#export_month_'. $current_time->format('Ym'),
            '#export_day_'. $current_time->modify('+1 day')->format('Ymd'),
            '#export_hour_'. $current_time->format('Ymdh'),
            '#export_minute_'. $current_time->format('Ymdhi'),
        ];

         //CLI
        foreach ($export_details as $currency_key => $export_detail) {

            $export_details_by_currency =   $export_detail['export_details'];

            foreach ($export_details_by_currency  as $value) {
                $dbEnv = "\n".'__OG_TARGET_DB='.$currency_key;
                $function = 'do_remote_export_csv_job';
                $cmd_str .= $dbEnv.' '.$php_str.' '.$og_admin_home.'/shell/ci_cli.php cli/command/'.$function.' "'.$value['token'].'"'."\n";
                $cmd .=  $dbEnv.' '.$php_str.' '.$og_admin_home.'/shell/ci_cli.php cli/command/'.$function.' "'.$value['token'].'"';
                $cmd .=   "\n"."wait";
                $from_to = " (".$value['from_date'].' - '.$value['to_date'].")";

                $export_details_group_by[$value['currency_db']][$value['game_api_id']][] = $value['client_csv_download_base_url'].'/export_data/queue/'.$value['token'];
                $export_details_group_by_details[$value['currency_db']][$value['game_api_id']][] = [
                    $value['client_csv_download_base_url'].'/export_data/queue/'.$value['token'],
                    $from_to,
                    $dbEnv,
                    $function = 'do_remote_export_csv_job',
                    $value['token'],
                ];
            }
        }
        //CLIENTS
        foreach ($export_details_group_by as $currency_key => $link_details) {
            foreach ($link_details as $platform_id => $links) {
                $for_clients_view = ":point_right: **FOR SENDING TO CLIENT**";
                $for_clients_view .= "\n";
                $j=1;
                $link_count = count($links);
                $for_clients_view .= '**'.$game_platform_name_map[$platform_id] .' ['.$cover_from.' - '.$cover_to.']** ';
                $for_clients_view .= "\n";
                $for_clients_view .= "```";
                $for_clients_view .= "\n";
                $for_clients_view .= "\n";
                foreach ($links as $link) {
                    $for_clients_view .=  'Part '.$j.' of '.$link_count;
                    $for_clients_view .=  "\n";
                    $for_clients_view .=  $link;
                    $for_clients_view .=  "\n";
                    $j++;
                }
                $for_clients_view .= "```";
                $for_clients_view .=  "\n";
                $client_tag[7] = '#'.$currency_key;
                $client_tag[8] = '#GAME_PLATFORM_ID_'.$platform_id;
                if($isManualExport){
                    $client_tag[9] ='#manual_export';
                }
                $this->sendNotificationToMattermost($mm_user, $mm_key, $for_clients_view, 'info', $client_tag);
                sleep(1);
            }
        }

       //T1
        foreach ($export_details_group_by_details as $currency_key => $link_details) {
            foreach ($link_details as $platform_id => $links) {
                $for_t1s_view  = ":point_down: **FOR T1's DATA ANALYSTS VIEW | YOU CAN MANUAL REEXPORT ONE BY ONE BELOW** :point_down:";
                $for_t1s_view .=  "\n";
                $k=1;
                $link_count = count($links);
                $for_t1s_view .= '**['.$currency_key.'] ['.$platform_id.'] '.$game_platform_name_map[$platform_id] .' ['.$cover_from.' to '.$cover_to.']** ';
                $for_t1s_view .=  "\n";
                $for_t1s_view .=  "```";
                $for_t1s_view .=  "\n";
                $for_t1s_view .= "\n";
                $for_t1s_view .=  "#Follow up CLI command:";
                $for_t1s_view .= "\n";
                $for_t1s_view .= "\n";

                foreach ($links as $link) {
                    $for_t1s_view .=  '#Part '.$k.' of '.$link_count;
                    $for_t1s_view .=  $link[0];
                    $for_t1s_view .=  $link[1];
                    $for_t1s_view .=  "\n";
                    $for_t1s_view .=  $link[2].' '.$php_str.' '.$og_admin_home.'/shell/ci_cli.php cli/command/'.$link[3].' "'.$link[4].'"';
                    $for_t1s_view .=  "\n";
                    if($k != $link_count){
                       $for_t1s_view .=  "wait";
                       $for_t1s_view .=  "\n";
                   }
                $for_t1s_view .=  "\n";
             
                $k++;
            }
            $for_t1s_view .= "```";
            $for_t1s_view .=  "\n";
            $client_tag[7] = '#'.$currency_key;
            $client_tag[8] = '#GAME_PLATFORM_ID_'.$platform_id;
            // if($isManualExport){
            //     $client_tag[9] ='#manual_export';
            // }
            $this->sendNotificationToMattermost($mm_user, $mm_key, $for_t1s_view, 'info', $client_tag);
            sleep(1);
        }

     }

    unset($client_tag[7]);
    unset($client_tag[8]);

    $msg = "[".$current_time->format('Y-m-d H:i:s')."] Hostname: ". $this->utils->getHostname()." PID: ".getmypid();
    $msg .= "\n";
    // $msg .= "\n";
    // $msg .= $for_clients_view;
    // $msg .= "\n";
    // $msg .= $for_t1s_view;
    $msg .= "\n";
    $msg .= ":warning: **THIS IS ONLY SUMMARY OF EXPORT EXECUTION. PLEASE WAIT UNTIL IT IS FINISHED! A NOTIFICATION WILL BE SENT AFTER THIS.**";
    $msg .= "\n";
    $msg .= ":point_right: **YOU CAN VIEW EXPORT JOBS STATUS PER CURRENCY AT THIS URL ".$client_csv_download_base_url."/system_management/view_task_list **";
    $msg .= "\n";
    $msg .= ":point_right: **YOU CAN EXECUTE MANUALLY AT DIFFERENT DATE. EX. sudo ./command.sh exportGamelogsThruCronjob '2019-05-09' 'TRUE' (Note: add 1 day based on the target date.)**";

    $this->sendNotificationToMattermost($mm_user, $mm_key, $msg, 'info', $client_tag);

    $uniqueid=random_string('md5');
            //app log
        $log_dir=BASEPATH.'/../application/logs/tmp_shell'; //.$this->_app_prefix;
        // $log_dir='/tmp/'.$this->_app_prefix;
        if(!file_exists($log_dir)){
            @mkdir($log_dir, 0777 , true);
        }
        $title = __FUNCTION__;
        $func = $title;

        $noroot_command_shell=<<<EOD
#!/bin/bash

echo "start {$title} `date`"

start_time=`date "+%s"`
{$cmd}
end_time=`date "+%s"`

echo "Total run time: `expr \$end_time - \$start_time` (s)"
echo "done {$title} `date`"

EOD;
        $tmp_shell=$log_dir.'/'.$func.'_'.$uniqueid.'.sh';
        file_put_contents($tmp_shell, $noroot_command_shell);

        $cmd='bash '.$tmp_shell;
        exec($cmd);
        $this->sendNotificationToMattermost($mm_user, $mm_key, ':wave:['.$this->utils->formatDateTimeForMysql(new \DateTime()).'] Done Exporting at PID: '.getmypid().':wave:', 'info', $client_tag);
    }


    public function exportGamelogsThruCronjob($day=_COMMAND_LINE_NULL,$isManualExport='FALSE'){

        $config = $this->utils->getConfig('export_gamelogs_thru_cron_settings');

        if(empty($config)){
            return $this->utils->info_log('Please setup export_gamelogs_thru_cron_settings config');
        }
        if($config['is_disable'] === true){
            return $this->utils->info_log('config export_gamelogs_thru_cron_settings is in disable mode');
        }
        if(empty($config['mattermost_key']) || empty($config['mattermost_key'])){
            return $this->utils->info_log('mattermost most key and user is missing!');
        }
        $mm_key = $config['mattermost_key'];
        $mm_user = $config['mattermost_user'];

        $current_time  = new \DateTime();

        if($day != _COMMAND_LINE_NULL){
           //$day = null;
           $current_time  = new \DateTime($day);
        }

        $isManualExport = (strtoupper($isManualExport)=='FALSE') ? FALSE : TRUE;

        $today_str = $current_time->format('Y-m-d');
        $yesterday = $current_time->modify('-1 day');
        $yesterday_str = $yesterday->format('Y-m-d');
        $export_times = $config['export_times'];
        $client_csv_download_base_url = $config['client_csv_download_base_url'];
        $params = json_decode($config['param_json_template'], TRUE);
        $game_apis_per_currency_map = $config['game_apis_per_currency'];
        $language_function = $this->load->library('language_function');
        $game_platform_name_map = [];
        $cover_from = null;
        $cover_to = null;
        $i=1;
        $export_times_count =  count($export_times);
        foreach ($export_times as $v) {

            if($i==1){
                if($v['fromYesterday']){
                    $cover_from = $yesterday_str.' '.$v['from'];
                }else{
                    $cover_from = $today_str.' '.$v['from'];
                }
            }

            if($i==$export_times_count){
                if($v['untilToday']){
                    $cover_to = $today_str.' '.$v['to'];
                }else{
                    $cover_to = $yesterday_str.' '.$v['to'];
                }
            }
            $i++;
        }

         ///create queue
        if($this->utils->isEnabledMDB()){

            $export_details = $this->utils->foreachMultipleDBToCIDB(function($db,$db_name)
             use ($params,$language_function,$game_apis_per_currency_map,&$game_platform_name_map,
                $export_times,$today_str,$yesterday_str,$client_csv_download_base_url){

                $lang=$this->language_function->getCurrentLanguage();
                $funcName='remote_export_csv';
                $caller=0;
                $callerType=Queue_result::CALLER_TYPE_SYSTEM;
                $state=null;
                //check if currency exist on config
                if(isset($game_apis_per_currency_map[$db_name])){
                    $game_apis_per_currency = $game_apis_per_currency_map[$db_name]['game_api_ids'];

                    $db
                    ->select('game_type.*, external_system.system_code as game_platform_name')
                    ->from('game_type')
                    ->join('external_system', 'game_type.game_platform_id = external_system.id');
                    $game_platform_details =  $this->db->get()->result_array();


                    $game_platform_details_map = [];
                    $game_types_ids =[];

                    foreach ($game_platform_details as $key => $value) {

                        if(in_array($value['game_platform_id'], $game_apis_per_currency)){
                            $game_platform_details_map[$value['game_platform_id']][]=$value['id'];
                            $game_platform_name_map[$value['game_platform_id']]=$value['game_platform_name'];
                        }
                    }

                    $export_details_arr = [];

                    foreach ($game_platform_details_map  as $apiId => $gt_ids) {
                        $extra_search = $params[0]['extra_search'];
                        $game_type_str = implode(",", $gt_ids);
                        foreach ($export_times as $export_time) {
                            $from_date = '';
                            $to_date = '';
                            foreach ($extra_search as  &$form_values) {

                                if(isset($form_values['value'])){

                                    $form_name = $form_values['name'];
                                    switch ($form_name) {
                                    case 'game_type_id':
                                    $form_values['value'] = $game_type_str ;
                                    break;
                                    case 'by_game_platform_id':
                                    $form_values['value'] = $apiId ;
                                    break;
                                    case 'by_date_from':
                                    if($export_time['fromYesterday']){
                                        $from_date = $form_values['value'] = $yesterday_str.' '.$export_time['from'];
                                    }else{
                                        $from_date = $form_values['value'] = $today_str.' '.$export_time['from'];
                                    }
                                    break;
                                    case 'by_date_to':
                                    if($export_time['untilToday']){
                                        $to_date = $form_values['value'] = $today_str.' '.$export_time['to'];
                                    }else{
                                        $to_date = $form_values['value'] = $yesterday_str.' '.$export_time['to'];
                                    }
                                    break;
                                    default:
                                    # code...
                                    break;
                                    }
                                }
                            }//each extra search
                            $params[0]['extra_search'] = $extra_search;
                            $token=  $this->createQueueOnCommand($funcName, $params,$lang , $callerType, $caller, $state);
                            $export_details = [];
                            $export_details['token'] = $token;
                            $export_details['game_api_id'] = $apiId;
                            $export_details['game_platform_name']=$game_platform_name_map[$apiId];
                            $export_details['game_type_ids'] = $game_type_str;
                            $export_details['currency_db'] = $db_name;
                            $export_details['from_date'] = $from_date;
                            $export_details['to_date'] = $to_date;
                            $export_details['client_csv_download_base_url'] = $client_csv_download_base_url;
                             // $export_details['full_param'] = $params;
                            array_push($export_details_arr, $export_details);
                        }//export times
                    }//gt ids
                    return array('export_details' =>$export_details_arr);
                }
            });//loop

            $export_details=    array_filter($export_details);//remove empty array (currency)
            $this->utils->info_log('export details:',$export_details);
            $this->processAndExecuteExport($client_csv_download_base_url,$current_time,$export_details,$game_platform_name_map,$cover_from,$cover_to,$mm_key,$mm_user,$isManualExport);
        }//if multidb
    }

     protected function calculate_date_range($by,$manual_date,$number_range){

        $last_day=null; $max_day=null; $start_day=null; $start_day_minus_one_day=null;
        $d1=null;$d2=null;$d3=null;$d4=null;

        $add=$number_range+1;

        if($manual_date !=_COMMAND_LINE_NULL){
            $d1 = new Datetime($manual_date);
            $d2 = new Datetime($manual_date);
            $d3 = new Datetime($manual_date);
            $d4 = new Datetime($manual_date);
        }

        switch ($by) {

            case 'by_day':
            case 'by_days':

            if($manual_date !=_COMMAND_LINE_NULL){
                $last_day =  $d1->modify('-'.$number_range.'  days')->format('Y-m-d');
                $max_day =  $d2->modify($last_day.' -1 day')->format('Y-m-d');
                $start_day =  $d3->modify($max_day.'-'.$number_range.' days')->format('Y-m-d');
                $start_day_minus_one_day =  $d4->modify($max_day.'-'.$add.' days')->format('Y-m-d');
            }else{
                $last_day =  date("Y-m-d", strtotime('-'.$number_range.'  days'));
                $max_day = date("Y-m-d", strtotime($last_day.' -1 day'));//subtract 1 day
                $start_day = date("Y-m-d", strtotime($max_day.'-'.$number_range.' days'));
                $start_day_minus_one_day = date("Y-m-d", strtotime($max_day.'-'.$add.' days'));
                }
                break;
                case 'by_month':
                case 'by_months':
                $add=$number_range+1;
                if($manual_date !=_COMMAND_LINE_NULL){
                    $last_day =  $d1->modify('-'.$number_range.'  months')->format('Y-m-d');
                    $max_day =  $d2->modify($last_day.' -1 day')->format('Y-m-d');
                    $start_day =  $d3->modify($max_day.'-'.$number_range.' months')->format('Y-m-d');
                    $start_day_minus_one_day =  $d4->modify($max_day.'-'.$add.' months')->format('Y-m-d');

                }else{
                    $last_day =  date("Y-m-d", strtotime('-'.$number_range.'  months'));
                    $max_day = date("Y-m-d", strtotime($last_day.' -1 day'));//subtract 1 day
                    $start_day = date("Y-m-d", strtotime($max_day.'-'.$number_range.' months'));
                    $start_day_minus_one_day = date("Y-m-d", strtotime($max_day.'-'.$add.' months'));
                }
                break;
        } //switch
        //minus_one_day<--start_day<----------max_day<--last_day<----------now|manual_date
        return array($start_day,$max_day,$last_day,$start_day_minus_one_day);


    }

    protected function runDeleteData($table, $date_field, $from, $to,$dry_run,$mattermost_key=_COMMAND_LINE_NULL, $token=_COMMAND_LINE_NULL){

        $sql='DELETE FROM `'.$table.'` WHERE `'.$date_field."`>='".$from."' and ".$date_field."<='".$to."'";

        $deletedNumber=0;
        if($dry_run){
                //ignore
        }else{
            $q=$this->db->query($sql);
            $deletedNumber=$this->db->affected_rows();
        }
        $this->utils->debug_log('after exec sql, affected_rows', $deletedNumber, 'sql', $sql);
        return $deletedNumber;

    }


    protected function runCmdExportSql($sql_file,$db_settings,$from,$to){

        $uniqueid=random_string('md5');
         //app log
        $log_dir=BASEPATH.'/../application/logs/tmp_shell'; //.$this->_app_prefix;

        if(!file_exists($log_dir)){
            @mkdir($log_dir, 0777 , true);
        }
        $title = __FUNCTION__;
        $func = $title;
        //$log_file=$log_dir.'/job_'.$func.'_'.$uniqueid.'.log';

        $host=$db_settings['hostname'];
        $database=$db_settings['database'];
        $username=$db_settings['username'];
        $password=$db_settings['password'];
        $port=$db_settings['port'];
        $table=$db_settings['target_table'];
        $table_date_field=$db_settings['date_field'];
        //--compact option Produce more compact output. This option enables the --skip-add-drop-table, --skip-add-locks, --skip-comments, --skip-disable-keys, and --skip-set-charset options.
        $noroot_command_shell=<<<EOD
#!/bin/bash

echo "start {$title} `date`"

start_time=`date "+%s"`

#touch {$sql_file}

where=" {$table_date_field}  >= '{$from}' AND {$table_date_field} <= '{$to}' "

mysqldump -h{$host} -u{$username} -p{$password} -P {$port} {$database}  --tables {$table} --compact --single-transaction --quick --insert-ignore --no-create-info  --set-gtid-purged=OFF --where="\$where" > {$sql_file}
wait
gzip -f {$sql_file}
end_time=`date "+%s"`

echo "Total run time: `expr \$end_time - \$start_time` (s)"
echo "done {$title} `date`"

EOD;

        $tmp_shell=$log_dir.'/'.$func.'_'.$uniqueid.'.sh';
        file_put_contents($tmp_shell, $noroot_command_shell);

        $cmd='bash '.$tmp_shell;
        $str = exec($cmd, $output, $return_var);

        //delete shell
        unlink($tmp_shell);
        $this->utils->debug_log(__FUNCTION__.' delete tmp shell: '.$tmp_shell);
        $this->utils->debug_log("runCmdExportSql done exec ", $str, 'return: '.$return_var);
        unset($noroot_command_shell);
        //wait to finish -   dont run in background
        if(isset($output)){
            return array('success'=>($return_var==0));
        }

    }




    protected function runArchiveData($from,$to,$table_name,$save_to_table,$date_field,$token,$use_index_str=null,$dry_run=false){

        //track source table number of columns
        $source_fields = $this->db->list_fields($table_name);
        $source_tbl_fields_count  = count($source_fields);

        $sql='SELECT *  FROM `'.$table_name.'` WHERE `'.$date_field."`>='".$from
        ."' and ".$date_field."<'".$to."'";

        if(!empty($use_index_str)){
            $sql='SELECT *  FROM `'.$table_name.'`  '.$use_index_str.' WHERE `'.$date_field."`>='".$from
            ."' and ".$date_field."<'".$to."'";
        }

        $query =  $this->db->query($sql);
        $rows_count = $query->num_rows();
        $rows = $query->result_array();
        if($rows_count>0){
            $this->utils->info_log('archiveDataBeforeDelete results','total_rows_found',$rows_count, 'sql',$sql);
        }
        $is_done= false;
        $i=0;
        if($rows_count  > 0){
            foreach ($rows as $row) {
                $dt = new DateTime($row[$date_field]);
                $yearMonthStr  = $dt->format('Ym');
                $save_to_table_ym = $save_to_table.'_'.$yearMonthStr;

                if($this->utils->table_really_exists($save_to_table_ym)){
                    $target_fields = $this->db->list_fields($save_to_table_ym);
                    $target_tbl_fields_count  = count($target_fields);
                    // check if source has been altered by someone - columns added or removed
                    if($source_tbl_fields_count > $target_tbl_fields_count || $source_tbl_fields_count < $target_tbl_fields_count ){
                        $client_tag = [
                            ':warning:',
                            '#delete_and_archive_warning',
                            '#'.str_replace("og_","",$this->_app_prefix),
                            '#data_deletion'. $this->utils->formatYearMonthForMysql(new DateTime),
                            '#job_'.$token
                        ];
                        $comparison = [
                           'sourceTable'=> $table_name,
                           'sourceFields' => $source_fields,
                           'targetTable' => $save_to_table_ym,
                           'targetFields' => $target_fields
                       ];

                       $details = ['warning' => 'Altered source table detected! Please check archive table columns vs source table columns and manually fix it!', 'details' => $comparison ];
                       $msg = "Delete start time: ".$this->utils->getNowForMysql()." | Hostname: ". $this->utils->getHostname();
                       $msg .= "\n";
                       $msg .= "``` json \n".json_encode($details, JSON_PRETTY_PRINT)." \n```";
                       $this->sendNotificationToMattermost('Delete and Archive Data', 'delete_table_data', $msg, 'warning', $client_tag);
                       $this->queue_result->appendResult($token, ['request_id'=>_REQUEST_ID, 'result'=> true, 'details'=> $details], false, false);
                   }
               }else{
                    //normal flow - create back up table
                $this->utils->initArchiveTableByYearMonth($table_name, $save_to_table_ym);
                $this->utils->info_log('archiveDataBeforeDelete archive table',$table_name, $save_to_table_ym);
            }

            $update_row = [];
            foreach ($row as $field => $value) {

                    $value_ref = $value;// make reference for checking
                    $value = ($value==NULL || empty($value)) ? 'NULL': "'".$value."'"; //make null value

                    if($value_ref == "0"){//fix zero values
                      $value = 0;
                  }

                  if($value_ref!= strip_tags($value_ref)){
                      // is HTML
                      $value =  json_encode($value); //fix  html tags causes errors -solved by json encode
                  }

                  $update_row[] = "`".$field."` = ".$value;
              }
              $this->db->query($this->db->insert_string($save_to_table_ym, $row).' ON DUPLICATE KEY UPDATE  '.implode(", ",$update_row));

              if($i == ($rows_count - 1) ){
                $is_done=true;
            }
            $i++;
        }
    }

    $this->utils->debug_log('archiveDataBeforeDelete', $sql, $from, $to);
    if($is_done){
        return array('is_done'=>true, 'count'=>$rows_count);
    }

}



    public function cron_daily_export_sql_and_delete_data($manual_date=_COMMAND_LINE_NULL,$config_key=_COMMAND_LINE_NULL){

        $config_settings = $this->utils->getConfig('table_export_sql_and_deletions_settings');

        $export_delete_day_obj = new Datetime();
        $export_delete_day = $export_delete_day_obj->format('Ymd');

        if($config_key !=_COMMAND_LINE_NULL){
            $custom_config = $this->utils->getConfig($config_key);
           if(!empty($custom_config)){
            $config_settings = $custom_config;
           }
        }
        $funcName=__FUNCTION__;
        $params = $config_settings;
        $settings = $config_settings['settings'];
        $client = $config_settings['client'];
        $mattermost_key = $config_settings['mattermost_key'];
        $client_url = $config_settings['client_url'];
        $sql_back_up_path = $config_settings['sql_back_up_path'];


        $caller=0;
        $callerType=Queue_result::CALLER_TYPE_SYSTEM;
        $state=null;
        $lang=null;
        $token=$this->createQueueOnCommand($funcName, $params, $lang , $callerType, $caller, $state);

        $msg = '';
        $client_tag = [
            ':information_source:',
            '#'.str_replace("og_","",$this->_app_prefix).' #'.$client,
            '#export_delete'. $this->utils->formatYearMonthForMysql(new DateTime).' #export_delete_'. $export_delete_day,
        ];
        if($manual_date !=_COMMAND_LINE_NULL){
            array_push($client_tag, '#manual_date_'.$manual_date);
        }
        if(!empty($settings)){
            array_push($client_tag, $client_url.'/system_management/common_queue/'.$token);
            $msg .= __FUNCTION__." start time: ".$this->utils->getNowForMysql()." | Hostname: ". $this->utils->getHostname();
            $msg .= "\n";
            $msg .= "``` json \n".json_encode($config_settings, JSON_PRETTY_PRINT)." \n```";
            $this->sendNotificationToMattermost(__FUNCTION__, $mattermost_key, $msg, 'info', $client_tag);

        }else{
            $this->utils->error_log('cron_daily_export_sql_and_delete_data is empty');
            return;
        }

        $active_db_key = $this->utils->getActiveTargetDB();
        $is_multi_db = $this->utils->isEnabledMDB();
        $db_settings=[];

        if($is_multi_db === true){
            $db_settings_map = $this->utils->getConfig('multiple_databases');
            if(isset($db_settings_map[$active_db_key]['default'])){
                 $db_settings=$db_settings_map[$active_db_key]['default'];
            }
        }else{
           $db_settings=array(
            'hostname' => $this->utils->getConfig('db.default.hostname'),
            'username' => $this->utils->getConfig('db.default.username'),
            'password' => $this->utils->getConfig('db.default.password'),
            'database' => $this->utils->getConfig('db.default.database'),
            'port' => $this->utils->getConfig('db.default.port'),
            );
        }

        if(empty($db_settings)){
            $this->utils->error_log('empty connection setting', 'active_db_key',$active_db_key );
            return;
        }
        $filepath=null;
        $csvHeader = ['table','is_export_success', 'delete_count','from_date','to_date','sql_file'];
        if($is_multi_db === true){
            $this->utils->_appendSaveDetailedResultToRemoteLog($token, $active_db_key.'_cron_daily_export_sql_and_delete_data',  null, $filepath, true, $csvHeader);
        }else{
            $this->utils->_appendSaveDetailedResultToRemoteLog($token, 'cron_daily_export_sql_and_delete_data',  null, $filepath, true, $csvHeader);
        }

        $settings_cnt = count($settings);
        $i=0;
        $sqls_file_path = $sql_back_up_path;

        foreach($settings as $table => $setting) {
             $i++;
            //default
            $delete_by = 'by_months'; //by_days
            $step = '+24 hours';
            $dry_run = $setting['dry_run'];
            $delete_data_after_backup=$setting['delete_data_after_backup'];
            $export_sleep = $setting['export_sleep'];
            $delete_sleep = $setting['delete_sleep'];
            $delete_last_month_dir_not_in_range = $setting['delete_last_month_dir_not_in_range'];
            $only_start_at_max_day = $setting['only_start_at_max_day'];
            $overwrite_file = $setting['overwrite_file'];

             if($is_multi_db === true){
                if(array_key_exists('target_db_keys', $setting) ){
                    $target_db_keys = $setting['target_db_keys'];
                    if(!in_array($active_db_key, $target_db_keys) ){
                        //return;
                        continue;// skip
                    }
                }
            }
            if(array_key_exists('delete_by', $setting)){
                $delete_by = $setting['delete_by'];
            }
            if(array_key_exists('delete_step', $setting)){
                $step = $setting['delete_step'];
            }

            $table_name = $table;
            $date_field =   $setting['date_field'];
            $number_range = $setting['number_range'];
            //add to settings
            $db_settings['target_table'] = $table;
            $db_settings['date_field'] = $date_field;

            list($start_day,$max_day,$last_day,$delete_day) = $this->calculate_date_range($delete_by,$manual_date,$number_range);

            $this->utils->info_log('cron_daily_export_sql','delete_day',$delete_day, 'start_day', $start_day, 'max_day', $number_range);

            if(!empty($sql_back_up_path)){

                if(!file_exists($sql_back_up_path)){
                    mkdir($sql_back_up_path, 0777);
                    chmod($sql_back_up_path, 0777);
                }

            }else{
                $sqls_file_path = $this->utils->getSharingUploadPath('/remote_sqls');
            }

            //delete month not in range
            if($delete_last_month_dir_not_in_range === true){

                $day_obj1 = new Datetime($start_day);
                $day_obj2 =   new Datetime($start_day);

                $startday_month = $day_obj1->format('Ym');
                $delete_month = $day_obj2->modify('-1 months')->format('Ym');

                if($delete_month < $startday_month){
                   $month_dir = $sqls_file_path.'/'.$delete_month;
                   if($is_multi_db === true){
                    if (!is_dir($active_db_dir)) {
                        mkdir($active_db_dir, 0777, true);
                    }
                    $month_dir = $sqls_file_path.'/'.$active_db_key.'/'.$delete_month;
                }
                exec('rm -rf '.$month_dir);
                }
            }
            //extract month from delete day to get the right dir path then delete day folder to retain 2nd batch backup
            $month_dir_for_delete_day = (new DateTime($delete_day))->format('Ym');
            $month_dir = $sqls_file_path.'/'.$month_dir_for_delete_day;

            if($is_multi_db === true){
               $active_db_dir = $sqls_file_path.'/'.$active_db_key;
               if (!is_dir($active_db_dir)) {
                    mkdir($active_db_dir, 0777, true);
                }
              $month_dir = $sqls_file_path.'/'.$active_db_key.'/'.$month_dir_for_delete_day;
            }

            if (is_dir($month_dir.'/'.$table.'/'.$delete_day)) {
                exec('rm -rf '.$month_dir.'/'.$table.'/'.$delete_day);
            }

            if($only_start_at_max_day === true){
                $start_day = $max_day.' '.Utils::FIRST_TIME;
                $max_day = $max_day.' '.Utils::LAST_TIME;
            }else{
                $start_day = $start_day.' '.Utils::FIRST_TIME;
                $max_day = $max_day.' '.Utils::LAST_TIME;
            }

            $controller=$this;
            $delete_cnt = 0;
            $is_export_done = false;

            $success=$this->utils->loopDateTimeStartEnd($start_day, $max_day, $step, function($from, $to, $step)
                use(&$dateList,$mattermost_key,$controller,$is_multi_db,$active_db_key,$export_sleep,$delete_sleep, $delete_data_after_backup,
                    $table,$db_settings,$overwrite_file,$sqls_file_path,$month_dir_for_delete_day,$delete_day,&$delete_cnt,$date_field,$dry_run,$is_export_done,$token){

            $this->utils->debug_log($from, $to, $step);

            $from_datetime_str = $this->utils->formatDateTimeForMysql($from);
            $to_datetime_str = $this->utils->formatDateTimeForMysql($to);

            $from_datetime_for_filename= (new DateTime($from_datetime_str))->format('Y_m_d_H_i_s');
            $to_datetime_for_filename= (new DateTime($to_datetime_str))->format('Y_m_d_H_i_s');

            $year_month_str = $this->utils->formatYearMonthForMysql($from);
            $from_day_str = $this->utils->formatDateForMysql($from);
            $to_day_str = $this->utils->formatDateForMysql($to);

            $month_dir = $sqls_file_path.'/'.$year_month_str;

            if($this->utils->isEnabledMDB()){
                $month_dir = $sqls_file_path.'/'.$active_db_key.'/'.$year_month_str;
            }

            if (!is_dir($month_dir)) {
                mkdir($month_dir, 0777, true);
            }

            $table_dir = $month_dir.'/'.$table;
            if (!is_dir($table_dir)) {
                mkdir($table_dir, 0777, true);
            }

            $day_dir = $month_dir.'/'.$table.'/'.$from_day_str;
            if (!is_dir($day_dir)) {
                mkdir($day_dir, 0777, true);
            }

            $dumpfilename=$table.'-'.$from_datetime_for_filename.'_to_'.$to_datetime_for_filename.'.sql';

            $sql_file = $day_dir.'/'.$dumpfilename;
            $success = false;
            $export_rlt = null;
            $new_delete_cnt=0;
            if(file_exists($day_dir.'/'.$dumpfilename.'.gz')){
                if($overwrite_file === true){
                    $export_rlt = $this->runCmdExportSql($sql_file,$db_settings,$from_datetime_str,$to_datetime_str,$token);
                }
            }else{
                if($to_day_str != $delete_day){
                    $export_rlt = $this->runCmdExportSql($sql_file,$db_settings,$from_datetime_str,$to_datetime_str,$token);
                }
            }

            if($export_sleep>0){
                sleep($export_sleep);
            }

            if($delete_data_after_backup === true){
                if (isset($export_rlt) && $export_rlt['success'] === true ){
                    if($delete_sleep>0){
                        sleep($delete_sleep);
                    }
                    $new_delete_cnt = $controller->runDeleteData($table, $date_field, $from_datetime_str, $to_datetime_str,$dry_run,$mattermost_key, $token);
                    $delete_cnt = $delete_cnt + $new_delete_cnt;
                }
            }

            $log_msg = [$table, ($export_rlt['success'] === true) ? 'true' : 'false',$new_delete_cnt,$from_datetime_str,$to_datetime_str,$day_dir.'/'.$dumpfilename.'.gz'];
            $csvHeader=[];
            if($is_multi_db === true){
                $this->utils->_appendSaveDetailedResultToRemoteLog($token, $active_db_key.'_cron_daily_export_sql_and_delete_data',  $log_msg, $filepath, true, $csvHeader);
            }else{
                $this->utils->_appendSaveDetailedResultToRemoteLog($token, 'cron_daily_export_sql_and_delete_data', $log_msg, $filepath, true, $csvHeader);
            }
            $this->utils->info_log('export and delete status','table',$table, 'is_export_success',($export_rlt['success'] === true),'delete_cnt', $new_delete_cnt,'from',$from_datetime_str,'to',$to_datetime_str, 'gzip', $day_dir.'/'.$dumpfilename.'.gz' );

            return true;
           });

            if($token != _COMMAND_LINE_NULL){
                $client_tag = [
                    ':point_right:',
                    '#'.str_replace("og_","",$this->_app_prefix),
                    '#export_delete'. $this->utils->formatYearMonthForMysql(new DateTime).' #export_delete_'. $export_delete_day.' #'.$table,
                    '#job_'.$token
                ];

                $result = ['table'=>$table, 'date_field'=> $date_field, 'start_day' => $start_day, 'max_day' =>  $max_day, 'total_deleted_cnt'=> $delete_cnt,'remote_log_path' => site_url().'remote_logs/'.basename($filepath)];
                $is_final_setting = ($i == $settings_cnt);//for task progress view
                $this->queue_result->appendResult($token, ['request_id'=>_REQUEST_ID, 'result'=> $result ] , $is_final_setting, false);

                $msg = "``` json \n".json_encode(['table' => $table, __FUNCTION__.'_status'=>'done','process_end_time'=>$this->utils->getNowForMysql(), 'result' => $result])." \n```";
                $this->sendNotificationToMattermost(__FUNCTION__, $mattermost_key, $msg, 'info', $client_tag);
            }

        }//foreach end

   }


    public function cron_daily_archive_and_delete_data($manual_date=_COMMAND_LINE_NULL,$config_key=_COMMAND_LINE_NULL,$token=_COMMAND_LINE_NULL){

        $config_settings = $this->utils->getConfig('table_archive_and_deletions_settings');
        $this->load->model(['player_model']);

        $archive_delete_day_obj = new Datetime();
        $archive_delete_day = $archive_delete_day_obj->format('Ymd');
        if($config_key !=_COMMAND_LINE_NULL){
            $custom_config = $this->utils->getConfig($config_key);
           if(!empty($custom_config)){
            $config_settings = $custom_config;
           }
        }
        $funcName=__FUNCTION__;
        $params = $config_settings;

        if($token == _COMMAND_LINE_NULL){
            $caller=0;
            $callerType=Queue_result::CALLER_TYPE_SYSTEM;
            $state=null;
            $lang=null;
            $token=$this->createQueueOnCommand($funcName, $params, $lang , $callerType, $caller, $state);
        }else{
           $data =  $this->initJobData($token);
           $config_settings = $data['params'];
        }

        $settings = $config_settings['settings'];
        $client = $config_settings['client'];
        $mattermost_key = $config_settings['mattermost_key'];
        $client_url = $config_settings['client_url'];

        $msg = '';
        $client_tag = [
            ':information_source:',
            '#'.str_replace("og_","",$this->_app_prefix).' #'.$client,
            '#archive_delete'. $this->utils->formatYearMonthForMysql(new DateTime).' #archive_delete_'.$archive_delete_day,
        ];
        if($manual_date !=_COMMAND_LINE_NULL){
            array_push($client_tag, '#manual_date_'.$manual_date);
        }
        if(!empty($settings)){
            array_push($client_tag, $client_url.'/system_management/common_queue/'.$token);
            $msg .= __FUNCTION__." start time: ".$this->utils->getNowForMysql()." | Hostname: ". $this->utils->getHostname();
            $msg .= "\n";
            $msg .= "``` json \n".json_encode($config_settings, JSON_PRETTY_PRINT)." \n```";
            $this->sendNotificationToMattermost(__FUNCTION__, $mattermost_key, $msg, 'info', $client_tag);

        }else{
            $this->utils->error_log('cron_daily_archive_and_delete_data is empty');
            return;
        }

        $active_db_key = $this->utils->getActiveTargetDB();
        $is_multi_db = $this->utils->isEnabledMDB();
        $filepath=null;
        $csvHeader=['table','archive_cnt','delete_cnt','from', 'to'];
        if($is_multi_db === true){
            $this->utils->_appendSaveDetailedResultToRemoteLog($token, $active_db_key.'_cron_daily_archive_and_delete_data', null, $filepath, true, $csvHeader);
        }else{
            $this->utils->_appendSaveDetailedResultToRemoteLog($token, 'cron_daily_archive_and_delete_data',  null, $filepath, true, $csvHeader);
        }

        $controller=$this;
        $player_model = $this->player_model;
        $settings_cnt = count($settings);
        $i=0;

        foreach ($settings as $table => $setting) {
            $i++;
            //default
            $delete_by = 'by_months'; //by_days
            $step = '+24 hours';
            $dry_run = $setting['dry_run'];
            $delete_data_after_backup=$setting['delete_data_after_backup'];
            $archive_sleep = $setting['archive_sleep'];
            $delete_sleep = $setting['delete_sleep'];
            $drop_last_month_archive_table_not_in_range = $setting['drop_last_month_archive_table_not_in_range'];
            $use_index_str = $setting['use_index_str'];
            $only_start_at_max_day = $setting['only_start_at_max_day'];

            if($is_multi_db === true){
                if(array_key_exists('target_db_keys', $setting) ){
                    $target_db_keys = $setting['target_db_keys'];
                    if(!in_array($active_db_key, $target_db_keys) ){
                        //return;
                        $msg = '```'.$table.' Setting only set in ' .implode(",", $target_db_keys).' NOT IN CURRENT DB INSTANCE '.$this->_app_prefix.' (SKIPPED)```';
                        $this->sendNotificationToMattermost(__FUNCTION__, $mattermost_key, $msg, 'warning', ':warning:');
                        continue;// skip
                    }
                }
            }
            if(array_key_exists('delete_by', $setting)){
                $delete_by = $setting['delete_by'];
            }
            if(array_key_exists('delete_step', $setting)){
                $step = $setting['delete_step'];
            }

            $date_field =   $setting['date_field'];
            $number_range = $setting['number_range'];

            //$save_to_table = (empty($setting['save_to_table'])) ? _COMMAND_LINE_NULL : $setting['save_to_table'];
            $save_to_table =null;

            if(!empty($setting['save_to_table'])){
                $save_to_table = $setting['save_to_table'];
            }else{
                $save_to_table = $table.'_archive';
            }

            list($start_day,$max_day,$last_day,$delete_day) = $this->calculate_date_range($delete_by,$manual_date,$number_range);

            if($drop_last_month_archive_table_not_in_range === true){

                $day_obj1 = new Datetime($start_day);
                $day_obj2 =   new Datetime($start_day);

                $startday_month = $day_obj1->format('Ym');
                $delete_month = $day_obj2->modify('-1 months')->format('Ym');

                if($delete_month < $startday_month){
                  if($this->utils->table_really_exists($save_to_table.'_'.$delete_month)){
                    $this->load->dbforge();
                    $this->dbforge->drop_table($save_to_table.'_'.$delete_month);
                 }
             }

            }
            if($only_start_at_max_day === true){
                $start_day = $max_day.' '.Utils::FIRST_TIME;
                $max_day = $max_day.' '.Utils::LAST_TIME;
            }else{
                $start_day = $start_day.' '.Utils::FIRST_TIME;
                $max_day = $max_day.' '.Utils::LAST_TIME;
            }

            if($step !=  _COMMAND_LINE_NULL){
              $step='+'.$step;
            }

            $archive_cnt = 0;
            $delete_cnt = 0;

            $success=$this->utils->loopDateTimeStartEnd($start_day, $max_day, $step, function($from, $to, $step)
                use($controller,$player_model,$is_multi_db,$active_db_key,$archive_sleep,$delete_sleep, $delete_data_after_backup,$table,
                    $save_to_table, $use_index_str, $mattermost_key, $date_field, $filepath, $dry_run , &$archive_cnt, &$delete_cnt, $token){

                    $from_str=$this->utils->formatDateTimeForMysql($from);
                    $to_str=$this->utils->formatDateTimeForMysql($to);

                    // put it in mysql trans to prevent data loss
                    $player_model->startTrans();

                    $archive_rlt = $controller->runArchiveData($from_str,$to_str,$table,$save_to_table,$date_field,$token,$use_index_str,$dry_run);
                    if($archive_sleep>0){
                        sleep($archive_sleep);
                    }

                   // usleep(100000);
                    $new_delete_cnt =0;
                    if(isset($archive_rlt)){
                        if(isset($archive_rlt['count'])  && $archive_rlt['count'] > 0 ){
                            $archive_cnt = $archive_cnt + $archive_rlt['count'];
                            //$this->utils->info_log('archive_rlt',$archive_rlt);
                        }

                        if($delete_data_after_backup === true){
                            $new_delete_cnt = $controller->runDeleteData($table, $date_field, $from_str, $to_str,$dry_run,$mattermost_key, $token);
                            $delete_cnt = $delete_cnt + $new_delete_cnt;
                            if($delete_sleep>0){
                              sleep($delete_sleep);
                            }
                        }
                    }

                     //just in case error in trans occured
                    if($player_model->isErrorInTrans()){
                       $this->utils->error_log('cron_daily_archive_and_delete_data trans error ','from',$from_str,'to',$to_str);
                        $msg = "``` json \n".json_encode(['table' => $table,'details' => ['from'=>$from_str,'to'=>$to,'token',$token]])." \n```";
                         $this->sendNotificationToMattermost(__FUNCTION__, $mattermost_key, $msg, 'warning', ':warning:' );
                    }

                    $player_model->endTrans();

                    $log_msg = [$table,(empty($archive_rlt['count'])) ? 0 : $archive_rlt['count'] ,$new_delete_cnt,$from_str,$to_str];
                    $csvHeader=[];
                    if($is_multi_db === true){
                        $this->utils->_appendSaveDetailedResultToRemoteLog($token, $active_db_key.'_cron_daily_archive_and_delete_data', $log_msg, $filepath, true, $csvHeader);
                    }else{
                        $this->utils->_appendSaveDetailedResultToRemoteLog($token, 'cron_daily_archive_and_delete_data',  $log_msg, $filepath, true, $csvHeader);
                    }
                   $this->utils->debug_log('archive and delete status','archive_cnt', $archive_cnt,'table',$table,'delete_cnt', $delete_cnt,'from',$from_str,'to',$to_str,'delete_data_after_backup',$delete_data_after_backup);
                    return true;
            });

            if($token != _COMMAND_LINE_NULL){
                $client_tag = [
                ':point_right:',
                '#'.str_replace("og_","",$this->_app_prefix),
                '#archive_delete'. $this->utils->formatYearMonthForMysql(new DateTime).' #archive_delete_'. $archive_delete_day.' #'.$table,
                '#job_'.$token
                ];

                $result = ['table'=>$table, 'date_field'=> $date_field, 'start_day' => $start_day, 'max_day' =>  $max_day, 'total_deleted_cnt'=> $delete_cnt, 'total_archive_cnt' =>
                $archive_cnt, 'remote_log_path' => site_url().'remote_logs/'.basename($filepath)];
                $is_final_setting = ($i == $settings_cnt);//for task progress view
                $this->queue_result->appendResult($token, ['request_id'=>_REQUEST_ID, 'result'=> $result ] , $is_final_setting, false);

                $msg = "``` json \n".json_encode(['table' => $table, __FUNCTION__.'_status'=>'done','process_end_time'=>$this->utils->getNowForMysql(), 'result' => $result])." \n```";
                $this->sendNotificationToMattermost(__FUNCTION__, $mattermost_key, $msg, 'info', $client_tag);

            }

        }//foreach

    }

    public function reset_game_logs_id_on_redis($id){
        //stop all sync before run this
        $this->load->model(['game_logs']);
        $rlt=$this->game_logs->resetGameLogsIdOnRedis($id, $error);
        $this->utils->info_log('resetGameLogsIdOnRedis', $id, $rlt, $error);
    }

    public function reset_game_logs_unsettle_id_on_redis($id){
        //stop all sync before run this
        $this->load->model(['game_logs']);
        $rlt=$this->game_logs->resetGameLogsUnsettledIdOnRedis($id, $error);
        $this->utils->info_log('resetGameLogsUnsettledIdOnRedis', $id, $rlt, $error);
    }

}