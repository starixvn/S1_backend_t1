<?php
/**
 *   filename:   export_agency_module.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-06-17
 *   @brief:     export data for agency sub system
 */

trait export_agency_module {
    public function agency_player_reports() {

        $this->load->model(array('report_model','agency_model'));

        $request = $this->input->post();

        $agent_id = $this->session->userdata('agent_id');

        if( strpos($_SERVER['HTTP_HOST'], 'agency' ) !== false ) {
            if (isset($request['current_agent_name'])) {
                $agent_id_1 = $this->agency_model->get_agent_id_by_agent_name($request['current_agent_name']);
                if ($agent_id_1 != $agent_id && !$this->agency_model->is_upline($agent_id_1, $agent_id)) {
                    $this->output->set_status_header(401);
                    exit;
                };
            }
            if (isset($request['agent_name'])) {
                $agent_id_2 = $this->agency_model->get_agent_id_by_agent_name($request['agent_name']);
                if ($agent_id_2 != $agent_id && !$this->agency_model->is_upline($agent_id_2, $agent_id)) {
                    $this->output->set_status_header(401);
                    exit;
                };
            }
        }

        $this->utils->debug_log('export agency_player_reports', $request);
        $viewPlayerInfoPerm = true;
        $is_export = true;

        // if($this->utils->isEnabledFeature('export_excel_on_queue')){
        //    $this->load->library(['lib_queue']);
        //    $this->load->model(['queue_result']);
        //    $request=$this->utils->decodeJson($this->input->post('json_search'));

        //    $callerType=Queue_result::CALLER_TYPE_SYSTEM;
        //    $caller=0;
        //    $state='';
        //    $funcName='get_agency_player_reports';
        //    $params=[$request, $viewPlayerInfoPerm, $is_export];
        //    $token=$this->lib_queue->addExportCsvJob($funcName, $params, $callerType, $caller, $state);

        //    $link=site_url('/export_data/queue/'.$token);

        //    redirect($link);
        // }


        $result = $this->report_model->get_agency_player_reports($request, $viewPlayerInfoPerm, $is_export);
        $this->utils->debug_log('agency_player_reports, result', $result);

        $d = new DateTime();

        //$link = $this->utils->output_excel($result, 'player_reports_' . $d->format('YmdHisu'));
        foreach ($result['data'] as &$row) {
            $row = array_map('strip_tags', $row);
        }
        $link = $this->utils->create_excel($result, 'player_reports_' . $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999), TRUE);

        //return file link
        $rlt = array('success' => true, 'link' => $link);
        $this->returnJsonResult($rlt);
    }

	public function agency_agent_reports() {

		$this->load->model(array('report_model'));

        $request = $this->input->post();
        if( strpos($_SERVER['HTTP_HOST'], 'agency' ) !== false ) {
           $agent_id = $this->session->userdata('agent_id');
           if (isset($request['agent_id']) && $request['agent_id'] != $agent_id && ! $this->agency_model->is_upline($request['agent_id'], $agent_id)) {
            $this->output->set_status_header(401);
            exit;
            }
        }

        $this->utils->debug_log('export agency_agent_reports', $request);
		$viewAgentInfoPerm = true;
		$is_export = true;
		$result = $this->report_model->get_agency_agent_reports($request, $viewAgentInfoPerm, $is_export);
        $this->utils->debug_log('agency_agent_reports, result', $result);

		$d = new DateTime();

		//$link = $this->utils->output_excel($result, 'agent_reports_' . $d->format('YmdHisu'));
        foreach ($result['data'] as &$row) {
            $row = array_map('strip_tags', $row);
        }
		$link = $this->utils->create_excel($result, 'agent_reports_' . $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999), TRUE);

		//return file link
		$rlt = array('success' => true, 'link' => $link);
		$this->returnJsonResult($rlt);
	}

	public function agency_game_reports($player_id = null) {

		$this->load->model(array('report_model'));
		$request = $this->input->post();

        if( strpos($_SERVER['HTTP_HOST'], 'agency' ) !== false ) {
           if (isset($request['agent_name'])) {
                $agent_id = $this->session->userdata('agent_id');
                $agent_id_2 = $this->agency_model->get_agent_id_by_agent_name($request['agent_name']);
                if ($agent_id_2 != $agent_id && ! $this->agency_model->is_upline($agent_id_2, $agent_id)) {
                    $this->output->set_status_header(401);
                    exit;
               }
            }
        }

		$is_export = true;
		$result = $this->report_model->get_agency_game_reports($request, $player_id, $is_export);

		$d = new DateTime();
		$link = $this->utils->create_csv($result, 'game_report_' . $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999), TRUE);
		//return file link
		$rlt = array('success' => true, 'link' => $link);

		$this->returnJsonResult($rlt);
	}

    public function agency_settlement_list() {
        $this->load->model(array('agency_model'));
        $request = $this->input->post();
        $this->utils->debug_log('logs request', $request);
		$is_export = true;
        $result = $this->agency_model->get_settlement($request, $is_export);

        $this->utils->debug_log($result);

		$d = new DateTime();
		$link = $this->utils->create_excel($result, 'agency_settlement_' . $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999), TRUE);

		//return file link
		$rlt = array('success' => true, 'link' => $link);
		$this->returnJsonResult($rlt);
    } // agency_settlement_list  }}}2

    public function agency_settlement_list_wl($mode='only_agent') {
        $this->load->model(array('agency_model'));
        $request = $this->input->post();
        $this->utils->debug_log('logs request', $request);
        $is_export = true;
        //export is readonly
        $result = $this->agency_model->getWlSettlement($request, $mode, false, true);

        // Strip model generated array of html tags
        // foreach ($result['data'] as $row_num => & $row) {
            // foreach ($result['data'][$row_num] as $col_num => & $datacell) {
            //     if (!isset($result['header_data'][$col_num]) || empty($result['header_data'][$col_num])) {
            //         unset($result['data'][$row_num][$col_num]);
            //         continue;
            //     }
            //     $datacell = strip_tags($datacell);
            // }
        foreach ($result['data'] as $row_num => & $row) {
            foreach ($row as $col_num => & $datacell) {
                if (!isset($result['header_data'][$col_num]) || empty($result['header_data'][$col_num])) {
                    unset($row[$col_num]);
                    continue;
                }
                $datacell = strip_tags($datacell);
            }
        }

        $this->utils->debug_log('agency_settlement_list_wl', $result);

        $d = new DateTime();
        $link = $this->utils->create_excel($result, 'agency_settlement_' . $d->format('Y_m_d_H_i_s') . '_' . sprintf('%04x', rand(1, 0xffff)), TRUE);

        //return file link
        $rlt = array('success' => true, 'link' => $link);
        $this->returnJsonResult($rlt);
    }

    public function agency_invoice() {
        $this->load->model(array('agency_model', 'report_model'));
        $request = $this->input->post();
        $this->utils->debug_log('AGENCY_INVOICE_EXPORT', $request);

        $result = array();
        $is_export = true;
        $result[] = $this->agency_model->get_settlement($request, $is_export);

        $viewPlayerInfoPerm = true;
        $result[] = $this->report_model->get_agency_player_reports($request, $viewPlayerInfoPerm, $is_export);

        $player_id = null;
        $this->utils->debug_log('AGENCY_INVOICE REQUEST', $request);
        $result[] = $this->report_model->get_agency_game_reports($request, $player_id, $is_export);

        $this->utils->debug_log('AGENCY_INVOICE_RESULT', $result);

        $filename = $this->create_invoice_name($request);
        $titles = array('settlement report', 'player report', 'game report');
        $link = $this->utils->create_excel_multi_sheets($result, $filename, TRUE, $titles);

        //return file link
        $rlt = array('success' => true, 'link' => $link);
        $this->returnJsonResult($rlt);
    } // agency_invoice  }}}2

    private function create_invoice_name($request) {
        /*
        $agent_name = $this->input->post('agent_name');
        $period = $this->input->post('period');
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');
         */

        //$this->utils->debug_log('extra_search', $request, $request['extra_search']);
        $input_arr = $request['extra_search'];
        foreach($input_arr as $item) {
        //$this->utils->debug_log('extra_search', $item, $item['name']);
            $n = $item['name'];
            $v = $item['value'];
            switch($n) {
            case 'agent_name':
                $agent_name = $v;
                break;
            case 'date_from':
                $date_from = date('Ymd', strtotime("$v"));
                break;
            case 'date_to':
                $date_to = date('Ymd', strtotime("$v"));
                break;
            case 'period':
                $period = $v;
                break;
            default:
                break;
            }
        }
        //$this->utils->debug_log('date_from', $date_from, $date_to, $agent_name, $period);
        $range = $date_from . '_to_' . $date_to;
        $d = new DateTime();
        $created_at = $d->format('YmdHis');

        $name = $agent_name . '_' . $period . '_' . $range . '_' . $created_at . '_' . rand(1, 9999);

        return $name;
    } // create_invoice_name  }}}2

}
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of export_agency_module.php
