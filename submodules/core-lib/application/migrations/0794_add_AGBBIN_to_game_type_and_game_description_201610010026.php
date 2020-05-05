<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_AGBBIN_to_game_type_and_game_description_201610010026 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

// 		$this->db->trans_start();

// 		$data = array(

// 			array('game_type' => 'BK',
// 				'game_type_lang' => 'agbbin_bk',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.bk',
// 						'english_name' => 'agbbin.bk',
// 						'external_game_id' => 'BK',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'BS',
// 				'game_type_lang' => 'agbbin_bs',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.bs',
// 						'english_name' => 'agbbin.bs',
// 						'external_game_id' => 'BS',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'F1',
// 				'game_type_lang' => 'agbbin_f1',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.f1',
// 						'english_name' => 'agbbin.f1',
// 						'external_game_id' => 'F1',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'FB',
// 				'game_type_lang' => 'agbbin_fb',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.fb',
// 						'english_name' => 'agbbin.fb',
// 						'external_game_id' => 'FB',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'FT',
// 				'game_type_lang' => 'agbbin_ft',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.ft',
// 						'english_name' => 'agbbin.ft',
// 						'external_game_id' => 'FT',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'IH',
// 				'game_type_lang' => 'agbbin_ih',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.ih',
// 						'english_name' => 'agbbin.ih',
// 						'external_game_id' => 'IH',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'SP',
// 				'game_type_lang' => 'agbbin_sp',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.sp',
// 						'english_name' => 'agbbin.sp',
// 						'external_game_id' => 'SP',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'TN',
// 				'game_type_lang' => 'agbbin_tn',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.tn',
// 						'english_name' => 'agbbin.tn',
// 						'external_game_id' => 'TN',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'CP',
// 				'game_type_lang' => 'agbbin_cp',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.cp',
// 						'english_name' => 'agbbin.cp',
// 						'external_game_id' => 'CP',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'LT',
// 				'game_type_lang' => 'agbbin_lt',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.lt',
// 						'english_name' => 'agbbin.lt',
// 						'external_game_id' => 'LT',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'BJ3D',
// 				'game_type_lang' => 'agbbin_bj3d',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.bj3d',
// 						'english_name' => 'agbbin.bj3d',
// 						'external_game_id' => 'BJ3D',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'PL3D',
// 				'game_type_lang' => 'agbbin_pl3d',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.pl3d',
// 						'english_name' => 'agbbin.pl3d',
// 						'external_game_id' => 'PL3D',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'BBPK',
// 				'game_type_lang' => 'agbbin_bbpk',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.bbpk',
// 						'english_name' => 'agbbin.bbpk',
// 						'external_game_id' => 'BBPK',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'BB3D',
// 				'game_type_lang' => 'agbbin_bb3d',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.bb3d',
// 						'english_name' => 'agbbin.bb3d',
// 						'external_game_id' => 'BB3D',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'BBKN',
// 				'game_type_lang' => 'agbbin_bbkn',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.bbkn',
// 						'english_name' => 'agbbin.bbkn',
// 						'external_game_id' => 'BBKN',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'BBRB',
// 				'game_type_lang' => 'agbbin_bbrb',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.bbrb',
// 						'english_name' => 'agbbin.bbrb',
// 						'external_game_id' => 'BBRB',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'SH3D',
// 				'game_type_lang' => 'agbbin_sh3d',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.sh3d',
// 						'english_name' => 'agbbin.sh3d',
// 						'external_game_id' => 'SH3D',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'CQSC',
// 				'game_type_lang' => 'agbbin_cqsc',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.cqsc',
// 						'english_name' => 'agbbin.cqsc',
// 						'external_game_id' => 'CQSC',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'TJSC',
// 				'game_type_lang' => 'agbbin_tjsc',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.tjsc',
// 						'english_name' => 'agbbin.tjsc',
// 						'external_game_id' => 'TJSC',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'JXSC',
// 				'game_type_lang' => 'agbbin_jxsc',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.jxsc',
// 						'english_name' => 'agbbin.jxsc',
// 						'external_game_id' => 'JXSC',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'XJSC',
// 				'game_type_lang' => 'agbbin_xjsc',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.xjsc',
// 						'english_name' => 'agbbin.xjsc',
// 						'external_game_id' => 'XJSC',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'CQSF',
// 				'game_type_lang' => 'agbbin_cqsf',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.cqsf',
// 						'english_name' => 'agbbin.cqsf',
// 						'external_game_id' => 'CQSF',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'GXSF',
// 				'game_type_lang' => 'agbbin_gxsf',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.gxsf',
// 						'english_name' => 'agbbin.gxsf',
// 						'external_game_id' => 'GXSF',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'TJSF',
// 				'game_type_lang' => 'agbbin_tjsf',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.tjsf',
// 						'english_name' => 'agbbin.tjsf',
// 						'external_game_id' => 'TJSF',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'BJPK',
// 				'game_type_lang' => 'agbbin_bjpk',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.bjpk',
// 						'english_name' => 'agbbin.bjpk',
// 						'external_game_id' => 'BJPK',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'BJKN',
// 				'game_type_lang' => 'agbbin_bjkn',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.bjkn',
// 						'english_name' => 'agbbin.bjkn',
// 						'external_game_id' => 'BJKN',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'CAKN',
// 				'game_type_lang' => 'agbbin_cakn',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.cakn',
// 						'english_name' => 'agbbin.cakn',
// 						'external_game_id' => 'CAKN',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'GDE5',
// 				'game_type_lang' => 'agbbin_gde5',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.gde5',
// 						'english_name' => 'agbbin.gde5',
// 						'external_game_id' => 'GDE5',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'JSXE5',
// 				'game_type_lang' => 'agbbin_jsxe5',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.jsxe5',
// 						'english_name' => 'agbbin.jsxe5',
// 						'external_game_id' => 'JSXE5',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'SDE5',
// 				'game_type_lang' => 'agbbin_sde5',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.sde5',
// 						'english_name' => 'agbbin.sde5',
// 						'external_game_id' => 'SDE5',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'CQWC',
// 				'game_type_lang' => 'agbbin_cqwc',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.cqwc',
// 						'english_name' => 'agbbin.cqwc',
// 						'external_game_id' => 'CQWC',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'JLQ3',
// 				'game_type_lang' => 'agbbin_jlq3',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.jlq3',
// 						'english_name' => 'agbbin.jlq3',
// 						'external_game_id' => 'JLQ3',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'JSQ3',
// 				'game_type_lang' => 'agbbin_jsq3',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.jsq3',
// 						'english_name' => 'agbbin.jsq3',
// 						'external_game_id' => 'JSQ3',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => 'AHQ3',
// 				'game_type_lang' => 'agbbin_ahq3',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.ahq3',
// 						'english_name' => 'agbbin.ahq3',
// 						'external_game_id' => 'AHQ3',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '3001',
// 				'game_type_lang' => 'agbbin_3001',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.3001',
// 						'english_name' => 'agbbin.3001',
// 						'external_game_id' => '3001',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '3002',
// 				'game_type_lang' => 'agbbin_3002',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.3002',
// 						'english_name' => 'agbbin.3002',
// 						'external_game_id' => '3002',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '3003',
// 				'game_type_lang' => 'agbbin_3003',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.3003',
// 						'english_name' => 'agbbin.3003',
// 						'external_game_id' => '3003',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '3005',
// 				'game_type_lang' => 'agbbin_3005',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.3005',
// 						'english_name' => 'agbbin.3005',
// 						'external_game_id' => '3005',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '3006',
// 				'game_type_lang' => 'agbbin_3006',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.3006',
// 						'english_name' => 'agbbin.3006',
// 						'external_game_id' => '3006',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '3007',
// 				'game_type_lang' => 'agbbin_3007',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.3007',
// 						'english_name' => 'agbbin.3007',
// 						'external_game_id' => '3007',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '3008',
// 				'game_type_lang' => 'agbbin_3008',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.3008',
// 						'english_name' => 'agbbin.3008',
// 						'external_game_id' => '3008',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '3010',
// 				'game_type_lang' => 'agbbin_3010',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.3010',
// 						'english_name' => 'agbbin.3010',
// 						'external_game_id' => '3010',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '3011',
// 				'game_type_lang' => 'agbbin_3011',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.3011',
// 						'english_name' => 'agbbin.3011',
// 						'external_game_id' => '3011',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '3012',
// 				'game_type_lang' => 'agbbin_3012',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.3012',
// 						'english_name' => 'agbbin.3012',
// 						'external_game_id' => '3012',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '3014',
// 				'game_type_lang' => 'agbbin_3014',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.3014',
// 						'english_name' => 'agbbin.3014',
// 						'external_game_id' => '3014',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '3015',
// 				'game_type_lang' => 'agbbin_3015',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.3015',
// 						'english_name' => 'agbbin.3015',
// 						'external_game_id' => '3015',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5005',
// 				'game_type_lang' => 'agbbin_5005',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5005',
// 						'english_name' => 'agbbin.5005',
// 						'external_game_id' => '5005',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5006',
// 				'game_type_lang' => 'agbbin_5006',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5006',
// 						'english_name' => 'agbbin.5006',
// 						'external_game_id' => '5006',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5007',
// 				'game_type_lang' => 'agbbin_5007',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5007',
// 						'english_name' => 'agbbin.5007',
// 						'external_game_id' => '5007',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5008',
// 				'game_type_lang' => 'agbbin_5008',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5008',
// 						'english_name' => 'agbbin.5008',
// 						'external_game_id' => '5008',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5009',
// 				'game_type_lang' => 'agbbin_5009',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5009',
// 						'english_name' => 'agbbin.5009',
// 						'external_game_id' => '5009',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5011',
// 				'game_type_lang' => 'agbbin_5011',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5011',
// 						'english_name' => 'agbbin.5011',
// 						'external_game_id' => '5011',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5012',
// 				'game_type_lang' => 'agbbin_5012',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5012',
// 						'english_name' => 'agbbin.5012',
// 						'external_game_id' => '5012',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5013',
// 				'game_type_lang' => 'agbbin_5013',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5013',
// 						'english_name' => 'agbbin.5013',
// 						'external_game_id' => '5013',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5014',
// 				'game_type_lang' => 'agbbin_5014',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5014',
// 						'english_name' => 'agbbin.5014',
// 						'external_game_id' => '5014',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5015',
// 				'game_type_lang' => 'agbbin_5015',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5015',
// 						'english_name' => 'agbbin.5015',
// 						'external_game_id' => '5015',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5016',
// 				'game_type_lang' => 'agbbin_5016',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5016',
// 						'english_name' => 'agbbin.5016',
// 						'external_game_id' => '5016',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5017',
// 				'game_type_lang' => 'agbbin_5017',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5017',
// 						'english_name' => 'agbbin.5017',
// 						'external_game_id' => '5017',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5018',
// 				'game_type_lang' => 'agbbin_5018',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5018',
// 						'english_name' => 'agbbin.5018',
// 						'external_game_id' => '5018',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5019',
// 				'game_type_lang' => 'agbbin_5019',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5019',
// 						'english_name' => 'agbbin.5019',
// 						'external_game_id' => '5019',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5020',
// 				'game_type_lang' => 'agbbin_5020',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5020',
// 						'english_name' => 'agbbin.5020',
// 						'external_game_id' => '5020',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5025',
// 				'game_type_lang' => 'agbbin_5025',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5025',
// 						'english_name' => 'agbbin.5025',
// 						'external_game_id' => '5025',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5026',
// 				'game_type_lang' => 'agbbin_5026',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5026',
// 						'english_name' => 'agbbin.5026',
// 						'external_game_id' => '5026',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5027',
// 				'game_type_lang' => 'agbbin_5027',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5027',
// 						'english_name' => 'agbbin.5027',
// 						'external_game_id' => '5027',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5028',
// 				'game_type_lang' => 'agbbin_5028',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5028',
// 						'english_name' => 'agbbin.5028',
// 						'external_game_id' => '5028',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5029',
// 				'game_type_lang' => 'agbbin_5029',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5029',
// 						'english_name' => 'agbbin.5029',
// 						'external_game_id' => '5029',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5030',
// 				'game_type_lang' => 'agbbin_5030',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5030',
// 						'english_name' => 'agbbin.5030',
// 						'external_game_id' => '5030',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5034',
// 				'game_type_lang' => 'agbbin_5034',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5034',
// 						'english_name' => 'agbbin.5034',
// 						'external_game_id' => '5034',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5035',
// 				'game_type_lang' => 'agbbin_5035',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5035',
// 						'english_name' => 'agbbin.5035',
// 						'external_game_id' => '5035',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5039',
// 				'game_type_lang' => 'agbbin_5039',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5039',
// 						'english_name' => 'agbbin.5039',
// 						'external_game_id' => '5039',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5040',
// 				'game_type_lang' => 'agbbin_5040',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5040',
// 						'english_name' => 'agbbin.5040',
// 						'external_game_id' => '5040',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5041',
// 				'game_type_lang' => 'agbbin_5041',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5041',
// 						'english_name' => 'agbbin.5041',
// 						'external_game_id' => '5041',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5042',
// 				'game_type_lang' => 'agbbin_5042',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5042',
// 						'english_name' => 'agbbin.5042',
// 						'external_game_id' => '5042',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5047',
// 				'game_type_lang' => 'agbbin_5047',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5047',
// 						'english_name' => 'agbbin.5047',
// 						'external_game_id' => '5047',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5048',
// 				'game_type_lang' => 'agbbin_5048',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5048',
// 						'english_name' => 'agbbin.5048',
// 						'external_game_id' => '5048',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5049',
// 				'game_type_lang' => 'agbbin_5049',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5049',
// 						'english_name' => 'agbbin.5049',
// 						'external_game_id' => '5049',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5050',
// 				'game_type_lang' => 'agbbin_5050',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5050',
// 						'english_name' => 'agbbin.5050',
// 						'external_game_id' => '5050',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5057',
// 				'game_type_lang' => 'agbbin_5057',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5057',
// 						'english_name' => 'agbbin.5057',
// 						'external_game_id' => '5057',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5058',
// 				'game_type_lang' => 'agbbin_5058',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5058',
// 						'english_name' => 'agbbin.5058',
// 						'external_game_id' => '5058',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5059',
// 				'game_type_lang' => 'agbbin_5059',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5059',
// 						'english_name' => 'agbbin.5059',
// 						'external_game_id' => '5059',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5060',
// 				'game_type_lang' => 'agbbin_5060',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5060',
// 						'english_name' => 'agbbin.5060',
// 						'external_game_id' => '5060',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5061',
// 				'game_type_lang' => 'agbbin_5061',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5061',
// 						'english_name' => 'agbbin.5061',
// 						'external_game_id' => '5061',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5062',
// 				'game_type_lang' => 'agbbin_5062',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5062',
// 						'english_name' => 'agbbin.5062',
// 						'external_game_id' => '5062',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5063',
// 				'game_type_lang' => 'agbbin_5063',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5063',
// 						'english_name' => 'agbbin.5063',
// 						'external_game_id' => '5063',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5064',
// 				'game_type_lang' => 'agbbin_5064',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5064',
// 						'english_name' => 'agbbin.5064',
// 						'external_game_id' => '5064',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5065',
// 				'game_type_lang' => 'agbbin_5065',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5065',
// 						'english_name' => 'agbbin.5065',
// 						'external_game_id' => '5065',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5066',
// 				'game_type_lang' => 'agbbin_5066',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5066',
// 						'english_name' => 'agbbin.5066',
// 						'external_game_id' => '5066',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5070',
// 				'game_type_lang' => 'agbbin_5070',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5070',
// 						'english_name' => 'agbbin.5070',
// 						'external_game_id' => '5070',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5073',
// 				'game_type_lang' => 'agbbin_5073',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5073',
// 						'english_name' => 'agbbin.5073',
// 						'external_game_id' => '5073',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5076',
// 				'game_type_lang' => 'agbbin_5076',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5076',
// 						'english_name' => 'agbbin.5076',
// 						'external_game_id' => '5076',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5077',
// 				'game_type_lang' => 'agbbin_5077',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5077',
// 						'english_name' => 'agbbin.5077',
// 						'external_game_id' => '5077',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5078',
// 				'game_type_lang' => 'agbbin_5078',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5078',
// 						'english_name' => 'agbbin.5078',
// 						'external_game_id' => '5078',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5079',
// 				'game_type_lang' => 'agbbin_5079',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5079',
// 						'english_name' => 'agbbin.5079',
// 						'external_game_id' => '5079',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5080',
// 				'game_type_lang' => 'agbbin_5080',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5080',
// 						'english_name' => 'agbbin.5080',
// 						'external_game_id' => '5080',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5083',
// 				'game_type_lang' => 'agbbin_5083',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5083',
// 						'english_name' => 'agbbin.5083',
// 						'external_game_id' => '5083',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5084',
// 				'game_type_lang' => 'agbbin_5084',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5084',
// 						'english_name' => 'agbbin.5084',
// 						'external_game_id' => '5084',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5086',
// 				'game_type_lang' => 'agbbin_5086',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5086',
// 						'english_name' => 'agbbin.5086',
// 						'external_game_id' => '5086',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5088',
// 				'game_type_lang' => 'agbbin_5088',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5088',
// 						'english_name' => 'agbbin.5088',
// 						'external_game_id' => '5088',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5089',
// 				'game_type_lang' => 'agbbin_5089',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5089',
// 						'english_name' => 'agbbin.5089',
// 						'external_game_id' => '5089',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5091',
// 				'game_type_lang' => 'agbbin_5091',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5091',
// 						'english_name' => 'agbbin.5091',
// 						'external_game_id' => '5091',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5092',
// 				'game_type_lang' => 'agbbin_5092',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5092',
// 						'english_name' => 'agbbin.5092',
// 						'external_game_id' => '5092',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5093',
// 				'game_type_lang' => 'agbbin_5093',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5093',
// 						'english_name' => 'agbbin.5093',
// 						'external_game_id' => '5093',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5094',
// 				'game_type_lang' => 'agbbin_5094',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5094',
// 						'english_name' => 'agbbin.5094',
// 						'external_game_id' => '5094',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '8095',
// 				'game_type_lang' => 'agbbin_8095',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.8095',
// 						'english_name' => 'agbbin.8095',
// 						'external_game_id' => '8095',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5101',
// 				'game_type_lang' => 'agbbin_5101',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5101',
// 						'english_name' => 'agbbin.5101',
// 						'external_game_id' => '5101',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5102',
// 				'game_type_lang' => 'agbbin_5102',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5102',
// 						'english_name' => 'agbbin.5102',
// 						'external_game_id' => '5102',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5103',
// 				'game_type_lang' => 'agbbin_5103',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5103',
// 						'english_name' => 'agbbin.5103',
// 						'external_game_id' => '5103',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5104',
// 				'game_type_lang' => 'agbbin_5104',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5104',
// 						'english_name' => 'agbbin.5104',
// 						'external_game_id' => '5104',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5106',
// 				'game_type_lang' => 'agbbin_5106',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5106',
// 						'english_name' => 'agbbin.5106',
// 						'external_game_id' => '5106',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5115',
// 				'game_type_lang' => 'agbbin_5115',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5115',
// 						'english_name' => 'agbbin.5115',
// 						'external_game_id' => '5115',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5116',
// 				'game_type_lang' => 'agbbin_5116',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5116',
// 						'english_name' => 'agbbin.5116',
// 						'external_game_id' => '5116',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5117',
// 				'game_type_lang' => 'agbbin_5117',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5117',
// 						'english_name' => 'agbbin.5117',
// 						'external_game_id' => '5117',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5118',
// 				'game_type_lang' => 'agbbin_5118',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5118',
// 						'english_name' => 'agbbin.5118',
// 						'external_game_id' => '5118',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5131',
// 				'game_type_lang' => 'agbbin_5131',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5131',
// 						'english_name' => 'agbbin.5131',
// 						'external_game_id' => '5131',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5201',
// 				'game_type_lang' => 'agbbin_5201',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5201',
// 						'english_name' => 'agbbin.5201',
// 						'external_game_id' => '5201',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5202',
// 				'game_type_lang' => 'agbbin_5202',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5202',
// 						'english_name' => 'agbbin.5202',
// 						'external_game_id' => '5202',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5203',
// 				'game_type_lang' => 'agbbin_5203',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5203',
// 						'english_name' => 'agbbin.5203',
// 						'external_game_id' => '5203',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5204',
// 				'game_type_lang' => 'agbbin_5204',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5204',
// 						'english_name' => 'agbbin.5204',
// 						'external_game_id' => '5204',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5401',
// 				'game_type_lang' => 'agbbin_5401',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5401',
// 						'english_name' => 'agbbin.5401',
// 						'external_game_id' => '5401',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5402',
// 				'game_type_lang' => 'agbbin_5402',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5402',
// 						'english_name' => 'agbbin.5402',
// 						'external_game_id' => '5402',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5403',
// 				'game_type_lang' => 'agbbin_5403',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5403',
// 						'english_name' => 'agbbin.5403',
// 						'external_game_id' => '5403',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5404',
// 				'game_type_lang' => 'agbbin_5404',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5404',
// 						'english_name' => 'agbbin.5404',
// 						'external_game_id' => '5404',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5405',
// 				'game_type_lang' => 'agbbin_5405',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5405',
// 						'english_name' => 'agbbin.5405',
// 						'external_game_id' => '5405',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5406',
// 				'game_type_lang' => 'agbbin_5406',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5406',
// 						'english_name' => 'agbbin.5406',
// 						'external_game_id' => '5406',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5407',
// 				'game_type_lang' => 'agbbin_5407',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5407',
// 						'english_name' => 'agbbin.5407',
// 						'external_game_id' => '5407',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5601',
// 				'game_type_lang' => 'agbbin_5601',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5601',
// 						'english_name' => 'agbbin.5601',
// 						'external_game_id' => '5601',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5701',
// 				'game_type_lang' => 'agbbin_5701',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5701',
// 						'english_name' => 'agbbin.5701',
// 						'external_game_id' => '5701',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5703',
// 				'game_type_lang' => 'agbbin_5703',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5703',
// 						'english_name' => 'agbbin.5703',
// 						'external_game_id' => '5703',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5704',
// 				'game_type_lang' => 'agbbin_5704',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5704',
// 						'english_name' => 'agbbin.5704',
// 						'external_game_id' => '5704',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5705',
// 				'game_type_lang' => 'agbbin_5705',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5705',
// 						'english_name' => 'agbbin.5705',
// 						'external_game_id' => '5705',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5706',
// 				'game_type_lang' => 'agbbin_5706',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5706',
// 						'english_name' => 'agbbin.5706',
// 						'external_game_id' => '5706',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5707',
// 				'game_type_lang' => 'agbbin_5707',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5707',
// 						'english_name' => 'agbbin.5707',
// 						'external_game_id' => '5707',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5801',
// 				'game_type_lang' => 'agbbin_5801',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5801',
// 						'english_name' => 'agbbin.5801',
// 						'external_game_id' => '5801',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5802',
// 				'game_type_lang' => 'agbbin_5802',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5802',
// 						'english_name' => 'agbbin.5802',
// 						'external_game_id' => '5802',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5803',
// 				'game_type_lang' => 'agbbin_5803',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5803',
// 						'english_name' => 'agbbin.5803',
// 						'external_game_id' => '5803',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5804',
// 				'game_type_lang' => 'agbbin_5804',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5804',
// 						'english_name' => 'agbbin.5804',
// 						'external_game_id' => '5804',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5805',
// 				'game_type_lang' => 'agbbin_5805',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5805',
// 						'english_name' => 'agbbin.5805',
// 						'external_game_id' => '5805',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5806',
// 				'game_type_lang' => 'agbbin_5806',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5806',
// 						'english_name' => 'agbbin.5806',
// 						'external_game_id' => '5806',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5808',
// 				'game_type_lang' => 'agbbin_5808',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5808',
// 						'english_name' => 'agbbin.5808',
// 						'external_game_id' => '5808',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5809',
// 				'game_type_lang' => 'agbbin_5809',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5809',
// 						'english_name' => 'agbbin.5809',
// 						'external_game_id' => '5809',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5810',
// 				'game_type_lang' => 'agbbin_5810',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5810',
// 						'english_name' => 'agbbin.5810',
// 						'external_game_id' => '5810',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5811',
// 				'game_type_lang' => 'agbbin_5811',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5811',
// 						'english_name' => 'agbbin.5811',
// 						'external_game_id' => '5811',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5821',
// 				'game_type_lang' => 'agbbin_5821',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5821',
// 						'english_name' => 'agbbin.5821',
// 						'external_game_id' => '5821',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5823',
// 				'game_type_lang' => 'agbbin_5823',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5823',
// 						'english_name' => 'agbbin.5823',
// 						'external_game_id' => '5823',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5824',
// 				'game_type_lang' => 'agbbin_5824',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5824',
// 						'english_name' => 'agbbin.5824',
// 						'external_game_id' => '5824',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5825',
// 				'game_type_lang' => 'agbbin_5825',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5825',
// 						'english_name' => 'agbbin.5825',
// 						'external_game_id' => '5825',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5826',
// 				'game_type_lang' => 'agbbin_5826',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5826',
// 						'english_name' => 'agbbin.5826',
// 						'external_game_id' => '5826',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5827',
// 				'game_type_lang' => 'agbbin_5827',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5827',
// 						'english_name' => 'agbbin.5827',
// 						'external_game_id' => '5827',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5828',
// 				'game_type_lang' => 'agbbin_5828',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5828',
// 						'english_name' => 'agbbin.5828',
// 						'external_game_id' => '5828',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5831',
// 				'game_type_lang' => 'agbbin_5831',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5831',
// 						'english_name' => 'agbbin.5831',
// 						'external_game_id' => '5831',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5833',
// 				'game_type_lang' => 'agbbin_5833',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5833',
// 						'english_name' => 'agbbin.5833',
// 						'external_game_id' => '5833',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5835',
// 				'game_type_lang' => 'agbbin_5835',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5835',
// 						'english_name' => 'agbbin.5835',
// 						'external_game_id' => '5835',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5836',
// 				'game_type_lang' => 'agbbin_5836',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5836',
// 						'english_name' => 'agbbin.5836',
// 						'external_game_id' => '5836',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5837',
// 				'game_type_lang' => 'agbbin_5837',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5837',
// 						'english_name' => 'agbbin.5837',
// 						'external_game_id' => '5837',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5901',
// 				'game_type_lang' => 'agbbin_5901',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5901',
// 						'english_name' => 'agbbin.5901',
// 						'external_game_id' => '5901',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5902',
// 				'game_type_lang' => 'agbbin_5902',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5902',
// 						'english_name' => 'agbbin.5902',
// 						'external_game_id' => '5902',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5903',
// 				'game_type_lang' => 'agbbin_5903',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5903',
// 						'english_name' => 'agbbin.5903',
// 						'external_game_id' => '5903',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '5888',
// 				'game_type_lang' => 'agbbin_5888',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.5888',
// 						'english_name' => 'agbbin.5888',
// 						'external_game_id' => '5888',
// 						'game_code' => ''
// 						)
// 					),
// 				),
// 			array('game_type' => '15022',
// 				'game_type_lang' => 'agbbin_15022',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => 'agbbin.15022',
// 						'english_name' => 'agbbin.15022',
// 						'external_game_id' => '15022',
// 						'game_code' => ''
// 						)
// 					),
// 				),

// 		);//data

// $game_description_list = array();
// foreach ($data as $game_type) {

// 	$this->db->insert('game_type', array(
// 		'game_platform_id' => AGBBIN_API,
// 		'game_type' => $game_type['game_type'],
// 		'game_type_lang' => $game_type['game_type_lang'],
// 		'status' => $game_type['status'],
// 		'flag_show_in_site' => $game_type['flag_show_in_site'],
// 		));

// 	$game_type_id = $this->db->insert_id();
// 	foreach ($game_type['game_description_list'] as $game_description) {
// 		$game_description_list[] = array_merge(array(
// 			'game_platform_id' => AGBBIN_API,
// 			'game_type_id' => $game_type_id,
// 			), $game_description);
// 	}

// }

// $this->db->insert_batch('game_description', $game_description_list);
// $this->db->trans_complete();

}

public function down() {
	// $this->db->trans_start();
	// $this->db->delete('game_type', array('game_platform_id' => AGBBIN_API, 'game_type !='=> 'unknown'));
	// $this->db->delete('game_description', array('game_platform_id' => AGBBIN_API,'game_name !='=> 'agbbin.unknown'));
	// $this->db->trans_complete();
}
}