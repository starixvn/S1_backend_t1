<?php

/**
 * Defines the common functions a payment API should implement.
 *
 * @category Payment
 * @copyright 2013-2016 TripleOneTech
 */
interface Payment_api_interface {

	/**
	 * @param int playerId
	 * @param double amount
	 *
	 * @return int orderId
	 *
	 */
	public function createSaleOrder($playerId, $amount, $player_promo_id);
	/**
	 * @param int orderId
	 * @param int playerId
	 * @param double amount
	 * @param DateTime orderDateTime
	 * @param int $playerPromoId
	 * @param boolean $enabledSecondUrl
	 * @param int $bankId
	 *
	 * @return array ('success' => boolean, 'type'=>1=form, 2=url, 3=direct pay, 'url'=>string, 'post'=>boolean, 'params'=>array, 'html'=>string) , 'html' is customized
	 */
	public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null);
	/**
	 * @param int orderId
	 * @param array callbackExtraInfo
	 *
	 * @return array ('success' => boolean, 'next_url' => string, 'message' => lang())
	 */
	public function callbackFromServer($orderId, $callbackExtraInfo);

	/**
	 * @param int orderId
	 * @param array callbackExtraInfo
	 *
	 * @return array ('success' => boolean, 'next_url' => string, 'message' => lang())
	 */
	public function callbackFromBrowser($orderId, $callbackExtraInfo);

	/**
	 * direct pay, no callback, server to server mode
	 *
	 * @param Sale_order order
	 *
	 * @return array ('success' => boolean, 'next_url' => string, 'message' => lang())
	 */
	public function directPay($order);

	/**
	 * @param string enabledSecondUrl
	 *
	 * @return bool
	 */
	public function shouldRedirect($enabledSecondUrl);
}
