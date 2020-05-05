<?php
require_once dirname(__FILE__) . '/abstract_payment_api_daddypay.php';

/**
 *
 * DaddyPay 3rd party 第三方充值
 *
 * DADDYPAY_3RDPARTY_PAYMENT_API, ID: 115
 *
 * Required Fields:
 * * URL
 * * Key - signing key
 * * Extra Info
 *
 * Field Values:
 *
 * * URL (sandbox): http://52.69.65.224/Mownecum_2_API_Live/Deposit?format=json
 * * Extra Info
 * > {
 * >     "daddypay_company_id" : "## company id ##"
 * >     "bank_list": {
 * >		"1" => "_json: { \"1\": \"ICBC\", \"2\": \"中国工商银行\"}",
 * >		"2" => "_json: { \"1\": \"CMB\", \"2\": \"招商银行\"}",
 * >		"3" => "_json: { \"1\": \"CCB\", \"2\": \"中国建设银行\"}",
 * >		"4" => "_json: { \"1\": \"ABC\", \"2\": \"中国农业银行\"}",
 * >		"5" => "_json: { \"1\": \"BOC\", \"2\": \"中国银行\"}",
 * >		"6" => "_json: { \"1\": \"BCM\", \"2\": \"交通银行\"}",
 * >		"7" => "_json: { \"1\": \"CMBC\", \"2\": \"中国民生银行\"}",
 * >		"8" => "_json: { \"1\": \"ECC\", \"2\": \"中信银行\"}",
 * >		"9" => "_json: { \"1\": \"SPDB\", \"2\": \"上海浦东发展银行\"}",
 * >		"10" => "_json: { \"1\": \"PSBC\", \"2\": \"邮政储汇\"}",
 * >		"11" => "_json: { \"1\": \"CEB\", \"2\": \"中国光大银行\"}",
 * >		"12" => "_json: { \"1\": \"PINGAN\", \"2\": \"平安银行 （原深圳发展银行）\"}",
 * >		"13" => "_json: { \"1\": \"CGB\", \"2\": \"广发银行股份有限公司\"}",
 * >		"14" => "_json: { \"1\": \"HXB\", \"2\": \"华夏银行\"}",
 * >		"15" => "_json: { \"1\": \"CIB\", \"2\": \"福建兴业银行\"}",
 * >		"40" => "_json: { \"1\": \"WECHAT\", \"2\": \"微信支付（二维码）\"}"
 * >	}
 * > }
 *
 * @category Payment
 * @copyright 2013-2016 TripleOneTech
 */
class Payment_api_daddypay_3rdparty extends Abstract_payment_api_daddypay {

	public function getPlatformCode() {
		return DADDYPAY_3RDPARTY_PAYMENT_API;
	}

	public function getPrefix() {
		return 'daddypay_3rdparty';
	}

	public function getDepositMode() {
		return parent::DEPOSIT_MODE_3RDPARTY;
	}

	public function getNoteModel($bankId) {
		if($this->getSystemInfo("use_note_model_fp")) {
			return parent::NOTE_MODEL_DP;
		}
		return parent::NOTE_MODEL_PLATFORM;
	}

	public function handlePaymentFormResponse($resp, $params) {
		return array(
			'success' => true,
			'type' => self::REDIRECT_TYPE_URL,
			'url' => $resp['break_url'],
		);
	}

	# Reference: documentation, section 8.4
	# This will be overwritten in child implementations due to different availability of banks
	public function getBankListInfoFallback() {
		return array(
			array('label' => '中国工商银行', 'value' => 1),
			array('label' => '招商银行', 'value' => 2),
			array('label' => '中国建设银行', 'value' => 3),
			array('label' => '中国农业银行', 'value' => 4),
			array('label' => '中国银行', 'value' => 5),
			// array('label' => '交通银行', 'value' => 6),
			// array('label' => '中国民生银行', 'value' => 7),
			array('label' => '中信银行', 'value' => 8),
			array('label' => '上海浦东发展银行', 'value' => 9),
			array('label' => '邮政储汇', 'value' => 10),
			array('label' => '中国光大银行', 'value' => 11),
			// array('label' => '平安银行', 'value' => 12),
			array('label' => '广发银行股份有限公司', 'value' => 13),
			array('label' => '华夏银行', 'value' => 14),
			array('label' => '福建兴业银行', 'value' => 15),
			// array('label' => '银联无卡支付', 'value' => 51),
			// array('label' => '支付宝', 'value' => 30),
			// array('label' => '微信支付（二维码）', 'value' => 40),
		);
	}

    protected function configParams(&$params, $direct_pay_extra_info) {
        $params['terminal'] = '1';
    }
}
