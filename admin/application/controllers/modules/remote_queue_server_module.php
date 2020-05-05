<?php

/**
 * Class remote_queue_server_module
 *
 * General behaviors include :
 * exec remote queue job
 *
 * @category Command Line
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait remote_queue_server_module {

    public function remote_sync_game_logs($data, $params, $db=null){

        if($this->utils->getConfig('disabled_sync_game_logs_on_sbe')){
            $this->utils->error_log('!!!donot allow sync game logs!!!');
            return false;
        }

        $success=true;

        // $params = json_decode($data['full_params'], true);
        $token = $data['token'];

        //convert bool to string

        $toDateTimeStr = $params['toDateTimeStr'];
        $fromDateTimeStr = $params['fromDateTimeStr'];
        $game_api_id = $params['game_api_id'];
        $timelimit = $params['timelimit'];
        $playerName = $params['playerName'];
        $dry_run = $params['dry_run'];
        $merge_only = $params['merge_only'];
        $only_original = $params['only_original'];

        if(empty($game_api_id)){
            $game_api_id=_COMMAND_LINE_NULL;
        }

        // public function sync_game_logs($dry_run='true', $fromDateTimeStr, $toDateTimeStr, $game_api_id='', $timelimit = 60,
        //  $playerName = '', $merge_only='false', $only_original='false', $token=null) {

        $is_blocked=false;
        if(empty($db)){
             $cmd=$this->utils->generateCommandLine('sync_game_logs', [$dry_run, $fromDateTimeStr, $toDateTimeStr,
            $game_api_id, $timelimit, $playerName, $merge_only, $only_original, $token], $is_blocked);
        }else{
            $dbName=$db->getOgTargetDB();
            $file_list=[];
            $cmd=$this->utils->generateCommandLine('sync_game_logs', [$dry_run, $fromDateTimeStr, $toDateTimeStr,
            $game_api_id, $timelimit, $playerName, $merge_only, $only_original, $token], $is_blocked, $file_list, $dbName);
        }
        $success=$this->runCmd($cmd)==0;

        // $this->sync_game_logs($dry_run ? 'true' : 'false', $fromDateTimeStr, $toDateTimeStr,
        //  $game_api_id, $timelimit, $playerName, $merge_only ? 'true' : 'false',
        //  $only_original ? 'true' : 'false', $token);

        return $success;
    }

    public function remote_email($data, $params, $db=null){

        $this->load->library(array('email_setting'));

        $token = $data['token'];
        // $params = $data['params'];
        $to = $params['to'];
        $from = $params['from'];
        $fromName = $params['fromName'];
        $subject = $params['subject'];
        $body = $params['body'];
        $emailData = array(
            'from' => $from,
            'from_name' => $fromName,
            'subject' => $subject,
            'body' => $body,
        );

        $success = $this->email_setting->sendEmail($to, $emailData);

        if ($success === true) {
            if (!$this->queue_result->updateResult($token, $rlt)) {
                $this->utils->error_log("[ERROR] [do_email_job] to " . $to . ", token:" . $token . " failed: ", $params);
            }
        } else {
            $this->queue_result->failedResult($token, $rlt);
            $this->utils->error_log("[ERROR] [do_email_job] to " . $to . ", token:" . $token . " failed: " , $params);
        }

        return $success;
    }

    public function remote_sms($data, $params, $db=null){
        $this->load->library('sms/sms_sender');

        // $params = json_decode($data['full_params'], true);
        $token = $data['token'];
        $mobileList  = $params['mobileNum'];
        $smsContent = $params['content'];
        $isSuccess = true;
        $smsError  = [];
        $smsSuccess = [];

        foreach ($mobileList as $mobileNum)  {

            $rlt = $this->sms_sender->send($mobileNum, $smsContent);

            if ($rlt != $isSuccess) {
                $isSuccess = false;
            }

            if ($rlt) {
                $smsSuccess[] = [
                    'mobile' => $mobileNum
                ];
            } else {
                $smsError[] = [
                    'mobile' => $mobileNum,
                    'errorMsg' => $this->sms_sender->getLastError()
                ];
            }
        }

        if ($isSuccess) {
            $this->queue_result->appendResult($token, ['send sms result' => $isSuccess], true);
            $this->utils->debug_log('Sms api response success', $mobileList, $smsContent);
        } else {
            $this->queue_result->failedResult($token, ['send sms result' => $smsError, 'success' => $smsSuccess]);
            $this->utils->debug_log('Sms api response error', $mobileList, $smsContent, $smsError);
        }
    }

    public function remote_debug_queue($data, $params, $db=null){

        $token = $data['token'];
        // $params = $data['full_params'];

        $success = !empty($params['trigger_time']);
        $rlt=['trigger_time'=>$params['trigger_time'], 'run_time'=>$this->utils->getNowForMysql()];

        if ($success) {
            if (!$this->queue_result->appendResult($token, $rlt, true)) {
                $this->utils->error_log("[ERROR] [remote_debug_queue], append result failed token:" . $token . " failed: ", $params);
            }
        } else {
            $this->queue_result->appendResult($token, $rlt, false, true);
            $this->utils->error_log("[ERROR] [remote_debug_queue], token:" . $token . " failed: " , $params);
        }

        return $success;
    }

    public function remote_import_players($data, $params, $db=null){

        $token = $data['token'];
        // $params = $data['params'];

        // $success=true;

        // $rlt=[];

        // $files=$params['files'];
        // $importer_formatter=$params['importer_formatter'];
        // $summary=[];
        // $message=null;


        $is_blocked=false;

        if(empty($db)){
             $cmd=$this->utils->generateCommandLine('run_importer_by_queue_token', [$token], $is_blocked);
        }else{
            $dbName=$db->getOgTargetDB();
            $file_list=[];
            $cmd=$this->utils->generateCommandLine('run_importer_by_queue_token', [$token], $is_blocked, $file_list, $dbName);
        }
        $success=$this->runCmd($cmd)==0;

        $rlt=['running'=>$success, 'cmd'=>$cmd];
        $this->queue_result->appendResult($token, $rlt);

        //try load importer
        // $success=$this->player_model->importFromCSV($importer_formatter, $files, $summary, $message);
        // $rlt['summary']=$summary;
        // $rlt['message']=$message;
        // $rlt['importer_formatter']=$importer_formatter;

        // if ($success) {
        //     if (!$this->queue_result->appendResult($token, $rlt, true)) {
        //         $this->utils->error_log("[ERROR] [remote_import_players], append result failed token:" . $token . " failed: ", $params);
        //     }
        // } else {
        //     $this->queue_result->appendResult($token, $rlt, false, true);
        //     $this->utils->error_log("[ERROR] [remote_import_players], token:" . $token . " failed: " , $params);
        // }

        return $success;

    }

    public function remote_rebuild_games_total($data, $params, $db=null){

        $success=true;
        $token = $data['token'];
        $rebuild_hour = $params['rebuild_hour'];
        $rebuild_minute = $params['rebuild_minute'];
        $endDateTimeStr = $params['endDateTimeStr'];
        $fromDateTimeStr = $params['fromDateTimeStr'];
        $dry_run = $params['dry_run'];

        $dateList=[];
        $success= true;
        $error='';
        $is_blocked=false;

        if(empty($db)){
             $cmd=$this->utils->generateCommandLine('rebuild_totals', [$fromDateTimeStr, $endDateTimeStr,$rebuild_hour,$rebuild_minute,$token], $is_blocked);
        }else{
            $dbName=$db->getOgTargetDB();
            $file_list=[];
            $cmd=$this->utils->generateCommandLine('rebuild_totals', [$fromDateTimeStr, $endDateTimeStr,$rebuild_hour,$rebuild_minute,$token], $is_blocked, $file_list, $dbName);
        }
        $success=$this->runCmd($cmd)==0;

        return $success;
    }

    public function remote_pay_cashback_daily($data, $params, $db=null){

        $success=true;
        $token = $data['token'];
        $date = $params['date'];
        $playerId=0;
        $debug_mode='false';

        $result = ['start'=>'try run pay cashback by '.$date];
        $done=false;
        $this->queue_result->appendResult($token, ['request_id'=>_REQUEST_ID, 'result'=> $result ], $done);

        $success= true;
        $is_blocked=false;

        if(empty($db)){
             $cmd=$this->utils->generateCommandLine('onlyPayCashback', [$date, $playerId, $debug_mode, $token], $is_blocked);
        }else{
            $dbName=$db->getOgTargetDB();
            $file_list=[];
            $$cmd=$this->utils->generateCommandLine('onlyPayCashback', [$date, $playerId, $debug_mode, $token], $is_blocked, $file_list, $dbName);
        }
        $success=$this->runCmd($cmd)==0;

        return $success;

    }

    public function remote_sync_t1_gamegateway($data, $params, $db=null){

        $success=true;
        $token = $data['token'];
        $date_to = $params['date_to'];
        $date_from = $params['date_from'];
        $playerName=$params['playerName'];
        $timelimit=30;

        $dateList=[];
        $success= true;
        $error='';
        $is_blocked=false;
        // manually_sync_t1_gamegateway($dateTimeFromStr, $dateTimeToStr, $playerName = null, $timelimit = 30, $queue_token=null)
        if(empty($db)){
             $cmd=$this->utils->generateCommandLine('manually_sync_t1_gamegateway', [$date_from, $date_to, $playerName, $timelimit, $token], $is_blocked);
        }else{
            $dbName=$db->getOgTargetDB();
            $file_list=[];
            $cmd=$this->utils->generateCommandLine('manually_sync_t1_gamegateway', [$date_from, $date_to, $playerName, $timelimit, $token], $is_blocked, $file_list, $dbName);
        }

        $success=$this->runCmd($cmd)==0;

        return $success;

    }

    public function remote_export_csv($data, $params, $db=null){

        $success=true;
        $token = $data['token'];
        $dateList=[];
        $success= true;
        $is_blocked = false;

        if(empty($db)){
            $cmd=$this->utils->generateCommandLine('do_remote_export_csv_job', [$token], $is_blocked);
        }else{
            $dbName=$db->getOgTargetDB();
            $file_list=[];
            $cmd=$this->utils->generateCommandLine('do_remote_export_csv_job', [$token], $is_blocked, $file_list, $dbName);
        }

        $success=$this->runCmd($cmd)==0;

        return $success;
    }

    public function remote_stop_queue($data, $params, $db=null){
        $this->load->model(['queue_result']);
        $success=true;
        $params = $data['params'];
        $executed_stop_token = $data['token'];
        $token = $params['token_to_stop'];
        //temp call to get the process on dbs
        $result = $this->initJobData($token);
        $state =  json_decode($result['state']);
        $pid = $state->processId;
        // $cmd = "sudo kill -9 $pid";
        // $success=$this->runCmd($cmd)==0;
        $success = posix_kill($pid, SIGKILL);
        if($success){
           $this->utils->debug_log('Killed PID',$pid ,'success', $success);
           $this->queue_result->updateResultStopped($token);
       }else{
         $this->utils->debug_log('Attempt to kill',$pid, 'status', 'success', $success);
         $this->queue_result->updateResultStopped($token);
     }
     $this->queue_result->updateResult($executed_stop_token, ['success' => true]);
     return $success;

    }

    public function remote_manually_batch_add_cashback_bonus($data, $params, $db=null){

        $token = $data['token'];
        $is_blocked=false;
        if(empty($db)){
            $cmd=$this->utils->generateCommandLine('remote_manually_batch_add_cashback_bonus', [$token], $is_blocked);
        }else{
            $dbName=$db->getOgTargetDB();
            $file_list=[];
            $cmd=$this->utils->generateCommandLine('remote_manually_batch_add_cashback_bonus', [$token], $is_blocked, $file_list, $dbName);
        }
        $success=$this->runCmd($cmd)==0;

        $rlt=['running'=>$success, 'cmd'=>$cmd];
        $this->queue_result->appendResult($token, $rlt);

        return $success;

    }

    public function remote_batch_sync_balance_by($data, $params, $db=null){

        $success    = true;
        $token      = $data['token'];
        $mode       = $params['mode'];
        $dry_run    = $params['dry_run'];
        $max_number = $params['max_number'];
        $apiId      = $params['apiId'];

        $success = true;
        $is_blocked = false;

        if(empty($db)){
            $cmd = $this->utils->generateCommandLine('batch_sync_balance_by', [$mode, $dry_run, $max_number, $apiId, $token], $is_blocked);
        }else{
            $dbName=$db->getOgTargetDB();
            $file_list=[];
            $cmd = $this->utils->generateCommandLine('batch_sync_balance_by', [$mode, $dry_run, $max_number, $apiId, $token], $is_blocked, $file_list, $dbName);
        }
        $success = $this->runCmd($cmd) == 0;

        return $success;
    }

    public function remote_send_player_private_ip_mm_alert($data, $params, $db=null){

        $success    = true;
        $token      = $data['token'];
        $success    = true;
        $is_blocked = false;

        if(empty($db)){
            $cmd = $this->utils->generateCommandLine('send_player_private_ip_mm_alert', [$token], $is_blocked);
        }else{
            $dbName=$db->getOgTargetDB();
            $file_list=[];
            $cmd = $this->utils->generateCommandLine('send_player_private_ip_mm_alert', [$token], $is_blocked, $file_list, $dbName);
        }
        $success = $this->runCmd($cmd) == 0;

        return $success;
    }

    public function remote_regenerate_all_report($data, $params, $db=null){

        $success    = true;
        $token      = $data['token'];
        $start_date_obj = new DateTime($params['fromDateTimeStr']);
        $fromDateTimeStr = $start_date_obj->format('Y-m-d');
        $end_date_obj = new DateTime($params['endDateTimeStr']);
        $endDateTimeStr = $end_date_obj->format('Y-m-d');
        $success    = true;
        $is_blocked = false;

        if(empty($db)){
            $cmd = $this->utils->generateCommandLine('generate_all_report_from_to', [$endDateTimeStr,$fromDateTimeStr, $token],$is_blocked);
        }else{
            $dbName=$db->getOgTargetDB();
            $file_list=[];
            $cmd = $this->utils->generateCommandLine('generate_all_report_from_to', [$endDateTimeStr,$fromDateTimeStr, $token],$is_blocked, $file_list, $dbName);
        }

        $success = $this->runCmd($cmd) == 0;

        return $success;
    }

    public function remote_broadcast_message($data, $params, $db=null){

        $success=true;
        $token = $data['token'];
        $dateList=[];
        $success= true;
        $is_blocked = false;

        if(empty($db)){
            $cmd=$this->utils->generateCommandLine('do_broadcast_message_job', [$token], $is_blocked);
        }else{
            $dbName=$db->getOgTargetDB();
            $file_list=[];
            $cmd=$this->utils->generateCommandLine('do_broadcast_message_job', [$token,$dbName], $is_blocked, $file_list, $dbName);
        }

        $success=$this->runCmd($cmd)==0;

        return $success;
    }

    public function remote_batch_add_bonus($data, $params, $db=null){

        $success=true;
        $is_blocked=false;
        $token = $data['token'];

        if(empty($db)){
            $cmd=$this->utils->generateCommandLine('import_csv_bonus_by_queue', [$token], $is_blocked);
        }else{
            $dbName=$db->getOgTargetDB();
            $file_list=[];
            $cmd=$this->utils->generateCommandLine('import_csv_bonus_by_queue', [$token,$dbName], $is_blocked, $file_list, $dbName);
        }

        $success=$this->runCmd($cmd)==0;

        return $success;

    }

}
