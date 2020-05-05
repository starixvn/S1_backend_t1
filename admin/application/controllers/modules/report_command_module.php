<?php

/**
 * Class report_command_module
 *
 * General behaviors include :
 *
 * * Generate report
 *
 * @category Command Line
 * @version 1.8.10
 * @copyright 2013-2018 TripleOneTech
 */
trait report_command_module {

    public function generate_all_report_daily(){
        //yesterday and today
        $from=$this->utils->getYesterdayForMysql();
        $to=$this->utils->getTodayForMysql();
        $this->generate_all_report_from_to($from, $to);
    }


	/**
	 * generate_all_report_from_to
	 * @param  string $from  Y-m-d
	 * @param  string $to   Y-m-d
	 */
    public function generate_all_report_from_to($from, $to, $token=_COMMAND_LINE_NULL){
        if(empty($from) || empty($to)){
            $this->utils->error_log('please use format Y-m-d');
            return;
        }

        if($from > $to){
            $this->utils->error_log('from should <= to', $from, $to);
            return;
        }

        $lock_rebuild_reports_range= $this->utils->getConfig('lock_rebuild_reports_range');
        //convert to day to be sure
        $from_day = $this->utils->formatDateForMysql(new DateTime($from));
        $to_day = $this->utils->formatDateForMysql(new DateTime($to));
        if(!empty($lock_rebuild_reports_range)){
            if($this->utils->isRebuildReportDateLocked($lock_rebuild_reports_range,$from_day,$to_day,$rlt)){
                $this->utils->error_log(sprintf(lang("Regenarate All Report has lock - should not be equal or older than  %s  "),$rlt['cutoff_day']));
                return;
            }
        }
 
        $this->load->model(['multiple_db_model']);

        $this->utils->foreachDaily($from, $to, 1, function($from, $to) use($token){
            $success=true;

            $from=$this->utils->formatDateTimeForMysql($from);
            $to=$this->utils->formatDateTimeForMysql($to);

            $this->utils->debug_log('generate all report', $from, $to);

            $process_start_time = $this->utils->getNowForMysql();

            $this->rebuild_total_only_minute_hours_day($from, $to);
            $this->generate_game_report_hourly($from, $to);
            $this->generate_player_report_hourly($from, $to);
            $this->generate_payment_report_daily($from, $to);
            $this->generate_cashback_report_daily($from, $to);
            $this->generate_promotion_report_daily($from, $to);
            $this->generate_summary2_report_daily($from, $to);

            if($token !=_COMMAND_LINE_NULL){
                $done = false;
                $result = ['from'=>$from, 'to'=>$to, 'process_start_time'=>$process_start_time, 'process_end_time'=> $this->utils->getNowForMysql()];
                $this->queue_result->appendResult($token, ['request_id'=>_REQUEST_ID, 'result'=> $result ], $done, false);
            }
            return $success;
        });

        if($token != _COMMAND_LINE_NULL){
            $done = true;
            $result = ['done_all_at'=> $this->utils->getNowForMysql()];
            $this->queue_result->appendResult($token, ['request_id'=>_REQUEST_ID, 'result'=> $result ], $done, false);
        }
    }

    public function generate_game_report_hourly($from, $to){

        $lock_rebuild_reports_range= $this->utils->getConfig('lock_rebuild_reports_range');
        $from_day = $this->utils->formatDateForMysql(new DateTime($from));
        $to_day = $this->utils->formatDateForMysql(new DateTime($to));
        if(!empty($lock_rebuild_reports_range)){
            if($this->utils->isRebuildReportDateLocked($lock_rebuild_reports_range,$from_day,$to_day,$rlt)){
                $this->utils->error_log(sprintf(lang("Regenarate All Report has lock - should not be equal or older than  %s  "),$rlt['cutoff_day']));
                return;
            }
        }

        $from = date('Y-m-d H:', strtotime($from)).'00:00';
        if(empty($to)){
            //end of current hour
            $to = date('Y-m-d H:').'59:59';
        }else{
            $to = date('Y-m-d ', strtotime($to)).'23:59:59';
        }

        if($this->utils->isSuperModeOnMDB()){
            $this->utils->debug_log('is super site');
            return $this->merge_reports_to_super('game_report_hourly', $from, $to);
        }

        //sync from total hour
        if(strtotime($from) <= strtotime($to)){
            $this->load->model(['report_model']);
            $success=$this->report_model->generate_game_report_houry($from, $to);
            $this->utils->debug_log('generate_game_report_houry', $from, $to, $success);
        }
    }

    public function generate_payment_report_daily($from, $to){

        $lock_rebuild_reports_range= $this->utils->getConfig('lock_rebuild_reports_range');
        $from_day = $this->utils->formatDateForMysql(new DateTime($from));
        $to_day = $this->utils->formatDateForMysql(new DateTime($to));
        if(!empty($lock_rebuild_reports_range)){
            if($this->utils->isRebuildReportDateLocked($lock_rebuild_reports_range,$from_day,$to_day,$rlt)){
                $this->utils->error_log(sprintf(lang("Regenarate All Report has lock - should not be equal or older than  %s  "),$rlt['cutoff_day']));
                return;
            }
        }

        $from = date('Y-m-d H:', strtotime($from)).'00:00';
        if(empty($to)){
            //end of current hour
            $to = date('Y-m-d H:').'59:59';
        }else{
            $to = date('Y-m-d ', strtotime($to)).'23:59:59';
        }

        //if is super
        if($this->utils->isSuperModeOnMDB()){
            $this->utils->debug_log('is super site');
            return $this->merge_reports_to_super('payment_report_daily', $from, $to);
        }
        //update date from transactions
        if(strtotime($from) <= strtotime($to)){
            $this->load->model(['report_model']);
            $success=$this->report_model->generate_payment_report_daily($from, $to);
            $this->utils->debug_log('generate_payment_report_daily', $from, $to, $success);
        }

    }

    public function generate_cashback_report_daily($from, $to){

        $lock_rebuild_reports_range= $this->utils->getConfig('lock_rebuild_reports_range');
        $from_day = $this->utils->formatDateForMysql(new DateTime($from));
        $to_day = $this->utils->formatDateForMysql(new DateTime($to));
        if(!empty($lock_rebuild_reports_range)){
            if($this->utils->isRebuildReportDateLocked($lock_rebuild_reports_range,$from_day,$to_day,$rlt)){
                $this->utils->error_log(sprintf(lang("Regenarate All Report has lock - should not be equal or older than  %s  "),$rlt['cutoff_day']));
                return;
            }
        }

        $from = date('Y-m-d H:', strtotime($from)).'00:00';
        if(empty($to)){
            //end of current hour
            $to = date('Y-m-d H:').'59:59';
        }else{
            $to = date('Y-m-d ', strtotime($to)).'23:59:59';
        }

        //if is super
        if($this->utils->isSuperModeOnMDB()){
            $this->utils->debug_log('is super site');
            return $this->merge_reports_to_super('cashback_report_daily', $from, $to);
        }
        //update date from transactions
        if(strtotime($from) <= strtotime($to)){
            $this->load->model(['report_model']);
            $success=$this->report_model->generate_cashback_report_daily($from, $to);
            $this->utils->debug_log('generate_cashback_report_daily', $from, $to, $success);
        }

    }

    public function generate_promotion_report_daily($from, $to){

        $lock_rebuild_reports_range= $this->utils->getConfig('lock_rebuild_reports_range');
        $from_day = $this->utils->formatDateForMysql(new DateTime($from));
        $to_day = $this->utils->formatDateForMysql(new DateTime($to));
        if(!empty($lock_rebuild_reports_range)){
            if($this->utils->isRebuildReportDateLocked($lock_rebuild_reports_range,$from_day,$to_day,$rlt)){
                $this->utils->error_log(sprintf(lang("Regenarate All Report has lock - should not be equal or older than  %s  "),$rlt['cutoff_day']));
                return;
            }
        }

        $from = date('Y-m-d H:', strtotime($from)).'00:00';
        if(empty($to)){
            //end of current hour
            $to = date('Y-m-d H:').'59:59';
        }else{
            $to = date('Y-m-d ', strtotime($to)).'23:59:59';
        }

        //if is super
        if($this->utils->isSuperModeOnMDB()){
            $this->utils->debug_log('is super site');
            return $this->merge_reports_to_super('promotion_report_details', $from, $to);
        }
        //update date from transactions
        if(strtotime($from) <= strtotime($to)){
            $this->load->model(['report_model']);
            $success=$this->report_model->generate_promotion_report_details($from, $to);
            $this->utils->debug_log('generate_promotion_report_details', $from, $to, $success);
        }

    }

	public function generate_player_report_hourly($start='last_6_hours', $end=null, $username = '_null'){

        $lock_rebuild_reports_range= $this->utils->getConfig('lock_rebuild_reports_range');
        
        if(!empty($lock_rebuild_reports_range)){
            if($start =='last_6_hours'){
                $start = date('Y-m-d H:', strtotime('-6 hours')).'00:00';
            }
            $from_day = $this->utils->formatDateForMysql(new DateTime($start));
            $to_day = $this->utils->formatDateForMysql(new DateTime($end));
            if($this->utils->isRebuildReportDateLocked($lock_rebuild_reports_range,$from_day,$to_day,$rlt)){
                $this->utils->error_log(sprintf(lang("Regenarate All Report has lock - should not be equal or older than  %s  "),$rlt['cutoff_day']));
                return;
            }
        }

        $from = $to = date('Y-m-d');
		if($start =='last_6_hours'){
			$from = date('Y-m-d H:', strtotime('-6 hours')).'00:00';
            $to = date('Y-m-d H:').'00:00';
		}
        else{
            $from = date('Y-m-d H:', strtotime($start)).'00:00';
            if(empty($end)){
                $to = date('Y-m-d H:').'00:00';
            }else{
                $to = date('Y-m-d ', strtotime($end)).'23:00:00';
            }
        }

        $this->utils->debug_log('generate_player_report_hourly from to', $from, $to);
		//if is super
        if($this->utils->isSuperModeOnMDB()){
        	$this->utils->debug_log('is super site');
			return $this->merge_reports_to_super('player_report_hourly', $from, $to);
		}

        if(strtotime($from) < strtotime('2015-01-01') /*---minimum date---*/){
            $this->utils->debug_log('Invalid, minimum date 2015-01-01');
        }
        elseif(strtotime($from) <= strtotime($to)){
            if($username == '_null'){
                $player_id = null;
            }else{
                $player_id = $this->player_model->getPlayerIdByUsername($username);
            }

            $this->load->model(['report_model']);
            $success=$this->report_model->generate_player_report_hourly($from, $to, $player_id);

            if(!$success){
            	$this->utils->error_log('generate_player_report_hourly failed', $from, $to);
			}else{
	            $this->utils->debug_log('generate_player_report_hourly', $from, $to, $success);
			}
        }
        else{
            $this->utils->debug_log('Invalid dates!');
        }

    }

	public function generate_summary2_report_daily($start='last_2_days', $end=null){

        $lock_rebuild_reports_range= $this->utils->getConfig('lock_rebuild_reports_range');

        if(!empty($lock_rebuild_reports_range)){
            if($start =='last_2_days'){
                $start = date('Y-m-d', strtotime('yesterday'));
            }
            $from_day = $this->utils->formatDateForMysql(new DateTime($start));
            $to_day = $this->utils->formatDateForMysql(new DateTime($end));
            if($this->utils->isRebuildReportDateLocked($lock_rebuild_reports_range,$from_day,$to_day,$rlt)){
                $this->utils->error_log(sprintf(lang("Regenarate All Report has lock - should not be equal or older than  %s  "),$rlt['cutoff_day']));
                return;
            }

        }

        $from = $to = date('Y-m-d');
        if($start =='last_2_days'){
            $from = date('Y-m-d', strtotime('yesterday'));
            $to = date('Y-m-d');
        }
        else{
            $from = date('Y-m-d', strtotime($start));
            if(empty($end)){
                $to = date('Y-m-d');
            }else{
                $to = date('Y-m-d', strtotime($end));
            }
        }

		if(!empty($start) && !empty($end)){
			$from = date('Y-m-d', strtotime($start));
			$to = date('Y-m-d', strtotime($end));
		}

		$this->load->model(['report_model']);

        $this->utils->debug_log('date from to', $from, $to);


		//if is super
		if($this->utils->isSuperModeOnMDB()){
			$this->utils->debug_log('is super site');
			return $this->merge_reports_to_super('summary2_report', $from, $to);
		}

        if(strtotime($from) < strtotime('2015-01-01') /*---minimum date---*/){
            $this->utils->debug_log('Invalid, minimum date 2015-01-01');
        }
        elseif(strtotime($from) <= strtotime($to)){

            $success=$this->report_model->generate_summary2_report_daily($from, $to);
            $this->utils->debug_log('generate_summary2_report_daily', $from, $to, $success);
        }
        else{
            $this->utils->debug_log('Invalid dates!');
        }
	}

	public function generate_oneworks_report_daily($date = null){
		if(!$this->utils->isEnabledFeature('enabled_oneworks_game_report')){
			$this->utils->debug_log('feature enabled_oneworks_game_report disabled');
			return;
		}
		if(!$date){
			$date = date('Y-m-d', strtotime('today'));
		}
		$this->load->model(['report_model']);
		$success=$this->report_model->generate_oneworks_report_daily($date);
		$this->utils->debug_log('generate_oneworks_report_daily', $date, $success);
	}

    public function generate_oneworks_report_by_date_range($date_from, $date_to){
        $date = $date_from;
        $end_date = $date_to;
        while (strtotime($date) <= strtotime($end_date)) {
            $this->generate_oneworks_report_daily($date);
            $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
        }
        $this->utils->debug_log('generate_oneworks_report_by_date_range', $date_from ,$date_to);
    }

    public function generate_afb88_report_daily($date = null){
        if(!$this->utils->isEnabledFeature('enabled_afb88_sports_game_report')){
            $this->utils->debug_log('feature enabled_afb88_sports_game_report disabled');
            return;
        }
        if(!$date){
            $date = date('Y-m-d', strtotime('today'));
        }
        $this->load->model(['report_model']);
        $success=$this->report_model->generate_afb88_report_daily($date);
        $this->utils->debug_log('generate_afb88_report_daily', $date, $success);
    }

    public function generate_afb88_report_by_date_range($date_from, $date_to){
        $date = $date_from;
        $end_date = $date_to;
        while (strtotime($date) <= strtotime($end_date)) {
            $this->generate_afb88_report_daily($date);
            $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
        }
        $this->utils->debug_log('generate_afb88_report_by_date_range', $date_from ,$date_to);
    }

    public function generate_kingrich_summary_report_hourly($date_from=null, $date_to=null){
        $this->load->model(['report_model']);
        $success = $this->report_model->generate_kingrich_summary_report_hourly($date_from, $date_to);
        //$success = true;
        $this->utils->debug_log('generate_kingrich_summary_report_hourly logs success',$success);
    }

    //===super report==========================================
    public function merge_reports_to_super($report_type, $from, $to){
        if(!$this->utils->isEnabledMDB()){
            return false;
        }
        $result=null;
		$this->utils->debug_log('merge_reports_to_super:'.$report_type, $from, $to);
		$this->load->model(['multiple_db_model']);
		switch ($report_type){
			case 'summary2_report':
				$result=$this->multiple_db_model->syncSummary2Report($from, $to);
                $this->utils->info_log('syncSummary2Report result', $result);
				break;
			case 'player_report_hourly':
				$result=$this->multiple_db_model->syncPlayerReportHourly($from, $to);
                $this->utils->info_log('syncPlayerReportHourly result', $result);
				break;
            case 'game_report_hourly':
                $result=$this->multiple_db_model->syncGameReportHourly($from, $to);
                $this->utils->info_log('syncGameReportHourly result', $result);
                break;
            case 'payment_report_daily':
                $result=$this->multiple_db_model->syncPaymentReportDaily($from, $to);
                $this->utils->info_log('syncPaymentReportDaily result', $result);
                break;
            case 'cashback_report_daily':
                $result=$this->multiple_db_model->syncCashbackReportDaily($from, $to);
                $this->utils->info_log('syncCashbackReportDaily result', $result);
                break;
            case 'promotion_report_details':
                $result=$this->multiple_db_model->syncPromotionReportDetails($from, $to);
                $this->utils->info_log('syncPromotionReportDetails result', $result);
                break;
		}

        return $result;
    }
    //===super report==========================================

    //This is for Kingrich PagCor Compliance.
    public function generate_kingrich_send_data_schedule(){
        $this->load->model(['report_model']);
        $success = $this->report_model->generate_kingrich_send_data_schedule();
        //$success = true;
        $this->utils->debug_log('generate_kingrich_send_data_schedule logs success',$success);
    }

    public function generate_player_simple_report($fromDateStr, $toDateStr, $playerId='_null'){
        $from=new DateTime($fromDateStr);
        $to=new DateTime($toDateStr);

        if($playerId=='_null'){
            $playerId=null;
        }
        $this->load->model(['total_player_game_day']);
        $cntPlayerReport=0;
        //lock and trans
        $this->lockAndTrans(Utils::LOCK_ACTION_GENERATE_SIMPLE_PLAYER_GAME_REPORT, 0,
                function() use(&$cntPlayerReport, $from, $to, $playerId){
            $cntPlayerReport=$this->total_player_game_day->syncSimplePlayerGameReportDaily($from, $to, $playerId);
            return true;
        });
        // $this->utils->printLastSQL();
        $this->utils->debug_log('cntPlayerReport', $cntPlayerReport, $this->db->last_query(), $this->db->getOgTargetDB());

    }

     public function generate_vr_report_daily($date = null){
        if(!$this->utils->isEnabledFeature('enabled_vr_game_report')){
            $this->utils->debug_log('feature enabled_vr_game_report disabled');
            return;
        }
        if(!$date){
            $date = date('Y-m-d', strtotime('today'));
        }
        $this->load->model(['report_model']);
        $success=$this->report_model->generate_vr_report_daily($date);
        $this->utils->debug_log('getVRDataForReport', $date, $success);
    }

    public function generate_vr_report_by_date_range($date_from, $date_to){
        $date = $date_from;
        $end_date = $date_to;
        while (strtotime($date) <= strtotime($end_date)) {
            $this->generate_vr_report_daily($date);
            $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
        }
        $this->utils->debug_log('generate_vr_report_by_date_range', $date_from ,$date_to);
    }

    /*
     * Command Format:
     * 
     *     MDB:
     *         bash ./command_mdb_noroot.sh <db name> <function name> [datetime_from] [datetime_to]
     *         bash ./command_mdb_noroot.sh idr generate_agency_agent_report '2019-09-24 23:00:00' '2019-09-24 23:59:59'
     *         
     *     Single DB:
     *         bash ./command.sh generate_agency_agent_report [datetime_from] [datetime_to]
     *         bash ./command.sh generate_agency_agent_report '2019-08-27 15:00:00' '2019-08-27 15:59:59'
     */
    public function generate_agency_agent_report($datetime_from = null, $datetime_to = null)
    {
        $use_agency_agent_reports = $this->utils->getConfig('use_agency_agent_reports');
        if(!$use_agency_agent_reports){
            $this->utils->debug_log('use_agency_agent_reports was set to false');
            return;
        }
        $interval = new DateInterval('PT1H');
        if(!$datetime_from || !$datetime_to){
            $dateTimeFrom = new DateTime($this->utils->getNowForMysql());
            $dateTimeFrom->sub($interval);
            $dateTimeTo = new DateTime($this->utils->getNowForMysql());
            $dateTimeTo->sub($interval);
        }else{
            $dateTimeFrom = new DateTime($datetime_from);
            $dateTimeTo = new DateTime($datetime_to);
        }

        $this->load->model(['agency_agent_report']);
        $success=$this->agency_agent_report->sync($dateTimeFrom,$dateTimeTo);
        $this->utils->debug_log('Sync DateRange:', $dateTimeFrom->format("Y-m-d H:i:s")."-".$dateTimeTo->format("Y-m-d H:i:s"), $success);
    }
}
