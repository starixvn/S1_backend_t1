<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_function_active_player_report_201612221544 extends CI_Migration {

	const ROLE_DATA = array(
		array(
			'funcId' => '205',
			'funcName' => 'Active Player Report',
			'parentId' => '25',
			'funcCode' => 'active_player_report',
		)
	);


	public function up() {

		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// foreach (self::ROLE_DATA as $key => $value) {

		// 	$funcId = $value['funcId'];
		// 	$funcName = $value['funcName'];
		// 	$parentId = $value['parentId'];
		// 	$funcCode = $value['funcCode'];

		// 	$this->roles->initFunction( $funcCode, $funcName, $funcId, $parentId, true);
		// }

		// $succ = $this->roles->endTransWithSucc();
		// //process result

		// if ( !$succ ) {
		// 	throw new Exception('migrate failed ');
		// }
	}

	public function down() {

		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// foreach (self::ROLE_DATA as $key => $value){

		// 	$funcId = $value['funcId'];
		// 	$this->roles->deleteFunction($funcId);

		// }

		// $succ = $this->roles->endTransWithSucc();
		//process result


	}

}

////END OF FILE////