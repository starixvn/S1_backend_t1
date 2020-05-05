<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_bank_type_names_chinese_20160526 extends CI_Migration {

	public function up() {
		$this->db->select('bankTypeId');
		$this->db->select('bankName');
		$this->db->from('banktype');
		$query = $this->db->get();
		$bankTypes = $query->result_array();

		// language = English
		$this->lang->is_loaded = array();
        $this->lang->language = array();
		$this->lang->load('main', 'english');

		foreach ($bankTypes as &$bankType) {
			if($bankType['bankTypeId'] < 20) {
				$bankType['bankName_en'] = lang('bank_type'.$bankType['bankTypeId']);
			}
			else {
				$bankNameRaw = $bankType['bankName'];
				if(substr($bankNameRaw, 0, 6) === '_json:') {
					$bankNameRaw_decoded = json_decode(substr($bankNameRaw, 6),true);
					$bankNameRaw = $bankNameRaw_decoded[Language_function::INT_LANG_ENGLISH];
				}

				if (strpos(strtolower($bankNameRaw), 'alipay') !== false) {
					$bankType['bankName_en'] = lang('bank_type_alipay');
				} elseif (strpos(strtolower($bankNameRaw), 'wechat') !== false) {
					$bankType['bankName_en'] = lang('bank_type_wechat');
				} elseif (strpos(strtolower($bankNameRaw), 'spdb') !== false || strpos(strtolower($bankNameRaw), 'spk bank') !== false) {
					$bankType['bankName_en'] = lang('bank_type_spdb');
				} else {
					$bankType['bankName_en'] = lang($bankNameRaw);
				}
			}
		}

		// language = Chinese
		$this->lang->is_loaded = array();
        $this->lang->language = array();
		$this->lang->load('main', 'chinese');

		foreach ($bankTypes as &$bankType) {
			if($bankType['bankTypeId'] < 20) {
				$bankType['bankName_zh'] = lang('bank_type'.$bankType['bankTypeId']);
			}
			else {
				$bankNameRaw = $bankType['bankName'];
				if(substr($bankNameRaw, 0, 6) === '_json:') {
					$bankNameRaw_decoded = json_decode(substr($bankNameRaw, 6),true);
					$bankNameRaw = $bankNameRaw_decoded[Language_function::INT_LANG_CHINESE];
				}
				if (strpos(strtolower($bankNameRaw), 'alipay') !== false) {
					$bankType['bankName_zh'] = lang('bank_type_alipay');
				} elseif (strpos(strtolower($bankNameRaw), 'wechat') !== false) {
					$bankType['bankName_zh'] = lang('bank_type_wechat');
				} elseif (strpos(strtolower($bankNameRaw), 'spdb') !== false || strpos(strtolower($bankNameRaw), 'spk bank') !== false) {
					$bankType['bankName_zh'] = lang('bank_type_spdb');
				} else {
					$bankType['bankName_zh'] = lang($bankNameRaw);
				}
			}
		}

		// combine
		foreach ($bankTypes as &$bankType) {
			$bankType['bankName'] = '_json:' . json_encode(array(
				Language_function::INT_LANG_ENGLISH => $bankType['bankName_en'],
				Language_function::INT_LANG_CHINESE => $bankType['bankName_zh'],
			), JSON_UNESCAPED_UNICODE);
			unset($bankType['bankName_en']);
			unset($bankType['bankName_zh']);
		}


		$this->db->update_batch('banktype', $bankTypes, 'bankTypeId');
	}

	public function down() {
	}
}
