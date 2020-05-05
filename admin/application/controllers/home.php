<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * General behaviors include:
 * * Displays Messages/Notification
 * * All messages are sorted by Id
 * * Gets admin messages
 *
 * @see Redirect redirect to view_messages page
 *
 * @category Notification
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Home extends BaseController {

	function __construct() {
		parent::__construct();
		$this->load->library(array('template','data_tables','permissions'));
		$this->permissions->checkSettings();
		$this->permissions->setPermissions();
	}

	public function index() {
		// throw new Exception('test exception');

		// in_array('test', 'test error');
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());

		if(!$this->permissions->checkPermissions('dashboard')){
			return $this->utils->activeTabs();
		}

		$this->load->library(['report_functions']);
		// $this->load->model(['transactions','total_player_game_day', 'total_player_game_hour', 'report_model']);
		$this->load->model([ 'transactions' ]);

		$data = $this->transactions->getDashboard();
		// $this->utils->debug_log(__METHOD__, 'get dashboard', $data);

		$data['current_currency'] = $this->utils->getCurrentCurrency();

		// $other_fields = json_decode($data['other_dashboard_fields'], 'as array');
		// // $this->utils->debug_log('other_fields', $other_fields);
		// unset($data['other_dashboard_fields']);
		// $data = array_merge($data, $other_fields);

		// $data['summary_week'] = $this->report_functions->dashboard_revenue_chart($week_start, $week_end);

		$this->utils->debug_log('Dashboard data', $data);

		$this->template->write('title', lang('lang.dashboard'));
		$this->template->add_css('/resources/css/home.css?v=2');
		$this->template->add_css('/resources/third_party/font-awesome/v5/css/all.min.css');
		$this->template->add_css('/resources/third_party/morris/0.5/morris.css');
		$this->template->add_css('/resources/third_party/c3.js/0.6/c3.min.css');
        $this->template->add_css('/resources/css/general/style.css');
		$this->template->write_view('main_content', 'home', $data);
		$this->template->render();
	}

	public function view_messages() {
		//show and set unread
		$this->load->model(['users']);
		//$msg = $this->users->readAdminMessage($id);
		//$data['message'] = $msg;
		$this->template->add_js('resources/js/datatables.min.js');
		$this->template->write('title', lang('Admin Message'));
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());
		$this->template->write_view('main_content', 'admin_message', $data);
		$this->template->render();
	}
	public function view_messages_by_id($id) {
		$this->load->model(['users']);
		$msg = $this->users->readAdminMessage($id);
		echo $data['message'] =$msg['content'];
	}
	public function getAdminMessages($id=null) {
		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'dt'=>$i++,
				'alias' => 'id',
				'select' => 'admin_messages.id',
			),
			array(
				'dt'=>$i++,
				'alias' => 'from_username',
				'select' => 'admin_messages.from_username',
			),
			array(
				'dt'=>$i++,
				'alias' => 'content',
				'select' => 'admin_messages.content',
			),
			array(
				'dt'=>$i++,
				'alias' => 'created_at',
				'select' => 'admin_messages.created_at',
			),
			array(
				'dt'=>$i++,
				'alias' => 'isread',
				'select' => 'admin_messages.status',
				'formatter'=>function ($d,$row) {
										return '
													<td style="text-align:center;">
		                                            <a href="#"  data-toggle="modal" data-target="#myModal" data-toggle="tooltip" title="'.lang('tool.cs01').'" onclick="viewChatDetails('.$row['id'].');"><span class="glyphicon glyphicon-zoom-in"></span></a>
		                                        	</td>';
                            }
			),
		);
		# END DEFINE COLUMNS #################################################################################################################################################
		$table = 'admin_messages';
		$joins=array();
		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
		if(isset($input['id'])){
			$where[] = "admin_messages.id = '".$input['id']."'";
			$values[] = $id;
		}
		if (isset($input['message'])) {
			$where[] = "admin_messages.content LIKE '%".$input['message']."%'";
			$values[] = $input['message'];
		}
		if (isset($input['sender'])) {
			$where[] = "admin_messages.from_username LIKE '%".$input['sender']."%'";
			$values[] = $input['sender'];
		}
		if(!isset($input['id'])){
			if (isset($input['date_from'], $input['date_to'])) {
				$where[] = "admin_messages.created_at BETWEEN '".$input['date_from']."' AND '".$input['date_to']."'";
				$values[] = $input['date_from'];
				$values[] = $input['date_to'];
			}
		}
		//filter not closed messages
		$where[] = "admin_messages.deleted_at IS NULL ";
		# END PROCESS SEARCH FORM #################################################################################################################################################
		//echo "<pre>";print_r($request);
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		//echo $this->db->last_query();
		$this->returnJsonResult($result);
	}

	public function nav($item, $dateRangeDay = false){

		$start_today = $this->utils->getTodayForMysql() . ' 00:00:00';
		$end_today =  $this->utils->getTodayForMysql() . ' 23:59:59';
		if($item=='deposit_all'){
			// redirect("/payment_management/deposit_list/?dwStatus=requestToday");
			redirect("/payment_management/deposit_list/?dwStatus=requestAll&select_all=true");
		}elseif($item=='deposit_local'){
			redirect('/payment_management/deposit_list/?dwStatus=requestBankDeposit&select_all=true&select_all_payments=on&payment_flag_1=1&payment_flag_2=0&payment_flag_3=1&deposit_date_from='.$start_today.'&deposit_date_to='.$end_today);
			//redirect('/payment_management/deposit_list/?dwStatus=requestToday&enable_date=true&select_all=true&excludeTimeout=on&select_all_payments=on&payment_flag[]='.MANUAL_ONLINE_PAYMENT.'&payment_flag[]='.LOCAL_BANK_OFFLINE);
		}elseif($item=='deposit_3rdparty'){
			redirect('/payment_management/deposit_list/?dwStatus=request3rdParty&select_all=true&select_all_payments=on&payment_flag_1=0&payment_flag_2=1&payment_flag_3=0&deposit_date_from='.$start_today.'&deposit_date_to='.$end_today);
		}elseif($item=='withdrawal'){
			$params = array(
				'dwStatus' => 'request',
				'enable_date' => true,
				'withdrawal_date_from' => $start_today,
				'withdrawal_date_to' => $end_today,
				'date_range' => '2'
			);
			if($dateRangeDay){
				redirect('/payment_management/viewWithdrawalRequestList?'. http_build_query($params));
			}else{
				//dateRangeMon
				redirect('/payment_management/viewWithdrawalRequestList?dwStatus=request');
			}
		}elseif($item=='requestToday'){
			redirect('/payment_management/deposit_list/?dwStatus=requestToday&select_all=true&select_all_payments=on&deposit_date_from=' . $start_today . '&deposit_date_to=' . $end_today);
		}elseif($item=='approvedToday'){
			redirect('/payment_management/deposit_list/?dwStatus=approvedToday&select_all=true&select_all_payments=on&deposit_date_from=' . $start_today . '&deposit_date_to=' . $end_today);
		}elseif($item=='new_player'){
			$params = array(
				'search_reg_date' => 'on',
				'registration_date_from' => $start_today,
				'registration_date_to' => $end_today,
			);

			$this->utils->setLastViewedNewPlayerDateTime($this->utils->getNowSub(120));

			redirect('/player_management/searchAllPlayer/?' . http_build_query($params));
		}else{
			redirect('/');
		}
	}

}
/* End of file player_management.php */
/* Location: ./application/controllers/home.php */