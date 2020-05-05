<?php
require_once dirname(__FILE__) . '/payment_api_daddypay_3rdparty.php';

/**
 *
 * DaddyPay 3rd party 第三方充值 聚宝盆
 * 
 * JBP_3RDPARTY_PAYMENT_API, ID: 999
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
class Payment_api_jbp_3rdparty extends Payment_api_daddypay_3rdparty {

	public function getPlatformCode() {
		return JBP_3RDPARTY_PAYMENT_API;
	}

	public function getPrefix() {
		return 'jbp_3rdparty';
	}
}
