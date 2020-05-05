<?php
/**
 *   filename:   api_shopping_center_module.php
 *   @brief:     APIs for shopping
 */

trait api_shopping_center_module {

	/**
	 * detail: get promo applications
	 *
	 * @return json
	 */
	public function shoppingItemClaimList() {

		$this->load->model(array('report_model', 'point_transactions'));

		$request = $this->input->post();

		$is_export = false;
		$result = $this->report_model->shoppingItemClaimList($request, $is_export);

		$this->returnJsonResult($result);
	}

	public function getShoppingTransactionHistory($playerId, $output_html = null) {
		$this->load->model('shopper_list');
		$data = $this->shopper_list->getShopperList(null, null, $playerId);

		return $this->returnJsonResult($data);
	}

}
// end of api_shopping_center_module.php
