<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Game_functions
 *
 * Game_functions library for FHI OG System
 *
 * @package   Game_functions
 * @author    Rendell Nuñez
 * @version   1.0.0
 */

class Excel {
	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('session', 'utils'));
		$this->ci->load->model(array('game'));
		$this->ci->lang->load('main', 'english');
	}

	function to_excel($array, $filename) {
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename=' . $filename . '.xls');

		$h = array();
		foreach ($array->result_array() as $row) {
			foreach ($row as $key => $val) {
				if (!in_array($key, $h)) {
					$h[] = $key;
				}
			}
		}
		//echo the entire table headers
		echo '<table><tr>';
		foreach ($h as $key) {
			$key = ucwords($key);
			echo '<th>' . $key . '</th>';
		}
		echo '</tr>';

		foreach ($array->result_array() as $row) {
			echo '<tr>';
			foreach ($row as $val) {
				$this->writeRow($val);
			}

		}
		echo '</tr>';
		echo '</table>';

	}
	function writeRow($val) {
		echo '<td>' . utf8_decode($val) . '</td>';
	}

	function transactionToExcel($array) {

		$this->ci->load->helper('file');
		$table = '';

		$table .= '<table style="border:1px solid  #8BA8FE;color:#007fff;" ><tr style="height:50px;font-weight:bold;background:#E8EDFF;font-size:20px;"><td colspan="13"> ' . $array['title'] . '' . $array['date'] . '</td></tr></table><br>';

		foreach ($array['transactionsData'] as $key => $value) {

			$total_amount = $value['total'];
			$table_title = $value['name'];
			$table_columns = $this->getColumnHeaders($value['transDataColTitles'], 'main-table');
			$table_rows = $this->getRows($value['transactionsData']['data'], 'main-table');

			$table_html = '';
			$table_html .= '<table border="1" bordercolor="#8BA8FE" style="border-collapse: collapse;border:1px solid  #8BA8FE;">';
			$table_html .= '<tr style="height:50px;font-weight:bold;background:#ccffcc;font-size:20px;"><td colspan="13">' . $table_title . '</td></tr>';
			$table_html .= '<tr>';
			$table_html .= $table_columns;
			$table_html .= '</tr>';
			$table_html .= $table_rows;

			if ($value['type'] != 'withdrawals') {
				$table_html .= '<tr style="height:30px;font-weight:bold;background:#cdd8fe;border:1px solid #8BA8FE;"><td colspan="10"></td><td colspan="2">Total ' . $table_title . '</td><td  align="right">' . $this->ci->utils->formatCurrencyNoSym($total_amount) . '</td></tr>';
				$table_html .= '<tr><td colspan="10" ></td><td colspan="3" style="height:30px;font-weight:bold;background:#ccffcc;border:1px solid  #8BA8FE;" >SUMMARY</td></tr>';
			}
			//sub tables
			if ($value['type'] == 'deposits') {

				$table_html .= $this->getColumnHeaders($value['summaryColTitles'], 'append-table');
				$table_html .= $this->getRows($value['summary']['data'], 'sub-table');
			}
			if ($value['type'] == 'bonuses') {

				$table_html .= $this->getColumnHeaders($value['summaryColTitles'], 'append-table');
				$table_html .= $this->getRows($value['summary']['data'], 'sub-table');
			}

			$table_html .= '<tr style="height:30px;font-weight:bold;background:#cdd8fe"><td colspan="10"></td><td colspan="2">Total ' . $table_title . '</td><td  align="right">' . $this->ci->utils->formatCurrencyNoSym($total_amount) . '</td></tr>';

			$table .= $table_html;

			//add below some space
			$table .= '<table></table>';
		}

		if (!is_dir($this->ci->config->item('report_path'))) {
			mkdir($this->ci->config->item('report_path'), 0777, TRUE);
		}

		$excel_file = $this->ci->config->item('report_path') . '/' . $array['filename'] . '.xls';

		if (!write_file($excel_file, $table)) {
			return FALSE;
		} else {
			return TRUE;
		}

	}

	function getColumnHeaders($tableCols, $type) {
		$columns = '';

		if ($type != 'main-table') {
			$columns .= '<th colspan="10"></th>';
		}
		foreach ($tableCols as $col) {
			$columns .= '<th style="background:#cdd8fe;border:1px solid #8BA8FE;height:40px;text-align:center;padding:2px;">' . $col . '</th>';

		}
		return $columns;

	}
	function getRows($tableRows, $type) {

#NOTE: NOT LOADING LANGUAGE FILE WHEN I USE ON CMD
		$payment_types = array();
		$payment_types['bank_type1'] = "Industrial and Commercial Bank(ICBC)";
		$payment_types['bank_type2'] = "China Merchant Bank(CMB)";
		$payment_types['bank_type3'] = "China Construction Bank(CCB)";
		$payment_types['bank_type4'] = "Argicultural Bank of China(AGB)";
		$payment_types['bank_type5'] = "Bank of Communications(BCOMM)";
		$payment_types['bank_type6'] = "Bank of China(BOC)";
		$payment_types['bank_type7'] = "Shenzhen Development Bank (SDB)";
		$payment_types['bank_type8'] = "Guangdong Development Bank (GDB)";
		$payment_types['bank_type9'] = "Dongguan Rural Commercial Bank (DRC Bank)";
		$payment_types['bank_type10'] = "China Citic Bank";
		$payment_types['bank_type11'] = "China Minsheng Banking Corp Ltd (CMBC)";
		$payment_types['bank_type12'] = "Postal Savings Bank of China";
		$payment_types['bank_type13'] = "Industrial Bank Co.Ltd";
		$payment_types['bank_type14'] = "Huaxia Bank";
		$payment_types['bank_type15'] = "Ping An Bank";
		$payment_types['bank_type16'] = "Guangxi rural credit cooperative";
		$payment_types['bank_type17'] = "Bank Of Guangzhou";
		$payment_types['bank_type18'] = "Bank OF Nanjing";
		$payment_types['bank_type19'] = "Guangzhou Rural Commercial Bank";
		$payment_types['bank_type20'] = "China Everbright Bank";
		$payment_types['bank_type_alipay'] = "Alipay";
		$payment_types['bank_type_wechat'] = "WeChat";
		$payment_types['payment.type.4'] = 'IPS';
		$payment_types['payment.type.5'] = "GOPAY";
		$payment_types['payment.type.9'] = "BOFO";
		$payment_types['payment.type.12'] = "BAOFU88";
		$payment_types['payment.type.16'] = "365PAY";
		$payment_types['payment.type.17'] = "LOADCARD";

		$transaction_types = array();
		$transaction_types['transaction.transaction.type.1'] = "Deposit";
		$transaction_types['transaction.transaction.type.2'] = "Withdrawal";
		$transaction_types['transaction.transaction.type.3'] = "Fee for member";
		$transaction_types['transaction.transaction.type.4'] = "Fee for operator";
		$transaction_types['transaction.transaction.type.5'] = "Fund transfer to sub wallet";
		$transaction_types['transaction.transaction.type.6'] = "Fund transfer to main wallet";
		$transaction_types['transaction.transaction.type.7'] = "Manual add balance";
		$transaction_types['transaction.transaction.type.8'] = "Manual subtract balance";
		$transaction_types['transaction.transaction.type.9'] = "Add bonus";
		$transaction_types['transaction.transaction.type.10'] = "Subtract bonus";
		$transaction_types['transaction.transaction.type.11'] = "Manual add balance on sub wallet";
		$transaction_types['transaction.transaction.type.12'] = "Manual subtract balance on sub wallet";
		$transaction_types['transaction.transaction.type.13'] = "Cashback";
		$transaction_types['transaction.transaction.type.14'] = "VIP Bonus";
		$transaction_types['transaction.transaction.type.15'] = "Player Refer Bonus";
		$transaction_types['transaction.transaction.type.16'] = "Affiliate Monthly Earnings";
		$transaction_types['transaction.transaction.type.17'] = "Manual add credit";
		$transaction_types['transaction.transaction.type.18'] = "Manual subtract credit";
		$transaction_types['transaction.transaction.type.19'] = "Random Bonus";

		$rows = '';
		$count = 0;
		$colspan = 0;

		if ($tableRows) {

			foreach ($tableRows as $row => $td_vals) {
				$row = '';
				$colspan = count($td_vals);
				// add colspan td for summary
				if ($type == 'sub-table') {
					$row .= '<td colspan="10" ></td>';
				}

				foreach ($td_vals as $tdkey => $td) {
					//bankname fix
					$td = $td;

					if ($tdkey == 'payment_type_name') {
						//  $td = lang($td);
						$td = $payment_types[$td];

					}
					if ($tdkey == 'flag') {
						$td = lang('transaction.flag.' . $td);
					}

					if ($tdkey == 'transaction_type') {
						// $td = lang('transaction.transaction.type.'.$td);
						$trans_type = 'transaction.transaction.type.' . $td;
						$td = $transaction_types[$trans_type];
					}

					if ($tdkey == 'total_before_balance' && $td == null) {
						$td = '0';
					}
					if ($tdkey == 'note' && $td == null) {
						$td = 'N/A';
					}

					if ($tdkey == 'from_username' && $td == null) {
						$td = 'N/A';
					}

					if ($tdkey == 'to_username' && $td == null) {
						$td = 'N/A';
					}
					if ($tdkey == 'subwallet' && $td == null) {
						$td = 'N/A';
					}

					if ($tdkey == 'external_transaction_id' && $td == null) {
						$td = 'N/A';
					}
					if ($tdkey == 'promoTypeName' && $td == null) {
						$td = 'N/A';
					}

					//null
					if ($td == null && $type == 'sub-table') {
						$td = 'N/A';
					}
					if (is_numeric($td)) {
						$row .= '<td align="right">' . $this->ci->utils->formatCurrencyNoSym($td) . '</td>';
					} else {
						$row .= '<td>' . $td . '</td>';
					}
				}

				$count++;
				if ($count % 2 == 0 && $type != 'sub-table') {
					$rows .= '<tr style="height:30px;background:#E8EDFF;">' . $row . '</tr>';
				} else {
					$rows .= '<tr style="height:30px;" >' . $row . '</tr>';
				}
			}
		} else {

			$rows = '<tr style="height:30px;background:#ffb3b3" ><td colspan="' . $colspan . '">NO DATA</td></tr>';
		}

		return $rows;

	}

}
