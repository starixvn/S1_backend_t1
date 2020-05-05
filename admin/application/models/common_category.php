<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get kyc Status
 * * Get player kyc Status
 * * Get/insert/update/ player kyc status
 *
 * @category Player KYC Status
 * @version 1.8.10
 * @author Jhunel L. Ebero
 * @copyright 2013-2017 TripleOneTech
 */
class common_category extends BaseModel {
	public function __construct() {
		parent::__construct();
		$this->load->model(array('player_model','player_kyc','player','system_feature','users'));
		$this->load->library(array('permissions'));
	}

	protected $tableName = 'common_category';

	const CATEGORY_STATUS_INACTIVE = 0;
	const CATEGORY_STATUS_ACTIVE = 1;
	const CATEGORY_STATUS_DELETED = 2;

	const CATEGORY_WITHRAWAL_DECLINED = "withdrawal_declined";
	const CATEGORY_ADJUSTMENT = "adjustment";

	public function addUpdateCategory($data){
		$response = false;
		if(!empty($data)){
			if(!empty($data['id'])){
				$data['updated_at'] = $this->getNowForMysql();
				$data['updated_by'] = $this->authentication->getUserId();
				$this->db->where('id', $data['id']);
				$this->db->set($data);
				return $this->runAnyUpdate($this->tableName);
			} else {
				$data['created_at'] = $this->getNowForMysql();
				$data['status'] = self::CATEGORY_STATUS_INACTIVE;
				$data['created_by'] = $this->authentication->getUserId();
				return $this->insertData($this->tableName, $data);
			}
		}
		return $response;
	}

	public function getActiveCategoryByType($type){

		$this->db->select('*');
		$this->db->where('category_type', $type);
		$this->db->where('status', self::CATEGORY_STATUS_ACTIVE);
		$qry = $this->db->get($this->tableName);
        return $this->getMultipleRowArray($qry);
	}

	public function getCategoryInfoById($id){
		$this->db->select('*');
		$this->db->from($this->tableName);
		$this->db->where('id', $id);
        return $this->runOneRowArray();
	}

	public function updateCategoryById($data){
		$response = false;
		if(!empty($data)){
			$data['updated_at'] = $this->getNowForMysql();
			$data['updated_by'] = $this->authentication->getUserId();
			$this->db->where('id', $data['id']);
			$this->db->set($data);
			return $this->runAnyUpdate($this->tableName);
		}
		return $response;
	}

	/**
	 * overview: user logs list
	 */

	public function getWithdrawalDeclinedCategory( $request, $is_export = false ){

		# START DEFINE COLUMNS #################################################################################################################################################
		
		$users = $this->users;
		$permissions = $this->permissions;
		$activePermission = false ;
		$this->load->library('data_tables');
		$input = $this->data_tables->extra_search($request);

		if(isset($input['category_type'])){
			switch ($input['category_type']) {
				case self::CATEGORY_WITHRAWAL_DECLINED:
					$activePermission = $permissions->checkPermissions('modified_withdrawal_declined_category');
					break;
				case self::CATEGORY_ADJUSTMENT :
					$activePermission = $permissions->checkPermissions('modified_adjustment_category');
					break;
			}
		}
		$i = 0;
		if($activePermission){
			$i = 1;
		}

		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'id',
				'select' => 'common_category.id',
				'name' => lang('sys.pay.systemid'),
				'formatter' => function($d, $row) use ($is_export){

					if ($is_export) {
						return (!$d || strtotime($d) < 0) ? lang('lang.norecyet') : date('Y-m-d H:i:s', strtotime($d));
					} else {
						return $d;
					}

				}
			),
			array(
				'dt' => $i++,
				'alias' => 'category_name',
				'select' => 'common_category.category_name',
				'name' => function() use ($input){ 
					if(isset($input['category_type'])){
						switch ($input['category_type']) {
							case self::CATEGORY_WITHRAWAL_DECLINED:
								return lang('Withdrawal Declined');
								break;
							case self::CATEGORY_ADJUSTMENT :
								return lang('Adjustment Category');
								break;
							default:
								return lang('Withdrawal Declined');
								break;
						}
					} else {
						return lang('Withdrawal Declined');
					}

				},
				'formatter' => function($d, $row) use ($is_export){

					if( ! $is_export ){
						return lang($d) ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					} else{

						return ( $d ) ? lang($d) : lang('lang.norecyet');
					}

				}
			),
			array(
				'dt' => $i++,
				'alias' => 'order_by',
				'select' => 'common_category.order_by',
				'name' => lang('report.p18'),
				'formatter' => function($d, $row) use ($is_export){

					if ($is_export) {
						return (!$d || strtotime($d) < 0) ? lang('lang.norecyet') : date('Y-m-d H:i:s', strtotime($d));
					} else {
						 return $d;
					}

				}
			),
			array(
				'dt' => $i++,
				'alias' => 'created_at',
				'select' => 'common_category.created_at',
				'name' => lang('sys.createdon'),
				'formatter' => function($d, $row) use ($is_export){

					if ($is_export) {
						return (!$d || strtotime($d) < 0) ? lang('lang.norecyet') : date('Y-m-d H:i:s', strtotime($d));
					} else {
						return (!$d || strtotime($d) < 0) ? '<i>' . lang('lang.norecyet') . '</i>' : date('Y-m-d H:i:s', strtotime($d));
					}

				}
			),
			array(
				'dt' => $i++,
				'alias' => 'updated_at',
				'select' => 'common_category.updated_at',
				'name' => lang('sys.updatedon'),
				'formatter' => function ($d) use ($is_export) {

					if ($is_export) {
						return (!$d || strtotime($d) < 0) ? lang('lang.norecyet') : date('Y-m-d H:i:s', strtotime($d));
					} else {
						return (!$d || strtotime($d) < 0) ? '<i class="text-muted">' . lang('lang.norecyet') . '</i>' : date('Y-m-d H:i:s', strtotime($d));
					}

				},
			),
			array(
				'dt' => $i++,
				'alias' => 'created_by',
				'select' => 'common_category.created_by',
				'name' => lang('sys.createdby'),
				'formatter' => function ($d) use ($is_export,$users) {
					if( ! $is_export ){
						return $users->getUserUsernameByid($d) ? $users->getUserUsernameByid($d): '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					} else{

						return ( $users->getUserUsernameByid($d) ) ? $users->getUserUsernameByid($d) : lang('lang.norecyet');
					}

				},
			),
			array(
				'dt' => $i++,
				'alias' => 'updated_by',
				'select' => 'common_category.updated_by',
				'name' => lang('sys.updatedby'),
				'formatter' => function ($d) use ($is_export,$users) {

					if( ! $is_export ){
						return $users->getUserUsernameByid($d) ? $users->getUserUsernameByid($d): '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					} else{

						return ( $users->getUserUsernameByid($d) ) ? $users->getUserUsernameByid($d) : lang('lang.norecyet');
					}

				},
			),
			array(
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'common_category.status',
				'name' => lang('lang.status'),
				'formatter' => function($d, $row) use ($is_export,$activePermission){
					$output = '';
					$status = '';
					switch ($d) {
						case self::CATEGORY_STATUS_INACTIVE:
							$output .= '<i class="text-danger">' . lang('Blocked') . '</i>';
							if($activePermission){
								$output .= '<a href="javascript:void(0)" data-toggle="tooltip" title="' . lang('Blocked') . '" class="pull-right"><span class="fa fa-unlock" onclick="return CommonCategory.activeCategoryById('.$row['id'].')"></span></a>';
							}
							$status = lang('Blocked');
							break;
						case self::CATEGORY_STATUS_ACTIVE:
							$output .= '<i class="text-success">' . lang('lang.active') . '</i>';
							if($activePermission){
								$output .= '<a href="javascript:void(0)" data-toggle="tooltip" title="' . lang('Blocked') . '" class="pull-right" onclick="return CommonCategory.inactiveCategoryById('.$row['id'].')"><span class="fa fa-lock"></span></a>';
							}
							$status = lang('lang.active');
							break;
						default:
							$output .= '<i class="text-danger">' . lang('Blocked') . '</i>';
							if($activePermission){
								$output .= '<a href="javascript:void(0)" data-toggle="tooltip" title="' . lang('lang.inactive') . '" class="pull-right"><span class="fa fa-unlock" onclick="return CommonCategory.activeCategoryById('.$row['id'].')"></span></a>';
							}
							$status = lang('Blocked');
							break;
					}
					if ($is_export) {
						return $status ? : lang('lang.norecyet');
					} else {
						return $output ? $output : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}

				},
			),
		);

		if (!$is_export) {
			if($activePermission){
				array_unshift($columns, array(
					'dt' => 0,
					'alias' => 'action',
					'select' => 'common_category.id',
					'name' => lang('sys.action'),
					'formatter' => function($d, $row) use ($is_export,$activePermission){

						if (!$is_export) {
							$pointer = $activePermission ? 'auto' : 'none';
							$output = '';
							$output .= '<div style="pointer-events:'.$pointer.';">';
							$output .= '<a href="javascript:void(0)" data-toggle="tooltip" title="' . lang('sys.em6') . '" onclick="return CommonCategory.loadCategoryInfoById('.$d.')"><span class="glyphicon glyphicon-edit"></span></a> ';
							$output .= '<a href="javascript:void(0)" data-toggle="tooltip" title="' . lang('sys.gd21') . '" onclick="return CommonCategory.deleteCategoryById('.$d.')"><span class="glyphicon glyphicon-trash text-danger"></span></a> ';
							$output .= '</div>';
							return $output;
						}
					},
				));
			}
		}
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = $this->tableName;
		$joins = array();

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();

		

		if (isset($input['category_type'])) {
			$where[] = "common_category.category_type = ?";
			$values[] = $input['category_type'];
		}

		//only active and inactive status only to view
		$where[] = "common_category.status != ?";
		$values[] = self::CATEGORY_STATUS_DELETED;

		# END PROCESS SEARCH FORM #################################################################################################################################################

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

		return $result;

	}
}